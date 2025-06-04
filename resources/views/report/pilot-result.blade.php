<!-- filepath: c:\Users\Noval Rais\Documents\Github Repository\GMF-Reliability\resources\views\report\pilot-result.blade.php -->
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

        <!-- Pilot Report Skeleton -->
        <div class="flow-root px-4 bg-gray-200 rounded-lg mb-4">
            <div class="h-8 bg-gray-300 rounded animate-pulse w-32 mx-auto my-4"></div>
            <div class="space-y-3">
                <div class="h-12 bg-gray-300 rounded animate-pulse"></div>
                <div class="h-12 bg-gray-300 rounded animate-pulse"></div>
                <div class="h-12 bg-gray-300 rounded animate-pulse"></div>
                @for($i = 0; $i < 15; $i++)
                    <div class="h-10 bg-gray-300 rounded animate-pulse"></div>
                @endfor
            </div>
        </div>

        <!-- Maintenance Report Skeleton -->
        <div class="flow-root px-4 bg-gray-200 rounded-lg mb-4">
            <div class="h-8 bg-gray-300 rounded animate-pulse w-40 mx-auto my-4"></div>
            <div class="space-y-3">
                <div class="h-12 bg-gray-300 rounded animate-pulse"></div>
                <div class="h-12 bg-gray-300 rounded animate-pulse"></div>
                <div class="h-12 bg-gray-300 rounded animate-pulse"></div>
                @for($i = 0; $i < 15; $i++)
                    <div class="h-10 bg-gray-300 rounded animate-pulse"></div>
                @endfor
            </div>
        </div>

        <!-- Delay Report Skeleton -->
        <div class="flow-root px-4 bg-gray-200 rounded-lg">
            <div class="h-8 bg-gray-300 rounded animate-pulse w-48 mx-auto my-4"></div>
            <div class="space-y-3">
                <div class="h-12 bg-gray-300 rounded animate-pulse"></div>
                <div class="h-12 bg-gray-300 rounded animate-pulse"></div>
                <div class="h-12 bg-gray-300 rounded animate-pulse"></div>
                @for($i = 0; $i < 15; $i++)
                    <div class="h-10 bg-gray-300 rounded animate-pulse"></div>
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
            <p class="y-2">Pilot Report AC Type {{ $aircraftType }}, bulan {{ \Carbon\Carbon::parse($period)->format('F Y') }}</p>
            <div class="flex space-x-1">
                <form action="{{ route('report.pilot.export.pdf') }}" method="POST">
                    @csrf
                    <input type="hidden" name="period" value="{{ $period }}">
                    <input type="hidden" name="aircraft_type" value="{{ $aircraftType }}">
                    <button type="submit" class="block rounded-md bg-gray-800 px-3 py-2 text-center text-sm text-white shadow-sm hover:bg-gray-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-600">
                        Export to PDF
                    </button>
                </form>
                <form action="#" method="POST">
                    @csrf
                    <input type="hidden" name="period" value="{{ $period }}">
                    <input type="hidden" name="aircraft_type" value="{{ $aircraftType }}">
                    <button type="submit" class="block rounded-md bg-green-500 px-3 py-2 text-center text-sm text-white shadow-sm hover:bg-green-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-600">
                        Export to Excel
                    </button>
                </form> 
            </div>
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
                        <x-table.td>{{ number_format($row['pirep2Rate'],2) }}</x-table.td>
                        <x-table.td>{{ number_format($row['pirep1Rate'],2) }}</x-table.td>
                        <x-table.td>{{ number_format($row['pirepRate'],2) }}</x-table.td>
                        <x-table.td>{{ number_format($row['pirepRate3Month'],2) }}</x-table.td>
                        <x-table.td>{{ number_format($row['pirepRate12Month'],2) }}</x-table.td>
                        <x-table.td>{{ number_format($row['pirepAlertLevel'],2) }}</x-table.td>
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
                        <x-table.td>{{ number_format($row['marep2Rate'], 2) }}</x-table.td>
                        <x-table.td>{{ number_format($row['marep1Rate'], 2) }}</x-table.td>
                        <x-table.td>{{ number_format($row['marepRate'], 2) }}</x-table.td>
                        <x-table.td>{{ number_format($row['marepRate3Month'], 2) }}</x-table.td>
                        <x-table.td>{{ number_format($row['marepRate12Month'], 2) }}</x-table.td>
                        <x-table.td>{{ number_format($row['marepAlertLevel'], 2) }}</x-table.td>
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
                        <x-table.td>{{ number_format($row['delay2Rate'], 2) }}</x-table.td>
                        <x-table.td>{{ number_format($row['delay1Rate'], 2) }}</x-table.td>
                        <x-table.td>{{ number_format($row['delayRate'], 2) }}</x-table.td>
                        <x-table.td>{{ number_format($row['delayRate3Month'], 2) }}</x-table.td>
                        <x-table.td>{{ number_format($row['delayRate12Month'], 2) }}</x-table.td>
                        <x-table.td>{{ number_format($row['delayAlertLevel'], 2) }}</x-table.td>
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