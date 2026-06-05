@php
    if(isset($app_id)){
        $current_app = \Modules\Settings\Entities\MyApp::find($app_id);
    }else{
        $current_app = null;
    }

    if(!isset($user)) $user = \Illuminate\Support\Facades\Auth::user();
@endphp
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ env('APP_NAME') }} :: {{ $title ?? "" }}</title>

    <!-- Global stylesheets -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet"
          type="text/css">
    <link href="{{ asset('assets/css/icons/fontawesome/styles.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/icons/icomoon/styles.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
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

    <!-- Theme JS files -->
    <script src="{{ asset('assets/js/plugins/visualization/d3/d3.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/visualization/d3/d3_tooltip.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/forms/styling/switchery.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/forms/selects/bootstrap_multiselect.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/ui/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/pickers/daterangepicker.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
    <!-- /theme JS files -->

    <script type="text/javascript">
        $(document).ready(function () {
            $(".delete").click(function (e) {
                return confirm('Are you sure you want to delete?')
            })
        })

        $(document).ready(function () {
            $.each($(".req"), function(i, j){
                var label = $(this).html();
                $(this).html(label+" <span class='starik' style='color:red;font-size:11px;'>*</span>");
            })

        });

        $(document).ready(function () {
            $(".data_mf_table").dataTable()
        })
    </script>

    @stack('stylesheets')
    <style>
        .wizard-form .steps{
        background: #F5F5F5;
    }
    .wizard-form .actions{
        display: none;
    }
    .wizard>.steps>ul>li.current .number{
        border-color: #46A24A;
        color: #46A24A;
    }
    .wizard>.steps>ul>li.current>a{
        color: #46A24A;
    }
    .wizard>.steps>ul>li.done .number{
        background-color: #46A24A;
        border-color: #46A24A;
    }
    .wizard>.steps>ul>li:after,
    .wizard>.steps>ul>li:before{
        background-color: #46A24A;
    }
    .wizard-form .content{
        /* border: 1px solid #46A24A; */
        margin: 0px;
        padding-top: 10px;
        box-shadow: 0 1px 10px rgb(0 0 0 / 5%);
    }

    .wizard-form .content .form-control,
    .wizard-form .content .select2-selection--single:not([class*=bg-]):not([class*=border-]),
    .modal .form-control,
    .modal .select2-selection--single:not([class*=bg-]):not([class*=border-]){
        background: #f5f5f5;
    }

    .wizard-form .content label{
        color: #46A24A;
        font-weight: 500;
    }

    .wizard-form .content label[class^="form"]{
        color: unset;
        font-weight: unset;
    }
    .wizard-form fieldset{
        min-height: 240px;
    }
    .custom-nav ul{
        padding: 0px;
        list-style: none;
        margin: 0px;
        display: flex;
        flex-direction: row;
        justify-content: space-around;
        border: 1px solid #eee;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
        overflow: hidden;
        margin-top: 30px;
    }

    .custom-nav ul li a{ color: #999; }
    .custom-nav ul li{
        width: 100%;
        background: white;
        text-align: center;
        padding: 10px;
        font-size: 1rem;
        border-left: 1px solid #eee;
        border-right: 1px solid #eee;
    }
    .custom-nav ul li:first-child{border-left: unset;}
    .custom-nav ul li:last-child{border-right: unset;}
    .custom-nav ul li.active{
        background: #46A24A;
    }
    .custom-nav ul li.active::before{
        content: "";
        font-family: icomoon;
        display: inline-block;
        font-size: 1rem;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        transition: all ease-in-out .15s;
        color: white;
        margin-right: 5px;
    }
    .custom-nav ul li.active a{
        color: white;
    }
    @media(max-width: 768px){
        .wizard-form .steps > ul {
            display: none;
        }
        .wizard-form .custom-nav ul{
            margin-top: unset;
        }
    }
    </style>

    @stack('scripts')

</head>

<body>

<!-- Main navbar -->
<div class="navbar navbar-expand-md navbar-dark" style="background-color: #{{ env('GREEN') }}">
    <div class="navbar-brand wmin-200" style="padding: 0px;margin: 0px;">
        <a href="/" class="d-inline-block" style="font-size: 26px; color: #fff; padding-top: 9px;">
            {{--<img src="{{ asset('assets/images/logo_light.png') }}" alt="">--}}
            {{--<img src="{{ asset('assets/images/logo_white.svg') }}" alt="" style="width: 70px; height: auto; display: inline-block">--}}
            {{ env('APP_ABBR') }}
        </a>
    </div>

    <div class="d-md-none">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-mobile">
            {{--<i class="icon-tree5"></i>--}}
            <i class="icon-user"></i>
            <i class="icon-chevron-down"></i>
        </button>
        <button class="navbar-toggler sidebar-mobile-main-toggle" type="button">
            <i class="icon-paragraph-justify3"></i>
        </button>
    </div>

    <div class="collapse navbar-collapse" id="navbar-mobile">
        <ul class="navbar-nav">
            <li class="nav-item">
                {{--<a href="#" class="navbar-nav-link sidebar-control sidebar-main-toggle d-none d-md-block">
                    <i class="icon-paragraph-justify3"></i>
                </a>--}}
            </li>


        </ul>

        <span class="b-adge b-g-success-400 ml-md-auto mr-md-3">{{--Active--}}</span>

        <ul class="navbar-nav">


            <li class="nav-item dropdown dropdown-user">
                <a href="#" class="navbar-nav-link d-flex align-items-center dropdown-toggle" data-toggle="dropdown">
                    <img src="{{ asset('assets/images/placeholders/admin2.jpeg') }}" class="rounded-circle mr-2"
                         height="34" alt="">
                    <span>{{ $user->name }}</span>
                </a>

                <div class="dropdown-menu dropdown-menu-right">
                    <a href="{{ route('settings.users-mgt.my-profile') }}" class="dropdown-item"><i class="icon-user-plus"></i> My profile</a>
                    {{--<a href="#" class="dropdown-item"><i class="icon-coins"></i> My balance</a>
                    <a href="#" class="dropdown-item"><i class="icon-comment-discussion"></i> Messages <span
                            class="badge badge-pill bg-blue ml-auto">58</span></a>
                    <div class="dropdown-divider"></div>--}}
                    <a href="{{ route('settings.users-mgt.change-password') }}" class="dropdown-item"><i class="icon-cog5"></i> Change Password</a>

                    {{--<a href="#" class="dropdown-item"><i class="icon-switch2"></i> Logout</a>--}}
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
                <a href="{{ route('app.landing-screen') }}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>

                @if(isset($current_app))
                    <span class="breadcrumb-item">
                    <a href="{{ url($current_app->route) }}">
                    {{ $current_app->title ?? '' }}
                    </a>
                </span>
                @endif

                @if(isset($back_route))
                    @if(isset($back_route[2]))
                        <span class="breadcrumb-item">
                        <a href="{{ $back_route[0] }}">
                        {{ $back_route[1] }}
                        </a>
                    </span>
                    @else
                        <span class="breadcrumb-item">
                        <a href="{{ route($back_route[0]) }}">
                        {{ $back_route[1] }}
                        </a>
                    </span>
                    @endif

                @endif

                <span class="breadcrumb-item active">{{ $title ?? "" }}</span>

            </div>

            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>

        <div class="header-elements d-none">
            {{--<div class="breadcrumb justify-content-center">
                <a href="#" class="breadcrumb-elements-item">
                    <i class="icon-comment-discussion mr-2"></i>
                    Support
                </a>

                <div class="breadcrumb-elements-item dropdown p-0">
                    <a href="#" class="breadcrumb-elements-item dropdown-toggle" data-toggle="dropdown">
                        <i class="icon-gear mr-2"></i>
                        Settings
                    </a>

                    <div class="dropdown-menu dropdown-menu-right">
                        <a href="#" class="dropdown-item"><i class="icon-user-lock"></i> Account security</a>
                        <a href="#" class="dropdown-item"><i class="icon-statistics"></i> Analytics</a>
                        <a href="#" class="dropdown-item"><i class="icon-accessibility"></i> Accessibility</a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item"><i class="icon-gear"></i> All settings</a>
                    </div>
                </div>
            </div>--}}
        </div>
    </div>

    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex" style="padding: 1rem 0;">
            <h4>
                <a href="{{ route('app.landing-screen') }}">
                    <i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">Home</span>
                </a> -

                @if(isset($current_app))
                    <a href="{{ url($current_app->route) }}">
                        {{ $current_app->title ?? '' }}
                    </a> -
                @endif

                {{ $title ?? '' }}
            </h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>


        @include('layouts._partials.page_actions', ['back_route' => $back_route ?? null])


    </div>
</div>
<!-- /page header -->


<!-- Page content -->
<div class="page-content pt-0">

    <!-- Main sidebar -->
    <div class="sidebar sidebar-light sidebar-main sidebar-expand-md align-self-start">

        <!-- Sidebar mobile toggler -->
        <div class="sidebar-mobile-toggler text-center">
            <a href="#" class="sidebar-mobile-main-toggle">
                <i class="icon-arrow-left8"></i>
            </a>
            <span class="font-weight-semibold">Main sidebar</span>
            <a href="#" class="sidebar-mobile-expand">
                <i class="icon-screen-full"></i>
                <i class="icon-screen-normal"></i>
            </a>
        </div>
        <!-- /sidebar mobile toggler -->


        <!-- Sidebar content -->
        <div class="sidebar-content">
            <div class="card card-sidebar-mobile">

                <!-- Header -->
                <div class="card-header header-elements-inline">
                    <h6 class="card-title">Navigation</h6>
                    <div class="header-elements">
                        <div class="list-icons">

                            {{--<a class="list-icons-item" data-action="collapse"></a>--}}

                            <a href="#"
                               class="list-icons-item navbar-nav-link sidebar-control sidebar-main-toggle d-none d-md-block "
                               style="padding: 0">
                                <i class="icon-transmission"></i>
                            </a>

                        </div>
                    </div>
                </div>

                <!-- User menu -->
                <div class="sidebar-user">
                    <div class="card-body">
                        <div class="media">
                            <div class="mr-3">
                                <a href="#"><img src="{{ asset('assets/images/placeholders/admin2.jpeg') }}" width="38"
                                                 height="38" class="rounded-circle" alt=""
                                                 style="border: 1px solid #ccc"></a>
                            </div>

                            <div class="media-body">
                                <div class="media-title font-weight-semibold">{{ $user->name }}</div>
                                <div class="font-size-xs opacity-50">
                                    <i class="icon-pin font-size-sm"></i> &nbsp;Khyber Pakhtunkhwa
                                </div>
                            </div>

                            <div class="ml-3 align-self-center">
                                <a href="#" class="text-white"><i class="icon-cog3"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /user menu -->


                <!-- Main navigation -->
                <div class="card-body p-0">
                    <ul class="nav nav-sidebar" data-nav-type="accordion">

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
                        })->where('app_id', '=', $current_app->id)->get();
                        // dd($menu_r->toSql());
                    @endphp

                    <!-- Main -->
                        <li class="nav-item-header mt-0">
                            <div class="text-uppercase font-size-xs line-height-xs">
                                <hr>{{--Main--}}
                            </div>
                            <i class="icon-menu" title="Main"></i></li>

                        <li class="nav-item">
                            <a href="{{ url($current_app->route) }}" class="nav-link active">
                                <i class="icon-home4"></i>
                                <span>
										Dashboard
										{{--<span class="d-block font-weight-normal opacity-50">No active orders</span>--}}
									</span>
                            </a>
                        </li>

                        @foreach($menu_r as $menu)
                            <li class="nav-item nav-item-submenu">
                                <a href="#" class="nav-link"><i class="{{ $menu->icon }}"></i>
                                    <span>{{ $menu->title }}</span></a>
                                <ul class="nav nav-group-sub" data-submenu-title="Layouts">
                                    @foreach($menu->myPermissions as $mp)
                                        @foreach($mp->routes as $mpr)
                                            <li class="nav-item"><a href="{{ route($mpr->route) }}" class="nav-link">{{ $mpr->title }}</a></li>
                                        @endforeach
                                    @endforeach
                                </ul>
                            </li>
                        @endforeach


                    {{--<li class="nav-item nav-item-submenu">
                        <a href="#" class="nav-link"><i class="icon-copy"></i> <span>Layouts</span></a>
                        <ul class="nav nav-group-sub" data-submenu-title="Layouts">
                            <li class="nav-item"><a href="#" class="nav-link">Default layout</a></li>
                            <li class="nav-item"><a href="#" class="nav-link">Layout 2</a></li>
                            <li class="nav-item"><a href="#" class="nav-link active">Layout 3</a></li>
                            <li class="nav-item"><a href=".#" class="nav-link">Layout 4</a></li>
                        </ul>
                    </li>
                    <!-- /main -->

                    <!-- Layout -->
                    <li class="nav-item-header"><div class="text-uppercase font-size-xs line-height-xs">Layout</div> <i class="icon-menu" title="Layout options"></i></li>

                    <li class="nav-item nav-item-submenu">
                        <a href="#" class="nav-link"><i class="icon-stack2"></i> <span>Page layouts</span></a>

                        <ul class="nav nav-group-sub" data-submenu-title="Page layouts">
                            <li class="nav-item"><a href="#" class="nav-link">Fixed main navbar</a></li>
                            <li class="nav-item"><a href="#" class="nav-link">Sticky secondary navbar</a></li>
                        </ul>
                    </li>
                    <li class="nav-item nav-item-submenu">
                        <a href="#" class="nav-link"><i class="icon-page-break2"></i> <span>Headers &amp; footers</span></a>
                        <ul class="nav nav-group-sub" data-submenu-title="Content styling">
                            <li class="nav-item"><a href="content_page_header.html" class="nav-link">Page header</a></li>
                            <li class="nav-item"><a href="content_page_footer.html" class="nav-link disabled">Page footer <span class="badge bg-transparent align-self-center ml-auto">Coming soon</span></a></li>
                        </ul>
                    </li>--}}

                    <!-- /layout -->

                    </ul>
                </div>
                <!-- /main navigation -->

            </div>
        </div>
        <!-- /sidebar content -->

    </div>
    <!-- /main sidebar -->


    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Content area -->
        <div class="content">

            <!-- Main charts -->
        {{--<div class="row">
            <div class="col-12">

                <!-- Traffic sources -->
                <div class="card">
                    <div class="card-header header-elements-inline">
                        <h6 class="card-title">Sample Card - widget</h6>
                        <div class="header-elements">
                            <div class="form-check form-check-right form-check-switchery form-check-switchery-sm">

                                --}}{{--<label class="form-check-label">
                                    Live update:
                                    <input type="checkbox" class="form-input-switchery" checked data-fouc>
                                </label>--}}{{--
                            </div>
                        </div>
                    </div>

                    <div class="card-body">

                        <div class="row">
                            <div class="col-5">asdf</div>
                        </div>

                    </div>


                </div>
                <!-- /traffic sources -->

            </div>
        </div>--}}
        <!-- /main charts -->

            <div class="row">
                <div class="col-12">
                    @if(\Illuminate\Support\Facades\Session::has('error'))
                        <div class="alert alert-danger alert-styled-left alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                            <span
                                class="font-weight-semibold">Error! </span> {{ \Illuminate\Support\Facades\Session::get('error') }}
                            .
                        </div>
                    @endif

                    @if(\Illuminate\Support\Facades\Session::has('success'))
                        <div class="alert alert-success alert-styled-left alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                            <span
                                class="font-weight-semibold">Success! </span> {{ \Illuminate\Support\Facades\Session::get('success') }}
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


