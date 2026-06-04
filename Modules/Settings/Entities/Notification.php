<?php

namespace Modules\Settings\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Settings\Database\factories\NotificationFactory;

class Notification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['title', 'description', 'link', 'notifiable_id', 'notifiable_type', 'app_id', 'status', 'mobile_app', 'extra'];

    protected static function newFactory(): NotificationFactory
    {
        //return NotificationFactory::new();
    }

    public function notifiable()
    {
        return $this->morphTo();
    }


}
