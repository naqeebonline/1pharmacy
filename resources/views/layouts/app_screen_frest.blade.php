@php
    if (isset($app_id)) {
        $current_app = \Modules\Settings\Entities\MyApp::find($app_id);
    } else {
        $current_app = null;
    }

    if (!isset($user)) {
        $user = \Illuminate\Support\Facades\Auth::user();
    }
@endphp
<!DOCTYPE html>

<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed" dir="ltr" data-theme="theme-default"
    data-assets-path="{{ asset('assets/') }}" data-template="vertical-menu-template-no-customizer">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>HMIS</title>

    <meta name="description" content="" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/site-images/logos.png') }}" />

    <!-- Fonts -->
    {{--<link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />--}}
    {{--<link
        href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />--}}

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/fontawesome.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/flag-icons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-default.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/typeahead-js/typeahead.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />

    <!-- Page CSS -->

    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/my_custom.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/toastr/toastr.css') }}" />


    <link rel="stylesheet" href="{{asset('theme/plugins/leaflet/leaflet.css')}}">
    <link rel="stylesheet" href="{{asset('theme/plugins/leaflet/plugins/L.Control.Pan.css')}}">
    <link rel="stylesheet" href="{{asset('theme/plugins/leaflet/plugins/L.Control.MousePosition.css')}}">
    {{--<link rel="stylesheet" href="{{asset('theme/plugins/leaflet/plugins/leaflet-icon-pulse-master/dist/L.Icon.Pulse.css')}}">--}}
    <link rel="stylesheet" href="{{asset('theme/custom/style.css')}}">


    @stack('stylesheets')

    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('assets/js/config.js') }}"></script>
    <style>
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 0 !important;
            width: 100vw;
            height: 100vh;
            background-color: #263c55;
        }
        .menu-item > a {
            color: #FFFFFF !important;
            font-weight: bold;

        }
        .menu-item.open > a {
            color: #ffcb02 !important;
            font-weight: bold;
        }

        html:not(.layout-menu-collapsed) .bg-menu-theme .menu-inner > .menu-item.open:not(.active), .layout-menu-hover.layout-menu-collapsed .bg-menu-theme .menu-inner > .menu-item.open:not(.active) {
            background-color: #4273a7;
        }

        .bg-menu-theme .menu-inner-shadow {
             background: none;
        }
    </style>

    <script>

            function limitKeypress(event, value, maxLength) {
                if (value != undefined && value.toString().length >= maxLength) {
                    event.preventDefault();
                }
            }


        $(document).ready(function() {
            $(".select2").select2();

            /**
             * To use this function you need to assign class select2-customer to the <select> elemet
             * and also define the data-placeholder attribute
             */
            if ($('.select2-custom').html() != undefined && $('.select2-custom').length > 0) {
                $('.select2-custom').each(function(index, value) {
                    renderSelect2(value);
                })
            }


            /**
             * On multiple selection remove the ALL value
             **/
            $('.select2-custom').on('select2:select', function(e) {
                var data = e.params.data;
                var id = e.target.id
                $(this).select2('destroy');
                if (data.id == 'ALL') {
                    $('#' + id).val(null).trigger('change');
                    $('#' + id).val(data.id).trigger('change');
                } else {
                    arr = $('#' + id).val()
                    arr = $.grep(arr, function(n) {
                        return n != 'ALL';
                    });
                    $('#' + id).val(arr).trigger('change');
                }
                $(this).select2();
            });

            $(document).find('.radio-custom :checked').each(function(i, v) {
                $(v).parents('.radio-custom').addClass('active');
            })

            $(document).on('change', '[type="radio"]', function() {
                console.log($(this).parents('.radio-container').html())
                $(this).parents('.radio-container').find('.radio-custom').each(function(i, v) {
                    $(v).removeClass('active');
                })
                $(this).parents('.radio-custom').addClass('active');
            })


        })

        /**
         * Show Select option in Ajax Success;
         * param will require data in this format: <select></select>
         */
        function renderSelect2(selector) {
            placeholder = $(selector).data('placeholder');
            if (placeholder == undefined) {
                placeholder = "Select an option"
            }
            if ($(selector).attr('multiple') != undefined) {
                $(selector).select2({
                    placeholder,
                    allowHtml: true,
                    allowClear: true,
                })
            } else {
                $(selector).select2({
                    placeholder,
                    allowClear: true
                })
            }
        }

        /**
         * Reset select2 before AjaxRequest
         * in params pass identifier name like id => cc_user_id
         */
        function resetSelect2(id) {
            $("select#" + id).select2("destroy");
            $("select#" + id).css("max-height", "2.3rem");
            $("#" + id).html("<option>Loading...</option>");
        }

        $(document).ready(function() {
            $(".delete").click(function(e) {
                return confirm('Are you sure you want to delete?')
            })
        })

        $(document).ready(function() {
            $.each($(".req"), function(i, j) {
                var label = $(this).html();
                $(this).html(label + " <span class='starik' style='color:red;font-size:11px;'>*</span>");
            })

        });
    </script>

    <style>
        .content-wrapper{

            background: transparent;
            z-index: 1;
        }
        /* .content-wrapper::after{
            content:"";
            position: absolute;
            top: 0;
            left: 0;
            z-index: -2;
            background:linear-gradient(-45deg, #033775, #006ded);
            width: 100%;
            height: 200px;
        } */
        .bg-navbar-theme .navbar-nav > .nav-link, .bg-navbar-theme .navbar-nav > .nav-item > .nav-link, .bg-navbar-theme .navbar-nav > .nav > .nav-item > .nav-link{
            color:#fff !important;
        }
        .bottom-border-line{
            border-bottom: 10px solid #EC1C24 !important;
        }

        .bg-menu-theme .menu-link.active, .bg-menu-theme .menu-horizontal-prev.active, .bg-menu-theme .menu-horizontal-next.active{
            color:#2B1178 !important;
            font-weight: bold;
        }

        .bg-menu-theme .menu-item.active > .menu-link:not(.menu-toggle){
            color:#2B1178 !important;
            background: #E9E1FF !important;
        }
        .bg-menu-theme .menu-inner > .menu-item.open .menu-item.active > .menu-link::before{
            background: #2B1178 !important;
        }

        #loading-icon {
            background-color: rgba(255, 255, 255, 0.7); /* Semi-transparent background */
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }

        #screen-blocker {
            cursor: wait; /* Changes cursor to indicate a loading state */
        }

        html:not(.layout-menu-collapsed) .bg-menu-theme .menu-inner > .menu-item.open:not(.active), .layout-menu-hover.layout-menu-collapsed .bg-menu-theme .menu-inner > .menu-item.open:not(.active){
            background: #043a7a !important;
        }
        .bg-menu-theme .menu-item.active > .menu-link:not(.menu-toggle){
            background: #fff !important;
        }

        .menu-vertical .menu-item .menu-link{
            font-size: 0.85rem !important;
        }
    </style>
