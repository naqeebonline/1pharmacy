@extends('layouts.'.config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('stylesheets')
    <style>
        .table > :not(caption) > * > * {padding: 5px;}
        @media print {
            .no-print { display: none !important; }
            body { 
                font-size: 12px; 
                margin: 0;
                padding: 0;
            }
            
            /* Hide everything except the table */
            body * {
                visibility: hidden;
            }
            
            /* Show only the table and its contents */
            .table-to-print, .table-to-print * {
                visibility: visible;
            }
            
            /* Position the table at the top of the page */
            .table-to-print {
                position: absolute;
                left: 0;
                top: 0;
                width: 100% !important;
                margin: 0;
                padding: 10px;
            }
            
            /* Ensure table styling is preserved in print */
            .table-to-print table {
                width: 100% !important;
                border-collapse: collapse !important;
            }
            
            .table-to-print th, .table-to-print td {
                border: 1px solid #000 !important;
                padding: 5px !important;
                font-size: 10px !important;
            }
            
            .table-to-print th {
                background-color: #f0f0f0 !important;
                font-weight: bold !important;
            }
        }
        .report-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .stats-card {
            background: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 10px;
            margin-bottom: 10px;
        }
        .stats-card h3 {
            font-size: 1.2rem;
            margin-bottom: 5px;
        }
        .stats-card p {
            font-size: 0.8rem;
            margin-bottom: 2px;
        }
        .stats-card small {
            font-size: 0.7rem;
        }
        .table th {
            background-color: #343a40;
            color: #ffff !important;
            font-weight: 600;
            font-size: 9px;
        }
        .table td {
            font-size: 11px;
        }
        .table-striped > tbody > tr:nth-of-type(odd) > td {
            background-color: rgba(0,123,255,.05);
        }
    </style>

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <!-- GRN Supplier Report -->
            <div class="card">
                <div class="card-header header-elements-inline">
                    <div class="text-muted">
                        <i class="fas fa-truck me-2"></i>GRN Supplier Purchase Report - {{ $supplier->Name }}
                    </div>
                </div>

                <div class="card-body">
                    <!-- Report Header Section -->
                    <div class="report-header text-center">
                        <p class="mb-0">Supplier Purchase Report</p>
                        <p class="mb-0"><b>Name:</b> {{ $supplier->Name }}  <b>| Contact:</b> {{ $supplier->ContactNo ?? 'N/A' }}</p>
                        <p class="mb-0">

                        @if($invoice_no)

                               <b> Invoice#:</b> {{ $invoice_no }}

                        @endif
                        @if(!empty($product_ids))

                               <b>| Products:</b> {{ count($product_ids) }} Selected

                        @endif
                        @if($from_date || $to_date)

                                <b>| Date Filter:</b>
                                {{ $from_date ? date('d-M-Y', strtotime($from_date)) : 'All' }}
                                <b>to </b>
                                {{ $to_date ? date('d-M-Y', strtotime($to_date)) : 'All' }}

                        @endif
                        </p>
                        <p class="mb-0"><small>Report Generated: {{ date('d-M-Y H:i:s') }}</small></p>
                    </div>

                    <!-- Summary Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="stats-card text-center">
                                <h3 class="text-primary">{{ $uniqueProducts }}</h3>
                                <p class="mb-0">Total Items Purchased</p>
                                <small class="text-muted">(Unique Products)</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-card text-center">
                                <h3 class="text-success">{{ number_format($totalItems, 0) }}</h3>
                                <p class="mb-0">Total Quantity</p>
                                <small class="text-muted">(All Purchases)</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-card text-center">
                                <h3 class="text-info">Rs. {{ number_format($totalSubtotal, 2) }}</h3>
                                <p class="mb-0">Subtotal</p>
                                <small class="text-muted">(Before Tax/Discount)</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-card text-center">
                                <h3 class="text-danger">Rs. {{ number_format($totalDiscount, 2) }}</h3>
                                <p class="mb-0">Total Discount</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-card text-center">
                                <h3 class="text-warning">Rs. {{ number_format($totalAdvanceTax + $totalGstTax, 2) }}</h3>
                                <p class="mb-0">Total Taxes</p>
                                <small class="text-muted">(GST + Advance)</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-card text-center">
                                <h3 class="text-success">Rs. {{ number_format($totalAmount, 2) }}</h3>
                                <p class="mb-0">Final Total</p>
                                <small class="text-muted">{{ $totalGRNs }} GRNs</small>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mb-3 no-print">
                        <div class="col-md-12">
                             
                            <a href="{{ route('reports.grn_supplier_report_pdf', [
                                'supplier_id' => $supplier_id,
                                'from_date' => $from_date,
                                'to_date' => $to_date,
                                'invoice_no' => $invoice_no,
                                'product_ids' => $product_ids
                            ]) }}" class="btn btn-sm btn-danger me-2" title="Download PDF">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                            <button onclick="exportToExcel()" class="btn btn-sm btn-success me-2" title="Export to Excel">
                                <i class="fas fa-file-excel"></i>
                            </button>
                            <a href="{{ route('reports.dashboard') }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                        <div class="col-md-12 mt-2">
    <form method="GET" action="{{ route('reports.grn_supplier_report') }}">
        <div class="row">
            <div class="col-md-4 mb-2">
                <select name="supplier_id" id="supplier_id" class="form-select">
                    @foreach($allSuppliers as $sup)
                        <option value="{{ $sup->SCID }}" {{ $sup->SCID == $supplier_id ? 'selected' : '' }}>
                            {{ $sup->Name }} (ID: {{ $sup->SCID }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-8 mb-2">
                <select name="product_ids[]" id="product_select" class="form-select" multiple>
                    @foreach($availableProducts as $product)
                        <option value="{{ $product->ProductID }}" 
                            {{ in_array($product->ProductID, $product_ids ?? []) ? 'selected' : '' }}>
                            {{ $product->ProductName }} (ID: {{ $product->ProductID }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 mb-2">
                <input type="text" name="invoice_no" class="form-control" 
                       value="{{ $invoice_no ?? '' }}" placeholder="Invoice No">
            </div>

            <div class="col-md-3 mb-2">
                <input type="date" name="from_date" class="form-control" 
                       value="{{ $from_date }}" placeholder="From Date">
            </div>

            <div class="col-md-3 mb-2">
                <input type="date" name="to_date" class="form-control" 
                       value="{{ $to_date }}" placeholder="To Date">
            </div>

            <div class="col-md-3 mb-2 d-flex">
                <button type="submit" class="btn btn-sm btn-warning me-2 flex-fill">
                    Filter
                </button>
                @if($from_date || $to_date || $supplier_id != 1 || $invoice_no || !empty($product_ids))
                    <a href="{{ route('reports.grn_supplier_report') }}" 
                       class="btn btn-sm btn-outline-secondary flex-fill">
                        Clear
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>
                    </div>
                </div>
            </div>
            <!-- /grn supplier report header -->
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <!-- Products Table -->
            <div class="card table-to-print">
                <div class="card-header header-elements-inline">
                    <h5 class="mb-0">Detailed Purchase Records from {{ $supplier->Name }}</h5>
                    <small class="text-muted">Each row represents a single item purchase from different GRNs</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="purchaseTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>GRN ID</th>
                                    <th>Invoice No</th>
                                    <th>Date</th>
                                    <th>Product ID</th>
                                    <th>Product Name</th>
                                    <th>Batch No</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Subtotal</th>
                                    <th>Discount</th>
                                    <th>Advance Tax</th>
                                    <th>GST Tax</th>
                                    <th>Final Total</th>
                                    <th>Expiry Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchaseData as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $item->GRNID }}</span>
                                        </td>
                                        <td>{{ $item->InvoiceNo ?? 'N/A' }}</td>
                                        <td>{{ $item->Dated ? date('d-M-Y', strtotime($item->Dated)) : 'N/A' }}</td>
                                        <td>{{ $item->ProductID }}</td>
                                        <td><strong>{{ $item->ProductName }}</strong></td>
                                        <td>
                                            @if($item->batch_no)
                                                <span class="badge bg-info">{{ $item->batch_no }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary">{{ number_format($item->Quantity, 0) }}</span>
                                        </td>
                                        <td class="text-end">Rs. {{ number_format($item->UnitPrice, 2) }}</td>
                                        <td class="text-end">Rs. {{ number_format($item->subtotal, 2) }}</td>
                                        <td class="text-end">
                                            @if($item->discount > 0)
                                                <span class="text-danger">
                                                    Rs. {{ number_format($item->calculated_discount, 2) }}
                                                    <small>({{ $item->discount }}%)</small>
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if($item->calculated_advance_tax > 0)
                                                <span class="text-info">
                                                    Rs. {{ number_format($item->calculated_advance_tax, 2) }}
                                                    @if($item->advance_tax > 0)
                                                        <small>({{ $item->advance_tax }}%)</small>
                                                    @endif
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if($item->calculated_gst_tax > 0)
                                                <span class="text-warning">
                                                    Rs. {{ number_format($item->calculated_gst_tax, 2) }}
                                                    @if($item->gst_tax > 0)
                                                        <small>({{ $item->gst_tax }}%)</small>
                                                    @endif
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <strong>Rs. {{ number_format($item->total_amount, 2) }}</strong>
                                        </td>
                                        <td>
                                            @if($item->expiry_date)
                                                <span class="badge {{ strtotime($item->expiry_date) < time() ? 'bg-danger' : 'bg-success' }}">
                                                    {{ date('d-M-Y', strtotime($item->expiry_date)) }}
                                                </span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="15" class="text-center text-muted py-4">
                                            <h5>No purchase data found for this supplier</h5>
                                            <p>No products have been purchased from {{ $supplier->Name }} yet.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($purchaseData->count() > 0)
                                <tfoot>
                                    <tr style="background-color: #f8f9fa; color: #000;">
                                        <th colspan="14" class="text-end" style="color: #000;"><strong>Total Quantity:</strong></th>
                                        <th class="text-center" style="color: #000;">{{ number_format($totalItems, 0) }}</th>
                                    </tr>
                                    <tr style="background-color: #f8f9fa; color: #000;">
                                        <th colspan="14" class="text-end" style="color: #000;"><strong>Subtotal:</strong></th>
                                        <th class="text-end" style="color: #000;">Rs. {{ number_format($totalSubtotal, 2) }}</th>
                                    </tr>
                                    <tr style="background-color: #f8f9fa; color: #000;">
                                        <th colspan="14" class="text-end" style="color: #000;"><strong>Total Discount:</strong></th>
                                        <th class="text-end"><span class="text-danger">Rs. {{ number_format($totalDiscount, 2) }}</span></th>
                                    </tr>
                                    <tr style="background-color: #f8f9fa; color: #000;">
                                        <th colspan="14" class="text-end" style="color: #000;"><strong>Total Advance Tax:</strong></th>
                                        <th class="text-end"><span class="text-info">Rs. {{ number_format($totalAdvanceTax, 2) }}</span></th>
                                    </tr>
                                    <tr style="background-color: #f8f9fa; color: #000;">
                                        <th colspan="14" class="text-end" style="color: #000;"><strong>Total GST Tax:</strong></th>
                                        <th class="text-end"><span class="text-warning">Rs. {{ number_format($totalGstTax, 2) }}</span></th>
                                    </tr>
                                    <tr style="background-color: #e9ecef; color: #000; border-top: 2px solid #6c757d;">
                                        <th colspan="14" class="text-end" style="color: #000;"><strong>GRAND TOTAL:</strong></th>
                                        <th class="text-end"><span class="text-success" style="font-size: 1.1em;"><strong>Rs. {{ number_format($totalAmount, 2) }}</strong></span></th>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
            <!-- /products table -->
        </div>
    </div>

    <!-- Footer -->
    <div class="row mt-4 mb-3">
        <div class="col-12 text-center">
            <small class="text-muted">
                Report generated on {{ date('d-M-Y H:i:s') }} | 
                Hospital Management System
            </small>
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
        $(document).ready(function() {
            // Initialize Select2 for product multi-select
            setTimeout(function(){
            $('#product_select').select2({
                placeholder: 'Select Products (Optional)',
                allowClear: true,
                width: '100%'
            });
             $('#supplier_id').select2({
                placeholder: 'Select Products (Optional)',
                allowClear: true,
                width: '100%'
            });
            },2000);
        });

        function exportToExcel() {
            // Simple CSV export
            let csv = 'GRN ID,Invoice No,Date,Product ID,Product Name,Batch No,Quantity,Unit Price,Subtotal,Discount %,Discount Amount,Advance Tax %,Advance Tax Amount,GST Tax %,GST Tax Amount,Final Total,Expiry Date\n';
            
            @foreach($purchaseData as $item)
                csv += '{{ $item->GRNID }},"{{ $item->InvoiceNo }}","{{ $item->Dated }}",{{ $item->ProductID }},"{{ $item->ProductName }}","{{ $item->batch_no }}",{{ $item->Quantity }},{{ $item->UnitPrice }},{{ $item->subtotal }},{{ $item->discount }},{{ $item->calculated_discount }},{{ $item->advance_tax }},{{ $item->calculated_advance_tax }},{{ $item->gst_tax }},{{ $item->calculated_gst_tax }},{{ $item->total_amount }},"{{ $item->expiry_date }}"\n';
            @endforeach
            
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'grn_purchase_details_{{ $supplier->Name }}_{{ date("Y-m-d") }}.csv';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }
    </script>
@endpush
