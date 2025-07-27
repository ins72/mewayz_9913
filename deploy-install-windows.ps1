# Mewayz Platform - Windows Deployment Installation Script
# This script installs all dependencies and sets up the platform for production on Windows

param(
    [switch]$SkipDocker,
    [switch]$SkipMongoDB,
    [switch]$SkipRedis
)

# Colors for output
$Red = "Red"
$Green = "Green"
$Yellow = "Yellow"
$Blue = "Blue"
$Cyan = "Cyan"

# Function to print colored output
function Write-Status {
    param([string]$Message)
    Write-Host "[INFO] $Message" -ForegroundColor $Green
}

function Write-Warning {
    param([string]$Message)
    Write-Host "[WARNING] $Message" -ForegroundColor $Yellow
}

function Write-Error {
    param([string]$Message)
    Write-Host "[ERROR] $Message" -ForegroundColor $Red
}

function Write-Header {
    param([string]$Message)
    Write-Host $Message -ForegroundColor $Blue
}

function Write-Success {
    param([string]$Message)
    Write-Host $Message -ForegroundColor $Green
}

function Write-Question {
    param([string]$Message)
    Write-Host $Message -ForegroundColor $Cyan
}

# Function to check if command exists
function Test-Command {
    param([string]$Command)
    try {
        Get-Command $Command -ErrorAction Stop | Out-Null
        return $true
    }
    catch {
        return $false
    }
}

