<div class="space-y-6" x-data="{ 
    refreshInterval: null,
    init() {
        this.refreshInterval = setInterval(() => {
            $wire.refreshData();
        }, 30000); // Refresh every 30 seconds
    },
    destroy() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
        }
    }
}" x-init="init()" @beforeunload="destroy()">
    
    <!-- Header with Refresh Button -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Real-Time Dashboard</h2>
            <p class="text-gray-600 dark:text-gray-400">Live monitoring of CCTV system status</p>
        </div>
        <div class="flex items-center space-x-4">
            <div class="text-sm text-gray-500 dark:text-gray-400">
                Last updated: {{ $lastUpdate?->format('H:i:s') ?? 'Never' }}
            </div>
            <button wire:click="refreshData" 
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span>Refresh</span>
            </button>
        </div>
    </div>

    <!-- Status Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Total CCTVs -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total CCTVs</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $statusSummary['total'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Online CCTVs -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Online</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $statusSummary['online'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Offline CCTVs -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 dark:bg-red-900 rounded-lg">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Offline</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $statusSummary['offline'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Maintenance CCTVs -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Maintenance</p>
                    <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $statusSummary['maintenance'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Uptime Percentage -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">System Uptime</h3>
            <div class="text-center">
                <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                    {{ $performanceMetrics['uptime_percentage'] ?? 0 }}%
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                    {{ $performanceMetrics['total_uptime_hours'] ?? 0 }} hours uptime
                </p>
            </div>
        </div>

        <!-- Response Time -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Avg Response Time</h3>
            <div class="text-center">
                <div class="text-3xl font-bold text-green-600 dark:text-green-400">
                    {{ $performanceMetrics['average_response_time'] ?? 0 }}ms
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                    Online CCTVs only
                </p>
            </div>
        </div>

        <!-- Notifications -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Notifications</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Unread Notifications</span>
                    <span class="text-lg font-semibold text-orange-600 dark:text-orange-400">{{ $unreadNotifications }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Unread Messages</span>
                    <span class="text-lg font-semibold text-blue-600 dark:text-blue-400">{{ $unreadMessages }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Building Status Grid -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Building Status Overview</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($buildingStatus as $building)
            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                <div class="flex justify-between items-start mb-2">
                    <h4 class="font-medium text-gray-900 dark:text-white">{{ $building['name'] }}</h4>
                    <div class="text-sm font-semibold px-2 py-1 rounded-full 
                        {{ $building['health_percentage'] >= 80 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                           ($building['health_percentage'] >= 60 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 
                           'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200') }}">
                        {{ $building['health_percentage'] }}%
                    </div>
                </div>
                <div class="space-y-1 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Total:</span>
                        <span class="font-medium">{{ $building['total_cctvs'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-green-600 dark:text-green-400">Online:</span>
                        <span class="font-medium">{{ $building['online_cctvs'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-red-600 dark:text-red-400">Offline:</span>
                        <span class="font-medium">{{ $building['offline_cctvs'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-yellow-600 dark:text-yellow-400">Maintenance:</span>
                        <span class="font-medium">{{ $building['maintenance_cctvs'] }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Recent Alerts -->
    @if(count($recentAlerts) > 0)
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Alerts</h3>
        <div class="space-y-3">
            @foreach($recentAlerts as $alert)
            <div class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $alert['name'] }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $alert['building'] }} / {{ $alert['room'] }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium text-red-600 dark:text-red-400">
                        Offline for {{ $alert['downtime_hours'] }} hours
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $alert['last_online']?->diffForHumans() ?? 'Unknown' }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Auto-refresh indicator -->
    <div class="text-center text-sm text-gray-500 dark:text-gray-400">
        <div class="inline-flex items-center space-x-2">
            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
            <span>Auto-refreshing every 30 seconds</span>
        </div>
    </div>

    <!-- JavaScript for real-time updates -->
    <script>
        document.addEventListener('livewire:init', () => {
            // Listen for data refresh events
            Livewire.on('data-refreshed', () => {
                // Update last update time
                const now = new Date();
                const timeString = now.toLocaleTimeString();
                document.querySelector('[data-last-update]').textContent = timeString;
                
                // Show refresh notification
                Livewire.dispatch('show-notification', {
                    title: 'Data Updated',
                    body: 'Dashboard data has been refreshed',
                    type: 'success'
                });
            });

            // Listen for notification events
            Livewire.on('show-notification', (event) => {
                // You can implement a toast notification system here
                console.log('Notification:', event);
            });
        });
    </script>
</div>
