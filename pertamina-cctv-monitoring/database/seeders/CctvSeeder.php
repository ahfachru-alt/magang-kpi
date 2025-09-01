<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Room;
use App\Models\Cctv;

class CctvSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = Room::all();
        $cctvCounter = 1;

        foreach ($rooms as $room) {
            // Create 3-4 CCTV per room
            $cctvCount = rand(3, 4);
            
            for ($i = 1; $i <= $cctvCount; $i++) {
                $ipNumber = str_pad($cctvCounter, 3, '0', STR_PAD_LEFT);
                $ipAddress = "rtsp://admin:password.123@10.56.236.{$ipNumber}/streaming/channels/";
                
                $statuses = ['online', 'offline', 'maintenance'];
                $status = $statuses[array_rand($statuses)];
                
                Cctv::create([
                    'room_id' => $room->id,
                    'name' => 'CCTV ' . $room->name . ' - ' . $i,
                    'ip_address' => $ipAddress,
                    'stream_url' => $ipAddress,
                    'status' => $status,
                    'description' => 'CCTV untuk monitoring ' . $room->name,
                    'model' => 'IP Camera ' . rand(1000, 9999),
                    'manufacturer' => 'Hikvision',
                    'last_online' => $status === 'online' ? now() : null,
                    'is_active' => true,
                ]);
                
                $cctvCounter++;
                
                // Stop at 700 CCTV
                if ($cctvCounter > 700) {
                    break 2;
                }
            }
        }
    }
}
