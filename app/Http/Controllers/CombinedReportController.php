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

class CombinedReportController extends Controller
{
    public function index()
    {
        // Ambil Aircraft Types dari TblPirepSwift (untuk Pilot Report)
        $aircraftTypesFromPirep = TblPirepSwift::select('ACTYPE as ACType')->distinct()
            ->whereNotNull('ACTYPE')
            ->where('ACTYPE', '!=', '')
            ->where('ACTYPE', '!=', 'default')
            ->orderBy('ACTYPE')
            ->get();

        // Ambil Aircraft Types dari TblMasterac (untuk AOS Report)
        $aircraftTypesFromMaster = TblMasterac::select('ACType')->distinct()
            ->whereNotNull('ACType')
            ->where('ACType', '!=', '')
            ->orderBy('ACType')
            ->get();

        // Ambil Operators dari TblMasterac
        $operators = TblMasterac::select('Operator')->distinct()
            ->whereNotNull('Operator')
            ->where('Operator', '!=', '')
            ->get();

        // Ambil dan format data periode dari TblMonthlyfhfc
        $periods = TblMonthlyfhfc::select('MonthEval')->distinct()
            ->orderByDesc('MonthEval')
            ->get()
            ->map(function($item) {
                return [
                    'formatted' => Carbon::parse($item->MonthEval)->format('Y-m'),
                    'original' => $item->MonthEval
                ];
            });

        return view('report.combined-content', compact(
            'aircraftTypesFromPirep', 
            'aircraftTypesFromMaster', 
            'operators', 
            'periods'
        ));
    }

    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'period' => 'required',
            'aircraft_type_aos' => 'required',
            'aircraft_type_pilot' => 'required',
        ]);

        $period = $request->period;
        $operator = $request->operator;
        $aircraftTypeAos = $request->aircraft_type_aos;
        $aircraftTypePilot = $request->aircraft_type_pilot;

        // ===== GET AOS DATA =====
        $aosData = $this->getAosData($aircraftTypeAos, $period);

        // ===== GET PILOT DATA =====
        $pilotData = $this->getPilotData($aircraftTypePilot, $period, $request);

        return view('report.combined-result', [
            'aosData' => $aosData,
            'pilotData' => $pilotData,
            'aircraftTypeAos' => $aircraftTypeAos,
            'aircraftTypePilot' => $aircraftTypePilot,
            'period' => $period,
            'operator' => $operator
        ]);
    }

    public function exportPdf(Request $request)
    {
        $request->validate([
            'period' => 'required',
            'aircraft_type_aos' => 'required',
            'aircraft_type_pilot' => 'required',
        ]);

        $period = $request->period;
        $operator = $request->operator;
        $aircraftTypeAos = $request->aircraft_type_aos;     
        $aircraftTypePilot = $request->aircraft_type_pilot;  

        // ===== GET AOS DATA =====
        $aosData = $this->getAosData($aircraftTypeAos, $period);

        // ===== GET PILOT DATA =====
        $pilotData = $this->getPilotData($aircraftTypePilot, $period, $request);

        // Helper function untuk format number
        $formatNumber = function($value, $decimals = 2) {
            return rtrim(rtrim(number_format($value, $decimals), '0'), '.');
        };

        $data = [
            'aosData' => $aosData,
            'pilotData' => $pilotData,
            'aircraftTypeAos' => $aircraftTypeAos,        
            'aircraftTypePilot' => $aircraftTypePilot,    
            'period' => $period,
            'operator' => $operator,
            'formatNumber' => $formatNumber
        ];

        // Generate PDF
        $pdf = Pdf::loadView('pdf.combined-pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'sans-serif'
        ]);
        
        $periodFormatted = \Carbon\Carbon::parse($period)->format('Y-m');
        $filename = "Fleet Reliability Report_{$aircraftTypeAos}_{$periodFormatted}.pdf";
        
        return $pdf->download($filename);
    }

    private function getAosData($aircraftType, $period)
    {
        // Helper function untuk menghilangkan trailing zero
        $formatRate = function($value) {
            return (float) number_format($value, 10, '.', '');
        };

        // Initialize an array to hold report data for each month
        $reportData = [];
        $totalFlightHoursPerTakeOffTotal = 0;
        $totalRevenueFlightHoursPerTakeOff = 0;
        $totalDailyUtilizationFlyingHoursTotal = 0;
        $totalRevenueDailyUtilizationFlyingHoursTotal = 0;
        $totalTotalDuration = 0;
        $totalAverageDuration = 0;

        // Loop through the last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $currentPeriod = Carbon::parse($period)->subMonth($i)->format('Y-m');
            $month = date('m', strtotime($currentPeriod));
            $year = date('Y', strtotime($currentPeriod));

            // 1. A/C In Fleet
            $acInFleet = TblMonthlyfhfc::where('Actype', $aircraftType)
                ->whereMonth('MonthEval', $month)
                ->whereYear('MonthEval', $year)
                ->count('Reg');

            // 2. A/C Days In Service
            $daysInService = TblMonthlyfhfc::where('Actype', $aircraftType)
                ->whereMonth('MonthEval', $month)
                ->whereYear('MonthEval', $year)
                ->sum('AvaiDays');

            // Calculate the number of days in the month
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

            // A/C in Service (DENGAN FORMAT RATE)
            $acInService = $daysInMonth > 0 ? $formatRate($daysInService / $daysInMonth) : 0;

            // 3. Flying Hours - Total
            $flyingHoursTotal = TblMonthlyfhfc::where('Actype', $aircraftType)
                ->whereMonth('MonthEval', $month)
                ->whereYear('MonthEval', $year)
                ->selectRaw('SUM(RevFHHours + (RevFHMin / 60) + NoRevFHHours + (NoRevFHMin / 60)) as total')
                ->first()->total;

            // 4. Revenue Flying Hours
            $revenueFlyingHours = TblMonthlyfhfc::where('Actype', $aircraftType)
                ->whereMonth('MonthEval', $month)
                ->whereYear('MonthEval', $year)
                ->selectRaw('SUM(RevFHHours + (RevFHMin / 60)) as revenue')
                ->first()->revenue;

            // 5. Take Off - Total
            $takeOffTotal = TblMonthlyfhfc::where('Actype', $aircraftType)
                ->whereMonth('MonthEval', $month)
                ->whereYear('MonthEval', $year)
                ->selectRaw('SUM(RevFC + NoRevFC) as total')
                ->first()->total;

            // 6. Revenue Take Off
            $revenueTakeOff = TblMonthlyfhfc::where('Actype', $aircraftType)
                ->whereMonth('MonthEval', $month)
                ->whereYear('MonthEval', $year)
                ->sum('RevFC');

            // 7. Flight Hours per Take Off - Total
            $flightHoursPerTakeOffTotal = $takeOffTotal > 0 ? $flyingHoursTotal / $takeOffTotal : 0;

            // 8. Revenue Flight Hours per Take Off
            $revenueFlightHoursPerTakeOff = $revenueTakeOff > 0 ? $revenueFlyingHours / $revenueTakeOff : 0;

            // 9. Daily Utilization - Flying Hours Total
            $dailyUtilizationFlyingHoursTotal = $daysInService > 0 ? $flyingHoursTotal / $daysInService : 0;

            // 10. Revenue Daily Utilization - Flying Hours Total
            $revenueDailyUtilizationFlyingHoursTotal = $daysInService > 0 ? $revenueFlyingHours / $daysInService : 0;

            // 11. Daily Utilization - Take Off Total
            $dailyUtilizationTakeOffTotal = $daysInService > 0 ? $takeOffTotal / $daysInService : 0;

            // 12. Revenue Daily Utilization - Take Off Total
            $revenueDailyUtilizationTakeOffTotal = $daysInService > 0 ? $revenueTakeOff / $daysInService : 0;

            // 13. Technical Delay - Total
            $technicalDelayTotal = Mcdrnew::where('ACType', $aircraftType)
                ->whereMonth('DateEvent', '=', $month)
                ->whereYear('DateEvent', '=', $year)
                ->where('DCP', 'LIKE', '%D%')
                ->count();

            // 14. Total Duration
            $totalDuration = Mcdrnew::where('ACType', $aircraftType)
                ->whereMonth('DateEvent', '=', $month)
                ->whereYear('DateEvent', '=', $year)
                ->where('DCP', 'LIKE', '%D%')
                ->selectRaw('SUM(HoursTek + (MinTek / 60)) as total_duration')
                ->first()->total_duration;

            // 15. Average Duration
            $averageDuration = $technicalDelayTotal > 0 ? $totalDuration / $technicalDelayTotal : 0;

            // 16. Rate / 100 Take Off (DENGAN FORMAT RATE)
            $ratePer100TakeOff = $revenueTakeOff > 0 ? $formatRate(($technicalDelayTotal * 100) / $revenueTakeOff) : 0;

            // Technical Incident - Total
            $technicalIncidentTotal = TblSdr::where('ACType', $aircraftType)
                ->whereMonth('DateOccur', '=', $month)
                ->whereYear('DateOccur', '=', $year)
                ->count();

            // Technical Incident Rate /100 FC (DENGAN FORMAT RATE)
            $technicalIncidentRate = $revenueTakeOff > 0 ? $formatRate(($technicalIncidentTotal * 100) / $revenueTakeOff) : 0;

            // 17. Technical Cancellation - Total
            $technicalCancellationTotal = Mcdrnew::where('ACType', $aircraftType)
                ->whereMonth('DateEvent', '=', $month)
                ->whereYear('DateEvent', '=', $year)
                ->where('DCP', 'LIKE', '%C%')
                ->count();

            // 18. Dispatch Reliability (%) (DENGAN FORMAT RATE)
            $dispatchReliability = $revenueTakeOff > 0 ? 
                $formatRate((($revenueTakeOff - $technicalDelayTotal - $technicalCancellationTotal) / $revenueTakeOff) * 100) : 0;

            // Store the metrics for the current month in the report data array
            $reportData[$currentPeriod] = [
                'acInFleet' => $acInFleet,
                'acInService' => $acInService,
                'daysInService' => $daysInService,
                'flyingHoursTotal' => $flyingHoursTotal,
                'revenueFlyingHours' => $revenueFlyingHours,
                'takeOffTotal' => $takeOffTotal,
                'revenueTakeOff' => $revenueTakeOff,
                // KONVERSI KE FORMAT HH:MM
                'flightHoursPerTakeOffTotal' => $this->convertDecimalToHoursMinutes($flightHoursPerTakeOffTotal),
                'revenueFlightHoursPerTakeOff' => $this->convertDecimalToHoursMinutes($revenueFlightHoursPerTakeOff),
                'dailyUtilizationFlyingHoursTotal' => $this->convertDecimalToHoursMinutes($dailyUtilizationFlyingHoursTotal),
                'revenueDailyUtilizationFlyingHoursTotal' => $this->convertDecimalToHoursMinutes($revenueDailyUtilizationFlyingHoursTotal),
                'dailyUtilizationTakeOffTotal' => $formatRate($dailyUtilizationTakeOffTotal),
                'revenueDailyUtilizationTakeOffTotal' => $formatRate($revenueDailyUtilizationTakeOffTotal),
                'technicalDelayTotal' => $technicalDelayTotal,
                // UNTUK DURATION, SIMPAN YANG SUDAH DIKONVERSI KARENA INI DISPLAY SAJA
                'totalDuration' => $this->convertDecimalToHoursMinutes($totalDuration),
                'averageDuration' => $this->convertDecimalToHoursMinutes($averageDuration),
                'ratePer100TakeOff' => $ratePer100TakeOff,
                'technicalIncidentTotal' => $technicalIncidentTotal,
                'technicalIncidentRate' => $technicalIncidentRate,
                'technicalCancellationTotal' => $technicalCancellationTotal,
                'dispatchReliability' => $dispatchReliability,
            ];

            // Mengonversi ke format desimal untuk penjumlahan
            $totalFlightHoursPerTakeOffTotal += $flightHoursPerTakeOffTotal;
            $totalRevenueFlightHoursPerTakeOff += $revenueFlightHoursPerTakeOff;
            $totalDailyUtilizationFlyingHoursTotal += $dailyUtilizationFlyingHoursTotal;
            $totalRevenueDailyUtilizationFlyingHoursTotal += $revenueDailyUtilizationFlyingHoursTotal;
            $totalTotalDuration += $totalDuration;
            $totalAverageDuration += $averageDuration;
        }

        // Menghitung rata-rata 12 bulan dan konversi ke format (HH:MM)
        $averageFlightHoursPerTakeOffTotal = $totalFlightHoursPerTakeOffTotal / 12;
        $avgFlightHoursPerTakeOffTotal = $this->convertDecimalToHoursMinutes($averageFlightHoursPerTakeOffTotal);

        $averageRevenueFlightHoursPerTakeOff = $totalRevenueFlightHoursPerTakeOff / 12;
        $avgRevenueFlightHoursPerTakeOff = $this->convertDecimalToHoursMinutes($averageRevenueFlightHoursPerTakeOff);

        $averageDailyUtilizationFlyingHoursTotal = $totalDailyUtilizationFlyingHoursTotal / 12;
        $avgDailyUtilizationFlyingHoursTotal = $this->convertDecimalToHoursMinutes($averageDailyUtilizationFlyingHoursTotal);

        $averageRevenueDailyUtilizationFlyingHoursTotal = $totalRevenueDailyUtilizationFlyingHoursTotal / 12;
        $avgRevenueDailyUtilizationFlyingHoursTotal = $this->convertDecimalToHoursMinutes($averageRevenueDailyUtilizationFlyingHoursTotal);

        $averageTotalDuration = $totalTotalDuration / 12;
        $avgTotalDuration = $this->convertDecimalToHoursMinutes($averageTotalDuration);

        $averageAverageDuration = $totalAverageDuration / 12;
        $avgAverageDuration = $this->convertDecimalToHoursMinutes($averageAverageDuration);

        return [
            'reportData' => $reportData,
            'avgFlightHoursPerTakeOffTotal' => $avgFlightHoursPerTakeOffTotal,
            'avgRevenueFlightHoursPerTakeOff' => $avgRevenueFlightHoursPerTakeOff,
            'avgDailyUtilizationFlyingHoursTotal' => $avgDailyUtilizationFlyingHoursTotal,
            'avgRevenueDailyUtilizationFlyingHoursTotal' => $avgRevenueDailyUtilizationFlyingHoursTotal,
            'avgTotalDuration' => $avgTotalDuration,
            'avgAverageDuration' => $avgAverageDuration
        ];
    }

    private function getPilotData($aircraftType, $period, $request = null)
    {
        // Helper function untuk menghilangkan trailing zero
        $formatRate = function($value) {
            return (float) number_format($value, 10, '.', '');
        };

        $month = date('m', strtotime($period));
        $year = date('Y', strtotime($period));

        // Fungsi untuk menghitung total flying hours
        $getFlyingHours = function($aircraftType, $period) {
            return TblMonthlyfhfc::where('Actype', $aircraftType)
                ->where('MonthEval', $period)
                ->selectRaw('SUM(RevFHHours + (RevFHMin / 60) + NoRevFHHours + (NoRevFHMin / 60)) as total')
                ->first()->total ?? 0;
        };

        $getFlyingCycles = function($aircraftType, $period) {
            return TblMonthlyfhfc::where('Actype', $aircraftType)
                ->where('MonthEval', $period)
                ->selectRaw('SUM(COALESCE(RevFC, 0) + COALESCE(NoRevFC, 0)) as total')
                ->first()->total ?? 0;
        };

        // Hitung flying hours untuk periode sekarang dan sebelumnya
        $flyingHoursTotal = $getFlyingHours($aircraftType, $period);
        $flyingHoursBefore = $getFlyingHours($aircraftType, Carbon::parse($period)->subMonth(1)->format('Y-m-01'));
        $flyingHours2Before = $getFlyingHours($aircraftType, Carbon::parse($period)->subMonth(2)->format('Y-m-01'));

        $flyingCyclesTotal = $getFlyingCycles($aircraftType, $period);
        $flyingCyclesBefore = $getFlyingCycles($aircraftType, Carbon::parse($period)->subMonth(1)->format('Y-m-01'));
        $flyingCycles2Before = $getFlyingCycles($aircraftType, Carbon::parse($period)->subMonth(2)->format('Y-m-01'));
        
        //Hitung Totals
        $fh3Last = $flyingHoursTotal + $flyingHoursBefore + $flyingHours2Before;
        $fc3Last = $flyingCyclesTotal + $flyingCyclesBefore + $flyingCycles2Before;

        $fh12Last = 0;
        $fc12Last = 0;
        for ($i = 0; $i <= 11; $i++) {
            $periodBefore = Carbon::parse($period)->subMonth($i)->format('Y-m-01');
            $fh12Last += $getFlyingHours($aircraftType, $periodBefore);
            $fc12Last += $getFlyingCycles($aircraftType, $periodBefore);
        }

        $excludedIds = [5, 11, 12, 58, 70];
        $tblAta = TblMasterAta::whereNotIn('ATA', $excludedIds)->get();

        // ===== OPTIMIZED: Single Query untuk semua data PIREP =====
        $pirepData = [];
        for ($i = 0; $i < 12; $i++) {
            $loopMonth = Carbon::parse($period)->subMonths($i)->month;
            $loopYear = Carbon::parse($period)->subMonths($i)->year;
            
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
            $loopMonth = Carbon::parse($period)->subMonths($i)->month;
            $loopYear = Carbon::parse($period)->subMonths($i)->year;
            
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
            $loopMonth = Carbon::parse($period)->subMonths($i)->month;
            $loopYear = Carbon::parse($period)->subMonths($i)->year;
            
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

            $pirepRate = $formatRate($pirepCount * 1000 / ($flyingHoursTotal ?: 1));
            $pirep1Rate = $formatRate($pirepCountBefore * 1000 / ($flyingHoursBefore ?: 1));
            $pirep2Rate = $formatRate($pirepCountTwoMonthsAgo * 1000 / ($flyingHours2Before ?: 1));
            $pirepRate3Month = $formatRate(($pirepRate + $pirep1Rate + $pirep2Rate) / 3);
            $pirepRate12Month = $formatRate($pirep12Month * 1000 / ($fh12Last ?: 1));
            
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
                $pirepAlertLevel = $formatRate($pirepRate12Month + 2 * $stddev);
            } else {
                $pirepAlertLevel = $formatRate($pirepAlertLevel);
            }

            // ~~~ PIREP ALERT STATUS ~~~
            $pirepAlertStatus = '';
            if ($pirepRate > $pirepAlertLevel && $pirep1Rate > $pirepAlertLevel && $pirep2Rate > $pirepAlertLevel){
                $pirepAlertStatus = 'RED-3';
            } elseif ($pirepRate > $pirepAlertLevel && $pirep1Rate > $pirepAlertLevel){
                $pirepAlertStatus = 'RED-2';
            } elseif ($pirepRate > $pirepAlertLevel){
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
            $marepRate = $formatRate($marepCount * 1000 / ($flyingHoursTotal ?: 1));
            $marep1Rate = $formatRate($marepCountBefore * 1000 / ($flyingHoursBefore ?: 1));
            $marep2Rate = $formatRate($marepCountTwoMonthsAgo * 1000 / ($flyingHours2Before ?: 1));
            $marepRate3Month = $formatRate(($marepRate + $marep1Rate + $marep2Rate) / 3);
            $marepRate12Month = $formatRate($marep12Month * 1000 / ($fh12Last ?: 1));
            
            // ~~~ MAREP ALERT LEVEL ~~~
            $marepAlertLevel = $alertLevels[$ata]['ALM'][0]->alertlevel ?? null;

            if (is_null($marepAlertLevel)) {
                $marepRates = $request->input('rates', []);
                if (count($marepRates) > 12) {
                    $marepRates = array_slice($marepRates, -12);
                }
                if (empty($marepRates) || count($marepRates) < 12) {
                    $marepRates = [$marepRate, $marep1Rate, $marep2Rate];
                }
                $mean = array_sum($marepRates) / count($marepRates);
                $variance = array_sum(array_map(function($rate) use ($mean) {
                    return pow($rate - $mean, 2);
                }, $marepRates)) / count($marepRates);
                $stddev = sqrt($variance);
                $marepAlertLevel = $formatRate($mean + 2 * $stddev);
            } else {
                $marepAlertLevel = $formatRate($marepAlertLevel);
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
            $delayRate = $formatRate($delayCount * 1000 / ($flyingCyclesTotal ?: 1));
            $delay1Rate = $formatRate($delayCountBefore * 1000 / ($flyingCyclesBefore ?: 1));
            $delay2Rate = $formatRate($delayCountTwoMonthsAgo * 1000 / ($flyingCycles2Before ?: 1));
            $delayRate3Month = $formatRate(($delayRate + $delay1Rate + $delay2Rate) / 3);
            $delayRate12Month = $formatRate($delay12Month * 1000 / ($fc12Last ?: 1));

            // ~~~ TECHNICAL DELAY ALERT LEVEL ~~~
            $delayAlertLevel = $alertLevels[$ata]['ALD'][0]->alertlevel ?? null;

            if (is_null($delayAlertLevel)) {
                $delayRates = $request->input('rates', []);
                if (count($delayRates) > 12) {
                    $delayRates = array_slice($delayRates, -12);
                }
                if (empty($delayRates) || count($delayRates) < 12) {
                    $delayRates = [$delayRate, $delay1Rate, $delay2Rate];
                    for ($i = 3; $i < 12; $i++) {
                        $fc = $getFlyingCycles($aircraftType, \Carbon\Carbon::parse($period)->subMonths($i)->format('Y-m-d'));
                        $count = $delayData[$i][$ata]->count ?? 0;
                        $rate = $count * 1000 / ($fc ?: 1);
                        $delayRates[] = $rate;
                    }
                }
                $mean = array_sum($delayRates) / count($delayRates);
                $variance = array_sum(array_map(function($rate) use ($mean) {
                    return pow($rate - $mean, 2);
                }, $delayRates)) / count($delayRates);
                $stddev = sqrt($variance);
                $delayAlertLevel = $formatRate($mean + 2 * $stddev);
            } else {
                $delayAlertLevel = $formatRate($delayAlertLevel);
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
            if ($delayRate < $delay1Rate && $delay1Rate < $delay2Rate) {
                $delayTrend = 'DOWN';
            } elseif ($delayRate > $delay1Rate && $delay1Rate < $delay2Rate) {
                $delayTrend = '';
            } elseif ($delayRate > $delay1Rate && $delay1Rate > $delay2Rate) {
                $delayTrend = 'UP';
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

        return [
            'reportPerAta' => $reportPerAta,
            'flyingHoursTotal' => $flyingHoursTotal,
            'flyingHoursBefore' => $flyingHoursBefore,
            'flyingHours2Before' => $flyingHours2Before,
            'fh3Last' => $fh3Last,
            'fh12Last' => $fh12Last,
            'flyingCyclesTotal' => $flyingCyclesTotal,
            'flyingCyclesBefore' => $flyingCyclesBefore,
            'flyingCycles2Before' => $flyingCycles2Before,
            'fc3Last' => $fc3Last,
            'fc12Last' => $fc12Last,
            'tblAta' => $tblAta,
            'month' => $month
        ];
    }

    // Untuk convert format menjadi (HH : MM)
    private function convertDecimalToHoursMinutes($decimalHours) {
        $hours = floor($decimalHours);
        $minutes = round(($decimalHours - $hours) * 60);
        return sprintf('%d : %02d', $hours, $minutes);
    }
}