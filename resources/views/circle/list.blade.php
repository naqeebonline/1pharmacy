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
                <div class="card-header header-elements-inline">
                    <h6 class="card-title pb-0 mb-0">{{ $title }}</h6>
                    {{--                    <div class="header-elements"> --}}
                    {{--                        <div class="form-check form-check-right form-check-switchery form-check-switchery-sm"> --}}

                    {{--                            --}}{{-- <label class="form-check-label"> --}}
                    {{--                                Live update: --}}
                    {{--                                <input type="checkbox" class="form-input-switchery" checked data-fouc> --}}
                    {{--                            </label> --}}
                    {{--                        </div> --}}
                    {{--                    </div> --}}
                </div>

                <div class="card-body">

                    <div class="row">


                        <div class="col-12">

                            <div class="table-responsive" style="min-height: 200px">

                                <table id="users-list" class="table table-striped data_mf_table table-condensed">

                                    <thead>
                                        <tr>
                                            {{--                                        <th>#</th> --}}

                                            <th style="width: 7%">Name</th>
                                            <th style="width: 10%">Latitude</th>
                                            <th style="width: 10%">Longitude</th>


                                            <th style="width: 10%">Action</th>
                                        </tr>
                                    </thead>
                                    @foreach ($data as $key => $value)
                                        <tr>
                                            <td>{{ $value->name }}</td>
                                            <td>{{ $value->lat }}</td>
                                            <td>{{ $value->lng }}</td>


                                            <td>
                                                {{-- <td> --}}

                                                {{-- <a href="{{ route('edit.hospital', $value->id) }}" style="co"
                                                    class="btn btn-primary btn-icon btn-sm">
                                                    <i class="tf-icons bx bx-pencil"></i>
                                                </a> --}}
                                            </td>
                                        </tr>
                                    @endforeach

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
    <script>
        $(document).ready(function() {
            $('#users-list').DataTable({
                lengthMenu: [100, 200, 300, 500],
                pageLength: 100,
                processing: true,
                serverSide: false

            });
        })
    </script>
@endpush
