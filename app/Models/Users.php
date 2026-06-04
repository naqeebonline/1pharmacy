<?php

namespace App\Models;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Settings\Entities\MyRole;

class Users extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "users";
    protected $guarded = ["id"];

    public function district()
    {
        return $this->belongsTo(Districts::class, 'district_id');
    }

    public function roles()
    {
        return $this->belongsToMany(MyRole::class, 'role_user', 'user_id', 'role_id')->withTimestamps();
    }


}
