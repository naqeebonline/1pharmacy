<!doctype html>
<head>
	<meta charset="utf-8">
	<title>POS</title>
	<link rel="stylesheet" href="{{asset('assets/css/print_style.css')}}">
</head>
<body>

	<div class="wrap">

		<div class="logo">
			<h2 style="font-size: 22px">{{env('COMPANY_NAME')}}</h2>
			<small style="font-size: 13px">{{date("d-m-Y h:i A")}} </small>
		</div>

	<div class="customer">
		<table>
			<tr>
				<td style="font-weight: bold;font-size: 13px">Invoice</td>
				<th style="text-align: left;font-size: 13px"> {{$data[0]->invoice_no ?? ""}}</th>
			</tr>
			<tr>
				<td style="font-weight: bold;font-size: 13px">Patient Name:</td>
				<th style="text-align: left;font-size: 13px"> {{$data[0]->patient->name ?? ""}}</th>
			</tr>

			<tr>
				<td style="font-weight: bold;font-size: 13px">Age/Gender:</td>
				<th style="text-align: left;font-size: 13px"> {{$data[0]->patient->age ?? ""}}/{{$data[0]->patient->gender ?? ""}}</th>
			</tr>

		</table>
	</div>


	<div class="main">
		<table>
			<thead>
				<tr>
					<th style="width: 20%; text-align: left">#</th>
					<th class="left" style="font-size: 13px">Test</th>

				</tr>
			</thead>
			<tbody>
			@foreach($data as $key => $value)
				<tr>
					<td style="width: 20%; text-align: left;font-size: 13px">{{$key + 1}}</td>
					<td class="left" style="font-weight: bold;font-size: 13px">{{$value->investigation->name ?? ""}}</td>
				</tr>
			@endforeach

			</tfoot>
		</table>
	</div>

	</div>

</body>
</html>