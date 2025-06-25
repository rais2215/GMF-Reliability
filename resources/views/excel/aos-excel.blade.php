<table border="1" style="border-collapse: collapse; width: 100%; font-family: Arial, sans-serif;">
    <thead>
        <!-- Title Row -->
        <tr>
            <th colspan="15" style="text-align: center; font-weight: bold; background-color: #112955; color: white; padding: 10px; font-size: 16px;">
                AIRCRAFT ON SCHEDULE (AOS) REPORT
            </th>
        </tr>
        <!-- Aircraft Type Row -->
        <tr>
            <th colspan="15" style="text-align: center; font-weight: bold; background-color: #0066B3; color: white; padding: 8px; font-size: 14px;">
                Aircraft Type: {{ strtoupper($aircraftType) }}
            </th>
        </tr>
        <!-- Period Row -->
        <tr>
            <th colspan="15" style="text-align: center; font-weight: bold; background-color: #7EBB1A ; color: white; padding: 6px; font-size: 12px;">
                @php
                    $startYear = \Carbon\Carbon::parse($period)->subMonth(11)->format('Y');
                    $endYear = \Carbon\Carbon::parse($period)->format('Y');
                @endphp
                Period: {{ $startYear }} - {{ $endYear }}
            </th>
        </tr>
        <!-- Header Row dengan Start Year + 12 bulan + Last 12 Months -->
        <tr style="background-color: #4472C4; color: white; font-weight: bold; text-align: center;">
            <th style="padding: 8px; border: 1px solid #ccc; width: 200px; text-align: center; font-weight: bold;">Metrics</th>
            <th style="padding: 8px; border: 1px solid #ccc; width: 80px; text-align: center; font-weight: bold;">{{ $startYear }}</th>
            @for ($i = 11; $i >= 0; $i--)
            @php
                $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
            @endphp
            <th style="padding: 8px; border: 1px solid #ccc; width: 80px; text-align: center; font-weight: bold;">
                {{ \Carbon\Carbon::parse($monthKey)->format('M') }}
            </th>
            @endfor
            <th style="padding: 8px; border: 1px solid #ccc; width: 100px; text-align: center; font-weight: bold;">LAST 12 MTHS</th>
        </tr>
    </thead>
    <tbody>
        @php
            $formatNumber = function($value, $decimals = 2) {
                if (!is_numeric($value)) return '0';
                return rtrim(rtrim(number_format($value, $decimals, '.', ''), '0'), '.');
            };

            // Helper functions seperti di PDF
            $safeNumber = function($value, $default = 0) {
                if (is_null($value) || !is_numeric($value)) {
                    return $default;
                }
                return floatval($value);
            };

            $formatTime = function($value) {
                if (is_null($value) || $value === '' || $value === 0) {
                    return '00:00';
                }
                if (is_string($value) && strpos($value, ':') !== false) {
                    return $value;
                }
                if (is_numeric($value)) {
                    $hours = floor($value);
                    $minutes = round(($value - $hours) * 60);
                    return sprintf('%d:%02d', $hours, $minutes);
                }
                return $value ?: '00:00';
            };

            $timeToDecimal = function($timeString) {
                if (is_null($timeString) || $timeString === '' || $timeString === 0) {
                    return 0;
                }
                if (is_numeric($timeString)) {
                    return floatval($timeString);
                }
                if (is_string($timeString) && strpos($timeString, ':') !== false) {
                    $parts = explode(':', $timeString);
                    $hours = intval($parts[0]);
                    $minutes = isset($parts[1]) ? intval($parts[1]) : 0;
                    return $hours + ($minutes / 60);
                }
                return 0;
            };

            $calculateAvgTime = function($totalDecimal) use ($formatTime) {
                $avg = $totalDecimal / 12;
                return $formatTime($avg);
            };

            // Calculate totals for Start Year and Last 12 Months - sama seperti di PDF
            $totals = [];
            $metricsForCalculation = [
                'acInFleet', 'acInService', 'daysInService', 'flyingHoursTotal', 'revenueFlyingHours',
                'takeOffTotal', 'revenueTakeOff', 'flightHoursPerTakeOffTotal', 'revenueFlightHoursPerTakeOff',
                'dailyUtilizationFlyingHoursTotal', 'revenueDailyUtilizationFlyingHoursTotal',
                'dailyUtilizationTakeOffTotal', 'revenueDailyUtilizationTakeOffTotal',
                'technicalDelayTotal', 'totalDuration', 'averageDuration', 'ratePer100TakeOff',
                'technicalIncidentTotal', 'technicalIncidentRate', 'technicalCancellationTotal', 'dispatchReliability'
            ];

            foreach ($metricsForCalculation as $metric) {
                $totals[$metric] = 0;
                for ($j = 11; $j >= 0; $j--) {
                    $monthKey = \Carbon\Carbon::parse($period)->subMonth($j)->format('Y-m');
                    $monthData = $reportData[$monthKey] ?? [];
                    $value = $monthData[$metric] ?? 0;
                    
                    if (in_array($metric, ['flightHoursPerTakeOffTotal', 'revenueFlightHoursPerTakeOff', 'dailyUtilizationFlyingHoursTotal', 'revenueDailyUtilizationFlyingHoursTotal', 'totalDuration', 'averageDuration'])) {
                        $totals[$metric] += $timeToDecimal($value);
                    } else {
                        $totals[$metric] += $safeNumber($value);
                    }
                }
            }

            $metrics = [
                'acInFleet' => ['label' => 'A/C in Fleet', 'format' => 'average'],
                'acInService' => ['label' => 'A/C in Service (Revenue)', 'format' => 'average'],
                'daysInService' => ['label' => 'A/C Days in Service (Revenue)', 'format' => 'total'],
                'flyingHoursTotal' => ['label' => 'Flying Hours - Total', 'format' => 'total'],
                'revenueFlyingHours' => ['label' => '- Revenue', 'format' => 'total'],
                'takeOffTotal' => ['label' => 'Take-off - Total', 'format' => 'total'],
                'revenueTakeOff' => ['label' => '- Revenue', 'format' => 'total'],
                'flightHoursPerTakeOffTotal' => ['label' => 'Flight Hours/Take-off - Total', 'format' => 'time_avg'],
                'revenueFlightHoursPerTakeOff' => ['label' => '- Revenue', 'format' => 'time_avg'],
                'dailyUtilizationFlyingHoursTotal' => ['label' => 'Daily Utilization Flying Hours - Total', 'format' => 'time_avg'],
                'revenueDailyUtilizationFlyingHoursTotal' => ['label' => '- Revenue', 'format' => 'time_avg'],
                'dailyUtilizationTakeOffTotal' => ['label' => 'Daily Utilization Take-off - Total', 'format' => 'average'],
                'revenueDailyUtilizationTakeOffTotal' => ['label' => '- Revenue', 'format' => 'average'],
                'technicalDelayTotal' => ['label' => 'Technical Delay - Total', 'format' => 'total'],
                'totalDuration' => ['label' => '- Total Duration', 'format' => 'time_avg'],
                'averageDuration' => ['label' => '- Avg Duration', 'format' => 'time_avg'],
                'ratePer100TakeOff' => ['label' => '- Rate/100 Take-Off', 'format' => 'average'],
                'technicalIncidentTotal' => ['label' => 'Technical Incident - Total', 'format' => 'total'],
                'technicalIncidentRate' => ['label' => '- Rate / 100 FC', 'format' => 'average'],
                'technicalCancellationTotal' => ['label' => 'Technical Cancellation - Total', 'format' => 'total'],
                'dispatchReliability' => ['label' => 'Dispatch Reliability (%)', 'format' => 'percent'],
            ];
        @endphp

        @foreach($metrics as $metricKey => $metric)
            <tr style="{{ in_array($metricKey, ['acInFleet', 'flyingHoursTotal', 'takeOffTotal', 'flightHoursPerTakeOffTotal', 'dailyUtilizationFlyingHoursTotal', 'dailyUtilizationTakeOffTotal', 'technicalDelayTotal', 'technicalIncidentTotal', 'technicalCancellationTotal']) ? 'background-color: #F8F9FA;' : '' }}">
                <!-- Metrics Label -->
                <td style="padding: 8px; border: 1px solid #ccc; text-align: left; font-weight: {{ in_array($metricKey, ['acInFleet', 'flyingHoursTotal', 'takeOffTotal', 'flightHoursPerTakeOffTotal', 'dailyUtilizationFlyingHoursTotal', 'dailyUtilizationTakeOffTotal', 'technicalDelayTotal', 'technicalIncidentTotal', 'technicalCancellationTotal']) ? 'bold' : 'normal' }};">
                    {{ $metric['label'] }}
                </td>

                <!-- Start Year Column (sama dengan Last 12 Months) -->
                <td style="padding: 6px; border: 1px solid #ccc; text-align: center; background-color: white;">
                    @switch($metric['format'])
                        @case('average')
                            {{ $formatNumber($totals[$metricKey] / 12) }}
                            @break
                        @case('total')
                            {{ round($totals[$metricKey]) }}
                            @break
                        @case('time_avg')
                            {{ $calculateAvgTime($totals[$metricKey]) }}
                            @break
                        @case('percent')
                            {{ $formatNumber($totals[$metricKey] / 12) }}%
                            @break
                        @default
                            {{ $totals[$metricKey] }}
                    @endswitch
                </td>

                <!-- Data untuk 12 bulan -->
                @for ($i = 11; $i >= 0; $i--)
                    @php
                        $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                        $monthData = $reportData[$monthKey] ?? [];
                        $value = $monthData[$metricKey] ?? 0;
                        $isCurrentMonth = $monthKey === \Carbon\Carbon::parse($period)->format('Y-m');
                        $cellStyle = $isCurrentMonth ? 'background-color: white;' : '';
                        
                        // Format value berdasarkan tipe
                        switch($metric['format']) {
                            case 'average':
                            case 'total':
                                if ($metricKey === 'acInFleet' || strpos($metricKey, 'Total') !== false) {
                                    $displayValue = round($value);
                                } else {
                                    $displayValue = $formatNumber($value);
                                }
                                break;
                            case 'time_avg':
                                $displayValue = $formatTime($value);
                                break;
                            case 'percent':
                                $displayValue = $formatNumber($value) . '%';
                                break;
                            default:
                                $displayValue = $value;
                        }
                    @endphp
                    <td style="padding: 6px; border: 1px solid #ccc; text-align: center; {{ $cellStyle }}">
                        {{ $displayValue }}
                    </td>
                @endfor

                <!-- Last 12 Months Column -->
                <td style="padding: 6px; border: 1px solid #ccc; text-align: center; background-color: white;">
                    @switch($metric['format'])
                        @case('average')
                            {{ $formatNumber($totals[$metricKey] / 12) }}
                            @break
                        @case('total')
                            {{ round($totals[$metricKey]) }}
                            @break
                        @case('time_avg')
                            {{ $calculateAvgTime($totals[$metricKey]) }}
                            @break
                        @case('percent')
                            {{ $formatNumber($totals[$metricKey] / 12) }}%
                            @break
                        @default
                            {{ $totals[$metricKey] }}
                    @endswitch
                </td>
            </tr>
        @endforeach
    </tbody>
</table>