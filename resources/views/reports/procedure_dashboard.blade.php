@extends('layouts.' . config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp
@push('stylesheets')
<style>
    /* Custom card styles for better appearance */
    .card {
        transition: all 0.3s ease;
        border: 1px solid rgba(0, 0, 0, 0.125);
        box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.12);
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px 0 rgba(67, 89, 113, 0.16);
    }

    .avatar-initial {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
    }

    .card-title {
        font-size: 0.9rem;
        font-weight: 600;
        color: #566a7f;
    }

    .filter-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }


    .chart-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }

    .chart-area {
        min-height: 340px;
    }

    .table-responsive {
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }

    /* Bootstrap button enhancements */
    .btn {
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
    }

    .btn-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
    }

    .dashboard-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 30px;
        text-align: center;
    }

    .dashboard-header h1 {
        margin: 0;
        font-size: 2.5rem;
        font-weight: 300;
    }

    .dashboard-header p {
        margin: 10px 0 0 0;
        opacity: 0.9;
    }

    .loading {
        opacity: 0.6;
        pointer-events: none;
    }

    .loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 20px;
        height: 20px;
        margin: -10px 0 0 -10px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #667eea;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    #alert-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        max-width: 400px;
    }

    .form-control form-control-sm {
        border-radius: 10px;
        border: 2px solid #e9ecef;
        padding: 5px 10px;
    }


    .form-control form-control-sm:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .form-select {
        display: block;
        width: 100%;
        padding: 0.469rem 1.735rem 0.469rem 0.735rem;
        -moz-padding-start: calc(0.735rem - 3px);
        font-size: 0.9375rem;
        font-weight: 400;
        line-height: 0.9;
    }

    .form-select form-select -sm {
        border-radius: 10px;
        border: 2px solid #e9ecef;
        padding: 5px 10px;
    }

    .form-select form-select -sm:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    /* Mobile Responsive Styles */
    @media (max-width: 768px) {
        .container-fluid {
            padding: 10px;
        }

        .filter-card,
        .chart-card {
            padding: 15px;
        }

        .card-title {
            font-size: 1.25rem !important;
        }

        .dashboard-header {
            padding: 20px;
            text-align: center;
        }

        .dashboard-header h1 {
            font-size: 1.8rem;
        }

        .btn {
            width: 100%;
            margin-bottom: 10px;
        }

        .table-responsive {
            font-size: 0.85rem;
        }

        .chart-area {
            min-height: 260px;
        }

        .col-lg-3.col-md-3 {
            flex: 0 0 100%;
            max-width: 100%;
            margin-bottom: 1rem;
        }
    }

    @media (max-width: 576px) {
        .card-title {
            font-size: 1.1rem !important;
        }

        .avatar-initial {
            width: 35px;
            height: 35px;
        }

        .avatar-initial i {
            font-size: 1rem !important;
        }

        .chart-area {
            min-height: 220px;
        }
    }

    /* Modal fix styles */
    .modal-backdrop {
        z-index: 1040 !important;
    }

    .modal {
        z-index: 1050 !important;
    }

    /* Ensure body doesn't get stuck with modal styles */
    body.modal-open {
        overflow: hidden !important;
        padding-right: 0 !important;
    }

    /* Prevent multiple backdrop issues */
    .modal-backdrop.show {
        opacity: 0.5;
    }

    /* Prevent page hanging after modal close */
    body:not(.modal-open) {
        overflow: auto !important;
        padding-right: 0 !important;
    }

    html {
        overflow: auto !important;
    }

    /* Force cleanup of modal states */
    .modal-open .modal {
        overflow-x: hidden;
        overflow-y: auto;
    }

    /* Print Styles */
    @media print {
        @page {
            margin: 0.5in;
            size: A4 landscape;
        }

        body {
            font-size: 12px;
            line-height: 1.3;
            color: #000 !important;
            background: white !important;
        }

        .no-print,
        .filter-card,
        .chart-card,
        #stats-container,
        #additional-stats-container,
        .card-header,
        .btn,
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate,
        .dataTables_wrapper .dataTables_processing {
            display: none !important;
        }

        .print-header {
            display: block !important;
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .print-filters {
            display: block !important;
            background: #f8f9fa;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .print-table {
            width: 100% !important;
            border-collapse: collapse;
            font-size: 10px;
        }

        .print-table th,
        .print-table td {
            border: 1px solid #000 !important;
            padding: 4px !important;
            text-align: left;
            vertical-align: top;
        }

        .print-table th {
            background-color: #e9ecef !important;
            font-weight: bold;
            color: #000 !important;
        }

        .badge {
            background: none !important;
            color: #000 !important;
            border: 1px solid #000 !important;
            padding: 2px 4px !important;
            font-size: 9px !important;
        }

        .container-fluid {
            padding: 0 !important;
        }

        .card {
            border: none !important;
            box-shadow: none !important;
        }

        .card-body {
            padding: 0 !important;
        }

        /* Force table to be visible in print */
        #procedures-table_wrapper {
            display: block !important;
        }

        #procedures-table {
            display: table !important;
        }
    }

    .print-header {
        display: none;
    }

    .print-filters {
        display: none;
    }
