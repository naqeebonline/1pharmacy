<?php

namespace App\Http\Controllers\Appointments;

use App\Http\Controllers\Controller;
use App\Models\Appointments\Appointment;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PackageAppointmentController extends Controller
{
    public function index()
    {
        return view('appointments.package_appointments');
    }

    public function get_package_appointments(Request $request)
    {
        if ($request->ajax()) {
            // Get date filters from request, default to today
            $fromDate = $request->input('from_date', Carbon::today()->format('Y-m-d'));
            $toDate = $request->input('to_date', Carbon::today()->format('Y-m-d'));
            
            // Debug logging
            \Log::info('Package Appointments Filter', [
                'from_date' => $fromDate,
                'to_date' => $toDate,
                'request_from' => $request->input('from_date'),
                'request_to' => $request->input('to_date')
            ]);
            
            $data = Appointment::with(['patient', 'consultant', 'opd_type'])
                ->whereHas('opd_type', function ($query) {
                    $query->where(function ($q) {
                        $q->where('including_medicine', 1)
                          ->orWhere('including_labs', 1);
                    });
                })
                ->whereDate('appointment_date', '>=', $fromDate)
                ->whereDate('appointment_date', '<=', $toDate)
                ->where('is_active', 1)
                ->orderBy('appointment_date', 'desc')
                ->get();

            return DataTables::of($data)
                ->addColumn('appointment_number', function ($row) {
                    return $row->appointment_number ?? 'N/A';
                })
                ->addColumn('patient_name', function ($row) {
                    return $row->patient->name ?? 'N/A';
                })
                ->addColumn('mr_no', function ($row) {
                    return $row->patient->mr_no ?? 'N/A';
                })
                ->addColumn('consultant_name', function ($row) {
                    return $row->consultant->name ?? 'N/A';
                })
                ->addColumn('opd_type_name', function ($row) {
                    return $row->opd_type->name ?? 'N/A';
                })
                ->addColumn('appointment_date', function ($row) {
                    return $row->appointment_date ? date('Y-m-d H:i', strtotime($row->appointment_date)) : 'N/A';
                })
                ->addColumn('fee', function ($row) {
                    return number_format($row->fee, 2);
                })
                ->addColumn('investigation_cost', function ($row) {
                    // Calculate total investigation cost from patient_investigations
                    $investigationCost = DB::table('patient_investigations')
                        ->where('appointment_id', $row->id)
                        ->where('is_active', 1)
                        ->sum('inv_amount');
                    
                    return number_format($investigationCost, 2);
                })
                ->addColumn('medicine_cost', function ($row) {
                    // Calculate total medicine cost from sale and sale_details
                    $medicineCost = DB::table('sale')
                        ->join('sale_details', 'sale.SaleID', '=', 'sale_details.SaleID')
                        ->where('sale.appointment_id', $row->id)
                        ->selectRaw('SUM(sale_details.PurchasePrice * (sale_details.Quantity - IFNULL(sale_details.ReturnQuantity, 0))) as total_medicine_cost')
                        ->value('total_medicine_cost');
                    
                    return number_format($medicineCost ?? 0, 2);
                })
                ->addColumn('total_cost', function ($row) {
                    // Calculate investigation cost
                    $investigationCost = DB::table('patient_investigations')
                        ->where('appointment_id', $row->id)
                        ->where('is_active', 1)
                        ->sum('inv_amount');
                    
                    // Calculate medicine cost
                    $medicineCost = DB::table('sale')
                        ->join('sale_details', 'sale.SaleID', '=', 'sale_details.SaleID')
                        ->where('sale.appointment_id', $row->id)
                        
                        ->selectRaw('SUM(sale_details.PurchasePrice * (sale_details.Quantity - IFNULL(sale_details.ReturnQuantity, 0))) as total_medicine_cost')
                        ->value('total_medicine_cost');
                    
                    // Get OPD fee
                    $opdFee = $row->opd_type->fees ?? 0;
                    
                    // Consultant fee (fixed at 500)
                    $consultantFee = 500;
                    
                    // Calculate total cost (only expenses: investigation + medicine + consultant)
                    $totalCost = ($investigationCost ?? 0) + ($medicineCost ?? 0) + $consultantFee;
                    
                    return '<strong>Rs. ' . number_format($totalCost, 2) . '</strong>';
                })
                ->addColumn('balance', function ($row) {
                    // Calculate investigation cost
                    $investigationCost = DB::table('patient_investigations')
                        ->where('appointment_id', $row->id)
                        ->where('is_active', 1)
                        ->sum('inv_amount');
                    
                    // Calculate medicine cost
                    $medicineCost = DB::table('sale')
                        ->join('sale_details', 'sale.SaleID', '=', 'sale_details.SaleID')
                        ->where('sale.appointment_id', $row->id)
                        ->selectRaw('SUM(sale_details.PurchasePrice * (sale_details.Quantity - IFNULL(sale_details.ReturnQuantity, 0))) as total_medicine_cost')
                        ->value('total_medicine_cost');
                    
                    // Consultant fee (fixed at 500)
                    $consultantFee = 500;
                    
                    // Calculate total expenses (investigation + medicine + consultant)
                    $totalExpenses = ($investigationCost ?? 0) + ($medicineCost ?? 0) + $consultantFee;
                    
                    // Calculate balance: fee (budget) - expenses
                    $balance = $row->fee - $totalExpenses;
                    
                    $balanceClass = $balance >= 0 ? 'text-success' : 'text-danger';
                    $balanceIcon = $balance >= 0 ? 'fa-plus-circle' : 'fa-minus-circle';
                    
                    return '<strong class="' . $balanceClass . '">
                                <i class="fa ' . $balanceIcon . '"></i> Rs. ' . number_format($balance, 2) . '
                            </strong>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="btn-group" role="group">';
                    
                    // Add Medicine button if including_medicine = 1
                    if ($row->opd_type->including_medicine == 1) {
                        $btn .= '<a href="' . route('pos.package_sale') . '?appointment_id=' . $row->id . '" class="btn btn-sm btn-success" title="Add Medicine">
                                    <i class="fa fa-pills"></i> Medicine
                                </a>';
                    }
                    
                  if ((getUserRole() == 'Super Admin' || getUserRole() == 'Finance')) {
                    $btn .= '<a href="javascript:void(0)" class="btn btn-sm btn-primary view-appointment" 
                                data-id="' . $row->id . '" title="View Details">
                                <i class="fa fa-eye"></i>
                            </a>';
                  }
                    
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['total_cost', 'balance', 'action'])
                ->setRowClass(function ($row) {
                    // Calculate balance to determine row color
                    $investigationCost = DB::table('patient_investigations')
                        ->where('appointment_id', $row->id)
                        ->where('is_active', 1)
                        ->sum('inv_amount');
                    
                    $medicineCost = DB::table('sale')
                        ->join('sale_details', 'sale.SaleID', '=', 'sale_details.SaleID')
                        ->where('sale.appointment_id', $row->id)
                        ->selectRaw('SUM(sale_details.PurchasePrice * (sale_details.Quantity - IFNULL(sale_details.ReturnQuantity, 0))) as total_medicine_cost')
                        ->value('total_medicine_cost');
                    
                    $consultantFee = 500;
                    $totalExpenses = ($investigationCost ?? 0) + ($medicineCost ?? 0) + $consultantFee;
                    $balance = $row->fee - $totalExpenses;
                    
                    // Return CSS class for negative balance
                    return $balance < 0 ? 'table-danger' : '';
                })
                ->make(true);
        }
    }

    public function get_appointment_details(Request $request)
    {
        $appointmentId = $request->appointment_id;
        
        $appointment = Appointment::with(['patient', 'consultant', 'opd_type'])
            ->where('id', $appointmentId)
            ->first();
            
        if (!$appointment) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment not found'
            ]);
        }
        
        // Get investigations
        $investigations = DB::table('patient_investigations')
            ->join('investigation_sub_category', 'patient_investigations.investigation_sub_category_id', '=', 'investigation_sub_category.id')
            ->where('patient_investigations.appointment_id', $appointmentId)
            ->where('patient_investigations.is_active', 1)
            ->select('investigation_sub_category.name', 'patient_investigations.inv_amount')
            ->get();
            
        $investigationCost = $investigations->sum('inv_amount');
        
        // Get medicines
        $medicines = DB::table('sale')
            ->join('sale_details', 'sale.SaleID', '=', 'sale_details.SaleID')
            ->join('products', 'sale_details.ProductID', '=', 'products.ProductID')
            ->where('sale.appointment_id', $appointmentId)
            
            ->select(
                'products.ProductName',
                'sale_details.Quantity',
                'sale_details.ReturnQuantity',
                'sale_details.PurchasePrice',
                DB::raw('(sale_details.PurchasePrice * (sale_details.Quantity - IFNULL(sale_details.ReturnQuantity, 0))) as total_cost')
            )
            ->get();
            
        $medicineCost = $medicines->sum('total_cost');
        
        // Fixed consultant fee
        $consultantFee = 500;
        
        // OPD fee
        $opdFee = $appointment->opd_type->fees ?? 0;
        
        // Calculate total expenses and balance
        $totalExpenses = $investigationCost + $medicineCost + $consultantFee;
        $balance = $appointment->fee - $totalExpenses;
        
        return response()->json([
            'success' => true,
            'appointment' => [
                'appointment_number' => $appointment->appointment_number,
                'patient_name' => $appointment->patient->name ?? 'N/A',
                'mr_no' => $appointment->patient->mr_no ?? 'N/A',
                'consultant_name' => $appointment->consultant->name ?? 'N/A',
                'opd_type' => $appointment->opd_type->name ?? 'N/A',
                'appointment_date' => $appointment->appointment_date ? date('Y-m-d H:i', strtotime($appointment->appointment_date)) : 'N/A',
                'fee' => $appointment->fee,
                'opd_fee' => $opdFee
            ],
            'investigations' => $investigations,
            'investigation_cost' => $investigationCost,
            'medicines' => $medicines,
            'medicine_cost' => $medicineCost,
            'consultant_fee' => $consultantFee,
            'total_expenses' => $totalExpenses,
            'balance' => $balance
        ]);
    }
}
