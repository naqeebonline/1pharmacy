<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseSub extends Model
{
    use HasFactory;
    protected $table = "expense_sub";
    protected $guarded = ["ESID"];
    protected $primaryKey = 'ESID';
    public $timestamps = false;

    public function expense(){
        return $this->belongsTo(Expense::class, 'ExpenseID', 'ExpenseID');
    }
}
