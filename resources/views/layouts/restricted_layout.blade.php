@php
    if (!isset($user)) {
        $user = \Illuminate\Support\Facades\Auth::user();
    }
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ env('APP_NAME') }} {{ $title ?? '' }}</title>

    <!-- Global stylesheets -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/icons/icomoon/styles.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/icons/icomoon/styles.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/bootstrap_limitless.min.css') }}" rel="stylesheet" type="text/css">

    <link href="{{ asset('assets/css/bootstrap_limitless.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/layout.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/components.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/colors.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/my_custom.css') }}" rel="stylesheet" type="text/css">
    <!-- /global stylesheets -->

    <!-- Core JS files -->
    <script src="{{ asset('assets/js/main/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/main/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/loaders/blockui.min.js') }}"></script>
    <!-- /core JS files -->

    <script>
        $(document).ready(function() {
            $(document).ready(function() {
                $.each($(".req"), function(i, j) {
                    var label = $(this).html();
                    $(this).html(label +
                        " <span class='starik' style='color:red;font-size:11px;'>*</span>");
                })

            });
        })
    </script>
    <style>
        .landing-app-box {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <!-- Main navbar -->
    <div class="navbar navbar-expand-md navbar-dark" style="background-color: #{{ env('GREEN') }}">
        <div class="navbar-brand wmin-200" style="padding: 0px;margin: 0px;">
            <a href="/" class="d-inline-block" style="font-size: 26px; color: #fff; padding-top: 9px;">
                {{-- <img src="{{ asset('assets/images/logo_light.png') }}" alt=""> --}}
                <img src="{{ asset('assets/images/logo_white.svg') }}" alt=""
                    style="width: 70px; height: auto; display: inline-block">
                {{ env('APP_ABBR') }}
            </a>
        </div>

        <div class="d-md-none">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-mobile">
                {{-- <i class="icon-tree5"></i> --}}
                <i class="icon-user"></i>
                <i class="icon-chevron-down"></i>
            </button>

            {{-- <button class="navbar-toggler sidebar-mobile-main-toggle" type="button">
            <i class="icon-paragraph-justify3"></i>
        </button> --}}

        </div>

        <div class="collapse navbar-collapse" id="navbar-mobile">
            <ul class="navbar-nav">
                <li class="nav-item">
                    {{-- <a href="#" class="navbar-nav-link sidebar-control sidebar-main-toggle d-none d-md-block">
                    <i class="icon-paragraph-justify3"></i>
                </a> --}}
                </li>


            </ul>

            <span class="b-adge b-g-success-400 ml-md-auto mr-md-3">{{-- Active --}}</span>

            <ul class="navbar-nav">


                <li class="nav-item dropdown dropdown-user">
                    <a href="#" class="navbar-nav-link d-flex align-items-center dropdown-toggle"
                        data-toggle="dropdown">
                        <img src="{{ asset('assets/images/placeholders/admin2.jpeg') }}" class="rounded-circle mr-2"
                            height="34" alt="">
                        <span>{{ $user->name }}</span>
                    </a>

                    <div class="dropdown-menu dropdown-menu-right">
                        <a href="{{ route('settings.users-mgt.my-profile') }}" class="dropdown-item"><i
                                class="icon-user-plus"></i> My profile</a>
                        {{-- <a href="#" class="dropdown-item"><i class="icon-coins"></i> My balance</a>
                    <a href="#" class="dropdown-item"><i class="icon-comment-discussion"></i> Messages <span
                            class="badge badge-pill bg-blue ml-auto">58</span></a>
                    <div class="dropdown-divider"></div> --}}
                        <a href="{{ route('settings.users-mgt.change-password') }}" class="dropdown-item"><i
                                class="icon-cog5"></i> Change Password</a>

                        {{-- <a href="#" class="dropdown-item"><i class="icon-switch2"></i> Logout</a> --}}
                        <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                            <i class="icon-switch2"></i> {{ __('Logout') }}
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>

                    </div>
                </li>
            </ul>
        </div>
    </div>
    <!-- /main navbar -->






    <!-- Page header -->
    <div class="page-header">
        <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
            <div class="d-flex">

                <div class="breadcrumb">
                    <a href="{{ route('app.landing-screen') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i>
                        Home</a>


                    <span class="breadcrumb-item active">{{ $title ?? '' }}</span>

                </div>

                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>

            <div class="header-elements d-none">

            </div>
        </div>

        <div class="page-header-content header-elements-md-inline">
            <div class="page-title d-flex" style="padding: 1rem 0;">
                <h4>
                    <a href="{{ route('app.landing-screen') }}">
                        <i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Home</span>
                    </a> -


                    {{ $title ?? '' }}
                </h4>
                <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
            </div>


            @include('layouts._partials.page_actions', ['back_route' => $back_route ?? null])


        </div>
    </div>
    <!-- /page header -->




    <!-- Page content -->
    <div class="page-content -pt-0">

        <!-- Main content -->
        <div class="content-wrapper">

            <!-- Content area -->
            <div class="content p-2">



                <div class="row">
                    <div class="col-12">
                        @if (\Illuminate\Support\Facades\Session::has('error'))
                            <div class="alert alert-danger alert-styled-left alert-dismissible">
                                <button type="button" class="close"
                                    data-dismiss="alert"><span>&times;</span></button>
                                <span class="font-weight-semibold">Error! </span>
                                {{ \Illuminate\Support\Facades\Session::get('error') }}
                                .
                            </div>
                        @endif

                        @if (\Illuminate\Support\Facades\Session::has('success'))
                            <div class="alert alert-success alert-styled-left alert-dismissible">
                                <button type="button" class="close"
                                    data-dismiss="alert"><span>&times;</span></button>
                                <span class="font-weight-semibold">Success! </span>
                                {{ \Illuminate\Support\Facades\Session::get('success') }}
                                .
                            </div>
                        @endif
                    </div>
                </div>

                @yield('content')


            </div>
            <!-- /content area -->

        </div>
        <!-- /main content -->

    </div>
    <!-- /page content -->


</body>

</html>
