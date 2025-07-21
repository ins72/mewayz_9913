"""
Database Collections Initialization Script
Creates all necessary collections for real data storage
"""

from motor.motor_asyncio import AsyncIOMotorClient
from datetime import datetime, timedelta
import uuid
import random

class DatabaseInitializer:
    def __init__(self, mongodb_url: str = "mongodb://localhost:27017/mewayz_pro"):
        self.client = AsyncIOMotorClient(mongodb_url)
        self.db = self.client.mewayz_professional
    
    async def initialize_collections(self):
        """Initialize all required collections with sample data"""
        print("🚀 Initializing database collections...")
        
        # User Activities Collection
        await self._init_user_activities()
        
        # AI Usage Collection  
        await self._init_ai_usage()
        
        # Analytics Collection
        await self._init_analytics()
        
        # Projects Collection
        await self._init_projects()
        
        # Email Campaigns Collection
        await self._init_email_campaigns()
        
        # Social Media Posts Collection
        await self._init_social_media_posts()
        
        # Financial Transactions Collection
        await self._init_financial_transactions()
        
        # Page Visits Collection
        await self._init_page_visits()
        
        # User Actions Collection
        await self._init_user_actions()
        
        # User Sessions Collection
        await self._init_user_sessions()
        
        print("✅ Database collections initialized successfully!")
    
    async def _init_user_activities(self):
        """Initialize user activities collection"""
        collection = self.db.user_activities
        
        # Create indexes
        await collection.create_index("user_id")
        await collection.create_index("timestamp")
        await collection.create_index([("user_id", 1), ("timestamp", -1)])
        
        # Sample data
        sample_activities = [
            {
                "_id": str(uuid.uuid4()),
                "user_id": "sample_user_1",
                "type": "login",
                "message": "User logged in successfully",
                "timestamp": datetime.utcnow() - timedelta(hours=1),
                "metadata": {"ip_address": "192.168.1.1", "user_agent": "Mozilla/5.0..."}
            },
            {
                "_id": str(uuid.uuid4()),
                "user_id": "sample_user_1", 
                "type": "workspace_created",
                "message": "New workspace 'My Business' created",
                "timestamp": datetime.utcnow() - timedelta(hours=2),
                "metadata": {"workspace_name": "My Business"}
            }
        ]
        
        await collection.insert_many(sample_activities)
        print("  ✅ user_activities collection initialized")
    
    async def _init_ai_usage(self):
        """Initialize AI usage collection"""
        collection = self.db.ai_usage
        
        await collection.create_index("user_id")
        await collection.create_index("created_at")
        await collection.create_index("service_type")
        
        sample_usage = [
            {
                "_id": str(uuid.uuid4()),
                "user_id": "sample_user_1",
                "service_type": "Text Generation", 
                "job_id": str(uuid.uuid4()),
                "tokens_used": 150,
                "cost": 0.30,
                "status": "success",
                "response_time": 2.5,
                "created_at": datetime.utcnow() - timedelta(hours=1),
                "metadata": {"model": "gpt-4", "prompt_length": 50}
            },
            {
                "_id": str(uuid.uuid4()),
                "user_id": "sample_user_1",
                "service_type": "Image Generation",
                "job_id": str(uuid.uuid4()),
                "tokens_used": 30,
                "cost": 0.04,
                "status": "success", 
                "response_time": 15.0,
                "created_at": datetime.utcnow() - timedelta(hours=3),
                "metadata": {"model": "dall-e-3", "resolution": "1024x1024"}
            }
        ]
        
        await collection.insert_many(sample_usage)
        print("  ✅ ai_usage collection initialized")
    
    async def _init_analytics(self):
        """Initialize analytics collection"""
        collection = self.db.analytics
        
        await collection.create_index("user_id")
        await collection.create_index("event_type")
        await collection.create_index("timestamp")
        
        print("  ✅ analytics collection initialized")
    
    async def _init_projects(self):
        """Initialize projects collection"""
        collection = self.db.projects
        
        await collection.create_index("user_id")
        await collection.create_index("status")
        
        sample_projects = [
            {
                "_id": str(uuid.uuid4()),
                "user_id": "sample_user_1",
                "name": "Website Redesign",
                "status": "active",
                "created_at": datetime.utcnow() - timedelta(days=5),
                "updated_at": datetime.utcnow() - timedelta(hours=2)
            },
            {
                "_id": str(uuid.uuid4()),
                "user_id": "sample_user_1",
                "name": "Marketing Campaign",
                "status": "completed",
                "created_at": datetime.utcnow() - timedelta(days=10),
                "updated_at": datetime.utcnow() - timedelta(days=2)
            }
        ]
        
        await collection.insert_many(sample_projects)
        print("  ✅ projects collection initialized")
    
    async def _init_email_campaigns(self):
        """Initialize email campaigns collection"""
        collection = self.db.email_campaigns
        
        await collection.create_index("user_id")
        await collection.create_index("status")
        
        print("  ✅ email_campaigns collection initialized")
    
    async def _init_social_media_posts(self):
        """Initialize social media posts collection"""
        collection = self.db.social_media_posts
        
        await collection.create_index("user_id")
        await collection.create_index("platform")
        await collection.create_index("created_at")
        
        print("  ✅ social_media_posts collection initialized")
    
    async def _init_financial_transactions(self):
        """Initialize financial transactions collection"""
        collection = self.db.financial_transactions
        
        await collection.create_index("user_id")
        await collection.create_index("transaction_date")
        await collection.create_index("type")
        
        print("  ✅ financial_transactions collection initialized")
    
    async def _init_page_visits(self):
        """Initialize page visits collection"""
        collection = self.db.page_visits
        
        await collection.create_index("user_id")
        await collection.create_index("visited_at")
        
        sample_visits = [
            {
                "_id": str(uuid.uuid4()),
                "user_id": "sample_user_1",
                "page": "/dashboard",
                "visited_at": datetime.utcnow() - timedelta(hours=i),
                "session_id": str(uuid.uuid4()),
                "duration": random.randint(30, 300)
            } for i in range(1, 25)  # Last 24 hours
        ]
        
        await collection.insert_many(sample_visits)
        print("  ✅ page_visits collection initialized")
    
    async def _init_user_actions(self):
        """Initialize user actions collection"""
        collection = self.db.user_actions
        
        await collection.create_index("user_id")
        await collection.create_index("status")
        
        sample_actions = [
            {
                "_id": str(uuid.uuid4()),
                "user_id": "sample_user_1",
                "action": "create_workspace",
                "status": "completed",
                "timestamp": datetime.utcnow() - timedelta(hours=2)
            },
            {
                "_id": str(uuid.uuid4()),
                "user_id": "sample_user_1", 
                "action": "upload_file",
                "status": "failed",
                "timestamp": datetime.utcnow() - timedelta(hours=3)
            }
        ]
        
        await collection.insert_many(sample_actions)
        print("  ✅ user_actions collection initialized")
    
    async def _init_user_sessions(self):
        """Initialize user sessions collection"""
        collection = self.db.user_sessions
        
        await collection.create_index("user_id")
        await collection.create_index("expires_at")
        
        sample_sessions = [
            {
                "_id": str(uuid.uuid4()),
                "user_id": "sample_user_1",
                "session_id": str(uuid.uuid4()),
                "created_at": datetime.utcnow() - timedelta(hours=1),
                "expires_at": datetime.utcnow() + timedelta(hours=23),
                "is_active": True,
                "last_activity": datetime.utcnow() - timedelta(minutes=5)
            }
        ]
        
        await collection.insert_many(sample_sessions)
        print("  ✅ user_sessions collection initialized")

if __name__ == "__main__":
    import asyncio
    
    async def main():
        initializer = DatabaseInitializer()
        await initializer.initialize_collections()
        print("\n🎉 Database initialization complete!")
        print("   Real data collections are now available for services to use.")
    
    asyncio.run(main())