from fastapi import APIRouter, Depends, HTTPException
from pydantic import BaseModel
from typing import List, Optional, Dict, Any
from datetime import datetime
import uuid
from motor.motor_asyncio import AsyncIOMotorClient
import os

# Import from main app
from main import get_current_user, database

router = APIRouter(prefix="/api/onboarding", tags=["onboarding"])

# Collections
onboarding_collection = database.onboarding_data
workspaces_collection = database.workspaces
users_collection = database.users

class OnboardingData(BaseModel):
    goals: List[str]
    features: List[str]
    teamSize: str
    industry: str
    businessType: str
    monthlyRevenue: str
    primaryFocus: str
    teamMembers: List[Dict[str, str]]
    subscriptionPlan: str

class WorkspaceSetup(BaseModel):
    name: str
    description: Optional[str] = None
    industry: Optional[str] = None
    goals: List[str] = []
    features: List[str] = []

@router.post("/complete")
async def complete_onboarding(
    onboarding_data: OnboardingData,
    current_user: dict = Depends(get_current_user)
):
    """Complete the onboarding process and set up user's workspace"""
    
    try:
        # Save onboarding data
        onboarding_doc = {
            "_id": str(uuid.uuid4()),
            "user_id": current_user["id"],
            "goals": onboarding_data.goals,
            "features": onboarding_data.features,
            "team_size": onboarding_data.teamSize,
            "industry": onboarding_data.industry,
            "business_type": onboarding_data.businessType,
            "monthly_revenue": onboarding_data.monthlyRevenue,
            "primary_focus": onboarding_data.primaryFocus,
            "subscription_plan": onboarding_data.subscriptionPlan,
            "completed_at": datetime.utcnow(),
            "created_at": datetime.utcnow()
        }
        
        await onboarding_collection.insert_one(onboarding_doc)
        
        # Create or update main workspace based on onboarding data
        workspace_name = f"{current_user['name']}'s Workspace"
        if onboarding_data.industry:
            workspace_name = f"{current_user['name']}'s {onboarding_data.industry.title()} Workspace"
        
        # Build features configuration based on selected goals and features
        features_enabled = {}
        
        # Enable features based on goals
        goal_feature_mapping = {
            'social_media': ['social_media_management', 'analytics'],
            'content_creation': ['ai_assistant', 'content_calendar'],
            'ecommerce': ['ecommerce', 'payment_processing', 'inventory'],
            'courses': ['course_management', 'student_portal'],
            'consulting': ['advanced_booking', 'crm', 'invoicing'],
            'analytics': ['advanced_analytics', 'reporting']
        }
        
        for goal in onboarding_data.goals:
            if goal in goal_feature_mapping:
                for feature in goal_feature_mapping[goal]:
                    features_enabled[feature] = True
        
        # Enable explicitly selected features
        feature_mapping = {
            'ai_assistant': 'ai_assistant',
            'bio_sites': 'bio_sites',
            'advanced_booking': 'advanced_booking',
            'financial_management': 'financial_management',
            'team_collaboration': 'team_collaboration'
        }
        
        for feature in onboarding_data.features:
            if feature in feature_mapping:
                features_enabled[feature_mapping[feature]] = True
        
        # Update or create workspace
        existing_workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
        
        if existing_workspace:
            # Update existing workspace
            await workspaces_collection.update_one(
                {"_id": existing_workspace["_id"]},
                {
                    "$set": {
                        "name": workspace_name,
                        "industry": onboarding_data.industry,
                        "features_enabled": features_enabled,
                        "onboarding_completed": True,
                        "business_type": onboarding_data.businessType,
                        "team_size": onboarding_data.teamSize,
                        "monthly_revenue": onboarding_data.monthlyRevenue,
                        "updated_at": datetime.utcnow()
                    }
                }
            )
            workspace_id = str(existing_workspace["_id"])
        else:
            # Create new workspace
            workspace_doc = {
                "_id": str(uuid.uuid4()),
                "owner_id": current_user["id"],
                "name": workspace_name,
                "slug": workspace_name.lower().replace(" ", "-").replace("'", ""),
                "description": f"Workspace for {onboarding_data.businessType} focused on {', '.join(onboarding_data.goals[:3])}",
                "industry": onboarding_data.industry,
                "business_type": onboarding_data.businessType,
                "team_size": onboarding_data.teamSize,
                "monthly_revenue": onboarding_data.monthlyRevenue,
                "features_enabled": features_enabled,
                "onboarding_completed": True,
                "is_active": True,
                "created_at": datetime.utcnow(),
                "updated_at": datetime.utcnow()
            }
            
            await workspaces_collection.insert_one(workspace_doc)
            workspace_id = workspace_doc["_id"]
        
        # Handle team member invitations
        team_invitations = []
        for member in onboarding_data.teamMembers:
            if member.get('email'):
                invitation_doc = {
                    "_id": str(uuid.uuid4()),
                    "workspace_id": workspace_id,
                    "invited_by": current_user["id"],
                    "email": member['email'],
                    "role": member.get('role', 'member'),
                    "status": "pending",
                    "invited_at": datetime.utcnow(),
                    "expires_at": datetime.utcnow().replace(day=datetime.utcnow().day + 7)  # 7 days expiry
                }
                team_invitations.append(invitation_doc)
        
        if team_invitations:
            await database.team_invitations.insert_many(team_invitations)
            # TODO: Send invitation emails
        
        # Update user subscription plan preference
        await users_collection.update_one(
            {"_id": current_user["_id"]},
            {
                "$set": {
                    "subscription_plan": onboarding_data.subscriptionPlan,
                    "onboarding_completed": True,
                    "updated_at": datetime.utcnow()
                }
            }
        )
        
        return {
            "success": True,
            "message": "Onboarding completed successfully",
            "data": {
                "workspace_id": workspace_id,
                "workspace_name": workspace_name,
                "features_enabled": list(features_enabled.keys()),
                "team_invitations_sent": len(team_invitations),
                "subscription_plan": onboarding_data.subscriptionPlan
            }
        }
        
    except Exception as e:
        raise HTTPException(
            status_code=500,
            detail=f"Failed to complete onboarding: {str(e)}"
        )

