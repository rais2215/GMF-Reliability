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
use App\Exports\AosExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function aosIndex()
    {
        $operators = TblMasterac::select('Operator')->distinct()->get();
        $aircraftTypes = TblMasterac::select('ACType')->distinct()->get();
        $periods = TblMonthlyfhfc::select('MonthEval')->distinct()->orderByDesc('MonthEval')->get()->map(function($item) {
            return [
                'formatted' => Carbon::parse($item->MonthEval)->format('Y-m'),
                'original' => $item->MonthEval
            ];
        });

        return view('report.aos-content', compact('aircraftTypes', 'operators', 'periods'));
    }

    public function getAircraftTypes(Request $request)
    {
        $operator = $request->input('operator');
        if (!$operator) {
            return response()->json([], 400);
        }
        $aircraftTypes = TblMasterac::where('Operator', $operator)
            ->select('ACType')
            ->distinct()
            ->get();
        return response()->json($aircraftTypes);
    }

    private function formatNumber($value, $decimals = 2)
    {
        if (!is_numeric($value)) {
            return '0';
        }
        return rtrim(rtrim(number_format($value, $decimals, '.', ''), '0'), '.');
    }

    private function formatRate($value, $decimals = 10)
    {
        if (!is_numeric($value)) {
            return 0;
        }
        return (float) number_format($value, $decimals, '.', '');
    }

    public static function formatNumberGlobal($value, $decimals = 2)
    {
        if (!is_numeric($value)) {
            return '0';
        }
        return rtrim(rtrim(number_format($value, $decimals, '.', ''), '0'), '.');
    }

    private function getMetricsConfiguration()
    {
        return [
            'acInFleet' => [
                'label' => 'A/C in Fleet',
                'format' => 'round',
                'average_type' => 'valid_only',
                'use_enhanced_calc' => true
            ],
            'acInService' => [
                'label' => 'A/C in Service (Revenue)',
                'format' => 'number',
                'average_type' => 'all_months',
                'use_enhanced_calc' => false
            ],
            'daysInService' => [
                'label' => 'A/C Days in Service (Revenue)',
                'format' => 'round',
                'average_type' => 'sum',
                'use_enhanced_calc' => false
            ],
            'flyingHoursTotal' => [
                'label' => 'Flying Hours - Total',
                'format' => 'round',
                'average_type' => 'sum',
                'use_enhanced_calc' => false
            ],
            'revenueFlyingHours' => [
                'label' => '- Revenue',
                'format' => 'round',
                'average_type' => 'sum',
                'use_enhanced_calc' => false
            ],
            'takeOffTotal' => [
                'label' => 'Take-off - Total',
                'format' => 'round',
                'average_type' => 'sum',
                'use_enhanced_calc' => false
            ],
            'revenueTakeOff' => [
                'label' => '- Revenue',
                'format' => 'round',
                'average_type' => 'sum',
                'use_enhanced_calc' => false
            ],
            'dailyUtilizationTakeOffTotal' => [
                'label' => 'Daily Utilization Take-off - Total',
                'format' => 'number',
                'average_type' => 'all_months',
                'use_enhanced_calc' => false
            ],
            'revenueDailyUtilizationTakeOffTotal' => [
                'label' => '- Revenue',
                'format' => 'number',
                'average_type' => 'all_months',
                'use_enhanced_calc' => false
            ],
            'technicalDelayTotal' => [
                'label' => 'Technical Delay - Total',
                'format' => 'round',
                'average_type' => 'sum',
                'use_enhanced_calc' => false
            ],
            'ratePer100TakeOff' => [
                'label' => '- Rate/100 Take-Off',
                'format' => 'number',
                'average_type' => 'all_months',
                'use_enhanced_calc' => false
            ],
            'technicalIncidentTotal' => [
                'label' => 'Technical Incident - Total',
                'format' => 'round',
                'average_type' => 'sum',
                'use_enhanced_calc' => false
            ],
            'technicalIncidentRate' => [
                'label' => '- Rate / 100 FC',
                'format' => 'number',
                'average_type' => 'all_months',
                'use_enhanced_calc' => false
            ],
            'technicalCancellationTotal' => [
                'label' => 'Technical Cancellation - Total',
                'format' => 'round',
                'average_type' => 'sum',
                'use_enhanced_calc' => false
            ],
            'dispatchReliability' => [
                'label' => 'Dispatch Reliability (%)',
                'format' => 'number',
                'average_type' => 'all_months',
                'use_enhanced_calc' => false
            ]
        ];
    }

    private function getHourMetricsConfiguration()
    {
        return [
            'flightHoursPerTakeOffTotal' => [
                'label' => 'Flight Hours/Take-off - Total',
                'average_var' => 'avgFlightHoursPerTakeOffTotal'
            ],
            'revenueFlightHoursPerTakeOff' => [
                'label' => '- Revenue',
                'average_var' => 'avgRevenueFlightHoursPerTakeOff'
            ],
            'dailyUtilizationFlyingHoursTotal' => [
                'label' => 'Daily Utilization Flying Hours - Total',
                'average_var' => 'avgDailyUtilizationFlyingHoursTotal'
            ],
            'revenueDailyUtilizationFlyingHoursTotal' => [
                'label' => '- Revenue',
                'average_var' => 'avgRevenueDailyUtilizationFlyingHoursTotal'
            ],
            'totalDuration' => [
                'label' => '- Total Duration',
                'average_var' => 'avgTotalDuration'
            ],
            'averageDuration' => [
                'label' => '- Avg Duration',
                'average_var' => 'avgAverageDuration'
            ]
        ];
    }

    /**
     * Menentukan tahun patokan berdasarkan periode data yang ditampilkan
     */
    private function determineBaseYear($period)
    {
        $endYear = Carbon::parse($period)->format('Y');
        $startYear = Carbon::parse($period)->subMonth(11)->format('Y');

        // Jika periode mencakup 2 tahun, gunakan tahun awal
        // Jika periode dalam 1 tahun, gunakan tahun tersebut
        return $startYear;
    }

    // Ambil data berdasarkan tahun kalender
    private function getAosReportDataByYear($aircraftType, $year)
    {
        $startDate = Carbon::create($year, 1, 1)->startOfDay();
        $endDate = Carbon::create($year, 12, 31)->endOfDay();

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

    // Proses data berdasarkan tahun kalender
    private function processReportDataByYear($aircraftType, $year)
    {
        $data = $this->getAosReportDataByYear($aircraftType, $year);

        $reportData = [];
        $totalFlyingHoursForAvg = 0;
        $totalTakeOffForAvg = 0;
        $totalRevenueFlyingHoursForAvg = 0;
        $totalRevenueTakeOffForAvg = 0;
        $totalDaysInServiceForAvg = 0;
        $totalDurationForAvg = 0;
        $totalTechnicalDelayForAvg = 0;

        for ($i = 1; $i <= 12; $i++) {
            $currentPeriod = sprintf('%d-%02d', $year, $i);

            $acInFleetOnly = $data['acInFleetData'][$currentPeriod] ?? null;
            $monthly = $data['monthlyData'][$currentPeriod] ?? null;
            $techDelay = $data['technicalDelayData'][$currentPeriod] ?? null;
            $techIncident = $data['technicalIncidentData'][$currentPeriod] ?? null;
            $techCancellation = $data['technicalCancellationData'][$currentPeriod] ?? null;

            if (!$monthly) {
                $reportData[$currentPeriod] = $this->getDefaultReportData();
                continue;
            }

            // Akumulasi total untuk perhitungan rata-rata yang akurat
            $totalFlyingHoursForAvg += $monthly->flying_hours_total;
            $totalTakeOffForAvg += $monthly->take_off_total;
            $totalRevenueFlyingHoursForAvg += $monthly->revenue_flying_hours;
            $totalRevenueTakeOffForAvg += $monthly->revenue_take_off;
            $totalDaysInServiceForAvg += $monthly->days_in_service;

            $acInFleet = $acInFleetOnly ? $acInFleetOnly->ac_in_fleet_only : 0;
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $i, $year);
            $acInService = $daysInMonth > 0 ? $this->formatRate($monthly->days_in_service / $daysInMonth) : 0;

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

            $technicalDelayTotal = $techDelay->technical_delay_total ?? 0;
            $totalDuration = $techDelay->total_duration ?? 0;

            // Akumulasi total untuk durasi delay
            $totalDurationForAvg += $totalDuration;
            $totalTechnicalDelayForAvg += $technicalDelayTotal;

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
                'acInFleet' => $acInFleet,
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
        }

        $averages = $this->calculateAverages($reportData, "$year-12");

        // Hitung rata-rata berbasis rasio dari total, bukan dari rata-rata bulanan
        $avgFlightHoursPerTakeOffTotal = $totalTakeOffForAvg > 0 ? $this->convertDecimalToHoursMinutes($totalFlyingHoursForAvg / $totalTakeOffForAvg) : '0 : 00';
        $avgRevenueFlightHoursPerTakeOff = $totalRevenueTakeOffForAvg > 0 ? $this->convertDecimalToHoursMinutes($totalRevenueFlyingHoursForAvg / $totalRevenueTakeOffForAvg) : '0 : 00';
        $avgDailyUtilizationFlyingHoursTotal = $totalDaysInServiceForAvg > 0 ? $this->convertDecimalToHoursMinutes($totalFlyingHoursForAvg / $totalDaysInServiceForAvg) : '0 : 00';
        $avgRevenueDailyUtilizationFlyingHoursTotal = $totalDaysInServiceForAvg > 0 ? $this->convertDecimalToHoursMinutes($totalRevenueFlyingHoursForAvg / $totalDaysInServiceForAvg) : '0 : 00';
        $avgTotalDuration = $this->convertDecimalToHoursMinutes($totalDurationForAvg);
        $avgAverageDuration = $totalTechnicalDelayForAvg > 0 ? $this->convertDecimalToHoursMinutes($totalDurationForAvg / $totalTechnicalDelayForAvg) : '0 : 00';

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

    // Fungsi default (rolling 12 bulan terakhir)
    private function getAosReportData($aircraftType, $period)
    {
        $endDate = Carbon::parse($period)->endOfMonth();
        $startDate = Carbon::parse($period)->subMonths(11)->startOfMonth();

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
                if ($metric === 'acInFleet') {
                    $validCount++;
                } else if ($value > 0) {
                    $validCount++;
                }
            }
            switch ($config['type']) {
            case 'average_valid':
                if ($metric === 'acInFleet') {
                    $result = round($total / 12); // Tambahkan round() untuk membulatkan
                } else {
                    $result = $validCount > 0 ? $total / $validCount : 0;
                }
                break;
            case 'average_all':
                // Untuk acInService, gunakan rata-rata dari bulan yang valid
                $validValues = array_filter($monthlyValues, function($v) { return $v > 0; });
                $result = count($validValues) > 0 ? array_sum($validValues) / count($validValues) : $total / 12;
                break;
            case 'sum':
                $result = $total;
                break;
            default:
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

    private function getMonthlyData($reportData, $period, $metric, $monthsBack = 12)
    {
        $data = [];
        for ($i = $monthsBack - 1; $i >= 0; $i--) {
            $monthKey = Carbon::parse($period)->subMonth($i)->format('Y-m');
            $value = $reportData[$monthKey][$metric] ?? 0;
            $data[] = $value;
        }
        return $data;
    }

    private function calculateSafeAverage($values, $type = 'all')
    {
        if (empty($values)) return 0;
        switch ($type) {
            case 'valid_only':
                $validValues = array_filter($values, function($v) { return $v > 0; });
                return count($validValues) > 0 ? array_sum($validValues) / count($validValues) : 0;
            case 'sum':
                return array_sum($values);
            default:
                return array_sum($values) / count($values);
        }
    }

    private function formatValue($value, $format = 'number', $decimals = 2)
    {
        switch ($format) {
            case 'round':
                return round($value);
            case 'hours':
                return $this->convertDecimalToHoursMinutes($value);
            default:
                return $this->formatNumber($value, $decimals);
        }
    }

    // Rolling 12 bulan terakhir (untuk Last 12 Months)
    private function processReportData($aircraftType, $period)
    {
        $data = $this->getAosReportData($aircraftType, $period);

        $reportData = [];
        $totalFlyingHoursForAvg = 0;
        $totalTakeOffForAvg = 0;
        $totalRevenueFlyingHoursForAvg = 0;
        $totalRevenueTakeOffForAvg = 0;
        $totalDaysInServiceForAvg = 0;
        $totalDurationForAvg = 0;
        $totalTechnicalDelayForAvg = 0;

        for ($i = 11; $i >= 0; $i--) {
            $currentPeriod = Carbon::parse($period)->subMonth($i)->format('Y-m');
            $month = (int)substr($currentPeriod, 5, 2);
            $year = (int)substr($currentPeriod, 0, 4);

            $acInFleetOnly = $data['acInFleetData'][$currentPeriod] ?? null;
            $monthly = $data['monthlyData'][$currentPeriod] ?? null;
            $techDelay = $data['technicalDelayData'][$currentPeriod] ?? null;
            $techIncident = $data['technicalIncidentData'][$currentPeriod] ?? null;
            $techCancellation = $data['technicalCancellationData'][$currentPeriod] ?? null;

            if (!$monthly) {
                $reportData[$currentPeriod] = $this->getDefaultReportData();
                continue;
            }

            // Akumulasi total untuk perhitungan rata-rata yang akurat
            $totalFlyingHoursForAvg += $monthly->flying_hours_total;
            $totalTakeOffForAvg += $monthly->take_off_total;
            $totalRevenueFlyingHoursForAvg += $monthly->revenue_flying_hours;
            $totalRevenueTakeOffForAvg += $monthly->revenue_take_off;
            $totalDaysInServiceForAvg += $monthly->days_in_service;

            $acInFleet = $acInFleetOnly ? $acInFleetOnly->ac_in_fleet_only : 0;
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $acInService = $daysInMonth > 0 ? $this->formatRate($monthly->days_in_service / $daysInMonth) : 0;

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

            $technicalDelayTotal = $techDelay->technical_delay_total ?? 0;
            $totalDuration = $techDelay->total_duration ?? 0;

            // Akumulasi total untuk durasi delay
            $totalDurationForAvg += $totalDuration;
            $totalTechnicalDelayForAvg += $technicalDelayTotal;

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
                'acInFleet' => $acInFleet,
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
        }

        $averages = $this->calculateAverages($reportData, $period);

        // Hitung rata-rata berbasis rasio dari total, bukan dari rata-rata bulanan
        $avgFlightHoursPerTakeOffTotal = $totalTakeOffForAvg > 0 ? $this->convertDecimalToHoursMinutes($totalFlyingHoursForAvg / $totalTakeOffForAvg) : '0 : 00';
        $avgRevenueFlightHoursPerTakeOff = $totalRevenueTakeOffForAvg > 0 ? $this->convertDecimalToHoursMinutes($totalRevenueFlyingHoursForAvg / $totalRevenueTakeOffForAvg) : '0 : 00';
        $avgDailyUtilizationFlyingHoursTotal = $totalDaysInServiceForAvg > 0 ? $this->convertDecimalToHoursMinutes($totalFlyingHoursForAvg / $totalDaysInServiceForAvg) : '0 : 00';
        $avgRevenueDailyUtilizationFlyingHoursTotal = $totalDaysInServiceForAvg > 0 ? $this->convertDecimalToHoursMinutes($totalRevenueFlyingHoursForAvg / $totalDaysInServiceForAvg) : '0 : 00';
        $avgTotalDuration = $this->convertDecimalToHoursMinutes($totalDurationForAvg);
        $avgAverageDuration = $totalTechnicalDelayForAvg > 0 ? $this->convertDecimalToHoursMinutes($totalDurationForAvg / $totalTechnicalDelayForAvg) : '0 : 00';

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

    private function convertDecimalToHoursMinutes($decimalHours)
    {
        if (!is_numeric($decimalHours)) {
            return '0 : 00';
        }
        $hours = floor($decimalHours);
        $minutes = round(($decimalHours - $hours) * 60);
        return sprintf('%d : %02d', $hours, $minutes);
    }

    public function aosStore(Request $request)
    {
        $request->validate([
            'period' => 'required',
            'aircraft_type' => 'required',
        ]);

        $aircraftType = $request->aircraft_type;
        $period = $request->period;
        $month = date('m', strtotime($period));
        $year = date('Y', strtotime($period));

        // Tentukan tahun patokan berdasarkan periode data
        $baseYear = $this->determineBaseYear($period);

        // Data rolling 12 bulan terakhir (Last 12 Months)
        $processedData = $this->processReportData($aircraftType, $period);

        // Generate data untuk tahun patokan
        $yearData = $this->processReportDataByYear($aircraftType, $baseYear);

        // Data tambahan untuk tahun lain (untuk fleksibilitas)
        $data2016 = null;
        $data2017 = null;

        // Hanya generate data tambahan jika diperlukan
        if ($baseYear != 2016 && ($year == 2016 || $baseYear == 2015)) {
            $data2016 = $this->processReportDataByYear($aircraftType, 2016);
        }
        if ($baseYear != 2017 && ($year == 2017 || $baseYear == 2016)) {
            $data2017 = $this->processReportDataByYear($aircraftType, 2017);
        }

        extract($processedData);

        $metricsConfig = $this->getMetricsConfiguration();
        $hourMetricsConfig = $this->getHourMetricsConfiguration();

        $formatNumber = function($value, $decimals = 2) {
            if (!is_numeric($value)) {
                return '0';
            }
            return rtrim(rtrim(number_format($value, $decimals, '.', ''), '0'), '.');
        };

        return view('report.aos-result', compact(
            'reportData', 'period', 'aircraftType', 'month', 'year', 'baseYear',
            'averages', 'metricsConfig', 'hourMetricsConfig',
            'avgFlightHoursPerTakeOffTotal', 'avgRevenueFlightHoursPerTakeOff',
            'avgDailyUtilizationFlyingHoursTotal', 'avgRevenueDailyUtilizationFlyingHoursTotal',
            'avgTotalDuration', 'avgAverageDuration', 'formatNumber',
            'yearData', 'data2016', 'data2017'
        ));
    }

    public function aosPdf(Request $request)
    {
        $request->validate([
            'period' => 'required',
            'aircraft_type' => 'required',
        ]);

        $aircraftType = $request->aircraft_type;
        $period = $request->period;
        $month = date('m', strtotime($period));
        $year = date('Y', strtotime($period));

        // Tentukan tahun patokan berdasarkan periode data
        $baseYear = $this->determineBaseYear($period);

        $processedData = $this->processReportData($aircraftType, $period);

        // Generate data untuk tahun patokan
        $yearData = $this->processReportDataByYear($aircraftType, $baseYear);

        $data2016 = null;
        $data2017 = null;

        // Hanya generate data tambahan jika diperlukan
        if ($baseYear != 2016 && ($year == 2016 || $baseYear == 2015)) {
            $data2016 = $this->processReportDataByYear($aircraftType, 2016);
        }
        if ($baseYear != 2017 && ($year == 2017 || $baseYear == 2016)) {
            $data2017 = $this->processReportDataByYear($aircraftType, 2017);
        }

        extract($processedData);

        $metricsConfig = $this->getMetricsConfiguration();
        $hourMetricsConfig = $this->getHourMetricsConfiguration();

        $formatNumber = function($value, $decimals = 2) {
            if (!is_numeric($value)) {
                return '0';
            }
            return rtrim(rtrim(number_format($value, $decimals, '.', ''), '0'), '.');
        };

        $pdf = PDF::loadView('pdf.aos-pdf', compact(
            'reportData', 'period', 'aircraftType', 'month', 'year', 'baseYear',
            'averages', 'metricsConfig', 'hourMetricsConfig',
            'avgFlightHoursPerTakeOffTotal', 'avgRevenueFlightHoursPerTakeOff',
            'avgDailyUtilizationFlyingHoursTotal', 'avgRevenueDailyUtilizationFlyingHoursTotal',
            'avgTotalDuration', 'avgAverageDuration', 'formatNumber',
            'yearData', 'data2016', 'data2017'
        ));

        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('AOS-Report-' . $year . '-' . $month . '.pdf');
    }

    public function aosExcel(Request $request)
    {
        $request->validate([
            'period' => 'required',
            'aircraft_type' => 'required',
        ]);

        $period = $request->period;
        $aircraftType = $request->aircraft_type;
        $month = date('m', strtotime($period));
        $year = date('Y', strtotime($period));

        // Tentukan tahun patokan berdasarkan periode data
        $baseYear = $this->determineBaseYear($period);

        // Data rolling 12 bulan terakhir (Last 12 Months)
        $processedData = $this->processReportData($aircraftType, $period);

        // Generate data untuk tahun patokan
        $yearData = $this->processReportDataByYear($aircraftType, $baseYear);

        $data2016 = null;
        $data2017 = null;

        // Hanya generate data tambahan jika diperlukan
        if ($baseYear != 2016 && ($year == 2016 || $baseYear == 2015)) {
            $data2016 = $this->processReportDataByYear($aircraftType, 2016);
        }
        if ($baseYear != 2017 && ($year == 2017 || $baseYear == 2016)) {
            $data2017 = $this->processReportDataByYear($aircraftType, 2017);
        }

        return Excel::download(new AosExport(
            $processedData['reportData'],
            $period,
            $aircraftType,
            $processedData,   // Data untuk "Last 12 Months"
            $yearData,        // Data untuk tahun patokan
            $data2016,        // Data tahun 2016 (jika relevan)
            $data2017         // Data tahun 2017 (jika relevan)
        ), 'AOS-Report-' . substr($period, 0, 7) . '.xlsx');
    }
}
