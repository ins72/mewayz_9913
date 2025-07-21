"""
Team Management Services Business Logic
Professional Mewayz Platform
"""

from datetime import datetime
from typing import Dict, Any, List, Optional
from core.database import get_database
import uuid

class TeamService:
    """Service for team management operations"""
    
    @staticmethod
    async def get_team_members(user_id: str, workspace_id: str = None):
        """Get team members"""
        db = await get_database()
        
        query = {"owner_id": user_id}
        if workspace_id:
            query["workspace_id"] = workspace_id
        
        members = await db.team_members.find(query).to_list(length=None)
        return members
    
    @staticmethod
    async def invite_team_member(user_id: str, invite_data: Dict[str, Any]):
        """Invite new team member"""
        db = await get_database()
        
        invitation = {
    "_id": str(uuid.uuid4()),
    "owner_id": user_id,
    "workspace_id": invite_data.get("workspace_id"),
    "email": invite_data.get("email"),
    "role": invite_data.get("role", "member"),
    "permissions": invite_data.get("permissions", []),
    "status": "pending",
    "invited_at": datetime.utcnow(),
            "expires_at": datetime.utcnow() + datetime.timedelta(days=7)
    }
        
        result = await db.team_invitations.insert_one(invitation)
        return invitation
    
    @staticmethod
    async def get_team_projects(user_id: str):
        """Get team projects"""
        db = await get_database()
        
        projects = await db.team_projects.find({
    "$or": [
    {"owner_id": user_id},
    {"team_members": user_id}
    ]
        }).sort("created_at", -1).to_list(length=None)
        
        return projects
    
    @staticmethod
    async def create_project(user_id: str, project_data: Dict[str, Any]):
        """Create new team project"""
        db = await get_database()
        
        project = {
    "_id": str(uuid.uuid4()),
    "owner_id": user_id,
    "name": project_data.get("name"),
    "description": project_data.get("description", ""),
    "status": project_data.get("status", "active"),
    "team_members": project_data.get("team_members", []),
    "tasks": [],
    "deadline": project_data.get("deadline"),
    "priority": project_data.get("priority", "medium"),
    "created_at": datetime.utcnow(),
    "updated_at": datetime.utcnow()
    }
        
        result = await db.team_projects.insert_one(project)
        return project

# Global service instance
team_service = TeamService()
