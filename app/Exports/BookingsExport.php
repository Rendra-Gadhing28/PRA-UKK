<?php

namespace App\Exports;

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

class BookingsExport implements FromView, ShouldAutoSize, WithStyles, WithTitle
{
    use Exportable;

    protected Collection $bookings;

    protected array $filters;

    public function __construct(Collection $bookings, array $filters = [])
    {
        $this->bookings = $bookings;
        $this->filters = $filters;
    }

    public function view(): View
    {
        $totalAmount = $this->bookings->sum('total_amount');

        return view('exports.bookings_excel', [
            'bookings' => $this->bookings,
            'filters' => $this->filters,
            'totalAmount' => $totalAmount,
            'downloadedAt' => now(),
        ]);
    }

    public function title(): string
    {
        return 'Laporan Reservasi';
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = max(7, 6 + $this->bookings->count());
        $summaryRow = $lastRow + 1;

        // Auto filter on table header
        $sheet->setAutoFilter('A6:L6');

        // Freeze panes so header rows stay visible when scrolling
        $sheet->freezePane('A7');

        // Style table header (Row 6)
        $sheet->getStyle('A6:L6')->applyFromArray([
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
        $sheet->getRowDimension(6)->setRowHeight(28);

        // Style table data rows
        if ($this->bookings->count() > 0) {
            // Borders on data table
            $sheet->getStyle('A6:L'.$summaryRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'E2E8F0'],
                    ],
                ],
            ]);

            // Alignment & Number format for data rows
            $sheet->getStyle('A7:A'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B7:B'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D7:D'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D7:D'.$lastRow)->getNumberFormat()->setFormatCode('@'); // text format for phone
            $sheet->getStyle('G7:G'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('H7:H'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('I7:I'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('J7:J'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('J7:J'.$lastRow)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('K7:K'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('L7:L'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Zebra striping for even data rows
            for ($row = 7; $row <= $lastRow; $row++) {
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
