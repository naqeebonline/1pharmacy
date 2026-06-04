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
                        <form id="add_in_patient_payment" method="post" action="{{ route('pos.save_consultant_procedure_pricing') }}">
                            @csrf
                            <div class="card-body">
                                <h5>Consultant Procedure Pricing</h5>
                                <div class="row">
                                    <div class="col-md-3">
                                        <input type="hidden" value="{{$consultant_procedure_id}}" id="consultant_procedure_id" name="consultant_procedure_id"
                                               class="form-control" placeholder="" autocomplete="off">
                                    </div>

                                </div>




                            </div>

                            <div class="card-body">
                            <div class="row">

                                <div class="col-md-6">
                                    <h6 style="font-weight: bold;">Procedure Name: {{$consultant_procedure->procedure->name ?? 0}}</h6>
                                    <h6 style="font-weight: bold;">Procedure Amount: {{$consultant_procedure->amount ?? 0}}</h6>
                                    <h6 style="font-weight: bold;">Consultant Charges: {{$consultant_procedure->consultant_charges ?? 0}}</h6>
                                    <table class="table table-bordered">

                                        @foreach($service_type as $key => $value)


                                            <tr>
                                                <td width="50%" >{{$value->name}}
                                                    @if($value->type !="Fixed")
                                                        <span style="color: red;font-size: 10px;float: right">({{$value->price}} * 1 day) = ({{$value->price * 1}})</span>
                                                    @endif
                                                </td>
                                                <td width="50%">
                                                    <input type="hidden"  id="amount" style="height: 30px !important;" name="service_charges_id[]" value="{{$value->id}}" class="form-control" placeholder="" autocomplete="off">
                                                    @if($value->type !="Fixed")
                                                        <input  type="number" required id="amount" style="height: 30px !important; width: 50% !important;" name="service_charges_amount[]" value="{{$value->patient_charges->amount ?? 0}}" class="form-control" placeholder="" autocomplete="off">
                                                    @else
                                                        <input  type="number" required id="amount" style="height: 30px !important; width: 50% !important;" name="service_charges_amount[]" value="{{$value->patient_charges->amount ?? 0}}" class="form-control" placeholder="" autocomplete="off">
                                                    @endif
                                                </td>

                                            </tr>
                                        @endforeach

                                            <tr>
                                                <td width="50%" style="border-top: 2px solid black;font-weight: bold" >Total</td>
                                                <td width="50%" style="border-top: 2px solid black; font-weight: bold">
                                                    {{ ($total_pricing) }}
                                                </td>
                                            </tr>

                                            <tr>
                                                <td width="50%" style="border-top: 2px solid black;font-weight: bold" >Balance</td>
                                                <td width="50%" style="border-top: 2px solid black; color: red;font-weight: bold">

                                                    <input type="text" disabled id="right_hand_balance" value="{{ ($consultant_procedure->amount ?? 0) - ($total_pricing) }}">
                                                </td>
                                            </tr>

                                    </table>
                                </div>

                                <div class="col-md-12 mt-2">
                                    <button type="submit" class="btn btn-primary" id="discharge_patient">Submit</button>
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
