<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'school_year_id',
        'Grade',
        'status',
        'Phone',
        'Stu_position',
        'teacher_id',
        'driver_id',
        'region_id',
        'wing_id',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function wing()
    {
        return $this->belongsTo(Wing::class);
    }
}