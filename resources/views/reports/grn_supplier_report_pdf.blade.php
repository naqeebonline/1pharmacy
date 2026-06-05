<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>GRN Supplier Report - {{ $supplier->Name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 10px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 18px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 16px;
            color: #333;
        }
        
        .header h2 {
            margin: 4px 0;
            font-size: 13px;
            color: #666;
        }
        
        .header p {
            margin: 2px 0;
            font-size: 10px;
            color: #888;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 8px;
        }
        
        .table th {
            background-color: #343a40;
            color: white;
            padding: 4px 3px;
            text-align: center;
            border: 1px solid #ddd;
            font-weight: bold;
            font-size: 8px;
        }
        
        .table td {
            padding: 3px 2px;
            border: 1px solid #ddd;
            text-align: center;
            font-size: 8px;
        }
        
        .table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .footer-totals {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 8px;
        }
        
        .grand-total {
            background-color: #e9ecef;
            border-top: 2px solid #6c757d;
            font-size: 9px;
        }
        
        .footer {
            margin-top: 18px;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .no-data {
            text-align: center;
            padding: 20px;
            color: #666;
        }
        
        .product-name {
            font-size: 8px;
            font-weight: bold;
        }
        
        small {
            font-size: 7px;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header">
        <h1>Supplier Purchase Report</h1>
        <h2>{{ $supplier->Name }}</h2>
        <p>Supplier ID: {{ $supplier->SCID }} | Contact: {{ $supplier->ContactNo ?? 'N/A' }}</p>
        <p>Address: {{ $supplier->Address ?? $supplier->BusinessAddress ?? 'N/A' }}</p>
        @if($invoice_no)
            <p><strong>Invoice Filter:</strong> {{ $invoice_no }}</p>
        @endif
        @if($from_date || $to_date)
            <p>
                <strong>Date Filter:</strong> 
                {{ $from_date ? date('d-M-Y', strtotime($from_date)) : 'All' }} 
                to 
                {{ $to_date ? date('d-M-Y', strtotime($to_date)) : 'All' }}
            </p>
        @endif
        <p>Report Generated: {{ date('d-M-Y H:i:s') }}</p>
    </div>

    <!-- Products Table -->
    @if($purchaseData->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Invoice No</th>
                    <th>Date</th>
                    <th>Product Name</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Subtotal</th>
                    <th>Discount</th>
                    <th>Adv Tax</th>
                    <th>GST Tax</th>
                    <th>Final Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchaseData as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->InvoiceNo ?? 'N/A' }}</td>
                        <td>{{ $item->Dated ? date('d-M-Y', strtotime($item->Dated)) : 'N/A' }}</td>
                        <td><span class="product-name">{{ $item->ProductName }}</span></td>
                        <td class="text-center">
                            {{ number_format($item->Quantity, 0) }}
                        </td>
                        <td class="text-right">Rs. {{ number_format($item->UnitPrice, 2) }}</td>
                        <td class="text-right">Rs. {{ number_format($item->subtotal, 2) }}</td>
                        <td class="text-right">
                            @if($item->discount > 0)
                                Rs. {{ number_format($item->calculated_discount, 2) }}
                                <small>({{ $item->discount }}%)</small>
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right">
                            @if($item->calculated_advance_tax > 0)
                                Rs. {{ number_format($item->calculated_advance_tax, 2) }}
                                @if($item->advance_tax > 0)
                                    <small>({{ $item->advance_tax }}%)</small>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right">
                            @if($item->calculated_gst_tax > 0)
                                Rs. {{ number_format($item->calculated_gst_tax, 2) }}
                                @if($item->gst_tax > 0)
                                    <small>({{ $item->gst_tax }}%)</small>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right">
                            <strong>Rs. {{ number_format($item->total_amount, 2) }}</strong>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="footer-totals">
                    <td colspan="10" class="text-right"><strong>Total Quantity:</strong></td>
                    <td class="text-center"><strong>{{ number_format($totalItems, 0) }}</strong></td>
                </tr>
                <tr class="footer-totals">
                    <td colspan="10" class="text-right"><strong>Subtotal:</strong></td>
                    <td class="text-right"><strong>Rs. {{ number_format($totalSubtotal, 2) }}</strong></td>
                </tr>
                <tr class="footer-totals">
                    <td colspan="10" class="text-right"><strong>Total Discount:</strong></td>
                    <td class="text-right"><strong>Rs. {{ number_format($totalDiscount, 2) }}</strong></td>
                </tr>
                <tr class="footer-totals">
                    <td colspan="10" class="text-right"><strong>Total Advance Tax:</strong></td>
                    <td class="text-right"><strong>Rs. {{ number_format($totalAdvanceTax, 2) }}</strong></td>
                </tr>
                <tr class="footer-totals">
                    <td colspan="10" class="text-right"><strong>Total GST Tax:</strong></td>
                    <td class="text-right"><strong>Rs. {{ number_format($totalGstTax, 2) }}</strong></td>
                </tr>
                <tr class="grand-total">
                    <td colspan="10" class="text-right"><strong>GRAND TOTAL:</strong></td>
                    <td class="text-right"><strong>Rs. {{ number_format($totalAmount, 2) }}</strong></td>
                </tr>
            </tfoot>
        </table>
    @else
        <div class="no-data">
            <h3>No purchase data found for this supplier</h3>
            <p>No products have been purchased from {{ $supplier->Name }} with the applied filters.</p>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        Report generated on {{ date('d-M-Y H:i:s') }} | Hospital Management System
    </div>
</body>
</html>
