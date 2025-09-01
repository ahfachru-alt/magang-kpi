<?php

namespace App\Livewire\Admin\Maps;

use Livewire\Component;
use App\Models\Building;
use App\Models\Room;
use App\Models\Cctv;

class Edit extends Component
{
    public $id;
    public $name = '';
    public $address = '';
    public $latitude = '';
    public $longitude = '';
    public $building_id = '';
    public $room_id = '';
    public $ip_address = '';
    public $status = 'online';

    public function mount($id)
    {
        $this->id = $id;
        $this->loadData();
    }

    public function loadData()
    {
        $cctv = Cctv::with(['building', 'room'])->findOrFail($this->id);
        
        $this->name = $cctv->name;
        $this->address = $cctv->building->address;
        $this->latitude = $cctv->latitude ?? $cctv->building->latitude;
        $this->longitude = $cctv->longitude ?? $cctv->building->longitude;
        $this->building_id = $cctv->building_id;
        $this->room_id = $cctv->room_id;
        $this->ip_address = $cctv->ip_address;
        $this->status = $cctv->status;
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'building_id' => 'required|exists:buildings,id',
            'room_id' => 'required|exists:rooms,id',
            'ip_address' => 'required|string|max:255',
            'status' => 'required|in:online,offline,maintenance',
        ];
    }

    public function save()
    {
        $this->validate();

        $cctv = Cctv::findOrFail($this->id);

        // Update building
        $cctv->building->update([
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ]);

        // Update CCTV
        $cctv->update([
            'name' => $this->name,
            'ip_address' => $this->ip_address,
            'building_id' => $this->building_id,
            'room_id' => $this->room_id,
            'status' => $this->status,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ]);

        session()->flash('message', 'Map location updated successfully.');

        return redirect()->route('admin.maps.index');
    }

    public function render()
    {
        $buildings = Building::all();
        $rooms = Room::when($this->building_id, function ($query) {
            $query->where('building_id', $this->building_id);
        })->get();

        return view('livewire.admin.maps.edit', [
            'buildings' => $buildings,
            'rooms' => $rooms,
        ])->layout('layouts.admin');
    }
}