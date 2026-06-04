<?php

namespace App\Models;

use App\Models\Patient\Patient;
use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyTransfer extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "pharmacy_transfer";
    protected $primaryKey = 'id';
    protected $guarded = ["id"];
    public $timestamps = false;

    public function patient(){
        return $this->belongsTo(Patient::class,"patient_id","id");
    }

    public function created_by(){
        return $this->belongsTo(Users::class,"CreatedBy","id");
    }



  
    
}
