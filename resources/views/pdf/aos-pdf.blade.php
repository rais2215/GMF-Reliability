<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>AOS PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 12px; /* Adjusted to fill page */
        }
        th, td {
            border: 1px solid #000;
            padding: 5px; /* Adjusted for larger font */
            text-align: center;
            vertical-align: middle;
        }
        .header-title {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
        }
        .header-subtitle {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
        }
        .header-period {
            font-size: 14px;
            text-align: center;
        }
        .aos-label {
            font-weight: bold;
            text-align: left;
            white-space: nowrap; /* Prevent label wrapping */
        }
        .column-header {
            font-weight: bold;
        }
    </style>
</head>
<body>
    @php
        // Helper functions
        $safeNumber = function($value, $default = 0) {
            return is_numeric($value) ? floatval($value) : $default;
        };

        $formatNumber = function($value, $decimals = 2) {
            if (!is_numeric($value)) {
                return '0';
            }
            return rtrim(rtrim(number_format(floatval($value), $decimals, '.', ''), '0'), '.');
        };

        $formatTime = function($value) {
            if (is_string($value) && str_contains($value, ':')) {
                 // Already formatted as H : i
                $parts = explode(':', str_replace(' ', '', $value));
                return sprintf('%d:%02d', $parts[0], $parts[1]);
            }
            if (!is_numeric($value) || $value == 0) {
                return '0:00';
            }
            $decimalHours = floatval($value);
            $hours = floor($decimalHours);
            $minutes = round(($decimalHours - $hours) * 60);
            return sprintf('%d:%02d', $hours, $minutes);
        };

        // Year variables
        $startYear = \Carbon\Carbon::parse($period)->subMonths(11)->format('Y');
        $endYear = \Carbon\Carbon::parse($period)->format('Y');

        // Select the correct year's data based on the start year of the report
        $yearColumnData = [];
        if (isset($data2016) && $startYear == '2016') {
            $yearColumnData = $data2016;
        } elseif (isset($data2017) && $startYear == '2017') {
            $yearColumnData = $data2017;
        }
        // Add more years here if needed

        // Function to get value from nested array safely for YEAR data
        $getYearValue = function($data, $key, $source) {
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

        // Define metrics to iterate through
        $metrics = [
            'acInFleet' => ['label' => 'A/C in Fleet', 'format' => 'number', 'decimals' => 2, 'source' => 'averages', 'last12' => $averages['acInFleet']['value'] ?? 0],
            'acInService' => ['label' => 'A/C in Service (Revenue)', 'format' => 'number', 'decimals' => 2, 'source' => 'averages', 'last12' => $averages['acInService']['value'] ?? 0],
            'daysInService' => ['label' => 'A/C Days in Service (Revenue)', 'format' => 'integer', 'source' => 'averages', 'last12' => $averages['daysInService']['value'] ?? 0],
            'flyingHoursTotal' => ['label' => 'Flying Hours - Total', 'format' => 'integer', 'source' => 'averages', 'last12' => $averages['flyingHoursTotal']['value'] ?? 0],
            'revenueFlyingHours' => ['label' => '- Revenue', 'format' => 'integer', 'source' => 'averages', 'last12' => $averages['revenueFlyingHours']['value'] ?? 0],
            'takeOffTotal' => ['label' => 'Take Off - Total', 'format' => 'integer', 'source' => 'averages', 'last12' => $averages['takeOffTotal']['value'] ?? 0],
            'revenueTakeOff' => ['label' => '- Revenue', 'format' => 'integer', 'source' => 'averages', 'last12' => $averages['revenueTakeOff']['value'] ?? 0],
            'flightHoursPerTakeOffTotal' => ['label' => 'Flight Hours per Take Off - Total', 'format' => 'time', 'source' => 'direct', 'last12' => $avgFlightHoursPerTakeOffTotal ?? '0:00'],
            'revenueFlightHoursPerTakeOff' => ['label' => '- Revenue', 'format' => 'time', 'source' => 'direct', 'last12' => $avgRevenueFlightHoursPerTakeOff ?? '0:00'],
            'dailyUtilizationFlyingHoursTotal' => ['label' => 'Daily Utilization Flying Hours - Total', 'format' => 'time', 'source' => 'direct', 'last12' => $avgDailyUtilizationFlyingHoursTotal ?? '0:00'],
            'revenueDailyUtilizationFlyingHoursTotal' => ['label' => '- Revenue', 'format' => 'time', 'source' => 'direct', 'last12' => $avgRevenueDailyUtilizationFlyingHoursTotal ?? '0:00'],
            'dailyUtilizationTakeOffTotal' => ['label' => 'Daily Utilization Take Off - Total', 'format' => 'number', 'decimals' => 2, 'source' => 'averages', 'last12' => $averages['dailyUtilizationTakeOffTotal']['value'] ?? 0],
            'revenueDailyUtilizationTakeOffTotal' => ['label' => '- Revenue', 'format' => 'number', 'decimals' => 2, 'source' => 'averages', 'last12' => $averages['revenueDailyUtilizationTakeOffTotal']['value'] ?? 0],
            'technicalDelayTotal' => ['label' => 'Technical Delay - Total', 'format' => 'integer', 'source' => 'averages', 'last12' => $averages['technicalDelayTotal']['value'] ?? 0],
            'totalDuration' => ['label' => '- Total Duration', 'format' => 'time', 'source' => 'direct', 'last12' => $avgTotalDuration ?? '0:00'],
            'averageDuration' => ['label' => '- Average Duration', 'format' => 'time', 'source' => 'direct', 'last12' => $avgAverageDuration ?? '0:00'],
            'ratePer100TakeOff' => ['label' => '- Rate per 100 Take Off', 'format' => 'number', 'decimals' => 2, 'source' => 'averages', 'last12' => $averages['ratePer100TakeOff']['value'] ?? 0],
            'technicalIncidentTotal' => ['label' => 'Technical Incident - Total', 'format' => 'integer', 'source' => 'averages', 'last12' => $averages['technicalIncidentTotal']['value'] ?? 0],
            'technicalIncidentRate' => ['label' => '- Rate per 100 Take Off', 'format' => 'number', 'decimals' => 2, 'source' => 'averages', 'last12' => $averages['technicalIncidentRate']['value'] ?? 0],
            'technicalCancellationTotal' => ['label' => 'Technical Cancellation - Total', 'format' => 'integer', 'source' => 'averages', 'last12' => $averages['technicalCancellationTotal']['value'] ?? 0],
        ];

        $dispatchReliabilityMetric = [
            'dispatchReliability' => ['label' => 'Dispatch Reliability (%)', 'format' => 'percent', 'source' => 'averages', 'last12' => $averages['dispatchReliability']['value'] ?? 0],
        ];

    @endphp
    <div>
        <table>
            <thead>
                <tr>
                    <th colspan="15" class="header-title">AIRCRAFT OPERATION SUMMARY</th>
                </tr>
                <tr>
                    <th colspan="15" class="header-subtitle">{{ $aircraftType }}</th>
                </tr>
                <tr>
                    <th colspan="15" class="header-period">{{ $startYear }} - {{ $endYear }}</th>
                </tr>
                <tr>
                    <td class="aos-label column-header"></td>
                    <td class="column-header"><b>{{ $startYear }}</b></td>
                    @for ($i = 11; $i >= 0; $i--)
                        <td class="column-header"><b>{{ substr(\Carbon\Carbon::parse($period)->subMonth($i)->format('F'), 0, 3) }}</b></td>
                    @endfor
                    <td class="column-header"><b>LAST 12 MTHS</b></td>
                </tr>
            </thead>
            <tbody>
                @foreach($metrics as $key => $metric)
                <tr>
                    <td class="aos-label">{{ $metric['label'] }}</td>

                    {{-- Year Column --}}
                    <td>
                        @php $yearValue = $getYearValue($yearColumnData, $key, $metric['source']); @endphp
                        @if($metric['format'] === 'time')
                            {{ $formatTime($yearValue) }}
                        @elseif($metric['format'] === 'integer')
                            {{ round($safeNumber($yearValue)) }}
                        @else
                            {{ $formatNumber($yearValue, $metric['decimals']) }}
                        @endif
                    </td>

                    {{-- Monthly Columns --}}
                    @for ($i = 11; $i >= 0; $i--)
                        <td>
                            @php $monthValue = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')][$key] ?? null; @endphp
                            @if($metric['format'] === 'time')
                                {{ $formatTime($monthValue) }}
                            @elseif($metric['format'] === 'integer')
                                {{ round($safeNumber($monthValue)) }}
                            @else
                                {{ $formatNumber($monthValue, $metric['decimals']) }}
                            @endif
                        </td>
                    @endfor

                    {{-- Last 12 Months Column --}}
                    <td>
                        @php $last12MthsValue = $metric['last12']; @endphp
                        @if($metric['format'] === 'time')
                            {{ $formatTime($last12MthsValue) }}
                        @elseif($metric['format'] === 'integer')
                            {{ round($safeNumber($last12MthsValue)) }}
                        @else
                            {{ $formatNumber($last12MthsValue, $metric['decimals']) }}
                        @endif
                    </td>
                </tr>
                @endforeach

                {{-- Special Row for Dispatch Reliability --}}
                @foreach($dispatchReliabilityMetric as $key => $metric)
                <tr>
                    <td class="aos-label">{{ $metric['label'] }}</td>

                    {{-- Year Column --}}
                    <td>
                        @php $yearValue = $getYearValue($yearColumnData, $key, $metric['source']); @endphp
                        {{ $formatNumber($yearValue, 2) }}%
                    </td>

                   {{-- Monthly Columns --}}
                    @for ($i = 11; $i >= 0; $i--)
                        <td>
                            @php $monthValue = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')][$key] ?? null; @endphp
                            {{ $formatNumber($monthValue, 2) }}%
                        </td>
                    @endfor

                    {{-- Last 12 Months Column --}}
                    <td>
                        @php $last12MthsValue = $metric['last12']; @endphp
                        {{ $formatNumber($last12MthsValue, 2) }}%
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
