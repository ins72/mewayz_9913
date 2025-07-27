# Frontend Startup Script
Write-Host "🚀 Starting Mewayz Frontend Server..." -ForegroundColor Green
Write-Host "🎯 Port: 3001 | API: http://localhost:8001" -ForegroundColor Yellow

Set-Location frontend

Write-Host "🌐 Starting React development server on http://localhost:3001" -ForegroundColor Cyan
Write-Host "📱 Frontend will connect to backend API" -ForegroundColor Cyan
Write-Host "Press Ctrl+C to stop the server" -ForegroundColor Yellow

# Start React development server on port 3001
npm start -- --port 3001
