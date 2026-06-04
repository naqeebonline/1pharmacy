<?php

namespace App\Models;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrnDetails extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "grn_details";
    protected $primaryKey = 'GDID';
    protected $guarded = ["GDID"];
    public $timestamps = false;
    protected static function booted()
    {
        static::created(function ($data) {
            // After creating, copy SaleID into id
            $data->id = $data->GDID;
            $data->saveQuietly(); // avoid recursion
        });
    }

    public function products(){
        return $this->belongsTo(Product::class,"ProductID");
    }

    public function grn(){
        return $this->belongsTo(Grn::class, "GRNID", "GRNID");
    }

  
    
}
