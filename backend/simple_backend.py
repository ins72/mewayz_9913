"""
Simple FastAPI Backend for Mewayz Platform
MongoDB Production Ready
"""

import os
import logging
from typing import Dict, Any
from datetime import datetime
from contextlib import asynccontextmanager

from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from fastapi.responses import JSONResponse
from motor.motor_asyncio import AsyncIOMotorClient

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# Global MongoDB connection
mongo_client = None
mongo_db = None

async def init_mongodb():
    """Initialize MongoDB connection"""
    global mongo_client, mongo_db
    
    try:
        # MongoDB connection string
        mongodb_url = "mongodb://localhost:5000"
        mongo_client = AsyncIOMotorClient(mongodb_url)
        
        # Test connection
        await mongo_client.admin.command('ping')
        
        mongo_db = mongo_client.mewayz_production
        logger.info("🚀 MongoDB connection established successfully")
        return True
    except Exception as e:
        logger.error(f"❌ Failed to connect to MongoDB: {e}")
        return False

async def populate_sample_data():
    """Populate MongoDB with sample data"""
    try:
        # Users collection
        users_collection = mongo_db.users
        if await users_collection.count_documents({}) == 0:
            sample_user = {
                "email": "admin@mewayz.com",
                "username": "admin",
                "hashed_password": "hashed_password_123",
                "full_name": "Admin User",
                "is_active": True,
                "is_verified": True,
                "created_at": datetime.utcnow(),
                "updated_at": datetime.utcnow()
            }
            await users_collection.insert_one(sample_user)
            logger.info("✅ Sample user created")
        
        # Workspaces collection
        workspaces_collection = mongo_db.workspaces
        if await workspaces_collection.count_documents({}) == 0:
            sample_workspace = {
                "name": "Mewayz Business Platform",
                "description": "Professional business management workspace",
                "user_id": "admin",
                "status": "active",
                "created_at": datetime.utcnow(),
                "updated_at": datetime.utcnow()
            }
            await workspaces_collection.insert_one(sample_workspace)
            logger.info("✅ Sample workspace created")
        
        logger.info("✅ Sample data populated successfully")
        
    except Exception as e:
        logger.error(f"❌ Failed to populate sample data: {e}")

@asynccontextmanager
async def lifespan(app: FastAPI):
    """Application lifespan manager"""
    # Startup
    logger.info("🚀 Starting Mewayz Professional Platform...")
    logger.info("🎯 MongoDB Production Ready")
    
    # Initialize MongoDB
    if await init_mongodb():
        await populate_sample_data()
        logger.info("✅ MongoDB database initialized successfully")
    else:
        logger.error("❌ Failed to initialize MongoDB")
    
    yield
    
    # Shutdown
    if mongo_client:
        mongo_client.close()
        logger.info("✅ MongoDB connection closed")

# Create FastAPI app
app = FastAPI(
    title="Mewayz Professional Platform",
    description="Complete Business Management Platform",
    version="4.0.0",
    lifespan=lifespan
)

# Add CORS middleware
app.add_middleware(
    CORSMiddleware,
    allow_origins=["http://localhost:3000", "http://127.0.0.1:3000"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

@app.get("/")
async def root():
    """Root endpoint"""
    return {
        "message": "Mewayz Professional Platform API",
        "version": "4.0.0",
        "status": "running",
        "database": "MongoDB",
        "timestamp": datetime.utcnow().isoformat()
    }

@app.get("/health")
async def health_check():
    """Health check endpoint"""
    return {
        "status": "healthy",
        "database": "connected" if mongo_db else "disconnected",
        "timestamp": datetime.utcnow().isoformat()
    }

@app.get("/api/workspaces")
async def get_workspaces():
    """Get all workspaces"""
    try:
        if not mongo_db:
            raise HTTPException(status_code=500, detail="Database not connected")
        
        workspaces = await mongo_db.workspaces.find({}).to_list(length=100)
        return {"workspaces": workspaces, "count": len(workspaces)}
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

@app.get("/api/users")
async def get_users():
    """Get all users"""
    try:
        if not mongo_db:
            raise HTTPException(status_code=500, detail="Database not connected")
        
        users = await mongo_db.users.find({}).to_list(length=100)
        return {"users": users, "count": len(users)}
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

@app.get("/api/analytics/overview")
async def analytics_overview():
    """Get analytics overview"""
    try:
        if not mongo_db:
            raise HTTPException(status_code=500, detail="Database not connected")
        
        # Get counts
        user_count = await mongo_db.users.count_documents({})
        workspace_count = await mongo_db.workspaces.count_documents({})
        
        return {
            "total_users": user_count,
            "total_workspaces": workspace_count,
            "platform_status": "active",
            "timestamp": datetime.utcnow().isoformat()
        }
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8000) 