<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }

        .toolbar {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .toolbar button {
            padding: 6px 14px;
            border: 1px solid #1a1a1a;
            background: #f7f7f7;
            color: #1a1a1a;
            border-radius: 4px;
            cursor: pointer;
        }

        .toolbar button:hover {
            background: #1a1a1a;
            color: #ffffff;
        }

        .report-header {
            text-align: center;
            border-bottom: 2px solid #1a1a1a;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }

        .report-header .accent-line {
            width: 80px;
            height: 4px;
            background: #1a1a1a;
            margin: 0 auto 12px;
            border-radius: 4px;
        }

        .report-header .brand h1 {
            margin: 0;
            font-size: 30px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .report-header .brand .subtitle {
            margin: 8px 0 0 0;
            font-size: 16px;
            font-weight: 600;
            letter-spacing: 0.4px;
            text-transform: uppercase;
            color: #555;
        }

        .report-header .contact {
            margin-top: 12px;
            font-size: 13px;
            line-height: 1.6;
        }

        .report-header .contact .contact-item {
            display: inline-block;
            margin: 0 8px;
            color: #444;
        }

        .meta {
            font-size: 13px;
            margin-bottom: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #cfcfcf;
            padding: 8px 10px;
            font-size: 13px;
            text-align: left;
        }

        th {
            background: #f0f0f0;
            font-weight: 600;
        }

        .empty-state {
            margin-top: 40px;
            font-size: 15px;
        }

        @media print {
            .toolbar {
                display: none;
            }

            body {
                margin: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button type="button" onclick="window.print()">Print</button>
        <button type="button" onclick="window.close()">Close</button>
    </div>

    <div class="report-header">
        <div class="accent-line"></div>
        <div class="brand">
            <h1>{{ $hospitalName }}</h1>
            <p class="subtitle">{{ $reportTitle }}</p>
        </div>
        <div class="contact">
            <span class="contact-item"><strong>Mobile:</strong> {{ $contactPhone }}</span>
            @if(!empty($contactEmails))
                <span class="contact-item"><strong>Email:</strong> {{ implode(' | ', $contactEmails) }}</span>
            @endif
        </div>
    </div>

    <div class="meta">
        <strong>Report Generated:</strong> {{ $generated_at->format('d-M-Y H:i:s') }} &middot; Use Ctrl+P to print this page.
    </div>

    @if($serviceTypes->isEmpty())
        <p class="empty-state">No service types available.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th style="width: 10%;">#</th>
                    <th>Name</th>
                </tr>
            </thead>
            <tbody>
                @foreach($serviceTypes as $index => $serviceType)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $serviceType->name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <script>
        window.addEventListener('load', function () {
            window.focus();
        });
    </script>
</body>
</html>
