<?php

namespace App\Models\Configuration;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcedureType extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "procedure_type";
    protected $guarded = ["id"];
    public $timestamps = false;
}
