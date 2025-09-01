<?php

namespace App\Livewire\User\Notification;

use Livewire\Component;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    public $notifications;
    public $unreadCount = 0;

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $user = Auth::user();
        
        $this->notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        $this->unreadCount = $this->notifications->where('is_read', false)->count();
    }

    public function markAsRead($notificationId)
    {
        $notification = Notification::find($notificationId);
        if ($notification && $notification->user_id === Auth::id()) {
            $notification->update(['is_read' => true]);
            $this->loadNotifications();
        }
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
            
        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.user.notification.index')->layout('layouts.app');
    }
}