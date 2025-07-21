"""
Link Shortener Services Business Logic
Professional Mewayz Platform
"""

from datetime import datetime
from typing import Dict, Any, List, Optional
from core.database import get_database
import uuid
import hashlib
import random
import string

class LinkShortenerService:
    """Service for link shortening operations"""
    
    @staticmethod
    def generate_short_code(length: int = 6) -> str:
        """Generate random short code"""
        characters = string.ascii_letters + string.digits
        return ''.join(random.choice(characters) for _ in range(length))
    
    @staticmethod
    async def create_short_link(user_id: str, url: str, custom_code: str = None):
        """Create shortened link"""
        db = await get_database()
        
        # Generate or use custom short code
        short_code = custom_code or LinkShortenerService.generate_short_code()
        
        # Check if code already exists
        existing = await db.short_links.find_one({"short_code": short_code})
        if existing:
            if custom_code:
                raise ValueError("Custom code already exists")
            # Generate new random code
            short_code = LinkShortenerService.generate_short_code()
        
        link = {
            "_id": str(uuid.uuid4()),
            "user_id": user_id,
            "original_url": url,
            "short_code": short_code,
            "click_count": 0,
            "created_at": datetime.utcnow(),
            "expires_at": None,
            "status": "active"
        }
        
        result = await db.short_links.insert_one(link)
        return link
    
    @staticmethod
    async def get_user_links(user_id: str):
        """Get user's shortened links"""
        db = await get_database()
        
        links = await db.short_links.find({"user_id": user_id}).sort("created_at", -1).to_list(length=None)
        return links
    
    @staticmethod
    async def get_link_by_code(short_code: str):
        """Get link by short code"""
        db = await get_database()
        
        link = await db.short_links.find_one({"short_code": short_code, "status": "active"})
        return link
    
    @staticmethod
    async def increment_click_count(short_code: str, visitor_info: Dict[str, Any] = None):
        """Increment click count and log visit"""
        db = await get_database()
        
        # Update click count
        result = await db.short_links.update_one(
            {"short_code": short_code},
            {"$inc": {"click_count": 1}}
        )
        
        # Log visit for analytics
        if visitor_info:
            visit = {
                "_id": str(uuid.uuid4()),
                "short_code": short_code,
                "visitor_ip": visitor_info.get("ip"),
                "user_agent": visitor_info.get("user_agent"),
                "referer": visitor_info.get("referer"),
                "visited_at": datetime.utcnow()
            }
            await db.link_visits.insert_one(visit)
        
        return result.modified_count > 0