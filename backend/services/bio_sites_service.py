"""
Bio Sites Services Business Logic
Professional Mewayz Platform
"""

from datetime import datetime
from typing import Dict, Any, List, Optional
from core.database import get_database
import uuid

class BioSitesService:
    """Service for bio sites operations"""
    
    @staticmethod
    async def get_bio_site(user_id: str):
        """Get user's bio site"""
        db = await get_database()
        
        bio_site = await db.bio_sites.find_one({"user_id": user_id})
        if not bio_site:
            # Create default bio site
            bio_site = {
                "_id": str(uuid.uuid4()),
                "user_id": user_id,
                "title": "My Bio",
                "bio": "Welcome to my bio site!",
                "links": [],
                "theme": "modern",
                "is_published": False,
                "created_at": datetime.utcnow()
            }
            await db.bio_sites.insert_one(bio_site)
        
        return bio_site
    
    @staticmethod
    async def update_bio_site(user_id: str, site_data: Dict[str, Any]):
        """Update user's bio site"""
        db = await get_database()
        
        update_data = {
            "title": site_data.get("title"),
            "bio": site_data.get("bio"),
            "links": site_data.get("links", []),
            "theme": site_data.get("theme", "modern"),
            "is_published": site_data.get("is_published", False),
            "updated_at": datetime.utcnow()
        }
        
        result = await db.bio_sites.update_one(
            {"user_id": user_id},
            {"$set": update_data},
            upsert=True
        )
        
        return await db.bio_sites.find_one({"user_id": user_id})

# Global service instance
bio_sites_service = BioSitesService()
