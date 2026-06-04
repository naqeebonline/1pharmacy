<?php

namespace Modules\Settings\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Settings\Entities\Tehsil;

class UnionCouncil extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['district_id', 'tehsil_id', 'status', 'name', 'type'];

    protected $connection = "mysql";

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $table = "uc_vc_lists";

    protected static function newFactory()
    {
        return \Modules\Settings\Database\factories\TehsilFactory::new();
    }

    public function tehsil(){
        return $this->belongsTo(Tehsil::class);
    }
}
