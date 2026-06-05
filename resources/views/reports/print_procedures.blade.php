<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">

    <style>
        body {
            overflow-x: hidden;
        }

        p {
            font-size: 14px
        }

        table body td:first-child table thead th:first-child {
            width: 20%;
        }

        th {
            width: 10%;
            font-size: 12px !important;
        }

        th,
        td {
            font-size: 12px;
            padding: 0px 3px !important;
        }

        .wrapper {
            overflow: hidden;
        }

        @media print {
            p {
                font-size: 12px
            }

            th,
            td {
                font-size: 10px;
                padding: 0px 3px !important;
            }

            th {
                width: 10%;
                font-size: 8px !important;
            }
        }
    </style>
</head>

<body>

    <div class="wrapper">

        <header class="mb-3">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-12 text-center">
                        <h4 class="mb-1">{{ $title }}</h4>
                        <p class="m-0">Generated on: {{ $generated_at }}</p>
                    </div>
                </div>

                <!-- Filter Information -->
                <div class="col-12 mt-3">
                    <div class="row">
                        <div class="col-3">
                            <p class="m-0"><strong>Date Range:</strong>
                                @if($filters['from_date'] && $filters['to_date'])
                                {{ $filters['from_date'] }} to {{ $filters['to_date'] }}
                                @elseif($filters['from_date'])
                                From {{ $filters['from_date'] }}
                                @elseif($filters['to_date'])
                                Until {{ $filters['to_date'] }}
                                @else
                                All Dates
                                @endif
                            </p>
                        </div>
                        <div class="col-3">
                            <p class="m-0"><strong>Consultant:</strong> {{ $filters['consultant_name'] }}</p>
                        </div>
                        <div class="col-3">
                            <p class="m-0"><strong>Procedure Type:</strong> {{ $filters['procedure_type'] ?: 'All Types' }}</p>
                        </div>
                        <div class="col-3">
                            <p class="m-0"><strong>Status:</strong> {{ $filters['admission_status'] ?: 'All Statuses' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="row">
            <div class="col-12">
                <div class="card border-0">
                    <div class="">
                        <table class="table table-bordered table-responsive m-0">
                            <thead>
                                <tr>
                                    <th style="width: 3%">#</th>
                                    <th style="width: 12%">Patient</th>
                                    <th style="width: 8%">MR No</th>
                                    <th style="width: 10%">Consultant</th>
                                    <th style="width: 15%">Procedure</th>
                                    <th style="width: 8%">Type</th>
                                
                                    <th style="width: 8%">Amount</th>
                                    <th style="width: 8%">Consultant Share</th>
                                    <th style="width: 10%">Admission Date</th>
                                    <th style="width: 8%">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($procedures as $key => $procedure)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $procedure->patient->name ?? 'N/A' }}</td>
                                    <td>{{ $procedure->patient->mr_no ?? 'N/A' }}</td>
                                    <td>{{ $procedure->consultant->name ?? 'N/A' }}</td>
                                    <td>{{ $procedure->consultant_procedure->procedure->name ?? 'N/A' }}</td>
                                    <td>{{ $procedure->consultant_procedure->procedure->type ?? 'N/A' }}</td>
                                    
                                    <td>{{ number_format($procedure->procedure_rate ?? 0, 2) }}</td>
                                    <td>{{ number_format($procedure->consultant_share_amount ?? 0, 2) }}</td>
                                    <td>{{ $procedure->admission_date ? date('d-M-Y', strtotime($procedure->admission_date)) : 'N/A' }}</td>
                                    <td>{{ $procedure->admission_status ?? 'Unknown' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center">No procedures found</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6" style="font-weight: bold;">Total Records: {{ $totals['total_procedures'] }}</td>
                                    <th>Total Amount:</th>
                                    <th>{{ number_format($totals['total_amount'], 2) }}</th>
                                    <th>{{ number_format($totals['total_consultant_share'], 2) }}</th>
                                    <td colspan="2"></td>
                                </tr>
                                <tr>
                                    <td colspan="6"></td>
                                    <th>Hospital Share:</th>
                                    <th>{{ number_format($totals['total_hospital_share'], 2) }}</th>
                                    <td colspan="3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/jquery-3.5.1.min.js') }}"></script>
    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        }
    </script>

</body>

</html>