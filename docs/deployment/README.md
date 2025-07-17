# Mewayz Platform v2 - Deployment Guide

*Last Updated: January 17, 2025*

## 🚀 **DEPLOYMENT OVERVIEW**

This comprehensive guide covers deployment strategies, infrastructure setup, and production configuration for the **Mewayz Platform v2** built on **Laravel 11 + MySQL**.

---

## 🎯 **DEPLOYMENT OPTIONS**

### 1. Traditional Server Deployment
- **Best for**: Small to medium applications
- **Requirements**: VPS/Dedicated server
- **Technologies**: LAMP/LEMP stack with Laravel 11
- **Estimated Cost**: $50-200/month

### 2. Container Deployment
- **Best for**: Scalable applications
- **Requirements**: Docker, Kubernetes
- **Technologies**: Docker, K8s, Helm
- **Estimated Cost**: $100-500/month

### 3. Cloud Platform Deployment
- **Best for**: Enterprise applications
- **Requirements**: Cloud provider account
- **Technologies**: AWS, GCP, Azure
- **Estimated Cost**: $200-1000+/month

---

## 📋 **SYSTEM REQUIREMENTS**

### Server Requirements
- **OS**: Ubuntu 22.04+ / CentOS 8+
- **PHP**: 8.2+ (required for Laravel 11)
- **Database**: MySQL 8.0+ / MariaDB 10.6+
- **Web Server**: Nginx 1.20+ / Apache 2.4+
- **Memory**: 4GB+ RAM (8GB+ recommended)
- **Storage**: 50GB+ SSD (100GB+ recommended)
- **CPU**: 2+ cores (4+ recommended)

### Laravel 11 Specific Requirements
- **PHP Extensions**: mbstring, OpenSSL, PDO, Tokenizer, XML, Ctype, JSON, BCMath, Fileinfo
- **Composer**: 2.0+
- **Node.js**: 18+ (for asset compilation)
- **Redis**: 6.0+ (for caching and sessions)

---

## 🔧 **INSTALLATION STEPS**

### 1. Server Setup
```bash
# Update system packages
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install -y nginx mysql-server php8.2-fpm php8.2-mysql php8.2-xml php8.2-curl php8.2-zip php8.2-mbstring php8.2-gd php8.2-redis redis-server

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs
```

### 2. Database Configuration
```bash
# Secure MySQL installation
sudo mysql_secure_installation

# Create database and user
sudo mysql -u root -p
```

```sql
CREATE DATABASE mewayz_v2;
CREATE USER 'mewayz_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON mewayz_v2.* TO 'mewayz_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3. Application Deployment
```bash
# Clone repository
git clone https://github.com/mewayz/platform.git /var/www/mewayz
cd /var/www/mewayz

# Install dependencies
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Set permissions
sudo chown -R www-data:www-data /var/www/mewayz
sudo chmod -R 755 /var/www/mewayz
sudo chmod -R 775 /var/www/mewayz/storage
sudo chmod -R 775 /var/www/mewayz/bootstrap/cache
```

### 4. Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database settings
nano .env
```

```env
APP_NAME="Mewayz Platform v2"
APP_ENV=production
APP_KEY=base64:generated_key_here
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=mewayz_v2
DB_USERNAME=mewayz_user
DB_PASSWORD=secure_password

REDIS_HOST=localhost
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null

STRIPE_KEY=your_stripe_key
STRIPE_SECRET=your_stripe_secret
```

### 5. Database Migration
```bash
# Run migrations
php artisan migrate --force

# Seed database (optional)
php artisan db:seed --force

# Optimize application
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🌐 **WEB SERVER CONFIGURATION**

### Nginx Configuration
```nginx
# /etc/nginx/sites-available/mewayz
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/mewayz/public;
    
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
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

### SSL Configuration (Let's Encrypt)
```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Obtain SSL certificate
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# Auto-renewal
sudo crontab -e
# Add: 0 12 * * * /usr/bin/certbot renew --quiet
```

---

## 🐳 **DOCKER DEPLOYMENT**

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
    nginx

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www
RUN chmod -R 755 /var/www/storage

EXPOSE 80

CMD ["php-fpm"]
```

### Docker Compose
```yaml
version: '3.8'

services:
  app:
    build: .
    container_name: mewayz_app
    working_dir: /var/www
    volumes:
      - ./:/var/www
    depends_on:
      - mysql
      - redis
    networks:
      - mewayz

  nginx:
    image: nginx:alpine
    container_name: mewayz_nginx
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./:/var/www
      - ./docker/nginx/nginx.conf:/etc/nginx/nginx.conf
    depends_on:
      - app
    networks:
      - mewayz

  mysql:
    image: mysql:8.0
    container_name: mewayz_mysql
    environment:
      MYSQL_ROOT_PASSWORD: root_password
      MYSQL_DATABASE: mewayz_v2
      MYSQL_USER: mewayz_user
      MYSQL_PASSWORD: secure_password
    volumes:
      - mysql_data:/var/lib/mysql
    networks:
      - mewayz

  redis:
    image: redis:7-alpine
    container_name: mewayz_redis
    networks:
      - mewayz

