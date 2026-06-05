<?php

namespace App\Http\Controllers\PatientController;

use App\Http\Controllers\Controller;
use App\Models\Configuration\ConsultantProcedure;
use App\Models\Configuration\Consultants;
use App\Models\Configuration\Investigation;
use App\Models\Configuration\InvestigationSubCategory;
use App\Models\Configuration\ProcedureType;
use App\Models\Configuration\ServiceType;
use App\Models\Configuration\Ward;
use App\Models\Configuration\WardBed;
use App\Models\Machine\MachinePatient;
use App\Models\Machine\MachineShift;
use App\Models\Patient\InPatientAdmission;
use App\Models\Patient\Patient;
use App\Models\Patient\PatientAdmission;
use App\Models\Patient\PatientBaby;
use App\Models\Patient\PatientDischargeChecklist;
use App\Models\Patient\PatientInvestigation;
use App\Models\Patient\PatientInvestigationPayment;
use App\Models\Patient\PatientNurseNote;
use App\Models\Patient\PatientOtNote;
use App\Models\Patient\PatientPayment;
use App\Models\Patient\PatientRefund;
use App\Models\Patient\PatientServiceCharges;
use App\Models\Patient\PatientVital;
use App\Models\Patient\Relation;
use App\Models\Sale;
use App\Models\SaleDetails;
use App\Models\SalePayment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Contracts\DataTable;
use Yajra\DataTables\Facades\DataTables;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
class PatientAdmissionController extends Controller
{

    public function patient_admission()
    {
        $data['title'] = "Patient Admission";
        $admitted_patient = PatientAdmission::where(["admission_status" => "Admit","is_active"=>1])->where("patient_type","sehat_card")->pluck("patient_id");
        //dd($admitted_patient);
        $data["patients"] = Patient::whereNotIn("id", $admitted_patient)->get();
        $data["relations"] = Relation::get();
        $data["wards"] = Ward::get();
        $data["procedure_type"] = ProcedureType::whereIsActive(1)->get();
        $data["consultant"] = Consultants::whereIsActive(1)->get();
        return view("patients.patient_admission", $data);
    }



