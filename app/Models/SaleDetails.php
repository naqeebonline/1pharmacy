<?php

namespace App\Models;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleDetails extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "sale_details";
    protected $primaryKey = 'SDID';
    protected $guarded = ["SDID"];
    public $timestamps = false;

    protected static function booted()
    {
        static::created(function ($sale_details) {
            // After creating, copy sale_details into id
            $sale_details->id = $sale_details->SDID;
            $sale_details->saveQuietly(); // avoid recursion
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'ProductID');
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'SaleID');
    }

    public function return_by()
    {
        return $this->belongsTo(User::class, 'return_by',"id");
    }
    public function return_by_user()
    {
        return $this->belongsTo(User::class, 'return_by',"id");
    }

  
    
}
