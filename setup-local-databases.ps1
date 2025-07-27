# Local Database Setup Script for Mewayz Platform
Write-Host "🚀 Setting up Local Databases for Mewayz Platform" -ForegroundColor Green
Write-Host "==================================================" -ForegroundColor Cyan

Write-Host "`n📋 This script will help you set up local databases:" -ForegroundColor Yellow
Write-Host "   • MongoDB (Portable version)" -ForegroundColor White
Write-Host "   • Redis (Portable version)" -ForegroundColor White

# Create databases directory
$dbDir = ".\databases"
if (!(Test-Path $dbDir)) {
    New-Item -ItemType Directory -Path $dbDir -Force
    Write-Host "✅ Created databases directory" -ForegroundColor Green
}

# Create MongoDB data directory
$mongoDataDir = ".\databases\mongodb"
if (!(Test-Path $mongoDataDir)) {
    New-Item -ItemType Directory -Path $mongoDataDir -Force
    Write-Host "✅ Created MongoDB data directory" -ForegroundColor Green
}

# Create Redis data directory
$redisDataDir = ".\databases\redis"
if (!(Test-Path $redisDataDir)) {
    New-Item -ItemType Directory -Path $redisDataDir -Force
    Write-Host "✅ Created Redis data directory" -ForegroundColor Green
}

Write-Host "`n🔗 Option 1: Download Portable MongoDB" -ForegroundColor Cyan
Write-Host "1. Go to: https://www.mongodb.com/try/download/community" -ForegroundColor White
Write-Host "2. Download MongoDB Community Server (Windows x64)" -ForegroundColor White
Write-Host "3. Extract to: .\databases\mongodb" -ForegroundColor White
Write-Host "4. Run: .\databases\mongodb\bin\mongod.exe --dbpath .\databases\mongodb\data" -ForegroundColor White

Write-Host "`n🔗 Option 2: Use Python-based MongoDB Alternative" -ForegroundColor Cyan
Write-Host "We can use TinyDB or SQLite as a lightweight alternative" -ForegroundColor White

Write-Host "`n🔗 Option 3: Use Cloud Databases (Recommended)" -ForegroundColor Cyan
Write-Host "1. MongoDB Atlas (Free): https://www.mongodb.com/atlas" -ForegroundColor White
Write-Host "2. Redis Cloud (Free): https://redis.com/try-free/" -ForegroundColor White

Write-Host "`n📝 Environment Configuration" -ForegroundColor Cyan
Write-Host "Update backend\.env with your chosen database URLs:" -ForegroundColor White

$envContent = @"
# Database Configuration
# Option 1: Local MongoDB (if you install portable version)
MONGO_URL=mongodb://localhost:27017/mewayz

# Option 2: Cloud MongoDB Atlas
# MONGO_URL=mongodb+srv://username:password@cluster.mongodb.net/mewayz?retryWrites=true&w=majority

# Option 3: SQLite (lightweight alternative)
# MONGO_URL=sqlite:///./databases/mewayz.db

# Redis Configuration
# Option 1: Local Redis (if you install portable version)
REDIS_URL=redis://localhost:6379

# Option 2: Cloud Redis
# REDIS_URL=redis://username:password@host:port

# Application Configuration
ENVIRONMENT=development
DEBUG=true
JWT_SECRET=mewayz-dev-jwt-secret-key-2025
ENCRYPTION_KEY=mewayz-32-byte-encryption-key-2025

# API Keys (Add your actual keys)
OPENAI_API_KEY=your-openai-api-key
STRIPE_SECRET_KEY=your-stripe-secret-key
STRIPE_PUBLISHABLE_KEY=your-stripe-publishable-key
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret

# Email Configuration
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=your-email@gmail.com
SMTP_PASSWORD=your-app-password
"@

Write-Host "`n📄 Example .env content:" -ForegroundColor Yellow
Write-Host $envContent -ForegroundColor Gray

Write-Host "`n✅ Ready to proceed?" -ForegroundColor Green
Write-Host "Choose your preferred database option and update the .env file" -ForegroundColor White
Write-Host "Then run: .\start-backend.ps1" -ForegroundColor Cyan

Write-Host "`n🌐 Your Mewayz platform will be available at:" -ForegroundColor Yellow
Write-Host "  Frontend: http://localhost:3001" -ForegroundColor White
Write-Host "  Backend: http://localhost:8001" -ForegroundColor White
Write-Host "  API Docs: http://localhost:8001/docs" -ForegroundColor White 