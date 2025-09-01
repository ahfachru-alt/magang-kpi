<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Log;
use Pusher\Pusher;

class NotificationService
{
    protected $pusher;

    public function __construct()
    {
        $this->pusher = new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            config('broadcasting.connections.pusher.options')
        );
    }

    /**
     * Send notification to specific user
     */
    public function sendToUser($userId, $title, $body, $type = 'info', $data = [])
    {
        try {
            $notification = Notification::create([
                'user_id' => $userId,
                'admin_id' => null,
                'type' => $type,
                'title' => $title,
                'body' => $body,
                'data' => $data,
                'read_at' => null,
            ]);

            // Send real-time notification
            $this->pusher->trigger('user.' . $userId, 'notification', [
                'id' => $notification->id,
                'title' => $title,
                'body' => $body,
                'type' => $type,
                'data' => $data,
                'created_at' => $notification->created_at->toISOString(),
            ]);

            return $notification;
        } catch (\Exception $e) {
            Log::error('Failed to send notification to user: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification to specific admin
     */
    public function sendToAdmin($adminId, $title, $body, $type = 'info', $data = [])
    {
        try {
            $notification = Notification::create([
                'user_id' => null,
                'admin_id' => $adminId,
                'type' => $type,
                'title' => $title,
                'body' => $body,
                'data' => $data,
                'read_at' => null,
            ]);

            // Send real-time notification
            $this->pusher->trigger('admin.' . $adminId, 'notification', [
                'id' => $notification->id,
                'title' => $title,
                'body' => $body,
                'type' => $type,
                'data' => $data,
                'created_at' => $notification->created_at->toISOString(),
            ]);

            return $notification;
        } catch (\Exception $e) {
            Log::error('Failed to send notification to admin: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification to all users
     */
    public function sendToAllUsers($title, $body, $type = 'info', $data = [])
    {
        try {
            $users = User::all();
            
            foreach ($users as $user) {
                $this->sendToUser($user->id, $title, $body, $type, $data);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send notification to all users: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification to all admins
     */
    public function sendToAllAdmins($title, $body, $type = 'info', $data = [])
    {
        try {
            $admins = Admin::all();
            
            foreach ($admins as $admin) {
                $this->sendToAdmin($admin->id, $title, $body, $type, $data);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send notification to all admins: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send CCTV status change notification
     */
    public function sendCctvStatusNotification($cctv, $oldStatus, $newStatus)
    {
        $title = "CCTV Status Changed";
        $body = "CCTV {$cctv->name} status changed from {$oldStatus} to {$newStatus}";
        $type = $newStatus === 'online' ? 'success' : 'warning';
        
        $data = [
            'cctv_id' => $cctv->id,
            'cctv_name' => $cctv->name,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'building' => $cctv->building->name,
            'room' => $cctv->room->name,
        ];

        // Notify all admins
        $this->sendToAllAdmins($title, $body, $type, $data);

        // Notify users who have access to this building/room
        $users = User::whereHas('buildings', function ($query) use ($cctv) {
            $query->where('id', $cctv->building_id);
        })->get();

        foreach ($users as $user) {
            $this->sendToUser($user->id, $title, $body, $type, $data);
        }
    }

    /**
     * Send system maintenance notification
     */
    public function sendMaintenanceNotification($title, $body, $data = [])
    {
        $this->sendToAllUsers($title, $body, 'warning', $data);
        $this->sendToAllAdmins($title, $body, 'warning', $data);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId, $userId = null, $adminId = null)
    {
        try {
            $notification = Notification::where('id', $notificationId);
            
            if ($userId) {
                $notification->where('user_id', $userId);
            }
            
            if ($adminId) {
                $notification->where('admin_id', $adminId);
            }

            $notification->update(['read_at' => now()]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to mark notification as read: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get unread count for user/admin
     */
    public function getUnreadCount($userId = null, $adminId = null)
    {
        try {
            $query = Notification::whereNull('read_at');
            
            if ($userId) {
                $query->where('user_id', $userId);
            }
            
            if ($adminId) {
                $query->where('admin_id', $adminId);
            }

            return $query->count();
        } catch (\Exception $e) {
            Log::error('Failed to get unread count: ' . $e->getMessage());
            return 0;
        }
    }
}