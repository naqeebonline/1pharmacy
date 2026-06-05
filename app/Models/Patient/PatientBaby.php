<?php

namespace App\Models\Patient;

use App\Models\Configuration\Investigation;
use App\Models\Configuration\InvestigationSubCategory;
use App\Models\Configuration\Ward;
use App\Models\Configuration\WardBed;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class PatientBaby extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "patient_baby";
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
