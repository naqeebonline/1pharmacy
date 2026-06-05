<?php

namespace App\Models\Finance;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyUserClosing extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "daily_user_closings";
    protected $primaryKey = 'id';
    protected $guarded = ["id"];
    public $timestamps = false;
}
