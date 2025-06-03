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

        // ===== OPTIMIZED: Single Query untuk semua data PIREP =====
        $pirepData = [];
        for ($i = 0; $i < 12; $i++) {
            $loopMonth = \Carbon\Carbon::parse($period)->subMonths($i)->month;
            $loopYear = \Carbon\Carbon::parse($period)->subMonths($i)->year;
            
            $pirepCounts = TblPirepSwift::where('ACTYPE', $aircraftType)
                ->whereMonth('DATE', $loopMonth)
                ->whereYear('DATE', $loopYear)
                ->where('PirepMarep', 'Pirep')
                ->whereIn('ATA', $tblAta->pluck('ATA'))
                ->select('ATA', DB::raw('COUNT(*) as count'))
                ->groupBy('ATA')
                ->get()
                ->keyBy('ATA');

            $pirepData[$i] = $pirepCounts;
        }

        // ===== OPTIMIZED: Single Query untuk semua data MAREP =====
        $marepData = [];
        for ($i = 0; $i < 12; $i++) {
            $loopMonth = \Carbon\Carbon::parse($period)->subMonths($i)->month;
            $loopYear = \Carbon\Carbon::parse($period)->subMonths($i)->year;
            
            $marepCounts = TblPirepSwift::where('ACTYPE', $aircraftType)
                ->whereMonth('DATE', $loopMonth)
                ->whereYear('DATE', $loopYear)
                ->where('PirepMarep', 'Marep')
                ->whereIn('ATA', $tblAta->pluck('ATA'))
                ->select('ATA', DB::raw('COUNT(*) as count'))
                ->groupBy('ATA')
                ->get()
                ->keyBy('ATA');

            $marepData[$i] = $marepCounts;
        }

        // ===== OPTIMIZED: Single Query untuk semua data DELAY =====
        $delayData = [];
        for ($i = 0; $i < 12; $i++) {
            $loopMonth = \Carbon\Carbon::parse($period)->subMonths($i)->month;
            $loopYear = \Carbon\Carbon::parse($period)->subMonths($i)->year;
            
            $delayCounts = Mcdrnew::where('ACtype', $aircraftType)
                ->whereMonth('DateEvent', $loopMonth)
                ->whereYear('DateEvent', $loopYear)
                ->where('DCP', '<>', 'X')
                ->whereIn('ATAtdm', $tblAta->pluck('ATA'))
                ->select('ATAtdm as ATA', DB::raw('COUNT(*) as count'))
                ->groupBy('ATAtdm')
                ->get()
                ->keyBy('ATA');

            $delayData[$i] = $delayCounts;
        }

        // ===== OPTIMIZED: Single Query untuk semua Alert Levels =====
        $alertLevels = TblAlertLevel::where('actype', $aircraftType)
            ->whereIn('ata', $tblAta->pluck('ATA'))
            ->whereIn('type', ['ALP', 'ALM', 'ALD'])
            ->where(function($query) use ($period) {
                $query->whereBetween('startmonth', [$period, $period])
                    ->orWhereBetween('endmonth', [$period, $period])
                    ->orWhere(function($query) use ($period) {
                        $query->where('startmonth', '<=', $period)
                            ->where('endmonth', '>=', $period);
                    });
            })
            ->get()
            ->groupBy(['ata', 'type']);

        $reportPerAta = [];

        foreach ($tblAta as $ataRow) {
            $ata = $ataRow->ATA;
            $ata_name = $ataRow->ATA_DESC ?? $ataRow->ATAName ?? '';

            // ===== PIREP CALCULATIONS =====
            $pirepCount = $pirepData[0][$ata]->count ?? 0;
            $pirepCountBefore = $pirepData[1][$ata]->count ?? 0;
            $pirepCountTwoMonthsAgo = $pirepData[2][$ata]->count ?? 0;
            $pirep3Month = $pirepCount + $pirepCountBefore + $pirepCountTwoMonthsAgo;
            
            $pirep12Month = 0;
            for ($i = 0; $i < 12; $i++) {
                $pirep12Month += $pirepData[$i][$ata]->count ?? 0;
            }

            $pirepRate = $pirepCount * 1000 / ($flyingHoursTotal ?: 1);
            $pirep1Rate = $pirepCountBefore * 1000 / ($flyingHoursBefore ?: 1);
            $pirep2Rate = $pirepCountTwoMonthsAgo * 1000 / ($flyingHours2Before ?: 1);
            $pirepRate3Month = ($pirepRate + $pirep1Rate + $pirep2Rate) / 3;
            $pirepRate12Month = $pirep12Month * 1000 / ($fh12Last ?: 1);
            
            // PIREP ALERT LEVEL
            $pirepAlertLevel = $alertLevels[$ata]['ALP'][0]->alertlevel ?? null;

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
                        $fh = $getFlyingHours($aircraftType, \Carbon\Carbon::parse($period)->subMonths($i)->format('Y-m'));
                        $count = $pirepData[$i][$ata]->count ?? 0;
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

            // ===== MAREP CALCULATIONS =====
            $marepCount = $marepData[0][$ata]->count ?? 0;
            $marepCountBefore = $marepData[1][$ata]->count ?? 0;
            $marepCountTwoMonthsAgo = $marepData[2][$ata]->count ?? 0;
            $marep3Month = $marepCount + $marepCountBefore + $marepCountTwoMonthsAgo;
            
            $marep12Month = 0;
            for ($i = 0; $i < 12; $i++) {
                $marep12Month += $marepData[$i][$ata]->count ?? 0;
            }

            // ~~~ MAREP RATE PERIOD ~~~
            $marepRate = $marepCount * 1000 / ($flyingHoursTotal ?: 1);
            $marep1Rate = $marepCountBefore * 1000 / ($flyingHoursBefore ?: 1);
            $marep2Rate = $marepCountTwoMonthsAgo * 1000 / ($flyingHours2Before ?: 1);
            $marepRate3Month = ($marepRate + $marep1Rate + $marep2Rate) / 3;
            $marepRate12Month = $marep12Month * 1000 / ($fh12Last ?: 1);
            
            // ~~~ MAREP ALERT LEVEL ~~~
            $marepAlertLevel = $alertLevels[$ata]['ALM'][0]->alertlevel ?? null;

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
                        $marep12Month += $marepData[$i][$ata]->count ?? 0;
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

            // ===== DELAY CALCULATIONS =====
            $delayCount = $delayData[0][$ata]->count ?? 0;
            $delayCountBefore = $delayData[1][$ata]->count ?? 0;
            $delayCountTwoMonthsAgo = $delayData[2][$ata]->count ?? 0;
            $delay3Month = $delayCount + $delayCountBefore + $delayCountTwoMonthsAgo;
            
            $delay12Month = 0;
            for ($i = 0; $i < 12; $i++) {
                $delay12Month += $delayData[$i][$ata]->count ?? 0;
            }

            // ~~~ TECHNICAL DELAY RATE ~~~
            $delayRate = $delayCount * 1000 / ($flyingHoursTotal ?: 1);
            $delay1Rate = $delayCountBefore * 1000 / ($flyingHoursBefore ?: 1);
            $delay2Rate = $delayCountTwoMonthsAgo * 1000 / ($flyingHours2Before ?: 1);
            $delayRate3Month = ($delayRate + $delay1Rate + $delay2Rate) / 3;
            $delayRate12Month = $delay12Month * 1000 / ($fh12Last ?: 1);

            // ~~~ TECHNICAL DELAY ALERT LEVEL ~~~
            $delayAlertLevel = $alertLevels[$ata]['ALD'][0]->alertlevel ?? null;

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
                        $delay12Month += $delayData[$i][$ata]->count ?? 0;
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

    public function pilotPdf(Request $request)
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
                ->first()->total ?? 0;
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

        // ===== OPTIMIZED: Single Query untuk semua data PIREP =====
        $pirepData = [];
        for ($i = 0; $i < 12; $i++) {
            $loopMonth = \Carbon\Carbon::parse($period)->subMonths($i)->month;
            $loopYear = \Carbon\Carbon::parse($period)->subMonths($i)->year;
            
            $pirepCounts = TblPirepSwift::where('ACTYPE', $aircraftType)
                ->whereMonth('DATE', $loopMonth)
                ->whereYear('DATE', $loopYear)
                ->where('PirepMarep', 'Pirep')
                ->whereIn('ATA', $tblAta->pluck('ATA'))
                ->select('ATA', DB::raw('COUNT(*) as count'))
                ->groupBy('ATA')
                ->get()
                ->keyBy('ATA');

            $pirepData[$i] = $pirepCounts;
        }

        // ===== OPTIMIZED: Single Query untuk semua data MAREP =====
        $marepData = [];
        for ($i = 0; $i < 12; $i++) {
            $loopMonth = \Carbon\Carbon::parse($period)->subMonths($i)->month;
            $loopYear = \Carbon\Carbon::parse($period)->subMonths($i)->year;
            
            $marepCounts = TblPirepSwift::where('ACTYPE', $aircraftType)
                ->whereMonth('DATE', $loopMonth)
                ->whereYear('DATE', $loopYear)
                ->where('PirepMarep', 'Marep')
                ->whereIn('ATA', $tblAta->pluck('ATA'))
                ->select('ATA', DB::raw('COUNT(*) as count'))
                ->groupBy('ATA')
                ->get()
                ->keyBy('ATA');

            $marepData[$i] = $marepCounts;
        }

        // ===== OPTIMIZED: Single Query untuk semua data DELAY =====
        $delayData = [];
        for ($i = 0; $i < 12; $i++) {
            $loopMonth = \Carbon\Carbon::parse($period)->subMonths($i)->month;
            $loopYear = \Carbon\Carbon::parse($period)->subMonths($i)->year;
            
            $delayCounts = Mcdrnew::where('ACtype', $aircraftType)
                ->whereMonth('DateEvent', $loopMonth)
                ->whereYear('DateEvent', $loopYear)
                ->where('DCP', '<>', 'X')
                ->whereIn('ATAtdm', $tblAta->pluck('ATA'))
                ->select('ATAtdm as ATA', DB::raw('COUNT(*) as count'))
                ->groupBy('ATAtdm')
                ->get()
                ->keyBy('ATA');

            $delayData[$i] = $delayCounts;
        }

        // ===== OPTIMIZED: Single Query untuk semua Alert Levels =====
        $alertLevels = TblAlertLevel::where('actype', $aircraftType)
            ->whereIn('ata', $tblAta->pluck('ATA'))
            ->whereIn('type', ['ALP', 'ALM', 'ALD'])
            ->where(function($query) use ($period) {
                $query->whereBetween('startmonth', [$period, $period])
                    ->orWhereBetween('endmonth', [$period, $period])
                    ->orWhere(function($query) use ($period) {
                        $query->where('startmonth', '<=', $period)
                            ->where('endmonth', '>=', $period);
                    });
            })
            ->get()
            ->groupBy(['ata', 'type']);

        $reportPerAta = [];

        foreach ($tblAta as $ataRow) {
            $ata = $ataRow->ATA;
            $ata_name = $ataRow->ATA_DESC ?? $ataRow->ATAName ?? '';

            // ===== PIREP CALCULATIONS =====
            $pirepCount = $pirepData[0][$ata]->count ?? 0;
            $pirepCountBefore = $pirepData[1][$ata]->count ?? 0;
            $pirepCountTwoMonthsAgo = $pirepData[2][$ata]->count ?? 0;
            $pirep3Month = $pirepCount + $pirepCountBefore + $pirepCountTwoMonthsAgo;
            
            $pirep12Month = 0;
            for ($i = 0; $i < 12; $i++) {
                $pirep12Month += $pirepData[$i][$ata]->count ?? 0;
            }

            $pirepRate = $pirepCount * 1000 / ($flyingHoursTotal ?: 1);
            $pirep1Rate = $pirepCountBefore * 1000 / ($flyingHoursBefore ?: 1);
            $pirep2Rate = $pirepCountTwoMonthsAgo * 1000 / ($flyingHours2Before ?: 1);
            $pirepRate3Month = ($pirepRate + $pirep1Rate + $pirep2Rate) / 3;
            $pirepRate12Month = $pirep12Month * 1000 / ($fh12Last ?: 1);
            
            // PIREP ALERT LEVEL
            $pirepAlertLevel = $alertLevels[$ata]['ALP'][0]->alertlevel ?? null;

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
                        $fh = $getFlyingHours($aircraftType, \Carbon\Carbon::parse($period)->subMonths($i)->format('Y-m'));
                        $count = $pirepData[$i][$ata]->count ?? 0;
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

            // ===== MAREP CALCULATIONS =====
            $marepCount = $marepData[0][$ata]->count ?? 0;
            $marepCountBefore = $marepData[1][$ata]->count ?? 0;
            $marepCountTwoMonthsAgo = $marepData[2][$ata]->count ?? 0;
            $marep3Month = $marepCount + $marepCountBefore + $marepCountTwoMonthsAgo;
            
            $marep12Month = 0;
            for ($i = 0; $i < 12; $i++) {
                $marep12Month += $marepData[$i][$ata]->count ?? 0;
            }

            // ~~~ MAREP RATE PERIOD ~~~
            $marepRate = $marepCount * 1000 / ($flyingHoursTotal ?: 1);
            $marep1Rate = $marepCountBefore * 1000 / ($flyingHoursBefore ?: 1);
            $marep2Rate = $marepCountTwoMonthsAgo * 1000 / ($flyingHours2Before ?: 1);
            $marepRate3Month = ($marepRate + $marep1Rate + $marep2Rate) / 3;
            $marepRate12Month = $marep12Month * 1000 / ($fh12Last ?: 1);
            
            // ~~~ MAREP ALERT LEVEL ~~~
            $marepAlertLevel = $alertLevels[$ata]['ALM'][0]->alertlevel ?? null;

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
                        $marep12Month += $marepData[$i][$ata]->count ?? 0;
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

            // ===== DELAY CALCULATIONS =====
            $delayCount = $delayData[0][$ata]->count ?? 0;
            $delayCountBefore = $delayData[1][$ata]->count ?? 0;
            $delayCountTwoMonthsAgo = $delayData[2][$ata]->count ?? 0;
            $delay3Month = $delayCount + $delayCountBefore + $delayCountTwoMonthsAgo;
            
            $delay12Month = 0;
            for ($i = 0; $i < 12; $i++) {
                $delay12Month += $delayData[$i][$ata]->count ?? 0;
            }

            // ~~~ TECHNICAL DELAY RATE ~~~
            $delayRate = $delayCount * 1000 / ($flyingHoursTotal ?: 1);
            $delay1Rate = $delayCountBefore * 1000 / ($flyingHoursBefore ?: 1);
            $delay2Rate = $delayCountTwoMonthsAgo * 1000 / ($flyingHours2Before ?: 1);
            $delayRate3Month = ($delayRate + $delay1Rate + $delay2Rate) / 3;
            $delayRate12Month = $delay12Month * 1000 / ($fh12Last ?: 1);

            // ~~~ TECHNICAL DELAY ALERT LEVEL ~~~
            $delayAlertLevel = $alertLevels[$ata]['ALD'][0]->alertlevel ?? null;

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
                        $delay12Month += $delayData[$i][$ata]->count ?? 0;
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

        // Data untuk PDF - ambil dari report pertama untuk nilai global
        $firstReport = $reportPerAta[0] ?? [];
        
        $data = [
            'reportPerAta' => $reportPerAta,
            'flyingHoursTotal' => $flyingHoursTotal,
            'flyingHoursBefore' => $flyingHoursBefore,
            'flyingHours2Before' => $flyingHours2Before,
            'fh3Last' => $fh3Last,
            'fh12Last' => $fh12Last,
            'aircraftType' => $aircraftType,
            'tblAta' => $tblAta,
            'month' => $month,
            'period' => $period,
            // Data untuk PIREP
            'pirepCount' => $firstReport['pirepCount'] ?? 0,
            'pirepCountBefore' => $firstReport['pirepCountBefore'] ?? 0,
            'pirepCountTwoMonthsAgo' => $firstReport['pirepCountTwoMonthsAgo'] ?? 0,
            'pirep3Month' => $firstReport['pirep3Month'] ?? 0,
            'pirep12Month' => $firstReport['pirep12Month'] ?? 0,
            'pirepRate' => $firstReport['pirepRate'] ?? 0,
            'pirep1Rate' => $firstReport['pirep1Rate'] ?? 0,
            'pirep2Rate' => $firstReport['pirep2Rate'] ?? 0,
            'pirepRate3Month' => $firstReport['pirepRate3Month'] ?? 0,
            'pirepRate12Month' => $firstReport['pirepRate12Month'] ?? 0,
            'pirepAlertLevel' => $firstReport['pirepAlertLevel'] ?? 0,
            'pirepAlertStatus' => $firstReport['pirepAlertStatus'] ?? '',
            'pirepTrend' => $firstReport['pirepTrend'] ?? '',
            // Data untuk MAREP
            'marepCount' => $firstReport['marepCount'] ?? 0,
            'marepCountBefore' => $firstReport['marepCountBefore'] ?? 0,
            'marepCountTwoMonthsAgo' => $firstReport['marepCountTwoMonthsAgo'] ?? 0,
            'marep3Month' => $firstReport['marep3Month'] ?? 0,
            'marep12Month' => $firstReport['marep12Month'] ?? 0,
            'marepRate' => $firstReport['marepRate'] ?? 0,
            'marep1Rate' => $firstReport['marep1Rate'] ?? 0,
            'marep2Rate' => $firstReport['marep2Rate'] ?? 0,
            'marepRate3Month' => $firstReport['marepRate3Month'] ?? 0,
            'marepRate12Month' => $firstReport['marepRate12Month'] ?? 0,
            'marepAlertLevel' => $firstReport['marepAlertLevel'] ?? 0,
            'marepAlertStatus' => $firstReport['marepAlertStatus'] ?? '',
            'marepTrend' => $firstReport['marepTrend'] ?? '',
            // Data untuk DELAY
            'delayCount' => $firstReport['delayCount'] ?? 0,
            'delayCountBefore' => $firstReport['delayCountBefore'] ?? 0,
            'delayCountTwoMonthsAgo' => $firstReport['delayCountTwoMonthsAgo'] ?? 0,
            'delay3Month' => $firstReport['delay3Month'] ?? 0,
            'delay12Month' => $firstReport['delay12Month'] ?? 0,
            'delayRate' => $firstReport['delayRate'] ?? 0,
            'delay1Rate' => $firstReport['delay1Rate'] ?? 0,
            'delay2Rate' => $firstReport['delay2Rate'] ?? 0,
            'delayRate3Month' => $firstReport['delayRate3Month'] ?? 0,
            'delayRate12Month' => $firstReport['delayRate12Month'] ?? 0,
            'delayAlertLevel' => $firstReport['delayAlertLevel'] ?? 0,
            'delayAlertStatus' => $firstReport['delayAlertStatus'] ?? '',
            'delayTrend' => $firstReport['delayTrend'] ?? ''
        ];

        // Generate PDF
        $pdf = Pdf::loadView('pdf.pilot-pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        
        // Download PDF dengan nama file yang sesuai
        $periodOnlyDate = date('Y-m', strtotime($period));
        $filename = "Pilot_Report_{$aircraftType}_{$periodOnlyDate}.pdf";
        
        return $pdf->download($filename);
    }
}