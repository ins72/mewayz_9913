# Mewayz Platform - Troubleshooting Guide

This comprehensive troubleshooting guide covers common issues, solutions, and debugging techniques for the Mewayz platform.

## 🔍 Quick Diagnostics

### System Health Check
```bash
# Check application health
curl -s http://localhost:8001/api/health | jq

# Check database connection
php artisan tinker
>>> DB::connection()->getPdo();

# Check Redis connection
redis-cli ping

# Check Laravel configuration
php artisan config:show
```

### Common Status Checks
```bash
# Check Laravel services
php artisan about

# Check queue status
php artisan queue:monitor

# Check cache status
php artisan cache:clear
php artisan config:clear

# Check storage permissions
ls -la storage/
ls -la bootstrap/cache/
```

## 🚨 Common Issues & Solutions

### 1. Installation Issues

#### PHP Extension Missing
**Error**: `PHP extension [ext] is not installed`

**Solution**:
```bash
# For Ubuntu/Debian
sudo apt-get install php8.2-ext

# For CentOS/RHEL
sudo yum install php82-php-ext

# Restart web server
sudo systemctl restart apache2
# or
sudo systemctl restart nginx
```

#### Composer Install Failures
**Error**: `Your requirements could not be resolved to an installable set of packages`

**Solutions**:
```bash
# Clear composer cache
composer clear-cache

# Update composer
composer self-update

# Install with ignore platform requirements (development only)
composer install --ignore-platform-reqs

# Force specific PHP version
composer config platform.php 8.2.0
```

#### Node.js/NPM Issues
**Error**: `npm install` fails or `npm run build` fails

**Solutions**:
```bash
# Clear npm cache
npm cache clean --force

# Delete node_modules and reinstall
rm -rf node_modules package-lock.json
npm install

# Use different Node version
nvm use 18
npm install

# Build assets
npm run build
```

### 2. Database Issues

#### Connection Refused
**Error**: `Connection refused` or `Access denied`

**Diagnosis**:
```bash
# Test database connection
mysql -u username -p -h localhost database_name

# Check database service
sudo systemctl status mysql
sudo systemctl status mariadb

# Check database configuration
php artisan config:show database
```

**Solutions**:
```bash
# Start database service
sudo systemctl start mysql

# Reset database password
sudo mysql -u root -p
ALTER USER 'root'@'localhost' IDENTIFIED BY 'new_password';
FLUSH PRIVILEGES;

# Check .env database configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mewayz
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

#### Migration Failures
**Error**: `Migration table not found` or `Column already exists`

**Solutions**:
```bash
# Check migration status
php artisan migrate:status

# Rollback problematic migration
php artisan migrate:rollback --step=1

# Fresh migration (WARNING: destroys data)
php artisan migrate:fresh

# Reset and seed
php artisan migrate:fresh --seed
```

#### Table Doesn't Exist
**Error**: `Table 'database.table' doesn't exist`

**Solutions**:
```bash
# Run migrations
php artisan migrate

# Check if migration file exists
ls database/migrations/

# Create missing migration
php artisan make:migration create_table_name_table
```

### 3. Authentication Issues

#### Session Not Working
**Error**: Sessions not persisting or login loops

**Diagnosis**:
```bash
# Check session configuration
php artisan config:show session

# Check session files
ls -la storage/framework/sessions/

# Check session driver
echo "Session driver: " . config('session.driver');
```

**Solutions**:
```bash
# Clear sessions
php artisan session:clear

# Fix session permissions
sudo chown -R www-data:www-data storage/
sudo chmod -R 755 storage/

# Change session driver (in .env)
SESSION_DRIVER=file
# or
SESSION_DRIVER=redis
```

#### CSRF Token Mismatch
**Error**: `419 Page Expired` or `CSRF token mismatch`

**Solutions**:
```bash
# Clear cache
php artisan cache:clear
php artisan config:clear

# Check CSRF configuration
php artisan config:show app.key

# Regenerate app key
php artisan key:generate
```

