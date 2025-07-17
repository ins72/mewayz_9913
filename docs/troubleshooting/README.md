# Mewayz Platform v2 - Troubleshooting Guide

*Last Updated: January 17, 2025*

## 🔧 **TROUBLESHOOTING OVERVIEW**

This comprehensive troubleshooting guide helps you diagnose and resolve common issues with the **Mewayz Platform v2** built on **Laravel 11 + MySQL**.

---

## 🚨 **COMMON ISSUES & SOLUTIONS**

### 1. Authentication Issues

#### Problem: "Unauthorized" Error (401)
```json
{
  "success": false,
  "error": "Unauthorized"
}
```

**Possible Causes:**
- Invalid or expired token
- Missing Authorization header
- CustomSanctumAuth middleware issues

**Solutions:**
```bash
# Check token validity
curl -X GET https://api.mewayz.com/api/user \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"

# Generate new token
curl -X POST https://api.mewayz.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "user@example.com", "password": "password"}'
```

#### Problem: Login Fails with Correct Credentials
**Check:**
1. Database connection
2. User exists in database
3. Password is correctly hashed
4. Email verification status

**Debug Commands:**
```bash
# Check user in database
php artisan tinker
>>> User::where('email', 'user@example.com')->first()

# Reset password
php artisan tinker
>>> $user = User::where('email', 'user@example.com')->first();
>>> $user->password = Hash::make('newpassword');
>>> $user->save();
```

### 2. Database Connection Issues

#### Problem: "Database Connection Failed"
**Check:**
1. MySQL service is running
2. Database credentials in `.env`
3. Database exists
4. User has proper permissions

**Solutions:**
```bash
# Check MySQL service
sudo systemctl status mysql

# Test connection
mysql -u mewayz_user -p mewayz_v2

# Check Laravel connection
php artisan migrate:status
```

#### Problem: Migration Errors
**Common Issues:**
- Foreign key constraints
- Duplicate column names
- Missing database

**Solutions:**
```bash
# Reset migrations
php artisan migrate:reset
php artisan migrate

# Check migration status
php artisan migrate:status

# Rollback specific migration
php artisan migrate:rollback --step=1
```

### 3. API Performance Issues

#### Problem: Slow Response Times (>1 second)
**Check:**
1. Database queries (N+1 problem)
2. Missing indexes
3. Large dataset without pagination
4. Unoptimized relationships

**Solutions:**
```bash
# Enable query logging
php artisan tinker
>>> DB::enableQueryLog();
>>> // Run your query
>>> DB::getQueryLog();

# Optimize database
php artisan db:optimize

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### Problem: Memory Limit Exceeded
**Solutions:**
```bash
# Increase memory limit in php.ini
memory_limit = 512M

# Or temporarily in code
ini_set('memory_limit', '512M');

# Use chunking for large datasets
User::chunk(100, function ($users) {
    foreach ($users as $user) {
        // Process user
    }
});
```

### 4. File Upload Issues

#### Problem: File Upload Fails
**Check:**
1. File size limits
2. Upload directory permissions
3. Disk space
4. PHP configuration

**Solutions:**
```bash
# Check PHP limits
php -i | grep -E "(upload_max_filesize|post_max_size|max_execution_time)"

# Set proper permissions
sudo chown -R www-data:www-data storage/
sudo chmod -R 755 storage/

# Check disk space
df -h
```

#### Problem: Images Not Displaying
**Check:**
1. Storage link exists
2. File permissions
3. URL configuration
4. CDN issues

**Solutions:**
```bash
# Create storage link
php artisan storage:link

# Check storage structure
ls -la storage/app/public/

# Check URL in browser
curl -I https://your-domain.com/storage/image.jpg
```

### 5. Queue Processing Issues

#### Problem: Jobs Not Processing
**Check:**
1. Queue worker running
2. Queue configuration
3. Failed jobs table
4. Redis connection

**Solutions:**
```bash
# Start queue worker
php artisan queue:work

# Check queue status
php artisan queue:monitor

# Clear failed jobs
php artisan queue:clear

# Restart queue workers
php artisan queue:restart
```

#### Problem: Redis Connection Failed
**Solutions:**
```bash
# Check Redis service
sudo systemctl status redis

# Test Redis connection
redis-cli ping