</style>
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
<script id="apexcharts-local" src="{{ asset('assets/vendors/js/charts/apexcharts.js') }}"></script>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Alert Container -->
    <div id="alert-container"></div>


    <!-- Filters Section -->
    <div class="filter-card">
        <h5 class="mb-4"><i class="tf-icons bx bx-filter"></i> Filters & Controls</h5>
        <form id="filter-form">
            <div class="row">
                <div class="col-md-2 mb-3">
                    <label class="form-label">From Date</label>
                    <input type="date" class="form-control form-control-sm" id="from_date" name="from_date" value="{{ date('Y-m-01') }}">
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">To Date</label>
                    <input type="date" class="form-control form-control-sm" id="to_date" name="to_date" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Consultant</label>
                    <select class="form-select form-select -sm" id="consultant_id" name="consultant_id">
                        <option value="">All Consultants</option>
                        @foreach($consultants as $consultant)
                        <option value="{{ $consultant->id }}">{{ $consultant->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Procedure Type</label>
                    <select class="form-select form-select -sm" id="procedure_type" name="procedure_type">
                        <option value="">All Types</option>
                        @foreach($procedure_types as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Admission Status</label>
                    <select class="form-select form-select -sm" id="admission_status" name="admission_status">
                        <option value="">All Statuses</option>
                        <option value="Admit">Currently Admitted</option>
                        <option value="Discharged">Discharged</option>
                        <option value="Cancelled">Cancelled</option>
                        <option value="Reffered">Reffered</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <button type="button" class="btn btn-primary me-2" id="apply-filters">
                        <i class="bx bx-filter-alt me-1"></i> Apply Filters
                    </button>
                    <button type="button" class="btn btn-outline-secondary me-2" id="reset-filters">
                        <i class="bx bx-refresh me-1"></i> Reset Filters
                    </button>

                    <button type="button" class="btn btn-info me-2" id="print-table">
                        <i class="bx bx-printer me-1"></i> Print Table
                    </button>

                    <!-- Hidden cleanup button for emergency use -->
                    <button type="button" class="btn btn-outline-warning" id="force-cleanup" style="display: none;" title="Force cleanup if page is stuck">
                        <i class="bx bx-recycle me-1"></i> Force Cleanup
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Statistics Cards -->
    <!-- Financial Summary Cards -->
    <div class="row" id="financial-stats-container">
        <div class="col-lg-3 col-md-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar">
                                <span class="avatar-initial bg-label-info rounded-circle"><i class="bx bx-money fs-4"></i></span>
                            </div>
                            <div class="card-info">
                                <h5 class="card-title mb-0 me-2" id="total-procedure-amount">Rs. 0</h5>
                                <small class="text-muted">Total Procedure Amount</small>
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
                                <span class="avatar-initial bg-label-success rounded-circle"><i class="bx bx-credit-card fs-4"></i></span>
                            </div>
                            <div class="card-info">
                                <h5 class="card-title mb-0 me-2" id="total-consultant-share">Rs. 0</h5>
                                <small class="text-muted">Total Consultant Share</small>
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
                                <span class="avatar-initial bg-label-warning rounded-circle"><i class="bx bx-trending-up fs-4"></i></span>
                            </div>
                            <div class="card-info">
                                <h5 class="card-title mb-0 me-2" id="net-revenue">Rs. 0</h5>
                                <small class="text-muted">Net Hospital Revenue</small>
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
                                <span class="avatar-initial bg-label-primary rounded-circle"><i class="bx bx-clipboard fs-4"></i></span>
                            </div>
                            <div class="card-info">
                                <h5 class="card-title mb-0 me-2" id="total-procedures">0</h5>
                                <small class="text-muted">Total Procedures</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Cards -->
    <div class="row" id="stats-container">
        <div class="col-lg-3 col-md-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar">
                                <span class="avatar-initial bg-label-success rounded-circle"><i class="bx bx-user-check fs-4"></i></span>
                            </div>
                            <div class="card-info">
                                <a target="_blank" href="{{route('pos.in_patient_admission_list')}}">

                                    <h5 class="card-title mb-0 me-2" id="currently-admit">0</h5>
                                    <small class="text-muted">Currently Admitted</small>
                                </a>
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
                                <span class="avatar-initial bg-label-info rounded-circle"><i class="bx bx-check-circle fs-4"></i></span>
                            </div>
                            <div class="card-info">
                                <h5 class="card-title mb-0 me-2" id="discharged">0</h5>
                                <small class="text-muted">Discharged</small>
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
                                <span class="avatar-initial bg-label-danger rounded-circle"><i class="bx bx-x-circle fs-4"></i></span>
                            </div>
                            <div class="card-info">
                                <h5 class="card-title mb-0 me-2" id="cancelled">0</h5>
                                <small class="text-muted">Cancelled</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row for Additional Stats -->
    <div class="row" id="additional-stats-container">
        <div class="col-lg-3 col-md-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar">
                                <span class="avatar-initial bg-label-warning rounded-circle"><i class="bx bx-share-alt fs-4"></i></span>
                            </div>
                            <div class="card-info">
                                <h5 class="card-title mb-0 me-2" id="referred">0</h5>
                                <small class="text-muted">Referred</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row">
        <div class="col-12">
            <div class="chart-card">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3">
                    <h5 class="mb-2 mb-md-0"><i class="tf-icons bx bx-bar-chart"></i> Procedure Types Distribution</h5>
                    <small class="text-muted">Click on bars for detailed view</small>
                </div>
                <div id="procedureTypeChart" class="chart-area"></div>
            </div>
        </div>

    </div>

    <!-- Top Consultants -->
    <div class="row">
        <div class="col-12">
            <div class="chart-card" id="consultants-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="tf-icons bx bx-crown"></i> Top Consultants Performance</h5>
                    <span class="badge bg-info" id="consultants-count">Loading...</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover" id="consultants-table">
                        <thead class="table-dark">
                            <tr>
                                <th><i class="tf-icons bx bx-user"></i> Consultant</th>
                                <th><i class="tf-icons bx bx-list-ul"></i> Procedures</th>
                                <th><i class="tf-icons bx bx-money"></i> Revenue</th>
                                <th><i class="tf-icons bx bx-credit-card"></i> Share Amount</th>
                                <th><i class="tf-icons bx bx-chart"></i> Performance</th>
                            </tr>
                        </thead>
                        <tbody id="consultants-tbody">
                            <tr>
                                <td colspan="5" class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <div class="mt-2">Loading consultant data...</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Header and Filters (only visible when printing) -->
    <div class="print-header">
        <h2>Hospital Management System</h2>
        <h3>Procedure Dashboard Report</h3>
        <p>Generated on: <span id="print-date"></span></p>
    </div>

    <div class="print-filters">
        <h4>Applied Filters:</h4>
        <div class="row">
            <div class="col-md-3">
                <strong>Date Range:</strong>
                <div id="print-date-range">All Dates</div>
            </div>
            <div class="col-md-3">
                <strong>Consultant:</strong>
                <div id="print-consultant">All Consultants</div>
            </div>
            <div class="col-md-3">
                <strong>Procedure Type:</strong>
                <div id="print-procedure-type">All Types</div>
            </div>
            <div class="col-md-3">
                <strong>Status:</strong>
                <div id="print-status">All Statuses</div>
            </div>
        </div>
    </div>

    <!-- Main Data Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header no-print">
                    <h5 class="card-title mb-0"><i class="tf-icons bx bx-table"></i> Procedure Details</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="procedures-table" class="table table-striped table-hover print-table">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>MR No</th>
                                    <th>Consultant</th>
                                    <th>Procedure</th>
                                    <th>Type</th>
                                    <th>Ward/Bed</th>
                                    <th>Amount</th>
                                    <th>Consultant Share</th>
                                    <th>Admission Date</th>
                                    <th>Discharge Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
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

<script>
    let proceduresTable;
    let procedureTypeChart;

    $(document).ready(function() {
        // Initialize DataTable
        initializeDataTable();

        // Load initial data
        loadDashboardData();

        // Event listeners
        $('#apply-filters').on('click', function() {
            proceduresTable.ajax.reload();
            loadDashboardData();
        });

        $('#reset-filters').on('click', function() {
            $('#filter-form')[0].reset();
            proceduresTable.ajax.reload();
            loadDashboardData();
        });

        $('#export-pdf').on('click', function() {
            exportToPdf();
        });

        $('#print-table').on('click', function() {
            printProcedures();
        });

        // Force cleanup button (emergency use)
        $('#force-cleanup').on('click', function() {
            cleanupModal();
            showAlert('Page cleaned up successfully!', 'success');
        });

        // Show cleanup button if modal issues detected
        setTimeout(function() {
            if ($('.modal-backdrop').length > 0 || $('body').hasClass('modal-open')) {
                $('#force-cleanup').show();
            }
        }, 2000);

        // Emergency cleanup on ESC key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && ($('.modal-backdrop').length > 0 || $('#procedureDetailsModal').length > 0)) {
                cleanupModal();
            }
        });

        // Cleanup on page unload
        $(window).on('beforeunload', function() {
            cleanupModal();
        });

        // Update print filter display whenever filters change
        $('#filter-form input, #filter-form select').on('change', function() {
            updatePrintFilters();
        });

        // Initialize print filters display
        updatePrintFilters();
    });

    function initializeDataTable() {
        proceduresTable = $('#procedures-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: '{{ route("reports.procedure_dashboard.data") }}',
                data: function(d) {
                    d.from_date = $('#from_date').val();
                    d.to_date = $('#to_date').val();
                    d.consultant_id = $('#consultant_id').val();
                    d.procedure_type = $('#procedure_type').val();
                    d.admission_status = $('#admission_status').val();
                }
            },
            columns: [{
                    data: 'patient_name',
                    name: 'patient.name'
                },
                {
                    data: 'patient_mrno',
                    name: 'patient.mrno'
                },
                {
                    data: 'consultant_name',
                    name: 'consultant.name'
                },
                {
                    data: 'procedure_name',
                    name: 'consultant_procedure.procedure.name'
                },
                {
                    data: 'procedure_type',
                    name: 'consultant_procedure.procedure.type'
                },
                {
                    data: 'ward_bed',
                    name: 'ward_bed',
                    orderable: false
                },
                {
                    data: 'procedure_amount',
                    name: 'procedure_rate',
                    render: function(data) {
                        return 'Rs. ' + data;
                    }
                },
                {
                    data: 'consultant_share',
                    name: 'consultant_share_amount',
                    render: function(data) {
                        return 'Rs. ' + data;
                    }
                },
                {
                    data: 'admission_date',
                    name: 'admission_date'
                },
                {
                    data: 'discharge_date',
                    name: 'discharge_date'
                },
                {
                    data: 'admission_status_badge',
                    name: 'admission_status',
                    orderable: false
                }
            ],
            order: [
                [8, 'desc']
            ], // Order by admission_date desc
            pageLength: 25,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'print'
            ]
        });
    }

    function loadDashboardData() {
        // Show loading state
        $('.card').addClass('loading');
        $('#consultants-card').addClass('loading');
        $('#consultants-count').text('Loading...');
        $('#financial-stats-container .card').addClass('loading');

        // Disable buttons during loading
        $('#apply-filters, #reset-filters, #export-pdf, #print-table').prop('disabled', true);

        $.ajax({
            url: '{{ route("reports.procedure_dashboard.stats") }}',
            method: 'GET',
            timeout: 30000,
            data: {
                from_date: $('#from_date').val(),
                to_date: $('#to_date').val(),
                consultant_id: $('#consultant_id').val(),
                procedure_type: $('#procedure_type').val(),
                admission_status: $('#admission_status').val()
            },
            success: function(response) {
                try {
                    console.log('Dashboard data received:', response);

                    if (response.stats) {
                        updateStatsCards(response.stats);
                    }

                    if (response.procedure_types) {
                        updateProcedureTypeChart(response.procedure_types);
                    }



                    if (response.top_consultants) {
                        updateConsultantsTable(response.top_consultants);
                    }

                } catch (error) {
                    console.error('Error processing dashboard data:', error);
                    alert('Error processing dashboard data. Please refresh the page.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading dashboard data:', {
                    xhr,
                    status,
                    error
                });

                let errorMessage = 'Error loading dashboard data.';
                if (status === 'timeout') {
                    errorMessage = 'Request timed out. Please try again.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Server error. Please contact administrator.';
                }

                alert(errorMessage);
            },
            complete: function() {
                $('.card').removeClass('loading');
                $('#consultants-card').removeClass('loading');
                $('#financial-stats-container .card').removeClass('loading');

                // Re-enable buttons after loading
                $('#apply-filters, #reset-filters, #export-pdf, #print-table').prop('disabled', false);

                // Update print filters display
                updatePrintFilters();
            }
        });
    }

    function updateStatsCards(stats) {
        // Update financial cards
        $('#total-procedure-amount').text('Rs. ' + (stats.total_revenue || 0).toLocaleString());
        $('#total-consultant-share').text('Rs. ' + (stats.total_consultant_share || 0).toLocaleString());
        $('#net-revenue').text('Rs. ' + (stats.total_hospital_share || 0).toLocaleString());

        // Update count cards
        $('#total-procedures').text(stats.total_procedures.toLocaleString());
        $('#currently-admit').text(stats.currently_admitted_count.toLocaleString());
        $('#discharged').text(stats.discharged_count.toLocaleString());
        $('#cancelled').text(stats.cancelled_count.toLocaleString());
        $('#referred').text(stats.referred_count.toLocaleString());
    }

    function updateProcedureTypeChart(data) {
        const chartEl = document.getElementById('procedureTypeChart');

        if (procedureTypeChart) {
            procedureTypeChart.destroy();
        }

        // Handle empty data
        if (!data || data.length === 0) {
            chartEl.innerHTML = '<div class="text-center text-muted py-5"><i class="tf-icons bx bx-info-circle fs-1 mb-2"></i><div>No procedure data found</div><small>Try adjusting your filter criteria</small></div>';
            return;
        }

        // Check if ApexCharts is available, if not load from CDN
        if (typeof window.ApexCharts === 'undefined') {
            const fallback = document.createElement('script');
            fallback.src = 'https://cdn.jsdelivr.net/npm/apexcharts';
            fallback.onload = function() {
                renderProcedureChart(data, chartEl);
            };
            fallback.onerror = function() {
                chartEl.innerHTML = '<div class="text-center text-muted py-5">Unable to load chart library.</div>';
            };
            document.head.appendChild(fallback);
            return;
        }

        renderProcedureChart(data, chartEl);
    }

    function renderProcedureChart(data, chartEl) {

        const total = data.reduce((sum, item) => sum + item.count, 0);
        const colors = [
            '#667eea', '#f093fb', '#4facfe', '#43e97b', '#fa709a',
            '#ff9a9e', '#a8edea', '#fed6e3', '#d299c2', '#ffecd2'
        ];

        const options = {
            chart: {
                type: 'bar',
                height: 360,
                toolbar: {
                    show: false
                },
                events: {
                    dataPointSelection: function(event, chartContext, config) {
                        if (config.dataPointIndex >= 0 && data[config.dataPointIndex]) {
                            const procedureType = data[config.dataPointIndex];
                            showProcedureDetails(procedureType);
                        }
                    }
                }
            },
            series: [{
                name: 'Procedures',
                data: data.map(item => item.count)
            }],
            plotOptions: {
                bar: {
                    columnWidth: '60%',
                    borderRadius: 8,
                    distributed: true
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return val;
                },
                offsetY: -16,
                style: {
                    fontSize: '12px',
                    colors: ['#304758']
                }
            },
            xaxis: {
                categories: data.map(item => item.type),
                labels: {
                    rotate: -30,
                    trim: false,
                    style: {
                        fontSize: '12px',
                        fontWeight: 'bold'
                    }
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            yaxis: {
                min: 0,
                forceNiceScale: true,
                title: {
                    text: 'Number of Procedures',
                    style: {
                        fontSize: '14px',
                        fontWeight: 'bold'
                    }
                },
                labels: {
                    formatter: function(val) {
                        return Math.floor(val);
                    }
                }
            },
            colors: colors,
            grid: {
                strokeDashArray: 4,
                borderColor: '#e4e6ef'
            },
            tooltip: {
                y: {
                    formatter: function(val, opts) {
                        const index = opts.dataPointIndex;
                        const percentage = ((val / total) * 100).toFixed(1);
                        const revenue = data[index].revenue || 0;
                        return `<div>
                            <div>Procedures: ${val}</div>
                            <div>Percentage: ${percentage}%</div>
                            <div>Revenue: Rs. ${revenue.toLocaleString()}</div>
                            <div style="font-style: italic; font-size: 12px;">Click to view details</div>
                        </div>`;
                    }
                }
            },
            legend: {
                show: false
            }
        };

        procedureTypeChart = new ApexCharts(chartEl, options);
        procedureTypeChart.render();
    }



    function updateConsultantsTable(data) {
        const tbody = $('#consultants-tbody');
        tbody.empty();

        // Update count badge
        $('#consultants-count').text(data.length + ' Consultants');

        if (data.length === 0) {
            tbody.append(`
            <tr>
                <td colspan="5" class="text-center py-4">
                    <div class="text-muted">
                        <i class="tf-icons bx bx-info-circle fs-1 mb-2"></i>
                        <div>No consultant data found for the selected filters</div>
                        <small>Try adjusting your filter criteria</small>
                    </div>
                </td>
            </tr>
        `);
            return;
        }

        const maxRevenue = Math.max(...data.map(item => item.revenue));
        const totalRevenue = data.reduce((sum, item) => sum + item.revenue, 0);

        data.forEach((consultant, index) => {
            const performance = Math.round((consultant.revenue / maxRevenue) * 100);
            const revenuePercentage = ((consultant.revenue / totalRevenue) * 100).toFixed(1);

            // Determine rank styling
            let rankIcon = '';
            let rankClass = '';
            if (index === 0) {
                rankIcon = '<i class="tf-icons bx bx-trophy text-warning"></i>';
                rankClass = 'table-warning';
            } else if (index === 1) {
                rankIcon = '<i class="tf-icons bx bx-medal text-info"></i>';
                rankClass = 'table-info';
            } else if (index === 2) {
                rankIcon = '<i class="tf-icons bx bx-award text-success"></i>';
                rankClass = 'table-success';
            }

            const row = `
            <tr class="${rankClass}" data-rank="${index + 1}">
                <td>
                    <div class="d-flex align-items-center">
                        <span class="me-2">${rankIcon}</span>
                        <div>
                            <strong>${consultant.consultant}</strong>
                            <br><small class="text-muted">Rank #${index + 1}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge bg-primary fs-6">${consultant.procedures}</span>
                    <br><small class="text-muted">procedures</small>
                </td>
                <td>
                    <strong>Rs. ${consultant.revenue.toLocaleString()}</strong>
                    <br><small class="text-muted">${revenuePercentage}% of total</small>
                </td>
                <td>
                    <strong>Rs. ${consultant.share.toLocaleString()}</strong>
                    <br><small class="text-muted">${((consultant.share / consultant.revenue) * 100).toFixed(1)}% share</small>
                </td>
                <td>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar ${index < 3 ? 'bg-success' : 'bg-primary'}" 
                             role="progressbar" 
                             style="width: ${performance}%" 
                             aria-valuenow="${performance}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            <strong>${performance}%</strong>
                        </div>
                    </div>
                    <small class="text-muted">vs top performer</small>
                </td>
            </tr>
        `;
            tbody.append(row);
        });
    }

    function exportToPdf() {
        const params = new URLSearchParams({
            from_date: $('#from_date').val(),
            to_date: $('#to_date').val(),
            consultant_id: $('#consultant_id').val(),
            procedure_type: $('#procedure_type').val(),
            admission_status: $('#admission_status').val()
        });

        window.open('{{ route("reports.procedure_dashboard.pdf") }}?' + params.toString(), '_blank');
    }

    function printProcedures() {
        // Get current filter values
        const params = new URLSearchParams({
            from_date: $('#from_date').val(),
            to_date: $('#to_date').val(),
            consultant_id: $('#consultant_id').val(),
            procedure_type: $('#procedure_type').val(),
            admission_status: $('#admission_status').val()
        });

        // Open print view in new window
        const printUrl = '{{ route("reports.procedure_dashboard.print") }}?' + params.toString();
        window.open(printUrl, '_blank');
    }

    function updatePrintFilters() {
        // Update date range
        const fromDate = $('#from_date').val();
        const toDate = $('#to_date').val();
        let dateRange = 'All Dates';
        if (fromDate && toDate) {
            dateRange = fromDate + ' to ' + toDate;
        } else if (fromDate) {
            dateRange = 'From ' + fromDate;
        } else if (toDate) {
            dateRange = 'Until ' + toDate;
        }
        $('#print-date-range').text(dateRange);

        // Update consultant
        const consultantSelect = $('#consultant_id');
        const consultantText = consultantSelect.val() ?
            consultantSelect.find('option:selected').text() : 'All Consultants';
        $('#print-consultant').text(consultantText);

        // Update procedure type
        const procedureTypeSelect = $('#procedure_type');
        const procedureTypeText = procedureTypeSelect.val() ?
            procedureTypeSelect.find('option:selected').text() : 'All Types';
        $('#print-procedure-type').text(procedureTypeText);

        // Update status
        const statusSelect = $('#admission_status');
        const statusText = statusSelect.val() ?
            statusSelect.find('option:selected').text() : 'All Statuses';
        $('#print-status').text(statusText);
    }

    function showProcedureDetails(procedureType) {
        // Clean up any existing modal safely
        cleanupModal();

        // Show loading modal first
        const loadingModal = `
        <div class="modal fade" id="procedureDetailsModal" tabindex="-1" aria-labelledby="procedureDetailsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="procedureDetailsModalLabel">
                            <i class="tf-icons bx bx-clipboard"></i> ${procedureType.type} - Procedure Details
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div class="mt-2">Loading procedure details...</div>
                    </div>
                </div>
            </div>
        </div>
    `;

        $('body').append(loadingModal);

        // Setup modal cleanup handler
        $('#procedureDetailsModal').on('hidden.bs.modal.procedureDetails', function() {
            cleanupModal();
        });

        $('#procedureDetailsModal').modal('show');

        // Fetch detailed procedure data
        $.ajax({
            url: '{{ route("reports.procedure_dashboard.data") }}',
            method: 'GET',
            data: {
                procedure_type: procedureType.type,
                from_date: $('#from_date').val(),
                to_date: $('#to_date').val(),
                consultant_id: $('#consultant_id').val(),
                admission_status: $('#admission_status').val(),
                length: 8000, // Limit to first 10 records
                start: 0
            },
            success: function(response) {
                const procedures = response.data || [];

                let proceduresList = '';
                if (procedures.length > 0) {
                    proceduresList = procedures.map(proc => `
                    <tr>
                        <td><strong>${proc.patient_name || 'N/A'}</strong></td>
                        <td>${proc.procedure_name || 'N/A'}</td>
                        <td>${proc.consultant_name || 'N/A'}</td>
                        <td>${proc.admission_date || 'N/A'}</td>
                        <td><span class="badge bg-success">Rs. ${proc.procedure_amount || '0'}</span></td>
                    </tr>
                `).join('');
                } else {
                    proceduresList = '<tr><td colspan="5" class="text-center">No procedures found</td></tr>';
                }

                const detailedModal = `
                <div class="modal fade" id="procedureDetailsModal" tabindex="-1" aria-labelledby="procedureDetailsModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="procedureDetailsModalLabel">
                                    <i class="tf-icons bx bx-clipboard"></i> ${procedureType.type} - Procedure Details
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row mb-4">
                                    <div class="col-md-3">
                                        <div class="card text-center bg-light">
                                            <div class="card-body">
                                                <h3 class="text-primary">${procedureType.count}</h3>
                                                <p class="mb-0">Total Procedures</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card text-center bg-light">
                                            <div class="card-body">
                                                <h3 class="text-success">Rs. ${(procedureType.revenue || 0).toLocaleString()}</h3>
                                                <p class="mb-0">Total Revenue</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card text-center bg-light">
                                            <div class="card-body">
                                                <h3 class="text-info">${((procedureType.count / procedureType.total_procedures * 100) || 0).toFixed(1)}%</h3>
                                                <p class="mb-0">Of Total</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card text-center bg-light">
                                            <div class="card-body">
                                                <h3 class="text-warning">Rs. ${((procedureType.revenue || 0) / procedureType.count).toFixed(0)}</h3>
                                                <p class="mb-0">Avg per Procedure</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <h6><i class="tf-icons bx bx-list-ul"></i> Recent Procedures</h6>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Patient Name</th>
                                                <th>Procedure Name</th>
                                                <th>Consultant</th>
                                                <th>Date</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${proceduresList}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="tf-icons bx bx-x"></i> Close
                                </button>
                                
                            </div>
                        </div>
                    </div>
                </div>
            `;

                // Replace loading modal with detailed modal
                $('#procedureDetailsModal').off('hidden.bs.modal.procedureDetails');
                $('#procedureDetailsModal').modal('hide');

                setTimeout(() => {
                    cleanupModal();
                    $('body').append(detailedModal);

                    // Setup modal cleanup handler with unique namespace
                    $('#procedureDetailsModal').on('hidden.bs.modal.procedureDetails', function() {
                        cleanupModal();
                    });

                    $('#procedureDetailsModal').modal('show');
                }, 350); // Wait for hide animation to complete
            },
            error: function() {
                $('#procedureDetailsModal .modal-body').html(`
                <div class="alert alert-danger">
                    <i class="tf-icons bx bx-error"></i> Failed to load procedure details. Please try again.
                </div>
            `);
            }
        });
    }

    function applyProcedureTypeFilter(procedureType) {
        $('#procedure_type').val(procedureType);

        // Close modal and reload data
        if ($('#procedureDetailsModal').length > 0) {
            $('#procedureDetailsModal').off('hidden.bs.modal.procedureDetails');
            $('#procedureDetailsModal').on('hidden.bs.modal.procedureFilter', function() {
                cleanupModal();
                // Reload data after modal is fully closed
                proceduresTable.ajax.reload();
                loadDashboardData();
            });
            $('#procedureDetailsModal').modal('hide');
        } else {
            // If no modal, just reload data
            proceduresTable.ajax.reload();
            loadDashboardData();
        }
    }

    // Centralized modal cleanup function
    function cleanupModal() {
        // Remove modal and all event listeners
        if ($('#procedureDetailsModal').length > 0) {
            $('#procedureDetailsModal').off();
            $('#procedureDetailsModal').remove();
        }
        
        // Clean up bootstrap modal artifacts
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css({
            'padding-right': '',
            'overflow': ''
        });
        
        // Ensure page scrolling is restored
        $('html').css('overflow', '');
        
        // Clear any stuck modal states
        $(document).off('keydown.bs.modal');
    }

    function showAlert(message, type = 'info') {
        const alertClass = type === 'error' ? 'alert-danger' : type === 'success' ? 'alert-success' : 'alert-info';
        const alertIcon = type === 'error' ? 'bx-error' : type === 'success' ? 'bx-check' : 'bx-info-circle';

        const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="tf-icons bx ${alertIcon}"></i>
            <strong>${type === 'error' ? 'Error!' : type === 'success' ? 'Success!' : 'Info!'}</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;

        $('#alert-container').html(alertHtml);

        // Auto-hide after 5 seconds
        setTimeout(() => {
            $('#alert-container .alert').fadeOut();
        }, 5000);
    }
</script>
@endpush