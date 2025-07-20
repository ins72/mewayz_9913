# MEWAYZ V2 - COMPREHENSIVE WORKSPACE SYSTEM
# Version: 2.0 | Date: July 19, 2025
# Multi-workspace architecture with feature-based access control

from fastapi import HTTPException, Depends, status, Query, BackgroundTasks, Form
from sqlalchemy.orm import Session, relationship
from sqlalchemy import Column, Integer, String, Boolean, DateTime, Text, JSON, ForeignKey, Decimal, Enum
from sqlalchemy.ext.declarative import declarative_base
from datetime import datetime, timedelta
import uuid
import secrets
from typing import Optional, List, Dict, Any
from main import *

# Enhanced Workspace Models
class Workspace(Base):
    __tablename__ = "workspaces"
    
    id = Column(Integer, primary_key=True, index=True)
    uuid = Column(String(36), unique=True, default=lambda: str(uuid.uuid4()))
    name = Column(String(255), nullable=False)
    slug = Column(String(255), unique=True, nullable=False)
    description = Column(Text)
    logo_url = Column(String(500))
    brand_color = Column(String(7), default="#007AFF")
    settings = Column(JSON)
    subscription_plan_id = Column(Integer, ForeignKey("subscription_plans.id"))
    subscription_status = Column(Enum("active", "past_due", "suspended", "cancelled", "trialing"), default="trialing")
    grace_period_ends_at = Column(DateTime)
    trial_ends_at = Column(DateTime, default=lambda: datetime.utcnow() + timedelta(days=14))
    created_at = Column(DateTime, default=datetime.utcnow)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    
    # Relationships
    users = relationship("WorkspaceUser", back_populates="workspace")
    goals = relationship("WorkspaceGoal", back_populates="workspace")
    features = relationship("WorkspaceFeature", back_populates="workspace")
    subscription = relationship("Subscription", back_populates="workspace")

class WorkspaceUser(Base):
    __tablename__ = "workspace_users"
    
    id = Column(Integer, primary_key=True, index=True)
    workspace_id = Column(Integer, ForeignKey("workspaces.id", ondelete="CASCADE"))
    user_id = Column(Integer, ForeignKey("users.id", ondelete="CASCADE"))
    role = Column(Enum("owner", "admin", "editor", "viewer"), nullable=False)
    permissions = Column(JSON)
    invited_at = Column(DateTime)
    joined_at = Column(DateTime)
    invitation_token = Column(String(255))
    created_at = Column(DateTime, default=datetime.utcnow)
    
    # Relationships
    workspace = relationship("Workspace", back_populates="users")
    user = relationship("User")

# Goals and Features System
class Goal(Base):
    __tablename__ = "goals"
    
    id = Column(Integer, primary_key=True, index=True)
    key = Column(String(100), unique=True, nullable=False)
    name = Column(String(255), nullable=False)
    description = Column(Text)
    icon = Column(String(100))
    color = Column(String(7))
    category = Column(String(100))
    sort_order = Column(Integer, default=0)
    is_active = Column(Boolean, default=True)
    created_at = Column(DateTime, default=datetime.utcnow)

class Feature(Base):
    __tablename__ = "features"
    
    id = Column(Integer, primary_key=True, index=True)
    key = Column(String(100), unique=True, nullable=False)
    name = Column(String(255), nullable=False)
    description = Column(Text)
    goal_key = Column(String(100), ForeignKey("goals.key"))
    category = Column(String(100))
    type = Column(Enum("binary", "quota", "tiered"), default="binary")
    dependencies = Column(JSON)
    is_active = Column(Boolean, default=True)
    created_at = Column(DateTime, default=datetime.utcnow)

class WorkspaceGoal(Base):
    __tablename__ = "workspace_goals"
    
    id = Column(Integer, primary_key=True, index=True)
    workspace_id = Column(Integer, ForeignKey("workspaces.id", ondelete="CASCADE"))
    goal_key = Column(String(100), ForeignKey("goals.key"))
    is_enabled = Column(Boolean, default=True)
    settings = Column(JSON)
    created_at = Column(DateTime, default=datetime.utcnow)
    
    # Relationships
    workspace = relationship("Workspace", back_populates="goals")
    goal = relationship("Goal")

