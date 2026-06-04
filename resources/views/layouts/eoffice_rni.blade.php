<!DOCTYPE html>
<html>
    <head>

        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-66715839-7"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments); }
            gtag('js', new Date());
            gtag('config', 'UA-66715839-7');
        </script>


        <meta http-equiv="content-type" content="text/html;charset=UTF-8"/>
        <meta charset="utf-8"/>
        <title>@yield('title') - eOffice RNI</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
        <meta content="" name="description"/>
        <meta content="" name="author"/>

        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <!-- BEGIN PLUGIN CSS -->
        <link href="{!! asset('assets/plugins/pace/pace-theme-flash.css') !!}" rel="stylesheet" type="text/css"

              media="screen"/>
        <link href="{!! asset('assets/plugins/datatable-1.18/dataTables.css') !!}" rel="stylesheet" type="text/css"/>
        <!-- END PLUGIN CSS -->
        <!-- BEGIN CORE CSS FRAMEWORK -->
        <link href="{!! asset('assets/plugins/boostrapv3/css/bootstrap.min.css') !!}" rel="stylesheet" type="text/css"/>
        <link href="{!! asset('assets/plugins/boostrapv3/css/bootstrap-theme.min.css') !!}" rel="stylesheet"
              type="text/css"/>
        <link href="{!! asset('assets/plugins/font-awesome-old/css/font-awesome.min.css') !!}" rel="stylesheet"
              type="text/css"/>
        <link href="{!! asset('assets/plugins/fontawesome-free-5.8.1-web/css/all.min.css') !!}" rel="stylesheet"
              type="text/css"/>
        <link href="{!! asset('assets/plugins/webfont-maki-master/css/maki-min.css') !!}" rel="stylesheet"
              type="text/css"/>
        <link href="{!! asset('assets/css/animate.min.css') !!}" rel="stylesheet" type="text/css"/>
        <link href="{!! asset('assets/plugins/jquery-scrollbar/jquery.scrollbar.css') !!}" rel="stylesheet"
              type="text/css"/>
        <!-- END CORE CSS FRAMEWORK -->
        <!-- BEGIN CSS TEMPLATE -->
        <link href="{!! asset('assets/css/style.css') !!}" rel="stylesheet" type="text/css"/>
        <link href="{!! asset('assets/css/responsive.css') !!}" rel="stylesheet" type="text/css"/>
        <link href="{!! asset('assets/css/custom-icon-set.css') !!}" rel="stylesheet" type="text/css"/>
        <link href="{!! asset('assets/plugins/bootstrap-datepicker/css/datepicker.min.css') !!}" rel="stylesheet"
              type="text/css" media="screen"/>



        <!-- clock picker -->
        <link href="{!! asset('assets/plugins/boostrap-clockpicker/bootstrap-clockpicker.min.css') !!}" rel="stylesheet"
              type="text/css" media="screen"/>

        <link href="{!! asset('assets/plugins/bootstrap-select2/select2.min.css') !!}" rel="stylesheet"
              type="text/css" media="screen"/>


        <link href="{!! asset('assets/plugins/jquery-lightbox/css/lightbox.css') !!}" rel="stylesheet" type="text/css"
              media="screen"/>

        <link href="{!! asset('assets/plugins/DataTables-1.10.12/media/css/jquery.dataTables.min.css') !!}" rel="stylesheet"
              type="text/css" media="screen"/>
        <link href="{!! asset('assets/plugins/DataTables-1.10.12/extensions/Buttons/css/buttons.dataTables.min.css') !!}"
              rel="stylesheet"
              type="text/css" media="screen"/>


        <link rel="stylesheet" href="{!! asset('assets/plugins/tipsy/css/tipsy.css') !!}">
        <link href="{!! asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.min.css') !!}" rel="stylesheet" type="text/css" media="screen"/>
        <link href="{!! asset('assets/plugins/tooltiper/tooltiper.css') !!}" rel="stylesheet" type="text/css"/>

        <link href="{!! asset('assets/css/my_style.css') !!}" rel="stylesheet" type="text/css"/>

        <!-- END CSS TEMPLATE -->



        <link rel="stylesheet" href="{!! asset('assets/plugins/jquery-ui/jquery-ui-1.10.1.custom.min.css') !!}">


        @stack('stylesheets')


        <script src="{!! asset('assets/plugins/jquery-1.8.3.min.js') !!}" type="text/javascript"></script>

        <script src="{!! asset('assets/plugins/sweetalert-master/dist/sweetalert.min.js') !!}"></script>
        <link rel="stylesheet" type="text/css" href="{!! asset('assets/plugins/sweetalert-master/dist/sweetalert.css') !!}">





        @stack('scripts')

        <script src="{!! asset('assets/plugins/printThis/printThis.js') !!}"></script>



        <script type="text/javascript">

            $(document).ready(function () {

            $('.tipsy').tipsy({gravity: $.fn.tipsy.autoNS});
            $(".delete .btn").click(function(e) {
            return confirm("Are you sure you want to delete?");
            });


            //notification alerts
            @if (session()->has('success'))
                    $.notify('{!! session()->get('success') !!}', {
                    style: 'bootstrap',
                            className: 'success',
                            position: "top center"
                    });
            @elseif(session()->has('error'))
                    $.notify('{!! session()->get('error') !!}', {
                    style: 'bootstrap',
                            className: 'error',
                            position: "top center"
                    });
            @elseif(session()->has('info'))
                    $.notify('{!! session()->get('info') !!}', {
                    style: 'bootstrap',
                            className: 'info',
                            position: "top center"
                    });
            @endif


                    $(".datepicker").datepicker({
            format: 'yyyy-mm-dd',
                    autoclose: true
            });
            $(".datepicker_custom1").datepicker({
            format: 'yyyy-mm-dd',
                    autoclose:true,
                    endDate: "today",
                    maxDate: "today"
            });
            $(".yearpicker").datepicker({
            format: 'yyyy',
                    minViewMode: 'years',
                    autoclose: true
            });
            $(".monthpicker").datepicker({
            format: 'yyyy-mm',
                    minViewMode: 'months',
                    autoclose: true
            });
            $(document).ready(function() {
            $(".select2").select2();
            });
            $(document).ready(function () {
            $('.clockpicker').clockpicker({
            autoclose: true
            });
            });
            $(document).ready(function () {
            $.each($(".req"), function(i, j){
            var label = $(this).html();
            $(this).html(label + " <span style='color:red;font-size:11px;'>*</span>");
            })

            });
            })
        </script>


        <style type="text/css">
            @media only screen{
                .fuck-table-responsive{
                    overflow-x: auto !important;
                }
                .mb-1{
                    margin-bottom:10px !important;
                }
                .mb-2{
                    margin-bottom:20px !important;
                }
                .page-sidebar.mini{
                    width: 10px !important;
                }
                .page-sidebar .menu-title{
                    font-size: 16px;
                    border-bottom: 1px dotted lightgray !important;
                }
                .page-sidebar .menu-title i{
                    font-size: 16px;
                }
                .page-sidebar .inner-menu{
                    width: 240px !important;
                    border-right: 1px dotted lightgray !important;
                }
                .page-sidebar .inner-menu ul {
                    width: 230px !important;
                }
                .page-sidebar .inner-menu ul li{
                    line-height: 16px;
                }
                .page-sidebar .inner-menu ul li a{
                    font-size: 15px;
                }
                .inner-menu-content .menu-title{
                    padding-top: 5px;
                }
                .inner-menu .inner-wrapper{
                    padding: 15px 10px 0 !important;
                }
                .inner-menu .compose a{
                    background-color: #0090d9 !important;
                    transition: 2s all;
                    border: 1px solid #0090d9;
                }
                .inner-menu .compose:hover a{
                    background-color: white !important;
                    color: #0090d9 !important;
                    border: 1px solid #0090d9;
                }
                .header .search-form{
                    margin-left: 2px !important;
                }
                .header-seperation,
                .footer-widget {
                    background-color: #0090d9 !important;
                }
                .footer-widget a{
                    color: white !important;
                }
                .header .nav > li.dropdown .active{
                    background-color: #0090d9 !important;
                    border: 1px solid white;
                }
                .inner-menu{
                    background: #E5E9EC !important;
                }
                .inner-menu ul.big-items li .badge{
                    margin-left: 5px;
                }
                .inner-menu ul.big-items li:hover{
                    cursor: pointer;
                }
                .inner-menu ul.big-items li:hover .badge,
                .inner-menu ul.big-items li.active .badge{
                    color: #0090d9 !important;
                    background: #ffffff !important;
                }
                .inner-menu ul.big-items li{
                    margin-top: 5px;
                }
                .inner-menu ul.big-items li a{
                    color: black;
                }
                .inner-menu ul.big-items li:hover a{
                    color: #ffffff !important;
                }
                .inner-menu ul.big-items li.active,
                .inner-menu ul.big-items li:hover,
                .inner-menu ul.big-items li.active a{
                    border-top-right-radius: 5px !important;
                    border-bottom-right-radius: 5px !important;
                    background: #0090d9 !important;
                    color: white !important;
                }
                .page-sidebar-wrapper ul{
                    display: none !important;
                }
                .inner-menu ul.big-items li{
                    padding: 8px 20px !important;
                }

                .blinking{
                    animation:blinkingText 0.8s infinite;
                }
                @keyframes blinkingText{
                    0%{		color: red;	}
                    49%{	color: transparent;	}
                    50%{	color: transparent;	}
                    99%{	color:transparent;	}
                    100%{	color: #red;	}
                }
                .box .tiles-body{
                    text-align: center
                }
                .box .tiles{
                    background: transparent;
                    color: inherit;
                }
                .summary .box .tiles .tiles-title{
                    font-size: 16px;
                }
                .page-content > .content{
                    padding-left: 15px;
                    padding-right: 15px;
                }
                .summary .tiles .tiles-body,
                .summary2 .tiles .tiles-body {
                    padding: 0px 10px 5px 15px;
                }
                .summary2 .box .tiles .tiles-title{
                    font-size: 12px;
                }
                .summary .box .tiles{
                    /* border-right: 1px solid #cecece; */
                }
                .box-heading{
                    padding: 5px 20px;
                    border: 2px solid #0090d9;
                    background: white;
                    font-size: 20px;
                    z-index: 1;
                    border-radius: 5px;
                }
                .box2{
                    float: left;
                    padding: 5px;
                    border: 1px solid;
                    border-radius: 8px;
                    background: #66ccff;
                    border: 1px solid #66ccff;
                    box-shadow: 0px 0px 2px #66ccff;
                    padding-right: 10px;
                    margin: 5px;
                }
                .box2 a{
                    color: white;
                    display: flex;
                    flex-direction: row;
                }
                .box2 div.counter{
                    padding: 3px 5px;
                    border: 1px solid white;
                    border-radius: 8px;
                    background: white;
                    color: #0090d9;
                    font-weight: bold;
                    min-width: 40px;
                    float: left;
                    text-align: center;
                    font-size: 16px;
                }
                .box2 div.title{
                    margin-top: 5px;
                    margin-left: 5px;
                    font-size: 16px;
                }
                .text-danger{
                    color: #0090d9 !important;
                }
                .floating-button{
                    position:fixed;
                    width:50px;
                    height:50px;
                    bottom:30px;
                    right:30px;
                    background-color:#0090d9;
                    color:#FFF;
                    border-radius:50px;
                    text-align:center;
                    box-shadow: 2px 2px 3px #999;
                }
                .floating-add{
                    margin-top:18px;
                    color: white !important;
                }
                .quick-section .add-on:hover{
                    cursor: pointer;
                }
                .select2-chosen{
                    padding-bottom: 5px;
                }
            }
        </style>

    </head>
    <!-- END HEAD -->

    <!-- BEGIN BODY -->
    <body class="app_{{ $app_id }}">

        <!-- BEGIN HEADER -->
        <div class="header navbar navbar-inverse ">
            <!-- BEGIN TOP NAVIGATION BAR -->
            <div class="navbar-inner">
                <div class="header-seperation">
                    <ul class="nav pull-left notifcation-center" id="main-menu-toggle-wrapper" style="display:none">
                        <li class="dropdown"><a id="main-menu-toggle" href="#main-menu" class="">
                                <div class="iconset top-menu-toggle-white"></div>
                            </a></li>
                    </ul>
                    <!-- BEGIN LOGO -->
                    <a href="{!! route('home') !!}">
                        <img src="{!! asset('assets/img/logo2x.png') !!}" class="logo" alt=""
                            data-src="{!! asset('assets/img/logo2x.png') !!}"
                            data-src-retina="{!! asset('assets/img/logo2x.png') !!}"
                            height="20"/></a>
                    <!-- END LOGO -->
                    <ul class="nav pull-right notifcation-center">
                        <li class="dropdown" id="header_task_bar">
                            <a href="{!! route('home') !!}" title="IPMS Apps Home" class="dropdown-toggle active" data-toggle="">
                                <div class="iconset top-home"></div>
                            </a>
                        </li>

                    </ul>
                </div>
                <!-- END RESPONSIVE MENU TOGGLER -->
                <div class="header-quick-nav">
                    <!-- BEGIN TOP NAVIGATION MENU -->
                    <div class="pull-left">
                        <ul class="nav quick-section">
                            {{-- <li class="quicklinks"><a href="#" class="" id="layout-condensed-toggle">
                                    <div class="iconset top-menu-toggle-dark"></div>
                                </a>
                            </li> --}}
                            <li class="m-r-10 input-prepend inside search-form no-boarder">
                                <span class="add-on" onclick="$('.barcode-search').trigger('click')">
                                    <span class="iconset top-search"></span>
                                </span>
                                {{-- <form method="POST" action="{{ route('reports.barcodesearch') }}" @if( Route::currentRouteName() != 'reports.barcodesearch' ) target="_blank" @endif>
                                    @csrf
                                    <input name="barcode" type="text" class="no-boarder" placeholder="Search file using BARCODE / Tracking ID" style="width:300px;">
                                    {!! Form::submit('submit', ['class'=>'hidden barcode-search']) !!}
                                </form> --}}
                            </li>
                        </ul>



                    </div>
                    <!-- END TOP NAVIGATION MENU -->
                    <!-- BEGIN CHAT TOGGLER -->
                    <div class="pull-right">
                        <div class="chat-toggler">
                            <a href="#" class="dropdown-toggle" id="my-task-list" data-placement="bottom" data-content=''
                               data-toggle="dropdown" data-original-title="Notifications">
                                <div class="user-details">
                                    <div class="username">
                                        <span id="not-counter" class="badge badge-important" style="visibility: hidden">3</span>
                                        {{-- @inject('comm', 'App\Utilities\ViewCommonData') --}}
                                        <?php
                                        $user_title = "RNI";// $comm->userCol(session()->get('user_id'), "title");
                                        $tparts = explode(" ", $user_title);
                                        //var_dump($tparts);
                                        ?>


                                        @foreach($tparts as $p)

                                        @if($loop->index > 0)
                                        <span class="bold">
                                            @endif

                                            {{ $tparts[$loop->index] }}

                                            @if($loop->iteration == count($tparts))
                                        </span>
                                        @endif

                                        @endforeach
                                    </div>
                                </div>
                                <div class="iconset top-down-arrow"></div>
                            </a>
                            <div id="notification-list" style="display:none">

                                <div style="width:300px">

                                </div>

                            </div>
                            <div class="profile-pic">
                                <img src="{!! asset('assets/img/profiles/avatar_small2x.jpg') !!}" alt=""
                                     data-src="{!! asset('assets/img/profiles/avatar_small2x.jpg') !!}"
                                     data-src-retina="{!! asset('assets/img/profiles/avatar_small2x.jpg') !!}" width="35" height="35"/>
                            </div>
                        </div>
                        <ul class="nav quick-section ">
                            <li class="quicklinks">
                                <a data-toggle="dropdown" class="dropdown-toggle  pull-right " href="#" id="user-options">
                                    <div class="iconset top-settings-dark "></div>
                                </a>
                                <ul class="dropdown-menu  pull-right" role="menu" aria-labelledby="user-options">
                                    {{-- <li><a href="{!! route('users.change_email') !!}"><i class="fa fa-envelope"></i> Update
                                            Email</a>
                                    </li>
                                    <li><a href="{!! route('users.change_password') !!}"><i class="fa fa-certificate"></i> Update Password</a>
                                    </li>
                                    <li class="divider"></li>
                                    <li><a href="{!! route('users.logout') !!}"><i class="fa fa-power-off"></i>&nbsp;&nbsp;Log
                                            Out</a></li> --}}
                                </ul>
                            </li>

                        </ul>
                    </div>
                    <!-- END CHAT TOGGLER -->
                </div>
                <!-- END TOP NAVIGATION MENU -->

            </div>
            <!-- END TOP NAVIGATION BAR -->
        </div>
        <!-- END HEADER -->
        <!-- BEGIN CONTAINER -->
        <div class="page-container row-fluid" style="background: #E5E9EC;">
            @if($app_id!=40)
            <!-- BEGIN SIDEBAR -->
            <div class="page-sidebar" id="main-menu">
                <!-- BEGIN MINI-PROFILE -->
                <div class="page-sidebar-wrapper scrollbar-dynamic" id="main-menu-wrapper">
                    <div class="user-info-wrapper">
                        <div class="user-info">
                            <div class="username">{{--John--}} <span class="semi-bold"> {{--Smith--}}</span></div>
                            <div class="status">
                                {{-- <a href="{!! route('users.logout') !!}">
                                    <div class="status-icon red"></div>
                                    Logout
                                </a> --}}
                            </div>
                        </div>
                    </div>
                    <!-- END MINI-PROFILE -->

                    <!-- BEGIN SIDEBAR MENU -->
                    <p class="menu-title"></p>
                    <div class="clearfix"></div>
                    <!-- END SIDEBAR MENU -->
                </div>
            </div>
            <!-- END SIDEBAR -->
            @endif




            {{-- <a href="#" class="scrollup" style="bottom: 45px;left:225px;">Scroll</a> --}}
            <div class="footer-widget" style="z-index: 100; width:100%;padding:5px;">
                <div class="pull-right hidden">
                    {{-- <a href="{!! route('users.logout') !!}">Logout <i class="fa fa-power-off"></i></a> --}}
                </div>

                <a href="{{ route('eo.rni.create') }}" class="floating-button hide tip @if(Route::currentRouteName() == 'eo.rni.create') hide @endif" title="NEW RNI">
                    <i class="fa fa-plus floating-add"></i>
                </a>

            </div>
            <!-- END scroll -->





            <!-- BEGIN PAGE CONTAINER-->
            <div class="page-content" style="margin-left: 0px !important;">
                <!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
                <div id="portlet-config" class="modal hide">
                    <div class="modal-header">
                        <button data-dismiss="modal" class="close" type="button"></button>
                        <h3>Widget Settings</h3>
                    </div>
                    <div class="modal-body"> Widget settings form goes here</div>
                </div>
                <div class="clearfix"></div>
                <div class="content" id="fullscreen" style="margin-bottom:30px;">

                    <div class="hide" id="content-pre-loader" style="position: absolute; width: 95%; height: 95%; background: #c9c9c9; opacity: 0.6; top: 0px; z-index: 99999;">
                        <div class="text-center" style="margin-top: 35%;">
                            <i class="fa fa-spinner fa-6x fa-spin" aria-hidden="true"></i>
                        </div>
                    </div>

                    @yield('content')

                </div>
            </div>


        </div>
        <!-- END CONTAINER -->

        <!-- END CONTAINER -->
        <!-- BEGIN CORE JS FRAMEWORK-->
        <script src="{!! asset('assets/plugins/boostrapv3/js/bootstrap.min.js') !!}" type="text/javascript"></script>
        <script src="{!! asset('assets/plugins/jquery-ui/jquery-ui-1.10.1.custom.min.js') !!}" type="text/javascript"></script>
        <script src="{!! asset('assets/plugins/breakpoints.js') !!}" type="text/javascript"></script>
        <script src="{!! asset('assets/plugins/jquery-unveil/jquery.unveil.min.js') !!}" type="text/javascript"></script>
        <script src="{!! asset('assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js') !!}" type="text/javascript"></script>
        <!-- END CORE JS FRAMEWORK -->
        <!-- BEGIN PAGE LEVEL JS -->
        <script src="{!! asset('assets/plugins/pace/pace.min.js') !!}" type="text/javascript"></script>
        <script src="{!! asset('assets/plugins/jquery-numberAnimate/jquery.animateNumbers.js') !!}"
        type="text/javascript"></script>
        <!-- END PAGE LEVEL PLUGINS -->


        <script type="text/javascript" src="{!! asset('assets/plugins/bootstrap-select2/select2.min.js')
        !!}"></script>


        <script type="text/javascript" src="{!! asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')
        !!}"></script>


        <script type="text/javascript" src="{!! asset('assets/plugins/boostrap-clockpicker/bootstrap-clockpicker.min.js')
        !!}"></script>


        <script type="text/javascript" src="{!! asset('assets/plugins/jquery-lightbox/js/lightbox.js') !!}"></script>

        <script type="text/javascript" src="{!! asset('assets/plugins/DataTables-1.10.12/media/js/jquery.dataTables.min.js')
        !!}"></script>

        <script type="text/javascript" src="{!! asset('assets/plugins/DataTables-1.10.12/extensions/Buttons/js/dataTables.buttons.js') !!}"></script>
        <script type="text/javascript" src="{!! asset('assets/plugins/DataTables-1.10.12/extensions/Buttons/js/buttons.flash.js') !!}"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
        <script src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
        <script src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
        <script type="text/javascript" src="{!! asset('assets/plugins/DataTables-1.10.12/extensions/Buttons/js/buttons.html5.js') !!}"></script>
        <script type="text/javascript" src="{!! asset('assets/plugins/DataTables-1.10.12/extensions/Buttons/js/buttons.print.js') !!}"></script>


        <script src="https://code.highcharts.com/highcharts.js"></script>
        <script src="https://code.highcharts.com/modules/data.js"></script>
        <script src="https://code.highcharts.com/modules/drilldown.js"></script>

        <script src="{!! asset('assets/js/email_comman.js') !!}" type="text/javascript"></script>
        <!-- BEGIN CORE TEMPLATE JS -->
        <script src="{!! asset('assets/js/notify.min.js') !!}" type="text/javascript"></script>

        <script src="{!! asset('assets/plugins/tipsy/js/jquery.tipsy.js') !!}" type="text/javascript"></script>

        <script src="{!! asset('assets/js/core.js') !!}" type="text/javascript"></script>
        <!-- END CORE TEMPLATE JS -->

        <script type="text/javascript" src="{!! asset('assets/plugins/fullscreen/jquery.fullscreen-min.js') !!}"></script>
        <script type="text/javascript" src="{!! asset('assets/plugins/tooltiper/tooltiper.js') !!}"></script>
        <script type="text/javascript" src="{!! asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js') !!}"></script>
        <script type="text/javascript" src="{!! asset('assets/plugins/hide-seek/demo/javascripts/vendor/jquery.hideseek.min.js') !!}"></script>


        <script type="text/javascript">
            $(document).ready(function () {
                $("#datatable").DataTable();
                $('#datatable100').dataTable({
                    "iDisplayLength": 100,
                });

                // Click on link
                $('body').on('click', '.inner-menu ul.big-items li', function(event){
                    var link = $(this).find('a').attr('href');
                    location.href = link;
                    return false;
                });

                $('.tooltiper').tooltipster({
                    delay: 100,
                    maxWidth: 500,
                    speed: 300,
                    interactive: true,
                    animation: 'grow',
                    trigger: 'click'
                });

            });

            function showPreLoader() {
                $('#content-pre-loader').removeClass('hide')
            }
            function hidePreLoader() {
                $('#content-pre-loader').addClass('hide')
            }

        </script>

        @yield('scripts')
        @stack('scripts_bottom')
    </body>
</html>
