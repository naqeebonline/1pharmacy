<!DOCTYPE html>
<html lang="en" style="height: 11in;">
<head>
    <meta charset="UTF-8">
    <title>Patient Report</title>
</head>
<body style="font-family: Arial, sans-serif; font-size: 12px; color: #000; margin:0; padding:20px;width: 8.5in;margin:0 auto;height: 11in;">

<div style="max-width:800px; margin:0 auto;">

    <!-- Header -->
    <div style="display:flex; align-items:center; margin-bottom:10px;">
        <img src="{{asset('logo.png')}}" alt="logo" style="height:50px;">
        <span style="font-weight:bold; font-size:22px; text-decoration:underline; margin-left:80px;">
            {{ env('COMPANY_NAME') }}
        </span>
    </div>

    <!-- Patient / Invoice Info -->
    <div style="display:flex; justify-content:space-between; margin-bottom:15px;">
        <div style="width:48%;">

            <div style="display: flex;justify-content: start;align-items: center">
                <h4 style="width: 100px;margin:2px 0;">M.R #:</h4>
                <p style="margin:2px 0;">  {{ $result->patient->mr_no }}</p>
            </div>
            <div style="display: flex;justify-content: start;align-items: center">
                <h4 style="width: 100px;margin:2px 0;">Patient name:</h4>
                <p style="margin:2px 0;"> {{ $result->patient->name }}</p>
            </div>
            <div style="display: flex;justify-content: start;align-items: center">
                <h4 style="width: 100px;margin:2px 0;">Age | Gender:</h4>
                <p   style="margin:2px 0;"> {{ $result->patient->age }}-Year | {{ $result->patient->gender }}</p>
            </div>
            <div style="display: flex;justify-content: start;align-items: center">
                <h4 style="width: 100px;margin:2px 0;">Invoice Date:</h4>
                <p style="margin:2px 0;">{{ \Carbon\Carbon::now()->format('d-M-Y') }}</p>
            </div>

        </div>
        <div style="width:28%;">
            <div style="display: flex;justify-content: start;align-items: center">
                <h4 style="width: 100px;margin:2px 0;">Refered By:</h4>
                <p style="margin:2px 0;"> {{ $result->consultant->name ?? "-" }}</p>
            </div>
            <div style="display: flex;justify-content: start;align-items: center">
                <h4 style="width: 100px;margin:2px 0;">Receipt #:</h4>
                <p style="margin:2px 0;">{{ date('yh') }} - {{ date('is') }}</p>
            </div>
            <div style="display: flex;justify-content: start;align-items: center">
                <h4 style="width: 100px;margin:2px 0;">Patient Status:</h4>
                <p   style="margin:2px 0;"> OPD Patient</p>
            </div>
            <div style="display: flex;justify-content: start;align-items: center">
                <h4 style="width: 100px;margin:2px 0;">Result Date:</h4>
                <p style="margin:2px 0;"> {{ \Carbon\Carbon::parse($result->inv_out_date)->format('d-M-Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Test Section Heading -->
    <div style="text-align:center; margin:20px 0; ">
        <h4 style="margin:5px 0; font-style:italic; text-decoration:underline;border: 2px dashed #d5d5d5;padding: 5px;">
            {{ $result->subCategory->main_category->name }}
        </h4>
        <h4 style="margin:5px 0;">{{ $result->subCategory->name }}</h4>
    </div>

    <div style="min-height: 8in;">





    <!-- Test Results Table -->
    <table style="width:100%; border-collapse:collapse; margin-bottom:15px; font-size:13px;">
        <thead>
        <tr>
            <th style="border-bottom:1px solid #000;padding:4px; ">Test</th>
            <th style="border-bottom:1px solid #000;padding:4px; text-align:center;">Result</th>
            <th style="border-bottom:1px solid #000;padding:4px; text-align:center;">Unit</th>
            <th style="border-bottom:1px solid #000; padding:4px; text-align:center;">Reference Values</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($result->investigationResult as $item)
            <tr>
                <td style="border-bottom:1px solid #000; padding:4px;">{{ $item->parameter->name ?? '' }}</td>
                <td style="border-bottom:1px solid #000; padding:4px; text-align:center; font-weight:bold; {{ ($item->result_value > ($item->parameter->male_max ?? 999)) ? 'border-bottom:2px dashed #000;' : '' }}">
                    {{ $item->result_value }}
                </td>
                <td style="border-bottom:1px solid #000; padding:4px; text-align:center;">{{ $item->parameter->unit ?? '' }}</td>
                <td style="border-bottom:1px solid #000; padding:4px; text-align:center;">
                    {{ $item->parameter->male_min ?? '' }} - {{ $item->parameter->male_max ?? '' }}
                </td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <td colspan="4">
            <p style="font-size:12px; margin:10px 0;">
                <b>Disclaimer:</b> Every diagnostic test has scientific acceptable technology or technique-based limitations of uncertainty of measurement. False positive or false negative results may occur and do not fall under the domain of negligence.
            </p>
        </td>
        </tfoot>
    </table>
    </div>


    <div style="margin-top:40px; text-align:right;">
        <b style="text-decoration:underline;">Lab Technologist/Technician</b>
    </div>

    <!-- Disclaimer -->


    <div style="margin-top:20px; display:flex; justify-content:space-between; font-size:12px; margin-top: 10px;border-top: 1px solid #000;padding-top: 10px;">
        <div>
            <p style="margin:2px 0;"><b>Contact #:</b> 0938-481111 / 0316-8481111</p>
            <p style="margin:2px 0;"><b>Reported By:</b> Ahmad Johar</p>
        </div>
        <div>
            <p style="margin:2px 0;"><b>Address:</b> AMC, Opposite Waleed Filling Station, Gar Munara, District & Tehsil Swabi</p>
            <p style="margin:2px 0;"><b>Print Date:</b> {{ \Carbon\Carbon::now()->format('d-M-Y h:i A') }}</p>
        </div>
    </div>




    <!-- Footer -->


</div>
</body>
</html>


