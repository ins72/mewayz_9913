# Mewayz Platform - Simple Installation Script (No Admin Required)
# This script sets up the Mewayz platform using existing Python and Node.js installations

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

# Function to check versions
function Check-Versions {
    Write-Header "🔍 Checking System Requirements"
    
    # Check Python
    if (Test-Command "python") {
        $pythonVersion = python --version 2>&1
        Write-Status "Python: $pythonVersion ✓"
    } else {
        Write-Error "Python not found. Please install Python 3.11+"
        return $false
    }
    
    # Check Node.js
    if (Test-Command "node") {
        $nodeVersion = node --version
        Write-Status "Node.js: $nodeVersion ✓"
    } else {
        Write-Error "Node.js not found. Please install Node.js 18+"
        return $false
    }
    
    # Check npm
    if (Test-Command "npm") {
        $npmVersion = npm --version
        Write-Status "npm: $npmVersion ✓"
    } else {
        Write-Error "npm not found. Please install npm"
        return $false
    }
    
    return $true
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
ENVIRONMENT=development
DEBUG=true
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
REACT_APP_ENVIRONMENT=development
"@ | Out-File -FilePath "frontend\.env" -Encoding UTF8
        
        Write-Success "Frontend .env created ✓"
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
    Write-Success "✅ Mewayz platform setup completed successfully!"
    Write-Host ""
    Write-Status "📋 Next Steps:"
    Write-Host "   1. Update API keys in backend\.env and frontend\.env"
    Write-Host "   2. Set up MongoDB and Redis (or use Docker)"
    Write-Host "   3. Configure your domain and SSL certificates for production"
    Write-Host ""
    Write-Status "🚀 Start the application:"
    Write-Host "   • Backend only: .\start-backend.bat or .\start-backend.ps1"
    Write-Host "   • Frontend only: .\start-frontend.bat or .\start-frontend.ps1"
    Write-Host "   • Both services: Run both scripts in separate terminals"
    Write-Host ""
    Write-Status "🌐 Access URLs:"
    Write-Host "   • Frontend: http://localhost:3000"
    Write-Host "   • Backend API: http://localhost:8001"
    Write-Host "   • API Docs: http://localhost:8001/docs"
    Write-Host ""
    Write-Status "📚 Documentation:"
    Write-Host "   • Deployment Guide: DEPLOYMENT_INSTALLATION_GUIDE.md"
    Write-Host "   • API Documentation: docs\api\"
    Write-Host ""
    Write-Warning "⚠️  Database Setup Options:"
    Write-Host "   • Option 1: Install MongoDB and Redis locally"
    Write-Host "   • Option 2: Use Docker containers"
    Write-Host "   • Option 3: Use cloud services (MongoDB Atlas, Redis Cloud)"
    Write-Host ""
    Write-Success "🎯 Your Mewayz platform is ready for development!"
}

# Main installation function
function Main {
    Write-Header "🚀 Mewayz Platform - Simple Installation"
    Write-Header "========================================="
    Write-Host ""
    
    # Check system requirements
    if (!(Check-Versions)) {
        Write-Error "System requirements not met. Please install missing dependencies."
        exit 1
    }
    
    # Setup project environments
    Setup-PythonEnv
    Setup-NodeEnv
    
    # Create configuration files
    Create-EnvFiles
    
    # Create startup scripts
    Create-StartupScripts
    
    # Show final instructions
    Show-FinalInstructions
}

# Run main function
Main 