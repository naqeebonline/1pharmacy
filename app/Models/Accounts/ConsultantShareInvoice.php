<?php

namespace App\Models\Accounts;

use App\Models\Configuration\Consultants;
use App\Models\User;
use App\Models\Users;
use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultantShareInvoice extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "consultant_shares_payment_invoice";
    protected $primaryKey = 'id';
    protected $guarded = ["id"];
    public $timestamps = false;

    public function consultant()
    {
        return $this->belongsTo(Consultants::class, 'consultant_id');
    }

    public function created_by()
    {
        return $this->belongsTo(Users::class, 'created_by');
    }
}
