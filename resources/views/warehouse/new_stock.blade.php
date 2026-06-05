<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Interface</title>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <script src="{{ asset('assets/js/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/ckeditor.js') }}"></script>
    <style>
        /* Custom styles based on the design */
        * {
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #48AF5A42;
        }

        .ckeditor-full-height .cke_inner {
            height: 100% !important;
        }

        .ckeditor-full-height .cke_contents {
            height: calc(100% - 70px) !important;
            /* Adjust based on toolbar height */
        }

        #popu-message {
            width: 100%;
            padding-top: 20px;
            padding-bottom: 20px;
            text-align: center;
            margin-top: 0px;
            margin-left: 0px;
            font-weight: bold;
            font-size: 14px;
            position: fixed;
            display: none;
            color: white;
            z-index: 100000;

        }

        .header-section input,
        .header-section select,
        .header-section label {
            color: black;
        }

        .header-section {
            background-color: #48af5a;
            padding: 5px 5px 10px 5px;
        }

        .header-section label {
            font-weight: bold;
            font-size: 0.8rem !important;
        }

        .table-header {
            background-color: #9f1c20;
            color: white;
            font-weight: bold;
            text-align: center;
        }

        .table {
            background-color: white;
            border: 2px solid #0b0b0b;
        }

        .footer-section {
            background-color: #48af5a;
            color: white;
            font-weight: bold;
            padding: 10px;
        }

        .btn-custom {
            background-color: #48af5a;
            color: white;
            border: none;
        }

        .btn-custom:hover {
            background-color: #3a9c4a;
        }

        .sidebar {
            background-color: #e7f2f7;
            border: 2px solid #48af5a;
            border-radius: 5px;
            padding: 10px;
        }

        .sidebar button {
            width: 45%;
            margin-bottom: 10px;
        }

        .no-photo {
            width: 100%;
            height: 90px;
            border-radius: 5px;
            margin-bottom: 10px;
            padding: 20px;
            box-sizing: border-box;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
            background-color: #48AF5A42;
            animation: anu 2s infinite;

        }

        @keyframes anu {
            0% {
                background-color: #48AF5A42;
                transform: scale(1);
            }

            50% {
                background-color: #48AF5AFA;
                transform: scale(1.02);
            }

            100% {
                background-color: #48AF5A42;
                transform: scale(1);
            }
        }

        .no-photo h5 {
            font-size: 1.2rem;
            text-align: center;
            /*color: #fff;*/

        }

        .table_scroll {
            height: 385px;
            overflow-y: scroll;
            scroll-behavior: smooth;
        }

        .table_scroll::-webkit-scrollbar {
            display: none;
        }

        .table_scroll table {
            border-top: 0 !important;
        }

        .table_scroll table td {
            padding: 3px !important;
            font-family: Verdana;
            font-weight: 500;
            height: 20px;
            font-size: 14px;
            text-align: center;
        }

        .table_scroll table td:nth-child(2) {
            text-align: left !important;
        }

        .form-control,
        .form-select {
            padding: 5px 10px !important;
            border-radius: 0 !important;
            font-size: 0.9rem;
            box-shadow: none !important;
        }

        .tableHead {
            margin-bottom: 0 !important;
        }

        .tableHead tr th {
            padding: 3px !important;
            font-family: Verdana;
            font-size: 14px;

        }
    </style>
</head>

