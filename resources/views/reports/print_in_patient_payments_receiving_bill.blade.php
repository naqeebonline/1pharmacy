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
        <h6 style="font-size: 16px; text-align: center; text-decoration: underline">Patient Payments Slip</h6>
        <h6 style="font-size: 14px">MR#: {{ $admission->patient->mr_no ?? "" }}</h6>
        <h6 style="font-size: 14px">Patient Name: {{ $admission->patient->name ?? "" }}</h6>
        <h6 style="font-size: 14px">Admission Date: {{ date("d-m-Y H:i:s",strtotime($admission->admission_date)) ?? "" }}</h6>



        <h6 style="font-size: 14px">Procedure: {{ $admission->consultant_procedure->procedure->name ?? "" }}</h6>
        <h6 style="font-size: 14px">Printed By: {{ auth()->user()->name ?? "" }}</h6>
        <hr style="border: 1px solid black;">
        <table style="margin-top: 5px">
            <thead>
            <tr>
                <th style="width: 5%; font-size: 14px">S.No</th>
                <th style="width: 20%; font-size: 14px">Payment</th>
                <th style="width: 20%; font-size: 14px">Amount</th>
                <th style="width: 30%; font-size: 14px">Date</th>
                <th style="width: 25%; font-size: 14px">Rec By</th>
            </tr>
            </thead>
            <tbody>
            @if($admission->advance_payment != 0)
            <tr>
                <td style="font-weight: bold !important; font-size: 12px"></td>
                <td style="font-weight: bold !important; font-size: 10px">
                    {{"Advance Payment During Admission"}}

                </td>
                <td style="font-weight: bold !important; font-size: 12px"><?php echo $admission->advance_payment; ?></td>

                <td style="font-weight: bold !important; font-size: 12px">{{date("d-m-Y H:i:s",strtotime($admission->created_at))}}</td>
                <td style="font-weight: bold !important; font-size: 12px">{{$admission->createdBy->name}}</td>
            </tr>
            @endif
                @foreach($payments as $key => $value)
                    <tr>
                        <td style="font-weight: bold !important; font-size: 12px"><?php echo $key + 1; ?></td>
                        <td style="font-weight: bold !important; font-size: 10px">
                            @if(strtoupper($value->payment_type) == "ADVANCE")
                                {{"Advance"}}
                            @else
                                {{"Return"}}
                             @endif

                        </td>
                        <td style="font-weight: bold !important; font-size: 12px">

                                {{$value->amount}}


                        </td>

                        <td style="font-weight: bold !important; font-size: 12px">{{date("d-m-Y H:i:s",strtotime($value->created_at))}}</td>
                        <td style="font-weight: bold !important; font-size: 12px">{{$value->createdBy->name}}</td>
                    </tr>
                @endforeach


            </tbody>

        </table>
    </div>
    <hr style="border: 1px solid black;">
    <p style="text-align: center;font-weight: bold;font-size: 14px">Total Received: {{$total}}</p>
    <br>
    <div style="display: table; margin: 0 auto;">{!! DNS2D::getBarcodeHTML(config("app.LIVE_URL"), 'QRCODE', 3, 3) !!}</div>
    <p style="font-size: 12px; font-weight: bold; text-align: center !important;">Thank You For Visiting</p>
    <p style="font-size: 12px; font-weight: bold; text-align: center !important;">Note: Returns are accepted only with the original receipt/invoice in 48 Hours.</p>
    <br>
</div>



</body>
</html>