# Check Laravel Redis config
php artisan tinker
>>> Redis::connection()->ping()
```

### 6. Asset Compilation Issues

#### Problem: Vite Build Fails
**Check:**
1. Node.js version
2. Package dependencies
3. Build configuration
4. Memory issues

**Solutions:**
```bash
# Check Node.js version
node --version
npm --version

# Clear cache and reinstall
rm -rf node_modules package-lock.json
npm install

# Build assets
npm run build

# Development mode
npm run dev
```

#### Problem: CSS/JS Not Loading
**Solutions:**
```bash
# Check asset manifest
cat public/build/manifest.json

# Verify file exists
ls -la public/build/

# Check browser console for errors
# Clear browser cache
```

### 7. Email Delivery Issues

#### Problem: Emails Not Sending
**Check:**
1. SMTP configuration
2. Email service credentials
3. Mail queue processing
4. Firewall restrictions

**Solutions:**
```bash
# Test email configuration
php artisan tinker
>>> Mail::raw('Test email', function ($message) {
    $message->to('test@example.com')->subject('Test');
});

# Check mail queue
php artisan queue:work --queue=mail

# Check logs
tail -f storage/logs/laravel.log
```

### 8. Social Media Integration Issues

#### Problem: Instagram API Errors
**Common Issues:**
- Expired access tokens
- API rate limits
- Invalid permissions
- Deprecated endpoints

**Solutions:**
```bash
# Check token validity
curl -X GET "https://graph.instagram.com/me?access_token=YOUR_TOKEN"

# Refresh token
curl -X GET "https://graph.instagram.com/refresh_access_token?grant_type=ig_refresh_token&access_token=YOUR_TOKEN"

# Check rate limits
curl -X GET "https://graph.instagram.com/me?access_token=YOUR_TOKEN" -I
```

### 9. Payment Processing Issues

#### Problem: Stripe Payment Fails
**Check:**
1. API keys (test vs live)
2. Webhook endpoints
3. Product/price IDs
4. Customer creation

**Solutions:**
```bash
# Test Stripe connection
php artisan tinker
>>> \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
>>> \Stripe\Account::retrieve();

# Check webhook events
# Visit Stripe Dashboard → Webhooks → Events

# Verify webhook endpoint
curl -X POST https://your-domain.com/stripe/webhook \
  -H "Content-Type: application/json" \
  -d '{"type": "payment_intent.succeeded"}'
```

### 10. PWA Installation Issues

#### Problem: "Add to Home Screen" Not Appearing
**Check:**
1. HTTPS requirement
2. Service worker registration
3. Web app manifest
4. Browser compatibility

**Solutions:**
```bash
# Check service worker
curl -X GET https://your-domain.com/sw.js

# Validate manifest
curl -X GET https://your-domain.com/manifest.json

# Check browser console for errors
# Verify PWA criteria in Chrome DevTools
```

---

## 🔍 **DEBUGGING TECHNIQUES**

### 1. Laravel Debugging
```php
// Enable debug mode
APP_DEBUG=true

// Log custom messages
Log::info('Debug message', ['data' => $data]);

// Dump and die
dd($variable);

// Debug queries
DB::listen(function ($query) {
    Log::info($query->sql, $query->bindings);
});
```

### 2. API Debugging
```bash
# Test API endpoints
curl -X GET https://api.mewayz.com/api/health -v

# Check response headers
curl -I https://api.mewayz.com/api/user

# Debug with verbose output
curl -X POST https://api.mewayz.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "user@example.com", "password": "password"}' \
  -v
```

### 3. Database Debugging
```sql
-- Check table structure
DESCRIBE users;

-- Check indexes
SHOW INDEX FROM users;

-- Check foreign keys
SELECT * FROM information_schema.KEY_COLUMN_USAGE 
WHERE TABLE_NAME = 'workspaces';

-- Check query performance
EXPLAIN SELECT * FROM users WHERE email = 'user@example.com';
```

### 4. Frontend Debugging
```javascript
// Browser console debugging
console.log('Debug data:', data);

// Check network requests
// Open DevTools → Network tab

// Check localStorage
localStorage.getItem('auth_token');

