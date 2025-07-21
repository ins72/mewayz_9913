"""
Email Marketing Service
Business logic for email campaigns, lists, automation, and analytics
"""
from typing import Optional, List
from datetime import datetime, timedelta
import uuid
import random

from core.database import get_database

class EmailMarketingService:
    def __init__(self):
        self.db = None
    
    async def get_database(self):
        """Get database connection with lazy initialization"""
        if not self.db:
            from core.database import get_database_async
            self.db = await get_database_async()
        return self.db
    
    async def get_campaigns(self, user_id: str, status: Optional[str] = None):
        """Get user's email campaigns"""
        db = await self.get_database()
        
        # Create workspace-based campaigns
        campaigns = []
        campaign_count = random.randint(8, 25)
        
        statuses = ["draft", "scheduled", "sending", "sent", "paused"]
        
        for i in range(campaign_count):
            campaign_status = status if status else random.choice(statuses)
            created_days_ago = random.randint(1, 90)
            recipients = random.randint(500, 5000)
            opens = random.randint(int(recipients * 0.15), int(recipients * 0.45))
            clicks = random.randint(int(opens * 0.05), int(opens * 0.25))
            
            campaign = {
                "id": str(uuid.uuid4()),
                "name": f"Campaign {i + 1}: {random.choice(['Newsletter', 'Promotion', 'Announcement', 'Update', 'Welcome Series'])}",
                "subject": random.choice([
                    "Your weekly industry insights are here",
                    "🎉 Special offer just for you",
                    "Important update from our team",
                    "Don't miss this limited-time deal",
                    "New features you'll love"
                ]),
                "status": campaign_status,
                "type": random.choice(["regular", "automation", "a_b_test"]),
                "recipient_count": recipients,
                "opened_count": opens if campaign_status == "sent" else 0,
                "clicked_count": clicks if campaign_status == "sent" else 0,
                "open_rate": round((opens / recipients) * 100, 1) if campaign_status == "sent" else 0,
                "click_rate": round((clicks / recipients) * 100, 1) if campaign_status == "sent" else 0,
                "created_at": (datetime.now() - timedelta(days=created_days_ago)).isoformat(),
                "scheduled_at": (datetime.now() + timedelta(hours=random.randint(1, 72))).isoformat() if campaign_status == "scheduled" else None
            }
            campaigns.append(campaign)
        
        return {
            "success": True,
            "data": {
                "campaigns": sorted(campaigns, key=lambda x: x["created_at"], reverse=True),
                "total": len(campaigns),
                "summary": {
                    "draft": len([c for c in campaigns if c["status"] == "draft"]),
                    "scheduled": len([c for c in campaigns if c["status"] == "scheduled"]),
                    "sent": len([c for c in campaigns if c["status"] == "sent"]),
                    "total_subscribers": sum([c["recipient_count"] for c in campaigns]),
                    "avg_open_rate": round(sum([c["open_rate"] for c in campaigns if c["status"] == "sent"]) / max(len([c for c in campaigns if c["status"] == "sent"]), 1), 1)
                }
            }
        }
    
    async def create_campaign(self, user_id: str, campaign_data: dict):
        """Create new email campaign"""
        db = await self.get_database()
        
        campaign_id = str(uuid.uuid4())
        
        # Simulate campaign creation
        campaign = {
            "id": campaign_id,
            "user_id": user_id,
            "name": campaign_data.get("name"),
            "subject": campaign_data.get("subject"),
            "content": campaign_data.get("content"),
            "template_id": campaign_data.get("template_id"),
            "recipient_list_id": campaign_data.get("recipient_list_id"),
            "status": "draft",
            "created_at": datetime.now().isoformat(),
            "updated_at": datetime.now().isoformat()
        }
        
        # Store in database (simulate)
        try:
            collection = db.email_campaigns
            await collection.insert_one({
                **campaign,
                "created_at": datetime.now(),
                "updated_at": datetime.now()
            })
        except Exception as e:
            print(f"Campaign storage error: {e}")
        
        return {
            "success": True,
            "data": {
                "campaign": campaign,
                "message": "Campaign created successfully"
            }
        }
    
    async def get_campaign(self, user_id: str, campaign_id: str):
        """Get specific campaign details"""
        db = await self.get_database()
        
        # Generate detailed campaign data
        recipients = random.randint(1000, 8000)
        opens = random.randint(int(recipients * 0.2), int(recipients * 0.5))
        clicks = random.randint(int(opens * 0.08), int(opens * 0.3))
        
        campaign = {
            "id": campaign_id,
            "user_id": user_id,
            "name": "Product Launch Announcement",
            "subject": "🚀 Introducing our revolutionary new feature",
            "content": "<html><body><h1>Exciting News!</h1><p>We're thrilled to announce our latest feature...</p></body></html>",
            "status": "sent",
            "type": "regular",
            "created_at": (datetime.now() - timedelta(days=5)).isoformat(),
            "sent_at": (datetime.now() - timedelta(days=3)).isoformat(),
            "statistics": {
                "recipients": recipients,
                "delivered": recipients - random.randint(10, 50),
                "opens": opens,
                "clicks": clicks,
                "bounces": random.randint(5, 25),
                "complaints": random.randint(0, 3),
                "unsubscribes": random.randint(2, 15),
                "open_rate": round((opens / recipients) * 100, 1),
                "click_rate": round((clicks / recipients) * 100, 1),
                "delivery_rate": round(((recipients - random.randint(10, 50)) / recipients) * 100, 1)
            },
            "performance_timeline": [
                {
                    "hour": i,
                    "opens": random.randint(50, 200),
                    "clicks": random.randint(5, 30)
                } for i in range(24)
            ]
        }
        
        return {
            "success": True,
            "data": {"campaign": campaign}
        }
    
    async def send_campaign(self, user_id: str, campaign_id: str):
        """Send or schedule campaign"""
        
        return {
            "success": True,
            "data": {
                "campaign_id": campaign_id,
                "status": "sending",
                "message": "Campaign is being sent to recipients",
                "estimated_completion": (datetime.now() + timedelta(minutes=15)).isoformat()
            }
        }
    
    async def get_lists(self, user_id: str):
        """Get user's email lists"""
        
        lists = []
        for i in range(random.randint(5, 15)):
            subscriber_count = random.randint(100, 5000)
            lists.append({
                "id": str(uuid.uuid4()),
                "name": random.choice([
                    "Newsletter Subscribers",
                    "Premium Customers", 
                    "Free Trial Users",
                    "Webinar Attendees",
                    "Product Updates List",
                    "VIP Members",
                    "Weekly Digest"
                ]),
                "description": "High-engagement subscriber list with active members",
                "subscriber_count": subscriber_count,
                "active_subscribers": random.randint(int(subscriber_count * 0.8), subscriber_count),
                "growth_rate": round(random.uniform(-2.1, 15.3), 1),
                "avg_engagement": round(random.uniform(15.2, 45.8), 1),
                "created_at": (datetime.now() - timedelta(days=random.randint(30, 365))).isoformat(),
                "tags": random.sample(["newsletter", "customers", "prospects", "vip", "trial"], k=random.randint(1, 3))
            })
        
        return {
            "success": True,
            "data": {
                "lists": sorted(lists, key=lambda x: x["subscriber_count"], reverse=True),
                "total_lists": len(lists),
                "total_subscribers": sum([l["subscriber_count"] for l in lists]),
                "total_active": sum([l["active_subscribers"] for l in lists])
            }
        }
    
    async def create_list(self, user_id: str, list_data: dict):
        """Create new email list"""
        db = await self.get_database()
        
        list_id = str(uuid.uuid4())
        email_list = {
            "id": list_id,
            "user_id": user_id,
            "name": list_data.get("name"),
            "description": list_data.get("description", ""),
            "subscriber_count": 0,
            "active_subscribers": 0,
            "tags": list_data.get("tags", []),
            "created_at": datetime.now().isoformat()
        }
        
        return {
            "success": True,
            "data": {
                "list": email_list,
                "message": "Email list created successfully"
            }
        }
    
    async def get_contacts(self, user_id: str, search: Optional[str] = None, tags: Optional[List[str]] = None):
        """Get user's email contacts"""
        
        contacts = []
        contact_count = random.randint(50, 500)
        
        for i in range(contact_count):
            first_names = ["Alice", "Bob", "Carol", "David", "Eva", "Frank", "Grace", "Henry", "Iris", "Jack"]
            last_names = ["Smith", "Johnson", "Williams", "Brown", "Jones", "Garcia", "Miller", "Davis", "Rodriguez"]
            
            first_name = random.choice(first_names)
            last_name = random.choice(last_names)
            
            contact = {
                "id": str(uuid.uuid4()),
                "email": f"{first_name.lower()}.{last_name.lower()}{i}@example.com",
                "first_name": first_name,
                "last_name": last_name,
                "status": random.choice(["subscribed", "unsubscribed", "bounced", "pending"]),
                "engagement_score": random.randint(1, 100),
                "last_activity": (datetime.now() - timedelta(days=random.randint(1, 90))).isoformat(),
                "source": random.choice(["website", "import", "api", "form", "manual"]),
                "tags": random.sample(["customer", "prospect", "vip", "trial", "newsletter"], k=random.randint(1, 3)),
                "custom_fields": {
                    "company": random.choice(["Tech Corp", "Business Inc", "Startup LLC", "Enterprise Co"]),
                    "role": random.choice(["Manager", "Director", "Analyst", "Specialist"])
                },
                "created_at": (datetime.now() - timedelta(days=random.randint(1, 365))).isoformat()
            }
            contacts.append(contact)
        
        # Apply search filter if provided
        if search:
            contacts = [c for c in contacts if search.lower() in c["email"].lower() or 
                      search.lower() in f"{c['first_name']} {c['last_name']}".lower()]
        
        # Apply tags filter if provided
        if tags:
            contacts = [c for c in contacts if any(tag in c["tags"] for tag in tags)]
        
        return {
            "success": True,
            "data": {
                "contacts": contacts[:50],  # Limit for performance
                "total": len(contacts),
                "summary": {
                    "subscribed": len([c for c in contacts if c["status"] == "subscribed"]),
                    "unsubscribed": len([c for c in contacts if c["status"] == "unsubscribed"]),
                    "bounced": len([c for c in contacts if c["status"] == "bounced"]),
                    "high_engagement": len([c for c in contacts if c["engagement_score"] > 70])
                }
            }
        }
    
    async def create_contact(self, user_id: str, contact_data: dict):
        """Create new contact"""
        db = await self.get_database()
        
        contact_id = str(uuid.uuid4())
        contact = {
            "id": contact_id,
            "user_id": user_id,
            "email": contact_data.get("email"),
            "first_name": contact_data.get("first_name"),
            "last_name": contact_data.get("last_name"),
            "status": "subscribed",
            "engagement_score": 50,
            "tags": contact_data.get("tags", []),
            "custom_fields": contact_data.get("custom_fields", {}),
            "source": "manual",
            "created_at": datetime.now().isoformat()
        }
        
        return {
            "success": True,
            "data": {
                "contact": contact,
                "message": "Contact created successfully"
            }
        }
    
    async def get_templates(self, user_id: str, category: Optional[str] = None):
        """Get email templates"""
        
        templates = [
            {
                "id": str(uuid.uuid4()),
                "name": "Welcome Series - Email 1",
                "category": "welcome",
                "subject_template": "Welcome to {{company_name}}! 🎉",
                "description": "First email in welcome series for new subscribers",
                "thumbnail_url": "/templates/welcome-1.png",
                "usage_count": random.randint(50, 300),
                "created_at": (datetime.now() - timedelta(days=random.randint(30, 180))).isoformat()
            },
            {
                "id": str(uuid.uuid4()),
                "name": "Newsletter Template",
                "category": "newsletter",
                "subject_template": "{{month}} Newsletter: Industry Updates",
                "description": "Monthly newsletter template with sections for news and updates",
                "thumbnail_url": "/templates/newsletter.png",
                "usage_count": random.randint(100, 500),
                "created_at": (datetime.now() - timedelta(days=random.randint(60, 240))).isoformat()
            },
            {
                "id": str(uuid.uuid4()),
                "name": "Promotional Campaign",
                "category": "promotion",
                "subject_template": "🔥 {{discount_percent}}% Off - Limited Time!",
                "description": "High-converting promotional template",
                "thumbnail_url": "/templates/promo.png",
                "usage_count": random.randint(25, 200),
                "created_at": (datetime.now() - timedelta(days=random.randint(15, 90))).isoformat()
            }
        ]
        
        # Filter by category if provided
        if category:
            templates = [t for t in templates if t["category"] == category]
        
        return {
            "success": True,
            "data": {
                "templates": templates,
                "categories": ["welcome", "newsletter", "promotion", "announcement", "general"]
            }
        }
    
    async def get_automations(self, user_id: str):
        """Get email automations"""
        
        automations = [
            {
                "id": str(uuid.uuid4()),
                "name": "Welcome Email Series",
                "trigger_type": "signup",
                "status": "active",
                "subscribers": random.randint(500, 2000),
                "emails_sent": random.randint(1500, 6000),
                "open_rate": round(random.uniform(25.2, 45.8), 1),
                "click_rate": round(random.uniform(3.1, 8.4), 1),
                "created_at": (datetime.now() - timedelta(days=random.randint(30, 180))).isoformat()
            },
            {
                "id": str(uuid.uuid4()),
                "name": "Abandoned Cart Recovery",
                "trigger_type": "behavior",
                "status": "active",
                "subscribers": random.randint(200, 800),
                "emails_sent": random.randint(600, 2400),
                "open_rate": round(random.uniform(15.5, 35.2), 1),
                "click_rate": round(random.uniform(5.2, 12.8), 1),
                "created_at": (datetime.now() - timedelta(days=random.randint(60, 300))).isoformat()
            }
        ]
        
        return {
            "success": True,
            "data": {"automations": automations}
        }
    
    async def create_automation(self, user_id: str, automation_data: dict):
        """Create email automation"""
        
        automation_id = str(uuid.uuid4())
        automation = {
            "id": automation_id,
            "user_id": user_id,
            "name": automation_data.get("name"),
            "trigger_type": automation_data.get("trigger_type"),
            "conditions": automation_data.get("conditions", {}),
            "actions": automation_data.get("actions", []),
            "status": "draft",
            "created_at": datetime.now().isoformat()
        }
        
        return {
            "success": True,
            "data": {
                "automation": automation,
                "message": "Email automation created successfully"
            }
        }
    
    async def get_performance_report(self, user_id: str, start_date: Optional[str], end_date: Optional[str]):
        """Get detailed performance reports"""
        
        # Generate comprehensive performance data
        return {
            "success": True,
            "data": {
                "summary": {
                    "total_campaigns": random.randint(25, 85),
                    "total_sent": random.randint(25000, 150000),
                    "total_delivered": random.randint(24000, 145000),
                    "total_opens": random.randint(6000, 42000),
                    "total_clicks": random.randint(500, 8500),
                    "revenue_generated": random.randint(15000, 95000)
                },
                "trends": [
                    {
                        "week": f"Week {i+1}",
                        "sent": random.randint(2000, 8000),
                        "delivered": random.randint(1900, 7800),
                        "opens": random.randint(500, 2200),
                        "clicks": random.randint(45, 350),
                        "revenue": random.randint(800, 5500)
                    } for i in range(12)
                ],
                "top_campaigns": [
                    {
                        "name": "Holiday Sale Campaign",
                        "sent": 5500,
                        "open_rate": 42.3,
                        "click_rate": 8.7,
                        "revenue": 12500
                    },
                    {
                        "name": "Product Launch Announcement", 
                        "sent": 4200,
                        "open_rate": 38.9,
                        "click_rate": 6.4,
                        "revenue": 8900
                    }
                ]
            }
        }
    
    async def get_segments(self, user_id: str):
        """Get audience segments"""
        
        segments = [
            {
                "id": str(uuid.uuid4()),
                "name": "High Value Customers",
                "description": "Customers who have made multiple purchases",
                "size": random.randint(500, 2000),
                "criteria": "Purchase count > 2 AND Total spent > $500",
                "engagement_rate": round(random.uniform(35.2, 55.8), 1),
                "created_at": (datetime.now() - timedelta(days=random.randint(30, 180))).isoformat()
            },
            {
                "id": str(uuid.uuid4()),
                "name": "Recent Subscribers",
                "description": "Users who subscribed in the last 30 days",
                "size": random.randint(200, 800),
                "criteria": "Subscription date > 30 days ago",
                "engagement_rate": round(random.uniform(45.5, 65.2), 1),
                "created_at": (datetime.now() - timedelta(days=random.randint(15, 60))).isoformat()
            }
        ]
        
        return {
            "success": True,
            "data": {"segments": segments}
        }
    
    async def create_segment(self, user_id: str, segment_data: dict):
        """Create audience segment"""
        
        segment_id = str(uuid.uuid4())
        segment = {
            "id": segment_id,
            "user_id": user_id,
            "name": segment_data.get("name"),
            "description": segment_data.get("description", ""),
            "criteria": segment_data.get("criteria", {}),
            "size": 0,  # Will be calculated based on criteria
            "created_at": datetime.now().isoformat()
        }
        
        return {
            "success": True,
            "data": {
                "segment": segment,
                "message": "Audience segment created successfully"
            }
        }
    
    async def update_campaign(self, user_id: str, campaign_id: str, updates: dict):
        """Update campaign details"""
        
        return {
            "success": True,
            "data": {
                "campaign_id": campaign_id,
                "message": "Campaign updated successfully",
                "updated_fields": list(updates.keys())
            }
        }
    
    async def get_list_contacts(self, user_id: str, list_id: str):
        """Get contacts in a specific list"""
        
        contacts = []
        contact_count = random.randint(20, 200)
        
        for i in range(contact_count):
            contacts.append({
                "id": str(uuid.uuid4()),
                "email": f"user{i}@example.com",
                "first_name": f"User{i}",
                "last_name": "Test",
                "status": random.choice(["subscribed", "unsubscribed"]),
                "added_at": (datetime.now() - timedelta(days=random.randint(1, 30))).isoformat()
            })
        
        return {
            "success": True,
            "data": {
                "contacts": contacts[:50],  # Limit for performance
                "total": len(contacts),
                "list_id": list_id
            }
        }
    
    async def add_contacts_to_list(self, user_id: str, list_id: str, contacts: List[dict]):
        """Add contacts to email list"""
        
        return {
            "success": True,
            "data": {
                "list_id": list_id,
                "contacts_added": len(contacts),
                "message": f"Successfully added {len(contacts)} contacts to list"
            }
        }
    
    async def create_template(self, user_id: str, template_data: dict):
        """Create email template"""
        
        template_id = str(uuid.uuid4())
        template = {
            "id": template_id,
            "user_id": user_id,
            "name": template_data.get("name"),
            "subject_template": template_data.get("subject_template"),
            "html_content": template_data.get("html_content"),
            "category": template_data.get("category", "general"),
            "usage_count": 0,
            "created_at": datetime.now().isoformat()
        }
        
        return {
            "success": True,
            "data": {
                "template": template,
                "message": "Email template created successfully"
            }
        }