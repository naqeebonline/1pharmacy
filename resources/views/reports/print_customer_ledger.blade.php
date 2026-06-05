<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Customer Ledger</title>
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

        .ledger {
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
            width: 92px;
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

        .header-right .info-line.highlight .value {
            font-weight: 800;
            font-size: 11px;
        }

        table.ledger-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.ledger-table th,
        table.ledger-table td {
            border: 1px solid #333;
            padding: 5px 6px;
            font-size: 10px;
            vertical-align: middle;
        }

        table.ledger-table th {
            background: #f0f0f0;
            text-align: center;
            font-weight: 700;
        }

        table.ledger-table td.num {
            text-align: right;
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
        }

        table.ledger-table td.center {
            text-align: center;
        }

        table.ledger-table tbody tr:nth-child(even) {
            background: #fafafa;
        }

        .opening-row td {
            background: #eef5ff !important;
            font-weight: 700;
        }

        .totals-row td {
            background: #f0f0f0 !important;
            font-weight: 700;
        }

        .text-debit {
            color: #111;
        }

        .text-credit {
            color: #111;
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
    <div class="ledger">
        <div class="company-bar">
            <h1>{{ $company_name }}</h1>
        </div>

        <div class="header-info">
            <p class="report-title">Customer Ledger Report</p>

            <div class="header-details">
                <div class="header-left">
                    <div class="info-line">
                        <span class="label">Customer Name</span>
                        <span class="value">{{ $patient->name ?? '-' }}</span>
                    </div>
                    <div class="info-line">
                        <span class="label">Period</span>
                        <span class="value">
                            {{ \Carbon\Carbon::parse($start_date)->format('d-m-Y') }}
                            to
                            {{ \Carbon\Carbon::parse($end_date)->format('d-m-Y') }}
                        </span>
                    </div>
                    <div class="info-line">
                        <span class="label">Printed On</span>
                        <span class="value">{{ now()->format('d-m-Y h:i A') }}</span>
                    </div>
                </div>

                <div class="header-right">
                    <div class="info-line">
                        <span class="label">Opening Balance</span>
                        <span class="value">{{ number_format($opening_balance, 2) }}</span>
                    </div>
                    <!-- <div class="info-line">
                        <span class="label">Total Bills (Dr)</span>
                        <span class="value text-debit">{{ number_format($total_debit, 2) }}</span>
                    </div>
                    <div class="info-line">
                        <span class="label">Total Payments (Cr)</span>
                        <span class="value text-credit">{{ number_format($total_credit, 2) }}</span>
                    </div> -->
                    <div class="info-line highlight">
                        <span class="label">Closing Balance</span>
                        <span class="value">{{ number_format($closing_balance, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <table class="ledger-table">
            <thead>
                <tr>
                    <th style="width:36px;">#</th>
                    <th style="width:78px;">Date</th>
                    <th>Particulars</th>
                    <th style="width:110px;">Reference</th>
                    <th style="width:88px;">Bill (Dr)</th>
                    <th style="width:88px;">Payment (Cr)</th>
                    <th style="width:96px;">Balance</th>
                </tr>
            </thead>
            <tbody>
                <tr class="opening-row">
                    <td class="center">-</td>
                    <td class="center">{{ \Carbon\Carbon::parse($start_date)->format('d-m-Y') }}</td>
                    <td>Opening Balance (before selected period)</td>
                    <td class="center">-</td>
                    <td class="num">-</td>
                    <td class="num">-</td>
                    <td class="num">{{ number_format($opening_balance, 2) }}</td>
                </tr>

                @forelse($entries as $index => $entry)
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td class="center">{{ \Carbon\Carbon::parse($entry['date'])->format('d-m-Y') }}</td>
                        <td>{{ $entry['particulars'] }}</td>
                        <td class="center">{{ $entry['reference'] }}</td>
                        <td class="num text-debit">
                            {{ $entry['debit'] > 0 ? number_format($entry['debit'], 2) : '-' }}
                        </td>
                        <td class="num text-credit">
                            {{ $entry['credit'] > 0 ? number_format($entry['credit'], 2) : '-' }}
                        </td>
                        <td class="num">{{ number_format($entry['balance'], 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="center" style="padding: 14px;">No transactions found for the selected period.</td>
                    </tr>
                @endforelse

                <tr class="totals-row">
                    <td colspan="4" class="center">Period Totals</td>
                    <td class="num text-debit">{{ number_format($total_debit, 2) }}</td>
                    <td class="num text-credit">{{ number_format($total_credit, 2) }}</td>
                    <td class="num">{{ number_format($closing_balance, 2) }}</td>
                </tr>
            </tbody>
        </table>

       
    </div>
</body>

</html>
