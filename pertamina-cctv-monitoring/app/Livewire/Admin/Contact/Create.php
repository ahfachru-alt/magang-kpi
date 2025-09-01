<?php

namespace App\Livewire\Admin\Contact;

use Livewire\Component;
use App\Models\Contact;

class Create extends Component
{
    public $name = '';
    public $position = '';
    public $department = '';
    public $email = '';
    public $phone = '';

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:255',
        ];
    }

    public function save()
    {
        $this->validate();

        Contact::create([
            'name' => $this->name,
            'position' => $this->position,
            'department' => $this->department,
            'email' => $this->email,
            'phone' => $this->phone,
        ]);

        session()->flash('message', 'Contact created successfully.');

        return redirect()->route('admin.contact.index');
    }

    public function render()
    {
        return view('livewire.admin.contact.create')->layout('layouts.admin');
    }
}