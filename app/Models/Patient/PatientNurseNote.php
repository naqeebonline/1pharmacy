<?php

namespace App\Models\Patient;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientNurseNote extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "patient_nurse_notes";
    protected $guarded = ["id"];
    protected $primaryKey = 'id';
    public $timestamps = true;

    public function patient()
    {
        return $this->belongsTo(Patient::class, "patient_id", "id");
    }
    public function admission()
    {
        return $this->belongsTo(PatientAdmission::class, "admission_id", "id");
    }

   
}
