@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-indigo-900 py-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Cumulative Flight Hours Report</h1>
            <div class="text-white/80">
                <p class="text-lg">{{ $operator }} - {{ $aircraft_type }}</p>
                <p class="text-sm">Period: {{ $formatted_period }}</p>
            </div>
        </div>

        <!-- Export Buttons -->
        <div class="flex justify-center gap-4 mb-6">
            <form action="{{ route('report.cumulative.export.pdf') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="period" value="{{ $period }}">
                <input type="hidden" name="operator" value="{{ $operator }}">
                <input type="hidden" name="aircraft_type" value="{{ $aircraft_type }}">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                    <i class="fas fa-file-pdf mr-2"></i>Export PDF
                </button>
            </form>

            <form action="{{ route('report.cumulative.export.excel') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="period" value="{{ $period }}">
                <input type="hidden" name="operator" value="{{ $operator }}">
                <input type="hidden" name="aircraft_type" value="{{ $aircraft_type }}">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                    <i class="fas fa-file-excel mr-2"></i>Export Excel
                </button>
            </form>

            <a href="{{ route('report.cumulative.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i>Back to Form
            </a>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg p-4 shadow-md">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ number_format($summary['total_aircraft']) }}</div>
                    <div class="text-sm text-gray-600">Total Aircraft</div>
                </div>
            </div>
            <div class="bg-white rounded-lg p-4 shadow-md">
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ number_format($summary['total_flight_hours'], 1) }}</div>
                    <div class="text-sm text-gray-600">Total Flight Hours</div>
                </div>
            </div>
            <div class="bg-white rounded-lg p-4 shadow-md">
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ number_format($summary['total_flight_cycles']) }}</div>
                    <div class="text-sm text-gray-600">Total Flight Cycles</div>
                </div>
            </div>
            <div class="bg-white rounded-lg p-4 shadow-md">
                <div class="text-center">
                    <div class="text-2xl font-bold text-orange-600">{{ number_format($summary['total_takeoffs']) }}</div>
                    <div class="text-sm text-gray-600">Total Takeoffs</div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h3 class="text-lg font-semibold text-gray-800">Aircraft Details</h3>
            </div>
            
            @if($data->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aircraft Registration</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aircraft Type</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Flight Hours</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Flight Cycles</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Takeoffs</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Months in Service</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($data as $index => $aircraft)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $aircraft->ACReg }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $aircraft->ACType }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($aircraft->total_flight_hours, 1) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($aircraft->total_flight_cycles) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($aircraft->total_takeoffs) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ $aircraft->total_months }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr class="font-semibold">
                                <td colspan="3" class="px-6 py-4 text-sm text-gray-900">TOTAL</td>
                                <td class="px-6 py-4 text-sm text-gray-900 text-right">{{ number_format($summary['total_flight_hours'], 1) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 text-right">{{ number_format($summary['total_flight_cycles']) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 text-right">{{ number_format($summary['total_takeoffs']) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 text-right">-</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="px-6 py-8 text-center">
                    <div class="text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-4"></i>
                        <p class="text-lg">No data found for the selected criteria.</p>
                        <p class="text-sm">Try adjusting your filter parameters.</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Footer Info -->
        <div class="mt-6 text-center text-white/80 text-sm">
            <p>Report generated on {{ date('d F Y, H:i:s') }}</p>
        </div>
    </div>
</div>
@endsection