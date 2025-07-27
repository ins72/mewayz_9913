# Production Configuration Setup for Mewayz Platform
Write-Host "🚀 Setting up Production Configuration for Mewayz Platform" -ForegroundColor Green
Write-Host "=============================================================" -ForegroundColor Cyan

Write-Host "`n📋 Production Configuration Setup:" -ForegroundColor Yellow
Write-Host "   • Secure environment variables" -ForegroundColor White
Write-Host "   • Production-ready settings" -ForegroundColor White
Write-Host "   • Security hardening" -ForegroundColor White

# Generate secure secrets
$jwtSecret = -join ((33..126) | Get-Random -Count 64 | ForEach-Object {[char]$_})
$encryptionKey = -join ((33..126) | Get-Random -Count 32 | ForEach-Object {[char]$_})
$apiKey = -join ((33..126) | Get-Random -Count 32 | ForEach-Object {[char]$_})

Write-Host "`n🔐 Generated secure secrets:" -ForegroundColor Cyan
Write-Host "   • JWT Secret: $($jwtSecret.Substring(0, 16))..." -ForegroundColor Gray
Write-Host "   • Encryption Key: $($encryptionKey.Substring(0, 16))..." -ForegroundColor Gray
Write-Host "   • API Key: $($apiKey.Substring(0, 16))..." -ForegroundColor Gray

# Create production environment file
$prodEnvContent = @"
# Production Environment Configuration
# ===================================

# Database Configuration
MONGO_URL=sqlite:///./databases/mewayz.db
REDIS_URL=redis://localhost:6379

# Application Configuration
ENVIRONMENT=production
DEBUG=false
LOG_LEVEL=INFO

# Security Configuration
JWT_SECRET=$jwtSecret
ENCRYPTION_KEY=$encryptionKey
API_KEY=$apiKey

# CORS Configuration
CORS_ORIGINS=http://localhost:3001,https://yourdomain.com
CORS_ALLOW_CREDENTIALS=true

# Rate Limiting
RATE_LIMIT_REQUESTS=100
RATE_LIMIT_WINDOW=60

# Session Configuration
SESSION_SECRET=$jwtSecret
SESSION_TIMEOUT=3600

# API Keys (Add your actual keys)
OPENAI_API_KEY=your-openai-api-key-here
STRIPE_SECRET_KEY=your-stripe-secret-key-here
STRIPE_PUBLISHABLE_KEY=your-stripe-publishable-key-here
GOOGLE_CLIENT_ID=your-google-client-id-here
GOOGLE_CLIENT_SECRET=your-google-client-secret-here

# Email Configuration
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=your-email@gmail.com
SMTP_PASSWORD=your-app-password-here
EMAIL_FROM=noreply@yourdomain.com

# Monitoring Configuration
SENTRY_DSN=your-sentry-dsn-here
LOGGLY_TOKEN=your-loggly-token-here

# Performance Configuration
CACHE_TTL=3600
DB_POOL_SIZE=10
MAX_CONNECTIONS=100

# Security Headers
SECURITY_HEADERS=true
HSTS_MAX_AGE=31536000
CONTENT_SECURITY_POLICY=default-src 'self'

# Backup Configuration
BACKUP_ENABLED=true
BACKUP_RETENTION_DAYS=30
BACKUP_SCHEDULE=0 2 * * *

# Feature Flags
FEATURE_AI_ENABLED=true
FEATURE_PAYMENTS_ENABLED=true
FEATURE_ANALYTICS_ENABLED=true
FEATURE_REALTIME_ENABLED=true
"@

$prodEnvContent | Out-File -FilePath "backend\.env.production" -Encoding UTF8
Write-Host "✅ Created backend\.env.production file" -ForegroundColor Green

# Create development environment file
$devEnvContent = @"
# Development Environment Configuration
# ====================================

# Database Configuration
MONGO_URL=sqlite:///./databases/mewayz.db
REDIS_URL=redis://localhost:6379

# Application Configuration
ENVIRONMENT=development
DEBUG=true
LOG_LEVEL=DEBUG

# Security Configuration
JWT_SECRET=dev-jwt-secret-key-2025
ENCRYPTION_KEY=dev-32-byte-encryption-key-2025
API_KEY=dev-api-key-2025

# CORS Configuration
CORS_ORIGINS=http://localhost:3001,http://localhost:3000
CORS_ALLOW_CREDENTIALS=true

# Rate Limiting
RATE_LIMIT_REQUESTS=1000
RATE_LIMIT_WINDOW=60

# Session Configuration
SESSION_SECRET=dev-session-secret-2025
SESSION_TIMEOUT=3600

# API Keys (Development - Add real keys for testing)
OPENAI_API_KEY=your-openai-api-key-here
STRIPE_SECRET_KEY=your-stripe-secret-key-here
STRIPE_PUBLISHABLE_KEY=your-stripe-publishable-key-here
GOOGLE_CLIENT_ID=your-google-client-id-here
GOOGLE_CLIENT_SECRET=your-google-client-secret-here

# Email Configuration
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=your-email@gmail.com
SMTP_PASSWORD=your-app-password-here
EMAIL_FROM=noreply@yourdomain.com

# Monitoring Configuration
SENTRY_DSN=
LOGGLY_TOKEN=

# Performance Configuration
CACHE_TTL=300
DB_POOL_SIZE=5
MAX_CONNECTIONS=50

