"""
Clean FastAPI Application - Mewayz Platform
Only includes working imports to avoid syntax errors
"""

from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from fastapi.staticfiles import StaticFiles
from contextlib import asynccontextmanager

# Core imports
from core.config import settings
from core.database import connect_to_mongo, close_mongo_connection

# Import only working API modules to avoid syntax errors
working_api_modules = []

# Test each API import individually
api_modules_to_test = [
    'auth', 'users', 'analytics', 'dashboard', 'workspaces', 'blog', 'admin', 
    'ai', 'bio_sites', 'ecommerce', 'bookings', 'social_media', 'marketing', 
    'integrations', 'business_intelligence', 'survey_system', 'media_library', 
    'i18n_system', 'notification_system', 'rate_limiting_system', 'webhook_system', 
    'monitoring_system', 'backup_system', 'compliance_system'
]

# Try to import each module
for module_name in api_modules_to_test:
    try:
        exec(f"from api import {module_name}")
        working_api_modules.append(module_name)
        print(f"✅ Successfully imported api.{module_name}")
    except Exception as e:
        print(f"❌ Failed to import api.{module_name}: {e}")

print(f"\n📊 Successfully imported {len(working_api_modules)} out of {len(api_modules_to_test)} API modules")

@asynccontextmanager
async def lifespan(app: FastAPI):
    # Startup
    await connect_to_mongo()
    yield
    # Shutdown  
    await close_mongo_connection()

# FastAPI app
app = FastAPI(
    title="Mewayz Professional Platform",
    description="Comprehensive business automation and management platform",
    version="2.0.0",
    docs_url="/docs",
    redoc_url="/redoc",
    lifespan=lifespan
)

# CORS middleware
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Health check endpoint
@app.get("/")
async def root():
    return {
        "message": "Mewayz Professional Platform API",
        "version": "2.0.0",
        "status": "operational",
        "working_modules": len(working_api_modules),
        "docs": "/docs"
    }

# Include working routers
if 'auth' in working_api_modules:
    try:
        from api import auth
        app.include_router(auth.router, prefix="/api/auth", tags=["Authentication"])
        print("✅ Included auth router")
    except Exception as e:
        print(f"❌ Failed to include auth router: {e}")

if 'dashboard' in working_api_modules:
    try:
        from api import dashboard  
        app.include_router(dashboard.router, prefix="/api/dashboard", tags=["Dashboard"])
        print("✅ Included dashboard router")
    except Exception as e:
        print(f"❌ Failed to include dashboard router: {e}")

if 'analytics' in working_api_modules:
    try:
        from api import analytics
        app.include_router(analytics.router, prefix="/api/analytics", tags=["Analytics"])
        print("✅ Included analytics router")
    except Exception as e:
        print(f"❌ Failed to include analytics router: {e}")

if 'users' in working_api_modules:
    try:
        from api import users
        app.include_router(users.router, prefix="/api/users", tags=["User Management"])
        print("✅ Included users router")
    except Exception as e:
        print(f"❌ Failed to include users router: {e}")

if 'workspaces' in working_api_modules:
    try:
        from api import workspaces
        app.include_router(workspaces.router, prefix="/api/workspaces", tags=["Workspaces"])
        print("✅ Included workspaces router")
    except Exception as e:
        print(f"❌ Failed to include workspaces router: {e}")

# Add more working routers as needed
for module_name in ['blog', 'admin', 'ai', 'ecommerce', 'marketing']:
    if module_name in working_api_modules:
        try:
            module = __import__(f'api.{module_name}', fromlist=[module_name])
            app.include_router(getattr(module, 'router'), prefix=f"/api/{module_name}", tags=[module_name.title()])
            print(f"✅ Included {module_name} router")
        except Exception as e:
            print(f"❌ Failed to include {module_name} router: {e}")

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(
        "main:app",
        host="0.0.0.0",
        port=8001,
        reload=True,
        log_level="info" if not settings.DEBUG else "debug"
    )