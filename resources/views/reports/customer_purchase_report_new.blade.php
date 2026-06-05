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

        </div>

        <div class="invoice-meta header-right">


        </div>
    </header>



        <section class="items">
            <table>
                <tr>
                    <td style="font-weight: bold;">MR:NO</td>
                    <td>{{$admission->patient->mr_no ?? ''}}</td>
                    <td style="font-weight: bold;">Sehat Card Referral#</td>
                    <td>{{$admission->sc_ref_no ?? ''}}</td>
                    <td style="font-weight: bold;">Patient Name</td>
                    <td>{{$admission->patient->name ?? ''}}</td>
                    <td style="font-weight: bold;">Admisison Date</td>
                    <td>{{ date("d-m-Y h:i A",strtotime($admission->admission_date)) }}</td>
                </tr>
            </table>

            <table style="margin-top: 5px">
                <thead>
                    <tr>
                        <th style="width: 5%;">S.No</th>
                        <th style="width: 65%;">Item</th>
                        <th style="width: 10%;">Qty</th>
                        <th style="width: 10%;">Consumed</th>
                        <th style="width: 10%;">Return Qty</th>
                        <th style="width: 10%;">Medicine Type</th>
                        <th style="width: 10%;">Unit Price</th>
                        <th style="width: 10%;">Total</th>

                    </tr>
                </thead>
                <tbody>
                <?php $i=1; $taxAmount = 0; $totalAmount = 0; foreach($data as $d){
                    $quantity = ($d->Quantity);
                    $consumed = $quantity - $d->ReturnQuantity;
                    $amount = ($consumed * $d->UnitePrice);
                    $totalAmount = $totalAmount + $amount;
                    ?>
                    <tr>
                        <td><?php echo $i; $i++;;?></td>
                        <td><?php echo $d->product->ProductName ?? "";?></td>
                        <td><?php echo $quantity;?></td>
                        <td><?php echo ($consumed);?></td>
                        <td><?php echo $d->ReturnQuantity;?></td>
                        <td><?php echo $d->sale->medicine_type ?? '';?></td>
                        <td><?php echo $d->sale->UnitePrice ?? '';?></td>
                        <td><?php echo $amount;?></td>
                    </tr>
                <?php } ?>

                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>

                    <td style="font-weight: bold;font-size: 18px">Total</td>
                    <td style="font-weight: bold; font-size: 18px">{{$totalAmount}}</td>
                </tr>

                </tbody>

            </table>
        </section>




    </div>
</body>

</html>