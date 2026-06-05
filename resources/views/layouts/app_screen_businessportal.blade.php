@php
if(isset($app_id)){
$current_app = \Modules\Settings\Entities\MyApp::find($app_id);
}else{
$current_app = null;
}

if(!isset($user)) $user = \Illuminate\Support\Facades\Auth::user();
@endphp
<!DOCTYPE html>

<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed" dir="ltr" data-theme="theme-default"
    data-assets-path="{{ asset('assets/') }}" data-template="vertical-menu-template-no-customizer">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Business Portal - {{ $title ?? "" }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

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
    @stack('vendor-style')
    @stack('stylesheets')

    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('assets/js/config.js') }}"></script>

    <script>
        $(document).ready(function() {
            $(".select2").select2()
        })

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
</head>


<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->


            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo">
                    <a href="" class="app-brand-link">
                        <span class="app-brand-text demo menu-text fw-bold ms-2">
                            <img src="{{asset('assets/site-images/logo.png')}}" width="150">
                        </span>
                    </a>

                    {{--<a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
                        <i class="bx menu-toggle-icon d-none d-xl-block fs-4 align-middle"></i>
                        <i class="bx bx-x d-block d-xl-none bx-sm align-middle"></i>
                    </a>--}}
                </div>

                <div class="menu-divider mt-0"></div>

                <div class="menu-inner-shadow"></div>

                <ul class="menu-inner py-1">



                    @php

                    $user_role_ids = [0];
                    foreach($user->roles as $ur){
                    $user_role_ids[] = $ur->id;
                    }

                    // menu of this app assigned to this user and roles
                    $menu_r = \Modules\Settings\Entities\Menu::with([
                    'myPermissions' => function($q) use ($user_role_ids, $user){
                    $q->with([
                    'routes' => function($q) use ($user_role_ids, $user){
                    $q->where('is_default', '=', 'yes');
                    }
                    ])
                    ->where('show_in_menu', '=', 'yes')

                    ->whereRaw("id in (
                    SELECT permission_id FROM permission_role WHERE role_id in (".implode(",", $user_role_ids).")
                    )"
                    )->orWhereRaw("id in (
                    SELECT permission_id FROM permission_user WHERE user_id in (".$user->id.")
                    )");

                    }
                    ])->whereHas('myPermissions', function($q) use ($user_role_ids, $user){
                    $q->whereRaw("id in (
                    SELECT permission_id FROM permission_role WHERE role_id in (".implode(",", $user_role_ids).")
                    )"
                    )->orWhereRaw("id in (
                    SELECT permission_id FROM permission_user WHERE user_id in (".$user->id.")
                    )");
                    })->where('app_id', '=', $current_app->id)->orderBy('order')->get();
                    // dd($menu_r->toSql());

                    @endphp


                    {{-- <li class="menu-item">
                        <a href="{{ url($current_app->route) }}" class="menu-link active">
                            <i class="menu-icon tf-icons bx bx-home-circle"></i>
                            <span data-i18n="Dashboards">Dashboard</span>
                        </a>
                    </li> --}}



                    @foreach($menu_r as $menu)
                    {{-- @if ($menu->is_collapsible == 'no')
                    <li class="menu-item">
                        @php
                            $firstPermission = $menu->myPermissions[0] ?? false;
                        @endphp
                        @if ($firstPermission)
                        <a href="{{ route($firstPermission->routes[0]->route) }}" class="menu-link @if(\Illuminate\Support\Facades\Route::currentRouteName() == $firstPermission->routes[0]->route) active @endif">
                            <i class="menu-icon tf-icons {{ $menu->icon }}"></i>
                            <span data-i18n="{{ $firstPermission->routes[0]->title }}">{{ $firstPermission->routes[0]->title }}</span>
                        </a>
                        @endif
                    </li>
                    @else --}}
                    <li class="menu-item" id="men_item_{{ $menu->id }}">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons {{ $menu->icon }}"></i>
                            <span>{{ $menu->title }}</span>
                        </a>
                        <ul class="menu-sub" data-submenu-title="{{ $menu->title }}">
                            @foreach($menu->myPermissions as $mp)
                            @foreach($mp->routes as $mpr)

                            @continue($mp->show_in_menu == "no")
                            <li
                                class="menu-item @if(\Illuminate\Support\Facades\Route::currentRouteName() == $mpr->route) active @endif">
                                <a href="{{ route($mpr->route) }}" class="menu-link">
                                    {{ $mpr->title }}
                                </a>
                            </li>

                            @if(\Illuminate\Support\Facades\Route::currentRouteName() == $mpr->route)
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
                    {{-- @endif --}}
                    @endforeach




                </ul>
            </aside>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->

                <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme"
                    id="layout-navbar">
                    <div class="container-fluid">
                        <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                            <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                                <i class="bx bx-menu bx-sm"></i>
                            </a>
                        </div>

                        <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                            <!-- Search -->
                            <div class="navbar-nav align-items-center">
                                <div class="nav-item navbar-search-wrapper mb-0">
                                    <a class="nav-item nav-link search-toggler px-0" href="javascript:void(0);">
                                        <i class="bx bx-search-alt bx-sm"></i>
                                        <span class="d-none d-md-inline-block text-muted">Search (Ctrl+/)</span>
                                    </a>
                                </div>
                            </div>
                            <!-- /Search -->

                            <ul class="navbar-nav flex-row align-items-center ms-auto">
                                <!-- User -->
                                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                                        data-bs-toggle="dropdown">
                                        <div class="avatar avatar-online">
                                            <i class="fa fa-user fa-2x"></i>
                                        </div>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="javascript:;">
                                                <div class="d-flex">
                                                    <div class="flex-shrink-0 me-3">
                                                        <div class="avatar avatar-online">
                                                            <div class="avatar avatar-online">
                                                                <i class="fa fa-user fa-2x"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <span
                                                            class="fw-semibold d-block lh-1">{{auth()->user()->name}}</span>
                                                        {{-- <small>Admin</small>--}}
                                                    </div>
                                                </div>
                                            </a>
                                        </li>

                                        <li>
                                            <div class="dropdown-divider"></div>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{route('settings.users-mgt.my-profile')}}">
                                                <i class="bx bx-user me-2"></i>
                                                <span class="align-middle">My Profile</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{route('settings.users-mgt.change-password')}}">
                                                <i class="bx bx-cog me-2"></i>
                                                <span class="align-middle">Change Password</span>
                                            </a>
                                        </li>

                                        <li>
                                            <div class="dropdown-divider"></div>
                                        </li>


                                        <li>
                                            {{--<a class="dropdown-item" href="auth-login-cover.html" target="_blank">
                                                <i class="bx bx-power-off me-2"></i>
                                                <span class="align-middle">Log Out</span>
                                            </a>--}}


                                            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
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
                                placeholder="Search..." aria-label="Search..." />
                            <i class="bx bx-x bx-sm search-toggler cursor-pointer"></i>
                        </div>
                    </div>
                </nav>

                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->

                    <div class="container-xxl flex-grow-1 container-p-y">

                        <h5 class="py-3 breadcrumb-wrapper mb-4">
                            <span class="text-muted fw-light">
                                <a href="{{ route('app.landing-screen') }}"><i class="bx bx-home mr2"></i> Home</a> /
                            </span>

                            @if(isset($current_app))
                            <span class="text-muted fw-light">
                                <a href="{{ url($current_app->route) }}">
                                    @if(!is_null($current_app->icon))
                                    <i class="bx {{ $current_app->icon }}"></i>
                                    @endif
                                    {{ $current_app->title ?? '' }}
                                </a> /
                            </span>
                            @endif

                            @if(isset($back_route))
                            @if(isset($back_route[2]))
                            <span class="text-muted fw-light">
                                <a href="{{ $back_route[0] }}">
                                    {{ $back_route[1] }}
                                </a> /
                            </span>
                            @else
                            <span class="text-muted fw-light">
                                <a href="{{ route($back_route[0]) }}">
                                    {{ $back_route[1] }}
                                </a> /
                            </span>
                            @endif

                            @endif

                            {{ $title ?? "" }}
                        </h5>



                        <div class="row">
                            <div class="col-12">
                                @if(\Illuminate\Support\Facades\Session::has('error'))

                                <div class="alert alert-solid-danger alert-dismissible" role="alert">
                                    <h6 class="alert-heading mb-1"><i
                                            class="bx bx-xs bx-desktop align-top me-2"></i>Error!</h6>
                                    <span>{{ \Illuminate\Support\Facades\Session::get('error') }}</span>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>

                                @endif

                                @if(\Illuminate\Support\Facades\Session::has('success'))

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


                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    <footer class="content-footer footer bg-footer-theme bottom-border-line">
                        <div
                            class="container-fluid d-flex flex-wrap justify-content-center py-2 flex-md-row flex-column">
                            <div class="mb-2 mb-md-0">
                                <h3 class="text-center an-initiative-on">An Initiative of</h3>
                                <img src="{{asset('assets/site-images/logo-bottom.png')}}" width="300" />
                            </div>
                        </div>
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
    <script>
        if (document.getElementById('vueapp')) {
            new Vue({
                el: '#vueapp'
            })
        }
    </script>
    @stack('scripts')
    @stack('vendor-script')
    @stack('page-script')

    <!-- Page JS -->
</body>

</html>