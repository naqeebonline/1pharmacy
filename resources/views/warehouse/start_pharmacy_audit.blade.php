<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

        body {
            font-family: "Poppins", serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
        }

        strong,
        p {
            font-size: 10px;
            margin: 0;
            font-weight: 600;
            line-height: 1.2;
        }

        table {
            border-collapse: collapse;
        }

        table th,
        td {
            padding: 0 3px;
            border: 1px solid #000;
            font-size: 10px;
        }

        table th {
            background-color: #B2BBC3;
            font-weight: 600;
            padding: 3px;
        }

        .text-blue{
            color: rgb(0, 151, 211);
        }

        .text-red {
            color: rgb(231, 12, 12);
        }
        body{
            padding: 10px 20px;
            background-color: #fff;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0px auto;
            /* padding: 10px 20px; */
            background: #fff;
            /* border: 1px solid #ddd; */
            border-radius: 5px;
        }

        header {
            display: flex;
            justify-content: space-between;
            padding: 0px 0px;
            padding-bottom: 0px;
            width: 100%;

        }

        .company-details {
            width: 50%;
        }

        .header-right{
            width: 50%;
        }
        .header-right table{
            margin-left: auto;
        }

        .company-details h1 {
            margin: 0;
            font-size: 20px;
            color: #333;
            font-weight: 500;
            line-height: 1;
            margin-bottom: 3px;
        }

        .company-details p {
            margin: 0;
            font-weight: 600;
            text-transform: uppercase;
        }

        .logo img {
            max-width: 80px;
            margin-bottom: 10px;
        }

        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            margin-bottom: 10px;
        }
        .invoice-details>div{
            width: 50%;
        }


        .to-details h3 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
            line-height: 1;
            margin-bottom: 3px;
        }

        .to-details p {
            margin: 0;

        }


        .invoice-meta table tbody tr td:first-child {
            background-color: #B2BBC3;
            min-width: 120px;
            font-weight: 600;
        }


        .text-center {
            text-align: center !important;
        }

        .items table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .items table th,
        .items table td {
            text-align: left;
            font-size: 10px;
        }

        .items table th {}

        .items table td {
            padding: 2px 3px;

        }



        .totals {
            display: flex;
            justify-content: end;
        }

        .totals table strong {
            font-size: 10px !important;
        }

        .totals table td:first-child {
            background-color: #B2BBC3;
        }

        .totals table td:last-child {
            width: 110px;
            font-weight: 600;
        }

        .totals-row {
            margin: 5px 0;
        }

        .total-balance {
            font-size: 18px;
            color: #d00;
        }

        footer {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
            color: #666;
        }
        .select2-results__option:hover {
            background-color: green !important;
            color: white !important;
        }

        /* Change selected item background */
        .select2-results__option--highlighted {
            background-color: darkgreen !important;
            color: white !important;
        }

        .select2-container--default .select2-selection--single:hover {
            border-color: green !important;
        }
        .select2-container--default .select2-selection--single {
            border: 1px solid #ccc !important; /* Adjust color as needed */
            border-radius: 4px; /* Optional: Adjust for rounded corners */
            padding: 4px; /* Optional: Adjust spacing */
        }
    </style>
</head>

<body>
<div class="invoice-container">
    <header>
        <div class="company-details">
            <h1 style="text-align: center;margin-left: 50%;width: 100%;">Pharmacy Audit</h1>
            <a class="btn btn-primary" href="{{route('pos.pharmacy_audit')}}">Back</a>

        </div>

        <div class="invoice-meta header-right">


        </div>
    </header>



    <section class="items">
        @if($audit->status == 0)
        <table>
            <tr>
                <form method="post" action="{{ route('pos.add_product_to_audit') }}">
                    @csrf
                <td style="width: 50%">
                    <input type="hidden" name="audit_id" value="{{$audit_id}}">
                    <label style="font-weight: bold">Product Name</label>
                    <select id="product_name" class="form-select" name="product_id">
                        <option value="">Select Product</option>
                        @foreach($data as $key => $value)
                            <option value="{{$value->ProductID}}">{{$value->ProductName}} - {{$value->generic_name->name ?? ''}}</option>


                        @endforeach
                    </select>
                </td>
                <td style="width: 25%">
                    <label style="font-weight: bold">Physical Avaliable Qty</label>
                    <input type="number" style="height: 30px !important;" class="form-control" name="phy_avaliable_quantity" value="">
                </td>
                <td style="width: 25%">
                    <button type="submit" class="btn btn-success">Add Item</button>
                </td>
                </form>
            </tr>
        </table>
        @endif


        <table style="margin-top: 5px; width: 100% !important;">
            <thead>
            <tr>
                <th style="width: 5%;">S.N</th>
                <th style="width:20% !important;">Product Name</th>
                <th style="width:10%; ">Generic Name</th>
                <th style="width: 10%;">Avaliable Quantity</th>
                <th style="width: 10%;">Avaliable in Pharmacy</th>
                <th style="width: 5%;">Defficency</th>
                <th style="width: 10%;"></th>

            </tr>
            </thead>
            <tbody>
            @foreach($items as $key => $value)

                <tr>
                     @if($audit->status == 0)
                    <form method="post" action="{{ route('pos.update_audit') }}">
                        @csrf
                     @endif

                        <td >{{ $key + 1 }}</td>
                        <td>
                            {{$value->product->ProductName ?? ''}}
                        </td>
                        <td >{{$value->product->generic_name->name ?? ''}}</td>
                        <td >
                            <input type="hidden" name="id" value="{{$value->id}}">
                            <input type="hidden" name="product_id" value="{{$value->product_id}}">
                            <input type="hidden" name="audit_id" value="{{$value->audit_id}}">
                            {{$value->avaliable_quantity}}
                        </td>
                        <td ><input type="number" style="width: 70px" name="phy_avaliable_quantity" value="{{$value->phy_avaliable_quantity ?? 0}}"></td>
                        <td ><input style="width: 50px" type="number" disabled value="{{$value->phy_avaliable_quantity - $value->avaliable_quantity }}"></td>
                        <td  >
                            <button type="submit" style="display: none"    class="btn btn-primary">update</button>
                            @if($audit->status == 0)
                            <a class="btn btn-sm btn-danger" href="{{route('pos.delete_product_from_audit',[$value->id])}}">x</a>
                            @endif
                        </td>
                    @if($audit->status == 0)
                    </form>
                    @endif
                </tr>
            @endforeach

            </tbody>

        </table>
    </section>

    <script type="text/javascript">
        setTimeout(function () {
            $("#product_name").select2();
            $("#product_name").select2("open");
        },200);
    </script>


</div>
</body>

</html>