// Check service worker
navigator.serviceWorker.getRegistrations().then(registrations => {
    console.log('SW registrations:', registrations);
});
```

---

## 📊 **PERFORMANCE MONITORING**

### 1. Application Monitoring
```php
// Monitor slow queries
DB::listen(function ($query) {
    if ($query->time > 1000) {
        Log::warning('Slow query detected', [
            'sql' => $query->sql,
            'time' => $query->time,
            'bindings' => $query->bindings
        ]);
    }
});

// Monitor memory usage
Log::info('Memory usage: ' . memory_get_peak_usage(true));
```

### 2. Server Monitoring
```bash
# Check system resources
htop
iostat
free -h

# Monitor MySQL performance
mysqladmin -u root -p processlist
mysqladmin -u root -p status

# Check disk usage
df -h
du -sh /var/www/mewayz/storage/logs/
```

### 3. Redis Monitoring
```bash
# Redis info
redis-cli info

# Monitor Redis commands
redis-cli monitor

# Check Redis memory usage
redis-cli info memory
```

---

## 🔧 **SYSTEM MAINTENANCE**

### 1. Log Management
```bash
# Clear old logs
sudo logrotate -f /etc/logrotate.d/laravel

# Monitor log size
du -sh storage/logs/

# Clear Laravel logs
> storage/logs/laravel.log

# Archive logs
tar -czf logs_backup_$(date +%Y%m%d).tar.gz storage/logs/
```

### 2. Database Maintenance
```sql
-- Optimize tables
OPTIMIZE TABLE users, workspaces, social_media_posts;

-- Check table sizes
SELECT 
    table_name AS "Table",
    round(((data_length + index_length) / 1024 / 1024), 2) AS "Size (MB)"
FROM information_schema.tables 
WHERE table_schema = "mewayz_v2"
ORDER BY (data_length + index_length) DESC;

-- Update table statistics
ANALYZE TABLE users, workspaces;
```

### 3. Cache Management
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Clear specific cache
php artisan cache:forget cache_key

# Warm up cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🆘 **EMERGENCY PROCEDURES**

### 1. Site Down Recovery
```bash
# Check services
sudo systemctl status nginx
sudo systemctl status mysql
sudo systemctl status php8.2-fpm

# Restart services
sudo systemctl restart nginx
sudo systemctl restart mysql
sudo systemctl restart php8.2-fpm

# Check logs
tail -f /var/log/nginx/error.log
tail -f storage/logs/laravel.log
```

### 2. Database Recovery
```bash
# Restore from backup
mysql -u root -p mewayz_v2 < backup_file.sql

# Check database integrity
mysql -u root -p -e "CHECK TABLE users, workspaces;"

# Repair tables if needed
mysql -u root -p -e "REPAIR TABLE users;"
```

### 3. Roll Back Deployment
```bash
# Revert to previous version
git checkout HEAD~1

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rollback migrations if needed
php artisan migrate:rollback
```

---

## 📞 **GETTING HELP**

### 1. Internal Resources
- **Log Files**: Check `storage/logs/laravel.log`
- **Database**: Query `activity_logs` table
- **System Logs**: Check `/var/log/nginx/` and `/var/log/mysql/`

### 2. External Support
- **Technical Support**: tech-support@mewayz.com
- **Emergency Support**: emergency@mewayz.com
- **Documentation**: https://docs.mewayz.com
- **Status Page**: https://status.mewayz.com

### 3. Community Resources
- **Developer Forum**: https://forum.mewayz.com
- **GitHub Issues**: https://github.com/mewayz/platform/issues
- **Stack Overflow**: Tag with `mewayz-platform`

---

## 📋 **TROUBLESHOOTING CHECKLIST**

### Before Contacting Support
- [ ] Check system requirements
- [ ] Verify environment configuration
- [ ] Review recent changes
- [ ] Check log files
- [ ] Try basic solutions first
- [ ] Document error messages
- [ ] Note steps to reproduce

### Information to Provide
- **Error Message**: Exact error text
- **Steps to Reproduce**: Detailed steps
- **Environment**: OS, PHP version, Browser
- **Log Files**: Relevant log entries
- **Recent Changes**: Any recent modifications
- **User Impact**: How many users affected

---

*Last Updated: January 17, 2025*
*Platform Version: v2.0.0*
*Framework: Laravel 11 + MySQL*
*Status: Production-Ready*