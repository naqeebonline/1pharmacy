<?php

namespace App\Models\Configuration;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultantProcedure extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "consultant_procedures";
    protected $guarded = ["id"];
    public $timestamps = false;

    public function consultant()
    {
        return $this->belongsTo(Consultants::class, 'consultant_id');
    }

    public function procedure()
    {
        return $this->belongsTo(ProcedureType::class, 'procedure_type_id');
    }
}
