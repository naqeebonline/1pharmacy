<?php

namespace App\Http\Controllers;

use App\Models\Patient\PatientAdmission;
use Illuminate\Http\Request;

class CsvImportController extends Controller
{
    public function showForm()
    {
        $total = PatientAdmission::whereNotNull('amount_received_from_sehat_card')
            //->where("is_active",1)
                ->groupBy("sc_ref_no")
            ->get();
        //dd($total->sum('amount_received_from_sehat_card'));
        return view('csv_import');
    }

    public function import(Request $request)
    {


        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240', // max 10MB
        ]);

        $data = [];

        if (($handle = fopen($request->file('csv_file')->getRealPath(), 'r')) !== false) {
            $header = fgetcsv($handle); // get the first row as header

            while (($row = fgetcsv($handle)) !== false) {
                $data[] = array_combine($header, $row);
            }

            fclose($handle);
        }
        $ids = [];
        $claim_and_amount = [];
        $totalBillAmount = 0;
        foreach ($data as $key => $value){
            $amountPaid = str_replace(',', '', ($value['AmountPaid']));
            $totalBillAmount = (int) ($totalBillAmount) + (int) ($amountPaid);
            if(!empty(trim($value['Visitno']))){
                array_push($ids,(int) trim($value['Visitno']));
                array_push(
                    $claim_and_amount,
                    ["sc_ref_no" => (int) trim($value['Visitno']),"amount"=>$amountPaid]
                );
            }

        }

        //------ make deduction according to sehat card  -------//
        $percentage = 9;
        $amount = $totalBillAmount;
        $deduction = ($percentage / 100) * $amount;
        $net_amount = ($amount) - ($deduction);

     //   dd($claim_and_amount);




        $system_ids = [];
        $res = PatientAdmission::leftJoin('patients',"patients.id",'=',"patient_admissions.patient_id")->whereIn('patient_admissions.sc_ref_no',$ids)->where("patient_admissions.is_active",1)->groupBy("patient_admissions.sc_ref_no")->get('sc_ref_no');
        foreach ($res as $key => $value){
            array_push($system_ids,(int) $value->sc_ref_no);
        }


        $not_found = [];
        foreach ($ids as $value) {
            if (!in_array($value, $system_ids)) {
                $not_found[] = $value;
            }
        }

        //print_r($new);
        //dd(count($system_ids));


        return view('csv_import', compact('data',"not_found","totalBillAmount","ids","system_ids","claim_and_amount","net_amount"));
    }

    public function saveSehatCardPayment()
    {
        $claims = json_decode(request()->claim_and_amount);
        foreach ($claims as $key => $value){
            $percentage = 9;
            $amount = $value->amount;
            $deduction = ($percentage / 100) * $amount;
            $net_amount = ($value->amount) - ($deduction);
           // echo $amount."----".$deduction."----". $net_amount;exit;

            PatientAdmission::where(["sc_ref_no" => $value->sc_ref_no])->update(["amount_received_from_sehat_card"=>$net_amount]);
        }

        return redirect()->route('pos.import_form');
    }
}
