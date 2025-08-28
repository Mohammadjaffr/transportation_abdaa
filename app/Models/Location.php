<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Location extends Model
{
    use HasFactory;

    protected $primaryKey = 'LocNo';
    public $incrementing = false;
    protected $keyType = 'integer';

    protected $fillable = [
        'LocNo', 'Name', 'DailyAmount', 'Fees'
    ];

    public function buses()
    {
        return $this->hasMany(Bus::class, 'LocNo', 'LocNo');
    }
}
