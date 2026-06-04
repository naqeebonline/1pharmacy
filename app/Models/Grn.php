<?php

namespace App\Models;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grn extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "grn";
    protected $primaryKey = 'GRNID';
    protected $guarded = ["GRNID"];
    public $timestamps = false;


    public function products(){
        return $this->belongsTo(Product::class);
    }

    public function supplier(){
        return $this->belongsTo(Customer::class, 'SCID', 'SCID');
    }

    public function grnDetails(){
        return $this->hasMany(GrnDetails::class, 'GRNID', 'GRNID');
    }

    protected static function booted()
    {
        static::created(function ($data) {
            // After creating, copy SaleID into id
            $data->id = $data->GRNID;
            $data->saveQuietly(); // avoid recursion
        });
    }

  
    
}
