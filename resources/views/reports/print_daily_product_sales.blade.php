<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Sales Report - {{ $from_date }} to {{ $to_date }}</title>
    <style>
        body <tr><th style="width: 2%">#</th><th style="width: 20%">Product Name</th><th style="width: 6%">Qty Sold</th><th style="width: 6%">Returned</th><th style="width: 9%">Sale Amount</th><th style="width: 9%">Purchase Amount</th><th style="width: 9%">Revenue</th><th style="width: 8%">Cost</th><th style="width: 8%">Discount</th><th style="width: 8%">Gross Profit</th><th style="width: 8%">Net Profit</th><th style="width: 7%">Margin %</th></tr>font-family: Arial,
        sans-serif;
        margin: 0;
        padding: 20px;
        color: #333;
        line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            color: #2c3e50;
            font-size: 24px;
        }

        .header h2 {
            margin: 5px 0;
            color: #7f8c8d;
            font-weight: normal;
            font-size: 16px;
        }

        .report-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }

        .report-info div {
            text-align: center;
        }

        .report-info .label {
            font-weight: bold;
            color: #495057;
            font-size: 12px;
            text-transform: uppercase;
        }

        .report-info .value {
            font-size: 18px;
            color: #2c3e50;
            margin-top: 5px;
        }

        .summary-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }

        .summary-card {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            text-align: center;
        }

        .summary-card h3 {
            margin: 0 0 10px 0;
            font-size: 24px;
            color: #2c3e50;
        }

        .summary-card p {
            margin: 0;
            color: #6c757d;
            font-size: 12px;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 11px;
        }

        th,
        td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #343a40;
            color: white;
            font-weight: bold;
            text-align: center;
        }

        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tbody tr:hover {
            background-color: #e9ecef;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .profit-positive {
            color: #28a745;
            font-weight: bold;
        }

        .profit-negative {
            color: #dc3545;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            color: #6c757d;
            font-size: 12px;
        }

        tfoot {
            background-color: #e9ecef;
            font-weight: bold;
        }

        tfoot td {
            border-top: 2px solid #343a40;
        }

        @media print {
            body {
                margin: 0;
                padding: 15px;
            }

            .summary-cards {
                grid-template-columns: repeat(4, 1fr);
                page-break-inside: avoid;
            }

            table {
                font-size: 10px;
            }

            th,
            td {
                padding: 6px;
            }
        }

        .no-print {
            display: none;
        }

        @media screen {
            .no-print {
                display: block;
                text-align: center;
                margin-bottom: 20px;
            }

            .print-btn {
                background-color: #007bff;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 5px;
                cursor: pointer;
                font-size: 16px;
            }

            .print-btn:hover {
                background-color: #0056b3;
            }
        }
    </style>
</head>