**Frontend Fix**:
```javascript
// Ensure CSRF token is included
<meta name="csrf-token" content="{{ csrf_token() }}">

// In JavaScript
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
```

### 4. Payment Integration Issues

#### Stripe Connection Failed
**Error**: `Invalid API key` or `Connection timeout`

**Diagnosis**:
```bash
# Test Stripe configuration
php artisan tinker
>>> \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
>>> \Stripe\Account::retrieve();
```

**Solutions**:
```bash
# Check Stripe keys in .env
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_API_KEY=sk_test_...

# Test API key format
echo "Stripe key length: " . strlen(env('STRIPE_SECRET'));

# Clear config cache
php artisan config:clear
```

#### Payment Transaction Failures
**Error**: Payment creation fails or webhook not received

**Solutions**:
```bash
# Check payment transaction table
php artisan migrate:refresh --path=/database/migrations/2024_01_01_000000_create_payment_transactions_table.php

# Test webhook endpoint
curl -X POST http://localhost:8001/api/webhook/stripe \
  -H "Content-Type: application/json" \
  -d '{"type":"test","data":{"object":{"id":"test"}}}'

# Check webhook secret
echo "Webhook secret: " . config('services.stripe.webhook_secret');
```

### 5. Frontend Issues

#### Assets Not Loading
**Error**: `404 Not Found` for CSS/JS files

**Solutions**:
```bash
# Build assets
npm run build

# For development
npm run dev

# Clear view cache
php artisan view:clear

# Check asset manifest
cat public/build/manifest.json
```

#### Vite Issues
**Error**: `Vite dev server not running` or build failures

**Solutions**:
```bash
# Start Vite dev server
npm run dev

# Build for production
npm run build

# Check Vite configuration
cat vite.config.js

# Clear Vite cache
rm -rf node_modules/.vite
```

### 6. Performance Issues

#### Slow Page Load
**Symptoms**: Pages taking > 5 seconds to load

**Diagnosis**:
```bash
# Check database queries
php artisan debugbar:clear

# Monitor database performance
mysql -u root -p -e "SHOW PROCESSLIST;"

# Check log files
tail -f storage/logs/laravel.log
```

**Solutions**:
```bash
# Enable query caching
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize database
php artisan migrate:fresh --seed
php artisan db:seed --class=OptimizationSeeder

# Enable Redis caching
CACHE_DRIVER=redis
SESSION_DRIVER=redis
```

#### Memory Issues
**Error**: `Fatal error: Allowed memory size exhausted`

**Solutions**:
```bash
# Increase PHP memory limit
echo "memory_limit = 256M" >> /etc/php/8.2/apache2/php.ini

# Optimize queries
# Use eager loading instead of lazy loading
$sites = Site::with('user')->get();

# Use chunking for large datasets
Site::chunk(100, function($sites) {
    foreach ($sites as $site) {
        // Process site
    }
});
```

### 7. File Permission Issues

#### Storage Permission Denied
**Error**: `Permission denied` for storage operations

**Solutions**:
```bash
# Fix storage permissions
sudo chown -R www-data:www-data storage/
sudo chown -R www-data:www-data bootstrap/cache/
sudo chmod -R 755 storage/
sudo chmod -R 755 bootstrap/cache/

# For development (not recommended for production)
sudo chmod -R 777 storage/
sudo chmod -R 777 bootstrap/cache/

# Create storage link
php artisan storage:link
```

#### Log Files Not Writable
**Error**: `Unable to create log file`

**Solutions**:
```bash
# Create log directory
mkdir -p storage/logs

# Fix permissions
sudo chown -R www-data:www-data storage/logs/
sudo chmod -R 755 storage/logs/

# Check log configuration
cat config/logging.php
```

## 🔧 Debugging Tools

### Laravel Debugging

#### Debug Mode
```bash
# Enable debug mode (development only)
APP_DEBUG=true

# Check debug status
php artisan about
```

#### Logging
```php
// Add debug logging
use Illuminate\Support\Facades\Log;

Log::debug('Debug message', ['data' => $data]);
Log::info('Info message');
Log::warning('Warning message');
Log::error('Error message', ['exception' => $exception]);
```

