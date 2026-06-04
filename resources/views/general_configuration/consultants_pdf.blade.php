<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            margin: 0;
            padding: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #333;
            font-weight: bold;
        }

        .header h2 {
            margin: 5px 0;
            font-size: 14px;
            color: #666;
        }

        .header p {
            margin: 2px 0;
            font-size: 10px;
            color: #888;
        }

        .info-section {
            margin-bottom: 10px;
            font-size: 10px;
        }

        .info-section span {
            font-weight: bold;
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
            padding: 5px 3px;
            text-align: center;
            border: 1px solid #dee2e6;
            font-weight: bold;
            font-size: 8px;
        }

        .table td {
            padding: 4px 3px;
            border: 1px solid #dee2e6;
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
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }

        .page-break {
            page-break-after: always;
        }

        .summary-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 4px;
        }

        .currency {
            font-weight: bold;
            color: #28a745;
        }

        .percentage {
            font-weight: bold;
            color: #007bff;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Hospital Management System</h1>
        <h2>{{ $title }}</h2>
        <p>Generated on: {{ $generated_at }}</p>
    </div>

    <div class="summary-box">
        <div style="display: flex; justify-content: space-between; flex-wrap: wrap;">
            <span><strong>Total Consultants:</strong> {{ $total_consultants }}</span>
            @if(isset($statistics))
            <span><strong>Avg General OPD:</strong> Rs. {{ number_format($statistics['average_general_opd_fee'], 0) }}</span>
            <span><strong>Avg Consultant OPD:</strong> Rs. {{ number_format($statistics['average_consultant_opd_fee'], 0) }}</span>
            @endif
        </div>

        @if(isset($statistics) && $statistics['by_department']->count() > 0)
        <div style="margin-top: 8px; font-size: 9px;">
            <strong>By Department:</strong>
            @foreach($statistics['by_department'] as $dept => $count)
            {{ $dept ?? 'N/A' }}: {{ $count }};
            @endforeach
        </div>
        @endif
    </div>

    @if($consultants->count() > 0)
    <table class="table">
        <thead>
            <tr>
                <th style="width: 3%">#</th>
                <th style="width: 12%">Name</th>
                <th style="width: 8%">CNIC</th>
                <th style="width: 6%">PMDC #</th>
                <th style="width: 8%">Joining Date</th>
                <th style="width: 10%">Department</th>
                <th style="width: 10%">Speciality</th>
                <th style="width: 8%">Type</th>
                <th style="width: 6%">General OPD</th>
                <th style="width: 6%">Consultant OPD</th>
                <th style="width: 6%">Hospital Share</th>
                <th style="width: 6%">Consultant Share</th>
                <th style="width: 5%">SC Share %</th>
                <th style="width: 6%">Lab %</th>
            </tr>
        </thead>
        <tbody>
            @foreach($consultants as $index => $consultant)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="text-left">{{ $consultant->name ?? 'N/A' }}</td>
                <td>{{ $consultant->cnic ?? 'N/A' }}</td>
                <td>{{ $consultant->pmdc_number ?? 'N/A' }}</td>
                <td>{{ $consultant->joining_date ? date('d-M-Y', strtotime($consultant->joining_date)) : 'N/A' }}</td>
                <td class="text-left">{{ $consultant->consultant_department->name ?? 'N/A' }}</td>
                <td class="text-left">{{ $consultant->consultant_speciality->name ?? 'N/A' }}</td>
                <td class="text-left">{{ $consultant->consultant_type->name ?? 'N/A' }}</td>
                <td class="currency">{{ number_format($consultant->general_opd_fee, 0) }}</td>
                <td class="currency">{{ number_format($consultant->consultant_opd_fee, 0) }}</td>
                <td class="currency">{{ number_format($consultant->hospital_share, 0) }}</td>
                <td class="currency">{{ number_format($consultant->consultant_share, 0) }}</td>
                <td class="percentage">{{ $consultant->share_percentage }}%</td>
                <td class="percentage">{{ $consultant->lab_percentage }}%</td>
            </tr>

            {{-- Add page break every 35 rows for better readability --}}
            @if(($index + 1) % 35 == 0 && $index + 1 < $consultants->count())
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
                <th style="width: 3%">#</th>
                <th style="width: 12%">Name</th>
                <th style="width: 8%">CNIC</th>
                <th style="width: 6%">PMDC #</th>
                <th style="width: 8%">Joining Date</th>
                <th style="width: 10%">Department</th>
                <th style="width: 10%">Speciality</th>
                <th style="width: 8%">Type</th>
                <th style="width: 6%">General OPD</th>
                <th style="width: 6%">Consultant OPD</th>
                <th style="width: 6%">Hospital Share</th>
                <th style="width: 6%">Consultant Share</th>
                <th style="width: 5%">SC Share %</th>
                <th style="width: 6%">Lab %</th>
            </tr>
        </thead>
        <tbody>
            @endif
            @endforeach
        </tbody>
    </table>
    @else
    <div class="text-center" style="margin: 50px 0; font-size: 12px; color: #666;">
        <p>No consultants found.</p>
    </div>
    @endif

    <div class="footer">
        <p>Report generated from Hospital Management System | Page printed at {{ date('Y-m-d H:i:s') }}</p>
        <p>This is a computer-generated document. No signature required.</p>
    </div>
</body>

</html>