# 🚀 **FULLSTACK DEVELOPER GUIDE**
## Pertamina CCTV Monitoring System

---

## 🎯 **OVERVIEW**

Selamat datang di level **Fullstack Developer**! Sistem ini telah diupgrade dengan fitur-fitur enterprise-grade yang akan membuat Anda siap untuk production deployment dan scaling.

---

## 🏗️ **ARCHITECTURE OVERVIEW**

### **System Architecture**
```
┌─────────────────────────────────────────────────────────────┐
│                    FRONTEND LAYER                           │
├─────────────────────────────────────────────────────────────┤
│  • Livewire Components (Real-time UI)                      │
│  • Alpine.js (Interactive Features)                        │
│  • TailwindCSS v4 (Responsive Design)                      │
│  • Dark Mode Support                                        │
└─────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────┐
│                   APPLICATION LAYER                         │
├─────────────────────────────────────────────────────────────┤
│  • Laravel 12 (Backend Framework)                          │
│  • Livewire 3.x (Real-time Components)                     │
│  • Service Layer (Business Logic)                          │
│  • API Controllers (RESTful Endpoints)                     │
└─────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────┐
│                    SERVICE LAYER                            │
├─────────────────────────────────────────────────────────────┤
│  • NotificationService (Real-time Notifications)           │
│  • MessageService (Real-time Chat)                         │
│  • CctvMonitoringService (Status Monitoring)               │
│  • ExportService (Excel Generation)                        │
└─────────────────────────────────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────┐
│                    INFRASTRUCTURE LAYER                     │
├─────────────────────────────────────────────────────────────┤
│  • FFmpeg (RTSP to HLS Streaming)                          │
│  • Pusher (Real-time Communication)                        │
│  • SQLite (Database)                                        │
│  • Redis (Caching - Optional)                              │
└─────────────────────────────────────────────────────────────┘
```

---

## 🔧 **CORE SERVICES IMPLEMENTATION**

### **1. NotificationService**
**File:** `app/Services/NotificationService.php`

**Features:**
- Real-time notifications via Pusher
- Multi-channel delivery (User, Admin, All)
- CCTV status change notifications
- System maintenance alerts

**Usage:**
```php
// Inject service
public function __construct(NotificationService $notificationService) {
    $this->notificationService = $notificationService;
}

// Send notification
$this->notificationService->sendToUser($userId, 'Alert', 'CCTV offline', 'warning');
```

### **2. MessageService**
**File:** `app/Services/MessageService.php`

**Features:**
- Real-time chat between Users and Admins
- Conversation management
- Unread message tracking
- Message history

**Usage:**
```php
// Send message from user to admin
$this->messageService->sendUserMessage($userId, $adminId, 'Hello Admin!');

// Get conversation
$messages = $this->messageService->getConversation($userId, $adminId);
```

### **3. CctvMonitoringService**
**File:** `app/Services/CctvMonitoringService.php`

**Features:**
- Automatic CCTV status monitoring
- Performance metrics calculation
- Health scoring system
- Maintenance management

**Usage:**
```php
// Check all CCTV statuses
$statusChanges = $this->monitoringService->checkCctvStatus();

// Get performance metrics
$metrics = $this->monitoringService->getPerformanceMetrics();
```

---

## 📡 **REAL-TIME FEATURES**

### **Pusher Integration**
**Configuration:** `.env`
```env
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=your_cluster
```

**Channels:**
- `user.{userId}` - User-specific notifications
- `admin.{adminId}` - Admin-specific notifications
- `cctv-status` - Global CCTV status updates

### **Livewire Real-time Updates**
**Component:** `app/Livewire/Admin/RealTimeDashboard.php`

**Features:**
- Auto-refresh every 30 seconds
- Real-time status updates
- Live notification counters
- Performance metrics

---

## 🎮 **COMMAND LINE TOOLS**

### **CCTV Streaming Commands**
```bash
# Start all CCTV streams
php artisan cctv:stream --all

# Start specific CCTV
php artisan cctv:stream --cctv_id=1

# Stop all streams
php artisan cctv:stop --all

# Stop specific CCTV
php artisan cctv:stop --cctv_id=1
```

### **Monitoring Commands**
```bash
# Continuous monitoring (5-minute intervals)
php artisan cctv:monitor --interval=5

# One-time status check
php artisan cctv:monitor --once
```

---

## 🌐 **API ENDPOINTS**

### **RESTful API Structure**
```
GET    /api/cctv                    # List all CCTVs
POST   /api/cctv                    # Create new CCTV
GET    /api/cctv/{id}              # Get specific CCTV
PUT    /api/cctv/{id}              # Update CCTV
DELETE /api/cctv/{id}              # Delete CCTV

# Status endpoints
GET    /api/cctv/status/summary    # CCTV status summary
GET    /api/cctv/status/buildings  # Building status
GET    /api/cctv/status/rooms      # Room status
GET    /api/cctv/alerts            # CCTV alerts
GET    /api/cctv/performance       # Performance metrics

# Maintenance endpoints
POST   /api/cctv/{id}/maintenance/start  # Start maintenance
POST   /api/cctv/{id}/maintenance/end    # End maintenance
```

