<?php

namespace App\Models;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrnAudit extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "grn_audit";
    protected $primaryKey = 'id';
    protected $guarded = ["id"];
    public $timestamps = false;


    public function users(){
        return $this->belongsTo(Users::class,"audit_by","id");
    }

    public function approve_by(){
        return $this->belongsTo(Users::class,"approve_by","id");
    }

  
    
}
