"""
Database Connection and Models
Professional Mewayz Platform
"""
from motor.motor_asyncio import AsyncIOMotorClient
from typing import Optional
import asyncio
from .config import settings

class Database:
    client: Optional[AsyncIOMotorClient] = None
    database = None

db = Database()

async def connect_to_mongo():
    """Create database connection"""
    db.client = AsyncIOMotorClient(settings.MONGO_URL)
    db.database = db.client[settings.DATABASE_NAME]
    
    # Test connection
    try:
        await db.client.admin.command('ping')
        print(f"✅ Connected to MongoDB: {settings.DATABASE_NAME}")
    except Exception as e:
        print(f"❌ Failed to connect to MongoDB: {e}")
        raise

async def close_mongo_connection():
    """Close database connection"""
    if db.client:
        db.client.close()
        print("✅ Disconnected from MongoDB")

def get_database():
    """Get database instance"""
    return db.database

# Collection getters - these will return the actual collections
def get_users_collection():
    return db.database.users

def get_workspaces_collection():
    return db.database.workspaces

def get_bio_sites_collection():
    return db.database.bio_sites

def get_analytics_collection():
    return db.database.analytics_events

def get_bookings_collection():
    return db.database.bookings

def get_services_collection():
    return db.database.services

def get_contacts_collection():
    return db.database.contacts

def get_campaigns_collection():
    return db.database.email_campaigns

def get_ai_conversations_collection():
    return db.database.ai_conversations

def get_notifications_collection():
    return db.database.notifications