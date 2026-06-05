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


                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <a class="btn btn-primary" href="{{route('pos.print_pharmacy_audit_form')}}">Start Audit</a>
                        </div>

                        <div class="col-md-4">
                            <select class="form-control" name="store_id" id="store_id">
                                <option value="">Please select Store....</option>
                                @foreach($store as $key => $value)
                                    <option  value="{{$value->id}}" {{session('store_id') == $value->id ? "selected" : ''}}>{{$value->store_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">


                        <div class="col-12">

                            <div class="table-responsive" style="min-height: 200px">

                                <table id="users-list" class="table table-responsive table-striped data_mf_table table-condensed" >

                                    <thead>
                                    <tr>
                                        <th >Name</th>
                                        <th >Purchase Price</th>
                                        <th >Avaliable Quantity</th>
                                        <th >Total Amount</th>
                                        <th >Action</th>
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
                    url: '{{route("pos.get_ware_house_stock")}}',
                    data: function (d) {
                        d.store_id = "{{$selected_store_id}}";
                        d.user_id = $('#attendance_user_filter').val();
                        d.attendance_date_from = $('#attendance_date_from').val();
                        d.attendance_date_to = $('#attendance_date_to').val();


                    }

                },

                columns: [

                    {data: 'ProductName', name: 'ProductName',searchable: true},
                    {data: 'PurchasePrice', name: 'PurchasePrice',searchable: true},
                    {data: 'AvailableQuantity', name: 'AvailableQuantity',searchable: true},
                    {data: 'TotalProductAmount', name: 'TotalProductAmount',searchable: true},
                    {data: 'action', name: 'action',searchable: false},

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


        });






    </script>
@endpush