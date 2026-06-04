@extends('layouts.' . config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('stylesheets')
    <style>
        .table> :not(caption)>*>* {
            padding: 5px;
        }
        .red-row {
            background-color: #e9747f !important;
            color:white;
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
                <div class="card-body">
                    <form class=" form-submit-event" id="patient_admission">
                        <div class="row">
                            <input type="hidden" required id="id" name="id" value="0" class="form-control" />
                            <input type="hidden" name="g4no" class="form-control" value="0" placeholder="" autocomplete="off">


                            <div class="col-md-6 mb-3">
                                <label for="nameBasic" class="form-label">Patient<span class="asterisk">*</span></label>
                                <select name="patient_id" id="patient_id" class="form-control">
                                    <option value="">Select Patient</option>
                                    @foreach ($patients as $patient)
                                        <option value="{{ $patient->id }}">{{ $patient->name }} | {{ $patient->mr_no }} | {{ $patient->cnic }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 col-sm-4 mb-3">
                                <label for="nameBasic" class="form-label">Sehat Card Refrence No<span class="asterisk">*</span></label>
                                <input type="text"  id="sc_ref_no" name="sc_ref_no" class="form-control"
                                    placeholder="" autocomplete="off">
                            </div>

                            {{--<div class="col-md-2 col-sm-4 mb-3">
                                <label for="nameBasic" class="form-label">g4no<span class="asterisk">*</span></label>
                                <input type="text" required id="g4no" name="g4no" class="form-control"
                                    placeholder="" autocomplete="off">
                            </div>--}}

                            <div class="col-md-2 mb-3">
                                <label for="nameBasic" class="form-label">Admission Date<span
                                            class="asterisk">*</span></label>
                                <input type="date" value="{{ date('Y-m-d') }}" required id="admission_date" name="admission_date"
                                       class="form-control" placeholder="" autocomplete="off">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="nameBasic" class="form-label">Guardian Name<span
                                        class="asterisk">*</span></label>

                                <input type="text" required id="guardian_name" name="guardian_name" class="form-control"
                                    placeholder="" autocomplete="off">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="nameBasic" class="form-label">Emergency Contact#<span
                                        class="asterisk">*</span></label>

                                <input type="number" required id="emergency_contact_no" name="emergency_contact_no"
                                    oninput="if(this.value.length > 11) this.value = this.value.slice(0, 11);"
                                    class="form-control" placeholder="" autocomplete="off">
                            </div>

                            <div class="col-md-3 col-sm-4 mb-3">
                                <label for="nameBasic" class="form-label">Relation<span class="asterisk">*</span></label>
                                <select name="relation_id" required id="relation_id" class="form-select">
                                    <option value="">Select Relation</option>
                                    @foreach ($relations as $relation)
                                        <option value="{{ $relation->id }}">{{ $relation->name }}</option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="col-md-3 mb-3">
                                <label for="nameBasic" class="form-label">Ward<span class="asterisk">*</span></label>
                                <select name="ward_id" required id="ward_id" class="form-control">
                                    <option value="">Select Ward</option>
                                    @foreach ($wards as $ward)
                                        <option value="{{ $ward->id }}">{{ $ward->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="nameBasic" class="form-label">Bed<span class="asterisk">*</span></label>
                                <select name="bed_id" required id="bed_id" class="form-control">
                                    <option value="">Select Bed</option>

                                </select>
                            </div>



                            <div class="col-md-3 mb-3">
                                <label for="nameBasic" class="form-label">Consultant Name<span
                                        class="asterisk">*</span></label>

                                <select name="consultant_id" required id="consultant_id" class="form-control">
                                    <option value="">Consultant Name...</option>
                                    @foreach ($consultant as $value)
                                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                                    @endforeach
                                </select>
                            </div>



                            <div class="col-md-3 mb-3">
                                <label for="nameBasic" class="form-label">Procedure Type<span
                                        class="asterisk">*</span></label>

                                <select name="procedure_type_id" required id="procedure_type_id" class="form-control">
                                    <option value="">Select Procedure</option>
                                    @foreach ($procedure_type as $value)
                                        <option data-net_rate="{{ $value->net_rate }}" value="{{ $value->id }}">
                                            {{ $value->name }} (Rs: {{ $value->net_rate }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="nameBasic" class="form-label">Sub Consultant Name<span
                                            class="asterisk">*</span></label>

                                <select name="sub_consultant_id" id="sub_consultant_id" class="form-control">
                                    <option value="">Consultant Name...</option>
                                    @foreach ($consultant as $value)
                                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="nameBasic" class="form-label">Secondary Procedure<span
                                            class="asterisk">*</span></label>

                                <select name="sec_procedure_type_id"  id="sec_procedure_type_id" class="form-control">
                                    <option value="">Select Secondary Procedure</option>
                                    @foreach ($procedure_type as $value)
                                        <option data-net_rate="{{ $value->net_rate }}" value="{{ $value->id }}">
                                            {{ $value->name }} (Rs: {{ $value->net_rate }})</option>
                                    @endforeach
                                </select>
                            </div>



                            {{--<div class="col-md-3 mb-3">
                                <label for="nameBasic" class="form-label">Is Sub Consultant<span
                                            class="asterisk">*</span></label>
                                <input type="checkbox" name="is_sub_consultant" class="form-control is_sub_consultant" value="0">

                            </div>--}}



                            <div class="text-left mt-3">
                                <button class="btn btn-success" id="submit_btn">Admit</button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>


            {{-- LISTIN PATIENTS --}}
            <div class="card my-5">
                <div class="card-body">
                    <h5 class="card-title">Admitted Patients List</h5>

                    <div class="table-responsive" style="min-height: 200px">

                        <table id="patient-list"
                            class="table table-responsive data_mf_table table-condensed">
                            <thead>
                                <tr>
                                    <th width="15%">Patient MR no.</th>


                                    <th style="display: none;">MR</th>
                                    <th style="display: none;">Refrence#</th>

                                    <th>Guardian Name</th>
                                    <th>Consultant</th>
                                    <th>Sub Consultant</th>
                                    <th>Procedure Name</th>
                                    <th>Procedure Type</th>
                                    <th>Ward Name</th>
                                    <th>Bed no.</th>

                                    <th>Admission Date</th>
                                    <th>Discharge Date</th>
                                    <th>Status</th>
                                    {{--<th>Total Cost</th>
                                    <th>Balance</th>--}}
                                    <th style="width: 10%">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
            <!-- /traffic sources -->
        </div>
    </div>
@endsection
<div class="modal fade" id="cancel_admission_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form class="modal-content form-submit-event" id="cancel_admission_form">
            <input type="hidden" id="cancel_admission_id" name="id" value="0">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Cancel Admission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">

                    <div class="col-md-12 mb-3">
                        <label for="nameBasic" class="form-label">Reason<span class="asterisk">*</span></label>
                        <textarea class="form-control" id="reason" name="canelation_reason" cols="15"></textarea>
                    </div>
                </div>




            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Close                </button>
                <button type="submit" id="submit_btn" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade my_modal" id="patient_admission_edit_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog " role="document">
        <form class="modal-content form-submit-event" id="cancel_admission_form">
            <input type="hidden" id="cancel_admission_id" name="id" value="0">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Update Admission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">

                    <div class="col-md-12 mb-3">
                        <label for="nameBasic" class="form-label">Sehat Card Reference Number<span
                                    class="asterisk">*</span></label>
                        <input type="text" class="form-control" pattern="\d{8,}"  style="font-weight: bold; color:red" required id="edit_sc_ref_no" name="edit_sc_ref_no" value="">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="nameBasic" class="form-label">Consultant Name<span
                                    class="asterisk">*</span></label>
                        <input id="edit_admission_id" value="" type="hidden">
                        <select name="consultant_id"  id="edit_consultant_id" class="form-select">
                            <option value="">Select Consultant</option>
                            @foreach ($consultant as $value)
                                <option date_consultant_share="{{$value->share_percentage}}" value="{{ $value->id }}">{{ $value->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="nameBasic" class="form-label">Sub Consultant Name<span
                                    class="asterisk">*</span></label>
                        <select name="edit_sub_consultant_id"  id="edit_sub_consultant_id" class="form-select">
                            <option value="">Select Consultant</option>
                            @foreach ($consultant as $value)
                                <option date_consultant_share="{{$value->share_percentage}}" value="{{ $value->id }}">{{ $value->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="nameBasic" class="form-label">Procedure Type<span
                                    class="asterisk">*</span></label>

                        <select id="edit_procedure_type_id" class="form-select">
                            <option value="">Select Procedure...</option>
                            @foreach ($procedure_type as $value)
                                <option data-net_rate="{{ $value->net_rate }}" value="{{ $value->id }}">
                                    {{ $value->name }} (Rs: {{ $value->net_rate }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="nameBasic" class="form-label">Secondary Procedure Type<span
                                    class="asterisk">*</span></label>

                        <select id="edit_sec_procedure_type_id" class="form-select">
                            <option value="">Select Secondary Procedure...</option>
                            @foreach ($procedure_type as $value)
                                <option data-net_rate="{{ $value->net_rate }}" value="{{ $value->id }}">
                                    {{ $value->name }} (Rs: {{ $value->net_rate }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>




            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Close                </button>
                <div id="update_admission" class="btn btn-primary">Update</div>
            </div>
        </form>
    </div>
</div>
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
            $("#patient_id").select2();
            $("#procedure_type_id").select2();
            $("#sec_procedure_type_id").select2();
            $("#relation_id").select2();
            $("#sub_consultant_id").select2();
            $("#consultant_id").select2();
            $("#ward_id").select2();
            $("#bed_id").select2();
            $("#edit_consultant_id").select2({dropdownParent: $('.my_modal')});
            $("#edit_sub_consultant_id").select2({dropdownParent: $('.my_modal')});
            $("#edit_procedure_type_id").select2({dropdownParent: $('.my_modal')});
            $("#edit_sec_procedure_type_id").select2({dropdownParent: $('.my_modal')});
        }, 1000);

        $("body").on("change","#procedure_type_id",function (e) {
            $("#sec_procedure_type_id").val('').trigger("change");
        });
        $("body").on("change","#sec_procedure_type_id",function (e) {
            var procedure_type_id = $("#procedure_type_id").val();
            var sec_procedure_type_id = $("#sec_procedure_type_id").val();
            if(procedure_type_id == ''){
                alert("Please Select Primary Procedure");
                $("#procedure_type_id").select2('open');
                return false;
            }
            if(procedure_type_id == sec_procedure_type_id){
                alert("Primary and Secondary Procedure will not be same. Please select another");
                $("#sec_procedure_type_id").val('').trigger('change');
                return false;
            }
        });


        $("body").on("click","#update_admission",function (e) {
           var consultant_id =  $("#edit_consultant_id").val();
           var edit_sc_ref_no =  $("#edit_sc_ref_no").val();
           var sub_consultant_id =  $("#edit_sub_consultant_id").val();
           var procedure_type_id =  $("#edit_procedure_type_id").val();
           var sec_procedure_type_id =  $("#edit_sec_procedure_type_id").val();
            var admission_id = $('#edit_admission_id').val();

            var consultant_share = $('#edit_consultant_id').find(':selected').attr('date_consultant_share');
            var procedure_rate = $('#edit_procedure_type_id').find(':selected').attr('data-net_rate');
            var sec_procedure_rate = $('#edit_sec_procedure_type_id').find(':selected').attr('data-net_rate');

            $.ajax({
                type: "post",
                url: "{{ route('pos.update_patient_admission') }}",
                data: {
                    edit_sc_ref_no: edit_sc_ref_no,
                    consultant_id: consultant_id,
                    sub_consultant_id: sub_consultant_id,
                    procedure_type_id: procedure_type_id,
                    sec_procedure_type_id: sec_procedure_type_id,
                    admission_id: admission_id,
                    consultant_share: consultant_share,
                    procedure_rate: procedure_rate,
                    sec_procedure_rate: sec_procedure_rate,
                    "_token": "{{ csrf_token() }}"
                },
                success: function(res) {
                    $("#edit_consultant_id").val('').trigger("change");
                    $("#edit_sub_consultant_id").val('').trigger("change");
                    $("#procedure_type_id").val('').trigger("change");
                    $("#edit_sec_procedure_type_id").val('').trigger("change");
                    $("#edit_admission_id").val('');
                    $("#edit_sc_ref_no").val('');
                    $('#patient_admission_edit_modal').modal("hide");
                    user_table.ajax.reload();

                }
            });
        });

        $("body").on("click",".cancel_admission",function (e) {
            record_id = $(this).attr("data-id");
            $("#cancel_admission_id").val(record_id);

            $("#canelation_reason").val('');
            $("#cancel_admission_modal").modal("show");
        });
        user_table = $('#patient-list').DataTable({
            processing: true,
            serverSide: true,

            pageLength: 10,
            ajax: {
                url: "{{ route('pos.list_admission') }}",


            },

            columns: [
                {
                    data: null, // Use `null` because we will combine two fields
                    name: 'patient.name', // Use one field for search or sorting
                    searchable: true,
                    render: function(data, type, row) {
                        return `<b style='font-size:12px' class="custom-class another-class">${row.patient.name.toUpperCase()}</b><br><b style='font-size:12px; color:red'>${row.patient.mr_no}</b><br><b style='font-size:12px; color:green'>Referal#: ${row.sc_ref_no}</b>`;
                    }
                },
                {
                    data: 'patient.mr_no',
                    name: 'patient.mr_no',
                    visible: false // This column will be hidden
                },
                {
                    data: 'sc_ref_no',
                    name: 'sc_ref_no',
                    visible: false
                },


                {
                    data: 'guardian_name',
                    name: 'guardian_name',
                    searchable: true
                },
                {
                    data: 'consultant.name',
                    name: 'consultant.name',
                    searchable: true
                },
                {
                    data: null,
                    name: 'sub_consultant.name',
                    searchable: true,
                    render: function(data, type, row) {
                        if (row.sub_consultant) {
                            return row.sub_consultant.name;
                        }else{
                            return ''
                        }
                        // Return the actual data if it exists

                    }
                },
                {
                    data: 'procedure_type.name',
                    name: 'procedure_type.name',
                    searchable: true
                },
                {
                    data: 'procedure_type.type',
                    name: 'procedure_type.type',
                    searchable: true
                },
                {
                    data: 'ward.name',
                    name: 'ward.name',
                    searchable: true
                },
                {
                    data: 'bed.name',
                    name: 'bed.name',
                    searchable: true
                },



                {
                    data: 'admission_date',
                    name: 'admission_date',
                    searchable: true
                },
                {
                    data: 'discharge_date',
                    name: 'discharge_date',
                    searchable: true
                },
                {
                    data: 'admission_status',
                    name: 'admission_status',
                    searchable: true
                },
                /*{
                    data: 'totalCost',
                    name: 'totalCost',
                    searchable: true
                },
                {
                    data: 'balance',
                    name: 'balance',
                    searchable: true
                },*/

                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false
                },

            ],
            createdRow: function(row, data, dataIndex) {

                if (data.alert == true) { // Change condition as needed
                    $(row).addClass('red-row');
                }
            },

            responsive: true,
            processing: true,
            serverSide: true,
            searching: true,
            sorting: true,
            paging: true,

        });






        $("body").on("change", "#ward_id", function() {
            getBeds();
        });

        function getBeds() {
            let bedId = $("#ward_id").val();
            $.ajax({
                type: "post",
                url: "{{ route('pos.ward_bed') }}",
                data: {
                    id: bedId,
                    "_token": "{{ csrf_token() }}"
                },
                success: function(res) {
                    if (res.status)

                        $("#bed_id").find("option").not(":first").remove();

                    if (res.data.length > 0) {
                        $.each(res.data, function(key, bed) {
                            $("#bed_id").append(
                                `<option value="${bed.id}">${bed.name}</option>`
                            )
                        });
                    }
                }
            });
        }

        function getEditBeds(ward_id, bed_id) {

            $.ajax({
                type: "post",
                url: "{{ route('pos.ward_bed') }}",
                data: {
                    id: ward_id,
                    "_token": "{{ csrf_token() }}"
                },
                success: function(res) {
                    if (res.status)

                        $("#bed_id").find("option").not(":first").remove();

                    if (res.data.length > 0) {
                        $.each(res.data, function(key, bed) {
                            $("#bed_id").append(
                                `<option value="${bed.id}">${bed.name}</option>`
                            )
                        });
                    }

                    setTimeout(function() {
                        $("#bed_id").val(bed_id);
                    }, 1000);
                }
            });
        }


        $("#patient_admission").on("submit", function(e) {

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

                $("#patient_admission").ajaxSubmit({
                    url: '{{ route('pos.store_patient_admission') }}',
                    type: 'post',
                    data: {
                        _token: '{{ csrf_token() }}'

                    },
                    success: function(response) {

                        window.location.reload();
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        //console.log();
                        //alert("Status: " + textStatus); alert("Error: " + errorThrown);
                        alert(JSON.parse(XMLHttpRequest.responseText).message);
                    }
                });
            }
        });

        function reset_fields() {
            $("#id").val(0);
            $("#patient_id").val('');
            $("#bed_id").val('').trigger("change");
            $("#ward_id").val('').trigger("change");
            $("#procedure_type_id").val('').trigger("change");
            $("#consultant_id").val('').trigger("change");
            $("#admission_data").val('');
            $("#emergency_contact_no").val('');
            $("#guardian_name").val('');
            $("#admission_date").val('');
            $("#sc_ref_no").val('');
            $("#g4no").val('');
        }

        $("body").on("click", ".edit_record", function(e) {
            record_id = $(this).attr("data-id");
            var details = JSON.parse($(this).attr("data-details"));
            var fullDateTime = details.admission_date;
            let dateOnly = fullDateTime.split(' ')[0];

            $('#edit_admission_id').val(details.id);
            $('#edit_sc_ref_no').val(details.sc_ref_no);
            $('#edit_consultant_id').val(details.consultant_id).trigger('change');
            $('#edit_sub_consultant_id').val(details.sub_consultant_id).trigger('change');
            $('#edit_procedure_type_id').val(details.procedure_type_id).trigger('change');
            $('#edit_sec_procedure_type_id').val(details.sec_procedure_type_id).trigger('change');
            $('#patient_admission_edit_modal').modal("show");


            return false;

            $("#id").val(details.id);

            $("#admission_date").val(details.admission_date_edit);
            $("#sc_ref_no").val(details.sc_ref_no);
            $("#g4no").val(details.g4no);
            $("#emergency_contact_no").val(details.emergency_contact_no);
            $("#guardian_name").val(details.guardian_name);
            $("#procedure_type_id").val(details.procedure_type_id).trigger("change");
            $("#consultant_id").val(details.consultant_id).trigger("change");
            $("#bed_id").val(details.bed_id);
            $("#patient_id").val(details.patient_id).trigger('change');
            $("#ward_id").val(details.ward_id).trigger('change');
            $("#relation_id").val(details.relation_id).trigger("change");
            $("#admission_date").val(dateOnly);
            getEditBeds(details.ward_id, details.bed_id);

        });


        $("body").on("click", ".delete_record", function(e) {
            var id = $(this).attr("data-id");
            if (confirm('Are you sure to delete this record ?')) {
                $.ajax({
                    type: 'post',
                    url: "{{ route('pos.deactivate_record') }}",
                    data: {
                        id: id,
                        table: "patient_admissions",
                        _token: '{{ csrf_token() }}'

                    },
                    success: function(res) {
                        user_table.ajax.reload();
                        //window.location.reload();
                    }
                })
            } else {
                alert('Why did you press cancel? You should have confirmed');
            }
        });

        $("#cancel_admission_form").on("submit", function(e) {

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

                $("#cancel_admission_form").ajaxSubmit({
                    url: '{{ route('pos.cencel_patient_admission') }}',
                    type: 'post',
                    data: {
                        _token: '{{ csrf_token() }}'

                    },
                    success: function(response) {
                        console.log(response);
                        reset_fields();
                        $("#cancel_admission_modal").modal("hide");
                        user_table.ajax.reload();
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        //console.log();
                        //alert("Status: " + textStatus); alert("Error: " + errorThrown);
                        alert(JSON.parse(XMLHttpRequest.responseText).message);
                    }
                });
            }
        });
    </script>
@endpush
