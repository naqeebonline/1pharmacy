<?php

namespace App\Http\Controllers\Investigation;

use App\Http\Controllers\Controller;
use App\Models\Configuration\InvestigationParameter;
use App\Models\Configuration\InvestigationSubCategory;
use App\Models\Finance\FinanceTransaction;
use App\Models\Finance\FinanceVoucher;
use App\Models\ParameterHeading;
use App\Models\Patient\InvestigationResult as PatientInvestigationResult;
use App\Models\Patient\Patient;
use App\Models\Patient\PatientInvestigation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use LDAP\Result;
use Yajra\DataTables\Facades\DataTables;

class InvestigationResult extends Controller
{
    //
    public function investigation_result()
    {

        $investigations = PatientInvestigation::with("patient", "admission", "investigation")->get();
        // return $investigations;

        return view("laboratory.investigation_result");
    }

    public function investigation_result_list()
    {
        $investigations = PatientInvestigation::whereStatus(0)->where(["is_active"=>1])->with("patient", "admission", "investigation");

        return DataTables::of($investigations)
            ->editColumn('status', function ($row) {
                return $row->status == 1
                    ? '<span class="badge bg-success badge-success">Success</span>'
                    : '<span class="badge bg-dark badge-warning">Pending</span>';
            })
            ->editColumn('patient_type', function ($row) {
                return $row->patient_type == 'general_patient'
                    ? '<span class="badge bg-success badge-success">General Patient</span>'
                    : '<span class="badge bg-danger badge-danger">Sehat Card Patient</span>';
            })
            ->addColumn("actions", function ($investigation) {
                return '<a href="' . route('pos.investigation_add_result', [$investigation->id, $investigation->investigation_sub_category_id]) . '"  data-details=\'' . $investigation . '\'  class="btn btn-sm btn-warning ">Result</a>'
                    . (($investigation->status == 1)
                        ? '<a href="' . route('pos.print_inv_result', [$investigation->id]) . '" class="btn btn-sm btn-success btn-inline-block ">Print</a>'
                        : '');
            })
            ->rawColumns(["status", "actions","patient_type"])
            ->make(true);
    }
    public function investigation_completed_list()
    {
        $investigations = PatientInvestigation::whereStatus(1)->with("patient", "admission", "investigation")->orderBy("inv_out_date","desc");

        return DataTables::of($investigations)
            ->editColumn('status', function ($row) {
                return $row->status == 1
                    ? '<span class="badge bg-success badge-success">Success</span>'
                    : '<span class="badge bg-dark badge-warning">Pending</span>';
            })
            ->addColumn("actions", function ($investigation) {
                $buttons =  '&nbsp;<a href="' . route('pos.investigation_add_result', [$investigation->id, $investigation->investigation_sub_category_id]) . '"  data-details=\'' . $investigation . '\'  class="btn btn-sm btn-warning ">Result</a>'
                . (($investigation->status == 1)
                    ? '&nbsp;<a href="' . route('pos.print_inv_result', [$investigation->id]) . '" class="btn btn-sm btn-success btn-inline-block ">Print</a>'
                    : '');

                if(getUserRole() == "Super Admin"){
                    $buttons .= '&nbsp;<a href="javascript:void(0)" data-id=\'' . $investigation->id . '\'  class="btn btn-sm btn-danger delete_result">Delete</a>';
                }
                return $buttons;
            })
            ->rawColumns(["status", "actions"])
            ->make(true);
    }



