<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Services\AiAnomalyDetectionService;
use App\Services\CctvMonitoringService;
use App\Models\CctvAnomaly;
use App\Models\Cctv;
use Illuminate\Support\Facades\Cache;

class AiAnalyticsDashboard extends Component
{
    public $anomalyStatistics = [];
    public $recentAnomalies = [];
    public $aiInsights = [];
    public $performanceMetrics = [];
    public $trendAnalysis = [];
    public $predictiveInsights = [];
    public $lastAnalysis = null;

    protected $aiService;
    protected $monitoringService;

    public function boot(
        AiAnomalyDetectionService $aiService,
        CctvMonitoringService $monitoringService
    ) {
        $this->aiService = $aiService;
        $this->monitoringService = $monitoringService;
    }

    public function mount()
    {
        $this->loadAnalytics();
    }

    public function loadAnalytics()
    {
        // Load anomaly statistics
        $this->anomalyStatistics = $this->aiService->getAnomalyStatistics(30);
        
        // Load recent anomalies
        $this->recentAnomalies = CctvAnomaly::with(['cctv.building', 'cctv.room'])
            ->where('status', 'active')
            ->orderBy('detected_at', 'desc')
            ->limit(10)
            ->get();
        
        // Load performance metrics
        $this->performanceMetrics = $this->monitoringService->getPerformanceMetrics();
        
        // Generate AI insights
        $this->generateAiInsights();
        
        // Generate trend analysis
        $this->generateTrendAnalysis();
        
        // Generate predictive insights
        $this->generatePredictiveInsights();
        
        $this->lastAnalysis = now();
    }

    protected function generateAiInsights()
    {
        $this->aiInsights = [
            [
                'type' => 'anomaly_trend',
                'title' => 'Anomaly Trend Analysis',
                'description' => $this->analyzeAnomalyTrend(),
                'severity' => $this->getTrendSeverity(),
                'recommendation' => $this->getTrendRecommendation(),
            ],
            [
                'type' => 'performance_insight',
                'title' => 'Performance Optimization',
                'description' => $this->analyzePerformance(),
                'severity' => 'medium',
                'recommendation' => 'Consider implementing predictive maintenance for high-risk CCTVs',
            ],
            [
                'type' => 'security_insight',
                'title' => 'Security Assessment',
                'description' => $this->analyzeSecurity(),
                'severity' => 'low',
                'recommendation' => 'System security is within acceptable parameters',
            ],
            [
                'type' => 'maintenance_insight',
                'title' => 'Maintenance Planning',
                'description' => $this->analyzeMaintenance(),
                'severity' => 'medium',
                'recommendation' => 'Schedule preventive maintenance for aging equipment',
            ],
        ];
    }

    protected function generateTrendAnalysis()
    {
        $this->trendAnalysis = [
            'anomaly_frequency' => $this->calculateAnomalyFrequency(),
            'performance_trends' => $this->calculatePerformanceTrends(),
            'maintenance_patterns' => $this->calculateMaintenancePatterns(),
            'geographic_distribution' => $this->calculateGeographicDistribution(),
        ];
    }

    protected function generatePredictiveInsights()
    {
        $this->predictiveInsights = [
            'predicted_failures' => $this->predictFailures(),
            'maintenance_schedule' => $this->predictMaintenanceSchedule(),
            'capacity_planning' => $this->predictCapacityNeeds(),
            'risk_assessment' => $this->assessRiskLevels(),
        ];
    }

    protected function analyzeAnomalyTrend()
    {
        $trend = $this->anomalyStatistics['trend'] ?? 'stable';
        
        switch ($trend) {
            case 'increasing':
                return 'Anomaly detection rate is increasing, indicating potential system stress or emerging issues.';
            case 'decreasing':
                return 'Anomaly detection rate is decreasing, suggesting improved system stability.';
            default:
                return 'Anomaly detection rate is stable, indicating consistent system performance.';
        }
    }

