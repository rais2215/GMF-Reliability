<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PILOT PDF</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
            font-size: 7px;
            text-align: center;
        }
        th, td {
            border: 1px solid black;
            padding: 5px;
        }
        h6 {
            font-size: 7px; 
            text-align: left;
            margin: 5px;
        }
        .issued {
            text-align: right;
            margin: 6px;
        }
    </style>
</head>
<body>
    {{-- Pilot Report --}}
    <div>
        <table>
            <thead>
                <tr>
                    <th colspan="2">{{ $aircraftType }}</th>
                    <th colspan="13">PILOT REPORT</th>
                </tr>
                <tr>
                    <th colspan="15"></th>
                </tr>
                <tr>
                    <th colspan="2">Total Flight Hours</th>
                    <th>{{ round($flyingHours2Before) }}</th>
                    <th>{{ round($flyingHoursBefore) }}</th>
                    <th>{{ round($flyingHoursTotal) }}</th>
                    <th>{{ round($fh3Last) }}</th>
                    <th>{{ round($fh12Last) }}</th>
                    <th colspan="8"></th>
                </tr>
                <tr>
                    <th colspan="2" rowspan="2">ATA CHAPTER</th>
                    <th>{{ substr(\Carbon\Carbon::parse($period)->subMonths(2)->format('F'), 0, 3) }}</th>
                    <th>{{ substr(\Carbon\Carbon::parse($period)->subMonth(1)->format('F'), 0, 3) }}</th>
                    <th>{{ substr(\Carbon\Carbon::parse($period)->format('F'), 0, 3) }}</th>
                    <th>Last 3</th>
                    <th>Last 12</th>
                    <th>{{ substr(\Carbon\Carbon::parse($period)->subMonths(2)->format('F'), 0, 3) }}</th>
                    <th>{{ substr(\Carbon\Carbon::parse($period)->subMonth(1)->format('F'), 0, 3) }}</th>
                    <th>{{ substr(\Carbon\Carbon::parse($period)->format('F'), 0, 3) }}</th>
                    <th>3 Months</th>
                    <th>12 Months</th>
                    <th>ALERT</th>
                    <th>ALERT</th>
                    <th rowspan="2">TREND</th>
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Months</th>
                    <th>Months</th>
                    <th>RATE</th>
                    <th>RATE</th>
                    <th>RATE</th>
                    <th>RATE</th>
                    <th>RATE</th>
                    <th>LEVEL</th>
                    <th>STATUS</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tblAta as $item)
                <tr>
                    <th>{{ $item->ATA }}</th>
                    <th>{{ $item->ATA_DESC }}</th>
                    <td>{{ $pirepCountTwoMonthsAgo }}</td>
                    <td>{{ $pirepCountBefore }}</td>
                    <td>{{ $pirepCount }}</td>
                    <td>{{ $pirep3Month }}</td>
                    <td>{{ $pirep12Month }}</td>
                    <td>{{ number_format($pirep2Rate,2) }}</td>
                    <td>{{ number_format($pirep1Rate,2) }}</td>
                    <td>{{ number_format($pirepRate,2) }}</td>
                    <td>{{ number_format($pirepRate3Month,2) }}</td>
                    <td>{{ number_format($pirepRate12Month, 2) }}</td>
                    <td>{{ number_format($pirepAlertLevel,2) }}</td>
                    <td>{{ $pirepAlertStatus }}</td>
                    <td>{{ $pirepTrend }}</td>
                </tr>  
                @endforeach
            </tbody>
        </table>
        <h6>NOTE :</h6>
        <h6>The Alert Level (AL) is based on monthly Technical Pilot Report / Maintenance Finding Report / Delay Rate of last Four Quarters (Average + 2 *STD)</h6>
        <h6>The Alert Status colomn will show "RED-1" if the last month Delay Rate exceed the AL, "RED-2" if this is true for the last two consecutive months,</h6>
        <h6>and "RED-3" if this is true for the last three consecutive months.</h6>
        <h6>The TREND colomn show an "UP" or "DOWN" when the rate has increased or decreased for 3 months</h6>
        <h6 class="issued">Issued by Citilink Engineering & Maintenance and Compiled by GMF Reliability Engineering & Services</h6>
    </div>

    {{-- Maintenance Report --}}
        <div>
            <table>
                <thead>
                    <tr>
                        <th colspan="2">{{ $aircraftType }}</th>
                        <th colspan="13">MAINTENANCE FINDING REPORT</th>
                    </tr>
                    <tr>
                        <th colspan="15"></th>
                    </tr>
                    <tr>
                        <th colspan="2">Total Flight Hours</th>
                        <th>{{ round($flyingHours2Before) }}</th>
                        <th>{{ round($flyingHoursBefore) }}</th>
                        <th>{{ round($flyingHoursTotal) }}</th>
                        <th>{{ round($fh3Last) }}</th>
                        <th>{{ round($fh12Last) }}</th>
                        <th colspan="8"></th>
                    </tr>
                    <tr>
                        <th colspan="2" rowspan="2">ATA CHAPTER</th>
                        <th rowspan="2">{{ substr(\Carbon\Carbon::parse($period)->subMonths(2)->format('F'), 0, 3) }}</th>
                        <th rowspan="2">{{ substr(\Carbon\Carbon::parse($period)->subMonth(1)->format('F'), 0, 3) }}</th>
                        <th rowspan="2">{{ substr(\Carbon\Carbon::parse($period)->format('F'), 0, 3) }}</th>
                        <th>Last 3</th>
                        <th>Last 12</th>
                        <th>{{ substr(\Carbon\Carbon::parse($period)->subMonths(2)->format('F'), 0, 3) }}</th>
                        <th>{{ substr(\Carbon\Carbon::parse($period)->subMonth(1)->format('F'), 0, 3) }}</th>
                        <th>{{ substr(\Carbon\Carbon::parse($period)->format('F'), 0, 3) }}</th>
                        <th>3 Months</th>
                        <th>12 Months</th>
                        <th>ALERT</th>
                        <th>ALERT</th>
                        <th rowspan="2">TREND</th>
                    </tr>
                    <tr>
                        <th>Months</th>
                        <th>Months</th>
                        <th>RATE</th>
                        <th>RATE</th>
                        <th>RATE</th>
                        <th>RATE</th>
                        <th>RATE</th>
                        <th>LEVEL</th>
                        <th>STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tblAta as $item)
                    <tr>
                        <th>{{ $item->ATA }}</th>
                        <th>{{ $item->ATA_DESC }}</th>
                        <td>{{ $marepCountTwoMonthsAgo }}</td>
                        <td>{{ $marepCountBefore }}</td>
                        <td>{{ $marepCount }}</td>
                        <td>{{ $marep3Month }}</td>
                        <td>{{ $marep12Month }}</td>
                        <td>{{ number_format($marep2Rate, 2) }}</td>
                        <td>{{ number_format($marep1Rate, 2) }}</td>
                        <td>{{ number_format($marepRate, 2) }}</td>
                        <td>{{ number_format($marepRate3Month, 2) }}</td>
                        <td>{{ number_format($marepRate12Month, 2) }}</td>
                        <td>{{ number_format($marepAlertLevel, 2) }}</td>
                        <td>{{ $marepAlertStatus }}</td>
                        <td>{{ $marepTrend }}</td>
                    </tr>  
                    @endforeach
                </tbody>
            </table>
            <h6>NOTE :</h6>
            <h6>The Alert Level (AL) is based on monthly Technical Pilot Report / Maintenance Finding Report / Delay Rate of last Four Quarters (Average + 2 *STD)</h6>
            <h6>The Alert Status colomn will show "RED-1" if the last month Delay Rate exceed the AL, "RED-2" if this is true for the last two consecutive months,</h6>
            <h6>and "RED-3" if this is true for the last three consecutive months.</h6>
            <h6>The TREND colomn show an "UP" or "DOWN" when the rate has increased or decreased for 3 months</h6>
            <h6 class="issued">Issued by Citilink Engineering & Maintenance and Compiled by GMF Reliability Engineering & Services</h6>
        </div>

    {{-- Delay Report --}}
        <div>
            <table>
                <thead>
                    <tr>
                        <th colspan="2">{{ $aircraftType }}</th>
                        <th colspan="13">TECHNICAL DELAY > 15 MINUTES AND CANCELLATION</th>
                    </tr>
                    <tr>
                        <th colspan="15"></th>
                    </tr>
                    <tr>
                        <th colspan="2">Total Flight Hours</th>
                        <th>{{ round($flyingHours2Before) }}</th>
                        <th>{{ round($flyingHoursBefore) }}</th>
                        <th>{{ round($flyingHoursTotal) }}</th>
                        <th>{{ round($fh3Last) }}</th>
                        <th>{{ round($fh12Last) }}</th>
                        <th colspan="8"></th>
                    </tr>
                    <tr>
                        <th colspan="2" rowspan="2">ATA CHAPTER</th>
                        <th rowspan="2">{{ substr(\Carbon\Carbon::parse($period)->subMonths(2)->format('F'), 0, 3) }}</th>
                        <th rowspan="2">{{ substr(\Carbon\Carbon::parse($period)->subMonth(1)->format('F'), 0, 3) }}</th>
                        <th rowspan="2">{{ substr(\Carbon\Carbon::parse($period)->format('F'), 0, 3) }}</th>
                        <th>Last 3</th>
                        <th>Last 12</th>
                        <th>{{ substr(\Carbon\Carbon::parse($period)->subMonths(2)->format('F'), 0, 3) }}</th>
                        <th>{{ substr(\Carbon\Carbon::parse($period)->subMonth(1)->format('F'), 0, 3) }}</th>
                        <th>{{ substr(\Carbon\Carbon::parse($period)->format('F'), 0, 3) }}</th>
                        <th>3 Months</th>
                        <th>12 Months</th>
                        <th>ALERT</th>
                        <th>ALERT</th>
                        <th rowspan="2">TREND</th>
                    </tr>
                    <tr>
                        <th>Months</th>
                        <th>Months</th>
                        <th>RATE</th>
                        <th>RATE</th>
                        <th>RATE</th>
                        <th>RATE</th>
                        <th>RATE</th>
                        <th>LEVEL</th>
                        <th>STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tblAta as $item)
                    <tr>
                        <th>{{ $item->ATA }}</th>
                        <th>{{ $item->ATA_DESC }}</th>
                        <td>{{ $delayCountTwoMonthsAgo }}</td>
                        <td>{{ $delayCountBefore }}</td>
                        <td>{{ $delayCount }}</td>
                        <td>{{ $delay3Month }}</td>
                        <td>{{ $delay12Month }}</td>
                        <td>{{ number_format($delay2Rate, 2) }}</td>
                        <td>{{ number_format($delay1Rate, 2) }}</td>
                        <td>{{ number_format($delayRate, 2) }}</td>
                        <td>{{ number_format($delayRate3Month, 2) }}</td>
                        <td>{{ number_format($delayRate12Month, 2) }}</td>
                        <td>{{ number_format($delayAlertLevel, 2) }}</td>
                        <td>{{ $delayAlertStatus }}</td>
                        <td>{{ $delayTrend }}</td>
                    </tr>  
                    @endforeach
                </tbody>
            </table>
            <h6>NOTE :</h6>
            <h6>The Alert Level (AL) is based on monthly Technical Pilot Report / Maintenance Finding Report / Delay Rate of last Four Quarters (Average + 2 *STD)</h6>
            <h6>The Alert Status colomn will show "RED-1" if the last month Delay Rate exceed the AL, "RED-2" if this is true for the last two consecutive months,</h6>
            <h6>and "RED-3" if this is true for the last three consecutive months.</h6>
            <h6>The TREND colomn show an "UP" or "DOWN" when the rate has increased or decreased for 3 months</h6>
            <h6 class="issued">Issued by Citilink Engineering & Maintenance and Compiled by GMF Reliability Engineering & Services</h6>
        </div>
</body>
</html>