    public function store_inv_result()
    {
       // dd(request()->all());
        $investigation  = $data["investigation"] = PatientInvestigation::find(request()->inv_id);
        $inv_result = PatientInvestigationResult::where("patient_investigation_id", request()->inv_id)->get();
        foreach ($inv_result as $result) {
            $result->delete();
        }

        if(request()->is_textual){
            PatientInvestigationResult::create([
                "patient_investigation_id" => request()->inv_id,
                "result_text_value" => request()->result_text_value,
                "result_entry_date" => Carbon::now(),
                "created_by" => auth()->user()->id,
                "updated_by" => auth()->user()->id,
            ]);
        }else{

                foreach (request()->parameter_id as $key => $paramenter) {

                    PatientInvestigationResult::create([
                        "patient_investigation_id" => request()->inv_id,
                        "parameter_id" => $paramenter,
                        "result_value" => request()->result[$key],
                        "result_text_value" => request()->result_text_value[$key],
                        "result_entry_date" => Carbon::now(),
                        "created_by" => auth()->user()->id,
                        "updated_by" => auth()->user()->id,
                    ]);
                }


        }

       /* $current_investigation =  PatientInvestigation::with(['consultant'])->where("id",request()->inv_id)->first();
        if($current_investigation && $current_investigation->status == 0 && $current_investigation->consultant_share_amount > 0){
            $voucher = generateVoucherNumber("investigation_shares",auth()->user()->id);
            $voucher_data = [
                "voucher_number" =>$voucher,
                "voucher_type"   => "investigation_shares",
                'user_id' => auth()->user()->id,
                'created_by' => auth()->user()->id,
                "voucher_date"   => date("Y-m-d"),
                "total_amount"   => $current_investigation->consultant_share_amount,
                "remarks"   => "Investigation Shares of Doctor ".$current_investigation->consultant->name ?? "",
                "created_by"   =>  auth()->user()->id,
                "approved_by"   => auth()->user()->id,
                "approved_at"   => date("Y-m-d H:i:s"),
                "created_at"   =>  date("Y-m-d H:i:s"),
            ];
            $voucher = FinanceVoucher::create($voucher_data);

            $rec = [
                'voucher_id' => $voucher->id,
                'transaction_date' => today(),
                'amount' => $current_investigation->consultant_share_amount,
                'debit_head_id' => financeHeadId('doctor_commission'),  // Doctor commision expense
                'credit_head_id' => $current_investigation->consultant->finance_head_id, // Dr. Naqeeb Ahmad (Liability)
                'reference_type' => 'commission',
                'reference_id' => $current_investigation->id,
                'user_id' => auth()->user()->id,
                'created_by' => auth()->user()->id,
                'remarks' => 'Lab shares of Invoice#'.$current_investigation->id.' posted to Doctor: '.$current_investigation->consultant->name ?? "".' Account. posted by '.auth()->user()->name,
                'created_at' => now()
            ];
            FinanceTransaction::insert($rec);
        }*/


        $investigation->status = 1;
        $investigation->inv_out_date = Carbon::now();
        $investigation->is_sync = 0;
        $investigation->inv_comment = request()->inv_comment;
        $investigation->update();
        
        return response()->json([
            "status"=> true,
            "message" => "Result Added"
        ]);
        
    }


    public function print_inv_result($inv_id)
    {
        $data["result"] = PatientInvestigation::where("id", $inv_id)->where("status", 1)->with("patient", "subCategory.main_category", "investigationResult.parameter", "admission.consultant","consultant")->first();

        $data['inv_sub_category'] = InvestigationSubCategory::where(['id'=>$data["result"]->investigation_sub_category_id])->first();
        foreach ($data["result"]->investigationResult as $key => $value){
            $value->parameter_heading = $value->parameter->parameter_heading->name ?? '';
        }

       // $res = collect($data["result"]->investigationResult)->groupBy('parameter_heading')->toArray();
        //dd($data["result"]->investigationResult);
        return view("reports.investigation_result", $data);
    }

    public function print_out_user_inv_result($inv_id)
    {
        $inv_id = Crypt::decrypt($inv_id);
        $data["result"] = PatientInvestigation::where("id", $inv_id)
            ->where("status", 1)
            ->with("patient", "subCategory.main_category", "investigationResult.parameter", "admission.consultant", "consultant")
            ->first();

        $data['inv_sub_category'] = InvestigationSubCategory::where(['id'=>$data["result"]->investigation_sub_category_id])->first();

        foreach ($data["result"]->investigationResult as $key => $value) {
            $value->parameter_heading = $value->parameter->parameter_heading->name ?? '';
        }

        // generate pdf
       // $pdf = Pdf::loadView('reports.print_out_user_inv_result', $data);

        // download with filename
       // return $pdf->download('investigation-result-'.$inv_id.'.pdf');
        return view("reports.print_out_user_inv_result", $data);
    }

    public function investigation_add_result($inv_id, $cat_id)
    {
        $data["investigation"] = PatientInvestigation::find($inv_id);
        $data['patient'] = Patient::where("id",$data["investigation"]->patient_id)->first();
        $sub_category = InvestigationSubCategory::find($cat_id);

        $data['sub_category'] = $sub_category;
        $data['is_textual'] = ($sub_category->is_parameter == 0) ? 1: 0;


        $data["is_ict"] = ($sub_category->is_ict == 1) ? true : false;

        $heading = ParameterHeading::where(["investigation_sub_category_id"=>$cat_id])->orWhere("id",1)->orderBy("id","asc")->get();
        foreach ($heading as $key => $value){
            $value->parameters = InvestigationParameter::where("is_active",1)
                ->where("parameter_heading_id",$value->id)
                ->where("investigation_sub_category_id", $cat_id)->orderBy("index_number","asc")->get();
        }

        $data['all_data'] = $heading;
        $data["parameters"] = InvestigationParameter::where("is_active",1)->where("investigation_sub_category_id", $cat_id)->orderBy("index_number","asc")->get();

        // return $paramenters;
        $data["result"] = PatientInvestigationResult::where("patient_investigation_id", $inv_id)->get();

        return view("laboratory.add_investigation_result", $data);
    }

    public function delete_investigation_result($patient_investigation_id)
    {
        DB::table('patient_investigation_result')->where(["patient_investigation_id"=>$patient_investigation_id])->delete();
        return redirect()->back();
    }
}
