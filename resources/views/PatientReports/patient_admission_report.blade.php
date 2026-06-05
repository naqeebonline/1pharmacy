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
                    <h5 class="card-title">All Patients</h5>
                    @if(in_array(auth()->user()->roles->pluck('name')[0],["Super Admin","Sehat Card Focal Person"]))
                    <div class="row">
                         <div class="col-md-2">
                             <label>From Date</label>
                             <input type="date" class="form-control" id="from_date">
                         </div>
                         <div class="col-md-2">
                             <label>To Date</label>
                             <input type="date" class="form-control" id="to_date">
                         </div>

                        <div class="col-md-2">
                             <label>Consultant</label>
                            <select name="consultant_id"  id="consultant_id" class="form-select">
                                <option value="">View All</option>
                                @foreach ($consultant as $value)
                                    <option date_consultant_share="{{$value->share_percentage}}" value="{{ $value->id }}">{{ $value->name }}</option>
                                @endforeach
                            </select>
                         </div>

                        <div class="col-md-2">
                             <label>Procedure Type</label>
                            <select class="form-select" id="filter_by_procedure_type">
                                <option value="">View All...</option>
                                @foreach($procedure_type as $key => $value)
                                    <option value="{{$value->id}}">{{$value->name}}</option>
                                @endforeach
                            </select>
                         </div>
                         <div class="col-md-3 mt-4">
                             <div class="btn btn-primary print_all_details">Print</div>
                         </div>
                     </div>
                    @endif
                    <div class="table-responsive" style="min-height: 200px">

                        <table id="patient-list"
                            class="table table-responsive table-striped data_mf_table table-condensed">
                            <thead>
                                <tr>
                                    <th>Patient MR no.</th>
                                    <th>Patient Name</th>
                                    <th>Sehat Card Refrence No</th>

                                    <th>Guardian Name</th>
                                    <th>Consultant</th>
                                    <th>Procedure Type</th>
                                    {{--<th>Ward Name</th>
                                    <th>Bed no.</th>--}}

                                    <th>Admission Date</th>
                                    <th>Discharge Date</th>
                                    <th>Status</th>
                                    <th>Doctor Payment</th>
                                    <th>Payment Received</th>
                                    <th>Action</th>
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


    <div class="modal fade my_modal" id="patient_admission_edit_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <form class="modal-content form-submit-event" id="cancel_admission_form">
                <input type="hidden" id="cancel_admission_id" name="id" value="0">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Update Informations</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Consultant Name<span
                                        class="asterisk">*</span></label>
                            <input id="edit_admission_id" value="" type="hidden">


                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="nameBasic" class="form-label">Sehat Card Reference No<span
                                        class="asterisk">*</span></label>

                            <input type="number" class="form-control" name="edit_sc_ref_no" id="edit_sc_ref_no">
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
        user_table = '';
        $("body").on("click", ".print_all_details", function(e) {
            var from_date = $("#from_date").val();
            var to_date = $("#to_date").val();
            var procedure_type_id = $("#filter_by_procedure_type").val();
            var consultant_id = $("#consultant_id").val();
            if(from_date == ''){
                from_date = "nill";
            }

            if(to_date == ''){
                to_date = 'nill';
            }
            if(procedure_type_id == ''){
                procedure_type_id = 0;
            }
            if(consultant_id == ''){
                consultant_id = 0;
            }

            var url = "{{route('pos.print_patient_admission_report')}}/"+from_date+"/"+to_date+"/"+consultant_id+"/"+procedure_type_id;
            var newWindow = window.open(url, '_blank', 'width=1200,height=800');
            newWindow.focus();

        });

        $("body").on("click", ".edit_record", function(e) {
            record_id = $(this).attr("data-id");
            var details = JSON.parse($(this).attr("data-details"));


            $('#edit_admission_id').val(details.id);
            $('#edit_sc_ref_no').val(details.sc_ref_no);

            $('#patient_admission_edit_modal').modal("show");


            return false;
        });

        $("body").on("click","#update_admission",function (e) {

            var admission_id = $('#edit_admission_id').val();
            var sc_ref_no = $('#edit_sc_ref_no').val();
            var procedure_rate = $('#procedure_rate').val();

            /*var consultant_share = $('#edit_consultant_id').find(':selected').attr('date_consultant_share');
            var procedure_rate = $('#edit_procedure_type_id').find(':selected').attr('data-net_rate');*/

            $.ajax({
                type: "post",
                url: "{{ route('pos.update_patient_admission_info') }}",
                data: {

                    admission_id: admission_id,
                    sc_ref_no: sc_ref_no,

                    "_token": "{{ csrf_token() }}"
                },
                success: function(res) {

                    $("#edit_admission_id").val('');
                    $("#edit_sc_ref_no").val('');
                    $('#patient_admission_edit_modal').modal("hide");
                    user_table.ajax.reload();

                }
            });
        });

        setTimeout(function() {
            $("#filter_by_procedure_type").select2();
            $("#consultant_id").select2();
        }, 1000);
        $("body").on("change","#filter_by_procedure_type",function () {
            user_table.ajax.reload();
        });
        $("body").on("change","#consultant_id",function () {
            user_table.ajax.reload();
        });

        $("body").on("change", "#from_date,#to_date", function(e) {
            var from_date = $("#from_date").val();
            var to_date = $("#to_date").val();
            if (new Date(from_date) > new Date(to_date)) {
                alert("From date cannot be greater than To date.");
                return false;
            }
            if(from_date != '' && to_date != ""){
                user_table.ajax.reload();
            }


        });

        user_table = $('#patient-list').DataTable({
            processing: true,
            serverSide: true,

            pageLength: 20,
            ajax: {
                url: "{{ route('pos.list_all_patients') }}",
                data: function (d) {

                    d.from_date = $('#from_date').val();
                    d.to_date = $('#to_date').val();
                    d.procedure_type_id = $('#filter_by_procedure_type').val();
                    d.consultant_id = $('#consultant_id').val();



                }


            },

            columns: [

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
                    data: 'sc_ref_no',
                    name: 'sc_ref_no',
                    searchable: true
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
                    data: 'procedure_type.name',
                    name: 'procedure_type.name',
                    searchable: true
                },
                /*{
                    data: 'ward.name',
                    name: 'ward.name',
                    searchable: true
                },
                {
                    data: 'bed.name',
                    name: 'bed.name',
                    searchable: true
                },*/



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
                {
                    data: null, // Use `null` because we will combine two fields
                    name: 'payment_received', // Use one field for search or sorting
                    searchable: true,
                    render: function(data, type, row) {
                        if (data.payment_received == true) { // Change condition as needed
                            return "<b style='color:green'>Payment Received</b>"
                        }else{
                            return '';
                        }
                    }
                },
                {
                    data: null, // Use `null` because we will combine two fields
                    name: 'payment_to_doctor', // Use one field for search or sorting
                    searchable: true,
                    render: function(data, type, row) {
                        if (data.payment_to_doctor == true) { // Change condition as needed
                            return "<b style='color:green'>Paid</b>"
                        }else{
                            return '';
                        }
                    }
                },
                {
                    data: 'actions',
                    name: 'actions',
                    searchable: true
                },

            ],
            createdRow: function(row, data, dataIndex) {

                if (data.payment_received == true) { // Change condition as needed
                    $(row).addClass('red-row');
                }
            },

            responsive: true,


            searching: true,
            sorting: true,
            paging: true,
            dom: 'Bfrtip',
            // buttons: [
            //     'copy', 'csv', 'excel', 'pdf', 'print'
            // ]
        });






    </script>


@endpush
