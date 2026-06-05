<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Birth Certificate</title>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">

    <style>


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
            margin-top: 70px;
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

        @media print {
            footer {
                position: absolute;
                bottom: 0px;
                left: 0;
                width: 100%;
            }
        }

        .page-break {
            page-break-before: always; /* Forces a new page */
        }
        .center_text{
            text-align: center !important;
        }
    </style>
</head>

<body>
   @foreach($baby as $key => $value)

       <div id="circular-text">{{env('COMPANY_NAME')}}</div>

       <div class="logo">
           <img src="{{ asset('logo.png') }}" alt="">
       </div>



       <div class="main" style="margin-left: 40px">
           <div class="">
               <h2 class="text-center text-decoration-underline birth">BIRTH CERTIFICATE</h2>
               <div class="row my-4">
                   <div class="col-2 "><strong class="me-2">S.#:</strong><br> {{$value->id}}</div>
                   <div class="col-3 "><strong class="me-2">Referral #:</strong><br> {{$value->admission->sc_ref_no ?? ''}}</div>
                   <div class="col-3 "><strong class="me-2">MR #:</strong><br> {{$value->patient->mr_no ?? ''}}</div>
                   <div class="col-4 "><strong class="me-2">Issued on:</strong><br> {{date("Y-m-d h:i A")}} </div>
               </div>
           </div>

           <div class="mt-2 pt-3">
               <h3 class="text-center fw-bold certify text-decoration-underline">THIS IS CERTIFY THAT</h3>
               <div class="row mt-4">
                   <div class="col-3 mb-4"><strong class="me-2">Baby Name<br></strong> {{$value->baby_name}}</div>
                   <div class="col-3 mb-4"><strong class="me-2">Birth Date<br></strong> {{date("d-m-Y",strtotime($value->dob))}} </div>
                   <div class="col-3 mb-4"><strong class="me-2">Birth Time<br></strong> {{date("h:i A",strtotime($value->dob))}}</div>



                   <div class="col-3 mb-4"><strong class="me-2">Gender<br></strong> {{$value->baby_gender}}</div>
                   <div class="col-3 mb-4"><strong class="me-2">Mother Name<br></strong> {{$value->mother_name}}</div>
                   <div class="col-3 mb-4"><strong class="me-2">Father Name<br></strong> {{$value->father_name}}</div>
                   <div class="col-3 mb-4"><strong class="me-2">Mother/Father CNIC <br></strong> {{$value->father_mother_cnic}}</div>
                  {{-- <div class="col-3 mb-4"><strong class="me-2">Address<br></strong> </div>--}}
                   <div class="col-3 mb-4"><strong class="me-2">Admission Date<br></strong> {{date("d-m-Y h:i A",strtotime($value->admission->admission_date))}}</div>

               </div>
           </div>
       </div>




       <footer>
           <div class="row mt-5">
               <div class="col-5"><strong class="me-2">Discharged On: {{$value->admission->discharge_date ?? ''}}</strong> </div>
               <div class="col-2"></div>
               <div class="col-5"><strong class="me-2">Doctor Sign/Stamp:</strong> </div>
           </div>

           <div class="row mt-3">
               <div class="col-12 text-center"><strong class="me-2">Address:</strong> AMC, Opposite Waleed Filling Station,
                   Gari Munara, District & Tehsil Swabi</div>
           </div>
       </footer>

       {{--<div class="page-break"></div>--}}

   @endforeach

<script src="{{ asset('assets/js/circletype.min.js') }}"></script>

<script>
    new CircleType(document.getElementById("circular-text")).dir(1).radius(380);
</script>
   <script>
       window.onload = function() {
           window.print(); // Automatically opens the print dialog when the page loads
       };
   </script>
</body>

</html>