<?php

namespace App\Http\Controllers\ProductConfiguration;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductKit;
use Yajra\DataTables\Facades\DataTables;

class ProductKitController extends Controller
{
    public function product_kit($id)
    {

        $data["products"] = Product::where(["IsActive" => 1])->get();
        // return $data;
        return view('product_configuration.product_kit', $data);
    }


    public function product_kit_save()
    {

        $is_exist = ProductKit::whereIsActive(1)->where(["product_id" => request("product_id"), "product_main_id"=> request("product_main_id")])->first();

        if ($is_exist) {
            return response()->json([
                "status" => false,
                "message" => "Product  Already Exist"
            ]);
        }

        ProductKit::create([
            "product_id" => request("product_id"),
            "product_main_id" => request("product_main_id"),
            "kit_id" => request("kit_id"),
            "qty" => request("qty"),
            "is_ctive" => 1
        ]);

        return response()->json([
            "status" => true,
            "message" => "Product  Added Successfully"
        ]);
    }


    public function product_kit_list($product_main_id)
    {
        $product_kits = ProductKit::where(["is_active" => 1, "product_main_id"=> $product_main_id])->with("product")->latest();
        return DataTables::of($product_kits)

            ->addColumn("actions", function ($product_kit) {
                return ' 
                        <a href="javascript:void(0)" data-id="' . $product_kit->id . '" class="btn btn-danger btn-sm delete_record">Delete</button>';
            })
            ->rawColumns(["edit_admission_date", "actions"])
            ->make(true);
    }
}
