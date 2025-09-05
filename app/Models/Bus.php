<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bus extends Model
{
    use HasFactory;

    protected $fillable = [
        'BusType', 'Model', 'SeatsNo', 'CustomsNo', 'StudentsNo', 'location_id'
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function driver()
    {
        return $this->hasOne(Driver::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function presentations()
    {
        return $this->hasMany(Presentation::class);
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