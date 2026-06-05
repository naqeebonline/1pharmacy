<?php

namespace Modules\Settings\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\IncidentReporting\Entities\SpectrumAssignment;

class CompanyLevel extends Model
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
        return \Modules\Settings\Database\factories\CompanyLevelFactory::new();
    }

    public function companies(){
        return $this->hasMany(Company::class, 'company_level_id', 'id');
    }

    public function spectrumAssignments(){
        return $this->hasMany(SpectrumAssignment::class, 'department_level_id', 'id');
    }
}
