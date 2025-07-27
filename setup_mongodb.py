#!/usr/bin/env python3
"""
MongoDB Setup Script
Sets up MongoDB connection and tests it
"""

import asyncio
from motor.motor_asyncio import AsyncIOMotorClient
from datetime import datetime
import os

async def setup_mongodb():
    """Setup MongoDB connection"""
    try:
        # Try different MongoDB connection options
        connection_strings = [
            "mongodb://localhost:27017",
            "mongodb://127.0.0.1:27017",
            "mongodb+srv://test:test@cluster0.mongodb.net/mewayz?retryWrites=true&w=majority"  # Example cloud connection
        ]
        
        client = None
        for conn_str in connection_strings:
            try:
                print(f"🔗 Trying MongoDB connection: {conn_str}")
                client = AsyncIOMotorClient(conn_str)
                
                # Test connection
                await client.admin.command('ping')
                print(f"✅ MongoDB connection successful: {conn_str}")
                break
            except Exception as e:
                print(f"❌ Connection failed: {e}")
                if client:
                    client.close()
                continue
        
        if not client:
            print("❌ No MongoDB connection available")
            return None
        
        # Get database
        db = client.mewayz_production
        
        # Test database operations
        print("🧪 Testing database operations...")
        
        # Test insert
        test_collection = db.test
        test_doc = {"test": "data", "timestamp": datetime.utcnow()}
        result = await test_collection.insert_one(test_doc)
        print(f"✅ Insert test successful: {result.inserted_id}")
        
        # Test find
        found_doc = await test_collection.find_one({"_id": result.inserted_id})
        print(f"✅ Find test successful: {found_doc}")
        
        # Test update
        update_result = await test_collection.update_one(
            {"_id": result.inserted_id},
            {"$set": {"updated": True}}
        )
        print(f"✅ Update test successful: {update_result.modified_count}")
        
        # Test delete
        delete_result = await test_collection.delete_one({"_id": result.inserted_id})
        print(f"✅ Delete test successful: {delete_result.deleted_count}")
        
        print("🎉 MongoDB setup completed successfully!")
        return client
        
    except Exception as e:
        print(f"❌ MongoDB setup failed: {e}")
        return None

async def create_collections():
    """Create necessary collections"""
    try:
        client = AsyncIOMotorClient("mongodb://localhost:27017")
        db = client.mewayz_production
        
        collections = [
            "users",
            "workspaces", 
            "analytics",
            "products",
            "crm_contacts",
            "support_tickets",
            "ai_usage",
            "user_activities",
            "marketing_analytics"
        ]
        
        for collection_name in collections:
            try:
                await db.create_collection(collection_name)
                print(f"✅ Created collection: {collection_name}")
            except Exception as e:
                print(f"ℹ️ Collection {collection_name} already exists or error: {e}")
        
        print("🎉 Collections setup completed!")
        
    except Exception as e:
        print(f"❌ Collections setup failed: {e}")

async def main():
    """Main function"""
    print("🚀 Setting up MongoDB for Mewayz Platform...")
    
    # Setup MongoDB connection
    client = await setup_mongodb()
    
    if client:
        # Create collections
        await create_collections()
        
        # Close connection
        client.close()
        print("✅ MongoDB setup completed successfully!")
    else:
        print("❌ MongoDB setup failed. Please ensure MongoDB is running.")

if __name__ == "__main__":
    asyncio.run(main()) 