<!DOCTYPE html>
<html>
<head>
    <title>Import CSV File</title>
</head>
<body>

<h2>Upload CSV File</h2>

@if ($errors->any())
    <div style="color: red;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('pos.import') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="csv_file" required>
    <button type="submit">Import</button>
</form>

@if (!empty($data))

    <div>
        @if(count($not_found) > 0)
            <p style="color: red;">Following Refrence Number Not Found in Hospital Record. Please cross check it with Hospital Sehat Card Dashboard.</p>
        @else
            <p style="color:green; font-size: 18px; font-weight: bold;">Sehat Card Record matched with hospital Record. Please Click on Save button to update hospital hmis.</p>

            <form action="{{ route('pos.saveSehatCardPayment') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <table>
                    <tr>
                        <td>Claim No</td>
                        <td>
                            <input type="hidden" name="claim_and_amount" value="{{json_encode($claim_and_amount)}}">
                            <input type="number" name="claim_no">
                        </td>
                        <td>Total Amount</td>
                        <td><input type="number" value="{{$net_amount}}" name="received_amount"></td>
                        <td>Date</td>
                        <td><input type="date" name="receiving_date"></td>
                        <td></td>
                        <td><button type="submit">Save</button></td>
                    </tr>


                </table>

            </form>
        @endif
        @foreach($not_found as $key => $value)
            <span style="background-color: red; color:white; padding: 0px; border-radius: 3px; margin: 1px;">&nbsp;&nbsp;{{$value}}&nbsp;&nbsp;</span>
        @endforeach



    </div>
    <br>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
        <tr>
            <th>S.No</th>
            @foreach(array_keys($data[0]) as $key)
                <th>{{ str_replace(' ', '', $key) }}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @foreach($data as $key => $row)
            @if(!empty(trim($row['Visitno'])))
                <tr style="color: {{ in_array($row['Visitno'], $not_found) ? 'red' : 'green' }}">
                    <td>{{$key + 1}}</td>
                    @foreach($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>

    @else
    <h3 style="color: red">Note: Csv Column Name Formate. it will process date on baseis of given Column Name</h3>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
        <tr>
            <th>SNo</th>
            <th>Visitno</th>
            <th>HospitalMr</th>
            <th>PatientName</th>
            <th>AdmissionDate</th>
            <th>DischargeDate</th>
            <th>Treatments</th>
            <th>DeductionRemarks</th>
            <th>AmountPaid</th>
            <th>TotalDeduction</th>
        </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
@endif

</body>
</html>
