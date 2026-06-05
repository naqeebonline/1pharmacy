<?php

namespace App\Http\Controllers\PatientController;

use App\Http\Controllers\Controller;
use App\Models\Configuration\Consultants;
use App\Models\Configuration\District;
use App\Models\Configuration\InvestigationMainCategory;
use App\Models\Configuration\InvestigationParameter;
use App\Models\Configuration\InvestigationSubCategory;
use App\Models\Configuration\ProcedureType;
use App\Models\Configuration\ServiceType;
use App\Models\Configuration\Ward;
use App\Models\Configuration\WardBed;
use App\Models\Patient\Patient;
use App\Models\Patient\PatientInvestigation;
use App\Models\Patient\PatientInvestigationPayment;
use App\Models\Patient\PatientLocation;
use App\Models\Patient\Relation;
use App\Models\Users;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PatientInvestigationController extends Controller
{
    public function general_patient_investigation()
    {
        $data['users'] = Users::whereHas('roles', function ($q) {
            $q->where('name', 'Super Admin')->orWhere('name', 'like', '%Receiption User%');
        })->get(["id", "name"]);
        //dd($superAdmins);
        $data["title"] = "Patient Investigation";
        $data['investigation'] = InvestigationSubCategory::whereIsActive(1)->get();
        $data['service_type'] = ServiceType::whereIsActive(1)->get();
        $data["relations"] = Relation::get();
        $data["district"] = District::get();
        $data["investigation_sub_category"] = InvestigationSubCategory::get(["id", "name"]);

        $data["locations"] = PatientLocation::get();
        $data["consultants"] = Consultants::where(["is_active" => 1])->get();
        return view("investigation.patient_investigation", $data);
    }

    public function save_general_patient_investigation()
    {
        $data = request()->except(['_token', "id", "list_investigations", "invoice_no", "discount_percentage", "consultant_id", "selected_appointment_id"]);


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
        $patient_id = $patient->id;
        $list_investigations = json_decode(request()->list_investigations);

        if (count($list_investigations) == 0) {
            return response()->json([
                "status" => "empty",
                "message" => "Please Add Some investigation."
            ]);
        }

        // Get appointment_id from request
        $appointment_id = request()->input('selected_appointment_id', 0);

        $consultant_share_percentage = 0;
        $consultant_id = 0;
        if (request()->has('consultant_id')) {
            $consultant_id = request()->consultant_id;
            $con = Consultants::where(["id" => $consultant_id])->first();
            $consultant_share_percentage = $con->lab_percentage ?? 0;
        }
        $total_inv_amount = 0;
        foreach ($list_investigations as $key => $value) {
            $investigation = InvestigationSubCategory::where("id", $value->investigation_id)->first();
            $investigation_rate_after_discount = ($investigation->sale_price * $value->frequency) - ($value->discount_amount);
            $total_inv_amount = ($total_inv_amount) + ($investigation_rate_after_discount);
            $data = [
                "invoice_no"        => request()->invoice_no,
                "patient_id"        => $patient_id,
                "investigation_sub_category_id"    => $value->investigation_id,
                "consultant_id"    => $consultant_id,
                "consultant_share_percentage"    => $consultant_share_percentage,
                "consultant_share_amount"    => ($investigation_rate_after_discount * $consultant_share_percentage) / 100,
                "inv_amount"        => $investigation->price,
                "sale_price"        => ($investigation->sale_price * $value->frequency),
                "frequency"         => $value->frequency,
                "discount_percentage"    => $value->discount_percentage,
                "discount_amount"    => $value->discount_amount,
                "appointment_id"    => $appointment_id > 0 ? $appointment_id : null,
                "inv_date"    => date("Y-m-d H:i:s"),
                "created_by"    => auth()->user()->id,
                "created_at"    => date("Y-m-d H:i:s"),
                "patient_type"    => 'hospital_patient',

            ];
            if ($value->frequency > 3) {
                $data['frequency'] = 1;
                $data['sale_price'] = $value->frequency;
                $data['inv_amount'] = $value->frequency;
            }
            if (in_array($investigation->investigation_category_id, [7, 8, 9, 10])) {
                $data['status'] = 1;
                $data['inv_out_date'] = date("Y-m-d H:i:s");
                $data['inv_comment'] = "no result required";
            }
            PatientInvestigation::create($data);
        }

        PatientInvestigationPayment::create(["invoice_no" => request()->invoice_no, "patient_id" => $patient_id, "amount" => $total_inv_amount, "created_by" => auth()->user()->id, "created_at" => date("Y-m-d H:i:s")]);

        return response()->json([
            "status" => true,
            "message" => "Record save successfully."
        ]);
    }

    public function get_list_investigations()
    {
        $patients = PatientInvestigation::where("is_active", 1)->with("investigation")
            ->with("patient")
            ->with("users")
            ->with("created_by_user")

            ->when(request()->from_date, function ($query) {
                $query->whereDate('inv_date', '>=', date("Y-m-d", strtotime(request()->from_date)));
            })
            ->when(request()->to_date, function ($query) {
                $query->whereDate('inv_date', '<=', date("Y-m-d", strtotime(request()->to_date)));
            })
            ->when(request()->investigation_sub_category_id, function ($query) {
                $query->where('investigation_sub_category_id', '=', request()->investigation_sub_category_id);
            })
            ->when(request()->created_by, function ($query) {
                $query->where('created_by', '=', request()->created_by);
            })
            ->where(["patient_type" => "hospital_patient"])
            ->orderBy("id", "desc");
        return DataTables::of($patients)
            ->addColumn("actions", function ($patient) {
                $button = '';
                if ($patient->status == 1) {
                    $button = $button . '<a target="_blank" href="' . route('pos.print_inv_result', [$patient->id]) . '"  data-details=\'' . $patient . '\'  class="btn btn-sm btn-success print_inv_record"><i class="tf-icons bx bx-printer"></i></a>';
                } else {
                    $button = $button . '<button data-id="' . $patient->id . '" class="btn btn-danger btn-sm delete_record"><i class="bx bx-trash tf-icons"></i></button>';
                }


                return $button;
            })
            ->addColumn("received_amount", function ($patient) {
                return (($patient->sale_price) - ($patient->discount_amount)) * $patient->frequency;
            })
            ->addColumn("print_invoice_number", function ($patient) {
                return '<a class="print_invoice_link" href="' . route('pos.print_hospital_lab_invoice', [$patient->invoice_no]) . '">' . $patient->invoice_no . '</a>';
            })
            ->addColumn('created_by_user', function ($row) {
                return $row->created_by_user->name ?? '';
            })
            ->rawColumns(["print_invoice_number", "received_amount", "actions"])
            ->make(true);
    }

    public function print_all_investigations($from_date, $to_date, $investigation_sub_category_id, $created_by)
    {
        $res =  PatientInvestigation::where("is_active", 1)->with("investigation")->with("patient")
            ->select('patient_investigations.*')
            ->leftJoin('users', 'patient_investigations.created_by', '=', 'users.id')
            ->when($from_date, function ($query) use ($from_date) {
                $query->whereDate('inv_date', '>=', $from_date);
            })
            ->when($to_date, function ($query) use ($to_date) {
                $query->whereDate('inv_date', '<=', $to_date);
            })
            ->when($investigation_sub_category_id, function ($query) use ($investigation_sub_category_id) {
                $query->where('investigation_sub_category_id', '=', $investigation_sub_category_id);
            })->when($created_by, function ($query) use ($created_by) {
                $query->where('created_by', '=', $created_by);
            })

            ->where(["patient_type" => "hospital_patient"])
            ->orderBy("id", "desc")
            ->get();

        $data['from_date'] = ($from_date && $from_date != 'nill') ? $from_date : "-";
        $data['to_date'] = ($to_date && $to_date != 'nill') ? $to_date : "-";
        $data['data'] = $res;

        return view("investigation.reports.print_all_investigations", $data);
    }



    public function save_patient_investigation()
    {
        $data = request()->except(['_token', "id"]);

        $data['inv_date'] = request()->inv_date . " " . date("h:i:s");
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

        return response()->json([
            "status" => true,
            "message" => "Record save successfully."
        ]);
    }


    public function delete_patient_investigation()
    {


        PatientInvestigation::whereId(request()->id)->update(["is_active" => 0, "is_sync" => 0, "updated_at" => date("Y-m-d H:i:s")]);
        $list_investigations = PatientInvestigation::whereId(request()->id)->first();
        $invoice_no = $list_investigations->invoice_no ?? "";
        $list_investigations = PatientInvestigation::with("subCategory")->where("invoice_no", $invoice_no)->where(["is_active" => 1])->get();
        $total_inv_amount = 0;


        foreach ($list_investigations as $key => $value) {

            //$investigation = InvestigationSubCategory::where("id",$value->investigation_id)->first();

            $investigation_rate_after_discount = ($value->sale_price) - ($value->discount_amount);
            $total_inv_amount = ($total_inv_amount) + ($investigation_rate_after_discount);
        }

        PatientInvestigationPayment::where("invoice_no", $invoice_no)->update(["amount" => $total_inv_amount]);
        return ["status" => true, "message" => "Record saved successfully"];
    }
}
