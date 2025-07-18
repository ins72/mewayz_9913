#!/bin/bash

# Mewayz v2 - Automated Setup Script
# This script will set up your Mewayz v2 platform automatically

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
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

# Check if running as root
if [ "$EUID" -eq 0 ]; then
    print_error "Please don't run this script as root"
    exit 1
fi

print_header "🚀 Mewayz v2 - Automated Setup"
print_header "================================"

# Step 1: System Requirements Check
print_status "Checking system requirements..."

# Check OS
if [[ "$OSTYPE" == "linux-gnu"* ]]; then
    print_status "Linux detected ✓"
elif [[ "$OSTYPE" == "darwin"* ]]; then
    print_status "macOS detected ✓"
else
    print_error "Unsupported operating system"
    exit 1
fi

# Step 2: Install Dependencies
print_status "Installing system dependencies..."

if [[ "$OSTYPE" == "linux-gnu"* ]]; then
    # Update package list
    sudo apt update

    # Install PHP 8.2
    if ! command -v php8.2 &> /dev/null; then
        print_status "Installing PHP 8.2..."
        sudo apt install -y software-properties-common
        sudo add-apt-repository ppa:ondrej/php -y
        sudo apt update
        sudo apt install -y php8.2 php8.2-cli php8.2-common php8.2-mysql php8.2-curl php8.2-json php8.2-mbstring php8.2-xml php8.2-zip php8.2-gd php8.2-intl php8.2-bcmath
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

elif [[ "$OSTYPE" == "darwin"* ]]; then
    # macOS setup with Homebrew
    if ! command -v brew &> /dev/null; then
        print_status "Installing Homebrew..."
        /bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
    fi

    print_status "Installing dependencies with Homebrew..."
    brew install php@8.2 mysql node composer
fi

# Step 3: Project Setup
print_status "Setting up project dependencies..."

# Install PHP dependencies
print_status "Installing PHP dependencies..."
composer install --optimize-autoloader --no-dev

# Install Node.js dependencies
print_status "Installing Node.js dependencies..."
npm install

# Step 4: Environment Configuration
print_status "Configuring environment..."

# Copy environment file
if [ ! -f .env ]; then
    cp .env.example .env
    print_status "Environment file created ✓"
fi

# Update .env with production settings
sed -i.bak "s|APP_ENV=local|APP_ENV=production|g" .env
sed -i.bak "s|APP_URL=http://localhost|APP_URL=https://test.mewayz.com|g" .env
sed -i.bak "s|APP_DEBUG=true|APP_DEBUG=false|g" .env
sed -i.bak "s|DB_DATABASE=mewayz|DB_DATABASE=mewayz_production|g" .env

# Generate application key
php artisan key:generate --force

# Step 5: Database Setup
print_status "Setting up database..."

# Create database
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS mewayz_production;" 2>/dev/null || {
    print_warning "Could not create database automatically. Please create 'mewayz_production' database manually."
}

# Run migrations and seeders
print_status "Running database migrations..."
php artisan migrate --force --seed

# Step 6: Build Frontend Assets
print_status "Building frontend assets..."
npm run build

# Step 7: Optimize for Production
print_status "Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Step 8: Set Permissions
print_status "Setting file permissions..."
sudo chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || {
    # Fallback for non-Linux systems
    chmod -R 755 storage bootstrap/cache
}

# Step 9: Create Admin User
print_status "Creating admin user..."
php artisan tinker --execute="
\App\Models\User::create([
    'name' => 'Admin User',
    'email' => 'admin@mewayz.com',
    'password' => bcrypt('admin123'),
    'email_verified_at' => now(),
    'is_admin' => true
]);
"

# Step 10: Setup Complete
print_header "✅ Setup Complete!"
print_header "=================="

echo ""
print_status "🎉 Congratulations! Your Mewayz v2 platform is ready!"
echo ""
print_status "📋 Setup Summary:"
echo "   • Application URL: https://test.mewayz.com"
echo "   • Admin Email: admin@mewayz.com"
echo "   • Admin Password: admin123"
echo "   • Database: mewayz_production"
echo ""
print_status "🔧 Next Steps:"
echo "   1. Configure your web server (Apache/Nginx)"
echo "   2. Set up SSL certificate"
echo "   3. Update your domain DNS settings"
echo "   4. Add your API keys in .env file"
echo ""
print_status "📚 Documentation:"
echo "   • Setup Guide: ./SETUP_GUIDE.md"
echo "   • User Guide: ./docs/user-guide/"
echo "   • API Docs: ./docs/api/"
echo ""
print_status "🆘 Need Help?"
echo "   • Email: support@mewayz.com"
echo "   • Docs: https://docs.mewayz.com"
echo ""

# Optional: Start development server
read -p "Would you like to start the development server? (y/N): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    print_status "Starting development server..."
    php artisan serve --host=0.0.0.0 --port=8000 &
    echo ""
    print_status "🌐 Development server started at http://localhost:8000"
    print_status "Press Ctrl+C to stop the server"
fi

print_header "🚀 Happy building with Mewayz v2!"