<?php

namespace App\Livewire\Admin\Contact;

use Livewire\Component;
use App\Models\Contact;

class Edit extends Component
{
    public Contact $contact;
    public $name = '';
    public $position = '';
    public $department = '';
    public $email = '';
    public $phone = '';

    public function mount($id)
    {
        $this->contact = Contact::findOrFail($id);
        $this->name = $this->contact->name;
        $this->position = $this->contact->position;
        $this->department = $this->contact->department;
        $this->email = $this->contact->email;
        $this->phone = $this->contact->phone;
    }

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

        $this->contact->update([
            'name' => $this->name,
            'position' => $this->position,
            'department' => $this->department,
            'email' => $this->email,
            'phone' => $this->phone,
        ]);

        session()->flash('message', 'Contact updated successfully.');

        return redirect()->route('admin.contact.index');
    }

    public function render()
    {
        return view('livewire.admin.contact.edit')->layout('layouts.admin');
    }
}