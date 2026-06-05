<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Interface</title>
    <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet">
    <script src="{{asset('assets/js/jquery-3.5.1.min.js')}}"></script>
    <script src="{{asset('assets/js/bootstrap.bundle.min.js')}}"></script>
    <link href="{{asset('assets/css/select2.min.css')}}" rel="stylesheet" />
    <script src="{{asset('assets/js/select2.min.js')}}"></script>
    <script src="{{asset('assets/js/ckeditor.js')}}"></script>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">

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
            height: calc(100% - 70px) !important; /* Adjust based on toolbar height */
        }

        #popu-message{
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
        .header-section input, .header-section select, .header-section label {
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

        @keyframes  anu{
            0%{
                background-color: #48AF5A42;
                transform: scale(1);
            }
            50%{
                background-color: #48AF5AFA;
                transform: scale(1.02);
            }
            100%{
                background-color: #48AF5A42;
                transform: scale(1);
            }
        }
        .no-photo h5{
            font-size: 1.2rem ;
            text-align: center;
            /*color: #fff;*/

        }
        .table_scroll {
            height: 385px;
            overflow-y: scroll;
            scroll-behavior: smooth;
        }
        .table_scroll::-webkit-scrollbar{
            display: none;
        }
        .table_scroll table{
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
        .table_scroll table td:nth-child(2){
            text-align: left !important;
        }
        .form-control,
        .form-select {
            padding: 5px 10px !important;
            border-radius: 0 !important;
            font-size: 0.9rem;
            box-shadow: none !important;
        }
        .tableHead{
            margin-bottom: 0 !important;
        }
        .tableHead tr th{
            padding: 3px !important;
            font-family: Verdana;
            font-size: 14px;

        }
        .ward_request_table_class{
            background: #fff;
            height: 250px;
            overflow: auto
        }
        .previous-bills{
            background: #fff;
            height: 150px;
            overflow: auto
        }
        .previous-bills table tr th,
        .previous-bills table tr td{
            padding: 3px;
            font-size: 14px
        }
        .previous-bills table tr td:first-child{
            width: 90px;
        }
        .previous-bills table tr td:last-child{
            width: 85px;
        }
        .previous-bills table tr td a{
            font-size: 12px;
            padding: 3px
        }

        .horizontal-menu {
            list-style-type: none; /* Remove bullets */
            padding: 0;
            margin: 0;
            display: flex; /* Display items in a row */
        }

        .horizontal-menu li {
            background-color: #b52255;
            color: white;
            padding: 2px 13px;
            margin-right: 10px;
            border-radius: 20px;
            text-align: center;
        }

    </style>
</head>
<body>
<div id="popu-message">Error Occur</div>
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row header-section">

        <div class="col-md-2" style="display: none">
            <label for="invoice_number">Barcode</label>
            <input type="text"  id="barcode" class="form-control" placeholder="Barcode">
        </div>
        <div class="col-md-1">
            <label for="invoice_number">Invoice Number</label>
            <input type="text" id="invoice_number" style="pointer-events: none;" required="required" value="{{$invoiceNo ?? ''}}" class="form-control" value="" >
        </div>

        <div class="col-md-2">
            <label for="date">Medicine Type</label>
            <select class="form-select" id="medicine_type" >
                <option value="">Select Medicine Type...</option>
                <option value="Ward" {{($type == "Ward") ? "selected" : ""}}>Ward Medicine</option>
                <option value="OT" {{($type == "OT") ? "selected" : ""}}>OT Medicine</option>
                <option value="Home" {{($type == "Home") ? "selected" : ""}}>Home Medicine</option>
            </select>
        </div>

        <div class="col-md-3">
            <label for="received">Select Patient</label>
            <select id="SID" name="SID" class="form-control">
                <option value="">Please Select Patient...</option>
                <?php foreach($admitted_patients as $key => $value){ ?>
                <option data-admission_id="{{$value->id}}" value="<?php echo $value->patient_id; ?>"><?php echo $value->patient->name." - ".$value->patient->mr_no; ?> Status:({{$value->admission_status}}) </option>
                <?php } ?>
            </select>
        </div>



        <div class="col-md-2">
            <label for="date">Date</label>
            <input type="date" id="bill_date" class="form-control" value="<?php echo date("Y-m-d") ?>">
        </div>


        <div class="col-md-2">
            <label for="balance">Description</label>
            <input type="text" id="previous_balance" class="form-control" value="" style="font-weight: bold; pointer-events: none" >
        </div>

    </div>

    <!-- Item Input Section -->
    <div class="row mt-2">
        <div class="col-md-9">
            <div class="row">
                <!--<div class="col-md-3">
                    <input type="text" class="form-control" placeholder="Item/Bar Code">
                </div>-->
               @if($type !='')
                        <div class="col-md-5 mt-1">
                            <select class="form-control" id="product_id">
                                <option value="">Select Product...</option>
                                <?php foreach ($products as $key => $value): ?>
                                   {{-- @if($value->avaliable_qty > 0)--}}
                                @if($value->ProductName != '' && $value->ProductName != '-')
                                    <option value="{{$value->ProductID}}" data-purchasePrice={{$value->SalePrice}} data-taxPercentage="{{$value->taxPercentage}}">{{$value->ProductName}} | PS: {{$value->pack_size}} | Qty {{$value->avaliable_qty}}</option>
                                    {{--@endif--}}
                                @endif
                                <?php endforeach; ?>
                            </select>
                            {{--<input type="text" name="product_name" id="product_name" class="form-control" placeholder="Product Name">--}}
                        </div>


                        @if($type == "Home" || $type == "Ward")
                            <div class="col-md-3 mt-1">

                                <select class="form-select" id="dose_type" >
                                    <option value="">Select Dose Type...</option>
                                    <option value="-">-</option>
                                    <option value="TDS">TDS (صبح ,دوپہر ,شام )</option>
                                    <option value="BD">BD  (صبح ,شام )</option>
                                    <option value="OD">OD  (صبح )</option>

                                    <option value="HS" >HS (رات کو)</option>
                                    <option value="QID">QID (ہر 6 گھنٹے بعد)</option>
                                </select>
                            </div>
                        @endif


                        <div class="col-md-2 mt-1">
                            <input type="number"  id="SalePrice" class="form-control" placeholder="Unit Price">
                        </div>

                        <div class="col-md-2 mt-1">
                            <input type="number" class="form-control" id="sale_quantity" placeholder="Quantity" value="1">
                        </div>







                        <div class="col-md-2 mt-1" style="display: none">
                            <input type="text" disabled class="form-control" id="avaliable_qty" placeholder="Avaliable Quantity" readonly>
                        </div>
               @endif
            </div>

            <!-- Items List Section -->
            <ul class="horizontal-menu"></ul>

            <table class="table table-bordered mt-2 tableHead">
                <thead class="table-header">
                <tr>
                    <th style="width: 5%">#</th>
                    <th style="width: 20%">Product Name</th>
                    <th style="width: 10%">Quantity</th>
                    <th style="width: 10%">Rate</th>
                    <th style="width: 10%">Amount</th>
                    <th style="width: 10%">Dose</th>
                    <th style="">Available Unit Qty</th>

                    <th style="width: 10%">Actions</th>
                </tr>
                </thead>
            </table>
            <div class="table_scroll">
                <table class="table table-bordered table-striped" >
                    <tbody id="product_table">









                    </tbody>
                </table>
            </div>

            <!-- Footer Section -->
            <div class="footer-section">
                <div class="row">

                    <div class="col-md-2">
                        <label for="remarks">Received Amount</label>
                        <input type="number" id="ReceivedAmount" value="0" class="form-control">
                    </div>

                    <div class="col-md-8">
                        <label for="remarks">Bill Description</label>
                        <input type="text" name="BillDiscription" id="BillDiscription" class="form-control">
                    </div>


                    <div class="col-md-2">
                        <label for="remarks">Bill Amount</label>
                        <input type="number" readonly style="font-weight: bold; font-size: 14px;" id="BillAmount" class="form-control">
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <button class="btn btn-custom me-2" id="save_bill">Save Bill</button>
                    <a class="btn btn-custom me-2" target="_blank" >New Bill</a>
                    <a class="btn btn-custom me-2 logout_btn" style="float: right; background-color:red" href="javascript:void(0)">Logout</a>

                </div>
            </div>

        </div>

        <!-- Sidebar Section -->
        <div class="col-md-3 sidebar">
            <!--<div class="no-photo">
                <h5><?php /*echo Company_Name; */?></h5>
            </div>-->
            {{--<table class="table table-bordered">
                <tr>
                    <td width="50%">Previous Balance</td>
                    <td><span id="prv_balance" style="font-weight: bold; font-size: 14px"></span></td>
                </tr>
                <tr>
                    <td>Current Bill</td>
                    <td><span id="current_bill" style="font-weight: bold; font-size: 14px"></span></td>
                </tr>

                <tr>
                    <td>Total</td>
                    <td><span id="total_bill" style="font-weight: bold; font-size: 14px"></span></td>
                </tr>
            </table>--}}
                <h5 style="text-align: center; color:green">{{session('store_name')}}</h5>
                <hr>

                <h5 style="text-align: center; color:red">Ward Request</h5>
            <div class="ward_request_table_class">

                <table class="table table-bordered" style="width: 100%" id="ward_request_table">
                    <thead>
                    <tr>
                        <th style="display: none">Patient</th>
                        <th>Inv.No</th>
                        <th>Patient</th>
                        <th>Req By</th>

                        <th style="width: 10%">Action</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>

                    
                </table>

            </div>
                <h5 style="text-align: center; color:red">Previous Bills</h5>
            <div class="previous-bills">

                <table class="table table-bordered" style="width: 100%" id="previous-bill-table">
                    <thead>
                        <tr>
                            <th>Invioce #</th>
                            <th>Patient</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>


                </table>

            </div>
           
        </div>
    </div>

    <!-- Action Buttons Section -->

</div>
<script src="{{ asset('assets/vendor/libs/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-responsive/datatables.responsive.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.js') }}"></script>

    <script src="{{ asset('assets/js/jquery.form.min.js') }}"></script>

<script>
    $("body").on("click",".view_ward_request",function () {
       var id = $(this).attr('data_ward_request_id');
       var url = "{{route('pos.add_new_sale')}}?type=Ward&ward_request="+id;
       window.location = url;
    });
     previous_bill_table = $('#previous-bill-table').DataTable({
                processing: true,
                serverSide: true,

                lengthMenu: [
                    [100, 250, 500, 1000],
                    ['100', '250', '500', '1000']
                ],
                pageLength: 50,
                ajax: {
                    url: `{{ route('pos.previous_bills') }}`,
                    
                },

                columns: [

                    {
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
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });

     ward_request_table = $('#ward_request_table').DataTable({
         processing: true,
         serverSide: true,

         pageLength: 20,
         ajax: {
             url: "{{ route('pos.get_list_ward_request_in_pharmacy') }}",
             data: function (d) {
                 d.from_date = $('#filter_from_date').val();
                 d.to_date = $('#filter_to_date').val();
                 d.opd_type_id = $('#filter_opd_type_id').val();
                 d.consultant_id = $('#filter_consultant_id').val();

             }
         },

         columns: [
             {
                 data: 'patient.name',
                 visible: false
             },
             {
                 data: 'invoice_no',
                 name: 'invoice_no',
                 searchable: true
             },
             {
                 data: null, // Use null because you're creating a custom render
                 name: 'patient',
                 searchable: false, // If you want it searchable, additional backend support is required
                 render: function (data, type, row) {
                     // Combine first_name and last_name to display full_name
                     return '<b style="font-size:12px;color:green;">'+row.patient.name+'</b>';
                 }
             },


             {
                 data: 'user.name',
                 name: 'user.name',
                 searchable: true
             },
             /*{
                 data: 'requested_at',
                 name: 'requested_at',
                 searchable: true
             },*/





             {
                 data: 'actions',
                 name: 'actions',
                 orderable: false,
                 searchable: false
             }
         ],


         responsive: true,
         processing: true,
         serverSide: true,
         searching: true,
         sorting: true,
         paging: true,
         dom: 'Bfrtip',
         // buttons: [
         //     'copy', 'csv', 'excel', 'pdf', 'print'
         // ]
     });

    setInterval(() => {
        ward_request_table.ajax.reload();
        //alert();
    }, 30000);
</script>
<script type="text/javascript">
    var preValue = '';
    var selectedRow = "";
    var ProductList = [];
    var PreviousBalance = 0;
    var taxPercentage = 0;
    var currentAvailableQuantity = 0;
    var patient_admission_id = 0;
    ward_request_id = 0;

    @if(count($list_products) > 0)
          ProductList = @json($list_products);
    @endif
    reload_table();

    setTimeout(function () {
        @if($patient_id !='')
            ward_request_id = "{{$ward_request}}";
          $("#SID").val("{{$patient_id}}").trigger("change");
        @endif

    },500);

    $(document).on('click', '.logout_btn', function(e) {
        e.preventDefault();

        // Show confirm alert
        if (confirm("Are you sure you want to log out?")) {
            // Redirect to logout URL if "Yes" is clicked
            window.location.href = "";
        }
    });



    $(document).ready(function(){
        $("#product_id").select2();
        $("#SID").select2();




       // $("#product_id").select2();

        $("body").on("change","#medicine_type",function () {
            var value = $(this).val();
            window.location = "{{route('pos.add_new_sale')}}?type="+value;
        });
        $("body").on("change","#SID",function () {
            patient_admission_id= $('#SID option:selected').attr('data-admission_id');
            get_prev_balance();
        });

        $("body").on("change","#barcode",function () {

            var values=$(this).val();
            $(this).val('');

            var barcode_number=values.trim();
            $.ajax({
                type:"post",
                dataType:"json",
                data:{barcode:barcode_number,"_token": "{{ csrf_token() }}"},
                url:"{{route('pos.get_items_by_barcode')}}",
                success:function(response){
                    if(response!=false){
                        $.each(response.data,function(key,value){

                            ProductID=value.ProductID;
                            Product=value.ProductName;
                            UnitePrice=value.SalePrice;
                            Name=value.Specification_name;
                            AvailableQuantity=value.AvailableQuantity;
                            taxPercentage=value.taxPercentage;


                        });
                        if(AvailableQuantity <= 0){
                            alert("Item is out of stock");
                            return false;
                        }else{
                            setTimeout(function () {
                                var dose_type = '';
                                add_item_to_grid(ProductID,Product,UnitePrice,Name,AvailableQuantity,1,taxPercentage,dose_type);
                                //clearForm();
                                $("#barcode").focus();
                            },300);

                        }

                    }else{
                        popupMsg("Item is Not Registered in Inventory","error");
                        $("#PID").data('kendoDropDownList').value('');
                        return;
                    }
                }
            });
        });
        // Make table cells editable on click
        $("body").on("click",".editable",function () {



            var $cell = $(this);
            selectedRow = $(this);
            var currentValue = $cell.text();
            if(currentValue == ''){
                currentValue = preValue;
            }else{
                preValue = currentValue;
            }
            $cell.html('<input type="number" class="form-control editable-input" value="' + currentValue + '">');
            $cell.find(".editable-input").focus();

        });

        // Save the edited value on blur (when input loses focus)
        $(document).on("blur", ".editable-input", function(){

            var $input = $(this);
            var newValue = $input.val();
            $input.parent().text(newValue);
            if(newValue == '' || newValue == null){
                return false;
            }
            var quantity = selectedRow.closest("tr").find("td:eq(2)").text();
            var rate = selectedRow.closest("tr").find("td:eq(3)").text();
            var avaliable_qty = selectedRow.closest("tr").find("td:eq(6)").text();


            if(parseInt(quantity) > parseInt(avaliable_qty)){
                selectedRow.closest("tr").find("td:eq(2)").text(preValue);
                quantity = preValue;
                popupMsg("Exceeding Available Quantity. You can't change the value.","error");

            }


            if(quantity == '' || rate == '' || quantity == null || rate == null){
                return false;
            }
            var total = parseFloat(quantity) * parseFloat(rate);

            selectedRow.closest("tr").find("td:eq(4)").text(total);
            var product_id = selectedRow.closest("tr").find("td:eq(0)").attr("data-id");
            updateProductByID(product_id,quantity,rate,total);


        });

        $(document).on("change", "#product_id", function(){
            var purchasePrice = $('#product_id option:selected').attr('data-purchasePrice');
            taxPercentage = $('#product_id option:selected').attr('data-taxPercentage');
            $("#SalePrice").val(purchasePrice);
            getItemDetails();
        });

        $("body").on("blur","#sale_quantity",function(){
            saveItemToBill();
        });

        $("body").on("click",".remove_item",function(){
            removeProductByID($(this).attr("data-id"));

        });


        $("body").on("click","#save_bill",function(){
            var count_error_items = $('.horizontal-menu li').length;

            if(count_error_items > 0){
                popupMsg("Please Add grn of pending item of KIT or Skip low quantity Items","error");
                return false;
            }

            SID = $("#SID").val();
            company_name = $("#company_name").val();
            invoice_number = $("#invoice_number").val();
            medicine_type = $("#medicine_type").val();
            currency_type = $("#currency_type").val();
            bill_date = $("#bill_date").val();
            customer_name = $("#customer_name").val();
            previous_balance = $("#previous_balance").val();
            ReceivedAmount = $("#ReceivedAmount").val();
            BillDiscription = $("#BillDiscription").val();
            BillAmount = $("#BillAmount").val();
            bill_address = '';
            $("#save_bill").hide();
            if(SID == ''){
                popupMsg("Please Select Customer","error");
                $("#SID").focus();
                $("#SID").select2('open');
                $("#save_bill").show();
                return false;
            }

            if(medicine_type == ''){
                popupMsg("Please Select Medicine Type","error");
                $("#medicine_type").focus();
                $("#save_bill").show();
                return false;
            }
            if(invoice_number == ''){
                popupMsg("enter invoice number","error");

                $("#invoice_number").focus();
                $("#save_bill").show();
                return false;
            }
            if(company_name == ''){
                popupMsg("Enter Company Name","error");
                $("#company_name").focus();
                $("#save_bill").show();
                return false;
            }
            if(currency_type == ''){
                popupMsg("Select Currency Type","error");
                $("#currency_type").focus();
                $("#currency_type").trigger('click');
                $("#save_bill").show();
                return false;
            }
            if(bill_date == ''){
                popupMsg("Enter Bill Date","error");
                $("#bill_date").focus();
                $("#save_bill").show();
                return false;
            }

            if(ProductList.length <= 0){
                popupMsg("Please Add Items to Bill","error");
                $("#save_bill").show();
                return false;
            }



           var patient_id = SID;
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data:{
                    SID,
                    patient_id,
                    ward_request_id,
                    patient_admission_id,
                    company_name,
                    invoice_number,
                    medicine_type,
                    currency_type,
                    bill_date,
                    customer_name,
                    previous_balance,
                    bill_address,
                    ReceivedAmount,
                    BillDiscription,
                    BillAmount,
                    ProductList,
                    "_token": "{{ csrf_token() }}"
                },
                url: "{{ route("pos.save_sale") }}",
                success:function(response){
                    $("#save_bill").show();
                    sale_id_for_print=response.id;

                    window.location.reload();



                    url="{{route('pos.print_thermel_purchase_details')}}/"+sale_id_for_print;
                    window.open(url, '_blank');

                    window.location="{{route('pos.add_new_sale')}}";


                }
            });
            //console.log(CKEDITOR.getData());

        });



        $("body").on("click",".print_bill",function(){

            SID = $("#SID").val();
            company_name = $("#company_name").val();
            invoice_number = $("#invoice_number").val();
            currency_type = $("#currency_type").val();
            bill_date = $("#bill_date").val();
            customer_name = $("#customer_name").val();
            previous_balance = $("#previous_balance").val();
            ReceivedAmount = $("#ReceivedAmount").val();
            BillDiscription = $("#BillDiscription").val();
            BillAmount = $("#BillAmount").val();
            bill_address = '';
            $("#save_bill").hide();
            if(SID == ''){
                popupMsg("Please Select Customer","error");
                $("#SID").focus();
                $("#SID").select2('open');
                $("#save_bill").show();
                return false;
            }
            if(invoice_number == ''){
                popupMsg("enter invoice number","error");
                $("#invoice_number").focus();
                $("#save_bill").show();
                return false;
            }
            if(company_name == ''){
                popupMsg("Enter Company Name","error");
                $("#company_name").focus();
                $("#save_bill").show();
                return false;
            }
            if(currency_type == ''){
                popupMsg("Select Currency Type","error");
                $("#currency_type").focus();
                $("#currency_type").trigger('click');
                $("#save_bill").show();
                return false;
            }
            if(bill_date == ''){
                popupMsg("Enter Bill Date","error");
                $("#bill_date").focus();
                $("#save_bill").show();
                return false;
            }

            if(ProductList.length <= 0){
                popupMsg("Please Add Items to Bill","error");
                $("#save_bill").show();
                return false;
            }




            $.ajax({
                type: 'POST',
                dataType: 'json',
                data:{
                    SID,
                    company_name,
                    invoice_number,
                    currency_type,
                    bill_date,
                    customer_name,
                    previous_balance,
                    bill_address,
                    ReceivedAmount,
                    BillDiscription,
                    BillAmount,
                    ProductList,
                    "_token": "{{ csrf_token() }}"
                },
                url: "{{ route("pos.temp_save_sale") }}",
                success:function(response){
                    $("#save_bill").show();
                    sale_id_for_print=response.id;


                   // window.location.reload();
                    var date="{{ date("Y-m-d") }}";
                    url="{{route('pos.print_temp_sale')}}/1/"+SID+"/"+date+"/"+ReceivedAmount;
                    window.open(url, 'Direct Bill', 'width=1200,height=600,scrollbars=yes');



                    //window.location.reload();



                }
            });
            //console.log(CKEDITOR.getData());

        });
    });

    function removeProductByID(productID) {
        ProductList = ProductList.filter(product => product.ProductID != productID);
        reload_table();
    }

    function updateProductByID(product_id,quantity,rate,total) {

        let product = ProductList.find(product => product.ProductID == product_id);
        if (product) {

            product.Quantity = quantity;
            product.UnitePrice = rate;
            product.Total = total;

            reload_table();
        } else {
            reload_table();
           // console.log(`Product with ID ${productID} not found.`);
        }
    }

    $("body").on("click",".remove_low_qty_item",function () {
       var id = $(this).attr("data-item_id");
       $(`#${id}`).remove();
    });
    function getItemDetails(){
        currentAvailableQuantity = 0;
        var p_id = $("#product_id").val();
        if(p_id == ''){
            return false;
        }
        $.ajax({
            type:"post",
            dataType:"json",
            data:{p_id:p_id,_token: '{{ csrf_token() }}'},
            url:"{{route('pos.get_items_by_product_id')}}",
            success:function(response){

                if(response.status == true){
                    $.each(response.data,function(key,value){
                        if(value.is_product_kit){
                            if(value.AvailableQuantity < value.qty){
                                $(".horizontal-menu").append(`<li id='product_id_${value.product.ProductID}'>${value.product.ProductName} (Qty: ${value.AvailableQuantity}) <span data-item_id="product_id_${value.product.ProductID}" class="btn btn-warning remove_low_qty_item">x</span></li>`);
                            }
                            add_item_to_grid(value.product.ProductID,value.product.ProductName,value.product.SalePrice,value.product.name,value.AvailableQuantity,value.qty,'');
                        }else{

                            currentAvailableQuantity=value.AvailableQuantity ? value.AvailableQuantity : 0;
                            if(currentAvailableQuantity > 0){
                                $(`#product_id_${value.ProductID}`).remove();

                            }
                        }

                    });

                }else{
                    popupMsg("Item is Not Registered in Inventory","error");
                    return;
                }
            }
        });

    }

    function saveItemToBill() {
        var medicine_type = "{{$type}}";
        var dose_type = '';
        if(medicine_type == 'Home' || medicine_type == "Ward"){
            dose_type = $("#dose_type").val();
            if(dose_type == ''){
                popupMsg("Please Select Dose Type. ","error");
                return false;
            }
        }

        ProductID= $('#product_id').val();
        Product=$('#product_id option:selected').text();

        Name=$('#product_id option:selected').text();
        AvailableQuantity= currentAvailableQuantity;

        var quantity=$("#sale_quantity").val();
        UnitePrice=$("#SalePrice").val();

        if(ProductID == '' || Name == '' || quantity == '' || UnitePrice == ''){
            popupMsg("Please Fill All required fields.. ","error");
            return false;
        }

        add_item_to_grid(ProductID,Product,UnitePrice,Name,AvailableQuantity,quantity,dose_type);
        clearForm();
        return true;


    }

    function clearForm() {
        $("#product_name").val('');


        $("#sale_quantity").val(1);
        $("#SalePrice").val('');

        $("#product_id").val(null).trigger('change');
        $("#product_id").focus();
        $("#product_id").select2('open');
        $("#dose_type").val('');
        taxPercentage = 0;
        currentAvailableQuantity = 0;

    }

    function get_prev_balance(e){

        var value=$("#SID").val();
        var name=$('#SID').select2('data')[0]['text'];
        var serverBaseUrl = "";
        $("#page_title").text(name);
        if(value!=''){
            $.ajax({
                type:"get",
                url:"{{route("pos.customer_previous_balance")}}/"+value,
                success:function(response){

                    PreviousBalance = parseFloat(response).toFixed(2);
                    $("#previous_balance").val(PreviousBalance);
                    calculateBalance();

                }
            });
        }else{
            $("#prev_balance").val(0);
        }
    }

    function calculateBalance() {
        $("#prv_balance").text(PreviousBalance);
        $("#current_bill").text($("#BillAmount").val());
        var total_bill = (parseFloat($("#BillAmount").val()) + parseFloat(PreviousBalance)).toFixed(2);
        $("#total_bill").text(total_bill);
    }

    function add_item_to_grid(ProductID,Product,UnitPrice,Name,AvailableQuantity,quantity='',dose_type=''){

        if(AvailableQuantity == 0){
            popupMsg(Product+" Is not Available in Stock","error");
            return false;
        }
        if(quantity > AvailableQuantity){
            popupMsg("You are Exceeding Available Quantity.","error");
            return false;
        }

         Quantity = 1;
        if(quantity!=''){
            Quantity=quantity;
        }
        let exists = ProductList.some(item => item.ProductID === ProductID);

        if (!exists) {
            Quantity = 1;
            if(quantity!=''){
                Quantity=quantity;
            }
            var totalAmount = (Quantity * UnitPrice).toFixed(2);
            var taxRate = taxPercentage / 100; // 12% as a decimal
            var taxAmount = (totalAmount * taxRate).toFixed(2);

            var data_array = {
                ProductID: ProductID,
                Product  : Product,
                Name  : Product,
                UnitePrice : UnitPrice,
                Quantity : Quantity,
                Total    : Quantity * UnitPrice,
                AvailableQuantity:AvailableQuantity,
                taxAmount:taxAmount,
                taxPercentage:taxPercentage,
                currentAvailableQuantity:currentAvailableQuantity,
                dose_type:dose_type,

            };
            ProductList.push(data_array);
            reload_table();
        } else {
            popupMsg("Select Product already exist in list","error");
            return false;
        }


    }

    function reload_table() {
        $("#product_table").html('');
        var total_amount = 0;
        ProductList.forEach((value,key) => {
            var html = `<tr>
                        <td style="width: 5%" data-id="${value.ProductID}">${key+1}</td>
                        <td style="width: 20%">${value.Name}</td>

                        <td style="width: 10%" class="editable" data-field="quantity">${value.Quantity}</td>
                        <td style="width: 10%" class="editable" data-field="rate">${value.UnitePrice}</td>
                        <td style="width: 10%">${value.Total}</td>
                        <td style="width: 10%">${value.dose_type}</td>
                        <td style="">${value.AvailableQuantity}</td>

                        <td style="width: 10%">
                            <a class="btn btn-sm btn-danger remove_item" data-id="${value.ProductID}">x<a/>
                        </td>


                    </tr>`;
            total_amount = parseFloat(total_amount) + parseFloat(value.Total) + parseFloat(value.taxAmount);
            $("#product_table").prepend(html);
            setTimeout(function () {
                calculateBalance();

            },1000);

        });
        $("#BillAmount").val(total_amount);
        if(ProductList.length < 15){
            var length = (15)-(ProductList.length);
            var i=1;
            for(i=1; i<=length; i++){
        var html = `<tr>
                        <td style="width: 5%" >&nbsp;</td>
                        <td style="width: 20%"></td>

                        <td style="width: 10%" class="editable" data-field="quantity"></td>
                        <td style="width: 10%" class="editable" data-field="rate"></td>
                        <td style="width: 10%"></td>
                        <td style="width: 10%"></td>
                        <td style=""></td>

                        <td style="width: 10%">

                        </td>


                    </tr>`;
                     $("#product_table").append(html);
            }
        }

    }


    function popupMsg(msg,msgtype){
        var color = '#dd1144';
        if(msgtype.toLowerCase() == 'success'){
            var color = '#00CC00';
        }

        $("#popu-message").css('background-color',color).html(msg).slideDown().delay(2000).slideUp();

    }
</script>

</body>
</html>
