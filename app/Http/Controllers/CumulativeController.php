<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CumulativeExport;
use App\Models\TblPirepSwift;
use App\Models\TblMasterac;
use App\Models\TblMonthlyfhfc;
use Carbon\Carbon;

class CumulativeController extends Controller
{
    public function cumulativeIndex()
    {
        // Ambil aircraft types dari TblMasterac yang active = 1
        $aircraftTypes = TblMasterac::select('ACType')->distinct()
            ->whereNotNull('ACType')
            ->where('ACType', '!=', '')
            ->where('active', 1) // Filter hanya yang active
            ->orderBy('ACType')
            ->get();

        $operators = TblMasterac::select('Operator')->distinct()
            ->whereNotNull('Operator')
            ->where('Operator', '!=', '')
            ->where('active', 1) // Filter hanya yang active
            ->orderBy('Operator')
            ->get();

        $periods = TblMonthlyfhfc::select('MonthEval')->distinct()
            ->orderByDesc('MonthEval')->get()
            ->map(function($item) {
                return [
                    'formatted' => Carbon::parse($item->MonthEval)->format('Y-m'),
                    'original' => $item->MonthEval
                ];
            });

        return view('report.cumulative-content', compact('aircraftTypes', 'operators', 'periods'));
    }

    public function cumulativeStore(Request $request)
    {
        $request->validate([
            'aircraft_type' => 'nullable|string',
            'operator' => 'nullable|string',
            'period' => 'nullable|date',
            'reg' => 'nullable|string',
        ]);

        // --- Kolom yang dipilih sudah benar ---
        $query = TblMonthlyfhfc::select(
            'tbl_monthlyfhfc.Reg',
            'tbl_monthlyfhfc.MonthEval',
            'tbl_monthlyfhfc.TSN',
            'tbl_monthlyfhfc.TSNMin',
            'tbl_monthlyfhfc.CSN'
        );

        // Filter berdasarkan operator atau aircraft_type yang active = 1
        if ($request->filled('operator') || $request->filled('aircraft_type')) {
            $masteracQuery = TblMasterac::where('active', 1); // Filter hanya yang active

            if ($request->filled('operator')) {
                $masteracQuery->where('Operator', $request->operator);
            }

            if ($request->filled('aircraft_type')) {
                $masteracQuery->where('ACType', $request->aircraft_type);
            }

            $registrations = $masteracQuery->pluck('ACReg');

            $query->whereIn('tbl_monthlyfhfc.Reg', $registrations);
        } else {
            // Jika tidak ada filter operator/aircraft_type, tetap filter berdasarkan active = 1
            $activeRegistrations = TblMasterac::where('active', 1)->pluck('ACReg');
            $query->whereIn('tbl_monthlyfhfc.Reg', $activeRegistrations);
        }

        if ($request->filled('reg')) {
            // Pastikan reg yang dipilih juga active = 1
            $regIsActive = TblMasterac::where('ACReg', $request->reg)
                                    ->where('active', 1)
                                    ->exists();

            if ($regIsActive) {
                $query->where('tbl_monthlyfhfc.Reg', $request->reg);
            } else {
                // Jika reg tidak active, return dengan data kosong
                $data = [];
                $summary = [
                    'total_records' => 0,
                    'total_aircraft' => 0,
                    'date_range' => [
                        'from' => null,
                        'to' => null
                    ]
                ];

                return view('report.cumulative-result', compact(
                    'data',
                    'summary'
                ))->with([
                    'operator' => $request->operator,
                    'aircraft_type' => $request->aircraft_type,
                    'reg' => $request->reg,
                    'period' => $request->period,
                    'formatted_period' => null
                ]);
            }
        }

        // --- Logika pengambilan data selama 12 bulan ---
        if ($request->filled('period')) {
            $endDate = Carbon::parse($request->period)->endOfMonth();
            // Menggunakan logika yang lebih jelas untuk mendapatkan tanggal awal
            $startDate = Carbon::parse($request->period)->startOfMonth()->subMonths(11);
            $query->whereBetween('tbl_monthlyfhfc.MonthEval', [$startDate, $endDate]);
        }

        $rawData = $query->orderBy('tbl_monthlyfhfc.MonthEval', 'desc')
                    ->orderBy('tbl_monthlyfhfc.Reg')
                    ->get();

        // --- Logika kalkulasi yang benar ---
        $processedData = $rawData->map(function($item) {
            $cumulativeFH = round($item->TSN + ($item->TSNMin / 60));
            $cumulativeFC = $item->CSN;

            return [
                'reg' => $item->Reg,
                'month_eval' => $item->MonthEval,
                'csn_by_fh' => $cumulativeFH,
                'csn_by_fc' => $cumulativeFC,
            ];
        });

        $groupedData = $processedData->groupBy('reg');

        $summary = [
            'total_records' => $processedData->count(),
            'total_aircraft' => $groupedData->count(),
            'date_range' => [
                'from' => $rawData->count() > 0 ? Carbon::parse($rawData->min('MonthEval'))->format('F Y') : null,
                'to' => $rawData->count() > 0 ? Carbon::parse($rawData->max('MonthEval'))->format('F Y') : null
            ]
        ];

        $formatted_period = null;
        if ($request->filled('period')) {
            $formatted_period = Carbon::parse($request->period)->format('F Y');
        }

        // --- PERBAIKAN FINAL: Mengembalikan nama variabel ke '$data' dan mengubahnya menjadi array ---
        // Ini untuk memastikan kompatibilitas penuh dengan file view Anda yang sudah ada.
        $data = $processedData->toArray();

        return view('report.cumulative-result', compact(
            'data', // Nama variabel dikembalikan ke 'data'
            'summary',
            'formatted_period'
        ))->with([
            'operator' => $request->operator,
            'aircraft_type' => $request->aircraft_type,
            'reg' => $request->reg,
            'period' => $request->period
        ]);
    }
}
