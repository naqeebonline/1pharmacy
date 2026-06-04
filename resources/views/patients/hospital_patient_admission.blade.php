@extends('layouts.' . config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('stylesheets')
<style>
    .table> :not(caption)>*>* {
        padding: 5px;
    }

    .red-row {
        background-color: #e9747f !important;
        color: white;
    }

    .select2-container--default .select2-selection--single {
        min-height: 28px !important;
        height: 28px !important;
        padding: 0 6px !important;
        font-size: 0.8rem !important;
        border-radius: 0.375rem !important;
        display: flex !important;
        align-items: center !important;
        border: 1px solid #d1d5db !important;
    }

    .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 26px !important;
        padding-left: 0 !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 28px !important;
    }

    /* Professional Compact Form Styling */
    .admission-form-card {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        background: #ffffff;
    }

    .form-header {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: white;
        padding: 15px 20px;
        border-radius: 8px 8px 0 0;
        margin: -1px -1px 0 -1px;
    }

    .form-header h5 {
        margin: 0;
        font-weight: 600;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
    }

    .form-header i {
        margin-right: 6px;
        font-size: 0.9rem;
    }

    .compact-section {
        background: #f8fafc;
        border-left: 3px solid #3b82f6;
        padding: 10px 12px;
        margin-bottom: 12px;
        border-radius: 0 6px 6px 0;
    }

    .section-title {
        font-size: 0.8rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
    }

    .section-title i {
        margin-right: 5px;
        color: #3b82f6;
        font-size: 0.75rem;
    }

    .form-control-sm,
    .form-select-sm {
        padding: 0.25rem 0.4rem;
        font-size: 0.8rem;
        border-radius: 0.375rem;
        border: 1px solid #d1d5db;
        height: 28px;
    }

    .form-control-sm:focus,
    .form-select-sm:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 0.125rem rgba(59, 130, 246, 0.25);
    }

    .form-label {
        font-size: 0.75rem;
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.25rem;
    }

    .asterisk {
        color: #ef4444;
        font-weight: bold;
    }

    .btn-admit {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border: none;
        border-radius: 6px;
        padding: 6px 20px;
        font-weight: 600;
        font-size: 0.8rem;
        color: white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
    }

    .btn-admit:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .input-icon {
        position: relative;
    }

    .input-icon i {
        position: absolute;
        left: 6px;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
        font-size: 0.7rem;
        z-index: 3;
    }

    .input-icon .form-control-sm {
        padding-left: 22px;
    }

    .row.g-2>* {
        padding-right: calc(var(--bs-gutter-x) * 0.25);
        padding-left: calc(var(--bs-gutter-x) * 0.25);
    }
</style>

<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
@endpush

