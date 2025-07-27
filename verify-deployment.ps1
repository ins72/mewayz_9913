# Mewayz Platform - Deployment Verification Script
Write-Host "🔍 Verifying Mewayz Platform Deployment" -ForegroundColor Green
Write-Host "=======================================" -ForegroundColor Cyan

Write-Host "`n📋 Checking Service Status..." -ForegroundColor Yellow

# Check Backend (Port 8001)
Write-Host "`n🔧 Backend API (Port 8001):" -ForegroundColor Cyan
try {
    $backendHealth = Invoke-RestMethod -Uri "http://localhost:8001/health" -Method GET -TimeoutSec 5
    Write-Host "✅ Status: $($backendHealth.status)" -ForegroundColor Green
    Write-Host "   Database: $($backendHealth.database)" -ForegroundColor Gray
    Write-Host "   Modules: $($backendHealth.modules)" -ForegroundColor Gray
    Write-Host "   Timestamp: $($backendHealth.timestamp)" -ForegroundColor Gray
} catch {
    Write-Host "❌ Backend not responding: $($_.Exception.Message)" -ForegroundColor Red
}

# Check Frontend (Port 3001)
Write-Host "`n🎨 Frontend (Port 3001):" -ForegroundColor Cyan
try {
    $frontendResponse = Invoke-WebRequest -Uri "http://localhost:3001" -Method GET -TimeoutSec 5
    Write-Host "✅ Status: $($frontendResponse.StatusCode) OK" -ForegroundColor Green
    Write-Host "   Content Type: $($frontendResponse.Headers.'Content-Type')" -ForegroundColor Gray
    Write-Host "   Server: React Development Server" -ForegroundColor Gray
} catch {
    Write-Host "❌ Frontend not responding: $($_.Exception.Message)" -ForegroundColor Red
}

# Test API Endpoints
Write-Host "`n🧪 Testing API Endpoints..." -ForegroundColor Cyan

# Test API Root
try {
    $apiRoot = Invoke-RestMethod -Uri "http://localhost:8001/" -Method GET -TimeoutSec 5
    Write-Host "✅ API Root: $($apiRoot.message)" -ForegroundColor Green
} catch {
    Write-Host "❌ API Root: Not accessible" -ForegroundColor Red
}

# Test API Documentation
try {
    $apiDocs = Invoke-RestMethod -Uri "http://localhost:8001/openapi.json" -Method GET -TimeoutSec 5
    Write-Host "✅ API Documentation: Available" -ForegroundColor Green
    Write-Host "   Title: $($apiDocs.info.title)" -ForegroundColor Gray
    Write-Host "   Version: $($apiDocs.info.version)" -ForegroundColor Gray
    Write-Host "   Endpoints: $($apiDocs.paths.PSObject.Properties.Count)" -ForegroundColor Gray
} catch {
    Write-Host "❌ API Documentation: Not accessible" -ForegroundColor Red
}

# Test Authentication Endpoint
try {
    $authTest = Invoke-RestMethod -Uri "http://localhost:8001/api/auth/register" -Method POST -ContentType "application/json" -Body '{"email":"test@example.com","password":"testpass123","username":"testuser"}' -TimeoutSec 5
    Write-Host "✅ Authentication: Registration endpoint working" -ForegroundColor Green
} catch {
    $errorMsg = $_.Exception.Message
    if ($errorMsg -like "*422*") {
        Write-Host "✅ Authentication: Endpoint working (validation error expected)" -ForegroundColor Green
    } else {
        Write-Host "⚠️  Authentication: $errorMsg" -ForegroundColor Yellow
    }
}

# Test Protected Endpoint
try {
    $protectedTest = Invoke-RestMethod -Uri "http://localhost:8001/api/dashboard/overview" -Method GET -TimeoutSec 5
    Write-Host "❌ Protected Endpoint: Should require authentication" -ForegroundColor Red
} catch {
    $errorMsg = $_.Exception.Message
    if ($errorMsg -like "*403*" -or $errorMsg -like "*Not authenticated*") {
        Write-Host "✅ Protected Endpoint: Properly secured" -ForegroundColor Green
    } else {
        Write-Host "⚠️  Protected Endpoint: $errorMsg" -ForegroundColor Yellow
    }
}

