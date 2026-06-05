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
            <form method="post" id="from_submit">
                <div class="row ">

                    <!-- Right Block: Form Inputs -->
                    <div class="col-md-12 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <table class="table table-responsive table-bordered">
                                        <tr>
                                            <td style="font-weight: bold">MRNO</td>
                                            <td>{{ $patient->mr_no ?? '' }}</td>
                                            <td style="font-weight: bold">Name</td>
                                            <td>{{ $patient->name ?? '' }}</td>
                                            <td style="font-weight: bold">Father Name</td>
                                            <td>{{ $patient->father_husband_name ?? '' }}</td>
                                            <td style="font-weight: bold">Contact Number</td>
                                            <td>{{ $patient->contact_no ?? "" }}</td>
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
            </form>




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
                                        <button class="nav-link " data-bs-toggle="tab" data-bs-target="#patient_treatment_tab"
                                                role="tab" aria-selected="true">
                                            Treatment
                                        </button>
                                    </li>


                                </ul>
                            </div>

                            <div class="tab-content">

                                <div class="tab-pane fade active show" id="investigation_tab" role="tabpanel">
                                    <div class="table-responsive" style="min-height: 200px">
                                        <button class="btn btn-primary" id="add_new_investigation">Add New
                                            Investigation</button>

                                        <table id="investigation_table" style="width: 100% !important"
                                            class="table table-responsive table-striped data_mf_table ">

                                            <thead>
                                                <tr>
                                                    <th>Invoice#</th>
                                                    <th>Amount</th>
                                                    <th>Date</th>



                                                    <th style="width: 10%">Action</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>


                                <div class="tab-pane fade " id="service_charges_tab" role="tabpanel">
                                    <div class="table-responsive" style="min-height: 200px">
                                        <button class="btn btn-primary" id="add_new_service_charges">Add Service
                                            Charges</button>
                                        <table id="service_charges_table" style="width: 100% !important;"
                                            class="table table-responsive table-striped data_mf_table table-condensed table-responsive">

                                            <thead>
                                                <tr>
                                                    <th style="width: 10%">Invoice</th>

                                                    <th style="width: 10%">Dated</th>
                                                    <th style="width: 10%">Amount</th>


                                                    <th style="width: 10%">Action</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>


                                <div class="tab-pane fade " id="patient_treatment_tab" role="tabpanel">
                                    <div class="table-responsive" style="min-height: 200px">

                                        <table id="patient_treatment_table" style="width: 100% !important;"
                                               class="table table-responsive table-striped data_mf_table table-condensed table-responsive">

                                            <thead>
                                            <tr>
                                                <th style="width: 10%">Product Name</th>

                                                <th style="width: 10%">Quantity</th>
                                                {{--<th style="width: 10%">Unit Price</th>
                                                <th style="width: 10%">Total</th>--}}
                                                <th style="width: 10%">Date</th>


                                                <th style="width: 10%">Action</th>
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

    <div class="modal fade" id="add_investigation_model" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form class="modal-content form-submit-event" id="investigation_form">
                <input type="hidden" id="patient_inv_id" name="id" value="0">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Add New Investigation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Date<span class="asterisk">*</span></label>
                            <input type="hidden" value="{{ $admission->patient_id }}" name="patient_id"
                                class="form-control" placeholder="" autocomplete="off">
                            <input type="hidden" value="{{ $admission->id }}" name="admission_id" class="form-control"
                                placeholder="" autocomplete="off">
                            <input type="date" required id="inv_date" value="{{ date('Y-m-d') }}" name="inv_date"
                                class="form-control" placeholder="" autocomplete="off">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Select Investigation<span
                                    class="asterisk">*</span></label>
                            <select name="investigation_sub_category_id" required id="investigation_sub_category_id" class="form-select">
                                <option value="">Select Investigation...</option>
                                @forelse ($investigation as $item)
                                    <option data-rate="{{ $item->price }}" value="{{ $item->id }}">
                                        {{ $item->name }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Amount<span class="asterisk">*</span></label>
                            <input type="number" required id="inv_amount" name="inv_amount" class="form-control"
                                placeholder="" autocomplete="off">
                        </div>


                    </div>




                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close </button>
                    <button type="submit" id="submit_inv_btn" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>


    <div class="modal fade" id="add_service_charges_model" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form class="modal-content form-submit-event" id="service_charges_form">
                <input type="hidden" id="patient_service_charges_id" name="id" value="0">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Add New Service Charges</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Date<span class="asterisk">*</span></label>
                            <input type="hidden" value="{{ $admission->patient_id }}" name="patient_id"
                                class="form-control" placeholder="" autocomplete="off">
                            <input type="hidden" value="{{ $admission->id }}" name="admission_id" class="form-control"
                                placeholder="" autocomplete="off">
                            <input type="date" required id="service_date" value="{{ date('Y-m-d') }}"
                                name="service_date" class="form-control" placeholder="" autocomplete="off">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Select Service Type<span
                                    class="asterisk">*</span></label>
                            <select name="service_type_id" required id="service_type_id" class="form-select">
                                <option value="">Select Service Type...</option>
                                @forelse ($service_type as $item)
                                    <option data-rate="{{ $item->price }}" value="{{ $item->id }}">
                                        {{ $item->name }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Amount<span class="asterisk">*</span></label>
                            <input type="number" required id="service_rate" name="service_rate" class="form-control"
                                placeholder="" autocomplete="off">
                        </div>


                    </div>




                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close </button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
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
        $(document).ready(function() {
            patient_id = "{{ $admission->patient_id }}";
            admission_id = "{{ $admission->id }}";
            setTimeout(function() {

                $('#SID').select2();
                $('#investigation_sub_category_id').select2();

            }, 1000);

            $("body").on("click", "#add_new_investigation", function(e) {
                $("#patient_inv_id").val(0);
                $("#investigation_sub_category_id").val('');
                $("#inv_amount").val('');
                $("#add_investigation_model").modal("show");

            });

            $("body").on("change", "#investigation_sub_category_id", function(e) {
                const dataRate = $('#investigation_sub_category_id option:selected').attr('data-rate');
                $("#inv_amount").val(dataRate);
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

            //---------- service charges related script goes here------//
            $("body").on("click", "#add_new_service_charges", function(e) {
                $("#patient_service_charges_id").val(0);
                $("#service_type_id").val('');
                $("#service_rate").val('');
                $("#add_service_charges_model").modal("show");

            });

            $("body").on("change", "#service_type_id", function(e) {
                const dataRate = $('#service_type_id option:selected').attr('data-rate');
                $("#service_rate").val(dataRate);
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
                            service_charges_table.destroy();
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


            //-------------------------------------------------------//




            loadInvestigationData(patient_id, admission_id);
            loadServiceChargesData(patient_id, admission_id);
            loadTreatmentData(patient_id, admission_id);
        });

        function resetForm() {
            //$("#SID").val('').trigger("change");
            $("#Amount").val('');
            $("#discount").val(0);
            $("#Date").val('');
            $("#Description").val('');
            $("#InvoiceNo").val('');
            $("#payment_type_id").val('').trigger("change");

        }

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





                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
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
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
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



                    }

                },

                columns: [

                    {
                        data: 'product.ProductName',
                        name: 'product.ProductName',
                        searchable: true
                    },
                    {
                        data: 'Quantity',
                        name: 'Quantity',
                        searchable: true
                    },
                    /*{
                        data: 'UnitePrice',
                        name: 'UnitePrice',
                        searchable: true
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount',
                        searchable: true,
                    },*/
                    {
                        data: 'sale.Date',
                        name: 'sale.Date',
                        searchable: true
                    },





                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
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


            $("#inv_amount").val(details.inv_amount);
            $("#service_type_id").val(details.service_type_id).trigger('change');
            $("#service_date").val(dateOnly);
           

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
