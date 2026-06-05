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
    <hr style="border: 1px solid black;">
    <div class="main">
        <h6 style="font-size: 14px">MR#: {{ $admission->patient->mr_no ?? "" }}</h6>
        <h6 style="font-size: 14px">Patient Name: {{ $admission->patient->name ?? "" }}</h6>
        
        <h6 style="font-size: 14px">Admission Date: {{ date("d-m-Y H:i A",strtotime($admission->admission_date)) ?? "" }}</h6>



        <h6 style="font-size: 14px">Procedure: {{ $admission->consultant_procedure->procedure->name ?? "" }}</h6>
        <h6 style="font-size: 14px">Printed By: {{ auth()->user()->name ?? "" }}</h6>
         <hr style="border: 1px solid black;">
        <table style="margin-top: 5px">
            <thead>
            <tr>
                <th style="width: 5%; font-size: 14px">S.No</th>
                <th style="width: 45%; font-size: 14px">Description</th>
                <th style="width: 10%; font-size: 14px">Qty</th>
                <th style="width: 10%; font-size: 14px">Price</th>
                <th style="width: 10%; font-size: 14px">Amount</th>
            </tr>
            <tr>
                <td colspan="5">
                    <hr style="border: 1px solid black;">
                </td>
            </tr>
            </thead>
            <tbody>
                @foreach($sale as $key => $value)
                    <tr>
                        <th style="font-size: 14px" colspan="5">Invoice#: {{$value->InvoiceNo}}</th>

                    </tr>
                    @foreach($value->items as $key2 => $item)

                        <tr>
                            <td style="font-weight: bold !important; font-size: 12px"><?php echo $key2 + 1; ?></td>
                            <td style="font-weight: bold !important; font-size: 12px"><?php echo $item->product_name; ?></td>
                            <td style="font-weight: bold !important; font-size: 12px">
                                <?php echo ($item->Quantity - $item->ReturnQuantity); ?>
                                <?php if ($item->ReturnQuantity > 0): ?>
                                Return: (<?php echo $item->ReturnQuantity ?>)
                                <?php endif; ?>
                            </td>
                            <td style="font-weight: bold !important; font-size: 12px"><?php echo $item->UnitePrice; ?></td>
                            <td style="font-weight: bold !important; font-size: 12px"><?php echo $item->total_amount; ?></td>
                        </tr>
                    @endforeach

                    <tr>
                        <td colspan="5">
                            <hr style="border: 1px solid black;">
                        </td>
                    </tr>
                @endforeach

                <tr>
                    <th colspan="3" style="border-top: 2px solid black;"></th>
                    <th style="font-size: 12px; border-top: 2px solid black;">Total:</th>
                    <th style="font-size: 12px; border-top: 2px solid black;">{{ $total_amount }}</th>
                </tr>
                <tr>
                    <th colspan="3"></th>
                    <th style="font-size: 12px">Discount:</th>
                    <th style="font-size: 12px">{{ round($total_discount) }}</th>
                </tr>

                <tr>
                    <th colspan="3"></th>
                    <th style="font-size: 14px">Amount:</th>
                    <th style="font-size: 14px">{{ ($total_amount < 1) ? 0 : ($total_amount - $total_discount) }}</th>
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
