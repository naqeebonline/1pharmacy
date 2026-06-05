<?php

namespace Modules\Settings\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\IncidentReporting\Entities\Person;
use Modules\IncidentReporting\Entities\Place;
use Modules\IncidentReporting\Entities\PoliceStation;
use Modules\SupplyChain\Entities\DistrictAllocation;
use Modules\SupplyChain\Entities\DistrictNeed;

class District extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['province_id', 'division_id', 'title', 'urdu_title', 'description', 'active', 'latitude', 'longitude'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    protected $connection = "mysql";

    protected static function newFactory()
    {
        return \Modules\Settings\Database\factories\DistrictFactory::new();
    }

    public function province(){
        return $this->belongsTo(Province::class);
    }

    public function division(){
        return $this->belongsTo(Division::class);
    }

    public function tehsils(){
        return $this->hasMany(Tehsil::class);
    }

    public function companies(){
        return $this->hasMany(Company::class);
    }

    public function allocations(){
        return $this->hasMany(DistrictAllocation::class, 'district_id', 'id');
    }

    public function districtNeeds(){
        return $this->hasMany(DistrictNeed::class, 'district_id', 'id');
    }

    public function policeStations()
    {
        return $this->hasMany(PoliceStation::class, 'district_id', 'id');
    }

    public function persons()
    {
        return $this->hasMany(Person::class, 'district_id', 'id');
    }

    public function places()
    {
        return $this->hasMany(Place::class, 'district_id', 'id');
    }
}
