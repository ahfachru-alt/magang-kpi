<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\Message as MessageModel;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;

class Message extends Component
{
    public $messages;
    public $newMessage = '';
    public $selectedAdmin = null;
    public $admins;

    public function mount()
    {
        $this->loadAdmins();
        $this->loadMessages();
    }

    public function loadAdmins()
    {
        $this->admins = Admin::where('is_active', true)->get();
    }

    public function loadMessages()
    {
        $user = Auth::user();
        
        $this->messages = MessageModel::where(function ($query) use ($user) {
            $query->where(function ($q) use ($user) {
                $q->where('from_id', $user->id)
                  ->where('from_type', 'App\Models\User');
            })->orWhere(function ($q) use ($user) {
                $q->where('to_id', $user->id)
                  ->where('to_type', 'App\Models\User');
            });
        })
        ->with(['from', 'to'])
        ->orderBy('created_at', 'asc')
        ->get();
    }

    public function selectAdmin($adminId)
    {
        $this->selectedAdmin = $adminId;
    }

    public function sendMessage()
    {
        if (empty($this->newMessage) || !$this->selectedAdmin) {
            return;
        }

        $user = Auth::user();
        
        MessageModel::create([
            'from_id' => $user->id,
            'from_type' => 'App\Models\User',
            'to_id' => $this->selectedAdmin,
            'to_type' => 'App\Models\Admin',
            'message' => $this->newMessage,
        ]);

        $this->newMessage = '';
        $this->loadMessages();
    }

    public function render()
    {
        return view('livewire.user.message')->layout('layouts.app');
    }
}