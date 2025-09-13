<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wage extends Model
{
    use HasFactory;

    protected $fillable = [
        'Fees', 'Date', 'driver_id', 'region_id',
    ];

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }

    public function region()
    {
        return $this->belongsTo(region::class);
    }
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}