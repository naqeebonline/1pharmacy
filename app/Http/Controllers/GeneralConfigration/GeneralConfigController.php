<?php

namespace App\Http\Controllers\GeneralConfigration;

use App\Http\Controllers\Controller;
use App\Models\Configuration\ConsultantProcedure;
use App\Models\Configuration\ConsultantProcedurePricing;
use App\Models\Configuration\Consultants;
use App\Models\Configuration\ProcedureType;
use App\Models\Configuration\ServiceType;
use App\Models\Configuration\Ward;
use App\Models\Configuration\WardBed;
use App\Models\Finance\FinanceHead;
use App\Models\Patient\PatientLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class GeneralConfigController extends Controller
{
    public function add_ward()
    {
        $data["title"] = "Add New Ward";
        return view("general_configuration.ward",$data);
    }

    public function list_ward()
    {
        $res = Ward::where(["is_active"=>1]);

        return DataTables::of($res)
            ->addColumn('action', function($cert) {
                $details = json_encode($cert);
                if(in_array(auth()->user()->roles->pluck('name')[0],["Super Admin","District Super Admin"])){
                    $html = '<a href="javascript:void(0)" data-details=\''.$details.'\' class="btn btn-warning btn-icon btn-sm edit_record" data-name="'.$cert->name.'" data-id="'.$cert->id.'"><i class="tf-icons bx bx-pencil"></i></a>';
                    $html .= '<button class="btn btn-danger btn-icon btn-sm delete_record" data-id="'.$cert->id.'" type="submit"><i class="bx bx-trash tf-icons"></i></button>';
                }else{
                    $html = "";
                }
                return $html;
            })
            ->rawColumns(["action"])
            ->make(true);
    }

    public function save_ward()
    {
        Ward::updateOrCreate(
            ["id"=>request()->id],
            request()->except(["id","_token"])
        );
        return ["status"=>true,"message"=>"Record saved successfully"];
    }

    public function add_bed()
    {
        $data["title"] = "Add New Bed";
        $data['wards'] = Ward::whereIsActive(1)->get();
        return view("general_configuration.bed",$data);
    }

    public function list_bed()
    {
        $res = WardBed::with(["ward"])->where(["is_active"=>1]);

        return DataTables::of($res)
            ->addColumn('action', function($cert) {
                $details = json_encode($cert);
                if(in_array(auth()->user()->roles->pluck('name')[0],["Super Admin","District Super Admin"])){
                    $html = '<a href="javascript:void(0)" data-details=\''.$details.'\' class="btn btn-warning btn-icon btn-sm edit_record" data-name="'.$cert->name.'" data-id="'.$cert->id.'"><i class="tf-icons bx bx-pencil"></i></a>';
                    $html .= '<button class="btn btn-danger btn-icon btn-sm delete_record" data-id="'.$cert->id.'" type="submit"><i class="bx bx-trash tf-icons"></i></button>';
                }else{
                    $html = "";
                }
                return $html;
            })
            ->rawColumns(["action"])
            ->make(true);
    }

    public function save_bed()
    {

        WardBed::updateOrCreate(
            ["id"=>request()->id],
            request()->except(["id","_token"])
        );
        return ["status"=>true,"message"=>"Record saved successfully"];
    }

    public function add_service_type()
    {
        $data["title"] = "Add Service Type";
        return view("general_configuration.service_type",$data);
    }

    public function list_service_type()
    {
        $res = ServiceType::where(["is_active"=>1]);

        return DataTables::of($res)
            ->addColumn('action', function($cert) {
                $details = json_encode($cert);
                if(in_array(auth()->user()->roles->pluck('name')[0],["Super Admin","District Super Admin"])){
                    $html = '<a href="javascript:void(0)" data-details=\''.$details.'\' class="btn btn-warning btn-icon btn-sm edit_record" data-name="'.$cert->name.'" data-id="'.$cert->id.'"><i class="tf-icons bx bx-pencil"></i></a>';
                    $html .= '<button class="btn btn-danger btn-icon btn-sm delete_record" data-id="'.$cert->id.'" type="submit"><i class="bx bx-trash tf-icons"></i></button>';
                }else{
                    $html = "";
                }
                return $html;
            })
            ->rawColumns(["action"])
            ->make(true);
    }

    public function print_service_type()
    {
        $serviceTypes = ServiceType::where(['is_active' => 1])
            ->orderBy('name')
            ->get();

        return view('general_configuration.service_type_print', [
            'title' => 'Akakhel Medical Complex - Service Type Report',
            'hospitalName' => 'Akakhel Medical Complex',
            'reportTitle' => 'Service Type Report',
            'contactPhone' => '03149465659',
            'contactEmails' => ['naqeebonline@gmail.com', 'info@amch.org.pk'],
            'serviceTypes' => $serviceTypes,
            'generated_at' => now(),
        ]);
    }

    public function save_service_type()
    {
        $code = str_replace(' ', '_', request()->name);
        $finance_head = FinanceHead::where("description",$code)->first();
        $finance_head_id = "";

        $type = "income";
        if($finance_head){
            $finance_head_id = $finance_head->id;
            $code = $finance_head->description;

        }else{
            $create_head = [
                "name" => request()->name,
                "type"  => $type,
                "description" => $code
            ];
            $finance_head = FinanceHead::create($create_head);
            $finance_head_id = $finance_head->id;
        }

        $data = request()->except(["id","_token"]);
        $data['finance_head_id'] = $finance_head_id;
        ServiceType::updateOrCreate(
            ["id"=>request()->id],
            $data
        );
        return ["status"=>true,"message"=>"Record saved successfully"];
    }

    public function add_procedure_type()
    {
        $data["title"] = "Add Procedure Type";
        return view("general_configuration.procedure_type",$data);
    }



    public function list_procedure_type()
    {
        $res = ProcedureType::where(["is_active"=>1]);

        return DataTables::of($res)
            ->addColumn('action', function($cert) {
                $details = json_encode($cert);
                if(in_array(auth()->user()->roles->pluck('name')[0],["Super Admin","District Super Admin"])){
                    $html = '<a href="javascript:void(0)" data-details=\''.$details.'\' class="btn btn-warning btn-icon btn-sm edit_record" data-name="'.$cert->name.'" data-id="'.$cert->id.'"><i class="tf-icons bx bx-pencil"></i></a>';
                    $html .= '<button class="btn btn-danger btn-icon btn-sm delete_record" data-id="'.$cert->id.'" type="submit"><i class="bx bx-trash tf-icons"></i></button>';
                }else{
                    $html = "";
                }
                return $html;
            })
            ->rawColumns(["action"])
            ->make(true);
    }

    public function save_procedure_type()
    {
        //dd(request()->all());
        ProcedureType::updateOrCreate(
            ["id"=>request()->id],
            request()->except(["id","_token"])
        );
        return ["status"=>true,"message"=>"Record saved successfully"];
    }


    public function add_locations()
    {
        $data["title"] = "Add New Locations";
        return view("general_configuration.locations",$data);
    }

    public function list_locations()
    {
        $res = PatientLocation::where(["is_active"=>1]);

        return DataTables::of($res)
            ->addColumn('action', function($cert) {
                $details = json_encode($cert);
                if(in_array(auth()->user()->roles->pluck('name')[0],["Super Admin","District Super Admin"])){
                    $html = '<a href="javascript:void(0)" data-details=\''.$details.'\' class="btn btn-warning btn-icon btn-sm edit_record" data-name="'.$cert->name.'" data-id="'.$cert->id.'"><i class="tf-icons bx bx-pencil"></i></a>';
                    $html .= '<button class="btn btn-danger btn-icon btn-sm delete_record" data-id="'.$cert->id.'" type="submit"><i class="bx bx-trash tf-icons"></i></button>';
                }else{
                    $html = "";
                }
                return $html;
            })
            ->rawColumns(["action"])
            ->make(true);
    }

    public function save_locations()
    {
        PatientLocation::updateOrCreate(
            ["id"=>request()->id],
            request()->except(["id","_token"])
        );
        return ["status"=>true,"message"=>"Record saved successfully"];
    }


    public function add_consultant_procedure()
    {
        $data["title"] = "Add Procedure Type";
        $data['consultant'] = Consultants::where("is_active",1)->get();
        $data['procedure_type'] = ProcedureType::where("is_active",1)->get();
        return view("general_configuration.add_consultant_procedure",$data);
    }



    public function list_consultant_procedure()
    {
        $res = ConsultantProcedure::with(["consultant","procedure"])->where(["is_active"=>1])->orderBy("id","desc");

        return DataTables::of($res)
            ->addColumn('action', function($cert) {
                $details = json_encode($cert);
                if(in_array(auth()->user()->roles->pluck('name')[0],["Super Admin","District Super Admin"])){
                    $html = '<a href="javascript:void(0)" data-details=\''.$details.'\' class="btn btn-warning btn-icon btn-sm edit_record" data-name="'.$cert->name.'" data-id="'.$cert->id.'"><i class="tf-icons bx bx-pencil"></i></a>';
                    $html .= '<button class="btn btn-danger btn-icon btn-sm delete_record" data-id="'.$cert->id.'" type="submit"><i class="bx bx-trash tf-icons"></i></button>';
                }else{
                    $html = "";
                }
                return $html;
            })
            ->addColumn('pricing', function($cert) {
                $details = json_encode($cert);
                $html = '<a class="btn btn-danger btn-sm" target="_blank" href="'.route('pos.consultant_procedure_pricing',[$cert->id]).'" data-id="'.$cert->id.'">Add Pricing</a>';
                return $html;
            })
            ->rawColumns(["action","pricing"])
            ->make(true);
    }

    public function save_consultant_procedures()
    {
        $id = request()->id;

        $exists = ConsultantProcedure::where('consultant_id', request()->consultant_id)
            ->where('procedure_type_id', request()->procedure_type_id)
            ->where('is_active', 1)
            ->when($id, function ($query) use ($id) {
                return $query->where('id', '!=', $id); // Skip current ID if editing
            })
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'This procedure already exists for the selected consultant.'
            ], 422);
        }

        ConsultantProcedure::updateOrCreate(
            ['id' => $id],
            request()->except(['id', '_token'])
        );

        return response()->json([
            'status' => true,
            'message' => 'Record saved successfully.'
        ]);
    }

    public function consultant_procedure_pricing($consultant_procedure_id)
    {
        $data["title"] = "Procedure Pricing";
        $data["consultant_procedure_id"] = $consultant_procedure_id;
        $data['consultant_procedure'] = ConsultantProcedure::with(["procedure","consultant"])->where(["id"=>$consultant_procedure_id])->first();
        $data['service_type'] = ServiceType::where(["show_in_discharge_form"=>1])->whereIsActive(1)->get();
        $total_pricing = 0;
        foreach ($data['service_type'] as $key => $value){
            $value->patient_charges = ConsultantProcedurePricing::where(["consultant_procedure_id"=>$consultant_procedure_id,"service_type_id"=>$value->id])->first();
            $total_pricing = ($total_pricing) + ($value->patient_charges->amount ?? 0);
        }
        $data['total_pricing'] = $total_pricing;

        //dd($data['service_type']);
        return view("general_configuration.consultant_procedure_pricing",$data);
    }

    public function save_consultant_procedure_pricing()
    {

        $service_charges_id = request()->service_charges_id;
        $service_charges_amount = request()->service_charges_amount;
        foreach ($service_charges_id as $key => $value){
            $data = [
                "consultant_procedure_id" => request()->consultant_procedure_id,
                "service_type_id"         => $service_charges_id[$key],
                "amount"         => $service_charges_amount[$key],
            ];
            ConsultantProcedurePricing::updateOrCreate(
                ["consultant_procedure_id" => request()->consultant_procedure_id, "service_type_id" => $service_charges_id[$key]],
                $data
            );
        }

        return redirect()->back()->with("success","Pricing Saved Successfully");
    }

    public function get_consultant_procedures()
    {
        $consultant_id = request()->consultant_id;
        $data = DB::table('consultant_procedures')
            ->select('consultant_procedures.*', 'procedure_type.name as procedure_name')
            ->leftJoin('procedure_type', 'procedure_type.id', '=', 'consultant_procedures.procedure_type_id')
            ->where("consultant_id",request()->consultant_id)
            ->get();
        return ["status" => true,"data"=>$data];
    }


}
