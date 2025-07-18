#!/bin/bash

# Mewayz v2 - Interactive Docker Setup Script
# This script will ask for all configuration details and set up Docker containers

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
        while [ -z "$input" ]; do
            print_warning "This field is required!"
            print_question "$question: "
            read -r input
        done
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

# Function to generate random password
generate_password() {
    local length=${1:-16}
    openssl rand -base64 $length | tr -d "=+/" | cut -c1-$length 2>/dev/null || \
    dd if=/dev/urandom bs=1 count=32 2>/dev/null | base64 | tr -d "=+/" | cut -c1-$length
}

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    print_error "Docker is not installed. Please install Docker first:"
    echo "curl -fsSL https://get.docker.com -o get-docker.sh"
    echo "sudo sh get-docker.sh"
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    print_error "Docker Compose is not installed. Please install Docker Compose first."
    exit 1
fi

clear
print_header "🐳 Mewayz v2 - Interactive Docker Setup Wizard"
print_header "================================================"
echo
print_status "This script will guide you through setting up your Mewayz v2 platform with Docker."
print_status "All services will be containerized and managed automatically."
echo
print_status "Services included:"
echo "  🌐 Nginx (Web Server with SSL)"
echo "  🐘 PHP 8.2 + Laravel 11 (Application)"
echo "  🗄️ MySQL 8.0 (Database)"
echo "  🚀 Redis (Cache & Queue)"
echo "  ⚙️ Queue Workers"
echo "  📡 WebSocket Server"
echo
read -p "Press Enter to continue..."

# Check if configuration already exists
if [ -f ".env" ] && [ -f "docker-compose.yml" ]; then
    print_warning "Existing configuration found!"
    print_question "Do you want to reconfigure? This will overwrite existing settings. (y/n) [n]: "
    read -r reconfigure
    if [ "$reconfigure" != "y" ] && [ "$reconfigure" != "Y" ]; then
        print_status "Using existing configuration. Starting containers..."
        docker-compose up -d --build
        exit 0
    fi
fi

# Step 1: Application Configuration
print_header "\n📋 Step 1: Application Configuration"
print_header "====================================="

ask_input "Application Name" "Mewayz" "APP_NAME"
ask_input "Application Domain (without https://)" "test.mewayz.com" "APP_DOMAIN"
APP_URL="https://$APP_DOMAIN"

ask_input "Environment (production/local)" "production" "APP_ENV"
ask_input "Enable Debug Mode? (true/false)" "false" "APP_DEBUG"

if [ "$APP_ENV" = "production" ]; then
    LOG_LEVEL="error"
else
    LOG_LEVEL="debug"
fi

# Step 2: Database Configuration
print_header "\n🗄️ Step 2: Database Configuration"
print_header "=================================="

ask_input "Database Name" "mewayz_production" "DB_DATABASE"
ask_input "Database Username" "mewayz_user" "DB_USERNAME"
ask_input "Database Root Password" "$(generate_password)" "DB_ROOT_PASSWORD"
ask_input "Database User Password" "$(generate_password)" "DB_PASSWORD"

# Step 3: Admin User Configuration
print_header "\n👤 Step 3: Admin User Configuration"
print_header "===================================="

ask_input "Admin Full Name" "Admin User" "ADMIN_NAME"
ask_input "Admin Email" "admin@$APP_DOMAIN" "ADMIN_EMAIL"
ask_password "Admin Password" "ADMIN_PASSWORD"

# Step 4: SSL Configuration
print_header "\n🔐 Step 4: SSL Configuration"
print_header "============================="

print_question "Do you have existing SSL certificates? (y/n) [n]: "
read -r has_ssl

if [ "$has_ssl" = "y" ] || [ "$has_ssl" = "Y" ]; then
    ask_input "SSL Certificate Path" "/path/to/cert.pem" "SSL_CERT_PATH"
    ask_input "SSL Private Key Path" "/path/to/key.pem" "SSL_KEY_PATH"
    USE_EXISTING_SSL="true"
