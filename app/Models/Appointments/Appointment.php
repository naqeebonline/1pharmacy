<?php

namespace App\Models\Appointments;

use App\Models\Configuration\Consultants;
use App\Models\Patient\Patient;
use App\Models\Patient\PatientLocation;
use App\Models\User;
use App\Models\Users;
use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "appointments";
    protected $primaryKey = 'id';
    protected $guarded = ["id"];
    public $timestamps = true;

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function opd_type()
    {
        return $this->belongsTo(OpdType::class, 'opd_type_id');
    }

    public function consultant()
    {
        return $this->belongsTo(Consultants::class, 'consultant_id');
    }

    public function location()
    {
        return $this->belongsTo(PatientLocation::class, 'location_id');
    }

    public function created_by()
    {
        return $this->belongsTo(Users::class, 'created_by', 'id');
    }

    public function created_by_user()
    {
        return $this->belongsTo(Users::class, 'created_by', 'id');
    }

    public function hxComplaint()
    {
        return $this->hasOne(\App\Models\HxComplaint::class, 'appointment_id', 'id');
    }

    public function investigations()
    {
        return $this->hasMany(\App\Models\Patient\PatientInvestigation::class, 'appointment_id', 'id');
    }

    public function getAppointmentDateAttribute($value)
    {
        return date("Y-m-d h:i A", strtotime($value));
    }
}
