<?php

namespace App\Models\Patient;
use App\Models\Configuration\ConsultantProcedure;
use App\Models\Configuration\Consultants;
use App\Models\Configuration\ProcedureType;
use App\Models\Configuration\Ward;
use App\Models\Configuration\WardBed;

use App\Models\User;
use App\Models\Users;
use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InPatientAdmission extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "in_patient_admissions";
    protected $guarded = ["id"];
    protected $primaryKey = 'id';
    public $timestamps = true;

 
    public function user()
    {
        return $this->belongsTo(User::class, "created_by", "id");
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, "patient_id", "id");
    }

    public function ward()
    {
        return $this->belongsTo(Ward::class, "ward_id", "id");
    }
    public function bed()
    {
        return $this->belongsTo(WardBed::class, 'bed_id', "id");
    }

    public function consultant_procedure()
    {
        return $this->belongsTo(ConsultantProcedure::class, 'consultant_procedure_id', "id");
    }
    public function consultant()
    {
        return $this->belongsTo(Consultants::class, 'consultant_id', "id");
    }

    public function sec_procedure()
    {
        return $this->belongsTo(ProcedureType::class, 'sec_procedure_type_id', "id");
    }

    public function sub_consultant()
    {
        return $this->belongsTo(Consultants::class, 'sub_consultant_id', "id");
    }
    public function relation()
    {
        return $this->belongsTo(Relation::class, 'relation_id', "id");
    }
    public function procedure_type()
    {
        return $this->belongsTo(ProcedureType::class, 'procedure_type_id', "id");
    }

    public function createdBy()
    {
        return $this->belongsTo(Users::class, 'created_by', "id");
    }
 
}
