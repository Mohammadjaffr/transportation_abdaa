<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wage extends Model
{
    use HasFactory;

    protected $fillable = [
        'BusNo', 'Fees', 'Date', 'LocNo'
    ];
    public function bus()
    {
        return $this->belongsTo(Bus::class, 'BusNo', 'BusNo');
    }
    public function location()
    {
        return $this->belongsTo(Location::class, 'LocNo', 'LocNo');
    }
}
