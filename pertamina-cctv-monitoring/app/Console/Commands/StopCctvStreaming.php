<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cctv;

class StopCctvStreaming extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cctv:stop {--cctv_id= : Specific CCTV ID to stop} {--all : Stop all streaming CCTVs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Stop CCTV streaming processes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Stopping CCTV Streaming Service...');

        if ($this->option('all')) {
            $this->stopAllStreams();
        } elseif ($this->option('cctv_id')) {
            $this->stopSingleStream($this->option('cctv_id'));
        } else {
            $this->error('Please specify either --all or --cctv_id option');
            return 1;
        }

        return 0;
    }

    protected function stopAllStreams()
    {
        $streamingDir = storage_path('app/streaming');
        
        if (!file_exists($streamingDir)) {
            $this->info('No streaming processes found.');
            return;
        }

        $pidFiles = glob($streamingDir . '/*.pid');
        $this->info("Found " . count($pidFiles) . " streaming processes to stop");

        foreach ($pidFiles as $pidFile) {
            $this->stopStreamByPidFile($pidFile);
        }

        $this->info('All streaming processes stopped successfully!');
    }

    protected function stopSingleStream($cctvId)
    {
        $pidFile = storage_path("app/streaming/cctv_{$cctvId}.pid");
        
        if (!file_exists($pidFile)) {
            $this->warn("No streaming process found for CCTV ID: {$cctvId}");
            return;
        }

        $this->stopStreamByPidFile($pidFile);
        $this->info("Streaming stopped for CCTV ID: {$cctvId}");
    }

    protected function stopStreamByPidFile($pidFile)
    {
        $pid = trim(file_get_contents($pidFile));
        
        if ($pid && is_numeric($pid)) {
            // Kill the process
            exec("kill {$pid} 2>/dev/null", $output, $returnCode);
            
            if ($returnCode === 0) {
                $this->line("✅ Stopped process PID: {$pid}");
            } else {
                $this->warn("⚠️  Process PID {$pid} may have already stopped");
            }

            // Extract CCTV ID from filename and update database
            if (preg_match('/cctv_(\d+)\.pid/', basename($pidFile), $matches)) {
                $cctvId = $matches[1];
                $cctv = Cctv::find($cctvId);
                if ($cctv) {
                    $cctv->update(['stream_url' => null]);
                }
            }
        }

        // Remove PID file
        unlink($pidFile);
    }
}
