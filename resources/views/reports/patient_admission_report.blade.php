<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{asset('')}}Jameel Noori Nastaleeq Regular.ttf">
    <!-- <link rel="stylesheet" href="style.css"> -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

        body {
            font-family: "Poppins", serif;
            margin: 0;
            padding: 0;
            color: #333;
        }

        @font-face {
            font-family: 'CustomFont';
            /* Name of the font */
            src: url('{{asset('')}}Jameel Noori Nastaleeq Regular.ttf') format('truetype');
            /* Path to the .ttf file */
            font-weight: normal;
            /* Optional: define font weight */
            font-style: normal;
            /* Optional: define font style */
        }

        strong,
        p {
            font-size: 20px;
            margin: 0;
            /* font-weight: 600; */
            line-height: 1.4;
            color: #000;
        }

        .invoice-container {
            max-width: 800px;
            height: calc(100%);
            margin: 0px auto;
            background: #fff;
            border-radius: 5px;
        }


        footer {
            position: absolute;
            bottom: 0;
            margin-top: 20px;
            font-size: 14px;
            color: #000;
        }

        footer p{
            font-size: 14px
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


            }
        }

        #circular-text {
            font-size: 60px;
            font-weight: bold;
            font-family: "Graduate", serif;
        }

        .logo {
            position: absolute;
            left: 50%;
            top: 230px;
            transform: translateX(-50%);
        }

        .main {
            margin-top: 120px;
        }

        .birth {
            font-family: "Graduate", serif;
            font-weight: bold;
            text-underline-offset: 7px;
        }

        .certify {
            font-family: "Graduate", serif;
            text-underline-offset: 7px;

        }

        .page-break {
            page-break-before: always; /* Forces a new page */
        }

    </style>
    <style>
        .main_body {

            font-family: 'CustomFont', sans-serif;
            direction: rtl;
            text-align: right;
            font-weight: bold;
            margin: 20px;
            font-size: 18px !important;
        }
        .container {
            border: 1px solid #000;
            padding: 16px;
            margin: 0 auto;
            max-width: 800px;
            line-height: 1.8;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.5em;
            font-weight: bold;
            text-decoration: underline;
        }
        .content {
            font-size: 1.1em;
        }
        .signature {
            margin-top: 20px;
            font-size: 1em;
            line-height: 2;
        }
        .signature span {
            display: inline-block;
            width: 200px;
            border-bottom: 1px solid #000;
            margin-left: 10px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 0.9em;
        }
    </style>

</head>

<body>





