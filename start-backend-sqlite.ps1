# Backend startup script for SQLite version
Write-Host "🚀 Starting Mewayz Backend Server (SQLite Mode)..." -ForegroundColor Green
Write-Host "🎯 Lightweight database - No admin privileges required" -ForegroundColor Yellow

Set-Location backend
.\\venv\\Scripts\\Activate.ps1

Write-Host "🌐 Starting server on http://localhost:8001" -ForegroundColor Cyan
Write-Host "📚 API Documentation will be available at http://localhost:8001/docs" -ForegroundColor Cyan
Write-Host "📊 Database: SQLite (./databases/mewayz.db)" -ForegroundColor Cyan
Write-Host "Press Ctrl+C to stop the server" -ForegroundColor Yellow

# Start the server with SQLite version
uvicorn main_sqlite:app --host 0.0.0.0 --port 8001 --reload 