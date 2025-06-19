<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CumulativeExport;
use App\Models\TblPirepSwift;
use App\Models\TblMasterac;
use App\Models\TblMonthlyfhfc;
use Carbon\Carbon;

class CumulativeController extends Controller
{
    // Ubah dari cumulativeIndex() menjadi index()
    public function index()
    {
        $aircraftTypes = TblPirepSwift::select('ACTYPE')->distinct()
            ->whereNotNull('ACTYPE')
            ->where('ACTYPE', '!=', '')
            ->where('ACTYPE', '!=', 'default')
            ->get();

        $operators = TblMasterac::select('Operator')->distinct()
            ->whereNotNull('Operator')
            ->where('Operator', '!=', '')
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

    // Ubah dari cumulativeStore() menjadi store()
    public function store(Request $request)
    {
        // Implementation untuk store
    }

    public function exportPdf(Request $request)
    {
        // Implementation untuk export PDF
    }

    public function exportExcel(Request $request)
    {
        // Implementation untuk export Excel
    }
}