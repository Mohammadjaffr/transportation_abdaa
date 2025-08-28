<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presentation extends Model
{
    use HasFactory;

    protected $fillable = [
        'BusNo', 'atendTime', 'atendStudents', 'leaveTime', 'leaveStudents', 'note', 'date'
    ];
    public function bus()
    {
        return $this->belongsTo(Bus::class, 'BusNo', 'BusNo');
    }
}
