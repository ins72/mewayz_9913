#!/bin/bash

# Mewayz Platform - Complete Deployment Installation Script
# This script installs all dependencies and sets up the platform for production

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

print_success() {
    echo -e "${GREEN}$1${NC}"
}

print_question() {
    echo -e "${CYAN}$1${NC}"
}

# Function to check if command exists
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Function to detect OS
detect_os() {
    if [[ "$OSTYPE" == "linux-gnu"* ]]; then
        if command_exists apt-get; then
            echo "ubuntu"
        elif command_exists yum; then
            echo "centos"
        else
            echo "linux"
        fi
    elif [[ "$OSTYPE" == "darwin"* ]]; then
        echo "macos"
    elif [[ "$OSTYPE" == "msys" ]] || [[ "$OSTYPE" == "cygwin" ]]; then
        echo "windows"
    else
        echo "unknown"
    fi
}

# Function to install system dependencies
install_system_deps() {
    local os=$(detect_os)
    
    print_header "🔧 Installing System Dependencies for $os"
    
    case $os in
        "ubuntu")
            print_status "Updating package list..."
            sudo apt update
            
            print_status "Installing system packages..."
            sudo apt install -y \
                curl \
                wget \
                git \
                build-essential \
                software-properties-common \
                apt-transport-https \
                ca-certificates \
                gnupg \
                lsb-release \
                unzip \
                zip \
                python3-pip \
                python3-venv \
                python3-dev \
                libffi-dev \
                libssl-dev \
                libjpeg-dev \
                libpng-dev \
                libfreetype6-dev \
                libxml2-dev \
                libxslt1-dev \
                libpq-dev \
                libmariadb-dev \
                libsqlite3-dev \
                libbz2-dev \
                libreadline-dev \
                libsqlite3-dev \
                libncursesw5-dev \
                xz-utils \
                tk-dev \
                libxml2-dev \
                libxmlsec1-dev \
                libffi-dev \
                liblzma-dev \
                libgdbm-compat-dev \
                libnss3-dev \
                libssl-dev \
                libreadline-dev \
                libsqlite3-dev \
                libbz2-dev \
                libncurses5-dev \
                libncursesw5-dev \
                libgdbm-dev \
                liblzma-dev \
                zlib1g-dev \
                uuid-dev \
                libffi-dev \
                libssl-dev \
                libreadline-dev \
                libsqlite3-dev \
                libbz2-dev \
                libncurses5-dev \
                libncursesw5-dev \
                libgdbm-dev \
                liblzma-dev \
                zlib1g-dev \
                uuid-dev
            ;;
        "centos")
            print_status "Installing system packages..."
            sudo yum groupinstall -y "Development Tools"
            sudo yum install -y \
                curl \
                wget \
                git \
                openssl-devel \
                bzip2-devel \
                libffi-devel \
                zlib-devel \
                readline-devel \
                sqlite-devel \
                tk-devel \
                libxml2-devel \
                libxslt-devel \
                libjpeg-devel \
                libpng-devel \
                freetype-devel \
                postgresql-devel \
                mysql-devel \
                sqlite-devel \
                ncurses-devel \
                gdbm-devel \
                xz-devel \
                uuid-devel
            ;;
        "macos")
            if ! command_exists brew; then
                print_status "Installing Homebrew..."
                /bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
            fi
            
            print_status "Installing system packages..."
            brew install \
                curl \
                wget \
                git \
                openssl \
                readline \
                sqlite3 \
                xz \
                zlib \
                tcl-tk \
                libxml2 \
                libxslt \
                libjpeg \
                libpng \
                freetype \
                postgresql \
                mysql
            ;;
        *)
            print_error "Unsupported operating system: $os"
            exit 1
            ;;
    esac
}

