<?php

namespace App\Models\Patient;

use App\Models\Configuration\District;
use App\Traits\Syncable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "patients";
    protected $guarded = ["id"];
    protected $primaryKey = 'id';
    protected $appends = ['formatted_date'];
    public $timestamps = true;


    public function location()
    {
        return $this->belongsTo(PatientLocation::class, "location_id", "id");
    }

    public function relation()
    {
        return $this->belongsTo(Relation::class, "relation_id", "id");
    }

    public function district()
    {
        return $this->belongsTo(District::class, "district_id", "id");
    }

    public function appointments()
    {
        return $this->hasMany(\App\Models\Appointments\Appointment::class, "patient_id", "id");
    }

    public function getRegdateAttribute($value)
    {
        return ($value) ? Carbon::parse($value)->format('d-m-Y h:i A') : ""; // Format as Day-Month-Year
    }

    public function setRegdateAttribute($value)
    {
        return ($value) ? Carbon::parse($value)->format('Y-m-d H:i:s') : ""; // Format as Day-Month-Year
    }

    public function getFormattedDateAttribute($value)
    {
        return $value ? date("Y-m-d h:i A", strtotime($value)) : "";
    }
}
