<?php

namespace App\Models\Patient;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Relation extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "relations";
    protected $guarded = ["id"];
    protected $primaryKey = 'id';
    public $timestamps = false;
}
