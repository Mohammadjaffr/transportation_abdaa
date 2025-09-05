<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
         'Name', 'Phone', 'LicenseNo', 'Ownership', 'Wing',
        'CheckUp', 'Behavior', 'Form', 'Fitnes', 'bus_id'
    ];

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }
}