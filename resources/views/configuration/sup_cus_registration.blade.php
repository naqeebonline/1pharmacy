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
                    <div class="btn btn-primary add_new_record">Add New Customer/Supplier</div>

                </div>

                <div class="card-body">


                    <div class="row">


                        <div class="col-12">

                            <div class="table-responsive" style="min-height: 200px">

                                <table id="users-list" class="table table-responsive table-striped data_mf_table table-condensed" >

                                    <thead>
                                    <tr>
                                        <th >Name</th>
                                        <th >Contact #</th>
                                        <th >Address</th>
                                        <th >Email</th>
                                        <th >Type</th>
                                        <th >Market Name</th>

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
            <form class="modal-content form-submit-event" id="from_submit">
                <input type="hidden" id="id" name="id" value="0">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Add New Customer or Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <!-- Name -->
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Name<span class="asterisk">*</span></label>
                            <input type="text" id="Name" name="Name" class="form-control" required data-required-msg="Name is Required" placeholder="Enter name" autocomplete="off">
                        </div>

                        <!-- Contact No -->
                        <div class="col-md-6 mb-3">
                            <label for="contactNo" class="form-label">Contact No</label>
                            <input type="text" id="ContactNo" name="ContactNo" class="form-control" placeholder="Enter contact number" autocomplete="off">
                        </div>

                        <!-- Address -->
                        <div class="col-md-6 mb-3">
                            <label for="address" class="form-label">Address<span class="asterisk">*</span></label>
                            <input type="text" id="Address" name="Address" class="form-control" required data-required-msg="Address is Required" placeholder="Enter address" autocomplete="off">
                        </div>

                        <!-- Business Address -->
                        <div class="col-md-6 mb-3">
                            <label for="BusinessAddress" class="form-label">Business Address</label>
                            <input type="text" id="BusinessAddress" name="BusinessAddress" class="form-control" placeholder="Enter business address" autocomplete="off">
                        </div>

                        <!-- Email -->
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="Email" name="Email" class="form-control" placeholder="Enter email" autocomplete="off">
                        </div>

                        <!-- Type Dropdown -->
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">Type<span class="asterisk">*</span></label>
                            <select id="Type" name="Type" class="form-control" required data-required-msg="Type is Required">
                                <option value="" disabled selected>Select Type...</option>
                                <option value="1">Supplier</option>
                                <option value="2">Customer</option>

                            </select>
                        </div>

                        <!-- Market Dropdown -->
                        <div class="col-md-6 mb-3">
                            <label for="market_id" class="form-label">Market<span class="asterisk">*</span></label>
                            <select id="market_id" name="market_id" class="form-control" required data-required-msg="Select Market">
                                <option value="" disabled selected>Select Market...</option>
                                @foreach($market as $key => $value):
                                    <option value="{{$value->id}}">{{$value->name}}</option>
                                @endforeach

                            </select>
                        </div>

                        <!-- Opening Balance -->
                        <div class="col-md-6 mb-3">
                            <label for="openingBalance" class="form-label">Opening Balance</label>
                            <input type="number" id="OpeningBalance" name="OpeningBalance" class="form-control" placeholder="Enter opening balance" min="0" step="0.01">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
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
            resetForm();
            $("#add_new_record_model").modal("show");

        });

        $("body").on("click",".edit_record",function (e) {
            record_id = $(this).attr("data-id");
            var details = JSON.parse($(this).attr("data-details"));

            $("#id").val(record_id);
            $("#Name").val(details.Name);
            $("#ContactNo").val(details.ContactNo);
            $("#BusinessAddress").val(details.BusinessAddress);
            $("#Email").val(details.Email);
            $("#Type").val(details.Type);
            $("#market_id").val(details.market_id);
            $("#add_new_record_model").modal("show");
        });

        $("body").on("click","#submit_btn",function (e) {
            e.preventDefault();

            $("#from_submit").ajaxSubmit({
                url: '{{route('pos.save_customer')}}',
                type: 'post',
                data: {
                    _token: '{{ csrf_token() }}'

                },
                success: function(response){
                    $("#add_new_record_model").modal("hide");
                    resetForm();
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
                    url: '{{route("pos.list_customer")}}',
                    data: function (d) {
                        d.user_id = $('#attendance_user_filter').val();
                        d.attendance_date_from = $('#attendance_date_from').val();
                        d.attendance_date_to = $('#attendance_date_to').val();


                    }

                },

                columns: [

                    {data: 'Name', name: 'Name',searchable: true},
                    {data: 'ContactNo', name: 'ContactNo',searchable: true},
                    {data: 'Address', name: 'Address',searchable: true},
                    {data: 'Email', name: 'Email',searchable: true},
                    {data: 'customType', name: 'customType',searchable: true},
                    {data: 'market.name', name: 'market.name',searchable: true},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
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
                        url: "{{ route('pos.delete-table-data') }}",
                        data: {
                            id: id,
                            table:"products",
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

        function resetForm() {
            $("#id").val(0);
            $("#Name").val('');
            $("#ContactNo").val('');
            $("#BusinessAddress").val('');
            $("#Email").val('');
            $("#Type").val('');
            $("#market_id").val('');
            $("#add_new_record_model").modal("show");
        }




    </script>
@endpush