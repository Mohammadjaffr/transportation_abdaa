<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSmsNotification extends Model
{
    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'sent_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function guardian()
    {
        return $this->belongsTo(Guardian::class);
    }
}
