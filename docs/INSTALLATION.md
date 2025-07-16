# Mewayz Platform - Installation Guide

This guide provides step-by-step instructions for installing and setting up the Mewayz platform in various environments.

## 🎯 Prerequisites

### System Requirements
- **PHP**: 8.2 or higher
- **Node.js**: 18.0 or higher
- **Composer**: 2.0 or higher
- **Database**: MariaDB 10.6+ or MySQL 8.0+
- **Memory**: 512MB minimum, 2GB recommended
- **Storage**: 1GB free space

### Required PHP Extensions
```bash
# Core extensions
php8.2-cli
php8.2-fpm
php8.2-mysql
php8.2-xml
php8.2-mbstring
php8.2-curl
php8.2-zip
php8.2-bcmath
php8.2-gd
php8.2-json
php8.2-intl
```

### Third-Party Services
- **Stripe Account** - For payment processing
- **Email Service** - For notifications (optional)
- **CDN** - For asset delivery (optional)

## 🚀 Installation Methods

### Method 1: Standard Installation

#### Step 1: Clone Repository
```bash
git clone https://github.com/your-org/mewayz.git
cd mewayz
```

#### Step 2: Install Dependencies
```bash
# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node.js dependencies
npm install
```

#### Step 3: Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

#### Step 4: Database Setup
```bash
# Create database
mysql -u root -p -e "CREATE DATABASE mewayz CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations
php artisan migrate --force

# Seed database
php artisan db:seed --class=AdminSeeder
```

#### Step 5: Build Assets
```bash
# Build for production
npm run build

# Or for development
npm run dev
```

#### Step 6: Set Permissions
```bash
# Set storage permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Set ownership (if running as different user)
chown -R www-data:www-data storage bootstrap/cache
```

### Method 2: Docker Installation (Recommended)

#### Step 1: Docker Setup
```bash
# Clone repository
git clone https://github.com/your-org/mewayz.git
cd mewayz

# Build Docker image
docker-compose build

# Start services
docker-compose up -d
```

#### Step 2: Container Setup
```bash
# Install dependencies inside container
docker-compose exec app composer install
docker-compose exec app npm install

# Run migrations
docker-compose exec app php artisan migrate --seed
```

### Method 3: Kubernetes Deployment

#### Step 1: Prepare Kubernetes Manifests
```yaml
# deployment.yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: mewayz-app
spec:
  replicas: 3
  selector:
    matchLabels:
      app: mewayz
  template:
    metadata:
      labels:
        app: mewayz
    spec:
      containers:
      - name: mewayz
        image: mewayz:latest
        ports:
        - containerPort: 8001
        env:
        - name: DB_HOST
          value: "mysql-service"
        - name: APP_ENV
          value: "production"
```

#### Step 2: Deploy to Kubernetes
```bash
# Apply manifests
kubectl apply -f k8s/

# Check deployment status
kubectl get pods -l app=mewayz
```

## ⚙️ Configuration

### Environment Variables

#### Core Application Settings
```env
# Application
APP_NAME=Mewayz
APP_ENV=production
APP_KEY=base64:your-generated-key
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mewayz
DB_USERNAME=mewayz_user
DB_PASSWORD=secure_password

# Cache & Session
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

#### Payment Configuration
```env
# Stripe Configuration
STRIPE_KEY=pk_live_your_publishable_key
STRIPE_SECRET=sk_live_your_secret_key
STRIPE_API_KEY=sk_live_your_secret_key
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret
```

#### Email Configuration
```env
# Mail Settings
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@mewayz.com
MAIL_FROM_NAME=Mewayz
```

#### Storage Configuration
```env
# File Storage
FILESYSTEM_DISK=local
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false
```

### Web Server Configuration

#### Apache Configuration
```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /var/www/mewayz/public
    
    <Directory /var/www/mewayz/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/mewayz_error.log
    CustomLog ${APACHE_LOG_DIR}/mewayz_access.log combined
