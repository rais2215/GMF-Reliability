<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class AosExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $reportData;
    protected $period;
    protected $aircraftType;

    public function __construct($reportData, $period, $aircraftType)
    {
        $this->reportData = $reportData;
        $this->period = $period;
        $this->aircraftType = $aircraftType;
    }

    public function collection()
    {
        $data = new Collection();
        
        foreach ($this->reportData as $period => $metrics) {
            $data->push([
                'Period' => $period,
                'A/C In Fleet' => $metrics['acInFleet'],
                'A/C In Service' => number_format($metrics['acInService'], 2),
                'A/C Days In Service' => $metrics['daysInService'],
                'Flying Hours - Total' => intval($metrics['flyingHoursTotal']),
                'Revenue Flying Hours' => intval($metrics['revenueFlyingHours']),
                'Take Off - Total' => $metrics['takeOffTotal'],
                'Revenue Take Off' => $metrics['revenueTakeOff'],
                'Flight Hours per Take Off - Total' => $metrics['flightHoursPerTakeOffTotal'],
                'Revenue Flight Hours per Take Off' => $metrics['revenueFlightHoursPerTakeOff'],
                'Daily Utilization - Flying Hours Total' => $metrics['dailyUtilizationFlyingHoursTotal'],
                'Revenue Daily Utilization - Flying Hours Total' => $metrics['revenueDailyUtilizationFlyingHoursTotal'],
                'Daily Utilization - Take Off Total' => number_format($metrics['dailyUtilizationTakeOffTotal'], 2),
                'Revenue Daily Utilization - Take Off Total' => number_format($metrics['revenueDailyUtilizationTakeOffTotal'], 2),
                'Technical Delay - Total' => $metrics['technicalDelayTotal'],
                'Total Duration' => $metrics['totalDuration'],
                'Average Duration' => $metrics['averageDuration'],
                'Rate / 100 Take Off' => number_format($metrics['ratePer100TakeOff'], 2),
                'Technical Incident - Total' => $metrics['technicalIncidentTotal'],
                'Technical Incident Rate /100 FC' => number_format($metrics['technicalIncidentRate'], 2),
                'Technical Cancellation - Total' => $metrics['technicalCancellationTotal'],
                'Dispatch Reliability (%)' => number_format($metrics['dispatchReliability'], 2),
            ]);
        }
        
        return $data;
    }

    public function headings(): array
    {
        return [
            ['AIRCRAFT OPERATIONS SUMMARY REPORT'],
            ['Aircraft Type: ' . $this->aircraftType],
            ['Period: ' . $this->period],
            [
                'Period',
                'A/C In Fleet',
                'A/C In Service',
                'A/C Days In Service',
                'Flying Hours - Total',
                'Revenue Flying Hours',
                'Take Off - Total',
                'Revenue Take Off',
                'Flight Hours per Take Off - Total',
                'Revenue Flight Hours per Take Off',
                'Daily Utilization - Flying Hours Total', 
                'Revenue Daily Utilization - Flying Hours Total',
                'Daily Utilization - Take Off Total',
                'Revenue Daily Utilization - Take Off Total',
                'Technical Delay - Total',
                'Total Duration',
                'Average Duration',
                'Rate / 100 Take Off',
                'Technical Incident - Total',
                'Technical Incident Rate /100 FC',
                'Technical Cancellation - Total',
                'Dispatch Reliability (%)'
            ],
        ];
    }

    public function styles(Worksheet $sheet)
{
    $highestRow = $sheet->getHighestRow();
    $highestColumn = $sheet->getHighestColumn();
    $dataRange = 'A4:' . $highestColumn . $highestRow; //Start from row 4

    // Merge cell untuk Judul
    $sheet->mergeCells("A1:{$highestColumn}1");
    $sheet->mergeCells("A2:{$highestColumn}2");
    $sheet->mergeCells("A3:{$highestColumn}3");    

    // Style untuk judul
    $sheet->getStyle('A1')->applyFromArray([
        'font' => [
            'bold' => true,
            'size' => 20,
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ],
    ]);

    // Style untuk Aircraft Type dan Period
    $sheet->getStyle('A2')->applyFromArray([
        'font' => [
            'size' => 14,
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ],
    ]);
    $sheet->getStyle('A3')->applyFromArray([
        'font' => [
            'italic' => true,
            'size' => 14,
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ],
    ]);

    // Style untuk header (baris 4)
    $sheet->getStyle('A4:' . $highestColumn . '4')->applyFromArray([
        'font' => [
            'bold' => true,
            'size' => 12,
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            'wrapText' => true,
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => '0096FF']
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ],
        ],
    ]);

    // Style untuk seluruh data (termasuk header agar seragam)
    $sheet->getStyle($dataRange)->applyFromArray([
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ],
        ],
    ]);

    return [];
}
}