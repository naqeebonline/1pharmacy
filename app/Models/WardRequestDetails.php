<?php

namespace App\Models;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WardRequestDetails extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "ward_request_details";
    protected $primaryKey = 'id';
    protected $guarded = ["id"];
    public $timestamps = false;


    public function products(){
        return $this->belongsTo(Product::class,"product_id");
    }


  
    
}
