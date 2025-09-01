<?php

namespace App\Livewire\Admin\Notification;

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
        if (!$this->notification->is_read && $this->notification->admin_id === Auth::guard('admin')->id()) {
            $this->notification->update(['is_read' => true]);
        }
    }

    public function render()
    {
        return view('livewire.admin.notification.show')->layout('layouts.admin');
    }
}