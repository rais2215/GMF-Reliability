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
    protected $data2016;      // Untuk kolom tahun
    protected $data2017;      // Untuk kolom tahun

    // Constructor harus menerima semua 6 argumen
    public function __construct(
        array $reportData,
        string $period,
        string $aircraftType,
        array $processedData,
        array $data2016,
        array $data2017
    ) {
        $this->reportData = $reportData;
        $this->period = $period;
        $this->aircraftType = $aircraftType;
        $this->processedData = $processedData;
        $this->data2016 = $data2016;
        $this->data2017 = $data2017;
    }

    /**
     * @return View
     */
    public function view(): View
    {
        // Teruskan semua data ke view dengan nama variabel yang benar
        return view('excel.aos-excel', [
            'reportData' => $this->reportData,
            'period' => $this->period,
            'aircraftType' => $this->aircraftType,
            'processedData' => $this->processedData, // Ini akan mengatasi error
            'data2016' => $this->data2016,
            'data2017' => $this->data2017,
        ]);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'AOS Report';
    }
}
