<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'Name', 'DailyAmount', 'Fees'
    ];

    public function buses()
    {
        return $this->hasMany(Bus::class);
    }

    public function wages()
    {
        return $this->hasMany(Wage::class);
    }
}