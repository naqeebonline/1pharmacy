<?php

namespace Modules\Settings\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\IncidentReporting\Entities\ClosureStep;
use Modules\IncidentReporting\Entities\DepartmentTypeIncidentType;
use Modules\IncidentReporting\Entities\OdForm;
use Modules\IncidentReporting\Entities\SpectrumAssignment;

class CompanyType extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'title',
        'description'
    ];

    protected $connection = "mysql";

    protected static function newFactory()
    {
        return \Modules\Settings\Database\factories\CompanyTypeFactory::new();
    }

    public function companies(){
        return $this->hasMany(Company::class, 'company_type_id', 'id');
    }

    public function spectrumAssignments(){
        return $this->hasMany(SpectrumAssignment::class, 'department_type_id', 'id');
    }

    public function departmentTypeIncidentType(){
        return $this->hasMany(DepartmentTypeIncidentType::class, 'department_type_id', 'id');
    }

    public function forms()
    {
        return $this->hasMany(OdForm::class, 'department_type_id', 'id');
    }

    public function closureSteps()
    {
        return $this->hasMany(ClosureStep::class, 'department_type_id', 'id');
    }
}
