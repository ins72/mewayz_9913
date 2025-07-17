# Mewayz Platform v2 - Deployment Guide

*Last Updated: July 17, 2025*

## Overview

This guide covers the deployment of Mewayz Platform v2 in production environments. The platform is built on Laravel 11 + MySQL and uses Supervisor for process management.

## System Requirements

### Minimum Requirements
- **OS**: Linux (Ubuntu 20.04+ or CentOS 7+)
- **PHP**: 8.2 or higher
- **MySQL**: 8.0 or higher (MariaDB 10.5+ compatible)
- **Memory**: 2GB RAM minimum (4GB recommended)
- **Storage**: 10GB available disk space
- **Network**: HTTPS/SSL certificate required

### Recommended Requirements
- **CPU**: 4 cores or more
- **Memory**: 8GB RAM
- **Storage**: 50GB SSD
- **Network**: CDN integration for static assets

## Installation

### 1. Server Preparation

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt update

# Install PHP 8.2 and extensions
sudo apt install -y php8.2 php8.2-cli php8.2-fpm php8.2-mysql \
  php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip \
  php8.2-gd php8.2-bcmath php8.2-json php8.2-tokenizer \
  php8.2-ctype php8.2-fileinfo

# Install MySQL
sudo apt install -y mysql-server

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js and NPM
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Install Supervisor
sudo apt install -y supervisor
```

### 2. Application Deployment

```bash
# Clone the repository
git clone [repository-url] /var/www/mewayz
cd /var/www/mewayz

# Set permissions
sudo chown -R www-data:www-data /var/www/mewayz
sudo chmod -R 755 /var/www/mewayz/storage
sudo chmod -R 755 /var/www/mewayz/bootstrap/cache

# Install dependencies
composer install --no-dev --optimize-autoloader
npm ci --only=production

# Environment configuration
cp .env.example .env
php artisan key:generate
```

### 3. Database Setup

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE mewayz;"
mysql -u root -p -e "CREATE USER 'mewayz'@'localhost' IDENTIFIED BY 'secure_password';"
mysql -u root -p -e "GRANT ALL PRIVILEGES ON mewayz.* TO 'mewayz'@'localhost';"
mysql -u root -p -e "FLUSH PRIVILEGES;"

# Run migrations
php artisan migrate --force
```

### 4. Build Assets

```bash
# Build production assets
npm run build

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Configuration

### Environment Variables

Create and configure `/var/www/mewayz/.env`:

```env
APP_NAME=Mewayz
APP_ENV=production
APP_KEY=base64:generated_key_here
APP_DEBUG=false
APP_URL=https://your-domain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mewayz
DB_USERNAME=mewayz
DB_PASSWORD=secure_password

BROADCAST_DRIVER=log
CACHE_DRIVER=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=redis
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="${APP_NAME}"

# Additional production settings
SESSION_SECURE_COOKIE=true
SANCTUM_STATEFUL_DOMAINS=your-domain.com
```

### Supervisor Configuration

Create `/etc/supervisor/conf.d/mewayz.conf`:

```ini
[program:mewayz-app]
process_name=%(program_name)s
command=php /var/www/mewayz/artisan serve --host=0.0.0.0 --port=8001
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/supervisor/mewayz-app.log
stderr_logfile=/var/log/supervisor/mewayz-app.err.log

