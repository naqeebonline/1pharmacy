<?php

namespace App\Models;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductConsumption extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "product_consumption";
    protected $primaryKey = 'id';
    protected $guarded = ["id"];
    public $timestamps = true;


    public function created_by(){
        return $this->belongsTo(Users::class,"created_by","id");
    }
    public function product(){
        return $this->belongsTo(Product::class,"product_id","id");
    }

    public function audit(){
        return $this->belongsTo(GrnAudit::class,"audit_id","id");
    }

  
    
}
