<?php

namespace Modules\Settings\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class FirebaseNotification extends Model
{
    use HasFactory, SoftDeletes;
    protected $connection = 'mysql';
    protected $guarded = ['id', 'created_at', 'updated_at'];


    public function morphable()
    {
        return $this->morphTo();
    }
}
