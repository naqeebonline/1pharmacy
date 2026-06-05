@extends('layouts.'.config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('stylesheets')
    <style>
        .table > :not(caption) > * > * {padding: 5px;}
    </style>

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />

    <style>
        .custom-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px; /* Adjust margin as needed */
        }


    </style>

@endpush

@section('content')

    <div class="row">
        <div class="col-12">

            <!-- Traffic sources -->
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title pb-0 mb-0">{{ $title }}</h6>
{{--                    <div class="header-elements">--}}
{{--                        <div class="form-check form-check-right form-check-switchery form-check-switchery-sm">--}}

{{--                            --}}{{--<label class="form-check-label">--}}
{{--                                Live update:--}}
{{--                                <input type="checkbox" class="form-input-switchery" checked data-fouc>--}}
{{--                            </label>--}}
{{--                        </div>--}}
{{--                    </div>--}}
                </div>

                <div class="card-body">

                    <div class="row">


                        <div class="col-12">

                            <div class="table-responsive" style="min-height: 200px">

                                <table id="users-lists" class="table table-striped data_mf_table table-condensed" >

                                    <thead>
                                    <tr>
{{--                                        <th>#</th>--}}
                                        <th style="width: 10%">Department Name</th>
                                        <th style="width: 10%">User</th>
                                        <th style="width: 10%">To</th>
                                        <th style="width: 10%">Message</th>
                                        <th style="width: 10%">Operator</th>
                                        <th style="width: 10%">Response</th>
                                        <th style="width: 10%">Status</th>
                                        <th style="width: 10%">Date</th>

                                    </tr>
                                    </thead>

                                    <tbody>
                                    @foreach($data as $key => $value)
                                    <tr>
                                        <td>{{$value->user->userDepartment->name ?? ""}}</td>
                                        <td>{{$value->user->username ?? ""}}</td>
                                        <td>{{$value->phone_numbers ?? ""}}</td>
                                        <td>{{$value->message ?? ""}}</td>
                                        <td>{{$value->network_type ?? ""}}</td>
                                        <td>{!! $value->sms_api_response ?? ""  !!}</td>
                                        <td>{!! $value->sms_status ?? ""   !!}</td>
                                        <td>{{ date("d-m-Y h:i:s",strtotime($value->created_at)) }}</td>

                                    </tr>
                                    @endforeach



                                    </tbody>
                                </table>

                            </div>

                           {{ $data->links() }}

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
    <script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>



    <script>
        var id =0;
        $(document).ready(function (){
            user_table = $('#users-list').DataTable({
                processing: true,
                serverSide: true,
                lengthMenu: [ [100, 250, -1],[100, 250, 'All']],
                pageLength: 50,
                ajax: {
                    url: '{{route("list-sms")}}',
                    data: {
                        'post_param': '1'
                    }

                },

                columns: [
                    {data: 'phone_numbers', name: 'phone_numbers'},

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


        })
    </script>
@endpush