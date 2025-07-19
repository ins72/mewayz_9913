# Mewayz Platform v3.0.0 - Production Deployment Guide

**Version:** 3.0.0  
**Date:** July 20, 2025  
**Status:** ✅ **Production Ready**

## 🚀 **Quick Start Production Deployment**

### **System Requirements**
- **Python:** 3.11+ (Backend)
- **Node.js:** 18+ (Frontend)
- **MongoDB:** 6.0+
- **Redis:** 7.0+
- **CPU:** 4+ cores recommended
- **Memory:** 8GB+ recommended
- **Storage:** 50GB+ SSD recommended

### **Production Environment Setup**

#### **1. Backend Deployment (FastAPI)**

```bash
# Clone and setup backend
git clone <repository-url>
cd mewayz-platform/backend

# Create virtual environment
python -m venv venv
source venv/bin/activate  # Linux/Mac
# venv\Scripts\activate     # Windows

# Install dependencies
pip install -r requirements.txt

# Production environment configuration
cp .env.example .env
```

**Production .env Configuration:**
```bash
# Database
MONGO_URL=mongodb://your-mongo-cluster:27017/mewayz_production
REDIS_URL=redis://your-redis-cluster:6379

# Security
JWT_SECRET=your-super-secure-jwt-secret-key
ENCRYPTION_KEY=your-32-byte-encryption-key

# OAuth Configuration
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
APPLE_CLIENT_ID=your-apple-client-id
APPLE_CLIENT_SECRET=your-apple-client-secret

# Payment Processing
STRIPE_SECRET_KEY=sk_live_your-stripe-secret-key
STRIPE_WEBHOOK_SECRET=whsec_your-webhook-secret
PAYPAL_CLIENT_ID=your-paypal-client-id
PAYPAL_CLIENT_SECRET=your-paypal-client-secret

# Email Services
SMTP_SERVER=your-smtp-server.com
SMTP_PORT=587
SMTP_USERNAME=your-smtp-username
SMTP_PASSWORD=your-smtp-password

# File Storage (AWS S3)
AWS_ACCESS_KEY_ID=your-aws-access-key
AWS_SECRET_ACCESS_KEY=your-aws-secret-key
AWS_S3_BUCKET=your-s3-bucket-name
AWS_REGION=us-east-1

# Application Settings
ENVIRONMENT=production
DEBUG=false
CORS_ORIGINS=https://yourdomain.com,https://www.yourdomain.com
```

**Start Production Backend:**
```bash
# Using Gunicorn (recommended)
gunicorn main:app -w 4 -k uvicorn.workers.UvicornWorker -b 0.0.0.0:8001

# Or using Uvicorn directly
uvicorn main:app --host 0.0.0.0 --port 8001 --workers 4
```

#### **2. Frontend Deployment (React)**

```bash
# Setup frontend
cd ../frontend
yarn install

# Production environment configuration
cp .env.example .env
```

**Production .env Configuration:**
```bash
# Backend API
REACT_APP_BACKEND_URL=https://api.yourdomain.com

# OAuth Configuration
REACT_APP_GOOGLE_CLIENT_ID=your-google-client-id
REACT_APP_APPLE_CLIENT_ID=your-apple-client-id

# Payment Processing
REACT_APP_STRIPE_PUBLISHABLE_KEY=pk_live_your-stripe-publishable-key

# Application Settings
REACT_APP_ENVIRONMENT=production
REACT_APP_APP_NAME=Mewayz
REACT_APP_APP_URL=https://yourdomain.com
```

**Build and Deploy Frontend:**
```bash
# Build production bundle
yarn build

# Serve with production web server
# Option 1: Nginx (recommended)
sudo cp -r build/* /var/www/html/

# Option 2: Serve with Node.js
yarn global add serve
serve -s build -l 3000
```

### **3. Database Setup**

#### **MongoDB Configuration**
```bash
# Create production database and collections
mongosh "mongodb://your-mongo-cluster:27017/mewayz_production"

# Create indexes for performance
db.users.createIndex({ "email": 1 })
db.workspaces.createIndex({ "owner_id": 1 })
db.posts.createIndex({ "workspace_id": 1, "created_at": -1 })
db.analytics.createIndex({ "workspace_id": 1, "date": -1 })

# Create admin user (optional)
db.users.insertOne({
  "email": "admin@yourdomain.com",
  "password": "hashed-password",
  "role": "admin",
  "is_verified": true,
  "created_at": new Date()
})
```

#### **Redis Configuration**
```bash
# Redis performance configuration
echo "maxmemory 2gb" >> /etc/redis/redis.conf
echo "maxmemory-policy allkeys-lru" >> /etc/redis/redis.conf
systemctl restart redis
```

