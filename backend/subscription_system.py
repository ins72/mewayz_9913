from fastapi import APIRouter, Depends, HTTPException, status
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials
from pydantic import BaseModel
from typing import List, Optional, Dict, Any
from datetime import datetime, timedelta
import uuid
import os
from motor.motor_asyncio import AsyncIOMotorClient
from dotenv import load_dotenv
from jose import JWTError, jwt
from passlib.context import CryptContext
import stripe

# Load environment variables
load_dotenv()

# Database setup
MONGO_URL = os.getenv("MONGO_URL", "mongodb://localhost:27017/mewayz_professional")
SECRET_KEY = os.getenv("SECRET_KEY", "mewayz-professional-secret-key-2025-ultra-secure")
ALGORITHM = os.getenv("ALGORITHM", "HS256")

# Configure Stripe (you'll need to add Stripe keys to .env)
stripe.api_key = os.getenv("STRIPE_SECRET_KEY", "sk_test_dummy_key")

# MongoDB client
client = AsyncIOMotorClient(MONGO_URL)
database = client.get_database()

# Collections
subscriptions_collection = database.subscriptions
users_collection = database.users
workspaces_collection = database.workspaces

# Security setup
pwd_context = CryptContext(schemes=["bcrypt"], deprecated="auto")
security = HTTPBearer()

router = APIRouter(prefix="/api/subscriptions", tags=["subscriptions"])

# Auth dependency
async def verify_token(credentials: HTTPAuthorizationCredentials = Depends(security)):
    try:
        payload = jwt.decode(credentials.credentials, SECRET_KEY, algorithms=[ALGORITHM])
        email: str = payload.get("sub")
        if email is None:
            raise HTTPException(
                status_code=status.HTTP_401_UNAUTHORIZED,
                detail="Invalid authentication credentials"
            )
        return email
    except JWTError:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Invalid authentication credentials"
        )

async def get_current_user(email: str = Depends(verify_token)):
    user = await users_collection.find_one({"email": email})
    if user is None:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="User not found"
        )
    # Convert ObjectId to string for JSON serialization
    user["id"] = str(user["_id"])
    return user

class SubscriptionPlan(BaseModel):
    id: str
    name: str
    price: float
    interval: str  # month, year
    features: List[str]
    limits: Dict[str, Any]

class UpgradeRequest(BaseModel):
    plan_id: str
    payment_method_id: Optional[str] = None

# Available subscription plans
SUBSCRIPTION_PLANS = {
    "free": {
        "id": "free",
        "name": "Free Starter",
        "price": 0,
        "interval": "month",
        "stripe_price_id": None,
        "features": [
            "1 Workspace",
            "3 Bio Sites", 
            "Basic AI Features",
            "Community Support"
        ],
        "limits": {
            "workspaces": 1,
            "bio_sites": 3,
            "ai_requests": 10,
            "storage_gb": 1,
            "team_members": 1,
            "custom_domains": False
        }
    },
    "pro": {
        "id": "pro",
        "name": "Professional",
        "price": 29,
        "interval": "month",
        "stripe_price_id": "price_pro_monthly",  # Replace with actual Stripe price ID
        "features": [
            "5 Workspaces",
            "Unlimited Bio Sites",
            "Advanced AI Features",
            "Priority Support",
            "Custom Domains"
        ],
        "limits": {
            "workspaces": 5,
            "bio_sites": -1,  # unlimited
            "ai_requests": 1000,
            "storage_gb": 50,
            "team_members": 10,
            "custom_domains": True
        }
    },
    "enterprise": {
        "id": "enterprise", 
        "name": "Enterprise",
        "price": 99,
        "interval": "month",
        "stripe_price_id": "price_enterprise_monthly",
        "features": [
            "Unlimited Workspaces",
            "Unlimited Bio Sites",
            "Enterprise AI Features",
            "24/7 Support",
            "Custom Integrations"
        ],
        "limits": {
            "workspaces": -1,
            "bio_sites": -1,
            "ai_requests": -1,
            "storage_gb": -1,
            "team_members": -1,
            "custom_domains": True
        }
    }
}

@router.get("/plans")
async def get_subscription_plans():
    """Get available subscription plans"""
    return {
        "success": True,
        "data": {
            "plans": list(SUBSCRIPTION_PLANS.values())
        }
    }

