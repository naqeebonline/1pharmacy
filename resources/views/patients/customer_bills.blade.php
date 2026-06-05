@extends('layouts.' . config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('stylesheets')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />

    <style>
        .table> :not(caption)>*>* { padding: 5px; }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3 col-sm-6 mb-2">
                            <label class="form-label">Invoice No</label>
                            <input type="text" id="invoice_no" class="form-control" placeholder="Search invoice..." />
                        </div>

                        <div class="col-md-3 col-sm-6 mb-2">
                            <label class="form-label">Customer</label>
                            <select id="patient_id" class="form-control">
                                <option value="">All Customers</option>
                                @foreach($customers as $c)
                                    <option data-finance-head-id="{{ $c->finance_head_id }}" value="{{ $c->id }}">
                                        {{ $c->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 col-sm-6 mb-2">
                            <label class="form-label">From Date</label>
                            <input type="date" id="from_date" class="form-control" value="{{ date('Y-m-d') }}" />
                        </div>

                        <div class="col-md-3 col-sm-6 mb-2">
                            <label class="form-label">To Date</label>
                            <input type="date" id="to_date" class="form-control" value="{{ date('Y-m-d') }}" />
                        </div>

                        <div class="col-md-3 col-sm-6 mb-2">
                            <label class="form-label">Medicine Type</label>
                            <select class="form-select" id="medicine_type">
                                <option value="" selected="">All</option>
                                <?php foreach ($medicine_types as $key => $value) { ?>
                        <option value="<?php echo $value->value; ?>"  ><?php echo ucfirst($value->name); ?>  </option>
                    <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mb-3">
                        <button type="button" class="btn btn-label-secondary" id="btn_reset">Reset</button>
                        <button type="button" class="btn btn-success" id="btn_export_excel">Export Excel</button>
                        <button type="button" class="btn btn-success" id="btn_print">Print</button>
                        <button type="button" class="btn btn-primary" id="btn_print_ledger" disabled>Print Customer Ledger</button>
                        <button type="button" class="btn btn-info" id="btn_print_profit_loss" disabled>Print Customer Profit &amp; Loss</button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-condensed" id="bills_table">
                            <thead>
                                <tr>
                                    <th>InvoiceNo</th>
                                    <th>Medicine Type</th>
                                    
                                    <th>Date</th>
                                    <th>Customer</th>
                                    
                                    <th>TotalSale</th>
                                    <th>Discount</th>
                                    <th>Received</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-responsive/datatables.responsive.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.js') }}"></script>

    <script>
        let bills_table = null;

        const TODAY = "{{ date('Y-m-d') }}";

        // Safety net: if browser cleared date inputs (some autofill/old cache cases)
        // keep them defaulted to today so initial listing loads for current date.
        $(function () {
            if (!$('#from_date').val()) $('#from_date').val(TODAY);
            if (!$('#to_date').val()) $('#to_date').val(TODAY);
        });

        setTimeout(function() {
            $('#patient_id').select2();
        }, 300);

        function initBillsTable() {
            if (bills_table) return;

            bills_table = $('#bills_table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 10,
                ajax: {
                    url: "{{ route('pos.customer_bills_list') }}",
                    data: function (d) {
                        d.invoice_no = $('#invoice_no').val();
                        d.patient_id = $('#patient_id').val();
                        d.from_date = $('#from_date').val();
                        d.to_date = $('#to_date').val();
                        d.medicine_type = $('#medicine_type').val();
                    }
                },
                columns: [
                    {data: 'InvoiceNo', name: 'InvoiceNo'},
                    {data: 'medicine_type', name: 'medicine_type', orderable: false, searchable: false},
                    
                    {data: 'CreatedAt', name: 'CreatedAt'},
                    {data: 'customer', name: 'patient.name', orderable: false},
                    {data: 'TotalSale', name: 'TotalSale'},
                    {data: 'Discount', name: 'Discount'},
                    {data: 'ReceivedAmountFromCustomer', name: 'ReceivedAmountFromCustomer'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false}
                ],
                responsive: true,
                searching: true,
                sorting: true,
                paging: true,
            });
        }

        function reloadBillsTable() {
            if (!bills_table) return;
            bills_table.ajax.reload(null, false);
        }

        function debounce(fn, wait) {
            let t = null;
            return function () {
                const ctx = this;
                const args = arguments;
                clearTimeout(t);
                t = setTimeout(function () {
                    fn.apply(ctx, args);
                }, wait);
            };
        }

        const reloadBillsTableDebounced = debounce(function () {
            initBillsTable();
            reloadBillsTable();
            updateLedgerButtonState();
        }, 300);

        function updateLedgerButtonState() {
            const patientId = $('#patient_id').val();
            const startDate = $('#from_date').val();
            const endDate = $('#to_date').val();

            const ok = !!patientId && !!startDate && !!endDate;
            $('#btn_print_ledger').prop('disabled', !ok);
            $('#btn_print_profit_loss').prop('disabled', !ok);
        }

        $('#btn_reset').on('click', function () {
            $('#invoice_no').val('');
            $('#patient_id').val('').trigger('change');
            $('#from_date').val(TODAY);
            $('#to_date').val(TODAY);
            $('#medicine_type').val('');
            initBillsTable();
            reloadBillsTable();
            updateLedgerButtonState();
        });

        // Auto apply filters
        $('#invoice_no').on('keyup', reloadBillsTableDebounced);
        $('#patient_id').on('change', function () {
            reloadBillsTableDebounced();
            updateLedgerButtonState();
        });
        $('#from_date').on('change', function () {
            reloadBillsTableDebounced();
            updateLedgerButtonState();
        });
        $('#to_date').on('change', function () {
            reloadBillsTableDebounced();
            updateLedgerButtonState();
        });
        $('#medicine_type').on('change', reloadBillsTableDebounced);

        $('#btn_print_ledger').on('click', function () {
            const patientId = $('#patient_id').val();
            const startDate = $('#from_date').val();
            const endDate = $('#to_date').val();

            if (!patientId || !startDate || !endDate) {
                updateLedgerButtonState();
                return;
            }

            const baseUrl = "{{ route('pos.customer_ledger_report') }}";
            const url = baseUrl
                + '?patient_id=' + encodeURIComponent(patientId)
                + '&start_date=' + encodeURIComponent(startDate)
                + '&end_date=' + encodeURIComponent(endDate);

            window.open(url, '_blank');
        });

        $('#btn_print_profit_loss').on('click', function () {
            const patientId = $('#patient_id').val();
            const startDate = $('#from_date').val();
            const endDate = $('#to_date').val();

            if (!patientId || !startDate || !endDate) {
                updateLedgerButtonState();
                return;
            }

            const params = {
                patient_id: patientId,
                start_date: startDate,
                end_date: endDate,
                invoice_no: $('#invoice_no').val() || '',
                medicine_type: $('#medicine_type').val() || '',
            };

            const baseUrl = "{{ route('pos.customer_profit_loss_report') }}";
            const query = $.param(params);
            window.open(baseUrl + '?' + query, '_blank');
        });

        $('#btn_print').on('click', function () {
            initBillsTable();

            const companyName = "{{ env('COMPANY_NAME') }}";
            const invoiceNo = $('#invoice_no').val() || '';
            const customerText = $('#patient_id option:selected').text() || 'All Customers';
            const fromDate = $('#from_date').val() || '';
            const toDate = $('#to_date').val() || '';
            const medicineType = $('#medicine_type option:selected').text() || '';

            const oldLen = bills_table.page.len();
            bills_table.page.len(-1).draw();

            // Sum TotalSale from filtered rows
            let total = 0;
            const rows = bills_table.rows({ search: 'applied' }).data();
            for (let i = 0; i < rows.length; i++) {
                const t = parseFloat(rows[i].TotalSale ?? 0);
                total += (isNaN(t) ? 0 : t);
            }

            // Build a clean table for printing (avoid DataTables artefacts/columns)
            const printableRows = bills_table.rows({ search: 'applied', order: 'applied' }).data();
            let bodyHtml = '';
            for (let i = 0; i < printableRows.length; i++) {
                const r = printableRows[i];
                const totalSale = parseFloat(r.TotalSale ?? 0);
                const discount = parseFloat(r.Discount ?? 0);

                bodyHtml += `
                    <tr>
                        <td style="text-align:center;">${i + 1}</td>
                        <td>${r.InvoiceNo ?? ''}</td>
                        <td style="text-align:center;">${r.CreatedAt ?? ''}</td>
                        <td>${r.customer ?? ''}</td>
                        <td style="text-align:right;">${(isNaN(totalSale) ? 0 : totalSale).toFixed(2)}</td>
                        <td style="text-align:right;">${(isNaN(discount) ? 0 : discount).toFixed(2)}</td>
                    </tr>
                `;
            }

            const printTableHtml = `
                <table class="print-table">
                    <thead>
                        <tr>
                            <th style="width:70px;">S.No</th>
                            <th>InvoiceNo</th>
                            <th style="width:120px;">Date</th>
                            <th>Customer</th>
                            <th style="width:120px;">TotalSale</th>
                            <th style="width:120px;">Discount</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${bodyHtml || '<tr><td colspan="6" style="text-align:center; padding:16px;">No records found</td></tr>'}
                    </tbody>
                </table>
            `;

            const html = `
                <html>
                <head>
                    <title>Customer Bills</title>
                    <style>
                        @page { size: A4; margin: 12mm; }
                        body{ font-family: Arial, sans-serif; color:#111; }
                        .header{ text-align:center; margin-bottom: 14px; }
                        .company{ font-weight:700; font-size:18px; line-height: 1.2; }
                        .report-title{ font-weight:700; font-size:14px; margin-top: 4px; }

                        .meta{ margin: 10px 0 14px; font-size: 12px; }
                        .meta .row{ display:flex; justify-content: space-between; gap: 16px; }
                        .meta .row > div{ flex:1; }

                        .print-table{ width:100%; border-collapse: collapse; }
                        .print-table th,
                        .print-table td{ border:1px solid #333; padding:7px 8px; font-size: 12px; }
                        .print-table th{ background:#f0f0f0; text-align:center; }

                        .total{ margin-top: 10px; text-align: right; font-weight: 700; font-size: 13px; }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <div class="company">${companyName}</div>
                        <div class="report-title">Customer Bills</div>
                    </div>
                    <div class="meta">
                        <div class="row">
                            <div><strong>Invoice No:</strong> ${invoiceNo || 'All'}</div>
                            <div><strong>Customer:</strong> ${customerText}</div>
                            <div style="text-align:right;"><strong>Medicine Type:</strong> ${medicineType || 'All'}</div>
                        </div>
                        <div class="row" style="margin-top:6px;">
                            <div><strong>From:</strong> ${fromDate || '-'}</div>
                            <div><strong>To:</strong> ${toDate || '-'}</div>
                            <div style="text-align:right;"><strong>Printed On:</strong> ${(new Date()).toLocaleString()}</div>
                        </div>
                    </div>
                    ${printTableHtml}
                    <div class="total">Total Bills Amount: ${total.toFixed(2)}</div>
                </body>
                </html>
            `;

            const w = window.open('', '_blank');
            w.document.open();
            w.document.write(html);
            w.document.close();
            w.focus();
            w.print();

            bills_table.page.len(oldLen).draw();
        });

        $('#btn_export_excel').on('click', function () {
            const baseUrl = "{{ route('pos.customer_bills_export_excel') }}";

            const params = {
                invoice_no: $('#invoice_no').val(),
                patient_id: $('#patient_id').val(),
                from_date: $('#from_date').val(),
                to_date: $('#to_date').val(),
                medicine_type: $('#medicine_type').val(),
            };

            Object.keys(params).forEach(function (k) {
                if (params[k] === null || params[k] === undefined || params[k] === '') {
                    delete params[k];
                }
            });

            const query = $.param(params);
            const url = query ? (baseUrl + '?' + query) : baseUrl;
            window.location.href = url;
        });

        // initial load
        initBillsTable();
        updateLedgerButtonState();
    </script>
@endpush
