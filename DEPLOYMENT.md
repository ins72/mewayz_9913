# 🚀 Mewayz Platform Deployment Guide

*Complete Production Deployment Guide for Mewayz Platform*

## 📋 Overview

This guide provides step-by-step instructions for deploying the Mewayz Platform to production environments. The platform uses a single Laravel backend with multiple frontend options and requires specific infrastructure setup.

## 🏗️ Architecture Overview

### Production Architecture
```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Load Balancer │    │   Web Server    │    │   Database      │
│   (Nginx/HAProxy)│◄─►│   (Nginx/Apache)│◄─►│   MySQL/MariaDB │
│   SSL/TLS       │    │   PHP-FPM       │    │   Redis Cache   │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │                       │                       │
         │              ┌─────────────────┐              │
         │              │   Laravel       │              │
         └──────────────│   Application   │──────────────┘
                        │   (Port 8001)   │
                        └─────────────────┘
```

### Components
- **Laravel Backend**: Core application (PHP 8.1+)
- **MySQL Database**: Primary data storage
- **Redis**: Caching and session storage
- **Nginx**: Web server and reverse proxy
- **Supervisor**: Process management
- **SSL/TLS**: HTTPS encryption

## 🔧 Prerequisites

### Server Requirements
- **Operating System**: Ubuntu 20.04 LTS or later
- **RAM**: Minimum 2GB, Recommended 4GB+
- **CPU**: Minimum 2 cores, Recommended 4 cores+
- **Storage**: Minimum 20GB SSD
- **Network**: Public IP address and domain name

### Software Requirements
- **PHP**: 8.1 or higher
- **MySQL**: 8.0 or higher
- **Redis**: 6.0 or higher
- **Nginx**: 1.18 or higher
- **Supervisor**: 4.0 or higher
- **Composer**: 2.0 or higher
- **Node.js**: 16.0 or higher (for asset compilation)

## 📦 Quick Deployment

### Automated Deployment
```bash
# Clone repository
git clone https://github.com/mewayz/platform.git
cd platform

# Run deployment script
./deploy.sh production
```

### Manual Deployment
Follow the detailed steps below for complete control.

## 🚀 Step-by-Step Deployment

### Step 1: Server Setup

#### 1.1 Update System
```bash
sudo apt update && sudo apt upgrade -y
```

#### 1.2 Install PHP and Extensions
```bash
sudo apt install -y php8.1 php8.1-fpm php8.1-mysql php8.1-xml php8.1-curl \
php8.1-mbstring php8.1-zip php8.1-gd php8.1-bcmath php8.1-redis \
php8.1-intl php8.1-soap
```

#### 1.3 Install Database
```bash
sudo apt install -y mysql-server
sudo mysql_secure_installation
```

#### 1.4 Install Redis
```bash
sudo apt install -y redis-server
sudo systemctl enable redis-server
```

#### 1.5 Install Web Server
```bash
sudo apt install -y nginx
sudo systemctl enable nginx
```

#### 1.6 Install Supervisor
```bash
sudo apt install -y supervisor
sudo systemctl enable supervisor
```

### Step 2: Database Configuration

#### 2.1 Create Database
```sql
CREATE DATABASE mewayz CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'mewayz'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON mewayz.* TO 'mewayz'@'localhost';
FLUSH PRIVILEGES;
```

#### 2.2 Optimize MySQL
```bash
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
```

Add:
```ini
[mysqld]
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
max_connections = 200
```

### Step 3: Application Setup

#### 3.1 Clone and Install
```bash
cd /var/www
sudo git clone https://github.com/mewayz/platform.git mewayz
cd mewayz
sudo composer install --no-dev --optimize-autoloader
```

#### 3.2 Environment Configuration
```bash
sudo cp .env.example .env
sudo nano .env
```

