<?php

namespace App\Http\Controllers\ProductConfiguration;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PatientController\PatientAdmissionController;
use App\Models\GenericName;
use App\Models\ItemForm;
use App\Models\ItemMake;
use App\Models\MainCategory;
use App\Models\MedicineType;
use App\Models\SubCategory;
use App\Models\Tax;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class CategoryController extends Controller
{
    public function add_main_category()
    {
        $data["title"] = "Add New Market";
        return view("product_configuration.main_category",$data);
    }

    public function list_main_category()
    {
        $res = MainCategory::where(["is_active"=>1]);

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

    public function save_main_category()
    {

        MainCategory::updateOrCreate(
            ["id"=>request()->id],
            request()->except(["id","_token"])
        );
        return ["status"=>true,"message"=>"Record saved successfully"];
    }

    public function add_sub_category()
    {
        $data["title"] = "Add Sub Category";
        $data['main_category'] = MainCategory::whereIsActive(1)->get();
        return view("product_configuration.sub-category",$data);
    }

    public function list_sub_category()
    {
        $res = SubCategory::with(["main_category"])->where(["is_active"=>1]);


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

    public function save_sub_category()
    {

        SubCategory::updateOrCreate(
            ["id"=>request()->id],
            request()->except(["id","_token"])
        );
        return ["status"=>true,"message"=>"Record saved successfully"];
    }


    public function add_item_form()
    {
        $data["title"] = "Add Item Form";
        return view("product_configuration.item_form",$data);
    }

    public function list_item_form()
    {
        $res = ItemForm::where(["is_active"=>1]);

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

    public function save_item_form()
    {

        ItemForm::updateOrCreate(
            ["id"=>request()->id],
            request()->except(["id","_token"])
        );
        return ["status"=>true,"message"=>"Record saved successfully"];
    }

    public function add_make()
    {
        $data["title"] = "Add Item Make";
        return view("product_configuration.make",$data);
    }

    public function list_make()
    {
        $res = ItemMake::where(["is_active"=>1]);

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

    public function save_make()
    {

        ItemMake::updateOrCreate(
            ["id"=>request()->id],
            request()->except(["id","_token"])
        );
        return ["status"=>true,"message"=>"Record saved successfully"];
    }

    public function add_generic_name()
    {
        $data["title"] = "Add Generic Name";
        return view("product_configuration.generic_name",$data);
    }

    public function list_generic_name()
    {
        $res = GenericName::where(["is_active"=>1]);

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

    public function save_generic_name()
    {

        GenericName::updateOrCreate(
            ["id"=>request()->id],
            request()->except(["id","_token"])
        );
        return ["status"=>true,"message"=>"Record saved successfully"];
    }

    public function add_taxes()
    {
        $data["title"] = "Taxes";
        return view("product_configuration.taxes", $data);
    }

    public function list_taxes()
    {
        $res = Tax::query()->where("is_active", 1);

        return DataTables::of($res)
            ->editColumn('tax_percentage', function ($row) {
                return number_format((float) $row->tax_percentage, 2) . '%';
            })
            ->addColumn('action', function ($cert) {
                $details = json_encode([
                    'id' => $cert->id,
                    'name' => $cert->name,
                    'tax_percentage' => (float) $cert->tax_percentage,
                ]);
                $detailsAttr = htmlspecialchars($details, ENT_QUOTES, 'UTF-8');
                if (in_array(auth()->user()->roles->pluck('name')[0], ["Super Admin", "District Super Admin"])) {
                    $html = '<a href="javascript:void(0)" data-details="'.$detailsAttr.'" class="btn btn-warning btn-icon btn-sm edit_record" data-id="'.$cert->id.'"><i class="tf-icons bx bx-pencil"></i></a>';
                    $html .= '<button class="btn btn-danger btn-icon btn-sm delete_record" data-id="'.$cert->id.'" type="submit"><i class="bx bx-trash tf-icons"></i></button>';
                } else {
                    $html = "";
                }

                return $html;
            })
            ->rawColumns(["action"])
            ->make(true);
    }

    public function save_taxes()
    {
        $validator = Validator::make(request()->all(), [
            'name' => 'required|string|max:255',
            'tax_percentage' => 'required|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $payload = request()->only(['name', 'tax_percentage']);
        $payload['is_active'] = 1;

        $id = (int) request()->input('id', 0);
        if ($id > 0) {
            Tax::where('id', $id)->update($payload);
        } else {
            Tax::create($payload);
        }

        return ["status" => true, "message" => "Record saved successfully"];
    }

    public function zero_tax_percentage()
    {
        $validator = Validator::make(request()->all(), [
            'id' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        Tax::where('id', (int) request('id'))->update(['tax_percentage' => 0]);

        return ['status' => true, 'message' => 'Tax percentage set to zero'];
    }

    public function add_medicine_types()
    {
        $data['title'] = 'Medicine Types';

        return view('product_configuration.medicine_types', $data);
    }

    public function list_medicine_types()
    {
        $res = MedicineType::query();

        return DataTables::of($res)
            ->addColumn('action', function ($cert) {
                $details = json_encode([
                    'id' => $cert->id,
                    'name' => $cert->name,
                    'value' => $cert->value,
                ]);
                $detailsAttr = htmlspecialchars($details, ENT_QUOTES, 'UTF-8');
                if (in_array(auth()->user()->roles->pluck('name')[0], ['Super Admin', 'District Super Admin'])) {
                    $html = '<a href="javascript:void(0)" data-details="'.$detailsAttr.'" class="btn btn-warning btn-icon btn-sm edit_record" data-id="'.$cert->id.'"><i class="tf-icons bx bx-pencil"></i></a>';
                    $html .= '<button class="btn btn-danger btn-icon btn-sm delete_record" data-id="'.$cert->id.'" type="submit"><i class="bx bx-trash tf-icons"></i></button>';
                } else {
                    $html = '';
                }

                return $html;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function save_medicine_types()
    {
        $validator = Validator::make(request()->all(), [
            'name' => 'required|string|max:255',
            'value' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $payload = request()->only(['name', 'value']);
        $id = (int) request()->input('id', 0);
        if ($id > 0) {
            MedicineType::where('id', $id)->update($payload);
        } else {
            MedicineType::create($payload);
        }

        return ['status' => true, 'message' => 'Record saved successfully'];
    }

    public function delete_medicine_type()
    {
        $validator = Validator::make(request()->all(), [
            'id' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        MedicineType::where('id', (int) request('id'))->delete();

        return ['status' => true, 'message' => 'Record deleted successfully'];
    }

    public function get_sub_category()
    {
        return [
            "status" => true,
            "data"  => SubCategory::where(["main_category_id"=>request()->id])->whereIsActive(1)->get()
        ];
    }




    public function deactivate_record()
    {
        DB::table(request()->table)->whereId(request()->id)->update(["is_active"=>0]);
        if(request()->has('admission_id')){
            (new PatientAdmissionController())->updateAdmissionDetails(request()->admission_id);

        }
        return ["status"=>true,"message"=>"Record saved successfully"];

    }

    public function delete_appointment()
    {
        DB::table(request()->table)->whereId(request()->id)->update(["is_active"=>0,"is_sync"=>0,"updated_at"=>date("Y-m-d H:i:s")]);
        return ["status"=>true,"message"=>"Record saved successfully"];

    }

    public function deactivate_in_patient_service_charges()
    {
        DB::table(request()->table)->whereId(request()->id)->update(["is_active"=>0]);
        if(request()->has('admission_id')){
            (new PatientAdmissionController())->updateInAdmissionDetails(request()->admission_id);

        }
        return ["status"=>true,"message"=>"Record saved successfully"];

    }
}