else
    print_status "Self-signed certificates will be generated automatically."
    USE_EXISTING_SSL="false"
    SSL_CERT_PATH="./docker/nginx/ssl/cert.pem"
    SSL_KEY_PATH="./docker/nginx/ssl/key.pem"
fi

# Step 5: Email Configuration (Optional)
print_header "\n📧 Step 5: Email Configuration (Optional)"
print_header "========================================="

print_question "Do you want to configure email settings now? (y/n) [n]: "
read -r configure_email

if [ "$configure_email" = "y" ] || [ "$configure_email" = "Y" ]; then
    ask_input "Mail Driver (smtp/sendmail/mailgun)" "smtp" "MAIL_MAILER"
    ask_input "SMTP Host" "smtp.gmail.com" "MAIL_HOST"
    ask_input "SMTP Port" "587" "MAIL_PORT"
    ask_input "SMTP Username" "" "MAIL_USERNAME"
    ask_password "SMTP Password" "MAIL_PASSWORD"
    ask_input "Mail Encryption (tls/ssl/null)" "tls" "MAIL_ENCRYPTION"
    ask_input "Mail From Address" "noreply@$APP_DOMAIN" "MAIL_FROM_ADDRESS"
else
    MAIL_MAILER="log"
    MAIL_HOST="localhost"
    MAIL_PORT="1025"
    MAIL_USERNAME=""
    MAIL_PASSWORD=""
    MAIL_ENCRYPTION="null"
    MAIL_FROM_ADDRESS="noreply@$APP_DOMAIN"
fi

# Step 6: Additional Services
print_header "\n⚙️ Step 6: Additional Services"
print_header "==============================="

print_question "Enable Redis for caching and queues? (y/n) [y]: "
read -r enable_redis
if [ "$enable_redis" != "n" ] && [ "$enable_redis" != "N" ]; then
    ENABLE_REDIS="true"
    CACHE_DRIVER="redis"
    QUEUE_CONNECTION="redis"
else
    ENABLE_REDIS="false"
    CACHE_DRIVER="file"
    QUEUE_CONNECTION="sync"
fi

print_question "Enable WebSocket server for real-time features? (y/n) [y]: "
read -r enable_websockets
if [ "$enable_websockets" != "n" ] && [ "$enable_websockets" != "N" ]; then
    ENABLE_WEBSOCKETS="true"
else
    ENABLE_WEBSOCKETS="false"
fi

# Step 7: Performance Settings
print_header "\n⚡ Step 7: Performance Settings"
print_header "==============================="

ask_input "Number of PHP-FPM workers" "4" "PHP_FPM_WORKERS"
ask_input "Number of Queue workers" "2" "QUEUE_WORKERS"
ask_input "MySQL max connections" "100" "MYSQL_MAX_CONNECTIONS"

# Step 8: Confirm Configuration
print_header "\n✅ Step 8: Configuration Summary"
print_header "================================="

echo
print_status "Please review your configuration:"
echo "  • App Name: $APP_NAME"
echo "  • App URL: $APP_URL"
echo "  • Environment: $APP_ENV"
echo "  • Database: $DB_DATABASE"
echo "  • DB User: $DB_USERNAME"
echo "  • Admin Email: $ADMIN_EMAIL"
echo "  • SSL: $([ "$USE_EXISTING_SSL" = "true" ] && echo "Existing certificates" || echo "Self-signed")"
echo "  • Redis: $([ "$ENABLE_REDIS" = "true" ] && echo "Enabled" || echo "Disabled")"
echo "  • WebSockets: $([ "$ENABLE_WEBSOCKETS" = "true" ] && echo "Enabled" || echo "Disabled")"
echo "  • Mail: $MAIL_MAILER ($MAIL_HOST)"
echo

print_question "Is this configuration correct? (y/n) [y]: "
read -r confirm

if [ "$confirm" = "n" ] || [ "$confirm" = "N" ]; then
    print_error "Setup cancelled. Please run the script again."
    exit 1
fi

