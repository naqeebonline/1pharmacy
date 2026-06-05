<?php

namespace App\Models;

use App\Models\Patient\Patient;
use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "sale";
    protected $primaryKey = 'SaleID';
    protected $guarded = ["SaleID"];
    public $timestamps = false;

    protected static function booted()
    {
        static::created(function ($sale) {
            // After creating, copy SaleID into id
            $sale->id = $sale->SaleID;
            $sale->saveQuietly(); // avoid recursion
        });
    }
    public function patient(){
        return $this->belongsTo(Patient::class,"patient_id","id");
    }
    public function created_by(){
        return $this->belongsTo(Users::class,"CreatedBy","id");
    }



  
    
}
