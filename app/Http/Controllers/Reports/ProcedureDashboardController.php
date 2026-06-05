<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Configuration\Consultants;
use App\Models\Configuration\ProcedureType;
use App\Models\Patient\InPatientAdmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class ProcedureDashboardController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Procedure Dashboard',
            'consultants' => Consultants::where('is_active', 1)->orderBy('name')->get(),
            'procedure_types' => ProcedureType::select('type')
                ->where('is_active', 1)
                ->distinct()
                ->orderBy('type')
                ->pluck('type')
        ];

        return view('reports.procedure_dashboard', $data);
    }

    public function getProcedureData(Request $request)
    {
        $query = InPatientAdmission::with([
            'patient',
            'consultant',
            'consultant_procedure.procedure',
            'ward',
            'bed'
        ])->where('is_active', 1);

        // Apply filters
        if ($request->from_date) {
            $query->whereDate('admission_date', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $query->whereDate('admission_date', '<=', $request->to_date);
        }

        if ($request->consultant_id) {
            $query->where('consultant_id', $request->consultant_id);
        }

        if ($request->procedure_type) {
            $query->whereHas('consultant_procedure.procedure', function ($q) use ($request) {
                $q->where('type', $request->procedure_type);
            });
        }

        if ($request->admission_status) {
            $query->where('admission_status', $request->admission_status);
        }

        return DataTables::of($query)
            ->addColumn('patient_name', function ($row) {
                return $row->patient->name ?? 'N/A';
            })
            ->addColumn('patient_mrno', function ($row) {
                return $row->patient->mrno ?? 'N/A';
            })
            ->addColumn('consultant_name', function ($row) {
                return $row->consultant->name ?? 'N/A';
            })
            ->addColumn('procedure_name', function ($row) {
                return $row->consultant_procedure->procedure->name ?? 'N/A';
            })
            ->addColumn('procedure_type', function ($row) {
                return $row->consultant_procedure->procedure->type ?? 'N/A';
            })
            ->addColumn('ward_bed', function ($row) {
                $ward = $row->ward->name ?? '';
                $bed = $row->bed->name ?? '';
                return $ward . ($ward && $bed ? ' - ' : '') . $bed;
            })
            ->addColumn('procedure_amount', function ($row) {
                return number_format($row->procedure_rate ?? 0, 2);
            })
            ->addColumn('consultant_share', function ($row) {
                return number_format($row->consultant_share_amount ?? 0, 2);
            })
            ->addColumn('admission_status_badge', function ($row) {
                $status = $row->admission_status ?? 'Unknown';
                $badgeClass = match ($status) {
                    'Admit' => 'bg-primary',
                    'Discharged' => 'bg-success',
                    'Cancelled' => 'bg-danger',
                    default => 'bg-secondary'
                };
                return '<span class="badge ' . $badgeClass . '">' . $status . '</span>';
            })
            ->editColumn('admission_date', function ($row) {
                return $row->admission_date ? date('d-M-Y H:i', strtotime($row->admission_date)) : 'N/A';
            })
            ->editColumn('discharge_date', function ($row) {
                return $row->discharge_date ? date('d-M-Y H:i', strtotime($row->discharge_date)) : 'N/A';
            })
            ->rawColumns(['admission_status_badge'])
            ->make(true);
    }

    public function getDashboardStats(Request $request)
    {
        $query = InPatientAdmission::with(['consultant_procedure.procedure', 'consultant'])
            ->where('is_active', 1);

        // Apply same filters as main data
        if ($request->from_date) {
            $query->whereDate('admission_date', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $query->whereDate('admission_date', '<=', $request->to_date);
        }

        if ($request->consultant_id) {
            $query->where('consultant_id', $request->consultant_id);
        }

        if ($request->procedure_type) {
            $query->whereHas('consultant_procedure.procedure', function ($q) use ($request) {
                $q->where('type', $request->procedure_type);
            });
        }

        if ($request->admission_status) {
            $query->where('admission_status', $request->admission_status);
        }

        $data = $query->get();

        // Calculate statistics
        $stats = [
            'total_procedures' => $data->count(),
            'total_revenue' => $data->sum('procedure_rate'),
            'total_consultant_share' => $data->sum('consultant_share_amount'),
            'total_hospital_share' => $data->sum('procedure_rate') - $data->sum('consultant_share_amount'),
            'avg_procedure_amount' => $data->avg('procedure_rate') ?? 0,
            'admitted_count' => $data->where('admission_status', 'Admit')->count(),
            'currently_admitted_count' => InPatientAdmission::where('admission_status', 'Admit')->where('is_active',1)->count(),
            'discharged_count' => $data->where('admission_status', 'Discharged')->count(),
            'cancelled_count' => $data->where('admission_status', 'Canceled')->count(),
            'referred_count' => $data->where('admission_status', 'Reffered')->count(),
        ];

        // Procedure type breakdown
        $totalProcedures = $data->count();
        $procedureTypeStats = $data->groupBy('consultant_procedure.procedure.type')
            ->map(function ($group, $type) use ($totalProcedures) {
                return [
                    'type' => $type ?? 'Unknown',
                    'count' => $group->count(),
                    'revenue' => $group->sum('procedure_rate'),
                    'percentage' => $totalProcedures > 0 ? round(($group->count() / $totalProcedures) * 100, 1) : 0,
                    'total_procedures' => $totalProcedures
                ];
            })->values();

        // Top consultants
        $consultantStats = $data->groupBy('consultant.name')
            ->map(function ($group, $name) {
                return [
                    'consultant' => $name ?? 'Unknown',
                    'procedures' => $group->count(),
                    'revenue' => $group->sum('procedure_rate'),
                    'share' => $group->sum('consultant_share_amount')
                ];
            })
            ->sortByDesc('revenue')
            ->take(400)
            ->values();

        // Monthly trend (last 12 months or filtered period)
        $monthlyStats = $data->groupBy(function ($item) {
            return date('Y-m', strtotime($item->admission_date));
        })->map(function ($group, $month) {
            $monthName = date('M Y', strtotime($month . '-01'));
            return [
                'month' => $monthName,
                'month_key' => $month,
                'count' => $group->count(),
                'revenue' => $group->sum('procedure_rate')
            ];
        })->sortBy('month_key')->values();

        // If no date filter is applied, limit to last 12 months
        if (!$request->from_date && !$request->to_date) {
            $monthlyStats = $monthlyStats->take(12);
        }

        return response()->json([
            'stats' => $stats,
            'procedure_types' => $procedureTypeStats,
            'top_consultants' => $consultantStats,
            'monthly_trend' => $monthlyStats
        ]);
    }

    public function exportPdf(Request $request)
    {
        try {
            $query = InPatientAdmission::with([
                'patient',
                'consultant',
                'consultant_procedure.procedure',
                'ward',
                'bed'
            ])->where('is_active', 1);

            // Apply filters
            if ($request->from_date) {
                $query->whereDate('admission_date', '>=', $request->from_date);
            }

            if ($request->to_date) {
                $query->whereDate('admission_date', '<=', $request->to_date);
            }

            if ($request->consultant_id) {
                $query->where('consultant_id', $request->consultant_id);
            }

            if ($request->procedure_type) {
                $query->whereHas('consultant_procedure.procedure', function ($q) use ($request) {
                    $q->where('type', $request->procedure_type);
                });
            }

            if ($request->admission_status) {
                $query->where('admission_status', $request->admission_status);
            }

            $procedures = $query->orderBy('admission_date', 'desc')->get();

            // Calculate statistics
            $stats = [
                'total_procedures' => $procedures->count(),
                'total_revenue' => $procedures->sum('procedure_rate'),
                'total_consultant_share' => $procedures->sum('consultant_share_amount'),
                'total_hospital_share' => $procedures->sum('procedure_rate') - $procedures->sum('consultant_share_amount'),
                'admitted_count' => $procedures->where('admission_status', 'Admit')->count(),
                'discharged_count' => $procedures->where('admission_status', 'Discharged')->count(),
                'cancelled_count' => $procedures->where('admission_status', 'Cancelled')->count(),
                'referred_count' => $procedures->where('admission_status', 'Reffered')->count(),
                'avg_procedure_amount' => $procedures->avg('procedure_rate') ?? 0,
            ];

            $data = [
                'procedures' => $procedures,
                'stats' => $stats,
                'filters' => [
                    'from_date' => $request->from_date,
                    'to_date' => $request->to_date,
                    'consultant_id' => $request->consultant_id,
                    'procedure_type' => $request->procedure_type,
                    'admission_status' => $request->admission_status,
                    'consultant_name' => $request->consultant_id ?
                        Consultants::find($request->consultant_id)->name ?? 'Unknown' : 'All',
                ],
                'title' => 'Procedure Dashboard Report',
                'generated_at' => now()->format('Y-m-d H:i:s')
            ];

            $filename = 'Procedure_Dashboard_Report_' . date('Y-m-d_H-i-s') . '.pdf';

            $pdf = Pdf::loadView('reports.procedure_dashboard_pdf', $data);
            $pdf->setPaper('a4', 'landscape');

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    public function printProcedures(Request $request)
    {
        try {
            $query = InPatientAdmission::with([
                'patient',
                'consultant',
                'consultant_procedure.procedure',
                'ward',
                'bed'
            ])->where('is_active', 1);

            // Apply filters
            if ($request->from_date) {
                $query->whereDate('admission_date', '>=', $request->from_date);
            }

            if ($request->to_date) {
                $query->whereDate('admission_date', '<=', $request->to_date);
            }

            if ($request->consultant_id) {
                $query->where('consultant_id', $request->consultant_id);
            }

            if ($request->procedure_type) {
                $query->whereHas('consultant_procedure.procedure', function ($q) use ($request) {
                    $q->where('type', $request->procedure_type);
                });
            }

            if ($request->admission_status) {
                $query->where('admission_status', $request->admission_status);
            }

            $procedures = $query->orderBy('admission_date', 'desc')->get();

            // Calculate totals
            $totalAmount = $procedures->sum('procedure_rate');
            $totalConsultantShare = $procedures->sum('consultant_share_amount');
            $totalHospitalShare = $totalAmount - $totalConsultantShare;

            $data = [
                'procedures' => $procedures,
                'filters' => [
                    'from_date' => $request->from_date,
                    'to_date' => $request->to_date,
                    'consultant_id' => $request->consultant_id,
                    'procedure_type' => $request->procedure_type,
                    'admission_status' => $request->admission_status,
                    'consultant_name' => $request->consultant_id ?
                        Consultants::find($request->consultant_id)->name ?? 'Unknown' : 'All Consultants',
                ],
                'totals' => [
                    'total_procedures' => $procedures->count(),
                    'total_amount' => $totalAmount,
                    'total_consultant_share' => $totalConsultantShare,
                    'total_hospital_share' => $totalHospitalShare,
                ],
                'title' => 'Procedure Details Report',
                'generated_at' => now()->format('d-M-Y H:i:s')
            ];

            return view('reports.print_procedures', $data);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load print view: ' . $e->getMessage());
        }
    }
}
