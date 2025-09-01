<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\Building;
use App\Models\Room;
use App\Models\Cctv;

class Location extends Component
{
    public $buildings;
    public $selectedBuilding = null;
    public $rooms;
    public $selectedRoom = null;
    public $cctvs;

    public function mount()
    {
        $this->loadBuildings();
    }

    public function loadBuildings()
    {
        $this->buildings = Building::with(['rooms.cctvs'])->get();
    }

    public function selectBuilding($buildingId)
    {
        $this->selectedBuilding = $buildingId;
        $this->selectedRoom = null;
        $this->loadRooms();
        $this->loadCctvs();
    }

    public function selectRoom($roomId)
    {
        $this->selectedRoom = $roomId;
        $this->loadCctvs();
    }

    public function loadRooms()
    {
        if ($this->selectedBuilding) {
            $this->rooms = Room::where('building_id', $this->selectedBuilding)
                ->with(['cctvs'])
                ->get();
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
        
        $this->cctvs = $query->get();
    }

    public function render()
    {
        return view('livewire.user.location')->layout('layouts.app');
    }
}