# Generate secure random keys
APP_KEY="base64:$(generate_password 32)"
REDIS_PASSWORD=$(generate_password)

# Step 9: Create Environment File
print_header "\n📝 Step 9: Creating Configuration Files"
print_header "======================================="

print_status "Creating .env file..."

cat > .env << EOL
# Application Configuration
APP_NAME="$APP_NAME"
APP_ENV=$APP_ENV
APP_KEY=$APP_KEY
APP_DEBUG=$APP_DEBUG
APP_URL=$APP_URL

# Logging Configuration
LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=$LOG_LEVEL

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=$DB_DATABASE
DB_USERNAME=$DB_USERNAME
DB_PASSWORD=$DB_PASSWORD

# Cache & Session Configuration
BROADCAST_DRIVER=redis
CACHE_DRIVER=$CACHE_DRIVER
FILESYSTEM_DISK=local
QUEUE_CONNECTION=$QUEUE_CONNECTION
SESSION_DRIVER=redis
SESSION_LIFETIME=120

# Redis Configuration
REDIS_HOST=redis
REDIS_PASSWORD=$REDIS_PASSWORD
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

# Docker Configuration
DOCKER_DB_ROOT_PASSWORD=$DB_ROOT_PASSWORD
DOCKER_REDIS_PASSWORD=$REDIS_PASSWORD

# SSL Configuration
SSL_CERT_PATH=$SSL_CERT_PATH
SSL_KEY_PATH=$SSL_KEY_PATH

# Performance Configuration
PHP_FPM_WORKERS=$PHP_FPM_WORKERS
QUEUE_WORKERS=$QUEUE_WORKERS
MYSQL_MAX_CONNECTIONS=$MYSQL_MAX_CONNECTIONS
EOL

# Step 10: Create Docker Compose File
print_status "Creating docker-compose.yml..."

cat > docker-compose.yml << 'EOL'
version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: docker/app/Dockerfile
      args:
        - PHP_FPM_WORKERS=${PHP_FPM_WORKERS:-4}
    container_name: mewayz-app
    restart: unless-stopped
    working_dir: /var/www/html
    volumes:
      - ./:/var/www/html
      - ./storage:/var/www/html/storage
    networks:
      - mewayz-network
    depends_on:
      - db
      - redis
    environment:
      - APP_ENV=${APP_ENV}
      - APP_DEBUG=${APP_DEBUG}
      - APP_URL=${APP_URL}
      - DB_CONNECTION=mysql
      - DB_HOST=db
      - DB_PORT=3306
      - DB_DATABASE=${DB_DATABASE}
      - DB_USERNAME=${DB_USERNAME}
      - DB_PASSWORD=${DB_PASSWORD}
      - REDIS_HOST=redis
      - REDIS_PASSWORD=${DOCKER_REDIS_PASSWORD}
      - CACHE_DRIVER=${CACHE_DRIVER}
      - SESSION_DRIVER=redis
      - QUEUE_CONNECTION=${QUEUE_CONNECTION}

  web:
    image: nginx:alpine
    container_name: mewayz-web
    restart: unless-stopped
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./:/var/www/html
      - ./docker/nginx/conf.d:/etc/nginx/conf.d
      - ./docker/nginx/ssl:/etc/nginx/ssl
    depends_on:
      - app
    networks:
      - mewayz-network

  db:
    image: mysql:8.0
    container_name: mewayz-db
    restart: unless-stopped
    command: --default-authentication-plugin=mysql_native_password --max-connections=${MYSQL_MAX_CONNECTIONS:-100}
    ports:
      - "3306:3306"
    volumes:
      - mysql_data:/var/lib/mysql
      - ./docker/mysql/init:/docker-entrypoint-initdb.d
      - ./docker/mysql/conf.d:/etc/mysql/conf.d
    environment:
      - MYSQL_ROOT_PASSWORD=${DOCKER_DB_ROOT_PASSWORD}
      - MYSQL_DATABASE=${DB_DATABASE}
      - MYSQL_USER=${DB_USERNAME}
      - MYSQL_PASSWORD=${DB_PASSWORD}
    networks:
      - mewayz-network

  redis:
    image: redis:7.0-alpine
    container_name: mewayz-redis
    restart: unless-stopped
    command: redis-server --requirepass ${DOCKER_REDIS_PASSWORD}
    ports:
      - "6379:6379"
    volumes:
      - redis_data:/data
    networks:
      - mewayz-network

  queue:
    build:
      context: .
      dockerfile: docker/app/Dockerfile
    container_name: mewayz-queue
    restart: unless-stopped
    command: php artisan queue:work --sleep=3 --tries=3 --timeout=60
    volumes:
      - ./:/var/www/html
    depends_on:
      - db
      - redis
    networks:
      - mewayz-network
    environment:
      - APP_ENV=${APP_ENV}
      - DB_CONNECTION=mysql
      - DB_HOST=db
      - REDIS_HOST=redis
      - REDIS_PASSWORD=${DOCKER_REDIS_PASSWORD}
      - QUEUE_CONNECTION=${QUEUE_CONNECTION}
    deploy:
      replicas: ${QUEUE_WORKERS:-2}

  scheduler:
    build:
      context: .
      dockerfile: docker/app/Dockerfile
    container_name: mewayz-scheduler
    restart: unless-stopped
    command: php artisan schedule:work
    volumes:
      - ./:/var/www/html
    depends_on:
      - db
      - redis
    networks:
      - mewayz-network
    environment:
      - APP_ENV=${APP_ENV}
      - DB_CONNECTION=mysql
      - DB_HOST=db
      - REDIS_HOST=redis

