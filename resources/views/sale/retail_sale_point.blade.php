<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy Sale Point</title>
    <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet">
    <script src="{{asset('assets/js/jquery-3.5.1.min.js')}}"></script>
    <script src="{{asset('assets/js/bootstrap.bundle.min.js')}}"></script>
    <link href="{{asset('assets/css/select2.min.css')}}" rel="stylesheet" />
    <script src="{{asset('assets/js/select2.min.js')}}"></script>
    <script src="{{asset('assets/js/ckeditor.js')}}"></script>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #2c5aa0;
            --secondary-color: #17a2b8;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --pharmacy-green: #00a86b;
            --pharmacy-blue: #4169e1;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            overflow: hidden;
        }

        .main-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin: 8px;
            padding: 12px;
            backdrop-filter: blur(10px);
            height: calc(100vh - 16px);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .header-section {
            background: linear-gradient(135deg, var(--pharmacy-green) 0%, var(--pharmacy-blue) 100%);
            border-radius: 10px;
            padding: 4px;
            margin-bottom: 8px;
            color: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            flex-shrink: 0;
        }

        .header-section h2 {
            font-size: 1.2rem;
            margin-bottom: 8px;
        }

        .header-section .card {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            backdrop-filter: blur(10px);
            border-radius: 8px;
            margin-bottom: 8px;
        }

        .header-section .card-body {
            padding: 8px;
        }

        .header-section label {
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 3px;
            display: block;
            font-size: 0.75rem;
        }

        .header-section .form-control,
        .header-section .form-select {
            border: 2px solid rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.9);
            border-radius: 6px;
            padding: 4px 6px;
            font-weight: 500;
            font-size: 0.75rem;
            height: 28px;
        }

        .content-section {
            flex: 1;
            display: flex;
            gap: 8px;
            overflow: hidden;
            min-height: 0;
        }

        .left-sidebar {
            width: 300px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex-shrink: 0;
        }

        .sidebar-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 10px;
            padding: 5px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar-section h6 {
            font-size: 0.9rem;
            margin-bottom: 4px;
            color: var(--pharmacy-green);
            font-weight: 700;
        }

        .sidebar-section .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
            margin-bottom: 0;
        }

        .sidebar-section .card-body {
            padding: 3px;
        }

        .sidebar-section .form-control,
        .sidebar-section .form-select {
            height: 28px;
            padding: 4px 6px;
            font-size: 0.75rem;
        }

        .product-input-section .card-body {
            padding: 6px;
        }

        .product-input-section .form-control,
        .product-input-section .form-select {
            height: 28px;
            padding: 4px 6px;
            font-size: 0.75rem;
        }

        .product-input-section .input-group-text {
            padding: 4px 6px;
            font-size: 0.7rem;
        }

        .right-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 0;
            gap: 8px;
        }

        .product-input-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 10px;
            padding: 5px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            flex-shrink: 0;
        }

        .product-input-section h5 {
            font-size: 1rem;
            margin-bottom: 4px;
            color: var(--pharmacy-green);
            font-weight: 700;
        }

        .product-input-section .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
        }

        .product-input-section .card-body {
            padding: 4px;
        }

        .product-input-section label {
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 3px;
            color: var(--dark-color);
        }

        .product-input-section .form-control,
        .product-input-section .form-select {
            padding: 6px 8px;
            font-size: 0.8rem;
            height: 32px;
            border-radius: 6px;
        }

        .pharmacy-table {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            flex: 1;
            min-height: 0;
            display: flex;
            flex-direction: column;
        }

        .table-header {
            background: linear-gradient(135deg, var(--pharmacy-green) 0%, var(--pharmacy-blue) 100%);
            color: white;
            font-weight: 700;
            text-align: center;
            font-size: 0.75rem;
            flex-shrink: 0;
        }

        .table-header th {
            padding: 8px 4px !important;
            border: none;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .table-scroll {
            flex: 1;
            overflow-y: auto;
            scroll-behavior: smooth;
            min-height: 0;
        }

        .table-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .table-scroll::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .table-scroll::-webkit-scrollbar-thumb {
            background: var(--pharmacy-green);
            border-radius: 2px;
        }

        .table-scroll table td {
            padding: 6px 4px !important;
            font-family: 'Segoe UI', sans-serif;
            font-weight: 500;
            font-size: 0.75rem;
            text-align: center;
            vertical-align: middle;
            border-bottom: 1px solid #e9ecef;
        }

        .table-scroll table td:nth-child(2) {
            text-align: left !important;
            font-weight: 600;
            color: var(--dark-color);
        }

        .sidebar {
            width: 280px;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 10px;
            padding: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .sidebar h5 {
            color: var(--pharmacy-green);
            font-weight: 700;
            text-align: center;
            margin-bottom: 8px;
            padding: 6px;
            background: rgba(0, 168, 107, 0.1);
            border-radius: 6px;
            font-size: 0.9rem;
        }

        .previous-bills {
            max-height: 220px;
            overflow-y: auto;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            background: #fff;
        }

        .previous-bills .dataTables_wrapper {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .previous-bills .dataTables_scrollBody {
            flex: 1;
            overflow-y: auto !important;
        }

        .previous-bills table th {
            background: var(--light-color);
            color: var(--dark-color);
            font-weight: 600;
            font-size: 0.7rem;
            padding: 6px 4px;
            border-bottom: 2px solid #dee2e6;
        }

        .previous-bills table td {
            padding: 4px;
            font-size: 0.7rem;
            vertical-align: middle;
        }

        .previous-bills table td a {
            font-size: 0.65rem;
            padding: 2px 4px;
            border-radius: 3px;
            text-decoration: none;
        }

        .footer-section {
            background: linear-gradient(135deg, var(--dark-color) 0%, #495057 100%);
            color: white;
            border-radius: 10px;
            padding: 10px;
            margin-top: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            flex-shrink: 0;
        }

        .footer-section label {
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 3px;
            font-size: 0.75rem;
        }

        .footer-section .form-control,
        .footer-section .form-select {
            border: 2px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.75rem;
            height: 28px;
            padding: 4px 6px;
        }

        .footer-section .input-group-text {
            background: var(--pharmacy-green);
            color: white;
            border: none;
            font-weight: 600;
            font-size: 0.7rem;
            padding: 4px 6px;
        }

        .btn-pharmacy {
            background: linear-gradient(135deg, var(--pharmacy-green) 0%, var(--pharmacy-blue) 100%);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 8px 16px;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        }

        .btn-pharmacy:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            color: white;
        }

        .btn-home {
            background: linear-gradient(135deg, var(--success-color) 0%, #20c997 100%);
        }

        .btn-new {
            background: linear-gradient(135deg, var(--info-color) 0%, #6f42c1 100%);
        }

        .editable {
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .editable:hover {
            background-color: rgba(0, 168, 107, 0.1) !important;
        }

        .remove-btn {
            background: var(--danger-color);
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            color: white;
            font-size: 10px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .remove-btn:hover {
            background: #c82333;
            transform: scale(1.1);
        }

        .dose-badge {
            background: linear-gradient(135deg, var(--warning-color) 0%, #fd7e14 100%);
            color: white;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 0.65rem;
            font-weight: 600;
        }

        .qty-badge {
            background: linear-gradient(135deg, var(--info-color) 0%, var(--primary-color) 100%);
            color: white;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 0.65rem;
            font-weight: 600;
        }

        .amount-display {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--pharmacy-green);
        }

        .total-display {
            font-size: 1rem;
            font-weight: 800;
            color: var(--danger-color);
        }

        #popu-message {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 10000;
            padding: 10px 15px;
            border-radius: 6px;
            color: white;
            font-weight: 600;
            display: none;
            min-width: 250px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            font-size: 0.85rem;
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
            }

            50% {
                transform: scale(1.01);
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            }

            100% {
                transform: scale(1);
                box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
            }
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }

        /* Compact form controls */
        .form-control,
        .form-select {
            border-radius: 6px;
            border: 1px solid #ced4da;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--pharmacy-green);
            box-shadow: 0 0 0 0.1rem rgba(0, 168, 107, 0.25);
        }

        /* Compact input groups */
        .input-group-text {
            font-size: 0.75rem;
            padding: 6px 8px;
        }

        /* Compact table styling */
        .table-sm td,
        .table-sm th {
            padding: 0.2rem !important;
        }

        /* Custom scrollbars */
        .dataTables_scrollBody::-webkit-scrollbar {
            width: 4px;
        }

        .dataTables_scrollBody::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 2px;
        }

        .dataTables_scrollBody::-webkit-scrollbar-thumb {
            background: var(--pharmacy-green);
            border-radius: 2px;
        }

        /* Hide DataTables search and pagination for compact view */
        .dataTables_filter,
        .dataTables_length,
        .dataTables_paginate,
        .dataTables_info {
            display: none !important;
        }

        /* Compact DataTable styling */
        #previous-bill-table {
            font-size: 0.7rem;
        }

        #previous-bill-table th,
        #previous-bill-table td {
            padding: 4px 6px !important;
            border: none;
        }

        #previous-bill-table_wrapper .dataTables_paginate {
            display: block !important;
            margin-top: 5px;
        }

        #previous-bill-table_wrapper .dataTables_paginate .paginate_button {
            padding: 2px 6px !important;
            margin: 0 2px;
            font-size: 0.7rem;
        }

        .previous-bills .dataTables_scrollBody {
            border: 1px solid #e9ecef;
            border-radius: 5px;
        }

        /* Responsive adjustments */
        @media (max-width: 1200px) {
            .sidebar {
                width: 250px;
            }

            .footer-section .btn-pharmacy {
                padding: 6px 12px;
                font-size: 0.8rem;
            }
        }

        /* Ensure no body scrolling */
        html,
        body {
            overflow: hidden !important;
            height: 100vh;
        }
    </style>
