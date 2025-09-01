<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Services\CctvMonitoringService;
use App\Services\NotificationService;
use App\Services\MessageService;
use Illuminate\Support\Facades\Cache;

class RealTimeDashboard extends Component
{
    public $statusSummary = [];
    public $buildingStatus = [];
    public $recentAlerts = [];
    public $performanceMetrics = [];
    public $unreadNotifications = 0;
    public $unreadMessages = 0;
    public $lastUpdate = null;

    protected $monitoringService;
    protected $notificationService;
    protected $messageService;

    public function boot(
        CctvMonitoringService $monitoringService,
        NotificationService $notificationService,
        MessageService $messageService
    ) {
        $this->monitoringService = $monitoringService;
        $this->notificationService = $notificationService;
        $this->messageService = $messageService;
    }

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->statusSummary = $this->monitoringService->getStatusSummary();
        $this->buildingStatus = $this->monitoringService->getBuildingStatusSummary();
        $this->recentAlerts = $this->monitoringService->getCctvAlerts()->take(5);
        $this->performanceMetrics = $this->monitoringService->getPerformanceMetrics();
        
        if (auth('admin')->check()) {
            $this->unreadNotifications = $this->notificationService->getUnreadCount(null, auth('admin')->id());
            $this->unreadMessages = $this->messageService->getTotalAdminUnreadCount(auth('admin')->id());
        }
        
        $this->lastUpdate = now();
    }

    public function getListeners()
    {
        return [
            'echo:admin.' . (auth('admin')->id() ?? 0), 'notification' => 'handleNotification',
            'echo:admin.' . (auth('admin')->id() ?? 0), 'new-message' => 'handleNewMessage',
            'refresh-data' => 'loadData',
        ];
    }

    public function handleNotification($event)
    {
        $this->unreadNotifications++;
        $this->dispatch('show-notification', [
            'title' => $event['title'],
            'body' => $event['body'],
            'type' => $event['type'],
        ]);
    }

    public function handleNewMessage($event)
    {
        $this->unreadMessages++;
        $this->dispatch('show-notification', [
            'title' => 'New Message',
            'body' => 'You have a new message from ' . ($event['user']['name'] ?? 'User'),
            'type' => 'info',
        ]);
    }

    public function refreshData()
    {
        $this->loadData();
        $this->dispatch('data-refreshed');
    }

    public function render()
    {
        return view('livewire.admin.real-time-dashboard');
    }
}
