<?php

namespace App\Models\Accounts;

use App\Models\Configuration\Consultants;
use App\Models\PaymentType;
use App\Models\Users;
use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultantSehatCardPayment extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "consultant_sc_payments_details";
    protected $primaryKey = 'id';
    protected $guarded = ["id"];
    public $timestamps = true;

    public function consultant()
    {
        return $this->belongsTo(Consultants::class, 'consultant_id');
    }

    public function created_by()
    {
        return $this->belongsTo(Users::class, 'created_by');
    }

    public function payment_type()
    {
        return $this->belongsTo(PaymentType::class, 'payment_type_id');
    }
}