# Function to install Python
install_python() {
    print_header "🐍 Installing Python 3.11+"
    
    if command_exists python3.11; then
        print_status "Python 3.11+ already installed ✓"
        return
    fi
    
    local os=$(detect_os)
    
    case $os in
        "ubuntu")
            print_status "Adding deadsnakes PPA..."
            sudo add-apt-repository ppa:deadsnakes/ppa -y
            sudo apt update
            sudo apt install -y python3.11 python3.11-venv python3.11-dev python3.11-pip
            
            # Create symlinks
            sudo update-alternatives --install /usr/bin/python3 python3 /usr/bin/python3.11 1
            sudo update-alternatives --install /usr/bin/python python /usr/bin/python3.11 1
            ;;
        "centos")
            print_status "Installing Python 3.11 from source..."
            cd /tmp
            wget https://www.python.org/ftp/python/3.11.7/Python-3.11.7.tgz
            tar -xzf Python-3.11.7.tgz
            cd Python-3.11.7
            ./configure --enable-optimizations
            make -j$(nproc)
            sudo make altinstall
            
            # Create symlinks
            sudo ln -sf /usr/local/bin/python3.11 /usr/bin/python3
            sudo ln -sf /usr/local/bin/python3.11 /usr/bin/python
            ;;
        "macos")
            print_status "Installing Python 3.11..."
            brew install python@3.11
            brew link python@3.11 --force
            ;;
    esac
    
    print_success "Python 3.11+ installed successfully ✓"
}

# Function to install Node.js
install_nodejs() {
    print_header "📦 Installing Node.js 18+"
    
    if command_exists node && node --version | grep -q "v1[89]"; then
        print_status "Node.js 18+ already installed ✓"
        return
    fi
    
    local os=$(detect_os)
    
    case $os in
        "ubuntu")
            print_status "Installing Node.js 18..."
            curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
            sudo apt install -y nodejs
            ;;
        "centos")
            print_status "Installing Node.js 18..."
            curl -fsSL https://rpm.nodesource.com/setup_18.x | sudo bash -
            sudo yum install -y nodejs
            ;;
        "macos")
            print_status "Installing Node.js 18..."
            brew install node@18
            brew link node@18 --force
            ;;
    esac
    
    print_success "Node.js installed successfully ✓"
}

# Function to install Docker
install_docker() {
    print_header "🐳 Installing Docker"
    
    if command_exists docker; then
        print_status "Docker already installed ✓"
        return
    fi
    
    local os=$(detect_os)
    
    case $os in
        "ubuntu")
            print_status "Installing Docker..."
            curl -fsSL https://get.docker.com -o get-docker.sh
            sudo sh get-docker.sh
            sudo usermod -aG docker $USER
            sudo systemctl enable docker
            sudo systemctl start docker
            ;;
        "centos")
            print_status "Installing Docker..."
            sudo yum install -y yum-utils
            sudo yum-config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo
            sudo yum install -y docker-ce docker-ce-cli containerd.io
            sudo systemctl enable docker
            sudo systemctl start docker
            sudo usermod -aG docker $USER
            ;;
        "macos")
            print_status "Installing Docker Desktop..."
            brew install --cask docker
            ;;
    esac
    
    print_success "Docker installed successfully ✓"
}

# Function to install MongoDB
install_mongodb() {
    print_header "🍃 Installing MongoDB"
    
    if command_exists mongod; then
        print_status "MongoDB already installed ✓"
        return
    fi
    
    local os=$(detect_os)
    
    case $os in
        "ubuntu")
            print_status "Installing MongoDB..."
            wget -qO - https://www.mongodb.org/static/pgp/server-6.0.asc | sudo apt-key add -
            echo "deb [ arch=amd64,arm64 ] https://repo.mongodb.org/apt/ubuntu focal/mongodb-org/6.0 multiverse" | sudo tee /etc/apt/sources.list.d/mongodb-org-6.0.list
            sudo apt update
            sudo apt install -y mongodb-org
            sudo systemctl enable mongod
            sudo systemctl start mongod
            ;;
        "centos")
            print_status "Installing MongoDB..."
            echo '[mongodb-org-6.0]' | sudo tee /etc/yum.repos.d/mongodb-org-6.0.repo
            echo 'name=MongoDB Repository' | sudo tee -a /etc/yum.repos.d/mongodb-org-6.0.repo
            echo 'baseurl=https://repo.mongodb.org/yum/redhat/$releasever/mongodb-org/6.0/x86_64/' | sudo tee -a /etc/yum.repos.d/mongodb-org-6.0.repo
            echo 'gpgcheck=1' | sudo tee -a /etc/yum.repos.d/mongodb-org-6.0.repo
            echo 'enabled=1' | sudo tee -a /etc/yum.repos.d/mongodb-org-6.0.repo
            echo 'gpgkey=https://www.mongodb.org/static/pgp/server-6.0.asc' | sudo tee -a /etc/yum.repos.d/mongodb-org-6.0.repo
            sudo yum install -y mongodb-org
            sudo systemctl enable mongod
            sudo systemctl start mongod
            ;;
        "macos")
            print_status "Installing MongoDB..."
            brew tap mongodb/brew
            brew install mongodb-community
            brew services start mongodb/brew/mongodb-community
            ;;
    esac
    
    print_success "MongoDB installed successfully ✓"
}

