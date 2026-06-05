<!doctype html>

<head>
	<meta charset="utf-8">
	<title>POS</title>
	<link rel="stylesheet" href="{{asset('assets/css/print_style.css')}}">
</head>

<body>

	<div class="wrap">

		<div class="logo">
			<h2 style="font-size: 22px">{{config('app.COMPANY_NAME')}}</h2>
			<p style="font-size: 13px; font-weight:bold;">{{ config('app.Branch_Name') }}</p>
			<small style="font-size: 13px">{{date("d-m-Y h:i A")}} </small>
		</div>

		<div class="customer">
			<table>
				<tr>
					<td style="font-weight: bold;font-size: 13px">MR#:</td>
					<th style="text-align: left;font-size: 13px"> {{$data[0]->patient->mr_no ?? ""}}</th>
				</tr>
				<tr>
					<td style="font-weight: bold;font-size: 13px">Invoice</td>
					<th style="text-align: left;font-size: 13px"> {{$data[0]->invoice_no ?? ""}}</th>
				</tr>
				@if($consultant_name !='')
				<tr>
					<td style="font-weight: bold;font-size: 13px">Referred By:</td>
					<th style="text-align: left;font-size: 13px"> {{$consultant_name}}</th>
				</tr>
				@endif
				<tr>
					<td style="font-weight: bold;font-size: 13px">Patient Name:</td>
					<th style="text-align: left;font-size: 13px"> {{$data[0]->patient->name ?? ""}}</th>
				</tr>

				<tr>
					<td style="font-weight: bold;font-size: 13px">Age/Gender:</td>
					<th style="text-align: left;font-size: 13px"> {{$data[0]->patient->age ?? ""}}/{{$data[0]->patient->gender ?? ""}}</th>
				</tr>

				<tr>
					<td style="font-weight: bold;font-size: 13px">Printed By:</td>
					<th style="text-align: left;font-size: 13px"> {{$data[0]->created_by_user?->name ?? ""}}</th>
				</tr>

			</table>
		</div>


		<div class="main">
			<table>
				<thead>
					<tr>
						<th style="width: 20%; text-align: left">#</th>
						<th class="left" style="font-size: 13px">Test Name</th>
						<th class="left" style="font-size: 13px">Amount</th>

					</tr>
				</thead>
				<tbody>
					@foreach($data as $key => $value)
					<tr>
						<td style="width: 20%; text-align: left;font-size: 13px">{{$key + 1}}</td>
						<td class="left" style="font-weight: bold;font-size: 11px">{{$value->investigation->name ?? ""}}</td>
						<td class="left" style="font-weight: bold;font-size: 11px">{{$value->sale_price ?? ""}}</td>
					</tr>
					@endforeach
					<tr style="">
						<td style="width: 20%; text-align: left;font-size: 13px; border-top: 2px solid black"></td>
						<td class="left" style="font-weight: bold;font-size: 13px;border-top: 2px solid black">Total:</td>
						<td class="left" style="font-weight: bold;font-size: 13px;border-top: 2px solid black">{{$total}}</td>
					</tr>
					<tr>
						<td style="width: 20%; text-align: left;font-size: 13px"></td>
						<td class="left" style="font-weight: bold;font-size: 13px">Discount:</td>
						<td class="left" style="font-weight: bold;font-size: 13px">Rs:{{round($discount_amount)}} ({{$discount_percentage}} %)</td>
					</tr>
					<tr>
						<td style="width: 20%; text-align: left;font-size: 13px"></td>
						<td class="left" style="font-weight: bold;font-size: 13px">Net Amount:</td>
						<td class="left" style="font-weight: bold;font-size: 13px">{{round($total - $discount_amount)}}</td>
					</tr>



			</table>

			<p style="text-align: center"><img src="data:image/png;base64,{{ DNS2D::getBarcodePNG(config('app.LIVE_URL')."lab-results/".$data[0]->invoice_no, 'QRCODE', 3, 3) }}" alt="qrcode" /></p>
			<p style="text-align: center; font-weight: bold">Thank You For Visiting.</p>
		</div>

	</div>

	<script>
		// Automatically trigger print dialog when page loads
		window.onload = function() {
			window.print();
		};
	</script>

</body>

</html>