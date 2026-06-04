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
    <style>
        @media print {
            .btn.btn-primary {
                display: none !important;
            }
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
        <form method="GET" action="{{ route('pos.get_user_base_daily_closing_report2') }}" class="mb-3">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Start Date:
                        <input type="date" name="start_date" class="form-control" value="{{ $start_date }}">
                    </th>
                    <th class="text-end">End Date:
                        <input type="date" name="end_date" class="form-control" value="{{ $end_date }}">
                    </th>
                    <th class="text-end" style="width: 15% !important;">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </th>
                </tr>
                </thead>
            </table>
        </form>

        @foreach($finalReport as $date => $reportData)
            <div class="mt-2">
                <hr>
                <p class="border-bottom pb-1" style="font-weight: bold; background-color: #B2BBC3; text-align: center; font-size: 14px;">
                    Date: {{ $date }}
                </p>
                <?php $total = 0; ?>
                @foreach($reportData['users'] as $user)

                    <div class="card mt-3">
                        <div class="card-header">
                            <strong style="font-size: 12px">User: {{ $user['name'] }}</strong><br>
                            <small>Advance: Rs. {{ number_format($user['user_advance'], 2) }}</small>
                        </div>
                        <div class="card-body p-2">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Head Name</th>
                                    <th>Reference Type</th>
                                    <th>Total Count</th>
                                    <th>Debit (Rs.)</th>
                                    <th>Credit (Rs.)</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($user['transactions'] as $index => $txn)
                                    @if($txn['total_credit'] > 0 || $txn['total_credit'] < 0 )
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $txn['head_name'] }}</td>
                                        <td>{{ $txn['reference_type'] }}</td>
                                        <td>{{ $txn['total_count'] }}</td>
                                        <td>{{ number_format($txn['total_debit'], 2) }}</td>
                                        <td>{{ number_format($txn['total_credit'], 2) }}</td>
                                    </tr>
                                    @endif
                                @endforeach
                                <tr class="table-secondary">
                                    <th colspan="4" class="text-end">User Total</th>
                                    <th>
                                        Rs. {{ number_format(collect($user['transactions'])->sum('total_debit'), 2) }}
                                    </th>
                                    <th>
                                        Rs. {{ number_format(collect($user['transactions'])->sum('total_credit'), 2) }}
                                        <?php $total = ($total) + (collect($user['transactions'])->sum('total_credit')); ?>
                                    </th>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
                <p style="text-align: center;font-size: 12px;float: right">Total Amount = {{$total}}</p>
                <p class="border-bottom mt-2" style="font-weight: bold; background-color: #B2BBC3; text-align: center; font-size: 14px">
                    Date: {{ $date }}
                </p>
            </div>
        @endforeach
    </section>





</div>
</body>

</html>