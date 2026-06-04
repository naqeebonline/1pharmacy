<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GRN Return - {{ $return->ReturnID }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }

        .container {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
            color: #2c3e50;
        }

        .header h2 {
            font-size: 18px;
            color: #e74c3c;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 11px;
            color: #7f8c8d;
        }

        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 20px;
        }

        .info-box {
            flex: 1;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #f8f9fa;
        }

        .info-box h3 {
            font-size: 14px;
            margin-bottom: 10px;
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 5px;
        }

        .info-row {
            display: flex;
            margin-bottom: 5px;
        }

        .info-label {
            font-weight: bold;
            width: 120px;
            color: #555;
        }

        .info-value {
            flex: 1;
            color: #333;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 11px;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .status-completed {
            background: #d1ecf1;
            color: #0c5460;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table thead {
            background: #34495e;
            color: white;
        }

        .items-table th {
            padding: 10px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
            border: 1px solid #2c3e50;
        }

        .items-table td {
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 11px;
        }

        .items-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        .items-table tbody tr:hover {
            background: #e9ecef;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .summary-section {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 13px;
        }

        .summary-row.total {
            border-top: 2px solid #333;
            margin-top: 10px;
            padding-top: 10px;
            font-size: 16px;
            font-weight: bold;
            color: #e74c3c;
        }

        .remarks-section {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #fff9e6;
        }

        .remarks-section h3 {
            font-size: 14px;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #7f8c8d;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 60px;
            padding: 0 40px;
        }

        .signature-box {
            text-align: center;
        }

        .signature-line {
            border-top: 2px solid #333;
            width: 200px;
            margin-bottom: 5px;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none;
            }

            .container {
                max-width: 100%;
            }
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .print-button:hover {
            background: #2980b9;
        }
    </style>
</head>

<body>
    <button class="print-button no-print" onclick="window.print()">
        <i class="bx bx-printer"></i> Print
    </button>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Hospital Name</h1>
            <h2>GOODS RETURN NOTE (GRN Return)</h2>
            <p>Return to Supplier - Expired/Near Expiry Items</p>
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <div class="info-box">
                <h3>Return Information</h3>
                <div class="info-row">
                    <span class="info-label">Return ID:</span>
                    <span class="info-value"><strong>{{ $return->ReturnID }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Return Date:</span>
                    <span class="info-value">{{ date('d-M-Y', strtotime($return->ReturnDate)) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value">
                        <span class="status-badge status-{{ strtolower($return->Status) }}">
                            {{ $return->Status }}
                        </span>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Created By:</span>
                    <span class="info-value">{{ $return->CreatedByName ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Created At:</span>
                    <span class="info-value">{{ date('d-M-Y h:i A', strtotime($return->CreatedAt)) }}</span>
                </div>
            </div>

            <div class="info-box">
                <h3>Supplier Information</h3>
                <div class="info-row">
                    <span class="info-label">Supplier Name:</span>
                    <span class="info-value"><strong>{{ $return->SupplierName }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Address:</span>
                    <span class="info-value">{{ $return->SupplierAddress ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%">#</th>
                    <th style="width: 30%">Product Name</th>
                    <th style="width: 12%">Batch No</th>
                    <th style="width: 12%">Expiry Date</th>
                    <th style="width: 10%" class="text-right">Quantity</th>
                    <th style="width: 12%" class="text-right">Unit Price</th>
                    <th style="width: 14%" class="text-right">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($returnDetails as $index => $detail)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $detail->ProductName }}</td>
                    <td>{{ $detail->BatchNo ?? 'N/A' }}</td>
                    <td>{{ $detail->ExpiryDate ? date('d-M-Y', strtotime($detail->ExpiryDate)) : 'N/A' }}</td>
                    <td class="text-right">{{ number_format($detail->ReturnQuantity, 2) }}</td>
                    <td class="text-right">Rs. {{ number_format($detail->UnitPrice, 2) }}</td>
                    <td class="text-right">Rs. {{ number_format($detail->TotalAmount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary Section -->
        <div class="summary-section">
            <div class="summary-row">
                <span>Total Items:</span>
                <span><strong>{{ count($returnDetails) }}</strong></span>
            </div>
            <div class="summary-row">
                <span>Total Quantity:</span>
                <span><strong>{{ number_format($returnDetails->sum('ReturnQuantity'), 2) }}</strong></span>
            </div>
            <div class="summary-row total">
                <span>GRAND TOTAL:</span>
                <span>Rs. {{ number_format($return->TotalAmount, 2) }}</span>
            </div>
        </div>

        <!-- Remarks Section -->
        @if($return->Remarks)
        <div class="remarks-section">
            <h3>Remarks:</h3>
            <p style="white-space: pre-line;">{{ $return->Remarks }}</p>
        </div>
        @endif

        <!-- Signatures -->
        <div class="signatures">
            <div class="signature-box">
                <div class="signature-line"></div>
                <p><strong>Prepared By</strong></p>
                <p style="font-size: 10px;">{{ $return->CreatedByName ?? '' }}</p>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <p><strong>Approved By</strong></p>
                <p style="font-size: 10px;">Pharmacist/Manager</p>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <p><strong>Received By</strong></p>
                <p style="font-size: 10px;">Supplier Representative</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>This is a computer-generated document. No signature required unless printed.</p>
            <p>Printed on: {{ date('d-M-Y h:i A') }}</p>
        </div>
    </div>

    <script>
        // Auto print on load (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>

</html>