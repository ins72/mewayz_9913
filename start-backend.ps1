# Backend startup script for PowerShell
Write-Host "🚀 Starting Mewayz Backend Server..." -ForegroundColor Green
Set-Location backend
.\venv\Scripts\Activate.ps1
uvicorn main:app --host 0.0.0.0 --port 8001 --reload
