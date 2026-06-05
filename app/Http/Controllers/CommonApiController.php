<?php

namespace App\Http\Controllers;

use App\Models\Circle;
use App\Models\Districts;
use App\Models\FacilityType;
use App\Models\PolicePost;
use App\Models\PoliceStation;
use App\Models\Sensitivity;
use App\Models\Tehsils;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;



class CommonApiController extends Controller
{
    public function sensitivity($id=null)
    {
        if($id){
            $data = Sensitivity::whereId($id)->first();
            return response()->json(['error' => false, 'message' => "data found","data"=>$data],200);
        }else{
            $data = Sensitivity::get();
            return response()->json(['error' => false, 'message' => "data found","data"=>$data],200);
        }


    }

    public function facilityType($id=null)
    {

        $district_id = $_GET['district_id'] ?? "";
        if($id){
            $data = FacilityType::when($district_id, function ($q) use ($district_id) {
                return $q->where(["district_id"=>$district_id]);
            })->whereId($id)->first();
            return response()->json(['error' => false, 'message' => "data found","data"=>$data],200);
        }else{
            $data = FacilityType::when($district_id, function ($q) use ($district_id) {
                return $q->where(["district_id"=>$district_id]);
            })->get();
            return response()->json(['error' => false, 'message' => "data found","data"=>$data],200);
        }

    }

    public function policeStations($id=null)
    {
        $district_id = $_GET['district_id'] ?? "";
        if($id){
            $data = PoliceStation::whereId($id)->first();
            $district = Districts::select(["id","title"])->whereId($data->district_id)->first();
            return response()->json(['error' => false, 'message' => "data found","data"=>$data,"district"=>$district],200);
        }else{
            $data = PoliceStation::when($district_id, function ($q) use ($district_id) {
                return $q->where(["district_id"=>$district_id]);
            })->get();
            return response()->json(['error' => false, 'message' => "data found","data"=>$data],200);
        }

    }



    public function circles($id=null)
    {
            $id = request()->id ?? "";
            $district_id = request()->district_id ?? "";

            $data = Circle::when(($id),function ($q)use($id){
                $q->where("id",$id);
            })->when(($district_id),function ($q)use($district_id){
                $q->where("district_id",$district_id);
            })->get();

            return response()->json(['error' => false, 'message' => "data found","data"=>$data],200);


    }


    public function districts($id=null)
    {
        if($id){
            $data = Districts::whereId($id)->first();
            return response()->json(['error' => false, 'message' => "data found","data"=>$data],200);
        }else{
            $data = Districts::get();
            return response()->json(['error' => false, 'message' => "data found","data"=>$data],200);
        }

    }

    public function tehsils($district_id=null,$tehsil_id=null)
    {
        if($tehsil_id){
            $data = Tehsils::whereDistrictId($district_id)->whereId($tehsil_id)->first();
            return response()->json(['error' => false, 'message' => "data found","data"=>$data],200);
        }else{
            $data = Tehsils::whereDistrictId($district_id)->get();
            return response()->json(['error' => false, 'message' => "data found","data"=>$data],200);
        }

    }

    public function getRank()
    {
        $res = array(
            array(
                "id" => 1,
                "name" =>"Constable"
            ),
            array(
                "id" => 2,
                "name" =>"Head Constable"
            ),
            array(
                "id" => 3,
                "name" =>"ASI"
            ),
            array(
                "id" => 4,
                "name" =>"SI"
            ),
            array(
                "id" => 5,
                "name" =>"Inspector"
            ),
            array(
                "id" => 6,
                "name" =>"DSP"
            ),
            array(
                "id" => 7,
                "name" =>"SP"
            ),
            array(
                "id" => 8,
                "name" =>"SSP"
            ),
            array(
                "id" => 9,
                "name" =>"DIG"
            )
        );
        return response()->json(['error' => false, 'message' => "data found","data"=>$res],200);

    }

    public function getRankForWeb()
    {
        $res = array(
            array(
                "id" => 1,
                "name" =>"Constable"
            ),
            array(
                "id" => 2,
                "name" =>"Head Constable"
            ),
            array(
                "id" => 3,
                "name" =>"ASI"
            ),
            array(
                "id" => 4,
                "name" =>"SI"
            ),
            array(
                "id" => 5,
                "name" =>"Inspector"
            ),
            array(
                "id" => 6,
                "name" =>"DSP"
            ),
            array(
                "id" => 7,
                "name" =>"SP"
            ),
            array(
                "id" => 8,
                "name" =>"SSP"
            ),
            array(
                "id" => 9,
                "name" =>"DIG"
            )
        );
        return $res;

    }

    public function getVehicleType($id=null)
    {
        $data = VehicleType::get();
        return response()->json(['error' => false, 'message' => "data found","data"=>$data],200);

    }
}
