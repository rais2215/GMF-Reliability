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

    <div class="mx-auto py-4 px-4 sm:px-6 lg:px-8">
        <div id="loadingSkeleton" class="fixed inset-0 z-50 flex-col items-center justify-center bg-white bg-opacity-90 hidden transition-opacity duration-300"> <div class="flex space-x-2 mb-4"> <div class="w-3 h-12 bg-blue-600 rounded animate-loader-bar"></div> <div class="w-3 h-12 bg-blue-600 rounded animate-loader-bar delay-150"></div> <div class="w-3 h-12 bg-blue-600 rounded animate-loader-bar delay-300"></div> </div> <span id="loader-text" class="text-sm font-medium text-gray-800">Loading data...</span> </div>
        <style> @keyframes bar-bounce{0%,100%{transform:scaleY(.5);opacity:.5}50%{transform:scaleY(1.2);opacity:1}}.animate-loader-bar{animation:bar-bounce 1s infinite ease-in-out}.delay-150{animation-delay:.15s}.delay-300{animation-delay:.3s} </style>
        <script> function showLoadingAndGoBack(){const o=document.getElementById("loadingSkeleton"),e=document.getElementById("loader-text");o.classList.remove("hidden"),o.classList.add("flex"),e.textContent="Navigating back...",setTimeout(()=>{history.back()},500)}function showExportLoading(o){const e=document.getElementById("loadingSkeleton"),t=document.getElementById("loader-text");e.classList.remove("hidden"),e.classList.add("flex"),t.textContent=`Exporting to ${o}...`,setTimeout(()=>{e.classList.add("hidden"),e.classList.remove("flex"),t.textContent="Loading data..."},3e3)} </script>
        
        <div class="flex justify-between items-center mb-4">
            <button onclick="showLoadingAndGoBack()" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"> <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg> Back to Report </button>
            <div class="flex space-x-2">
                <form action="{{ route('report.cumulative.export.pdf') }}" method="POST" class="inline" onsubmit="showExportLoading('PDF')"> @csrf @if(isset($period))<input type="hidden" name="period" value="{{ $period }}">@endif @if(isset($operator))<input type="hidden" name="operator" value="{{ $operator }}">@endif @if(isset($aircraft_type))<input type="hidden" name="aircraft_type" value="{{ $aircraft_type }}">@endif @if(isset($reg))<input type="hidden" name="reg" value="{{ $reg }}">@endif <button type="submit" class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-red-500 rounded-md shadow-sm hover:bg-red-800"> Export to PDF </button> </form>
                <form action="{{ route('report.cumulative.export.excel') }}" method="POST" class="inline" onsubmit="showExportLoading('Excel')"> @csrf @if(isset($period))<input type="hidden" name="period" value="{{ $period }}">@endif @if(isset($operator))<input type="hidden" name="operator" value="{{ $operator }}">@endif @if(isset($aircraft_type))<input type="hidden" name="aircraft_type" value="{{ $aircraft_type }}">@endif @if(isset($reg))<input type="hidden" name="reg" value="{{ $reg }}">@endif <button type="submit" class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-green-500 rounded-md shadow-sm hover:bg-green-800"> Export to Excel </button> </form>
            </div>
        </div>

        <div class="text-center mb-6">
            <p class="text-xl font-bold uppercase">CUMULATIVE FLIGHT HOURS AND TAKE OFF {{ $yearRange }}</p>
            <p class="text-lg font-semibold uppercase">{{ $aircraftType }}</p>
        </div>

        <div class="mt-3 flow-root">
            @if (!empty($pivotedData))
                <div class="mb-10">
                    <h3 class="font-bold bg-gray-200 p-2 text-left text-sm">FLIGHT HOURS</h3>
                    <x-table.index>
                        <x-table.thead>
                            <tr class="bg-gray-50">
                                <x-table.th>A/C REG</x-table.th>
                                <x-table.th>YEAR</x-table.th>
                                @foreach ($months as $month)
                                    <x-table.th>{{ $month }}</x-table.th>
                                @endforeach
                            </tr>
                        </x-table.thead>
                        <x-table.tbody>
                            @foreach ($pivotedData as $registration => $monthlyData)
                                <tr>
                                    <x-table.td>{{ $registration }}</x-table.td>
                                    <x-table.td>{{ $startYearForDisplay }}</x-table.td>
                                    @foreach ($months as $month)
                                        <x-table.td class="text-right">
                                            {{ formatTableNumber($monthlyData['fh'][$month] ?? null) }}
                                        </x-table.td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </x-table.tbody>
                    </x-table.index>
                </div>

                <div>
                    <h3 class="font-bold bg-gray-200 p-2 text-left text-sm">FLIGHT CYCLE</h3>
                    <x-table.index>
                        <x-table.thead>
                            <tr class="bg-gray-50">
                                <x-table.th>A/C REG</x-table.th>
                                <x-table.th>YEAR</x-table.th>
                                @foreach ($months as $month)
                                    <x-table.th>{{ $month }}</x-table.th>
                                @endforeach
                            </tr>
                        </x-table.thead>
                        <x-table.tbody>
                            @foreach ($pivotedData as $registration => $monthlyData)
                                <tr>
                                    <x-table.td>{{ $registration }}</x-table.td>
                                    <x-table.td>{{ $startYearForDisplay }}</x-table.td>
                                    @foreach ($months as $month)
                                        <x-table.td class="text-right">
                                            {{ formatTableNumber($monthlyData['fc'][$month] ?? null) }}
                                        </x-table.td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </x-table.tbody>
                    </x-table.index>
                </div>
            @else
                 <x-table.index>
                    <x-table.tbody>
                        <tr>
                            <x-table.td colspan="14" class="text-center py-8">
                                <div class="text-gray-500">
                                    <p class="text-lg">No data found for the selected criteria.</p>
                                    <p class="text-sm">Try adjusting your filter parameters.</p>
                                </div>
                            </x-table.td>
                        </tr>
                    </x-table.tbody>
                </x-table.index>
            @endif
        </div>

        <div class="mt-6 text-center text-gray-600 text-sm">
            <p>Report generated on {{ date('d F Y, H:i:s') }}</p>
        </div>
    </div>
</x-app-layout>