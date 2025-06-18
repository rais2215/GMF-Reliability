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
        // Helper function untuk menghilangkan trailing zero
        function formatNumber($value, $decimals = 2) {
            return rtrim(rtrim(number_format($value, $decimals), '0'), '.');
        }

        // Inisialisasi total untuk setiap metrik
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
        $totalTechnicalIncidentTotal= 0;
        $totalTechnicalIncidentRate=0;
        $totalTechnicalCancellationTotal=0;
        $totalDispatchReliability=0;
    @endphp
    <div>
        <table>
            <thead>
                <tr>
                    <th colspan="14" class="style2">AIRCRAFT OPERATION SUMMARY</th>
                </tr>
                <tr>
                    <th colspan="14" class="style2">{{ $aircraftType }}</th>
                </tr>
                <tr>
                    <th></th>
                    <th colspan="13">{{ $year-1 }}-{{ $year }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="aos-label"></td>
                    @for ($i = 11; $i >= 0; $i--)
                        <td><b>{{ substr(\Carbon\Carbon::parse($period)->subMonth($i)->format('F'), 0, 3) }}</b></td>
                    @endfor
                    <td><b>LAST 12 MTHS</b></td>
                </tr>
                <tr>
                    <td class="aos-label">A/C in Fleet</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                        $acInFleet = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['acInFleet'];
                            $totalAcInFleet += $acInFleet;
                        @endphp
                        <td>{{ formatNumber($acInFleet) }}</td>
                    @endfor
                    <td>{{ formatNumber($totalAcInFleet / 12) }}</td>
                </tr>
                <tr>
                    <td class="aos-label">A/C in Service</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $acInService = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['acInService'];
                            $totalAcInService += $acInService;
                        @endphp
                        <td>{{ formatNumber($acInService) }}</td>
                    @endfor
                    <td>{{ formatNumber($totalAcInService / 12) }}</td>
                </tr>
                <tr>
                    <td class="aos-label">A/C Days in Service</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $daysInService = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['daysInService'];
                            $totalDaysInService += $daysInService;
                        @endphp
                        <td>{{ $daysInService }}</td>
                    @endfor
                    <td>{{ round($totalDaysInService) }}</td>
                </tr>
                <tr>
                    <td class="aos-label">Flying Hours - Total</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $flyingHoursTotal = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['flyingHoursTotal'];
                            $totalFlyingHoursTotal += $flyingHoursTotal;
                        @endphp
                        <td>{{ round($flyingHoursTotal) }}</td>
                    @endfor
                    <td>{{ round($totalFlyingHoursTotal)}}</td>
                </tr>
                <tr>
                    <td class="aos-label">- Revenue</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $revenueFlyingHours = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['revenueFlyingHours'];
                            $totalRevenueFlyingHours += $revenueFlyingHours;
                        @endphp
                        <td>{{ round($revenueFlyingHours) }}</td>
                    @endfor
                    <td>{{ round($totalRevenueFlyingHours) }}</td>
                </tr>
                <tr>
                    <td class="aos-label">Take Off - Total</td>
                    @for ($i = 11; $i >= 0; $i--)
                            @php
                                $takeOffTotal = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['takeOffTotal'];
                                $totalTakeOffTotal += $takeOffTotal;
                            @endphp
                            <td>{{ $takeOffTotal ?? 0}}</td>
                        @endfor
                        <td>{{ round($totalTakeOffTotal) }}</td>
                </tr>
                <tr>
                    <td class="aos-label">- Revenue</td>
                    @for ($i = 11; $i >= 0; $i--)
                            @php
                                $revenueTakeOff = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['revenueTakeOff'];
                                $totalRevenueTakeOff += $revenueTakeOff;
                            @endphp
                            <td>{{ $revenueTakeOff }}</td>
                        @endfor
                        <td>{{ round($totalRevenueTakeOff) }}</td>
                </tr>
                <tr>
                    <td class="aos-label">Flight Hours per Take Off - Total</td>
                    @for ($i = 11; $i >= 0; $i--)
                            @php
                                $flightHoursPerTakeOffTotal = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['flightHoursPerTakeOffTotal'];
                            @endphp
                            <td>{{ $flightHoursPerTakeOffTotal }}</td>
                        @endfor
                        <td>{{ $avgFlightHoursPerTakeOffTotal }}</td>
                </tr>
                <tr>
                    <td class="aos-label">- Revenue</td>
                    @for ($i = 11; $i >= 0; $i--)
                            @php
                                $revenueFlightHoursPerTakeOff = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['revenueFlightHoursPerTakeOff'];
                            @endphp
                            <td>{{ $revenueFlightHoursPerTakeOff }}</td>
                        @endfor
                        <td>{{ $avgRevenueFlightHoursPerTakeOff }}</td>
                </tr>
                <tr>
                    <td class="aos-label">Daily Utiliz - Total FH</td>
                    @for ($i = 11; $i >= 0; $i--)
                            @php
                                $dailyUtilizationFlyingHoursTotal = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['dailyUtilizationFlyingHoursTotal'];
                            @endphp
                            <td>{{ $dailyUtilizationFlyingHoursTotal }}</td>
                        @endfor
                        <td>{{ $avgDailyUtilizationFlyingHoursTotal }}</td>
                </tr>
                <tr>
                    <td class="aos-label">- Revenue FH</td>
                    @for ($i = 11; $i >= 0; $i--)
                            @php
                                $revenueDailyUtilizationFlyingHoursTotal = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['revenueDailyUtilizationFlyingHoursTotal'];
                            @endphp
                            <td>{{ $revenueDailyUtilizationFlyingHoursTotal }}</td>
                        @endfor
                        <td>{{ $avgRevenueDailyUtilizationFlyingHoursTotal }}</td>
                </tr>
                <tr>
                    <td class="aos-label">- Total FC</td>
                    @for ($i = 11; $i >= 0; $i--)
                            @php
                                $dailyUtilizationTakeOffTotal = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['dailyUtilizationTakeOffTotal'];
                                $totalDailyUtilizationTakeOffTotal += is_numeric($dailyUtilizationTakeOffTotal) ? $dailyUtilizationTakeOffTotal : 0;
                            @endphp
                            <td>{{ formatNumber($dailyUtilizationTakeOffTotal) }}</td>
                        @endfor
                        <td>{{ formatNumber($totalDailyUtilizationTakeOffTotal / 12) }}</td>
                </tr>
                <tr>
                    <td class="aos-label">- Revenue FC</td>
                    @for ($i = 11; $i >= 0; $i--)
                            @php
                                $revenueDailyUtilizationTakeOffTotal = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['revenueDailyUtilizationTakeOffTotal'];
                                $totalRevenueDailyUtilizationTakeOffTotal += is_numeric($revenueDailyUtilizationTakeOffTotal) ? $revenueDailyUtilizationTakeOffTotal : 0;
                            @endphp
                            <td>{{ formatNumber($revenueDailyUtilizationTakeOffTotal) }}</td>
                        @endfor
                        <td>{{ formatNumber($totalRevenueDailyUtilizationTakeOffTotal / 12) }}</td>
                </tr>
                <tr>
                    <td class="aos-label">Technical Delay - Total</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $technicalDelayTotal = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['technicalDelayTotal'];
                            $totalTechnicalDelayTotal += is_numeric($technicalDelayTotal) ? $technicalDelayTotal:0;
                        @endphp
                        <td>{{ round($technicalDelayTotal) }}</td>
                    @endfor
                    <td>{{ round($totalTechnicalDelayTotal) }}</td>
                </tr>
                <tr>
                    <td class="aos-label">- Tot Duration</td>
                    @for ($i = 11; $i >= 0; $i--)
                            @php
                                $totalDuration = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['totalDuration'];
                            @endphp
                            <td>{{ $totalDuration }}</td>
                        @endfor
                        <td>{{ $avgTotalDuration }}</td>
                </tr>
                <tr>
                    <td class="aos-label">- Avg Duration</td>
                    @for ($i = 11; $i >= 0; $i--)
                            @php
                                $averageDuration = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['averageDuration'];
                            @endphp
                            <td>{{ $averageDuration }}</td>
                        @endfor
                        <td>{{ $avgAverageDuration }}</td>
                </tr>
                <tr>
                    <td class="aos-label">- Rate / 100 Take Off</td>
                    @for ($i = 11; $i >= 0; $i--)
                            @php
                                $ratePer100TakeOff = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['ratePer100TakeOff'];
                                $totalRatePer100TakeOff += is_numeric($ratePer100TakeOff) ? $ratePer100TakeOff:0;
                            @endphp
                            <td>{{ formatNumber($ratePer100TakeOff) }}</td>
                        @endfor
                        <td>{{ formatNumber($totalRatePer100TakeOff / 12) }}</td>
                </tr>
                <tr>
                    <td class="aos-label">Technical Incident - Total</td>
                    @for ($i = 11; $i >= 0; $i--)
                            @php
                                $technicalIncidentTotal = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['technicalIncidentTotal'];
                                $totalTechnicalIncidentTotal += is_numeric($technicalIncidentTotal) ? $technicalIncidentTotal:0;
                            @endphp
                            <td>{{ round($technicalIncidentTotal) }}</td>
                        @endfor
                        <td>{{ round($totalTechnicalIncidentTotal / 12) }}</td>
                </tr>
                <tr>
                    <td class="aos-label">- Rate/100 FC</td>
                    @for ($i = 11; $i >= 0; $i--)
                            @php
                                $technicalIncidentRate = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['technicalIncidentRate'] ?? 0;
                                $totalTechnicalIncidentRate += is_numeric($technicalIncidentRate) ? $technicalIncidentRate : 0;
                            @endphp
                            <td>{{ formatNumber($technicalIncidentRate, 3) }}</td>
                        @endfor
                        <td>{{ formatNumber($totalTechnicalIncidentRate / 12) }}</td>
                </tr>
                <tr>
                    <td class="aos-label">Technical Cancellation - Total</td>
                    @for ($i = 11; $i >= 0; $i--)
                            @php
                                $technicalCancellationTotal = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['technicalCancellationTotal'] ?? 0;
                                $totalTechnicalCancellationTotal += is_numeric($technicalCancellationTotal) ? $technicalCancellationTotal : 0;
                            @endphp
                            <td>{{ round($technicalCancellationTotal) }}</td>
                        @endfor
                        <td>{{ round($totalTechnicalCancellationTotal) }}</td>
                </tr>
                <tr>
                    <td class="aos-label">Dispatch Reliability (%)</td>
                    @for ($i = 11; $i >= 0; $i--)
                            @php
                                $dispatchReliability = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['dispatchReliability'] ?? 0;
                                $totalDispatchReliability += is_numeric($dispatchReliability) ? $dispatchReliability : 0;
                            @endphp
                            <td>{{ formatNumber($dispatchReliability) }}%</td>
                        @endfor
                        <td>{{ formatNumber($totalDispatchReliability / 12) }}%</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>