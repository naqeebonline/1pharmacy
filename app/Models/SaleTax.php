<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleTax extends Model
{
    use HasFactory;

    protected $table = 'sale_taxes';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'sale_id',
        'tax_id',
        'tax_percentage',
    ];

    protected $casts = [
        'sale_id' => 'integer',
        'tax_id' => 'integer',
        'tax_percentage' => 'decimal:0',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id', 'SaleID');
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class, 'tax_id', 'id');
    }
}
