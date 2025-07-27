# Mewayz Platform Service Management
Write-Host "🔧 Mewayz Platform Service Management" -ForegroundColor Green
Write-Host "=====================================" -ForegroundColor Cyan

Write-Host "
📋 Available Commands:" -ForegroundColor Yellow
Write-Host "   • .\\start-backend-8001.ps1 - Start backend on port 8001" -ForegroundColor White
Write-Host "   • .\\start-frontend-3001.ps1 - Start frontend on port 3001" -ForegroundColor White
Write-Host "   • .\\deploy-all-services.ps1 - Deploy all services" -ForegroundColor White

Write-Host "
🌐 Service URLs:" -ForegroundColor Yellow
Write-Host "   • Frontend: http://localhost:3001" -ForegroundColor White
Write-Host "   • Backend API: http://localhost:8001" -ForegroundColor White
Write-Host "   • API Docs: http://localhost:8001/docs" -ForegroundColor White
Write-Host "   • Health Check: http://localhost:8001/health" -ForegroundColor White

Write-Host "
🔍 Check Service Status:" -ForegroundColor Yellow
Write-Host "   • netstat -an | findstr :8001 - Check backend" -ForegroundColor White
Write-Host "   • netstat -an | findstr :3001 - Check frontend" -ForegroundColor White

Write-Host "
🎯 Quick Access:" -ForegroundColor Yellow
Write-Host "   • Open frontend: start http://localhost:3001" -ForegroundColor White
Write-Host "   • Open API docs: start http://localhost:8001/docs" -ForegroundColor White
Write-Host "   • Health check: start http://localhost:8001/health" -ForegroundColor White
