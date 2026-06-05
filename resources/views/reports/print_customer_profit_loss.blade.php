<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Customer Profit &amp; Loss</title>
    <script>
        window.addEventListener('load', function () {
            setTimeout(function () {
                window.print();
            }, 300);
        });
    </script>
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #111;
            font-size: 11px;
            line-height: 1.35;
            margin: 0;
            padding: 0;
        }

        * {
            box-sizing: border-box;
        }

        .report {
            width: 100%;
        }

        .company-bar {
            text-align: center;
            padding: 0 0 6px;
        }

        .company-bar h1 {
            margin: 0;
            font-size: 17px;
            letter-spacing: 0.3px;
        }

        .report-title {
            margin: 0 0 8px;
            font-size: 12px;
            font-weight: 700;
            color: #111;
            text-align: center;
        }

        .header-info {
            padding: 4px 0 10px;
            margin-bottom: 8px;
            border-bottom: 2px solid #111;
        }

        .header-details {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 24px;
        }

        .header-left,
        .header-right {
            width: 50%;
        }

        .header-left {
            text-align: left;
        }

        .header-right {
            text-align: right;
        }

        .info-line {
            display: flex;
            align-items: baseline;
            margin-bottom: 4px;
            font-size: 10px;
            line-height: 1.5;
        }

        .header-left .info-line .label {
            width: 110px;
            flex-shrink: 0;
            font-weight: 700;
            color: #555;
        }

        .header-left .info-line .value {
            flex: 1;
            font-weight: 600;
            color: #111;
        }

        .header-right .info-line {
            justify-content: flex-end;
        }

        .header-right .info-line .label {
            font-weight: 700;
            color: #555;
            margin-right: 10px;
            white-space: nowrap;
        }

        .header-right .info-line .value {
            min-width: 88px;
            font-weight: 600;
            color: #111;
            text-align: right;
            font-variant-numeric: tabular-nums;
        }

        .summary-box {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin: 10px 0 12px;
        }

        .summary-card {
            border: 1px solid #333;
            padding: 8px 12px;
            min-width: 140px;
            text-align: center;
            background: #f8f8f8;
        }

        .summary-card .label {
            font-size: 9px;
            font-weight: 700;
            color: #555;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .summary-card .value {
            font-size: 13px;
            font-weight: 800;
            font-variant-numeric: tabular-nums;
        }

        .summary-card.profit .value {
            color: {{ $total_profit >= 0 ? '#0a6b0a' : '#b00020' }};
        }

        .tax-collected-section {
            margin: 0 0 14px;
            padding: 10px 12px;
            border: 1px solid #333;
            background: #fafafa;
        }

        .tax-collected-section h3 {
            margin: 0 0 8px;
            font-size: 11px;
            font-weight: 700;
            text-align: center;
        }

        .tax-collected-section .note {
            margin: 0 0 8px;
            font-size: 9px;
            color: #666;
            text-align: center;
        }

        .tax-collected-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px 24px;
        }

        .tax-collected-item {
            font-size: 11px;
            font-weight: 600;
        }

        .tax-collected-item .tax-name {
            color: #333;
        }

        .tax-collected-item .tax-amount {
            font-variant-numeric: tabular-nums;
            font-weight: 800;
            margin-left: 6px;
        }

        .tax-collected-total {
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px solid #333;
            text-align: center;
            font-size: 12px;
            font-weight: 800;
        }

        .tax-collected-total .tax-amount {
            font-variant-numeric: tabular-nums;
            margin-left: 8px;
        }

        table.report-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.report-table th,
        table.report-table td {
            border: 1px solid #333;
            padding: 5px 6px;
            font-size: 10px;
            vertical-align: middle;
        }

        table.report-table th {
            background: #f0f0f0;
            text-align: center;
            font-weight: 700;
        }

        table.report-table td.num {
            text-align: right;
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
        }

        table.report-table td.center {
            text-align: center;
        }

        table.report-table tbody tr:nth-child(even) {
            background: #fafafa;
        }

        .totals-row td {
            background: #f0f0f0 !important;
            font-weight: 700;
        }

        .formula-note {
            margin-top: 8px;
            font-size: 9px;
            color: #666;
        }

        .footer-note {
            margin-top: 6px;
            font-size: 9px;
            color: #666;
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="report">
        <div class="company-bar">
            <h1>{{ $company_name }}</h1>
        </div>

        <div class="header-info">
            <p class="report-title">Customer Profit &amp; Loss Report</p>

            <div class="header-details">
                <div class="header-left">
                    <div class="info-line">
                        <span class="label">Customer Name</span>
                        <span class="value">{{ $patient->name ?? '' }}</span>
                    </div>
                    <div class="info-line">
                        <span class="label">MR#</span>
                        <span class="value">{{ $patient->mr_no ?? '-' }}</span>
                    </div>
                    <div class="info-line">
                        <span class="label">Period</span>
                        <span class="value">{{ $start_date }} to {{ $end_date }}</span>
                    </div>
                    @if(!empty($invoice_no))
                        <div class="info-line">
                            <span class="label">Invoice No</span>
                            <span class="value">{{ $invoice_no }}</span>
                        </div>
                    @endif
                    @if(!empty($medicine_type))
                        <div class="info-line">
                            <span class="label">Medicine Type</span>
                            <span class="value">{{ $medicine_type }}</span>
                        </div>
                    @endif
                </div>
                <div class="header-right">
                    <div class="info-line">
                        <span class="label">Printed On</span>
                        <span class="value">{{ now()->format('d-M-Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="summary-box">
            <div class="summary-card">
                <div class="label">Total Sale</div>
                <div class="value">{{ number_format($total_sale, 2) }}</div>
            </div>
            <div class="summary-card">
                <div class="label">Cost of Goods Sold</div>
                <div class="value">{{ number_format($total_cogs, 2) }}</div>
            </div>
            <div class="summary-card profit">
                <div class="label">Profit / Loss</div>
                <div class="value">{{ number_format($total_profit, 2) }}</div>
            </div>
        </div>

        @if(!empty($tax_collected))
            <div class="tax-collected-section">
                <h3>Tax Collected from Customer</h3>
                <p class="note">Shown for information only — not included in profit / loss</p>
                <div class="tax-collected-list">
                    @foreach($tax_collected as $tax)
                        <div class="tax-collected-item">
                            <span class="tax-name">{{ $tax['name'] }}</span>
                            <span class="tax-amount">{{ number_format($tax['amount'], 2) }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="tax-collected-total">
                    <span class="tax-name">Total Tax Collected</span>
                    <span class="tax-amount">{{ number_format($total_tax_collected ?? 0, 2) }}</span>
                </div>
            </div>
        @endif

        <table class="report-table">
            <thead>
                <tr>
                    <th style="width:40px;">S.No</th>
                    <th>Invoice No</th>
                    <th style="width:90px;">Date</th>
                    <th>Medicine Type</th>
                    <th style="width:100px;">Total Sale</th>
                    <th style="width:100px;">COGS</th>
                    <th style="width:100px;">Profit / Loss</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bill_rows as $index => $row)
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td>{{ $row['invoice_no'] }}</td>
                        <td class="center">{{ $row['date'] }}</td>
                        <td>{{ $row['medicine_type'] ?: '-' }}</td>
                        <td class="num">{{ number_format($row['total_sale'], 2) }}</td>
                        <td class="num">{{ number_format($row['cogs'], 2) }}</td>
                        <td class="num">{{ number_format($row['profit'], 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="center" style="padding:14px;">No bills found for selected filters.</td>
                    </tr>
                @endforelse
            </tbody>
            @if(count($bill_rows) > 0)
                <tfoot>
                    <tr class="totals-row">
                        <td colspan="4" class="center">Grand Total</td>
                        <td class="num">{{ number_format($total_sale, 2) }}</td>
                        <td class="num">{{ number_format($total_cogs, 2) }}</td>
                        <td class="num">{{ number_format($total_profit, 2) }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>

        <p class="formula-note">
            Total Sale = Sum of (sale.net_amount − sale.tax_amount) per bill &nbsp;|&nbsp;
            COGS = Sum of (Quantity − Return Quantity) × Purchase Price from sale details &nbsp;|&nbsp;
            Profit = Total Sale − COGS &nbsp;|&nbsp;
            Tax collected = Sum of row Sale Tax % and Income Tax % from sale details (informational only)
        </p>

        <p class="footer-note">Generated from Customer Bills</p>
    </div>
</body>

</html>
