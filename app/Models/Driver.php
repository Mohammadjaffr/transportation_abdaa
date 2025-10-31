<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'Name',
        'IDNo',
        'Phone',
        'LicenseNo',
        'Picture',
        'Ownership',
        'Bus_type',
        'No_Passengers',
        'wing_id',
        'CheckUp',
        'Behavior',
        'Form',
        'Fitnes',
    ];

  public function regions()
{
    return $this->belongsToMany(Region::class, 'driver_region');
}

    public function wing()
    {
        return $this->belongsTo(Wing::class);
    }

    public function bus()
    {
        return $this->belongsTo(Bus::class);
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

    public function preparations()
    {
        return $this->hasMany(PreparationDriver::class, 'driver_id');
    }
}