volumes:
  mysql_data:

networks:
  mewayz:
    driver: bridge
```

---

## ☁️ **CLOUD DEPLOYMENT**

### AWS Deployment
```bash
# Install AWS CLI
curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "awscliv2.zip"
unzip awscliv2.zip
sudo ./aws/install

# Configure AWS credentials
aws configure

# Deploy using Elastic Beanstalk
eb init
eb create mewayz-production
eb deploy
```

### Google Cloud Platform
```bash
# Install Google Cloud SDK
curl https://sdk.cloud.google.com | bash
exec -l $SHELL

# Initialize project
gcloud init

# Deploy to App Engine
gcloud app deploy
```

---

## 🔍 **MONITORING & LOGGING**

### System Monitoring
```bash
# Install monitoring tools
sudo apt install -y htop iotop nethogs

# Setup log rotation
sudo nano /etc/logrotate.d/mewayz
```

### Application Logging
```php
// config/logging.php
'channels' => [
    'single' => [
        'driver' => 'single',
        'path' => storage_path('logs/laravel.log'),
        'level' => env('LOG_LEVEL', 'debug'),
    ],
    'daily' => [
        'driver' => 'daily',
        'path' => storage_path('logs/laravel.log'),
        'level' => env('LOG_LEVEL', 'debug'),
        'days' => 14,
    ],
]
```

---

## 🚀 **PERFORMANCE OPTIMIZATION**

### Database Optimization
```sql
-- Enable MySQL query cache
SET GLOBAL query_cache_size = 1048576;
SET GLOBAL query_cache_type = ON;

-- Optimize table indexes
OPTIMIZE TABLE users, workspaces, social_media_posts;
```

### Application Optimization
```bash
# Cache optimization
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Queue processing
php artisan queue:work --daemon

# Schedule jobs
* * * * * cd /var/www/mewayz && php artisan schedule:run >> /dev/null 2>&1
```

### CDN Configuration
```bash
# Configure AWS CloudFront
aws cloudfront create-distribution \
    --distribution-config file://distribution-config.json
```

---

## 🔄 **BACKUP & RECOVERY**

### Database Backup
```bash
#!/bin/bash
# backup.sh
BACKUP_DIR="/var/backups/mewayz"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR

# Create database backup
mysqldump -u mewayz_user -p mewayz_v2 > $BACKUP_DIR/db_backup_$DATE.sql

# Create application backup
tar -czf $BACKUP_DIR/app_backup_$DATE.tar.gz /var/www/mewayz

# Upload to S3 (optional)
aws s3 cp $BACKUP_DIR/db_backup_$DATE.sql s3://mewayz-backups/
```

### Automated Backup
```bash
# Add to crontab
0 2 * * * /path/to/backup.sh
```

---

## 🛡️ **SECURITY HARDENING**

### Server Security
```bash
# Configure firewall
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'
sudo ufw --force enable

# Disable root login
sudo nano /etc/ssh/sshd_config
# Set: PermitRootLogin no

# Install fail2ban
sudo apt install fail2ban
sudo systemctl enable fail2ban
```

### Application Security
```php
// config/session.php
'secure' => env('SESSION_SECURE_COOKIE', true),
'http_only' => true,
'same_site' => 'strict',
```

---

## 🎉 **DEPLOYMENT CHECKLIST**

### Pre-Deployment
- [ ] Server requirements met
- [ ] Database configured
- [ ] Environment variables set
- [ ] SSL certificate installed
- [ ] Backup system configured

### Post-Deployment
- [ ] Application accessible
- [ ] Database migrations completed
- [ ] Caching configured
- [ ] Monitoring setup
- [ ] Security hardening applied

### Testing
- [ ] Health check endpoint responds
- [ ] User registration works
- [ ] API endpoints functional
- [ ] Payment processing tested
- [ ] Email functionality verified

---

## 📞 **SUPPORT**

### Deployment Support
- **Email**: deployment@mewayz.com
- **Documentation**: https://docs.mewayz.com
- **Status Page**: https://status.mewayz.com

### Common Issues
- **Port conflicts**: Check nginx/apache configuration
- **Permission errors**: Verify file permissions
- **Database connection**: Check MySQL credentials
- **Asset compilation**: Verify Node.js version

---

*Last Updated: January 17, 2025*
*Platform Version: v2.0.0*
*Framework: Laravel 11 + MySQL*
*Status: Production-Ready*