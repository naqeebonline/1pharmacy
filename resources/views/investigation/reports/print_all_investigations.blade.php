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
    </style>
</head>

<body>
<div class="invoice-container">
    <header>
        <div class="company-details">
            <h1 style="text-align: center;margin-left: 50%;width: 100%;">{{env('COMPANY_NAME')}}</h1>
            <h1 style="text-align: center;margin-left: 50%;width: 100%;">List Appointments</h1>

        </div>

        <div class="invoice-meta header-right">


        </div>
    </header>



    <section class="items">
        <table>
            <tr>
                <td style="font-weight: bold; width: 8%">From Date:</td>
                <td style=" width: 10%">{{$from_date ?? ''}}</td>
                <td style="font-weight: bold; width: 8%">To Date</td>
                <td style="width: 10%">{{$to_date ?? ''}}</td>


            </tr>
        </table>

        <table style="margin-top: 5px">
            <thead>
            <tr>
                <th style="width: 5%;">S.No</th>
                <th style="width: 6%; ">Invoice#</th>
                <th style="width: 15%; ">Patient Name</th>
                <th style="width: 15%;">Investigation</th>
                <th style="width: 10%;">Amount</th>
                <th style="width: 10%;">Frequency</th>
                <th style="width: 10%;">Total</th>
                <th style="width: 10%;">Discount</th>
                <th style="width: 10%;">Created By</th>
                <th style="width: 10%;">Net Total</th>



            </tr>
            </thead>
            <tbody>
            <?php $total_amount = 0; $total_discount=0; $total_after_dicount=0;$total_discount=0 ?>

            @foreach($data as $key => $value)
                <?php
                        $total = ($value->sale_price * ($value->frequency));

                $after_discount = $total - ($value->discount_amount);
                $total_amount = ($total_amount) + $total;

                 $total_discount = ($total_discount) + ($value->discount_amount);
                $total_after_dicount = ($total_after_dicount) + ($after_discount);




                 ?>
                <tr>
                    <td >{{ $key + 1 }}</td>
                    <td >{{$value->invoice_no ?? ""}} <br>
                    <td >{{$value->patient->name ?? ''}}</td>
                    <td>{{$value->investigation->name ?? ''}}</td>
                    <td>{{$value->sale_price ?? ''}}</td>
                    <td>{{$value->frequency ?? ''}}</td>
                    <td>{{$total ?? 0}}</td>
                    <td>{{$value->discount_amount ?? 0}}</td>
                    <td>{{$value->created_by_user->name ?? ''}}</td>
                    <td>{{$after_discount}}</td>


                </tr>
            @endforeach
            <tr>
                <td ></td>
                <td ></td>
                <td ></td>
                <td ></td>
                <td ></td>
                <td ></td>

                <td style="font-weight: bold" >{{ $total_amount }}</td>
                <td style="font-weight: bold">{{$total_discount}}</td>
                <td style="font-weight: bold"></td>
                <td style="font-weight: bold">{{$total_after_dicount}}</td>

            </tr>
            </tbody>

        </table>
    </section>




</div>
</body>

</html>