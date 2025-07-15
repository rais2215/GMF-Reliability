<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CumulativeExport implements FromView
{
    protected $data;
    protected $summary;
    protected $formatted_period;
    protected $cumulativeData;

    public function __construct($data, $summary, $formatted_period, $cumulativeData)
    {
        $this->data = $data;
        $this->summary = $summary;
        $this->formatted_period = $formatted_period;
        $this->cumulativeData = $cumulativeData;
    }

    public function view(): View
    {
        return view('excel.cumulative-excel', [
            'data' => $this->data,
            'summary' => $this->summary,
            'formatted_period' => $this->formatted_period,
            'cumulativeData' => $this->cumulativeData
        ]);
    }
}
