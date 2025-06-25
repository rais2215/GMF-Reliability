<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>AOS PDF</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            font-family: Arial, sans-serif;
            font-size: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }
        .style1 {
            text-align: center;
        }
        .style2 {
            font-size: 20px;
            text-align: center;
        }
        .aos-label {
            font-weight: bold !important;
            text-align: left !important;
        }
    </style>
</head>
<body>
    @php
        // Helper functions - PERSIS sama seperti di aos-result.blade.php
        $safeNumber = function($value, $default = 0) {
            if (is_null($value) || !is_numeric($value)) {
                return $default;
            }
            return floatval($value);
        };
        
        $formatNumber = function($value, $decimals = 2) {
            if (is_null($value) || !is_numeric($value)) {
                return '0';
            }
            $numValue = floatval($value);
            return rtrim(rtrim(number_format($numValue, $decimals, '.', ''), '0'), '.');
        };

        // Helper function for time formatting - SAMA dengan aos-result
        $formatTime = function($value) {
            if (is_null($value) || $value === '' || $value === 0) {
                return '00:00';
            }
            // If it's already in H:i format, return as is
            if (is_string($value) && strpos($value, ':') !== false) {
                return $value;
            }
            // If it's a number, convert to H:i format
            if (is_numeric($value)) {
                $hours = floor($value);
                $minutes = round(($value - $hours) * 60);
                return sprintf('%d:%02d', $hours, $minutes);
            }
            return $value ?: '00:00';
        };

        // Convert time string to decimal hours for calculation
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

        // Calculate average time values for totals
        $calculateAvgTime = function($totalDecimal) use ($formatTime) {
            $avg = $totalDecimal / 12;
            return $formatTime($avg);
        };

        // Calculate total time 
        $calculateTotalTime = function($totalDecimal) use ($formatTime) {
            return $formatTime($totalDecimal);
        };

        $startYear = \Carbon\Carbon::parse($period)->subMonth(11)->format('Y');

        // Initialize totals - SAMA seperti di aos-result.blade.php
        $totalAcInFleet = 0;
        $totalAcInService = 0;
        $totalDaysInService = 0;
        $totalFlyingHoursTotal = 0;
        $totalRevenueFlyingHours = 0;
        $totalTakeOffTotal = 0;
        $totalRevenueTakeOff = 0;
        $totalFlightHoursPerTakeOffTotal = 0;
        $totalRevenueFlightHoursPerTakeOff = 0;
        $totalDailyUtilizationFlyingHoursTotal = 0;
        $totalRevenueDailyUtilizationFlyingHoursTotal = 0;
        $totalDailyUtilizationTakeOffTotal = 0;
        $totalRevenueDailyUtilizationTakeOffTotal = 0;
        $totalTechnicalDelayTotal = 0;
        $totalTotalDuration = 0;
        $totalAverageDuration = 0;
        $totalRatePer100TakeOff = 0;
        $totalTechnicalIncidentTotal = 0;
        $totalTechnicalIncidentRate = 0;
        $totalTechnicalCancellationTotal = 0;
        $totalDispatchReliability = 0;

        // Calculate totals for 12 months - SAMA seperti di aos-result.blade.php
        for ($j = 11; $j >= 0; $j--) {
            $monthKey = \Carbon\Carbon::parse($period)->subMonth($j)->format('Y-m');
            $monthData = $reportData[$monthKey] ?? [];
            
            $totalAcInFleet += $safeNumber($monthData['acInFleet'] ?? 0);
            $totalAcInService += $safeNumber($monthData['acInService'] ?? 0);
            $totalDaysInService += $safeNumber($monthData['daysInService'] ?? 0);
            $totalFlyingHoursTotal += $safeNumber($monthData['flyingHoursTotal'] ?? 0);
            $totalRevenueFlyingHours += $safeNumber($monthData['revenueFlyingHours'] ?? 0);
            $totalTakeOffTotal += $safeNumber($monthData['takeOffTotal'] ?? 0);
            $totalRevenueTakeOff += $safeNumber($monthData['revenueTakeOff'] ?? 0);
            
            // Convert time values to decimal for proper calculation
            $totalFlightHoursPerTakeOffTotal += $timeToDecimal($monthData['flightHoursPerTakeOffTotal'] ?? 0);
            $totalRevenueFlightHoursPerTakeOff += $timeToDecimal($monthData['revenueFlightHoursPerTakeOff'] ?? 0);
            $totalDailyUtilizationFlyingHoursTotal += $timeToDecimal($monthData['dailyUtilizationFlyingHoursTotal'] ?? 0);
            $totalRevenueDailyUtilizationFlyingHoursTotal += $timeToDecimal($monthData['revenueDailyUtilizationFlyingHoursTotal'] ?? 0);
            
            $totalDailyUtilizationTakeOffTotal += $safeNumber($monthData['dailyUtilizationTakeOffTotal'] ?? 0);
            $totalRevenueDailyUtilizationTakeOffTotal += $safeNumber($monthData['revenueDailyUtilizationTakeOffTotal'] ?? 0);
            $totalTechnicalDelayTotal += $safeNumber($monthData['technicalDelayTotal'] ?? 0);
            
            // Convert time values to decimal for proper calculation
            $totalTotalDuration += $timeToDecimal($monthData['totalDuration'] ?? 0);
            $totalAverageDuration += $timeToDecimal($monthData['averageDuration'] ?? 0);
            
            $totalRatePer100TakeOff += $safeNumber($monthData['ratePer100TakeOff'] ?? 0);
            $totalTechnicalIncidentTotal += $safeNumber($monthData['technicalIncidentTotal'] ?? 0);
            $totalTechnicalIncidentRate += $safeNumber($monthData['technicalIncidentRate'] ?? 0);
            $totalTechnicalCancellationTotal += $safeNumber($monthData['technicalCancellationTotal'] ?? 0);
            $totalDispatchReliability += $safeNumber($monthData['dispatchReliability'] ?? 0);
        }
    @endphp
    <div>
        <table>
            <thead>
                <tr>
                    <th colspan="15" class="style2">AIRCRAFT OPERATION SUMMARY</th>
                </tr>
                <tr>
                    <th colspan="15" class="style2">{{ $aircraftType }}</th>
                </tr>
                <tr>
                    <th colspan="15">{{ $startYear }} - {{ $year }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="aos-label"></td>
                    <td><b>{{ $startYear }}</b></td>
                    @for ($i = 11; $i >= 0; $i--)
                        <td><b>{{ substr(\Carbon\Carbon::parse($period)->subMonth($i)->format('F'), 0, 3) }}</b></td>
                    @endfor
                    <td><b>LAST 12 MTHS</b></td>
                </tr>
                
                <!-- A/C In Fleet Row - SAMA seperti aos-result -->
                <tr>
                    <td class="aos-label">A/C in Fleet</td>
                    <td>{{ $formatNumber($totalAcInFleet / 12) }}</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $acInFleet = $safeNumber($reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['acInFleet'] ?? 0);
                        @endphp
                        <td>{{ round($acInFleet) }}</td>
                    @endfor
                    <td>{{ $formatNumber($totalAcInFleet / 12) }}</td>
                </tr>

                <!-- A/C In Service Row - SAMA seperti aos-result -->
                <tr>
                    <td class="aos-label">A/C in Service (Revenue)</td>
                    <td>{{ $formatNumber($totalAcInService / 12) }}</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $value = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['acInService'] ?? 0;
                        @endphp
                        <td>{{ $formatNumber($safeNumber($value)) }}</td>
                    @endfor
                    <td>{{ $formatNumber($totalAcInService / 12) }}</td>
                </tr>

                <!-- A/C Days In Service Row - SAMA seperti aos-result -->
                <tr>
                    <td class="aos-label">A/C Days in Service (Revenue)</td>
                    <td>{{ round($safeNumber($totalDaysInService)) }}</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $value = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['daysInService'] ?? 0;
                        @endphp
                        <td>{{ round($safeNumber($value)) }}</td>
                    @endfor
                    <td>{{ round($safeNumber($totalDaysInService)) }}</td>
                </tr>

                <!-- Flying Hours - Total Row - SAMA seperti aos-result -->
                <tr>
                    <td class="aos-label">Flying Hours - Total</td>
                    <td>{{ round($safeNumber($totalFlyingHoursTotal)) }}</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $value = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['flyingHoursTotal'] ?? 0;
                        @endphp
                        <td>{{ round($safeNumber($value)) }}</td>
                    @endfor
                    <td>{{ round($safeNumber($totalFlyingHoursTotal)) }}</td>
                </tr>

                <!-- Flying Hours - Revenue Row - SAMA seperti aos-result -->
                <tr>
                    <td class="aos-label">- Revenue</td>
                    <td>{{ round($safeNumber($totalRevenueFlyingHours)) }}</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $value = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['revenueFlyingHours'] ?? 0;
                        @endphp
                        <td>{{ round($safeNumber($value)) }}</td>
                    @endfor
                    <td>{{ round($safeNumber($totalRevenueFlyingHours)) }}</td>
                </tr>

                <!-- Take Off - Total Row - SAMA seperti aos-result -->
                <tr>
                    <td class="aos-label">Take Off - Total</td>
                    <td>{{ round($safeNumber($totalTakeOffTotal)) }}</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $value = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['takeOffTotal'] ?? 0;
                        @endphp
                        <td>{{ round($safeNumber($value)) }}</td>
                    @endfor
                    <td>{{ round($safeNumber($totalTakeOffTotal)) }}</td>
                </tr>

                <!-- Take Off - Revenue Row - SAMA seperti aos-result -->
                <tr>
                    <td class="aos-label">- Revenue</td>
                    <td>{{ round($safeNumber($totalRevenueTakeOff)) }}</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $value = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['revenueTakeOff'] ?? 0;
                        @endphp
                        <td>{{ round($safeNumber($value)) }}</td>
                    @endfor
                    <td>{{ round($safeNumber($totalRevenueTakeOff)) }}</td>
                </tr>

                <!-- Flight Hours per Take Off - Total Row - SAMA seperti aos-result -->
                <tr>
                    <td class="aos-label">Flight Hours per Take Off - Total</td>
                    <td>{{ $calculateAvgTime($totalFlightHoursPerTakeOffTotal) }}</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $value = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['flightHoursPerTakeOffTotal'] ?? 0;
                        @endphp
                        <td>{{ $formatTime($value) }}</td>
                    @endfor
                    <td>{{ $calculateAvgTime($totalFlightHoursPerTakeOffTotal) }}</td>
                </tr>

                <!-- Flight Hours per Take Off - Revenue Row - SAMA seperti aos-result -->
                <tr>
                    <td class="aos-label">- Revenue</td>
                    <td>{{ $calculateAvgTime($totalRevenueFlightHoursPerTakeOff) }}</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $value = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['revenueFlightHoursPerTakeOff'] ?? 0;
                        @endphp
                        <td>{{ $formatTime($value) }}</td>
                    @endfor
                    <td>{{ $calculateAvgTime($totalRevenueFlightHoursPerTakeOff) }}</td>
                </tr>

                <!-- Daily Utilization Flying Hours - Total Row - SAMA seperti aos-result -->
                <tr>
                    <td class="aos-label">Daily Utilization Flying Hours - Total</td>
                    <td>{{ $calculateAvgTime($totalDailyUtilizationFlyingHoursTotal) }}</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $value = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['dailyUtilizationFlyingHoursTotal'] ?? 0;
                        @endphp
                        <td>{{ $formatTime($value) }}</td>
                    @endfor
                    <td>{{ $calculateAvgTime($totalDailyUtilizationFlyingHoursTotal) }}</td>
                </tr>

                <!-- Daily Utilization Flying Hours - Revenue Row - SAMA seperti aos-result -->
                <tr>
                    <td class="aos-label">- Revenue</td>
                    <td>{{ $calculateAvgTime($totalRevenueDailyUtilizationFlyingHoursTotal) }}</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $value = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['revenueDailyUtilizationFlyingHoursTotal'] ?? 0;
                        @endphp
                        <td>{{ $formatTime($value) }}</td>
                    @endfor
                    <td>{{ $calculateAvgTime($totalRevenueDailyUtilizationFlyingHoursTotal) }}</td>
                </tr>

                <!-- Daily Utilization Take Off - Total Row - SAMA seperti aos-result -->
                <tr>
                    <td class="aos-label">Daily Utilization Take Off - Total</td>
                    <td>{{ $formatNumber($totalDailyUtilizationTakeOffTotal / 12) }}</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $value = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['dailyUtilizationTakeOffTotal'] ?? 0;
                        @endphp
                        <td>{{ $formatNumber($safeNumber($value)) }}</td>
                    @endfor
                    <td>{{ $formatNumber($totalDailyUtilizationTakeOffTotal / 12) }}</td>
                </tr>

                <!-- Daily Utilization Take Off - Revenue Row - SAMA seperti aos-result -->
                <tr>
                    <td class="aos-label">- Revenue</td>
                    <td>{{ $formatNumber($totalRevenueDailyUtilizationTakeOffTotal / 12) }}</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $value = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['revenueDailyUtilizationTakeOffTotal'] ?? 0;
                        @endphp
                        <td>{{ $formatNumber($safeNumber($value)) }}</td>
                    @endfor
                    <td>{{ $formatNumber($totalRevenueDailyUtilizationTakeOffTotal / 12) }}</td>
                </tr>

                <!-- Technical Delay - Total Row - SAMA seperti aos-result -->
                <tr>
                    <td class="aos-label">Technical Delay - Total</td>
                    <td>{{ round($safeNumber($totalTechnicalDelayTotal)) }}</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $value = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['technicalDelayTotal'] ?? 0;
                        @endphp
                        <td>{{ round($safeNumber($value)) }}</td>
                    @endfor
                    <td>{{ round($safeNumber($totalTechnicalDelayTotal)) }}</td>
                </tr>

                <!-- Technical Delay - Total Duration Row - SAMA seperti aos-result -->
                <tr>
                    <td class="aos-label">- Total Duration</td>
                    <td>{{ $calculateAvgTime($totalTotalDuration) }}</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $value = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['totalDuration'] ?? 0;
                        @endphp
                        <td>{{ $formatTime($value) }}</td>
                    @endfor
                    <td>{{ $calculateAvgTime($totalTotalDuration) }}</td>
                </tr>

                <!-- Technical Delay - Average Duration Row - SAMA seperti aos-result -->
                <tr>
                    <td class="aos-label">- Average Duration</td>
                    <td>{{ $calculateAvgTime($totalAverageDuration) }}</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $value = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['averageDuration'] ?? 0;
                        @endphp
                        <td>{{ $formatTime($value) }}</td>
                    @endfor
                    <td>{{ $calculateAvgTime($totalAverageDuration) }}</td>
                </tr>

                <!-- Technical Delay - Rate per 100 Take Off Row - SAMA seperti aos-result -->
                <tr>
                    <td class="aos-label">- Rate per 100 Take Off</td>
                    <td>{{ $formatNumber($totalRatePer100TakeOff / 12) }}</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $value = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['ratePer100TakeOff'] ?? 0;
                        @endphp
                        <td>{{ $formatNumber($safeNumber($value)) }}</td>
                    @endfor
                    <td>{{ $formatNumber($totalRatePer100TakeOff / 12) }}</td>
                </tr>

                <!-- Technical Incident - Total Row - SAMA seperti aos-result -->
                <tr>
                    <td class="aos-label">Technical Incident - Total</td>
                    <td>{{ round($safeNumber($totalTechnicalIncidentTotal)) }}</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $value = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['technicalIncidentTotal'] ?? 0;
                        @endphp
                        <td>{{ round($safeNumber($value)) }}</td>
                    @endfor
                    <td>{{ round($safeNumber($totalTechnicalIncidentTotal)) }}</td>
                </tr>

                <!-- Technical Incident - Rate per 100 Take Off Row - SAMA seperti aos-result -->
                <tr>
                    <td class="aos-label">- Rate per 100 Take Off</td>
                    <td>{{ $formatNumber($totalTechnicalIncidentRate / 12) }}</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $value = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['technicalIncidentRate'] ?? 0;
                        @endphp
                        <td>{{ $formatNumber($safeNumber($value)) }}</td>
                    @endfor
                    <td>{{ $formatNumber($totalTechnicalIncidentRate / 12) }}</td>
                </tr>

                <!-- Technical Cancellation - Total Row - SAMA seperti aos-result -->
                <tr>
                    <td class="aos-label">Technical Cancellation - Total</td>
                    <td>{{ round($safeNumber($totalTechnicalCancellationTotal)) }}</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $value = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['technicalCancellationTotal'] ?? 0;
                        @endphp
                        <td>{{ round($safeNumber($value)) }}</td>
                    @endfor
                    <td>{{ round($safeNumber($totalTechnicalCancellationTotal)) }}</td>
                </tr>

                <!-- Dispatch Reliability Row - SAMA seperti aos-result -->
                <tr>
                    <td class="aos-label">Dispatch Reliability (%)</td>
                    <td>{{ $formatNumber($totalDispatchReliability / 12) }}%</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $value = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['dispatchReliability'] ?? 0;
                        @endphp
                        <td>{{ $formatNumber($safeNumber($value)) }}%</td>
                    @endfor
                    <td>{{ $formatNumber($totalDispatchReliability / 12) }}%</td>
                </tr>

            </tbody>
        </table>
    </div>
</body>
</html>