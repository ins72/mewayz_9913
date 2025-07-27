# 🚀 Mewayz Platform - Quick Start Guide

## ✅ Installation Complete!

Your Mewayz platform has been successfully installed with:
- ✅ Python 3.13.5 (Backend)
- ✅ Node.js v22.14.0 (Frontend)
- ✅ All dependencies installed
- ✅ Virtual environment configured
- ✅ Startup scripts created

## 🚀 Starting the Application

### Option 1: Development Mode (Recommended for testing)

#### Start Backend Only:
```powershell
.\start-backend.ps1
```

#### Start Frontend Only:
```powershell
.\start-frontend.ps1
```

#### Start Both Services:
Open two PowerShell windows and run:
```powershell
# Terminal 1 - Backend
.\start-backend.ps1

# Terminal 2 - Frontend  
.\start-frontend.ps1
```

### Option 2: Docker Mode (Full stack)
```powershell
.\start-docker.ps1
```

## 🌐 Access URLs

Once started, access your application at:
- **Frontend:** http://localhost:3000
- **Backend API:** http://localhost:8001
- **API Documentation:** http://localhost:8001/docs
- **Health Check:** http://localhost:8001/health

## ⚙️ Configuration

### Database Setup Options:

#### Option 1: Local Installation
1. Install MongoDB: https://docs.mongodb.com/manual/installation/
2. Install Redis: https://redis.io/download
3. Start services and update `backend\.env` with connection details

#### Option 2: Docker (Easiest)
```powershell
# Install Docker Desktop from https://www.docker.com/products/docker-desktop
# Then run:
.\start-docker.ps1
```

#### Option 3: Cloud Services
- **MongoDB Atlas:** https://www.mongodb.com/atlas
- **Redis Cloud:** https://redis.com/try-free/
- Update `backend\.env` with cloud connection strings

### API Keys Setup:
Edit `backend\.env` and `frontend\.env` to add your API keys:
- Google OAuth credentials
- Stripe payment keys
- Email service credentials
- AWS S3 credentials (for file storage)

## 🔧 Troubleshooting

### Common Issues:

#### Port Already in Use:
```powershell
# Check what's using the port
netstat -ano | findstr :8001
netstat -ano | findstr :3000

# Kill the process (replace PID with actual process ID)
taskkill /PID <PID> /F
```

#### Python Virtual Environment Issues:
```powershell
cd backend
python -m venv venv
.\venv\Scripts\Activate.ps1
pip install -r requirements.txt
```

#### Node.js Dependencies Issues:
```powershell
cd frontend
npm install
```

#### Database Connection Issues:
- Ensure MongoDB and Redis are running
- Check connection strings in `backend\.env`
- For Docker: `docker-compose logs db redis`

## 📚 Next Steps

1. **Set up your database** (MongoDB + Redis)
2. **Configure API keys** in environment files
3. **Test the application** by accessing the URLs
4. **Review the API documentation** at http://localhost:8001/docs
5. **Read the full deployment guide** in `DEPLOYMENT_INSTALLATION_GUIDE.md`

## 🆘 Need Help?

- **Documentation:** `docs/` directory
- **API Docs:** http://localhost:8001/docs
- **Deployment Guide:** `DEPLOYMENT_INSTALLATION_GUIDE.md`
- **Architecture Overview:** `docs/architecture/SYSTEMS_OVERVIEW.md`

## 🎉 Success!

Your Mewayz platform is now ready for development and testing!

**Happy building! 🚀** 