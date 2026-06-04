@extends('layouts.'.config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('stylesheets')
    <style>
        .table > :not(caption) > * > * {padding: 8px;}
        .inventory-card {
            transition: transform 0.3s;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .inventory-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
        }
        .low-stock {
            background-color: #ffe6e6;
        }
        .high-stock {
            background-color: #e6ffe6;
        }
        .medium-stock {
            background-color: #fff9e6;
        }
        .quantity-badge {
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 20px;
        }
        
        @media print {
            .btn, .header-elements, .dataTables_wrapper .dataTables_length, 
            .dataTables_wrapper .dataTables_filter, .dataTables_wrapper .dataTables_info, 
            .dataTables_wrapper .dataTables_paginate {
                display: none !important;
            }
            .table-responsive {
                overflow: visible !important;
            }
            .card-header {
                background-color: #f8f9fa !important;
                border-bottom: 2px solid #dee2e6 !important;
            }
        }
        
        .btn-excel {
            background: linear-gradient(45deg, #1e7e34, #28a745);
            border: none;
            transition: all 0.3s ease;
        }
        
        .btn-excel:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
        }
        
        .btn-warning-excel {
            background: linear-gradient(45deg, #e0a800, #ffc107);
            border: none;
            transition: all 0.3s ease;
        }
        
        .btn-warning-excel:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
        }
        
        .export-tooltip {
            position: relative;
        }
        
        .export-tooltip:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: #333;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            white-space: nowrap;
            z-index: 1000;
            font-size: 12px;
        }
    </style>

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <!-- Inventory Summary Header -->
            <div class="card inventory-card mb-4">
                <div class="card-header header-elements-inline">
                    <div class="text-muted">
                        <i class="fas fa-boxes me-2"></i>Inventory Summary Report
                    </div>
                    <div class="text-muted small">
                        Generated on: {{ $generated_at }}
                    </div>
                </div>
            </div>

            <!-- Summary Statistics Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card stat-card h-100">
                        <div class="card-body text-center">
                            <div class="mb-2">
                                <i class="fas fa-cubes fa-2x"></i>
                            </div>
                            <h3 class="text-white">{{ $totalProducts }}</h3>
                            <p class="mb-0">Total Products</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card stat-card h-100">
                        <div class="card-body text-center">
                            <div class="mb-2">
                                <i class="fas fa-warehouse fa-2x"></i>
                            </div>
                            <h3 class="text-white">{{ number_format($totalQuantity) }}</h3>
                            <p class="mb-0">Total Quantity</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card stat-card h-100">
                        <div class="card-body text-center">
                            <div class="mb-2">
                                <i class="fas fa-dollar-sign fa-2x"></i>
                            </div>
                            <h3 class="text-white">₨ {{ $totalInventoryValue }}</h3>
                            <p class="mb-0">Total Value</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card stat-card h-100">
                        <div class="card-body text-center">
                            <div class="mb-2">
                                <i class="fas fa-chart-bar fa-2x"></i>
                            </div>
                            <h3 class="text-white">₨ {{ $averageValue }}</h3>
                            <p class="mb-0">Average Value</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Low Stock Alert -->
            @if($lowStockProducts->count() > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card inventory-card">
                        <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-exclamation-triangle me-2"></i>Low Stock Alert (Less than 10 units)
                            </div>
                            <div>
                                <a href="{{ route('reports.export_low_stock_excel') }}" 
                                   class="btn btn-outline-light btn-sm export-tooltip" 
                                   data-tooltip="Export low stock products to Excel">
                                    <i class="fas fa-file-excel me-1"></i>Export Low Stock
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($lowStockProducts->take(6) as $product)
                                <div class="col-lg-4 col-md-6 mb-2">
                                    <div class="alert alert-warning mb-1">
                                        <strong>{{ $product->ProductName }}</strong>
                                        <span class="badge bg-danger float-end">{{ $product->total_available_quantity }}</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Detailed Inventory Table -->
            <div class="card inventory-card">
                <div class="card-header header-elements-inline">
                    <div class="text-muted">
                        <i class="fas fa-table me-2"></i>Detailed Inventory Report
                    </div>
                    <div class="header-elements">
                        <div class="btn-group me-2">
                            <a href="{{ route('reports.export_inventory_excel') }}" 
                               class="btn btn-success btn-sm btn-excel export-tooltip" 
                               data-tooltip="Export complete inventory to Excel">
                                <i class="fas fa-file-excel me-1"></i>Export All to Excel
                            </a>
                            <a href="{{ route('reports.export_low_stock_excel') }}" 
                               class="btn btn-warning btn-sm btn-warning-excel export-tooltip" 
                               data-tooltip="Export products with less than 10 units">
                                <i class="fas fa-exclamation-triangle me-1"></i>Export Low Stock
                            </a>
                        </div>
                        <button onclick="window.print()" class="btn btn-primary btn-sm">
                            <i class="fas fa-print me-1"></i>Print Report
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="inventoryTable" class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>S.No</th>
                                    <th>Product ID</th>
                                    <th>Product Name</th>
                                    <th>Available Quantity</th>
                                    <th>Avg. Unit Price</th>
                                    <th>Total Value</th>
                                    <th>Purchase Entries</th>
                                    <th>First Purchase</th>
                                    <th>Last Purchase</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($inventoryData as $index => $item)
                                <tr class="
                                    @if($item->total_available_quantity < 10) low-stock
                                    @elseif($item->total_available_quantity > 100) high-stock
                                    @else medium-stock
                                    @endif
                                ">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->ProductID }}</td>
                                    <td>
                                        <strong>{{ $item->ProductName }}</strong>
                                        @if($item->generic_name && $item->generic_name != 'N/A')
                                            <br><small class="text-muted">{{ $item->generic_name }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="quantity-badge 
                                            @if($item->total_available_quantity < 10) bg-danger text-white
                                            @elseif($item->total_available_quantity > 100) bg-success text-white
                                            @else bg-warning text-dark
                                            @endif
                                        ">
                                            {{ number_format($item->total_available_quantity) }}
                                        </span>
                                    </td>
                                    <td>₨ {{ number_format($item->average_unit_price, 2) }}</td>
                                    <td>₨ {{ number_format($item->total_value, 2) }}</td>
                                    <td>{{ $item->purchase_entries }}</td>
                                    <td>{{ $item->first_purchase_date ? \Carbon\Carbon::parse($item->first_purchase_date)->format('M d, Y') : 'N/A' }}</td>
                                    <td>{{ $item->last_purchase_date ? \Carbon\Carbon::parse($item->last_purchase_date)->format('M d, Y') : 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- High Value Products -->
            @if($highValueProducts->count() > 0)
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card inventory-card">
                        <div class="card-header bg-success text-white">
                            <i class="fas fa-star me-2"></i>Top 10 High Value Products
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Quantity</th>
                                            <th>Total Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($highValueProducts as $product)
                                        <tr>
                                            <td><strong>{{ $product->ProductName }}</strong></td>
                                            <td>{{ number_format($product->total_available_quantity) }}</td>
                                            <td>₨ {{ number_format($product->total_value, 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-responsive/datatables.responsive.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.js') }}"></script>
    
    <script>
        $(document).ready(function() {
            $('#inventoryTable').DataTable({
                responsive: true,
                pageLength: 25,
                order: [[3, 'desc']], // Order by Available Quantity descending (now column 3)
                columnDefs: [
                    { 
                        targets: [4, 5], // Unit Price and Total Value columns (now columns 4 and 5)
                        className: 'text-end' 
                    }
                ],
                language: {
                    search: "Search inventory:",
                    lengthMenu: "Show _MENU_ products per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ products",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                }
            });

            // Add some interactive features
            $('.quantity-badge').hover(
                function() {
                    $(this).addClass('shadow-lg');
                },
                function() {
                    $(this).removeClass('shadow-lg');
                }
            );

            console.log('Inventory Summary loaded successfully');
            console.log('Total Products: {{ $totalProducts }}');
            console.log('Total Quantity: {{ $totalQuantity }}');
            console.log('Total Value: {{ $totalValue }}');

            // Add loading state for Excel export buttons
            $('.btn-excel, .btn-warning-excel, .export-tooltip a').on('click', function() {
                var $btn = $(this);
                var originalText = $btn.html();
                var exportType = $btn.hasClass('btn-warning-excel') || $btn.attr('href').includes('low-stock') ? 'Low Stock' : 'Complete Inventory';
                
                // Show loading state
                $btn.html('<i class="fas fa-spinner fa-spin me-1"></i>Preparing ' + exportType + ' Export...');
                $btn.prop('disabled', true);
                
                // Show success message after a delay (file download should start)
                setTimeout(function() {
                    $btn.html(originalText);
                    $btn.prop('disabled', false);
                    
                    // Show success toast
                    showToast('success', 'Excel export started successfully! Check your downloads folder.');
                }, 2000);
            });

            // Function to show toast notifications
            function showToast(type, message) {
                var toastHtml = `
                    <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" 
                         role="alert" aria-live="assertive" aria-atomic="true" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                                ${message}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                `;
                
                $('body').append(toastHtml);
                var toast = new bootstrap.Toast($('.toast').last());
                toast.show();
                
                // Auto remove after 5 seconds
                setTimeout(function() {
                    $('.toast').last().remove();
                }, 5000);
            }
        });
    </script>
@endpush
