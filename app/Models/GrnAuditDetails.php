<?php

namespace App\Models;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrnAuditDetails extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "grn_audit_details";
    protected $primaryKey = 'id';
    protected $guarded = ["id"];
    public $timestamps = false;


    public function product(){
        return $this->belongsTo(Product::class,"product_id","ProductID");
    }

    public function audit(){
        return $this->belongsTo(GrnAudit::class,"audit_id","id");
    }

  
    
}
