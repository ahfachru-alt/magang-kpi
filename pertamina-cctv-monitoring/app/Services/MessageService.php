<?php

namespace App\Services;

use App\Models\Message;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Log;
use Pusher\Pusher;

class MessageService
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
     * Send message from user to admin
     */
    public function sendUserMessage($userId, $adminId, $message)
    {
        try {
            $messageModel = Message::create([
                'user_id' => $userId,
                'admin_id' => $adminId,
                'message' => $message,
                'from_user' => true,
                'read_at' => null,
            ]);

            // Send real-time message
            $this->pusher->trigger('admin.' . $adminId, 'new-message', [
                'id' => $messageModel->id,
                'user_id' => $userId,
                'admin_id' => $adminId,
                'message' => $message,
                'from_user' => true,
                'created_at' => $messageModel->created_at->toISOString(),
                'user' => User::find($userId)->only(['id', 'name', 'email']),
            ]);

            return $messageModel;
        } catch (\Exception $e) {
            Log::error('Failed to send user message: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send message from admin to user
     */
    public function sendAdminMessage($adminId, $userId, $message)
    {
        try {
            $messageModel = Message::create([
                'user_id' => $userId,
                'admin_id' => $adminId,
                'message' => $message,
                'from_user' => false,
                'read_at' => null,
            ]);

            // Send real-time message
            $this->pusher->trigger('user.' . $userId, 'new-message', [
                'id' => $messageModel->id,
                'user_id' => $userId,
                'admin_id' => $adminId,
                'message' => $message,
                'from_user' => false,
                'created_at' => $messageModel->created_at->toISOString(),
                'admin' => Admin::find($adminId)->only(['id', 'name', 'email']),
            ]);

            return $messageModel;
        } catch (\Exception $e) {
            Log::error('Failed to send admin message: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get conversation between user and admin
     */
    public function getConversation($userId, $adminId, $limit = 50)
    {
        try {
            return Message::where(function ($query) use ($userId, $adminId) {
                $query->where('user_id', $userId)
                      ->where('admin_id', $adminId);
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();
        } catch (\Exception $e) {
            Log::error('Failed to get conversation: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get all conversations for a user
     */
    public function getUserConversations($userId)
    {
        try {
            return Message::where('user_id', $userId)
                ->select('admin_id')
                ->distinct()
                ->with('admin:id,name,email')
                ->get()
                ->map(function ($item) use ($userId) {
                    $lastMessage = Message::where('user_id', $userId)
                        ->where('admin_id', $item->admin_id)
                        ->orderBy('created_at', 'desc')
                        ->first();

                    return [
                        'admin' => $item->admin,
                        'last_message' => $lastMessage->message,
                        'last_message_time' => $lastMessage->created_at,
                        'unread_count' => $this->getUnreadCount($userId, $item->admin_id),
                    ];
                })
                ->sortByDesc('last_message_time');
        } catch (\Exception $e) {
            Log::error('Failed to get user conversations: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get all conversations for an admin
     */
    public function getAdminConversations($adminId)
    {
        try {
            return Message::where('admin_id', $adminId)
                ->select('user_id')
                ->distinct()
                ->with('user:id,name,email')
                ->get()
                ->map(function ($item) use ($adminId) {
                    $lastMessage = Message::where('user_id', $item->user_id)
                        ->where('admin_id', $adminId)
                        ->orderBy('created_at', 'desc')
                        ->first();

                    return [
                        'user' => $item->user,
                        'last_message' => $lastMessage->message,
                        'last_message_time' => $lastMessage->created_at,
                        'unread_count' => $this->getUnreadCount($item->user_id, $adminId),
                    ];
                })
                ->sortByDesc('last_message_time');
        } catch (\Exception $e) {
            Log::error('Failed to get admin conversations: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Mark messages as read
     */
    public function markAsRead($userId, $adminId, $markerId = null)
    {
        try {
            $query = Message::where('user_id', $userId)
                ->where('admin_id', $adminId)
                ->whereNull('read_at');

            if ($markerId) {
                $query->where('id', '<=', $markerId);
            }

            $query->update(['read_at' => now()]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to mark messages as read: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get unread message count
     */
    public function getUnreadCount($userId, $adminId)
    {
        try {
            return Message::where('user_id', $userId)
                ->where('admin_id', $adminId)
                ->whereNull('read_at')
                ->count();
        } catch (\Exception $e) {
            Log::error('Failed to get unread count: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get total unread count for user
     */
    public function getTotalUnreadCount($userId)
    {
        try {
            return Message::where('user_id', $userId)
                ->whereNull('read_at')
                ->count();
        } catch (\Exception $e) {
            Log::error('Failed to get total unread count: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get total unread count for admin
     */
    public function getTotalAdminUnreadCount($adminId)
    {
        try {
            return Message::where('admin_id', $adminId)
                ->whereNull('read_at')
                ->count();
        } catch (\Exception $e) {
            Log::error('Failed to get total admin unread count: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Delete conversation
     */
    public function deleteConversation($userId, $adminId)
    {
        try {
            Message::where('user_id', $userId)
                ->where('admin_id', $adminId)
                ->delete();

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to delete conversation: ' . $e->getMessage());
            return false;
        }
    }
}