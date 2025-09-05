<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retreat extends Model
{
    use HasFactory;

    protected $fillable = [
        'Name', 'Grade', 'bus_id'
    ];

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }
}