<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bus extends Model
{
    use HasFactory;

    protected $primaryKey = 'BusNo';
    public $incrementing = false;
    protected $keyType = 'integer';

    protected $fillable = [
        'BusNo', 'BusType', 'Model', 'SeatsNo', 'CustomsNo', 'StudentsNo', 'LocNo'
    ];

    public function location()
    {
        return $this->belongsTo(Location::class, 'LocNo', 'LocNo');
    }

    public function driver()
    {
        return $this->hasOne(Driver::class, 'BusNo', 'BusNo');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'BusNo', 'BusNo');
    }
}
