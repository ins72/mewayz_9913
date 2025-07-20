"""
Analytics Service - Real Database Operations and Calculations
Professional Mewayz Platform
"""
from typing import Dict, Any, List, Optional
from datetime import datetime, timedelta
from collections import defaultdict
import uuid

from ..core.database import (
    get_analytics_collection, get_users_collection, 
    get_workspaces_collection, get_bio_sites_collection
)

class AnalyticsService:
    def __init__(self):
        self.analytics_collection = get_analytics_collection()
        self.users_collection = get_users_collection()
        self.workspaces_collection = get_workspaces_collection()
        self.bio_sites_collection = get_bio_sites_collection()

    async def track_event(self, event_data: Dict[str, Any]) -> str:
        """Track analytics event with real database operations"""
        event_doc = {
            "_id": str(uuid.uuid4()),
            "event_type": event_data.get("event_type"),
            "user_id": event_data.get("user_id"),
            "workspace_id": event_data.get("workspace_id"),
            "properties": event_data.get("properties", {}),
            "timestamp": datetime.utcnow(),
            "session_id": event_data.get("session_id"),
            "ip_address": event_data.get("ip_address"),
            "user_agent": event_data.get("user_agent"),
            "referrer": event_data.get("referrer")
        }
        
        result = await self.analytics_collection.insert_one(event_doc)
        return str(result.inserted_id)

    async def get_user_analytics(self, user_id: str, days: int = 30) -> Dict[str, Any]:
        """Get real user analytics from database"""
        start_date = datetime.utcnow() - timedelta(days=days)
        
        # Get events for this user
        events = await self.analytics_collection.find({
            "user_id": user_id,
            "timestamp": {"$gte": start_date}
        }).to_list(length=None)
        
        # Calculate real statistics
        total_events = len(events)
        
        # Group events by type
        events_by_type = defaultdict(int)
        events_by_day = defaultdict(int)
        
        for event in events:
            events_by_type[event["event_type"]] += 1
            day_key = event["timestamp"].strftime("%Y-%m-%d")
            events_by_day[day_key] += 1
        
        # Get user workspaces count
        workspace_count = await self.workspaces_collection.count_documents({"owner_id": user_id})
        
        # Get bio sites count
        bio_sites_count = await self.bio_sites_collection.count_documents({"user_id": user_id})
        
        analytics_data = {
            "summary": {
                "total_events": total_events,
                "unique_event_types": len(events_by_type),
                "avg_events_per_day": round(total_events / days, 2) if days > 0 else 0,
                "workspaces_count": workspace_count,
                "bio_sites_count": bio_sites_count,
                "date_range": {
                    "start": start_date.isoformat(),
                    "end": datetime.utcnow().isoformat(),
                    "days": days
                }
            },
            "events_by_type": dict(events_by_type),
            "daily_activity": dict(events_by_day),
            "top_activities": sorted(events_by_type.items(), key=lambda x: x[1], reverse=True)[:10]
        }
        
        return analytics_data

    async def get_workspace_analytics(self, workspace_id: str, days: int = 30) -> Dict[str, Any]:
        """Get real workspace analytics from database"""
        start_date = datetime.utcnow() - timedelta(days=days)
        
        # Get workspace info
        workspace = await self.workspaces_collection.find_one({"_id": workspace_id})
        if not workspace:
            raise ValueError("Workspace not found")
        
        # Get events for this workspace
        events = await self.analytics_collection.find({
            "workspace_id": workspace_id,
            "timestamp": {"$gte": start_date}
        }).to_list(length=None)
        
        # Get unique users in this workspace
        unique_users = set()
        events_by_user = defaultdict(int)
        
        for event in events:
            if event.get("user_id"):
                unique_users.add(event["user_id"])
                events_by_user[event["user_id"]] += 1
        
        # Calculate member activity
        member_count = len(workspace.get("members", []))
        active_members = len(unique_users)
        
        analytics_data = {
            "workspace_info": {
                "id": workspace["_id"],
                "name": workspace["name"],
                "owner_id": workspace["owner_id"],
                "created_at": workspace["created_at"],
                "member_count": member_count
            },
            "activity_summary": {
                "total_events": len(events),
                "active_members": active_members,
                "activity_rate": round((active_members / member_count) * 100, 2) if member_count > 0 else 0,
                "avg_events_per_member": round(len(events) / active_members, 2) if active_members > 0 else 0
            },
            "member_activity": dict(events_by_user),
            "date_range": {
                "start": start_date.isoformat(),
                "end": datetime.utcnow().isoformat(),
                "days": days
            }
        }
        
        return analytics_data

    async def get_platform_overview(self) -> Dict[str, Any]:
        """Get real platform-wide analytics"""
        # Get real counts from database
        total_users = await self.users_collection.count_documents({})
        total_workspaces = await self.workspaces_collection.count_documents({})
        total_bio_sites = await self.bio_sites_collection.count_documents({})
        
        # Get active users (logged in last 30 days)
        thirty_days_ago = datetime.utcnow() - timedelta(days=30)
        active_users = await self.users_collection.count_documents({
            "usage_stats.last_login": {"$gte": thirty_days_ago}
        })
        
        # Get new users this month
        start_of_month = datetime.utcnow().replace(day=1, hour=0, minute=0, second=0, microsecond=0)
        new_users_this_month = await self.users_collection.count_documents({
            "created_at": {"$gte": start_of_month}
        })
        
        # Get subscription distribution
        subscription_pipeline = [
            {"$group": {"_id": "$subscription_plan", "count": {"$sum": 1}}}
        ]
        subscription_dist = await self.users_collection.aggregate(subscription_pipeline).to_list(length=None)
        subscription_breakdown = {item["_id"]: item["count"] for item in subscription_dist}
        
        # Get recent activity
        recent_events = await self.analytics_collection.count_documents({
            "timestamp": {"$gte": datetime.utcnow() - timedelta(hours=24)}
        })
        
        overview_data = {
            "platform_statistics": {
                "total_users": total_users,
                "active_users_30d": active_users,
                "new_users_this_month": new_users_this_month,
                "total_workspaces": total_workspaces,
                "total_bio_sites": total_bio_sites,
                "activity_last_24h": recent_events
            },
            "growth_metrics": {
                "user_growth_rate": round((new_users_this_month / max(total_users - new_users_this_month, 1)) * 100, 2),
                "user_activation_rate": round((active_users / total_users) * 100, 2) if total_users > 0 else 0
            },
            "subscription_breakdown": subscription_breakdown,
            "calculated_at": datetime.utcnow().isoformat()
        }
        
        return overview_data

    async def get_feature_usage_analytics(self, user_id: Optional[str] = None) -> Dict[str, Any]:
        """Get real feature usage analytics"""
        query = {}
        if user_id:
            query["user_id"] = user_id
        
        # Get feature usage events
        pipeline = [
            {"$match": query},
            {"$group": {
                "_id": "$event_type",
                "usage_count": {"$sum": 1},
                "unique_users": {"$addToSet": "$user_id"},
                "last_used": {"$max": "$timestamp"}
            }},
            {"$sort": {"usage_count": -1}}
        ]
        
        feature_usage = await self.analytics_collection.aggregate(pipeline).to_list(length=None)
        
        # Process results
        usage_data = []
        for item in feature_usage:
            usage_data.append({
                "feature": item["_id"],
                "usage_count": item["usage_count"],
                "unique_users": len(item["unique_users"]),
                "last_used": item["last_used"],
                "popularity_score": round(item["usage_count"] / len(item["unique_users"]), 2) if item["unique_users"] else 0
            })
        
        analytics_data = {
            "feature_usage": usage_data,
            "most_popular_features": usage_data[:10],
            "total_features_tracked": len(usage_data),
            "analysis_scope": "user_specific" if user_id else "platform_wide"
        }
        
        return analytics_data

# Create service instance
analytics_service = AnalyticsService()