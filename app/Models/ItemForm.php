<?php

namespace App\Models;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemForm extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "item_form";
    protected $guarded = ["id"];
    public $timestamps = false;

}