# Function to install Redis
install_redis() {
    print_header "🔴 Installing Redis"
    
    if command_exists redis-server; then
        print_status "Redis already installed ✓"
        return
    fi
    
    local os=$(detect_os)
    
    case $os in
        "ubuntu")
            print_status "Installing Redis..."
            sudo apt install -y redis-server
            sudo systemctl enable redis-server
            sudo systemctl start redis-server
            ;;
        "centos")
            print_status "Installing Redis..."
            sudo yum install -y redis
            sudo systemctl enable redis
            sudo systemctl start redis
            ;;
        "macos")
            print_status "Installing Redis..."
            brew install redis
            brew services start redis
            ;;
    esac
    
    print_success "Redis installed successfully ✓"
}

# Function to setup Python virtual environment
setup_python_env() {
    print_header "🐍 Setting up Python Virtual Environment"
    
    cd backend
    
    if [ ! -d "venv" ]; then
        print_status "Creating virtual environment..."
        python3 -m venv venv
    fi
    
    print_status "Activating virtual environment..."
    source venv/bin/activate
    
    print_status "Upgrading pip..."
    pip install --upgrade pip
    
    print_status "Installing Python dependencies..."
    pip install -r requirements.txt
    
    print_success "Python environment setup complete ✓"
}

# Function to setup Node.js environment
setup_node_env() {
    print_header "📦 Setting up Node.js Environment"
    
    cd frontend
    
    print_status "Installing Node.js dependencies..."
    npm install
    
    print_success "Node.js environment setup complete ✓"
}

# Function to create environment files
create_env_files() {
    print_header "⚙️ Creating Environment Configuration Files"
    
    # Backend environment
    if [ ! -f "backend/.env" ]; then
        print_status "Creating backend .env file..."
        cat > backend/.env << EOF
# Database Configuration
MONGO_URL=mongodb://localhost:27017/mewayz_production
REDIS_URL=redis://localhost:6379

# Security
JWT_SECRET=$(openssl rand -base64 32)
ENCRYPTION_KEY=$(openssl rand -base64 32)

# Application Settings
ENVIRONMENT=production
DEBUG=false
CORS_ORIGINS=http://localhost:3000,https://localhost:3000

# API Keys (Update these with your actual keys)
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
STRIPE_SECRET_KEY=your-stripe-secret-key
STRIPE_WEBHOOK_SECRET=your-stripe-webhook-secret

# Email Configuration
SMTP_SERVER=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your-email@gmail.com
SMTP_PASSWORD=your-app-password

# File Storage
AWS_ACCESS_KEY_ID=your-aws-access-key
AWS_SECRET_ACCESS_KEY=your-aws-secret-key
AWS_S3_BUCKET=your-s3-bucket
AWS_REGION=us-east-1
EOF
        print_success "Backend .env created ✓"
    fi
    
    # Frontend environment
    if [ ! -f "frontend/.env" ]; then
        print_status "Creating frontend .env file..."
        cat > frontend/.env << EOF
REACT_APP_API_URL=http://localhost:8001
REACT_APP_GOOGLE_CLIENT_ID=your-google-client-id
REACT_APP_STRIPE_PUBLISHABLE_KEY=your-stripe-publishable-key
REACT_APP_ENVIRONMENT=production
EOF
        print_success "Frontend .env created ✓"
    fi
}

