<?php

namespace App\Models\Configuration;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestigationMainCategory extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "investigation_category";
    protected $guarded = ["id"];
    public $timestamps = false;

    public function investigation_category(){
        return $this->belongsTo(InvestigationCategory::class,'investigation_category_id');
    }
}
