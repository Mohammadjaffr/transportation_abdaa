<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retreat extends Model
{
       use HasFactory;

    protected $fillable = [
        'student_id',
        'Grade',
        'Division',
        'Date_of_interruption',
        'Reason',
        'region_id',
        'driver_id'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

}