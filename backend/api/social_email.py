"""
Social Email API Routes

Provides API endpoints for social email functionality including
email campaign social media integration and cross-platform marketing.
"""

from fastapi import APIRouter, Depends, HTTPException
from typing import Dict, List, Optional, Any
from services.social_email_service import social_email_service
from core.auth import get_current_user

router = APIRouter(prefix="/api/social-email", tags=["Social Email"])

@router.get("/campaigns")
async def get_social_email_campaigns(current_user: dict = Depends(get_current_user)):
    """Get social email campaigns"""
    user_id = current_user.get("user_id")
    return await social_email_service.get_social_email_campaigns(user_id)

@router.post("/campaigns")
async def create_social_email_campaign(
    campaign_data: Dict[str, Any],
    current_user: dict = Depends(get_current_user)
):
    """Create a new social email campaign"""
    user_id = current_user.get("user_id")
    return await social_email_service.create_social_email_campaign(user_id, campaign_data)

@router.get("/campaigns/{campaign_id}")
async def get_campaign_details(
    campaign_id: str,
    current_user: dict = Depends(get_current_user)
):
    """Get detailed campaign information"""
    return await social_email_service.get_campaign_details(campaign_id)

@router.put("/campaigns/{campaign_id}")
async def update_campaign(
    campaign_id: str,
    update_data: Dict[str, Any],
    current_user: dict = Depends(get_current_user)
):
    """Update campaign settings"""
    return await social_email_service.update_campaign(campaign_id, update_data)

@router.post("/campaigns/{campaign_id}/launch")
async def launch_campaign(
    campaign_id: str,
    current_user: dict = Depends(get_current_user)
):
    """Launch a social email campaign"""
    return await social_email_service.launch_campaign(campaign_id)

@router.get("/templates")
async def get_email_templates(
    current_user: dict = Depends(get_current_user),
    category: Optional[str] = None
):
    """Get social email templates"""
    return await social_email_service.get_email_templates(category)

@router.get("/analytics/{campaign_id}")
async def get_campaign_analytics(
    campaign_id: str,
    current_user: dict = Depends(get_current_user)
):
    """Get campaign performance analytics"""
    return await social_email_service.get_campaign_analytics(campaign_id)

@router.post("/automation/rules")
async def create_automation_rule(
    rule_data: Dict[str, Any],
    current_user: dict = Depends(get_current_user)
):
    """Create social email automation rule"""
    user_id = current_user.get("user_id")
    return await social_email_service.create_automation_rule(user_id, rule_data)

@router.get("/integration/status")
async def get_integration_status(current_user: dict = Depends(get_current_user)):
    """Get social platform integration status"""
    user_id = current_user.get("user_id")
    return await social_email_service.get_integration_status(user_id)