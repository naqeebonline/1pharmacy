<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class OpdTypeProduct extends Pivot
{
    use HasFactory;

    protected $table = 'opd_type_products';

    protected $fillable = [
        'opd_type_id',
        'product_id',
        'quantity',
    ];

    public $timestamps = true;

    /**
     * Get the OPD type that owns the OpdTypeProduct
     */
    public function opdType()
    {
        return $this->belongsTo(OpdType::class, 'opd_type_id', 'id');
    }

    /**
     * Get the product that owns the OpdTypeProduct
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'ProductID');
    }
}