# 🌐 Mewayz v2 - CloudPanel Deployment

## 🚀 CloudPanel Setup (Recommended)

This setup is optimized for CloudPanel and can be deployed directly to your domain root.

### **Prerequisites:**
- CloudPanel server
- PHP 8.2+
- MySQL 8.0+
- Node.js 18+
- Composer

### **Step 1: Create Site in CloudPanel**

1. **Login to CloudPanel**
2. **Create New Site**:
   - Domain: `test.mewayz.com` (or your domain)
   - PHP Version: `8.2`
   - Document Root: `/htdocs`
3. **Create Database**:
   - Database Name: `mewayz-test`
   - Username: `mewayz-test`  
   - Password: `YaVOnrH9g4Me9HJF4fxY`

### **Step 2: Deploy Application**

SSH into your CloudPanel server:

```bash
# Navigate to your site directory
cd /home/mewayz/htdocs/test.mewayz.com

# Remove default files
rm -rf *

# Clone Mewayz v2
git clone https://github.com/ins72/mewayz_9913.git .

# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node.js dependencies  
npm install
npm run build

# Set permissions (CloudPanel specific)
chown -R mewayz:mewayz storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### **Step 3: Configure Environment**

Create your `.env` file:

```bash
cp .env.example .env
nano .env
```

Update with your settings:
```env
APP_NAME=Mewayz
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://test.mewayz.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mewayz-test
DB_USERNAME=mewayz-test
DB_PASSWORD=YaVOnrH9g4Me9HJF4fxY

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

### **Step 4: Complete Setup**

```bash
# Generate application key
php artisan key:generate

# Run database setup
php artisan migrate --seed

# Create admin user
php artisan tinker --execute="
App\Models\User::create([
    'name' => 'Admin User',
    'email' => 'admin@test.mewayz.com', 
    'password' => bcrypt('your_password'),
    'email_verified_at' => now(),
    'is_admin' => true
]);
"

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### **Step 5: CloudPanel Nginx Configuration**

In CloudPanel, update your site's Nginx config:

1. **Go to**: Sites → Your Site → Nginx
2. **Add this configuration**:

```nginx
# Add to your server block
location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;
    fastcgi_hide_header X-Powered-By;
    fastcgi_read_timeout 300;
    fastcgi_send_timeout 300;
}

# Security headers
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header X-Content-Type-Options "nosniff" always;

# Static files caching
location ~* \.(jpg|jpeg|gif|png|svg|ico|css|js|woff|woff2|ttf|eot)$ {
    expires 1y;
    add_header Cache-Control "public, immutable";
    access_log off;
}
```

3. **Click "Save & Reload Nginx"**

## 🎯 **CloudPanel Advantages:**

### **✅ Simplified Deployment:**
- No Docker complexity
- Direct file system access
- Standard Laravel deployment
- Easy database management

### **✅ CloudPanel Features:**
- **SSL/HTTPS**: Automatic Let's Encrypt
- **PHP Management**: Easy version switching
- **Database GUI**: phpMyAdmin included
- **File Manager**: Web-based file management
- **Backup System**: Automated backups
- **Monitoring**: Built-in server monitoring

### **✅ Production Ready:**
- **Nginx Optimization**: Pre-configured for performance
- **PHP-FPM**: Optimized PHP processing  
- **Security**: CloudPanel security hardening
- **Updates**: Easy application updates via git

## 🔧 **Management Commands:**

### **Update Application:**
```bash
cd /home/mewayz/htdocs/test.mewayz.com

# Pull latest changes
git pull origin main

# Update dependencies
composer install --no-dev --optimize-autoloader
npm install && npm run build

# Run migrations
php artisan migrate --force

# Clear cache
php artisan optimize:clear
php artisan optimize
```

### **Database Management:**
```bash
# Backup database
mysqldump -u mewayz-test -p mewayz-test > backup.sql

# Restore database  
mysql -u mewayz-test -p mewayz-test < backup.sql

# Access database
mysql -u mewayz-test -p mewayz-test
```

### **Logs & Debugging:**
```bash
# View Laravel logs
tail -f storage/logs/laravel.log

# View Nginx logs (via CloudPanel)
# Go to: Sites → Your Site → Logs

# PHP error logs
tail -f /var/log/php8.2-fpm.log
```

## 🌟 **CloudPanel Setup Wizard (Optional)**

If you want a visual setup wizard, I can create one that runs at `/setup` route:

### **Access Setup Wizard:**
- **URL**: `https://test.mewayz.com/setup`
- **Features**: 
  - Visual configuration
  - Database testing
  - Admin user creation
  - Environment setup

### **Enable Setup Wizard:**
```bash
# Add route temporarily
echo "Route::get('/setup', [SetupController::class, 'index']);" >> routes/web.php
```

## 📊 **CloudPanel vs Docker Comparison:**

| Feature | CloudPanel | Docker |
|---------|------------|--------|
| **Setup Time** | 5 minutes | 30+ minutes |
| **Complexity** | Simple | Complex |
| **Management** | GUI-based | Command-line |
| **Performance** | Native | Container overhead |
| **SSL/HTTPS** | Automatic | Manual setup |
| **Backups** | Built-in | Manual |
| **Monitoring** | Included | Additional setup |
| **Updates** | Standard git pull | Rebuild container |

## 🎯 **Final Result:**

- **✅ Professional deployment** on CloudPanel
- **✅ Automatic SSL** with Let's Encrypt  
- **✅ Production optimized** Nginx + PHP-FPM
- **✅ Easy management** via CloudPanel GUI
- **✅ Standard Laravel** deployment (no containers)
- **✅ Database GUI** with phpMyAdmin
- **✅ File management** via CloudPanel
- **✅ Automated backups** and monitoring

## 🆘 **Support:**

### **CloudPanel Documentation:**
- Official Docs: https://www.cloudpanel.io/docs/
- Laravel Guide: https://www.cloudpanel.io/docs/applications/laravel/

### **Quick Links:**
- **Site Management**: CloudPanel → Sites
- **Database**: CloudPanel → Databases  
- **File Manager**: CloudPanel → File Manager
- **SSL Certificate**: CloudPanel → Sites → SSL/TLS
- **Backups**: CloudPanel → Backups

---

**🚀 Perfect for CloudPanel! Much simpler than Docker and leverages all CloudPanel features!**