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

    // ✅ REFACTORED: Single Query Approach untuk AOS Data
    private function getAosData($aircraftType, $period)
    {
        $endDate = Carbon::parse($period)->endOfMonth();
        $startDate = Carbon::parse($period)->subMonths(11)->startOfMonth();
        
        // ✅ SINGLE QUERY untuk semua data TblMonthlyfhfc (12 months)
        $monthlyData = TblMonthlyfhfc::where('Actype', $aircraftType)
            ->whereBetween('MonthEval', [$startDate, $endDate])
            ->selectRaw('
                YEAR(MonthEval) as year,
                MONTH(MonthEval) as month,
                COUNT(Reg) as ac_in_fleet,
                SUM(AvaiDays) as days_in_service,
                SUM(RevFHHours + (RevFHMin / 60) + NoRevFHHours + (NoRevFHMin / 60)) as flying_hours_total,
                SUM(RevFHHours + (RevFHMin / 60)) as revenue_flying_hours,
                SUM(RevFC + NoRevFC) as take_off_total,
                SUM(RevFC) as revenue_take_off
            ')
            ->groupBy('year', 'month')
            ->get()
            ->keyBy(function($item) {
                return $item->year . '-' . sprintf('%02d', $item->month);
            });

        // ✅ SINGLE QUERY untuk semua Technical Delay data (12 months)
        $technicalDelayData = Mcdrnew::where('ACType', $aircraftType)
            ->whereBetween('DateEvent', [$startDate, $endDate])
            ->where('DCP', 'LIKE', '%D%')
            ->selectRaw('
                YEAR(DateEvent) as year,
                MONTH(DateEvent) as month,
                COUNT(*) as technical_delay_total,
                SUM(HoursTek + (MinTek / 60)) as total_duration
            ')
            ->groupBy('year', 'month')
            ->get()
            ->keyBy(function($item) {
                return $item->year . '-' . sprintf('%02d', $item->month);
            });

        // ✅ SINGLE QUERY untuk semua Technical Incident data (12 months)
        $technicalIncidentData = TblSdr::where('ACType', $aircraftType)
            ->whereBetween('DateOccur', [$startDate, $endDate])
            ->selectRaw('
                YEAR(DateOccur) as year,
                MONTH(DateOccur) as month,
                COUNT(*) as technical_incident_total
            ')
            ->groupBy('year', 'month')
            ->get()
            ->keyBy(function($item) {
                return $item->year . '-' . sprintf('%02d', $item->month);
            });

        // ✅ SINGLE QUERY untuk semua Technical Cancellation data (12 months)
        $technicalCancellationData = Mcdrnew::where('ACType', $aircraftType)
            ->whereBetween('DateEvent', [$startDate, $endDate])
            ->where('DCP', 'LIKE', '%C%')
            ->selectRaw('
                YEAR(DateEvent) as year,
                MONTH(DateEvent) as month,
                COUNT(*) as technical_cancellation_total
            ')
            ->groupBy('year', 'month')
            ->get()
            ->keyBy(function($item) {
                return $item->year . '-' . sprintf('%02d', $item->month);
            });

        // Helper function untuk format rate
        $formatRate = function($value) {
            return (float) number_format($value, 10, '.', '');
        };

        // Process data menggunakan hasil single queries
        $reportData = [];
        $totalFlightHoursPerTakeOffTotal = 0;
        $totalRevenueFlightHoursPerTakeOff = 0;
        $totalDailyUtilizationFlyingHoursTotal = 0;
        $totalRevenueDailyUtilizationFlyingHoursTotal = 0;
        $totalTotalDuration = 0;
        $totalAverageDuration = 0;

        // Process data untuk setiap bulan (NO MORE DATABASE CALLS!)
        for ($i = 11; $i >= 0; $i--) {
            $currentPeriod = Carbon::parse($period)->subMonth($i)->format('Y-m');
            $month = (int)substr($currentPeriod, 5, 2);
            $year = (int)substr($currentPeriod, 0, 4);
            
            // Ambil data dari hasil single query
            $monthly = $monthlyData[$currentPeriod] ?? null;
            $techDelay = $technicalDelayData[$currentPeriod] ?? null;
            $techIncident = $technicalIncidentData[$currentPeriod] ?? null;
            $techCancellation = $technicalCancellationData[$currentPeriod] ?? null;

            // Set default values jika tidak ada data
            if (!$monthly) {
                $reportData[$currentPeriod] = [
                    'acInFleet' => 0,
                    'acInService' => 0,
                    'daysInService' => 0,
                    'flyingHoursTotal' => 0,
                    'revenueFlyingHours' => 0,
                    'takeOffTotal' => 0,
                    'revenueTakeOff' => 0,
                    'flightHoursPerTakeOffTotal' => '0 : 00',
                    'revenueFlightHoursPerTakeOff' => '0 : 00',
                    'dailyUtilizationFlyingHoursTotal' => '0 : 00',
                    'revenueDailyUtilizationFlyingHoursTotal' => '0 : 00',
                    'dailyUtilizationTakeOffTotal' => 0,
                    'revenueDailyUtilizationTakeOffTotal' => 0,
                    'technicalDelayTotal' => 0,
                    'totalDuration' => '0 : 00',
                    'averageDuration' => '0 : 00',
                    'ratePer100TakeOff' => 0,
                    'technicalIncidentTotal' => 0,
                    'technicalIncidentRate' => 0,
                    'technicalCancellationTotal' => 0,
                    'dispatchReliability' => 0,
                ];
                continue;
            }

            // Calculate metrics dari data yang sudah ada
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $acInService = $daysInMonth > 0 ? $formatRate($monthly->days_in_service / $daysInMonth) : 0;

            // Flight metrics
            $flightHoursPerTakeOffTotal = $monthly->take_off_total > 0 ? 
                $monthly->flying_hours_total / $monthly->take_off_total : 0;
            $revenueFlightHoursPerTakeOff = $monthly->revenue_take_off > 0 ? 
                $monthly->revenue_flying_hours / $monthly->revenue_take_off : 0;

            // Daily utilization metrics
            $dailyUtilizationFlyingHoursTotal = $monthly->days_in_service > 0 ? 
                $monthly->flying_hours_total / $monthly->days_in_service : 0;
            $revenueDailyUtilizationFlyingHoursTotal = $monthly->days_in_service > 0 ? 
                $monthly->revenue_flying_hours / $monthly->days_in_service : 0;
            $dailyUtilizationTakeOffTotal = $monthly->days_in_service > 0 ? 
                $monthly->take_off_total / $monthly->days_in_service : 0;
            $revenueDailyUtilizationTakeOffTotal = $monthly->days_in_service > 0 ? 
                $monthly->revenue_take_off / $monthly->days_in_service : 0;

            // Technical metrics with null safety
            $technicalDelayTotal = $techDelay->technical_delay_total ?? 0;
            $totalDuration = $techDelay->total_duration ?? 0;
            $averageDuration = $technicalDelayTotal > 0 ? $totalDuration / $technicalDelayTotal : 0;
            $ratePer100TakeOff = $monthly->revenue_take_off > 0 ? 
                $formatRate(($technicalDelayTotal * 100) / $monthly->revenue_take_off) : 0;

            $technicalIncidentTotal = $techIncident->technical_incident_total ?? 0;
            $technicalIncidentRate = $monthly->revenue_take_off > 0 ? 
                $formatRate(($technicalIncidentTotal * 100) / $monthly->revenue_take_off) : 0;

            $technicalCancellationTotal = $techCancellation->technical_cancellation_total ?? 0;
            $dispatchReliability = $monthly->revenue_take_off > 0 ? 
                $formatRate((($monthly->revenue_take_off - $technicalDelayTotal - $technicalCancellationTotal) 
                / $monthly->revenue_take_off) * 100) : 0;

            // Store hasil calculation
            $reportData[$currentPeriod] = [
                'acInFleet' => $monthly->ac_in_fleet,
                'acInService' => $acInService,
                'daysInService' => $monthly->days_in_service,
                'flyingHoursTotal' => $monthly->flying_hours_total,
                'revenueFlyingHours' => $monthly->revenue_flying_hours,
                'takeOffTotal' => $monthly->take_off_total,
                'revenueTakeOff' => $monthly->revenue_take_off,
                'flightHoursPerTakeOffTotal' => $this->convertDecimalToHoursMinutes($flightHoursPerTakeOffTotal),
                'revenueFlightHoursPerTakeOff' => $this->convertDecimalToHoursMinutes($revenueFlightHoursPerTakeOff),
                'dailyUtilizationFlyingHoursTotal' => $this->convertDecimalToHoursMinutes($dailyUtilizationFlyingHoursTotal),
                'revenueDailyUtilizationFlyingHoursTotal' => $this->convertDecimalToHoursMinutes($revenueDailyUtilizationFlyingHoursTotal),
                'dailyUtilizationTakeOffTotal' => $formatRate($dailyUtilizationTakeOffTotal),
                'revenueDailyUtilizationTakeOffTotal' => $formatRate($revenueDailyUtilizationTakeOffTotal),
                'technicalDelayTotal' => $technicalDelayTotal,
                'totalDuration' => $this->convertDecimalToHoursMinutes($totalDuration),
                'averageDuration' => $this->convertDecimalToHoursMinutes($averageDuration),
                'ratePer100TakeOff' => $ratePer100TakeOff,
                'technicalIncidentTotal' => $technicalIncidentTotal,
                'technicalIncidentRate' => $technicalIncidentRate,
                'technicalCancellationTotal' => $technicalCancellationTotal,
                'dispatchReliability' => $dispatchReliability,
            ];

            // Accumulate totals for averages
            $totalFlightHoursPerTakeOffTotal += $flightHoursPerTakeOffTotal;
            $totalRevenueFlightHoursPerTakeOff += $revenueFlightHoursPerTakeOff;
            $totalDailyUtilizationFlyingHoursTotal += $dailyUtilizationFlyingHoursTotal;
            $totalRevenueDailyUtilizationFlyingHoursTotal += $revenueDailyUtilizationFlyingHoursTotal;
            $totalTotalDuration += $totalDuration;
            $totalAverageDuration += $averageDuration;
        }

        // Calculate averages
        $avgFlightHoursPerTakeOffTotal = $this->convertDecimalToHoursMinutes($totalFlightHoursPerTakeOffTotal / 12);
        $avgRevenueFlightHoursPerTakeOff = $this->convertDecimalToHoursMinutes($totalRevenueFlightHoursPerTakeOff / 12);
        $avgDailyUtilizationFlyingHoursTotal = $this->convertDecimalToHoursMinutes($totalDailyUtilizationFlyingHoursTotal / 12);
        $avgRevenueDailyUtilizationFlyingHoursTotal = $this->convertDecimalToHoursMinutes($totalRevenueDailyUtilizationFlyingHoursTotal / 12);
        $avgTotalDuration = $this->convertDecimalToHoursMinutes($totalTotalDuration / 12);
        $avgAverageDuration = $this->convertDecimalToHoursMinutes($totalAverageDuration / 12);

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

    // ✅ REFACTORED: Single Query Approach untuk Pilot Data - FIXED GROUP BY Issues
    private function getPilotData($aircraftType, $period, $request = null)
    {
        $endDate = Carbon::parse($period)->endOfMonth();
        $startDate = Carbon::parse($period)->subMonths(11)->startOfMonth();
        
        // Helper function untuk format rate
        $formatRate = function($value) {
            return (float) number_format($value, 10, '.', '');
        };

        // ✅ FIXED: SINGLE QUERY untuk semua Flying Hours (12 months) - Proper GROUP BY
        $flyingHoursData = TblMonthlyfhfc::where('Actype', $aircraftType)
            ->whereBetween('MonthEval', [$startDate, $endDate])
            ->selectRaw('
                DATE_FORMAT(MonthEval, "%Y-%m") as period,
                SUM(RevFHHours + (RevFHMin / 60) + NoRevFHHours + (NoRevFHMin / 60)) as flying_hours,
                SUM(COALESCE(RevFC, 0) + COALESCE(NoRevFC, 0)) as flying_cycles
            ')
            ->groupBy(DB::raw('DATE_FORMAT(MonthEval, "%Y-%m")'))
            ->get()
            ->keyBy('period');

        // Extract flying hours data untuk 3 months dan 12 months
        $flyingHoursTotal = $flyingHoursData[Carbon::parse($period)->format('Y-m')]->flying_hours ?? 0;
        $flyingHoursBefore = $flyingHoursData[Carbon::parse($period)->subMonth(1)->format('Y-m')]->flying_hours ?? 0;
        $flyingHours2Before = $flyingHoursData[Carbon::parse($period)->subMonth(2)->format('Y-m')]->flying_hours ?? 0;
        $fh3Last = $flyingHoursTotal + $flyingHoursBefore + $flyingHours2Before;
        $fh12Last = $flyingHoursData->sum('flying_hours');

        // Extract flying cycles data
        $flyingCyclesTotal = $flyingHoursData[Carbon::parse($period)->format('Y-m')]->flying_cycles ?? 0;
        $flyingCyclesBefore = $flyingHoursData[Carbon::parse($period)->subMonth(1)->format('Y-m')]->flying_cycles ?? 0;
        $flyingCycles2Before = $flyingHoursData[Carbon::parse($period)->subMonth(2)->format('Y-m')]->flying_cycles ?? 0;
        $fc3Last = $flyingCyclesTotal + $flyingCyclesBefore + $flyingCycles2Before;
        $fc12Last = $flyingHoursData->sum('flying_cycles');

        // Get excluded ATA dan TblAta
        $excludedIds = [5, 11, 12, 58, 70];
        $tblAta = TblMasterAta::whereNotIn('ATA', $excludedIds)->get();

        // ✅ FIXED: SINGLE QUERY untuk semua PIREP data (12 months) - Removed non-aggregated DATE column
        $pirepData = TblPirepSwift::where('ACTYPE', $aircraftType)
            ->whereBetween('DATE', [$startDate, $endDate])
            ->where('PirepMarep', 'Pirep')
            ->whereIn('ATA', $tblAta->pluck('ATA'))
            ->selectRaw('
                YEAR(DATE) as year,
                MONTH(DATE) as month,
                ATA,
                COUNT(*) as count
            ')
            ->groupBy(DB::raw('YEAR(DATE)'), DB::raw('MONTH(DATE)'), 'ATA')
            ->get()
            ->groupBy(['year', 'month', 'ATA']);

        // ✅ FIXED: SINGLE QUERY untuk semua MAREP data (12 months) - Removed non-aggregated DATE column
        $marepData = TblPirepSwift::where('ACTYPE', $aircraftType)
            ->whereBetween('DATE', [$startDate, $endDate])
            ->where('PirepMarep', 'Marep')
            ->whereIn('ATA', $tblAta->pluck('ATA'))
            ->selectRaw('
                YEAR(DATE) as year,
                MONTH(DATE) as month,
                ATA,
                COUNT(*) as count
            ')
            ->groupBy(DB::raw('YEAR(DATE)'), DB::raw('MONTH(DATE)'), 'ATA')
            ->get()
            ->groupBy(['year', 'month', 'ATA']);

        // ✅ FIXED: SINGLE QUERY untuk semua DELAY data (12 months) - Removed non-aggregated DateEvent column
        $delayData = Mcdrnew::where('ACtype', $aircraftType)
            ->whereBetween('DateEvent', [$startDate, $endDate])
            ->where('DCP', '<>', 'X')
            ->whereIn('ATAtdm', $tblAta->pluck('ATA'))
            ->selectRaw('
                YEAR(DateEvent) as year,
                MONTH(DateEvent) as month,
                ATAtdm as ATA,
                COUNT(*) as count
            ')
            ->groupBy(DB::raw('YEAR(DateEvent)'), DB::raw('MONTH(DateEvent)'), 'ATAtdm')
            ->get()
            ->groupBy(['year', 'month', 'ATA']);

        // ✅ SINGLE QUERY untuk semua Alert Levels
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

        // Helper function untuk get count dari grouped data
        $getCountFromGroupedData = function($groupedData, $year, $month, $ata) {
            return $groupedData[$year][$month][$ata][0]->count ?? 0;
        };

        // Process data per ATA (NO MORE LOOPS WITH QUERIES!)
        $reportPerAta = [];
        $currentYear = Carbon::parse($period)->year;
        $currentMonth = Carbon::parse($period)->month;
        $beforeYear = Carbon::parse($period)->subMonth(1)->year;
        $beforeMonth = Carbon::parse($period)->subMonth(1)->month;
        $twoMonthsAgoYear = Carbon::parse($period)->subMonth(2)->year;
        $twoMonthsAgoMonth = Carbon::parse($period)->subMonth(2)->month;

        foreach ($tblAta as $ataRow) {
            $ata = $ataRow->ATA;
            $ata_name = $ataRow->ATA_DESC ?? $ataRow->ATAName ?? '';

            // ===== PIREP CALCULATIONS =====
            $pirepCount = $getCountFromGroupedData($pirepData, $currentYear, $currentMonth, $ata);
            $pirepCountBefore = $getCountFromGroupedData($pirepData, $beforeYear, $beforeMonth, $ata);
            $pirepCountTwoMonthsAgo = $getCountFromGroupedData($pirepData, $twoMonthsAgoYear, $twoMonthsAgoMonth, $ata);
            $pirep3Month = $pirepCount + $pirepCountBefore + $pirepCountTwoMonthsAgo;
            
            // Calculate 12 months PIREP
            $pirep12Month = 0;
            for ($i = 0; $i < 12; $i++) {
                $date = Carbon::parse($period)->subMonths($i);
                $pirep12Month += $getCountFromGroupedData($pirepData, $date->year, $date->month, $ata);
            }

            // PIREP Rates
            $pirepRate = $formatRate($pirepCount * 1000 / ($flyingHoursTotal ?: 1));
            $pirep1Rate = $formatRate($pirepCountBefore * 1000 / ($flyingHoursBefore ?: 1));
            $pirep2Rate = $formatRate($pirepCountTwoMonthsAgo * 1000 / ($flyingHours2Before ?: 1));
            $pirepRate3Month = $formatRate(($pirepRate + $pirep1Rate + $pirep2Rate) / 3);
            $pirepRate12Month = $formatRate($pirep12Month * 1000 / ($fh12Last ?: 1));
            
            // PIREP Alert Level
            $pirepAlertLevel = $alertLevels[$ata]['ALP'][0]->alertlevel ?? null;
            if (is_null($pirepAlertLevel)) {
                $pirepRates = [$pirepRate, $pirep1Rate, $pirep2Rate];
                for ($i = 3; $i < 12; $i++) {
                    $date = Carbon::parse($period)->subMonths($i);
                    $fh = $flyingHoursData[$date->format('Y-m')]->flying_hours ?? 1;
                    $count = $getCountFromGroupedData($pirepData, $date->year, $date->month, $ata);
                    $rate = $count * 1000 / $fh;
                    $pirepRates[] = $rate;
                }
                
                $mean = array_sum($pirepRates) / count($pirepRates);
                $variance = array_sum(array_map(function($rate) use ($mean) {
                    return pow($rate - $mean, 2);
                }, $pirepRates)) / count($pirepRates);
                $stddev = sqrt($variance);
                $pirepAlertLevel = $mean + 2 * $stddev;
            }

            // PIREP Alert Status
            $pirepAlertStatus = '';
            if ($pirepRate > $pirepAlertLevel && $pirep1Rate > $pirepAlertLevel && $pirep2Rate > $pirepAlertLevel){
                $pirepAlertStatus = 'RED-3';
            } elseif ($pirepRate > $pirepAlertLevel && $pirep1Rate > $pirepAlertLevel){
                $pirepAlertStatus = 'RED-2';
            } elseif ($pirepRate > $pirepAlertLevel){
                $pirepAlertStatus = 'RED-1';
            }
            
            // PIREP Trend
            $pirepTrend = '';
            if ($pirepRate < $pirep1Rate && $pirep1Rate < $pirep2Rate) {
                $pirepTrend = 'DOWN';
            } elseif ($pirepRate > $pirep1Rate && $pirep1Rate > $pirep2Rate) {
                $pirepTrend = 'UP';
            }

            // ===== MAREP CALCULATIONS =====
            $marepCount = $getCountFromGroupedData($marepData, $currentYear, $currentMonth, $ata);
            $marepCountBefore = $getCountFromGroupedData($marepData, $beforeYear, $beforeMonth, $ata);
            $marepCountTwoMonthsAgo = $getCountFromGroupedData($marepData, $twoMonthsAgoYear, $twoMonthsAgoMonth, $ata);
            $marep3Month = $marepCount + $marepCountBefore + $marepCountTwoMonthsAgo;
            
            // Calculate 12 months MAREP
            $marep12Month = 0;
            for ($i = 0; $i < 12; $i++) {
                $date = Carbon::parse($period)->subMonths($i);
                $marep12Month += $getCountFromGroupedData($marepData, $date->year, $date->month, $ata);
            }

            // MAREP Rates
            $marepRate = $formatRate($marepCount * 1000 / ($flyingHoursTotal ?: 1));
            $marep1Rate = $formatRate($marepCountBefore * 1000 / ($flyingHoursBefore ?: 1));
            $marep2Rate = $formatRate($marepCountTwoMonthsAgo * 1000 / ($flyingHours2Before ?: 1));
            $marepRate3Month = $formatRate(($marepRate + $marep1Rate + $marep2Rate) / 3);
            $marepRate12Month = $formatRate($marep12Month * 1000 / ($fh12Last ?: 1));
            
            // MAREP Alert Level
            $marepAlertLevel = $alertLevels[$ata]['ALM'][0]->alertlevel ?? null;
            if (is_null($marepAlertLevel)) {
                $marepRates = [$marepRate, $marep1Rate, $marep2Rate];
                for ($i = 3; $i < 12; $i++) {
                    $date = Carbon::parse($period)->subMonths($i);
                    $fh = $flyingHoursData[$date->format('Y-m')]->flying_hours ?? 1;
                    $count = $getCountFromGroupedData($marepData, $date->year, $date->month, $ata);
                    $rate = $count * 1000 / $fh;
                    $marepRates[] = $rate;
                }
                
                $mean = array_sum($marepRates) / count($marepRates);
                $variance = array_sum(array_map(function($rate) use ($mean) {
                    return pow($rate - $mean, 2);
                }, $marepRates)) / count($marepRates);
                $stddev = sqrt($variance);
                $marepAlertLevel = $mean + 2 * $stddev;
            }

            // MAREP Alert Status
            $marepAlertStatus = '';
            if ($marepRate > $marepAlertLevel && $marep1Rate > $marepAlertLevel && $marep2Rate > $marepAlertLevel) {
                $marepAlertStatus = 'RED-3';
            } elseif ($marepRate > $marepAlertLevel && $marep1Rate > $marepAlertLevel) {
                $marepAlertStatus = 'RED-2';
            } elseif ($marepRate > $marepAlertLevel) {
                $marepAlertStatus = 'RED-1';
            }
            
            // MAREP Trend
            $marepTrend = '';
            if ($marepRate < $marep1Rate && $marep1Rate < $marep2Rate) {
                $marepTrend = 'DOWN';
            } elseif ($marepRate > $marep1Rate && $marep1Rate > $marep2Rate) {
                $marepTrend = 'UP';
            }

            // ===== DELAY CALCULATIONS =====
            $delayCount = $getCountFromGroupedData($delayData, $currentYear, $currentMonth, $ata);
            $delayCountBefore = $getCountFromGroupedData($delayData, $beforeYear, $beforeMonth, $ata);
            $delayCountTwoMonthsAgo = $getCountFromGroupedData($delayData, $twoMonthsAgoYear, $twoMonthsAgoMonth, $ata);
            $delay3Month = $delayCount + $delayCountBefore + $delayCountTwoMonthsAgo;
            
            // Calculate 12 months DELAY
            $delay12Month = 0;
            for ($i = 0; $i < 12; $i++) {
                $date = Carbon::parse($period)->subMonths($i);
                $delay12Month += $getCountFromGroupedData($delayData, $date->year, $date->month, $ata);
            }

            // DELAY Rates
            $delayRate = $formatRate($delayCount * 1000 / ($flyingCyclesTotal ?: 1));
            $delay1Rate = $formatRate($delayCountBefore * 1000 / ($flyingCyclesBefore ?: 1));
            $delay2Rate = $formatRate($delayCountTwoMonthsAgo * 1000 / ($flyingCycles2Before ?: 1));
            $delayRate3Month = $formatRate(($delayRate + $delay1Rate + $delay2Rate) / 3);
            $delayRate12Month = $formatRate($delay12Month * 1000 / ($fc12Last ?: 1));

            // DELAY Alert Level
            $delayAlertLevel = $alertLevels[$ata]['ALD'][0]->alertlevel ?? null;
            if (is_null($delayAlertLevel)) {
                $delayRates = [$delayRate, $delay1Rate, $delay2Rate];
                for ($i = 3; $i < 12; $i++) {
                    $date = Carbon::parse($period)->subMonths($i);
                    $fc = $flyingHoursData[$date->format('Y-m')]->flying_cycles ?? 1;
                    $count = $getCountFromGroupedData($delayData, $date->year, $date->month, $ata);
                    $rate = $count * 1000 / $fc;
                    $delayRates[] = $rate;
                }
                
                $mean = array_sum($delayRates) / count($delayRates);
                $variance = array_sum(array_map(function($rate) use ($mean) {
                    return pow($rate - $mean, 2);
                }, $delayRates)) / count($delayRates);
                $stddev = sqrt($variance);
                $delayAlertLevel = $mean + 2 * $stddev;
            }

            // DELAY Alert Status
            $delayAlertStatus = '';
            if ($delayRate > $delayAlertLevel && $delay1Rate > $delayAlertLevel && $delay2Rate > $delayAlertLevel){
                $delayAlertStatus = 'RED-3';
            } elseif ($delayRate > $delayAlertLevel && $delay1Rate > $delayAlertLevel){
                $delayAlertStatus = 'RED-2';
            } elseif ($delayRate > $delayAlertLevel){
                $delayAlertStatus = 'RED-1';
            }
            
            // DELAY Trend
            $delayTrend = '';
            if ($delayRate < $delay1Rate && $delay1Rate < $delay2Rate) {
                $delayTrend = 'DOWN';
            } elseif ($delayRate > $delay1Rate && $delay1Rate > $delay2Rate) {
                $delayTrend = 'UP';
            }

            // Store hasil per ATA
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
            'month' => Carbon::parse($period)->month
        ];
    }

    // Helper method untuk convert decimal ke format HH:MM
    private function convertDecimalToHoursMinutes($decimalHours) 
    {
        $hours = floor($decimalHours);
        $minutes = round(($decimalHours - $hours) * 60);
        return sprintf('%d : %02d', $hours, $minutes);
    }
}