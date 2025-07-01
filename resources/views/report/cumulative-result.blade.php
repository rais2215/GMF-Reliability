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

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-gray-50 to-slate-100 py-4 sm:py-8">
        <div class="max-w-full mx-auto px-2 sm:px-4 lg:px-6">

            <!-- Header Section -->
            <div class="mb-4 sm:mb-8 fade-in">
                <div class="flex flex-col space-y-4 lg:flex-row lg:justify-between lg:items-start lg:space-y-0 mb-6 sm:mb-8 gap-2 sm:gap-4">
                    <button onclick="showLoadingAndGoBack()"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-3 py-2 sm:px-4 sm:py-2.5 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-800 transition-all duration-200 hover:shadow-lg fade-in-left">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span class="truncate">Back to Report</span>
                    </button>

                    <!-- Export Buttons -->
                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3 w-full lg:w-auto fade-in-right">
                        <form action="{{ route('report.cumulative.export.pdf') }}" method="POST" class="inline w-full sm:w-auto" onsubmit="showExportLoading('PDF')">
                            @csrf
                            @if(isset($period))<input type="hidden" name="period" value="{{ $period }}">@endif
                            @if(isset($operator))<input type="hidden" name="operator" value="{{ $operator }}">@endif
                            @if(isset($aircraft_type))<input type="hidden" name="aircraft_type" value="{{ $aircraft_type }}">@endif
                            @if(isset($reg))<input type="hidden" name="reg" value="{{ $reg }}">@endif
                            <button type="submit"
                                    class="export-btn w-full sm:w-auto inline-flex items-center justify-center px-3 py-2 sm:px-4 sm:py-2.5 text-xs sm:text-sm font-medium text-white bg-red-600 rounded-lg shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 hover:shadow-md">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="truncate">Export PDF</span>
                            </button>
                        </form>
                        <form action="{{ route('report.cumulative.export.excel') }}" method="POST" class="inline w-full sm:w-auto" onsubmit="showExportLoading('Excel')">
                            @csrf
                            @if(isset($period))<input type="hidden" name="period" value="{{ $period }}">@endif
                            @if(isset($operator))<input type="hidden" name="operator" value="{{ $operator }}">@endif
                            @if(isset($aircraft_type))<input type="hidden" name="aircraft_type" value="{{ $aircraft_type }}">@endif
                            @if(isset($reg))<input type="hidden" name="reg" value="{{ $reg }}">@endif
                            <button type="submit"
                                    class="export-btn w-full sm:w-auto inline-flex items-center justify-center px-3 py-2 sm:px-4 sm:py-2.5 text-xs sm:text-sm font-medium text-white bg-lime-600 rounded-lg shadow-sm hover:bg-lime-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lime-500 transition-all duration-200 hover:shadow-md">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="truncate">Export Excel</span>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="text-center fade-in-up">
                    <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold bg-gradient-to-r from-sky-800 to-lime-600 bg-clip-text text-transparent mb-2 sm:mb-4">
                        CUMULATIVE FLIGHT HOURS AND TAKE OFF
                    </h1>
                    <p class="text-sm sm:text-base md:text-lg lg:text-xl text-gray-600 mb-3 sm:mb-6">{{ $aircraftType }} | {{ $yearRange }}</p>
                    <div class="w-24 sm:w-32 h-1 bg-gradient-to-r from-sky-800 via-lime-600 to-lime-600 mx-auto rounded-full"></div>
                </div>
            </div>

            <!-- Tables Section -->
            <div class="space-y-8">
                @if (!empty($pivotedData))
                    {{-- Flight Hours Section --}}
                    <div class="mb-8 fade-in-up">
                        <div class="bg-white rounded-xl sm:rounded-2xl shadow-xl sm:shadow-2xl overflow-hidden border border-gray-200">
                            <!-- Header -->
                            <div class="bg-gradient-to-r from-sky-800 to-lime-600 px-6 py-4">
                                <h2 class="text-xl sm:text-2xl font-bold text-white flex items-center">
                                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    FLIGHT HOURS
                                </h2>
                            </div>

                            <!-- Responsive table wrapper -->
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                        <!-- Baris pertama: Header utama dengan periode -->
                                        <tr class="border-b-2 border-sky-200">
                                            <th rowspan="2" class="px-4 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider bg-gradient-to-r from-gray-50 to-gray-100 border-r-2 border-gray-300">
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-2 text-sky-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m0 0h4M9 7h6m-6 4h6m-6 4h6"></path>
                                                    </svg>
                                                    A/C REG
                                                </span>
                                            </th>
                                            <th rowspan="2" class="px-4 py-4 text-center text-sm font-bold text-gray-700 uppercase tracking-wider bg-gradient-to-r from-gray-50 to-gray-100 border-r-2 border-gray-300">
                                                <span class="flex items-center justify-center">
                                                    <svg class="w-4 h-4 mr-2 text-sky-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3"></path>
                                                    </svg>
                                                    YEAR
                                                </span>
                                            </th>
                                            <th colspan="12" class="px-4 py-3 text-center text-sm font-bold text-white bg-sky-700 uppercase tracking-wider">
                                                {{ $yearRange }}
                                            </th>
                                        </tr>
                                        <!-- Baris kedua: Nama-nama bulan -->
                                        <tr class="border-b-2 border-gray-300">
                                            @foreach ($months as $month)
                                                <th class="px-3 py-3 text-center text-sm font-bold text-gray-700 uppercase tracking-wider {{ $loop->index % 2 == 0 ? 'bg-gray-50' : 'bg-white' }} {{ !$loop->last ? 'border-r border-gray-300' : '' }}">
                                                    {{ $month }}
                                                </th>
                                            @endforeach
                                        </tr>
                                        <!-- Baris ketiga-->
                                        <tr>
                                            <th class="px-4 py-2 bg-gradient-to-r from-gray-50 to-gray-100"></th>
                                            <th class="px-4 py-2 text-center text-sm font-semibold text-gray-700 bg-gradient-to-r from-gray-50 to-gray-100 border-l-2 border-r-2 border-t border-b border-gray-300">
                                                {{ $startYearForDisplay }}
                                            </th>
                                            @foreach ($months as $month)
                                                <th class="px-3 py-2 text-sm"></th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($pivotedData as $registration => $monthlyData)
                                            <tr class="table-row hover:bg-sky-50 transition-colors duration-200 fade-in-row {{ $loop->index % 2 == 0 ? 'bg-gray-50' : 'bg-white' }}">
                                                <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900 bg-gradient-to-r from-gray-50 to-gray-100 border-r border-gray-300">
                                                    {{ $registration }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-center font-semibold text-gray-700 bg-gradient-to-r from-gray-50 to-gray-100 border-r border-gray-300">
                                                    {{ $startYearForDisplay }}
                                                </td>
                                                @foreach ($months as $month)
                                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-semibold hover:bg-sky-100 transition-colors {{ !$loop->last ? 'border-r border-gray-200' : '' }}">
                                                        @if(isset($monthlyData['fh'][$month]) && $monthlyData['fh'][$month] !== null)
                                                            <span class="px-2 py-1 rounded-lg bg-white shadow-sm border font-medium">{{ formatTableNumber($monthlyData['fh'][$month]) }}</span>
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
                    </div>

                    {{-- Flight Cycle Section --}}
                    <div class="mb-8 fade-in-up">
                        <div class="bg-white rounded-xl sm:rounded-2xl shadow-xl sm:shadow-2xl overflow-hidden border border-gray-200">
                            <!-- Header -->
                            <div class="bg-gradient-to-r from-lime-600 to-sky-700 px-6 py-4">
                                <h2 class="text-xl sm:text-2xl font-bold text-white flex items-center">
                                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    FLIGHT CYCLE
                                </h2>
                            </div>

                            <!-- Responsive table wrapper -->
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                        <!-- Baris pertama: Header utama dengan periode -->
                                        <tr class="border-b-2 border-lime-200">
                                            <th rowspan="2" class="px-4 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider bg-gradient-to-r from-gray-50 to-gray-100 border-r-2 border-gray-300">
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-2 text-lime-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m0 0h4M9 7h6m-6 4h6m-6 4h6"></path>
                                                    </svg>
                                                    A/C REG
                                                </span>
                                            </th>
                                            <th rowspan="2" class="px-4 py-4 text-center text-sm font-bold text-gray-700 uppercase tracking-wider bg-gradient-to-r from-gray-50 to-gray-100 border-r-2 border-gray-300">
                                                <span class="flex items-center justify-center">
                                                    <svg class="w-4 h-4 mr-2 text-lime-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3"></path>
                                                    </svg>
                                                    YEAR
                                                </span>
                                            </th>
                                            <th colspan="12" class="px-4 py-3 text-center text-sm font-bold text-white bg-lime-600 uppercase tracking-wider">
                                                {{ $yearRange }}
                                            </th>
                                        </tr>
                                        <!-- Baris kedua: Nama-nama bulan -->
                                        <tr>
                                            @foreach ($months as $month)
                                                <th class="px-3 py-3 text-center text-sm font-bold text-gray-700 uppercase tracking-wider {{ $loop->index % 2 == 0 ? 'bg-gray-50' : 'bg-white' }} {{ !$loop->last ? 'border-r border-gray-300' : '' }}">
                                                    {{ $month }}
                                                </th>
                                            @endforeach
                                        </tr>
                                        <!-- Baris ketiga-->
                                        <tr>
                                            <th class="px-4 py-2 bg-gradient-to-r from-gray-50 to-gray-100"></th>
                                            <th class="px-4 py-2 text-center text-sm font-semibold text-gray-700 bg-gradient-to-r from-gray-50 to-gray-100 border-l-2 border-r-2 border-t border-b border-gray-300">
                                                {{ $startYearForDisplay }}
                                            </th>
                                            @foreach ($months as $month)
                                                <th class="px-3 py-2 text-sm"></th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($pivotedData as $registration => $monthlyData)
                                            <tr class="table-row hover:bg-lime-50 transition-colors duration-200 fade-in-row {{ $loop->index % 2 == 0 ? 'bg-gray-50' : 'bg-white' }}">
                                                <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900 bg-gradient-to-r from-gray-50 to-gray-100 border-r border-gray-300">
                                                    {{ $registration }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-center font-semibold text-gray-700 bg-gradient-to-r from-gray-50 to-gray-100 border-r border-gray-300">
                                                    {{ $startYearForDisplay }}
                                                </td>
                                                @foreach ($months as $month)
                                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-semibold hover:bg-lime-100 transition-colors {{ !$loop->last ? 'border-r border-gray-200' : '' }}">
                                                        @if(isset($monthlyData['fc'][$month]) && $monthlyData['fc'][$month] !== null)
                                                            <span class="px-2 py-1 rounded-lg bg-white shadow-sm border font-medium">{{ formatTableNumber($monthlyData['fc'][$month]) }}</span>
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
                    </div>
                @else
                    <!-- No Data State -->
                    <div class="bg-white rounded-xl sm:rounded-2xl shadow-xl sm:shadow-2xl border border-gray-200 p-12 fade-in-up">
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

            <!-- Mobile scroll indicator -->
            <div class="mt-2 text-center lg:hidden fade-in-up">
                <p class="text-xs text-gray-500 flex items-center justify-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                    </svg>
                    <span>Swipe left/right to see more data</span>
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </p>
            </div>
        </div>
    </div>

    <!-- Simple CSS Animations -->
    <style>
    /* Simple fade animations */
    .fade-in {
        opacity: 0;
        animation: fadeIn 0.6s ease-out forwards;
    }

    .fade-in-left {
        opacity: 0;
        transform: translateX(-20px);
        animation: fadeInLeft 0.5s ease-out forwards;
        animation-delay: 0.1s;
    }

    .fade-in-right {
        opacity: 0;
        transform: translateX(20px);
        animation: fadeInRight 0.5s ease-out forwards;
        animation-delay: 0.2s;
    }

    .fade-in-up {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.5s ease-out forwards;
        animation-delay: 0.3s;
    }

    .fade-in-row {
        opacity: 0;
        animation: fadeIn 0.4s ease-out forwards;
    }

    .fade-in-row:nth-child(1) { animation-delay: 0.1s; }
    .fade-in-row:nth-child(2) { animation-delay: 0.15s; }
    .fade-in-row:nth-child(3) { animation-delay: 0.2s; }
    .fade-in-row:nth-child(4) { animation-delay: 0.25s; }
    .fade-in-row:nth-child(5) { animation-delay: 0.3s; }
    .fade-in-row:nth-child(n+6) { animation-delay: 0.35s; }

    /* Keyframes */
    @keyframes fadeIn {
        to {
            opacity: 1;
        }
    }

    @keyframes fadeInLeft {
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes fadeInRight {
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Hover effects */
    .table-row:hover {
        transform: translateY(-1px);
        transition: transform 0.2s ease;
    }

    .export-btn:hover {
        transform: translateY(-1px);
        transition: transform 0.2s ease;
    }
    </style>

    <!-- JavaScript Functions -->
    <script>
    function showLoadingAndGoBack() {
        const button = event.target.closest('button');
        const originalText = button.innerHTML;

        button.disabled = true;
        button.innerHTML = `
            <div class="flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Loading...</span>
            </div>
        `;

        setTimeout(() => {
            window.history.back();
        }, 500);
    }

    function showExportLoading(type) {
        const form = event.target.closest('form');
        const button = form.querySelector('button[type="submit"]');
        const originalText = button.innerHTML;

        button.disabled = true;
        button.innerHTML = `
            <div class="flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Exporting...</span>
            </div>
        `;

        setTimeout(() => {
            button.disabled = false;
            button.innerHTML = originalText;
        }, 5000);
    }
    </script>
</x-app-layout>
