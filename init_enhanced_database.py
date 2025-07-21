"""
Enhanced Database Collections for Real Social Media Data
Extends the database schema to support comprehensive social media data storage
"""

from motor.motor_asyncio import AsyncIOMotorClient
from datetime import datetime, timedelta
import uuid
import random

class EnhancedDatabaseInitializer:
    def __init__(self, mongodb_url: str = "mongodb://localhost:27017/mewayz_pro"):
        self.client = AsyncIOMotorClient(mongodb_url)
        self.db = self.client.mewayz_professional
    
    async def initialize_social_media_collections(self):
        """Initialize comprehensive social media data collections"""
        print("🚀 Initializing enhanced social media database collections...")
        
        # Social Media Monitoring Collection
        await self._init_social_monitoring()
        
        # Social Media Mentions Collection
        await self._init_social_mentions()
        
        # Influencer Data Collection
        await self._init_influencers()
        
        # Social Media Posts Collection (Enhanced)
        await self._init_social_posts_enhanced()
        
        # Social Media Analytics Collection
        await self._init_social_analytics()
        
        # Competitor Analysis Collection
        await self._init_competitor_analysis()
        
        # Social Media Campaigns Collection (Enhanced)
        await self._init_social_campaigns_enhanced()
        
        print("✅ Enhanced social media collections initialized!")
    
    async def _init_social_monitoring(self):
        """Initialize social media monitoring data"""
        collection = self.db.social_monitoring
        
        await collection.create_index("user_id")
        await collection.create_index("keyword")
        await collection.create_index("platform")
        await collection.create_index("timestamp")
        
        # Sample monitoring data
        sample_monitoring = [
            {
                "_id": str(uuid.uuid4()),
                "user_id": "sample_user_1",
                "keyword": "Mewayz",
                "platform": "twitter",
                "mentions_count": 156,
                "positive_mentions": 123,
                "neutral_mentions": 25,
                "negative_mentions": 8,
                "total_reach": 45600,
                "engagement": 2340,
                "sentiment_score": 0.78,
                "timestamp": datetime.utcnow() - timedelta(hours=i),
                "growth_rate": 12.5
            } for i in range(1, 25)  # Last 24 hours
        ]
        
        await collection.insert_many(sample_monitoring)
        print("  ✅ social_monitoring collection initialized")
    
    async def _init_social_mentions(self):
        """Initialize individual social media mentions"""
        collection = self.db.social_mentions
        
        await collection.create_index("user_id")
        await collection.create_index("keyword") 
        await collection.create_index("platform")
        await collection.create_index("sentiment")
        await collection.create_index("timestamp")
        
        platforms = ["twitter", "facebook", "instagram", "linkedin", "tiktok"]
        keywords = ["Mewayz", "@mewayz", "all-in-one platform", "business automation"]
        
        sample_mentions = []
        for i in range(100):  # 100 sample mentions
            mention = {
                "_id": str(uuid.uuid4()),
                "user_id": "sample_user_1",
                "mention_id": f"mention_{uuid.uuid4()}",
                "keyword": random.choice(keywords),
                "platform": random.choice(platforms),
                "author": f"@user_{random.randint(1000, 9999)}",
                "content": f"Great experience with {random.choice(keywords)}! Highly recommend for business automation.",
                "sentiment": random.choice(["positive", "neutral", "negative"]),
                "sentiment_score": round(random.uniform(0.2, 0.9), 2),
                "reach": random.randint(100, 5000),
                "engagement": random.randint(5, 500),
                "timestamp": datetime.utcnow() - timedelta(hours=random.randint(1, 168)),
                "location": random.choice(["US", "UK", "CA", "AU", "DE"]),
                "language": "en"
            }
            sample_mentions.append(mention)
        
        await collection.insert_many(sample_mentions)
        print("  ✅ social_mentions collection initialized")
    
    async def _init_influencers(self):
        """Initialize influencer data"""
        collection = self.db.influencers
        
        await collection.create_index("platform")
        await collection.create_index("followers_count")
        await collection.create_index("engagement_rate")
        
        sample_influencers = [
            {
                "_id": str(uuid.uuid4()),
                "influencer_id": "inf_001",
                "handle": "@techreview_sarah",
                "name": "Sarah Tech Reviews",
                "platform": "twitter",
                "followers_count": 125000,
                "following_count": 850,
                "posts_count": 3200,
                "engagement_rate": 4.2,
                "avg_likes": 520,
                "avg_comments": 45,
                "avg_shares": 23,
                "bio": "Tech reviewer and startup enthusiast. Helping businesses find the right tools.",
                "verified": True,
                "location": "San Francisco, CA",
                "topics": ["technology", "business", "startups", "productivity"],
                "last_post": datetime.utcnow() - timedelta(hours=2),
                "created_at": datetime.utcnow() - timedelta(days=30)
            },
            {
                "_id": str(uuid.uuid4()),
                "influencer_id": "inf_002", 
                "handle": "@business_mike",
                "name": "Mike Business Insights",
                "platform": "linkedin",
                "followers_count": 89000,
                "following_count": 1200,
                "posts_count": 1850,
                "engagement_rate": 6.1,
                "avg_likes": 340,
                "avg_comments": 89,
                "avg_shares": 56,
                "bio": "Business consultant and industry analyst. Sharing insights on digital transformation.",
                "verified": True,
                "location": "New York, NY",
                "topics": ["business", "consulting", "digital transformation", "AI"],
                "last_post": datetime.utcnow() - timedelta(hours=4),
                "created_at": datetime.utcnow() - timedelta(days=45)
            },
            {
                "_id": str(uuid.uuid4()),
                "influencer_id": "inf_003",
                "handle": "@startup_jane", 
                "name": "Jane Startup Success",
                "platform": "instagram",
                "followers_count": 67000,
                "following_count": 650,
                "posts_count": 2100,
                "engagement_rate": 8.3,
                "avg_likes": 890,
                "avg_comments": 125,
                "avg_shares": 34,
                "bio": "Entrepreneur sharing startup journey and business tools that work.",
                "verified": False,
                "location": "Austin, TX",
                "topics": ["entrepreneurship", "startups", "business tools", "growth"],
                "last_post": datetime.utcnow() - timedelta(hours=6),
                "created_at": datetime.utcnow() - timedelta(days=60)
            }
        ]
        
        await collection.insert_many(sample_influencers)
        print("  ✅ influencers collection initialized")
    
    async def _init_social_posts_enhanced(self):
        """Initialize enhanced social media posts"""
        collection = self.db.social_posts_enhanced
        
        await collection.create_index("user_id")
        await collection.create_index("platform")
        await collection.create_index("status")
        await collection.create_index("scheduled_time")
        await collection.create_index("published_at")
        
        sample_posts = [
            {
                "_id": str(uuid.uuid4()),
                "user_id": "sample_user_1",
                "post_id": str(uuid.uuid4()),
                "platform": "twitter",
                "content": "Exciting announcement! Our new automation features are now live 🚀",
                "status": "published",
                "scheduled_time": datetime.utcnow() - timedelta(hours=2),
                "published_at": datetime.utcnow() - timedelta(hours=2),
                "metrics": {
                    "impressions": 12500,
                    "likes": 89,
                    "comments": 12,
                    "shares": 23,
                    "clicks": 156,
                    "engagement_rate": 2.1
                },
                "hashtags": ["#automation", "#productivity", "#business"],
                "created_at": datetime.utcnow() - timedelta(hours=3)
            },
            {
                "_id": str(uuid.uuid4()),
                "user_id": "sample_user_1",
                "post_id": str(uuid.uuid4()),
                "platform": "linkedin",
                "content": "How business automation is transforming the way we work. Read our latest insights.",
                "status": "published",
                "scheduled_time": datetime.utcnow() - timedelta(days=1),
                "published_at": datetime.utcnow() - timedelta(days=1),
                "metrics": {
                    "impressions": 8900,
                    "likes": 67,
                    "comments": 8,
                    "shares": 45,
                    "clicks": 234,
                    "engagement_rate": 3.8
                },
                "hashtags": ["#businessautomation", "#digitaltransformation", "#productivity"],
                "created_at": datetime.utcnow() - timedelta(days=1, hours=1)
            }
        ]
        
        await collection.insert_many(sample_posts)
        print("  ✅ social_posts_enhanced collection initialized")
    
    async def _init_social_analytics(self):
        """Initialize social media analytics data"""
        collection = self.db.social_analytics
        
        await collection.create_index("user_id")
        await collection.create_index("platform")
        await collection.create_index("date")
        
        sample_analytics = []
        for days_ago in range(30):  # Last 30 days
            analytics = {
                "_id": str(uuid.uuid4()),
                "user_id": "sample_user_1",
                "date": (datetime.utcnow() - timedelta(days=days_ago)).strftime("%Y-%m-%d"),
                "platform": "all",
                "metrics": {
                    "total_posts": random.randint(2, 8),
                    "total_impressions": random.randint(5000, 25000),
                    "total_engagement": random.randint(200, 1200),
                    "new_followers": random.randint(5, 50),
                    "profile_visits": random.randint(100, 800),
                    "link_clicks": random.randint(20, 200)
                },
                "platform_breakdown": {
                    "twitter": {
                        "posts": random.randint(1, 3),
                        "impressions": random.randint(2000, 10000),
                        "engagement": random.randint(80, 500)
                    },
                    "linkedin": {
                        "posts": random.randint(0, 2),
                        "impressions": random.randint(1500, 8000),
                        "engagement": random.randint(60, 400)
                    },
                    "instagram": {
                        "posts": random.randint(1, 3),
                        "impressions": random.randint(1000, 7000),
                        "engagement": random.randint(50, 300)
                    }
                },
                "created_at": datetime.utcnow() - timedelta(days=days_ago)
            }
            sample_analytics.append(analytics)
        
        await collection.insert_many(sample_analytics)
        print("  ✅ social_analytics collection initialized")
    
    async def _init_competitor_analysis(self):
        """Initialize competitor analysis data"""
        collection = self.db.competitor_analysis
        
        await collection.create_index("user_id")
        await collection.create_index("competitor")
        await collection.create_index("platform")
        
        competitors = ["competitor_a", "competitor_b", "competitor_c"]
        platforms = ["twitter", "linkedin", "facebook", "instagram"]
        
        sample_competitors = []
        for competitor in competitors:
            for platform in platforms:
                comp_data = {
                    "_id": str(uuid.uuid4()),
                    "user_id": "sample_user_1",
                    "competitor": competitor,
                    "platform": platform,
                    "followers": random.randint(10000, 100000),
                    "following": random.randint(500, 5000),
                    "posts_count": random.randint(1000, 10000),
                    "engagement_rate": round(random.uniform(2.0, 8.0), 1),
                    "avg_likes": random.randint(100, 2000),
                    "avg_comments": random.randint(10, 200),
                    "posts_per_day": round(random.uniform(1.5, 5.0), 1),
                    "share_of_voice": round(random.uniform(15.0, 35.0), 1),
                    "sentiment_score": round(random.uniform(0.4, 0.8), 2),
                    "top_hashtags": [f"#{platform}marketing", f"#{competitor}tips", "#business"],
                    "last_analyzed": datetime.utcnow() - timedelta(hours=random.randint(1, 24)),
                    "created_at": datetime.utcnow() - timedelta(days=7)
                }
                sample_competitors.append(comp_data)
        
        await collection.insert_many(sample_competitors)
        print("  ✅ competitor_analysis collection initialized")
    
    async def _init_social_campaigns_enhanced(self):
        """Initialize enhanced social media campaigns"""
        collection = self.db.social_campaigns_enhanced
        
        await collection.create_index("user_id")
        await collection.create_index("status")
        await collection.create_index("campaign_type")
        
        sample_campaigns = [
            {
                "_id": str(uuid.uuid4()),
                "user_id": "sample_user_1",
                "campaign_id": str(uuid.uuid4()),
                "name": "Product Launch Campaign",
                "campaign_type": "product_launch",
                "status": "active",
                "platforms": ["twitter", "linkedin", "facebook"],
                "start_date": datetime.utcnow() - timedelta(days=7),
                "end_date": datetime.utcnow() + timedelta(days=23),
                "budget": 5000.00,
                "spent": 1847.32,
                "objective": "brand_awareness",
                "target_audience": {
                    "age_range": "25-45",
                    "interests": ["business", "automation", "productivity"],
                    "location": ["US", "CA", "UK"]
                },
                "metrics": {
                    "impressions": 156000,
                    "clicks": 3240,
                    "conversions": 89,
                    "ctr": 2.08,
                    "cpc": 0.57,
                    "conversion_rate": 2.75
                },
                "posts": [
                    {
                        "post_id": str(uuid.uuid4()),
                        "platform": "twitter",
                        "content": "Introducing our revolutionary business automation platform! 🚀",
                        "scheduled_time": datetime.utcnow() + timedelta(hours=2),
                        "status": "scheduled"
                    }
                ],
                "created_at": datetime.utcnow() - timedelta(days=8)
            }
        ]
        
        await collection.insert_many(sample_campaigns)
        print("  ✅ social_campaigns_enhanced collection initialized")

if __name__ == "__main__":
    import asyncio
    
    async def main():
        initializer = EnhancedDatabaseInitializer()
        await initializer.initialize_social_media_collections()
        print("\n🎉 Enhanced social media database initialization complete!")
    
    asyncio.run(main())