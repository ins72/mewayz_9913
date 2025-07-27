# Backend startup script without database requirement
Write-Host "🚀 Starting Mewayz Backend Server (No Database Mode)..." -ForegroundColor Green
Write-Host "⚠️  Note: This mode runs without database - some features will be limited" -ForegroundColor Yellow

Set-Location backend
.\venv\Scripts\Activate.ps1

# Set environment variables for no-database mode
$env:ENVIRONMENT = "development"
$env:DEBUG = "true"
$env:SKIP_DATABASE = "true"

Write-Host "🌐 Starting server on http://localhost:8001" -ForegroundColor Cyan
Write-Host "📚 API Documentation will be available at http://localhost:8001/docs" -ForegroundColor Cyan
Write-Host "Press Ctrl+C to stop the server" -ForegroundColor Yellow

# Start the server with a modified command that skips database initialization
python -c "
import os
os.environ['SKIP_DATABASE'] = 'true'
import uvicorn
from main import app
uvicorn.run(app, host='0.0.0.0', port=8001, reload=True)
" 