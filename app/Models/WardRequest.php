<?php

namespace App\Models;

use App\Models\Patient\Patient;
use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WardRequest extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "ward_request";
    protected $primaryKey = 'id';
    protected $guarded = ["id"];
    public $timestamps = false;


    public function patient(){
        return $this->belongsTo(Patient::class,"patient_id");
    }
    public function user(){
        return $this->belongsTo(Users::class,"requested_by");
    }

  
    
}