</VirtualHost>
```

#### Nginx Configuration
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/mewayz/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Database Configuration

#### MySQL/MariaDB Setup
```sql
-- Create database
CREATE DATABASE mewayz CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user
CREATE USER 'mewayz_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON mewayz.* TO 'mewayz_user'@'localhost';
FLUSH PRIVILEGES;
```

#### Database Optimization
```sql
-- Optimize for Laravel
SET GLOBAL innodb_buffer_pool_size = 1G;
SET GLOBAL query_cache_size = 256M;
SET GLOBAL max_connections = 1000;
```

## 🔧 Post-Installation Setup

### Step 1: Admin User Creation
```bash
# Create admin user
php artisan tinker
>>> App\Models\User::create([
...     'name' => 'Admin User',
...     'email' => 'admin@mewayz.com',
...     'password' => bcrypt('secure_password'),
...     'email_verified_at' => now(),
... ]);
```

### Step 2: Storage Setup
```bash
# Create storage link
php artisan storage:link

# Set up file permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### Step 3: Cache Configuration
```bash
# Clear all caches
php artisan optimize:clear

# Optimize for production
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 4: Queue Setup
```bash
# Install supervisor
apt-get install supervisor

# Create queue worker configuration
sudo nano /etc/supervisor/conf.d/mewayz-worker.conf
```

#### Supervisor Configuration
```ini
[program:mewayz-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/mewayz/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=8
redirect_stderr=true
stdout_logfile=/var/log/mewayz-worker.log
stopwaitsecs=3600
```

### Step 5: SSL Configuration
```bash
# Install Certbot
apt-get install certbot python3-certbot-apache

# Get SSL certificate
certbot --apache -d your-domain.com
```

## 🧪 Verification

### Step 1: Basic Health Check
```bash
# Test application
curl -I https://your-domain.com

# Check database connection
php artisan tinker
>>> DB::connection()->getPdo();
```

### Step 2: Payment Integration Test
```bash
# Test Stripe configuration
php artisan tinker
>>> \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
>>> \Stripe\Account::retrieve();
```

### Step 3: Run Test Suite
```bash
# Run all tests
php artisan test

# Run specific test suites
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit
```

## 🔍 Troubleshooting

### Common Issues

#### Permission Errors
```bash
# Fix storage permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 755 storage bootstrap/cache
```

#### Database Connection Issues
```bash
# Check database connection
php artisan tinker
>>> DB::connection()->getPdo();

# Test with different credentials
mysql -u mewayz_user -p mewayz
```

#### Asset Compilation Issues
```bash
# Clear node modules
rm -rf node_modules package-lock.json
npm install

# Rebuild assets
npm run build
```

#### Cache Issues
```bash
# Clear all caches
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Performance Optimization

#### PHP Configuration
```ini
# php.ini optimizations
memory_limit = 256M
max_execution_time = 300
upload_max_filesize = 64M
post_max_size = 64M
max_input_vars = 3000
```

#### Database Optimization
```sql
-- Index optimization
ANALYZE TABLE users, payment_transactions, sites;
OPTIMIZE TABLE users, payment_transactions, sites;
```

## 📈 Production Deployment

### Step 1: Environment Preparation
```bash
# Set production environment
APP_ENV=production
APP_DEBUG=false

# Configure caching
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### Step 2: Security Hardening
```bash
# Set strict file permissions
find /var/www/mewayz -type f -exec chmod 644 {} \;
find /var/www/mewayz -type d -exec chmod 755 {} \;
chmod -R 755 storage bootstrap/cache
```

### Step 3: Monitoring Setup
```bash
# Install monitoring tools
apt-get install htop iotop nethogs

# Set up log rotation
sudo nano /etc/logrotate.d/mewayz
```

### Step 4: Backup Configuration
```bash
# Create backup script
sudo nano /usr/local/bin/mewayz-backup.sh
chmod +x /usr/local/bin/mewayz-backup.sh

# Add to crontab
crontab -e
0 2 * * * /usr/local/bin/mewayz-backup.sh
```

## 📞 Support

### Getting Help
- **Documentation**: Check [docs/](../README.md)
- **Troubleshooting**: See [TROUBLESHOOTING.md](TROUBLESHOOTING.md)
- **Issues**: Report via GitHub Issues
- **Email**: support@mewayz.com

### Version Information
- **Current Version**: 2.0
- **Laravel Version**: 10.48
- **PHP Version**: 8.2+
- **Database**: MariaDB 10.6+

---

**Last Updated**: January 16, 2025  
**Installation Support**: Available 24/7