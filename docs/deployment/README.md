# 🚀 Deployment Guide

This guide covers deployment strategies, infrastructure setup, and production configuration for the Mewayz platform.

## 🎯 Deployment Options

### 1. Traditional Server Deployment
- **Best for**: Small to medium applications
- **Requirements**: VPS/Dedicated server
- **Technologies**: LAMP/LEMP stack

### 2. Container Deployment
- **Best for**: Scalable applications
- **Requirements**: Docker, Kubernetes
- **Technologies**: Docker, K8s, Helm

### 3. Cloud Platform Deployment
- **Best for**: Enterprise applications
- **Requirements**: Cloud provider account
- **Technologies**: AWS, GCP, Azure

## 📋 Prerequisites

### Server Requirements
- **OS**: Ubuntu 20.04+ / CentOS 8+
- **PHP**: 8.2+
- **Database**: MySQL 8.0+ / PostgreSQL 13+
- **Web Server**: Nginx 1.18+ / Apache 2.4+
- **Memory**: 2GB+ RAM
- **Storage**: 20GB+ SSD
- **Node.js**: 18+ (for asset compilation)

### Software Dependencies
```bash
# PHP Extensions
php-cli php-fpm php-mysql php-mbstring php-xml php-curl
php-zip php-gd php-intl php-bcmath php-soap php-redis

# System Tools
git curl wget unzip supervisor redis-server
```

## 🔧 Server Setup

### 1. Update System
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y software-properties-common
```

### 2. Install PHP 8.2
```bash
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install -y php8.2 php8.2-cli php8.2-fpm php8.2-mysql \
  php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip \
  php8.2-gd php8.2-intl php8.2-bcmath php8.2-soap php8.2-redis
```

### 3. Install MySQL
```bash
sudo apt install -y mysql-server
sudo mysql_secure_installation
```

### 4. Install Redis
```bash
sudo apt install -y redis-server
sudo systemctl enable redis-server
sudo systemctl start redis-server
```

### 5. Install Nginx
```bash
sudo apt install -y nginx
sudo systemctl enable nginx
sudo systemctl start nginx
```

### 6. Install Node.js
```bash
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
```

### 7. Install Composer
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
```

## 📁 Application Deployment

### 1. Clone Repository
```bash
sudo mkdir -p /var/www/mewayz
sudo chown -R $USER:$USER /var/www/mewayz
cd /var/www/mewayz
git clone https://github.com/your-org/mewayz.git .
```

### 2. Install Dependencies
```bash
composer install --optimize-autoloader --no-dev
npm install
npm run build
```

### 3. Set Permissions
```bash
sudo chown -R www-data:www-data /var/www/mewayz
sudo chmod -R 755 /var/www/mewayz
sudo chmod -R 775 /var/www/mewayz/storage
sudo chmod -R 775 /var/www/mewayz/bootstrap/cache
```

### 4. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` file:
```env
APP_NAME=Mewayz
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mewayz
DB_USERNAME=mewayz_user
DB_PASSWORD=secure_password

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
```

### 5. Database Setup
```bash
# Create database
mysql -u root -p
CREATE DATABASE mewayz;
CREATE USER 'mewayz_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON mewayz.* TO 'mewayz_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Run migrations
php artisan migrate --force
php artisan db:seed --force
```

### 6. Optimize Application
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

## 🌐 Web Server Configuration

### Nginx Configuration
Create `/etc/nginx/sites-available/mewayz`:
```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/mewayz/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

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
}
```

