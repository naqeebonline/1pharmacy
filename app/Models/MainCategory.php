<?php

namespace App\Models;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainCategory extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "main_category";
    protected $guarded = ["id"];
    public $timestamps = false;
}
