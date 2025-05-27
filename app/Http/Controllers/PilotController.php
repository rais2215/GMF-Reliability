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
    public function pilotIndex()
    {
        $aircraftTypes = TblPirepSwift::select('ACTYPE')->distinct()
            ->whereNotNull('ACTYPE')
            ->where('ACTYPE', '!=', '')
            ->where('ACTYPE', '!=', 'default')
            ->get();

        $operators = TblMasterac::select('Operator')->distinct()
            ->whereNotNull('Operator')
            ->where('Operator', '!=', '')
            ->get();

        $periods = TblMonthlyfhfc::select('MonthEval')->distinct()
            ->orderByDesc('MonthEval')->get()
            ->map(function($item) {
                return [
                    'formatted' => Carbon::parse($item->MonthEval)->format('Y-m'),
                    'original' => $item->MonthEval
                ];
            });

        return view('report.pilot-content', compact('aircraftTypes', 'operators', 'periods'));
    }

    public function pilotStore(Request $request)
    {
        $request->validate([
            'period' => 'required',
            'aircraft_type' => 'required',
        ]);

        $aircraftType = $request->aircraft_type;
        $period = $request->period; // Format: YYYY-MM
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

        $excludedIds = [5, 11, 12, 58, 70];
        $tblAta = TblMasterAta::whereNotIn('ATA', $excludedIds)->get();

        // Fungsi perhitungan per ATA
        $getPirepCount = function($aircraftType, $month, $year, $ata) {
            return TblPirepSwift::where('ACTYPE', $aircraftType)
                ->whereMonth('DATE', $month)
                ->whereYear('DATE', $year)
                ->where('PirepMarep', 'Pirep')
                ->where('ATA', $ata)
                ->count();
        };
        $getMarepCount = function($aircraftType, $month, $year, $ata) {
            return TblPirepSwift::where('ACTYPE', $aircraftType)
                ->whereMonth('DATE', $month)
                ->whereYear('DATE', $year)
                ->where('PirepMarep', 'Marep')
                ->where('ATA', $ata)
                ->count();
        };
        $getDelayCount = function($aircraftType, $month, $year, $ata) {
            return Mcdrnew::where('ACtype', $aircraftType)
                ->whereMonth('DateEvent', $month)
                ->whereYear('DateEvent', $year)
                ->where('DCP', '<>', 'X')
                ->where('ATAtdm', $ata)
                ->count();
        };

        $reportPerAta = [];

        foreach ($tblAta as $ataRow) {
            $ata = $ataRow->ATA;
            $ata_name = $ataRow->ATA_DESC ?? $ataRow->ATAName ?? '';

            // PIREP
            $pirepCount = $getPirepCount($aircraftType, $month, $year, $ata);
            $pirepCountBefore = $getPirepCount($aircraftType, \Carbon\Carbon::parse($period)->subMonth(1)->month, \Carbon\Carbon::parse($period)->subMonth(1)->year, $ata);
            $pirepCountTwoMonthsAgo = $getPirepCount($aircraftType, \Carbon\Carbon::parse($period)->subMonths(2)->month, \Carbon\Carbon::parse($period)->subMonths(2)->year, $ata);
            $pirep3Month = $pirepCount + $pirepCountBefore + $pirepCountTwoMonthsAgo;
            $pirep12Month = 0;
            for ($i = 0; $i < 12; $i++) {
                $loopMonth = \Carbon\Carbon::parse($period)->subMonths($i)->month;
                $loopYear = \Carbon\Carbon::parse($period)->subMonths($i)->year;
                $pirep12Month += $getPirepCount($aircraftType, $loopMonth, $loopYear, $ata);
            }
            $pirepRate = $pirepCount * 1000 / ($flyingHoursTotal ?: 1);
            $pirep1Rate = $pirepCountBefore * 1000 / ($flyingHoursBefore ?: 1);
            $pirep2Rate = $pirepCountTwoMonthsAgo * 1000 / ($flyingHours2Before ?: 1);
            $pirepRate3Month = ($pirepRate + $pirep1Rate + $pirep2Rate) / 3;
            $pirepRate12Month = $pirep12Month * 1000 / ($fh12Last ?: 1);
            
            // PIREP ALERT LEVEL
            $pirepAlertLevel = TblAlertLevel::where('actype', $aircraftType)
                ->where('ata', $ata)
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

            if (is_null($pirepAlertLevel)) {
            // Ambil array rate 12 bulan terakhir dari kolom Rate di pilot-result.blade.php
            $pirepRates = $request->input('rates', []);
            // Pastikan hanya 12 data terakhir yang digunakan
            if (count($pirepRates) > 12) {
            $pirepRates = array_slice($pirepRates, -12);
            }
            // Jika data rates tidak valid, fallback ke perhitungan lama
            if (empty($pirepRates) || count($pirepRates) < 12) {
            $pirepRates = [$pirepRate, $pirep1Rate, $pirep2Rate];
                for ($i = 3; $i < 12; $i++) {
                    $loopMonth = \Carbon\Carbon::parse($period)->subMonths($i)->month;
                    $loopYear = \Carbon\Carbon::parse($period)->subMonths($i)->year;
                    $fh = $getFlyingHours($aircraftType, \Carbon\Carbon::parse($period)->subMonths($i)->format('Y-m'));
                    $count = $getPirepCount($aircraftType, $loopMonth, $loopYear, $ata);
                    $pirepRate12Month = $pirep12Month * 1000 / ($fh12Last ?: 1);
                }   
            }
            $stddev = sqrt($pirepRate12Month / count($pirepRates));
            // Alert level = rata-rata + 2 * standar deviasi
            $pirepAlertLevel = $pirepRate12Month + 2 * $stddev;
            }

            // ~~~ PIREP ALERT STATUS ~~~
            $pirepAlertStatus = '';
            $alertCount = 0;
            if ($pirepRate > $pirepAlertLevel) $alertCount++;
            if ($pirep1Rate > $pirepAlertLevel) $alertCount++;
            if ($pirep2Rate > $pirepAlertLevel) $alertCount++;

            if ($alertCount == 3) {
                $pirepAlertStatus = 'RED-3';
            } elseif ($alertCount == 2) {
                $pirepAlertStatus = 'RED-2';
            } elseif ($alertCount == 1) {
                $pirepAlertStatus = 'RED-1';
            }
            
            // ~~~ PIREP TREND ~~~
            $pirepTrend = '';
            if ($pirepRate < $pirep1Rate && $pirep1Rate < $pirep2Rate) {
                $pirepTrend = 'DOWN';
            } elseif ($pirepRate > $pirep1Rate && $pirep1Rate < $pirep2Rate) {
                $pirepTrend = '';
            } elseif ($pirepRate > $pirep1Rate && $pirep1Rate > $pirep2Rate) {
                $pirepTrend = 'UP';
            }

            // MAREP
            $marepCount = $getMarepCount($aircraftType, $month, $year, $ata);
            $marepCountBefore = $getMarepCount($aircraftType, \Carbon\Carbon::parse($period)->subMonth(1)->month, \Carbon\Carbon::parse($period)->subMonth(1)->year, $ata);
            $marepCountTwoMonthsAgo = $getMarepCount($aircraftType, \Carbon\Carbon::parse($period)->subMonths(2)->month, \Carbon\Carbon::parse($period)->subMonths(2)->year, $ata);
            $marep3Month = $marepCount + $marepCountBefore + $marepCountTwoMonthsAgo;
            $marep12Month = 0;
            for ($i = 0; $i < 12; $i++) {
                $loopMonth = \Carbon\Carbon::parse($period)->subMonths($i)->month;
                $loopYear = \Carbon\Carbon::parse($period)->subMonths($i)->year;
                $marep12Month += $getMarepCount($aircraftType, $loopMonth, $loopYear, $ata); 
            }

            // ~~~ MAREP RATE PERIOD ~~~
            $marepRate = $marepCount * 1000 / ($flyingHoursTotal ?: 1);
            $marep1Rate = $marepCountBefore * 1000 / ($flyingHoursBefore ?: 1);
            $marep2Rate = $marepCountTwoMonthsAgo * 1000 / ($flyingHours2Before ?: 1);
            $marepRate3Month = ($marepRate + $marep1Rate + $marep2Rate) / 3;
            $marepRate12Month = $marep12Month * 1000 / ($fh12Last ?: 1);
            
            // ~~~ MAREP ALERT LEVEL ~~~
            $marepAlertLevel = TblAlertLevel::where('actype', $aircraftType)
                ->where('ata', $ata)
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

            if (is_null($marepAlertLevel)) {
                // Ambil array rate 12 bulan terakhir dari kolom Rate di pilot-result.blade.php
                $marepRates = $request->input('rates', []);
                // Pastikan hanya 12 data terakhir yang digunakan
                if (count($marepRates) > 12) {
                $marepRates = array_slice($marepRates, -12);
                }
                // Jika data rates tidak valid, fallback ke perhitungan lama
                if (empty($marepRates) || count($marepRates) < 12) {
                $marepRates = [$marepRate, $marep1Rate, $marep2Rate];
                    for ($i = 0; $i < 12; $i++) {
                        $loopMonth = \Carbon\Carbon::parse($period)->subMonths($i)->month;
                        $loopYear = \Carbon\Carbon::parse($period)->subMonths($i)->year;
                        $marep12Month += $getMarepCount($aircraftType, $loopMonth, $loopYear, $ata); 
                    } 
                }
                $stddev = sqrt($marepRate12Month / count($marepRates));
                // Alert level = rata-rata + 2 * standar deviasi
                $marepAlertLevel = $marepRate12Month + 2 * $stddev;
            }

           // ~~~ MAREP ALERT STATUS ~~~
            $marepAlertStatus = '';
            // RED-3: 3 bulan berturut-turut melebihi alert level
            if ($marepRate > $marepAlertLevel && $marep1Rate > $marepAlertLevel && $marep2Rate > $marepAlertLevel) {
                $marepAlertStatus = 'RED-3';
            }
            // RED-2: 2 bulan terakhir berturut-turut melebihi alert level
            elseif ($marepRate > $marepAlertLevel && $marep1Rate > $marepAlertLevel) {
                $marepAlertStatus = 'RED-2';
            }
            // RED-1: hanya bulan terakhir melebihi alert level
            elseif ($marepRate > $marepAlertLevel) {
                $marepAlertStatus = 'RED-1';
            }
            
            // ~~~ MAREP TREND ~~~
            $marepTrend = '';
            if ($marepRate < $marep1Rate && $marep1Rate < $marep2Rate) {
                $marepTrend = 'DOWN';
            } elseif ($marepRate > $marep1Rate && $marep1Rate < $marep2Rate) {
                $marepTrend = '';
            } elseif ($marepRate > $marep1Rate && $marep1Rate > $marep2Rate) {
                $marepTrend = 'UP';
            }

            // ~~~~~ {{ Technical Delay }} ~~~~~
            // DELAY
            $delayCount = $getDelayCount($aircraftType, $month, $year, $ata);
            $delayCountBefore = $getDelayCount($aircraftType, \Carbon\Carbon::parse($period)->subMonth(1)->month, \Carbon\Carbon::parse($period)->subMonth(1)->year, $ata);
            $delayCountTwoMonthsAgo = $getDelayCount($aircraftType, \Carbon\Carbon::parse($period)->subMonths(2)->month, \Carbon\Carbon::parse($period)->subMonths(2)->year, $ata);
            $delay3Month = $delayCount + $delayCountBefore + $delayCountTwoMonthsAgo;
            $delay12Month = 0;
            for ($i = 0; $i < 12; $i++) {
                $loopMonth = \Carbon\Carbon::parse($period)->subMonths($i)->month;
                $loopYear = \Carbon\Carbon::parse($period)->subMonths($i)->year;
                $delay12Month += $getDelayCount($aircraftType, $loopMonth, $loopYear, $ata); 
            }

            // ~~~ TECHNICAL DELAY RATE ~~~
            $delayRate = $delayCount * 1000 / ($flyingHoursTotal ?: 1);
            $delay1Rate = $delayCountBefore * 1000 / ($flyingHoursBefore ?: 1);
            $delay2Rate = $delayCountTwoMonthsAgo * 1000 / ($flyingHours2Before ?: 1);
            $delayRate3Month = ($delayRate + $delay1Rate + $delay2Rate) / 3;
            $delayRate12Month = $delay12Month * 1000 / ($fh12Last ?: 1);

            // ~~~ TECHNICAL DELAY ALERT LEVEL ~~~
            $delayAlertLevel = TblAlertLevel::where('actype', $aircraftType)
                ->where('ata', $ata)
                ->where('type', 'ALD')
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

            if (is_null($delayAlertLevel)) {
                // Ambil array rate 12 bulan terakhir dari kolom Rate di pilot-result.blade.php
                $delayRates = $request->input('rates', []);
                // Pastikan hanya 12 data terakhir yang digunakan
                if (count($delayRates) > 12) {
                $delayRates = array_slice($delayRates, -12);
                }
                // Jika data rates tidak valid, fallback ke perhitungan lama
                if (empty($delayRates) || count($delayRates) < 12) {
                $delaypRates = [$delayRate, $delay1Rate, $delay2Rate];
                    for ($i = 0; $i < 12; $i++) {
                        $loopMonth = \Carbon\Carbon::parse($period)->subMonths($i)->month;
                        $loopYear = \Carbon\Carbon::parse($period)->subMonths($i)->year;
                        $delay12Month += $getDelayCount($aircraftType, $loopMonth, $loopYear, $ata); 
                    } 
                }
                $stddev = sqrt($delayRate12Month / count($delaypRates));
                // Alert level = rata-rata + 2 * standar deviasi
                $delayAlertLevel = $delayRate12Month + 2 * $stddev;
            }

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

            // Simpan hasil per ATA
            $reportPerAta[] = [
                'ata' => $ata,
                'ata_name' => $ata_name,
                'pirepCount' => $pirepCount,
                'pirepCountBefore' => $pirepCountBefore,
                'pirepCountTwoMonthsAgo' => $pirepCountTwoMonthsAgo,
                'pirep3Month' => $pirep3Month,
                'pirep12Month' => $pirep12Month,
                'pirepRate' => $pirepRate,
                'pirep1Rate' => $pirep1Rate,
                'pirep2Rate' => $pirep2Rate,
                'pirepRate3Month' => $pirepRate3Month,
                'pirepRate12Month' => $pirepRate12Month,
                'pirepAlertLevel' => $pirepAlertLevel,
                'pirepAlertStatus' => $pirepAlertStatus,
                'pirepTrend' => $pirepTrend,
                'marepCount' => $marepCount,
                'marepCountBefore' => $marepCountBefore,
                'marepCountTwoMonthsAgo' => $marepCountTwoMonthsAgo,
                'marep3Month' => $marep3Month,
                'marep12Month' => $marep12Month,
                'marepRate' => $marepRate,
                'marep1Rate' => $marep1Rate,
                'marep2Rate' => $marep2Rate,
                'marepRate3Month' => $marepRate3Month,
                'marepRate12Month' => $marepRate12Month,
                'marepAlertLevel' => $marepAlertLevel,
                'marepAlertStatus' => $marepAlertStatus,
                'marepTrend' => $marepTrend,
                'delayCount' => $delayCount,
                'delayCountBefore' => $delayCountBefore,
                'delayCountTwoMonthsAgo' => $delayCountTwoMonthsAgo,
                'delay3Month' => $delay3Month,
                'delay12Month' => $delay12Month,
                'delayRate' => $delayRate,
                'delay1Rate' => $delay1Rate,
                'delay2Rate' => $delay2Rate,
                'delayRate3Month' => $delayRate3Month,
                'delayRate12Month' => $delayRate12Month,
                'delayAlertLevel' => $delayAlertLevel,
                'delayAlertStatus' => $delayAlertStatus,
                'delayTrend' => $delayTrend
            ];
        }

        return view('report.pilot-result', [
            'reportPerAta' => $reportPerAta,
            'flyingHoursTotal' => $flyingHoursTotal, 'flyingHoursBefore' => $flyingHoursBefore,
            'flyingHours2Before' => $flyingHours2Before, 'fh3Last' => $fh3Last, 'fh12Last' => $fh12Last, 
            'aircraftType' => $aircraftType, 'tblAta' => $tblAta, 'month' => $month, 'period' => $period,
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

    // public function pilotPdf(Request $request)
    // {
    //     $request->validate([
    //         'period' => 'required',
    //         'aircraft_type' => 'required',
    //     ]);

    //     $aircraftType = $request->aircraft_type;
    //     $period = $request->period;
    //     $month = date('m', strtotime($period));
    //     $year = date('Y', strtotime($period));

    //     $getFlyingHours = function($aircraftType, $period) {
    //         return TblMonthlyfhfc::where('Actype', $aircraftType)
    //             ->where('MonthEval', $period)
    //             ->selectRaw('SUM(RevFHHours + (RevFHMin / 60) + NoRevFHHours + (NoRevFHMin / 60)) as total')
    //             ->first()->total ?? 0;
    //     };

    //     $flyingHoursTotal = $getFlyingHours($aircraftType, $period);
    //     $flyingHoursBefore = $getFlyingHours($aircraftType, Carbon::parse($period)->subMonth(1)->format('Y-m'));
    //     $flyingHours2Before = $getFlyingHours($aircraftType, Carbon::parse($period)->subMonth(2)->format('Y-m'));
    //     $fh3Last = $flyingHoursTotal + $flyingHoursBefore + $flyingHours2Before;
    //     $fh12Last = 0;
    //     for ($i = 0; $i <= 11; $i++) {
    //         $periodBefore = Carbon::parse($period)->subMonth($i)->format('Y-m');
    //         $fh12Last += $getFlyingHours($aircraftType, $periodBefore);
    //     }

    //     $excludedIds = [5, 11, 12, 58, 70];
    //     $tblAta = TblMasterAta::whereNotIn('ATA', $excludedIds)->get();

    //     $getPirepCount = function($aircraftType, $month, $year, $ata) {
    //         return TblPirepSwift::where('ACTYPE', $aircraftType)
    //             ->whereMonth('DATE', $month)
    //             ->whereYear('DATE', $year)
    //             ->where('PirepMarep', 'Pirep')
    //             ->where('ATA', $ata)
    //             ->count();
    //     };
    //     $getMarepCount = function($aircraftType, $month, $year, $ata) {
    //         return TblPirepSwift::where('ACTYPE', $aircraftType)
    //             ->whereMonth('DATE', $month)
    //             ->whereYear('DATE', $year)
    //             ->where('PirepMarep', 'Marep')
    //             ->where('ATA', $ata)
    //             ->count();
    //     };
    //     $getDelayCount = function($aircraftType, $month, $year, $ata) {
    //         return Mcdrnew::where('ACtype', $aircraftType)
    //             ->whereMonth('DateEvent', $month)
    //             ->whereYear('DateEvent', $year)
    //             ->where('DCP', '<>', 'X')
    //             ->where('ATAtdm', $ata)
    //             ->count();
    //     };

    //     $reportPerAta = [];

    //     foreach ($tblAta as $ataRow) {
    //         $ata = $ataRow->ATA;
    //         $ata_name = $ataRow->ATA_DESC ?? $ataRow->ATAName ?? '';

    //         // PIREP
    //         $pirepCount = $getPirepCount($aircraftType, $month, $year, $ata);
    //         $pirepCountBefore = $getPirepCount($aircraftType, Carbon::parse($period)->subMonth(1)->month, Carbon::parse($period)->subMonth(1)->year, $ata);
    //         $pirepCountTwoMonthsAgo = $getPirepCount($aircraftType, Carbon::parse($period)->subMonths(2)->month, Carbon::parse($period)->subMonths(2)->year, $ata);
    //         $pirep3Month = $pirepCount + $pirepCountBefore + $pirepCountTwoMonthsAgo;
    //         $pirep12Month = 0;
    //         for ($i = 0; $i < 12; $i++) {
    //             $loopMonth = Carbon::parse($period)->subMonths($i)->month;
    //             $loopYear = Carbon::parse($period)->subMonths($i)->year;
    //             $pirep12Month += $getPirepCount($aircraftType, $loopMonth, $loopYear, $ata);
    //         }
    //         $pirepRate = $pirepCount * 1000 / ($flyingHoursTotal ?: 1);
    //         $pirep1Rate = $pirepCountBefore * 1000 / ($flyingHoursBefore ?: 1);
    //         $pirep2Rate = $pirepCountTwoMonthsAgo * 1000 / ($flyingHours2Before ?: 1);
    //         $pirepRate3Month = ($pirepRate + $pirep1Rate + $pirep2Rate) / 3;
    //         $pirepRate12Month = $pirep12Month * 1000 / ($fh12Last ?: 1);

    //         $pirepAlertLevel = TblAlertLevel::where('actype', $aircraftType)
    //             ->where('ata', $ata)
    //             ->where('type', 'ALP')
    //             ->where(function($query) use ($period) {
    //                 $query->whereBetween('startmonth', [$period, $period])
    //                     ->orWhereBetween('endmonth', [$period, $period])
    //                     ->orWhere(function($query) use ($period) {
    //                         $query->where('startmonth', '<=', $period)
    //                             ->where('endmonth', '>=', $period);
    //                     });
    //             })
    //             ->pluck('alertlevel')
    //             ->first();

    //         if (is_null($pirepAlertLevel)) {
    //             $pirepAlertLevel = $pirepRate12Month + 2 * sqrt($pirepRate12Month / 12);
    //         }

    //         $alertCount = 0;
    //         if ($pirepRate > $pirepAlertLevel) $alertCount++;
    //         if ($pirep1Rate > $pirepAlertLevel) $alertCount++;
    //         if ($pirep2Rate > $pirepAlertLevel) $alertCount++;
    //         $pirepAlertStatus = $alertCount == 3 ? 'RED-3' : ($alertCount == 2 ? 'RED-2' : ($alertCount == 1 ? 'RED-1' : ''));
    //         $pirepTrend = '';
    //         if ($pirepRate < $pirep1Rate && $pirep1Rate < $pirep2Rate) $pirepTrend = 'DOWN';
    //         elseif ($pirepRate > $pirep1Rate && $pirep1Rate > $pirep2Rate) $pirepTrend = 'UP';

    //         // MAREP
    //         $marepCount = $getMarepCount($aircraftType, $month, $year, $ata);
    //         $marepCountBefore = $getMarepCount($aircraftType, Carbon::parse($period)->subMonth(1)->month, Carbon::parse($period)->subMonth(1)->year, $ata);
    //         $marepCountTwoMonthsAgo = $getMarepCount($aircraftType, Carbon::parse($period)->subMonths(2)->month, Carbon::parse($period)->subMonths(2)->year, $ata);
    //         $marep3Month = $marepCount + $marepCountBefore + $marepCountTwoMonthsAgo;
    //         $marep12Month = 0;
    //         for ($i = 0; $i < 12; $i++) {
    //             $loopMonth = Carbon::parse($period)->subMonths($i)->month;
    //             $loopYear = Carbon::parse($period)->subMonths($i)->year;
    //             $marep12Month += $getMarepCount($aircraftType, $loopMonth, $loopYear, $ata);
    //         }
    //         $marepRate = $marepCount * 1000 / ($flyingHoursTotal ?: 1);
    //         $marep1Rate = $marepCountBefore * 1000 / ($flyingHoursBefore ?: 1);
    //         $marep2Rate = $marepCountTwoMonthsAgo * 1000 / ($flyingHours2Before ?: 1);
    //         $marepRate3Month = ($marepRate + $marep1Rate + $marep2Rate) / 3;
    //         $marepRate12Month = $marep12Month * 1000 / ($fh12Last ?: 1);

    //         $marepAlertLevel = TblAlertLevel::where('actype', $aircraftType)
    //             ->where('ata', $ata)
    //             ->where('type', 'ALM')
    //             ->where(function($query) use ($period) {
    //                 $query->whereBetween('startmonth', [$period, $period])
    //                     ->orWhereBetween('endmonth', [$period, $period])
    //                     ->orWhere(function($query) use ($period) {
    //                         $query->where('startmonth', '<=', $period)
    //                             ->where('endmonth', '>=', $period);
    //                     });
    //             })
    //             ->pluck('alertlevel')
    //             ->first();

    //         if (is_null($marepAlertLevel)) {
    //             $marepAlertLevel = $marepRate12Month + 2 * sqrt($marepRate12Month / 12);
    //         }

    //         $marepAlertStatus = '';
    //         if ($marepRate > $marepAlertLevel && $marep1Rate > $marepAlertLevel && $marep2Rate > $marepAlertLevel) {
    //             $marepAlertStatus = 'RED-3';
    //         } elseif ($marepRate > $marepAlertLevel && $marep1Rate > $marepAlertLevel) {
    //             $marepAlertStatus = 'RED-2';
    //         } elseif ($marepRate > $marepAlertLevel) {
    //             $marepAlertStatus = 'RED-1';
    //         }
    //         $marepTrend = '';
    //         if ($marepRate < $marep1Rate && $marep1Rate < $marep2Rate) $marepTrend = 'DOWN';
    //         elseif ($marepRate > $marep1Rate && $marep1Rate > $marep2Rate) $marepTrend = 'UP';

    //         // DELAY
    //         $delayCount = $getDelayCount($aircraftType, $month, $year, $ata);
    //         $delayCountBefore = $getDelayCount($aircraftType, Carbon::parse($period)->subMonth(1)->month, Carbon::parse($period)->subMonth(1)->year, $ata);
    //         $delayCountTwoMonthsAgo = $getDelayCount($aircraftType, Carbon::parse($period)->subMonths(2)->month, Carbon::parse($period)->subMonths(2)->year, $ata);
    //         $delay3Month = $delayCount + $delayCountBefore + $delayCountTwoMonthsAgo;
    //         $delay12Month = 0;
    //         for ($i = 0; $i < 12; $i++) {
    //             $loopMonth = Carbon::parse($period)->subMonths($i)->month;
    //             $loopYear = Carbon::parse($period)->subMonths($i)->year;
    //             $delay12Month += $getDelayCount($aircraftType, $loopMonth, $loopYear, $ata);
    //         }
    //         $delayRate = $delayCount * 1000 / ($flyingHoursTotal ?: 1);
    //         $delay1Rate = $delayCountBefore * 1000 / ($flyingHoursBefore ?: 1);
    //         $delay2Rate = $delayCountTwoMonthsAgo * 1000 / ($flyingHours2Before ?: 1);
    //         $delayRate3Month = ($delayRate + $delay1Rate + $delay2Rate) / 3;
    //         $delayRate12Month = $delay12Month * 1000 / ($fh12Last ?: 1);

    //         $delayAlertLevel = TblAlertLevel::where('actype', $aircraftType)
    //             ->where('ata', $ata)
    //             ->where('type', 'ALD')
    //             ->where(function($query) use ($period) {
    //                 $query->whereBetween('startmonth', [$period, $period])
    //                     ->orWhereBetween('endmonth', [$period, $period])
    //                     ->orWhere(function($query) use ($period) {
    //                         $query->where('startmonth', '<=', $period)
    //                             ->where('endmonth', '>=', $period);
    //                     });
    //             })
    //             ->pluck('alertlevel')
    //             ->first();

    //         if (is_null($delayAlertLevel)) {
    //             $delayAlertLevel = $delayRate12Month + 2 * sqrt($delayRate12Month / 12);
    //         }

    //         $delayAlertStatus = '';
    //         if ($delayRate > $delayAlertLevel && $delay1Rate > $delayAlertLevel && $delay2Rate > $delayAlertLevel) {
    //             $delayAlertStatus = 'RED-3';
    //         } elseif ($delayRate > $delayAlertLevel && $delay1Rate > $delayAlertLevel) {
    //             $delayAlertStatus = 'RED-2';
    //         } elseif ($delayRate > $delayAlertLevel) {
    //             $delayAlertStatus = 'RED-1';
    //         }
    //         $delayTrend = '';
    //         if ($delayRate < $delay1Rate && $delay1Rate < $delay2Rate) $delayTrend = 'DOWN';
    //         elseif ($delayRate > $delay1Rate && $delay1Rate > $delay2Rate) $delayTrend = 'UP';

    //         $reportPerAta[] = [
    //             'ata' => $ata,
    //             'ata_name' => $ata_name,
    //             'pirepCount' => $pirepCount,
    //             'pirepCountBefore' => $pirepCountBefore,
    //             'pirepCountTwoMonthsAgo' => $pirepCountTwoMonthsAgo,
    //             'pirep3Month' => $pirep3Month,
    //             'pirep12Month' => $pirep12Month,
    //             'pirepRate' => $pirepRate,
    //             'pirep1Rate' => $pirep1Rate,
    //             'pirep2Rate' => $pirep2Rate,
    //             'pirepRate3Month' => $pirepRate3Month,
    //             'pirepRate12Month' => $pirepRate12Month,
    //             'pirepAlertLevel' => $pirepAlertLevel,
    //             'pirepAlertStatus' => $pirepAlertStatus,
    //             'pirepTrend' => $pirepTrend,
    //             'marepCount' => $marepCount,
    //             'marepCountBefore' => $marepCountBefore,
    //             'marepCountTwoMonthsAgo' => $marepCountTwoMonthsAgo,
    //             'marep3Month' => $marep3Month,
    //             'marep12Month' => $marep12Month,
    //             'marepRate' => $marepRate,
    //             'marep1Rate' => $marep1Rate,
    //             'marep2Rate' => $marep2Rate,
    //             'marepRate3Month' => $marepRate3Month,
    //             'marepRate12Month' => $marepRate12Month,
    //             'marepAlertLevel' => $marepAlertLevel,
    //             'marepAlertStatus' => $marepAlertStatus,
    //             'marepTrend' => $marepTrend,
    //             'delayCount' => $delayCount,
    //             'delayCountBefore' => $delayCountBefore,
    //             'delayCountTwoMonthsAgo' => $delayCountTwoMonthsAgo,
    //             'delay3Month' => $delay3Month,
    //             'delay12Month' => $delay12Month,
    //             'delayRate' => $delayRate,
    //             'delay1Rate' => $delay1Rate,
    //             'delay2Rate' => $delay2Rate,
    //             'delayRate3Month' => $delayRate3Month,
    //             'delayRate12Month' => $delayRate12Month,
    //             'delayAlertLevel' => $delayAlertLevel,
    //             'delayAlertStatus' => $delayAlertStatus,
    //             'delayTrend' => $delayTrend
    //         ];
    //     }

    //     $pdf = Pdf::loadView('pdf.pilot-pdf', [
    //         'reportPerAta' => $reportPerAta,
    //         'flyingHoursTotal' => $flyingHoursTotal,
    //         'flyingHoursBefore' => $flyingHoursBefore,
    //         'flyingHours2Before' => $flyingHours2Before,
    //         'fh3Last' => $fh3Last,
    //         'fh12Last' => $fh12Last,
    //         'aircraftType' => $aircraftType,
    //         'tblAta' => $tblAta,
    //         'month' => $month,
    //         'period' => $period,
    //     ]);

    //     return $pdf->download('Pilot_Report_' . $aircraftType . '_' . $period . '.pdf');
    // }
}