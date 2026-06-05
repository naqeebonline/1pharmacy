<?php
namespace App\Models;

use App\Traits\Syncable;
use Illuminate\Database\Eloquent\Model;

class FinanceHead extends Model
{
    protected $table = 'finance_heads';
    use Syncable;
    protected $fillable = [
        'parent_id', 'level', 'head_code', 'name', 'type', 'description', 'is_contra', 'is_sync'
    ];
    public $timestamps = false;

    public function parent()
    {
        return $this->belongsTo(FinanceHead::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(FinanceHead::class, 'parent_id');
    }
}
