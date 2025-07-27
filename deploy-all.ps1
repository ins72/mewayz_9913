# Mewayz Platform Complete Deployment Script
# Deploys Frontend (3000), Backend (8000), and MongoDB (5000)

Write-Host "🚀 MEWAYZ PLATFORM - COMPLETE DEPLOYMENT" -ForegroundColor Green
Write-Host "=========================================" -ForegroundColor Cyan

# Stop any existing processes
Write-Host "`n🛑 Stopping existing processes..." -ForegroundColor Yellow
taskkill /f /im python.exe 2>$null
taskkill /f /im node.exe 2>$null
taskkill /f /im mongod.exe 2>$null

# Create necessary directories
Write-Host "`n📁 Creating directories..." -ForegroundColor Yellow
if (!(Test-Path "C:\data\db")) {
    New-Item -ItemType Directory -Path "C:\data\db" -Force
    Write-Host "✅ Created MongoDB data directory" -ForegroundColor Green
}

if (!(Test-Path "C:\data\log")) {
    New-Item -ItemType Directory -Path "C:\data\log" -Force
    Write-Host "✅ Created MongoDB log directory" -ForegroundColor Green
}

# Step 1: Start MongoDB on port 5000
Write-Host "`n🔗 Step 1: Starting MongoDB on port 5000..." -ForegroundColor Cyan
Start-Process -FilePath "C:\Program Files\MongoDB\Server\8.1\bin\mongod.exe" -ArgumentList "--port", "5000", "--dbpath", "C:\data\db", "--logpath", "C:\data\log\mongod.log" -WindowStyle Hidden
Start-Sleep -Seconds 3

# Check if MongoDB is running
$mongoRunning = netstat -an | findstr ":5000"
if ($mongoRunning) {
    Write-Host "✅ MongoDB started successfully on port 5000" -ForegroundColor Green
} else {
    Write-Host "❌ MongoDB failed to start" -ForegroundColor Red
    exit 1
}

# Step 2: Start Backend on port 8000
Write-Host "`n🔧 Step 2: Starting Backend on port 8000..." -ForegroundColor Cyan
Set-Location backend

# Install dependencies if needed
if (!(Test-Path "venv")) {
    Write-Host "📦 Creating virtual environment..." -ForegroundColor Yellow
    python -m venv venv
}

# Activate virtual environment and install dependencies
Write-Host "📦 Installing backend dependencies..." -ForegroundColor Yellow
.\venv\Scripts\Activate.ps1
pip install -r requirements.txt

# Start backend
Write-Host "🚀 Starting backend server..." -ForegroundColor Yellow
Start-Process -FilePath "python" -ArgumentList "simple_backend.py" -WindowStyle Hidden
Start-Sleep -Seconds 5

# Check if backend is running
$backendRunning = netstat -an | findstr ":8000"
if ($backendRunning) {
    Write-Host "✅ Backend started successfully on port 8000" -ForegroundColor Green
} else {
    Write-Host "❌ Backend failed to start" -ForegroundColor Red
}

Set-Location ..

# Step 3: Start Frontend on port 3000
Write-Host "`n🌐 Step 3: Starting Frontend on port 3000..." -ForegroundColor Cyan
Set-Location frontend

# Install frontend dependencies if needed
if (!(Test-Path "node_modules")) {
    Write-Host "📦 Installing frontend dependencies..." -ForegroundColor Yellow
    npm install
}

# Start frontend
Write-Host "🚀 Starting frontend server..." -ForegroundColor Yellow
Start-Process -FilePath "npm" -ArgumentList "start" -WindowStyle Hidden
Start-Sleep -Seconds 10

# Check if frontend is running
$frontendRunning = netstat -an | findstr ":3000"
if ($frontendRunning) {
    Write-Host "✅ Frontend started successfully on port 3000" -ForegroundColor Green
} else {
    Write-Host "❌ Frontend failed to start" -ForegroundColor Red
}

Set-Location ..

# Final status check
Write-Host "`n📊 DEPLOYMENT STATUS:" -ForegroundColor Green
Write-Host "====================" -ForegroundColor Cyan

$mongoStatus = netstat -an | findstr ":5000"
$backendStatus = netstat -an | findstr ":8000"
$frontendStatus = netstat -an | findstr ":3000"

Write-Host "MongoDB (5000):   $(if ($mongoStatus) { '✅ RUNNING' } else { '❌ STOPPED' })" -ForegroundColor $(if ($mongoStatus) { 'Green' } else { 'Red' })
Write-Host "Backend (8000):   $(if ($backendStatus) { '✅ RUNNING' } else { '❌ STOPPED' })" -ForegroundColor $(if ($backendStatus) { 'Green' } else { 'Red' })
Write-Host "Frontend (3000):  $(if ($frontendStatus) { '✅ RUNNING' } else { '❌ STOPPED' })" -ForegroundColor $(if ($frontendStatus) { 'Green' } else { 'Red' })

Write-Host "`n🌐 ACCESS URLs:" -ForegroundColor Green
Write-Host "==============" -ForegroundColor Cyan
Write-Host "Frontend:  http://localhost:3000" -ForegroundColor White
Write-Host "Backend:   http://localhost:8000" -ForegroundColor White
Write-Host "Health:    http://localhost:8000/health" -ForegroundColor White
Write-Host "API Docs:  http://localhost:8000/docs" -ForegroundColor White

Write-Host "`n✅ Deployment completed!" -ForegroundColor Green
Write-Host "Press any key to stop all services..." -ForegroundColor Yellow
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")

# Stop all services
Write-Host "`n🛑 Stopping all services..." -ForegroundColor Yellow
taskkill /f /im python.exe 2>$null
taskkill /f /im node.exe 2>$null
taskkill /f /im mongod.exe 2>$null
Write-Host "✅ All services stopped" -ForegroundColor Green 