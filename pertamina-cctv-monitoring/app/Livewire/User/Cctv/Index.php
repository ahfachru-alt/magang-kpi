<?php

namespace App\Livewire\User\Cctv;

use Livewire\Component;
use App\Models\Cctv;
use App\Models\Building;
use App\Models\Room;

class Index extends Component
{
    public $cctvs;
    public $buildings;
    public $rooms;
    public $selectedBuilding = '';
    public $selectedRoom = '';
    public $selectedStatus = '';
    public $search = '';

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->buildings = Building::all();
        $this->loadRooms();
        $this->loadCctvs();
    }

    public function loadRooms()
    {
        if ($this->selectedBuilding) {
            $this->rooms = Room::where('building_id', $this->selectedBuilding)->get();
        } else {
            $this->rooms = collect();
        }
    }

    public function loadCctvs()
    {
        $query = Cctv::with(['room.building']);
        
        if ($this->selectedBuilding) {
            $query->whereHas('room', function ($q) {
                $q->where('building_id', $this->selectedBuilding);
            });
        }
        
        if ($this->selectedRoom) {
            $query->where('room_id', $this->selectedRoom);
        }
        
        if ($this->selectedStatus) {
            $query->where('status', $this->selectedStatus);
        }
        
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('ip_address', 'like', '%' . $this->search . '%')
                  ->orWhereHas('room', function ($r) {
                      $r->where('name', 'like', '%' . $this->search . '%');
                  })
                  ->orWhereHas('room.building', function ($b) {
                      $b->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }
        
        $this->cctvs = $query->get();
    }

    public function updatedSelectedBuilding()
    {
        $this->selectedRoom = '';
        $this->loadRooms();
        $this->loadCctvs();
    }

    public function updatedSelectedRoom()
    {
        $this->loadCctvs();
    }

    public function updatedSelectedStatus()
    {
        $this->loadCctvs();
    }

    public function updatedSearch()
    {
        $this->loadCctvs();
    }

    public function render()
    {
        return view('livewire.user.cctv.index')->layout('layouts.app');
    }
}