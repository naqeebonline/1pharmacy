<?php

namespace Modules\Settings\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mouza extends Model
{
    use HasFactory, SoftDeletes;
    protected $connection = 'mysql';
    protected $guarded = ['id', 'created_at', 'updated_at'];


    protected static function newFactory()
    {
        return \Modules\Settings\Database\factories\MouzaFactory::new();
    }
}
