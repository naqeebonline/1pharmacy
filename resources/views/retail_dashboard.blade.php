@extends('layouts.'.config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@section('content')

    <style>
        .retail-dashboard {
            --rd-card-radius: 12px;
            --rd-card-shadow: 0 2px 12px rgba(67, 89, 113, 0.08);
            --rd-card-shadow-hover: 0 8px 24px rgba(67, 89, 113, 0.14);
            --rd-gap: 1.25rem;
        }

        .retail-dashboard .dashboard-filter-card {
            border: 0;
            border-radius: var(--rd-card-radius);
            box-shadow: var(--rd-card-shadow);
            margin-bottom: var(--rd-gap);
        }

        .retail-dashboard .dashboard-filter-card .card-body {
            padding: 1.25rem 1.5rem;
        }

        .retail-dashboard .dashboard-filter-card label {
            color: #566a7f !important;
            font-weight: 600;
            font-size: 0.8125rem;
            margin-bottom: 0.35rem;
        }

        .retail-dashboard .dashboard-filter-card .btn-primary {
            min-height: 38px;
            font-weight: 600;
        }

        .retail-dashboard .dashboard-stat-card,
        .retail-dashboard .dashboard-link-card,
        .retail-dashboard .dashboard-feature-card {
            height: 100%;
            border: 0;
            border-radius: var(--rd-card-radius);
            box-shadow: var(--rd-card-shadow);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            overflow: hidden;
        }

        .retail-dashboard .dashboard-stat-card:hover,
        .retail-dashboard .dashboard-link-card:hover,
        .retail-dashboard .dashboard-feature-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--rd-card-shadow-hover);
        }

        .retail-dashboard .dashboard-stat-card .card-body,
        .retail-dashboard .dashboard-link-card .card-body {
            padding: 1.35rem 1.5rem;
            height: 100%;
            display: flex;
            align-items: center;
        }

        .retail-dashboard .dashboard-stat-card .card-info h5 {
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1.2;
            color: #384551;
        }

        .retail-dashboard .dashboard-stat-card .card-info small,
        .retail-dashboard .dashboard-link-card .card-info small {
            font-size: 0.8125rem;
            color: #697a8d;
        }

        .retail-dashboard .dashboard-link-card .card-title {
            font-size: 1rem;
            font-weight: 600;
            color: #384551;
        }

        .retail-dashboard .dashboard-link-card a {
            font-weight: 500;
            text-decoration: none;
        }

        .retail-dashboard .dashboard-link-card a:hover {
            text-decoration: underline;
        }

        .retail-dashboard .dashboard-feature-card {
            display: flex;
            flex-direction: column;
        }

        .retail-dashboard .dashboard-feature-card .card-header {
            border-bottom: 1px solid rgba(67, 89, 113, 0.08);
            padding: 1rem 1.25rem;
        }

        .retail-dashboard .dashboard-feature-card .card-body {
            padding: 1.25rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .retail-dashboard .dashboard-feature-card .card-body > .d-flex {
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .retail-dashboard .dashboard-feature-card h4 {
            font-size: 1.35rem;
            font-weight: 700;
        }

        .retail-dashboard .dashboard-feature-card .btn-sm {
            white-space: nowrap;
            font-weight: 600;
        }

        .retail-dashboard .avatar {
            flex-shrink: 0;
        }

        .retail-dashboard .dashboard-section {
            margin-bottom: 0.5rem;
        }

        @media (max-width: 575.98px) {
            .retail-dashboard .dashboard-filter-card .btn-primary {
                width: 100%;
                margin-top: 0.25rem;
            }

            .retail-dashboard .dashboard-stat-card .card-info h5 {
                font-size: 1.25rem;
            }

            .retail-dashboard .dashboard-feature-card .card-body > .d-flex {
                flex-direction: column;
                align-items: flex-start !important;
            }

            .retail-dashboard .dashboard-feature-card .btn-sm {
                width: 100%;
            }
        }

        @media (min-width: 576px) and (max-width: 991.98px) {
            .retail-dashboard .dashboard-filter-card .btn-primary {
                width: 100%;
            }
        }
    </style>

    <!-- Content -->

    <div class="container-xxl flex-grow-1 retail-dashboard px-3 px-md-4">
        <div class="card dashboard-filter-card">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-sm-6 col-lg-4">
                        <label for="from_date">From Date</label>
                        <input type="date" class="form-control" id="from_date" value="{{$from_date}}">
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4">
                        <label for="to_date">To Date</label>
                        <input type="date" class="form-control" id="to_date" value="{{$to_date}}">
                    </div>
                    <div class="col-12 col-lg-4">
                        <a class="btn btn-primary w-100 w-lg-auto" href="javascript:void(0)" id="search_dashobard">Search</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 g-lg-4 dashboard-section">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card dashboard-stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar">
                                <span class="avatar-initial bg-label-primary rounded-circle"><i class="bx bx-user fs-4"></i></span>
                            </div>
                            <div class="card-info">
                                <h5 class="card-title mb-1">{{ $data->TotalSale ?? 0 }}</h5>
                                <small class="text-muted">Total Pharmacy Sale</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card dashboard-stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar">
                                <span class="avatar-initial bg-label-primary rounded-circle"><i class="bx bx-user fs-4"></i></span>
                            </div>
                            <div class="card-info">
                                <h5 class="card-title mb-1">{{ ($data->Discount ?? 0) + ($data->invoice_discount ?? 0) }}</h5>
                                <small class="text-muted">Pharmacy Discount</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card dashboard-stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar">
                                <span class="avatar-initial bg-label-warning rounded-circle"><i class="bx bx-user fs-4"></i></span>
                            </div>
                            <div class="card-info">
                                <h5 class="card-title mb-1">{{ round($data->received_amount) }}</h5>
                                <small class="text-muted">Pharmacy Cash in Hand</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if((getUserRole() == 'Super Admin'))
        <div class="row g-3 g-lg-4 dashboard-section">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card dashboard-link-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar">
                                <span class="avatar-initial bg-label-success rounded-circle"><i class="bx bx-box fs-4"></i></span>
                            </div>
                            <div class="card-info">
                                <h5 class="card-title mb-1">Inventory</h5>
                                <small class="text-muted"><a target="_blank" href="{{ route('reports.inventory_summary') }}">Summary Report</a></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card dashboard-link-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar">
                                <span class="avatar-initial bg-label-info rounded-circle"><i class="bx bx-line-chart fs-4"></i></span>
                            </div>
                            <div class="card-info">
                                <h5 class="card-title mb-1">Sales Trends</h5>
                                <small class="text-muted"><a target="_blank" href="{{ route('reports.analytics') }}">View Dashboard</a></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card dashboard-link-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar">
                                <span class="avatar-initial bg-label-info rounded-circle"><i class="bx bx-line-chart fs-4"></i></span>
                            </div>
                            <div class="card-info">
                                <h5 class="card-title mb-1">Sales Statistics</h5>
                                <small class="text-muted"><a target="_blank" href="{{ route('sale.dashboard') }}">View Dashboard</a></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card dashboard-feature-card border-primary h-100">
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

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card dashboard-feature-card border-danger h-100">
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

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card dashboard-feature-card border-warning h-100">
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
