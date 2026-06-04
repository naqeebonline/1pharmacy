<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Appointments\Appointment;
use App\Models\Finance\DailyUserClosing;
use App\Models\Finance\FinanceHead;
use App\Models\Finance\FinanceTransaction;
use App\Models\Finance\FinanceVoucher;
use App\Models\Patient\InPatientAdmission;
use App\Models\Patient\PatientAdmission;
use App\Models\Patient\PatientInvestigation;
use App\Models\Patient\PatientInvestigationPayment;
use App\Models\Patient\PatientServiceCharges;
use App\Models\PharmacyRetrun;
use App\Models\Sale;
use App\Models\SaleDetails;
use App\Models\SalePayment;
use App\Models\Users;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function daily_closing()
    {



        

        $closing_date = $_GET['closing_date'] ?? date("Y-m-d");
        $user_id = $_GET['user_id'] ?? '';
        $data['user_id'] = $user_id;
        $data['closing_date'] = $closing_date;
        $data['users'] = Users::where("status",1)
            ->when((getUserRole() != 'Super Admin' && getUserRole() != 'Finance'), function ($query) use ($user_id) {
                return $query->where('id',auth()->user()->id);
            })
            ->get();

        $data['finance_heads'] = FinanceHead::where(["name"=>"Cash At Office"])->get();


        $data['data'] = $this->sale_payments($closing_date,$user_id);
        $data['payments_received_from_customer'] = $this->payments_received_from_customer($closing_date,$user_id);


        $data['pharmacy_return'] = $this->total_return_in_pharmacy($closing_date,$user_id);
        $data['appointments'] = $this->appointmentsPayment($closing_date,$user_id);
        $data['investigations'] = $this->investigationPayment($closing_date,$user_id);
        $data['service_charges'] = $this->serviceCharges($closing_date,$user_id);
        $data['in_patient_sale'] = $this->in_patient_sale($closing_date,$user_id);
        $data['payment_received_from_customer'] = $this->payment_received_from_customer($closing_date,$user_id);
        //$data['payment_received_from_customer'] = 0;
        

        $data['consultant_charges'] = $this->consultant_charges($closing_date,$user_id);
        $data['pharmacy_item_returns'] = $this->total_return_in_pharmacy_by_user($closing_date,$user_id);

       // dd($user_id,$data['pharmacy_item_returns']);
        $data['voucher'] = FinanceVoucher::when((getUserRole() != 'Super Admin' && getUserRole() != 'Finance'), function ($query) use ($user_id) {
            return $query->where(["created_by"=>auth()->user()->id]);
        })->with(['user'])->orderBy("id","desc")
            ->where(["voucher_type"=>"closing"])
            ->paginate(20);

       return view("Finance.daily_closing",$data);
    }

    public function view_details()
    {
        $closing_date = $_GET['closing_date'] ?? "";
        $data['from_date'] = $closing_date;
        $data['to_date'] = $closing_date;
        $user_id = $_GET['user_id'] ?? "";
        if($closing_date == "" || $user_id == ""){
            echo "invalid attempt. your id and details are recorded. ".auth()->user()->name;
            exit;
        }
        $data['sale'] = $this->sale_payments($closing_date,$user_id,true);

        $data['pharmacy_item_returns'] = $this->total_return_in_pharmacy_by_user($closing_date,$user_id);
        $data['appointments'] = $this->appointmentsPayment($closing_date,$user_id,true);
        $data['investigations'] = $this->investigationPayment($closing_date,$user_id,true);
//dd($data['sale']);
        return view("Finance.Reports.print_user_transactions_report",$data);
    }

    public function post_daily_closing()
    {

        $not_approve_transaction = FinanceVoucher::where(["voucher_type"=>"closing","created_by"=>auth()->user()->id])->whereNull('approved_by')->first();
        if($not_approve_transaction){
            return redirect()->back()->with("error","Unapproved transaction exist. approve it and then post next transaction");
        }

        $user_id = request()->user_id;
        if($user_id == '' || $user_id == 0){
            return redirect()->back()->with("error","Please Select Closing User");
        }

        $closing_date = request()->closing_date;
        if(request()->finance_head_id == ''){
            echo "Please select account head to post amount";
            exit;
        }


        $query = SalePayment::where("is_posted",0)
            ->where("admission_id","=",0)
            ->when($closing_date, function ($query) use ($closing_date) {
                return $query->whereDate('created_at', '<=', date("Y-m-d", strtotime($closing_date)));
            })
            ->when($user_id, function ($query) use ($user_id) {
                return $query->where('created_by',$user_id);
            });

        $sale_totals = $query->selectRaw('SUM(amount) as received_amount')->first();
        $appointments = $this->appointmentsPayment($closing_date,$user_id);
        $investigations = $this->investigationPayment($closing_date,$user_id);



        $data['payments_received_from_customer'] = $this->payments_received_from_customer($closing_date,$user_id);


        $sale = $sale_totals->received_amount ?? 0;
        $appointments = $appointments->total_fees ?? 0;
        $investigations = $investigations->cash_in_hand ?? 0;
        $pharmacy_return = $this->total_return_in_pharmacy($closing_date,$user_id);
        $service_charges = $this->serviceCharges($closing_date,$user_id);
        $consultant_charges = $this->consultant_charges($closing_date,$user_id);
        $total_amount = ($sale) + ($data['payments_received_from_customer']) + ($appointments) + ($investigations) + ($service_charges) + ($consultant_charges) - ($pharmacy_return);

        
        
        
        
        if($total_amount == 0 && $pharmacy_return == 0){
            return redirect()->back()->with("error","You can not post Zero Amount of user.");
        }


        $voucher = generateVoucherNumber("closing",$user_id);

        $voucher_data = [
            "voucher_number" =>$voucher,
            "voucher_type"   => "closing",
            'user_id' => $user_id,
            'created_by' => auth()->user()->id,
            "voucher_date"   => date("Y-m-d"),
            "total_amount"   => $total_amount,
            "remarks"   => "Daily user closing of ".auth()->user()->name ?? '',
            "created_by"   => auth()->user()->id,
            "created_at"   => date("Y-m-d H:i:s"),
        ];
        $voucher = FinanceVoucher::create($voucher_data);
        $voucher_id = $voucher->id;
        //$voucher_id = 1;

        $record = [];
        

        if($sale > 0){
            $query = SalePayment::where("is_posted",0)
                ->where("admission_id","=",0)
                ->when($closing_date, function ($query) use ($closing_date) {
                    return $query->whereDate('created_at', '<=', date("Y-m-d", strtotime($closing_date)));
                })
                ->when($user_id, function ($query) use ($user_id) {
                    return $query->where('created_by',$user_id);
                })->get();

            //naqeeb

            foreach ($query as $key => $value){
                if($value->sale_id){
                    $cogs_amount = $this->cogs_purchase($value->sale_id);
                }else{
                    $cogs_amount = $this->cogs_purchase_in_patient($value->admission_id);
                }

                $amount = $value->amount;
                $remarks = "Pharamacy Income";
                // cash at office debuit   pharmacy income credit
                make_entry($voucher_id,request()->finance_head_id,$amount,0,"sale",$value->id,$user_id,$remarks);
                make_entry($voucher_id,financeHeadId('pharmacy_income'),0,$amount,"sale",$value->id,$user_id,$remarks);

                $remarks = "pharmacy_sale_cogs";
                // cash at office debuit   pharmacy_purchase credit
                make_entry($voucher_id,financeHeadId('cogs'),$cogs_amount,0,"pharmacy_sale_cogs",$value->id,$user_id,$remarks);
                make_entry($voucher_id,financeHeadId('pharmacy_purchase'),0,$cogs_amount,"pharmacy_sale_cogs",$value->id,$user_id,$remarks);

            }

          
        }

          if($data['payments_received_from_customer'] > 0){
             
             
            $query = SalePayment::with(["patient"])->when($closing_date, function ($query) use ($closing_date) {
                return $query->whereDate('created_at', '<=', date("Y-m-d", strtotime($closing_date)));
            })
            ->where("sale_id","=",0)
            ->where("is_posted","=",0)
            ->where("is_active",1)
            ->when($user_id, function ($query) use ($user_id) {
                return $query->where('created_by',$user_id);
            })->get();

            foreach ($query as $key => $value){
                $amount = $value->amount;
                $remarks = "Cash Received from Customer: ".$value->patient->name ?? "";
                // cash at office debuit   Customer credit
                make_entry($voucher_id,financeHeadId('cash_at_office'),$amount,0,"sale_payments",$value->id,$user_id,$remarks);
                make_entry($voucher_id,$value->patient->finance_head_id,0,$amount,"sale_payments",$value->id,$user_id,$remarks);
            }
                
            }


        if($pharmacy_return > 0){
            $return = $query = PharmacyRetrun::where("pharmacy_return_items.is_posted", 0)
                ->select("pharmacy_return_items.*", "sale.CreatedBy as bill_user")
                ->join("sale", "sale.SaleID", "=", "pharmacy_return_items.sale_id")
                ->where(function ($q) use ($user_id) {
                    $q->where("sale.CreatedBy", "!=", $user_id)
                        ->orWhere("sale.is_posted", 1);
                })
                ->when($closing_date, function ($query) use ($closing_date) {
                    return $query->whereDate(
                        'pharmacy_return_items.created_at',
                        '<=',
                        date("Y-m-d", strtotime($closing_date))
                    );
                })
                ->when($user_id, function ($query) use ($user_id) {
                    return $query->where('pharmacy_return_items.created_by', $user_id);
                })->get();
            foreach ($return as $key => $value){
                $amount = $value->amount;
                $remarks = "Pharmacy Return";

                // pharmacy_return debit     cash at office credit
                make_entry($voucher_id,financeHeadId('pharmacy_return'),$amount,0,"pharmacy_return",$value->id,$user_id,$remarks);
                make_entry($voucher_id,request()->finance_head_id,0,$amount,"pharmacy_return",$value->id,$user_id,$remarks);

                $cost_of_good_sales_of_return_item = $this->cogs_after_return($value->id);
                // pharmacy_purchase debit    cogs credit
                make_entry($voucher_id,financeHeadId('pharmacy_purchase'),$cost_of_good_sales_of_return_item,0,"cogs_pharmacy_sale_return",$value->id,$user_id,$remarks);
                make_entry($voucher_id,financeHeadId('cogs'),0,$cost_of_good_sales_of_return_item,"cogs_pharmacy_sale_return",$value->id,$user_id,$remarks);



            }
        }
       

      //  FinanceTransaction::insert($record);

        $remarks = "Closing done by ".auth()->user()->name." on ".date("Y-m-d H:i:s");
        DailyUserClosing::create([
            "user_id"=>auth()->user()->id,
            "closing_date"=>$closing_date,
            "investigation_amount"=>$investigations,
            "sale_amount"=>$sale,
            "appointment_amount"=>$appointments,
            "total_amount"=>$total_amount,
            "remarks"=>$remarks
            ]);

        return redirect()->back()->with('success', 'Record Posted Successfully.');
    }

    public function sale_payments($closing_date='',$user_id='',$get_result=false)
    {
        $query = SalePayment::where("is_posted",0)
            ->when($closing_date, function ($query) use ($closing_date) {
                return $query->whereDate('created_at', '<=', date("Y-m-d", strtotime($closing_date)));
            })
            ->where("sale_id","!=",0)
            ->where("is_active",1)
            ->when($user_id, function ($query) use ($user_id) {
                return $query->where('created_by',$user_id);
            });
        if($get_result){
            $totals = $query->with(["patient","sale","createdBy"])->get();
            return $totals;
        }else{
            $totals = $query->selectRaw('SUM(amount) as received_amount')->first();
            return $totals;
        }

    }

     public function payments_received_from_customer($closing_date='',$user_id='',$get_result=false)
    {
        $query = SalePayment::when($closing_date, function ($query) use ($closing_date) {
                return $query->whereDate('created_at', '<=', date("Y-m-d", strtotime($closing_date)));
            })
            ->where("sale_id","=",0)
            ->where("is_posted","=",0)
            ->where("is_active",1)
            ->when($user_id, function ($query) use ($user_id) {
                return $query->where('created_by',$user_id);
            });
        if($get_result){
            $totals = $query->with(["patient","sale","createdBy"])->get();
            return $totals;
        }else{
            $totals = $query->selectRaw('SUM(amount) as received_amount')->first();
            return $totals->received_amount ?? 0;
        }

    }

    public function total_return_in_pharmacy($closing_date='',$user_id='',$get_result=false)
    {
        $query = $query = PharmacyRetrun::where("pharmacy_return_items.is_posted", 0)
            ->select("pharmacy_return_items.*", "sale.CreatedBy as bill_user","sale.InvoiceNo")
            ->join("sale", "sale.SaleID", "=", "pharmacy_return_items.sale_id")
            ->where(function ($q) use ($user_id) {
                $q->where("sale.CreatedBy", "!=", $user_id)
                    ->orWhere("sale.is_posted", 1);
            })
            ->when($closing_date, function ($query) use ($closing_date) {
                return $query->whereDate(
                    'pharmacy_return_items.created_at',
                    '<=',
                    date("Y-m-d", strtotime($closing_date))
                );
            })
            ->when($user_id, function ($query) use ($user_id) {
                return $query->where('pharmacy_return_items.created_by', $user_id);
            });

        if($get_result){
            $totals = $query->with(["patient","product","createdBy"])->get();
            return $totals;
        }else{
            return $query->sum('amount');
        }


    }

    public function appointmentsPayment($closing_date='',$user_id='',$get_result=false)
    {
        $query = Appointment::where("is_posted",0)
            ->when($closing_date, function ($query) use ($closing_date) {
                return $query->whereDate('appointment_date', '<=', date("Y-m-d", strtotime($closing_date)));
            })
            ->where("is_active",1)
            ->when($user_id, function ($query) use ($user_id) {
                return $query->where('created_by',$user_id);
            });
        if($get_result){
            $totals = $query->with(["patient","opd_type","consultant"])->get();
            return $totals;
        }else{
            $totals = $query->selectRaw('SUM(fee) as total_fees, SUM(hospital_share) as total_hospital_share, SUM(consultant_share) as total_consultant_share')->first();
            return $totals;
        }

    }

    public function investigationPayment($closing_date='',$user_id='',$get_result=false)
    {
        $query = PatientInvestigationPayment::where("is_posted",0)
            //->whereNull("admission_id")
            ->when($closing_date, function ($query) use ($closing_date) {
                return $query->whereDate('created_at', '<=', date("Y-m-d", strtotime($closing_date)));
            })
            ->where("is_active",1)
            ->when($user_id, function ($query) use ($user_id) {
                return $query->where('created_by',$user_id);
            });
        if($get_result){
            $totals = $query->with(["patient","createdBy"])->get();
            return $totals;
        }else{
            $totals = $query->selectRaw('SUM(amount) as cash_in_hand')->first();
            //dd($totals);
            return $totals;
        }

    }

    public function serviceCharges($closing_date='',$user_id='')
    {
        $query = PatientServiceCharges::where("patient_service_charges.is_posted",0)
            ->leftJoin("in_patient_admissions","in_patient_admissions.id","=","patient_service_charges.admission_id")
            //->whereNull("admission_id")
            ->when($closing_date, function ($query) use ($closing_date) {
                return $query->whereDate('patient_service_charges.service_date', '<=', date("Y-m-d", strtotime($closing_date)));
            })
            ->where("patient_service_charges.is_active",1)
            ->where("patient_service_charges.patient_type",'in_patient')
            ->whereIn("in_patient_admissions.admission_status",["Discharged","Reffered","Canceled"])
            ->when($user_id, function ($query) use ($user_id) {
                return $query->where('patient_service_charges.created_by',$user_id);
            });

        $totals = $query->sum('service_rate');

        //dd($totals);
        return $totals ?? 0;
    }

    public function update_post_status($closing_date,$user_id)
    {

        $all_admissions = InPatientAdmission::with(['consultant'])
            ->where("is_posted",0)
            ->whereIn("admission_status",["Discharged","Reffered","Canceled"])
            ->when($closing_date, function ($query) use ($closing_date) {
                return $query->whereDate('admission_date', '<=', date("Y-m-d", strtotime($closing_date)));
            })
            ->when($user_id, function ($query) use ($user_id) {
                return $query->where('discharge_by',$user_id);
            })->update(["is_posted"=>1,"posted_on"=>date("Y-m-d")]);

        SalePayment::where("is_posted",0)
            ->when($closing_date, function ($query) use ($closing_date) {
                return $query->whereDate('created_at', '<=', date("Y-m-d", strtotime($closing_date)));
            })
            ->where("is_active",1)
            ->when($user_id, function ($query) use ($user_id) {
                return $query->where('created_by',$user_id);
            })->update(["is_posted"=>1,"posted_on"=>date("Y-m-d"),"updated_by"=>auth()->user()->id,"updated_at"=>date("Y-m-d H:i:s")]);


            
        Appointment::where("is_posted",0)
            ->when($closing_date, function ($query) use ($closing_date) {
                return $query->whereDate('appointment_date', '<=', date("Y-m-d", strtotime($closing_date)));
            })
            ->where("is_active",1)
            ->when($user_id, function ($query) use ($user_id) {
                return $query->where('created_by',$user_id);
            })->update(["is_posted"=>1,"posted_on"=>date("Y-m-d"),"updated_by"=>auth()->user()->id,"updated_at"=>date("Y-m-d H:i:s")]);

        PatientInvestigationPayment::where("is_posted",0)
            //->whereNull("admission_id")
            ->when($closing_date, function ($query) use ($closing_date) {
                return $query->whereDate('created_at', '<=', date("Y-m-d", strtotime($closing_date)));
            })
            ->where("is_active",1)
            ->when($user_id, function ($query) use ($user_id) {
                return $query->where('created_by',$user_id);
            })->update(["is_posted"=>1,"posted_on"=>date("Y-m-d"),"updated_by"=>auth()->user()->id,"updated_at"=>date("Y-m-d H:i:s")]);

        PatientInvestigation::with(['consultant'])
            ->where("is_posted",0)
            ->where("is_active",1)
            //->whereNull("admission_id")
            ->when($closing_date, function ($query) use ($closing_date) {
                return $query->whereDate('created_at', '<=', date("Y-m-d", strtotime($closing_date)));
            })
            ->where("is_active",1)
            ->when($user_id, function ($query) use ($user_id) {
                return $query->where('created_by',$user_id);
            })->update(["is_posted"=>1,"posted_on"=>date("Y-m-d"),"updated_by"=>auth()->user()->id,"updated_at"=>date("Y-m-d H:i:s")]);

       PatientServiceCharges::where("is_posted",0)
            ->where("is_active",1)
            ->with(['service_type'])
            ->when($closing_date, function ($query) use ($closing_date) {
                return $query->whereDate('service_date', '<=', date("Y-m-d", strtotime($closing_date)));
            })
            ->when($user_id, function ($query) use ($user_id) {
                return $query->where('created_by',$user_id);
            })->update(["is_posted"=>1,"posted_on"=>date("Y-m-d"),"updated_by"=>auth()->user()->id,"updated_at"=>date("Y-m-d H:i:s")]);


        PharmacyRetrun::where("is_posted",0)
            //->whereNull("admission_id")
            ->when($closing_date, function ($query) use ($closing_date) {
                return $query->whereDate('created_at', '<=', date("Y-m-d", strtotime($closing_date)));
            })
            ->when($user_id, function ($query) use ($user_id) {
                return $query->where('created_by',$user_id);
            })->update(["is_posted"=>1,"posted_on"=>date("Y-m-d"),"updated_by"=>auth()->user()->id,"updated_at"=>date("Y-m-d H:i:s")]);



        Sale::where("is_posted",0)->when($closing_date, function ($query) use ($closing_date) {
            return $query->whereDate('CreatedAt', '<=', date("Y-m-d", strtotime($closing_date)));
        })
            ->when($user_id, function ($query) use ($user_id) {
                return $query->where('CreatedBy',$user_id);
            })->update(["is_posted"=>1,"posted_on"=>date("Y-m-d"),"ModifiedBy"=>auth()->user()->id,"ModifiedAt"=>date("Y-m-d")]);


        return true;
    }


    public function cash_payment_voucher()
    {
        $data['finance_heads'] = FinanceHead::whereIn("type",["asset","liability","expense","income"])->get();
        $data['sub_heads'] = FinanceHead::whereIn("type",["asset","liability","expense","income"])->get();
        $data['vouchers'] = FinanceVoucher::orderBy("id","DESC")->where("voucher_type","payment")->paginate(30);





        return view("Finance.cash_payment_voucher",$data);
    }

    public function save_cash_payment_voucher()
    {

        $amount = request()->amount;
        $voucher = generateVoucherNumber("Payment",auth()->user()->id);

        $voucher_data = [
            "voucher_number" =>$voucher,
            "user_id"   =>  auth()->user()->id,
            "voucher_type"   => "payment",
            "voucher_date"   => date("Y-m-d"),
            "total_amount"   => $amount,
            "remarks"   =>    request()->remarks.". Paid by ".auth()->user()->name,
            "created_by"   => auth()->user()->id,
            "created_at"   => date("Y-m-d H:i:s"),
        ];
        $voucher = FinanceVoucher::create($voucher_data);
        $voucher_id = $voucher->id;

        $remarks = request()->remarks.". Payment to ".financeHeadName(request()->debit_head_id)." From ".financeHeadName(request()->credit_head_id)." by ".auth()->user()->name;
        make_entry($voucher_id,request()->debit_head_id,$amount,0,"cash_payment_voucher",NULL,auth()->user()->id,$remarks);
        make_entry($voucher_id,request()->credit_head_id,0,$amount,"cash_payment_voucher",NULL,auth()->user()->id,$remarks);


        return redirect()->back()->with('success', 'Record saved successfully.');
    }



    public function cash_receipt_voucher()
    {
        $data['finance_heads'] = FinanceHead::whereIn("type",["liability","asset"])->get();
        $data['sub_heads'] = FinanceHead::whereIn("type",["liability"])->get();
        $data['vouchers'] = FinanceVoucher::orderBy("id","DESC")->where("voucher_type","receipt")->paginate(30);

        return view("Finance.cash_receipt_voucher",$data);
    }

    public function save_cash_receipt_voucher()
    {
        $amount = request()->amount;
        $voucher = generateVoucherNumber("Receipt",auth()->user()->id);

        $voucher_data = [
            "voucher_number" =>$voucher,
            "user_id"   =>  auth()->user()->id,
            "voucher_type"   => "receipt",
            "voucher_date"   => date("Y-m-d"),
            "total_amount"   => $amount,
            "remarks"   => request()->remarks.". Payment received by ".auth()->user()->name,
            "created_by"   => auth()->user()->id,
            "created_at"   => date("Y-m-d H:i:s"),
        ];
        $voucher = FinanceVoucher::create($voucher_data);
        if($amount > 0){
            $voucher_id = $voucher->id;

            $remarks = request()->remarks."- Received from ".financeHeadName(request()->credit_head_id)." by".auth()->user()->name;
            make_entry($voucher_id,request()->debit_head_id,$amount,0,"cash_payment_voucher",NULL,auth()->user()->id,$remarks);
            make_entry($voucher_id,request()->credit_head_id,0,$amount,"cash_payment_voucher",NULL,auth()->user()->id,$remarks);
        }


        return redirect()->back()->with('success', 'Record saved successfully.');
    }

    public function journal_voucher()
    {
        $data['finance_heads'] = FinanceHead::get();
        $data['sub_heads'] = FinanceHead::get();
        $data['vouchers'] = FinanceVoucher::orderBy("id","DESC")->where("voucher_type","journal_voucher")->paginate(30);
        return view("Finance.journal_voucher",$data);
    }

    public function save_journal_voucher()
    {

        $amount = request()->amount;
        $voucher = generateVoucherNumber("journal_voucher",auth()->user()->id);

        $voucher_data = [
            "voucher_number" =>$voucher,
            "user_id"   =>  auth()->user()->id,
            "voucher_type"   => "journal_voucher",
            "voucher_date"   => date("Y-m-d"),
            "total_amount"   => $amount,
            "remarks"   =>    request()->remarks.". Paid by ".auth()->user()->name,
            "created_by"   => auth()->user()->id,
            "created_at"   => date("Y-m-d H:i:s"),
        ];
        $voucher = FinanceVoucher::create($voucher_data);
        $voucher_id = $voucher->id;

        $remarks = request()->remarks.". Payment to ".financeHeadName(request()->debit_head_id)." From ".financeHeadName(request()->credit_head_id)." by ".auth()->user()->name;
        make_entry($voucher_id,request()->debit_head_id,$amount,0,"journal_voucher",NULL,auth()->user()->id,$remarks);
        make_entry($voucher_id,request()->credit_head_id,0,$amount,"journal_voucher",NULL,auth()->user()->id,$remarks);


        return redirect()->back()->with('success', 'Record saved successfully.');
    }

    public function getBalance()
    {
        $totals = DB::table('finance_transactions')
            ->select(
                'head_id',
                DB::raw('SUM(debit) as total_debit'),
                DB::raw('SUM(credit) as total_credit')
            )
            ->where('is_active', 1)
            ->groupBy('head_id');

        $report = FinanceHead::leftJoinSub($totals, 'totals', 'finance_heads.id', '=', 'totals.head_id')
            ->select(
                'finance_heads.id',
                'finance_heads.name',
                'finance_heads.type',
                DB::raw('COALESCE(totals.total_debit, 0) as total_debit'),
                DB::raw('COALESCE(totals.total_credit, 0) as total_credit')
            )
            ->when(request()->filled('id'), function ($query) {
                $query->where('finance_heads.id', request()->id);
            })
            ->get()
            ->map(function ($head) {
                if (in_array($head->type, ['asset', 'expense'])) {
                    $head->balance = $head->total_debit - $head->total_credit;
                } else {
                    $head->balance = $head->total_credit - $head->total_debit;
                }
                return $head;
            });

        return $report[0]['balance'] ?? 0;
    }


    public function in_patient_sale($closing_date='',$user_id='')
    {
        $query = Sale::where("is_posted",0)->when($closing_date, function ($query) use ($closing_date) {
                return $query->whereDate('CreatedAt', '<=', date("Y-m-d", strtotime($closing_date)));
            })
            ->where('admission_id',"!=",0)
            ->when($user_id, function ($query) use ($user_id) {
                return $query->where('CreatedBy',$user_id);
            });

        $totals = $query->selectRaw('SUM(TotalSale) - SUM(Discount) as in_patient_sale')->first();
        return $totals->in_patient_sale ?? 0;
    }
  
    public function payment_received_from_customer($closing_date='',$user_id='')
    {
        $query = SalePayment::where("is_posted",0)
            ->when($closing_date, function ($query) use ($closing_date) {
                return $query->whereDate('created_at', '<=', date("Y-m-d", strtotime($closing_date)));
            })
            ->where("admission_id",1)
            ->where("is_active",1)
            ->when($user_id, function ($query) use ($user_id) {
                return $query->where('created_by',$user_id);
            });
            $totals = $query->selectRaw('SUM(amount) as received_amount')->first();
            return $totals->received_amount ?? 0;      
    }

    public function consultant_charges($closing_date='',$user_id='')
    {



        $total = InPatientAdmission::with(['consultant'])
            ->where("is_posted",0)
            ->whereIn("admission_status",["Discharged"])
            ->when($closing_date, function ($query) use ($closing_date) {
                return $query->whereDate('admission_date', '<=', date("Y-m-d", strtotime($closing_date)));
            })
            ->when($user_id, function ($query) use ($user_id) {
                return $query->where('discharge_by',$user_id);
            })->sum('consultant_charges');


        return $total;


    }

    public function total_return_in_pharmacy_by_user($closing_date='',$user_id='')
    {

        $data = SaleDetails::with(['product','return_by_user'])
            ->leftJoin("sale","sale.SaleID","=","sale_details.SaleID")
            ->leftJoin("pharmacy_return_items","sale.SaleID","=","pharmacy_return_items.sale_id")
            ->select("sale_details.*","sale.*","users.name","pharmacy_return_items.amount as returnAmount")
            ->leftJoin("users","users.id","=","sale.ModifiedBy")
            ->where("sale_details.return_by",$user_id)
            ->whereDate("sale.ModifiedAt",$closing_date)
            ->get();
        return $data;
    }

    public function approve_transaction_entry()
    {
        $voucher = FinanceVoucher::where("id",request()->id)->first();
        FinanceVoucher::where(["id"=>request()->id])->update(["approved_by"=>auth()->user()->id,"approved_at"=>date("Y-m-d H:i:s")]);

        if($voucher->voucher_type == 'closing'){
            $this->update_post_status($voucher->voucher_date,$voucher->user_id);
        }

        return ["status"=>true,"message"=>"record approved successfully"];
    }

    public function delete_transaction_entry()
    {
        FinanceVoucher::where(["id"=>request()->id])->delete();
        FinanceTransaction::where(["voucher_id"=>request()->id])->delete();
        return ["status"=>true,"message"=>"record approved successfully"];
    }

    public function cogs_purchase($sale_id)
    {
        $cogs = DB::table('sale_details')
            ->selectRaw('SaleID, SUM((Quantity - ReturnQuantity) * PurchasePrice) as cogs')
            ->where('SaleID', $sale_id)
            ->groupBy('SaleID')
            ->first();
        return $cogs->cogs ?? 0;
    }



    public function cogs_purchase_in_patient($admission_id)
    {
        $sale_ids = Sale::where("admission_id",$admission_id)->pluck('SaleID');
        $cogs = DB::table('sale_details')
            ->selectRaw('SaleID, SUM((Quantity - ReturnQuantity) * PurchasePrice) as cogs')
            ->whereIn('SaleID', $sale_ids)
            ->groupBy('SaleID')
            ->first();
        return $cogs->cogs ?? 0;
    }

    public function cogs_after_return($pharmacy_return_id)
    {
        $return = PharmacyRetrun::where("id",$pharmacy_return_id)->first();
        $return_qty = $return->quantity;
        $cogs = DB::table('sale_details')
            ->where('SaleID', $return->sale_id)
            ->where('ProductID', $return->product_id)
            ->first();

        return ($return_qty ?? 0) * ($cogs->PurchasePrice ?? 0);

    }

    public function create()
    {
       // $finance_heads = FinanceHead::whereNotNul('parent_id')->get();
        $finance_heads = FinanceHead::whereNotNull('parent_id')->get();
        $vouchers = FinanceVoucher::orderBy("id","DESC")->where("voucher_type","journal_voucher")->paginate(30);
        return view('Finance.journal_voucher', compact('finance_heads','vouchers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'entries' => 'required'
        ]);

        $entries = json_decode($request->entries, true);

        if(empty($entries)){
            return back()->with('error', 'No entries found.');
        }
        $remarks = "";
        foreach($entries as $entry){
            $remarks = $remarks.$entry['remarks']." , ";
        }

        $totalAmount = collect($entries)->where('type', 'credit')->sum('amount');
       // dd($totalAmount);


            $voucher = generateVoucherNumber("journal_voucher",auth()->user()->id);

            $voucher_data = [
                "voucher_number" =>$voucher,
                "user_id"   =>  auth()->user()->id,
                "voucher_type"   => "journal_voucher",
                "voucher_date"   => date("Y-m-d"),
                "total_amount"   => $totalAmount,
                "remarks"   =>    "JV Created by ".auth()->user()->name,
                "created_by"   => auth()->user()->id,
                "created_at"   => date("Y-m-d H:i:s"),
            ];
            $voucher = FinanceVoucher::create($voucher_data);
            $voucher_id = $voucher->id;

            foreach($entries as $entry){

                FinanceTransaction::create([
                    'voucher_id' => $voucher_id,
                    'transaction_date' => date("Y-m-d"),
                    'reference_type' => "journal_voucher",
                    'reference_id' => 0,
                    'head_id' => $entry['head_id'],
                    'debit' => $entry['type'] === 'debit' ? $entry['amount'] : 0,
                    'credit' => $entry['type'] === 'credit' ? $entry['amount'] : 0,
                    'remarks' => $entry['remarks'],
                    'user_id' => auth()->user()->id,
                    'created_by' => auth()->user()->id,
                    'created_at' => date("Y-m-d H:i:s"),
                ]);
            }


        return redirect()->route('pos.journal_voucher')->with('success', 'Journal Voucher Posted');
    }



}
