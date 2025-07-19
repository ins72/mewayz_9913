#!/bin/bash

# Mewayz v2 - CloudPanel Deployment Script
# Run this script in your CloudPanel site directory

set -e

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

print_status() {
    echo -e "${GREEN}[✓]${NC} $1"
}

print_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if we're in the right directory
if [[ ! -f "/.clpanel-version" ]]; then
    print_error "This script should be run on a CloudPanel server"
    exit 1
fi

# Get current directory info
CURRENT_DIR=$(pwd)
SITE_USER=$(stat -c '%U' .)

print_info "🚀 Mewayz v2 CloudPanel Deployment"
print_info "=================================="
print_info "Current directory: $CURRENT_DIR"
print_info "Site user: $SITE_USER"
echo

# Ask for configuration
read -p "Enter your domain (e.g., test.mewayz.com): " DOMAIN
read -p "Enter database name [mewayz-test]: " DB_NAME
DB_NAME=${DB_NAME:-mewayz-test}
read -p "Enter database username [mewayz-test]: " DB_USER  
DB_USER=${DB_USER:-mewayz-test}
read -s -p "Enter database password: " DB_PASS
echo
read -p "Enter admin email [admin@$DOMAIN]: " ADMIN_EMAIL
ADMIN_EMAIL=${ADMIN_EMAIL:-admin@$DOMAIN}
read -s -p "Enter admin password: " ADMIN_PASS
echo

print_info "Starting deployment..."

# Remove existing files (keep .git if exists)
print_status "Cleaning directory..."
find . -maxdepth 1 ! -name '.git' ! -name '.' ! -name '..' -exec rm -rf {} + 2>/dev/null || true

# Clone repository
print_status "Downloading Mewayz v2..."
if [[ -d ".git" ]]; then
    git pull origin main
else
    git clone https://github.com/ins72/mewayz_9913.git .
fi

# Install PHP dependencies
print_status "Installing PHP dependencies..."
composer install --optimize-autoloader --no-dev --no-interaction

# Install Node dependencies and build assets
print_status "Building frontend assets..."
npm install --production
npm run build

# Create environment file
print_status "Creating environment configuration..."
cat > .env << EOF
APP_NAME=Mewayz
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://$DOMAIN

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=$DB_NAME
DB_USERNAME=$DB_USER
DB_PASSWORD=$DB_PASS

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_HOST=localhost
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=noreply@$DOMAIN
MAIL_FROM_NAME="Mewayz"
EOF

# Generate application key
print_status "Generating application key..."
php artisan key:generate --force

# Create required directories
print_status "Creating application directories..."
mkdir -p resources/views/livewire
mkdir -p storage/app/public
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions  
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Set proper permissions for CloudPanel
print_status "Setting file permissions..."
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod -R 775 storage bootstrap/cache
chown -R $SITE_USER:$SITE_USER storage bootstrap/cache

# Database operations
print_status "Setting up database..."

# Test database connection
if mysql -u "$DB_USER" -p"$DB_PASS" -e "USE $DB_NAME;" 2>/dev/null; then
    print_status "Database connection successful"
    
    # Run migrations
    print_status "Running database migrations..."
    php artisan migrate --force --seed
    
    # Create admin user
    print_status "Creating admin user..."
    php artisan tinker --execute="
    App\Models\User::firstOrCreate(
        ['email' => '$ADMIN_EMAIL'],
        [
            'name' => 'Admin User',
            'email' => '$ADMIN_EMAIL',
            'password' => bcrypt('$ADMIN_PASS'),
            'email_verified_at' => now(),
            'is_admin' => true
        ]
    );
    echo 'Admin user created successfully!';
    "
else
    print_warning "Database connection failed. Please check your credentials."
    print_info "You can run migrations manually later with: php artisan migrate --seed"
fi

# Optimize for production
print_status "Optimizing for production..."
php artisan config:cache
php artisan route:cache  
php artisan view:cache
php artisan optimize

# Create CloudPanel Nginx config
print_status "Creating Nginx configuration..."
cat > nginx.conf << 'EOF'
# Add this to your CloudPanel site's Nginx configuration

# Main application
location / {
    try_files $uri $uri/ /index.php?$query_string;
}

# PHP processing
location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;
    fastcgi_hide_header X-Powered-By;
    fastcgi_read_timeout 300;
    fastcgi_send_timeout 300;
    fastcgi_buffer_size 128k;
    fastcgi_buffers 4 256k;
    fastcgi_busy_buffers_size 256k;
}

# Security headers
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header X-Content-Type-Options "nosniff" always;
add_header Referrer-Policy "no-referrer-when-downgrade" always;
add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

# Static file caching
location ~* \.(jpg|jpeg|gif|png|svg|ico|css|js|woff|woff2|ttf|eot)$ {
    expires 1y;
    add_header Cache-Control "public, immutable";
    access_log off;
}

# Security - block access to sensitive files
location ~ /\.(env|git) {
    deny all;
}

location ~ /(vendor|storage|bootstrap)/.*\.php$ {
    deny all;
}
EOF

# Final instructions
echo
print_status "🎉 Deployment completed successfully!"
echo
print_info "📋 Next Steps:"
echo "   1. Go to CloudPanel → Sites → $DOMAIN → Nginx"
echo "   2. Add the configuration from './nginx.conf' to your site"
echo "   3. Click 'Save & Reload Nginx'"
echo "   4. Set up SSL certificate in CloudPanel"
echo
print_info "🌐 Access your application:"
echo "   • Application: https://$DOMAIN"
echo "   • Admin Email: $ADMIN_EMAIL"
echo "   • Admin Password: [Your chosen password]"
echo
print_info "🔧 CloudPanel Management:"
echo "   • Database: CloudPanel → Databases → $DB_NAME"
echo "   • File Manager: CloudPanel → File Manager"
echo "   • Backups: CloudPanel → Backups"
echo "   • Logs: CloudPanel → Sites → $DOMAIN → Logs"
echo
print_info "📝 Configuration files created:"
echo "   • .env - Environment configuration"
echo "   • nginx.conf - Nginx configuration for CloudPanel"
echo
print_warning "🔒 Security:"
echo "   • Change admin password after first login"
echo "   • Set up regular backups in CloudPanel"
echo "   • Enable CloudPanel firewall"
echo "   • Review file permissions regularly"
echo

print_status "🚀 Mewayz v2 is ready to use!"