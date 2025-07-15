{{-- filepath: resources/views/excel/cumulative-excel.blade.php --}}
@php
    // Siapkan data pivot dan bulan rolling 12
    $pivotedData = [];
    $allRegs = [];
    if (isset($data) && is_array($data)) {
        foreach ($data as $record) {
            if (empty($record['reg']) || empty($record['month_eval'])) continue;
            $reg = $record['reg'];
            $month = strtoupper(\Carbon\Carbon::parse($record['month_eval'])->format('M'));
            if (!in_array($reg, $allRegs)) $allRegs[] = $reg;
            $pivotedData[$reg]['fh'][$month] = $record['csn_by_fh'] ?? null;
            $pivotedData[$reg]['fc'][$month] = $record['csn_by_fc'] ?? null;
        }
    }
    sort($allRegs);
    $sortedPivotedData = [];
    foreach ($allRegs as $reg) {
        if (isset($pivotedData[$reg])) $sortedPivotedData[$reg] = $pivotedData[$reg];
    }
    $pivotedData = $sortedPivotedData;

    $endDate = isset($formatted_period) ? \Carbon\Carbon::parse($formatted_period) : \Carbon\Carbon::now();
    $months = [];
    $currentDate = $endDate->copy()->subMonths(11)->startOfMonth();
    for ($i = 0; $i < 12; $i++) {
        $months[] = strtoupper($currentDate->format('M'));
        $currentDate->addMonth();
    }
    $startYearForDisplay = $endDate->copy()->subMonths(11)->year;

    function formatTableNumber($value) {
        if (!is_numeric($value)) return '';
        return number_format((float)$value, 0, '.', '');
    }
    function getCumulativeValue($reg, $type, $cumulativeData) {
        if (!isset($cumulativeData[$reg])) return '-';
        $key = $type === 'fh' ? 'cumulative_fh' : 'cumulative_fc';
        $value = $cumulativeData[$reg][$key] ?? 0;
        return $value > 0 ? formatTableNumber($value) : '-';
    }
@endphp

{{-- FLIGHT HOURS TABLE --}}
<table border="1" style="border-collapse: collapse; width: 100%;">
    <thead>
        <tr>
            <th colspan="14" style="background-color: #cfe2f3; text-align: center; font-weight: bold; border: 1px solid #000; font-size: 14px;">
                FLIGHT HOURS
            </th>
        </tr>
        <tr>
            <th rowspan="2" style="background-color: #f2f2f2; vertical-align: middle; text-align: center; font-weight: bold; border: 1px solid #000; font-size: 12px;"><b>A/C REG</b></th>
            <th rowspan="2" style="background-color: #f2f2f2; vertical-align: middle; text-align: center; font-weight: bold; border: 1px solid #000; font-size: 12px;"><b>YEAR</b></th>
            <th colspan="12" style="background-color: #f2f2f2; text-align: center; font-weight: bold; text-align: center; border: 1px solid #000; font-size: 12px;">
                <b>
                    {{ $startYearForDisplay == $endDate->year ? $startYearForDisplay : $startYearForDisplay . ' - ' . $endDate->year }}
                </b>
            </th>
        </tr>
        <tr>
            @foreach ($months as $month)
                <th style="background-color: #f2f2f2; text-align: center; font-weight: bold; border: 1px solid #000; font-size: 12px;"><b>{{ $month }}</b></th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($pivotedData as $registration => $monthlyData)
            <tr>
                <td style="border: 1px solid #000; text-align: center;">{{ $registration }}</td>
                <td style="border: 1px solid #000; text-align: center;">
                    {{ getCumulativeValue($registration, 'fh', $cumulativeData ?? []) }}
                </td>
                @foreach ($months as $month)
                    <td style="border: 1px solid #000; text-align: center;">
                        {{ isset($monthlyData['fh'][$month]) && $monthlyData['fh'][$month] !== null ? formatTableNumber($monthlyData['fh'][$month]) : '-' }}
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

<br><br>

{{-- FLIGHT CYCLE TABLE --}}
<table border="1" style="border-collapse: collapse; width: 100%;">
    <thead>
        <tr>
            <th colspan="14" style="background-color: #d9ead3; text-align: center; font-weight: bold; border: 1px solid #000; font-size: 14px;">
                FLIGHT CYCLE
            </th>
        </tr>
        <tr>
            <th rowspan="2" style="background-color: #f2f2f2; vertical-align: middle; text-align: center; font-weight: bold; border: 1px solid #000; font-size: 12px;"><b>A/C REG</b></th>
            <th rowspan="2" style="background-color: #f2f2f2; vertical-align: middle; text-align: center; font-weight: bold; border: 1px solid #000; font-size: 12px;"><b>YEAR</b></th>
            <th colspan="12" style="background-color: #f2f2f2; text-align: center; font-weight: bold; text-align: center; border: 1px solid #000; font-size: 12px;">
                <b>
                    {{ $startYearForDisplay == $endDate->year ? $startYearForDisplay : $startYearForDisplay . ' - ' . $endDate->year }}
                </b>
            </th>
        </tr>
        <tr>
            @foreach ($months as $month)
                <th style="background-color: #f2f2f2; text-align: center; font-weight: bold; border: 1px solid #000; font-size: 12px;"><b>{{ $month }}</b></th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($pivotedData as $registration => $monthlyData)
            <tr>
                <td style="border: 1px solid #000; text-align: center;">{{ $registration }}</td>
                <td style="border: 1px solid #000; text-align: center;">
                    {{ getCumulativeValue($registration, 'fc', $cumulativeData ?? []) }}
                </td>
                @foreach ($months as $month)
                    <td style="border: 1px solid #000; text-align: center;">
                        {{ isset($monthlyData['fc'][$month]) && $monthlyData['fc'][$month] !== null ? formatTableNumber($monthlyData['fc'][$month]) : '-' }}
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
