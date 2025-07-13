@php
function formatNumber($value, $decimals = 2) {
    return rtrim(rtrim(number_format($value, $decimals), '0'), '.');
}
@endphp

<table border="1" style="border-collapse: collapse; border: 1px solid #000;">
    <thead>
        {{-- Header Title --}}
        <tr>
            <th colspan="15" style="text-align: center; font-weight: bold; font-size: 16px; background-color: #4CAF50; color: white; border: 1px solid #000;">
                PILOT REPORT - {{ strtoupper($aircraftType) }} - {{ \Carbon\Carbon::parse($period)->subYears(1)->format('Y') }}-{{ \Carbon\Carbon::parse($period)->format('Y') }}
            </th>
        </tr>
        <tr></tr>

        {{-- Pilot Report Section --}}
        <tr>
            <th colspan="15" style="text-align: center; font-weight: bold; font-size: 14px; background-color: #c8e6c9; border: 1px solid #000;">
                PILOT REPORT
            </th>
        </tr>

        {{-- Flying Hours Info --}}
        <tr>
            <th colspan="2" style="font-weight: bold; background-color: #e8f5e8; border: 1px solid #000;">Total Flight Hours</th>
            <th style="text-align: center; font-weight: bold; background-color: #e8f5e8; border: 1px solid #000;">{{ round($flyingHours2Before) }}</th>
            <th style="text-align: center; font-weight: bold; background-color: #e8f5e8; border: 1px solid #000;">{{ round($flyingHoursBefore) }}</th>
            <th style="text-align: center; font-weight: bold; background-color: #e8f5e8; border: 1px solid #000;">{{ round($flyingHoursTotal) }}</th>
            <th style="text-align: center; font-weight: bold; background-color: #e8f5e8; border: 1px solid #000;">{{ round($fh3Last) }}</th>
            <th style="text-align: center; font-weight: bold; background-color: #e8f5e8; border: 1px solid #000;">{{ round($fh12Last) }}</th>
            <th colspan="8" style="background-color: #e8f5e8; border: 1px solid #000;"></th>
        </tr>

        {{-- Header Row 1 --}}
        <tr>
            <th colspan="2" rowspan="2" style="text-align: center; font-weight: bold; background-color: #f0f0f0; border: 1px solid #000;">ATA CHAPTER</th>
            <th rowspan="2" style="text-align: center; font-weight: bold; background-color: #f0f0f0; border: 1px solid #000;">{{ substr(\Carbon\Carbon::parse($period)->subMonths(2)->format('F'), 0, 3) }}</th>
            <th rowspan="2" style="text-align: center; font-weight: bold; background-color: #f0f0f0; border: 1px solid #000;">{{ substr(\Carbon\Carbon::parse($period)->subMonth(1)->format('F'), 0, 3) }}</th>
            <th rowspan="2" style="text-align: center; font-weight: bold; background-color: #f0f0f0; border: 1px solid #000;">{{ substr(\Carbon\Carbon::parse($period)->format('F'), 0, 3) }}</th>
            <th style="text-align: center; font-weight: bold; background-color: #f0f0f0; border: 1px solid #000;">Last 3</th>
            <th style="text-align: center; font-weight: bold; background-color: #f0f0f0; border: 1px solid #000;">Last 12</th>
            <th style="text-align: center; font-weight: bold; background-color: #f0f0f0; border: 1px solid #000;">{{ substr(\Carbon\Carbon::parse($period)->subMonths(2)->format('F'), 0, 3) }}</th>
            <th style="text-align: center; font-weight: bold; background-color: #f0f0f0; border: 1px solid #000;">{{ substr(\Carbon\Carbon::parse($period)->subMonth(1)->format('F'), 0, 3) }}</th>
            <th style="text-align: center; font-weight: bold; background-color: #f0f0f0; border: 1px solid #000;">{{ substr(\Carbon\Carbon::parse($period)->format('F'), 0, 3) }}</th>
            <th style="text-align: center; font-weight: bold; background-color: #f0f0f0; border: 1px solid #000;">3 Months</th>
            <th style="text-align: center; font-weight: bold; background-color: #f0f0f0; border: 1px solid #000;">12 Months</th>
            <th style="text-align: center; font-weight: bold; background-color: #f0f0f0; border: 1px solid #000;">ALERT</th>
            <th style="text-align: center; font-weight: bold; background-color: #f0f0f0; border: 1px solid #000;">ALERT</th>
            <th rowspan="2" style="text-align: center; font-weight: bold; background-color: #f0f0f0; border: 1px solid #000;">TREND</th>
        </tr>

        {{-- Header Row 2 --}}
        <tr>
            <th style="text-align: center; font-weight: bold; background-color: #f0f0f0; border: 1px solid #000;">Months</th>
            <th style="text-align: center; font-weight: bold; background-color: #f0f0f0; border: 1px solid #000;">Months</th>
            <th style="text-align: center; font-weight: bold; background-color: #f0f0f0; border: 1px solid #000;">RATE</th>
            <th style="text-align: center; font-weight: bold; background-color: #f0f0f0; border: 1px solid #000;">RATE</th>
            <th style="text-align: center; font-weight: bold; background-color: #f0f0f0; border: 1px solid #000;">RATE</th>
            <th style="text-align: center; font-weight: bold; background-color: #f0f0f0; border: 1px solid #000;">RATE</th>
            <th style="text-align: center; font-weight: bold; background-color: #f0f0f0; border: 1px solid #000;">RATE</th>
            <th style="text-align: center; font-weight: bold; background-color: #f0f0f0; border: 1px solid #000;">LEVEL</th>
            <th style="text-align: center; font-weight: bold; background-color: #f0f0f0; border: 1px solid #000;">STATUS</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($reportPerAta as $row)
        <tr>
            <td style="text-align: center; font-weight: bold; border: 1px solid #000;">{{ $row['ata'] }}</td>
            <td style="font-weight: bold; border: 1px solid #000;">{{ $row['ata_name'] ?? '' }}</td>
            <td style="text-align: center; border: 1px solid #000;">{{ $row['pirepCountTwoMonthsAgo'] }}</td>
            <td style="text-align: center; border: 1px solid #000;">{{ $row['pirepCountBefore'] }}</td>
            <td style="text-align: center; border: 1px solid #000;">{{ $row['pirepCount'] }}</td>
            <td style="text-align: center; border: 1px solid #000;">{{ $row['pirep3Month'] }}</td>
            <td style="text-align: center; border: 1px solid #000;">{{ $row['pirep12Month'] }}</td>
            <td style="text-align: center; border: 1px solid #000;">{{ formatNumber($row['pirep2Rate']) }}</td>
            <td style="text-align: center; border: 1px solid #000;">{{ formatNumber($row['pirep1Rate']) }}</td>
            <td style="text-align: center; border: 1px solid #000;">{{ formatNumber($row['pirepRate']) }}</td>
            <td style="text-align: center; border: 1px solid #000;">{{ formatNumber($row['pirepRate3Month']) }}</td>
            <td style="text-align: center; border: 1px solid #000;">{{ formatNumber($row['pirepRate12Month']) }}</td>
            <td style="text-align: center; border: 1px solid #000;">{{ formatNumber($row['pirepAlertLevel']) }}</td>
            <td style="text-align: center; border: 1px solid #000;
                @if($row['pirepAlertStatus'] == 'RED-3') background-color: #ff6666; color: white;
                @elseif($row['pirepAlertStatus'] == 'RED-2') background-color: #ff9999; color: white;
                @elseif($row['pirepAlertStatus'] == 'RED-1') background-color: #ffcccc;
                @endif">
                {{ $row['pirepAlertStatus'] }}
            </td>
            <td style="text-align: center; border: 1px solid #000;">{{ $row['pirepTrend'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- Spacing --}}
<table>
    <tr><td colspan="15">&nbsp;</td></tr>
    <tr><td colspan="15">&nbsp;</td></tr>
</table>

{{-- Maintenance Report Section --}}
<table border="1" style="border-collapse: collapse; border: 1px solid #000;">
    <thead>
        <tr>
            <th colspan="15" style="text-align: center; font-weight: bold; font-size: 14px; background-color: #c8e6c9; border: 1px solid #000;">
                MAINTENANCE REPORT
            </th>
        </tr>

        {{-- Flying Hours Info --}}
        <tr>
            <th colspan="2" style="font-weight: bold; background-color: #e8f5e8; border: 1px solid #000;">Total Flight Hours</th>
            <th style="text-align: center; background-color: #e8f5e8; border: 1px solid #000;">{{ round($flyingHours2Before) }}</th>
            <th style="text-align: center; background-color: #e8f5e8; border: 1px solid #000;">{{ round($flyingHoursBefore) }}</th>
            <th style="text-align: center; background-color: #e8f5e8; border: 1px solid #000;">{{ round($flyingHoursTotal) }}</th>
            <th style="text-align: center; background-color: #e8f5e8; border: 1px solid #000;">{{ round($fh3Last) }}</th>
            <th style="text-align: center; background-color: #e8f5e8; border: 1px solid #000;">{{ round($fh12Last) }}</th>
            <th colspan="8" style="background-color: #e8f5e8; border: 1px solid #000;"></th>
        </tr>

        {{-- Header Row 1 --}}
        <tr>
            <th colspan="2" rowspan="2" style="text-align: center; font-weight: bold; background-color: #f0f0f0; border: 1px solid #000;">ATA CHAPTER</th>
            <th rowspan="2" style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">{{ substr(\Carbon\Carbon::parse($period)->subMonths(2)->format('F'), 0, 3) }}</th>
            <th rowspan="2" style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">{{ substr(\Carbon\Carbon::parse($period)->subMonth(1)->format('F'), 0, 3) }}</th>
            <th rowspan="2" style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">{{ substr(\Carbon\Carbon::parse($period)->format('F'), 0, 3) }}</th>
            <th style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">Last 3</th>
            <th style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">Last 12</th>
            <th style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">{{ substr(\Carbon\Carbon::parse($period)->subMonths(2)->format('F'), 0, 3) }}</th>
            <th style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">{{ substr(\Carbon\Carbon::parse($period)->subMonth(1)->format('F'), 0, 3) }}</th>
            <th style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">{{ substr(\Carbon\Carbon::parse($period)->format('F'), 0, 3) }}</th>
            <th style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">3 Months</th>
            <th style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">12 Months</th>
            <th style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">ALERT</th>
            <th style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">ALERT</th>
            <th rowspan="2" style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">TREND</th>
        </tr>

        {{-- Header Row 2 --}}
        <tr>
            <th style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">Months</th>
            <th style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">Months</th>
            <th style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">RATE</th>
            <th style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">RATE</th>
            <th style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">RATE</th>
            <th style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">RATE</th>
            <th style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">RATE</th>
            <th style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">LEVEL</th>
            <th style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">STATUS</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($reportPerAta as $row)
        <tr>
            <td style="text-align: center; font-weight: bold; border: 1px solid #000;">{{ $row['ata'] }}</td>
            <td style="font-weight: bold; border: 1px solid #000;">{{ $row['ata_name'] ?? '' }}</td>
            <td style="text-align: center; border: 1px solid #000;">{{ $row['marepCountTwoMonthsAgo'] }}</td>
            <td style="text-align: center; border: 1px solid #000;">{{ $row['marepCountBefore'] }}</td>
            <td style="text-align: center; border: 1px solid #000;">{{ $row['marepCount'] }}</td>
            <td style="text-align: center; border: 1px solid #000;">{{ $row['marep3Month'] }}</td>
            <td style="text-align: center; border: 1px solid #000;">{{ $row['marep12Month'] }}</td>
            <td style="text-align: center; border: 1px solid #000;">{{ formatNumber($row['marep2Rate']) }}</td>
            <td style="text-align: center; border: 1px solid #000;">{{ formatNumber($row['marep1Rate']) }}</td>
            <td style="text-align: center; border: 1px solid #000;">{{ formatNumber($row['marepRate']) }}</td>
            <td style="text-align: center; border: 1px solid #000;">{{ formatNumber($row['marepRate3Month']) }}</td>
            <td style="text-align: center; border: 1px solid #000;">{{ formatNumber($row['marepRate12Month']) }}</td>
            <td style="text-align: center; border: 1px solid #000;">{{ formatNumber($row['marepAlertLevel']) }}</td>
            <td style="text-align: center; border: 1px solid #000;
                @if($row['marepAlertStatus'] == 'RED-3') background-color: #ff6666; color: white;
                @elseif($row['marepAlertStatus'] == 'RED-2') background-color: #ff9999; color: white;
                @elseif($row['marepAlertStatus'] == 'RED-1') background-color: #ffcccc;
                @endif">
                {{ $row['marepAlertStatus'] }}
            </td>
            <td style="text-align: center; border: 1px solid #000;">{{ $row['marepTrend'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- Spacing --}}
<table>
    <tr><td colspan="15">&nbsp;</td></tr>
    <tr><td colspan="15">&nbsp;</td></tr>
</table>

{{-- Technical Delay Report Section --}}
<table border="1" style="border-collapse: collapse; border: 1px solid #000;">
    <thead>
        <tr>
            <th colspan="15" style="text-align: center; font-weight: bold; font-size: 14px; background-color: #c8e6c9; border: 1px solid #000;">
                TECHNICAL DELAY REPORT
            </th>
        </tr>

        {{-- Flying Cycles Info --}}
        <tr>
            <th colspan="2" style="font-weight: bold; background-color: #e8f5e8; border: 1px solid #000;">Total Flight Cycles</th>
            <th style="text-align: center; background-color: #e8f5e8; border: 1px solid #000;">{{ round($flyingCycles2Before) }}</th>
            <th style="text-align: center; background-color: #e8f5e8; border: 1px solid #000;">{{ round($flyingCyclesBefore) }}</th>
            <th style="text-align: center; background-color: #e8f5e8; border: 1px solid #000;">{{ round($flyingCyclesTotal) }}</th>
            <th style="text-align: center; background-color: #e8f5e8; border: 1px solid #000;">{{ round($fc3Last) }}</th>
            <th style="text-align: center; background-color: #e8f5e8; border: 1px solid #000;">{{ round($fc12Last) }}</th>
            <th colspan="8" style="background-color: #e8f5e8; border: 1px solid #000;"></th>
        </tr>

        {{-- Header Row 1 --}}
        <tr>
            <th colspan="2" rowspan="2" style="text-align: center; font-weight: bold; background-color: #f0f0f0; border: 1px solid #000;">ATA CHAPTER</th>
            <th rowspan="2" style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">{{ substr(\Carbon\Carbon::parse($period)->subMonths(2)->format('F'), 0, 3) }}</th>
            <th rowspan="2" style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">{{ substr(\Carbon\Carbon::parse($period)->subMonth(1)->format('F'), 0, 3) }}</th>
            <th rowspan="2" style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">{{ substr(\Carbon\Carbon::parse($period)->format('F'), 0, 3) }}</th>
            <th style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">Last 3</th>
            <th style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">Last 12</th>
            <th style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">{{ substr(\Carbon\Carbon::parse($period)->subMonths(2)->format('F'), 0, 3) }}</th>
            <th style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">{{ substr(\Carbon\Carbon::parse($period)->subMonth(1)->format('F'), 0, 3) }}</th>
            <th style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">{{ substr(\Carbon\Carbon::parse($period)->format('F'), 0, 3) }}</th>
            <th style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">3 Months</th>
            <th style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">12 Months</th>
            <th style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">ALERT</th>
            <th style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">ALERT</th>
            <th rowspan="2" style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">TREND</th>
        </tr>

        {{-- Header Row 2 --}}
        <tr>
            <th style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">Months</th>
            <th style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">Months</th>
            <th style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">RATE</th>
            <th style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">RATE</th>
            <th style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">RATE</th>
            <th style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">RATE</th>
            <th style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">RATE</th>
            <th style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">LEVEL</th>
            <th style="text-align: center; background-color: #f0f0f0; border: 1px solid #000;">STATUS</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($reportPerAta as $row)
        <tr>
            <td style="text-align: center; font-weight: bold; border: 1px solid #000;">{{ $row['ata'] }}</td>
            <td style="font-weight: bold; border: 1px solid #000;">{{ $row['ata_name'] ?? '' }}</td>
            <td style="text-align: center; border: 1px solid #000;">{{ $row['delayCountTwoMonthsAgo'] }}</td>
            <td style="text-align: center; border: 1px solid #000;">{{ $row['delayCountBefore'] }}</td>
            <td style="text-align: center; border: 1px solid #000;">{{ $row['delayCount'] }}</td>
            <td style="text-align: center; border: 1px solid #000;">{{ $row['delay3Month'] }}</td>
            <td style="text-align: center; border: 1px solid #000;">{{ $row['delay12Month'] }}</td>
            <td style="text-align: center; border: 1px solid #000;">{{ formatNumber($row['delay2Rate']) }}</td>
            <td style="text-align: center; border: 1px solid #000;">{{ formatNumber($row['delay1Rate']) }}</td>
            <td style="text-align: center; border: 1px solid #000;">{{ formatNumber($row['delayRate']) }}</td>
            <td style="text-align: center; border: 1px solid #000;">{{ formatNumber($row['delayRate3Month']) }}</td>
            <td style="text-align: center; border: 1px solid #000;">{{ formatNumber($row['delayRate12Month']) }}</td>
            <td style="text-align: center; border: 1px solid #000;">{{ formatNumber($row['delayAlertLevel']) }}</td>
            <td style="text-align: center; border: 1px solid #000;
                @if($row['delayAlertStatus'] == 'RED-3') background-color: #ff6666; color: white;
                @elseif($row['delayAlertStatus'] == 'RED-2') background-color: #ff9999; color: white;
                @elseif($row['delayAlertStatus'] == 'RED-1') background-color: #ffcccc;
                @endif">
                {{ $row['delayAlertStatus'] }}
            </td>
            <td style="text-align: center; border: 1px solid #000;">{{ $row['delayTrend'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
