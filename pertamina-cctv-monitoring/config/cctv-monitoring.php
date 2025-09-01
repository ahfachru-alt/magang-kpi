<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CCTV Monitoring Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for the CCTV monitoring system
    | including streaming settings, monitoring intervals, and alert thresholds.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Streaming Configuration
    |--------------------------------------------------------------------------
    */
    'streaming' => [
        'enabled' => env('CCTV_STREAMING_ENABLED', true),
        'ffmpeg_path' => env('FFMPEG_PATH', '/usr/bin/ffmpeg'),
        'hls_segment_time' => env('HLS_SEGMENT_TIME', 10),
        'hls_segment_list_size' => env('HLS_SEGMENT_LIST_SIZE', 3),
        'hls_segment_wrap' => env('HLS_SEGMENT_WRAP', 3),
        'output_directory' => env('CCTV_OUTPUT_DIR', 'public/streaming'),
        'max_concurrent_streams' => env('MAX_CONCURRENT_STREAMS', 50),
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring Configuration
    |--------------------------------------------------------------------------
    */
    'monitoring' => [
        'enabled' => env('CCTV_MONITORING_ENABLED', true),
        'check_interval' => env('CCTV_CHECK_INTERVAL', 5), // minutes
        'ping_timeout' => env('CCTV_PING_TIMEOUT', 3), // seconds
        'http_timeout' => env('CCTV_HTTP_TIMEOUT', 5), // seconds
        'max_retries' => env('CCTV_MAX_RETRIES', 3),
        'retry_delay' => env('CCTV_RETRY_DELAY', 60), // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Alert Configuration
    |--------------------------------------------------------------------------
    */
    'alerts' => [
        'enabled' => env('CCTV_ALERTS_ENABLED', true),
        'offline_threshold' => env('CCTV_OFFLINE_THRESHOLD', 60), // minutes
        'critical_threshold' => env('CCTV_CRITICAL_THRESHOLD', 1440), // minutes (24 hours)
        'high_threshold' => env('CCTV_HIGH_THRESHOLD', 720), // minutes (12 hours)
        'notification_channels' => [
            'database' => true,
            'email' => env('CCTV_EMAIL_ALERTS', false),
            'slack' => env('CCTV_SLACK_ALERTS', false),
            'webhook' => env('CCTV_WEBHOOK_ALERTS', false),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Configuration
    |--------------------------------------------------------------------------
    */
    'performance' => [
        'cache_ttl' => env('CCTV_CACHE_TTL', 300), // seconds
        'max_response_time' => env('CCTV_MAX_RESPONSE_TIME', 1000), // milliseconds
        'uptime_calculation_hours' => env('CCTV_UPTIME_CALCULATION_HOURS', 24),
        'health_score_weights' => [
            'uptime' => 0.4,
            'response_time' => 0.3,
            'maintenance_frequency' => 0.2,
            'alert_frequency' => 0.1,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Configuration
    |--------------------------------------------------------------------------
    */
    'maintenance' => [
        'auto_maintenance_mode' => env('CCTV_AUTO_MAINTENANCE', false),
        'maintenance_duration_limit' => env('CCTV_MAINTENANCE_DURATION_LIMIT', 168), // hours (1 week)
        'maintenance_notification_interval' => env('CCTV_MAINTENANCE_NOTIFICATION_INTERVAL', 24), // hours
        'required_reason' => env('CCTV_MAINTENANCE_REASON_REQUIRED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    */
    'api' => [
        'enabled' => env('CCTV_API_ENABLED', true),
        'rate_limit' => env('CCTV_API_RATE_LIMIT', 60), // requests per minute
        'max_results' => env('CCTV_API_MAX_RESULTS', 1000),
        'public_endpoints' => [
            'status_summary' => true,
            'alerts' => true,
            'building_status' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Real-time Configuration
    |--------------------------------------------------------------------------
    */
    'realtime' => [
        'enabled' => env('CCTV_REALTIME_ENABLED', true),
        'update_interval' => env('CCTV_UPDATE_INTERVAL', 30), // seconds
        'websocket_enabled' => env('CCTV_WEBSOCKET_ENABLED', false),
        'pusher_enabled' => env('CCTV_PUSHER_ENABLED', true),
        'max_connections' => env('CCTV_MAX_CONNECTIONS', 100),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    */
    'security' => [
        'ip_whitelist' => env('CCTV_IP_WHITELIST', ''),
        'authentication_required' => env('CCTV_AUTH_REQUIRED', true),
        'api_key_required' => env('CCTV_API_KEY_REQUIRED', false),
        'max_failed_attempts' => env('CCTV_MAX_FAILED_ATTEMPTS', 5),
        'lockout_duration' => env('CCTV_LOCKOUT_DURATION', 900), // seconds (15 minutes)
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    */
    'logging' => [
        'enabled' => env('CCTV_LOGGING_ENABLED', true),
        'level' => env('CCTV_LOG_LEVEL', 'info'),
        'channels' => [
            'daily' => true,
            'slack' => env('CCTV_SLACK_LOGGING', false),
            'database' => env('CCTV_DB_LOGGING', true),
        ],
        'retention_days' => env('CCTV_LOG_RETENTION_DAYS', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Backup Configuration
    |--------------------------------------------------------------------------
    */
    'backup' => [
        'enabled' => env('CCTV_BACKUP_ENABLED', true),
        'schedule' => env('CCTV_BACKUP_SCHEDULE', 'daily'),
        'retention_days' => env('CCTV_BACKUP_RETENTION_DAYS', 7),
        'include_streams' => env('CCTV_BACKUP_STREAMS', false),
        'compression' => env('CCTV_BACKUP_COMPRESSION', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Integration Configuration
    |--------------------------------------------------------------------------
    */
    'integrations' => [
        'email' => [
            'enabled' => env('CCTV_EMAIL_INTEGRATION', false),
            'provider' => env('CCTV_EMAIL_PROVIDER', 'smtp'),
            'from_address' => env('CCTV_EMAIL_FROM', 'noreply@pertamina.com'),
            'to_addresses' => env('CCTV_EMAIL_TO', ''),
        ],
        'slack' => [
            'enabled' => env('CCTV_SLACK_INTEGRATION', false),
            'webhook_url' => env('CCTV_SLACK_WEBHOOK', ''),
            'channel' => env('CCTV_SLACK_CHANNEL', '#cctv-alerts'),
        ],
        'webhook' => [
            'enabled' => env('CCTV_WEBHOOK_INTEGRATION', false),
            'url' => env('CCTV_WEBHOOK_URL', ''),
            'secret' => env('CCTV_WEBHOOK_SECRET', ''),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Development Configuration
    |--------------------------------------------------------------------------
    */
    'development' => [
        'debug_mode' => env('CCTV_DEBUG_MODE', false),
        'mock_streams' => env('CCTV_MOCK_STREAMS', false),
        'fake_data' => env('CCTV_FAKE_DATA', false),
        'performance_monitoring' => env('CCTV_PERFORMANCE_MONITORING', false),
    ],
];