<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}" />
    {{-- <link rel="stylesheet" href="style.css">
     --}}
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
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
            font-size: 12px;
            margin: 0;
            /* font-weight: 600; */
            line-height: 1.2;
            color: #000;
        }

        table {
            border-collapse: collapse;
        }

        table th,
        td {
            padding: 0 15px;
            /* border: 1px solid #000; */
            font-size: 14px;
        }

        .logo {
            margin-bottom: 10px;
        }

        .logo img {}

        .invoice-table th {
            background-color: #B2BBC3;
            font-weight: 600;
            padding: 0 3px;
        }

        .text-blue {
            color: rgb(0, 151, 211);
        }

        .text-red {
            color: rgb(231, 12, 12);
        }

        body {
            padding: 10px 20px;
            background-color: #fff;
            /* min-height: 11in; */
            padding-bottom: 300px;
        }

        .invoice-container {
            max-width: 800px;
            height: 12in;
            margin: 0px auto;
            background: #fff;
            border-radius: 5px;
        }



        .inv-detail .fw-bold {
            font-weight: 600 !important;
        }

        .inv-detail p {
            font-size: 12px;
            color: #000 !important;
            line-height: 1.4;
        }

        .invoice-details>div {
            width: 50%;
        }

        .bar {
            margin-top: 40px;
        }

        .bar h5:first-child {
            text-decoration: underline;
            font-style: italic;
        }

        .bar h5 {
            border: 2px dashed grey;
            font-size: 14px;
            font-weight: 600;
            color: #000;
            line-height: 1.7;
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
            margin-bottom: 0px;
            border-color: gray;
        }


        .items table td {
            text-align: left;
            font-size: 14px;
        }

        .items table thead {
            border-top: transparent;
        }

        .items table th,
        .items table td {
            padding: 1px 10px;
            border: none;
            color: #000;
            border-color: #B2BBC3;


        }

        .items table tbody tr td:nth-child(2) {
            background-color: #B2BBC3;
        }



        .totals {
            display: flex;
            justify-content: end;
        }

        .totals table strong {
            font-size: 14px !important;
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
            position: absolute;
            bottom: 0;
            margin-top: 20px;
            font-size: 14px;
            color: #000;
            display: block;
        }

        .out-range {
            border-bottom: 2px dashed #000 !important;
        }



        @page {
            size: A4;
        }

        @media print {
            footer {
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                display: block;


            }

            .invoice-container {
                height: auto;
            }


        }
    </style>

</head>