</head>

<body>
<div id="show_notifications">

</div>
<div id="screen-blocker" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 9998;"></div>
<div id="loading-icon" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 9999;">
    <img src="{{ asset('assets/img/loader.gif') }}" alt="Loading...">
</div>

    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->


            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme" style="background: #0250ab !important">
                <div class="app-brand demo">
                    <a href="{{ route('home') }}" class="app-brand-link">
                        <span class="app-brand-text demo menu-text fw-bold ms-2" style="color:white; text-decoration: underline; font-size: 14px;">
                            <!-- <img src="{{ asset('assets/site-images/logo.png') }}" width="150"> -->
                             {{ config('app.COMPANY_NAME') }}
                        </span>
                    </a>

                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-none">
                        <i class="bx menu-toggle-icon d-none d-xl-block fs-4 align-middle"></i>
                        <i class="bx bx-x d-block d-xl-none bx-sm align-middle"></i>
                    </a>

                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
              <i class="bx menu-toggle-icon d-none d-xl-block fs-4 align-middle"></i>
              <i class="bx bx-x d-block d-xl-none bx-sm align-middle"></i>
            </a>
                </div>

                <div class="menu-divider mt-0"></div>

                <div class="menu-inner-shadow"></div>

                <ul class="menu-inner py-1" style="background: #0250ab;">



                    @php

                        $user_role_ids = [0];
                        foreach ($user->roles as $ur) {
                            $user_role_ids[] = $ur->id;
                        }

                        // menu of this app assigned to this user and roles
                        $menu_r = \Modules\Settings\Entities\Menu::with([
                            'myPermissions' => function ($q) use ($user_role_ids, $user) {
                                $q->with([
                                    'routes' => function ($q) use ($user_role_ids, $user) {
                                        $q->where('is_default', '=', 'yes');
                                    },
                                ])
                                    ->where('show_in_menu', '=', 'yes')

                                    ->whereRaw(
                                        "id in (
                                    SELECT permission_id FROM permission_role WHERE role_id in (" .
                                            implode(',', $user_role_ids) .
                                            ")
                                )",
                                    )
                                    ->orWhereRaw(
                                        "id in (
                                    SELECT permission_id FROM permission_user WHERE user_id in (" .
                                            $user->id .
                                            ")
                                )",
                                    );
                            },
                        ])
                            ->whereHas('myPermissions', function ($q) use ($user_role_ids, $user) {
                                $q->whereRaw(
                                    "id in (
                                    SELECT permission_id FROM permission_role WHERE role_id in (" .
                                        implode(',', $user_role_ids) .
                                        ")
                                )",
                                )->orWhereRaw(
                                    "id in (
                                    SELECT permission_id FROM permission_user WHERE user_id in (" .
                                        $user->id .
                                        ")
                                )",
                                );
                            })
                            ->where('app_id', '=', $current_app->id)
                            ->orderBy("order","asc")
                            ->get();

                    @endphp


                    <li class="menu-item">
                        <a href="{{ url($current_app->route) }}" class="menu-link active">
                        <i class="menu-icon tf-icons bx bx-home-circle"></i>
                            <span data-i18n="Dashboards" style="color: #FFFFFF">Dashboard</span>
                        </a>
                    </li>





                    @foreach ($menu_r as $menu)
                        <li class="menu-item" id="men_item_{{ $menu->id }}" >
                            <a href="javascript:void(0);" class="menu-link menu-toggle">
                                <i class="menu-icon tf-icons {{ $menu->icon }}"></i>
                                <span style="">{{ $menu->title }}</span>
                            </a>
                            <ul class="menu-sub" data-submenu-title="{{ $menu->title }}">
                                @foreach ($menu->myPermissions as $mp)
                                    @foreach ($mp->routes as $mpr)
                                        @continue($mp->show_in_menu == 'no')

                                        <li class="menu-item @if (\Illuminate\Support\Facades\Route::currentRouteName() == $mpr->route) active @endif">
                                            <a href="{{ route($mpr->route) }}" style="font-size: 15px !important; font-weight: bold" class="menu-link">
                                                {{ $mpr->title }}
                                            </a>
                                            @php $assignedMenus[$mpr->route] = $mpr->title @endphp
                                        </li>

                                        @if (\Illuminate\Support\Facades\Route::currentRouteName() == $mpr->route)
                                            <script>
                                                $(document).ready(function() {
                                                    $("#men_item_{{ $menu->id }}").addClass('open');

                                                })
                                            </script>
                                        @endif
                                    @endforeach
                                @endforeach
                            </ul>
                        </li>
                    @endforeach

                    <!-- Dashboards -->
                    {{-- <li class="menu-item">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons bx bx-home-circle"></i>
                        <div data-i18n="Dashboards">Dashboards</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item">
                            <a href="index.html" class="menu-link">
                                <div data-i18n="Analytics">Analytics</div>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="dashboards-ecommerce.html" class="menu-link">
                                <div data-i18n="eCommerce">eCommerce</div>
                            </a>
                        </li>
                    </ul>
                </li> --}}

                    <!-- Components -->
                    {{-- <li class="menu-header small text-uppercase"><span class="menu-header-text">Components</span></li> --}}

                </ul>
            </aside>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->

                <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme"
                        id="layout-navbar" style="background:linear-gradient(-45deg, #033775, #006ded)">
                    <div class="container-fluid">
                        <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                            <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                                <i class="bx bx-menu bx-sm"></i>
                            </a>
                        </div>

                        <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                            <!-- Search -->
                            {{--<div class="navbar-nav align-items-center">
                                <div class="nav-item navbar-search-wrapper mb-0">
                                    <a class="nav-item nav-link search-toggler px-0" href="javascript:void(0);">
                                        <i class="bx bx-search-alt bx-sm"></i>
                                        <span class="d-none d-md-inline-block ">Search (Ctrl+/)</span>
                                    </a>
                                </div>
                            </div>--}}


                            <!-- /Search -->

                            <ul class="navbar-nav flex-row align-items-center ms-auto">
                                @if(getUserRole() == "Super Admin" || getUserRole() == "Finance" || getUserRole() == "Receiption User")
                                    <!-- <li>
                                        <a class="btn btn-sm btn-success" target="_blank" href="{{route('pos.syncData')}}" style="margin-right: 40px; color: white;font-weight: bold;">Sync Local Data</a>
                                    </li> -->
                                @endif
                                <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2">
                                    
                                    <!-- <select class="form-control form-control-sm" id="my_store_id">
                                        <option value="">Select Store...</option>
                                        @foreach(app('stores') as $key => $value)
                                            <option value="{{$value->id}}" {{(session('store_id') == $value->id) ? "selected" : ""}}>{{$value->store_name}}</option>
                                        @endforeach
                                    </select> -->
                                </li>


                                <!-- User -->


                                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                                        data-bs-toggle="dropdown">
                                        <div class="avatar avatar-online">
                                            <i class="fa fa-user fa-2x"> </i>
                                        </div>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="javascript:;">
                                                <div class="d-flex">
                                                    <div class="flex-shrink-0 me-3">
                                                        <div class="avatar avatar-online">
                                                            <div class="avatar avatar-online">
                                                                <i class="fa fa-user fa-2x" style="color:#fff !important;"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <span class="fw-semibold d-block lh-1">{{ auth()->user()->name }}</span>
                                                        <p style="font-size: 12px;margin-bottom: 0px;">Email: {{auth()->user()->email ?? ""}}</p>
                                                        <p style="font-size: 12px;margin-bottom: 0px;">District: {{auth()->user()->district->title ?? ""}}</p>
                                                        {{--                                                    <small>Admin</small> --}}
                                                    </div>
                                                </div>
                                            </a>
                                        </li>

                                        <li>
                                            <div class="dropdown-divider"></div>
                                        </li>
                                        {{--<li>
                                            <a class="dropdown-item"
                                                href="{{ route('settings.users-mgt.my-profile') }}">
                                                <i class="bx bx-user me-2"></i>
                                                <span class="align-middle">My Profile</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('settings.users-mgt.change-password') }}">
                                                <i class="bx bx-cog me-2"></i>
                                                <span class="align-middle">Change Password</span>
                                            </a>
                                        </li>--}}




                                        <li>



                                            <a class="dropdown-item" href="{{ route('logout') }}"
                                                onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                                <i class="bx bx-power-off me-2"></i>
                                                <span class="align-middle">{{ __('Logout') }}</span>
                                            </a>

                                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                class="d-none">
                                                @csrf
                                            </form>
                                        </li>
                                    </ul>
                                </li>
                                <!--/ User -->
                            </ul>

                        </div>

                        <!-- Search Small Screens -->
                        <div class="navbar-search-wrapper search-input-wrapper d-none">
                            <input type="text" class="form-control search-input container-fluid border-0"
                                placeholder="Search..." aria-label="Search..."  />
                            <i class="bx bx-x bx-sm search-toggler cursor-pointer" style="color:#fff;"></i>
                        </div>
                    </div>
                </nav>

                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->

                    <div class="container-xxl flex-grow-1 container-p-y">

                        {{--<h5 class="py-3 breadcrumb-wrapper mb-4">
                            <span class="fw-light">
                                <a href="{{ route('app.landing-screen') }}" style="color:#fff !important;"><i class="bx bx-home mr2"></i> Home</a> /
                            </span>

                            @if (isset($current_app))
                                <span class=" fw-light">
                                    <a href="{{ url($current_app->route) }}"  style="color:#fff !important;">
                                        @if (!is_null($current_app->icon))
                                            <i class="bx {{ $current_app->icon }}"></i>
                                        @endif
                                        {{ $current_app->title ?? '' }}
                                    </a> /
                                </span>
                            @endif

                            @if (isset($back_route))
                                @if (isset($back_route[2]))
                                    <span class=" fw-light"  style="color:#fff !important;">
                                        <a href="{{ $back_route[0] }}">
                                            {{ $back_route[1] }}
                                        </a> /
                                    </span>
                                @else
                                    <span class="fw-light"  style="color:#fff !important;">
                                        <a href="{{ route($back_route[0]) }}">
                                            {{ $back_route[1] }}
                                        </a> /
                                    </span>
                                @endif

                            @endif

                            <span  style="color:#fff !important;">

                            {{ $title ?? '' }}
                            </span>

                        </h5>--}}



                        <div class="row">
                            <div class="col-12">
                                @if (\Illuminate\Support\Facades\Session::has('error'))
                                    <div class="alert alert-solid-danger alert-dismissible" role="alert">
                                        <h6 class="alert-heading mb-1"><i
                                                class="bx bx-xs bx-desktop align-top me-2"></i>Error!</h6>
                                        <span>{{ \Illuminate\Support\Facades\Session::get('error') }}</span>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endif

                                @if (\Illuminate\Support\Facades\Session::has('success'))
                                    <div class="alert alert-solid-success alert-dismissible" role="alert">
                                        <h6 class="alert-heading mb-1"><i
                                                class="bx bx-xs bx-desktop align-top me-2"></i>Success!</h6>
                                        <span>{{ \Illuminate\Support\Facades\Session::get('success') }}</span>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endif
                            </div>
                        </div>


                        @yield('content')
                        <?php
                        if( $app_id==15
                            && !(\Str::contains(request()->route()->getName(), '.create'))
                            && !(\Str::contains(request()->route()->getName(), '.edit'))
                            && !(\Str::contains(request()->route()->getName(), '.form'))
                            && !(\Str::contains(request()->route()->getName(), 'display'))
                            && !(\Str::contains(request()->route()->getName(), 'track'))
                        ) { ?>
                        <div class="footer-widget" style="z-index: 100; width:100%;padding-left:95%;">

                            <div class="demo-inline-spacing page-shortcut">
                                <div class="btn-group">
                                    <button type="button"
                                        class="btn btn-danger btn-icon rounded-pill btn-lg dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bx bx-plus bx-vertical-rounded" style="font-size:100%"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" style="">
                                        @if(isset($assignedMenus, $assignedMenus['eo.rni.create']))
                                            <li><a class="dropdown-item" href="{{ route('eo.rni.create') }}">RNI</a></li>
                                        @endif
                                        @if(isset($assignedMenus, $assignedMenus['eo.e_letters.form']))
                                            <li><a class="dropdown-item" href="{{ route('eo.e_letters.form') }}">e-Letter</a></li>
                                        @endif
                                        @if(isset($assignedMenus, $assignedMenus['eo.noting.create']))
                                            <li><a class="dropdown-item" href="{{ route('eo.noting.create') }}">e-Noting</a></li>
                                        @endif
                                    </ul>
                                </div>
                            </div>


                        </div>
                        <?php } ?>

                    </div>

                    <!-- / Content -->

                    <!-- Footer -->
                    <footer class="content-footer footer bg-footer-theme bottom-border-line">

                    </footer>
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>

        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

    <script src="{{ asset('assets/vendor/libs/hammer/hammer.js') }}"></script>

    <script src="{{ asset('assets/vendor/libs/i18n/i18n.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>

    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script src="{{ asset('assets/js/vue.js') }}"></script>
    <script src="{{ asset('assets/js/laravel-file-uploader.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/toastr/toastr.js') }}"></script>


