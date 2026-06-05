<?php

namespace App\Models\Patient;

use App\Traits\Syncable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientVital extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "patient_vitals";
    protected $guarded = ["id"];
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $appends = ['my_date'];



    public function getMyDateAttribute()
    {
        return ($this->date) ? Carbon::parse($this->date)->format('Y-m-d') : "";
    }
}
