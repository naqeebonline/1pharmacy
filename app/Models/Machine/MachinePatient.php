<?php

namespace App\Models\Machine;

use App\Models\Patient\Patient;
use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachinePatient extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "machine_patients";
    protected $guarded = ["id"];
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function patient(){
        return $this->belongsTo(Patient::class, "patient_id", "id");
    }

    public function machine_shift(){
        return $this->belongsTo(MachineShift::class, "machine_shift_id", "id");
    }
    
    public function machine_category(){
        return $this->belongsTo(MachineCategory::class, "machine_category_id", "id");
    }

}
