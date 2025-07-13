{{-- filepath: c:\Users\Noval Rais\Documents\Github Repository\GMF-Reliability\resources\views\pdf\combined-pdf.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Combined Report - AOS & Pilot Report</title>
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

        /* Text Alignment Utilities */
        .style1 {
            text-align: center;
        }

        .style2 {
            font-size: 18px;
            text-align: center;
            font-weight: bold;
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

        /* Alert Styling */
        .alert-red {
            background-color: red;
            color: white;
        }

        /* Specialized Table Cell Styles */
        .aos-label {
            text-align: left;
            font-weight: bold;
            font-size: 9px;
            width: 130px;
        }

        .ata-name {
            text-align: left;
            font-weight: bold;
            font-size: 8px;
            max-width: 110px;
        }

        /* Cover Page Layout */
        .cover-page {
            text-align: center;
            padding: 30px 15px;
            min-height: 90vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        /* Cover Page Typography */
        .cover-title {
            font-size: 48px;
            font-weight: bold;
            color: #000000;
            margin-bottom: 15px;
            font-family: Arial, sans-serif;
            line-height: 1.2;
        }

        .cover-subtitle {
            font-size: 18px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 10px;
            font-family: Arial, sans-serif;
        }

        .cover-info {
            font-size: 40px;
            font-weight: bold;
            color: #000000;
            margin-bottom: 8px;
            font-family: Arial, sans-serif;
        }

        .cover-period {
            font-size: 40px;
            font-weight: bold;
            color: #1e3a8a;
            margin-top: 20px;
            font-family: Arial, sans-serif;
        }

        .cover-footer {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 12px;
            color: #9ca3af;
            font-family: Arial, sans-serif;
        }

        /* Responsive Table Layout */
        .table-responsive {
            overflow-x: auto;
            font-size: 9px;
        }

        .table-responsive table {
            min-width: 100%;
            font-size: 9px;
        }

        .table-responsive th,
        .table-responsive td {
            padding: 3px;
            font-size: 8px;
        }

        /* Compact Table for Dense Data Display */
        .compact-table {
            margin-bottom: 15px;
        }

        .compact-table th,
        .compact-table td {
            padding: 3px 2px;
            font-size: 8px;
            line-height: 1.1;
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
         COVER PAGE SECTION
         =================================== --}}
    <div class="cover-page" style="display: flex; flex-direction: column; justify-content: center; align-items: center; min-height: 100vh; text-align: center; padding: 0;">

        <div class="cover-title" style="margin-bottom: 30px;">Fleet Reliability Report</div>

        @php
            $logoPath = public_path('images/coverPDF.jpg');
            $logoExists = file_exists($logoPath);
        @endphp

        @if($logoExists)
            @php
            try {
                $imageData = file_get_contents($logoPath);
                $imageBase64 = base64_encode($imageData);
                $imageSrc = 'data:image/jpg;base64,' . $imageBase64;
            } catch (Exception $e) {
                $imageSrc = null;
            }
            @endphp

            @if(isset($imageSrc))
            <div style="display: flex; justify-content: center; align-items: center; margin: 60px auto; width: 100%; text-align: center;">
                <img src="{{ $imageSrc }}" alt="GMF AeroAsia Logo" style="max-width: 600px; width: 600px; height: auto; display: block; margin: 0 auto;">
            </div>
            @else
            <div style="display: flex; justify-content: center; align-items: center; margin: 60px auto; width: 100%; text-align: center;">
                <div style="width: 600px; height: 180px; border: 3px solid #1e40af; display: flex; flex-direction: column; justify-content: center; align-items: center; background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); border-radius: 15px; margin: 0 auto;">
                    <div style="font-size: 48px; font-weight: bold; color: white; margin-bottom: 12px;">GMF AeroAsia</div>
                    <div style="font-size: 18px; color: #e2e8f0; letter-spacing: 2px;">GARUDA INDONESIA GROUP</div>
                </div>
            </div>
            @endif
        @else
            <div style="display: flex; justify-content: center; align-items: center; margin: 60px auto; width: 100%; text-align: center;">
                <div style="width: 600px; height: 180px; border: 3px solid #1e40af; display: flex; flex-direction: column; justify-content: center; align-items: center; background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); border-radius: 15px; margin: 0 auto;">
                    <div style="font-size: 48px; font-weight: bold; color: white; margin-bottom: 12px;">GMF AeroAsia</div>
                    <div style="font-size: 18px; color: #e2e8f0; letter-spacing: 2px;">GARUDA INDONESIA GROUP</div>
                </div>
            </div>
        @endif

        <div style="position: absolute; bottom: 100px; left: 50%; transform: translateX(-50%); text-align: center;">
            <div class="cover-info">{{ ($operator ?? 'Garuda Indonesia') }}</div>
            <div class="cover-period" style="margin-bottom: 15px;">
                {{ isset($period) ? \Carbon\Carbon::parse($period)->format('F Y') : 'N/A' }}
            </div>
        </div>

        <div style="position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%); font-size: 12px; color: #9ca3af; font-family: Arial, sans-serif;">
            Generated by GMF Reliability Engineering & Services
        </div>
    </div>

    {{-- ===================================
         ENGINEERING RELIABILITY REPORT PAGE
         =================================== --}}
    <div style="page-break-before: always;" class="engineering-report-page">
        <div style="min-height: 100vh; display: flex; flex-direction: column; padding: 10mm;">

            <div style="min-height: calc(100vh - 30mm); border: 2px solid #000000; margin: 0; padding: 10mm; position: relative; display: flex; flex-direction: column; box-sizing: border-box;">

                <div style="text-align: center; margin-bottom: 10px; margin-top: -5px;">
                    @php
                        $garudaLogoPath = public_path('images/GarudaIndonesia.jpg');
                        $garudaLogoExists = file_exists($garudaLogoPath);
                    @endphp

                    @if($garudaLogoExists)
                        @php
                        try {
                            $garudaImageData = file_get_contents($garudaLogoPath);
                            $garudaImageBase64 = base64_encode($garudaImageData);
                            $garudaImageSrc = 'data:image/png;base64,' . $garudaImageBase64;
                        } catch (Exception $e) {
                            $garudaImageSrc = null;
                        }
                        @endphp

                        @if(isset($garudaImageSrc))
                        <img src="{{ $garudaImageSrc }}" alt="Garuda Indonesia Logo" style="max-width: 200px; height: auto; margin: 0 auto;">
                        @else
                        <div style="width: 200px; height: 120px; margin: 0 auto; display: flex; align-items: center; justify-content: center; border: 2px solid #1e40af; background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); border-radius: 8px;">
                            <div style="color: white; font-weight: bold; font-size: 24px; text-align: center; line-height: 1.4;">
                                <div>GARUDA</div>
                                <div>INDONESIA</div>
                            </div>
                        </div>
                        @endif
                    @else
                        <div style="width: 200px; height: 120px; margin: 0 auto; display: flex; align-items: center; justify-content: center; border: 2px solid #1e40af; background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); border-radius: 8px;">
                            <div style="color: white; font-weight: bold; font-size: 24px; text-align: center; line-height: 1.4;">
                                <div>GARUDA</div>
                                <div>INDONESIA</div>
                            </div>
                        </div>
                    @endif
                </div>

                <div style="flex: 1; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; margin: 250px 0;">
                    <div style="font-size: 25px; font-weight: bold; color: #000000; font-family: Arial, sans-serif; line-height: 1.2;">
                        ENGINEERING
                    </div>
                    <div style="font-size: 25px; font-weight: bold; color: #000000; font-family: Arial, sans-serif; line-height: 1.2;">
                        RELIABILITY REPORT
                    </div>
                    <div style="font-size: 25px; font-weight: bold; color: #000000; font-family: Arial, sans-serif; line-height: 1.2;">
                        {{ strtoupper(\Carbon\Carbon::parse($period)->format('F Y')) }}
                    </div>
                </div>

                <div style="margin-top: auto;">

                    <div style="text-align: right; margin-bottom: 20px;">
                        <div style="font-size: 14px; color: #000000; margin-bottom: 5px; font-family: Arial, sans-serif; line-height: 1.2;">
                            Issued by JKTMQGA
                        </div>
                        <div style="font-size: 14px; color: #000000; font-family: Arial, sans-serif; line-height: 1.2;">
                            Compiled by GMF Reliability & Engineering Services
                        </div>
                    </div>

                    <div style="margin-bottom: 0;">

                        <div style="display: flex; align-items: center; margin: 0 -10mm 10px -10mm; width: calc(100% + 20mm);">
                            @php
                                $gmfLogoPath = public_path('images/banner.png');
                                $gmfLogoExists = file_exists($gmfLogoPath);
                            @endphp

                            @if($gmfLogoExists)
                                @php
                                try {
                                    $gmfImageData = file_get_contents($gmfLogoPath);
                                    $gmfImageBase64 = base64_encode($gmfImageData);
                                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                                    $mimeType = finfo_buffer($finfo, $gmfImageData);
                                    finfo_close($finfo);
                                    $gmfImageSrc = 'data:' . $mimeType . ';base64,' . $gmfImageBase64;
                                } catch (Exception $e) {
                                    $gmfImageSrc = null;
                                }
                                @endphp

                                @if(isset($gmfImageSrc))
                                <img src="{{ $gmfImageSrc }}" alt="GMF Logo" style="width: 100%; height: 40px; object-fit: contain; object-position: center; image-rendering: pixelated; image-rendering: -moz-crisp-edges; image-rendering: crisp-edges;">
                                @else
                                <div style="width: 100%; height: 50px; background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 14px; color: white; font-weight: bold; letter-spacing: 1px;">
                                    GMF AeroAsia - GARUDA INDONESIA GROUP
                                </div>
                                @endif
                            @else
                                <div style="width: 100%; height: 50px; background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 14px; color: white; font-weight: bold; letter-spacing: 1px;">
                                    GMF AeroAsia - GARUDA INDONESIA GROUP
                                </div>
                            @endif
                        </div>

                        <div style="text-align: right; font-size: 14px; line-height: 1.2; color: #000000; margin-top: 3px; margin-bottom: 0; margin-right: 0;">
                            <div style="margin-bottom: 1px;">Hangar 3 Room 231</div>
                            <div style="margin-bottom: 1px;">Soekarno-Hatta International Airport</div>
                            <div style="margin-bottom: 1px;">P.O.Box 1303, BLSH 19130</div>
                            <div style="margin-bottom: 1px;">Cengkareng - Indonesia</div>
                            <div style="margin-top: 2px;">Phone: +62-21-5508199 Fax: +62-21-5501578</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================================
         AIRCRAFT OPERATION SUMMARY (AOS) SECTION
         =================================== --}}
    <div style="page-break-before: always;">
    @php
        $safeNumber = function($value, $default = 0) {
            return is_numeric($value) ? floatval($value) : $default;
        };

        $formatNumber = function($value, $decimals = 2) {
            if (!is_numeric($value)) {
                return '0';
            }
            return rtrim(rtrim(number_format(floatval($value), $decimals, '.', ''), '0'), '.');
        };

        $formatTime = function($value) {
            if (is_string($value) && str_contains($value, ':')) {
                 // Already formatted as H : i
                $parts = explode(':', str_replace(' ', '', $value));
                return sprintf('%d:%02d', $parts[0], $parts[1]);
            }
            if (!is_numeric($value) || $value == 0) {
                return '0:00';
            }
            $decimalHours = floatval($value);
            $hours = floor($decimalHours);
            $minutes = round(($decimalHours - $hours) * 60);
            return sprintf('%d:%02d', $hours, $minutes);
        };

        // Year variables
        $startYear = \Carbon\Carbon::parse($period)->subMonths(11)->format('Y');
        $endYear = \Carbon\Carbon::parse($period)->format('Y');

        $metrics = [
            'acInFleet' => ['label' => 'A/C in Fleet', 'format' => 'number', 'decimals' => 2, 'last12' => $averages['acInFleet']['value'] ?? 0],
            'acInService' => ['label' => 'A/C in Service (Revenue)', 'format' => 'number', 'decimals' => 2, 'last12' => $averages['acInService']['value'] ?? 0],
            'daysInService' => ['label' => 'A/C Days in Service (Revenue)', 'format' => 'integer', 'last12' => $averages['daysInService']['value'] ?? 0],
            'flyingHoursTotal' => ['label' => 'Flying Hours - Total', 'format' => 'integer', 'last12' => $averages['flyingHoursTotal']['value'] ?? 0],
            'revenueFlyingHours' => ['label' => '- Revenue', 'format' => 'integer', 'last12' => $averages['revenueFlyingHours']['value'] ?? 0],
            'takeOffTotal' => ['label' => 'Take Off - Total', 'format' => 'integer', 'last12' => $averages['takeOffTotal']['value'] ?? 0],
            'revenueTakeOff' => ['label' => '- Revenue', 'format' => 'integer', 'last12' => $averages['revenueTakeOff']['value'] ?? 0],
            'flightHoursPerTakeOffTotal' => ['label' => 'Flight Hours per Take Off - Total', 'format' => 'time', 'last12' => $avgFlightHoursPerTakeOffTotal ?? '0:00'],
            'revenueFlightHoursPerTakeOff' => ['label' => '- Revenue', 'format' => 'time', 'last12' => $avgRevenueFlightHoursPerTakeOff ?? '0:00'],
            'dailyUtilizationFlyingHoursTotal' => ['label' => 'Daily Utilization Flying Hours - Total', 'format' => 'time', 'last12' => $avgDailyUtilizationFlyingHoursTotal ?? '0:00'],
            'revenueDailyUtilizationFlyingHoursTotal' => ['label' => '- Revenue', 'format' => 'time', 'last12' => $avgRevenueDailyUtilizationFlyingHoursTotal ?? '0:00'],
            'dailyUtilizationTakeOffTotal' => ['label' => 'Daily Utilization Take Off - Total', 'format' => 'number', 'decimals' => 2, 'last12' => $averages['dailyUtilizationTakeOffTotal']['value'] ?? 0],
            'revenueDailyUtilizationTakeOffTotal' => ['label' => '- Revenue', 'format' => 'number', 'decimals' => 2, 'last12' => $averages['revenueDailyUtilizationTakeOffTotal']['value'] ?? 0],
            'technicalDelayTotal' => ['label' => 'Technical Delay - Total', 'format' => 'integer', 'last12' => $averages['technicalDelayTotal']['value'] ?? 0],
            'totalDuration' => ['label' => '- Total Duration', 'format' => 'time', 'last12' => $avgTotalDuration ?? '0:00'],
            'averageDuration' => ['label' => '- Average Duration', 'format' => 'time', 'last12' => $avgAverageDuration ?? '0:00'],
            'ratePer100TakeOff' => ['label' => '- Rate per 100 Take Off', 'format' => 'number', 'decimals' => 2, 'last12' => $averages['ratePer100TakeOff']['value'] ?? 0],
            'technicalIncidentTotal' => ['label' => 'Technical Incident - Total', 'format' => 'integer', 'last12' => $averages['technicalIncidentTotal']['value'] ?? 0],
            'technicalIncidentRate' => ['label' => '- Rate per 100 Take Off', 'format' => 'number', 'decimals' => 2, 'last12' => $averages['technicalIncidentRate']['value'] ?? 0],
            'technicalCancellationTotal' => ['label' => 'Technical Cancellation - Total', 'format' => 'integer', 'last12' => $averages['technicalCancellationTotal']['value'] ?? 0],
        ];

        $dispatchReliabilityMetric = [
            'dispatchReliability' => ['label' => 'Dispatch Reliability (%)', 'format' => 'percent', 'last12' => $averages['dispatchReliability']['value'] ?? 0],
        ];
    @endphp
    <table>
        <thead>
            <tr>
                <th colspan="15" style="font-size: 16px; font-weight: bold;">AIRCRAFT OPERATION SUMMARY</th>
            </tr>
            <tr>
                <th colspan="15" style="font-size: 14px; font-weight: bold;">{{ $aircraftType }}</th>
            </tr>
            <tr>
                <th colspan="15" style="font-size: 12px;">{{ $startYear }} - {{ $endYear }}</th>
            </tr>
            <tr>
                <td class="aos-label"></td>
                <td><b>{{ $baseYear }}</b></td>
                @for ($i = 11; $i >= 0; $i--)
                    <td><b>{{ substr(\Carbon\Carbon::parse($period)->subMonth($i)->format('F'), 0, 3) }}</b></td>
                @endfor
                <td><b>LAST 12 MTHS</b></td>
            </tr>
        </thead>
        <tbody>
            @foreach($metrics as $key => $metric)
            <tr>
                <td class="aos-label">{{ $metric['label'] }}</td>
                {{-- Year Column --}}
                <td>
                    @php $yearValue = $yearColumnData[$key] ?? null; @endphp
                    @if($metric['format'] === 'time')
                        {{ $formatTime($yearValue) }}
                    @elseif($metric['format'] === 'integer')
                        {{ round($safeNumber($yearValue)) }}
                    @else
                        {{ $formatNumber($yearValue, $metric['decimals'] ?? 2) }}
                    @endif
                </td>
                {{-- Monthly Columns --}}
                @for ($i = 11; $i >= 0; $i--)
                    <td>
                        @php $monthValue = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')][$key] ?? null; @endphp
                        @if($metric['format'] === 'time')
                            {{ $formatTime($monthValue) }}
                        @elseif($metric['format'] === 'integer')
                            {{ round($safeNumber($monthValue)) }}
                        @else
                            {{ $formatNumber($monthValue, $metric['decimals'] ?? 2) }}
                        @endif
                    </td>
                @endfor
                {{-- Last 12 Months Column --}}
                <td>
                    @php $last12MthsValue = $metric['last12']; @endphp
                    @if($metric['format'] === 'time')
                        {{ $formatTime($last12MthsValue) }}
                    @elseif($metric['format'] === 'integer')
                        {{ round($safeNumber($last12MthsValue)) }}
                    @else
                        {{ $formatNumber($last12MthsValue, $metric['decimals'] ?? 2) }}
                    @endif
                </td>
            </tr>
            @endforeach

            {{-- Special Row for Dispatch Reliability --}}
            @foreach($dispatchReliabilityMetric as $key => $metric)
            <tr>
                <td class="aos-label">{{ $metric['label'] }}</td>
                {{-- Year Column --}}
                <td>
                    @php $yearValue = $yearColumnData[$key] ?? 0; @endphp
                    {{ $formatNumber($yearValue, 2) }}%
                </td>
                {{-- Monthly Columns --}}
                @for ($i = 11; $i >= 0; $i--)
                    <td>
                        @php $monthValue = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')][$key] ?? 0; @endphp
                        {{ $formatNumber($monthValue, 2) }}%
                    </td>
                @endfor
                {{-- Last 12 Months Column --}}
                <td>
                    @php $last12MthsValue = $metric['last12']; @endphp
                    {{ $formatNumber($last12MthsValue, 2) }}%
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

    {{-- ===================================
         PILOT REPORT SECTION
         Technical Pilot Report Analysis by ATA Chapter
         =================================== --}}
    <div style="page-break-before: always;">
        <table>
            <thead>
                <tr>
                    <th colspan="2">{{ $aircraftType ?? 'N/A' }}</th>
                    <th colspan="13">PILOT REPORT</th>
                </tr>

                <tr>
                    <th colspan="2">Total Flight Hours</th>
                    <th>{{ round($pilotData['flyingHours2Before'] ?? 0) }}</th>
                    <th>{{ round($pilotData['flyingHoursBefore'] ?? 0) }}</th>
                    <th>{{ round($pilotData['flyingHoursTotal'] ?? 0) }}</th>
                    <th>{{ round($pilotData['fh3Last'] ?? 0) }}</th>
                    <th>{{ round($pilotData['fh12Last'] ?? 0) }}</th>
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
                @if(isset($pilotData['reportPerAta']) && is_array($pilotData['reportPerAta']))
                    @foreach ($pilotData['reportPerAta'] as $report)
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
                <tr>
                    <th colspan="2">{{ $aircraftType ?? 'N/A' }}</th>
                    <th colspan="13">MAINTENANCE FINDING REPORT</th>
                </tr>

                <tr>
                    <th colspan="2">Total Flight Hours</th>
                    <th>{{ round($pilotData['flyingHours2Before'] ?? 0) }}</th>
                    <th>{{ round($pilotData['flyingHoursBefore'] ?? 0) }}</th>
                    <th>{{ round($pilotData['flyingHoursTotal'] ?? 0) }}</th>
                    <th>{{ round($pilotData['fh3Last'] ?? 0) }}</th>
                    <th>{{ round($pilotData['fh12Last'] ?? 0) }}</th>
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
                @if(isset($pilotData['reportPerAta']) && is_array($pilotData['reportPerAta']))
                    @foreach ($pilotData['reportPerAta'] as $report)
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
                <tr>
                    <th colspan="2">{{ $aircraftType ?? 'N/A' }}</th>
                    <th colspan="13">TECHNICAL DELAY > 15 MINUTES AND CANCELLATION</th>
                </tr>

                <tr>
                    <th colspan="2">Total Flight Cycles</th>
                    <th>{{ round($pilotData['flyingCycles2Before'] ?? 0) }}</th>
                    <th>{{ round($pilotData['flyingCyclesBefore'] ?? 0) }}</th>
                    <th>{{ round($pilotData['flyingCyclesTotal'] ?? 0) }}</th>
                    <th>{{ round($pilotData['fc3Last'] ?? 0) }}</th>
                    <th>{{ round($pilotData['fc12Last'] ?? 0) }}</th>
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
                @if(isset($pilotData['reportPerAta']) && is_array($pilotData['reportPerAta']))
                    @foreach ($pilotData['reportPerAta'] as $row)
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
