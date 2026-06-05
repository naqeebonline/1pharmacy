<?php

namespace App\Models;

use App\Models\Patient\Patient;
use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalePayment extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "sale_payments";
    protected $primaryKey = 'id';
    protected $guarded = ["id"];
    public $timestamps = false;

    public function patient(){
        return $this->belongsTo(Patient::class,"patient_id","id");
    }
    public function sale(){
        return $this->belongsTo(Sale::class,"sale_id","id");
    }

    public function createdBy()
    {
        return $this->belongsTo(Users::class, 'created_by',"id");
    }

  
    
}
