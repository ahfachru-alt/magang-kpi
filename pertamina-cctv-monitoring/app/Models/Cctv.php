<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cctv extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'name',
        'ip_address',
        'stream_url',
        'status',
        'description',
        'model',
        'manufacturer',
        'last_online',
        'is_active',
    ];

    protected $casts = [
        'last_online' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function building()
    {
        return $this->room->building();
    }

    public function getStreamUrlAttribute($value)
    {
        if ($this->status === 'online') {
            $ip = $this->ip_address;
            $filename = str_replace(['.', ':', '@'], '_', parse_url($ip, PHP_URL_HOST));
            return "/live/{$filename}.m3u8";
        }
        return $value;
    }

    public function scopeOnline($query)
    {
        return $query->where('status', 'online');
    }

    public function scopeOffline($query)
    {
        return $query->where('status', 'offline');
    }

    public function scopeMaintenance($query)
    {
        return $query->where('status', 'maintenance');
    }
}
