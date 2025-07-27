# Docker startup script for PowerShell
Write-Host "🐳 Starting Mewayz Platform with Docker..." -ForegroundColor Green
docker-compose up -d
Write-Host "✅ Docker containers started successfully!" -ForegroundColor Green
Write-Host "🌐 Access URLs:" -ForegroundColor Cyan
Write-Host "   • Frontend: http://localhost:3000" -ForegroundColor White
Write-Host "   • Backend API: http://localhost:8001" -ForegroundColor White
Write-Host "   • API Docs: http://localhost:8001/docs" -ForegroundColor White 