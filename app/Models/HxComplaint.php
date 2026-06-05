<?php

namespace App\Models;

use App\Models\Appointments\Appointment;
use App\Models\Patient\Patient;
use App\Models\Users;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HxComplaint extends Model
{
    use HasFactory;

    protected $table = 'hx_complaints';

    protected $fillable = [
        'appointment_id',
        'patient_id',
        'complaint',
        'bp',
        'temp',
        'pulse',
        'rr',
        'investigation',
        'recorded_by',
        'is_active',
    ];

    /**
     * Get the appointment associated with this HX complaint
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id', 'id');
    }

    /**
     * Get the patient associated with this HX complaint
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'id');
    }

    /**
     * Get the user who recorded this HX complaint
     */
    public function recordedBy()
    {
        return $this->belongsTo(Users::class, 'recorded_by', 'id');
    }

    /**
     * Get the user who recorded this HX complaint (alias)
     */
    public function created_by_user()
    {
        return $this->belongsTo(Users::class, 'recorded_by', 'id');
    }
}
