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
                        <div class="card-body">
                            <h4>Patient Refunds</h4>
                            <form action="" id="add_patient_refund">

                                <input type="hidden" value="0" id="id" name="id">
                                <input type="hidden" value="0" id="patient_id" name="patient_id">
                                <input type="hidden" value="0" id="admission_id" name="admission_id">

                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="" class="form-label">Select Patient</label>
                                        <select name="" id="admit_patient" class="form-select">
                                            <option value="" disabled selected>Select Patient</option>
                                            @foreach ($patients as $item)
                                                <option value="{{ $item->id }}" data-item='{{ json_encode($item) }}'
                                                    data-admission={{ $item->id }} data-patient={{ $item->patient_id }}>
                                                    {{ $item->patient->name . ' - ' . $item->patient->mr_no }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="" class="form-label">Consultant</label>
                                        <select class="form-select" name="consultant_id" id="consultant_id">
                                            <option> Select Consultant</option>
                                            @foreach ($consultants as $consultant)
                                                <option value="{{ $consultant->id }}"> {{ $consultant->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="" class="form-label">Consultant Fee amount</label>
                                        <input required type="text" class="form-control" id="consultant_fee_amount"
                                            name="consultant_fee_amount" value="">
                                    </div>

                                    <div class="col-md-2 mb-3">
                                        <label for="" class="form-label">Investigation Amount</label>
                                        <input required type="text" class="form-control" id="investigation_amount"
                                            name="investigation_amount" value="">
                                    </div>

                                    <div class="col-md-2 mb-3">
                                        <label for="" class="form-label">Investigation Invoice No.</label>
                                        <input required type="text" class="form-control" id="inv_invoice_no"
                                            name="inv_invoice_no" value="">
                                    </div>


                                    <div class="col-12 mb-3">
                                        <div class="row">
                                            @foreach ($investigation as $item)
                                                <div class="col-md-3">
                                                    <div class="form-check form-check-inline">
                                                        <input value="{{ $item->id }}" type="checkbox"
                                                            class="form-check-input" id="inv-{{ $item->id }}"
                                                            name="investigation_ids[]">
                                                        <label for="inv-{{ $item->id }}"
                                                            class="form-check-label">{{ $item->name }}</label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>


                                </div>

                                <div class="mb-3">
                                    <button class="btn btn-primary" id="save_inv_btn" type="submit">Save
                                        Investigation</button>
                                </div>

                            </form>

                            <div class="" id="patient_detail">
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
            $('#investigation_sub_category_id').select2();

        }, 1000);

        $("body").on("change", "#admit_patient", function(e) {
            $(".form-check-input").prop("checked", false);

            $("#id").val(0);
            
            $("#consultant_id").val("").trigger("change");
            $("#consultant_fee_amount").val("");
            $("#investigation_amount").val("");
            $("#inv_invoice_no").val("");
            
            let selectedOption = e.target.options[e.target.selectedIndex];

            let data = JSON.parse(selectedOption.getAttribute("data-item"));


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


            let time = Date.now();
            $("#invoice_no").val(time);

            let patient_id = selectedOption.getAttribute("data-patient");
            let admission_id = selectedOption.getAttribute("data-admission");



            $("#patient_id").val(patient_id);
            $("#admission_id").val(admission_id);

            getPatientRefunds(patient_id, admission_id);


        });


        function getPatientRefunds(patient_id, admission_id) {
            $.ajax({
                type: "GET",
                url: `{{ route('pos.get_patient_refunds') }}/${patient_id}/${admission_id}`,
                success: function(response) {
                    const data = response.data;
                    if (data) {

                        $("#id").val(data.id);
                        $("#consultant_fee_amount").val(data.consultant_fee_amount);
                        $("#investigation_amount").val(data.investigation_amount);
                        $("#inv_invoice_no").val(data.inv_invoice_no);
                        $("#consultant_id").val(data.consultant_id).trigger("change");
                        
                        const ids = JSON.parse(data.investigation_ids);
                        if (ids.length > 0) {
                            ids.forEach(id => {
                                $(`#inv-${id}`).prop("checked", "true")
                            });
                        }
                    }

                }
            });
        }


        $("#investigation_sub_category_id").on("change", function(e) {
            let rate = $(this).find("option:selected").attr("data-rate");
            $("#inv_amount").val(rate);
        });


        $("#add_patient_refund").on("submit", function(e) {
            e.preventDefault();
            let isValid = true;

            // Clear previous error messages
            $(".error-message").remove();
            $(".is-invalid").removeClass("is-invalid");

            // Validate required fields
            $(this).find("[required]").each(function() {
                if (!$(this).val().trim()) {
                    isValid = false;
                    $(this).addClass("is-invalid"); // Highlight invalid field
                    $(this).after(
                        `<span class="error-message text-danger">This field is required.</span>`
                    ); // Show error message
                }
            });

            if (isValid) {
                $("#save_inv_btn").prop("disabled", true).text("Saving...");
                $("#add_patient_refund").ajaxSubmit({
                    url: '{{ route('pos.save_patient_refunds') }}',
                    type: 'post',
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        window.location.reload();

                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        alert(JSON.parse(XMLHttpRequest.responseText).message);
                    }
                });
            }
        });




    </script>
@endpush
