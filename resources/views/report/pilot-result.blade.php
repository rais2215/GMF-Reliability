<x-app-layout>
    @php
    function formatNumber($value, $decimals = 2) {
        return rtrim(rtrim(number_format($value, $decimals), '0'), '.');
    }
    @endphp

    <div class="mx-auto py-4 px-4 sm:px-6 lg:px-8">
        {{-- Loading 3 Bar Loader Overlay (hidden by default) --}}
        <div id="loadingSkeleton" class="fixed inset-0 z-50 flex-col items-center justify-center bg-white bg-opacity-90 hidden transition-opacity duration-300">
            <div class="flex space-x-2 mb-4">
                <div class="w-3 h-12 bg-blue-600 rounded animate-loader-bar"></div>
                <div class="w-3 h-12 bg-blue-600 rounded animate-loader-bar delay-150"></div>
                <div class="w-3 h-12 bg-blue-600 rounded animate-loader-bar delay-300"></div>
            </div>
            <span id="loader-text" class="text-sm font-medium text-gray-800">Loading data...</span>
        </div>

        {{-- Loading Animation Style --}}
        <style>
            @keyframes bar-bounce {
                0%, 100% { transform: scaleY(0.5); opacity: 0.5; }
                50% { transform: scaleY(1.2); opacity: 1; }
            }

            .animate-loader-bar {
                animation: bar-bounce 1s infinite ease-in-out;
            }

            .delay-150 {
                animation-delay: 0.15s;
            }

            .delay-300 {
                animation-delay: 0.3s;
            }
        </style>

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
                <form action="{{ route('report.pilot.export.pdf') }}" method="POST" class="inline" onsubmit="showExportLoading('PDF')">
                    @csrf
                    <input type="hidden" name="period" value="{{ $period }}">
                    <input type="hidden" name="aircraft_type" value="{{ $aircraftType }}">
                    <button type="submit" class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-red-500 border border-red-500 rounded-md shadow-sm hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                        Export to PDF
                    </button>
                </form>
                <form action="#" method="POST" class="inline" onsubmit="showExportLoading('Excel')">
                    @csrf
                    <input type="hidden" name="period" value="{{ $period }}">
                    <input type="hidden" name="aircraft_type" value="{{ $aircraftType }}">
                    <button type="submit" class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-green-500 border border-green-500 rounded-md shadow-sm hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                        Export to Excel
                    </button>
                </form> 
            </div>
        </div>

        {{-- JavaScript Functions --}}
        <script>
        function showLoadingAndGoBack() {
            const backBtn = document.getElementById('backBtn');
            const backIcon = document.getElementById('backIcon');
            const backText = document.getElementById('backText');
            const loadingSpinner = document.getElementById('loadingSpinner');
            const loadingSkeleton = document.getElementById('loadingSkeleton');
            const loaderText = document.getElementById('loader-text');
            
            // Show loading state on button
            backIcon.classList.add('hidden');
            loadingSpinner.classList.remove('hidden');
            backText.textContent = 'Loading...';
            backBtn.disabled = true;
            backBtn.classList.add('opacity-75', 'cursor-not-allowed');
            
            // Show 3 bar loader overlay with updated text
            loadingSkeleton.classList.remove('hidden');
            loadingSkeleton.classList.add('flex');
            loaderText.textContent = 'Navigating back...';
            
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
                    loadingSkeleton.classList.remove('flex');
                    loaderText.textContent = 'Loading data...';
                }, 2000);
            }, 500);
        }

        // Function untuk show loading saat export
        function showExportLoading(type) {
            const loadingSkeleton = document.getElementById('loadingSkeleton');
            const loaderText = document.getElementById('loader-text');
            
            loadingSkeleton.classList.remove('hidden');
            loadingSkeleton.classList.add('flex');
            loaderText.textContent = `Exporting to ${type}...`;
            
            // Hide loading setelah 3 detik (asumsi export selesai)
            setTimeout(() => {
                loadingSkeleton.classList.add('hidden');
                loadingSkeleton.classList.remove('flex');
                loaderText.textContent = 'Loading data...';
            }, 3000);
        }
        </script>

        <div class="flex justify-between items-center">
            <p class="y-2">Pilot Report AC Type {{ $aircraftType }}, bulan {{ \Carbon\Carbon::parse($period)->format('F Y') }}</p>
        </div>

        {{-- Pilot Report --}}
        <div class=" flow-root px-4 bg-green-200">
            <h1 class="text-center mb-3 mt-3">Pilot Report</h1>
            <x-table.index>
                <x-table.thead>
                    <tr>
                        <x-table.th colspan="2">Total Flight Hours</x-table.th>
                        <x-table.th>{{ round($flyingHours2Before) }}</x-table.th>
                        <x-table.th>{{ round($flyingHoursBefore) }}</x-table.th>
                        <x-table.th>{{ round($flyingHoursTotal) }}</x-table.th>
                        <x-table.th>{{ round($fh3Last) }}</x-table.th>
                        <x-table.th>{{ round($fh12Last) }}</x-table.th>
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
                    @foreach ($reportPerAta as $row)
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

        {{-- Maintenance Report --}}
        <div class="mt-4 flow-root px-4 bg-green-200">
            <h1 class="text-center mb-3 mt-3 text-bold">Maintenance Report</h1>
            <x-table.index>
                <x-table.thead>
                    <tr>
                        <x-table.th colspan="2">Total Flight Hours</x-table.th>
                        <x-table.th>{{ round($flyingHours2Before) }}</x-table.th>
                        <x-table.th>{{ round($flyingHoursBefore) }}</x-table.th>
                        <x-table.th>{{ round($flyingHoursTotal) }}</x-table.th>
                        <x-table.th>{{ round($fh3Last) }}</x-table.th>
                        <x-table.th>{{ round($fh12Last) }}</x-table.th>
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
                    @foreach ($reportPerAta as $row)
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

        {{-- Delay Report --}}
        <div class="mt-4 flow-root px-4 bg-green-200">
            <h1 class="text-center mb-3 mt-3">Technical Delay Report</h1>
            <x-table.index>
                <x-table.thead>
                    <tr>
                        <x-table.th colspan="2">Total Flight Cycles</x-table.th>  
                        <x-table.th>{{ round($flyingCycles2Before) }}</x-table.th>    
                        <x-table.th>{{ round($flyingCyclesBefore) }}</x-table.th>     
                        <x-table.th>{{ round($flyingCyclesTotal) }}</x-table.th>      
                        <x-table.th>{{ round($fc3Last) }}</x-table.th>                
                        <x-table.th>{{ round($fc12Last) }}</x-table.th>               
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
                    @foreach ($reportPerAta as $row)
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
            const isLoadingFromForm = sessionStorage.getItem('pilotReportLoading');
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
                sessionStorage.removeItem('pilotReportLoading');
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