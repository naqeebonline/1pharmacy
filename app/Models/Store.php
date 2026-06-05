<?php

namespace App\Models;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "store";
    protected $primaryKey = 'id';
    protected $guarded = ["id"];
    public $timestamps = false;

  
    
}
