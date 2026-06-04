<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Invoice - A4</title>
    <script>
        // Open print dialog after the full page has loaded (more reliable than DOMContentLoaded for print layout).
        window.addEventListener('load', function () {
            setTimeout(function () {
                window.print();
            }, 300);
        });
    </script>
    <style>
        @page {
            size: A4;
            margin: 8mm 10mm;
        }

        html,
        body {
            height: auto;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #111;
            font-size: 9px;
            line-height: 1.25;
        }

        * {
            box-sizing: border-box;
        }

        .invoice {
            width: 100%;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #111;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .head-details {
            width: 100%;
            margin-top: 8px;
            border-top: 1px solid #ddd;
            padding-top: 8px;
            display: flex;
            gap: 12px;
        }

        .head-details .box {
            flex: 1;
        }

        .head-details .box .label {
            color: #444;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .3px;
            margin-bottom: 4px;
        }

        .head-details .box .value {
            color: #111;
            font-size: 12px;
        }

        .brand h1 {
            font-size: 15px;
            margin: 0;
        }

        .brand .branch {
            margin-top: 3px;
            font-weight: 700;
        }

        .meta {
            text-align: right;
            min-width: 260px;
        }

        .meta .title {
            font-size: 12px;
            font-weight: 700;
            margin: 0 0 4px 0;
        }

        .meta table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta td {
            padding: 1px 0;
            vertical-align: top;
        }

        .meta td:first-child {
            color: #444;
            width: 45%;
            padding-right: 8px;
        }

        /* Old big Customer/Balance cards removed to save space */

        .muted {
            color: #555;
        }

        .items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            table-layout: fixed;
        }

        /* Fixed column widths — compact layout for more rows per page */
        .col-sno { width: 3%; }
        .col-name { width: 17%; }
        .col-pack { width: 7%; }
        .col-unit { width: 6%; }
        .col-price { width: 8%; }
        .col-gross { width: 9%; }
        .col-disc { width: 5%; }
        .col-stax { width: 7%; }
        .col-itax { width: 7%; }
        .col-net { width: 11%; }

        .product-name {
            font-weight: 600;
            display: block;
            font-size: 10px;
            line-height: 1.25;
            word-break: break-word;
            white-space: normal;
        }

        .items thead th {
            text-align: left;
            padding: 4px 3px;
            background: #f5f5f5;
            border-bottom: 1px solid #bbb;
            font-size: 9px;
            vertical-align: middle;
            line-height: 1.2;
        }

        /* Make numeric headers right-aligned exactly like numeric cells */
        .items thead th.num {
            text-align: right;
        }

        .items thead th,
        .items tbody td {
            overflow: hidden;
        }

        .items tbody td {
            padding: 4px 3px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
            font-size: 10px;
        }

        .items tfoot td {
            padding: 6px;
        }

        .num {
            text-align: right;
            white-space: nowrap;
        }

        .totals {
            margin-top: 10px;
            display: flex;
            justify-content: flex-end;
        }

        .totals table {
            width: 360px;
            border-collapse: collapse;
        }

        .totals {
            font-size: 11px;
        }

        .totals td {
            padding: 2px 0;
        }

        .totals td:first-child {
            color: #444;
        }

        .totals tr.total td {
            font-size: 14px;
            font-weight: 700;
            border-top: 2px solid #111;
            padding-top: 8px;
        }

        .footer {
            margin-top: 14px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 12px;
        }

        .notes {
            font-size: 11px;
            color: #444;
        }

        @media print {
            html,
            body {
                height: auto !important;
                min-height: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
                overflow: visible !important;
            }

            /* Make sure nothing adds a forced extra page */
            .invoice {
                page-break-after: avoid;
            }

            a {
                text-decoration: none;
                color: inherit;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    @php
        /**
         * Convert unit quantity to "{packs} Pack {units} Unit" based on pack size.
         * Example: qty=202, packSize=100 => "2 Pack 2 Unit".
         */
        $formatPackQty = function ($qty, $packSize) {
            $qty = (int) ($qty ?? 0);
            $packSize = (int) ($packSize ?? 0);
            if ($qty <= 0) {
                return '0';
            }
            if ($packSize <= 0) {
                return '-';
            }
            return (string) intdiv($qty, $packSize);
        };

        $taxAmount = 0;
        $totalAmountBeforeDiscount = 0;
        $totalDiscountAmount = 0;
        $finalTotal = 0;

        foreach ($data as $d) {
            $taxAmount += $d->taxAmount;
            $quantity = max(0, $d->Quantity - $d->ReturnQuantity);
            $discountPercentage = $d->discount_percentage ?? 0;

            $lineAmountBeforeDiscount = max(0, $quantity * $d->UnitePrice);
            $totalAmountBeforeDiscount += $lineAmountBeforeDiscount;

            $itemDiscountAmount = 0;
            if ($quantity > 0) {
                if (isset($d->discount_percentage_amount) && $d->discount_percentage_amount > 0) {
                    $itemDiscountAmount = $d->discount_percentage_amount;
                } else if (isset($d->itemDiscountAmount) && $d->itemDiscountAmount > 0) {
                    $itemDiscountAmount = $d->itemDiscountAmount;
                } else if ($discountPercentage > 0) {
                    $itemDiscountAmount = ($lineAmountBeforeDiscount * $discountPercentage) / 100;
                }
            }
            $totalDiscountAmount += max(0, $itemDiscountAmount);
        }

        $invoiceDiscount = $record->invoice_discount ?? 0;
        $amountAfterDiscount = $totalAmountBeforeDiscount - $totalDiscountAmount - $invoiceDiscount;
        $amountAfterDiscount = round((float) $amountAfterDiscount);
        

        

        $saleTaxLines = $sale_tax_lines ?? [];
        $totalSaleTaxAmount = (float) ($total_row_tax_amount ?? 0);
        if ($totalSaleTaxAmount <= 0 && !empty($saleTaxLines)) {
            $totalSaleTaxAmount = array_sum(array_column($saleTaxLines, 'amount'));
        }
        $storedTaxAmount = (float) ($record->tax_amount ?? 0);
        $netTotal = $amountAfterDiscount + ($storedTaxAmount > 0 ? $storedTaxAmount : $totalSaleTaxAmount);
    @endphp

    <div class="invoice">
        <div class="header">
            <div class="brand">
                <h1>{{ config('app.COMPANY_NAME') }}</h1>
                <div class="branch">{{ config('app.BRANCH_NAME') }}</div>
                <div class="" style="margin-top: 2px; font-weight: bold;">@if(!empty($appointment_patient_name))
                        Name: {{$patient->name ?? ""}}
                    @else
                        Name: Walking Customer
                    @endif</div>
                    <div class="branch">
                        @if($patient && $patient->cnic !='')
                            NTN No: {{ $patient->cnic }}
                        @else
                        
                        @endif
                    </div>
                <div class="muted" style="margin-top: 2px;">Printed: {{ date('d-m-Y h:i A') }}</div>
            </div>

            <div class="meta">
                <div class="title">Sales Invoice</div>
                <table>
                    <tr>
                        <td>Invoice #</td>
                        <td class="num">{{ $record->InvoiceNo ?? '' }}</td>
                    </tr>
                    @if($record->sale_descriptions)
                    <tr>
                        <td>Refrence #</td>
                        <td class="num">{{ $record->sale_descriptions ?? '' }}</td>
                    </tr>
                    @endif
                    
                    <tr>
                        <td>Created At</td>
                        <td class="num">{{ $record->CreatedAt ?? '' }}</td>
                    </tr>
                     
                    <tr>
                        <td>Printed By</td>
                        <td class="num">{{ auth()->user()->name ?? '' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="head-details">
            <div class="box">
                
                <div class="value muted">
                    
                </div>
            </div>
            <div class="box" style="max-width: 280px;">
                 
                
            </div>
        </div>

        <table class="items">
            <thead>
                <tr>
                    <th class="col-sno">#</th>
                    <th class="col-name">Product Name</th>
                    <th class="col-pack num">Pack Qty</th>
                    <th class="col-unit num">Unit Qty</th>
                    <th class="col-price num">Unit Price</th>
                    <th class="col-gross num">Gross</th>
                    <th class="col-disc num">Disc</th>
                    <th class="col-stax num">Sale Tax %</th>
                    <th class="col-itax num">Inc.Tax %</th>
                    <th class="col-net num">Net Amount</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $i = 1;
                    $totalRowSaleTax = 0;
                    $totalRowIncomeTax = 0;
                @endphp
                @foreach($data as $d)
                    @php
                        $quantity = max(0, $d->Quantity - $d->ReturnQuantity);
                        $packSize = (int) ($d->product->pack_size ?? 0);
                        $discountPercentage = (float) ($d->discount_percentage ?? 0);
                        $lineAmountBeforeDiscount = max(0, $quantity * $d->UnitePrice);

                        $itemDiscountAmount = 0;
                        if ($quantity > 0) {
                            if (isset($d->discount_percentage_amount) && $d->discount_percentage_amount > 0) {
                                $itemDiscountAmount = $d->discount_percentage_amount;
                            } elseif (isset($d->itemDiscountAmount) && $d->itemDiscountAmount > 0) {
                                $itemDiscountAmount = $d->itemDiscountAmount;
                            } elseif ($discountPercentage > 0) {
                                $itemDiscountAmount = ($lineAmountBeforeDiscount * $discountPercentage) / 100;
                            }
                        }

                        $lineAmountAfterDiscount = max(0, $lineAmountBeforeDiscount - $itemDiscountAmount);
                        $saleTaxPct = (float) ($d->sale_tax ?? 0);
                        $incomeTaxPct = (float) ($d->income_tax ?? 0);
                        $rowSaleTaxAmount = isset($d->row_sale_tax_amount)
                            ? (float) $d->row_sale_tax_amount
                            : round($lineAmountAfterDiscount * $saleTaxPct / 100, 2);
                        $rowIncomeTaxAmount = isset($d->row_income_tax_amount)
                            ? (float) $d->row_income_tax_amount
                            : round($lineAmountAfterDiscount * $incomeTaxPct / 100, 2);
                        $lineNetAmount = $lineAmountAfterDiscount;
                        $totalRowSaleTax += $rowSaleTaxAmount;
                        $totalRowIncomeTax += $rowIncomeTaxAmount;
                    @endphp
                    <tr>
                        <td class="col-sno num">{{ $i++ }}</td>
                        <td class="col-name">
                            <span class="product-name">{{ $d->product->ProductName ?? '' }}</span>
                            @if(($d->ReturnQuantity ?? 0) > 0)
                                <div class="muted" style="font-size: 8px;">Ret: {{ $d->ReturnQuantity }}</div>
                            @endif
                        </td>
                        <td class="col-pack num">{{ $formatPackQty($quantity, $packSize) }}</td>
                        <td class="col-unit num">{{ $quantity }}</td>
                        <td class="col-price num">{{ number_format($d->UnitePrice ?? 0, 2) }}</td>
                        <td class="col-gross num">{{ number_format($lineAmountBeforeDiscount, 2) }}</td>
                        <td class="col-disc num">{{ $discountPercentage > 0 ? number_format($discountPercentage, 0) : '-' }}</td>
                        <td class="col-stax num">{{ $saleTaxPct > 0 ? number_format($rowSaleTaxAmount, 2) .' ('.$saleTaxPct .'%)' : '-' }}</td>
                        <td class="col-itax num">{{ $incomeTaxPct > 0 ? number_format($rowIncomeTaxAmount, 2) .' ('.$incomeTaxPct .'%)' : '-' }}</td>
                        <td class="col-net num">{{ number_format($lineNetAmount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <table>
                <tr>
                    <td>Gross Amount</td>
                    <td class="num">{{ number_format($totalAmountBeforeDiscount, 2) }}</td>
                </tr>
                <tr>
                    <td>Total Discount</td>
                    <td class="num">{{ number_format(round((float) $totalDiscountAmount), 0) }} </td>
                </tr>
                <tr>
                    <td>Invoice Discount</td>
                    <td class="num">{{ number_format(round((float) $invoiceDiscount), 0) }}</td>
                </tr>
                <tr>
                    <td><strong>Amount After Discount</strong></td>
                    <td class="num"><strong>{{ number_format($amountAfterDiscount, 2) }}</strong></td>
                </tr>
                <tr>
                    <td>Total Sale Tax</td>
                    <td class="num">{{ number_format($totalRowSaleTax ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td>Total Income Tax</td>
                    <td class="num">{{ number_format($totalRowIncomeTax ?? 0, 2) }}</td>
                </tr>
                <!-- @foreach($saleTaxLines as $saleTaxLine)
                <tr>
                    <td>{{ $saleTaxLine['name'] }}@if(!empty($saleTaxLine['percentage'])) ({{ number_format($saleTaxLine['percentage'], 0) }}%)@endif</td>
                    <td class="num">{{ number_format($saleTaxLine['amount'], 2) }}</td>
                </tr>
                @endforeach -->
                @if(count($saleTaxLines) > 1)
                <tr>
                    <td>Total Tax</td>
                    <td class="num">{{ number_format($totalSaleTaxAmount, 2) }}</td>
                </tr>
                @endif
                <tr class="total">
                    <td>Net Bill Amount</td>
                    <td class="num">{{ number_format($netTotal, 2) }}</td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <div class="notes">
                <div><strong>Thank you for visiting.</strong></div>
                <div>Note: Returns are accepted only with the original receipt/invoice within 48 hours.</div>
            </div>

            <div class="muted" style="text-align:right;">
                <div>{{ config('app.LIVE_URL') }}</div>
            </div>
        </div>
    </div>

</body>

</html>
