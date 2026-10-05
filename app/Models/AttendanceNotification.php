<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceNotification extends Model
{
    protected $fillable = [
        'student_id',
        'guardian_id',
        'date',
        'type',
        'channel',
        'event',
        'phone',
        'status',
        'attempts',
        'provider_message_id',
        'last_error',
        'sent_at'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
