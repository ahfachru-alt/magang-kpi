<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CctvResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'ip_address' => $this->ip_address,
            'status' => $this->status,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'stream_url' => $this->stream_url,
            'last_online_at' => $this->last_online_at?->toISOString(),
            'maintenance_reason' => $this->maintenance_reason,
            'maintenance_started_at' => $this->maintenance_started_at?->toISOString(),
            'maintenance_ended_at' => $this->maintenance_ended_at?->toISOString(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            
            // Relationships
            'building' => [
                'id' => $this->building->id,
                'name' => $this->building->name,
                'address' => $this->building->address,
                'latitude' => $this->building->latitude,
                'longitude' => $this->building->longitude,
            ],
            'room' => [
                'id' => $this->room->id,
                'name' => $this->room->name,
                'floor' => $this->room->floor,
            ],
            
            // Computed fields
            'is_online' => $this->status === 'online',
            'is_offline' => $this->status === 'offline',
            'is_maintenance' => $this->status === 'maintenance',
            'downtime_hours' => $this->status === 'offline' && $this->last_online_at 
                ? $this->last_online_at->diffInHours(now()) 
                : 0,
            'health_status' => $this->getHealthStatus(),
        ];
    }

    /**
     * Get health status based on CCTV status and downtime
     */
    protected function getHealthStatus()
    {
        if ($this->status === 'maintenance') {
            return 'maintenance';
        }

        if ($this->status === 'offline') {
            $downtime = $this->last_online_at ? $this->last_online_at->diffInHours(now()) : 0;
            
            if ($downtime > 24) {
                return 'critical';
            } elseif ($downtime > 12) {
                return 'high';
            } else {
                return 'medium';
            }
        }

        return 'healthy';
    }
}
