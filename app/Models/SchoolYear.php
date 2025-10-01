<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolYear extends Model
{
    
    protected $fillable = ['year', 'is_current'];

    public function students()
{
    return $this->hasMany(Student::class);
}

}