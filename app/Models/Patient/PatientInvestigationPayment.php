<?php

namespace App\Models\Patient;

use App\Models\Configuration\District;
use App\Models\Users;
use App\Traits\Syncable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientInvestigationPayment extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "patient_investigations_payments";
    protected $guarded = ["id"];
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(Users::class, 'created_by',"id");
    }

    
}
