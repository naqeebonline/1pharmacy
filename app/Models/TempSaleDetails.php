<?php

namespace App\Models;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempSaleDetails extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "temp_sale_details";
    protected $primaryKey = 'id';
    protected $guarded = ["id"];
    public $timestamps = false;

    public function product()
    {
        return $this->belongsTo(Product::class, 'ProductID');
    }

    public function tempSale()
    {
        return $this->belongsTo(TempSale::class, 'SaleID');
    }




  
    
}
