<?php

namespace App\Http\Controllers\PatientController;

use App\Http\Controllers\Controller;
use App\Models\Patient\InPatientAdmission;
use App\Models\Patient\PatientAdmission;
use App\Models\Patient\PatientInvestigation;
use App\Models\Patient\PatientServiceCharges;
use App\Models\SaleDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PatientExpenseController extends Controller
{

    public function totalCases($from_date='',$to_date="",$procedure_type_id='',$consultant_id='')
    {
        $admission = PatientAdmission::with('patient','procedure_type','consultant',"sub_consultant")
            ->where("admission_status","=","Discharged")
            ->when($from_date, function ($query) use($from_date) {

                $fromDate = Carbon::parse($from_date)->endOfDay();
                $query->whereDate('discharge_date','>=',$fromDate);
            })
            ->when($to_date, function ($query) use ($to_date) {
                $toDate = Carbon::parse($to_date)->endOfDay();
                $query->whereDate('discharge_date','<=',$toDate);
            })
            ->when($procedure_type_id, function ($query) use ($procedure_type_id) {
                $query->where('procedure_type_id',$procedure_type_id);
            })
            /*->when($consultant_id, function ($query) use ($consultant_id) {
                $query->where(function ($query) use($consultant_id) {
                    $query->where('consultant_id', $consultant_id)
                        ->orWhere('sub_consultant_id', $consultant_id);
                });
                //$query->where('consultant_id',$consultant_id);//->orWhere(['sub_consultant_id'=>$consultant_id]);
            })*/
            ->when($consultant_id, function ($query) use ($consultant_id)  {
                $query->where(['consultant_id'=> $consultant_id]);//->orWhere(['sub_consultant_id'=> request()->consultant_id]);
            })
           // ->limit(5)
            ->get();
        foreach ($admission as $key => $value){
            $value->getAdmissionDetails = $this->getAdmissionDetails1($value);
        }
        $data['data'] = $admission;

        return view("reports.all_patient_total_cost",$data);
    }

    public function totalCasesD($from_date='',$to_date="",$procedure_type_id='')
    {
        $admission = PatientAdmission::with('patient','procedure_type','consultant')
            ->where("admission_status","=","Discharged")
            ->when($from_date, function ($query) use($from_date) {

                $fromDate = Carbon::parse($from_date)->endOfDay();
                $query->whereDate('discharge_date','>=',$fromDate);
            })
            ->when($to_date, function ($query) use ($to_date) {
                $toDate = Carbon::parse($to_date)->endOfDay();
                $query->whereDate('discharge_date','<=',$toDate);
            })
            ->when($procedure_type_id, function ($query) use ($procedure_type_id) {
                $query->where('procedure_type_id',$procedure_type_id);
            })
            ->limit(5)
            ->get();

        $totalProcedureAmount = 0; $totalConsultantAmount = 0; $totalInvAmount =0;
        $totalMedicine_amount=0; $totalConsultant_share_amount=0;
        $grandTotalCost = 0;
        foreach ($admission as $key => $value){
            $value->getAdmissionDetails = $this->getAdmissionDetails($value->id);


            $totalProcedureAmount += ($value->getAdmissionDetails['procedure_amount']);
            $totalConsultantAmount += $value->getAdmissionDetails['consultant_share_amount'];
            $totalInvAmount += $value->getAdmissionDetails['investigation_amount'];
            $totalMedicine_amount += $value->getAdmissionDetails['medicine_amount'];

            $value->totalCost = $value->getAdmissionDetails['totalCost'];
            $value->balance = $value->getAdmissionDetails['balance'];
            $value->alert = $value->getAdmissionDetails['alert'];
            $value->procedure_amount = $value->getAdmissionDetails['procedure_amount'];



        }

        $data['data'] = $admission;

        return view("reports.all_patient_total_cost",$data);
    }


    public function calculatePatientMedecineAmount($admission_id)
    {
        $admission = PatientAdmission::with('patient','procedure_type','consultant')->where(["id"=>$admission_id])->first();
        $data['procedure_amount1'] = $admission->procedure_rate;
        $data['procedure_amount'] = $admission->procedure_rate;
        $data["is_medical_case"] = false;
        $data["daysDifference"] = 0;

        $data['consultant_share'] = $admission->consultant_share;
        if($admission->procedure_rate == 0){
            $data['procedure_amount1'] = $admission->procedure_type->net_rate;
            $data['procedure_amount'] = $admission->procedure_type->net_rate;
        }
        if($admission->consultant_share == 0){
            $data['consultant_share'] = $admission->consultant->share_percentage;
        }


        if($admission->procedure_type->type == "Medical"){
            $data["is_medical_case"] = true;
            $admissionDate = Carbon::parse($admission->admission_date);
            $dischargeDate = Carbon::parse($admission->discharge_date);
            if($admission->discharge_date == '' || $admission->discharge_date == NULL){

                $dischargeDate = Carbon::parse(date("Y-m-d")) ;
            }
            $daysDifference = $admissionDate->diffInDays($dischargeDate) + 1;
            $data['procedure_amount'] = ($data['procedure_amount']) * ($daysDifference);

            $data['daysDifference'] = $daysDifference;
        }



        //----- share percentange ---------//
        $share_amount = 0;
        if($data['consultant_share'] !='' && $data['consultant_share'] > 0){
            $percentage = $data['consultant_share']/100;
            $share_amount = ($data['procedure_amount']) * ($percentage);
        }



        $data['consultant_share_amount'] = $share_amount;

        $investigations = PatientInvestigation::with("subCategory")->where(["admission_id"=>$admission_id,"is_active"=>1])->get();
        $investigationAmount = 0;
        foreach ($investigations as $key => $value){
            $investigationAmount += $value->subCategory->price;
        }

        $data['investigation_amount'] = $investigationAmount;
        $data['service_charges'] = PatientServiceCharges::where(["admission_id"=>$admission_id,"is_active"=>1])->sum('service_rate');
        $patient_id = $admission->patient_id;
        $sale_details = SaleDetails::with("product", "sale")
            ->when($patient_id, function ($query) use ($patient_id) {
                $query->where('patient_id', $patient_id);
            })
            ->when($admission_id, function ($query) use ($admission_id) {
                $query->where('admission_id', $admission_id);
            })->get();


        $i=1; $taxAmount = 0; $totalAmount = 0;
        foreach($sale_details as $d) {
            // dd($d);
            $quantity = ($d->Quantity);
            $consumed = $quantity - $d->ReturnQuantity;
            $amount = ($consumed * $d->UnitePrice);
            $totalAmount = $totalAmount + $amount;
        }
        $data['medicine_amount'] =$totalAmount;
        $data['admission'] =$admission;
        return view("reports.patient_total_cost",$data);


    }


    public function getAdmissionDetails($admission_id)
    {
        $admission = PatientAdmission::with('patient','procedure_type','consultant')->where(["id"=>$admission_id])->first();
        $data['procedure_amount1'] = $admission->procedure_rate ?? '';
        $data['procedure_amount'] = $admission->procedure_rate ?? '';
        $data["is_medical_case"] = false;
        $data["daysDifference"] = 0;

        $data['consultant_share'] = $admission->consultant_share;
        if($admission->procedure_rate == 0){
            $data['procedure_amount1'] = $admission->procedure_type->net_rate;
            $data['procedure_amount'] = $admission->procedure_type->net_rate;
        }
        /*if($admission->consultant_share == 0){
            $data['consultant_share'] = $admission->consultant->share_percentage;
        }*/

        if($admission->procedure_type->type == "Medical"){
        //    dd($admission);
            $data["is_medical_case"] = true;
            $admissionDate = Carbon::parse($admission->admission_date);
            $dischargeDate = Carbon::parse($admission->discharge_date);
            if($admission->discharge_date == '' || $admission->discharge_date == NULL){

                $dischargeDate = Carbon::parse(date("Y-m-d")) ;
            }
            $daysDifference = $admissionDate->diffInDays($dischargeDate) + 1;
            $data['procedure_amount'] = ($data['procedure_amount']) * ($daysDifference);
            $data['daysDifference'] = $daysDifference;


        }



        //----- share percentange ---------//
        $share_amount = 0;
        if($data['consultant_share'] !='' && $data['consultant_share'] > 0){
            $percentage = $data['consultant_share']/100;
            $share_amount = ($data['procedure_amount']) * ($percentage);
        }



        $data['consultant_share_amount'] = $share_amount;



        $data['investigation_amount'] = $admission->investigation_cost;
        $data['service_charges'] = $admission->service_charges_cost;
        $patient_id = $admission->patient_id;

        $data['medicine_amount'] =$admission->medicine_cost;

        $total_cost = (($data['consultant_share_amount']) + ($data['investigation_amount']) + ($data['service_charges']) + ($data['medicine_amount']));
        $balance = ($data['procedure_amount']) - ($total_cost);
        $data['totalCost'] = $total_cost;
        $data['balance'] = $balance;
        $alertBalnce = ($data['procedure_amount']) * (25/100);
        $data['alert_balance'] = $alertBalnce;
        $data['alert'] = false;
        if($balance <= $alertBalnce){
            $data['alert'] = true;
        }

        return $data;


    }

    public function getAdmissionDetails1($value)
    {
        $admission = $value;

        $data['procedure_amount1'] = $admission->procedure_rate ?? '';
        $data['procedure_amount'] = $admission->procedure_rate ?? '';
        $data["is_medical_case"] = false;
        $data["daysDifference"] = 0;

        $data['consultant_share'] = $admission->consultant_share;
        if($admission->procedure_rate == 0){
            $data['procedure_amount1'] = $admission->consultant_procedure->amount;
            $data['procedure_amount'] = $admission->consultant_procedure->amount;
        }
        /*if($admission->consultant_share == 0){
            $data['consultant_share'] = $admission->consultant->share_percentage;
        }*/

        if($admission->procedure_type->type == "Medical"){
            //    dd($admission);
            $data["is_medical_case"] = true;
            $admissionDate = Carbon::parse($admission->admission_date);
            $dischargeDate = Carbon::parse($admission->discharge_date);
            if($admission->discharge_date == '' || $admission->discharge_date == NULL){

                $dischargeDate = Carbon::parse(date("Y-m-d")) ;
            }
            $daysDifference = $admissionDate->diffInDays($dischargeDate) + 1;
            $data['procedure_amount'] = ($data['procedure_amount']) * ($daysDifference);
            $data['daysDifference'] = $daysDifference;


        }



        //----- share percentange ---------//
        $share_amount = 0;
        if($data['consultant_share'] !='' && $data['consultant_share'] > 0){
            $percentage = $data['consultant_share']/100;
            $share_amount = ($data['procedure_amount']) * ($percentage);
        }



        $data['consultant_share_amount'] = $share_amount;



        $data['investigation_amount'] = $admission->investigation_cost;
        $data['service_charges'] = $admission->service_charges_cost;
        $patient_id = $admission->patient_id;

        $data['medicine_amount'] =$admission->medicine_cost;

        $total_cost = (($data['consultant_share_amount']) + ($data['investigation_amount']) + ($data['service_charges']) + ($data['medicine_amount']));
        $balance = ($data['procedure_amount']) - ($total_cost);
        $data['totalCost'] = $total_cost;
        $data['balance'] = $balance;
        $alertBalnce = ($data['procedure_amount']) * (25/100);
        $data['alert_balance'] = $alertBalnce;
        $data['alert'] = false;
        if($balance <= $alertBalnce){
            $data['alert'] = true;
        }

        return $data;


    }


    public function getInAdmissionDetails($admission_id)
    {
       // $admission = PatientAdmission::with('patient','procedure_type','consultant')->where(["id"=>$admission_id])->first();
        $admission = InPatientAdmission::where(["is_active"=> 1])->with("patient", "ward", "bed", 'consultant_procedure.procedure', 'consultant','sub_consultant')
            ->where(["id"=>$admission_id])->first();

        $data['procedure_amount1'] = $admission->procedure_rate ?? '';
        $data['procedure_amount'] = $admission->procedure_rate ?? '';
        $data["is_medical_case"] = false;
        $data["daysDifference"] = 0;

        $data['consultant_share'] = $admission->consultant_share_amount;
        if($admission->procedure_rate == 0){
            $data['procedure_amount1'] = $admission->procedure_type->net_rate;
            $data['procedure_amount'] = $admission->procedure_type->net_rate;
        }
        /*if($admission->consultant_share == 0){
            $data['consultant_share'] = $admission->consultant->share_percentage;
        }*/

        //dd($admission->consultant_procedure->procedure->type);

        if($admission->consultant_procedure->procedure->type == "Medical"){
            //    dd($admission);
            $data["is_medical_case"] = true;
            $admissionDate = Carbon::parse($admission->admission_date);
            $dischargeDate = Carbon::parse($admission->discharge_date);
            if($admission->discharge_date == '' || $admission->discharge_date == NULL){

                $dischargeDate = Carbon::parse(date("Y-m-d")) ;
            }
            $daysDifference = $admissionDate->diffInDays($dischargeDate) + 1;
            $data['procedure_amount'] = ($data['procedure_amount']) * ($daysDifference);
            $data['daysDifference'] = $daysDifference;


        }



        //----- share percentange ---------//
        $share_amount = $admission->consultant_share_amount;
        /*if($data['consultant_share'] !='' && $data['consultant_share'] > 0){
            $percentage = $data['consultant_share']/100;
            $share_amount = ($data['procedure_amount']) * ($percentage);
        }*/



        $data['consultant_share_amount'] = $share_amount;



        $data['investigation_amount'] = $admission->investigation_cost;
        $data['service_charges'] = $admission->service_charges_cost;
        $patient_id = $admission->patient_id;

        $data['medicine_amount'] =$admission->medicine_cost;

        $total_cost = (($data['consultant_share_amount']) + ($data['investigation_amount']) + ($data['service_charges']) + ($data['medicine_amount']));
        $balance = ($data['procedure_amount']) - ($total_cost);
        $data['totalCost'] = $total_cost;
        $data['balance'] = $balance;
        $alertBalnce = ($data['procedure_amount']) * (25/100);
        $data['alert_balance'] = $alertBalnce;
        $data['alert'] = false;
        if($balance <= $alertBalnce){
            $data['alert'] = true;
        }

        return $data;


    }
}
