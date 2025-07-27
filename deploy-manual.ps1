# Mewayz Platform Manual Deployment Script
# Run each step manually

Write-Host "🚀 MEWAYZ PLATFORM - MANUAL DEPLOYMENT" -ForegroundColor Green
Write-Host "=====================================" -ForegroundColor Cyan

Write-Host "`n📋 This script will help you deploy the platform step by step:" -ForegroundColor Yellow
Write-Host "1. Start MongoDB on port 5000" -ForegroundColor White
Write-Host "2. Start Backend on port 8000" -ForegroundColor White
Write-Host "3. Start Frontend on port 3000" -ForegroundColor White

Write-Host "`n🔗 Step 1: Start MongoDB" -ForegroundColor Cyan
Write-Host "Run this command in a new PowerShell window:" -ForegroundColor Yellow
Write-Host "& 'C:\Program Files\MongoDB\Server\8.1\bin\mongod.exe' --port 5000 --dbpath 'C:\data\db' --logpath 'C:\data\log\mongod.log'" -ForegroundColor White

Write-Host "`n🔧 Step 2: Start Backend" -ForegroundColor Cyan
Write-Host "Run these commands in a new PowerShell window:" -ForegroundColor Yellow
Write-Host "cd backend" -ForegroundColor White
Write-Host "python -m venv venv" -ForegroundColor White
Write-Host ".\venv\Scripts\Activate.ps1" -ForegroundColor White
Write-Host "pip install -r requirements.txt" -ForegroundColor White
Write-Host "python simple_backend.py" -ForegroundColor White

Write-Host "`n🌐 Step 3: Start Frontend" -ForegroundColor Cyan
Write-Host "Run these commands in a new PowerShell window:" -ForegroundColor Yellow
Write-Host "cd frontend" -ForegroundColor White
Write-Host "npm install" -ForegroundColor White
Write-Host "npm start" -ForegroundColor White

Write-Host "`n🌐 ACCESS URLs:" -ForegroundColor Green
Write-Host "==============" -ForegroundColor Cyan
Write-Host "Frontend:  http://localhost:3000" -ForegroundColor White
Write-Host "Backend:   http://localhost:8000" -ForegroundColor White
Write-Host "Health:    http://localhost:8000/health" -ForegroundColor White
Write-Host "API Docs:  http://localhost:8000/docs" -ForegroundColor White

Write-Host "`n✅ Ready to deploy! Follow the steps above." -ForegroundColor Green 