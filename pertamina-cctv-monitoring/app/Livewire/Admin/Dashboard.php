<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Building;
use App\Models\Room;
use App\Models\Cctv;
use App\Models\User;
use App\Models\Admin;

class Dashboard extends Component
{
    public $totalBuildings;
    public $totalRooms;
    public $totalCctvs;
    public $onlineCctvs;
    public $offlineCctvs;
    public $maintenanceCctvs;
    public $totalUsers;
    public $totalAdmins;

    public function mount()
    {
        $this->loadStatistics();
    }

    public function loadStatistics()
    {
        $this->totalBuildings = Building::count();
        $this->totalRooms = Room::count();
        $this->totalCctvs = Cctv::count();
        $this->onlineCctvs = Cctv::where('status', 'online')->count();
        $this->offlineCctvs = Cctv::where('status', 'offline')->count();
        $this->maintenanceCctvs = Cctv::where('status', 'maintenance')->count();
        $this->totalUsers = User::count();
        $this->totalAdmins = Admin::count();
    }

    public function render()
    {
        return view('livewire.admin.dashboard')->layout('layouts.admin');
    }
}