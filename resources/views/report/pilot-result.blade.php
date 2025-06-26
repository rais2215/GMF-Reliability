<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-gray-50 to-slate-100 py-4 sm:py-8">
        <div class="max-w-full mx-auto px-2 sm:px-4 lg:px-6">

            {{-- Loading 3 Bar Loader Overlay (hidden by default) --}}
            <div id="loadingSkeleton" class="fixed inset-0 z-50 flex-col items-center justify-center bg-white bg-opacity-90 hidden transition-opacity duration-300">
                <div class="flex space-x-2 mb-4">
                    <div class="w-3 h-12 bg-sky-800 rounded animate-loader-bar"></div>
                    <div class="w-3 h-12 bg-sky-800 rounded animate-loader-bar delay-150"></div>
                    <div class="w-3 h-12 bg-sky-800 rounded animate-loader-bar delay-300"></div>
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

            <!-- Header Section -->
            <div class="mb-4 sm:mb-8">
                <div class="flex flex-col space-y-4 lg:flex-row lg:justify-between lg:items-start lg:space-y-0 mb-6 sm:mb-8 gap-2 sm:gap-4">
                    <button onclick="showLoadingAndGoBack()" 
                            class="w-full sm:w-auto inline-flex items-center justify-center px-3 py-2 sm:px-4 sm:py-2.5 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-800 transition-all duration-200">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span class="truncate">Back to Report</span>
                    </button>

                    <!-- Export Buttons -->
                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3 w-full lg:w-auto">
                        <form action="{{ route('report.pilot.export.pdf') }}" method="POST" class="inline w-full sm:w-auto" onsubmit="showExportLoading('PDF')">
                            @csrf
                            <input type="hidden" name="period" value="{{ $period }}">
                            <input type="hidden" name="aircraft_type" value="{{ $aircraftType }}">
                            <button type="submit" 
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-3 py-2 sm:px-4 sm:py-2.5 text-xs sm:text-sm font-medium text-white bg-red-600 rounded-lg shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="truncate">Export PDF</span>
                            </button>
                        </form>
                        <form action="{{ route('report.pilot.export.excel') }}" method="POST" class="inline w-full sm:w-auto" onsubmit="showExportLoading('Excel')">
                            @csrf
                            <input type="hidden" name="period" value="{{ $period }}">
                            <input type="hidden" name="aircraft_type" value="{{ $aircraftType }}">
                            <button type="submit" 
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-3 py-2 sm:px-4 sm:py-2.5 text-xs sm:text-sm font-medium text-white bg-lime-600 rounded-lg shadow-sm hover:bg-lime-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lime-500 transition-all duration-200">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="truncate">Export Excel</span>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="text-center">
                    <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold bg-gradient-to-r from-sky-800 to-lime-600 bg-clip-text text-transparent mb-2 sm:mb-4">
                        Pilot & Maintenance Report
                    </h1>
                    <p class="text-sm sm:text-base md:text-lg lg:text-xl text-gray-600 mb-3 sm:mb-6">{{ $aircraftType }} | {{ \Carbon\Carbon::parse($period)->subMonth(11)->format('Y') }} - {{ \Carbon\Carbon::parse($period)->format('Y') }}</p>
                    <div class="w-24 sm:w-32 h-1 bg-gradient-to-r from-sky-800 via-lime-600 to-lime-600 mx-auto rounded-full animate-pulse"></div>
                </div>
            </div>

            @php
            function formatNumber($value, $decimals = 2) {
                return rtrim(rtrim(number_format($value, $decimals), '0'), '.');
            }
            @endphp

            {{-- Pilot Report Section --}}
            <div class="mb-8">
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-xl sm:shadow-2xl overflow-hidden border border-gray-200">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-sky-800 to-lime-600 px-6 py-4">
                        <h2 class="text-xl sm:text-2xl font-bold text-white flex items-center">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Pilot Report
                        </h2>
                    </div>
                    
                    <!-- Responsive table wrapper -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <tr class="border-b-2 border-sky-200">
                                    <th colspan="2" class="px-4 py-3 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">Total Flight Hours</th>
                                    <th class="px-3 py-3 text-center text-sm font-bold text-white bg-sky-700">{{ round($flyingHours2Before) }}</th>
                                    <th class="px-3 py-3 text-center text-sm font-bold text-white bg-sky-700">{{ round($flyingHoursBefore) }}</th>
                                    <th class="px-3 py-3 text-center text-sm font-bold text-white bg-sky-700">{{ round($flyingHoursTotal) }}</th>
                                    <th class="px-3 py-3 text-center text-sm font-bold text-white bg-sky-800">{{ round($fh3Last) }}</th>
                                    <th class="px-3 py-3 text-center text-sm font-bold text-white bg-sky-800">{{ round($fh12Last) }}</th>
                                    <th colspan="8" class="px-3 py-3 bg-gray-50"></th>
                                </tr>
                                <tr>
                                    <th colspan="2" rowspan="2" class="px-4 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider bg-gradient-to-r from-gray-50 to-gray-100 border-r-2 border-gray-300">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-sky-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                            ATA CHAPTER
                                        </span>
                                    </th>
                                    <th rowspan="2" class="px-3 py-4 text-center text-sm font-bold text-gray-700 uppercase tracking-wider bg-white">{{ substr(\Carbon\Carbon::parse($period)->subMonths(2)->format('F'), 0, 3) }}</th>
                                    <th rowspan="2" class="px-3 py-4 text-center text-sm font-bold text-gray-700 uppercase tracking-wider bg-gray-50">{{ substr(\Carbon\Carbon::parse($period)->subMonth(1)->format('F'), 0, 3) }}</th>
                                    <th rowspan="2" class="px-3 py-4 text-center text-sm font-bold text-gray-700 uppercase tracking-wider bg-white">{{ substr(\Carbon\Carbon::parse($period)->format('F'), 0, 3) }}</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-white bg-sky-700">Last 3</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-white bg-sky-800">Last 12</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-gray-700 bg-gray-50">{{ substr(\Carbon\Carbon::parse($period)->subMonths(2)->format('F'), 0, 3) }}</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-gray-700 bg-white">{{ substr(\Carbon\Carbon::parse($period)->subMonth(1)->format('F'), 0, 3) }}</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-gray-700 bg-gray-50">{{ substr(\Carbon\Carbon::parse($period)->format('F'), 0, 3) }}</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-white bg-sky-700">3 Months</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-white bg-sky-800">12 Months</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-white bg-red-500">ALERT</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-white bg-red-600">ALERT</th>
                                    <th rowspan="2" class="px-3 py-4 text-center text-sm font-bold text-white bg-lime-600">TREND</th>
                                </tr>
                                <tr>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-white bg-sky-700">Months</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-white bg-sky-800">Months</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-gray-700 bg-gray-50">RATE</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-gray-700 bg-white">RATE</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-gray-700 bg-gray-50">RATE</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-white bg-sky-700">RATE</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-white bg-sky-800">RATE</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-white bg-red-500">LEVEL</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-white bg-red-600">STATUS</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($reportPerAta as $index => $row)
                                <tr class="hover:bg-sky-50 transition-all duration-300 transform hover:scale-[1.01] {{ $index % 2 == 0 ? 'bg-gray-50' : 'bg-white' }}">
                                    <th class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900 bg-gradient-to-r from-gray-50 to-gray-100 border-r border-gray-300">{{ $row['ata'] }}</th>
                                    <th class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900 bg-gradient-to-r from-gray-50 to-gray-100 border-r border-gray-300 max-w-xs truncate">{{ $row['ata_name'] ?? '' }}</th>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-semibold hover:bg-sky-100 transition-colors">
                                        <span class="px-2 py-1 rounded-lg bg-white shadow-sm border">{{ $row['pirepCountTwoMonthsAgo'] }}</span>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-semibold hover:bg-sky-100 transition-colors">
                                        <span class="px-2 py-1 rounded-lg bg-white shadow-sm border">{{ $row['pirepCountBefore'] }}</span>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-semibold hover:bg-sky-100 transition-colors">
                                        <span class="px-2 py-1 rounded-lg bg-white shadow-sm border">{{ $row['pirepCount'] }}</span>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-semibold text-white bg-sky-700">{{ $row['pirep3Month'] }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-semibold text-white bg-sky-800">{{ $row['pirep12Month'] }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-semibold hover:bg-sky-100 transition-colors">
                                        <span class="px-2 py-1 rounded-lg bg-white shadow-sm border">{{ formatNumber($row['pirep2Rate']) }}</span>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-semibold hover:bg-sky-100 transition-colors">
                                        <span class="px-2 py-1 rounded-lg bg-white shadow-sm border">{{ formatNumber($row['pirep1Rate']) }}</span>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-semibold hover:bg-sky-100 transition-colors">
                                        <span class="px-2 py-1 rounded-lg bg-white shadow-sm border">{{ formatNumber($row['pirepRate']) }}</span>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-semibold text-white bg-sky-700">{{ formatNumber($row['pirepRate3Month']) }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-semibold text-white bg-sky-800">{{ formatNumber($row['pirepRate12Month']) }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-semibold text-white bg-red-500">{{ formatNumber($row['pirepAlertLevel']) }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-bold text-white bg-red-600">{{ $row['pirepAlertStatus'] }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-bold text-white bg-lime-600">{{ $row['pirepTrend'] }}</td>
                                </tr>  
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Maintenance Report Section --}}
            <div class="mb-8">
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-xl sm:shadow-2xl overflow-hidden border border-gray-200">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-lime-600 to-sky-700 px-6 py-4">
                        <h2 class="text-xl sm:text-2xl font-bold text-white flex items-center">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Maintenance Finding Report
                        </h2>
                    </div>
                    
                    <!-- Responsive table wrapper -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <tr class="border-b-2 border-lime-200">
                                    <th colspan="2" class="px-4 py-3 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">Total Flight Hours</th>
                                    <th class="px-3 py-3 text-center text-sm font-bold text-white bg-lime-600">{{ round($flyingHours2Before) }}</th>
                                    <th class="px-3 py-3 text-center text-sm font-bold text-white bg-lime-600">{{ round($flyingHoursBefore) }}</th>
                                    <th class="px-3 py-3 text-center text-sm font-bold text-white bg-lime-600">{{ round($flyingHoursTotal) }}</th>
                                    <th class="px-3 py-3 text-center text-sm font-bold text-white bg-lime-700">{{ round($fh3Last) }}</th>
                                    <th class="px-3 py-3 text-center text-sm font-bold text-white bg-lime-700">{{ round($fh12Last) }}</th>
                                    <th colspan="8" class="px-3 py-3 bg-gray-50"></th>
                                </tr>
                                <tr>
                                    <th colspan="2" rowspan="2" class="px-4 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider bg-gradient-to-r from-gray-50 to-gray-100 border-r-2 border-gray-300">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-lime-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                            ATA CHAPTER
                                        </span>
                                    </th>
                                    <th rowspan="2" class="px-3 py-4 text-center text-sm font-bold text-gray-700 uppercase tracking-wider bg-white">{{ substr(\Carbon\Carbon::parse($period)->subMonths(2)->format('F'), 0, 3) }}</th>
                                    <th rowspan="2" class="px-3 py-4 text-center text-sm font-bold text-gray-700 uppercase tracking-wider bg-gray-50">{{ substr(\Carbon\Carbon::parse($period)->subMonth(1)->format('F'), 0, 3) }}</th>
                                    <th rowspan="2" class="px-3 py-4 text-center text-sm font-bold text-gray-700 uppercase tracking-wider bg-white">{{ substr(\Carbon\Carbon::parse($period)->format('F'), 0, 3) }}</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-white bg-lime-600">Last 3</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-white bg-lime-700">Last 12</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-gray-700 bg-gray-50">{{ substr(\Carbon\Carbon::parse($period)->subMonths(2)->format('F'), 0, 3) }}</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-gray-700 bg-white">{{ substr(\Carbon\Carbon::parse($period)->subMonth(1)->format('F'), 0, 3) }}</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-gray-700 bg-gray-50">{{ substr(\Carbon\Carbon::parse($period)->format('F'), 0, 3) }}</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-white bg-lime-600">3 Months</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-white bg-lime-700">12 Months</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-white bg-red-500">ALERT</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-white bg-red-600">ALERT</th>
                                    <th rowspan="2" class="px-3 py-4 text-center text-sm font-bold text-white bg-lime-600">TREND</th>
                                </tr>
                                <tr>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-white bg-lime-600">Months</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-white bg-lime-700">Months</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-gray-700 bg-gray-50">RATE</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-gray-700 bg-white">RATE</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-gray-700 bg-gray-50">RATE</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-white bg-lime-600">RATE</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-white bg-lime-700">RATE</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-white bg-red-500">LEVEL</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-white bg-red-600">STATUS</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($reportPerAta as $index => $row)
                                <tr class="hover:bg-lime-50 transition-all duration-300 transform hover:scale-[1.01] {{ $index % 2 == 0 ? 'bg-gray-50' : 'bg-white' }}">
                                    <th class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900 bg-gradient-to-r from-gray-50 to-gray-100 border-r border-gray-300">{{ $row['ata'] }}</th>
                                    <th class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900 bg-gradient-to-r from-gray-50 to-gray-100 border-r border-gray-300 max-w-xs truncate">{{ $row['ata_name'] ?? '' }}</th>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-semibold hover:bg-lime-100 transition-colors">
                                        <span class="px-2 py-1 rounded-lg bg-white shadow-sm border">{{ $row['marepCountTwoMonthsAgo'] }}</span>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-semibold hover:bg-lime-100 transition-colors">
                                        <span class="px-2 py-1 rounded-lg bg-white shadow-sm border">{{ $row['marepCountBefore'] }}</span>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-semibold hover:bg-lime-100 transition-colors">
                                        <span class="px-2 py-1 rounded-lg bg-white shadow-sm border">{{ $row['marepCount'] }}</span>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-semibold text-white bg-lime-600">{{ $row['marep3Month'] }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-semibold text-white bg-lime-700">{{ $row['marep12Month'] }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-semibold hover:bg-lime-100 transition-colors">
                                        <span class="px-2 py-1 rounded-lg bg-white shadow-sm border">{{ formatNumber($row['marep2Rate']) }}</span>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-semibold hover:bg-lime-100 transition-colors">
                                        <span class="px-2 py-1 rounded-lg bg-white shadow-sm border">{{ formatNumber($row['marep1Rate']) }}</span>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-semibold hover:bg-lime-100 transition-colors">
                                        <span class="px-2 py-1 rounded-lg bg-white shadow-sm border">{{ formatNumber($row['marepRate']) }}</span>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-semibold text-white bg-lime-600">{{ formatNumber($row['marepRate3Month']) }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-semibold text-white bg-lime-700">{{ formatNumber($row['marepRate12Month']) }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-semibold text-white bg-red-500">{{ formatNumber($row['marepAlertLevel']) }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-bold text-white bg-red-600">{{ $row['marepAlertStatus'] }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-bold text-white bg-lime-600">{{ $row['marepTrend'] }}</td>
                                </tr>  
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Technical Delay Report Section --}}
            <div class="mb-8">
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-xl sm:shadow-2xl overflow-hidden border border-gray-200">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-sky-800 to-sky-700 px-6 py-4">
                        <h2 class="text-xl sm:text-2xl font-bold text-white flex items-center">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Technical Delay > 15 Minutes and Cancellation
                        </h2>
                    </div>
                    
                    <!-- Responsive table wrapper -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <tr class="border-b-2 border-sky-200">
                                    <th colspan="2" class="px-4 py-3 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">Total Flight Cycles</th>
                                    <th class="px-3 py-3 text-center text-sm font-bold text-white bg-sky-700">{{ round($flyingCycles2Before) }}</th>
                                    <th class="px-3 py-3 text-center text-sm font-bold text-white bg-sky-700">{{ round($flyingCyclesBefore) }}</th>
                                    <th class="px-3 py-3 text-center text-sm font-bold text-white bg-sky-700">{{ round($flyingCyclesTotal) }}</th>
                                    <th class="px-3 py-3 text-center text-sm font-bold text-white bg-sky-800">{{ round($fc3Last) }}</th>
                                    <th class="px-3 py-3 text-center text-sm font-bold text-white bg-sky-800">{{ round($fc12Last) }}</th>
                                    <th colspan="8" class="px-3 py-3 bg-gray-50"></th>
                                </tr>
                                <tr>
                                    <th colspan="2" rowspan="2" class="px-4 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider bg-gradient-to-r from-gray-50 to-gray-100 border-r-2 border-gray-300">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-sky-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                            ATA CHAPTER
                                        </span>
                                    </th>
                                    <th rowspan="2" class="px-3 py-4 text-center text-sm font-bold text-gray-700 uppercase tracking-wider bg-white">{{ substr(\Carbon\Carbon::parse($period)->subMonths(2)->format('F'), 0, 3) }}</th>
                                    <th rowspan="2" class="px-3 py-4 text-center text-sm font-bold text-gray-700 uppercase tracking-wider bg-gray-50">{{ substr(\Carbon\Carbon::parse($period)->subMonth(1)->format('F'), 0, 3) }}</th>
                                    <th rowspan="2" class="px-3 py-4 text-center text-sm font-bold text-gray-700 uppercase tracking-wider bg-white">{{ substr(\Carbon\Carbon::parse($period)->format('F'), 0, 3) }}</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-white bg-sky-700">Last 3</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-white bg-sky-800">Last 12</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-gray-700 bg-gray-50">{{ substr(\Carbon\Carbon::parse($period)->subMonths(2)->format('F'), 0, 3) }}</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-gray-700 bg-white">{{ substr(\Carbon\Carbon::parse($period)->subMonth(1)->format('F'), 0, 3) }}</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-gray-700 bg-gray-50">{{ substr(\Carbon\Carbon::parse($period)->format('F'), 0, 3) }}</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-white bg-sky-700">3 Months</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-white bg-sky-800">12 Months</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-white bg-orange-500">ALERT</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-white bg-orange-600">ALERT</th>
                                    <th rowspan="2" class="px-3 py-4 text-center text-sm font-bold text-white bg-sky-700">TREND</th>
                                </tr>
                                <tr>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-white bg-sky-700">Months</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-white bg-sky-800">Months</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-gray-700 bg-gray-50">RATE</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-gray-700 bg-white">RATE</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-gray-700 bg-gray-50">RATE</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-white bg-sky-700">RATE</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-white bg-sky-800">RATE</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-white bg-orange-500">LEVEL</th>
                                    <th class="px-3 py-2 text-center text-sm font-bold text-white bg-orange-600">STATUS</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($reportPerAta as $index => $row)
                                <tr class="hover:bg-sky-50 transition-all duration-300 transform hover:scale-[1.01] {{ $index % 2 == 0 ? 'bg-gray-50' : 'bg-white' }}">
                                    <th class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900 bg-gradient-to-r from-gray-50 to-gray-100 border-r border-gray-300">{{ $row['ata'] }}</th>
                                    <th class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900 bg-gradient-to-r from-gray-50 to-gray-100 border-r border-gray-300 max-w-xs truncate">{{ $row['ata_name'] ?? '' }}</th>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-semibold hover:bg-sky-100 transition-colors">
                                        <span class="px-2 py-1 rounded-lg bg-white shadow-sm border">{{ $row['delayCountTwoMonthsAgo'] ?? 0 }}</span>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-semibold hover:bg-sky-100 transition-colors">
                                        <span class="px-2 py-1 rounded-lg bg-white shadow-sm border">{{ $row['delayCountBefore'] ?? 0 }}</span>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-semibold hover:bg-sky-100 transition-colors">
                                        <span class="px-2 py-1 rounded-lg bg-white shadow-sm border">{{ $row['delayCount'] ?? 0 }}</span>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-semibold text-white bg-sky-700">{{ $row['delay3Month'] ?? 0 }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-semibold text-white bg-sky-800">{{ $row['delay12Month'] ?? 0 }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-semibold hover:bg-sky-100 transition-colors">
                                        <span class="px-2 py-1 rounded-lg bg-white shadow-sm border">{{ formatNumber($row['delay2Rate'] ?? 0) }}</span>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-semibold hover:bg-sky-100 transition-colors">
                                        <span class="px-2 py-1 rounded-lg bg-white shadow-sm border">{{ formatNumber($row['delay1Rate'] ?? 0) }}</span>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-semibold hover:bg-sky-100 transition-colors">
                                        <span class="px-2 py-1 rounded-lg bg-white shadow-sm border">{{ formatNumber($row['delayRate'] ?? 0) }}</span>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-semibold text-white bg-sky-700">{{ formatNumber($row['delayRate3Month'] ?? 0) }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-semibold text-white bg-sky-800">{{ formatNumber($row['delayRate12Month'] ?? 0) }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-semibold text-white bg-orange-500">{{ formatNumber($row['delayAlertLevel'] ?? 0) }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-bold text-white bg-orange-600">{{ $row['delayAlertStatus'] ?? 'N/A' }}</td>
                                    <td class="px-3 py-3 whitespace-nowrap text-sm text-center font-bold text-white bg-sky-700">{{ $row['delayTrend'] ?? 'N/A' }}</td>
                                </tr>  
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Mobile scroll indicator -->
            <div class="mt-2 text-center lg:hidden">
                <p class="text-xs text-gray-500 flex items-center justify-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                    </svg>
                    Swipe left/right to see more data
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </p>
            </div>
        </div>
    </div>

    <!-- JavaScript Functions -->
    <script>
    function showLoadingAndGoBack() {
        // Show loading state
        const button = event.target;
        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = `
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Loading...
        `;
        
        // Navigate back to the previous page
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
            <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Exporting ${type}...
        `;
        
        // Reset button after a delay (in case of errors)
        setTimeout(() => {
            button.disabled = false;
            button.innerHTML = originalText;
        }, 10000);
    }
    </script>
</x-app-layout>