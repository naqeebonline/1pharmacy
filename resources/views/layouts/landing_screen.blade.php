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
    <!-- /global stylesheets -->

    <!-- Core JS files -->
    <script src="{{ asset('assets/js/main/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/main/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/loaders/blockui.min.js') }}"></script>
    <!-- /core JS files -->
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
                {{-- <img src="{{ asset('assets/images/logo_white.svg') }}" alt="" style="width: 70px; height: auto; display: inline-block"> --}}
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


    <!-- Page content -->
    <div class="page-content -pt-0">

        <!-- Main content -->
        <div class="content-wrapper" style="-border: 1px solid red">

            <!-- Content area -->
            <div class="content" style="-border: 1px solid green">

                <div class="container pt-5">

                    <div class="row pb-5">
                        <div class="col-12">

                            <div class="form-group form-group-feedback form-group-feedback-left"
                                style="margin-bottom: 0px">
                                <input type="text" class="form-control rounded-pill form-control-lg"
                                    placeholder="Search module...">
                                <div class="form-control-feedback form-control-feedback-lg">
                                    <i class="icon-search4"></i>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- modules list -->
                    <div class="row">

                        @foreach ($apps as $app)
                            <div class="col-2 text-center">

                                <a href="{{ url($app->route) }}">
                                    <div class="card">
                                        <div class="card-body">

                                            <div class="text-center">
                                                <i class="{{ $app->icon }} icon-2x"></i><br>
                                                <p class="pt-2"><strong>{{ $app->title }}</strong></p>
                                            </div>

                                        </div>
                                    </div>
                                </a>

                            </div>
                        @endforeach


                    </div>
                    <!-- modules list -->

                </div>

            </div>
            <!-- /content area -->

        </div>
        <!-- /main content -->

    </div>
    <!-- /page content -->


</body>

</html>
