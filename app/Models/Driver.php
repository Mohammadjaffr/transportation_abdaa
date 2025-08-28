<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $primaryKey = 'CardNo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'CardNo', 'Name', 'Phone', 'LicenseNo', 'Ownership', 'Wing',
        'CheckUp', 'Behavior', 'Form', 'Fitnes', 'BusNo'
    ];

    public function bus()
    {
        return $this->belongsTo(Bus::class, 'BusNo', 'BusNo');
    }

}
