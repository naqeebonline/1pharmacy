<?php

namespace App\Http\Controllers\PatientController;

use App\Http\Controllers\Controller;
use App\Models\Machine\MachineCategory;
use App\Models\Machine\MachinePatient;
use App\Models\Machine\MachineShift;
use App\Models\Patient\Patient;
use Yajra\DataTables\Facades\DataTables;

class MachinePatientController extends Controller
{

    public function machine_patient()
    {
        $data["patients"] = Patient::whereIsActive(1)->get();
        $data["shifts"] = MachineShift::get();
        $data["machine_categories"] = MachineCategory::get();
        return view('patients.machine_patients', $data);
    }

    function get_machine_category()
    {

        
        $machine_category = MachineCategory::find(request()->machine_category_id);

        $machine_patients = MachinePatient::whereIsActive(1)->where(["day" => request()->day, "machine_shift_id" => request()->machine_shift_id, "machine_category_id" => request()->machine_category_id])->count();

        if ($machine_patients >= $machine_category->machine) {
            return response()->json([
                "code" => 200,
                "avaliable_slots" => 0,
                "message" => "Slot not available"
            ]);
        }else{
            return response()->json([
                "code" => 200,
                "avaliable_slots" => ($machine_category->machine) - ($machine_patients),
                "message" => "Slot available"
            ]);
        }
    }

    function store_machine_patient()
    {
        MachinePatient::updateOrCreate(
            ["id" => request()->id],
            request()->except(["_token", "id"])
        );
    }

    function machine_patient_list()
    {
        $machine_patients = MachinePatient::whereIsActive(1)->with("patient", "machine_shift", "machine_category")
        ->when(request()->day,function($q){
             $q->where("day",request()->day);
        })
        ->when(request()->machine_shift_id,function($q){
             $q->where("machine_shift_id",request()->machine_shift_id);
        })
        ->when(request()->machine_category_id,function($q){
             $q->where("machine_category_id",request()->machine_category_id);
        })
        ->latest()->get();

        return DataTables::of($machine_patients)
            ->addColumn("actions", function ($machine_patient) {
                return '
                
                        <button data-id="' . $machine_patient->id . '" class="btn btn-danger btn-sm delete_record"><i class="bx bx-trash tf-icons"></i></button>
                 
                        ';
            })->rawColumns(["actions"])
            ->make(true);
    }
}