class WorkspaceFeature(Base):
    __tablename__ = "workspace_features"
    
    id = Column(Integer, primary_key=True, index=True)
    workspace_id = Column(Integer, ForeignKey("workspaces.id", ondelete="CASCADE"))
    feature_key = Column(String(100), ForeignKey("features.key"))
    is_enabled = Column(Boolean, default=True)
    quota_limit = Column(Integer)
    usage_count = Column(Integer, default=0)
    last_used_at = Column(DateTime)
    created_at = Column(DateTime, default=datetime.utcnow)
    
    # Relationships
    workspace = relationship("Workspace", back_populates="features")
    feature = relationship("Feature")

# Subscription System
class SubscriptionPlan(Base):
    __tablename__ = "subscription_plans"
    
    id = Column(Integer, primary_key=True, index=True)
    name = Column(String(255), nullable=False)
    description = Column(Text)
    pricing_type = Column(Enum("feature_based", "flat_monthly"), nullable=False)
    base_price = Column(Decimal(10, 2), default=0.00)
    feature_price_monthly = Column(Decimal(10, 2), default=0.00)
    feature_price_yearly = Column(Decimal(10, 2), default=0.00)
    max_features = Column(Integer)
    includes_whitelabel = Column(Boolean, default=False)
    is_active = Column(Boolean, default=True)
    is_public = Column(Boolean, default=True)
    sort_order = Column(Integer, default=0)
    created_at = Column(DateTime, default=datetime.utcnow)

class PlanFeature(Base):
    __tablename__ = "plan_features"
    
    id = Column(Integer, primary_key=True, index=True)
    plan_id = Column(Integer, ForeignKey("subscription_plans.id", ondelete="CASCADE"))
    feature_key = Column(String(100), ForeignKey("features.key"))
    is_included = Column(Boolean, default=True)
    quota_limit = Column(Integer)
    overage_price = Column(Decimal(10, 2), default=0.00)
    config = Column(JSON)

class Subscription(Base):
    __tablename__ = "subscriptions"
    
    id = Column(Integer, primary_key=True, index=True)
    workspace_id = Column(Integer, ForeignKey("workspaces.id", ondelete="CASCADE"))
    plan_id = Column(Integer, ForeignKey("subscription_plans.id"))
    stripe_subscription_id = Column(String(255))
    status = Column(Enum("active", "past_due", "suspended", "cancelled", "trialing"), default="trialing")
    payment_status = Column(Enum("active", "past_due", "suspended", "cancelled"), default="active")
    current_period_start = Column(DateTime)
    current_period_end = Column(DateTime)
    trial_start = Column(DateTime)
    trial_end = Column(DateTime)
    cancelled_at = Column(DateTime)
    grace_period_ends_at = Column(DateTime)
    retry_count = Column(Integer, default=0)
    last_retry_date = Column(DateTime)
    last_payment_failed_at = Column(DateTime)
    created_at = Column(DateTime, default=datetime.utcnow)
    
    # Relationships
    workspace = relationship("Workspace", back_populates="subscription")
    plan = relationship("SubscriptionPlan")

# API Endpoints for Workspace Management
@app.post("/api/workspaces/create")
def create_workspace(
    workspace_data: dict,
    current_user: User = Depends(get_current_user),
    db: Session = Depends(get_db)
):
    """Create a new workspace with selected goals"""
    
    # Create workspace
    workspace = Workspace(
        name=workspace_data.get("name"),
        slug=workspace_data.get("slug"),
        description=workspace_data.get("description"),
        brand_color=workspace_data.get("brand_color", "#007AFF")
    )
    db.add(workspace)
    db.flush()
    
    # Add user as owner
    workspace_user = WorkspaceUser(
        workspace_id=workspace.id,
        user_id=current_user.id,
        role="owner",
        joined_at=datetime.utcnow()
    )
    db.add(workspace_user)
    
    # Add selected goals
    selected_goals = workspace_data.get("goals", [])
    for goal_key in selected_goals:
        workspace_goal = WorkspaceGoal(
            workspace_id=workspace.id,
            goal_key=goal_key,
            is_enabled=True
        )
        db.add(workspace_goal)
    
    # Assign free plan features
    free_plan = db.query(SubscriptionPlan).filter(
        SubscriptionPlan.name == "Free Plan"
    ).first()
    
    if free_plan:
        workspace.subscription_plan_id = free_plan.id
        
        # Add basic features based on goals
        default_features = get_default_features_for_goals(selected_goals)
        for feature_key, config in default_features.items():
            workspace_feature = WorkspaceFeature(
                workspace_id=workspace.id,
                feature_key=feature_key,
                is_enabled=True,
                quota_limit=config.get("quota_limit")
            )
            db.add(workspace_feature)
    
    db.commit()
    db.refresh(workspace)
    
    return {
        "success": True,
        "data": {
            "workspace": {
                "id": workspace.id,
                "uuid": workspace.uuid,
                "name": workspace.name,
                "slug": workspace.slug,
                "trial_ends_at": workspace.trial_ends_at.isoformat() if workspace.trial_ends_at else None
            }
        }
    }

