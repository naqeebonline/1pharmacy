<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PatientController\PatientExpenseController;
use App\Models\Accounts\ConsultantSehatCardPayment;
use App\Models\Accounts\ConsultantShareInvoice;
use App\Models\Configuration\Consultants;
use App\Models\Configuration\ProcedureType;
use App\Models\Patient\PatientAdmission;
use App\Models\PaymentType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class DoctorPaymentsController extends Controller
{
    public function index()
    {
        $data['title'] = "Generate Sehat Saholat  Card Payments of Consultant";
        $data["procedure_type"] = ProcedureType::whereIsActive(1)->get();
        $data["consultant"] = Consultants::whereIsActive(1)->get();
        return view("accounts.generate_doctor_payments", $data);
    }

    public function load_discharge_patients_for_doctor_payments()
    {
        $patients = PatientAdmission::where(["is_active" => 1,"consultant_shares_payment_invoice_id"=>0])
            ->with("patient", "ward", "bed", 'procedure_type', 'consultant','sub_consultant')

            ->when(request()->from_date, function ($query) {
                $fromDate = Carbon::parse(request()->from_date)->endOfDay();
                $query->where('admission_date','>=',date("Y-m-d",strtotime($fromDate)));
            })
            ->when(request()->to_date, function ($query) {
                $toDate = Carbon::parse(request()->to_date)->endOfDay();
                $query->where('admission_date','<=',date("Y-m-d",strtotime($toDate)));
            })
            ->when(request()->procedure_type_id, function ($query) {
                //  dd("here");
                $query->where(['patient_admissions.procedure_type_id'=> request()->procedure_type_id]);
            })
            /*->when(request()->consultant_id, function ($query)  {
                $query->where(function ($query) {
                    $query->where('consultant_id', request()->consultant_id);
                       // ->orWhere('sub_consultant_id', request()->consultant_id);
                });

            })*/
            ->when(request()->consultant_id, function ($query) {
                $query->where(['consultant_id'=> request()->consultant_id]);//->orWhere(['sub_consultant_id'=> request()->consultant_id]);
            })
            ->orderBy("discharge_date","desc");
        return DataTables::of($patients)
            ->addColumn("edit_admission_date", function ($patient) {
                return  date("Y-m-d", strtotime($patient->admission_date));
            })
            ->addColumn("actions", function ($patient) {
                $buttons =  '<a target="_blank" href="' . route('pos.print_patient_admission', [$patient->id]) . '" class="btn btn-primary btn-sm "><i class="bx bx-printer tf-icons"></i></a>
                      <a href="javascript:void(0)"  data-details=\'' . $patient . '\'  class="btn btn-sm btn-warning edit_record"><i class="tf-icons bx bx-pencil"></i></a>
                      <a href="' . route('pos.view_patient_summary', [$patient->patient_id,$patient->id]) . '"  class="btn btn-sm btn-success edit_record"><i class="bx bxs-show tf-icons"></i></a>
                        ';
                if ($patient->procedure_type_id == 6 || $patient->procedure_type_id == 208 || $patient->procedure_type_id == 211) {
                    $buttons = $buttons . '<a  href="' . route('pos.patient_baby', [$patient->patient_id, $patient->id]) . '"  class="btn btn-primary btn-sm mt-2">View Baby Information</a>';
                }
                return $buttons;
            })

            ->rawColumns(["edit_admission_date", "actions"])
            ->make(true);
    }

    public function generatePayment($from_date='',$to_date="",$consultant_id='')
    {
        $data["from_date"] = $from_date;
        $data["to_date"] = $to_date;
        $data["consultant_id"] = $consultant_id;

        $saveInvoice = $_GET['save_payment'] ?? "no";

        if($saveInvoice == "yes"){
            $ids = json_decode($_GET['id']);
            
            $admission = PatientAdmission::with('patient','procedure_type','consultant',"sub_consultant")
                ->whereIn("patient_admissions.id",$ids)
                ->get();
            $total_share_amount = 0;
            foreach ($admission as $key => $value){
                $value->getAdmissionDetails = (new PatientExpenseController())->getAdmissionDetails($value->id);
                $value->consultant_share = $value->getAdmissionDetails['consultant_share'];
                $total_share_amount = ($total_share_amount) + $value->getAdmissionDetails['consultant_share_amount'];

            }
            $invoice = [
                "consultant_id" => $consultant_id,
                "from_date"     => date("Y-m-d",strtotime($from_date)),
                "to_date"       => date("Y-m-d",strtotime($to_date)),
                "created_by"    => auth()->user()->id,
                "created_at"    => date("Y-m-d H:i:s"),
                "total_amount"  => $total_share_amount,
                "admission_ids" => json_encode($ids)
            ];
            //dd($invoice);
            $invoice = ConsultantShareInvoice::create($invoice);
            $invoice_id = $invoice->id;
            foreach ($admission as $key => $value){
                PatientAdmission::where(["id"=>$value->id])->update(["consultant_shares_payment_invoice_id" => $invoice_id]);
            }
            return redirect()->route("pos.generate-doctor-invoice");
        }else{

            $admission = PatientAdmission::where(["is_active" => 1,"consultant_shares_payment_invoice_id"=>0])
                ->where("admission_status","Discharged")
                ->with("patient", "ward", "bed", 'procedure_type', 'consultant','sub_consultant')
                ->when(request()->from_date, function ($query) {
                    $fromDate = Carbon::parse(request()->from_date)->endOfDay();
                    $query->whereDate('discharge_date','>=',date("Y-m-d",strtotime($fromDate)));
                })
                ->when(request()->to_date, function ($query) {
                    $toDate = Carbon::parse(request()->to_date)->endOfDay();
                    $query->whereDate('discharge_date','<=',date("Y-m-d",strtotime($toDate)));
                })
                ->when(request()->procedure_type_id, function ($query) {
                    //  dd("here");
                    $query->where(['patient_admissions.procedure_type_id'=> request()->procedure_type_id]);
                })
                /*->when(request()->consultant_id, function ($query)  {
                    $query->where(function ($query) {
                        $query->where('consultant_id', request()->consultant_id);
                           // ->orWhere('sub_consultant_id', request()->consultant_id);
                    });

                })*/
                ->when(request()->consultant_id, function ($query) {
                    $query->where(['consultant_id'=> request()->consultant_id]);//->orWhere(['sub_consultant_id'=> request()->consultant_id]);
                })
                ->orderBy("discharge_date","asc")
                ->get();


            $total_share_amount = 0;
            $data['allData'] = $admission->pluck('id')->toArray() ?? [];

            foreach ($admission as $key => $value){
                $value->getAdmissionDetails = (new PatientExpenseController())->getAdmissionDetails($value->id);
                $value->consultant_share = $value->getAdmissionDetails['consultant_share'];
                $total_share_amount = ($total_share_amount) + $value->getAdmissionDetails['consultant_share_amount'];

            }
            $data['data'] = $admission;
            return view("accounts.generate_payment_view",$data);
        }

        //return view("reports.all_patient_total_cost",$data);
    }

    public function doctor_sehat_card_payments()
    {
        $data["title"] = "Doctor Sehat Card Payments";
        $data["suppliers"] = Consultants::where("is_active", 1)->get();
        $data["payment_type"] = PaymentType::get();
        return view("accounts/docrtor_sehat_card_payments", $data);
    }


    public function save_sc_payments(Request $request)
    {

        $data = $request->except(["id","_token"]);
        $data["created_by"] = Auth::user()->id;
        $data["created_at"] = date("Y-m-d H:i:s");
        $data["consultant_id"] = $request->consultant_id;

        $paymentDetail = ConsultantSehatCardPayment::create($data);

        return response()->json([
            "data" => $paymentDetail
        ]);
    }

    public function doctor_sc_invoices($id=''){

        $data =  ConsultantShareInvoice::with(["consultant","created_by"])->when($id, function ($query) use ($id) {
            return $query->where("consultant_id",$id);
        })
            ->where(["is_active"=>1]);
        //  <a class="btn btn-sm btn-primary" href="'.route('pos.edit_purchase_bill',[$data->GRNID]).'">Edit</a>
        return DataTables::of($data)

            ->addColumn('action', function ($data) {

                return '<a class="btn btn-sm btn-success" target="_blank" href="'.route('pos.print_doctor_invoice',[$data->id]).'">Print Invoice</a>
               
               
                    <a class="btn btn-sm btn-danger">Delete</a>';
            })
            ->rawColumns(['action']) // Allow raw HTML for action buttons
            ->make(true);


    }

    public function get_sc_payments_to_doctors($id=''){

        $data =  ConsultantSehatCardPayment::with(["consultant","created_by","payment_type"])->when($id, function ($query) use ($id) {
            return $query->where("consultant_id",$id);
        })
            ->where(["is_active"=>1])
            ->orderBy("id","desc");
        //  <a class="btn btn-sm btn-primary" href="'.route('pos.edit_purchase_bill',[$data->GRNID]).'">Edit</a>
        return DataTables::of($data)

            ->addColumn('action', function ($data) {

                return '<a class="btn btn-sm btn-danger delete_record" data-id="'.$data->id.'">Delete</a>';
            })
            ->rawColumns(['action']) // Allow raw HTML for action buttons
            ->make(true);


    }

    public function print_doctor_invoice($id)
    {
        $data['title'] = "Doctor Invoice";
        $res = ConsultantShareInvoice::whereId($id)->first();

        $admission = PatientAdmission::with('patient','procedure_type','consultant',"sub_consultant")
            ->whereIn("patient_admissions.id",json_decode($res->admission_ids))
            ->get();
        $total_share_amount = 0;
        foreach ($admission as $key => $value){
            $value->getAdmissionDetails = (new PatientExpenseController())->getAdmissionDetails($value->id);
            $value->consultant_share = $value->getAdmissionDetails['consultant_share'];
            $total_share_amount = ($total_share_amount) + $value->getAdmissionDetails['consultant_share_amount'];

        }
        $data['data'] = $admission;

    //    dd($admission);
        return view("accounts.print_doctor_invoice",$data);
    }
}