### **API Authentication**
**Sanctum Token Authentication:**
```bash
# Get token
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "admin@pertamina.com", "password": "admin123"}'

# Use token
curl -H "Authorization: Bearer {token}" \
  http://localhost:8000/api/cctv
```

---

## 🎨 **FRONTEND COMPONENTS**

### **Theme Switcher**
**Component:** `app/Livewire/Components/ThemeSwitcher.php`

**Features:**
- Light/Dark/System modes
- Persistent theme storage
- CSS custom properties
- Alpine.js integration

### **Real-time Dashboard**
**Component:** `app/Livewire/Admin/RealTimeDashboard.php`

**Features:**
- Live status updates
- Performance metrics
- Building health scores
- Recent alerts
- Auto-refresh

---

## 🔒 **SECURITY FEATURES**

### **Authentication & Authorization**
- Multi-guard authentication (Admin/User)
- Role-based access control
- API token authentication
- Rate limiting

### **Data Protection**
- CSRF protection
- SQL injection prevention
- XSS protection
- Input validation

---

## 📊 **PERFORMANCE OPTIMIZATION**

### **Caching Strategy**
```php
// Cache CCTV status for 5 minutes
Cache::remember('cctv_status_summary', 300, function () {
    return $this->monitoringService->getStatusSummary();
});
```

### **Database Optimization**
- Eager loading relationships
- Database indexing
- Query optimization
- Connection pooling

### **Asset Optimization**
```bash
# Build production assets
npm run build

# Watch for changes (development)
npm run dev
```

---

## 🚀 **DEPLOYMENT STRATEGY**

### **Production Environment**
```bash
# Install dependencies
composer install --optimize-autoloader --no-dev
npm run build

# Set permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Clear caches
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### **Environment Configuration**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pertamina_cctv
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Broadcasting
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret

# Queue
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

---

## 🔍 **MONITORING & LOGGING**

### **Application Logs**
```bash
# View logs
tail -f storage/logs/laravel.log

# Monitor errors
tail -f storage/logs/laravel-$(date +%Y-%m-%d).log
```

### **Performance Monitoring**
```bash
# Check CCTV status
php artisan cctv:monitor --once

# View performance metrics
curl http://localhost:8000/api/cctv/performance
```

---

## 🧪 **TESTING STRATEGY**

### **Unit Tests**
```bash
# Run tests
php artisan test

# Run specific test
php artisan test --filter=CctvMonitoringServiceTest
```

### **Feature Tests**
```bash
# Test API endpoints
php artisan test --testsuite=Feature

# Test Livewire components
php artisan test --testsuite=Unit
```

---

## 📈 **SCALING CONSIDERATIONS**

### **Horizontal Scaling**
- Load balancer configuration
- Database replication
- Redis clustering
- Queue workers

### **Vertical Scaling**
- Server resource optimization
- Database query optimization
- Asset delivery optimization
- Caching strategies

---

## 🛠️ **DEVELOPMENT WORKFLOW**

### **Git Workflow**
```bash
# Feature branch
git checkout -b feature/real-time-monitoring

# Development
git add .
git commit -m "Add real-time monitoring features"

# Merge to main
git checkout main
git merge feature/real-time-monitoring
```

### **Code Quality**
```bash
# PHP CS Fixer
./vendor/bin/php-cs-fixer fix

# PHPStan
./vendor/bin/phpstan analyse

# Laravel Pint
./vendor/bin/pint
```

---

## 🎯 **NEXT DEVELOPMENT PHASES**

### **Phase 3: Advanced Features**
- [ ] **Machine Learning Integration** - Anomaly detection
- [ ] **Advanced Analytics** - Predictive maintenance
- [ ] **Mobile App** - React Native/Flutter
- [ ] **IoT Integration** - Sensor data collection

### **Phase 4: Enterprise Features**
- [ ] **Multi-tenant Architecture** - Multiple locations
- [ ] **Advanced Reporting** - Custom dashboards
- [ ] **Workflow Automation** - Incident management
- [ ] **Integration APIs** - Third-party systems

---

## 📚 **LEARNING RESOURCES**

### **Laravel 12**
- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Livewire](https://livewire.laravel.com/)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)

### **Frontend Technologies**
- [TailwindCSS v4](https://tailwindcss.com/)
- [Alpine.js](https://alpinejs.dev/)
- [Livewire 3](https://livewire.laravel.com/docs/v3)

### **Real-time Technologies**
- [Pusher](https://pusher.com/docs)
- [WebSockets](https://developer.mozilla.org/en-US/docs/Web/API/WebSockets_API)
- [Server-Sent Events](https://developer.mozilla.org/en-US/docs/Web/API/Server-sent_events)

---

## 🎉 **ACHIEVEMENT UNLOCKED!**

**🏆 FULLSTACK DEVELOPER LEVEL COMPLETED!**

**✅ Skills Acquired:**
- Real-time application development
- Service-oriented architecture
- API design and implementation
- Advanced Livewire components
- Performance optimization
- Security implementation
- Production deployment
- Monitoring and logging

**🚀 Ready for:**
- Enterprise application development
- Team leadership
- System architecture design
- Production deployment
- Performance optimization
- Security auditing

---

*Generated: $(date +'%Y-%m-%d %H:%M:%S')*  
*Level: FULLSTACK DEVELOPER*  
*Next Level: SENIOR DEVELOPER / ARCHITECT* 🚀