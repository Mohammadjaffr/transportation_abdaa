<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bus extends Model
{
    use HasFactory;

    protected $fillable = [
        'BusType', 'Model', 'SeatsNo', 'CustomsNo', 'StudentsNo', 'region_id','driver_id'
    ];

    public function region()
    {
        return $this->belongsTo(region::class);
    }

    public function driver()
    {
        return $this->hasOne(Driver::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

 

    public function retreats()
    {
        return $this->hasMany(Retreat::class);
    }

    public function wages()
    {
        return $this->hasMany(Wage::class);
    }
}