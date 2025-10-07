<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PreparationDriver extends Model
{
    use HasFactory;

    protected $fillable = [
        'Atend',
        'Month',
        'driver_id',
        'region_id'
    ];
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
    public function region()
    {
        return $this->belongsTo(region::class);
    }
}