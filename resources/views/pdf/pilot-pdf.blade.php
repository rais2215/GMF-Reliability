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
            font-size: 10px;
            margin: 0;
            padding: 0;
        }

        /* Table Base Styles */
        table {
            border-collapse: collapse;
            width: 100%;
            font-family: Arial, sans-serif;
            font-size: 10px;
        }

        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            font-size: 9px;
            word-wrap: break-word;
            line-height: 1.2;
            vertical-align: middle;
        }

        /* Typography Components */
        h6 {
            font-size: 11px;
            text-align: left;
            margin: 4px;
            line-height: 1.3;
        }

        .issued {
            text-align: right;
            margin: 5px;
        }

        /* Specialized Table Cell Styles */
        .ata-name {
            text-align: left;
            font-weight: bold;
            font-size: 8px;
            max-width: 110px;
        }

        /* Notes Section Styling */
        .notes-section {
            margin-top: 15px;
            font-size: 11px;
        }

        .notes-section h6 {
            font-size: 11px;
            margin: 2px 0;
            line-height: 1.2;
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
    $formatNumber = $formatNumber ?? function($value, $decimals = 2) {
        if (is_null($value) || !is_numeric($value)) {
            return '0';
        }
        $numValue = floatval($value);
        return rtrim(rtrim(number_format($numValue, $decimals, '.', ''), '0'), '.');
    };
    @endphp

    {{-- ===================================
         PILOT REPORT SECTION
         Technical Pilot Report Analysis by ATA Chapter
         =================================== --}}
    <div>
        <table>
            <thead>
                <!-- Report Header -->
                <tr>
                    <th colspan="2">{{ $aircraftType ?? 'N/A' }}</th>
                    <th colspan="13">PILOT REPORT</th>
                </tr>

                <!-- Flight Hours Summary -->
                <tr>
                    <th colspan="2">Total Flight Hours</th>
                    <th>{{ round($flyingHours2Before ?? 0) }}</th>
                    <th>{{ round($flyingHoursBefore ?? 0) }}</th>
                    <th>{{ round($flyingHoursTotal ?? 0) }}</th>
                    <th>{{ round($fh3Last ?? 0) }}</th>
                    <th>{{ round($fh12Last ?? 0) }}</th>
                    <th colspan="8"></th>
                </tr>

                <!-- Column Headers with Period Information -->
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
                <!-- Pilot Report Data by ATA Chapter -->
                @if(isset($reportPerAta) && is_array($reportPerAta))
                    @foreach ($reportPerAta as $report)
                    <tr>
                        <th>{{ $report['ata'] ?? '' }}</th>
                        <th class="ata-name">{{ $report['ata_name'] ?? '' }}</th>
                        <td>{{ $report['pirepCountTwoMonthsAgo'] ?? 0 }}</td>
                        <td>{{ $report['pirepCountBefore'] ?? 0 }}</td>
                        <td>{{ $report['pirepCount'] ?? 0 }}</td>
                        <td>{{ $report['pirep3Month'] ?? 0 }}</td>
                        <td>{{ $report['pirep12Month'] ?? 0 }}</td>
                        <td>{{ $formatNumber($report['pirep2Rate'] ?? 0) }}</td>
                        <td>{{ $formatNumber($report['pirep1Rate'] ?? 0) }}</td>
                        <td>{{ $formatNumber($report['pirepRate'] ?? 0) }}</td>
                        <td>{{ $formatNumber($report['pirepRate3Month'] ?? 0) }}</td>
                        <td>{{ $formatNumber($report['pirepRate12Month'] ?? 0) }}</td>
                        <td>{{ $formatNumber($report['pirepAlertLevel'] ?? 0) }}</td>
                        <td>{{ $report['pirepAlertStatus'] ?? '' }}</td>
                        <td>{{ $report['pirepTrend'] ?? '' }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="15" style="text-align: center; padding: 20px;">No data available</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- Report Notes and Documentation -->
        <div class="notes-section">
            <h6>NOTE :</h6>
        </div>
        <h6>The Alert Level (AL) is based on monthly Technical Pilot Report / Maintenance Finding Report / Delay Rate of last Four Quarters (Average + 2 *STD)</h6>
        <h6>The Alert Status colomn will show "RED-1" if the last month Delay Rate exceed the AL, "RED-2" if this is true for the last two consecutive months,</h6>
        <h6>and "RED-3" if this is true for the last three consecutive months.</h6>
        <h6>The TREND colomn show an "UP" or "DOWN" when the rate has increased or decreased for 3 months</h6>
        <h6 class="issued">Issued by JKTMQGA and Compiled by GMF Reliability Engineering & Services</h6>
    </div>

    {{-- ===================================
         MAINTENANCE FINDING REPORT SECTION
         Maintenance Finding Report Analysis by ATA Chapter
         =================================== --}}
    <div style="page-break-before: always;">
        <table>
            <thead>
                <!-- Report Header -->
                <tr>
                    <th colspan="2">{{ $aircraftType ?? 'N/A' }}</th>
                    <th colspan="13">MAINTENANCE FINDING REPORT</th>
                </tr>

                <!-- Flight Hours Summary -->
                <tr>
                    <th colspan="2">Total Flight Hours</th>
                    <th>{{ round($flyingHours2Before ?? 0) }}</th>
                    <th>{{ round($flyingHoursBefore ?? 0) }}</th>
                    <th>{{ round($flyingHoursTotal ?? 0) }}</th>
                    <th>{{ round($fh3Last ?? 0) }}</th>
                    <th>{{ round($fh12Last ?? 0) }}</th>
                    <th colspan="8"></th>
                </tr>

                <!-- Column Headers with Period Information -->
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
                <!-- Maintenance Finding Report Data by ATA Chapter -->
                @if(isset($reportPerAta) && is_array($reportPerAta))
                    @foreach ($reportPerAta as $report)
                    <tr>
                        <th>{{ $report['ata'] ?? '' }}</th>
                        <th class="ata-name">{{ $report['ata_name'] ?? '' }}</th>
                        <td>{{ $report['marepCountTwoMonthsAgo'] ?? 0 }}</td>
                        <td>{{ $report['marepCountBefore'] ?? 0 }}</td>
                        <td>{{ $report['marepCount'] ?? 0 }}</td>
                        <td>{{ $report['marep3Month'] ?? 0 }}</td>
                        <td>{{ $report['marep12Month'] ?? 0 }}</td>
                        <td>{{ $formatNumber($report['marep2Rate'] ?? 0) }}</td>
                        <td>{{ $formatNumber($report['marep1Rate'] ?? 0) }}</td>
                        <td>{{ $formatNumber($report['marepRate'] ?? 0) }}</td>
                        <td>{{ $formatNumber($report['marepRate3Month'] ?? 0) }}</td>
                        <td>{{ $formatNumber($report['marepRate12Month'] ?? 0) }}</td>
                        <td>{{ $formatNumber($report['marepAlertLevel'] ?? 0) }}</td>
                        <td>{{ $report['marepAlertStatus'] ?? '' }}</td>
                        <td>{{ $report['marepTrend'] ?? '' }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="15" style="text-align: center; padding: 20px;">No data available</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- Report Notes and Documentation -->
        <div class="notes-section">
            <h6>NOTE :</h6>
        </div>
        <h6>The Alert Level (AL) is based on monthly Technical Pilot Report / Maintenance Finding Report / Delay Rate of last Four Quarters (Average + 2 *STD)</h6>
        <h6>The Alert Status colomn will show "RED-1" if the last month Delay Rate exceed the AL, "RED-2" if this is true for the last two consecutive months,</h6>
        <h6>and "RED-3" if this is true for the last three consecutive months.</h6>
        <h6>The TREND colomn show an "UP" or "DOWN" when the rate has increased or decreased for 3 months</h6>
        <h6 class="issued">Issued by JKTMQGA and Compiled by GMF Reliability Engineering & Services</h6>
    </div>

    {{-- ===================================
         TECHNICAL DELAY REPORT SECTION
         Technical Delay > 15 Minutes and Cancellation Analysis
         =================================== --}}
    <div style="page-break-before: always;">
        <table>
            <thead>
                <!-- Report Header -->
                <tr>
                    <th colspan="2">{{ $aircraftType ?? 'N/A' }}</th>
                    <th colspan="13">TECHNICAL DELAY > 15 MINUTES AND CANCELLATION</th>
                </tr>

                <!-- Flight Cycles Summary -->
                <tr>
                    <th colspan="2">Total Flight Cycles</th>
                    <th>{{ round($flyingCycles2Before ?? 0) }}</th>
                    <th>{{ round($flyingCyclesBefore ?? 0) }}</th>
                    <th>{{ round($flyingCyclesTotal ?? 0) }}</th>
                    <th>{{ round($fc3Last ?? 0) }}</th>
                    <th>{{ round($fc12Last ?? 0) }}</th>
                    <th colspan="8"></th>
                </tr>

                <!-- Column Headers with Period Information -->
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
                <!-- Technical Delay Report Data by ATA Chapter -->
                @if(isset($reportPerAta) && is_array($reportPerAta))
                    @foreach ($reportPerAta as $row)
                    <tr>
                        <th>{{ $row['ata'] ?? '' }}</th>
                        <th class="ata-name">{{ $row['ata_name'] ?? '' }}</th>
                        <td>{{ $row['delayCountTwoMonthsAgo'] ?? 0 }}</td>
                        <td>{{ $row['delayCountBefore'] ?? 0 }}</td>
                        <td>{{ $row['delayCount'] ?? 0 }}</td>
                        <td>{{ $row['delay3Month'] ?? 0 }}</td>
                        <td>{{ $row['delay12Month'] ?? 0 }}</td>
                        <td>{{ $formatNumber($row['delay2Rate'] ?? 0) }}</td>
                        <td>{{ $formatNumber($row['delay1Rate'] ?? 0) }}</td>
                        <td>{{ $formatNumber($row['delayRate'] ?? 0) }}</td>
                        <td>{{ $formatNumber($row['delayRate3Month'] ?? 0) }}</td>
                        <td>{{ $formatNumber($row['delayRate12Month'] ?? 0) }}</td>
                        <td>{{ $formatNumber($row['delayAlertLevel'] ?? 0) }}</td>
                        <td>{{ $row['delayAlertStatus'] ?? '' }}</td>
                        <td>{{ $row['delayTrend'] ?? '' }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="15" style="text-align: center; padding: 20px;">No data available</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- Report Notes and Documentation -->
        <div class="notes-section">
            <h6>NOTE :</h6>
        </div>
        <h6>The Alert Level (AL) is based on monthly Technical Pilot Report / Maintenance Finding Report / Delay Rate of last Four Quarters (Average + 2 *STD)</h6>
        <h6>The Alert Status colomn will show "RED-1" if the last month Delay Rate exceed the AL, "RED-2" if this is true for the last two consecutive months,</h6>
        <h6>and "RED-3" if this is true for the last three consecutive months.</h6>
        <h6>The TREND colomn show an "UP" or "DOWN" when the rate has increased or decreased for 3 months</h6>
        <h6 class="issued">Issued by JKTMQGA and Compiled by GMF Reliability Engineering & Services</h6>
    </div>
</body>
</html>
