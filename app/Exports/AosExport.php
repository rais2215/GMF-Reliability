<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class AosExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
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
                'Days In Service' => $metrics['daysInService'],
                'Flying Hours - Total' => number_format($metrics['flyingHoursTotal'], 2),
                'Revenue Flying Hours' => number_format($metrics['revenueFlyingHours'], 2),
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
            'Period',
            'A/C In Fleet',
            'A/C In Service',
            'Days In Service',
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
            'Dispatch Reliability (%)',
        ];
    }

    public function title(): string
    {
        return 'AOS Report - ' . $this->aircraftType . ' - ' . $this->period;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row (headings)
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D9D9D9']
                ],
            ],
        ];
    }
}