Configure:
```env
APP_NAME=Mewayz
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=mewayz
DB_USERNAME=mewayz
DB_PASSWORD=secure_password

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

#### 3.3 Application Setup
```bash
sudo php artisan key:generate
sudo php artisan migrate --force
sudo php artisan storage:link
sudo php artisan config:cache
sudo php artisan route:cache
sudo php artisan view:cache
```

#### 3.4 Set Permissions
```bash
sudo chown -R www-data:www-data /var/www/mewayz
sudo chmod -R 755 /var/www/mewayz
sudo chmod -R 775 /var/www/mewayz/storage
```

### Step 4: Web Server Configuration

#### 4.1 Nginx Configuration
```bash
sudo nano /etc/nginx/sites-available/mewayz
```

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/mewayz/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

#### 4.2 Enable Site
```bash
sudo ln -s /etc/nginx/sites-available/mewayz /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 4. Supervisor Configuration

#### Backend Service
```ini
# /etc/supervisor/conf.d/mewayz-backend.conf
[program:mewayz-backend]
process_name=%(program_name)s_%(process_num)02d
command=php -S 0.0.0.0:8001 -t /var/www/mewayz/public
directory=/var/www/mewayz
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/log/supervisor/mewayz-backend.log
```

#### Frontend Service
```ini
# /etc/supervisor/conf.d/mewayz-frontend.conf
[program:mewayz-frontend]
process_name=%(program_name)s_%(process_num)02d
command=python3 -m http.server 3000
directory=/var/www/mewayz/public
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/log/supervisor/mewayz-frontend.log
```

#### Start Services
```bash
# Update supervisor
sudo supervisorctl reread
sudo supervisorctl update

# Start services
sudo supervisorctl start mewayz-backend
sudo supervisorctl start mewayz-frontend

# Check status
sudo supervisorctl status
```

### 5. SSL Certificate Setup

#### Using Let's Encrypt
```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Get SSL certificate
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# Auto-renewal
sudo crontab -e
# Add: 0 12 * * * /usr/bin/certbot renew --quiet
```

#### Using Custom Certificate
```bash
# Copy certificate files
sudo cp your-certificate.crt /etc/ssl/certs/
sudo cp your-private.key /etc/ssl/private/

# Set permissions
sudo chmod 600 /etc/ssl/private/your-private.key
sudo chown root:root /etc/ssl/private/your-private.key
```

### 6. Performance Optimization

#### Laravel Optimizations
```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize

# Enable OPcache
sudo nano /etc/php/8.2/fpm/php.ini
# Uncomment and set:
# opcache.enable=1
# opcache.memory_consumption=128
# opcache.interned_strings_buffer=8
# opcache.max_accelerated_files=4000
# opcache.revalidate_freq=60
# opcache.fast_shutdown=1
```

#### Database Optimization
```sql
-- Add database indexes
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_bio_sites_slug ON bio_sites(slug);
CREATE INDEX idx_workspaces_slug ON workspaces(slug);
CREATE INDEX idx_social_media_accounts_user_id ON social_media_accounts(user_id);
```

#### Web Server Optimization
```bash
# Increase PHP-FPM workers
sudo nano /etc/php/8.2/fpm/pool.d/www.conf
# Adjust:
# pm.max_children = 50
# pm.start_servers = 20
# pm.min_spare_servers = 5
# pm.max_spare_servers = 35

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm
```

### 7. Monitoring and Logging

#### Setup Log Rotation
```bash
# /etc/logrotate.d/mewayz
/var/www/mewayz/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
    postrotate
        sudo supervisorctl restart mewayz-backend
    endscript
}
```

#### Setup Monitoring
```bash
# Install monitoring tools
sudo apt install htop iotop nethogs

# Monitor application
sudo supervisorctl tail -f mewayz-backend
sudo supervisorctl tail -f mewayz-frontend

# Monitor system resources
htop
iotop
nethogs
```

### 8. Backup Strategy

