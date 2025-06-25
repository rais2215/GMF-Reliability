<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Contracts\View\View;

class AosExport implements FromView, WithTitle
{
    protected $data;

    public function __construct($reportData, $period, $aircraftType, $averages = null)
    {
        $this->data = [
            'reportData' => $reportData,
            'period' => $period,
            'aircraftType' => $aircraftType,
            'averages' => $averages
        ];
    }

    public function view(): View
    {
        return view('excel.aos-excel', $this->data);
    }

    public function title(): string
    {
        return 'AOS Report';
    }
}