Enable site:
```bash
sudo ln -s /etc/nginx/sites-available/mewayz /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### SSL Certificate (Let's Encrypt)
```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com -d www.your-domain.com
```

## 🔄 Process Management

### Supervisor Configuration
Create `/etc/supervisor/conf.d/mewayz-worker.conf`:
```ini
[program:mewayz-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/mewayz/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/var/www/mewayz/storage/logs/worker.log
stopwaitsecs=3600
```

Start supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start mewayz-worker:*
```

### Cron Jobs
Add to crontab:
```bash
sudo crontab -e
```

Add:
```
* * * * * cd /var/www/mewayz && php artisan schedule:run >> /dev/null 2>&1
```

## 🐳 Docker Deployment

### Dockerfile
```dockerfile
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx \
    supervisor

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application code
COPY . .

# Install dependencies
RUN composer install --optimize-autoloader --no-dev
RUN npm install && npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www
RUN chmod -R 755 /var/www/storage

# Copy configuration files
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose port
EXPOSE 80

# Start supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
```

### Docker Compose
```yaml
version: '3.8'
services:
  app:
    build: .
    ports:
      - "80:80"
    depends_on:
      - db
      - redis
    environment:
      - APP_ENV=production
      - DB_HOST=db
      - REDIS_HOST=redis
    volumes:
      - ./storage:/var/www/storage

  db:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: mewayz
      MYSQL_USER: mewayz_user
      MYSQL_PASSWORD: secure_password
    volumes:
      - db_data:/var/lib/mysql

  redis:
    image: redis:alpine
    ports:
      - "6379:6379"

volumes:
  db_data:
```

## ☁️ Cloud Deployment

### AWS Deployment
#### Using AWS Elastic Beanstalk
1. Install EB CLI
2. Initialize application
3. Configure environment
4. Deploy application

#### Using AWS ECS
1. Create ECS cluster
2. Define task definition
3. Create service
4. Configure load balancer

### Google Cloud Platform
#### Using Google App Engine
1. Create app.yaml
2. Configure services
3. Deploy application

#### Using Google Kubernetes Engine
1. Create cluster
2. Configure deployment
3. Set up services

### Azure Deployment
#### Using Azure App Service
1. Create resource group
2. Create app service
3. Configure deployment
4. Set up CI/CD

## 🔧 Production Configuration

### Environment Variables
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

SESSION_DRIVER=redis
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis

LOG_CHANNEL=daily
LOG_LEVEL=error

MAIL_MAILER=smtp
BROADCAST_DRIVER=redis
```

### Security Headers
```nginx
add_header X-Frame-Options "SAMEORIGIN";
add_header X-Content-Type-Options "nosniff";
add_header X-XSS-Protection "1; mode=block";
add_header Strict-Transport-Security "max-age=31536000; includeSubDomains";
add_header Content-Security-Policy "default-src 'self'";
```

### Performance Optimization
```nginx
# Gzip compression
gzip on;
gzip_vary on;
gzip_min_length 1024;
gzip_proxied expired no-cache no-store private must-revalidate auth;
gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss;

# Browser caching
location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
    expires 1y;
    add_header Cache-Control "public, immutable";
}
```

## 📊 Monitoring & Logging

### Application Monitoring
```php
// config/logging.php
'daily' => [
    'driver' => 'daily',
    'path' => storage_path('logs/laravel.log'),
    'level' => env('LOG_LEVEL', 'debug'),
    'days' => 14,
],
```

### Health Checks
```php
// routes/web.php
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now(),
        'version' => config('app.version'),
    ]);
});
```

### Log Rotation
```bash
# /etc/logrotate.d/mewayz
/var/www/mewayz/storage/logs/*.log {
    daily
    missingok
    rotate 52
    compress
    delaycompress
    notifempty
    create 0644 www-data www-data
}
```

## 🚀 CI/CD Pipeline

### GitHub Actions
```yaml
name: Deploy to Production

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
    - uses: actions/checkout@v2
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.2'
        
    - name: Install dependencies
      run: composer install --optimize-autoloader --no-dev
      
    - name: Run tests
      run: php artisan test
      
    - name: Build assets
      run: |
        npm install
        npm run build
        
    - name: Deploy to server
      uses: appleboy/ssh-action@v0.1.5
      with:
        host: ${{ secrets.HOST }}
        username: ${{ secrets.USERNAME }}
        key: ${{ secrets.KEY }}
        script: |
          cd /var/www/mewayz
          git pull origin main
          composer install --optimize-autoloader --no-dev
          npm install
          npm run build
          php artisan migrate --force
          php artisan config:cache
          php artisan route:cache
          php artisan view:cache
          sudo supervisorctl restart mewayz-worker:*
```

## 🛡️ Security Best Practices

### Server Security
```bash
# Disable root login
sudo sed -i 's/PermitRootLogin yes/PermitRootLogin no/' /etc/ssh/sshd_config

# Configure firewall
sudo ufw allow ssh
sudo ufw allow 80
sudo ufw allow 443
sudo ufw enable

# Install fail2ban
sudo apt install -y fail2ban
```

### Application Security
```php
// config/session.php
'secure' => env('SESSION_SECURE_COOKIE', true),
'http_only' => true,
'same_site' => 'strict',

// config/cors.php
'allowed_origins' => ['https://your-domain.com'],
'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE'],
```

## 🔄 Backup Strategy

### Database Backup
```bash
#!/bin/bash
# backup.sh
BACKUP_DIR="/var/backups/mewayz"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="mewayz"
DB_USER="mewayz_user"
DB_PASSWORD="secure_password"

mkdir -p $BACKUP_DIR
mysqldump -u $DB_USER -p$DB_PASSWORD $DB_NAME > $BACKUP_DIR/db_backup_$DATE.sql
gzip $BACKUP_DIR/db_backup_$DATE.sql

# Keep only last 7 days
find $BACKUP_DIR -name "*.sql.gz" -mtime +7 -delete
```

### File Backup
```bash
#!/bin/bash
# file_backup.sh
BACKUP_DIR="/var/backups/mewayz"
DATE=$(date +%Y%m%d_%H%M%S)
APP_DIR="/var/www/mewayz"

tar -czf $BACKUP_DIR/files_backup_$DATE.tar.gz $APP_DIR/storage/app/public
find $BACKUP_DIR -name "files_backup_*.tar.gz" -mtime +7 -delete
```

## 📋 Deployment Checklist

### Pre-deployment
- [ ] Code review completed
- [ ] Tests passing
- [ ] Database migrations tested
- [ ] Environment variables configured
- [ ] SSL certificate installed
- [ ] Backup created

### Post-deployment
- [ ] Application accessible
- [ ] Database migrated successfully
- [ ] Queue workers running
- [ ] Cron jobs configured
- [ ] Monitoring active
- [ ] Performance optimized

### Rollback Plan
- [ ] Database backup available
- [ ] Previous version tagged
- [ ] Rollback procedure documented
- [ ] Monitoring alerts configured

---

**Need Help?**
- 📧 DevOps Support: devops@mewayz.com
- 📚 Infrastructure Documentation: [docs.mewayz.com/infrastructure](https://docs.mewayz.com/infrastructure)
- 🔧 Status Page: [status.mewayz.com](https://status.mewayz.com)

**Last Updated**: January 2025  
**Version**: 1.0.0