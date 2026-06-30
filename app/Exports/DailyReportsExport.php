<?php

namespace App\Exports;

use App\Models\DailyReport;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class DailyReportsExport
{
    protected $startDate;
    protected $endDate;
    protected $restaurantIds;
    protected $exportAllDates;

    public function __construct($startDate, $endDate, $restaurantIds = null, $exportAllDates = false)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->exportAllDates = $exportAllDates;
        
        // Accept single ID or array of IDs
        if (is_numeric($restaurantIds)) {
            $this->restaurantIds = [$restaurantIds];
        } elseif (is_array($restaurantIds)) {
            $this->restaurantIds = $restaurantIds;
        } else {
            $this->restaurantIds = null;
        }
    }

    public function export()
    {
        $query = DailyReport::with(['restaurant', 'user', 'details', 'approver'])
            ->orderBy('date', 'desc')
            ->orderBy('restaurant_id', 'asc');

        // Only add date filter if NOT exporting all dates
        if (!$this->exportAllDates && $this->startDate && $this->endDate) {
            $query->whereBetween('date', [$this->startDate, $this->endDate]);
        }

        if ($this->restaurantIds) {
            $query->whereIn('restaurant_id', $this->restaurantIds);
        }

        $reports = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Daily Reports');

        $headers = [
            'Report Date',
            'Restaurant',
            'Session',
            'Revenue Food',
            'Revenue Beverage',
            'Revenue Others',
            'Revenue Event',
            'Total Revenue',
            'Total Cover',
            'Staff on Duty',
            'Remarks',
            'Status',
            'Created By',
            'Approved By',
        ];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ]);
            $col++;
        }

        $row = 2;
        foreach ($reports as $report) {
            foreach ($report->details as $detail) {
                $totalRevenue = $detail->revenue_food + $detail->revenue_beverage 
                              + $detail->revenue_others + $detail->revenue_event;

                $totalCover = 0;
                if (!empty($detail->cover_data) && is_array($detail->cover_data)) {
                    foreach ($detail->cover_data as $val) {
                        if (is_numeric($val)) {
                            $totalCover += $val;
                        }
                    }
                }

                $staffNames = '';
                if (!empty($detail->staff_on_duty) && is_array($detail->staff_on_duty)) {
                    $staffNames = implode(', ', $detail->staff_on_duty);
                }

                $sheet->setCellValue('A' . $row, $report->date->format('d M Y'));
                $sheet->setCellValue('B' . $row, $report->restaurant->name);
                $sheet->setCellValue('C' . $row, ucfirst($detail->session_type));
                $sheet->setCellValue('D' . $row, number_format($detail->revenue_food, 0, ',', '.'));
                $sheet->setCellValue('E' . $row, number_format($detail->revenue_beverage, 0, ',', '.'));
                $sheet->setCellValue('F' . $row, number_format($detail->revenue_others, 0, ',', '.'));
                $sheet->setCellValue('G' . $row, number_format($detail->revenue_event, 0, ',', '.'));
                $sheet->setCellValue('H' . $row, number_format($totalRevenue, 0, ',', '.'));
                $sheet->setCellValue('I' . $row, $totalCover);
                $sheet->setCellValue('J' . $row, $staffNames);
                $sheet->setCellValue('K' . $row, $detail->remarks ?? '-');
                $sheet->setCellValue('L' . $row, ucfirst($report->status));
                $sheet->setCellValue('M' . $row, $report->user->name);
                $sheet->setCellValue('N' . $row, $report->approver->name ?? '-');

                $row++;
            }
        }

        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ]
        ];
        $sheet->getStyle('A1:N' . ($row - 1))->applyFromArray($styleArray);

        $writer = new Xlsx($spreadsheet);
        
        // Generate filename based on export type
        if ($this->exportAllDates) {
            $filename = 'Daily_Reports_All_Historical_Data_' . date('Y-m-d_His') . '.xlsx';
        } else {
            $filename = 'Daily_Reports_' . $this->startDate . '_to_' . $this->endDate . '.xlsx';
        }
        
        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($tempFile);

        return [
            'file' => $tempFile,
            'filename' => $filename
        ];
    }
}
