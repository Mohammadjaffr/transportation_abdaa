<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guardian extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'national_id',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
    public function students()
    {
        return $this->belongsToMany(Student::class, 'guardian_student', 'guardian_id', 'student_id')
            ->withPivot([
                'relationship',
                'is_primary',
                'receive_notifications'
            ])
            ->withTimestamps();
    }

    public function user()
    {
        return $this->hasOne(User::class,'guardian_id');
    }
}