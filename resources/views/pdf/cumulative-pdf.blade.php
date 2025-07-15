{{-- filepath: resources/views/pdf/cumulative-pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cumulative Report PDF</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        h1, h2 { margin-bottom: 0.5em; }
        .main-title { font-size: 20px; font-weight: bold; text-align: center; margin-bottom: 0.2em; }
        .subtitle { font-size: 15px; font-weight: bold; text-align: center; margin-bottom: 1em; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 2em; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: center; }
        th { background: #e5e7eb; font-weight: bold; }
        .reg-col { background: #f3f4f6; font-weight: bold; }
        .year-col { background: #e0f2fe; font-weight: bold; }
        .header-main { background: #0369a1; color: #fff; }
        .header-main-fc { background: #65a30d; color: #fff; }
        .month-col { background: #f9fafb; }
    </style>
</head>
<body>
    @php
        use Carbon\Carbon;
        // Data preparation
        $pivotedData = [];
        $allRegs = [];
        $aircraftType = $aircraftType ?? ($aircraft_type ?? 'BOEING B737-800');
        if (isset($data) && is_array($data)) {
            foreach ($data as $record) {
                if (empty($record['reg']) || empty($record['month_eval'])) continue;
                $reg = $record['reg'];
                $month = strtoupper(Carbon::parse($record['month_eval'])->format('M'));
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

        // Year range
        $yearRange = '';
        if (isset($summary['date_range']['from']) && isset($summary['date_range']['to'])) {
            $startYear = Carbon::parse($summary['date_range']['from'])->year;
            $endYear = Carbon::parse($summary['date_range']['to'])->year;
            $yearRange = ($startYear == $endYear) ? $startYear : $startYear . ' - ' . $endYear;
        }

        // Months header
        $endDate = isset($formatted_period) ? Carbon::parse($formatted_period) : (isset($period) ? Carbon::parse($period) : Carbon::now());
        $months = [];
        $currentDate = $endDate->copy()->subMonths(11)->startOfMonth();
        $startYearForDisplay = $currentDate->year;
        for ($i = 0; $i < 12; $i++) {
            $months[] = strtoupper($currentDate->format('M'));
            $currentDate->addMonth();
        }

        function formatTableNumber($value) {
            if (!is_numeric($value)) return '';
            return number_format((float)$value, 0, '.', '');
        }

        function getCumulativeValue($registration, $type, $cumulativeData) {
            if (!isset($cumulativeData[$registration])) return '-';
            $key = $type === 'fh' ? 'cumulative_fh' : 'cumulative_fc';
            $value = $cumulativeData[$registration][$key] ?? 0;
            return $value > 0 ? number_format((float)$value, 0, '.', '') : '-';
        }
    @endphp

    <div class="main-title">
        CUMULATIVE FLIGHT HOURS AND TAKE OFF {{ $yearRange }}
    </div>
    <div class="subtitle">
        {{ $aircraftType }}
    </div>

    {{-- FLIGHT HOURS TABLE --}}
    <table>
        <thead>
            <tr>
                <th colspan="2" style="text-align:left;">FLIGHT HOURS</th>
                <th colspan="12" class="header-main">{{ $yearRange }}</th>
            </tr>
            <tr>
                <th class="reg-col">A/C REG</th>
                <th class="year-col">YEAR</th>
                @foreach ($months as $month)
                    <th class="month-col">{{ $month }}</th>
                @endforeach
            </tr>
            <tr>
                <th></th>
                <th class="year-col">{{ $startYearForDisplay }}</th>
                @foreach ($months as $month)
                    <th></th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @if (!empty($pivotedData))
                @foreach ($pivotedData as $registration => $monthlyData)
                    <tr>
                        <td class="reg-col">{{ $registration }}</td>
                        <td>
                            {{ getCumulativeValue($registration, 'fh', $cumulativeData ?? []) }}
                        </td>
                        @foreach ($months as $month)
                            <td>
                                {{ isset($monthlyData['fh'][$month]) && $monthlyData['fh'][$month] !== null ? number_format((float)$monthlyData['fh'][$month], 0, '.', '') : '-' }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="14" style="text-align:center; color:#888;">No data found for the selected criteria.</td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- FLIGHT CYCLE TABLE --}}
    <table>
        <thead>
            <tr>
                <th colspan="2" style="text-align:left;">FLIGHT CYCLE</th>
                <th colspan="12" class="header-main-fc">{{ $yearRange }}</th>
            </tr>
            <tr>
                <th class="reg-col">A/C REG</th>
                <th class="year-col">YEAR</th>
                @foreach ($months as $month)
                    <th class="month-col">{{ $month }}</th>
                @endforeach
            </tr>
            <tr>
                <th></th>
                <th class="year-col">{{ $startYearForDisplay }}</th>
                @foreach ($months as $month)
                    <th></th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @if (!empty($pivotedData))
                @foreach ($pivotedData as $registration => $monthlyData)
                    <tr>
                        <td class="reg-col">{{ $registration }}</td>
                        <td>
                            {{ getCumulativeValue($registration, 'fc', $cumulativeData ?? []) }}
                        </td>
                        @foreach ($months as $month)
                            <td>
                                {{ isset($monthlyData['fc'][$month]) && $monthlyData['fc'][$month] !== null ? number_format((float)$monthlyData['fc'][$month], 0, '.', '') : '-' }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="14" style="text-align:center; color:#888;">No data found for the selected criteria.</td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
