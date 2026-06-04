@extends('layouts.' . config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('stylesheets')
<style>
    .table> :not(caption)>*>* {
        padding: 5px;
    }

    .filter-section {
        background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
        color: white;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .filter-section .form-label {
        color: white;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .filter-section .form-control,
    .filter-section .form-select {
        border: 2px solid rgba(255, 255, 255, 0.3);
        background: rgba(255, 255, 255, 0.9);
    }

    .filter-section .btn-search {
        background: #10b981;
        border: none;
        color: white;
        font-weight: 600;
    }

    .filter-section .btn-search:hover {
        background: #059669;
    }

    .selected-items-section {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-top: 20px;
        border: 2px dashed #dee2e6;
    }

    .selected-item-card {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 10px;
        margin-bottom: 10px;
    }

    .form-check-input:checked {
        background-color: #dc3545;
        border-color: #dc3545;
    }
</style>

<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bx bx-package me-2"></i>Return Expired/Short Expiry Items to Supplier
                </h5>
            </div>

            <div class="card-body">
                <!-- Filter Section -->
                <div class="filter-section">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Select Supplier</label>
                            <select id="filter_supplier_id" class="form-select form-select-sm">
                                <option value="">-- All Suppliers --</option>
                                @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->SCID }}">{{ $supplier->Name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Select Product</label>
                            <select id="filter_product_id" class="form-select form-select-sm">
                                <option value="">-- All Products --</option>
                                @foreach($products as $product)
                                <option value="{{ $product->ProductID }}">{{ $product->ProductName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Expiry Status</label>
                            <select id="filter_expiry_status" class="form-select form-select-sm">
                                <option value="">-- All Status --</option>
                                <option value="expired">Expired</option>
                                <option value="expiring_soon">Expiring Soon (≤30 days)</option>
                                <option value="near_expiry">Near Expiry (31-100 days)</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-search btn-sm w-100" id="btn_search">
                                <i class="bx bx-search me-1"></i>Search
                            </button>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-light btn-sm w-100" id="btn_reset">
                                <i class="bx bx-reset me-1"></i>Reset
                            </button>
                        </div>
                        <div class="col-md-1">
                            <button class="btn btn-info btn-sm w-100" id="btn_select_all">
                                <i class="bx bx-check-square me-1"></i>All
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="table-responsive" style="min-height: 300px">
                    <table id="returnable-items-table" class="table table-striped table-bordered" style="width: 100%">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="select_all_checkbox" class="form-check-input">
                                </th>
                                <th>#</th>
                                <th>Product Name</th>
                                <th>Supplier</th>
                                <th>Batch No</th>
                                <th>Expiry Date</th>
                                <th>Days Until Expiry</th>
                                <th>Status</th>
                                <th>Available Qty</th>
                                <th>Unit Price</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

                <!-- Selected Items Section -->
                <div class="selected-items-section" id="selected_items_section" style="display: none;">
                    <h6 class="mb-3">
                        <i class="bx bx-list-ul me-2"></i>Selected Items for Return
                        <span class="badge bg-danger" id="selected_count">0</span>
                    </h6>

                    <div id="selected_items_container">
                        <!-- Selected items will be added here dynamically -->
                    </div>

                    <!-- Total Summary -->
                    <div class="row mt-3">
                        <div class="col-md-8"></div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <strong>Total Items:</strong>
                                        <span id="total_items_count">0</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <strong>Total Quantity:</strong>
                                        <span id="total_quantity">0</span>
                                    </div>
                                    <hr class="my-2">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="mb-0"><strong>Grand Total:</strong></h6>
                                        <h6 class="mb-0 text-danger"><strong>Rs. <span id="grand_total">0.00</span></strong></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12 text-end">
                            <button class="btn btn-danger" id="btn_process_return">
                                <i class="bx bx-undo me-1"></i>Process Return to Supplier
                            </button>
                        </div>
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
<script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>

<script>
    var returnable_items_table;
    var selectedItems = [];

    $(document).ready(function() {
        // Initialize Select2
        $("#filter_supplier_id, #filter_product_id").select2({
            placeholder: "-- Select --",
            allowClear: true
        });

        // Initialize DataTable
        returnable_items_table = $('#returnable-items-table').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            ajax: {
                url: "{{ route('expiry.get_returnable_items') }}",
                data: function(d) {
                    d.supplier_id = $('#filter_supplier_id').val();
                    d.product_id = $('#filter_product_id').val();
                    d.expiry_status = $('#filter_expiry_status').val();
                }
            },
            columns: [{
                    data: 'select_item',
                    name: 'select_item',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'ProductName',
                    name: 'products.ProductName'
                },
                {
                    data: 'SupplierName',
                    name: 'sup_cus_details.Name'
                },
                {
                    data: 'batch_no',
                    name: 'grn_details.batch_no'
                },
                {
                    data: 'expiry_date',
                    name: 'grn_details.expiry_date'
                },
                {
                    data: 'days_until_expiry',
                    name: 'days_until_expiry',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'RemainingQuantity',
                    name: 'grn_details.RemainingQuantity'
                },
                {
                    data: 'UnitPrice',
                    name: 'grn_details.UnitPrice'
                }
            ],
            order: [
                [5, 'asc']
            ],
            responsive: true,
            drawCallback: function() {
                // Re-check checkboxes for items that are in selectedItems array
                selectedItems.forEach(function(selectedItem) {
                    $('.select-item[data-id="' + selectedItem.gdid + '"]').prop('checked', true);
                });
            }
        });

        // Search button click
        $("#btn_search").on("click", function() {
            returnable_items_table.ajax.reload();
        });

        // Reset button click
        $("#btn_reset").on("click", function() {
            $("#filter_supplier_id").val('').trigger('change');
            $("#filter_product_id").val('').trigger('change');
            $("#filter_expiry_status").val('');
            returnable_items_table.ajax.reload();
        });

        // Select all checkbox in header
        $(document).on('change', '#select_all_checkbox', function() {
            $('.select-item:visible').prop('checked', this.checked).trigger('change');
        });

        // Individual item selection
        $(document).on('change', '.select-item', function() {
            var checkbox = $(this);
            var gdid = checkbox.data('id');

            // Check if item already exists in selectedItems
            var existingIndex = selectedItems.findIndex(item => item.gdid === gdid);

            if (checkbox.is(':checked')) {
                // Only add if not already in the array (prevent duplicates)
                if (existingIndex === -1) {
                    var itemData = {
                        gdid: gdid,
                        product_id: checkbox.data('product-id'),
                        product_name: checkbox.data('product-name'),
                        batch_no: checkbox.data('batch'),
                        expiry_date: checkbox.data('expiry'),
                        available_qty: checkbox.data('qty'),
                        unit_price: checkbox.data('price'),
                        supplier_id: checkbox.data('supplier-id'),
                        supplier_name: checkbox.data('supplier-name'),
                        return_qty: checkbox.data('qty')
                    };
                    selectedItems.push(itemData);
                }
            } else {
                // Remove item if unchecked
                if (existingIndex > -1) {
                    selectedItems.splice(existingIndex, 1);
                }
            }

            updateSelectedItemsDisplay();
        });

        // Select all visible items
        $("#btn_select_all").on("click", function() {
            $('#select_all_checkbox').prop('checked', true).trigger('change');
        });

        // Process return
        $("#btn_process_return").on("click", function() {
            if (selectedItems.length === 0) {
                alert('Please select at least one item to return.');
                return;
            }

            // Validate quantities
            var isValid = true;
            selectedItems.forEach(function(item) {
                if (item.return_qty <= 0 || item.return_qty > item.available_qty) {
                    isValid = false;
                }
            });

            if (!isValid) {
                alert('Please enter valid return quantities for all items.');
                return;
            }

            if (confirm('Are you sure you want to process return for ' + selectedItems.length + ' items?')) {
                // Disable button to prevent double submission
                $(this).prop('disabled', true).html('<i class="bx bx-loader bx-spin me-1"></i>Processing...');

                $.ajax({
                    url: "{{ route('expiry.save_return_to_supplier') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        items: selectedItems
                    },
                    success: function(response) {
                        if (response.status) {
                            alert(response.message);

                            // Redirect to GRN returns list
                            window.location.href = "{{ route('expiry.grn_returns_list') }}";
                        } else {
                            alert('Error: ' + response.message);
                            $("#btn_process_return").prop('disabled', false).html('<i class="bx bx-undo me-1"></i>Process Return to Supplier');
                        }
                    },
                    error: function(xhr, status, error) {
                        var errorMessage = 'Error processing return: ' + error;
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        alert(errorMessage);
                        $("#btn_process_return").prop('disabled', false).html('<i class="bx bx-undo me-1"></i>Process Return to Supplier');
                    }
                });
            }
        });
    });

    function updateSelectedItemsDisplay() {
        var container = $('#selected_items_container');
        container.empty();

        if (selectedItems.length === 0) {
            $('#selected_items_section').hide();
            $('#selected_count').text('0');
            $('#total_items_count').text('0');
            $('#total_quantity').text('0');
            $('#grand_total').text('0.00');
            return;
        }

        $('#selected_items_section').show();
        $('#selected_count').text(selectedItems.length);

        var totalQuantity = 0;
        var grandTotal = 0;

        selectedItems.forEach(function(item, index) {
            var lineTotal = (parseFloat(item.return_qty) || 0) * (parseFloat(item.unit_price) || 0);
            totalQuantity += (parseFloat(item.return_qty) || 0);
            grandTotal += lineTotal;

            var cardHtml = `
                <div class="selected-item-card">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <strong>${item.product_name}</strong><br>
                            <small class="text-muted">Batch: ${item.batch_no}</small>
                        </div>
                        <div class="col-md-2">
                            <small>Supplier:</small><br>
                            <strong>${item.supplier_name || 'N/A'}</strong>
                        </div>
                        <div class="col-md-2">
                            <small>Expiry Date:</small><br>
                            <strong class="text-danger">${formatDate(item.expiry_date)}</strong>
                        </div>
                        <div class="col-md-1">
                            <small>Available:</small><br>
                            <strong>${item.available_qty}</strong>
                        </div>
                        <div class="col-md-1">
                            <small>Unit Price:</small><br>
                            <strong>Rs. ${parseFloat(item.unit_price || 0).toFixed(2)}</strong>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label mb-0">Return Qty:</label>
                            <input type="number" class="form-control form-control-sm return-qty-input" 
                                   data-index="${index}" 
                                   value="${item.return_qty}" 
                                   min="1" 
                                   max="${item.available_qty}">
                        </div>
                        <div class="col-md-1">
                            <small>Line Total:</small><br>
                            <strong class="text-danger">Rs. ${lineTotal.toFixed(2)}</strong>
                        </div>
                        <div class="col-md-1 text-end">
                            <button class="btn btn-sm btn-danger remove-item" data-gdid="${item.gdid}">
                                <i class="bx bx-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            container.append(cardHtml);
        });

        // Update totals
        $('#total_items_count').text(selectedItems.length);
        $('#total_quantity').text(totalQuantity.toFixed(0));
        $('#grand_total').text(grandTotal.toFixed(2));
    }

    // Update return quantity
    $(document).on('input', '.return-qty-input', function() {
        var index = $(this).data('index');
        var qty = parseFloat($(this).val()) || 0;
        var maxQty = selectedItems[index].available_qty;

        if (qty > maxQty) {
            $(this).val(maxQty);
            qty = maxQty;
        }

        if (qty < 1) {
            $(this).val(1);
            qty = 1;
        }

        selectedItems[index].return_qty = qty;
        updateSelectedItemsDisplay();
    });

    // Remove item
    $(document).on('click', '.remove-item', function() {
        var gdid = $(this).data('gdid');
        $('.select-item[data-id="' + gdid + '"]').prop('checked', false);
        selectedItems = selectedItems.filter(item => item.gdid !== gdid);
        updateSelectedItemsDisplay();
    });

    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        var date = new Date(dateString);
        var options = {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        };
        return date.toLocaleDateString('en-GB', options);
    }
</script>
@endpush