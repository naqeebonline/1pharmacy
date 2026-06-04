<?php

namespace App\Models\Finance;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceTransaction extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "finance_transactions";
    protected $primaryKey = 'id';
    protected $guarded = ["id"];
    public $timestamps = false;

    public function debitHead()
    {
        return $this->belongsTo(FinanceHead::class, 'debit_head_id');
    }

    public function creditHead()
    {
        return $this->belongsTo(FinanceHead::class, 'credit_head_id');
    }
}
