@extends('layouts.'.config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('stylesheets')
    <style>
        .chart-container {
            height: 400px;
            margin-bottom: 20px;
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }
        .stats-card-success {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            border: none;
        }
        .stats-card-warning {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            color: white;
            border: none;
        }
        .stats-card-info {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            color: #333;
            border: none;
        }
        .chart-card {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border: none;
            border-radius: 10px;
        }
        .filter-controls {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .date-input {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 8px 12px;
        }
        .quick-date-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            margin: 2px;
            cursor: pointer;
            font-size: 12px;
        }
        .quick-date-btn:hover {
            background: #0056b3;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="bx bx-chart me-2"></i>Sales Analytics Dashboard
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stats-card h-100">
                <div class="card-body text-center">
                    <h2 class="fw-bold mb-2" id="total-sales">{{ number_format($total_sales, 2) }}</h2>
                    <p class="mb-0">Net Sales Revenue</p>
                    <small class="opacity-75">PKR (After Discounts)</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stats-card-success h-100">
                <div class="card-body text-center">
                    <h2 class="fw-bold mb-2" id="total-products">{{ number_format($total_products_sold) }}</h2>
                    <p class="mb-0">Products Sold</p>
                    <small class="opacity-75">Units</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stats-card-warning h-100">
                <div class="card-body text-center">
                    <h2 class="fw-bold mb-2" id="total-discount">{{ number_format($total_discount, 2) }}</h2>
                    <p class="mb-0">Total Discounts</p>
                    <small class="opacity-75">PKR</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stats-card-info h-100">
                <div class="card-body text-center">
                    <h2 class="fw-bold mb-2" id="return-rate">0%</h2>
                    <p class="mb-0">Return Rate</p>
                    <small class="opacity-75">Last 30 days</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Controls -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="filter-controls">
                <form method="GET" action="{{ route('reports.analytics') }}" id="dateFilterForm">
                    <div class="row align-items-end">
                        <div class="col-md-2">
                            <label class="form-label">From Date:</label>
                            <input type="date" class="form-control" name="from_date" id="from_date" 
                                   value="{{ $from_date }}" onchange="document.getElementById('dateFilterForm').submit();">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">To Date:</label>
                            <input type="date" class="form-control" name="to_date" id="to_date" 
                                   value="{{ $to_date }}" onchange="document.getElementById('dateFilterForm').submit();">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Time Period:</label>
                            <select class="form-select" id="time-period" onchange="setQuickDateRange()">
                                <option value="custom">Custom Range</option>
                                <option value="7">Last 7 days</option>
                                <option value="30" selected>Last 30 days</option>
                                <option value="90">Last 90 days</option>
                                <option value="365">Last Year</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Chart Type:</label>
                            <select class="form-select" id="chart-type">
                                <option value="daily" selected>Daily Sales</option>
                                <option value="monthly">Monthly Sales</option>
                                <option value="products">Top Products</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-filter me-1"></i>Filter
                            </button>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-secondary" onclick="refreshCharts()">
                                <i class="bx bx-refresh me-1"></i>Refresh Charts
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Sales Trend Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card chart-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-line-chart me-2"></i>Sales Trend
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Products Chart -->
        <div class="col-lg-4 mb-4">
            <div class="card chart-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-pie-chart-alt me-2"></i>Top Products
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="productsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Overview & Returns Analysis -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card chart-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-bar-chart me-2"></i>Monthly Overview
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card chart-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-analyse me-2"></i>Sales vs Returns Analysis
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="returnsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Selling Products Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-list-ul me-2"></i>Top Selling Products
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Units Sold</th>
                                    <th>Revenue</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($top_selling_products as $product)
                                <tr>
                                    <td>{{ $product->ProductName }}</td>
                                    <td>{{ number_format($product->total_sold) }}</td>
                                    <td>PKR {{ number_format($product->total_revenue, 2) }}</td>
                                    <td>
                                        @php 
                                            $percentage = $total_sales > 0 ? ($product->total_revenue / $total_sales) * 100 : 0;
                                        @endphp
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        {{ number_format($percentage, 1) }}%
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
    
    <script>
        let salesChart, productsChart, monthlyChart, returnsChart;
        
        $(document).ready(function() {
            initializeCharts();
            loadSalesStats();
            
            $('#time-period, #chart-type').on('change', function() {
                refreshCharts();
            });
        });
        
        function initializeCharts() {
            // Sales Trend Chart
            const salesCtx = document.getElementById('salesChart').getContext('2d');
            salesChart = new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Sales (PKR)',
                        data: [],
                        borderColor: '#667eea',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'PKR ' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
            
            // Products Pie Chart
            const productsCtx = document.getElementById('productsChart').getContext('2d');
            productsChart = new Chart(productsCtx, {
                type: 'doughnut',
                data: {
                    labels: [],
                    datasets: [{
                        data: [],
                        backgroundColor: [
                            '#667eea', '#764ba2', '#4facfe', '#00f2fe', '#fa709a',
                            '#fee140', '#667eea', '#764ba2', '#4facfe', '#00f2fe'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
            
            // Monthly Chart
            const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
            monthlyChart = new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Monthly Sales',
                        data: [],
                        backgroundColor: 'rgba(102, 126, 234, 0.7)',
                        borderColor: '#667eea',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            
            // Returns Analysis Chart
            const returnsCtx = document.getElementById('returnsChart').getContext('2d');
            returnsChart = new Chart(returnsCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Net Sales', 'Returns'],
                    datasets: [{
                        data: [0, 0],
                        backgroundColor: ['#4facfe', '#fa709a']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
            
            refreshCharts();
        }
        
        function setQuickDateRange() {
            const period = $('#time-period').val();
            
            if (period === 'custom') {
                return; // Let user set custom dates
            }
            
            const days = parseInt(period);
            const endDate = new Date();
            const startDate = new Date();
            startDate.setDate(endDate.getDate() - days);
            
            // Format dates for input fields (YYYY-MM-DD)
            const formatDate = (date) => {
                return date.getFullYear() + '-' + 
                       String(date.getMonth() + 1).padStart(2, '0') + '-' + 
                       String(date.getDate()).padStart(2, '0');
            };
            
            $('#from_date').val(formatDate(startDate));
            $('#to_date').val(formatDate(endDate));
            
            // Auto-submit the form to refresh statistics
            document.getElementById('dateFilterForm').submit();
        }
        
        function refreshCharts() {
            const period = $('#time-period').val();
            const chartType = $('#chart-type').val();
            const fromDate = $('#from_date').val();
            const toDate = $('#to_date').val();
            
            // Load sales chart
            if (chartType === 'daily') {
                loadSalesChart(period, fromDate, toDate);
            } else if (chartType === 'monthly') {
                loadMonthlyChart(period, fromDate, toDate);
            } else if (chartType === 'products') {
                loadProductsChart(period, fromDate, toDate);
            }
            
            // Always load all charts with current period and date range
            loadProductsChart(period, fromDate, toDate);
            loadMonthlyChart(period, fromDate, toDate);
            loadSalesStats(period, fromDate, toDate);
        }
        
        function loadSalesChart(days = 30, fromDate = null, toDate = null) {
            var url = "{{ route('analytics.sales_chart') }}";
            var params = { days: days };
            
            // Add date filters if provided
            if (fromDate) params.from_date = fromDate;
            if (toDate) params.to_date = toDate;
            
            $.get(url, params, function(data) {
                salesChart.data.labels = data.labels;
                salesChart.data.datasets[0].data = data.sales;
                salesChart.update();
            });
        }
        
        function loadProductsChart(days = 30, fromDate = null, toDate = null) {
            var url = "{{ route('analytics.product_sales_chart') }}";
            var params = { limit: 10, days: days };
            
            // Add date filters if provided
            if (fromDate) params.from_date = fromDate;
            if (toDate) params.to_date = toDate;
            
            $.get(url, params, function(data) {
                productsChart.data.labels = data.labels;
                productsChart.data.datasets[0].data = data.sales;
                productsChart.update();
            });
        }
        
        function loadMonthlyChart(days = 365, fromDate = null, toDate = null) {
            var url = "{{ route('analytics.monthly_sales_chart') }}";
            // Convert days to months for monthly chart
            var months = Math.ceil(days / 30);
            if (months < 3) months = 3; // Show at least 3 months
            if (months > 24) months = 24; // Don't show more than 24 months
            
            var params = { months: months };
            
            // Add date filters if provided
            if (fromDate) params.from_date = fromDate;
            if (toDate) params.to_date = toDate;
            
            $.get(url, params, function(data) {
                monthlyChart.data.labels = data.labels;
                monthlyChart.data.datasets[0].data = data.sales;
                monthlyChart.update();
            });
        }
        
        function loadSalesStats(days = 30, fromDate = null, toDate = null) {
            var url = "{{ route('analytics.sales_stats_chart') }}";
            var params = { days: days };
            
            // Add date filters if provided
            if (fromDate) params.from_date = fromDate;
            if (toDate) params.to_date = toDate;
            
            $.get(url, params, function(data) {
                $('#return-rate').text(data.return_percentage + '%');
                
                // Update returns chart
                returnsChart.data.datasets[0].data = [data.net_sales, data.total_returns];
                returnsChart.update();
            });
        }
    </script>
@endpush
