<?php

namespace App\Livewire\User\Notification;

use Livewire\Component;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class Show extends Component
{
    public Notification $notification;

    public function mount($notification)
    {
        $this->notification = $notification;
        
        // Mark as read if not already read
        if (!$this->notification->is_read && $this->notification->user_id === Auth::id()) {
            $this->notification->update(['is_read' => true]);
        }
    }

    public function render()
    {
        return view('livewire.user.notification.show')->layout('layouts.app');
    }
}