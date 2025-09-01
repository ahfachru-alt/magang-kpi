<?php

namespace App\Livewire\Admin\Table;

use Livewire\Component;
use App\Models\Building;
use App\Models\Room;
use App\Models\Cctv;
use App\Models\Contact;

class Index extends Component
{
    public $activeTab = 'buildings';
    public $search = '';

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->search = '';
    }

    public function render()
    {
        $data = collect();

        switch ($this->activeTab) {
            case 'buildings':
                $data = Building::when($this->search, function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('address', 'like', '%' . $this->search . '%');
                })->get();
                break;
            case 'rooms':
                $data = Room::when($this->search, function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('floor', 'like', '%' . $this->search . '%');
                })->with('building')->get();
                break;
            case 'cctvs':
                $data = Cctv::when($this->search, function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('ip_address', 'like', '%' . $this->search . '%');
                })->with(['building', 'room'])->get();
                break;
            case 'contacts':
                $data = Contact::when($this->search, function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('position', 'like', '%' . $this->search . '%')
                          ->orWhere('department', 'like', '%' . $this->search . '%');
                })->get();
                break;
        }

        return view('livewire.admin.table.index', [
            'data' => $data
        ])->layout('layouts.admin');
    }
}