@router.get("/status")
async def get_onboarding_status(current_user: dict = Depends(get_current_user)):
    """Get user's onboarding status"""
    
    onboarding_data = await onboarding_collection.find_one({"user_id": current_user["id"]})
    user_data = await users_collection.find_one({"_id": current_user["_id"]})
    
    return {
        "success": True,
        "data": {
            "completed": bool(onboarding_data),
            "user_onboarding_completed": user_data.get("onboarding_completed", False),
            "onboarding_data": onboarding_data,
            "completion_date": onboarding_data.get("completed_at") if onboarding_data else None
        }
    }

@router.post("/workspace/setup")
async def setup_workspace(
    workspace_setup: WorkspaceSetup,
    current_user: dict = Depends(get_current_user)
):
    """Set up a new workspace during onboarding"""
    
    try:
        # Generate unique slug
        slug = workspace_setup.name.lower().replace(" ", "-").replace("_", "-")
        
        # Check if workspace with same slug exists
        existing = await workspaces_collection.find_one({"slug": slug, "owner_id": current_user["id"]})
        if existing:
            slug = f"{slug}-{str(uuid.uuid4())[:8]}"
        
        # Build features configuration
        features_enabled = {}
        for feature in workspace_setup.features:
            features_enabled[feature] = True
        
        workspace_doc = {
            "_id": str(uuid.uuid4()),
            "owner_id": current_user["id"],
            "name": workspace_setup.name,
            "slug": slug,
            "description": workspace_setup.description,
            "industry": workspace_setup.industry,
            "goals": workspace_setup.goals,
            "features_enabled": features_enabled,
            "is_active": True,
            "created_from_onboarding": True,
            "created_at": datetime.utcnow(),
            "updated_at": datetime.utcnow()
        }
        
        await workspaces_collection.insert_one(workspace_doc)
        
        return {
            "success": True,
            "data": {
                "workspace": {
                    "id": workspace_doc["_id"],
                    "name": workspace_doc["name"],
                    "slug": workspace_doc["slug"],
                    "created_at": workspace_doc["created_at"].isoformat()
                }
            }
        }
        
    except Exception as e:
        raise HTTPException(
            status_code=500,
            detail=f"Failed to set up workspace: {str(e)}"
        )

@router.get("/recommendations")
async def get_onboarding_recommendations(current_user: dict = Depends(get_current_user)):
    """Get personalized recommendations based on user profile"""
    
    try:
        # Get user data for personalization
        user_data = await users_collection.find_one({"_id": current_user["_id"]})
        
        # Basic recommendations - could be enhanced with ML in the future
        recommendations = {
            "goals": [
                {
                    "id": "content_creation",
                    "title": "Content Creation",
                    "description": "Start creating engaging content with AI assistance",
                    "reason": "Most popular starting point for new users",
                    "confidence": 0.8
                },
                {
                    "id": "social_media",
                    "title": "Social Media Growth", 
                    "description": "Build your social media presence effectively",
                    "reason": "Essential for modern creator economy",
                    "confidence": 0.75
                }
            ],
            "features": [
                {
                    "id": "ai_assistant",
                    "title": "AI Content Assistant",
                    "description": "AI-powered content generation and optimization",
                    "reason": "Accelerates content creation by 300%",
                    "confidence": 0.9
                },
                {
                    "id": "bio_sites", 
                    "title": "Bio Link Pages",
                    "description": "Professional link-in-bio pages",
                    "reason": "Quick way to establish online presence",
                    "confidence": 0.7
                }
            ],
            "subscription": {
                "recommended_plan": "pro",
                "reason": "Best value for creators and small businesses",
                "upgrade_benefits": [
                    "Unlimited AI requests",
                    "Custom domains",
                    "Advanced analytics",
                    "Priority support"
                ]
            }
        }
        
        return {
            "success": True,
            "data": recommendations
        }
        
    except Exception as e:
        raise HTTPException(
            status_code=500,
            detail=f"Failed to get recommendations: {str(e)}"
        )

@router.delete("/reset")
async def reset_onboarding(current_user: dict = Depends(get_current_user)):
    """Reset onboarding data (for development/testing)"""
    
    try:
        # Remove onboarding data
        await onboarding_collection.delete_many({"user_id": current_user["id"]})
        
        # Update user
        await users_collection.update_one(
            {"_id": current_user["_id"]},
            {
                "$set": {
                    "onboarding_completed": False,
                    "updated_at": datetime.utcnow()
                }
            }
        )
        
        return {
            "success": True,
            "message": "Onboarding data reset successfully"
        }
        
    except Exception as e:
        raise HTTPException(
            status_code=500,
            detail=f"Failed to reset onboarding: {str(e)}"
        )