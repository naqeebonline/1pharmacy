<?php

namespace Modules\Settings\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['parent_id', 'company_id', 'title', 'description', 'user_id'];

    protected $connection = "mysql";

    protected static function newFactory()
    {
        return \Modules\Settings\Database\factories\SectionFactory::new();
    }

    public function parent(){
        return $this->belongsTo(Section::class, 'parent_id', 'id');
    }

    public function children(){
        return $this->hasMany(Section::class, 'parent_id', 'id');
    }

    public function company(){
        return $this->belongsTo(Company::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function users(){
        return $this->hasMany(User::class, 'section_id', 'id');
    }
}
