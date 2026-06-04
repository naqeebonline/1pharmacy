<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ProfitAndLossExcelExport implements FromCollection, WithTitle, WithEvents, ShouldAutoSize
{
    public function __construct(
        private array $incomeItems,
        private array $expenseItems,
        private float $totalIncome,
        private float $totalExpense,
        private float $netProfit,
        private string $startDate,
        private string $endDate,
        private string $companyName
    ) {
    }

    public function title(): string
    {
        return 'Profit & Loss';
    }

    public function collection(): Collection
    {
        $rows = [];

        // Title block (styled via AfterSheet)
        $rows[] = ['Profit and Loss Report'];
        $rows[] = [$this->companyName];
        $rows[] = ['Period', $this->startDate . ' to ' . $this->endDate];
        $rows[] = [''];

        // Income section
        $rows[] = ['Income'];
        $rows[] = ['Head', 'Amount'];
        foreach ($this->incomeItems as $item) {
            $rows[] = [$item['name'] ?? '', (float) ($item['amount'] ?? 0)];
        }
        $rows[] = ['Total Income', (float) $this->totalIncome];
        $rows[] = [''];

        // Expense section
        $rows[] = ['Expenses'];
        $rows[] = ['Head', 'Amount'];
        foreach ($this->expenseItems as $item) {
            $rows[] = [$item['name'] ?? '', (float) ($item['amount'] ?? 0)];
        }
        $rows[] = ['Total Expenses', (float) $this->totalExpense];
        $rows[] = [''];

        // Net
        $rows[] = ['Net ' . ($this->netProfit >= 0 ? 'Profit' : 'Loss'), (float) $this->netProfit];

        return collect($rows);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Layout / widths
                $sheet->getColumnDimension('A')->setWidth(45);
                $sheet->getColumnDimension('B')->setWidth(18);

                // Title styling
                $sheet->mergeCells('A1:B1');
                $sheet->mergeCells('A2:B2');
                $sheet->getRowDimension(1)->setRowHeight(28);
                $sheet->getStyle('A1:B2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F4E79']],
                ]);

                // Period row
                $sheet->getStyle('A3:B3')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '0B2239']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8F1FF']],
                    'borders' => [
                        'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9AA0A6']],
                    ],
                ]);

                // Find section rows
                $highestRow = $sheet->getHighestRow();

                $incomeTitleRow = null;
                $expenseTitleRow = null;
                $netRow = null;

                for ($r = 1; $r <= $highestRow; $r++) {
                    $a = trim((string) $sheet->getCell('A' . $r)->getValue());
                    if ($a === 'Income') $incomeTitleRow = $r;
                    if ($a === 'Expenses') $expenseTitleRow = $r;
                    if (str_starts_with($a, 'Net ')) $netRow = $r;
                }

                // Helper to style a section
                $styleSection = function (int $titleRow, string $headerColor, string $totalColor) use ($sheet, $highestRow) {
                    // Title row
                    $sheet->mergeCells('A' . $titleRow . ':B' . $titleRow);
                    $sheet->getStyle('A' . $titleRow . ':B' . $titleRow)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $headerColor]],
                    ]);

                    // Table header is next row
                    $headerRow = $titleRow + 1;
                    $sheet->getStyle('A' . $headerRow . ':B' . $headerRow)->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2F5597']],
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9AA0A6']],
                        ],
                    ]);

                    // Data rows until blank row
                    $start = $headerRow + 1;
                    $end = $start;
                    for ($r = $start; $r <= $highestRow; $r++) {
                        $a = trim((string) $sheet->getCell('A' . $r)->getValue());
                        $b = trim((string) $sheet->getCell('B' . $r)->getValue());
                        if ($a === '' && $b === '') {
                            $end = $r - 1;
                            break;
                        }
                        $end = $r;
                    }

                    if ($end >= $start) {
                        // Borders
                        $sheet->getStyle('A' . $headerRow . ':B' . $end)->applyFromArray([
                            'borders' => [
                                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9AA0A6']],
                            ],
                        ]);

                        // Zebra striping for rows excluding last total
                        for ($r = $start; $r <= $end; $r++) {
                            if ((($r - $start) % 2) === 0) {
                                $sheet->getStyle('A' . $r . ':B' . $r)->applyFromArray([
                                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F7F9FC']],
                                ]);
                            }
                        }

                        // Total row (last row)
                        $sheet->getStyle('A' . $end . ':B' . $end)->applyFromArray([
                            'font' => ['bold' => true],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $totalColor]],
                        ]);

                        // Amount formatting
                        $sheet->getStyle('B' . $start . ':B' . $end)
                            ->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
                        $sheet->getStyle('B' . $start . ':B' . $end)
                            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    }
                };

                if ($incomeTitleRow) {
                    $styleSection($incomeTitleRow, '00A65A', 'DFF0D8'); // green
                }
                if ($expenseTitleRow) {
                    $styleSection($expenseTitleRow, 'DD4B39', 'F2DEDE'); // red
                }

                // Net row styling
                if ($netRow) {
                    $sheet->getStyle('A' . $netRow . ':B' . $netRow)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => ($this->netProfit >= 0 ? '00A65A' : 'DD4B39')],
                        ],
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['rgb' => '1F4E79']],
                        ],
                    ]);
                    $sheet->getStyle('B' . $netRow)
                        ->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
                }

                // Freeze top rows
                $sheet->freezePane('A5');

                // Fit to width
                $sheet->getPageSetup()->setFitToWidth(1)->setFitToHeight(0);
            },
        ];
    }
}
