<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class Notification extends Component
{
    public $notifications;
    public $unreadCount = 0;

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $admin = Auth::guard('admin')->user();
        
        $this->notifications = Notification::where('admin_id', $admin->id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        $this->unreadCount = $this->notifications->where('is_read', false)->count();
    }

    public function markAsRead($notificationId)
    {
        $notification = Notification::find($notificationId);
        if ($notification && $notification->admin_id === Auth::guard('admin')->id()) {
            $notification->update(['is_read' => true]);
            $this->loadNotifications();
        }
    }

    public function markAllAsRead()
    {
        $admin = Auth::guard('admin')->user();
        Notification::where('admin_id', $admin->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
            
        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.admin.notification')->layout('layouts.admin');
    }
}