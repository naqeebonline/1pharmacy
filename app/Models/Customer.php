<?php

namespace App\Models;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "sup_cus_details";
    protected $primaryKey = 'SCID';
    protected $guarded = ["id"];
    public $timestamps = false;

    public function market()
    {
        return $this->belongsTo(Market::class, 'market_id');
    }
}
