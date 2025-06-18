{{-- filepath: c:\Users\Noval Rais\Documents\Github Repository\GMF-Reliability\resources\views\excel\pilot-excel.blade.php --}}

@php
function formatNumber($value, $decimals = 2) {
    return rtrim(rtrim(number_format($value, $decimals), '0'), '.');
}
@endphp

<table>
    <thead>
        {{-- Header Title --}}
        <tr>
            <th colspan="15" style="text-align: center; font-weight: bold; font-size: 16px; background-color: #4CAF50; color: white;">
                PILOT REPORT - {{ strtoupper($aircraftType) }} - {{ strtoupper(\Carbon\Carbon::parse($period)->format('F Y')) }}
            </th>
        </tr>
        <tr></tr>
        
        {{-- Pilot Report Section --}}
        <tr>
            <th colspan="15" style="text-align: center; font-weight: bold; font-size: 14px; background-color: #c8e6c9;">
                PILOT REPORT
            </th>
        </tr>
        
        {{-- Flying Hours Info --}}
        <tr>
            <th colspan="2" style="font-weight: bold; background-color: #e8f5e8;">Total Flight Hours</th>
            <th style="text-align: center; background-color: #e8f5e8;">{{ round($flyingHours2Before) }}</th>
            <th style="text-align: center; background-color: #e8f5e8;">{{ round($flyingHoursBefore) }}</th>
            <th style="text-align: center; background-color: #e8f5e8;">{{ round($flyingHoursTotal) }}</th>
            <th style="text-align: center; background-color: #e8f5e8;">{{ round($fh3Last) }}</th>
            <th style="text-align: center; background-color: #e8f5e8;">{{ round($fh12Last) }}</th>
            <th colspan="8" style="background-color: #e8f5e8;"></th>
        </tr>
        
        {{-- Header Row 1 --}}
        <tr>
            <th colspan="2" rowspan="2" style="text-align: center; font-weight: bold; background-color: #f0f0f0;">ATA CHAPTER</th>
            <th rowspan="2" style="text-align: center; background-color: #f0f0f0;">{{ substr(\Carbon\Carbon::parse($period)->subMonths(2)->format('F'), 0, 3) }}</th>
            <th rowspan="2" style="text-align: center; background-color: #f0f0f0;">{{ substr(\Carbon\Carbon::parse($period)->subMonth(1)->format('F'), 0, 3) }}</th>
            <th rowspan="2" style="text-align: center; background-color: #f0f0f0;">{{ substr(\Carbon\Carbon::parse($period)->format('F'), 0, 3) }}</th>
            <th style="text-align: center; background-color: #f0f0f0;">Last 3</th>
            <th style="text-align: center; background-color: #f0f0f0;">Last 12</th>
            <th style="text-align: center; background-color: #f0f0f0;">{{ substr(\Carbon\Carbon::parse($period)->subMonths(2)->format('F'), 0, 3) }}</th>
            <th style="text-align: center; background-color: #f0f0f0;">{{ substr(\Carbon\Carbon::parse($period)->subMonth(1)->format('F'), 0, 3) }}</th>
            <th style="text-align: center; background-color: #f0f0f0;">{{ substr(\Carbon\Carbon::parse($period)->format('F'), 0, 3) }}</th>
            <th style="text-align: center; background-color: #f0f0f0;">3 Months</th>
            <th style="text-align: center; background-color: #f0f0f0;">12 Months</th>
            <th style="text-align: center; background-color: #f0f0f0;">ALERT</th>
            <th style="text-align: center; background-color: #f0f0f0;">ALERT</th>
            <th rowspan="2" style="text-align: center; background-color: #f0f0f0;">TREND</th>
        </tr>
        
        {{-- Header Row 2 --}}
        <tr>
            <th style="text-align: center; background-color: #f0f0f0;">Months</th>
            <th style="text-align: center; background-color: #f0f0f0;">Months</th>
            <th style="text-align: center; background-color: #f0f0f0;">RATE</th>
            <th style="text-align: center; background-color: #f0f0f0;">RATE</th>
            <th style="text-align: center; background-color: #f0f0f0;">RATE</th>
            <th style="text-align: center; background-color: #f0f0f0;">RATE</th>
            <th style="text-align: center; background-color: #f0f0f0;">RATE</th>
            <th style="text-align: center; background-color: #f0f0f0;">LEVEL</th>
            <th style="text-align: center; background-color: #f0f0f0;">STATUS</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($reportPerAta as $row)
        <tr>
            <td style="text-align: center; font-weight: bold;">{{ $row['ata'] }}</td>
            <td style="font-weight: bold;">{{ $row['ata_name'] ?? '' }}</td>
            <td style="text-align: center;">{{ $row['pirepCountTwoMonthsAgo'] }}</td>
            <td style="text-align: center;">{{ $row['pirepCountBefore'] }}</td>
            <td style="text-align: center;">{{ $row['pirepCount'] }}</td>
            <td style="text-align: center;">{{ $row['pirep3Month'] }}</td>
            <td style="text-align: center;">{{ $row['pirep12Month'] }}</td>
            <td style="text-align: center;">{{ formatNumber($row['pirep2Rate']) }}</td>
            <td style="text-align: center;">{{ formatNumber($row['pirep1Rate']) }}</td>
            <td style="text-align: center;">{{ formatNumber($row['pirepRate']) }}</td>
            <td style="text-align: center;">{{ formatNumber($row['pirepRate3Month']) }}</td>
            <td style="text-align: center;">{{ formatNumber($row['pirepRate12Month']) }}</td>
            <td style="text-align: center;">{{ formatNumber($row['pirepAlertLevel']) }}</td>
            <td style="text-align: center;
                @if($row['pirepAlertStatus'] == 'RED-3') background-color: #ff6666; color: white;
                @elseif($row['pirepAlertStatus'] == 'RED-2') background-color: #ff9999; color: white;
                @elseif($row['pirepAlertStatus'] == 'RED-1') background-color: #ffcccc;
                @endif">
                {{ $row['pirepAlertStatus'] }}
            </td>
            <td style="text-align: center;">{{ $row['pirepTrend'] }}</td>
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
<table>
    <thead>
        <tr>
            <th colspan="15" style="text-align: center; font-weight: bold; font-size: 14px; background-color: #c8e6c9;">
                MAINTENANCE REPORT
            </th>
        </tr>
        
        {{-- Flying Hours Info --}}
        <tr>
            <th colspan="2" style="font-weight: bold; background-color: #e8f5e8;">Total Flight Hours</th>
            <th style="text-align: center; background-color: #e8f5e8;">{{ round($flyingHours2Before) }}</th>
            <th style="text-align: center; background-color: #e8f5e8;">{{ round($flyingHoursBefore) }}</th>
            <th style="text-align: center; background-color: #e8f5e8;">{{ round($flyingHoursTotal) }}</th>
            <th style="text-align: center; background-color: #e8f5e8;">{{ round($fh3Last) }}</th>
            <th style="text-align: center; background-color: #e8f5e8;">{{ round($fh12Last) }}</th>
            <th colspan="8" style="background-color: #e8f5e8;"></th>
        </tr>
        
        {{-- Header Row 1 --}}
        <tr>
            <th colspan="2" rowspan="2" style="text-align: center; font-weight: bold; background-color: #f0f0f0;">ATA CHAPTER</th>
            <th rowspan="2" style="text-align: center; background-color: #f0f0f0;">{{ substr(\Carbon\Carbon::parse($period)->subMonths(2)->format('F'), 0, 3) }}</th>
            <th rowspan="2" style="text-align: center; background-color: #f0f0f0;">{{ substr(\Carbon\Carbon::parse($period)->subMonth(1)->format('F'), 0, 3) }}</th>
            <th rowspan="2" style="text-align: center; background-color: #f0f0f0;">{{ substr(\Carbon\Carbon::parse($period)->format('F'), 0, 3) }}</th>
            <th style="text-align: center; background-color: #f0f0f0;">Last 3</th>
            <th style="text-align: center; background-color: #f0f0f0;">Last 12</th>
            <th style="text-align: center; background-color: #f0f0f0;">{{ substr(\Carbon\Carbon::parse($period)->subMonths(2)->format('F'), 0, 3) }}</th>
            <th style="text-align: center; background-color: #f0f0f0;">{{ substr(\Carbon\Carbon::parse($period)->subMonth(1)->format('F'), 0, 3) }}</th>
            <th style="text-align: center; background-color: #f0f0f0;">{{ substr(\Carbon\Carbon::parse($period)->format('F'), 0, 3) }}</th>
            <th style="text-align: center; background-color: #f0f0f0;">3 Months</th>
            <th style="text-align: center; background-color: #f0f0f0;">12 Months</th>
            <th style="text-align: center; background-color: #f0f0f0;">ALERT</th>
            <th style="text-align: center; background-color: #f0f0f0;">ALERT</th>
            <th rowspan="2" style="text-align: center; background-color: #f0f0f0;">TREND</th>
        </tr>
        
        {{-- Header Row 2 --}}
        <tr>
            <th style="text-align: center; background-color: #f0f0f0;">Months</th>
            <th style="text-align: center; background-color: #f0f0f0;">Months</th>
            <th style="text-align: center; background-color: #f0f0f0;">RATE</th>
            <th style="text-align: center; background-color: #f0f0f0;">RATE</th>
            <th style="text-align: center; background-color: #f0f0f0;">RATE</th>
            <th style="text-align: center; background-color: #f0f0f0;">RATE</th>
            <th style="text-align: center; background-color: #f0f0f0;">RATE</th>
            <th style="text-align: center; background-color: #f0f0f0;">LEVEL</th>
            <th style="text-align: center; background-color: #f0f0f0;">STATUS</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($reportPerAta as $row)
        <tr>
            <td style="text-align: center; font-weight: bold;">{{ $row['ata'] }}</td>
            <td style="font-weight: bold;">{{ $row['ata_name'] ?? '' }}</td>
            <td style="text-align: center;">{{ $row['marepCountTwoMonthsAgo'] }}</td>
            <td style="text-align: center;">{{ $row['marepCountBefore'] }}</td>
            <td style="text-align: center;">{{ $row['marepCount'] }}</td>
            <td style="text-align: center;">{{ $row['marep3Month'] }}</td>
            <td style="text-align: center;">{{ $row['marep12Month'] }}</td>
            <td style="text-align: center;">{{ formatNumber($row['marep2Rate']) }}</td>
            <td style="text-align: center;">{{ formatNumber($row['marep1Rate']) }}</td>
            <td style="text-align: center;">{{ formatNumber($row['marepRate']) }}</td>
            <td style="text-align: center;">{{ formatNumber($row['marepRate3Month']) }}</td>
            <td style="text-align: center;">{{ formatNumber($row['marepRate12Month']) }}</td>
            <td style="text-align: center;">{{ formatNumber($row['marepAlertLevel']) }}</td>
            <td style="text-align: center;
                @if($row['marepAlertStatus'] == 'RED-3') background-color: #ff6666; color: white;
                @elseif($row['marepAlertStatus'] == 'RED-2') background-color: #ff9999; color: white;
                @elseif($row['marepAlertStatus'] == 'RED-1') background-color: #ffcccc;
                @endif">
                {{ $row['marepAlertStatus'] }}
            </td>
            <td style="text-align: center;">{{ $row['marepTrend'] }}</td>
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
<table>
    <thead>
        <tr>
            <th colspan="15" style="text-align: center; font-weight: bold; font-size: 14px; background-color: #c8e6c9;">
                TECHNICAL DELAY REPORT
            </th>
        </tr>
        
        {{-- Flying Cycles Info --}}
        <tr>
            <th colspan="2" style="font-weight: bold; background-color: #e8f5e8;">Total Flight Cycles</th>
            <th style="text-align: center; background-color: #e8f5e8;">{{ round($flyingCycles2Before) }}</th>
            <th style="text-align: center; background-color: #e8f5e8;">{{ round($flyingCyclesBefore) }}</th>
            <th style="text-align: center; background-color: #e8f5e8;">{{ round($flyingCyclesTotal) }}</th>
            <th style="text-align: center; background-color: #e8f5e8;">{{ round($fc3Last) }}</th>
            <th style="text-align: center; background-color: #e8f5e8;">{{ round($fc12Last) }}</th>
            <th colspan="8" style="background-color: #e8f5e8;"></th>
        </tr>
        
        {{-- Header Row 1 --}}
        <tr>
            <th colspan="2" rowspan="2" style="text-align: center; font-weight: bold; background-color: #f0f0f0;">ATA CHAPTER</th>
            <th rowspan="2" style="text-align: center; background-color: #f0f0f0;">{{ substr(\Carbon\Carbon::parse($period)->subMonths(2)->format('F'), 0, 3) }}</th>
            <th rowspan="2" style="text-align: center; background-color: #f0f0f0;">{{ substr(\Carbon\Carbon::parse($period)->subMonth(1)->format('F'), 0, 3) }}</th>
            <th rowspan="2" style="text-align: center; background-color: #f0f0f0;">{{ substr(\Carbon\Carbon::parse($period)->format('F'), 0, 3) }}</th>
            <th style="text-align: center; background-color: #f0f0f0;">Last 3</th>
            <th style="text-align: center; background-color: #f0f0f0;">Last 12</th>
            <th style="text-align: center; background-color: #f0f0f0;">{{ substr(\Carbon\Carbon::parse($period)->subMonths(2)->format('F'), 0, 3) }}</th>
            <th style="text-align: center; background-color: #f0f0f0;">{{ substr(\Carbon\Carbon::parse($period)->subMonth(1)->format('F'), 0, 3) }}</th>
            <th style="text-align: center; background-color: #f0f0f0;">{{ substr(\Carbon\Carbon::parse($period)->format('F'), 0, 3) }}</th>
            <th style="text-align: center; background-color: #f0f0f0;">3 Months</th>
            <th style="text-align: center; background-color: #f0f0f0;">12 Months</th>
            <th style="text-align: center; background-color: #f0f0f0;">ALERT</th>
            <th style="text-align: center; background-color: #f0f0f0;">ALERT</th>
            <th rowspan="2" style="text-align: center; background-color: #f0f0f0;">TREND</th>
        </tr>
        
        {{-- Header Row 2 --}}
        <tr>
            <th style="text-align: center; background-color: #f0f0f0;">Months</th>
            <th style="text-align: center; background-color: #f0f0f0;">Months</th>
            <th style="text-align: center; background-color: #f0f0f0;">RATE</th>
            <th style="text-align: center; background-color: #f0f0f0;">RATE</th>
            <th style="text-align: center; background-color: #f0f0f0;">RATE</th>
            <th style="text-align: center; background-color: #f0f0f0;">RATE</th>
            <th style="text-align: center; background-color: #f0f0f0;">RATE</th>
            <th style="text-align: center; background-color: #f0f0f0;">LEVEL</th>
            <th style="text-align: center; background-color: #f0f0f0;">STATUS</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($reportPerAta as $row)
        <tr>
            <td style="text-align: center; font-weight: bold;">{{ $row['ata'] }}</td>
            <td style="font-weight: bold;">{{ $row['ata_name'] ?? '' }}</td>
            <td style="text-align: center;">{{ $row['delayCountTwoMonthsAgo'] }}</td>
            <td style="text-align: center;">{{ $row['delayCountBefore'] }}</td>
            <td style="text-align: center;">{{ $row['delayCount'] }}</td>
            <td style="text-align: center;">{{ $row['delay3Month'] }}</td>
            <td style="text-align: center;">{{ $row['delay12Month'] }}</td>
            <td style="text-align: center;">{{ formatNumber($row['delay2Rate']) }}</td>
            <td style="text-align: center;">{{ formatNumber($row['delay1Rate']) }}</td>
            <td style="text-align: center;">{{ formatNumber($row['delayRate']) }}</td>
            <td style="text-align: center;">{{ formatNumber($row['delayRate3Month']) }}</td>
            <td style="text-align: center;">{{ formatNumber($row['delayRate12Month']) }}</td>
            <td style="text-align: center;">{{ formatNumber($row['delayAlertLevel']) }}</td>
            <td style="text-align: center;
                @if($row['delayAlertStatus'] == 'RED-3') background-color: #ff6666; color: white;
                @elseif($row['delayAlertStatus'] == 'RED-2') background-color: #ff9999; color: white;
                @elseif($row['delayAlertStatus'] == 'RED-1') background-color: #ffcccc;
                @endif">
                {{ $row['delayAlertStatus'] }}
            </td>
            <td style="text-align: center;">{{ $row['delayTrend'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>