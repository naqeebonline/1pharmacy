@extends('layouts.'.config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('stylesheets')
<style>
    .table> :not(caption)>*>* {
        padding: 5px;
    }
</style>

<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Reports Dashboard -->
        <div class="card">
            <div class="card-header header-elements-inline">
                <div class="text-muted">
                    <i class="fas fa-chart-bar me-2"></i>Reports Dashboard
                </div>
            </div>

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
                        <div class="card border-secondary">
                            <div class="card-header bg-label-secondary">
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-2">
                                        <span class="avatar-initial bg-secondary rounded-circle"><i class="bx bx-dollar-circle fs-4"></i></span>
                                    </div>
                                    <div>
                                        <h6 class="card-title mb-0">Stock Purchase Value</h6>
                                        <small class="text-muted">By generic name or item form</small>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="mb-2 text-muted small">Active GRN batches (<code>ProductStatus = 1</code>) with purchase value per line.</p>
                                <a href="{{ route('reports.stock_purchase_value') }}" class="btn btn-secondary btn-sm">
                                    <i class="bx bx-detail me-1"></i>Open Report
                                </a>
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
        </div>
        <!-- /reports dashboard -->
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('assets/vendor/libs/datatables/jquery.dataTables.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/datatables-responsive/datatables.responsive.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.js') }}"></script>
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
<script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
<script src="{{ asset('assets/js/jquery.form.min.js') }}"></script>
<script>
    // Dashboard scripts can be added here if needed
    $(document).ready(function() {
        // Initialize any dashboard-specific functionality
        console.log('Reports Dashboard loaded successfully');

        loadShortExpiryData();
        loadSaleGrnCompareCount();
    });

    function loadSaleGrnCompareCount() {
        $.ajax({
            url: "{{ route('reports.sale_purchase_price_compare.count') }}",
            type: 'GET',
            success: function (response) {
                if (response.success) {
                    $('#sale-grn-compare-count').text(response.count);
                }
            },
            error: function () {
                $('#sale-grn-compare-count').text('—');
            }
        });
    }

    function loadShortExpiryData() {
        $.ajax({
            url: "{{ route('reports.short_expiry_count') }}",
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    $('#short-expiry-count').text(response.count);
                    $('#expiry-progress').css('width', response.percentage + '%');

                    if (response.count > 0) {
                        $('#expiry-status').html(`<span class="text-warning">${response.count} items expiring in 180 days</span>`);
                    } else {
                        $('#expiry-status').html(`<span class="text-success">No items expiring soon</span>`);
                    }
                }
            },
            error: function() {
                $('#short-expiry-count').text('Error');
                $('#expiry-status').text('Failed to load data');
            }
        });
    }
</script>
@endpush