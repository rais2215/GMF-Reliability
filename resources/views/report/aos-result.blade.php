<!-- filepath: c:\Users\Noval Rais\Documents\Github Repository\GMF-Reliability\resources\views\report\aos-result.blade.php -->
<x-app-layout>
    <!-- Loading Skeleton untuk Result Page -->
    <div id="result-skeleton-loader" class="mx-auto py-4 px-4 sm:px-6 lg:px-8">
        <!-- Header Skeleton -->
        <div class="flex justify-between items-center mb-6">
            <div class="h-6 bg-gray-300 rounded animate-pulse w-2/3"></div>
            <div class="flex space-x-1">
                <div class="h-10 bg-gray-300 rounded animate-pulse w-24"></div>
                <div class="h-10 bg-gray-300 rounded animate-pulse w-28"></div>
            </div>
        </div>

        <!-- Table Skeleton -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mt-3">
            <div class="p-4">
                <!-- Table Header -->
                <div class="grid grid-cols-14 gap-2 mb-4">
                    <div class="h-12 bg-gray-300 rounded animate-pulse"></div>
                    @for($i = 0; $i < 12; $i++)
                        <div class="h-12 bg-gray-300 rounded animate-pulse"></div>
                    @endfor
                    <div class="h-12 bg-gray-300 rounded animate-pulse"></div>
                </div>

                <!-- Table Rows -->
                @for($row = 0; $row < 22; $row++)
                    <div class="grid grid-cols-14 gap-2 mb-2">
                        <div class="h-10 bg-gray-200 rounded animate-pulse"></div>
                        @for($i = 0; $i < 12; $i++)
                            <div class="h-10 bg-gray-100 rounded animate-pulse"></div>
                        @endfor
                        <div class="h-10 bg-gray-100 rounded animate-pulse"></div>
                    </div>
                @endfor
            </div>
        </div>

        <!-- Loading Animation -->
        <div class="flex justify-center mt-6">
            <div class="flex space-x-2">
                <div class="w-3 h-3 bg-blue-500 rounded-full animate-bounce"></div>
                <div class="w-3 h-3 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                <div class="w-3 h-3 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
            </div>
        </div>
    </div>

    <!-- Actual Content (Hidden Initially) -->
    <div id="result-actual-content" class="hidden mx-auto py-4 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <p class="py-2">Data Aircraft Operation Summary Type: {{ $aircraftType }} pada {{ $month }}-{{ $year }}</p>
            <div class="flex space-x-1">
                <form action="{{ route('report.aos.export.pdf') }}" method="POST">
                    @csrf
                    <input type="hidden" name="period" value="{{ $period }}">
                    <input type="hidden" name="aircraft_type" value="{{ $aircraftType }}">
                    <button type="submit" class="block rounded-md bg-gray-800 px-3 py-2 text-center text-sm text-white shadow-sm hover:bg-gray-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-600">
                        Export to PDF
                    </button>
                </form>
                <form action="{{ route('report.aos.export.excel') }}" method="POST">
                    @csrf
                    <input type="hidden" name="period" value="{{ $period }}">
                    <input type="hidden" name="aircraft_type" value="{{ $aircraftType }}">
                    <button type="submit" class="block rounded-md bg-green-500 px-3 py-2 text-center text-sm text-white shadow-sm hover:bg-green-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-600">
                        Export to Excel
                    </button>
                </form> 
            </div>
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
                    {{-- Semua table rows tetap sama seperti sebelumnya --}}
                    <tr>
                        <x-table.th class="text-left">A/C In Fleet</x-table.th>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $acInFleet = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['acInFleet'];
                                $totalAcInFleet += $acInFleet;
                            @endphp
                            <x-table.td>{{ round($acInFleet) }}</x-table.td>
                        @endfor
                        <x-table.td>{{ number_format($totalAcInFleet / 12, decimals:2) }}</x-table.td>
                    </tr>
                    {{-- ... semua rows table lainnya tetap sama ... --}}
                    <tr>
                        <x-table.th class="text-left">A/C In Service (Revenue)</x-table.th>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $acInService = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['acInService'];
                                $totalAcInService += $acInService;
                            @endphp
                            <x-table.td>{{ number_format($acInService, decimals:2) }}</x-table.td>
                        @endfor
                        <x-table.td>{{ number_format($totalAcInService / 12, 2) }}</x-table.td>
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
                            <x-table.td>{{ number_format($dailyUtilizationTakeOffTotal, 2) }}</x-table.td>
                        @endfor
                        <x-table.td>{{ number_format($totalDailyUtilizationTakeOffTotal / 12, 2) }}</x-table.td>
                    </tr>
                    <tr>
                        <x-table.th class="text-left">- Revenue FC</x-table.th>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $revenueDailyUtilizationTakeOffTotal = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['revenueDailyUtilizationTakeOffTotal'];
                                $totalRevenueDailyUtilizationTakeOffTotal += is_numeric($revenueDailyUtilizationTakeOffTotal) ? $revenueDailyUtilizationTakeOffTotal : 0;
                            @endphp
                            <x-table.td>{{ number_format($revenueDailyUtilizationTakeOffTotal, 2)}}</x-table.td>
                        @endfor
                        <x-table.td>{{ number_format($totalRevenueDailyUtilizationTakeOffTotal / 12, 2) }}</x-table.td>
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
                            <x-table.td>{{ number_format($ratePer100TakeOff, 2) }}</x-table.td>
                        @endfor
                        <x-table.td>{{ number_format($totalRatePer100TakeOff / 12, 2) }}</x-table.td>
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
                                $technicalIncidentRate = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['technicalIncidentRate'] ?? 0; // Use null coalescing operator
                                $totalTechnicalIncidentRate += is_numeric($technicalIncidentRate) ? $technicalIncidentRate : 0; // Ensure the value is numeric
                            @endphp
                            <x-table.td>{{ $technicalIncidentRate == 0 ? '0' : number_format($technicalIncidentRate, 3) }}</x-table.td>
                        @endfor
                        <x-table.td>{{ number_format($totalTechnicalIncidentRate / 12, 2) }}</x-table.td>
                    </tr>
                    <tr>
                        <x-table.th class="text-left">Technical Cancellation - Total</x-table.th>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $technicalCancellationTotal = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['technicalCancellationTotal'] ?? 0; // Use null coalescing operator
                                $totalTechnicalCancellationTotal += is_numeric($technicalCancellationTotal) ? $technicalCancellationTotal : 0; // Ensure the value is numeric
                            @endphp
                            <x-table.td>{{ round($technicalCancellationTotal) }}</x-table.td> <!-- Display rounded total for the month -->
                        @endfor
                        <x-table.td>{{ round($totalTechnicalCancellationTotal) }}</x-table.td>
                    </tr>
                    <tr>
                        <x-table.th class="text-left">Dispatch Reliability (%)</x-table.th>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $dispatchReliability = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['dispatchReliability'] ?? 0; // Use null coalescing operator
                                $totalDispatchReliability += is_numeric($dispatchReliability) ? $dispatchReliability : 0; // Ensure the value is numeric
                            @endphp
                            <x-table.td>{{ number_format($dispatchReliability, 2) }}%</x-table.td> <!-- Display formatted reliability for the month -->
                        @endfor
                        <x-table.td>{{ number_format($totalDispatchReliability / 12, 2) }}%</x-table.td>
                    </tr>
                </x-table.tbody>
            </x-table.index>
        </div>
    </div>

    <!-- JavaScript untuk result page -->
    <script>
        // Deteksi refresh sebelum DOM dimuat
        (function() {
            // Cek jika ini adalah refresh
            const isRefresh = performance.navigation.type === performance.navigation.TYPE_RELOAD ||
                             sessionStorage.getItem('pageRefreshed') === 'true';
            
            // Jika refresh, langsung tampilkan skeleton
            if (isRefresh) {
                document.documentElement.style.setProperty('--skeleton-display', 'block');
                document.documentElement.style.setProperty('--content-display', 'none');
                sessionStorage.setItem('showSkeletonOnLoad', 'true');
            }
        })();

        document.addEventListener('DOMContentLoaded', function() {
            const resultSkeletonLoader = document.getElementById('result-skeleton-loader');
            const resultActualContent = document.getElementById('result-actual-content');

            // Cek berbagai flag loading
            const isLoadingFromForm = sessionStorage.getItem('aosReportLoading');
            const isRefresh = sessionStorage.getItem('pageRefreshed') === 'true';
            const showSkeletonOnLoad = sessionStorage.getItem('showSkeletonOnLoad') === 'true';
            
            function showResultContent() {
                if (resultSkeletonLoader) {
                    resultSkeletonLoader.classList.add('hidden');
                }
                if (resultActualContent) {
                    resultActualContent.classList.remove('hidden');
                }
                // Clear all loading flags
                sessionStorage.removeItem('aosReportLoading');
                sessionStorage.removeItem('pageRefreshed');
                sessionStorage.removeItem('showSkeletonOnLoad');
            }

            // Tentukan durasi skeleton berdasarkan sumber akses
            let skeletonDuration = 0;
            
            if (isLoadingFromForm === 'true') {
                skeletonDuration = 2000; // 2 detik dari form
            } else if (isRefresh || showSkeletonOnLoad) {
                skeletonDuration = 1500; // 1.5 detik untuk refresh
                // Tampilkan skeleton immediately untuk refresh
                if (resultSkeletonLoader) {
                    resultSkeletonLoader.classList.remove('hidden');
                }
                if (resultActualContent) {
                    resultActualContent.classList.add('hidden');
                }
            } else {
                // Direct access, tampilkan content langsung
                showResultContent();
                return;
            }

            // Tampilkan content setelah durasi yang ditentukan
            setTimeout(showResultContent, skeletonDuration);
        });

        // Deteksi refresh dengan berbagai metode
        window.addEventListener('beforeunload', function() {
            sessionStorage.setItem('pageRefreshed', 'true');
        });

        // Deteksi keyboard refresh (F5, Ctrl+R)
        document.addEventListener('keydown', function(e) {
            if ((e.key === 'F5') || (e.ctrlKey && e.key === 'r')) {
                sessionStorage.setItem('pageRefreshed', 'true');
            }
        });

        // Deteksi browser navigation (back/forward button)
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                sessionStorage.setItem('pageRefreshed', 'true');
                location.reload();
            }
        });

        // Deteksi refresh via performance API
        if (performance.navigation.type === performance.navigation.TYPE_RELOAD) {
            sessionStorage.setItem('pageRefreshed', 'true');
        }
    </script>
</x-app-layout>