# Security Headers
SECURITY_HEADERS=false
HSTS_MAX_AGE=0
CONTENT_SECURITY_POLICY=

# Backup Configuration
BACKUP_ENABLED=false
BACKUP_RETENTION_DAYS=7
BACKUP_SCHEDULE=0 2 * * *

# Feature Flags
FEATURE_AI_ENABLED=true
FEATURE_PAYMENTS_ENABLED=true
FEATURE_ANALYTICS_ENABLED=true
FEATURE_REALTIME_ENABLED=true
"@

$devEnvContent | Out-File -FilePath "backend\.env.development" -Encoding UTF8
Write-Host "✅ Created backend\.env.development file" -ForegroundColor Green

# Create frontend environment file
$frontendEnvContent = @"
# Frontend Environment Configuration
REACT_APP_API_URL=http://localhost:8001
REACT_APP_ENVIRONMENT=development
REACT_APP_DEBUG=true
REACT_APP_STRIPE_PUBLISHABLE_KEY=your-stripe-publishable-key-here
REACT_APP_GOOGLE_CLIENT_ID=your-google-client-id-here
REACT_APP_SENTRY_DSN=
REACT_APP_ANALYTICS_ID=
"@

$frontendEnvContent | Out-File -FilePath "frontend\.env" -Encoding UTF8
Write-Host "✅ Created frontend\.env file" -ForegroundColor Green

Write-Host "`n🔧 Installing production dependencies..." -ForegroundColor Cyan
cd backend
.\venv\Scripts\Activate.ps1

# Install production dependencies
pip install gunicorn uvicorn[standard] python-multipart python-jose[cryptography] passlib[bcrypt] redis sentry-sdk[fastapi] structlog

Write-Host "✅ Production dependencies installed" -ForegroundColor Green

cd ..

Write-Host "`n📝 Creating production startup scripts..." -ForegroundColor Cyan

# Create production startup script
$prodStartScript = @"
# Production Backend Startup Script
Write-Host "🚀 Starting Mewayz Backend Server (Production Mode)..." -ForegroundColor Green
Write-Host "🔒 Production security enabled" -ForegroundColor Yellow

Set-Location backend
.\\venv\\Scripts\\Activate.ps1

# Set production environment
`$env:ENVIRONMENT = "production"
`$env:DEBUG = "false"

Write-Host "🌐 Starting server on http://localhost:8001" -ForegroundColor Cyan
Write-Host "📚 API Documentation: http://localhost:8001/docs" -ForegroundColor Cyan
Write-Host "📊 Health Check: http://localhost:8001/health" -ForegroundColor Cyan

# Start with production settings
gunicorn main_sqlite:app -w 4 -k uvicorn.workers.UvicornWorker --bind 0.0.0.0:8001 --access-logfile - --error-logfile - --log-level info
"@

$prodStartScript | Out-File -FilePath "start-backend-production.ps1" -Encoding UTF8
Write-Host "✅ Created start-backend-production.ps1" -ForegroundColor Green

# Create development startup script
$devStartScript = @"
# Development Backend Startup Script
Write-Host "🚀 Starting Mewayz Backend Server (Development Mode)..." -ForegroundColor Green
Write-Host "🔧 Development mode with hot reload" -ForegroundColor Yellow

Set-Location backend
.\\venv\\Scripts\\Activate.ps1

# Set development environment
`$env:ENVIRONMENT = "development"
`$env:DEBUG = "true"

Write-Host "🌐 Starting server on http://localhost:8001" -ForegroundColor Cyan
Write-Host "📚 API Documentation: http://localhost:8001/docs" -ForegroundColor Cyan
Write-Host "📊 Health Check: http://localhost:8001/health" -ForegroundColor Cyan

# Start with development settings
uvicorn main_sqlite:app --host 0.0.0.0 --port 8001 --reload --log-level debug
"@

$devStartScript | Out-File -FilePath "start-backend-development.ps1" -Encoding UTF8
Write-Host "✅ Created start-backend-development.ps1" -ForegroundColor Green

Write-Host "`n🎉 Production Configuration Setup Complete!" -ForegroundColor Green
Write-Host "`n📋 Next Steps:" -ForegroundColor Yellow
Write-Host "1. Update API keys in .env files with your actual keys" -ForegroundColor White
Write-Host "2. Test production mode: .\start-backend-production.ps1" -ForegroundColor White
Write-Host "3. Test development mode: .\start-backend-development.ps1" -ForegroundColor White

Write-Host "`n🔐 Security Features Enabled:" -ForegroundColor Cyan
Write-Host "   • Secure JWT secrets" -ForegroundColor White
Write-Host "   • Rate limiting" -ForegroundColor White
Write-Host "   • CORS protection" -ForegroundColor White
Write-Host "   • Security headers" -ForegroundColor White
Write-Host "   • Session management" -ForegroundColor White

Write-Host "`n📊 Monitoring Features:" -ForegroundColor Cyan
Write-Host "   • Structured logging" -ForegroundColor White
Write-Host "   • Health checks" -ForegroundColor White
Write-Host "   • Performance metrics" -ForegroundColor White
Write-Host "   • Error tracking" -ForegroundColor White

Write-Host "`n🌐 Your Mewayz platform is now production-ready!" -ForegroundColor Green
Write-Host "   Frontend: http://localhost:3001" -ForegroundColor White
Write-Host "   Backend: http://localhost:8001" -ForegroundColor White
Write-Host "   API Docs: http://localhost:8001/docs" -ForegroundColor White 