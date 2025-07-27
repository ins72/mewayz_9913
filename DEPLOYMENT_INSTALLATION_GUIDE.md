# Mewayz Platform - Complete Deployment Installation Guide

**Version:** 4.0.0  
**Date:** January 2025  
**Status:** ✅ **Production Ready**

## 🚀 **Quick Start Options**

### **Option 1: Automated Installation (Recommended)**

#### **Windows (PowerShell)**
```powershell
# Run as Administrator
Set-ExecutionPolicy Bypass -Scope Process -Force
.\deploy-install-windows.ps1
```

#### **Linux/macOS (Bash)**
```bash
# Make executable and run
chmod +x deploy-install.sh
./deploy-install.sh
```

### **Option 2: Docker-Only Installation**
```bash
# Install Docker and run containers
docker-compose up -d
```

### **Option 3: Manual Installation**
Follow the step-by-step guide below.

---

## 📋 **System Requirements**

### **Minimum Requirements**
- **OS:** Windows 10/11, Ubuntu 20.04+, macOS 10.15+, CentOS 8+
- **CPU:** 4 cores (2 GHz+)
- **RAM:** 8GB
- **Storage:** 50GB SSD
- **Network:** Stable internet connection

### **Recommended Requirements**
- **OS:** Ubuntu 22.04 LTS, Windows 11, macOS 12+
- **CPU:** 8 cores (3 GHz+)
- **RAM:** 16GB
- **Storage:** 100GB NVMe SSD
- **Network:** High-speed internet (100 Mbps+)

---

## 🔧 **Software Dependencies**

### **Core Dependencies**
- **Python:** 3.11+ (Backend)
- **Node.js:** 18+ (Frontend)
- **MongoDB:** 6.0+ (Database)
- **Redis:** 7.0+ (Cache/Sessions)
- **Docker:** 20.10+ (Containerization)

### **Optional Dependencies**
- **Nginx:** 1.20+ (Reverse Proxy)
- **PM2:** Latest (Process Manager)
- **Certbot:** Latest (SSL Certificates)

---

## 🛠️ **Installation Methods**

### **Method 1: Automated Script Installation**

#### **Windows Installation**
1. **Download the script:**
   ```powershell
   # Clone or download the project
   git clone <repository-url>
   cd mewayz_9913
   ```

2. **Run the installation script:**
   ```powershell
   # Right-click PowerShell and "Run as Administrator"
   Set-ExecutionPolicy Bypass -Scope Process -Force
   .\deploy-install-windows.ps1
   ```

3. **Optional parameters:**
   ```powershell
   # Skip Docker installation
   .\deploy-install-windows.ps1 -SkipDocker
   
   # Skip MongoDB installation (use external)
   .\deploy-install-windows.ps1 -SkipMongoDB
   
   # Skip Redis installation (use external)
   .\deploy-install-windows.ps1 -SkipRedis
   ```

#### **Linux/macOS Installation**
1. **Download the script:**
   ```bash
   # Clone or download the project
   git clone <repository-url>
   cd mewayz_9913
   ```

2. **Run the installation script:**
   ```bash
   # Make executable
   chmod +x deploy-install.sh
   
   # Run installation
   ./deploy-install.sh
   ```

### **Method 2: Docker Installation**

#### **Prerequisites**
- Docker Desktop (Windows/macOS) or Docker Engine (Linux)
- Docker Compose

#### **Quick Docker Setup**
```bash
# Clone the repository
git clone <repository-url>
cd mewayz_9913

# Start all services
docker-compose up -d

# Check status
docker-compose ps

# View logs
docker-compose logs -f
```

#### **Docker Services**
- **Frontend:** React app on port 3000
- **Backend:** FastAPI on port 8001
- **Database:** MongoDB on port 27017
- **Cache:** Redis on port 6379
- **Web Server:** Nginx on port 80/443

### **Method 3: Manual Installation**

#### **Step 1: Install System Dependencies**

