<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 15px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 15px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #667eea;
            font-weight: bold;
        }

        .header p {
            margin: 5px 0 0 0;
            font-size: 12px;
            color: #666;
        }

        .filters-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }

        .filters-section h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #667eea;
        }

        .filter-item {
            display: inline-block;
            margin-right: 20px;
            margin-bottom: 5px;
        }

        .filter-label {
            font-weight: bold;
            color: #333;
        }

        .filter-value {
            color: #666;
        }

        .stats-section {
            margin-bottom: 25px;
        }

        .stats-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }

        .stats-row {
            display: table-row;
        }

        .stats-card {
            display: table-cell;
            width: 25%;
            padding: 15px;
            text-align: center;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: 1px solid #ddd;
        }

        .stats-card.revenue {
            background: linear-gradient(135deg, #f093fb, #f5576c);
        }

        .stats-card.procedures {
            background: linear-gradient(135deg, #4facfe, #00f2fe);
        }

        .stats-card.share {
            background: linear-gradient(135deg, #43e97b, #38f9d7);
        }

        .stats-value {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stats-label {
            font-size: 10px;
            opacity: 0.9;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 8px;
        }

        .table th {
            background-color: #667eea;
            color: white;
            padding: 8px 5px;
            text-align: center;
            border: 1px solid #ddd;
            font-weight: bold;
            font-size: 8px;
        }

        .table td {
            padding: 6px 5px;
            border: 1px solid #ddd;
            text-align: center;
            vertical-align: middle;
        }

        .table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .table tbody tr:hover {
            background-color: #e9ecef;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }

        .page-break {
            page-break-after: always;
        }

        .currency {
            font-weight: bold;
            color: #28a745;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 8px;
            font-weight: bold;
            border-radius: 10px;
            color: white;
        }

        .badge-primary {
            background-color: #007bff;
        }

        .badge-success {
            background-color: #28a745;
        }

        .badge-danger {
            background-color: #dc3545;
        }

        .badge-secondary {
            background-color: #6c757d;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #667eea;
            margin: 20px 0 10px 0;
            border-bottom: 2px solid #667eea;
            padding-bottom: 5px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Hospital Management System</h1>
        <h2>{{ $title }}</h2>
        <p>Generated on: {{ $generated_at }}</p>
    </div>

    <!-- Filters Applied -->
    <div class="filters-section">
        <h3>Applied Filters</h3>
        <div class="filter-item">
            <span class="filter-label">Date Range:</span>
            <span class="filter-value">
                {{ $filters['from_date'] ? date('d-M-Y', strtotime($filters['from_date'])) : 'All' }} -
                {{ $filters['to_date'] ? date('d-M-Y', strtotime($filters['to_date'])) : 'All' }}
            </span>
        </div>
        <div class="filter-item">
            <span class="filter-label">Consultant:</span>
            <span class="filter-value">{{ $filters['consultant_name'] }}</span>
        </div>
        <div class="filter-item">
            <span class="filter-label">Procedure Type:</span>
            <span class="filter-value">{{ $filters['procedure_type'] ?? 'All' }}</span>
        </div>
    </div>

    <!-- Statistics Summary -->
    <div class="stats-section">
        <div class="section-title">Summary Statistics</div>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stats-card procedures">
                    <div class="stats-value">{{ $stats['total_procedures'] }}</div>
                    <div class="stats-label">Total Procedures</div>
                </div>
                <div class="stats-card revenue">
                    <div class="stats-value">Rs. {{ number_format($stats['total_revenue'], 0) }}</div>
                    <div class="stats-label">Total Revenue</div>
                </div>
                <div class="stats-card share">
                    <div class="stats-value">Rs. {{ number_format($stats['total_consultant_share'], 0) }}</div>
                    <div class="stats-label">Consultant Share</div>
                </div>
                <div class="stats-card">
                    <div class="stats-value">Rs. {{ number_format($stats['total_hospital_share'], 0) }}</div>
                    <div class="stats-label">Hospital Share</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Procedure Details -->
    @if($procedures->count() > 0)
    <div class="section-title">Procedure Details</div>
    <table class="table">
        <thead>
            <tr>
                <th style="width: 12%">Patient Name</th>
                <th style="width: 8%">MR No</th>
                <th style="width: 12%">Consultant</th>
                <th style="width: 15%">Procedure</th>
                <th style="width: 8%">Type</th>
                <th style="width: 10%">Ward/Bed</th>
                <th style="width: 8%">Amount</th>
                <th style="width: 8%">Share</th>
                <th style="width: 10%">Admission</th>
                <th style="width: 9%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($procedures as $index => $procedure)
            <tr>
                <td class="text-left">{{ $procedure->patient->name ?? 'N/A' }}</td>
                <td>{{ $procedure->patient->mrno ?? 'N/A' }}</td>
                <td class="text-left">{{ $procedure->consultant->name ?? 'N/A' }}</td>
                <td class="text-left">{{ $procedure->consultant_procedure->procedure->name ?? 'N/A' }}</td>
                <td>{{ $procedure->consultant_procedure->procedure->type ?? 'N/A' }}</td>
                <td>
                    {{ $procedure->ward->name ?? '' }}
                    @if($procedure->ward && $procedure->bed) - @endif
                    {{ $procedure->bed->name ?? '' }}
                </td>
                <td class="currency">{{ number_format($procedure->procedure_rate ?? 0, 0) }}</td>
                <td class="currency">{{ number_format($procedure->consultant_share_amount ?? 0, 0) }}</td>
                <td>{{ $procedure->admission_date ? date('d-M-Y', strtotime($procedure->admission_date)) : 'N/A' }}</td>
                <td>
                    @php
                    $status = $procedure->admission_status ?? 'Unknown';
                    $badgeClass = match($status) {
                    'Admit' => 'badge-primary',
                    'Discharged' => 'badge-success',
                    'Cancelled' => 'badge-danger',
                    default => 'badge-secondary'
                    };
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                </td>
            </tr>

            {{-- Add page break every 40 rows for better readability --}}
            @if(($index + 1) % 40 == 0 && $index + 1 < $procedures->count())
        </tbody>
    </table>
    <div class="page-break"></div>

    <div class="header">
        <h1>Hospital Management System</h1>
        <h2>{{ $title }} (Continued)</h2>
        <p>Generated on: {{ $generated_at }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 12%">Patient Name</th>
                <th style="width: 8%">MR No</th>
                <th style="width: 12%">Consultant</th>
                <th style="width: 15%">Procedure</th>
                <th style="width: 8%">Type</th>
                <th style="width: 10%">Ward/Bed</th>
                <th style="width: 8%">Amount</th>
                <th style="width: 8%">Share</th>
                <th style="width: 10%">Admission</th>
                <th style="width: 9%">Status</th>
            </tr>
        </thead>
        <tbody>
            @endif
            @endforeach
        </tbody>
    </table>
    @else
    <div class="text-center" style="margin: 50px 0; font-size: 14px; color: #666;">
        <p>No procedures found matching the selected criteria.</p>
    </div>
    @endif

    <div class="footer">
        <p><strong>Report Summary:</strong>
            {{ $stats['total_procedures'] }} procedures |
            Rs. {{ number_format($stats['total_revenue'], 0) }} total revenue |
            Generated at {{ date('Y-m-d H:i:s') }}
        </p>
        <p>This is a computer-generated document from Hospital Management System. No signature required.</p>
    </div>
</body>

</html>