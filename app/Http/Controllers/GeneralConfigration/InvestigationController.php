<?php

namespace App\Http\Controllers\GeneralConfigration;

use App\Http\Controllers\Controller;
use App\Models\Configuration\InvestigationMainCategory;
use App\Models\Configuration\InvestigationParameter;
use App\Models\Configuration\InvestigationSubCategory;
use App\Models\Configuration\ProcedureType;
use App\Models\Configuration\ServiceType;
use App\Models\Configuration\Ward;
use App\Models\Configuration\WardBed;
use App\Models\ParameterHeading;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class InvestigationController extends Controller
{
    public function add_investigation_category()
    {
        $data["title"] = "Add Investigation Category";
        return view("lab_configuration.investigation_category",$data);
    }

    public function list_investigation_category()
    {
        $res = InvestigationMainCategory::where(["is_active"=>1]);

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

    public function save_investigation_category()
    {
        InvestigationMainCategory::updateOrCreate(
            ["id"=>request()->id],
            request()->except(["id","_token"])
        );
        return ["status"=>true,"message"=>"Record saved successfully"];
    }


    public function add_investigation_sub_category()
    {
        $data["title"] = "Investigation Sub Category";
        $data["inv_main_category"] = InvestigationMainCategory::whereIsActive(1)->get();
        return view("lab_configuration.investigation_sub_category",$data);
    }

    public function list_investigation_sub_category()
    {
        $res = InvestigationSubCategory::with(["main_category"])->where(["is_active"=>1]);

        return DataTables::of($res)
        ->addColumn("is_ict",function($cert){
            return ($cert->is_ict == 1) ? "Yes" : "No";
        })
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
            ->addColumn('test_type', function($cert) {
                $type_name =  ($cert->is_parameter == 1) ? "<a target='_blank' href='".route('pos.add_investigation_parameter',[$cert->id])."'>Parameter</a>" : "Textual";

                return $type_name;
            })
            ->addColumn('report_heading', function($cert) {
                $type_name =  '<a href="javascript:void(0)" class="add_report_heading" data-id="'.$cert->id.'">Add Report Headings</a>';

                return $type_name;
            })
            ->rawColumns(["test_type","report_heading","action"])
            ->make(true);
    }

    public function save_investigation_sub_category()
    {

        $data = request()->except(["id", "_token"]);
        $data["is_ict"] =  request()->is_ict ?? "0";

        InvestigationSubCategory::updateOrCreate(
            ["id"=>request()->id],
            $data
        );
        return ["status"=>true,"message"=>"Record saved successfully"];
    }


    public function add_investigation_parameter($id)
    {
        $data["title"] = "Investigation Parameters";
        $data["parameter_heading"] = ParameterHeading::where(["investigation_sub_category_id"=>$id])->orWhere('id',1)->orderBy("id","asc")->get();
        $data["inv_sub_category"] = InvestigationSubCategory::whereIsActive(1)->whereId($id)->first();
        $data['id'] = $id;
        return view("lab_configuration.investigation_parameter",$data);
    }

    public function list_investigation_parameter($id)
    {

        $res = InvestigationParameter::with(["investigation_sub_category","parameter_heading"])->where(["investigation_sub_category_id"=>$id])->where(["is_active"=>1])->orderBy("index_number","asc");

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

    public function save_investigation_parameter()
    {
        InvestigationParameter::updateOrCreate(
            ["id"=>request()->id],
            request()->except(["id","_token"])
        );
        return ["status"=>true,"message"=>"Record saved successfully"];
    }


    public function save_report_heading()
    {
        ParameterHeading::updateOrCreate(
            ["id"=>request()->id],
            request()->except(["id","_token"])
        );
        return ["status"=>true,"message"=>"Record saved successfully"];
    }




}
