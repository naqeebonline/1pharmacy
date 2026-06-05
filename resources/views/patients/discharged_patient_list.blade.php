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
                    <h5 class="card-title">Discharged Patients List</h5>
                    @if(in_array(auth()->user()->roles->pluck('name')[0],["Super Admin","Sehat Card Focal Person"]))
                    <div class="row">
                         <div class="col-md-3">
                             <label>From Date</label>
                             <input type="date" class="form-control" id="from_date">
                         </div>
                         <div class="col-md-3">
                             <label>To Date</label>
                             <input type="date" class="form-control" id="to_date">
                         </div>

                        <div class="col-md-3">
                             <label>Procedure Type</label>
                            <select class="form-select" id="filter_by_procedure_type">
                                <option value="">Please select option...</option>
                                @foreach($procedure_type as $key => $value)
                                    <option value="{{$value->id}}">{{$value->name}}</option>
                                @endforeach
                            </select>
                         </div>

                        <div class="col-md-3">
                             <label>Consultant</label>
                            <select class="form-select" id="filter_by_consultant_id">
                                <option value="">Please Consultant...</option>
                                @foreach($consultant as $key => $value)
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
                            class="table table-responsive  data_mf_table table-condensed">
                            <thead>
                                <tr>
                                    <th>Patient MR no.</th>
                                    <th>Patient Name</th>
                                    <th>Sehat Card Refrence No</th>

                                    <th>Guardian Name</th>
                                    <th>Consultant</th>
                                    <th>Sub Consultant</th>
                                    <th>Procedure Type</th>
                                    {{--<th>Ward Name</th>
                                    <th>Bed no.</th>--}}

                                    <th>Admission Date</th>
                                    <th>Discharge Date</th>
                                    <th>Status</th>
                                    <th>Balance</th>
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
                    <h5 class="modal-title" id="exampleModalLabel1">Cancel Admission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">

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
                            <label for="nameBasic" class="form-label">Procedure Type<span
                                        class="asterisk">*</span></label>

                            <select id="edit_procedure_type_id" class="form-select">
                                <option value="">Show All...</option>
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
            if(from_date == '' || to_date == ''){
                alert("Please select Date");
                return false;
            }
            var procedure_type_id = $("#filter_by_procedure_type").val();
            if(procedure_type_id == '')
                procedure_type_id = 0;
            var consultant_id = $("#filter_by_consultant_id").val();
            var url = "{{route('pos.totalCases')}}/"+from_date+"/"+to_date+"/"+procedure_type_id+"/"+consultant_id;
            var newWindow = window.open(url, '_blank', 'width=1200,height=800');
            newWindow.focus();

        });

        setTimeout(function() {
            $("#filter_by_procedure_type").select2();
            $("#filter_by_consultant_id").select2();
        }, 1000);
        $("body").on("change","#filter_by_procedure_type",function () {
            user_table.ajax.reload();
        });

        $("body").on("change","#filter_by_consultant_id",function () {
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

            pageLength: 50,
            ajax: {
                url: "{{ route('pos.discharged_patient_list') }}",
                data: function (d) {

                    d.from_date = $('#from_date').val();
                    d.to_date = $('#to_date').val();
                    d.procedure_type_id = $('#filter_by_procedure_type').val();
                    d.consultant_id = $('#filter_by_consultant_id').val();



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
                    data: null,
                    name: 'balance',
                    searchable: true,
                    render: function(data, type, row) {
                        return `${row.balance.toFixed(2)}`;


                    }
                },
                {
                    data: 'actions',
                    name: 'actions',
                    searchable: true
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
            dom: 'Bfrtip',
            // buttons: [
            //     'copy', 'csv', 'excel', 'pdf', 'print'
            // ]
        });


        $("body").on("click", ".edit_record", function(e) {
            record_id = $(this).attr("data-id");
            var details = JSON.parse($(this).attr("data-details"));
            var fullDateTime = details.admission_date;
            let dateOnly = fullDateTime.split(' ')[0];

            $('#edit_admission_id').val(details.id);
            $('#edit_consultant_id').val(details.consultant_id).trigger('change');
            $('#edit_procedure_type_id').val(details.procedure_type_id).trigger('change');
            $('#patient_admission_edit_modal').modal("show");


            return false;
        });

        $("body").on("click","#update_admission",function (e) {
            var consultant_id =  $("#edit_consultant_id").val();
            var procedure_type_id =  $("#edit_procedure_type_id").val();
            var admission_id = $('#edit_admission_id').val();

            var consultant_share = $('#edit_consultant_id').find(':selected').attr('date_consultant_share');
            var procedure_rate = $('#edit_procedure_type_id').find(':selected').attr('data-net_rate');

            $.ajax({
                type: "post",
                url: "{{ route('pos.update_patient_admission') }}",
                data: {
                    consultant_id: consultant_id,
                    procedure_type_id: procedure_type_id,
                    admission_id: admission_id,
                    consultant_share: consultant_share,
                    procedure_rate: procedure_rate,
                    "_token": "{{ csrf_token() }}"
                },
                success: function(res) {
                    $("#edit_consultant_id").val('').trigger("change");
                    $("#procedure_type_id").val('').trigger("change");
                    $("#edit_admission_id").val('');
                    $('#patient_admission_edit_modal').modal("hide");
                    user_table.ajax.reload();

                }
            });
        });

    </script>
@endpush