</head>

<body>
    <div id="popu-message">Notification Message</div>

    <div class="main-container">
        <!-- Header Section -->
        <div class="header-section">
            <div class="row align-items-center mb-2">
                <div class="col-6">
                    <h6 class="mb-0"><i class="fas fa-pills me-1"></i>{{session('store_name')}}</h6>
                </div>
                <div class="col-6 text-end">
                    <small class="opacity-75">Professional Pharmacy Management</small>
                </div>
            </div>

            <div class="row g-2">
                <div class="col-md-2" style="display: none;">
                    <div class="card card-hover">
                        <div class="card-body">
                            <label for="invoice_number"><i class="fas fa-receipt me-1"></i>Invoice #</label>
                            <div class="input-group">
                                <span class="input-group-text">#</span>
                                <input type="text" id="invoice_number" class="form-control" value="{{$invoiceNo ?? ''}}" readonly>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="col-md-3" style="display: none;">
                    <div class="card card-hover">
                        <div class="card-body">
                            <label for="SID"><i class="fas fa-user-injured me-1"></i>Patient</label>
                            <select id="SID" name="SID" class="form-control">
                                <option value="">Select Patient...</option>
                                @foreach($admitted_patients as $patient)
                                <option data-admission_id="0" value="{{$patient->id}}" {{($patient->patient_type == "walking_customer") ? "selected" : ""}}>{{$patient->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-2" style="display: none;">
                    <div class="card card-hover">
                        <div class="card-body">
                            <label for="bill_date"><i class="fas fa-calendar me-1"></i>Date</label>
                            <input type="date" id="bill_date" class="form-control" value="{{date('Y-m-d')}}">
                        </div>
                    </div>
                </div>


            </div>
        </div>

        <!-- Content Section -->
        <div class="content-section">
            <!-- Left Sidebar -->
            <div class="left-sidebar">
                <!-- Appointment Selection -->
                <div class="sidebar-section">
                    <h6><i class="fas fa-calendar-check me-1"></i>Appointment</h6>
                    <div class="card">
                        <div class="card-body">
                            <select id="appointment_id" name="appointment_id" class="form-control">
                                <option value="">Select Appointment...</option>
                                @foreach($appointments as $appointment)
                                <option value="{{$appointment->id}}">{{$appointment->patient->name}} | #{{$appointment->appointment_number}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Medicine Type -->
                <div class="sidebar-section">
                    <h6><i class="fas fa-pills me-1"></i>Medicine Type</h6>
                    <div class="card">
                        <div class="card-body">
                            <select class="form-select" id="medicine_type">
                                <option value="Home" selected="selected">Home</option>
                                <option value="Ward" {{($type == "Ward") ? "selected" : ""}}>Ward</option>
                                <option value="OT" {{($type == "OT") ? "selected" : ""}}>OT</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Recent Bills -->
                <div class="sidebar-section flex-grow-1">
                    <h6><i class="fas fa-history me-1"></i>Recent Bills</h6>
                    <div class="previous-bills">
                        <table class="table table-sm mb-0" id="previous-bill-table">
                            <thead>
                                <tr>
                                    <th style="width: 25%">Invoice</th>
                                    <th style="width: 35%">Patient</th>
                                    <th style="width: 20%">Amount</th>
                                    <th style="width: 20%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dynamic content -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Content Area -->
            <div class="right-content">
                <!-- Product Input Section -->
                <div class="product-input-section">
                    <h5><i class="fas fa-plus-circle me-1"></i>Add Products</h5>
                    <div class="row g-2">
                        <div class="col-md-5">
                            <div class="card">
                                <div class="card-body">
                                    <label><i class="fas fa-search me-1"></i>Product</label>
                                    <select class="form-control" id="product_id">
                                        <option value="">Select Product...</option>
                                        @foreach ($products as $product)
                                        @if($product->ProductName != '' && $product->ProductName != '-' && $product->avaliable_qty != 0)
                                        <option value="{{$product->ProductID}}" data-purchasePrice="{{$product->unit_sale_price}}" data-taxPercentage="{{$product->taxPercentage}}">
                                            {{$product->ProductName}} | {{$product->generic_name?->name ?? ''}} | Qty: {{$product->avaliable_qty}}
                                        </option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        @if($type == "Home" || $type == "Ward")
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <label><i class="fas fa-prescription-bottle me-1"></i>Dose</label>
                                    <select class="form-select" id="dose_type">
                                        <option value="-" selected>-</option>
                                        <option value="TDS">TDS (3x daily)</option>
                                        <option value="BD">BD (2x daily)</option>
                                        <option value="OD">OD (1x daily)</option>
                                        <option value="HS">HS (at night)</option>
                                        <option value="QID">QID (4x daily)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="col-md-2">
                            <div class="card">
                                <div class="card-body">
                                    <label><i class="fas fa-hashtag me-1"></i>Qty</label>
                                    <input type="number" class="form-control" id="sale_quantity" placeholder="1" value="1" min="1">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="card">
                                <div class="card-body">
                                    <label><i class="fas fa-money-bill me-1"></i>Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rs</span>
                                        <div class="form-control amount-display" id="SalePrice_text">0</div>
                                    </div>
                                    <input type="number" style="display: none;" id="SalePrice" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items Table Section -->
                <div class="pharmacy-table">
                    <table class="table mb-0 table-sm">
                        <thead class="table-header">
                            <tr>
                                <th style="width: 5%">#</th>
                                <th style="width: 30%">Product</th>
                                <th style="width: 8%">Qty</th>
                                <th style="width: 10%">Rate</th>
                                <th style="width: 10%">Amount</th>
                                <th style="width: 12%">Dose</th>
                                <th style="width: 10%">Stock</th>
                                <th style="width: 15%">Action</th>
                            </tr>
                        </thead>
                    </table>

                    <div class="table-scroll">
                        <table class="table table-striped mb-0 table-sm">
                            <tbody id="product_table">
                                <!-- Dynamic content will be added here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Section -->
        <div class="footer-section">
            <div class="row g-2 align-items-end">
                <div class="col-md-1">
                    <label for="BillAmount"><i class="fas fa-calculator me-1"></i>Total</label>
                    <div class="input-group">
                        <span class="input-group-text">Rs</span>
                        <input type="number" readonly class="form-control total-display" id="BillAmount" value="0">
                    </div>
                </div>

                <div class="col-md-1">
                    <label for="discount_id"><i class="fas fa-percent me-1"></i>Disc%</label>
                    <select class="form-control" id="discount_id">
                        <option value="0">0%</option>
                        <option value="2">2%</option>
                        <option value="3">3%</option>
                        <option value="5">5%</option>
                        <option value="10">10%</option>
                    </select>
                </div>

                <div class="col-md-1">
                    <label for="discount_amount"><i class="fas fa-tag me-1"></i>Disc</label>
                    <div class="input-group">
                        <span class="input-group-text">Rs</span>
                        <input type="number" readonly class="form-control" id="discount_amount" value="0">
                    </div>
                </div>

                <div class="col-md-1">
                    <label for="invoice_discount"><i class="fas fa-cut me-1"></i>Extra</label>
                    <div class="input-group">
                        <span class="input-group-text">Rs</span>
                        <input type="number" class="form-control" id="invoice_discount" value="0">
                    </div>
                </div>

                <div class="col-md-1">
                    <label for="ReceivedAmount"><i class="fas fa-money-check me-1"></i>Net</label>
                    <div class="input-group">
                        <span class="input-group-text">Rs</span>
                        <input type="number" disabled class="form-control total-display" id="ReceivedAmount" value="0">
                    </div>
                </div>

                <div class="col-md-2">
                    <label for="ReceivedAmountFromCustomer"><i class="fas fa-hand-holding-usd me-1"></i>Received</label>
                    <div class="input-group">
                        <span class="input-group-text">Rs</span>
                        <input type="number" class="form-control" id="ReceivedAmountFromCustomer" value="0">
                    </div>
                </div>

                <div class="col-md-1">
                    <label for="ReturnToCustomer"><i class="fas fa-undo me-1"></i>Return</label>
                    <div class="input-group">
                        <span class="input-group-text">Rs</span>
                        <input type="number" readonly class="form-control" id="ReturnToCustomer" value="0" style="font-weight: bold; font-size: 16px;">
                    </div>
                </div>

                <div class="col-md-4">
                    <button class="btn btn-pharmacy me-2 pulse-animation" id="save_bill">
                        <i class="fas fa-save me-1"></i>Save Bill
                    </button>
                    <a class="btn btn-pharmacy btn-new me-2" href="{{route('pos.retail_sale_point')}}" target="_blank">
                        <i class="fas fa-plus me-1"></i>New
                    </a>
                    <a class="btn btn-pharmacy btn-home go_to_home" href="javascript:void(0)">
                        <i class="fas fa-home me-1"></i>Home
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Include DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css">

    <!-- Include DataTables and other required JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>
    <script src="{{ asset('assets/js/jquery.form.min.js') }}"></script>

    <script>
        // Same JavaScript functionality as the original but with enhanced UX
        var preValue = '';
        var selectedRow = "";
        var ProductList = [];
        var PreviousBalance = 0;
        var taxPercentage = 0;
        var currentAvailableQuantity = 0;
        var patient_admission_id = 0;
        var ward_request_id = 0;

        @if(count($list_products) > 0)
        ProductList = @json($list_products);
        @endif

        reload_table();

        setTimeout(function() {
            @if($patient_id != '')
            ward_request_id = "{{$ward_request}}";
            $("#SID").val("{{$patient_id}}").trigger("change");
            @endif
        }, 500);

        $(document).on('click', '.go_to_home', function(e) {
            e.preventDefault();
            if (confirm("Are you sure you want to go back to home?")) {
                window.location.href = "{{route('home')}}";
            }
        });

        $(document).ready(function() {
            // Initialize DataTable for previous bills - check if already initialized
            if (!$.fn.DataTable.isDataTable('#previous-bill-table')) {
                $('#previous-bill-table').DataTable({
                    data: [
                        // Sample data - you can populate this from your backend
                        ['INV-001', 'John Doe', 'Rs. 1,500', '<a href="#" class="btn btn-sm btn-primary"><i class="fas fa-print"></i></a>'],
                        ['INV-002', 'Jane Smith', 'Rs. 2,350', '<a href="#" class="btn btn-sm btn-primary"><i class="fas fa-print"></i></a>'],
                        ['INV-003', 'Ali Khan', 'Rs. 890', '<a href="#" class="btn btn-sm btn-primary"><i class="fas fa-print"></i></a>']
                    ],
                    pageLength: 5,
                    lengthMenu: [5, 10],
                    responsive: true,
                    scrollY: '180px',
                    scrollCollapse: true,
                    searching: false,
                    info: false,
                    lengthChange: false,
                    dom: 'rt<\"row\"<\"col-12\"p>>',
                    language: {
                        emptyTable: 'No recent bills found',
                        paginate: {
                            next: '<i class=\"fas fa-chevron-right\"></i>',
                            previous: '<i class=\"fas fa-chevron-left\"></i>'
                        }
                    },
                    columnDefs: [{
                        orderable: false,
                        targets: 3
                    }]
                });
            }

            // Initialize Select2 with modern styling
            $("#product_id").select2({
                placeholder: "Search for products...",
                allowClear: true
            });

            $("#SID").select2({
                placeholder: "Select patient...",
                allowClear: true
            });

            $('#appointment_id').select2({
                placeholder: "Search appointments...",
                allowClear: true,
                minimumInputLength: 1,
                ajax: {
                    transport: function(params, success, failure) {
                        var term = params.data.q ? params.data.q.toLowerCase() : '';
                        var localMatch = [];

                        $('#appointment_id option').each(function() {
                            var text = $(this).text().toLowerCase();
                            if (text.indexOf(term) > -1) {
                                localMatch.push({
                                    id: $(this).val(),
                                    text: $(this).text()
                                });
                            }
                        });

                        if (localMatch.length > 0) {
                            success(localMatch);
                            return;
                        }

                        $.ajax(params, success, failure);
                    },
                    url: "{{ route('pos.search_appointment') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    }
                }
            });

            // Enhanced event handlers with better UX
            $("body").on("keyup", "#ReceivedAmountFromCustomer", function() {
                calculateReturnAmount();
            });

            $("body").on("change", "#ReceivedAmountFromCustomer", function() {
                calculateReturnAmount();
            });

            $("body").on("keyup", "#invoice_discount", function() {
                reload_table();
            });

            $("body").on("change", "#invoice_discount", function() {
                reload_table();
            });

            $("body").on("change", "#medicine_type", function() {
                var value = $(this).val();
                window.location = "{{route('pos.retail_sale_point')}}?type=" + value;
            });

            $("body").on("change", "#discount_id", function() {
                reload_table();
            });

            $("body").on("change", "#SID", function() {
                patient_admission_id = $('#SID option:selected').attr('data-admission_id');
                get_prev_balance();
            });

            $(document).on("change", "#product_id", function() {
                var purchasePrice = $('#product_id option:selected').attr('data-purchasePrice');
                taxPercentage = $('#product_id option:selected').attr('data-taxPercentage');
                $("#SalePrice").val(purchasePrice);
                $("#SalePrice_text").html(purchasePrice);
                getItemDetails();
            });

            $("body").on("blur", "#sale_quantity", function() {
                saveItemToBill();
            });

            $("body").on("click", ".remove_item", function() {
                removeProductByID($(this).attr("data-id"));
            });

            // Enhanced save bill functionality
            $("body").on("click", "#save_bill", function() {
                var invoice_discount = $("#invoice_discount").val();
                if (invoice_discount == '') {
                    invoice_discount = 0;
                }
                if (invoice_discount > 9) {
                    popupMsg("Invoice Discount Limit Exceed. Limit Rs:9 only/-", "error");
                    return false;
                }

                var return_amount = $("#ReturnToCustomer").val();
                if (return_amount < 0) {
                    popupMsg("Please collect amount from customer", "error");
                    $("#ReceivedAmountFromCustomer").focus();
                    return false;
                }

                var SID = $("#SID").val();
                var invoice_number = $("#invoice_number").val();
                var medicine_type = $("#medicine_type").val();
                var appointment_id = $("#appointment_id").val();
                var bill_date = $("#bill_date").val();
                var previous_balance = $("#previous_balance").val();
                var ReceivedAmount = $("#ReceivedAmount").val();
                var BillAmount = $("#BillAmount").val();
                var ReceivedAmountFromCustomer = $("#ReceivedAmountFromCustomer").val();
                var discount_percentage = $("#discount_id").val();
                var discount_amount = $("#discount_amount").val();

                $("#save_bill").hide();
                $(this).html('<i class="fas fa-spinner fa-spin me-2"></i>Saving...');

                if (SID == '') {
                    popupMsg("Please Select Customer", "error");
                    $("#SID").focus();
                    $("#save_bill").show();
                    $(this).html('<i class="fas fa-save me-2"></i>Save Bill');
                    return false;
                }

                if (ProductList.length <= 0) {
                    popupMsg("Please Add Items to Bill", "error");
                    $("#save_bill").show();
                    $(this).html('<i class="fas fa-save me-2"></i>Save Bill');
                    return false;
                }

                var patient_id = SID;
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        SID,
                        patient_id,
                        ReceivedAmountFromCustomer,
                        ward_request_id,
                        patient_admission_id,
                        discount_percentage,
                        company_name: '',
                        invoice_number,
                        medicine_type,
                        appointment_id,
                        discount_amount,
                        currency_type: '',
                        bill_date,
                        customer_name: '',
                        previous_balance,
                        bill_address: '',
                        ReceivedAmount,
                        BillDiscription: '-',
                        BillAmount,
                        invoice_discount,
                        ProductList,
                        "_token": "{{ csrf_token() }}"
                    },
                    url: "{{ route('pos.save_retail_sale') }}",
                    success: function(response) {
                        $("#save_bill").show();
                        $(this).html('<i class="fas fa-save me-2"></i>Save Bill');
                        popupMsg("Bill saved successfully!", "success");

                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);

                        var url = "{{route('pos.print_customer_bill')}}/" + response.id;
                        window.open(url, '_blank');
                    },
                    error: function() {
                        $("#save_bill").show();
                        $(this).html('<i class="fas fa-save me-2"></i>Save Bill');
                        popupMsg("Error saving bill. Please try again.", "error");
                    }
                });
            });

            // Enhanced table editing
            $("body").on("click", ".editable", function() {
                var $cell = $(this);
                selectedRow = $(this);
                var currentValue = $cell.text();
                if (currentValue == '') {
                    currentValue = preValue;
                } else {
                    preValue = currentValue;
                }
                $cell.html('<input type="number" class="form-control editable-input" value="' + currentValue + '">');
                $cell.find(".editable-input").focus();
            });

            $(document).on("blur", ".editable-input", function() {
                var $input = $(this);
                var newValue = $input.val();
                $input.parent().text(newValue);

                if (newValue == '' || newValue == null) {
                    return false;
                }

                var quantity = selectedRow.closest("tr").find("td:eq(2)").text();
                var rate = selectedRow.closest("tr").find("td:eq(3)").text().replace('Rs. ', '');
                var avaliable_qty = selectedRow.closest("tr").find("td:eq(6)").text();

                if (parseInt(quantity) > parseInt(avaliable_qty)) {
                    selectedRow.closest("tr").find("td:eq(2)").text(preValue);
                    quantity = preValue;
                    popupMsg("Exceeding Available Quantity. You can't change the value.", "error");
                }

                if (quantity == '' || rate == '' || quantity == null || rate == null) {
                    return false;
                }

                var total = (parseFloat(quantity) * parseFloat(rate)).toFixed(2);
                selectedRow.closest("tr").find("td:eq(4)").text('Rs. ' + total);
                var product_id = selectedRow.closest("tr").find("td:eq(0)").attr("data-id");
                updateProductByID(product_id, quantity, rate, parseFloat(total));
            });
        });

        // Initialize DataTable for previous bills
        var previous_bill_table = $('#previous-bill-table').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 8,
            ajax: {
                url: `{{ route('pos.retail_previous_bills') }}`,
            },
            columns: [{
                    data: 'InvoiceNo',
                    name: 'InvoiceNo',
                    searchable: true
                },
                {
                    data: 'patient.name',
                    name: 'patient.name',
                    searchable: true
                },
                {
                    data: 'received_amount',
                    name: 'received_amount',
                    searchable: true
                },
                {
                    data: 'action',
                    name: 'action',
                    searchable: true
                }
            ],
            responsive: true,
            processing: true,
            serverSide: true,
            searching: true,
            sorting: true,
            paging: true,
        });

        function calculateReturnAmount() {
            var bill_amount = parseFloat($("#ReceivedAmount").val()) || 0;
            var invoice_discount = parseFloat($("#invoice_discount").val()) || 0;
            var ReceivedAmountFromCustomer = parseFloat($("#ReceivedAmountFromCustomer").val()) || 0;

            // Calculate the actual net amount (bill_amount already includes discounts)
            var net_amount = bill_amount;

            // Calculate return amount
            var return_amount = ReceivedAmountFromCustomer - net_amount;

            // Update return field
            $("#ReturnToCustomer").val(return_amount.toFixed(2));

            // Change color based on return amount
            if (return_amount < 0) {
                $("#ReturnToCustomer").css('color', 'red');
                $("#ReturnToCustomer").css('font-weight', 'bold');
            } else {
                $("#ReturnToCustomer").css('color', 'green');
                $("#ReturnToCustomer").css('font-weight', 'bold');
            }
        }

        function removeProductByID(productID) {
            ProductList = ProductList.filter(product => product.ProductID != productID);
            reload_table();
        }

        function updateProductByID(product_id, quantity, rate, total) {
            let product = ProductList.find(product => product.ProductID == product_id);
            if (product) {
                product.Quantity = quantity;
                product.UnitePrice = rate;
                product.Total = total;
                reload_table();
            } else {
                reload_table();
            }
        }

        function getItemDetails() {
            currentAvailableQuantity = 0;
            var p_id = $("#product_id").val();
            if (p_id == '') {
                return false;
            }
            $.ajax({
                type: "post",
                dataType: "json",
                data: {
                    p_id: p_id,
                    _token: '{{ csrf_token() }}'
                },
                url: "{{route('pos.get_items_by_product_id')}}",
                success: function(response) {
                    if (response.status == true) {
                        $.each(response.data, function(key, value) {
                            if (value.is_product_kit) {
                                if (value.AvailableQuantity < value.qty) {
                                    popupMsg("Low stock for kit item: " + value.product.ProductName, "warning");
                                }
                                add_item_to_grid(value.product.ProductID, value.product.ProductName, value.product.unit_sale_price, value.product.name, value.AvailableQuantity, value.qty, '');
                            } else {
                                currentAvailableQuantity = value.AvailableQuantity ? value.AvailableQuantity : 0;
                            }
                        });
                    } else {
                        popupMsg("Item is Not Registered in Inventory", "error");
                        return;
                    }
                }
            });
        }

        function saveItemToBill() {
            var medicine_type = "{{$type}}";
            var dose_type = '';
            if (medicine_type == 'Home' || medicine_type == "Ward") {
                dose_type = $("#dose_type").val();
                if (dose_type == '') {
                    popupMsg("Please Select Dose Type. ", "error");
                    return false;
                }
            }

            var ProductID = $('#product_id').val();
            var Product = $('#product_id option:selected').text();
            var Name = $('#product_id option:selected').text();
            var AvailableQuantity = currentAvailableQuantity;
            var quantity = $("#sale_quantity").val();
            var UnitePrice = $("#SalePrice").val();

            if (ProductID == '' || Name == '' || quantity == '' || UnitePrice == '') {
                popupMsg("Please Fill All required fields.. ", "error");
                return false;
            }

            add_item_to_grid(ProductID, Product, UnitePrice, Name, AvailableQuantity, quantity, dose_type);
            clearForm();
            return true;
        }

        function clearForm() {
            $("#sale_quantity").val(1);
            $("#SalePrice").val('');
            $("#SalePrice_text").html('0.00');

            $("#product_id").val(null).trigger('change');
            $("#dose_type").val('-');
            taxPercentage = 0;
            currentAvailableQuantity = 0;
            $("#product_id").select2('open');
        }

        function get_prev_balance(e) {
            var value = $("#SID").val();
            if (value != '') {
                $.ajax({
                    type: "get",
                    url: "{{route('pos.customer_previous_balance')}}/" + value,
                    success: function(response) {
                        PreviousBalance = parseFloat(response).toFixed(2);
                        $("#previous_balance").val(PreviousBalance);
                        calculateBalance();
                    }
                });
            } else {
                $("#previous_balance").val(0);
            }
        }

        function calculateBalance() {
            var total_bill = (parseFloat($("#BillAmount").val()) + parseFloat(PreviousBalance)).toFixed(2);
        }

        function add_item_to_grid(ProductID, Product, UnitPrice, Name, AvailableQuantity, quantity = '', dose_type = '') {
            if (AvailableQuantity == 0) {
                popupMsg(Product + " Is not Available in Stock", "error");
                return false;
            }
            if (quantity > AvailableQuantity) {
                popupMsg("You are Exceeding Available Quantity.", "error");
                return false;
            }

            var Quantity = 1;
            if (quantity != '') {
                Quantity = quantity;
            }

            let exists = ProductList.some(item => item.ProductID === ProductID);

            if (!exists) {
                var totalAmount = (Quantity * UnitPrice).toFixed(2);
                var taxRate = taxPercentage / 100;
                var taxAmount = (totalAmount * taxRate).toFixed(2);

                var data_array = {
                    ProductID: ProductID,
                    Product: Product,
                    Name: Product,
                    UnitePrice: UnitPrice,
                    Quantity: Quantity,
                    Total: Quantity * UnitPrice,
                    AvailableQuantity: AvailableQuantity,
                    taxAmount: taxAmount,
                    taxPercentage: taxPercentage,
                    currentAvailableQuantity: currentAvailableQuantity,
                    dose_type: dose_type,
                };
                ProductList.push(data_array);
                reload_table();
            } else {
                popupMsg("Product already exists in list", "warning");
                return false;
            }
        }

        function reload_table() {
            $("#product_table").html('');
            var total_amount = 0;

            ProductList.forEach((value, key) => {
                var doseDisplay = value.dose_type ? `<span class="dose-badge">${value.dose_type}</span>` : '-';
                var qtyDisplay = `<span class="qty-badge">${value.AvailableQuantity}</span>`;

                var html = `<tr style="border-left: 4px solid var(--pharmacy-green);">
                        <td style="width: 5%" data-id="${value.ProductID}">${key+1}</td>
                        <td style="width: 25%; text-align: left; font-weight: 600;">${value.Name}</td>
                        <td style="width: 10%" class="editable" data-field="quantity">${value.Quantity}</td>
                        <td style="width: 12%">Rs. ${value.UnitePrice}</td>
                        <td style="width: 12%; font-weight: 600; color: var(--pharmacy-green);">Rs. ${value.Total}</td>
                        <td style="width: 12%">${doseDisplay}</td>
                        <td style="width: 12%">${qtyDisplay}</td>
                        <td style="width: 12%">
                            <button class="remove-btn remove_item" data-id="${value.ProductID}" title="Remove Item">
                                <i class="fas fa-times"></i>
                            </button>
                        </td>
                    </tr>`;
                total_amount = parseFloat(total_amount) + parseFloat(value.Total) + parseFloat(value.taxAmount || 0);
                $("#product_table").prepend(html);
            });

            // Calculate discounts and final amounts
            var bill_discount_percent = parseFloat($("#discount_id").val()) || 0;
            var discount_amount = (total_amount * bill_discount_percent) / 100;
            var invoice_discount = parseFloat($("#invoice_discount").val()) || 0;

            // Update UI fields
            $("#discount_amount").val(discount_amount.toFixed(2));
            $("#BillAmount").val(Math.ceil(total_amount));

            // Calculate net amount after all discounts
            var net_amount = Math.ceil(total_amount - discount_amount - invoice_discount);
            $("#ReceivedAmount").val(net_amount);

            // Add empty rows for better presentation
            if (ProductList.length < 10) {
                var length = 10 - ProductList.length;
                for (var i = 1; i <= length; i++) {
                    var html = `<tr style="opacity: 0.3;">
                            <td>&nbsp;</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>`;
                    $("#product_table").append(html);
                }
            }

            // Calculate return amount after updating all fields
            setTimeout(function() {
                calculateReturnAmount();
            }, 100);
        }

        function popupMsg(msg, msgtype) {
            var color = '#dc3545';
            if (msgtype.toLowerCase() == 'success') {
                color = '#28a745';
            } else if (msgtype.toLowerCase() == 'warning') {
                color = '#ffc107';
            }

            $("#popu-message").css('background-color', color).html(msg).fadeIn().delay(3000).fadeOut();
        }
    </script>

</body>

</html>