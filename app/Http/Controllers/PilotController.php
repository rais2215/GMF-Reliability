<?php

namespace App\Http\Controllers;

use App\Models\TblMasterac;
use App\Models\TblMonthlyfhfc;
use App\Models\Mcdrnew;
use App\Models\TblAlertLevel;
use App\Models\TblMasterAta;
use App\Models\TblPirepSwift;
use App\Models\TblSdr;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class PilotController extends Controller
{
    //Controller Button Pilot Report
    public function pilotIndex() { // Button Filter Pilot Report
        $aircraftTypes = TblPirepSwift::select('ACTYPE')->distinct()->whereNotNull('ACTYPE')->where('ACTYPE', '!=', '')->where('ACTYPE', '!=', 'default')->get();

        $operators = TblMasterac::select('Operator')->distinct()->whereNotNull('Operator')->where('Operator', '!=', '')->get();

        $periods = TblMonthlyfhfc::select('MonthEval')->distinct()->orderByDesc('MonthEval')->get()->map(function($item) {
            return [
                'formatted' => Carbon::parse($item->MonthEval)->format('Y-m'), // Format menjadi yyyy-mm
                'original' => $item->MonthEval
            ];
        });

        return view('report.pilot-content', compact('aircraftTypes', 'operators', 'periods'));
    }

    public function pilotStore(Request $request) { 
        // Validate input
        $request->validate([
            'period' => 'required',
            'aircraft_type' => 'required',
        ]);
    
        $aircraftType = $request->aircraft_type;
        $period = $request->period; // Format: YYYY-MM
    
        // Mendapatkan bulan dan tahun dari periode
        $month = date('m', strtotime($period));
        $year = date('Y', strtotime($period));
    
        // Fungsi untuk menghitung total flying hours
        $getFlyingHours = function($aircraftType, $period) {
            return TblMonthlyfhfc::where('Actype', $aircraftType)
                ->where('MonthEval', $period)
                ->selectRaw('SUM(RevFHHours + (RevFHMin / 60) + NoRevFHHours + (NoRevFHMin / 60)) as total')
                ->first()->total ?? 0; // Menggunakan null coalescing operator
        };
    
        // Hitung flying hours untuk periode sekarang dan sebelumnya
        $flyingHoursTotal = $getFlyingHours($aircraftType, $period);
        $flyingHoursBefore = $getFlyingHours($aircraftType, \Carbon\Carbon::parse($period)->subMonth(1));
        $flyingHours2Before = $getFlyingHours($aircraftType, \Carbon\Carbon::parse($period)->subMonth(2));
        $fh3Last = $flyingHoursTotal + $flyingHoursBefore + $flyingHours2Before;
        $fh12Last = 0;
        for ($i = 0; $i <= 11; $i++) {
            $periodBefore = \Carbon\Carbon::parse($period)->subMonth($i);
            $fh12Last += $getFlyingHours($aircraftType, $periodBefore);
        }
    
        // Daftar ID yang ingin dikecualikan
        $excludedIds = [5, 11, 12, 58, 70];
        // Ambil semua data dari tbl_master_ata kecuali yang ada di $excludedIds
        $tblAta = TblMasterAta::whereNotIn('ATA', $excludedIds)->get();

        
        // ~~~~~ {{ PILOT REPORT }} ~~~~~
        // Fungsi untuk menghitung PIREP
        $getPirepCount = function($aircraftType, $month, $year) {
            return TblPirepSwift::where('ACTYPE', $aircraftType)
                ->whereMonth('DATE', $month)
                ->whereYear('DATE', $year)
                ->where('PirepMarep', 'Pirep')
                ->where('ATA', '21')  // Changed from whereIn to where
                ->count();
        };
        // Hitung PIREP untuk periode sekarang dan sebelumnya
        $pirepCount = $getPirepCount($aircraftType, $month, $year);
        $pirepCountBefore = $getPirepCount($aircraftType, \Carbon\Carbon::parse($period)->subMonth(1)->month, \Carbon\Carbon::parse($period)->subMonth(1)->year);
        $pirepCountTwoMonthsAgo = $getPirepCount($aircraftType, \Carbon\Carbon::parse($period)->subMonths(2)->month, \Carbon\Carbon::parse($period)->subMonths(2)->year);
        $pirep3Month = $pirepCount + $pirepCountBefore + $pirepCountTwoMonthsAgo;
        $pirep12Month = 0;
        for ($i = 0; $i < 12; $i++) {
            $loopMonth = \Carbon\Carbon::parse($period)->subMonths($i)->month;
            $loopYear = \Carbon\Carbon::parse($period)->subMonths($i)->year;
            $pirep12Month += $getPirepCount($aircraftType, $loopMonth, $loopYear);
        }
        // ~~~ PIREP RATE PERIOD ~~~
        $pirepRate = $pirepCount * 1000 / ($flyingHoursTotal ?: 1); // Menghindari pembagian dengan nol
        $pirep1Rate = $pirepCountBefore * 1000 / ($flyingHoursBefore ?: 1);
        $pirep2Rate = $pirepCountTwoMonthsAgo * 1000 / ($flyingHours2Before ?: 1);
        $pirepRate3Month = ($pirepRate + $pirep1Rate + $pirep2Rate) / 3;
        $pirepRate12Month = $pirep12Month * 1000 / ($fh12Last ?: 1);   
        // PIREP ALERT LEVEL
        // Cara 1: Cek apakah data alertlevel untuk period dan actype/ata/type sudah ada di tbl_alertlevel
        $pirepAlertLevel = TblAlertLevel::where('actype', $aircraftType)
            ->where('ata', '21')
            ->where('type', 'ALP')
            ->where(function($query) use ($period) {
            $query->whereBetween('startmonth', [$period, $period])
                ->orWhereBetween('endmonth', [$period, $period])
                ->orWhere(function($query) use ($period) {
                $query->where('startmonth', '<=', $period)
                      ->where('endmonth', '>=', $period);
                });
            })
            ->pluck('alertlevel')
            ->first();

        // Cara 2: Jika tidak ada di database, gunakan nilai dari $pirepRate12Month + 2 * standar deviasi
        if (is_null($pirepAlertLevel)) {
            // Hitung standar deviasi dari 12 bulan rate
            $pirepRates = [];
            for ($i = 0; $i < 12; $i++) {
            $loopMonth = \Carbon\Carbon::parse($period)->subMonths($i)->month;
            $loopYear = \Carbon\Carbon::parse($period)->subMonths($i)->year;
            $fh = $getFlyingHours($aircraftType, \Carbon\Carbon::parse($period)->subMonths($i)->format('Y-m'));
            $count = $getPirepCount($aircraftType, $loopMonth, $loopYear);
            $rate = $count * 1000 / ($fh ?: 1);
            $pirepRates[] = $rate;
            }
            $mean = array_sum($pirepRates) / count($pirepRates);
            $variance = 0;
            foreach ($pirepRates as $rate) {
            $variance += pow($rate - $mean, 2);
            }
            $stddev = sqrt($variance / count($pirepRates));
            $mean12Month = $pirep12Month / 12;
            $pirepAlertLevel = $mean12Month + 2 * $stddev;
        }

        // // ~~~ PIREP ~~~
        // $pirepData = [];
        // foreach ($tblAta as $item) {
        //     $pirepData = $item->ATA;

        //     $pirepCount = $getPirepCount($aircraftType, $month, $year, $pirepData);
        //     $pirepCountBefore = $getPirepCount($aircraftType, \Carbon\Carbon::parse($period)->subMonth(1)->month, $year, $pirepData);
        //     $pirepCountTwoMonthsAgo = $getPirepCount($aircraftType, \Carbon\Carbon::parse($period)->subMonths(2)->month, $year, $pirepData);
            
        //     $pirep3Month = $pirepCount + $pirepCountBefore + $pirepCountTwoMonthsAgo;
        //     $pirep12Month = 0;
        //     for ($i = 0; $i < 12; $i++) {
        //         $month = \Carbon\Carbon::parse($period)->subMonths($i)->month;
        //         $year = \Carbon\Carbon::parse($period)->subMonths($i)->year;
        //         $pirep12Month += $getPirepCount($aircraftType, $month, $year, $pirepData);
        //     }
            
        //     $pirepRate = $pirepCount * 1000 / ($flyingHoursTotal ?: 1);
        //     $pirep1Rate = $pirepCountBefore * 1000 / ($flyingHoursBefore ?: 1);
        //     $pirep2Rate = $pirepCountTwoMonthsAgo * 1000 / ($flyingHours2Before ?: 1);
        //     $pirepRate3Month = ($pirepRate + $pirep1Rate + $pirep2Rate) / 3;
        //     $pirepRate12Month = $pirep12Month * 1000 / ($fh12Last ?: 1);

            // $pirepData[$item->ATA] = [
            //     'countTwoMonthsAgo' => $pirepCountTwoMonthsAgo,
            //     'countBefore' => $pirepCountBefore,
            //     'count' => $pirepCount,
            //     'threeMonth' => $pirep3Month,
            //     'twelveMonth' => $pirep12Month,
            //     'rate2' => $pirep2Rate,
            //     'rate1' => $pirep1Rate,
            //     'rate' => $pirepRate,
            //     'rate3Month' => $pirepRate3Month,
            //     'alertLevel' => $pirepAlertLevel, // Anda mungkin ingin menghitung ini juga untuk setiap ATA
            //     'alertStatus' => $pirepAlertStatus, // Anda mungkin ingin menghitung ini juga untuk setiap ATA
            //     'trend' => $pirepTrend, // Anda mungkin ingin menghitung ini juga untuk setiap ATA
            // ];
        // }

        // // Cara 2: Jika tidak ada di database, hitung rata-rata dari 12 data rate yang sudah dihitung (bukan dari query ulang), yaitu variabel $pirepRate, $pirep1Rate, $pirep2Rate, dan 9 rate sebelumnya
        // if (is_null($pirepAlertLevel)) {
        //     // Ambil array rate 12 bulan terakhir dari kolom Rate di pilot-result.blade.php (dikirim dari controller)
        //     $pirepRates = $request->input('rates', []);
        //     // Pastikan hanya 12 data terakhir yang digunakan
        //     if (count($pirepRates) > 12) {
        //     $pirepRates = array_slice($pirepRates, -12);
        //     }
        //     // Jika data rates tidak valid, fallback ke perhitungan lama
        //     if (empty($pirepRates) || count($pirepRates) < 12) {
        //     $pirepRates = [$pirepRate, $pirep1Rate, $pirep2Rate];
        //     for ($i = 3; $i < 12; $i++) {
        //         $loopMonth = \Carbon\Carbon::parse($period)->subMonths($i)->month;
        //         $loopYear = \Carbon\Carbon::parse($period)->subMonths($i)->year;
        //         $fh = $getFlyingHours($aircraftType, \Carbon\Carbon::parse($period)->subMonths($i)->format('Y-m'));
        //         $count = $getPirepCount($aircraftType, $loopMonth, $loopYear);
        //         $rate = $count * 1000 / ($fh ?: 1);
        //         $pirepRates[] = $rate;
        //     }
        //     }
        //     // Hitung rata-rata dan standar deviasi dari 12 data Rate
        //     $mean = array_sum($pirepRates) / count($pirepRates);
        //     $variance = 0;
        //     foreach ($pirepRates as $rate) {
        //     $variance += pow($rate - $mean, 2);
        //     }
        //     $stddev = sqrt($variance / count($pirepRates));
        //     // Alert level = rata-rata + 2 * standar deviasi
        //     $pirepAlertLevel = $mean + 2 * $stddev;
        // }

        // ~~~ PIREP ALERT STATUS ~~~
        $pirepAlertStatus = '';
        if ($pirepRate > $pirepAlertLevel && $pirep1Rate > $pirepAlertLevel && $pirep2Rate > $pirepAlertLevel) {
            $pirepAlertStatus = 'RED-3';
        } elseif ($pirepRate > $pirepAlertLevel && $pirep1Rate > $pirepAlertLevel) {
            $pirepAlertStatus = 'RED-2';
        } elseif ($pirepRate > $pirepAlertLevel) {
            $pirepAlertStatus = 'RED-1';
        }
        // ~~~ PIREP TREND ~~~
        $pirepTrend = '';
        if ($pirep1Rate > $pirep2Rate && $pirep1Rate < $pirepRate) {
            $pirepTrend = 'UP';
        } elseif ($pirep1Rate < $pirep2Rate && $pirep1Rate > $pirepRate) {
            $pirepTrend = 'DOWN';
        }

        // ~~~~~ {{ Maintenance Report }} ~~~~~
        // Fungsi untuk menghitung MAREP
        $getMarepCount = function($aircraftType, $month, $year) {
            return TblPirepSwift::where('ACTYPE', $aircraftType)
            ->whereMonth('DATE', $month)
            ->whereYear('DATE', $year)
            ->where('PirepMarep', 'Marep')
            ->where('ATA', '21')  // Changed from array to single value
            ->count();
        };
        // // Hitung MAREP untuk periode sekarang dan sebelumnya
        $marepCount = $getMarepCount($aircraftType, $month, $year);
        $marepCountBefore = $getMarepCount($aircraftType, \Carbon\Carbon::parse($period)->subMonth(1)->month, \Carbon\Carbon::parse($period)->subMonth(1)->year);
        $marepCountTwoMonthsAgo = $getMarepCount($aircraftType, \Carbon\Carbon::parse($period)->subMonths(2)->month, \Carbon\Carbon::parse($period)->subMonths(2)->year);
        $marep3Month = $marepCount + $marepCountBefore + $marepCountTwoMonthsAgo;
        $marep12Month = 0;  // Changed from string to integer
        for ($i = 0; $i < 12; $i++) {
            $loopMonth = \Carbon\Carbon::parse($period)->subMonths($i)->month;
            $loopYear = \Carbon\Carbon::parse($period)->subMonths($i)->year;
            $marep12Month += $getMarepCount($aircraftType, $loopMonth, $loopYear);  // Fixed variable name
        }
        // ~~~ MAREP RATE PERIOD ~~~
        $marepRate = $marepCount * 1000 / ($flyingHoursTotal ?: 1);
        $marep1Rate = $marepCountBefore * 1000 / ($flyingHoursBefore ?: 1);
        $marep2Rate = $marepCountTwoMonthsAgo * 1000 / ($flyingHours2Before ?: 1);
        $marepRate3Month = ($marepRate + $marep1Rate + $marep2Rate) / 3;
        $marepRate12Month = $marep12Month * 1000 / ($fh12Last ?: 1);
        // ~~~ MAREP ALERT LEVEL ~~~
        // Cara 1: Cek apakah data alertlevel untuk period dan actype/ata/type sudah ada di tbl_alertlevel
        $marepAlertLevel = TblAlertLevel::where('actype', $aircraftType)
            ->where('ata', '21')
            ->where('type', 'ALM')
            ->where(function($query) use ($period) {
                $query->whereBetween('startmonth', [$period, $period])
                    ->orWhereBetween('endmonth', [$period, $period])
                    ->orWhere(function($query) use ($period) {
                        $query->where('startmonth', '<=', $period)
                            ->where('endmonth', '>=', $period);
                    });
            })
            ->pluck('alertlevel')
            ->first();

    // // Cara 2: Jika tidak ada di database, hitung rata-rata dari 12 data rate yang sudah dihitung (bukan dari query ulang), yaitu variabel $marepRate, $marep1Rate, $marep2Rate, dan 9 rate sebelumnya
    // if (is_null($marepAlertLevel)) {
    //     // Ambil array rate 12 bulan terakhir dari kolom Rate di pilot-result.blade.php (dikirim dari controller)
    //     $marepRates = $request->input('marep_rates', []);
    //     // Pastikan hanya 12 data terakhir yang digunakan
    //     if (count($marepRates) > 12) {
    //         $marepRates = array_slice($marepRates, -12);
    //     }
    //     // Jika data rates tidak valid, fallback ke perhitungan lama
    //     if (empty($marepRates) || count($marepRates) < 12) {
    //         $marepRates = [$marepRate, $marep1Rate, $marep2Rate];
    //         for ($i = 3; $i < 12; $i++) {
    //             $loopMonth = \Carbon\Carbon::parse($period)->subMonths($i)->month;
    //             $loopYear = \Carbon\Carbon::parse($period)->subMonths($i)->year;
    //             $fh = $getFlyingHours($aircraftType, \Carbon\Carbon::parse($period)->subMonths($i)->format('Y-m'));
    //             $count = $getMarepCount($aircraftType, $loopMonth, $loopYear);
    //             $rate = $count * 1000 / ($fh ?: 1);
    //             $marepRates[] = $rate;
    //         }
    //     }
    //     // Hitung rata-rata dan standar deviasi dari 12 data Rate
    //     $mean = array_sum($marepRates) / count($marepRates);
    //     $variance = 0;
    //     foreach ($marepRates as $rate) {
    //         $variance += pow($rate - $mean, 2);
    //     }
    //     $stddev = sqrt($variance / count($marepRates));
    //     // Alert level = 2 * standar deviasi (tanpa mean)
    //     $marepAlertLevel = 2 * $stddev;
    // }

        // ~~~ MAREP ALERT STATUS ~~~
        $marepAlertStatus = '';
        if ($marepRate > $marepAlertLevel && $marep1Rate > $marepAlertLevel && $marep2Rate > $marepAlertLevel){
            $marepAlertStatus = 'RED-3';
        } elseif ($marepRate > $marepAlertLevel && $marep1Rate > $marepAlertLevel){
            $marepAlertStatus = 'RED-2';
        } elseif ($marepRate > $marepAlertLevel){
            $marepAlertStatus = 'RED-1';
        }
        // ~~~ MAREP TREND ~~~
        $marepTrend = '';
        if ($marep1Rate > $marep2Rate && $marep1Rate < $marepRate) {
            $marepTrend = 'UP';
        } elseif ($marep1Rate < $marep2Rate && $marep1Rate > $marepRate) {
            $marepTrend = 'DOWN';
        }

        // ~~~~~ {{ Technical Delay }} ~~~~~
        // ~~~ COUNTING TECHNICAL DELAY ~~~
        $getDelayCount = function($aircraftType, $month, $year) {
            return Mcdrnew::where('ACtype', $aircraftType)
            ->whereMonth('DateEvent', $month)
            ->whereYear('DateEvent', $year)
            ->where('DCP', '<>', 'X')
            ->where('ATAtdm', '21')  // Changed from array to single value
            ->count();
        };
        $delayCount = $getDelayCount($aircraftType, $month, $year);
        $delayCountBefore = $getDelayCount($aircraftType, \Carbon\Carbon::parse($period)->subMonth(1)->month, \Carbon\Carbon::parse($period)->subMonth(1)->year);
        $delayCountTwoMonthsAgo = $getDelayCount($aircraftType, \Carbon\Carbon::parse($period)->subMonths(2)->month, \Carbon\Carbon::parse($period)->subMonths(2)->year);
        $delay3Month = $delayCount + $delayCountBefore + $delayCountTwoMonthsAgo;
        $delay12Month = 0;  // Changed from string to integer
        for ($i = 0; $i < 12; $i++) {
            $loopMonth = \Carbon\Carbon::parse($period)->subMonths($i)->month;
            $loopYear = \Carbon\Carbon::parse($period)->subMonths($i)->year;
            $delay12Month += $getDelayCount($aircraftType, $loopMonth, $loopYear);  // Fixed variable name
        }

        // ~~~ TECHNICAL DELAY RATE ~~~
        $delayRate = $delayCount * 1000 / ($flyingHoursTotal ?: 1);
        $delay1Rate = $delayCountBefore * 1000 / ($flyingHoursBefore ?: 1);
        $delay2Rate = $delayCountTwoMonthsAgo * 1000 / ($flyingHours2Before ?: 1);
        $delayRate3Month = ($delayRate + $delay1Rate + $delay2Rate) / 3;
        $delayRate12Month = $delay12Month * 1000 / ($fh12Last ?: 1);
        
        // ~~~ TECHNICAL DELAY ALERT LEVEL ~~~
        // Cara 1: Cek apakah data alertlevel untuk period dan actype/ata/type sudah ada di tbl_alertlevel
        $delayAlertLevel = TblAlertLevel::where('actype', $aircraftType)
            ->where('ata', '21')
            ->where('type', 'ALD')
            ->where(function($query) use ($period){
                $query->whereBetween('startmonth', [$period, $period])
                    ->orWhereBetween('endmonth', [$period, $period])
                    ->orWhere(function($query) use ($period){
                        $query->where('startmonth', '<=', $period)
                            ->where('endmonth', '>=', $period);
                    });
            })
            ->pluck('alertlevel')
            ->first();

        // // Cara 2: Jika tidak ada di database, hitung rata-rata kolom RATE 12 bulan + 2 * standar deviasi
        // if (is_null($delayAlertLevel)) {
        //     // Ambil 12 rate dari variabel yang sudah dihitung (bukan query ulang)
        //     $delayRates = [$delayRate, $delay1Rate, $delay2Rate];
        //     for ($i = 3; $i < 12; $i++) {
        //     $loopMonth = \Carbon\Carbon::parse($period)->subMonths($i)->month;
        //     $loopYear = \Carbon\Carbon::parse($period)->subMonths($i)->year;
        //     $fh = $getFlyingHours($aircraftType, \Carbon\Carbon::parse($period)->subMonths($i)->format('Y-m'));
        //     $count = $getDelayCount($aircraftType, $loopMonth, $loopYear);
        //     $rate = $count * 1000 / ($fh ?: 1);
        //     $delayRates[] = $rate;
        //     }
        //     $mean = array_sum($delayRates) / count($delayRates);
        //     $variance = 0;
        //     foreach ($delayRates as $rate) {
        //     $variance += pow($rate - $mean, 2);
        //     }
        //     $stddev = sqrt($variance / count($delayRates));
        //     $delayAlertLevel = $mean + 2 * $stddev;
        // // }

         // ~~~ TECHNICAL DELAY ALERT STATUS ~~~
         $delayAlertStatus = '';
         if ($delayRate > $delayAlertLevel && $delay1Rate > $delayAlertLevel && $delay2Rate > $delayAlertLevel){
             $delayAlertStatus = 'RED-3';
         } elseif ($delayRate > $delayAlertLevel && $delay1Rate > $delayAlertLevel){
             $delayAlertStatus = 'RED-2';
         } elseif ($delayRate > $delayAlertLevel){
             $delayAlertStatus = 'RED-1';
         }
         // ~~~ TECHNICAL DELAY TREND ~~~
         $delayTrend = '';
         if ($delay1Rate > $delay2Rate && $delay1Rate < $delayRate) {
             $delayTrend = 'UP';
         } elseif ($delay1Rate < $delay2Rate && $delay1Rate > $delayRate) {
             $delayTrend = 'DOWN';
         }
        
        // Mengirimkan semua variabel yang diperlukan ke view
        return view('report.pilot-result', [
            'flyingHoursTotal' => $flyingHoursTotal, 'flyingHoursBefore' => $flyingHoursBefore,
            'flyingHours2Before' => $flyingHours2Before, 'fh3Last' => $fh3Last, 'fh12Last' => $fh12Last, 
            'aircraftType' => $aircraftType, 'tblAta' => $tblAta, 'month' => $month, 'period' => $period,
            // 'pirepData' => $pirepData,
            'pirepCount' => $pirepCount, // AWAL PILOT REPORT
            'pirepCountBefore' => $pirepCountBefore, 'pirepCountTwoMonthsAgo' => $pirepCountTwoMonthsAgo,
            'pirep3Month' => $pirep3Month, 'pirep12Month' => $pirep12Month,
            'pirepRate' => $pirepRate, 'pirep1Rate' => $pirep1Rate,
            'pirep2Rate' => $pirep2Rate, 'pirepRate3Month' => $pirepRate3Month,
            'pirepRate12Month' => $pirepRate12Month, 'pirepAlertLevel' => $pirepAlertLevel,
            'pirepAlertStatus' => $pirepAlertStatus, 'pirepTrend' => $pirepTrend,
            'marepCount' => $marepCount, // AWAL MAINTENANCE REPORT
            'marepCountBefore' => $marepCountBefore, 'marepCountTwoMonthsAgo' => $marepCountTwoMonthsAgo,
            'marep3Month' => $marep3Month, 'marep12Month' => $marep12Month,
            'marepRate' => $marepRate, 'marep1Rate' => $marep1Rate,
            'marep2Rate' => $marep2Rate, 'marepRate3Month' => $marepRate3Month,
            'marepRate12Month' => $marepRate12Month, 'marepAlertLevel' => $marepAlertLevel,
            'marepAlertStatus' => $marepAlertStatus, 'marepTrend' => $marepTrend,
            'delayCount' => $delayCount, // AWAL TECHNICAL DELAY
            'delayCountBefore'=> $delayCountBefore, 'delayCountTwoMonthsAgo' => $delayCountTwoMonthsAgo,
            'delay3Month' => $delay3Month, 'delay12Month' => $delay12Month,
            'delayRate' => $delayRate, 'delay1Rate' => $delay1Rate,
            'delay2Rate' => $delay2Rate, 'delayRate3Month' => $delayRate3Month,
            'delayRate12Month' => $delayRate12Month, 'delayAlertLevel' => $delayAlertLevel,
            'delayAlertStatus' => $delayAlertStatus, 'delayTrend' => $delayTrend
        ]);
    }

    public function pilotPdf(Request $request){
        // Validate input
        $request->validate([
            'period' => 'required',
            'aircraft_type' => 'required',
        ]);
    
        $aircraftType = $request->aircraft_type;
        $period = $request->period; // Format: YYYY-MM
    
        // Mendapatkan bulan dan tahun dari periode
        $month = date('m', strtotime($period));
        $year = date('Y', strtotime($period));
    
        // Fungsi untuk menghitung total flying hours
        $getFlyingHours = function($aircraftType, $period) {
            return TblMonthlyfhfc::where('Actype', $aircraftType)
                ->where('MonthEval', $period)
                ->selectRaw('SUM(RevFHHours + (RevFHMin / 60) + NoRevFHHours + (NoRevFHMin / 60)) as total')
                ->first()->total ?? 0; // Menggunakan null coalescing operator
        };
    
        // Hitung flying hours untuk periode sekarang dan sebelumnya
        $flyingHoursTotal = $getFlyingHours($aircraftType, $period);
        $flyingHoursBefore = $getFlyingHours($aircraftType, \Carbon\Carbon::parse($period)->subMonth(1));
        $flyingHours2Before = $getFlyingHours($aircraftType, \Carbon\Carbon::parse($period)->subMonth(2));
        $fh3Last = $flyingHoursTotal + $flyingHoursBefore + $flyingHours2Before;
        $fh12Last = 0;
        for ($i = 0; $i <= 11; $i++) {
            $periodBefore = \Carbon\Carbon::parse($period)->subMonth($i);
            $fh12Last += $getFlyingHours($aircraftType, $periodBefore);
        }
    
        // Daftar ID yang ingin dikecualikan
        $excludedIds = [5, 11, 12, 58, 70];
        // Ambil semua data dari tbl_master_ata kecuali yang ada di $excludedIds
        $tblAta = TblMasterAta::whereNotIn('ATA', $excludedIds)->get();
        
        // ~~~~~ {{ PILOT REPORT }} ~~~~~
        // Fungsi untuk menghitung PIREP
        $getPirepCount = function($aircraftType, $month, $year) {
            return TblPirepSwift::where('ACTYPE', $aircraftType)
                ->whereMonth('DATE', $month)
                ->whereYear('DATE', $year)
                ->where('PirepMarep', 'Pirep')
                ->where('ATA', '21')  // Changed from whereIn to where
                ->count();
        };
        // Hitung PIREP untuk periode sekarang dan sebelumnya
        $pirepCount = $getPirepCount($aircraftType, $month, $year);
        $pirepCountBefore = $getPirepCount($aircraftType, \Carbon\Carbon::parse($period)->subMonth(1)->month, \Carbon\Carbon::parse($period)->subMonth(1)->year);
        $pirepCountTwoMonthsAgo = $getPirepCount($aircraftType, \Carbon\Carbon::parse($period)->subMonths(2)->month, \Carbon\Carbon::parse($period)->subMonths(2)->year);
        $pirep3Month = $pirepCount + $pirepCountBefore + $pirepCountTwoMonthsAgo;
        $pirep12Month = 0;
        for ($i = 0; $i < 12; $i++) {
            $loopMonth = \Carbon\Carbon::parse($period)->subMonths($i)->month;
            $loopYear = \Carbon\Carbon::parse($period)->subMonths($i)->year;
            $pirep12Month += $getPirepCount($aircraftType, $loopMonth, $loopYear);
        }
        // ~~~ PIREP RATE PERIOD ~~~
        $pirepRate = $pirepCount * 1000 / ($flyingHoursTotal ?: 1); // Menghindari pembagian dengan nol
        $pirep1Rate = $pirepCountBefore * 1000 / ($flyingHoursBefore ?: 1);
        $pirep2Rate = $pirepCountTwoMonthsAgo * 1000 / ($flyingHours2Before ?: 1);
        $pirepRate3Month = ($pirepRate + $pirep1Rate + $pirep2Rate) / 3;
        $pirepRate12Month = $pirep12Month * 1000 / ($fh12Last ?: 1);   
        
        // PIREP ALERT LEVEL
        // Cara 1: Cek apakah data alertlevel untuk period dan actype/ata/type sudah ada di tbl_alertlevel
        $pirepAlertLevel = TblAlertLevel::where('actype', $aircraftType)
            ->where('ata', '21')
            ->where('type', 'ALP')
            ->where(function($query) use ($period) {
            $query->whereBetween('startmonth', [$period, $period])
                ->orWhereBetween('endmonth', [$period, $period])
                ->orWhere(function($query) use ($period) {
                $query->where('startmonth', '<=', $period)
                      ->where('endmonth', '>=', $period);
                });
            })
            ->pluck('alertlevel')
            ->first();

        // // Cara 2: Jika tidak ada di database, hitung rata-rata 12 bulan + 2 * standar deviasi
        // if (is_null($pirepAlertLevel)) {
        //     $pirepRates = [];
        //     for ($i = 0; $i < 12; $i++) {
        //     $loopMonth = \Carbon\Carbon::parse($period)->subMonths($i)->month;
        //     $loopYear = \Carbon\Carbon::parse($period)->subMonths($i)->year;
        //     $fh = $getFlyingHours($aircraftType, \Carbon\Carbon::parse($period)->subMonths($i)->format('Y-m'));
        //     $count = $getPirepCount($aircraftType, $loopMonth, $loopYear);
        //     $rate = $count * 1000 / ($fh ?: 1);
        //     $pirepRates[] = $rate;
        //     }
        //     $mean = array_sum($pirepRates) / count($pirepRates);
        //     $variance = 0;
        //     foreach ($pirepRates as $rate) {
        //     $variance += pow($rate - $mean, 2);
        //     }
        //     $stddev = sqrt($variance / count($pirepRates));
        //     $pirepAlertLevel = $mean + 2 * $stddev;
        // }
        // ~~~ PIREP ALERT STATUS ~~~
        $pirepAlertStatus = '';
        if ($pirepRate > $pirepAlertLevel && $pirep1Rate > $pirepAlertLevel && $pirep2Rate > $pirepAlertLevel) {
            $pirepAlertStatus = 'RED-3';
        } elseif ($pirepRate > $pirepAlertLevel && $pirep1Rate > $pirepAlertLevel) {
            $pirepAlertStatus = 'RED-2';
        } elseif ($pirepRate > $pirepAlertLevel) {
            $pirepAlertStatus = 'RED-1';
        }
        // ~~~ PIREP TREND ~~~
        $pirepTrend = '';
        if ($pirep1Rate > $pirep2Rate && $pirep1Rate < $pirepRate) {
            $pirepTrend = 'UP';
        } elseif ($pirep1Rate < $pirep2Rate && $pirep1Rate > $pirepRate) {
            $pirepTrend = 'DOWN';
        }

        // ~~~~~ {{ Maintenance Report }} ~~~~~
        // Fungsi untuk menghitung MAREP
        $getMarepCount = function($aircraftType, $month, $year) {
            return TblPirepSwift::where('ACTYPE', $aircraftType)
            ->whereMonth('DATE', $month)
            ->whereYear('DATE', $year)
            ->where('PirepMarep', 'Marep')
            ->where('ATA', '21')  // Changed from whereIn to where
            ->count();
        };
        // // Hitung MAREP untuk periode sekarang dan sebelumnya
        $marepCount = $getMarepCount($aircraftType, $month, $year);
        $marepCountBefore = $getMarepCount($aircraftType, \Carbon\Carbon::parse($period)->subMonth(1)->month, \Carbon\Carbon::parse($period)->subMonth(1)->year);
        $marepCountTwoMonthsAgo = $getMarepCount($aircraftType, \Carbon\Carbon::parse($period)->subMonths(2)->month, \Carbon\Carbon::parse($period)->subMonths(2)->year);
        $marep3Month = $marepCount + $marepCountBefore + $marepCountTwoMonthsAgo;
        $marep12Month = 0;  // Changed from string to integer
        for ($i = 0; $i < 12; $i++) {
            $loopMonth = \Carbon\Carbon::parse($period)->subMonths($i)->month;
            $loopYear = \Carbon\Carbon::parse($period)->subMonths($i)->year;
            $marep12Month += $getMarepCount($aircraftType, $loopMonth, $loopYear);  // Fixed variable name
        }
        // ~~~ MAREP RATE PERIOD ~~~
        $marepRate = $marepCount * 1000 / ($flyingHoursTotal ?: 1);
        $marep1Rate = $marepCountBefore * 1000 / ($flyingHoursBefore ?: 1);
        $marep2Rate = $marepCountTwoMonthsAgo * 1000 / ($flyingHours2Before ?: 1);
        $marepRate3Month = ($marepRate + $marep1Rate + $marep2Rate) / 3;
        $marepRate12Month = $marep12Month * 1000 / ($fh12Last ?: 1);
        // ~~~ MAREP ALERT LEVEL ~~~
        // Cara 1: Cek apakah data alertlevel untuk period dan actype/ata/type sudah ada di tbl_alertlevel
        $marepAlertLevel = TblAlertLevel::where('actype', $aircraftType)
            ->where('ata', '21')
            ->where('type', 'ALM')
            ->where(function($query) use ($period) {
                $query->whereBetween('startmonth', [$period, $period])
                    ->orWhereBetween('endmonth', [$period, $period])
                    ->orWhere(function($query) use ($period) {
                        $query->where('startmonth', '<=', $period)
                            ->where('endmonth', '>=', $period);
                    });
            })
            ->pluck('alertlevel')
            ->first();

        // // Cara 2: Jika tidak ada di database, hitung rata-rata 12 bulan + 2 * standar deviasi
        // if (is_null($marepAlertLevel)) {
        //     $marepRates = [];
        //     for ($i = 0; $i < 12; $i++) {
        //         $loopMonth = \Carbon\Carbon::parse($period)->subMonths($i)->month;
        //         $loopYear = \Carbon\Carbon::parse($period)->subMonths($i)->year;
        //         $fh = $getFlyingHours($aircraftType, \Carbon\Carbon::parse($period)->subMonths($i)->format('Y-m'));
        //         $count = $getMarepCount($aircraftType, $loopMonth, $loopYear);
        //         $rate = $count * 1000 / ($fh ?: 1);
        //         $marepRates[] = $rate;
        //     }
        //     $mean = array_sum($marepRates) / count($marepRates);
        //     $variance = 0;
        //     foreach ($marepRates as $rate) {
        //         $variance += pow($rate - $mean, 2);
        //     }
        //     $stddev = sqrt($variance / count($marepRates));
        //     $marepAlertLevel = $mean + 2 * $stddev;
        // }$marepAlertLevel = TblAlertLevel::where('actype', $aircraftType)
        //     ->where('ata', '21')
        //     ->where('type', 'ALM')
        //     ->where(function($query) use ($period){
        //         $query->whereBetween('startmonth', [$period, $period])
        //             ->orWhereBetween('endmonth', [$period, $period])
        //             ->orWhere(function($query) use ($period){
        //                 $query->where('startmonth', '<=', $period)
        //                     ->where('endmonth', '>=', $period);
        //             });
        //     })
        //     ->pluck('alertlevel')
        //     ->first();

        // ~~~ MAREP ALERT STATUS ~~~
        $marepAlertStatus = '';
        if ($marepRate > $marepAlertLevel && $marep1Rate > $marepAlertLevel && $marep2Rate > $marepAlertLevel){
            $marepAlertStatus = 'RED-3';
        } elseif ($marepRate > $marepAlertLevel && $marep1Rate > $marepAlertLevel){
            $marepAlertStatus = 'RED-2';
        } elseif ($marepRate > $marepAlertLevel){
            $marepAlertStatus = 'RED-1';
        }
        // ~~~ MAREP TREND ~~~
        $marepTrend = '';
        if ($marep1Rate > $marep2Rate && $marep1Rate < $marepRate) {
            $marepTrend = 'UP';
        } elseif ($marep1Rate < $marep2Rate && $marep1Rate > $marepRate) {
            $marepTrend = 'DOWN';
        }


        // ~~~~~ {{ Technical Delay }} ~~~~~
        // ~~~ COUNTING TECHNICAL DELAY ~~~
        $getDelayCount = function($aircraftType, $month, $year) {
            return Mcdrnew::where('ACtype', $aircraftType)
            ->whereMonth('DateEvent', $month)
            ->whereYear('DateEvent', $year)
            ->where('DCP', '<>', 'X')
            ->where('ATAtdm', '21')  // Changed from array to single value
            ->count();
        };
        $delayCount = $getDelayCount($aircraftType, $month, $year);
        $delayCountBefore = $getDelayCount($aircraftType, \Carbon\Carbon::parse($period)->subMonth(1)->month, \Carbon\Carbon::parse($period)->subMonth(1)->year);
        $delayCountTwoMonthsAgo = $getDelayCount($aircraftType, \Carbon\Carbon::parse($period)->subMonths(2)->month, \Carbon\Carbon::parse($period)->subMonths(2)->year);
        $delay3Month = $delayCount + $delayCountBefore + $delayCountTwoMonthsAgo;
        $delay12Month = 0;  // Changed from string to integer
        for ($i = 0; $i < 12; $i++) {
            $loopMonth = \Carbon\Carbon::parse($period)->subMonths($i)->month;
            $loopYear = \Carbon\Carbon::parse($period)->subMonths($i)->year;
            $delay12Month += $getDelayCount($aircraftType, $loopMonth, $loopYear);  // Fixed variable name
        }
        // ~~~ TECHNICAL DELAY RATE ~~~
        $delayRate = $delayCount * 1000 / ($flyingHoursTotal ?: 1);
        $delay1Rate = $delayCountBefore * 1000 / ($flyingHoursBefore ?: 1);
        $delay2Rate = $delayCountTwoMonthsAgo * 1000 / ($flyingHours2Before ?: 1);
        $delayRate3Month = ($delayRate + $delay1Rate + $delay2Rate) / 3;
        $delayRate12Month = $delay12Month * 1000 / ($fh12Last ?: 1);
        // ~~~ TECHNICAL DELAY ALERT LEVEL ~~~
        // Cara 1: Cek apakah data alertlevel untuk period dan actype/ata/type sudah ada di tbl_alertlevel
        $delayAlertLevel = TblAlertLevel::where('actype', $aircraftType)
            ->where('ata', '21')
            ->where('type', 'ALD')
            ->where(function($query) use ($period){
                $query->whereBetween('startmonth', [$period, $period])
                    ->orWhereBetween('endmonth', [$period, $period])
                    ->orWhere(function($query) use ($period){
                        $query->where('startmonth', '<=', $period)
                            ->where('endmonth', '>=', $period);
                    });
            })
            ->pluck('alertlevel')
            ->first();

        // // Cara 2: Jika tidak ada di database, hitung rata-rata 12 bulan + 2 * standar deviasi
        // if (is_null($delayAlertLevel)) {
        //     $delayRates = [];
        //     for ($i = 0; $i < 12; $i++) {
        //         $loopMonth = \Carbon\Carbon::parse($period)->subMonths($i)->month;
        //         $loopYear = \Carbon\Carbon::parse($period)->subMonths($i)->year;
        //         $fh = $getFlyingHours($aircraftType, \Carbon\Carbon::parse($period)->subMonths($i)->format('Y-m'));
        //         $count = $getDelayCount($aircraftType, $loopMonth, $loopYear);
        //         $rate = $count * 1000 / ($fh ?: 1);
        //         $delayRates[] = $rate;
        //     }
        //     $mean = array_sum($delayRates) / count($delayRates);
        //     $variance = 0;
        //     foreach ($delayRates as $rate) {
        //         $variance += pow($rate - $mean, 2);
        //     }
        //     $stddev = sqrt($variance / count($delayRates));
        //     $delayAlertLevel = $mean + 2 * $stddev;
        // }
        
         // ~~~ TECHNICAL DELAY ALERT STATUS ~~~
         $delayAlertStatus = '';
         if ($delayRate > $delayAlertLevel && $delay1Rate > $delayAlertLevel && $delay2Rate > $delayAlertLevel){
             $delayAlertStatus = 'RED-3';
         } elseif ($delayRate > $delayAlertLevel && $delay1Rate > $delayAlertLevel){
             $delayAlertStatus = 'RED-2';
         } elseif ($delayRate > $delayAlertLevel){
             $delayAlertStatus = 'RED-1';
         }
         // ~~~ TECHNICAL DELAY TREND ~~~
         $delayTrend = '';
         if ($delay1Rate > $delay2Rate && $delay1Rate < $delayRate) {
             $delayTrend = 'UP';
         } elseif ($delay1Rate < $delay2Rate && $delay1Rate > $delayRate) {
             $delayTrend = 'DOWN';
         }
        // Mengirimkan semua variabel yang diperlukan ke view
        $pdf = Pdf::loadView('pdf.pilot-pdf', [
            'flyingHoursTotal' => $flyingHoursTotal, 'flyingHoursBefore' => $flyingHoursBefore,
            'flyingHours2Before' => $flyingHours2Before, 'fh3Last' => $fh3Last, 'fh12Last' => $fh12Last, 
            'aircraftType' => $aircraftType, 'tblAta' => $tblAta, 'month' => $month, 'period' => $period,
            // 'pirepData' => $pirepData,
            'pirepCount' => $pirepCount, // AWAL PILOT REPORT
            'pirepCountBefore' => $pirepCountBefore, 'pirepCountTwoMonthsAgo' => $pirepCountTwoMonthsAgo,
            'pirep3Month' => $pirep3Month, 'pirep12Month' => $pirep12Month,
            'pirepRate' => $pirepRate, 'pirep1Rate' => $pirep1Rate,
            'pirep2Rate' => $pirep2Rate, 'pirepRate3Month' => $pirepRate3Month,
            'pirepRate12Month' => $pirepRate12Month, 'pirepAlertLevel' => $pirepAlertLevel,
            'pirepAlertStatus' => $pirepAlertStatus, 'pirepTrend' => $pirepTrend,
            'marepCount' => $marepCount, // AWAL MAINTENANCE REPORT
            'marepCountBefore' => $marepCountBefore, 'marepCountTwoMonthsAgo' => $marepCountTwoMonthsAgo,
            'marep3Month' => $marep3Month, 'marep12Month' => $marep12Month,
            'marepRate' => $marepRate, 'marep1Rate' => $marep1Rate,
            'marep2Rate' => $marep2Rate, 'marepRate3Month' => $marepRate3Month,
            'marepRate12Month' => $marepRate12Month, 'marepAlertLevel' => $marepAlertLevel,
            'marepAlertStatus' => $marepAlertStatus, 'marepTrend' => $marepTrend,
            'delayCount' => $delayCount, // AWAL TECHNICAL DELAY
            'delayCountBefore'=> $delayCountBefore, 'delayCountTwoMonthsAgo' => $delayCountTwoMonthsAgo,
            'delay3Month' => $delay3Month, 'delay12Month' => $delay12Month,
            'delayRate' => $delayRate, 'delay1Rate' => $delay1Rate,
            'delay2Rate' => $delay2Rate, 'delayRate3Month' => $delayRate3Month,
            'delayRate12Month' => $delayRate12Month, 'delayAlertLevel' => $delayAlertLevel,
            'delayAlertStatus' => $delayAlertStatus, 'delayTrend' => $delayTrend
        ]);
        return $pdf->download('Pilot_Report_' . $aircraftType . '_' . $period . '.pdf');
    }
}