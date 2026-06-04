<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Invoice - A4</title>
    @if (empty($for_pdf))
    <script>
        // Open print dialog after the full page has loaded (more reliable than DOMContentLoaded for print layout).
        window.addEventListener('load', function () {
            setTimeout(function () {
                window.print();
            }, 300);
        });
    </script>
    @endif
    <style>
        @page {
            size: A4 portrait;
            margin: 14mm 14mm 14mm 18mm;
        }

        html,
        body {
            height: auto;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #111;
            font-size: 12px;
            line-height: 1.35;
        }

        body.pdf-export {
            width: 100%;
        }

        * {
            box-sizing: border-box;
        }

        .invoice {
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
            padding: 6mm 14mm 8mm 10mm;
        }

        .layout-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table {
            border-bottom: 2px solid #111;
            margin-bottom: 10px;
        }

        .header-table td {
            vertical-align: top;
            padding-bottom: 10px;
        }

        .header-table .meta {
            text-align: right;
        }

        .head-details-table {
            margin-top: 8px;
            border-top: 1px solid #ddd;
        }

        .head-details-table td {
            vertical-align: top;
            padding-top: 8px;
        }

        .head-details .box {
            width: 50%;
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
            font-size: 20px;
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
            font-size: 16px;
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
            max-width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            table-layout: fixed;
        }

        /* Column widths total 100% (old layout was ~130% and clipped the right side) */
        .col-sno { width: 4%; }
        .col-name { width: 11%; }
        .col-pack { width: 9%; }
        .col-qty { width: 7%; }
        .col-price { width: 14%; }
        .col-disc { width: 5%; }
        .col-sale-tax { width: 13%; }
        .col-income-tax { width: 13%; }
        .col-amount { width: 24%; }

        .product-name {
            font-weight: 700;
            display: block;
            max-width: 100%;
            font-size: 10px;
            line-height: 1.25;
            white-space: normal;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .items thead th {
            text-align: left;
            padding: 6px 3px;
            background: #f5f5f5;
            border-bottom: 1px solid #bbb;
            font-size: 9px;
            vertical-align: top;
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
            padding: 6px 3px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
            font-size: 10px;
        }

        .items thead th:first-child,
        .items tbody td:first-child {
            padding-left: 6px;
        }

        .items thead th:last-child,
        .items tbody td:last-child {
            padding-right: 10px;
        }

        .items .col-name {
            padding-right: 4px;
        }

        .items tfoot td {
            padding: 6px;
        }

        .num {
            text-align: right;
            white-space: nowrap;
        }

        .totals-wrap {
            margin-top: 10px;
        }

        .totals-wrap .totals-inner {
            width: 320px;
            max-width: 100%;
            margin-left: auto;
            margin-right: 4px;
            border-collapse: collapse;
        }

        .totals-wrap .totals-inner td {
            padding: 4px 0;
        }

        .totals-wrap .totals-inner td:first-child {
            color: #444;
        }

        .totals-wrap .totals-inner tr.total td {
            font-size: 14px;
            font-weight: 700;
            border-top: 2px solid #111;
            padding-top: 8px;
        }

        .footer-table {
            margin-top: 14px;
            border-top: 1px solid #ddd;
        }

        .footer-table td {
            vertical-align: bottom;
            padding-top: 10px;
        }

        .footer-table .footer-right {
            text-align: right;
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

            .invoice {
                page-break-after: avoid;
                padding: 4mm 12mm 6mm 8mm !important;
                box-sizing: border-box !important;
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

<body @if(!empty($for_pdf)) class="pdf-export" @endif>

    @php
        /**
         * Convert unit quantity to "{packs} Pack {units} Unit" based on pack size.
         * Example: qty=202, packSize=100 => "2 Pack 2 Unit".
         */
        $formatQtyWithPacks = function ($qty, $packSize) {
            $qty = (int) ($qty ?? 0);
            $packSize = (int) ($packSize ?? 0);

            if ($qty <= 0) {
                return '0';
            }
            if ($packSize <= 0) {
                return (string) $qty;
            }

            $packs = intdiv($qty, $packSize);
            $units = $qty % $packSize;

            $parts = [];
            if ($packs > 0) {
                $parts[] = $packs . ' Pack';
            }
            if ($units > 0) {
                $parts[] = $units . ' Unit';
            }

            return implode(' ', $parts);
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
        <table class="layout-table header-table">
            <tr>
                <td class="brand" style="width: 55%;">
                    <h1>{{ config('app.COMPANY_NAME') }}</h1>
                    <div class="branch">{{ config('app.BRANCH_NAME') }}</div>
                    <div style="margin-top: 2px; font-weight: bold;">
                        @if(!empty($appointment_patient_name))
                            Name: {{ $patient->name ?? '' }}
                        @else
                            Name: Walking Customer
                        @endif
                    </div>
                    <div class="branch">
                        @if($patient && $patient->cnic != '')
                            NTN No: {{ $patient->cnic }}
                        @endif
                    </div>
                    <div class="muted" style="margin-top: 2px;">Printed: {{ date('d-m-Y h:i A') }}</div>
                </td>
                <td class="meta" style="width: 45%; padding-right: 6px;">
                    <div class="title">Sales Invoice</div>
                    <table class="layout-table">
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
                </td>
            </tr>
        </table>

        <table class="layout-table head-details-table head-details">
            <tr>
                <td class="box">
                    <div class="value muted">&nbsp;</div>
                </td>
                <td class="box" style="width: 280px;">
                    <div class="value">
                        <div style="margin-top: 2px;"><span class="muted">Return in bill:</span> <strong>{{ $return ?? 'No' }}</strong></div>
                    </div>
                </td>
            </tr>
        </table>

        <table class="items">
            <colgroup>
                <col class="col-sno">
                <col class="col-name">
                <col class="col-pack">
                <col class="col-qty">
                <col class="col-price">
                <col class="col-disc">
                <col class="col-sale-tax">
                <col class="col-income-tax">
                <col class="col-amount">
            </colgroup>
            <thead>
                <tr>
                    <th class="col-sno">#</th>
                    <th class="col-name">Description</th>
                    <th class="col-pack num">Pack</th>
                    <th class="col-qty num">Qty</th>
                    <th class="col-price num">Price</th>
                    <th class="col-disc num">Disc%</th>
                    <th class="col-sale-tax num">ST%</th>
                    <th class="col-income-tax num">IT%</th>
                    <th class="col-amount num">Amount</th>
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
                        $discountPercentage = $d->discount_percentage ?? 0;
                        $lineAmountBeforeDiscount = max(0, $quantity * $d->UnitePrice);

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

                        $lineAmountAfterDiscount = max(0, $lineAmountBeforeDiscount - $itemDiscountAmount);
                        $saleTaxPct = (float) ($d->sale_tax ?? 0);
                        $incomeTaxPct = (float) ($d->income_tax ?? 0);
                        $rowSaleTaxAmount = isset($d->row_sale_tax_amount)
                            ? (float) $d->row_sale_tax_amount
                            : round($lineAmountAfterDiscount * $saleTaxPct / 100, 2);
                        $rowIncomeTaxAmount = isset($d->row_income_tax_amount)
                            ? (float) $d->row_income_tax_amount
                            : round($lineAmountAfterDiscount * $incomeTaxPct / 100, 2);
                        $totalRowSaleTax += $rowSaleTaxAmount;
                        $totalRowIncomeTax += $rowIncomeTaxAmount;
                    @endphp
                    <tr>
                        <td class="col-sno num">{{ $i++ }}</td>
                        <td class="col-name">
                            <span class="product-name">{{ $d->product->ProductName ?? '' }}</span>
                            @if(($d->ReturnQuantity ?? 0) > 0)
                                <div class="muted" style="font-size: 11px;">Returned: {{ $d->ReturnQuantity }}</div>
                            @endif
                        </td>
                        <td class="col-pack num">{{ $formatQtyWithPacks($quantity, $d->product->pack_size ?? 0) }}</td>
                        <td class="col-qty num">{{ $quantity }}</td>
                        <td class="col-price num">{{ number_format($d->UnitePrice ?? 0, 2) }}</td>
                        <td class="col-disc num">{{ number_format($d->discount_percentage, 0) }}</td>
                       
                        <td class="col-sale-tax num">{{ number_format($saleTaxPct, 0) }}%</td>
                        <td class="col-income-tax num">{{ number_format($incomeTaxPct, 0) }}%</td>
                        <td class="col-amount num">{{ number_format($lineAmountAfterDiscount, 2) }}</td>
                        
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="layout-table totals-wrap">
            <tr>
                <td align="right">
                    <table class="totals-inner">
                        <tr>
                            <td>Total Amount</td>
                            <td class="num">{{ number_format($totalAmountBeforeDiscount, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Total Discount</td>
                            <td class="num">{{ number_format(round((float) $totalDiscountAmount), 0) }}</td>
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
                </td>
            </tr>
        </table>

        <table class="layout-table footer-table">
            <tr>
                <td class="notes" style="width: 70%;">
                    <div><strong>Thank you for visiting.</strong></div>
                    <div>Note: Returns are accepted only with the original receipt/invoice within 48 hours.</div>
                </td>
                <td class="footer-right muted" style="width: 30%;">
                    <div>{{ config('app.LIVE_URL') }}</div>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
