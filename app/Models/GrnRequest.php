<?php

namespace App\Models;

use App\Models\Finance\FinanceHead;
use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrnRequest extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "grn_request";
    protected $primaryKey = 'GRNID';
    protected $guarded = ["GRNID"];
    public $timestamps = false;

    protected static function booted()
    {
        static::created(function ($sale) {
            // After creating, copy SaleID into id
            $sale->id = $sale->GRNID;
            $sale->saveQuietly(); // avoid recursion
        });
    }
    public function products(){
        return $this->belongsTo(Product::class);
    }

    public function supplier(){
        return $this->belongsTo(Customer::class,"SCID","SCID");
    }

    public function store(){
        return $this->belongsTo(Store::class,"store_id","id");
    }



  
    
}
