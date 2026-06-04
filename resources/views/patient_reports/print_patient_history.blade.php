<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient History Report</title>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <style>
        @media print {
            @page {
                margin: 15mm;
            }

            body {
                margin: 0;
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #3b82f6;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            color: #1e40af;
            font-size: 22px;
        }

        .header p {
            margin: 5px 0 0 0;
            color: #6b7280;
        }

        .patient-info {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
        }

        .patient-info h5 {
            color: #1e40af;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .info-row {
            margin-bottom: 5px;
        }

        .info-label {
            font-weight: bold;
            color: #374151;
            display: inline-block;
            width: 120px;
        }

        .appointment-block {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .appointment-header {
            background: #3b82f6;
            color: white;
            padding: 10px;
            border-radius: 6px 6px 0 0;
            font-weight: bold;
        }

        .appointment-body {
            border: 1px solid #e5e7eb;
            border-top: none;
            padding: 15px;
            border-radius: 0 0 6px 6px;
        }

        .investigation-table {
            width: 100%;
            margin-top: 15px;
            border-collapse: collapse;
        }

        .investigation-table th {
            background: #f3f4f6;
            padding: 8px;
            text-align: left;
            border: 1px solid #d1d5db;
            font-size: 11px;
        }

        .investigation-table td {
            padding: 8px;
            border: 1px solid #e5e7eb;
            font-size: 11px;
        }

        .section-title {
            font-weight: bold;
            color: #1e40af;
            margin-top: 15px;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .print-date {
            text-align: right;
            color: #6b7280;
            font-size: 10px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Patient History Report</h2>
        <p>Complete Medical Records
            @if(isset($search_type))
            @if($search_type === 'mobile')
            by Mobile Number: {{ $search_value }}
            @elseif($search_type === 'mr_number')
            by MR Number: {{ $search_value }}
            @elseif($search_type === 'appointment_number')
            by Appointment Number: {{ $search_value }}
            @endif
            @else
            by Mobile Number: {{ $search_value ?? 'N/A' }}
            @endif
        </p>
    </div>

    @foreach($patients as $patient)
    <div class="patient-info">
        <h5><i>Patient Information</i></h5>
        <div class="info-row">
            <span class="info-label">Patient Name:</span>
            <span>{{ $patient->name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">MR Number:</span>
            <span>{{ $patient->mr_no ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Contact Number:</span>
            <span>{{ $patient->contact_no }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Age:</span>
            <span>{{ $patient->age ?? 'N/A' }} Years</span>
        </div>
        <div class="info-row">
            <span class="info-label">Gender:</span>
            <span>{{ ucfirst($patient->gender ?? 'N/A') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">CNIC:</span>
            <span>{{ $patient->cnic ?? 'N/A' }}</span>
        </div>
    </div>

    @if($patient->appointments && count($patient->appointments) > 0)
    <div class="section-title">Appointment History ({{ count($patient->appointments) }} Records)</div>

    @foreach($patient->appointments as $index => $appointment)
    <div class="appointment-block">
        <div class="appointment-header">
            Appointment #{{ $index + 1 }} - {{ date('d M Y, h:i A', strtotime($appointment->appointment_date)) }}
        </div>
        <div class="appointment-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="info-row">
                        <span class="info-label">Consultant:</span>
                        <span>{{ $appointment->consultant->name ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">OPD Type:</span>
                        <span>{{ $appointment->opd_type->name ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-row">
                        <span class="info-label">Fee:</span>
                        <span>Rs. {{ number_format($appointment->fee ?? 0, 2) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Appointment No:</span>
                        <span>{{ $appointment->appointment_number ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            @if($appointment->investigations && count($appointment->investigations) > 0)
            <div class="section-title" style="font-size: 13px; margin-top: 15px;">
                Investigations ({{ count($appointment->investigations) }})
            </div>
            <table class="investigation-table">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="50%">Investigation Name</th>
                        <th width="25%">Date</th>
                        <th width="20%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($appointment->investigations as $inv_index => $inv)
                    <tr>
                        <td>{{ $inv_index + 1 }}</td>
                        <td><strong>{{ $inv->investigation->name ?? 'N/A' }}</strong></td>
                        <td>{{ date('d M Y', strtotime($inv->created_at)) }}</td>
                        <td>{{ $inv->is_completed ? 'Completed' : 'Pending' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p style="margin-top: 15px; color: #6b7280; font-style: italic;">No investigations recorded for this appointment.</p>
            @endif
        </div>
    </div>
    @endforeach
    @else
    <p style="text-align: center; color: #6b7280; padding: 30px;">No appointments found for this patient.</p>
    @endif

    @if(!$loop->last)
    <div style="page-break-after: always;"></div>
    @endif
    @endforeach

    <div class="print-date">
        <strong>Printed on:</strong> {{ date('d M Y, h:i A') }}
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>

</html>