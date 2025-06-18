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
        
        // Ambil data untuk dropdown
        $aircraftTypes = TblMasterac::select('ACType')->distinct()->get();
        
        // Ambil dan format data periode
        $periods = TblMonthlyfhfc::select('MonthEval')->distinct()->orderByDesc('MonthEval')->get()->map(function($item) {
            return [
                'formatted' => Carbon::parse($item->MonthEval)->format('Y-m'), // Format menjadi yyyy-mm
                'original' => $item->MonthEval
            ];
        });

        return view('report.aos-content', compact('aircraftTypes', 'operators', 'periods'));
    }

    // Function for button filter operator and actype in AOS
    public function getAircraftTypes(Request $request)
    {
        $operator = $request->input('operator');

        if (!$operator) {
            return response()->json([], 400);
        }

        // Query data ACType berdasarkan operator
        $aircraftTypes = TblMasterac::where('Operator', $operator)
            ->select('ACType')
            ->distinct()
            ->get();

        return response()->json($aircraftTypes);
    }

    // ✅ ADDED: Helper function untuk format number - FIXED ERROR
    private function formatNumber($value, $decimals = 2)
    {
        if (!is_numeric($value)) {
            return '0';
        }
        return rtrim(rtrim(number_format($value, $decimals, '.', ''), '0'), '.');
    }

    // ✅ ADDED: Helper function untuk format rate dengan precision tinggi
    private function formatRate($value, $decimals = 10)
    {
        if (!is_numeric($value)) {
            return 0;
        }
        return (float) number_format($value, $decimals, '.', '');
    }

    // ✅ ADDED: Global helper function untuk digunakan di view
    public static function formatNumberGlobal($value, $decimals = 2)
    {
        if (!is_numeric($value)) {
            return '0';
        }
        return rtrim(rtrim(number_format($value, $decimals, '.', ''), '0'), '.');
    }

    // NEW METHOD: Get Metrics Configuration
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

    // NEW METHOD: Get Hour Metrics Configuration
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

    // Private method untuk mengambil semua data dengan single queries
    private function getAosReportData($aircraftType, $period)
    {
        $endDate = Carbon::parse($period)->endOfMonth();
        $startDate = Carbon::parse($period)->subMonths(11)->startOfMonth();
        
        // ✅ FIXED: Single query untuk semua data TblMonthlyfhfc dengan proper GROUP BY
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
            ->groupBy(DB::raw('YEAR(MonthEval)'), DB::raw('MONTH(MonthEval)'))
            ->get()
            ->keyBy(function($item) {
                return $item->year . '-' . sprintf('%02d', $item->month);
            });

        // ✅ FIXED: Single query untuk data technical delay dengan proper GROUP BY
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

        // ✅ FIXED: Single query untuk data technical incident dengan proper GROUP BY
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

        // ✅ FIXED: Single query untuk data technical cancellation dengan proper GROUP BY
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

        return compact('monthlyData', 'technicalDelayData', 'technicalIncidentData', 'technicalCancellationData');
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

    // Calculate flexible averages untuk mengatasi masalah perhitungan
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
                
                if ($value > 0) {
                    $validCount++;
                }
            }
            
            // Calculate based on metric type
            switch ($config['type']) {
                case 'average_valid':
                    $result = $validCount > 0 ? $total / $validCount : 0;
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

    // Get monthly data safely dengan null handling
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

    // Calculate safe average dengan berbagai opsi
    private function calculateSafeAverage($values, $type = 'all')
    {
        if (empty($values)) return 0;
        
        switch ($type) {
            case 'valid_only':
                $validValues = array_filter($values, function($v) { return $v > 0; });
                return count($validValues) > 0 ? array_sum($validValues) / count($validValues) : 0;
            case 'sum':
                return array_sum($values);
            default: // 'all'
                return array_sum($values) / count($values);
        }
    }

    // Format nilai berdasarkan tipe
    private function formatValue($value, $format = 'number', $decimals = 2)
    {
        switch ($format) {
            case 'round':
                return round($value);
            case 'hours':
                return $this->convertDecimalToHoursMinutes($value);
            default: // 'number'
                return $this->formatNumber($value, $decimals);
        }
    }

    // Process report data dengan enhanced calculations
    private function processReportData($aircraftType, $period)
    {
        // Ambil semua data dengan single queries
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
            
            // Ambil data dari hasil query (tanpa query tambahan)
            $monthly = $data['monthlyData'][$currentPeriod] ?? null;
            $techDelay = $data['technicalDelayData'][$currentPeriod] ?? null;
            $techIncident = $data['technicalIncidentData'][$currentPeriod] ?? null;
            $techCancellation = $data['technicalCancellationData'][$currentPeriod] ?? null;

            if (!$monthly) {
                // Set default values jika tidak ada data
                $reportData[$currentPeriod] = $this->getDefaultReportData();
                continue;
            }

            // Hitung metrics berdasarkan data yang sudah diambil
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $acInService = $daysInMonth > 0 ? $this->formatRate($monthly->days_in_service / $daysInMonth) : 0;

            // Calculations based on existing data
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

    public function aosStore(Request $request)
    {
        // Validate input
        $request->validate([
            'period' => 'required',
            'aircraft_type' => 'required',
        ]);

        $aircraftType = $request->aircraft_type;
        $period = $request->period;
        $month = date('m', strtotime($period));
        $year = date('Y', strtotime($period));

        // Process report data using optimized method
        $processedData = $this->processReportData($aircraftType, $period);

        // Extract data from processed result
        extract($processedData);

        // Get configurations
        $metricsConfig = $this->getMetricsConfiguration();
        $hourMetricsConfig = $this->getHourMetricsConfiguration();

        // ✅ ADDED: Helper function untuk view
        $formatNumber = function($value, $decimals = 2) {
            if (!is_numeric($value)) {
                return '0';
            }
            return rtrim(rtrim(number_format($value, $decimals, '.', ''), '0'), '.');
        };

        // Return view with processed data
        return view('report.aos-result', compact(
            'reportData', 'period', 'aircraftType', 'month', 'year',
            'averages', 'metricsConfig', 'hourMetricsConfig',
            'avgFlightHoursPerTakeOffTotal', 'avgRevenueFlightHoursPerTakeOff', 
            'avgDailyUtilizationFlyingHoursTotal', 'avgRevenueDailyUtilizationFlyingHoursTotal',
            'avgTotalDuration', 'avgAverageDuration', 'formatNumber'
        ));
    }

    // Untuk convert format menjadi (HH : MM)
    private function convertDecimalToHoursMinutes($decimalHours) 
    {
        if (!is_numeric($decimalHours)) {
            return '0 : 00';
        }
        $hours = floor($decimalHours);
        $minutes = round(($decimalHours - $hours) * 60);
        return sprintf('%d : %02d', $hours, $minutes);
    }

    // Export PDF dengan enhanced data
    public function aosPdf(Request $request)
    {
        // Validate input
        $request->validate([
            'period' => 'required',
            'aircraft_type' => 'required',
        ]);

        $aircraftType = $request->aircraft_type;
        $period = $request->period;
        $month = date('m', strtotime($period));
        $year = date('Y', strtotime($period));

        // Process report data using optimized method
        $processedData = $this->processReportData($aircraftType, $period);

        // Extract data from processed result
        extract($processedData);

        // Get configurations
        $metricsConfig = $this->getMetricsConfiguration();
        $hourMetricsConfig = $this->getHourMetricsConfiguration();

        // ✅ ADDED: Helper function untuk format number dalam PDF
        $formatNumber = function($value, $decimals = 2) {
            if (!is_numeric($value)) {
                return '0';
            }
            return rtrim(rtrim(number_format($value, $decimals, '.', ''), '0'), '.');
        };

        // Generate PDF
        $pdf = PDF::loadView('pdf.aos-pdf', compact(
            'reportData', 'period', 'aircraftType', 'month', 'year',
            'averages', 'metricsConfig', 'hourMetricsConfig',
            'avgFlightHoursPerTakeOffTotal', 'avgRevenueFlightHoursPerTakeOff', 
            'avgDailyUtilizationFlyingHoursTotal', 'avgRevenueDailyUtilizationFlyingHoursTotal',
            'avgTotalDuration', 'avgAverageDuration', 'formatNumber'
        ));

        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('AOS-Report-' . $year . '-' . $month . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'period' => 'required',
            'aircraft_type' => 'required',
        ]);

        $period = $request->period;
        $aircraftType = $request->aircraft_type;

        // Process report data using optimized method
        $processedData = $this->processReportData($aircraftType, $period);
        $reportData = $processedData['reportData'];

        return Excel::download(new AosExport($reportData, $period, $aircraftType), 'AOS-Report-' . substr($period, 0, 7) . '.xlsx');
    }
    
    public function cumulativeContent()
    {
        return view('report.cumulative-content');
    }
}