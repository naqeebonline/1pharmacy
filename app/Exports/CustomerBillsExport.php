<?php

namespace App\Exports;

use App\Models\Sale;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomerBillsExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(
        private readonly string $invoiceNo = '',
        private readonly int $patientId = 0,
        private readonly ?string $fromDate = null,
        private readonly ?string $toDate = null,
        private readonly string $medicineType = ''
    ) {
    }

    public function query(): Builder
    {
        $query = Sale::query()
            ->with(['patient:id,name,mr_no'])
            ->when(session('store_id'), function ($q) {
                $q->where('store_id', session('store_id'));
            });

        if ($this->invoiceNo !== '') {
            $query->where('InvoiceNo', 'like', '%' . $this->invoiceNo . '%');
        }
        if ($this->patientId > 0) {
            $query->where('patient_id', $this->patientId);
        }
        if (!empty($this->fromDate)) {
            $query->whereDate('CreatedAt', '>=', $this->fromDate);
        }
        if (!empty($this->toDate)) {
            $query->whereDate('CreatedAt', '<=', $this->toDate);
        }
        if ($this->medicineType !== '') {
            $query->where('medicine_type', $this->medicineType);
        }

        return $query->orderByDesc('SaleID');
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
            'TotalSale',
            'Discount',
            'Received',
        ];
    }

    /**
     * @param  mixed  $row
     */
    public function map($row): array
    {
        return [
            $row->SaleID,
            $row->InvoiceNo,
            $row->CreatedAt,
            $row->patient?->name ?? '',
            $row->patient?->mr_no ?? '',
            $row->medicine_type ?? '',
            $row->TotalSale,
            $row->Discount,
            $row->ReceivedAmountFromCustomer,
        ];
    }
}