# Function to build frontend
build_frontend() {
    print_header "🏗️ Building Frontend"
    
    cd frontend
    
    print_status "Building production assets..."
    npm run build
    
    print_success "Frontend build complete ✓"
}

# Function to setup database
setup_database() {
    print_header "🗄️ Setting up Database"
    
    print_status "Starting MongoDB service..."
    local os=$(detect_os)
    
    case $os in
        "ubuntu"|"centos")
            sudo systemctl start mongod
            ;;
        "macos")
            brew services start mongodb/brew/mongodb-community
            ;;
    esac
    
    print_status "Starting Redis service..."
    case $os in
        "ubuntu"|"centos")
            sudo systemctl start redis-server
            ;;
        "macos")
            brew services start redis
            ;;
    esac
    
    print_success "Database services started ✓"
}

# Function to create startup scripts
create_startup_scripts() {
    print_header "🚀 Creating Startup Scripts"
    
    # Backend startup script
    cat > start-backend.sh << 'EOF'
#!/bin/bash
cd backend
source venv/bin/activate
uvicorn main:app --host 0.0.0.0 --port 8001 --reload
EOF
    chmod +x start-backend.sh
    
    # Frontend startup script
    cat > start-frontend.sh << 'EOF'
#!/bin/bash
cd frontend
npm start
EOF
    chmod +x start-frontend.sh
    
    # Docker startup script
    cat > start-docker.sh << 'EOF'
#!/bin/bash
docker-compose up -d
EOF
    chmod +x start-docker.sh
    
    print_success "Startup scripts created ✓"
}

# Function to display final instructions
show_final_instructions() {
    print_header "🎉 Installation Complete!"
    print_header "========================"
    
    echo ""
    print_success "✅ All dependencies installed successfully!"
    echo ""
    print_status "📋 Next Steps:"
    echo "   1. Update API keys in backend/.env and frontend/.env"
    echo "   2. Configure your domain and SSL certificates"
    echo "   3. Set up your database with production data"
    echo ""
    print_status "🚀 Start the application:"
    echo "   • Backend only: ./start-backend.sh"
    echo "   • Frontend only: ./start-frontend.sh"
    echo "   • Full stack (Docker): ./start-docker.sh"
    echo ""
    print_status "🌐 Access URLs:"
    echo "   • Frontend: http://localhost:3000"
    echo "   • Backend API: http://localhost:8001"
    echo "   • API Docs: http://localhost:8001/docs"
    echo ""
    print_status "📚 Documentation:"
    echo "   • Deployment Guide: docs/DEPLOYMENT_GUIDE_v3.0.md"
    echo "   • API Documentation: docs/api/"
    echo ""
    print_warning "⚠️  Remember to:"
    echo "   • Update all API keys in .env files"
    echo "   • Configure your production database"
    echo "   • Set up proper SSL certificates"
    echo "   • Configure your web server (Nginx/Apache)"
    echo ""
    print_success "🎯 Your Mewayz platform is ready for deployment!"
}

# Main installation function
main() {
    print_header "🚀 Mewayz Platform - Complete Installation"
    print_header "=========================================="
    echo ""
    
    # Check if running as root
    if [ "$EUID" -eq 0 ]; then
        print_error "Please don't run this script as root"
        exit 1
    fi
    
    # Install system dependencies
    install_system_deps
    
    # Install core software
    install_python
    install_nodejs
    install_docker
    install_mongodb
    install_redis
    
    # Setup project environments
    setup_python_env
    setup_node_env
    
    # Create configuration files
    create_env_files
    
    # Build frontend
    build_frontend
    
    # Setup database
    setup_database
    
    # Create startup scripts
    create_startup_scripts
    
    # Show final instructions
    show_final_instructions
}

# Run main function
main "$@" 