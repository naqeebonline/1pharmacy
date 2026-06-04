@extends('layouts.'.config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@section('content')

    <style>
        .tags {
            list-style: none;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px 0;
            width: 95%;
            margin: 0 auto;

        }

        .tags li {
            padding: 0 20px;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .tags li.warning:after {
            background-color: orange;
        }

        .tags li.success:after {
            background-color: green;
        }

        .tags li.danger:after {
            background-color: red;
        }

        .tags li:after {
            content: '';
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 0px;
            width: 10px;
            border-radius: 10px;
            height: 10px;
        }

        .text_height_map{
            line-height: 18px;
        }



    </style>

    <!-- Content -->

    <div class="container-xxl flex-grow-1 px-0">
        <div class="row">
            <!-- Activity -->
            <div class="col-md-6 col-lg-6  mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Activity</h5>
                    </div>
                    <div class="card-body">
                        <ul class="p-0 m-0">
                            <li class="d-flex mb-4 pb-2">
                                <div class="avatar avatar-sm flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded-circle bg-label-primary"><i class="bx bx-cube"></i></span>
                                </div>
                                <div class="d-flex flex-column w-100">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Total Sales</span>
                                        <span class="text-muted">£{{ $totalSale }}</span>
                                    </div>
                                    <div class="progress" style="height: 6px">
                                        <div class="progress-bar bg-primary" style="width: 40%" role="progressbar"
                                             aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </li>
                            <li class="d-flex mb-4 pb-2">
                                <div class="avatar avatar-sm flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded-circle bg-label-success"><i class="bx bx-dollar"></i></span>
                                </div>
                                <div class="d-flex flex-column w-100">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Purchase</span>
                                        <span class="text-muted">£{{ $totalPurchase }}</span>
                                    </div>
                                    <div class="progress" style="height: 6px">
                                        <div class="progress-bar bg-success" style="width: 80%" role="progressbar"
                                             aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </li>
                            <li class="d-flex mb-4 pb-2">
                                <div class="avatar avatar-sm flex-shrink-0 me-3">
                                        <span class="avatar-initial rounded-circle bg-label-warning"><i
                                                    class="bx bx-trending-up"></i></span>
                                </div>
                                <div class="d-flex flex-column w-100">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Expense</span>
                                        <span class="text-muted">£{{ $expense }}</span>
                                    </div>
                                    <div class="progress" style="height: 6px">
                                        <div class="progress-bar bg-warning" style="width: 80%" role="progressbar"
                                             aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>
            <!--/ Activity -->

            <div class="col-lg-6 col-md-12">
                <div class="row">
                    <div class="col-lg-6 col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar">
                                            <span class="avatar-initial bg-label-primary rounded-circle"><i class="bx bx-user fs-4"></i></span>
                                        </div>
                                        <div class="card-info">
                                            <h5 class="card-title mb-0 me-2">{{ $todaySale }}</h5>
                                            <small class="text-muted">Today Sale</small>
                                        </div>
                                    </div>
                                    <div id="conversationChart"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{--<div class="col-lg-6 col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar">
                                        <span class="avatar-initial bg-label-warning rounded-circle"><i
                                                    class="bx bx-dollar fs-4"></i></span>
                                        </div>
                                        <div class="card-info">
                                            <h5 class="card-title mb-0 me-2">£{{$cashInHand}}</h5>
                                            <small class="text-muted">Cash by Hand</small>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>--}}


                    {{--<div class="col-lg-6 col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar">
                                        <span class="avatar-initial bg-label-warning rounded-circle"><i
                                                    class="bx bx-dollar fs-4"></i></span>
                                        </div>
                                        <div class="card-info">
                                            <h5 class="card-title mb-0 me-2">£{{ $cashByBank }}</h5>
                                            <small class="text-muted">Cash by Bank</small>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>--}}

                    <div class="col-lg-6 col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar">
                                        <span class="avatar-initial bg-label-warning rounded-circle"><i
                                                    class="bx bx-dollar fs-4"></i></span>
                                        </div>
                                        <div class="card-info">
                                            <h5 class="card-title mb-0 me-2">{{ $todayPurchase }}</h5>
                                            <small class="text-muted">Today Purchases</small>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    {{--<div class="col-lg-6 col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar">
                                        <span class="avatar-initial bg-label-warning rounded-circle"><i
                                                    class="bx bx-dollar fs-4"></i></span>
                                        </div>
                                        <div class="card-info">
                                            <h5 class="card-title mb-0 me-2">£{{ $supplierPayment }}</h5>
                                            <small class="text-muted">Supplier Payments</small>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>--}}

                    {{--<div class="col-lg-6 col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar">
                                        <span class="avatar-initial bg-label-warning rounded-circle"><i
                                                    class="bx bx-dollar fs-4"></i></span>
                                        </div>
                                        <div class="card-info">
                                            <h5 class="card-title mb-0 me-2">£{{$totalReceivables}}</h5>
                                            <small class="text-muted">Total Receivables</small>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>--}}


                </div>

            </div>


            <div class="col-lg-12 col-md-12 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Daily Sale</h5>

                    </div>
                    <div class="card-body pb-2">

                        <div id="dailySaleBarChart"></div>
                    </div>
                </div>
            </div>

            <!-- Website Analytics-->
            <div class="col-lg-6 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Monthly Sale</h5>

                    </div>
                    <div class="card-body pb-2">

                        <div id="saleBarChart"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Monthly Purchase</h5>

                    </div>
                    <div class="card-body pb-2">

                        <div id="purchaseBarChart"></div>
                    </div>
                </div>
            </div>




        </div>
    </div>
    <!-- / Content -->


@endsection


@push('scripts')



@endpush

@push('vendor-style')

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
@endpush

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/js/app-custom.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script src="{{ asset('assets/js/dashboards-analytics.js') }}"></script>


    <script type="text/javascript">

        let configs = {
            colors: {
                primary: '#5a8dee',
                secondary: '#69809a',
                success: '#39da8a',
                info: '#00cfdd',
                warning: '#fdac41',
                danger: '#ff5b5c',
                dark: '#495563',
                black: '#000',
                white: '#fff',
                cardColor: '#fff',
                bodyBg: '#f2f2f6',
                bodyColor: '#677788',
                headingColor: '#516377',
                textMuted: '#a8b1bb',
                borderColor: '#e9ecee'
            },
            colors_label: {
                primary: '#5a8dee29',
                secondary: '#69809a29',
                success: '#39da8a29',
                info: '#00cfdd29',
                warning: '#fdac4129',
                danger: '#ff5b5c29',
                dark: '#49556329'
            },
            colors_dark: {
                cardColor: '#283144',
                bodyBg: '#1c222f',
                bodyColor: '#a1b0cb',
                headingColor: '#d8deea',
                textMuted: '#8295ba',
                borderColor: '#36445d'
            },
            enableMenuLocalStorage: true // Enable menu state with local storage support
        };
        cardColor = configs.colors.white;
        headingColor = configs.colors.headingColor;
        axisColor = configs.colors.axisColor;
        borderColor = configs.colors.borderColor;
        shadeColor = 'light';


        setTimeout(function () {
            populateChartData("saleBarChart",@json($monthly_saleData['months']),@json($monthly_saleData['amount']),"#5a8dee");
            populateChartData("purchaseBarChart",@json($monthly_purchaseData['months']),@json($monthly_purchaseData['amount']),'#39da8a');
            populateChartData("dailySaleBarChart",@json($daily_saleData['months']),@json($daily_saleData['amount']),"#3A1271");
        },1000);

        function populateChartData(id,months,data,color) {
            const analyticsBarChartEl = document.querySelector('#'+id),
                analyticsBarChartConfig = {
                    chart: {
                        height: 250,
                        type: 'bar',
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '20%',
                            borderRadius: 3,
                            startingShape: 'rounded'
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    colors: [color, color],
                    series: [
                        {
                            name: '2024',
                            //data: [1800, 9, 15, 20, 14, 22, 29, 27, 13]
                            data: data
                        }
                    ],
                    grid: {
                        borderColor: borderColor,
                        padding: {
                            bottom: -8
                        }
                    },
                    xaxis: {
                        categories: months,
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        },
                        labels: {
                            style: {
                                colors: color
                            }
                        }
                    },
                    yaxis: {
                        min: 0,

                        tickAmount: 3,
                        labels: {
                            style: {
                                colors: axisColor
                            }
                        }
                    },
                    legend: {
                        show: false
                    },
                    tooltip: {
                        y: {
                            formatter: function (val) {
                                return 'PKR ' + val ;
                            }
                        }
                    }
                };
            if (typeof analyticsBarChartEl !== undefined && analyticsBarChartEl !== null) {
                const analyticsBarChart = new ApexCharts(analyticsBarChartEl, analyticsBarChartConfig);
                analyticsBarChart.render();
            }
        }
    </script>
@endpush
