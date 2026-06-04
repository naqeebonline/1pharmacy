<?php

namespace App\Exports;

use App\Models\Customer;
use App\Models\Patient\Patient;
use App\Models\TempSale;
use App\Models\TempSaleDetails;
use App\Http\Controllers\Admin\CustomerPayments;
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

/**
 * Exports an invoice in an Excel layout similar to `print_customer_bill_a4`.
 *
 * Layout (single sheet):
 * - Header/meta rows
 * - Items table with columns similar to A4 invoice rows
 * - Totals rows
 */
class CustomerBillA4ExcelExport implements FromCollection, ShouldAutoSize, WithTitle, WithEvents
{
    public function __construct(private int $saleId)
    {
    }

    private function formatQtyWithPacks(float $qty, float $packSize): string
    {
        $qtyInt = (int) round($qty);
        $packSizeInt = (int) round($packSize);

        if ($qtyInt <= 0) {
            return '0';
        }
        if ($packSizeInt <= 0) {
            return (string) $qtyInt;
        }

        $packs = intdiv($qtyInt, $packSizeInt);
        $units = $qtyInt % $packSizeInt;

        $parts = [];
        if ($packs > 0) {
            $parts[] = $packs . ' Pack';
        }
        if ($units > 0) {
            $parts[] = $units . ' Unit';
        }

        return implode(', ', $parts);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Determine used range
                $highestRow = $sheet->getHighestRow();
                $highestCol = $sheet->getHighestColumn();
                $highestColIndex = Coordinate::columnIndexFromString($highestCol);

                // Page setup
                $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);

                // Default font
                $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

                // Column widths (we use up to 5 columns)
                $sheet->getColumnDimension('A')->setWidth(18);
                $sheet->getColumnDimension('B')->setWidth(45);
                $sheet->getColumnDimension('C')->setWidth(18); // Pack Qty
                $sheet->getColumnDimension('D')->setWidth(12); // Qty
                $sheet->getColumnDimension('E')->setWidth(16); // Unit Price
                $sheet->getColumnDimension('F')->setWidth(18); // Amount

                // Insert a title row at the top by shifting everything down
                $sheet->insertNewRowBefore(1, 1);

