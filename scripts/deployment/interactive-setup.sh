#!/bin/bash

# Mewayz v2 - Interactive Setup Script
# This script will ask for all configuration details and set up everything properly

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_header() {
    echo -e "${BLUE}$1${NC}"
}

print_question() {
    echo -e "${CYAN}$1${NC}"
}

print_success() {
    echo -e "${GREEN}$1${NC}"
}

# Function to ask for input with default value
ask_input() {
    local question="$1"
    local default="$2"
    local var_name="$3"
    
    if [ -n "$default" ]; then
        print_question "$question [$default]: "
        read -r input
        if [ -z "$input" ]; then
            input="$default"
        fi
    else
        print_question "$question: "
        read -r input
    fi
    
    eval "$var_name=\"$input\""
}

# Function to ask for password
ask_password() {
    local question="$1"
    local var_name="$2"
    
    print_question "$question: "
    read -s password
    echo
    print_question "Confirm password: "
    read -s password_confirm
    echo
    
    if [ "$password" != "$password_confirm" ]; then
        print_error "Passwords don't match! Please try again."
        ask_password "$question" "$var_name"
    else
        eval "$var_name=\"$password\""
    fi
}

# Check if running as root
if [ "$EUID" -eq 0 ]; then
    print_error "Please don't run this script as root. Use a regular user with sudo privileges."
    exit 1
fi

clear
print_header "🚀 Mewayz v2 - Interactive Setup Wizard"
print_header "==========================================="
echo
print_status "This script will guide you through setting up your Mewayz v2 platform."
print_status "Please have the following information ready:"
echo "  • Application URL (e.g., https://test.mewayz.com)"
echo "  • Database credentials"
echo "  • Admin user details"
echo "  • Email configuration (optional)"
echo
read -p "Press Enter to continue..."

# Step 1: Application Configuration
print_header "\n📋 Step 1: Application Configuration"
print_header "====================================="

ask_input "Application Name" "Mewayz" "APP_NAME"
ask_input "Application URL (with https://)" "https://test.mewayz.com" "APP_URL"
ask_input "Environment (production/local)" "production" "APP_ENV"

if [ "$APP_ENV" = "production" ]; then
    APP_DEBUG="false"
    LOG_LEVEL="error"
else
    APP_DEBUG="true"
    LOG_LEVEL="debug"
fi

# Step 2: Database Configuration
print_header "\n🗄️ Step 2: Database Configuration"
print_header "=================================="

ask_input "Database Name" "mewayz-test" "DB_DATABASE"
ask_input "Database Username" "mewayz-test" "DB_USERNAME"
ask_password "Database Password" "DB_PASSWORD"
ask_input "Database Host" "127.0.0.1" "DB_HOST"
ask_input "Database Port" "3306" "DB_PORT"

# Step 3: Admin User Configuration
print_header "\n👤 Step 3: Admin User Configuration"
print_header "===================================="

ask_input "Admin Name" "Admin User" "ADMIN_NAME"
ask_input "Admin Email" "admin@mewayz.com" "ADMIN_EMAIL"
ask_password "Admin Password" "ADMIN_PASSWORD"

# Step 4: Email Configuration (Optional)
print_header "\n📧 Step 4: Email Configuration (Optional)"
print_header "========================================="

print_question "Do you want to configure email settings now? (y/n) [n]: "
read -r configure_email

if [ "$configure_email" = "y" ] || [ "$configure_email" = "Y" ]; then
    ask_input "Mail Driver (smtp/sendmail/mailgun)" "smtp" "MAIL_MAILER"
    ask_input "Mail Host" "localhost" "MAIL_HOST"
    ask_input "Mail Port" "587" "MAIL_PORT"
    ask_input "Mail Username" "" "MAIL_USERNAME"
    ask_password "Mail Password" "MAIL_PASSWORD"
    ask_input "Mail Encryption (tls/ssl/null)" "tls" "MAIL_ENCRYPTION"
    ask_input "Mail From Address" "noreply@$(echo $APP_URL | cut -d'/' -f3)" "MAIL_FROM_ADDRESS"
else
    MAIL_MAILER="smtp"
    MAIL_HOST="localhost"
    MAIL_PORT="587"
    MAIL_USERNAME=""
    MAIL_PASSWORD=""
    MAIL_ENCRYPTION="null"
    MAIL_FROM_ADDRESS="noreply@example.com"
fi

# Step 5: Confirm Configuration
print_header "\n✅ Step 5: Configuration Summary"
print_header "================================="

