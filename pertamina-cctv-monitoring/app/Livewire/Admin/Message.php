<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Message as MessageModel;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Message extends Component
{
    public $messages;
    public $newMessage = '';
    public $selectedUser = null;
    public $users;

    public function mount()
    {
        $this->loadUsers();
        $this->loadMessages();
    }

    public function loadUsers()
    {
        $this->users = User::all();
    }

    public function loadMessages()
    {
        $admin = Auth::guard('admin')->user();
        
        $this->messages = MessageModel::where(function ($query) use ($admin) {
            $query->where(function ($q) use ($admin) {
                $q->where('from_id', $admin->id)
                  ->where('from_type', 'App\Models\Admin');
            })->orWhere(function ($q) use ($admin) {
                $q->where('to_id', $admin->id)
                  ->where('to_type', 'App\Models\Admin');
            });
        })
        ->with(['from', 'to'])
        ->orderBy('created_at', 'asc')
        ->get();
    }

    public function selectUser($userId)
    {
        $this->selectedUser = $userId;
    }

    public function sendMessage()
    {
        if (empty($this->newMessage) || !$this->selectedUser) {
            return;
        }

        $admin = Auth::guard('admin')->user();
        
        MessageModel::create([
            'from_id' => $admin->id,
            'from_type' => 'App\Models\Admin',
            'to_id' => $this->selectedUser,
            'to_type' => 'App\Models\User',
            'message' => $this->newMessage,
        ]);

        $this->newMessage = '';
        $this->loadMessages();
    }

    public function render()
    {
        return view('livewire.admin.message')->layout('layouts.admin');
    }
}