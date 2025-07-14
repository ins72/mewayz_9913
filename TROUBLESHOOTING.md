# Mewayz Platform - Troubleshooting Guide

**Professional Support Documentation for Zeph Technologies' Flagship Platform**

*Ensuring seamless operations through comprehensive problem resolution*

---

## 🛠️ Support Philosophy

At Zeph Technologies, we believe that exceptional software requires exceptional support. This troubleshooting guide embodies our commitment to seamless operations, providing comprehensive solutions for all aspects of the Mewayz platform.

**Our Support Principles:**
- **Proactive**: Anticipate issues before they impact users
- **Comprehensive**: Cover all scenarios with detailed solutions  
- **Seamless**: Provide smooth resolution paths with minimal disruption
- **Professional**: Maintain enterprise-grade support standards

---

## Common Issues and Solutions

### 1. Backend Issues

#### Laravel Not Starting
**Symptoms:** Backend service fails to start or returns 500 errors

**Solutions:**
```bash
# Check supervisor logs
tail -n 100 /var/log/supervisor/backend.*.log

# Check Laravel logs
tail -n 100 storage/logs/laravel.log

# Restart backend service
sudo supervisorctl restart backend

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

#### Database Connection Issues
**Symptoms:** Database connection refused or timeout errors

**Solutions:**
```bash
# Check database configuration
php artisan config:show database

# Test database connection
php artisan db:show

# Run migrations
php artisan migrate

# Check database service
sudo systemctl status mysql
```

#### CORS Issues
**Symptoms:** Frontend cannot access backend API, CORS errors in browser

**Solutions:**
```php
// config/cors.php
'allowed_origins' => [
    'http://localhost:3000',
    'https://your-production-domain.com',
],
'supports_credentials' => true,
```

```bash
# Restart after CORS changes
sudo supervisorctl restart backend
```

#### API Authentication Issues
**Symptoms:** 401 Unauthorized errors, token validation failures

**Solutions:**
```bash
# Check Sanctum configuration
php artisan config:show sanctum

# Clear auth cache
php artisan auth:clear-resets

# Check middleware configuration
php artisan route:list --path=api
```

### 2. Frontend Issues

#### Flutter App Not Loading
**Symptoms:** Flutter app shows blank screen or loading indefinitely

**Solutions:**
```bash
# Check Flutter build
cd flutter_app
flutter build web

# Check web files
ls -la web/

# Check service worker
curl -I http://localhost:3000/sw.js

# Clear browser cache
# Use browser dev tools > Application > Storage > Clear storage
```

#### PWA Issues
**Symptoms:** PWA features not working, no install prompt

**Solutions:**
```bash
# Check service worker registration
# Open browser dev tools > Application > Service Workers

# Check manifest
curl -I http://localhost:3000/manifest.json

# Verify PWA files location
ls -la flutter_app/web/
ls -la public/
```

#### API Connection Issues
**Symptoms:** Frontend cannot connect to backend API

**Solutions:**
```javascript
// Check API base URL in Flutter
// lib/services/api_service.dart
const String baseUrl = '/api'; // Should be relative

// Check browser network tab for failed requests
// Verify CORS headers in response
```

### 3. Authentication Issues

#### OAuth Not Working
**Symptoms:** OAuth redirects fail, provider authentication errors

**Solutions:**
```bash
# Check OAuth configuration
php artisan config:show services

# Verify OAuth credentials in .env
GOOGLE_CLIENT_ID=your-client-id
GOOGLE_CLIENT_SECRET=your-client-secret

# Check OAuth routes
php artisan route:list --path=oauth
```

#### 2FA Issues
**Symptoms:** 2FA codes not working, QR code not generating

**Solutions:**
```bash
# Check 2FA package
composer show pragmarx/google2fa-laravel

# Test 2FA generation
php artisan tinker
>>> app('pragmarx.google2fa')->generateSecretKey()

# Check 2FA routes
php artisan route:list --path=2fa
```

#### Session Issues
**Symptoms:** User sessions not persisting, frequent logouts

**Solutions:**
```bash
# Check session configuration
php artisan config:show session

# Clear sessions
php artisan session:clear

# Check session storage
ls -la storage/framework/sessions/
```

### 4. Service Worker Issues

#### Service Worker Not Registering
**Symptoms:** PWA offline features not working

**Solutions:**
```bash
# Check service worker file location
ls -la flutter_app/web/sw.js
ls -la public/sw.js

# Copy service worker to public root
cp flutter_app/web/sw.js public/sw.js
cp flutter_app/web/offline.html public/offline.html

# Check service worker registration
# Browser dev tools > Application > Service Workers
```

#### Offline Page Not Loading
**Symptoms:** Offline page returns 404

**Solutions:**
```bash
# Verify offline page location
ls -la public/offline.html

# Check service worker cache
# Browser dev tools > Application > Cache > Cache Storage
```

### 5. Database Issues

#### Migration Errors
**Symptoms:** Migration fails with schema errors

**Solutions:**
```bash
# Check migration status
php artisan migrate:status

# Reset migrations (development only)
php artisan migrate:fresh

# Run specific migration
php artisan migrate --path=database/migrations/specific_migration.php

