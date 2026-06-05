<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tehsils extends Model
{
    use HasFactory;
    protected $table = "tehsils";
    protected $guarded = ["id"];
}
