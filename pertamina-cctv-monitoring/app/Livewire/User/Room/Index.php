<?php

namespace App\Livewire\User\Room;

use Livewire\Component;
use App\Models\Room;
use App\Models\Cctv;

class Index extends Component
{
    public $rooms;
    public $selectedRoom = null;
    public $cctvs;
    public $search = '';

    public function mount()
    {
        $this->loadRooms();
    }

    public function loadRooms()
    {
        $query = Room::with(['building', 'cctvs']);
        
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhereHas('building', function ($q) {
                      $q->where('name', 'like', '%' . $this->search . '%');
                  });
        }
        
        $this->rooms = $query->get();
    }

    public function selectRoom($roomId)
    {
        $this->selectedRoom = $roomId;
        $this->loadCctvs();
    }

    public function loadCctvs()
    {
        if ($this->selectedRoom) {
            $this->cctvs = Cctv::where('room_id', $this->selectedRoom)
                ->with(['room.building'])
                ->get();
        } else {
            $this->cctvs = collect();
        }
    }

    public function updatedSearch()
    {
        $this->loadRooms();
    }

    public function render()
    {
        return view('livewire.user.room.index')->layout('layouts.app');
    }
}