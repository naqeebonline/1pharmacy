<?php

namespace Modules\Settings\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\EIdentity\Entities\Employees;
use Modules\IncidentReporting\Entities\Incident;
use Modules\IncidentReporting\Entities\SpectrumAssignment;
use Modules\SupplyChain\Entities\Contractor;
use Modules\SupplyChain\Entities\ContractorAssignment;

class Company extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'company_type_id',
        'company_level_id',
        'parent_id',
        'country_id',
        'province_id',
        'division_id',
        'district_id',
        'tehsil_id',
        'title',
        'description',
        'prefix',
        'user_id',
        'reference_id',
        'reference_model'
    ];

    protected $connection = "mysql";

    protected static function newFactory()
    {
        return \Modules\Settings\Database\factories\CompanyFactory::new();
    }

    public function type(){
        return $this->belongsTo(CompanyType::class, 'company_type_id', 'id');
    }

    public function level(){
        return $this->belongsTo(CompanyLevel::class, 'company_level_id', 'id');
    }

    public function parent(){
        return $this->belongsTo(Company::class, 'parent_id', 'id');
    }

    public function children(){
        return $this->hasMany(Company::class, 'parent_id', 'id');
    }

    public function country(){
        return $this->belongsTo(Country::class);
    }

    public function province(){
        return $this->belongsTo(Province::class);
    }

    public function division(){
        return $this->belongsTo(Division::class);
    }

    public function district(){
        return $this->belongsTo(District::class);
    }

    public function tehsil(){
        return $this->belongsTo(Tehsil::class);
    }

    // creator user
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function users(){
        return $this->hasMany(User::class, 'company_id', 'id');
    }

    public function contractor(){
        return $this->hasOne(Contractor::class, 'company_id', 'id');
    }

    public function contractorAssignments(){
        return $this->hasMany(ContractorAssignment::class, 'company_id', 'id');
    }

    public function spectrumAssignments(){
        return $this->hasMany(SpectrumAssignment::class, 'department_id', 'id');
    }

    public function incidents()
    {
        return $this->hasMany(Incident::class, 'department_id', 'id');
    }


    public function employees(){
        return $this->hasMany(Employees::class,'department_id','id');
    }
}
