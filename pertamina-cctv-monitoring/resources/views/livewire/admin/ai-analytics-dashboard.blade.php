<div class="space-y-6" x-data="{ 
    refreshInterval: null,
    init() {
        this.refreshInterval = setInterval(() => {
            $wire.refreshAnalytics();
        }, 60000); // Refresh every minute
    },
    destroy() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
        }
    }
}" x-init="init()" @beforeunload="destroy()">
    
    <!-- Header with AI Controls -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">🤖 AI Analytics Dashboard</h2>
            <p class="text-gray-600 dark:text-gray-400">Machine Learning powered insights and predictive analytics</p>
        </div>
        <div class="flex items-center space-x-4">
            <div class="text-sm text-gray-500 dark:text-gray-400">
                Last analysis: {{ $lastAnalysis?->format('H:i:s') ?? 'Never' }}
            </div>
            <button wire:click="runAnomalyDetection" 
                    class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors duration-200 flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
                <span>Run AI Analysis</span>
            </button>
            <button wire:click="refreshAnalytics" 
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span>Refresh</span>
            </button>
        </div>
    </div>

    <!-- AI Insights Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($aiInsights as $insight)
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $insight['title'] }}</h3>
                <div class="w-3 h-3 rounded-full 
                    {{ $insight['severity'] === 'critical' ? 'bg-red-500' : 
                       ($insight['severity'] === 'high' ? 'bg-orange-500' : 
                       ($insight['severity'] === 'medium' ? 'bg-yellow-500' : 'bg-green-500')) }}">
                </div>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ $insight['description'] }}</p>
            <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                <p class="text-xs font-medium text-gray-700 dark:text-gray-300">Recommendation:</p>
                <p class="text-xs text-gray-600 dark:text-gray-400">{{ $insight['recommendation'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Anomaly Statistics & Performance -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Anomaly Statistics -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">📊 Anomaly Statistics</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Total Anomalies</span>
                    <span class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $anomalyStatistics['total'] ?? 0 }}</span>
                </div>
                
                @if(isset($anomalyStatistics['by_severity']))
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Critical</span>
                        <span class="text-sm font-semibold text-red-600 dark:text-red-400">{{ $anomalyStatistics['by_severity']['critical'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">High</span>
                        <span class="text-sm font-semibold text-orange-600 dark:text-orange-400">{{ $anomalyStatistics['by_severity']['high'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Medium</span>
                        <span class="text-sm font-semibold text-yellow-600 dark:text-yellow-400">{{ $anomalyStatistics['by_severity']['medium'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Low</span>
                        <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">{{ $anomalyStatistics['by_severity']['low'] ?? 0 }}</span>
                    </div>
                </div>
                @endif
                
                <div class="pt-2 border-t border-gray-200 dark:border-gray-600">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Trend</span>
                        <span class="text-sm font-semibold 
                            {{ ($anomalyStatistics['trend'] ?? 'stable') === 'increasing' ? 'text-red-600 dark:text-red-400' : 
                               (($anomalyStatistics['trend'] ?? 'stable') === 'decreasing' ? 'text-green-600 dark:text-green-400' : 'text-gray-600 dark:text-gray-400') }}">
                            {{ ucfirst($anomalyStatistics['trend'] ?? 'stable') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">⚡ Performance Metrics</h3>
            <div class="space-y-4">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                        {{ $performanceMetrics['uptime_percentage'] ?? 0 }}%
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">System Uptime</p>
                </div>
                
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                        {{ $performanceMetrics['average_response_time'] ?? 0 }}ms
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Avg Response Time</p>
                </div>
                
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                        {{ $trendAnalysis['performance_trends']['overall_performance'] ?? 0 }}
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Performance Score</p>
                </div>
            </div>
        </div>

        <!-- Trend Analysis -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">📈 Trend Analysis</h3>
            <div class="space-y-4">
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Daily Avg</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $trendAnalysis['anomaly_frequency']['daily_average'] ?? 0 }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Weekly Avg</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $trendAnalysis['anomaly_frequency']['weekly_average'] ?? 0 }}
                        </span>
                    </div>
                </div>
                
                <div class="pt-2 border-t border-gray-200 dark:border-gray-600">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Uptime Trend</span>
                        <span class="text-sm font-semibold 
                            {{ ($trendAnalysis['performance_trends']['uptime_trend'] ?? 'good') === 'excellent' ? 'text-green-600 dark:text-green-400' : 
                               (($trendAnalysis['performance_trends']['uptime_trend'] ?? 'good') === 'good' ? 'text-blue-600 dark:text-blue-400' : 'text-red-600 dark:text-red-400') }}">
                            {{ ucfirst($trendAnalysis['performance_trends']['uptime_trend'] ?? 'good') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Anomalies -->
    @if(count($recentAnomalies) > 0)
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">🚨 Recent Anomalies</h3>
        <div class="space-y-3">
            @foreach($recentAnomalies as $anomaly)
            <div class="flex items-center justify-between p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                <div class="flex items-center space-x-4">
                    <div class="w-3 h-3 rounded-full 
                        {{ $anomaly->severity === 'critical' ? 'bg-red-500' : 
                           ($anomaly->severity === 'high' ? 'bg-orange-500' : 
                           ($anomaly->severity === 'medium' ? 'bg-yellow-500' : 'bg-blue-500')) }}">
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">
                            {{ $anomaly->cctv->name ?? 'Unknown CCTV' }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $anomaly->cctv->building->name ?? 'Unknown' }} / {{ $anomaly->cctv->room->name ?? 'Unknown' }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $anomaly->type_label }}
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium text-red-600 dark:text-red-400">
                        {{ ucfirst($anomaly->severity) }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $anomaly->detected_at->diffForHumans() }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Predictive Insights -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Failure Prediction -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">🔮 Failure Prediction</h3>
            <div class="space-y-3">
                <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $predictiveInsights['predicted_failures'] ?? 'No prediction available' }}</p>
                </div>
            </div>
        </div>

        <!-- Maintenance Planning -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">🔧 Maintenance Planning</h3>
            <div class="space-y-3">
                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $predictiveInsights['maintenance_schedule'] ?? 'No prediction available' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Risk Assessment -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">⚠️ Risk Assessment</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="text-2xl font-bold text-red-600 dark:text-red-400">
                    {{ $predictiveInsights['risk_assessment'] ?? 'Low risk' }}
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Overall Risk Level</p>
            </div>
            
            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                    {{ $predictiveInsights['capacity_planning'] ?? 'Optimal' }}
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Capacity Status</p>
            </div>
            
            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                    {{ $trendAnalysis['anomaly_frequency']['trend'] ?? 'stable' }}
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Anomaly Trend</p>
            </div>
        </div>
    </div>

    <!-- Auto-refresh indicator -->
    <div class="text-center text-sm text-gray-500 dark:text-gray-400">
        <div class="inline-flex items-center space-x-2">
            <div class="w-2 h-2 bg-purple-500 rounded-full animate-pulse"></div>
            <span>AI Analytics auto-refreshing every minute</span>
        </div>
    </div>

    <!-- JavaScript for real-time updates -->
    <script>
        document.addEventListener('livewire:init', () => {
            // Listen for analytics refresh events
            Livewire.on('analytics-refreshed', () => {
                // Show refresh notification
                Livewire.dispatch('show-notification', {
                    title: 'Analytics Updated',
                    body: 'AI analytics data has been refreshed',
                    type: 'success'
                });
            });

            // Listen for anomaly detection events
            Livewire.on('anomaly-detection-completed', (event) => {
                Livewire.dispatch('show-notification', {
                    title: 'AI Analysis Complete',
                    body: event.message + ' Detected ' + event.count + ' anomalies.',
                    type: 'success'
                });
            });

            Livewire.on('anomaly-detection-failed', (event) => {
                Livewire.dispatch('show-notification', {
                    title: 'AI Analysis Failed',
                    body: event.message,
                    type: 'error'
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