<div class="invoice-container">

        <header class="header text-center">
            <div class="text-center">
                <h1>{{env('COMPANY_NAME')}}</h1>
                <h4>Registration No: {{ $patient->patient->mr_no }}</h4>
            </div>
            <div class="d-flex justify-content-between mt-4">
                <h5 class="text-end">Sehat Card Ref# : {{ $patient->sc_ref_no }}</h5>

                <h5 class="text-end">Admitted By: {{$patient->user->name ?? ""}}</h5>
            </div>
        </header>
        <main>

            <div class="row justify-content-between my-4 patient-detail ">
                <div class="col-5 my-1 d-flex">
                    <p style="min-width: 150px; font-weight:600">Patient Name:</p>
                    <p class="w-50 text-capitalize"> {{ $patient->patient->name }}</p>
                </div>
                <div class="col-5 my-1 d-flex">
                    <p style="min-width: 150px; font-weight:600">Guardian's Name:</p>
                    <p class="w-50 text-capitalize">{{ $patient->patient->father_husband_name }}</p>
                </div>
                <div class="col-5 my-1 d-flex">
                    <p style="min-width: 150px; font-weight:600">Patient Age:</p>
                    <p class="w-50 text-capitalize">{{ $patient->patient->age }}-years</p>
                </div>
                <div class="col-5 my-1 d-flex">
                    <p style="min-width: 150px; font-weight:600">Gender:</p>
                    <p class="w-50 text-capitalize">{{ $patient->patient->gender }}</p>
                </div>
                <div class="col-6 my-1 d-flex">
                    <p style="min-width: 150px; font-weight:600">Consultant:</p>
                    <p class="w-50 text-capitalize">{{ $patient->consultant->name }}</p>
                </div>
                <div class="col-5 my-1 d-flex ">
                    <p style="min-width: 150px; font-weight:600" >Address:</p>
                    <p class="w-50 text-capitalize">{{ $patient->patient->location->name .", ".$patient->patient->district->name }} </p>
                </div>
                <div class="col-5 my-1 d-flex">
                    <p style="min-width: 150px; font-weight:600">Admission Date:</p>
                    <p class="w-50 text-capitalize">{{ \Carbon\Carbon::parse($patient->admission_date)->format("d-m-Y") }}</p>
                </div>
                <div class="col-5 my-1 d-flex">
                    <p style="min-width: 150px; font-weight:600">CNIC:</p>
                    <p class="w-50 text-capitalize">{{ $patient->patient->cnic }}</p>
                </div>
                @if($patient->discharge_date)
                <div class="col-5 my-1 d-flex">
                    <p style="min-width: 150px; font-weight:600">Discharge Date:</p>
                    <p class="w-50 text-capitalize">{{ $patient->discharge_date }}</p>
                </div>
                @endif
                <div class="col-5 my-1 d-flex">
                    <p style="min-width: 150px; font-weight:600">Contact #:</p>
                    <p class="w-50 text-capitalize">{{ $patient->patient->contact_no }}</p>
                </div>
                <div class="col-5 my-1 d-flex">
                    {{--<p style="min-width: 150px; font-weight:600">SSP Visit No.:</p>
                    <p class="w-50">123----</p>--}}
                </div>
            </div>


            <div class="concern text-center" style="border: 1px solid grey;">
                <h2 class="m-0 text-capitalize py-1">{{ $patient->procedure_type->name }}
                @if($patient->sec_procedure)
                  + {{$patient->sec_procedure->name}}
                @endif
                </h2>
            </div>

            <table class="table table-bordered mt-3" style="border-color: grey;" border="1" cellspacing="0">
                <tbody>
                    <tr>
                        <th>Ward</th>
                        <th>Room</th>
                        <th>Bed Name</th>
                        <th>Status</th>

                    </tr>
                    <tr>
                        <td>{{ $patient->ward->name }}</td>
                        <td>{{ $patient->ward->name }}</td>
                        <td>{{ $patient->bed->name }}</td>
                        <td>{{ $patient->admission_status }}</td>
                    </tr>
                </tbody>
            </table>





        </main>

        <footer>
            <div class="row mb-2">
                <div class="col-5">
                    <div class="d-flex">
                       <p style="width: 100px;">Contact #:</p>
                        <p class="w-50">0938-481111 <br /> 0316-8481111</p>
                    </div>
                </div>

                <div class="col-7">
                    <div class="d-flex">
                        <p style="width: 100px;">Address:</p>
                        <p class="w-100">AMC, Opposite Waleed Filling Station, Gar Munara, District & Tehsil Swabi</p>
                    </div>
                </div>
            </div>

        </footer>
    </div>

@if($patient->procedure_type->type == 'Dialysis')
    <div class="page-break"></div>

