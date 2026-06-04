<?php

namespace App\Models\Finance;

use App\Models\Users;
use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceVoucher extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "finance_vouchers";
    protected $primaryKey = 'id';
    protected $guarded = ["id"];
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(Users::class, 'created_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(Users::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(Users::class, 'created_by');
    }

    public function transactions()
    {
        return $this->hasMany(FinanceTransaction::class, 'voucher_id');
    }
}
