# Simple backend startup script
Write-Host "🚀 Starting Mewayz Backend Server..." -ForegroundColor Green

Set-Location backend
.\venv\Scripts\Activate.ps1

Write-Host "🌐 Starting server on http://localhost:8001" -ForegroundColor Cyan
Write-Host "📚 API Documentation will be available at http://localhost:8001/docs" -ForegroundColor Cyan

# Start the server
uvicorn main_no_db:app --host 0.0.0.0 --port 8001 --reload 