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
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label>District</label>
                            <select class="form-control select2" id="district_id">
                                <option value="">Select District</option>
                                @foreach($districts as $key => $value)
                                    <option value="{{$value->id}}" {{(isset($_GET['district_id']) && $_GET['district_id'] == $value->id) ? "selected=selected" : ""}}>{{$value->title}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label>Role</label>
                            <select class="form-control select2" id="role_id">
                                <option value="">Select Role</option>
                                @foreach($role_data as $key => $value)
                                    <option value="{{$value->id}}" {{(isset($_GET['role_id']) && $_GET['role_id'] == $value->id) ? "selected=selected" : ""}}>{{$value->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label>Search By username</label>
                            <input type="text" class="form-control" value="{{$_GET['search_by'] ?? ""}}" name="search_by" id="search_by" placeholder="search by username...">
                        </div>

                        <div class="col-md-3">
                            <label>Search By Email</label>
                            <input type="text" class="form-control" name="email" value="{{$_GET['email'] ?? ""}}" id="email" placeholder="search by email...">
                        </div>

                        <div class="col-md-2">
                            <div class="btn btn-success" id="search_user" style="width: 100%">Search</div>
                        </div>

                        <div class="col-md-2">
                            <div class="btn btn-danger" id="show_all" style="width: 100%">Show All</div>
                        </div>



                    </div>

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
                                        <th style="width: 10%">Token</th>
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
                                            <td><code>{{ $item->password }}</code></td>
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
           {{ $items->links('pagination::bootstrap-4')}}
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
    <script>

        $("body").on("click","#search_user",function (e) {
            var district_id = $("#district_id").val();
            var search_by = $("#search_by").val();
            var email = $("#email").val();
            var role_id = $("#role_id").val();
            var url = `{{route('settings.users-mgt.list')}}?district_id=${district_id}&search_by=${search_by}&email=${email}&role_id=${role_id}`;
            window.location = url;

        });

        $("body").on("click","#show_all",function (e) {

            var url = `{{route('settings.users-mgt.list')}}`;
            window.location = url;

        });

    </script>
@endpush