#### Database Backup
```bash
# Create backup script
sudo nano /usr/local/bin/mewayz-backup.sh

#!/bin/bash
BACKUP_DIR="/var/backups/mewayz"
DATE=$(date +"%Y%m%d_%H%M%S")

mkdir -p $BACKUP_DIR

# Database backup
mysqldump -u mewayz -p mewayz > $BACKUP_DIR/mewayz_db_$DATE.sql

# Application backup
tar -czf $BACKUP_DIR/mewayz_app_$DATE.tar.gz -C /var/www mewayz

# Keep only last 7 days
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete

# Make executable
sudo chmod +x /usr/local/bin/mewayz-backup.sh

# Schedule backup
sudo crontab -e
# Add: 0 2 * * * /usr/local/bin/mewayz-backup.sh
```

#### File Backup
```bash
# Backup important files
rsync -av /var/www/mewayz/storage/ /backup/mewayz/storage/
rsync -av /var/www/mewayz/.env /backup/mewayz/
```

### 9. Security Hardening

#### Firewall Configuration
```bash
# Install UFW
sudo apt install ufw

# Configure firewall
sudo ufw allow ssh
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable

# Check status
sudo ufw status
```

#### File Permissions
```bash
# Set proper permissions
sudo chown -R www-data:www-data /var/www/mewayz
sudo chmod -R 755 /var/www/mewayz
sudo chmod -R 777 /var/www/mewayz/storage
sudo chmod -R 777 /var/www/mewayz/bootstrap/cache
```

#### Security Headers
```nginx
# Add to Nginx configuration
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-Content-Type-Options "nosniff" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline';" always;
```

### 10. Testing Deployment

#### Functionality Tests
```bash
# Test health endpoint
curl -I https://your-domain.com/api/health

# Test authentication
curl -X POST https://your-domain.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@mewayz.com","password":"secure_password"}'

# Test OAuth redirects
curl -I https://your-domain.com/api/auth/oauth/google

# Test PWA manifest
curl -I https://your-domain.com/manifest.json

# Test service worker
curl -I https://your-domain.com/sw.js
```

#### Performance Tests
```bash
# Test response times
curl -w "@curl-format.txt" -o /dev/null -s https://your-domain.com

# Test concurrent users
ab -n 100 -c 10 https://your-domain.com/api/health

# Test database performance
mysql -u mewayz -p -e "SHOW PROCESSLIST;"
```

### 11. Post-Deployment Checklist

- [ ] All services running correctly
- [ ] SSL certificate installed and working
- [ ] Database migrations completed
- [ ] Admin user created
- [ ] OAuth providers configured
- [ ] Email sending configured
- [ ] Backup system active
- [ ] Monitoring in place
- [ ] Performance optimized
- [ ] Security headers configured
- [ ] Firewall rules applied
- [ ] Log rotation configured
- [ ] Error tracking setup

### 12. Troubleshooting Common Issues

#### Service Not Starting
```bash
# Check supervisor logs
sudo supervisorctl tail -f mewayz-backend

# Check PHP errors
tail -f /var/log/php8.2-fpm.log

# Check Nginx errors
tail -f /var/log/nginx/error.log
```

#### Database Connection Issues
```bash
# Test database connection
mysql -u mewayz -p -h localhost mewayz

# Check Laravel configuration
php artisan config:show database

# Test with artisan
php artisan migrate:status
```

#### SSL Issues
```bash
# Check SSL certificate
openssl s_client -connect your-domain.com:443

# Renew Let's Encrypt certificate
sudo certbot renew --dry-run

# Check certificate expiry
sudo certbot certificates
```

### 13. Maintenance

#### Regular Updates
```bash
# Update system packages
sudo apt update && sudo apt upgrade

# Update Composer packages
composer update

# Update npm packages
npm update

# Update Flutter
flutter upgrade
```

#### Health Checks
```bash
# Check application health
curl https://your-domain.com/api/health

# Check database health
php artisan db:show

# Check queue health (if using queues)
php artisan queue:work --once

# Check storage space
df -h
```

This deployment guide provides a comprehensive setup for production deployment of the Mewayz application. Follow each step carefully and test thoroughly before going live.