<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Building;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $buildings = Building::all();
        
        $roomTypes = [
            'Control Room',
            'Server Room',
            'Meeting Room',
            'Office Space',
            'Storage Room',
            'Equipment Room',
            'Monitoring Room',
            'Security Room',
            'Utility Room',
            'Workshop',
            'Laboratory',
            'Rest Area'
        ];

        foreach ($buildings as $building) {
            for ($i = 1; $i <= 12; $i++) {
                Room::create([
                    'building_id' => $building->id,
                    'name' => $roomTypes[$i - 1] . ' ' . $i,
                    'description' => $roomTypes[$i - 1] . ' di ' . $building->name,
                    'floor' => rand(1, 3),
                    'room_number' => $building->id . '0' . $i,
                    'is_active' => true,
                ]);
            }
        }
    }
}
