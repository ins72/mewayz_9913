# SQLite Database Setup for Mewayz Platform
Write-Host "🚀 Setting up SQLite Database for Mewayz Platform" -ForegroundColor Green
Write-Host "==================================================" -ForegroundColor Cyan

Write-Host "`n📋 SQLite Setup (No Admin Required):" -ForegroundColor Yellow
Write-Host "   • Lightweight database" -ForegroundColor White
Write-Host "   • No installation required" -ForegroundColor White
Write-Host "   • Perfect for development" -ForegroundColor White

# Create databases directory
$dbDir = ".\databases"
if (!(Test-Path $dbDir)) {
    New-Item -ItemType Directory -Path $dbDir -Force
    Write-Host "✅ Created databases directory" -ForegroundColor Green
}

# Create SQLite database file
$sqliteDbPath = ".\databases\mewayz.db"
if (!(Test-Path $sqliteDbPath)) {
    New-Item -ItemType File -Path $sqliteDbPath -Force
    Write-Host "✅ Created SQLite database file" -ForegroundColor Green
}

Write-Host "`n🔧 Installing SQLite dependencies..." -ForegroundColor Cyan
cd backend
.\venv\Scripts\Activate.ps1

# Install SQLite support
pip install aiosqlite sqlalchemy

Write-Host "✅ SQLite dependencies installed" -ForegroundColor Green

cd ..

Write-Host "`n📝 Creating .env file with SQLite configuration..." -ForegroundColor Cyan

$envContent = @"
# Database Configuration - SQLite (No Admin Required)
MONGO_URL=sqlite:///./databases/mewayz.db
REDIS_URL=redis://localhost:6379

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

$envContent | Out-File -FilePath "backend\.env" -Encoding UTF8
Write-Host "✅ Created backend\.env file" -ForegroundColor Green

Write-Host "`n🎉 SQLite Database Setup Complete!" -ForegroundColor Green
Write-Host "`n✅ Ready to start the backend:" -ForegroundColor Yellow
Write-Host "  .\start-backend.ps1" -ForegroundColor Cyan

Write-Host "`n🌐 Your Mewayz platform will be available at:" -ForegroundColor Yellow
Write-Host "  Frontend: http://localhost:3001" -ForegroundColor White
Write-Host "  Backend: http://localhost:8001" -ForegroundColor White
Write-Host "  API Docs: http://localhost:8001/docs" -ForegroundColor White

Write-Host "`n📊 Database file location:" -ForegroundColor Yellow
Write-Host "  $sqliteDbPath" -ForegroundColor White 