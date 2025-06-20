<x-app-layout>
    {{-- Pre-processing data untuk membuat struktur tabel pivot --}}
    @php
        // 1. Restrukturisasi data mentah menjadi format pivot tunggal per registrasi
        $pivotedData = [];
        $allRegs = [];
        $aircraftType = $aircraft_type ?? 'BOEING 737-800';

        if (isset($data) && is_array($data)) {
            foreach ($data as $record) {
                if (empty($record['reg']) || empty($record['month_eval'])) continue;

                $reg = $record['reg'];
                $month = strtoupper(\Carbon\Carbon::parse($record['month_eval'])->format('M'));

                if (!in_array($reg, $allRegs)) {
                    $allRegs[] = $reg;
                }

                $pivotedData[$reg]['fh'][$month] = $record['csn_by_fh'] ?? null;
                $pivotedData[$reg]['fc'][$month] = $record['csn_by_fc'] ?? null;
            }
        }

        // 2. Urutkan registrasi berdasarkan abjad
        sort($allRegs);

        $sortedPivotedData = [];
        foreach ($allRegs as $reg) {
            if (isset($pivotedData[$reg])) {
                $sortedPivotedData[$reg] = $pivotedData[$reg];
            }
        }
        $pivotedData = $sortedPivotedData;

        // 3. Siapkan variabel untuk header tabel
        $yearRange = '';
        if (isset($summary['date_range']['from']) && isset($summary['date_range']['to'])) {
            $startYear = \Carbon\Carbon::parse($summary['date_range']['from'])->year;
            $endYear = \Carbon\Carbon::parse($summary['date_range']['to'])->year;
            $yearRange = ($startYear == $endYear) ? $startYear : $startYear . ' - ' . $endYear;
        }

        // Tentukan tanggal akhir periode untuk header bulan
        $endDate = isset($period) ? \Carbon\Carbon::parse($period) : \Carbon\Carbon::now();

        // Buat daftar bulan mundur selama 12 bulan
        $months = [];
        $currentDate = $endDate->copy()->subMonths(11)->startOfMonth();
        
        // Simpan tahun awal dari periode untuk ditampilkan di kolom YEAR
        $startYearForDisplay = $currentDate->year;

        for ($i = 0; $i < 12; $i++) {
            $months[] = strtoupper($currentDate->format('M'));
            $currentDate->addMonth();
        }

        // 4. Fungsi helper untuk memformat angka dalam tabel
        function formatTableNumber($value) {
            if (!is_numeric($value)) {
                return '';
            }
            return number_format((float)$value, 0, '.', '');
        }
    @endphp

    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50">
        <!-- Loading Skeleton -->
        <div id="loadingSkeleton" class="fixed inset-0 z-50 flex-col items-center justify-center bg-white bg-opacity-95 backdrop-blur-sm hidden transition-all duration-300">
            <div class="flex space-x-3 mb-6">
                <div class="w-4 h-16 bg-gradient-to-t from-blue-600 to-blue-400 rounded-lg animate-loader-bar shadow-lg"></div>
                <div class="w-4 h-16 bg-gradient-to-t from-blue-600 to-blue-400 rounded-lg animate-loader-bar delay-150 shadow-lg"></div>
                <div class="w-4 h-16 bg-gradient-to-t from-blue-600 to-blue-400 rounded-lg animate-loader-bar delay-300 shadow-lg"></div>
            </div>
            <span id="loader-text" class="text-lg font-semibold text-gray-800 animate-pulse">Loading data...</span>
        </div>

        <style>
            @keyframes bar-bounce {
                0%, 100% { transform: scaleY(0.4); opacity: 0.5; }
                50% { transform: scaleY(1.2); opacity: 1; }
            }
            .animate-loader-bar { animation: bar-bounce 1.2s infinite ease-in-out; }
            .delay-150 { animation-delay: 0.15s; }
            .delay-300 { animation-delay: 0.3s; }
        </style>

        <script>
            function showLoadingAndGoBack() {
                const loader = document.getElementById("loadingSkeleton");
                const text = document.getElementById("loader-text");
                loader.classList.remove("hidden");
                loader.classList.add("flex");
                text.textContent = "Navigating back...";
                setTimeout(() => { history.back(); }, 800);
            }

            function showExportLoading(type) {
                const loader = document.getElementById("loadingSkeleton");
                const text = document.getElementById("loader-text");
                loader.classList.remove("hidden");
                loader.classList.add("flex");
                text.textContent = `Exporting to ${type}...`;
                setTimeout(() => {
                    loader.classList.add("hidden");
                    loader.classList.remove("flex");
                    text.textContent = "Loading data...";
                }, 4000);
            }
        </script>

        <div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                <button onclick="showLoadingAndGoBack()" 
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Report
                </button>

                <div class="flex space-x-3">
                    <form action="{{ route('report.cumulative.export.pdf') }}" method="POST" class="inline" onsubmit="showExportLoading('PDF')">
                        @csrf
                        @if(isset($period))<input type="hidden" name="period" value="{{ $period }}">@endif
                        @if(isset($operator))<input type="hidden" name="operator" value="{{ $operator }}">@endif
                        @if(isset($aircraft_type))<input type="hidden" name="aircraft_type" value="{{ $aircraft_type }}">@endif
                        @if(isset($reg))<input type="hidden" name="reg" value="{{ $reg }}">@endif
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export PDF
                        </button>
                    </form>

                    <form action="{{ route('report.cumulative.export.excel') }}" method="POST" class="inline" onsubmit="showExportLoading('Excel')">
                        @csrf
                        @if(isset($period))<input type="hidden" name="period" value="{{ $period }}">@endif
                        @if(isset($operator))<input type="hidden" name="operator" value="{{ $operator }}">@endif
                        @if(isset($aircraft_type))<input type="hidden" name="aircraft_type" value="{{ $aircraft_type }}">@endif
                        @if(isset($reg))<input type="hidden" name="reg" value="{{ $reg }}">@endif
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-white bg-green-600 rounded-lg shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export Excel
                        </button>
                    </form>
                </div>
            </div>

            <!-- Title Section -->
            <div class="text-center mb-10">
                <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-200">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2 tracking-wide">
                        CUMULATIVE FLIGHT HOURS AND TAKE OFF
                    </h1>
                    <div class="text-xl font-semibold text-blue-600 mb-2">{{ $yearRange }}</div>
                    <div class="text-lg font-medium text-gray-700 bg-gray-50 rounded-lg px-4 py-2 inline-block">
                        {{ $aircraftType }}
                    </div>
                </div>
            </div>

            <!-- Tables Section -->
            <div class="space-y-10">
                @if (!empty($pivotedData))
                    <!-- Flight Hours Table -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                            <h3 class="text-lg font-bold text-white flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                FLIGHT HOURS
                            </h3>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-200">
                                    <!-- Baris pertama: Header utama dengan periode -->
                                    <tr>
                                        <th rowspan="2" class="px-6 py-6 text-left text-sm font-black text-gray-900 uppercase tracking-wider border-r border-b border-gray-300">
                                            A/C REG
                                        </th>
                                        <th rowspan="2" class="px-6 py-6 text-center text-sm font-black text-gray-900 uppercase tracking-wider border-r border-b border-gray-300">
                                            YEAR
                                        </th>
                                        <th colspan="12" class="px-4 py-3 text-center text-sm font-black text-gray-900 uppercase tracking-wider border-b border-gray-300">
                                            {{ $yearRange }}
                                        </th>
                                    </tr>
                                    <!-- Baris kedua: Nama-nama bulan -->
                                    <tr>
                                        @foreach ($months as $month)
                                            <th class="px-4 py-3 text-center text-sm font-black text-gray-900 uppercase tracking-wider {{ !$loop->last ? 'border-r border-gray-300' : '' }}">
                                                {{ $month }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($pivotedData as $registration => $monthlyData)
                                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 border-r border-gray-200 bg-gray-50">
                                                {{ $registration }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-700 border-r border-gray-200 bg-gray-50">
                                                {{ $startYearForDisplay }}
                                            </td>
                                            @foreach ($months as $month)
                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-right text-gray-900 {{ !$loop->last ? 'border-r border-gray-200' : '' }}">
                                                    @if(isset($monthlyData['fh'][$month]) && $monthlyData['fh'][$month] !== null)
                                                        <span class="font-medium">{{ formatTableNumber($monthlyData['fh'][$month]) }}</span>
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Flight Cycle Table -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                        <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                            <h3 class="text-lg font-bold text-white flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                FLIGHT CYCLE
                            </h3>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-200">
                                    <!-- Baris pertama: Header utama dengan periode -->
                                    <tr>
                                        <th rowspan="2" class="px-6 py-6 text-left text-sm font-black text-gray-900 uppercase tracking-wider border-r border-b border-gray-300">
                                            A/C REG
                                        </th>
                                        <th rowspan="2" class="px-6 py-6 text-center text-sm font-black text-gray-900 uppercase tracking-wider border-r border-b border-gray-300">
                                            YEAR
                                        </th>
                                        <th colspan="12" class="px-4 py-3 text-center text-sm font-black text-gray-900 uppercase tracking-wider border-b border-gray-300">
                                            {{ $yearRange }}
                                        </th>
                                    </tr>
                                    <!-- Baris kedua: Nama-nama bulan -->
                                    <tr>
                                        @foreach ($months as $month)
                                            <th class="px-4 py-3 text-center text-sm font-black text-gray-900 uppercase tracking-wider {{ !$loop->last ? 'border-r border-gray-300' : '' }}">
                                                {{ $month }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($pivotedData as $registration => $monthlyData)
                                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 border-r border-gray-200 bg-gray-50">
                                                {{ $registration }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-700 border-r border-gray-200 bg-gray-50">
                                                {{ $startYearForDisplay }}
                                            </td>
                                            @foreach ($months as $month)
                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-right text-gray-900 {{ !$loop->last ? 'border-r border-gray-200' : '' }}">
                                                    @if(isset($monthlyData['fc'][$month]) && $monthlyData['fc'][$month] !== null)
                                                        <span class="font-medium">{{ formatTableNumber($monthlyData['fc'][$month]) }}</span>
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <!-- No Data State -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-12">
                        <div class="text-center">
                            <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Data Found</h3>
                            <p class="text-gray-500 mb-4">No data found for the selected criteria.</p>
                            <p class="text-sm text-gray-400">Try adjusting your filter parameters.</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="mt-12 text-center">
                <div class="bg-white rounded-lg shadow p-4 inline-block border border-gray-200">
                    <p class="text-sm text-gray-600 flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Report generated on {{ date('d F Y, H:i:s') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>