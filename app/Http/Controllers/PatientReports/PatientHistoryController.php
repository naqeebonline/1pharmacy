<?php

namespace App\Http\Controllers\PatientReports;

use App\Http\Controllers\Controller;
use App\Models\Patient\Patient;
use Illuminate\Http\Request;

class PatientHistoryController extends Controller
{
    /**
     * Display the patient history search view
     */
    public function index()
    {
        return view('patient_reports.patient_history_by_mobile');
    }

    /**
     * Search patients by mobile number, MR number, or appointment number
     */
    public function searchByMobile(Request $request)
    {
        $request->validate([
            'search_value' => 'required|string|min:3',
            'search_type' => 'required|in:mobile,mr_number,appointment_number'
        ]);

        $searchValue = $request->search_value;
        $searchType = $request->search_type;

        // Build query based on search type
        if ($searchType === 'mobile') {
            // Search by mobile number
            $patients = Patient::where('contact_no', 'LIKE', '%' . $searchValue . '%')
                ->with([
                    'appointments' => function ($query) {
                        $query->where('is_active', 1)
                            ->orderBy('appointment_date', 'desc');
                    },
                    'appointments.consultant',
                    'appointments.opd_type',
                    'appointments.investigations' => function ($query) {
                        $query->with('investigation');
                    }
                ])
                ->get();
        } elseif ($searchType === 'mr_number') {
            // Search by MR number
            $patients = Patient::where('mr_no', 'LIKE', '%' . $searchValue . '%')
                ->with([
                    'appointments' => function ($query) {
                        $query->where('is_active', 1)
                            ->orderBy('appointment_date', 'desc');
                    },
                    'appointments.consultant',
                    'appointments.opd_type',
                    'appointments.investigations' => function ($query) {
                        $query->with('investigation');
                    }
                ])
                ->get();
        } elseif ($searchType === 'appointment_number') {
            // Search by appointment number - get patients through appointments
            $patients = Patient::whereHas('appointments', function ($query) use ($searchValue) {
                $query->where('appointment_number', 'LIKE', '%' . $searchValue . '%')
                    ->where('is_active', 1);
            })
                ->with([
                    'appointments' => function ($query) use ($searchValue) {
                        $query->where('appointment_number', 'LIKE', '%' . $searchValue . '%')
                            ->where('is_active', 1)
                            ->orderBy('appointment_date', 'desc');
                    },
                    'appointments.consultant',
                    'appointments.opd_type',
                    'appointments.investigations' => function ($query) {
                        $query->with('investigation');
                    }
                ])
                ->get();
        }

        if ($patients->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No patients found with this search criteria'
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $patients
        ]);
    }

    /**
     * Print patient history by mobile number, MR number, or appointment number
     */
    public function printHistoryByMobile(Request $request)
    {
        $searchValue = $request->get('search_value');
        $searchType = $request->get('search_type', 'mobile');

        if (!$searchValue) {
            return redirect()->back()->with('error', 'Search value is required');
        }

        // Build query based on search type
        if ($searchType === 'mobile') {
            // Search by mobile number
            $patients = Patient::where('contact_no', 'LIKE', '%' . $searchValue . '%')
                ->with([
                    'appointments' => function ($query) {
                        $query->where('is_active', 1)
                            ->orderBy('appointment_date', 'desc');
                    },
                    'appointments.consultant',
                    'appointments.opd_type',
                    'appointments.investigations' => function ($query) {
                        $query->with('investigation');
                    }
                ])
                ->get();
        } elseif ($searchType === 'mr_number') {
            // Search by MR number
            $patients = Patient::where('mr_no', 'LIKE', '%' . $searchValue . '%')
                ->with([
                    'appointments' => function ($query) {
                        $query->where('is_active', 1)
                            ->orderBy('appointment_date', 'desc');
                    },
                    'appointments.consultant',
                    'appointments.opd_type',
                    'appointments.investigations' => function ($query) {
                        $query->with('investigation');
                    }
                ])
                ->get();
        } elseif ($searchType === 'appointment_number') {
            // Search by appointment number
            $patients = Patient::whereHas('appointments', function ($query) use ($searchValue) {
                $query->where('appointment_number', 'LIKE', '%' . $searchValue . '%')
                    ->where('is_active', 1);
            })
                ->with([
                    'appointments' => function ($query) use ($searchValue) {
                        $query->where('appointment_number', 'LIKE', '%' . $searchValue . '%')
                            ->where('is_active', 1)
                            ->orderBy('appointment_date', 'desc');
                    },
                    'appointments.consultant',
                    'appointments.opd_type',
                    'appointments.investigations' => function ($query) {
                        $query->with('investigation');
                    }
                ])
                ->get();
        }

        $data = [
            'patients' => $patients,
            'search_value' => $searchValue,
            'search_type' => $searchType
        ];

        return view('patient_reports.print_patient_history', $data);
    }
}
