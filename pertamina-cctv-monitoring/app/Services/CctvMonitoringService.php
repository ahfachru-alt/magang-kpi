<?php

namespace App\Services;

use App\Models\Cctv;
use App\Models\Building;
use App\Models\Room;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CctvMonitoringService
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Check CCTV status by pinging IP addresses
     */
    public function checkCctvStatus()
    {
        $cctvs = Cctv::with(['building', 'room'])->get();
        $statusChanges = [];

        foreach ($cctvs as $cctv) {
            $oldStatus = $cctv->status;
            $newStatus = $this->pingCctv($cctv->ip_address);
            
            if ($oldStatus !== $newStatus) {
                $statusChanges[] = [
                    'cctv' => $cctv,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                ];

                // Update CCTV status
                $cctv->update([
                    'status' => $newStatus,
                    'last_online_at' => $newStatus === 'online' ? now() : null,
                ]);

                // Send notification
                $this->notificationService->sendCctvStatusNotification($cctv, $oldStatus, $newStatus);
            }
        }

        // Cache status for quick access
        Cache::put('cctv_status_summary', [
            'total' => $cctvs->count(),
            'online' => $cctvs->where('status', 'online')->count(),
            'offline' => $cctvs->where('status', 'offline')->count(),
            'maintenance' => $cctvs->where('status', 'maintenance')->count(),
            'last_check' => now(),
        ], 300); // 5 minutes

        return $statusChanges;
    }

    /**
     * Ping CCTV IP address to check status
     */
    protected function pingCctv($ipAddress)
    {
        try {
            // Try HTTP connection first (for web-based cameras)
            $response = Http::timeout(5)->get("http://{$ipAddress}");
            if ($response->successful()) {
                return 'online';
            }
        } catch (\Exception $e) {
            // HTTP failed, try ping
        }

        try {
            // Try ping command
            $result = exec("ping -c 1 -W 3 {$ipAddress} 2>/dev/null", $output, $returnCode);
            
            if ($returnCode === 0) {
                return 'online';
            }
        } catch (\Exception $e) {
            Log::warning("Failed to ping CCTV {$ipAddress}: " . $e->getMessage());
        }

        return 'offline';
    }

    /**
     * Get CCTV status summary
     */
    public function getStatusSummary()
    {
        return Cache::remember('cctv_status_summary', 300, function () {
            $cctvs = Cctv::all();
            
            return [
                'total' => $cctvs->count(),
                'online' => $cctvs->where('status', 'online')->count(),
                'offline' => $cctvs->where('status', 'offline')->count(),
                'maintenance' => $cctvs->where('status', 'maintenance')->count(),
                'last_check' => now(),
            ];
        });
    }

    /**
     * Get building status summary
     */
    public function getBuildingStatusSummary()
    {
        return Building::with(['rooms.cctvs'])->get()->map(function ($building) {
            $totalCctvs = $building->rooms->sum(function ($room) {
                return $room->cctvs->count();
            });

            $onlineCctvs = $building->rooms->sum(function ($room) {
                return $room->cctvs->where('status', 'online')->count();
            });

            $offlineCctvs = $building->rooms->sum(function ($room) {
                return $room->cctvs->where('status', 'offline')->count();
            });

            $maintenanceCctvs = $building->rooms->sum(function ($room) {
                return $room->cctvs->where('status', 'maintenance')->count();
            });

            return [
                'id' => $building->id,
                'name' => $building->name,
                'total_cctvs' => $totalCctvs,
                'online_cctvs' => $onlineCctvs,
                'offline_cctvs' => $offlineCctvs,
                'maintenance_cctvs' => $maintenanceCctvs,
                'health_percentage' => $totalCctvs > 0 ? round(($onlineCctvs / $totalCctvs) * 100, 2) : 0,
            ];
        });
    }

    /**
     * Get room status summary
     */
    public function getRoomStatusSummary($buildingId = null)
    {
        $query = Room::with(['building', 'cctvs']);
        
        if ($buildingId) {
            $query->where('building_id', $buildingId);
        }

        return $query->get()->map(function ($room) {
            $totalCctvs = $room->cctvs->count();
            $onlineCctvs = $room->cctvs->where('status', 'online')->count();
            $offlineCctvs = $room->cctvs->where('status', 'offline')->count();
            $maintenanceCctvs = $room->cctvs->where('status', 'maintenance')->count();

            return [
                'id' => $room->id,
                'name' => $room->name,
                'building' => $room->building->name,
                'floor' => $room->floor,
                'total_cctvs' => $totalCctvs,
                'online_cctvs' => $onlineCctvs,
                'offline_cctvs' => $offlineCctvs,
                'maintenance_cctvs' => $maintenanceCctvs,
                'health_percentage' => $totalCctvs > 0 ? round(($onlineCctvs / $totalCctvs) * 100, 2) : 0,
            ];
        });
    }

    /**
     * Get CCTV performance metrics
     */
    public function getPerformanceMetrics()
    {
        $cctvs = Cctv::all();
        
        $totalUptime = 0;
        $totalDowntime = 0;
        
        foreach ($cctvs as $cctv) {
            if ($cctv->last_online_at) {
                $lastOnline = $cctv->last_online_at;
                $now = now();
                
                if ($cctv->status === 'online') {
                    $totalUptime += $lastOnline->diffInSeconds($now);
                } else {
                    $totalDowntime += $lastOnline->diffInSeconds($now);
                }
            }
        }

        $totalTime = $totalUptime + $totalDowntime;
        $uptimePercentage = $totalTime > 0 ? round(($totalUptime / $totalTime) * 100, 2) : 0;

        return [
            'total_cctvs' => $cctvs->count(),
            'uptime_percentage' => $uptimePercentage,
            'total_uptime_hours' => round($totalUptime / 3600, 2),
            'total_downtime_hours' => round($totalDowntime / 3600, 2),
            'average_response_time' => $this->getAverageResponseTime(),
            'last_maintenance' => $cctvs->where('status', 'maintenance')->max('updated_at'),
        ];
    }

    /**
     * Get average response time for online CCTVs
     */
    protected function getAverageResponseTime()
    {
        $onlineCctvs = Cctv::where('status', 'online')->get();
        $totalResponseTime = 0;
        $testedCctvs = 0;

        foreach ($onlineCctvs as $cctv) {
            $startTime = microtime(true);
            
            try {
                $response = Http::timeout(3)->get("http://{$cctv->ip_address}");
                if ($response->successful()) {
                    $responseTime = (microtime(true) - $startTime) * 1000; // Convert to milliseconds
                    $totalResponseTime += $responseTime;
                    $testedCctvs++;
                }
            } catch (\Exception $e) {
                // Skip failed connections
            }
        }

        return $testedCctvs > 0 ? round($totalResponseTime / $testedCctvs, 2) : 0;
    }

    /**
     * Get CCTV alerts (offline for more than 1 hour)
     */
    public function getCctvAlerts()
    {
        return Cctv::where('status', 'offline')
            ->where('last_online_at', '<=', now()->subHour())
            ->with(['building', 'room'])
            ->get()
            ->map(function ($cctv) {
                $downtime = $cctv->last_online_at ? $cctv->last_online_at->diffInHours(now()) : 0;
                
                return [
                    'id' => $cctv->id,
                    'name' => $cctv->name,
                    'ip_address' => $cctv->ip_address,
                    'building' => $cctv->building->name,
                    'room' => $cctv->room->name,
                    'downtime_hours' => $downtime,
                    'last_online' => $cctv->last_online_at,
                    'severity' => $downtime > 24 ? 'critical' : ($downtime > 12 ? 'high' : 'medium'),
                ];
            })
            ->sortByDesc('downtime_hours');
    }

    /**
     * Start maintenance mode for CCTV
     */
    public function startMaintenance($cctvId, $reason = null)
    {
        try {
            $cctv = Cctv::findOrFail($cctvId);
            $oldStatus = $cctv->status;
            
            $cctv->update([
                'status' => 'maintenance',
                'maintenance_reason' => $reason,
                'maintenance_started_at' => now(),
            ]);

            // Send notification
            $this->notificationService->sendCctvStatusNotification($cctv, $oldStatus, 'maintenance');

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to start maintenance: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * End maintenance mode for CCTV
     */
    public function endMaintenance($cctvId)
    {
        try {
            $cctv = Cctv::findOrFail($cctvId);
            $oldStatus = $cctv->status;
            
            $cctv->update([
                'status' => 'online',
                'maintenance_reason' => null,
                'maintenance_started_at' => null,
                'maintenance_ended_at' => now(),
            ]);

            // Send notification
            $this->notificationService->sendCctvStatusNotification($cctv, $oldStatus, 'online');

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to end maintenance: ' . $e->getMessage());
            return false;
        }
    }
}