volumes:
  mysql_data:
  redis_data:

networks:
  mewayz-network:
    driver: bridge
EOL

# Add WebSocket service if enabled
if [ "$ENABLE_WEBSOCKETS" = "true" ]; then
    cat >> docker-compose.yml << 'EOL'

  websocket:
    build:
      context: .
      dockerfile: docker/app/Dockerfile
    container_name: mewayz-websocket
    restart: unless-stopped
    command: php artisan websockets:serve --host=0.0.0.0 --port=6001
    ports:
      - "6001:6001"
    volumes:
      - ./:/var/www/html
    depends_on:
      - db
      - redis
    networks:
      - mewayz-network
    environment:
      - APP_ENV=${APP_ENV}
      - DB_CONNECTION=mysql
      - DB_HOST=db
      - REDIS_HOST=redis
EOL
fi

# Step 11: Create Docker Configuration Files
print_status "Creating Docker configuration files..."

# Create directories
mkdir -p docker/app docker/nginx/conf.d docker/nginx/ssl docker/mysql/init docker/mysql/conf.d

# Create App Dockerfile
cat > docker/app/Dockerfile << 'EOL'
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm \
    supervisor \
    cron \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev --no-interaction --ignore-platform-reqs

# Install Node.js dependencies and build assets
RUN npm ci && npm run build

# Create required directories
RUN mkdir -p resources/views/livewire \
    storage/app/public \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Expose port
EXPOSE 9000

CMD ["php-fpm"]
EOL

# Create Nginx configuration
cat > docker/nginx/conf.d/default.conf << EOL
server {
    listen 80;
    server_name $APP_DOMAIN;
    return 301 https://\$server_name\$request_uri;
}

server {
    listen 443 ssl http2;
    server_name $APP_DOMAIN;
    root /var/www/html/public;
    index index.php;

    # SSL Configuration
    ssl_certificate /etc/nginx/ssl/cert.pem;
    ssl_certificate_key /etc/nginx/ssl/key.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-SHA384;
    ssl_prefer_server_ciphers on;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;
    
    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    # Main location
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    # PHP files
    location ~ \.php\$ {
        fastcgi_pass app:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
        fastcgi_read_timeout 300;
        fastcgi_send_timeout 300;
        fastcgi_buffer_size 128k;
        fastcgi_buffers 4 256k;
        fastcgi_busy_buffers_size 256k;
    }

    # Static assets
    location ~* \.(jpg|jpeg|gif|png|svg|ico|css|js|woff|woff2|ttf|eot)\$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    # Security
    location ~ /\.ht {
        deny all;
    }
}
EOL

