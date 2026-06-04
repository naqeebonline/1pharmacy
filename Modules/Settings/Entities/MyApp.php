<?php

namespace Modules\Settings\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class MyApp extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['title', 'description', 'icon', 'route', 'active'];

    protected $connection = "mysql";

    protected $table = 'apps';

    protected static function newFactory()
    {
        return \Modules\Settings\Database\factories\MyAppFactory::new();
    }

    public function menus(){
        return $this->hasMany(Menu::class, 'app_id', 'id');
    }

    public function myPersmissions(){
        return $this->hasMany(MyPermission::class, 'app_id', 'id');
    }

    public function routes(){
        return $this->hasMany(PermissionRoute::class, 'app_id', 'id');
    }
}
