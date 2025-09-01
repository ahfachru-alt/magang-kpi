<?php

namespace App\Livewire\Admin\Table;

use Livewire\Component;
use App\Models\Building;
use App\Models\Room;
use App\Models\Cctv;
use App\Models\Contact;

class Create extends Component
{
    public $type = 'building';
    public $name = '';
    public $address = '';
    public $building_id = '';
    public $floor = '';
    public $ip_address = '';
    public $room_id = '';
    public $status = 'online';
    public $position = '';
    public $department = '';
    public $email = '';
    public $phone = '';

    public function mount($type = 'building')
    {
        $this->type = $type;
    }

    protected function rules()
    {
        $rules = [];

        switch ($this->type) {
            case 'building':
                $rules = [
                    'name' => 'required|string|max:255',
                    'address' => 'required|string|max:255',
                ];
                break;
            case 'room':
                $rules = [
                    'name' => 'required|string|max:255',
                    'building_id' => 'required|exists:buildings,id',
                    'floor' => 'required|string|max:50',
                ];
                break;
            case 'cctv':
                $rules = [
                    'name' => 'required|string|max:255',
                    'ip_address' => 'required|string|max:255',
                    'building_id' => 'required|exists:buildings,id',
                    'room_id' => 'required|exists:rooms,id',
                    'status' => 'required|in:online,offline,maintenance',
                ];
                break;
            case 'contact':
                $rules = [
                    'name' => 'required|string|max:255',
                    'position' => 'required|string|max:255',
                    'department' => 'required|string|max:255',
                    'email' => 'required|email|max:255',
                    'phone' => 'required|string|max:255',
                ];
                break;
        }

        return $rules;
    }

    public function save()
    {
        $this->validate();

        switch ($this->type) {
            case 'building':
                Building::create([
                    'name' => $this->name,
                    'address' => $this->address,
                ]);
                break;
            case 'room':
                Room::create([
                    'name' => $this->name,
                    'building_id' => $this->building_id,
                    'floor' => $this->floor,
                ]);
                break;
            case 'cctv':
                Cctv::create([
                    'name' => $this->name,
                    'ip_address' => $this->ip_address,
                    'building_id' => $this->building_id,
                    'room_id' => $this->room_id,
                    'status' => $this->status,
                ]);
                break;
            case 'contact':
                Contact::create([
                    'name' => $this->name,
                    'position' => $this->position,
                    'department' => $this->department,
                    'email' => $this->email,
                    'phone' => $this->phone,
                ]);
                break;
        }

        session()->flash('message', ucfirst($this->type) . ' created successfully.');

        return redirect()->route('admin.table.index');
    }

    public function render()
    {
        $buildings = Building::all();
        $rooms = Room::when($this->building_id, function ($query) {
            $query->where('building_id', $this->building_id);
        })->get();

        return view('livewire.admin.table.create', [
            'buildings' => $buildings,
            'rooms' => $rooms,
        ])->layout('layouts.admin');
    }
}