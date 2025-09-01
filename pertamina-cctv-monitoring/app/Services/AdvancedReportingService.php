<?php

namespace App\Services;

use App\Models\Report;
use App\Models\Cctv;
use App\Models\Building;
use App\Models\Room;
use App\Models\CctvAnomaly;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AdvancedReportingService
{
    protected $aiService;
    protected $monitoringService;

    public function __construct(
        AiAnomalyDetectionService $aiService,
        CctvMonitoringService $monitoringService
    ) {
        $this->aiService = $aiService;
        $this->monitoringService = $monitoringService;
    }

    /**
     * Generate comprehensive system report
     */
    public function generateSystemReport(array $filters = []): array
    {
        try {
            $report = [
                'generated_at' => now()->toISOString(),
                'period' => $this->getReportPeriod($filters),
                'summary' => $this->getSystemSummary(),
                'performance_metrics' => $this->getPerformanceMetrics($filters),
                'anomaly_analysis' => $this->getAnomalyAnalysis($filters),
                'geographic_distribution' => $this->getGeographicDistribution(),
                'maintenance_insights' => $this->getMaintenanceInsights($filters),
                'security_assessment' => $this->getSecurityAssessment($filters),
                'trends' => $this->getTrendAnalysis($filters),
                'recommendations' => $this->generateRecommendations(),
                'export_data' => $this->prepareExportData($filters),
            ];

            // Cache the report for performance
            $cacheKey = 'system_report_' . md5(serialize($filters));
            Cache::put($cacheKey, $report, 3600); // Cache for 1 hour

            return $report;
        } catch (\Exception $e) {
            Log::error('System report generation failed: ' . $e->getMessage());
            return $this->getErrorReport($e->getMessage());
        }
    }

    /**
     * Generate custom dashboard report
     */
    public function generateCustomDashboard(Report $report): array
    {
        try {
            $configuration = $report->configuration;
            $dataSource = $report->data_source;
            $filters = $report->filters ?? [];

            $dashboardData = [];

            foreach ($configuration['widgets'] ?? [] as $widget) {
                $widgetData = $this->generateWidgetData($widget, $dataSource, $filters);
                $dashboardData[$widget['id']] = $widgetData;
            }

            // Update report last generated timestamp
            $report->update([
                'last_generated_at' => now(),
                'next_generation_at' => $this->calculateNextGenerationTime($report),
            ]);

            return [
                'report_id' => $report->id,
                'generated_at' => now()->toISOString(),
                'dashboard_data' => $dashboardData,
                'configuration' => $configuration,
                'filters' => $filters,
            ];

        } catch (\Exception $e) {
            Log::error('Custom dashboard generation failed: ' . $e->getMessage());
            return $this->getErrorReport($e->getMessage());
        }
    }

    /**
     * Generate widget data based on configuration
     */
    protected function generateWidgetData(array $widget, array $dataSource, array $filters): array
    {
        $type = $widget['type'];
        $dataType = $widget['data_type'] ?? 'realtime';

        return match($type) {
            'metric_card' => $this->generateMetricCard($widget, $dataSource, $filters),
            'line_chart' => $this->generateLineChart($widget, $dataSource, $filters),
            'bar_chart' => $this->generateBarChart($widget, $dataSource, $filters),
            'pie_chart' => $this->generatePieChart($widget, $dataSource, $filters),
            'data_table' => $this->generateDataTable($widget, $dataSource, $filters),
            'gauge' => $this->generateGauge($widget, $dataSource, $filters),
            'heatmap' => $this->generateHeatmap($widget, $dataSource, $filters),
            'status_grid' => $this->generateStatusGrid($widget, $dataSource, $filters),
            default => $this->generateDefaultWidget($widget, $dataSource, $filters),
        };
    }

    /**
     * Generate metric card widget
     */
    protected function generateMetricCard(array $widget, array $dataSource, array $filters): array
    {
        $metric = $widget['metric'] ?? 'total_cctvs';
        $value = $this->getMetricValue($metric, $filters);
        $previousValue = $this->getPreviousMetricValue($metric, $filters);
        $change = $this->calculateChange($value, $previousValue);

        return [
            'type' => 'metric_card',
            'title' => $widget['title'] ?? ucfirst(str_replace('_', ' ', $metric)),
            'value' => $value,
            'previous_value' => $previousValue,
            'change' => $change,
            'change_percentage' => $this->calculateChangePercentage($value, $previousValue),
            'trend' => $change > 0 ? 'up' : ($change < 0 ? 'down' : 'stable'),
            'icon' => $widget['icon'] ?? $this->getMetricIcon($metric),
            'color' => $widget['color'] ?? $this->getMetricColor($metric),
            'format' => $widget['format'] ?? 'number',
        ];
    }

    /**
     * Generate line chart widget
     */
    protected function generateLineChart(array $widget, array $dataSource, array $filters): array
    {
        $metric = $widget['metric'] ?? 'cctv_status';
        $period = $widget['period'] ?? '7d';
        $data = $this->getTimeSeriesData($metric, $period, $filters);

        return [
            'type' => 'line_chart',
            'title' => $widget['title'] ?? 'Time Series Chart',
            'data' => $data,
            'x_axis' => 'time',
            'y_axis' => $metric,
            'period' => $period,
            'options' => $widget['chart_options'] ?? [],
        ];
    }

    /**
     * Generate bar chart widget
     */
    protected function generateBarChart(array $widget, array $dataSource, array $filters): array
    {
        $metric = $widget['metric'] ?? 'building_cctv_count';
        $groupBy = $widget['group_by'] ?? 'building';
        $data = $this->getGroupedData($metric, $groupBy, $filters);

        return [
            'type' => 'bar_chart',
            'title' => $widget['title'] ?? 'Bar Chart',
            'data' => $data,
            'x_axis' => $groupBy,
            'y_axis' => $metric,
            'options' => $widget['chart_options'] ?? [],
        ];
    }

    /**
     * Generate pie chart widget
     */
    protected function generatePieChart(array $widget, array $dataSource, array $filters): array
    {
        $metric = $widget['metric'] ?? 'cctv_status_distribution';
        $data = $this->getDistributionData($metric, $filters);

        return [
            'type' => 'pie_chart',
            'title' => $widget['title'] ?? 'Distribution Chart',
            'data' => $data,
            'options' => $widget['chart_options'] ?? [],
        ];
    }

    /**
     * Generate data table widget
     */
    protected function generateDataTable(array $widget, array $dataSource, array $filters): array
    {
        $table = $widget['table'] ?? 'cctvs';
        $columns = $widget['columns'] ?? ['name', 'status', 'building', 'room'];
        $limit = $widget['limit'] ?? 10;
        $data = $this->getTableData($table, $columns, $filters, $limit);

        return [
            'type' => 'data_table',
            'title' => $widget['title'] ?? 'Data Table',
            'columns' => $columns,
            'data' => $data,
            'total_records' => $this->getTableTotalCount($table, $filters),
            'pagination' => [
                'current_page' => 1,
                'per_page' => $limit,
                'total_pages' => ceil($this->getTableTotalCount($table, $filters) / $limit),
            ],
        ];
    }

    /**
     * Generate gauge widget
     */
    protected function generateGauge(array $widget, array $dataSource, array $filters): array
    {
        $metric = $widget['metric'] ?? 'system_health';
        $value = $this->getMetricValue($metric, $filters);
        $min = $widget['min'] ?? 0;
        $max = $widget['max'] ?? 100;
        $thresholds = $widget['thresholds'] ?? [
            'low' => 30,
            'medium' => 70,
            'high' => 100,
        ];

        return [
            'type' => 'gauge',
            'title' => $widget['title'] ?? 'Gauge Chart',
            'value' => $value,
            'min' => $min,
            'max' => $max,
            'thresholds' => $thresholds,
            'color' => $this->getGaugeColor($value, $thresholds),
            'percentage' => round(($value / $max) * 100, 1),
        ];
    }

    /**
     * Generate heatmap widget
     */
    protected function generateHeatmap(array $widget, array $dataSource, array $filters): array
    {
        $metric = $widget['metric'] ?? 'cctv_density';
        $data = $this->getHeatmapData($metric, $filters);

        return [
            'type' => 'heatmap',
            'title' => $widget['title'] ?? 'Heatmap',
            'data' => $data,
            'x_axis' => $widget['x_axis'] ?? 'buildings',
            'y_axis' => $widget['y_axis'] ?? 'rooms',
            'color_scale' => $widget['color_scale'] ?? 'viridis',
        ];
    }

    /**
     * Generate status grid widget
     */
    protected function generateStatusGrid(array $widget, array $dataSource, array $filters): array
    {
        $groupBy = $widget['group_by'] ?? 'building';
        $data = $this->getStatusGridData($groupBy, $filters);

        return [
            'type' => 'status_grid',
            'title' => $widget['title'] ?? 'Status Grid',
            'data' => $data,
            'group_by' => $groupBy,
            'statuses' => ['online', 'offline', 'maintenance'],
            'options' => $widget['grid_options'] ?? [],
        ];
    }

    /**
     * Get metric value
     */
    protected function getMetricValue(string $metric, array $filters): mixed
    {
        return match($metric) {
            'total_cctvs' => Cctv::filter($filters)->count(),
            'online_cctvs' => Cctv::filter($filters)->where('status', 'online')->count(),
            'offline_cctvs' => Cctv::filter($filters)->where('status', 'offline')->count(),
            'maintenance_cctvs' => Cctv::filter($filters)->where('status', 'maintenance')->count(),
            'total_buildings' => Building::filter($filters)->count(),
            'total_rooms' => Room::filter($filters)->count(),
            'total_anomalies' => CctvAnomaly::filter($filters)->count(),
            'critical_anomalies' => CctvAnomaly::filter($filters)->where('severity', 'critical')->count(),
            'system_uptime' => $this->calculateSystemUptime($filters),
            'average_response_time' => $this->calculateAverageResponseTime($filters),
            'system_health_score' => $this->calculateSystemHealthScore($filters),
            default => 0,
        };
    }

    /**
     * Get previous metric value for comparison
     */
    protected function getPreviousMetricValue(string $metric, array $filters): mixed
    {
        // For now, return 0. In a real implementation, you'd compare with previous period
        return 0;
    }

    /**
     * Calculate change between current and previous values
     */
    protected function calculateChange($current, $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return $current - $previous;
    }

    /**
     * Calculate change percentage
     */
    protected function calculateChangePercentage($current, $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return round((($current - $previous) / $previous) * 100, 1);
    }

    /**
     * Get metric icon
     */
    protected function getMetricIcon(string $metric): string
    {
        return match($metric) {
            'total_cctvs' => 'video-camera',
            'online_cctvs' => 'check-circle',
            'offline_cctvs' => 'x-circle',
            'maintenance_cctvs' => 'wrench',
            'total_buildings' => 'building',
            'total_rooms' => 'home',
            'total_anomalies' => 'exclamation-triangle',
            'critical_anomalies' => 'exclamation-circle',
            'system_uptime' => 'clock',
            'average_response_time' => 'speedometer',
            'system_health_score' => 'heart',
            default => 'chart-bar',
        };
    }

    /**
     * Get metric color
     */
    protected function getMetricColor(string $metric): string
    {
        return match($metric) {
            'total_cctvs' => '#3B82F6',
            'online_cctvs' => '#10B981',
            'offline_cctvs' => '#EF4444',
            'maintenance_cctvs' => '#F59E0B',
            'total_buildings' => '#8B5CF6',
            'total_rooms' => '#06B6D4',
            'total_anomalies' => '#F97316',
            'critical_anomalies' => '#DC2626',
            'system_uptime' => '#059669',
            'average_response_time' => '#7C3AED',
            'system_health_score' => '#EC4899',
            default => '#6B7280',
        };
    }

    /**
     * Get time series data
     */
    protected function getTimeSeriesData(string $metric, string $period, array $filters): array
    {
        $days = match($period) {
            '1d' => 1,
            '7d' => 7,
            '30d' => 30,
            '90d' => 90,
            default => 7,
        };

        $data = [];
        $startDate = now()->subDays($days);

        for ($i = 0; $i <= $days; $i++) {
            $date = $startDate->copy()->addDays($i);
            $value = $this->getMetricValueForDate($metric, $date, $filters);
            
            $data[] = [
                'date' => $date->format('Y-m-d'),
                'value' => $value,
                'timestamp' => $date->timestamp,
            ];
        }

        return $data;
    }

    /**
     * Get grouped data
     */
    protected function getGroupedData(string $metric, string $groupBy, array $filters): array
    {
        return match($groupBy) {
            'building' => $this->getBuildingGroupedData($metric, $filters),
            'room' => $this->getRoomGroupedData($metric, $filters),
            'status' => $this->getStatusGroupedData($metric, $filters),
            'severity' => $this->getSeverityGroupedData($metric, $filters),
            default => [],
        };
    }

    /**
     * Get distribution data
     */
    protected function getDistributionData(string $metric, array $filters): array
    {
        return match($metric) {
            'cctv_status_distribution' => $this->getCctvStatusDistribution($filters),
            'anomaly_severity_distribution' => $this->getAnomalySeverityDistribution($filters),
            'building_cctv_distribution' => $this->getBuildingCctvDistribution($filters),
            default => [],
        };
    }

    /**
     * Get table data
     */
    protected function getTableData(string $table, array $columns, array $filters, int $limit): array
    {
        return match($table) {
            'cctvs' => $this->getCctvTableData($columns, $filters, $limit),
            'anomalies' => $this->getAnomalyTableData($columns, $filters, $limit),
            'buildings' => $this->getBuildingTableData($columns, $filters, $limit),
            default => [],
        };
    }

    /**
     * Get system summary
     */
    protected function getSystemSummary(): array
    {
        return [
            'total_cctvs' => Cctv::count(),
            'online_cctvs' => Cctv::where('status', 'online')->count(),
            'offline_cctvs' => Cctv::where('status', 'offline')->count(),
            'maintenance_cctvs' => Cctv::where('status', 'maintenance')->count(),
            'total_buildings' => Building::count(),
            'total_rooms' => Room::count(),
            'total_anomalies' => CctvAnomaly::count(),
            'system_health_score' => $this->calculateSystemHealthScore(),
        ];
    }

    /**
     * Get performance metrics
     */
    protected function getPerformanceMetrics(array $filters): array
    {
        return [
            'uptime_percentage' => $this->calculateSystemUptime($filters),
            'average_response_time' => $this->calculateAverageResponseTime($filters),
            'throughput' => $this->calculateThroughput($filters),
            'error_rate' => $this->calculateErrorRate($filters),
            'availability' => $this->calculateAvailability($filters),
        ];
    }

    /**
     * Get anomaly analysis
     */
    protected function getAnomalyAnalysis(array $filters): array
    {
        $anomalies = CctvAnomaly::filter($filters)->get();
        
        return [
            'total_anomalies' => $anomalies->count(),
            'by_severity' => $anomalies->groupBy('severity')->map->count(),
            'by_type' => $anomalies->groupBy('type')->map->count(),
            'trend' => $this->aiService->getAnomalyStatistics(30)['trend'] ?? 'stable',
            'recent_anomalies' => $anomalies->take(5)->map(function ($anomaly) {
                return [
                    'id' => $anomaly->id,
                    'type' => $anomaly->type,
                    'severity' => $anomaly->severity,
                    'description' => $anomaly->description,
                    'detected_at' => $anomaly->detected_at->toISOString(),
                ];
            }),
        ];
    }

    /**
     * Get geographic distribution
     */
    protected function getGeographicDistribution(): array
    {
        $buildings = Building::with('cctvs')->get();
        
        return $buildings->map(function ($building) {
            return [
                'building' => $building->name,
                'total_cctvs' => $building->cctvs->count(),
                'online_cctvs' => $building->cctvs->where('status', 'online')->count(),
                'offline_cctvs' => $building->cctvs->where('status', 'offline')->count(),
                'maintenance_cctvs' => $building->cctvs->where('status', 'maintenance')->count(),
                'health_score' => $this->calculateBuildingHealthScore($building),
            ];
        });
    }

    /**
     * Get maintenance insights
     */
    protected function getMaintenanceInsights(array $filters): array
    {
        $maintenanceCctvs = Cctv::where('status', 'maintenance')->get();
        
        return [
            'total_maintenance' => $maintenanceCctvs->count(),
            'by_building' => $maintenanceCctvs->groupBy('building.name')->map->count(),
            'by_reason' => $maintenanceCctvs->groupBy('maintenance_reason')->map->count(),
            'estimated_completion' => $this->estimateMaintenanceCompletion($maintenanceCctvs),
            'cost_impact' => $this->calculateMaintenanceCostImpact($maintenanceCctvs),
        ];
    }

    /**
     * Get security assessment
     */
    protected function getSecurityAssessment(array $filters): array
    {
        $offlineCctvs = Cctv::where('status', 'offline')->get();
        $totalCctvs = Cctv::count();
        
        return [
            'offline_percentage' => $totalCctvs > 0 ? round(($offlineCctvs->count() / $totalCctvs) * 100, 1) : 0,
            'security_score' => $this->calculateSecurityScore($offlineCctvs, $totalCctvs),
            'risk_level' => $this->assessSecurityRisk($offlineCctvs, $totalCctvs),
            'recommendations' => $this->generateSecurityRecommendations($offlineCctvs, $totalCctvs),
        ];
    }

    /**
     * Get trend analysis
     */
    protected function getTrendAnalysis(array $filters): array
    {
        return [
            'cctv_growth' => $this->calculateCctvGrowthTrend(),
            'anomaly_trend' => $this->calculateAnomalyTrend(),
            'performance_trend' => $this->calculatePerformanceTrend(),
            'maintenance_trend' => $this->calculateMaintenanceTrend(),
        ];
    }

    /**
     * Generate recommendations
     */
    protected function generateRecommendations(): array
    {
        $recommendations = [];
        
        // Performance recommendations
        $uptime = $this->calculateSystemUptime();
        if ($uptime < 95) {
            $recommendations[] = [
                'category' => 'performance',
                'priority' => 'high',
                'title' => 'Improve System Uptime',
                'description' => 'Current uptime is below 95%. Consider infrastructure upgrades and preventive maintenance.',
                'action' => 'Schedule system health check and infrastructure review.',
            ];
        }
        
        // Security recommendations
        $offlinePercentage = (Cctv::where('status', 'offline')->count() / Cctv::count()) * 100;
        if ($offlinePercentage > 10) {
            $recommendations[] = [
                'category' => 'security',
                'priority' => 'critical',
                'title' => 'Address Offline CCTVs',
                'description' => 'High number of offline CCTVs detected. Immediate security review required.',
                'action' => 'Investigate offline CCTVs and implement security measures.',
            ];
        }
        
        // Maintenance recommendations
        $maintenanceCount = Cctv::where('status', 'maintenance')->count();
        if ($maintenanceCount > 50) {
            $recommendations[] = [
                'category' => 'maintenance',
                'priority' => 'medium',
                'title' => 'Optimize Maintenance Schedule',
                'description' => 'High maintenance load detected. Consider staggered maintenance approach.',
                'action' => 'Review maintenance procedures and resource allocation.',
            ];
        }
        
        return $recommendations;
    }

    /**
     * Prepare export data
     */
    protected function prepareExportData(array $filters): array
    {
        return [
            'cctv_data' => $this->getCctvExportData($filters),
            'anomaly_data' => $this->getAnomalyExportData($filters),
            'performance_data' => $this->getPerformanceExportData($filters),
            'maintenance_data' => $this->getMaintenanceExportData($filters),
        ];
    }

    /**
     * Get report period
     */
    protected function getReportPeriod(array $filters): array
    {
        $startDate = $filters['start_date'] ?? now()->subDays(30)->format('Y-m-d');
        $endDate = $filters['end_date'] ?? now()->format('Y-m-d');
        
        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'duration_days' => Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)),
        ];
    }

    /**
     * Calculate next generation time for scheduled reports
     */
    protected function calculateNextGenerationTime(Report $report): ?Carbon
    {
        if (!$report->is_scheduled || !$report->schedule_cron) {
            return null;
        }

        // Simple cron parsing for common patterns
        $cron = $report->schedule_cron;
        
        if ($cron === '5min') {
            return now()->addMinutes(5);
        } elseif ($cron === '15min') {
            return now()->addMinutes(15);
        } elseif ($cron === '1hour') {
            return now()->addHour();
        } elseif ($cron === '1day') {
            return now()->addDay();
        }
        
        return null;
    }

    /**
     * Get error report
     */
    protected function getErrorReport(string $error): array
    {
        return [
            'error' => true,
            'message' => $error,
            'generated_at' => now()->toISOString(),
            'data' => [],
        ];
    }

    // Additional helper methods would be implemented here...
    // For brevity, I'm showing the main structure
}