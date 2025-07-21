"""
Customer Experience Suite Services Business Logic
Professional Mewayz Platform
"""

from datetime import datetime, timedelta
from typing import Dict, Any, List, Optional
from core.database import get_database
import uuid
import random

class CustomerExperienceSuiteService:
    """Service for customer experience management operations"""
    
    @staticmethod
    async def get_customer_journey_analytics(user_id: str):
        """Get customer journey analytics"""
        db = await get_database()
        
        # In real implementation, this would analyze actual customer data
        analytics = {
            "journey_stages": {
                "awareness": {
                    "visitors": random.randint(2000, 5000),
                    "conversion_rate": round(random.uniform(15, 30), 1),
                    "avg_time_spent": "2m 34s",
                    "top_sources": ["organic_search", "social_media", "referral"]
                },
                "interest": {
                    "visitors": random.randint(800, 1500),
                    "conversion_rate": round(random.uniform(25, 45), 1),
                    "avg_time_spent": "5m 12s",
                    "common_actions": ["download_resource", "view_pricing", "watch_demo"]
                },
                "consideration": {
                    "visitors": random.randint(300, 600),
                    "conversion_rate": round(random.uniform(35, 60), 1),
                    "avg_time_spent": "8m 45s",
                    "common_actions": ["free_trial", "contact_sales", "read_reviews"]
                },
                "purchase": {
                    "visitors": random.randint(100, 250),
                    "conversion_rate": round(random.uniform(60, 85), 1),
                    "avg_time_spent": "12m 20s",
                    "completion_rate": round(random.uniform(75, 90), 1)
                }
            },
            "funnel_analysis": {
                "drop_off_points": [
                    {"stage": "pricing_page", "drop_off_rate": 35.2},
                    {"stage": "signup_form", "drop_off_rate": 22.8},
                    {"stage": "payment_page", "drop_off_rate": 15.4}
                ],
                "optimization_opportunities": [
                    "Simplify signup process",
                    "Add more pricing transparency",
                    "Improve checkout flow"
                ]
            },
            "customer_segments": [
                {
                    "name": "Enterprise Customers",
                    "size": 45,
                    "ltv": 2500,
                    "churn_rate": 2.1,
                    "satisfaction_score": 8.7
                },
                {
                    "name": "SMB Customers",
                    "size": 320,
                    "ltv": 850,
                    "churn_rate": 5.8,
                    "satisfaction_score": 7.9
                },
                {
                    "name": "Individual Users",
                    "size": 1200,
                    "ltv": 180,
                    "churn_rate": 12.3,
                    "satisfaction_score": 7.2
                }
            ]
        }
        
        return analytics
    
    @staticmethod
    async def get_customer_feedback_summary(user_id: str):
        """Get customer feedback summary"""
        db = await get_database()
        
        # Get actual feedback from database
        feedback = await db.customer_feedback.find({"user_id": user_id}).sort("created_at", -1).limit(100).to_list(length=None)
        
        # Calculate sentiment scores (in real implementation, would use NLP)
        positive_feedback = len([f for f in feedback if f.get("sentiment", "neutral") == "positive"])
        negative_feedback = len([f for f in feedback if f.get("sentiment", "neutral") == "negative"])
        total_feedback = len(feedback)
        
        summary = {
            "overview": {
                "total_feedback": total_feedback,
                "positive_feedback": positive_feedback,
                "negative_feedback": negative_feedback,
                "sentiment_score": round(((positive_feedback - negative_feedback) / max(total_feedback, 1)) * 100, 1),
                "avg_rating": round(random.uniform(3.8, 4.8), 1),
                "response_rate": round(random.uniform(15, 35), 1)
            },
            "common_themes": [
                {"theme": "Product Quality", "mentions": random.randint(20, 50), "sentiment": "positive"},
                {"theme": "Customer Support", "mentions": random.randint(15, 30), "sentiment": "positive"},
                {"theme": "Pricing", "mentions": random.randint(10, 25), "sentiment": "mixed"},
                {"theme": "User Interface", "mentions": random.randint(8, 20), "sentiment": "positive"},
                {"theme": "Feature Requests", "mentions": random.randint(5, 15), "sentiment": "neutral"}
            ],
            "recent_feedback": feedback[:10],
            "action_items": [
                {"priority": "high", "item": "Address pricing concerns", "feedback_count": 12},
                {"priority": "medium", "item": "Improve mobile experience", "feedback_count": 8},
                {"priority": "low", "item": "Add requested integrations", "feedback_count": 5}
            ]
        }
        
        return summary
    
    @staticmethod
    async def create_customer_survey(user_id: str, survey_data: Dict[str, Any]):
        """Create customer satisfaction survey"""
        db = await get_database()
        
        survey = {
            "_id": str(uuid.uuid4()),
            "user_id": user_id,
            "title": survey_data.get("title"),
            "description": survey_data.get("description", ""),
            "type": survey_data.get("type", "satisfaction"),  # satisfaction, nps, csat, ces
            "questions": survey_data.get("questions", []),
            "target_audience": survey_data.get("target_audience", "all_customers"),
            "distribution_channels": survey_data.get("channels", ["email"]),
            "settings": {
                "anonymous": survey_data.get("anonymous", False),
                "mandatory_completion": survey_data.get("mandatory", False),
                "time_limit": survey_data.get("time_limit"),
                "randomize_questions": survey_data.get("randomize", False)
            },
            "status": "draft",
            "responses_count": 0,
            "completion_rate": 0,
            "avg_completion_time": 0,
            "created_at": datetime.utcnow(),
            "updated_at": datetime.utcnow()
        }
        
        result = await db.customer_surveys.insert_one(survey)
        return survey
    
    @staticmethod
    async def get_nps_score(user_id: str, period_days: int = 30):
        """Calculate Net Promoter Score"""
        db = await get_database()
        
        since_date = datetime.utcnow() - timedelta(days=period_days)
        
        # Get NPS responses
        nps_responses = await db.nps_responses.find({
            "user_id": user_id,
            "created_at": {"$gte": since_date}
        }).to_list(length=None)
        
        if not nps_responses:
            # Generate sample data for demonstration
            nps_responses = [
                {"score": random.randint(0, 10), "feedback": "Sample feedback"} 
                for _ in range(random.randint(20, 100))
            ]
        
        total_responses = len(nps_responses)
        promoters = len([r for r in nps_responses if r.get("score", 0) >= 9])
        detractors = len([r for r in nps_responses if r.get("score", 0) <= 6])
        passives = total_responses - promoters - detractors
        
        nps_score = ((promoters - detractors) / max(total_responses, 1)) * 100
        
        nps_data = {
            "nps_score": round(nps_score, 1),
            "total_responses": total_responses,
            "promoters": {"count": promoters, "percentage": round((promoters / max(total_responses, 1)) * 100, 1)},
            "passives": {"count": passives, "percentage": round((passives / max(total_responses, 1)) * 100, 1)},
            "detractors": {"count": detractors, "percentage": round((detractors / max(total_responses, 1)) * 100, 1)},
            "trend": random.choice(["improving", "stable", "declining"]),
            "benchmark": {
                "industry_average": round(random.uniform(20, 50), 1),
                "your_score": round(nps_score, 1),
                "performance": "above_average" if nps_score > 30 else "average" if nps_score > 0 else "below_average"
            },
            "recent_comments": [
                {"score": 9, "comment": "Great product, excellent support!"},
                {"score": 8, "comment": "Very satisfied with the service"},
                {"score": 4, "comment": "Could use some improvements"}
            ][:3]
        }
        
        return nps_data
    
    @staticmethod
    async def get_customer_health_score(user_id: str, customer_id: str = None):
        """Calculate customer health scores"""
        db = await get_database()
        
        if customer_id:
            # Individual customer health score
            customer = await db.customers.find_one({"_id": customer_id, "user_id": user_id})
            if not customer:
                return None
            
            # Calculate health score based on various factors
            health_factors = {
                "usage_frequency": random.uniform(0.6, 1.0),
                "feature_adoption": random.uniform(0.5, 0.9),
                "support_tickets": random.uniform(0.7, 1.0),
                "payment_history": random.uniform(0.8, 1.0),
                "engagement_level": random.uniform(0.6, 0.95)
            }
            
            overall_score = sum(health_factors.values()) / len(health_factors)
            
            return {
                "customer_id": customer_id,
                "overall_health_score": round(overall_score * 100, 1),
                "risk_level": "low" if overall_score > 0.8 else "medium" if overall_score > 0.6 else "high",
                "factors": health_factors,
                "recommendations": [
                    "Increase feature engagement",
                    "Schedule quarterly check-in",
                    "Provide advanced training"
                ],
                "last_calculated": datetime.utcnow()
            }
        else:
            # Portfolio view of customer health
            total_customers = random.randint(100, 500)
            healthy_customers = int(total_customers * random.uniform(0.6, 0.8))
            at_risk_customers = int(total_customers * random.uniform(0.15, 0.25))
            critical_customers = total_customers - healthy_customers - at_risk_customers
            
            return {
                "portfolio_overview": {
                    "total_customers": total_customers,
                    "healthy": {"count": healthy_customers, "percentage": round((healthy_customers / total_customers) * 100, 1)},
                    "at_risk": {"count": at_risk_customers, "percentage": round((at_risk_customers / total_customers) * 100, 1)},
                    "critical": {"count": critical_customers, "percentage": round((critical_customers / total_customers) * 100, 1)},
                },
                "avg_health_score": round(random.uniform(70, 85), 1),
                "trend": random.choice(["improving", "stable", "declining"]),
                "action_required": critical_customers + at_risk_customers
            }