# Check database schema
php artisan db:table users
```

#### Model Relationship Issues
**Symptoms:** Eloquent relationships not working

**Solutions:**
```php
// Check model relationships
// In tinker:
php artisan tinker
>>> $user = App\Models\User::first()
>>> $user->workspaces
>>> $user->bioSites
```

### 6. Performance Issues

#### Slow API Responses
**Symptoms:** API responses taking >1 second

**Solutions:**
```bash
# Enable query logging
# In AppServiceProvider:
DB::listen(function ($query) {
    Log::info($query->sql);
});

# Check slow queries
tail -n 100 storage/logs/laravel.log | grep -i query

# Optimize database
php artisan optimize:clear
```

#### Frontend Loading Issues
**Symptoms:** Frontend takes >5 seconds to load

**Solutions:**
```bash
# Build optimized assets
npm run build

# Check asset sizes
ls -lah public/build/

# Enable compression
# Check vite.config.js for compression settings
```

### 7. Production Deployment Issues

#### Environment Configuration
**Symptoms:** App not working in production

**Solutions:**
```bash
# Check environment
php artisan config:show app

# Set production environment
APP_ENV=production
APP_DEBUG=false

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### SSL/HTTPS Issues
**Symptoms:** Mixed content warnings, SSL errors

**Solutions:**
```bash
# Force HTTPS
APP_URL=https://your-domain.com

# Check SSL certificate
openssl s_client -connect your-domain.com:443

# Configure trusted proxies
# In app/Http/Middleware/TrustProxies.php
```

### 8. Kubernetes/Container Issues

#### Service Not Accessible
**Symptoms:** Services return 404 or connection refused

**Solutions:**
```bash
# Check service status
sudo supervisorctl status

# Check port binding
netstat -tulpn | grep :8001
netstat -tulpn | grep :3000

# Check ingress configuration
# Verify /api prefix routing
```

#### Resource Limits
**Symptoms:** Services crashing or running out of memory

**Solutions:**
```bash
# Check resource usage
top
htop

# Check container limits
docker stats (if using Docker)

# Increase memory limits in supervisor configuration
```

## Debugging Commands

### Backend Debugging
```bash
# Check Laravel environment
php artisan about

# Check database connection
php artisan db:show

# Check routes
php artisan route:list

# Check configuration
php artisan config:show

# Check logs
tail -f storage/logs/laravel.log

# Interactive debugging
php artisan tinker
```

### Frontend Debugging
```bash
# Check Flutter configuration
flutter doctor

# Check web build
flutter build web --verbose

# Check assets
ls -la flutter_app/build/web/

# Check service worker
curl -I http://localhost:3000/sw.js
```

### API Testing
```bash
# Test health endpoint
curl -X GET http://localhost:8001/api/health

# Test authentication
curl -X POST http://localhost:8001/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@mewayz.com","password":"password"}'

# Test authenticated endpoint
curl -X GET http://localhost:8001/api/workspaces \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Database Debugging
```bash
# Check database tables
php artisan db:table users

# Check database size
php artisan db:show --database=mysql

# Export database
mysqldump -u root -p mewayz > backup.sql

# Check migration status
php artisan migrate:status
```

## Performance Monitoring

### Backend Monitoring
```bash
# Check API response times
curl -w "@curl-format.txt" -o /dev/null -s http://localhost:8001/api/health

# Monitor database queries
# Add to AppServiceProvider boot method:
DB::listen(function ($query) {
    if ($query->time > 100) {
        Log::warning('Slow query: ' . $query->sql);
    }
});
```

### Frontend Monitoring
```bash
# Check bundle sizes
npm run build -- --analyze

# Monitor Core Web Vitals
# Use browser dev tools > Lighthouse

# Check PWA score
# Use browser dev tools > Lighthouse > Progressive Web App
```

## Emergency Procedures

### Service Recovery
```bash
# Restart all services
sudo supervisorctl restart all

# Emergency database recovery
php artisan migrate:refresh --seed

# Clear all caches
php artisan optimize:clear

# Reset file permissions
sudo chown -R www-data:www-data storage/
sudo chmod -R 755 storage/
```

### Rollback Procedures
```bash
# Rollback migration
php artisan migrate:rollback

# Rollback to specific migration
php artisan migrate:rollback --step=5

# Restore database from backup
mysql -u root -p mewayz < backup.sql
```

## Getting Help

### Log Files
- Laravel logs: `storage/logs/laravel.log`
- Supervisor logs: `/var/log/supervisor/backend.*.log`
- Nginx logs: `/var/log/nginx/error.log`
- System logs: `/var/log/syslog`

### Useful Commands
```bash
# Check system status
sudo systemctl status nginx
sudo systemctl status mysql
sudo supervisorctl status

# Monitor logs in real-time
tail -f storage/logs/laravel.log
tail -f /var/log/supervisor/backend.*.log

# Check disk space
df -h

# Check memory usage
free -h
```

### Contact Information
- **Technical Support**: support@mewayz.com
- **Documentation**: [docs.mewayz.com](https://docs.mewayz.com)
- **Issue Tracker**: [github.com/mewayz/mewayz/issues](https://github.com/mewayz/mewayz/issues)