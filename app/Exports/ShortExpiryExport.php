<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Carbon\Carbon;

class ShortExpiryExport implements FromCollection, WithHeadings, WithStyles, WithColumnFormatting, WithMapping
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return collect($this->data);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'S.No',
            'Product Name',
            'Batch Number',
            'Supplier',
            'Category',
            'Expiry Date',
            'Days Left',
            'Current Stock',
            'Purchase Price',
            'Sale Price',
            'Total Value',
            'Status'
        ];
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        static $counter = 0;
        $counter++;

        $daysLeft = $row->days_left;

        // Determine status
        if ($daysLeft < 0) {
            $status = 'Expired';
        } elseif ($daysLeft <= 7) {
            $status = 'Critical';
        } elseif ($daysLeft <= 30) {
            $status = 'Warning';
        } else {
            $status = 'Good';
        }

        return [
            $counter,
            $row->product_name,
            $row->batch_number ?: 'N/A',
            $row->supplier_name,
            $row->category_name,
            Carbon::parse($row->expiry_date)->format('d-M-Y'),
            $daysLeft,
            number_format($row->current_stock),
            number_format($row->purchase_price, 2),
            number_format($row->sale_price, 2),
            number_format($row->total_value, 2),
            $status
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1e40af']
                ],
                'font' => ['color' => ['argb' => 'FFFFFFFF'], 'bold' => true]
            ],
        ];
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_DATE_DDMMYYYY, // Expiry Date
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1, // Current Stock
            'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE, // Purchase Price
            'J' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE, // Sale Price
            'K' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE, // Total Value
        ];
    }
}
