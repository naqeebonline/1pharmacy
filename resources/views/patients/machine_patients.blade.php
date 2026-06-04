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
        table tr td{
            text-transform: capitalize;
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
                            <form action="" method="post" id="machine_patient_form">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="" class="form-label">Select Patient</label>
                                        <select required name="patient_id" id="patient_id" class="form-select">
                                            <option value="" disabled selected>Select Patient</option>
                                            @foreach ($patients as $item)
                                                <option value="{{ $item->id }}">
                                                    {{ $item->name . ' - ' . $item->mr_no }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="" class="form-label">Select Day</label>
                                        {{-- <input required type="date" value="{{ date("Y-m-d") }}" id="date" name="date" class="form-control"> --}}
                                        <select name="day" id="day" class="form-control">
                                            <option value="">Select Day</option>
                                            <option value="monday">Monday</option>
                                            <option value="tuesday">Tuesday</option>
                                            <option value="wednesday">Wednesday</option>
                                            <option value="thursday">Thursday</option>
                                            <option value="friday">Friday</option>
                                            <option value="saturday">Saturday</option>
                                            <option value="sunday">Sunday</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="" class="form-label">Select Shift</label>
                                        <select required name="machine_shift_id" id="machine_shift_id" class="form-control">
                                            <option value="">Select Shift</option>
                                            @foreach ($shifts as $shift)
                                                <option value="{{ $shift->id }}">{{ $shift->name }}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="" class="form-label">Select Machine | Avaliable Slots: <span style="color: red; font-weight:bold; font-size:18px;" class="my-0" id='avaliable_slot_id'></span></label>
                                        <select required name="machine_category_id" id="machine_category_id" class="form-select">
                                            <option value="">Select Machine</option>
                                            @foreach ($machine_categories as $machine_category)
                                                <option value="{{ $machine_category->id }}">{{ $machine_category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    

                                </div>

                                <div class="d-flex align-items-center">
                                    <button class="btn btn-success" id="save_button" type="submit">Save</button>
                                   
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>




            <div class="card">

                <div class="row">
                    <div class="col">

                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">

                                    </div>
                                    <div class="col-md-9">
                                        <a style="float: right" target="_blank" href="{{route('pos.getMachinePatientReport')}}" class="btn btn-danger">Print All Patients</a>
                                    </div>
                                </div>
                                <div class="table-responsive" style="min-height: 200px">

                                    <table id="machine_patient_list" style="width: 100% !important"
                                        class="table table-responsive table-striped  ">

                                        <thead>
                                            <tr>
                                                <th>Patient</th>
                                                <th>Contact #</th>
                                                <th>CNIC</th>
                                                <th>Day</th>
                                                <th>Shift</th>
                                                <th>Category</th>
                                                <th>Actions</th>



                                                {{-- <th style="width: 10%">Action</th> --}}
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
        $("body").on("change", "#day,#machine_shift_id,#machine_category_id", function(e) {

            machine_patient.ajax.reload();
        });
        setTimeout(function () {
            $("#patient_id").select2();
        },1000);
        $("body").on("change", "#machine_category_id", function(e) {
            let machine_shift_id = $("#machine_shift_id").val();
            let day = $("#day").val();
            let machine_category_id = $("#machine_category_id").val();

            if (!day) {
                alert("Please select day.");
                return
            }
            if (!machine_shift_id) {
                alert("Please select Shift.");
                return
            }

            $.ajax({
                url: "{{ route('post.get_machine_category') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    machine_shift_id: machine_shift_id,
                    day: day,
                    machine_category_id: machine_category_id
                },
                success: function(response) {
                    $("#avaliable_slot_id").html(response.avaliable_slots);
                    if (response.avaliable_slots == 0) {
                         $("#save_button").hide();
                      //  $("#machine_category_id").val("").trigger("change");
                    }else{
                        $("#save_button").show();
                    }
                }
            })



        });

        $("#machine_patient_form").on("submit", function(e) {
            e.preventDefault();
            let isValid = true;
            $(".error-message").remove();
            $(".is-invalid").removeClass("is-invalid");

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

                $("#machine_patient_form").ajaxSubmit({
                    url: '{{ route('pos.store_machine_patient') }}',
                    type: 'post',
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        $("#machine_category_id").val("").trigger("change");
                        machine_patient.ajax.reload();
                        // loadInvestigationData();
                    }
                });
            }
        });

        loadInvestigationData();

        function loadInvestigationData(patient_id, admission_id = '') {
            machine_patient = $('#machine_patient_list').DataTable({
                processing: true,
                serverSide: true,

                lengthMenu: [
                    [100, 250, 500, 1000],
                    ['100', '250', '500', '1000']
                ],
                pageLength: 50,
                ajax: {
                    url: `{{ route('pos.machine_patient_list') }}`,
                     data: function (d) {
                        d.day = $('#day').val();
                        d.machine_shift_id = $('#machine_shift_id').val();
                        d.machine_category_id = $('#machine_category_id').val();
                        /*d.attendance_date_from = $('#attendance_date_from').val();
                        d.attendance_date_to = $('#attendance_date_to').val();*/


                    }
                },

                columns: [

                    {
                        data: 'patient.name',
                        name: 'patient.name',
                        searchable: true
                    },
                    {
                        data: 'patient.contact_no',
                        name: 'patient.contact_no',
                        searchable: true
                    },
                    {
                        data: 'patient.cnic',
                        name: 'patient.cnic',
                        searchable: true
                    },
                    {
                        data: 'day',
                        name: 'day',
                        searchable: true
                    },
                    {
                        data: 'machine_shift.name',
                        name: 'machine_shift.name',
                        searchable: true
                    },
                    {
                        data: 'machine_category.name',
                        name: 'machine_category.name',
                        searchable: true
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        searchable: true
                    },
                   
                ],


                responsive: true,
                processing: true,
                serverSide: true,
                searching: true,
                sorting: true,
                paging: true,

            });
        }


        $("body").on("click", ".delete_record", function(e) {
            var id = $(this).attr("data-id");
            if (confirm('Are you sure to delete this record ?')) {
                $.ajax({
                    type: 'post',
                    url: "{{ route('pos.deactivate_record') }}",
                    data: {
                        id: id,
                        table: "machine_patients",
                        _token: '{{ csrf_token() }}'

                    },
                    success: function(res) {
                        machine_patient.ajax.reload();
                        //window.location.reload();
                    }
                })
            } 
        });
        
    </script>
@endpush
