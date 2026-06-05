<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

        body {
            font-family: "Poppins", serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
        }

        strong,
        p {
            font-size: 10px;
            margin: 0;
            font-weight: 600;
            line-height: 1.2;
        }

        table {
            border-collapse: collapse;
        }

        table th,
        td {
            padding: 0 3px;
            border: 1px solid #000;
            font-size: 10px;
        }

        table th {
            background-color: #B2BBC3;
            font-weight: 600;
            padding: 3px;
        }

        .text-blue{
            color: rgb(0, 151, 211);
        }

        .text-red {
            color: rgb(231, 12, 12);
        }
        body{
            padding: 10px 20px;
            background-color: #fff;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0px auto;
            /* padding: 10px 20px; */
            background: #fff;
            /* border: 1px solid #ddd; */
            border-radius: 5px;
        }

        header {
            display: flex;
            justify-content: space-between;
            padding: 0px 0px;
            padding-bottom: 0px;
            width: 100%;

        }

        .company-details {
            width: 50%;
        }

        .header-right{
            width: 50%;
        }
        .header-right table{
            margin-left: auto;
        }

        .company-details h1 {
            margin: 0;
            font-size: 20px;
            color: #333;
            font-weight: 500;
            line-height: 1;
            margin-bottom: 3px;
        }

        .company-details p {
            margin: 0;
            font-weight: 600;
            text-transform: uppercase;
        }

        .logo img {
            max-width: 80px;
            margin-bottom: 10px;
        }

        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            margin-bottom: 10px;
        }
        .invoice-details>div{
            width: 50%;
        }


        .to-details h3 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
            line-height: 1;
            margin-bottom: 3px;
        }

        .to-details p {
            margin: 0;

        }


        .invoice-meta table tbody tr td:first-child {
            background-color: #B2BBC3;
            min-width: 120px;
            font-weight: 600;
        }


        .text-center {
            text-align: center !important;
        }

        .items table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .items table th,
        .items table td {
            text-align: left;
            font-size: 10px;
        }

        .items table th {}

        .items table td {
            padding: 2px 3px;

        }



        .totals {
            display: flex;
            justify-content: end;
        }

        .totals table strong {
            font-size: 10px !important;
        }

        .totals table td:first-child {
            background-color: #B2BBC3;
        }

        .totals table td:last-child {
            width: 110px;
            font-weight: 600;
        }

        .totals-row {
            margin: 5px 0;
        }

        .total-balance {
            font-size: 18px;
            color: #d00;
        }

        footer {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
            color: #666;
        }
        table td{
            font-size: 13px !important;
        }

        .table-borderless td,
        .table-borderless th,
        .table-borderless tr,
        .table-borderless {
            border: none !important;
        }
    </style>
</head>

<body>
<div class="invoice-container">
    <header>
        <div class="company-details">
            <h1 style="text-align: center;margin-left: 50%;width: 100%;">{{env('COMPANY_NAME')}}</h1><br>


        </div>


    </header>



    <section class="items">




        <table class="table table-borderless" style="border: 0px !important;">
            <tr>
                <td colspan="4"  style="text-align: center">Posted Cash Summary (Datewise / Userwise)</td>

            </tr>


            <tr>
                <td style="font-weight: bold;width: 25%">From Posting Date</td>
                <td style="width: 25%">{{ date("d-m-Y",strtotime($start_date)) }}</td>
                <td style="width: 25%;font-weight: bold">To Posting Date</td>
                <td style="width: 25%">{{ date("d-m-Y",strtotime($end_date)) }}</td>

            </tr>

            <tr>
                <td colspan="4" style="text-align: center">Note: This report is based on posted date and will not match the cash reports as cash reports are based on cash collection date.</td>

            </tr>
        </table>
         <?php $total = 0; ?>
        @foreach($report as $userId => $rows)
            <?php $total = $total + ($rows->user_advance); ?>

            <div class="card mt-4">
                <div class="card-header">
                    <strong>{{ $rows[0]->user_name ??'' }}</strong>
                </div>

                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-dark">
                        <tr>
                            <th style="width: 10%">#</th>
                            <th style="width: 40%">Head Name</th>
                            <th style="width: 20%"></th>
                            <th >Total Amount (Rs.)</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $index = 0; ?>
                        @foreach($rows as $index => $row)
                            <?php $total = ($total) + ($row->total_amount); ?>
                            <tr>
                                <td>{{ $index = $index + 1 }}</td>
                                <td>{{ $row->head_name }}</td>
                                <td>Count: {{ $row->total_count }}</td>
                                <td>{{ number_format($row->total_amount, 2) }}</td>
                            </tr>
                        @endforeach

                        @if($rows->user_advance > 0)
                            <tr>
                                <td>{{$index + 1}}</td>
                                <td>Advance Payment</td>
                                <td></td>
                                <td>{{$rows->user_advance}}</td>
                            </tr>
                        @endif
                        <tr>
                            <th colspan="3">Grand Total</th>
                            <th>
                                Rs. {{ number_format(($rows->sum('total_amount')) + ($rows->user_advance), 2) }}
                            </th>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

        <table>
            <tr>
                <td style="width: 70%; font-weight: bold">Total:</td>
                <td style="font-weight: bold;">{{ number_format($total, 2) }}</td>
            </tr>
        </table>

    </section>




</div>
</body>

</html>