<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-green-50 py-4 sm:py-8">
        <div class="max-w-full mx-auto px-2 sm:px-4 lg:px-6">
            
            <!-- Header Section -->
            <div class="mb-4 sm:mb-8">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                    <button onclick="showLoadingAndGoBack()" 
                            class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Report
                    </button>
                </div>

                <div class="text-center">
                    <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold bg-gradient-to-r from-blue-600 to-green-500 bg-clip-text text-transparent mb-2 sm:mb-4">
                        Aircraft Operations Summary
                    </h1>
                    <p class="text-base sm:text-lg md:text-xl text-gray-600 mb-3 sm:mb-6">{{ $aircraftType }} | {{ \Carbon\Carbon::parse($period)->subMonth(11)->format('Y') }} - {{ \Carbon\Carbon::parse($period)->format('Y') }}</p>
                    <div class="w-24 sm:w-32 h-1 bg-gradient-to-r from-blue-500 via-green-500 to-green-500 mx-auto rounded-full animate-pulse"></div>
                </div>
            </div>

            <!-- Export Buttons -->
            <div class="flex justify-end mb-6">
                <div class="flex space-x-3">
                    <form action="{{ route('report.aos.export.pdf') }}" method="POST" class="inline" onsubmit="showExportLoading('PDF')">
                        @csrf
                        <input type="hidden" name="period" value="{{ $period }}">
                        <input type="hidden" name="aircraft_type" value="{{ $aircraftType }}">
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export PDF
                        </button>
                    </form>

                    <form action="{{ route('report.aos.export.excel') }}" method="POST" class="inline" onsubmit="showExportLoading('Excel')">
                        @csrf
                        <input type="hidden" name="period" value="{{ $period }}">
                        <input type="hidden" name="aircraft_type" value="{{ $aircraftType }}">
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

            <!-- Main Data Table -->
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-xl sm:shadow-2xl overflow-hidden border border-gray-200">
                <!-- Responsive table wrapper with horizontal scroll on mobile -->
                <div class="overflow-x-auto lg:overflow-x-visible">
                    <table class="min-w-full lg:w-full divide-y divide-gray-200">
                        <!-- Table Headers -->
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                            <tr>
                                <th class="w-48 sm:w-56 lg:w-60 px-4 sm:px-6 py-4 sm:py-6 text-left text-sm font-bold text-gray-700 uppercase tracking-wider sticky left-0 bg-gradient-to-r from-gray-50 to-gray-100 z-20 border-r-2 border-gray-300 shadow-lg">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                        Metrics
                                    </span>
                                </th>

                                @php
                                    $startYear = \Carbon\Carbon::parse($period)->subMonth(11)->format('Y');
                                    
                                    // Helper functions
                                    $safeNumber = function($value, $default = 0) {
                                        if (is_null($value) || !is_numeric($value)) {
                                            return $default;
                                        }
                                        return floatval($value);
                                    };
                                    
                                    $formatNumber = function($value, $decimals = 2) {
                                        if (is_null($value) || !is_numeric($value)) {
                                            return '0';
                                        }
                                        $numValue = floatval($value);
                                        return rtrim(rtrim(number_format($numValue, $decimals, '.', ''), '0'), '.');
                                    };

                                    // Helper function for time formatting
                                    $formatTime = function($value) {
                                        if (is_null($value) || $value === '' || $value === 0) {
                                            return '00:00';
                                        }
                                        // If it's already in H:i format, return as is
                                        if (is_string($value) && strpos($value, ':') !== false) {
                                            return $value;
                                        }
                                        // If it's a number, convert to H:i format
                                        if (is_numeric($value)) {
                                            $hours = floor($value);
                                            $minutes = round(($value - $hours) * 60);
                                            return sprintf('%d:%02d', $hours, $minutes);
                                        }
                                        return $value ?: '00:00';
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

                                    // Calculate total time 
                                    $calculateTotalTime = function($totalDecimal) use ($formatTime) {
                                        return $formatTime($totalDecimal);
                                    };
                                @endphp
                                
                                <th class="w-16 sm:w-18 lg:w-20 px-2 sm:px-3 py-4 sm:py-6 text-center text-sm font-bold text-white uppercase tracking-wider bg-gradient-to-r from-blue-500 to-blue-600">
                                    {{ $startYear }}
                                </th>
                                
                                @for ($i = 11; $i >= 0; $i--)
                                    <th class="w-14 sm:w-16 px-2 py-4 sm:py-6 text-center text-sm font-bold text-gray-700 uppercase tracking-wider {{ $i % 2 == 0 ? 'bg-gradient-to-b from-gray-50 to-gray-100' : 'bg-gradient-to-b from-white to-gray-50' }} hover:bg-gradient-to-b hover:from-blue-50 hover:to-blue-100 transition-all duration-300">
                                        {{ substr(\Carbon\Carbon::parse($period)->subMonth($i)->format('F'), 0, 3) }}
                                    </th>
                                @endfor
                                
                                <th class="w-16 sm:w-18 lg:w-20 px-2 sm:px-3 py-4 sm:py-6 text-center text-sm font-bold text-white uppercase tracking-wider bg-gradient-to-r from-blue-500 to-blue-600">
                                    <span class="hidden sm:inline">Last 12 MONTHS</span>
                                    <span class="sm:hidden">Total</span>
                                </th>
                            </tr>
                        </thead>
                        
                        <!-- Table Body -->
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php
                                // Initialize totals
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

                                // Calculate totals for 12 months
                                for ($j = 11; $j >= 0; $j--) {
                                    $monthKey = \Carbon\Carbon::parse($period)->subMonth($j)->format('Y-m');
                                    $monthData = $reportData[$monthKey] ?? [];
                                    
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

                            <!-- A/C In Fleet Row -->
                            <tr class="bg-blue-50 hover:bg-blue-100 transition-all duration-300 transform hover:scale-[1.01]">
                                <td class="w-48 sm:w-56 lg:w-60 px-4 sm:px-6 py-3 sm:py-6 whitespace-nowrap text-sm font-bold text-gray-900 sticky left-0 bg-blue-50 z-10 border-r-2 border-blue-200 shadow-lg">
                                    <span class="inline-flex items-center group">
                                        <div class="w-3 h-3 sm:w-4 sm:h-4 bg-blue-500 rounded-full mr-2 sm:mr-3 group-hover:animate-pulse"></div>
                                        <span class="text-gray-800">A/C In Fleet</span>
                                    </span>
                                </td>
                                <td class="w-16 sm:w-18 lg:w-20 px-2 sm:px-3 py-3 sm:py-6 whitespace-nowrap text-sm text-white text-center font-bold bg-blue-500">
                                    {{ $formatNumber($totalAcInFleet / 12) }}
                                </td>
                                @for ($i = 11; $i >= 0; $i--)
                                    @php
                                        $acInFleet = $safeNumber($reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['acInFleet'] ?? 0);
                                    @endphp
                                    <td class="w-14 sm:w-16 px-2 py-3 sm:py-6 whitespace-nowrap text-sm text-gray-900 text-center font-semibold {{ $i % 2 == 0 ? 'bg-gray-50' : 'bg-white' }} hover:bg-blue-50 transition-all duration-300">
                                        <span class="px-2 py-1 rounded-lg bg-white shadow-sm">{{ round($acInFleet) }}</span>
                                    </td>
                                @endfor
                                <td class="w-16 sm:w-18 lg:w-20 px-2 sm:px-3 py-3 sm:py-6 whitespace-nowrap text-sm text-white text-center font-bold bg-blue-600">
                                    {{ $formatNumber($totalAcInFleet / 12) }}
                                </td>
                            </tr>

                            <!-- All Other Metrics Rows -->
                            @foreach([
                                ['label' => 'A/C In Service (Revenue)', 'short' => 'A/C In Service', 'total' => $totalAcInService, 'key' => 'acInService', 'format' => 'number'],
                                ['label' => 'A/C Days In Service (Revenue)', 'short' => 'Days In Service', 'total' => $totalDaysInService, 'key' => 'daysInService', 'format' => 'round'],
                                ['label' => 'Flying Hours - Total', 'short' => 'Flying Hours - Total', 'total' => $totalFlyingHoursTotal, 'key' => 'flyingHoursTotal', 'format' => 'round'],
                                ['label' => '- Revenue', 'short' => '- Revenue', 'total' => $totalRevenueFlyingHours, 'key' => 'revenueFlyingHours', 'format' => 'round', 'indent' => true],
                                ['label' => 'Take Off - Total', 'short' => 'Take Off - Total', 'total' => $totalTakeOffTotal, 'key' => 'takeOffTotal', 'format' => 'round'],
                                ['label' => '- Revenue', 'short' => '- Revenue', 'total' => $totalRevenueTakeOff, 'key' => 'revenueTakeOff', 'format' => 'round', 'indent' => true],
                                ['label' => 'Flight Hours per Take Off - Total', 'short' => 'FH per TO - Total', 'total' => $totalFlightHoursPerTakeOffTotal, 'key' => 'flightHoursPerTakeOffTotal', 'format' => 'time'],
                                ['label' => '- Revenue', 'short' => '- Revenue', 'total' => $totalRevenueFlightHoursPerTakeOff, 'key' => 'revenueFlightHoursPerTakeOff', 'format' => 'time', 'indent' => true],
                                ['label' => 'Daily Utilization Flying Hours - Total', 'short' => 'Daily Util FH - Total', 'total' => $totalDailyUtilizationFlyingHoursTotal, 'key' => 'dailyUtilizationFlyingHoursTotal', 'format' => 'time'],
                                ['label' => '- Revenue', 'short' => '- Revenue', 'total' => $totalRevenueDailyUtilizationFlyingHoursTotal, 'key' => 'revenueDailyUtilizationFlyingHoursTotal', 'format' => 'time', 'indent' => true],
                                ['label' => 'Daily Utilization Take Off - Total', 'short' => 'Daily Util TO - Total', 'total' => $totalDailyUtilizationTakeOffTotal, 'key' => 'dailyUtilizationTakeOffTotal', 'format' => 'number'],
                                ['label' => '- Revenue', 'short' => '- Revenue', 'total' => $totalRevenueDailyUtilizationTakeOffTotal, 'key' => 'revenueDailyUtilizationTakeOffTotal', 'format' => 'number', 'indent' => true],
                                ['label' => 'Technical Delay - Total', 'short' => 'Tech Delay - Total', 'total' => $totalTechnicalDelayTotal, 'key' => 'technicalDelayTotal', 'format' => 'round'],
                                ['label' => '- Total Duration', 'short' => '- Total Duration', 'total' => $totalTotalDuration, 'key' => 'totalDuration', 'format' => 'time', 'indent' => true],
                                ['label' => '- Average Duration', 'short' => '- Avg Duration', 'total' => $totalAverageDuration, 'key' => 'averageDuration', 'format' => 'time', 'indent' => true],
                                ['label' => '- Rate per 100 Take Off', 'short' => '- Rate per 100 TO', 'total' => $totalRatePer100TakeOff, 'key' => 'ratePer100TakeOff', 'format' => 'number', 'indent' => true],
                                ['label' => 'Technical Incident - Total', 'short' => 'Tech Incident - Total', 'total' => $totalTechnicalIncidentTotal, 'key' => 'technicalIncidentTotal', 'format' => 'round'],
                                ['label' => '- Rate per 100 Take Off', 'short' => '- Rate per 100 TO', 'total' => $totalTechnicalIncidentRate, 'key' => 'technicalIncidentRate', 'format' => 'number', 'indent' => true],
                                ['label' => 'Technical Cancellation - Total', 'short' => 'Tech Cancel - Total', 'total' => $totalTechnicalCancellationTotal, 'key' => 'technicalCancellationTotal', 'format' => 'round'],
                            ] as $row)
                                <tr class="bg-blue-50 hover:bg-blue-100 transition-all duration-300 transform hover:scale-[1.01]">
                                    <td class="w-48 sm:w-56 lg:w-60 px-4 sm:px-6 py-3 sm:py-6 whitespace-nowrap text-sm font-bold text-gray-900 sticky left-0 bg-blue-50 z-10 border-r-2 border-blue-200 shadow-lg">
                                        <span class="inline-flex items-center group {{ isset($row['indent']) ? 'ml-4 sm:ml-6 lg:ml-8' : '' }}">
                                            @if(!isset($row['indent']))
                                                <div class="w-3 h-3 sm:w-4 sm:h-4 bg-blue-500 rounded-full mr-2 sm:mr-3 group-hover:animate-pulse"></div>
                                            @else
                                                <div class="w-2 h-2 bg-blue-400 rounded-full mr-2 sm:mr-3"></div>
                                            @endif
                                            <span class="text-gray-800">
                                                <span class="hidden lg:inline">{{ $row['label'] }}</span>
                                                <span class="lg:hidden">{{ $row['short'] ?? $row['label'] }}</span>
                                            </span>
                                        </span>
                                    </td>
                                    <td class="w-16 sm:w-18 lg:w-20 px-2 sm:px-3 py-3 sm:py-6 whitespace-nowrap text-sm text-white text-center font-bold bg-blue-500">
                                        @if($row['format'] === 'number')
                                            {{ $formatNumber($row['total'] / 12) }}
                                        @elseif($row['format'] === 'time')
                                            {{ $calculateAvgTime($row['total']) }}
                                        @else
                                            {{ round($safeNumber($row['total'])) }}
                                        @endif
                                    </td>
                                    @for ($i = 11; $i >= 0; $i--)
                                        @php
                                            $value = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')][$row['key']] ?? 0;
                                        @endphp
                                        <td class="w-14 sm:w-16 px-2 py-3 sm:py-6 whitespace-nowrap text-sm text-gray-900 text-center font-semibold {{ $i % 2 == 0 ? 'bg-gray-50' : 'bg-white' }} hover:bg-blue-50 transition-all duration-300">
                                            <span class="px-2 py-1 rounded-lg bg-white shadow-sm">
                                                @if($row['format'] === 'number')
                                                    {{ $formatNumber($safeNumber($value)) }}
                                                @elseif($row['format'] === 'time')
                                                    {{ $formatTime($value) }}
                                                @else
                                                    {{ round($safeNumber($value)) }}
                                                @endif
                                            </span>
                                        </td>
                                    @endfor
                                    <td class="w-16 sm:w-18 lg:w-20 px-2 sm:px-3 py-3 sm:py-6 whitespace-nowrap text-sm text-white text-center font-bold bg-blue-600">
                                        @if($row['format'] === 'number')
                                            {{ $formatNumber($row['total'] / 12) }}
                                        @elseif($row['format'] === 'time')
                                            {{ $calculateAvgTime($row['total']) }}
                                        @else
                                            {{ round($safeNumber($row['total'])) }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                            <!-- Dispatch Reliability Row -->
                            <tr class="bg-blue-50 hover:bg-blue-100 transition-all duration-300 transform hover:scale-[1.01]">
                                <td class="w-48 sm:w-56 lg:w-60 px-4 sm:px-6 py-3 sm:py-6 whitespace-nowrap text-sm font-bold text-gray-900 sticky left-0 bg-blue-50 z-10 border-r-2 border-blue-200 shadow-lg">
                                    <span class="inline-flex items-center group">
                                        <div class="w-3 h-3 sm:w-4 sm:h-4 bg-blue-500 rounded-full mr-2 sm:mr-3 group-hover:animate-pulse"></div>
                                        <span class="text-gray-800">
                                            <span class="hidden sm:inline">Dispatch Reliability (%)</span>
                                            <span class="sm:hidden">Dispatch Rel (%)</span>
                                        </span>
                                    </span>
                                </td>
                                <td class="w-16 sm:w-18 lg:w-20 px-2 sm:px-3 py-3 sm:py-6 whitespace-nowrap text-sm text-white text-center font-bold bg-blue-500">
                                    {{ $formatNumber($totalDispatchReliability / 12) }}%
                                </td>
                                @for ($i = 11; $i >= 0; $i--)
                                    @php
                                        $dispatchReliability = $safeNumber($reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['dispatchReliability'] ?? 0);
                                    @endphp
                                    <td class="w-14 sm:w-16 px-2 py-3 sm:py-6 whitespace-nowrap text-sm text-gray-900 text-center font-semibold {{ $i % 2 == 0 ? 'bg-gray-50' : 'bg-white' }} hover:bg-blue-50 transition-all duration-300">
                                        <span class="px-2 py-1 rounded-lg bg-white shadow-sm">
                                            {{ $formatNumber($dispatchReliability) }}%
                                        </span>
                                    </td>
                                @endfor
                                <td class="w-16 sm:w-18 lg:w-20 px-2 sm:px-3 py-3 sm:py-6 whitespace-nowrap text-sm text-white text-center font-bold bg-blue-600">
                                    {{ $formatNumber($totalDispatchReliability / 12) }}%
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
</x-app-layout>

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