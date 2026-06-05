<?php

namespace Modules\Settings\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['app_id', 'parent_id', 'title', 'description', 'icon', 'order', 'is_collapsible'];

    protected $connection = "mysql";

    protected $table = "menus";

    protected static function newFactory()
    {
        return \Modules\Settings\Database\factories\MenuFactory::new();
    }

    public function myApp(){
        return $this->belongsTo(MyApp::class, 'app_id', 'id');
    }

    public function parent(){
        return $this->belongsTo(Menu::class, 'parent_id', 'id');
    }

    public function children(){
        return $this->hasMany(Menu::class, 'parent_id', 'id');
    }

    public function myPermissions(){
        return $this->hasMany(MyPermission::class, 'menu_id', 'id');
    }

    public function routes(){
        return $this->hasMany(PermissionRoute::class, 'menu_id', 'id');
    }
}