@app.get("/api/workspaces")
def get_user_workspaces(
    current_user: User = Depends(get_current_user),
    db: Session = Depends(get_db)
):
    """Get all workspaces for the current user"""
    
    workspace_users = db.query(WorkspaceUser).filter(
        WorkspaceUser.user_id == current_user.id
    ).all()
    
    workspaces = []
    for workspace_user in workspace_users:
        workspace = workspace_user.workspace
        workspaces.append({
            "id": workspace.id,
            "uuid": workspace.uuid,
            "name": workspace.name,
            "slug": workspace.slug,
            "logo_url": workspace.logo_url,
            "brand_color": workspace.brand_color,
            "role": workspace_user.role,
            "subscription_status": workspace.subscription_status,
            "trial_ends_at": workspace.trial_ends_at.isoformat() if workspace.trial_ends_at else None
        })
    
    return {
        "success": True,
        "data": {
            "workspaces": workspaces
        }
    }

@app.get("/api/workspaces/{workspace_id}")
def get_workspace_details(
    workspace_id: int,
    current_user: User = Depends(get_current_user),
    db: Session = Depends(get_db)
):
    """Get detailed workspace information"""
    
    # Check access
    workspace_user = db.query(WorkspaceUser).filter(
        WorkspaceUser.workspace_id == workspace_id,
        WorkspaceUser.user_id == current_user.id
    ).first()
    
    if not workspace_user:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    workspace = workspace_user.workspace
    
    # Get goals
    workspace_goals = db.query(WorkspaceGoal).filter(
        WorkspaceGoal.workspace_id == workspace_id,
        WorkspaceGoal.is_enabled == True
    ).all()
    
    goals = []
    for wg in workspace_goals:
        goal = wg.goal
        goals.append({
            "key": goal.key,
            "name": goal.name,
            "description": goal.description,
            "icon": goal.icon,
            "color": goal.color,
            "enabled": wg.is_enabled
        })
    
    # Get features
    workspace_features = db.query(WorkspaceFeature).filter(
        WorkspaceFeature.workspace_id == workspace_id
    ).all()
    
    features = []
    for wf in workspace_features:
        feature = wf.feature
        features.append({
            "key": feature.key,
            "name": feature.name,
            "description": feature.description,
            "type": feature.type,
            "enabled": wf.is_enabled,
            "quota_limit": wf.quota_limit,
            "usage_count": wf.usage_count,
            "usage_percentage": (wf.usage_count / wf.quota_limit * 100) if wf.quota_limit else 0
        })
    
    return {
        "success": True,
        "data": {
            "workspace": {
                "id": workspace.id,
                "uuid": workspace.uuid,
                "name": workspace.name,
                "slug": workspace.slug,
                "description": workspace.description,
                "logo_url": workspace.logo_url,
                "brand_color": workspace.brand_color,
                "subscription_status": workspace.subscription_status,
                "trial_ends_at": workspace.trial_ends_at.isoformat() if workspace.trial_ends_at else None,
                "role": workspace_user.role
            },
            "goals": goals,
            "features": features
        }
    }

