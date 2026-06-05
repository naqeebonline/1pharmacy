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
            <p style="text-align: center;margin-left: 50%;width: 100%;">User Closing Details</p>

        </div>

        <div class="invoice-meta header-right">


        </div>
    </header>



    <section class="">
        <table>
            <tr>
                <td style="font-weight: bold; width: 8%">From Date:</td>
                <td style=" width: 10%">{{$from_date ?? ''}}</td>
                <td style="font-weight: bold; width: 8%">To Date</td>
                <td style="width: 10%">{{$to_date ?? ''}}</td>


            </tr>
        </table>
        @if(count($appointments) > 0)
            <p style="text-align: center; font-weight: bold; font-size:15px;margin-top: 6px">Appointments</p>
                <table>
            <thead>
            <tr>
                <th style="width: 5%;">S.No</th>
                <th style="width: 6%; ">Appointment#</th>
                <th style="width: 15%; ">Patient Name</th>
                <th style="width: 15%; ">Contact#</th>
                <th style="width: 15%;">Consultant</th>
                <th style="width: 10%;">OPD Type</th>

                <th style="width: 13%;">Date</th>
                <th style="width: 8%;">Fees</th>
                <th style="width: 10%;">Hospital Share</th>
                <th style="width: 10%;">Consultant Share</th>
                <th style="width: 10%;">Created By</th>


            </tr>
            </thead>
            <tbody>
            <?php $totalFees =0; $totalHospitalShare=0; $totalConsultantShare = 0; ?>
            @foreach($appointments as $key => $value)
                <?php $totalFees += $value->fee;
                $totalHospitalShare += $value->hospital_share;
                $totalConsultantShare += $value->consultant_share; ?>
                <tr>
                    <td >{{ $key + 1 }}</td>
                    <td >{{$value->appointment_number ?? ''}}</td>
                    <td >{{ucfirst($value->patient->name) ?? ''}} <br>
                    {{$value->patient->mr_no ?? ''}}
                    </td>
                    <td >{{$value->patient->contact_no?? ''}}
                    <td >{{$value->consultant->name ?? ''}}</td>
                    <td >{{$value->opd_type->name ?? ''}}</td>

                    <td >{{$value->appointment_date ?? ''}}</td>
                    <td >{{$value->fee ?? ''}}</td>
                    <td >{{$value->hospital_share ?? ''}}</td>
                    <td >{{$value->consultant_share ?? ''}}</td>
                    <td >{{$value->created_by_user->name ?? ''}}</td>

                </tr>
            @endforeach
            <tr>
                <td ></td>
                <td ></td>
                <td ></td>
                <td ></td>
                <td ></td>
                <td ></td>
                <td ></td>
                <td style="font-weight: bold" >{{ $totalFees }}</td>
                <td style="font-weight: bold">{{$totalHospitalShare}}</td>
                <td style="font-weight: bold">{{$totalConsultantShare}}</td>
                <td ></td>
            </tr>
            </tbody>

        </table>
        @endif

        @if(count($investigations))
            <p style="text-align: center; font-weight: bold; font-size:15px;margin-top: 6px">Investigations Payments</p>

            <table >
                <thead>
                <tr>
                    <th style="width: 5%;">S.No</th>
                    <th style="width: 6%; ">Invoice#</th>
                    <th style="width: 6%; ">Patient</th>
                    <th style="width: 6%; ">Remarks</th>
                    <th style="width: 6%; ">Created By</th>
                    <th style="width: 6%; ">Created At</th>
                    <th style="width: 6%; ">Amount</th>

                </tr>
                </thead>
                <tbody>
                <?php $investigation_total = 0; ?>
                @foreach($investigations as $key => $value)
                    <?php $investigation_total = ($investigation_total) + ($value->amount); ?>
                    <tr>
                        <td>{{$key + 1}}</td>
                        <td>
                            <a target="_blank" href="{{route('pos.print_hospital_lab_invoice',[$value->invoice_no ?? 0])}}">
                            {{$value->invoice_no}}
                        </a>
                        </td>
                        <td>{{$value->patient?->name ?? ""}}</td>
                        <td>{{str_replace("_"," ",$value->remarks)}}</td>
                        <td>{{$value->createdBy?->name ?? ''}}</td>
                        <td>{{date("d-m-Y H:i A",strtotime($value->created_at))}}</td>
                        <td>{{$value->amount}}</td>

                    </tr>
                @endforeach
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="font-weight: bold">Total</td>
                    <td style="font-weight: bold">{{$investigation_total}}</td>

                </tr>
                </tbody>
            </table>
        @endif

        @if(count($sale))
            <p style="text-align: center; font-weight: bold; font-size:15px;margin-top: 6px">Pharmacy Sale Details</p>

            <table >
                <thead>
                <tr>
                    <th style="width: 5%;">S.No</th>
                    <th style="width: 6%; ">Invoice#</th>
                    <th style="width: 6%; ">Patient</th>
                    <th style="width: 6%; ">Created By</th>
                    <th style="width: 6%; ">Amount</th>
                    <th style="width: 6%; ">Date</th>

                </tr>
                </thead>
                <tbody>
                <?php $sale_total = 0; ?>
                @foreach($sale as $key => $value)
                    <?php $sale_total = ($sale_total) + ($value->amount); ?>
                    <tr>
                        <td>{{$key + 1}}</td>
                        <td>
                            <a target="_blank" href="{{route('pos.print_customer_bill',[$value->sale_id ?? 0])}}">
                                {{$value->sale?->InvoiceNo ?? "In-Patient Amount Received on Discharge"}}
                        </a>
                    </td>
                        <td>{{$value->patient?->name ?? ""}}</td>
                        <td>{{$value->createdBy?->name ?? ''}}</td>
                        <td>{{$value->amount}}</td>
                        <td>{{date("d-m-Y H:i A",strtotime($value->created_at))}}</td>

                    </tr>
                @endforeach
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="font-weight: bold">Total</td>
                    <td style="font-weight: bold">{{$sale_total}}</td>
