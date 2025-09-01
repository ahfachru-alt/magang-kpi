<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CctvMonitoringService;
use Illuminate\Support\Facades\Log;

class MonitorCctvStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cctv:monitor {--interval=5 : Monitoring interval in minutes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor CCTV status continuously with specified interval';

    protected $monitoringService;

    public function __construct(CctvMonitoringService $monitoringService)
    {
        parent::__construct();
        $this->monitoringService = $monitoringService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $interval = (int) $this->option('interval');
        $this->info("Starting CCTV monitoring service with {$interval} minute interval...");
        $this->info("Press Ctrl+C to stop monitoring");

        while (true) {
            try {
                $this->info("\n[" . now()->format('Y-m-d H:i:s') . "] Checking CCTV status...");
                
                $statusChanges = $this->monitoringService->checkCctvStatus();
                
                if (count($statusChanges) > 0) {
                    $this->warn("Found " . count($statusChanges) . " status changes:");
                    
                    foreach ($statusChanges as $change) {
                        $cctv = $change['cctv'];
                        $this->line("  • {$cctv->name} ({$cctv->ip_address}): {$change['old_status']} → {$change['new_status']}");
                    }
                } else {
                    $this->info("No status changes detected");
                }

                // Get summary
                $summary = $this->monitoringService->getStatusSummary();
                $this->info("Status Summary: {$summary['online']} online, {$summary['offline']} offline, {$summary['maintenance']} maintenance");

                // Check for critical alerts
                $alerts = $this->monitoringService->getCctvAlerts();
                if (count($alerts) > 0) {
                    $this->error("⚠️  Found " . count($alerts) . " critical alerts:");
                    
                    foreach ($alerts as $alert) {
                        $severityColor = $alert['severity'] === 'critical' ? 'red' : ($alert['severity'] === 'high' ? 'yellow' : 'blue');
                        $this->line("  • {$alert['cctv_name']} - {$alert['building']} / {$alert['room']} - Offline for {$alert['downtime_hours']} hours");
                    }
                }

                $this->info("Next check in {$interval} minutes...");
                
                // Sleep for specified interval
                sleep($interval * 60);
                
            } catch (\Exception $e) {
                $this->error("Error during monitoring: " . $e->getMessage());
                Log::error("CCTV monitoring error: " . $e->getMessage());
                
                // Wait 1 minute before retrying
                sleep(60);
            }
        }
    }
}
