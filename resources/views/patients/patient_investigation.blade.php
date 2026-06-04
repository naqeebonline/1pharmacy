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

                        <form action="" id="add_patient_investgiation">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="" class="form-label">Select Patient</label>
                                    <select name="" id="admit_patient" class="form-select">
                                        <option value="" disabled selected>Select Patient</option>
                                        @foreach ($patients as $item)
                                        <option value="{{ $item->id }}" data-item='{{ json_encode($item) }}'
                                            data-admission={{ $item->id }} data-patient={{ $item->patient_id }}>
                                            {{ $item->patient->name . ' - ' . $item->patient->mr_no }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <input type="hidden" value="0" id="invoice_no" name="invoice_no"
                                        class="form-control" placeholder="" autocomplete="off">

                                    <input type="hidden" value="0" id="patient_id" name="patient_id"
                                        class="form-control" placeholder="" autocomplete="off">

                                    <input type="hidden" value="0" id="admission_id" name="admission_id"
                                        class="form-control" placeholder="" autocomplete="off">

                                    <label for="nameBasic" class="form-label">Date<span
                                            class="asterisk">*</span></label>
                                    <input type="date" required id="inv_date" value="{{ date('Y-m-d') }}"
                                        name="inv_date" class="form-control" placeholder="" autocomplete="off">
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="nameBasic" class="form-label">Select Investigation<span
                                            class="asterisk">*</span></label>
                                    <select name="investigation_sub_category_id" required
                                        id="investigation_sub_category_id" class="form-select">
                                        <option value="">Select Investigation...</option>
                                        @forelse ($investigation as $item)
                                        <option data-rate="{{ $item->price }}" value="{{ $item->id }}">
                                            {{ $item->name }}
                                        </option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="nameBasic" class="form-label">Amount<span
                                            class="asterisk">*</span></label>
                                    <input type="number" required id="inv_amount" name="inv_amount"
                                        class="form-control" placeholder="" autocomplete="off">
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
                            </ul>
                        </div>

                        <div class="tab-content">

                            <div class="tab-pane fade active show" id="investigation_tab" role="tabpanel">
                                <div class="table-responsive" style="min-height: 200px">
                                    <table id="investigation_table" style="width: 100% !important"
                                        class="table table-responsive table-striped data_mf_table ">

                                        <thead>
                                            <tr>
                                                <th>Invoice#</th>
                                                <th>Name</th>
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
    setTimeout(function() {

        $('#admit_patient').select2();
        $('#investigation_sub_category_id').select2();

    }, 1000);

    function getInvestigations(admission_id) {
        $.ajax({
            type: 'post',
            url: "{{ route('pos.get_ward_investigations') }}/" + admission_id,
            data: {
                admission_id: admission_id,
                _token: '{{ csrf_token() }}'

            },
            success: function(res) {
                $("#investigation_sub_category_id").html("");
                $("#investigation_sub_category_id").append(
                    `<option value="">Select Investigation</option>`
                )
                if (res.data.length > 0) {

                    $("#investigation_sub_category_id").html("");
                    $("#investigation_sub_category_id").append(
                        `<option value="" data-rate="">Please Select Investigation...</option>`
                    );
                    $.each(res.data, function(key, bed) {
                        $("#investigation_sub_category_id").append(
                            `<option value="${bed.id}" data-rate="${bed.price}">${bed.name}</option>`
                        )
                    });
                }

            }
        })
    }

    $("body").on("change", "#admit_patient", function(e) {

        $('#investigation_table').DataTable().destroy();
        $('#service_charges_table').DataTable().destroy();
        $('#patient_treatment_table').DataTable().destroy();
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

        loadInvestigationData(patient_id, admission_id);
        getInvestigations(admission_id);
    });


    $("#investigation_sub_category_id").on("change", function(e) {
        let rate = $(this).find("option:selected").attr("data-rate");

        if (rate == '') {
            rate = 0;
            $("#inv_amount").val(rate);
        } else {
            $("#inv_amount").val(rate);
        }


    });


    $("#add_patient_investgiation").on("submit", function(e) {
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
            $("#add_patient_investgiation").ajaxSubmit({
                url: "{{ route('pos.save_patient_investigation') }}",
                type: 'post',
                data: {
                    _token: '{{ csrf_token() }}',
                },
                success: function(response) {
                    investigation_table.destroy();
                    let patient_id = $("#patient_id").val();
                    let admission_id = $("#admission_id").val();
                    loadInvestigationData(patient_id, admission_id);
                    investigation_table.ajax.reload();

                    $("#inv_amount").val("");
                    $("#investigation_sub_category_id").val("").trigger("change");
                    alert("Investigation Added");
                    $("#save_inv_btn").prop("disabled", false).text("Save Investigation");

                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert(JSON.parse(XMLHttpRequest.responseText).message);
                }
            });
        }
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
                    data: 'print_invoice_number',
                    name: 'print_invoice_number',
                    searchable: true
                },
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


    $("body").on("click", ".delete_record", function(e) {
        var id = $(this).attr("data-id");
        var admission_id = $("#admission_id").val();
        if (confirm('Are you sure to delete this record ?')) {
            $.ajax({
                type: 'post',
                url: "{{ route('pos.deactivate_record') }}",
                data: {
                    id: id,
                    admission_id: admission_id,
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
</script>
@endpush