<?php

namespace App\Models\Patient;

use App\Models\Configuration\Consultants;
use App\Models\Configuration\Investigation;
use App\Models\Configuration\InvestigationSubCategory;
use App\Models\Configuration\Ward;
use App\Models\Configuration\WardBed;

use App\Models\Users;
use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class PatientInvestigation extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "patient_investigations";
    protected $guarded = ["id"];
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $appends = ['my_date'];


    public function patient()
    {
        return $this->belongsTo(Patient::class, "patient_id", "id");
    }

    public function admission()
    {
        return $this->belongsTo(PatientAdmission::class, "admission_id", "id");
    }
    public function investigation()
    {
        return $this->belongsTo(InvestigationSubCategory::class, 'investigation_sub_category_id', "id");
    }

    public function consultant()
    {
        return $this->belongsTo(Consultants::class, 'consultant_id', "id");
    }

    public function investigationResult()
    {
        return $this->hasMany(InvestigationResult::class, 'patient_investigation_id', "id");
    }
    public function subCategory(){
        return $this->belongsTo(InvestigationSubCategory::class, "investigation_sub_category_id");
    }

    public function users(){
        return $this->belongsTo(Users::class, "created_by","id");
    }
    public function created_by_user(){
        return $this->belongsTo(Users::class, "created_by","id");
    }

  /*  public function getInvDateAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y H:i A'); // Format as Day-Month-Year
    }

    public function getInvOutDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d-m-Y h:i A') : "";
    }*/

    public function getMyDateAttribute()
    {
        return ($this->inv_date) ? Carbon::parse($this->inv_date)->format('Y-m-d') : "";
    }
}
