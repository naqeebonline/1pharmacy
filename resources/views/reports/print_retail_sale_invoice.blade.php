<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>POS</title>
    <link rel="stylesheet" href="{{ asset('assets/css/print_style.css') }}">
    <style>
        h6 {
            margin: 3px 0;
            padding: 2px 0;
        }

        .bill-items-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }

        .bill-items-table th,
        .bill-items-table td {
            font-size: 10px;
            padding: 2px 2px;
            text-align: center;
            vertical-align: top;
            word-wrap: break-word;
        }

        .bill-items-table th.col-desc,
        .bill-items-table td.col-desc {
            text-align: left;
        }

        .bill-items-table td.num {
            white-space: nowrap;
        }

        @media print {

            .cut-break {
                page-break-after: always;
            }
        }
    </style>
    <script>
        window.onload = function() {
            // window.print(); // Auto-trigger print dialog
        };
    </script>
</head>

<body>

    <div class="wrap">

        <div class="logo text-center">
            <h2 style="font-size: 22px">{{ config('app.COMPANY_NAME') }}</h2>
            <p style="font-size: 13px; font-weight:bold;">{{ config('app.BRANCH_NAME') }}</p>
            <small style="font-size: 13px">{{ date("d-m-Y h:i A") }}</small>
        </div>

        <div class="main">
            <h6 style="font-size: 14px">Invoice#: {{ $record->InvoiceNo ?? "" }} | Created At: {{ $record->CreatedAt ?? "" }}</h6>
            {{--<h6 style="font-size: 14px">Name: {{ $patient->name ?? '' }}</h6>--}}
            @if($appointment_patient_name !='')
            <h6 style="font-size: 14px">{!! $appointment_patient_name !!} </h6>
            @endif

            <h6 style="font-size: 14px">Created By: {{ $record->created_by->name ?? "" }} | Printed By: {{ auth()->user()->name ?? "" }}</h6>

            <table class="bill-items-table" style="margin-top: 5px">
                <thead>
                    <tr>
                        <th style="width: 5%">#</th>
                        <th class="col-desc" style="width: 45%">Description</th>
                        <th style="width: 12%">Qty</th>
                        <th style="width: 15%">Price</th>
                        <th style="width: 23%">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    $taxAmount = 0;
                    $totalAmountBeforeDiscount = 0;
                    $totalDiscountAmount = 0;
                    $totalRowSaleTax = 0;
                    $totalRowIncomeTax = 0;
                    foreach ($data as $d) {
                        $taxAmount += $d->taxAmount;
                        $quantity = max(0, $d->Quantity - $d->ReturnQuantity); // Active quantity after returns
                        $discountPercentage = $d->discount_percentage ?? 0;

                        // Calculate line amount before discount (Active Quantity × Unit Price)
                        $lineAmountBeforeDiscount = max(0, $quantity * $d->UnitePrice);
                        $totalAmountBeforeDiscount += $lineAmountBeforeDiscount;

                        // Use the updated discount amount from database (already proportional after returns)
                        $itemDiscountAmount = 0;
                        if ($quantity > 0) {
                            if (isset($d->discount_percentage_amount) && $d->discount_percentage_amount > 0) {
                                // Use the stored discount amount (already updated proportionally by return_pharmacy_item)
                                $itemDiscountAmount = $d->discount_percentage_amount;
                            } else if (isset($d->itemDiscountAmount) && $d->itemDiscountAmount > 0) {
                                // Fallback: use itemDiscountAmount if available
                                $itemDiscountAmount = $d->itemDiscountAmount;
                            } else if ($discountPercentage > 0) {
                                // Calculate discount from percentage for active quantity
                                $itemDiscountAmount = ($lineAmountBeforeDiscount * $discountPercentage) / 100;
                            }
                        }
                        // If quantity is 0, discount is automatically 0

                        $totalDiscountAmount += max(0, $itemDiscountAmount); // Ensure non-negative discount

                        // Calculate amount after discount for display in Amount column
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
                    ?>
                        <tr>
                            <td class="num" style="font-weight: bold"><?php echo $i++; ?></td>
                            <td class="col-desc" style="font-weight: bold"><?php echo $d->product->ProductName; ?></td>
                            <td class="num" style="font-weight: bold">
                                <?php echo $quantity; ?>
                                <?php if ($d->ReturnQuantity > 0): ?>
                                    <br><small>Ret: <?php echo $d->ReturnQuantity ?></small>
                                <?php endif; ?>
                            </td>
                            <td class="num" style="font-weight: bold"><?php echo number_format($d->UnitePrice, 2); ?></td>
                            <td class="num" style="font-weight: bold"><?php echo number_format($lineAmountBeforeDiscount, 2); ?></td>
                        </tr>
                    <?php
                    }

                    $invoiceDiscount = $record->invoice_discount ?? 0;
                    $amountAfterDiscount = round((float) ($totalAmountBeforeDiscount - $totalDiscountAmount - $invoiceDiscount));

                    $saleTaxLines = $sale_tax_lines ?? [];
                    $totalSaleTaxAmount = (float) ($total_row_tax_amount ?? 0);
                    if ($totalSaleTaxAmount <= 0 && !empty($saleTaxLines)) {
                        $totalSaleTaxAmount = array_sum(array_column($saleTaxLines, 'amount'));
                    }
                    $storedTaxAmount = (float) ($record->tax_amount ?? 0);
                    $netTotal = $amountAfterDiscount + ($storedTaxAmount > 0 ? $storedTaxAmount : $totalSaleTaxAmount);
                    ?>

                    <tr>
                        <th colspan="4" style="font-size: 10px; border-top: 2px solid black; text-align:right;">Total Amount:</th>
                        <th style="font-size: 10px; border-top: 2px solid black; text-align:right;">{{ number_format($totalAmountBeforeDiscount, 2) }}</th>
                    </tr>
                    @if((float) $totalDiscountAmount > 0)
                    <tr>
                        <th colspan="4" style="font-size: 10px; text-align:right;">{{ number_format((float) ($record->discount_percentage ?? 0), 0) }}% Discount:</th>
                        <th style="font-size: 10px; text-align:right;">{{ number_format($totalDiscountAmount, 2) }}</th>
                    </tr>
                    @endif
                    @if((float) $invoiceDiscount > 0)
                    <tr>
                        <th colspan="4" style="font-size: 10px; text-align:right;">Invoice Discount:</th>
                        <th style="font-size: 10px; text-align:right;">{{ number_format($invoiceDiscount, 2) }}</th>
                    </tr>
                    @endif
                    <tr>
                        <th colspan="4" style="font-size: 10px; text-align:right;">Amount After Discount:</th>
                        <th style="font-size: 10px; text-align:right;">{{ number_format($amountAfterDiscount, 2) }}</th>
                    </tr>
                    @if((float) $totalRowSaleTax > 0)
                    <tr>
                        <th colspan="4" style="font-size: 10px; text-align:right;">Total Sale Tax:</th>
                        <th style="font-size: 10px; text-align:right;">{{ number_format($totalRowSaleTax, 2) }}</th>
                    </tr>
                    @endif
                    @if((float) $totalRowIncomeTax > 0)
                    <tr>
                        <th colspan="4" style="font-size: 10px; text-align:right;">Total Income Tax:</th>
                        <th style="font-size: 10px; text-align:right;">{{ number_format($totalRowIncomeTax, 2) }}</th>
                    </tr>
                    @endif
                    @foreach($saleTaxLines as $saleTaxLine)
                    @if((float) ($saleTaxLine['amount'] ?? 0) > 0)
                    <tr>
                        <th colspan="4" style="font-size: 10px; text-align:right;">{{ $saleTaxLine['name'] }}@if(!empty($saleTaxLine['percentage'])) ({{ number_format($saleTaxLine['percentage'], 0) }}%)@endif:</th>
                        <th style="font-size: 10px; text-align:right;">{{ number_format($saleTaxLine['amount'], 2) }}</th>
                    </tr>
                    @endif
                    @endforeach
                    <tr>
                        <th colspan="4" style="font-size: 12px; text-align:right;">Net Bill Amount:</th>
                        <th style="font-size: 12px; text-align:right;">{{ number_format($netTotal, 2) }}</th>
                    </tr>
                </tbody>
            </table>
        </div>

        <div style="display: table; margin: 0 auto;">{!! DNS2D::getBarcodeHTML(config("app.LIVE_URL"), 'QRCODE', 3, 3) !!}</div>
        <p style="font-size: 12px; font-weight: bold; text-align: center !important;">Thank You For Visiting</p>
        <p style="font-size: 12px; font-weight: bold; text-align: center !important;">Note: Returns are accepted only with the original receipt/invoice in 48 Hours.</p>
        <br>
    </div>



</body>

</html>