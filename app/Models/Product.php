<?php

namespace App\Models;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "products";
    protected $primaryKey = 'ProductID';
    protected $guarded = ["id"];
    public $timestamps = false;

    protected static function booted()
    {
        static::created(function ($sale) {
            // After creating, copy SaleID into id
            $sale->id = $sale->ProductID;
            $sale->saveQuietly(); // avoid recursion
        });
    }
    public function main_categroy(){
        return $this->belongsTo(MainCategory::class,"main_category_id");
    }

    public function sub_categroy(){
        return $this->belongsTo(SubCategory::class,"sub_category_id");
    }
    public function item_form(){
        return $this->belongsTo(ItemForm::class,"item_form_id");
    }

    public function item_make(){
        return $this->belongsTo(ItemMake::class,"item_make_id");
    }

    public function generic_name()
    {
        return $this->belongsTo(GenericName::class,"generic_name_id");
    }

}