## 🐳 **Docker Deployment**

### **Docker Compose Production Setup**

```yaml
# docker-compose.prod.yml
version: '3.8'

services:
  # MongoDB
  mongodb:
    image: mongo:6.0
    container_name: mewayz-mongo
    restart: unless-stopped
    environment:
      MONGO_INITDB_ROOT_USERNAME: admin
      MONGO_INITDB_ROOT_PASSWORD: your-mongo-password
      MONGO_INITDB_DATABASE: mewayz_production
    volumes:
      - mongodb_data:/data/db
      - ./mongo-init.js:/docker-entrypoint-initdb.d/mongo-init.js
    ports:
      - "27017:27017"
    networks:
      - mewayz-network

  # Redis
  redis:
    image: redis:7.0-alpine
    container_name: mewayz-redis
    restart: unless-stopped
    command: redis-server --appendonly yes --maxmemory 2gb --maxmemory-policy allkeys-lru
    volumes:
      - redis_data:/data
    ports:
      - "6379:6379"
    networks:
      - mewayz-network

  # Backend API
  backend:
    build:
      context: ./backend
      dockerfile: Dockerfile.prod
    container_name: mewayz-backend
    restart: unless-stopped
    environment:
      - MONGO_URL=mongodb://admin:your-mongo-password@mongodb:27017/mewayz_production?authSource=admin
      - REDIS_URL=redis://redis:6379
    env_file:
      - ./backend/.env
    ports:
      - "8001:8001"
    depends_on:
      - mongodb
      - redis
    networks:
      - mewayz-network

  # Frontend
  frontend:
    build:
      context: ./frontend
      dockerfile: Dockerfile.prod
    container_name: mewayz-frontend
    restart: unless-stopped
    ports:
      - "80:80"
      - "443:443"
    depends_on:
      - backend
    networks:
      - mewayz-network
    volumes:
      - ./ssl:/etc/ssl/certs

volumes:
  mongodb_data:
  redis_data:

networks:
  mewayz-network:
    driver: bridge
```

### **Backend Dockerfile (Dockerfile.prod)**
```dockerfile
FROM python:3.11-slim

WORKDIR /app

# Install system dependencies
RUN apt-get update && apt-get install -y \
    gcc \
    && rm -rf /var/lib/apt/lists/*

# Install Python dependencies
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# Copy application code
COPY . .

# Create non-root user
RUN groupadd -r mewayz && useradd -r -g mewayz mewayz
RUN chown -R mewayz:mewayz /app
USER mewayz

# Expose port
EXPOSE 8001

# Start application
CMD ["gunicorn", "main:app", "-w", "4", "-k", "uvicorn.workers.UvicornWorker", "-b", "0.0.0.0:8001"]
```

### **Frontend Dockerfile (Dockerfile.prod)**
```dockerfile
FROM node:18-alpine as builder

WORKDIR /app
COPY package.json yarn.lock ./
RUN yarn install --frozen-lockfile

COPY . .
RUN yarn build

# Production stage
FROM nginx:alpine

# Copy built files
COPY --from=builder /app/build /usr/share/nginx/html

# Copy nginx configuration
COPY nginx.conf /etc/nginx/nginx.conf

# Copy SSL certificates
COPY ssl/ /etc/ssl/certs/

EXPOSE 80 443

CMD ["nginx", "-g", "daemon off;"]
```

### **Nginx Configuration (nginx.conf)**
```nginx
events {
    worker_connections 1024;
}

http {
    include /etc/nginx/mime.types;
    default_type application/octet-stream;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;

    # Frontend server
    server {
        listen 80;
        server_name yourdomain.com www.yourdomain.com;
        return 301 https://$server_name$request_uri;
    }

    server {
        listen 443 ssl http2;
        server_name yourdomain.com www.yourdomain.com;

        ssl_certificate /etc/ssl/certs/fullchain.pem;
        ssl_certificate_key /etc/ssl/certs/privkey.pem;

        root /usr/share/nginx/html;
        index index.html;

        # Handle React routes
        location / {
            try_files $uri $uri/ /index.html;
        }

        # API proxy
        location /api/ {
            proxy_pass http://backend:8001;
            proxy_set_header Host $host;
            proxy_set_header X-Real-IP $remote_addr;
            proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
            proxy_set_header X-Forwarded-Proto $scheme;
        }

        # WebSocket support
        location /ws/ {
            proxy_pass http://backend:8001;
            proxy_http_version 1.1;
            proxy_set_header Upgrade $http_upgrade;
            proxy_set_header Connection "upgrade";
            proxy_set_header Host $host;
        }

        # Security headers
        add_header X-Frame-Options DENY;
        add_header X-Content-Type-Options nosniff;
        add_header X-XSS-Protection "1; mode=block";
        add_header Strict-Transport-Security "max-age=63072000; includeSubDomains; preload";
    }
}
```