    public function sehatcard_patients_statistics()
    {
       // $this->sehatCardPaymentReport();
        $data["title"] = "Sehat Card Patients Statistics";

        $res = PatientAdmission::where('is_active', 1)
            ->whereNotIn('id', [11])
            ->where('admission_status', '!=', 'Canceled')
            ->selectRaw('
        SUM(procedure_rate) as procedure_rate,
        SUM(consultant_share_amount) as consultant_share_amount,
        SUM(medicine_cost) as medicine_cost,
        SUM(investigation_cost) as investigation_cost,
        SUM(service_charges_cost) as service_charges_cost,
        SUM(totalCost) as totalCost,
        SUM(balance) as balance
    ')
            ->first();


        $data['total_claim'] =  $res->procedure_rate;
        $data['total_consultant_share_amount'] =  $res->consultant_share_amount;
        $data['total_medicine_cost'] =  $res->medicine_cost;
        $data['total_inv_cost'] =  $res->investigation_cost;
        $data['total_service_charges_cost'] =  $res->service_charges_cost;
        $data['total_cost'] =  $res->totalCost;
        $data['total_balance'] =  $res->balance;

        return view("accounts.sehatcard_patients_statistics",$data);
    }

    public function discharge_patient()
    {
        $data['title'] = "Discharge Patient";
        $data['procedure_type'] = ProcedureType::get();
        return view("patients.discharge_patient", $data);
    }


    public function list_admission()
    {
        $patients = PatientAdmission::where(["is_active"=> 1,"admission_status"=>"Admit"])->with("patient", "ward", "bed", 'procedure_type', 'consultant','sub_consultant')
        ->orderBy("id","desc");
        $res = [];
        return DataTables::of($patients)

            ->addColumn("edit_admission_date", function ($patient) {
                //dd(date("Y-m-d", strtotime($patient->admission_date)));
                return  date("Y-m-d", strtotime($patient->admission_date));
            })
            ->addColumn("alert", function ($patient) {
                $res = (new PatientExpenseController())->getAdmissionDetails1($patient);
                return  $res['alert'];
            })
            ->addColumn("totalCost", function ($patient) use($res) {
                $res = (new PatientExpenseController())->getAdmissionDetails1($patient);
                return  $res['totalCost'];
            })
            ->addColumn("balance", function ($patient) use($res) {
                $res = (new PatientExpenseController())->getAdmissionDetails1($patient);
                return  $res['balance'];
            })
            ->addColumn("actions", function ($patient) {
                $buttons =  '<a href="javascript:void(0)"  data-details=\'' . $patient . '\'  class="btn btn-sm btn-warning edit_record"><i class="tf-icons bx bx-pencil"></i></a> 
                        <button data-id="' . $patient->id . '" class="btn btn-danger btn-sm delete_record"><i class="bx bx-trash tf-icons"></i></button>
                        <a href="' . route('pos.view_patient_summary', [$patient->patient_id, $patient->id]) . '" class="btn btn-primary btn-sm "><i class="bx bxs-show tf-icons"></i></a>
                        <a href="' . route('pos.print_patient_admission', [$patient->id]) . '" class="btn btn-primary btn-sm "><i class="bx bx-printer tf-icons"></i></a>
                      
                        ';
                if ($patient->admission_status == "Admit") {
                    $buttons = $buttons . '<button data-id="' . $patient->id . '" class="btn btn-danger btn-sm cancel_admission mt-2">Admission Cancelation</button>';
                }
                if ($patient->procedure_type_id == 6 || $patient->procedure_type_id == 208 || $patient->procedure_type_id == 211) {
                    $buttons = $buttons . '<a  href="' . route('pos.patient_baby', [$patient->patient_id, $patient->id]) . '"  class="btn btn-success btn-sm mt-2">Add Baby Information</a>';
                }




                return $buttons;
            })

            ->rawColumns(["edit_admission_date",'totalCost','balance','alert', "actions"])
            ->make(true);
    }

    public function list_admission_statistics()
    {
        $patients = PatientAdmission::where(["is_active"=> 1])->with("patient", 'procedure_type', 'consultant')
            ;

        $res = [];
        return DataTables::of($patients)

            ->addColumn("edit_admission_date", function ($patient) {
                //dd(date("Y-m-d", strtotime($patient->admission_date)));
                return  date("Y-m-d", strtotime($patient->admission_date));
            })
            ->addColumn("alert", function ($patient) {
                $res = (new PatientExpenseController())->getAdmissionDetails1($patient);
                return  $res['alert'];
            })
            ->addColumn("totalCost", function ($patient) use($res) {
                $res = (new PatientExpenseController())->getAdmissionDetails1($patient);
                return  $res['totalCost'];
            })
            ->addColumn("balance", function ($patient) use($res) {
                $res = (new PatientExpenseController())->getAdmissionDetails1($patient);
                return  $res['balance'];
            })
            ->addColumn("actions", function ($patient) {




               $buttons = '<a href="' . route('pos.view_patient_summary', [$patient->patient_id, $patient->id]) . '" class="btn btn-primary btn-sm "><i class="bx bxs-show tf-icons"></i></a>';

                return $buttons;
            })

            ->rawColumns(["edit_admission_date",'totalCost','balance','alert', "actions"])
            ->make(true);
    }


    public function discharged_patient()
    {


        $data['title'] = "Discharged Patient";
        $data['type'] = "sehat_card";
        $data["procedure_type"] = ProcedureType::whereIsActive(1)->get();
        $data["consultant"] = Consultants::whereIsActive(1)->get();
        return view("patients.discharged_patient_list", $data);
    }



    public function discharged_patient_list()
    {

        $patients = PatientAdmission::where(["is_active" => 1, "admission_status" => "Discharged"])
            ->with("patient", "ward", "bed", 'procedure_type', 'consultant','sub_consultant')

            ->when(request()->patient_type, function ($query) {
                $query->where('patient_type',request()->patient_type);
            })
            ->when(request()->from_date, function ($query) {
                $fromDate = Carbon::parse(request()->from_date)->endOfDay();
                $query->where('discharge_date','>=',date("Y-m-d",strtotime($fromDate)));
            })
            ->when(request()->to_date, function ($query) {
                $toDate = Carbon::parse(request()->to_date)->endOfDay();
                $query->where('discharge_date','<=',date("Y-m-d",strtotime($toDate)));
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
            ->addColumn("alert", function ($patient) {
                $res = (new PatientExpenseController())->getAdmissionDetails1($patient);
                return  $res['alert'];
            })
            ->addColumn("totalCost", function ($patient) {
                $res = (new PatientExpenseController())->getAdmissionDetails1($patient);
                return  $res['totalCost'];
            })
            ->addColumn("balance", function ($patient)  {
                $res = (new PatientExpenseController())->getAdmissionDetails1($patient);
                return  $res['balance'];
            })
            ->rawColumns(["alert","totalCost","balance","edit_admission_date", "actions"])
            ->make(true);
    }



    public function ward_bed()
    {
        $occupied_beds = PatientAdmission::whereIsActive(1)->where(["admission_status" => "Admit"])->pluck('bed_id');
        $ward_bed = WardBed::where("ward_id", request()->id)
            ->when($occupied_beds, function ($query) use ($occupied_beds) {
                $query->whereNotIn('id', $occupied_beds)->whereIsActive(1)->get();
            })
            ->whereIsActive(1)->get();
        return response()->json([
            "status" => true,
            "message" => "Record Found.",
            "data" => $ward_bed
        ]);
    }

    public function store_patient_admission()
    {

        $data = request()->except(['_token', "id"]);
        if (request()->id == 0) {
            $data['admission_date'] = request()->admission_date . " " . date("H:i:s");

            $data['created_by'] = auth()->user()->id;
        } else {
            $data = request()->except(['_token', "id","patient_id"]);
            $data['updated_by'] = auth()->user()->id;
        }
        $procedure_type_id = ProcedureType::where(["id"=>request()->procedure_type_id])->first();

        $consultant = Consultants::where(["id"=>request()->consultant_id])->first();
        $data['consultant_share'] = $consultant->share_percentage;
        $data['procedure_rate'] = $procedure_type_id->net_rate;

        $share_amount = 0;
        if($data['consultant_share'] !='' && $data['consultant_share'] > 0){
            $percentage = $data['consultant_share']/100;
            $share_amount = ($data['procedure_rate']) * ($percentage);
        }

        $data['consultant_share_amount'] = $share_amount;


        $data['sec_procedure_rate'] = 0;
        if(request()->sec_procedure_type_id){
            $sec_procedure_type_id = ProcedureType::where(["id"=>request()->sec_procedure_type_id])->first();
            $data['sec_procedure_rate'] = $sec_procedure_type_id->net_rate ?? 0;
        }
        if(!request()->id){
            $data['patient_type'] = "sehat_card";
        }

        PatientAdmission::updateOrCreate(
            ["id" => request()->id],
            $data
        );

        return response()->json([
            "status" => true,
            "message" => "Record save successfully."
        ]);
    }

    public function patient_account($patient_id, $admission_id)
    {
        $data['patient'] = Patient::whereId($patient_id)->whereIsActive(1)->first();

        $data['investigation'] = InvestigationSubCategory::whereIsActive(1)->get();
        $data['service_type'] = ServiceType::whereIsActive(1)->get();
        $data['admission'] = PatientAdmission::with(["ward", "bed"])->whereId($admission_id)->whereIsActive(1)->first();
        return view("patients.patient_account", $data);
    }

    public function save_patient_investigation()
    {
        $data = request()->except(['_token', "id"]);

        $data['inv_date'] = request()->inv_date . " " . date("h:i:s");
        $investigation = InvestigationSubCategory::where(["id"=>request()->investigation_sub_category_id])->first();
        $data['inv_amount'] = $investigation->price ?? 0;
        $data['sale_price'] = $investigation->sale_price ?? 0;
        if (request()->id == 0) {
            $data['created_by'] = auth()->user()->id;
            $data['patient_type'] = "sehat_card";
        } else {
            $data['updated_by'] = auth()->user()->id;
        }
        PatientInvestigation::updateOrCreate(
            ["id" => request()->id],
            $data
        );
        $this->updateAdmissionDetails(request()->admission_id);
        return response()->json([
            "status" => true,
            "message" => "Record save successfully."
        ]);
    }

    public function save_in_patient_investigation()
    {
        $data = request()->except(['_token', "id"]);

        $data['inv_date'] = request()->inv_date . " " . date("h:i:s");
        $investigation = InvestigationSubCategory::where(["id"=>request()->investigation_sub_category_id])->first();
        $data['inv_amount'] = $investigation->price ?? 0;
        $data['sale_price'] = $investigation->sale_price ?? 0;
        if (request()->id == 0) {
            $data['created_by'] = auth()->user()->id;
            $data['patient_type'] = "sehat_card";
        } else {
            $data['updated_by'] = auth()->user()->id;
        }
        PatientInvestigation::updateOrCreate(
            ["id" => request()->id],
            $data
        );
        //$this->updateAdmissionDetails(request()->admission_id);
        return response()->json([
            "status" => true,
            "message" => "Record save successfully."
        ]);
    }


    public function get_admission_investigations($patient_id = '', $admission_id = '')
    {
       // dd("here");
        $patients = PatientInvestigation::where("is_active", 1)->with("investigation")
            ->when($patient_id, function ($query) use ($patient_id) {
                $query->where('patient_id', $patient_id);
            })
            /*->when(session('store_id'),function ($q){
                $q->where('store_id',session('store_id'));
            })*/
            ->when($admission_id, function ($query) use ($admission_id) {
                $query->where('admission_id', $admission_id);
            })
            ->orderBy("id", "desc");
        return DataTables::of($patients)
            ->addColumn("actions", function ($patient) {
                $button = '';
                if ($patient->status == 1) {
                    $button = $button . '<a target="_blank" href="' . route('pos.print_inv_result', [$patient->id]) . '"  data-details=\'' . $patient . '\'  class="btn btn-sm btn-success print_inv_record"><i class="tf-icons bx bx-printer"></i></a>';
                }
                $button = $button . '<button data-id="' . $patient->id . '" class="btn btn-danger btn-sm delete_record"><i class="bx bx-trash tf-icons"></i></button>';

                return $button;
            })

            ->addColumn("print_invoice_number", function ($patient) {
                return '<a target="_blank" href="' . route('pos.print_hospital_lab_invoice', [$patient->invoice_no]) . '">' . $patient->invoice_no . '</a>';
            })
            ->rawColumns(["print_invoice_number", "actions"])
            ->make(true);
    }

    public function save_patient_service_charges()
    {
        $data = request()->except(['_token', "id"]);
        $service = ServiceType::where("id",request()->service_type_id)->first();
        $data['actual_price'] = $service->price;
        $data['service_date'] = request()->service_date . " " . date("h:i:s");
        $data['created_by'] = auth()->user()->id;
        PatientServiceCharges::updateOrCreate(
            ["id" => request()->id],
            $data
        );
        $this->updateAdmissionDetails(request()->admission_id);
        return response()->json([
            "status" => true,
            "message" => "Record save successfully."
        ]);
    }

    public function save_in_patient_service_charges()
    {
        $data = request()->except(['_token', "id"]);
        $service = ServiceType::where("id",request()->service_type_id)->first();
        $data['actual_price'] = $service->price;
        $data['service_date'] = request()->service_date . " " . date("h:i:s");
        $data['created_by'] = auth()->user()->id;
        PatientServiceCharges::updateOrCreate(
            ["id" => request()->id],
            $data
        );
        $this->updateInAdmissionDetails(request()->admission_id);
        return response()->json([
            "status" => true,
            "message" => "Record save successfully."
        ]);
    }

    public function get_admission_service_charges($patient_id = '', $admission_id = '')
    {
        $patients = PatientServiceCharges::where("is_active", 1)->with("service_type")
            ->when($patient_id, function ($query) use ($patient_id) {
                $query->where('patient_id', $patient_id);
            })
            ->when($admission_id, function ($query) use ($admission_id) {
                $query->where('admission_id', $admission_id);
            })
            ->when(request()->patient_type, function ($query) {
                $query->where('patient_type', request()->patient_type);
            })
            ->orderBy("id", "desc");
        return DataTables::of($patients)
            ->addColumn("actions", function ($patient) {
                $buttons = "";
                $buttons .= '<a href="javascript:void(0)"  data-details=\'' . $patient . '\'  class="btn btn-sm btn-warning edit_service_record"><i class="tf-icons bx bx-pencil"></i></a>';
                return ' 
                       <button data-id="' . $patient->id . '" class="btn btn-danger btn-sm delete_service_record"><i class="bx bx-trash tf-icons"></i></button>
                 
                        ';
            })
            ->rawColumns(["actions"])
            ->make(true);
    }

    public function get_admitted_patient_treatments($patient_id, $admission_id)
    {
        $patients = SaleDetails::with("product", "sale")
            ->when($patient_id, function ($query) use ($patient_id) {
                $query->where('patient_id', $patient_id);
            })
            ->when(request()->medicine_type, function ($query) {
                $query->whereHas('sale', function ($q) {

                    $q->where('medicine_type', request()->medicine_type);
                });
            })

            ->when($admission_id, function ($query) use ($admission_id) {
                $query->where('admission_id', $admission_id);
            });
        return DataTables::of($patients)
            ->addColumn("actions", function ($patient) {
                if($patient->ReturnQuantity == $patient->Quantity){
                    return "";
                }else{
                    return '<a href="javascript:void(0)"  data-details=\'' . $patient . '\'  class="btn btn-sm btn-primary return_product">Return</a>';
                }

            })
            ->addColumn("total_amount", function ($value) {
                $total = ($value->UnitePrice) * ($value->Quantity);
                return number_format($total, 2);
            })
            ->addColumn("total_consumed", function ($value) {
                $total = ($value->Quantity) - ($value->ReturnQuantity);
                return $total;
            })

            ->rawColumns(["actions", "total_amount","total_consumed"])
            ->make(true);
    }


    public function list_admitted_patients()
    {
        $patients = PatientAdmission::where(["is_active"=> 1, "admission_status"=> "Admit"])
            //->where(["admission_status"=>"Admit"])
            ->with("patient", "ward", "bed", 'procedure_type', 'consultant');
        return DataTables::of($patients)

            ->addColumn("edit_admission_date", function ($patient) {
                //dd(date("Y-m-d", strtotime($patient->admission_date)));
                return  date("Y-m-d", strtotime($patient->admission_date));
            })
            ->addColumn("actions", function ($patient) {
                return ' 
                        <a href="' . route('pos.discharge_patient_from_ward', [$patient->id]) . '" class="btn btn-danger btn-sm">Discharge</button>
                       
                        ';
            })
            ->rawColumns(["edit_admission_date", "actions"])
            ->make(true);
    }

    public function discharge_patient_from_ward($id)
    {
        $data['data'] = PatientAdmission::whereId($id)->with(['patient', 'consultant', 'ward', 'bed'])->first();
        $data['procedure_type_id'] = $data['data']->procedure_type_id;
        $data['baby_count'] = PatientBaby::where(["admission_id"=>$id])->count();

        $data['admission_id'] = $id;

        $data['checklist'] = PatientDischargeChecklist::where(["admission_id" => $id])->first();
        $this->updateAdmissionDetails($id);
        return view("patients.discharge_patient_checklist", $data);
    }

    public function save_discharge_checklist()
    {
        PatientDischargeChecklist::updateOrCreate(
            ["id" => request()->id],
            request()->except(["id", "_token","admission_status","discharge_summary","sc_ref_no"])
        );
        PatientAdmission::whereId(request()->admission_id)->update(["discharge_date" => date("Y-m-d H:i:s"), "admission_status" => request()->admission_status,'discharge_summary'=>request()->discharge_summary,"sc_ref_no"=>request()->sc_ref_no]);
       // $this->updateAdmissionDetails(request()->admission_id);
        return redirect()->back();
    }






    public function patient_summary()
    {
        $data["patients"] = PatientAdmission::with("patient", "ward", "bed","consultant","procedure_type")
            ->where("admission_status", "Admit")
            ->orWhereDate('discharge_date', '>=', Carbon::now()->subDay(2)->format('Y-m-d H:i:s'))
            ->get();
        $data['investigation'] = InvestigationSubCategory::whereIsActive(1)->get();
        $data['service_type'] = ServiceType::whereIsActive(1)->get();


        return view("patients.patient_summary", $data);
    }



    public function patient_investigation()
    {
        $data["patients"] = PatientAdmission::where("admission_status", "Admit")
            ->with("patient", "ward", "bed")->get();
        $data['investigation'] = InvestigationSubCategory::whereIsActive(1)->get();
        $data['service_type'] = ServiceType::whereIsActive(1)->get();
        return view("patients.patient_investigation", $data);
    }

    function patient_service_charges()
    {
        $data["patients"] = PatientAdmission::where("admission_status", "Admit")
            ->orWhereDate('discharge_date', '>=', Carbon::now()->subDay(2)->format('Y-m-d H:i:s'))
            ->with("patient", "ward", "bed")->get();
       // dd($data["patients"]);
        $data['investigation'] = InvestigationSubCategory::whereIsActive(1)->get();
        $data['service_type'] = ServiceType::whereIsActive(1)->get();
        return view("patients.patient_service_charge", $data);
    }



    public function ot_notes()
    {
        //dd(Carbon::now()->subDay(2)->format('Y-m-d H:i:s'));
        $data["patients"] = PatientAdmission::where("admission_status", "Admit")
            ->orWhereDate('discharge_date', '>=', Carbon::now()->subDay(2)->format('Y-m-d H:i:s'))
            ->with("patient", "ward", "bed")->get();
        return view("patients.patient_ot_notes", $data);
    }


    public function save_patient_ot_notes()
    {
        $data = request()->except(['_token', "id"]);
        if (request()->id == 0) {
            $data["created_by"] = auth()->user()->id;
        } else {
            $data["updated_by"] = auth()->user()->id;
        }
        PatientOtNote::updateOrCreate(
            ["id" => request()->id],
            $data
        );
        return response()->json([
            "status" => true,
            "message" => "Record save successfully."
        ]);
    }

    public function get_patient_ot_notes($patient_id, $admission_id)
    {
        $patient_ot_notes = PatientOtNote::where(["patient_id" => $patient_id, "admission_id" => $admission_id])->whereIsActive(1)->with("patient", "admission");

        return DataTables::of($patient_ot_notes)
            ->addColumn("actions", function ($patient) {
                return '<a href="javascript:void(0)"  data-details=\'' . $patient . '\'  class="btn btn-sm btn-warning edit_service_record"><i class="tf-icons bx bx-pencil"></i></a> 
                        <button data-id="' . $patient->id . '" class="btn btn-danger btn-sm delete_service_record"><i class="bx bx-trash tf-icons"></i></button>
                 
                        ';
            })
            ->rawColumns(["actions"])
            ->make(true);
    }


    public function save_patient_nurse_notes()
    {
        $data = request()->except(['_token', "id"]);
        if (request()->id == 0) {
            $data["created_by"] = auth()->user()->id;
        } else {
            $data["updated_by"] = auth()->user()->id;
        }


        PatientNurseNote::updateOrCreate(
            ["id" => request()->id],
            $data
        );
        return response()->json([
            "status" => true,
            "message" => "Record save successfully."
        ]);
    }

    public function patient_nurse_notes()
    {
        $data["patients"] = PatientAdmission::where("admission_status", "Admit")
            ->orWhereDate('discharge_date', '>=', Carbon::now()->subDay(2)->format('Y-m-d H:i:s'))
            ->with("patient", "ward", "bed")->get();
        return view("patients.patient_nurse_notes", $data);
    }

    public function get_patient_nurse_notes($patient_id, $admission_id)
    {
        $patient_ot_notes = PatientNurseNote::where(["patient_id" => $patient_id, "admission_id" => $admission_id])->whereIsActive(1)->with("patient", "admission");

        return DataTables::of($patient_ot_notes)
            ->addColumn("actions", function ($patient) {
                return '<a href="javascript:void(0)"  data-details=\'' . $patient . '\'  class="btn btn-sm btn-warning edit_service_record"><i class="tf-icons bx bx-pencil"></i></a> 
                        <button data-id="' . $patient->id . '" class="btn btn-danger btn-sm delete_service_record"><i class="bx bx-trash tf-icons"></i></button>
                 
                        ';
            })
            ->rawColumns(["actions"])
            ->make(true);
    }

    public function print_patient_admission($id)
    {
        $data["patient"] = PatientAdmission::whereId($id)->with("user", "patient", "ward", "bed", "consultant", "procedure_type",'relation')->first();

        $data['cnic_array'] =str_split($data["patient"]->patient->cnic);
        $data['cnic_array'] = array_reverse($data['cnic_array'], true);
        $this->updateAdmissionDetails($id);
        return view("reports.patient_admission_report", $data);
    }

    public function print_in_patient_admission($id)
    {
        $data["patient"] = InPatientAdmission::whereId($id)->with("user", "patient", "ward", "bed", "consultant", "consultant_procedure.procedure",'relation')->first();

        $data['cnic_array'] =str_split($data["patient"]->patient->cnic);
        $data['cnic_array'] = array_reverse($data['cnic_array'], true);
       // $this->updateAdmissionDetails($id);
        return view("reports.in_patient_admission_report", $data);
    }


    public function patient_refunds()
    {
        $data["patients"] = PatientAdmission::where("admission_status", "Admit")->with("patient", "consultant", "ward", "bed","sec_procedure")->get();
        $data["consultants"] = Consultants::get();
        $data['investigation'] = InvestigationSubCategory::whereIsActive(1)->get();
        $data['service_type'] = ServiceType::whereIsActive(1)->get();
        return view("patients.patient_refunds", $data);
    }


    public function save_patient_refunds()
    {
        $data = request()->except(['_token', "id"]);
        $data["investigation_ids"] = json_encode(request()->investigation_ids);
        if (request()->id != 0) {
            $data["updated_at"] = Carbon::now();
            $data["updated_by"] = auth()->user()->id;
        } else {
            $data["created_by"] = auth()->user()->id;
        }
        PatientRefund::updateOrCreate(
            ["id" => request()->id],
            $data
        );
    }

    public function get_patient_refunds($patient_id, $admission_id)
    {
        $patient_refunds = PatientRefund::where(["patient_id" => $patient_id, "admission_id" => $admission_id])->first();

        return response()->json([
            "status" => true,
            "data" => $patient_refunds
        ]);
    }

    public function print_lab_invoice($invoice)
    {
        $data['data'] = PatientInvestigation::with(["investigation", "patient"])->where(["invoice_no" => $invoice])->whereIsActive(1)->get();
        return view("reports.print_lab_invoice", $data);
    }

    public function print_hospital_lab_invoice($invoice)
    {
        $data['data'] = PatientInvestigation::with(["investigation", "patient","created_by_user","consultant"])->where(["invoice_no" => $invoice])->whereIsActive(1)->get();
         //dd($data['data'][0]->created_by_user->name);
        $data['consultant_name'] = "";
        if($data['data'] && $data['data'][0]->consultant){
            $data['consultant_name'] = $data['data'][0]->consultant->name;
        }

        $total = 0;
        $discount_percentage = 0;
        $discount_amount = 0;
        foreach ($data['data'] as $key => $value){
            $total = $total + ($value->sale_price);
            $discount_percentage = $value->discount_percentage;
            $discount_amount = $discount_amount + ($value->discount_amount);
        }
        $data['total'] = $total;
        $data['discount_percentage'] = $discount_percentage;
        $data['discount_amount'] = $discount_amount;
        //$this->printInvestigationReceipt($data['data'],$data['total'],$data['discount_amount'],$data['discount_percentage']);
        //$this->printInvestigationReceipt($data['data'],$data['total'],$data['discount_amount'],$data['discount_percentage']);
        return view("reports.print_hospital_lab_invoice", $data);
    }

    public function printInvestigationReceipt($data, $total, $discountAmount = 0, $discountPercent = 0)
    {
        try {
            $connector = new WindowsPrintConnector(env("THERMAL_PRINTER_NAME"));
            $printer = new Printer($connector);

            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true); // Bold ON
            $printer->text(env('COMPANY_NAME') . "\n");
            $printer->setEmphasis(false); // Bold OFF
            $printer->text(date("d-m-Y h:i A") . "\n");
            $printer->text("------------------------------------------\n");

            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $invoice = $data[0]->invoice_no ?? "";
            $name = $data[0]->patient->name ?? "";
            $age = $data[0]->patient->age ?? "";
            $gender = $data[0]->patient->gender ?? "";

            $printer->text("Invoice: $invoice\n");
            $printer->text("Patient: $name\n");
            $printer->text("Age/Gender: $age / $gender\n");
            $printer->text(str_repeat("-", 48) . "\n");

            $printer->text(str_pad("#", 8) . str_pad("Test Name", 30) . str_pad("Amount", 10, " ", STR_PAD_LEFT) . "\n");
            $printer->text(str_repeat("-", 48) . "\n");

            $runningTotal = 0;
            $qrTestDetails = "";

            foreach ($data as $key => $value) {
                $testName = $value->investigation->name ?? 'N/A';
                $amount = number_format($value->sale_price ?? 0, 2);
                $runningTotal += $value->sale_price ?? 0;

                $shortName = strlen($testName) > 29 ? substr($testName, 0, 26) . '...' : $testName;

                $printer->text(
                    str_pad(($key + 1) . ".", 8) .
                    str_pad($shortName, 30) .
                    str_pad($amount, 10, ' ', STR_PAD_LEFT) .
                    "\n"
                );


                $printer->text(str_repeat("-", 48) . "\n");

                $netAmount = round($total - $discountAmount);

                $printer->text(str_pad("Total:", 38) . str_pad(number_format($total, 2), 10, ' ', STR_PAD_LEFT) . "\n");
                $printer->text(str_pad("Discount:", 30) . "Rs: $discountAmount ($discountPercent%)\n");
                $printer->text(str_pad("Net Amount:", 38) . str_pad(number_format($netAmount, 2), 10, ' ', STR_PAD_LEFT) . "\n");

                $printer->feed(2);
                $printer->setJustification(Printer::JUSTIFY_CENTER);
                $printer->text("Thank you for visiting!\n");

                // 👉 Full QR Code Content
                $qrContent = "http://127.0.0.1:8000/print_hospital_lab_invoice/1752813420210";

                $printer->feed(1);
                // $printer->qrCode($qrContent, Printer::QR_ECLEVEL_L, 6); // Size 6
                $printer->feed(1);
                $printer->text("Scan for Details\n");

                $printer->feed(3);
                $printer->cut();
                $printer->close();
            }

            return true;
        } catch (\Exception $e) {
            \Log::error("Print failed: " . $e->getMessage());
            return false;
        }
    }

    public function get_ward_investigations($admission_id)
    {
        $admission = PatientAdmission::whereId($admission_id)->first();
        $ward = Ward::whereId($admission->ward_id)->first();
        if ($ward->short_name == "Dialysis") {
            $patientId = $admission->patient_id;
            $investigationIds = [1, 16, 60, 17, 18, 11]; // List of investigation IDs to check

            $filteredInvestigations = collect($investigationIds)->filter(function ($investigationId) use ($patientId) {
                // Check if the investigation was performed in the last 30 days
                $recentInvestigation = DB::table('patient_investigations')
                    ->where('patient_id', $patientId)
                    ->where('investigation_sub_category_id', $investigationId)
                    ->whereDate('created_at', '>=', Carbon::now()->subDays(30)) // Performed within the last 30 days
                    ->exists();

                // Allow only investigations not performed in the last 30 days
                return !$recentInvestigation;
            });

            $investigations = InvestigationSubCategory::whereIn('id', $filteredInvestigations->toArray())
                ->whereIsActive(1)
                ->get();


           // $investigations = InvestigationSubCategory::whereIn("id", [1, 16, 60, 17, 18, 11])->whereIsActive(1)->get();


        } else {
            $investigations = InvestigationSubCategory::whereIsActive(1)->get();
        }
        foreach ($investigations as $key => $value){
            $value->price = ($value->price) ? $value->price : 0;
        }
        return response()->json(["status" => true, "data" => $investigations]);
    }

    public function out_user_print_investigation($invoice_no)
    {
        $patients = PatientInvestigation::where("is_active", 1)->with("investigation")
            ->with("patient")
            ->with("users")
            ->with("created_by_user")

            ->when(request()->from_date, function ($query) {
                $query->whereDate('inv_date','>=',date("Y-m-d",strtotime(request()->from_date)));
            })
            ->when(request()->to_date, function ($query) {
                $query->whereDate('inv_date','<=',date("Y-m-d",strtotime(request()->to_date)));
            })
            ->when(request()->investigation_sub_category_id, function ($query) {
                $query->where('investigation_sub_category_id','=',request()->investigation_sub_category_id);
            })
            ->when(request()->created_by, function ($query) {
                $query->where('created_by','=',request()->created_by);
            })
            ->where(["patient_type"=>"hospital_patient"])
            ->where(["invoice_no"=>$invoice_no])
            ->orderBy("id", "desc")->get();
          $data['data'] = $patients;
         // dd($patients);
          return view("out_user.out_user_print_investigation",$data);
    }



    public function patient_vitals(){
        $data["patients"] = PatientAdmission::where("admission_status", "Admit")
            ->orWhereDate('discharge_date', '>=', Carbon::now()->subDay(2)->format('Y-m-d H:i:s'))
            ->with("patient", "ward", "bed")->get();
        return view("patients.patient_vitals", $data);
    }

    public function patient_vitals_list($patient_id, $admission_id){
        $vitals = PatientVital::where(["patient_id"=>$patient_id, "admission_id"=>$admission_id])->whereIsActive(1);

        return DataTables::of($vitals)
            ->addColumn("actions", function ($vital) {
                return '<a href="javascript:void(0)"  data-details=\'' . $vital . '\'  class="btn btn-sm btn-warning edit_service_record"><i class="tf-icons bx bx-pencil"></i></a> 
                        <button data-id="' . $vital->id . '" class="btn btn-danger btn-sm delete_service_record"><i class="bx bx-trash tf-icons"></i></button>
                 
                        ';
            })
            ->rawColumns(["actions"])
            ->make(true);
        
    }

    public function save_patient_vitals(){
        PatientVital::updateOrCreate(
            ["id"=>request()->id],
            request()->except(["_token","id"])
        );
        return ["status"=>true,"message"=>"Record saved successfully"];
    }

    public function print_admitted_patient_treatment_report($patient_id, $admission_id)
    {
        $data['admission'] = PatientAdmission::with('patient')->where(["id"=>$admission_id])->first();
        $data['data'] = SaleDetails::with("product", "sale")
            ->when($patient_id, function ($query) use ($patient_id) {
                $query->where('patient_id', $patient_id);
            })
            ->when($admission_id, function ($query) use ($admission_id) {
                $query->where('admission_id', $admission_id);
            })->get();
        return view("reports.customer_purchase_report_new",$data);
    }

    public function patient_treatment_chart_report($patient_id, $admission_id,$medicine_type_id='')
    {
        $data['admission'] = PatientAdmission::with('patient')->where(["id"=>$admission_id])->first();
        $data['data'] = SaleDetails::with("product", "sale")
            ->when($patient_id, function ($query) use ($patient_id) {
                $query->where('patient_id', $patient_id);
            })
            ->when($admission_id, function ($query) use ($admission_id) {
                $query->where('admission_id', $admission_id);
            })
            ->when($medicine_type_id, function ($query) use($medicine_type_id) {
                $query->whereHas('sale', function ($q) use($medicine_type_id){

                    $q->where('medicine_type', $medicine_type_id);
                });
            })
            ->get();
        return view("reports.patient_treatment_chart_report",$data);
    }

    public function in_patient_treatment_chart_report($patient_id, $admission_id,$medicine_type_id='')
    {
        $data['admission'] = InPatientAdmission::with('patient')->where(["id"=>$admission_id])->first();
        //dd($data);
        $data['data'] = SaleDetails::with("product", "sale")
            ->when($patient_id, function ($query) use ($patient_id) {
                $query->where('patient_id', $patient_id);
            })
            ->when($admission_id, function ($query) use ($admission_id) {
                $query->where('admission_id', $admission_id);
            })
            ->when($medicine_type_id, function ($query) use($medicine_type_id) {
                $query->whereHas('sale', function ($q) use($medicine_type_id){

                    $q->where('medicine_type', $medicine_type_id);
                });
            })
            ->get();
        return view("reports.patient_treatment_chart_report",$data);
    }

    public function cencel_patient_admission()
    {
        $data = request()->except(["_token","id",'']);
         $data['admission_status'] = "Canceled";
         $data['discharge_summary'] = request()->discharge_summary;
         $data['discharge_date'] = date("Y-m-d H:i:s");

        PatientAdmission::updateOrCreate(
            ["id" => request()->id],
            $data
        );
        return response()->json(["status" => true, "data" => "done"]);
    }

    public function cancel_in_patient_admission()
    {
        $data = request()->except(["_token","id",'']);
         $data['admission_status'] = "Canceled";
         $data['discharge_summary'] = request()->discharge_summary;
         $data['discharge_date'] = date("Y-m-d H:i:s");
         $data['updated_by'] = auth()->user()->id;
         $data['updated_at'] = date("Y-m-d H:i:s");

        InPatientAdmission::updateOrCreate(
            ["id" => request()->id],
            $data
        );
        return response()->json(["status" => true, "data" => "done"]);
    }

    public function canceled_patients()
    {
        $data['title'] = "Canceled Admissions";
        return view("patients.canceled_patient_list", $data);
    }
    public function canceled_patient_list()
    {
        $patients = PatientAdmission::where(["is_active" => 1, "admission_status" => "Canceled"])->with("patient", "ward", "bed", 'procedure_type', 'consultant');
        return DataTables::of($patients)

            ->addColumn("edit_admission_date", function ($patient) {
                //dd(date("Y-m-d", strtotime($patient->admission_date)));
                return  date("Y-m-d", strtotime($patient->admission_date));
            })
            ->addColumn("canelation_reason_text", function ($patient) {
                return "<p style='color: red'>".$patient->canelation_reason."</p>";
            })
            ->addColumn("actions", function ($patient) {
                $buttons =  '<a href="javascript:void(0)"  data-details=\'' . $patient . '\'  class="btn btn-sm btn-warning edit_record"><i class="tf-icons bx bx-pencil"></i></a> 
                        <button data-id="' . $patient->id . '" class="btn btn-danger btn-sm delete_record"><i class="bx bx-trash tf-icons"></i></button>
                        <a href="' . route('pos.patient_account', [$patient->patient_id, $patient->id]) . '" class="btn btn-primary btn-sm "><i class="bx bxs-show tf-icons"></i></a>
                        <a href="' . route('pos.print_patient_admission', [$patient->id]) . '" class="btn btn-primary btn-sm "><i class="bx bx-printer tf-icons"></i></a>
                      
                        ';
                if ($patient->admission_status == "Admit") {
                    $buttons = $buttons . '<button data-id="' . $patient->id . '" class="btn btn-danger btn-sm cancel_admission mt-2">Admission Cancelation</button>';
                }
                return $buttons;
            })
            ->rawColumns(["edit_admission_date", "actions","canelation_reason_text"])
            ->make(true);
    }

    public function update_patient_admission()
    {
        $share_amount = 0;
        if(request()->consultant_share !='' && request()->consultant_share > 0){
            $percentage = request()->consultant_share/100;
            $share_amount = (request()->procedure_rate) * ($percentage);
        }



        PatientAdmission::where(["id"=>request()->admission_id])->update(
            [
                "consultant_id"=>request()->consultant_id,
                "sc_ref_no"=>request()->edit_sc_ref_no,
                "sub_consultant_id"=>request()->sub_consultant_id,
                "procedure_type_id"=>request()->procedure_type_id,
                "consultant_share"=>request()->consultant_share,
                "procedure_rate"=>request()->procedure_rate,
                "consultant_share_amount"=>$share_amount,
                "sec_procedure_type_id"=>request()->sec_procedure_type_id,
                "sec_procedure_rate"=>request()->sec_procedure_rate ?? 0,
                "advance_payment"=>request()->advance_payment ?? 0,
                //"sc_ref_no"=>request()->edit_sc_ref_no ?? 0,
                "updated_by"=>auth()->user()->id,
            ]
        );

        $this->updateAdmissionDetails(request()->admission_id);
        return ["status"=>true,"message"=>"Record updated successfully"];
    }

    public function update_in_patient_admission()
    {
        $share_amount = 0;
        if(request()->consultant_share !='' && request()->consultant_share > 0){
            $percentage = request()->consultant_share/100;
            $share_amount = (request()->procedure_rate) * ($percentage);
        }

        $consultant_procedure_id = ConsultantProcedure::where(["id"=>request()->consultant_procedure_id])->first();



        $data['consultant_share'] = $consultant_procedure_id->consultant_share_percentage;
        $data['consultant_share_amount'] = $consultant_procedure_id->consultant_share_amount;
        $data['procedure_rate'] = request()->procedure_rate;

        InPatientAdmission::where(["id"=>request()->admission_id])->update(
            [
                "consultant_id"=>request()->consultant_id,
               // "sc_ref_no"=>request()->edit_sc_ref_no,
                //"sub_consultant_id"=>request()->sub_consultant_id,
                "included_medicine"=> request()->included_medicine ? request()->included_medicine : 0,
                "consultant_procedure_id"=>request()->consultant_procedure_id,
                "consultant_share"=>$consultant_procedure_id->consultant_share_percentage,
                "procedure_rate"=>request()->procedure_rate,
                "consultant_share_amount"=>$consultant_procedure_id->consultant_share_amount,
                //"sec_procedure_type_id"=>request()->sec_procedure_type_id,
                //"sec_procedure_rate"=>request()->sec_procedure_rate ?? 0,
                "advance_payment"=>request()->advance_payment ?? 0,
                //"sc_ref_no"=>request()->edit_sc_ref_no ?? 0,
                "updated_by"=>auth()->user()->id,
            ]
        );

      //  $this->updateAdmissionDetails(request()->admission_id);
        return ["status"=>true,"message"=>"Record updated successfully"];
    }

    public function update_patient_admission_info()
    {

        $data = request()->except(["id","_token","admission_id"]);
        $data['updated_by'] = auth()->user()->id;
        PatientAdmission::where(["id"=>request()->admission_id])->update(
            $data
        );

        $this->updateAdmissionDetails(request()->admission_id);
        return ["status"=>true,"message"=>"Record updated successfully"];
    }

    public function birth_certificate($baby_id)
    {
        $data['baby'] = PatientBaby::with(['admission','patient'])->where(['id'=>$baby_id])->get();

        return view("reports.birth_certificate",$data);
    }

    public function patient_baby($patient_id='',$admission_id='')
    {
        $data['admission'] = PatientAdmission::where(["id"=>$admission_id])->first();
        $data['patient'] = Patient::where(["id"=>$patient_id])->first();
       //  dd($data['patient']);
        $data['title'] = "Patient Baby";
        $data['patient_id'] = $patient_id;
        $data['admission_id'] = $admission_id;
        return view('patients.patient_baby',$data);
    }

    public function list_patient_baby($patient_id, $admission_id){
        $vitals = PatientBaby::where(["patient_id"=>$patient_id, "admission_id"=>$admission_id])->where(["is_active"=>1]);

        return DataTables::of($vitals)
            ->addColumn("actions", function ($vital) {
                $buttons = '<a href="javascript:void(0)"  data-details=\'' . $vital . '\'  class="btn btn-sm btn-warning edit_service_record"><i class="tf-icons bx bx-pencil"></i></a> 
                        <button data-id="' . $vital->id . '" class="btn btn-danger btn-sm delete_record"><i class="bx bx-trash tf-icons"></i></button>
                 
                        ';
                if($vital->baby_status == 'Alive'){
                    $buttons = $buttons . '<a target="_blank"  href="' . route('pos.birth_certificate', [$vital->id]) . '"  class="btn btn-primary btn-sm mt-2">Print</a>';
                }

                return $buttons;

            })
            ->rawColumns(["actions"])
            ->make(true);

    }

    public function save_baby()
    {
        $data = request()->except(['_token', "id"]);

        if (request()->id == 0) {
            $data['created_by'] = auth()->user()->id;
        } else {
            $data['updated_by'] = auth()->user()->id;
        }
        PatientBaby::updateOrCreate(
            ["id" => request()->id],
            $data
        );

        return response()->json([
            "status" => true,
            "message" => "Record save successfully."
        ]);
    }

    public function getMachinePatientReport()
    {

        $data = [
            ["day" =>"monday"],
            ["day" =>"tuesday"],
            ["day" =>"wednesday"],
            ["day" =>"thursday"],
            ["day" =>"friday"],
            ["day" =>"saturday"],
            ["day" =>"sunday"],
        ];
        foreach ($data as $key => $value){
            $data[$key]['shift'] = MachineShift::get();
        }

        foreach ($data as $key => $value){
            foreach ($value['shift'] as $key2 => $shift){
                $value['shift'][$key2]['patients'] = MachinePatient::with('patient','machine_shift','machine_category')->where(["day"=>$value['day'],"machine_shift_id"=> $shift->id,"is_active"=>1])->get();
            }
        }
        $data['data'] = $data;
        return view('reports.machine_patient_report',$data);
    }

    public function view_patient_summary($patient_id,$admission_id)
    {
        $data["patient"] = PatientAdmission::with("patient", "ward", "bed","consultant","procedure_type")
            ->where("id", $admission_id)

            ->first();
        $data['summary'] =  (new PatientExpenseController())->getAdmissionDetails($admission_id);

        $data['investigation'] = InvestigationSubCategory::whereIsActive(1)->get();
        $data['service_type'] = ServiceType::whereIsActive(1)->get();

        $this->updateAdmissionDetails($admission_id);

        $data['patient_id'] = $patient_id;
        $data['admission_id'] = $admission_id;
        return view("patients.view_patient_summary", $data);
    }

    public function view_in_patient_summary($patient_id,$admission_id)
    {
        $this->updateInAdmissionDetails($admission_id);
        $data["patient"] = InPatientAdmission::where(["is_active"=> 1,"admission_status"=>"Admit"])->with("patient", "ward", "bed", 'consultant_procedure.procedure', 'consultant','sub_consultant')
            ->where("id", $admission_id)
            ->first();

        $data['summary'] =  (new PatientExpenseController())->getInAdmissionDetails($admission_id);


        $data['investigation'] = InvestigationSubCategory::whereIsActive(1)->get();
        $data['service_type'] = ServiceType::whereIsActive(1)->get();



        $data['patient_id'] = $patient_id;
        $data['admission_id'] = $admission_id;
        return view("patients.view_in_patient_summary", $data);
    }

    public function updatePatientData($from_id,$to_id)
    {
        $admission = PatientAdmission::where("id",">",$from_id)->where("id","<=",$to_id)->get("id");
        foreach ($admission as $key => $value){
            $this->updateAdmissionDetails($value->id);
        }

        dd($from_id,$to_id,"from id $from_id to $to_id data has been processed");
    }

    public function updateAdmissionDetails($admission_id)
    {

        $admission = PatientAdmission::with('patient','procedure_type','consultant')->where(["id"=>$admission_id])->first();
        if($admission->patient_type == "hospital_patient"){

        }else{

        }
        $data['procedure_amount1'] = $admission->procedure_rate ?? 0;
        $data['procedure_amount'] = $admission->procedure_rate ?? 0;
        $data["is_medical_case"] = false;
        $data["daysDifference"] = 0;

        $data['consultant_share'] = $admission->consultant_share ?? 0;
       // if($admission->procedure_rate == 0){
            $data['procedure_amount1'] = $admission->procedure_rate ?? 0;
            $data['procedure_amount'] = $admission->procedure_rate ?? 0;
        //}
        /*if($admission->consultant_share == 0){
            $data['consultant_share'] = $admission->consultant->share_percentage;
        }*/

        if(($admission) && $admission->procedure_type->type == "Medical"){

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

        $investigations = PatientInvestigation::with('subCategory')->where(["admission_id"=>$admission_id,"is_active"=>1])->get();
        $investigationAmount = 0;
        foreach ($investigations as $key => $value){
            $investigationAmount += $value->subCategory->price;
        }

        $data['investigation_amount'] = $investigationAmount;
        $data['service_charges'] = PatientServiceCharges::where(["admission_id"=>$admission_id,"is_active"=>1])->sum('service_rate');
        $patient_id = $admission->patient_id ?? 0;
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

        $updateData = [
            "consultant_share_amount" => $share_amount,
            "investigation_cost"        =>$investigationAmount,
            "service_charges_cost"      =>$data['service_charges'],
            "medicine_cost"      =>$totalAmount,
            "totalCost"      =>$total_cost,
            "balance"      =>$balance,
        ];

        PatientAdmission::where("id",$admission_id)->update($updateData);

        return $data;


    }

    public function sehatCardDashboard()
    {
       $data = $this->sehatCardDashboardData();
       return view("sehat_card_dashboard",$data);

    }

    public function sehatCardDashboardData()
    {
 
        $data['received_from_sc'] = PatientAdmission::whereNotNull('amount_received_from_sehat_card')
                                    ->where("is_active",1)
                                    ->select('sc_ref_no', \DB::raw('MAX(amount_received_from_sehat_card) as amount'))
                                    ->groupBy('sc_ref_no')
                                
                                    ->get()
                                    ->sum('amount');

        $data['total_payment_to_doctor'] = PatientAdmission::where("consultant_shares_payment_invoice_id",">",'procedure_rate')->sum('consultant_share_amount');


        $data['total_claims'] = PatientAdmission::where("is_active",1)
            ->whereIn("admission_status",["Admit","Discharged"])
            ->count() - 2;

        $data['sc_paid_claims'] = PatientAdmission::whereNotNull('amount_received_from_sehat_card')
            ->count();
        $data['sc_pending_claims'] = $data['total_claims'] - $data['sc_paid_claims'];

        return $data;
    }


    public function hospital_patient_admission()
    {
        $data['title'] = "Patient Admission";
        $admitted_patient = InPatientAdmission::where(["admission_status" => "Admit","is_active"=>1])->where("patient_type","hospital_patient")->pluck("patient_id");
        //dd($admitted_patient);
        $data["patients"] = Patient::whereNotIn("id", $admitted_patient)->get();
        $data["relations"] = Relation::get();
        $data["wards"] = Ward::get();
        $data["procedure_type"] = ProcedureType::whereIsActive(1)->get();
        $data["consultant"] = Consultants::whereIsActive(1)->get();
        return view("patients.hospital_patient_admission", $data);
    }



    public function hospital_list_admission()
    {
        $patients = InPatientAdmission::where(["is_active"=> 1,"admission_status"=>"Admit"])->with("patient", "ward", "bed", 'consultant_procedure.procedure', 'consultant','sub_consultant')
            ->orderBy("id","desc")
            ->where("patient_type","hospital_patient");


        $res = [];
        return DataTables::of($patients)

            ->addColumn("edit_admission_date", function ($patient) {
                //dd(date("Y-m-d", strtotime($patient->admission_date)));
                //return  date("Y-m-d", strtotime($patient->admission_date));
            })
            ->addColumn("alert", function ($patient) {
                /*$res = (new PatientExpenseController())->getAdmissionDetails1($patient);
                return  $res['alert'];*/
            })
            ->addColumn("totalCost", function ($patient) use($res) {
                /*$res = (new PatientExpenseController())->getAdmissionDetails1($patient);
                return  $res['totalCost'];*/
            })
            ->addColumn("balance", function ($patient) use($res) {
                /*$res = (new PatientExpenseController())->getAdmissionDetails1($patient);
                return  $res['balance'];*/
            })
            ->addColumn("actions", function ($patient) {
                $buttons =  '<a href="javascript:void(0)"  data-details=\'' . $patient . '\'  class="btn btn-sm btn-warning edit_record"><i class="tf-icons bx bx-pencil"></i></a> 
                        <button data-id="' . $patient->id . '" class="btn btn-danger btn-sm delete_record"><i class="bx bx-trash tf-icons"></i></button>
                        <a href="' . route('pos.view_in_patient_summary', [$patient->patient_id, $patient->id]) . '" class="btn btn-primary btn-sm "><i class="bx bxs-show tf-icons"></i></a>
                        <a href="' . route('pos.print_in_patient_admission', [$patient->id]) . '" class="btn btn-primary btn-sm "><i class="bx bx-printer tf-icons"></i></a>
                      
                        ';
                if ($patient->admission_status == "Admit") {
                    $buttons = $buttons . '<button data-id="' . $patient->id . '" class="btn btn-danger btn-sm cancel_admission mt-2">Admission Cancelation</button>';
                }
                /*if ($patient->procedure_type_id == 6 || $patient->procedure_type_id == 208 || $patient->procedure_type_id == 211) {
                    $buttons = $buttons . '<a  href="' . route('pos.patient_baby', [$patient->patient_id, $patient->id]) . '"  class="btn btn-success btn-sm mt-2">Add Baby Information</a>';
                }*/




                return $buttons;
            })

            ->rawColumns(["edit_admission_date",'totalCost','balance','alert', "actions"])
            ->make(true);
    }

    public function hospital_store_patient_admission()
    {

        //dd(request()->all());
        $data = request()->except(['_token', "id"]);
        if (request()->id == 0) {
            $data['admission_date'] = request()->admission_date . " " . date("H:i:s");

            $data['created_by'] = auth()->user()->id;
        } else {
            $data = request()->except(['_token', "id","patient_id"]);
            $data['updated_by'] = auth()->user()->id;
        }
        $consultant_procedure_id = ConsultantProcedure::where(["id"=>request()->consultant_procedure_id])->first();

        $consultant = Consultants::where(["id"=>request()->consultant_id])->first();
        $data['consultant_share'] = $consultant_procedure_id->consultant_share_percentage;
        $data['consultant_share_amount'] = $consultant_procedure_id->consultant_share_amount;
        $data['consultant_charges'] = $consultant_procedure_id->consultant_charges;
        $data['procedure_rate'] = request()->procedure_rate;



        $data['patient_type'] = "hospital_patient";
       $admission =  InPatientAdmission::updateOrCreate(
            ["id" => request()->id],
            $data
        );

        return response()->json([
            "status" => true,
            "message" => "Record save successfully."
        ]);
    }


    public function in_patient_investigation()
    {
        $data["patients"] = InPatientAdmission::where("admission_status", "Admit")
            ->with("patient", "ward", "bed")->where("patient_type","hospital_patient")->get();
        $data['investigation'] = InvestigationSubCategory::whereIsActive(1)->get();
        $data['service_type'] = ServiceType::whereIsActive(1)->get();
        return view("patients.in_patient_investigation", $data);
    }

    public function in_patient_discharged_list()
    {


        $data['title'] = "Discharged Patient";
        $data['type'] = "sehat_card";
        $data["procedure_type"] = ProcedureType::whereIsActive(1)->get();
        $data["consultant"] = Consultants::whereIsActive(1)->get();
        return view("patients.in_patient_discharged_list", $data);
    }

    function in_patient_service_charges()
    {
        $data["patients"] = InPatientAdmission::where("admission_status", "Admit")
            ->where("patient_type","hospital_patient")
            //->orWhereDate('discharge_date', '>=', Carbon::now()->subDay(2)->format('Y-m-d H:i:s'))
            ->with("patient", "ward", "bed")->get();

        $data['service_type'] = ServiceType::whereIsActive(1)->get();
        return view("patients.in_patient_service_charge", $data);
    }

    function in_patient_payment()
    {
        $data["patients"] = InPatientAdmission::where("admission_status", "Admit")
            ->where("patient_type","hospital_patient")
            ->when(request()->has('patient_id'), function ($query) {
                $query->where('patient_id',request()->patient_id);
            })
            //->orWhereDate('discharge_date', '>=', Carbon::now()->subDay(2)->format('Y-m-d H:i:s'))
            ->with("patient", "ward", "bed")->get();

        $data['redirect'] = false;
        if(request()->has('patient_id')){
            $data['redirect'] = true;
        }

        $data['service_type'] = ServiceType::whereIsActive(1)->get();
        return view("patients.in_patient_payment", $data);
    }



    public function in_patient_admission_list()
    {


        $data['title'] = "Discharged Patient List";
        $data["procedure_type"] = ProcedureType::whereIsActive(1)->get();
        $data["consultant"] = Consultants::whereIsActive(1)->get();
        $data['type'] = "hospital_patient";
        return view("patients.in_patient_admission_list", $data);
    }


    public function in_patient_discharged_listing()
    {

        $patients = InPatientAdmission::where(["is_active" => 1])
            ->whereIn("admission_status",["Discharged","Reffered","Canceled"])
            ->with("patient", "ward", "bed", 'consultant_procedure.procedure', 'consultant','sub_consultant')

            ->when(request()->from_date, function ($query) {
                $fromDate = Carbon::parse(request()->from_date)->endOfDay();
                $query->where('discharge_date','>=',date("Y-m-d",strtotime($fromDate)));
            })
            ->when(request()->to_date, function ($query) {
                $toDate = Carbon::parse(request()->to_date)->endOfDay();
                $query->where('discharge_date','<=',date("Y-m-d",strtotime($toDate)));
            })
            /*->when(request()->procedure_type_id, function ($query) {
                //  dd("here");
                $query->where(['patient_admissions.procedure_type_id'=> request()->procedure_type_id]);
            })*/
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
                $buttons =  '<a target="_blank" href="' . route('pos.print_in_patient_admission', [$patient->id]) . '" class="btn btn-primary btn-sm "><i class="bx bx-printer tf-icons"></i></a>
                     
                      <a href="' . route('pos.primary_discharge', [$patient->id]) . '"  class="btn btn-sm btn-success edit_record"><i class="bx bxs-show tf-icons"></i></a>
                        ';
                if ($patient->procedure_type_id == 6 || $patient->procedure_type_id == 208 || $patient->procedure_type_id == 211) {
                    $buttons = $buttons . '<a  href="' . route('pos.patient_baby', [$patient->patient_id, $patient->id]) . '"  class="btn btn-primary btn-sm mt-2">View Baby Information</a>';
                }
                return $buttons;
            })
            ->addColumn("alert", function ($patient) {
               /* $res = (new PatientExpenseController())->getAdmissionDetails1($patient);
                return  $res['alert'];*/
               return 0;
            })
            ->addColumn("totalCost", function ($patient) {
               /* $res = (new PatientExpenseController())->getAdmissionDetails1($patient);
                return  $res['totalCost'];*/
                return 0;
            })
            ->addColumn("balance", function ($patient)  {
                /*$res = (new PatientExpenseController())->getAdmissionDetails1($patient);
                return  $res['balance'];*/
                return 0;
            })
            ->rawColumns(["alert","totalCost","balance","edit_admission_date", "actions"])
            ->make(true);
    }


    function primary_discharge($admission_id='')
    {
        $admission = InPatientAdmission::with("patient", "ward", "bed", 'consultant_procedure.procedure', 'consultant')
            ->where("id",$admission_id)
            ->first();


        $procedure_id = $admission->consultant_procedure_id;
        $consultant_id = $admission->consultant_id;

       // dd($consultant_id,$procedure_id);

        //dd($procedure_id,$consultant_id);

        $admissionDate = Carbon::parse($admission->admission_date);
        $dischargeDate = Carbon::parse($admission->discharge_date);
        if($admission->discharge_date == '' || $admission->discharge_date == NULL){

            $dischargeDate = Carbon::parse(date("Y-m-d")) ;
        }
        $data['total_days'] = $admissionDate->diffInDays($dischargeDate) + 1;



        $data['admission'] = $admission;
        $data["patient"] = $admission->patient;
        $data['service_type'] = ServiceType::where(["show_in_discharge_form"=>1])->whereIsActive(1)->get();
        $total_service_charges = 0;

        $data['is_service_charges_posted'] = false;
        foreach ($data['service_type'] as $key => $value){

            $value->patient_charges = PatientServiceCharges::where(["patient_id"=>$admission->patient_id,"admission_id"=>$admission->id,"service_type_id"=>$value->id])->where(["is_active"=>1])->first();
            //$value->patient_charges = $this->getServiceRate($consultant_id,$procedure_id,$value->id);

            if($value->patient_charges){
                $data['is_service_charges_posted'] = true;
                $total_service_charges = ($total_service_charges) + ($value->patient_charges->service_rate ?? 0);
            }else{
                $data['is_service_charges_posted'] = false;
                $consultant_charges = $this->getServiceRate($consultant_id,$procedure_id,$value->id);
                //dd($consultant_charges);
                $value->consultant_charges = $consultant_charges;
                $total_service_charges = ($total_service_charges) + ($consultant_charges);
            }
        }
        $data['total_service_charges'] = $total_service_charges + ($admission->consultant_charges);
        $data['investigation'] = $this->patientInvestigationBalance($admission->patient_id,$admission_id);
        $data['pharmacy'] = $this->totalMedicineCost($admission->patient_id,$admission_id);
        $data['total_received_payment'] = $this->totalReceivedPayment($admission->patient_id,$admission_id);
        $data['admission_id'] = $admission_id;


       // $data['service_type'] = ServiceType::whereIsActive(1)->get();

        if($admission->included_medicine){
            return view("patients.primary_discharge_included_medicine", $data);
        }else{
            return view("patients.primary_discharge_without_medicine", $data);
        }


    }

    public function getServiceRate($consultant_id,$procedure_id,$service_type_id)
    {
        $data = DB::table('consultant_procedures')
            ->select('consultant_procedures.*', 'consultant_procedure_pricing.amount as service_amount','service_type.name as service_name')
            ->leftJoin('consultant_procedure_pricing', 'consultant_procedure_pricing.consultant_procedure_id', '=', 'consultant_procedures.id')
            ->leftJoin('service_type', 'service_type.id', '=', 'consultant_procedure_pricing.service_type_id')
            ->where("consultant_procedures.consultant_id",$consultant_id)
            ->where("consultant_procedures.id",$procedure_id)
            ->where("consultant_procedure_pricing.service_type_id",$service_type_id)
            ->first();

        return $data->service_amount ?? 0;
    }

    public function patientInvestigationBalance($patient_id,$admission_id)
    {
       $total_amount =  PatientInvestigation::where(["patient_id"=>$patient_id,"admission_id"=>$admission_id])->selectRaw('SUM(sale_price - discount_amount) as total')->first();
       $received_amount =  PatientInvestigationPayment::where(["patient_id"=>$patient_id,"admission_id"=>$admission_id])->sum('amount');
       return ["total"=>$total_amount->total ?? 0,"received"=>$received_amount];
    }

    public function totalMedicineCost($patient_id,$admission_id)
    {
        $total_amount =  Sale::where(["patient_id"=>$patient_id,"admission_id"=>$admission_id])->selectRaw('SUM(TotalSale - Discount) as total')->first();
        $received_amount =  SalePayment::where(["patient_id"=>$patient_id,"admission_id"=>$admission_id])->sum('amount');
        return ["total"=>$total_amount->total ?? 0,"received"=>$received_amount];
    }

    public function totalReceivedPayment($patient_id,$admission_id)
    {
        $advance =  PatientPayment::where(["patient_id"=>$patient_id,"admission_id"=>$admission_id])->where("payment_type","advance")->sum('amount');
        $return =  PatientPayment::where(["patient_id"=>$patient_id,"admission_id"=>$admission_id])->where("payment_type","return_to_patient")->sum('amount');

        return ($advance) - ($return);
    }


    
    







    public function save_patient_payment()
    {
        $data = request()->except(["id","_token"]);
        $data['created_at'] = date("Y-m-d H:i:s");
        $data['created_by'] = auth()->user()->id;
        PatientPayment::updateOrCreate(
            ["id" => request()->id],
            $data
        );

        return response()->json([
            "status" => true,
            "message" => "Record save successfully."
        ]);
    }

    public function list_in_admitted_patients()
    {
        $patients = InPatientAdmission::where(["is_active"=> 1, "admission_status"=> "Admit"])
            //->where(["admission_status"=>"Admit"])
            ->with("patient", "ward", "bed", 'consultant_procedure.procedure', 'consultant')
            ->where("patient_type","hospital_patient");
        return DataTables::of($patients)

            ->addColumn("edit_admission_date", function ($patient) {
                //dd(date("Y-m-d", strtotime($patient->admission_date)));
                return  date("Y-m-d", strtotime($patient->admission_date));
            })
            ->addColumn("actions", function ($patient) {
                return ' 
                        <a href="' . route('pos.primary_discharge', [$patient->id]) . '" class="btn btn-danger btn-sm">Discharge</button>
                       
                        ';
            })
            ->rawColumns(["edit_admission_date", "actions"])
            ->make(true);
    }

    public function calculate_patient_discharge_amount()
    {

        $patient_admission = InPatientAdmission::where("id",request()->admission_id)->first();
        if(request()->has('consultant_charges') && $patient_admission->consultant_charges != request()->consultant_charges){
            $consultant_charges = request()->consultant_charges;
            $consultant_share_amount = ($consultant_charges) * (($patient_admission->consultant_share ?? 0)/100);
            InPatientAdmission::where("id",request()->admission_id)->update(
                [
                    "consultant_charges"            => request()->consultant_charges,
                    "consultant_share_amount"       => $consultant_share_amount,
                    "updated_by"            => auth()->user()->id,
                    "updated_at"            => date("Y-m-d H:i:s"),
                ]
            );
        }
        if(request()->has('procedure_rate') && $patient_admission->procedure_rate != request()->procedure_rate){
            InPatientAdmission::where("id",request()->admission_id)->update(
                [
                    "procedure_rate"            => request()->procedure_rate,
                    "updated_by"            => auth()->user()->id,
                    "updated_at"            => date("Y-m-d H:i:s"),
                ]
            );
        }
       // dd($patient_admission);

        $service_charges_id = request()->service_charges_id;
        $service_charges_amount = request()->service_charges_amount;
        $data = [];
        $user_role = getUserRole();

        foreach ($service_charges_id as $key => $value){
            $service = ServiceType::where("id",$service_charges_id[$key])->first();
            $data= [
                "patient_id" => request()->patient_id,
                "admission_id" => request()->admission_id,
                "service_type_id" => $service_charges_id[$key],
                "actual_price" => $service->price ?? 0,
                "service_rate" => $service_charges_amount[$key],
                "service_date" => date("Y-m-d H:i:s-"),
                "created_at" => date("Y-m-d H:i:s-"),
            ];
            //---- its because closing amount goes to super admin when created_by updated on super admin id ---/
            if($user_role != "Super Admin"){
                $data['created_by'] = auth()->user()->id;
            }
            PatientServiceCharges::updateOrCreate(
                ["patient_id" => request()->patient_id, "admission_id" => request()->admission_id, "service_type_id" => $service_charges_id[$key],"is_active"=>1],
                $data
            );
        }
        return redirect()->back()->with("success","Record Saved Successfully");
    }


    public function primary_discharge_in_patient()
    {
        $total_payment_paid_by_patient = request()->total_payment_paid_by_patient;
        $investigations = request()->investigations ?? 0;
        $pharmacy_sale = request()->pharmacy_sale ?? 0;

        if($investigations > 0){
            PatientInvestigationPayment::create([
                "patient_id"=>request()->patient_id,
                "admission_id"=>request()->admission_id,
                "amount"=>$investigations,
                "remarks"=>"payment_received_on_discharge",
                "created_by"=>auth()->user()->id,
                "created_at"=>date("Y-m-d H:i:s")
            ]);
        }
        if($pharmacy_sale > 0){
            SalePayment::create([
                "patient_id"=>request()->patient_id,
                "admission_id"=>request()->admission_id,
                "amount"=>$pharmacy_sale,
                "remarks"=>"payment_received_on_discharge",
                "created_by"=>auth()->user()->id,
                "created_at"=>date("Y-m-d H:i:s")
            ]);
        }


        if($total_payment_paid_by_patient > 0){
            InPatientAdmission::where(["patient_id"=>request()->patient_id,"id"=>request()->admission_id])
                ->update([
                    "total_amount_received_from_patient"=>$total_payment_paid_by_patient,
                    "discharge_by"          =>auth()->user()->id,
                    "admission_status"      =>"Discharged",
                    "discharge_date"        =>date("Y-m-d H:i:s"),
                ]);
        }

        return ["status"=>true,"message"=>"Patient Discharged Successfully"];
    }


    public function get_in_admission_investigations($patient_id = '', $admission_id = '')
    {
        // dd("here");
        $patients = PatientInvestigation::where("is_active", 1)->with("investigation")
            ->when($patient_id, function ($query) use ($patient_id) {
                $query->where('patient_id', $patient_id);
            })
            /*->when(session('store_id'),function ($q){
                $q->where('store_id',session('store_id'));
            })*/
            ->when($admission_id, function ($query) use ($admission_id) {
                $query->where('admission_id', $admission_id);
            })
            ->orderBy("id", "desc");
        return DataTables::of($patients)
            ->addColumn("actions", function ($patient) {
                $button = '';
                if ($patient->status == 1) {
                    $button = $button . '<a target="_blank" href="' . route('pos.print_inv_result', [$patient->id]) . '"  data-details=\'' . $patient . '\'  class="btn btn-sm btn-success print_inv_record"><i class="tf-icons bx bx-printer"></i></a>';
                }else{
                  //  $button = $button . '<button data-id="' . $patient->id . '" class="btn btn-danger btn-sm delete_record"><i class="bx bx-trash tf-icons"></i></button>';
                }


                return $button;
            })

            ->addColumn("print_invoice_number", function ($patient) {
                return '<a target="_blank" href="' . route('pos.print_hospital_lab_invoice', [$patient->invoice_no]) . '">' . $patient->invoice_no . '</a>';
            })
            ->rawColumns(["print_invoice_number", "actions"])
            ->make(true);
    }


    public function updateInAdmissionDetails($admission_id)
    {


        $admission = InPatientAdmission::with("patient", "ward", "bed", 'consultant_procedure.procedure', 'consultant','sub_consultant')->where(["id"=>$admission_id])->first();
        if($admission->patient_type == "hospital_patient"){

        }else{

        }
        $data['procedure_amount1'] = $admission->procedure_rate ?? 0;
        $data['procedure_amount'] = $admission->procedure_rate ?? 0;
        $data["is_medical_case"] = false;
        $data["daysDifference"] = 0;

        $data['consultant_share'] = $admission->consultant_share_amount ?? 0;
        // if($admission->procedure_rate == 0){
        $data['procedure_amount1'] = $admission->procedure_rate ?? 0;
        $data['procedure_amount'] = $admission->procedure_rate ?? 0;
        //}
        /*if($admission->consultant_share == 0){
            $data['consultant_share'] = $admission->consultant->share_percentage;
        }*/

        if(($admission) && $admission->consultant_procedure->procedure->type == "Medical"){

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
        /*$share_amount = 0;
        if($data['consultant_share'] !='' && $data['consultant_share'] > 0){
            $percentage = $data['consultant_share']/100;
            $share_amount = ($data['procedure_amount']) * ($percentage);
        }*/



        $data['consultant_share_amount'] = $admission->consultant_share_amount;

        $investigations = PatientInvestigation::with('subCategory')->where(["admission_id"=>$admission_id,"is_active"=>1])->get();
        $investigationAmount = 0;
        foreach ($investigations as $key => $value){
            $investigationAmount += $value->subCategory->sale_price;
        }

        $data['investigation_amount'] = $investigationAmount;
        $data['service_charges'] = PatientServiceCharges::where(["admission_id"=>$admission_id,"is_active"=>1])->sum('service_rate');
        $patient_id = $admission->patient_id ?? 0;
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

        $updateData = [
            "consultant_share_amount" => $admission->consultant_share_amount,
            "investigation_cost"        =>$investigationAmount,
            "service_charges_cost"      =>$data['service_charges'],
            "medicine_cost"      =>$totalAmount,
            "totalCost"      =>$total_cost,
            "balance"      =>$balance,
        ];
       // dd($updateData);

        InPatientAdmission::where("id",$admission_id)->update($updateData);

        return $data;


    }

    public function print_admitted_patient_medicine_list($admission_id)
    {
        $data['admission'] = InPatientAdmission::with(['patient','consultant_procedure.procedure'])->where("id",$admission_id)->first();
        //dd($data['admission']->consultant_procedure->procedure->name);
        $sale = Sale::where("admission_id",$admission_id)->get();
        $total_discount = 0;
        $total_amount = 0;
        foreach ($sale as $key => $value){
            $total_discount = ($total_discount) + ($value->Discount);
            $value->items = SaleDetails::with('product')->where(["SaleID"=>$value->SaleID])->get();

            foreach ($value->items as $key => $value2){
                $value2->product_name = $value2->product->ProductName ?? '';

                $value2->total_amount = (($value2->Quantity) - ($value2->ReturnQuantity)) * ($value2->UnitePrice);
                $total_amount = ($total_amount) + ($value2->total_amount);

            }
        }
        $data['sale'] = $sale;
        $data['total_discount'] = $total_discount;
        $data['total_amount'] = $total_amount;
        return view('reports.print_patient_medicine_list',$data);

    }

    public function in_patient_payments_receiving_bill($admission_id)
    {
        $data['admission'] = InPatientAdmission::with(['patient','consultant_procedure.procedure','createdBy'])->where("id",$admission_id)->first();
        $data['payments'] = PatientPayment::with(["createdBy"])->where("admission_id",$admission_id)->get();
        $advance_amount = $data['admission']->advance_payment;
        $total = $advance_amount;
        foreach($data['payments'] as $key => $value){
            if(strtoupper($value->payment_type) == "ADVANCE"){
                $total = ($total) + ($value->amount);
                $value->amount = $value->amount;
            }else{
                $total = ($total) - ($value->amount);
                $value->amount = "-".$value->amount;

            }

        }
        $data['total'] = $total;
        return view('reports.print_in_patient_payments_receiving_bill',$data);
    }




}
