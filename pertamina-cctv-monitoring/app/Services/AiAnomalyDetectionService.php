<?php

namespace App\Services;

use App\Models\Cctv;
use App\Models\CctvAnomaly;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Phpml\Math\Statistic\StandardDeviation;
use Phpml\Math\Statistic\Mean;
use Phpml\Math\Statistic\Correlation;

class AiAnomalyDetectionService
{
    protected $notificationService;
    protected $monitoringService;

    public function __construct(
        NotificationService $notificationService,
        CctvMonitoringService $monitoringService
    ) {
        $this->notificationService = $notificationService;
        $this->monitoringService = $monitoringService;
    }

    /**
     * Analyze CCTV data for anomalies using machine learning
     */
    public function detectAnomalies()
    {
        try {
            $anomalies = [];
            $cctvs = Cctv::with(['building', 'room'])->get();

            foreach ($cctvs as $cctv) {
                $cctvAnomalies = $this->analyzeCctvAnomalies($cctv);
                if (!empty($cctvAnomalies)) {
                    $anomalies = array_merge($anomalies, $cctvAnomalies);
                }
            }

            // Store anomalies in database
            $this->storeAnomalies($anomalies);

            // Send notifications for critical anomalies
            $this->notifyCriticalAnomalies($anomalies);

            return $anomalies;
        } catch (\Exception $e) {
            Log::error('Anomaly detection failed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Analyze anomalies for a specific CCTV
     */
    protected function analyzeCctvAnomalies($cctv)
    {
        $anomalies = [];

        // 1. Response Time Anomaly Detection
        $responseTimeAnomaly = $this->detectResponseTimeAnomaly($cctv);
        if ($responseTimeAnomaly) {
            $anomalies[] = $responseTimeAnomaly;
        }

        // 2. Status Pattern Anomaly Detection
        $statusAnomaly = $this->detectStatusPatternAnomaly($cctv);
        if ($statusAnomaly) {
            $anomalies[] = $statusAnomaly;
        }

        // 3. Usage Pattern Anomaly Detection
        $usageAnomaly = $this->detectUsagePatternAnomaly($cctv);
        if ($usageAnomaly) {
            $anomalies[] = $usageAnomaly;
        }

        // 4. Geographic Anomaly Detection
        $geographicAnomaly = $this->detectGeographicAnomaly($cctv);
        if ($geographicAnomaly) {
            $anomalies[] = $geographicAnomaly;
        }

        return $anomalies;
    }

    /**
     * Detect response time anomalies using statistical analysis
     */
    protected function detectResponseTimeAnomaly($cctv)
    {
        // Get historical response times (last 24 hours)
        $responseTimes = $this->getHistoricalResponseTimes($cctv->id, 24);
        
        if (count($responseTimes) < 10) {
            return null; // Need more data for analysis
        }

        $mean = Mean::arithmetic($responseTimes);
        $stdDev = StandardDeviation::population($responseTimes);
        $threshold = 2.5; // 2.5 standard deviations

        $currentResponseTime = $this->getCurrentResponseTime($cctv->ip_address);
        
        if ($currentResponseTime === null) {
            return null;
        }

        $zScore = abs(($currentResponseTime - $mean) / $stdDev);

        if ($zScore > $threshold) {
            return [
                'cctv_id' => $cctv->id,
                'type' => 'response_time_anomaly',
                'severity' => $this->calculateSeverity($zScore),
                'description' => "Response time anomaly detected. Current: {$currentResponseTime}ms, Normal range: " . 
                               round($mean - $threshold * $stdDev) . "ms - " . 
                               round($mean + $threshold * $stdDev) . "ms",
                'data' => [
                    'current_response_time' => $currentResponseTime,
                    'mean_response_time' => round($mean, 2),
                    'standard_deviation' => round($stdDev, 2),
                    'z_score' => round($zScore, 2),
                    'threshold' => $threshold,
                ],
                'detected_at' => now(),
            ];
        }

        return null;
    }

    /**
     * Detect status pattern anomalies
     */
    protected function detectStatusPatternAnomaly($cctv)
    {
        // Get status history (last 7 days)
        $statusHistory = $this->getStatusHistory($cctv->id, 7);
        
        if (count($statusHistory) < 20) {
            return null;
        }

        // Calculate status change frequency
        $statusChanges = 0;
        $previousStatus = null;
        
        foreach ($statusHistory as $status) {
            if ($previousStatus && $status['status'] !== $previousStatus) {
                $statusChanges++;
            }
            $previousStatus = $status['status'];
        }

        $changeRate = $statusChanges / count($statusHistory);
        $normalChangeRate = 0.1; // 10% change rate is considered normal

        if ($changeRate > $normalChangeRate * 2) { // 2x normal rate
            return [
                'cctv_id' => $cctv->id,
                'type' => 'status_pattern_anomaly',
                'severity' => 'high',
                'description' => "Unusual status change pattern detected. Change rate: " . 
                               round($changeRate * 100, 1) . "%, Normal: " . 
                               round($normalChangeRate * 100, 1) . "%",
                'data' => [
                    'status_changes' => $statusChanges,
                    'total_records' => count($statusHistory),
                    'change_rate' => round($changeRate, 3),
                    'normal_change_rate' => $normalChangeRate,
                ],
                'detected_at' => now(),
            ];
        }

        return null;
    }

    /**
     * Detect usage pattern anomalies
     */
    protected function detectUsagePatternAnomaly($cctv)
    {
        // Get usage patterns (last 30 days)
        $usagePatterns = $this->getUsagePatterns($cctv->id, 30);
        
        if (count($usagePatterns) < 15) {
            return null;
        }

        // Analyze daily usage patterns
        $dailyUsage = [];
        foreach ($usagePatterns as $pattern) {
            $date = $pattern['date'];
            if (!isset($dailyUsage[$date])) {
                $dailyUsage[$date] = 0;
            }
            $dailyUsage[$date] += $pattern['usage_minutes'];
        }

        $usageValues = array_values($dailyUsage);
        $mean = Mean::arithmetic($usageValues);
        $stdDev = StandardDeviation::population($usageValues);
        $threshold = 2.0;

        $todayUsage = $usageValues[count($usageValues) - 1] ?? 0;
        $zScore = abs(($todayUsage - $mean) / $stdDev);

        if ($zScore > $threshold) {
            return [
                'cctv_id' => $cctv->id,
                'type' => 'usage_pattern_anomaly',
                'severity' => $this->calculateSeverity($zScore),
                'description' => "Usage pattern anomaly detected. Today: {$todayUsage} minutes, Normal range: " . 
                               round($mean - $threshold * $stdDev) . " - " . 
                               round($mean + $threshold * $stdDev) . " minutes",
                'data' => [
                    'today_usage' => $todayUsage,
                    'mean_usage' => round($mean, 2),
                    'standard_deviation' => round($stdDev, 2),
                    'z_score' => round($zScore, 2),
                ],
                'detected_at' => now(),
            ];
        }

        return null;
    }

    /**
     * Detect geographic anomalies (unusual movement patterns)
     */
    protected function detectGeographicAnomaly($cctv)
    {
        if (!$cctv->latitude || !$cctv->longitude) {
            return null;
        }

        // Get location history (last 24 hours)
        $locationHistory = $this->getLocationHistory($cctv->id, 24);
        
        if (count($locationHistory) < 5) {
            return null;
        }

        // Calculate distance variations
        $distances = [];
        $previousLocation = null;
        
        foreach ($locationHistory as $location) {
            if ($previousLocation) {
                $distance = $this->calculateDistance(
                    $previousLocation['latitude'],
                    $previousLocation['longitude'],
                    $location['latitude'],
                    $location['longitude']
                );
                $distances[] = $distance;
            }
            $previousLocation = $location;
        }

        if (empty($distances)) {
            return null;
        }

        $meanDistance = Mean::arithmetic($distances);
        $stdDevDistance = StandardDeviation::population($distances);
        $threshold = 3.0;

        $lastDistance = end($distances);
        $zScore = abs(($lastDistance - $meanDistance) / $stdDevDistance);

        if ($zScore > $threshold) {
            return [
                'cctv_id' => $cctv->id,
                'type' => 'geographic_anomaly',
                'severity' => 'critical',
                'description' => "Geographic anomaly detected. Unusual movement pattern: " . 
                               round($lastDistance, 2) . " meters, Normal range: " . 
                               round($meanDistance - $threshold * $stdDevDistance, 2) . " - " . 
                               round($meanDistance + $threshold * $stdDevDistance, 2) . " meters",
                'data' => [
                    'last_distance' => round($lastDistance, 2),
                    'mean_distance' => round($meanDistance, 2),
                    'standard_deviation' => round($stdDevDistance, 2),
                    'z_score' => round($zScore, 2),
                ],
                'detected_at' => now(),
            ];
        }

        return null;
    }

    /**
     * Calculate severity based on Z-score
     */
    protected function calculateSeverity($zScore)
    {
        if ($zScore >= 4.0) {
            return 'critical';
        } elseif ($zScore >= 3.0) {
            return 'high';
        } elseif ($zScore >= 2.5) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    /**
     * Get historical response times
     */
    protected function getHistoricalResponseTimes($cctvId, $hours)
    {
        // This would typically come from a monitoring log table
        // For now, we'll simulate with random data
        $responseTimes = [];
        for ($i = 0; $i < $hours; $i++) {
            $responseTimes[] = rand(50, 200); // Simulated response times
        }
        return $responseTimes;
    }

    /**
     * Get current response time
     */
    protected function getCurrentResponseTime($ipAddress)
    {
        try {
            $startTime = microtime(true);
            $response = \Http::timeout(3)->get("http://{$ipAddress}");
            if ($response->successful()) {
                return (microtime(true) - $startTime) * 1000;
            }
        } catch (\Exception $e) {
            // CCTV might be offline
        }
        return null;
    }

    /**
     * Get status history
     */
    protected function getStatusHistory($cctvId, $days)
    {
        // This would typically come from a status log table
        // For now, we'll simulate with random data
        $statuses = ['online', 'offline', 'maintenance'];
        $history = [];
        
        for ($i = 0; $i < $days * 24; $i++) {
            $history[] = [
                'status' => $statuses[array_rand($statuses)],
                'timestamp' => now()->subHours($i),
            ];
        }
        
        return $history;
    }

    /**
     * Get usage patterns
     */
    protected function getUsagePatterns($cctvId, $days)
    {
        // This would typically come from a usage log table
        // For now, we'll simulate with random data
        $patterns = [];
        
        for ($i = 0; $i < $days; $i++) {
            $patterns[] = [
                'date' => now()->subDays($i)->format('Y-m-d'),
                'usage_minutes' => rand(60, 1440), // 1-24 hours
            ];
        }
        
        return $patterns;
    }

    /**
     * Get location history
     */
    protected function getLocationHistory($cctvId, $hours)
    {
        // This would typically come from a location log table
        // For now, we'll simulate with random data
        $history = [];
        
        for ($i = 0; $i < $hours; $i++) {
            $history[] = [
                'latitude' => rand(-6, -5) + (rand(0, 1000) / 1000),
                'longitude' => rand(106, 107) + (rand(0, 1000) / 1000),
                'timestamp' => now()->subHours($i),
            ];
        }
        
        return $history;
    }

    /**
     * Calculate distance between two points (Haversine formula)
     */
    protected function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Earth's radius in meters

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Store anomalies in database
     */
    protected function storeAnomalies($anomalies)
    {
        foreach ($anomalies as $anomaly) {
            CctvAnomaly::create([
                'cctv_id' => $anomaly['cctv_id'],
                'type' => $anomaly['type'],
                'severity' => $anomaly['severity'],
                'description' => $anomaly['description'],
                'data' => $anomaly['data'],
                'detected_at' => $anomaly['detected_at'],
                'resolved_at' => null,
                'status' => 'active',
            ]);
        }
    }

    /**
     * Notify critical anomalies
     */
    protected function notifyCriticalAnomalies($anomalies)
    {
        $criticalAnomalies = array_filter($anomalies, function ($anomaly) {
            return $anomaly['severity'] === 'critical';
        });

        foreach ($criticalAnomalies as $anomaly) {
            $cctv = Cctv::find($anomaly['cctv_id']);
            if ($cctv) {
                $this->notificationService->sendToAllAdmins(
                    'Critical Anomaly Detected',
                    "AI detected critical anomaly in CCTV {$cctv->name}: {$anomaly['description']}",
                    'critical',
                    $anomaly
                );
            }
        }
    }

    /**
     * Get anomaly statistics
     */
    public function getAnomalyStatistics($days = 30)
    {
        $anomalies = CctvAnomaly::where('detected_at', '>=', now()->subDays($days))->get();
        
        $statistics = [
            'total' => $anomalies->count(),
            'by_type' => $anomalies->groupBy('type')->map->count(),
            'by_severity' => $anomalies->groupBy('severity')->map->count(),
            'by_status' => $anomalies->groupBy('status')->map->count(),
            'trend' => $this->calculateAnomalyTrend($anomalies),
        ];

        return $statistics;
    }

    /**
     * Calculate anomaly trend
     */
    protected function calculateAnomalyTrend($anomalies)
    {
        $dailyCounts = [];
        
        foreach ($anomalies as $anomaly) {
            $date = $anomaly->detected_at->format('Y-m-d');
            if (!isset($dailyCounts[$date])) {
                $dailyCounts[$date] = 0;
            }
            $dailyCounts[$date]++;
        }

        // Calculate trend (simple linear regression)
        $dates = array_keys($dailyCounts);
        $counts = array_values($dailyCounts);
        
        if (count($dates) < 2) {
            return 'insufficient_data';
        }

        $n = count($dates);
        $sumX = array_sum($dates);
        $sumY = array_sum($counts);
        $sumXY = 0;
        $sumX2 = 0;
        
        for ($i = 0; $i < $n; $i++) {
            $sumXY += $dates[$i] * $counts[$i];
            $sumX2 += $dates[$i] * $dates[$i];
        }
        
        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        
        if ($slope > 0.1) {
            return 'increasing';
        } elseif ($slope < -0.1) {
            return 'decreasing';
        } else {
            return 'stable';
        }
    }
}