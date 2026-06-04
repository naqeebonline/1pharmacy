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
            <h1>Expert Pos</h1>
            <p>Peshawar</p>
            <p>03149465659</p>
        <!--<p>VAT NUMBER: <span class="text-blue"><?php /*echo VAT_NUMBER; */?></span></p>-->
            <p>Company No: 1234543</p>
            <strong style="border: 1px solid lightgrey; margin:  font-size: 11px; color: black; line-height: 1 !important;">To: <?php echo $customer[0]->Name ?? "";?> <br> <?php echo $customer[0]->Address ?? "";?></strong>
        </div>

        <div class="invoice-meta header-right">
            <table style="width: 95%">
                <tbody>

                <tr>
                    <td style="width: 30% !important;">Contact#:</td>
                    <td class="text-center"><?php echo $customer[0]->ContactNo ?? ""; ?></td>
                </tr>

                <tr>
                    <td style="width: 30% !important;">Invoice #:</td>
                    <td class="text-center"><?php echo $record[0]->InvoiceNo;?></td>
                </tr>
                <tr>
                    <td>Date:</td>
                    <td class="text-center"><?php if(isset($record[0]->Date)){echo date('d-m-Y',strtotime($record[0]->Date));}?></td>
                </tr>

                </tbody>
            </table>

        </div>
    </header>





    <section class="items">
        <table>
            <thead>
            <tr>
                <th>S.No</th>
                <th>Description</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Amount</th>
            </tr>
            </thead>
            <tbody>
            <?php $i=1; $taxAmount = 0; $totalAmount = 0; foreach($data as $d){
            $taxAmount = ($taxAmount) + ($d->taxAmount);
            $totalAmount = ($totalAmount) + ($d->totalAmount);
            ?>
            <tr>
                <td><?php echo $i; $i++;;?></td>
                <td><?php echo $d->product->ProductName ?? "";?></td>
                <td><?php echo $d->Quantity;?></td>
                <td><?php echo $d->UnitePrice;?></td>
                <td><?php echo number_format($d->totalAmount,2);?></td>
            </tr>
            <?php } ?>

            </tbody>

        </table>
    </section>


    <section class="totals">

        <table>
            <tbody>

            <tr>
                <td><strong>Net Price:</strong></td>
                <td>£ <?php echo $totalAmount;?></td>
            </tr>
            <tr>
                <td><strong>VAT:</strong></td>
                <td>£ <?php echo $taxAmount; ?></td>
            </tr>

            <tr>
                <td><strong>Current Total:</strong></td>
                <td>£ <?php echo ($totalAmount) + ($taxAmount);?></td>
            </tr>

            <tr>
                <td><strong>Previous Balance:</strong></td>
                <td>£ <?php echo $PreviousBalance;?></td>
            </tr>

            <tr>
                <td><strong class="text-red">Balance:</strong></td>
                <td><strong class="text-red">£(<?php echo ($totalAmount) + ($PreviousBalance) + ($taxAmount);?>)</strong></td>
            </tr>
            </tbody>
        </table>

    </section>

    <?php /*$this->load->view('common/new_report_footer');*/?>
</div>
</body>

</html>