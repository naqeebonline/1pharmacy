<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Facades\DB;

class InventoryExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle, ShouldAutoSize, WithEvents
{
    protected $type;
    protected $data;
    
    public function __construct($type = 'all')
    {
        $this->type = $type;
        $this->loadData();
    }

    protected function loadData()
    {
        try {
            // Get inventory data from GRN details table
            $inventoryQuery = DB::table('grn_details')
                ->join('products', 'grn_details.ProductID', '=', 'products.ProductID')
                ->leftJoin('grn', 'grn_details.GRNID', '=', 'grn.GRNID')
                ->leftJoin('sup_cus_details', 'grn.SCID', '=', 'sup_cus_details.SCID')
                ->leftJoin('generic_names', 'products.generic_name_id', '=', 'generic_names.id')
                ->where('grn_details.ProductStatus', 1) // Only active products
                ->where('grn_details.RemainingQuantity', '>', 0); // Only products with remaining quantity

            // Group by ProductID and sum the RemainingQuantity
            $inventoryData = $inventoryQuery->select(
                    'products.ProductID',
                    'products.ProductName',
                    DB::raw('COALESCE(generic_names.name, "N/A") as generic_name'),
                    DB::raw('SUM(grn_details.RemainingQuantity) as total_available_quantity'),
                    DB::raw('AVG(grn_details.UnitPrice) as average_unit_price'),
                    DB::raw('SUM(grn_details.RemainingQuantity * grn_details.UnitPrice) as total_value'),
                    DB::raw('COUNT(DISTINCT grn_details.GRNID) as purchase_entries'),
                    DB::raw('MIN(grn.Dated) as first_purchase_date'),
                    DB::raw('MAX(grn.Dated) as last_purchase_date')
                )
                ->groupBy('products.ProductID', 'products.ProductName', 'generic_names.name')
                ->having('total_available_quantity', '>', 0)
                ->orderBy('total_available_quantity', 'DESC')
                ->get();

        } catch (\Exception $e) {
            // Fallback query without generic names if there's an issue
            $inventoryQuery = DB::table('grn_details')
                ->join('products', 'grn_details.ProductID', '=', 'products.ProductID')
                ->leftJoin('grn', 'grn_details.GRNID', '=', 'grn.GRNID')
                ->where('grn_details.ProductStatus', 1)
                ->where('grn_details.RemainingQuantity', '>', 0);

            $inventoryData = $inventoryQuery->select(
                    'products.ProductID',
                    'products.ProductName',
                    DB::raw('"N/A" as generic_name'),
                    DB::raw('SUM(grn_details.RemainingQuantity) as total_available_quantity'),
                    DB::raw('AVG(grn_details.UnitPrice) as average_unit_price'),
                    DB::raw('SUM(grn_details.RemainingQuantity * grn_details.UnitPrice) as total_value'),
                    DB::raw('COUNT(DISTINCT grn_details.GRNID) as purchase_entries'),
                    DB::raw('MIN(grn.Dated) as first_purchase_date'),
                    DB::raw('MAX(grn.Dated) as last_purchase_date')
                )
                ->groupBy('products.ProductID', 'products.ProductName')
                ->having('total_available_quantity', '>', 0)
                ->orderBy('total_available_quantity', 'DESC')
                ->get();
        }

        // Filter for low stock if type is 'low_stock'
        if ($this->type === 'low_stock') {
            $inventoryData = $inventoryData->where('total_available_quantity', '<', 10);
        }

        $this->data = $inventoryData;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Transform data for Excel export
        return $this->data->map(function($item, $index) {
            return [
                'sr_no' => $index + 1,
                'product_id' => $item->ProductID,
                'product_name' => $item->ProductName,
                'generic_name' => $item->generic_name,
                'available_quantity' => $item->total_available_quantity,
                'avg_unit_price' => round($item->average_unit_price, 2),
                'total_value' => round($item->total_value, 2),
                'purchase_entries' => $item->purchase_entries,
                'first_purchase_date' => $item->first_purchase_date ? \Carbon\Carbon::parse($item->first_purchase_date)->format('M d, Y') : 'N/A',
                'last_purchase_date' => $item->last_purchase_date ? \Carbon\Carbon::parse($item->last_purchase_date)->format('M d, Y') : 'N/A',
                'stock_status' => $item->total_available_quantity < 10 ? 'Low Stock' : ($item->total_available_quantity > 100 ? 'High Stock' : 'Medium Stock')
            ];
        });
    }

