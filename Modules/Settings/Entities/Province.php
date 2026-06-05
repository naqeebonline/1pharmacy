<?php

namespace Modules\Settings\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\IncidentReporting\Entities\Person;
use Modules\IncidentReporting\Entities\Place;
use Modules\IncidentReporting\Entities\PoliceStation;

class Province extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['country_id', 'title', 'description', 'active'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    protected $connection = "mysql";

    protected static function newFactory()
    {
        return \Modules\Settings\Database\factories\ProvinceFactory::new();
    }

    public function country(){
        return $this->belongsTo(Country::class);
    }

    public function divisions(){
        return $this->hasMany(Division::class);
    }

    public function districts(){
        return $this->hasMany(District::class);
    }

    public function companies(){
        return $this->hasMany(Company::class);
    }

    public function policeStations()
    {
        return $this->hasMany(PoliceStation::class, 'province_id', 'id');
    }

    public function persons()
    {
        return $this->hasMany(Person::class, 'province_id', 'id');
    }

    public function places()
    {
        return $this->hasMany(Place::class, 'province_id', 'id');
    }
}
