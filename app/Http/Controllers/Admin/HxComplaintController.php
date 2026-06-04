<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HxComplaint;
use App\Models\Appointments\Appointment;
use App\Models\Patient\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class HxComplaintController extends Controller
{
    /**
     * Display nursing counter page for recording HX complaints
     */
    public function nursing_hx_complaints()
    {
        $data["title"] = "Nursing Counter - HX Complaints";
        return view("hx_complaints.nursing_hx_complaints", $data);
    }

    /**
     * Get list of today's appointments for DataTable
     */
    public function get_today_appointments()
    {
        $today = Carbon::today()->format('Y-m-d');

        $appointments = Appointment::with(['patient', 'consultant'])
            ->whereDate('appointment_date', $today)
            ->where('is_active', 1)
            ->select('appointments.*');

        return DataTables::of($appointments)
            ->addIndexColumn()
            ->addColumn('mr_no', function ($data) {
                return $data->patient->mr_no ?? 'N/A';
            })
            ->addColumn('patient_name', function ($data) {
                return $data->patient->name ?? 'N/A';
            })
            ->addColumn('patient_phone', function ($data) {
                return $data->patient->phone ?? 'N/A';
            })
            ->addColumn('patient_age', function ($data) {
                return $data->patient->age ?? 'N/A';
            })
            ->addColumn('consultant_name', function ($data) {
                return $data->consultant->name ?? 'N/A';
            })
            ->addColumn('appointment_time', function ($data) {
                return $data->appointment_time ?? 'N/A';
            })
            ->addColumn('hx_status', function ($data) {
                $hxComplaint = HxComplaint::where('appointment_id', $data->id)
                    ->where('is_active', 1)
                    ->first();

                if ($hxComplaint) {
                    return '<span class="badge badge-success">Recorded</span>';
                }
                return '<span class="badge badge-warning">Pending</span>';
            })
            ->addColumn('action', function ($data) {
                $hxComplaint = HxComplaint::where('appointment_id', $data->id)
                    ->where('is_active', 1)
                    ->first();

                if ($hxComplaint) {
                    return '<button data-id="' . $data->id . '" data-hx-id="' . $hxComplaint->id . '" class="btn btn-primary btn-sm view_hx_record"><i class="fa fa-eye"></i> View/Edit</button>';
                } else {
                    return '<button data-id="' . $data->id . '" class="btn btn-success btn-sm record_hx"><i class="fa fa-plus"></i> Record</button>';
                }
            })
            ->rawColumns(['hx_status', 'action'])
            ->make(true);
    }

    /**
     * Get appointment details for recording HX complaint
     */
    public function get_appointment_details(Request $request)
    {
        $appointment = Appointment::with(['patient', 'consultant'])
            ->where('id', $request->appointment_id)
            ->first();

        if (!$appointment) {
            return response()->json([
                "status" => false,
                "message" => "Appointment not found"
            ]);
        }

        // Check if HX complaint already exists
        $hxComplaint = HxComplaint::where('appointment_id', $appointment->id)
            ->where('is_active', 1)
            ->first();

        return response()->json([
            "status" => true,
            "appointment" => $appointment,
            "hx_complaint" => $hxComplaint
        ]);
    }

    /**
     * Save HX complaint record
     */
    public function save_hx_complaint(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'patient_id' => 'required|exists:patients,id',
        ]);

        $data = [
            'appointment_id' => $request->appointment_id,
            'patient_id' => $request->patient_id,
            'complaint' => $request->complaint,
            'bp' => $request->bp,
            'temp' => $request->temp,
            'pulse' => $request->pulse,
            'rr' => $request->rr,
            'investigation' => $request->investigation,
            'recorded_by' => Auth::id(),
        ];

        if ($request->id && $request->id > 0) {
            // Update existing record
            HxComplaint::where('id', $request->id)->update($data);
            $message = "HX Complaint updated successfully";
        } else {
            // Create new record
            HxComplaint::create($data);
            $message = "HX Complaint recorded successfully";
        }

        return response()->json([
            "status" => true,
            "message" => $message
        ]);
    }

    /**
     * Get HX complaint details
     */
    public function get_hx_complaint(Request $request)
    {
        $hxComplaint = HxComplaint::with(['appointment.patient', 'appointment.consultant'])
            ->where('id', $request->hx_id)
            ->where('is_active', 1)
            ->first();

        if (!$hxComplaint) {
            return response()->json([
                "status" => false,
                "message" => "HX Complaint not found"
            ]);
        }

        return response()->json([
            "status" => true,
            "hx_complaint" => $hxComplaint,
            "appointment" => $hxComplaint->appointment
        ]);
    }

    /**
     * Get all HX complaints with filters
     */
    public function get_list_hx_complaints()
    {
        $hxComplaints = HxComplaint::where("is_active", 1)
            ->with("patient")
            ->with("appointment")
            ->with("created_by_user")
            ->when(request()->from_date, function ($query) {
                $query->whereDate('created_at', '>=', date("Y-m-d", strtotime(request()->from_date)));
            })
            ->when(request()->to_date, function ($query) {
                $query->whereDate('created_at', '<=', date("Y-m-d", strtotime(request()->to_date)));
            })
            ->when(request()->created_by, function ($query) {
                $query->where('recorded_by', '=', request()->created_by);
            })
            ->orderBy("id", "desc");

        return DataTables::of($hxComplaints)
            ->addIndexColumn()
            ->addColumn('mr_no', function ($row) {
                return $row->patient->mr_no ?? 'N/A';
            })
            ->addColumn('patient_name', function ($row) {
                return $row->patient->name ?? 'N/A';
            })
            ->addColumn('appointment_date', function ($row) {
                return $row->appointment->appointment_date ?? 'N/A';
            })
            ->addColumn('vital_signs', function ($row) {
                return "BP: {$row->bp}, Temp: {$row->temp}, Pulse: {$row->pulse}, R/R: {$row->rr}";
            })
            ->addColumn('recorded_by_name', function ($row) {
                return $row->created_by_user->name ?? 'N/A';
            })
            ->addColumn('actions', function ($row) {
                return '<button data-id="' . $row->id . '" class="btn btn-info btn-sm view_details"><i class="fa fa-eye"></i> View</button>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    /**
     * Delete HX complaint record
     */
    public function delete_hx_complaint(Request $request)
    {
        HxComplaint::whereId($request->id)->update([
            "is_active" => 0,
            "updated_at" => date("Y-m-d H:i:s")
        ]);

        return response()->json([
            "status" => true,
            "message" => "Record deleted successfully"
        ]);
    }
}
