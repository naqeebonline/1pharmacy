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

                                <table id="users-list" class="table table-striped data_mf_table table-condensed" >

                                    <thead>
                                    <tr>
{{--                                        <th>#</th>--}}
                                        <th style="width: 10%">Name</th>
                                        <th  style="width: 7%">Email</th>
                                        <th style="width: 10%">Role</th>
                                        <th style="width: 10%">Username</th>
                                        <th style="width: 10%">District</th>
                                        <th style="width: 10%">Parent User</th>
                                        <th style="width: 10%">No. of Children Users</th>
                                        <th style="width: 10%">{{ config('settings.company_title') }}</th>
                                        <th style="width: 10%">{{ config('settings.section_title') }}</th>
                                        <th style="width: 10%">Roles</th>
                                        <th style="width: 10%">No. of Permissions</th>
                                        <th  style="width: 10%">Action</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @foreach($items as $item)
                                        <tr>
{{--                                            <td>{{ $loop->iteration }}</td>--}}
                                            <td><strong>{{ $item->name }}</strong></td>
                                            <td>{{ $item->email }}</td>
                                            <td>
                                                @foreach($item->roles as $key2 => $value)
                                                    <code>{{ $value->name ?? "" }}</code>
                                                @endforeach
                                            </td>
                                            <td><code>{{ $item->username }}</code></td>
                                            <td><code style="color: green">{{ $item->district->title ?? "" }}</code></td>
                                            <td>{{ $item->parent->name ?? "" }}</td>
                                            <td>{{ $item->children->count() }}</td>
                                            <td>{{ $item->company->title ?? "" }}</td>
                                            <td>{{ $item->section->title ?? "" }}</td>
                                            <td>
                                                @foreach($item->roles as $r) <span class="badge badge-info" style="color: red;">{{ $r->name }}</span> @endforeach
                                            </td>
                                            <td>{{ $item->permissions->count() }}</td>

                                            <td>
                                                <a href="{{ route('settings.users-mgt.edit', ['id' => encrypt($item->id)]) }}" class="btn btn-warning btn-icon btn-sm">
                                                    <i class="tf-icons bx bx-pencil"></i>
                                                </a>
                                                <a href="{{ route('settings.users-mgt.show', ['id' => encrypt($item->id)]) }}" class="btn btn-primary btn-icon  btn-sm">
                                                    <i class="tf-icons bx bx-wrench"></i>
                                                </a>
                                                {!! Form::open(['method' => 'delete', 'route' => ['settings.users-mgt.delete',encrypt($item->id)], 'class' => 'dropdown-item delete', 'style' => 'display:inline; padding: 0px']) !!}
                                                {!! Form::button('<i class="bx bx-trash tf-icons"></i>', array('class'=>'btn btn-danger btn-icon btn-sm', 'type'=>'submit')) !!}
                                                {!! Form::close() !!}
                                            </td>
                                        </tr>
                                    @endforeach
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
        $(document).ready(function (){
            $('#users-list').DataTable({
                lengthMenu: [100, 200, 300, 500],
                pageLength: 100,
                processing: true,
                serverSide:false

            });
        })
    </script>
@endpush