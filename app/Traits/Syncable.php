<?php

namespace App\Traits;

trait Syncable
{
    public static function bootSyncable()
    {
        static::creating(function ($model) {
            $model->is_sync = 0;
        });

        static::updating(function ($model) {
            $model->is_sync = 0;
        });
    }
}

