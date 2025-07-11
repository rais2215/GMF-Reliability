<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AosExport implements FromView, WithTitle, ShouldAutoSize
{
    protected $reportData;
    protected $period;
    protected $aircraftType;
    protected $processedData; // Untuk "Last 12 Months"
    protected $yearData;      // Data untuk kolom tahun (base year)
    protected $baseYear;      // Tahun patokan

    public function __construct(
        array $reportData,
        string $period,
        string $aircraftType,
        array $processedData,
        array $yearData,
        string $baseYear
    ) {
        $this->reportData = $reportData;
        $this->period = $period;
        $this->aircraftType = $aircraftType;
        $this->processedData = $processedData;
        $this->yearData = $yearData;
        $this->baseYear = $baseYear;
    }

    /**
     * @return View
     */
    public function view(): View
    {
        return view('excel.aos-excel', [
            'reportData' => $this->reportData,
            'period' => $this->period,
            'aircraftType' => $this->aircraftType,
            'processedData' => $this->processedData,
            'yearData' => $this->yearData,
            'baseYear' => $this->baseYear,
        ]);
    }

    public function title(): string
    {
        return 'AOS Report';
    }
}
