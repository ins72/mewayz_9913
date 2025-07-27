# Cloud Database Setup Script for Mewayz Platform
Write-Host "🚀 Setting up Cloud Databases for Mewayz Platform" -ForegroundColor Green
Write-Host "==================================================" -ForegroundColor Cyan

Write-Host "`n📋 This script will help you set up cloud databases:" -ForegroundColor Yellow
Write-Host "   • MongoDB Atlas (Free tier available)" -ForegroundColor White
Write-Host "   • Redis Cloud (Free tier available)" -ForegroundColor White

Write-Host "`n🔗 Step 1: MongoDB Atlas Setup" -ForegroundColor Cyan
Write-Host "1. Go to: https://www.mongodb.com/atlas" -ForegroundColor White
Write-Host "2. Create a free account" -ForegroundColor White
Write-Host "3. Create a new cluster (M0 Free tier)" -ForegroundColor White
Write-Host "4. Create a database user (remember username/password)" -ForegroundColor White
Write-Host "5. Add your IP address to IP Access List (or 0.0.0.0/0 for all)" -ForegroundColor White
Write-Host "6. Get your connection string" -ForegroundColor White

Write-Host "`n🔗 Step 2: Redis Cloud Setup" -ForegroundColor Cyan
Write-Host "1. Go to: https://redis.com/try-free/" -ForegroundColor White
Write-Host "2. Create a free account" -ForegroundColor White
Write-Host "3. Create a new database (Free tier)" -ForegroundColor White
Write-Host "4. Get your connection string" -ForegroundColor White

Write-Host "`n📝 Step 3: Update Environment Variables" -ForegroundColor Cyan
Write-Host "Once you have your connection strings, update backend\.env:" -ForegroundColor White

$envContent = @"
# Database Configuration
MONGO_URL=mongodb+srv://username:password@cluster.mongodb.net/mewayz?retryWrites=true&w=majority
REDIS_URL=redis://username:password@host:port

# Application Configuration
ENVIRONMENT=production
DEBUG=false
JWT_SECRET=your-super-secret-jwt-key-here
ENCRYPTION_KEY=your-32-character-encryption-key

# API Keys (Add your actual keys)
OPENAI_API_KEY=your-openai-api-key
STRIPE_SECRET_KEY=your-stripe-secret-key
STRIPE_PUBLISHABLE_KEY=your-stripe-publishable-key
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret

# Email Configuration
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=your-email@gmail.com
SMTP_PASSWORD=your-app-password
"@

Write-Host "`n📄 Example .env content:" -ForegroundColor Yellow
Write-Host $envContent -ForegroundColor Gray

Write-Host "`n✅ Ready to proceed?" -ForegroundColor Green
Write-Host "After setting up your cloud databases, run:" -ForegroundColor White
Write-Host "  .\start-backend.ps1" -ForegroundColor Cyan
Write-Host "  .\start-frontend.ps1" -ForegroundColor Cyan

Write-Host "`n🌐 Your Mewayz platform will be available at:" -ForegroundColor Yellow
Write-Host "  Frontend: http://localhost:3001" -ForegroundColor White
Write-Host "  Backend: http://localhost:8001" -ForegroundColor White
Write-Host "  API Docs: http://localhost:8001/docs" -ForegroundColor White 