**Ubuntu/Debian:**
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Python 3.11
sudo add-apt-repository ppa:deadsnakes/ppa -y
sudo apt install python3.11 python3.11-venv python3.11-dev python3.11-pip

# Install Node.js 18
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install nodejs

# Install MongoDB
wget -qO - https://www.mongodb.org/static/pgp/server-6.0.asc | sudo apt-key add -
echo "deb [ arch=amd64,arm64 ] https://repo.mongodb.org/apt/ubuntu focal/mongodb-org/6.0 multiverse" | sudo tee /etc/apt/sources.list.d/mongodb-org-6.0.list
sudo apt update
sudo apt install mongodb-org

# Install Redis
sudo apt install redis-server

# Start services
sudo systemctl enable mongod redis-server
sudo systemctl start mongod redis-server
```

**CentOS/RHEL:**
```bash
# Install Python 3.11
sudo yum groupinstall "Development Tools"
cd /tmp
wget https://www.python.org/ftp/python/3.11.7/Python-3.11.7.tgz
tar -xzf Python-3.11.7.tgz
cd Python-3.11.7
./configure --enable-optimizations
make -j$(nproc)
sudo make altinstall

# Install Node.js 18
curl -fsSL https://rpm.nodesource.com/setup_18.x | sudo bash -
sudo yum install nodejs

# Install MongoDB
echo '[mongodb-org-6.0]' | sudo tee /etc/yum.repos.d/mongodb-org-6.0.repo
echo 'name=MongoDB Repository' | sudo tee -a /etc/yum.repos.d/mongodb-org-6.0.repo
echo 'baseurl=https://repo.mongodb.org/yum/redhat/$releasever/mongodb-org/6.0/x86_64/' | sudo tee -a /etc/yum.repos.d/mongodb-org-6.0.repo
echo 'gpgcheck=1' | sudo tee -a /etc/yum.repos.d/mongodb-org-6.0.repo
echo 'enabled=1' | sudo tee -a /etc/yum.repos.d/mongodb-org-6.0.repo
echo 'gpgkey=https://www.mongodb.org/static/pgp/server-6.0.asc' | sudo tee -a /etc/yum.repos.d/mongodb-org-6.0.repo
sudo yum install mongodb-org

# Install Redis
sudo yum install redis

