{{-- filepath: c:\Users\Noval Rais\Documents\Github Repository\GMF-Reliability\resources\views\pdf\pilot-pdf.blade.php --}}
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
            font-size: 12px;
            text-align: center;
        }
        th, td {
            border: 1px solid black;
            padding: 5px;
        }
        h6 {
            font-size: 12px; 
            text-align: left;
            margin: 5px;
        }
        .issued {
            text-align: right;
            margin: 6px;
        }
        .alert-red {
            background-color: red;
            color: white;
        }
    </style>
</head>
<body>
    @php
    // Helper function untuk menghilangkan trailing zero
    function formatNumber($value, $decimals = 2) {
        return rtrim(rtrim(number_format($value, $decimals), '0'), '.');
    }
    @endphp

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
                {{-- PILOT REPORT DATA dengan formatNumber --}}
                @foreach ($reportPerAta as $report)
                <tr>
                    <th>{{ $report['ata'] }}</th>
                    <th>{{ $report['ata_name'] }}</th>
                    <td>{{ $report['pirepCountTwoMonthsAgo'] }}</td>
                    <td>{{ $report['pirepCountBefore'] }}</td>
                    <td>{{ $report['pirepCount'] }}</td>
                    <td>{{ $report['pirep3Month'] }}</td>
                    <td>{{ $report['pirep12Month'] }}</td>
                    <td>{{ formatNumber($report['pirep2Rate']) }}</td>
                    <td>{{ formatNumber($report['pirep1Rate']) }}</td>
                    <td>{{ formatNumber($report['pirepRate']) }}</td>
                    <td>{{ formatNumber($report['pirepRate3Month']) }}</td>
                    <td>{{ formatNumber($report['pirepRate12Month']) }}</td>
                    <td>{{ formatNumber($report['pirepAlertLevel']) }}</td>
                    <td>{{ $report['pirepAlertStatus'] }}</td>
                    <td>{{ $report['pirepTrend'] }}</td>
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
    <div style="page-break-before: always;">
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
                {{-- MAINTENANCE REPORT DATA dengan formatNumber --}}
                @foreach ($reportPerAta as $report)
                <tr>
                    <th>{{ $report['ata'] }}</th>
                    <th>{{ $report['ata_name'] }}</th>
                    <td>{{ $report['marepCountTwoMonthsAgo'] }}</td>
                    <td>{{ $report['marepCountBefore'] }}</td>
                    <td>{{ $report['marepCount'] }}</td>
                    <td>{{ $report['marep3Month'] }}</td>
                    <td>{{ $report['marep12Month'] }}</td>
                    <td>{{ formatNumber($report['marep2Rate']) }}</td>
                    <td>{{ formatNumber($report['marep1Rate']) }}</td>
                    <td>{{ formatNumber($report['marepRate']) }}</td>
                    <td>{{ formatNumber($report['marepRate3Month']) }}</td>
                    <td>{{ formatNumber($report['marepRate12Month']) }}</td>
                    <td>{{ formatNumber($report['marepAlertLevel']) }}</td>
                    <td>{{ $report['marepAlertStatus'] }}</td>
                    <td>{{ $report['marepTrend'] }}</td>
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
    <div style="page-break-before: always;">
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
                    <th colspan="2">Total Flight Cycles</th>  
                    <th>{{ round($flyingCycles2Before) }}</th>    
                    <th>{{ round($flyingCyclesBefore) }}</th>     
                    <th>{{ round($flyingCyclesTotal) }}</th>      
                    <th>{{ round($fc3Last) }}</th>                
                    <th>{{ round($fc12Last) }}</th>               
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
                {{-- DELAY REPORT DATA dengan formatNumber --}}
                @foreach ($reportPerAta as $row)
                <tr>
                    <th>{{ $row['ata'] }}</th>
                    <th>{{ $row['ata_name'] ?? '' }}</th>
                    <td>{{ $row['delayCountTwoMonthsAgo'] }}</td>
                    <td>{{ $row['delayCountBefore'] }}</td>
                    <td>{{ $row['delayCount'] }}</td>
                    <td>{{ $row['delay3Month'] }}</td>
                    <td>{{ $row['delay12Month'] }}</td>
                    <td>{{ formatNumber($row['delay2Rate']) }}</td>
                    <td>{{ formatNumber($row['delay1Rate']) }}</td>
                    <td>{{ formatNumber($row['delayRate']) }}</td>
                    <td>{{ formatNumber($row['delayRate3Month']) }}</td>
                    <td>{{ formatNumber($row['delayRate12Month']) }}</td>
                    <td>{{ formatNumber($row['delayAlertLevel']) }}</td>
                    <td>{{ $row['delayAlertStatus'] }}</td>
                    <td>{{ $row['delayTrend'] }}</td>
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