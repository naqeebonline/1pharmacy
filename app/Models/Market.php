<?php

namespace App\Models;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Market extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "market";
    protected $guarded = ["id"];
    public $timestamps = false;
}
