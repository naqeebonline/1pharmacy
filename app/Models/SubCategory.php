<?php

namespace App\Models;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "sub_category";
    protected $guarded = ["id"];
    public $timestamps = false;

    public function main_category()
    {
        return $this->belongsTo(MainCategory::class, 'main_category_id');
    }
}
