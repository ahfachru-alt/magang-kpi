<?php

namespace App\Livewire\User\Contact;

use Livewire\Component;
use App\Models\Contact;

class Index extends Component
{
    public $contacts;
    public $search = '';

    public function mount()
    {
        $this->loadContacts();
    }

    public function loadContacts()
    {
        $query = Contact::query();
        
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%')
                  ->orWhere('position', 'like', '%' . $this->search . '%')
                  ->orWhere('department', 'like', '%' . $this->search . '%');
            });
        }
        
        $this->contacts = $query->get();
    }

    public function updatedSearch()
    {
        $this->loadContacts();
    }

    public function render()
    {
        return view('livewire.user.contact.index')->layout('layouts.app');
    }
}