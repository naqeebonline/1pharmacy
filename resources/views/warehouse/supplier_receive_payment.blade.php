@extends('layouts.' . config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('stylesheets')
    <style>
        .table> :not(caption)>*>* { padding: 5px; }
        #balance_box { font-size: 18px; font-weight: 700; }
    </style>

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form class="form-submit-event" id="supplier_receive_payment_form">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 col-sm-6 mb-3">
                                <label class="form-label">Supplier<span class="asterisk">*</span></label>
                                <select name="SCID" id="SCID" class="form-control" required>
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $s)
                                        <option value="{{ $s->SCID }}">
                                            {{ $s->Name }} (ID: {{ $s->SCID }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 col-sm-6 mb-3">
                                <label class="form-label">Paid Amount<span class="asterisk">*</span></label>
                                <input type="number" step="0.01" min="0" name="amount" id="amount" class="form-control" required autocomplete="off" />
                            </div>

                            <div class="col-md-5 col-sm-12 mb-3">
                                <label class="form-label">Remarks</label>
                                <input type="text" name="remarks" id="remarks" class="form-control" autocomplete="off" maxlength="255" />
                            </div>

                            <div class="col-12 mt-2">
                                <button class="btn btn-success" id="submit_btn">Pay Supplier</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card my-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="card-title">Supplier Balance</h5>
                            <div id="balance_box">Balance: <span id="balance_value">0</span></div>
                        </div>
                        <div class="col-md-6 text-end">
                            <small class="text-muted">Balance is loaded using supplier (SCID)</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card my-4">
                <div class="card-body">
                    <h5 class="card-title">Previous Payments</h5>

                    <div class="row mb-3">
                        <div class="col-md-3 col-sm-6 mb-2">
                            <label class="form-label">From Date</label>
                            <input type="date" id="from_date" class="form-control" />
                        </div>
                        <div class="col-md-3 col-sm-6 mb-2">
                            <label class="form-label">To Date</label>
                            <input type="date" id="to_date" class="form-control" />
                        </div>
                        <div class="col-md-6 col-sm-12 d-flex align-items-end gap-2 mb-2">
                            <button type="button" class="btn btn-primary" id="btn_filter_payments">Filter</button>
                            <button type="button" class="btn btn-label-secondary" id="btn_reset_payments">Reset</button>
                        </div>
                    </div>

                    <div class="table-responsive" style="min-height: 150px">
                        <table class="table table-striped table-condensed" id="payments_table">
                            <thead>
                                <tr>
                                    <th width="10%">ID</th>
                                    <th width="15%">Date</th>
                                    <th width="15%">Amount</th>
                                    <th>Remarks</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="edit_payment_modal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <form class="modal-content form-submit-event" id="edit_payment_form">
                        @csrf
                        <input type="hidden" name="payment_id" id="edit_payment_id" value="">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Payment</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Amount<span class="asterisk">*</span></label>
                                    <input type="number" step="0.01" min="0" name="amount" id="edit_amount" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Remarks</label>
                                    <input type="text" name="remarks" id="edit_remarks" maxlength="255" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="edit_submit_btn">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.form.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-responsive/datatables.responsive.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.js') }}"></script>

    <script>
        setTimeout(function() {
            $('#SCID').select2();
        }, 300);

        let payments_table = null;

        function initPaymentsTable() {
            if (payments_table) return;
            payments_table = $('#payments_table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 10,
                ajax: {
                    url: "{{ route('pos.supplier_receive_payment_list') }}",
                    data: function (d) {
                        d.SCID = $('#SCID').val();
                        d.from_date = $('#from_date').val();
                        d.to_date = $('#to_date').val();
                    }
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'amount', name: 'amount'},
                    {data: 'remarks', name: 'remarks'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ],
                responsive: true,
                searching: true,
                sorting: true,
                paging: true,
            });
        }

        function reloadPaymentsTable() {
            if (!payments_table) return;
            payments_table.ajax.reload(null, false);
        }

        function loadBalance(scid) {
            if (!scid) {
                $('#balance_value').text('0');
                return;
            }
            const balanceUrlBase = "{{ route('pos.supplier_previous_balance') }}";
            $.get(balanceUrlBase + '/' + scid, function (res) {
                $('#balance_value').text(res ?? 0);
            }).fail(function () {
                $('#balance_value').text('0');
            });
        }

        $('#SCID').on('change', function () {
            const scid = $(this).val();
            $('#balance_value').text('...');
            initPaymentsTable();
            reloadPaymentsTable();
            loadBalance(scid);
        });

        $('#btn_filter_payments').on('click', function () {
            initPaymentsTable();
            reloadPaymentsTable();
        });

        $('#btn_reset_payments').on('click', function () {
            $('#from_date').val('');
            $('#to_date').val('');
            initPaymentsTable();
            reloadPaymentsTable();
        });

        $('#supplier_receive_payment_form').ajaxForm({
            url: '{{ url("store-supplier-receive-payment") }}',
            type: 'POST',
            dataType: 'json',
            beforeSubmit: function() {
                $('#submit_btn').prop('disabled', true);
            },
            success: function(res) {
                $('#submit_btn').prop('disabled', false);
                if (res && res.status) {
                    const scid = $('#SCID').val();
                    $('#amount').val('');
                    $('#remarks').val('');
                    initPaymentsTable();
                    reloadPaymentsTable();
                    loadBalance(scid);
                }
            },
            error: function() {
                $('#submit_btn').prop('disabled', false);
            }
        });

        $(document).on('click', '.edit_payment', function () {
            const id = $(this).data('id');
            $.get('{{ url("supplier-receive-payment/payment") }}/' + id, function (res) {
                if (res && res.status) {
                    $('#edit_payment_id').val(res.data.id);
                    $('#edit_amount').val(res.data.amount);
                    $('#edit_remarks').val(res.data.remarks ?? '');
                    $('#edit_payment_modal').modal('show');
                }
            });
        });

        $('#edit_payment_form').ajaxForm({
            url: '{{ url("supplier-receive-payment/payment/update") }}',
            type: 'POST',
            dataType: 'json',
            beforeSubmit: function() {
                $('#edit_submit_btn').prop('disabled', true);
            },
            success: function(res) {
                $('#edit_submit_btn').prop('disabled', false);
                if (res && res.status) {
                    $('#edit_payment_modal').modal('hide');
                    const scid = $('#SCID').val();
                    initPaymentsTable();
                    reloadPaymentsTable();
                    loadBalance(scid);
                }
            },
            error: function() {
                $('#edit_submit_btn').prop('disabled', false);
            }
        });

        $(document).on('click', '.delete_payment', function () {
            const id = $(this).data('id');
            if (!confirm('Are you sure you want to delete this payment?')) return;

            $.post('{{ url("supplier-receive-payment/payment/delete") }}', {
                _token: '{{ csrf_token() }}',
                payment_id: id
            }, function (res) {
                if (res && res.status) {
                    const scid = $('#SCID').val();
                    initPaymentsTable();
                    reloadPaymentsTable();
                    loadBalance(scid);
                }
            });
        });

        $(document).on('click', '.approve_payment', function () {
            const id = $(this).data('id');
            if (!confirm('Approve this payment?')) return;

            $.post('{{ url("supplier-receive-payment/payment/approve") }}', {
                _token: '{{ csrf_token() }}',
                payment_id: id
            }, function (res) {
                if (res && res.status) {
                    const scid = $('#SCID').val();
                    initPaymentsTable();
                    reloadPaymentsTable();
                    loadBalance(scid);
                } else if (res && res.message) {
                    alert(res.message);
                }
            });
        });
    </script>
@endpush
