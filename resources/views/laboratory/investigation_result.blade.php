@extends('layouts.' . config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('stylesheets')
    <style>
        .table> :not(caption)>*>* {
            padding: 5px;
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
                    <h5 class="card-title">Investigation List</h5>

                    <div class="table-responsive" style="min-height: 200px">

                        <table id="investigation-list"
                            class="table table-responsive table-striped data_mf_table table-condensed">
                            <thead>
                                <tr>
                                    <th>Inv.No</th>
                                    <th>Patient MR no.</th>
                                    <th>Patient Name</th>
                                    <th>Gender</th>
                                    <th>Investigation Category</th>
                                    <th>Request Date</th>
                                    <th>Result Date</th>
                                    <th>Patient Type</th>
                                    <th>Status</th>

                                    {{-- <th>Status</th> --}}
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

             <div class="card my-5">
                <div class="card-body">
                    <h5 class="card-title">Investigation Completed List</h5>

                    <div class="table-responsive" style="min-height: 200px">

                        <table id="investigation-completed-list"
                            class="table table-responsive table-striped data_mf_table table-condensed">
                            <thead>
                                <tr>

                                    <th>Inv No</th>
                                    <th>Patient MR no.</th>
                                    <th>Patient Name</th>
                                    <th>Gender</th>
                                    <th>Investigation Category</th>
                                    <th>Request Date</th>
                                    <th>Comment</th>
                                    <th>Result Date</th>
                                    <th>Status</th>

                                    {{-- <th>Status</th> --}}
                                    <th style="width: 250px">Action</th>
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
@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-responsive/datatables.responsive.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"
        integrity="sha512-YUkaLm+KJ5lQXDBdqBqk7EVhJAdxRnVdT2vtCzwPHSweCzyMgYV/tgGF4/dCyqtCC2eCphz0lRQgatGVdfR0ww=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        // setTimeout(function() {
        //     $("#patient_id").select2();
        //     $("#procedure_type_id").select2();
        //     $("#consultant_id").select2();
        // }, 1000);
        investigation_result = $('#investigation-list').DataTable({
            processing: true,
            serverSide: true,

            pageLength: 10,
            ajax: {
                url: "{{ route('pos.investigation_result_list') }}",


            },

            columns: [
                {
                    data: 'invoice_no',
                    name: 'invoice_no',
                    searchable: true
                },


                {
                    data: null,
                    name: 'patient.name',
                    searchable: true,
                    render: function(data, type, row) {
                        if (row.patient) {
                            return row.patient.mr_no;
                        }else{
                            return ''
                        }


                    }
                },

                {
                    data: null,
                    name: 'patient.name',
                    searchable: true,
                    render: function(data, type, row) {
                        if (row.patient) {
                            return row.patient.name;
                        }else{
                            return ''
                        }


                    }
                },
                {
                    data: null,
                    name: 'patient.gender',
                    searchable: true,
                    render: function(data, type, row) {
                        if (row.patient) {
                            return row.patient.gender;
                        }else{
                            return ''
                        }


                    }
                },

                {
                    data: 'investigation.name',
                    name: 'investigation.name',
                    searchable: true
                },
                
                {
                    data: 'inv_date',
                    name: 'inv_date',
                    searchable: true
                },
                {
                    data: 'inv_out_date',
                    name: 'inv_out_date',
                    searchable: true
                },
                {
                    data: 'patient_type',
                    name: 'patient_type',
                    searchable: true
                },
                {
                    data: 'status',
                    name: 'status',
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
            // buttons: [
            //     'copy', 'csv', 'excel', 'pdf', 'print'
            // ]
        });

        investigation_completed_result = $('#investigation-completed-list').DataTable({
            processing: true,
            serverSide: true,

            pageLength: 10,
            ajax: {
                url: "{{ route('pos.investigation_completed_list') }}",


            },

            columns: [
                {
                    data: 'invoice_no',
                    name: 'invoice_no',
                    searchable: true
                },
                {
                    data: 'patient.mr_no',
                    name: 'patient.mr_no',
                    searchable: true
                },
                {
                    data: 'patient.name',
                    name: 'patient.name',
                    searchable: true
                },
                {
                    data: 'patient.gender',
                    name: 'patient.gender',
                    searchable: true
                },

                {
                    data: 'investigation.name',
                    name: 'investigation.name',
                    searchable: true
                },
                
                {
                    data: 'inv_date',
                    name: 'inv_date',
                    searchable: true
                },
                {
                    data: 'inv_comment',
                    name: 'inv_comment',
                    searchable: true
                },
                {
                    data: 'inv_out_date',
                    name: 'inv_out_date',
                    searchable: true
                },
                {
                    data: 'status',
                    name: 'status',
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
            // buttons: [
            //     'copy', 'csv', 'excel', 'pdf', 'print'
            // ]
        });






        // $("body").on("change", "#ward_id", function() {
        //     getBeds();
        // });

        // function getBeds() {
        //     let bedId = $("#ward_id").val();
        //     $.ajax({
        //         type: "post",
        //         url: "{{ route('pos.ward_bed') }}",
        //         data: {
        //             id: bedId,
        //             "_token": "{{ csrf_token() }}"
        //         },
        //         success: function(res) {
        //             if (res.status)

        //                 $("#bed_id").find("option").not(":first").remove();

        //             if (res.data.length > 0) {
        //                 $.each(res.data, function(key, bed) {
        //                     $("#bed_id").append(
        //                         `<option value="${bed.id}">${bed.name}</option>`
        //                     )
        //                 });
        //             }
        //         }
        //     });
        // }

        // function getEditBeds(ward_id, bed_id) {

        //     $.ajax({
        //         type: "post",
        //         url: "{{ route('pos.ward_bed') }}",
        //         data: {
        //             id: ward_id,
        //             "_token": "{{ csrf_token() }}"
        //         },
        //         success: function(res) {
        //             if (res.status)

        //                 $("#bed_id").find("option").not(":first").remove();

        //             if (res.data.length > 0) {
        //                 $.each(res.data, function(key, bed) {
        //                     $("#bed_id").append(
        //                         `<option value="${bed.id}">${bed.name}</option>`
        //                     )
        //                 });
        //             }

        //             setTimeout(function() {
        //                 $("#bed_id").val(bed_id);
        //             }, 1000);
        //         }
        //     });
        // }


        // $("#patient_admission").on("submit", function(e) {

        //     e.preventDefault();

        //     let isValid = true;

        //     // Clear previous error messages
        //     $(".error-message").remove();
        //     $(".is-invalid").removeClass("is-invalid");

        //     // Validate required fields
        //     $(this).find("[required]").each(function() {
        //         if (!$(this).val().trim()) {
        //             isValid = false;
        //             $(this).addClass("is-invalid"); // Highlight invalid field
        //             $(this).after(
        //                 `<span class="error-message text-danger">This field is required.</span>`
        //             ); // Show error message
        //         }
        //     });

        //     if (isValid) {

        //         $("#patient_admission").ajaxSubmit({
        //             url: '{{ route('pos.store_patient_admission') }}',
        //             type: 'post',
        //             data: {
        //                 _token: '{{ csrf_token() }}'

        //             },
        //             success: function(response) {
        //                 console.log(response);
        //                 reset_fields();
        //                 investigation_result.ajax.reload();
        //             },
        //             error: function(XMLHttpRequest, textStatus, errorThrown) {
        //                 //console.log();
        //                 //alert("Status: " + textStatus); alert("Error: " + errorThrown);
        //                 alert(JSON.parse(XMLHttpRequest.responseText).message);
        //             }
        //         });
        //     }
        // });

        // function reset_fields() {
        //     $("#id").val(0);
        //     $("#patient_id").val('');
        //     $("#bed_id").val('');
        //     $("#ward_id").val('');
        //     $("#procedure_type_id").val('').trigger("change");
        //     $("#consultant_id").val('').trigger("change");
        //     $("#admission_data").val('');
        //     $("#emergency_contact_no").val('');
        //     $("#guardian_name").val('');
        //     $("#admission_date").val('');
        // }

        // $("body").on("click", ".edit_record", function(e) {
        //     record_id = $(this).attr("data-id");
        //     var details = JSON.parse($(this).attr("data-details"));
        //     var fullDateTime = details.admission_date;
        //     let dateOnly = fullDateTime.split(' ')[0];

        //     $("#id").val(details.id);

        //     $("#admission_date").val(details.admission_date_edit);
        //     $("#emergency_contact_no").val(details.emergency_contact_no);
        //     $("#guardian_name").val(details.guardian_name);
        //     $("#procedure_type_id").val(details.procedure_type_id).trigger("change");
        //     $("#consultant_id").val(details.consultant_id).trigger("change");
        //     $("#bed_id").val(details.bed_id);
        //     $("#patient_id").val(details.patient_id).trigger('change');
        //     $("#ward_id").val(details.ward_id);
        //     $("#relation_id").val(details.relation_id);
        //     $("#admission_date").val(dateOnly);
        //     getEditBeds(details.ward_id, details.bed_id);

        // });


        $("body").on("click", ".delete_result", function(e) {
            var id = $(this).attr("data-id");

            if (confirm('Are you sure to delete this test Result ?')) {
                var  url = "{{route('pos.delete_investigation_result')}}/"+id;
                window.location = url;
                /*$.ajax({
                    type: 'post',
                    url: " //route('pos.deactivate_record') ",
                    data: {
                        id: id,
                        table: "patient_admissions",
                        _token: '{{ csrf_token() }}'

                    },
                    success: function(res) {
                        investigation_result.ajax.reload();
                        //window.location.reload();
                    }
                })*/
            } else {

            }
        });
    </script>
@endpush
