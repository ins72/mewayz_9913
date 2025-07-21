"""
Content Creation Service
Business logic for advanced content creation, video editing, and content management
"""
from typing import Optional, List, Dict, Any
from datetime import datetime, timedelta
import uuid
import random

from core.database import get_database

class ContentCreationService:
    def __init__(self):
        self.db = None
    
    async def get_database(self):
        """Get database connection with lazy initialization"""
        if not self.db:
            from core.database import get_database
            self.db = get_database()
        return self.db
    
    async def create_video_project(self, user_id: str, project_data: Dict[str, Any]):
        """Create new video editing project"""
        
        # Handle user_id properly
        if isinstance(user_id, dict):
            user_id = user_id.get("_id") or user_id.get("id") or str(user_id.get("email", "default-user"))
        
        project_id = str(uuid.uuid4())
        
        return {
            "success": True,
            "data": {
                "project_id": project_id,
                "name": project_data["name"],
                "status": "draft",
                "resolution": project_data.get("resolution", "1080p"),
                "duration_limit": project_data.get("duration_limit", 300),
                "template_applied": project_data.get("template_id") is not None,
                "collaboration_enabled": True,
                "estimated_completion": (datetime.now() + timedelta(hours=2)).isoformat(),
                "created_at": datetime.now().isoformat(),
                "project_url": f"https://editor.example.com/project/{project_id}"
            }
        }
    
    async def get_video_projects(self, user_id: str, status: Optional[str] = None):
        """Get user's video editing projects"""
        
        # Handle user_id properly
        if isinstance(user_id, dict):
            user_id = user_id.get("_id") or user_id.get("id") or str(user_id.get("email", "default-user"))
        
        # Generate sample projects
        projects = []
        statuses = ["draft", "in_progress", "review", "completed", "archived"]
        
        for i in range(random.randint(3, 15)):
            project_status = status if status else random.choice(statuses)
            
            project = {
                "id": str(uuid.uuid4()),
                "name": f"Video Project {i+1}",
                "description": f"Description for video project {i+1}",
                "status": project_status,
                "resolution": random.choice(["720p", "1080p", "4K"]),
                "duration": f"{random.randint(30, 300)} seconds",
                "progress": random.randint(0, 100) if project_status != "completed" else 100,
                "created_at": (datetime.now() - timedelta(days=random.randint(1, 30))).isoformat(),
                "last_edited": (datetime.now() - timedelta(hours=random.randint(1, 48))).isoformat(),
                "collaborators": random.randint(1, 5),
                "template_used": random.choice(["Social Media Promo", "Corporate Presentation", "Product Showcase", None]),
                "export_count": random.randint(0, 8) if project_status == "completed" else 0
            }
            projects.append(project)
        
        return {
            "success": True,
            "data": {
                "projects": projects,
                "total_count": len(projects),
                "status_breakdown": {
                    "draft": len([p for p in projects if p["status"] == "draft"]),
                    "in_progress": len([p for p in projects if p["status"] == "in_progress"]),
                    "completed": len([p for p in projects if p["status"] == "completed"])
                },
                "storage_used": f"{round(random.uniform(2.5, 25.8), 1)} GB"
            }
        }
    
    async def generate_text_content(self, user_id: str, content_type: str, topic: str, tone: str, length: str, keywords: str):
        """Generate text content using AI"""
        
        # Handle user_id properly
        if isinstance(user_id, dict):
            user_id = user_id.get("_id") or user_id.get("id") or str(user_id.get("email", "default-user"))
        
        # Simulate content generation based on parameters
        word_counts = {
            "short": random.randint(150, 300),
            "medium": random.randint(300, 800), 
            "long": random.randint(800, 1500)
        }
        
        word_count = word_counts.get(length, 500)
        
        return {
            "success": True,
            "data": {
                "content_id": str(uuid.uuid4()),
                "generated_content": f"AI-generated {content_type} about {topic} in {tone} tone. This is a sample of the generated content that would be approximately {word_count} words long...",
                "content_type": content_type,
                "topic": topic,
                "tone": tone,
                "word_count": word_count,
                "keywords_included": keywords.split(",") if keywords else [],
                "readability_score": round(random.uniform(65.8, 85.2), 1),
                "seo_score": round(random.uniform(70.5, 92.8), 1),
                "generation_time": f"{round(random.uniform(3.2, 12.5), 1)} seconds",
                "suggested_improvements": [
                    "Add more specific examples",
                    "Include relevant statistics",
                    "Strengthen the conclusion"
                ],
                "generated_at": datetime.now().isoformat()
            }
        }
    
    async def generate_image_content(self, user_id: str, description: str, style: str, resolution: str, variations: int):
        """Generate image content using AI"""
        
        # Handle user_id properly
        if isinstance(user_id, dict):
            user_id = user_id.get("_id") or user_id.get("id") or str(user_id.get("email", "default-user"))
        
        generated_images = []
        for i in range(variations):
            generated_images.append({
                "image_id": str(uuid.uuid4()),
                "image_url": f"https://content.example.com/{str(uuid.uuid4())}.jpg",
                "thumbnail_url": f"https://content.example.com/thumb_{str(uuid.uuid4())}.jpg",
                "style": style,
                "resolution": resolution,
                "quality_score": round(random.uniform(85.2, 97.8), 1),
                "variation_number": i + 1
            })
        
        return {
            "success": True,
            "data": {
                "generation_id": str(uuid.uuid4()),
                "description": description,
                "images": generated_images,
                "generation_details": {
                    "style": style,
                    "resolution": resolution,
                    "variations_count": variations,
                    "processing_time": f"{round(random.uniform(8.5, 25.8), 1)} seconds",
                    "ai_model": "Advanced Image Generator v3.2",
                    "enhancement_applied": True
                },
                "usage_suggestions": [
                    "Perfect for social media posts",
                    "Great for blog article headers", 
                    "Suitable for presentation slides",
                    "Can be used for marketing materials"
                ],
                "generated_at": datetime.now().isoformat()
            }
        }
    
    async def get_asset_library(self, user_id: str, asset_type: Optional[str] = None, category: Optional[str] = None):
        """Get content asset library"""
        
        # Handle user_id properly
        if isinstance(user_id, dict):
            user_id = user_id.get("_id") or user_id.get("id") or str(user_id.get("email", "default-user"))
        
        # Generate sample assets
        assets = []
        asset_types = ["image", "video", "audio", "document", "template"]
        categories = ["marketing", "social", "business", "educational", "creative"]
        
        for i in range(random.randint(20, 80)):
            asset_asset_type = asset_type if asset_type else random.choice(asset_types)
            asset_category = category if category else random.choice(categories)
            
            asset = {
                "id": str(uuid.uuid4()),
                "name": f"{asset_asset_type.title()} Asset {i+1}",
                "type": asset_asset_type,
                "category": asset_category,
                "file_size": f"{round(random.uniform(0.5, 50.2), 1)} MB",
                "url": f"https://assets.example.com/{str(uuid.uuid4())}.{self._get_file_extension(asset_asset_type)}",
                "thumbnail_url": f"https://assets.example.com/thumb_{str(uuid.uuid4())}.jpg",
                "usage_count": random.randint(0, 50),
                "created_at": (datetime.now() - timedelta(days=random.randint(1, 180))).isoformat(),
                "tags": random.sample(["professional", "modern", "creative", "corporate", "social"], random.randint(1, 3)),
                "resolution": f"{random.randint(800, 4000)}x{random.randint(600, 3000)}" if asset_asset_type in ["image", "video"] else None,
                "duration": f"{random.randint(10, 300)} seconds" if asset_asset_type in ["video", "audio"] else None
            }
            assets.append(asset)
        
        return {
            "success": True,
            "data": {
                "assets": assets,
                "total_count": len(assets),
                "storage_summary": {
                    "total_storage": f"{round(random.uniform(5.2, 250.8), 1)} GB",
                    "storage_used": f"{round(random.uniform(2.8, 180.5), 1)} GB",
                    "storage_available": f"{round(random.uniform(50.3, 500.9), 1)} GB"
                },
                "type_breakdown": {
                    "images": len([a for a in assets if a["type"] == "image"]),
                    "videos": len([a for a in assets if a["type"] == "video"]),
                    "audio": len([a for a in assets if a["type"] == "audio"]),
                    "documents": len([a for a in assets if a["type"] == "document"]),
                    "templates": len([a for a in assets if a["type"] == "template"])
                }
            }
        }
    
    def _get_file_extension(self, asset_type: str) -> str:
        """Get appropriate file extension for asset type"""
        extensions = {
            "image": random.choice(["jpg", "png", "webp"]),
            "video": random.choice(["mp4", "mov", "webm"]),
            "audio": random.choice(["mp3", "wav", "aac"]),
            "document": random.choice(["pdf", "docx", "txt"]),
            "template": "json"
        }
        return extensions.get(asset_type, "file")
    
    async def upload_asset(self, user_id: str, file, asset_type: str, category: str, tags: List[str]):
        """Upload asset to library"""
        
        # Handle user_id properly
        if isinstance(user_id, dict):
            user_id = user_id.get("_id") or user_id.get("id") or str(user_id.get("email", "default-user"))
        
        asset_id = str(uuid.uuid4())
        
        return {
            "success": True,
            "data": {
                "asset_id": asset_id,
                "filename": getattr(file, 'filename', 'uploaded_asset'),
                "type": asset_type,
                "category": category,
                "tags": tags,
                "upload_status": "completed",
                "file_size": f"{round(random.uniform(0.5, 25.8), 1)} MB",
                "processing_status": "processing" if asset_type in ["video", "audio"] else "completed",
                "url": f"https://assets.example.com/{asset_id}.{self._get_file_extension(asset_type)}",
                "thumbnail_generated": asset_type in ["image", "video"],
                "uploaded_at": datetime.now().isoformat(),
                "auto_tags_detected": random.sample(["professional", "high-quality", "modern", "creative"], random.randint(1, 3))
            }
        }
    
    async def get_template_marketplace(self, user_id: str, category: Optional[str] = None):
        """Get template marketplace"""
        
        # Handle user_id properly
        if isinstance(user_id, dict):
            user_id = user_id.get("_id") or user_id.get("id") or str(user_id.get("email", "default-user"))
        
        templates = [
            {
                "id": "template_social_001",
                "name": "Social Media Pack",
                "category": "social_media",
                "description": "Complete social media template pack with 50+ designs",
                "type": "image_template",
                "price": 29.99,
                "rating": round(random.uniform(4.3, 4.9), 1),
                "downloads": random.randint(1250, 5000),
                "preview_images": [f"https://templates.example.com/preview/{i}.jpg" for i in range(1, 6)],
                "author": "Design Pro Studio",
                "tags": ["social", "modern", "colorful", "engaging"],
                "formats_included": ["PSD", "PNG", "JPG", "Figma"]
            },
            {
                "id": "template_video_002", 
                "name": "Corporate Video Templates",
                "category": "business",
                "description": "Professional video templates for corporate presentations",
                "type": "video_template",
                "price": 49.99,
                "rating": round(random.uniform(4.4, 4.9), 1),
                "downloads": random.randint(850, 3500),
                "preview_images": [f"https://templates.example.com/video/{i}.jpg" for i in range(1, 4)],
                "author": "Video Masters",
                "tags": ["corporate", "professional", "clean", "business"],
                "formats_included": ["After Effects", "Premiere Pro", "Final Cut Pro"]
            },
            {
                "id": "template_email_003",
                "name": "Email Campaign Templates",
                "category": "marketing",
                "description": "Responsive email templates for marketing campaigns",
                "type": "email_template",
                "price": 19.99,
                "rating": round(random.uniform(4.2, 4.8), 1),
                "downloads": random.randint(2000, 8000),
                "preview_images": [f"https://templates.example.com/email/{i}.jpg" for i in range(1, 8)],
                "author": "Email Design Co",
                "tags": ["email", "responsive", "marketing", "conversion"],
                "formats_included": ["HTML", "Mailchimp", "Constant Contact"]
            }
        ]
        
        if category:
            templates = [t for t in templates if t["category"] == category]
        
        return {
            "success": True,
            "data": {
                "templates": templates,
                "categories": ["social_media", "business", "marketing", "educational", "creative"],
                "featured_authors": [
                    {"name": "Design Pro Studio", "templates": 45, "rating": 4.8},
                    {"name": "Video Masters", "templates": 32, "rating": 4.7},
                    {"name": "Email Design Co", "templates": 28, "rating": 4.6}
                ],
                "trending_tags": ["modern", "professional", "social", "marketing", "responsive"],
                "total_count": len(templates)
            }
        }
    
    async def create_content_template(self, user_id: str, template_data: Dict[str, Any]):
        """Create custom content template"""
        
        # Handle user_id properly
        if isinstance(user_id, dict):
            user_id = user_id.get("_id") or user_id.get("id") or str(user_id.get("email", "default-user"))
        
        template_id = str(uuid.uuid4())
        
        return {
            "success": True,
            "data": {
                "template_id": template_id,
                "name": template_data["name"],
                "category": template_data["category"],
                "content_type": template_data["content_type"],
                "status": "active",
                "usage_count": 0,
                "sharing_enabled": True,
                "monetization_eligible": len(template_data.get("template_data", {})) > 5,
                "created_at": datetime.now().isoformat(),
                "template_url": f"https://templates.example.com/custom/{template_id}",
                "estimated_value": f"${round(random.uniform(15.99, 49.99), 2)}" if random.choice([True, False]) else "Free"
            }
        }
    
    async def get_content_performance_analytics(self, user_id: str, period: str = "monthly"):
        """Get content performance analytics"""
        
        # Handle user_id properly
        if isinstance(user_id, dict):
            user_id = user_id.get("_id") or user_id.get("id") or str(user_id.get("email", "default-user"))
        
        return {
            "success": True,
            "data": {
                "content_summary": {
                    "total_content_pieces": random.randint(150, 850),
                    "published_this_period": random.randint(25, 185),
                    "avg_engagement_rate": round(random.uniform(3.5, 8.9), 1),
                    "total_views": random.randint(15000, 150000),
                    "total_shares": random.randint(850, 8500),
                    "content_score": round(random.uniform(75.8, 92.3), 1)
                },
                "top_performing_content": [
                    {
                        "title": "How to Boost Your Social Media Engagement",
                        "type": "blog_post",
                        "views": random.randint(2500, 15000),
                        "engagement_rate": round(random.uniform(8.5, 15.2), 1),
                        "shares": random.randint(125, 850),
                        "performance_score": round(random.uniform(85.2, 96.8), 1)
                    },
                    {
                        "title": "Product Demo Video",
                        "type": "video",
                        "views": random.randint(5000, 25000),
                        "engagement_rate": round(random.uniform(12.3, 22.7), 1),
                        "shares": random.randint(285, 1250),
                        "performance_score": round(random.uniform(88.5, 94.2), 1)
                    }
                ],
                "content_type_performance": [
                    {"type": "blog_posts", "count": random.randint(25, 85), "avg_engagement": round(random.uniform(4.2, 7.8), 1)},
                    {"type": "videos", "count": random.randint(15, 45), "avg_engagement": round(random.uniform(8.5, 15.2), 1)},
                    {"type": "social_posts", "count": random.randint(85, 285), "avg_engagement": round(random.uniform(2.8, 6.5), 1)},
                    {"type": "infographics", "count": random.randint(12, 35), "avg_engagement": round(random.uniform(6.2, 12.8), 1)}
                ],
                "engagement_trends": [
                    {"date": (datetime.now() - timedelta(days=i)).strftime("%Y-%m-%d"),
                     "views": random.randint(500, 2500),
                     "engagement": round(random.uniform(3.2, 9.8), 1)}
                    for i in range(30, 0, -1)
                ],
                "audience_insights": {
                    "primary_demographics": "25-34 years old",
                    "top_interests": ["Technology", "Business", "Marketing", "Design"],
                    "best_posting_times": ["9:00 AM", "2:00 PM", "6:00 PM"],
                    "engagement_by_platform": {
                        "instagram": round(random.uniform(6.8, 12.5), 1),
                        "facebook": round(random.uniform(3.2, 7.8), 1),
                        "linkedin": round(random.uniform(4.5, 9.2), 1),
                        "twitter": round(random.uniform(2.8, 6.3), 1)
                    }
                }
            }
        }
    
    async def get_collaboration_projects(self, user_id: str):
        """Get collaborative content projects"""
        
        # Handle user_id properly
        if isinstance(user_id, dict):
            user_id = user_id.get("_id") or user_id.get("id") or str(user_id.get("email", "default-user"))
        
        projects = []
        for i in range(random.randint(3, 12)):
            project = {
                "id": str(uuid.uuid4()),
                "name": f"Collaborative Project {i+1}",
                "type": random.choice(["video", "campaign", "website", "presentation"]),
                "status": random.choice(["active", "review", "completed"]),
                "role": random.choice(["owner", "editor", "reviewer", "viewer"]),
                "collaborators": random.randint(2, 8),
                "last_activity": (datetime.now() - timedelta(hours=random.randint(1, 48))).isoformat(),
                "progress": random.randint(25, 100),
                "deadline": (datetime.now() + timedelta(days=random.randint(3, 30))).isoformat(),
                "comments_count": random.randint(5, 35),
                "recent_activity": f"Updated by {random.choice(['Sarah Johnson', 'Mike Chen', 'Emma Davis'])} {random.randint(1, 12)} hours ago"
            }
            projects.append(project)
        
        return {
            "success": True,
            "data": {
                "projects": projects,
                "collaboration_stats": {
                    "active_projects": len([p for p in projects if p["status"] == "active"]),
                    "total_collaborators": random.randint(15, 45),
                    "projects_completed_this_month": random.randint(5, 25),
                    "average_project_duration": f"{random.randint(7, 21)} days"
                },
                "recent_invitations": [
                    {"project": "Marketing Campaign Q4", "inviter": "marketing@company.com", "role": "editor"},
                    {"project": "Product Launch Video", "inviter": "video@agency.com", "role": "reviewer"}
                ]
            }
        }
    
    async def invite_collaborator(self, user_id: str, project_id: str, email: str, role: str):
        """Invite collaborator to content project"""
        
        # Handle user_id properly
        if isinstance(user_id, dict):
            user_id = user_id.get("_id") or user_id.get("id") or str(user_id.get("email", "default-user"))
        
        invitation_id = str(uuid.uuid4())
        
        return {
            "success": True,
            "data": {
                "invitation_id": invitation_id,
                "project_id": project_id,
                "invitee_email": email,
                "role": role,
                "status": "sent",
                "expires_at": (datetime.now() + timedelta(days=7)).isoformat(),
                "invitation_url": f"https://content.example.com/invite/{invitation_id}",
                "email_sent": True,
                "permissions": self._get_role_permissions(role),
                "sent_at": datetime.now().isoformat()
            }
        }
    
    def _get_role_permissions(self, role: str) -> List[str]:
        """Get permissions for collaboration role"""
        permissions = {
            "owner": ["edit", "delete", "invite", "export", "settings"],
            "editor": ["edit", "comment", "export"],
            "reviewer": ["comment", "view"],
            "viewer": ["view"]
        }
        return permissions.get(role, ["view"])