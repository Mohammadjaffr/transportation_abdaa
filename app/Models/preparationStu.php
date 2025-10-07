<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreparationStu extends Model
{
    use HasFactory;
   
    protected $fillable = [
        'Atend',
        'Date',
        'type',
        'student_id',
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