@router.get("/current")
async def get_current_subscription(current_user: dict = Depends(get_current_user)):
    """Get user's current subscription and usage"""
    
    try:
        # Get current subscription
        current_subscription = await subscriptions_collection.find_one({
            "user_id": current_user["id"],
            "status": {"$in": ["active", "trialing"]}
        })
        
        # Default to free plan if no subscription
        current_plan_id = "free"
        if current_subscription:
            current_plan_id = current_subscription.get("plan_id", "free")
        
        current_plan = SUBSCRIPTION_PLANS.get(current_plan_id, SUBSCRIPTION_PLANS["free"])
        
        # Calculate usage
        user_workspaces = await workspaces_collection.count_documents({"owner_id": current_user["id"]})
        user_bio_sites = await database.bio_sites.count_documents({"owner_id": current_user["id"]})
        
        # Mock AI usage (you'd track this in practice)
        ai_requests_used = 5  # This would come from actual usage tracking
        
        usage_data = {
            "ai_requests": ai_requests_used,
            "ai_requests_limit": current_plan["limits"]["ai_requests"],
            "workspaces": user_workspaces,
            "workspaces_limit": current_plan["limits"]["workspaces"], 
            "bio_sites": user_bio_sites,
            "bio_sites_limit": current_plan["limits"]["bio_sites"],
            "storage_used": 0.2,  # GB - mock data
            "storage_limit": current_plan["limits"]["storage_gb"]
        }
        
        # Get subscription history
        all_subscriptions = await subscriptions_collection.find(
            {"user_id": current_user["id"]}
        ).sort("created_at", -1).limit(10).to_list(length=10)
        
        return {
            "success": True,
            "data": {
                "current_plan": {
                    "id": current_plan_id,
                    "name": current_plan["name"],
                    "price": current_plan["price"], 
                    "status": current_subscription.get("status", "active") if current_subscription else "active",
                    "current_period_end": current_subscription.get("current_period_end") if current_subscription else None
                },
                "usage": usage_data,
                "subscriptions": [
                    {
                        "id": str(sub["_id"]),
                        "plan_name": SUBSCRIPTION_PLANS.get(sub["plan_id"], {}).get("name", "Unknown"),
                        "amount": sub.get("amount", 0),
                        "status": sub.get("status", "unknown"),
                        "created_at": sub["created_at"].isoformat(),
                        "current_period_end": sub.get("current_period_end", datetime.utcnow()).isoformat()
                    } for sub in all_subscriptions
                ]
            }
        }
        
    except Exception as e:
        raise HTTPException(
            status_code=500,
            detail=f"Failed to get subscription data: {str(e)}"
        )

@router.post("/upgrade")
async def upgrade_subscription(
    upgrade_request: UpgradeRequest,
    current_user: dict = Depends(get_current_user)
):
    """Upgrade to a new subscription plan"""
    
    try:
        target_plan = SUBSCRIPTION_PLANS.get(upgrade_request.plan_id)
        if not target_plan:
            raise HTTPException(status_code=400, detail="Invalid plan ID")
        
        # For free plan, no Stripe interaction needed
        if upgrade_request.plan_id == "free":
            # Cancel any existing subscription
            await subscriptions_collection.update_many(
                {"user_id": current_user["id"], "status": "active"},
                {"$set": {"status": "cancelled", "cancelled_at": datetime.utcnow()}}
            )
            
            return {
                "success": True,
                "message": "Downgraded to free plan successfully"
            }
        
        # For paid plans, create mock subscription for development
        subscription_doc = {
            "_id": str(uuid.uuid4()),
            "user_id": current_user["id"],
            "plan_id": upgrade_request.plan_id,
            "status": "active",
            "amount": target_plan["price"],
            "currency": "usd",
            "interval": target_plan["interval"],
            "current_period_start": datetime.utcnow(),
            "current_period_end": datetime.utcnow() + timedelta(days=30),
            "stripe_subscription_id": None,
            "created_at": datetime.utcnow()
        }
        
        # Cancel existing subscriptions
        await subscriptions_collection.update_many(
            {"user_id": current_user["id"], "status": "active"},
            {"$set": {"status": "cancelled", "cancelled_at": datetime.utcnow()}}
        )
        
        # Create new subscription
        await subscriptions_collection.insert_one(subscription_doc)
        
        # Update user subscription plan
        await users_collection.update_one(
            {"_id": current_user["_id"]},
            {"$set": {"subscription_plan": upgrade_request.plan_id}}
        )
        
        return {
            "success": True,
            "message": f"Successfully upgraded to {target_plan['name']}",
            "data": {
                "plan": target_plan["name"],
                "price": target_plan["price"],
                "features": target_plan["features"]
            }
        }
        
    except Exception as e:
        raise HTTPException(
            status_code=500,
            detail=f"Failed to upgrade subscription: {str(e)}"
        )

