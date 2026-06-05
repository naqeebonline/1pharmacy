<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Grn;
use App\Models\Market;
use App\Models\PaymentDetail;
use App\Models\PaymentType;
use App\Models\Product;
use App\Models\ReceiveablesDetail;
use App\Models\Sale;
use App\Models\SalePayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class CustomerPayments extends Controller
{
    public function customer_payments()
    {
        $data["title"] = "Customer Payments";
        $data["suppliers"] = Customer::where("Type", 2)->get();
        $data["payment_type"] = PaymentType::get();
        return view("sale/customer_payments", $data);
    }


    public function save_customer_payments(Request $request)
    {
        $data = $request->all();
        
        $data["CreatedBy"] = Auth::user()->id;
        $data["CreatedAt"] = Carbon::today();
        $data["SCID"] = $request->SID;
        $data["Payment_type_ID"] = $request->payment_type_id;

        $paymentDetail = ReceiveablesDetail::create($data);

        return response()->json([
            "data" => $paymentDetail
        ]);
    }


    public function receiveables($id)
    {
        $data = ReceiveablesDetail::where('SCID', $id)
            ->with('paymentType')->get();
        return DataTables::of($data)
            ->addColumn('payment_type', function ($data) {
                return $data->paymentType->payment_type ?? ''; // Safeguard against null
            })
           
            ->addColumn('action', function ($data) {
                // Example: Add an Edit/Delete button for actions
                return '<a class="btn btn-sm btn-primary">Edit</a>
                    <a class="btn btn-sm btn-danger">Delete</a>';
            })
            ->rawColumns(['payment_type','action']) // Allow raw HTML for action buttons
            ->make(true);
    }


    public function sale_details($id){
        
        $data =  Sale::where("SCID", $id);
        return DataTables::of($data)
            ->addColumn('action', function ($data) {
                
                return '<a href="'.route("pos.print_thermel_purchase_details", [$data->SaleID]).'" class="btn btn-sm btn-success ">Print Thermal</a>
                <a class="btn btn-sm btn-success print_invoice" data-id="' . $data->SaleID . '">Print</a>
                    <a class="btn btn-sm btn-primary edit_invoice" data-id="'.$data->SaleID.'">Edit</a>
                    <a class="btn btn-sm btn-danger delete_invoice" data-id="'.$data->SaleID.'">Delete</a>';
            })
            ->rawColumns(['action']) // Allow raw HTML for action buttons
            ->make(true);

        
    }

    function customer_previous_balance($customer_id,$date=''){

       // $customer = Customer::where(["sup_cus_details.SCID"=>$customer_id])->first();
        $openingBalance= 0;
        if(!$openingBalance){
            $openingBalance=0;
        }

        $TotalSale = Sale::where(["patient_id"=>$customer_id])

            ->when($date, function ($query) use ($date) {
                return $query->where('Date', '>=', date("Y-m-d",strtotime($date)));
            })
            ->when(session('store_id'),function ($q){
                $q->where('store_id',session('store_id'));
            })
            ->sum('net_amount');

        $TotalReceived = SalePayment::where(["patient_id"=>$customer_id])
            ->when($date, function ($query) use ($date) {
                return $query->where('created_at', '>=', date("Y-m-d",strtotime($date)));
            })->sum('amount');

        $TotalDiscount = Sale::where(["patient_id"=>$customer_id])
            ->when($date, function ($query) use ($date) {
                return $query->where('Date', '>=', date("Y-m-d",strtotime($date)));
            })->sum('Discount');
        /*$TotalPaid = ReceiveablesDetail::where(["SCID"=>$customer_id])
            ->when($date, function ($query) use ($date) {
                return $query->where('Date', '<', date("Y-m-d",strtotime($date)));
            })->sum('Amount');*/


        $TotalAmount = ($openingBalance + $TotalSale) - $TotalReceived;

        if($TotalAmount){
            return $TotalAmount;
        }else{
            return 0;
        }

    }

    function customer_previous_balance_backup($customer_id,$date=''){
        $customer = Customer::where(["sup_cus_details.SCID"=>$customer_id])->first();
        $openingBalance=$customer->OpeningBalance ?? 0;
        if(!$openingBalance){
            $openingBalance=0;
        }

        $TotalSale = Sale::where(["SCID"=>$customer_id])
            ->when($date, function ($query) use ($date) {
                return $query->where('Date', '>=', date("Y-m-d",strtotime($date)));
            })->sum('TotalSale');
        $TotalPaid = ReceiveablesDetail::where(["SCID"=>$customer_id])
            ->when($date, function ($query) use ($date) {
                return $query->where('Date', '<', date("Y-m-d",strtotime($date)));
            })->sum('Amount');

        $TotalAmount = ($openingBalance + $TotalSale) - $TotalPaid;

        if($TotalAmount){
            return $TotalAmount;
        }else{
            return 0;
        }

    }
    
    
}
