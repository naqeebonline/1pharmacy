@extends('layouts.' . config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('stylesheets')
<style>
    .table> :not(caption)>*>* {
        padding: 5px;
    }

    .status-badge {
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status-approved {
        background: #d1fae5;
        color: #065f46;
    }

    .status-rejected {
        background: #fee2e2;
        color: #991b1b;
    }

    .status-completed {
        background: #dbeafe;
        color: #1e40af;
    }

    .filter-section {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
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

    .action-btn {
        padding: 3px 8px;
        font-size: 0.85rem;
        margin: 2px;
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
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bx bx-list-ul me-2"></i>GRN Returns List
                </h5>
                <a href="{{ route('expiry.return_expired_items') }}" class="btn btn-primary btn-sm">
                    <i class="bx bx-plus me-1"></i>Create New Return
                </a>
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
                            <label class="form-label">Status</label>
                            <select id="filter_status" class="form-select form-select-sm">
                                <option value="">-- All Status --</option>
                                <option value="Pending">Pending</option>
                                <option value="Approved">Approved</option>
                                <option value="Rejected">Rejected</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">From Date</label>
                            <input type="date" id="filter_from_date" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">To Date</label>
                            <input type="date" id="filter_to_date" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-light btn-sm w-100" id="btn_search">
                                <i class="bx bx-search me-1"></i>Search
                            </button>
                        </div>
                        <div class="col-md-1">
                            <button class="btn btn-secondary btn-sm w-100" id="btn_reset">
                                <i class="bx bx-reset me-1"></i>Reset
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="table-responsive">
                    <table id="grn-returns-table" class="table table-striped table-bordered" style="width: 100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Return ID</th>
                                <th>Return Date</th>
                                <th>Supplier</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Created By</th>
                                <th>Created At</th>
                                <th>Actions</th>
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

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Approve Return Request</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to approve this return request?</p>
                <div class="mb-3">
                    <label class="form-label">Approval Remarks (Optional)</label>
                    <textarea class="form-control" id="approve_remarks" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="btn_confirm_approve">
                    <i class="bx bx-check me-1"></i>Approve
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Reject Return Request</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to reject this return request?</p>
                <div class="mb-3">
                    <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="reject_remarks" rows="3" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="btn_confirm_reject">
                    <i class="bx bx-x me-1"></i>Reject
                </button>
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
    var grn_returns_table;
    var currentReturnId = null;

    $(document).ready(function() {
        // Initialize Select2
        $("#filter_supplier_id").select2({
            placeholder: "-- Select Supplier --",
            allowClear: true
        });

        // Initialize DataTable
        grn_returns_table = $('#grn-returns-table').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            ajax: {
                url: "{{ route('expiry.get_grn_returns_list') }}",
                data: function(d) {
                    d.supplier_id = $('#filter_supplier_id').val();
                    d.status = $('#filter_status').val();
                    d.from_date = $('#filter_from_date').val();
                    d.to_date = $('#filter_to_date').val();
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'ReturnID',
                    name: 'grn_returns.ReturnID'
                },
                {
                    data: 'ReturnDate',
                    name: 'grn_returns.ReturnDate'
                },
                {
                    data: 'SupplierName',
                    name: 'sup_cus_details.Name'
                },
                {
                    data: 'TotalAmount',
                    name: 'grn_returns.TotalAmount'
                },
                {
                    data: 'status_badge',
                    name: 'status_badge',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'CreatedByName',
                    name: 'users.name'
                },
                {
                    data: 'CreatedAt',
                    name: 'grn_returns.CreatedAt'
                },
                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false
                }
            ],
            order: [
                [1, 'desc']
            ],
            responsive: true
        });

        // Search button click
        $("#btn_search").on("click", function() {
            grn_returns_table.ajax.reload();
        });

        // Reset button click
        $("#btn_reset").on("click", function() {
            $("#filter_supplier_id").val('').trigger('change');
            $("#filter_status").val('');
            $("#filter_from_date").val('');
            $("#filter_to_date").val('');
            grn_returns_table.ajax.reload();
        });

        // View details
        $(document).on('click', '.btn-view', function() {
            var returnId = $(this).data('id');
            window.location.href = "{{ route('expiry.view_grn_return', ':id') }}".replace(':id', returnId);
        });

        // Print return
        $(document).on('click', '.btn-print', function() {
            var returnId = $(this).data('id');
            var printUrl = "{{ route('expiry.print_grn_return', ':id') }}".replace(':id', returnId);
            window.open(printUrl, '_blank');
        });

        // Edit return
        $(document).on('click', '.btn-edit', function() {
            var returnId = $(this).data('id');
            window.location.href = "{{ route('expiry.edit_grn_return', ':id') }}".replace(':id', returnId);
        });

        // Approve return
        $(document).on('click', '.btn-approve', function() {
            currentReturnId = $(this).data('id');
            $('#approve_remarks').val('');
            $('#approveModal').modal('show');
        });

        // Reject return
        $(document).on('click', '.btn-reject', function() {
            currentReturnId = $(this).data('id');
            $('#reject_remarks').val('');
            $('#rejectModal').modal('show');
        });

        // Confirm approve
        $("#btn_confirm_approve").on("click", function() {
            var remarks = $('#approve_remarks').val();

            $(this).prop('disabled', true).html('<i class="bx bx-loader bx-spin me-1"></i>Processing...');

            $.ajax({
                url: "{{ route('expiry.approve_grn_return') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    return_id: currentReturnId,
                    remarks: remarks
                },
                success: function(response) {
                    if (response.status) {
                        $('#approveModal').modal('hide');
                        alert(response.message);
                        grn_returns_table.ajax.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                    $("#btn_confirm_approve").prop('disabled', false).html('<i class="bx bx-check me-1"></i>Approve');
                },
                error: function(xhr) {
                    var errorMessage = 'Error approving return';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                    $("#btn_confirm_approve").prop('disabled', false).html('<i class="bx bx-check me-1"></i>Approve');
                }
            });
        });

        // Confirm reject
        $("#btn_confirm_reject").on("click", function() {
            var remarks = $('#reject_remarks').val();

            if (!remarks.trim()) {
                alert('Please provide a rejection reason.');
                return;
            }

            $(this).prop('disabled', true).html('<i class="bx bx-loader bx-spin me-1"></i>Processing...');

            $.ajax({
                url: "{{ route('expiry.reject_grn_return') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    return_id: currentReturnId,
                    remarks: remarks
                },
                success: function(response) {
                    if (response.status) {
                        $('#rejectModal').modal('hide');
                        alert(response.message);
                        grn_returns_table.ajax.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                    $("#btn_confirm_reject").prop('disabled', false).html('<i class="bx bx-x me-1"></i>Reject');
                },
                error: function(xhr) {
                    var errorMessage = 'Error rejecting return';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                    $("#btn_confirm_reject").prop('disabled', false).html('<i class="bx bx-x me-1"></i>Reject');
                }
            });
        });

        // Delete return
        $(document).on('click', '.btn-delete', function() {
            if (!confirm('Are you sure you want to delete this return request?')) {
                return;
            }

            var returnId = $(this).data('id');
            var btn = $(this);
            btn.prop('disabled', true);

            $.ajax({
                url: "{{ route('expiry.delete_grn_return') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    return_id: returnId
                },
                success: function(response) {
                    if (response.status) {
                        alert(response.message);
                        grn_returns_table.ajax.reload();
                    } else {
                        alert('Error: ' + response.message);
                        btn.prop('disabled', false);
                    }
                },
                error: function(xhr) {
                    var errorMessage = 'Error deleting return';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                    btn.prop('disabled', false);
                }
            });
        });
    });
</script>
@endpush