<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\Building;
use App\Models\Room;
use App\Models\Cctv;
use App\Models\User;

class Dashboard extends Component
{
    public function render()
    {
        $stats = [
            'buildings' => Building::count(),
            'rooms' => Room::count(),
            'cctvs' => Cctv::count(),
            'cctvs_online' => Cctv::where('status', 'online')->count(),
            'cctvs_offline' => Cctv::where('status', 'offline')->count(),
            'cctvs_maintenance' => Cctv::where('status', 'maintenance')->count(),
            'users' => User::count(),
        ];

        return view('livewire.user.dashboard', compact('stats'))->layout('layouts.app');
    }
}