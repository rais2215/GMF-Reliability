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
        // UNIFIED: Hanya ambil Aircraft Types dari TblMasterac untuk kedua report
        $aircraftTypes = TblMasterac::select('ACType')->distinct()
            ->whereNotNull('ACType')
            ->where('ACType', '!=', '')
            ->orderBy('ACType')
            ->get();

        // Ambil Operators dari TblMasterac
        $operators = TblMasterac::select('Operator')->distinct()
            ->whereNotNull('Operator')
            ->where('Operator', '!=', '')
            ->orderBy('Operator')
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
            'aircraftTypes',  // Sekarang hanya 1 variable untuk aircraft types
            'operators',
            'periods'
        ));
    }

    public function store(Request $request)
    {
        // Validate input - sekarang hanya perlu 1 aircraft_type
        $request->validate([
            'period' => 'required',
            'aircraft_type' => 'required',  // Hanya 1 field aircraft_type
        ]);

        $period = $request->period;
        $operator = $request->operator;
        $aircraftType = $request->aircraft_type;  // Same aircraft type for both reports

        // ===== GET AOS DATA =====
        $aosData = $this->getAosData($aircraftType, $period);

        // Ambil data tahun dasar (baseYear) dan data tahun berjalan
        $baseYear = \Carbon\Carbon::parse($period)->subMonths(11)->format('Y');
        $yearColumnData = $this->getAosYearlySummary($aircraftType, $baseYear);

        // Data rata-rata rolling 12 bulan terakhir
        $avgFlightHoursPerTakeOffTotal = $aosData['avgFlightHoursPerTakeOffTotal'] ?? '0:00';
        $avgRevenueFlightHoursPerTakeOff = $aosData['avgRevenueFlightHoursPerTakeOff'] ?? '0:00';
        $avgDailyUtilizationFlyingHoursTotal = $aosData['avgDailyUtilizationFlyingHoursTotal'] ?? '0:00';
        $avgRevenueDailyUtilizationFlyingHoursTotal = $aosData['avgRevenueDailyUtilizationFlyingHoursTotal'] ?? '0:00';
        $avgTotalDuration = $aosData['avgTotalDuration'] ?? '0:00';
        $avgAverageDuration = $aosData['avgAverageDuration'] ?? '0:00';

        $reportData = $aosData['reportData'] ?? [];
        $averages = $aosData['averages'] ?? [];

        // ===== GET PILOT DATA =====
        $pilotData = $this->getPilotData($aircraftType, $period, $request);

        return view('report.combined-result', [
            'aosData' => $aosData,
            'pilotData' => $pilotData,
            'aircraftType' => $aircraftType,  // Same aircraft type for both
            'period' => $period,
            'operator' => $operator,
            'baseYear' => $baseYear,
            'yearColumnData' => $yearColumnData
        ]);
    }

    public function exportPdf(Request $request)
    {
        $request->validate([
            'period' => 'required',
            'aircraft_type' => 'required',  // Hanya 1 field aircraft_type
        ]);

        $period = $request->period;
        $operator = $request->operator;
        $aircraftType = $request->aircraft_type;  // Same aircraft type for both

        // ===== GET AOS DATA =====
        $aosData = $this->getAosData($aircraftType, $period);

        $baseYear = \Carbon\Carbon::parse($period)->subMonths(11)->format('Y');
        $yearColumnData = $this->getAosYearlySummary($aircraftType, $baseYear);

        $avgFlightHoursPerTakeOffTotal = $aosData['avgFlightHoursPerTakeOffTotal'] ?? '0:00';
        $avgRevenueFlightHoursPerTakeOff = $aosData['avgRevenueFlightHoursPerTakeOff'] ?? '0:00';
        $avgDailyUtilizationFlyingHoursTotal = $aosData['avgDailyUtilizationFlyingHoursTotal'] ?? '0:00';
        $avgRevenueDailyUtilizationFlyingHoursTotal = $aosData['avgRevenueDailyUtilizationFlyingHoursTotal'] ?? '0:00';
        $avgTotalDuration = $aosData['avgTotalDuration'] ?? '0:00';
        $avgAverageDuration = $aosData['avgAverageDuration'] ?? '0:00';

        $reportData = $aosData['reportData'] ?? [];
        $averages = $aosData['averages'] ?? [];

        // ===== GET PILOT DATA =====
        $pilotData = $this->getPilotData($aircraftType, $period, $request);

        // Helper function untuk format number
        $formatNumber = function($value, $decimals = 2) {
            return rtrim(rtrim(number_format($value, $decimals), '0'), '.');
        };

        $data = [
            'aosData' => $aosData,
            'pilotData' => $pilotData,
            'aircraftType' => $aircraftType,
            'period' => $period,
            'operator' => $operator,
            'formatNumber' => $formatNumber,
            'baseYear' => $baseYear,
            'yearColumnData' => $yearColumnData,
            'reportData' => $reportData,
            'averages' => $averages,
            'avgFlightHoursPerTakeOffTotal' => $avgFlightHoursPerTakeOffTotal,
            'avgRevenueFlightHoursPerTakeOff' => $avgRevenueFlightHoursPerTakeOff,
            'avgDailyUtilizationFlyingHoursTotal' => $avgDailyUtilizationFlyingHoursTotal,
            'avgRevenueDailyUtilizationFlyingHoursTotal' => $avgRevenueDailyUtilizationFlyingHoursTotal,
            'avgTotalDuration' => $avgTotalDuration,
            'avgAverageDuration' => $avgAverageDuration,
        ];;

        // Generate PDF
        $pdf = Pdf::loadView('pdf.combined-pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'sans-serif'
        ]);

        $periodFormatted = \Carbon\Carbon::parse($period)->format('Y-m');
        $filename = "Fleet_Reliability_Report_{$aircraftType}_{$periodFormatted}.pdf";

        return $pdf->download($filename);
    }

    /**
     * NEW METHOD: Calculates the Aircraft Operation Summary for a full year.
     * This method fetches aggregated data for an entire year to prevent calculation errors.
     *
     * @param string $aircraftType
     * @param string $year
     * @return array
     */
    private function getAosYearlySummary($aircraftType, $year)
    {
        $startDate = Carbon::createFromDate($year)->startOfYear();
        $endDate = Carbon::createFromDate($year)->endOfYear();

        // 1. Calculate the average number of aircraft in the fleet for the year
        $avgAcInFleetData = TblMonthlyfhfc::where('Actype', $aircraftType)
            ->whereBetween('MonthEval', [$startDate, $endDate])
            ->where(function($query) {
                $query->whereNull('Remark')
                      ->orWhere('Remark', '')
                      ->orWhere('Remark', '!=', 'out')
                      ->orWhere('Remark', 'not like', '%out%');
            })
            ->selectRaw('COUNT(Reg) as ac_in_fleet_monthly')
            ->groupBy(DB::raw('YEAR(MonthEval)'), DB::raw('MONTH(MonthEval)'))
            ->get();
        $acInFleet = $avgAcInFleetData->avg('ac_in_fleet_monthly') ?? 0;

        // 2. Get yearly totals for flying hours, cycles, and days in service
        $yearlyTotals = TblMonthlyfhfc::where('Actype', $aircraftType)
            ->whereBetween('MonthEval', [$startDate, $endDate])
            ->selectRaw('
                SUM(AvaiDays) as days_in_service,
                SUM(RevFHHours + (RevFHMin / 60) + NoRevFHHours + (NoRevFHMin / 60)) as flying_hours_total,
                SUM(RevFHHours + (RevFHMin / 60)) as revenue_flying_hours,
                SUM(RevFC + NoRevFC) as take_off_total,
                SUM(RevFC) as revenue_take_off
            ')
            ->first();

        // 3. Get yearly totals for technical delays
        $technicalDelayTotals = Mcdrnew::where('ACType', $aircraftType)
            ->whereBetween('DateEvent', [$startDate, $endDate])
            ->where('DCP', 'LIKE', '%D%')
            ->selectRaw('
                COUNT(*) as technical_delay_total,
                SUM(HoursTek + (MinTek / 60)) as total_duration
            ')
            ->first();

        // 4. Get yearly total for technical incidents
        $technicalIncidentTotal = TblSdr::where('ACType', $aircraftType)
            ->whereBetween('DateOccur', [$startDate, $endDate])
            ->count();

        // 5. Get yearly total for technical cancellations
        $technicalCancellationTotal = Mcdrnew::where('ACType', $aircraftType)
            ->whereBetween('DateEvent', [$startDate, $endDate])
            ->where('DCP', 'LIKE', '%C%')
            ->count();

        // 6. Calculate all metrics based on the annual aggregate data
        $daysInYear = $startDate->isLeapYear() ? 366 : 365;
        $daysInService = $yearlyTotals->days_in_service ?? 0;
        $acInService = $daysInYear > 0 ? ($daysInService / $daysInYear) : 0;

        $flyingHoursTotal = $yearlyTotals->flying_hours_total ?? 0;
        $takeOffTotal = $yearlyTotals->take_off_total ?? 0;
        $revenueFlyingHours = $yearlyTotals->revenue_flying_hours ?? 0;
        $revenueTakeOff = $yearlyTotals->revenue_take_off ?? 0;

        $flightHoursPerTakeOffTotal = $takeOffTotal > 0 ? ($flyingHoursTotal / $takeOffTotal) : 0;
        $revenueFlightHoursPerTakeOff = $revenueTakeOff > 0 ? ($revenueFlyingHours / $revenueTakeOff) : 0;
        $dailyUtilizationFlyingHoursTotal = $daysInService > 0 ? ($flyingHoursTotal / $daysInService) : 0;
        $revenueDailyUtilizationFlyingHoursTotal = $daysInService > 0 ? ($revenueFlyingHours / $daysInService) : 0;
        $dailyUtilizationTakeOffTotal = $daysInService > 0 ? ($takeOffTotal / $daysInService) : 0;
        $revenueDailyUtilizationTakeOffTotal = $daysInService > 0 ? ($revenueTakeOff / $daysInService) : 0;

        $technicalDelayTotal = $technicalDelayTotals->technical_delay_total ?? 0;
        $totalDuration = $technicalDelayTotals->total_duration ?? 0;
        $averageDuration = $technicalDelayTotal > 0 ? ($totalDuration / $technicalDelayTotal) : 0;
        $ratePer100TakeOff = $revenueTakeOff > 0 ? (($technicalDelayTotal * 100) / $revenueTakeOff) : 0;

        $technicalIncidentRate = $revenueTakeOff > 0 ? (($technicalIncidentTotal * 100) / $revenueTakeOff) : 0;

        $dispatchReliability = $revenueTakeOff > 0 ?
            ((($revenueTakeOff - $technicalDelayTotal - $technicalCancellationTotal) / $revenueTakeOff) * 100) : 0;

        // 7. Return a single associative array with all calculated metrics
        return [
            'acInFleet' => $acInFleet,
            'acInService' => $acInService,
            'daysInService' => $daysInService,
            'flyingHoursTotal' => $flyingHoursTotal,
            'revenueFlyingHours' => $revenueFlyingHours,
            'takeOffTotal' => $takeOffTotal,
            'revenueTakeOff' => $revenueTakeOff,
            'flightHoursPerTakeOffTotal' => $flightHoursPerTakeOffTotal,
            'revenueFlightHoursPerTakeOff' => $revenueFlightHoursPerTakeOff,
            'dailyUtilizationFlyingHoursTotal' => $dailyUtilizationFlyingHoursTotal,
            'revenueDailyUtilizationFlyingHoursTotal' => $revenueDailyUtilizationFlyingHoursTotal,
            'dailyUtilizationTakeOffTotal' => $dailyUtilizationTakeOffTotal,
            'revenueDailyUtilizationTakeOffTotal' => $revenueDailyUtilizationTakeOffTotal,
            'technicalDelayTotal' => $technicalDelayTotal,
            'totalDuration' => $totalDuration,
            'averageDuration' => $averageDuration,
            'ratePer100TakeOff' => $ratePer100TakeOff,
            'technicalIncidentTotal' => $technicalIncidentTotal,
            'technicalIncidentRate' => $technicalIncidentRate,
            'technicalCancellationTotal' => $technicalCancellationTotal,
            'dispatchReliability' => $dispatchReliability,
        ];
    }

    // Helper function untuk format number
    private function formatNumber($value, $decimals = 2)
    {
        if (!is_numeric($value)) {
            return '0';
        }
        return rtrim(rtrim(number_format($value, $decimals, '.', ''), '0'), '.');
    }

    // Helper function untuk format rate dengan precision tinggi
    private function formatRate($value, $decimals = 10)
    {
        if (!is_numeric($value)) {
            return 0;
        }
        return (float) number_format($value, $decimals, '.', '');
    }

    // Private method untuk mengambil semua data AOS dengan queries terpisah (mengikuti ReportController)
    private function getAosReportData($aircraftType, $period)
    {
        $endDate = Carbon::parse($period)->endOfMonth();
        $startDate = Carbon::parse($period)->subMonths(11)->startOfMonth();

        // Query terpisah untuk A/C in Fleet (exclude remark "out")
        $acInFleetData = TblMonthlyfhfc::where('Actype', $aircraftType)
            ->whereBetween('MonthEval', [$startDate, $endDate])
            ->where(function($query) {
                $query->whereNull('Remark')
                      ->orWhere('Remark', '')
                      ->orWhere('Remark', '!=', 'out')
                      ->orWhere('Remark', 'not like', '%out%');
            })
            ->selectRaw('
                YEAR(MonthEval) as year,
                MONTH(MonthEval) as month,
                COUNT(Reg) as ac_in_fleet_only
            ')
            ->groupBy(DB::raw('YEAR(MonthEval)'), DB::raw('MONTH(MonthEval)'))
            ->get()
            ->keyBy(function($item) {
                return $item->year . '-' . sprintf('%02d', $item->month);
            });

        // Query untuk semua data lainnya (tanpa filter remark "out")
        $monthlyData = TblMonthlyfhfc::where('Actype', $aircraftType)
            ->whereBetween('MonthEval', [$startDate, $endDate])
            ->selectRaw('
                YEAR(MonthEval) as year,
                MONTH(MonthEval) as month,
                COUNT(Reg) as ac_total_count,
                SUM(AvaiDays) as days_in_service,
                SUM(RevFHHours + (RevFHMin / 60) + NoRevFHHours + (NoRevFHMin / 60)) as flying_hours_total,
                SUM(RevFHHours + (RevFHMin / 60)) as revenue_flying_hours,
                SUM(RevFC + NoRevFC) as take_off_total,
                SUM(RevFC) as revenue_take_off
            ')
            ->groupBy(DB::raw('YEAR(MonthEval)'), DB::raw('MONTH(MonthEval)'))
            ->get()
            ->keyBy(function($item) {
                return $item->year . '-' . sprintf('%02d', $item->month);
            });

        // Single query untuk data technical delay dengan proper GROUP BY
        $technicalDelayData = Mcdrnew::where('ACType', $aircraftType)
            ->whereBetween('DateEvent', [$startDate, $endDate])
            ->where('DCP', 'LIKE', '%D%')
            ->selectRaw('
                YEAR(DateEvent) as year,
                MONTH(DateEvent) as month,
                COUNT(*) as technical_delay_total,
                SUM(HoursTek + (MinTek / 60)) as total_duration
            ')
            ->groupBy(DB::raw('YEAR(DateEvent)'), DB::raw('MONTH(DateEvent)'))
            ->get()
            ->keyBy(function($item) {
                return $item->year . '-' . sprintf('%02d', $item->month);
            });

        // Single query untuk data technical incident dengan proper GROUP BY
        $technicalIncidentData = TblSdr::where('ACType', $aircraftType)
            ->whereBetween('DateOccur', [$startDate, $endDate])
            ->selectRaw('
                YEAR(DateOccur) as year,
                MONTH(DateOccur) as month,
                COUNT(*) as technical_incident_total
            ')
            ->groupBy(DB::raw('YEAR(DateOccur)'), DB::raw('MONTH(DateOccur)'))
            ->get()
            ->keyBy(function($item) {
                return $item->year . '-' . sprintf('%02d', $item->month);
            });

        // Single query untuk data technical cancellation dengan proper GROUP BY
        $technicalCancellationData = Mcdrnew::where('ACType', $aircraftType)
            ->whereBetween('DateEvent', [$startDate, $endDate])
            ->where('DCP', 'LIKE', '%C%')
            ->selectRaw('
                YEAR(DateEvent) as year,
                MONTH(DateEvent) as month,
                COUNT(*) as technical_cancellation_total
            ')
            ->groupBy(DB::raw('YEAR(DateEvent)'), DB::raw('MONTH(DateEvent)'))
            ->get()
            ->keyBy(function($item) {
                return $item->year . '-' . sprintf('%02d', $item->month);
            });

        return compact('acInFleetData', 'monthlyData', 'technicalDelayData', 'technicalIncidentData', 'technicalCancellationData');
    }

    // Private method untuk mendapatkan default report data
    private function getDefaultReportData()
    {
        return [
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
    }

    // Calculate averages dengan proper logic untuk A/C In Fleet (mengikuti ReportController)
    private function calculateAverages($reportData, $period)
    {
        $averages = [];
        $metrics = [
            'acInFleet' => ['type' => 'average_valid', 'format' => 'number'],
            'acInService' => ['type' => 'average_all', 'format' => 'number'],
            'daysInService' => ['type' => 'sum', 'format' => 'round'],
            'flyingHoursTotal' => ['type' => 'sum', 'format' => 'round'],
            'revenueFlyingHours' => ['type' => 'sum', 'format' => 'round'],
            'takeOffTotal' => ['type' => 'sum', 'format' => 'round'],
            'revenueTakeOff' => ['type' => 'sum', 'format' => 'round'],
            'technicalDelayTotal' => ['type' => 'sum', 'format' => 'round'],
            'technicalIncidentTotal' => ['type' => 'sum', 'format' => 'round'],
            'technicalCancellationTotal' => ['type' => 'sum', 'format' => 'round'],
            'dailyUtilizationTakeOffTotal' => ['type' => 'average_all', 'format' => 'number'],
            'revenueDailyUtilizationTakeOffTotal' => ['type' => 'average_all', 'format' => 'number'],
            'ratePer100TakeOff' => ['type' => 'average_all', 'format' => 'number'],
            'technicalIncidentRate' => ['type' => 'average_all', 'format' => 'number'],
            'dispatchReliability' => ['type' => 'average_all', 'format' => 'number']
        ];

        foreach ($metrics as $metric => $config) {
            $total = 0;
            $validCount = 0;
            $monthlyValues = [];

            for ($i = 11; $i >= 0; $i--) {
                $monthKey = Carbon::parse($period)->subMonth($i)->format('Y-m');
                $value = $reportData[$monthKey][$metric] ?? 0;

                $monthlyValues[] = $value;
                $total += $value;

                // Untuk A/C In Fleet, count all months (including zero values) for proper average
                if ($metric === 'acInFleet') {
                    $validCount++;
                } else if ($value > 0) {
                    $validCount++;
                }
            }

            // Calculate based on metric type
            switch ($config['type']) {
                case 'average_valid':
                    // Untuk A/C In Fleet, use all 12 months for average calculation
                    if ($metric === 'acInFleet') {
                        $result = $total / 12;
                    } else {
                        $result = $validCount > 0 ? $total / $validCount : 0;
                    }
                    break;
                case 'sum':
                    $result = $total;
                    break;
                default: // 'average_all'
                    $result = $total / 12;
                    break;
            }

            $averages[$metric] = [
                'value' => $result,
                'total' => $total,
                'valid_months' => $validCount,
                'monthly_values' => $monthlyValues,
                'format' => $config['format']
            ];
        }

        return $averages;
    }

    // ✅ REFACTORED: AOS Data mengikuti struktur ReportController
    private function getAosData($aircraftType, $period)
    {
        // Ambil semua data dengan queries terpisah
        $data = $this->getAosReportData($aircraftType, $period);

        $reportData = [];
        $totalFlightHoursPerTakeOffTotal = 0;
        $totalRevenueFlightHoursPerTakeOff = 0;
        $totalDailyUtilizationFlyingHoursTotal = 0;
        $totalRevenueDailyUtilizationFlyingHoursTotal = 0;
        $totalTotalDuration = 0;
        $totalAverageDuration = 0;

        // Process data untuk setiap bulan
        for ($i = 11; $i >= 0; $i--) {
            $currentPeriod = Carbon::parse($period)->subMonth($i)->format('Y-m');
            $month = (int)substr($currentPeriod, 5, 2);
            $year = (int)substr($currentPeriod, 0, 4);

            // Ambil data dari hasil query yang terpisah
            $acInFleetOnly = $data['acInFleetData'][$currentPeriod] ?? null; // Data A/C In Fleet (exclude remark "out")
            $monthly = $data['monthlyData'][$currentPeriod] ?? null; // Data lainnya (semua data)
            $techDelay = $data['technicalDelayData'][$currentPeriod] ?? null;
            $techIncident = $data['technicalIncidentData'][$currentPeriod] ?? null;
            $techCancellation = $data['technicalCancellationData'][$currentPeriod] ?? null;

            if (!$monthly) {
                // Set default values jika tidak ada data
                $reportData[$currentPeriod] = $this->getDefaultReportData();
                continue;
            }

            // Gunakan data A/C In Fleet yang sudah exclude remark "out"
            $acInFleet = $acInFleetOnly ? $acInFleetOnly->ac_in_fleet_only : 0;

            // Hitung metrics berdasarkan data yang sudah diambil (semua data untuk metrics selain A/C In Fleet)
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $acInService = $daysInMonth > 0 ? $this->formatRate($monthly->days_in_service / $daysInMonth) : 0;

            // Calculations based on existing data (semua data)
            $flightHoursPerTakeOffTotal = $monthly->take_off_total > 0 ?
                $monthly->flying_hours_total / $monthly->take_off_total : 0;

            $revenueFlightHoursPerTakeOff = $monthly->revenue_take_off > 0 ?
                $monthly->revenue_flying_hours / $monthly->revenue_take_off : 0;

            $dailyUtilizationFlyingHoursTotal = $monthly->days_in_service > 0 ?
                $monthly->flying_hours_total / $monthly->days_in_service : 0;

            $revenueDailyUtilizationFlyingHoursTotal = $monthly->days_in_service > 0 ?
                $monthly->revenue_flying_hours / $monthly->days_in_service : 0;

            $dailyUtilizationTakeOffTotal = $monthly->days_in_service > 0 ?
                $monthly->take_off_total / $monthly->days_in_service : 0;

            $revenueDailyUtilizationTakeOffTotal = $monthly->days_in_service > 0 ?
                $monthly->revenue_take_off / $monthly->days_in_service : 0;

            // Technical data with null checks
            $technicalDelayTotal = $techDelay->technical_delay_total ?? 0;
            $totalDuration = $techDelay->total_duration ?? 0;
            $averageDuration = $technicalDelayTotal > 0 ? $totalDuration / $technicalDelayTotal : 0;
            $ratePer100TakeOff = $monthly->revenue_take_off > 0 ?
                $this->formatRate(($technicalDelayTotal * 100) / $monthly->revenue_take_off) : 0;

            $technicalIncidentTotal = $techIncident->technical_incident_total ?? 0;
            $technicalIncidentRate = $monthly->revenue_take_off > 0 ?
                $this->formatRate(($technicalIncidentTotal * 100) / $monthly->revenue_take_off) : 0;

            $technicalCancellationTotal = $techCancellation->technical_cancellation_total ?? 0;
            $dispatchReliability = $monthly->revenue_take_off > 0 ?
                $this->formatRate((($monthly->revenue_take_off - $technicalDelayTotal - $technicalCancellationTotal)
                / $monthly->revenue_take_off) * 100) : 0;

            $reportData[$currentPeriod] = [
                'acInFleet' => $acInFleet, // Menggunakan data yang exclude remark "out"
                'acInService' => $acInService, // Menggunakan semua data
                'daysInService' => $monthly->days_in_service, // Menggunakan semua data
                'flyingHoursTotal' => $monthly->flying_hours_total, // Menggunakan semua data
                'revenueFlyingHours' => $monthly->revenue_flying_hours, // Menggunakan semua data
                'takeOffTotal' => $monthly->take_off_total, // Menggunakan semua data
                'revenueTakeOff' => $monthly->revenue_take_off, // Menggunakan semua data
                'flightHoursPerTakeOffTotal' => $this->convertDecimalToHoursMinutes($flightHoursPerTakeOffTotal),
                'revenueFlightHoursPerTakeOff' => $this->convertDecimalToHoursMinutes($revenueFlightHoursPerTakeOff),
                'dailyUtilizationFlyingHoursTotal' => $this->convertDecimalToHoursMinutes($dailyUtilizationFlyingHoursTotal),
                'revenueDailyUtilizationFlyingHoursTotal' => $this->convertDecimalToHoursMinutes($revenueDailyUtilizationFlyingHoursTotal),
                'dailyUtilizationTakeOffTotal' => $dailyUtilizationTakeOffTotal,
                'revenueDailyUtilizationTakeOffTotal' => $revenueDailyUtilizationTakeOffTotal,
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

        // Calculate averages using new method
        $averages = $this->calculateAverages($reportData, $period);

        // Keep existing averages for backward compatibility
        $avgFlightHoursPerTakeOffTotal = $this->convertDecimalToHoursMinutes($totalFlightHoursPerTakeOffTotal / 12);
        $avgRevenueFlightHoursPerTakeOff = $this->convertDecimalToHoursMinutes($totalRevenueFlightHoursPerTakeOff / 12);
        $avgDailyUtilizationFlyingHoursTotal = $this->convertDecimalToHoursMinutes($totalDailyUtilizationFlyingHoursTotal / 12);
        $avgRevenueDailyUtilizationFlyingHoursTotal = $this->convertDecimalToHoursMinutes($totalRevenueDailyUtilizationFlyingHoursTotal / 12);
        $avgTotalDuration = $this->convertDecimalToHoursMinutes($totalTotalDuration / 12);
        $avgAverageDuration = $this->convertDecimalToHoursMinutes($totalAverageDuration / 12);

        return [
            'reportData' => $reportData,
            'averages' => $averages,
            'avgFlightHoursPerTakeOffTotal' => $avgFlightHoursPerTakeOffTotal,
            'avgRevenueFlightHoursPerTakeOff' => $avgRevenueFlightHoursPerTakeOff,
            'avgDailyUtilizationFlyingHoursTotal' => $avgDailyUtilizationFlyingHoursTotal,
            'avgRevenueDailyUtilizationFlyingHoursTotal' => $avgRevenueDailyUtilizationFlyingHoursTotal,
            'avgTotalDuration' => $avgTotalDuration,
            'avgAverageDuration' => $avgAverageDuration
        ];
    }

    // ✅ MODIFIED: Pilot Data method - sekarang menggunakan TblMasterac untuk aircraft type
    private function getPilotData($aircraftType, $period, $request = null)
    {
        $endDate = Carbon::parse($period)->endOfMonth();
        $startDate = Carbon::parse($period)->subMonths(11)->startOfMonth();

        // Helper function untuk format rate
        $formatRate = function($value) {
            return (float) number_format($value, 10, '.', '');
        };

        // ✅ SINGLE QUERY untuk semua Flying Hours (12 months) - Proper GROUP BY
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

        // ✅ SINGLE QUERY untuk semua PIREP data (12 months)
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

        // ✅ SINGLE QUERY untuk semua MAREP data (12 months)
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

        // ✅ SINGLE QUERY untuk semua DELAY data (12 months)
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
        if (!is_numeric($decimalHours)) {
            return '0 : 00';
        }
        $hours = floor($decimalHours);
        $minutes = round(($decimalHours - $hours) * 60);
        return sprintf('%d : %02d', $hours, $minutes);
    }
}
