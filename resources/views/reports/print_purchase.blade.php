<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">


    <style>
        body {
            overflow-x: hidden;

        }

        p {
            font-size: 14px
        }

        table body td:first-child table thead th:first-child {
            width: 20%;
        }


        th {
            width: 10%;
            font-size: 12px !important;
        }

        th,
        td {
            font-size: 12px;
            padding: 0px 3px !important;
        }

        .wrapper {
            overflow: hidden;
        }

        @media print {
            p {
                font-size: 12px
            }

            th,
            td {
                font-size: 10px;
                padding: 0px 3px !important;
            }

            th {
                width: 10%;
                font-size: 8px !important;
            }

        }
    </style>
</head>

<body>


    <div class="wrapper">

        <header class="mb-3">
            <div class="col-md-6 mx-auto">
                <div class="row">
                    <div class="col-12 text-cente">
                        <p class="m-0">Invoice#: {{ $grn_request->InvoiceNo }}</p>
                        <h4 class="mb-1">{{ $supplier->Name }}</h4>
                    </div>
                    <div class="col-6">

                        <p class="m-0">Contact #: {{ $supplier->ContactNo }}</p>
                        <p class="m-0">Email #: {{ $supplier->Email }}</p>
                    </div>
                    <div class="col-6">
                        <p class="m-0">Address: {{ $supplier->Address }}</p>
                        <p class="m-0">Market: {{ $supplier->market->name ?? '' }}</p>
                    </div>
                </div>
            </div>
        </header>


        <div class="row">
            <div class="col-12">
                <div class="card border-0">
                    <div class=" ">


                        <input type="hidden" name="GRNID" value="{{ $id }}">

                        <table class="table table-bordered  table-responsive m-0">
                            <thead>
                            <tr>
                                <th style="width: 5%">#</th>
                                <th style="width: 20%">Name</th>
                                <th style="width: 5%">Qty</th>
                                <th style="width: 5%">Units</th>
                                <th style="width: 10%">Pack Price</th>

                                <th style="width: 5%">GST Amt</th>

                                <th style="width: 5%">Adv.Tax Amt</th>
                                <th style="width: 6%">Dis.%</th>
                                <th style="width: 6%">Disc Amt</th>
                                <th style="width: 5%">Adv.Tax%</th>

                                <th style="width: 8%">GST%</th>
                                <th style="width: 5%">Expiry</th>
                                <th style="width: 10%">Total</th>

                            </tr>
                            </thead>
                            @php
                                $gstTotalAmount = 0;
                                $advanceTaxTotalAmount = 0;
                                $grantTotalAmount = 0;
                                $grandtotalDiscount = 0;
                            @endphp
                            <tbody>
                            @foreach ($purchase as $key => $item)
                                @php
                                    $gstTotalAmount += $item->gst_tax_amount;
                                    $qty = $item->Quantity / $item->pack_size;
                                   // $totalAmount = $qty * $item->pack_price;
                                   $totalAmount = $item->Quantity * $item->UnitPrice;
                                   $discount = $item->discount / 100;
                                   $discountAmount = $totalAmount * $discount;
                                   $grandtotalDiscount += $discountAmount;

                                    $grantTotalAmount += $totalAmount;
                                    //dd($grantTotalAmount);
                                    $advanceTaxTotalAmount += $item->advance_tax_amount;
                                @endphp
                                <tr >


                                        <td>{{ $key + 1 }}</td>
                                        <td>

                                            {{ $item->products->ProductName }}
                                        </td>
                                        <td class="text-center">{{ ceil($qty) }} {{--{{ number_format($qty,2) . ' x ' . $item->pack_size }}--}}</td>
                                        <td class="text-center"> {{ $item->Quantity }}</td>

                                        <td>{{ $item->pack_price }} {{--<span style="color: red;font-size: 10px;">UP: {{$item->UnitPrice}}</span>--}}</td>

                                        <td>{{ $item->gst_tax_amount }}</td>
                                        <td>{{ $item->advance_tax_amount }}</td>
                                        <td class="text-center">{{ $item->discount ?? "0" }}%</td>
                                        <td class="text-center">{{$discountAmount}}</td>
                                        <td class="text-center">{{ $item->advance_tax }}</td>

                                        <td class="text-center">{{ $item->gst_tax }}</td>
                                        <td class="text-center">{{ ($item->expiry_date) ? date("Y-m-d",strtotime($item->expiry_date)) :"" }}</td>
                                        <td>{{ $totalAmount }}</td>

                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr >


                                <td colspan="5"></td>
                                <td style="font-weight: bold;">{{ number_format($gstTotalAmount, 2) }}</td>
                                <td style="font-weight: bold;">{{ number_format($advanceTaxTotalAmount, 2) }}</td>
                                <td ></td>
                                <td style="font-weight: bold;">{{$grandtotalDiscount}}</td>
                                <td ></td>
                                <td ></td>
                                <th>Gross Amount:</th>
                                <th> {{ number_format($grantTotalAmount, 2) }}</th>

                            </tr>
                            <tr >
                                <td colspan="11"></td>
                                <th>GST:</th>
                                <th> {{ number_format($gstTotalAmount, 2) }}</th>
                            </tr>
                            <tr >
                                <td colspan="11"></td>
                                <th>Adv.Tax:</th>
                                <th> {{ number_format($advanceTaxTotalAmount, 2) }}</th>
                            </tr>

                            <tr >
                                <td colspan="11"></td>
                                <th>Discount:</th>
                                <th> - {{ number_format($grandtotalDiscount, 2) }}</th>
                            </tr>

                            <tr >
                                <td colspan="11"></td>
                                <th>Net Amount:</th>
                                <th>  {{ number_format( ($grantTotalAmount + $gstTotalAmount + $advanceTaxTotalAmount) - ($grandtotalDiscount), 2) }}</th>
                            </tr>

                            </tfoot>


                        </table>



                    </div>
                </div>



            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/jquery-3.5.1.min.js') }}"></script>
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>

</body>

</html>