    protected function getTrendSeverity()
    {
        $trend = $this->anomalyStatistics['trend'] ?? 'stable';
        
        if ($trend === 'increasing') {
            $criticalCount = $this->anomalyStatistics['by_severity']['critical'] ?? 0;
            return $criticalCount > 5 ? 'critical' : 'high';
        }
        
        return 'medium';
    }

    protected function getTrendRecommendation()
    {
        $trend = $this->anomalyStatistics['trend'] ?? 'stable';
        
        switch ($trend) {
            case 'increasing':
                return 'Immediate investigation required. Consider system-wide health check and preventive measures.';
            case 'decreasing':
                return 'Continue current maintenance practices. System is improving.';
            default:
                return 'Monitor for changes. Current practices are effective.';
        }
    }

    protected function analyzePerformance()
    {
        $uptime = $this->performanceMetrics['uptime_percentage'] ?? 0;
        
        if ($uptime >= 99.5) {
            return 'Excellent system uptime. Performance is optimal.';
        } elseif ($uptime >= 98) {
            return 'Good system uptime. Minor optimizations possible.';
        } else {
            return 'System uptime below target. Immediate attention required.';
        }
    }

    protected function analyzeSecurity()
    {
        $offlineCctvs = Cctv::where('status', 'offline')->count();
        $totalCctvs = Cctv::count();
        $offlinePercentage = $totalCctvs > 0 ? ($offlineCctvs / $totalCctvs) * 100 : 0;
        
        if ($offlinePercentage > 20) {
            return 'High number of offline CCTVs detected. Security risk assessment needed.';
        } elseif ($offlinePercentage > 10) {
            return 'Moderate number of offline CCTVs. Monitor for security implications.';
        } else {
            return 'Low number of offline CCTVs. Security status is good.';
        }
    }

    protected function analyzeMaintenance()
    {
        $maintenanceCctvs = Cctv::where('status', 'maintenance')->count();
        $totalCctvs = Cctv::count();
        $maintenancePercentage = $totalCctvs > 0 ? ($maintenanceCctvs / $totalCctvs) * 100 : 0;
        
        if ($maintenancePercentage > 15) {
            return 'High maintenance load detected. Consider resource allocation review.';
        } elseif ($maintenancePercentage > 8) {
            return 'Moderate maintenance load. Monitor maintenance efficiency.';
        } else {
            return 'Low maintenance load. System is well-maintained.';
        }
    }

    protected function calculateAnomalyFrequency()
    {
        $totalAnomalies = $this->anomalyStatistics['total'] ?? 0;
        $days = 30;
        
        return [
            'daily_average' => round($totalAnomalies / $days, 2),
            'weekly_average' => round($totalAnomalies / ($days / 7), 2),
            'trend' => $this->anomalyStatistics['trend'] ?? 'stable',
        ];
    }

    protected function calculatePerformanceTrends()
    {
        $uptime = $this->performanceMetrics['uptime_percentage'] ?? 0;
        $responseTime = $this->performanceMetrics['average_response_time'] ?? 0;
        
        return [
            'uptime_trend' => $uptime >= 99 ? 'excellent' : ($uptime >= 95 ? 'good' : 'poor'),
            'response_time_trend' => $responseTime <= 100 ? 'excellent' : ($responseTime <= 300 ? 'good' : 'poor'),
            'overall_performance' => $this->calculateOverallPerformanceScore(),
        ];
    }

    protected function calculateMaintenancePatterns()
    {
        $maintenanceCctvs = Cctv::where('status', 'maintenance')->get();
        
        $buildingDistribution = $maintenanceCctvs->groupBy('building.name')->map->count();
        $typeDistribution = $maintenanceCctvs->groupBy('maintenance_reason')->map->count();
        
        return [
            'building_distribution' => $buildingDistribution,
            'type_distribution' => $typeDistribution,
            'total_maintenance' => $maintenanceCctvs->count(),
        ];
    }