### **Deploy with Docker**
```bash
# Build and start all services
docker-compose -f docker-compose.prod.yml up -d

# View logs
docker-compose -f docker-compose.prod.yml logs -f

# Scale backend if needed
docker-compose -f docker-compose.prod.yml up -d --scale backend=3
```

## ☁️ **Cloud Deployment Options**

### **AWS Deployment**

#### **Using AWS ECS + Fargate**
```bash
# Create ECS cluster
aws ecs create-cluster --cluster-name mewayz-production

# Create task definition
aws ecs register-task-definition --cli-input-json file://task-definition.json

# Create service
aws ecs create-service \
    --cluster mewayz-production \
    --service-name mewayz-service \
    --task-definition mewayz-task \
    --desired-count 2 \
    --launch-type FARGATE
```

#### **Using AWS EKS (Kubernetes)**
```yaml
# k8s-deployment.yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: mewayz-backend
spec:
  replicas: 3
  selector:
    matchLabels:
      app: mewayz-backend
  template:
    metadata:
      labels:
        app: mewayz-backend
    spec:
      containers:
      - name: backend
        image: your-registry/mewayz-backend:v3.0.0
        ports:
        - containerPort: 8001
        env:
        - name: MONGO_URL
          valueFrom:
            secretKeyRef:
              name: mewayz-secrets
              key: mongo-url
---
apiVersion: v1
kind: Service
metadata:
  name: mewayz-backend-service
spec:
  selector:
    app: mewayz-backend
  ports:
  - port: 8001
    targetPort: 8001
  type: LoadBalancer
```

### **Google Cloud Platform**

```bash
# Build and push to Container Registry
docker build -t gcr.io/your-project/mewayz-backend:v3.0.0 ./backend
docker push gcr.io/your-project/mewayz-backend:v3.0.0

# Deploy to Cloud Run
gcloud run deploy mewayz-backend \
    --image gcr.io/your-project/mewayz-backend:v3.0.0 \
    --platform managed \
    --region us-central1 \
    --allow-unauthenticated \
    --port 8001
```

### **Digital Ocean**

```bash
# Create droplet
doctl compute droplet create mewayz-prod \
    --size s-4vcpu-8gb \
    --image ubuntu-20-04-x64 \
    --region nyc1 \
    --ssh-keys your-ssh-key-id

# Deploy using Docker Compose
scp docker-compose.prod.yml root@your-droplet-ip:~/
ssh root@your-droplet-ip
docker-compose -f docker-compose.prod.yml up -d
```

## 🔒 **SSL Certificate Setup**

### **Let's Encrypt (Free SSL)**
```bash
# Install Certbot
sudo apt update
sudo apt install certbot python3-certbot-nginx

# Get certificate
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Auto-renewal
sudo crontab -e
# Add: 0 12 * * * /usr/bin/certbot renew --quiet
```

### **CloudFlare (Recommended)**
```bash
# Configure CloudFlare DNS
# Point A records to your server IP:
# yourdomain.com -> your-server-ip
# www.yourdomain.com -> your-server-ip

# Use CloudFlare's SSL (flexible or full)
# Enable security features in CloudFlare dashboard
```

## 📊 **Monitoring & Logging**

### **Application Monitoring**
```bash
# Install monitoring tools
pip install prometheus-client
npm install --save express-prometheus-middleware

# Add to backend main.py
from prometheus_client import Counter, Histogram, generate_latest
request_count = Counter('http_requests_total', 'Total HTTP requests')
request_duration = Histogram('http_request_duration_seconds', 'HTTP request duration')
```

### **Log Management**
```bash
# Centralized logging with ELK Stack
docker run -d --name elasticsearch \
    -p 9200:9200 -p 9300:9300 \
    -e "discovery.type=single-node" \
    elasticsearch:7.15.0

docker run -d --name kibana \
    -p 5601:5601 \
    --link elasticsearch:elasticsearch \
    kibana:7.15.0

# Configure Logstash for log aggregation
```

## 🔄 **Backup Strategy**

### **Database Backup**
```bash
# MongoDB backup script
#!/bin/bash
MONGO_HOST="localhost"
MONGO_PORT="27017"
DB_NAME="mewayz_production"
BACKUP_DIR="/backups/mongodb"
DATE=$(date +%Y%m%d_%H%M%S)

mongodump --host $MONGO_HOST:$MONGO_PORT --db $DB_NAME --out $BACKUP_DIR/$DATE

# Compress backup
tar -czf $BACKUP_DIR/backup_$DATE.tar.gz $BACKUP_DIR/$DATE
rm -rf $BACKUP_DIR/$DATE

# Upload to S3
aws s3 cp $BACKUP_DIR/backup_$DATE.tar.gz s3://your-backup-bucket/mongodb/
```

