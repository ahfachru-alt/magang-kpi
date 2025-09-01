<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Building;
use App\Models\Cctv;

class Maps extends Component
{
    public $buildings;
    public $cctvs;
    public $selectedStatus = 'all';

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->buildings = Building::with(['rooms.cctvs'])->get();
        
        $query = Cctv::with(['room.building']);
        
        if ($this->selectedStatus !== 'all') {
            $query->where('status', $this->selectedStatus);
        }
        
        $this->cctvs = $query->get();
    }

    public function updatedSelectedStatus()
    {
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.admin.maps')->layout('layouts.admin');
    }
}