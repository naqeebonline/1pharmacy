<!doctype html>
<head>
	<meta charset="utf-8">
	<title>POS</title>
	<link rel="stylesheet" href="{{asset('assets/css/print_style.css')}}">
</head>
<style>
    h6{
        margin: 3px 0;
        padding: 2px 0;
    }


</style>

 <script>
       /* window.onload = function () {
            window.print(); // Open the print dialog when the page finishes loading
            setTimeout(function() {
                window.print(); // Second print after a short delay
            }, 1000);
        };*/
    </script>
<body>

	<div class="wrap">
		
		<div class="logo">
			<h2 style="font-size: 22px">{{env('COMPANY_NAME')}}</h2>
			<small style="font-size: 13px">{{date("d-m-Y h:i A")}} </small>
		</div>	

 


	<div class="main">
         
        <h6 style="font-size: 14px">Printed By: {{ auth()->user()->name ?? "" }}</h6>
		   <table style="margin-top: 5px">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th style="width: 5%;">Invoice#</th>
                        <th style="width: 65%; ">Patient</th>
                        <th style="width: 10%;">Amount</th>
                        <th style="width: 10%;">User</th>
                        
                    </tr>
                </thead>
                <tbody>
                <?php $i=1; $total = 0; foreach($data as $key => $value){
                    $total = ($total) + ($value->received_amount);
                    
                    ?>
                    <tr>
                        <td style="font-weight: bold !important; font-size: 14px"><?php echo $i; $i++;;?></td>
                        <td style="font-weight: bold !important; font-size: 14px">{{$value->InvoiceNo}}</td>
                        <td style="font-weight: bold !important; font-size: 14px">{{$value->patient->name}} - {{$value->SaleID}}</td>
                        <td style="font-weight: bold !important; font-size: 14px">{{$value->received_amount}}</td>
                        <td style="font-weight: bold !important; font-size: 14px">
                             {{$value->created_by_user->name ?? ""}}
                        </td>
                        
                    </tr>
                <?php } ?>
                 <tr>
                    <td colspan="5" style="font-weight: bold; font-size:16px;">Total: {{$total}}</td>
                 </tr>
                </tbody>

            </table>

                 
	</div>
        
	</div>



</body>
</html>