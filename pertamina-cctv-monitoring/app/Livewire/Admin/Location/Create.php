<?php

namespace App\Livewire\Admin\Location;

use Livewire\Component;
use App\Models\Building;
use App\Models\Room;
use App\Models\Cctv;

class Create extends Component
{
    public $building_name = '';
    public $building_address = '';
    public $room_name = '';
    public $room_floor = '';
    public $cctv_name = '';
    public $cctv_ip_address = '';
    public $cctv_status = 'online';

    protected function rules()
    {
        return [
            'building_name' => 'required|string|max:255',
            'building_address' => 'required|string|max:255',
            'room_name' => 'required|string|max:255',
            'room_floor' => 'required|string|max:50',
            'cctv_name' => 'required|string|max:255',
            'cctv_ip_address' => 'required|string|max:255',
            'cctv_status' => 'required|in:online,offline,maintenance',
        ];
    }

    public function save()
    {
        $this->validate();

        // Create building
        $building = Building::create([
            'name' => $this->building_name,
            'address' => $this->building_address,
        ]);

        // Create room
        $room = Room::create([
            'name' => $this->room_name,
            'building_id' => $building->id,
            'floor' => $this->room_floor,
        ]);

        // Create CCTV
        Cctv::create([
            'name' => $this->cctv_name,
            'ip_address' => $this->cctv_ip_address,
            'building_id' => $building->id,
            'room_id' => $room->id,
            'status' => $this->cctv_status,
        ]);

        session()->flash('message', 'Location created successfully.');

        return redirect()->route('admin.location.index');
    }

    public function render()
    {
        return view('livewire.admin.location.create')->layout('layouts.admin');
    }
}