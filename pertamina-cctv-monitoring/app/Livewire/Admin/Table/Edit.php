<?php

namespace App\Livewire\Admin\Table;

use Livewire\Component;
use App\Models\Building;
use App\Models\Room;
use App\Models\Cctv;
use App\Models\Contact;

class Edit extends Component
{
    public $type = 'building';
    public $id;
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

    public function mount($type, $id)
    {
        $this->type = $type;
        $this->id = $id;
        $this->loadData();
    }

    public function loadData()
    {
        switch ($this->type) {
            case 'building':
                $building = Building::findOrFail($this->id);
                $this->name = $building->name;
                $this->address = $building->address;
                break;
            case 'room':
                $room = Room::findOrFail($this->id);
                $this->name = $room->name;
                $this->building_id = $room->building_id;
                $this->floor = $room->floor;
                break;
            case 'cctv':
                $cctv = Cctv::findOrFail($this->id);
                $this->name = $cctv->name;
                $this->ip_address = $cctv->ip_address;
                $this->building_id = $cctv->building_id;
                $this->room_id = $cctv->room_id;
                $this->status = $cctv->status;
                break;
            case 'contact':
                $contact = Contact::findOrFail($this->id);
                $this->name = $contact->name;
                $this->position = $contact->position;
                $this->department = $contact->department;
                $this->email = $contact->email;
                $this->phone = $contact->phone;
                break;
        }
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
                Building::findOrFail($this->id)->update([
                    'name' => $this->name,
                    'address' => $this->address,
                ]);
                break;
            case 'room':
                Room::findOrFail($this->id)->update([
                    'name' => $this->name,
                    'building_id' => $this->building_id,
                    'floor' => $this->floor,
                ]);
                break;
            case 'cctv':
                Cctv::findOrFail($this->id)->update([
                    'name' => $this->name,
                    'ip_address' => $this->ip_address,
                    'building_id' => $this->building_id,
                    'room_id' => $this->room_id,
                    'status' => $this->status,
                ]);
                break;
            case 'contact':
                Contact::findOrFail($this->id)->update([
                    'name' => $this->name,
                    'position' => $this->position,
                    'department' => $this->department,
                    'email' => $this->email,
                    'phone' => $this->phone,
                ]);
                break;
        }

        session()->flash('message', ucfirst($this->type) . ' updated successfully.');

        return redirect()->route('admin.table.index');
    }

    public function render()
    {
        $buildings = Building::all();
        $rooms = Room::when($this->building_id, function ($query) {
            $query->where('building_id', $this->building_id);
        })->get();

        return view('livewire.admin.table.edit', [
            'buildings' => $buildings,
            'rooms' => $rooms,
        ])->layout('layouts.admin');
    }
}