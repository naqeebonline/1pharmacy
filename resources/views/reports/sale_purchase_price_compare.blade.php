@extends('layouts.'.config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('stylesheets')
<style>
    .compare-table input.sale-price-input {
        min-width: 100px;
        font-weight: 600;
        border: 2px solid #696cff;
    }

    .compare-table input.sale-price-input:focus {
        border-color: #5f61e6;
        box-shadow: 0 0 0 0.15rem rgba(105, 108, 255, 0.25);
    }

    .compare-table input.unit-sale-input {
        min-width: 130px;
        width: 100%;
        font-weight: 600;
        border: 2px solid #03c3ec;
    }

    .compare-table input.unit-sale-input:focus {
        border-color: #03a9d4;
        box-shadow: 0 0 0 0.15rem rgba(3, 195, 236, 0.25);
    }

    .compare-table th.col-unit-sale,
    .compare-table td.col-unit-sale {
        min-width: 140px;
        width: 12%;
    }

    .compare-table tr.issue-row {
        background-color: #fff5f5;
    }

    .compare-table tr.selected-row {
        background-color: #eef2ff;
    }

    .price-readonly {
        background: #f8f9fa;
        font-weight: 600;
    }

    .suggested-price {
        font-size: 0.8rem;
        color: #696cff;
        cursor: pointer;
        text-decoration: underline;
    }

    .bulk-toolbar {
        background: #f8f9fa;
        border: 1px solid #e7e7e8;
        border-radius: 8px;
        padding: 12px 16px;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="card-title mb-0">
                        <i class="bx bx-transfer-alt me-2"></i>Sale vs Purchase Price (Active GRN)
                    </h5>
                    <p class="text-muted mb-0 small" id="filterDescription">
                        {{ $filter_options[$filter] ?? '' }} — active stock: <code>grn_details.ProductStatus = 1</code>
                    </p>
                </div>
                <a href="{{ route('reports.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bx bx-arrow-back me-1"></i>Reports
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('reports.sale_purchase_price_compare') }}" class="row g-2 align-items-end" id="filterForm">
                    <div class="col-md-4">
                        <label class="form-label">Filter</label>
                        <select name="filter" id="priceFilter" class="form-select">
                            @foreach($filter_options as $value => $label)
                                <option value="{{ $value }}" {{ ($filter ?? 'sale_lte_purchase') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Search product</label>
                        <input type="text" name="search" class="form-control" placeholder="Product name..."
                            value="{{ $search }}">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bx bx-filter-alt me-1"></i>Apply
                        </button>
                        @if($search || ($filter ?? 'sale_lte_purchase') !== 'sale_lte_purchase')
                            <a href="{{ route('reports.sale_purchase_price_compare') }}" class="btn btn-outline-secondary">Reset</a>
                        @endif
                        <span class="ms-2 text-muted">Total: <strong>{{ $products->total() }}</strong></span>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="bulk-toolbar mb-3">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-auto">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAllRows">
                                <label class="form-check-label" for="selectAllRows">Select all on page</label>
                            </div>
                        </div>
                        <div class="col-md-auto">
                            <label class="form-label mb-0 me-1">Markup % on GRN price:</label>
                            <input type="number" id="bulkMarginPercent" class="form-control form-control-sm d-inline-block"
                                style="width: 80px;" value="15" min="0" step="0.1">
                        </div>
                        <div class="col-md-auto">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="applyMarginSelected">
                                Apply to selected
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-info" id="applyMarginAll">
                                Apply to all on page
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="useGrnPriceSelected">
                                Match GRN price
                            </button>
                        </div>
                        <div class="col-md-auto ms-md-auto">
                            <button type="button" class="btn btn-success" id="bulkSaveBtn">
                                <i class="bx bx-save me-1"></i>Save selected prices
                            </button>
                        </div>
                    </div>
                </div>

                <div id="bulkSaveAlert" class="alert d-none" role="alert"></div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover compare-table mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 3%"></th>
                                <th>#</th>
                                <th>Product</th>
                                <th class="text-end">GRN Pack Price</th>
                                <th class="text-end">Product Pack Price</th>
                                <th class="text-end">Current Sale Price</th>
                                <th class="text-end">New Sale Price</th>
                                <th class="text-end col-unit-sale">Unit Sale Price</th>
                                <th class="text-center">Margin %</th>
                                <th class="text-center">GRN Qty</th>
                                <th>Batch</th>
                                <th>Expiry</th>
                                <th class="text-center">Quick</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $index => $row)
                                @php
                                    $packSize = max(1, (float) ($row->pack_size ?: $row->grn_pack_size ?: 1));
                                    $grnPack = (float) $row->grn_pack_price;
                                    $currentSale = (float) $row->SalePrice;
                                    $currentUnitSale = (float) ($row->unit_sale_price ?? ($currentSale / $packSize));
                                    $marginPct = $grnPack > 0
                                        ? round((($currentSale - $grnPack) / $grnPack) * 100, 2)
                                        : 0;
                                    $suggested15 = round($grnPack * 1.15, 2);
                                @endphp
                                <tr class="issue-row product-row" data-product-id="{{ $row->ProductID }}"
                                    data-grn-pack="{{ $grnPack }}" data-pack-size="{{ $packSize }}">
                                    <td class="text-center">
                                        <input type="checkbox" class="form-check-input row-select">
                                    </td>
                                    <td>{{ $products->firstItem() + $index }}</td>
                                    <td>
                                        <strong>{{ $row->ProductName }}</strong>
                                        <div class="text-muted small">ID: {{ $row->ProductID }}</div>
                                    </td>
                                    <td class="text-end price-readonly grn-pack-cell">{{ number_format($grnPack, 2) }}</td>
                                    <td class="text-end text-muted">{{ number_format((float) $row->product_pack_price, 2) }}</td>
                                    <td class="text-end current-sale-cell">{{ number_format($currentSale, 2) }}</td>
                                    <td class="text-end">
                                        <input type="number" step="0.01" min="0"
                                            class="form-control form-control-sm sale-price-input text-end"
                                            value="{{ $currentSale }}" data-original="{{ $currentSale }}">
                                    </td>
                                    <td class="text-end col-unit-sale">
                                        <input type="number" step="0.01" min="0"
                                            class="form-control form-control-sm unit-sale-input text-end"
                                            value="{{ number_format($currentUnitSale, 2, '.', '') }}"
                                            data-original="{{ number_format($currentUnitSale, 2, '.', '') }}">
                                    </td>
                                    <td class="text-center margin-cell {{ $marginPct < 0 ? 'text-danger' : 'text-success' }}">
                                        {{ number_format($marginPct, 2) }}%
                                    </td>
                                    <td class="text-center">{{ number_format((float) $row->grn_remaining_qty, 0) }}</td>
                                    <td>{{ $row->batch_no ?? '-' }}</td>
                                    <td>{{ $row->expiry_date ? date('d-M-Y', strtotime($row->expiry_date)) : '-' }}</td>
                                    <td class="text-center text-nowrap">
                                        <button type="button" class="btn btn-xs btn-outline-primary btn-sm apply-suggested"
                                            data-price="{{ $suggested15 }}" title="GRN + 15%">+15%</button>
                                        <button type="button" class="btn btn-xs btn-outline-secondary btn-sm apply-grn-match"
                                            title="Set = GRN pack price">=GRN</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="14" class="text-center py-4 text-muted">
                                        No products found for the selected filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const bulkUpdateUrl = @json(route('reports.sale_purchase_price_compare.bulk_update'));
    const csrfToken = @json(csrf_token());

    function formatNum(n, d = 2) {
        return parseFloat(n || 0).toFixed(d);
    }

    function updateSelectAllCheckbox() {
        const total = $('.row-select').length;
        const checked = $('.row-select:checked').length;
        $('#selectAllRows').prop('checked', total > 0 && total === checked);
    }

    function rowPriceChanged($row) {
        const origSale = parseFloat($row.find('.sale-price-input').data('original')) || 0;
        const origUnit = parseFloat($row.find('.unit-sale-input').data('original')) || 0;
        const sale = parseFloat($row.find('.sale-price-input').val()) || 0;
        const unit = parseFloat($row.find('.unit-sale-input').val()) || 0;
        return Math.abs(sale - origSale) > 0.009 || Math.abs(unit - origUnit) > 0.009;
    }

    function syncRowSelectionFromPrices($row) {
        if (rowPriceChanged($row)) {
            $row.find('.row-select').prop('checked', true);
            $row.addClass('selected-row');
        } else {
            $row.find('.row-select').prop('checked', false);
            $row.removeClass('selected-row');
        }
        updateSelectAllCheckbox();
    }

    function recalcRowFromSale($row) {
        const packSize = parseFloat($row.data('pack-size')) || 1;
        const salePrice = parseFloat($row.find('.sale-price-input').val()) || 0;
        const unitSale = salePrice / packSize;
        $row.find('.unit-sale-input').val(formatNum(unitSale));
        updateRowMargin($row, salePrice);
        syncRowSelectionFromPrices($row);
    }

    function recalcRowFromUnit($row) {
        const packSize = parseFloat($row.data('pack-size')) || 1;
        const unitSale = parseFloat($row.find('.unit-sale-input').val()) || 0;
        const salePrice = unitSale * packSize;
        $row.find('.sale-price-input').val(formatNum(salePrice));
        updateRowMargin($row, salePrice);
        syncRowSelectionFromPrices($row);
    }

    function updateRowMargin($row, salePrice) {
        const grnPack = parseFloat($row.data('grn-pack')) || 0;
        const margin = grnPack > 0 ? ((salePrice - grnPack) / grnPack) * 100 : 0;
        const $marginCell = $row.find('.margin-cell');
        $marginCell.text(formatNum(margin) + '%');
        $marginCell.removeClass('text-danger text-success')
            .addClass(margin < 0 ? 'text-danger' : 'text-success');

        if (salePrice <= grnPack) {
            $row.addClass('issue-row');
        } else {
            $row.removeClass('issue-row');
        }
    }

    $(document).on('input change', '.sale-price-input', function () {
        recalcRowFromSale($(this).closest('tr'));
    });

    $(document).on('input change', '.unit-sale-input', function () {
        recalcRowFromUnit($(this).closest('tr'));
    });

    $(document).on('click', '.apply-suggested', function () {
        const price = $(this).data('price');
        const $row = $(this).closest('tr');
        $row.find('.sale-price-input').val(price);
        recalcRowFromSale($row);
    });

    $(document).on('click', '.apply-grn-match', function () {
        const $row = $(this).closest('tr');
        const grnPack = parseFloat($row.data('grn-pack')) || 0;
        $row.find('.sale-price-input').val(formatNum(grnPack));
        recalcRowFromSale($row);
    });

    function applyMarginToRows($rows, marginPercent) {
        const pct = parseFloat(marginPercent) || 0;
        $rows.each(function () {
            const $row = $(this);
            const grnPack = parseFloat($row.data('grn-pack')) || 0;
            const newPrice = grnPack * (1 + pct / 100);
            $row.find('.sale-price-input').val(formatNum(newPrice));
            recalcRowFromSale($row);
        });
    }

    $('#applyMarginSelected').on('click', function () {
        const margin = $('#bulkMarginPercent').val();
        applyMarginToRows($('tr.product-row').filter(function () {
            return $(this).find('.row-select').is(':checked');
        }), margin);
    });

    $('#applyMarginAll').on('click', function () {
        applyMarginToRows($('tr.product-row'), $('#bulkMarginPercent').val());
    });

    $('#useGrnPriceSelected').on('click', function () {
        $('tr.product-row').filter(function () {
            return $(this).find('.row-select').is(':checked');
        }).each(function () {
            const $row = $(this);
            $row.find('.sale-price-input').val(formatNum($row.data('grn-pack')));
            recalcRowFromSale($row);
        });
    });

    $('#selectAllRows').on('change', function () {
        const checked = $(this).is(':checked');
        $('.row-select').prop('checked', checked);
        $('tr.product-row').toggleClass('selected-row', checked);
    });

    $(document).on('change', '.row-select', function () {
        $(this).closest('tr').toggleClass('selected-row', $(this).is(':checked'));
        updateSelectAllCheckbox();
    });

    $('#bulkSaveBtn').on('click', function () {
        const payload = [];
        $('tr.product-row').each(function () {
            const $row = $(this);
            if (!$row.find('.row-select').is(':checked')) {
                return;
            }
            const productId = $row.data('product-id');
            const salePrice = parseFloat($row.find('.sale-price-input').val());
            const unitSalePrice = parseFloat($row.find('.unit-sale-input').val());
            if (!productId || isNaN(salePrice) || isNaN(unitSalePrice)) {
                return;
            }
            payload.push({
                product_id: productId,
                sale_price: salePrice,
                unit_sale_price: unitSalePrice
            });
        });

        if (payload.length === 0) {
            showAlert('warning', 'Change sale price on a product (it will auto-select), or tick rows to update.');
            return;
        }

        const $btn = $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

        $.ajax({
            url: bulkUpdateUrl,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            data: { products: payload },
            success: function (res) {
                showAlert('success', res.message || 'Prices updated.');
                setTimeout(function () { window.location.reload(); }, 1200);
            },
            error: function (xhr) {
                let msg = 'Failed to update prices.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                showAlert('danger', msg);
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="bx bx-save me-1"></i>Save selected prices');
            }
        });
    });

    function showAlert(type, message) {
        const $alert = $('#bulkSaveAlert');
        $alert.removeClass('d-none alert-success alert-danger alert-warning')
            .addClass('alert-' + type)
            .text(message);
    }

    $('tr.product-row').each(function () {
        const $row = $(this);
        recalcRowFromSale($row);
        $row.find('.row-select').prop('checked', false);
        $row.removeClass('selected-row');
    });
    updateSelectAllCheckbox();

    $('#priceFilter').on('change', function () {
        $('#filterForm').submit();
    });
</script>
@endpush
