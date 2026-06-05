@extends('layouts.'.config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('stylesheets')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <style>
        .product-item, .investigation-item {
            background: #f8f9fa;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }
        .remove-btn {
            cursor: pointer;
        }
        .product-badge, .investigation-badge {
            display: inline-block;
            background: #696cff;
            color: white;
            padding: 8px 15px;
            margin: 5px;
            border-radius: 20px;
            font-size: 14px;
            position: relative;
        }
        .product-badge .remove-icon, .investigation-badge .remove-icon {
            margin-left: 10px;
            cursor: pointer;
            font-weight: bold;
            color: #fff;
            background: rgba(255,255,255,0.3);
            padding: 2px 6px;
            border-radius: 50%;
            font-size: 12px;
        }
        .product-badge .remove-icon:hover, .investigation-badge .remove-icon:hover {
            background: rgba(255,255,255,0.5);
        }
        #products_section, #investigations_section {
            transition: all 0.3s ease;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>{{ $opdType ? 'Edit OPD Type' : 'Create OPD Type' }}</h4>
                </div>
                <div class="card-body">
                    <form id="opd-type-form">
                        @csrf
                        <input type="hidden" id="opd_type_id" value="{{ $opdType ? $opdType->id : 0 }}">

                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">OPD Type Name<span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control" required
                                       value="{{ $opdType ? $opdType->name : '' }}" placeholder="Enter OPD type name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fees<span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="fees" id="fees" class="form-control" required
                                       value="{{ $opdType ? $opdType->fees : '' }}" placeholder="Enter fees amount">
                            </div>
                        </div>

                        <!-- Including Options -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="including_medicine" 
                                           id="including_medicine" value="1"
                                           {{ $opdType && $opdType->including_medicine == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="including_medicine">
                                        Including Medicine
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="including_labs" 
                                           id="including_labs" value="1"
                                           {{ $opdType && $opdType->including_labs == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="including_labs">
                                        Including Labs
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Products Section -->
                        <div class="row mb-4" id="products_section" style="display: {{ ($opdType && $opdType->including_medicine == 1) ? 'block' : 'none' }};">
                            <div class="col-12">
                                <h5>Assign Products (Medicine) <span class="badge bg-primary" id="products_counter">{{ $opdType ? $opdType->products->count() : 0 }}</span></h5>
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Select Product</label>
                                        <select id="product_select" class="form-control select2">
                                            <option value="">-- Select Product --</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->ProductID }}" 
                                                        data-name="{{ $product->ProductName }}">
                                                    {{ $product->ProductName }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div id="products_list" class="d-flex flex-wrap">
                                    @if($opdType && $opdType->products->count() > 0)
                                        @foreach($opdType->products as $product)
                                            <div class="product-badge" data-product-id="{{ $product->ProductID }}" data-product-name="{{ $product->ProductName }}">
                                                {{ $product->ProductName }}
                                                <input type="hidden" name="products[]" value="{{ $product->ProductID }}">
                                                <span class="remove-icon remove-product">&times;</span>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Investigations Section -->
                        <div class="row mb-4" id="investigations_section" style="display: {{ ($opdType && $opdType->including_labs == 1) ? 'block' : 'none' }};">
                            <div class="col-12">
                                <h5>Assign Investigations (Labs) <span class="badge bg-success" id="investigations_counter">{{ $opdType ? $opdType->investigations->count() : 0 }}</span></h5>
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Select Investigation</label>
                                        <select id="investigation_select" class="form-control select2">
                                            <option value="">-- Select Investigation --</option>
                                            @foreach($investigations as $investigation)
                                                <option value="{{ $investigation->id }}" 
                                                        data-name="{{ $investigation->name }}">
                                                    {{ $investigation->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div id="investigations_list" class="d-flex flex-wrap">
                                    @if($opdType && $opdType->investigations->count() > 0)
                                        @foreach($opdType->investigations as $investigation)
                                            <div class="investigation-badge" data-investigation-id="{{ $investigation->id }}" data-investigation-name="{{ $investigation->name }}">
                                                {{ $investigation->name }}
                                                <input type="hidden" name="investigations[]" value="{{ $investigation->id }}">
                                                <span class="remove-icon remove-investigation">&times;</span>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-success" id="submit_btn">
                                    <i class="bx bx-save"></i> {{ $opdType ? 'Update OPD Type' : 'Create OPD Type' }}
                                </button>
                                <a href="{{ route('pos.opd_types') }}" class="btn btn-secondary">
                                    <i class="bx bx-x"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2();

            // Function to update product counter
            function updateProductCounter() {
                var count = $('.product-badge').length;
                $('#products_counter').text(count);
            }

            // Function to update investigation counter
            function updateInvestigationCounter() {
                var count = $('.investigation-badge').length;
                $('#investigations_counter').text(count);
            }

            // Toggle Products Section based on Including Medicine checkbox
            $('#including_medicine').change(function() {
                if ($(this).is(':checked')) {
                    $('#products_section').slideDown();
                } else {
                    $('#products_section').slideUp();
                    // Clear products when hiding
                    $('#products_list').empty();
                    updateProductCounter();
                }
            });

            // Toggle Investigations Section based on Including Labs checkbox
            $('#including_labs').change(function() {
                if ($(this).is(':checked')) {
                    $('#investigations_section').slideDown();
                } else {
                    $('#investigations_section').slideUp();
                    // Clear investigations when hiding
                    $('#investigations_list').empty();
                    updateInvestigationCounter();
                }
            });

            // Auto Add Product on Selection Change
            $('#product_select').change(function() {
                var productId = $(this).val();
                var productName = $(this).find('option:selected').data('name');
                var $selectedOption = $(this).find('option:selected');

                if (!productId) {
                    return;
                }

                // Check if product already added
                if ($('.product-badge[data-product-id="' + productId + '"]').length > 0) {
                    alert('This product is already added');
                    $(this).val('').trigger('change');
                    $('#product_select').select2('open');
                    return;
                }

                var html = `
                    <div class="product-badge" data-product-id="${productId}" data-product-name="${productName}">
                        ${productName}
                        <input type="hidden" name="products[]" value="${productId}">
                        <span class="remove-icon remove-product">&times;</span>
                    </div>
                `;

                $('#products_list').append(html);
                
                // Remove the selected option from dropdown
                $selectedOption.remove();
                
                // Update counter
                updateProductCounter();
                
                $(this).val('').trigger('change');
                // Keep dropdown open after adding
                setTimeout(function() {
                    $('#product_select').select2('open');
                }, 50);
            });

            // Auto Add Investigation on Selection Change
            $('#investigation_select').change(function() {
                var investigationId = $(this).val();
                var investigationName = $(this).find('option:selected').data('name');
                var $selectedOption = $(this).find('option:selected');

                if (!investigationId) {
                    return;
                }

                // Check if investigation already added
                if ($('.investigation-badge[data-investigation-id="' + investigationId + '"]').length > 0) {
                    alert('This investigation is already added');
                    $(this).val('').trigger('change');
                    $('#investigation_select').select2('open');
                    return;
                }

                var html = `
                    <div class="investigation-badge" data-investigation-id="${investigationId}" data-investigation-name="${investigationName}">
                        ${investigationName}
                        <input type="hidden" name="investigations[]" value="${investigationId}">
                        <span class="remove-icon remove-investigation">&times;</span>
                    </div>
                `;

                $('#investigations_list').append(html);
                
                // Remove the selected option from dropdown
                $selectedOption.remove();
                
                // Update counter
                updateInvestigationCounter();
                
                $(this).val('').trigger('change');
                // Keep dropdown open after adding
                setTimeout(function() {
                    $('#investigation_select').select2('open');
                }, 50);
            });

            // Remove Product
            $(document).on('click', '.remove-product', function() {
                var $badge = $(this).closest('.product-badge');
                var productId = $badge.attr('data-product-id');
                var productName = $badge.attr('data-product-name');
                
                // Add the product back to the dropdown
                var newOption = new Option(productName, productId, false, false);
                $(newOption).attr('data-name', productName);
                $('#product_select').append(newOption);
                
                // Remove the badge
                $badge.remove();
                
                // Update counter
                updateProductCounter();
            });

            // Remove Investigation
            $(document).on('click', '.remove-investigation', function() {
                var $badge = $(this).closest('.investigation-badge');
                var investigationId = $badge.attr('data-investigation-id');
                var investigationName = $badge.attr('data-investigation-name');
                
                // Add the investigation back to the dropdown
                var newOption = new Option(investigationName, investigationId, false, false);
                $(newOption).attr('data-name', investigationName);
                $('#investigation_select').append(newOption);
                
                // Remove the badge
                $badge.remove();
                
                // Update counter
                updateInvestigationCounter();
            });

            // Form Submit
            $('#opd-type-form').submit(function(e) {
                e.preventDefault();

                var opdTypeId = $('#opd_type_id').val();
                var url = opdTypeId > 0 
                    ? "{{ url('update_opd_type') }}/" + opdTypeId 
                    : "{{ route('pos.store_opd_type') }}";

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.status) {
                            alert(response.message);
                            window.location.href = "{{ route('pos.opd_types') }}";
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON?.message || 'Error saving OPD type';
                        alert(errors);
                    }
                });
            });
        });
    </script>
@endpush
