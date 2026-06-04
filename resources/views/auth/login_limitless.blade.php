<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>KPITB Digital</title>

    <!-- Global stylesheets -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/icons/icomoon/styles.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/bootstrap_limitless.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/layout.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/components.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/colors.min.css') }}" rel="stylesheet" type="text/css">
    <!-- /global stylesheets -->

    <!-- Core JS files -->
    <script src="{{ asset('assets/js/main/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/main/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/loaders/blockui.min.js') }}"></script>
    <!-- /core JS files -->

    <!-- Theme JS files -->
    <script src="{{ asset('assets/assets/js/app.js') }}"></script>
    <!-- /theme JS files -->

</head>

<body>

<!-- Main navbar -->
<div class="navbar navbar-expand-md navbar-dark" style="background-color: #{{ env('GREEN') }}">
    <div class="navbar-brand text-center" style="width: 100%; padding: 0px;margin: 0px;">
        <a href="/" class="d-inline-block" style="font-size: 20px; color: #fff; padding-top: 9px;">
            {{--<img src="{{ asset('assets/images/logo_light.png') }}" alt="">--}}
            {{--Khyber Pakhtunkhwa <i>Integrated Security Workspace</i>--}}
            {{ env('APP_NAME') }}
        </a>
    </div>
</div>
<!-- /main navbar -->


<!-- Page content -->
<div class="page-content">

    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Content area -->
        <div class="content d-flex justify-content-center align-items-center">

            <!-- Login form -->
            <form class="login-form" method="POST" action="{{ route('custom-authenticate') }}">
                @csrf

                @if(\Illuminate\Support\Facades\Session::has('error'))
                <div class="alert alert-danger">
                    {{ \Illuminate\Support\Facades\Session::get('error') }}
                </div>
                @endif

                <div class="card mb-0">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <i class="icon-unlocked2 icon-2x text-slate-400 border-slate-400 border-3 rounded-round p-3 mb-3 mt-1"></i>

                            {{--<div class="text-center">
                                <img src="{{ asset('assets/images/logo.svg') }}" alt="" style="width: 150px">
                            </div>--}}

                            <h5 class="mb-0">Login to your account</h5>
                            <span class="d-block text-muted">Enter your credentials below</span>
                        </div>


                        <div class="form-group form-group-feedback form-group-feedback-left">
                            <input type="text" id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="username" autofocus placeholder="Username">
                            <div class="form-control-feedback">
                                <i class="icon-user text-muted"></i>
                            </div>

                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>



                        <div class="form-group form-group-feedback form-group-feedback-left">
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                            <div class="form-control-feedback">
                                <i class="icon-lock2 text-muted"></i>
                            </div>

                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror


                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-info btn-block">Sign in <i class="icon-circle-right2 ml-2"></i></button>
                        </div>

                        {{--<div class="text-center">
                            <a href="login_password_recover.html">Forgot password?</a>
                        </div>--}}
                    </div>
                </div>
            </form>
            <!-- /login form -->

        </div>
        <!-- /content area -->




    </div>
    <!-- /main content -->

</div>
<!-- /page content -->

</body>
</html>
