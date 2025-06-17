<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Combined Report - AOS & Pilot Report</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm; /* Dikurangi margin untuk lebih banyak ruang */
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10px; /* Diperbesar dari 8px */
            margin: 0;
            padding: 0;
        }
        
        table {
            border-collapse: collapse;
            width: 100%;
            font-family: Arial, sans-serif;
            font-size: 10px; /* Diperbesar dari 8px */
        }
        th, td {
            border: 1px solid #000;
            padding: 4px; /* Diperbesar dari 3px */
            text-align: center;
            font-size: 9px; /* Diperbesar dari 7px */
            word-wrap: break-word;
            line-height: 1.2; /* Tambahkan line-height */
        }
        .style1 {
            text-align: center;
        }
        .style2 {
            font-size: 18px; /* Diperbesar dari 16px */
            text-align: center;
            font-weight: bold;
        }
        h6 {
            font-size: 11px; /* Diperbesar dari 10px */
            text-align: left;
            margin: 4px; /* Diperbesar dari 3px */
            line-height: 1.3;
        }
        .issued {
            text-align: right;
            margin: 5px; /* Diperbesar dari 4px */
        }
        .alert-red {
            background-color: red;
            color: white;
        }
        table th,
        table td {
            vertical-align: middle;
            text-align: center;
        }
        .aos-label {
            text-align: left;
            font-weight: bold;
            font-size: 9px; /* Diperbesar dari 7px */
            width: 130px; /* Diperbesar dari 120px */
        }
        .ata-name {
            text-align: left;
            font-weight: bold;
            font-size: 8px; /* Diperbesar dari 7px */
            max-width: 110px; /* Diperbesar dari 100px */
        }
        
        .cover-page {
            text-align: center;
            padding: 30px 15px;
            min-height: 90vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .logo {
            max-width: 300px; 
            width: 100%;
            height: auto;
            margin-bottom: 30px;
        }
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
        
        /* Update table-responsive untuk font yang lebih besar */
        .table-responsive {
            overflow-x: auto;
            font-size: 9px; /* Diperbesar dari 6px */
        }
        
        .table-responsive table {
            min-width: 100%;
            font-size: 9px; /* Diperbesar dari 6px */
        }
        
        .table-responsive th,
        .table-responsive td {
            padding: 3px; /* Diperbesar dari 2px */
            font-size: 8px; /* Diperbesar dari 6px */
        }
        
        /* Tambahan untuk mengoptimalkan ruang tabel */
        .compact-table {
            margin-bottom: 15px;
        }
        
        .compact-table th,
        .compact-table td {
            padding: 3px 2px;
            font-size: 8px;
            line-height: 1.1;
        }
        
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
    // Helper function untuk menghilangkan trailing zero
    function formatNumber($value, $decimals = 2) {
        return rtrim(rtrim(number_format($value, $decimals), '0'), '.');
    }
    @endphp

    {{-- Cover Page --}}
    <div class="cover-page" style="display: flex; flex-direction: column; justify-content: center; align-items: center; min-height: 100vh; text-align: center; padding: 0;">

        <div class="cover-title" style="margin-bottom: 30px;">Fleet Reliability Report</div>
        
        <!-- Logo GMF AeroAsia - berada di tengah kertas -->
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
            {{-- Fallback Logo - di tengah kertas --}}
            <div style="display: flex; justify-content: center; align-items: center; margin: 60px auto; width: 100%; text-align: center;">
                <div style="width: 600px; height: 180px; border: 3px solid #1e40af; display: flex; flex-direction: column; justify-content: center; align-items: center; background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); border-radius: 15px; margin: 0 auto;">
                    <div style="font-size: 48px; font-weight: bold; color: white; margin-bottom: 12px;">GMF AeroAsia</div>
                    <div style="font-size: 18px; color: #e2e8f0; letter-spacing: 2px;">GARUDA INDONESIA GROUP</div>
                </div>
            </div>
            @endif
        @else
            {{-- Fallback Logo jika file tidak ada - di tengah kertas --}}
            <div style="display: flex; justify-content: center; align-items: center; margin: 60px auto; width: 100%; text-align: center;">
                <div style="width: 600px; height: 180px; border: 3px solid #1e40af; display: flex; flex-direction: column; justify-content: center; align-items: center; background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); border-radius: 15px; margin: 0 auto;">
                    <div style="font-size: 48px; font-weight: bold; color: white; margin-bottom: 12px;">GMF AeroAsia</div>
                    <div style="font-size: 18px; color: #e2e8f0; letter-spacing: 2px;">GARUDA INDONESIA GROUP</div>
                </div>
            </div>
        @endif
        
        <div style="position: absolute; bottom: 100px; left: 50%; transform: translateX(-50%); text-align: center;">
            <div class="cover-info">Garuda Indonesia</div>
            
            <div class="cover-period" style="margin-bottom: 15px;">
            {{ isset($period) ? \Carbon\Carbon::parse($period)->format('F Y') : 'N/A' }}
            </div>
        </div>
        
        <div style="position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%); font-size: 12px; color: #9ca3af; font-family: Arial, sans-serif;">
            Generated by GMF Reliability Engineering & Services
        </div>
    </div>

    {{-- Engineering Reliability Report Page --}}
    <div style="page-break-before: always;" class="engineering-report-page">
        <div style="min-height: 100vh; display: flex; flex-direction: column; padding: 10mm;">

        {{-- Container dengan border kotak yang lebih sesuai --}}
        <div style="min-height: calc(100vh - 30mm); border: 2px solid #000000; margin: 0; padding: 10mm; position: relative; display: flex; flex-direction: column; box-sizing: border-box;">
            
            {{-- Header dengan Logo Garuda Indonesia --}}
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
                    {{-- Fallback Logo Garuda Indonesia --}}
                    <div style="width: 200px; height: 120px; margin: 0 auto; display: flex; align-items: center; justify-content: center; border: 2px solid #1e40af; background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); border-radius: 8px;">
                        <div style="color: white; font-weight: bold; font-size: 24px; text-align: center; line-height: 1.4;">
                            <div>GARUDA</div>
                            <div>INDONESIA</div>
                        </div>
                    </div>
                    @endif
                @else
                    {{-- Fallback Logo jika file tidak ada --}}
                    <div style="width: 200px; height: 120px; margin: 0 auto; display: flex; align-items: center; justify-content: center; border: 2px solid #1e40af; background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); border-radius: 8px;">
                        <div style="color: white; font-weight: bold; font-size: 24px; text-align: center; line-height: 1.4;">
                            <div>GARUDA</div>
                            <div>INDONESIA</div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Main Content - ditengah halaman --}}
            <div style="flex: 1; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; margin: 250px 0;">
                <div style="font-size: 25px; font-weight: bold; color: #000000; font-family: Arial, sans-serif; line-height: 1.2;">
                    ENGINEERING
                </div>
                <div style="font-size: 25px; font-weight: bold; color: #000000; font-family: Arial, sans-serif; line-height: 1.2;">
                    RELIABILITY REPORT
                </div>
                <div style="font-size: 25px; font-weight: bold; color: #000000; font-family: Arial, sans-serif; line-height: 1.2;">
                    PERIODE: {{ strtoupper(\Carbon\Carbon::parse($period)->format('F Y')) }}
                </div>
            </div>

            {{-- Footer section - di bagian bawah --}}
            <div style="margin-top: auto;">
                {{-- Header Information --}}
                <div style="text-align: right; margin-bottom: 20px;">
                    <div style="font-size: 14px; color: #000000; margin-bottom: 5px; font-family: Arial, sans-serif; line-height: 1.2;">
                        Issued by JKTMQGA
                    </div>
                    <div style="font-size: 14px; color: #000000; font-family: Arial, sans-serif; line-height: 1.2;">
                        Compiled by GMF Reliability & Engineering Services
                    </div>
                </div>
                
                {{-- GMF AeroAsia Footer --}}
                <div style="margin-bottom: 0;">
                    {{-- Logo dan Banner --}}
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
                                // Pastikan format yang benar
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
                            {{-- Fallback Logo GMF --}}
                            <div style="width: 100%; height: 50px; background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 14px; color: white; font-weight: bold; letter-spacing: 1px;">
                                GMF AeroAsia - GARUDA INDONESIA GROUP
                            </div>
                            @endif
                        @else
                            {{-- Fallback Logo jika file tidak ada --}}
                            <div style="width: 100%; height: 50px; background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 14px; color: white; font-weight: bold; letter-spacing: 1px;">
                                GMF AeroAsia - GARUDA INDONESIA GROUP
                            </div>
                        @endif
                    </div>
                    
                    {{-- Informasi Alamat --}}
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

    {{-- AOS REPORT SECTION --}}
    <div style="page-break-before: always;" class="table-responsive">
        <table class="compact-table">
            <thead>
                <tr>
                    <th colspan="14" class="style2">AIRCRAFT OPERATION SUMMARY</th>
                </tr>
                <tr>
                    <th colspan="14" class="style2">{{ $aircraftTypeAos }}</th>
                </tr>
                <tr>
                    <th></th>
                    <th colspan="13">{{ \Carbon\Carbon::parse($period)->subYear(1)->year }}-{{ \Carbon\Carbon::parse($period)->year }}</th>
                </tr>
            </thead>
            <tbody>
                @php
                    // Inisialisasi total untuk setiap metrik
                    $totalAcInFleet = 0;
                    $totalAcInService = 0;
                    $totalDaysInService = 0;
                    $totalFlyingHoursTotal = 0;
                    $totalRevenueFlyingHours = 0;
                    $totalTakeOffTotal = 0;
                    $totalRevenueTakeOff = 0;
                    $totalDailyUtilizationTakeOffTotal = 0;
                    $totalRevenueDailyUtilizationTakeOffTotal = 0;
                    $totalTechnicalDelayTotal = 0;
                    $totalRatePer100TakeOff = 0;
                    $totalTechnicalIncidentTotal = 0;
                    $totalTechnicalIncidentRate = 0;
                    $totalTechnicalCancellationTotal = 0;
                    $totalDispatchReliability = 0;
                @endphp
                
                <tr>
                    <td class="aos-label"></td>
                    @for ($i = 11; $i >= 0; $i--)
                        <td><b>{{ substr(\Carbon\Carbon::parse($period)->subMonth($i)->format('F'), 0, 3) }}</b></td>
                    @endfor
                    <td><b>LAST 12 MTHS</b></td>
                </tr>
                
                <tr>
                    <td class="aos-label">A/C in Fleet</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                            $acInFleet = $aosData['reportData'][$monthKey]['acInFleet'] ?? 0;
                            $totalAcInFleet += $acInFleet;
                        @endphp
                        <td>{{ formatNumber($acInFleet) }}</td>
                    @endfor
                    <td>{{ formatNumber($totalAcInFleet / 12) }}</td>
                </tr>
                
                <tr>
                    <td class="aos-label">A/C in Service</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                            $acInService = $aosData['reportData'][$monthKey]['acInService'] ?? 0;
                            $totalAcInService += $acInService;
                        @endphp
                        <td>{{ formatNumber($acInService) }}</td>
                    @endfor
                    <td>{{ formatNumber($totalAcInService / 12) }}</td>
                </tr>
                
                <tr>
                    <td class="aos-label">A/C Days in Service</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                            $daysInService = $aosData['reportData'][$monthKey]['daysInService'] ?? 0;
                            $totalDaysInService += $daysInService;
                        @endphp
                        <td>{{ $daysInService }}</td>
                    @endfor
                    <td>{{ round($totalDaysInService) }}</td>
                </tr>
                
                <tr>
                    <td class="aos-label">Flying Hours - Total</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                            $flyingHoursTotal = $aosData['reportData'][$monthKey]['flyingHoursTotal'] ?? 0;
                            $totalFlyingHoursTotal += $flyingHoursTotal;
                        @endphp
                        <td>{{ round($flyingHoursTotal) }}</td>
                    @endfor
                    <td>{{ round($totalFlyingHoursTotal) }}</td>
                </tr>
                
                <tr>
                    <td class="aos-label">- Revenue</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                            $revenueFlyingHours = $aosData['reportData'][$monthKey]['revenueFlyingHours'] ?? 0;
                            $totalRevenueFlyingHours += $revenueFlyingHours;
                        @endphp
                        <td>{{ round($revenueFlyingHours) }}</td>
                    @endfor
                    <td>{{ round($totalRevenueFlyingHours) }}</td>
                </tr>
                
                <tr>
                    <td class="aos-label">Take Off - Total</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                            $takeOffTotal = $aosData['reportData'][$monthKey]['takeOffTotal'] ?? 0;
                            $totalTakeOffTotal += $takeOffTotal;
                        @endphp
                        <td>{{ $takeOffTotal }}</td>
                    @endfor
                    <td>{{ round($totalTakeOffTotal) }}</td>
                </tr>
                
                <tr>
                    <td class="aos-label">- Revenue</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                            $revenueTakeOff = $aosData['reportData'][$monthKey]['revenueTakeOff'] ?? 0;
                            $totalRevenueTakeOff += $revenueTakeOff;
                        @endphp
                        <td>{{ $revenueTakeOff }}</td>
                    @endfor
                    <td>{{ round($totalRevenueTakeOff) }}</td>
                </tr>
                
                <tr>
                    <td class="aos-label">Flight Hours per Take Off - Total</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                            $flightHoursPerTakeOffTotal = $aosData['reportData'][$monthKey]['flightHoursPerTakeOffTotal'] ?? '0 : 00';
                        @endphp
                        <td>{{ $flightHoursPerTakeOffTotal }}</td>
                    @endfor
                    <td>{{ $aosData['avgFlightHoursPerTakeOffTotal'] ?? '0 : 00' }}</td>
                </tr>
                
                <tr>
                    <td class="aos-label">- Revenue</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                            $revenueFlightHoursPerTakeOff = $aosData['reportData'][$monthKey]['revenueFlightHoursPerTakeOff'] ?? '0 : 00';
                        @endphp
                        <td>{{ $revenueFlightHoursPerTakeOff }}</td>
                    @endfor
                    <td>{{ $aosData['avgRevenueFlightHoursPerTakeOff'] ?? '0 : 00' }}</td>
                </tr>
                
                <tr>
                    <td class="aos-label">Daily Utiliz - Total FH</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                            $dailyUtilizationFlyingHoursTotal = $aosData['reportData'][$monthKey]['dailyUtilizationFlyingHoursTotal'] ?? '0 : 00';
                        @endphp
                        <td>{{ $dailyUtilizationFlyingHoursTotal }}</td>
                    @endfor
                    <td>{{ $aosData['avgDailyUtilizationFlyingHoursTotal'] ?? '0 : 00' }}</td>
                </tr>
                
                <tr>
                    <td class="aos-label">- Revenue FH</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                            $revenueDailyUtilizationFlyingHoursTotal = $aosData['reportData'][$monthKey]['revenueDailyUtilizationFlyingHoursTotal'] ?? '0 : 00';
                        @endphp
                        <td>{{ $revenueDailyUtilizationFlyingHoursTotal }}</td>
                    @endfor
                    <td>{{ $aosData['avgRevenueDailyUtilizationFlyingHoursTotal'] ?? '0 : 00' }}</td>
                </tr>
                
                <tr>
                    <td class="aos-label">- Total FC</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                            $dailyUtilizationTakeOffTotal = $aosData['reportData'][$monthKey]['dailyUtilizationTakeOffTotal'] ?? 0;
                            $totalDailyUtilizationTakeOffTotal += is_numeric($dailyUtilizationTakeOffTotal) ? $dailyUtilizationTakeOffTotal : 0;
                        @endphp
                        <td>{{ formatNumber($dailyUtilizationTakeOffTotal) }}</td>
                    @endfor
                    <td>{{ formatNumber($totalDailyUtilizationTakeOffTotal / 12) }}</td>
                </tr>
                
                <tr>
                    <td class="aos-label">- Revenue FC</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                            $revenueDailyUtilizationTakeOffTotal = $aosData['reportData'][$monthKey]['revenueDailyUtilizationTakeOffTotal'] ?? 0;
                            $totalRevenueDailyUtilizationTakeOffTotal += is_numeric($revenueDailyUtilizationTakeOffTotal) ? $revenueDailyUtilizationTakeOffTotal : 0;
                        @endphp
                        <td>{{ formatNumber($revenueDailyUtilizationTakeOffTotal) }}</td>
                    @endfor
                    <td>{{ formatNumber($totalRevenueDailyUtilizationTakeOffTotal / 12) }}</td>
                </tr>
                
                <tr>
                    <td class="aos-label">Technical Delay - Total</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                            $technicalDelayTotal = $aosData['reportData'][$monthKey]['technicalDelayTotal'] ?? 0;
                            $totalTechnicalDelayTotal += is_numeric($technicalDelayTotal) ? $technicalDelayTotal : 0;
                        @endphp
                        <td>{{ round($technicalDelayTotal) }}</td>
                    @endfor
                    <td>{{ round($totalTechnicalDelayTotal) }}</td>
                </tr>
                
                <tr>
                    <td class="aos-label">- Tot Duration</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                            $totalDuration = $aosData['reportData'][$monthKey]['totalDuration'] ?? '0 : 00';
                        @endphp
                        <td>{{ $totalDuration }}</td>
                    @endfor
                    <td>{{ $aosData['avgTotalDuration'] ?? '0 : 00' }}</td>
                </tr>
                
                <tr>
                    <td class="aos-label">- Avg Duration</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                            $averageDuration = $aosData['reportData'][$monthKey]['averageDuration'] ?? '0 : 00';
                        @endphp
                        <td>{{ $averageDuration }}</td>
                    @endfor
                    <td>{{ $aosData['avgAverageDuration'] ?? '0 : 00' }}</td>
                </tr>
                
                <tr>
                    <td class="aos-label">- Rate / 100 Take Off</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                            $ratePer100TakeOff = $aosData['reportData'][$monthKey]['ratePer100TakeOff'] ?? 0;
                            $totalRatePer100TakeOff += is_numeric($ratePer100TakeOff) ? $ratePer100TakeOff : 0;
                        @endphp
                        <td>{{ formatNumber($ratePer100TakeOff) }}</td>
                    @endfor
                    <td>{{ formatNumber($totalRatePer100TakeOff / 12) }}</td>
                </tr>
                
                <tr>
                    <td class="aos-label">Technical Incident - Total</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                            $technicalIncidentTotal = $aosData['reportData'][$monthKey]['technicalIncidentTotal'] ?? 0;
                            $totalTechnicalIncidentTotal += is_numeric($technicalIncidentTotal) ? $technicalIncidentTotal : 0;
                        @endphp
                        <td>{{ round($technicalIncidentTotal) }}</td>
                    @endfor
                    <td>{{ round($totalTechnicalIncidentTotal / 12) }}</td>
                </tr>
                
                <tr>
                    <td class="aos-label">- Rate/100 FC</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                            $technicalIncidentRate = $aosData['reportData'][$monthKey]['technicalIncidentRate'] ?? 0;
                            $totalTechnicalIncidentRate += is_numeric($technicalIncidentRate) ? $technicalIncidentRate : 0;
                        @endphp
                        <td>{{ formatNumber($technicalIncidentRate, 3) }}</td>
                    @endfor
                    <td>{{ formatNumber($totalTechnicalIncidentRate / 12) }}</td>
                </tr>
                
                <tr>
                    <td class="aos-label">Technical Cancellation - Total</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                            $technicalCancellationTotal = $aosData['reportData'][$monthKey]['technicalCancellationTotal'] ?? 0;
                            $totalTechnicalCancellationTotal += is_numeric($technicalCancellationTotal) ? $technicalCancellationTotal : 0;
                        @endphp
                        <td>{{ round($technicalCancellationTotal) }}</td>
                    @endfor
                    <td>{{ round($totalTechnicalCancellationTotal) }}</td>
                </tr>
                
                <tr>
                    <td class="aos-label">Dispatch Reliability (%)</td>
                    @for ($i = 11; $i >= 0; $i--)
                        @php
                            $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                            $dispatchReliability = $aosData['reportData'][$monthKey]['dispatchReliability'] ?? 0;
                            $totalDispatchReliability += is_numeric($dispatchReliability) ? $dispatchReliability : 0;
                        @endphp
                        <td>{{ formatNumber($dispatchReliability) }}%</td>
                    @endfor
                    <td>{{ formatNumber($totalDispatchReliability / 12) }}%</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- PILOT REPORT SECTION --}}
    <div style="page-break-before: always;">
        <table>
            <thead>
                <tr>
                    <th colspan="2">{{ $aircraftTypePilot }}</th>
                    <th colspan="13">PILOT REPORT</th>
                </tr>
                <tr>
                    <th colspan="2">Total Flight Hours</th>
                    <th>{{ round($pilotData['flyingHours2Before']) }}</th>
                    <th>{{ round($pilotData['flyingHoursBefore']) }}</th>
                    <th>{{ round($pilotData['flyingHoursTotal']) }}</th>
                    <th>{{ round($pilotData['fh3Last']) }}</th>
                    <th>{{ round($pilotData['fh12Last']) }}</th>
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
                @foreach ($pilotData['reportPerAta'] as $report)
                <tr>
                    <th>{{ $report['ata'] }}</th>
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
        <div class="notes-section">
            <h6>NOTE :</h6>
        </div>
        <h6>The Alert Level (AL) is based on monthly Technical Pilot Report / Maintenance Finding Report / Delay Rate of last Four Quarters (Average + 2 *STD)</h6>
        <h6>The Alert Status colomn will show "RED-1" if the last month Delay Rate exceed the AL, "RED-2" if this is true for the last two consecutive months,</h6>
        <h6>and "RED-3" if this is true for the last three consecutive months.</h6>
        <h6>The TREND colomn show an "UP" or "DOWN" when the rate has increased or decreased for 3 months</h6>
        <h6 class="issued">Issued by JKTMQGA and Compiled by GMF Reliability Engineering & Services</h6>
    </div>

    {{-- MAINTENANCE REPORT SECTION --}}
    <div style="page-break-before: always;">
        <table>
            <thead>
                <tr>
                    <th colspan="2">{{ $aircraftTypePilot }}</th>
                    <th colspan="13">MAINTENANCE FINDING REPORT</th>
                </tr>
                <tr>
                    <th colspan="2">Total Flight Hours</th>
                    <th>{{ round($pilotData['flyingHours2Before']) }}</th>
                    <th>{{ round($pilotData['flyingHoursBefore']) }}</th>
                    <th>{{ round($pilotData['flyingHoursTotal']) }}</th>
                    <th>{{ round($pilotData['fh3Last']) }}</th>
                    <th>{{ round($pilotData['fh12Last']) }}</th>
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
                @foreach ($pilotData['reportPerAta'] as $report)
                <tr>
                    <th>{{ $report['ata'] }}</th>
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
        <div class="notes-section">
            <h6>NOTE :</h6>
        </div>
        <h6>The Alert Level (AL) is based on monthly Technical Pilot Report / Maintenance Finding Report / Delay Rate of last Four Quarters (Average + 2 *STD)</h6>
        <h6>The Alert Status colomn will show "RED-1" if the last month Delay Rate exceed the AL, "RED-2" if this is true for the last two consecutive months,</h6>
        <h6>and "RED-3" if this is true for the last three consecutive months.</h6>
        <h6>The TREND colomn show an "UP" or "DOWN" when the rate has increased or decreased for 3 months</h6>
        <h6 class="issued">Issued by JKTMQGA and Compiled by GMF Reliability Engineering & Services</h6>
    </div>

    {{-- TECHNICAL DELAY REPORT SECTION --}}
    <div style="page-break-before: always;">
        <table>
            <thead>
                <tr>
                    <th colspan="2">{{ $aircraftTypePilot }}</th>
                    <th colspan="13">TECHNICAL DELAY > 15 MINUTES AND CANCELLATION</th>
                </tr>
                <tr>
                    <th colspan="2">Total Flight Cycles</th>
                    <th>{{ round($pilotData['flyingCycles2Before']) }}</th>
                    <th>{{ round($pilotData['flyingCyclesBefore']) }}</th>
                    <th>{{ round($pilotData['flyingCyclesTotal']) }}</th>
                    <th>{{ round($pilotData['fc3Last']) }}</th>
                    <th>{{ round($pilotData['fc12Last']) }}</th>
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
                @foreach ($pilotData['reportPerAta'] as $row)
                <tr>
                    <th>{{ $row['ata'] }}</th>
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