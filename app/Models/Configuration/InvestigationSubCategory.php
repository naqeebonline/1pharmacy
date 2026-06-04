<?php

namespace App\Models\Configuration;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestigationSubCategory extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "investigation_sub_category";
    protected $guarded = ["id"];
    public $timestamps = false;

    public function main_category(){
        return $this->belongsTo(InvestigationMainCategory::class,"investigation_category_id");
    }
}