<body>
<div class="invoice-container">

    <header class="header">
        <div class="logo ">
            <img style="height: 50px"
                 src="{{asset('logo.png')}}" alt="">
            <span style="font-weight: bold;font-size: 22px; text-decoration: underline; margin-left: 80px">{{env('COMPANY_NAME')}}</span>
        </div>
    </header>
    <main>

        <div class="inv-detail row">

            <div class="left-side col-5">
                <div class="d-flex justify-content-between">
                    <p class="w-50 fw-bold">M.R #:</p>
                    <p class="w-50">{{ $result->patient->mr_no }}</p>
                </div>

                <div class="d-flex justify-content-between">
                    <p class="w-50 fw-bold">Patient name:</p>
                    <p class="w-50">{{ ucfirst($result->patient->name) }}</p>
                </div>

                <div class="d-flex justify-content-between">
                    <p class="w-50 fw-bold">Age | Gender:</p>
                    <p class="w-50">{{ $result->patient->age }}-Year | {{ ucfirst($result->patient->gender) }}</p>
                </div>

                <div class="d-flex justify-content-between">
                    <p class="w-50 fw-bold">Invoice Date:</p>
                    <p class="w-50">{{ \Carbon\Carbon::now()->format('d-M-Y') }}</p>
                </div>
            </div>

            <div class="right-side col-5 ms-auto">
                <div class="d-flex justify-content-between">
                    <p class="w-50 fw-bold">Refered By:</p>
                    <p class="w-50">{{ $result->consultant->name ?? "" }}</p>
                </div>

                <div class="d-flex justify-content-between">
                    <p class="w-50 fw-bold">Receipt #:</p>
                    <p class="w-50">A{{ date('yh') }} - {{ date('is') }}</p>
                </div>

                <div class="d-flex justify-content-between">
                    <p class="w-50 fw-bold">Patient Status:</p>
                    <p class="w-50">OPD Patient</p>
                </div>

                <div class="d-flex justify-content-between">
                    <p class="w-50 fw-bold">Result Date:</p>
                    <p class="w-50">{{ \Carbon\Carbon::parse($result->inv_out_date)->format('d-M-Y') }}</p>
                </div>
            </div>


        </div>

        <div class="text-center bar">
            <h5 class="my-0 text-decoration-underline text-uppercase">
                {{ $result->subCategory->main_category->name }}</h5>
            <h5 class="my-0 border-0">{{ $result->subCategory->name }}</h5>
        </div>

        <section class="items">
            <table class="table table-bordered invoice-table">
                <thead class="thead-light">
                <tr>
                    <th>Test</th>
                    <th class="text-center">Result</th>
                    <th class="text-center">Unit</th>
                    <th class="text-center">Reference Values</th>
                </tr>





                </thead>
                <tbody>
                @if($inv_sub_category->is_parameter != 0)
                    <?php $default_heading = ($result->investigationResult)[0]->parameter_heading;
                    $default_heading = strtolower($default_heading);
                    ?>
                    @foreach ($result->investigationResult as $key => $item)

                        @php

                            $range_max_value = 0;
                            $range_min_value = 0;

                            if ($result->patient->gender == 'male' && $result->patient->age > 2) {
                                $range_max_value = $item->parameter->male_max ?? '';
                                $range_min_value = $item->parameter->male_min ?? '';
                            } elseif ($result->patient->gender == 'female' && $result->patient->age > 2) {
                                $range_max_value = $item->parameter->female_max ?? '';
                                $range_min_value = $item->parameter->female_min ?? '';
                            } else {
                                $range_max_value = $item->parameter->child_max ?? '';
                                $range_min_value = $item->parameter->child_min ?? '';
                            }

                        @endphp
                        <?php if(($default_heading != strtolower($item->parameter_heading) || $key == 0) && $default_heading != 'no heading'){ $default_heading =strtolower($item->parameter_heading); ?>
                        <tr>
                            <td colspan="4" style="font-weight: bold; font-size: 16px; font-style: oblique; text-decoration: underline">{{strtoupper($default_heading)}}</td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <td style="margin-left: 20px">{{ $item->parameter->name ?? '' }}</td>
                            @if ($item->result_text_value != '')
                                <td
                                        class="text-center {{ strtolower($item->result_text_value) == 'positive' ? 'fw-bold out-range' : '' }}">
                                    {{ ucfirst($item->result_text_value) }} </td>
                            @else
                                @if(is_numeric($item->result_value))
                                    <td
                                            class="text-center {{ $item->result_value > $range_max_value || $item->result_value < $range_min_value ? 'fw-bold out-range' : '' }}">

                                        <?php

                                        if (strpos($item->result_value, ':') == false) {
                                            echo  number_format($item->result_value,1) ;
                                        } else {
                                            echo $item->result_value;
                                        }

                                        ?>
                                    </td>
                                @else
                                    <td class="text-center">
                                        {{$item->result_value}}
                                    </td>
                                @endif
                            @endif


                            <td class="text-center">{{ $item->parameter->unit ?? '' }}</td>

                            @if ($item->result_text_value != '' && $inv_sub_category->is_ict)
                                <td class="text-center">-</td>
                            @else
                                @if ($result->patient->gender == 'male' && $result->patient->age > 2)
                                    <td class="text-center">{{ number_format($item->parameter?->male_min, 1) }} -
                                        {{ number_format($item->parameter?->male_max,1) }}</td>
                                @elseif($result->patient->gender == 'female' && $result->patient->age > 2)
                                    <td class="text-center">{{ number_format($item->parameter?->female_min,1) }} -
                                        {{ number_format($item->parameter?->female_max,1) }}</td>
                                @else
                                    <td class="text-center">{{ number_format($item->parameter?->child_min) }} -
                                        {{ number_format($item->parameter?->child_max,1) }}</td>
                                @endif
                            @endif

                        </tr>
                    @endforeach


                </tbody>

            </table>
            @else
                <?php echo ($result->investigationResult[0]->result_text_value); ?>
                <br>
                <br>

            @endif


            @if ($result->inv_comment != '')
                <p class="footer-note mb-2">
                    <strong>Comments:</strong> {{ $result->inv_comment }}
                </p>
            @endif





        </section>

        <p class="footer-note"
           style="margin-top:5px; font-size:12px; font-style:italic; border-top:1px solid #ccc; padding-top:4px;">
            <strong>Disclaimer:</strong> Every diagnostic test has scientific acceptable technology or
            technique-based
            limitations of uncertainty of measurement. False positive or false negative results may occur and do
            not
            fall under
            the domain of negligence.
        </p>
    </main>

    <footer>
        <div class="d-flex justify-content-end ">
            <div class="mb-2">
                <b style="text-decoration: underline;">Lab Technologist/Technician</b>
            </div>
        </div>
        <hr style="border: 2px solid black">
        <div class="row mb-2">
            <div class="col-5">
                <div class="d-flex">
                    <p style="width: 100px;font-weight: bold;">Contact #:</p>
                    <p class="w-50">0938-481111 <br /> 0316-8481111</p>
                </div>
            </div>

            <div class="col-7">
                <div class="d-flex">
                    <p style="width: 100px;font-weight: bold;">Address:</p>
                    <p class="w-100">AMC, Opposite Waleed Filling Station, Gar Munara, District & Tehsil Swabi</p>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-5">
                <div class="d-flex">
                    <p style="width: 100px;" class="fw-bold">Reported By:</p>
                    <p class="w-50">Ahmad Johar</p>
                </div>
            </div>

            <div class="col-7">
                <div class="d-flex">
                    <p style="width: 100px;" class="fw-bold">Print Date:</p>
                    <p class="w-100" id="print-date">{{ \Carbon\Carbon::now()->format('d-M-Y   h:i A') }}</p>
                </div>
            </div>
        </div>

    </footer>
</div>


</body>

</html>
