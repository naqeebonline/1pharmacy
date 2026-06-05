<?php

namespace App\Models\Patient;
use App\Models\Configuration\Investigation;
use App\Models\Configuration\InvestigationParameter;
use App\Models\Configuration\InvestigationSubCategory;
use App\Models\Configuration\Ward;
use App\Models\Configuration\WardBed;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestigationResult extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "patient_investigation_result";
    protected $guarded = ["id"];
    protected $primaryKey = 'id';
    public $timestamps = false;

 
    public function patient_investigation()
    {
        return $this->belongsTo(PatientInvestigation::class, "patient_investigation_id", "id");
    }

    public function parameter()
    {
        return $this->belongsTo(InvestigationParameter::class, "parameter_id", "id");
    }

 
}
