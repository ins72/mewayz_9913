# Mewayz Platform v2 - Troubleshooting Guide

*Last Updated: July 17, 2025*

## Overview

This guide provides solutions to common issues encountered when working with the Mewayz Platform v2. The platform is built on Laravel 11 + MySQL with Supervisor for process management.

## Common Issues and Solutions

### Installation Issues

#### PHP Not Found
```bash
Error: php: not found
```

**Solution:**
```bash
# Install PHP 8.2 and required extensions
sudo apt update
sudo apt install -y php8.2 php8.2-cli php8.2-mbstring php8.2-xml \
  php8.2-curl php8.2-zip php8.2-mysql php8.2-gd php8.2-bcmath \
  php8.2-json php8.2-tokenizer php8.2-ctype php8.2-fileinfo

# Verify installation
php --version
```

#### Composer Install Fails
```bash
Error: Your requirements could not be resolved to an installable set of packages.
```

**Solution:**
```bash
# Update Composer
composer self-update

# Clear cache and reinstall
composer clear-cache
composer install --no-dev --optimize-autoloader

# If still failing, check PHP extensions
composer check-platform-reqs
```

#### Database Connection Issues
```bash
Error: SQLSTATE[HY000] [2002] Connection refused
```

**Solution:**
```bash
# Start MySQL service
sudo systemctl start mysql
sudo systemctl enable mysql

# Check if MySQL is running
sudo systemctl status mysql

# Reset root password if needed
sudo mysql_secure_installation

# Test connection
mysql -u root -p -e "SELECT 1;"
```

### Laravel Application Issues

#### Application Key Not Set
```bash
Error: No application encryption key has been specified.
```

**Solution:**
```bash
# Generate application key
php artisan key:generate

# If .env doesn't exist
cp .env.example .env
php artisan key:generate
```

#### Migration Errors
```bash
Error: SQLSTATE[42S01]: Base table or view already exists
```

**Solution:**
```bash
# Drop and recreate database
mysql -u root -p -e "DROP DATABASE IF EXISTS mewayz; CREATE DATABASE mewayz;"

# Run fresh migrations
php artisan migrate:fresh --force

# If specific table issues
php artisan migrate:rollback
php artisan migrate
```

#### Foreign Key Constraint Errors
```bash
Error: SQLSTATE[23000]: Integrity constraint violation
```

**Solution:**
```bash
# Check foreign key relationships
mysql -u root -p -e "USE mewayz; SHOW CREATE TABLE users;"

# Temporarily disable foreign key checks
mysql -u root -p -e "SET FOREIGN_KEY_CHECKS=0;"
php artisan migrate
mysql -u root -p -e "SET FOREIGN_KEY_CHECKS=1;"
```

### Supervisor Issues

#### Supervisor Not Starting
```bash
Error: unix:///var/run/supervisor.sock no such file
```

**Solution:**
```bash
# Install supervisor
sudo apt install -y supervisor

# Start supervisor
sudo systemctl start supervisor
sudo systemctl enable supervisor

# Check status
sudo systemctl status supervisor
```

#### Laravel App Not Running
```bash
Error: laravel-app: ERROR (spawn error)
```

**Solution:**
```bash
# Check supervisor logs
sudo supervisorctl tail laravel-app

# Restart supervisor
sudo supervisorctl restart laravel-app

# Check permissions
sudo chown -R www-data:www-data /var/www/mewayz
sudo chmod -R 755 /var/www/mewayz/storage
```

### Asset Compilation Issues

#### Vite Build Errors
```bash
Error: Vite manifest not found
```

**Solution:**
```bash
# Install Node.js dependencies
npm install

# Build assets
npm run build

# For development
npm run dev

# Clear Laravel caches
php artisan config:clear
php artisan cache:clear
```

#### CSS/JS Not Loading
```bash
Error: Failed to load resource: net::ERR_ABORTED 404
```

**Solution:**
```bash
# Rebuild assets
npm run build

# Check public directory permissions
sudo chown -R www-data:www-data /var/www/mewayz/public
sudo chmod -R 755 /var/www/mewayz/public

# Clear browser cache
# Check nginx configuration for asset serving
```

### Authentication Issues

#### Token Authentication Failing
```bash
Error: Unauthenticated
```

**Solution:**
```bash
# Check if Sanctum is properly configured
php artisan config:cache

# Verify middleware in routes
# Check if token is being passed correctly in headers
Authorization: Bearer {token}

# Debug token
php artisan tinker
>>> use Laravel\Sanctum\PersonalAccessToken;
>>> PersonalAccessToken::findToken('token_here');
```

#### Session Issues
```bash
Error: Session store not set on request
```

**Solution:**
```bash
# Check session configuration in .env
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Clear sessions
php artisan session:table
php artisan migrate
php artisan cache:clear
```

### Performance Issues

#### Slow Database Queries
```bash
# Enable query logging
```

