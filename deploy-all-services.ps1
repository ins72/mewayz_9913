# Mewayz Platform - Complete Deployment Script
# Deploys all services: Frontend (3001), Backend (8001), Database (5001)

Write-Host "🚀 Deploying Mewayz Platform - All Services" -ForegroundColor Green
Write-Host "=============================================" -ForegroundColor Cyan

Write-Host "`n📋 Service Configuration:" -ForegroundColor Yellow
Write-Host "   • Frontend: http://localhost:3001" -ForegroundColor White
Write-Host "   • Backend API: http://localhost:8001" -ForegroundColor White
Write-Host "   • Database: SQLite (Port 5001 for future expansion)" -ForegroundColor White

# Check if services are already running
Write-Host "`n🔍 Checking current service status..." -ForegroundColor Cyan

$backendRunning = $false
$frontendRunning = $false

try {
    $backendCheck = Invoke-RestMethod -Uri "http://localhost:8001/health" -Method GET -TimeoutSec 3
    $backendRunning = $true
    Write-Host "✅ Backend already running on port 8001" -ForegroundColor Green
} catch {
    Write-Host "❌ Backend not running on port 8001" -ForegroundColor Red
}

try {
    $frontendCheck = Invoke-WebRequest -Uri "http://localhost:3001" -Method GET -TimeoutSec 3
    $frontendRunning = $true
    Write-Host "✅ Frontend already running on port 3001" -ForegroundColor Green
} catch {
    Write-Host "❌ Frontend not running on port 3001" -ForegroundColor Red
}

# Start Backend (if not running)
if (-not $backendRunning) {
    Write-Host "`n🔧 Starting Backend Server on port 8001..." -ForegroundColor Cyan
    
    # Create backend startup script
    $backendScript = @"
# Backend Startup Script
Write-Host "🚀 Starting Mewayz Backend Server (SQLite Mode)..." -ForegroundColor Green
Write-Host "🎯 Port: 8001 | Database: SQLite" -ForegroundColor Yellow

Set-Location backend
.\\venv\\Scripts\\Activate.ps1

# Set environment variables
`$env:ENVIRONMENT = "development"
`$env:DEBUG = "true"

Write-Host "🌐 Starting server on http://localhost:8001" -ForegroundColor Cyan
Write-Host "📚 API Documentation: http://localhost:8001/docs" -ForegroundColor Cyan
Write-Host "📊 Health Check: http://localhost:8001/health" -ForegroundColor Cyan
Write-Host "Press Ctrl+C to stop the server" -ForegroundColor Yellow

# Start the server with SQLite version
uvicorn main_sqlite:app --host 0.0.0.0 --port 8001 --reload
"@

    $backendScript | Out-File -FilePath "start-backend-8001.ps1" -Encoding UTF8
    
    # Start backend in background
    Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd '$PWD'; .\\start-backend-8001.ps1"
    Write-Host "✅ Backend startup initiated" -ForegroundColor Green
    
    # Wait for backend to start
    Write-Host "⏳ Waiting for backend to start..." -ForegroundColor Yellow
    Start-Sleep -Seconds 10
}

# Start Frontend (if not running)
if (-not $frontendRunning) {
    Write-Host "`n🔧 Starting Frontend Server on port 3001..." -ForegroundColor Cyan
    
    # Create frontend startup script
    $frontendScript = @"
# Frontend Startup Script
Write-Host "🚀 Starting Mewayz Frontend Server..." -ForegroundColor Green
Write-Host "🎯 Port: 3001 | API: http://localhost:8001" -ForegroundColor Yellow

Set-Location frontend

Write-Host "🌐 Starting React development server on http://localhost:3001" -ForegroundColor Cyan
Write-Host "📱 Frontend will connect to backend API" -ForegroundColor Cyan
Write-Host "Press Ctrl+C to stop the server" -ForegroundColor Yellow

# Start React development server on port 3001
npm start -- --port 3001
"@

    $frontendScript | Out-File -FilePath "start-frontend-3001.ps1" -Encoding UTF8
    
    # Start frontend in background
    Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd '$PWD'; .\\start-frontend-3001.ps1"
    Write-Host "✅ Frontend startup initiated" -ForegroundColor Green
    
    # Wait for frontend to start
    Write-Host "⏳ Waiting for frontend to start..." -ForegroundColor Yellow
    Start-Sleep -Seconds 15
}

# Verify all services are running
Write-Host "`n🔍 Verifying all services..." -ForegroundColor Cyan

$allServicesRunning = $true

# Check Backend
try {
    $backendHealth = Invoke-RestMethod -Uri "http://localhost:8001/health" -Method GET -TimeoutSec 5
    Write-Host "✅ Backend: Running on http://localhost:8001" -ForegroundColor Green
    Write-Host "   Status: $($backendHealth.status)" -ForegroundColor Gray
    Write-Host "   Database: $($backendHealth.database)" -ForegroundColor Gray
    Write-Host "   Modules: $($backendHealth.modules)" -ForegroundColor Gray
} catch {
    Write-Host "❌ Backend: Not responding on port 8001" -ForegroundColor Red
    $allServicesRunning = $false
}

# Check Frontend
try {
    $frontendResponse = Invoke-WebRequest -Uri "http://localhost:3001" -Method GET -TimeoutSec 5
    Write-Host "✅ Frontend: Running on http://localhost:3001" -ForegroundColor Green
    Write-Host "   Status: $($frontendResponse.StatusCode)" -ForegroundColor Gray
} catch {
    Write-Host "❌ Frontend: Not responding on port 3001" -ForegroundColor Red
    $allServicesRunning = $false
}

