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

### Step 5: SSL Certificate

#### 5.1 Install Certbot
```bash
sudo apt install -y certbot python3-certbot-nginx
```

#### 5.2 Obtain Certificate
```bash
sudo certbot --nginx -d yourdomain.com
```

### Step 6: Process Management

#### 6.1 Supervisor Configuration
```bash
sudo nano /etc/supervisor/conf.d/mewayz.conf
```

```ini
[program:mewayz-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/mewayz/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/mewayz/storage/logs/worker.log
```

#### 6.2 Start Workers
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start mewayz-worker:*
```

## 🔧 Configuration Options

### Performance Optimization
- **PHP-FPM**: Configure process management
- **Redis**: Optimize memory usage
- **MySQL**: Tune for performance
- **Nginx**: Enable gzip compression

### Security Configuration
- **Firewall**: Configure UFW
- **Fail2Ban**: Protect against brute force
- **SSL/TLS**: Enable HTTPS
- **Security Headers**: Add security headers

### Monitoring Setup
- **Log Rotation**: Configure logrotate
- **Health Checks**: Set up monitoring
- **Alerts**: Configure notifications

## 🔄 Deployment Updates

### Zero-Downtime Deployment
```bash
#!/bin/bash
# Update deployment script

cd /var/www/mewayz
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo supervisorctl restart mewayz-worker:*
```

### Rollback Strategy
```bash
#!/bin/bash
# Rollback script

# Create backup before deployment
# Use Git tags for version control
# Quick rollback to previous version
```

## 🚨 Troubleshooting

### Common Issues
1. **500 Error**: Check PHP-FPM and Nginx logs
2. **Database Connection**: Verify MySQL credentials
3. **Redis Connection**: Check Redis service
4. **Queue Workers**: Restart supervisor processes

### Performance Issues
1. **Slow Queries**: Enable MySQL slow query log
2. **Memory Usage**: Monitor and optimize PHP settings
3. **CPU Usage**: Check for inefficient processes

### Security Issues
1. **SSL Certificate**: Verify certificate validity
2. **Firewall**: Check UFW rules
3. **File Permissions**: Ensure proper permissions

## 📞 Support

### Getting Help
- **Documentation**: Complete deployment guide
- **Support Email**: support@mewayz.com
- **GitHub Issues**: Report deployment issues
- **Community**: Join our Discord server

### Professional Services
- **Managed Hosting**: Available through Mewayz Technologies
- **Custom Deployment**: Professional deployment services
- **24/7 Support**: Enterprise support packages

---

**Last Updated**: July 15, 2025  
**Version**: 1.0.0  
**Platform**: Mewayz All-in-One Business Solution

---

*This deployment guide ensures a secure, scalable, and maintainable production environment for the Mewayz Platform.*