    protected function calculateGeographicDistribution()
    {
        $cctvs = Cctv::with('building')->get();
        
        $geographicDistribution = $cctvs->groupBy('building.name')->map(function ($buildingCctvs) {
            return [
                'total' => $buildingCctvs->count(),
                'online' => $buildingCctvs->where('status', 'online')->count(),
                'offline' => $buildingCctvs->where('status', 'offline')->count(),
                'maintenance' => $buildingCctvs->where('status', 'maintenance')->count(),
            ];
        });
        
        return $geographicDistribution;
    }

    protected function predictFailures()
    {
        // Simple prediction based on current anomalies and performance
        $criticalAnomalies = $this->anomalyStatistics['by_severity']['critical'] ?? 0;
        $highAnomalies = $this->anomalyStatistics['by_severity']['high'] ?? 0;
        
        $riskScore = ($criticalAnomalies * 3) + ($highAnomalies * 2);
        
        if ($riskScore > 20) {
            return 'High risk of system failures. Immediate intervention required.';
        } elseif ($riskScore > 10) {
            return 'Moderate risk of system failures. Monitor closely.';
        } else {
            return 'Low risk of system failures. Continue monitoring.';
        }
    }

    protected function predictMaintenanceSchedule()
    {
        $maintenanceCctvs = Cctv::where('status', 'maintenance')->count();
        $totalCctvs = Cctv::count();
        
        if ($maintenanceCctvs > $totalCctvs * 0.1) {
            return 'High maintenance load. Consider staggered maintenance schedule.';
        } else {
            return 'Normal maintenance load. Current schedule is adequate.';
        }
    }

    protected function predictCapacityNeeds()
    {
        $onlineCctvs = Cctv::where('status', 'online')->count();
        $totalCctvs = Cctv::count();
        $utilizationRate = $totalCctvs > 0 ? ($onlineCctvs / $totalCctvs) * 100 : 0;
        
        if ($utilizationRate < 80) {
            return 'System has capacity for expansion. Consider adding more CCTVs.';
        } elseif ($utilizationRate > 95) {
            return 'System approaching capacity. Plan for infrastructure expansion.';
        } else {
            return 'System operating at optimal capacity.';
        }
    }

    protected function assessRiskLevels()
    {
        $criticalAnomalies = $this->anomalyStatistics['by_severity']['critical'] ?? 0;
        $offlineCctvs = Cctv::where('status', 'offline')->count();
        $maintenanceCctvs = Cctv::where('status', 'maintenance')->count();
        
        $riskFactors = [
            'critical_anomalies' => $criticalAnomalies > 5 ? 'high' : 'low',
            'offline_cctvs' => $offlineCctvs > 50 ? 'high' : 'low',
            'maintenance_load' => $maintenanceCctvs > 100 ? 'high' : 'low',
        ];
        
        $highRiskFactors = count(array_filter($riskFactors, fn($risk) => $risk === 'high'));
        
        if ($highRiskFactors >= 2) {
            return 'High overall risk level. Immediate attention required.';
        } elseif ($highRiskFactors >= 1) {
            return 'Moderate overall risk level. Monitor closely.';
        } else {
            return 'Low overall risk level. System is stable.';
        }
    }

    protected function calculateOverallPerformanceScore()
    {
        $uptime = $this->performanceMetrics['uptime_percentage'] ?? 0;
        $responseTime = $this->performanceMetrics['average_response_time'] ?? 0;
        
        // Normalize scores (0-100)
        $uptimeScore = min(100, $uptime);
        $responseTimeScore = max(0, 100 - ($responseTime / 10)); // Lower response time = higher score
        
        return round(($uptimeScore + $responseTimeScore) / 2, 1);
    }

    public function refreshAnalytics()
    {
        $this->loadAnalytics();
        $this->dispatch('analytics-refreshed');
    }

    public function runAnomalyDetection()
    {
        try {
            $anomalies = $this->aiService->detectAnomalies();
            $this->loadAnalytics(); // Reload data
            
            $this->dispatch('anomaly-detection-completed', [
                'count' => count($anomalies),
                'message' => 'Anomaly detection completed successfully!'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('anomaly-detection-failed', [
                'message' => 'Anomaly detection failed: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.ai-analytics-dashboard');
    }
}
