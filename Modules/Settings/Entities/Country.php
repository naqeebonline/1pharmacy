<?php

namespace Modules\Settings\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\IncidentReporting\Entities\Person;
use Modules\IncidentReporting\Entities\Place;

class Country extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['title', 'description', 'active'];

    protected $connection = "mysql";

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected static function newFactory()
    {
        return \Modules\Settings\Database\factories\CountryFactory::new();
    }

    public function provinces(){
        return $this->hasMany(Province::class);
    }

    public function companies(){
        return $this->hasMany(Company::class);
    }

    public function persons()
    {
        return $this->hasMany(Person::class, 'country_id', 'id');
    }

    public function places()
    {
        return $this->hasMany(Place::class, 'country_id', 'id');
    }
}
