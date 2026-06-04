@extends('layouts.'.config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('stylesheets')
<style>
    .table> :not(caption)>*>* {
        padding: 5px;
    }

    .header-buttons {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .btn i {
        margin-right: 5px;
    }
</style>

<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
@endpush

@section('content')

<div class="row">
    <div class="col-12">

        <!-- Traffic sources -->
        <div class="card">
            <div class="card-header header-elements-inline d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Consultant Management</h5>
                <div class="header-buttons">
                    <div class="btn btn-primary add_new_record me-2">
                        <i class="tf-icons bx bx-plus"></i> Add New Consultant
                    </div>
                    <a href="{{ route('pos.preview_consultants_pdf') }}" class="btn btn-info me-2" title="Preview PDF Report" target="_blank">
                        <i class="tf-icons bx bx-show"></i> Preview
                    </a>
                    <a href="{{ route('pos.export_consultants_pdf') }}" class="btn btn-success" title="Export All Consultants to PDF">
                        <i class="tf-icons bx bx-download"></i> Export PDF
                    </a>
                </div>
            </div>

            <div class="card-body">

                <div class="row">


                    <div class="col-12">

                        <div class="table-responsive" style="min-height: 200px">

                            <table id="users-list" class="table table-responsive table-striped data_mf_table table-condensed">

                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>CNIC</th>
                                        <th>Joining Date</th>
                                        <th>Department</th>
                                        <th>Speciality</th>
                                        <th>Type</th>
                                        <th>General OPD</th>
                                        <th>Consultant OPD</th>
                                        <th>Hospital Share Rs</th>
                                        <th>Consultant Share Rs</th>
                                        <th>Share (%age)</th>
                                        <th>Lab Share (%age)</th>

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
        <!-- /traffic sources -->
    </div>
</div>

<div class="modal fade" id="add_new_record_model" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form class="modal-content  form-submit-event" id="from_submit">
            <input type="hidden" id="id" name="id" value="0">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Add New Consultant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nameBasic" class="form-label">Consultant Name<span class="asterisk">*</span></label>
                        <input type="text" required id="name" name="name" class="form-control" placeholder="" autocomplete="off">
                    </div>

                    <div class="col-md-6 col-sm-4 mb-3">
                        <label class="form-label">CNIC (without -)<span class="asterisk">*</span></label>
                        <input type="number" required id="cnic" name="cnic"
                            oninput="if(this.value.length > 13) this.value = this.value.slice(0, 13);"
                            class="form-control" placeholder="" autocomplete="off">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="nameBasic" class="form-label">PMDC #<span class="asterisk">*</span></label>
                        <input type="text" id="pmdc_number" name="pmdc_number" class="form-control" placeholder="" autocomplete="off">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="nameBasic" class="form-label">Speciality<span
                                class="asterisk">*</span></label>
                        <select name="consultant_speciality_id" id="consultant_speciality_id" class="form-select">
                            <option selected value="">Select Speciality...</option>
                            @forelse ($speciality as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @empty



                            @endforelse
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="nameBasic" class="form-label">Department<span
                                class="asterisk">*</span></label>
                        <select name="consultant_department_id" id="consultant_department_id" class="form-select">
                            <option selected value="">Select Department...</option>
                            @forelse ($department as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>



                    <div class="col-md-6 col-sm-4 mb-3">
                        <label for="nameBasic" class="form-label">Joining Date<span
                                class="asterisk">*</span></label>
                        <input type="date" required id="joining_date" value="{{date("Y-m-d")}}" name="joining_date" class="form-control"
                            placeholder="" autocomplete="off">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="nameBasic" class="form-label">Type<span
                                class="asterisk">*</span></label>
                        <select name="consultant_type_id" id="consultant_type_id" class="form-select">
                            <option selected value="">Select Type...</option>
                            @forelse ($type as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @empty
                            @endforelse
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="nameBasic" class="form-label">Sehat Card Share (%age)<span
                                class="asterisk">*</span></label>
                        <input type="number" class="form-control" value="0" name="share_percentage" id="share_percentage">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="nameBasic" class="form-label">General OPD Fee<span
                                class="asterisk">*</span></label>
                        <input type="number" class="form-control" value="0" name="general_opd_fee" id="general_opd_fee">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="nameBasic" class="form-label">Hospital Share<span
                                class="asterisk">*</span></label>
                        <input type="number" class="form-control" value="0" name="general_opd_hospital_share" id="general_opd_hospital_share">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="nameBasic" class="form-label">Consultant Share<span
                                class="asterisk">*</span></label>
                        <input type="number" class="form-control" value="0" name="general_opd_consultant_share" id="general_opd_consultant_share">
                    </div>


                    <div class="col-md-4 mb-3">
                        <label for="nameBasic" class="form-label">Consultant OPD Fee<span
                                class="asterisk">*</span></label>
                        <input type="number" class="form-control" value="0" name="consultant_opd_fee" id="consultant_opd_fee">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="nameBasic" class="form-label">Hospital Share Rs.<span
                                class="asterisk">*</span></label>
                        <input type="number" class="form-control" value="0" name="hospital_share" id="hospital_share">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="nameBasic" class="form-label">Consultant Share Rs.<span
                                class="asterisk">*</span></label>
                        <input type="number" class="form-control" value="0" name="consultant_share" id="consultant_share">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="nameBasic" class="form-label">ER Fee<span
                                class="asterisk">*</span></label>
                        <input type="number" class="form-control" value="0" name="er_fee" id="er_fee">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="nameBasic" class="form-label">ER Hospital Share Rs.<span
                                class="asterisk">*</span></label>
                        <input type="number" class="form-control" value="0" name="er_hospital_share" id="er_hospital_share">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="nameBasic" class="form-label">ER Consultant Share Rs.<span
                                class="asterisk">*</span></label>
                        <input type="number" class="form-control" value="0" name="er_consultant_share" id="er_consultant_share">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="nameBasic" class="form-label">Lab Percentage<span
                                class="asterisk">*</span></label>
                        <input type="number" class="form-control" value="0" name="lab_percentage" id="lab_percentage">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="nameBasic" class="form-label">Lab Percentage<span
                                class="asterisk">*</span></label>
                        <textarea name="description" rows="6" id="description" class="form-control"
                            placeholder="e.g. FCPS Part 2<br>FCPS Cardiology"></textarea>
                    </div>




                </div>




            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Close </button>
                <button type="submit" id="submit_btn" class="btn btn-primary">Save</button>
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
    $("body").on("click", ".add_new_record", function(e) {
        $("#id").val(0);
        $("#name").val('');
        $("#consultant_speciality_id").val('');
        $("#pmdc_number").val('');
        $("#consultant_department_id").val('');
        $("#consultant_type_id").val('');
        $("#cnic").val('');
        $("#joining_date").val('');
        $("#share_percentage").val('');
        $("#general_opd_fee").val(0);
        $("#general_opd_hospital_share").val(0);
        $("#general_opd_consultant_share").val(0);
        $("#consultant_opd_fee").val(0);
        $("#hospital_share").val(0);
        $("#consultant_share").val(0);

        $("#er_fee").val(0);
        $("#er_hospital_share").val(0);
        $("#er_consultant_share").val(0);
        $("#lab_percentage").val(0);
        $("#description").val('');
        $("#add_new_record_model").modal("show");

    });

    $("body").on("click", ".edit_record", function(e) {
        record_id = $(this).attr("data-id");
        var details = JSON.parse($(this).attr("data-details"));

        $("#id").val(details.id);
        $("#name").val(details.name);
        $("#consultant_speciality_id").val(details.consultant_speciality_id);
        $("#pmdc_number").val(details.pmdc_number);
        $("#consultant_department_id").val(details.consultant_department_id);
        $("#consultant_type_id").val(details.consultant_type_id);
        $("#cnic").val(details.cnic);
        $("#joining_date").val(details.joining_date);
        $("#share_percentage").val(details.share_percentage);
        $("#general_opd_fee").val(details.general_opd_fee);
        $("#general_opd_hospital_share").val(details.general_opd_hospital_share);
        $("#general_opd_consultant_share").val(details.general_opd_consultant_share);
        $("#consultant_opd_fee").val(details.consultant_opd_fee);
        $("#hospital_share").val(details.hospital_share);
        $("#consultant_share").val(details.consultant_share);
        $("#er_fee").val(details.er_fee);
        $("#er_hospital_share").val(details.er_hospital_share);
        $("#er_consultant_share").val(details.er_consultant_share);
        $("#lab_percentage").val(details.lab_percentage);
        $("#description").val(details.description);
        $("#add_new_record_model").modal("show");
    });

    $("body").on("click", "#submit_btn", function(e) {
        e.preventDefault();

        $("#from_submit").ajaxSubmit({
            url: "{{route('pos.save_consultant')}}",
            type: 'post',
            data: {
                _token: '{{ csrf_token() }}'

            },
            success: function(response) {
                $("#add_new_record_model").modal("hide");
                user_table.ajax.reload();
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                //console.log();
                //alert("Status: " + textStatus); alert("Error: " + errorThrown);
                alert(JSON.parse(XMLHttpRequest.responseText).message);
            }
        });
    });

    $(document).ready(function() {

        district_id = "";
        police_station_id = [];
        leave_request_id = '';

        user_table = $('#users-list').DataTable({
            processing: true,
            serverSide: true,

            lengthMenu: [
                [100, 250, 500, 1000],
                ['100', '250', '500', '1000']
            ],
            pageLength: 50,
            ajax: {
                url: '{{route("pos.list_consultant")}}',
                data: function(d) {
                    d.user_id = $('#attendance_user_filter').val();
                    /*d.attendance_date_from = $('#attendance_date_from').val();
                    d.attendance_date_to = $('#attendance_date_to').val();*/


                }

            },

            columns: [

                {
                    data: 'name',
                    name: 'name',
                    searchable: true
                },
                {
                    data: 'cnic',
                    name: 'cnic',
                    searchable: true
                },
                {
                    data: 'joining_date',
                    name: 'joining_date',
                    searchable: true
                },
                {
                    data: 'consultant_department.name',
                    name: 'consultant_department.name',
                    searchable: true
                },
                {
                    data: 'consultant_speciality.name',
                    name: 'consultant_speciality.name',
                    searchable: true
                },
                {
                    data: 'consultant_type.name',
                    name: 'consultant_type.name',
                    searchable: true
                },
                {
                    data: 'general_opd_fee',
                    name: 'general_opd_fee',
                    searchable: true
                },
                {
                    data: 'consultant_opd_fee',
                    name: 'consultant_opd_fee',
                    searchable: true
                },
                {
                    data: 'hospital_share',
                    name: 'hospital_share',
                    searchable: true
                },
                {
                    data: 'consultant_share',
                    name: 'consultant_share',
                    searchable: true
                },
                {
                    data: 'share_percentage',
                    name: 'share_percentage',
                    searchable: true
                },
                {
                    data: 'lab_percentage',
                    name: 'lab_percentage',
                    searchable: true
                },
                {
                    data: 'action',
                    name: 'action',
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
                'copy', 'csv', 'excel', 'pdf', 'print',
                {
                    text: '<i class="tf-icons bx bx-file-pdf"></i> Custom PDF',
                    className: 'btn btn-danger',
                    action: function(e, dt, node, config) {
                        window.open('{{ route("pos.export_consultants_pdf") }}', '_blank');
                    }
                }
            ]
        });

        $('#attendance_user_filter, #attendance_date_from, #attendance_date_to').on('change', function(e) {
            e.preventDefault();
            user_table.ajax.reload();
        });

        $("body").on("click", ".delete_record", function(e) {
            var id = $(this).attr("data-id");
            if (confirm('Are you sure to delete this record ?')) {
                $.ajax({
                    type: 'post',
                    url: "{{ route('pos.deactivate_record') }}",
                    data: {
                        id: id,
                        table: "consultants",
                        _token: '{{ csrf_token() }}'

                    },
                    success: function(res) {
                        //user_table.dataTable.reload();
                        window.location.reload();
                    }
                })
            } else {
                alert('Why did you press cancel? You should have confirmed');
            }
        });
    });
</script>
@endpush