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
            <div class="col-lg-6 col-md-6 ">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0" style="width:100%; text-align: center;font-weight: bold">Daily Admissions</h5>

                    </div>
                    <div class="card-body pb-2 ">

                        <div id="dailyAdmissionsChart"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-6 ">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0" style="width:100%; text-align: center;font-weight: bold">Total Procedures</h5>

                    </div>
                    <div class="card-body pb-2 ">

                        <div id="totalProcedures"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-6 mt-3">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0" style="width:100%; text-align: center;font-weight: bold">Monthly Admissions</h5>

                    </div>
                    <div class="card-body pb-2 ">

                        <div id="monthlyAdmissions"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mt-3">
                <div class="card">
                    <div class="card-body">
                        <div id="patients_summary"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mt-3">
                <div class="card">
                    <div class="card-body">
                        <div id="investigation_chart"></div>
                    </div>
                </div>
            </div>



            <div class="col-md-6 mt-3">
                <div class="card">
                    <div class="card-body">
                        <div id="container"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mt-3">
                <div class="card">
                    <div class="card-body">
                        <div id="pharmacy"></div>
                    </div>
                </div>
            </div>


            <div class="col-md-6 col-lg-6 mt-3">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Total Procedures Done</h5>
                    </div>
                    <div class="card-body">
                        <ul class="p-0 m-0">
                            @foreach($surgery as $key => $value)
                                @if($value->type != "-")
                                    <li class="d-flex mb-4 pb-2">
                                        <div class="avatar avatar-sm flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded-circle bg-label-primary"><i class="bx bx-cube"></i></span>
                                        </div>
                                        <div class="d-flex flex-column w-100">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span>{{$value->type}} Cases</span>
                                                <span class="text-muted">{{ $value->total_admissions }}</span>
                                            </div>
                                            <div class="progress" style="height: 6px">
                                                <div class="progress-bar bg-primary" style="width: 40%" role="progressbar"
                                                     aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </li>
                                @endif
                            @endforeach


                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-6 mt-3">
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
                                            <h5 class="card-title mb-0 me-2">{{ $total_patients }}</h5>
                                            <small class="text-muted">Total Patients</small>
                                        </div>
                                    </div>
                                    <div id="conversationChart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar">
                                            <span class="avatar-initial bg-label-primary rounded-circle"><i class="bx bx-user fs-4"></i></span>
                                        </div>
                                        <div class="card-info">
                                            <h5 class="card-title mb-0 me-2">{{ $admitted_patients - 1 }}</h5>
                                            <small class="text-muted">Admitted Patients</small>
                                        </div>
                                    </div>
                                    <div id="conversationChart"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar">
                                        <span class="avatar-initial bg-label-warning rounded-circle"><i
                                                    class="bx bx-user fs-4"></i></span>
                                        </div>
                                        <div class="card-info">
                                            <h5 class="card-title mb-0 me-2">{{$discharged_patients}}</h5>
                                            <small class="text-muted">Discharged Patients</small>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-lg-6 col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar">
                                        <span class="avatar-initial bg-label-warning rounded-circle"><i
                                                    class="bx bx-user fs-4"></i></span>
                                        </div>
                                        <div class="card-info">
                                            <h5 class="card-title mb-0 me-2">{{ $canceled_patients }}</h5>
                                            <small class="text-muted">Cancelled Admissions</small>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar">
                                        <span class="avatar-initial bg-label-warning rounded-circle"><i
                                                    class="bx bx-user fs-4"></i></span>
                                        </div>
                                        <div class="card-info">
                                            <h5 class="card-title mb-0 me-2">{{ $reffered_patients }}</h5>
                                            <small class="text-muted">Referred Patients</small>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar">
                                        <span class="avatar-initial bg-label-warning rounded-circle"><i
                                                    class="bx bx-user fs-4"></i></span>
                                        </div>
                                        <div class="card-info">
                                            <h5 class="card-title mb-0 me-2">{{ $total_nvd_cases }}</h5>
                                            <small class="text-muted">NVD Patients</small>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>




            </div>
            <!-- Activity -->

            <!--/ Activity -->


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
            <script src="{{ asset('js/highcharts.js') }}"></script>

            <script>
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

                var chart_title = @json($investigation_chart_title);
                var chart_Data = @json($investigation_chart_data);
                populateChart('investigation_chart',chart_title,chart_Data);

                setTimeout(function () {

                    populateChartData("dailyAdmissionsChart",@json($daily_admissions['months']),@json($daily_admissions['amount']),"#3A1271");
                    populateChartData("totalProcedures",@json($total_procedures['months']),@json($total_procedures['amount']),"#39da8a");
                    populateChartData("monthlyAdmissions",@json($yearly_admissions['months']),@json($yearly_admissions['amount']),"#39da8a");
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
                                    name: '{{date("Y")}}',
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
                                        return 'Total: ' + val ;
                                    }
                                }
                            }
                        };
                    if (typeof analyticsBarChartEl !== undefined && analyticsBarChartEl !== null) {
                        const analyticsBarChart = new ApexCharts(analyticsBarChartEl, analyticsBarChartConfig);
                        analyticsBarChart.render();
                    }
                }

                function donutChart(id,labels,data,color) {
                    const donutChartEl = document.querySelector('#'+id),
                        donutChartConfig = {
                            chart: {
                                height: 390,
                                fontFamily: 'IBM Plex Sans',
                                type: 'donut'
                            },
                            labels: labels,
                            series: data,
                            colors: ['#FF5733', '#3357FF', '#20c997', '#fdac41', '#9b59b6', '#f1c40f', '#e74c3c', '#2ecc71', '#3498db', '#1abc9c'],
                            stroke: {
                                show: false,
                                curve: 'straight'
                            },
                            dataLabels: {
                                enabled: true,
                                formatter: function (val, opt) {
                                    return parseInt(val) + '';
                                }
                            },
                            legend: {
                                show: true,
                                position: 'bottom',
                                labels: {
                                    colors: ['#FF5733', '#3357FF', '#20c997', '#fdac41', '#9b59b6', '#f1c40f', '#e74c3c', '#2ecc71', '#3498db', '#1abc9c'],
                                    useSeriesColors: false
                                }
                            },
                            plotOptions: {
                                pie: {
                                    donut: {
                                        labels: {
                                            show: true,
                                            name: {
                                                fontSize: '2rem',
                                                color: ['#FF5733', '#3357FF', '#20c997', '#fdac41', '#9b59b6', '#f1c40f', '#e74c3c', '#2ecc71', '#3498db', '#1abc9c']
                                            },
                                            value: {
                                                fontSize: '1.2rem',
                                                color: ['#FF5733', '#3357FF', '#20c997', '#fdac41', '#9b59b6', '#f1c40f', '#e74c3c', '#2ecc71', '#3498db', '#1abc9c'],
                                                fontFamily: 'IBM Plex Sans',
                                                formatter: function (val) {
                                                    return parseInt(val) + '';
                                                }
                                            },
                                            total: {
                                                show: true,
                                                fontSize: '1.5rem',
                                                color: ['#FF5733', '#3357FF', '#20c997', '#fdac41', '#9b59b6', '#f1c40f', '#e74c3c', '#2ecc71', '#3498db', '#1abc9c'],
                                                label: '',
                                                formatter: function (w) {
                                                    return '';
                                                }
                                            }
                                        }
                                    }
                                }
                            },
                            responsive: [
                                {
                                    breakpoint: 992,
                                    options: {
                                        chart: {
                                            height: 380
                                        },
                                        legend: {
                                            position: 'bottom',
                                            labels: {
                                                colors: ['#FF5733', '#3357FF', '#20c997', '#fdac41', '#9b59b6', '#f1c40f', '#e74c3c', '#2ecc71', '#3498db', '#1abc9c'],
                                                useSeriesColors: false
                                            }
                                        }
                                    }
                                },
                                {
                                    breakpoint: 576,
                                    options: {
                                        chart: {
                                            height: 320
                                        },
                                        plotOptions: {
                                            pie: {
                                                donut: {
                                                    labels: {
                                                        show: true,
                                                        name: {
                                                            fontSize: '1.5rem'
                                                        },
                                                        value: {
                                                            fontSize: '1rem'
                                                        },
                                                        total: {
                                                            fontSize: '1.5rem'
                                                        }
                                                    }
                                                }
                                            }
                                        },
                                        legend: {
                                            position: 'bottom',
                                            labels: {
                                                colors: ['#FF5733', '#3357FF', '#20c997', '#fdac41', '#9b59b6', '#f1c40f', '#e74c3c', '#2ecc71', '#3498db', '#1abc9c'],
                                                useSeriesColors: false
                                            }
                                        }
                                    }
                                },
                                {
                                    breakpoint: 420,
                                    options: {
                                        chart: {
                                            height: 280
                                        },
                                        legend: {
                                            show: false
                                        }
                                    }
                                },
                                {
                                    breakpoint: 360,
                                    options: {
                                        chart: {
                                            height: 250
                                        },
                                        legend: {
                                            show: false
                                        }
                                    }
                                }
                            ]
                        };
                    if (typeof donutChartEl !== undefined && donutChartEl !== null) {
                        const donutChart = new ApexCharts(donutChartEl, donutChartConfig);
                        donutChart.render();
                    }
                }

                function populateChart(chart_id,categories_name,category_data){
                    console.log(categories_name);
                    console.log(category_data);
                    Highcharts.chart(chart_id, {
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: 'Investigations Done So For'
                        },
                        xAxis: {
                            //categories: ['Total Purchases','Utilize Pharmacy','Available Pharmacy '],
                            categories: categories_name,
                            labels: {
                                enabled: false // Disable category labels
                            },
                            title: {
                                text: ''
                            }
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: 'Investigations Cost',
                                align: 'high'
                            },
                            labels: {
                                overflow: 'justify'
                            }
                        },
                        tooltip: {
                            valuePrefix: 'PKR: ',
                        },
                        plotOptions: {
                            bar: {
                                dataLabels: {
                                    enabled: true
                                }
                            }
                        },
                        series: [{
                            name: '',
                            //data: [10,20,30,40......],
                            data: category_data,
                            colorByPoint: true, // Ensures each column gets a different color
                            colors: ['#FF5733', '#3357FF', '#20c997', '#fdac41', '#9b59b6', '#f1c40f', '#e74c3c', '#2ecc71', '#3498db', '#1abc9c'] // Custom colors for each column
                        }]
                    });
                }//investigation_chart_data




                Highcharts.chart('pharmacy', {
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: 'Pharmacy Statistics'
                    },
                    xAxis: {
                        categories: ['Total Purchases','Utilize Pharmacy','Available Pharmacy '],
                        title: {
                            text: 'PKR'
                        }
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Pharmacy Stock',
                            align: 'high'
                        },
                        labels: {
                            overflow: 'justify'
                        }
                    },
                    tooltip: {
                        valuePrefix: 'PKR: ',
                    },
                    plotOptions: {
                        bar: {
                            dataLabels: {
                                enabled: true
                            }
                        }
                    },
                    series: [{
                        name: '',
                        data: [{{$total_purchased_stock_amount}},{{$total_utilized}},{{$avaliable_stock_amount}}],
                        colorByPoint: true, // Ensures each column gets a different color
                        colors: ['#FF5733','#3357FF', '#20c997', '#fdac41'] // Custom colors for each column
                    }]
                });

                Highcharts.chart('patients_summary', {
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: 'Patient Summary'
                    },
                    xAxis: {
                        categories: ['Total Patients','Discharge Patients','Admitted Patients', 'Canceled Patients', 'Referred Patients'],
                        title: {
                            text: '-'
                        }
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Total Patients',
                            align: 'high'
                        },
                        labels: {
                            overflow: 'justify'
                        }
                    },
                    tooltip: {
                        valuePrefix: '',
                    },
                    plotOptions: {
                        bar: {
                            dataLabels: {
                                enabled: true
                            }
                        }
                    },
                    series: [{
                        name: '',
                        data: [{{$total_patients}},{{$discharged_patients}},{{$admitted_patients - 1}}, {{$canceled_patients}}, {{$reffered_patients}}],
                        colorByPoint: true, // Ensures each column gets a different color
                        colors: ['#FF5733','#3357FF', '#20c997', '#fdac41'] // Custom colors for each column
                    }]
                });


                Highcharts.chart('container', {
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: 'Total Cost on Patients'
                    },
                    xAxis: {
                        categories: ['Procedure Cost','Net Cost', 'Investigation Cost', 'Consultant Shares','Medicine Cost'],
                        title: {
                            text: '-'
                        }
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Total Amount',
                            align: 'high'
                        },
                        labels: {
                            overflow: 'justify'
                        }
                    },
                    tooltip: {
                        valuePrefix: 'PKR'
                    },
                    plotOptions: {
                        bar: {
                            dataLabels: {
                                enabled: true
                            }
                        }
                    },
                    series: [{
                        name: '',
                        data: [{{$total_procedure_amount}},{{$net_cost_on_patient}}, {{$total_investigation_amount}}, {{$total_percentage_paid_to_consultant}},{{$total_medicine_amount}}],
                        colorByPoint: true, // Ensures each column gets a different color
                        colors: ['#ff5b5c', '#20c997', '#3357FF','#FF5733', '#fdac41'] // Custom colors for each column
                    }]
                });
            </script>

    <script type="text/javascript">


    </script>
@endpush
