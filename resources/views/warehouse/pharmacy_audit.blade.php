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

            <!-- Traffic sources -->
            <div class="card">
                <div class="card-body">
                    <form class=" form-submit-event" id="patient_register">
                        <div class="row">
                            <input type="hidden" required id="id" name="id" value="0"
                                   class="form-control" />



                            <div class="col-md-2 col-sm-4 mb-3">
                                <label for="nameBasic" class="form-label">Audit By<span class="asterisk">*</span></label>

                                <select name="audit_by" required id="audit_by" class="form-select">
                                    <option value="">Select User..</option>
                                    @foreach ($users as $key => $value)
                                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                                    @endforeach
                                </select>


                            </div>


                            <div class="col-md-2 col-sm-4 mb-3">
                                <label for="nameBasic" class="form-label">Audit Date<span
                                            class="asterisk">*</span></label>
                                <input type="date" required id="dated" value="{{ date('Y-m-d') }}" name="dated"
                                       class="form-control" placeholder="" autocomplete="off">
                            </div>
                            @if(!$is_active_audit)
                            <div class="text-left mt-3">
                                <button class="btn btn-success" id="submit_btn">Start New Audit</button>
                            </div>
                            @endif

                        </div>
                    </form>
                </div>
            </div>


            {{-- LISTIN PATIENTS --}}
            <div class="card my-5">
                <div class="card-body">
                    <h5 class="card-title">List of Patients</h5>

                    <div class="table-responsive" style="min-height: 200px">

                        <table id="patient-list" class="table table-responsive table-striped  table-condensed">
                            <thead>
                            <tr>
                                <th width="15%">Audit ID</th>

                                <th width="15%">Audit By</th>
                                <th width="15%">Dated</th>
                                <th>Approve Date</th>
                                <th style="width: 30%">Action</th>
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


    <div class="modal fade" id="add_new_record_model" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form class="modal-content form-submit-event" id="from_submit">
                <input type="hidden" id="id" name="id" value="0">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Patients</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <table class="table table-bordered">
                            <tr>
                                <th>Serial</th>
                                <th>Name</th>
                                <th>Father Name</th>
                                <th></th>
                            </tr>
                            <tbody id="prev_patients">

                            </tbody>
                        </table>

                    </div>




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
            $("#district_id").select2();
            $("#location_id").select2();
        }, 1000);

        function calculateDOB(years, months, days) {
            // Get the current date
            const currentDate = new Date();

            // Create a new date object for the calculation
            const dob = new Date(currentDate);

            // Subtract years, months, and days
            dob.setFullYear(dob.getFullYear() - years); // Subtract years
            dob.setMonth(dob.getMonth() - months);      // Subtract months
            dob.setDate(dob.getDate() - days);          // Subtract days


            const date = new Date(dob);

            // Format the date as d-m-Y
            const day = String(date.getDate()).padStart(2, '0'); // Get day and add leading zero
            const month = String(date.getMonth() + 1).padStart(2, '0'); // Get month (0-indexed, so add 1) and add leading zero
            const year = date.getFullYear(); // Get the full year

            // Combine into the desired format
            var formattedDate = `${year}-${month}-${day}`;

            return formattedDate;
        }

        function calculateAgeDetails(birthDate) {
            const currentDate = new Date(); // Current date
            const birth = new Date(birthDate); // Convert input to Date object

            // Calculate the differences
            let years = currentDate.getFullYear() - birth.getFullYear();
            let months = currentDate.getMonth() - birth.getMonth();
            let days = currentDate.getDate() - birth.getDate();
            if (days < 0) {
                months -= 1;
                const prevMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 0); // Last day of the previous month
                days += prevMonth.getDate(); // Add days from the previous month
            }
            if (months < 0) {
                years -= 1;
                months += 12;
            }
            $("#age").val(years);
            $("#months").val(months);
            $("#days").val(days);
        }




        $("body").on("keyup", "#days,#months,#age", function() {
            var year = $("#age").val();
            var months = $("#months").val();
            var days = $("#days").val();
            if(year == ''){
                year = 0;
            }
            if(months == ''){
                months = 0;
            }
            if(days == ''){
                days = 0;
            }

            var dob = calculateDOB(year,months,days);
            $("#dob").val(dob);
        });



        $("body").on("change", "#dob", function() {
            calculateAgeDetails($(this).val());
        });

        $("body").on("blur", "#cnic", function() {
            var cnic = $("#cnic").val();
            var contact_no = $("#contact_no").val();
            var id = $("#id").val();
            if ((cnic.length > 10 || contact_no.length > 8) && id == 0 ) {
                $.ajax({
                    type: 'post',
                    url: "{{ route('pos.get_patient_by_cnic') }}",
                    data: {
                        cnic: cnic,
                        contact_no: contact_no,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        // console.log(res);
                        if (res.status) {

                            // $("#submit_btn").hide();
                            $("#prev_patients").html('');
                            registered_patients = res.data;
                            if(registered_patients.length > 0){
                                $("#submit_btn").hide();
                                alert("Patient Already Registered");
                                window.location = "{{route('pos.patient_admission')}}";
                                $.each(registered_patients, function(index, value) {
                                    var html = `<tr>
                                            <td>${index + 1}</td>
                                            <td>${value.name}</td>
                                            <td>${value.father_husband_name}</td>
                                            <td><div class="btn btn-success select_patient" data-id='${index}' >Select</div></td>

                                            `;
                                    $("#prev_patients").append(html);

                                });
                                $("#add_new_record_model").modal("show");
                            }
                        }else{
                            registered_patients = [];
                        }
                    }
                });

            }
        });


        $("body").on("click",".select_patient",function () {
            var index = $(this).attr("data-id");
            var details = registered_patients[index];

            $("#id").val(details.id);
            $("#cnic").val(details.cnic);
            $("#name").val(details.name);
            $("#contact_no").val(details.contact_no);
            $("#district_id").val(details.district_id).trigger('change');
            $("#location_id").val(details.location_id).trigger('change');
            $("#dob").val(details.dob);
            $("#father_husband_name").val(details.father_husband_name);
            $("#g4no").val(details.g4no);
            $("#age").val(details.age);
            $("#gender").val(details.gender);
            //$("#regdate").val(details.formatted_date);
            registered_patients = [];
            $("#add_new_record_model").modal("hide");

        });




        user_table = $('#patient-list').DataTable({
            processing: true,
            serverSide: true,

            pageLength: 10,
            ajax: {
                url: "{{ route('pos.list_audit') }}",
                // data: function (d) {
                //     d.user_id = $('#attendance_user_filter').val();
                //     d.attendance_date_from = $('#attendance_date_from').val();
                //     d.attendance_date_to = $('#attendance_date_to').val();


                // }

            },

            columns: [

                {
                    data: 'audit_no',
                    name: 'audit_no',
                    searchable: true
                },



                {
                    data: 'users.name',
                    name: 'users.name',
                    searchable: true
                },
                {
                    data: 'dated',
                    name: 'dated',
                    searchable: true
                },

                {
                    data: 'aprove_date',
                    name: 'aprove_date',
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


        $("body").on("click", ".edit_record", function(e) {
            record_id = $(this).attr("data-id");
            var details = JSON.parse($(this).attr("data-details"));

            $("#id").val(record_id);


            $("#contact_no").val(details.contact_no);
            $("#district_id").val(details.district_id).trigger('change');
            $("#dob").val(details.dob);
            $("#father_husband_name").val(details.father_husband_name);
            $("#g4no").val(details.g4no);
            $("#age").val(details.age);
            $("#months").val(details.months);
            $("#days").val(details.days);
            $("#gender").val(details.gender);
            $("#id").val(details.id);
            $("#location_id").val(details.location_id).trigger('change');
            // $("#mr_no").val(details.mr_no);
            $("#name").val(details.name);
            $("#cnic").val(details.cnic);
            $("#regdate").val(details.formatted_date);
            // $("#relation_id").val(details.relation_id);
            $("#sc_ref_no").val(details.sc_ref_no);
        });


        //$("body").on("click", "#submit_btn", function(e) {
        $("#patient_register").on("submit", function(e) {

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

                $("#patient_register").ajaxSubmit({
                    url: '{{ route('pos.store_audit') }}',
                    type: 'post',
                    data: {
                        _token: '{{ csrf_token() }}'

                    },
                    success: function(response) {
                        console.log(response);
                        reset_fields();
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

        $("body").on("click", ".delete_record", function(e) {
            var id = $(this).attr("data-id");
            if (confirm('Are you sure to delete this record ?')) {
                $.ajax({
                    type: 'post',
                    url: "{{ route('pos.deactivate_record') }}",
                    data: {
                        id: id,
                        table: "patients",
                        _token: '{{ csrf_token() }}'

                    },
                    success: function(res) {
                        // user_table.dataTable.reload();
                        window.location.reload();
                    }
                })
            } else {
                alert('Why did you press cancel? You should have confirmed');
            }
        });

        function reset_fields() {
            $("#id").val(0);
            $("#contact_no").val('');
            $("#district_id").val('');
            $("#dob").val('');
            $("#father_husband_name").val('');
            $("#g4no").val(0);
            $("#age").val('');
            $("#months").val('');
            $("#days").val('');
            $("#gender").val('');
            $("#id").val('');
            $("#location").val('');
            $("#cnic").val('');
            $("#cnic").val('');
            $("#name").val('');
            $("#regdate").val('');
            //$("#relation_id").val('');
            $("#sc_ref_no").val('');
        }
    </script>
@endpush
