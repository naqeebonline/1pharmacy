<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmsHistory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SmsHistoryController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Sms History',

        ];
        $data['data'] =  SmsHistory::with(["user"])->when(auth()->user()->roles->pluck('name')[0] !="Super Admin", function ($q) {

            return $q->where(["user_id"=>auth()->user()->id]);
        })->orderBy("created_at",'desc')->paginate(50);
        foreach ($data['data'] as $key => $value){
            $value->sms_api_response = "<code>$value->sms_api_response</code>";
            $value->sms_status = "";
            if($value->is_sent == 0)
                $value->sms_status = "<b style='color: orange;'>Pending</b>";
            if($value->is_sent == 1)
                $value->sms_status = "<b style='color: green;'>Sent</b>";
            if($value->is_sent == 2)
                $value->sms_status = "<b style='color: red;'>Fail</b>";
        }
        return view('sms_history.list',$data);
    }

    public function listSms()
    {
        $users = SmsHistory::with(["department"])->when(auth()->user()->roles->pluck('name')[0] !="Super Admin", function ($q) {
            return $q->where(["user_id"=>auth()->user()->user_id]);
        });
        //dd($users->get());
        return DataTables::of($users)
            ->addColumn('action', function($cert) {
                    return "";
            })
            ->rawColumns(["action"])
            ->make(true);
    }
}
