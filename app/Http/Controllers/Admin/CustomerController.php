<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Finance\FinanceHead;
use App\Models\Market;
use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    public function add_new_customer()
    {

        $data["title"] = "Add New Supplier or Customer";
        $data['market'] = Market::where(["IsActive"=>1])->get()->sortBy('Name');

        return view("configuration.sup_cus_registration",$data);
    }

    public function list_customer()
    {
        $res = Customer::with("market")->where(["IsActive"=>1]);

        return DataTables::of($res)
            ->addColumn('action', function($cert) {
                $details = json_encode($cert);
                if(in_array(auth()->user()->roles->pluck('name')[0],["Super Admin","District Super Admin"])){
                    $html = '<a href="javascript:void(0)" class="btn btn-warning btn-icon btn-sm edit_record" data-details=\''.$details.'\'  data-id="'.$cert->SCID.'"><i class="tf-icons bx bx-pencil"></i></a>';
                    $html .= '<button class="btn btn-danger btn-icon btn-sm delete_record" data-id="'.$cert->SCID.'" type="submit"><i class="bx bx-trash tf-icons"></i></button>';
                }else{
                    $html = "";
                }

                return $html;
            })
            ->addColumn('customType', function($cert) {
               $cert->customType;
                if($cert->Type == 2){
                    return $cert->customType = "Customer";
                }else{
                    return $cert->customType = "Supplier";
                }
            })
            ->rawColumns(["customType","action"])
            ->make(true);
    }

    public function save_customer()
    {

        if(request()->Name == '' || request()->ContactNo == ''){
            echo "invalid";
            exit;
        }
        $finance_head = FinanceHead::where("description",request()->ContactNo)->first();
        $finance_head_id = "";

        $type = "liability";

        if($finance_head){
            $finance_head_id = $finance_head->id;
            $code = $finance_head->description;

        }else{
            $create_head = [
                "name" => "Supplier- ".request()->Name,
                "type"  => $type,
                "parent_id"  => 44, // finanace head parent id consultant payable
                "description" => request()->ContactNo
            ];
            $finance_head = FinanceHead::create($create_head);
            $finance_head_id = $finance_head->id;
        }
        $data = request()->except(["id","_token"]);
        $data['finance_head_id'] = $finance_head_id;

        Customer::updateOrCreate(
            ["SCID"=>request()->id],
            $data
        );
        return ["status"=>true,"message"=>"Record saved successfully"];
    }


}
