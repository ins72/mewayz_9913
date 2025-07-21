"""
Blog/Content Services Business Logic
Professional Mewayz Platform
"""

from datetime import datetime
from typing import Dict, Any, List, Optional
from core.database import get_database
import uuid
import re

class BlogService:
    """Service for blog and content operations"""
    
    @staticmethod
    def generate_slug(title: str) -> str:
        """Generate URL-friendly slug from title"""
        slug = title.lower()
        slug = re.sub(r'[^a-z0-9\s-]', '', slug)
        slug = re.sub(r'[\s-]+', '-', slug)
        return slug.strip('-')
    
    @staticmethod
    async def get_blog_posts(user_id: str, published_only: bool = False):
        """Get user's blog posts"""
        db = await get_database()
        
        query = {"user_id": user_id}
        if published_only:
            query["status"] = "published"
        
        posts = await db.blog_posts.find(query).sort("created_at", -1).to_list(length=None)
        return posts
    
    @staticmethod
    async def create_blog_post(user_id: str, post_data: Dict[str, Any]):
        """Create new blog post"""
        db = await get_database()
        
        slug = BlogService.generate_slug(post_data.get("title", ""))
        
        post = {
    "_id": str(uuid.uuid4()),
    "user_id": user_id,
    "title": post_data.get("title"),
    "slug": slug,
    "content": post_data.get("content", ""),
    "excerpt": post_data.get("excerpt", ""),
    "featured_image": post_data.get("featured_image"),
    "categories": post_data.get("categories", []),
    "tags": post_data.get("tags", []),
    "status": post_data.get("status", "draft"),
    "seo": {
    "meta_title": post_data.get("meta_title"),
    "meta_description": post_data.get("meta_description"),
    "og_image": post_data.get("og_image")
    },
    "view_count": 0,
    "like_count": 0,
    "comment_count": 0,
    "created_at": datetime.utcnow(),
    "updated_at": datetime.utcnow(),
            "published_at": datetime.utcnow() if post_data.get("status") == "published" else None
    }
        
        result = await db.blog_posts.insert_one(post)
        return post
    
    @staticmethod
    async def get_post_by_slug(slug: str, user_id: str = None):
        """Get blog post by slug"""
        db = await get_database()
        
        query = {"slug": slug}
        if user_id:
            query["user_id"] = user_id
        
        post = await db.blog_posts.find_one(query)
        return post
    
    @staticmethod
    async def get_blog_categories(user_id: str):
        """Get blog categories for user"""
        db = await get_database()
        
        # Aggregate categories from posts
        pipeline = [
    {"$match": {"user_id": user_id}},
    {"$unwind": "$categories"},
    {"$group": {"_id": "$categories", "count": {"$sum": 1}}},
    {"$sort": {"count": -1}}
    ]
        
        categories = await db.blog_posts.aggregate(pipeline).to_list(length=None)
        return [{"name": cat["_id"], "count": cat["count"]} for cat in categories]

# Global service instance
blog_service = BlogService()
