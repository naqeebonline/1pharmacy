<?php

namespace App\Models\Configuration;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "wards";
    protected $guarded = ["id"];
    public $timestamps = false;


    public function bed(){
        return $this->belongsTo(WardBed::class, "bed_id", "id" );
    }

    public function beds(){
        return $this->hasMany(WardBed::class, "ward_id", "id" );
    }
    
}
