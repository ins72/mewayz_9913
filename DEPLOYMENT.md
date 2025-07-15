# 🚀 Mewayz Platform Deployment Guide

*Complete Production Deployment Guide for Mewayz Platform*

## 📋 Overview

This guide provides step-by-step instructions for deploying the Mewayz Platform to production environments. The platform uses a single Laravel backend with multiple frontend options and requires specific infrastructure setup.

---

## 🚀 Deployment Philosophy

At Mewayz Technologies Inc., we understand that exceptional software requires exceptional deployment practices. This guide embodies our commitment to seamless, reliable, and secure production deployments for the Mewayz platform.

**Our Deployment Principles:**
- **Reliability**: Zero-downtime deployments with automated rollback capabilities
- **Security**: Enterprise-grade security from development to production
- **Scalability**: Infrastructure that grows seamlessly with your business
- **Performance**: Optimized configurations for maximum efficiency
- **Monitoring**: Comprehensive observability for proactive issue resolution

**Target Environments:**
- **Production**: Live customer-facing deployments at https://mewayz.com
- **Staging**: Pre-production testing and validation
- **Development**: Local and team development environments

**Domain Configuration:**
- **Production Domain**: mewayz.com
- **Platform URL**: https://mewayz.com
- **API Endpoint**: https://mewayz.com/api
- **Admin Panel**: https://mewayz.com/admin

---

## Production Deployment

### Prerequisites

- **Server Requirements**:
  - PHP 8.2+
  - Node.js 18+
  - MySQL 8.0+ or MariaDB
  - Nginx or Apache
  - SSL Certificate
  - Minimum 2GB RAM, 20GB Storage

- **Development Tools**:
  - Composer
  - npm/yarn
  - Flutter SDK (for mobile builds)
  - Git

### 1. Server Setup

#### Install Required Packages
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP and extensions
sudo apt install php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-zip php8.2-curl php8.2-gd php8.2-bcmath

# Install MySQL
sudo apt install mysql-server

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install nodejs

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Supervisor
sudo apt install supervisor

# Install Nginx
sudo apt install nginx
```

#### Configure MySQL
```bash
# Secure MySQL installation
sudo mysql_secure_installation

# Create database
sudo mysql -u root -p
CREATE DATABASE mewayz;
CREATE USER 'mewayz'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON mewayz.* TO 'mewayz'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 2. Application Deployment

#### Clone and Setup
```bash
# Clone repository
git clone https://github.com/mewayz/mewayz.git /var/www/mewayz
cd /var/www/mewayz

# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node.js dependencies
npm install --production

# Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 755 storage bootstrap/cache
```

#### Environment Configuration
```bash
# Create environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure environment
nano .env
```

**Production .env Configuration:**
```env
APP_NAME=Mewayz
APP_ENV=production
APP_KEY=base64:generated-key-here
APP_DEBUG=false
APP_URL=https://your-domain.com
APP_INSTALLED=true

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mewayz
DB_USERNAME=mewayz
DB_PASSWORD=secure_password

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your-email@example.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls

# OAuth Configuration
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
APPLE_CLIENT_ID=your-apple-client-id
APPLE_CLIENT_SECRET=your-apple-client-secret

# Sanctum Configuration
SANCTUM_STATEFUL_DOMAINS=your-domain.com
```

#### Database Setup
```bash
# Run migrations
php artisan migrate --force

# Seed database (optional)
php artisan db:seed

# Create admin user
php artisan tinker
>>> App\Models\User::create([
...     'name' => 'Admin User',
...     'email' => 'admin@mewayz.com',
...     'password' => Hash::make('secure_password'),
... ]);
```

#### Build Assets
```bash
# Build frontend assets
npm run build

# Build Flutter web app
cd flutter_app
flutter build web --release
cd ..

# Copy Flutter build to public
cp -r flutter_app/build/web/* public/
```

### 3. Web Server Configuration

#### Nginx Configuration
```nginx
# /etc/nginx/sites-available/mewayz
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name your-domain.com www.your-domain.com;
    
    root /var/www/mewayz/public;
    index index.php index.html index.htm;
    
    # SSL Configuration
    ssl_certificate /path/to/ssl/certificate.crt;
    ssl_certificate_key /path/to/ssl/private.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    
    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    
    # Gzip Compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;
    
    # API Routes
    location /api {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    # Flutter App Routes
    location /app {
        try_files $uri $uri/ /app.html;
    }
    
    # Static Files
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff|woff2|ttf|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
    
    # PHP Processing
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    # Deny access to hidden files
    location ~ /\. {
        deny all;
    }
}
```

#### Enable Site
```bash
# Link configuration
sudo ln -s /etc/nginx/sites-available/mewayz /etc/nginx/sites-enabled/

# Test configuration
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx
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