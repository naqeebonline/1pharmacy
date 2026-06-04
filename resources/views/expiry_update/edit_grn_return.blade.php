@extends('layouts.' . config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('stylesheets')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
<style>
    .table> :not(caption)>*>* {
        padding: 8px;
    }

    .info-card {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .item-row {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 10px;
    }

    .item-row:hover {
        background: #f8f9fa;
    }

    .total-summary {
        background: #e3f2fd;
        padding: 15px;
        border-radius: 8px;
        border-left: 4px solid #2196f3;
    }

    .btn-remove-item {
        padding: 5px 10px;
        font-size: 0.85rem;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bx bx-edit me-2"></i>Edit GRN Return - #{{ $return->ReturnID }}
                </h5>
                <div>
                    <a href="{{ route('expiry.grn_returns_list') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i>Back to List
                    </a>
                    <a href="{{ route('expiry.view_grn_return', $return->ReturnID) }}" class="btn btn-info btn-sm">
                        <i class="bx bx-show me-1"></i>View Details
                    </a>
                </div>
            </div>

            <div class="card-body">
                <!-- Return Information -->
                <div class="info-card">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Return ID:</strong> #{{ $return->ReturnID }}
                        </div>
                        <div class="col-md-3">
                            <strong>Return Date:</strong> {{ date('d-M-Y', strtotime($return->ReturnDate)) }}
                        </div>
                        <div class="col-md-6">
                            <strong>Supplier:</strong> {{ $return->SupplierName }}
                        </div>
                    </div>
                </div>

                <!-- Add New Item Section -->
                <div class="card border-success mb-4">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="bx bx-plus-circle me-2"></i>Add New Items to Return</h6>
                        <span class="badge bg-light text-dark">Click checkbox to add items directly</span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Expiry Status</label>
                                <select id="filter_expiry_status" class="form-select">
                                    <option value="">All</option>
                                    <option value="expired">Expired</option>
                                    <option value="expiring_soon">Expiring Soon (≤30 days)</option>
                                    <option value="near_expiry">Near Expiry (31-100 days)</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Product</label>
                                <select id="filter_product" class="form-select">
                                    <option value="">All Products</option>
                                </select>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="available_items_table" class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">Select</th>
                                        <th width="25%">Product Name</th>
                                        <th width="15%">Batch No</th>
                                        <th width="15%">Expiry Date</th>
                                        <th width="12%">Status</th>
                                        <th width="10%">Days Left</th>
                                        <th width="10%">Available Qty</th>
                                        <th width="8%">Unit Price</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Return Items -->
                <h6 class="mb-3"><i class="bx bx-package me-2"></i>Return Items</h6>
                <div id="items_container">
                    @foreach($returnDetails as $index => $detail)
                    <div class="item-row" data-detail-id="{{ $detail->ReturnDetailID }}">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <label class="form-label mb-1"><strong>Product</strong></label>
                                <p class="mb-0">{{ $detail->ProductName }}</p>
                                <small class="text-muted">Batch: {{ $detail->BatchNo ?? 'N/A' }}</small>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label mb-1"><strong>Expiry Date</strong></label>
                                <p class="mb-0">{{ $detail->ExpiryDate ? date('d-M-Y', strtotime($detail->ExpiryDate)) : 'N/A' }}</p>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label mb-1"><strong>Unit Price</strong></label>
                                <p class="mb-0">Rs. {{ number_format($detail->UnitPrice, 2) }}</p>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label mb-1"><strong>Max Available</strong></label>
                                <p class="mb-0 text-success">{{ number_format($detail->MaxAvailableQty, 2) }}</p>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label mb-1"><strong>Return Qty <span class="text-danger">*</span></strong></label>
                                <input type="number"
                                    class="form-control form-control-sm return-qty-input"
                                    data-detail-id="{{ $detail->ReturnDetailID }}"
                                    data-gdid="{{ $detail->GDID }}"
                                    data-product-id="{{ $detail->ProductID }}"
                                    data-unit-price="{{ $detail->UnitPrice }}"
                                    data-max-qty="{{ $detail->MaxAvailableQty }}"
                                    value="{{ $detail->ReturnQuantity }}"
                                    min="1"
                                    max="{{ $detail->MaxAvailableQty }}"
                                    step="0.01">
                                <small class="text-muted">Max: {{ number_format($detail->MaxAvailableQty, 2) }}</small>
                            </div>
                            <div class="col-md-1 text-end">
                                <label class="form-label mb-1">&nbsp;</label>
                                <button class="btn btn-sm btn-danger btn-remove-item w-100"
                                    data-detail-id="{{ $detail->ReturnDetailID }}"
                                    data-gdid="{{ $detail->GDID }}"
                                    data-original-qty="{{ $detail->ReturnQuantity }}">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12 text-end">
                                <strong>Line Total: Rs. <span class="line-total" data-detail-id="{{ $detail->ReturnDetailID }}">{{ number_format($detail->TotalAmount, 2) }}</span></strong>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Total Summary -->
                <div class="total-summary mt-4">
                    <div class="row">
                        <div class="col-md-8"></div>
                        <div class="col-md-4">
                            <div class="d-flex justify-content-between mb-2">
                                <strong>Total Items:</strong>
                                <span id="total_items">{{ count($returnDetails) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <strong>Total Quantity:</strong>
                                <span id="total_quantity">{{ number_format($returnDetails->sum('ReturnQuantity'), 2) }}</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-0"><strong>Grand Total:</strong></h6>
                                <h6 class="mb-0 text-danger"><strong>Rs. <span id="grand_total">{{ number_format($return->TotalAmount, 2) }}</span></strong></h6>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-4 text-end">
                    <a href="{{ route('expiry.grn_returns_list') }}" class="btn btn-secondary">
                        <i class="bx bx-x me-1"></i>Cancel
                    </a>
                    <button class="btn btn-primary" id="btn_save_changes">
                        <i class="bx bx-save me-1"></i>Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/vendor/libs/datatables/jquery.dataTables.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
<script>
    var returnId = {{ $return->ReturnID }};
    var removedItems = [];

    // Helper functions - defined first
    function getExistingGdids() {
        var gdids = [];
        $('.return-qty-input').each(function() {
            gdids.push($(this).data('gdid'));
        });
        return gdids;
    }

    function isItemAlreadyAdded(gdid) {
        var exists = false;
        $('.return-qty-input').each(function() {
            if ($(this).data('gdid') == gdid) {
                exists = true;
                return false; // break loop
            }
        });
        return exists;
    }

    function addNewItemRow(item) {
        var returnQty = item.ReturnQuantity || item.MaxAvailableQty;
        var lineTotal = returnQty * item.UnitPrice;
        var newItemId = 'new_' + Date.now(); // Unique ID for new items

        // Determine expiry status based on expiry date
        var expiryDate = new Date(item.ExpiryDate);
        var today = new Date();
        var daysUntilExpiry = Math.ceil((expiryDate - today) / (1000 * 60 * 60 * 24));
        var statusBadge = '';
        
        if (daysUntilExpiry < 0) {
            statusBadge = '<span class="badge bg-danger">Expired</span>';
        } else if (daysUntilExpiry <= 30) {
            statusBadge = '<span class="badge bg-warning">Expiring Soon</span>';
        } else if (daysUntilExpiry <= 100) {
            statusBadge = '<span class="badge bg-info">Near Expiry</span>';
        } else {
            statusBadge = '<span class="badge bg-success">Valid</span>';
        }

        var formattedExpiryDate = expiryDate.toLocaleDateString('en-GB', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });

        var newRow = `
            <div class="item-row" data-detail-id="${newItemId}" data-gdid="${item.GDID}">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <label class="form-label mb-1"><strong>Product</strong></label>
                        <p class="mb-0">
                            <span class="badge bg-success me-1">NEW</span>
                            ${item.ProductName}
                        </p>
                        <small class="text-muted">Batch: ${item.BatchNo || 'N/A'}</small>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label mb-1"><strong>Expiry Date</strong></label>
                        <p class="mb-0">${formattedExpiryDate}</p>
                        <small>${statusBadge}</small>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label mb-1"><strong>Unit Price</strong></label>
                        <p class="mb-0">Rs. ${parseFloat(item.UnitPrice).toFixed(2)}</p>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label mb-1"><strong>Max Available</strong></label>
                        <p class="mb-0 text-success">${parseFloat(item.MaxAvailableQty).toFixed(2)}</p>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label mb-1"><strong>Return Qty <span class="text-danger">*</span></strong></label>
                        <input type="number"
                            class="form-control form-control-sm return-qty-input"
                            data-detail-id="${newItemId}"
                            data-gdid="${item.GDID}"
                            data-product-id="${item.ProductID}"
                            data-unit-price="${item.UnitPrice}"
                            data-max-qty="${item.MaxAvailableQty}"
                            data-is-new="true"
                            value="${returnQty}"
                            min="1"
                            step="0.01"
                            max="${item.MaxAvailableQty}"
                            required>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label mb-1"><strong>Action</strong></label><br>
                        <button type="button"
                            class="btn btn-sm btn-danger btn-remove-item"
                            data-detail-id="${newItemId}"
                            data-gdid="${item.GDID}"
                            data-original-qty="0"
                            data-is-new="true">
                            <i class="bx bx-trash"></i> Remove
                        </button>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12 text-end">
                        <strong>Line Total: Rs. <span class="line-total" data-detail-id="${newItemId}">${lineTotal.toFixed(2)}</span></strong>
                    </div>
                </div>
            </div>
        `;

        $('#items_container').append(newRow);
    }

    function calculateTotals() {
        var totalItems = $('.item-row').length;
        var totalQty = 0;
        var grandTotal = 0;

        $('.return-qty-input').each(function() {
            var qty = parseFloat($(this).val()) || 0;
            var unitPrice = parseFloat($(this).data('unit-price'));

            totalQty += qty;
            grandTotal += (qty * unitPrice);
        });

        $('#total_items').text(totalItems);
        $('#total_quantity').text(totalQty.toFixed(2));
        $('#grand_total').text(grandTotal.toFixed(2));
    }

    $(document).ready(function() {
        console.log('Page loaded. ReturnID:', returnId);
        console.log('jQuery version:', $.fn.jquery);
        console.log('Select2 available:', typeof $.fn.select2 !== 'undefined');

        // Update quantity
        $(document).on('input', '.return-qty-input', function() {
            var input = $(this);
            var qty = parseFloat(input.val()) || 0;
            var maxQty = parseFloat(input.data('max-qty'));
            var unitPrice = parseFloat(input.data('unit-price'));
            var detailId = input.data('detail-id');

            // Validate quantity
            if (qty < 1) {
                input.val(1);
                qty = 1;
            }

            if (qty > maxQty) {
                input.val(maxQty);
                qty = maxQty;
                alert('Quantity cannot exceed maximum available quantity of ' + maxQty);
            }

            // Update line total
            var lineTotal = qty * unitPrice;
            $('.line-total[data-detail-id="' + detailId + '"]').text(lineTotal.toFixed(2));

            // Update grand total
            calculateTotals();
        });

        // Remove item
        $(document).on('click', '.btn-remove-item', function() {
            if (!confirm('Are you sure you want to remove this item from the return?')) {
                return;
            }

            var detailId = $(this).data('detail-id');
            var gdid = $(this).data('gdid');
            var originalQty = $(this).data('original-qty');
            var isNew = $(this).data('is-new') === true;

            // Only add to removed items array if it's an existing item (not new)
            if (!isNew && originalQty > 0) {
                removedItems.push({
                    detail_id: detailId,
                    gdid: gdid,
                    original_qty: originalQty
                });
            }

            // Remove the row
            $('.item-row[data-detail-id="' + detailId + '"]').remove();

            // Update totals
            calculateTotals();

            // If it was a NEW item, refresh DataTable to show it again
            if (isNew) {
                availableItemsTable.draw();
            }

            // Check if all items removed
            if ($('.item-row').length === 0) {
                alert('You cannot remove all items. At least one item must remain.');
                location.reload();
            }
        });

        // Save changes
        $('#btn_save_changes').on('click', function() {
            var items = [];
            var newItems = [];
            var isValid = true;

            // Collect all items
            $('.return-qty-input').each(function() {
                var input = $(this);
                var qty = parseFloat(input.val()) || 0;
                var maxQty = parseFloat(input.data('max-qty'));
                var isNew = input.data('is-new') === true;

                if (qty <= 0 || qty > maxQty) {
                    isValid = false;
                    input.addClass('is-invalid');
                } else {
                    input.removeClass('is-invalid');

                    var itemData = {
                        detail_id: input.data('detail-id'),
                        gdid: input.data('gdid'),
                        product_id: input.data('product-id'),
                        return_qty: qty,
                        unit_price: input.data('unit-price')
                    };

                    // Separate new items from existing items
                    if (isNew) {
                        newItems.push(itemData);
                    } else {
                        items.push(itemData);
                    }
                }
            });

            if (!isValid) {
                alert('Please check the quantities. All quantities must be valid and within the available range.');
                return;
            }

            if (items.length === 0 && newItems.length === 0) {
                alert('At least one item is required.');
                return;
            }

            if (!confirm('Are you sure you want to save these changes?')) {
                return;
            }

            // Disable button
            $(this).prop('disabled', true).html('<i class="bx bx-loader bx-spin me-1"></i>Saving...');

            $.ajax({
                url: "{{ route('expiry.update_grn_return') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    return_id: returnId,
                    items: items,
                    new_items: newItems,
                    removed_items: removedItems
                },
                success: function(response) {
                    if (response.status) {
                        alert(response.message);
                        window.location.href = "{{ route('expiry.grn_returns_list') }}";
                    } else {
                        alert('Error: ' + response.message);
                        $('#btn_save_changes').prop('disabled', false).html('<i class="bx bx-save me-1"></i>Save Changes');
                    }
                },
                error: function(xhr) {
                    var errorMessage = 'Error saving changes';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                    $('#btn_save_changes').prop('disabled', false).html('<i class="bx bx-save me-1"></i>Save Changes');
                }
            });
        });

        // Initialize DataTable for available items
        var availableItemsTable = $('#available_items_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('expiry.get_returnable_items') }}",
                data: function(d) {
                    d.supplier_id = {{ $return->SCID }};
                    d.expiry_status = $('#filter_expiry_status').val();
                    d.product_id = $('#filter_product').val();
                    d.exclude_gdids = getExistingGdids();
                }
            },
            columns: [
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return '<input type="checkbox" class="form-check-input select-new-item" ' +
                            'data-gdid="' + row.GDID + '" ' +
                            'data-product-id="' + row.ProductID + '" ' +
                            'data-product-name="' + row.ProductName + '" ' +
                            'data-batch="' + row.batch_no + '" ' +
                            'data-expiry="' + row.expiry_date + '" ' +
                            'data-qty="' + row.RemainingQuantity + '" ' +
                            'data-price="' + row.UnitPrice + '" ' +
                            'data-days="' + row.days_until_expiry + '">';
                    }
                },
                { data: 'ProductName', name: 'products.ProductName' },
                { data: 'batch_no', name: 'grn_details.batch_no' },
                { 
                    data: 'expiry_date', 
                    name: 'grn_details.expiry_date',
                    render: function(data) {
                        return data ? new Date(data).toLocaleDateString('en-GB', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        }) : 'N/A';
                    }
                },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'days_until_expiry', name: 'days_until_expiry', orderable: false, searchable: false },
                { data: 'RemainingQuantity', name: 'grn_details.RemainingQuantity' },
                { 
                    data: 'UnitPrice', 
                    name: 'grn_details.UnitPrice',
                    render: function(data) {
                        return parseFloat(data).toFixed(2);
                    }
                }
            ],
            order: [[3, 'asc']],
            pageLength: 10,
            drawCallback: function(settings) {
                // Re-attach event handlers after redraw
                attachNewItemHandlers();
            }
        });

        // Filter handlers
        $('#filter_expiry_status, #filter_product').on('change', function() {
            availableItemsTable.draw();
        });

        // Attach handlers for new items
        function attachNewItemHandlers() {
            $('.select-new-item').off('change').on('change', function() {
                var checkbox = $(this);
                var gdid = checkbox.data('gdid');
                
                if (checkbox.is(':checked')) {
                    // Check if item is already added
                    if (isItemAlreadyAdded(gdid)) {
                        alert('This item is already in the return list!');
                        checkbox.prop('checked', false);
                        return;
                    }
                    
                    // Add item to return list immediately
                    var item = {
                        GDID: gdid,
                        ProductID: checkbox.data('product-id'),
                        ProductName: checkbox.data('product-name'),
                        BatchNo: checkbox.data('batch'),
                        ExpiryDate: checkbox.data('expiry'),
                        UnitPrice: checkbox.data('price'),
                        MaxAvailableQty: checkbox.data('qty'),
                        ReturnQuantity: checkbox.data('qty') // Default to max available
                    };
                    
                    addNewItemRow(item);
                    calculateTotals();
                    
                    // Refresh the DataTable to exclude this item
                    availableItemsTable.draw();
                } else {
                    // Remove item from return list (only NEW items can be removed this way)
                    var itemRow = $('.item-row[data-gdid="' + gdid + '"]');
                    
                    // Check if this is a NEW item (has the success badge)
                    if (itemRow.length > 0 && itemRow.find('.badge-success').length > 0) {
                        itemRow.remove();
                        calculateTotals();
                        
                        // Refresh the DataTable to show this item again
                        availableItemsTable.draw();
                    }
                }
            });
        }
    });

</script>
@endpush