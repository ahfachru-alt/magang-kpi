<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cctv;

class StreamCctvCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cctv:stream';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start streaming all CCTV cameras';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting CCTV streaming...');
        
        $cctvs = Cctv::where('is_active', true)->get();
        
        foreach ($cctvs as $cctv) {
            $ip = $cctv->ip_address;
            $filename = str_replace(['.', ':', '@'], '_', parse_url($ip, PHP_URL_HOST));
            $output = public_path('live/' . $filename . '.m3u8');
            
            // Create live directory if not exists
            if (!file_exists(public_path('live'))) {
                mkdir(public_path('live'), 0755, true);
            }
            
            $cmd = "ffmpeg -rtsp_transport tcp -i '{$ip}' -c:v libx264 -preset ultrafast -tune zerolatency -f hls -hls_time 1 -hls_list_size 3 -hls_flags delete_segments '{$output}' > /dev/null 2>&1 &";
            
            exec($cmd);
            
            $this->info("Started streaming for CCTV: {$cctv->name}");
        }
        
        $this->info('CCTV streaming started successfully!');
    }
}
