<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AiAnomalyDetectionService;
use Illuminate\Support\Facades\Log;

class AiAnomalyDetection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:detect-anomalies {--continuous : Run continuously with interval} {--interval=5 : Interval in minutes for continuous mode}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run AI-powered anomaly detection for CCTV system';

    protected $aiService;

    public function __construct(AiAnomalyDetectionService $aiService)
    {
        parent::__construct();
        $this->aiService = $aiService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('continuous')) {
            $this->runContinuousDetection();
        } else {
            $this->runSingleDetection();
        }
    }

    /**
     * Run single anomaly detection
     */
    protected function runSingleDetection()
    {
        $this->info('🤖 Starting AI Anomaly Detection...');
        $this->info('Analyzing CCTV system for anomalies...');

        try {
            $startTime = microtime(true);
            $anomalies = $this->aiService->detectAnomalies();
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            $this->info("✅ Anomaly detection completed in {$executionTime}ms");
            $this->info("📊 Found " . count($anomalies) . " anomalies");

            if (count($anomalies) > 0) {
                $this->displayAnomalies($anomalies);
            } else {
                $this->info('🎉 No anomalies detected. System is healthy!');
            }

            // Display statistics
            $this->displayStatistics();

        } catch (\Exception $e) {
            $this->error('❌ Anomaly detection failed: ' . $e->getMessage());
            Log::error('AI anomaly detection failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Run continuous anomaly detection
     */
    protected function runContinuousDetection()
    {
        $interval = (int) $this->option('interval');
        $this->info("🤖 Starting Continuous AI Anomaly Detection with {$interval} minute interval...");
        $this->info("Press Ctrl+C to stop continuous detection");

        while (true) {
            try {
                $this->info("\n[" . now()->format('Y-m-d H:i:s') . "] Running AI anomaly detection...");
                
                $startTime = microtime(true);
                $anomalies = $this->aiService->detectAnomalies();
                $executionTime = round((microtime(true) - $startTime) * 1000, 2);

                $this->info("✅ Detection completed in {$executionTime}ms - Found " . count($anomalies) . " anomalies");

                if (count($anomalies) > 0) {
                    $this->displayAnomalies($anomalies, false);
                }

                $this->info("⏰ Next detection in {$interval} minutes...");
                
                // Sleep for specified interval
                sleep($interval * 60);
                
            } catch (\Exception $e) {
                $this->error("❌ Detection failed: " . $e->getMessage());
                Log::error('Continuous AI anomaly detection failed: ' . $e->getMessage());
                
                // Wait 1 minute before retrying
                $this->info("🔄 Retrying in 1 minute...");
                sleep(60);
            }
        }
    }

    /**
     * Display detected anomalies
     */
    protected function displayAnomalies($anomalies, $detailed = true)
    {
        $this->newLine();
        $this->warn("🚨 Detected Anomalies:");

        foreach ($anomalies as $index => $anomaly) {
            $severityColor = $this->getSeverityColor($anomaly['severity']);
            $severityIcon = $this->getSeverityIcon($anomaly['severity']);

            $this->line("  {$severityIcon} {$anomaly['type']} - {$anomaly['severity']} severity");
            $this->line("     Description: {$anomaly['description']}");

            if ($detailed && isset($anomaly['data'])) {
                foreach ($anomaly['data'] as $key => $value) {
                    $this->line("     {$key}: {$value}");
                }
            }

            if ($index < count($anomalies) - 1) {
                $this->newLine();
            }
        }
    }

    /**
     * Display anomaly statistics
     */
    protected function displayStatistics()
    {
        try {
            $statistics = $this->aiService->getAnomalyStatistics(30);
            
            $this->newLine();
            $this->info("📈 Anomaly Statistics (Last 30 days):");
            $this->line("  Total Anomalies: {$statistics['total']}");
            
            if (isset($statistics['by_severity'])) {
                foreach ($statistics['by_severity'] as $severity => $count) {
                    $severityIcon = $this->getSeverityIcon($severity);
                    $this->line("  {$severityIcon} {$severity}: {$count}");
                }
            }
            
            $this->line("  Trend: " . ucfirst($statistics['trend'] ?? 'stable'));
            
        } catch (\Exception $e) {
            $this->warn("⚠️  Could not retrieve statistics: " . $e->getMessage());
        }
    }

    /**
     * Get severity color for output
     */
    protected function getSeverityColor($severity)
    {
        return match($severity) {
            'critical' => 'red',
            'high' => 'yellow',
            'medium' => 'blue',
            'low' => 'green',
            default => 'white',
        };
    }

    /**
     * Get severity icon for output
     */
    protected function getSeverityIcon($severity)
    {
        return match($severity) {
            'critical' => '🚨',
            'high' => '⚠️',
            'medium' => '🔶',
            'low' => 'ℹ️',
            default => '❓',
        };
    }
}
