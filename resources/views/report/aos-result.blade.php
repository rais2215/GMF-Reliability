{{-- aos-result.blade.php Hasil Display AOS --}}

<x-app-layout>
    <div class=" mx-auto py-4 px-4 sm:px-6 lg:px-8">
        {{-- Loading Skeleton Overlay (hidden by default) --}}
        <div id="loadingSkeleton" class="hidden fixed inset-0 bg-white bg-opacity-90 z-50 items-center justify-center">
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

        @php
        function formatNumber($value, $decimals = 2) {
            return rtrim(rtrim(number_format($value, $decimals), '0'), '.');
        }
        @endphp

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
        </script>

        {{-- Header dengan tombol yang sejajar --}}
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
                <form action="{{ route('report.aos.export.pdf') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="period" value="{{ $period }}">
                    <input type="hidden" name="aircraft_type" value="{{ $aircraftType }}">
                    <button type="submit" class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-red-500 border border-transparent rounded-md shadow-sm hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                        Export to PDF
                    </button>
                </form>
                <form action="{{ route('report.aos.export.excel') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="period" value="{{ $period }}">
                    <input type="hidden" name="aircraft_type" value="{{ $aircraftType }}">
                    <button type="submit" class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-green-500 border border-transparent rounded-md shadow-sm hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                        Export to Excel
                    </button>
                </form>
            </div>
        </div>

        {{-- Title Section --}}
        <div class="mb-4">
            <p class="py-2">Data Aircraft Operation Summary Type: {{ $aircraftType }} pada {{ $month }}-{{ $year }}</p>
        </div>

        <div class="mt-3 flow-root">
            <!-- Ganti pertahun -->
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
                    @php
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
                                $acInFleet = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['acInFleet'];
                                $totalAcInFleet += $acInFleet;
                            @endphp
                            <x-table.td>{{ round($acInFleet) }}</x-table.td>
                        @endfor
                        <x-table.td>{{ formatNumber($totalAcInFleet / 12) }}</x-table.td>
                    </tr>
                    <tr>
                        <x-table.th class="text-left">A/C In Service (Revenue)</x-table.th>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $acInService = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['acInService'];
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
                                $daysInService = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['daysInService'];
                                $totalDaysInService += $daysInService;
                            @endphp
                            <x-table.td>{{ $daysInService }}</x-table.td>
                        @endfor
                        <x-table.td>{{ round($totalDaysInService) }}</x-table.td>
                    </tr>
                    <tr>
                        <x-table.th class="text-left">Flying Hours - Total</x-table.th>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $flyingHoursTotal = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['flyingHoursTotal'];
                                $totalFlyingHoursTotal += $flyingHoursTotal;
                            @endphp
                            <x-table.td>{{ round($flyingHoursTotal) }}</x-table.td>
                        @endfor
                        <x-table.td>{{ round($totalFlyingHoursTotal)}}</x-table.td>
                    </tr>
                    <tr>
                        <x-table.th class="text-left">- Revenue</x-table.th>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $revenueFlyingHours = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['revenueFlyingHours'];
                                $totalRevenueFlyingHours += $revenueFlyingHours;
                            @endphp
                            <x-table.td>{{ round($revenueFlyingHours) }}</x-table.td>
                        @endfor
                        <x-table.td>{{ round($totalRevenueFlyingHours) }}</x-table.td>
                    </tr>
                    <tr>
                        <x-table.th class="text-left">Take Off - Total</x-table.th>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $takeOffTotal = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['takeOffTotal'];
                                $totalTakeOffTotal += $takeOffTotal;
                            @endphp
                            <x-table.td>{{ $takeOffTotal ?? 0 }}</x-table.td>
                        @endfor
                        <x-table.td>{{ round($totalTakeOffTotal) }}</x-table.td>
                    </tr>
                    <tr>
                        <x-table.th class="text-left">- Revenue</x-table.th>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $revenueTakeOff = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['revenueTakeOff'];
                                $totalRevenueTakeOff += $revenueTakeOff;
                            @endphp
                            <x-table.td>{{ $revenueTakeOff }}</x-table.td>
                        @endfor
                        <x-table.td>{{ round($totalRevenueTakeOff) }}</x-table.td>
                    </tr>
                    <tr>
                        <x-table.th class="text-left">Flight Hours per Take Off - Total</x-table.th>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $flightHoursPerTakeOffTotal = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['flightHoursPerTakeOffTotal'];
                            @endphp
                            <x-table.td>{{ $flightHoursPerTakeOffTotal }}</x-table.td>
                        @endfor
                        <x-table.td>{{ $avgFlightHoursPerTakeOffTotal }}</x-table.td>
                    </tr>
                    <tr>
                        <x-table.th class="text-left">- Revenue</x-table.th>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $revenueFlightHoursPerTakeOff = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['revenueFlightHoursPerTakeOff'];
                            @endphp
                            <x-table.td>{{ $revenueFlightHoursPerTakeOff }}</x-table.td>
                        @endfor
                        <x-table.td>{{ $avgRevenueFlightHoursPerTakeOff }}</x-table.td>
                    </tr>
                    <tr>
                        <x-table.th class="text-left">Daily Utiliz - Total FH</x-table.th>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $dailyUtilizationFlyingHoursTotal = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['dailyUtilizationFlyingHoursTotal'];
                            @endphp
                            <x-table.td>{{ $dailyUtilizationFlyingHoursTotal }}</x-table.td>
                        @endfor
                        <x-table.td>{{ $avgDailyUtilizationFlyingHoursTotal }}</x-table.td>
                    </tr>
                    <tr>
                        <x-table.th class="text-left">- Revenue FH</x-table.th>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $revenueDailyUtilizationFlyingHoursTotal = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['revenueDailyUtilizationFlyingHoursTotal'];
                            @endphp
                            <x-table.td>{{ $revenueDailyUtilizationFlyingHoursTotal }}</x-table.td>
                        @endfor
                        <x-table.td>{{ $avgRevenueDailyUtilizationFlyingHoursTotal }}</x-table.td>
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
                            <x-table.td>{{ round($technicalDelayTotal) }}</x-table.td>
                        @endfor
                        <x-table.td>{{ round($totalTechnicalDelayTotal) }}</x-table.td>
                    </tr>
                    <tr>
                        <x-table.th class="text-left">- Tot Duration</x-table.th>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $totalDuration = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['totalDuration'];
                            @endphp
                            <x-table.td>{{ $totalDuration }}</x-table.td>
                        @endfor
                        <x-table.td>{{ $avgTotalDuration }}</x-table.td>
                    </tr>
                    <tr>
                        <x-table.th class="text-left">- Avg Duration</x-table.th>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $averageDuration = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['averageDuration']
                            @endphp
                            <x-table.td>{{ $averageDuration }}</x-table.td>
                        @endfor
                        <x-table.td>{{ $avgAverageDuration }}</x-table.td>
                    </tr>
                    <tr>
                        <x-table.th class="text-left">- Rate/100 Take-Off</x-table.th>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $ratePer100TakeOff = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['ratePer100TakeOff'];
                                $totalRatePer100TakeOff += is_numeric($ratePer100TakeOff) ? $ratePer100TakeOff:0;
                            @endphp
                            <x-table.td>{{ formatNumber($ratePer100TakeOff) }}</x-table.td>
                        @endfor
                        <x-table.td>{{ formatNumber($totalRatePer100TakeOff / 12) }}</x-table.td>
                    </tr>
                    <tr>
                        <x-table.th class="text-left">Technical Incident - Total</x-table.th>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $technicalIncidentTotal = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['technicalIncidentTotal'];
                                $totalTechnicalIncidentTotal += is_numeric($technicalIncidentTotal) ? $technicalIncidentTotal:0;
                            @endphp
                            <x-table.td>{{ round($technicalIncidentTotal) }}</x-table.td>
                        @endfor
                        <x-table.td>{{ round($totalTechnicalIncidentTotal / 12) }}</x-table.td>
                    </tr>
                    <tr>
                        <x-table.th class="text-left">- Rate / 100 FC</x-table.th>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $technicalIncidentRate = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['technicalIncidentRate'] ?? 0;
                                $totalTechnicalIncidentRate += is_numeric($technicalIncidentRate) ? $technicalIncidentRate : 0;
                            @endphp
                            <x-table.td>{{ formatNumber($technicalIncidentRate) }}</x-table.td>
                        @endfor
                        <x-table.td>{{ formatNumber($totalTechnicalIncidentRate / 12) }}</x-table.td>
                    </tr>
                    <tr>
                        <x-table.th class="text-left">Technical Cancellation - Total</x-table.th>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $technicalCancellationTotal = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['technicalCancellationTotal'] ?? 0;
                                $totalTechnicalCancellationTotal += is_numeric($technicalCancellationTotal) ? $technicalCancellationTotal : 0;
                            @endphp
                            <x-table.td>{{ round($technicalCancellationTotal) }}</x-table.td>
                        @endfor
                        <x-table.td>{{ round($totalTechnicalCancellationTotal) }}</x-table.td>
                    </tr>
                    <tr>
                        <x-table.th class="text-left">Dispatch Reliability (%)</x-table.th>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $dispatchReliability = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['dispatchReliability'] ?? 0;
                                $totalDispatchReliability += is_numeric($dispatchReliability) ? $dispatchReliability : 0;
                            @endphp
                            <x-table.td>{{ formatNumber($dispatchReliability) }}%</x-table.td>
                        @endfor
                        <x-table.td>{{ formatNumber($totalDispatchReliability / 12) }}%</x-table.td>
                    </tr>
                </x-table.tbody>
            </x-table.index>
        </div>
    </div>
</x-app-layout>