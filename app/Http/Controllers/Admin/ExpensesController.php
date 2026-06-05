<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseSub;
use App\Models\GeneralExpense;
use App\Models\Market;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ExpensesController extends Controller
{
    public function add_new_expenses()
    {
        $data["title"] = "Add New Expenses";
        return view("expenses.expenses", $data);
    }
    



    public function expenses_list()
    {
        $res = Expense::where(["IsActive" => 1]);

        return DataTables::of($res)
            ->addColumn('action', function ($cert) {
                $details = json_encode($cert);
                if (in_array(auth()->user()->roles->pluck('name')[0], ["Super Admin", "District Super Admin"])) {
                    $html = '<a href="javascript:void(0)" data-details=\'' . $details . '\' class="btn btn-warning btn-icon btn-sm edit_record" data-name="' . $cert->name . '" data-id="' . $cert->ExpenseID . '"><i class="tf-icons bx bx-pencil"></i></a>';
                    $html .= '<button class="btn btn-danger btn-icon btn-sm delete_record" data-id="' . $cert->ExpenseID . '" type="submit"><i class="bx bx-trash tf-icons"></i></button>';
                } else {
                    $html = "";
                }

                return $html;
            })
            ->rawColumns(["action"])
            ->make(true);
    }

    public function save_expanses()
    {

        Expense::updateOrCreate(
            ["ExpenseID" => request()->id],
            request()->except(["id", "_token"])
        );
        return ["status" => true, "message" => "Record saved successfully"];
    }



    public function sub_expanses()
    {
        $data["title"] = "Add Sub Expenses";
        $data['expense'] = Expense::get();
        return view("expenses.sub-expenses", $data);
    }


    public function sub_expenses_list()
    {
        $res = ExpenseSub::with(["expense"])->where(["IsActive" => 1]);

        return DataTables::of($res)
            ->addColumn('action', function ($cert) {
                $details = json_encode($cert);
                if (in_array(auth()->user()->roles->pluck('name')[0], ["Super Admin", "District Super Admin"])) {
                    $html = '<a href="javascript:void(0)" data-details=\'' . $details . '\' class="btn btn-warning btn-icon btn-sm edit_record" data-id="' . $cert->ESID . '"><i class="tf-icons bx bx-pencil"></i></a>';
                    $html .= '<button class="btn btn-danger btn-icon btn-sm delete_record" data-id="' . $cert->ESID . '" type="submit"><i class="bx bx-trash tf-icons"></i></button>';
                } else {
                    $html = "";
                }

                return $html;
            })
            ->addColumn('expense_name', function ($cert) {
               return $cert->expense->ExpenseTitle;
            })
            ->rawColumns(["expense_name","action"])
            ->make(true);
    }



    public function save_sub_expanses()
    {

        ExpenseSub::updateOrCreate(
            ["ESID" => request()->id],
            request()->except(["id", "_token"])
        );
        return ["status" => true, "message" => "Record saved successfully"];
    }

    
    public function general_expanses(){
        $data["title"] = "Add General Expenses";
        $data['expense'] = Expense::get();
        return view("expenses.general-expenses", $data);
    }


    public function general_expanses_list()
    {
        $res = GeneralExpense::with(["sub_expenses", "sub_expenses.expense"])->where(["IsActive" => 1])->get();
        
        return DataTables::of($res)
        ->addColumn('action', function ($cert) {
            $details = json_encode($cert);
            if (in_array(auth()->user()->roles->pluck('name')[0], ["Super Admin", "District Super Admin"])) {
                $html = '<a href="javascript:void(0)" data-details=\'' . $details . '\' class="btn btn-warning btn-icon btn-sm edit_record" data-id="' . $cert->GXID . '"><i class="tf-icons bx bx-pencil"></i></a>';
                $html .= '<button class="btn btn-danger btn-icon btn-sm delete_record" data-id="' . $cert->GXID . '" type="submit"><i class="bx bx-trash tf-icons"></i></button>';
            } else {
                $html = "";
            }

            return $html;
        })
        ->addColumn('expense_name', function ($cert) {
            return $cert->sub_expenses->expense->ExpenseTitle ?? "";
        })
         
        ->rawColumns(["expense_name","action"])
        ->make(true);
    }
    

    public function get_sub_expanses(Request $request){
        $data = ExpenseSub::where("ExpenseID", $request->id)->get();

        return response()->json([
            "data"=> $data
        ]);
        
    }

    public function save_general_expanses(Request $request){
        GeneralExpense::updateOrCreate(
            ["ESID" => request()->id],
            request()->except(["id", "_token","ExpenseID"])
        );
        return ["status" => true, "message" => "Record saved successfully"];
    }
    
    
    
}
