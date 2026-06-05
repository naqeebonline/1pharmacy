<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralExpense extends Model
{
    use HasFactory;
    protected $table = "general_expenses";
    protected $guarded = ["GXID"];
    protected $primaryKey = 'GXID';
    public $timestamps = false;

    public function sub_expenses(){
        return $this->belongsTo(ExpenseSub::class, "ESID", "ESID");
    }
}