echo
print_status "Please review your configuration:"
echo "  • App Name: $APP_NAME"
echo "  • App URL: $APP_URL"
echo "  • Environment: $APP_ENV"
echo "  • Database: $DB_DATABASE"
echo "  • DB User: $DB_USERNAME"
echo "  • DB Host: $DB_HOST:$DB_PORT"
echo "  • Admin Name: $ADMIN_NAME"
echo "  • Admin Email: $ADMIN_EMAIL"
echo "  • Mail Host: $MAIL_HOST"
echo

print_question "Is this configuration correct? (y/n) [y]: "
read -r confirm

if [ "$confirm" = "n" ] || [ "$confirm" = "N" ]; then
    print_error "Setup cancelled. Please run the script again."
    exit 1
fi

# Step 6: System Dependencies
print_header "\n🔧 Step 6: Installing System Dependencies"
print_header "========================================="

print_status "Updating system packages..."
sudo apt update

# Install PHP 8.2
if ! command -v php8.2 &> /dev/null; then
    print_status "Installing PHP 8.2..."
    sudo apt install -y software-properties-common
    sudo add-apt-repository ppa:ondrej/php -y
    sudo apt update
    sudo apt install -y php8.2 php8.2-cli php8.2-common php8.2-mysql php8.2-curl php8.2-json php8.2-mbstring php8.2-xml php8.2-zip php8.2-gd php8.2-intl php8.2-bcmath php8.2-fpm
else
    print_status "PHP 8.2 already installed ✓"
fi

# Install MySQL
if ! command -v mysql &> /dev/null; then
    print_status "Installing MySQL..."
    sudo apt install -y mysql-server
    sudo mysql_secure_installation
else
    print_status "MySQL already installed ✓"
fi

# Install Node.js
if ! command -v node &> /dev/null; then
    print_status "Installing Node.js..."
    curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
    sudo apt install -y nodejs
else
    print_status "Node.js already installed ✓"
fi

# Install Composer
if ! command -v composer &> /dev/null; then
    print_status "Installing Composer..."
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
    sudo chmod +x /usr/local/bin/composer
else
    print_status "Composer already installed ✓"
fi

# Install Redis
if ! command -v redis-server &> /dev/null; then
    print_status "Installing Redis..."
    sudo apt install -y redis-server
    sudo systemctl enable redis-server
    sudo systemctl start redis-server
else
    print_status "Redis already installed ✓"
fi

# Step 7: Database Setup
print_header "\n🗄️ Step 7: Database Setup"
print_header "=========================="

print_status "Creating database and user..."

# Create database and user
sudo mysql -e "CREATE DATABASE IF NOT EXISTS \`$DB_DATABASE\`;" 2>/dev/null || {
    print_warning "Could not create database automatically. Please create it manually."
}

sudo mysql -e "CREATE USER IF NOT EXISTS '$DB_USERNAME'@'localhost' IDENTIFIED BY '$DB_PASSWORD';" 2>/dev/null || {
    print_warning "User might already exist or manual creation needed."
}

sudo mysql -e "GRANT ALL PRIVILEGES ON \`$DB_DATABASE\`.* TO '$DB_USERNAME'@'localhost';" 2>/dev/null || {
    print_warning "Could not grant privileges automatically."
}

sudo mysql -e "FLUSH PRIVILEGES;" 2>/dev/null || true

# Test database connection
print_status "Testing database connection..."
if mysql -u "$DB_USERNAME" -p"$DB_PASSWORD" -h "$DB_HOST" -P "$DB_PORT" -e "USE \`$DB_DATABASE\`;" 2>/dev/null; then
    print_success "✓ Database connection successful!"
else
    print_error "Database connection failed. Please check your credentials."
    exit 1
fi

# Step 8: Project Setup
print_header "\n📦 Step 8: Project Setup"
print_header "========================="

print_status "Installing PHP dependencies..."
composer install --optimize-autoloader --no-dev

print_status "Installing Node.js dependencies..."
npm install

# Step 9: Environment Configuration
print_header "\n⚙️ Step 9: Environment Configuration"
print_header "===================================="

print_status "Creating .env file..."

cat > .env << EOL
# Application Configuration
APP_NAME="$APP_NAME"
APP_ENV=$APP_ENV
APP_KEY=
APP_DEBUG=$APP_DEBUG
APP_URL=$APP_URL

# Logging Configuration
LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=$LOG_LEVEL

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=$DB_HOST
DB_PORT=$DB_PORT
DB_DATABASE=$DB_DATABASE
DB_USERNAME=$DB_USERNAME
DB_PASSWORD=$DB_PASSWORD

