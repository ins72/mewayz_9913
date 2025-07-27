# Mewayz Platform - Simple Database Setup
# This script helps you set up MongoDB and Redis for the Mewayz platform

Write-Host "🗄️ Mewayz Platform - Database Setup" -ForegroundColor Blue
Write-Host "=====================================" -ForegroundColor Blue
Write-Host ""

Write-Host "Choose your database setup option:" -ForegroundColor Cyan
Write-Host "1. Use MongoDB Atlas (Cloud - Free tier)" -ForegroundColor White
Write-Host "2. Use Redis Cloud (Cloud - Free tier)" -ForegroundColor White
Write-Host "3. Install MongoDB locally" -ForegroundColor White
Write-Host "4. Install Redis locally" -ForegroundColor White
Write-Host "5. Use Docker for both (requires Docker Desktop)" -ForegroundColor White
Write-Host "6. Skip database setup for now" -ForegroundColor White

$choice = Read-Host "Enter your choice (1-6)"

switch ($choice) {
    "1" {
        Write-Host "🌐 MongoDB Atlas Setup:" -ForegroundColor Green
        Write-Host "1. Go to https://www.mongodb.com/atlas"
        Write-Host "2. Create a free account"
        Write-Host "3. Create a new cluster (free tier)"
        Write-Host "4. Get your connection string"
        Write-Host "5. Update backend\.env with your MongoDB connection string"
        Write-Host ""
        Write-Host "Example connection string:"
        Write-Host "mongodb+srv://username:password@cluster.mongodb.net/mewayz_production"
    }
    "2" {
        Write-Host "🔴 Redis Cloud Setup:" -ForegroundColor Green
        Write-Host "1. Go to https://redis.com/try-free/"
        Write-Host "2. Create a free account"
        Write-Host "3. Create a new database (free tier)"
        Write-Host "4. Get your connection string"
        Write-Host "5. Update backend\.env with your Redis connection string"
        Write-Host ""
        Write-Host "Example connection string:"
        Write-Host "redis://username:password@host:port"
    }
    "3" {
        Write-Host "🍃 Local MongoDB Installation:" -ForegroundColor Green
        Write-Host "1. Download MongoDB Community Server from:"
        Write-Host "   https://www.mongodb.com/try/download/community"
        Write-Host "2. Install with default settings"
        Write-Host "3. Start MongoDB service"
        Write-Host "4. Your connection string will be:"
        Write-Host "   mongodb://localhost:27017/mewayz_production"
    }
    "4" {
        Write-Host "🔴 Local Redis Installation:" -ForegroundColor Green
        Write-Host "1. Download Redis for Windows from:"
        Write-Host "   https://github.com/microsoftarchive/redis/releases"
        Write-Host "2. Install Redis"
        Write-Host "3. Start Redis service"
        Write-Host "4. Your connection string will be:"
        Write-Host "   redis://localhost:6379"
    }
    "5" {
        Write-Host "🐳 Docker Setup:" -ForegroundColor Green
        Write-Host "1. Install Docker Desktop from:"
        Write-Host "   https://www.docker.com/products/docker-desktop"
        Write-Host "2. Start Docker Desktop"
        Write-Host "3. Run the following command:"
        Write-Host "   docker-compose up -d db redis"
        Write-Host "4. This will start MongoDB and Redis containers"
    }
    "6" {
        Write-Host "⏭️ Skipping database setup" -ForegroundColor Yellow
        Write-Host "You can set up the database later and update backend\.env"
    }
    default {
        Write-Host "❌ Invalid choice" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "📝 Next Steps:" -ForegroundColor Cyan
Write-Host "1. Set up your chosen database option"
Write-Host "2. Update backend\.env with your database connection strings"
Write-Host "3. Start the application with: .\start-backend.ps1"
Write-Host ""

Write-Host "🔧 Quick Environment File Update:" -ForegroundColor Yellow
Write-Host "Edit backend\.env and update these lines:"
Write-Host "MONGO_URL=your-mongodb-connection-string"
Write-Host "REDIS_URL=your-redis-connection-string"
Write-Host ""

Write-Host "✅ Database setup guide completed!" -ForegroundColor Green 