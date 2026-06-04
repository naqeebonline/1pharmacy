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
                            <h5>Add Nurse Notes</h5>

                            <form action="" id="add_nurse_notes">
                                <div class="row">
                                    <div class="col-md-3">

                                        <input type="hidden"  name="id" id="id" value="0">
                                        
                                        <label for="" class="form-label">Select Patient</label>
                                        <select name="" id="admit_patient" class="form-select">
                                            <option value="" >Select Patient</option>
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
                                        <input type="date" required id="service_date" value="{{ date('Y-m-d') }}"
                                            name="service_date" class="form-control" placeholder="" autocomplete="off">
                                    </div>

                                    <div class="col-12">
                                        <label for="" class="form-label">Note</label>
                                        <textarea name="nurse_note" id="nurse_note" class="form-control" cols="30" rows="6"></textarea>
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
                                            Nursing Notes
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
                                                    <th style="width: 10%">OT Notes</th>

                                                    <th style="width: 10%">Dated</th>


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

            let patient_id = selectedOption.getAttribute("data-patient");
            let admission_id = selectedOption.getAttribute("data-admission");

            $("#patient_id").val(patient_id);
            $("#admission_id").val(admission_id);


            loadNurseNotes(patient_id, admission_id);
        });


        $("body").on("change", "#service_type_id", function(e) {
            let selectedOption = e.target.options[e.target.selectedIndex];
            let rate = selectedOption.getAttribute("data-rate");
            $("#service_rate").val(rate);
        });
        
        
        
        $("#add_nurse_notes").on("submit", function(e) {
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
                $("#save_inv_btn").prop('disabled', true).text("Saving....");
                $("#add_nurse_notes").ajaxSubmit({
                    url: '{{ route('pos.save_patient_nurse_notes') }}',
                    type: 'post',
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(response) {
                         $("#patient_ot_notes_table").DataTable().destroy();
                         let patient_id = $("#patient_id").val();
                         let admission_id = $("#admission_id").val();
                         loadNurseNotes(patient_id, admission_id);
                        // patient_ot_notes_table.ajax.reload();
                         $("#nurse_note").val("");
                         $("#save_inv_btn").prop("disabled", false);
 

                        $("#save_inv_btn").prop('disabled', false).text("Save Note");


                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        alert(JSON.parse(XMLHttpRequest.responseText).message);
                    }
                });
            }
        });

     




        function loadNurseNotes(patient_id = '', admission_id = '') {
            patient_nurse_notes_table = $('#patient_ot_notes_table').DataTable({
                processing: true,
                serverSide: true,

                lengthMenu: [
                    [100, 250, 500, 1000],
                    ['100', '250', '500', '1000']
                ],
                pageLength: 50,
                ajax: {
                    url: `{{ route('pos.get_patient_nurse_notes') }}/${patient_id}/${admission_id}`,

                    data: function(d) {
                        d.user_id = $('#attendance_user_filter').val();
                        d.attendance_date_from = $('#attendance_date_from').val();
                        d.attendance_date_to = $('#attendance_date_to').val();


                    }

                },

                columns: [

                    {
                        data: 'nurse_note',
                        name: 'nurse_note',
                        searchable: true
                    },
                    
                    {
                        data: 'created_at',
                        name: 'created_at',
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


        $("body").on("click", ".edit_service_record", function(e){
            var value = $(this).attr("data-details");
            detail = JSON.parse(value);
            $("#nurse_note").val(detail.nurse_note);
            $("#id").val(detail.id);
            
        })

       
        

         $("body").on("click", ".delete_service_record", function(e) {
            var id = $(this).attr("data-id");
            if (confirm('Are you sure to delete this record ?')) {
                $.ajax({
                    type: 'post',
                    url: "{{ route('pos.deactivate_record') }}",
                    data: {
                        id: id,
                        table: "patient_nurse_notes",
                        _token: '{{ csrf_token() }}'

                    },
                    success: function(res) {
                        patient_nurse_notes_table.ajax.reload();
                        //window.location.reload();
                    }
                })
            } else {
                alert('Why did you press cancel? You should have confirmed');
            }
        });
    

     
    </script>
@endpush
