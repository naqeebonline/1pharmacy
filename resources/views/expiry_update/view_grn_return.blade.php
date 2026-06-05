@extends('layouts.' . config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('stylesheets')
<style>
    .info-card {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #dee2e6;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: #6c757d;
    }

    .info-value {
        color: #212529;
    }

    .status-badge {
        padding: 5px 15px;
        border-radius: 4px;
        font-size: 0.9rem;
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

    .table> :not(caption)>*>* {
        padding: 8px;
    }

    .total-row {
        background: #f8f9fa;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bx bx-file me-2"></i>GRN Return Details - #{{ $return->ReturnID }}
                </h5>
                <div>
                    <a href="{{ route('expiry.grn_returns_list') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i>Back to List
                    </a>
                    @if($return->Status == 'Pending')
                    <a href="{{ route('expiry.edit_grn_return', $return->ReturnID) }}" class="btn btn-primary btn-sm">
                        <i class="bx bx-edit me-1"></i>Edit
                    </a>
                    @endif
                </div>
            </div>

            <div class="card-body">
                <!-- Return Information -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-card">
                            <h6 class="mb-3"><i class="bx bx-info-circle me-2"></i>Return Information</h6>
                            <div class="info-row">
                                <span class="info-label">Return ID:</span>
                                <span class="info-value">#{{ $return->ReturnID }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Return Date:</span>
                                <span class="info-value">{{ date('d-M-Y', strtotime($return->ReturnDate)) }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Supplier:</span>
                                <span class="info-value">{{ $return->SupplierName }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Status:</span>
                                <span class="info-value">
                                    @php
                                    $statusClass = [
                                    'Pending' => 'status-pending',
                                    'Approved' => 'status-approved',
                                    'Rejected' => 'status-rejected',
                                    'Completed' => 'status-completed'
                                    ];
                                    $class = $statusClass[$return->Status] ?? 'status-pending';
                                    @endphp
                                    <span class="status-badge {{ $class }}">{{ $return->Status }}</span>
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Total Amount:</span>
                                <span class="info-value text-danger fw-bold">Rs. {{ number_format($return->TotalAmount, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-card">
                            <h6 class="mb-3"><i class="bx bx-user me-2"></i>Additional Details</h6>
                            <div class="info-row">
                                <span class="info-label">Created By:</span>
                                <span class="info-value">{{ $return->CreatedByName ?? 'N/A' }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Created At:</span>
                                <span class="info-value">{{ $return->CreatedAt ? date('d-M-Y h:i A', strtotime($return->CreatedAt)) : 'N/A' }}</span>
                            </div>
                            @if($return->ApprovedBy)
                            <div class="info-row">
                                <span class="info-label">Approved By:</span>
                                <span class="info-value">{{ $return->ApprovedByName ?? 'N/A' }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Approved At:</span>
                                <span class="info-value">{{ $return->ApprovedAt ? date('d-M-Y h:i A', strtotime($return->ApprovedAt)) : 'N/A' }}</span>
                            </div>
                            @endif
                            @if($return->Remarks)
                            <div class="info-row">
                                <span class="info-label">Remarks:</span>
                                <span class="info-value">{{ $return->Remarks }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Return Items -->
                <div class="mt-4">
                    <h6 class="mb-3"><i class="bx bx-package me-2"></i>Return Items</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Product Name</th>
                                    <th>Batch No</th>
                                    <th>Expiry Date</th>
                                    <th>Return Qty</th>
                                    <th>Unit Price</th>
                                    <th>Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($returnDetails as $index => $detail)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $detail->ProductName }}</td>
                                    <td>{{ $detail->BatchNo ?? 'N/A' }}</td>
                                    <td>{{ $detail->ExpiryDate ? date('d-M-Y', strtotime($detail->ExpiryDate)) : 'N/A' }}</td>
                                    <td>{{ number_format($detail->ReturnQuantity, 2) }}</td>
                                    <td>Rs. {{ number_format($detail->UnitPrice, 2) }}</td>
                                    <td>Rs. {{ number_format($detail->TotalAmount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="total-row">
                                    <td colspan="6" class="text-end"><strong>Grand Total:</strong></td>
                                    <td><strong>Rs. {{ number_format($return->TotalAmount, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Action Buttons -->
                @if($return->Status == 'Pending')
                <div class="mt-4 text-end">
                    <button class="btn btn-success" id="btn_approve">
                        <i class="bx bx-check me-1"></i>Approve Return
                    </button>
                    <button class="btn btn-warning" id="btn_reject">
                        <i class="bx bx-x me-1"></i>Reject Return
                    </button>
                </div>
                @endif
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
<script>
    $(document).ready(function() {
        var returnId = {
            {
                $return - > ReturnID
            }
        };

        // Approve button
        $("#btn_approve").on("click", function() {
            $('#approve_remarks').val('');
            $('#approveModal').modal('show');
        });

        // Reject button
        $("#btn_reject").on("click", function() {
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
                    return_id: returnId,
                    remarks: remarks
                },
                success: function(response) {
                    if (response.status) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                        $("#btn_confirm_approve").prop('disabled', false).html('<i class="bx bx-check me-1"></i>Approve');
                    }
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
                    return_id: returnId,
                    remarks: remarks
                },
                success: function(response) {
                    if (response.status) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                        $("#btn_confirm_reject").prop('disabled', false).html('<i class="bx bx-x me-1"></i>Reject');
                    }
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
    });
</script>
@endpush