<body>
    <div class="no-print">
        <button class="print-btn" onclick="window.print()">🖨️ Print Report</button>
    </div>

    <div class="header">
        <h1>Daily Product-wise Sales Report</h1>
        <h2>Date Range: {{ date('d M Y', strtotime($from_date)) }} to {{ date('d M Y', strtotime($to_date)) }}</h2>
    </div>

    <div class="report-info">
        <div>
            <div class="label">Report Generated</div>
            <div class="value">{{ $report_generated_at }}</div>
        </div>
        <div>
            <div class="label">Total Products</div>
            <div class="value">{{ $total_products }}</div>
        </div>
        <div>
            <div class="label">Date Range</div>
            <div class="value">{{ \Carbon\Carbon::parse($from_date)->diffInDays(\Carbon\Carbon::parse($to_date)) + 1 }} Days</div>
        </div>
    </div>

    <div class="summary-cards">
        <div class="summary-card">
            <h3>{{ number_format($total_revenue, 2) }}</h3>
            <p>Total Revenue</p>
        </div>
        <div class="summary-card">
            <h3>{{ number_format($total_cost, 2) }}</h3>
            <p>Total Cost</p>
        </div>
        <div class="summary-card">
            <h3>{{ number_format($total_discount ?? 0, 2) }}</h3>
            <p>Total Discount</p>
        </div>
        <div class="summary-card">
            <h3>{{ number_format($gross_profit ?? 0, 2) }}</h3>
            <p>Gross Profit</p>
        </div>
        <div class="summary-card">
            <h3 class="{{ $total_profit >= 0 ? 'profit-positive' : 'profit-negative' }}">
                {{ number_format($total_profit, 2) }}
            </h3>
            <p>Total Profit</p>
        </div>
        <div class="summary-card">
            <h3>{{ $total_revenue > 0 ? number_format(($total_profit / $total_revenue) * 100, 1) : 0 }}%</h3>
            <p>Profit Margin</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 3%">#</th>
                <th style="width: 18%">Product Name</th>
                <th style="width: 6%">Net Qty</th>
                <th style="width: 6%">Returned</th>
                <th style="width: 8%">Sale Amount</th>
                <th style="width: 8%">Purchase Amount</th>
                <th style="width: 8%">Revenue</th>
                <th style="width: 8%">Cost</th>
                <th style="width: 8%">Discount</th>
                <th style="width: 8%">Gross Profit</th>
                <th style="width: 8%">Net Profit</th>
                <th style="width: 6%">Margin %</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $index => $product)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td><strong>{{ $product->ProductName }}</strong></td>
                <td class="text-center">{{ number_format($product->net_quantity) }}</td>
                <td class="text-center">{{ number_format($product->returned_quantity ?? 0) }}</td>
                <td class="text-right"><strong>{{ number_format($product->total_sale_amount ?? 0, 2) }}</strong></td>
                <td class="text-right">{{ number_format($product->total_purchase_amount ?? 0, 2) }}</td>
                <td class="text-right"><strong>{{ number_format($product->total_revenue, 2) }}</strong></td>
                <td class="text-right">{{ number_format($product->total_cost, 2) }}</td>
                <td class="text-right text-warning">{{ number_format($product->total_discount ?? 0, 2) }}</td>
                <td class="text-right text-info">{{ number_format($product->gross_profit ?? 0, 2) }}</td>
                <td class="text-right {{ $product->total_profit >= 0 ? 'profit-positive' : 'profit-negative' }}">
                    <strong>{{ number_format($product->total_profit, 2) }}</strong>
                </td>
                <td class="text-right {{ $product->total_profit >= 0 ? 'profit-positive' : 'profit-negative' }}">
                    <strong>{{ $product->total_revenue > 0 ? number_format(($product->total_profit / $product->total_revenue) * 100, 1) : 0 }}%</strong>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="12" class="text-center" style="padding: 30px; color: #6c757d;">
                    No sales data found for the selected date range
                </td>
            </tr>
            @endforelse
        </tbody>
        @if($products->count() > 0)
        <tfoot>
            <tr>
                <td colspan="2" class="text-center"><strong>TOTALS</strong></td>
                <td class="text-center"><strong>{{ number_format($products->sum('net_quantity')) }}</strong></td>
                <td class="text-center"><strong>{{ number_format($products->sum('returned_quantity')) }}</strong></td>
                <td class="text-right"><strong>{{ number_format($products->sum('total_sale_amount'), 2) }}</strong></td>
                <td class="text-right"><strong>{{ number_format($products->sum('total_purchase_amount'), 2) }}</strong></td>
                <td class="text-right"><strong>{{ number_format($total_revenue, 2) }}</strong></td>
                <td class="text-right"><strong>{{ number_format($total_cost, 2) }}</strong></td>
                <td class="text-right" style="color: #ffc107;"><strong>{{ number_format($total_discount ?? 0, 2) }}</strong></td>
                <td class="text-right" style="color: #17a2b8;"><strong>{{ number_format($gross_profit ?? 0, 2) }}</strong></td>
                <td class="text-right {{ $total_profit >= 0 ? 'profit-positive' : 'profit-negative' }}">
                    <strong>{{ number_format($total_profit, 2) }}</strong>
                </td>
                <td class="text-right {{ $total_profit >= 0 ? 'profit-positive' : 'profit-negative' }}">
                    <strong>{{ $total_revenue > 0 ? number_format(($total_profit / $total_revenue) * 100, 1) : 0 }}%</strong>
                </td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer">
        <p>This report was generated automatically from the Hospital Management System</p>
        <p>© {{ date('Y') }} Hospital Management System. All rights reserved.</p>
    </div>

    <script>
        // Auto-print when opened in new window
        if (window.location.search.includes('auto_print=1')) {
            window.onload = function() {
                setTimeout(function() {
                    window.print();
                }, 500);
            };
        }
    </script>
</body>

</html>