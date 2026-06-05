<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrnRequestDetails extends Model
{
    use HasFactory;
    protected $table = "grn_request_details";
    protected $primaryKey = 'GDID';
    protected $guarded = ["GDID"];
    public $timestamps = false;
    protected static function booted()
    {
        static::created(function ($sale) {
            // After creating, copy SaleID into id
            $sale->id = $sale->GDID;
            $sale->saveQuietly(); // avoid recursion
        });
    }

    public function products(){
        return $this->belongsTo(Product::class,"ProductID");
    }

  
    
}
