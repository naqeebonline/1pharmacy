<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Market;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MarketController extends Controller
{
    public function add_new_market()
    {
        $data["title"] = "Add New Market";
        return view("configuration.market",$data);
    }

    public function list_market()
    {
        $res = Market::where(["IsActive"=>1]);

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

    public function save_market()
    {

        Market::updateOrCreate(
            ["id"=>request()->id],
            request()->except(["id","_token"])
        );
        return ["status"=>true,"message"=>"Record saved successfully"];
    }

    public function delete_table_data()
    {
        DB::table(request()->table)->whereId(request()->id)->update(["IsActive"=>0]);
        return ["status"=>true,"message"=>"Record saved successfully"];

    }

    public function delete_table_data_2()
    {
        DB::table(request()->table)->whereId(request()->id)->update(["is_active"=>0]);
        return ["status"=>true,"message"=>"Record saved successfully"];

    }

    public function delete_IsActive()
    {
        DB::table(request()->table)->where("ProductID",request()->id)->update(["IsActive"=>0]);
        return ["status"=>true,"message"=>"Record saved successfully"];

    }
}
