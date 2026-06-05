<?php

namespace App\Models;

use App\Models\Configuration\InvestigationSubCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpdType extends Model
{
    use HasFactory;

    protected $table = "opd_type";
    protected $guarded = ["id"];
    public $timestamps = false;

    /**
     * Get the products associated with the OPD type
     */
    public function products()
    {
        return $this->belongsToMany(
            Product::class,
            'opd_type_products',
            'opd_type_id',
            'product_id',
            'id',
            'ProductID'
        )->withPivot('quantity')->withTimestamps();
    }

    /**
     * Get the investigations associated with the OPD type
     */
    public function investigations()
    {
        return $this->belongsToMany(
            InvestigationSubCategory::class,
            'opd_type_investigations',
            'opd_type_id',
            'investigation_sub_category_id'
        )->withTimestamps();
    }
}
