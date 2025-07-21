"""
Advanced AI-Powered Analytics Engine
Predictive insights, trend analysis, and intelligent recommendations
"""
import asyncio
import numpy as np
from datetime import datetime, timedelta
from typing import Dict, Any, List, Optional, Tuple
import json
from dataclasses import dataclass
from enum import Enum

from core.database import get_database
from core.professional_logger import professional_logger, LogLevel, LogCategory
from core.external_api_integrator import ai_service_integrator

class AnalyticsType(str, Enum):
    PREDICTIVE = "predictive"
    TREND = "trend"
    ANOMALY = "anomaly"
    RECOMMENDATION = "recommendation"
    SENTIMENT = "sentiment"
    FORECASTING = "forecasting"

@dataclass
class AnalyticsInsight:
    insight_id: str
    type: AnalyticsType
    title: str
    description: str
    confidence: float
    impact_level: str  # low, medium, high, critical
    recommendations: List[str]
    data_points: Dict[str, Any]
    created_at: datetime
    expires_at: Optional[datetime] = None

class AIAnalyticsEngine:
    """Advanced AI-powered analytics with predictive capabilities"""
    
    def __init__(self):
        self.db = None
        self.models = {}
        self.insights_cache = {}
    
    async def get_database(self):
        """Get database connection with lazy initialization"""
        if not self.db:
            self.db = get_database()
        return self.db
    
    async def generate_predictive_insights(self, user_id: str, timeframe: int = 30) -> List[AnalyticsInsight]:
        """Generate AI-powered predictive insights"""
        try:
            db = await self.get_database()
            
            # Get historical data for analysis
            historical_data = await self._gather_historical_data(user_id, timeframe * 2)
            
            insights = []
            
            # Revenue prediction
            revenue_insight = await self._predict_revenue_trend(user_id, historical_data)
            if revenue_insight:
                insights.append(revenue_insight)
            
            # User engagement prediction
            engagement_insight = await self._predict_engagement_trends(user_id, historical_data)
            if engagement_insight:
                insights.append(engagement_insight)
            
            # Churn prediction
            churn_insight = await self._predict_churn_risk(user_id, historical_data)
            if churn_insight:
                insights.append(churn_insight)
            
            # Content performance prediction
            content_insight = await self._predict_content_performance(user_id, historical_data)
            if content_insight:
                insights.append(content_insight)
            
            # Store insights for future reference
            for insight in insights:
                await self._store_insight(user_id, insight)
            
            await professional_logger.log(
                LogLevel.INFO, LogCategory.AI_SERVICE,
                f"Generated {len(insights)} predictive insights for user {user_id}",
                details={"insights_count": len(insights), "timeframe_days": timeframe}
            )
            
            return insights
            
        except Exception as e:
            await professional_logger.log(
                LogLevel.ERROR, LogCategory.AI_SERVICE,
                f"Failed to generate predictive insights: {str(e)}",
                error=e
            )
            return []
    
    async def _gather_historical_data(self, user_id: str, days: int) -> Dict[str, Any]:
        """Gather comprehensive historical data for analysis"""
        db = await self.get_database()
        start_date = datetime.utcnow() - timedelta(days=days)
        
        # Financial data
        financial_data = await db.financial_transactions.find({
            "user_id": user_id,
            "created_at": {"$gte": start_date}
        }).to_list(length=None)
        
        # Social media data
        social_data = await db.social_media_posts.find({
            "user_id": user_id,
            "created_at": {"$gte": start_date}
        }).to_list(length=None)
        
        # Email campaign data
        email_data = await db.email_campaigns.find({
            "user_id": user_id,
            "created_at": {"$gte": start_date}
        }).to_list(length=None)
        
        # User activity data
        activity_data = await db.user_activities.find({
            "user_id": user_id,
            "timestamp": {"$gte": start_date}
        }).to_list(length=None)
        
        return {
            "financial": financial_data,
            "social": social_data,
            "email": email_data,
            "activity": activity_data,
            "date_range": {"start": start_date, "end": datetime.utcnow(), "days": days}
        }
    
    async def _predict_revenue_trend(self, user_id: str, historical_data: Dict[str, Any]) -> Optional[AnalyticsInsight]:
        """Predict revenue trends using AI analysis"""
        try:
            financial_data = historical_data.get("financial", [])
            if len(financial_data) < 5:  # Need minimum data points
                return None
            
            # Calculate revenue by week
            weekly_revenue = {}
            for transaction in financial_data:
                if transaction.get("type") == "revenue":
                    week = transaction["created_at"].strftime("%Y-W%U")
                    weekly_revenue[week] = weekly_revenue.get(week, 0) + transaction.get("amount", 0)
            
            if len(weekly_revenue) < 3:
                return None
            
            # Simple trend analysis
            revenue_values = list(weekly_revenue.values())
            recent_avg = sum(revenue_values[-2:]) / 2
            earlier_avg = sum(revenue_values[:-2]) / len(revenue_values[:-2]) if len(revenue_values) > 2 else recent_avg
            
            trend_percentage = ((recent_avg - earlier_avg) / earlier_avg * 100) if earlier_avg > 0 else 0
            
            # Generate AI-powered recommendation
            prompt = f"""
            Analyze this revenue trend data and provide business insights:
            - Recent average weekly revenue: ${recent_avg:,.2f}
            - Previous average weekly revenue: ${earlier_avg:,.2f}
            - Trend percentage: {trend_percentage:+.1f}%
            
            Provide 3 specific recommendations for optimizing revenue.
            """
            
            ai_response = await ai_service_integrator.generate_content(prompt, max_tokens=200)
            recommendations = []
            
            if ai_response.get("success"):
                # Parse AI recommendations
                content = ai_response.get("content", "")
                recommendations = [line.strip("- ") for line in content.split("\n") if line.strip().startswith("-")][:3]
            
            if not recommendations:
                recommendations = [
                    "Analyze top-performing revenue sources and replicate successful strategies",
                    "Optimize pricing strategy based on customer value perception",
                    "Implement upselling opportunities for existing customers"
                ]
            
            confidence = min(0.9, max(0.3, len(financial_data) / 30))  # Higher confidence with more data
            impact_level = "high" if abs(trend_percentage) > 15 else "medium" if abs(trend_percentage) > 5 else "low"
            
            return AnalyticsInsight(
                insight_id=f"revenue_trend_{user_id}_{datetime.utcnow().strftime('%Y%m%d')}",
                type=AnalyticsType.PREDICTIVE,
                title=f"Revenue Trend Analysis: {trend_percentage:+.1f}% Change",
                description=f"Revenue trend analysis shows a {trend_percentage:+.1f}% change from previous period. Recent weekly average is ${recent_avg:,.2f} compared to ${earlier_avg:,.2f} earlier.",
                confidence=confidence,
                impact_level=impact_level,
                recommendations=recommendations,
                data_points={
                    "recent_weekly_avg": recent_avg,
                    "previous_weekly_avg": earlier_avg,
                    "trend_percentage": trend_percentage,
                    "data_points": len(financial_data),
                    "weeks_analyzed": len(weekly_revenue)
                },
                created_at=datetime.utcnow(),
                expires_at=datetime.utcnow() + timedelta(days=7)
            )
            
        except Exception as e:
            await professional_logger.log(
                LogLevel.ERROR, LogCategory.AI_SERVICE,
                f"Failed to predict revenue trend: {str(e)}",
                error=e
            )
            return None
    
    async def _predict_engagement_trends(self, user_id: str, historical_data: Dict[str, Any]) -> Optional[AnalyticsInsight]:
        """Predict social media engagement trends"""
        try:
            social_data = historical_data.get("social", [])
            if len(social_data) < 5:
                return None
            
            # Calculate engagement metrics
            total_engagements = []
            for post in social_data:
                metrics = post.get("metrics", {})
                engagement = (metrics.get("like_count", 0) + 
                            metrics.get("share_count", 0) + 
                            metrics.get("comment_count", 0))
                total_engagements.append(engagement)
            
            if not total_engagements:
                return None
            
            avg_engagement = sum(total_engagements) / len(total_engagements)
            recent_engagement = sum(total_engagements[-3:]) / min(3, len(total_engagements))
            
            trend_percentage = ((recent_engagement - avg_engagement) / avg_engagement * 100) if avg_engagement > 0 else 0
            
            # AI-powered content recommendations
            prompt = f"""
            Analyze social media engagement data:
            - Average engagement per post: {avg_engagement:.1f}
            - Recent engagement trend: {trend_percentage:+.1f}%
            - Total posts analyzed: {len(social_data)}
            
            Provide 3 specific strategies to improve engagement.
            """
            
            ai_response = await ai_service_integrator.generate_content(prompt, max_tokens=150)
            recommendations = []
            
            if ai_response.get("success"):
                content = ai_response.get("content", "")
                recommendations = [line.strip("- ") for line in content.split("\n") if line.strip().startswith("-")][:3]
            
            if not recommendations:
                recommendations = [
                    "Post during peak audience activity hours for maximum visibility",
                    "Use interactive content formats like polls and questions",
                    "Respond promptly to comments to boost engagement signals"
                ]
            
            confidence = min(0.85, max(0.4, len(social_data) / 20))
            impact_level = "high" if abs(trend_percentage) > 25 else "medium" if abs(trend_percentage) > 10 else "low"
            
            return AnalyticsInsight(
                insight_id=f"engagement_trend_{user_id}_{datetime.utcnow().strftime('%Y%m%d')}",
                type=AnalyticsType.TREND,
                title=f"Social Engagement Trend: {trend_percentage:+.1f}%",
                description=f"Social media engagement analysis shows a {trend_percentage:+.1f}% trend. Average engagement per post is {avg_engagement:.1f} interactions.",
                confidence=confidence,
                impact_level=impact_level,
                recommendations=recommendations,
                data_points={
                    "avg_engagement": avg_engagement,
                    "recent_engagement": recent_engagement,
                    "trend_percentage": trend_percentage,
                    "posts_analyzed": len(social_data)
                },
                created_at=datetime.utcnow(),
                expires_at=datetime.utcnow() + timedelta(days=5)
            )
            
        except Exception as e:
            await professional_logger.log(
                LogLevel.ERROR, LogCategory.AI_SERVICE,
                f"Failed to predict engagement trends: {str(e)}",
                error=e
            )
            return None
    
    async def _predict_churn_risk(self, user_id: str, historical_data: Dict[str, Any]) -> Optional[AnalyticsInsight]:
        """Predict customer churn risk using activity patterns"""
        try:
            activity_data = historical_data.get("activity", [])
            if len(activity_data) < 10:
                return None
            
            # Analyze activity patterns
            daily_activity = {}
            for activity in activity_data:
                day = activity["timestamp"].strftime("%Y-%m-%d")
                daily_activity[day] = daily_activity.get(day, 0) + 1
            
            # Calculate activity trend
            days = sorted(daily_activity.keys())
            if len(days) < 7:
                return None
            
            recent_days = days[-7:]
            earlier_days = days[:-7] if len(days) > 7 else days[:7]
            
            recent_avg = sum(daily_activity[day] for day in recent_days) / len(recent_days)
            earlier_avg = sum(daily_activity[day] for day in earlier_days) / len(earlier_days)
            
            activity_decline = ((earlier_avg - recent_avg) / earlier_avg * 100) if earlier_avg > 0 else 0
            
            # Determine churn risk level
            if activity_decline > 40:
                risk_level = "high"
                risk_percentage = min(85, 50 + activity_decline)
            elif activity_decline > 20:
                risk_level = "medium"
                risk_percentage = 30 + activity_decline
            else:
                risk_level = "low"
                risk_percentage = max(5, 20 - activity_decline)
            
            recommendations = [
                "Send personalized re-engagement email campaign",
                "Offer exclusive features or content to increase value perception",
                "Schedule one-on-one check-in to understand user needs"
            ] if risk_level != "low" else [
                "Continue providing consistent value and monitor satisfaction",
                "Proactively gather feedback to maintain engagement",
                "Showcase new features and platform improvements"
            ]
            
            return AnalyticsInsight(
                insight_id=f"churn_risk_{user_id}_{datetime.utcnow().strftime('%Y%m%d')}",
                type=AnalyticsType.RECOMMENDATION,
                title=f"Churn Risk Assessment: {risk_level.upper()} ({risk_percentage:.0f}%)",
                description=f"User activity analysis indicates {risk_level} churn risk at {risk_percentage:.0f}%. Activity declined {activity_decline:.1f}% in recent period.",
                confidence=0.75,
                impact_level="critical" if risk_level == "high" else "medium" if risk_level == "medium" else "low",
                recommendations=recommendations,
                data_points={
                    "risk_percentage": risk_percentage,
                    "activity_decline": activity_decline,
                    "recent_daily_avg": recent_avg,
                    "previous_daily_avg": earlier_avg,
                    "days_analyzed": len(days)
                },
                created_at=datetime.utcnow(),
                expires_at=datetime.utcnow() + timedelta(days=3)
            )
            
        except Exception as e:
            await professional_logger.log(
                LogLevel.ERROR, LogCategory.AI_SERVICE,
                f"Failed to predict churn risk: {str(e)}",
                error=e
            )
            return None
    
    async def _predict_content_performance(self, user_id: str, historical_data: Dict[str, Any]) -> Optional[AnalyticsInsight]:
        """Predict content performance using AI analysis"""
        try:
            social_data = historical_data.get("social", [])
            email_data = historical_data.get("email", [])
            
            if len(social_data) < 3 and len(email_data) < 3:
                return None
            
            # Analyze top performing content
            high_performing_posts = []
            for post in social_data:
                metrics = post.get("metrics", {})
                engagement = metrics.get("like_count", 0) + metrics.get("share_count", 0)
                if engagement > 0:
                    high_performing_posts.append({
                        "content": post.get("content", "")[:100],
                        "engagement": engagement,
                        "platform": post.get("platform", "unknown")
                    })
            
            # Sort by engagement
            high_performing_posts.sort(key=lambda x: x["engagement"], reverse=True)
            top_posts = high_performing_posts[:3]
            
            if not top_posts:
                return None
            
            # Generate AI-powered content strategy
            content_analysis = "\n".join([f"- {post['content']} (Engagement: {post['engagement']})" for post in top_posts])
            
            prompt = f"""
            Analyze these high-performing content pieces:
            {content_analysis}
            
            Identify 3 content strategies that would likely perform well based on this data.
            """
            
            ai_response = await ai_service_integrator.generate_content(prompt, max_tokens=200)
            recommendations = []
            
            if ai_response.get("success"):
                content = ai_response.get("content", "")
                recommendations = [line.strip("- ") for line in content.split("\n") if line.strip().startswith("-")][:3]
            
            if not recommendations:
                recommendations = [
                    "Create content similar to your top-performing posts",
                    "Use engaging questions and calls-to-action",
                    "Post consistently during your audience's peak hours"
                ]
            
            avg_engagement = sum(post["engagement"] for post in top_posts) / len(top_posts)
            
            return AnalyticsInsight(
                insight_id=f"content_performance_{user_id}_{datetime.utcnow().strftime('%Y%m%d')}",
                type=AnalyticsType.RECOMMENDATION,
                title="Content Performance Optimization Strategy",
                description=f"Analysis of your top-performing content shows average engagement of {avg_engagement:.0f}. Strategic content optimization can improve performance.",
                confidence=0.7,
                impact_level="medium",
                recommendations=recommendations,
                data_points={
                    "top_posts_analyzed": len(top_posts),
                    "avg_engagement": avg_engagement,
                    "total_content_pieces": len(social_data) + len(email_data)
                },
                created_at=datetime.utcnow(),
                expires_at=datetime.utcnow() + timedelta(days=14)
            )
            
        except Exception as e:
            await professional_logger.log(
                LogLevel.ERROR, LogCategory.AI_SERVICE,
                f"Failed to predict content performance: {str(e)}",
                error=e
            )
            return None
    
    async def _store_insight(self, user_id: str, insight: AnalyticsInsight):
        """Store insight in database for future reference"""
        try:
            db = await self.get_database()
            
            insight_doc = {
                "insight_id": insight.insight_id,
                "user_id": user_id,
                "type": insight.type.value,
                "title": insight.title,
                "description": insight.description,
                "confidence": insight.confidence,
                "impact_level": insight.impact_level,
                "recommendations": insight.recommendations,
                "data_points": insight.data_points,
                "created_at": insight.created_at,
                "expires_at": insight.expires_at,
                "viewed": False,
                "acted_upon": False
            }
            
            await db.ai_insights.insert_one(insight_doc)
            
        except Exception as e:
            await professional_logger.log(
                LogLevel.ERROR, LogCategory.DATABASE,
                f"Failed to store insight: {str(e)}",
                error=e
            )
    
    async def get_user_insights(self, user_id: str, limit: int = 10) -> List[Dict[str, Any]]:
        """Get stored insights for user"""
        try:
            db = await self.get_database()
            
            # Get active insights (not expired)
            insights = await db.ai_insights.find({
                "user_id": user_id,
                "$or": [
                    {"expires_at": None},
                    {"expires_at": {"$gt": datetime.utcnow()}}
                ]
            }).sort("created_at", -1).limit(limit).to_list(length=limit)
            
            # Convert ObjectId to string
            for insight in insights:
                insight["_id"] = str(insight["_id"])
                if "created_at" in insight:
                    insight["created_at"] = insight["created_at"].isoformat()
                if insight.get("expires_at"):
                    insight["expires_at"] = insight["expires_at"].isoformat()
            
            return insights
            
        except Exception as e:
            await professional_logger.log(
                LogLevel.ERROR, LogCategory.DATABASE,
                f"Failed to get user insights: {str(e)}",
                error=e
            )
            return []
    
    async def generate_anomaly_detection(self, user_id: str) -> List[AnalyticsInsight]:
        """Detect anomalies in user data patterns"""
        try:
            # This is a simplified anomaly detection - in production, you'd use more sophisticated algorithms
            insights = []
            
            # Check for unusual activity patterns, revenue spikes, engagement drops, etc.
            # Implementation would include statistical analysis and machine learning models
            
            return insights
            
        except Exception as e:
            await professional_logger.log(
                LogLevel.ERROR, LogCategory.AI_SERVICE,
                f"Failed to generate anomaly detection: {str(e)}",
                error=e
            )
            return []

# Global instance
ai_analytics_engine = AIAnalyticsEngine()