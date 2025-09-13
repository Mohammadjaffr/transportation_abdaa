<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Wing extends Model
{
      use HasFactory;
    
      protected $fillable = [
        'Name',
    ];

     public function drivers()
    {
        return $this->hasMany(Driver::class);
    }

    public function region()
    {
        return $this->belongsTo(region::class);
    }
    public function students()
    {
        return $this->hasMany(Student::class);
    }
}