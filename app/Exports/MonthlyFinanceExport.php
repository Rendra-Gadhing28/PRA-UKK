<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MonthlyFinanceExport implements FromView, ShouldAutoSize, WithStyles, WithTitle
{
    use Exportable;

    protected Collection $bookings;

    protected Carbon $month;

    protected float $income;

    protected float $expense;

    public function __construct(Collection $bookings, Carbon $month, float $income, float $expense)
    {
        $this->bookings = $bookings;
        $this->month = $month;
        $this->income = $income;
        $this->expense = $expense;
    }

    public function view(): View
    {
        return view('exports.monthly_finance_excel', [
            'bookings' => $this->bookings,
            'month' => $this->month,
            'income' => $this->income,
            'expense' => $this->expense,
            'netProfit' => $this->income - $this->expense,
            'downloadedAt' => now(),
        ]);
    }

    public function title(): string
    {
        return 'Laporan Bulanan '.$this->month->format('M Y');
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = max(8, 7 + $this->bookings->count());
        $summaryRow = $lastRow + 1;

        // Auto filter on table header
        $sheet->setAutoFilter('A7:L7');

        // Freeze panes
        $sheet->freezePane('A8');

        // Style table header (Row 7)
        $sheet->getStyle('A7:L7')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 10,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F45472'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);
        $sheet->getRowDimension(7)->setRowHeight(28);

        // Style table data rows
        if ($this->bookings->count() > 0) {
            $sheet->getStyle('A7:L'.$summaryRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'E2E8F0'],
                    ],
                ],
            ]);

            // Alignments & Number formats
            $sheet->getStyle('A8:A'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B8:B'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D8:D'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D8:D'.$lastRow)->getNumberFormat()->setFormatCode('@');
            $sheet->getStyle('G8:G'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('H8:H'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('I8:I'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('J8:J'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('J8:J'.$lastRow)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('K8:K'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('L8:L'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Zebra striping
            for ($row = 8; $row <= $lastRow; $row++) {
                if ($row % 2 === 0) {
                    $sheet->getStyle('A'.$row.':L'.$row)->getFill()->applyFromArray([
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFF9FA'],
                    ]);
                }
                $sheet->getRowDimension($row)->setRowHeight(22);
            }
        }

        // Style summary row
        $sheet->getStyle('A'.$summaryRow.':L'.$summaryRow)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '9F1239'],
                'size' => 10,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFE4E6'],
            ],
        ]);
        $sheet->getStyle('J'.$summaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('J'.$summaryRow)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getRowDimension($summaryRow)->setRowHeight(25);

        return [];
    }
}
