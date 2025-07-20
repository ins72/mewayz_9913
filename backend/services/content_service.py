"""
Content Service - Real Database Operations for Blogging
Professional Mewayz Platform
"""
from typing import Optional, Dict, Any, List
from datetime import datetime
import uuid
import re

from core.database import get_database

class ContentService:
    def __init__(self):
        self.db = None
        self.blog_posts_collection = None
        self.categories_collection = None
        self.tags_collection = None
    
    def _ensure_collections(self):
        """Ensure collections are initialized"""
        if self.db is None:
            self.db = get_database()
            self.blog_posts_collection = self.db.blog_posts
            self.categories_collection = self.db.blog_categories
            self.tags_collection = self.db.blog_tags

    async def create_blog_post(self, post_data: Dict[str, Any], author_id: str) -> Dict[str, Any]:
        """Create blog post with real database operations"""
        # Generate slug from title
        slug = self._generate_slug(post_data["title"])
        
        # Check if slug already exists
        existing_post = await self.blog_posts_collection.find_one({"slug": slug})
        if existing_post:
            slug = f"{slug}-{str(uuid.uuid4())[:8]}"
        
        # Create post document
        post_doc = {
            "_id": str(uuid.uuid4()),
            "title": post_data["title"],
            "slug": slug,
            "content": post_data["content"],
            "excerpt": post_data.get("excerpt", self._generate_excerpt(post_data["content"])),
            "author_id": author_id,
            "status": post_data.get("status", "draft"),  # draft, published, archived
            "featured_image": post_data.get("featured_image"),
            "categories": post_data.get("categories", []),
            "tags": post_data.get("tags", []),
            "seo": {
                "meta_title": post_data.get("meta_title", post_data["title"]),
                "meta_description": post_data.get("meta_description", self._generate_excerpt(post_data["content"])[:160]),
                "focus_keyword": post_data.get("focus_keyword"),
                "seo_score": 0  # Will be calculated
            },
            "analytics": {
                "views": 0,
                "likes": 0,
                "shares": 0,
                "comments": 0,
                "reading_time": self._calculate_reading_time(post_data["content"])
            },
            "created_at": datetime.utcnow(),
            "updated_at": datetime.utcnow(),
            "published_at": datetime.utcnow() if post_data.get("status") == "published" else None
        }
        
        # Calculate SEO score
        post_doc["seo"]["seo_score"] = self._calculate_seo_score(post_doc)
        
        # Insert post
        await self.blog_posts_collection.insert_one(post_doc)
        
        # Update categories and tags
        await self._update_categories_and_tags(post_doc["categories"], post_doc["tags"])
        
        return post_doc

    async def get_blog_posts(self, 
                           filters: Optional[Dict[str, Any]] = None,
                           limit: int = 10,
                           skip: int = 0) -> Dict[str, Any]:
        """Get blog posts with real database operations"""
        query = {}
        
        if filters:
            if filters.get("status"):
                query["status"] = filters["status"]
            if filters.get("author_id"):
                query["author_id"] = filters["author_id"]
            if filters.get("categories"):
                query["categories"] = {"$in": filters["categories"]}
            if filters.get("tags"):
                query["tags"] = {"$in": filters["tags"]}
            if filters.get("search"):
                query["$text"] = {"$search": filters["search"]}
        
        # Get total count
        total_count = await self.blog_posts_collection.count_documents(query)
        
        # Get posts
        posts = await self.blog_posts_collection.find(query).sort("created_at", -1).skip(skip).limit(limit).to_list(length=None)
        
        # Get author information for posts
        author_ids = list(set([post["author_id"] for post in posts]))
        authors = await self.db.users.find(
            {"_id": {"$in": author_ids}},
            {"name": 1, "email": 1, "profile.avatar": 1}
        ).to_list(length=None)
        
        authors_dict = {author["_id"]: author for author in authors}
        
        # Add author info to posts
        for post in posts:
            post["author"] = authors_dict.get(post["author_id"], {})
        
        return {
            "posts": posts,
            "total_count": total_count,
            "has_next": (skip + limit) < total_count,
            "page": (skip // limit) + 1,
            "total_pages": (total_count + limit - 1) // limit
        }

    async def get_blog_post(self, post_id: Optional[str] = None, slug: Optional[str] = None) -> Optional[Dict[str, Any]]:
        """Get single blog post by ID or slug with real database operations"""
        if post_id:
            query = {"_id": post_id}
        elif slug:
            query = {"slug": slug}
        else:
            raise ValueError("Either post_id or slug must be provided")
        
        post = await self.blog_posts_collection.find_one(query)
        if not post:
            return None
        
        # Get author information
        author = await self.db.users.find_one(
            {"_id": post["author_id"]},
            {"name": 1, "email": 1, "profile.avatar": 1}
        )
        post["author"] = author or {}
        
        # Increment view count
        await self.blog_posts_collection.update_one(
            {"_id": post["_id"]},
            {"$inc": {"analytics.views": 1}}
        )
        
        return post

    async def update_blog_post(self, post_id: str, update_data: Dict[str, Any], user_id: str) -> Dict[str, Any]:
        """Update blog post with real database operations"""
        # Find post
        post = await self.blog_posts_collection.find_one({"_id": post_id})
        if not post:
            raise ValueError("Post not found")
        
        # Check permissions
        if post["author_id"] != user_id:
            # Check if user is admin
            user = await self.db.users.find_one({"_id": user_id})
            if not user or not user.get("is_admin", False):
                raise ValueError("Permission denied")
        
        # Prepare update document
        update_doc = {
            "$set": {
                "updated_at": datetime.utcnow()
            }
        }
        
        # Update allowed fields
        allowed_fields = ["title", "content", "excerpt", "status", "featured_image", "categories", "tags", "seo"]
        for field in allowed_fields:
            if field in update_data:
                if field == "title":
                    # Update slug if title changes
                    new_slug = self._generate_slug(update_data["title"])
                    if new_slug != post["slug"]:
                        # Check if new slug exists
                        existing_post = await self.blog_posts_collection.find_one({
                            "slug": new_slug,
                            "_id": {"$ne": post_id}
                        })
                        if existing_post:
                            new_slug = f"{new_slug}-{str(uuid.uuid4())[:8]}"
                        update_doc["$set"]["slug"] = new_slug
                    update_doc["$set"][field] = update_data[field]
                elif field == "seo":
                    # Merge SEO data
                    for key, value in update_data[field].items():
                        update_doc["$set"][f"seo.{key}"] = value
                else:
                    update_doc["$set"][field] = update_data[field]
        
        # Update published_at if status changes to published
        if update_data.get("status") == "published" and post["status"] != "published":
            update_doc["$set"]["published_at"] = datetime.utcnow()
        
        # Recalculate SEO score if content changed
        if "content" in update_data or "title" in update_data:
            updated_post = {**post, **update_data}
            update_doc["$set"]["seo.seo_score"] = self._calculate_seo_score(updated_post)
            update_doc["$set"]["analytics.reading_time"] = self._calculate_reading_time(updated_post["content"])
        
        # Update post
        result = await self.blog_posts_collection.update_one(
            {"_id": post_id},
            update_doc
        )
        
        if result.modified_count == 0:
            raise ValueError("No changes made")
        
        # Update categories and tags
        if "categories" in update_data or "tags" in update_data:
            await self._update_categories_and_tags(
                update_data.get("categories", post["categories"]),
                update_data.get("tags", post["tags"])
            )
        
        # Return updated post
        return await self.get_blog_post(post_id=post_id)

    async def delete_blog_post(self, post_id: str, user_id: str) -> Dict[str, Any]:
        """Delete blog post with real database operations"""
        # Find post
        post = await self.blog_posts_collection.find_one({"_id": post_id})
        if not post:
            raise ValueError("Post not found")
        
        # Check permissions
        if post["author_id"] != user_id:
            # Check if user is admin
            user = await self.db.users.find_one({"_id": user_id})
            if not user or not user.get("is_admin", False):
                raise ValueError("Permission denied")
        
        # Delete post
        result = await self.blog_posts_collection.delete_one({"_id": post_id})
        
        if result.deleted_count == 0:
            raise ValueError("Failed to delete post")
        
        return {
            "post_id": post_id,
            "deleted": True,
            "message": "Post deleted successfully"
        }

    async def get_blog_analytics(self, author_id: Optional[str] = None) -> Dict[str, Any]:
        """Get blog analytics with real database calculations"""
        # Build query
        query = {}
        if author_id:
            query["author_id"] = author_id
        
        # Get total stats
        total_posts = await self.blog_posts_collection.count_documents(query)
        published_posts = await self.blog_posts_collection.count_documents({**query, "status": "published"})
        
        # Get view statistics
        pipeline = [
            {"$match": query},
            {"$group": {
                "_id": None,
                "total_views": {"$sum": "$analytics.views"},
                "total_likes": {"$sum": "$analytics.likes"},
                "total_shares": {"$sum": "$analytics.shares"},
                "avg_seo_score": {"$avg": "$seo.seo_score"}
            }}
        ]
        
        stats = await self.blog_posts_collection.aggregate(pipeline).to_list(length=1)
        stats = stats[0] if stats else {"total_views": 0, "total_likes": 0, "total_shares": 0, "avg_seo_score": 0}
        
        # Get most popular posts
        popular_posts = await self.blog_posts_collection.find(
            query,
            {"title": 1, "slug": 1, "analytics.views": 1, "published_at": 1}
        ).sort("analytics.views", -1).limit(10).to_list(length=None)
        
        # Get recent posts
        recent_posts = await self.blog_posts_collection.find(
            query,
            {"title": 1, "slug": 1, "status": 1, "created_at": 1}
        ).sort("created_at", -1).limit(5).to_list(length=None)
        
        analytics_data = {
            "overview": {
                "total_posts": total_posts,
                "published_posts": published_posts,
                "draft_posts": total_posts - published_posts,
                "total_views": stats["total_views"],
                "total_likes": stats["total_likes"],
                "total_shares": stats["total_shares"],
                "avg_seo_score": round(stats.get("avg_seo_score", 0), 1)
            },
            "popular_posts": popular_posts,
            "recent_posts": recent_posts,
            "engagement_metrics": {
                "avg_views_per_post": round(stats["total_views"] / max(published_posts, 1), 1),
                "avg_likes_per_post": round(stats["total_likes"] / max(published_posts, 1), 1),
                "engagement_rate": round((stats["total_likes"] / max(stats["total_views"], 1)) * 100, 2)
            }
        }
        
        return analytics_data

    def _generate_slug(self, title: str) -> str:
        """Generate URL slug from title"""
        # Convert to lowercase and replace spaces with hyphens
        slug = re.sub(r'[^a-zA-Z0-9\s-]', '', title.lower())
        slug = re.sub(r'\s+', '-', slug.strip())
        return slug[:50]  # Limit length

    def _generate_excerpt(self, content: str, length: int = 200) -> str:
        """Generate excerpt from content"""
        # Remove HTML tags and get first N characters
        clean_content = re.sub(r'<[^>]+>', '', content)
        return clean_content[:length] + "..." if len(clean_content) > length else clean_content

    def _calculate_reading_time(self, content: str) -> int:
        """Calculate estimated reading time in minutes"""
        word_count = len(content.split())
        return max(1, round(word_count / 200))  # Assume 200 words per minute

    def _calculate_seo_score(self, post: Dict[str, Any]) -> int:
        """Calculate SEO score for post"""
        score = 0
        
        # Title length (optimal 30-60 characters)
        title_len = len(post["title"])
        if 30 <= title_len <= 60:
            score += 20
        elif 20 <= title_len < 80:
            score += 10
        
        # Content length (optimal 300+ words)
        word_count = len(post["content"].split())
        if word_count >= 300:
            score += 20
        elif word_count >= 150:
            score += 10
        
        # Meta description
        if post["seo"].get("meta_description") and 120 <= len(post["seo"]["meta_description"]) <= 160:
            score += 15
        
        # Focus keyword
        if post["seo"].get("focus_keyword"):
            score += 15
            # Check if keyword is in title
            if post["seo"]["focus_keyword"].lower() in post["title"].lower():
                score += 10
        
        # Tags and categories
        if post.get("tags"):
            score += 10
        if post.get("categories"):
            score += 10
        
        return min(score, 100)  # Cap at 100

    async def _update_categories_and_tags(self, categories: List[str], tags: List[str]):
        """Update categories and tags collections"""
        # Update categories
        for category in categories:
            await self.categories_collection.update_one(
                {"name": category},
                {
                    "$set": {"name": category, "updated_at": datetime.utcnow()},
                    "$inc": {"post_count": 1},
                    "$setOnInsert": {"created_at": datetime.utcnow()}
                },
                upsert=True
            )
        
        # Update tags
        for tag in tags:
            await self.tags_collection.update_one(
                {"name": tag},
                {
                    "$set": {"name": tag, "updated_at": datetime.utcnow()},
                    "$inc": {"post_count": 1},
                    "$setOnInsert": {"created_at": datetime.utcnow()}
                },
                upsert=True
            )

# Create service instance function (dependency injection)
def get_content_service() -> ContentService:
    return ContentService()