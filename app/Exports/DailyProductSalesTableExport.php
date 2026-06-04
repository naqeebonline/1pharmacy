<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DailyProductSalesTableExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    public function __construct(
        protected Collection $products,
        protected array $summary,
        protected string $fromDate,
        protected string $toDate
    ) {
    }

    public function title(): string
    {
        return 'Product Sales';
    }

    public function collection(): Collection
    {
        $rows = $this->products->values();

        if ($rows->isEmpty()) {
            return $rows;
        }

        $rows->push((object) [
            'ProductName' => 'TOTAL',
            'gross_quantity' => $this->summary['gross_quantity'] ?? 0,
            'total_returned' => $this->summary['total_returned'] ?? 0,
            'total_quantity_sold' => $this->summary['total_quantity_sold'] ?? 0,
            'total_sale_amount' => $this->summary['total_sale_amount'] ?? 0,
            'total_purchase_amount' => $this->summary['total_purchase_amount'] ?? 0,
            'total_revenue' => $this->summary['total_revenue'] ?? 0,
            'total_cost' => $this->summary['total_cost'] ?? 0,
            'total_discount' => $this->summary['total_discount'] ?? 0,
            'gross_profit' => $this->summary['gross_profit'] ?? 0,
            'total_profit' => $this->summary['total_profit'] ?? 0,
            'profit_margin' => $this->summary['avg_profit_margin'] ?? 0,
            'total_transactions' => $this->summary['total_transactions'] ?? 0,
            'is_total_row' => true,
        ]);

        return $rows;
    }

    public function headings(): array
    {
        return [
            '#',
            'Product Name',
            'Qty Sold',
            'Qty Returned',
            'Net Qty',
            'Total Sale Amount',
            'Total Purchase Amount',
            'Total Revenue',
            'Total Cost',
            'Total Discount',
            'Gross Profit',
            'Net Profit',
            'Profit Margin %',
            'Transactions',
        ];
    }

    public function map($product): array
    {
        static $counter = 0;

        if (empty($product->is_total_row)) {
            $counter++;
        }

        $qtySold = (float) ($product->gross_quantity ?? $product->total_quantity_sold ?? 0)
            - (float) ($product->total_returned ?? 0);

        return [
            empty($product->is_total_row) ? $counter : '',
            $product->ProductName ?? '',
            round($qtySold, 2),
            (float) ($product->total_returned ?? 0),
            (float) ($product->total_quantity_sold ?? 0),
            round((float) ($product->total_sale_amount ?? 0), 2),
            round((float) ($product->total_purchase_amount ?? 0), 2),
            round((float) ($product->total_revenue ?? 0), 2),
            round((float) ($product->total_cost ?? 0), 2),
            round((float) ($product->total_discount ?? 0), 2),
            round((float) ($product->gross_profit ?? 0), 2),
            round((float) ($product->total_profit ?? 0), 2),
            round((float) ($product->profit_margin ?? 0), 2),
            (int) ($product->total_transactions ?? 0),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
