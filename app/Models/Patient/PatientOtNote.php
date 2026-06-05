<?php

namespace App\Models\Patient;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientOtNote extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "patient_ot_notes";
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
