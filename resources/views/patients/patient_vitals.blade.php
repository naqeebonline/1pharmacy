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

        input{
            height: 50% !important;
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
                            <h5>Add Vital </h5>

                            <form action="" id="add_patient_vitals">
                                <div class="row">
                                    <div class="col-md-3">

                                        <input type="hidden" name="id" id="id" value="0">

                                        <label for="" class="form-label">Select Patient</label>
                                        <select name="" id="admit_patient" class="form-select">
                                            <option value="" disabled selected>Select Patient</option>
                                            @foreach ($patients as $item)
                                                <option value="{{ $item->id }}" data-item='{{ json_encode($item) }}'
                                                    data-admission={{ $item->id }} data-patient={{ $item->patient_id }}>
                                                    {{ $item->patient->name . ' - ' . $item->patient->mr_no }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3 mb-3">

                                        <input type="hidden" value="0" id="patient_id" name="patient_id"
                                            class="form-control" placeholder="" autocomplete="off">

                                        <input type="hidden" value="0" id="admission_id" name="admission_id"
                                            class="form-control" placeholder="" autocomplete="off">

                                        <label for="nameBasic" class="form-label">Date<span
                                                class="asterisk">*</span></label>
                                        <input type="date" name="date" required id="date" value="{{ date('Y-m-d') }}"
                                            name="service_date" class="form-control" placeholder="" autocomplete="off">
                                    </div>

                                    <div class="col-md-3">
                                        <label for="" class="form-label">Time</label>
                                        <input type="time" class="form-control" id="time" name="time"
                                            value="{{ date('H:i') }}" placeholder="Time" autocomplete="off">
                                    </div>

                                    <div class="col-12">
                                        <div class="row">

                                            <div class="col-md-2 mb-3">

                                                <label for="nameBasic" class="form-label">rbs<span
                                                        class="asterisk">*</span></label>
                                                <input type="text" required id="rbs" value=""
                                                    name="rbs" class="form-control emp_input" placeholder=""
                                                    autocomplete="off">
                                            </div>

                                            <div class="col-md-2 mb-3">

                                                <label for="nameBasic" class="form-label">rr<span
                                                        class="asterisk">*</span></label>
                                                <input type="text" required id="r_r" value=""
                                                    name="r_r" class="form-control emp_input" placeholder=""
                                                    autocomplete="off">
                                            </div>
                                            <div class="col-md-2 mb-3">

                                                <label for="nameBasic" class="form-label">hr<span
                                                        class="asterisk">*</span></label>
                                                <input type="text" required id="hr" value=""
                                                    name="hr" class="form-control emp_input" placeholder=""
                                                    autocomplete="off">
                                            </div>
                                            <div class="col-md-2 mb-3">

                                                <label for="nameBasic" class="form-label">bp<span
                                                        class="asterisk">*</span></label>
                                                <input type="text" required id="bp" value=""
                                                    name="bp" class="form-control emp_input" placeholder=""
                                                    autocomplete="off">
                                            </div>
                                            <div class="col-md-2 mb-3">

                                                <label for="nameBasic" class="form-label">temp<span
                                                        class="asterisk">*</span></label>
                                                <input type="text" required id="temp" value=""
                                                    name="temp" class="form-control emp_input" placeholder=""
                                                    autocomplete="off">
                                            </div>
                                            <div class="col-md-2 mb-3">

                                                <label for="nameBasic" class="form-label">spo2<span
                                                        class="asterisk">*</span></label>
                                                <input type="text" required id="spo2" value=""
                                                    name="spo2" class="form-control emp_input" placeholder=""
                                                    autocomplete="off">
                                            </div>
                                            <div class="col-md-2 mb-3">

                                                <label for="nameBasic" class="form-label">remarks<span
                                                        class="asterisk">*</span></label>
                                                <input type="text" required id="remarks" value=""
                                                    name="remarks" class="form-control emp_input" placeholder=""
                                                    autocomplete="off">
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="my-3">
                                    <button class="btn btn-primary" id="save_inv_btn" type="submit">Save Note</button>
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
                                            data-bs-target="#service_charges_tab" role="tab" aria-selected="true">
                                            Patient Vitals
                                        </button>
                                    </li>



                                </ul>
                            </div>

                            <div class="tab-content">



                                <div class="tab-pane active show" id="patient_ot_notes" role="tabpanel">
                                    <div class="table-responsive" style="min-height: 200px">
                                        <table id="patient_ot_notes_table" style="width: 100% !important;"
                                            class="table table-responsive table-striped data_mf_table table-condensed table-responsive">

                                            <thead>
                                                <tr>
                                                    <th>RBS</th>
                                                    <th>RR</th>
                                                    <th>HR</th>
                                                    <th>BP</th>
                                                    <th>Temp</th>
                                                    <th>SPO2</th>
                                                    <th>Remarks</th>
                                                    <th>Actions</th>

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

    <script src="{{ asset('assets/js/jquery.form.min.js') }}"></script>

    <script>
        patient_id = 0;
        admission_id = 0;
        setTimeout(function() {

            $('#admit_patient').select2();
            $('#service_type_id').select2();

        }, 1000);


        $("body").on("change", "#admit_patient", function(e) {



            $('#patient_ot_notes_table').DataTable().destroy();
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

            $("#patient_detail").html(html);


             patient_id = selectedOption.getAttribute("data-patient");
             admission_id = selectedOption.getAttribute("data-admission");
            $("#id").val("0");
            $("#patient_id").val(patient_id);
            $("#admission_id").val(admission_id);


            
            loadVitalNotes(patient_id, admission_id);
        });

        $("#add_patient_vitals").on("submit", function(e) {
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
                $("#add_patient_vitals").ajaxSubmit({
                    url: '{{ route('pos.save_patient_vitals') }}',
                    type: 'post',
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        $("#patient_ot_notes_table").DataTable().destroy();
                        let patient_id = $("#patient_id").val();
                        let admission_id = $("#admission_id").val();
                        loadVitalNotes(patient_id, admission_id);
                        patient_ot_notes_table.ajax.reload();
                        // $(".emp_input").val("");
                        $("#id").val("0");
                        resetFields();

                        



                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        alert(JSON.parse(XMLHttpRequest.responseText).message);
                    }
                });
            }
        });


        function loadVitalNotes(patient_id = '', admission_id = '') {
            
            patient_ot_notes_table = $('#patient_ot_notes_table').DataTable({
                processing: true,
                serverSide: true,

                lengthMenu: [
                    [100, 250, 500, 1000],
                    ['100', '250', '500', '1000']
                ],
                pageLength: 50,
                ajax: {
                    url: `{{ route('pos.patient_vitals_list') }}/${patient_id}/${admission_id}`,
                    
                },

                columns: [

                    {
                        data: 'rbs',
                        name: 'rbs',
                        searchable: true
                    },

                    {
                        data: 'r_r',
                        name: 'r_r',
                        searchable: true
                    },
                    {
                        data: 'hr',
                        name: 'hr',
                        searchable: true
                    },
                    {
                        data: 'bp',
                        name: 'bp',
                        searchable: true
                    },
                    {
                        data: 'temp',
                        name: 'temp',
                        searchable: true
                    },
                    {
                        data: 'spo2',
                        name: 'spo2',
                        searchable: true
                    },
                    {
                        data: 'remarks',
                        name: 'remarks',
                        searchable: true
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    },
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


        $("body").on("click", ".edit_service_record", function(e) {
            var value = $(this).attr("data-details");
            detail = JSON.parse(value);
            $("#admission_id").val(detail.admission_id);
            $("#patient_id").val(detail.admission_id);

            $("#bp").val(detail.bp);
            $("#hr").val(detail.hr);
            $("#r_r").val(detail.r_r);
            $("#spo2").val(detail.spo2);
            $("#rbs").val(detail.rbs);
            $("#temp").val(detail.temp);
            $("#date").val(detail.my_date);
            $("#time").val(detail.time);
            $("#remarks").val(detail.remarks);
            
            $("#id").val(detail.id);
            $("html, body").animate({
                scrollTop: 0
            }, 0);

        })




        $("body").on("click", ".delete_service_record", function(e) {
            var id = $(this).attr("data-id");
            if (confirm('Are you sure to delete this record ?')) {
                $.ajax({
                    type: 'post',
                    url: "{{ route('pos.deactivate_record') }}",
                    data: {
                        id: id,
                        table: "patient_vitals",
                        _token: '{{ csrf_token() }}'

                    },
                    success: function(res) {
                        patient_ot_notes_table.ajax.reload();
                        //window.location.reload();
                    }
                })
            } else {
                alert('Why did you press cancel? You should have confirmed');
            }
        });

        function resetFields() {
            $("#bp").val('');
            $("#hr").val('');
            $("#r_r").val('');
            $("#spo2").val('');
            $("#rbs").val('');
            $("#temp").val('');
            $("#date").val('');
            $("#time").val('');
            $("#remarks").val('');

            $("#id").val(0);
        }
    </script>
@endpush
