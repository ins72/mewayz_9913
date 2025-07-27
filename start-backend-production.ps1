# Production Backend Startup Script
Write-Host "🚀 Starting Mewayz Backend Server (Production Mode)..." -ForegroundColor Green
Write-Host "🔒 Production security enabled" -ForegroundColor Yellow

Set-Location backend
.\\venv\\Scripts\\Activate.ps1

# Set production environment
$env:ENVIRONMENT = "production"
$env:DEBUG = "false"

Write-Host "🌐 Starting server on http://localhost:8001" -ForegroundColor Cyan
Write-Host "📚 API Documentation: http://localhost:8001/docs" -ForegroundColor Cyan
Write-Host "📊 Health Check: http://localhost:8001/health" -ForegroundColor Cyan

# Start with production settings
gunicorn main_sqlite:app -w 4 -k uvicorn.workers.UvicornWorker --bind 0.0.0.0:8001 --access-logfile - --error-logfile - --log-level info
