{{-- filepath: c:\Users\Noval Rais\Documents\Github Repository\GMF-Reliability\resources\views\report\aos-result.blade.php --}}
<x-app-layout>
    <div class=" mx-auto py-4 px-4 sm:px-6 lg:px-8">
        <div class="mt-3 flow-root">
            <x-table.index>
                <x-table.thead>
                    <tr>
                        <x-table.th>Metrics</x-table.th>
                        @php
                            $startYear = \Carbon\Carbon::parse($period)->subMonth(11)->format('Y');
                            
                            // Define formatNumber function for this view
                            $formatNumber = function($value, $decimals = 2) {
                                if (!is_numeric($value)) {
                                    return '0';
                                }
                                return rtrim(rtrim(number_format($value, $decimals, '.', ''), '0'), '.');
                            };
                        @endphp
                        
                        <x-table.th>{{ $startYear }}</x-table.th>
                        
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

                        // Hitung total untuk 12 bulan (untuk kolom "Last 12 MTHS")
                        // ✅ UPDATED: Perhitungan A/C In Fleet sudah mengexclude data dengan remark "out"
                        for ($j = 11; $j >= 0; $j--) {
                            $monthKey = \Carbon\Carbon::parse($period)->subMonth($j)->format('Y-m');
                            $monthData = $reportData[$monthKey] ?? [];
                            
                            // A/C In Fleet sudah di-filter di controller untuk exclude remark "out"
                            $totalAcInFleet += $monthData['acInFleet'] ?? 0;
                            $totalAcInService += $monthData['acInService'] ?? 0;
                            $totalDaysInService += $monthData['daysInService'] ?? 0;
                            $totalFlyingHoursTotal += $monthData['flyingHoursTotal'] ?? 0;
                            $totalRevenueFlyingHours += $monthData['revenueFlyingHours'] ?? 0;
                            $totalTakeOffTotal += $monthData['takeOffTotal'] ?? 0;
                            $totalRevenueTakeOff += $monthData['revenueTakeOff'] ?? 0;
                            $totalDailyUtilizationTakeOffTotal += is_numeric($monthData['dailyUtilizationTakeOffTotal'] ?? 0) ? $monthData['dailyUtilizationTakeOffTotal'] : 0;
                            $totalRevenueDailyUtilizationTakeOffTotal += is_numeric($monthData['revenueDailyUtilizationTakeOffTotal'] ?? 0) ? $monthData['revenueDailyUtilizationTakeOffTotal'] : 0;
                            $totalTechnicalDelayTotal += is_numeric($monthData['technicalDelayTotal'] ?? 0) ? $monthData['technicalDelayTotal'] : 0;
                            $totalRatePer100TakeOff += is_numeric($monthData['ratePer100TakeOff'] ?? 0) ? $monthData['ratePer100TakeOff'] : 0;
                            $totalTechnicalIncidentTotal += is_numeric($monthData['technicalIncidentTotal'] ?? 0) ? $monthData['technicalIncidentTotal'] : 0;
                            $totalTechnicalIncidentRate += is_numeric($monthData['technicalIncidentRate'] ?? 0) ? $monthData['technicalIncidentRate'] : 0;
                            $totalTechnicalCancellationTotal += is_numeric($monthData['technicalCancellationTotal'] ?? 0) ? $monthData['technicalCancellationTotal'] : 0;
                            $totalDispatchReliability += is_numeric($monthData['dispatchReliability'] ?? 0) ? $monthData['dispatchReliability'] : 0;
                        }
                    @endphp
                    <tr>
                        <x-table.th class="text-left">A/C In Fleet</x-table.th>
                        <x-table.td>{{ $formatNumber($totalAcInFleet / 12) }}</x-table.td>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                // Data A/C In Fleet sudah di-filter di controller untuk exclude remark "out"
                                $acInFleet = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['acInFleet'] ?? 0;
                            @endphp
                            <x-table.td>{{ round($acInFleet) }}</x-table.td>
                        @endfor
                        <x-table.td>{{ $formatNumber($totalAcInFleet / 12) }}</x-table.td>
                    </tr>
                    <tr>
                        <x-table.th class="text-left">A/C In Service (Revenue)</x-table.th>
                        <x-table.td>{{ $formatNumber($totalAcInService / 12) }}</x-table.td>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $acInService = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['acInService'] ?? 0;
                            @endphp
                            <x-table.td>{{ $formatNumber($acInService) }}</x-table.td>
                        @endfor
                        <x-table.td>{{ $formatNumber($totalAcInService / 12) }}</x-table.td>
                    </tr>
                    <tr>
                        <x-table.th class="text-left">A/C Days In Service (Revenue)</x-table.th>
                        <x-table.td>{{ round($totalDaysInService) }}</x-table.td>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $daysInService = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['daysInService'] ?? 0;
                            @endphp
                            <x-table.td>{{ $daysInService }}</x-table.td>
                        @endfor
                        <x-table.td>{{ round($totalDaysInService) }}</x-table.td>
                    </tr>
                    <tr>
                        <x-table.th class="text-left">Flying Hours - Total</x-table.th>
                        <x-table.td>{{ round($totalFlyingHoursTotal) }}</x-table.td>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $flyingHoursTotal = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['flyingHoursTotal'] ?? 0;
                            @endphp
                            <x-table.td>{{ round($flyingHoursTotal) }}</x-table.td>
                        @endfor
                        <x-table.td>{{ round($totalFlyingHoursTotal)}}</x-table.td>
                    </tr>
                    <tr>
                        <x-table.th class="text-left">- Revenue</x-table.th>
                        <x-table.td>{{ round($totalRevenueFlyingHours) }}</x-table.td>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $revenueFlyingHours = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['revenueFlyingHours'] ?? 0;
                            @endphp
                            <x-table.td>{{ round($revenueFlyingHours) }}</x-table.td>
                        @endfor
                        <x-table.td>{{ round($totalRevenueFlyingHours) }}</x-table.td>
                    </tr>
                    <tr>
                        <x-table.th class="text-left">Take Off - Total</x-table.th>
                        <x-table.td>{{ round($totalTakeOffTotal) }}</x-table.td>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $takeOffTotal = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['takeOffTotal'] ?? 0;
                            @endphp
                            <x-table.td>{{ $takeOffTotal }}</x-table.td>
                        @endfor
                        <x-table.td>{{ round($totalTakeOffTotal) }}</x-table.td>
                    </tr>
                    <tr>
                        <x-table.th class="text-left">- Revenue</x-table.th>
                        <x-table.td>{{ round($totalRevenueTakeOff) }}</x-table.td>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $revenueTakeOff = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['revenueTakeOff'] ?? 0;
                            @endphp
                            <x-table.td>{{ $revenueTakeOff }}</x-table.td>
                        @endfor
                        <x-table.td>{{ round($totalRevenueTakeOff) }}</x-table.td>
                    </tr>
                    <tr>
                        <x-table.th class="text-left">Flight Hours per Take Off - Total</x-table.th>
                        <x-table.td>{{ $avgFlightHoursPerTakeOffTotal }}</x-table.td>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $flightHoursPerTakeOffTotal = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['flightHoursPerTakeOffTotal'] ?? '00:00';
                            @endphp
                            <x-table.td>{{ $flightHoursPerTakeOffTotal }}</x-table.td>
                        @endfor
                        <x-table.td>{{ $avgFlightHoursPerTakeOffTotal }}</x-table.td>
                    </tr>
                    <tr>
                        <x-table.th class="text-left">- Revenue</x-table.th>
                        <x-table.td>{{ $avgRevenueFlightHoursPerTakeOff }}</x-table.td>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $revenueFlightHoursPerTakeOff = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['revenueFlightHoursPerTakeOff'] ?? '00:00';
                            @endphp
                            <x-table.td>{{ $revenueFlightHoursPerTakeOff }}</x-table.td>
                        @endfor
                        <x-table.td>{{ $avgRevenueFlightHoursPerTakeOff }}</x-table.td>
                    </tr>
                    <tr>
                        <x-table.th class="text-left">Daily Utiliz - Total FH</x-table.th>
                        <x-table.td>{{ $avgDailyUtilizationFlyingHoursTotal }}</x-table.td>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $dailyUtilizationFlyingHoursTotal = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['dailyUtilizationFlyingHoursTotal'] ?? '00:00';
                            @endphp
                            <x-table.td>{{ $dailyUtilizationFlyingHoursTotal }}</x-table.td>
                        @endfor
                        <x-table.td>{{ $avgDailyUtilizationFlyingHoursTotal }}</x-table.td>
                    </tr>
                    <tr>
                        <x-table.th class="text-left">- Revenue FH</x-table.th>
                        <x-table.td>{{ $avgRevenueDailyUtilizationFlyingHoursTotal }}</x-table.td>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $revenueDailyUtilizationFlyingHoursTotal = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['revenueDailyUtilizationFlyingHoursTotal'] ?? '00:00';
                            @endphp
                            <x-table.td>{{ $revenueDailyUtilizationFlyingHoursTotal }}</x-table.td>
                        @endfor
                        <x-table.td>{{ $avgRevenueDailyUtilizationFlyingHoursTotal }}</x-table.td>
                    </tr>
                    <tr>
                        <x-table.th class="text-left">- Total FC</x-table.th>
                        <x-table.td>{{ $formatNumber($totalDailyUtilizationTakeOffTotal / 12) }}</x-table.td>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $dailyUtilizationTakeOffTotal = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['dailyUtilizationTakeOffTotal'] ?? 0;
                            @endphp
                            <x-table.td>{{ $formatNumber($dailyUtilizationTakeOffTotal) }}</x-table.td>
                        @endfor
                        <x-table.td>{{ $formatNumber($totalDailyUtilizationTakeOffTotal / 12) }}</x-table.td>
                    </tr>
                    <tr>
                        <x-table.th class="text-left">- Revenue FC</x-table.th>
                        <x-table.td>{{ $formatNumber($totalRevenueDailyUtilizationTakeOffTotal / 12) }}</x-table.td>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $revenueDailyUtilizationTakeOffTotal = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['revenueDailyUtilizationTakeOffTotal'] ?? 0;
                            @endphp
                            <x-table.td>{{ $formatNumber($revenueDailyUtilizationTakeOffTotal) }}</x-table.td>
                        @endfor
                        <x-table.td>{{ $formatNumber($totalRevenueDailyUtilizationTakeOffTotal / 12) }}</x-table.td>
                    </tr>
                    <tr>
                        <x-table.th class="text-left">Technical Delay - Total</x-table.th>
                        <x-table.td>{{ round($totalTechnicalDelayTotal) }}</x-table.td>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $technicalDelayTotal = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['technicalDelayTotal'] ?? 0;
                            @endphp
                            <x-table.td>{{ round($technicalDelayTotal) }}</x-table.td>
                        @endfor
                        <x-table.td>{{ round($totalTechnicalDelayTotal) }}</x-table.td>
                    </tr>
                    <tr>
                        <x-table.th class="text-left">- Tot Duration</x-table.th>
                        <x-table.td>{{ $avgTotalDuration }}</x-table.td>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $totalDuration = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['totalDuration'] ?? '00:00';
                            @endphp
                            <x-table.td>{{ $totalDuration }}</x-table.td>
                        @endfor
                        <x-table.td>{{ $avgTotalDuration }}</x-table.td>
                    </tr>
                    <tr>
                        <x-table.th class="text-left">- Avg Duration</x-table.th>
                        <x-table.td>{{ $avgAverageDuration }}</x-table.td>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $averageDuration = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['averageDuration'] ?? '00:00';
                            @endphp
                            <x-table.td>{{ $averageDuration }}</x-table.td>
                        @endfor
                        <x-table.td>{{ $avgAverageDuration }}</x-table.td>
                    </tr>
                    <tr>
                        <x-table.th class="text-left">- Rate/100 Take-Off</x-table.th>
                        <x-table.td>{{ $formatNumber($totalRatePer100TakeOff / 12) }}</x-table.td>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $ratePer100TakeOff = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['ratePer100TakeOff'] ?? 0;
                            @endphp
                            <x-table.td>{{ $formatNumber($ratePer100TakeOff) }}</x-table.td>
                        @endfor
                        <x-table.td>{{ $formatNumber($totalRatePer100TakeOff / 12) }}</x-table.td>
                    </tr>
                    <tr>
                        <x-table.th class="text-left">Technical Incident - Total</x-table.th>
                        <x-table.td>{{ round($totalTechnicalIncidentTotal) }}</x-table.td>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $technicalIncidentTotal = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['technicalIncidentTotal'] ?? 0;
                            @endphp
                            <x-table.td>{{ round($technicalIncidentTotal) }}</x-table.td>
                        @endfor
                        <x-table.td>{{ round($totalTechnicalIncidentTotal) }}</x-table.td>
                    </tr>
                    <tr>
                        <x-table.th class="text-left">- Rate / 100 FC</x-table.th>
                        <x-table.td>{{ $formatNumber($totalTechnicalIncidentRate / 12, 3) }}</x-table.td>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $technicalIncidentRate = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['technicalIncidentRate'] ?? 0;
                            @endphp
                            <x-table.td>{{ $formatNumber($technicalIncidentRate, 3) }}</x-table.td>
                        @endfor
                        <x-table.td>{{ $formatNumber($totalTechnicalIncidentRate / 12, 3) }}</x-table.td>
                    </tr>
                    <tr>
                        <x-table.th class="text-left">Technical Cancellation - Total</x-table.th>
                        <x-table.td>{{ round($totalTechnicalCancellationTotal) }}</x-table.td>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $technicalCancellationTotal = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['technicalCancellationTotal'] ?? 0;
                            @endphp
                            <x-table.td>{{ round($technicalCancellationTotal) }}</x-table.td>
                        @endfor
                        <x-table.td>{{ round($totalTechnicalCancellationTotal) }}</x-table.td>
                    </tr>
                    <tr>
                        <x-table.th class="text-left">Dispatch Reliability (%)</x-table.th>
                        <x-table.td>{{ $formatNumber($totalDispatchReliability / 12) }}%</x-table.td>
                        @for ($i = 11; $i >= 0; $i--)
                            @php
                                $dispatchReliability = $reportData[\Carbon\Carbon::parse($period)->subMonth($i)->format('Y-m')]['dispatchReliability'] ?? 0;
                            @endphp
                            <x-table.td>{{ $formatNumber($dispatchReliability) }}%</x-table.td>
                        @endfor
                        <x-table.td>{{ $formatNumber($totalDispatchReliability / 12) }}%</x-table.td>
                    </tr>
                </x-table.tbody>
            </x-table.index>
        </div>
    </div>
</x-app-layout>