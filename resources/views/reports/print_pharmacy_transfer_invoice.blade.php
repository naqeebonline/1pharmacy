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
        @media print {

            .cut-break {
                page-break-after: always;
            }
        }
    </style>
    <script>
        window.onload = function () {
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
         
         
        <h6 style="font-size: 14px">{{ ucwords(str_replace('_', ' ', $record->transfer_type ?? ""))  }} </h6>
        <h6 style="font-size: 14px">Created By: {{ $record->created_by->name ?? "" }} | Printed By: {{ auth()->user()->name ?? "" }}</h6>

        <table style="margin-top: 5px">
            <thead>
            <tr>
                <th style="width: 5%; font-size: 14px">S.No</th>
                <th style="width: 45%; font-size: 14px">Description</th>
                <th style="width: 10%; font-size: 14px">Qty</th>
                <th style="width: 10%; font-size: 14px">Price</th>
                <th style="width: 10%; font-size: 14px">Amount</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $i = 1;
            $taxAmount = 0;
            $totalAmount = 0;
            foreach ($data as $d) {
            $taxAmount += $d->taxAmount;
            $totalAmount += $d->totalAmount;
            $quantity = $d->Quantity - $d->ReturnQuantity;
            ?>
            <tr>
                <td style="font-weight: bold !important; font-size: 12px"><?php echo $i++; ?></td>
                <td style="font-weight: bold !important; font-size: 12px"><?php echo $d->product->ProductName; ?></td>
                <td style="font-weight: bold !important; font-size: 12px">
                    <?php echo $quantity; ?>
                    <?php if ($d->ReturnQuantity > 0): ?>
                    Return: (<?php echo $d->ReturnQuantity ?>)
                    <?php endif; ?>
                </td>
                <td style="font-weight: bold !important; font-size: 12px"><?php echo $d->UnitePrice; ?></td>
                <td style="font-weight: bold !important; font-size: 12px"><?php echo $d->totalAmount; ?></td>
            </tr>
            <?php } ?>

            <tr>
                <th colspan="3" style="border-top: 2px solid black;"></th>
                <th style="font-size: 12px; border-top: 2px solid black;">Total:</th>
                <th style="font-size: 12px; border-top: 2px solid black;">{{ $totalAmount }}</th>
            </tr>
            <tr>
                <th colspan="3"></th>
                <th style="font-size: 12px">Discount:</th>
                <th style="font-size: 12px">{{ round($TotalDiscount + $record->invoice_discount) }}</th>
            </tr>

            <tr>
                <th colspan="3"></th>
                <th style="font-size: 14px">Amount:</th>
                <th style="font-size: 14px">{{ ($totalAmount < 1) ? 0 : round($totalAmount - $record->Discount - $record->invoice_discount) }}</th>
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
