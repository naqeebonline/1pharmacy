<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}" />
    <!-- <link rel="stylesheet" href="style.css"> -->

    <style>
        @media print {
            @page {
                margin: 0;
                /* Removes the default margin */
            }

            body {
                margin: 0;
                padding: 15px;
                /* Ensures the body content aligns with the page */
            }
        }

        @font-face {
            font-family: 'Jameel Noori Nastaleeq Regular';
            src: url('{{asset("Jameel Noori Nastaleeq Regular.ttf")}}') format('truetype');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }

        /* Alternative loading method */
        @font-face {
            font-family: 'JameelNoori';
            src: url('{{asset("Jameel Noori Nastaleeq Regular.ttf")}}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        .inv-header .head {
            border-top: 15px solid #333;
            border-bottom: 5px solid #333;
            padding: 10px;
        }

        .inv-header h4 {
            margin: 0;
            font-size: 14px;
        }

        .inv-header ul {
            margin: 0;
            padding-left: 10px;
        }

        .inv-header ul li {
            font-size: 12px;
            margin: 0;
            line-height: 1.3;

        }

        .inv-header ul li:last-child {
            line-height: .7;
        }

        .inv-header p {
            font-size: 12px;
            margin: 0;
        }

        .logo {
            /* height: 40px; */
            object-fit: contain;
        }

        .pt_detail p strong {
            white-space: nowrap;
        }

        .pt_detail p {
            display: flex;
            font-size: 12px;
        }

        .pt_detail span {
            margin-left: 10px;
            width: 50%;
            display: inline-block;
            border-bottom: 1px solid #333;
        }

        .main {
            padding: 4px 20px;
            width: 100%;
            height: 705px;
            margin-top: 10px;
        }

        .height {
            height: 30px !important;
        }

        .side {
            border-right: 1px solid #333 !important;
        }

        .side .rx-logo {
            width: 40px;
            border: 2px solid #333;
            object-fit: contain;
            position: absolute;
            left: calc(100% + 10px);
            bottom: calc(100% - 10px);
        }

        footer {
            border-bottom: 15px solid #333;
            border-top: 5px solid #333;
            position: relative;
            padding: 5px 0;
            /* bottom:10px;
            left: 50%;
            transform: translateX(-50%);
            width: calc(100% - 20px); */
            position: relative;
        }

        footer h4 {
            font-size: 28px;
            font-weight: bolder;
            font-family: 'Jameel Noori Nastaleeq Regular', Arial, sans-serif;
            padding: 0 20px;
        }

        footer p {
            font-size: 10px;
            color: #000 !important;

        }

        footer a {
            color: #000;
        }

        .ft-sign {
            position: absolute;
            right: 0;
            bottom: calc(100% + 10px);
        }

        .ft-sign p {
            padding-right: 40px;
            font-size: 14px;
            margin-bottom: 5px !important;
        }
    </style>
</head>

<body>

    <div class="wrapper">
        <header class="inv-header">
            <div class="head">
                <div class="row">
                    <div class="col-4">
                        <img class="logo" width="100%" src="{{asset('')}}amc.jpeg" alt="">
                        <h4 style="text-align: center">{{config('app.Branch_Name')}}</h4>
                    </div>

                    <div class="col-4">
                        <h4 style="text-align: center; font-weight: bold;">{{$data->consultant->name ?? ""}}</h4>

                        <ul class="list-unstyled">
                            <li style="text-align: center;">{!! $data->consultant->description !!}</li>

                            {{--<li>{{ucfirst($data->consultant->consultant_speciality->name) ?? ""}}</li>
                            <li>{{ucfirst($data->consultant->consultant_type->name) ?? ""}}</li>
                            <li>PMDC# {{$data->consultant->pmdc_number ?? ""}}</li>--}}

                        </ul>
                        <p style="text-align: center;"><b>PMDC#{!! $data->consultant->pmdc_number !!} </b></p>
                        <p style="text-align: center">{!! DNS1D::getBarcodeSVG($data->appointment_number, 'C128', 2, 20, 'black', false) !!}</p>
                    </div>

                    <div class="col-4">
                        <p><strong>{{date('l, F d, Y', strtotime($data->appointment_date))}} <br /> {{date('h:i A', strtotime($data->appointment_date))}}</strong></p>
                        <p><strong>{{ucfirst($data->opd_type->name) ?? ''}}</strong></p>
                        <p><strong>Appointment#: {{config('app.BRANCH_CODE')}} {{ $data->appointment_number }}</strong></p>

                    </div>

                </div>
            </div>

            <div class="patient_detail pb-3">
                <p class="text-end my-2 me-5 pe-2" style="font-weight: bold">MR# {{config('app.BRANCH_CODE')}} {{ ucfirst($data->patient->mr_no) ?? ''}}</p>

                <div class="row g-0 pt_detail">
                    <div class="name col-6 pe-2 height">
                        <p class=""><strong style="width: 100px; white-space: nowrap;">Patient Name:</strong> <span
                                style="width: 95%; text-align: center;font-weight: bold">{{ucfirst($data->patient->name) ?? ''}}</span></p>
                    </div>

                    <div class="gender col-3 pe-2 height">
                        <p><strong>Gender:</strong> <span style="width: 100%; text-align: center;font-weight: bold">{{ucfirst($data->patient->gender) ?? ''}}</span></p>
                    </div>
                    <div class="age col-3 height">
                        <p><strong>Age:</strong> <span style="width: 95%; text-align: center;font-weight: bold">
                                @if($data->patient->age !="" && $data->patient->age != 0)
                                {{($data->patient->age)}} Years
                                @elseif($data->patient->months !="" && $data->patient->months != 0)
                                {{($data->patient->months)}} Months
                                @else
                                {{($data->patient->days)}} days
                                @endif
                            </span></p>
                    </div>



                    <div class="gender col-4 pe-2 height">
                        <p><strong>Weight (KG)</strong> <span style="width: 100%; text-align: center;font-weight: bold"></span></p>
                    </div>
                    <div class="name col-4 pe-2 height">
                        <p class=""><strong style="width: 100px; white-space: nowrap;">Contact No</strong> <span
                                style="width: 95%; text-align: center;font-weight: bold">{{$data->patient->contact_no ?? ''}}</span></p>
                    </div>

                    <div class="gender col-4 pe-2 height">
                        <p><strong>Address</strong> <span style="width: 100%; text-align: center;font-weight: bold">{{$data->patient->location->name ?? ''}}</span></p>
                    </div>

                    <div class="gender col-12 pe-2 height">
                        <p><strong>Allergies</strong> <span style="width: 100%; text-align: center;font-weight: bold"></span></p>
                    </div>

                    <div class="gender col-12 pe-2 height">
                        <p><strong>Provisional / Diagnosis</strong> <span style="width: 100%; text-align: center;font-weight: bold"></span></p>
                    </div>

                </div>

            </div>

        </header>

        <div class="main">
            <div class="row h-100 ">
                <div class="col-3 side border-end position-relative">
                    <img class="rx-logo" src="{{asset('')}}rx.png" alt="">
                    <p style="font-weight: bold">HX/Complaints</p>
                    <div style="margin-top: 300px !important;">
                        <p>O/E</p>
                        <p>BP ____________</p>
                        <p>Temp ____________</p>
                        <p>Pulse ____________</p>
                        <p>R/R ____________</p>
                        <p>Investigations</p>
                    </div>
                </div>
                <div class="col-9">
                    <div class="row g-0 pt_detail">
                        <div class="name col-4  height mt-4">
                            <p class=""><strong style="width: 100px; "></strong> <span
                                    style="width: 100%;font-weight: bold">Medicine</span></p>
                        </div>

                        <div class="gender col-3  height mt-4">
                            <p><strong></strong> <span style="width: 100%;font-weight: bold">Dose</span></p>
                        </div>
                        <div class="gender col-3  height mt-4">
                            <p><strong></strong> <span style="width: 100%;font-weight: bold">Route</span></p>
                        </div>
                        <div class="gender col-2 pe-2 height mt-4">
                            <p><strong></strong> <span style="width: 100%;font-weight: bold">Days</span></p>
                        </div>
                    </div>

                    @if(isset($medications) && $medications->count() > 0)
                    <div class="medications-list mt-3">
                        @foreach($medications as $index => $med)
                        <div class="row g-0 pt_detail mb-2">
                            <div class="name col-4 height">
                                <p><strong>{{ $index + 1 }}.</strong> <span style="width: 90%; font-weight: bold">{{ $med->product->ProductName ?? 'N/A' }}</span></p>
                            </div>
                            <div class="gender col-3 height">
                                <p><span style="width: 100%; font-weight: bold">
                                        @if($med->dose_type)
                                        @if($med->dose_type == 'TDS')
                                        TDS (صبح، دوپہر، شام)
                                        @elseif($med->dose_type == 'BD')
                                        BD (صبح، شام)
                                        @elseif($med->dose_type == 'OD')
                                        OD (صبح)
                                        @elseif($med->dose_type == 'HS')
                                        HS (رات کو)
                                        @elseif($med->dose_type == 'QID')
                                        QID (ہر 6 گھنٹے بعد)
                                        @else
                                        {{ $med->dose_type }}
                                        @endif
                                        @else
                                        -
                                        @endif
                                    </span></p>
                            </div>
                            <div class="gender col-3 height">
                                <p><span style="width: 100%; font-weight: bold">Oral</span></p>
                            </div>
                            <div class="gender col-2 pe-2 height">
                                <p><span style="width: 100%; font-weight: bold">{{ ceil($med->Quantity / ($med->dose_type == 'TDS' ? 3 : ($med->dose_type == 'BD' ? 2 : 1))) }} days</span></p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <p style="margin-top: {{ isset($medications) && $medications->count() > 0 ? '300px' : '500px' }}; font-weight: bold;">Adv:</p>
                </div>
            </div>
        </div>



        <footer class="footer">

            <div class="text-end ft-sign">

                <p class="m-0"><strong>Sign & Stamp</strong></p>
                <p class="m-0"><strong>Not Valid For Court Purpose</strong></p>
            </div>
            <div class="row align-items-center">
                <div class="col-8  ">
                    <h4 class="my-0">{{config('app.COMPANY_ADDRESS')}}</h4>
                </div>
                <div class="col-4">
                    <p class="my-0"><strong>Website: <a href="#">{{config('app.COMPANY_WEBSITE')}}</a></strong></p>
                    <p class="my-0"><strong>Mob: {{config('app.COMPANY_PHONE')}} , {{config('app.COMPANY_MOBILE')}}</strong></p>
                    <p class="my-0"><strong>Email: {{config('app.COMPANY_EMAIL')}}</strong></p>
                </div>
            </div>
        </footer>



    </div>


</body>
<script>
    window.onload = function() {
        window.print(); // Automatically triggers the print dialog
    };
    /* window.onafterprint = function() {
         window.close(); // Close the window after printing
     };*/
</script>

</html>