<?php

namespace App\Http\Controllers\Finance\Reports;

use App\Http\Controllers\Controller;
use App\Exports\ProfitAndLossExcelExport;
use App\Models\Finance\FinanceHead;
use App\Models\Finance\FinanceTransaction;
use App\Models\Finance\FinanceVoucher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class FinanceReportController extends Controller
{
    public function balanceReport()
    {
        $report = DB::table('finance_heads as fh')
            ->leftJoin('finance_transactions as ft', 'fh.id', '=', 'ft.head_id')
            ->select(
                'fh.id',
                'fh.name as head_name',
                'fh.type as head_type',
                DB::raw('SUM(ft.debit) as total_debit'),
                DB::raw('SUM(ft.credit) as total_credit'),
                DB::raw('
                CASE 
                    WHEN fh.type IN ("asset", "expense") THEN SUM(ft.debit) - SUM(ft.credit)
                    WHEN fh.type IN ("income", "liability") THEN SUM(ft.credit) - SUM(ft.debit)
                    ELSE 0
                END as balance
            ')
            )
            ->groupBy('fh.id', 'fh.name', 'fh.type')
            ->orderBy('fh.name')
            ->get();

        return view('Finance.Reports.finance_balance_report', compact('report'));


    }

    public function profitAndLossReport(Request $request)
    {
        $startDate = $request->input('start_date') ?? now()->startOfMonth()->toDateString();
        $endDate = $request->input('end_date') ?? now()->endOfMonth()->toDateString();

        $incomeHeads = FinanceHead::where('type', 'income')->get();
        $incomeItems = [];
        $totalIncome = 0;

        foreach ($incomeHeads as $head) {
            $amount = FinanceTransaction::where('head_id', $head->id)
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->sum('credit') -
                FinanceTransaction::where('head_id', $head->id)
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->sum('debit');

            $incomeItems[] = [
                'name' => $head->name,
                'amount' => $amount
            ];
            $totalIncome += $amount;
        }

        $expenseHeads = FinanceHead::where('type', 'expense')->get();
        $expenseItems = [];
        $totalExpense = 0;

        foreach ($expenseHeads as $head) {
            $amount = FinanceTransaction::where('head_id', $head->id)
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->sum('debit') -
                FinanceTransaction::where('head_id', $head->id)
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->sum('credit');

            $expenseItems[] = [
                'name' => $head->name,
                'amount' => $amount
            ];
            $totalExpense += $amount;
        }

        $netProfit = $totalIncome - $totalExpense;

        return view('Finance.Reports.profit_and_lost_report', compact(
            'incomeItems', 'expenseItems', 'totalIncome', 'totalExpense', 'netProfit', 'startDate', 'endDate'
        ));

    }

    public function profitAndLossReportExcel(Request $request)
    {
        // Same dataset as profitAndLossReport()
        $startDate = $request->input('start_date') ?? now()->startOfMonth()->toDateString();
        $endDate = $request->input('end_date') ?? now()->endOfMonth()->toDateString();

        $incomeHeads = FinanceHead::where('type', 'income')->get();
        $incomeItems = [];
        $totalIncome = 0;

        foreach ($incomeHeads as $head) {
            $amount = FinanceTransaction::where('head_id', $head->id)
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->sum('credit') -
                FinanceTransaction::where('head_id', $head->id)
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->sum('debit');

            $incomeItems[] = [
                'name' => $head->name,
                'amount' => $amount
            ];
            $totalIncome += $amount;
        }

        $expenseHeads = FinanceHead::where('type', 'expense')->get();
        $expenseItems = [];
        $totalExpense = 0;

        foreach ($expenseHeads as $head) {
            $amount = FinanceTransaction::where('head_id', $head->id)
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->sum('debit') -
                FinanceTransaction::where('head_id', $head->id)
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->sum('credit');

            $expenseItems[] = [
                'name' => $head->name,
                'amount' => $amount
            ];
            $totalExpense += $amount;
        }

        $netProfit = $totalIncome - $totalExpense;

        $fileName = 'profit_and_loss_' . $startDate . '_to_' . $endDate . '.xlsx';
        $companyName = env('COMPANY_NAME', 'Company');

        return Excel::download(
            new ProfitAndLossExcelExport(
                $incomeItems,
                $expenseItems,
                (float) $totalIncome,
                (float) $totalExpense,
                (float) $netProfit,
                (string) $startDate,
                (string) $endDate,
                (string) $companyName,
            ),
            $fileName
        );
    }


 

    public function postDailyIncome(Request $request)
    {
        $from_date = $request->from_date ?? date("Y-m-d");
        $to_date = $request->to_date ?? date("Y-m-d");
        $userId = request()->user_id ?? auth()->user()->id; // or from $request if posting for another user
        $date = date("Y-m-d");
        // 1. Get income head IDs
        $cashHeadId = DB::table('finance_heads')->where('description', 'cash_at_office')->value('id');
        $appointmentHeadId = DB::table('finance_heads')->where('description', 'appointments')->value('id');
        $investigationHeadId = DB::table('finance_heads')->where('description', 'patient_investigations')->value('id');
        $pharmacyHeadId = DB::table('finance_heads')->where('description', 'sale')->value('id');

        // 2. Calculate daily totals
        $appointmentTotal = DB::table('appointments')
            ->when($from_date, function ($query) use ($from_date) {
                $query->whereDate('appointment_date',">=", $from_date);
            })
            ->when($to_date, function ($query) use ($to_date) {
                $query->whereDate('appointment_date',"<=", $to_date);
            })
            ->where('user_id', $userId)
            ->sum('fee');

        $investigationTotal = DB::table('patient_investigations')
            ->when($from_date, function ($query) use ($from_date) {
                $query->whereDate('inv_date',">=", $from_date);
            })
            ->when($to_date, function ($query) use ($to_date) {
                $query->whereDate('inv_date',"<=", $to_date);
            })
            ->where('user_id', $userId)
            ->sum('sale_price');

        $pharmacyTotal = DB::table('sale')
            ->when($from_date, function ($query) use ($from_date) {
                $query->whereDate('Date',">=", $from_date);
            })
            ->when($to_date, function ($query) use ($to_date) {
                $query->whereDate('Date',"<=", $to_date);
            })
            ->where('user_id', $userId)
            ->sum('total_price');

        // 3. Insert finance transactions
        $now = Carbon::now();

        $entries = [];

        if ($appointmentTotal > 0) {
            $entries[] = [
                'transaction_date' => $date,
                'amount' => $appointmentTotal,
                'debit_head_id' => $cashHeadId,
                'credit_head_id' => $appointmentHeadId,
                'reference_type' => 'appointment',
                'user_id' => $userId,
                'remarks' => "Posted appointment income",
                'created_at' => $now
            ];
        }

        if ($investigationTotal > 0) {
            $entries[] = [
                'transaction_date' => $date,
                'amount' => $investigationTotal,
                'debit_head_id' => $cashHeadId,
                'credit_head_id' => $investigationHeadId,
                'reference_type' => 'investigation',
                'user_id' => $userId,
                'remarks' => "Posted investigation income",
                'created_at' => $now
            ];
        }

        if ($pharmacyTotal > 0) {
            $entries[] = [
                'transaction_date' => $date,
                'amount' => $pharmacyTotal,
                'debit_head_id' => $cashHeadId,
                'credit_head_id' => $pharmacyHeadId,
                'reference_type' => 'pharmacy_sale',
                'user_id' => $userId,
                'remarks' => "Posted pharmacy income",
                'created_at' => $now
            ];
        }

      //  DB::table('finance_transactions')->insert($entries);

        return back()->with('success', 'Daily income posted to finance transactions.');
    }



    public function printDailyClosingVoucher($voucher_id)
    {
        $voucher = FinanceVoucher::with(['createdBy', 'approvedBy'])
            ->findOrFail($voucher_id);
           

        // Get transactions related to the voucher, excluding commissions
        $transactions = DB::table('finance_transactions as ft')
            ->join('finance_heads as fh', 'fh.id', '=', 'ft.head_id')
            ->select(
                'fh.head_code',
                'fh.name as head_title',
                'ft.debit',
                'ft.reference_type',
                'ft.credit'
            )
            ->where('ft.voucher_id', $voucher_id)
            //->where('fh.type', "!=","asset")
            ->where(function ($q) {
                $q->whereNull('ft.reference_type')
                    ->orWhere('ft.reference_type', '!=', 'commission');
            })
            ->where('ft.is_active', 1)
            ->get();

        



        // Map and prepare rows
        $rows = collect();
        foreach ($transactions as $t) {
            if((!in_array($t->reference_type, ['pharmacy_sale_cogs', 'pharmacy_sale_cogs']))){
            $rows->push([
                'head_code' => $t->head_code,
                'head_title' => $t->head_title,
                'reference_type' => $t->reference_type,
                'debit' => $t->debit,
                'credit' => $t->credit,
                'balance' => $t->debit > 0 ? $t->debit : $t->credit,
                'type' => $t->debit > 0 ? 'debit' : 'credit',
            ]);
        }
        }

        // Sort debit first
        $sortedRows = $rows->sortBy(fn($item) => $item['type'] === 'debit' ? 0 : 1)->values();



    $totalDebit = $sortedRows->sum('debit');
    $totalCredit = $sortedRows->sum('credit');
   // dd($totalDebit,$totalCredit);

    return view('Finance.Reports.print_daily_closing_voucher', compact(
        'voucher', 'sortedRows', 'totalDebit', 'totalCredit'
    ));
}

    public function printJournalVoucher($voucher_id)
    {
        $voucher = FinanceVoucher::with(['createdBy', 'approvedBy'])
            ->findOrFail($voucher_id);


        // Get transactions related to the voucher, excluding commissions
        $transactions = DB::table('finance_transactions as ft')
            ->join('finance_heads as fh', 'fh.id', '=', 'ft.head_id')
            ->select(
                'fh.head_code',
                'fh.name as head_title',
                'ft.debit',
                'ft.credit',
                'ft.remarks'
            )
            ->where('ft.voucher_id', $voucher_id)
            ->where(function ($q) {
                $q->whereNull('ft.reference_type')
                    ->orWhere('ft.reference_type', '!=', 'commission');
            })
            ->where('ft.is_active', 1)
            ->get();

        //  dd($transactions);



        // Map and prepare rows
        $rows = collect();
        foreach ($transactions as $t) {
            $rows->push([
                'head_code' => $t->head_code,
                'head_title' => $t->head_title,
                'remarks' => $t->remarks,
                'debit' => $t->debit,
                'credit' => $t->credit,
                'balance' => $t->debit > 0 ? $t->debit : $t->credit,
                'type' => $t->debit > 0 ? 'debit' : 'credit',
            ]);
        }

        // Sort debit first
        $sortedRows = $rows->sortBy(fn($item) => $item['type'] === 'debit' ? 0 : 1)->values();



    $totalDebit = $sortedRows->sum('debit');
    $totalCredit = $sortedRows->sum('credit');
   // dd($totalDebit,$totalCredit);

    return view('Finance.Reports.print_journal_voucher', compact(
        'voucher', 'sortedRows', 'totalDebit', 'totalCredit'
    ));
}

    function get_user_base_daily_closing_report(){
        $start_date = date("Y-m-d");
        $end_date = date("Y-m-d");
        $report = DB::table('finance_transactions as ft')
            ->join('finance_heads as fh', 'fh.id', '=', 'ft.credit_head_id')
            ->join('users as user', 'ft.user_id', '=', 'user.id')
            ->select('ft.user_id', 'fh.name as head_name', 'ft.reference_type', DB::raw('COUNT(ft.reference_type) as total_count'),DB::raw('SUM(ft.amount) as total_amount'),'user.name as user_name')
            ->whereBetween('ft.transaction_date', [$start_date, $end_date])
            ->where('ft.is_active', 1)
            ->where('ft.reference_type', '!=', 'commission')
            ->groupBy('ft.user_id', 'ft.reference_type', 'fh.name')
            ->orderBy('ft.user_id')
            ->get()
            ->groupBy('user_id');

        $userAdvances = [];
        foreach ($report as $key => $value){
            $userAdvances[$key] = user_advance($key,$start_date,$end_date);
        }





        return view('Finance.Reports.user_base_closing_report',compact("start_date","end_date","report","userAdvances"));
    }


    public function get_user_base_daily_closing_report2()
    {
        $start_date = request()->start_date ?? date('Y-m-d');
        $end_date = request()->end_date ?? date('Y-m-d');

        $transactions = DB::table('finance_transactions as ft')
            ->join('users as user', 'ft.user_id', '=', 'user.id')
            ->leftJoin('finance_heads as fh', 'ft.head_id', '=', 'fh.id')
            ->whereBetween('ft.transaction_date', [$start_date, $end_date])
            ->where('ft.is_active', 1)
            ->where('ft.reference_type','!=', 'cash_payment_voucher')
            ->where('ft.reference_type','!=', 'cash_receipt_voucher')
            ->where('ft.reference_type','!=', 'journal_voucher')
            ->where('ft.reference_type','!=', 'cost_of_lab_consumable')
            ->where('ft.reference_type','!=', 'grn')
            ->where('ft.reference_type','!=', 'pharmacy_sale_cogs')
            ->where('ft.reference_type','!=', 'cogs_pharmacy_sale_return')
            ->where(function ($q) {
                $q->whereNull('ft.reference_type')
                    ->orWhere('ft.reference_type', '!=', 'commission');

            })
            ->select(
                'ft.transaction_date',
                'ft.user_id',
                'user.name as user_name',
                'fh.name as head_name',
                'ft.reference_type',
                DB::raw("SUM(ft.debit) as total_debit"),
                DB::raw("SUM(ft.credit) as total_credit"),
                DB::raw("COUNT(ft.id) as total_count")
            )
            ->groupBy('ft.transaction_date', 'ft.user_id', 'user.name', 'fh.name', 'ft.reference_type')
            ->orderBy('ft.transaction_date')
            ->orderBy('ft.user_id')
            ->get()
            ->groupBy('transaction_date');
      //  dd($transactions);

        $finalReport = [];

        foreach ($transactions as $date => $dayGroup) {
            $users = $dayGroup->groupBy('user_id')->map(function ($txns, $userId) use ($date) {
                $userName = $txns->first()->user_name;
                $advance = user_advance($userId, $date, $date);

                $rows = $txns->map(function ($t) {
                    if($t->reference_type == 'pharmacy_return'){
                        $t->total_credit = -($t->total_credit);
                    }
                    return [
                        'head_name'      => $t->head_name,
                        'reference_type' => $t->reference_type,
                        'total_count'    => $t->total_count,
                        'total_debit'    => $t->total_debit,
                        'total_credit'   => $t->total_credit,
                    ];
                });

                return [
                    'user_id'      => $userId,
                    'name'         => $userName,
                    'user_advance' => $advance,
                    'transactions' => $rows,
                ];
            });

            $finalReport[$date] = [
                'date' => $date,
                'users' => $users->values()
            ];

        }

        return view('Finance.Reports.user_base_closing_report2', compact('start_date', 'end_date', 'finalReport'));
    }

    public function trail_balance_report(Request $request)
    {
        $start_date = $request->start_date ?? date('Y-m-d');
        $end_date = $request->end_date ?? date('Y-m-d');

        $final_balance = DB::table('finance_transactions as ft')
            ->join('finance_heads as fh', 'ft.head_id', '=', 'fh.id')
            ->leftJoin('finance_vouchers', 'ft.voucher_id', '=', 'finance_vouchers.id')
            ->whereNotNull('finance_vouchers.approved_by')
            ->whereBetween('ft.transaction_date', [$start_date, $end_date])
            ->select(
                'fh.id as head_id',
                'fh.name as head_name',
                DB::raw('SUM(ft.debit) as total_debit'),
                DB::raw('SUM(ft.credit) as total_credit')
            )
            ->groupBy('fh.id', 'fh.name')
            ->get();

        $total_debit = $final_balance->sum('total_debit');
        $total_credit = $final_balance->sum('total_credit');
        $is_balanced = ($total_debit == $total_credit);

        return view('Finance.Reports.trail_balance_report', compact(
            'final_balance', 'total_debit', 'total_credit', 'is_balanced', 'start_date', 'end_date'
        ));
    }

    public function finance_vouchers_report()
    {
        $start_date = request()->start_date ?? now()->toDateString();
        $end_date = request()->end_date ?? now()->toDateString();
        $voucher_type_param = request()->voucher_type ?? "";


        $enumRaw = DB::select("SHOW COLUMNS FROM finance_vouchers WHERE Field = 'voucher_type'");
        $enumString = collect($enumRaw)->pluck('Type')->first();
        preg_match('/enum\((.*)\)/', $enumString, $matches);
        $voucherTypes = collect(explode(',', $matches[1]))->map(fn($v) => trim($v, " '"));

        $vouchers = DB::table('finance_vouchers as fv')
            ->whereBetween('fv.voucher_date', [$start_date, $end_date])
            ->whereNotNull('fv.approved_by')
            ->orderBy('fv.voucher_date')
            ->when($voucher_type_param, function ($query) use ($voucher_type_param) {
                return $query->where('voucher_type',$voucher_type_param);
            })
            ->get();

       // dd($vouchers);

        // Fetch transactions grouped by voucher_id
        $transactions = DB::table('finance_transactions as ft')
            ->join('finance_heads as dh', 'ft.debit_head_id', '=', 'dh.id')
            ->join('finance_heads as ch', 'ft.credit_head_id', '=', 'ch.id')
            ->join('users', 'ft.user_id', '=', 'users.id')
            ->leftJoin('finance_vouchers', 'ft.voucher_id', '=', 'finance_vouchers.id')
            ->whereNotNull('finance_vouchers.approved_by')
            ->whereBetween('ft.transaction_date', [$start_date, $end_date])

            ->select(
                'ft.voucher_id',
                'ft.transaction_date',
                'ft.amount',
                'dh.name as debit_head',
                'ch.name as credit_head',
                'users.name as user_name',
                'ft.remarks'
            )
            ->orderBy('ft.transaction_date')
            ->get()
            ->groupBy('voucher_id');

        return view('Finance.Reports.finance_vouchers_report', compact('vouchers', 'transactions', 'start_date', 'end_date','voucherTypes',"voucher_type_param"));

        //Finance.Reports.
    }

    public function finance_ledger_report()
    {
        $finance_head_id = request()->finance_head_id ?? '';
        $start_date = request()->start_date ?? date('Y-m-01');
        $end_date = request()->end_date ?? date('Y-m-d');

        $finance_heads = DB::table('finance_heads')->orderBy('name')->get();

        $entries = collect();
        $running_balance = 0;
        $head_type = null;

        if ($finance_head_id) {
            $finance_h = DB::table('finance_heads')->where('id', $finance_head_id)->first();
            $head_type = $finance_h->type ?? null;
        }

        // Calculate opening balance
        $opening = DB::table('finance_transactions')
            ->where('transaction_date', '<', $start_date)
            ->where('head_id', $finance_head_id)
            ->selectRaw('
            SUM(debit) as total_debit,
            SUM(credit) as total_credit
        ')
            ->first();

        $total_debit = $opening->total_debit ?? 0;
        $total_credit = $opening->total_credit ?? 0;

        if (in_array($head_type, ['asset', 'expense'])) {
            $opening_balance = $total_debit - $total_credit;
        } else {
            $opening_balance = $total_credit - $total_debit;
        }

        $running_balance = $opening_balance;

        if ($finance_head_id) {
            $entries = DB::table('finance_transactions as ft')
                ->leftJoin('users', 'users.id', '=', 'ft.user_id')
                ->where('ft.head_id', $finance_head_id)
                ->whereBetween('ft.transaction_date', [$start_date, $end_date])
                ->orderBy('ft.transaction_date')
                ->orderBy('ft.id')
                ->select(
                    'ft.transaction_date',
                    'ft.debit',
                    'ft.credit',
                    'ft.remarks',
                    'users.name as user_name'
                )
                ->get()
                ->map(function ($row) use ($head_type, &$running_balance) {
                    if (in_array($head_type, ['asset', 'expense'])) {
                        $running_balance += $row->debit - $row->credit;
                    } else {
                        $running_balance += $row->credit - $row->debit;
                    }

                    $row->running_balance = $running_balance;
                    return $row;
                });
        }

        return view('Finance.Reports.finance_ledger_report', compact(
            'finance_heads',
            'finance_head_id',
            'entries',
            'start_date',
            'end_date',
            'opening_balance'
        ));
    }
    public function outstanding_balances_report()
    {
        $heads = DB::table('finance_heads')
            ->whereIn('type', ['asset', 'liability'])
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        foreach ($heads as $head) {
            $totals = DB::table('finance_transactions as ft')
                ->join('finance_vouchers as fv', 'ft.voucher_id', '=', 'fv.id')
                ->whereNotNull('fv.approved_by')
                ->where('ft.head_id', $head->id)
                ->selectRaw("
                SUM(ft.debit) as total_debit,
                SUM(ft.credit) as total_credit
            ")
                ->first();

            $head->total_debit = $totals->total_debit ?? 0;
            $head->total_credit = $totals->total_credit ?? 0;

            // Calculate balance based on head type
            if ($head->type === 'asset') {
                $head->balance = $head->total_debit - $head->total_credit;
                $head->status = $head->balance >= 0 ? 'Receivable' : 'Overpaid';
            } else { // liability
                $head->balance = $head->total_credit - $head->total_debit;
                $head->status = $head->balance >= 0 ? 'Payable' : 'Advance';
            }
        }

        return view('Finance.Reports.outstanding_balances_report', compact('heads'));
    }


    public function twoLevelBalanceReport()
    {
        $level2Heads = FinanceHead::where('level', 2)->get();

        $report = [];

        foreach ($level2Heads as $level2) {
            // Get level-3 child heads
            $children = FinanceHead::where('parent_id', $level2->id)->pluck('id');

            // Sum debit/credit for children
            $totals = FinanceTransaction::select(
                DB::raw('SUM(debit) as total_debit'),
                DB::raw('SUM(credit) as total_credit')
            )
                ->whereIn('head_id', $children)
                ->first();

            $total_debit = $totals->total_debit ?? 0;
            $total_credit = $totals->total_credit ?? 0;

            // Determine balance logic based on account type
            if (in_array($level2->type, ['asset', 'expense'])) {
                $balance = $total_debit - $total_credit;
            } elseif (in_array($level2->type, ['liability', 'income', 'capital'])) {
                $balance = $total_credit - $total_debit;
            } else {
                $balance = 0;
            }

            $report[] = [
                'head_code' => $level2->head_code,
                'name' => $level2->name,
                'type' => ucfirst($level2->type),
                'total_debit' => $total_debit,
                'total_credit' => $total_credit,
                'balance' => $balance,
            ];
        }

        return view('Finance.Reports.two_level_balance', compact('report'));
    }

}
