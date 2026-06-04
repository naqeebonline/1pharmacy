<?php

namespace Modules\Settings\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermissionRoute extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['permission_id', 'app_id', 'menu_id', 'is_default', 'route', 'title', 'description'];

    protected $connection = "mysql";

    protected static function newFactory()
    {
        return \Modules\Settings\Database\factories\PermissionRouteFactory::new();
    }

    public function myPermission(){
        return $this->belongsTo(MyPermission::class, 'permission_id', 'id');
    }

    public function myApp(){
        return $this->belongsTo(MyApp::class, 'app_id', 'id');
    }

    public function menu(){
        return $this->belongsTo(Menu::class, 'menu_id', 'id');
    }
}