<td></td>
                </tr>
                </tbody>
            </table>
        @endif

        @if(count($pharmacy_item_returns) > 0)
            <p style="text-align: center; font-weight: bold; font-size:15px;margin-top: 6px">Pharmacy Sale Details</p>
            <table style="width: 100%">
                <tr>
                    <th style="width: 10%">S.No</th>
                    <th style="width: 20%">Product Name</th>
                    <th style="width: 10%">Return Qty</th>
                    <th style="width: 10%">Unit Price</th>
                    <th style="width: 10%">Return Amount</th>
                    <th style="width: 10%">Return By</th>
                </tr>
                <?php $total_return=0; ?>
                @foreach($pharmacy_item_returns as $key => $value)
                    <?php $total_return = ($total_return) + ($value->returnAmount ?? 0); ?>
                    @if($value->admission_id == 0 || $value->admission_id == "null")

                        <tr>
                            <td style="width: 5%">{{$key + 1}}</td>
                            <td style="width: 15%">{{$value->product->ProductName ?? ''}}</td>
                            <td style="width: 10%">{{$value->ReturnQuantity}}</td>
                            <td style="width: 10%">{{$value->UnitePrice}}</td>
                            {{--<td style="width: 10%">{{($value->ReturnQuantity) * ($value->UnitePrice)}}</td>--}}
                            <td style="width: 10%">{{$value->returnAmount ?? "0"}}</td>
                            <td style="width: 10%">{{$value->return_by_user->name ?? ''}}</td>

                        </tr>
                    @endif
                @endforeach
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><b>Total:</b></td>
                    <td><b>{{$total_return}}</b></td>
                    <td></td>
                </tr>
            </table>


       @endif
    </section>




</div>
</body>

</html>