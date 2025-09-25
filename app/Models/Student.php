<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'teacher_id',
    ];

 
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

}