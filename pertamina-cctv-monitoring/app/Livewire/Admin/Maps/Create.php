<?php

namespace App\Livewire\Admin\Maps;

use Livewire\Component;
use App\Models\Building;
use App\Models\Room;
use App\Models\Cctv;

class Create extends Component
{
    public $name = '';
    public $address = '';
    public $latitude = '';
    public $longitude = '';
    public $building_id = '';
    public $room_id = '';
    public $ip_address = '';
    public $status = 'online';

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

        // Create building if it doesn't exist
        $building = Building::firstOrCreate(
            ['id' => $this->building_id],
            [
                'name' => $this->name,
                'address' => $this->address,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
            ]
        );

        // Create room if it doesn't exist
        $room = Room::firstOrCreate(
            ['id' => $this->room_id],
            [
                'name' => 'Room ' . $this->room_id,
                'building_id' => $building->id,
                'floor' => '1',
            ]
        );

        // Create CCTV
        Cctv::create([
            'name' => $this->name,
            'ip_address' => $this->ip_address,
            'building_id' => $building->id,
            'room_id' => $room->id,
            'status' => $this->status,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ]);

        session()->flash('message', 'Map location created successfully.');

        return redirect()->route('admin.maps.index');
    }

    public function render()
    {
        $buildings = Building::all();
        $rooms = Room::when($this->building_id, function ($query) {
            $query->where('building_id', $this->building_id);
        })->get();

        return view('livewire.admin.maps.create', [
            'buildings' => $buildings,
            'rooms' => $rooms,
        ])->layout('layouts.admin');
    }
}