@extends('layouts.'.config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('stylesheets')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
<style>
    .table> :not(caption)>*>* {
        padding: 5px;
    }

    .stats-card {
        background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
    }

    .stats-card .card-body {
        padding: 1.5rem;
    }

    .stats-number {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }

    .chart-container {
        height: 400px;
        margin: 20px 0;
    }

    .profit-positive {
        color: #28a745;
    }

    .profit-negative {
        color: #dc3545;
    }

    .product-card {
        transition: transform 0.2s;
        border: 1px solid #e3e6f0;
    }

    .product-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Page Header -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-0">
                        <i class="bx bx-bar-chart-alt-2 me-2"></i>Daily Product-wise Sales Dashboard
                    </h5>
                    <p class="text-muted mb-0">Comprehensive analysis of product sales with profit calculations</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary" onclick="refreshDashboard()">
                        <i class="bx bx-refresh me-1"></i>Refresh
                    </button>
                    <button type="button" class="btn btn-success" onclick="printReport()">
                        <i class="bx bx-printer me-1"></i>Print Report
                    </button>
                </div>
            </div>
        </div>

        <!-- Date Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form id="dateFilterForm" class="row g-3">
                    <div class="col-md-4">
                        <label for="from_date" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="from_date" name="from_date" value="{{ $from_date }}">
                    </div>
                    <div class="col-md-4">
                        <label for="to_date" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="to_date" name="to_date" value="{{ $to_date }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bx bx-search me-1"></i>Filter
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="resetFilters()">
                            <i class="bx bx-reset me-1"></i>Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sales Statistics -->
        <div class="row mb-4" id="statisticsCards">
            <div class="col-lg-2 col-md-4 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <div class="stats-number" id="totalRevenue">0</div>
                        <div>Total Revenue</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 mb-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <div class="stats-number" id="totalProfit">0</div>
                        <div>Total Profit</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 mb-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <div class="stats-number" id="totalProducts">0</div>
                        <div>Products Sold</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 mb-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <div class="stats-number" id="totalQuantity">0</div>
                        <div>Quantity Sold</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 mb-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <div class="stats-number" id="totalTransactions">0</div>
                        <div>Transactions</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 mb-3">
                <div class="card bg-secondary text-white">
                    <div class="card-body text-center">
                        <div class="stats-number" id="profitMargin">0%</div>
                        <div>Avg Profit Margin</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Daily Sales Trend</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="dailySalesChart" class="chart-container"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Top Selling Products</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="topProductsChart" class="chart-container"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Sales Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">Product-wise Sales Details</h6>
                <div class="d-flex gap-2">
                    <select id="sortFilter" class="form-select" style="width: auto;">
                        <option value="default">Default Order</option>
                        <option value="quantity_desc">High Quantity Sold</option>
                        <option value="quantity_asc">Low Quantity Sold</option>
                        <option value="revenue_desc">High Value Products</option>
                        <option value="revenue_asc">Low Value Products</option>
                        <option value="profit_desc">High Profit Products</option>
                        <option value="profit_asc">Low Profit Products</option>
                    </select>
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bx bx-export me-1"></i>Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="exportToExcel()">Export to Excel</a></li>
                            <li><a class="dropdown-item" href="#" onclick="exportToPDF()">Export to PDF</a></li>
                            <li><a class="dropdown-item" href="#" onclick="printTable()">Print Table</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="productSalesTable" class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Product Name</th>
                                <th class="price-breakdown-col">Unit Sale Price(s)</th>
                                <th class="price-breakdown-col">Unit Purchase Price(s)</th>
                                <th>Qty Sold</th>
                                <th>Qty Returned</th>
                                <th>Net Qty</th>
                                <th>Total Sale Amount</th>
                                <th>Total Purchase Amount</th>
                                <th>Total Revenue</th>
                                <th>Total Cost</th>
                                <th>Total Discount</th>
                                <th>Gross Profit</th>
                                <th>Net Profit</th>
                                <th>Profit Margin %</th>
                                <th>Preview</th>
                            </tr>
                        </thead>
                        <tbody id="productTableBody">
                            <tr>
                                <td colspan="15" class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="table-light">
                            <tr id="totalRow">
                                <th>Total</th>
                                <th class="text-muted small price-breakdown-col">Shown for <= 30 days</th>
                                <th class="text-muted small price-breakdown-col">Shown for <= 30 days</th>
                                <th id="footerQtySold">0</th>
                                <th id="footerQtyReturned">0</th>
                                <th id="footerNetQty">0</th>
                                <th id="footerTotalSaleAmount">0</th>
                                <th id="footerTotalPurchaseAmount">0</th>
                                <th id="footerTotalRevenue">0</th>
                                <th id="footerTotalCost">0</th>
                                <th id="footerTotalDiscount">0</th>
                                <th id="footerGrossProfit">0</th>
                                <th id="footerTotalProfit">0</th>
                                <th id="footerAvgProfitMargin">0%</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/vendor/libs/datatables/jquery.dataTables.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    let dailySalesChart, topProductsChart;
    let productSalesData = [];
    const PRICE_BREAKDOWN_MAX_DAYS = 30;
    const TABLE_COLS_WITH_PRICE_BREAKDOWN = 15;
    const TABLE_COLS_WITHOUT_PRICE_BREAKDOWN = 13;

    function daysBetweenInclusive(fromDate, toDate) {
        if (!fromDate || !toDate) return 0;
        const from = new Date(fromDate + 'T00:00:00');
        const to = new Date(toDate + 'T00:00:00');
        const diffMs = to.getTime() - from.getTime();
        if (isNaN(diffMs)) return 0;
        return Math.floor(diffMs / (1000 * 60 * 60 * 24)) + 1;
    }

    function shouldShowPriceBreakdownColumns(fromDate, toDate) {
        const days = daysBetweenInclusive(fromDate, toDate);
        return days > 0 && days <= PRICE_BREAKDOWN_MAX_DAYS;
    }

    function getCurrentShowPriceBreakdownColumns() {
        return shouldShowPriceBreakdownColumns($('#from_date').val(), $('#to_date').val());
    }

    function getCurrentTableColspan() {
        return getCurrentShowPriceBreakdownColumns()
            ? TABLE_COLS_WITH_PRICE_BREAKDOWN
            : TABLE_COLS_WITHOUT_PRICE_BREAKDOWN;
    }

    function syncPriceBreakdownColumnVisibility() {
        const show = getCurrentShowPriceBreakdownColumns();
        $('.price-breakdown-col').toggle(show);
        const colspan = show ? TABLE_COLS_WITH_PRICE_BREAKDOWN : TABLE_COLS_WITHOUT_PRICE_BREAKDOWN;
        $('#productTableBody tr:first td[colspan]').attr('colspan', colspan);
    }

    $(document).ready(function() {
        loadDashboardData();

        $('#dateFilterForm').on('submit', function(e) {
            e.preventDefault();
            loadDashboardData();
        });

        $('#sortFilter').on('change', function() {
            const sortValue = $(this).val();
            sortProductTable(sortValue);
        });
    });

    function loadDashboardData() {
        const fromDate = $('#from_date').val();
        const toDate = $('#to_date').val();
        syncPriceBreakdownColumnVisibility();

        // Load all dashboard components
        loadStatistics(fromDate, toDate);
        loadProductSalesTable(fromDate, toDate);
        loadDailySalesChart(fromDate, toDate);
        loadTopProductsChart(fromDate, toDate);
    }

    function loadStatistics(fromDate, toDate) {
        $.ajax({
            url: "{{ route('reports.daily_product_sales.statistics') }}",
            method: 'GET',
            data: {
                from_date: fromDate,
                to_date: toDate
            },
            success: function(response) {
                if (response.success && response.statistics) {
                    const stats = response.statistics;
                    $('#totalRevenue').text(formatNumber(stats.total_revenue || 0));
                    $('#totalProducts').text(stats.unique_products || 0);
                    $('#totalQuantity').text(formatNumber(stats.total_quantity_sold || 0));
                    $('#totalTransactions').text(stats.total_transactions || 0);
                }
            }
        });
    }

    function loadProductSalesTable(fromDate, toDate) {
        $.ajax({
            url: "{{ route('reports.daily_product_sales.with_cost') }}",
            method: 'GET',
            data: {
                from_date: fromDate,
                to_date: toDate
            },
            success: function(response) {
                if (response.success) {
                    productSalesData = response.data;
                    renderProductTable(response.data);
                    updateFooterTotals(response.summary);
                    updateProfitStatistics(response.summary);
                }
            },
            error: function() {
                $('#productTableBody').html('<tr><td colspan="' + getCurrentTableColspan() + '" class="text-center text-danger">Error loading data</td></tr>');
            }
        });
    }

    function renderPriceBreakdown(product, type) {
        const rows = Array.isArray(product.price_breakdown) ? product.price_breakdown : [];
        if (!rows.length) {
            return '<span class="text-muted">-</span>';
        }

        return rows.map(function(row) {
            const qty = formatNumber(row.qty || 0);
            const price = type === 'sale' ? (row.unit_sale_price || 0) : (row.unit_purchase_price || 0);
            return `<div><code>${formatNumber(price, 2)}</code> <small class="text-muted">(Qty ${qty})</small></div>`;
        }).join('');
    }

    function renderProductTable(data) {
        let html = '';
        const showPriceBreakdown = getCurrentShowPriceBreakdownColumns();
        const colspan = showPriceBreakdown ? TABLE_COLS_WITH_PRICE_BREAKDOWN : TABLE_COLS_WITHOUT_PRICE_BREAKDOWN;

        if (data.length === 0) {
            html = '<tr><td colspan="' + colspan + '" class="text-center text-muted">No data found for selected date range</td></tr>';
        } else {
            data.forEach(function(product, index) {
                const profitClass = product.total_profit >= 0 ? 'profit-positive' : 'profit-negative';
                const grossProfitClass = product.gross_profit >= 0 ? 'profit-positive' : 'profit-negative';
                const profitMarginClass = product.profit_margin >= 0 ? 'profit-positive' : 'profit-negative';

                html += `
                <tr>
                    <td><strong>${product.ProductName}</strong><div class="text-muted small">ID: ${product.ProductID}</div></td>
                    ${showPriceBreakdown ? `<td>${renderPriceBreakdown(product, 'sale')}</td>` : ''}
                    ${showPriceBreakdown ? `<td>${renderPriceBreakdown(product, 'purchase')}</td>` : ''}
                    <td class="text-center">${formatNumber(product.gross_quantity - product.total_returned)}</td>
                    <td class="text-center">${formatNumber(product.total_returned || 0)}</td>
                    <td class="text-center"><strong>${formatNumber(product.total_quantity_sold)}</strong></td>
                    <td class="text-end"><strong>${formatNumber(product.total_sale_amount, 2)}</strong></td>
                    <td class="text-end">${formatNumber(product.total_purchase_amount, 2)}</td>
                    <td class="text-end"><strong>${formatNumber(product.total_revenue, 2)}</strong></td>
                    <td class="text-end">${formatNumber(product.total_cost, 2)}</td>
                    <td class="text-end text-warning"><strong>${formatNumber(product.total_discount, 2)}</strong></td>
                    <td class="text-end ${grossProfitClass}">${formatNumber(product.gross_profit, 2)}</td>
                    <td class="text-end ${profitClass}"><strong>${formatNumber(product.total_profit, 2)}</strong></td>
                    <td class="text-end ${profitMarginClass}"><strong>${formatNumber(product.profit_margin, 2)}%</strong></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary" onclick="previewProduct(${index})" title="Print Preview">
                            <i class="bx bx-show"></i>
                        </button>
                    </td>
                </tr>
            `;
            });
        }

        $('#productTableBody').html(html);
    }

    function updateFooterTotals(summary) {
        $('#footerQtySold').text(formatNumber((summary.gross_quantity || 0) - (summary.total_returned || 0)));
        $('#footerQtyReturned').text(formatNumber(summary.total_returned || 0));
        $('#footerNetQty').text(formatNumber(summary.total_quantity_sold || 0));
        $('#footerTotalSaleAmount').text(formatNumber(summary.total_sale_amount || 0, 2));
        $('#footerTotalPurchaseAmount').text(formatNumber(summary.total_purchase_amount || 0, 2));
        $('#footerTotalRevenue').text(formatNumber(summary.total_revenue || 0, 2));
        $('#footerTotalCost').text(formatNumber(summary.total_cost || 0, 2));
        $('#footerTotalDiscount').text(formatNumber(summary.total_discount || 0, 2));
        $('#footerGrossProfit').text(formatNumber(summary.gross_profit || 0, 2));
        $('#footerTotalProfit').text(formatNumber(summary.total_profit || 0, 2));
        $('#footerAvgProfitMargin').text(formatNumber(summary.avg_profit_margin || 0, 2) + '%');
    }

    function updateProfitStatistics(summary) {
        $('#totalProfit').text(formatNumber(summary.total_profit || 0));
        $('#profitMargin').text(formatNumber(summary.avg_profit_margin || 0, 1) + '%');
    }

    function loadDailySalesChart(fromDate, toDate) {
        $.ajax({
            url: "{{ route('reports.daily_product_sales.chart') }}",
            method: 'GET',
            data: {
                from_date: fromDate,
                to_date: toDate
            },
            success: function(response) {
                if (response.success) {
                    renderDailySalesChart(response);
                }
            }
        });
    }

    function loadTopProductsChart(fromDate, toDate) {
        $.ajax({
            url: "{{ route('reports.daily_product_sales.top_products') }}",
            method: 'GET',
            data: {
                from_date: fromDate,
                to_date: toDate,
                limit: 10
            },
            success: function(response) {
                if (response.success) {
                    renderTopProductsChart(response);
                }
            }
        });
    }

    function renderDailySalesChart(data) {
        const ctx = document.getElementById('dailySalesChart').getContext('2d');

        if (dailySalesChart) {
            dailySalesChart.destroy();
        }

        dailySalesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Daily Revenue',
                    data: data.revenue,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }, {
                    label: 'Daily Quantity',
                    data: data.quantity,
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    yAxisID: 'y1',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                }
            }
        });
    }

    function renderTopProductsChart(data) {
        const ctx = document.getElementById('topProductsChart').getContext('2d');

        if (topProductsChart) {
            topProductsChart.destroy();
        }

        topProductsChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.products,
                datasets: [{
                    data: data.revenue,
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
                        '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#FF6384'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            font: {
                                size: 10
                            }
                        }
                    }
                }
            }
        });
    }

    function formatNumber(num, decimals = 0) {
        if (num === null || num === undefined) return '0';
        return parseFloat(num).toFixed(decimals).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function refreshDashboard() {
        loadDashboardData();
    }

    function resetFilters() {
        const today = new Date().toISOString().split('T')[0];
        $('#from_date').val(today);
        $('#to_date').val(today);
        loadDashboardData();
    }

    function printReport() {
        const fromDate = $('#from_date').val();
        const toDate = $('#to_date').val();
        const url = "{{ route('reports.daily_product_sales.print') }}?from_date=" + fromDate + "&to_date=" + toDate;
        window.open(url, '_blank');
    }

    function buildTableExportUrl(routeName) {
        const fromDate = $('#from_date').val();
        const toDate = $('#to_date').val();
        const sort = $('#sortFilter').val() || 'default';
        const params = new URLSearchParams({
            from_date: fromDate,
            to_date: toDate,
            sort: sort
        });
        return routeName + '?' + params.toString();
    }

    function exportToExcel() {
        window.location.href = buildTableExportUrl("{{ route('reports.daily_product_sales.export_excel') }}");
    }

    function exportToPDF() {
        window.location.href = buildTableExportUrl("{{ route('reports.daily_product_sales.export_pdf') }}");
    }

    function printTable() {
        const printWindow = window.open('', '_blank');
        const fromDate = $('#from_date').val();
        const toDate = $('#to_date').val();

        printWindow.document.write(`
        <html>
        <head>
            <title>Product Sales Report</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                table { border-collapse: collapse; width: 100%; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                .text-center { text-align: center; }
                .text-end { text-align: right; }
                .profit-positive { color: green; }
                .profit-negative { color: red; }
            </style>
        </head>
        <body>
            <h2>Daily Product-wise Sales Report</h2>
            <p>Date Range: ${fromDate} to ${toDate}</p>
            <p>Generated on: ${new Date().toLocaleString()}</p>
            ${document.getElementById('productSalesTable').outerHTML}
        </body>
        </html>
    `);

        printWindow.document.close();
        printWindow.print();
    }

    function sortProductTable(sortValue) {
        if (sortValue === 'default') {
            renderProductTable(productSalesData);
            syncPriceBreakdownColumnVisibility();
            return;
        }

        let sortedData = [...productSalesData];

        switch (sortValue) {
            case 'quantity_desc':
                sortedData.sort((a, b) => b.total_quantity_sold - a.total_quantity_sold);
                break;
            case 'quantity_asc':
                sortedData.sort((a, b) => a.total_quantity_sold - b.total_quantity_sold);
                break;
            case 'revenue_desc':
                sortedData.sort((a, b) => b.total_revenue - a.total_revenue);
                break;
            case 'revenue_asc':
                sortedData.sort((a, b) => a.total_revenue - b.total_revenue);
                break;
            case 'profit_desc':
                sortedData.sort((a, b) => b.total_profit - a.total_profit);
                break;
            case 'profit_asc':
                sortedData.sort((a, b) => a.total_profit - b.total_profit);
                break;
        }

        renderProductTable(sortedData);
        syncPriceBreakdownColumnVisibility();
    }

    function previewProduct(index) {
        const product = productSalesData[index];
        if (!product) return;

        const fromDate = $('#from_date').val();
        const toDate = $('#to_date').val();

        const printWindow = window.open('', '_blank');

        const profitClass = product.total_profit >= 0 ? 'profit-positive' : 'profit-negative';
        const profitMarginClass = product.profit_margin >= 0 ? 'profit-positive' : 'profit-negative';

        printWindow.document.write(`
        <html>
        <head>
            <title>Product Sales Detail - ${product.ProductName}</title>
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    margin: 20px; 
                    line-height: 1.6;
                }
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                    border-bottom: 2px solid #333;
                    padding-bottom: 20px;
                }
                .product-info {
                    background: #f8f9fa;
                    padding: 20px;
                    border-radius: 5px;
                    margin-bottom: 20px;
                }
                .stats-grid {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 20px;
                    margin-bottom: 20px;
                }
                .stat-item {
                    background: white;
                    padding: 15px;
                    border: 1px solid #ddd;
                    border-radius: 5px;
                }
                .stat-label {
                    font-weight: bold;
                    color: #666;
                    font-size: 12px;
                    text-transform: uppercase;
                }
                .stat-value {
                    font-size: 24px;
                    font-weight: bold;
                    margin-top: 5px;
                }
                .profit-positive { color: #28a745; }
                .profit-negative { color: #dc3545; }
                .summary {
                    background: #e9ecef;
                    padding: 15px;
                    border-radius: 5px;
                    margin-top: 20px;
                }
                @media print {
                    body { margin: 0; }
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Product Sales Detail Report</h1>
                <h2>${product.ProductName}</h2>
                <p>Date Range: ${fromDate} to ${toDate}</p>
                <p>Generated on: ${new Date().toLocaleString()}</p>
            </div>
            
            <div class="product-info">
                <h3>Product Information</h3>
                <p><strong>Product Name:</strong> ${product.ProductName}</p>
                <p><strong>Product ID:</strong> ${product.ProductID}</p>
                <p><strong>Report Period:</strong> ${fromDate} to ${toDate}</p>
            </div>
            
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-label">Total Quantity Sold</div>
                    <div class="stat-value">${formatNumber(product.total_quantity_sold)}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Quantity Returned</div>
                    <div class="stat-value">${formatNumber(product.total_returned || 0)}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Total Sale Amount</div>
                    <div class="stat-value">${formatNumber(product.total_sale_amount, 2)}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Total Purchase Amount</div>
                    <div class="stat-value">${formatNumber(product.total_purchase_amount, 2)}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Total Revenue</div>
                    <div class="stat-value">${formatNumber(product.total_revenue, 2)}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Total Cost</div>
                    <div class="stat-value">${formatNumber(product.total_cost, 2)}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Total Discount</div>
                    <div class="stat-value" style="color: #ffc107;">${formatNumber(product.total_discount, 2)}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Gross Profit</div>
                    <div class="stat-value" style="color: #17a2b8;">${formatNumber(product.gross_profit, 2)}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Net Profit (After Discount)</div>
                    <div class="stat-value ${profitClass}">${formatNumber(product.total_profit, 2)}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Profit Margin</div>
                    <div class="stat-value ${profitMarginClass}">${formatNumber(product.profit_margin, 2)}%</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Total Transactions</div>
                    <div class="stat-value">${product.total_transactions}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Net Quantity</div>
                    <div class="stat-value">${formatNumber(product.total_quantity_sold)}</div>
                </div>
            </div>
            
            <div class="summary">
                <h3>Financial Summary</h3>
                <p>This product generated a total revenue of <strong>${formatNumber(product.total_revenue, 2)}</strong> 
                from <strong>${formatNumber(product.total_quantity_sold)}</strong> units sold across 
                <strong>${product.total_transactions}</strong> transactions.</p>
                <p><strong>Financial Breakdown:</strong><br>
                - Gross Profit: <strong>${formatNumber(product.gross_profit, 2)}</strong><br>
                - Total Discounts: <strong>${formatNumber(product.total_discount, 2)}</strong><br>
                - Net Profit (After Discounts): <strong class="${profitClass}">${formatNumber(product.total_profit, 2)}</strong><br>
                - Profit Margin: <strong class="${profitClass}">${formatNumber(product.profit_margin, 2)}%</strong></p>
            </div>
            
            <div class="no-print" style="margin-top: 30px; text-align: center;">
                <button onclick="window.print()" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer;">
                    Print This Report (Ctrl+P)
                </button>
                <button onclick="window.close()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; margin-left: 10px;">
                    Close
                </button>
            </div>
        </body>
        </html>
    `);

        printWindow.document.close();
        printWindow.focus();
    }
</script>
@endpush