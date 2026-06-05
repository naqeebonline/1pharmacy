@extends('layouts.'.config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('stylesheets')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
<style>
    .stock-purchase-table th,
    .stock-purchase-table td {
        vertical-align: middle;
        font-size: 0.875rem;
    }

    .stock-purchase-table .text-end {
        text-align: right !important;
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
                        <i class="bx bx-package me-2"></i>Stock Purchase Value Report
                    </h5>
                    <p class="text-muted mb-0 small">
                        Active GRN stock batches only — <code>grn_details.ProductStatus = 1</code>
                    </p>
                </div>
                <a href="{{ route('reports.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bx bx-arrow-back me-1"></i>Reports
                </a>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('reports.stock_purchase_value') }}" class="row g-3 align-items-end" id="filterForm">
                    <div class="col-md-3">
                        <label class="form-label">Filter by</label>
                        <select name="filter_type" id="filterType" class="form-select" required>
                            <option value="generic_name" {{ ($filterType ?? '') === 'generic_name' ? 'selected' : '' }}>Generic Name</option>
                            <option value="item_form" {{ ($filterType ?? '') === 'item_form' ? 'selected' : '' }}>Item Form</option>
                            <option value="product" {{ ($filterType ?? '') === 'product' ? 'selected' : '' }}>Product</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <div class="filter-group" id="genericNameGroup">
                            <label class="form-label">Generic Name</label>
                            <select name="filter_id" id="genericNameSelect" class="form-select select2-filter">
                                <option value=""></option>
                                @foreach($genericNames as $item)
                                    <option value="{{ $item->id }}"
                                        {{ ($filterType ?? 'generic_name') === 'generic_name' && (int) ($filterId ?? 0) === (int) $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-group d-none" id="itemFormGroup">
                            <label class="form-label">Item Form</label>
                            <select id="itemFormSelect" class="form-select select2-filter">
                                <option value=""></option>
                                @foreach($itemForms as $item)
                                    <option value="{{ $item->id }}"
                                        {{ ($filterType ?? '') === 'item_form' && (int) ($filterId ?? 0) === (int) $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-group d-none" id="productGroup">
                            <label class="form-label">Product</label>
                            <select id="productSelect" class="form-select select2-filter">
                                <option value=""></option>
                                @foreach($products as $item)
                                    <option value="{{ $item->ProductID }}"
                                        {{ ($filterType ?? '') === 'product' && (int) ($filterId ?? 0) === (int) $item->ProductID ? 'selected' : '' }}>
                                        {{ $item->ProductID }} — {{ $item->ProductName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-filter-alt me-1"></i>Filter
                        </button>
                        @if($hasFilter ?? false)
                            <a href="{{ route('reports.stock_purchase_value') }}" class="btn btn-outline-secondary">Reset</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        @if($hasFilter ?? false)
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">{{ $filterLabel }}</h6>
                    @if($rows)
                        <small class="text-muted">Showing {{ $rows->firstItem() }}–{{ $rows->lastItem() }} of {{ $rows->total() }} row(s) — grouped by product &amp; unit cost</small>
                    @endif
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0 stock-purchase-table">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%;">S.No</th>
                                    <th style="width: 12%;">Item Code</th>
                                    <th>Product Name</th>
                                    <th class="text-end" style="width: 14%;">Remaining Quantity</th>
                                    <th class="text-end" style="width: 12%;">Unit Cost</th>
                                    <th class="text-end" style="width: 14%;">Row Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rows as $index => $row)
                                    <tr>
                                        <td>{{ $rows->firstItem() + $index }}</td>
                                        <td>{{ $row->ProductID }}</td>
                                        <td>{{ $row->ProductName }}</td>
                                        <td class="text-end">{{ number_format((float) $row->RemainingQuantity, 2) }}</td>
                                        <td class="text-end">{{ number_format((float) $row->UnitCost, 2) }}</td>
                                        <td class="text-end fw-semibold">{{ number_format((float) $row->row_total, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">No active stock found for this filter.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($rows && $rows->count() > 0)
                                <tfoot class="table-secondary">
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Grand Total</td>
                                        <td class="text-end fw-bold">{{ number_format($totals['total_remaining'], 2) }}</td>
                                        <td></td>
                                        <td class="text-end fw-bold">{{ number_format($totals['total_row_value'], 2) }}</td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
                @if($rows && $rows->hasPages())
                    <div class="card-footer">
                        {{ $rows->links() }}
                    </div>
                @endif
            </div>
        @else
            <div class="alert alert-info mb-0">
                <i class="bx bx-info-circle me-1"></i>
                Select <strong>Generic Name</strong>, <strong>Item Form</strong>, or <strong>Product</strong>, choose a value, then click <strong>Filter</strong> to load the report.
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
<script>
$(document).ready(function () {
    const $filterType = $('#filterType');
    const $genericGroup = $('#genericNameGroup');
    const $itemFormGroup = $('#itemFormGroup');
    const $productGroup = $('#productGroup');
    const $genericSelect = $('#genericNameSelect');
    const $itemFormSelect = $('#itemFormSelect');
    const $productSelect = $('#productSelect');
    const $filterCardBody = $('#filterForm').closest('.card-body');
    const $allValueSelects = $genericSelect.add($itemFormSelect).add($productSelect);

    function destroySelect2($el) {
        if ($el.length && $el.hasClass('select2-hidden-accessible')) {
            $el.select2('destroy');
        }
    }

    function initSelect2($el) {
        destroySelect2($el);
        $el.select2({
            width: '100%',
            placeholder: 'Select...',
            allowClear: true,
            dropdownParent: $filterCardBody.length ? $filterCardBody : $(document.body)
        });
    }

    function syncFilterGroups() {
        const type = $filterType.val();

        $genericGroup.toggleClass('d-none', type !== 'generic_name');
        $itemFormGroup.toggleClass('d-none', type !== 'item_form');
        $productGroup.toggleClass('d-none', type !== 'product');

        $allValueSelects.each(function () {
            destroySelect2($(this));
            $(this).removeAttr('name');
        });

        let $active = $genericSelect;
        if (type === 'item_form') {
            $active = $itemFormSelect;
        } else if (type === 'product') {
            $active = $productSelect;
        }

        $active.attr('name', 'filter_id');
        initSelect2($active);
    }

    $filterType.on('change', syncFilterGroups);

    $('#filterForm').on('submit', function () {
        const type = $filterType.val();
        $allValueSelects.removeAttr('name');
        if (type === 'generic_name') {
            $genericSelect.attr('name', 'filter_id');
        } else if (type === 'item_form') {
            $itemFormSelect.attr('name', 'filter_id');
        } else if (type === 'product') {
            $productSelect.attr('name', 'filter_id');
        }
    });

    syncFilterGroups();
});
</script>
@endpush
