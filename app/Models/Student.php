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
        'Picture',
        'Stu_position',
        'wing_id',
        'Division',
        'region_id',
        'Sex',
    ];

    public function bus()
    {
        return $this->belongsTo(Bus::class);
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
}
