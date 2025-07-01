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

        <!-- Main Title -->
        <div class="cover-title" style="margin-bottom: 30px;">Fleet Reliability Report</div>

        <!-- Company Logo Section -->
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
            <!-- Primary Logo Display -->
            <div style="display: flex; justify-content: center; align-items: center; margin: 60px auto; width: 100%; text-align: center;">
                <img src="{{ $imageSrc }}" alt="GMF AeroAsia Logo" style="max-width: 600px; width: 600px; height: auto; display: block; margin: 0 auto;">
            </div>
            @else
            <!-- Fallback Logo Design -->
            <div style="display: flex; justify-content: center; align-items: center; margin: 60px auto; width: 100%; text-align: center;">
                <div style="width: 600px; height: 180px; border: 3px solid #1e40af; display: flex; flex-direction: column; justify-content: center; align-items: center; background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); border-radius: 15px; margin: 0 auto;">
                    <div style="font-size: 48px; font-weight: bold; color: white; margin-bottom: 12px;">GMF AeroAsia</div>
                    <div style="font-size: 18px; color: #e2e8f0; letter-spacing: 2px;">GARUDA INDONESIA GROUP</div>
                </div>
            </div>
            @endif
        @else
            <!-- Default Fallback Logo -->
            <div style="display: flex; justify-content: center; align-items: center; margin: 60px auto; width: 100%; text-align: center;">
                <div style="width: 600px; height: 180px; border: 3px solid #1e40af; display: flex; flex-direction: column; justify-content: center; align-items: center; background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); border-radius: 15px; margin: 0 auto;">
                    <div style="font-size: 48px; font-weight: bold; color: white; margin-bottom: 12px;">GMF AeroAsia</div>
                    <div style="font-size: 18px; color: #e2e8f0; letter-spacing: 2px;">GARUDA INDONESIA GROUP</div>
                </div>
            </div>
        @endif

        <!-- Report Information -->
        <div style="position: absolute; bottom: 100px; left: 50%; transform: translateX(-50%); text-align: center;">
            <div class="cover-info">{{ ($operator ?? 'Garuda Indonesia') }}</div>
            <div class="cover-period" style="margin-bottom: 15px;">
                {{ isset($period) ? \Carbon\Carbon::parse($period)->format('F Y') : 'N/A' }}
            </div>
        </div>

        <!-- Footer Information -->
        <div style="position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%); font-size: 12px; color: #9ca3af; font-family: Arial, sans-serif;">
            Generated by GMF Reliability Engineering & Services
        </div>
    </div>

    {{-- ===================================
         ENGINEERING RELIABILITY REPORT PAGE
         =================================== --}}
    <div style="page-break-before: always;" class="engineering-report-page">
        <div style="min-height: 100vh; display: flex; flex-direction: column; padding: 10mm;">

            <!-- Report Container with Border -->
            <div style="min-height: calc(100vh - 30mm); border: 2px solid #000000; margin: 0; padding: 10mm; position: relative; display: flex; flex-direction: column; box-sizing: border-box;">

                <!-- Header Logo Section -->
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
                        <!-- Garuda Indonesia Logo -->
                        <img src="{{ $garudaImageSrc }}" alt="Garuda Indonesia Logo" style="max-width: 200px; height: auto; margin: 0 auto;">
                        @else
                        <!-- Fallback Garuda Logo -->
                        <div style="width: 200px; height: 120px; margin: 0 auto; display: flex; align-items: center; justify-content: center; border: 2px solid #1e40af; background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); border-radius: 8px;">
                            <div style="color: white; font-weight: bold; font-size: 24px; text-align: center; line-height: 1.4;">
                                <div>GARUDA</div>
                                <div>INDONESIA</div>
                            </div>
                        </div>
                        @endif
                    @else
                        <!-- Default Garuda Logo -->
                        <div style="width: 200px; height: 120px; margin: 0 auto; display: flex; align-items: center; justify-content: center; border: 2px solid #1e40af; background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); border-radius: 8px;">
                            <div style="color: white; font-weight: bold; font-size: 24px; text-align: center; line-height: 1.4;">
                                <div>GARUDA</div>
                                <div>INDONESIA</div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Main Content Section -->
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

                <!-- Footer Section -->
                <div style="margin-top: auto;">

                    <!-- Report Attribution -->
                    <div style="text-align: right; margin-bottom: 20px;">
                        <div style="font-size: 14px; color: #000000; margin-bottom: 5px; font-family: Arial, sans-serif; line-height: 1.2;">
                            Issued by JKTMQGA
                        </div>
                        <div style="font-size: 14px; color: #000000; font-family: Arial, sans-serif; line-height: 1.2;">
                            Compiled by GMF Reliability & Engineering Services
                        </div>
                    </div>

                    <!-- GMF AeroAsia Footer Section -->
                    <div style="margin-bottom: 0;">

                        <!-- Company Banner -->
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
                                <!-- Fallback Banner -->
                                <div style="width: 100%; height: 50px; background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 14px; color: white; font-weight: bold; letter-spacing: 1px;">
                                    GMF AeroAsia - GARUDA INDONESIA GROUP
                                </div>
                                @endif
                            @else
                                <!-- Default Banner -->
                                <div style="width: 100%; height: 50px; background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 14px; color: white; font-weight: bold; letter-spacing: 1px;">
                                    GMF AeroAsia - GARUDA INDONESIA GROUP
                                </div>
                            @endif
                        </div>

                        <!-- Company Address Information -->
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
         Enhanced with StartYear column implementation
         =================================== --}}
    <div style="page-break-before: always;" class="table-responsive">
        <table class="compact-table">
            <thead>
                <!-- Report Title Headers -->
                <tr>
                    <th colspan="15" class="style2" style="text-align: center;">AIRCRAFT OPERATION SUMMARY</th>
                </tr>
                <tr>
                    <th colspan="15" class="style2" style="text-align: center;">{{ $aircraftType ?? 'N/A' }}</th>
                </tr>
                <!-- Period Range Header -->
                <tr>
                    <th colspan="15" style="text-align: center;">
                        {{ \Carbon\Carbon::parse($period)->subYear(1)->year }} - {{ \Carbon\Carbon::parse($period)->year }}
                    </th>
                </tr>
            </thead>
            <tbody>
                @php
                /**
                 * Data Processing for AOS Report
                 * Initialize period arrays and calculation functions
                 */

                // Generate 12-month period array for data iteration
                $periods = [];
                for ($i = 11; $i >= 0; $i--) {
                    $periods[] = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                }

                /**
                 * Helper functions yang sama dengan aos-pdf.blade.php
                 */
                $safeNumber = function($value, $default = 0) {
                    if (is_null($value) || !is_numeric($value)) {
                        return $default;
                    }
                    return floatval($value);
                };

                // Helper function for time formatting - SAMA dengan aos-pdf
                $formatTime = function($value) {
                    if (is_null($value) || $value === '' || $value === 0) {
                        return '0 : 00';
                    }
                    // If it's already in H:i format, return as is
                    if (is_string($value) && strpos($value, ':') !== false) {
                        return $value;
                    }
                    // If it's a number, convert to H:i format
                    if (is_numeric($value)) {
                        $hours = floor($value);
                        $minutes = round(($value - $hours) * 60);
                        return sprintf('%d : %02d', $hours, $minutes);
                    }
                    return $value ?: '0 : 00';
                };

                // Convert time string to decimal hours for calculation
                $timeToDecimal = function($timeString) {
                    if (is_null($timeString) || $timeString === '' || $timeString === 0) {
                        return 0;
                    }
                    if (is_numeric($timeString)) {
                        return floatval($timeString);
                    }
                    if (is_string($timeString) && strpos($timeString, ':') !== false) {
                        $parts = explode(':', $timeString);
                        $hours = intval($parts[0]);
                        $minutes = isset($parts[1]) ? intval($parts[1]) : 0;
                        return $hours + ($minutes / 60);
                    }
                    return 0;
                };

                // Calculate average time values for totals
                $calculateAvgTime = function($totalDecimal) use ($formatTime) {
                    $avg = $totalDecimal / 12;
                    return $formatTime($avg);
                };

                /**
                 * Calculate totals SAMA seperti aos-pdf.blade.php
                 */
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
                $totalTechnicalIncidentTotal = 0;
                $totalTechnicalIncidentRate = 0;
                $totalTechnicalCancellationTotal = 0;
                $totalDispatchReliability = 0;

                // Calculate totals for 12 months - SAMA seperti aos-pdf.blade.php
                foreach ($periods as $monthKey) {
                    $monthData = $aosData['reportData'][$monthKey] ?? [];

                    $totalAcInFleet += $safeNumber($monthData['acInFleet'] ?? 0);
                    $totalAcInService += $safeNumber($monthData['acInService'] ?? 0);
                    $totalDaysInService += $safeNumber($monthData['daysInService'] ?? 0);
                    $totalFlyingHoursTotal += $safeNumber($monthData['flyingHoursTotal'] ?? 0);
                    $totalRevenueFlyingHours += $safeNumber($monthData['revenueFlyingHours'] ?? 0);
                    $totalTakeOffTotal += $safeNumber($monthData['takeOffTotal'] ?? 0);
                    $totalRevenueTakeOff += $safeNumber($monthData['revenueTakeOff'] ?? 0);

                    // Convert time values to decimal for proper calculation
                    $totalFlightHoursPerTakeOffTotal += $timeToDecimal($monthData['flightHoursPerTakeOffTotal'] ?? 0);
                    $totalRevenueFlightHoursPerTakeOff += $timeToDecimal($monthData['revenueFlightHoursPerTakeOff'] ?? 0);
                    $totalDailyUtilizationFlyingHoursTotal += $timeToDecimal($monthData['dailyUtilizationFlyingHoursTotal'] ?? 0);
                    $totalRevenueDailyUtilizationFlyingHoursTotal += $timeToDecimal($monthData['revenueDailyUtilizationFlyingHoursTotal'] ?? 0);

                    $totalDailyUtilizationTakeOffTotal += $safeNumber($monthData['dailyUtilizationTakeOffTotal'] ?? 0);
                    $totalRevenueDailyUtilizationTakeOffTotal += $safeNumber($monthData['revenueDailyUtilizationTakeOffTotal'] ?? 0);
                    $totalTechnicalDelayTotal += $safeNumber($monthData['technicalDelayTotal'] ?? 0);

                    // Convert time values to decimal for proper calculation
                    $totalTotalDuration += $timeToDecimal($monthData['totalDuration'] ?? 0);
                    $totalAverageDuration += $timeToDecimal($monthData['averageDuration'] ?? 0);

                    $totalRatePer100TakeOff += $safeNumber($monthData['ratePer100TakeOff'] ?? 0);
                    $totalTechnicalIncidentTotal += $safeNumber($monthData['technicalIncidentTotal'] ?? 0);
                    $totalTechnicalIncidentRate += $safeNumber($monthData['technicalIncidentRate'] ?? 0);
                    $totalTechnicalCancellationTotal += $safeNumber($monthData['technicalCancellationTotal'] ?? 0);
                    $totalDispatchReliability += $safeNumber($monthData['dispatchReliability'] ?? 0);
                }
                @endphp

                <!-- Column Headers with Month Abbreviations -->
                <tr>
                    <td class="aos-label"></td>
                    <td><b>{{ \Carbon\Carbon::parse($period)->subYear(1)->year }}</b></td>
                    @for ($i = 11; $i >= 0; $i--)
                        <td><b>{{ substr(\Carbon\Carbon::parse($period)->subMonth($i)->format('F'), 0, 3) }}</b></td>
                    @endfor
                    <td><b>LAST 12 MTHS</b></td>
                </tr>

                <!-- Aircraft Fleet Metrics -->
                <tr>
                    <td class="aos-label">A/C in Fleet</td>
                    <td>{{ $formatNumber($totalAcInFleet / 12) }}</td>
                    @foreach ($periods as $monthKey)
                        @php
                            $acInFleet = $safeNumber($aosData['reportData'][$monthKey]['acInFleet'] ?? 0);
                        @endphp
                        <td>{{ round($acInFleet) }}</td>
                    @endforeach
                    <td>{{ $formatNumber($totalAcInFleet / 12) }}</td>
                </tr>

                <tr>
                    <td class="aos-label">A/C in Service</td>
                    <td>{{ $formatNumber($totalAcInService / 12) }}</td>
                    @foreach ($periods as $monthKey)
                        @php
                            $acInService = $safeNumber($aosData['reportData'][$monthKey]['acInService'] ?? 0);
                        @endphp
                        <td>{{ $formatNumber($acInService) }}</td>
                    @endforeach
                    <td>{{ $formatNumber($totalAcInService / 12) }}</td>
                </tr>

                <tr>
                    <td class="aos-label">A/C Days in Service</td>
                    <td>{{ round($safeNumber($totalDaysInService)) }}</td>
                    @foreach ($periods as $monthKey)
                        @php
                            $daysInService = $safeNumber($aosData['reportData'][$monthKey]['daysInService'] ?? 0);
                        @endphp
                        <td>{{ round($daysInService) }}</td>
                    @endforeach
                    <td>{{ round($safeNumber($totalDaysInService)) }}</td>
                </tr>

                <!-- Flight Operations Metrics -->
                <tr>
                    <td class="aos-label">Flying Hours - Total</td>
                    <td>{{ round($safeNumber($totalFlyingHoursTotal)) }}</td>
                    @foreach ($periods as $monthKey)
                        @php
                            $flyingHoursTotal = $safeNumber($aosData['reportData'][$monthKey]['flyingHoursTotal'] ?? 0);
                        @endphp
                        <td>{{ round($flyingHoursTotal) }}</td>
                    @endforeach
                    <td>{{ round($safeNumber($totalFlyingHoursTotal)) }}</td>
                </tr>

                <tr>
                    <td class="aos-label">- Revenue</td>
                    <td>{{ round($safeNumber($totalRevenueFlyingHours)) }}</td>
                    @foreach ($periods as $monthKey)
                        @php
                            $revenueFlyingHours = $safeNumber($aosData['reportData'][$monthKey]['revenueFlyingHours'] ?? 0);
                        @endphp
                        <td>{{ round($revenueFlyingHours) }}</td>
                    @endforeach
                    <td>{{ round($safeNumber($totalRevenueFlyingHours)) }}</td>
                </tr>

                <tr>
                    <td class="aos-label">Take Off - Total</td>
                    <td>{{ round($safeNumber($totalTakeOffTotal)) }}</td>
                    @foreach ($periods as $monthKey)
                        @php
                            $takeOffTotal = $safeNumber($aosData['reportData'][$monthKey]['takeOffTotal'] ?? 0);
                        @endphp
                        <td>{{ round($takeOffTotal) }}</td>
                    @endforeach
                    <td>{{ round($safeNumber($totalTakeOffTotal)) }}</td>
                </tr>

                <tr>
                    <td class="aos-label">- Revenue</td>
                    <td>{{ round($safeNumber($totalRevenueTakeOff)) }}</td>
                    @foreach ($periods as $monthKey)
                        @php
                            $revenueTakeOff = $safeNumber($aosData['reportData'][$monthKey]['revenueTakeOff'] ?? 0);
                        @endphp
                        <td>{{ round($revenueTakeOff) }}</td>
                    @endforeach
                    <td>{{ round($safeNumber($totalRevenueTakeOff)) }}</td>
                </tr>

                <!-- Flight Efficiency Metrics -->
                <tr>
                    <td class="aos-label">Flight Hours per Take Off - Total</td>
                    <td>{{ $calculateAvgTime($totalFlightHoursPerTakeOffTotal) }}</td>
                    @foreach ($periods as $monthKey)
                        @php
                            $flightHoursPerTakeOffTotal = $aosData['reportData'][$monthKey]['flightHoursPerTakeOffTotal'] ?? 0;
                        @endphp
                        <td>{{ $formatTime($flightHoursPerTakeOffTotal) }}</td>
                    @endforeach
                    <td>{{ $calculateAvgTime($totalFlightHoursPerTakeOffTotal) }}</td>
                </tr>

                <tr>
                    <td class="aos-label">- Revenue</td>
                    <td>{{ $calculateAvgTime($totalRevenueFlightHoursPerTakeOff) }}</td>
                    @foreach ($periods as $monthKey)
                        @php
                            $revenueFlightHoursPerTakeOff = $aosData['reportData'][$monthKey]['revenueFlightHoursPerTakeOff'] ?? 0;
                        @endphp
                        <td>{{ $formatTime($revenueFlightHoursPerTakeOff) }}</td>
                    @endforeach
                    <td>{{ $calculateAvgTime($totalRevenueFlightHoursPerTakeOff) }}</td>
                </tr>

                <!-- Daily Utilization Metrics -->
                <tr>
                    <td class="aos-label">Daily Utiliz - Total FH</td>
                    <td>{{ $calculateAvgTime($totalDailyUtilizationFlyingHoursTotal) }}</td>
                    @foreach ($periods as $monthKey)
                        @php
                            $dailyUtilizationFlyingHoursTotal = $aosData['reportData'][$monthKey]['dailyUtilizationFlyingHoursTotal'] ?? 0;
                        @endphp
                        <td>{{ $formatTime($dailyUtilizationFlyingHoursTotal) }}</td>
                    @endforeach
                    <td>{{ $calculateAvgTime($totalDailyUtilizationFlyingHoursTotal) }}</td>
                </tr>

                <tr>
                    <td class="aos-label">- Revenue FH</td>
                    <td>{{ $calculateAvgTime($totalRevenueDailyUtilizationFlyingHoursTotal) }}</td>
                    @foreach ($periods as $monthKey)
                        @php
                            $revenueDailyUtilizationFlyingHoursTotal = $aosData['reportData'][$monthKey]['revenueDailyUtilizationFlyingHoursTotal'] ?? 0;
                        @endphp
                        <td>{{ $formatTime($revenueDailyUtilizationFlyingHoursTotal) }}</td>
                    @endforeach
                    <td>{{ $calculateAvgTime($totalRevenueDailyUtilizationFlyingHoursTotal) }}</td>
                </tr>

                <tr>
                    <td class="aos-label">- Total FC</td>
                    <td>{{ $formatNumber($totalDailyUtilizationTakeOffTotal / 12) }}</td>
                    @foreach ($periods as $monthKey)
                        @php
                            $dailyUtilizationTakeOffTotal = $safeNumber($aosData['reportData'][$monthKey]['dailyUtilizationTakeOffTotal'] ?? 0);
                        @endphp
                        <td>{{ $formatNumber($dailyUtilizationTakeOffTotal) }}</td>
                    @endforeach
                    <td>{{ $formatNumber($totalDailyUtilizationTakeOffTotal / 12) }}</td>
                </tr>

                <tr>
                    <td class="aos-label">- Revenue FC</td>
                    <td>{{ $formatNumber($totalRevenueDailyUtilizationTakeOffTotal / 12) }}</td>
                    @foreach ($periods as $monthKey)
                        @php
                            $revenueDailyUtilizationTakeOffTotal = $safeNumber($aosData['reportData'][$monthKey]['revenueDailyUtilizationTakeOffTotal'] ?? 0);
                        @endphp
                        <td>{{ $formatNumber($revenueDailyUtilizationTakeOffTotal) }}</td>
                    @endforeach
                    <td>{{ $formatNumber($totalRevenueDailyUtilizationTakeOffTotal / 12) }}</td>
                </tr>

                <!-- Technical Performance Metrics -->
                <tr>
                    <td class="aos-label">Technical Delay - Total</td>
                    <td>{{ round($safeNumber($totalTechnicalDelayTotal)) }}</td>
                    @foreach ($periods as $monthKey)
                        @php
                            $technicalDelayTotal = $safeNumber($aosData['reportData'][$monthKey]['technicalDelayTotal'] ?? 0);
                        @endphp
                        <td>{{ round($technicalDelayTotal) }}</td>
                    @endforeach
                    <td>{{ round($safeNumber($totalTechnicalDelayTotal)) }}</td>
                </tr>

                <tr>
                    <td class="aos-label">- Tot Duration</td>
                    <td>{{ $calculateAvgTime($totalTotalDuration) }}</td>
                    @foreach ($periods as $monthKey)
                        @php
                            $totalDuration = $aosData['reportData'][$monthKey]['totalDuration'] ?? 0;
                        @endphp
                        <td>{{ $formatTime($totalDuration) }}</td>
                    @endforeach
                    <td>{{ $calculateAvgTime($totalTotalDuration) }}</td>
                </tr>

                <tr>
                    <td class="aos-label">- Avg Duration</td>
                    <td>{{ $calculateAvgTime($totalAverageDuration) }}</td>
                    @foreach ($periods as $monthKey)
                        @php
                            $averageDuration = $aosData['reportData'][$monthKey]['averageDuration'] ?? 0;
                        @endphp
                        <td>{{ $formatTime($averageDuration) }}</td>
                    @endforeach
                    <td>{{ $calculateAvgTime($totalAverageDuration) }}</td>
                </tr>

                <tr>
                    <td class="aos-label">- Rate / 100 Take Off</td>
                    <td>{{ $formatNumber($totalRatePer100TakeOff / 12) }}</td>
                    @foreach ($periods as $monthKey)
                        @php
                            $ratePer100TakeOff = $safeNumber($aosData['reportData'][$monthKey]['ratePer100TakeOff'] ?? 0);
                        @endphp
                        <td>{{ $formatNumber($ratePer100TakeOff) }}</td>
                    @endforeach
                    <td>{{ $formatNumber($totalRatePer100TakeOff / 12) }}</td>
                </tr>

                <!-- Technical Incident Metrics -->
                <tr>
                    <td class="aos-label">Technical Incident - Total</td>
                    <td>{{ round($safeNumber($totalTechnicalIncidentTotal)) }}</td>
                    @foreach ($periods as $monthKey)
                        @php
                            $technicalIncidentTotal = $safeNumber($aosData['reportData'][$monthKey]['technicalIncidentTotal'] ?? 0);
                        @endphp
                        <td>{{ round($technicalIncidentTotal) }}</td>
                    @endforeach
                    <td>{{ round($safeNumber($totalTechnicalIncidentTotal)) }}</td>
                </tr>

                <tr>
                    <td class="aos-label">- Rate/100 FC</td>
                    <td>{{ $formatNumber($totalTechnicalIncidentRate / 12) }}</td>
                    @foreach ($periods as $monthKey)
                        @php
                            $technicalIncidentRate = $safeNumber($aosData['reportData'][$monthKey]['technicalIncidentRate'] ?? 0);
                        @endphp
                        <td>{{ $formatNumber($technicalIncidentRate) }}</td>
                    @endforeach
                    <td>{{ $formatNumber($totalTechnicalIncidentRate / 12) }}</td>
                </tr>

                <!-- Technical Cancellation and Reliability Metrics -->
                <tr>
                    <td class="aos-label">Technical Cancellation - Total</td>
                    <td>{{ round($safeNumber($totalTechnicalCancellationTotal)) }}</td>
                    @foreach ($periods as $monthKey)
                        @php
                            $technicalCancellationTotal = $safeNumber($aosData['reportData'][$monthKey]['technicalCancellationTotal'] ?? 0);
                        @endphp
                        <td>{{ round($technicalCancellationTotal) }}</td>
                    @endforeach
                    <td>{{ round($safeNumber($totalTechnicalCancellationTotal)) }}</td>
                </tr>

                <tr>
                    <td class="aos-label">Dispatch Reliability (%)</td>
                    <td>{{ $formatNumber($totalDispatchReliability / 12) }}%</td>
                    @foreach ($periods as $monthKey)
                        @php
                            $dispatchReliability = $safeNumber($aosData['reportData'][$monthKey]['dispatchReliability'] ?? 0);
                        @endphp
                        <td>{{ $formatNumber($dispatchReliability) }}%</td>
                    @endforeach
                    <td>{{ $formatNumber($totalDispatchReliability / 12) }}%</td>
                </tr>
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
                <!-- Report Header -->
                <tr>
                    <th colspan="2">{{ $aircraftType ?? 'N/A' }}</th>
                    <th colspan="13">PILOT REPORT</th>
                </tr>

                <!-- Flight Hours Summary -->
                <tr>
                    <th colspan="2">Total Flight Hours</th>
                    <th>{{ round($pilotData['flyingHours2Before'] ?? 0) }}</th>
                    <th>{{ round($pilotData['flyingHoursBefore'] ?? 0) }}</th>
                    <th>{{ round($pilotData['flyingHoursTotal'] ?? 0) }}</th>
                    <th>{{ round($pilotData['fh3Last'] ?? 0) }}</th>
                    <th>{{ round($pilotData['fh12Last'] ?? 0) }}</th>
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
                    <th>{{ round($pilotData['flyingHours2Before'] ?? 0) }}</th>
                    <th>{{ round($pilotData['flyingHoursBefore'] ?? 0) }}</th>
                    <th>{{ round($pilotData['flyingHoursTotal'] ?? 0) }}</th>
                    <th>{{ round($pilotData['fh3Last'] ?? 0) }}</th>
                    <th>{{ round($pilotData['fh12Last'] ?? 0) }}</th>
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
                    <th>{{ round($pilotData['flyingCycles2Before'] ?? 0) }}</th>
                    <th>{{ round($pilotData['flyingCyclesBefore'] ?? 0) }}</th>
                    <th>{{ round($pilotData['flyingCyclesTotal'] ?? 0) }}</th>
                    <th>{{ round($pilotData['fc3Last'] ?? 0) }}</th>
                    <th>{{ round($pilotData['fc12Last'] ?? 0) }}</th>
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
