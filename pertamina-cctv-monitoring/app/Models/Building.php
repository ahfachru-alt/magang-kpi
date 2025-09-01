<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'latitude',
        'longitude',
        'address',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_active' => 'boolean',
    ];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function activeRooms()
    {
        return $this->hasMany(Room::class)->where('is_active', true);
    }

    public function cctvs()
    {
        return $this->hasManyThrough(Cctv::class, Room::class);
    }

    public function activeCctvs()
    {
        return $this->hasManyThrough(Cctv::class, Room::class)
            ->where('cctvs.is_active', true);
    }
}
