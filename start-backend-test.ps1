# Backend test startup script for PowerShell
Write-Host "🚀 Starting Mewayz Backend Server (Test Mode)..." -ForegroundColor Green
Write-Host "⚠️  Note: This is a test mode - some features may not work without database" -ForegroundColor Yellow

Set-Location backend
.\venv\Scripts\Activate.ps1

# Set environment variables for test mode
$env:ENVIRONMENT = "development"
$env:DEBUG = "true"
$env:MONGO_URL = "mongodb://localhost:27017/mewayz_test"
$env:REDIS_URL = "redis://localhost:6379"

Write-Host "🌐 Starting server on http://localhost:8001" -ForegroundColor Cyan
Write-Host "📚 API Documentation will be available at http://localhost:8001/docs" -ForegroundColor Cyan
Write-Host "Press Ctrl+C to stop the server" -ForegroundColor Yellow

uvicorn main:app --host 0.0.0.0 --port 8001 --reload 