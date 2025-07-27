"""
Professional FastAPI Application - Mewayz Platform
Complete Enterprise-Grade Implementation - SQLite Version for Testing
Version: 4.0.0 - SQLite Mode
"""

import os
import logging
from typing import Dict, Any, Optional, List
from datetime import datetime
from contextlib import asynccontextmanager

from fastapi import FastAPI, HTTPException, Depends, Request
from fastapi.middleware.cors import CORSMiddleware
from fastapi.responses import JSONResponse
from fastapi.exceptions import RequestValidationError
from starlette.exceptions import HTTPException as StarletteHTTPException
import time

# Core imports
from core.config import settings

# Complete list of all API modules
ALL_API_MODULES = [
    'admin', 'advanced_ai', 'advanced_ai_analytics', 'advanced_ai_suite', 'advanced_analytics',
    'advanced_financial', 'advanced_financial_analytics', 'ai', 'ai_content', 'ai_content_generation',
    'ai_token_management', 'analytics', 'analytics_system', 'auth', 'automation_system',
    'backup_system', 'bio_sites', 'blog', 'booking', 'bookings', 'business_intelligence',
    'compliance_system', 'content', 'content_creation', 'content_creation_suite', 'course_management',
    'crm_management', 'customer_experience', 'customer_experience_suite', 'dashboard', 'ecommerce',
    'email_marketing', 'enhanced_ecommerce', 'escrow_system', 'financial_management', 'form_builder',
    'google_oauth', 'i18n_system', 'integration', 'integrations', 'link_shortener', 'marketing',
    'media', 'media_library', 'monitoring_system', 'notification_system', 'onboarding_system',
    'promotions_referrals', 'rate_limiting_system', 'realtime_notifications', 'social_email',
    'social_email_integration', 'social_media', 'social_media_suite', 'subscription_management',
    'support_system', 'survey_system', 'team_management', 'template_marketplace', 'user', 'users',
    'website_builder', 'webhook_system', 'workflow_automation', 'workspace', 'workspaces'
]

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# Global variables for database connection
db_client = None
redis_client = None

@asynccontextmanager
async def lifespan(app: FastAPI):
    """Application lifespan manager"""
    global db_client, redis_client
    
    # Startup
    logger.info("🚀 Starting Mewayz Professional Platform v4.0...")
    logger.info("🎯 SQLite Mode - Lightweight Database")
    
    # Initialize database connection (SQLite)
    try:
        import sqlite3
        db_path = os.path.join(os.path.dirname(os.path.dirname(__file__)), 'databases', 'mewayz.db')
        db_client = sqlite3.connect(db_path)
        db_client.row_factory = sqlite3.Row
        
        # Create basic tables if they don't exist
        cursor = db_client.cursor()
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                email TEXT UNIQUE NOT NULL,
                username TEXT UNIQUE NOT NULL,
                hashed_password TEXT NOT NULL,
                is_active BOOLEAN DEFAULT 1,
                is_verified BOOLEAN DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ''')
        
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS workspaces (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                description TEXT,
                user_id INTEGER,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users (id)
            )
        ''')
        
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS analytics (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                workspace_id INTEGER,
                metric_name TEXT NOT NULL,
                metric_value REAL NOT NULL,
                recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (workspace_id) REFERENCES workspaces (id)
            )
        ''')
        
        db_client.commit()
        logger.info("✅ SQLite database initialized successfully")
        
    except Exception as e:
        logger.error(f"❌ Database initialization failed: {e}")
    
    yield
    
    # Shutdown
    if db_client:
        db_client.close()
        logger.info("🔌 Database connection closed")

# Create FastAPI app
app = FastAPI(
    title="Mewayz Professional Platform",
    description="Complete Enterprise Business Automation Platform",
    version="4.0.0",
    lifespan=lifespan
)

# CORS middleware
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # Configure appropriately for production
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Global exception handlers
@app.exception_handler(StarletteHTTPException)
async def http_exception_handler(request: Request, exc: StarletteHTTPException):
    return JSONResponse(
        status_code=exc.status_code,
        content={"detail": exc.detail, "path": str(request.url)}
    )

@app.exception_handler(RequestValidationError)
async def validation_exception_handler(request: Request, exc: RequestValidationError):
    return JSONResponse(
        status_code=422,
        content={"detail": exc.errors(), "path": str(request.url)}
    )

# Load API modules
logger.info("🚀 Loading Mewayz Professional Platform API modules...")

loaded_modules = 0
failed_modules = 0

for module_name in ALL_API_MODULES:
    try:
        module = __import__(f"api.{module_name}", fromlist=["router"])
        if hasattr(module, "router"):
            app.include_router(module.router, prefix=f"/api/{module_name.replace('_', '-')}")
            logger.info(f"  ✅ {module_name}")
            loaded_modules += 1
        else:
            logger.warning(f"  ⚠️  Skipping {module_name}: No router found")
            failed_modules += 1
    except Exception as e:
        logger.warning(f"  ⚠️  Skipping {module_name}: {str(e)}")
        failed_modules += 1

logger.info(f"📊 Successfully imported {loaded_modules} out of {len(ALL_API_MODULES)} API modules")
if failed_modules > 0:
    logger.info(f"❌ Failed modules: {failed_modules}")

# Health check endpoints
@app.get("/")
async def root():
    return {
        "message": "Mewayz Professional Platform v4.0.0",
        "status": "running",
        "database": "sqlite",
        "modules_loaded": loaded_modules,
        "total_modules": len(ALL_API_MODULES)
    }

@app.get("/health")
async def health_check():
    return {
        "status": "healthy",
        "timestamp": datetime.now().isoformat(),
        "database": "connected" if db_client else "disconnected",
        "modules": f"{loaded_modules}/{len(ALL_API_MODULES)}"
    }

@app.get("/api/health")
async def api_health():
    return {
        "status": "healthy",
        "api_version": "4.0.0",
        "database": "sqlite",
        "modules_loaded": loaded_modules
    }

@app.get("/healthz")
async def healthz():
    return {"status": "ok"}

@app.get("/ready")
async def ready():
    return {"status": "ready"}

@app.get("/metrics")
async def metrics():
    return {
        "modules_loaded": loaded_modules,
        "modules_failed": failed_modules,
        "total_modules": len(ALL_API_MODULES),
        "database_connected": db_client is not None
    }

# Request logging middleware
@app.middleware("http")
async def log_requests(request: Request, call_next):
    start_time = time.time()
    response = await call_next(request)
    process_time = time.time() - start_time
    
    logger.info(f"{request.method} {request.url.path} - {response.status_code} - {process_time:.3f}s")
    
    return response

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8001) 