<?php

namespace App\Models\Configuration;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "service_type";
    protected $guarded = ["id"];
    public $timestamps = false;
}
