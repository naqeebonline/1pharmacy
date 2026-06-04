@extends('layouts.' . config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('stylesheets')
    <style>
        .checkbox-grid {
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            gap: 7px;
            text-align: center;
            margin-bottom: 20px;
            float: left;
            margin-left: 43px;
            margin-top: -26px;


        }


        label {
            font-weight: bold;
        }

        input[type="checkbox"] {
            /* 1.5 times bigger */


        }

        .table> :not(caption)>*>* {
            padding: 5px;
        }
    </style>

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    {{-- <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script> --}}
@endpush

@section('content')
    <div class="row">
        <div class="col-12">


            <div class="row ">

                <div class="col-md-12 mb-4">
                    <div class="card">
                        <form id="add_in_patient_payment" method="post" action="{{ route('pos.calculate_patient_discharge_amount') }}">
                            @csrf
                            <div class="card-body">
                                <h5 style="text-align: center;">Discharge Form</h5>


                                <div class="row">
                                    <div class="col-md-3">
                                        <input type="hidden" value="{{$admission->patient_id ?? 0}}" id="patient_id" name="patient_id"
                                               class="form-control" placeholder="" autocomplete="off">

                                        <input type="hidden" value="{{$admission->id ?? 0}}" id="admission_id" name="admission_id"
                                               class="form-control" placeholder="" autocomplete="off">


                                    </div>

                                </div>




                                <div class="mt-2" id="patient_detail">

                                    <table class="table table-responsive table-bordered">
                                        <tr>
                                            <td style="font-weight: bold">MRNO</td>
                                            <td>{{ $patient->mr_no ?? '' }}</td>
                                            <td style="font-weight: bold">Name</td>
                                            <td>{{ $patient->name ?? '' }}</td>
                                            <td style="font-weight: bold">Father Name</td>
                                            <td>{{ $patient->father_husband_name ?? '' }}</td>
                                            <td style="font-weight: bold">Contact Number</td>
                                            <td>{{ $patient->contact_no ?? '' }}</td>
                                        </tr>

                                        <tr>
                                            <td style="font-weight: bold">Ward No</td>
                                            <td>{{ $admission->ward->name ?? '' }}</td>
                                            <td style="font-weight: bold">Bed No</td>
                                            <td>{{ $admission->bed->name ?? '' }}</td>
                                            <td style="font-weight: bold">Admission Date</td>
                                            <td>{{ $admission->admission_date ?? '' }}</td>
                                            <td style="font-weight: bold">Discharge On</td>
                                            <td>{{ $patient->discharge_date ?? '' }}</td>
                                        </tr>

                                    </table>



                                </div>

                            </div>

                            <div class="card-body">
                            <div class="row">
                                <h4 style="text-align: center;color:green">Procedure Name: {{$admission->consultant_procedure->procedure->name ?? ""}}  <span style="color: red;">Note: Medicine Included</span></h4>
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td width="40%" >Procedure Amount</td>
                                            <td width="60%">
                                                @if(getUserRole() == "Super Admin")
                                                <input type="number" name="procedure_rate" value="{{ rtrim(rtrim(number_format($admission->procedure_rate, 2, '.', ''), '0'), '.') }}" >
                                                @else
                                                    {{$admission->procedure_rate ?? 0}}
                                                @endif

                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="40%" >Advance Received</td>
                                            <td width="60%">
                                                {{$admission->advance_payment ?? 0}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="40%" >Security Received</td>
                                            <td width="60%">
                                                {{$admission->security_amount ?? 0}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="40%" >Payment Received</td>
                                            <td width="60%">
                                                {{$total_received_payment ?? 0}}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="60%" style="color:red">Total Payment Received form Patient</td>
                                            <td width="40%">
                                                {{ $total_payment_paid_by_patient =  ($total_received_payment ?? 0) + $admission->advance_payment ?? 0}}
                                                <a href="{{route('pos.in_patient_payment')}}?patient_id={{$admission->patient_id}}">Receive Payment</a>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="40%" style="border-top: 2px solid black; font-weight: bold;" >Balance</td>
                                            <td width="60%" style="border-top: 2px solid black;font-weight: bold;">
                                                <input type="text" disabled id="left_hand_balance" value="{{ ($total_payment_paid_by_patient) - ($admission->procedure_rate ?? 0)}}">
                                            </td>
                                        </tr>

                                        {{--<tr>
                                            <td width="40%" >Total Service Charges</td>
                                            <td width="60%">
                                                {{$total_service_charges ?? 0}}
                                            </td>
                                        </tr>--}}




                                        @if($admission->admission_status == "Admit")
                                            <tr>
                                                <td width="40%" style="font-weight: bold;"></td>
                                                <td width="60%">
                                                    <button  class="btn btn-success" id="save_my_btn"  type="submit">Submit</button>
                                                </td>

                                            </tr>
                                        @endif

                                    </table>
                                    <ul>
                                        <li><a target="_blank" href="{{route('pos.print_admitted_patient_medicine_list',[$admission_id])}}">Print Medicine List</a></li>
                                        <li><a target="_blank" href="{{route('pos.in_patient_payments_receiving_bill',[$admission_id])}}">Print Patient Payment Slip</a></li>
                                    </ul>
                                </div>

                                <div class="col-md-6">

                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="50%" >Procedure Amount: {{$admission->procedure_rate ?? 0}}</th>
                                            <th width="50%">
                                                @if(!$is_service_charges_posted)
                                                    <span style="color: red">Please click submit button to post services charges</span>
                                                @endif
                                            </th>
                                        </tr>

                                        <tr>
                                            <th width="50%" >Consultant Charges</th>
                                            <th width="50%">
                                                @if(getUserRole() == "Super Admin")
                                                    <input type="text"  id="consultant_charges" style="height: 30px !important;" name="consultant_charges" value="{{$admission->consultant_charges}}" class="form-control" placeholder="" autocomplete="off">
                                                @else
                                                    {{$admission->consultant_charges ?? 0}}
                                                @endif

                                            </th>
                                        </tr>
                                        @foreach($service_type as $key => $value)


                                            <tr>
                                                <td width="50%" >{{$value->name}}
                                                    @if($value->type !="Fixed")
                                                        <span style="color: red;font-size: 10px;float: right">({{$value->price}} * {{$total_days}} days) = ({{$value->price * $total_days}})</span>
                                                    @endif
                                                </td>
                                                <td width="50%">
                                                    <input type="hidden"  id="amount" style="height: 30px !important;" name="service_charges_id[]" value="{{$value->id}}" class="form-control" placeholder="" autocomplete="off">
                                                    @if($value->type !="Fixed")
                                                        <input  type="number" required id="amount" style="height: 30px !important; width: 50% !important;" name="service_charges_amount[]" value="{{$value->patient_charges->service_rate ?? $value->consultant_charges}}" class="form-control" placeholder="" autocomplete="off">
                                                    @else
                                                        <input  type="number" required id="amount" style="height: 30px !important; width: 50% !important;" name="service_charges_amount[]" value="{{$value->patient_charges->service_rate ?? ($value->consultant_charges)}}" class="form-control" placeholder="" autocomplete="off">
                                                    @endif
                                                </td>

                                            </tr>
                                        @endforeach

                                        <tr>
                                            <td width="50%" >Investigation</td>
                                            <td width="50%">
                                                {{$investigation['total'] ?? 0}} &nbsp;&nbsp;&nbsp; Balance: {{ ($investigation['total'] ?? 0) - ($investigation['received'] ?? 0)}}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="50%" >Pharmacy</td>
                                            <td width="50%">
                                                {{round($pharmacy['total']) ?? 0}} &nbsp;&nbsp;&nbsp; Balance: {{ (round($pharmacy['total']) ?? 0) - (round($pharmacy['received']) ?? 0)}}
                                            </td>
                                        </tr>



                                        <tr>
                                            <td width="50%" style="border-top: 2px solid black;font-weight: bold" >Total</td>
                                            <td width="50%" style="border-top: 2px solid black; font-weight: bold">
                                                {{ $total = round(($total_service_charges) + ($investigation['total']) + (round($pharmacy['total'])))  }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="50%" style="border-top: 2px solid black;font-weight: bold" >Balance</td>
                                            <td width="50%" style="border-top: 2px solid black; color: red;font-weight: bold">

                                                <input type="text" disabled id="right_hand_balance" value="{{ ($admission->procedure_rate) - round($total) }}">
                                            </td>
                                        </tr>






                                    </table>
                                </div>

                                <div class="col-md-12">
                                    <div class="btn btn-primary" id="discharge_patient">Discharge Patient</div>
                                </div>
                            </div>

                            </div>
                        </form>

                    </div>





                </div>
            </div>




        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-responsive/datatables.responsive.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.form.min.js') }}"></script>

    <script>
        setTimeout(function() {

            $('#admit_patient').select2();
            $('#service_type_id').select2();

        }, 1000);

        var left_hand_balance = $("#left_hand_balance").val();
        var right_hand_balance = $("#right_hand_balance").val();
        var admission_status = "{{$admission->admission_status}}";
        var is_service_charges_posted = "{{$is_service_charges_posted}}";
        if(left_hand_balance == 0 && right_hand_balance == 0 && admission_status == "Admit" && is_service_charges_posted == true){
                $("#discharge_patient").show();
        }else{
            $("#discharge_patient").hide();
        }

        $("body").on("click", "#discharge_patient", function(e) {
            $("#discharge_patient").hide();
            var total_payment_paid_by_patient = "{{$total_payment_paid_by_patient ?? 0}}";
            var investigations = "{{$investigation['total']}}";
            var pharmacy_sale = "{{round($pharmacy['total'])}}";
            if (confirm('Are you sure to delete this record ?')) {
                $.ajax({
                    type: 'post',
                    url: "{{ route('pos.primary_discharge_in_patient') }}",
                    data: {
                        patient_id: "{{$admission->patient_id}}",
                        admission_id: "{{$admission->id}}",
                        total_payment_paid_by_patient: total_payment_paid_by_patient,
                        investigations: investigations,
                        pharmacy_sale: pharmacy_sale,
                        _token: '{{ csrf_token() }}'

                    },
                    success: function(res) {
                        window.location.reload();
                    }
                })
            } else {
                $("#discharge_patient").show();
                //alert('Why did you press cancel? You should have confirmed');
            }

        });


        $("body").on("change", "#admit_patient", function(e) {
            alert("hi");
            $('#investigation_table').DataTable().destroy();
            $('#service_charges_table').DataTable().destroy();
            $('#patient_treatment_table').DataTable().destroy();
            let selectedOption = e.target.options[e.target.selectedIndex];

            let data = JSON.parse(selectedOption.getAttribute("data-item"));;


            $("#patient_detail").html("");
            let html = `
                <table class="table table-responsive table-bordered" >
                    <tr>
                        <td style="font-weight: bold">MRNO</td>
                        <td>${data.patient.mr_no}</td>
                        <td style="font-weight: bold">Name</td>
                        <td>${data.patient.name}</td>
                        <td style="font-weight: bold">Father Name</td>
                        <td>${data.patient.father_husband_name}</td>
                        <td style="font-weight: bold">Contact Number</td>
                        <td>${data.patient.contact_no}</td>
                    </tr>

                    <tr>
                        <td style="font-weight: bold">Ward No</td>
                        <td>${data.ward.name ?? ""}</td>
                        <td style="font-weight: bold">Bed No</td>
                        <td>${data.bed.name ?? ""}</td>
                        <td style="font-weight: bold">Admission Date</td>
                        <td>${data.admission_date ?? ""}</td>
                        <td style="font-weight: bold">Discharge On</td>
                        <td>${data.discharge_date ?? ""}</td>
                    </tr>
                    </table/>`;

            $("#patient_detail").html(html);


            let patient_id = selectedOption.getAttribute("data-patient");
            let admission_id = selectedOption.getAttribute("data-admission");

            $("#patient_id").val(patient_id);
            $("#admission_id").val(admission_id);





        });


        $("body").on("change", "#service_type_id", function(e) {
            let selectedOption = e.target.options[e.target.selectedIndex];
            let rate = selectedOption.getAttribute("data-rate");
            $("#service_rate").val(rate);
        });








         $("body").on("click", ".delete_service_record", function(e) {
            var id = $(this).attr("data-id");
             var  admission_id = $("#admission_id").val();
            if (confirm('Are you sure to delete this record ?')) {
                $.ajax({
                    type: 'post',
                    url: "{{ route('pos.deactivate_record') }}",
                    data: {
                        id: id,
                        admission_id: admission_id,
                        table: "patient_service_charges",
                        _token: '{{ csrf_token() }}'

                    },
                    success: function(res) {
                        service_charges_table.ajax.reload();
                        //window.location.reload();
                    }
                })
            } else {
                alert('Why did you press cancel? You should have confirmed');
            }
        });



    </script>
@endpush
