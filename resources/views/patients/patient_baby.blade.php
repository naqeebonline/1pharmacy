@extends('layouts.'.config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('stylesheets')
    <style>
        .table > :not(caption) > * > * {padding: 5px;}
    </style>

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
@endpush

@section('content')

    <div class="row">
        <div class="col-12">

            <!-- Traffic sources -->
            <div class="card">
                <div class="card-header header-elements-inline">
                    <div class="btn btn-primary add_new_record">Add New Baby</div>

                </div>

                <div class="card-body">

                    <div class="row">


                        <div class="col-12">

                            <div class="table-responsive" style="min-height: 200px">

                                <table id="users-list" class="table table-responsive table-striped data_mf_table table-condensed" >

                                    <thead>
                                    <tr>
                                        <th >Baby Name</th>
                                        <th >Mother Name</th>
                                        <th >Father Name</th>
                                        <th >CNIC</th>
                                        <th >Date of Birth</th>
                                        <th >Baby Gender</th>
                                        <th >Baby Status</th>
                                        <th  style="width: 10%">Action</th>
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
                <input type="hidden" id="admission_id" name="admission_id" value="{{$admission_id}}">
                <input type="hidden" id="patient_id" name="patient_id" value="{{$patient_id}}">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Add Baby Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Baby Name<span class="asterisk">*</span></label>
                            <input type="text" required id="baby_name" value="Baby of {{$patient->name ?? ''}}" name="baby_name" class="form-control" placeholder="" autocomplete="off">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Mother Name<span class="asterisk">*</span></label>
                            <input type="text" required id="baby_name" value="{{$patient->name ?? ''}}" name="mother_name" class="form-control" placeholder="" autocomplete="off">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Father Name<span class="asterisk">*</span></label>
                            <input type="text" required id="father_name" value="{{$patient->father_husband_name ?? ''}}" name="father_name" class="form-control" placeholder="" autocomplete="off">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Baby Gender<span class="asterisk">*</span></label>
                            <select class="form-select" required id="baby_gender" name="baby_gender">
                                <option value="">Select Baby Gender...</option>
                                <option value="Male" >Male</option>
                                <option value="Female" >Female</option>

                            </select>
                        </div>



                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Date of Birth<span class="asterisk">*</span></label>
                            <input type="datetime-local" required id="dob" name="dob" class="form-control" placeholder="" autocomplete="off">
                        </div>




                        <div class="col-md-12 col-sm-4 mb-3">
                            <label class="form-label">Father Mother Cnic (without -)<span class="asterisk">*</span></label>
                            <input type="number" required id="father_mother_cnic" value="{{$patient->cnic ?? ''}}" name="father_mother_cnic"
                                   oninput="if(this.value.length > 13) this.value = this.value.slice(0, 13);"
                                   class="form-control" placeholder="" autocomplete="off">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Baby Status<span class="asterisk">*</span></label>
                            <select class="form-select" id="baby_status" required name="baby_status">
                                <option value="">Select Baby Status...</option>
                                <option value="Alive" >Alive</option>
                                <option value="died_after_delivery" >Died After Delivery</option>
                                <option value="died_before_delivery" >Died Before Delivery</option>

                            </select>
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

        $("body").on("click",".add_new_record",function (e) {
            $("#id").val(0);

            $("#add_new_record_model").modal("show");

        });

        $("body").on("click",".edit_service_record",function (e) {
            record_id = $(this).attr("data-id");
            var details = JSON.parse($(this).attr("data-details"));

            $("#id").val(details.id);
            $("#baby_name").val(details.baby_name);
            $("#mother_name").val(details.mother_name);
            $("#father_name").val(details.father_name);
            $("#baby_gender").val(details.baby_gender);
            $("#dob").val(details.dob);
            $("#father_mother_cnic").val(details.father_mother_cnic);
            $("#baby_status").val(details.baby_status);
            $("#add_new_record_model").modal("show");
        });

        $("body").on("click","#submit_btn",function (e) {
            e.preventDefault();
            var baby_gender = $("#baby_gender").val();
            var dob = $("#dob").val();
            var father_mother_cnic = $("#father_mother_cnic").val();
            var baby_status = $("#baby_status").val();
            if(baby_gender == ''){
                alert("Select Gender");
                return false;
            }
            if(dob == ''){
                alert("Select Date of Birth");
                return false;
            }
            if(father_mother_cnic == ''){
                alert("Select Mother CNIC");
                return false;
            }
            if(baby_status == ''){
                alert("Select Baby Status");
                return false;
            }



            $("#from_submit").ajaxSubmit({
                url: '{{route('pos.save_baby')}}',
                type: 'post',
                data: {
                    _token: '{{ csrf_token() }}'

                },
                success: function(response){
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

        $(document).ready(function (){

            district_id = "";
            police_station_id = [];
            leave_request_id = '';

            user_table = $('#users-list').DataTable({
                processing: true,
                serverSide: true,

                lengthMenu: [
                    [ 100, 250, 500, 1000 ],
                    [ '100', '250', '500', '1000']
                ],
                pageLength: 50,
                ajax: {
                    url: '{{route("pos.list_patient_baby")}}/{{$patient_id}}/{{$admission_id}}',
                    data: function (d) {
                        d.user_id = $('#attendance_user_filter').val();
                        /*d.attendance_date_from = $('#attendance_date_from').val();
                        d.attendance_date_to = $('#attendance_date_to').val();*/


                    }

                },

                columns: [

                    {data: 'baby_name', name: 'baby_name',searchable: true},
                    {data: 'mother_name', name: 'mother_name',searchable: true},
                    {data: 'father_name', name: 'father_name',searchable: true},
                    {data: 'father_mother_cnic', name: 'father_mother_cnic',searchable: true},
                    {data: 'dob', name: 'dob',searchable: true},
                    {data: 'baby_gender', name: 'baby_gender',searchable: true},
                    {data: 'baby_status', name: 'baby_status',searchable: true},


                    {data: 'actions', name: 'actions', orderable: false, searchable: false}
                ],

                responsive: true,
                processing: true,
                serverSide: true,
                searching:  true,
                sorting:    true,
                paging:     true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });

            $('#attendance_user_filter, #attendance_date_from, #attendance_date_to').on('change', function(e) {
                e.preventDefault();
                user_table.ajax.reload();
            });

            $("body").on("click",".delete_record",function (e) {
                var id  = $(this).attr("data-id");
                if (confirm('Are you sure to delete this record ?')) {
                    $.ajax({
                        type: 'post',
                        url: "{{ route('pos.deactivate_record') }}",
                        data: {
                            id: id,
                            table:"patient_baby",
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

