# 🌐 Mewayz v2 - Public IP Docker Setup

## 🚀 Quick Start (Accessible from Anywhere)

This Docker setup creates a single container with a web-based setup wizard accessible via public IP and port.

### **Step 1: Build and Run**

```bash
# Clone the repository
git clone https://github.com/ins72/mewayz_9913
cd mewayz_9913

# Build and run the container
docker-compose -f docker-compose.setup.yml up -d --build
```

### **Step 2: Access Setup Wizard**

Once the container is running, access the setup wizard at:

**Setup Wizard**: `http://YOUR_SERVER_IP:8080`
**Application**: `http://YOUR_SERVER_IP:80` (after setup)

Replace `YOUR_SERVER_IP` with your actual server's public IP address.

## 🎯 **What You Get**

### **Setup Wizard Features:**
- 📋 **Step 1**: Application Configuration (Name, URL, Environment)
- 🗄️ **Step 2**: Database Setup with connection testing
- 👤 **Step 3**: Admin user creation
- 📧 **Step 4**: Email configuration (optional)
- ✅ **Step 5**: Review and automatic installation

### **Accessible Ports:**
- **Port 8080**: Setup Wizard (public access)
- **Port 80**: Main Application (after setup)

### **Container Features:**
- 🐘 **PHP 8.2** with all required extensions
- 🌐 **Nginx** web server
- ⚙️ **Supervisor** process management
- 📦 **Node.js & NPM** for frontend assets
- 🎨 **Beautiful setup wizard** with step-by-step guidance

## 🔧 **Usage Instructions**

### **1. Initial Setup**
```bash
# Build and start container
docker-compose -f docker-compose.setup.yml up -d --build

# Check container status
docker-compose -f docker-compose.setup.yml ps

# View logs
docker-compose -f docker-compose.setup.yml logs -f
```

### **2. Access Setup Wizard**
1. Open browser to `http://YOUR_PUBLIC_IP:8080`
2. Follow the 5-step wizard:
   - Configure application settings
   - Set up database connection
   - Create admin account
   - Configure email (optional)
   - Review and install

### **3. After Setup**
- Main application: `http://YOUR_PUBLIC_IP:80`
- Admin login with credentials you created
- Setup wizard will automatically redirect after installation

## 🌍 **Public Access Configuration**

### **Firewall Rules**
Make sure these ports are open on your server:
```bash
# For Ubuntu/Debian
sudo ufw allow 8080
sudo ufw allow 80

# For CentOS/RHEL
sudo firewall-cmd --permanent --add-port=8080/tcp
sudo firewall-cmd --permanent --add-port=80/tcp
sudo firewall-cmd --reload
```

### **Cloud Provider Setup**
- **AWS**: Open ports 80 and 8080 in Security Group
- **DigitalOcean**: Allow ports in Firewall settings
- **Azure**: Configure Network Security Group rules
- **Google Cloud**: Add firewall rules for ports 80 and 8080

## 🔒 **Security Notes**

### **Setup Wizard Security**
- Setup wizard is only accessible until `.env` file is created
- After successful setup, wizard automatically disables
- Use strong passwords for database and admin account

### **Production Recommendations**
1. **After Setup**: Close port 8080 in firewall
2. **SSL/HTTPS**: Set up SSL certificates for production
3. **Database**: Use external database for production
4. **Backups**: Set up regular backups of `/var/www/html/storage`

## 🎛️ **Container Management**

```bash
# Start container
docker-compose -f docker-compose.setup.yml up -d

# Stop container
docker-compose -f docker-compose.setup.yml down

# Restart container
docker-compose -f docker-compose.setup.yml restart

# View logs
docker-compose -f docker-compose.setup.yml logs -f

# Execute commands in container
docker-compose -f docker-compose.setup.yml exec mewayz-setup bash

# Remove everything (including data)
docker-compose -f docker-compose.setup.yml down -v
```

## 📊 **Monitoring & Debugging**

### **Check Container Status**
```bash
# Container status
docker-compose -f docker-compose.setup.yml ps

# Resource usage
docker stats mewayz-setup
```

### **Access Container Logs**
```bash
# All logs
docker-compose -f docker-compose.setup.yml logs -f

# Nginx logs
docker-compose -f docker-compose.setup.yml exec mewayz-setup tail -f /var/log/nginx/access.log

# PHP logs  
docker-compose -f docker-compose.setup.yml exec mewayz-setup tail -f /var/log/nginx/error.log

# Installation logs (during setup)
docker-compose -f docker-compose.setup.yml exec mewayz-setup tail -f /var/log/install.log
```

### **Troubleshooting**
```bash
# Enter container shell
docker-compose -f docker-compose.setup.yml exec mewayz-setup bash

# Check running processes
docker-compose -f docker-compose.setup.yml exec mewayz-setup ps aux

# Test nginx configuration
docker-compose -f docker-compose.setup.yml exec mewayz-setup nginx -t

# Check PHP configuration
docker-compose -f docker-compose.setup.yml exec mewayz-setup php -v
```

## 🎯 **Example Access URLs**

If your server IP is `203.0.113.10`:
- **Setup Wizard**: `http://203.0.113.10:8080`
- **Application**: `http://203.0.113.10:80`

## 📝 **Setup Wizard Screenshots**

The setup wizard includes:
1. **Welcome Screen** with progress indicators
2. **Application Config** with URL and environment settings
3. **Database Setup** with connection testing
4. **Admin Creation** with secure password requirements
5. **Email Config** with SMTP testing (optional)
6. **Final Review** with installation progress

## 🌟 **After Installation**

Once setup is complete:
- Access your Mewayz v2 platform at `http://YOUR_IP:80`
- Login with your admin credentials
- Setup wizard will be automatically disabled
- All data persists in Docker volumes

## 🆘 **Need Help?**

1. **Check logs**: `docker-compose -f docker-compose.setup.yml logs -f`
2. **Verify ports**: `netstat -tlnp | grep -E '80|8080'`
3. **Test connectivity**: `curl http://localhost:8080`
4. **Container shell**: `docker-compose -f docker-compose.setup.yml exec mewayz-setup bash`

---

**🚀 Ready to launch your Mewayz v2 platform accessible from anywhere!**