<?php

namespace Modules\Settings\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\IncidentReporting\Entities\Person;
use Modules\IncidentReporting\Entities\Place;
use Modules\IncidentReporting\Entities\PoliceStation;
use Modules\Settings\Entities\UnionCouncil;

class Tehsil extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['district_id', 'title'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $connection = "mysql";

    protected static function newFactory()
    {
        return \Modules\Settings\Database\factories\TehsilFactory::new();
    }

    public function district(){
        return $this->belongsTo(District::class);
    }

    public function unionCouncils(){
        return $this->hasMany(UnionCouncil::class);
    }

    public function companies(){
        return $this->hasMany(Company::class);
    }

    public function policeStations()
    {
        return $this->hasMany(PoliceStation::class, 'tehsil_id', 'id');
    }

    public function persons()
    {
        return $this->hasMany(Person::class, 'tehsil_id', 'id');
    }

    public function places()
    {
        return $this->hasMany(Place::class, 'tehsil_id', 'id');
    }
}
