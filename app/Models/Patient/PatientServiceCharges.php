<?php

namespace App\Models\Patient;
use App\Models\Configuration\Investigation;
use App\Models\Configuration\ServiceType;
use App\Models\Configuration\Ward;
use App\Models\Configuration\WardBed;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientServiceCharges extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "patient_service_charges";
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
    public function service_type()
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id', "id");
    }
 
}
