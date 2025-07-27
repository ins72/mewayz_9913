# Development Backend Startup Script
Write-Host "🚀 Starting Mewayz Backend Server (Development Mode)..." -ForegroundColor Green
Write-Host "🔧 Development mode with hot reload" -ForegroundColor Yellow

Set-Location backend
.\\venv\\Scripts\\Activate.ps1

# Set development environment
$env:ENVIRONMENT = "development"
$env:DEBUG = "true"

Write-Host "🌐 Starting server on http://localhost:8001" -ForegroundColor Cyan
Write-Host "📚 API Documentation: http://localhost:8001/docs" -ForegroundColor Cyan
Write-Host "📊 Health Check: http://localhost:8001/health" -ForegroundColor Cyan

# Start with development settings
uvicorn main_sqlite:app --host 0.0.0.0 --port 8001 --reload --log-level debug
