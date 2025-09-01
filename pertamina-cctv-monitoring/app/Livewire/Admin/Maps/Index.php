<?php

namespace App\Livewire\Admin\Maps;

use Livewire\Component;
use App\Models\Building;
use App\Models\Cctv;

class Index extends Component
{
    public $buildings;
    public $cctvs;
    public $selectedStatus = '';

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->buildings = Building::all();
        $this->loadCctvs();
    }

    public function loadCctvs()
    {
        $query = Cctv::with(['building', 'room']);
        
        if ($this->selectedStatus) {
            $query->where('status', $this->selectedStatus);
        }
        
        $this->cctvs = $query->get();
    }

    public function updatedSelectedStatus()
    {
        $this->loadCctvs();
    }

    public function render()
    {
        return view('livewire.admin.maps.index')->layout('layouts.admin');
    }
}