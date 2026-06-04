<?php

namespace App\Exports;

use App\Models\TempSale;
use App\Models\TempSaleDetails;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomerBillInvoiceExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(private int $saleId)
    {
    }

    public function collection(): Collection
    {
        // Export line items (invoice-style excel)
        return TempSaleDetails::with(['product'])
            ->where('SaleID', $this->saleId)
            ->get();
    }

    public function headings(): array
    {
        return [
            'SaleID',
            'InvoiceNo',
            'Date',
            'Customer',
            'MR#',
            'Medicine Type',
            'Product',
            'BatchNo',
            'Expiry',
            'Qty',
            'UnitPrice',
            'Amount',
        ];
    }

    public function map($row): array
    {
        $sale = TempSale::with(['patient'])->where('SaleID', $this->saleId)->first();

        // Fallbacks are defensive (old data / missing relations)
        $invoiceNo = $sale?->InvoiceNo ?? '';
        $date = $sale?->CreatedAt ?? '';
        $customerName = $sale?->patient?->name ?? '';
        $mrNo = $sale?->patient?->mr_no ?? '';
        $medicineType = $sale?->medicine_type ?? '';

        $qty = (float) ($row->Quantity ?? 0);
        $retQty = (float) ($row->ReturnQuantity ?? 0);
        $activeQty = max(0, $qty - $retQty);

        $unitPrice = (float) ($row->UnitePrice ?? 0);
        $amount = $activeQty * $unitPrice;

        return [
            $this->saleId,
            $invoiceNo,
            $date,
            $customerName,
            $mrNo,
            $medicineType,
            $row->product?->ProductName ?? '',
            $row->BatchNo ?? '',
            $row->ExpiryDate ?? '',
            $activeQty,
            number_format($unitPrice, 2, '.', ''),
            number_format($amount, 2, '.', ''),
        ];
    }
}