<script src="{{asset('theme/plugins/leaflet/leaflet.js')}}"></script>
<script src="{{asset('theme/plugins/leaflet/plugins/L.Control.Pan.js')}}"></script>
<script src="{{asset('theme/plugins/leaflet/plugins/L.Control.MousePosition.js')}}"></script>
<script src="{{asset('theme/plugins/leaflet/plugins/leaflet.ajax.min.js')}}"></script>
{{--<script src="{{asset('theme/plugins/leaflet/plugins/leaflet-icon-pulse-master/dist/L.Icon.Pulse.js')}}"></script>--}}



{{--
<link type="text/css" rel="stylesheet" href="https://unpkg.com/leaflet-pulse-icon@0.1.0/src/L.Icon.Pulse.css" />

<script src="https://unpkg.com/leaflet-pulse-icon@0.1.0/src/L.Icon.Pulse.js"></script>
--}}

    <script>
        if (document.getElementById('vueapp')) {
            new Vue({
                el: '#vueapp'
            })
        }
    </script>

    {{--<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.3.2/firebase.js"></script>--}}
    <script>
        base_url = "<?php echo asset(''); ?>";

        /*var firebaseConfig = {
            apiKey: "AIzaSyDdbJCRRLnBqQMvddmuR-A9h_pW7mn0X_E",
            authDomain: "metal-bus-301614.firebaseapp.com",
            projectId: "metal-bus-301614",
            storageBucket: "metal-bus-301614.appspot.com",
            messagingSenderId: "359801234829",
            appId: "1:359801234829:web:264dca09704623a19349c4",
            measurementId: "G-BTYERB0HWL"
        };
        firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();
        messaging.onMessage((payload) => {
            console.log(payload);
            var audio = new Audio(base_url+'notification.mp3');
            audio.play();
              $("#show_notifications").html(`
              <div class="bs-toast toast toast-ex animate__animated my-2 animate__tada fade show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="2000">
                <div class="toast-header bg-primary">
                  <img src="../../assets/img/avatars/1.png" class="d-block w-px-20 h-auto rounded me-2" alt="">
                  <div class="me-auto fw-semibold">${payload.notification.title}</div>
                  <small>11 mins ago</small>
                  <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                ${payload.notification.body}


                </div>

              </div>
        `);

        });*/

        $(document).ajaxStart(function() {
            $('#screen-blocker').show();
           $('#loading-icon').show();
        });


        $(document).ajaxStop(function() {
            $('#screen-blocker').hide();
            $('#loading-icon').hide();
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        //notification_count
        function update(){
            /*$.ajax({

                method: 'GET',
                success: function (response) {
                    $("#notification_count").text(response.total);

                }
            });*/
        }
        setInterval(update, 30000);
        update();

        $("body").on("change","#store_id",function () {
            var store_id = $(this).val();
            var url =  "{{route('pos.set_user_store_id')}}/"+store_id;
            window.location = url;
        });
        $("body").on("change","#my_store_id",function () {
            var store_id = $(this).val();
            var url =  "{{route('pos.set_user_store_id')}}/"+store_id;
            window.location = url;
        });


    </script>
    @stack('scripts')

    <!-- Page JS -->
</body>

</html>