# Check Database (SQLite)
$dbPath = ".\databases\mewayz.db"
if (Test-Path $dbPath) {
    Write-Host "✅ Database: SQLite operational at $dbPath" -ForegroundColor Green
} else {
    Write-Host "⚠️  Database: SQLite file not found, will be created on first use" -ForegroundColor Yellow
}

# Test API endpoints
Write-Host "`n🧪 Testing API endpoints..." -ForegroundColor Cyan

try {
    $apiRoot = Invoke-RestMethod -Uri "http://localhost:8001/" -Method GET -TimeoutSec 5
    Write-Host "✅ API Root: $($apiRoot.message)" -ForegroundColor Green
} catch {
    Write-Host "❌ API Root: Not accessible" -ForegroundColor Red
}

try {
    $apiDocs = Invoke-RestMethod -Uri "http://localhost:8001/openapi.json" -Method GET -TimeoutSec 5
    Write-Host "✅ API Documentation: Available ($($apiDocs.info.title) v$($apiDocs.info.version))" -ForegroundColor Green
} catch {
    Write-Host "❌ API Documentation: Not accessible" -ForegroundColor Red
}

# Create service management script
Write-Host "`n📝 Creating service management scripts..." -ForegroundColor Cyan

$managementScript = @"
# Mewayz Platform Service Management
Write-Host "🔧 Mewayz Platform Service Management" -ForegroundColor Green
Write-Host "=====================================" -ForegroundColor Cyan

Write-Host "`n📋 Available Commands:" -ForegroundColor Yellow
Write-Host "   • .\\start-backend-8001.ps1 - Start backend on port 8001" -ForegroundColor White
Write-Host "   • .\\start-frontend-3001.ps1 - Start frontend on port 3001" -ForegroundColor White
Write-Host "   • .\\deploy-all-services.ps1 - Deploy all services" -ForegroundColor White

Write-Host "`n🌐 Service URLs:" -ForegroundColor Yellow
Write-Host "   • Frontend: http://localhost:3001" -ForegroundColor White
Write-Host "   • Backend API: http://localhost:8001" -ForegroundColor White
Write-Host "   • API Docs: http://localhost:8001/docs" -ForegroundColor White
Write-Host "   • Health Check: http://localhost:8001/health" -ForegroundColor White

Write-Host "`n🔍 Check Service Status:" -ForegroundColor Yellow
Write-Host "   • netstat -an | findstr :8001 - Check backend" -ForegroundColor White
Write-Host "   • netstat -an | findstr :3001 - Check frontend" -ForegroundColor White

Write-Host "`n🎯 Quick Access:" -ForegroundColor Yellow
Write-Host "   • Open frontend: start http://localhost:3001" -ForegroundColor White
Write-Host "   • Open API docs: start http://localhost:8001/docs" -ForegroundColor White
Write-Host "   • Health check: start http://localhost:8001/health" -ForegroundColor White
"@

$managementScript | Out-File -FilePath "service-management.ps1" -Encoding UTF8

# Final status report
Write-Host "`n📊 Deployment Summary:" -ForegroundColor Yellow

if ($allServicesRunning) {
    Write-Host "🎉 SUCCESS: All services deployed successfully!" -ForegroundColor Green
    
    Write-Host "`n🌐 Access Your Mewayz Platform:" -ForegroundColor Cyan
    Write-Host "   🎨 Frontend: http://localhost:3001" -ForegroundColor White
    Write-Host "   🔧 Backend API: http://localhost:8001" -ForegroundColor White
    Write-Host "   📚 API Documentation: http://localhost:8001/docs" -ForegroundColor White
    Write-Host "   📊 Health Check: http://localhost:8001/health" -ForegroundColor White
    
    Write-Host "`n🔐 Authentication:" -ForegroundColor Cyan
    Write-Host "   • Login page: http://localhost:3001/login" -ForegroundColor White
    Write-Host "   • Dashboard: http://localhost:3001/dashboard" -ForegroundColor White
    
    Write-Host "`n📝 Management:" -ForegroundColor Cyan
    Write-Host "   • Service management: .\\service-management.ps1" -ForegroundColor White
    Write-Host "   • Restart all services: .\\deploy-all-services.ps1" -ForegroundColor White
    
    Write-Host "`n🚀 Your Mewayz platform is now fully operational!" -ForegroundColor Green
    Write-Host "   Frontend: ✅ Running on port 3001" -ForegroundColor Green
    Write-Host "   Backend: ✅ Running on port 8001" -ForegroundColor Green
    Write-Host "   Database: ✅ SQLite operational" -ForegroundColor Green
    
} else {
    Write-Host "⚠️  WARNING: Some services may not be fully operational" -ForegroundColor Yellow
    Write-Host "   Please check the service status and restart if needed" -ForegroundColor White
}

Write-Host "`n🎯 Next Steps:" -ForegroundColor Cyan
Write-Host "1. Open http://localhost:3001 to access the frontend" -ForegroundColor White
Write-Host "2. Test the login functionality" -ForegroundColor White
Write-Host "3. Explore the dashboard and API endpoints" -ForegroundColor White
Write-Host "4. Check http://localhost:8001/docs for API documentation" -ForegroundColor White

Write-Host "`n🔧 Troubleshooting:" -ForegroundColor Cyan
Write-Host "• If services don't start, check the PowerShell windows for errors" -ForegroundColor White
Write-Host "• Restart services: .\\deploy-all-services.ps1" -ForegroundColor White
Write-Host "• Check logs in the PowerShell windows running the services" -ForegroundColor White

Write-Host "`n🎊 Deployment Complete!" -ForegroundColor Green 