# Cache & Session Configuration
BROADCAST_DRIVER=redis
CACHE_DRIVER=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

# Redis Configuration
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail Configuration
MAIL_MAILER=$MAIL_MAILER
MAIL_HOST=$MAIL_HOST
MAIL_PORT=$MAIL_PORT
MAIL_USERNAME="$MAIL_USERNAME"
MAIL_PASSWORD="$MAIL_PASSWORD"
MAIL_ENCRYPTION=$MAIL_ENCRYPTION
MAIL_FROM_ADDRESS="$MAIL_FROM_ADDRESS"
MAIL_FROM_NAME="\${APP_NAME}"

# AWS Configuration
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

# Pusher Configuration (WebSocket)
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

# Vite Configuration
VITE_APP_NAME="\${APP_NAME}"
VITE_PUSHER_APP_KEY="\${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="\${PUSHER_HOST}"
VITE_PUSHER_PORT="\${PUSHER_PORT}"
VITE_PUSHER_SCHEME="\${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="\${PUSHER_APP_CLUSTER}"

# Security Configuration
BCRYPT_ROUNDS=12
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=strict
EOL

# Generate application key
print_status "Generating application key..."
php artisan key:generate --force

# Step 10: Create Missing Directories
print_header "\n📁 Step 10: Creating Required Directories"
print_header "=========================================="

print_status "Creating missing directories..."
mkdir -p resources/views/livewire
mkdir -p storage/app/public
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache
mkdir -p public/storage

# Set proper permissions
print_status "Setting proper permissions..."
chmod -R 755 storage bootstrap/cache
chmod -R 755 resources/views

# Step 11: Database Migration
print_header "\n🚀 Step 11: Database Migration"
print_header "==============================="

print_status "Running database migrations..."
php artisan migrate --force

print_status "Seeding database..."
php artisan db:seed --force

# Step 12: Create Admin User
print_header "\n👤 Step 12: Creating Admin User"
print_header "==============================="

print_status "Creating admin user..."
php artisan tinker --execute="
\App\Models\User::firstOrCreate(
    ['email' => '$ADMIN_EMAIL'],
    [
        'name' => '$ADMIN_NAME',
        'email' => '$ADMIN_EMAIL',
        'password' => bcrypt('$ADMIN_PASSWORD'),
        'email_verified_at' => now(),
        'is_admin' => true
    ]
);
echo 'Admin user created successfully!';
"

# Step 13: Build Frontend Assets
print_header "\n🎨 Step 13: Building Frontend Assets"
print_header "===================================="

print_status "Building frontend assets..."
npm run build

# Step 14: Optimize for Production
print_header "\n⚡ Step 14: Production Optimization"
print_header "==================================="

print_status "Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Step 15: Setup Complete
print_header "\n✅ Setup Complete!"
print_header "=================="

echo
print_success "🎉 Congratulations! Your Mewayz v2 platform is ready!"
echo
print_status "📋 Setup Summary:"
echo "   • Application URL: $APP_URL"
echo "   • Database: $DB_DATABASE"
echo "   • Admin Email: $ADMIN_EMAIL"
echo "   • Admin Password: [Your chosen password]"
echo
print_status "🚀 Access Your Platform:"
echo "   • Main Application: $APP_URL"
echo "   • Admin Dashboard: $APP_URL/admin"
echo "   • API Health Check: $APP_URL/api/health"
echo
print_status "🔧 Quick Commands:"
echo "   • Start server: php artisan serve --host=0.0.0.0 --port=8000"
echo "   • Check health: curl $APP_URL/api/health"
echo "   • View logs: tail -f storage/logs/laravel.log"
echo
print_status "📚 Documentation:"
echo "   • Setup Guide: ./SETUP_GUIDE.md"
echo "   • User Guide: ./docs/user-guide/"
echo "   • API Docs: ./docs/api/"

# Optional: Start development server
echo
print_question "Would you like to start the development server now? (y/n) [y]: "
read -r start_server

if [ "$start_server" != "n" ] && [ "$start_server" != "N" ]; then
    print_status "Starting development server..."
    echo
    print_success "🌐 Server starting at: $APP_URL"
    print_success "Press Ctrl+C to stop the server"
    echo
    php artisan serve --host=0.0.0.0 --port=8000
fi

print_header "🚀 Setup completed successfully! Enjoy your Mewayz v2 platform!"