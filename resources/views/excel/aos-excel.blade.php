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
            // Helper functions
            $safeNumber = function($value, $default = 0) {
                return is_numeric($value) ? floatval($value) : $default;
            };

            $formatNumber = function($value, $decimals = 2) {
                if (!is_numeric($value)) return '0';
                return rtrim(rtrim(number_format(floatval($value), $decimals, '.', ''), '0'), '.');
            };

            $formatTime = function($value) {
                if (is_string($value) && str_contains($value, ':')) {
                    $parts = explode(':', str_replace(' ', '', $value));
                    return sprintf('%d:%02d', $parts[0], $parts[1]);
                }
                if (!is_numeric($value) || $value == 0) return '0:00';
                $decimalHours = floatval($value);
                $hours = floor($decimalHours);
                $minutes = round(($decimalHours - $hours) * 60);
                return sprintf('%d:%02d', $hours, $minutes);
            };

            // Select the correct year's data
            $yearColumnData = [];
            if (isset($data2016) && $startYear == '2016') {
                $yearColumnData = $data2016;
            } elseif (isset($data2017) && $startYear == '2017') {
                $yearColumnData = $data2017;
            }
            // Add more years here if needed

            // Function to get value from nested array safely
            $getValue = function($data, $key, $source) {
                if (empty($data)) return null;
                if ($source === 'averages') {
                    return $data['averages'][$key]['value'] ?? null;
                }
                if ($source === 'direct') {
                    $avgKey = 'avg' . ucfirst($key);
                    return $data[$avgKey] ?? null;
                }
                return null;
            };

            // Metrics definition
            $metrics = [
                'acInFleet' => ['label' => 'A/C in Fleet', 'format' => 'round', 'source' => 'averages'],
                'acInService' => ['label' => 'A/C in Service (Revenue)', 'format' => 'number', 'source' => 'averages'],
                'daysInService' => ['label' => 'A/C Days in Service (Revenue)', 'format' => 'round', 'source' => 'averages'],
                'flyingHoursTotal' => ['label' => 'Flying Hours - Total', 'format' => 'round', 'source' => 'averages'],
                'revenueFlyingHours' => ['label' => '- Revenue', 'format' => 'round', 'source' => 'averages'],
                'takeOffTotal' => ['label' => 'Take-off - Total', 'format' => 'round', 'source' => 'averages'],
                'revenueTakeOff' => ['label' => '- Revenue', 'format' => 'round', 'source' => 'averages'],
                'flightHoursPerTakeOffTotal' => ['label' => 'Flight Hours/Take-off - Total', 'format' => 'time', 'source' => 'direct'],
                'revenueFlightHoursPerTakeOff' => ['label' => '- Revenue', 'format' => 'time', 'source' => 'direct'],
                'dailyUtilizationFlyingHoursTotal' => ['label' => 'Daily Utilization Flying Hours - Total', 'format' => 'time', 'source' => 'direct'],
                'revenueDailyUtilizationFlyingHoursTotal' => ['label' => '- Revenue', 'format' => 'time', 'source' => 'direct'],
                'dailyUtilizationTakeOffTotal' => ['label' => 'Daily Utilization Take-off - Total', 'format' => 'number', 'source' => 'averages'],
                'revenueDailyUtilizationTakeOffTotal' => ['label' => '- Revenue', 'format' => 'number', 'source' => 'averages'],
                'technicalDelayTotal' => ['label' => 'Technical Delay - Total', 'format' => 'round', 'source' => 'averages'],
                'totalDuration' => ['label' => '- Total Duration', 'format' => 'time', 'source' => 'direct'],
                'averageDuration' => ['label' => '- Avg Duration', 'format' => 'time', 'source' => 'direct'],
                'ratePer100TakeOff' => ['label' => '- Rate/100 Take-Off', 'format' => 'number', 'source' => 'averages'],
                'technicalIncidentTotal' => ['label' => 'Technical Incident - Total', 'format' => 'round', 'source' => 'averages'],
                'technicalIncidentRate' => ['label' => '- Rate / 100 FC', 'format' => 'number', 'source' => 'averages'],
                'technicalCancellationTotal' => ['label' => 'Technical Cancellation - Total', 'format' => 'round', 'source' => 'averages'],
                'dispatchReliability' => ['label' => 'Dispatch Reliability (%)', 'format' => 'percent', 'source' => 'averages'],
            ];
        @endphp

        @foreach($metrics as $metricKey => $metric)
            @php
                $isHeaderRow = in_array($metricKey, ['acInFleet', 'flyingHoursTotal', 'takeOffTotal', 'flightHoursPerTakeOffTotal', 'dailyUtilizationFlyingHoursTotal', 'dailyUtilizationTakeOffTotal', 'technicalDelayTotal', 'technicalIncidentTotal', 'technicalCancellationTotal', 'dispatchReliability']);
                $isSubRow = strpos($metric['label'], '-') === 0;
            @endphp
            <tr style="{{ $isHeaderRow && !$isSubRow ? 'background-color: #F8F9FA;' : '' }}">
                <!-- Metrics Label -->
                <td style="padding: 8px; border: 1px solid #ccc; text-align: left; font-weight: {{ $isHeaderRow && !$isSubRow ? 'bold' : 'normal' }}; padding-left: {{ $isSubRow ? '20px' : '8px' }};">
                    {{ $metric['label'] }}
                </td>

                <!-- Start Year Column -->
                <td style="padding: 6px; border: 1px solid #ccc; text-align: center;">
                    @php $yearValue = $getValue($yearColumnData, $metricKey, $metric['source']); @endphp
                    @switch($metric['format'])
                        @case('number')
                            {{ $formatNumber($yearValue) }}
                            @break
                        @case('round')
                            {{ round($safeNumber($yearValue)) }}
                            @break
                        @case('time')
                            {{ $formatTime($yearValue) }}
                            @break
                        @case('percent')
                            {{ $formatNumber($yearValue) }}%
                            @break
                        @default
                            {{ $yearValue }}
                    @endswitch
                </td>

                <!-- Monthly Data Columns -->
                @for ($i = 11; $i >= 0; $i--)
                    @php
                        $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                        $value = $reportData[$monthKey][$metricKey] ?? 0;
                    @endphp
                    <td style="padding: 6px; border: 1px solid #ccc; text-align: center;">
                        @switch($metric['format'])
                            @case('number')
                                {{ $formatNumber($value) }}
                                @break
                            @case('round')
                                {{ round($safeNumber($value)) }}
                                @break
                            @case('time')
                                {{ $formatTime($value) }}
                                @break
                            @case('percent')
                                {{ $formatNumber($value) }}%
                                @break
                            @default
                                {{ $value }}
                        @endswitch
                    </td>
                @endfor

                <!-- Last 12 Months Column -->
                <td style="padding: 6px; border: 1px solid #ccc; text-align: center;">
                    @php
                        // Use the processedData passed from the controller for "Last 12 Months"
                        $last12MthsValue = $getValue($processedData, $metricKey, $metric['source']);
                    @endphp
                     @switch($metric['format'])
                        @case('number')
                            {{ $formatNumber($last12MthsValue) }}
                            @break
                        @case('round')
                            {{ round($safeNumber($last12MthsValue)) }}
                            @break
                        @case('time')
                            {{ $formatTime($last12MthsValue) }}
                            @break
                        @case('percent')
                            {{ $formatNumber($last12MthsValue) }}%
                            @break
                        @default
                            {{ $last12MthsValue }}
                    @endswitch
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
