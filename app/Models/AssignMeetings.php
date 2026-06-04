<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignMeetings extends Model
{
    use HasFactory;
    protected $table = "assign_meetings";
    protected $guarded = ["id"];

    public function users()
    {
        return $this->belongsTo(User::class, 'to_user');
    }

    public function meetings()
    {
        return $this->belongsTo(Meeting::class, 'zoom_meeting_id','zoom_meeting_id');
    }
}