#### Artisan Commands
```bash
# Debug routes
php artisan route:list

# Debug configuration
php artisan config:show

# Debug database
php artisan migrate:status

# Debug cache
php artisan cache:clear
```

### Database Debugging

#### Query Debugging
```php
// Enable query logging
DB::enableQueryLog();

// Your database operations
$sites = Site::where('user_id', 1)->get();

// Get executed queries
dd(DB::getQueryLog());
```

#### Database Profiling
```bash
# Check slow queries
mysql -u root -p -e "SHOW FULL PROCESSLIST;"

# Enable slow query log
mysql -u root -p -e "SET GLOBAL slow_query_log = 'ON';"
mysql -u root -p -e "SET GLOBAL long_query_time = 2;"

# Check query performance
mysql -u root -p -e "EXPLAIN SELECT * FROM sites WHERE user_id = 1;"
```

### API Debugging

#### Test API Endpoints
```bash
# Test health endpoint
curl -s http://localhost:8001/api/health | jq

# Test with authentication
curl -H "Authorization: Bearer your-token" \
     -H "Content-Type: application/json" \
     http://localhost:8001/api/sites

# Test payment endpoint
curl -X POST http://localhost:8001/api/payments/checkout/session \
     -H "Content-Type: application/json" \
     -d '{"package_id":"starter","success_url":"https://example.com/success","cancel_url":"https://example.com/cancel"}'
```

#### Monitor API Responses
```php
// Add response logging middleware
class APIResponseLogger
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        
        Log::info('API Response', [
            'url' => $request->url(),
            'method' => $request->method(),
            'status' => $response->status(),
            'response_time' => microtime(true) - LARAVEL_START,
        ]);
        
        return $response;
    }
}
```

## 📊 Performance Monitoring

### Application Performance

#### Monitor Response Times
```bash
# Apache log analysis
tail -f /var/log/apache2/access.log | grep -E "POST|GET"

# Laravel log monitoring
tail -f storage/logs/laravel.log
```

#### Database Performance
```sql
-- Check slow queries
SHOW FULL PROCESSLIST;

-- Check table sizes
SELECT 
    table_name,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)'
FROM information_schema.tables
WHERE table_schema = 'mewayz'
ORDER BY (data_length + index_length) DESC;

-- Check index usage
SHOW INDEX FROM sites;
```

### Memory and CPU Monitoring

#### System Resources
```bash
# Check memory usage
free -h

# Check CPU usage
top -p $(pgrep php)

# Check disk usage
df -h
```

#### Laravel Performance
```php
// Monitor memory usage
echo "Memory usage: " . memory_get_usage(true) / 1024 / 1024 . " MB\n";
echo "Peak memory: " . memory_get_peak_usage(true) / 1024 / 1024 . " MB\n";

// Monitor execution time
$start = microtime(true);
// Your code here
$end = microtime(true);
echo "Execution time: " . ($end - $start) . " seconds\n";
```

## 🛠️ Advanced Troubleshooting

### Container Issues

#### Docker Container Problems
```bash
# Check container status
docker ps -a

# View container logs
docker logs container_name

# Debug container
docker exec -it container_name /bin/bash

# Restart container
docker restart container_name
```

#### Kubernetes Issues
```bash
# Check pod status
kubectl get pods

# View pod logs
kubectl logs pod_name

# Describe pod for events
kubectl describe pod pod_name

# Debug pod
kubectl exec -it pod_name -- /bin/bash
```

### Network Issues

#### Connection Problems
```bash
# Test network connectivity
ping database_host
telnet database_host 3306

# Check DNS resolution
nslookup database_host

# Check firewall
sudo ufw status
sudo iptables -L
```

#### SSL/TLS Issues
```bash
# Test SSL certificate
openssl s_client -connect your-domain.com:443

# Check certificate expiry
openssl x509 -in certificate.crt -text -noout | grep "Not After"

# Test with curl
curl -I https://your-domain.com
```

## 🔍 Specific Error Messages