[program:mewayz-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/mewayz/artisan queue:work --sleep=3 --tries=3 --timeout=90
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/supervisor/mewayz-worker.log
stderr_logfile=/var/log/supervisor/mewayz-worker.err.log

[program:mewayz-scheduler]
process_name=%(program_name)s
command=php /var/www/mewayz/artisan schedule:work
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/supervisor/mewayz-scheduler.log
stderr_logfile=/var/log/supervisor/mewayz-scheduler.err.log
```

### Nginx Configuration

Create `/etc/nginx/sites-available/mewayz`:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name your-domain.com;
    root /var/www/mewayz/public;

    ssl_certificate /path/to/ssl/cert.pem;
    ssl_certificate_key /path/to/ssl/private.key;
    ssl_session_timeout 1d;
    ssl_session_cache shared:MozTLS:10m;
    ssl_session_tickets off;

    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;

    add_header Strict-Transport-Security "max-age=63072000" always;

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # API routes
    location /api {
        proxy_pass http://127.0.0.1:8001;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

## Service Management

### Start Services

```bash
# Reload Supervisor configuration
sudo supervisorctl reread
sudo supervisorctl update

# Start all services
sudo supervisorctl start all

# Enable Nginx site
sudo ln -s /etc/nginx/sites-available/mewayz /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Service Commands

```bash
# Check service status
sudo supervisorctl status

# Start specific service
sudo supervisorctl start mewayz-app

# Restart services
sudo supervisorctl restart all

# View logs
sudo supervisorctl tail mewayz-app
sudo supervisorctl tail mewayz-worker

# Stop services
sudo supervisorctl stop all
```

## Monitoring

### Log Files

```bash
# Application logs
tail -f /var/www/mewayz/storage/logs/laravel.log

# Supervisor logs
tail -f /var/log/supervisor/mewayz-app.log
tail -f /var/log/supervisor/mewayz-worker.log

# Nginx logs
tail -f /var/log/nginx/access.log
tail -f /var/log/nginx/error.log
```

### Health Checks

```bash
# Application health
curl -H "Accept: application/json" https://your-domain.com/api/health

# Database connection
php /var/www/mewayz/artisan tinker
>>> DB::connection()->getPdo();

# Queue status
php /var/www/mewayz/artisan queue:monitor
```

## Performance Optimization

### PHP-FPM Configuration

Edit `/etc/php/8.2/fpm/pool.d/www.conf`:

```ini
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 1000
```

### MySQL Optimization

Edit `/etc/mysql/mysql.conf.d/mysqld.cnf`:

```ini
[mysqld]
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT
```

### Redis Configuration

Install and configure Redis:

```bash
sudo apt install -y redis-server
sudo systemctl enable redis-server
sudo systemctl start redis-server
```

## Security

### SSL/TLS Configuration

Use Let's Encrypt for free SSL certificates:

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com
sudo systemctl enable certbot.timer
```

### Firewall Configuration

```bash
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw --force enable
```

### Security Headers

Add to Nginx configuration:

```nginx
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-Content-Type-Options "nosniff" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
```

## Backup and Recovery

### Database Backup

```bash
# Create backup script
cat > /usr/local/bin/backup-mewayz.sh << 'EOF'
#!/bin/bash
BACKUP_DIR="/var/backups/mewayz"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR
mysqldump -u mewayz -p mewayz > $BACKUP_DIR/mewayz_$DATE.sql
gzip $BACKUP_DIR/mewayz_$DATE.sql

# Keep only last 7 days
find $BACKUP_DIR -name "*.gz" -mtime +7 -delete
EOF

chmod +x /usr/local/bin/backup-mewayz.sh

# Add to crontab
echo "0 2 * * * /usr/local/bin/backup-mewayz.sh" | sudo crontab -
```

### Application Backup

```bash
# Backup application files
tar -czf /var/backups/mewayz_app_$(date +%Y%m%d).tar.gz \
  --exclude=node_modules \
  --exclude=vendor \
  --exclude=storage/logs \
  /var/www/mewayz
```

## Troubleshooting

### Common Issues

1. **Permission Errors**
   ```bash
   sudo chown -R www-data:www-data /var/www/mewayz
   sudo chmod -R 755 /var/www/mewayz/storage
   ```

2. **Memory Issues**
   ```bash
   # Increase PHP memory limit
   echo "memory_limit = 512M" >> /etc/php/8.2/cli/php.ini
   ```

3. **Database Connection**
   ```bash
   php /var/www/mewayz/artisan config:clear
   php /var/www/mewayz/artisan cache:clear
   ```

### Support

For deployment support:
- Check the [Troubleshooting Guide](../troubleshooting/README.md)
- Review the [Developer Guide](../developer/README.md)
- Consult the main [Documentation](../README.md)