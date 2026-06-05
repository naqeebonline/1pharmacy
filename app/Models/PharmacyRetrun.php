<?php

namespace App\Models;

use App\Models\Patient\Patient;
use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyRetrun extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "pharmacy_return_items";
    protected $primaryKey = 'id';
    protected $guarded = ["id"];
    public $timestamps = false;

    public function patient(){
        return $this->belongsTo(Patient::class,"patient_id","id");
    }
    public function product(){
        return $this->belongsTo(Product::class,"product_id","ProductID");
    }

    public function createdBy(){
        return $this->belongsTo(Users::class,"created_by","id");
    }



  
    
}
