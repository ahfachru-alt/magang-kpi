<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Contact;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contacts = [
            [
                'name' => 'IT Support Pertamina',
                'email' => 'it.support@pertamina.com',
                'phone' => '+62-254-401234',
                'whatsapp' => '+62-812-34567890',
                'address' => 'Kilang Pertamina RU VI Balongan, Indramayu, Jawa Barat',
                'position' => 'IT Support',
                'department' => 'Information Technology',
                'is_active' => true,
            ],
            [
                'name' => 'Security Pertamina',
                'email' => 'security@pertamina.com',
                'phone' => '+62-254-401235',
                'whatsapp' => '+62-812-34567891',
                'address' => 'Kilang Pertamina RU VI Balongan, Indramayu, Jawa Barat',
                'position' => 'Security Officer',
                'department' => 'Security',
                'is_active' => true,
            ],
            [
                'name' => 'Maintenance Pertamina',
                'email' => 'maintenance@pertamina.com',
                'phone' => '+62-254-401236',
                'whatsapp' => '+62-812-34567892',
                'address' => 'Kilang Pertamina RU VI Balongan, Indramayu, Jawa Barat',
                'position' => 'Maintenance Engineer',
                'department' => 'Maintenance',
                'is_active' => true,
            ],
            [
                'name' => 'Emergency Response',
                'email' => 'emergency@pertamina.com',
                'phone' => '+62-254-401237',
                'whatsapp' => '+62-812-34567893',
                'address' => 'Kilang Pertamina RU VI Balongan, Indramayu, Jawa Barat',
                'position' => 'Emergency Response Team',
                'department' => 'HSSE',
                'is_active' => true,
            ],
        ];

        foreach ($contacts as $contact) {
            Contact::create($contact);
        }
    }
}
