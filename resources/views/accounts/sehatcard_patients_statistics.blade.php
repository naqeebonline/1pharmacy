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


                                <th>Consultant</th>

                                <th>Procedure Name</th>



                                <th>Admission Date</th>

                                <th>Procedure Rate</th>
                                <th style="width: 20%">Cost </th>

                                <th>Total Cost </th>
                                <th>Balance </th>
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

            pageLength: 50,
            ajax: {
                url: "{{ route('pos.list_admission_statistics') }}",


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
                    data: 'consultant.name',
                    name: 'consultant.name',
                    searchable: true
                },
               /* {
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
                    data: 'procedure_type.name',
                    name: 'procedure_type.name',
                    searchable: true
                },
                /*{
                    data: 'procedure_type.type',
                    name: 'procedure_type.type',
                    searchable: true
                },*/




                {
                    data: 'admission_date',
                    name: 'admission_date',
                    searchable: true
                },

                {
                    data: 'procedure_rate',
                    name: 'procedure_rate',
                    searchable: false
                },

                {
                    data: null,
                    name: 'investigation_cost',
                    searchable: false,
                    render: function(data, type, row) {
                        return `
                                <b style='font-size:12px' class="custom-class another-class">Con.Share: ${row.consultant_share_amount}</b><br>
                                <b style='font-size:12px' class="custom-class another-class">Inv Cost: ${row.investigation_cost}</b><br>
                                <b style='font-size:12px' class="custom-class another-class">Medical Cost: ${row.medicine_cost}</b><br>
                                <b style='font-size:12px' class="custom-class another-class">Service Charges: ${row.service_charges_cost}</b>

                                `;
                    }
                },



                {

                    data: null,
                    name: 'totalCost',
                    searchable: false,
                    render: function(data, type, row) {
                        return row.totalCost.toFixed(2);
                    }
                },

                {

                    data: null,
                    name: 'balance',
                    searchable: false,
                    render: function(data, type, row) {
                        return row.balance.toFixed(2);
                    }
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
