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
        <h6 style="font-size: 14px">Patient Name: {{ $patient->name ?? '' }} </h6>
        <h6 style="font-size: 14px">MR.No: {{ $patient->mr_no ?? '' }}</h6>

          <h6 style="font-size: 14px">Medicine Type: {{ $record[0]->medicine_type ?? "" }} Medicine</h6>
        <h6 style="font-size: 14px">Printed By: {{ auth()->user()->name ?? "" }}</h6>
		   <table style="margin-top: 5px">
                <thead>
                    <tr>
                        <th style="width: 5%;">S.No</th>
                        <th style="width: 65%; ">Description</th>
                        <th style="width: 10%;">Qty</th>
                        
                    </tr>
                </thead>
                <tbody>
                <?php $i=1; $taxAmount = 0; $totalAmount = 0; foreach($data as $d){
                    $taxAmount = ($taxAmount) + ($d->taxAmount);
                    $totalAmount = ($totalAmount) + ($d->totalAmount);
                    $quantity = ($d->Quantity) - ($d->ReturnQuantity);
                    ?>
                    <tr>
                        <td style="font-weight: bold !important; font-size: 14px"><?php echo $i; $i++;;?></td>
                        <td style="font-weight: bold !important; font-size: 14px"><?php echo $d->product->ProductName;?></td>
                        <td style="font-weight: bold !important; font-size: 14px">
                            <?php echo $quantity;?>
                            <?php if($d->ReturnQuantity > 0): ?>
                                Return: (<?php echo $d->ReturnQuantity ?>)
                            <?php endif; ?>
                        </td>
                        
                    </tr>
                <?php } ?>

                </tbody>

            </table>

                 
	</div>
        <br>
        <strong style="font-size: 12px; text-align: center !important;">Note: All Medicine are free of cost under Sehat Sahulat Program.</strong>
        <br>
	</div>



</body>
</html>