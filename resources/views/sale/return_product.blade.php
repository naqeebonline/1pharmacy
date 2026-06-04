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
                                <label for="">Select Patient</label>
                                <select name="" id="admit_patient" class="form-select">
                                    <option value="" disabled selected>Select Patient</option>
                                    @foreach ($patients as $item)
                                        <option value="{{ $item->id }}" data-item='{{ json_encode($item) }}'
                                            data-admission={{ $item->id }} data-patient={{ $item->patient_id }}>
                                            {{ $item->patient->name . ' - ' . $item->patient->mr_no }}</option>
                                    @endforeach
                                </select>
                            </div>

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




            <div class="card">
                <div class="row">
                    <div class="col">

                        <div class="card mb-3">
                            <div class="card-header border-bottom">
                                <ul class="nav nav-tabs card-header-tabs" role="tablist">


                                    <li class="nav-item">
                                        <button class="nav-link active" data-bs-toggle="tab"
                                            data-bs-target="#patient_treatment_tab" role="tab" aria-selected="true">
                                            Treatment
                                        </button>
                                    </li>


                                </ul>
                            </div>

                            <div class="tab-content">


                                <div class="tab-pane active " id="patient_treatment_tab" role="tabpanel">
                                    <div class="table-responsive" style="min-height: 200px">
                                        <div class="btn btn-primary print_medicine_report">Print Report</div>
                                        <table id="patient_treatment_table" style="width: 100% !important;"
                                            class="table table-responsive table-striped data_mf_table table-condensed table-responsive">

                                            <thead>
                                                <tr>
                                                    <th style="width: 10%">Product Name</th>

                                                    <th style="width: 10%">Quantity</th>
                                                    <th style="width: 10%">Total Consumed</th>
                                                    <th style="width: 10%">Return Quantity</th>

                                                    <th style="width: 10%">Date</th>
                                                    <th style="width: 10%">Medicine Type</th>



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

    <div class="modal fade" id="return_product_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form class="modal-content form-submit-event" id="return_product_form">
                <input type="hidden" id="patient_inv_id" name="id" value="0">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Return Poduct</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-12 mb-3">
                            <input type="hidden" id="SDID" name="SDID" value="0">
                            <input type="hidden" id="patient_id" name="patient_id" value="0">
                            <input type="hidden" id="admission_id" name="admission_id" value="0">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Quantity<span class="asterisk">*</span></label>
                            <input type="number" min="0" readonly required id="Quantity" name="Quantity" class="form-control"
                                placeholder="" autocomplete="off">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Return Quantity<span class="asterisk">*</span></label>
                            <input type="number" min="0" required id="ReturnQuantity" name="ReturnQuantity" class="form-control"
                                placeholder="" autocomplete="off">
                        </div>
                        {{-- <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Quantity<span class="asterisk">*</span></label>
                            <input type="number" readonly required id="quantity" name="quantity" class="form-control"
                                placeholder="" autocomplete="off">
                        </div> --}}


                    </div>




                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close </button>
                    <div id="return_item_to_stock" class="btn btn-primary">Return Item</div>
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
        setTimeout(function() {

            $('#admit_patient').select2();

        }, 1000);

        $("body").on("change", "#admit_patient", function(e) {
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

            $("#patient_detail").html(html)


            patient_id = selectedOption.getAttribute("data-patient");
            admission_id = selectedOption.getAttribute("data-admission");

            $("#investigation_patient_id").val(patient_id);
            $("#investigation_admission_id").val(admission_id);


            $("#service_charges_patient_id").val(patient_id);
            $("#service_charges_admission_id").val(admission_id);



            loadTreatmentData(patient_id, admission_id);
        });

        $("body").on("click", ".print_medicine_report", function(e) {
            var url = "{{route('pos.print_admitted_patient_treatment_report')}}"+"/"+patient_id+"/"+admission_id;

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

        $("body").on("click", ".return_product", function(e) {
            let data = $(this).attr("data-details");
            let details = JSON.parse(data); 


            $("#return_product_modal").modal("show");
            $("#SDID").val(details.SDID);

            $("#patient_id").val(details.patient_id);
            $("#admission_id").val(details.admission_id);
            $("#Quantity").val(details.Quantity - details.ReturnQuantity);
            $("#ReturnQuantity").val('');


        });

        $("body").on("input", "#ReturnQuantity", function(e) {
            "use strict";
            let returnQuantity = parseInt($(this).val()) || 0;
            let Quantity = parseInt($("#Quantity").val()) || 0;
            if (returnQuantity > Quantity) {
                alert("Return Quantity cannot be greater than Quantity");
                $(this).val(Quantity);
            }
        });

        $("body").on("click", "#return_item_to_stock", function(e) {
           var SDID= $("#SDID").val();
            var ReturnQuantity = $("#ReturnQuantity").val();
            if(ReturnQuantity == '' || ReturnQuantity == 0){
                alert("You can't make return with emptry or zero value");
                return false;
            }
            $.ajax({
                type: 'post',
                url: "{{ route('pos.return_item') }}",
                data: {
                    SDID: SDID,
                    ReturnQuantity: ReturnQuantity,
                    _token: '{{ csrf_token() }}'

                },
                success: function(res) {
                    $("#return_product_modal").modal('hide');
                    treatment_data_table.ajax.reload();
                    //window.location.reload();
                }
            })
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