# Function to install Chocolatey
function Install-Chocolatey {
    Write-Header "🍫 Installing Chocolatey Package Manager"
    
    if (Test-Command "choco") {
        Write-Status "Chocolatey already installed ✓"
        return
    }
    
    Write-Status "Installing Chocolatey..."
    Set-ExecutionPolicy Bypass -Scope Process -Force
    [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072
    iex ((New-Object System.Net.WebClient).DownloadString('https://community.chocolatey.org/install.ps1'))
    
    # Refresh environment variables
    $env:Path = [System.Environment]::GetEnvironmentVariable("Path","Machine") + ";" + [System.Environment]::GetEnvironmentVariable("Path","User")
    
    Write-Success "Chocolatey installed successfully ✓"
}

# Function to install Python
function Install-Python {
    Write-Header "🐍 Installing Python 3.11+"
    
    if (Test-Command "python") {
        $pythonVersion = python --version 2>&1
        if ($pythonVersion -match "Python 3\.1[1-9]") {
            Write-Status "Python 3.11+ already installed ✓"
            return
        }
    }
    
    Write-Status "Installing Python 3.11..."
    choco install python311 -y
    
    # Refresh environment variables
    $env:Path = [System.Environment]::GetEnvironmentVariable("Path","Machine") + ";" + [System.Environment]::GetEnvironmentVariable("Path","User")
    
    Write-Success "Python 3.11+ installed successfully ✓"
}

# Function to install Node.js
function Install-NodeJS {
    Write-Header "📦 Installing Node.js 18+"
    
    if (Test-Command "node") {
        $nodeVersion = node --version
        if ($nodeVersion -match "v1[89]") {
            Write-Status "Node.js 18+ already installed ✓"
            return
        }
    }
    
    Write-Status "Installing Node.js 18..."
    choco install nodejs-lts -y
    
    # Refresh environment variables
    $env:Path = [System.Environment]::GetEnvironmentVariable("Path","Machine") + ";" + [System.Environment]::GetEnvironmentVariable("Path","User")
    
    Write-Success "Node.js installed successfully ✓"
}

# Function to install Docker Desktop
function Install-Docker {
    if ($SkipDocker) {
        Write-Warning "Skipping Docker installation as requested"
        return
    }
    
    Write-Header "🐳 Installing Docker Desktop"
    
    if (Test-Command "docker") {
        Write-Status "Docker already installed ✓"
        return
    }
    
    Write-Status "Installing Docker Desktop..."
    choco install docker-desktop -y
    
    Write-Warning "Docker Desktop installed. Please restart your computer and start Docker Desktop manually."
    Write-Warning "After restart, Docker Desktop will need to be started from the Start menu."
    
    Write-Success "Docker Desktop installed successfully ✓"
}

# Function to install MongoDB
function Install-MongoDB {
    if ($SkipMongoDB) {
        Write-Warning "Skipping MongoDB installation as requested"
        return
    }
    
    Write-Header "🍃 Installing MongoDB"
    
    if (Test-Command "mongod") {
        Write-Status "MongoDB already installed ✓"
        return
    }
    
    Write-Status "Installing MongoDB..."
    choco install mongodb -y
    
    # Create data directory
    $dataDir = "C:\data\db"
    if (!(Test-Path $dataDir)) {
        New-Item -ItemType Directory -Path $dataDir -Force | Out-Null
    }
    
    Write-Success "MongoDB installed successfully ✓"
}

# Function to install Redis
function Install-Redis {
    if ($SkipRedis) {
        Write-Warning "Skipping Redis installation as requested"
        return
    }
    
    Write-Header "🔴 Installing Redis"
    
    if (Test-Command "redis-server") {
        Write-Status "Redis already installed ✓"
        return
    }
    
    Write-Status "Installing Redis..."
    choco install redis-64 -y
    
    Write-Success "Redis installed successfully ✓"
}

# Function to setup Python virtual environment
function Setup-PythonEnv {
    Write-Header "🐍 Setting up Python Virtual Environment"
    
    Set-Location backend
    
    if (!(Test-Path "venv")) {
        Write-Status "Creating virtual environment..."
        python -m venv venv
    }
    
    Write-Status "Activating virtual environment..."
    & ".\venv\Scripts\Activate.ps1"
    
    Write-Status "Upgrading pip..."
    python -m pip install --upgrade pip
    
    Write-Status "Installing Python dependencies..."
    pip install -r requirements.txt
    
    Set-Location ..
    Write-Success "Python environment setup complete ✓"
}

# Function to setup Node.js environment
function Setup-NodeEnv {
    Write-Header "📦 Setting up Node.js Environment"
    
    Set-Location frontend
    
    Write-Status "Installing Node.js dependencies..."
    npm install
    
    Set-Location ..
    Write-Success "Node.js environment setup complete ✓"
}

# Function to create environment files
function Create-EnvFiles {
    Write-Header "⚙️ Creating Environment Configuration Files"
    
    # Generate random secrets
    $jwtSecret = [System.Convert]::ToBase64String([System.Security.Cryptography.RandomNumberGenerator]::GetBytes(32))
    $encryptionKey = [System.Convert]::ToBase64String([System.Security.Cryptography.RandomNumberGenerator]::GetBytes(32))
    
    # Backend environment
    if (!(Test-Path "backend\.env")) {
        Write-Status "Creating backend .env file..."
        @"
# Database Configuration
MONGO_URL=mongodb://localhost:27017/mewayz_production
REDIS_URL=redis://localhost:6379

# Security
JWT_SECRET=$jwtSecret
ENCRYPTION_KEY=$encryptionKey

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
"@ | Out-File -FilePath "backend\.env" -Encoding UTF8
        
        Write-Success "Backend .env created ✓"
    }
    
    # Frontend environment
    if (!(Test-Path "frontend\.env")) {
        Write-Status "Creating frontend .env file..."
        @"
REACT_APP_API_URL=http://localhost:8001
REACT_APP_GOOGLE_CLIENT_ID=your-google-client-id
REACT_APP_STRIPE_PUBLISHABLE_KEY=your-stripe-publishable-key
REACT_APP_ENVIRONMENT=production
"@ | Out-File -FilePath "frontend\.env" -Encoding UTF8
        
        Write-Success "Frontend .env created ✓"
    }
}

# Function to build frontend
function Build-Frontend {
    Write-Header "🏗️ Building Frontend"
    
    Set-Location frontend
    
    Write-Status "Building production assets..."
    npm run build
    
    Set-Location ..
    Write-Success "Frontend build complete ✓"
}

# Function to setup database services
function Setup-DatabaseServices {
    Write-Header "🗄️ Setting up Database Services"
    
    if (!$SkipMongoDB) {
        Write-Status "Starting MongoDB service..."
        try {
            Start-Service MongoDB -ErrorAction Stop
            Write-Success "MongoDB service started ✓"
        }
        catch {
            Write-Warning "Could not start MongoDB service. Please start it manually."
        }
    }
    
    if (!$SkipRedis) {
        Write-Status "Starting Redis service..."
        try {
            Start-Service Redis -ErrorAction Stop
            Write-Success "Redis service started ✓"
        }
        catch {
            Write-Warning "Could not start Redis service. Please start it manually."
        }
    }
}

# Function to create startup scripts
function Create-StartupScripts {
    Write-Header "🚀 Creating Startup Scripts"
    
    # Backend startup script
    @"
@echo off
cd backend
call venv\Scripts\activate.bat
uvicorn main:app --host 0.0.0.0 --port 8001 --reload
pause
"@ | Out-File -FilePath "start-backend.bat" -Encoding ASCII
    
    # Frontend startup script
    @"
@echo off
cd frontend
npm start
pause
"@ | Out-File -FilePath "start-frontend.bat" -Encoding ASCII
    
    # Docker startup script
    @"
@echo off
docker-compose up -d
pause
"@ | Out-File -FilePath "start-docker.bat" -Encoding ASCII
    
    # PowerShell backend script
    @"
# Backend startup script for PowerShell
Set-Location backend
.\venv\Scripts\Activate.ps1
uvicorn main:app --host 0.0.0.0 --port 8001 --reload
"@ | Out-File -FilePath "start-backend.ps1" -Encoding UTF8
    
    # PowerShell frontend script
    @"
# Frontend startup script for PowerShell
Set-Location frontend
npm start
"@ | Out-File -FilePath "start-frontend.ps1" -Encoding UTF8
    
    Write-Success "Startup scripts created ✓"
}

# Function to display final instructions
function Show-FinalInstructions {
    Write-Header "🎉 Installation Complete!"
    Write-Header "========================"
    
    Write-Host ""
    Write-Success "✅ All dependencies installed successfully!"
    Write-Host ""
    Write-Status "📋 Next Steps:"
    Write-Host "   1. Update API keys in backend\.env and frontend\.env"
    Write-Host "   2. Configure your domain and SSL certificates"
    Write-Host "   3. Set up your database with production data"
    Write-Host ""
    Write-Status "🚀 Start the application:"
    Write-Host "   • Backend only: .\start-backend.bat or .\start-backend.ps1"
    Write-Host "   • Frontend only: .\start-frontend.bat or .\start-frontend.ps1"
    Write-Host "   • Full stack (Docker): .\start-docker.bat"
    Write-Host ""
    Write-Status "🌐 Access URLs:"
    Write-Host "   • Frontend: http://localhost:3000"
    Write-Host "   • Backend API: http://localhost:8001"
    Write-Host "   • API Docs: http://localhost:8001/docs"
    Write-Host ""
    Write-Status "📚 Documentation:"
    Write-Host "   • Deployment Guide: docs\DEPLOYMENT_GUIDE_v3.0.md"
    Write-Host "   • API Documentation: docs\api\"
    Write-Host ""
    Write-Warning "⚠️  Remember to:"
    Write-Host "   • Update all API keys in .env files"
    Write-Host "   • Configure your production database"
    Write-Host "   • Set up proper SSL certificates"
    Write-Host "   • Configure your web server (IIS/Nginx)"
    Write-Host ""
    
    if (!$SkipDocker) {
        Write-Warning "🔄 Docker Desktop:"
        Write-Host "   • Restart your computer if you haven't already"
        Write-Host "   • Start Docker Desktop from the Start menu"
        Write-Host "   • Wait for Docker to fully start before running containers"
        Write-Host ""
    }
    
    Write-Success "🎯 Your Mewayz platform is ready for deployment!"
}

# Main installation function
function Main {
    Write-Header "🚀 Mewayz Platform - Windows Installation"
    Write-Header "=========================================="
    Write-Host ""
    
    # Check if running as administrator
    $isAdmin = ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole] "Administrator")
    
    if (!$isAdmin) {
        Write-Error "Please run this script as Administrator"
        Write-Host "Right-click on PowerShell and select 'Run as Administrator'"
        exit 1
    }
    
    # Install Chocolatey first
    Install-Chocolatey
    
    # Install core software
    Install-Python
    Install-NodeJS
    Install-Docker
    Install-MongoDB
    Install-Redis
    
    # Setup project environments
    Setup-PythonEnv
    Setup-NodeEnv
    
    # Create configuration files
    Create-EnvFiles
    
    # Build frontend
    Build-Frontend
    
    # Setup database
    Setup-DatabaseServices
    
    # Create startup scripts
    Create-StartupScripts
    
    # Show final instructions
    Show-FinalInstructions
}

# Run main function
Main 