@router.post("/cancel")
async def cancel_subscription(current_user: dict = Depends(get_current_user)):
    """Cancel current subscription"""
    
    try:
        # Get current active subscription
        current_subscription = await subscriptions_collection.find_one({
            "user_id": current_user["id"],
            "status": "active"
        })
        
        if not current_subscription:
            raise HTTPException(status_code=404, detail="No active subscription found")
        
        # Update subscription status
        await subscriptions_collection.update_one(
            {"_id": current_subscription["_id"]},
            {
                "$set": {
                    "status": "cancelled",
                    "cancelled_at": datetime.utcnow(),
                    "cancel_at_period_end": True
                }
            }
        )
        
        return {
            "success": True,
            "message": "Subscription cancelled successfully. You'll retain access until the end of your billing period."
        }
        
    except Exception as e:
        raise HTTPException(
            status_code=500,
            detail=f"Failed to cancel subscription: {str(e)}"
        )

@router.get("/usage")
async def get_usage_statistics(current_user: dict = Depends(get_current_user)):
    """Get detailed usage statistics"""
    
    try:
        # Get current plan limits
        user_data = await users_collection.find_one({"_id": current_user["_id"]})
        current_plan_id = user_data.get("subscription_plan", "free")
        current_plan = SUBSCRIPTION_PLANS.get(current_plan_id, SUBSCRIPTION_PLANS["free"])
        
        # Calculate actual usage
        workspaces_count = await workspaces_collection.count_documents({"owner_id": current_user["id"]})
        bio_sites_count = await database.bio_sites.count_documents({"owner_id": current_user["id"]})
        
        # Mock data for other usage metrics (implement actual tracking)
        usage_stats = {
            "period": {
                "start": datetime.utcnow().replace(day=1).isoformat(),
                "end": (datetime.utcnow().replace(day=1) + timedelta(days=32)).replace(day=1).isoformat()
            },
            "ai_requests": {
                "used": 5,
                "limit": current_plan["limits"]["ai_requests"],
                "percentage": 50.0 if current_plan["limits"]["ai_requests"] > 0 else 0
            },
            "workspaces": {
                "used": workspaces_count,
                "limit": current_plan["limits"]["workspaces"],
                "percentage": (workspaces_count / current_plan["limits"]["workspaces"]) * 100 if current_plan["limits"]["workspaces"] > 0 else 0
            },
            "bio_sites": {
                "used": bio_sites_count,
                "limit": current_plan["limits"]["bio_sites"],
                "percentage": (bio_sites_count / current_plan["limits"]["bio_sites"]) * 100 if current_plan["limits"]["bio_sites"] > 0 else 20
            },
            "storage": {
                "used_gb": 0.5,
                "limit_gb": current_plan["limits"]["storage_gb"],
                "percentage": (0.5 / current_plan["limits"]["storage_gb"]) * 100 if current_plan["limits"]["storage_gb"] > 0 else 5
            },
            "features_enabled": current_plan["features"],
            "plan_name": current_plan["name"]
        }
        
        return {
            "success": True,
            "data": usage_stats
        }
        
    except Exception as e:
        raise HTTPException(
            status_code=500,
            detail=f"Failed to get usage statistics: {str(e)}"
        )

@router.post("/trial/start")
async def start_trial(current_user: dict = Depends(get_current_user)):
    """Start a trial period for premium features"""
    
    try:
        # Check if user already had a trial
        existing_trial = await subscriptions_collection.find_one({
            "user_id": current_user["id"],
            "status": {"$in": ["trialing", "trial_ended"]}
        })
        
        if existing_trial:
            raise HTTPException(status_code=400, detail="Trial already used")
        
        # Create trial subscription
        trial_doc = {
            "_id": str(uuid.uuid4()),
            "user_id": current_user["id"],
            "plan_id": "pro",
            "status": "trialing",
            "amount": 0,
            "currency": "usd",
            "interval": "month",
            "current_period_start": datetime.utcnow(),
            "current_period_end": datetime.utcnow() + timedelta(days=14),  # 14-day trial
            "trial_start": datetime.utcnow(),
            "trial_end": datetime.utcnow() + timedelta(days=14),
            "created_at": datetime.utcnow()
        }
        
        await subscriptions_collection.insert_one(trial_doc)
        
        # Update user
        await users_collection.update_one(
            {"_id": current_user["_id"]},
            {"$set": {"subscription_plan": "pro", "trial_started": True}}
        )
        
        return {
            "success": True,
            "message": "14-day trial started successfully",
            "data": {
                "trial_end": trial_doc["trial_end"].isoformat(),
                "plan": "pro"
            }
        }
        
    except Exception as e:
        raise HTTPException(
            status_code=500,
            detail=f"Failed to start trial: {str(e)}"
        )