@section('content')
<div class="row">
    <div class="col-12">

        <!-- Patient Admission Form -->
        <div class="card admission-form-card">
            <div class="form-header">
                <h5 style="color: white;"><i class="fas fa-user-plus"></i>Patient Admission Form</h5>
            </div>
            <div class="card-body p-3">
                <form class="form-submit-event" id="patient_admission">
                    <input type="hidden" required id="id" name="id" value="0" class="form-control form-control-sm" />
                    <input type="hidden" name="g4no" class="form-control form-control-sm" value="0" placeholder="" autocomplete="off">

                    <!-- Patient & Basic Info -->
                    <div class="compact-section">
                        <div class="section-title"><i class="fas fa-user"></i>Patient Information</div>
                        <div class="row g-2">
                            <div class="col-md-4 mb-2">
                                <label for="patient_id" class="form-label">Patient<span class="asterisk">*</span></label>
                                <select name="patient_id" id="patient_id" class="form-control form-control-sm">
                                    <option value="">Select Patient</option>
                                    @foreach ($patients as $patient)
                                    <option value="{{ $patient->id }}">{{ $patient->name }} | {{ $patient->mr_no }} | {{ $patient->cnic }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="guardian_name" class="form-label">Guardian Name<span class="asterisk">*</span></label>
                                <input type="text" required id="guardian_name" name="guardian_name" class="form-control form-control-sm" placeholder="Guardian name" autocomplete="off">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="admission_date" class="form-label">Admission Date<span class="asterisk">*</span></label>
                                <input type="date" value="{{ date('Y-m-d') }}" required id="admission_date" name="admission_date" class="form-control form-control-sm" autocomplete="off">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="emergency_contact_no" class="form-label">Emergency Contact<span class="asterisk">*</span></label>
                                <div class="input-icon">
                                    <i class="fas fa-phone"></i>
                                    <input type="number" required id="emergency_contact_no" name="emergency_contact_no" oninput="if(this.value.length > 11) this.value = this.value.slice(0, 11);" class="form-control form-control-sm" placeholder="03xxxxxxxxx" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Relation & Ward Info -->
                    <div class="compact-section">
                        <div class="section-title"><i class="fas fa-bed"></i>Ward Assignment</div>
                        <div class="row g-2">
                            <div class="col-md-3 mb-2">
                                <label for="relation_id" class="form-label">Relation<span class="asterisk">*</span></label>
                                <select name="relation_id" required id="relation_id" class="form-select form-select-sm">
                                    <option value="">Select Relation</option>
                                    @foreach ($relations as $relation)
                                    <option value="{{ $relation->id }}">{{ $relation->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="ward_id" class="form-label">Ward<span class="asterisk">*</span></label>
                                <select name="ward_id" required id="ward_id" class="form-control form-control-sm">
                                    <option value="">Select Ward</option>
                                    @foreach ($wards as $ward)
                                    <option value="{{ $ward->id }}">{{ $ward->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="bed_id" class="form-label">Bed<span class="asterisk">*</span></label>
                                <select name="bed_id" required id="bed_id" class="form-control form-control-sm">
                                    <option value="">Select Bed</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="included_medicine" class="form-label">Medicine Include<span class="asterisk">*</span></label>
                                <select name="included_medicine" required id="included_medicine" class="form-select form-select-sm">
                                    <option value="">Select Option</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Medical & Payment Info -->
                    <div class="compact-section">
                        <div class="section-title"><i class="fas fa-stethoscope"></i>Medical & Payment Details</div>
                        <div class="row g-2">
                            <div class="col-md-3 mb-2">
                                <label for="consultant_id" class="form-label">Consultant<span class="asterisk">*</span></label>
                                <select name="consultant_id" required id="consultant_id" class="form-control form-control-sm">
                                    <option value="">Select Consultant</option>
                                    @foreach ($consultant as $value)
                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="consultant_procedure_id" class="form-label">Procedure Type<span class="asterisk">*</span></label>
                                <select name="consultant_procedure_id" required id="consultant_procedure_id" class="form-control form-control-sm">
                                    <option value="">Select Procedure</option>
                                    @foreach ($procedure_type as $value)
                                    <option data-net_rate="{{ $value->net_rate }}" value="{{ $value->id }}">{{ $value->name }} (Rs: {{ $value->net_rate }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="procedure_rate" class="form-label">Rate<span class="asterisk">*</span></label>
                                <div class="input-icon">
                                    <i class="fas fa-rupee-sign"></i>
                                    <input type="number" required id="procedure_rate" name="procedure_rate" oninput="if(this.value.length > 11) this.value = this.value.slice(0, 11);" class="form-control form-control-sm" placeholder="0.00" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="advance_payment" class="form-label">Advance</label>
                                <div class="input-icon">
                                    <i class="fas fa-rupee-sign"></i>
                                    <input type="number" required id="advance_payment" name="advance_payment" oninput="if(this.value.length > 11) this.value = this.value.slice(0, 11);" class="form-control form-control-sm" placeholder="0.00" value="0" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="security_amount" class="form-label">Security</label>
                                <div class="input-icon">
                                    <i class="fas fa-rupee-sign"></i>
                                    <input type="number" required id="security_amount" name="security_amount" oninput="if(this.value.length > 11) this.value = this.value.slice(0, 11);" class="form-control form-control-sm" placeholder="0.00" value="0" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-center mt-3">
                        <button class="btn btn-admit" id="submit_btn" type="submit">
                            <i class="fas fa-user-plus me-1"></i>Admit Patient
                        </button>
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


                                <th>Guardian Name</th>
                                <th>Consultant</th>

                                <th>Procedure Name</th>
                                <th>Procedure Type</th>
                                <th>Procedure Rate</th>
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
                        <textarea class="form-control form-control-sm" id="reason" name="canelation_reason" cols="15"></textarea>
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

<div class="modal fade my_modal" id="patient_admission_edit_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog " role="document">
        <form class="modal-content form-submit-event" id="cancel_admission_forms">
            <input type="hidden" id="cancel_admission_id" name="id" value="0">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Update Admission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">



                    <div class="col-md-12 mb-3">
                        <label for="nameBasic" class="form-label">Consultant Name<span
                                class="asterisk">*</span></label>
                        <input id="edit_admission_id" value="" type="hidden">
                        <select name="consultant_id" id="edit_consultant_id" class="form-select form-select-sm">
                            <option value="">Select Consultant</option>
                            @foreach ($consultant as $value)
                            <option date_consultant_share="{{$value->share_percentage}}" value="{{ $value->id }}">{{ $value->name }}</option>
                            @endforeach
                        </select>
                    </div>



                    <div class="col-md-12 mb-3">
                        <label for="nameBasic" class="form-label">Procedure Type<span
                                class="asterisk">*</span></label>

                        <select id="edit_consultant_procedure_id" class="form-select form-select-sm">
                            <option value="">Select Procedure...</option>
                            @foreach ($procedure_type as $value)
                            <option data-net_rate="{{ $value->net_rate }}" value="{{ $value->id }}">
                                {{ $value->name }} (Rs: {{ $value->net_rate }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="nameBasic" class="form-label">Procedure Rate<span
                                class="asterisk">*</span></label>
                        <input type="number" class="form-control form-control-sm" id="edit_procedure_rate" name="edit_procedure_rate">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="nameBasic" class="form-label">Patient Advance Payment<span
                                class="asterisk">*</span></label>
                        <input type="number" class="form-control form-control-sm" id="edit_advance_payment" name="edit_advance_payment">
                    </div>

                    <div class="col-md-12 col-sm-4 mb-3">
                        <label for="nameBasic" class="form-label">Is Medicine Include<span class="asterisk">*</span></label>
                        <select name="edit_included_medicine" required id="edit_included_medicine" class="form-select form-select-sm">
                            <option value="">Select Relation</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>


                </div>




            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Close </button>
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
        $("#consultant_procedure_id").select2();

        $("#relation_id").select2();

        $("#consultant_id").select2();
        $("#ward_id").select2();
        $("#bed_id").select2();
        $("#edit_consultant_id").select2({
            dropdownParent: $('.my_modal')
        });
        $("#edit_consultant_procedure_id").select2({
            dropdownParent: $('.my_modal')
        });

    }, 1000);

    $("body").on("change", "#consultant_id", function(e) {
        var consultant_id = $(this).val();
        get_procedures(consultant_id);

    });

    $("body").on("change", "#edit_consultant_id", function(e) {
        var consultant_id = $(this).val();
        get_edit_procedures(consultant_id, "");

    });

    function get_procedures(consultant_id) {
        $.ajax({
            type: "post",
            url: "{{ route('pos.get_consultant_procedures') }}",
            data: {
                consultant_id: consultant_id,
                "_token": "{{ csrf_token() }}"
            },
            success: function(res) {
                let $select = $('#consultant_procedure_id'); // Replace with your <select> element ID
                $select.empty(); // Clear existing options
                $select.append(`<option value="" data-net_rate=""></option>`);
                $.each(res.data, function(index, value) {
                    let option = `<option data-net_rate="${value.amount}" value="${value.id}">
                            ${value.procedure_name} (Rs: ${value.amount})
                          </option>`;
                    $select.append(option);
                });

                $select.select2('destroy').select2({
                    placeholder: "Select Procedure....", // optional
                    allowClear: true // optional
                });

            }
        });
    }

    function get_edit_procedures(consultant_id, consultant_procedure_id) {
        $.ajax({
            type: "post",
            url: "{{ route('pos.get_consultant_procedures') }}",
            data: {
                consultant_id: consultant_id,
                "_token": "{{ csrf_token() }}"
            },
            success: function(res) {

                let $select = $('#edit_consultant_procedure_id'); // Replace with your <select> element ID
                $select.empty(); // Clear existing options
                $select.append(`<option value="" data-net_rate=""></option>`);
                $.each(res.data, function(index, value) {
                    let option = `<option data-net_rate="${value.amount}"  value="${value.id}">
                            ${value.procedure_name} (Rs: ${value.amount})
                          </option>`;
                    $select.append(option);
                });

                /*$select.select2('destroy').select2({
                    placeholder: "Select Procedure....", // optional
                    allowClear: true                // optional
                });*/

                setTimeout(function(e) {
                    $("#edit_consultant_procedure_id").val(consultant_procedure_id).trigger("change");
                    $("#edit_consultant_procedure_id").val(consultant_procedure_id).trigger("change");
                    $("#edit_consultant_procedure_id").val(consultant_procedure_id).trigger("change");

                }, 3000);

            }
        });
    }


    $("body").on("change", "#edit_consultant_procedure_id", function(e) {
        var procedure_rate = $('#edit_consultant_procedure_id').find(':selected').attr('data-net_rate');
        $("#edit_procedure_rate").val(procedure_rate);

    });

    $("body").on("change", "#consultant_procedure_id", function(e) {
        var procedure_rate = $('#consultant_procedure_id').find(':selected').attr('data-net_rate');
        $("#procedure_rate").val(procedure_rate);

    });



    $("body").on("click", "#update_admission", function(e) {
        var consultant_id = $("#edit_consultant_id").val();

        var consultant_procedure_id = $("#edit_consultant_procedure_id").val();

        var edit_procedure_rate = $("#edit_procedure_rate").val();
        var edit_advance_payment = $("#edit_advance_payment").val();
        var edit_included_medicine = $("#edit_included_medicine").val();
        var admission_id = $('#edit_admission_id').val();
        var consultant_share = $('#edit_consultant_id').find(':selected').attr('date_consultant_share');


        if (consultant_id == '') {
            alert("Please Select Consultant...");
            return false;
        }
        if (consultant_procedure_id == '') {
            alert("Please Select Procedure Type");
            return false;
        }

        if (edit_procedure_rate == '' || edit_procedure_rate == 0) {
            alert("Please enter procedure rate");
            return false;
        }

        if (edit_advance_payment == '') {
            alert("Please Advance amount. if no advance please enter 0.");
            return false;
        }
        if (edit_included_medicine == '') {
            alert("Please select medicine include or not.");
            return false;
        }


        $.ajax({
            type: "post",
            url: "{{ route('pos.update_in_patient_admission') }}",
            data: {

                consultant_id: consultant_id,
                procedure_rate: edit_procedure_rate,
                advance_payment: edit_advance_payment,
                included_medicine: edit_included_medicine,

                consultant_procedure_id: consultant_procedure_id,
                admission_id: admission_id,
                consultant_share: consultant_share,
                "_token": "{{ csrf_token() }}"
            },
            success: function(res) {
                $("#edit_consultant_id").val('').trigger("change");

                $("#consultant_procedure_id").val('').trigger("change");
                $("#edit_admission_id").val('');
                $("#edit_advance_payment").val(0);
                $("#edit_included_medicine").val('');

                $('#patient_admission_edit_modal').modal("hide");
                user_table.ajax.reload();

            }
        });
    });

    $("body").on("click", ".cancel_admission", function(e) {
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
            url: "{{ route('pos.hospital_list_admission') }}",


        },

        columns: [{
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
                data: 'guardian_name',
                name: 'guardian_name',
                searchable: true
            },
            {
                data: 'consultant.name',
                name: 'consultant.name',
                searchable: true
            },
            /*{
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
            },*/
            {
                data: 'consultant_procedure.procedure.name',
                name: 'consultant_procedure.procedure.name',
                searchable: true
            },
            {
                data: 'consultant_procedure.procedure.type',
                name: 'consultant_procedure.procedure.type',
                searchable: true
            },
            {
                data: 'procedure_rate',
                name: 'procedure_rate',
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
                url: "{{ route('pos.hospital_store_patient_admission') }}",
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
        $("#consultant_procedure_id").val('').trigger("change");
        $("#consultant_id").val('').trigger("change");
        $("#admission_data").val('');
        $("#emergency_contact_no").val('');
        $("#guardian_name").val('');
        $("#admission_date").val('');

        $("#g4no").val('');
    }

    $("body").on("click", ".edit_record", function(e) {

        record_id = $(this).attr("data-id");

        var details = JSON.parse($(this).attr("data-details"));
        var fullDateTime = details.admission_date;
        let dateOnly = fullDateTime.split(' ')[0];

        $('#edit_admission_id').val(details.id);

        $('#edit_advance_payment').val(details.advance_payment);
        $('#edit_included_medicine').val(details.included_medicine);

        $('#edit_consultant_id').val(details.consultant_id).trigger('change');

        //$('#edit_consultant_procedure_id').val(details.consultant_procedure_id).trigger('change');
        get_edit_procedures(details.consultant_id, details.consultant_procedure_id);
        $("#update_admission").hide();
        setTimeout(function() {
            $('#edit_procedure_rate').val(details.procedure_rate);
            $("#update_admission").show();
        }, 4000);


        $('#patient_admission_edit_modal').modal("show");


        return false;

        $("#id").val(details.id);

        $("#admission_date").val(details.admission_date_edit);

        $("#g4no").val(details.g4no);
        $("#emergency_contact_no").val(details.emergency_contact_no);
        $("#guardian_name").val(details.guardian_name);
        $("#consultant_procedure_id").val(details.consultant_procedure_id).trigger("change");
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
                    table: "in_patient_admissions",
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
                url: "{{ route('pos.cancel_in_patient_admission') }}",
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