    public function headings(): array
    {
        return [
            'S.No',
            'Product ID',
            'Product Name',
            'Generic Name',
            'Available Quantity',
            'Avg. Unit Price (₨)',
            'Total Value (₨)',
            'Purchase Entries',
            'First Purchase',
            'Last Purchase',
            'Stock Status'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the header row
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => Color::COLOR_WHITE],
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => '2E3B4E'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THICK,
                        'color' => ['argb' => Color::COLOR_WHITE],
                    ],
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,   // S.No
            'B' => 12,  // Product ID
            'C' => 35,  // Product Name
            'D' => 25,  // Generic Name
            'E' => 18,  // Available Quantity
            'F' => 18,  // Avg Unit Price
            'G' => 18,  // Total Value
            'H' => 15,  // Purchase Entries
            'I' => 15,  // First Purchase
            'J' => 15,  // Last Purchase
            'K' => 15,  // Stock Status
        ];
    }

    public function title(): string
    {
        return $this->type === 'low_stock' ? 'Low Stock Report' : 'Complete Inventory Report';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                
                // Add title and metadata at the top
                $sheet->insertNewRowBefore(1, 3);
                
                // Main title
                $sheet->setCellValue('A1', 'Hospital Management System - ' . ($this->type === 'low_stock' ? 'Low Stock Inventory Report' : 'Complete Inventory Report'));
                $sheet->mergeCells('A1:K1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                        'color' => ['argb' => Color::COLOR_DARKBLUE],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                
                // Report metadata
                $sheet->setCellValue('A2', 'Generated on: ' . now()->format('F j, Y g:i A'));
                $sheet->setCellValue('H2', 'Total Records: ' . $this->data->count());
                $sheet->getStyle('A2:K2')->applyFromArray([
                    'font' => ['size' => 11, 'italic' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->mergeCells('A2:G2');
                $sheet->mergeCells('H2:K2');
                
                // Empty row
                $sheet->setCellValue('A3', '');
                
                // Apply borders to all data
                $sheet->getStyle('A4:K' . ($highestRow + 3))->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => Color::COLOR_BLACK],
                        ],
                    ],
                ]);
                
                // Center align numeric columns
                $sheet->getStyle('A4:A' . ($highestRow + 3))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // S.No
                $sheet->getStyle('B4:B' . ($highestRow + 3))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Product ID
                $sheet->getStyle('E4:H' . ($highestRow + 3))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Numeric columns
                $sheet->getStyle('K4:K' . ($highestRow + 3))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Stock Status
                
                // Conditional formatting for stock status
                for ($row = 5; $row <= $highestRow + 3; $row++) {
                    $stockStatus = $sheet->getCell('K' . $row)->getValue();
                    $quantity = $sheet->getCell('E' . $row)->getValue();
                    
                    if (is_numeric($quantity)) {
                        if ($quantity < 10) {
                            // Low stock - Red background
                            $sheet->getStyle('A' . $row . ':K' . $row)->applyFromArray([
                                'fill' => [
                                    'fillType' => Fill::FILL_SOLID,
                                    'startColor' => ['argb' => 'FFFFE6E6'],
                                ],
                            ]);
                            $sheet->getStyle('K' . $row)->applyFromArray([
                                'font' => [
                                    'bold' => true,
                                    'color' => ['argb' => Color::COLOR_DARKRED],
                                ],
                            ]);
                        } elseif ($quantity > 100) {
                            // High stock - Green background
                            $sheet->getStyle('A' . $row . ':K' . $row)->applyFromArray([
                                'fill' => [
                                    'fillType' => Fill::FILL_SOLID,
                                    'startColor' => ['argb' => 'FFE6FFE6'],
                                ],
                            ]);
                            $sheet->getStyle('K' . $row)->applyFromArray([
                                'font' => [
                                    'bold' => true,
                                    'color' => ['argb' => Color::COLOR_DARKGREEN],
                                ],
                            ]);
                        } else {
                            // Medium stock - Yellow background
                            $sheet->getStyle('A' . $row . ':K' . $row)->applyFromArray([
                                'fill' => [
                                    'fillType' => Fill::FILL_SOLID,
                                    'startColor' => ['argb' => 'FFFFF9E6'],
                                ],
                            ]);
                            $sheet->getStyle('K' . $row)->applyFromArray([
                                'font' => [
                                    'bold' => true,
                                    'color' => ['argb' => 'FFD68910'],
                                ],
                            ]);
                        }
                    }
                }
                
                // Add totals row at the bottom
                $totalRow = $highestRow + 4;
                $sheet->setCellValue('A' . $totalRow, 'TOTALS:');
                $sheet->setCellValue('D' . $totalRow, 'Total Products: ' . $this->data->count());
                $sheet->setCellValue('E' . $totalRow, $this->data->sum('total_available_quantity'));
                $sheet->setCellValue('G' . $totalRow, $this->data->sum('total_value'));
                
                $sheet->getStyle('A' . $totalRow . ':K' . $totalRow)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                        'color' => ['argb' => Color::COLOR_DARKBLUE],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFF0F0F0'],
                    ],
                    'borders' => [
                        'top' => [
                            'borderStyle' => Border::BORDER_THICK,
                            'color' => ['argb' => Color::COLOR_DARKBLUE],
                        ],
                    ],
                ]);
                
                // Set row height for header
                $sheet->getRowDimension(1)->setRowHeight(25);
                $sheet->getRowDimension(4)->setRowHeight(20);
            },
        ];
    }
}
