<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SchoolYear;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'Name',
        'Grade',
        'Phone',
        'Stu_position',
        'wing_id',
        'Division',
        'region_id',
        'Sex',
        'status',
        'school_year_id',
        'teacher_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($student) {
            if (!$student->school_year_id) {
                $year = SchoolYear::where('is_current', true)->first();
                if ($year) {
                    $student->school_year_id = $year->id;
                }
            }
        });
    }

    public function records()
    {
        return $this->hasMany(StudentRecord::class);
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
    public function retreats()
    {
        return $this->hasMany(Retreat::class);
    }
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }
    public function preparations()
    {
        return $this->hasMany(PreparationStu::class, 'student_id');
    }
    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }
}
