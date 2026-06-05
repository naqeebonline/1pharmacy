@extends('layouts.'.config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('stylesheets')
<style>
    .table> :not(caption)>*>* {
        padding: 8px;
    }

    .select2-container--default .select2-selection--single {
        min-height: 32px !important;
        height: 32px !important;
        padding: 0 8px !important;
        font-size: 0.9rem !important;
        border-radius: 0.375rem !important;
        display: flex !important;
        align-items: center !important;
        border: 1px solid #d1d5db !important;
    }

    .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 30px !important;
        padding-left: 0 !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 32px !important;
    }

    /* Status badges */
    .status-expired {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-expiring {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-warning {
        background: linear-gradient(135deg, #eab308 0%, #ca8a04 100%);
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-good {
        background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    /* Summary cards */
    .summary-card {
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        transition: transform 0.2s ease;
    }

    .summary-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
    }

    .stats-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    /* Filter section */
    .filter-section {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
        border: 1px solid #e2e8f0;
    }

    /* Data table styling */
    .expiry-table {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .expiry-table thead th {
        background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
        color: white;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 12px 8px;
        border: none;
    }

    .expiry-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .days-left {
        font-weight: 600;
        font-size: 0.9rem;
    }

    .expired {
        color: #dc2626;
    }

    .expiring-soon {
        color: #f59e0b;
    }

    .expiring-warning {
        color: #eab308;
    }

    .expiring-good {
        color: #16a34a;
    }

    .table:not(.table-dark) thead:not(.table-dark) th {
    color: #ffffff !important;
}

    /* Print styles */
    @media print {
        .no-print {
            display: none !important;
        }

        .summary-card {
            box-shadow: none;
            border: 1px solid #ddd;
        }

        .expiry-table {
            box-shadow: none;
        }
    }
</style>

<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0"><i class="bx bx-time me-2 text-warning"></i>Short Expiry Report</h4>
                    <p class="text-muted mb-0">Monitor products approaching expiry dates</p>
                </div>
                <div class="no-print">
                    <button class="btn btn-primary me-2" onclick="window.print()">
                        <i class="bx bx-printer me-1"></i>Print Report
                    </button>
                    <button class="btn btn-success" id="exportExcel">
                        <i class="bx bx-download me-1"></i>Export Excel
                    </button>
                </div>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card summary-card border-danger">
                    <div class="card-body d-flex align-items-center">
                        <div class="stats-icon bg-danger text-white me-3">
                            <i class="bx bx-x-circle"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 text-danger" id="expired-count">0</h3>
                            <p class="mb-0 text-muted small">Expired Items</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card summary-card border-warning">
                    <div class="card-body d-flex align-items-center">
                        <div class="stats-icon bg-warning text-white me-3">
                            <i class="bx bx-error-circle"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 text-warning" id="expiring-7-count">0</h3>
                            <p class="mb-0 text-muted small">Expiring in 7 days</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card summary-card border-info">
                    <div class="card-body d-flex align-items-center">
                        <div class="stats-icon bg-info text-white me-3">
                            <i class="bx bx-time-five"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 text-info" id="expiring-30-count">0</h3>
                            <p class="mb-0 text-muted small">Expiring in 30 days</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card summary-card border-primary">
                    <div class="card-body d-flex align-items-center">
                        <div class="stats-icon bg-primary text-white me-3">
                            <i class="bx bx-package"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 text-primary" id="total-count">0</h3>
                            <p class="mb-0 text-muted small">Total Active Items</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filter-section no-print">
            <h6 class="mb-3"><i class="bx bx-filter me-2"></i>Filter Options</h6>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Days Range</label>
                    <select id="days-filter" class="form-select">
                        <option value="all">All Items</option>
                        <option value="expired">Expired Only</option>
                        <option value="7">Expiring in 7 days</option>
                        <option value="15">Expiring in 15 days</option>
                        <option value="30" >Expiring in 30 days</option>
                        <option value="60">Expiring in 60 days</option>
                        <option value="90">Expiring in 90 days</option>
                        <option value="180" selected>Expiring in 180 days</option>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Supplier</label>
                    <select id="supplier-filter" class="form-select select2">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->supplier_id }}">{{ $supplier->supplier_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Product Category</label>
                    <select id="category-filter" class="form-select select2">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Actions</label>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary flex-fill" id="apply-filters">
                            <i class="bx bx-search me-1"></i>Apply
                        </button>
                        <button class="btn btn-outline-secondary" id="reset-filters">
                            <i class="bx bx-reset me-1"></i>Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="short-expiry-table" class="table table-striped expiry-table">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Product Name</th>
                                <th>Batch Number</th>
                                <th>Supplier</th>
                                <th>Category</th>
                                <th>Expiry Date</th>
                                <th>Days Left</th>
                                <th>Current Stock</th>
                                <th>Purchase Price</th>
                                <th>Sale Price</th>
                                <th>Total Value</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/vendor/libs/datatables/jquery.dataTables.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/datatables-responsive/datatables.responsive.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
<script src="{{ asset('assets/js/jquery.form.min.js') }}"></script>

<script>
    let shortExpiryTable;

    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            width: '100%'
        });

        // Initialize DataTable
        initializeDataTable();

        // Load summary statistics
        loadSummaryStats();

        // Event handlers
        $('#apply-filters').click(function() {
            shortExpiryTable.ajax.reload();
            loadSummaryStats();
        });

        $('#reset-filters').click(function() {
            $('#days-filter').val('30');
            $('#supplier-filter').val('').trigger('change');
            $('#category-filter').val('').trigger('change');
            shortExpiryTable.ajax.reload();
            loadSummaryStats();
        });

        $('#exportExcel').click(function() {
            exportToExcel();
        });
    });

    function initializeDataTable() {
        shortExpiryTable = $('#short-expiry-table').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            order: [
                [6, 'asc']
            ], // Order by days left (ascending)
            ajax: {
                url: "{{ route('reports.short_expiry_data') }}",
                data: function(d) {
                    d.days_filter = $('#days-filter').val();
                    d.supplier_filter = $('#supplier-filter').val();
                    d.category_filter = $('#category-filter').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                    width: '5%'
                },
                {
                    data: 'product_name',
                    name: 'p.name',
                    width: '20%'
                },
                {
                    data: 'batch_number',
                    name: 'gd.batch_number',
                    width: '10%'
                },
                {
                    data: 'supplier_name',
                    name: 's.name',
                    width: '12%'
                },
                {
                    data: 'category_name',
                    name: 'c.name',
                    width: '10%'
                },
                {
                    data: 'expiry_date',
                    name: 'gd.expiry_date',
                    width: '10%'
                },
                {
                    data: 'days_left',
                    name: 'days_left',
                    orderable: true,
                    width: '8%',
                    render: function(data, type, row) {
                        let className = '';
                        if (data < 0) className = 'expired';
                        else if (data <= 7) className = 'expiring-soon';
                        else if (data <= 30) className = 'expiring-warning';
                        else className = 'expiring-good';

                        return `<span class="days-left ${className}">${data}</span>`;
                    }
                },
                {
                    data: 'current_stock',
                    name: 'gd.current_stock',
                    width: '8%'
                },
                {
                    data: 'purchase_price',
                    name: 'gd.purchase_price',
                    width: '8%'
                },
                {
                    data: 'sale_price',
                    name: 'gd.sale_price',
                    width: '8%'
                },
                {
                    data: 'total_value',
                    name: 'total_value',
                    width: '10%'
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: false,
                    width: '8%'
                }
            ],
            responsive: true,
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'excel',
                    text: '<i class="bx bx-download me-1"></i>Excel',
                    className: 'btn btn-success btn-sm'
                },
                {
                    extend: 'pdf',
                    text: '<i class="bx bx-file-pdf me-1"></i>PDF',
                    className: 'btn btn-danger btn-sm'
                },
                {
                    extend: 'print',
                    text: '<i class="bx bx-printer me-1"></i>Print',
                    className: 'btn btn-info btn-sm'
                }
            ],
            language: {
                processing: "Loading expiry data...",
                emptyTable: "No products found matching the selected criteria",
                zeroRecords: "No matching records found"
            }
        });
    }

    function loadSummaryStats() {
        $.ajax({
            url: "{{ route('reports.short_expiry_stats') }}",
            type: 'GET',
            data: {
                days_filter: $('#days-filter').val(),
                supplier_filter: $('#supplier-filter').val(),
                category_filter: $('#category-filter').val()
            },
            success: function(response) {
                if (response.success) {
                    $('#expired-count').text(response.stats.expired || 0);
                    $('#expiring-7-count').text(response.stats.expiring_7 || 0);
                    $('#expiring-30-count').text(response.stats.expiring_30 || 0);
                    $('#total-count').text(response.stats.total || 0);
                }
            },
            error: function() {
                console.error('Failed to load summary statistics');
            }
        });
    }

    function exportToExcel() {
        let params = new URLSearchParams({
            days_filter: $('#days-filter').val(),
            supplier_filter: $('#supplier-filter').val(),
            category_filter: $('#category-filter').val(),
            export: 'excel'
        });

        window.open("{{ route('reports.short_expiry_export') }}?" + params.toString());
    }
</script>
@endpush