<div class="main_body">
    <div class="container">
        <div class="header">اجازات نامہ برائے ڈائیلاسز</div>
        <div class="signature">
            میں مسمی ومسماۃ<span>&nbsp;&nbsp;&nbsp;{{ucfirst($patient->guardian_name) ?? ''}}</span>والد/شوہر<span>&nbsp;&nbsp;&nbsp;-</span>
            ساکن <span>&nbsp;&nbsp;&nbsp;{{$patient->patient->location->name ?? ''}}</span>
            مسمی/مسما<span>&nbsp;&nbsp;&nbsp;{{ucfirst($patient->patient->name) ?? ''}}</span> والد/زوجہ<span>&nbsp;&nbsp;&nbsp;{{ucfirst($patient->patient->father_husband_name) ?? ''}}</span> اپنے مریض یا مریضہ
            سکنہ<span>&nbsp;&nbsp;&nbsp;{{$patient->patient->location->name ?? ''}}</span> محلہ <span>&nbsp;&nbsp;&nbsp;{{$patient->patient->location->name ?? ''}}</span>
            کا ڈائیلاسز اپنی مرضی سےاکاخیل میڈیکل کمپلیکس گاڑ منارہ صوابی میں کرانا چاہتا/چاہتی ہوں۔ مجھے اس سنٹر کے انتطامات، سٹا ف اور ڈاکٹر ز کی قابلیت پر مکمل اعتماد ہے۔ موجودہ وقت میں اس مریض کا میں سب سے زیادہ قریبی اور زمہ دار رشتہ دار ہوں۔ میں اپنے مریض کی ہر قسم اجازت دیتا ہوں اور اگر دوران ڈئیلاسز یا اس کے بعد کسی بھی پیچیدگی کا میں کسی کو بھی ذمہ دار نہیں ٹھراونگا۔
            <br>
            دوران ڈائیلاسز یا اس کے بعد میرے مریض کی موت واقع ہونے کی صورت میں ڈائیلاسز ٹیکنیشن، ڈاکٹرز یا عملے کے کسی فرد کو زمہ دار نہیں ٹھراونگا۔
            <br>
            میرے مریض کا ڈئیلاسز معمولی معاوضے میں کرایہ جارہا ہے۔ میں غیر معمولی سہولیات، آلات، انتطامات کا دعوی نہیں کرونگا۔ میں میرے رشتہ دار کسی قسم کی قانونی چارہ جوئی کا کوئی حق نہیں رکھتے۔
            <br>
            1- میرے مریض کی حالت بیماری انتہائی تشویشناک ہے۔<br>
            2- ڈائیلاسز کی بغیر کوئی چارہ نہیں۔<br>
            3- میرے پاس خون کا انتطام نہیں۔<br>
            4- ڈائیلاسز کی بغیر میرے مریض کے زندہ بچنے کی کوئی امید نہیں۔<br>
            5- ڈائیلاسز کے دوران ڈئیلاسز منسوح ہونے کی صورت میں ڈئیلاسز کٹ چارج کیا جائیگا۔
            <br><br>
            مندرجہ بالا تحریر میں نے خود پڑھا اور سمجھا، پڑھایا اور سمجھا گیا۔
        </div>
        <div class="signature">
            ڈائیلاسز: <span></span> نام ڈاکٹر /ٹیکنیشن: <span>&nbsp;&nbsp;&nbsp;&nbsp;{{$patient->consultant->name ?? ''}}</span><br>
            دستخط انگوٹھا: <span></span> عمر: <span>&nbsp;&nbsp;&nbsp;&nbsp;
                @if($patient->patient->age !="" && $patient->patient->age != 0)
                    {{($patient->patient->age)}} سال
                @elseif($patient->patient->months !="" && $patient->patient->months != 0)
                    {{($patient->patient->months)}} Months
                @else
                    {{($patient->patient->days)}} days
                @endif
                &nbsp;&nbsp;&nbsp;&nbsp;
            </span><br>
            مریض کے ساتھ رشتہ: <span>&nbsp;&nbsp;&nbsp;{{$patient->relation->name ?? ''}}</span> روم نمبر: <span>-</span> وارڈ: <span style="font-size: 14px">&nbsp;&nbsp;&nbsp;{{$patient->ward->name ?? ''}}</span> بیڈ نمبر: <span >&nbsp;&nbsp;&nbsp;{{$patient->bed->name ?? ''}}</span><br>
            شناختی کارڈ نمبر : <span>&nbsp;&nbsp;&nbsp;{{$patient->patient->cnic ?? ''}}</span> ڈائیلاسز ٹیکنیشن: <span></span><br>
            تاریخ: <span></span> فون: <span></span>
        </div>
        <div class="footer">
            © 2025 اکا خیل میڈیکل کمپلیکس گاڑ منارہ صوابی
        </div>
    </div>
</div>
@endif


<div class="page-break"></div>

