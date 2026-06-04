<?php

namespace App\Http\Controllers\Appointments;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PatientController\PatientController;
use App\Models\Appointments\Appointment;
use App\Models\Appointments\OpdType;
use App\Models\Configuration\Consultants;
use App\Models\Configuration\District;
use App\Models\Configuration\ProcedureType;
use App\Models\Configuration\Ward;
use App\Models\Patient\Patient;
use App\Models\Patient\PatientAdmission;
use App\Models\Patient\PatientLocation;
use App\Models\Patient\Relation;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class AppointmentController extends Controller
{
    public function appointment()
    {
        $data['users'] = Users::whereHas('roles', function ($q) {
            $q->where('name', 'Super Admin')->orWhere('name', 'like', '%Receiption User%');
        })->get(["id", "name"]);
        $data["consultants"] = Consultants::where(["is_active" => 1])->get();
        $data["relations"] = Relation::get();
        $data["district"] = District::get();
        $data["locations"] = PatientLocation::get();
        $data["opd_type"] = OpdType::get();

        return view("appointments.appointments", $data);
    }

    public function print_appointment($id)
    {

        $appointment = Appointment::with(["patient", "opd_type", "consultant", "created_by", "location"])->where(["is_active" => 1])
            ->where("id", $id)
            ->first();
        $data["data"] = $appointment;

        return view("PatientReports.print_doctor_slip", $data);
    }

    public function print_package_appointment($id)
    {

        $appointment = Appointment::with(["patient", "opd_type", "consultant", "created_by", "location"])->where(["is_active" => 1])
            ->where("id", $id)
            ->first();
        $data["data"] = $appointment;

        // Get products associated with the OPD type
        if ($appointment && $appointment->opd_type_id) {
            $packageProducts = DB::table('opd_type_products')
                ->join('products', 'opd_type_products.product_id', '=', 'products.ProductID')
                ->leftJoin('item_generic_name', 'products.generic_name_id', '=', 'item_generic_name.id')
                ->where('opd_type_products.opd_type_id', $appointment->opd_type_id)
                
                ->select('products.ProductID', 'products.ProductName', 'item_generic_name.name as generic_name')
                ->orderBy('products.ProductName', 'asc')
                ->get();
            $data["packageProducts"] = $packageProducts;

            // Get investigations associated with the OPD type
            $packageInvestigations = DB::table('opd_type_investigations')
                ->join('investigation_sub_category', 'opd_type_investigations.investigation_sub_category_id', '=', 'investigation_sub_category.id')
                ->where('opd_type_investigations.opd_type_id', $appointment->opd_type_id)
               
                ->select('investigation_sub_category.id', 'investigation_sub_category.name')
                ->orderBy('investigation_sub_category.name', 'asc')
                ->get();
            $data["packageInvestigations"] = $packageInvestigations;
        } else {
            $data["packageProducts"] = collect();
            $data["packageInvestigations"] = collect();
        }

        return view("PatientReports.print_package_appointment", $data);
    }

    public function print_e_prescription($id)
    {
        $appointment = Appointment::with(["patient", "opd_type", "consultant", "created_by", "location"])->where(["is_active" => 1])
            ->where("id", $id)
            ->first();
        $data["data"] = $appointment;

        // Fetch all sales with medications for this appointment
        $sales = \App\Models\Sale::where('appointment_id', $id)
            ->orderBy('SaleID', 'DESC')
            ->get();

        if ($sales->count() > 0) {
            // Fetch sale details for all sales
            $allMedications = [];
            foreach ($sales as $sale) {
                $saleDetails = \App\Models\SaleDetails::with('product')
                    ->where('SaleID', $sale->SaleID)
                    ->get();

                $allMedications[] = [
                    'sale' => $sale,
                    'medications' => $saleDetails
                ];
            }
            $data["sales_data"] = $allMedications;
            $data["sales"] = $sales;
        } else {
            $data["sales_data"] = [];
            $data["sales"] = collect();
        }

        // Fetch HX complaints for this appointment
        $hxComplaint = \App\Models\HxComplaint::where('appointment_id', $id)
            ->where('is_active', 1)
            ->first();
        $data["hx_complaint"] = $hxComplaint;

        // Fetch patient investigations for this appointment
        $investigations = \App\Models\Patient\PatientInvestigation::with('investigation')
            ->where('appointment_id', $id)
            ->get();
        $data["investigations"] = $investigations;

        return view("PatientReports.print_e_prescription", $data);
    }

    public function list_appointments()
    {

        $res = Appointment::with(["patient", "opd_type", "consultant", "created_by_user"])->where(["is_active" => 1])
            ->select('appointments.*')
            ->leftJoin('users', 'appointments.created_by', '=', 'users.id')
            ->when(request()->from_date, function ($q) {
                // dd("here");
                $q->whereDate("appointment_date", ">=", request()->from_date);
            })
            ->when(request()->to_date, function ($q) {
                $q->whereDate("appointment_date", "<=", request()->to_date);
            })
            ->when(request()->opd_type_id, function ($q) {
                $q->where("opd_type_id", request()->opd_type_id);
            })
            ->when(request()->consultant_id, function ($q) {
                $q->where("consultant_id", request()->consultant_id);
            })
            ->when(request()->created_by, function ($q) {
                $q->where("created_by", request()->created_by);
            })
            ->orderBy("id", "desc");

        return DataTables::of($res)
            ->addColumn('actions', function ($cert) {
                $details = json_encode($cert);
                //if(in_array(auth()->user()->roles->pluck('name')[0],["Super Admin","District Super Admin"])){
                $html = "";
                 if ($cert->opd_type && ($cert->opd_type->including_medicine == 1 || $cert->opd_type->including_labs == 1)) {
                    $html .= '<a target="_blank" href="' . route('pos.print_package_appointment', [$cert->id]) . '" class="btn btn-primary btn-icon btn-sm" data-id="' . $cert->id . '" type="submit" title="Print Package Appointment"><i class="bx bx-printer tf-icons"></i></a>&nbsp;&nbsp;';
                }
                 if ($cert->opd_type && $cert->opd_type->including_labs == 1) {
                    $html .= '<button class="btn btn-info btn-icon btn-sm add_investigation_btn" data-appointment-id="' . $cert->id . '" data-patient-id="' . $cert->patient_id . '" data-opd-type-id="' . $cert->opd_type_id . '" type="button" title="Add Investigation"><i class="bx bx-test-tube tf-icons"></i></button>&nbsp;&nbsp;';
                }
                $html .= '<a target="_blank" href="' . route('pos.print_appointment', [$cert->id]) . '" class="btn btn-success btn-icon btn-sm" data-id="' . $cert->id . '" type="submit" title="Print Appointment"><i class="bx bx-printer tf-icons"></i></a>&nbsp;&nbsp;';
                
                // Add Print Package Appointment button if OPD type is package (including_medicine or including_labs)
               
                
                $html .= '<a target="_blank" href="' . route('pos.print_e_prescription', [$cert->id]) . '" class="btn btn-primary btn-icon btn-sm" data-id="' . $cert->id . '" type="submit" title="Print E-Prescription"><i class="bx bx-file tf-icons"></i></a>&nbsp;&nbsp;';
                
                // Add Investigation button if OPD type includes labs
               
                
                if ((getUserRole() == 'Super Admin' || getUserRole() == 'Finance')) {
                    $html .= '<a href="javascript:void(0)" data-details=\'' . $details . '\' class="btn btn-warning btn-icon btn-sm edit_record" data-name="' . $cert->name . '" data-id="' . $cert->id . '"><i class="tf-icons bx bx-pencil"></i></a>&nbsp;&nbsp;';
                    $html .= '<button class="btn btn-danger btn-icon btn-sm delete_record" data-id="' . $cert->id . '" type="submit"><i class="bx bx-trash tf-icons"></i></button>&nbsp;&nbsp;';
                }



                /*}else{
                    $html = "";
                }*/
                return $html;
            })
            ->addColumn('created_by_user', function ($row) {
                return $row->created_by_user->name ?? '';
            })
            ->addIndexColumn()
            ->rawColumns(["actions", "created_by_user"])
            ->make(true);
    }

    public function save_appointments()
    {
        $data = request()->except(['_token', "id"]);

        if (request()->id == 0) {
            $number = (new PatientController())->generateMrNumber();
            $data['mr_no'] = $number;
            $data['regdate'] = request()->regdate . " " . date("H:i:s");
            $data['patient_type'] = "hospital_patient";
        }
        $patient =  Patient::updateOrCreate(
            ["id" => request()->id],
            $data
        );


        $appointment = Appointment::where(["patient_id" => $patient->id, "consultant_id" => request()->consultant_id, "opd_type_id" => request()->opd_type_id])
            ->whereDate('appointment_date', request()->regdate)
            ->where("is_active", 1)
            ->first();
        if ($appointment) {
            // Appointment exists
            return response()->json([
                'status' => 'exist',
                'message' => 'An appointment already exists for this patient with the same consultant on the given date.'
            ], 400);
        }


        $consultant = Consultants::where(["id" => request()->consultant_id])->first();
        $opd_type = OpdType::where(["id" => request()->opd_type_id])->first();
        /*if(request()->opd_type_id == 1){
            $fees = $consultant->general_opd_fee;
            $hospital_share = $consultant->general_opd_fee;
            $consultant_share = 0;
        }

        if(request()->opd_type_id == 2){

            $fees = $consultant->consultant_opd_fee;
            $hospital_share = $consultant->hospital_share;
            $consultant_share = $consultant->consultant_share;
        }

        if(request()->opd_type_id == 3){
            $fees = 0;
            $hospital_share = 0;
            $consultant_share = 0;
        }
        if(request()->opd_type_id == 4){
            $fees = $opd_type->fees;
            $hospital_share = 0;
            $consultant_share = 0;
        }*/
        switch (request()->opd_type_id) {
            case 1:
                $fees = $consultant->general_opd_fee;
                $hospital_share = $consultant->general_opd_hospital_share;
                $consultant_share = $consultant->general_opd_consultant_share;
                break;

            case 2:
                $fees = $consultant->consultant_opd_fee;
                $hospital_share = $consultant->hospital_share;
                $consultant_share = $consultant->consultant_share;
                break;
            case 4:
                $fees = $consultant->er_fee;
                $hospital_share = $consultant->er_hospital_share;
                $consultant_share = $consultant->er_consultant_share;
                break;

            default:
                $fees = $opd_type->fees;
                $hospital_share = $opd_type->fees;
                $consultant_share = 0;
                break;
        }

        //$patients = Appointment::orderBy("id","desc")->first();
        $number = $this->generateAppointmentNumber();

        $data = [
            "patient_id"    => $patient->id,
            "appointment_number" => $number,
            "consultant_id" => request()->consultant_id,
            "opd_type_id"   => request()->opd_type_id,
            "fee"   => $fees,
            "hospital_share"   => $hospital_share,
            "consultant_share"   => $consultant_share,
            "appointment_date"   => request()->regdate . " " . date("H:i:s"),
            "created_by"   => auth()->user()->id,

        ];

        $appointment = Appointment::create($data);
        return response()->json([
            "status" => true,
            "appointment_id" => $appointment->id,
            "message" => "Record save successfully."
        ]);
    }

    public function update_appointment()
    {
        $consultant = Consultants::where(["id" => request()->consultant_id])->first();
        $opd_type = OpdType::where(["id" => request()->opd_type_id])->first();
        $fees = 0;
        $hospital_share = 0;
        $consultant_share = 0;
        /*if(request()->opd_type_id == 1){
            $fees = $opd_type->fees;
            $hospital_share = $opd_type->fees;
            $consultant_share = 0;
        } else if(request()->opd_type_id == 2){
            $fees = $consultant->consultant_opd_fee;
            $hospital_share = $consultant->hospital_share;
            $consultant_share = $consultant->consultant_share;
        } else if(request()->opd_type_id == 3){
            $fees = 0;
            $hospital_share = 0;
            $consultant_share = 0;
        } else(request()->opd_type_id == 4){
            $fees = $opd_type->fees;
            $hospital_share = 0;
            $consultant_share = 0;
        }*/
        switch (request()->opd_type_id) {
            case 1:
                $fees = $consultant->general_opd_fee;
                $hospital_share = $consultant->general_opd_hospital_share;
                $consultant_share = $consultant->general_opd_consultant_share;
                break;

            case 2:
                $fees = $consultant->consultant_opd_fee;
                $hospital_share = $consultant->hospital_share;
                $consultant_share = $consultant->consultant_share;
                break;
            case 4:
                $fees = $consultant->er_fee;
                $hospital_share = $consultant->er_hospital_share;
                $consultant_share = $consultant->er_consultant_share;
                break;
            default:
                $fees = $opd_type->fees;
                $hospital_share = $opd_type->fees;
                $consultant_share = 0;
                break;
        }

        $data = [

            "consultant_id" => request()->consultant_id,
            "opd_type_id"   => request()->opd_type_id,
            "fee"   => $fees,
            "hospital_share"   => $hospital_share,
            "consultant_share"   => $consultant_share,
            "updated_by"   => auth()->user()->id,
            "is_sync"   => 0,

        ];

        // dd($data,request()->id);
        $appointment = Appointment::where(["id" => request()->id])->update($data);
        return response()->json([
            "status" => true,
            "appointment_id" => request()->id,
            "message" => "Record Updated successfully."
        ]);
    }

    public function print_all_appointments($from_date, $to_date, $opd_type_id, $consultant_id, $user_id)
    {

        $res = Appointment::with(["patient", "opd_type", "consultant", "created_by_user"])->where(["is_active" => 1])
            ->when(($from_date && $from_date != 'nill'), function ($q) use ($from_date) {
                // dd("here");
                $q->whereDate("appointment_date", ">=", $from_date);
            })
            ->when(($to_date && $to_date != 'nill'), function ($q) use ($to_date) {
                $q->whereDate("appointment_date", "<=", $to_date);
            })
            ->when($opd_type_id, function ($q) use ($opd_type_id) {
                $q->where("opd_type_id", $opd_type_id);
            })
            ->when($consultant_id, function ($q) use ($consultant_id) {
                $q->where("consultant_id", $consultant_id);
            })
            ->when($user_id, function ($q) use ($user_id) {
                $q->where("created_by", $user_id);
            })
            ->get();

        $data['from_date'] = ($from_date && $from_date != 'nill') ? $from_date : "-";
        $data['to_date'] = ($to_date && $to_date != 'nill') ? $to_date : "-";
        $data['data'] = $res;
        return view("appointments.reports.print_all_appointments", $data);
    }

    function generateAppointmentNumber()
    {
        $year = date('y');  // Last 2 digits of the year, e.g., "25"
        $month = date('m'); // 2-digit month, e.g., "07"

        // Get count of appointments for the current year and month
        $count = Appointment::whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->count();

        $sequence = $count + 1;

        // Sequence should start at 2 digits and grow as needed
        $minLength = 2;
        $dynamicLength = max($minLength, strlen((string)$sequence));
        $paddedSequence = str_pad($sequence, $dynamicLength, '0', STR_PAD_LEFT);

        // Combine year, month, and padded sequence
        return $year . $month . $paddedSequence;
    }

    /**
     * Get investigations for a specific OPD type
     */
    public function get_opd_investigations(Request $request)
    {
        $opdTypeId = $request->opd_type_id;
        $appointmentId = $request->appointment_id;

        $opdType = OpdType::with('investigations')->find($opdTypeId);
        
        if (!$opdType) {
            return response()->json([
                'status' => false,
                'message' => 'OPD Type not found'
            ]);
        }

        // Get existing investigations for this appointment (only active ones)
        $existingInvestigations = \App\Models\Patient\PatientInvestigation::where('appointment_id', $appointmentId)
            ->where('is_active', 1)
            ->get()
            ->keyBy('investigation_sub_category_id');

        $investigations = $opdType->investigations->map(function($inv) use ($existingInvestigations) {
            $existingInv = $existingInvestigations->get($inv->id);
            return [
                'id' => $inv->id,
                'name' => $inv->name,
                'price' => $inv->price,
                'sale_price' => $inv->sale_price,
                'already_added' => $existingInv !== null,
                'invoice_no' => $existingInv ? $existingInv->invoice_no : null
            ];
        });

        return response()->json([
            'status' => true,
            'investigations' => $investigations
        ]);
    }

    /**
     * Save patient investigations
     */
    public function save_patient_investigations(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'patient_id' => 'required|exists:patients,id',
            'investigations' => 'required|array',
            'investigations.*' => 'exists:investigation_sub_category,id'
        ]);

        $appointment = Appointment::find($request->appointment_id);
        $invoiceNo = time() . rand(100, 999); // Generate unique invoice number

        $savedInvestigations = [];
        
        foreach ($request->investigations as $investigationId) {
            $investigation = \App\Models\Configuration\InvestigationSubCategory::find($investigationId);
            
            if (!$investigation) {
                continue;
            }

            // Check if already exists (including deleted ones)
            $existing = \App\Models\Patient\PatientInvestigation::where([
                'appointment_id' => $request->appointment_id,
                'investigation_sub_category_id' => $investigationId
            ])->first();

            if ($existing) {
                // If exists and is deleted (is_active = 0), reactivate it
                if ($existing->is_active == 0) {
                    $existing->update([
                        'is_active' => 1,
                        'invoice_no' => $invoiceNo,
                        'inv_amount' => $investigation->price ?? 0,
                        'sale_price' => 0,
                        'updated_by' => auth()->id(),
                        'updated_at' => now(),
                        'is_sync' => 0
                    ]);
                    $savedInvestigations[] = $existing;
                }
                // If already active, skip it
                continue;
            }

            $data = [
                'invoice_no' => $invoiceNo,
                'patient_id' => $request->patient_id,
                'appointment_id' => $request->appointment_id,
                'investigation_sub_category_id' => $investigationId,
                'consultant_id' => $appointment->consultant_id,
                'consultant_share_percentage' => 0,
                'consultant_share_amount' => 0,
                'inv_amount' => $investigation->price ?? 0,
                'sale_price' => 0, // Free for patients
                'frequency' => 1,
                'discount_percentage' => 0,
                'discount_amount' => 0,
                'inv_date' => now(),
                'created_by' => auth()->id(),
                'created_at' => now(),
                'is_active' => 1,
                'patient_type' => 'hospital_patient',
                'is_posted' => 0,
                'status' => 0,
                'is_sync' => 0
            ];

            $savedInvestigations[] = \App\Models\Patient\PatientInvestigation::create($data);
        }

        return response()->json([
            'status' => true,
            'invoice_number' => $invoiceNo,
            'message' => count($savedInvestigations) . ' investigation(s) added successfully',
            'count' => count($savedInvestigations)
        ]);
    }
}