<body>
    <div id="popu-message">Error Occur</div>
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="row header-section">

            {{--<div class="col-md-2">
                <label for="invoice_number">Barcode</label>
                <input type="text" id="barcode" class="form-control" placeholder="Barcode">
            </div>--}}
            <div class="col-md-2">
                <label for="invoice_number">Store Name</label>
                <select id="store_id" name="store_id" class="form-control">
                    <option value="">Please Store...</option>
                    <?php foreach($store as $key => $value){ ?>
                    <option value="<?php echo $value->id; ?>" {{ (session('store_id') == $value->id) ? "selected" : ''}}><?php echo $value->store_name; ?> </option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-md-2">
                <label for="invoice_number">Invoice Number</label>
                <input type="text" id="invoice_number" required="required" class="form-control" value="">
            </div>

            <div class="col-md-2">
                <label for="received">Select Supplier</label>
                <select id="SID" name="SID" class="form-control">
                    <option value="">Please Select Supplier...</option>
                    <?php foreach($suppliers as $key => $value){ ?>
                    <option value="<?php echo $value->SCID; ?>"><?php echo $value->Name; ?> </option>
                    <?php } ?>
                </select>
            </div>




            <div class="col-md-2">
                <label for="balance">Previous Balance</label>
                <input type="text" id="previous_balance" style="font-weight: bold; pointer-events: none";
                    class="form-control" value="-">
            </div>



            <div class="col-md-2">
                <label for="date">Discount On Bill</label>
                <input type="number" id="bill_discount" class="form-control" value="0">
            </div>

            <div class="col-md-2">
                <label for="date">Date</label>
                <input type="date" id="bill_date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
            </div>

        </div>

        <!-- Item Input Section -->
        <div class="row mt-2">
            <div class="col-md-10">




                <div class="row mt-3">
                    <div class="col-md-12">
                        <button class="btn btn-custom me-2" id="save_bill">Generate Bill</button>
                        <a class="btn btn-custom me-2" href="{{route('pos.grn_request')}}" >Cancel Bill</a>
                        <a class="btn btn-custom me-2 logout_btn" style="float: right; background-color:red"
                            href="javascript:void(0)">Logout</a>

                    </div>
                </div>

            </div>


        </div>

        <!-- Action Buttons Section -->

    </div>

    {{-- data-packsize="{{$value->pack_size}}" data-pack_price={{$value->pack_price}} data-purchasePrice={{$value->PurchasePrice}} data-taxPercentage="{{$value->taxPercentage}}" --}}
    <script>
        let validLengths = [1,3, 7,11,13];
        $('#product_id').select2({
            placeholder: 'Search for an item',
            minimumInputLength: 2, // Start searching after typing 2 characters
            ajax: {
                url: '{{ route('pos.get_products') }}', // Your server-side route
                dataType: 'json',
                delay: 250, // Delay for debounce
                data: function(params) {
                     
                    return {
                        q: params.term, // Search term
                        page: params.page || 1, // Pagination
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.items.map(item => ({
                            id: item.ProductID,
                            text: item.ProductName,
                            data_packsize:item.pack_size,
                            data_pack_price:item.pack_price,
                            data_purchasePrice:item.PurchasePrice,
                            data_taxPercentage:item.taxPercentage

                        })),
                        pagination: {
                            more: data.more, // Indicates if more data is available
                        },
                    };
                },
                cache: true,
            },
        });

        $("#store_id").select2();
    </script>
    <script type="text/javascript">
        var preValue = '';
        var selectedRow = "";
        var ProductList = [];
        var PreviousBalance = 0;
        var taxPercentage = 0;
        var pack_size = 0;
        unit_price = 0;
        reload_table();

        $(document).on('click', '.logout_btn', function(e) {
            e.preventDefault();

            // Show confirm alert
            if (confirm("Are you sure you want to log out?")) {
                // Redirect to logout URL if "Yes" is clicked
                window.location.href = "";
            }
        });


        $("body").on("click", ".add_stock", function() {
            saveItemToBill();
        });

        function saveItemToBill() {

            ProductID = $('#product_id').val();
            Product = $('#product_id option:selected').text();

            Name = $('#product_id option:selected').text();
            batch_no = $("#batch_no").val();
            expiry_date = $("#expiry_date").val();
            advance_tax = $("#advance_tax").val();
            gst_tax = $("#gst_tax").val();
            AvailableQuantity = 0;

            var quantity = $("#sale_quantity").val();
            var pack_price = $("#SalePrice").val();


            if (ProductID == '' || Name == '' || quantity == '' || pack_price == '' || batch_no == '' || expiry_date ==
                '' || advance_tax == '' || gst_tax == '') {
                popupMsg("Please Fill All required fields.. ", "error");
                return false;
            }

            add_item_to_grid(ProductID, Product, pack_price, Name, AvailableQuantity, quantity, batch_no, expiry_date,
                advance_tax, gst_tax);
            clearForm();
            return true;


        }

        function add_item_to_grid(ProductID, Product, pack_price, Name, AvailableQuantity, quantity = '', batch_no,
            expiry_date, advance_tax, gst_tax) {

            /*if(AvailableQuantity==0){
                popupMsg(Product+" Is no Avaliable in Stock","error");
                return false;
            }*/
            Quantity = 1;
            if (quantity != '') {
                Quantity = quantity;
            }
            let exists = ProductList.some(item => item.ProductID === ProductID);

            if (!exists) {
                Quantity = 1;
                if (quantity != '') {
                    Quantity = quantity;
                }
                var totalAmount = (Quantity * pack_price).toFixed(2);
                var gst_formula = gst_tax / 100; // 12% as a decimal
                var advance_tax_formula = advance_tax / 100; // 12% as a decimal
                var taxAmount = 0;
                var gst_tax_amount = (totalAmount * gst_formula).toFixed(2);
                var advance_tax_amount = (totalAmount * advance_tax_formula).toFixed(2);
                var item_total_price = Math.round(Quantity * pack_price);

                var net_amount = parseFloat(totalAmount) + parseFloat(gst_tax_amount) + parseFloat(advance_tax_amount);

                var data_array = {
                    ProductID: ProductID,
                    Product: Product,
                    Name: Product,
                    pack_price: pack_price,
                    unit_price: unit_price,
                    Quantity: Quantity,
                    Total: item_total_price,
                    AvailableQuantity: AvailableQuantity,
                    taxAmount: taxAmount,
                    taxPercentage: 0,
                    gst_tax_amount: gst_tax_amount,
                    advance_tax_amount: advance_tax_amount,
                    batch_no: batch_no,
                    expiry_date: expiry_date,
                    advance_tax: advance_tax,
                    gst_tax: gst_tax,
                    net_amount: net_amount,
                    pack_size: pack_size

                };
                ProductList.push(data_array);
                reload_table();
            } else {
                popupMsg("Select Product already exist in list", "error");
                return false;
            }


        }

        $(document).ready(function() {

           // $("#product_id").select2();
            $("#SID").select2();




            /*let CKEDITOR;

            ClassicEditor
                .create(document.querySelector('#bill_address'))
                .then(editor => {
                    CKEDITOR = editor; // Store the editor instance in CKEDITOR variable
                })
                .catch(error => {
                    console.error(error);
                });*/


            // $("#product_id").select2();

            $("body").on("change", "#SID", function() {

                get_prev_balance();
            });
            // Make table cells editable on click
            $("body").on("click", ".editable", function() {



                var $cell = $(this);
                selectedRow = $(this);
                var currentValue = $cell.text();
                if (currentValue == '') {
                    currentValue = preValue;
                } else {
                    preValue = currentValue;
                }
                $cell.html('<input type="number" class="form-control editable-input" value="' +
                    currentValue + '">');
                $cell.find(".editable-input").focus();

            });

            // Save the edited value on blur (when input loses focus)
            $(document).on("blur", ".editable-input", function() {
                var $input = $(this);
                var newValue = $input.val();
                $input.parent().text(newValue);
                if (newValue == '' || newValue == null) {
                    return false;
                }
                var quantity = selectedRow.closest("tr").find("td:eq(2)").text();

                var rate = selectedRow.closest("tr").find("td:eq(3)").text();
                if (quantity == '' || rate == '' || quantity == null || rate == null) {
                    return false;
                }
                var total = parseFloat(quantity) * parseFloat(rate);

                selectedRow.closest("tr").find("td:eq(4)").text(total);
                var product_id = selectedRow.closest("tr").find("td:eq(0)").attr("data-id");


                var advance_tax = selectedRow.closest("tr").find("td:eq(7)").text(); // 12% as a decimal
                var gst_tax = selectedRow.closest("tr").find("td:eq(8)").text(); // 12% as a decimal
                var advance_tax_formula = advance_tax / 100; // 12% as a decimal
                var gst_formula = gst_tax / 100; // 12% as a decimal
                var taxAmount = 0;

                var gst_tax_amount = (total * gst_formula).toFixed(2);
                var advance_tax_amount = (total * advance_tax_formula).toFixed(2);

                var net_amount = parseFloat(total) + parseFloat(advance_tax_amount) + parseFloat(
                    gst_tax_amount);

                updateProductByID(product_id, quantity, rate, total, advance_tax_amount, gst_tax_amount,
                    net_amount);


            });

            $(document).on("change", "#product_id", function() {
                unit_price = $('#product_id option:selected').attr('data-purchasePrice');
                pack_price = $('#product_id option:selected').attr('data-pack_price');
                taxPercentage = $('#product_id option:selected').attr('data-taxPercentage');
                pack_size = $('#product_id option:selected').attr('data-packsize');
                if(pack_size == 0 || pack_price == 0){
                    alert("Please define pack price and pack size from product configuration");
                    $("#product_id").val('').trigger("change");
                    return false;
                }


                $("#SalePrice").val(pack_price);
                // getItemDetails();
            });

            $("body").on("blur", "#sale_quantitys", function() {
                saveItemToBill();
            });



            $("body").on("click", ".remove_item", function() {
                removeProductByID($(this).attr("data-id"));

            });

            $("body").on("click", ".print_bill", function() {

                SID = $("#SID").val();
                company_name = $("#company_name").val();
                invoice_number = $("#invoice_number").val();
                currency_type = $("#currency_type").val();
                bill_date = $("#bill_date").val();
                bill_discount = $("#bill_discount").val();
                customer_name = $("#customer_name").val();
                previous_balance = $("#previous_balance").val();
                ReceivedAmount = $("#ReceivedAmount").val();
                BillDiscription = $("#BillDiscription").val();
                BillAmount = $("#BillAmount").val();
                bill_address = '';
                if (SID == '') {
                    popupMsg("Please Select Customer", "error");
                    $("#SID").focus();
                    $("#SID").select2('open');
                    return false;
                }
                if (invoice_number == '') {
                    popupMsg("enter invoice number", "error");
                    $("#invoice_number").focus();
                    return false;
                }
                if (company_name == '') {
                    popupMsg("Enter Company Name", "error");
                    $("#company_name").focus();
                    return false;
                }
                if (currency_type == '') {
                    popupMsg("Select Currency Type", "error");
                    $("#currency_type").focus();
                    $("#currency_type").trigger('click');
                    return false;
                }
                if (bill_discount == '') {
                    bill_discount = 0;
                }
                if (bill_date == '') {
                    popupMsg("Enter Bill Date", "error");
                    $("#bill_date").focus();
                    return false;
                }
                if (ProductList.length <= 0) {
                    popupMsg("Please Add Items to Bill", "error");
                    return false;
                }


                return false;
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        SID,
                        company_name,
                        invoice_number,
                        currency_type,
                        bill_date,
                        bill_discount,
                        customer_name,
                        previous_balance,
                        bill_address,
                        ReceivedAmount,
                        BillDiscription,
                        BillAmount,
                        ProductList
                    },
                    url: 'Tempsalecontroller/AddNewSale',
                    success: function(response) {
                        var customer_id = SID;
                        var date = "<?php echo date('Y-m-d'); ?>";
                        var received_amount = $("#ReceivedAmount").val();
                        if (received_amount == '') {
                            received_amount = 0;
                        }
                        url = '';
                        window.open(url, 'Direct Bill', 'width=1200,height=600,scrollbars=yes');

                        /*url="<?php //echo base_url().'Tempsalecontroller/print_purchase_detail/1';
                        ?>";
                        window.open(url, 'Print Details', 'width=1200,height=600,scrollbars=yes');*/

                    }
                });
                //console.log(CKEDITOR.getData());

            });


            $("body").on("click", "#save_bill", function() {

                SID = $("#SID").val();
                company_name = $("#company_name").val();
                invoice_number = $("#invoice_number").val();
                store_id = $("#store_id").val();
                currency_type = $("#currency_type").val();
                bill_date = $("#bill_date").val();
                bill_discount = $("#bill_discount").val();
                customer_name = $("#customer_name").val();
                previous_balance = $("#previous_balance").val();
                ReceivedAmount = $("#ReceivedAmount").val();
                BillDiscription = $("#BillDiscription").val();
                BillAmount = $("#BillAmount").val();
                total_gst = $("#total_gst").val();
                total_advance_tax = $("#total_advance_tax").val();
                bill_address = '';
                if(bill_discount == ''){
                    bill_discount = 0;
                }
                $("#save_bill").hide();
                if (store_id == '') {
                    popupMsg("Please Select Store", "error");
                    $("#store_id").focus();
                    $("#store_id").select2('open');
                    $("#save_bill").show();
                    return false;
                } if (SID == '') {
                    popupMsg("Please Select Customer", "error");
                    $("#SID").focus();
                    $("#SID").select2('open');
                    $("#save_bill").show();
                    return false;
                }
                if (invoice_number == '') {
                    popupMsg("enter invoice number", "error");
                    $("#invoice_number").focus();
                    $("#save_bill").show();
                    return false;
                }
                if (company_name == '') {
                    popupMsg("Enter Company Name", "error");
                    $("#company_name").focus();
                    $("#save_bill").show();
                    return false;
                }
                if (currency_type == '') {
                    popupMsg("Select Currency Type", "error");
                    $("#currency_type").focus();
                    $("#currency_type").trigger('click');
                    $("#save_bill").show();
                    return false;
                }
                if (bill_date == '') {
                    popupMsg("Enter Bill Date", "error");
                    $("#bill_date").focus();
                    $("#save_bill").show();
                    return false;
                }

              /*  if (ProductList.length <= 0) {
                    popupMsg("Please Add Items to Bill", "error");
                    $("#save_bill").show();
                    return false;
                }*/




                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        SID,
                        company_name,
                        invoice_number,
                        store_id,
                        currency_type,
                        bill_date,
                        bill_discount,
                        customer_name,
                        previous_balance,
                        bill_address,
                        ReceivedAmount,
                        BillDiscription,
                        BillAmount,
                        ProductList,
                        total_gst,
                        total_advance_tax,

                        "_token": "{{ csrf_token() }}"
                    },
                    url: "{{ route('pos.save_stock') }}",
                    success: function(response) {
                        $("#save_bill").show();
                        bill_id = response.id;
                        window.location="{{route('pos.add_bill_items')}}/"+bill_id;

                    }
                });
                //console.log(CKEDITOR.getData());

            });
        });

        function removeProductByID(productID) {
            ProductList = ProductList.filter(product => product.ProductID != productID);
            reload_table();
        }

        function updateProductByID(product_id, quantity, rate, total, advance_tax_amount, gst_tax_amount, net_amount) {

            let product = ProductList.find(product => product.ProductID === product_id);

            if (product) {
                product.Quantity = quantity;
                product.UnitePrice = rate;
                product.Total = total;
                product.advance_tax_amount = advance_tax_amount;
                product.gst_tax_amount = gst_tax_amount;
                product.net_amount = net_amount;

                reload_table();
            } else {
                // console.log(`Product with ID ${productID} not found.`);
            }
        }


        function getItemDetails() {

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
                url: "{{ route('pos.getProduct') }}",
                success: function(response) {

                    if (response != 'false') {
                        $.each(response, function(key, value) {
                            // $("#avaliable_qty").val("Available Quantity: "+value.AvailableQuantity);
                            //$("#SalePrice").val("Sale Price: "+value.SalePrice);
                            //currentAvailableQuantity=value.AvailableQuantity ? value.AvailableQuantity : 0;
                        });

                    } else {
                        popupMsg("Item is Not Registered in Inventory", "error");

                        return;
                    }
                }
            });

        }



        function clearForm() {
            $("#product_name").val('');


            $("#sale_quantity").val(1);
            $("#SalePrice").val('');

            $("#product_id").val(null).trigger('change');
            $("#product_id").focus();
            $("#product_id").select2('open');
            taxPercentage = 0;
            pack_size = 0;

        }

        function get_prev_balance(e) {
            var value = $("#SID").val();
            var name = $('#SID').select2('data')[0]['text'];
            var serverBaseUrl = "";
            $("#page_title").text(name);
            if (value != '') {
                $.ajax({
                    type: "get",
                    url: "{{ route('pos.supplier_previous_balance') }}/" + value,
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
            $("#prv_balance").text(PreviousBalance);
            $("#current_bill").text($("#BillAmount").val());
            var total_bill = (parseFloat($("#BillAmount").val()) + parseFloat(PreviousBalance)).toFixed(2);
            $("#total_bill").text(total_bill);
        }



        function reload_table() {
            $("#product_table").html('');
            var total_amount = 0;
            var total_advance_tax = 0;
            var total_gst = 0;
            var net_amount = 0;
            ProductList.forEach((value, key) => {

                var html = `<tr>
                        <td style="width: 5%" data-id="${value.ProductID}">${key+1}</td>
                        <td style="width: 20%">${value.Name}</td>

                        <td style="width: 10%" class="editable" data-field="quantity">${value.Quantity}</td>
                        <td style="width: 10%" class="editable" data-field="rate">${value.pack_price}</td>
                        <td style="width: 10%">${value.gst_tax_amount}</td>
                        <td style="width: 10%">${value.advance_tax_amount}</td>
                        <td style="width: 10%">${value.Total}</td>
                        <td style="display: none">${value.advance_tax}</td>
                        <td style="display: none">${value.gst_tax}</td>
                        <td style="width: 10%">${value.net_amount}</td>

                        <td style="width: 10%">
                            <a class="btn btn-sm btn-danger remove_item" data-id="${value.ProductID}">x<a/>
                        </td>


                    </tr>`;
                total_advance_tax = parseFloat(total_advance_tax) + parseFloat(value.advance_tax_amount);
                total_gst = parseFloat(total_gst) + parseFloat(value.gst_tax_amount);
                total_amount = parseFloat(total_amount) + parseFloat(value.Total) + parseFloat(value
                    .advance_tax_amount) + parseFloat(value.gst_tax_amount);
                $("#product_table").prepend(html);



            });
            //total_amount = (total_amount) + (total_gst) + (total_advance_tax);
            $("#BillAmount").val(total_amount.toFixed(2));
            $("#total_advance_tax").val(total_advance_tax.toFixed(2));
            $("#total_gst").val(total_gst.toFixed(2));

            $("#current_bill_gst").text(total_gst.toFixed(2));
            $("#current_bill_advtax").text(total_advance_tax.toFixed(2));
            setTimeout(function() {
                calculateBalance();
            }, 1000);
            if (ProductList.length < 15) {
                var length = (15) - (ProductList.length);
                var i = 1;
                for (i = 1; i <= length; i++) {
                    var html = `<tr>
                        <td style="width: 5%" >&nbsp;</td>
                        <td style="width: 20%"></td>

                        <td style="width: 10%" class="editable" data-field="quantity"></td>
                        <td style="width: 10%" class="editable" data-field="rate"></td>
                        <td style="width: 10%"></td>
                        <td style="width: 10%"></td>
                        <td style="width: 10%"></td>
                        <td style="display: none"></td>
                        <td style="display: none"></td>
                        <td style="width: 10%"></td>

                        <td style="width: 10%">

                        </td>


                    </tr>`;
                    $("#product_table").append(html);
                }
            }

        }


        function popupMsg(msg, msgtype) {
            var color = '#dd1144';
            if (msgtype.toLowerCase() == 'success') {
                var color = '#00CC00';
            }

            $("#popu-message").css('background-color', color).html(msg).slideDown().delay(2000).slideUp();

        }
    </script>

</body>

</html>
