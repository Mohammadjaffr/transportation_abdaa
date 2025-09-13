<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Region extends Model
{
     use HasFactory;

    protected $fillable = [
        'Name','parent_id'
    ];

    public function buses()
    {
        return $this->hasMany(Bus::class);
    }

    public function wages()
    {
        return $this->hasMany(Wage::class);
    }
    public function drivers()
    {
        return $this->hasMany(Driver::class);
    }
    public function students()
    {
        return $this->hasMany(Student::class);
    }
    public function wings()
    {
        return $this->hasMany(Wing::class);
    }
    public function retreats()
    {
        return $this->hasMany(Retreat::class);
    }
}