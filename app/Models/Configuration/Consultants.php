<?php

namespace App\Models\Configuration;

use App\Models\Patient\Patient;
use App\Models\Patient\PatientAdmission;
use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultants extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "consultants";
    protected $guarded = ["id"];
    public $timestamps = false;

    public function consultant_department()
    {
        return $this->belongsTo(ConsultantDepartment::class, 'consultant_department_id', "id");
    }

    public function consultant_speciality()
    {
        return $this->belongsTo(ConsultantSpeciality::class, 'consultant_speciality_id', "id");
    }

    public function consultant_type()
    {
        return $this->belongsTo(ConsultantType::class, 'consultant_type_id', "id");
    }

    public function patients()
    {
        return $this->hasMany(PatientAdmission::class, 'consultant_id', "id");
    }
   
}
