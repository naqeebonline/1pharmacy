<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expiry Report - {{ $report_date }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 18px;
            color: #666;
            margin-bottom: 10px;
        }

        .report-info {
            margin-bottom: 20px;
            background: #f5f5f5;
            padding: 10px;
            border-radius: 5px;
        }

        .report-info p {
            margin: 5px 0;
            font-weight: bold;
        }

        .status-label {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }

        .status-expired {
            background: #dc3545;
            color: white;
        }

        .status-expiring-soon {
            background: #ffc107;
            color: #000;
        }

        .status-near-expiry {
            background: #17a2b8;
            color: white;
        }

        .status-valid {
            background: #28a745;
            color: white;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table thead {
            background: #333;
            color: white;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        table th {
            font-weight: bold;
            font-size: 11px;
        }

        table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        table tbody tr:hover {
            background: #f1f1f1;
        }

        .text-danger {
            color: #dc3545;
            font-weight: bold;
        }

        .text-warning {
            color: #fd7e14;
            font-weight: bold;
        }

        .text-info {
            color: #17a2b8;
            font-weight: bold;
        }

        .text-success {
            color: #28a745;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .summary-box {
            display: inline-block;
            margin: 10px;
            padding: 15px 20px;
            border-radius: 5px;
            text-align: center;
        }

        .summary-box h3 {
            font-size: 28px;
            margin-bottom: 5px;
        }

        .summary-box p {
            font-size: 12px;
            font-weight: bold;
        }

        @media print {
            body {
                padding: 10px;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Product Expiry Report</h1>
        <h2>
            @if($status_filter == 'expired')
            Expired Items Report
            @elseif($status_filter == 'expiring_soon')
            Expiring Soon Items Report (≤30 Days)
            @elseif($status_filter == 'near_expiry')
            Near Expiry Items Report (31-90 Days)
            @elseif($status_filter == 'valid')
            Valid Items Report (>90 Days)
            @else
            All Items Report
            @endif
        </h2>
        <p>Report Date: {{ $report_date }}</p>
    </div>

    <div class="report-info">
        <p>Total Items: {{ count($items) }}</p>
        @php
        $expired = 0;
        $expiringSoon = 0;
        $nearExpiry = 0;
        $valid = 0;

        foreach($items as $item) {
        if($item->expiry_date) {
        $daysUntilExpiry = (strtotime($item->expiry_date) - strtotime(date('Y-m-d'))) / (60 * 60 * 24);

        if($daysUntilExpiry < 0) {
            $expired++;
            } elseif($daysUntilExpiry <=30) {
            $expiringSoon++;
            } elseif($daysUntilExpiry <=90) {
            $nearExpiry++;
            } else {
            $valid++;
            }
            }
            }
            @endphp

            <div style="text-align: center; margin-top: 15px;">
            @if(!$status_filter || $status_filter == 'expired')
            <div class="summary-box status-expired">
                <h3>{{ $expired }}</h3>
                <p>EXPIRED</p>
            </div>
            @endif

            @if(!$status_filter || $status_filter == 'expiring_soon')
            <div class="summary-box status-expiring-soon">
                <h3>{{ $expiringSoon }}</h3>
                <p>EXPIRING SOON</p>
            </div>
            @endif

            @if(!$status_filter || $status_filter == 'near_expiry')
            <div class="summary-box status-near-expiry">
                <h3>{{ $nearExpiry }}</h3>
                <p>NEAR EXPIRY</p>
            </div>
            @endif

            @if(!$status_filter || $status_filter == 'valid')
            <div class="summary-box status-valid">
                <h3>{{ $valid }}</h3>
                <p>VALID</p>
            </div>
            @endif
    </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 20%">Product Name</th>
                <th style="width: 15%">Supplier</th>
                <th style="width: 10%">Batch No</th>
                <th style="width: 10%">Expiry Date</th>
                <th style="width: 15%">Days Until Expiry</th>
                <th style="width: 10%">Status</th>
                <th style="width: 8%">Quantity</th>
                <th style="width: 10%">GRN Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->ProductName }}</td>
                <td>{{ $item->SupplierName ?? 'N/A' }}</td>
                <td>{{ $item->batch_no ?? 'N/A' }}</td>
                <td>{{ $item->expiry_date ? date('d-M-Y', strtotime($item->expiry_date)) : 'N/A' }}</td>
                <td>
                    @if($item->expiry_date)
                    @php
                    $daysUntilExpiry = floor((strtotime($item->expiry_date) - strtotime(date('Y-m-d'))) / (60 * 60 * 24));
                    @endphp

                    @if($daysUntilExpiry < 0)
                        <span class="text-danger">Expired {{ abs($daysUntilExpiry) }} days ago</span>
                        @elseif($daysUntilExpiry == 0)
                        <span class="text-danger">Expires Today</span>
                        @elseif($daysUntilExpiry == 1)
                        <span class="text-danger">Expires Tomorrow</span>
                        @elseif($daysUntilExpiry <= 30)
                            <span class="text-warning">{{ $daysUntilExpiry }} days left</span>
                            @elseif($daysUntilExpiry <= 90)
                                <span class="text-info">{{ $daysUntilExpiry }} days left</span>
                                @else
                                <span class="text-success">{{ $daysUntilExpiry }} days left</span>
                                @endif
                                @else
                                N/A
                                @endif
                </td>
                <td>
                    @if($item->expiry_date)
                    @php
                    $daysUntilExpiry = (strtotime($item->expiry_date) - strtotime(date('Y-m-d'))) / (60 * 60 * 24);
                    @endphp

                    @if($daysUntilExpiry < 0)
                        <span class="status-label status-expired">EXPIRED</span>
                        @elseif($daysUntilExpiry <= 30)
                            <span class="status-label status-expiring-soon">EXPIRING SOON</span>
                            @elseif($daysUntilExpiry <= 90)
                                <span class="status-label status-near-expiry">NEAR EXPIRY</span>
                                @else
                                <span class="status-label status-valid">VALID</span>
                                @endif
                                @else
                                <span class="status-label" style="background: #6c757d; color: white;">NO EXPIRY</span>
                                @endif
                </td>
                <td>{{ $item->Quantity }}</td>
                <td>{{ $item->Dated ? date('d-M-Y', strtotime($item->Dated)) : 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Generated on {{ date('d-M-Y h:i A') }} | Hospital Management System</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>