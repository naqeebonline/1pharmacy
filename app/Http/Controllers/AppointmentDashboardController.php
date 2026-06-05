<?php

namespace App\Http\Controllers;

use App\Models\Appointments\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AppointmentDashboardController extends Controller
{
    public function index(Request $request)
    {
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
         

        $usedDefaultRange = !$request->filled('from_date') && !$request->filled('to_date');

        $now = Carbon::now();
        if (!$from_date) {
            $from_date = $now->copy()->subDays(0)->toDateString();
        }
        if (!$to_date) {
            $to_date = $now->toDateString();
        }
        if (Carbon::parse($from_date)->greaterThan(Carbon::parse($to_date))) {
            [$from_date, $to_date] = [$to_date, $from_date];
        }

        $consultant_id = $request->input('consultant_id');
        $opd_type_id = $request->input('opd_type_id');

        $query = DB::table('appointments')
            ->whereDate('appointment_date', '>=', $from_date)
            ->whereDate('appointment_date', '<=', $to_date)
            ->where('is_active', 1);
        if ($consultant_id) {
            $query->where('consultant_id', $consultant_id);
        }
        if ($opd_type_id) {
            $query->where('opd_type_id', $opd_type_id);
        }

        $total_appointments = $query->count();
        $total_hospital_share = (clone $query)->sum('hospital_share');
        $total_consultant_share = (clone $query)->sum('consultant_share');
        $total_amount = $total_hospital_share + $total_consultant_share;

        $opd_type_counts_query = DB::table('appointments')
            ->select('opd_type_id', DB::raw('count(*) as total'))
            ->whereDate('appointment_date', '>=', $from_date)
            ->whereDate('appointment_date', '<=', $to_date)
            ->where('is_active', 1);
        if ($consultant_id) {
            $opd_type_counts_query->where('consultant_id', $consultant_id);
        }
        if ($opd_type_id) {
            $opd_type_counts_query->where('opd_type_id', $opd_type_id);
        }
        $opd_type_counts = $opd_type_id
            ? $opd_type_counts_query->groupBy('opd_type_id')->get()->filter(fn($row) => $row->opd_type_id == $opd_type_id)
            : $opd_type_counts_query->groupBy('opd_type_id')->get();

        $daily_counts_query = DB::table('appointments')
            ->select(DB::raw('DATE(appointment_date) as appointment_day'), DB::raw('COUNT(*) as total'))
            ->whereDate('appointment_date', '>=', $from_date)
            ->whereDate('appointment_date', '<=', $to_date)
            ->where('is_active', 1);
        if ($consultant_id) {
            $daily_counts_query->where('consultant_id', $consultant_id);
        }
        if ($opd_type_id) {
            $daily_counts_query->where('opd_type_id', $opd_type_id);
        }
        $daily_counts_collection = $daily_counts_query->groupBy('appointment_day')->orderBy('appointment_day')->get();

        $daily_counts_map = $daily_counts_collection->pluck('total', 'appointment_day');



        $doctor_summary = DB::table('appointments')

            ->join('consultants', 'consultants.id', '=', 'appointments.consultant_id')

            ->select('appointments.consultant_id', 'consultants.name as consultant_name', DB::raw('COUNT(*) as total'))

            ->whereDate('appointments.appointment_date', '>=', $from_date)

            ->whereDate('appointments.appointment_date', '<=', $to_date)

            ->where('appointments.is_active', 1);



        if ($consultant_id) {

            $doctor_summary->where('appointments.consultant_id', $consultant_id);

        }



        if ($opd_type_id) {

            $doctor_summary->where('appointments.opd_type_id', $opd_type_id);

        }



        $doctor_summary = $doctor_summary

            ->groupBy('appointments.consultant_id', 'consultants.name')

            ->having('total', '>', 0)

            ->orderByDesc('total')

            ->get();

        $period = CarbonPeriod::create($from_date, $to_date);
        $daily_appointment_labels = [];

        $daily_appointment_dates = [];
        $daily_appointment_totals = [];
        foreach ($period as $date) {
            $day_key = $date->toDateString();
            $daily_appointment_labels[] = $date->format('d M');
            $daily_appointment_totals[] = (int) ($daily_counts_map[$day_key] ?? 0);

            $daily_appointment_dates[] = $day_key;
        }

        $from_carbon = Carbon::parse($from_date);
        $to_carbon = Carbon::parse($to_date);
        if ($usedDefaultRange && $from_carbon->diffInDays($to_carbon) >= 29) {
            $daily_chart_title = 'Last 30 Days (' . $from_carbon->format('d M Y') . ' - ' . $to_carbon->format('d M Y') . ')';
        } elseif ($from_carbon->isSameMonth($to_carbon) && $from_carbon->isSameYear($to_carbon)) {
            $daily_chart_title = $from_carbon->format('F Y');
        } else {
            $daily_chart_title = $from_carbon->format('d M Y') . ' - ' . $to_carbon->format('d M Y');
        }


        $consultants = DB::table('consultants')->select('id', 'name')->get();
        $opd_types = DB::table('opd_type')->select('id', 'name')->get();

        return view('appointment_dashboard', [
            'total_appointments' => $total_appointments,
            'total_hospital_share' => $total_hospital_share,
            'total_consultant_share' => $total_consultant_share,
            'total_amount' => $total_amount,
            'opd_type_counts' => $opd_type_counts,
            'consultants' => $consultants,
            'opd_types' => $opd_types,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'date_range_label' => $daily_chart_title,
            'daily_appointment_labels' => $daily_appointment_labels,
            'daily_appointment_totals' => $daily_appointment_totals,

            'daily_appointment_dates' => $daily_appointment_dates,

            'doctor_summary' => $doctor_summary,

            'selected_consultant' => $consultant_id,
            'selected_opd_type' => $opd_type_id,
        ]);
    }

    public function dayAppointments(Request $request)
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'consultant_id' => ['nullable', 'integer'],
            'opd_type_id' => ['nullable', 'integer'],
        ]);

        $selectedDate = Carbon::parse($validated['date'])->toDateString();

        $query = Appointment::with(['patient', 'consultant', 'opd_type', 'created_by_user'])
            ->whereDate('appointment_date', $selectedDate)
            ->where('is_active', 1);

        if ($request->filled('consultant_id')) {
            $query->where('consultant_id', $request->input('consultant_id'));
        }

        if ($request->filled('opd_type_id')) {
            $query->where('opd_type_id', $request->input('opd_type_id'));
        }

        $appointments = $query->orderBy('appointment_date')->get()->map(function (Appointment $appointment) {
            $appointmentDate = Carbon::parse($appointment->getRawOriginal('appointment_date'));
            $patient = $appointment->patient;

            return [
                'id' => $appointment->id,
                'patient_name' => optional($patient)->name ?? ($appointment->name ?? 'N/A'),
                'mr_no' => optional($patient)->mr_no,
                'consultant' => optional($appointment->consultant)->name,
                'opd_type' => optional($appointment->opd_type)->name,
                'appointment_time' => $appointmentDate->format('h:i A'),
                'appointment_datetime' => $appointmentDate->format('d M Y h:i A'),
                'contact' => optional($patient)->contact_no ?? optional($patient)->phone ?? optional($patient)->mobile ?? null,
                'hospital_share' => $appointment->hospital_share,
                'consultant_share' => $appointment->consultant_share,
                'created_by' => optional($appointment->created_by_user)->name,
            ];
        });

        return response()->json([
            'date_label' => Carbon::parse($selectedDate)->format('d M Y'),
            'total' => $appointments->count(),
            'appointments' => $appointments,
        ]);
    }

}
