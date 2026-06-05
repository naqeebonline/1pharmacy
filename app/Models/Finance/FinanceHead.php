<?php

namespace App\Models\Finance;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceHead extends Model
{
    use HasFactory;
    use Syncable;
    protected $table = "finance_heads";
    protected $primaryKey = 'id';
    protected $guarded = ["id"];
    public $timestamps = false;
}
