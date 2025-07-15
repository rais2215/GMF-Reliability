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
        $aircraftTypes = TblMasterac::select('ACType')->distinct()
            ->whereNotNull('ACType')
            ->where('ACType', '!=', '')
            ->where('active', 1)
            ->orderBy('ACType')
            ->get();

        $operators = TblMasterac::select('Operator')->distinct()
            ->whereNotNull('Operator')
            ->where('Operator', '!=', '')
            ->where('active', 1)
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

        $query = TblMonthlyfhfc::select(
            'tbl_monthlyfhfc.Reg',
            'tbl_monthlyfhfc.MonthEval',
            'tbl_monthlyfhfc.TSN',
            'tbl_monthlyfhfc.TSNMin',
            'tbl_monthlyfhfc.CSN'
        );

        if ($request->filled('operator') || $request->filled('aircraft_type')) {
            $masteracQuery = TblMasterac::where('active', 1);

            if ($request->filled('operator')) {
                $masteracQuery->where('Operator', $request->operator);
            }

            if ($request->filled('aircraft_type')) {
                $masteracQuery->where('ACType', $request->aircraft_type);
            }

            $registrations = $masteracQuery->pluck('ACReg');
            $query->whereIn('tbl_monthlyfhfc.Reg', $registrations);
        } else {
            $activeRegistrations = TblMasterac::where('active', 1)->pluck('ACReg');
            $query->whereIn('tbl_monthlyfhfc.Reg', $activeRegistrations);
        }

        if ($request->filled('reg')) {
            $regIsActive = TblMasterac::where('ACReg', $request->reg)
                                    ->where('active', 1)
                                    ->exists();

            if ($regIsActive) {
                $query->where('tbl_monthlyfhfc.Reg', $request->reg);
            } else {
                $data = [];
                $summary = [
                    'total_records' => 0,
                    'total_aircraft' => 0,
                    'date_range' => [
                        'from' => null,
                        'to' => null
                    ]
                ];
                $cumulativeData = [];
                return view('report.cumulative-result', compact(
                    'data',
                    'summary',
                    'cumulativeData'
                ))->with([
                    'operator' => $request->operator,
                    'aircraft_type' => $request->aircraft_type,
                    'reg' => $request->reg,
                    'period' => $request->period,
                    'formatted_period' => null
                ]);
            }
        }

        if ($request->filled('period')) {
            $endDate = Carbon::parse($request->period)->endOfMonth();
            $startDate = Carbon::parse($request->period)->startOfMonth()->subMonths(12);
            $query->whereBetween('tbl_monthlyfhfc.MonthEval', [$startDate, $endDate]);
        }

        $rawData = $query->orderBy('tbl_monthlyfhfc.MonthEval', 'desc')
                    ->orderBy('tbl_monthlyfhfc.Reg')
                    ->get();

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

        $data = $processedData->toArray();

        // --- Kumulatif: nilai akhir (Januari tahun sebelum periode) - nilai awal (Desember tahun dua sebelum periode) ---
        $cumulativeData = [];
        if ($request->filled('period')) {
            $regData = [];
            foreach ($data as $record) {
                $reg = $record['reg'] ?? null;
                $monthEval = isset($record['month_eval']) ? Carbon::parse($record['month_eval']) : null;
                if (!$reg || !$monthEval) continue;
                $regData[$reg][] = [
                    'month_eval' => $monthEval,
                    'csn_by_fh' => $record['csn_by_fh'],
                    'csn_by_fc' => $record['csn_by_fc'],
                ];
            }

            foreach ($regData as $reg => $items) {
                // Urutkan data berdasarkan tanggal dari yang terlama ke terbaru
                usort($items, function($a, $b) {
                    return $a['month_eval']->timestamp <=> $b['month_eval']->timestamp;
                });

                // 1. AMBIL DATA TERAKHIR SEBAGAI TITIK AKHIR
                $lastData = end($items);
                if (!$lastData) continue; // Lanjut ke registrasi berikutnya jika tidak ada data

                $endDate = $lastData['month_eval'];
                $fh_end = $lastData['csn_by_fh'];
                $fc_end = $lastData['csn_by_fc'];

                // 2. TENTUKAN TITIK AWAL: 12 BULAN SEBELUM TITIK AKHIR
                $beforeDate = $endDate->copy()->subMonths(12);

                // 3. CARI DATA PEMBANDING (TITIK AWAL)
                $fh_before = null;
                $fc_before = null;
                foreach ($items as $item) {
                    if ($item['month_eval']->isSameMonth($beforeDate)) {
                        $fh_before = $item['csn_by_fh'];
                        $fc_before = $item['csn_by_fc'];
                        break; // Hentikan pencarian jika sudah ketemu
                    }
                }

                // 4. JIKA TIDAK ADA, GUNAKAN LOGIKA ESTIMASI ASLI ANDA
                if ($fh_before === null || $fc_before === null) {
                    $after = [];
                    foreach ($items as $item) {
                        // Kumpulkan data yang ada setelah tanggal titik awal yang dicari
                        if ($item['month_eval'] > $beforeDate) {
                            $after[] = $item;
                        }
                    }
                    if (count($after) >= 2) {
                        // Estimasi linear: nilai = y2 - (y3 - y2)
                        $fh_before = $after[0]['csn_by_fh'] - ($after[1]['csn_by_fh'] - $after[0]['csn_by_fh']);
                        $fc_before = $after[0]['csn_by_fc'] - ($after[1]['csn_by_fc'] - $after[0]['csn_by_fc']);
                    } else {
                        // Jika tidak bisa estimasi, maka 'before' dianggap 0 agar selisihnya adalah total lifetime
                        $fh_before = 0;
                        $fc_before = 0;
                    }
                }

                // 5. HITUNG SELISIHNYA
                $cumulativeData[$reg] = [
                    'cumulative_fh' => $fh_end - $fh_before,
                    'cumulative_fc' => $fc_end - $fc_before,
                ];
            }
        }

        return view('report.cumulative-result', compact(
            'data',
            'summary',
            'formatted_period',
            'cumulativeData'
         ))->with([
            'operator' => $request->operator,
            'aircraft_type' => $request->aircraft_type,
            'reg' => $request->reg,
            'period' => $request->period
        ]);
    }

    public function cumulativeExportPdf(Request $request)
    {
        $request->validate([
            'aircraft_type' => 'nullable|string',
            'operator' => 'nullable|string',
            'period' => 'nullable|date',
            'reg' => 'nullable|string',
        ]);

        // Ambil semua data sesuai filter, JANGAN batasi reg kecuali user memilih reg tertentu
        $query = TblMonthlyfhfc::select(
            'tbl_monthlyfhfc.Reg',
            'tbl_monthlyfhfc.MonthEval',
            'tbl_monthlyfhfc.TSN',
            'tbl_monthlyfhfc.TSNMin',
            'tbl_monthlyfhfc.CSN'
        );

        // Filter operator/aircraft_type jika ada
        if ($request->filled('operator') || $request->filled('aircraft_type')) {
            $masteracQuery = TblMasterac::where('active', 1);

            if ($request->filled('operator')) {
                $masteracQuery->where('Operator', $request->operator);
            }

            if ($request->filled('aircraft_type')) {
                $masteracQuery->where('ACType', $request->aircraft_type);
            }

            $registrations = $masteracQuery->pluck('ACReg');
            $query->whereIn('tbl_monthlyfhfc.Reg', $registrations);
        } else {
            // Ambil semua reg aktif
            $activeRegistrations = TblMasterac::where('active', 1)->pluck('ACReg');
            $query->whereIn('tbl_monthlyfhfc.Reg', $activeRegistrations);
        }

        // Filter periode jika ada
        if ($request->filled('period')) {
            $endDate = Carbon::parse($request->period)->endOfMonth();
            $startDate = Carbon::parse($request->period)->startOfMonth()->subMonths(12);
            $query->whereBetween('tbl_monthlyfhfc.MonthEval', [$startDate, $endDate]);
        }

        // Ambil data
        $rawData = $query->orderBy('tbl_monthlyfhfc.MonthEval', 'desc')
                    ->orderBy('tbl_monthlyfhfc.Reg')
                    ->get();

        // Proses data
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

        $data = $processedData->toArray();

        // Hitung kumulatif per reg (jika diperlukan di PDF)
        $cumulativeData = [];
        if ($request->filled('period')) {
            $periodYear = Carbon::parse($request->period)->year;
            $endMonth = 1;
            $endYear = $periodYear;
            $beforeMonth = 1;
            $beforeYear = $periodYear - 1;

            $regData = [];
            foreach ($data as $record) {
                $reg = $record['reg'] ?? null;
                $monthEval = isset($record['month_eval']) ? Carbon::parse($record['month_eval']) : null;
                if (!$reg || !$monthEval) continue;
                $regData[$reg][] = [
                    'month_eval' => $monthEval,
                    'csn_by_fh' => $record['csn_by_fh'],
                    'csn_by_fc' => $record['csn_by_fc'],
                ];
            }

            foreach ($regData as $reg => $items) {
                usort($items, function($a, $b) {
                    return $a['month_eval']->timestamp <=> $b['month_eval']->timestamp;
                });

                $fh_end = null;
                $fc_end = null;
                foreach ($items as $item) {
                    $m = $item['month_eval']->month;
                    $y = $item['month_eval']->year;
                    if ($m == $endMonth && $y == $endYear) {
                        $fh_end = $item['csn_by_fh'];
                        $fc_end = $item['csn_by_fc'];
                        break;
                    }
                }
                $fh_end = $fh_end ?? 0;
                $fc_end = $fc_end ?? 0;

                $fh_before = null;
                $fc_before = null;
                foreach ($items as $item) {
                    $m = $item['month_eval']->month;
                    $y = $item['month_eval']->year;
                    if ($m == $beforeMonth && $y == $beforeYear) {
                        $fh_before = $item['csn_by_fh'];
                        $fc_before = $item['csn_by_fc'];
                        break;
                    }
                }

                if ($fh_before === null || $fc_before === null) {
                    $after = [];
                    foreach ($items as $item) {
                        $y = $item['month_eval']->year;
                        $m = $item['month_eval']->month;
                        if ($y > $beforeYear || ($y == $beforeYear && $m > $beforeMonth)) {
                            $after[] = $item;
                        }
                    }
                    if (count($after) >= 2) {
                        $fh_before = $after[0]['csn_by_fh'] - ($after[1]['csn_by_fh'] - $after[0]['csn_by_fh']);
                        $fc_before = $after[0]['csn_by_fc'] - ($after[1]['csn_by_fc'] - $after[0]['csn_by_fc']);
                    } elseif (count($after) == 1) {
                        $fh_before = 0;
                        $fc_before = 0;
                    } else {
                        $fh_before = 0;
                        $fc_before = 0;
                    }
                }

                $cumulativeData[$reg] = [
                    'cumulative_fh' => $fh_end - $fh_before,
                    'cumulative_fc' => $fc_end - $fc_before,
                ];
            }
        }

        // Kirim ke view PDF
        $pdf = Pdf::loadView('pdf.cumulative-pdf', compact(
            'data',
            'summary',
            'formatted_period',
            'cumulativeData'
        ));

        $pdf->setPaper('A4', 'landscape');
        // Tambahkan type of aircraft dan period ke nama file
        $aircraftType = $request->aircraft_type ? str_replace(' ', '_', $request->aircraft_type) : 'AllTypes';
        $period = $formatted_period ? str_replace(' ', '_', $formatted_period) : 'AllPeriods';
        $filename = "Cumulative-Report-{$aircraftType}-{$period}.pdf";

        return $pdf->download($filename);
    }

    public function cumulativeExportExcel(Request $request)
    {
        $request->validate([
            'aircraft_type' => 'nullable|string',
            'operator' => 'nullable|string',
            'period' => 'nullable|date',
            'reg' => 'nullable|string',
        ]);

        // --- Copy logic dari cumulativeExportPdf, hanya ganti return menjadi Excel::download ---
        $query = TblMonthlyfhfc::select(
            'tbl_monthlyfhfc.Reg',
            'tbl_monthlyfhfc.MonthEval',
            'tbl_monthlyfhfc.TSN',
            'tbl_monthlyfhfc.TSNMin',
            'tbl_monthlyfhfc.CSN'
        );

        if ($request->filled('operator') || $request->filled('aircraft_type')) {
            $masteracQuery = TblMasterac::where('active', 1);

            if ($request->filled('operator')) {
                $masteracQuery->where('Operator', $request->operator);
            }

            if ($request->filled('aircraft_type')) {
                $masteracQuery->where('ACType', $request->aircraft_type);
            }

            $registrations = $masteracQuery->pluck('ACReg');
            $query->whereIn('tbl_monthlyfhfc.Reg', $registrations);
        } else {
            $activeRegistrations = TblMasterac::where('active', 1)->pluck('ACReg');
            $query->whereIn('tbl_monthlyfhfc.Reg', $activeRegistrations);
        }

        if ($request->filled('period')) {
            $endDate = Carbon::parse($request->period)->endOfMonth();
            $startDate = Carbon::parse($request->period)->startOfMonth()->subMonths(12);
            $query->whereBetween('tbl_monthlyfhfc.MonthEval', [$startDate, $endDate]);
        }

        $rawData = $query->orderBy('tbl_monthlyfhfc.MonthEval', 'desc')
                    ->orderBy('tbl_monthlyfhfc.Reg')
                    ->get();

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

        $data = $processedData->toArray();

        // Hitung kumulatif per reg (sama seperti PDF)
        $cumulativeData = [];
        if ($request->filled('period')) {
            $periodYear = Carbon::parse($request->period)->year;
            $endMonth = 1;
            $endYear = $periodYear;
            $beforeMonth = 1;
            $beforeYear = $periodYear - 1;

            $regData = [];
            foreach ($data as $record) {
                $reg = $record['reg'] ?? null;
                $monthEval = isset($record['month_eval']) ? Carbon::parse($record['month_eval']) : null;
                if (!$reg || !$monthEval) continue;
                $regData[$reg][] = [
                    'month_eval' => $monthEval,
                    'csn_by_fh' => $record['csn_by_fh'],
                    'csn_by_fc' => $record['csn_by_fc'],
                ];
            }

            foreach ($regData as $reg => $items) {
                usort($items, function($a, $b) {
                    return $a['month_eval']->timestamp <=> $b['month_eval']->timestamp;
                });

                $fh_end = null;
                $fc_end = null;
                foreach ($items as $item) {
                    $m = $item['month_eval']->month;
                    $y = $item['month_eval']->year;
                    if ($m == $endMonth && $y == $endYear) {
                        $fh_end = $item['csn_by_fh'];
                        $fc_end = $item['csn_by_fc'];
                        break;
                    }
                }
                $fh_end = $fh_end ?? 0;
                $fc_end = $fc_end ?? 0;

                $fh_before = null;
                $fc_before = null;
                foreach ($items as $item) {
                    $m = $item['month_eval']->month;
                    $y = $item['month_eval']->year;
                    if ($m == $beforeMonth && $y == $beforeYear) {
                        $fh_before = $item['csn_by_fh'];
                        $fc_before = $item['csn_by_fc'];
                        break;
                    }
                }

                if ($fh_before === null || $fc_before === null) {
                    $after = [];
                    foreach ($items as $item) {
                        $y = $item['month_eval']->year;
                        $m = $item['month_eval']->month;
                        if ($y > $beforeYear || ($y == $beforeYear && $m > $beforeMonth)) {
                            $after[] = $item;
                        }
                    }
                    if (count($after) >= 2) {
                        $fh_before = $after[0]['csn_by_fh'] - ($after[1]['csn_by_fh'] - $after[0]['csn_by_fh']);
                        $fc_before = $after[0]['csn_by_fc'] - ($after[1]['csn_by_fc'] - $after[0]['csn_by_fc']);
                    } elseif (count($after) == 1) {
                        $fh_before = 0;
                        $fc_before = 0;
                    } else {
                        $fh_before = 0;
                        $fc_before = 0;
                    }
                }

                $cumulativeData[$reg] = [
                    'cumulative_fh' => $fh_end - $fh_before,
                    'cumulative_fc' => $fc_end - $fc_before,
                ];
            }
        }

        $aircraftType = $request->aircraft_type ? str_replace(' ', '_', $request->aircraft_type) : 'AllTypes';
        $period = $formatted_period ? str_replace(' ', '_', $formatted_period) : 'AllPeriods';
        $filename = "Cumulative-Report-{$aircraftType}-{$period}.xlsx";

        return Excel::download(
            new \App\Exports\CumulativeExport($data, $summary, $formatted_period, $cumulativeData),
            $filename
        );
    }
}
