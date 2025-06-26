<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PILOT PDF</title>
    <style>
        /* PDF Page Configuration */
        @page {
            size: A4 portrait;
            margin: 10mm;
        }
        
        /* Base Typography and Layout */
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            line-height: 1.4;
        }
        
        /* Table Base Styles */
        table {
            border-collapse: collapse;
            width: 100%;
            font-family: Arial, sans-serif;
            font-size: 11px;
        }
        
        th, td {
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: center;
            font-size: 10px;
            word-wrap: break-word;
            line-height: 1.3;
            vertical-align: middle;
        }
        
        /* Typography Components */
        h6 {
            font-size: 12px;
            text-align: left;
            margin: 6px 4px;
            line-height: 1.3;
        }
        
        .issued {
            text-align: right;
            margin: 8px;
            font-size: 11px;
        }
        
        /* Alert Styling */
        .alert-red {
            background-color: red;
            color: white;
        }
        
        /* Specialized Table Cell Styles */
        .ata-name {
            text-align: left;
            font-weight: bold;
            font-size: 9px;
            max-width: 120px;
            padding: 6px 4px;
        }
        
        /* Responsive Table Layout */
        .table-responsive {
            overflow-x: auto;
            font-size: 11px;
            margin-bottom: 20px;
        }
        
        .table-responsive table {
            min-width: 100%;
            font-size: 11px;
        }
        
        .table-responsive th,
        .table-responsive td {
            padding: 6px 4px;
            font-size: 10px;
            line-height: 1.3;
        }
        
        /* Compact Table for Dense Data Display */
        .compact-table {
            margin-bottom: 20px;
        }
        
        .compact-table th,
        .compact-table td {
            padding: 6px 4px;
            font-size: 10px;
            line-height: 1.2;
        }
        
        /* Header cells larger font */
        .compact-table thead th {
            font-size: 11px;
            padding: 6px 4px;
            line-height: 1.3;
        }
        
        /* Notes Section Styling */
        .notes-section {
            margin-top: 20px;
            font-size: 12px;
        }
        
        .notes-section h6 {
            font-size: 12px;
            margin: 4px 0;
            line-height: 1.3;
        }
        
        /* Optimize space usage */
        .page-section {
            min-height: calc(100vh - 40mm);
            page-break-inside: avoid;
        }
        
        /* Tighter spacing for table rows */
        tbody tr {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    @php
    /**
     * Helper function for number formatting
     * Removes trailing zeros and decimal points for cleaner display
     * 
     * @param float $value The numeric value to format
     * @param int $decimals Number of decimal places
     * @return string Formatted number
     */
    function formatNumber($value, $decimals = 2) {
        if (is_null($value) || !is_numeric($value)) {
            return '0';
        }
        $numValue = floatval($value);
        return rtrim(rtrim(number_format($numValue, $decimals, '.', ''), '0'), '.');
    }
    @endphp

    {{-- =================================== 
         PILOT REPORT SECTION
         Technical Pilot Report Analysis by ATA Chapter
         =================================== --}}
    <div class="table-responsive page-section">
        <table class="compact-table">
            <thead>
                <!-- Report Header -->
                <tr>
                    <th colspan="2" style="font-size: 12px; font-weight: bold;">{{ $aircraftType }}</th>
                    <th colspan="13" style="font-size: 13px; font-weight: bold;">PILOT REPORT</th>
                </tr>
                
                <!-- Flight Hours Summary -->
                <tr>
                    <th colspan="2" style="font-size: 11px;">Total Flight Hours</th>
                    <th style="font-size: 11px;">{{ round($flyingHours2Before) }}</th>
                    <th style="font-size: 11px;">{{ round($flyingHoursBefore) }}</th>
                    <th style="font-size: 11px;">{{ round($flyingHoursTotal) }}</th>
                    <th style="font-size: 11px;">{{ round($fh3Last) }}</th>
                    <th style="font-size: 11px;">{{ round($fh12Last) }}</th>
                    <th colspan="8"></th>
                </tr>
                
                <!-- Column Headers with Period Information -->
                <tr>
                    <th colspan="2" rowspan="2" style="font-size: 10px; line-height: 1.2;">ATA CHAPTER</th>
                    <th rowspan="2" style="font-size: 10px;">{{ substr(\Carbon\Carbon::parse($period)->subMonths(2)->format('F'), 0, 3) }}</th>
                    <th rowspan="2" style="font-size: 10px;">{{ substr(\Carbon\Carbon::parse($period)->subMonth(1)->format('F'), 0, 3) }}</th>
                    <th rowspan="2" style="font-size: 10px;">{{ substr(\Carbon\Carbon::parse($period)->format('F'), 0, 3) }}</th>
                    <th style="font-size: 9px;">Last 3</th>
                    <th style="font-size: 9px;">Last 12</th>
                    <th style="font-size: 9px;">{{ substr(\Carbon\Carbon::parse($period)->subMonths(2)->format('F'), 0, 3) }}</th>
                    <th style="font-size: 9px;">{{ substr(\Carbon\Carbon::parse($period)->subMonth(1)->format('F'), 0, 3) }}</th>
                    <th style="font-size: 9px;">{{ substr(\Carbon\Carbon::parse($period)->format('F'), 0, 3) }}</th>
                    <th style="font-size: 9px;">3 Months</th>
                    <th style="font-size: 9px;">12 Months</th>
                    <th style="font-size: 9px;">ALERT</th>
                    <th style="font-size: 9px;">ALERT</th>
                    <th rowspan="2" style="font-size: 10px;">TREND</th>
                </tr>
                <tr>
                    <th style="font-size: 9px;">Months</th>
                    <th style="font-size: 9px;">Months</th>
                    <th style="font-size: 9px;">RATE</th>
                    <th style="font-size: 9px;">RATE</th>
                    <th style="font-size: 9px;">RATE</th>
                    <th style="font-size: 9px;">RATE</th>
                    <th style="font-size: 9px;">RATE</th>
                    <th style="font-size: 9px;">LEVEL</th>
                    <th style="font-size: 9px;">STATUS</th>
                </tr>
            </thead>
            <tbody>
                <!-- Pilot Report Data by ATA Chapter -->
                @foreach ($reportPerAta as $report)
                <tr>
                    <th style="font-size: 10px;">{{ $report['ata'] }}</th>
                    <th class="ata-name">{{ $report['ata_name'] }}</th>
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
        
        <!-- Report Notes and Documentation -->
        <div class="notes-section">
            <h6><strong>NOTE :</strong></h6>
        </div>
        <h6>The Alert Level (AL) is based on monthly Technical Pilot Report / Maintenance Finding Report / Delay Rate of last Four Quarters (Average + 2 *STD)</h6>
        <h6>The Alert Status column will show "RED-1" if the last month Delay Rate exceed the AL, "RED-2" if this is true for the last two consecutive months,</h6>
        <h6>and "RED-3" if this is true for the last three consecutive months.</h6>
        <h6>The TREND column show an "UP" or "DOWN" when the rate has increased or decreased for 3 months</h6>
        <h6 class="issued">Issued by JKTMQGA and Compiled by GMF Reliability Engineering & Services</h6>
    </div>

    {{-- =================================== 
         MAINTENANCE FINDING REPORT SECTION
         Maintenance Finding Report Analysis by ATA Chapter
         =================================== --}}
    <div style="page-break-before: always;" class="table-responsive page-section">
        <table class="compact-table">
            <thead>
                <!-- Report Header -->
                <tr>
                    <th colspan="2" style="font-size: 12px; font-weight: bold;">{{ $aircraftType }}</th>
                    <th colspan="13" style="font-size: 13px; font-weight: bold;">MAINTENANCE FINDING REPORT</th>
                </tr>
                
                <!-- Flight Hours Summary -->
                <tr>
                    <th colspan="2" style="font-size: 11px;">Total Flight Hours</th>
                    <th style="font-size: 11px;">{{ round($flyingHours2Before) }}</th>
                    <th style="font-size: 11px;">{{ round($flyingHoursBefore) }}</th>
                    <th style="font-size: 11px;">{{ round($flyingHoursTotal) }}</th>
                    <th style="font-size: 11px;">{{ round($fh3Last) }}</th>
                    <th style="font-size: 11px;">{{ round($fh12Last) }}</th>
                    <th colspan="8"></th>
                </tr>
                
                <!-- Column Headers with Period Information -->
                <tr>
                    <th colspan="2" rowspan="2" style="font-size: 10px; line-height: 1.2;">ATA CHAPTER</th>
                    <th rowspan="2" style="font-size: 10px;">{{ substr(\Carbon\Carbon::parse($period)->subMonths(2)->format('F'), 0, 3) }}</th>
                    <th rowspan="2" style="font-size: 10px;">{{ substr(\Carbon\Carbon::parse($period)->subMonth(1)->format('F'), 0, 3) }}</th>
                    <th rowspan="2" style="font-size: 10px;">{{ substr(\Carbon\Carbon::parse($period)->format('F'), 0, 3) }}</th>
                    <th style="font-size: 9px;">Last 3</th>
                    <th style="font-size: 9px;">Last 12</th>
                    <th style="font-size: 9px;">{{ substr(\Carbon\Carbon::parse($period)->subMonths(2)->format('F'), 0, 3) }}</th>
                    <th style="font-size: 9px;">{{ substr(\Carbon\Carbon::parse($period)->subMonth(1)->format('F'), 0, 3) }}</th>
                    <th style="font-size: 9px;">{{ substr(\Carbon\Carbon::parse($period)->format('F'), 0, 3) }}</th>
                    <th style="font-size: 9px;">3 Months</th>
                    <th style="font-size: 9px;">12 Months</th>
                    <th style="font-size: 9px;">ALERT</th>
                    <th style="font-size: 9px;">ALERT</th>
                    <th rowspan="2" style="font-size: 10px;">TREND</th>
                </tr>
                <tr>
                    <th style="font-size: 9px;">Months</th>
                    <th style="font-size: 9px;">Months</th>
                    <th style="font-size: 9px;">RATE</th>
                    <th style="font-size: 9px;">RATE</th>
                    <th style="font-size: 9px;">RATE</th>
                    <th style="font-size: 9px;">RATE</th>
                    <th style="font-size: 9px;">RATE</th>
                    <th style="font-size: 9px;">LEVEL</th>
                    <th style="font-size: 9px;">STATUS</th>
                </tr>
            </thead>
            <tbody>
                <!-- Maintenance Finding Report Data by ATA Chapter -->
                @foreach ($reportPerAta as $report)
                <tr>
                    <th style="font-size: 10px;">{{ $report['ata'] }}</th>
                    <th class="ata-name">{{ $report['ata_name'] }}</th>
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
        
        <!-- Report Notes and Documentation -->
        <div class="notes-section">
            <h6><strong>NOTE :</strong></h6>
        </div>
        <h6>The Alert Level (AL) is based on monthly Technical Pilot Report / Maintenance Finding Report / Delay Rate of last Four Quarters (Average + 2 *STD)</h6>
        <h6>The Alert Status column will show "RED-1" if the last month Delay Rate exceed the AL, "RED-2" if this is true for the last two consecutive months,</h6>
        <h6>and "RED-3" if this is true for the last three consecutive months.</h6>
        <h6>The TREND column show an "UP" or "DOWN" when the rate has increased or decreased for 3 months</h6>
        <h6 class="issued">Issued by JKTMQGA and Compiled by GMF Reliability Engineering & Services</h6>
    </div>

    {{-- =================================== 
         TECHNICAL DELAY REPORT SECTION
         Technical Delay > 15 Minutes and Cancellation Analysis
         =================================== --}}
    <div style="page-break-before: always;" class="table-responsive page-section">
        <table class="compact-table">
            <thead>
                <!-- Report Header -->
                <tr>
                    <th colspan="2" style="font-size: 12px; font-weight: bold;">{{ $aircraftType }}</th>
                    <th colspan="13" style="font-size: 13px; font-weight: bold;">TECHNICAL DELAY > 15 MINUTES AND CANCELLATION</th>
                </tr>
                
                <!-- Flight Cycles Summary -->
                <tr>
                    <th colspan="2" style="font-size: 11px;">Total Flight Cycles</th>  
                    <th style="font-size: 11px;">{{ round($flyingCycles2Before) }}</th>    
                    <th style="font-size: 11px;">{{ round($flyingCyclesBefore) }}</th>     
                    <th style="font-size: 11px;">{{ round($flyingCyclesTotal) }}</th>      
                    <th style="font-size: 11px;">{{ round($fc3Last) }}</th>                
                    <th style="font-size: 11px;">{{ round($fc12Last) }}</th>               
                    <th colspan="8"></th>
                </tr>
                
                <!-- Column Headers with Period Information -->
                <tr>
                    <th colspan="2" rowspan="2" style="font-size: 10px; line-height: 1.2;">ATA CHAPTER</th>
                    <th rowspan="2" style="font-size: 10px;">{{ substr(\Carbon\Carbon::parse($period)->subMonths(2)->format('F'), 0, 3) }}</th>
                    <th rowspan="2" style="font-size: 10px;">{{ substr(\Carbon\Carbon::parse($period)->subMonth(1)->format('F'), 0, 3) }}</th>
                    <th rowspan="2" style="font-size: 10px;">{{ substr(\Carbon\Carbon::parse($period)->format('F'), 0, 3) }}</th>
                    <th style="font-size: 9px;">Last 3</th>
                    <th style="font-size: 9px;">Last 12</th>
                    <th style="font-size: 9px;">{{ substr(\Carbon\Carbon::parse($period)->subMonths(2)->format('F'), 0, 3) }}</th>
                    <th style="font-size: 9px;">{{ substr(\Carbon\Carbon::parse($period)->subMonth(1)->format('F'), 0, 3) }}</th>
                    <th style="font-size: 9px;">{{ substr(\Carbon\Carbon::parse($period)->format('F'), 0, 3) }}</th>
                    <th style="font-size: 9px;">3 Months</th>
                    <th style="font-size: 9px;">12 Months</th>
                    <th style="font-size: 9px;">ALERT</th>
                    <th style="font-size: 9px;">ALERT</th>
                    <th rowspan="2" style="font-size: 10px;">TREND</th>
                </tr>
                <tr>
                    <th style="font-size: 9px;">Months</th>
                    <th style="font-size: 9px;">Months</th>
                    <th style="font-size: 9px;">RATE</th>
                    <th style="font-size: 9px;">RATE</th>
                    <th style="font-size: 9px;">RATE</th>
                    <th style="font-size: 9px;">RATE</th>
                    <th style="font-size: 9px;">RATE</th>
                    <th style="font-size: 9px;">LEVEL</th>
                    <th style="font-size: 9px;">STATUS</th>
                </tr>
            </thead>
            <tbody>
                <!-- Technical Delay Report Data by ATA Chapter -->
                @foreach ($reportPerAta as $row)
                <tr>
                    <th style="font-size: 10px;">{{ $row['ata'] }}</th>
                    <th class="ata-name">{{ $row['ata_name'] ?? '' }}</th>
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
        
        <!-- Report Notes and Documentation -->
        <div class="notes-section">
            <h6><strong>NOTE :</strong></h6>
        </div>
        <h6>The Alert Level (AL) is based on monthly Technical Pilot Report / Maintenance Finding Report / Delay Rate of last Four Quarters (Average + 2 *STD)</h6>
        <h6>The Alert Status column will show "RED-1" if the last month Delay Rate exceed the AL, "RED-2" if this is true for the last two consecutive months,</h6>
        <h6>and "RED-3" if this is true for the last three consecutive months.</h6>
        <h6>The TREND column show an "UP" or "DOWN" when the rate has increased or decreased for 3 months</h6>
        <h6 class="issued">Issued by JKTMQGA and Compiled by GMF Reliability Engineering & Services</h6>
    </div>
</body>
</html>