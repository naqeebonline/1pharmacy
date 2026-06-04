<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GenericName extends Model
{
    use HasFactory;
    protected $table = "item_generic_name";
    protected $guarded = ["id"];
    public $timestamps = false;

}
