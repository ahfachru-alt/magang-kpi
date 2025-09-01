<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'building_id',
        'name',
        'description',
        'floor',
        'room_number',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function cctvs()
    {
        return $this->hasMany(Cctv::class);
    }

    public function activeCctvs()
    {
        return $this->hasMany(Cctv::class)->where('is_active', true);
    }
}
