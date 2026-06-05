<?php

namespace App\Models\Patient;
use App\Models\Configuration\Consultants;
use App\Models\Configuration\ProcedureType;
use App\Models\Configuration\Ward;
use App\Models\Configuration\WardBed;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientDischargeChecklist extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "patient_discharge_checklist";
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


 
}
