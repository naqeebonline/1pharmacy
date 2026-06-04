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
use App\Models\SalePayment;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function daily_closing()
    {
        $closing_date = $_GET['closing_date'] ?? date("Y-m-d");
        $user_id = $_GET['user_id'] ?? 0;
        $data['user_id'] = $user_id;
        $data['closing_date'] = $closing_date;
        $data['users'] = Users::where("status",1)->get();
        $data['finance_heads'] = FinanceHead::get();

        $query = SalePayment::where("is_posted",0)
            ->when($closing_date, function ($query) use ($closing_date) {
                return $query->whereDate('created_at', '<=', date("Y-m-d", strtotime($closing_date)));
            })
            ->where("is_active",1)
            ->when($user_id, function ($query) use ($user_id) {
                return $query->where('created_by',$user_id);
            });

        $totals = $query->selectRaw('SUM(amount) as received_amount')->first();
        $data['data'] = $totals;


        $data['pharmacy_return'] = $this->total_return_in_pharmacy($closing_date,$user_id);
        $data['appointments'] = $this->appointmentsPayment($closing_date,$user_id);
        $data['investigations'] = $this->investigationPayment($closing_date,$user_id);
        $data['service_charges'] = $this->serviceCharges($closing_date,$user_id);
        $data['in_patient_sale'] = $this->in_patient_sale($closing_date,$user_id);
        $data['consultant_charges'] = $this->consultant_charges($closing_date,$user_id);
        $data['voucher'] = FinanceVoucher::where(["created_by"=>auth()->user()->id,"voucher_type"=>"closing"])->with(['user'])->orderBy("id","desc")->paginate(20);

       return view("Finance.daily_closing",$data);
    }

    public function post_daily_closing()
    {


        $not_approve_transaction = FinanceVoucher::where(["voucher_type"=>"closing","created_by"=>auth()->user()->id])->whereNull('approved_by')->first();
        if($not_approve_transaction){
            return redirect()->back()->with("error","Unapproved transaction exist. approve it and then post next transaction");
        }


        $user_id = request()->user_id;

        $closing_date = request()->closing_date;
        if(request()->finance_head_id == ''){
            echo "Please select account head to post amount";
            exit;
        }
        $query = SalePayment::where("is_posted",0)
            ->when($closing_date, function ($query) use ($closing_date) {
                return $query->whereDate('created_at', '<=', date("Y-m-d", strtotime($closing_date)));
            })
            ->when($user_id, function ($query) use ($user_id) {
                return $query->where('created_by',$user_id);
            });

        $sale_totals = $query->selectRaw('SUM(amount) as received_amount')->first();
        $appointments = $this->appointmentsPayment($closing_date,$user_id);
        $investigations = $this->investigationPayment($closing_date,$user_id);





        $sale = $sale_totals->received_amount ?? 0;
        $appointments = $appointments->total_fees ?? 0;
        $investigations = $investigations->cash_in_hand ?? 0;
        $pharmacy_return = $this->total_return_in_pharmacy($closing_date,$user_id);
        $service_charges = $this->serviceCharges($closing_date,$user_id);
        $consultant_charges = $this->consultant_charges($closing_date,$user_id);
        $total_amount = ($sale) + ($appointments) + ($investigations) + ($service_charges) + ($consultant_charges);

        $voucher = generateVoucherNumber("closing",$user_id);

       /* $voucher_data = [
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
        $voucher = FinanceVoucher::create($voucher_data);*/

        $record = [];

        if($sale > 0){
            $query = SalePayment::where("is_posted",0)
                ->when($closing_date, function ($query) use ($closing_date) {
                    return $query->whereDate('created_at', '<=', date("Y-m-d", strtotime($closing_date)));
                })
                ->when($user_id, function ($query) use ($user_id) {
                    return $query->where('created_by',$user_id);
                })->get();

            foreach ($query as $key => $value){
                $amount = $value->amount;
                // cash at office debuit   pharmacy income credit
                make_entry(1,request()->finance_head_id,$amount,0,"sale",$value->id,$user_id,"Pharamacy Income");
                make_entry(1,financeHeadId('pharmacy_income'),0,$amount,"sale",$value->id,$user_id,"Pharamacy Income");
                /*array_push($record,[
                    'transaction_date' => today(),
                    'voucher_id' => $voucher->id,
                    'amount' => ($value->amount),
                    'debit_head_id' => request()->finance_head_id,  //cash at office
                    'credit_head_id' => financeHeadId('pharmacy_income'), // pharmacy income
                    'reference_type' => 'sale_payments',
                    'reference_id' => $value->id,
                    'user_id' => $user_id,
                    'created_by' => auth()->user()->id,
                    'remarks' => 'Pharmacy Sale posted by '.auth()->user()->name,
                    'created_at' => now()
                ]);*/
            }
        }


        if($pharmacy_return > 0){
            $return = $query = PharmacyRetrun::where("is_posted",0)
                ->when($closing_date, function ($query) use ($closing_date) {
                    return $query->whereDate('created_at', '<=', date("Y-m-d", strtotime($closing_date)));
                })
                ->when($user_id, function ($query) use ($user_id) {
                    return $query->where('created_by',$user_id);
                })->get();
            foreach ($return as $key => $value){
                array_push($record,[
                    'voucher_id' => $voucher->id,
                    'transaction_date' => today(),
                    'amount' => $value->amount, // return amount
                    'debit_head_id' => financeHeadId('pharmacy_income'), // reduce income
                    'credit_head_id' => request()->finance_head_id, // reduce cash
                    'reference_type' => 'pharmacy_return',
                    'reference_id' => $value->id,
                    'user_id' => $user_id,
                    'created_by' => auth()->user()->id,
                    'remarks' => 'Pharmacy return (cash refunded)',
                    'created_at' => now()
                ]);
            }


        }
        if($appointments > 0){

            $all_appointments = Appointment::with(['consultant'])
                ->where("is_posted",0)
                ->where("is_active",1)
                ->when($closing_date, function ($query) use ($closing_date) {
                    return $query->whereDate('appointment_date', '<=', date("Y-m-d", strtotime($closing_date)));
                })
                ->when($user_id, function ($query) use ($user_id) {
                    return $query->where('created_by',$user_id);
                })->get();



            foreach ($all_appointments as $key => $value){

                //-- after posting cash to cashAtOffice here the docotro commision will post to doctor account  ---//
                if($value->consultant_share > 0){

                    $rec = [
                        'voucher_id' => $voucher->id,
                        'transaction_date' => today(),
                        'amount' => $value->consultant_share,
                        //'debit_head_id' => financeHeadId('doctor_share'),  // Doctor commision expense
                        'debit_head_id' => request()->finance_head_id,  // cash at office
                        'credit_head_id' => $value->consultant->finance_head_id,// Dr. Naqeeb Ahmad (Liability)
                        'reference_type' => 'appointments',
                        'reference_id' => $value->id,
                        'user_id' => $user_id,
                        'created_by' => auth()->user()->id,
                        'remarks' => 'Appointment Share posted to Doctor: '.$value->consultant->name ?? "".' Account. posted by '.auth()->user()->name,
                        'created_at' => now()
                    ];
                    array_push($record,$rec);
                    $rec = [
                        'voucher_id' => $voucher->id,
                        'transaction_date' => today(),
                        'amount' => $value->hospital_share,
                        //'debit_head_id' => financeHeadId('doctor_share'),  // Doctor commision expense
                        'debit_head_id' => request()->finance_head_id,  // cash at office
                        'credit_head_id' => financeHeadId('appointment_income'), // Appointments income
                        'reference_type' => 'appointments',
                        'reference_id' => $value->id,
                        'user_id' => $user_id,
                        'created_by' => auth()->user()->id,
                        'remarks' => 'Appointment Share posted to Doctor: '.$value->consultant->name ?? "".' Account. posted by '.auth()->user()->name,
                        'created_at' => now()
                    ];
                    array_push($record,$rec);
                }else{
                    $rec = [
                        'voucher_id' => $voucher->id,
                        'transaction_date' => today(),
                        'amount' => $value->fee,
                        'debit_head_id' => request()->finance_head_id,  // cash at office
                        'credit_head_id' => financeHeadId('appointment_income'), // Appointments income
                        'reference_type' => 'appointments',
                        'reference_id' => $value->id,
                        'user_id' => $user_id,
                        'created_by' => auth()->user()->id,
                        'remarks' => 'Appointment Share posted to Doctor: '.$value->consultant->name ?? "".' Account. posted by '.auth()->user()->name,
                        'created_at' => now()
                    ];
                    array_push($record,$rec);
                }

            }

        }

        if($investigations > 0){
            $query = PatientInvestigationPayment::where("is_posted",0)
                ->where("is_active",1)
                //->whereNull("admission_id")
                ->when($closing_date, function ($query) use ($closing_date) {
                    return $query->whereDate('created_at', '<=', date("Y-m-d", strtotime($closing_date)));
                })
                ->where("is_active",1)
                ->when($user_id, function ($query) use ($user_id) {
                    return $query->where('created_by',$user_id);
                })->get();
            foreach ($query as $key => $value){
                array_push($record,[
                    'voucher_id' => $voucher->id,
                    'transaction_date' => today(),
                    'amount' => $value->amount,
                    'debit_head_id' => request()->finance_head_id,  //cash at office
                    'credit_head_id' => financeHeadId('investigation_income'), //investigation_income
                    'reference_type' => 'patient_investigations_payments',
                    'reference_id' => $value->id,
                    'user_id' => $user_id,
                    'created_by' => auth()->user()->id,
                    'remarks' => 'Investigation Income posted to cash at office. Posted by '.auth()->user()->name,
                    'created_at' => now()
                ]);
            }
        }

        if($service_charges > 0){
            $query = PatientServiceCharges::where("patient_service_charges.is_posted",0)
                ->whereIn("in_patient_admissions.admission_status",["Discharged","Reffered","Canceled"])
                ->leftJoin("in_patient_admissions","in_patient_admissions.id","=","patient_service_charges.admission_id")
                //->whereNull("admission_id")
                ->when($closing_date, function ($query) use ($closing_date) {
                    return $query->whereDate('patient_service_charges.service_date', '<=', date("Y-m-d", strtotime($closing_date)));
                })

                ->when($user_id, function ($query) use ($user_id) {
                    return $query->where('patient_service_charges.created_by',$user_id);
                })->get();
            foreach ($query as $key => $value){
              if($value->service_rate > 0){
                  array_push($record,[
                      'voucher_id' => $voucher->id,
                      'transaction_date' => today(),
                      'amount' => $value->service_rate,
                      'debit_head_id' => request()->finance_head_id,  //cash at office
                      'credit_head_id' => $value->service_type->finance_head_id, // service_head id
                      //'reference_type' => financeHeadCode($value->service_type->finance_head_id),
                      'reference_type' => financeHeadCode($value->service_type->finance_head_id),
                      'reference_id' => $value->id,
                      'user_id' => $user_id,
                      'created_by' => auth()->user()->id,
                      'remarks' => $value->service_type->name." Payment Posted to cash at office. posted by ".auth()->user()->name,
                      'created_at' => now()
                  ]);
              }

            }

            //-------- doctor account will credit from procedure percentage of admission  -----------//
            $all_admissions = InPatientAdmission::with(['consultant'])
                ->where("is_posted",0)
                ->whereIn("admission_status",["Discharged"])
                ->when($closing_date, function ($query) use ($closing_date) {
                    return $query->whereDate('admission_date', '<=', date("Y-m-d", strtotime($closing_date)));
                })
                ->when($user_id, function ($query) use ($user_id) {
                    return $query->where('discharge_by',$user_id);
                })->get();
          //  dd($all_admissions);
            foreach ($all_admissions as $key => $value){
                if($value->consultant_share_amount > 0){
                    $rec = [
                        'voucher_id' => $voucher->id,
                        'transaction_date' => today(),
                        'amount' => $value->consultant_share_amount,
                        //'debit_head_id' => financeHeadId('doctor_share'),  // Doctor commision expense
                        'debit_head_id' => request()->finance_head_id,  // cash at office
                        'credit_head_id' => $value->consultant->finance_head_id, // Dr. Naqeeb Ahmad (Liability)
                        'reference_type' => 'InPatientAdmission',
                        'reference_id' => $value->id,
                        'user_id' => $user_id,
                        'created_by' => auth()->user()->id,
                        'remarks' => 'Consultant procedure share posted to doctor: '.$value->consultant->name ?? "".' account by '.auth()->user()->name,
                        'created_at' => now()
                    ];
                    array_push($record,$rec);

                    $rec = [
                        'voucher_id' => $voucher->id,
                        'transaction_date' => today(),
                        'amount' => ($value->consultant_charges) - ($value->consultant_share_amount),
                        //'debit_head_id' => financeHeadId('doctor_share'),  // Doctor commision expense
                        'debit_head_id' => request()->finance_head_id,  // cash at office
                        'credit_head_id' => financeHeadId('procedure_income'), // procedure income
                        'reference_type' => 'InPatientAdmission',
                        'reference_id' => $value->id,
                        'user_id' => $user_id,
                        'created_by' => auth()->user()->id,
                        'remarks' => 'Procedure income posted to Procedure Income by '.auth()->user()->name,
                        'created_at' => now()
                    ];
                    array_push($record,$rec);
                }else{
                    $rec = [
                        'voucher_id' => $voucher->id,
                        'transaction_date' => today(),
                        'amount' => ($value->consultant_charges),
                        //'debit_head_id' => financeHeadId('doctor_share'),  // Doctor commision expense
                        'debit_head_id' => request()->finance_head_id,  // cash at office
                        'credit_head_id' => financeHeadId('procedure_income'), // procedure income
                        'reference_type' => 'InPatientAdmission',
                        'reference_id' => $value->id,
                        'user_id' => $user_id,
                        'created_by' => auth()->user()->id,
                        'remarks' => 'Consultant procedure share posted to doctor: '.$value->consultant->name ?? "".' account by '.auth()->user()->name,
                        'created_at' => now()
                    ];
                    array_push($record,$rec);
                }

            }
        }

        FinanceTransaction::insert($record);

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

    public function total_return_in_pharmacy($closing_date='',$user_id='')
    {
        $query = PharmacyRetrun::where("is_posted",0)
            ->when($closing_date, function ($query) use ($closing_date) {
                return $query->whereDate('created_at', '<=', date("Y-m-d", strtotime($closing_date)));
            })
            ->when($user_id, function ($query) use ($user_id) {
                return $query->where('created_by',$user_id);
            })->sum('amount');


        return $query;
    }

    public function appointmentsPayment($closing_date='',$user_id='')
    {
        $query = Appointment::where("is_posted",0)
            ->when($closing_date, function ($query) use ($closing_date) {
                return $query->whereDate('appointment_date', '<=', date("Y-m-d", strtotime($closing_date)));
            })
            ->where("is_active",1)
            ->when($user_id, function ($query) use ($user_id) {
                return $query->where('created_by',$user_id);
            });

        $totals = $query->selectRaw('SUM(fee) as total_fees, SUM(hospital_share) as total_hospital_share, SUM(consultant_share) as total_consultant_share')->first();
        return $totals;
    }

    public function investigationPayment($closing_date='',$user_id='')
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

        $totals = $query->selectRaw('SUM(amount) as cash_in_hand')->first();
        //dd($totals);
        return $totals;
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
        $data['finance_heads'] = FinanceHead::whereIn("type",["asset","expense","liability"])->get();
        $data['sub_heads'] = FinanceHead::whereIn("type",["liability","expense","income"])->get();
        $data['voucher'] = FinanceTransaction::query()
            ->where('reference_type', 'cash_payment_voucher')
            ->where(["finance_transactions.is_active"=>1])
            ->leftJoin('finance_heads as debit_heads', 'finance_transactions.debit_head_id', '=', 'debit_heads.id')
            ->leftJoin('finance_heads as credit_heads', 'finance_transactions.credit_head_id', '=', 'credit_heads.id')
            ->leftJoin('finance_vouchers as fv', 'finance_transactions.voucher_id', '=', 'fv.id')
            ->select(
                'finance_transactions.*',
                'debit_heads.name as debit_head_name',
                'credit_heads.name as credit_head_name',
                'fv.approved_by',
                'fv.approved_at'
            )
            ->orderBy("id","DESC")
            ->paginate(30);




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


        if($amount > 0){
            $record = [
                'voucher_id' => $voucher->id,
                'transaction_date' => today(),
                'amount' => $amount,
                'credit_head_id' => request()->credit_head_id, // Appointments income
                'debit_head_id' => request()->debit_head_id,  //cash at office
                'reference_type' => 'cash_payment_voucher',
                'reference_id' => NULL,
                'user_id' => auth()->id(),
                'remarks' => request()->remarks.". Payment to ".financeHeadName(request()->debit_head_id)." From ".financeHeadName(request()->credit_head_id)." by ".auth()->user()->name,
                'created_at' => now()
            ];
        }

        FinanceTransaction::insert($record);
        return redirect()->back()->with('success', 'Record saved successfully.');
    }

    public function cash_receipt_voucher()
    {
        $data['finance_heads'] = FinanceHead::whereIn("type",["liability","asset"])->get();
        $data['sub_heads'] = FinanceHead::whereIn("type",["liability"])->get();

        $data['voucher'] = FinanceTransaction::query()
            ->where('reference_type', 'cash_receipt_voucher')
            ->where(["finance_transactions.is_active"=>1])
            ->leftJoin('finance_heads as debit_heads', 'finance_transactions.debit_head_id', '=', 'debit_heads.id')
            ->leftJoin('finance_heads as credit_heads', 'finance_transactions.credit_head_id', '=', 'credit_heads.id')
            ->leftJoin('finance_vouchers as fv', 'finance_transactions.voucher_id', '=', 'fv.id')
            ->select(
                'finance_transactions.*',
                'debit_heads.name as debit_head_name',
                'credit_heads.name as credit_head_name',
                'fv.approved_by',
                'fv.approved_at'
            )

            ->orderBy("id","DESC")
            ->paginate(30);
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
            $record = [
                'voucher_id' => $voucher->id,
                'transaction_date' => today(),
                'amount' => $amount,
                'debit_head_id' => request()->debit_head_id,  //cash at office
                'credit_head_id' => request()->credit_head_id, // Appointments income
                'reference_type' => 'cash_receipt_voucher',
                'reference_id' => NULL,
                'user_id' => auth()->id(),
                "remarks"   => request()->remarks."- Received from ".financeHeadName(request()->credit_head_id)." by".auth()->user()->name,
                'created_at' => now()
            ];
        }

        FinanceTransaction::insert($record);
        return redirect()->back()->with('success', 'Record saved successfully.');
    }

    public function getBalance()
    {
        $debits = DB::table('finance_transactions')
            ->select('debit_head_id as head_id', DB::raw('SUM(amount) as total_debit'))

            ->groupBy('debit_head_id');

        // Subquery: Credit totals
        $credits = DB::table('finance_transactions')

            ->select('credit_head_id as head_id', DB::raw('SUM(amount) as total_credit'))
            ->groupBy('credit_head_id');

        // Merge into finance_heads
        $report = FinanceHead::leftJoinSub($debits, 'debits', 'finance_heads.id', '=', 'debits.head_id')
            ->leftJoinSub($credits, 'credits', 'finance_heads.id', '=', 'credits.head_id')
            ->select(
                'finance_heads.id',
                'finance_heads.name',
                'finance_heads.type',
                DB::raw('COALESCE(total_debit, 0) as total_debit'),
                DB::raw('COALESCE(total_credit, 0) as total_credit')
            )
            ->where("id",request()->id)
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
            ->when($user_id, function ($query) use ($user_id) {
                return $query->where('CreatedBy',$user_id);
            });

        $totals = $query->selectRaw('SUM(TotalSale)-SUM(Discount) as in_patient_sale')->first();

        return $totals->in_patient_sale ?? 0;


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



}
