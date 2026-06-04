<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Low Sale Price Products Report</title>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">

    <style>
        body{
            overflow-x:hidden;
            background-color: #f5f5f5;
        }
        
        .wrapper{
            padding: 20px;
        }

        th {
            background-color: #9f1c20 !important;
            color: white;
            font-weight: bold;
            text-align: center;
            padding: 10px !important;
        }
        
        td{
            font-size: 13px;
            font-weight: bold;
            padding: 8px !important;
        }
        
        table input{
            width: 100%;
            height: 35px;
            font-weight: bold;
            border: 2px solid #ddd;
            padding: 5px;
        }
        
        .low-price-row {
            background-color: #ffcccc !important;
        }
        
        .low-price-row td {
            background-color: #ffcccc !important;
        }
        
        .btn-update {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 8px 15px;
            cursor: pointer;
            font-weight: bold;
        }
        
        .btn-update:hover {
            background-color: #218838;
        }
        
        .alert {
            margin: 10px 0;
            padding: 15px;
            border-radius: 5px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .page-header {
            background-color: #48af5a;
            color: white;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .calculated-field {
            background-color: #e9ecef !important;
            color: #495057;
        }
        
        /* Highlight the Last Purchase column to make it stand out */
        .last-purchase-price {
            background-color: #fff3cd !important; /* light yellow */
            color: #856404 !important; /* dark yellow/brown text */
            border-color: #ffeeba !important;
        }
        
        .filter-section {
            background-color: white;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .filter-section select {
            padding: 8px 12px;
            border: 2px solid #48af5a;
            border-radius: 5px;
            font-weight: bold;
        }
        
        .filter-section button {
            background-color: #48af5a;
            color: white;
            padding: 8px 20px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .filter-section button:hover {
            background-color: #3a9c4a;
        }
    </style>
</head>

<body>

<div class="wrapper">
    <div class="page-header">
        <h4 style="margin: 0; text-align: center;">Product Price Management Report</h4>
        <p style="margin: 5px 0 0 0; text-align: center; font-size: 14px;">Manage and Update Product Prices</p>
    </div>

    <div class="filter-section">
        <form method="GET" action="{{ route('pos.low_sale_price_products') }}" id="filterForm">
            <div class="row align-items-center mb-3">
                <div class="col-md-3">
                    <label for="filter" style="font-weight: bold; margin-bottom: 5px; display: block;">Price/Profit Filter:</label>
                    <select name="filter" id="filter" class="form-select" onchange="document.getElementById('filterForm').submit();">
                        <option value="all_active" {{ ($filter ?? 'sale_price_issue') == 'all_active' ? 'selected' : '' }}>All Active Products</option>
                        <option value="sale_price_issue" {{ ($filter ?? 'sale_price_issue') == 'sale_price_issue' ? 'selected' : '' }}>Sale Price ≤ Pack Price</option>
                        <option value="profit_less_15" {{ ($filter ?? 'sale_price_issue') == 'profit_less_15' ? 'selected' : '' }}>Profit < 15%</option>
                        <option value="profit_less_10" {{ ($filter ?? 'sale_price_issue') == 'profit_less_10' ? 'selected' : '' }}>Profit < 10%</option>
                        <option value="profit_less_5" {{ ($filter ?? 'sale_price_issue') == 'profit_less_5' ? 'selected' : '' }}>Profit < 5%</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="qty_filter" style="font-weight: bold; margin-bottom: 5px; display: block;">Quantity Filter:</label>
                    <select name="qty_filter" id="qty_filter" class="form-select" onchange="document.getElementById('filterForm').submit();">
                        <option value="" {{ ($qty_filter ?? '') == '' ? 'selected' : '' }}>All Quantities</option>
                        <option value="qty_greater_than_zero" {{ ($qty_filter ?? '') == 'qty_greater_than_zero' ? 'selected' : '' }}>Available Qty > 0</option>
                        <option value="qty_greater_equal_zero" {{ ($qty_filter ?? '') == 'qty_greater_equal_zero' ? 'selected' : '' }}>Available Qty ≥ 0</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="search" style="font-weight: bold; margin-bottom: 5px; display: block;">Search Product:</label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="Enter product name..." value="{{ $search ?? '' }}">
                </div>
                <div class="col-md-3" style="padding-top: 25px;">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search"></i> Search
                    </button>
                    @if(!empty($search) || $qty_filter || ($filter != 'sale_price_issue'))
                        <a href="{{ route('pos.low_sale_price_products') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-md-12" style="text-align: right;">
                    <strong>Total Products: {{ $products->total() }}</strong>
                </div>
            </div>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card border-0">
                <div class="card-body">
                    <table class="table table-bordered table-striped m-0">
                        <thead>
                            <tr>
                                <th style="width: 4%">#</th>
                                <th style="width: 18%">Product Name</th>
                                <th style="width: 7%">Pack Price</th>
                                <th style="width: 7%">Pack Size</th>
                                <th style="width: 7%">Unit Purchase</th>
                                <th style="width: 7%">Sale Price</th>
                                <th style="width: 7%">Unit Sale</th>
                                <th style="width: 7%">Last Purchase</th>
                                <th style="width: 7%">Available Qty</th>
                                <th style="width: 7%">Profit %</th>
                                <th style="width: 10%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $key => $product)
                                @php
                                    $rowClass = ($product->SalePrice <= $product->pack_price) ? 'low-price-row' : '';
                                    // Calculate profit percentage
                                    $profitPercentage = 0;
                                    if($product->pack_price > 0) {
                                        $profitPercentage = (($product->SalePrice - $product->pack_price) / $product->pack_price) * 100;
                                    }
                                @endphp
                                <tr class="{{ $rowClass }}">
                                    <form method="post" action="{{ route('pos.update_product_price') }}">
                                        @csrf
                                        <input type="hidden" name="ProductID" value="{{ $product->ProductID }}">
                                        
                                        <td>{{ $products->firstItem() + $key }}</td>
                                        <td>
                                            {{ $product->ProductName }}
                                            @if($product->generic_name)
                                                <br><small style="color: #666;">({{ $product->generic_name->name }})</small>
                                            @endif
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="pack_price" class="pack-price-input" 
                                                   value="{{ $product->pack_price }}" required>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="pack_size" class="pack-size-input" 
                                                   value="{{ $product->pack_size }}" required>
                                        </td>
                                        <td>
                                            <input type="text" class="calculated-field unit-purchase-price" 
                                                   value="{{ number_format($product->PurchasePrice, 2) }}" readonly>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="sale_price" class="sale-price-input" 
                                                   value="{{ $product->SalePrice }}" required>
                                        </td>
                                        <td>
                                            <input type="text" class="calculated-field unit-sale-price" 
                                                   value="{{ number_format($product->unit_sale_price, 2) }}" readonly>
                                        </td>
                                        <td>
                                            <input type="text" class="calculated-field last-purchase-price" 
                                                   value="{{ $product->last_purchase_price ? number_format($product->last_purchase_price, 2) : 'N/A' }}" readonly>
                                        </td>
                                        <td>
                                            <input type="text" class="calculated-field available-qty" 
                                                   value="{{ $product->avaliable_quantity ?? 0 }}" readonly>
                                        </td>
                                        <td>
                                            <input type="text" class="calculated-field profit-percentage" 
                                                   value="{{ number_format($profitPercentage, 2) }}%" readonly>
                                        </td>
                                        <td style="text-align: center;">
                                            <button type="submit" class="btn btn-update">Update</button>
                                        </td>
                                    </form>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" style="text-align: center; padding: 20px;">
                                        No products found for the selected filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div style="margin-top: 20px;">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('assets/js/jquery-3.5.1.min.js') }}"></script>
<script>
    $(document).ready(function() {
        // Function to calculate unit prices and profit percentage
        function calculateUnitPrices(row) {
            var packPrice = parseFloat(row.find('.pack-price-input').val()) || 0;
            var packSize = parseFloat(row.find('.pack-size-input').val()) || 1;
            var salePrice = parseFloat(row.find('.sale-price-input').val()) || 0;
            
            // Calculate unit purchase price
            var unitPurchasePrice = packPrice / packSize;
            row.find('.unit-purchase-price').val(unitPurchasePrice.toFixed(2));
            
            // Calculate unit sale price
            var unitSalePrice = salePrice / packSize;
            row.find('.unit-sale-price').val(unitSalePrice.toFixed(2));
            
            // Calculate profit percentage
            var profitPercentage = 0;
            if (packPrice > 0) {
                profitPercentage = ((salePrice - packPrice) / packPrice) * 100;
            }
            row.find('.profit-percentage').val(profitPercentage.toFixed(2) + '%');
            
            // Update row highlighting
            if (salePrice <= packPrice) {
                row.addClass('low-price-row');
            } else {
                row.removeClass('low-price-row');
            }
        }
        
        // Recalculate on pack price change
        $(document).on('keyup change', '.pack-price-input', function() {
            var row = $(this).closest('tr');
            calculateUnitPrices(row);
        });
        
        // Recalculate on pack size change
        $(document).on('keyup change', '.pack-size-input', function() {
            var row = $(this).closest('tr');
            calculateUnitPrices(row);
        });
        
        // Recalculate on sale price change
        $(document).on('keyup change', '.sale-price-input', function() {
            var row = $(this).closest('tr');
            calculateUnitPrices(row);
        });
        
        // Validate before form submission
        $(document).on('submit', 'form', function(e) {
            var form = $(this);
            var packPrice = parseFloat(form.find('.pack-price-input').val());
            var salePrice = parseFloat(form.find('.sale-price-input').val());
            
            if (salePrice < packPrice) {
                if (!confirm('⚠️ WARNING: Sale Price (' + salePrice + ') is less than Pack Price (' + packPrice + ').\n\nDo you want to continue?')) {
                    e.preventDefault();
                    return false;
                }
            }
        });
    });
</script>

</body>
</html>