# Start services
sudo systemctl enable mongod redis
sudo systemctl start mongod redis
```

**Windows:**
```powershell
# Install Chocolatey (run as Administrator)
Set-ExecutionPolicy Bypass -Scope Process -Force
[System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072
iex ((New-Object System.Net.WebClient).DownloadString('https://community.chocolatey.org/install.ps1'))

# Install dependencies
choco install python311 nodejs-lts mongodb redis-64 -y
```

**macOS:**
```bash
# Install Homebrew
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Install dependencies
brew install python@3.11 node@18 mongodb/brew/mongodb-community redis

# Start services
brew services start mongodb/brew/mongodb-community
brew services start redis
```

#### **Step 2: Setup Backend**

```bash
# Navigate to backend directory
cd backend

# Create virtual environment
python3 -m venv venv

# Activate virtual environment
# Linux/macOS:
source venv/bin/activate
# Windows:
.\venv\Scripts\Activate.ps1

# Install dependencies
pip install --upgrade pip
pip install -r requirements.txt

# Create environment file
cp .env.example .env
# Edit .env with your configuration
```

#### **Step 3: Setup Frontend**

```bash
# Navigate to frontend directory
cd frontend

# Install dependencies
npm install

# Create environment file
cp .env.example .env
# Edit .env with your configuration

# Build for production
npm run build
```

#### **Step 4: Configure Environment**

**Backend (.env):**
```bash
# Database Configuration
MONGO_URL=mongodb://localhost:27017/mewayz_production
REDIS_URL=redis://localhost:6379

# Security
JWT_SECRET=your-super-secure-jwt-secret-key
ENCRYPTION_KEY=your-32-byte-encryption-key

# Application Settings
ENVIRONMENT=production
DEBUG=false
CORS_ORIGINS=http://localhost:3000,https://localhost:3000

# API Keys (Update these with your actual keys)
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
STRIPE_SECRET_KEY=your-stripe-secret-key
STRIPE_WEBHOOK_SECRET=your-stripe-webhook-secret

# Email Configuration
SMTP_SERVER=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your-email@gmail.com
SMTP_PASSWORD=your-app-password

# File Storage
AWS_ACCESS_KEY_ID=your-aws-access-key
AWS_SECRET_ACCESS_KEY=your-aws-secret-key
AWS_S3_BUCKET=your-s3-bucket
AWS_REGION=us-east-1
```

**Frontend (.env):**
```bash
REACT_APP_API_URL=http://localhost:8001
REACT_APP_GOOGLE_CLIENT_ID=your-google-client-id
REACT_APP_STRIPE_PUBLISHABLE_KEY=your-stripe-publishable-key
REACT_APP_ENVIRONMENT=production
```

---

## 🚀 **Starting the Application**

### **Development Mode**

#### **Backend Only:**
```bash
cd backend
source venv/bin/activate  # Linux/macOS
# .\venv\Scripts\Activate.ps1  # Windows
uvicorn main:app --host 0.0.0.0 --port 8001 --reload
```

#### **Frontend Only:**
```bash
cd frontend
npm start
```

#### **Both Services:**
```bash
# Terminal 1 - Backend
cd backend && source venv/bin/activate && uvicorn main:app --host 0.0.0.0 --port 8001 --reload

# Terminal 2 - Frontend
cd frontend && npm start
```

### **Production Mode**

#### **Using PM2 (Recommended):**
```bash
# Install PM2
npm install -g pm2

# Start backend
cd backend
pm2 start "uvicorn main:app --host 0.0.0.0 --port 8001" --name "mewayz-backend"

# Start frontend
cd frontend
pm2 start "npm start" --name "mewayz-frontend"

# Save PM2 configuration
pm2 save
pm2 startup
```

#### **Using Systemd (Linux):**
```bash
# Create backend service
sudo tee /etc/systemd/system/mewayz-backend.service << EOF
[Unit]
Description=Mewayz Backend
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/path/to/mewayz_9913/backend
Environment=PATH=/path/to/mewayz_9913/backend/venv/bin
ExecStart=/path/to/mewayz_9913/backend/venv/bin/uvicorn main:app --host 0.0.0.0 --port 8001
Restart=always

[Install]
WantedBy=multi-user.target
EOF

# Enable and start service
sudo systemctl enable mewayz-backend
sudo systemctl start mewayz-backend
```

#### **Using Docker:**
```bash
# Start all services
docker-compose up -d

# Check status
docker-compose ps

# View logs
docker-compose logs -f
```

---

## 🌐 **Access URLs**

### **Development:**
- **Frontend:** http://localhost:3000
- **Backend API:** http://localhost:8001
- **API Documentation:** http://localhost:8001/docs
- **Health Check:** http://localhost:8001/health

### **Production:**
- **Frontend:** https://yourdomain.com
- **Backend API:** https://api.yourdomain.com
- **API Documentation:** https://api.yourdomain.com/docs

---

## 🔒 **Security Configuration**

### **SSL/TLS Setup**

#### **Using Let's Encrypt (Free):**
```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Get SSL certificate
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Auto-renewal
sudo crontab -e
# Add: 0 12 * * * /usr/bin/certbot renew --quiet
```

#### **Using Nginx Reverse Proxy:**
```nginx
# /etc/nginx/sites-available/mewayz
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;
    
    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
    
    # Frontend
    location / {
        proxy_pass http://localhost:3000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
    
    # Backend API
    location /api/ {
        proxy_pass http://localhost:8001/;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

### **Firewall Configuration**

#### **Ubuntu/Debian:**
```bash
# Install UFW
sudo apt install ufw

# Configure firewall
sudo ufw allow ssh
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw allow 3000/tcp
sudo ufw allow 8001/tcp

# Enable firewall
sudo ufw enable
```

#### **CentOS/RHEL:**
```bash
# Configure firewalld
sudo firewall-cmd --permanent --add-service=ssh
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --permanent --add-service=https
sudo firewall-cmd --permanent --add-port=3000/tcp
sudo firewall-cmd --permanent --add-port=8001/tcp

# Reload firewall
sudo firewall-cmd --reload
```

---

## 📊 **Monitoring & Maintenance**

### **Health Checks**
```bash
# Backend health
curl http://localhost:8001/health

# Database connection
curl http://localhost:8001/api/health

# System metrics
curl http://localhost:8001/metrics
```

### **Log Management**
```bash
# View application logs
tail -f /var/log/mewayz/application.log

# View system logs
journalctl -u mewayz-backend -f

# Docker logs
docker-compose logs -f
```

### **Backup Strategy**
```bash
# Database backup
mongodump --db mewayz_production --out /backup/$(date +%Y%m%d)

# Application backup
tar -czf /backup/mewayz-app-$(date +%Y%m%d).tar.gz /path/to/mewayz_9913

# Automated backup script
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backup/$DATE"

mkdir -p $BACKUP_DIR

# Database backup
mongodump --db mewayz_production --out $BACKUP_DIR/db

# Application backup
tar -czf $BACKUP_DIR/app.tar.gz /path/to/mewayz_9913

# Clean old backups (keep 30 days)
find /backup -type d -mtime +30 -exec rm -rf {} \;
```

---

## 🆘 **Troubleshooting**

### **Common Issues**

#### **Port Already in Use:**
```bash
# Check what's using the port
sudo netstat -tulpn | grep :8001

# Kill the process
sudo kill -9 <PID>
```

#### **Database Connection Issues:**
```bash
# Check MongoDB status
sudo systemctl status mongod

# Check MongoDB logs
sudo journalctl -u mongod -f

# Test connection
mongo --eval "db.runCommand('ping')"
```

#### **Permission Issues:**
```bash
# Fix file permissions
sudo chown -R $USER:$USER /path/to/mewayz_9913
sudo chmod -R 755 /path/to/mewayz_9913
```

#### **Docker Issues:**
```bash
# Clean up Docker
docker system prune -a

# Rebuild containers
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

### **Performance Optimization**

#### **Backend Optimization:**
```bash
# Use Gunicorn for production
pip install gunicorn
gunicorn main:app -w 4 -k uvicorn.workers.UvicornWorker -b 0.0.0.0:8001

# Enable caching
pip install redis
```

#### **Frontend Optimization:**
```bash
# Build optimized version
npm run build

# Serve static files with Nginx
# Configure Nginx to serve /build directory
```

---

## 📚 **Additional Resources**

### **Documentation**
- **API Documentation:** http://localhost:8001/docs
- **Deployment Guide:** `docs/DEPLOYMENT_GUIDE_v3.0.md`
- **Architecture Overview:** `docs/architecture/SYSTEMS_OVERVIEW.md`

### **Support**
- **Email:** support@mewayz.com
- **Documentation:** https://docs.mewayz.com
- **GitHub Issues:** https://github.com/mewayz/platform/issues

### **Community**
- **Discord:** https://discord.gg/mewayz
- **Forum:** https://forum.mewayz.com
- **Blog:** https://blog.mewayz.com

---

## ✅ **Installation Checklist**

- [ ] System requirements met
- [ ] Python 3.11+ installed
- [ ] Node.js 18+ installed
- [ ] MongoDB installed and running
- [ ] Redis installed and running
- [ ] Backend dependencies installed
- [ ] Frontend dependencies installed
- [ ] Environment files configured
- [ ] API keys updated
- [ ] Database initialized
- [ ] Frontend built
- [ ] Services started
- [ ] Health checks passing
- [ ] SSL certificates configured
- [ ] Firewall configured
- [ ] Monitoring setup
- [ ] Backup strategy implemented

---

**🎉 Congratulations! Your Mewayz platform is now ready for production deployment!** 