# Frontend Issue Fixer Script
Write-Host "🔧 Fixing Frontend Compilation Issues" -ForegroundColor Green
Write-Host "====================================" -ForegroundColor Cyan

Write-Host "`n📋 Checking for common issues..." -ForegroundColor Yellow

# Check if App.css exists
$appCssPath = "frontend\src\App.css"
if (-not (Test-Path $appCssPath)) {
    Write-Host "❌ App.css missing - creating it..." -ForegroundColor Red
    
    $appCssContent = @"
/* App.css - Main application styles */

.App {
  text-align: center;
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.App-header {
  background-color: #282c34;
  padding: 20px;
  color: white;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.btn {
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 16px;
  transition: all 0.3s ease;
}

.btn-primary {
  background-color: #007bff;
  color: white;
}

.form-group {
  margin-bottom: 15px;
}

.form-control {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 5px;
  font-size: 16px;
}

.card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  padding: 20px;
  margin-bottom: 20px;
}
"@
    
    $appCssContent | Out-File -FilePath $appCssPath -Encoding UTF8
    Write-Host "✅ App.css created successfully" -ForegroundColor Green
} else {
    Write-Host "✅ App.css exists" -ForegroundColor Green
}

# Check for syntax errors in apiService.js
$apiServicePath = "frontend\src\services\apiService.js"
if (Test-Path $apiServicePath) {
    Write-Host "`n🔍 Checking apiService.js for syntax errors..." -ForegroundColor Cyan
    
    $content = Get-Content $apiServicePath -Raw
    
    # Check for common syntax issues
    $issues = @()
    
    if ($content -match '\$\{this\.baseURL\}' -and $content -notmatch '`\$\{this\.baseURL\}`') {
        $issues += "Template literal syntax error in baseURL"
    }
    
    if ($content -match 'Bearer\s*;' -and $content -notmatch 'Bearer\s*\$\{this\.token\}') {
        $issues += "Bearer token syntax error"
    }
    
    if ($content -match '/api/.*\?.*=' -and $content -notmatch '`/api/.*\?.*=\$\{.*\}`') {
        $issues += "Template literal missing in API endpoints"
    }
    
    if ($issues.Count -gt 0) {
        Write-Host "❌ Found syntax issues in apiService.js:" -ForegroundColor Red
        foreach ($issue in $issues) {
            Write-Host "   • $issue" -ForegroundColor Yellow
        }
        
        Write-Host "`n🔧 Attempting to fix syntax errors..." -ForegroundColor Cyan
        
        # Fix common issues
        $fixedContent = $content -replace '\$\{this\.baseURL\}', '`${this.baseURL}${endpoint}`'
        $fixedContent = $fixedContent -replace 'Bearer\s*;', '`Bearer ${this.token}`'
        $fixedContent = $fixedContent -replace 'HTTP\s*\)', '`HTTP ${response.status}`'
        $fixedContent = $fixedContent -replace '/api/analytics/dashboard\?period=', '`/api/analytics/dashboard?period=${period}`'
        $fixedContent = $fixedContent -replace '/api/workspaces/', '`/api/workspaces/${workspaceId}`'
        $fixedContent = $fixedContent -replace '/api/ecommerce/products\?limit=', '`/api/ecommerce/products?limit=${limit}`'
        $fixedContent = $fixedContent -replace '/api/ecommerce/products/', '`/api/ecommerce/products/${productId}`'
        
        $fixedContent | Out-File -FilePath $apiServicePath -Encoding UTF8
        Write-Host "✅ Syntax errors fixed" -ForegroundColor Green
    } else {
        Write-Host "✅ No syntax errors found" -ForegroundColor Green
    }
} else {
    Write-Host "❌ apiService.js not found" -ForegroundColor Red
}

# Check if frontend is running
Write-Host "`n🔍 Checking frontend status..." -ForegroundColor Cyan
$frontendRunning = netstat -an | findstr ":3001" | findstr "LISTENING"

if ($frontendRunning) {
    Write-Host "✅ Frontend is running on port 3001" -ForegroundColor Green
    
    # Test frontend response
    try {
        $response = Invoke-WebRequest -Uri "http://localhost:3001" -Method GET -TimeoutSec 5
        Write-Host "✅ Frontend responding correctly (Status: $($response.StatusCode))" -ForegroundColor Green
    } catch {
        Write-Host "❌ Frontend not responding properly" -ForegroundColor Red
        Write-Host "   Error: $($_.Exception.Message)" -ForegroundColor Yellow
        
        Write-Host "`n🔄 Restarting frontend..." -ForegroundColor Cyan
        # Kill existing frontend process
        taskkill /f /im node.exe 2>$null
        
        # Start frontend again
        Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd '$PWD\frontend'; npm start -- --port 3001"
        
        Write-Host "⏳ Waiting for frontend to start..." -ForegroundColor Yellow
        Start-Sleep -Seconds 15
        
        # Test again
        try {
            $response = Invoke-WebRequest -Uri "http://localhost:3001" -Method GET -TimeoutSec 5
            Write-Host "✅ Frontend restarted successfully" -ForegroundColor Green
        } catch {
            Write-Host "❌ Frontend restart failed" -ForegroundColor Red
        }
    }
} else {
    Write-Host "❌ Frontend not running on port 3001" -ForegroundColor Red
    Write-Host "`n🚀 Starting frontend..." -ForegroundColor Cyan
    
    Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd '$PWD\frontend'; npm start -- --port 3001"
    
    Write-Host "⏳ Waiting for frontend to start..." -ForegroundColor Yellow
    Start-Sleep -Seconds 15
    
    # Test if it's now running
    $frontendRunning = netstat -an | findstr ":3001" | findstr "LISTENING"
    if ($frontendRunning) {
        Write-Host "✅ Frontend started successfully" -ForegroundColor Green
    } else {
        Write-Host "❌ Frontend failed to start" -ForegroundColor Red
    }
}

Write-Host "`n📊 Summary:" -ForegroundColor Yellow
Write-Host "   • App.css: $(if (Test-Path $appCssPath) { '✅ Exists' } else { '❌ Missing' })" -ForegroundColor White
Write-Host "   • apiService.js: $(if (Test-Path $apiServicePath) { '✅ Exists' } else { '❌ Missing' })" -ForegroundColor White
Write-Host "   • Frontend: $(if ($frontendRunning) { '✅ Running' } else { '❌ Not Running' })" -ForegroundColor White

Write-Host "`n🌐 Access URLs:" -ForegroundColor Cyan
Write-Host "   • Frontend: http://localhost:3001" -ForegroundColor White
Write-Host "   • Backend: http://localhost:8001" -ForegroundColor White
Write-Host "   • API Docs: http://localhost:8001/docs" -ForegroundColor White

Write-Host "`n🎊 Frontend issues fixed!" -ForegroundColor Green 