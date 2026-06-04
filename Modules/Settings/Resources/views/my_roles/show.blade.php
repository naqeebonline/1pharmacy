@extends('layouts.'.config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('scripts')

    <link href="{{ asset('assets/vendor/libs/fancytree/skin-win8/ui.fancytree.min.css') }}" rel="stylesheet">
    <script src="{{ asset('assets/vendor/libs/fancytree/jquery.fancytree-all-deps.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            $(".tree-checkbox-hierarchical").fancytree({
                checkbox: true,
                selectMode: 3,
                select: function (event, data) {
                    console.log(data)
                }
            });

            // Loop through all trees, get the selected nodes from each tree then
            // get the permission ids by removing extra text (perm-) and then
            // join them as string with , and put it in text field
            $('#permissions_assignment_form').submit(function (e) {
                // e.preventDefault()
                var allSelectedNodes = []
                $.each($(".tree-checkbox-hierarchical"), function(i,v){
                    var thisTreeSelectedNodes = $(v).fancytree('getTree').getSelectedNodes();
                    // console.table(thisTreeSelectedNodes)
                    var thisTreeSelKeys = $.map(thisTreeSelectedNodes, function (node) {
                        // return "[" + node.key + "]: '" + node.title + "'";
                        if(node.key.includes("perm-")){
                            return node.key.replace("perm-", "");
                        }
                    });
                    // console.log(thisTreeSelKeys)
                    allSelectedNodes = allSelectedNodes.concat(thisTreeSelKeys)
                })

                // console.log(allSelectedNodes);
                $("#checked_permissions").val(allSelectedNodes.join(","));
                // return false;

            });


        })
    </script>
@endpush


@section('content')

    <div class="row">
        <div class="col-12">

            <!-- Traffic sources -->
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title">Role Details: <strong><u>{{ $item->name }}</u></strong></h6>
                    <div class="header-elements">
                        <div class="form-check form-check-right form-check-switchery form-check-switchery-sm">

                            {{--<label class="form-check-label">
                                Live update:
                                <input type="checkbox" class="form-input-switchery" checked data-fouc>
                            </label>--}}
                        </div>
                    </div>
                </div>

                <div class="card-body">

                    <div class="row">


                        <div class="col-12">


                            <h5>Role Slug: <strong>{{ $item->slug }}</strong></h5>

                            <div>
                                <span class="badge badge-info badge-pill">Level {{ $item->level }}</span>
                                <span class="badge badge-warning badge-pill ">{{ $item->users->count() }} Users</span>
                                <span class="badge badge-primary badge-pill ">{{ $item->permissions->count() }} Permissions</span>

                            </div>
                            <p>{{ $item->description }}</p>



                            <div class="alert alert-info mt-3">
                                <i class="icon-warning mr-1"></i> <strong>ATTENTION!</strong>
                                <p>Please select permissions in each app that you want to assign to this role. After you select all permissions, make sure to click "Save Permissions" button at the bottom of the page.</p>
                            </div>

                        </div>

                    </div>

                </div>


            </div>
            <!-- /traffic sources -->

        </div>
    </div>




    {!! Form::open(['class' => 'form-horizontal', 'id' => 'permissions_assignment_form' ,'method' => 'post', 'route' => ['settings.my-roles.role-permissions-save', \Illuminate\Support\Facades\Crypt::encrypt($item->id)]]) !!}

    <div class="row">





        @php
            $assigned_perms_c = collect($item->permissions);
            $assigned_perms_ids = $assigned_perms_c->pluck('id')->toArray();
            // dd($assigned_perms_ids);
        @endphp
        <input type="hidden" value="" id="checked_permissions" name="checked_permissions"/>
        <input type="hidden" value="{{ implode(',', $assigned_perms_ids) }}" id="assigned_permissions" name="assigned_permissions">

        @foreach($trees as $tree)
        <div class="col-6 mt-4">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title"><strong><i class="{{ $tree->icon }} mr-1"></i>{{ $tree->title }} App</strong></h6>
                    <div class="header-elements">

                    </div>
                </div>

                <div class="card-body" style="max-height: 400px !important; min-height: 400px !important; overflow-y: auto;">




                    <div class="tree-checkbox-hierarchical">

                        <ul>

                            @foreach($tree->menus as $menu)
                                <li id="menu-{{ $menu->id }}" rel="{{ $menu->id }}" class=" folder expanded" >{{ $menu->title }}
                                    <ul>
                                        @foreach($menu->myPermissions as $perm)
                                            <li id="perm-{{ $perm->id }}" rel="{{ $perm->id }}" class="{{ (in_array($perm->id, $assigned_perms_ids)? 'selected': '') }}">{{ $perm->name }}</li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endforeach


                        </ul>
                    </div>


                </div>

            </div>
        </div>
        @endforeach




    </div>



    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">


                    <div class="col-12">
                        <a href="{{ route('settings.my-roles.list') }}" class="btn btn-warning">
                            <i class="bx bx-arrow-back"></i> Back to Roles
                        </a>

                        <button type="submit" class="btn btn-success">
                            <i class="bx bx-save"></i> Save Permissions
                        </button>
                    </div>


                </div>

            </div>
        </div>
    </div>


    {!! Form::close() !!}







@endsection
