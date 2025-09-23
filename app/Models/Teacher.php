<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Teacher extends Model
{
    use HasFactory;
    
    protected $fillable=[
        'Name',
        'Sex'
    ];

    public function student(){
        return $this->hasMany(Student::class);
    }
}