### **Application Backup**
```bash
# Backup application files and configurations
#!/bin/bash
BACKUP_DIR="/backups/app"
DATE=$(date +%Y%m%d_%H%M%S)

tar -czf $BACKUP_DIR/app_backup_$DATE.tar.gz \
    /app \
    /etc/nginx \
    /etc/systemd/system/mewayz.service

aws s3 cp $BACKUP_DIR/app_backup_$DATE.tar.gz s3://your-backup-bucket/app/
```

## 🚀 **Performance Optimization**

### **Backend Optimization**
```python
# Add Redis caching
import redis
redis_client = redis.Redis(host='redis', port=6379, db=0)

@app.middleware("http")
async def cache_middleware(request: Request, call_next):
    if request.method == "GET":
        cache_key = f"cache:{request.url.path}:{request.url.query}"
        cached_response = redis_client.get(cache_key)
        if cached_response:
            return JSONResponse(content=json.loads(cached_response))
    
    response = await call_next(request)
    
    if request.method == "GET" and response.status_code == 200:
        redis_client.setex(cache_key, 300, response.body)
    
    return response
```

### **Frontend Optimization**
```javascript
// Add service worker for caching
// public/sw.js
const CACHE_NAME = 'mewayz-v3.0.0';
const urlsToCache = [
    '/',
    '/static/css/main.css',
    '/static/js/main.js'
];

self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(function(cache) {
                return cache.addAll(urlsToCache);
            })
    );
});
```

## 🔧 **Maintenance & Updates**

### **Zero-Downtime Deployment**
```bash
# Blue-green deployment script
#!/bin/bash

# Build new version
docker build -t mewayz-backend:v3.0.1 ./backend

# Start new containers
docker run -d --name mewayz-backend-green \
    --network mewayz-network \
    -e MONGO_URL=$MONGO_URL \
    mewayz-backend:v3.0.1

# Health check
until curl -f http://mewayz-backend-green:8001/health; do
    echo "Waiting for green deployment..."
    sleep 5
done

# Switch traffic
docker exec nginx nginx -s reload

# Stop old containers
docker stop mewayz-backend-blue
docker rm mewayz-backend-blue
docker rename mewayz-backend-green mewayz-backend-blue
```

### **Database Migrations**
```python
# migrations/001_add_analytics_table.py
from pymongo import MongoClient

def up(db):
    db.create_collection("analytics")
    db.analytics.create_index([("workspace_id", 1), ("date", -1)])

def down(db):
    db.drop_collection("analytics")
```

## ✅ **Production Checklist**

- [ ] **Security**
  - [ ] SSL certificate configured
  - [ ] Firewall rules configured
  - [ ] Security headers enabled
  - [ ] CORS properly configured
  - [ ] Secrets stored securely

- [ ] **Performance**
  - [ ] Redis caching enabled
  - [ ] Database indexes created
  - [ ] CDN configured for static assets
  - [ ] Gzip compression enabled
  - [ ] Image optimization enabled

- [ ] **Monitoring**
  - [ ] Application monitoring setup
  - [ ] Error tracking configured
  - [ ] Log aggregation setup
  - [ ] Health checks implemented
  - [ ] Uptime monitoring configured

- [ ] **Backup**
  - [ ] Database backup automated
  - [ ] Application backup configured
  - [ ] Backup restoration tested
  - [ ] Recovery procedures documented

- [ ] **Documentation**
  - [ ] Deployment procedures documented
  - [ ] API documentation updated
  - [ ] User guides completed
  - [ ] Troubleshooting guide created

## 🎯 **Final Deployment**

```bash
# Final production deployment command
docker-compose -f docker-compose.prod.yml up -d --build

# Verify all services are running
docker-compose -f docker-compose.prod.yml ps

# Check application health
curl https://yourdomain.com/api/health

# Monitor logs
docker-compose -f docker-compose.prod.yml logs -f
```

## 📞 **Support**

**Production Support:**
- **Email:** support@mewayz.com
- **Emergency:** +1-XXX-XXX-XXXX
- **Documentation:** https://docs.mewayz.com
- **Status Page:** https://status.mewayz.com

---

**Deployment Date:** July 20, 2025  
**Version:** 3.0.0  
**Status:** ✅ **Production Ready**  
**Next Review:** August 20, 2025