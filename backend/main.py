"""
Professional FastAPI Application
Mewayz Platform - Restructured Architecture
"""
from fastapi import FastAPI, HTTPException, Depends, status
from fastapi.middleware.cors import CORSMiddleware
from contextlib import asynccontextmanager
import uvicorn

from core.config import settings
from core.database import connect_to_mongo, close_mongo_connection
from api import auth, users, analytics, dashboard, workspaces, blog, admin, ai, bio_sites, ecommerce, bookings

# Application lifespan management
@asynccontextmanager
async def lifespan(app: FastAPI):
    # Startup
    await connect_to_mongo()
    yield
    # Shutdown
    await close_mongo_connection()

# Create FastAPI application
app = FastAPI(
    title=settings.APP_NAME,
    version=settings.VERSION,
    docs_url="/docs" if settings.DEBUG else None,
    redoc_url="/redoc" if settings.DEBUG else None,
    lifespan=lifespan
)

# CORS Middleware
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # Configure appropriately for production
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Health check endpoint
@app.get("/health")
async def health_check():
    """Health check endpoint"""
    return {
        "status": "healthy",
        "app_name": settings.APP_NAME,
        "version": settings.VERSION,
        "debug": settings.DEBUG
    }

# Root endpoint
@app.get("/")
async def root():
    """Root endpoint"""
    return {
        "message": f"Welcome to {settings.APP_NAME}",
        "version": settings.VERSION,
        "docs_url": "/docs" if settings.DEBUG else "Contact admin for API documentation"
    }

# Include routers with real functionality
app.include_router(auth.router, prefix="/api/auth", tags=["Authentication"])
app.include_router(users.router, prefix="/api/users", tags=["Users"])
app.include_router(analytics.router, prefix="/api/analytics", tags=["Analytics"])
app.include_router(dashboard.router, prefix="/api/dashboard", tags=["Dashboard"])
app.include_router(workspaces.router, prefix="/api/workspaces", tags=["Workspaces"])
app.include_router(blog.router, prefix="/api/blog", tags=["Blog & Content"])
app.include_router(admin.router, prefix="/api/admin", tags=["Administration"])
app.include_router(ai.router, prefix="/api/ai", tags=["AI Services"])
app.include_router(bio_sites.router, prefix="/api/bio-sites", tags=["Bio Sites"])

if __name__ == "__main__":
    uvicorn.run(
        "main:app",
        host="0.0.0.0",
        port=8001,
        reload=settings.DEBUG,
        log_level="info" if not settings.DEBUG else "debug"
    )