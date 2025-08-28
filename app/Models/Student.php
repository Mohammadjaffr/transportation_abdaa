<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $primaryKey = 'stuId';
    public $incrementing = false;
    protected $keyType = 'integer';

    protected $fillable = [
        'stuId', 'Name', 'Grade', 'BusNo'
    ];

    public function bus()
    {
        return $this->belongsTo(Bus::class, 'BusNo', 'BusNo');
    }
}
