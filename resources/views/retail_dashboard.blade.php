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
            <div class="col-lg-4 col-md-4 mb-4">
                <label style="color:white;font-weight: bold">From Date</label>
                 <input type="date" class="form-control" id="from_date" value="{{$from_date}}">
            </div>
            <div class="col-lg-4 col-md-4 mb-4">
                <label style="color:white;font-weight: bold">To Date</label>
                <input type="date" class="form-control" id="to_date" value="{{$to_date}}">
            </div>

            <div class="col-lg-4 col-md-4 mb-4">

                <a class="btn btn-primary mt-4" style="color: white; font-weight: bold" href="javascript:void(0)" id="search_dashobard">Search</a>
            </div>
        </div>
        <div class="row">


            <div class="col-lg-12 col-md-12 mt-3">
                <div class="row">
                    <div class="col-lg-4 col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar">
                                            <span class="avatar-initial bg-label-primary rounded-circle"><i class="bx bx-user fs-4"></i></span>
                                        </div>
                                        <div class="card-info">
                                            <h5 class="card-title mb-0 me-2">{{ $data->TotalSale ?? 0 }}</h5>
                                            <small class="text-muted">Total Pharmacy Sale</small>
                                        </div>
                                    </div>
                                    <div id="conversationChart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar">
                                            <span class="avatar-initial bg-label-primary rounded-circle"><i class="bx bx-user fs-4"></i></span>
                                        </div>
                                        <div class="card-info">
                                            <h5 class="card-title mb-0 me-2">{{ ($data->Discount ?? 0) + ($data->invoice_discount ?? 0) }}</h5>
                                            <small class="text-muted">Pharmacy Discount</small>
                                        </div>
                                    </div>
                                    <div id="conversationChart"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar">
                                        <span class="avatar-initial bg-label-warning rounded-circle"><i
                                                    class="bx bx-user fs-4"></i></span>
                                        </div>
                                        <div class="card-info">
                                            <h5 class="card-title mb-0 me-2">{{round($data->received_amount)}}</h5>
                                            <small class="text-muted">Pharmacy Cash in Hand</small>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
            </div>

 

            



        </div>
     @if((getUserRole() == 'Super Admin')) 
        <div class="card-body">
                <div class="row">
                    <div class="col-lg-3 col-md-3 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar">
                                            <span class="avatar-initial bg-label-primary rounded-circle"><i class="bx bx-truck fs-4"></i></span>
                                        </div>
                                        <div class="card-info">
                                            <h5 class="card-title mb-0 me-2">GRN Report</h5>
                                            <small class="text-muted"><a target="_blank" href="{{ route('reports.grn_supplier_report') }}">Supplier Purchase</a></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-3 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar">
                                            <span class="avatar-initial bg-label-success rounded-circle"><i class="bx bx-box fs-4"></i></span>
                                        </div>
                                        <div class="card-info">
                                            <h5 class="card-title mb-0 me-2">Inventory</h5>
                                            <small class="text-muted"><a target="_blank" href="{{ route('reports.inventory_summary') }}">Summary Report</a></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                    <div class="col-lg-3 col-md-3 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar">
                                            <span class="avatar-initial bg-label-info rounded-circle"><i class="bx bx-line-chart fs-4"></i></span>
                                        </div>
                                        <div class="card-info">
                                            <h5 class="card-title mb-0 me-2">Sales Trends</h5>
                                            <small class="text-muted"><a target="_blank" href="{{ route('reports.analytics') }}">View Dashboard</a></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-3 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar">
                                            <span class="avatar-initial bg-label-info rounded-circle"><i class="bx bx-line-chart fs-4"></i></span>
                                        </div>
                                        <div class="card-info">
                                            <h5 class="card-title mb-0 me-2">Sales Statistics</h5>
                                            <small class="text-muted"><a target="_blank" href="{{ route('sale.dashboard') }}">View Dashboard</a></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-3 mb-4">
                        <div class="card border-primary">
                            <div class="card-header bg-label-primary">
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-2">
                                        <span class="avatar-initial bg-primary rounded-circle"><i class="bx bx-bar-chart-alt-2 fs-4"></i></span>
                                    </div>
                                    <div>
                                        <h6 class="card-title mb-0 text-primary">Daily Product Sales</h6>
                                        <small class="text-muted">Comprehensive product analysis</small>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="text-primary mb-1">Product Wise</h4>
                                        <p class="mb-0 text-muted small">Date range sales analysis</p>
                                    </div>
                                    <div>
                                        <a href="{{ route('reports.daily_product_sales') }}" class="btn btn-primary btn-sm">
                                            <i class="bx bx-detail me-1"></i>View Dashboard
                                        </a>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 85%"></div>
                                    </div>
                                    <small class="text-muted">Complete sales insights with filters</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-3 mb-4">
                        <div class="card border-danger">
                            <div class="card-header bg-label-danger">
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-2">
                                        <span class="avatar-initial bg-danger rounded-circle"><i class="bx bx-transfer-alt fs-4"></i></span>
                                    </div>
                                    <div>
                                        <h6 class="card-title mb-0 text-danger">Sale vs GRN Price</h6>
                                        <small class="text-muted">Sale ≤ purchase (active GRN)</small>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="text-danger mb-1" id="sale-grn-compare-count">Loading...</h4>
                                        <p class="mb-0 text-muted small">Products need price review</p>
                                    </div>
                                    <div>
                                        <a href="{{ route('reports.sale_purchase_price_compare') }}" class="btn btn-danger btn-sm">
                                            <i class="bx bx-edit me-1"></i>Review &amp; Update
                                        </a>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted">Bulk update sale prices from active stock batches</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-3 mb-4">
                        <div class="card border-warning">
                            <div class="card-header bg-label-warning">
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-2">
                                        <span class="avatar-initial bg-warning rounded-circle"><i class="bx bx-time fs-4"></i></span>
                                    </div>
                                    <div>
                                        <h6 class="card-title mb-0 text-warning">Short Expiry Alert</h6>
                                        <small class="text-muted">Items expiring soon</small>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="text-warning mb-1" id="short-expiry-count">Loading...</h4>
                                        <p class="mb-0 text-muted small">Products expiring in 180 days</p>
                                    </div>
                                    <div>
                                        <a href="{{ route('reports.short_expiry') }}" class="btn btn-warning btn-sm">
                                            <i class="bx bx-detail me-1"></i>View Report
                                        </a>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" id="expiry-progress"></div>
                                    </div>
                                    <small class="text-muted" id="expiry-status">Checking inventory...</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

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

            <script type="text/javascript">
                $("body").on("click","#search_dashobard",function () {
                   var from_date = $("#from_date").val();
                   var to_date = $("#to_date").val();
                   window.location = "{{route('pos.retailPharmacyDashboard')}}?from_date="+from_date+"&to_date="+to_date
                });
            </script>



@endpush
