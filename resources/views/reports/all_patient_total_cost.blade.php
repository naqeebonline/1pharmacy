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


        <table style="margin-top: 5px">
            <thead>
            <tr>

                <th style="width: 5%;">S.No</th>
                <th style="width: 10%;">Patient Name</th>
                <th style="width: 18%;">Procedure</th>
                <th style="width: 10%;">Consultant</th>

                <th style="width: 16%;">Procedure Rate</th>
                <th style="width: 7%;">Consultant Share Amount</th>
                <th style="width: 7%;">Investigation Cost</th>
                <th style="width: 7%;">Service Charges</th>
                <th style="width: 7%;">Medicine Cost</th>
                <th style="width: 7%;">Total Cost</th>
                <th style="width: 7%;">Balance</th>


            </tr>
            </thead>
            <tbody>
            <?php $totalProcedureAmount = 0; $totalConsultantAmount = 0; $totalInvAmount =0;
            $totalMedicine_amount=0; $totalConsultant_share_amount=0;
            $grandTotalCost = 0;
            ?>
            @foreach($data as $key => $admission)
                <?php
                $totalProcedureAmount += ($admission->getAdmissionDetails['procedure_amount']);
                $totalConsultantAmount += $admission->getAdmissionDetails['consultant_share_amount'];
                $totalInvAmount += $admission->getAdmissionDetails['investigation_amount'];
                $totalMedicine_amount += $admission->getAdmissionDetails['medicine_amount'];

                ?>

                <tr>
                    <td>{{$key + 1}}</td>
                    <td>{{$admission->patient->name ?? ""}}</td>

                    <td>Type: {{$admission->procedure_type->type ?? ""}}
                        <br>
                        Name: {{$admission->procedure_type->name ?? ""}}
                    </td>
                    <td>{{$admission->consultant->name ?? ""}} , {{$admission->sub_consultant->name ?? ""}}</td>
                    <td>{{$admission->getAdmissionDetails['procedure_amount1']}} <br>
                        @if($admission->getAdmissionDetails['is_medical_case'])
                            <span> x {{$admission->getAdmissionDetails['daysDifference']}} days = {{ ($admission->getAdmissionDetails['procedure_amount'])  }}</span>
                        @endif
                    </td>
                    <td>{{$admission->getAdmissionDetails['consultant_share_amount']}}</td>
                    <td>{{$admission->getAdmissionDetails['investigation_amount']}}</td>
                    <td>{{$admission->getAdmissionDetails['service_charges']}}</td>
                    <td>{{$admission->getAdmissionDetails['medicine_amount']}}</td>
                    <td>{{ $total_cost = (($admission->getAdmissionDetails['consultant_share_amount']) + ($admission->getAdmissionDetails['investigation_amount']) + ($admission->getAdmissionDetails['service_charges']) + ($admission->getAdmissionDetails['medicine_amount'])) }}</td>
                    <td>{{ ($admission->getAdmissionDetails['procedure_amount']) - ($total_cost) }}</td>
                    <?php $grandTotalCost += $total_cost; ?>
                </tr>

            @endforeach
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="font-weight: bold">{{$totalProcedureAmount}}</td>
                <td style="font-weight: bold">{{$totalConsultantAmount}}</td>
                <td style="font-weight: bold">{{$totalInvAmount}}</td>
                <td style="font-weight: bold"></td>
                <td style="font-weight: bold">{{$totalMedicine_amount}}</td>
                <td style="font-weight: bold">{{ $grandTotalCost }}</td>
                <td style="font-weight: bold"> {{ ($totalProcedureAmount) - ($grandTotalCost) }}</td>
            </tr>



            </tbody>

        </table>
    </section>




</div>
</body>

</html>