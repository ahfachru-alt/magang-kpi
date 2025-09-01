<?php

namespace App\Livewire\Admin\Location;

use Livewire\Component;
use App\Models\Building;
use App\Models\Room;
use App\Models\Cctv;

class Edit extends Component
{
    public $id;
    public $building_name = '';
    public $building_address = '';
    public $room_name = '';
    public $room_floor = '';
    public $cctv_name = '';
    public $cctv_ip_address = '';
    public $cctv_status = 'online';

    public function mount($id)
    {
        $this->id = $id;
        $this->loadData();
    }

    public function loadData()
    {
        $cctv = Cctv::with(['building', 'room'])->findOrFail($this->id);
        
        $this->building_name = $cctv->building->name;
        $this->building_address = $cctv->building->address;
        $this->room_name = $cctv->room->name;
        $this->room_floor = $cctv->room->floor;
        $this->cctv_name = $cctv->name;
        $this->cctv_ip_address = $cctv->ip_address;
        $this->cctv_status = $cctv->status;
    }

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

        $cctv = Cctv::findOrFail($this->id);

        // Update building
        $cctv->building->update([
            'name' => $this->building_name,
            'address' => $this->building_address,
        ]);

        // Update room
        $cctv->room->update([
            'name' => $this->room_name,
            'floor' => $this->room_floor,
        ]);

        // Update CCTV
        $cctv->update([
            'name' => $this->cctv_name,
            'ip_address' => $this->cctv_ip_address,
            'status' => $this->cctv_status,
        ]);

        session()->flash('message', 'Location updated successfully.');

        return redirect()->route('admin.location.index');
    }

    public function render()
    {
        return view('livewire.admin.location.edit')->layout('layouts.admin');
    }
}