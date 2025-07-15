# 🛠️ Mewayz Platform Installation Guide

*Complete Installation Guide for Development and Production Environments*

## 📋 Overview

This guide provides comprehensive installation instructions for the Mewayz Platform across different environments. Whether you're setting up a development environment or deploying to production, this guide covers all necessary steps.

## 🎯 Quick Start

### Option 1: Automated Installation
```bash
# Download and run installer
curl -fsSL https://raw.githubusercontent.com/mewayz/platform/main/install.sh | bash

# Or with specific environment
curl -fsSL https://raw.githubusercontent.com/mewayz/platform/main/install.sh | bash -s -- --env=production
```

### Option 2: Manual Installation
Follow the detailed steps below for complete control over the installation process.

## 🔧 System Requirements

### Minimum Requirements
- **Operating System**: Ubuntu 20.04 LTS, CentOS 8, or macOS 11+
- **RAM**: 2GB minimum, 4GB recommended
- **CPU**: 2 cores minimum, 4 cores recommended
- **Storage**: 10GB minimum, 20GB recommended
- **Network**: Stable internet connection

### Software Requirements
- **PHP**: 8.1 or higher
- **MySQL**: 8.0 or higher (or MariaDB 10.6+)
- **Redis**: 6.0 or higher
- **Composer**: 2.0 or higher
- **Node.js**: 16.0 or higher
- **Flutter**: 3.0 or higher (for mobile development)

## 🏠 Local Development Setup

### Step 1: Install Dependencies

#### Ubuntu/Debian
```bash
# Update package manager
sudo apt update && sudo apt upgrade -y

# Install PHP and extensions
sudo apt install -y php8.1 php8.1-cli php8.1-fpm php8.1-mysql php8.1-xml \
php8.1-curl php8.1-mbstring php8.1-zip php8.1-gd php8.1-bcmath \
php8.1-redis php8.1-intl php8.1-soap php8.1-sqlite3

# Install MySQL
sudo apt install -y mysql-server mysql-client

# Install Redis
sudo apt install -y redis-server

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 2. PHP Dependencies Installation

```bash
# Install PHP dependencies via Composer
composer install

# If you encounter memory issues
composer install --no-dev --optimize-autoloader

# For production
composer install --no-dev --optimize-autoloader --no-scripts
```

### 3. Node.js Dependencies Installation

```bash
# Install Node.js dependencies
npm install

# Alternative with Yarn
yarn install

# For production
npm ci --only=production
```

### 4. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Set proper permissions
chmod 644 .env
```

### 5. Database Setup

```bash
# Create database (MySQL/MariaDB)
mysql -u root -p -e "CREATE DATABASE mewayz CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Create database user (optional but recommended)
mysql -u root -p -e "CREATE USER 'mewayz'@'localhost' IDENTIFIED BY 'your_secure_password';"
mysql -u root -p -e "GRANT ALL PRIVILEGES ON mewayz.* TO 'mewayz'@'localhost';"
mysql -u root -p -e "FLUSH PRIVILEGES;"
```

### 6. Run Database Migrations

```bash
# Run database migrations
php artisan migrate

# Seed the database (optional)
php artisan db:seed

# For production (skip seeding)
php artisan migrate --force
```

### 7. Build Frontend Assets

```bash
# Build development assets
npm run dev

# Build production assets
npm run build

# Watch for changes (development)
npm run watch
```

### 8. Set Directory Permissions

```bash
# Set storage permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Set proper ownership (Linux/macOS)
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache
```

---

## ⚙️ Configuration

### Environment Variables

Edit your `.env` file with the following configuration:

```env
# Application Configuration
APP_NAME=Mewayz
APP_ENV=production
APP_KEY=your_generated_key_here
APP_DEBUG=false
APP_URL=https://mewayz.com

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mewayz
DB_USERNAME=mewayz
DB_PASSWORD=your_secure_password

# Cache Configuration
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis Configuration
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls

# OAuth Configuration
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret

FACEBOOK_CLIENT_ID=your_facebook_client_id
FACEBOOK_CLIENT_SECRET=your_facebook_client_secret

# File Storage
FILESYSTEM_DISK=local

# Broadcasting
BROADCAST_DRIVER=log
```

### OAuth Setup

#### Google OAuth Setup
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing
3. Enable Google+ API
4. Create OAuth 2.0 credentials
5. Add your domain to authorized domains
6. Set redirect URI: `https://yourdomain.com/auth/google/callback`

