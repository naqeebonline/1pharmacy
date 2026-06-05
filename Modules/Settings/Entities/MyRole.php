<?php

namespace Modules\Settings\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use jeremykenedy\LaravelRoles\Models\Role;

class MyRole extends Role
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = ['name', 'slug', 'description', 'level'];

    protected $connection = "mysql";

    protected $table = "roles";
    protected $primaryKey = "id";

    protected static function newFactory()
    {
        return \Modules\Settings\Database\factories\MyRoleFactory::new();
    }



    // role and permission many to many
    public function permissions()
    {
        return $this->belongsToMany(MyPermission::class, 'permission_role', 'role_id', 'permission_id')->withTimestamps();
    }

    // role and user many to many
    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user', 'role_id', 'user_id')->withTimestamps();
    }

    public function roleAction()
    {
        return $this->hasOne(\Modules\Agriculture\Entities\RoleAction::class, 'role_id');
    }
}
