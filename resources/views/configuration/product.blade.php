@extends('layouts.'.config('settings.active_layout'))
@php $app_id = config('settings.app_id') @endphp

@push('stylesheets')
    <style>
        .table > :not(caption) > * > * {padding: 5px;}
    </style>

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
@endpush

@section('content')

    <div class="row">
        <div class="col-12">

            <!-- Traffic sources -->
            <div class="card">




                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="btn btn-primary add_new_record">Add New Product</div>
                        </div>

                        <div class="col-md-4">
                            <select class="form-control" name="store_id" id="store_id">
                                <option value="">Please select Store....</option>
                                @foreach($store as $key => $value)
                                    <option  value="{{$value->id}}" {{session('store_id') == $value->id ? "selected" : ''}}>{{$value->store_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">


                        <div class="col-12">

                            <div class="table-responsive" style="min-height: 200px">

                                <table id="users-list" class="table table-responsive table-striped data_mf_table table-condensed" >

                                    <thead>
                                    <tr>
                                        <th width="15%">Name</th>
                                        <th width="15%">Generic Name</th>
                                        <th >Type</th>
                                        <th >Make</th>
                                        <th >Pack Price</th>
                                        <th >Pack Size</th>
                                        <th >Purchase Price</th>
                                        <th >Sale Price</th>
                                        <th >Quantity Limit</th>
                                        <th >Avail. Qty</th>
                                        <th >Allow Discount %</th>
                                        <th >Barcode</th>
                                        <th  style="width: 10%">Action</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /traffic sources -->
        </div>
    </div>

    <div class="modal fade" id="add_new_record_model" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form class="modal-content form-submit-event" id="from_submit">
                <input type="hidden" id="id" name="id" value="0">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Product Name -->
                        <div class="col-md-6 mb-3">
                            <label for="nameBasic" class="form-label">Main Category<span
                                        class="asterisk">*</span></label>
                            <select name="main_category_id" required id="main_category_id" class="form-select">
                                <option disabled selected value="">Select Main Category...</option>
                                @forelse ($main_category as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="nameBasic" class="form-label">Sub Category<span
                                        class="asterisk">*</span></label>
                            <select name="sub_category_id" id="sub_category_id" class="form-select">
                                <option  selected value="">Sub Category...</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="nameBasic" class="form-label">Item Form<span
                                        class="asterisk">*</span></label>
                            <select name="item_form_id" required id="item_form_id" class="form-select">
                                <option disabled selected value="">Item Form...</option>
                                @forelse ($item_form as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>



                        <div class="col-md-6 mb-3">
                            <label for="nameBasic" class="form-label">Make<span
                                        class="asterisk">*</span></label>
                            <select name="item_make_id" required id="item_make_id" class="form-select">
                                <option disabled selected value="">Select one...</option>
                                @forelse ($make as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="market_name" class="form-label">Trade Name <span class="asterisk">*</span></label>
                            <input type="text" required id="ProductName" name="ProductName" class="form-control" placeholder="Enter product name" autocomplete="off">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="nameBasic" class="form-label">Generic Name<span
                                        class="asterisk">*</span></label>
                            <select name="generic_name_id" required id="generic_name_id" class="form-select">
                                <option value="">Select Generic Name</option>
                                @forelse ($generic_name as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>

                        <!-- Purchase Price -->
                        <div class="col-md-6 mb-3">
                            <label for="purchase_price" class="form-label">Pack Price</label>
                            <input type="number" id="pack_price" name="pack_price" class="form-control" placeholder="pack price" autocomplete="off">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="purchase_price" class="form-label">Pack Size</label>
                            <input type="number" id="pack_size" name="pack_size" class="form-control" placeholder="pack size" autocomplete="off">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="purchase_price" class="form-label">Unit Price</label>
                            <input type="number" id="PurchasePrice" name="PurchasePrice" class="form-control" placeholder="Enter purchase price" autocomplete="off">
                        </div>

                        <!-- Sale Price -->
                        <div class="col-md-6 mb-3">
                            <label for="sale_price" class="form-label">Sale Price</label>
                            <input type="number" id="SalePrice" name="SalePrice" class="form-control" placeholder="Enter sale price" autocomplete="off">
                        </div>

                        <!-- Quantity Limit -->
                        <div class="col-md-6 mb-3">
                            <label for="quantity_limit" class="form-label">Quantity Limit</label>
                            <input type="number" id="low_limit" name="low_limit" class="form-control" placeholder="Enter quantity limit" autocomplete="off">
                        </div>

                        <!-- Allow Percentage -->
                        <div class="col-md-6 mb-3">
                            <label for="allow_percentage" class="form-label">Allow Discount %</label>
                            <input type="number" id="allow_percentage" name="allow_percentage" class="form-control" placeholder="Enter allowed discount percentage" autocomplete="off" min="0" max="100" step="0.01">
                        </div>

                        <!-- Barcode Number -->
                        <div class="col-md-6 mb-3">
                            <label for="barcode_number" class="form-label">Barcode Number</label>
                            <input type="text" id="BarCode" name="BarCode" class="form-control" placeholder="Enter barcode number" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="submit_btn" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>



@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-responsive/datatables.responsive.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.form.min.js') }}"></script>
    <script>

        $("body").on("change","#main_category_id",function (e) {
            var id = $(this).val();
            getSubCategory(id);

        });

        $("body").on("keyup","#pack_price",function (e) {

            calculatePrice();

        });
        $("body").on("keyup","#pack_size",function (e) {
            calculatePrice();


        });

        function calculatePrice() {
            var pack_price = $("#pack_price").val();
            var pack_size = $("#pack_size").val();
            if(pack_price == ''){
                pack_price = 0;
            }

            if(pack_size == '' || pack_size == 0){
                pack_size = 1;
            }
            var purchase_price = pack_price / pack_size;
            var value = purchase_price.toFixed(2);

            $('#PurchasePrice').val(value);

        }

        function getSubCategory(id){
            $.ajax({
                type:"post",
                dataType:"json",
                data:{id:id,"_token": "{{ csrf_token() }}"},
                url:"{{route('pos.get_sub_category')}}",
                success:function(response){
                    if (response.status) {
                        $("#sub_category_id").html('');
                        $("#sub_category_id").append(`<option value="">Select sub category...</option>`);
                        const $select = $("#sub_category_id");
                        $.each(response.data, function (index, item) {
                            const option = $("<option></option>")
                                .val(item.id)       // Set the value attribute
                                .text(item.name);   // Set the display text
                            $select.append(option);
                        });
                    }
                }
            });
        }

        function getEditSubCategory(id,sub_category_id){
            $.ajax({
                type:"post",
                dataType:"json",
                data:{id:id,"_token": "{{ csrf_token() }}"},
                url:"{{route('pos.get_sub_category')}}",
                success:function(response){
                    if (response.status) {
                        $("#sub_category_id").html('');
                        $("#sub_category_id").append(`<option value="">Select sub category...</option>`);
                        const $select = $("#sub_category_id");
                        $.each(response.data, function (index, item) {
                            const option = $("<option></option>")
                                .val(item.id)       // Set the value attribute
                                .text(item.name);   // Set the display text
                            $select.append(option);
                        });

                        setTimeout(function () {
                            $("#sub_category_id").val(sub_category_id).trigger('change');
                             
                        },1000);
                    }
                }
            });
        }

        $("body").on("click",".add_new_record",function (e) {
            $("#id").val(0);
            $("#ProductName").val('');
            $("#PurchasePrice").val('');
            $("#SalePrice").val('');
            $("#low_limit").val('');
            $("#BarCode").val('');
            $("#allow_percentage").val('');
            $("#main_category_id").val('');
            $("#sub_category_id").val('').trigger("change");

            $("#item_form_id").val('').trigger('change');;
            $("#item_make_id").val('').trigger('change');;
            $("#generic_name_id").val('').trigger('change');
            $("#pack_price").val('');
            $("#pack_size").val('');
            $("#add_new_record_model").modal("show");

        });

        setTimeout(function () {
            $("#generic_name_id").select2({dropdownParent: $('.modal')});
            $("#sub_category_id").select2({dropdownParent: $('.modal')});
            $("#item_form_id").select2({dropdownParent: $('.modal')});
            $("#item_make_id").select2({dropdownParent: $('.modal')});

        },400);

        $("body").on("click",".edit_record",function (e) {
            record_id = $(this).attr("data-id");
            var details = JSON.parse($(this).attr("data-details"));
           // console.log(details);
            $("#id").val(record_id);
            getEditSubCategory(details.main_category_id,details.sub_category_id);
            $("#ProductName").val(details.ProductName);
            $("#PurchasePrice").val(details.PurchasePrice);
            $("#SalePrice").val(details.SalePrice);
            $("#low_limit").val(details.low_limit);
            $("#BarCode").val(details.BarCode);
            $("#allow_percentage").val(details.allow_percentage || 0);
            $("#main_category_id").val(details.main_category_id);

            $("#item_form_id").val(details.item_form_id).trigger('change');;
            $("#item_make_id").val(details.item_make_id).trigger('change');;
            $("#generic_name_id").val(details.generic_name_id).trigger('change');
            $("#pack_price").val(details.pack_price);
            $("#pack_size").val(details.pack_size);
            $("#add_new_record_model").modal("show");
        });

        $("body").on("click","#submit_btn",function (e) {
            e.preventDefault();

            $("#from_submit").ajaxSubmit({
                url: "{{ route('pos.save_product') }}",
                type: 'post',
                data: {
                    _token: '{{ csrf_token() }}'

                },
                success: function(response){
                    $("#add_new_record_model").modal("hide");
                    user_table.ajax.reload();
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    //console.log();
                    //alert("Status: " + textStatus); alert("Error: " + errorThrown);
                    alert(JSON.parse(XMLHttpRequest.responseText).message);
                }
            });
        });

        $(document).ready(function (){

            district_id = "";
            police_station_id = [];
            leave_request_id = '';

            user_table = $('#users-list').DataTable({
                processing: true,
                serverSide: true,

                lengthMenu: [
                    [ 20, 100, 250, 500, 1000 ],
                    [ '20', '100', '250', '500', '1000']
                ],
                pageLength: 20,
                ajax: {
                    url: '{{route("pos.list_product")}}',
                    data: function (d) {
                        d.user_id = $('#attendance_user_filter').val();
                        d.attendance_date_from = $('#attendance_date_from').val();
                        d.attendance_date_to = $('#attendance_date_to').val();


                    }

                },

                columns: [

                    {data: 'ProductName', name: 'ProductName',searchable: true},

                    {
                        data: null,
                        name: 'generic_name.name',
                        searchable: true,
                        render: function(data, type, row) {
                            if (row.generic_name) {
                                return row.generic_name.name;
                            }else{
                                return ''
                            }


                        }
                    },

                        {
                        data: null,
                        name: 'item_form.name',
                        searchable: true,
                        render: function(data, type, row) {
                            if (row.item_form) {
                                return row.item_form.name;
                            }else{
                                return ''
                            }


                        }
                    },
                    {
                        data: null,
                        name: 'item_make.name',
                        searchable: true,
                        render: function(data, type, row) {
                            if (row.item_make) {
                                return row.item_make.name;
                            }else{
                                return ''
                            }


                        }
                    },

                    {data: 'pack_price', name: 'pack_price',searchable: true},
                    {data: 'pack_size', name: 'pack_size',searchable: true},
                    {data: 'PurchasePrice', name: 'PurchasePrice',searchable: true},
                    {data: 'SalePrice', name: 'SalePrice',searchable: true},
                    {data: 'low_limit', name: 'low_limit',searchable: true},
                    {data: 'AvailableQuantity', name: 'AvailableQuantity',searchable: true},
                    {
                        data: 'allow_percentage',
                        name: 'allow_percentage',
                        searchable: true,
                        render: function(data, type, row) {
                            return (data || 0) + '%';
                        }
                    },
                    {data: 'BarCode', name: 'BarCode',searchable: true},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],

                responsive: true,
                processing: true,
                serverSide: true,
                searching:  true,
                sorting:    true,
                paging:     true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });

            $('#attendance_user_filter, #attendance_date_from, #attendance_date_to').on('change', function(e) {
                e.preventDefault();
                user_table.ajax.reload();
            });

            $("body").on("click",".delete_record",function (e) {
                var id  = $(this).attr("data-id");
                if (confirm('Are you sure to delete this record ?')) {
                    $.ajax({
                        type: 'post',
                        url: "{{ route('pos.delete_IsActive') }}",
                        data: {
                            id: id,
                            table:"products",
                            _token: '{{ csrf_token() }}'

                        },
                        success: function(res) {
                            user_table.ajax.reload();
                           // window.location.reload();
                        }
                    })
                } else {
                    alert('Why did you press cancel? You should have confirmed');
                }
            });
        });






    </script>
@endpush