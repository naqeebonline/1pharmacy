<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    use HasFactory;

    protected $table = 'taxes';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'tax_percentage',
        'is_active',
    ];

    protected $casts = [
        'tax_percentage' => 'decimal:2',
        'is_active' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopeApplicableOnSale($query)
    {
        return $query->active()->where('tax_percentage', '>', 0);
    }
}