# Create MySQL initialization script
cat > docker/mysql/init/init.sql << EOL
-- Initialize Mewayz v2 Database
CREATE DATABASE IF NOT EXISTS \`$DB_DATABASE\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Performance optimizations
SET GLOBAL innodb_buffer_pool_size = 1G;
SET GLOBAL innodb_log_file_size = 256M;
SET GLOBAL innodb_flush_log_at_trx_commit = 2;
SET GLOBAL innodb_flush_method = O_DIRECT;

-- Security settings
SET GLOBAL local_infile = 0;
SET GLOBAL sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO';

FLUSH PRIVILEGES;
EOL

# Create MySQL configuration
cat > docker/mysql/conf.d/custom.cnf << 'EOL'
[mysqld]
# Performance settings
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT
max_connections = ${MYSQL_MAX_CONNECTIONS}

# Character set
character-set-server = utf8mb4
collation-server = utf8mb4_unicode_ci

# Security
local_infile = 0
EOL

# Step 12: Generate SSL Certificates
if [ "$USE_EXISTING_SSL" = "false" ]; then
    print_status "Generating self-signed SSL certificates..."
    openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
        -keyout docker/nginx/ssl/key.pem \
        -out docker/nginx/ssl/cert.pem \
        -subj "/C=US/ST=State/L=City/O=Organization/CN=$APP_DOMAIN"
else
    print_status "Copying existing SSL certificates..."
    cp "$SSL_CERT_PATH" docker/nginx/ssl/cert.pem
    cp "$SSL_KEY_PATH" docker/nginx/ssl/key.pem
fi

# Step 13: Create Admin User Setup Script
cat > docker/setup-admin.sh << EOL
#!/bin/bash
# Wait for database to be ready
sleep 10

# Create admin user
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
EOL

chmod +x docker/setup-admin.sh

# Step 14: Build and Start Containers
print_header "\n🐳 Step 14: Building and Starting Containers"
print_header "============================================="

print_status "Building Docker containers..."
docker-compose build --no-cache

print_status "Starting containers..."
docker-compose up -d

print_status "Waiting for services to start..."
sleep 30

# Step 15: Initialize Application
print_header "\n🚀 Step 15: Application Initialization"
print_header "======================================"

print_status "Running database migrations..."
docker-compose exec app php artisan migrate --force

print_status "Seeding database..."
docker-compose exec app php artisan db:seed --force

print_status "Creating admin user..."
docker-compose exec app ./docker/setup-admin.sh

print_status "Optimizing application..."
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# Step 16: Setup Complete
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
print_status "🐳 Docker Services:"
echo "   • Web Server: nginx (ports 80, 443)"
echo "   • Application: php-fpm"
echo "   • Database: mysql (port 3306)"
echo "   • Cache: redis (port 6379)"
echo "   • Queue Workers: $QUEUE_WORKERS workers"
if [ "$ENABLE_WEBSOCKETS" = "true" ]; then
    echo "   • WebSocket Server: port 6001"
fi
echo
print_status "🔧 Useful Commands:"
echo "   • View logs: docker-compose logs -f"
echo "   • Stop services: docker-compose stop"
echo "   • Start services: docker-compose start"
echo "   • Restart services: docker-compose restart"
echo "   • Update application: docker-compose exec app php artisan migrate"
echo
print_status "📚 Access Points:"
echo "   • Main Application: $APP_URL"
echo "   • Admin Dashboard: $APP_URL/admin"
echo "   • API Health Check: $APP_URL/api/health"
echo
print_status "📁 Configuration Files:"
echo "   • Environment: .env"
echo "   • Docker Compose: docker-compose.yml"
echo "   • SSL Certificates: docker/nginx/ssl/"

print_header "\n🚀 Your Mewayz v2 platform is now live at $APP_URL!"
print_header "Enjoy building your business empire! 🎉"