def get_default_features_for_goals(goal_keys: List[str]) -> Dict[str, Dict]:
    """Get default features based on selected goals"""
    feature_map = {
        "instagram": {
            "social_posting": {"quota_limit": 10},
            "instagram_search": {"quota_limit": 100},
            "social_analytics": {"quota_limit": None}
        },
        "link_bio": {
            "link_builder": {"quota_limit": 3},
            "bio_analytics": {"quota_limit": None}
        },
        "courses": {
            "course_creation": {"quota_limit": 1},
            "video_hosting": {"quota_limit": 5},
            "student_management": {"quota_limit": 50}
        },
        "ecommerce": {
            "product_catalog": {"quota_limit": 10},
            "order_management": {"quota_limit": None},
            "payment_processing": {"quota_limit": None}
        },
        "crm": {
            "contact_management": {"quota_limit": 100},
            "email_campaigns": {"quota_limit": 5},
            "lead_scoring": {"quota_limit": None}
        },
        "website": {
            "website_builder": {"quota_limit": 1},
            "custom_domain": {"quota_limit": None},
            "seo_tools": {"quota_limit": None}
        }
    }
    
    features = {}
    for goal_key in goal_keys:
        if goal_key in feature_map:
            features.update(feature_map[goal_key])
    
    return features

# Initialize default data
def init_default_goals_and_features(db: Session):
    """Initialize default goals and features"""
    
    default_goals = [
        {
            "key": "instagram",
            "name": "Instagram Management",
            "description": "Manage your Instagram presence and generate leads",
            "icon": "instagram",
            "color": "#E4405F",
            "category": "Social Media",
            "sort_order": 1
        },
        {
            "key": "link_bio", 
            "name": "Link in Bio",
            "description": "Create stunning link in bio pages",
            "icon": "link",
            "color": "#00D4AA",
            "category": "Marketing",
            "sort_order": 2
        },
        {
            "key": "courses",
            "name": "Course Creation",
            "description": "Build and sell online courses",
            "icon": "academic-cap",
            "color": "#F59E0B",
            "category": "Education",
            "sort_order": 3
        },
        {
            "key": "ecommerce",
            "name": "E-commerce",
            "description": "Build and manage your online store",
            "icon": "shopping-cart",
            "color": "#8B5CF6", 
            "category": "Commerce",
            "sort_order": 4
        },
        {
            "key": "crm",
            "name": "CRM & Email Marketing",
            "description": "Manage leads and send email campaigns",
            "icon": "users",
            "color": "#EF4444",
            "category": "Marketing",
            "sort_order": 5
        },
        {
            "key": "website",
            "name": "Website Builder",
            "description": "Build professional websites with no code",
            "icon": "globe",
            "color": "#3B82F6",
            "category": "Web Development",
            "sort_order": 6
        }
    ]
    
    for goal_data in default_goals:
        existing_goal = db.query(Goal).filter(Goal.key == goal_data["key"]).first()
        if not existing_goal:
            goal = Goal(**goal_data)
            db.add(goal)
    
    # Initialize subscription plans
    default_plans = [
        {
            "name": "Free Plan",
            "description": "Perfect for getting started",
            "pricing_type": "flat_monthly",
            "base_price": 0.00,
            "max_features": 10,
            "includes_whitelabel": False,
            "is_active": True,
            "is_public": True,
            "sort_order": 1
        },
        {
            "name": "Pro Plan", 
            "description": "For growing businesses",
            "pricing_type": "feature_based",
            "feature_price_monthly": 1.00,
            "feature_price_yearly": 10.00,
            "includes_whitelabel": False,
            "is_active": True,
            "is_public": True,
            "sort_order": 2
        },
        {
            "name": "Enterprise Plan",
            "description": "For large organizations",
            "pricing_type": "feature_based",
            "feature_price_monthly": 1.50,
            "feature_price_yearly": 15.00,
            "includes_whitelabel": True,
            "is_active": True,
            "is_public": True,
            "sort_order": 3
        }
    ]
    
    for plan_data in default_plans:
        existing_plan = db.query(SubscriptionPlan).filter(
            SubscriptionPlan.name == plan_data["name"]
        ).first()
        if not existing_plan:
            plan = SubscriptionPlan(**plan_data)
            db.add(plan)
    
    db.commit()