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

        <div class="container">
            <h4 style="text-align: center;">Voucher Wise Report</h4>

            <form method="GET" action="{{route('pos.finance_vouchers_report')}}" class="mb-3">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Start Date: <input type="date" name="start_date" class="form-control" value="{{ $start_date }}"></th>
                        <th class="text-end">End Date: <input type="date" name="end_date" class="form-control" value="{{ $end_date }}"></th>
                        <th class="text-end">Type:
                        <select name="voucher_type">
                            <option value="">All</option>
                            @foreach($voucherTypes as $key => $value)
                                <option value="{{$value}}" {{ $value == $voucher_type_param ? "selected" : "" }}>{{ ucfirst($value) }}</option>
                            @endforeach
                        </select>
                        </th>
                        <th class="text-end"><button type="submit" class="btn btn-primary">Filter</button></th>
                    </tr>
                    </thead>
                </table>

            </form>

            @foreach($vouchers as $voucher)
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <strong>Voucher #{{ $voucher->voucher_number }}</strong> —
                        <strong>Date: {{ $voucher->voucher_date }}</strong> <br>
                        {{--Type: {{ $voucher->voucher_type ?? 'N/A' }} |
                        Remarks: {{ $voucher->remarks ?? 'N/A' }}--}}
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-bordered mb-0" style="width: 100%">
                            <thead class="table-light">
                            <tr>
                                <th style="width: 12%">Date</th>
                                <th style="width: 18%">Debit Head</th>
                                <th style="width: 20%">Credit Head</th>
                                <th style="width: 10%" >Amount</th>
                                <th style="width: 10%">User</th>
                                <th style="width: 30%">Remarks</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($transactions[$voucher->id] ?? [] as $trx)
                                <tr>
                                    <td>{{ $trx->transaction_date }}</td>
                                    <td>{{ $trx->debit_head }}</td>
                                    <td>{{ $trx->credit_head }}</td>
                                    <td class="text-end">{{ number_format($trx->amount, 2) }}</td>
                                    <td>{{ $trx->user_name }}</td>
                                    <td>{{ $trx->remarks }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No transactions for this voucher.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>

    </section>




</div>
</body>

</html>