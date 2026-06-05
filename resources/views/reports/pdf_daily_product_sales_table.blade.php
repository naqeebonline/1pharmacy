<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product-wise Sales Details</title>
    <style>
        @page { size: A4 landscape; margin: 8mm; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 8px; color: #222; margin: 0; padding: 0; }
        h1 { font-size: 14px; margin: 0 0 4px; text-align: center; }
        .meta { text-align: center; font-size: 9px; margin-bottom: 10px; color: #444; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 4px 3px; }
        th { background: #343a40; color: #fff; font-size: 7.5px; text-align: center; }
        td { font-size: 7.5px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        tfoot td { background: #e9ecef; font-weight: bold; }
        .profit-positive { color: #28a745; }
        .profit-negative { color: #dc3545; }
    </style>
</head>
<body>
    <h1>Product-wise Sales Details</h1>
    <div class="meta">
        {{ config('app.COMPANY_NAME') }} &nbsp;|&nbsp;
        Period: {{ date('d M Y', strtotime($from_date)) }} to {{ date('d M Y', strtotime($to_date)) }} &nbsp;|&nbsp;
        Generated: {{ $report_generated_at }}
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th style="width: 14%; text-align: left;">Product Name</th>
                <th>Qty Sold</th>
                <th>Qty Ret.</th>
                <th>Net Qty</th>
                <th>Sale Amt</th>
                <th>Purch. Amt</th>
                <th>Revenue</th>
                <th>Cost</th>
                <th>Discount</th>
                <th>Gross Profit</th>
                <th>Net Profit</th>
                <th>Margin %</th>
                <th>Trans.</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $index => $product)
                @php
                    $qtySold = ($product->gross_quantity ?? $product->total_quantity_sold ?? 0) - ($product->total_returned ?? 0);
                    $profitClass = ($product->total_profit ?? 0) >= 0 ? 'profit-positive' : 'profit-negative';
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $product->ProductName }}</strong></td>
                    <td class="text-center">{{ number_format($qtySold, 0) }}</td>
                    <td class="text-center">{{ number_format($product->total_returned ?? 0, 0) }}</td>
                    <td class="text-center"><strong>{{ number_format($product->total_quantity_sold ?? 0, 0) }}</strong></td>
                    <td class="text-right">{{ number_format($product->total_sale_amount ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($product->total_purchase_amount ?? 0, 2) }}</td>
                    <td class="text-right"><strong>{{ number_format($product->total_revenue ?? 0, 2) }}</strong></td>
                    <td class="text-right">{{ number_format($product->total_cost ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($product->total_discount ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($product->gross_profit ?? 0, 2) }}</td>
                    <td class="text-right {{ $profitClass }}"><strong>{{ number_format($product->total_profit ?? 0, 2) }}</strong></td>
                    <td class="text-right {{ $profitClass }}">{{ number_format($product->profit_margin ?? 0, 2) }}%</td>
                    <td class="text-center">{{ $product->total_transactions ?? 0 }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="14" class="text-center" style="padding: 16px;">No data found for the selected date range.</td>
                </tr>
            @endforelse
        </tbody>
        @if($products->count() > 0)
        <tfoot>
            <tr>
                <td colspan="2" class="text-center">TOTAL</td>
                <td class="text-center">{{ number_format(($summary['gross_quantity'] ?? 0) - ($summary['total_returned'] ?? 0), 0) }}</td>
                <td class="text-center">{{ number_format($summary['total_returned'] ?? 0, 0) }}</td>
                <td class="text-center">{{ number_format($summary['total_quantity_sold'] ?? 0, 0) }}</td>
                <td class="text-right">{{ number_format($summary['total_sale_amount'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($summary['total_purchase_amount'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($summary['total_revenue'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($summary['total_cost'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($summary['total_discount'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($summary['gross_profit'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($summary['total_profit'] ?? 0, 2) }}</td>
                <td class="text-right">{{ number_format($summary['avg_profit_margin'] ?? 0, 2) }}%</td>
                <td class="text-center">{{ $summary['total_transactions'] ?? 0 }}</td>
            </tr>
        </tfoot>
        @endif
    </table>
</body>
</html>