#### Facebook OAuth Setup
1. Go to [Facebook Developers](https://developers.facebook.com/)
2. Create a new app
3. Add Facebook Login product
4. Set valid OAuth redirect URIs
5. Add your domain to App Domains

---

## 🗄️ Database Setup

### MySQL Configuration

```sql
-- Create database
CREATE DATABASE mewayz CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user
CREATE USER 'mewayz'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON mewayz.* TO 'mewayz'@'localhost';
FLUSH PRIVILEGES;

-- Verify setup
SHOW DATABASES;
SELECT User, Host FROM mysql.user WHERE User = 'mewayz';
```

### Database Optimization

```sql
-- Optimize for performance
SET innodb_buffer_pool_size = 1G;
SET query_cache_size = 128M;
SET max_connections = 1000;

-- Create indexes for better performance
USE mewayz;
ALTER TABLE users ADD INDEX idx_email (email);
ALTER TABLE sessions ADD INDEX idx_last_activity (last_activity);
```

---

## 🎨 Frontend Setup

### Laravel Frontend

```bash
# Install Laravel frontend dependencies
npm install

# Build assets for development
npm run dev

# Build assets for production
npm run build

# Watch for changes
npm run watch
```

### Flutter Setup

```bash
# Navigate to Flutter app
cd flutter_app

# Get Flutter dependencies
flutter pub get

# Build for web
flutter build web

# Build for mobile
flutter build apk
flutter build ios
```

### PWA Configuration

```bash
# Generate PWA manifest
php artisan make:manifest

# Create service worker
php artisan make:sw

# Build PWA assets
npm run build:pwa
```

---

## 🔧 Service Configuration

### Laravel Configuration

```bash
# Optimize Laravel for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Clear caches (if needed)
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

### Supervisor Configuration

Create `/etc/supervisor/conf.d/mewayz.conf`:

```ini
[program:mewayz]
command=php artisan serve --host=0.0.0.0 --port=8001
directory=/path/to/mewayz
autostart=true
autorestart=true
user=www-data
stderr_logfile=/var/log/supervisor/mewayz.err.log
stdout_logfile=/var/log/supervisor/mewayz.out.log
```

### Nginx Configuration

Create `/etc/nginx/sites-available/mewayz`:

```nginx
server {
    listen 80;
    server_name mewayz.com www.mewayz.com;
    root /path/to/mewayz/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

### SSL Configuration

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Generate SSL certificate
sudo certbot --nginx -d mewayz.com -d www.mewayz.com

# Auto-renew
sudo crontab -e
# Add: 0 12 * * * /usr/bin/certbot renew --quiet
```

---

## ✅ Verification

### Health Checks

```bash
# Check application health
curl http://localhost:8001/api/health

# Check database connection
php artisan tinker
>>> DB::connection()->getPdo();

# Check cache
php artisan cache:clear
redis-cli ping
```

### Service Status

```bash
# Check services
sudo systemctl status nginx
sudo systemctl status mysql
sudo systemctl status redis
sudo supervisorctl status

# Check logs
tail -f /var/log/nginx/access.log
tail -f /var/log/supervisor/mewayz.out.log
```

### Performance Testing

```bash
# Run performance tests
php artisan test --filter=Performance

# Load testing (optional)
ab -n 1000 -c 10 http://localhost:8001/
```

---

## 🔍 Troubleshooting

### Common Issues

#### Database Connection Issues
```bash
# Check database status
sudo systemctl status mysql

# Check connection
mysql -u mewayz -p mewayz

# Reset password
sudo mysql -u root -p
ALTER USER 'mewayz'@'localhost' IDENTIFIED BY 'new_password';
FLUSH PRIVILEGES;
```

#### Permission Issues
```bash
# Fix storage permissions
sudo chown -R www-data:www-data storage
sudo chown -R www-data:www-data bootstrap/cache
sudo chmod -R 755 storage
sudo chmod -R 755 bootstrap/cache
```

#### Composer Issues
```bash
# Clear composer cache
composer clear-cache

# Update dependencies
composer update

# Install with no scripts
composer install --no-scripts
```

#### NPM Issues
```bash
# Clear npm cache
npm cache clean --force

# Delete node_modules and reinstall
rm -rf node_modules package-lock.json
npm install
```

### Log Locations

```bash
# Laravel logs
tail -f storage/logs/laravel.log

# Nginx logs
tail -f /var/log/nginx/access.log
tail -f /var/log/nginx/error.log

# MySQL logs
tail -f /var/log/mysql/error.log

# Supervisor logs
tail -f /var/log/supervisor/mewayz.out.log
```

### Performance Issues

```bash
# Check memory usage
free -h

# Check disk usage
df -h

# Check CPU usage
top

# Optimize Laravel
php artisan optimize
```

---

## 📚 Next Steps

After successful installation:

1. **Configure OAuth**: Set up Google, Facebook, and other OAuth providers
2. **Set up monitoring**: Configure application monitoring
3. **Backup setup**: Set up automated backups
4. **Security hardening**: Follow security best practices
5. **Performance tuning**: Optimize for your specific use case

---

## 📞 Support

If you encounter issues during installation:

- **Documentation**: Check our [Troubleshooting Guide](TROUBLESHOOTING.md)
- **Community**: Join our community discussions
- **Professional Support**: Contact support@mewayz.com

---

*Mewayz Platform - Professional Installation Guide*  
*Built by Mewayz Technologies Inc.*  
*Creating seamless business solutions for the modern digital world*