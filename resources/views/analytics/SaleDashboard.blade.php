@extends('layouts.'.config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@section('content')

    <style>
        .tags {
            list-style: none;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px 0;
            width: 95%;
            margin: 0 auto;
        }

        .tags li {
            padding: 0 20px;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .tags li.warning:after {
            background-color: orange;
        }

        .tags li.success:after {
            background-color: green;
        }

        .tags li.danger:after {
            background-color: red;
        }

        .tags li:after {
            content: '';
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 0px;
            width: 10px;
            border-radius: 10px;
            height: 10px;
        }

        .text_height_map{
            line-height: 18px;
        }
    </style>

    <!-- Content -->
    <div class="container-xxl flex-grow-1 px-0">
        <div class="row mb-4">
            <div class="col-12">
                <h4 class="mb-4" style="color: white; font-weight: bold">Sales Analytics Dashboard</h4>
            </div>
        </div>

        
        <div class="row">
            <div class="col-lg-3 col-md-3 mb-4">
                <label style="color:white;font-weight: bold">From Date</label>
                <input type="date" class="form-control" id="from_date" value="{{$from_date}}">
            </div>
            <div class="col-lg-3 col-md-3 mb-4">
                <label style="color:white;font-weight: bold">To Date</label>
                <input type="date" class="form-control" id="to_date" value="{{$to_date}}">
            </div>
            <div class="col-lg-3 col-md-3 mb-4">
                <label style="color:white;font-weight: bold">User (Optional)</label>
                <select class="form-control" id="user_id">
                    <option value="">All Users</option>
                    @foreach($users as $value)
                        <option value="{{ $value->id }}" {{ $value->id == $user_id ? 'selected=selected' : '' }}>
                            {{ $value->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-lg-3 col-md-3 mb-4">
                <a class="btn btn-primary mt-4" style="color: white; font-weight: bold" href="javascript:void(0)" id="search_dashboard">Search</a>
            </div>
        </div>

         
        <div class="row">
             
            <div class="col-lg-3 col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar">
                                    <span class="avatar-initial bg-label-success rounded-circle">
                                        <i class="bx bx-money fs-4"></i>
                                    </span>
                                </div>
                                <div class="card-info">
                                    <h5 class="card-title mb-0 me-2">{{ number_format($analytics['total_sale_amount'], 2) }}</h5>
                                    <small class="text-muted">Total Sale Amount</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Transactions -->
            <div class="col-lg-3 col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar">
                                    <span class="avatar-initial bg-label-info rounded-circle">
                                        <i class="bx bx-receipt fs-4"></i>
                                    </span>
                                </div>
                                <div class="card-info">
                                    <h5 class="card-title mb-0 me-2">{{ number_format($analytics['total_transactions']) }}</h5>
                                    <small class="text-muted">Total Transactions</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Discount -->
            <div class="col-lg-3 col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar">
                                    <span class="avatar-initial bg-label-warning rounded-circle">
                                        <i class="bx bx-percentage fs-4"></i>
                                    </span>
                                </div>
                                <div class="card-info">
                                    <h5 class="card-title mb-0 me-2">{{ number_format($analytics['total_combined_discount'], 2) }}</h5>
                                    <small class="text-muted">Total Discount</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar">
                                    <span class="avatar-initial bg-label-warning rounded-circle">
                                        <i class="bx bx-percentage fs-4"></i>
                                    </span>
                                </div>
                                <div class="card-info">
                                    <h5 class="card-title mb-0 me-2">{{ number_format($analytics['total_returns'], 2) }}</h5>
                                    <small class="text-muted" style="color: red !important;"> Returns in other user/posted bills.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Net Sale Amount -->
            <div class="col-lg-3 col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar">
                                    <span class="avatar-initial bg-label-primary rounded-circle">
                                        <i class="bx bx-calculator fs-4"></i>
                                    </span>
                                </div>
                                <div class="card-info">
                                    <h5 class="card-title mb-0 me-2">{{ number_format($analytics['net_sale_amount'], 2) }}</h5>
                                    <small class="text-muted">Cash in Hand</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Regular Discount -->
            <div class="col-lg-3 col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar">
                                    <span class="avatar-initial bg-label-secondary rounded-circle">
                                        <i class="bx bx-tag fs-4"></i>
                                    </span>
                                </div>
                                <div class="card-info">
                                    <h5 class="card-title mb-0 me-2">{{ number_format($analytics['total_discount'], 2) }}</h5>
                                    <small class="text-muted">Regular Discount</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Discount -->
            <div class="col-lg-3 col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar">
                                    <span class="avatar-initial bg-label-dark rounded-circle">
                                        <i class="bx bx-discount fs-4"></i>
                                    </span>
                                </div>
                                <div class="card-info">
                                    <h5 class="card-title mb-0 me-2">{{ number_format($analytics['total_invoice_discount'], 2) }}</h5>
                                    <small class="text-muted">Invoice Discount</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Card -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Sales Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Period:</label>
                                    <p class="mb-0">{{ $from_date }} to {{ $to_date }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">User Filter:</label>
                                    <p class="mb-0">
                                        @if($user_id)
                                            {{ $users->where('id', $user_id)->first()->name ?? 'Unknown User' }}
                                        @else
                                            All Users
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Discount Rate:</label>
                                    <p class="mb-0">
                                        @if($analytics['total_sale_amount'] > 0)
                                            {{ number_format(($analytics['total_combined_discount'] / $analytics['total_sale_amount']) * 100, 2) }}%
                                        @else
                                            0%
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Avg Transaction:</label>
                                    <p class="mb-0">
                                        @if($analytics['total_transactions'] > 0)
                                            {{ number_format($analytics['total_sale_amount'] / $analytics['total_transactions'], 2) }}
                                        @else
                                            0
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection
@push('scripts')
    <script>
        document.getElementById('search_dashboard').addEventListener('click', function() {
            const fromDate = document.getElementById('from_date').value;
            const toDate = document.getElementById('to_date').value;
            const userId = document.getElementById('user_id').value;
            
            let url = `{{ route('sale.dashboard') }}?from_date=${fromDate}&to_date=${toDate}`;
            if (userId) {
                url += `&user_id=${userId}`;
            }
            
            window.location.href = url;
        });
    </script>
@endpush

