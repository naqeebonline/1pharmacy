@extends('layouts.' . config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('stylesheets')
<style>
    .table> :not(caption)>*>* {
        padding: 5px;
    }

    .filter-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
</style>

<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bx bx-calendar-edit me-2"></i>Update Product Expiry Dates
                </h5>
            </div>

            <div class="card-body">
                <!-- Filter Section -->
                <div class="filter-section">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Select Product</label>
                            <select id="filter_product_id" class="form-select form-select-sm">
                                <option value="">-- All Products --</option>
                                @foreach($products as $product)
                                <option value="{{ $product->ProductID }}">{{ $product->ProductName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Batch Number</label>
                            <input type="text" id="filter_batch_no" class="form-control form-control-sm" placeholder="Search batch">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Expiry Status</label>
                            <select id="filter_expiry_status" class="form-select form-select-sm">
                                <option value="">-- All Status --</option>
                                <option value="expired">Expired</option>
                                <option value="expiring_soon">Expiring Soon (≤30 days)</option>
                                <option value="near_expiry">Near Expiry (31-90 days)</option>
                                <option value="valid">Valid (>90 days)</option>
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
                            <button class="btn btn-primary btn-sm w-100" id="btn_print">
                                <i class="bx bx-printer me-1"></i>Print
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="table-responsive" style="min-height: 400px">
                    <table id="expiry-table" class="table table-striped table-bordered" style="width: 100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th>Supplier</th>
                                <th>Batch No</th>
                                <th>Expiry Date</th>
                                <th>Days Until Expiry</th>
                                <th>Status</th>
                                <th>Quantity</th>
                                <th>GRN Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Expiry Date Modal -->
<div class="modal fade" id="edit_expiry_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-white">
                    <i class="bx bx-edit me-2"></i>Update Expiry Date
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="update_expiry_form">
                <div class="modal-body">
                    <input type="hidden" id="grn_details_id" name="grn_details_id">

                    <div class="mb-3">
                        <label class="form-label"><strong>Product Name:</strong></label>
                        <p id="display_product_name" class="form-control-plaintext"></p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><strong>Batch Number:</strong></label>
                        <p id="display_batch_no" class="form-control-plaintext"></p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><strong>Current Expiry Date:</strong></label>
                        <p id="display_current_expiry" class="form-control-plaintext text-danger"></p>
                    </div>

                    <div class="mb-3">
                        <label for="new_expiry_date" class="form-label">
                            <strong>New Expiry Date <span class="text-danger">*</span></strong>
                        </label>
                        <input type="date" class="form-control" id="new_expiry_date" name="expiry_date" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-warning" id="btn_update">
                        <i class="bx bx-save me-1"></i>Update Expiry
                    </button>
                </div>
            </form>
        </div>
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

<script>
    var expiry_table;

    $(document).ready(function() {
        // Initialize Select2
        $("#filter_product_id").select2({
            placeholder: "-- All Products --",
            allowClear: true
        });

        // Initialize DataTable
        expiry_table = $('#expiry-table').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            ajax: {
                url: "{{ route('expiry.get_grn_details') }}",
                data: function(d) {
                    d.product_id = $('#filter_product_id').val();
                    d.batch_no = $('#filter_batch_no').val();
                    d.expiry_status = $('#filter_expiry_status').val();
                }
            },
            columns: [{
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
                    data: 'Quantity',
                    name: 'grn_details.Quantity'
                },
                {
                    data: 'Dated',
                    name: 'grn.Dated'
                },
                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false
                }
            ],
            order: [
                [0, 'desc']
            ],
            responsive: true
        });

        // Search button click
        $("#btn_search").on("click", function() {
            expiry_table.ajax.reload();
        });

        // Reset button click
        $("#btn_reset").on("click", function() {
            $("#filter_product_id").val('').trigger('change');
            $("#filter_batch_no").val('');
            $("#filter_expiry_status").val('');
            expiry_table.ajax.reload();
        });

        // Print button click
        $("#btn_print").on("click", function() {
            var product_id = $('#filter_product_id').val();
            var batch_no = $('#filter_batch_no').val();
            var expiry_status = $('#filter_expiry_status').val();

            var url = "{{ route('expiry.print_expiry_report') }}?";
            if (product_id) url += "product_id=" + product_id + "&";
            if (batch_no) url += "batch_no=" + batch_no + "&";
            if (expiry_status) url += "expiry_status=" + expiry_status;

            window.open(url, '_blank');
        });

        // Edit expiry button click
        $("body").on("click", ".edit_expiry", function() {
            var id = $(this).data("id");
            var expiry = $(this).data("expiry");
            var product = $(this).data("product");
            var batch = $(this).data("batch");

            $("#grn_details_id").val(id);
            $("#display_product_name").text(product);
            $("#display_batch_no").text(batch);
            $("#display_current_expiry").text(expiry);
            $("#new_expiry_date").val(expiry);

            $("#edit_expiry_modal").modal("show");
        });

        // Form submit
        $("#update_expiry_form").on("submit", function(e) {
            e.preventDefault();

            var formData = {
                grn_details_id: $("#grn_details_id").val(),
                expiry_date: $("#new_expiry_date").val(),
                _token: '{{ csrf_token() }}'
            };

            $.ajax({
                type: 'POST',
                url: "{{ route('expiry.update_expiry_date') }}",
                data: formData,
                beforeSend: function() {
                    $("#btn_update").prop('disabled', true).html('<i class="bx bx-loader bx-spin me-1"></i>Updating...');
                },
                success: function(response) {
                    if (response.status) {
                        alert(response.message);
                        $("#edit_expiry_modal").modal("hide");
                        expiry_table.ajax.reload();
                        $("#update_expiry_form")[0].reset();
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr) {
                    alert('Error: ' + (xhr.responseJSON?.message || 'Something went wrong!'));
                },
                complete: function() {
                    $("#btn_update").prop('disabled', false).html('<i class="bx bx-save me-1"></i>Update Expiry');
                }
            });
        });

        // Close modal and reset form
        $("#edit_expiry_modal").on("hidden.bs.modal", function() {
            $("#update_expiry_form")[0].reset();
        });
    });
</script>
@endpush