<div class="nursing_line">
    <div class="containers">
        <div class="logos">
            <img src="{{asset('')}}/amc.jpeg" alt="AMC Logo">
            <div class="headers">اکاخیل میڈیکل کمپلیکس</div>
        </div>
        <div class="contents">
            <p>میں مسمی <span class="lines">&nbsp;&nbsp;&nbsp;{{ucfirst($patient->guardian_name) ?? ''}}</span> والد/شوہر <span class="lines"> &nbsp;&nbsp;&nbsp;-  </span></p><br>
            <p>ساکن <span class="lines"> &nbsp;&nbsp;&nbsp;{{$patient->patient->location->name ?? ''}}</span></p><br>
            <p>مسمی/مسماہ <span class="lines">&nbsp;&nbsp;&nbsp;{{ucfirst($patient->patient->name) ?? ''}}</span> والد/زوجہ <span class="lines">&nbsp;&nbsp;&nbsp;{{ucfirst($patient->patient->father_husband_name) ?? ''}} </span></p><br>
            <p>سکنہ <span class="lines">&nbsp;&nbsp;{{$patient->patient->location->name ?? ''}}</span> محلہ <span class="lines"> &nbsp;&nbsp;&nbsp;{{$patient->patient->location->name ?? ''}}</span></p><br>
            <p>شناختی کارڈ نمبر</p><br>
            <div class="grids">
                @foreach($cnic_array as $key => $value)

                    <div>{{$value}}</div>
                    @if($key == 5 || $key == 12)
                        <div>-</div>
                    @endif
                @endforeach

            </div>
            <br><p>
                میرا اکاخیل میڈیکل کمپلیکس گاڑ منارہ میں <span class="lines" style="font-size: 14px">&nbsp;&nbsp;{{$patient->procedure_type->name ?? ''}}</span> اپریشن ہوا ہے۔ میرا یہاں کے سٹاف سے کوئی شکایت نہیں ہے۔
                اور میرا کوئی خرچہ نہیں ہوا اور نہ ہی باہر سے کوئی دوائی یا ٹیسٹ کے پیسے لگے ہیں۔ اور گھر کے لئے بھی دوائی مل گئی ہے۔
            </p>
            <p><strong>نوٹ:</strong> میرا ہسپتال سے کسی قسم کی کوئی شکایت نہیں۔</p>
        </div>
        <div class="signaturess">
            <div>
                <br><br>
                <p>Mobile No: </p>
                <p>Patient/Attendant Sign and Thumb</p>
            </div>
            <div>
                <br>
                <br>
                <p>Administration / Focal Person Sign & Stamp</p>
            </div>
        </div>
        <div class="footers" style="font-weight: bold; font-size: 18px">
            اکاخیل میڈیکل کمپلیکس نزد ولید فلنگ سٹیشن گاڑ منارہ ضلع و تحصیل صوابی
            <br>
            Website: www.amch.org.pk | Mobile: 0333-6941111, 0316-6941111 | Email: info@amch.org.pk
        </div>
    </div>
</div>
<style>
    .nursing_line {
        font-family: 'CustomFont', sans-serif;
        direction: rtl;
        text-align: right;
        font-weight: bold;
        margin: 20px;
        font-size: 18px !important;
    }
    .containers {
        border: 2px solid #007bff;
        padding: 20px;
        margin: 0 auto;
        max-width: 800px;
        line-height: 2.4;
    }
    .headers {
        text-align: center;
        font-size: 1.5em;
        font-weight: bold;
        margin-bottom: 10px;
    }
    .logos {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    .logos img {
        width: 170px;
    }
    .contents {
        font-size: 18px;
        line-height: 1.8;
    }
    .contents .lines {
        display: inline-block;
        border-bottom: 1px solid #000;
        width: 30%;
        margin-bottom: 5px;
    }
    .contents .grids {
        display: grid;
        grid-template-columns: repeat(15, 1fr);
        gap: 5px;
    }
    .contents .grids div {
        border: 1px solid #000;
        text-align: center;
        padding: 5px;
    }
    .footers {
        margin-top: 20px;
        text-align: center;
        font-size: 0.9em;
        border-top: 1px solid #000;
        padding-top: 10px;
    }
    .signaturess {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }
    .signaturess div {
        text-align: center;
        width: 45%;
        border: 1px solid #000;
        padding: 10px;
    }
</style>


    <script src="{{ asset('assets/js/circletype.min.js') }}"></script>

    <script>
        new CircleType(document.getElementById("circular-text")).dir(1).radius(380);
    </script>

</body>

</html>