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


            <!-- Traffic sources -->
            <div class="row ">

                <!-- Right Block: Form Inputs -->
                <div class="col-md-12 mb-4">
                    <div class="card">
                        <div class="card-body">



                            <div class="col-md-4 mb-3">
                                <label for="">-</label>
                                <div class="btn btn-primary print_summary">Print Summary</div>
                            </div>

                            <div class="" id="patient_detail">
                                <table class="table table-responsive table-bordered">
                                    <tr>
                                        <td style="font-weight: bold">MRNO</td>
                                        <td>{{ ($patient->patient->mr_no) ?? '' }}</td>
                                        <td style="font-weight: bold">Name</td>
                                        <td>{{ $patient->patient->name ?? '' }}</td>
                                        <td style="font-weight: bold">Father Name</td>
                                        <td>{{ $patient->patient->father_husband_name ?? '' }}</td>
                                        <td style="font-weight: bold">Contact Number</td>
                                        <td>{{ $patient->patient->contact_no ?? '' }}</td>
                                    </tr>

                                    <tr>
                                        <td style="font-weight: bold">Ward No</td>
                                        <td>{{ $patient->ward->name ?? '' }}</td>
                                        <td style="font-weight: bold">Bed No</td>
                                        <td>{{ $patient->bed->name ?? '' }}</td>
                                        <td style="font-weight: bold">Admission Date</td>
                                        <td>{{ $patient->admission_date ?? '' }}</td>
                                        <td style="font-weight: bold">Discharge On</td>
                                        <td>{{ $patient->discharge_date ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Consultant Name</td>
                                        <td style="font-weight: bold; ">{{ $patient->consultant->name ?? '' }}</td>
                                        <td>Procedure</td>
                                        <td style="font-weight: bold; ">{{ $patient->procedure_type->name ?? '' }}</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>

                                    <tr style="background-color: lightgrey; font-weight: bold; color:green">
                                        <td>Procedure Amount</td>
                                        <td >{{ $summary['procedure_amount'] }}</td>
                                        <td>Investigation</td>
                                        <td style="font-weight: bold; ">{{ $summary['investigation_amount'] }}</td>
                                        <td>Medicine</td>
                                        <td>{{ $summary['medicine_amount'] }}</td>
                                        <td>Balance</td>
                                        <td>{{  $summary['balance'] }}</td>
                                    </tr>

                                </table>

                            </div>
                        </div>
                    </div>




                </div>
            </div>




            <div class="card">
                <div class="row">
                    <div class="col">

                        <div class="card mb-3">
                            <div class="card-header border-bottom">
                                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                                    <li class="nav-item">
                                        <button class="nav-link active" data-bs-toggle="tab"
                                            data-bs-target="#investigation_tab" role="tab" aria-selected="false">
                                            Investigations
                                        </button>
                                    </li>

                                    <li class="nav-item">
                                        <button class="nav-link " data-bs-toggle="tab" data-bs-target="#service_charges_tab"
                                            role="tab" aria-selected="true">
                                            Service Charges
                                        </button>
                                    </li>

                                    <li class="nav-item">
                                        <button class="nav-link " data-bs-toggle="tab"
                                            data-bs-target="#patient_treatment_tab" role="tab" aria-selected="true">
                                            Treatment
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link " data-bs-toggle="tab" data-bs-target="#patient_ot_notes"
                                            role="tab" aria-selected="true">
                                            OT Notes
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link " data-bs-toggle="tab"
                                            data-bs-target="#patient_nursing_notes" role="tab" aria-selected="true">
                                            Nusing Notes
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link " data-bs-toggle="tab"
                                            data-bs-target="#patient_vital_notes" role="tab" aria-selected="true">
                                            Patient Vitals
                                        </button>
                                    </li>


                                </ul>
                            </div>

                            <div class="tab-content">

                                <div class="tab-pane fade active show" id="investigation_tab" role="tabpanel">
                                    <div class="table-responsive" style="min-height: 200px">
                                        <h4 style="color:green;">Total: {{$summary['investigation_amount']}}</h4>

                                        <table id="investigation_table" style="width: 100% !important"
                                            class="table table-responsive table-striped data_mf_table ">

                                            <thead>
                                                <tr>
                                                    <th>Invoice#</th>
                                                    <th>Amount</th>
                                                    <th>Date</th>



                                                </tr>
                                            </thead>

                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>


                                <div class="tab-pane fade " id="service_charges_tab" role="tabpanel">
                                    <div class="table-responsive" style="min-height: 200px">
                                        
                                        <table id="service_charges_table" style="width: 100% !important;"
                                            class="table table-responsive table-striped data_mf_table table-condensed table-responsive">

                                            <thead>
                                                <tr>
                                                    <th style="width: 10%">Invoice</th>

                                                    <th style="width: 10%">Amount</th>
                                                    <th style="width: 10%">Dated</th>


                                                </tr>
                                            </thead>

                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>


                                <div class="tab-pane fade " id="patient_treatment_tab" role="tabpanel">
                                    <div class="table-responsive" style="min-height: 200px">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <h4 style="color:green;">Total: {{$summary['medicine_amount']}}</h4>
                                            </div>
                                            <div class="col-md-3">
                                                <select class="form-select" id="medicine_type_id">
                                                    <option value="">Select Medicine Type</option>
                                                    <option value="Ward">Ward</option>
                                                    <option value="Home">Home</option>
                                                    <option value="OT">OT</option>
                                                </select>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="btn btn-primary print_medicine_report">Print</div>
                                            </div>
                                        </div>

                                        <table id="patient_treatment_table" style="width: 100% !important;"
                                            class="table table-responsive table-striped data_mf_table table-condensed table-responsive">

                                            <thead>
                                            <tr>
                                                <th style="width: 10%">Product Name</th>
                                                <th style="width: 10%">Dose Type</th>

                                                <th style="width: 10%">Quantity</th>
                                                <th style="width: 10%">Total Consumed</th>
                                                <th style="width: 10%">Return Quantity</th>

                                                <th style="width: 10%">Date</th>
                                                <th style="width: 10%">Medicine Type</th>




                                            </tr>
                                            </thead>

                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="patient_ot_notes" role="tabpanel">
                                    <div class="table-responsive" style="min-height: 200px">
                                        <table id="patient_ot_notes_table" style="width: 100% !important;"
                                            class="table table-responsive table-striped data_mf_table table-condensed table-responsive">

                                            <thead>
                                                <tr>
                                                    <th style="width: 10%">OT Notes</th>

                                                    <th style="width: 10%">Dated</th>


                                                </tr>
                                            </thead>

                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane" id="patient_nursing_notes" role="tabpanel">
                                    <div class="table-responsive" style="min-height: 200px">
                                        <table id="patient_nusing_notes_table" style="width: 100% !important;"
                                            class="table table-responsive table-striped data_mf_table table-condensed table-responsive">

                                            <thead>
                                                <tr>
                                                    <th style="width: 10%">OT Notes</th>

                                                    <th style="width: 10%">Dated</th>


                                                </tr>
                                            </thead>

                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                 <div class="tab-pane fade" id="patient_vital_notes" role="tabpanel">
                                    <div class="table-responsive" style="min-height: 200px">
                                        <table id="patient_vital_notes_table" style="width: 100% !important;"
                                            class="table table-responsive table-striped data_mf_table table-condensed table-responsive">

                                            <thead>
                                                <tr>
                                                    <th>RBS</th>
                                                    <th>RR</th>
                                                    <th>HR</th>
                                                    <th>BP</th>
                                                    <th>Temp</th>
                                                    <th>SPO2</th>
                                                    <th>Remarks</th>

                                                </tr>
                                            </thead>

                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>




                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /traffic sources -->
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
        patient_id = "{{$patient_id}}";
        admission_id = "{{$admission_id}}";
        setTimeout(function() {

            $('#admit_patient').select2();
            $("#investigation_patient_id").val(patient_id);
            $("#investigation_admission_id").val(admission_id);


            $("#service_charges_patient_id").val(patient_id);
            $("#service_charges_admission_id").val(admission_id);



            loadInvestigationData(patient_id, admission_id);
            loadServiceChargesData(patient_id, admission_id);
            loadTreatmentData(patient_id, admission_id);
            loadOtNotes(patient_id, admission_id);
            loadNurseNotes(patient_id, admission_id);
            loadVitalNotes(patient_id, admission_id);

        }, 1000);

        function resetForm() {
            //$("#SID").val('').trigger("change");
            $("#Amount").val('');
            $("#discount").val(0);
            $("#Date").val('');
            $("#Description").val('');
            $("#InvoiceNo").val('');
            $("#payment_type_id").val('').trigger("change");

        }

        $("body").on("click", ".print_summary", function(e) {
            var url = "{{route('pos.calculatePatientMedecineAmount')}}/"+admission_id;
            var newWindow = window.open(url, '_blank', 'width=1200,height=800');
            newWindow.focus();

        });




        function loadInvestigationData(patient_id, admission_id = '') {
            investigation_table = $('#investigation_table').DataTable({
                processing: true,
                serverSide: true,

                lengthMenu: [
                    [100, 250, 500, 1000],
                    ['100', '250', '500', '1000']
                ],
                pageLength: 50,
                ajax: {
                    url: `{{ route('pos.get_admission_investigations') }}/${patient_id}/${admission_id}`,
                    data: function(d) {
                        d.user_id = $('#attendance_user_filter').val();
                        d.attendance_date_from = $('#attendance_date_from').val();
                        d.attendance_date_to = $('#attendance_date_to').val();


                    }

                },

                columns: [

                    {
                        data: 'investigation.name',
                        name: 'investigation.name',
                        searchable: true
                    },
                    {
                        data: 'inv_amount',
                        name: 'inv_amount',
                        searchable: true
                    },
                    {
                        data: 'inv_date',
                        name: 'inv_date',
                        searchable: true
                    },

                ],


                responsive: true,
                processing: true,
                serverSide: true,
                searching: true,
                sorting: true,
                paging: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
        }

        function loadServiceChargesData(patient_id = '', admission_id = '') {
            service_charges_table = $('#service_charges_table').DataTable({
                processing: true,
                serverSide: true,

                lengthMenu: [
                    [100, 250, 500, 1000],
                    ['100', '250', '500', '1000']
                ],
                pageLength: 50,
                ajax: {
                    url: `{{ route('pos.get_admission_service_charges') }}/${patient_id}/${admission_id}`,

                    data: function(d) {
                        d.user_id = $('#attendance_user_filter').val();
                        d.attendance_date_from = $('#attendance_date_from').val();
                        d.attendance_date_to = $('#attendance_date_to').val();


                    }

                },

                columns: [

                    {
                        data: 'service_type.name',
                        name: 'service_type.name',
                        searchable: true
                    },
                    {
                        data: 'service_rate',
                        name: 'service_rate',
                        searchable: true
                    },
                    {
                        data: 'service_date',
                        name: 'service_date',
                        searchable: true
                    },
                   
                ],

                responsive: true,
                processing: true,
                serverSide: true,
                searching: true,
                sorting: true,
                paging: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
        }

        $("body").on("change","#medicine_type_id",function () {
            treatment_data_table.ajax.reload();
        });

        $("body").on("click", ".print_medicine_report", function(e) {
            var medicine_type_id = $("#medicine_type_id").val();

            var url = "{{route('pos.patient_treatment_chart_report')}}"+"/"+patient_id+"/"+admission_id+"/"+medicine_type_id;

            var reportUrl = url; // Replace with your report URL
            var windowFeatures = "width=1000,height=600,scrollbars=yes,resizable=yes";

            // Open the report in a new popup window
            window.open(reportUrl, "_blank", windowFeatures);

        });
        function loadTreatmentData(patient_id, admission_id = '') {
            treatment_data_table = $('#patient_treatment_table').DataTable({
                processing: true,
                serverSide: true,

                lengthMenu: [
                    [100, 250, 500, 1000],
                    ['100', '250', '500', '1000']
                ],
                pageLength: 50,
                ajax: {
                    url: `{{ route('pos.get_admitted_patient_treatments') }}/${patient_id}/${admission_id}`,
                    data: function(d) {
                        d.medicine_type = $('#medicine_type_id').val();


                    }

                },

                columns: [

                    {
                        data: 'product.ProductName',
                        name: 'product.ProductName',
                        searchable: true
                    },
                    {
                        data: 'dose_type',
                        name: 'dose_type',
                        searchable: true
                    },
                    {
                        data: 'Quantity',
                        name: 'Quantity',
                        searchable: true
                    },
                    {
                        data: 'total_consumed',
                        name: 'total_consumed',
                        searchable: true
                    },
                    {
                        data: 'ReturnQuantity',
                        name: 'ReturnQuantity',
                        searchable: true
                    },

                    {
                        data: 'sale.Date',
                        name: 'sale.Date',
                        searchable: true
                    },

                    {
                        data: 'sale.medicine_type',
                        name: 'sale.medicine_type',
                        searchable: true
                    },

                ],


                responsive: true,
                processing: true,
                serverSide: true,
                searching: true,
                sorting: true,
                paging: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
        }

        function loadOtNotes(patient_id = '', admission_id = '') {
            patient_ot_notes_table = $('#patient_ot_notes_table').DataTable({
                processing: true,
                serverSide: true,

                lengthMenu: [
                    [100, 250, 500, 1000],
                    ['100', '250', '500', '1000']
                ],
                pageLength: 50,
                ajax: {
                    url: `{{ route('pos.get_patient_ot_notes') }}/${patient_id}/${admission_id}`,

                    data: function(d) {
                        d.user_id = $('#attendance_user_filter').val();
                        d.attendance_date_from = $('#attendance_date_from').val();
                        d.attendance_date_to = $('#attendance_date_to').val();


                    }

                },

                columns: [

                    {
                        data: 'ot_notes',
                        name: 'ot_notes',
                        searchable: true
                    },

                    {
                        data: 'created_at',
                        name: 'created_at',
                        searchable: true
                    },
                   
                ],

                responsive: true,
                processing: true,
                serverSide: true,
                searching: true,
                sorting: true,
                paging: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
        }


        function loadNurseNotes(patient_id = '', admission_id = '') {
            patient_nurse_notes_table = $('#patient_nusing_notes_table').DataTable({
                processing: true,
                serverSide: true,

                lengthMenu: [
                    [100, 250, 500, 1000],
                    ['100', '250', '500', '1000']
                ],
                pageLength: 50,
                ajax: {
                    url: `{{ route('pos.get_patient_nurse_notes') }}/${patient_id}/${admission_id}`,

                    data: function(d) {
                        d.user_id = $('#attendance_user_filter').val();
                        d.attendance_date_from = $('#attendance_date_from').val();
                        d.attendance_date_to = $('#attendance_date_to').val();


                    }

                },

                columns: [

                    {
                        data: 'nurse_note',
                        name: 'nurse_note',
                        searchable: true
                    },

                    {
                        data: 'created_at',
                        name: 'created_at',
                        searchable: true
                    },
                    
                ],

                responsive: true,
                processing: true,
                serverSide: true,
                searching: true,
                sorting: true,
                paging: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
        }

        function loadVitalNotes(patient_id = '', admission_id = '') {

            patient_vital_notes_table = $('#patient_vital_notes_table').DataTable({
                processing: true,
                serverSide: true,

                lengthMenu: [
                    [100, 250, 500, 1000],
                    ['100', '250', '500', '1000']
                ],
                pageLength: 50,
                ajax: {
                    url: `{{ route('pos.patient_vitals_list') }}/${patient_id}/${admission_id}`,

                },

                columns: [

                    {
                        data: 'rbs',
                        name: 'rbs',
                        searchable: true
                    },

                    {
                        data: 'r_r',
                        name: 'r_r',
                        searchable: true
                    },
                    {
                        data: 'hr',
                        name: 'hr',
                        searchable: true
                    },
                    {
                        data: 'bp',
                        name: 'bp',
                        searchable: true
                    },
                    {
                        data: 'temp',
                        name: 'temp',
                        searchable: true
                    },
                    {
                        data: 'spo2',
                        name: 'spo2',
                        searchable: true
                    },
                    {
                        data: 'remarks',
                        name: 'remarks',
                        searchable: true
                    },
                   
                ],

                responsive: true,
                processing: true,
                serverSide: true,
                searching: true,
                sorting: true,
                paging: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
        }


        $("body").on("click", "#add_new_investigation", function(e) {
            $("#patient_inv_id").val(0);
            $("#investigation_sub_category_id").val('');
            $("#inv_amount").val('');
            $("#add_investigation_model").modal("show");

        });

        $("body").on("click", "#add_new_service_charges", function(e) {
            $("#patient_service_charges_id").val(0);
            $("#service_type_id").val('');
            $("#service_rate").val('');
            $("#add_service_charges_model").modal("show");

        });



        $("#investigation_form").on("submit", function(e) {
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

                $("#investigation_form").ajaxSubmit({
                    url: '{{ route('pos.save_patient_investigation') }}',
                    type: 'post',
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        investigation_table.destroy();
                        let patient_id = $("#investigation_patient_id").val();
                        let admission_id = $("#investigation_admission_id").val();
                        loadInvestigationData(patient_id, admission_id);
                        $("#add_investigation_model").modal("hide");
                        investigation_table.ajax.reload();
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        alert(JSON.parse(XMLHttpRequest.responseText).message);
                    }
                });
            }
        });


        // INVESTIGATION EDIT
        $("body").on("click", ".edit_inv_record", function(e) {
            $("#add_investigation_model").modal("show");

            record_id = $(this).attr("data-id");
            var details = JSON.parse($(this).attr("data-details"));
            console.log(details);

            $("#patient_inv_id").val(details.id);


            $("#inv_amount").val(details.inv_amount);
            $("#investigation_sub_category_id").val(details.investigation_sub_category_id).trigger('change');
            $("#admission_type_id").val(details.admission_type_id);

            var fullDateTime = details.inv_date;
            let dateOnly = fullDateTime.split(' ')[0];
            $("#inv_date").val(dateOnly);
            // getEditBeds(details.ward_id,details.bed_id);

        });
        // SERVICES CHARGE EDIT
        $("body").on("click", ".edit_service_record", function(e) {
            $("#add_service_charges_model").modal("show");

            record_id = $(this).attr("data-id");
            var details = JSON.parse($(this).attr("data-details"));
            console.log(details);
            $("#service_date").val('2024-01-02');
            var fullDateTime = details.service_date;
            let dateOnly = fullDateTime.split(' ')[0];


            $("#patient_service_charges_id").val(details.id);


            $("#service_rate").val(details.service_rate);
            $("#service_type_id").val(details.service_type_id).trigger('change');
            $("#service_date").val(dateOnly);


        });

        $("#service_charges_form").on("submit", function(e) {
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

                $("#service_charges_form").ajaxSubmit({
                    url: '{{ route('pos.save_patient_service_charges') }}',
                    type: 'post',
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        $('#service_charges_table').DataTable().destroy();

                        let patient_id = $("#service_charges_patient_id").val();
                        let admission_id = $("#service_charges_admission_id").val();
                        loadServiceChargesData(patient_id, admission_id);
                        $("#add_service_charges_model").modal("hide");
                        service_charges_table.ajax.reload();
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        alert(JSON.parse(XMLHttpRequest.responseText).message);
                    }
                });
            }
        });


        $("body").on("click", ".delete_record", function(e) {
            var id = $(this).attr("data-id");
            if (confirm('Are you sure to delete this record ?')) {
                $.ajax({
                    type: 'post',
                    url: "{{ route('pos.deactivate_record') }}",
                    data: {
                        id: id,
                        table: "patient_investigations",
                        _token: '{{ csrf_token() }}'

                    },
                    success: function(res) {
                        investigation_table.ajax.reload();
                        //window.location.reload();
                    }
                })
            } else {
                alert('Why did you press cancel? You should have confirmed');
            }
        });


        $("body").on("click", ".delete_service_record", function(e) {
            var id = $(this).attr("data-id");
            if (confirm('Are you sure to delete this record ?')) {
                $.ajax({
                    type: 'post',
                    url: "{{ route('pos.deactivate_record') }}",
                    data: {
                        id: id,
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
