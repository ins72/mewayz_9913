# Start both Mewayz services
Write-Host "🚀 Starting Mewayz Platform Services..." -ForegroundColor Green

# Start Frontend on port 3001
Write-Host "📱 Starting Frontend on port 3001..." -ForegroundColor Cyan
Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd frontend; npm start -- --port 3001"

# Wait a moment for frontend to start
Start-Sleep -Seconds 3

# Start Backend on port 8001 (No Database Mode)
Write-Host "🔧 Starting Backend on port 8001 (No Database Mode)..." -ForegroundColor Cyan
Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd backend; .\venv\Scripts\Activate.ps1; python main_no_db.py"

Write-Host "✅ Both services started!" -ForegroundColor Green
Write-Host "🌐 Frontend: http://localhost:3001" -ForegroundColor Yellow
Write-Host "🔧 Backend: http://localhost:8001" -ForegroundColor Yellow
Write-Host "📚 API Docs: http://localhost:8001/docs" -ForegroundColor Yellow 