<!-- Footer -->
<div class="navbar navbar-expand-lg navbar-light">
    <div class="text-center d-lg-none w-100">
        <button type="button" class="navbar-toggler dropdown-toggle" data-toggle="collapse"
                data-target="#navbar-footer">
            <i class="icon-unfold mr-2"></i>
            Footer
        </button>
    </div>

    <div class="navbar-collapse collapse" id="navbar-footer">
			<span class="navbar-text">
				&copy; {{ date("Y") }}. <a href="#">{{ env('APP_ABBR') }}</a> by <a href="#"
                                                                                 target="_blank">Techies</a>
			</span>

    </div>
</div>
<!-- /footer -->


 <!-- Theme JS Form files -->
 <script src="{{ asset('assets/js/plugins/forms/wizards/steps.min.js') }}"></script>
 <script src="{{ asset('assets/js/plugins/forms/selects/select2.min.js') }}"></script>
 <script src="{{ asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
 <script src="{{ asset('assets/js/plugins/forms/inputs/inputmask.js') }}"></script>
 <script src="{{ asset('assets/js/plugins/forms/validation/validate.min.js') }}"></script>
 <script src="{{ asset('assets/js/plugins/extensions/cookie.js') }}"></script>

 @stack('scripts-bottom')

 <script src="{{ asset('assets/js/app.js') }}"></script>
 <script src="{{ asset('assets/js/demo_pages/form_wizard.js') }}"></script>
 <script src="{{ asset('assets/js/demo_pages/dashboard.js') }}"></script>
 <!-- /theme JS files -->

 <script>
    $(function(){
        $('.form-control-select2').select2();


        // Custom links development
        if( $('form.wizard-form').html() != undefined ) {

            var formID = $('form.wizard-form').data('id');
            var links = [];

            $('.steps ul:first').removeAttr('role');
            $('.steps ul:first li').each(function(i,v){

                // Remove Default Behaviour
                $(v).find('a').removeAttr('id');
                $(v).find('a').removeAttr('aria-controls');
                $(v).removeAttr('role');
                $(v).removeAttr('aria-disabled');
                $(v).removeAttr('aria-selected');

                $(v).addClass('custom-link');

                if( formID == undefined || formID == 0) {
                    hrf = "javascript: void(0);"
                    $(v).find('a').off("click").attr('href', hrf);
                    $(v).find('a').attr('title', "Not Allowed! Complete General info then continue.");
                } else {
                    var hrf = $.trim($(v).find('.step-link').html());
                    $(v).find('a').attr('href', hrf);
                }

                links.push({
                    title: $.trim($(v).find('span.step-title').text()),
                    icon: $.trim($(v).find('span.step-icon').html()),
                    href: hrf,
                    active: $(v).hasClass('current')
                })
            })

            if( links.length > 0) {
                htm = `<div class="custom-nav"><ul>`;
                $.each(links, function(i,li){
                    hrf = `<a href="${li.href}">${li.title}</a>`;
                    htm += `<li class="${li.active ? 'active' : ''}">${hrf}</li>`
                })
                htm += `</ul></div>`;
                $('form.wizard-form:first .steps:first').append(htm);
            }

        }

        /** Wizard custom link click event */
        $('.custom-link a').click(function(e){
            var hrf = $(this).attr('href');
            e.preventDefault();
            e.stopPropagation();
            if( hrf != undefined) {
                window.location.href = hrf;
            }
        })
    })
</script>

</body>
</html>
