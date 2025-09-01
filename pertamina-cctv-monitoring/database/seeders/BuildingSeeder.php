<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Building;

class BuildingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $buildings = [
            [
                'name' => 'Gedung Kolaboratif',
                'description' => 'Gedung utama untuk kolaborasi dan meeting',
                'latitude' => -6.2088,
                'longitude' => 106.8456,
                'address' => 'Area Kilang Pertamina RU VI Balongan',
                'is_active' => true,
            ],
            [
                'name' => 'Gerbang Utama',
                'description' => 'Gerbang masuk utama kilang',
                'latitude' => -6.2090,
                'longitude' => 106.8458,
                'address' => 'Gerbang Utama Kilang Pertamina RU VI Balongan',
                'is_active' => true,
            ],
            [
                'name' => 'AWI',
                'description' => 'Area Workshop dan Instrumentasi',
                'latitude' => -6.2092,
                'longitude' => 106.8460,
                'address' => 'Area AWI Kilang Pertamina RU VI Balongan',
                'is_active' => true,
            ],
            [
                'name' => 'Shelter Maintenance Area 1',
                'description' => 'Shelter maintenance untuk area 1',
                'latitude' => -6.2094,
                'longitude' => 106.8462,
                'address' => 'Shelter Maintenance Area 1 Kilang Pertamina RU VI Balongan',
                'is_active' => true,
            ],
            [
                'name' => 'Shelter Maintenance Area 2',
                'description' => 'Shelter maintenance untuk area 2',
                'latitude' => -6.2096,
                'longitude' => 106.8464,
                'address' => 'Shelter Maintenance Area 2 Kilang Pertamina RU VI Balongan',
                'is_active' => true,
            ],
            [
                'name' => 'Shelter Maintenance Area 3',
                'description' => 'Shelter maintenance untuk area 3',
                'latitude' => -6.2098,
                'longitude' => 106.8466,
                'address' => 'Shelter Maintenance Area 3 Kilang Pertamina RU VI Balongan',
                'is_active' => true,
            ],
            [
                'name' => 'Shelter Maintenance Area 4',
                'description' => 'Shelter maintenance untuk area 4',
                'latitude' => -6.2100,
                'longitude' => 106.8468,
                'address' => 'Shelter Maintenance Area 4 Kilang Pertamina RU VI Balongan',
                'is_active' => true,
            ],
            [
                'name' => 'Shelter White OM',
                'description' => 'Shelter White Operation & Maintenance',
                'latitude' => -6.2102,
                'longitude' => 106.8470,
                'address' => 'Shelter White OM Kilang Pertamina RU VI Balongan',
                'is_active' => true,
            ],
            [
                'name' => 'Pintu Masuk Area Kilang Pertamina',
                'description' => 'Pintu masuk area kilang',
                'latitude' => -6.2104,
                'longitude' => 106.8472,
                'address' => 'Pintu Masuk Area Kilang Pertamina RU VI Balongan',
                'is_active' => true,
            ],
            [
                'name' => 'Marine Region III Pertamina Balongan',
                'description' => 'Area Marine Region III',
                'latitude' => -6.2106,
                'longitude' => 106.8474,
                'address' => 'Marine Region III Pertamina Balongan',
                'is_active' => true,
            ],
            [
                'name' => 'Main Control Room',
                'description' => 'Ruangan kontrol utama kilang',
                'latitude' => -6.2108,
                'longitude' => 106.8476,
                'address' => 'Main Control Room Kilang Pertamina RU VI Balongan',
                'is_active' => true,
            ],
            [
                'name' => 'Tank Farm Area 1',
                'description' => 'Area tank farm 1',
                'latitude' => -6.2110,
                'longitude' => 106.8478,
                'address' => 'Tank Farm Area 1 Kilang Pertamina RU VI Balongan',
                'is_active' => true,
            ],
            [
                'name' => 'Gedung EXOR',
                'description' => 'Gedung EXOR',
                'latitude' => -6.2112,
                'longitude' => 106.8480,
                'address' => 'Gedung EXOR Kilang Pertamina RU VI Balongan',
                'is_active' => true,
            ],
            [
                'name' => 'Area Produksi Crude Distillation Unit (CDU)',
                'description' => 'Area produksi CDU',
                'latitude' => -6.2114,
                'longitude' => 106.8482,
                'address' => 'Area Produksi CDU Kilang Pertamina RU VI Balongan',
                'is_active' => true,
            ],
            [
                'name' => 'HSSE Demo Room',
                'description' => 'Ruangan demo HSSE',
                'latitude' => -6.2116,
                'longitude' => 106.8484,
                'address' => 'HSSE Demo Room Kilang Pertamina RU VI Balongan',
                'is_active' => true,
            ],
            [
                'name' => 'Gedung Amanah',
                'description' => 'Gedung Amanah',
                'latitude' => -6.2118,
                'longitude' => 106.8486,
                'address' => 'Gedung Amanah Kilang Pertamina RU VI Balongan',
                'is_active' => true,
            ],
            [
                'name' => 'POC',
                'description' => 'Point of Control',
                'latitude' => -6.2120,
                'longitude' => 106.8488,
                'address' => 'POC Kilang Pertamina RU VI Balongan',
                'is_active' => true,
            ],
            [
                'name' => 'JGC',
                'description' => 'Gedung JGC',
                'latitude' => -6.2122,
                'longitude' => 106.8490,
                'address' => 'JGC Kilang Pertamina RU VI Balongan',
                'is_active' => true,
            ],
        ];

        foreach ($buildings as $building) {
            Building::create($building);
        }
    }
}
