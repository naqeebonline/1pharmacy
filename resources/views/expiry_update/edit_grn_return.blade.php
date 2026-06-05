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

    .return-items-table thead th {
        font-size: 0.8rem;
        white-space: nowrap;
        text-align: center;
        vertical-align: middle;
    }

    .return-items-table tbody td {
        vertical-align: middle;
        font-size: 0.85rem;
    }

    .return-items-table tbody td.col-product {
        text-align: left;
        min-width: 160px;
    }

    .return-items-table .grid-input {
        width: 100%;
        min-width: 72px;
        text-align: center;
        font-weight: 600;
    }

    .return-items-table .line-total {
        font-weight: 700;
        color: #dc3545;
        white-space: nowrap;
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
                <div class="table-responsive">
                    <table class="table table-bordered table-striped return-items-table">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Expiry</th>
                                <th>Available</th>
                                <th>Unit Price</th>
                                <th>Return Qty</th>
                                <th>Disc %</th>
                                <th>Line Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="items_container">
                            @foreach($returnDetails as $detail)
                            @php
                                $lineGross = (float) $detail->ReturnQuantity * (float) $detail->UnitPrice;
                                $discPct = (float) ($detail->discount ?? 0);
                                $lineTotal = $lineGross - ($lineGross * $discPct / 100);
                            @endphp
                            <tr class="item-row" data-detail-id="{{ $detail->ReturnDetailID }}" data-gdid="{{ $detail->GDID }}">
                                <td class="col-product">
                                    <strong>{{ $detail->ProductName }}</strong><br>
                                    <small class="text-muted">Batch: {{ $detail->BatchNo ?? 'N/A' }}</small>
                                </td>
                                <td>{{ $detail->ExpiryDate ? date('d-M-Y', strtotime($detail->ExpiryDate)) : 'N/A' }}</td>
                                <td class="text-center text-success">{{ number_format($detail->MaxAvailableQty, 2) }}</td>
                                <td>
                                    <input type="number" class="form-control form-control-sm grid-input unit-price-input"
                                           data-detail-id="{{ $detail->ReturnDetailID }}"
                                           value="{{ number_format($detail->UnitPrice, 2, '.', '') }}"
                                           min="0" step="0.01" inputmode="decimal">
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm grid-input return-qty-input"
                                           data-detail-id="{{ $detail->ReturnDetailID }}"
                                           data-gdid="{{ $detail->GDID }}"
                                           data-product-id="{{ $detail->ProductID }}"
                                           data-max-qty="{{ $detail->MaxAvailableQty }}"
                                           value="{{ $detail->ReturnQuantity }}"
                                           min="0" step="0.01" inputmode="decimal">
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm grid-input discount-pct-input"
                                           data-detail-id="{{ $detail->ReturnDetailID }}"
                                           value="{{ $discPct }}"
                                           min="0" max="100" step="0.01" inputmode="decimal">
                                </td>
                                <td class="text-end line-total" data-detail-id="{{ $detail->ReturnDetailID }}">Rs. {{ number_format($lineTotal, 2) }}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger btn-remove-item"
                                            data-detail-id="{{ $detail->ReturnDetailID }}"
                                            data-gdid="{{ $detail->GDID }}"
                                            data-original-qty="{{ $detail->ReturnQuantity }}">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
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
        $('.item-row').each(function() {
            gdids.push($(this).data('gdid'));
        });
        return gdids;
    }

    function isItemAlreadyAdded(gdid) {
        var exists = false;
        $('.item-row').each(function() {
            if (String($(this).data('gdid')) === String(gdid)) {
                exists = true;
                return false;
            }
        });
        return exists;
    }

    function normalizeDiscountPct(value) {
        var pct = parseFloat(value) || 0;
        if (pct < 0) return 0;
        if (pct > 100) return 100;
        return pct;
    }

    function getRowValues(detailId) {
        var $row = $('.item-row[data-detail-id="' + detailId + '"]');
        return {
            qty: parseFloat($row.find('.return-qty-input').val()) || 0,
            price: parseFloat($row.find('.unit-price-input').val()) || 0,
            disc: normalizeDiscountPct($row.find('.discount-pct-input').val())
        };
    }

    function calcLineTotal(qty, price, discPct) {
        var gross = (parseFloat(qty) || 0) * (parseFloat(price) || 0);
        discPct = normalizeDiscountPct(discPct);
        return gross - (gross * discPct / 100);
    }

    function updateLineTotalCell(detailId) {
        var values = getRowValues(detailId);
        $('.line-total[data-detail-id="' + detailId + '"]').text('Rs. ' + calcLineTotal(values.qty, values.price, values.disc).toFixed(2));
    }

    function addNewItemRow(item) {
        var returnQty = item.ReturnQuantity || item.MaxAvailableQty;
        var newItemId = 'new_' + Date.now();
        var lineTotal = calcLineTotal(returnQty, item.UnitPrice, 0);

        var expiryDate = new Date(item.ExpiryDate);
        var formattedExpiryDate = isNaN(expiryDate.getTime()) ? 'N/A' : expiryDate.toLocaleDateString('en-GB', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });

        var newRow = `
            <tr class="item-row" data-detail-id="${newItemId}" data-gdid="${item.GDID}">
                <td class="col-product">
                    <span class="badge bg-success me-1">NEW</span>
                    <strong>${item.ProductName}</strong><br>
                    <small class="text-muted">Batch: ${item.BatchNo || 'N/A'}</small>
                </td>
                <td>${formattedExpiryDate}</td>
                <td class="text-center text-success">${parseFloat(item.MaxAvailableQty).toFixed(2)}</td>
                <td>
                    <input type="number" class="form-control form-control-sm grid-input unit-price-input"
                           data-detail-id="${newItemId}"
                           value="${parseFloat(item.UnitPrice).toFixed(2)}"
                           min="0" step="0.01" inputmode="decimal">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm grid-input return-qty-input"
                           data-detail-id="${newItemId}"
                           data-gdid="${item.GDID}"
                           data-product-id="${item.ProductID}"
                           data-max-qty="${item.MaxAvailableQty}"
                           data-is-new="true"
                           value="${returnQty}"
                           min="0" step="0.01" inputmode="decimal">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm grid-input discount-pct-input"
                           data-detail-id="${newItemId}"
                           value="0"
                           min="0" max="100" step="0.01" inputmode="decimal">
                </td>
                <td class="text-end line-total" data-detail-id="${newItemId}">Rs. ${lineTotal.toFixed(2)}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger btn-remove-item"
                            data-detail-id="${newItemId}"
                            data-gdid="${item.GDID}"
                            data-original-qty="0"
                            data-is-new="true">
                        <i class="bx bx-trash"></i>
                    </button>
                </td>
            </tr>
        `;

        $('#items_container').append(newRow);
    }

    function calculateTotals() {
        var totalItems = $('.item-row').length;
        var totalQty = 0;
        var grandTotal = 0;

        $('.item-row').each(function() {
            var detailId = $(this).data('detail-id');
            var values = getRowValues(detailId);
            totalQty += values.qty;
            grandTotal += calcLineTotal(values.qty, values.price, values.disc);
        });

        $('#total_items').text(totalItems);
        $('#total_quantity').text(totalQty % 1 === 0 ? totalQty : totalQty.toFixed(2));
        $('#grand_total').text(grandTotal.toFixed(2));
    }

    function applyReturnQtyValue($input, validate) {
        var detailId = $input.data('detail-id');
        var maxQty = parseFloat($input.data('max-qty')) || 0;
        var raw = $.trim($input.val());

        if (!validate) {
            if (raw !== '') {
                updateLineTotalCell(detailId);
                calculateTotals();
            }
            return;
        }

        var qty = parseFloat(raw);
        if (isNaN(qty) || qty <= 0) qty = 1;
        if (qty > maxQty) qty = maxQty;
        $input.val(qty);
        updateLineTotalCell(detailId);
        calculateTotals();
    }

    function applyUnitPriceValue($input, validate) {
        var detailId = $input.data('detail-id');
        var raw = $.trim($input.val());

        if (!validate) {
            if (raw !== '') {
                updateLineTotalCell(detailId);
                calculateTotals();
            }
            return;
        }

        var price = parseFloat(raw);
        if (isNaN(price) || price < 0) price = 0;
        $input.val(price.toFixed(2));
        updateLineTotalCell(detailId);
        calculateTotals();
    }

    function applyDiscountValue($input, validate) {
        var detailId = $input.data('detail-id');
        var raw = $.trim($input.val());

        if (!validate) {
            if (raw !== '') {
                updateLineTotalCell(detailId);
                calculateTotals();
            }
            return;
        }

        var pct = normalizeDiscountPct(raw);
        $input.val(pct);
        updateLineTotalCell(detailId);
        calculateTotals();
    }

    $(document).ready(function() {
        calculateTotals();

        $(document).on('input', '.return-qty-input', function() {
            applyReturnQtyValue($(this), false);
        });
        $(document).on('blur change', '.return-qty-input', function() {
            applyReturnQtyValue($(this), true);
        });

        $(document).on('input', '.unit-price-input', function() {
            applyUnitPriceValue($(this), false);
        });
        $(document).on('blur change', '.unit-price-input', function() {
            applyUnitPriceValue($(this), true);
        });

        $(document).on('input', '.discount-pct-input', function() {
            applyDiscountValue($(this), false);
        });
        $(document).on('blur change', '.discount-pct-input', function() {
            applyDiscountValue($(this), true);
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
            $('.item-row').each(function() {
                var detailId = $(this).data('detail-id');
                var $qtyInput = $(this).find('.return-qty-input');
                var values = getRowValues(detailId);
                var maxQty = parseFloat($qtyInput.data('max-qty')) || 0;
                var isNew = $qtyInput.data('is-new') === true;

                if (values.qty <= 0 || values.qty > maxQty || values.price < 0) {
                    isValid = false;
                    $qtyInput.addClass('is-invalid');
                } else {
                    $qtyInput.removeClass('is-invalid');

                    var itemData = {
                        detail_id: detailId,
                        gdid: $qtyInput.data('gdid'),
                        product_id: $qtyInput.data('product-id'),
                        return_qty: values.qty,
                        unit_price: values.price,
                        discount_percentage: values.disc
                    };

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
                    
                    if (itemRow.length > 0 && itemRow.find('.badge.bg-success').length > 0) {
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