@php
    function formatNumber($value, $decimals = 2) {
        return rtrim(rtrim(number_format($value, $decimals), '0'), '.');
    }
@endphp

<!-- filepath: c:\Users\Noval Rais\Documents\Github Repository\GMF-Reliability\resources\views\report\combined-result.blade.php -->
<x-app-layout>
    <div class="mx-auto py-4 px-4 sm:px-6 lg:px-8">
        {{-- Loading Skeleton Overlay (hidden by default) --}}
        <div id="loadingSkeleton" class="fixed inset-0 bg-white bg-opacity-90 z-50 items-center justify-center hidden">
            <div class="max-w-6xl w-full mx-auto px-4 space-y-4">
                {{-- Header Skeleton --}}
                <div class="flex justify-between items-center mb-6">
                    <div class="h-6 bg-gray-300 rounded animate-pulse w-96"></div>
                    <div class="flex space-x-2">
                        <div class="h-10 w-32 bg-gray-300 rounded animate-pulse"></div>
                        <div class="h-10 w-32 bg-gray-300 rounded animate-pulse"></div>
                    </div>
                </div>
                
                {{-- Table Skeleton --}}
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    {{-- Table Header --}}
                    <div class="bg-gray-50 px-6 py-3 border-b border-gray-200">
                        <div class="flex space-x-4">
                            <div class="h-4 bg-gray-300 rounded animate-pulse w-24"></div>
                            <div class="h-4 bg-gray-300 rounded animate-pulse w-16"></div>
                            <div class="h-4 bg-gray-300 rounded animate-pulse w-16"></div>
                            <div class="h-4 bg-gray-300 rounded animate-pulse w-16"></div>
                            <div class="h-4 bg-gray-300 rounded animate-pulse w-16"></div>
                            <div class="h-4 bg-gray-300 rounded animate-pulse w-16"></div>
                        </div>
                    </div>
                    
                    {{-- Table Rows --}}
                    <div class="divide-y divide-gray-200">
                        @for ($i = 0; $i < 8; $i++)
                        <div class="px-6 py-4">
                            <div class="flex space-x-4">
                                <div class="h-4 bg-gray-200 rounded animate-pulse w-48"></div>
                                <div class="h-4 bg-gray-200 rounded animate-pulse w-16"></div>
                                <div class="h-4 bg-gray-200 rounded animate-pulse w-16"></div>
                                <div class="h-4 bg-gray-200 rounded animate-pulse w-16"></div>
                                <div class="h-4 bg-gray-200 rounded animate-pulse w-16"></div>
                                <div class="h-4 bg-gray-200 rounded animate-pulse w-16"></div>
                            </div>
                        </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>

        {{-- Header dengan Back Button dan Export Buttons yang sejajar --}}
        <div class="flex justify-between items-center mb-4">
             {{-- Back to Report Button --}}
            <button id="backBtn" onclick="showLoadingAndGoBack()" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                <svg id="backIcon" class="w-4 h-4 mr-2 transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span id="backText">Back to Report</span>
                {{-- Loading Spinner (hidden by default) --}}
                <svg id="loadingSpinner" class="hidden animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>

            {{-- Export Buttons --}}
            <div class="flex space-x-2">
                <form action="{{ route('report.combined.export.pdf') }}" method="POST">
                    @csrf
                    <input type="hidden" name="period" value="{{ $period }}">
                    <input type="hidden" name="operator" value="{{ $operator }}">
                    <input type="hidden" name="aircraft_type_aos" value="{{ $aircraftTypeAos }}">
                    <input type="hidden" name="aircraft_type_pilot" value="{{ $aircraftTypePilot }}">
                    <button type="submit" class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-red-500 border border-red-500 rounded-md shadow-sm hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                        Export to PDF
                    </button>
                </form>
            </div>
        </div>

        <div class="flex justify-between items-center">
            <p class="py-2">Combined Report - AOS Type: {{ $aircraftTypeAos }}, Pilot Type: {{ $aircraftTypePilot }}, Period: {{ \Carbon\Carbon::parse($period)->format('F Y') }}</p>
        </div>

        {{-- AIRCRAFT OPERATION SUMMARY SECTION --}}
        <div class="mt-4 flow-root px-4 bg-blue-200">
            <h1 class="text-center mb-3 mt-3 text-lg font-bold">AIRCRAFT OPERATION SUMMARY</h1>
            
            <div class="mb-4">
                <p class="py-2">Data Aircraft Operation Summary Type: {{ $aircraftTypeAos }} pada {{ \Carbon\Carbon::parse($period)->format('F Y') }}</p>
            </div>

            <div class="mt-3 flow-root">
                <x-table.index>
                    <x-table.thead>
                        <tr>
                            <x-table.th>Metrics</x-table.th>
                            @for ($i = 11; $i >= 0; $i--)
                                <x-table.th>{{ substr(\Carbon\Carbon::parse($period)->subMonth($i)->format('F'), 0, 3) }}</x-table.th>
                            @endfor
                            <x-table.th>Last 12 MTHS</x-table.th>
                        </tr>
                    </x-table.thead>
                    <x-table.tbody>
                        @if(isset($aosData['reportData']))
                            @php
                            $reportData = $aosData['reportData'];
                            // Inisialisasi total untuk setiap metrik
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
                            $totalTechnicalIncidentTotal= 0;
                            $totalTechnicalIncidentRate=0;
                            $totalTechnicalCancellationTotal=0;
                            $totalDispatchReliability=0;
                            @endphp
                            <tr>
                                <x-table.th class="text-left">A/C In Fleet</x-table.th>
                                @for ($i = 11; $i >= 0; $i--)
                                    @php
                                        $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                                        $acInFleet = $reportData[$monthKey]['acInFleet'] ?? 0;
                                        $totalAcInFleet += $acInFleet;
                                    @endphp
                                    <x-table.td>{{ formatNumber($acInFleet, 0) }}</x-table.td>
                                @endfor
                                <x-table.td>{{ formatNumber($totalAcInFleet / 12) }}</x-table.td>
                            </tr>
                            
                            <tr>
                                <x-table.th class="text-left">A/C In Service (Revenue)</x-table.th>
                                @for ($i = 11; $i >= 0; $i--)
                                    @php
                                        $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                                        $acInService = $reportData[$monthKey]['acInService'] ?? 0;
                                        $totalAcInService += $acInService;
                                    @endphp
                                    <x-table.td>{{ formatNumber($acInService) }}</x-table.td>
                                @endfor
                                <x-table.td>{{ formatNumber($totalAcInService / 12) }}</x-table.td>
                            </tr>
                            
                            <tr>
                                <x-table.th class="text-left">A/C Days In Service (Revenue)</x-table.th>
                                @for ($i = 11; $i >= 0; $i--)
                                    @php
                                        $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                                        $daysInService = $aosData['reportData'][$monthKey]['daysInService'] ?? 0;
                                        $totalDaysInService += $daysInService;
                                    @endphp
                                    <x-table.td>{{ formatNumber($daysInService, 0) }}</x-table.td>
                                @endfor
                                <x-table.td>{{ formatNumber($totalDaysInService, 0) }}</x-table.td>
                            </tr>

                            <tr>
                                <x-table.th class="text-left">Flying Hours - Total</x-table.th>
                                @for ($i = 11; $i >= 0; $i--)
                                    @php
                                        $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                                        $flyingHoursTotal = $reportData[$monthKey]['flyingHoursTotal'] ?? 0;
                                        $totalFlyingHoursTotal += $flyingHoursTotal;
                                    @endphp
                                    <x-table.td>{{ formatNumber($flyingHoursTotal, 0) }}</x-table.td>
                                @endfor
                                <x-table.td>{{ formatNumber($totalFlyingHoursTotal, 0) }}</x-table.td>
                            </tr>
                            
                            <tr>
                                <x-table.th class="text-left">- Revenue</x-table.th>
                                @for ($i = 11; $i >= 0; $i--)
                                    @php
                                        $revenueFlyingHours = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['revenueFlyingHours'];
                                        $totalRevenueFlyingHours += $revenueFlyingHours;
                                    @endphp
                                    <x-table.td>{{ formatNumber($revenueFlyingHours, 0) }}</x-table.td>
                                @endfor
                                <x-table.td>{{ formatNumber($totalRevenueFlyingHours, 0) }}</x-table.td>
                            </tr>

                            <tr>
                                <x-table.th class="text-left">Take Off - Total</x-table.th>
                                @for ($i = 11; $i >= 0; $i--)
                                    @php
                                        $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                                        $takeOffTotal = $reportData[$monthKey]['takeOffTotal'] ?? 0;
                                        $totalTakeOffTotal += $takeOffTotal;
                                    @endphp
                                    <x-table.td>{{ formatNumber($takeOffTotal, 0) }}</x-table.td>
                                @endfor
                                <x-table.td>{{ formatNumber($totalTakeOffTotal, 0) }}</x-table.td>
                            </tr>
                            
                            <tr>
                                <x-table.th class="text-left">- Revenue</x-table.th>
                                @for ($i = 11; $i >= 0; $i--)
                                    @php
                                        $revenueTakeOff = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['revenueTakeOff'];
                                        $totalRevenueTakeOff += $revenueTakeOff;
                                    @endphp
                                    <x-table.td>{{ formatNumber($revenueTakeOff, 0) }}</x-table.td>
                                @endfor
                                <x-table.td>{{ formatNumber($totalRevenueTakeOff, 0) }}</x-table.td>
                            </tr>

                            <tr>
                                <x-table.th class="text-left">Flight Hours per Take Off - Total</x-table.th>
                                @for ($i = 11; $i >= 0; $i--)
                                    @php
                                        $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                                        $flightHoursPerTakeOffTotal = $reportData[$monthKey]['flightHoursPerTakeOffTotal'] ?? '0 : 00';
                                    @endphp
                                    <x-table.td>{{ $flightHoursPerTakeOffTotal }}</x-table.td>
                                @endfor
                                <x-table.td>{{ $aosData['avgFlightHoursPerTakeOffTotal'] ?? '0 : 00' }}</x-table.td>
                            </tr>

                            <tr>
                                <x-table.th class="text-left">- Revenue</x-table.th>
                                @for ($i = 11; $i >= 0; $i--)
                                    @php
                                        $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                                        $revenueFlightHoursPerTakeOff = $reportData[$monthKey]['revenueFlightHoursPerTakeOff'] ?? '0 : 00';
                                    @endphp
                                    <x-table.td>{{ $revenueFlightHoursPerTakeOff }}</x-table.td>
                                @endfor
                                <x-table.td>{{ $aosData['avgRevenueFlightHoursPerTakeOff'] ?? '0 : 00' }}</x-table.td>
                            </tr>

                            <tr>
                                <x-table.th class="text-left">Daily Utiliz - Total FH</x-table.th>
                                @for ($i = 11; $i >= 0; $i--)
                                    @php
                                        $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                                        $dailyUtilizationFlyingHoursTotal = $reportData[$monthKey]['dailyUtilizationFlyingHoursTotal'] ?? '0 : 00';
                                    @endphp
                                    <x-table.td>{{ $dailyUtilizationFlyingHoursTotal }}</x-table.td>
                                @endfor
                                <x-table.td>{{ $aosData['avgDailyUtilizationFlyingHoursTotal'] ?? '0 : 00' }}</x-table.td>
                            </tr>

                            <tr>
                                <x-table.th class="text-left">- Revenue FH</x-table.th>
                                @for ($i = 11; $i >= 0; $i--)
                                    @php
                                        $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                                        $revenueDailyUtilizationFlyingHoursTotal = $reportData[$monthKey]['revenueDailyUtilizationFlyingHoursTotal'] ?? '0 : 00';
                                    @endphp
                                    <x-table.td>{{ $revenueDailyUtilizationFlyingHoursTotal }}</x-table.td>
                                @endfor
                                <x-table.td>{{ $aosData['avgRevenueDailyUtilizationFlyingHoursTotal'] ?? '0 : 00' }}</x-table.td>
                            </tr>

                            <tr>
                                <x-table.th class="text-left">- Total FC</x-table.th>
                                @for ($i = 11; $i >= 0; $i--)
                                    @php
                                        $dailyUtilizationTakeOffTotal = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['dailyUtilizationTakeOffTotal'];
                                        $totalDailyUtilizationTakeOffTotal += is_numeric($dailyUtilizationTakeOffTotal) ? $dailyUtilizationTakeOffTotal : 0;
                                    @endphp
                                    <x-table.td>{{ formatNumber($dailyUtilizationTakeOffTotal) }}</x-table.td>
                                @endfor
                                <x-table.td>{{ formatNumber($totalDailyUtilizationTakeOffTotal / 12) }}</x-table.td>
                            </tr>

                            <tr>
                                <x-table.th class="text-left">- Revenue FC</x-table.th>
                                @for ($i = 11; $i >= 0; $i--)
                                    @php
                                        $revenueDailyUtilizationTakeOffTotal = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['revenueDailyUtilizationTakeOffTotal'];
                                        $totalRevenueDailyUtilizationTakeOffTotal += is_numeric($revenueDailyUtilizationTakeOffTotal) ? $revenueDailyUtilizationTakeOffTotal : 0;
                                    @endphp
                                    <x-table.td>{{ formatNumber($revenueDailyUtilizationTakeOffTotal) }}</x-table.td>
                                @endfor
                                <x-table.td>{{ formatNumber($totalRevenueDailyUtilizationTakeOffTotal / 12) }}</x-table.td>
                            </tr>

                             <tr>
                                <x-table.th class="text-left">Technical Delay - Total</x-table.th>
                                @for ($i = 11; $i >= 0; $i--)
                                    @php
                                        $technicalDelayTotal = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['technicalDelayTotal'];
                                        $totalTechnicalDelayTotal += is_numeric($technicalDelayTotal) ? $technicalDelayTotal:0;
                                    @endphp
                                    <x-table.td>{{ formatNumber($technicalDelayTotal, 0) }}</x-table.td>
                                @endfor
                                <x-table.td>{{ formatNumber($totalTechnicalDelayTotal, 0) }}</x-table.td>
                            </tr>

                            <tr>
                                <x-table.th class="text-left">- Tot Duration</x-table.th>
                                @for ($i = 11; $i >= 0; $i--)
                                    @php
                                        $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                                        $totalDuration = $reportData[$monthKey]['totalDuration'] ?? 0;
                                    @endphp
                                    <x-table.td>{{ $totalDuration }}</x-table.td>
                                @endfor
                                <x-table.td>{{ isset($aosData['avgTotalDuration']) ? $aosData['avgTotalDuration'] : '0' }}</x-table.td>
                            </tr>
                            
                            <tr>
                                <x-table.th class="text-left">- Avg Duration</x-table.th>
                                @for ($i = 11; $i >= 0; $i--)
                                    @php
                                        $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                                        $averageDuration = $reportData[$monthKey]['averageDuration'] ?? 0;
                                    @endphp
                                    <x-table.td>{{ $averageDuration }}</x-table.td>
                                @endfor
                                <x-table.td>{{ isset($aosData['avgAverageDuration']) ? $aosData['avgAverageDuration'] : '0' }}</x-table.td>
                            </tr>

                            <tr>
                                <x-table.th class="text-left">- Rate/100 Take-Off</x-table.th>
                                @for ($i = 11; $i >= 0; $i--)
                                    @php
                                        $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                                        $ratePer100TakeOff = $reportData[$monthKey]['ratePer100TakeOff'] ?? 0;
                                        $ratePer100TakeOff = is_numeric($ratePer100TakeOff) ? (float)$ratePer100TakeOff : 0;
                                        $totalRatePer100TakeOff += $ratePer100TakeOff;
                                    @endphp
                                    <x-table.td>{{ formatNumber($ratePer100TakeOff) }}</x-table.td>
                                @endfor
                                <x-table.td>{{ formatNumber($totalRatePer100TakeOff / 12) }}</x-table.td>
                            </tr>

                            <tr>
                                <x-table.th class="text-left">Technical Incident - Total</x-table.th>
                                @for ($i = 11; $i >= 0; $i--)
                                    @php
                                        $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                                        $technicalIncidentTotal = $reportData[$monthKey]['technicalIncidentTotal'] ?? 0;
                                        $technicalIncidentTotal = is_numeric($technicalIncidentTotal) ? (float)$technicalIncidentTotal : 0;
                                        $totalTechnicalIncidentTotal += $technicalIncidentTotal;
                                    @endphp
                                    <x-table.td>{{ formatNumber($technicalIncidentTotal, 0) }}</x-table.td>
                                @endfor
                                <x-table.td>{{ formatNumber($totalTechnicalIncidentTotal, 0) }}</x-table.td>
                            </tr>

                            <tr>
                                <x-table.th class="text-left">- Rate / 100 FC</x-table.th>
                                @for ($i = 11; $i >= 0; $i--)
                                    @php
                                        $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                                        $technicalIncidentRate = $reportData[$monthKey]['technicalIncidentRate'] ?? 0;
                                        $technicalIncidentRate = is_numeric($technicalIncidentRate) ? (float)$technicalIncidentRate : 0;
                                        $totalTechnicalIncidentRate += $technicalIncidentRate;
                                    @endphp
                                    <x-table.td>{{ $technicalIncidentRate == 0 ? '0' : formatNumber($technicalIncidentRate, 3) }}</x-table.td>
                                @endfor
                                <x-table.td>{{ formatNumber($totalTechnicalIncidentRate / 12) }}</x-table.td>
                            </tr>

                            <tr>
                                <x-table.th class="text-left">Technical Cancellation - Total</x-table.th>
                                @for ($i = 11; $i >= 0; $i--)
                                    @php
                                        $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                                        $technicalCancellationTotal = $reportData[$monthKey]['technicalCancellationTotal'] ?? 0;
                                        $technicalCancellationTotal = is_numeric($technicalCancellationTotal) ? (float)$technicalCancellationTotal : 0;
                                        $totalTechnicalCancellationTotal += $technicalCancellationTotal;
                                    @endphp
                                    <x-table.td>{{ formatNumber($technicalCancellationTotal, 0) }}</x-table.td>
                                @endfor
                                <x-table.td>{{ formatNumber($totalTechnicalCancellationTotal, 0) }}</x-table.td>
                            </tr>

                            <tr>
                                <x-table.th class="text-left">Dispatch Reliability (%)</x-table.th>
                                @for ($i = 11; $i >= 0; $i--)
                                    @php
                                        $monthKey = \Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m');
                                        $dispatchReliability = $reportData[$monthKey]['dispatchReliability'] ?? 0;
                                        $dispatchReliability = is_numeric($dispatchReliability) ? (float)$dispatchReliability : 0;
                                        $totalDispatchReliability += $dispatchReliability;
                                    @endphp
                                    <x-table.td>{{ formatNumber($dispatchReliability) }}%</x-table.td>
                                @endfor
                                <x-table.td>{{ formatNumber($totalDispatchReliability / 12) }}%</x-table.td>
                            </tr>
                        @else
                            <tr>
                                <x-table.td colspan="14" class="text-center text-gray-500">No AOS data available</x-table.td>
                            </tr>
                        @endif
                    </x-table.tbody>
                </x-table.index>
            </div>
        </div>

        {{-- PILOT REPORT SECTION --}}
        @if(isset($pilotData['reportPerAta']))
        <div class="mt-4 flow-root px-4 bg-green-200">
            <h1 class="text-center mb-3 mt-3 text-lg font-bold">PILOT REPORT</h1>
            <p class="text-center mb-3">Aircraft Type: {{ $aircraftTypePilot }}</p>
            
            <x-table.index>
                <x-table.thead>
                    <tr>
                        <x-table.th colspan="2">Total Flight Hours</x-table.th>
                        <x-table.th>{{ formatNumber($pilotData['flyingHours2Before'] ?? 0, 0) }}</x-table.th>
                        <x-table.th>{{ formatNumber($pilotData['flyingHoursBefore'] ?? 0, 0) }}</x-table.th>
                        <x-table.th>{{ formatNumber($pilotData['flyingHoursTotal'] ?? 0, 0) }}</x-table.th>
                        <x-table.th>{{ formatNumber($pilotData['fh3Last'] ?? 0, 0) }}</x-table.th>
                        <x-table.th>{{ formatNumber($pilotData['fh12Last'] ?? 0, 0) }}</x-table.th>
                        <x-table.th colspan="8"></x-table.th>
                    </tr>
                    <tr>
                        <x-table.th colspan="2" rowspan="2">ATA CHAPTER</x-table.th>
                        <x-table.th rowspan="2">{{ substr(\Carbon\Carbon::parse($period)->subMonths(2)->format('F'), 0, 3) }}</x-table.th>
                        <x-table.th rowspan="2">{{ substr(\Carbon\Carbon::parse($period)->subMonth(1)->format('F'), 0, 3) }}</x-table.th>
                        <x-table.th rowspan="2">{{ substr(\Carbon\Carbon::parse($period)->format('F'), 0, 3) }}</x-table.th>
                        <x-table.th>Last 3</x-table.th>
                        <x-table.th>Last 12</x-table.th>
                        <x-table.th>{{ substr(\Carbon\Carbon::parse($period)->subMonths(2)->format('F'), 0, 3) }}</x-table.th>
                        <x-table.th>{{ substr(\Carbon\Carbon::parse($period)->subMonth(1)->format('F'), 0, 3) }}</x-table.th>
                        <x-table.th>{{ substr(\Carbon\Carbon::parse($period)->format('F'), 0, 3) }}</x-table.th>
                        <x-table.th>3 Months</x-table.th>
                        <x-table.th>12 Months</x-table.th>
                        <x-table.th>ALERT</x-table.th>
                        <x-table.th>ALERT</x-table.th>
                        <x-table.th rowspan="2">TREND</x-table.th>
                    </tr>
                    <tr>
                        <x-table.th>Months</x-table.th>
                        <x-table.th>Months</x-table.th>
                        <x-table.th>RATE</x-table.th>
                        <x-table.th>RATE</x-table.th>
                        <x-table.th>RATE</x-table.th>
                        <x-table.th>RATE</x-table.th>
                        <x-table.th>RATE</x-table.th>
                        <x-table.th>LEVEL</x-table.th>
                        <x-table.th>STATUS</x-table.th>
                    </tr>
                </x-table.thead>
                <x-table.tbody>
                    @foreach ($pilotData['reportPerAta'] as $row)
                    <tr>
                        <x-table.th>{{ $row['ata'] }}</x-table.th>
                        <x-table.th>{{ $row['ata_name'] ?? '' }}</x-table.th>
                        <x-table.td><a href="#">{{ $row['pirepCountTwoMonthsAgo'] }}</a></x-table.td>
                        <x-table.td><a href="#">{{ $row['pirepCountBefore'] }}</a></x-table.td>
                        <x-table.td><a href="#">{{ $row['pirepCount'] }}</a></x-table.td>
                        <x-table.td>{{ $row['pirep3Month'] }}</x-table.td>
                        <x-table.td>{{ $row['pirep12Month'] }}</x-table.td>
                        <x-table.td>{{ formatNumber($row['pirep2Rate']) }}</x-table.td>
                        <x-table.td>{{ formatNumber($row['pirep1Rate']) }}</x-table.td>
                        <x-table.td>{{ formatNumber($row['pirepRate']) }}</x-table.td>
                        <x-table.td>{{ formatNumber($row['pirepRate3Month']) }}</x-table.td>
                        <x-table.td>{{ formatNumber($row['pirepRate12Month']) }}</x-table.td>
                        <x-table.td>{{ formatNumber($row['pirepAlertLevel']) }}</x-table.td>
                        <x-table.td>{{ $row['pirepAlertStatus'] }}</x-table.td>
                        <x-table.td>{{ $row['pirepTrend'] }}</x-table.td>
                    </tr>  
                    @endforeach
                </x-table.tbody>
            </x-table.index>
        </div>
        @endif

        {{-- MAINTENANCE REPORT SECTION --}}
        @if(isset($pilotData['reportPerAta']))
        <div class="mt-4 flow-root px-4 bg-green-200">
            <h1 class="text-center mb-3 mt-3 text-lg font-bold">MAINTENANCE REPORT</h1>
            <p class="text-center mb-3">Aircraft Type: {{ $aircraftTypePilot }}</p>
            
            <x-table.index>
                <x-table.thead>
                    <tr>
                        <x-table.th colspan="2">Total Flight Hours</x-table.th>
                        <x-table.th>{{ formatNumber($pilotData['flyingHours2Before'] ?? 0, 0) }}</x-table.th>
                        <x-table.th>{{ formatNumber($pilotData['flyingHoursBefore'] ?? 0, 0) }}</x-table.th>
                        <x-table.th>{{ formatNumber($pilotData['flyingHoursTotal'] ?? 0, 0) }}</x-table.th>
                        <x-table.th>{{ formatNumber($pilotData['fh3Last'] ?? 0, 0) }}</x-table.th>
                        <x-table.th>{{ formatNumber($pilotData['fh12Last'] ?? 0, 0) }}</x-table.th>
                        <x-table.th colspan="8"></x-table.th>
                    </tr>
                    <tr>
                        <x-table.th colspan="2" rowspan="2">ATA CHAPTER</x-table.th>
                        <x-table.th rowspan="2">{{ substr(\Carbon\Carbon::parse($period)->subMonths(2)->format('F'), 0, 3) }}</x-table.th>
                        <x-table.th rowspan="2">{{ substr(\Carbon\Carbon::parse($period)->subMonth(1)->format('F'), 0, 3) }}</x-table.th>
                        <x-table.th rowspan="2">{{ substr(\Carbon\Carbon::parse($period)->format('F'), 0, 3) }}</x-table.th>
                        <x-table.th>Last 3</x-table.th>
                        <x-table.th>Last 12</x-table.th>
                        <x-table.th>{{ substr(\Carbon\Carbon::parse($period)->subMonths(2)->format('F'), 0, 3) }}</x-table.th>
                        <x-table.th>{{ substr(\Carbon\Carbon::parse($period)->subMonth(1)->format('F'), 0, 3) }}</x-table.th>
                        <x-table.th>{{ substr(\Carbon\Carbon::parse($period)->format('F'), 0, 3) }}</x-table.th>
                        <x-table.th>3 Months</x-table.th>
                        <x-table.th>12 Months</x-table.th>
                        <x-table.th>ALERT</x-table.th>
                        <x-table.th>ALERT</x-table.th>
                        <x-table.th rowspan="2">TREND</x-table.th>
                    </tr>
                    <tr>
                        <x-table.th>Months</x-table.th>
                        <x-table.th>Months</x-table.th>
                        <x-table.th>RATE</x-table.th>
                        <x-table.th>RATE</x-table.th>
                        <x-table.th>RATE</x-table.th>
                        <x-table.th>RATE</x-table.th>
                        <x-table.th>RATE</x-table.th>
                        <x-table.th>LEVEL</x-table.th>
                        <x-table.th>STATUS</x-table.th>
                    </tr>
                </x-table.thead>
                <x-table.tbody>
                    @foreach ($pilotData['reportPerAta'] as $row)
                    <tr>
                        <x-table.th>{{ $row['ata'] }}</x-table.th>
                        <x-table.th>{{ $row['ata_name'] ?? '' }}</x-table.th>
                        <x-table.td><a href="#">{{ $row['marepCountTwoMonthsAgo'] }}</a></x-table.td>
                        <x-table.td><a href="#">{{ $row['marepCountBefore'] }}</a></x-table.td>
                        <x-table.td><a href="#">{{ $row['marepCount'] }}</a></x-table.td>
                        <x-table.td>{{ $row['marep3Month'] }}</x-table.td>
                        <x-table.td>{{ $row['marep12Month'] }}</x-table.td>
                        <x-table.td>{{ formatNumber($row['marep2Rate']) }}</x-table.td>
                        <x-table.td>{{ formatNumber($row['marep1Rate']) }}</x-table.td>
                        <x-table.td>{{ formatNumber($row['marepRate']) }}</x-table.td>
                        <x-table.td>{{ formatNumber($row['marepRate3Month']) }}</x-table.td>
                        <x-table.td>{{ formatNumber($row['marepRate12Month']) }}</x-table.td>
                        <x-table.td>{{ formatNumber($row['marepAlertLevel']) }}</x-table.td>
                        <x-table.td>{{ $row['marepAlertStatus'] }}</x-table.td>
                        <x-table.td>{{ $row['marepTrend'] }}</x-table.td>
                    </tr>  
                    @endforeach
                </x-table.tbody>
            </x-table.index>
        </div>
        @endif

        {{-- TECHNICAL DELAY REPORT SECTION --}}
        @if(isset($pilotData['reportPerAta']))
        <div class="mt-4 flow-root px-4 bg-green-200">
            <h1 class="text-center mb-3 mt-3 text-lg font-bold">TECHNICAL DELAY REPORT</h1>
            <p class="text-center mb-3">Aircraft Type: {{ $aircraftTypePilot }}</p>
            
            <x-table.index>
                <x-table.thead>
                    <tr>
                        <x-table.th colspan="2">Total Flight Cycles</x-table.th>  
                        <x-table.th>{{ formatNumber($pilotData['flyingCycles2Before'] ?? 0, 0) }}</x-table.th>    
                        <x-table.th>{{ formatNumber($pilotData['flyingCyclesBefore'] ?? 0, 0) }}</x-table.th>     
                        <x-table.th>{{ formatNumber($pilotData['flyingCyclesTotal'] ?? 0, 0) }}</x-table.th>      
                        <x-table.th>{{ formatNumber($pilotData['fc3Last'] ?? 0, 0) }}</x-table.th>                
                        <x-table.th>{{ formatNumber($pilotData['fc12Last'] ?? 0, 0) }}</x-table.th>               
                        <x-table.th colspan="8"></x-table.th>
                    </tr>
                    <tr>
                        <x-table.th colspan="2" rowspan="2">ATA CHAPTER</x-table.th>
                        <x-table.th rowspan="2">{{ substr(\Carbon\Carbon::parse($period)->subMonths(2)->format('F'), 0, 3) }}</x-table.th>
                        <x-table.th rowspan="2">{{ substr(\Carbon\Carbon::parse($period)->subMonth(1)->format('F'), 0, 3) }}</x-table.th>
                        <x-table.th rowspan="2">{{ substr(\Carbon\Carbon::parse($period)->format('F'), 0, 3) }}</x-table.th>
                        <x-table.th>Last 3</x-table.th>
                        <x-table.th>Last 12</x-table.th>
                        <x-table.th>{{ substr(\Carbon\Carbon::parse($period)->subMonths(2)->format('F'), 0, 3) }}</x-table.th>
                        <x-table.th>{{ substr(\Carbon\Carbon::parse($period)->subMonth(1)->format('F'), 0, 3) }}</x-table.th>
                        <x-table.th>{{ substr(\Carbon\Carbon::parse($period)->format('F'), 0, 3) }}</x-table.th>
                        <x-table.th>3 Months</x-table.th>
                        <x-table.th>12 Months</x-table.th>
                        <x-table.th>ALERT</x-table.th>
                        <x-table.th>ALERT</x-table.th>
                        <x-table.th rowspan="2">TREND</x-table.th>
                    </tr>
                    <tr>
                        <x-table.th>Months</x-table.th>
                        <x-table.th>Months</x-table.th>
                        <x-table.th>RATE</x-table.th>
                        <x-table.th>RATE</x-table.th>
                        <x-table.th>RATE</x-table.th>
                        <x-table.th>RATE</x-table.th>
                        <x-table.th>RATE</x-table.th>
                        <x-table.th>LEVEL</x-table.th>
                        <x-table.th>STATUS</x-table.th>
                    </tr>
                </x-table.thead>
                <x-table.tbody>
                    @foreach ($pilotData['reportPerAta'] as $row)
                    <tr>
                        <x-table.th>{{ $row['ata'] }}</x-table.th>
                        <x-table.th>{{ $row['ata_name'] ?? '' }}</x-table.th>
                        <x-table.td><a href="#">{{ $row['delayCountTwoMonthsAgo'] }}</a></x-table.td>
                        <x-table.td><a href="#">{{ $row['delayCountBefore'] }}</a></x-table.td>
                        <x-table.td><a href="#">{{ $row['delayCount'] }}</a></x-table.td>
                        <x-table.td>{{ $row['delay3Month'] }}</x-table.td>
                        <x-table.td>{{ $row['delay12Month'] }}</x-table.td>
                        <x-table.td>{{ formatNumber($row['delay2Rate']) }}</x-table.td>
                        <x-table.td>{{ formatNumber($row['delay1Rate']) }}</x-table.td>
                        <x-table.td>{{ formatNumber($row['delayRate']) }}</x-table.td>
                        <x-table.td>{{ formatNumber($row['delayRate3Month']) }}</x-table.td>
                        <x-table.td>{{ formatNumber($row['delayRate12Month']) }}</x-table.td>
                        <x-table.td>{{ formatNumber($row['delayAlertLevel']) }}</x-table.td>
                        <x-table.td>{{ $row['delayAlertStatus'] }}</x-table.td>
                        <x-table.td>{{ $row['delayTrend'] }}</x-table.td>
                    </tr>  
                    @endforeach
                </x-table.tbody>
            </x-table.index>
        </div>
        @endif
    </div>

    {{-- JavaScript Function --}}
    <script>
        function showLoadingAndGoBack() {
            const backBtn = document.getElementById('backBtn');
            const backIcon = document.getElementById('backIcon');
            const backText = document.getElementById('backText');
            const loadingSpinner = document.getElementById('loadingSpinner');
            const loadingSkeleton = document.getElementById('loadingSkeleton');
            
            // Show loading state on button
            backIcon.classList.add('hidden');
            loadingSpinner.classList.remove('hidden');
            backText.textContent = 'Loading...';
            backBtn.disabled = true;
            backBtn.classList.add('opacity-75', 'cursor-not-allowed');
            
            // Show skeleton overlay
            loadingSkeleton.classList.remove('hidden');
            
            // Add slight delay for better UX, then go back
            setTimeout(() => {
                history.back();
                // If history.back() doesn't work (no previous page), hide loading after 2 seconds
                setTimeout(() => {
                    // Reset button state
                    backIcon.classList.remove('hidden');
                    loadingSpinner.classList.add('hidden');
                    backText.textContent = 'Back to Report';
                    backBtn.disabled = false;
                    backBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                    loadingSkeleton.classList.add('hidden');
                }, 2000);
            }, 500);
        }

        // Loading functionality similar to pilot-result
        document.addEventListener('DOMContentLoaded', function() {
            const loadingSkeleton = document.getElementById('loadingSkeleton');
            
            // Check if this page was loaded from form submission
            const isLoadingFromForm = sessionStorage.getItem('combinedReportLoading');
            const isRefresh = sessionStorage.getItem('pageRefreshed') === 'true';
            
            function showContent() {
                if (loadingSkeleton) {
                    loadingSkeleton.classList.add('hidden');
                }
                // Clear loading flags
                sessionStorage.removeItem('combinedReportLoading');
                sessionStorage.removeItem('pageRefreshed');
            }

            // Determine skeleton duration
            let skeletonDuration = 0;
            
            if (isLoadingFromForm === 'true') {
                skeletonDuration = 2000; // 2 seconds from form
                loadingSkeleton.classList.remove('hidden');
            } else if (isRefresh) {
                skeletonDuration = 1500; // 1.5 seconds for refresh
                loadingSkeleton.classList.remove('hidden');
            } else {
                // Direct access, show content immediately
                showContent();
                return;
            }

            // Show content after determined duration
            setTimeout(showContent, skeletonDuration);
        });

        // Detect refresh
        window.addEventListener('beforeunload', function() {
            sessionStorage.setItem('pageRefreshed', 'true');
        });

        // Detect keyboard refresh (F5, Ctrl+R)
        document.addEventListener('keydown', function(e) {
            if ((e.key === 'F5') || (e.ctrlKey && e.key === 'r')) {
                sessionStorage.setItem('pageRefreshed', 'true');
            }
        });
    </script>
</x-app-layout>