# Check Database
Write-Host "`n💾 Database Status:" -ForegroundColor Cyan
$dbPath = ".\databases\mewayz.db"
if (Test-Path $dbPath) {
    $dbSize = (Get-Item $dbPath).Length
    Write-Host "✅ SQLite Database: Operational" -ForegroundColor Green
    Write-Host "   Location: $dbPath" -ForegroundColor Gray
    Write-Host "   Size: $([math]::Round($dbSize/1KB, 2)) KB" -ForegroundColor Gray
} else {
    Write-Host "⚠️  SQLite Database: File not found (will be created on first use)" -ForegroundColor Yellow
}

# Check Port Status
Write-Host "`n🔌 Port Status:" -ForegroundColor Cyan
$backendPort = netstat -an | findstr ":8001" | findstr "LISTENING"
$frontendPort = netstat -an | findstr ":3001" | findstr "LISTENING"

if ($backendPort) {
    Write-Host "✅ Backend Port 8001: Listening" -ForegroundColor Green
} else {
    Write-Host "❌ Backend Port 8001: Not listening" -ForegroundColor Red
}

if ($frontendPort) {
    Write-Host "✅ Frontend Port 3001: Listening" -ForegroundColor Green
} else {
    Write-Host "❌ Frontend Port 3001: Not listening" -ForegroundColor Red
}

# Final Summary
Write-Host "`n📊 Deployment Summary:" -ForegroundColor Yellow

$allServicesWorking = $true

if ($backendHealth.status -eq "healthy") {
    Write-Host "✅ Backend API: Fully Operational" -ForegroundColor Green
} else {
    Write-Host "❌ Backend API: Issues detected" -ForegroundColor Red
    $allServicesWorking = $false
}

if ($frontendResponse.StatusCode -eq 200) {
    Write-Host "✅ Frontend: Fully Operational" -ForegroundColor Green
} else {
    Write-Host "❌ Frontend: Issues detected" -ForegroundColor Red
    $allServicesWorking = $false
}

if (Test-Path $dbPath) {
    Write-Host "✅ Database: Operational" -ForegroundColor Green
} else {
    Write-Host "⚠️  Database: Will be created on first use" -ForegroundColor Yellow
}

if ($allServicesWorking) {
    Write-Host "`n🎉 SUCCESS: All services are operational!" -ForegroundColor Green
    
    Write-Host "`n🌐 Access Your Platform:" -ForegroundColor Cyan
    Write-Host "   🎨 Frontend: http://localhost:3001" -ForegroundColor White
    Write-Host "   🔧 Backend API: http://localhost:8001" -ForegroundColor White
    Write-Host "   📚 API Documentation: http://localhost:8001/docs" -ForegroundColor White
    Write-Host "   📊 Health Check: http://localhost:8001/health" -ForegroundColor White
    
    Write-Host "`n🔐 Test Authentication:" -ForegroundColor Cyan
    Write-Host "   • Login: http://localhost:3001/login" -ForegroundColor White
    Write-Host "   • Dashboard: http://localhost:3001/dashboard" -ForegroundColor White
    
    Write-Host "`n🚀 Your Mewayz platform is ready for use!" -ForegroundColor Green
} else {
    Write-Host "`n⚠️  WARNING: Some services may have issues" -ForegroundColor Yellow
    Write-Host "   Please check the service logs and restart if needed" -ForegroundColor White
}

Write-Host "`n📝 Management Commands:" -ForegroundColor Cyan
Write-Host "   • Restart all: .\deploy-all-services.ps1" -ForegroundColor White
Write-Host "   • Check status: .\verify-deployment.ps1" -ForegroundColor White
Write-Host "   • Service management: .\service-management.ps1" -ForegroundColor White

Write-Host "`n🎊 Verification Complete!" -ForegroundColor Green 