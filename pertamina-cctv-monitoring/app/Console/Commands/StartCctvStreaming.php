<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cctv;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class StartCctvStreaming extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cctv:stream {--cctv_id= : Specific CCTV ID to stream} {--all : Stream all online CCTVs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start CCTV streaming using FFmpeg RTSP to HLS conversion';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting CCTV Streaming Service...');

        // Create streaming directory if it doesn't exist
        $streamingPath = public_path('streaming');
        if (!file_exists($streamingPath)) {
            mkdir($streamingPath, 0755, true);
        }

        if ($this->option('all')) {
            $this->streamAllCctvs();
        } elseif ($this->option('cctv_id')) {
            $this->streamSingleCctv($this->option('cctv_id'));
        } else {
            $this->error('Please specify either --all or --cctv_id option');
            return 1;
        }

        return 0;
    }

    protected function streamAllCctvs()
    {
        $cctvs = Cctv::where('status', 'online')->get();
        
        $this->info("Found {$cctvs->count()} online CCTVs to stream");

        foreach ($cctvs as $cctv) {
            $this->streamSingleCctv($cctv->id, false);
        }

        $this->info('All CCTV streams started successfully!');
    }

    protected function streamSingleCctv($cctvId, $showMessages = true)
    {
        $cctv = Cctv::find($cctvId);
        
        if (!$cctv) {
            $this->error("CCTV with ID {$cctvId} not found");
            return;
        }

        if ($cctv->status !== 'online') {
            if ($showMessages) {
                $this->warn("CCTV {$cctv->name} is not online. Status: {$cctv->status}");
            }
            return;
        }

        // Generate RTSP URL from IP address
        $rtspUrl = "rtsp://{$cctv->ip_address}/stream";
        
        // Generate HLS output path
        $outputDir = public_path("streaming/cctv_{$cctv->id}");
        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0755, true);
        }
        
        $outputPath = "{$outputDir}/playlist.m3u8";
        
        // FFmpeg command for RTSP to HLS conversion
        $ffmpegCommand = [
            'ffmpeg',
            '-i', $rtspUrl,
            '-c:v', 'libx264',
            '-c:a', 'aac',
            '-hls_time', '10',
            '-hls_list_size', '3',
            '-hls_wrap', '3',
            '-hls_flags', 'delete_segments',
            '-f', 'hls',
            $outputPath
        ];

        if ($showMessages) {
            $this->info("Starting stream for CCTV: {$cctv->name}");
            $this->line("RTSP URL: {$rtspUrl}");
            $this->line("HLS Output: {$outputPath}");
        }

        try {
            // Start FFmpeg process in background
            $process = Process::start($ffmpegCommand);
            
            // Update CCTV with stream URL
            $cctv->update([
                'stream_url' => url("streaming/cctv_{$cctv->id}/playlist.m3u8"),
                'last_online_at' => now(),
            ]);

            if ($showMessages) {
                $this->info("✅ Stream started for {$cctv->name}");
                $this->line("Stream URL: {$cctv->stream_url}");
            }

            // Store process PID for later management
            $pidFile = storage_path("app/streaming/cctv_{$cctv->id}.pid");
            if (!file_exists(dirname($pidFile))) {
                mkdir(dirname($pidFile), 0755, true);
            }
            file_put_contents($pidFile, $process->id());

        } catch (\Exception $e) {
            if ($showMessages) {
                $this->error("❌ Failed to start stream for {$cctv->name}: " . $e->getMessage());
            }
            
            // Mark CCTV as offline if streaming fails
            $cctv->update(['status' => 'offline']);
        }
    }
}
