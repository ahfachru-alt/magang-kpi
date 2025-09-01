<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Building;
use App\Models\Room;
use App\Models\Cctv;
use App\Models\Contact;

class Table extends Component
{
    public $activeTab = 'buildings';
    public $search = '';

    public function mount()
    {
        // Default to buildings tab
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->search = '';
    }

    public function render()
    {
        return view('livewire.admin.table')->layout('layouts.admin');
    }
}