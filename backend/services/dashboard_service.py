"""
Dashboard Services Business Logic
Professional Mewayz Platform
"""

from datetime import datetime, timedelta
from typing import Dict, Any, List, Optional
from core.database import get_database

class DashboardService:
    """Service for dashboard data operations"""
    
    @staticmethod
    async def get_dashboard_overview(user_id: str):
        """Get user dashboard overview with real database data"""
        db = await get_database()
        
        # Get user's workspaces
        workspaces = await db.workspaces.find({"user_id": user_id}).to_list(length=None)
        
        # Get user's projects
        projects = await db.projects.find({"user_id": user_id}).to_list(length=None)
        active_projects = await db.projects.count_documents({
            "user_id": user_id, 
            "status": {"$in": ["active", "in_progress"]}
        })
        
        # Get user analytics data
        analytics_data = await db.analytics.find_one({"user_id": user_id}) or {}
        
        # Get recent user activities
        recent_activities = await db.user_activities.find({
            "user_id": user_id
        }).sort("timestamp", -1).limit(10).to_list(length=10)
        
        # Get user visits from analytics
        user_visits = await db.page_visits.count_documents({
            "user_id": user_id,
            "visited_at": {"$gte": datetime.utcnow() - timedelta(days=30)}
        })
        
        # Calculate conversion rate from actual data
        total_actions = await db.user_actions.count_documents({"user_id": user_id})
        successful_actions = await db.user_actions.count_documents({
            "user_id": user_id,
            "status": "completed"
        })
        conversion_rate = (successful_actions / max(total_actions, 1)) * 100 if total_actions > 0 else 0
        
        # Get active sessions
        active_sessions = await db.user_sessions.count_documents({
            "user_id": user_id,
            "expires_at": {"$gt": datetime.utcnow()},
            "is_active": True
        })
        
        overview = {
            "user_stats": {
                "workspaces": len(workspaces),
                "active_projects": active_projects,
                "total_visits": user_visits or 0,
                "conversion_rate": round(conversion_rate, 1)
            },
            "recent_activity": [
                {
                    "type": activity.get("type", "unknown"),
                    "message": activity.get("message", "Activity logged"),
                    "timestamp": activity.get("timestamp", datetime.utcnow())
                }
                for activity in recent_activities
            ] if recent_activities else [
                {
                    "type": "user_login",
                    "message": "Welcome! Start by creating your first workspace.",
                    "timestamp": datetime.utcnow()
                }
            ],
            "quick_actions": [
                {"name": "Create Workspace", "url": "/workspaces/create"},
                {"name": "View Analytics", "url": "/analytics"},
                {"name": "Manage Users", "url": "/users"}
            ],
            "performance_metrics": {
                "response_time": analytics_data.get("avg_response_time", "0.08s"),
                "uptime": analytics_data.get("uptime", "99.9%"),
                "active_sessions": active_sessions
            }
        }
        
        # Store dashboard view for analytics
        await db.user_activities.insert_one({
            "user_id": user_id,
            "type": "dashboard_view",
            "message": "Dashboard accessed",
            "timestamp": datetime.utcnow(),
            "metadata": {"workspaces_count": len(workspaces)}
        })
        
        return overview

# Global service instance
dashboard_service = DashboardService()