**Solution:**
```bash
# Check slow query log
mysql -u root -p -e "SHOW VARIABLES LIKE 'slow_query_log';"

# Enable slow query log
mysql -u root -p -e "SET GLOBAL slow_query_log = 'ON';"

# Optimize common queries
php artisan optimize

# Check database indexes
mysql -u root -p -e "USE mewayz; SHOW INDEXES FROM users;"
```

#### High Memory Usage
```bash
Error: Fatal error: Allowed memory size exhausted
```

**Solution:**
```bash
# Increase PHP memory limit
echo "memory_limit = 512M" >> /etc/php/8.2/cli/php.ini
echo "memory_limit = 512M" >> /etc/php/8.2/fpm/php.ini

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm

# Optimize Laravel
php artisan optimize
```

### API Issues

#### CORS Errors
```bash
Error: Access to XMLHttpRequest blocked by CORS policy
```

**Solution:**
```bash
# Install laravel/sanctum if not already installed
composer require laravel/sanctum

# Configure sanctum in .env
SANCTUM_STATEFUL_DOMAINS=your-domain.com,localhost

# Clear config cache
php artisan config:clear
```

#### API Routes Not Found
```bash
Error: 404 Not Found
```

**Solution:**
```bash
# Check route list
php artisan route:list | grep api

# Clear route cache
php artisan route:clear
php artisan route:cache

# Check if routes are properly included
# Verify nginx configuration for API proxy
```

### Production Issues

#### 500 Internal Server Error
```bash
Error: The server encountered an internal error
```

**Solution:**
```bash
# Check Laravel logs
tail -f /var/www/mewayz/storage/logs/laravel.log

# Check web server logs
tail -f /var/log/nginx/error.log

# Check PHP-FPM logs
tail -f /var/log/php8.2-fpm.log

# Common fixes
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

#### SSL Certificate Issues
```bash
Error: SSL certificate problem
```

**Solution:**
```bash
# Renew Let's Encrypt certificate
sudo certbot renew

# Check certificate status
sudo certbot certificates

# Test SSL configuration
openssl s_client -connect your-domain.com:443
```

## Debugging Commands

### Laravel Debugging
```bash
# Check Laravel status
php artisan about

# Clear all caches
php artisan optimize:clear

# Check configuration
php artisan config:show

# Database connection test
php artisan tinker
>>> DB::connection()->getPdo();

# Check queues
php artisan queue:monitor
```

### System Debugging
```bash
# Check system resources
free -h
df -h
top

# Check service status
sudo systemctl status nginx
sudo systemctl status php8.2-fpm
sudo systemctl status mysql
sudo systemctl status supervisor

# Check logs
journalctl -u nginx -n 50
journalctl -u php8.2-fpm -n 50
```

### Network Debugging
```bash
# Test API endpoints
curl -H "Accept: application/json" http://localhost:8001/api/health

# Check port availability
netstat -tlnp | grep :8001
netstat -tlnp | grep :80
netstat -tlnp | grep :443

# Test DNS resolution
nslookup your-domain.com
```

## Recovery Procedures

### Database Recovery
```bash
# Restore from backup
mysql -u root -p mewayz < /var/backups/mewayz/mewayz_backup.sql

# Rebuild database
php artisan migrate:fresh --force
php artisan db:seed --force
```

### Application Recovery
```bash
# Reset to clean state
git reset --hard HEAD
composer install --no-dev --optimize-autoloader
npm ci --only=production
npm run build

# Reset permissions
sudo chown -R www-data:www-data /var/www/mewayz
sudo chmod -R 755 /var/www/mewayz/storage
sudo chmod -R 755 /var/www/mewayz/bootstrap/cache
```

## Getting Help

### Log Files Locations
- **Laravel**: `/var/www/mewayz/storage/logs/laravel.log`
- **Supervisor**: `/var/log/supervisor/`
- **Nginx**: `/var/log/nginx/`
- **PHP-FPM**: `/var/log/php8.2-fpm.log`
- **MySQL**: `/var/log/mysql/`

### Useful Commands
```bash
# Check all service status
sudo supervisorctl status

# Monitor logs in real-time
sudo tail -f /var/log/supervisor/laravel-app.log

# Check Laravel configuration
php artisan config:show | grep -i database

# Test database connection
php artisan migrate:status
```

### Support Resources
- [Developer Guide](../developer/README.md)
- [API Documentation](../api/README.md)
- [Deployment Guide](../deployment/README.md)
- [Main Documentation](../README.md)

## Prevention

### Regular Maintenance
```bash
# Weekly maintenance script
#!/bin/bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer dump-autoload --optimize
```

### Monitoring Setup
```bash
# Set up log rotation
sudo logrotate /etc/logrotate.d/laravel

# Monitor disk space
df -h | grep -E "(80|90|100)%"

# Monitor memory usage
free -h
```

This troubleshooting guide should help resolve most common issues with the Mewayz Platform v2. For persistent issues, check the relevant log files and follow the debugging procedures outlined above.