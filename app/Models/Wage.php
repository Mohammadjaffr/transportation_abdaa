<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wage extends Model
{
    use HasFactory;

    protected $fillable = [
        'Fees', 'Date', 'bus_id', 'location_id'
    ];

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}