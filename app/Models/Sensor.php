<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'devices_id',
        // 'devices_name',
        'data'
    ];

    // public function devices()
    // {
    //     return $this->belongsTo(Device::class, 'name', 'devices_name');
    // }
}