                // Title
                $title = 'Customer Bill / Invoice';
                $sheet->setCellValue('A1', $title);
                $sheet->mergeCells('A1:F1');
                $sheet->getRowDimension(1)->setRowHeight(26);
                $sheet->getStyle('A1:F1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F4E79']],
                ]);

                // Style meta rows (A2:B11 approx) - light background, bold labels
                $metaStartRow = 2;
                $metaEndRow = min($highestRow + 1, 12); // +1 because we inserted title row
                $sheet->getStyle('A' . $metaStartRow . ':B' . $metaEndRow)->applyFromArray([
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getStyle('A' . $metaStartRow . ':A' . $metaEndRow)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '0B2239']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8F1FF']],
                ]);
                $sheet->getStyle('B' . $metaStartRow . ':B' . $metaEndRow)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F6FAFF']],
                ]);

                // Find items header row by scanning column A for 'S.No'
                $itemsHeaderRow = null;
                for ($r = 1; $r <= ($highestRow + 1); $r++) {
                    $v = (string) $sheet->getCell('A' . $r)->getValue();
                    if (trim($v) === 'S.No') {
                        $itemsHeaderRow = $r;
                        break;
                    }
                }

                if ($itemsHeaderRow) {
                    // Header style
                    $sheet->getStyle('A' . $itemsHeaderRow . ':F' . $itemsHeaderRow)->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2F5597']],
                    ]);
                    $sheet->getRowDimension($itemsHeaderRow)->setRowHeight(20);

                    // Items data range until a blank row or until 'Total Amount'
                    $itemsStartRow = $itemsHeaderRow + 1;
                    $itemsEndRow = $itemsStartRow;
                    for ($r = $itemsStartRow; $r <= ($highestRow + 1); $r++) {
                        $a = (string) $sheet->getCell('A' . $r)->getValue();
                        $b = (string) $sheet->getCell('B' . $r)->getValue();
                        if (trim($a) === '' && trim($b) === '') {
                            $itemsEndRow = $r - 1;
                            break;
                        }
                        if (trim($a) === 'Total Amount') {
                            $itemsEndRow = $r - 1;
                            break;
                        }
                        $itemsEndRow = $r;
                    }

                    if ($itemsEndRow >= $itemsStartRow) {
                        // Borders + zebra striping
                        $sheet->getStyle('A' . $itemsHeaderRow . ':F' . $itemsEndRow)->applyFromArray([
                            'borders' => [
                                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9AA0A6']],
                            ],
                        ]);

                        for ($r = $itemsStartRow; $r <= $itemsEndRow; $r++) {
                            if ((($r - $itemsStartRow) % 2) === 0) {
                                $sheet->getStyle('A' . $r . ':E' . $r)->applyFromArray([
                                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F7F9FC']],
                                ]);
                            }
                        }

                        // Alignment
                        $sheet->getStyle('A' . $itemsStartRow . ':A' . $itemsEndRow)
                            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        // C (Pack Qty) is text; D-F are numeric
                        $sheet->getStyle('D' . $itemsStartRow . ':F' . $itemsEndRow)
                            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                        // Number formats: Qty numeric, Unit price & Amount numeric
                        $sheet->getStyle('D' . $itemsStartRow . ':D' . $itemsEndRow)
                            ->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
                        $sheet->getStyle('E' . $itemsStartRow . ':F' . $itemsEndRow)
                            ->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
                    }

                    // Totals styling (scan for Total Amount row)
                    $totalsRow = null;
                    for ($r = $itemsEndRow + 1; $r <= ($highestRow + 1); $r++) {
                        $a = (string) $sheet->getCell('A' . $r)->getValue();
                        if (trim($a) === 'Total Amount') {
                            $totalsRow = $r;
                            break;
                        }
                    }
                    if ($totalsRow) {
                        $totalsEnd = $totalsRow + 2; // Total Amount, Discount, Net Total
                        $sheet->getStyle('A' . $totalsRow . ':B' . $totalsEnd)->applyFromArray([
                            'font' => ['bold' => true],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF2CC']],
                            'borders' => [
                                'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D6B656']],
                            ],
                        ]);
                        $sheet->getStyle('B' . $totalsRow . ':B' . $totalsEnd)
                            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                        $sheet->getStyle('B' . $totalsRow . ':B' . $totalsEnd)
                            ->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
                    }
                }

                // Freeze panes under title + meta section
                $sheet->freezePane('A13');

                // Print area with some padding
                $sheet->getPageMargins()->setTop(0.3)->setRight(0.3)->setLeft(0.3)->setBottom(0.3);
                $sheet->setPrintGridlines(false);

                // Fit to width
                $sheet->getPageSetup()->setFitToWidth(1)->setFitToHeight(0);
            },
        ];
    }

    public function title(): string
    {
        return 'Invoice';
    }

    public function collection(): Collection
    {
        $record = TempSale::with(['created_by'])->where(['SaleID' => $this->saleId])->first();
        if (!$record) {
            return collect([['Sale record not found']]);
        }

        $patient = Patient::where(['id' => $record->patient_id])->first();

        $appointmentPatientName = 'Name: Walking Customer';
        if ($record->admission_id) {
            $appointmentPatientName = $patient ? ('Customer Name: ' . $patient->name) : '';
        }

        $customerId = $record->SCID;
        $discountPercentage = (float) ($record->discount_percentage ?? 0);

        $details = TempSaleDetails::with('product')->where(['SaleID' => $this->saleId])->get();

        $return = 'No';
        $totalAmount = 0.0;
    $totalDiscount = 0.0;

        // Same previous balance logic as A4
        $prevBalance = (new CustomerPayments())->customer_previous_balance($customerId, '');

        foreach ($details as $rec) {
            $rec->AvaliableQuantity = max(0, ($rec->Quantity ?? 0) - ($rec->ReturnQuantity ?? 0));
            $rec->totalAmount = ($rec->AvaliableQuantity) * ($rec->UnitePrice ?? 0);

            // Discount is stored in TempSaleDetails.discount_percentage_amount
            // Sum it proportionally to available quantity (same approach used elsewhere in prints).
            $detailDiscount = 0.0;
            if ($rec->AvaliableQuantity > 0 && isset($rec->discount_percentage_amount) && (float) $rec->discount_percentage_amount > 0) {
                $proportion = $rec->AvaliableQuantity / max(1, (float) ($rec->Quantity ?? 0));
                $detailDiscount = (float) $rec->discount_percentage_amount * $proportion;
            }
            $totalDiscount += $detailDiscount;

            if ($rec->AvaliableQuantity > 0) {
                if (isset($rec->discount_percentage_amount) && $rec->discount_percentage_amount > 0) {
                    $proportion = $rec->AvaliableQuantity / max(1, ($rec->Quantity ?? 0));
                    $rec->itemDiscountAmount = $rec->discount_percentage_amount * $proportion;
                } elseif (isset($rec->discount_percentage) && $rec->discount_percentage > 0) {
                    $rec->itemDiscountAmount = ($rec->totalAmount * $rec->discount_percentage) / 100;
                } else {
                    $rec->itemDiscountAmount = 0;
                }
            } else {
                $rec->itemDiscountAmount = 0;
            }

            // Item-level/net line calculation (kept for reference if needed)
            $rec->totalAmountAfterDiscount = max(0, $rec->totalAmount - $rec->itemDiscountAmount);

            // IMPORTANT:
            // The A4 report applies the overall sale discount % on the gross (after returns) amount.
            // So TotalAmount here must be GROSS before overall discount, otherwise discount is subtracted twice.
            $totalAmount += (float) $rec->totalAmount;

            if (($rec->ReturnQuantity ?? 0) > 0) {
                $return = 'Yes';
            }
        }

        // Same duplicate merge logic as A4 (for print only)
        $result = [];
        foreach ($details as $item) {
            $productId = $item->ProductID;
            if (isset($result[$productId])) {
                $result[$productId]->Quantity += ($item->Quantity ?? 0);
                $result[$productId]->totalAmount += ($item->totalAmount ?? 0);
                $result[$productId]->taxAmount += ($item->taxAmount ?? 0);
            } else {
                $result[$productId] = clone $item;
            }
        }
        $items = array_values($result);

        $netTotal = max(0, $totalAmount - $totalDiscount);

        $customer = Customer::where('SCID', $customerId)->first();

        // Build rows
        $rows = [];

        // Meta / Header
        
        $rows[] = ['SaleID', $this->saleId];
        $rows[] = ['Invoice No', $record->InvoiceNo ?? ''];
        $rows[] = ['Refrence#', $record->sale_descriptions ?? ''];
        $rows[] = ['Date', $record->CreatedAt ?? ''];
        $rows[] = ['Customer', $customer?->Name ?? ($patient?->name ?? '')];
        $rows[] = ['MR#', $patient?->mr_no ?? ''];
        $rows[] = ['Medicine Type', $record->medicine_type ?? ''];
        $rows[] = ['Created By', $record->created_by?->name ?? ''];
        $rows[] = ['Previous Balance', (string) $prevBalance];
        $rows[] = ['Return', $return];
        $rows[] = [''];

        // Items header
    $rows[] = ['S.No', 'Product', 'Pack Qty', 'Unit Qty', 'Unit Price', 'Amount'];

        $i = 1;
        foreach ($items as $it) {
            $qty = (float) ($it->AvaliableQuantity ?? max(0, ($it->Quantity ?? 0) - ($it->ReturnQuantity ?? 0)));
            $unitPrice = (float) ($it->UnitePrice ?? 0);
            // Keep line amount as gross (after returns), overall discount is applied only once in totals
            $amount = (float) (($it->totalAmount ?? 0) ?: ($qty * $unitPrice));

            $packSize = (float) ($it->product?->pack_size ?? 0);
            $packQtyDisplay = $this->formatQtyWithPacks($qty, $packSize);

            $rows[] = [
                $i++,
                $it->product?->ProductName ?? '',
                $packQtyDisplay,
                $qty,
                number_format($unitPrice, 2, '.', ''),
                number_format($amount, 2, '.', ''),
            ];
        }

        // Totals
        $rows[] = [''];
        $rows[] = ['Total Amount', number_format($totalAmount, 2, '.', '')];
    $rows[] = ['Discount', number_format($totalDiscount, 2, '.', '')];
        $rows[] = ['Net Total', number_format($netTotal, 2, '.', '')];

        // Extra (matches A4 page showing customer label)
        if (!empty($appointmentPatientName)) {
            $rows[] = [''];
            $rows[] = ['Note', $appointmentPatientName];
        }

        return collect($rows);
    }
}
