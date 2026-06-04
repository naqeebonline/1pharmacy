<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmergencyAlert extends Model
{
    use HasFactory;
    protected $table = "emergency_alerts";
    protected $guarded = ["id"];

    public function district()
    {
        return $this->belongsTo(Districts::class, 'district_id', 'id');
    }

    public function users()
    {
        return $this->belongsTo(Users::class, 'user_id');
    }
}
