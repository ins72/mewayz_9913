# 🚀 Mewayz v2 - Quick Setup Guide

## 📋 Overview

Mewayz v2 is a professional all-in-one business platform combining:
- 🎯 Social Media Management
- 📚 Course Creation & E-learning
- 🛒 E-commerce Platform
- 👥 CRM & Customer Management
- 📊 Advanced Analytics & BI
- 💰 Payment Processing
- 🔐 Enterprise Security Features

## ⚡ Quick Start (5 Minutes)

### 1. System Requirements
- PHP 8.2+ (automatically installed)
- MySQL/MariaDB (automatically installed)
- Node.js 18+ (automatically installed)
- Composer (automatically installed)

### 2. One-Click Setup
```bash
# Clone and setup everything automatically
git clone https://github.com/your-repo/mewayz-v2.git
cd mewayz-v2
chmod +x setup.sh
./setup.sh
```

### 3. Access Your Platform
- **Main Application**: https://test.mewayz.com
- **Admin Dashboard**: https://test.mewayz.com/admin
- **API Documentation**: https://test.mewayz.com/api/docs

### 4. Default Credentials
- **Admin Email**: admin@mewayz.com
- **Admin Password**: admin123
- **Database**: mewayz_production

## 🛠️ Manual Setup (Advanced Users)

### Step 1: Environment Setup
```bash
# Update system packages
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install -y php8.2 php8.2-cli php8.2-common php8.2-mysql php8.2-curl php8.2-json php8.2-mbstring php8.2-xml php8.2-zip

# Install MySQL
sudo apt install -y mysql-server

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### Step 2: Project Setup
```bash
# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node.js dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Build frontend assets
npm run build
```

### Step 3: Database Setup
```bash
# Create database
mysql -u root -p -e "CREATE DATABASE mewayz_production;"

# Run migrations and seeders
php artisan migrate --seed

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 4: Web Server Configuration

#### Apache Configuration
```apache
<VirtualHost *:80>
    ServerName test.mewayz.com
    DocumentRoot /path/to/mewayz-v2/public
    
    <Directory /path/to/mewayz-v2/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### Nginx Configuration
```nginx
server {
    listen 80;
    server_name test.mewayz.com;
    root /path/to/mewayz-v2/public;
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
}
```

## 🔧 Configuration

### Environment Variables
Update `.env` file with your settings:

```env
APP_NAME=Mewayz
APP_ENV=production
APP_URL=https://test.mewayz.com
APP_DEBUG=false

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mewayz_production
DB_USERNAME=root
DB_PASSWORD=your_password

# Optional: Add your API keys
OPENAI_API_KEY=your_openai_key
STRIPE_KEY=your_stripe_key
STRIPE_SECRET=your_stripe_secret
```

### SSL/HTTPS Setup
```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-apache

# Get SSL certificate
sudo certbot --apache -d test.mewayz.com

# Auto-renewal
sudo crontab -e
# Add: 0 12 * * * /usr/bin/certbot renew --quiet
```

## 🚀 Production Deployment

### Using Docker (Recommended)
```bash
# Build and run with Docker
docker-compose up -d --build

# Or use the provided script
./deploy.sh
```

### Manual Deployment
```bash
# Set proper permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 755 storage bootstrap/cache

# Enable maintenance mode during deployment
php artisan down

# Update code
git pull origin main
composer install --optimize-autoloader --no-dev
npm run build

# Update database
php artisan migrate --force

# Clear and rebuild cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Disable maintenance mode
php artisan up
```

## 🔐 Security Configuration

### File Permissions
```bash
# Set secure permissions
find . -type f -name "*.php" -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod -R 755 storage bootstrap/cache
```

### Environment Security
```bash
# Secure .env file
chmod 600 .env
```

## 📚 Available Features

### 🎯 Social Media Management
- Multi-platform posting
- Content scheduling
- Analytics & insights
- Hashtag optimization

### 🛒 E-commerce Platform
- Product catalog
- Order management
- Payment processing
- Inventory tracking

### 📚 Course Creation
- Video lessons
- Quizzes & assessments
- Student progress tracking
- Certification system

### 👥 CRM System
- Contact management
- Lead tracking
- Sales pipeline
- Automation workflows

### 📊 Analytics & BI
- Real-time metrics
- Custom reports
- Data visualization
- Performance insights

## 🆘 Support

### Documentation
- **User Guide**: `/docs/user-guide/`
- **API Documentation**: `/docs/api/`
- **Developer Guide**: `/docs/developer/`

### Troubleshooting
- **Logs**: `storage/logs/laravel.log`
- **Debug Mode**: Set `APP_DEBUG=true` in `.env`
- **Health Check**: Visit `/api/health`

### Support Channels
- **Email**: support@mewayz.com
- **GitHub Issues**: [Report Issues](https://github.com/your-repo/mewayz-v2/issues)
- **Documentation**: [Full Documentation](https://docs.mewayz.com)

## 🏆 Success!

Your Mewayz v2 platform is now ready! 🎉

Visit https://test.mewayz.com to start building your business empire!

---

*Need help? Check our comprehensive documentation or contact support.*