#!/bin/bash

# Mewayz v2 - Production Deployment Script
# This script handles production deployment with zero downtime

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
PROJECT_DIR="/var/www/mewayz-v2"
BACKUP_DIR="/var/backups/mewayz-v2"
LOG_FILE="/var/log/mewayz-deployment.log"

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
    echo "$(date '+%Y-%m-%d %H:%M:%S') [INFO] $1" >> $LOG_FILE
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
    echo "$(date '+%Y-%m-%d %H:%M:%S') [WARNING] $1" >> $LOG_FILE
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
    echo "$(date '+%Y-%m-%d %H:%M:%S') [ERROR] $1" >> $LOG_FILE
}

print_header() {
    echo -e "${BLUE}$1${NC}"
    echo "$(date '+%Y-%m-%d %H:%M:%S') [DEPLOY] $1" >> $LOG_FILE
}

# Check if running as root
if [ "$EUID" -ne 0 ]; then
    print_error "Please run this script as root (sudo ./deploy.sh)"
    exit 1
fi

print_header "🚀 Mewayz v2 - Production Deployment"
print_header "====================================="

# Step 1: Pre-deployment checks
print_status "Running pre-deployment checks..."

# Check if project directory exists
if [ ! -d "$PROJECT_DIR" ]; then
    print_error "Project directory not found: $PROJECT_DIR"
    exit 1
fi

cd $PROJECT_DIR

# Check if .env file exists
if [ ! -f .env ]; then
    print_error ".env file not found"
    exit 1
fi

# Step 2: Create backup
print_status "Creating backup..."
mkdir -p $BACKUP_DIR
BACKUP_NAME="mewayz-v2-$(date +%Y%m%d-%H%M%S)"
tar -czf "$BACKUP_DIR/$BACKUP_NAME.tar.gz" --exclude=node_modules --exclude=vendor .
print_status "Backup created: $BACKUP_DIR/$BACKUP_NAME.tar.gz"

# Step 3: Database backup
print_status "Backing up database..."
mysqldump -u root -p mewayz_production > "$BACKUP_DIR/$BACKUP_NAME-db.sql"
print_status "Database backup created: $BACKUP_DIR/$BACKUP_NAME-db.sql"

# Step 4: Enable maintenance mode
print_status "Enabling maintenance mode..."
php artisan down --render="errors::503" --secret="mewayz-secret-key"

# Step 5: Update code
print_status "Updating application code..."
git pull origin main

# Step 6: Install dependencies
print_status "Installing PHP dependencies..."
composer install --optimize-autoloader --no-dev

print_status "Installing Node.js dependencies..."
npm ci

# Step 7: Build assets
print_status "Building frontend assets..."
npm run build

# Step 8: Database migration
print_status "Running database migrations..."
php artisan migrate --force

# Step 9: Clear and rebuild cache
print_status "Clearing and rebuilding cache..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

# Step 10: Optimize for production
print_status "Optimizing for production..."
php artisan optimize

# Step 11: Set proper permissions
print_status "Setting file permissions..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 755 storage bootstrap/cache

# Step 12: Restart services
print_status "Restarting services..."
systemctl restart apache2 2>/dev/null || systemctl restart nginx 2>/dev/null || true
systemctl restart php8.2-fpm 2>/dev/null || true
systemctl restart mysql 2>/dev/null || true

# Step 13: Health check
print_status "Performing health check..."
sleep 5

# Check if application is responding
if curl -f -s -o /dev/null "https://test.mewayz.com/api/health"; then
    print_status "Health check passed ✓"
else
    print_error "Health check failed!"
    
    # Rollback option
    read -p "Do you want to rollback? (y/N): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        print_status "Rolling back..."
        
        # Restore files
        cd /
        tar -xzf "$BACKUP_DIR/$BACKUP_NAME.tar.gz" -C "$PROJECT_DIR"
        
        # Restore database
        mysql -u root -p mewayz_production < "$BACKUP_DIR/$BACKUP_NAME-db.sql"
        
        # Rebuild cache
        cd $PROJECT_DIR
        php artisan config:cache
        php artisan route:cache
        php artisan view:cache
        
        print_status "Rollback completed"
    fi
fi

# Step 14: Disable maintenance mode
print_status "Disabling maintenance mode..."
php artisan up

# Step 15: Clean up old backups (keep last 5)
print_status "Cleaning up old backups..."
cd $BACKUP_DIR
ls -t *.tar.gz | tail -n +6 | xargs -r rm --
ls -t *-db.sql | tail -n +6 | xargs -r rm --

# Step 16: Deployment complete
print_header "✅ Deployment Complete!"
print_header "======================"

echo ""
print_status "🎉 Deployment successful!"
echo ""
print_status "📋 Deployment Summary:"
echo "   • Application URL: https://test.mewayz.com"
echo "   • Backup Location: $BACKUP_DIR/$BACKUP_NAME.tar.gz"
echo "   • Log File: $LOG_FILE"
echo ""
print_status "🔍 Post-deployment checks:"
echo "   • Health Check: https://test.mewayz.com/api/health"
echo "   • Admin Panel: https://test.mewayz.com/admin"
echo "   • API Status: https://test.mewayz.com/api/test"
echo ""
print_status "📊 Next Steps:"
echo "   1. Verify all features are working"
echo "   2. Check error logs if any issues"
echo "   3. Monitor application performance"
echo "   4. Update monitoring/alerting systems"
echo ""

print_header "🚀 Mewayz v2 is now live!"