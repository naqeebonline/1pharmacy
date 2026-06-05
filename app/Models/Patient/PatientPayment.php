<?php

namespace App\Models\Patient;

use App\Models\Configuration\District;
use App\Models\Users;
use App\Traits\Syncable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientPayment extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "in_patient_payments";
    protected $guarded = ["id"];
    protected $primaryKey = 'id';
    public $timestamps = false;


    public function patient()
    {
        return $this->belongsTo(Patient::class, "patient_id", "id");
    }
    public function admission()
    {
        return $this->belongsTo(PatientAdmission::class, "admission_id", "id");
    }

    public function createdBy()
    {
        return $this->belongsTo(Users::class, "created_by", "id");
    }
    
}