### Common Laravel Errors

#### `Class not found`
**Solutions**:
```bash
# Regenerate autoloader
composer dump-autoload

# Clear cache
php artisan cache:clear
php artisan config:clear
```

#### `Method not found`
**Solutions**:
```bash
# Check method exists
php artisan tinker
>>> method_exists(App\Models\User::class, 'methodName');

# Check trait usage
php artisan tinker
>>> get_class_methods(App\Models\User::class);
```

#### `Column not found`
**Solutions**:
```bash
# Check table schema
php artisan tinker
>>> Schema::hasTable('table_name');
>>> Schema::hasColumn('table_name', 'column_name');

# Run migrations
php artisan migrate
```

### Payment Errors

#### `Invalid API key`
**Solutions**:
```bash
# Check API key format
echo "Key starts with: " . substr(env('STRIPE_SECRET'), 0, 8);

# Test key validity
php artisan tinker
>>> \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
>>> \Stripe\Account::retrieve();
```

#### `Webhook signature verification failed`
**Solutions**:
```bash
# Check webhook secret
echo "Webhook secret: " . config('services.stripe.webhook_secret');

# Test webhook endpoint
curl -X POST http://localhost:8001/api/webhook/stripe \
  -H "Stripe-Signature: t=1234567890,v1=test" \
  -H "Content-Type: application/json" \
  -d '{"type":"test"}'
```

## 📋 Troubleshooting Checklist

### Before You Start
- [ ] Check system requirements
- [ ] Verify all services are running
- [ ] Check log files for errors
- [ ] Test in different environment
- [ ] Document the issue

### Systematic Debugging
1. **Identify the Problem**
   - Reproduce the issue
   - Check error messages
   - Review recent changes

2. **Gather Information**
   - Check logs
   - Test related functionality
   - Verify configuration

3. **Isolate the Issue**
   - Test individual components
   - Check dependencies
   - Verify data integrity

4. **Apply Solutions**
   - Start with simple fixes
   - Test after each change
   - Document successful fixes

5. **Verify Resolution**
   - Test thoroughly
   - Check for side effects
   - Update documentation

## 🆘 Emergency Procedures

### Site Down
```bash
# Quick recovery steps
1. Check service status
sudo systemctl status apache2
sudo systemctl status mysql

2. Restart services
sudo systemctl restart apache2
sudo systemctl restart mysql

3. Check logs
tail -f storage/logs/laravel.log
tail -f /var/log/apache2/error.log

4. Clear cache
php artisan cache:clear
php artisan config:clear

5. Check database
php artisan migrate:status
```

### Database Corruption
```bash
# Emergency database recovery
1. Stop application
sudo systemctl stop apache2

2. Backup current database
mysqldump -u root -p mewayz > backup_$(date +%Y%m%d_%H%M%S).sql

3. Repair tables
mysql -u root -p mewayz
> REPAIR TABLE table_name;

4. Restore from backup if needed
mysql -u root -p mewayz < backup_file.sql

5. Restart application
sudo systemctl start apache2
```

## 📞 Getting Help

### Internal Resources
- **Documentation**: Check [docs/](../README.md)
- **Logs**: Always check application logs first
- **Configuration**: Verify environment settings
- **Testing**: Try to reproduce in development

### External Resources
- **Laravel Documentation**: https://laravel.com/docs
- **Stripe Documentation**: https://stripe.com/docs
- **Stack Overflow**: Tag questions with `laravel` and `mewayz`
- **GitHub Issues**: Report bugs at repository

### Support Channels
- **Development Team**: developers@mewayz.com
- **System Administration**: sysadmin@mewayz.com
- **Emergency**: emergency@mewayz.com (24/7)

### Information to Include
When reporting issues, please include:
- Error messages (exact text)
- Steps to reproduce
- Environment information
- Log file excerpts
- Screenshots if applicable
- Recent changes made

---

**Last Updated**: January 16, 2025  
**Version**: 2.0  
**Next Review**: March 2025

*This troubleshooting guide is regularly updated based on common issues and community feedback.*