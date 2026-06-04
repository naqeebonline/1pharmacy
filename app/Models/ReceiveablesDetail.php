<?php

namespace App\Models;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiveablesDetail extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "receivables_details";
    protected $primaryKey = 'RDID';
    protected $guarded = ["RDID"];
    public $timestamps = false;


    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class, 'Payment_type_ID');
    }
}
