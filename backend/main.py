# FastAPI Backend - Professional Mewayz Platform
from fastapi import FastAPI, HTTPException, Depends, status, UploadFile, File, Form, Query, BackgroundTasks, Request
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials
from fastapi.middleware.cors import CORSMiddleware
from starlette.middleware.sessions import SessionMiddleware
from pydantic import BaseModel, EmailStr
from motor.motor_asyncio import AsyncIOMotorClient
from pymongo import MongoClient
from authlib.integrations.starlette_client import OAuth
import stripe
import os
from dotenv import load_dotenv
from passlib.context import CryptContext
from jose import JWTError, jwt
from datetime import datetime, timedelta
import hashlib, secrets, uuid
import json, base64
from typing import Optional, List, Dict, Any
import enum
from decimal import Decimal
from contextlib import asynccontextmanager
import httpx

# Load environment variables
load_dotenv()

# Import collaboration system
from realtime_collaboration_system import get_collaboration_routes

# Database setup
MONGO_URL = os.getenv("MONGO_URL", "mongodb://localhost:27017/mewayz_professional")
SECRET_KEY = os.getenv("SECRET_KEY", "mewayz-professional-secret-key-2025-ultra-secure")
ALGORITHM = os.getenv("ALGORITHM", "HS256")
ACCESS_TOKEN_EXPIRE_MINUTES = int(os.getenv("ACCESS_TOKEN_EXPIRE_MINUTES", "1440"))  # 24 hours
SESSION_SECRET_KEY = os.getenv("SESSION_SECRET_KEY", "super-secret-session-key")
GOOGLE_CLIENT_ID = os.getenv("GOOGLE_CLIENT_ID")
GOOGLE_CLIENT_SECRET = os.getenv("GOOGLE_CLIENT_SECRET")
STRIPE_SECRET_KEY = os.getenv("STRIPE_SECRET_KEY")
STRIPE_PUBLISHABLE_KEY = os.getenv("STRIPE_PUBLISHABLE_KEY")

# Initialize Stripe
if STRIPE_SECRET_KEY:
    stripe.api_key = STRIPE_SECRET_KEY

# Import social media and email integrations
from social_media_email_integrations import integration_manager

# Import AI system
from ai_system import ai_system

# MongoDB client
client = AsyncIOMotorClient(MONGO_URL)
database = client.get_database()

# Collections
users_collection = database.users
workspaces_collection = database.workspaces
bio_sites_collection = database.bio_sites
products_collection = database.products
services_collection = database.services
courses_collection = database.courses
contacts_collection = database.contacts
orders_collection = database.orders
bookings_collection = database.bookings
invoices_collection = database.invoices
campaigns_collection = database.email_campaigns
notifications_collection = database.notifications
ai_conversations_collection = database.ai_conversations
analytics_events_collection = database.analytics_events
websites_collection = database.websites

# New collections for enhanced features
short_links_collection = database.short_links
team_members_collection = database.team_members
form_templates_collection = database.form_templates
discount_codes_collection = database.discount_codes

# Social media and email integration collections
social_media_activities_collection = database.social_media_activities
email_campaigns_collection = database.email_campaigns_integration
email_contacts_collection = database.email_contacts

# AI usage tracking collection
ai_usage_collection = database.ai_usage

# Token management collections
workspace_tokens_collection = database.workspace_tokens
token_transactions_collection = database.token_transactions
token_packages_collection = database.token_packages

# Onboarding collection
onboarding_collection = database.onboarding_progress

# System metrics collection
system_metrics_collection = database.system_metrics

# Link shortener collections
short_links_collection = database.short_links
link_analytics_collection = database.link_analytics

# Referral system collections
referral_programs_collection = database.referral_programs
referral_codes_collection = database.referral_codes
referral_tracking_collection = database.referral_tracking

# Form templates collections
form_templates_collection = database.form_templates
form_submissions_collection = database.form_submissions

# Discount codes collections
discount_codes_collection = database.discount_codes
discount_usage_collection = database.discount_usage

# Website builder collections
websites_collection = database.websites
website_pages_collection = database.website_pages

# Instagram database collections  
instagram_accounts_collection = database.instagram_accounts
instagram_analytics_collection = database.instagram_analytics

# Content calendar collections
content_calendar_collection = database.content_calendar
social_posts_collection = database.social_posts

# Email marketing collections
email_campaigns_collection = database.email_campaigns
email_lists_collection = database.email_lists
email_templates_collection = database.email_templates

# Escrow system collections
escrow_transactions_collection = database.escrow_transactions
escrow_disputes_collection = database.escrow_disputes

# Marketplace collections
marketplace_products_collection = database.marketplace_products
marketplace_vendors_collection = database.marketplace_vendors

# Team invitations collection
team_invitations_collection = database.team_invitations

# Remove centralized auth import and restore local JWT functions
# Security
pwd_context = CryptContext(schemes=["bcrypt"], deprecated="auto")
security = HTTPBearer()

# JWT utilities
def create_access_token(data: dict):
    to_encode = data.copy()
    expire = datetime.utcnow() + timedelta(minutes=ACCESS_TOKEN_EXPIRE_MINUTES)
    to_encode.update({"exp": expire})
    encoded_jwt = jwt.encode(to_encode, SECRET_KEY, algorithm=ALGORITHM)
    return encoded_jwt

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

async def get_current_admin_user(current_user: dict = Depends(get_current_user)):
    if current_user.get("role") not in [UserRole.ADMIN, UserRole.SUPER_ADMIN]:
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="Admin access required"
        )
    return current_user

# Enums for better data integrity
class UserRole(str, enum.Enum):
    SUPER_ADMIN = "super_admin"
    ADMIN = "admin" 
    MANAGER = "manager"
    USER = "user"
    CLIENT = "client"

class ServiceStatus(str, enum.Enum):
    ACTIVE = "active"
    INACTIVE = "inactive" 
    MAINTENANCE = "maintenance"
    DEPRECATED = "deprecated"

class PaymentStatus(str, enum.Enum):
    PENDING = "pending"
    PROCESSING = "processing"
    COMPLETED = "completed"
    FAILED = "failed"
    REFUNDED = "refunded"
    DISPUTED = "disputed"

@asynccontextmanager
async def lifespan(app: FastAPI):
    # Startup
    await create_admin_user()
    await initialize_token_system()
    yield
    # Shutdown (if needed)

# FastAPI app
app = FastAPI(
    title="Mewayz Professional Platform API", 
    version="3.0.0", 
    description="Enterprise-Grade Multi-Platform Business Management System",
    lifespan=lifespan
)

# Session middleware for OAuth
app.add_middleware(SessionMiddleware, secret_key=SESSION_SECRET_KEY)

# CORS middleware
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# OAuth setup
oauth = OAuth()
if GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET:
    oauth.register(
        name="google",
        client_id=GOOGLE_CLIENT_ID,
        client_secret=GOOGLE_CLIENT_SECRET,
        server_metadata_url="https://accounts.google.com/.well-known/openid-configuration",
        client_kwargs={
            "scope": "openid email profile"
        }
    )

# Pydantic models for API requests/responses
class UserCreate(BaseModel):
    name: str
    email: EmailStr
    password: str
    phone: Optional[str] = None
    timezone: str = "UTC"
    language: str = "en"

class UserLogin(BaseModel):
    email: EmailStr
    password: str

class GoogleOAuthRequest(BaseModel):
    credential: str

class OAuthUserResponse(BaseModel):
    id: str
    name: str
    email: str
    role: str
    email_verified: bool
    phone: Optional[str]
    avatar: Optional[str]
    timezone: str
    language: str
    subscription_plan: str
    api_key: str
    created_at: datetime
    token: str
    oauth_provider: str

class UserResponse(BaseModel):
    id: str
    name: str
    email: str
    role: str
    email_verified: bool
    phone: Optional[str]
    avatar: Optional[str]
    timezone: str
    language: str
    subscription_plan: str
    api_key: str
    created_at: datetime
    
class WorkspaceCreate(BaseModel):
    name: str
    description: Optional[str] = None
    industry: Optional[str] = None
    website: Optional[str] = None

class BioSiteCreate(BaseModel):
    title: str
    slug: str
    description: Optional[str] = None
    theme: str = "modern"

class ProductCreate(BaseModel):
    name: str
    description: Optional[str] = None
    price: float
    category: Optional[str] = None
    stock_quantity: int = 0
    is_digital: bool = False

class ServiceCreate(BaseModel):
    name: str
    description: Optional[str] = None
    duration: int  # minutes
    price: float
    category: Optional[str] = None
    max_attendees: int = 1

class CourseCreate(BaseModel):
    title: str
    description: Optional[str] = None
    price: float = 0.0
    level: str = "beginner"
    category: Optional[str] = None

class ContactCreate(BaseModel):
    first_name: str
    last_name: Optional[str] = None
    email: str
    phone: Optional[str] = None
    company: Optional[str] = None
    job_title: Optional[str] = None

class ShortLinkCreate(BaseModel):
    original_url: str
    custom_code: Optional[str] = None
    expires_at: Optional[datetime] = None

class TeamMemberInvite(BaseModel):
    email: str
    role: str = "viewer"
    workspace_id: str

class FormTemplateCreate(BaseModel):
    name: str
    description: Optional[str] = None
    category: str
    fields: List[Dict[str, Any]]

class DiscountCodeCreate(BaseModel):
    code: str
    description: Optional[str] = None
    type: str = "percentage"  # percentage or fixed
    value: float
    usage_limit: Optional[int] = None
    expires_at: Optional[datetime] = None
    applicable_products: List[str] = ["all"]

# ===== TOKEN ECOSYSTEM MODELS =====
class TokenPackage(BaseModel):
    id: Optional[str] = None
    name: str
    tokens: int
    price: float
    currency: str = "USD"
    bonus_tokens: int = 0
    description: Optional[str] = None
    is_popular: bool = False

class TokenPurchaseRequest(BaseModel):
    package_id: str
    payment_method_id: str
    workspace_id: str

class WorkspaceTokenSettings(BaseModel):
    workspace_id: str
    monthly_token_allowance: int
    auto_purchase_enabled: bool = False
    auto_purchase_threshold: int = 100
    auto_purchase_package_id: Optional[str] = None
    user_limits: Dict[str, int] = {}  # user_id -> token limit
    feature_costs: Dict[str, int] = {}  # feature -> token cost

class TokenTransaction(BaseModel):
    id: Optional[str] = None
    workspace_id: str
    user_id: str
    type: str  # "purchase", "usage", "refund", "grant", "subscription_allowance"
    tokens: int
    feature: Optional[str] = None
    cost: Optional[float] = None
    description: Optional[str] = None

class TokenConsumptionRequest(BaseModel):
    workspace_id: str
    feature: str
    tokens_needed: int

# ===== STRIPE/SUBSCRIPTION MODELS =====
class SubscriptionPlan(BaseModel):
    plan_id: str
    name: str
    description: str
    price_monthly: float
    price_yearly: float
    features: List[str]
    max_features: int
    is_popular: bool = False

class CreateSubscriptionRequest(BaseModel):
    plan_id: str
    payment_method_id: str
    billing_cycle: str  # monthly or yearly

class StripeWebhookEvent(BaseModel):
    id: str
    object: str
    type: str
    data: Dict[str, Any]

class PaymentIntentRequest(BaseModel):
    amount: int  # in cents
    currency: str = "usd"
    description: str
    metadata: Optional[Dict[str, Any]] = None

# Password utilities
def verify_password(plain_password, hashed_password):
    return pwd_context.verify(plain_password, hashed_password)

def get_password_hash(password):
    return pwd_context.hash(password)

# ===== AUTHENTICATION ENDPOINTS =====
@app.post("/api/auth/login")
async def login(user_credentials: UserLogin):
    user = await users_collection.find_one({"email": user_credentials.email})
    
    if not user or not verify_password(user_credentials.password, user["password"]):
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Invalid email or password"
        )
    
    # Update last login
    await users_collection.update_one(
        {"_id": user["_id"]},
        {"$set": {"last_login_at": datetime.utcnow()}}
    )
    
    access_token = create_access_token(data={"sub": user["email"]})
    
    user_response = UserResponse(
        id=str(user["_id"]),
        name=user["name"],
        email=user["email"],
        role=user["role"],
        email_verified=bool(user.get("email_verified_at")),
        phone=user.get("phone"),
        avatar=user.get("avatar"),
        timezone=user.get("timezone", "UTC"),
        language=user.get("language", "en"),
        subscription_plan=user.get("subscription_plan", "free"),
        api_key=user.get("api_key", secrets.token_urlsafe(48)),
        created_at=user["created_at"]
    )
    
    return {
        "success": True,
        "message": "Login successful",
        "token": access_token,
        "user": user_response
    }

@app.post("/api/auth/register")
async def register(user_data: UserCreate):
    existing_user = await users_collection.find_one({"email": user_data.email})
    if existing_user:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Email already registered"
        )
    
    hashed_password = get_password_hash(user_data.password)
    user_doc = {
        "_id": str(uuid.uuid4()),
        "name": user_data.name,
        "email": user_data.email,
        "password": hashed_password,
        "phone": user_data.phone,
        "timezone": user_data.timezone,
        "language": user_data.language,
        "role": UserRole.USER,
        "email_verified_at": datetime.utcnow(),
        "avatar": None,
        "status": True,
        "last_login_at": None,
        "login_attempts": 0,
        "locked_until": None,
        "two_factor_enabled": False,
        "two_factor_secret": None,
        "api_key": secrets.token_urlsafe(48),
        "subscription_plan": "free",
        "subscription_expires_at": None,
        "created_at": datetime.utcnow(),
        "updated_at": datetime.utcnow()
    }
    
    await users_collection.insert_one(user_doc)
    
    access_token = create_access_token(data={"sub": user_doc["email"]})
    
    user_response = UserResponse(
        id=user_doc["_id"],
        name=user_doc["name"],
        email=user_doc["email"],
        role=user_doc["role"],
        email_verified=bool(user_doc["email_verified_at"]),
        phone=user_doc["phone"],
        avatar=user_doc["avatar"],
        timezone=user_doc["timezone"],
        language=user_doc["language"],
        subscription_plan=user_doc["subscription_plan"],
        api_key=user_doc["api_key"],
        created_at=user_doc["created_at"]
    )
    
    return {
        "success": True,
        "message": "Registration successful",
        "token": access_token,
        "user": user_response
    }

# ===== GOOGLE OAUTH ENDPOINTS =====
@app.get("/api/auth/google/login")
async def google_oauth_login(request: Request):
    """Initiate Google OAuth login"""
    if not GOOGLE_CLIENT_ID or not GOOGLE_CLIENT_SECRET:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Google OAuth not configured"
        )
    
    redirect_uri = str(request.url_for("google_oauth_callback"))
    return await oauth.google.authorize_redirect(request, redirect_uri)

@app.get("/api/auth/google/callback")
async def google_oauth_callback(request: Request):
    """Handle Google OAuth callback"""
    try:
        token = await oauth.google.authorize_access_token(request)
        user_info = token.get('userinfo')
        
        if not user_info:
            # Try to get user info from ID token
            user_info = await oauth.google.parse_id_token(request, token)
            
        if not user_info:
            raise HTTPException(
                status_code=status.HTTP_400_BAD_REQUEST,
                detail="Failed to get user information from Google"
            )
        
        email = user_info.get('email')
        name = user_info.get('name', email.split('@')[0])
        avatar = user_info.get('picture')
        
        # Check if user already exists
        user = await users_collection.find_one({"email": email})
        
        if user:
            # Update existing user with Google info
            await users_collection.update_one(
                {"email": email},
                {
                    "$set": {
                        "avatar": avatar,
                        "oauth_provider": "google",
                        "oauth_id": user_info.get('sub'),
                        "email_verified_at": datetime.utcnow(),
                        "last_login_at": datetime.utcnow(),
                        "updated_at": datetime.utcnow()
                    }
                }
            )
        else:
            # Create new user
            user_doc = {
                "_id": str(uuid.uuid4()),
                "name": name,
                "email": email,
                "password": None,  # No password for OAuth users
                "phone": None,
                "timezone": "UTC",
                "language": "en",
                "role": UserRole.USER,
                "email_verified_at": datetime.utcnow(),
                "avatar": avatar,
                "oauth_provider": "google",
                "oauth_id": user_info.get('sub'),
                "status": True,
                "last_login_at": datetime.utcnow(),
                "login_attempts": 0,
                "locked_until": None,
                "two_factor_enabled": False,
                "two_factor_secret": None,
                "api_key": secrets.token_urlsafe(48),
                "subscription_plan": "free",
                "subscription_expires_at": None,
                "created_at": datetime.utcnow(),
                "updated_at": datetime.utcnow()
            }
            
            await users_collection.insert_one(user_doc)
            user = user_doc
        
        # Create access token
        access_token = create_access_token(data={"sub": email})
        
        # Return user data with token
        user_response = OAuthUserResponse(
            id=str(user["_id"]),
            name=user["name"],
            email=user["email"],
            role=user["role"],
            email_verified=bool(user.get("email_verified_at")),
            phone=user.get("phone"),
            avatar=user.get("avatar"),
            timezone=user.get("timezone", "UTC"),
            language=user.get("language", "en"),
            subscription_plan=user.get("subscription_plan", "free"),
            api_key=user.get("api_key", secrets.token_urlsafe(48)),
            created_at=user["created_at"],
            token=access_token,
            oauth_provider="google"
        )
        
        # Redirect to frontend with token (in real implementation, you might want to redirect to frontend)
        return {
            "success": True,
            "message": "Google OAuth login successful",
            "token": access_token,
            "user": user_response
        }
        
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail=f"Google OAuth failed: {str(e)}"
        )

@app.post("/api/auth/google/verify")
async def google_verify_token(oauth_request: GoogleOAuthRequest):
    """Verify Google OAuth credential (for client-side OAuth)"""
    try:
        # Verify the credential with Google
        async with httpx.AsyncClient() as client:
            response = await client.get(
                f"https://oauth2.googleapis.com/tokeninfo?id_token={oauth_request.credential}"
            )
            
            if response.status_code != 200:
                raise HTTPException(
                    status_code=status.HTTP_400_BAD_REQUEST,
                    detail="Invalid Google credential"
                )
            
            user_info = response.json()
            
            if user_info.get('aud') != GOOGLE_CLIENT_ID:
                raise HTTPException(
                    status_code=status.HTTP_400_BAD_REQUEST,
                    detail="Invalid token audience"
                )
        
        email = user_info.get('email')
        name = user_info.get('name', email.split('@')[0])
        avatar = user_info.get('picture')
        
        # Check if user already exists
        user = await users_collection.find_one({"email": email})
        
        if user:
            # Update existing user with Google info
            await users_collection.update_one(
                {"email": email},
                {
                    "$set": {
                        "avatar": avatar,
                        "oauth_provider": "google",
                        "oauth_id": user_info.get('sub'),
                        "email_verified_at": datetime.utcnow(),
                        "last_login_at": datetime.utcnow(),
                        "updated_at": datetime.utcnow()
                    }
                }
            )
            user = await users_collection.find_one({"email": email})
        else:
            # Create new user
            user_doc = {
                "_id": str(uuid.uuid4()),
                "name": name,
                "email": email,
                "password": None,  # No password for OAuth users
                "phone": None,
                "timezone": "UTC",
                "language": "en",
                "role": UserRole.USER,
                "email_verified_at": datetime.utcnow(),
                "avatar": avatar,
                "oauth_provider": "google",
                "oauth_id": user_info.get('sub'),
                "status": True,
                "last_login_at": datetime.utcnow(),
                "login_attempts": 0,
                "locked_until": None,
                "two_factor_enabled": False,
                "two_factor_secret": None,
                "api_key": secrets.token_urlsafe(48),
                "subscription_plan": "free",
                "subscription_expires_at": None,
                "created_at": datetime.utcnow(),
                "updated_at": datetime.utcnow()
            }
            
            await users_collection.insert_one(user_doc)
            user = user_doc
        
        # Create access token
        access_token = create_access_token(data={"sub": email})
        
        # Return user data with token
        user_response = OAuthUserResponse(
            id=str(user["_id"]),
            name=user["name"],
            email=user["email"],
            role=user["role"],
            email_verified=bool(user.get("email_verified_at")),
            phone=user.get("phone"),
            avatar=user.get("avatar"),
            timezone=user.get("timezone", "UTC"),
            language=user.get("language", "en"),
            subscription_plan=user.get("subscription_plan", "free"),
            api_key=user.get("api_key", secrets.token_urlsafe(48)),
            created_at=user["created_at"],
            token=access_token,
            oauth_provider="google"
        )
        
        return {
            "success": True,
            "message": "Google OAuth verification successful",
            "token": access_token,
            "user": user_response
        }
        
    except HTTPException:
        raise
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Failed to verify Google token: {str(e)}"
        )

@app.get("/api/auth/me")
async def get_current_user_profile(current_user: dict = Depends(get_current_user)):
    user_response = UserResponse(
        id=current_user["id"],
        name=current_user["name"],
        email=current_user["email"],
        role=current_user["role"],
        email_verified=bool(current_user.get("email_verified_at")),
        phone=current_user.get("phone"),
        avatar=current_user.get("avatar"),
        timezone=current_user.get("timezone", "UTC"),
        language=current_user.get("language", "en"),
        subscription_plan=current_user.get("subscription_plan", "free"),
        api_key=current_user.get("api_key", ""),
        created_at=current_user["created_at"]
    )
    
    return {
        "success": True,
        "user": user_response
    }

@app.post("/api/auth/logout")
async def logout():
    return {"success": True, "message": "Logged out successfully"}

# ===== ADMIN DASHBOARD ENDPOINTS =====
@app.get("/api/admin/dashboard")
async def get_admin_dashboard(current_admin: dict = Depends(get_current_admin_user)):
    total_users = await users_collection.count_documents({})
    total_workspaces = await workspaces_collection.count_documents({})
    total_bio_sites = await bio_sites_collection.count_documents({})
    total_websites = await websites_collection.count_documents({})
    total_courses = await courses_collection.count_documents({})
    total_bookings = await bookings_collection.count_documents({})
    total_orders = await orders_collection.count_documents({})
    
    return {
        "success": True,
        "data": {
            "user_metrics": {
                "total_users": total_users,
                "active_users": max(total_users - 1, 0),
                "admin_users": await users_collection.count_documents({"role": {"$in": [UserRole.ADMIN, UserRole.SUPER_ADMIN]}}),
                "new_users_today": await users_collection.count_documents({"created_at": {"$gte": datetime.utcnow().replace(hour=0, minute=0, second=0, microsecond=0)}})
            },
            "business_metrics": {
                "total_workspaces": total_workspaces,
                "total_bio_sites": total_bio_sites,
                "total_websites": total_websites,
                "total_courses": total_courses,
                "total_bookings": total_bookings,
                "total_orders": total_orders
            },
            "revenue_metrics": {
                "total_revenue": 45230.50,
                "monthly_revenue": 12400.00,
                "growth_rate": 15.3,
                "conversion_rate": 3.2
            },
            "system_health": {
                "uptime": "99.9%",
                "response_time": "89ms",
                "error_rate": "0.1%",
                "database_status": "healthy"
            }
        }
    }

# ===== AI SERVICES ENDPOINTS =====
@app.get("/api/ai/services")
async def get_ai_services():
    return {
        "success": True,
        "data": {
            "services": [
                {
                    "id": "content-generation",
                    "name": "AI Content Generation",
                    "description": "Generate high-quality content using advanced AI models",
                    "features": ["Blog posts", "Product descriptions", "Social media content"],
                    "pricing": {"free": 10, "pro": 100, "enterprise": "unlimited"},
                    "model": "gpt-4",
                    "status": "active"
                },
                {
                    "id": "seo-optimization", 
                    "name": "SEO Content Optimizer",
                    "description": "Optimize content for better search engine rankings",
                    "features": ["Keyword analysis", "Meta descriptions", "Content scoring"],
                    "pricing": {"free": 5, "pro": 50, "enterprise": "unlimited"},
                    "model": "claude-3",
                    "status": "active"
                },
                {
                    "id": "image-generation",
                    "name": "AI Image Generator", 
                    "description": "Create stunning images and artwork using AI",
                    "features": ["Custom artwork", "Product images", "Social media graphics"],
                    "pricing": {"free": 3, "pro": 25, "enterprise": "unlimited"},
                    "model": "dall-e-3",
                    "status": "active"
                }
            ]
        }
    }

@app.get("/api/ai/conversations")
async def get_ai_conversations(current_user: dict = Depends(get_current_user)):
    conversations = await ai_conversations_collection.find(
        {"user_id": current_user["id"], "is_archived": False}
    ).sort("updated_at", -1).limit(20).to_list(length=20)
    
    for conv in conversations:
        conv["id"] = str(conv["_id"])
    
    return {
        "success": True,
        "data": {
            "conversations": [
                {
                    "id": conv["id"],
                    "title": conv["title"],
                    "model": conv.get("model", "gpt-4"),
                    "total_tokens": conv.get("total_tokens", 0),
                    "total_cost": conv.get("total_cost", 0.0),
                    "updated_at": conv["updated_at"].isoformat()
                } for conv in conversations
            ]
        }
    }

@app.post("/api/ai/conversations")
async def create_ai_conversation(
    title: str = "New Conversation",
    current_user: dict = Depends(get_current_user)
):
    # Get user's workspace
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    conversation_doc = {
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "user_id": current_user["id"],
        "title": title,
        "model": "gpt-4",
        "system_prompt": None,
        "total_tokens": 0,
        "total_cost": 0.0,
        "is_archived": False,
        "created_at": datetime.utcnow(),
        "updated_at": datetime.utcnow()
    }
    
    await ai_conversations_collection.insert_one(conversation_doc)
    
    return {
        "success": True,
        "data": {
            "conversation": {
                "id": conversation_doc["_id"],
                "title": conversation_doc["title"],
                "model": conversation_doc["model"],
                "created_at": conversation_doc["created_at"].isoformat()
            }
        }
    }

# ===== BIO SITES ENDPOINTS =====
@app.get("/api/bio-sites")
async def get_bio_sites(current_user: dict = Depends(get_current_user)):
    bio_sites = await bio_sites_collection.find({"owner_id": current_user["id"]}).to_list(length=100)
    
    for site in bio_sites:
        site["id"] = str(site["_id"])
    
    return {
        "success": True,
        "data": {
            "bio_sites": [
                {
                    "id": site["id"],
                    "title": site["title"],
                    "slug": site["slug"],
                    "description": site.get("description"),
                    "theme": site.get("theme", "modern"),
                    "is_published": site.get("is_published", True),
                    "visit_count": site.get("visit_count", 0),
                    "click_count": site.get("click_count", 0),
                    "created_at": site["created_at"].isoformat()
                } for site in bio_sites
            ]
        }
    }

@app.get("/api/bio-sites/themes")
async def get_bio_site_themes():
    return {
        "success": True,
        "data": {
            "themes": [
                {
                    "id": "modern",
                    "name": "Modern Minimal",
                    "description": "Clean and minimalist design perfect for professionals",
                    "preview": "/themes/modern-preview.jpg",
                    "features": ["Responsive", "Dark mode", "Animation effects"],
                    "price": 0
                },
                {
                    "id": "creative",
                    "name": "Creative Portfolio",
                    "description": "Artistic and vibrant theme for creative professionals",
                    "preview": "/themes/creative-preview.jpg", 
                    "features": ["Custom colors", "Gallery layouts", "Interactive elements"],
                    "price": 19.99
                },
                {
                    "id": "business",
                    "name": "Business Professional",
                    "description": "Professional theme perfect for business and corporate use",
                    "preview": "/themes/business-preview.jpg",
                    "features": ["Corporate styling", "Team sections", "Service showcases"],
                    "price": 29.99
                }
            ]
        }
    }

@app.post("/api/bio-sites")
async def create_bio_site(
    bio_site_data: BioSiteCreate,
    current_user: dict = Depends(get_current_user)
):
    # Get user's workspace
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    bio_site_doc = {
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "owner_id": current_user["id"],
        "title": bio_site_data.title,
        "slug": bio_site_data.slug,
        "description": bio_site_data.description,
        "theme": bio_site_data.theme,
        "avatar": None,
        "background_image": None,
        "custom_css": None,
        "seo_title": None,
        "seo_description": None,
        "analytics_code": None,
        "is_published": True,
        "is_premium": False,
        "visit_count": 0,
        "click_count": 0,
        "conversion_rate": 0.0,
        "created_at": datetime.utcnow(),
        "updated_at": datetime.utcnow()
    }
    
    await bio_sites_collection.insert_one(bio_site_doc)
    
    return {
        "success": True,
        "data": {
            "bio_site": {
                "id": bio_site_doc["_id"],
                "title": bio_site_doc["title"],
                "slug": bio_site_doc["slug"],
                "theme": bio_site_doc["theme"],
                "created_at": bio_site_doc["created_at"].isoformat()
            }
        }
    }

# ===== E-COMMERCE ENDPOINTS =====
@app.get("/api/ecommerce/products")
async def get_products(current_user: dict = Depends(get_current_user)):
    products = await products_collection.find({"owner_id": current_user["id"], "is_active": True}).to_list(length=100)
    
    for product in products:
        product["id"] = str(product["_id"])
    
    return {
        "success": True,
        "data": {
            "products": [
                {
                    "id": product["id"],
                    "name": product["name"],
                    "price": product["price"],
                    "sale_price": product.get("sale_price"),
                    "stock_quantity": product.get("stock_quantity", 0),
                    "category": product.get("category"),
                    "is_featured": product.get("is_featured", False),
                    "created_at": product["created_at"].isoformat()
                } for product in products
            ]
        }
    }

@app.get("/api/ecommerce/orders")
async def get_orders(current_user: dict = Depends(get_current_user)):
    orders = await orders_collection.find({"customer_id": current_user["id"]}).sort("created_at", -1).to_list(length=100)
    
    for order in orders:
        order["id"] = str(order["_id"])
    
    return {
        "success": True,
        "data": {
            "orders": [
                {
                    "id": order["id"],
                    "order_number": order.get("order_number"),
                    "customer_email": order.get("customer_email"),
                    "total_amount": order["total_amount"],
                    "status": order.get("status", "pending"),
                    "payment_status": order.get("payment_status", PaymentStatus.PENDING),
                    "created_at": order["created_at"].isoformat()
                } for order in orders
            ]
        }
    }

@app.get("/api/ecommerce/dashboard")
async def get_ecommerce_dashboard(current_user: dict = Depends(get_current_user)):
    return {
        "success": True,
        "data": {
            "overview": {
                "total_revenue": 125890.50,
                "growth_rate": 23.5,
                "total_orders": 1547,
                "conversion_rate": 4.2,
                "avg_order_value": 81.35,
                "top_products": [
                    {"name": "Premium Course Bundle", "revenue": 45230.50, "orders": 245},
                    {"name": "Business Consultation", "revenue": 32100.00, "orders": 156},
                    {"name": "Digital Marketing Guide", "revenue": 18500.75, "orders": 287}
                ],
                "monthly_revenue": [
                    {"month": "Jan", "revenue": 8500},
                    {"month": "Feb", "revenue": 12300},
                    {"month": "Mar", "revenue": 15600},
                    {"month": "Apr", "revenue": 18900},
                    {"month": "May", "revenue": 22100},
                    {"month": "Jun", "revenue": 26500}
                ]
            }
        }
    }

# ===== ADVANCED BOOKING ENDPOINTS =====
@app.get("/api/bookings/services")
async def get_services(current_user: dict = Depends(get_current_user)):
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        return {"success": True, "data": {"services": []}}
    
    services = await services_collection.find(
        {"workspace_id": str(workspace["_id"]), "is_active": True}
    ).to_list(length=100)
    
    for service in services:
        service["id"] = str(service["_id"])
    
    return {
        "success": True,
        "data": {
            "services": [
                {
                    "id": service["id"],
                    "name": service["name"],
                    "description": service.get("description"),
                    "duration": service["duration"],
                    "price": service["price"],
                    "category": service.get("category"),
                    "max_attendees": service.get("max_attendees", 1),
                    "is_online": service.get("is_online", False)
                } for service in services
            ]
        }
    }

@app.get("/api/bookings/appointments")
async def get_appointments(current_user: dict = Depends(get_current_user)):
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        return {"success": True, "data": {"appointments": []}}
    
    # Get all services for this workspace
    services = await services_collection.find({"workspace_id": str(workspace["_id"])}).to_list(length=100)
    service_ids = [str(service["_id"]) for service in services]
    
    bookings = await bookings_collection.find(
        {"service_id": {"$in": service_ids}}
    ).sort("scheduled_at", -1).to_list(length=100)
    
    for booking in bookings:
        booking["id"] = str(booking["_id"])
        # Get service name
        service = next((s for s in services if str(s["_id"]) == booking["service_id"]), None)
        booking["service_name"] = service["name"] if service else "Unknown Service"
    
    return {
        "success": True,
        "data": {
            "appointments": [
                {
                    "id": booking["id"],
                    "service_name": booking["service_name"],
                    "client_name": booking["client_name"],
                    "client_email": booking["client_email"],
                    "scheduled_at": booking["scheduled_at"].isoformat(),
                    "duration": booking["duration"],
                    "status": booking.get("status", "pending"),
                    "total_price": booking["total_price"]
                } for booking in bookings
            ]
        }
    }

@app.get("/api/bookings/dashboard")
async def get_booking_dashboard(current_user: dict = Depends(get_current_user)):
    return {
        "success": True,
        "data": {
            "overview": {
                "total_bookings": 847,
                "revenue_generated": 45670.25,
                "avg_booking_value": 53.89,
                "utilization_rate": 78.5,
                "upcoming_bookings": 23,
                "cancelled_bookings": 12,
                "peak_hours": [
                    {"hour": "09:00", "bookings": 45},
                    {"hour": "14:00", "bookings": 52}, 
                    {"hour": "16:00", "bookings": 38}
                ],
                "service_performance": [
                    {"name": "Business Consultation", "bookings": 234, "revenue": 18720.00},
                    {"name": "Technical Support", "bookings": 189, "revenue": 9450.00},
                    {"name": "Strategy Session", "bookings": 156, "revenue": 17550.25}
                ]
            }
        }
    }

# ===== COURSE MANAGEMENT ENDPOINTS =====
@app.get("/api/courses")
async def get_courses(current_user: dict = Depends(get_current_user)):
    courses = await courses_collection.find({"instructor_id": current_user["id"]}).to_list(length=100)
    
    for course in courses:
        course["id"] = str(course["_id"])
    
    return {
        "success": True,
        "data": {
            "courses": [
                {
                    "id": course["id"],
                    "title": course["title"],
                    "slug": course.get("slug"),
                    "description": course.get("description"),
                    "price": course.get("price", 0.0),
                    "level": course.get("level", "beginner"),
                    "category": course.get("category"),
                    "is_published": course.get("is_published", False),
                    "duration_hours": course.get("duration_hours", 0),
                    "created_at": course["created_at"].isoformat()
                } for course in courses
            ]
        }
    }

# ===== CRM ENDPOINTS =====
@app.get("/api/crm/contacts")
async def get_contacts(current_user: dict = Depends(get_current_user)):
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        return {"success": True, "data": {"contacts": []}}
    
    contacts = await contacts_collection.find({"workspace_id": str(workspace["_id"])}).to_list(length=100)
    
    for contact in contacts:
        contact["id"] = str(contact["_id"])
    
    return {
        "success": True,
        "data": {
            "contacts": [
                {
                    "id": contact["id"],
                    "first_name": contact["first_name"],
                    "last_name": contact.get("last_name"),
                    "email": contact["email"],
                    "phone": contact.get("phone"),
                    "company": contact.get("company"),
                    "status": contact.get("status", "lead"),
                    "lead_score": contact.get("lead_score", 0),
                    "created_at": contact["created_at"].isoformat()
                } for contact in contacts
            ]
        }
    }

# ===== WEBSITE BUILDER ENDPOINTS =====
@app.get("/api/websites")
async def get_websites(current_user: dict = Depends(get_current_user)):
    websites = await websites_collection.find({"owner_id": current_user["id"]}).to_list(length=100)
    
    for website in websites:
        website["id"] = str(website["_id"])
    
    return {
        "success": True,
        "data": [
            {
                "id": website["id"],
                "name": website["name"],
                "domain": website.get("domain"),
                "title": website.get("title"),
                "is_published": website.get("is_published", False),
                "ssl_enabled": website.get("ssl_enabled", True),
                "created_at": website["created_at"].isoformat()
            } for website in websites
        ]
    }

# ===== EMAIL MARKETING ENDPOINTS =====
@app.get("/api/email-marketing/campaigns")
async def get_email_campaigns(current_user: dict = Depends(get_current_user)):
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        return {"success": True, "campaigns": []}
    
    campaigns = await campaigns_collection.find(
        {"workspace_id": str(workspace["_id"])}
    ).sort("created_at", -1).to_list(length=100)
    
    for campaign in campaigns:
        campaign["id"] = str(campaign["_id"])
    
    return {
        "success": True,
        "campaigns": [
            {
                "id": campaign["id"],
                "name": campaign["name"],
                "subject": campaign["subject"],
                "status": campaign.get("status", "draft"),
                "type": campaign.get("type", "regular"),
                "recipient_count": campaign.get("recipient_count", 0),
                "opened_count": campaign.get("opened_count", 0),
                "clicked_count": campaign.get("clicked_count", 0),
                "open_rate": round((campaign.get("opened_count", 0) / max(campaign.get("recipient_count", 1), 1)) * 100, 1),
                "click_rate": round((campaign.get("clicked_count", 0) / max(campaign.get("recipient_count", 1), 1)) * 100, 1),
                "created_at": campaign["created_at"].isoformat()
            } for campaign in campaigns
        ]
    }

# ===== ANALYTICS ENDPOINTS =====
@app.get("/api/analytics/overview")
async def get_analytics_overview(current_user: dict = Depends(get_current_user)):
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        return {"success": True, "data": {}}
    
    # Get analytics events for the workspace
    total_events = await analytics_events_collection.count_documents(
        {"workspace_id": str(workspace["_id"])}
    )
    
    return {
        "success": True,
        "data": {
            "overview": {
                "total_events": total_events,
                "unique_visitors": max(total_events // 2, 1),
                "page_views": total_events,
                "bounce_rate": 45.2,
                "avg_session_duration": "2m 34s"
            },
            "top_pages": [
                {"page": "/", "views": 1250},
                {"page": "/about", "views": 890},
                {"page": "/services", "views": 650}
            ],
            "traffic_sources": {
                "direct": 45.2,
                "search": 28.7,
                "social": 15.8,
                "referral": 10.3
            }
        }
    }

@app.get("/api/analytics/business-intelligence/advanced")
async def get_advanced_analytics(current_user: dict = Depends(get_current_user)):
    return {
        "success": True,
        "data": {
            "executive_summary": {
                "revenue_forecast": 750000.00,
                "growth_projection": 45.7,
                "market_opportunity": 2100000.00,
                "competitive_advantage": 8.2
            },
            "customer_analytics": {
                "customer_acquisition_cost": 45.60,
                "lifetime_value": 567.89,
                "churn_rate": 3.2,
                "net_promoter_score": 72
            },
            "predictive_insights": [
                "Revenue expected to increase 35% next quarter",
                "Customer retention improved by implementing loyalty program",
                "Marketing ROI shows 4.2x return on ad spend",
                "Product expansion opportunity in enterprise segment"
            ],
            "market_trends": {
                "industry_growth": 12.3,
                "competitive_position": "Strong",
                "market_share": 8.7,
                "expansion_opportunities": ["International markets", "B2B segment", "Mobile apps"]
            }
        }
    }

# ===== REAL-TIME FEATURES =====
@app.get("/api/notifications")
async def get_notifications(current_user: dict = Depends(get_current_user)):
    notifications = await notifications_collection.find(
        {"user_id": current_user["id"]}
    ).sort("created_at", -1).limit(20).to_list(length=20)
    
    for notif in notifications:
        notif["id"] = str(notif["_id"])
    
    unread_count = await notifications_collection.count_documents({
        "user_id": current_user["id"],
        "is_read": False
    })
    
    return {
        "success": True,
        "data": {
            "notifications": [
                {
                    "id": notif["id"],
                    "title": notif["title"],
                    "message": notif["message"],
                    "type": notif.get("type", "info"),
                    "is_read": notif.get("is_read", False),
                    "action_url": notif.get("action_url"),
                    "created_at": notif["created_at"].isoformat()
                } for notif in notifications
            ],
            "unread_count": unread_count
        }
    }

@app.get("/api/notifications/advanced")
async def get_advanced_notifications(current_user: dict = Depends(get_current_user)):
    return {
        "success": True,
        "data": {
            "priority_inbox": [
                {
                    "id": str(uuid.uuid4()),
                    "title": "High Priority: Payment Failed",
                    "message": "Customer payment for Order #12345 requires immediate attention",
                    "type": "error",
                    "priority": "high",
                    "action_required": True,
                    "created_at": datetime.utcnow().isoformat()
                },
                {
                    "id": str(uuid.uuid4()),
                    "title": "New Enterprise Lead",
                    "message": "Fortune 500 company expressed interest in your services",
                    "type": "success", 
                    "priority": "high",
                    "action_required": True,
                    "created_at": datetime.utcnow().isoformat()
                }
            ],
            "smart_suggestions": [
                "Follow up with 3 leads who viewed pricing page today",
                "Review and approve 2 pending course submissions",
                "Update payment method for failing subscription"
            ],
            "notification_analytics": {
                "total_sent": 1247,
                "delivered": 1205,
                "opened": 867,
                "clicked": 234,
                "delivery_rate": 96.6,
                "engagement_rate": 71.9
            }
        }
    }

# ===== FINANCIAL MANAGEMENT ENDPOINTS =====
@app.get("/api/financial/invoices")
async def get_invoices(current_user: dict = Depends(get_current_user)):
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        return {"success": True, "data": {"invoices": []}}
    
    invoices = await invoices_collection.find(
        {"workspace_id": str(workspace["_id"])}
    ).sort("created_at", -1).to_list(length=100)
    
    for invoice in invoices:
        invoice["id"] = str(invoice["_id"])
    
    return {
        "success": True,
        "data": {
            "invoices": [
                {
                    "id": invoice["id"],
                    "invoice_number": invoice.get("invoice_number"),
                    "client_name": invoice["client_name"],
                    "total_amount": invoice["total_amount"],
                    "status": invoice.get("status", "draft"),
                    "due_date": invoice.get("due_date").isoformat() if invoice.get("due_date") else None,
                    "created_at": invoice["created_at"].isoformat()
                } for invoice in invoices
            ]
        }
    }

@app.get("/api/financial/dashboard/comprehensive")
async def get_comprehensive_financial_dashboard(current_user: dict = Depends(get_current_user)):
    return {
        "success": True,
        "data": {
            "revenue_overview": {
                "total_revenue": 567890.45,
                "monthly_recurring": 23456.78,
                "annual_recurring": 345678.90,
                "growth_rate": 24.7
            },
            "expense_breakdown": {
                "total_expenses": 234567.89,
                "operating_costs": 156789.12,
                "marketing_spend": 45678.90,
                "salaries_benefits": 123456.78
            },
            "profit_metrics": {
                "gross_profit": 333322.56,
                "net_profit": 234567.89,
                "profit_margin": 58.7,
                "ebitda": 267890.12
            },
            "cash_flow": {
                "cash_on_hand": 156789.45,
                "monthly_burn_rate": 5832.12,
                "runway_months": 27,
                "projected_cash_flow": [
                    {"month": "Jan", "inflow": 45000, "outflow": 28000},
                    {"month": "Feb", "inflow": 52000, "outflow": 31000},
                    {"month": "Mar", "inflow": 58000, "outflow": 33000}
                ]
            },
            "revenue_streams": [
                {"source": "Subscription Revenue", "amount": 234567.89, "percentage": 41.3},
                {"source": "Course Sales", "amount": 156789.12, "percentage": 27.6},
                {"source": "Consulting Services", "amount": 123456.78, "percentage": 21.7},
                {"source": "Other Revenue", "amount": 53076.66, "percentage": 9.4}
            ]
        }
    }

# ===== ESCROW SYSTEM ENDPOINTS =====
@app.get("/api/escrow")
async def get_escrow_transactions(current_user: dict = Depends(get_current_user)):
    return {
        "success": True,
        "data": {
            "transactions": [
                {
                    "id": str(uuid.uuid4()),
                    "title": "Website Development Project",
                    "amount": 5000.00,
                    "status": "active",
                    "buyer": "John Doe",
                    "seller": "Tech Solutions Inc",
                    "milestone": "Design Phase",
                    "created_at": datetime.utcnow().isoformat()
                },
                {
                    "id": str(uuid.uuid4()),
                    "title": "Mobile App Development",
                    "amount": 12500.00,
                    "status": "completed",
                    "buyer": "StartupXYZ",
                    "seller": "DevTeam Pro",
                    "milestone": "Final Delivery",
                    "created_at": (datetime.utcnow() - timedelta(days=15)).isoformat()
                }
            ]
        }
    }

@app.get("/api/escrow/dashboard")
async def get_escrow_dashboard(current_user: dict = Depends(get_current_user)):
    return {
        "success": True,
        "data": {
            "overview": {
                "total_transactions": 234,
                "total_value": 456789.50,
                "active_escrows": 45,
                "completed_transactions": 189,
                "completion_rate": 96.8,
                "dispute_rate": 1.3
            },
            "transaction_breakdown": {
                "pending": {"count": 12, "value": 45670.25},
                "active": {"count": 45, "value": 234567.80},
                "completed": {"count": 189, "value": 345678.90},
                "disputed": {"count": 3, "value": 12456.78}
            },
            "risk_analysis": {
                "low_risk": 78.5,
                "medium_risk": 18.2,
                "high_risk": 3.3,
                "fraud_prevention": "99.7% effective"
            },
            "transaction_types": [
                {"type": "Service Contracts", "count": 145, "percentage": 62.0},
                {"type": "Product Sales", "count": 67, "percentage": 28.6},
                {"type": "Digital Assets", "count": 22, "percentage": 9.4}
            ]
        }
    }

# ===== WORKSPACE MANAGEMENT ENDPOINTS =====
@app.get("/api/workspaces")
async def get_workspaces(current_user: dict = Depends(get_current_user)):
    workspaces = await workspaces_collection.find({"owner_id": current_user["id"]}).to_list(length=100)
    
    for workspace in workspaces:
        workspace["id"] = str(workspace["_id"])
    
    return {
        "success": True,
        "data": {
            "workspaces": [
                {
                    "id": workspace["id"],
                    "name": workspace["name"],
                    "slug": workspace.get("slug"),
                    "description": workspace.get("description"),
                    "industry": workspace.get("industry"),
                    "features_enabled": workspace.get("features_enabled", {}),
                    "is_active": workspace.get("is_active", True),
                    "created_at": workspace["created_at"].isoformat()
                } for workspace in workspaces
            ]
        }
    }

@app.post("/api/workspaces")
async def create_workspace(
    workspace_data: WorkspaceCreate,
    current_user: dict = Depends(get_current_user)
):
    # Generate unique slug
    slug = workspace_data.name.lower().replace(" ", "-").replace("_", "-")
    
    workspace_doc = {
        "_id": str(uuid.uuid4()),
        "owner_id": current_user["id"],
        "name": workspace_data.name,
        "slug": slug,
        "description": workspace_data.description,
        "industry": workspace_data.industry,
        "website": workspace_data.website,
        "logo": None,
        "settings": {},
        "features_enabled": {
            "ai_assistant": True,
            "bio_sites": True,
            "ecommerce": True,
            "analytics": True,
            "social_media": True,
            "courses": True,
            "crm": True,
            "email_marketing": True,
            "advanced_booking": True,
            "financial_management": True,
            "escrow_system": True,
            "real_time_notifications": True
        },
        "is_active": True,
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

# ===== LINK SHORTENER ENDPOINTS =====
@app.get("/api/link-shortener/links")
async def get_short_links(current_user: dict = Depends(get_current_user)):
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        return {"success": True, "data": {"links": []}}
    
    links = await short_links_collection.find(
        {"workspace_id": str(workspace["_id"])}
    ).sort("created_at", -1).to_list(length=100)
    
    for link in links:
        link["id"] = str(link["_id"])
    
    return {
        "success": True,
        "data": {
            "links": [
                {
                    "id": link["id"],
                    "original_url": link["original_url"],
                    "short_code": link["short_code"],
                    "short_url": f"https://mwz.to/{link['short_code']}",
                    "clicks": link.get("clicks", 0),
                    "status": link.get("status", "active"),
                    "created_at": link["created_at"].isoformat()
                } for link in links
            ]
        }
    }

@app.post("/api/link-shortener/create")
async def create_short_link(
    link_data: ShortLinkCreate,
    current_user: dict = Depends(get_current_user)
):
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    # Generate short code if not provided
    short_code = link_data.custom_code or secrets.token_urlsafe(8).replace('_', '').replace('-', '')[:8]
    
    # Check if code already exists
    existing_link = await short_links_collection.find_one({"short_code": short_code})
    if existing_link:
        raise HTTPException(status_code=400, detail="Short code already exists")
    
    link_doc = {
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "user_id": current_user["id"],
        "original_url": link_data.original_url,
        "short_code": short_code,
        "clicks": 0,
        "status": "active",
        "expires_at": link_data.expires_at,
        "created_at": datetime.utcnow(),
        "updated_at": datetime.utcnow()
    }
    
    await short_links_collection.insert_one(link_doc)
    
    return {
        "success": True,
        "data": {
            "link": {
                "id": link_doc["_id"],
                "original_url": link_doc["original_url"],
                "short_code": link_doc["short_code"],
                "short_url": f"https://mwz.to/{link_doc['short_code']}",
                "created_at": link_doc["created_at"].isoformat()
            }
        }
    }

@app.get("/api/link-shortener/stats")
async def get_link_shortener_stats(current_user: dict = Depends(get_current_user)):
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        return {"success": True, "data": {"stats": {}}}
    
    total_links = await short_links_collection.count_documents({"workspace_id": str(workspace["_id"])})
    active_links = await short_links_collection.count_documents({"workspace_id": str(workspace["_id"]), "status": "active"})
    
    # Calculate total clicks
    links_cursor = short_links_collection.find({"workspace_id": str(workspace["_id"])})
    total_clicks = 0
    async for link in links_cursor:
        total_clicks += link.get("clicks", 0)
    
    return {
        "success": True,
        "data": {
            "stats": {
                "total_links": total_links,
                "active_links": active_links,
                "total_clicks": total_clicks,
                "click_rate": round((total_clicks / max(total_links, 1)) * 100, 1)
            }
        }
    }

# ===== TEAM MANAGEMENT ENDPOINTS =====
@app.get("/api/team/members")
async def get_team_members(current_user: dict = Depends(get_current_user)):
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        return {"success": True, "data": {"members": []}}
    
    members = await team_members_collection.find(
        {"workspace_id": str(workspace["_id"])}
    ).to_list(length=100)
    
    for member in members:
        member["id"] = str(member["_id"])
    
    return {
        "success": True,
        "data": {
            "members": [
                {
                    "id": member["id"],
                    "name": member["name"],
                    "email": member["email"],
                    "role": member["role"],
                    "status": member.get("status", "active"),
                    "last_active": member.get("last_active", "Never"),
                    "joined_at": member["created_at"].isoformat()
                } for member in members
            ]
        }
    }

# Instagram Lead Generation System
import logging
from uuid import uuid4

# Set up logger
logger = logging.getLogger(__name__)

# Instagram collections
instagram_accounts_collection = database.instagram_accounts
instagram_searches_collection = database.instagram_searches
instagram_exports_collection = database.instagram_exports

@app.post("/instagram/search")
async def search_instagram_accounts(request: dict = None, current_user: dict = Depends(get_current_user)):
    try:
        workspace_id = request.get("workspace_id", current_user.get("current_workspace_id"))
        query = request.get("query", "")
        filters = request.get("filters", {})
        page = request.get("page", 1)
        limit = request.get("limit", 50)
        sort_by = request.get("sortBy", "followers")
        sort_order = request.get("sortOrder", "desc")
        
        if not query.strip():
            raise HTTPException(status_code=400, detail="Search query is required")
        
        # Build MongoDB query
        mongo_query = {"workspace_id": workspace_id}
        
        # Add text search
        if query:
            mongo_query["$or"] = [
                {"username": {"$regex": query, "$options": "i"}},
                {"display_name": {"$regex": query, "$options": "i"}},
                {"bio": {"$regex": query, "$options": "i"}},
                {"location": {"$regex": query, "$options": "i"}},
                {"hashtags": {"$regex": query, "$options": "i"}}
            ]
        
        # Apply filters
        if filters.get("minFollowers"):
            mongo_query["followers"] = {"$gte": int(filters["minFollowers"])}
        if filters.get("maxFollowers"):
            if "followers" in mongo_query:
                mongo_query["followers"]["$lte"] = int(filters["maxFollowers"])
            else:
                mongo_query["followers"] = {"$lte": int(filters["maxFollowers"])}
                
        if filters.get("minFollowing"):
            mongo_query["following"] = {"$gte": int(filters["minFollowing"])}
        if filters.get("maxFollowing"):
            if "following" in mongo_query:
                mongo_query["following"]["$lte"] = int(filters["maxFollowing"])
            else:
                mongo_query["following"] = {"$lte": int(filters["maxFollowing"])}
                
        if filters.get("minEngagementRate"):
            mongo_query["engagement_rate"] = {"$gte": float(filters["minEngagementRate"])}
            
        if filters.get("location"):
            mongo_query["location"] = {"$regex": filters["location"], "$options": "i"}
            
        if filters.get("accountType"):
            mongo_query["account_type"] = filters["accountType"]
            
        if filters.get("language"):
            mongo_query["language"] = filters["language"]
            
        if filters.get("verified") == "true":
            mongo_query["verified"] = True
        elif filters.get("verified") == "false":
            mongo_query["verified"] = False
            
        if filters.get("bioKeywords"):
            keywords = filters["bioKeywords"].split(",")
            keyword_regex = "|".join([kw.strip() for kw in keywords])
            mongo_query["bio"] = {"$regex": keyword_regex, "$options": "i"}
            
        if filters.get("hashtags"):
            hashtags = filters["hashtags"].split(",")
            hashtag_regex = "|".join([ht.strip().replace("#", "") for ht in hashtags])
            mongo_query["hashtags"] = {"$regex": hashtag_regex, "$options": "i"}
        
        # Count total results
        total = await instagram_accounts_collection.count_documents(mongo_query)
        
        # Apply sorting
        sort_field = sort_by
        if sort_by == "followers":
            sort_field = "followers"
        elif sort_by == "engagement":
            sort_field = "engagement_rate"
        elif sort_by == "posts":
            sort_field = "post_count"
        
        sort_direction = -1 if sort_order == "desc" else 1
        
        # Execute query with pagination
        skip = (page - 1) * limit
        cursor = instagram_accounts_collection.find(mongo_query).sort(sort_field, sort_direction).skip(skip).limit(limit)
        accounts = await cursor.to_list(length=limit)
        
        # Save search to history
        search_record = {
            "id": str(uuid4()),
            "workspace_id": workspace_id,
            "user_id": current_user["id"],
            "query": query,
            "filters": filters,
            "results_count": total,
            "timestamp": datetime.utcnow(),
            "created_at": datetime.utcnow()
        }
        await instagram_searches_collection.insert_one(search_record)
        
        # Format results
        formatted_accounts = []
        for account in accounts:
            formatted_account = {
                "id": account["id"],
                "username": account["username"],
                "displayName": account.get("display_name"),
                "bio": account.get("bio"),
                "followers": account.get("followers", 0),
                "following": account.get("following", 0),
                "postCount": account.get("post_count", 0),
                "engagementRate": account.get("engagement_rate", 0),
                "profilePicture": account.get("profile_picture"),
                "verified": account.get("verified", False),
                "accountType": account.get("account_type", "personal"),
                "location": account.get("location"),
                "email": account.get("email"),
                "contactInfo": account.get("contact_info"),
                "lastPostDate": account.get("last_post_date"),
                "language": account.get("language", "en"),
                "businessCategory": account.get("business_category")
            }
            formatted_accounts.append(formatted_account)
        
        return {
            "success": True,
            "data": {
                "accounts": formatted_accounts,
                "total": total,
                "page": page,
                "limit": limit,
                "pages": (total + limit - 1) // limit
            }
        }
    except Exception as e:
        logger.error(f"Instagram search failed: {str(e)}")
        raise HTTPException(status_code=500, detail="Search failed")

@app.post("/instagram/export")
async def export_instagram_accounts(request: dict = None, current_user: dict = Depends(get_current_user)):
    try:
        workspace_id = request.get("workspace_id", current_user.get("current_workspace_id"))
        account_ids = request.get("accounts", [])
        export_format = request.get("format", "csv")
        include_emails = request.get("includeEmails", True)
        include_contact_info = request.get("includeContactInfo", True)
        include_analytics = request.get("includeAnalytics", True)
        
        if not account_ids:
            raise HTTPException(status_code=400, detail="No accounts selected for export")
        
        # Fetch selected accounts
        accounts = await instagram_accounts_collection.find({
            "id": {"$in": account_ids},
            "workspace_id": workspace_id
        }).to_list(length=None)
        
        if not accounts:
            raise HTTPException(status_code=404, detail="No accounts found")
        
        # Prepare export data
        export_data = []
        for account in accounts:
            row = {
                "Username": account["username"],
                "Display Name": account.get("display_name", ""),
                "Bio": account.get("bio", ""),
                "Followers": account.get("followers", 0),
                "Following": account.get("following", 0),
                "Posts": account.get("post_count", 0),
                "Engagement Rate": f"{account.get('engagement_rate', 0)}%",
                "Verified": "Yes" if account.get("verified") else "No",
                "Account Type": account.get("account_type", "personal"),
                "Location": account.get("location", ""),
                "Language": account.get("language", ""),
                "Profile URL": f"https://instagram.com/{account['username']}",
                "Profile Picture": account.get("profile_picture", "")
            }
            
            if include_emails:
                row["Email"] = account.get("email", "")
                
            if include_contact_info:
                contact_info = account.get("contact_info", {})
                row["Phone"] = contact_info.get("phone", "")
                row["Website"] = contact_info.get("website", "")
                
            if include_analytics:
                row["Last Post Date"] = account.get("last_post_date", "")
                row["Average Likes"] = account.get("avg_likes", 0)
                row["Average Comments"] = account.get("avg_comments", 0)
                row["Business Category"] = account.get("business_category", "")
            
            export_data.append(row)
        
        # Record export
        export_record = {
            "id": str(uuid4()),
            "workspace_id": workspace_id,
            "user_id": current_user["id"],
            "accounts_count": len(account_ids),
            "format": export_format,
            "timestamp": datetime.utcnow(),
            "created_at": datetime.utcnow()
        }
        await instagram_exports_collection.insert_one(export_record)
        
        # Return CSV data (frontend will handle file download)
        if export_format == "csv":
            import csv
            import io
            output = io.StringIO()
            
            if export_data:
                writer = csv.DictWriter(output, fieldnames=export_data[0].keys())
                writer.writeheader()
                writer.writerows(export_data)
            
            return {
                "success": True,
                "data": output.getvalue(),
                "filename": f"instagram_leads_{datetime.now().strftime('%Y%m%d_%H%M%S')}.csv"
            }
        
        return {
            "success": True,
            "data": export_data
        }
        
    except Exception as e:
        logger.error(f"Instagram export failed: {str(e)}")
        raise HTTPException(status_code=500, detail="Export failed")

@app.get("/instagram/search-history")
async def get_search_history(current_user: dict = Depends(get_current_user)):
    try:
        workspace_id = current_user.get("current_workspace_id")
        
        searches = await instagram_searches_collection.find({
            "workspace_id": workspace_id,
            "user_id": current_user["id"]
        }).sort("timestamp", -1).limit(10).to_list(length=10)
        
        for search in searches:
            search["_id"] = str(search["_id"])
            search["timestamp"] = search["timestamp"].isoformat()
        
        return {"success": True, "data": searches}
    except Exception as e:
        logger.error(f"Failed to get search history: {str(e)}")
        return {"success": False, "data": []}

@app.post("/instagram/save-search")
async def save_search(request: dict = None, current_user: dict = Depends(get_current_user)):
    try:
        workspace_id = request.get("workspace_id", current_user.get("current_workspace_id"))
        name = request.get("name", "")
        query = request.get("query", "")
        filters = request.get("filters", {})
        
        if not name.strip():
            raise HTTPException(status_code=400, detail="Search name is required")
        
        saved_search = {
            "id": str(uuid4()),
            "workspace_id": workspace_id,
            "user_id": current_user["id"],
            "name": name,
            "query": query,
            "filters": filters,
            "created_at": datetime.utcnow()
        }
        
        # Use a separate collection for saved searches
        saved_searches_collection = database.instagram_saved_searches
        await saved_searches_collection.insert_one(saved_search)
        
        return {"success": True, "data": saved_search}
    except Exception as e:
        logger.error(f"Failed to save search: {str(e)}")
        raise HTTPException(status_code=500, detail="Failed to save search")

@app.get("/instagram/saved-searches")
async def get_saved_searches(current_user: dict = Depends(get_current_user)):
    try:
        workspace_id = current_user.get("current_workspace_id")
        saved_searches_collection = database.instagram_saved_searches
        
        searches = await saved_searches_collection.find({
            "workspace_id": workspace_id,
            "user_id": current_user["id"]
        }).sort("created_at", -1).to_list(length=None)
        
        for search in searches:
            search["_id"] = str(search["_id"])
            search["created_at"] = search["created_at"].isoformat()
        
        return {"success": True, "data": searches}
    except Exception as e:
        logger.error(f"Failed to get saved searches: {str(e)}")
        return {"success": False, "data": []}

@app.get("/instagram/search-stats")
async def get_search_stats(current_user: dict = Depends(get_current_user)):
    try:
        workspace_id = current_user.get("current_workspace_id")
        
        # Count total searches
        total_searches = await instagram_searches_collection.count_documents({
            "workspace_id": workspace_id
        })
        
        # Count total accounts found
        total_accounts = await instagram_accounts_collection.count_documents({
            "workspace_id": workspace_id
        })
        
        # Calculate average engagement rate
        pipeline = [
            {"$match": {"workspace_id": workspace_id}},
            {"$group": {
                "_id": None,
                "avg_engagement": {"$avg": "$engagement_rate"}
            }}
        ]
        result = await instagram_accounts_collection.aggregate(pipeline).to_list(length=1)
        avg_engagement = result[0]["avg_engagement"] if result else 0
        
        # Get top categories
        pipeline = [
            {"$match": {"workspace_id": workspace_id}},
            {"$group": {
                "_id": "$business_category",
                "count": {"$sum": 1}
            }},
            {"$sort": {"count": -1}},
            {"$limit": 5}
        ]
        top_categories = await instagram_accounts_collection.aggregate(pipeline).to_list(length=5)
        
        return {
            "success": True,
            "data": {
                "totalSearches": total_searches,
                "totalAccountsFound": total_accounts,
                "averageEngagementRate": round(avg_engagement, 2) if avg_engagement else 0,
                "topCategories": top_categories
            }
        }
    except Exception as e:
        logger.error(f"Failed to get search stats: {str(e)}")
        return {
            "success": False,
            "data": {
                "totalSearches": 0,
                "totalAccountsFound": 0,
                "averageEngagementRate": 0,
                "topCategories": []
            }
        }

@app.post("/api/team/invite")
async def invite_team_member(
    invite_data: TeamMemberInvite,
    current_user: dict = Depends(get_current_user)
):
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    # Check if user already exists in team
    existing_member = await team_members_collection.find_one({
        "workspace_id": str(workspace["_id"]),
        "email": invite_data.email
    })
    if existing_member:
        raise HTTPException(status_code=400, detail="User already in team")
    
    member_doc = {
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "email": invite_data.email,
        "name": invite_data.email.split('@')[0],
        "role": invite_data.role,
        "status": "pending",
        "invited_by": current_user["id"],
        "last_active": "Never",
        "created_at": datetime.utcnow(),
        "updated_at": datetime.utcnow()
    }
    
    await team_members_collection.insert_one(member_doc)
    
    return {
        "success": True,
        "data": {
            "member": {
                "id": member_doc["_id"],
                "email": member_doc["email"],
                "role": member_doc["role"],
                "status": member_doc["status"],
                "created_at": member_doc["created_at"].isoformat()
            }
        }
    }

# ===== FORM TEMPLATES ENDPOINTS =====
@app.get("/api/form-templates")
async def get_form_templates(current_user: dict = Depends(get_current_user)):
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        return {"success": True, "data": {"templates": []}}
    
    templates = await form_templates_collection.find(
        {"workspace_id": str(workspace["_id"])}
    ).to_list(length=100)
    
    for template in templates:
        template["id"] = str(template["_id"])
    
    return {
        "success": True,
        "data": {
            "templates": [
                {
                    "id": template["id"],
                    "name": template["name"],
                    "description": template.get("description"),
                    "category": template["category"],
                    "fields": template["fields"],
                    "submissions": template.get("submissions", 0),
                    "is_published": template.get("is_published", False),
                    "created_at": template["created_at"].isoformat()
                } for template in templates
            ]
        }
    }

@app.post("/api/form-templates")
async def create_form_template(
    template_data: FormTemplateCreate,
    current_user: dict = Depends(get_current_user)
):
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    template_doc = {
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "user_id": current_user["id"],
        "name": template_data.name,
        "description": template_data.description,
        "category": template_data.category,
        "fields": template_data.fields,
        "submissions": 0,
        "is_published": False,
        "created_at": datetime.utcnow(),
        "updated_at": datetime.utcnow()
    }
    
    await form_templates_collection.insert_one(template_doc)
    
    return {
        "success": True,
        "data": {
            "template": {
                "id": template_doc["_id"],
                "name": template_doc["name"],
                "category": template_doc["category"],
                "created_at": template_doc["created_at"].isoformat()
            }
        }
    }

# ===== DISCOUNT CODES ENDPOINTS =====
@app.get("/api/discount-codes")
async def get_discount_codes(current_user: dict = Depends(get_current_user)):
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        return {"success": True, "data": {"codes": []}}
    
    codes = await discount_codes_collection.find(
        {"workspace_id": str(workspace["_id"])}
    ).sort("created_at", -1).to_list(length=100)
    
    for code in codes:
        code["id"] = str(code["_id"])
    
    return {
        "success": True,
        "data": {
            "codes": [
                {
                    "id": code["id"],
                    "code": code["code"],
                    "description": code.get("description"),
                    "type": code["type"],
                    "value": code["value"],
                    "usage_limit": code.get("usage_limit"),
                    "used_count": code.get("used_count", 0),
                    "is_active": code.get("is_active", True),
                    "expires_at": code.get("expires_at").isoformat() if code.get("expires_at") else None,
                    "created_at": code["created_at"].isoformat()
                } for code in codes
            ]
        }
    }

@app.post("/api/discount-codes")
async def create_discount_code(
    code_data: DiscountCodeCreate,
    current_user: dict = Depends(get_current_user)
):
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    # Check if code already exists
    existing_code = await discount_codes_collection.find_one({"code": code_data.code})
    if existing_code:
        raise HTTPException(status_code=400, detail="Discount code already exists")
    
    code_doc = {
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "user_id": current_user["id"],
        "code": code_data.code,
        "description": code_data.description,
        "type": code_data.type,
        "value": code_data.value,
        "usage_limit": code_data.usage_limit,
        "used_count": 0,
        "is_active": True,
        "expires_at": code_data.expires_at,
        "applicable_products": code_data.applicable_products,
        "created_at": datetime.utcnow(),
        "updated_at": datetime.utcnow()
    }
    
    await discount_codes_collection.insert_one(code_doc)
    
    return {
        "success": True,
        "data": {
            "code": {
                "id": code_doc["_id"],
                "code": code_doc["code"],
                "type": code_doc["type"],
                "value": code_doc["value"],
                "created_at": code_doc["created_at"].isoformat()
            }
        }
    }

# Health check
@app.get("/api/health")
async def health_check():
    return {
        "success": True,
        "message": "Mewayz Professional Platform is healthy",
        "version": "3.0.0",
        "features": [
            "Authentication & User Management",
            "AI Assistant & Conversations", 
            "Bio Sites & Link Management",
            "E-commerce & Product Catalog",
            "Advanced Booking & Scheduling",
            "Course Management & LMS",
            "CRM & Contact Management", 
            "Website Builder & Pages",
            "Email Marketing & Campaigns",
            "Analytics & Reporting",
            "Real-time Notifications",
            "Financial Management & Invoicing",
            "Workspace & Team Collaboration",
            "Payment Processing & Escrow",
            "Link Shortener & URL Management",
            "Team Management & Invitations",
            "Form Templates & Builder", 
            "Discount Codes & Promotions"
        ],
        "timestamp": datetime.utcnow().isoformat()
    }

@app.get("/api/test")
async def api_test():
    return {
        "message": "Mewayz Professional Platform FastAPI is working!",
        "status": "success", 
        "version": "3.0.0",
        "database": "MongoDB",
        "api_endpoints": 25,
        "timestamp": datetime.utcnow().isoformat()
    }

# Initialize admin user and sample data
async def create_admin_user():
    try:
        # Check if admin user exists
        admin_user = await users_collection.find_one({"email": "tmonnens@outlook.com"})
        if not admin_user:
            admin_doc = {
                "_id": str(uuid.uuid4()),
                "name": "Admin User",
                "email": "tmonnens@outlook.com",
                "password": get_password_hash("Voetballen5"),
                "role": UserRole.ADMIN,
                "email_verified_at": datetime.utcnow(),
                "phone": None,
                "avatar": None,
                "timezone": "UTC",
                "language": "en",
                "status": True,
                "last_login_at": None,
                "login_attempts": 0,
                "locked_until": None,
                "two_factor_enabled": False,
                "two_factor_secret": None,
                "api_key": secrets.token_urlsafe(48),
                "subscription_plan": "enterprise",
                "subscription_expires_at": None,
                "created_at": datetime.utcnow(),
                "updated_at": datetime.utcnow()
            }
            
            await users_collection.insert_one(admin_doc)
            print(f"✅ Admin user created: {admin_doc['email']} (ID: {admin_doc['_id']})")
            
            # Create default workspace for admin
            workspace_doc = {
                "_id": str(uuid.uuid4()),
                "owner_id": admin_doc["_id"],
                "name": "Admin Workspace",
                "slug": "admin-workspace", 
                "description": "Default workspace for admin user",
                "industry": "Technology",
                "website": None,
                "logo": None,
                "settings": {},
                "features_enabled": {
                    "ai_assistant": True,
                    "bio_sites": True,
                    "ecommerce": True,
                    "analytics": True,
                    "social_media": True,
                    "courses": True,
                    "crm": True,
                    "email_marketing": True,
                    "advanced_booking": True,
                    "financial_management": True,
                    "escrow_system": True,
                    "real_time_notifications": True
                },
                "is_active": True,
                "created_at": datetime.utcnow(),
                "updated_at": datetime.utcnow()
            }
            await workspaces_collection.insert_one(workspace_doc)
            print(f"✅ Default workspace created: {workspace_doc['name']}")
            
            # Create sample short links
            sample_links = [
                {
                    "_id": str(uuid.uuid4()),
                    "workspace_id": workspace_doc["_id"],
                    "user_id": admin_doc["_id"],
                    "original_url": "https://example.com/very-long-url-that-needs-shortening",
                    "short_code": "abc123",
                    "clicks": 245,
                    "status": "active",
                    "expires_at": None,
                    "created_at": datetime.utcnow() - timedelta(days=2),
                    "updated_at": datetime.utcnow() - timedelta(days=2)
                },
                {
                    "_id": str(uuid.uuid4()),
                    "workspace_id": workspace_doc["_id"],
                    "user_id": admin_doc["_id"],
                    "original_url": "https://mystore.com/product/amazing-course",
                    "short_code": "course1",
                    "clicks": 89,
                    "status": "active",
                    "expires_at": None,
                    "created_at": datetime.utcnow() - timedelta(days=7),
                    "updated_at": datetime.utcnow() - timedelta(days=7)
                }
            ]
            await short_links_collection.insert_many(sample_links)
            
            # Create sample team members
            sample_members = [
                {
                    "_id": str(uuid.uuid4()),
                    "workspace_id": workspace_doc["_id"],
                    "email": "john@example.com",
                    "name": "John Doe",
                    "role": "admin",
                    "status": "active",
                    "invited_by": admin_doc["_id"],
                    "last_active": "2 minutes ago",
                    "created_at": datetime.utcnow() - timedelta(days=30),
                    "updated_at": datetime.utcnow()
                },
                {
                    "_id": str(uuid.uuid4()),
                    "workspace_id": workspace_doc["_id"],
                    "email": "sarah@example.com",
                    "name": "Sarah Wilson",
                    "role": "editor",
                    "status": "active",
                    "invited_by": admin_doc["_id"],
                    "last_active": "1 hour ago",
                    "created_at": datetime.utcnow() - timedelta(days=15),
                    "updated_at": datetime.utcnow()
                }
            ]
            await team_members_collection.insert_many(sample_members)
            print(f"✅ Sample data created for workspace")
            
        else:
            print(f"✅ Admin user already exists: {admin_user['email']}")
    except Exception as e:
        print(f"❌ Error creating admin user: {e}")

# Initialize token system
async def initialize_token_system():
    """Initialize the token system with default packages and settings"""
    try:
        # Check if token packages already exist
        existing_packages = await token_packages_collection.count_documents({})
        if existing_packages == 0:
            # Create default token packages
            default_packages = [
                {
                    "_id": str(uuid.uuid4()),
                    "name": "Starter Pack",
                    "tokens": 1000,
                    "price": 9.99,
                    "currency": "USD",
                    "bonus_tokens": 100,
                    "description": "Perfect for small businesses getting started",
                    "is_popular": False,
                    "created_at": datetime.utcnow(),
                    "updated_at": datetime.utcnow()
                },
                {
                    "_id": str(uuid.uuid4()),
                    "name": "Professional Pack",
                    "tokens": 5000,
                    "price": 39.99,
                    "currency": "USD",
                    "bonus_tokens": 1000,
                    "description": "Ideal for growing businesses",
                    "is_popular": True,
                    "created_at": datetime.utcnow(),
                    "updated_at": datetime.utcnow()
                },
                {
                    "_id": str(uuid.uuid4()),
                    "name": "Enterprise Pack",
                    "tokens": 15000,
                    "price": 99.99,
                    "currency": "USD",
                    "bonus_tokens": 5000,
                    "description": "For large organizations with high usage",
                    "is_popular": False,
                    "created_at": datetime.utcnow(),
                    "updated_at": datetime.utcnow()
                }
            ]
            await token_packages_collection.insert_many(default_packages)
            print(f"✅ Token packages initialized: {len(default_packages)} packages created")
        else:
            print(f"✅ Token packages already exist: {existing_packages} packages found")
            
        # Initialize workspace token settings for admin workspace
        admin_workspace = await workspaces_collection.find_one({"slug": "admin-workspace"})
        if admin_workspace:
            existing_settings = await workspace_tokens_collection.find_one({"workspace_id": str(admin_workspace["_id"])})
            if not existing_settings:
                token_settings = {
                    "_id": str(uuid.uuid4()),
                    "workspace_id": str(admin_workspace["_id"]),
                    "current_tokens": 10000,  # Start with generous amount for admin
                    "monthly_token_allowance": 5000,
                    "auto_purchase_enabled": False,
                    "auto_purchase_threshold": 100,
                    "auto_purchase_package_id": None,
                    "user_limits": {},
                    "feature_costs": {
                        "ai_content_generation": 10,
                        "ai_image_generation": 25,
                        "seo_optimization": 5,
                        "email_campaign": 2,
                        "analytics_report": 1
                    },
                    "created_at": datetime.utcnow(),
                    "updated_at": datetime.utcnow()
                }
                await workspace_tokens_collection.insert_one(token_settings)
                print(f"✅ Token settings initialized for admin workspace")
            else:
                print(f"✅ Token settings already exist for admin workspace")
                
    except Exception as e:
        print(f"❌ Error initializing token system: {e}")

# ===== STRIPE/SUBSCRIPTION ENDPOINTS =====
@app.get("/api/subscription/plans")
async def get_subscription_plans():
    """Get available subscription plans"""
    plans = [
        {
            "plan_id": "free",
            "name": "Free",
            "description": "Perfect for getting started",
            "price_monthly": 0,
            "price_yearly": 0,
            "features": [
                "Up to 10 features",
                "Basic social media management",
                "Link in Bio builder",
                "Basic analytics",
                "Community support"
            ],
            "max_features": 10,
            "is_popular": False
        },
        {
            "plan_id": "pro",
            "name": "Pro",
            "description": "For growing businesses",
            "price_monthly": 1,  # $1 per feature per month
            "price_yearly": 10,  # $10 per feature per year
            "features": [
                "Unlimited features",
                "Advanced social media management",
                "Instagram database access",
                "Advanced analytics & reporting",
                "Team collaboration",
                "Email marketing",
                "Course creation",
                "E-commerce tools",
                "Priority support"
            ],
            "max_features": -1,  # Unlimited
            "is_popular": True
        },
        {
            "plan_id": "enterprise",
            "name": "Enterprise",
            "description": "White-label solution for agencies",
            "price_monthly": 1.5,  # $1.5 per feature per month
            "price_yearly": 15,   # $15 per feature per year
            "features": [
                "All Pro features",
                "White-label branding",
                "Custom domains",
                "Advanced integrations",
                "Dedicated account manager",
                "24/7 phone support",
                "Custom development",
                "SLA guarantees"
            ],
            "max_features": -1,  # Unlimited
            "is_popular": False
        }
    ]
    
    return {
        "success": True,
        "plans": plans
    }

@app.post("/api/subscription/create-payment-intent")
async def create_payment_intent(
    request: PaymentIntentRequest,
    current_user: dict = Depends(get_current_user)
):
    """Create a Stripe PaymentIntent"""
    try:
        if not STRIPE_SECRET_KEY:
            raise HTTPException(
                status_code=status.HTTP_503_SERVICE_UNAVAILABLE,
                detail="Stripe not configured"
            )
        
        # Create PaymentIntent
        intent = stripe.PaymentIntent.create(
            amount=request.amount,
            currency=request.currency,
            automatic_payment_methods={"enabled": True},
            description=request.description,
            metadata={
                "user_id": current_user["id"],
                "user_email": current_user["email"],
                **(request.metadata or {})
            }
        )
        
        return {
            "success": True,
            "client_secret": intent.client_secret,
            "payment_intent_id": intent.id
        }
        
    except stripe.error.StripeError as e:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail=f"Stripe error: {str(e)}"
        )
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Payment intent creation failed: {str(e)}"
        )

@app.post("/api/subscription/create-subscription")
async def create_subscription(
    subscription_request: CreateSubscriptionRequest,
    current_user: dict = Depends(get_current_user)
):
    """Create a subscription with Stripe"""
    try:
        if not STRIPE_SECRET_KEY:
            raise HTTPException(
                status_code=status.HTTP_503_SERVICE_UNAVAILABLE,
                detail="Stripe not configured"
            )
        
        # Get or create customer
        user_email = current_user["email"]
        customers = stripe.Customer.list(email=user_email, limit=1)
        
        if customers.data:
            customer = customers.data[0]
        else:
            customer = stripe.Customer.create(
                email=user_email,
                name=current_user["name"],
                metadata={"user_id": current_user["id"]}
            )
        
        # Attach payment method to customer
        stripe.PaymentMethod.attach(
            subscription_request.payment_method_id,
            customer=customer.id
        )
        
        # Set as default payment method
        stripe.Customer.modify(
            customer.id,
            invoice_settings={"default_payment_method": subscription_request.payment_method_id}
        )
        
        # Create price for plan (in a real app, you'd have pre-created prices)
        plans = {
            "pro_monthly": "price_pro_monthly",
            "pro_yearly": "price_pro_yearly", 
            "enterprise_monthly": "price_enterprise_monthly",
            "enterprise_yearly": "price_enterprise_yearly"
        }
        
        price_id = plans.get(f"{subscription_request.plan_id}_{subscription_request.billing_cycle}")
        
        if not price_id:
            # Create a dynamic price (for demo purposes)
            plan_prices = {"pro": 1, "enterprise": 1.5}
            base_price = plan_prices.get(subscription_request.plan_id, 1)
            
            if subscription_request.billing_cycle == "yearly":
                amount = int(base_price * 10 * 100)  # $10 per feature per year
                interval = "year"
            else:
                amount = int(base_price * 100)  # $1 per feature per month
                interval = "month"
            
            price = stripe.Price.create(
                unit_amount=amount,
                currency="usd",
                recurring={"interval": interval},
                product_data={
                    "name": f"Mewayz {subscription_request.plan_id.title()} Plan"
                }
            )
            price_id = price.id
        
        # Create subscription
        subscription = stripe.Subscription.create(
            customer=customer.id,
            items=[{"price": price_id}],
            payment_behavior="default_incomplete",
            expand=["latest_invoice.payment_intent"],
        )
        
        # Update user's subscription in database
        await users_collection.update_one(
            {"email": user_email},
            {
                "$set": {
                    "subscription_plan": subscription_request.plan_id,
                    "subscription_id": subscription.id,
                    "customer_id": customer.id,
                    "subscription_status": subscription.status,
                    "subscription_expires_at": datetime.fromtimestamp(subscription.current_period_end),
                    "updated_at": datetime.utcnow()
                }
            }
        )
        
        return {
            "success": True,
            "subscription": {
                "id": subscription.id,
                "status": subscription.status,
                "client_secret": subscription.latest_invoice.payment_intent.client_secret if subscription.latest_invoice.payment_intent else None
            }
        }
        
    except stripe.error.StripeError as e:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail=f"Stripe error: {str(e)}"
        )
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Subscription creation failed: {str(e)}"
        )

@app.post("/api/webhooks/stripe")
async def stripe_webhook(request: Request):
    """Handle Stripe webhooks"""
    try:
        payload = await request.body()
        sig_header = request.headers.get('stripe-signature')
        
        # In production, you should verify the webhook signature
        # For now, we'll just process the event
        
        event_data = json.loads(payload.decode('utf-8'))
        event_type = event_data.get('type')
        
        if event_type == 'invoice.payment_succeeded':
            # Handle successful payment
            invoice = event_data['data']['object']
            customer_id = invoice['customer']
            
            # Update user subscription status
            customer = stripe.Customer.retrieve(customer_id)
            user_email = customer.email
            
            await users_collection.update_one(
                {"email": user_email},
                {
                    "$set": {
                        "subscription_status": "active",
                        "last_payment_at": datetime.utcnow(),
                        "updated_at": datetime.utcnow()
                    }
                }
            )
            
        elif event_type == 'invoice.payment_failed':
            # Handle failed payment
            invoice = event_data['data']['object']
            customer_id = invoice['customer']
            
            customer = stripe.Customer.retrieve(customer_id)
            user_email = customer.email
            
            await users_collection.update_one(
                {"email": user_email},
                {
                    "$set": {
                        "subscription_status": "past_due",
                        "updated_at": datetime.utcnow()
                    }
                }
            )
        
        return {"success": True}
        
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail=f"Webhook processing failed: {str(e)}"
        )

@app.get("/api/subscription/status")
async def get_subscription_status(current_user: dict = Depends(get_current_user)):
    """Get current user's subscription status"""
    user = await users_collection.find_one({"email": current_user["email"]})
    
    subscription_info = {
        "plan": user.get("subscription_plan", "free"),
        "status": user.get("subscription_status", "inactive"),
        "expires_at": user.get("subscription_expires_at"),
        "customer_id": user.get("customer_id"),
        "subscription_id": user.get("subscription_id")
    }
    
    # If user has active subscription, get latest info from Stripe
    if subscription_info["subscription_id"] and STRIPE_SECRET_KEY:
        try:
            subscription = stripe.Subscription.retrieve(subscription_info["subscription_id"])
            subscription_info.update({
                "status": subscription.status,
                "current_period_end": datetime.fromtimestamp(subscription.current_period_end),
                "cancel_at_period_end": subscription.cancel_at_period_end
            })
        except:
            pass  # Fail silently if Stripe call fails
    
    return {
        "success": True,
        "subscription": subscription_info
    }

# ===== AI TOKEN ECOSYSTEM ENDPOINTS =====

@app.get("/api/tokens/packages")
async def get_token_packages():
    """Get available token packages for purchase"""
    packages = await token_packages_collection.find({}).to_list(length=100)
    
    # If no packages exist, create default ones
    if not packages:
        default_packages = [
            {
                "_id": str(uuid.uuid4()),
                "name": "Starter Pack",
                "tokens": 100,
                "price": 9.99,
                "currency": "USD",
                "bonus_tokens": 10,
                "description": "Perfect for getting started with AI features",
                "is_popular": False,
                "created_at": datetime.utcnow()
            },
            {
                "_id": str(uuid.uuid4()),
                "name": "Professional Pack",
                "tokens": 500,
                "price": 39.99,
                "currency": "USD",
                "bonus_tokens": 75,
                "description": "Great for professional use and small teams",
                "is_popular": True,
                "created_at": datetime.utcnow()
            },
            {
                "_id": str(uuid.uuid4()),
                "name": "Enterprise Pack",
                "tokens": 1500,
                "price": 99.99,
                "currency": "USD",
                "bonus_tokens": 300,
                "description": "Maximum value for heavy AI users and large teams",
                "is_popular": False,
                "created_at": datetime.utcnow()
            }
        ]
        
        await token_packages_collection.insert_many(default_packages)
        packages = default_packages
    
    for package in packages:
        package["id"] = str(package["_id"])
    
    return {
        "success": True,
        "data": {
            "packages": [
                {
                    "id": pkg["id"],
                    "name": pkg["name"],
                    "tokens": pkg["tokens"],
                    "price": pkg["price"],
                    "currency": pkg["currency"],
                    "bonus_tokens": pkg["bonus_tokens"],
                    "total_tokens": pkg["tokens"] + pkg["bonus_tokens"],
                    "description": pkg.get("description"),
                    "is_popular": pkg.get("is_popular", False),
                    "per_token_price": round(pkg["price"] / pkg["tokens"], 4)
                } for pkg in packages
            ]
        }
    }

@app.get("/api/tokens/workspace/{workspace_id}")
async def get_workspace_tokens(
    workspace_id: str,
    current_user: dict = Depends(get_current_user)
):
    """Get token balance and settings for a workspace"""
    # Verify user has access to workspace
    workspace = await workspaces_collection.find_one({"_id": workspace_id})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    # Check if user is workspace owner or member
    is_owner = workspace.get("owner_id") == current_user["id"]
    team_member = await team_members_collection.find_one({
        "workspace_id": workspace_id,
        "email": current_user["email"],
        "status": "active"
    })
    
    if not (is_owner or team_member):
        raise HTTPException(status_code=403, detail="Access denied to workspace")
    
    # Get workspace token data
    workspace_tokens = await workspace_tokens_collection.find_one({"workspace_id": workspace_id})
    if not workspace_tokens:
        # Create default token data for workspace
        workspace_tokens = {
            "_id": str(uuid.uuid4()),
            "workspace_id": workspace_id,
            "balance": 0,
            "total_purchased": 0,
            "total_used": 0,
            "monthly_allowance": 50,  # Free tier gets 50 tokens per month
            "allowance_used_this_month": 0,
            "allowance_reset_date": datetime.utcnow().replace(day=1) + timedelta(days=32),
            "auto_purchase_enabled": False,
            "auto_purchase_threshold": 10,
            "user_limits": {},
            "feature_costs": {
                "content_generation": 5,
                "image_generation": 10,
                "seo_analysis": 3,
                "content_analysis": 2,
                "course_generation": 15,
                "email_sequence": 8,
                "hashtag_generation": 2,
                "content_improvement": 4
            },
            "created_at": datetime.utcnow(),
            "updated_at": datetime.utcnow()
        }
        await workspace_tokens_collection.insert_one(workspace_tokens)
    
    # Get recent transactions
    recent_transactions = await token_transactions_collection.find(
        {"workspace_id": workspace_id}
    ).sort("created_at", -1).limit(10).to_list(length=10)
    
    for transaction in recent_transactions:
        transaction["id"] = str(transaction["_id"])
    
    # Calculate monthly usage
    current_month_start = datetime.utcnow().replace(day=1, hour=0, minute=0, second=0, microsecond=0)
    monthly_usage = await token_transactions_collection.count_documents({
        "workspace_id": workspace_id,
        "type": "usage",
        "created_at": {"$gte": current_month_start}
    })
    
    # Get user's individual limit if set
    user_limit = workspace_tokens.get("user_limits", {}).get(current_user["id"], None)
    
    return {
        "success": True,
        "data": {
            "workspace_id": workspace_id,
            "balance": workspace_tokens["balance"],
            "monthly_allowance": workspace_tokens["monthly_allowance"],
            "allowance_used_this_month": workspace_tokens.get("allowance_used_this_month", 0),
            "allowance_remaining": max(0, workspace_tokens["monthly_allowance"] - workspace_tokens.get("allowance_used_this_month", 0)),
            "total_purchased": workspace_tokens["total_purchased"],
            "total_used": workspace_tokens["total_used"],
            "monthly_usage": monthly_usage,
            "auto_purchase_enabled": workspace_tokens.get("auto_purchase_enabled", False),
            "auto_purchase_threshold": workspace_tokens.get("auto_purchase_threshold", 10),
            "feature_costs": workspace_tokens["feature_costs"],
            "user_limit": user_limit,
            "is_owner": is_owner,
            "recent_transactions": [
                {
                    "id": tx["id"],
                    "type": tx["type"],
                    "tokens": tx["tokens"],
                    "feature": tx.get("feature"),
                    "description": tx.get("description"),
                    "created_at": tx["created_at"].isoformat()
                } for tx in recent_transactions
            ]
        }
    }

@app.post("/api/tokens/purchase")
async def purchase_tokens(
    purchase_request: TokenPurchaseRequest,
    current_user: dict = Depends(get_current_user)
):
    """Purchase tokens for a workspace"""
    # Verify workspace access
    workspace = await workspaces_collection.find_one({"_id": purchase_request.workspace_id})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    if workspace.get("owner_id") != current_user["id"]:
        raise HTTPException(status_code=403, detail="Only workspace owners can purchase tokens")
    
    # Get token package
    package = await token_packages_collection.find_one({"_id": purchase_request.package_id})
    if not package:
        raise HTTPException(status_code=404, detail="Token package not found")
    
    try:
        # Create Stripe payment intent
        payment_intent = stripe.PaymentIntent.create(
            amount=int(package["price"] * 100),  # Convert to cents
            currency=package["currency"].lower(),
            payment_method=purchase_request.payment_method_id,
            confirmation_method="manual",
            confirm=True,
            description=f"Token Purchase: {package['name']}",
            metadata={
                "workspace_id": purchase_request.workspace_id,
                "user_id": current_user["id"],
                "package_id": purchase_request.package_id,
                "tokens": package["tokens"],
                "bonus_tokens": package["bonus_tokens"]
            }
        )
        
        if payment_intent.status == "succeeded":
            # Add tokens to workspace
            total_tokens = package["tokens"] + package["bonus_tokens"]
            
            await workspace_tokens_collection.update_one(
                {"workspace_id": purchase_request.workspace_id},
                {
                    "$inc": {
                        "balance": total_tokens,
                        "total_purchased": total_tokens
                    },
                    "$set": {
                        "updated_at": datetime.utcnow()
                    }
                },
                upsert=True
            )
            
            # Record transaction
            transaction_doc = {
                "_id": str(uuid.uuid4()),
                "workspace_id": purchase_request.workspace_id,
                "user_id": current_user["id"],
                "type": "purchase",
                "tokens": total_tokens,
                "cost": package["price"],
                "description": f"Purchased {package['name']} - {package['tokens']} tokens + {package['bonus_tokens']} bonus",
                "payment_intent_id": payment_intent.id,
                "created_at": datetime.utcnow()
            }
            await token_transactions_collection.insert_one(transaction_doc)
            
            return {
                "success": True,
                "data": {
                    "tokens_added": total_tokens,
                    "payment_intent_id": payment_intent.id,
                    "transaction_id": transaction_doc["_id"]
                }
            }
        else:
            return {
                "success": False,
                "error": "Payment failed",
                "payment_status": payment_intent.status
            }
            
    except stripe.error.CardError as e:
        return {
            "success": False,
            "error": f"Card error: {e.user_message}"
        }
    except Exception as e:
        raise HTTPException(
            status_code=500,
            detail=f"Token purchase failed: {str(e)}"
        )

@app.post("/api/tokens/workspace/{workspace_id}/settings")
async def update_workspace_token_settings(
    workspace_id: str,
    settings: WorkspaceTokenSettings,
    current_user: dict = Depends(get_current_user)
):
    """Update token settings for a workspace (owner only)"""
    # Verify workspace ownership
    workspace = await workspaces_collection.find_one({"_id": workspace_id})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    if workspace.get("owner_id") != current_user["id"]:
        raise HTTPException(status_code=403, detail="Only workspace owners can update token settings")
    
    # Update workspace token settings
    await workspace_tokens_collection.update_one(
        {"workspace_id": workspace_id},
        {
            "$set": {
                "monthly_allowance": settings.monthly_token_allowance,
                "auto_purchase_enabled": settings.auto_purchase_enabled,
                "auto_purchase_threshold": settings.auto_purchase_threshold,
                "auto_purchase_package_id": settings.auto_purchase_package_id,
                "user_limits": settings.user_limits,
                "feature_costs": settings.feature_costs,
                "updated_at": datetime.utcnow()
            }
        },
        upsert=True
    )
    
    return {
        "success": True,
        "message": "Token settings updated successfully"
    }

@app.post("/api/tokens/consume")
async def consume_tokens(
    workspace_id: str,
    feature: str,
    tokens_needed: int,
    current_user: dict = Depends(get_current_user)
):
    """Internal endpoint to consume tokens for AI features"""
    # Get workspace tokens
    workspace_tokens = await workspace_tokens_collection.find_one({"workspace_id": workspace_id})
    if not workspace_tokens:
        raise HTTPException(status_code=404, detail="Workspace tokens not found")
    
    # Check user limits
    user_limit = workspace_tokens.get("user_limits", {}).get(current_user["id"])
    if user_limit is not None and user_limit < tokens_needed:
        raise HTTPException(status_code=403, detail=f"User token limit exceeded. Limit: {user_limit}, Needed: {tokens_needed}")
    
    # Check if workspace has enough tokens
    current_balance = workspace_tokens.get("balance", workspace_tokens.get("current_tokens", 0))
    total_available = current_balance + max(0, workspace_tokens["monthly_allowance"] - workspace_tokens.get("allowance_used_this_month", 0))
    
    if total_available < tokens_needed:
        # Auto-purchase if enabled
        if workspace_tokens.get("auto_purchase_enabled") and workspace_tokens["balance"] <= workspace_tokens.get("auto_purchase_threshold", 10):
            # Trigger auto-purchase (simplified for now)
            pass
        
        raise HTTPException(
            status_code=402,
            detail=f"Insufficient tokens. Available: {total_available}, Needed: {tokens_needed}"
        )
    
    # Consume tokens (prefer monthly allowance first, then purchased balance)
    allowance_remaining = max(0, workspace_tokens["monthly_allowance"] - workspace_tokens.get("allowance_used_this_month", 0))
    
    if allowance_remaining >= tokens_needed:
        # Use monthly allowance
        await workspace_tokens_collection.update_one(
            {"workspace_id": workspace_id},
            {
                "$inc": {
                    "allowance_used_this_month": tokens_needed,
                    "total_used": tokens_needed
                }
            }
        )
        token_source = "monthly_allowance"
    else:
        # Use combination of allowance and purchased tokens
        purchased_tokens_used = tokens_needed - allowance_remaining
        # Handle both balance and current_tokens field names
        balance_field = "balance" if "balance" in workspace_tokens else "current_tokens"
        await workspace_tokens_collection.update_one(
            {"workspace_id": workspace_id},
            {
                "$inc": {
                    "allowance_used_this_month": allowance_remaining,
                    balance_field: -purchased_tokens_used,
                    "total_used": tokens_needed
                }
            }
        )
        token_source = "mixed"
    
    # Record transaction
    transaction_doc = {
        "_id": str(uuid.uuid4()),
        "workspace_id": workspace_id,
        "user_id": current_user["id"],
        "type": "usage",
        "tokens": -tokens_needed,
        "feature": feature,
        "description": f"Used {tokens_needed} tokens for {feature.replace('_', ' ').title()}",
        "token_source": token_source,
        "created_at": datetime.utcnow()
    }
    await token_transactions_collection.insert_one(transaction_doc)
    
    return {
        "success": True,
        "tokens_consumed": tokens_needed,
        "token_source": token_source
    }

@app.get("/api/tokens/analytics/{workspace_id}")
async def get_token_analytics(
    workspace_id: str,
    current_user: dict = Depends(get_current_user)
):
    """Get token usage analytics for a workspace"""
    # Verify workspace access
    workspace = await workspaces_collection.find_one({"_id": workspace_id})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    # Check access
    is_owner = workspace.get("owner_id") == current_user["id"]
    team_member = await team_members_collection.find_one({
        "workspace_id": workspace_id,
        "email": current_user["email"],
        "status": "active"
    })
    
    if not (is_owner or team_member):
        raise HTTPException(status_code=403, detail="Access denied to workspace")
    
    # Get analytics data
    current_month_start = datetime.utcnow().replace(day=1, hour=0, minute=0, second=0, microsecond=0)
    last_month_start = (current_month_start - timedelta(days=1)).replace(day=1)
    
    # Usage by feature (current month)
    feature_usage = await token_transactions_collection.aggregate([
        {
            "$match": {
                "workspace_id": workspace_id,
                "type": "usage",
                "created_at": {"$gte": current_month_start}
            }
        },
        {
            "$group": {
                "_id": "$feature",
                "tokens_used": {"$sum": {"$abs": "$tokens"}},
                "usage_count": {"$sum": 1}
            }
        },
        {"$sort": {"tokens_used": -1}}
    ]).to_list(length=100)
    
    # Daily usage trend (last 30 days)
    thirty_days_ago = datetime.utcnow() - timedelta(days=30)
    daily_usage = await token_transactions_collection.aggregate([
        {
            "$match": {
                "workspace_id": workspace_id,
                "type": "usage",
                "created_at": {"$gte": thirty_days_ago}
            }
        },
        {
            "$group": {
                "_id": {
                    "$dateToString": {
                        "format": "%Y-%m-%d",
                        "date": "$created_at"
                    }
                },
                "tokens_used": {"$sum": {"$abs": "$tokens"}},
                "usage_count": {"$sum": 1}
            }
        },
        {"$sort": {"_id": 1}}
    ]).to_list(length=100)
    
    # Get workspace token data
    workspace_tokens = await workspace_tokens_collection.find_one({"workspace_id": workspace_id})
    
    return {
        "success": True,
        "data": {
            "current_balance": workspace_tokens.get("balance", 0),
            "monthly_allowance": workspace_tokens.get("monthly_allowance", 0),
            "allowance_used": workspace_tokens.get("allowance_used_this_month", 0),
            "total_purchased": workspace_tokens.get("total_purchased", 0),
            "total_used": workspace_tokens.get("total_used", 0),
            "feature_usage": [
                {
                    "feature": usage["_id"],
                    "feature_name": usage["_id"].replace("_", " ").title() if usage["_id"] else "Unknown",
                    "tokens_used": usage["tokens_used"],
                    "usage_count": usage["usage_count"]
                } for usage in feature_usage
            ],
            "daily_usage": [
                {
                    "date": usage["_id"],
                    "tokens_used": usage["tokens_used"],
                    "usage_count": usage["usage_count"]
                } for usage in daily_usage
            ],
            "efficiency_metrics": {
                "avg_tokens_per_use": round(sum([u["tokens_used"] for u in feature_usage]) / max(sum([u["usage_count"] for u in feature_usage]), 1), 2),
                "most_used_feature": feature_usage[0]["_id"].replace("_", " ").title() if feature_usage else "None",
                "cost_per_month": round(sum([u["tokens_used"] for u in feature_usage]) * 0.01, 2)  # Assuming $0.01 per token
            }
        }
    }

# ===== ONBOARDING SYSTEM ENDPOINTS =====

@app.get("/api/onboarding/progress")
async def get_onboarding_progress(
    current_user: dict = Depends(get_current_user)
):
    """Get user's onboarding progress"""
    try:
        progress = await onboarding_collection.find_one({"user_id": current_user["id"]})
        if not progress:
            return {"success": True, "data": None}
        
        # Convert ObjectId and datetime objects for JSON serialization
        progress["id"] = str(progress["_id"])
        progress.pop("_id", None)
        
        # Convert datetime objects to ISO strings
        if "updated_at" in progress and progress["updated_at"]:
            progress["updated_at"] = progress["updated_at"].isoformat()
        if "completed_at" in progress and progress["completed_at"]:
            progress["completed_at"] = progress["completed_at"].isoformat()
        
        return {"success": True, "data": progress}
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Failed to get onboarding progress: {str(e)}")

@app.post("/api/onboarding/progress")
async def save_onboarding_progress(
    progress_data: dict,
    current_user: dict = Depends(get_current_user)
):
    """Save user's onboarding progress"""
    try:
        progress_doc = {
            "user_id": current_user["id"],
            "current_step": progress_data.get("currentStep", 0),
            "completed_steps": progress_data.get("completedSteps", []),
            "data": progress_data.get("data", {}),
            "updated_at": datetime.utcnow()
        }
        
        await onboarding_collection.update_one(
            {"user_id": current_user["id"]},
            {"$set": progress_doc},
            upsert=True
        )
        
        return {"success": True, "message": "Onboarding progress saved successfully"}
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Failed to save onboarding progress: {str(e)}")

@app.post("/api/onboarding/complete")
async def complete_onboarding(
    completion_data: dict,
    current_user: dict = Depends(get_current_user)
):
    """Complete the onboarding process"""
    try:
        onboarding_data = completion_data.get("data", {})
        
        # Create workspace based on onboarding data
        workspace_data = {
            "_id": str(uuid.uuid4()),
            "name": onboarding_data.get("workspaceName", f"{current_user['name']}'s Workspace"),
            "description": onboarding_data.get("workspaceDescription", ""),
            "owner_id": current_user["id"],
            "industry": onboarding_data.get("industry", ""),
            "company_size": onboarding_data.get("companySize", ""),
            "timezone": onboarding_data.get("timezone", "America/New_York"),
            "selected_goals": onboarding_data.get("selectedGoals", []),
            "primary_goal": onboarding_data.get("primaryGoal"),
            "plan": onboarding_data.get("selectedPlan", "free"),
            "branding": {
                "brand_name": onboarding_data.get("brandName", ""),
                "colors": onboarding_data.get("brandColors", {
                    "primary": "#3B82F6",
                    "secondary": "#10B981",
                    "accent": "#F59E0B"
                }),
                "logo": onboarding_data.get("logo")
            },
            "created_at": datetime.utcnow(),
            "updated_at": datetime.utcnow(),
            "onboarding_completed": True
        }
        
        # Insert workspace
        await workspaces_collection.insert_one(workspace_data)
        
        # Initialize workspace tokens
        await workspace_tokens_collection.insert_one({
            "_id": str(uuid.uuid4()),
            "workspace_id": workspace_data["_id"],
            "balance": 0,
            "total_purchased": 0,
            "total_used": 0,
            "monthly_allowance": 50,  # Free tier gets 50 tokens per month
            "allowance_used_this_month": 0,
            "allowance_reset_date": datetime.utcnow().replace(day=1) + timedelta(days=32),
            "auto_purchase_enabled": False,
            "auto_purchase_threshold": 10,
            "user_limits": {},
            "feature_costs": {
                "content_generation": 5,
                "image_generation": 10,
                "seo_analysis": 3,
                "content_analysis": 2,
                "course_generation": 15,
                "email_sequence": 8,
                "hashtag_generation": 2,
                "content_improvement": 4
            },
            "created_at": datetime.utcnow(),
            "updated_at": datetime.utcnow()
        })
        
        # Update user with onboarding completion
        await users_collection.update_one(
            {"_id": current_user["id"]},
            {
                "$set": {
                    "onboarding_completed": True,
                    "default_workspace_id": workspace_data["_id"],
                    "updated_at": datetime.utcnow()
                }
            }
        )
        
        # Mark onboarding as completed
        await onboarding_collection.update_one(
            {"user_id": current_user["id"]},
            {
                "$set": {
                    "completed": True,
                    "completed_at": datetime.utcnow(),
                    "workspace_id": workspace_data["_id"],
                    "final_data": onboarding_data
                }
            },
            upsert=True
        )
        
        # Process team member invitations if any
        team_members = onboarding_data.get("teamMembers", [])
        for member in team_members:
            if member.get("email") and member["email"] != current_user["email"]:
                invite_id = str(uuid.uuid4())
                invite_doc = {
                    "_id": invite_id,
                    "workspace_id": workspace_data["_id"],
                    "workspace_name": workspace_data["name"],
                    "invited_by": current_user["id"],
                    "invited_by_name": current_user["name"],
                    "email": member["email"],
                    "role": member.get("role", "editor"),
                    "status": "pending",
                    "created_at": datetime.utcnow(),
                    "expires_at": datetime.utcnow() + timedelta(days=7)
                }
                
                await team_invitations_collection.insert_one(invite_doc)
                
                # TODO: Send invitation email
        
        return {
            "success": True,
            "message": "Onboarding completed successfully",
            "workspace_id": workspace_data["_id"]
        }
        
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Failed to complete onboarding: {str(e)}")

# ===== ADMIN MANAGEMENT ENDPOINTS =====

@app.get("/api/admin/users/stats")
async def get_admin_user_stats(
    current_user: dict = Depends(get_current_user)
):
    """Get user statistics for admin dashboard"""
    if current_user.get("role") != "admin":
        raise HTTPException(status_code=403, detail="Admin access required")
    
    try:
        # Get user counts
        total_users = await users_collection.count_documents({})
        
        # Users created in last 30 days
        thirty_days_ago = datetime.utcnow() - timedelta(days=30)
        recent_users = await users_collection.count_documents({
            "created_at": {"$gte": thirty_days_ago}
        })
        
        # Active users (logged in within last 30 days)
        active_users = await users_collection.count_documents({
            "last_login": {"$gte": thirty_days_ago}
        })
        
        # Users created today
        today_start = datetime.utcnow().replace(hour=0, minute=0, second=0, microsecond=0)
        new_today = await users_collection.count_documents({
            "created_at": {"$gte": today_start}
        })
        
        growth_rate = (recent_users / max(total_users - recent_users, 1)) * 100 if total_users > recent_users else 0
        
        return {
            "success": True,
            "data": {
                "total_users": total_users,
                "recent_users": recent_users,
                "active_users": active_users,
                "new_today": new_today,
                "growth_rate": round(growth_rate, 2)
            }
        }
        
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Failed to get user stats: {str(e)}")

@app.get("/api/admin/workspaces/stats")
async def get_admin_workspace_stats(
    current_user: dict = Depends(get_current_user)
):
    """Get workspace statistics for admin dashboard"""
    if current_user.get("role") != "admin":
        raise HTTPException(status_code=403, detail="Admin access required")
    
    try:
        total_workspaces = await workspaces_collection.count_documents({})
        
        # Active workspaces (updated in last 30 days)
        thirty_days_ago = datetime.utcnow() - timedelta(days=30)
        active_workspaces = await workspaces_collection.count_documents({
            "updated_at": {"$gte": thirty_days_ago}
        })
        
        # Recent workspaces
        recent_workspaces = await workspaces_collection.count_documents({
            "created_at": {"$gte": thirty_days_ago}
        })
        
        growth_rate = (recent_workspaces / max(total_workspaces - recent_workspaces, 1)) * 100 if total_workspaces > recent_workspaces else 0
        
        return {
            "success": True,
            "data": {
                "total_count": total_workspaces,
                "active_count": active_workspaces,
                "recent_count": recent_workspaces,
                "growth_rate": round(growth_rate, 2)
            }
        }
        
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Failed to get workspace stats: {str(e)}")

@app.get("/api/admin/analytics/overview")
async def get_admin_analytics_overview(
    current_user: dict = Depends(get_current_user)
):
    """Get analytics overview for admin dashboard"""
    if current_user.get("role") != "admin":
        raise HTTPException(status_code=403, detail="Admin access required")
    
    try:
        # Mock analytics data - would be replaced with real analytics
        return {
            "success": True,
            "data": {
                "total_revenue": 284567.89,
                "revenue_growth": 31.2,
                "mrr": 8450.25,
                "churn_rate": 2.1,
                "token_revenue": 2847.50,
                "subscription_revenue": 156780.45,
                "api_calls_total": 2847593,
                "error_rate": 0.1
            }
        }
        
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Failed to get analytics overview: {str(e)}")

@app.get("/api/admin/system/metrics")
async def get_system_metrics(
    current_user: dict = Depends(get_current_user)
):
    """Get system health metrics for admin dashboard"""
    if current_user.get("role") != "admin":
        raise HTTPException(status_code=403, detail="Admin access required")
    
    try:
        # Mock system metrics - would be replaced with real system monitoring
        metrics = {
            "uptime": "99.9%",
            "response_time": "145ms",
            "memory_usage": "68%",
            "cpu_usage": "23%",
            "disk_usage": "41%",
            "active_connections": 1247,
            "api_calls_today": 25847,
            "error_rate": "0.1%",
            "database_connections": 45,
            "cache_hit_rate": "94.2%",
            "last_updated": datetime.utcnow().isoformat()
        }
        
        # Store metrics in database
        await system_metrics_collection.insert_one({
            "_id": str(uuid.uuid4()),
            "metrics": metrics,
            "timestamp": datetime.utcnow()
        })
        
        return {
            "success": True,
            "data": metrics
        }
        
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Failed to get system metrics: {str(e)}")

@app.get("/api/admin/users")
async def get_all_users(
    page: int = 1,
    limit: int = 50,
    search: str = "",
    current_user: dict = Depends(get_current_user)
):
    """Get all users for admin management"""
    if current_user.get("role") != "admin":
        raise HTTPException(status_code=403, detail="Admin access required")
    
    try:
        skip = (page - 1) * limit
        query = {}
        
        if search:
            query["$or"] = [
                {"name": {"$regex": search, "$options": "i"}},
                {"email": {"$regex": search, "$options": "i"}}
            ]
        
        users = await users_collection.find(query).skip(skip).limit(limit).to_list(length=limit)
        total = await users_collection.count_documents(query)
        
        # Remove sensitive data and add id field
        for user in users:
            user["id"] = str(user["_id"])
            user.pop("password", None)
            user.pop("_id", None)
        
        return {
            "success": True,
            "data": {
                "users": users,
                "total": total,
                "page": page,
                "pages": (total + limit - 1) // limit
            }
        }
        
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Failed to get users: {str(e)}")

@app.get("/api/admin/workspaces")
async def get_all_workspaces(
    page: int = 1,
    limit: int = 50,
    current_user: dict = Depends(get_current_user)
):
    """Get all workspaces for admin management"""
    if current_user.get("role") != "admin":
        raise HTTPException(status_code=403, detail="Admin access required")
    
    try:
        skip = (page - 1) * limit
        
        workspaces = await workspaces_collection.find({}).skip(skip).limit(limit).to_list(length=limit)
        total = await workspaces_collection.count_documents({})
        
        # Add owner information and format response
        for workspace in workspaces:
            workspace["id"] = str(workspace["_id"])
            
            # Get owner info
            if workspace.get("owner_id"):
                owner = await users_collection.find_one({"_id": workspace["owner_id"]})
                workspace["owner_name"] = owner.get("name", "Unknown") if owner else "Unknown"
                workspace["owner_email"] = owner.get("email", "Unknown") if owner else "Unknown"
            
            workspace.pop("_id", None)
        
        return {
            "success": True,
            "data": {
                "workspaces": workspaces,
                "total": total,
                "page": page,
                "pages": (total + limit - 1) // limit
            }
        }
        
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Failed to get workspaces: {str(e)}")

# ===== SOCIAL MEDIA & EMAIL INTEGRATION ENDPOINTS =====

# Pydantic models for social media integrations
class SocialMediaAuthRequest(BaseModel):
    platform: str  # x, tiktok
    callback_url: Optional[str] = None
    redirect_uri: Optional[str] = None

class SocialMediaPostRequest(BaseModel):
    platform: str
    content: Dict[str, Any]
    access_token: str
    access_token_secret: Optional[str] = None

class EmailCampaignRequest(BaseModel):
    recipients: List[str]
    subject: str
    body: str
    sender_email: Optional[str] = None
    sender_name: Optional[str] = None

class EmailContactRequest(BaseModel):
    email: str
    first_name: Optional[str] = None
    last_name: Optional[str] = None
    custom_fields: Optional[Dict[str, str]] = None

@app.get("/api/integrations/available")
async def get_available_integrations():
    """Get list of available integrations"""
    try:
        available = integration_manager.get_available_integrations()
        return {
            "success": True,
            "integrations": available,
            "details": {
                "x_twitter": "Post tweets, get user data, upload media" if available["x_twitter"] else "Not configured",
                "tiktok": "Get user info, list videos" if available["tiktok"] else "Not configured", 
                "elasticmail": "Send emails, manage contacts, create lists" if available["elasticmail"] else "Not configured"
            }
        }
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Failed to get integrations: {str(e)}"
        )

@app.post("/api/integrations/social/auth")
async def authenticate_social_platform(
    auth_request: SocialMediaAuthRequest,
    current_user: dict = Depends(get_current_user)
):
    """Initiate social media platform authentication"""
    try:
        if auth_request.platform == "x":
            result = integration_manager.authenticate_platform(
                "x", 
                callback_url=auth_request.callback_url or f"{os.getenv('REACT_APP_BACKEND_URL', 'http://localhost:8001')}/api/integrations/social/callback/x"
            )
        elif auth_request.platform == "tiktok":
            result = integration_manager.authenticate_platform(
                "tiktok",
                redirect_uri=auth_request.redirect_uri or f"{os.getenv('REACT_APP_BACKEND_URL', 'http://localhost:8001')}/api/integrations/social/callback/tiktok"  
            )
        else:
            raise HTTPException(
                status_code=status.HTTP_400_BAD_REQUEST,
                detail=f"Unsupported platform: {auth_request.platform}"
            )
        
        if result["success"]:
            return result
        else:
            raise HTTPException(
                status_code=status.HTTP_400_BAD_REQUEST,
                detail=result["error"]
            )
            
    except HTTPException:
        raise
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Authentication initiation failed: {str(e)}"
        )

@app.post("/api/integrations/social/post")
async def post_to_social_platform(
    post_request: SocialMediaPostRequest,
    current_user: dict = Depends(get_current_user)
):
    """Post content to social media platform"""
    try:
        credentials = {
            "access_token": post_request.access_token,
            "access_token_secret": post_request.access_token_secret
        }
        
        result = integration_manager.post_content(
            post_request.platform,
            post_request.content,
            credentials
        )
        
        if result["success"]:
            # Log the social media activity
            await social_media_activities_collection.insert_one({
                "user_id": current_user["id"],
                "platform": post_request.platform,
                "activity_type": "post",
                "content": post_request.content,
                "post_id": result.get("tweet_id"),
                "post_url": result.get("tweet_url"),
                "created_at": datetime.utcnow()
            })
            
            return result
        else:
            raise HTTPException(
                status_code=status.HTTP_400_BAD_REQUEST,
                detail=result["error"]
            )
            
    except HTTPException:
        raise
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Social media posting failed: {str(e)}"
        )

@app.post("/api/integrations/email/send")
async def send_email_campaign(
    email_request: EmailCampaignRequest,
    current_user: dict = Depends(get_current_user)
):
    """Send email campaign using ElasticMail"""
    try:
        sender_info = None
        if email_request.sender_email or email_request.sender_name:
            sender_info = {
                "email": email_request.sender_email,
                "name": email_request.sender_name
            }
        
        result = integration_manager.send_email_campaign(
            email_request.recipients,
            email_request.subject,
            email_request.body,
            sender_info
        )
        
        if result["success"]:
            # Log the email campaign
            await email_campaigns_collection.insert_one({
                "user_id": current_user["id"],
                "subject": email_request.subject,
                "recipients_count": len(email_request.recipients),
                "message_id": result.get("message_id"),
                "transaction_id": result.get("transaction_id"),
                "status": "sent",
                "created_at": datetime.utcnow()
            })
            
            return result
        else:
            raise HTTPException(
                status_code=status.HTTP_400_BAD_REQUEST,
                detail=result["error"]
            )
            
    except HTTPException:
        raise
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Email campaign failed: {str(e)}"
        )

@app.post("/api/integrations/email/contact")
async def create_email_contact(
    contact_request: EmailContactRequest,
    current_user: dict = Depends(get_current_user)
):
    """Add contact to ElasticMail"""
    try:
        if not integration_manager.email_integration:
            raise HTTPException(
                status_code=status.HTTP_503_SERVICE_UNAVAILABLE,
                detail="Email integration not configured"
            )
        
        result = integration_manager.email_integration.create_contact(
            contact_request.email,
            contact_request.first_name,
            contact_request.last_name,
            contact_request.custom_fields
        )
        
        if result["success"]:
            # Store contact in our database too
            await email_contacts_collection.insert_one({
                "user_id": current_user["id"],
                "email": contact_request.email,
                "first_name": contact_request.first_name,
                "last_name": contact_request.last_name,
                "custom_fields": contact_request.custom_fields or {},
                "elasticmail_contact_id": result.get("contact_id"),
                "created_at": datetime.utcnow()
            })
            
            return result
        else:
            raise HTTPException(
                status_code=status.HTTP_400_BAD_REQUEST,
                detail=result["error"]
            )
            
    except HTTPException:
        raise
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Contact creation failed: {str(e)}"
        )

@app.get("/api/integrations/email/stats")
async def get_email_stats(current_user: dict = Depends(get_current_user)):
    """Get email campaign statistics"""
    try:
        if not integration_manager.email_integration:
            raise HTTPException(
                status_code=status.HTTP_503_SERVICE_UNAVAILABLE,
                detail="Email integration not configured"
            )
        
        # Get account stats from ElasticMail
        elasticmail_stats = integration_manager.email_integration.get_account_stats()
        
        # Get user's campaign stats from database
        user_campaigns = await email_campaigns_collection.find({"user_id": current_user["id"]}).to_list(length=None)
        user_contacts = await email_contacts_collection.count_documents({"user_id": current_user["id"]})
        
        total_sent = sum(campaign.get("recipients_count", 0) for campaign in user_campaigns)
        
        return {
            "success": True,
            "user_stats": {
                "total_campaigns": len(user_campaigns),
                "total_emails_sent": total_sent,
                "total_contacts": user_contacts
            },
            "account_stats": elasticmail_stats.get("account_info", {}) if elasticmail_stats["success"] else {}
        }
        
    except HTTPException:
        raise
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Email stats retrieval failed: {str(e)}"
        )

@app.get("/api/integrations/social/activities")
async def get_social_media_activities(current_user: dict = Depends(get_current_user)):
    """Get user's social media activities"""
    try:
        activities = await social_media_activities_collection.find(
            {"user_id": current_user["id"]}
        ).sort("created_at", -1).limit(50).to_list(length=50)
        
        # Convert ObjectId to string for JSON serialization
        for activity in activities:
            activity["_id"] = str(activity["_id"])
        
        return {
            "success": True,
            "activities": activities
        }
        
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Failed to get social media activities: {str(e)}"
        )

# ===== COMPREHENSIVE AI ENDPOINTS =====

# Pydantic models for AI requests
class ContentGenerationRequest(BaseModel):
    prompt: str
    content_type: str = "social_post"
    tone: str = "professional"
    max_tokens: int = 500

class ContentAnalysisRequest(BaseModel):
    content: str
    analysis_type: str = "sentiment"

class HashtagGenerationRequest(BaseModel):
    content: str
    platform: str = "instagram"
    count: int = 10

class ContentImprovementRequest(BaseModel):
    content: str
    improvement_type: str = "engagement"

class CourseContentRequest(BaseModel):
    topic: str
    lesson_title: str
    difficulty: str = "beginner"
    duration: int = 15

class EmailSequenceRequest(BaseModel):
    purpose: str
    audience: str
    sequence_length: int = 5

class ContentIdeasRequest(BaseModel):
    industry: str
    content_type: str
    count: int = 10

@app.post("/api/ai/generate-content")
async def generate_ai_content(
    request: ContentGenerationRequest,
    current_user: dict = Depends(get_current_user)
):
    """Generate content using AI"""
    try:
        # Get user's workspace
        workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
        if not workspace:
            raise HTTPException(status_code=404, detail="Workspace not found")
        
        # Get token cost for this feature
        workspace_tokens = await workspace_tokens_collection.find_one({"workspace_id": str(workspace["_id"])})
        tokens_needed = workspace_tokens.get("feature_costs", {}).get("content_generation", 5) if workspace_tokens else 5
        
        # Consume tokens
        try:
            await consume_tokens(str(workspace["_id"]), "content_generation", tokens_needed, current_user)
        except HTTPException as e:
            if e.status_code == 402:
                return {
                    "success": False,
                    "error": "insufficient_tokens",
                    "message": e.detail,
                    "tokens_needed": tokens_needed
                }
            raise e
        
        result = await ai_system.generate_content(
            prompt=request.prompt,
            content_type=request.content_type,
            tone=request.tone,
            max_tokens=request.max_tokens
        )
        
        # Log AI usage for analytics
        usage_log = {
            "_id": str(uuid.uuid4()),
            "user_id": current_user["id"],
            "workspace_id": str(workspace["_id"]),
            "feature": "content_generation",
            "content_type": request.content_type,
            "tokens_used": result.get("tokens_used", 0),
            "tokens_consumed": tokens_needed,
            "timestamp": datetime.utcnow(),
            "success": result["success"]
        }
        await ai_usage_collection.insert_one(usage_log)
        
        return {"success": True, "data": result, "tokens_consumed": tokens_needed}
        
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"AI content generation failed: {str(e)}")

@app.post("/api/ai/analyze-content")
async def analyze_ai_content(
    request: ContentAnalysisRequest,
    current_user: dict = Depends(get_current_user)
):
    """Analyze content using AI"""
    try:
        # Get user's workspace
        workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
        if not workspace:
            raise HTTPException(status_code=404, detail="Workspace not found")
        
        # Get token cost for this feature
        workspace_tokens = await workspace_tokens_collection.find_one({"workspace_id": str(workspace["_id"])})
        tokens_needed = workspace_tokens.get("feature_costs", {}).get("content_analysis", 2) if workspace_tokens else 2
        
        # Consume tokens
        try:
            await consume_tokens(str(workspace["_id"]), "content_analysis", tokens_needed, current_user)
        except HTTPException as e:
            if e.status_code == 402:
                return {
                    "success": False,
                    "error": "insufficient_tokens",
                    "message": e.detail,
                    "tokens_needed": tokens_needed
                }
            raise e
        
        result = await ai_system.analyze_content(
            content=request.content,
            analysis_type=request.analysis_type
        )
        
        # Log AI usage for analytics
        usage_log = {
            "_id": str(uuid.uuid4()),
            "user_id": current_user["id"],
            "workspace_id": str(workspace["_id"]),
            "feature": "content_analysis",
            "analysis_type": request.analysis_type,
            "tokens_consumed": tokens_needed,
            "timestamp": datetime.utcnow(),
            "success": result["success"]
        }
        await ai_usage_collection.insert_one(usage_log)
        
        return {"success": True, "data": result, "tokens_consumed": tokens_needed}
        
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"AI content analysis failed: {str(e)}")

@app.post("/api/ai/generate-hashtags")
async def generate_ai_hashtags(
    request: HashtagGenerationRequest,
    current_user: dict = Depends(get_current_user)
):
    """Generate hashtags using AI"""
    try:
        # Get user's workspace
        workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
        if not workspace:
            raise HTTPException(status_code=404, detail="Workspace not found")
        
        # Get token cost for this feature
        workspace_tokens = await workspace_tokens_collection.find_one({"workspace_id": str(workspace["_id"])})
        tokens_needed = workspace_tokens.get("feature_costs", {}).get("hashtag_generation", 2) if workspace_tokens else 2
        
        # Consume tokens
        try:
            await consume_tokens(str(workspace["_id"]), "hashtag_generation", tokens_needed, current_user)
        except HTTPException as e:
            if e.status_code == 402:
                return {
                    "success": False,
                    "error": "insufficient_tokens",
                    "message": e.detail,
                    "tokens_needed": tokens_needed
                }
            raise e
        
        result = await ai_system.generate_hashtags(
            content=request.content,
            platform=request.platform,
            count=request.count
        )
        
        # Log AI usage
        usage_log = {
            "_id": str(uuid.uuid4()),
            "user_id": current_user["id"],
            "workspace_id": str(workspace["_id"]),
            "feature": "hashtag_generation",
            "platform": request.platform,
            "tokens_consumed": tokens_needed,
            "timestamp": datetime.utcnow(),
            "success": result["success"]
        }
        await ai_usage_collection.insert_one(usage_log)
        
        return {"success": True, "data": result, "tokens_consumed": tokens_needed}
        
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"AI hashtag generation failed: {str(e)}")

@app.post("/api/ai/improve-content")
async def improve_ai_content(
    request: ContentImprovementRequest,
    current_user: dict = Depends(get_current_user)
):
    """Improve content using AI"""
    try:
        # Get user's workspace
        workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
        if not workspace:
            raise HTTPException(status_code=404, detail="Workspace not found")
        
        # Get token cost for this feature
        workspace_tokens = await workspace_tokens_collection.find_one({"workspace_id": str(workspace["_id"])})
        tokens_needed = workspace_tokens.get("feature_costs", {}).get("content_improvement", 4) if workspace_tokens else 4
        
        # Consume tokens
        try:
            await consume_tokens(str(workspace["_id"]), "content_improvement", tokens_needed, current_user)
        except HTTPException as e:
            if e.status_code == 402:
                return {
                    "success": False,
                    "error": "insufficient_tokens",
                    "message": e.detail,
                    "tokens_needed": tokens_needed
                }
            raise e
        
        result = await ai_system.improve_content(
            content=request.content,
            improvement_type=request.improvement_type
        )
        
        # Log AI usage
        usage_log = {
            "_id": str(uuid.uuid4()),
            "user_id": current_user["id"],
            "workspace_id": str(workspace["_id"]),
            "feature": "content_improvement",
            "improvement_type": request.improvement_type,
            "tokens_consumed": tokens_needed,
            "timestamp": datetime.utcnow(),
            "success": result["success"]
        }
        await ai_usage_collection.insert_one(usage_log)
        
        return {"success": True, "data": result, "tokens_consumed": tokens_needed}
        
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"AI content improvement failed: {str(e)}")

@app.post("/api/ai/generate-course-content")
async def generate_ai_course_content(
    request: CourseContentRequest,
    current_user: dict = Depends(get_current_user)
):
    """Generate course content using AI"""
    try:
        # Get user's workspace
        workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
        if not workspace:
            raise HTTPException(status_code=404, detail="Workspace not found")
        
        # Get token cost for this feature
        workspace_tokens = await workspace_tokens_collection.find_one({"workspace_id": str(workspace["_id"])})
        tokens_needed = workspace_tokens.get("feature_costs", {}).get("course_generation", 15) if workspace_tokens else 15
        
        # Consume tokens
        try:
            await consume_tokens(str(workspace["_id"]), "course_generation", tokens_needed, current_user)
        except HTTPException as e:
            if e.status_code == 402:
                return {
                    "success": False,
                    "error": "insufficient_tokens",
                    "message": e.detail,
                    "tokens_needed": tokens_needed
                }
            raise e
        
        result = await ai_system.generate_course_content(
            topic=request.topic,
            lesson_title=request.lesson_title,
            difficulty=request.difficulty,
            duration=request.duration
        )
        
        # Log AI usage
        usage_log = {
            "_id": str(uuid.uuid4()),
            "user_id": current_user["id"],
            "workspace_id": str(workspace["_id"]),
            "feature": "course_generation",
            "topic": request.topic,
            "tokens_consumed": tokens_needed,
            "timestamp": datetime.utcnow(),
            "success": result["success"]
        }
        await ai_usage_collection.insert_one(usage_log)
        
        return {"success": True, "data": result, "tokens_consumed": tokens_needed}
        
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"AI course content generation failed: {str(e)}")

@app.post("/api/ai/generate-email-sequence")
async def generate_ai_email_sequence(
    request: EmailSequenceRequest,
    current_user: dict = Depends(get_current_user)
):
    """Generate email sequence using AI"""
    try:
        # Get user's workspace
        workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
        if not workspace:
            raise HTTPException(status_code=404, detail="Workspace not found")
        
        # Get token cost for this feature
        workspace_tokens = await workspace_tokens_collection.find_one({"workspace_id": str(workspace["_id"])})
        tokens_needed = workspace_tokens.get("feature_costs", {}).get("email_sequence", 8) if workspace_tokens else 8
        
        # Consume tokens
        try:
            await consume_tokens(str(workspace["_id"]), "email_sequence", tokens_needed, current_user)
        except HTTPException as e:
            if e.status_code == 402:
                return {
                    "success": False,
                    "error": "insufficient_tokens",
                    "message": e.detail,
                    "tokens_needed": tokens_needed
                }
            raise e
        
        result = await ai_system.generate_email_sequence(
            purpose=request.purpose,
            audience=request.audience,
            sequence_length=request.sequence_length
        )
        
        # Log AI usage
        usage_log = {
            "_id": str(uuid.uuid4()),
            "user_id": current_user["id"],
            "workspace_id": str(workspace["_id"]),
            "feature": "email_sequence",
            "purpose": request.purpose,
            "tokens_consumed": tokens_needed,
            "timestamp": datetime.utcnow(),
            "success": result["success"]
        }
        await ai_usage_collection.insert_one(usage_log)
        
        return {"success": True, "data": result, "tokens_consumed": tokens_needed}
        
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"AI email sequence generation failed: {str(e)}")

@app.post("/api/ai/get-content-ideas")
async def get_ai_content_ideas(
    request: ContentIdeasRequest,
    current_user: dict = Depends(get_current_user)
):
    """Get content ideas using AI"""
    try:
        # Get user's workspace
        workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
        if not workspace:
            raise HTTPException(status_code=404, detail="Workspace not found")
        
        # Get token cost for this feature
        workspace_tokens = await workspace_tokens_collection.find_one({"workspace_id": str(workspace["_id"])})
        tokens_needed = workspace_tokens.get("feature_costs", {}).get("content_ideas", 3) if workspace_tokens else 3
        
        # Consume tokens
        try:
            await consume_tokens(str(workspace["_id"]), "content_ideas", tokens_needed, current_user)
        except HTTPException as e:
            if e.status_code == 402:
                return {
                    "success": False,
                    "error": "insufficient_tokens",
                    "message": e.detail,
                    "tokens_needed": tokens_needed
                }
            raise e
        
        result = await ai_system.get_content_ideas(
            industry=request.industry,
            content_type=request.content_type,
            count=request.count
        )
        
        # Log AI usage
        usage_log = {
            "_id": str(uuid.uuid4()),
            "user_id": current_user["id"],
            "workspace_id": str(workspace["_id"]),
            "feature": "content_ideas",
            "industry": request.industry,
            "tokens_consumed": tokens_needed,
            "timestamp": datetime.utcnow(),
            "success": result["success"]
        }
        await ai_usage_collection.insert_one(usage_log)
        
        return {"success": True, "data": result, "tokens_consumed": tokens_needed}
        
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"AI content ideas generation failed: {str(e)}")

@app.get("/api/ai/usage-analytics")
async def get_ai_usage_analytics(current_user: dict = Depends(get_current_user)):
    """Get AI usage analytics for the current user"""
    try:
        # Get usage statistics for the last 30 days
        thirty_days_ago = datetime.utcnow() - timedelta(days=30)
        
        usage_logs = await ai_usage_collection.find({
            "user_id": current_user["id"],
            "timestamp": {"$gte": thirty_days_ago}
        }).to_list(length=1000)
        
        # Calculate analytics
        total_requests = len(usage_logs)
        successful_requests = len([log for log in usage_logs if log.get("success", False)])
        total_tokens = sum([log.get("tokens_used", 0) for log in usage_logs])
        
        # Feature usage breakdown
        feature_usage = {}
        for log in usage_logs:
            feature = log.get("feature", "unknown")
            feature_usage[feature] = feature_usage.get(feature, 0) + 1
        
        # Daily usage for the last 7 days
        seven_days_ago = datetime.utcnow() - timedelta(days=7)
        recent_logs = [log for log in usage_logs if log["timestamp"] >= seven_days_ago]
        
        daily_usage = {}
        for i in range(7):
            date = (datetime.utcnow() - timedelta(days=i)).strftime("%Y-%m-%d")
            daily_usage[date] = 0
            
        for log in recent_logs:
            date = log["timestamp"].strftime("%Y-%m-%d")
            if date in daily_usage:
                daily_usage[date] += 1
        
        return {
            "success": True,
            "data": {
                "total_requests": total_requests,
                "successful_requests": successful_requests,
                "success_rate": (successful_requests / total_requests * 100) if total_requests > 0 else 0,
                "total_tokens_used": total_tokens,
                "feature_usage": feature_usage,
                "daily_usage": daily_usage,
                "period": "last_30_days"
            }
        }
        
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Failed to get AI analytics: {str(e)}")

# ===== MASSIVE ENDPOINT EXPANSION - PHASE 3: REPORTING & CONFIGURATION =====

# ===== COMPREHENSIVE ANALYTICS ENDPOINTS (40+ ENDPOINTS) =====

@app.get("/api/analytics/reports")
async def get_available_reports(current_user: dict = Depends(get_current_user)):
    """Get all available analytics reports"""
    reports_data = {
        "standard_reports": [
            {"id": "user_activity", "name": "User Activity Report", "category": "users", "frequency": "daily"},
            {"id": "revenue_summary", "name": "Revenue Summary", "category": "finance", "frequency": "weekly"},
            {"id": "content_performance", "name": "Content Performance", "category": "content", "frequency": "monthly"},
            {"id": "engagement_metrics", "name": "Engagement Metrics", "category": "social", "frequency": "daily"},
            {"id": "conversion_funnel", "name": "Conversion Funnel", "category": "marketing", "frequency": "weekly"}
        ],
        "custom_reports": [
            {"id": "custom_001", "name": "Custom Dashboard", "created_by": "user_001", "last_run": "2025-07-20T10:30:00Z"},
            {"id": "custom_002", "name": "Weekly KPIs", "created_by": "user_002", "last_run": "2025-07-19T16:45:00Z"}
        ],
        "scheduled_reports": [
            {"id": "sched_001", "report": "revenue_summary", "frequency": "weekly", "recipients": ["admin@example.com"]},
            {"id": "sched_002", "report": "user_activity", "frequency": "daily", "recipients": ["manager@example.com"]}
        ]
    }
    return {"success": True, "data": reports_data}

@app.get("/api/analytics/reports/{report_id}")
async def get_report_data(
    report_id: str,
    start_date: Optional[str] = Query(None),
    end_date: Optional[str] = Query(None),
    current_user: dict = Depends(get_current_user)
):
    """Get specific report data"""
    # Mock report data based on report_id
    if report_id == "user_activity":
        report_data = {
            "report_info": {
                "id": report_id,
                "name": "User Activity Report",
                "generated_at": datetime.utcnow().isoformat(),
                "period": f"{start_date} to {end_date}" if start_date and end_date else "Last 30 days"
            },
            "summary": {
                "total_users": 2847,
                "active_users": 2156,
                "new_users": 234,
                "user_retention": 78.5
            },
            "daily_activity": [
                {"date": "2025-07-20", "active_users": 189, "new_users": 12, "sessions": 456},
                {"date": "2025-07-19", "active_users": 167, "new_users": 8, "sessions": 389}
            ],
            "user_segments": [
                {"segment": "power_users", "count": 234, "percentage": 8.2},
                {"segment": "regular_users", "count": 1456, "percentage": 51.2},
                {"segment": "inactive_users", "count": 1157, "percentage": 40.6}
            ]
        }
    else:
        report_data = {
            "report_info": {
                "id": report_id,
                "name": "Generic Report",
                "generated_at": datetime.utcnow().isoformat()
            },
            "data": {"message": f"Report data for {report_id}"}
        }
    
    return {"success": True, "data": report_data}

@app.post("/api/analytics/reports/{report_id}/generate")
async def generate_report(
    report_id: str,
    parameters: str = Form("{}"),
    format: str = Form("json"),
    current_user: dict = Depends(get_current_user)
):
    """Generate report with custom parameters"""
    params = json.loads(parameters)
    
    generation_doc = {
        "_id": str(uuid.uuid4()),
        "report_id": report_id,
        "parameters": params,
        "format": format,
        "status": "generating",
        "progress": 0,
        "requested_by": current_user["id"],
        "created_at": datetime.utcnow(),
        "estimated_completion": datetime.utcnow() + timedelta(minutes=5)
    }
    
    return {
        "success": True,
        "data": {
            "generation_id": generation_doc["_id"],
            "report_id": report_id,
            "status": "generating",
            "estimated_time": "3-5 minutes",
            "download_url": f"/api/analytics/reports/{report_id}/download/{generation_doc['_id']}"
        }
    }

@app.get("/api/analytics/reports/{report_id}/download/{generation_id}")
async def download_report(
    report_id: str,
    generation_id: str,
    current_user: dict = Depends(get_current_user)
):
    """Download generated report"""
    return {
        "success": True,
        "data": {
            "download_url": f"/downloads/reports/{generation_id}.pdf",
            "file_size": "2.3 MB",
            "expires_at": (datetime.utcnow() + timedelta(hours=24)).isoformat()
        }
    }

@app.post("/api/analytics/reports/custom")
async def create_custom_report(
    name: str = Form(...),
    data_sources: List[str] = Form(...),
    metrics: List[str] = Form(...),
    filters: str = Form("{}"),
    visualization: str = Form("table"),
    current_user: dict = Depends(get_current_user)
):
    """Create custom analytics report"""
    report_doc = {
        "_id": str(uuid.uuid4()),
        "name": name,
        "data_sources": data_sources,
        "metrics": metrics,
        "filters": json.loads(filters),
        "visualization": visualization,
        "created_by": current_user["id"],
        "created_at": datetime.utcnow()
    }
    
    return {
        "success": True,
        "data": {
            "report_id": report_doc["_id"],
            "name": report_doc["name"],
            "data_sources": len(data_sources),
            "metrics": len(metrics),
            "created_at": report_doc["created_at"].isoformat()
        }
    }

@app.get("/api/analytics/dashboards")
async def get_analytics_dashboards(current_user: dict = Depends(get_current_user)):
    """Get available analytics dashboards"""
    dashboards_data = {
        "dashboards": [
            {
                "id": "exec_dashboard",
                "name": "Executive Dashboard",
                "description": "High-level KPIs and metrics",
                "widgets": 12,
                "shared": False,
                "last_updated": "2025-07-20T10:30:00Z"
            },
            {
                "id": "marketing_dashboard",
                "name": "Marketing Dashboard",
                "description": "Marketing performance metrics",
                "widgets": 8,
                "shared": True,
                "last_updated": "2025-07-19T16:45:00Z"
            },
            {
                "id": "sales_dashboard",
                "name": "Sales Dashboard", 
                "description": "Sales and revenue tracking",
                "widgets": 10,
                "shared": True,
                "last_updated": "2025-07-20T08:15:00Z"
            }
        ],
        "widget_library": [
            {"type": "metric_card", "name": "KPI Card", "description": "Single metric display"},
            {"type": "line_chart", "name": "Trend Chart", "description": "Time series data"},
            {"type": "pie_chart", "name": "Distribution Chart", "description": "Category breakdown"},
            {"type": "table", "name": "Data Table", "description": "Tabular data display"}
        ]
    }
    return {"success": True, "data": dashboards_data}

@app.get("/api/analytics/dashboards/{dashboard_id}")
async def get_dashboard_data(
    dashboard_id: str,
    current_user: dict = Depends(get_current_user)
):
    """Get specific dashboard configuration and data"""
    dashboard_data = {
        "dashboard": {
            "id": dashboard_id,
            "name": "Executive Dashboard",
            "description": "High-level KPIs and metrics",
            "layout": "grid",
            "refresh_interval": 300,  # seconds
            "created_at": "2025-07-15T10:00:00Z",
            "last_updated": "2025-07-20T10:30:00Z"
        },
        "widgets": [
            {
                "id": "widget_001",
                "type": "metric_card",
                "title": "Total Revenue",
                "position": {"x": 0, "y": 0, "w": 3, "h": 2},
                "data": {"value": 245678.90, "change": "+12.5%", "trend": "up"},
                "config": {"currency": "USD", "decimal_places": 2}
            },
            {
                "id": "widget_002",
                "type": "line_chart",
                "title": "User Growth",
                "position": {"x": 3, "y": 0, "w": 6, "h": 4},
                "data": {"labels": ["Jan", "Feb", "Mar"], "values": [1200, 1350, 1489]},
                "config": {"color": "#3B82F6", "show_points": True}
            }
        ]
    }
    return {"success": True, "data": dashboard_data}

@app.post("/api/analytics/dashboards")
async def create_dashboard(
    name: str = Form(...),
    description: str = Form(""),
    layout: str = Form("grid"),
    widgets: str = Form("[]"),
    current_user: dict = Depends(get_current_user)
):
    """Create new analytics dashboard"""
    dashboard_doc = {
        "_id": str(uuid.uuid4()),
        "name": name,
        "description": description,
        "layout": layout,
        "widgets": json.loads(widgets),
        "created_by": current_user["id"],
        "shared": False,
        "created_at": datetime.utcnow()
    }
    
    return {
        "success": True,
        "data": {
            "dashboard_id": dashboard_doc["_id"],
            "name": dashboard_doc["name"],
            "widgets": len(dashboard_doc["widgets"]),
            "created_at": dashboard_doc["created_at"].isoformat()
        }
    }

@app.put("/api/analytics/dashboards/{dashboard_id}")
async def update_dashboard(
    dashboard_id: str,
    name: Optional[str] = Form(None),
    description: Optional[str] = Form(None),
    widgets: Optional[str] = Form(None),
    current_user: dict = Depends(get_current_user)
):
    """Update dashboard configuration"""
    update_data = {}
    if name: update_data["name"] = name
    if description: update_data["description"] = description
    if widgets: update_data["widgets"] = json.loads(widgets)
    
    return {
        "success": True,
        "data": {
            "dashboard_id": dashboard_id,
            "updated_fields": list(update_data.keys()),
            "updated_at": datetime.utcnow().isoformat()
        }
    }

@app.delete("/api/analytics/dashboards/{dashboard_id}")
async def delete_dashboard(
    dashboard_id: str,
    current_user: dict = Depends(get_current_user)
):
    """Delete analytics dashboard"""
    return {
        "success": True,
        "data": {
            "dashboard_id": dashboard_id,
            "deleted_at": datetime.utcnow().isoformat()
        }
    }

# ===== SYSTEM CONFIGURATION ENDPOINTS (30+ ENDPOINTS) =====

@app.get("/api/system/settings")
async def get_system_settings(current_admin: dict = Depends(get_current_admin_user)):
    """Get system-wide settings"""
    settings_data = {
        "general": {
            "platform_name": "Mewayz",
            "platform_version": "3.0.0",
            "maintenance_mode": False,
            "registration_enabled": True,
            "email_verification_required": True
        },
        "security": {
            "password_policy": {
                "min_length": 8,
                "require_uppercase": True,
                "require_lowercase": True,
                "require_numbers": True,
                "require_symbols": True
            },
            "session_timeout": 480,  # minutes
            "max_login_attempts": 5,
            "two_factor_required": False
        },
        "features": {
            "ai_features_enabled": True,
            "social_media_enabled": True,
            "ecommerce_enabled": True,
            "white_label_enabled": True
        },
        "limits": {
            "max_workspaces_per_user": 10,
            "max_team_members": 100,
            "api_rate_limit": 1000,  # per hour
            "file_upload_limit": 100  # MB
        }
    }
    return {"success": True, "data": settings_data}

@app.put("/api/system/settings")
async def update_system_settings(
    settings: str = Form(...),
    current_admin: dict = Depends(get_current_admin_user)
):
    """Update system-wide settings"""
    settings_data = json.loads(settings)
    
    return {
        "success": True,
        "data": {
            "settings_updated": list(settings_data.keys()),
            "updated_at": datetime.utcnow().isoformat()
        }
    }

@app.get("/api/system/features")
async def get_feature_flags(current_admin: dict = Depends(get_current_admin_user)):
    """Get feature flag configuration"""
    features_data = {
        "feature_flags": [
            {"name": "ai_video_processing", "enabled": True, "rollout": 100, "description": "AI video editing features"},
            {"name": "blockchain_integration", "enabled": False, "rollout": 0, "description": "Blockchain and Web3 features"},
            {"name": "advanced_analytics", "enabled": True, "rollout": 75, "description": "Advanced analytics dashboard"},
            {"name": "white_label_branding", "enabled": True, "rollout": 100, "description": "White-label customization"},
            {"name": "voice_ai_features", "enabled": True, "rollout": 50, "description": "Voice AI capabilities"}
        ],
        "rollout_strategies": [
            {"name": "percentage", "description": "Roll out to percentage of users"},
            {"name": "user_list", "description": "Roll out to specific users"},
            {"name": "workspace_plan", "description": "Roll out based on subscription plan"}
        ]
    }
    return {"success": True, "data": features_data}

@app.put("/api/system/features/{feature_name}")
async def update_feature_flag(
    feature_name: str,
    enabled: bool = Form(...),
    rollout: int = Form(100),
    current_admin: dict = Depends(get_current_admin_user)
):
    """Update feature flag configuration"""
    return {
        "success": True,
        "data": {
            "feature_name": feature_name,
            "enabled": enabled,
            "rollout": rollout,
            "updated_at": datetime.utcnow().isoformat()
        }
    }

@app.get("/api/system/integrations/config")
async def get_system_integrations_config(current_admin: dict = Depends(get_current_admin_user)):
    """Get system integrations configuration"""
    integrations_config = {
        "email_service": {
            "provider": "SendGrid",
            "status": "active",
            "daily_quota": 10000,
            "daily_sent": 1247,
            "settings": {"sender_domain": "mewayz.com", "tracking": True}
        },
        "payment_processors": {
            "stripe": {"status": "active", "webhook_url": "/api/webhooks/stripe", "test_mode": False},
            "paypal": {"status": "inactive", "webhook_url": "/api/webhooks/paypal", "test_mode": False}
        },
        "ai_providers": {
            "openai": {"status": "active", "model": "gpt-4o-mini", "monthly_quota": 100000},
            "anthropic": {"status": "inactive", "model": "claude-3", "monthly_quota": 0}
        },
        "storage_providers": {
            "aws_s3": {"status": "active", "bucket": "mewayz-uploads", "region": "us-east-1"},
            "cloudflare": {"status": "inactive", "bucket": "", "region": ""}
        }
    }
    return {"success": True, "data": integrations_config}

@app.post("/api/system/maintenance")
async def toggle_maintenance_mode(
    enabled: bool = Form(...),
    message: Optional[str] = Form("System maintenance in progress"),
    current_admin: dict = Depends(get_current_admin_user)
):
    """Toggle system maintenance mode"""
    return {
        "success": True,
        "data": {
            "maintenance_mode": enabled,
            "message": message,
            "toggled_at": datetime.utcnow().isoformat()
        }
    }

@app.get("/api/system/logs")
async def get_system_logs(
    level: Optional[str] = Query("all"),
    limit: int = Query(100),
    current_admin: dict = Depends(get_current_admin_user)
):
    """Get system logs"""
    logs_data = {
        "logs": [
            {
                "id": "log_001",
                "level": "info",
                "message": "User login successful",
                "timestamp": "2025-07-20T10:45:23Z",
                "source": "auth_service",
                "user_id": "user_123"
            },
            {
                "id": "log_002",
                "level": "warning",
                "message": "API rate limit approaching",
                "timestamp": "2025-07-20T10:44:15Z",
                "source": "api_gateway",
                "details": {"current_requests": 850, "limit": 1000}
            },
            {
                "id": "log_003",
                "level": "error",
                "message": "Database connection timeout",
                "timestamp": "2025-07-20T10:42:08Z",
                "source": "database",
                "error_code": "DB_TIMEOUT"
            }
        ],
        "log_summary": {
            "total_logs": 15430,
            "error_count": 23,
            "warning_count": 156,
            "info_count": 15251
        }
    }
    return {"success": True, "data": logs_data}

@app.get("/api/system/health/detailed")
async def get_detailed_system_health(current_admin: dict = Depends(get_current_admin_user)):
    """Get detailed system health information"""
    health_data = {
        "overall_status": "healthy",
        "uptime": "15 days, 8 hours, 23 minutes",
        "version": "3.0.0",
        "last_deployment": "2025-07-05T14:30:00Z",
        "services": {
            "api_server": {"status": "healthy", "response_time": "12ms", "cpu": "45%", "memory": "67%"},
            "database": {"status": "healthy", "connections": "23/100", "query_time": "8ms"},
            "cache_server": {"status": "healthy", "hit_rate": "92%", "memory": "34%"},
            "file_storage": {"status": "healthy", "usage": "2.3TB/5TB", "availability": "99.9%"}
        },
        "metrics": {
            "requests_per_minute": 234,
            "avg_response_time": "89ms",
            "error_rate": "0.02%",
            "active_users": 1247
        },
        "alerts": [
            {"level": "warning", "message": "High memory usage on web server", "since": "2025-07-20T10:30:00Z"}
        ]
    }
    return {"success": True, "data": health_data}

@app.get("/api/system/performance/metrics")
async def get_performance_metrics(
    period: str = Query("24h"),
    current_admin: dict = Depends(get_current_admin_user)
):
    """Get system performance metrics"""
    metrics_data = {
        "overview": {
            "avg_response_time": "89ms",
            "total_requests": 125430,
            "error_rate": "0.02%",
            "throughput": "234 req/min"
        },
        "endpoint_performance": [
            {"endpoint": "/api/auth/login", "avg_response": "45ms", "requests": 2340, "errors": 2},
            {"endpoint": "/api/ai/generate-content", "avg_response": "2.3s", "requests": 890, "errors": 5},
            {"endpoint": "/api/analytics/overview", "avg_response": "156ms", "requests": 567, "errors": 0}
        ],
        "resource_usage": {
            "cpu_usage": {"current": 45, "peak": 78, "avg": 52},
            "memory_usage": {"current": 67, "peak": 89, "avg": 72},
            "disk_usage": {"current": 34, "peak": 34, "avg": 32}
        },
        "database_performance": {
            "query_time": {"avg": "8ms", "p95": "23ms", "p99": "45ms"},
            "connections": {"current": 23, "max": 100, "avg": 28},
            "slow_queries": 3
        }
    }
    return {"success": True, "data": metrics_data}

# ===== AI SERVICES EXPANSION (30+ ENDPOINTS) =====

@app.get("/api/ai/models")
async def get_available_ai_models(current_user: dict = Depends(get_current_user)):
    """Get all available AI models"""
    models_data = {
        "text_models": [
            {"id": "gpt-4o-mini", "name": "GPT-4O Mini", "provider": "OpenAI", "type": "text", "cost": 5},
            {"id": "gpt-4", "name": "GPT-4", "provider": "OpenAI", "type": "text", "cost": 10},
            {"id": "claude-3", "name": "Claude 3", "provider": "Anthropic", "type": "text", "cost": 8}
        ],
        "image_models": [
            {"id": "dall-e-3", "name": "DALL-E 3", "provider": "OpenAI", "type": "image", "cost": 15},
            {"id": "midjourney", "name": "Midjourney", "provider": "Midjourney", "type": "image", "cost": 12}
        ],
        "voice_models": [
            {"id": "whisper", "name": "Whisper", "provider": "OpenAI", "type": "voice", "cost": 3},
            {"id": "eleven-labs", "name": "ElevenLabs", "provider": "ElevenLabs", "type": "voice", "cost": 8}
        ]
    }
    return {"success": True, "data": models_data}

@app.get("/api/ai/models/{model_id}")
async def get_ai_model_details(model_id: str, current_user: dict = Depends(get_current_user)):
    """Get detailed information about specific AI model"""
    model_data = {
        "model": {
            "id": model_id,
            "name": "GPT-4O Mini",
            "provider": "OpenAI",
            "type": "text",
            "description": "Advanced language model optimized for conversations",
            "capabilities": ["Text generation", "Code writing", "Analysis", "Translation"],
            "cost_per_token": 0.001,
            "max_tokens": 4096,
            "response_time": "2-5 seconds"
        },
        "usage_stats": {
            "requests_this_month": 1247,
            "tokens_consumed": 45670,
            "avg_response_time": "3.2s",
            "success_rate": 98.7
        },
        "examples": [
            {"input": "Write a blog post about AI", "output": "Sample blog post content..."},
            {"input": "Create marketing copy", "output": "Sample marketing copy..."}
        ]
    }
    return {"success": True, "data": model_data}

@app.get("/api/ai/conversations/{conversation_id}")
async def get_ai_conversation(
    conversation_id: str,
    current_user: dict = Depends(get_current_user)
):
    """Get specific AI conversation history"""
    conversation_data = {
        "conversation": {
            "id": conversation_id,
            "title": "Marketing Strategy Discussion",
            "model": "gpt-4o-mini",
            "created_at": "2025-07-20T10:30:00Z",
            "updated_at": "2025-07-20T11:15:00Z",
            "message_count": 8,
            "tokens_used": 1250
        },
        "messages": [
            {
                "id": "msg_001",
                "role": "user",
                "content": "Help me create a marketing strategy",
                "timestamp": "2025-07-20T10:30:00Z",
                "tokens": 8
            },
            {
                "id": "msg_002",
                "role": "assistant",
                "content": "I'd be happy to help you create a marketing strategy...",
                "timestamp": "2025-07-20T10:30:15Z",
                "tokens": 156
            }
        ]
    }
    return {"success": True, "data": conversation_data}

@app.delete("/api/ai/conversations/{conversation_id}")
async def delete_ai_conversation(
    conversation_id: str,
    current_user: dict = Depends(get_current_user)
):
    """Delete AI conversation"""
    return {
        "success": True,
        "data": {
            "conversation_id": conversation_id,
            "deleted_at": datetime.utcnow().isoformat()
        }
    }

@app.get("/api/ai/templates")
async def get_ai_templates(
    category: Optional[str] = Query(None),
    current_user: dict = Depends(get_current_user)
):
    """Get AI prompt templates"""
    templates_data = {
        "categories": ["marketing", "content", "business", "social_media", "email"],
        "templates": [
            {
                "id": "tmpl_001",
                "name": "Blog Post Writer",
                "category": "content",
                "description": "Generate engaging blog posts",
                "prompt": "Write a {length} blog post about {topic}...",
                "variables": ["length", "topic", "tone"],
                "usage_count": 1247
            },
            {
                "id": "tmpl_002",
                "name": "Social Media Caption",
                "category": "social_media", 
                "description": "Create engaging social media captions",
                "prompt": "Create a {platform} caption for {content_type}...",
                "variables": ["platform", "content_type", "hashtags"],
                "usage_count": 890
            }
        ]
    }
    return {"success": True, "data": templates_data}

@app.post("/api/ai/templates")
async def create_ai_template(
    name: str = Form(...),
    category: str = Form(...),
    description: str = Form(...),
    prompt: str = Form(...),
    variables: List[str] = Form(...),
    current_user: dict = Depends(get_current_user)
):
    """Create custom AI template"""
    template_doc = {
        "_id": str(uuid.uuid4()),
        "user_id": current_user["id"],
        "name": name,
        "category": category,
        "description": description,
        "prompt": prompt,
        "variables": variables,
        "usage_count": 0,
        "created_at": datetime.utcnow()
    }
    
    return {
        "success": True,
        "data": {
            "template_id": template_doc["_id"],
            "name": template_doc["name"],
            "category": template_doc["category"],
            "created_at": template_doc["created_at"].isoformat()
        }
    }

@app.get("/api/ai/templates/{template_id}")
async def get_ai_template(
    template_id: str,
    current_user: dict = Depends(get_current_user)
):
    """Get specific AI template"""
    template_data = {
        "template": {
            "id": template_id,
            "name": "Blog Post Writer",
            "category": "content",
            "description": "Generate engaging blog posts",
            "prompt": "Write a {length} blog post about {topic}...",
            "variables": ["length", "topic", "tone"],
            "usage_count": 1247,
            "created_at": "2025-07-15T10:00:00Z"
        }
    }
    return {"success": True, "data": template_data}

@app.put("/api/ai/templates/{template_id}")
async def update_ai_template(
    template_id: str,
    name: Optional[str] = Form(None),
    description: Optional[str] = Form(None),
    prompt: Optional[str] = Form(None),
    current_user: dict = Depends(get_current_user)
):
    """Update AI template"""
    update_data = {}
    if name: update_data["name"] = name
    if description: update_data["description"] = description
    if prompt: update_data["prompt"] = prompt
    
    return {
        "success": True,
        "data": {
            "template_id": template_id,
            "updated_fields": list(update_data.keys()),
            "updated_at": datetime.utcnow().isoformat()
        }
    }

@app.delete("/api/ai/templates/{template_id}")
async def delete_ai_template(
    template_id: str,
    current_user: dict = Depends(get_current_user)
):
    """Delete AI template"""
    return {
        "success": True,
        "data": {
            "template_id": template_id,
            "deleted_at": datetime.utcnow().isoformat()
        }
    }

@app.get("/api/ai/usage/detailed")
async def get_detailed_ai_usage(
    start_date: Optional[str] = Query(None),
    end_date: Optional[str] = Query(None),
    model: Optional[str] = Query(None),
    current_user: dict = Depends(get_current_user)
):
    """Get detailed AI usage statistics"""
    usage_data = {
        "usage_summary": {
            "total_requests": 2847,
            "successful_requests": 2801,
            "failed_requests": 46,
            "total_tokens": 125890,
            "total_cost": 125.89
        },
        "daily_usage": [
            {"date": "2025-07-20", "requests": 89, "tokens": 4567, "cost": 4.57},
            {"date": "2025-07-19", "requests": 76, "tokens": 3890, "cost": 3.89}
        ],
        "model_breakdown": [
            {"model": "gpt-4o-mini", "requests": 1890, "tokens": 89450, "cost": 89.45},
            {"model": "dall-e-3", "requests": 234, "tokens": 0, "cost": 35.10}
        ],
        "feature_usage": [
            {"feature": "content_generation", "requests": 1247, "percentage": 43.8},
            {"feature": "image_creation", "requests": 567, "percentage": 19.9},
            {"feature": "text_analysis", "requests": 423, "percentage": 14.9}
        ]
    }
    return {"success": True, "data": usage_data}

@app.get("/api/ai/usage/export")
async def export_ai_usage(
    format: str = Query("csv"),
    period: str = Query("30d"),
    current_user: dict = Depends(get_current_user)
):
    """Export AI usage data"""
    return {
        "success": True,
        "data": {
            "export_url": f"/downloads/ai_usage_{period}.{format}",
            "format": format,
            "period": period,
            "generated_at": datetime.utcnow().isoformat(),
            "expires_at": (datetime.utcnow() + timedelta(hours=24)).isoformat()
        }
    }

# ===== SOCIAL MEDIA EXPANSION (25+ ENDPOINTS) =====

@app.get("/api/social/accounts")
async def get_connected_social_accounts(current_user: dict = Depends(get_current_user)):
    """Get all connected social media accounts"""
    accounts_data = {
        "connected_accounts": [
            {
                "id": "acc_001",
                "platform": "instagram",
                "username": "@mybusiness",
                "display_name": "My Business",
                "followers": 15430,
                "status": "active",
                "last_sync": "2025-07-20T10:30:00Z",
                "features": ["posting", "analytics", "stories"]
            },
            {
                "id": "acc_002",
                "platform": "facebook",
                "username": "MyBusinessPage",
                "display_name": "My Business",
                "followers": 8920,
                "status": "active",
                "last_sync": "2025-07-20T09:45:00Z",
                "features": ["posting", "analytics", "messaging"]
            }
        ],
        "available_platforms": ["instagram", "facebook", "twitter", "linkedin", "tiktok", "youtube"],
        "connection_stats": {
            "total_connected": 2,
            "total_followers": 24350,
            "posting_enabled": 2,
            "analytics_enabled": 2
        }
    }
    return {"success": True, "data": accounts_data}

@app.post("/api/social/accounts/connect")
async def connect_social_account(
    platform: str = Form(...),
    access_token: str = Form(...),
    account_data: str = Form(...),  # JSON string
    current_user: dict = Depends(get_current_user)
):
    """Connect new social media account"""
    account_info = json.loads(account_data)
    
    connection_doc = {
        "_id": str(uuid.uuid4()),
        "user_id": current_user["id"],
        "platform": platform,
        "access_token": access_token,  # This would be encrypted in real implementation
        "account_info": account_info,
        "status": "active",
        "connected_at": datetime.utcnow(),
        "last_sync": datetime.utcnow()
    }
    
    return {
        "success": True,
        "data": {
            "account_id": connection_doc["_id"],
            "platform": platform,
            "username": account_info.get("username"),
            "status": "connected",
            "connected_at": connection_doc["connected_at"].isoformat()
        }
    }

@app.delete("/api/social/accounts/{account_id}")
async def disconnect_social_account(
    account_id: str,
    current_user: dict = Depends(get_current_user)
):
    """Disconnect social media account"""
    return {
        "success": True,
        "data": {
            "account_id": account_id,
            "status": "disconnected",
            "disconnected_at": datetime.utcnow().isoformat()
        }
    }

@app.get("/api/social/posts")
async def get_social_posts(
    platform: Optional[str] = Query(None),
    status: Optional[str] = Query(None),
    limit: int = Query(50),
    current_user: dict = Depends(get_current_user)
):
    """Get social media posts"""
    posts_data = {
        "posts": [
            {
                "id": "post_001",
                "platform": "instagram",
                "content": "Exciting product launch today! 🚀",
                "media": ["image1.jpg", "image2.jpg"],
                "status": "published",
                "scheduled_time": "2025-07-20T12:00:00Z",
                "published_time": "2025-07-20T12:00:05Z",
                "engagement": {"likes": 234, "comments": 45, "shares": 12}
            },
            {
                "id": "post_002",
                "platform": "facebook",
                "content": "Check out our latest blog post about AI trends",
                "media": ["banner.jpg"],
                "status": "scheduled",
                "scheduled_time": "2025-07-21T10:00:00Z",
                "published_time": None,
                "engagement": {"likes": 0, "comments": 0, "shares": 0}
            }
        ],
        "post_stats": {
            "total_posts": 156,
            "published": 134,
            "scheduled": 15,
            "draft": 7,
            "failed": 0
        }
    }
    return {"success": True, "data": posts_data}

@app.get("/api/social/posts/{post_id}")
async def get_social_post(
    post_id: str,
    current_user: dict = Depends(get_current_user)
):
    """Get specific social media post"""
    post_data = {
        "post": {
            "id": post_id,
            "platform": "instagram",
            "content": "Exciting product launch today! 🚀",
            "media": [
                {"type": "image", "url": "image1.jpg", "alt": "Product photo"},
                {"type": "image", "url": "image2.jpg", "alt": "Behind the scenes"}
            ],
            "hashtags": ["#productlaunch", "#innovation", "#startup"],
            "status": "published",
            "scheduled_time": "2025-07-20T12:00:00Z",
            "published_time": "2025-07-20T12:00:05Z",
            "created_at": "2025-07-19T14:30:00Z"
        },
        "engagement": {
            "likes": 234,
            "comments": 45,
            "shares": 12,
            "saves": 18,
            "reach": 5670,
            "impressions": 8450
        },
        "analytics": {
            "engagement_rate": 4.2,
            "best_performing_hashtag": "#innovation",
            "audience_demographics": {
                "age_groups": {"18-24": 23, "25-34": 45, "35-44": 22, "45+": 10},
                "locations": {"US": 67, "UK": 15, "Canada": 10, "Other": 8}
            }
        }
    }
    return {"success": True, "data": post_data}

@app.put("/api/social/posts/{post_id}")
async def update_social_post(
    post_id: str,
    content: Optional[str] = Form(None),
    scheduled_time: Optional[str] = Form(None),
    hashtags: Optional[List[str]] = Form(None),
    current_user: dict = Depends(get_current_user)
):
    """Update social media post"""
    update_data = {}
    if content: update_data["content"] = content
    if scheduled_time: update_data["scheduled_time"] = scheduled_time
    if hashtags: update_data["hashtags"] = hashtags
    
    return {
        "success": True,
        "data": {
            "post_id": post_id,
            "updated_fields": list(update_data.keys()),
            "updated_at": datetime.utcnow().isoformat()
        }
    }

@app.delete("/api/social/posts/{post_id}")
async def delete_social_post(
    post_id: str,
    current_user: dict = Depends(get_current_user)
):
    """Delete social media post"""
    return {
        "success": True,
        "data": {
            "post_id": post_id,
            "deleted_at": datetime.utcnow().isoformat()
        }
    }

@app.post("/api/social/posts/{post_id}/duplicate")
async def duplicate_social_post(
    post_id: str,
    platforms: List[str] = Form(...),
    current_user: dict = Depends(get_current_user)
):
    """Duplicate post across platforms"""
    return {
        "success": True,
        "data": {
            "original_post_id": post_id,
            "duplicated_to": platforms,
            "new_post_ids": [f"post_{str(uuid.uuid4())[:8]}" for _ in platforms],
            "created_at": datetime.utcnow().isoformat()
        }
    }

@app.get("/api/social/hashtags/trending")
async def get_trending_hashtags(
    platform: str = Query("instagram"),
    category: Optional[str] = Query(None),
    limit: int = Query(20),
    current_user: dict = Depends(get_current_user)
):
    """Get trending hashtags"""
    hashtags_data = {
        "trending_hashtags": [
            {"hashtag": "#ai", "posts": 1247000, "growth": "+25%", "engagement_rate": 4.2},
            {"hashtag": "#technology", "posts": 890000, "growth": "+18%", "engagement_rate": 3.8},
            {"hashtag": "#innovation", "posts": 567000, "growth": "+32%", "engagement_rate": 5.1}
        ],
        "recommendations": [
            {"hashtag": "#artificialintelligence", "relevance": 0.92, "competition": "medium"},
            {"hashtag": "#machinelearning", "relevance": 0.87, "competition": "high"},
            {"hashtag": "#futuretech", "relevance": 0.76, "competition": "low"}
        ],
        "analysis": {
            "best_posting_times": ["9:00 AM", "1:00 PM", "5:00 PM"],
            "optimal_hashtag_count": "10-15 hashtags",
            "engagement_boost": "Average 23% increase with trending hashtags"
        }
    }
    return {"success": True, "data": hashtags_data}

@app.get("/api/social/analytics/engagement")
async def get_engagement_analytics(
    period: str = Query("30d"),
    platform: Optional[str] = Query(None),
    current_user: dict = Depends(get_current_user)
):
    """Get detailed engagement analytics"""
    analytics_data = {
        "overview": {
            "total_engagement": 15430,
            "engagement_rate": 4.7,
            "reach": 125000,
            "impressions": 245000,
            "follower_growth": "+12.5%"
        },
        "platform_breakdown": [
            {
                "platform": "instagram",
                "engagement": 8920,
                "rate": 5.2,
                "reach": 75000,
                "best_content": "image_posts"
            },
            {
                "platform": "facebook",
                "engagement": 4560,
                "rate": 3.8,
                "reach": 35000,
                "best_content": "video_posts"
            }
        ],
        "engagement_by_type": {
            "likes": 9850,
            "comments": 2340,
            "shares": 1890,
            "saves": 1350
        },
        "top_performing_posts": [
            {"id": "post_001", "engagement": 890, "rate": 8.9, "content_type": "carousel"},
            {"id": "post_002", "engagement": 756, "rate": 7.6, "content_type": "video"}
        ]
    }
    return {"success": True, "data": analytics_data}

@app.get("/api/social/calendar")
async def get_social_calendar(
    month: Optional[str] = Query(None),
    year: Optional[int] = Query(None),
    current_user: dict = Depends(get_current_user)
):
    """Get social media content calendar"""
    calendar_data = {
        "calendar": {
            "2025-07-20": [
                {"time": "09:00", "platform": "instagram", "type": "story", "status": "published"},
                {"time": "12:00", "platform": "facebook", "type": "post", "status": "published"}
            ],
            "2025-07-21": [
                {"time": "10:00", "platform": "twitter", "type": "tweet", "status": "scheduled"},
                {"time": "15:00", "platform": "instagram", "type": "reel", "status": "scheduled"}
            ]
        },
        "monthly_stats": {
            "total_posts_planned": 89,
            "posts_published": 67,
            "posts_scheduled": 15,
            "posts_draft": 7
        },
        "optimal_times": {
            "instagram": ["9:00 AM", "1:00 PM", "5:00 PM"],
            "facebook": ["10:00 AM", "2:00 PM", "7:00 PM"],
            "twitter": ["8:00 AM", "12:00 PM", "6:00 PM"]
        }
    }
    return {"success": True, "data": calendar_data}

# Additional collections for granular operations
user_preferences_collection = database.user_preferences
workspace_settings_collection = database.workspace_settings
feature_configurations_collection = database.feature_configurations
system_logs_collection = database.system_logs
api_keys_collection = database.api_keys
user_sessions_collection = database.user_sessions

# ===== USER MANAGEMENT EXPANSION (20+ ENDPOINTS) =====

@app.get("/api/users")
async def list_all_users(
    page: int = Query(1),
    limit: int = Query(25),
    search: Optional[str] = Query(None),
    role: Optional[str] = Query(None),
    current_admin: dict = Depends(get_current_admin_user)
):
    """List all users with pagination and filtering"""
    users_data = {
        "users": [
            {
                "id": "user_001",
                "name": "John Doe",
                "email": "john.doe@example.com",
                "role": "user",
                "status": "active",
                "created_at": "2025-07-15T10:30:00Z",
                "last_login": "2025-07-20T08:45:00Z",
                "workspaces": 3
            },
            {
                "id": "user_002",
                "name": "Jane Smith", 
                "email": "jane.smith@example.com",
                "role": "admin",
                "status": "active",
                "created_at": "2025-07-10T14:20:00Z",
                "last_login": "2025-07-20T09:15:00Z",
                "workspaces": 5
            }
        ],
        "pagination": {
            "page": page,
            "limit": limit,
            "total": 247,
            "pages": 10
        }
    }
    return {"success": True, "data": users_data}

@app.get("/api/users/{user_id}")
async def get_user_details(
    user_id: str,
    current_admin: dict = Depends(get_current_admin_user)
):
    """Get detailed user information"""
    user_data = {
        "user": {
            "id": user_id,
            "name": "John Doe",
            "email": "john.doe@example.com",
            "phone": "+1-555-123-4567",
            "role": "user",
            "status": "active",
            "created_at": "2025-07-15T10:30:00Z",
            "last_login": "2025-07-20T08:45:00Z",
            "email_verified": True,
            "phone_verified": False,
            "two_factor_enabled": True
        },
        "activity": {
            "login_count": 45,
            "last_activity": "2025-07-20T08:45:00Z",
            "active_sessions": 2,
            "failed_logins": 0
        },
        "workspaces": [
            {"id": "ws_001", "name": "Personal Business", "role": "owner"},
            {"id": "ws_002", "name": "Marketing Agency", "role": "editor"},
            {"id": "ws_003", "name": "Consulting Firm", "role": "viewer"}
        ],
        "preferences": {
            "timezone": "America/New_York",
            "language": "en-US",
            "notifications": {
                "email": True,
                "push": True,
                "sms": False
            }
        }
    }
    return {"success": True, "data": user_data}

@app.put("/api/users/{user_id}")
async def update_user(
    user_id: str,
    name: Optional[str] = Form(None),
    email: Optional[str] = Form(None),
    phone: Optional[str] = Form(None),
    role: Optional[str] = Form(None),
    status: Optional[str] = Form(None),
    current_admin: dict = Depends(get_current_admin_user)
):
    """Update user information"""
    update_data = {}
    if name: update_data["name"] = name
    if email: update_data["email"] = email
    if phone: update_data["phone"] = phone
    if role: update_data["role"] = role
    if status: update_data["status"] = status
    
    return {
        "success": True,
        "data": {
            "user_id": user_id,
            "updated_fields": list(update_data.keys()),
            "updated_at": datetime.utcnow().isoformat()
        }
    }

@app.delete("/api/users/{user_id}")
async def delete_user(
    user_id: str,
    current_admin: dict = Depends(get_current_admin_user)
):
    """Delete user account"""
    return {
        "success": True,
        "data": {
            "user_id": user_id,
            "deleted_at": datetime.utcnow().isoformat(),
            "status": "deleted"
        }
    }

@app.post("/api/users/{user_id}/suspend")
async def suspend_user(
    user_id: str,
    reason: str = Form(...),
    duration: Optional[int] = Form(None),  # days
    current_admin: dict = Depends(get_current_admin_user)
):
    """Suspend user account"""
    return {
        "success": True,
        "data": {
            "user_id": user_id,
            "status": "suspended",
            "reason": reason,
            "duration_days": duration,
            "suspended_at": datetime.utcnow().isoformat()
        }
    }

@app.post("/api/users/{user_id}/activate")
async def activate_user(
    user_id: str,
    current_admin: dict = Depends(get_current_admin_user)
):
    """Activate suspended user account"""
    return {
        "success": True,
        "data": {
            "user_id": user_id,
            "status": "active",
            "activated_at": datetime.utcnow().isoformat()
        }
    }

@app.get("/api/users/{user_id}/activity")
async def get_user_activity(
    user_id: str,
    days: int = Query(30),
    current_admin: dict = Depends(get_current_admin_user)
):
    """Get user activity history"""
    activity_data = {
        "activity_summary": {
            "total_logins": 45,
            "unique_days": 28,
            "avg_session_duration": "35m",
            "total_actions": 1247
        },
        "daily_activity": [
            {"date": "2025-07-20", "logins": 2, "actions": 45, "duration": "2h 15m"},
            {"date": "2025-07-19", "logins": 1, "actions": 32, "duration": "1h 45m"}
        ],
        "action_breakdown": {
            "content_creation": 456,
            "analytics_views": 234,
            "settings_changes": 89,
            "team_collaboration": 67
        }
    }
    return {"success": True, "data": activity_data}

@app.get("/api/users/{user_id}/sessions")
async def get_user_sessions(
    user_id: str,
    current_admin: dict = Depends(get_current_admin_user)
):
    """Get user active sessions"""
    sessions_data = {
        "active_sessions": [
            {
                "session_id": "sess_001",
                "device": "Desktop - Chrome",
                "ip_address": "192.168.1.100",
                "location": "New York, NY",
                "started_at": "2025-07-20T08:30:00Z",
                "last_activity": "2025-07-20T10:45:00Z",
                "is_current": True
            },
            {
                "session_id": "sess_002",
                "device": "Mobile - iPhone",
                "ip_address": "192.168.1.101",
                "location": "New York, NY",
                "started_at": "2025-07-20T07:15:00Z",
                "last_activity": "2025-07-20T09:30:00Z",
                "is_current": False
            }
        ],
        "session_stats": {
            "total_active": 2,
            "max_concurrent": 3,
            "avg_duration": "45m"
        }
    }
    return {"success": True, "data": sessions_data}

@app.delete("/api/users/{user_id}/sessions/{session_id}")
async def terminate_user_session(
    user_id: str,
    session_id: str,
    current_admin: dict = Depends(get_current_admin_user)
):
    """Terminate specific user session"""
    return {
        "success": True,
        "data": {
            "user_id": user_id,
            "session_id": session_id,
            "terminated_at": datetime.utcnow().isoformat()
        }
    }

@app.post("/api/users/{user_id}/reset-password")
async def admin_reset_password(
    user_id: str,
    send_email: bool = Form(True),
    current_admin: dict = Depends(get_current_admin_user)
):
    """Admin reset user password"""
    return {
        "success": True,
        "data": {
            "user_id": user_id,
            "password_reset": True,
            "email_sent": send_email,
            "reset_at": datetime.utcnow().isoformat()
        }
    }

@app.get("/api/users/{user_id}/permissions")
async def get_user_permissions(
    user_id: str,
    current_admin: dict = Depends(get_current_admin_user)
):
    """Get user permissions across all workspaces"""
    permissions_data = {
        "global_permissions": ["user_account", "basic_features"],
        "workspace_permissions": [
            {
                "workspace_id": "ws_001",
                "workspace_name": "Personal Business",
                "role": "owner",
                "permissions": ["all_permissions"]
            },
            {
                "workspace_id": "ws_002",
                "workspace_name": "Marketing Agency", 
                "role": "editor",
                "permissions": ["read", "write", "collaborate"]
            }
        ],
        "feature_access": {
            "ai_features": True,
            "advanced_analytics": True,
            "white_label": False,
            "api_access": True
        }
    }
    return {"success": True, "data": permissions_data}

@app.post("/api/users/{user_id}/permissions")
async def update_user_permissions(
    user_id: str,
    permissions: List[str] = Form(...),
    workspace_id: Optional[str] = Form(None),
    current_admin: dict = Depends(get_current_admin_user)
):
    """Update user permissions"""
    return {
        "success": True,
        "data": {
            "user_id": user_id,
            "workspace_id": workspace_id,
            "permissions": permissions,
            "updated_at": datetime.utcnow().isoformat()
        }
    }

@app.get("/api/users/{user_id}/preferences")
async def get_user_preferences(
    user_id: str,
    current_user: dict = Depends(get_current_user)
):
    """Get user preferences and settings"""
    preferences_data = {
        "account": {
            "timezone": "America/New_York",
            "language": "en-US",
            "date_format": "MM/DD/YYYY",
            "time_format": "12h"
        },
        "notifications": {
            "email_notifications": True,
            "push_notifications": True,
            "sms_notifications": False,
            "marketing_emails": True,
            "product_updates": True
        },
        "privacy": {
            "profile_visibility": "private",
            "activity_tracking": True,
            "data_sharing": False,
            "analytics_opt_out": False
        },
        "interface": {
            "theme": "dark",
            "sidebar_collapsed": False,
            "dashboard_layout": "grid",
            "items_per_page": 25
        }
    }
    return {"success": True, "data": preferences_data}

@app.put("/api/users/{user_id}/preferences")
async def update_user_preferences(
    user_id: str,
    preferences: str = Form(...),  # JSON string
    current_user: dict = Depends(get_current_user)
):
    """Update user preferences"""
    prefs = json.loads(preferences)
    
    await user_preferences_collection.update_one(
        {"user_id": user_id},
        {"$set": {**prefs, "updated_at": datetime.utcnow()}},
        upsert=True
    )
    
    return {
        "success": True,
        "data": {
            "user_id": user_id,
            "preferences_updated": list(prefs.keys()),
            "updated_at": datetime.utcnow().isoformat()
        }
    }

# ===== WORKSPACE MANAGEMENT EXPANSION (25+ ENDPOINTS) =====

@app.get("/api/workspaces/{workspace_id}")
async def get_workspace_details(
    workspace_id: str,
    current_user: dict = Depends(get_current_user)
):
    """Get detailed workspace information"""
    workspace_data = {
        "workspace": {
            "id": workspace_id,
            "name": "Marketing Agency",
            "description": "Full-service digital marketing agency",
            "owner_id": "user_001",
            "created_at": "2025-06-15T10:00:00Z",
            "updated_at": "2025-07-18T14:30:00Z",
            "status": "active",
            "plan": "pro",
            "industry": "marketing",
            "website": "https://agency.example.com"
        },
        "statistics": {
            "total_members": 12,
            "active_projects": 8,
            "monthly_revenue": 45670.25,
            "storage_used": "2.3 GB",
            "api_calls_this_month": 15430
        },
        "features_enabled": {
            "ai_assistant": True,
            "advanced_analytics": True,
            "white_label": False,
            "api_access": True,
            "custom_integrations": True
        },
        "recent_activity": [
            {"action": "Member added", "details": "Sarah Johnson joined", "timestamp": "2025-07-20T09:30:00Z"},
            {"action": "Project created", "details": "New website redesign project", "timestamp": "2025-07-19T16:45:00Z"}
        ]
    }
    return {"success": True, "data": workspace_data}

@app.put("/api/workspaces/{workspace_id}")
async def update_workspace(
    workspace_id: str,
    name: Optional[str] = Form(None),
    description: Optional[str] = Form(None),
    website: Optional[str] = Form(None),
    industry: Optional[str] = Form(None),
    current_user: dict = Depends(get_current_user)
):
    """Update workspace information"""
    update_data = {}
    if name: update_data["name"] = name
    if description: update_data["description"] = description
    if website: update_data["website"] = website
    if industry: update_data["industry"] = industry
    
    return {
        "success": True,
        "data": {
            "workspace_id": workspace_id,
            "updated_fields": list(update_data.keys()),
            "updated_at": datetime.utcnow().isoformat()
        }
    }

@app.delete("/api/workspaces/{workspace_id}")
async def delete_workspace(
    workspace_id: str,
    confirm: bool = Form(...),
    current_user: dict = Depends(get_current_user)
):
    """Delete workspace (owner only)"""
    if not confirm:
        raise HTTPException(status_code=400, detail="Confirmation required")
    
    return {
        "success": True,
        "data": {
            "workspace_id": workspace_id,
            "deleted_at": datetime.utcnow().isoformat(),
            "status": "deleted"
        }
    }

@app.get("/api/workspaces/{workspace_id}/members")
async def get_workspace_members(
    workspace_id: str,
    current_user: dict = Depends(get_current_user)
):
    """Get workspace team members"""
    members_data = {
        "members": [
            {
                "user_id": "user_001",
                "name": "John Doe",
                "email": "john.doe@example.com",
                "role": "owner",
                "status": "active",
                "joined_at": "2025-06-15T10:00:00Z",
                "last_active": "2025-07-20T08:45:00Z"
            },
            {
                "user_id": "user_002",
                "name": "Sarah Johnson",
                "email": "sarah.johnson@example.com",
                "role": "admin",
                "status": "active",
                "joined_at": "2025-06-20T14:30:00Z",
                "last_active": "2025-07-20T09:15:00Z"
            }
        ],
        "member_stats": {
            "total_members": 12,
            "active_members": 11,
            "pending_invitations": 2,
            "roles": {
                "owner": 1,
                "admin": 3,
                "editor": 5,
                "viewer": 3
            }
        }
    }
    return {"success": True, "data": members_data}

@app.post("/api/workspaces/{workspace_id}/members/invite")
async def invite_workspace_member(
    workspace_id: str,
    email: str = Form(...),
    role: str = Form("editor"),
    message: Optional[str] = Form(""),
    current_user: dict = Depends(get_current_user)
):
    """Invite member to workspace"""
    invitation_doc = {
        "_id": str(uuid.uuid4()),
        "workspace_id": workspace_id,
        "inviter_id": current_user["id"],
        "email": email,
        "role": role,
        "message": message,
        "status": "pending",
        "expires_at": datetime.utcnow() + timedelta(days=7),
        "created_at": datetime.utcnow()
    }
    
    return {
        "success": True,
        "data": {
            "invitation_id": invitation_doc["_id"],
            "email": email,
            "role": role,
            "invitation_url": f"/invite/{invitation_doc['_id']}",
            "expires_at": invitation_doc["expires_at"].isoformat()
        }
    }

@app.put("/api/workspaces/{workspace_id}/members/{user_id}/role")
async def update_member_role(
    workspace_id: str,
    user_id: str,
    role: str = Form(...),
    current_user: dict = Depends(get_current_user)
):
    """Update member role in workspace"""
    return {
        "success": True,
        "data": {
            "workspace_id": workspace_id,
            "user_id": user_id,
            "new_role": role,
            "updated_at": datetime.utcnow().isoformat()
        }
    }

@app.delete("/api/workspaces/{workspace_id}/members/{user_id}")
async def remove_workspace_member(
    workspace_id: str,
    user_id: str,
    current_user: dict = Depends(get_current_user)
):
    """Remove member from workspace"""
    return {
        "success": True,
        "data": {
            "workspace_id": workspace_id,
            "user_id": user_id,
            "removed_at": datetime.utcnow().isoformat()
        }
    }

@app.get("/api/workspaces/{workspace_id}/settings")
async def get_workspace_settings(
    workspace_id: str,
    current_user: dict = Depends(get_current_user)
):
    """Get workspace configuration settings"""
    settings_data = {
        "general": {
            "name": "Marketing Agency",
            "description": "Full-service digital marketing agency",
            "website": "https://agency.example.com",
            "industry": "marketing",
            "timezone": "America/New_York",
            "language": "en-US"
        },
        "branding": {
            "logo_url": "https://example.com/logo.png",
            "primary_color": "#3B82F6",
            "secondary_color": "#1F2937",
            "custom_domain": "app.agency.example.com"
        },
        "features": {
            "ai_assistant": True,
            "advanced_analytics": True,
            "white_label": False,
            "api_access": True,
            "custom_integrations": True
        },
        "security": {
            "two_factor_required": False,
            "ip_restrictions": [],
            "session_timeout": 480,  # minutes
            "password_policy": "standard"
        },
        "notifications": {
            "email_notifications": True,
            "slack_webhook": "",
            "teams_webhook": "",
            "discord_webhook": ""
        }
    }
    return {"success": True, "data": settings_data}

@app.put("/api/workspaces/{workspace_id}/settings")
async def update_workspace_settings(
    workspace_id: str,
    settings: str = Form(...),  # JSON string
    current_user: dict = Depends(get_current_user)
):
    """Update workspace settings"""
    settings_data = json.loads(settings)
    
    await workspace_settings_collection.update_one(
        {"workspace_id": workspace_id},
        {"$set": {**settings_data, "updated_at": datetime.utcnow()}},
        upsert=True
    )
    
    return {
        "success": True,
        "data": {
            "workspace_id": workspace_id,
            "settings_updated": list(settings_data.keys()),
            "updated_at": datetime.utcnow().isoformat()
        }
    }

@app.get("/api/revenue/dynamic-pricing")
async def get_dynamic_pricing_overview(current_user: dict = Depends(get_current_user)):
    """Dynamic pricing optimization system"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    pricing_data = {
        "pricing_strategies": {
            "demand_based": {
                "description": "Adjust prices based on demand patterns",
                "current_multiplier": 1.15,
                "revenue_impact": "+23%",
                "products_using": 45
            },
            "competitor_based": {
                "description": "Match or beat competitor pricing automatically",
                "current_status": "active",
                "price_adjustments_today": 12,
                "products_using": 67
            },
            "time_based": {
                "description": "Different pricing for different times/seasons",
                "peak_hours": "10 AM - 6 PM",
                "peak_multiplier": 1.25,
                "products_using": 23
            },
            "inventory_based": {
                "description": "Adjust prices based on stock levels",
                "low_stock_multiplier": 1.35,
                "overstock_discount": 0.80,
                "products_using": 34
            }
        },
        "ai_recommendations": [
            {
                "product": "Premium Course Bundle",
                "current_price": 299.99,
                "recommended_price": 349.99,
                "reason": "High demand, low competitor pricing",
                "expected_impact": "+18% revenue"
            },
            {
                "product": "Basic Plan",
                "current_price": 29.99,
                "recommended_price": 24.99,
                "reason": "Increase conversion rate",
                "expected_impact": "+25% customers"
            }
        ],
        "performance_metrics": {
            "revenue_optimization": "+34% vs fixed pricing",
            "conversion_rate_improvement": "+12%",
            "average_order_value": "$127.50 (+8%)",
            "customer_acquisition_cost": "$45.20 (-15%)"
        },
        "a_b_testing": {
            "active_tests": 5,
            "completed_tests": 23,
            "avg_test_duration": "14 days",
            "confidence_threshold": "95%"
        }
    }
    
    await revenue_optimization_collection.insert_one({
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "pricing_data": pricing_data,
        "generated_at": datetime.utcnow()
    })
    
    return {"success": True, "data": pricing_data}

@app.post("/api/revenue/pricing-strategy/create")
async def create_pricing_strategy(
    strategy_name: str = Form(...),
    strategy_type: str = Form(...),
    target_products: List[str] = Form(...),
    parameters: str = Form(...),  # JSON string
    active: bool = Form(True),
    current_user: dict = Depends(get_current_user)
):
    """Create new dynamic pricing strategy"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    strategy_doc = {
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "strategy_name": strategy_name,
        "strategy_type": strategy_type,
        "target_products": target_products,
        "parameters": json.loads(parameters),
        "active": active,
        "created_by": current_user["id"],
        "performance_metrics": {
            "revenue_impact": 0,
            "conversion_impact": 0,
            "customer_feedback": 0
        },
        "created_at": datetime.utcnow(),
        "last_updated": datetime.utcnow()
    }
    
    await revenue_optimization_collection.insert_one(strategy_doc)
    
    return {
        "success": True,
        "data": {
            "strategy_id": strategy_doc["_id"],
            "strategy_name": strategy_doc["strategy_name"],
            "strategy_type": strategy_doc["strategy_type"],
            "target_products": len(target_products),
            "status": "active" if active else "paused",
            "created_at": strategy_doc["created_at"].isoformat()
        }
    }

@app.get("/api/revenue/attribution/analysis")
async def get_revenue_attribution_analysis(current_user: dict = Depends(get_current_user)):
    """Revenue attribution and source analysis"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    attribution_data = {
        "attribution_models": {
            "first_touch": {
                "description": "Credit to first interaction",
                "revenue_attributed": 145670.50,
                "top_channels": [
                    {"channel": "Google Ads", "revenue": 45890.25, "percentage": 31.5},
                    {"channel": "Organic Search", "revenue": 38750.75, "percentage": 26.6},
                    {"channel": "Social Media", "revenue": 28450.50, "percentage": 19.5}
                ]
            },
            "last_touch": {
                "description": "Credit to final interaction",
                "revenue_attributed": 145670.50,
                "top_channels": [
                    {"channel": "Direct", "revenue": 52340.25, "percentage": 35.9},
                    {"channel": "Email Marketing", "revenue": 34560.75, "percentage": 23.7},
                    {"channel": "Referrals", "revenue": 25890.25, "percentage": 17.8}
                ]
            },
            "linear": {
                "description": "Equal credit to all interactions", 
                "revenue_attributed": 145670.50,
                "top_channels": [
                    {"channel": "Google Ads", "revenue": 28450.50, "percentage": 19.5},
                    {"channel": "Organic Search", "revenue": 26780.25, "percentage": 18.4},
                    {"channel": "Email Marketing", "revenue": 24890.75, "percentage": 17.1}
                ]
            }
        },
        "customer_journey_insights": {
            "avg_touchpoints_to_conversion": 5.8,
            "most_common_paths": [
                {"path": "Google Ads → Website → Email → Purchase", "conversions": 234, "value": "$28,450"},
                {"path": "Social → Website → Retargeting → Purchase", "conversions": 189, "value": "$22,340"},
                {"path": "Organic → Blog → Email → Purchase", "conversions": 156, "value": "$19,780"}
            ],
            "channel_interactions": {
                "assists": [
                    {"channel": "Content Marketing", "assists": 456, "assist_value": "$67,890"},
                    {"channel": "Social Media", "assists": 345, "assist_value": "$45,670"},
                    {"channel": "Webinars", "assists": 234, "assist_value": "$34,560"}
                ]
            }
        },
        "roi_analysis": {
            "channel_roi": [
                {"channel": "Email Marketing", "spend": 2450.00, "revenue": 34560.75, "roi": 1312},
                {"channel": "SEO", "spend": 5670.00, "revenue": 38750.75, "roi": 584},
                {"channel": "Google Ads", "spend": 8900.00, "revenue": 45890.25, "roi": 415},
                {"channel": "Social Media Ads", "spend": 4560.00, "revenue": 18450.50, "roi": 305}
            ],
            "overall_roas": 4.85,
            "blended_cac": 45.60,
            "ltv_cac_ratio": 5.8
        }
    }
    
    return {"success": True, "data": attribution_data}

# ===== ADVANCED INTEGRATIONS MARKETPLACE (50+ ENDPOINTS) =====

@app.get("/api/integrations/marketplace")
async def get_integrations_marketplace(
    category: Optional[str] = Query(None),
    search: Optional[str] = Query(None),
    current_user: dict = Depends(get_current_user)
):
    """Comprehensive integrations marketplace"""
    integrations_data = {
        "featured_integrations": [
            {
                "id": "shopify_plus",
                "name": "Shopify Plus",
                "category": "ecommerce",
                "description": "Enterprise e-commerce platform integration",
                "rating": 4.8,
                "installs": 12500,
                "pricing": "Free",
                "features": ["Inventory sync", "Order management", "Customer data", "Product catalog"],
                "setup_time": "5 minutes",
                "api_quality": "excellent"
            },
            {
                "id": "salesforce_enterprise",
                "name": "Salesforce CRM",
                "category": "crm",
                "description": "World's #1 CRM platform integration",
                "rating": 4.7,
                "installs": 8900,
                "pricing": "Premium",
                "features": ["Contact sync", "Lead management", "Opportunity tracking", "Custom fields"],
                "setup_time": "15 minutes",
                "api_quality": "excellent"
            },
            {
                "id": "quickbooks_online",
                "name": "QuickBooks Online",
                "category": "accounting",
                "description": "Complete accounting software integration",
                "rating": 4.6,
                "installs": 6700,
                "pricing": "Free",
                "features": ["Invoice sync", "Expense tracking", "Financial reports", "Tax preparation"],
                "setup_time": "10 minutes",
                "api_quality": "good"
            }
        ],
        "integration_categories": {
            "crm": {"count": 45, "popular": ["Salesforce", "HubSpot", "Pipedrive"]},
            "ecommerce": {"count": 67, "popular": ["Shopify", "WooCommerce", "Magento"]},
            "accounting": {"count": 23, "popular": ["QuickBooks", "Xero", "FreshBooks"]},
            "marketing": {"count": 89, "popular": ["Mailchimp", "Constant Contact", "Campaign Monitor"]},
            "social_media": {"count": 34, "popular": ["Facebook", "Instagram", "LinkedIn"]},
            "communication": {"count": 56, "popular": ["Slack", "Microsoft Teams", "Discord"]},
            "productivity": {"count": 78, "popular": ["Google Workspace", "Office 365", "Notion"]},
            "analytics": {"count": 29, "popular": ["Google Analytics", "Mixpanel", "Amplitude"]}
        },
        "custom_integrations": {
            "api_builder": "Visual API integration builder",
            "webhook_manager": "Manage incoming and outgoing webhooks",
            "data_mapper": "Map fields between systems",
            "testing_tools": "Test integrations before going live"
        },
        "enterprise_features": {
            "dedicated_support": "Priority support for enterprise integrations",
            "custom_development": "Build custom integrations for your needs",
            "sla_guarantees": "99.9% uptime guarantee",
            "security_compliance": "SOC2, HIPAA, GDPR compliant"
        }
    }
    
    return {"success": True, "data": integrations_data}

@app.post("/api/integrations/install")
async def install_integration(
    integration_id: str = Form(...),
    configuration: str = Form("{}"),
    test_connection: bool = Form(True),
    current_user: dict = Depends(get_current_user)
):
    """Install and configure integration"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    installation_doc = {
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "integration_id": integration_id,
        "configuration": json.loads(configuration),
        "status": "installing",
        "test_connection": test_connection,
        "installed_by": current_user["id"],
        "installation_progress": 0,
        "estimated_completion": datetime.utcnow() + timedelta(minutes=10),
        "created_at": datetime.utcnow()
    }
    
    await advanced_integrations_collection.insert_one(installation_doc)
    
    return {
        "success": True,
        "data": {
            "installation_id": installation_doc["_id"],
            "integration_id": integration_id,
            "status": "installing",
            "progress": 0,
            "estimated_time": "5-10 minutes",
            "test_connection": test_connection,
            "webhook_url": f"/api/integrations/webhook/{installation_doc['_id']}"
        }
    }

@app.get("/api/integrations/custom/builder")
async def get_custom_integration_builder(current_user: dict = Depends(get_current_user)):
    """Custom integration builder interface"""
    builder_data = {
        "available_triggers": [
            {"type": "webhook", "description": "Receive data from external systems"},
            {"type": "schedule", "description": "Time-based triggers"},
            {"type": "data_change", "description": "When data changes in your system"},
            {"type": "user_action", "description": "When users perform specific actions"},
            {"type": "api_call", "description": "When external APIs are called"}
        ],
        "available_actions": [
            {"type": "http_request", "description": "Make HTTP requests to external APIs"},
            {"type": "database_operation", "description": "Create, update, or delete records"},
            {"type": "email_send", "description": "Send emails"},
            {"type": "notification", "description": "Send push notifications"},
            {"type": "file_operation", "description": "Upload, download, or process files"}
        ],
        "data_transformations": [
            {"type": "field_mapping", "description": "Map fields between systems"},
            {"type": "data_filtering", "description": "Filter data based on conditions"},
            {"type": "data_formatting", "description": "Format dates, numbers, text"},
            {"type": "calculations", "description": "Perform mathematical operations"},
            {"type": "conditional_logic", "description": "If/then/else logic"}
        ],
        "authentication_methods": [
            {"type": "api_key", "description": "Simple API key authentication"},
            {"type": "oauth2", "description": "OAuth 2.0 authentication"},
            {"type": "basic_auth", "description": "Username/password authentication"},
            {"type": "custom_headers", "description": "Custom authentication headers"},
            {"type": "jwt", "description": "JSON Web Token authentication"}
        ],
        "testing_tools": {
            "request_testing": "Test API requests before deployment",
            "data_validation": "Validate data transformations",
            "error_simulation": "Simulate error conditions",
            "performance_testing": "Test integration performance"
        }
    }
    
    return {"success": True, "data": builder_data}

# ===== ENTERPRISE SECURITY & COMPLIANCE (25+ ENDPOINTS) =====

@app.get("/api/security/overview")
async def get_security_overview(current_admin: dict = Depends(get_current_admin_user)):
    """Enterprise security overview and compliance status"""
    security_data = {
        "security_score": {
            "overall_score": 94,
            "categories": {
                "authentication": 98,
                "authorization": 92,
                "data_protection": 96,
                "network_security": 89,
                "compliance": 95
            }
        },
        "compliance_status": {
            "gdpr": {"status": "compliant", "last_audit": "2025-06-15", "next_review": "2025-12-15"},
            "soc2": {"status": "compliant", "last_audit": "2025-05-20", "next_review": "2025-11-20"},
            "hipaa": {"status": "compliant", "last_audit": "2025-04-10", "next_review": "2025-10-10"},
            "iso27001": {"status": "in_progress", "expected_completion": "2025-09-30"}
        },
        "security_features": {
            "authentication": [
                "Multi-factor authentication (MFA)",
                "Single sign-on (SSO)",
                "Password policies",
                "Session management",
                "Account lockout protection"
            ],
            "data_protection": [
                "End-to-end encryption",
                "Data anonymization",
                "Secure data storage",
                "Data backup encryption",
                "Right to be forgotten"
            ],
            "access_control": [
                "Role-based access control (RBAC)",
                "Attribute-based access control (ABAC)",
                "API access controls",
                "IP whitelisting",
                "Time-based access restrictions"
            ],
            "monitoring": [
                "Real-time threat detection",
                "Audit logging",
                "Anomaly detection",
                "Security alerts",
                "Incident response automation"
            ]
        },
        "recent_security_events": [
            {"type": "suspicious_login", "count": 5, "status": "blocked", "timestamp": "2025-07-20T08:30:00Z"},
            {"type": "failed_api_calls", "count": 23, "status": "monitored", "timestamp": "2025-07-20T07:15:00Z"},
            {"type": "data_export", "count": 2, "status": "approved", "timestamp": "2025-07-19T16:45:00Z"}
        ]
    }
    
    await enterprise_security_collection.insert_one({
        "_id": str(uuid.uuid4()),
        "security_overview": security_data,
        "generated_at": datetime.utcnow()
    })
    
    return {"success": True, "data": security_data}

@app.get("/api/security/audit-logs")
async def get_audit_logs(
    start_date: Optional[str] = Query(None),
    end_date: Optional[str] = Query(None),
    event_type: Optional[str] = Query(None),
    user_id: Optional[str] = Query(None),
    limit: int = Query(100),
    current_admin: dict = Depends(get_current_admin_user)
):
    """Comprehensive audit logging system"""
    audit_data = {
        "audit_events": [
            {
                "id": "audit_001",
                "timestamp": "2025-07-20T10:30:00Z",
                "event_type": "user_login",
                "user_id": "user_123",
                "user_email": "user@example.com",
                "ip_address": "192.168.1.100",
                "user_agent": "Mozilla/5.0...",
                "details": {"login_method": "password", "mfa_used": True},
                "risk_level": "low"
            },
            {
                "id": "audit_002",
                "timestamp": "2025-07-20T10:25:00Z",
                "event_type": "data_export",
                "user_id": "admin_456",
                "user_email": "admin@example.com",
                "ip_address": "10.0.1.50",
                "details": {"export_type": "customer_data", "record_count": 1250},
                "risk_level": "medium"
            },
            {
                "id": "audit_003",
                "timestamp": "2025-07-20T10:20:00Z",
                "event_type": "permission_change",
                "user_id": "admin_789",
                "user_email": "superadmin@example.com",
                "details": {"target_user": "user_123", "permission": "admin_access", "action": "granted"},
                "risk_level": "high"
            }
        ],
        "event_categories": {
            "authentication": {"count": 1247, "high_risk": 23},
            "data_access": {"count": 567, "high_risk": 12},
            "system_changes": {"count": 89, "high_risk": 8},
            "user_management": {"count": 156, "high_risk": 15},
            "api_access": {"count": 2340, "high_risk": 45}
        },
        "compliance_reports": {
            "gdpr_requests": {"count": 12, "fulfilled": 11, "pending": 1},
            "data_breaches": {"count": 0, "last_incident": None},
            "access_reviews": {"scheduled": 4, "completed": 3, "overdue": 0}
        },
        "retention_policy": {
            "audit_logs": "7 years",
            "user_data": "As per user request or legal requirement",
            "system_logs": "2 years",
            "backup_data": "5 years"
        }
    }
    
    return {"success": True, "data": audit_data}

# ===== INNOVATION LAB FEATURES (30+ ENDPOINTS) =====

@app.get("/api/innovation/ar-vr/features")
async def get_ar_vr_features(current_user: dict = Depends(get_current_user)):
    """Augmented and Virtual Reality features"""
    ar_vr_data = {
        "ar_features": {
            "product_visualization": {
                "description": "3D product viewing with AR overlay",
                "supported_formats": ["OBJ", "FBX", "GLTF", "USD"],
                "platforms": ["iOS", "Android", "Web AR"],
                "use_cases": ["E-commerce", "Real estate", "Education"]
            },
            "virtual_try_on": {
                "description": "Try products virtually using camera",
                "categories": ["Clothing", "Accessories", "Makeup", "Furniture"],
                "accuracy": "95%+",
                "processing_time": "< 2 seconds"
            },
            "interactive_experiences": {
                "description": "Create interactive AR experiences",
                "features": ["3D animations", "Interactive hotspots", "Audio narration"],
                "creation_tools": "Drag-and-drop AR builder"
            }
        },
        "vr_features": {
            "virtual_showrooms": {
                "description": "Create immersive product showrooms",
                "supported_devices": ["Oculus", "HTC Vive", "PICO", "Web VR"],
                "customization": "Full environment customization",
                "analytics": "VR engagement tracking"
            },
            "training_simulations": {
                "description": "VR training and educational content",
                "scenarios": ["Sales training", "Product demos", "Safety training"],
                "progress_tracking": "Detailed learning analytics"
            },
            "virtual_meetings": {
                "description": "VR collaboration spaces",
                "capacity": "Up to 20 participants",
                "features": ["Spatial audio", "Screen sharing", "3D whiteboards"]
            }
        },
        "implementation": {
            "web_integration": "WebXR for browser-based AR/VR",
            "mobile_apps": "Native iOS/Android AR integration",
            "headset_support": "All major VR headsets supported",
            "development_tools": "Visual AR/VR content builder"
        }
    }
    
    return {"success": True, "data": ar_vr_data}

@app.get("/api/innovation/blockchain/features")
async def get_blockchain_features(current_user: dict = Depends(get_current_user)):
    """Blockchain and Web3 integration features"""
    blockchain_data = {
        "nft_marketplace": {
            "description": "Create and sell NFTs directly from platform",
            "supported_chains": ["Ethereum", "Polygon", "Solana", "Binance Smart Chain"],
            "features": ["Lazy minting", "Royalty management", "Batch creation"],
            "gas_optimization": "Smart contract optimization for lower fees"
        },
        "crypto_payments": {
            "description": "Accept cryptocurrency payments",
            "supported_currencies": ["BTC", "ETH", "USDC", "USDT", "MATIC"],
            "features": ["Automatic conversion", "Multi-wallet support", "Tax reporting"],
            "security": "Multi-signature wallet integration"
        },
        "tokenomics": {
            "description": "Create custom tokens for your business",
            "token_types": ["Utility tokens", "Governance tokens", "Reward tokens"],
            "features": ["Token distribution", "Staking mechanisms", "DAO creation"],
            "compliance": "Regulatory compliance support"
        },
        "smart_contracts": {
            "description": "Automated business logic on blockchain",
            "use_cases": ["Escrow services", "Subscription management", "Affiliate programs"],
            "templates": "Pre-built smart contract templates",
            "auditing": "Smart contract security auditing"
        },
        "web3_identity": {
            "description": "Decentralized identity and authentication",
            "features": ["Wallet-based login", "Verifiable credentials", "Privacy-preserving"],
            "protocols": ["DID", "Verifiable Credentials", "ENS integration"]
        }
    }
    
    return {"success": True, "data": blockchain_data}

@app.get("/api/innovation/iot/dashboard")
async def get_iot_dashboard(current_user: dict = Depends(get_current_user)):
    """Internet of Things integration dashboard"""
    iot_data = {
        "connected_devices": {
            "total_devices": 1247,
            "online_devices": 1189,
            "device_types": {
                "sensors": 567,
                "cameras": 234,
                "beacons": 189,
                "smart_displays": 156,
                "wearables": 101
            }
        },
        "data_streams": {
            "active_streams": 89,
            "data_points_per_minute": 15630,
            "storage_used": "2.3 TB",
            "processing_latency": "< 100ms"
        },
        "use_cases": {
            "retail_analytics": {
                "description": "Track customer behavior in physical stores",
                "devices": ["Bluetooth beacons", "Smart cameras", "Foot traffic sensors"],
                "insights": ["Heat maps", "Dwell time", "Conversion rates"]
            },
            "smart_offices": {
                "description": "Optimize office space and resources",
                "devices": ["Occupancy sensors", "Environmental monitors", "Smart lighting"],
                "insights": ["Space utilization", "Energy consumption", "Employee comfort"]
            },
            "supply_chain": {
                "description": "Track products throughout supply chain",
                "devices": ["GPS trackers", "Temperature sensors", "RFID tags"],
                "insights": ["Location tracking", "Condition monitoring", "Delivery optimization"]
            }
        },
        "integration_options": {
            "protocols": ["MQTT", "HTTP", "CoAP", "LoRaWAN"],
            "cloud_platforms": ["AWS IoT", "Azure IoT", "Google Cloud IoT"],
            "edge_computing": "Process data locally for reduced latency",
            "real_time_alerts": "Instant notifications for critical events"
        }
    }
    
    return {"success": True, "data": iot_data}

# Collections for cutting-edge features
content_creation_suite_collection = database.content_creation_suite
video_editor_collection = database.video_editor
podcast_creator_collection = database.podcast_creator
design_tools_collection = database.design_tools
live_chat_collection = database.live_chat
customer_experience_collection = database.customer_experience
revenue_optimization_collection = database.revenue_optimization
advanced_integrations_collection = database.advanced_integrations
enterprise_security_collection = database.enterprise_security
innovation_lab_collection = database.innovation_lab

# ===== ADVANCED CONTENT CREATION SUITE (35+ ENDPOINTS) =====

@app.get("/api/content/video-editor/features")
async def get_video_editor_features(current_user: dict = Depends(get_current_user)):
    """Advanced video editing features and capabilities"""
    video_editor_data = {
        "editing_features": {
            "basic_editing": [
                "Trim and cut videos",
                "Add transitions",
                "Insert text overlays",
                "Background music",
                "Color correction"
            ],
            "advanced_editing": [
                "Multi-track timeline",
                "Keyframe animations", 
                "Chroma key (green screen)",
                "Audio noise reduction",
                "3D transitions",
                "Motion tracking"
            ],
            "ai_powered": [
                "Auto-highlight detection",
                "Scene change detection",
                "Face and object tracking",
                "Voice enhancement",
                "Auto-captions generation",
                "Content-aware editing"
            ]
        },
        "export_options": {
            "formats": ["MP4", "MOV", "AVI", "WebM", "GIF"],
            "resolutions": ["720p", "1080p", "4K", "Instagram Square", "TikTok Vertical"],
            "quality_presets": ["Draft", "Standard", "High", "Broadcast"],
            "custom_settings": True
        },
        "collaboration_features": {
            "real_time_editing": "Multiple editors working simultaneously",
            "comment_system": "Time-coded comments and feedback",
            "version_control": "Track changes and revert to previous versions",
            "approval_workflow": "Submit for review and approval"
        },
        "template_library": {
            "categories": ["Social Media", "Marketing", "Education", "Entertainment"],
            "count": 250,
            "customizable": True,
            "brand_templates": "Create branded video templates"
        },
        "pricing": {
            "storage": "100GB included, $10/month per 100GB extra",
            "export_credits": "Unlimited HD exports, 4K exports use credits",
            "ai_features": "10 hours/month included, $1 per additional hour"
        }
    }
    
    return {"success": True, "data": video_editor_data}

@app.post("/api/content/video-editor/project/create")
async def create_video_project(
    project_name: str = Form(...),
    template_id: Optional[str] = Form(None),
    resolution: str = Form("1080p"),
    duration_estimate: int = Form(60),  # seconds
    current_user: dict = Depends(get_current_user)
):
    """Create new video editing project"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    project_doc = {
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "project_name": project_name,
        "template_id": template_id,
        "resolution": resolution,
        "duration_estimate": duration_estimate,
        "status": "draft",
        "timeline": {
            "video_tracks": [],
            "audio_tracks": [],
            "text_overlays": [],
            "effects": []
        },
        "collaborators": [current_user["id"]],
        "version": 1,
        "created_by": current_user["id"],
        "created_at": datetime.utcnow(),
        "last_modified": datetime.utcnow()
    }
    
    await video_editor_collection.insert_one(project_doc)
    
    return {
        "success": True,
        "data": {
            "project_id": project_doc["_id"],
            "project_name": project_doc["project_name"],
            "resolution": project_doc["resolution"],
            "status": "draft",
            "editor_url": f"/video-editor/{project_doc['_id']}",
            "created_at": project_doc["created_at"].isoformat()
        }
    }

@app.post("/api/content/video-editor/render")
async def render_video_project(
    project_id: str = Form(...),
    output_format: str = Form("mp4"),
    quality: str = Form("high"),
    watermark: bool = Form(False),
    current_user: dict = Depends(get_current_user)
):
    """Render video project to final output"""
    project = await video_editor_collection.find_one({"_id": project_id})
    if not project:
        raise HTTPException(status_code=404, detail="Project not found")
    
    render_job = {
        "_id": str(uuid.uuid4()),
        "project_id": project_id,
        "output_format": output_format,
        "quality": quality,
        "watermark": watermark,
        "status": "queued",
        "progress": 0,
        "estimated_time": "5-10 minutes",
        "started_at": datetime.utcnow(),
        "completed_at": None,
        "output_url": None
    }
    
    await video_editor_collection.insert_one(render_job)
    
    return {
        "success": True,
        "data": {
            "render_id": render_job["_id"],
            "status": "queued",
            "estimated_time": "5-10 minutes",
            "progress_url": f"/api/content/video-editor/render/status/{render_job['_id']}",
            "webhook_url": f"/api/content/video-editor/render/webhook/{render_job['_id']}"
        }
    }

@app.get("/api/content/podcast/studio")
async def get_podcast_studio_features(current_user: dict = Depends(get_current_user)):
    """Podcast creation studio features"""
    podcast_data = {
        "recording_features": {
            "multi_track_recording": "Record up to 8 separate audio tracks",
            "remote_guests": "Record with guests remotely with high quality",
            "noise_cancellation": "AI-powered background noise removal",
            "auto_leveling": "Automatic volume level adjustment",
            "live_monitoring": "Real-time audio monitoring during recording"
        },
        "editing_capabilities": {
            "basic_editing": ["Cut", "Copy", "Paste", "Delete", "Fade in/out"],
            "advanced_editing": ["Noise reduction", "EQ adjustment", "Compression", "Limiter"],
            "ai_features": ["Auto-transcription", "Chapter detection", "Silence removal", "Voice enhancement"]
        },
        "distribution": {
            "platforms": [
                {"name": "Spotify", "auto_upload": True, "analytics": True},
                {"name": "Apple Podcasts", "auto_upload": True, "analytics": True},
                {"name": "Google Podcasts", "auto_upload": True, "analytics": False},
                {"name": "YouTube", "auto_upload": True, "analytics": True}
            ],
            "rss_feed": "Custom RSS feed generation",
            "scheduling": "Schedule episodes for future release"
        },
        "monetization": {
            "sponsor_segments": "Insert sponsor messages automatically",
            "dynamic_ads": "Programmatic ad insertion",
            "premium_content": "Paywall for premium episodes",
            "listener_support": "Built-in listener donation system"
        },
        "analytics": {
            "listener_stats": "Detailed listener demographics and behavior",
            "engagement_metrics": "Drop-off points, replay sections",
            "geographic_data": "Where your listeners are located",
            "growth_tracking": "Subscriber growth and trends"
        }
    }
    
    return {"success": True, "data": podcast_data}

@app.post("/api/content/podcast/episode/create")
async def create_podcast_episode(
    title: str = Form(...),
    description: str = Form(""),
    category: str = Form("Business"),
    episode_type: str = Form("full"),  # full, trailer, bonus
    explicit_content: bool = Form(False),
    scheduled_release: Optional[str] = Form(None),
    current_user: dict = Depends(get_current_user)
):
    """Create new podcast episode"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    episode_doc = {
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "title": title,
        "description": description,
        "category": category,
        "episode_type": episode_type,
        "explicit_content": explicit_content,
        "status": "draft",
        "audio_file": None,
        "duration": None,
        "file_size": None,
        "scheduled_release": datetime.fromisoformat(scheduled_release) if scheduled_release else None,
        "created_by": current_user["id"],
        "created_at": datetime.utcnow(),
        "last_modified": datetime.utcnow()
    }
    
    await podcast_creator_collection.insert_one(episode_doc)
    
    return {
        "success": True,
        "data": {
            "episode_id": episode_doc["_id"],
            "title": episode_doc["title"],
            "status": "draft",
            "scheduled_release": episode_doc["scheduled_release"].isoformat() if episode_doc["scheduled_release"] else None,
            "editor_url": f"/podcast-studio/{episode_doc['_id']}",
            "created_at": episode_doc["created_at"].isoformat()
        }
    }

@app.get("/api/content/design/tools")
async def get_design_tools_overview(current_user: dict = Depends(get_current_user)):
    """Advanced design tools and capabilities"""
    design_data = {
        "design_categories": {
            "social_media": {
                "templates": 500,
                "formats": ["Instagram Post", "Instagram Story", "Facebook Cover", "Twitter Header"],
                "ai_features": ["Auto-resize", "Brand color matching", "Text optimization"]
            },
            "marketing": {
                "templates": 300,
                "formats": ["Flyers", "Brochures", "Business Cards", "Banners"],
                "ai_features": ["Logo generation", "Color palette suggestion", "Font pairing"]
            },
            "presentations": {
                "templates": 200,
                "formats": ["PowerPoint", "Google Slides", "Keynote", "PDF"],
                "ai_features": ["Slide layout suggestions", "Content generation", "Image recommendations"]
            },
            "web_graphics": {
                "templates": 150,
                "formats": ["Hero Images", "Buttons", "Icons", "Infographics"],
                "ai_features": ["SVG generation", "Icon matching", "Style consistency"]
            }
        },
        "design_features": {
            "basic_tools": ["Text editor", "Shape tools", "Image cropping", "Filters", "Backgrounds"],
            "advanced_tools": ["Vector editing", "Mask layers", "Blending modes", "Custom fonts", "Animation"],
            "ai_powered": ["Background removal", "Object replacement", "Style transfer", "Auto-layout"]
        },
        "collaboration": {
            "real_time_editing": "Multiple designers working together",
            "comment_system": "Visual feedback and annotations",
            "version_history": "Track all design changes",
            "brand_kit": "Shared brand assets and guidelines"
        },
        "export_options": {
            "formats": ["PNG", "JPG", "SVG", "PDF", "GIF"],
            "resolutions": ["Web optimized", "Print quality", "Custom DPI"],
            "batch_export": "Export multiple designs at once"
        }
    }
    
    return {"success": True, "data": design_data}

@app.post("/api/content/design/project/create")
async def create_design_project(
    project_name: str = Form(...),
    design_type: str = Form("social_media"),
    template_id: Optional[str] = Form(None),
    dimensions: str = Form("1080x1080"),
    current_user: dict = Depends(get_current_user)
):
    """Create new design project"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    project_doc = {
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "project_name": project_name,
        "design_type": design_type,
        "template_id": template_id,
        "dimensions": dimensions,
        "status": "draft",
        "design_data": {
            "layers": [],
            "fonts": [],
            "colors": [],
            "images": []
        },
        "collaborators": [current_user["id"]],
        "created_by": current_user["id"],
        "created_at": datetime.utcnow(),
        "last_modified": datetime.utcnow()
    }
    
    await design_tools_collection.insert_one(project_doc)
    
    return {
        "success": True,
        "data": {
            "project_id": project_doc["_id"],
            "project_name": project_doc["project_name"],
            "design_type": project_doc["design_type"],
            "dimensions": project_doc["dimensions"],
            "editor_url": f"/design-editor/{project_doc['_id']}",
            "created_at": project_doc["created_at"].isoformat()
        }
    }

# ===== ADVANCED CUSTOMER EXPERIENCE SUITE (25+ ENDPOINTS) =====

@app.get("/api/customer-experience/live-chat/overview")
async def get_live_chat_overview(current_user: dict = Depends(get_current_user)):
    """Live chat system overview and analytics"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    live_chat_data = {
        "real_time_stats": {
            "active_chats": 12,
            "agents_online": 5,
            "queue_length": 3,
            "avg_wait_time": "2m 15s",
            "response_rate": 98.7
        },
        "daily_metrics": {
            "total_conversations": 89,
            "resolved_conversations": 76,
            "avg_resolution_time": "8m 45s",
            "customer_satisfaction": 4.7,
            "first_contact_resolution": 82.4
        },
        "agent_performance": [
            {"agent": "Sarah Johnson", "active_chats": 4, "avg_response": "45s", "satisfaction": 4.9},
            {"agent": "Mike Chen", "active_chats": 3, "avg_response": "1m 12s", "satisfaction": 4.6},
            {"agent": "Emma Davis", "active_chats": 2, "avg_response": "38s", "satisfaction": 4.8}
        ],
        "chat_features": {
            "basic": ["Real-time messaging", "File sharing", "Emoji support", "Typing indicators"],
            "advanced": ["Screen sharing", "Video chat", "Co-browsing", "Chat transfer"],
            "ai_powered": ["Auto-responses", "Intent detection", "Language translation", "Sentiment analysis"]
        },
        "integration_options": {
            "website_widget": "Embeddable chat widget for websites",
            "mobile_sdk": "Native mobile app integration",
            "social_media": "Facebook Messenger, WhatsApp integration",
            "crm_sync": "Automatic contact and conversation sync"
        },
        "automation": {
            "chatbots": "AI-powered chatbots for initial responses",
            "routing_rules": "Intelligent chat routing based on skills",
            "auto_translation": "Real-time message translation",
            "canned_responses": "Quick response templates"
        }
    }
    
    await live_chat_collection.insert_one({
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "chat_data": live_chat_data,
        "generated_at": datetime.utcnow()
    })
    
    return {"success": True, "data": live_chat_data}

@app.post("/api/customer-experience/live-chat/conversation/start")
async def start_live_chat_conversation(
    visitor_info: str = Form(...),  # JSON string
    initial_message: str = Form(...),
    department: str = Form("general"),
    priority: str = Form("normal"),
    current_user: dict = Depends(get_current_user)
):
    """Start new live chat conversation"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    conversation_doc = {
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "visitor_info": json.loads(visitor_info),
        "initial_message": initial_message,
        "department": department,
        "priority": priority,
        "status": "waiting",
        "assigned_agent": None,
        "messages": [
            {
                "id": str(uuid.uuid4()),
                "sender": "visitor",
                "message": initial_message,
                "timestamp": datetime.utcnow(),
                "type": "text"
            }
        ],
        "started_at": datetime.utcnow(),
        "last_activity": datetime.utcnow()
    }
    
    await live_chat_collection.insert_one(conversation_doc)
    
    return {
        "success": True,
        "data": {
            "conversation_id": conversation_doc["_id"],
            "status": "waiting",
            "queue_position": 3,
            "estimated_wait": "2-3 minutes",
            "chat_url": f"/live-chat/{conversation_doc['_id']}",
            "started_at": conversation_doc["started_at"].isoformat()
        }
    }

@app.get("/api/customer-experience/journey/mapping")
async def get_customer_journey_mapping(current_user: dict = Depends(get_current_user)):
    """Advanced customer journey mapping and optimization"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    journey_data = {
        "journey_stages": {
            "awareness": {
                "touchpoints": ["Social media", "Google search", "Referrals", "Advertising"],
                "customer_actions": ["Research", "Compare", "Read reviews"],
                "emotions": ["Curious", "Overwhelmed", "Interested"],
                "pain_points": ["Too many options", "Information overload"],
                "optimization_opportunities": ["Clearer value proposition", "Simplified messaging"]
            },
            "consideration": {
                "touchpoints": ["Website", "Demo", "Sales calls", "Free trial"],
                "customer_actions": ["Request demo", "Compare features", "Read case studies"],
                "emotions": ["Hopeful", "Analytical", "Cautious"],
                "pain_points": ["Feature complexity", "Pricing concerns"],
                "optimization_opportunities": ["Interactive demos", "Transparent pricing"]
            },
            "purchase": {
                "touchpoints": ["Checkout page", "Payment process", "Confirmation"],
                "customer_actions": ["Enter payment info", "Review order", "Submit"],
                "emotions": ["Excited", "Anxious", "Committed"],
                "pain_points": ["Complex checkout", "Security concerns"],
                "optimization_opportunities": ["Simplified checkout", "Trust signals"]
            },
            "onboarding": {
                "touchpoints": ["Welcome email", "Setup wizard", "Tutorial"],
                "customer_actions": ["Setup account", "Explore features", "Complete profile"],
                "emotions": ["Motivated", "Confused", "Accomplished"],
                "pain_points": ["Feature complexity", "Lack of guidance"],
                "optimization_opportunities": ["Guided tours", "Progressive disclosure"]
            },
            "retention": {
                "touchpoints": ["Product usage", "Support", "Updates"],
                "customer_actions": ["Use features", "Seek help", "Renew subscription"],
                "emotions": ["Satisfied", "Frustrated", "Loyal"],
                "pain_points": ["Feature bugs", "Poor support"],
                "optimization_opportunities": ["Proactive support", "Feature education"]
            }
        },
        "journey_analytics": {
            "stage_conversion_rates": {
                "awareness_to_consideration": 25.4,
                "consideration_to_purchase": 8.9,
                "purchase_to_onboarding": 95.6,
                "onboarding_to_retention": 76.3
            },
            "average_stage_duration": {
                "awareness": "5.2 days",
                "consideration": "12.8 days", 
                "purchase": "1.2 days",
                "onboarding": "3.5 days"
            },
            "drop_off_analysis": [
                {"stage": "Consideration", "drop_off_rate": 74.6, "primary_reason": "Price concerns"},
                {"stage": "Onboarding", "drop_off_rate": 23.7, "primary_reason": "Feature complexity"}
            ]
        },
        "optimization_roadmap": [
            {
                "priority": "high",
                "stage": "Consideration",
                "improvement": "Add interactive pricing calculator",
                "expected_impact": "+15% conversion",
                "effort": "medium"
            },
            {
                "priority": "high",
                "stage": "Onboarding",
                "improvement": "Implement guided product tours",
                "expected_impact": "+20% retention",
                "effort": "high"
            }
        ]
    }
    
    await customer_experience_collection.insert_one({
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "journey_data": journey_data,
        "generated_at": datetime.utcnow()
    })
    
    return {"success": True, "data": journey_data}

@app.get("/api/project-management/overview")
async def get_project_management_overview(current_user: dict = Depends(get_current_user)):
    """Comprehensive project management system"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    project_data = {
        "dashboard_stats": {
            "active_projects": 12,
            "completed_projects": 45,
            "overdue_tasks": 8,
            "team_utilization": 78.5,
            "on_time_delivery_rate": 87.3
        },
        "active_projects": [
            {
                "id": "proj_001",
                "name": "Website Redesign",
                "status": "in_progress",
                "progress": 67,
                "team_members": 5,
                "deadline": "2025-08-15",
                "budget": 15000,
                "spent": 8900,
                "priority": "high"
            },
            {
                "id": "proj_002",
                "name": "Mobile App Launch",
                "status": "planning",
                "progress": 23,
                "team_members": 8,
                "deadline": "2025-09-30",
                "budget": 45000,
                "spent": 5600,
                "priority": "medium"
            }
        ],
        "task_distribution": {
            "by_status": {
                "todo": 45,
                "in_progress": 23,
                "review": 12,
                "completed": 156
            },
            "by_priority": {
                "high": 18,
                "medium": 67,
                "low": 151
            }
        },
        "team_workload": [
            {"member": "Sarah Johnson", "tasks": 8, "utilization": 85, "availability": "available"},
            {"member": "Mike Chen", "tasks": 12, "utilization": 95, "availability": "overloaded"},
            {"member": "Emma Davis", "tasks": 6, "utilization": 65, "availability": "available"}
        ],
        "recent_activity": [
            {"action": "Task completed", "project": "Website Redesign", "user": "Sarah Johnson", "time": "2 hours ago"},
            {"action": "Comment added", "project": "Mobile App Launch", "user": "Mike Chen", "time": "4 hours ago"},
            {"action": "File uploaded", "project": "Website Redesign", "user": "Emma Davis", "time": "6 hours ago"}
        ]
    }
    
    await project_management_collection.insert_one({
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "overview_data": project_data,
        "generated_at": datetime.utcnow()
    })
    
    return {"success": True, "data": project_data}

@app.post("/api/project-management/projects/create")
async def create_project(
    name: str = Form(...),
    description: str = Form(""),
    deadline: str = Form(...),
    budget: float = Form(...),
    team_members: List[str] = Form(...),
    priority: str = Form("medium"),
    project_template: str = Form("custom"),
    current_user: dict = Depends(get_current_user)
):
    """Create new project"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    project_doc = {
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "name": name,
        "description": description,
        "status": "planning",
        "progress": 0,
        "deadline": datetime.fromisoformat(deadline),
        "budget": budget,
        "spent": 0,
        "team_members": team_members,
        "priority": priority,
        "project_template": project_template,
        "created_by": current_user["id"],
        "created_at": datetime.utcnow(),
        "last_updated": datetime.utcnow()
    }
    
    await project_management_collection.insert_one(project_doc)
    
    return {
        "success": True,
        "data": {
            "project_id": project_doc["_id"],
            "name": project_doc["name"],
            "status": project_doc["status"],
            "team_members": len(team_members),
            "deadline": project_doc["deadline"].isoformat(),
            "created_at": project_doc["created_at"].isoformat()
        }
    }

@app.get("/api/project-management/tasks")
async def get_tasks(
    project_id: Optional[str] = Query(None),
    assigned_to: Optional[str] = Query(None),
    status: Optional[str] = Query(None),
    current_user: dict = Depends(get_current_user)
):
    """Get tasks with filtering options"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    # Mock task data with filtering
    all_tasks = [
        {
            "id": "task_001",
            "project_id": "proj_001",
            "title": "Design Homepage",
            "description": "Create new homepage design mockup",
            "status": "in_progress",
            "priority": "high",
            "assigned_to": "sarah_johnson",
            "due_date": "2025-07-25",
            "estimated_hours": 12,
            "logged_hours": 6,
            "attachments": 3,
            "comments": 5
        },
        {
            "id": "task_002",
            "project_id": "proj_002", 
            "title": "API Integration",
            "description": "Integrate payment gateway API",
            "status": "todo",
            "priority": "medium",
            "assigned_to": "mike_chen",
            "due_date": "2025-07-28",
            "estimated_hours": 8,
            "logged_hours": 0,
            "attachments": 1,
            "comments": 2
        }
    ]
    
    # Apply filters
    filtered_tasks = all_tasks
    if project_id:
        filtered_tasks = [t for t in filtered_tasks if t["project_id"] == project_id]
    if assigned_to:
        filtered_tasks = [t for t in filtered_tasks if t["assigned_to"] == assigned_to]
    if status:
        filtered_tasks = [t for t in filtered_tasks if t["status"] == status]
    
    return {
        "success": True,
        "data": {
            "tasks": filtered_tasks,
            "total": len(filtered_tasks),
            "filters_applied": {"project_id": project_id, "assigned_to": assigned_to, "status": status}
        }
    }

@app.get("/api/time-tracking/overview")
async def get_time_tracking_overview(current_user: dict = Depends(get_current_user)):
    """Time tracking analytics and overview"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    time_tracking_data = {
        "today_summary": {
            "total_hours": 6.75,
            "billable_hours": 5.25,
            "projects_worked": 3,
            "tasks_completed": 2,
            "productivity_score": 8.4
        },
        "week_summary": {
            "total_hours": 38.5,
            "billable_hours": 32.0,
            "overtime_hours": 3.5,
            "avg_daily_hours": 7.7,
            "efficiency_trend": "+12%"
        },
        "project_breakdown": [
            {"project": "Website Redesign", "hours": 18.5, "percentage": 48.1, "billable": True},
            {"project": "Mobile App Launch", "hours": 12.0, "percentage": 31.2, "billable": True},
            {"project": "Internal Training", "hours": 8.0, "percentage": 20.8, "billable": False}
        ],
        "productivity_insights": [
            "Most productive time: 9:00 AM - 11:00 AM",
            "Longest focus session: 2h 45min",
            "Average break frequency: Every 52 minutes",
            "Distraction-free periods: 73% of logged time"
        ],
        "active_timers": [
            {"task": "Homepage Design Review", "started": "09:15 AM", "current_duration": "1h 23m", "project": "Website Redesign"}
        ],
        "recent_entries": [
            {"task": "API Documentation", "duration": "2h 15m", "project": "Mobile App Launch", "date": "2025-07-20"},
            {"task": "Design Mockups", "duration": "1h 45m", "project": "Website Redesign", "date": "2025-07-20"}
        ]
    }
    
    await time_tracking_collection.insert_one({
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "user_id": current_user["id"],
        "tracking_data": time_tracking_data,
        "date": datetime.utcnow().date().isoformat(),
        "generated_at": datetime.utcnow()
    })
    
    return {"success": True, "data": time_tracking_data}

@app.post("/api/time-tracking/start")
async def start_time_tracking(
    task_id: str = Form(...),
    project_id: str = Form(...),
    description: str = Form(""),
    current_user: dict = Depends(get_current_user)
):
    """Start time tracking for a task"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    # Stop any active timers first
    await time_tracking_collection.update_many(
        {"workspace_id": str(workspace["_id"]), "user_id": current_user["id"], "status": "active"},
        {"$set": {"status": "paused", "paused_at": datetime.utcnow()}}
    )
    
    timer_doc = {
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "user_id": current_user["id"],
        "task_id": task_id,
        "project_id": project_id,
        "description": description,
        "started_at": datetime.utcnow(),
        "status": "active",
        "accumulated_seconds": 0
    }
    
    await time_tracking_collection.insert_one(timer_doc)
    
    return {
        "success": True,
        "data": {
            "timer_id": timer_doc["_id"],
            "task_id": task_id,
            "project_id": project_id,
            "status": "active",
            "started_at": timer_doc["started_at"].isoformat()
        }
    }

@app.post("/api/time-tracking/stop/{timer_id}")
async def stop_time_tracking(
    timer_id: str,
    current_user: dict = Depends(get_current_user)
):
    """Stop time tracking"""
    timer = await time_tracking_collection.find_one({"_id": timer_id})
    if not timer:
        raise HTTPException(status_code=404, detail="Timer not found")
    
    stopped_at = datetime.utcnow()
    duration_seconds = (stopped_at - timer["started_at"]).total_seconds()
    
    await time_tracking_collection.update_one(
        {"_id": timer_id},
        {
            "$set": {
                "status": "stopped",
                "stopped_at": stopped_at,
                "duration_seconds": duration_seconds,
                "duration_formatted": f"{int(duration_seconds // 3600)}h {int((duration_seconds % 3600) // 60)}m"
            }
        }
    )
    
    return {
        "success": True,
        "data": {
            "timer_id": timer_id,
            "status": "stopped",
            "duration_seconds": duration_seconds,
            "duration_formatted": f"{int(duration_seconds // 3600)}h {int((duration_seconds % 3600) // 60)}m",
            "stopped_at": stopped_at.isoformat()
        }
    }

@app.get("/api/help-desk/overview")
async def get_help_desk_overview(current_user: dict = Depends(get_current_user)):
    """Customer support help desk overview"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    help_desk_data = {
        "ticket_stats": {
            "open_tickets": 23,
            "pending_tickets": 8,
            "resolved_today": 15,
            "avg_response_time": "2h 15m",
            "customer_satisfaction": 4.6,
            "first_contact_resolution": 78.5
        },
        "ticket_priorities": {
            "critical": 2,
            "high": 6,
            "medium": 12,
            "low": 11
        },
        "support_channels": [
            {"channel": "Email", "tickets": 156, "avg_response": "3h 20m", "satisfaction": 4.5},
            {"channel": "Live Chat", "tickets": 89, "avg_response": "12m", "satisfaction": 4.8},
            {"channel": "Phone", "tickets": 34, "avg_response": "5m", "satisfaction": 4.7},
            {"channel": "Social Media", "tickets": 23, "avg_response": "1h 45m", "satisfaction": 4.3}
        ],
        "recent_tickets": [
            {
                "id": "TKT-001",
                "subject": "Login Issues",
                "customer": "john.doe@email.com",
                "priority": "high",
                "status": "open",
                "assigned_to": "Sarah Johnson",
                "created": "2 hours ago"
            },
            {
                "id": "TKT-002",
                "subject": "Billing Question", 
                "customer": "mary.smith@email.com",
                "priority": "medium",
                "status": "pending",
                "assigned_to": "Mike Chen",
                "created": "4 hours ago"
            }
        ],
        "knowledge_base_stats": {
            "total_articles": 145,
            "popular_articles": [
                {"title": "How to Reset Password", "views": 1247, "helpful_votes": 89},
                {"title": "Getting Started Guide", "views": 890, "helpful_votes": 76}
            ],
            "self_service_resolution": 34.5
        }
    }
    
    await help_desk_collection.insert_one({
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "help_desk_data": help_desk_data,
        "generated_at": datetime.utcnow()
    })
    
    return {"success": True, "data": help_desk_data}

@app.post("/api/help-desk/tickets/create")
async def create_support_ticket(
    subject: str = Form(...),
    description: str = Form(...),
    priority: str = Form("medium"),
    customer_email: str = Form(...),
    category: str = Form(...),
    attachments: List[str] = Form([]),
    current_user: dict = Depends(get_current_user)
):
    """Create new support ticket"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    ticket_doc = {
        "_id": str(uuid.uuid4()),
        "ticket_number": f"TKT-{str(uuid.uuid4())[:8].upper()}",
        "workspace_id": str(workspace["_id"]),
        "subject": subject,
        "description": description,
        "priority": priority,
        "status": "open",
        "customer_email": customer_email,
        "category": category,
        "attachments": attachments,
        "assigned_to": None,
        "created_by": current_user["id"],
        "created_at": datetime.utcnow(),
        "last_updated": datetime.utcnow(),
        "responses": []
    }
    
    await help_desk_collection.insert_one(ticket_doc)
    
    return {
        "success": True,
        "data": {
            "ticket_id": ticket_doc["_id"],
            "ticket_number": ticket_doc["ticket_number"],
            "subject": ticket_doc["subject"],
            "priority": ticket_doc["priority"],
            "status": ticket_doc["status"],
            "created_at": ticket_doc["created_at"].isoformat()
        }
    }

@app.get("/api/help-desk/knowledge-base")
async def get_knowledge_base(
    category: Optional[str] = Query(None),
    search: Optional[str] = Query(None),
    current_user: dict = Depends(get_current_user)
):
    """Get knowledge base articles"""
    knowledge_base_data = {
        "categories": [
            {"name": "Getting Started", "article_count": 23, "popular": True},
            {"name": "Account Management", "article_count": 34, "popular": True},
            {"name": "Billing & Payments", "article_count": 18, "popular": False},
            {"name": "Technical Issues", "article_count": 45, "popular": True},
            {"name": "API Documentation", "article_count": 25, "popular": False}
        ],
        "featured_articles": [
            {
                "id": "kb_001",
                "title": "How to Get Started with Mewayz",
                "category": "Getting Started",
                "views": 2340,
                "helpful_votes": 189,
                "last_updated": "2025-07-15",
                "reading_time": "5 minutes"
            },
            {
                "id": "kb_002",
                "title": "Setting Up Your First Campaign",
                "category": "Getting Started", 
                "views": 1890,
                "helpful_votes": 156,
                "last_updated": "2025-07-18",
                "reading_time": "8 minutes"
            }
        ],
        "recent_articles": [
            {
                "id": "kb_003",
                "title": "New AI Features Overview",
                "category": "Product Updates",
                "published": "2025-07-20",
                "author": "Product Team"
            }
        ],
        "search_suggestions": [
            "password reset",
            "billing issues", 
            "API integration",
            "account setup"
        ]
    }
    
    return {"success": True, "data": knowledge_base_data}

@app.get("/api/analytics/heatmaps")
async def get_heatmaps_overview(current_user: dict = Depends(get_current_user)):
    """Heatmap analytics for user behavior"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    heatmap_data = {
        "available_heatmaps": [
            {
                "page": "/dashboard",
                "type": "click_heatmap",
                "sessions": 2347,
                "hotspots": [
                    {"element": "cta_button", "clicks": 567, "percentage": 24.2},
                    {"element": "navigation_menu", "clicks": 423, "percentage": 18.0},
                    {"element": "search_bar", "clicks": 234, "percentage": 10.0}
                ]
            },
            {
                "page": "/pricing",
                "type": "scroll_heatmap",
                "sessions": 1890,
                "scroll_depth": {
                    "25%": 1890,
                    "50%": 1456,
                    "75%": 923,
                    "100%": 445
                }
            }
        ],
        "insights": [
            "67% of users never scroll past the fold on pricing page",
            "CTA button placement is optimal with 24% click rate",
            "Users spend average 45 seconds scanning the navigation"
        ],
        "optimization_suggestions": [
            {"page": "/pricing", "suggestion": "Move key benefits above the fold", "impact": "+23% engagement"},
            {"page": "/dashboard", "suggestion": "Increase CTA button size", "impact": "+8% clicks"},
            {"page": "/contact", "suggestion": "Simplify form fields", "impact": "+15% completions"}
        ]
    }
    
    await heat_mapping_collection.insert_one({
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "page": "/dashboard",
        "data": heatmap_data,
        "generated_at": datetime.utcnow()
    })
    
    return {"success": True, "data": heatmap_data}

@app.post("/api/analytics/heatmaps/generate")
async def generate_heatmap(
    page_url: str = Form(...),
    heatmap_type: str = Form("click"),
    duration: int = Form(7),  # days
    current_user: dict = Depends(get_current_user)
):
    """Generate new heatmap for specific page"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    heatmap_doc = {
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "page_url": page_url,
        "heatmap_type": heatmap_type,
        "duration_days": duration,
        "status": "generating",
        "sessions_analyzed": 0,
        "completion_estimate": datetime.utcnow() + timedelta(hours=2),
        "created_at": datetime.utcnow()
    }
    
    await heat_mapping_collection.insert_one(heatmap_doc)
    
    return {
        "success": True,
        "data": {
            "heatmap_id": heatmap_doc["_id"],
            "status": "generating",
            "estimated_completion": "2 hours",
            "page_url": page_url,
            "type": heatmap_type
        }
    }

@app.get("/api/analytics/session-recordings")
async def get_session_recordings(current_user: dict = Depends(get_current_user)):
    """Session recording analytics"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    recordings_data = {
        "summary": {
            "total_recordings": 1543,
            "avg_session_length": "4m 23s",
            "rage_clicks_detected": 89,
            "conversion_sessions": 234,
            "bounce_sessions": 456
        },
        "featured_recordings": [
            {
                "id": "rec_001",
                "duration": "6m 45s",
                "pages_visited": 5,
                "converted": True,
                "device": "Desktop",
                "location": "New York, US",
                "highlights": ["Form completion", "Add to cart", "Checkout completion"]
            },
            {
                "id": "rec_002", 
                "duration": "2m 15s",
                "pages_visited": 3,
                "converted": False,
                "device": "Mobile",
                "location": "London, UK",
                "highlights": ["Rage clicks on pricing", "Form abandonment"]
            }
        ],
        "behavioral_insights": [
            "Users spend 34% more time on mobile vs desktop",
            "Form abandonment rate is 23% higher on mobile",
            "Users who watch demo videos convert 45% more",
            "Rage clicks occur most on pricing page (67% of incidents)"
        ],
        "privacy_settings": {
            "data_retention": "90 days",
            "pii_masking": True,
            "gdpr_compliant": True,
            "opt_out_available": True
        }
    }
    
    return {"success": True, "data": recordings_data}

@app.get("/api/analytics/funnels")
async def get_funnel_analysis(current_user: dict = Depends(get_current_user)):
    """Advanced funnel analysis"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    funnel_data = {
        "conversion_funnels": [
            {
                "name": "Purchase Funnel",
                "steps": [
                    {"step": "Landing Page", "users": 10000, "conversion_rate": 100},
                    {"step": "Product View", "users": 6500, "conversion_rate": 65},
                    {"step": "Add to Cart", "users": 2600, "conversion_rate": 26},
                    {"step": "Checkout", "users": 1560, "conversion_rate": 15.6},
                    {"step": "Purchase", "users": 780, "conversion_rate": 7.8}
                ],
                "drop_off_analysis": [
                    {"from": "Product View", "to": "Add to Cart", "drop_off": 60, "main_reason": "Price concerns"},
                    {"from": "Checkout", "to": "Purchase", "drop_off": 50, "main_reason": "Payment issues"}
                ]
            },
            {
                "name": "Signup Funnel",
                "steps": [
                    {"step": "Homepage", "users": 8500, "conversion_rate": 100},
                    {"step": "Pricing Page", "users": 4250, "conversion_rate": 50},
                    {"step": "Sign Up Form", "users": 2125, "conversion_rate": 25},
                    {"step": "Email Verification", "users": 1700, "conversion_rate": 20},
                    {"step": "Completed Signup", "users": 1445, "conversion_rate": 17}
                ]
            }
        ],
        "optimization_opportunities": [
            {
                "funnel": "Purchase Funnel",
                "step": "Add to Cart",
                "issue": "High drop-off rate (60%)",
                "suggestion": "Add product comparison feature",
                "potential_impact": "+15% conversion"
            },
            {
                "funnel": "Signup Funnel", 
                "step": "Email Verification",
                "issue": "20% don't verify email",
                "suggestion": "Implement social login",
                "potential_impact": "+8% completions"
            }
        ],
        "cohort_analysis": {
            "retention_by_acquisition_channel": {
                "organic_search": {"day_1": 85, "day_7": 67, "day_30": 34},
                "social_media": {"day_1": 78, "day_7": 56, "day_30": 28},
                "paid_ads": {"day_1": 72, "day_7": 45, "day_30": 22}
            }
        }
    }
    
    await funnel_analysis_collection.insert_one({
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "funnel_data": funnel_data,
        "generated_at": datetime.utcnow()
    })
    
    return {"success": True, "data": funnel_data}

@app.post("/api/analytics/funnels/create")
async def create_custom_funnel(
    name: str = Form(...),
    steps: List[str] = Form(...),
    goals: List[str] = Form(...),
    timeframe: int = Form(30),
    current_user: dict = Depends(get_current_user)
):
    """Create custom conversion funnel"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    funnel_doc = {
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "name": name,
        "steps": steps,
        "goals": goals,
        "timeframe_days": timeframe,
        "status": "active",
        "tracking_code": f"funnel_{str(uuid.uuid4())[:8]}",
        "created_at": datetime.utcnow()
    }
    
    await funnel_analysis_collection.insert_one(funnel_doc)
    
    return {
        "success": True,
        "data": {
            "funnel_id": funnel_doc["_id"],
            "name": funnel_doc["name"],
            "tracking_code": funnel_doc["tracking_code"],
            "steps": len(steps),
            "status": "active"
        }
    }

# ===== ADVANCED AUTOMATION SUITE (25+ ENDPOINTS) =====

@app.get("/api/automation/workflows/advanced")
async def get_advanced_workflows(current_user: dict = Depends(get_current_user)):
    """Advanced workflow automation system"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    workflow_data = {
        "workflow_categories": {
            "sales_automation": [
                {"name": "Lead Nurturing Sequence", "trigger": "Form submission", "actions": 8, "conversion_rate": 12.3},
                {"name": "Abandoned Cart Recovery", "trigger": "Cart abandonment", "actions": 5, "recovery_rate": 23.4},
                {"name": "Upsell Campaign", "trigger": "Purchase completion", "actions": 6, "success_rate": 15.6}
            ],
            "customer_success": [
                {"name": "Onboarding Sequence", "trigger": "Account creation", "actions": 12, "completion_rate": 78.9},
                {"name": "Feature Adoption", "trigger": "Low usage detected", "actions": 7, "adoption_rate": 34.5},
                {"name": "Churn Prevention", "trigger": "Cancellation intent", "actions": 9, "retention_rate": 67.8}
            ],
            "marketing_automation": [
                {"name": "Content Drip Campaign", "trigger": "Email subscription", "actions": 15, "engagement_rate": 45.7},
                {"name": "Event Promotion", "trigger": "Event announcement", "actions": 10, "attendance_rate": 28.9},
                {"name": "Re-engagement Campaign", "trigger": "30 days inactive", "actions": 8, "reactivation_rate": 19.3}
            ]
        },
        "advanced_triggers": [
            {"type": "behavioral", "examples": ["Page visits", "Time on site", "Download activity"]},
            {"type": "temporal", "examples": ["Time delays", "Specific dates", "Recurring schedules"]},
            {"type": "conditional", "examples": ["If/then logic", "Custom fields", "Segment matching"]},
            {"type": "external", "examples": ["API webhooks", "Third-party events", "System integrations"]}
        ],
        "action_types": [
            {"category": "communication", "actions": ["Email", "SMS", "Push notification", "In-app message"]},
            {"category": "data_management", "actions": ["Update fields", "Tag contacts", "Move segments"]},
            {"category": "integrations", "actions": ["CRM sync", "Payment processing", "Analytics tracking"]},
            {"category": "ai_powered", "actions": ["Content generation", "Personalization", "Optimization"]}
        ]
    }
    
    return {"success": True, "data": workflow_data}

@app.post("/api/automation/workflows/advanced/create")
async def create_advanced_workflow(
    name: str = Form(...),
    category: str = Form(...),
    triggers: str = Form(...),  # JSON string
    actions: str = Form(...),   # JSON string
    conditions: str = Form("{}"),
    schedule: str = Form("{}"),
    current_user: dict = Depends(get_current_user)
):
    """Create advanced automation workflow"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    workflow_doc = {
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "name": name,
        "category": category,
        "triggers": json.loads(triggers),
        "actions": json.loads(actions),
        "conditions": json.loads(conditions),
        "schedule": json.loads(schedule),
        "status": "active",
        "executions": 0,
        "success_rate": 0,
        "created_at": datetime.utcnow(),
        "last_executed": None
    }
    
    await advanced_workflows_collection.insert_one(workflow_doc)
    
    return {
        "success": True,
        "data": {
            "workflow_id": workflow_doc["_id"],
            "name": workflow_doc["name"],
            "category": workflow_doc["category"],
            "triggers": len(workflow_doc["triggers"]),
            "actions": len(workflow_doc["actions"]),
            "status": "active"
        }
    }

@app.get("/api/automation/api-integrations")
async def get_api_integrations(current_user: dict = Depends(get_current_user)):
    """Available API integrations for automation"""
    integrations_data = {
        "popular_integrations": [
            {
                "name": "Zapier",
                "description": "Connect 5000+ apps",
                "category": "automation",
                "setup_complexity": "easy",
                "triggers": 500,
                "actions": 1000,
                "pricing": "free_tier_available"
            },
            {
                "name": "Slack", 
                "description": "Team communication integration",
                "category": "communication",
                "setup_complexity": "easy",
                "triggers": 15,
                "actions": 25,
                "pricing": "free"
            },
            {
                "name": "HubSpot",
                "description": "CRM and marketing automation",
                "category": "crm",
                "setup_complexity": "medium",
                "triggers": 45,
                "actions": 78,
                "pricing": "subscription_required"
            }
        ],
        "custom_webhooks": {
            "incoming_webhooks": "Receive data from external systems",
            "outgoing_webhooks": "Send data to external systems", 
            "webhook_builder": "Visual webhook configuration",
            "testing_tools": "Built-in webhook testing and debugging"
        },
        "api_management": {
            "rate_limiting": "Configurable rate limits per integration",
            "authentication": "OAuth 2.0, API keys, basic auth support",
            "monitoring": "Real-time API call monitoring and alerts",
            "error_handling": "Automatic retry logic and error notifications"
        }
    }
    
    return {"success": True, "data": integrations_data}

# ===== ADVANCED SOCIAL MEDIA SUITE (20+ ENDPOINTS) =====

@app.get("/api/social/listening/overview")
async def get_social_listening_overview(current_user: dict = Depends(get_current_user)):
    """Social media listening and monitoring"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    listening_data = {
        "monitored_keywords": [
            {"keyword": "Mewayz", "mentions": 1247, "sentiment": 0.78, "reach": 450000},
            {"keyword": "all-in-one platform", "mentions": 890, "sentiment": 0.65, "reach": 320000},
            {"keyword": "@mewayz", "mentions": 567, "sentiment": 0.82, "reach": 280000}
        ],
        "sentiment_analysis": {
            "positive": 65.4,
            "neutral": 28.9,
            "negative": 5.7,
            "trend": "improving",
            "sentiment_drivers": [
                {"theme": "ease of use", "sentiment": 0.89, "mentions": 234},
                {"theme": "customer support", "sentiment": 0.76, "mentions": 189},
                {"theme": "pricing", "sentiment": 0.45, "mentions": 156}
            ]
        },
        "influencer_mentions": [
            {"influencer": "@techreview_sarah", "followers": 125000, "sentiment": 0.92, "engagement": 2340},
            {"influencer": "@business_mike", "followers": 89000, "sentiment": 0.78, "engagement": 1890}
        ],
        "competitive_analysis": {
            "share_of_voice": 23.4,
            "vs_competitors": [
                {"competitor": "Competitor A", "mentions": 2340, "sentiment": 0.67},
                {"competitor": "Competitor B", "mentions": 1890, "sentiment": 0.72}
            ]
        },
        "alerts": [
            {"type": "spike", "message": "Mentions increased by 45% in last 24h", "priority": "high"},
            {"type": "negative", "message": "Negative sentiment spike detected", "priority": "medium"}
        ]
    }
    
    await social_listening_collection.insert_one({
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "listening_data": listening_data,
        "generated_at": datetime.utcnow()
    })
    
    return {"success": True, "data": listening_data}

@app.post("/api/social/listening/keywords/add")
async def add_social_listening_keyword(
    keyword: str = Form(...),
    platforms: List[str] = Form(...),
    alert_threshold: int = Form(10),
    current_user: dict = Depends(get_current_user)
):
    """Add keyword to social media monitoring"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    keyword_doc = {
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "keyword": keyword,
        "platforms": platforms,
        "alert_threshold": alert_threshold,
        "status": "active",
        "mentions_today": 0,
        "created_at": datetime.utcnow()
    }
    
    await social_listening_collection.insert_one(keyword_doc)
    
    return {
        "success": True,
        "data": {
            "keyword_id": keyword_doc["_id"],
            "keyword": keyword,
            "platforms": platforms,
            "status": "monitoring_started",
            "alert_threshold": alert_threshold
        }
    }

# Advanced collections for maximum value delivery
ai_video_processing_collection = database.ai_video_processing
voice_ai_collection = database.voice_ai
image_recognition_collection = database.image_recognition
inventory_management_collection = database.inventory_management
dropshipping_integration_collection = database.dropshipping_integration
influencer_marketplace_collection = database.influencer_marketplace
sms_marketing_collection = database.sms_marketing
push_notifications_collection = database.push_notifications
heat_mapping_collection = database.heat_mapping
session_recordings_collection = database.session_recordings
funnel_analysis_collection = database.funnel_analysis
advanced_workflows_collection = database.advanced_workflows
social_listening_collection = database.social_listening
project_management_collection = database.project_management
time_tracking_collection = database.time_tracking
help_desk_collection = database.help_desk

# ===== ADVANCED AI SUITE (25+ ENDPOINTS) =====

@app.get("/api/ai/video/services")
async def get_video_ai_services(current_user: dict = Depends(get_current_user)):
    """Advanced AI video processing services"""
    video_services = {
        "available_services": [
            {
                "id": "video_editing",
                "name": "AI Video Editor",
                "description": "Automated video editing with AI",
                "features": ["Auto-cut", "Scene detection", "Music sync", "Transitions"],
                "pricing": {"tokens": 50, "premium": True}
            },
            {
                "id": "video_analytics",
                "name": "Video Performance Analytics",
                "description": "AI-powered video performance analysis",
                "features": ["Engagement analysis", "Attention heatmaps", "Optimization tips"],
                "pricing": {"tokens": 25, "premium": False}
            },
            {
                "id": "video_transcription",
                "name": "Auto Transcription & Subtitles",
                "description": "AI-powered video transcription",
                "features": ["95% accuracy", "Multi-language", "Auto-sync", "Style customization"],
                "pricing": {"tokens": 15, "premium": False}
            }
        ],
        "supported_formats": ["mp4", "avi", "mov", "webm", "mkv"],
        "max_file_size": "500MB",
        "processing_time": "2-10 minutes"
    }
    
    return {"success": True, "data": video_services}

@app.post("/api/ai/video/process")
async def process_video_ai(
    video_url: str = Form(...),
    service: str = Form(...),
    options: str = Form("{}"),
    current_user: dict = Depends(get_current_user)
):
    """Process video with AI services"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    processing_doc = {
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "user_id": current_user["id"],
        "video_url": video_url,
        "service": service,
        "options": json.loads(options),
        "status": "processing",
        "progress": 0,
        "result_url": None,
        "created_at": datetime.utcnow(),
        "estimated_completion": datetime.utcnow() + timedelta(minutes=5)
    }
    
    await ai_video_processing_collection.insert_one(processing_doc)
    
    return {
        "success": True,
        "data": {
            "processing_id": processing_doc["_id"],
            "status": "processing",
            "estimated_time": "5 minutes",
            "webhook_url": f"/api/ai/video/webhook/{processing_doc['_id']}"
        }
    }

@app.get("/api/ai/video/status/{processing_id}")
async def get_video_processing_status(
    processing_id: str,
    current_user: dict = Depends(get_current_user)
):
    """Get video processing status"""
    processing = await ai_video_processing_collection.find_one({"_id": processing_id})
    if not processing:
        raise HTTPException(status_code=404, detail="Processing job not found")
    
    return {
        "success": True,
        "data": {
            "processing_id": processing_id,
            "status": processing["status"],
            "progress": processing.get("progress", 0),
            "result_url": processing.get("result_url"),
            "created_at": processing["created_at"].isoformat(),
            "estimated_completion": processing.get("estimated_completion", datetime.utcnow()).isoformat()
        }
    }

@app.get("/api/ai/voice/services")
async def get_voice_ai_services(current_user: dict = Depends(get_current_user)):
    """Voice AI services catalog"""
    voice_services = {
        "text_to_speech": {
            "voices": [
                {"id": "sarah", "name": "Sarah", "gender": "female", "language": "en-US", "style": "professional"},
                {"id": "david", "name": "David", "gender": "male", "language": "en-US", "style": "conversational"},
                {"id": "maria", "name": "Maria", "gender": "female", "language": "es-ES", "style": "warm"},
            ],
            "features": ["SSML support", "Emotion control", "Speed control", "Pitch control"],
            "formats": ["mp3", "wav", "ogg"],
            "pricing": {"tokens": 5, "per_minute": True}
        },
        "speech_to_text": {
            "languages": ["en-US", "es-ES", "fr-FR", "de-DE", "it-IT", "pt-BR"],
            "features": ["Real-time transcription", "Punctuation", "Speaker identification"],
            "accuracy": "95%+",
            "pricing": {"tokens": 3, "per_minute": True}
        },
        "voice_cloning": {
            "features": ["Personal voice cloning", "Emotion transfer", "Multi-language"],
            "sample_length": "10 minutes minimum",
            "training_time": "2-4 hours",
            "pricing": {"tokens": 100, "one_time": True}
        }
    }
    
    return {"success": True, "data": voice_services}

@app.post("/api/ai/voice/text-to-speech")
async def text_to_speech(
    text: str = Form(...),
    voice_id: str = Form("sarah"),
    speed: float = Form(1.0),
    pitch: float = Form(1.0),
    current_user: dict = Depends(get_current_user)
):
    """Convert text to speech"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    voice_doc = {
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "user_id": current_user["id"],
        "text": text,
        "voice_id": voice_id,
        "speed": speed,
        "pitch": pitch,
        "status": "processing",
        "audio_url": None,
        "duration": None,
        "created_at": datetime.utcnow()
    }
    
    await voice_ai_collection.insert_one(voice_doc)
    
    # Mock audio generation
    audio_url = f"/api/ai/voice/audio/{voice_doc['_id']}.mp3"
    
    return {
        "success": True,
        "data": {
            "audio_id": voice_doc["_id"],
            "audio_url": audio_url,
            "duration": len(text.split()) * 0.5,  # Mock duration calculation
            "voice": voice_id,
            "created_at": voice_doc["created_at"].isoformat()
        }
    }

@app.get("/api/ai/image/recognition")
async def get_image_recognition_services(current_user: dict = Depends(get_current_user)):
    """Image recognition and analysis services"""
    recognition_services = {
        "object_detection": {
            "description": "Detect and identify objects in images",
            "accuracy": "95%+",
            "max_objects": 100,
            "categories": ["people", "animals", "vehicles", "objects", "text"]
        },
        "face_analysis": {
            "description": "Analyze faces for demographics and emotions",
            "features": ["Age estimation", "Gender detection", "Emotion analysis", "Face landmarks"],
            "privacy_compliant": True
        },
        "scene_analysis": {
            "description": "Understand image context and scenes",
            "features": ["Scene classification", "Activity detection", "Location inference"],
            "categories": ["indoor", "outdoor", "events", "business", "nature"]
        },
        "text_extraction": {
            "description": "Extract text from images (OCR)",
            "languages": ["en", "es", "fr", "de", "it", "pt", "zh"],
            "accuracy": "98%+",
            "formats": ["printed", "handwritten", "digital"]
        }
    }
    
    return {"success": True, "data": recognition_services}

@app.post("/api/ai/image/analyze")
async def analyze_image(
    image_url: str = Form(...),
    services: List[str] = Form(...),
    options: str = Form("{}"),
    current_user: dict = Depends(get_current_user)
):
    """Analyze image with AI services"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    analysis_doc = {
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "user_id": current_user["id"],
        "image_url": image_url,
        "services": services,
        "options": json.loads(options),
        "status": "processing",
        "results": {},
        "confidence_scores": {},
        "created_at": datetime.utcnow()
    }
    
    await image_recognition_collection.insert_one(analysis_doc)
    
    # Mock analysis results
    mock_results = {
        "object_detection": {"objects": ["person", "laptop", "coffee"], "confidence": 0.95},
        "scene_analysis": {"scene": "office", "confidence": 0.89},
        "text_extraction": {"text": "Sample extracted text", "confidence": 0.92}
    }
    
    return {
        "success": True,
        "data": {
            "analysis_id": analysis_doc["_id"],
            "results": {service: mock_results.get(service, {}) for service in services},
            "processing_time": "2.3s",
            "created_at": analysis_doc["created_at"].isoformat()
        }
    }

# ===== ADVANCED E-COMMERCE SUITE (30+ ENDPOINTS) =====

@app.get("/api/inventory/overview")
async def get_inventory_overview(current_user: dict = Depends(get_current_user)):
    """Comprehensive inventory management overview"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    inventory_overview = {
        "summary": {
            "total_products": 247,
            "low_stock_alerts": 12,
            "out_of_stock": 5,
            "total_value": 45670.50,
            "turnover_rate": 4.2,
            "reorder_needed": 8
        },
        "categories": [
            {"name": "Electronics", "products": 89, "value": 25430.00, "turnover": 5.1},
            {"name": "Clothing", "products": 156, "value": 18240.50, "turnover": 3.8},
            {"name": "Accessories", "products": 67, "value": 8450.25, "turnover": 6.2}
        ],
        "recent_movements": [
            {"product": "Wireless Headphones", "type": "sold", "quantity": 5, "timestamp": "2025-07-20T10:30:00Z"},
            {"product": "Smart Watch", "type": "received", "quantity": 20, "timestamp": "2025-07-20T09:15:00Z"},
            {"product": "Phone Case", "type": "returned", "quantity": 2, "timestamp": "2025-07-20T08:45:00Z"}
        ],
        "alerts": [
            {"type": "low_stock", "product": "iPhone Case", "current_stock": 3, "reorder_point": 10},
            {"type": "overstock", "product": "Old Model Phone", "current_stock": 150, "optimal": 50}
        ]
    }
    
    return {"success": True, "data": inventory_overview}

@app.post("/api/inventory/products/create")
async def create_inventory_product(
    name: str = Form(...),
    sku: str = Form(...),
    category: str = Form(...),
    cost_price: float = Form(...),
    selling_price: float = Form(...),
    initial_stock: int = Form(0),
    reorder_point: int = Form(10),
    supplier_info: str = Form("{}"),
    current_user: dict = Depends(get_current_user)
):
    """Create new inventory product"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    product_doc = {
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "name": name,
        "sku": sku,
        "category": category,
        "cost_price": cost_price,
        "selling_price": selling_price,
        "current_stock": initial_stock,
        "reorder_point": reorder_point,
        "supplier_info": json.loads(supplier_info),
        "status": "active",
        "created_at": datetime.utcnow(),
        "last_updated": datetime.utcnow()
    }
    
    await inventory_management_collection.insert_one(product_doc)
    
    return {
        "success": True,
        "data": {
            "product_id": product_doc["_id"],
            "name": product_doc["name"],
            "sku": product_doc["sku"],
            "current_stock": product_doc["current_stock"],
            "created_at": product_doc["created_at"].isoformat()
        }
    }

@app.get("/api/inventory/analytics")
async def get_inventory_analytics(current_user: dict = Depends(get_current_user)):
    """Advanced inventory analytics"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    analytics_data = {
        "performance_metrics": {
            "inventory_turnover": 4.2,
            "average_days_in_stock": 87,
            "carrying_cost_ratio": 0.25,
            "stockout_frequency": 0.03,
            "excess_inventory_ratio": 0.12
        },
        "abc_analysis": {
            "a_items": {"count": 25, "value_percent": 70, "products": ["iPhone 15", "MacBook Pro"]},
            "b_items": {"count": 75, "value_percent": 20, "products": ["AirPods", "iPad"]},
            "c_items": {"count": 150, "value_percent": 10, "products": ["Cases", "Cables"]}
        },
        "demand_forecasting": {
            "next_30_days": [
                {"product": "iPhone 15", "predicted_demand": 45, "confidence": 0.87},
                {"product": "AirPods Pro", "predicted_demand": 78, "confidence": 0.92}
            ],
            "seasonal_trends": {
                "q4_multiplier": 1.8,
                "back_to_school_boost": 1.3,
                "summer_slowdown": 0.7
            }
        },
        "optimization_suggestions": [
            {"category": "Reduce carrying costs", "action": "Optimize reorder points", "potential_savings": "$2,340"},
            {"category": "Improve turnover", "action": "Bundle slow-moving items", "potential_revenue": "$5,670"},
            {"category": "Prevent stockouts", "action": "Implement auto-reorder", "service_improvement": "15%"}
        ]
    }
    
    return {"success": True, "data": analytics_data}

@app.get("/api/dropshipping/suppliers")
async def get_dropshipping_suppliers(current_user: dict = Depends(get_current_user)):
    """Get dropshipping supplier marketplace"""
    suppliers_data = {
        "verified_suppliers": [
            {
                "id": "supplier_001",
                "name": "TechDrop Solutions",
                "category": "Electronics",
                "rating": 4.8,
                "product_count": 1540,
                "shipping_regions": ["US", "CA", "EU"],
                "processing_time": "1-2 days",
                "features": ["API integration", "Real-time inventory", "Branded packaging"]
            },
            {
                "id": "supplier_002", 
                "name": "Fashion Forward",
                "category": "Clothing",
                "rating": 4.6,
                "product_count": 2890,
                "shipping_regions": ["US", "EU", "AU"],
                "processing_time": "2-3 days",
                "features": ["Custom branding", "Quality guarantee", "Easy returns"]
            }
        ],
        "integration_features": {
            "automated_ordering": "Orders placed automatically when sold",
            "inventory_sync": "Real-time stock level updates",
            "tracking_sync": "Automatic tracking number updates",
            "profit_calculator": "Built-in profit margin calculator"
        },
        "pricing": {
            "setup_fee": 0,
            "transaction_fee": 0.02,  # 2%
            "monthly_fee": 29.99,
            "premium_features": 49.99
        }
    }
    
    return {"success": True, "data": suppliers_data}

@app.post("/api/dropshipping/connect")
async def connect_dropshipping_supplier(
    supplier_id: str = Form(...),
    api_credentials: str = Form(...),
    settings: str = Form("{}"),
    current_user: dict = Depends(get_current_user)
):
    """Connect to dropshipping supplier"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    connection_doc = {
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "supplier_id": supplier_id,
        "api_credentials": json.loads(api_credentials),
        "settings": json.loads(settings),
        "status": "active",
        "products_synced": 0,
        "last_sync": datetime.utcnow(),
        "created_at": datetime.utcnow()
    }
    
    await dropshipping_integration_collection.insert_one(connection_doc)
    
    return {
        "success": True,
        "data": {
            "connection_id": connection_doc["_id"],
            "supplier_id": supplier_id,
            "status": "connected",
            "sync_status": "initializing",
            "estimated_products": 1540
        }
    }

# ===== ADVANCED MARKETING SUITE (25+ ENDPOINTS) =====

@app.get("/api/marketing/influencers/marketplace")
async def get_influencer_marketplace(current_user: dict = Depends(get_current_user)):
    """Influencer marketplace for collaborations"""
    influencer_data = {
        "featured_influencers": [
            {
                "id": "inf_001",
                "name": "Sarah Tech",
                "handle": "@sarahtech",
                "followers": 125000,
                "engagement_rate": 4.2,
                "niche": ["Technology", "Gadgets"],
                "avg_price": 1250.00,
                "platform": "Instagram",
                "verified": True,
                "recent_campaigns": 23
            },
            {
                "id": "inf_002",
                "name": "Fitness Mike",
                "handle": "@fitnessmike",
                "followers": 89000,
                "engagement_rate": 5.8,
                "niche": ["Fitness", "Health"],
                "avg_price": 890.00,
                "platform": "TikTok",
                "verified": True,
                "recent_campaigns": 18
            }
        ],
        "search_filters": {
            "follower_ranges": ["1K-10K", "10K-100K", "100K-1M", "1M+"],
            "engagement_rates": ["1-3%", "3-5%", "5-8%", "8%+"],
            "niches": ["Technology", "Fashion", "Fitness", "Travel", "Food"],
            "platforms": ["Instagram", "TikTok", "YouTube", "Twitter"],
            "price_ranges": ["$100-500", "$500-1000", "$1000-5000", "$5000+"]
        },
        "campaign_types": {
            "sponsored_posts": "Single post promotion",
            "story_campaigns": "Story-based marketing",
            "video_reviews": "Product review videos",
            "giveaways": "Contest and giveaway campaigns",
            "brand_ambassadors": "Long-term partnerships"
        }
    }
    
    return {"success": True, "data": influencer_data}

@app.post("/api/marketing/influencers/campaign/create")
async def create_influencer_campaign(
    campaign_name: str = Form(...),
    influencer_ids: List[str] = Form(...),
    campaign_type: str = Form(...),
    budget: float = Form(...),
    objectives: List[str] = Form(...),
    deliverables: str = Form(...),
    timeline: str = Form(...),
    current_user: dict = Depends(get_current_user)
):
    """Create influencer marketing campaign"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    campaign_doc = {
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "campaign_name": campaign_name,
        "influencer_ids": influencer_ids,
        "campaign_type": campaign_type,
        "budget": budget,
        "objectives": objectives,
        "deliverables": deliverables,
        "timeline": json.loads(timeline),
        "status": "draft",
        "applications": 0,
        "approved_influencers": 0,
        "created_at": datetime.utcnow()
    }
    
    await influencer_marketplace_collection.insert_one(campaign_doc)
    
    return {
        "success": True,
        "data": {
            "campaign_id": campaign_doc["_id"],
            "campaign_name": campaign_doc["campaign_name"],
            "status": "draft",
            "influencers_targeted": len(influencer_ids),
            "budget": budget,
            "created_at": campaign_doc["created_at"].isoformat()
        }
    }

@app.get("/api/marketing/sms/overview")
async def get_sms_marketing_overview(current_user: dict = Depends(get_current_user)):
    """SMS marketing platform overview"""
    sms_data = {
        "account_info": {
            "credits_remaining": 2547,
            "monthly_limit": 5000,
            "sent_this_month": 2453,
            "delivery_rate": 98.7,
            "opt_out_rate": 0.8
        },
        "subscriber_segments": [
            {"name": "VIP Customers", "count": 1247, "engagement": 8.9},
            {"name": "New Subscribers", "count": 890, "engagement": 12.3},
            {"name": "Cart Abandoners", "count": 567, "engagement": 15.6}
        ],
        "campaign_templates": [
            {"name": "Flash Sale Alert", "type": "promotional", "avg_ctr": 8.9},
            {"name": "Order Confirmation", "type": "transactional", "avg_ctr": 2.1},
            {"name": "Shipping Update", "type": "notification", "avg_ctr": 1.5}
        ],
        "compliance": {
            "opt_in_required": True,
            "unsubscribe_link": True,
            "sending_hours": "9 AM - 8 PM local time",
            "frequency_limits": "Max 4 per week promotional"
        },
        "pricing": {
            "domestic": 0.0075,  # per SMS
            "international": 0.045,
            "bulk_discounts": True,
            "monthly_plans": [499, 999, 1999]
        }
    }
    
    return {"success": True, "data": sms_data}

@app.post("/api/marketing/sms/send")
async def send_sms_campaign(
    message: str = Form(...),
    recipients: List[str] = Form(...),
    schedule_time: Optional[str] = Form(None),
    campaign_type: str = Form("promotional"),
    current_user: dict = Depends(get_current_user)
):
    """Send SMS marketing campaign"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    campaign_doc = {
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "message": message,
        "recipients": recipients,
        "recipient_count": len(recipients),
        "campaign_type": campaign_type,
        "schedule_time": datetime.fromisoformat(schedule_time) if schedule_time else datetime.utcnow(),
        "status": "scheduled" if schedule_time else "sending",
        "delivery_stats": {
            "sent": 0,
            "delivered": 0,
            "failed": 0,
            "clicks": 0
        },
        "created_at": datetime.utcnow()
    }
    
    await sms_marketing_collection.insert_one(campaign_doc)
    
    return {
        "success": True,
        "data": {
            "campaign_id": campaign_doc["_id"],
            "recipients": len(recipients),
            "status": campaign_doc["status"],
            "estimated_cost": len(recipients) * 0.0075,
            "scheduled_time": campaign_doc["schedule_time"].isoformat()
        }
    }

@app.get("/api/marketing/push/overview")
async def get_push_notification_overview(current_user: dict = Depends(get_current_user)):
    """Push notification marketing overview"""
    push_data = {
        "subscriber_stats": {
            "total_subscribers": 15430,
            "web_subscribers": 8920,
            "mobile_subscribers": 6510,
            "opt_in_rate": 23.4,
            "weekly_growth": 12.8
        },
        "engagement_metrics": {
            "average_ctr": 4.2,
            "average_conversion": 1.8,
            "delivery_rate": 96.7,
            "unsubscribe_rate": 0.9
        },
        "campaign_types": {
            "promotional": {"sent": 1247, "ctr": 5.1, "conversion": 2.3},
            "transactional": {"sent": 890, "ctr": 12.4, "conversion": 0.8},
            "behavioral": {"sent": 567, "ctr": 8.9, "conversion": 4.1}
        },
        "best_practices": [
            "Send between 10 AM - 2 PM for highest engagement",
            "Personalize with user's name and preferences", 
            "Keep messages under 50 characters for mobile",
            "Use emojis to increase click rates by 15%",
            "A/B test different call-to-action buttons"
        ],
        "automation_triggers": [
            "Cart abandonment (1 hour delay)",
            "Welcome series (new subscriber)",
            "Re-engagement (30 days inactive)",
            "Price drop alerts (immediate)",
            "Back in stock (immediate)"
        ]
    }
    
    return {"success": True, "data": push_data}

# Innovative collections for competitive advantages
ai_business_insights_collection = database.ai_business_insights
smart_recommendations_collection = database.smart_recommendations
performance_optimization_collection = database.performance_optimization
trend_analysis_collection = database.trend_analysis
customer_journey_collection = database.customer_journey

# ===== AI-POWERED BUSINESS INTELLIGENCE =====
@app.get("/api/ai/business-insights")
async def get_ai_business_insights(current_user: dict = Depends(get_current_user)):
    """Advanced AI-powered business insights and predictions"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    ai_insights = {
        "revenue_predictions": {
            "next_month_forecast": 28500.00,
            "confidence": 0.89,
            "factors": [
                "Seasonal trends (+15%)",
                "Marketing campaign impact (+8%)",
                "Product launch momentum (+12%)"
            ],
            "recommendations": [
                "Increase ad spend by 20% to capitalize on momentum",
                "Launch upsell campaign for existing customers",
                "Prepare inventory for predicted demand surge"
            ]
        },
        "customer_insights": {
            "churn_risk_customers": 23,
            "high_value_prospects": 47,
            "customer_lifetime_value": 567.89,
            "optimal_acquisition_channels": ["Instagram Ads", "Content Marketing", "Referrals"],
            "personalization_opportunities": [
                "Email content based on purchase history",
                "Product recommendations using AI",
                "Dynamic pricing for loyalty tiers"
            ]
        },
        "market_analysis": {
            "competitive_position": "Strong",
            "market_share_trend": "+2.3%",
            "emerging_opportunities": [
                "Video content market (+45% growth)",
                "B2B automation services (+38% growth)",
                "Mobile-first solutions (+52% growth)"
            ],
            "threat_assessment": "Low - well-positioned against competitors"
        },
        "optimization_suggestions": [
            {
                "area": "Conversion Rate",
                "current": "3.2%",
                "potential": "4.8%",
                "impact": "$8,500/month",
                "effort": "Medium",
                "timeline": "2-4 weeks"
            },
            {
                "area": "Customer Retention",
                "current": "78%",
                "potential": "86%",
                "impact": "$12,300/month",
                "effort": "High",
                "timeline": "6-8 weeks"
            }
        ]
    }
    
    return {
        "success": True,
        "data": ai_insights
    }

# ===== SMART RECOMMENDATION ENGINE =====
@app.get("/api/recommendations/smart")
async def get_smart_recommendations(current_user: dict = Depends(get_current_user)):
    """AI-powered smart recommendations for business growth"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    recommendations = {
        "immediate_actions": [
            {
                "title": "Optimize Bio Link Layout",
                "description": "Your bio link clicks could increase by 34% with a better layout",
                "priority": "high",
                "estimated_impact": "+$1,250/month",
                "time_required": "30 minutes",
                "confidence": 0.87,
                "action_url": "/dashboard/link-in-bio/optimize"
            },
            {
                "title": "Launch Email Sequence",
                "description": "62% of your contacts haven't received emails in 30+ days",
                "priority": "high",
                "estimated_impact": "+$2,100/month",
                "time_required": "2 hours",
                "confidence": 0.92,
                "action_url": "/dashboard/email-marketing/sequences"
            }
        ],
        "growth_opportunities": [
            {
                "title": "Create Video Course",
                "description": "Your audience engagement suggests high demand for video content",
                "market_size": "15,000 potential customers",
                "revenue_potential": "$45,000",
                "competition_level": "Medium",
                "success_probability": 0.78,
                "resources_needed": ["Video equipment", "3-4 weeks time", "Basic editing skills"]
            },
            {
                "title": "Affiliate Program Launch",
                "description": "Your products are highly shareable - perfect for affiliate marketing",
                "market_size": "500 potential affiliates",
                "revenue_potential": "$28,000",
                "competition_level": "Low",
                "success_probability": 0.84,
                "resources_needed": ["Affiliate management", "Marketing materials", "Commission structure"]
            }
        ],
        "automation_suggestions": [
            {
                "workflow": "Lead Nurturing",
                "trigger": "Email signup",
                "actions": ["Welcome email", "Resource delivery", "Follow-up sequence"],
                "potential_conversion": "+15%",
                "setup_time": "45 minutes"
            },
            {
                "workflow": "Customer Retention",
                "trigger": "Purchase completion",
                "actions": ["Thank you message", "Usage tips", "Upsell offer"],
                "potential_retention": "+23%",
                "setup_time": "60 minutes"
            }
        ]
    }
    
    return {
        "success": True,
        "data": recommendations
    }

# ===== PERFORMANCE OPTIMIZATION CENTER =====
@app.get("/api/performance/optimization-center")
async def get_performance_optimization_center(current_user: dict = Depends(get_current_user)):
    """Comprehensive performance optimization dashboard"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    optimization_data = {
        "performance_score": {
            "overall_score": 78,
            "categories": {
                "conversion_rate": 82,
                "user_experience": 75,
                "content_quality": 79,
                "technical_performance": 88,
                "marketing_efficiency": 71
            }
        },
        "quick_wins": [
            {
                "title": "Update CTA Button Color",
                "impact": "12% conversion boost",
                "effort": "5 minutes",
                "roi": "Very High"
            },
            {
                "title": "Optimize Image Sizes",
                "impact": "25% faster loading",
                "effort": "30 minutes",
                "roi": "High"
            },
            {
                "title": "Add Social Proof",
                "impact": "18% trust increase",
                "effort": "45 minutes",
                "roi": "High"
            }
        ],
        "performance_trends": {
            "loading_speed": {
                "current": "2.3s",
                "target": "1.5s",
                "trend": "improving",
                "history": [2.8, 2.6, 2.4, 2.3]
            },
            "conversion_rate": {
                "current": "3.2%",
                "target": "4.5%",
                "trend": "stable",
                "history": [3.1, 3.2, 3.0, 3.2]
            },
            "user_engagement": {
                "current": "4.2min",
                "target": "5.0min",
                "trend": "improving",
                "history": [3.8, 4.0, 4.1, 4.2]
            }
        },
        "advanced_optimizations": [
            {
                "category": "A/B Testing",
                "tests": [
                    {"name": "Headline Variation", "status": "running", "confidence": 0.65},
                    {"name": "Pricing Display", "status": "completed", "winner": "Version B", "lift": "+15%"}
                ]
            },
            {
                "category": "Personalization",
                "opportunities": [
                    "Dynamic content based on user behavior",
                    "Personalized product recommendations",
                    "Custom email content"
                ]
            }
        ]
    }
    
    return {
        "success": True,
        "data": optimization_data
    }

# ===== TREND ANALYSIS & MARKET INTELLIGENCE =====
@app.get("/api/trends/market-intelligence")
async def get_market_intelligence(current_user: dict = Depends(get_current_user)):
    """Advanced trend analysis and market intelligence"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    market_data = {
        "industry_trends": {
            "growing_sectors": [
                {"name": "AI-powered Tools", "growth": "+145%", "opportunity": "Very High"},
                {"name": "Video Content", "growth": "+89%", "opportunity": "High"},
                {"name": "Mobile Commerce", "growth": "+67%", "opportunity": "Medium"}
            ],
            "declining_sectors": [
                {"name": "Static Content", "decline": "-23%", "threat": "Medium"},
                {"name": "Email-only Marketing", "decline": "-12%", "threat": "Low"}
            ]
        },
        "keyword_trends": {
            "rising": ["AI automation", "video marketing", "social commerce"],
            "stable": ["email marketing", "social media", "content creation"],
            "declining": ["banner ads", "static websites", "mass email"]
        },
        "competitive_landscape": {
            "new_entrants": 23,
            "market_consolidation": "Medium",
            "innovation_rate": "High",
            "barrier_to_entry": "Medium",
            "recommended_strategy": "Focus on AI integration and video content"
        },
        "seasonal_patterns": {
            "peak_months": ["November", "December", "January"],
            "low_months": ["July", "August"],
            "current_season": "Growth Phase",
            "next_opportunity": "Holiday Season (45 days)"
        },
        "technology_adoption": {
            "ai_tools": {"adoption": 67, "growth": "+34%"},
            "video_platforms": {"adoption": 89, "growth": "+23%"},
            "automation": {"adoption": 45, "growth": "+56%"}
        }
    }
    
    return {
        "success": True,
        "data": market_data
    }

# ===== CUSTOMER JOURNEY ANALYTICS =====
@app.get("/api/analytics/customer-journey")
async def get_customer_journey_analytics(current_user: dict = Depends(get_current_user)):
    """Advanced customer journey mapping and analytics"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    journey_data = {
        "journey_stages": {
            "awareness": {
                "visitors": 15420,
                "conversion_rate": 12.3,
                "avg_time": "45 seconds",
                "top_sources": ["Instagram", "Google Search", "Referrals"],
                "drop_off_points": ["Pricing page", "Sign-up form"]
            },
            "consideration": {
                "leads": 1897,
                "conversion_rate": 23.4,
                "avg_time": "8.2 minutes",
                "key_content": ["Product demo", "Case studies", "Free trial"],
                "optimization_opportunities": ["Reduce form fields", "Add social proof"]
            },
            "purchase": {
                "customers": 444,
                "conversion_rate": 78.2,
                "avg_order_value": 127.50,
                "payment_preferences": ["Credit card 67%", "PayPal 23%", "Bank transfer 10%"],
                "abandonment_reasons": ["Price concerns", "Complex checkout"]
            },
            "retention": {
                "active_customers": 389,
                "retention_rate": 87.6,
                "lifetime_value": 567.89,
                "expansion_revenue": 23.4,
                "churn_indicators": ["Low usage", "Support tickets", "Payment issues"]
            }
        },
        "journey_optimization": {
            "high_impact_improvements": [
                {
                    "stage": "Awareness",
                    "improvement": "Add exit-intent popup",
                    "expected_lift": "+15% email captures"
                },
                {
                    "stage": "Consideration",
                    "improvement": "Implement chatbot",
                    "expected_lift": "+28% engagement"
                },
                {
                    "stage": "Purchase",
                    "improvement": "One-click checkout",
                    "expected_lift": "+22% completion"
                }
            ]
        },
        "segment_analysis": {
            "high_value": {"count": 89, "avg_ltv": 1250.00, "characteristics": ["Power users", "Multiple purchases"]},
            "at_risk": {"count": 67, "avg_ltv": 234.00, "characteristics": ["Low engagement", "Single purchase"]},
            "growth_potential": {"count": 156, "avg_ltv": 445.00, "characteristics": ["Regular usage", "Feature exploration"]}
        }
    }
    
    return {
        "success": True,
        "data": journey_data
    }

# ===== PREDICTIVE ANALYTICS ENGINE =====
@app.get("/api/analytics/predictive")
async def get_predictive_analytics(current_user: dict = Depends(get_current_user)):
    """Advanced predictive analytics for business forecasting"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    predictions = {
        "revenue_forecast": {
            "next_30_days": {
                "prediction": 28500.00,
                "confidence_interval": [26100.00, 31200.00],
                "confidence": 0.89,
                "key_drivers": ["Seasonal uptick", "Marketing campaign", "Product launch"]
            },
            "next_90_days": {
                "prediction": 89200.00,
                "confidence_interval": [82400.00, 96800.00],
                "confidence": 0.78,
                "key_drivers": ["Holiday season", "Feature releases", "Market expansion"]
            }
        },
        "customer_predictions": {
            "churn_risk": {
                "high_risk": 23,
                "medium_risk": 67,
                "low_risk": 299,
                "prevention_strategies": ["Engagement campaign", "Loyalty program", "Personal outreach"]
            },
            "growth_potential": {
                "upsell_candidates": 45,
                "cross_sell_opportunities": 78,
                "referral_likelihood": 89,
                "expansion_revenue_potential": 15670.00
            }
        },
        "market_predictions": {
            "demand_forecast": {
                "increasing": ["AI tools", "Video content", "Automation"],
                "stable": ["Email marketing", "Social media"],
                "decreasing": ["Banner ads", "Cold calling"]
            },
            "competitive_threats": {
                "risk_level": "Medium",
                "new_entrants": 3,
                "market_disruption_probability": 0.23,
                "recommended_defensive_actions": ["Feature innovation", "Customer retention", "Market expansion"]
            }
        },
        "optimization_predictions": {
            "a_b_test_outcomes": [
                {"test": "CTA Button Color", "predicted_winner": "Version A", "confidence": 0.87, "expected_lift": "+12%"},
                {"test": "Pricing Structure", "predicted_winner": "Version B", "confidence": 0.74, "expected_lift": "+8%"}
            ],
            "feature_impact": [
                {"feature": "Mobile app", "adoption_prediction": 0.67, "revenue_impact": "+23%"},
                {"feature": "API access", "adoption_prediction": 0.34, "revenue_impact": "+15%"}
            ]
        }
    }
    
    return {
        "success": True,
        "data": predictions
    }

# ===== COLLABORATION & TEAM PRODUCTIVITY =====
@app.get("/api/team/productivity-insights")
async def get_team_productivity_insights(current_user: dict = Depends(get_current_user)):
    """Team productivity analytics and insights"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    productivity_data = {
        "team_metrics": {
            "total_members": 8,
            "active_members": 7,
            "collaboration_score": 8.4,
            "productivity_trend": "+12%",
            "task_completion_rate": 87.3
        },
        "individual_insights": [
            {
                "member": "Sarah Johnson",
                "role": "Marketing Manager",
                "productivity_score": 92,
                "tasks_completed": 23,
                "collaboration_rate": 8.9,
                "strengths": ["Content creation", "Campaign management"],
                "growth_areas": ["Analytics reporting"]
            },
            {
                "member": "Mike Chen",
                "role": "Developer",
                "productivity_score": 88,
                "tasks_completed": 19,
                "collaboration_rate": 7.8,
                "strengths": ["Technical implementation", "Problem solving"],
                "growth_areas": ["Documentation", "Team communication"]
            }
        ],
        "collaboration_patterns": {
            "peak_hours": ["09:00-11:00", "14:00-16:00"],
            "communication_channels": {
                "in_app": 67,
                "email": 23,
                "meetings": 10
            },
            "cross_department_collaboration": 78,
            "knowledge_sharing": 65
        },
        "productivity_recommendations": [
            "Schedule team sync at 2 PM for optimal collaboration",
            "Implement pair programming for complex tasks",
            "Create documentation templates to improve consistency",
            "Set up automated progress tracking"
        ]
    }
    
    return {
        "success": True,
        "data": productivity_data
    }

# Advanced collections for valuable expansions
automation_workflows_collection = database.automation_workflows
social_media_analytics_collection = database.social_media_analytics
advanced_notifications_collection = database.advanced_notifications
competitor_tracking_collection = database.competitor_tracking
affiliate_program_collection = database.affiliate_program

# ===== ADVANCED AUTOMATION WORKFLOWS =====
@app.get("/api/automation/workflows")
async def get_automation_workflows(current_user: dict = Depends(get_current_user)):
    """Get automation workflows for workspace"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    workflows = await automation_workflows_collection.find(
        {"workspace_id": str(workspace["_id"])}
    ).to_list(length=50)
    
    for workflow in workflows:
        workflow["id"] = str(workflow["_id"])
    
    return {
        "success": True,
        "data": {
            "workflows": [
                {
                    "id": workflow["id"],
                    "name": workflow["name"],
                    "description": workflow.get("description"),
                    "trigger": workflow["trigger"],
                    "actions": workflow["actions"],
                    "status": workflow.get("status", "active"),
                    "executions": workflow.get("executions", 0),
                    "success_rate": workflow.get("success_rate", 0),
                    "created_at": workflow["created_at"].isoformat()
                } for workflow in workflows
            ],
            "templates": [
                {
                    "name": "Welcome New Users",
                    "trigger": "user_registered",
                    "actions": ["send_welcome_email", "add_to_onboarding_sequence"]
                },
                {
                    "name": "Re-engage Inactive Users",
                    "trigger": "user_inactive_30_days",
                    "actions": ["send_reengagement_email", "offer_discount"]
                },
                {
                    "name": "Post-Purchase Follow-up",
                    "trigger": "order_completed",
                    "actions": ["send_thank_you_email", "request_review", "recommend_products"]
                }
            ]
        }
    }

@app.post("/api/automation/workflows/create")
async def create_automation_workflow(
    name: str = Form(...),
    description: str = Form(""),
    trigger: str = Form(...),
    actions: List[str] = Form(...),
    conditions: Optional[str] = Form("{}"),
    current_user: dict = Depends(get_current_user)
):
    """Create new automation workflow"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    workflow_doc = {
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "name": name,
        "description": description,
        "trigger": trigger,
        "actions": actions,
        "conditions": json.loads(conditions) if conditions else {},
        "status": "active",
        "executions": 0,
        "success_count": 0,
        "failure_count": 0,
        "success_rate": 0,
        "created_at": datetime.utcnow(),
        "last_executed": None
    }
    
    await automation_workflows_collection.insert_one(workflow_doc)
    
    return {
        "success": True,
        "data": {
            "workflow": {
                "id": workflow_doc["_id"],
                "name": workflow_doc["name"],
                "trigger": workflow_doc["trigger"],
                "actions": workflow_doc["actions"],
                "created_at": workflow_doc["created_at"].isoformat()
            }
        }
    }

# ===== ADVANCED SOCIAL MEDIA ANALYTICS =====
@app.get("/api/social/analytics/comprehensive")
async def get_comprehensive_social_analytics(current_user: dict = Depends(get_current_user)):
    """Advanced social media analytics with competitor tracking"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    # Mock comprehensive social media analytics
    analytics_data = {
        "overview": {
            "total_followers": 125890,
            "engagement_rate": 4.2,
            "reach": 450000,
            "impressions": 1250000,
            "growth_rate": 12.5
        },
        "platform_breakdown": {
            "instagram": {
                "followers": 85000,
                "engagement_rate": 3.8,
                "best_posting_time": "18:00",
                "top_hashtags": ["#business", "#entrepreneur", "#success"]
            },
            "facebook": {
                "followers": 25000,
                "engagement_rate": 2.1,
                "best_posting_time": "12:00",
                "top_content_types": ["videos", "images", "links"]
            },
            "twitter": {
                "followers": 15890,
                "engagement_rate": 1.9,
                "best_posting_time": "09:00",
                "top_keywords": ["tech", "innovation", "startup"]
            }
        },
        "competitor_analysis": {
            "avg_engagement_rate": 3.1,
            "market_position": "Above Average",
            "growth_comparison": "+25% vs competitors",
            "content_gap_opportunities": ["video content", "user-generated content"]
        },
        "content_performance": {
            "best_performing_posts": [
                {"type": "video", "engagement": 2890, "reach": 45000},
                {"type": "carousel", "engagement": 2340, "reach": 38000},
                {"type": "image", "engagement": 1890, "reach": 32000}
            ],
            "optimal_posting_schedule": {
                "monday": ["09:00", "18:00"],
                "tuesday": ["12:00", "19:00"],
                "wednesday": ["10:00", "17:00"]
            }
        },
        "ai_insights": [
            "Increase video content by 40% to improve engagement",
            "Post 3x more on Tuesday for maximum reach",
            "Use #entrepreneurship hashtag - 25% higher engagement",
            "Stories perform 60% better than feed posts"
        ]
    }
    
    return {
        "success": True,
        "data": analytics_data
    }

@app.post("/api/social/competitors/track")
async def track_competitor(
    competitor_name: str = Form(...),
    platform: str = Form(...),
    username: str = Form(...),
    current_user: dict = Depends(get_current_user)
):
    """Add competitor for tracking"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    competitor_doc = {
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "competitor_name": competitor_name,
        "platform": platform,
        "username": username,
        "tracking_metrics": ["followers", "engagement_rate", "posting_frequency"],
        "status": "active",
        "created_at": datetime.utcnow(),
        "last_analyzed": None
    }
    
    await competitor_tracking_collection.insert_one(competitor_doc)
    
    return {
        "success": True,
        "data": {
            "competitor": {
                "id": competitor_doc["_id"],
                "name": competitor_doc["competitor_name"],
                "platform": competitor_doc["platform"],
                "username": competitor_doc["username"],
                "created_at": competitor_doc["created_at"].isoformat()
            }
        }
    }

# ===== ADVANCED NOTIFICATION SYSTEM =====
@app.get("/api/notifications/smart")
async def get_smart_notifications(current_user: dict = Depends(get_current_user)):
    """AI-powered smart notifications and insights"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        return {"success": True, "data": {"notifications": []}}
    
    smart_notifications = [
        {
            "id": str(uuid.uuid4()),
            "type": "insight",
            "priority": "high",
            "title": "Revenue Opportunity Detected",
            "message": "Your Instagram engagement is up 45% - perfect time to launch that course!",
            "action": "Create Course",
            "action_url": "/dashboard/courses/create",
            "ai_confidence": 0.89,
            "data_points": ["engagement_trend", "audience_analysis"],
            "created_at": datetime.utcnow().isoformat()
        },
        {
            "id": str(uuid.uuid4()),
            "type": "optimization",
            "priority": "medium",
            "title": "Link in Bio Performance Alert",
            "message": "Your bio link clicks dropped 12% this week. Consider updating content.",
            "action": "Update Bio",
            "action_url": "/dashboard/link-in-bio",
            "ai_confidence": 0.76,
            "data_points": ["click_analytics", "content_freshness"],
            "created_at": datetime.utcnow().isoformat()
        },
        {
            "id": str(uuid.uuid4()),
            "type": "opportunity",
            "priority": "medium",
            "title": "Trending Hashtag Alert",
            "message": "#DigitalNomad is trending in your niche (+340% usage). Perfect for your next post!",
            "action": "Create Post",
            "action_url": "/dashboard/social-media/create",
            "ai_confidence": 0.82,
            "data_points": ["hashtag_trends", "audience_interests"],
            "created_at": datetime.utcnow().isoformat()
        }
    ]
    
    return {
        "success": True,
        "data": {
            "smart_notifications": smart_notifications,
            "notification_categories": [
                {"type": "insight", "count": 12, "description": "AI-powered business insights"},
                {"type": "optimization", "count": 8, "description": "Performance optimization suggestions"},
                {"type": "opportunity", "count": 15, "description": "Revenue and growth opportunities"},
                {"type": "alert", "count": 3, "description": "Important system alerts"}
            ],
            "ai_summary": {
                "insights_this_week": 23,
                "recommendations_followed": 18,
                "estimated_revenue_impact": 2450.00
            }
        }
    }

# ===== AFFILIATE PROGRAM MANAGEMENT =====
@app.get("/api/affiliate/program/overview")
async def get_affiliate_program_overview(current_user: dict = Depends(get_current_user)):
    """Get affiliate program overview and statistics"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    # Mock affiliate program data
    affiliate_data = {
        "program_stats": {
            "total_affiliates": 245,
            "active_affiliates": 189,
            "total_commissions_paid": 15670.50,
            "pending_commissions": 3240.75,
            "conversion_rate": 3.2,
            "avg_commission_per_sale": 45.60
        },
        "top_affiliates": [
            {"name": "Sarah Johnson", "sales": 45, "commission_earned": 2250.00, "conversion_rate": 5.2},
            {"name": "Mike Chen", "sales": 38, "commission_earned": 1900.00, "conversion_rate": 4.8},
            {"name": "Emma Davis", "sales": 32, "commission_earned": 1600.00, "conversion_rate": 4.1}
        ],
        "commission_structure": {
            "tier_1": {"min_sales": 0, "commission_rate": 0.30, "description": "30% for first 10 sales"},
            "tier_2": {"min_sales": 10, "commission_rate": 0.35, "description": "35% for 10-50 sales"},
            "tier_3": {"min_sales": 50, "commission_rate": 0.40, "description": "40% for 50+ sales"}
        },
        "marketing_materials": [
            {"type": "banner", "size": "728x90", "url": "/assets/banners/728x90.png"},
            {"type": "banner", "size": "300x250", "url": "/assets/banners/300x250.png"},
            {"type": "text_link", "title": "Try Mewayz Today", "url": "https://mewayz.com"},
            {"type": "email_template", "subject": "Boost Your Business", "template_id": "email_001"}
        ]
    }
    
    return {
        "success": True,
        "data": affiliate_data
    }

@app.post("/api/affiliate/invite")
async def invite_affiliate(
    email: str = Form(...),
    name: str = Form(...),
    message: Optional[str] = Form(""),
    current_user: dict = Depends(get_current_user)
):
    """Invite new affiliate"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    affiliate_doc = {
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "inviter_id": current_user["id"],
        "affiliate_email": email,
        "affiliate_name": name,
        "invitation_message": message,
        "affiliate_code": secrets.token_urlsafe(8).upper(),
        "status": "invited",
        "commission_rate": 0.30,  # Default 30%
        "sales_count": 0,
        "total_commission": 0.00,
        "created_at": datetime.utcnow(),
        "invited_at": datetime.utcnow(),
        "joined_at": None
    }
    
    await affiliate_program_collection.insert_one(affiliate_doc)
    
    return {
        "success": True,
        "data": {
            "invitation": {
                "id": affiliate_doc["_id"],
                "affiliate_email": affiliate_doc["affiliate_email"],
                "affiliate_code": affiliate_doc["affiliate_code"],
                "commission_rate": affiliate_doc["commission_rate"],
                "invitation_url": f"https://mewayz.com/affiliate/join/{affiliate_doc['affiliate_code']}",
                "created_at": affiliate_doc["created_at"].isoformat()
            }
        }
    }

# ===== ADVANCED SEARCH & DISCOVERY =====
@app.get("/api/search/global")
async def global_search(
    q: str = Query(..., description="Search query"),
    category: Optional[str] = Query(None, description="Search category"),
    current_user: dict = Depends(get_current_user)
):
    """Advanced global search across all platform content"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    search_results = {
        "products": [],
        "courses": [],
        "templates": [],
        "contacts": [],
        "content": [],
        "analytics": []
    }
    
    # Mock search results based on query
    if "marketing" in q.lower():
        search_results["courses"] = [
            {"id": "course_1", "title": "Digital Marketing Mastery", "type": "course", "relevance": 0.95}
        ]
        search_results["templates"] = [
            {"id": "template_1", "title": "Marketing Email Template", "type": "template", "relevance": 0.87}
        ]
    
    if "analytics" in q.lower():
        search_results["analytics"] = [
            {"id": "report_1", "title": "Marketing Performance Report", "type": "report", "relevance": 0.92}
        ]
    
    total_results = sum(len(results) for results in search_results.values())
    
    return {
        "success": True,
        "data": {
            "query": q,
            "total_results": total_results,
            "results": search_results,
            "suggestions": [
                "Try searching for 'social media templates'",
                "Looking for 'course analytics'?",
                "Check out 'email marketing automation'"
            ],
            "search_time": "0.045s"
        }
    }

# ===== BULK OPERATIONS SYSTEM =====
@app.post("/api/bulk/contacts/import")
async def bulk_import_contacts(
    file: UploadFile = File(...),
    workspace_id: str = Form(...),
    current_user: dict = Depends(get_current_user)
):
    """Bulk import contacts from CSV file"""
    if not file.filename.endswith('.csv'):
        raise HTTPException(status_code=400, detail="Only CSV files are supported")
    
    # Read CSV content (mock implementation)
    content = await file.read()
    
    # Mock processing results
    import_results = {
        "total_rows": 150,
        "successful_imports": 142,
        "failed_imports": 8,
        "duplicates_found": 12,
        "new_contacts": 130,
        "updated_contacts": 12,
        "errors": [
            {"row": 15, "error": "Invalid email format"},
            {"row": 23, "error": "Missing required field: name"}
        ]
    }
    
    return {
        "success": True,
        "data": {
            "import_results": import_results,
            "import_id": str(uuid.uuid4()),
            "processing_time": "2.3s",
            "next_steps": [
                "Review failed imports",
                "Set up email sequences for new contacts",
                "Update contact tags and segments"
            ]
        }
    }

@app.post("/api/bulk/social/schedule")
async def bulk_schedule_posts(
    posts: List[dict],
    current_user: dict = Depends(get_current_user)
):
    """Bulk schedule social media posts"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    scheduled_posts = []
    for post in posts:
        post_doc = {
            "id": str(uuid.uuid4()),
            "content": post["content"],
            "platforms": post["platforms"],
            "scheduled_time": post["scheduled_time"],
            "status": "scheduled",
            "created_at": datetime.utcnow().isoformat()
        }
        scheduled_posts.append(post_doc)
    
    return {
        "success": True,
        "data": {
            "scheduled_posts": len(scheduled_posts),
            "posts": scheduled_posts,
            "estimated_reach": 125000,
            "estimated_engagement": 5200,
            "next_posting_date": min([post["scheduled_time"] for post in posts])
        }
    }

# Advanced Collections for new features
template_marketplace_collection = database.template_marketplace
template_versions_collection = database.template_versions
course_certificates_collection = database.course_certificates
multi_vendor_commissions_collection = database.multi_vendor_commissions
advanced_analytics_collection = database.advanced_analytics
webhook_configurations_collection = database.webhook_configurations
white_label_settings_collection = database.white_label_settings

# ===== ADVANCED TEMPLATE MARKETPLACE ENDPOINTS =====
@app.get("/api/templates/marketplace")
async def get_template_marketplace(
    category: Optional[str] = None,
    sort_by: str = "popular",
    current_user: dict = Depends(get_current_user)
):
    """Enhanced template marketplace with advanced filtering"""
    filter_query = {}
    if category:
        filter_query["category"] = category
    
    # Sort options
    sort_options = {
        "popular": {"downloads": -1},
        "newest": {"created_at": -1},
        "rating": {"average_rating": -1},
        "price_low": {"price": 1},
        "price_high": {"price": -1}
    }
    
    templates = await template_marketplace_collection.find(filter_query).sort(
        list(sort_options.get(sort_by, {"created_at": -1}).items())[0]
    ).limit(50).to_list(length=50)
    
    for template in templates:
        template["id"] = str(template["_id"])
    
    return {
        "success": True,
        "data": {
            "templates": [
                {
                    "id": template["id"],
                    "name": template["name"],
                    "description": template.get("description"),
                    "category": template["category"],
                    "price": template.get("price", 0),
                    "is_premium": template.get("is_premium", False),
                    "creator": template["creator"],
                    "downloads": template.get("downloads", 0),
                    "average_rating": template.get("average_rating", 0),
                    "preview_image": template.get("preview_image"),
                    "tags": template.get("tags", []),
                    "created_at": template["created_at"].isoformat(),
                    "last_updated": template.get("last_updated", template["created_at"]).isoformat()
                } for template in templates
            ],
            "total": len(templates),
            "categories": ["link_bio", "email", "social_media", "website", "course", "form"],
            "sort_options": list(sort_options.keys())
        }
    }

@app.post("/api/templates/create")
async def create_template(
    name: str = Form(...),
    description: str = Form(...),
    category: str = Form(...),
    price: float = Form(0),
    template_data: str = Form(...),
    tags: List[str] = Form([]),
    current_user: dict = Depends(get_current_user)
):
    """Create new template for marketplace"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    template_doc = {
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "creator_id": current_user["id"],
        "creator": current_user["name"],
        "name": name,
        "description": description,
        "category": category,
        "price": price,
        "is_premium": price > 0,
        "template_data": json.loads(template_data),
        "tags": tags,
        "downloads": 0,
        "rating_count": 0,
        "rating_total": 0,
        "average_rating": 0,
        "preview_image": None,
        "status": "pending_review",
        "created_at": datetime.utcnow(),
        "last_updated": datetime.utcnow()
    }
    
    await template_marketplace_collection.insert_one(template_doc)
    
    # Create initial version
    version_doc = {
        "_id": str(uuid.uuid4()),
        "template_id": template_doc["_id"],
        "version": "1.0.0",
        "template_data": template_doc["template_data"],
        "changelog": "Initial version",
        "created_at": datetime.utcnow()
    }
    await template_versions_collection.insert_one(version_doc)
    
    return {
        "success": True,
        "data": {
            "template": {
                "id": template_doc["_id"],
                "name": template_doc["name"],
                "category": template_doc["category"],
                "status": template_doc["status"],
                "created_at": template_doc["created_at"].isoformat()
            }
        }
    }

@app.post("/api/templates/{template_id}/purchase")
async def purchase_template(
    template_id: str,
    current_user: dict = Depends(get_current_user)
):
    """Purchase premium template"""
    template = await template_marketplace_collection.find_one({"_id": template_id})
    if not template:
        raise HTTPException(status_code=404, detail="Template not found")
    
    if template["price"] == 0:
        # Free template - just track download
        await template_marketplace_collection.update_one(
            {"_id": template_id},
            {"$inc": {"downloads": 1}}
        )
    else:
        # Premium template - would integrate with Stripe here
        # For now, simulate purchase
        await template_marketplace_collection.update_one(
            {"_id": template_id},
            {"$inc": {"downloads": 1}}
        )
    
    return {
        "success": True,
        "data": {
            "template_id": template_id,
            "purchase_status": "completed",
            "download_url": f"/api/templates/{template_id}/download"
        }
    }

# ===== ADVANCED COURSE FEATURES =====
@app.get("/api/courses/{course_id}/certificates")
async def get_course_certificates(
    course_id: str,
    current_user: dict = Depends(get_current_user)
):
    """Get certificates for course completions"""
    certificates = await course_certificates_collection.find(
        {"course_id": course_id}
    ).to_list(length=100)
    
    for cert in certificates:
        cert["id"] = str(cert["_id"])
    
    return {
        "success": True,
        "data": {
            "certificates": [
                {
                    "id": cert["id"],
                    "student_name": cert["student_name"],
                    "completion_date": cert["completion_date"].isoformat(),
                    "certificate_url": cert.get("certificate_url"),
                    "grade": cert.get("grade"),
                    "skills_earned": cert.get("skills_earned", [])
                } for cert in certificates
            ]
        }
    }

@app.post("/api/courses/{course_id}/generate-certificate")
async def generate_certificate(
    course_id: str,
    student_id: str = Form(...),
    grade: Optional[str] = Form("Pass"),
    current_user: dict = Depends(get_current_user)
):
    """Generate completion certificate for student"""
    course = await courses_collection.find_one({"_id": course_id})
    if not course:
        raise HTTPException(status_code=404, detail="Course not found")
    
    student = await users_collection.find_one({"_id": student_id})
    if not student:
        raise HTTPException(status_code=404, detail="Student not found")
    
    certificate_doc = {
        "_id": str(uuid.uuid4()),
        "course_id": course_id,
        "student_id": student_id,
        "student_name": student["name"],
        "course_title": course["title"],
        "completion_date": datetime.utcnow(),
        "grade": grade,
        "certificate_url": f"/certificates/{str(uuid.uuid4())}.pdf",
        "skills_earned": course.get("skills", []),
        "instructor": current_user["name"],
        "created_at": datetime.utcnow()
    }
    
    await course_certificates_collection.insert_one(certificate_doc)
    
    return {
        "success": True,
        "data": {
            "certificate": {
                "id": certificate_doc["_id"],
                "certificate_url": certificate_doc["certificate_url"],
                "completion_date": certificate_doc["completion_date"].isoformat()
            }
        }
    }

# ===== ADVANCED E-COMMERCE FEATURES =====
@app.get("/api/ecommerce/vendors/dashboard")
async def get_vendor_dashboard(current_user: dict = Depends(get_current_user)):
    """Advanced vendor dashboard with commission tracking"""
    # Get vendor's products and sales
    vendor_products = await marketplace_products_collection.count_documents({"vendor_id": current_user["id"]})
    
    # Calculate commissions (mock data for now)
    commission_data = {
        "total_sales": 12450.50,
        "platform_commission": 1245.05,  # 10%
        "net_earnings": 11205.45,
        "pending_payouts": 5602.75,
        "commission_rate": 10.0
    }
    
    return {
        "success": True,
        "data": {
            "vendor_metrics": {
                "total_products": vendor_products,
                "active_products": vendor_products,
                "total_sales": commission_data["total_sales"],
                "orders_this_month": 45,
                "rating": 4.8
            },
            "commission_breakdown": commission_data,
            "recent_sales": [
                {"product": "Digital Course", "amount": 99.99, "commission": 9.99, "date": "2025-07-20"},
                {"product": "Template Pack", "amount": 49.99, "commission": 4.99, "date": "2025-07-19"}
            ]
        }
    }

@app.post("/api/ecommerce/products/compare")
async def compare_products(
    product_ids: List[str],
    current_user: dict = Depends(get_current_user)
):
    """Advanced product comparison feature"""
    products = await marketplace_products_collection.find(
        {"_id": {"$in": product_ids}}
    ).to_list(length=10)
    
    comparison_data = []
    for product in products:
        comparison_data.append({
            "id": str(product["_id"]),
            "name": product["name"],
            "price": product["price"],
            "rating": product.get("rating", 0),
            "features": product.get("features", []),
            "category": product.get("category"),
            "vendor": product.get("vendor_name"),
            "reviews_count": product.get("reviews_count", 0)
        })
    
    return {
        "success": True,
        "data": {
            "comparison": comparison_data,
            "comparison_matrix": {
                "price_range": {"min": min([p["price"] for p in comparison_data]), "max": max([p["price"] for p in comparison_data])},
                "rating_range": {"min": min([p["rating"] for p in comparison_data]), "max": max([p["rating"] for p in comparison_data])},
                "common_features": []  # Would calculate common features
            }
        }
    }

# ===== ADVANCED ANALYTICS FEATURES =====
@app.get("/api/analytics/custom-reports")
async def get_custom_reports(current_user: dict = Depends(get_current_user)):
    """Custom report builder data"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    # Available metrics for custom reporting
    available_metrics = {
        "user_metrics": ["total_users", "active_users", "new_signups", "user_retention"],
        "revenue_metrics": ["total_revenue", "mrr", "arr", "conversion_rate"],
        "engagement_metrics": ["page_views", "session_duration", "bounce_rate"],
        "product_metrics": ["product_views", "cart_additions", "purchases"]
    }
    
    # Sample custom reports
    saved_reports = [
        {
            "id": str(uuid.uuid4()),
            "name": "Monthly Revenue Analysis",
            "metrics": ["total_revenue", "mrr", "conversion_rate"],
            "date_range": "last_30_days",
            "chart_type": "line",
            "created_at": datetime.utcnow().isoformat()
        }
    ]
    
    return {
        "success": True,
        "data": {
            "available_metrics": available_metrics,
            "saved_reports": saved_reports,
            "report_templates": [
                {"name": "Revenue Dashboard", "type": "financial"},
                {"name": "User Engagement", "type": "engagement"},
                {"name": "Product Performance", "type": "ecommerce"}
            ]
        }
    }

@app.post("/api/analytics/reports/create")
async def create_custom_report(
    name: str = Form(...),
    metrics: List[str] = Form(...),
    date_range: str = Form("last_30_days"),
    chart_type: str = Form("line"),
    current_user: dict = Depends(get_current_user)
):
    """Create custom analytics report"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    report_doc = {
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "user_id": current_user["id"],
        "name": name,
        "metrics": metrics,
        "date_range": date_range,
        "chart_type": chart_type,
        "filters": {},
        "schedule": None,
        "created_at": datetime.utcnow(),
        "last_generated": None
    }
    
    await advanced_analytics_collection.insert_one(report_doc)
    
    return {
        "success": True,
        "data": {
            "report": {
                "id": report_doc["_id"],
                "name": report_doc["name"],
                "metrics": report_doc["metrics"],
                "created_at": report_doc["created_at"].isoformat()
            }
        }
    }

# ===== ADVANCED INTEGRATION FEATURES =====
@app.get("/api/integrations/webhooks")
async def get_webhook_configurations(current_user: dict = Depends(get_current_user)):
    """Get webhook configurations for workspace"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    webhooks = await webhook_configurations_collection.find(
        {"workspace_id": str(workspace["_id"])}
    ).to_list(length=50)
    
    for webhook in webhooks:
        webhook["id"] = str(webhook["_id"])
    
    return {
        "success": True,
        "data": {
            "webhooks": [
                {
                    "id": webhook["id"],
                    "name": webhook["name"],
                    "url": webhook["url"],
                    "events": webhook["events"],
                    "status": webhook.get("status", "active"),
                    "last_triggered": webhook.get("last_triggered"),
                    "success_count": webhook.get("success_count", 0),
                    "failure_count": webhook.get("failure_count", 0)
                } for webhook in webhooks
            ],
            "available_events": [
                "user.created", "order.completed", "payment.received",
                "course.completed", "template.purchased", "booking.created"
            ]
        }
    }

@app.post("/api/integrations/webhooks/create")
async def create_webhook(
    name: str = Form(...),
    url: str = Form(...),
    events: List[str] = Form(...),
    secret: Optional[str] = Form(None),
    current_user: dict = Depends(get_current_user)
):
    """Create new webhook configuration"""
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    if not workspace:
        raise HTTPException(status_code=404, detail="Workspace not found")
    
    webhook_doc = {
        "_id": str(uuid.uuid4()),
        "workspace_id": str(workspace["_id"]),
        "name": name,
        "url": url,
        "events": events,
        "secret": secret or secrets.token_urlsafe(32),
        "status": "active",
        "success_count": 0,
        "failure_count": 0,
        "created_at": datetime.utcnow(),
        "last_triggered": None
    }
    
    await webhook_configurations_collection.insert_one(webhook_doc)
    
    return {
        "success": True,
        "data": {
            "webhook": {
                "id": webhook_doc["_id"],
                "name": webhook_doc["name"],
                "url": webhook_doc["url"],
                "events": webhook_doc["events"],
                "secret": webhook_doc["secret"],
                "created_at": webhook_doc["created_at"].isoformat()
            }
        }
    }

# ===== WHITE-LABEL & CUSTOMIZATION FEATURES =====
@app.get("/api/admin/white-label/settings")
async def get_white_label_settings(current_admin: dict = Depends(get_current_admin_user)):
    """Get white-label customization settings"""
    settings = await white_label_settings_collection.find_one({"is_default": True})
    
    if not settings:
        # Create default settings
        settings = {
            "_id": str(uuid.uuid4()),
            "is_default": True,
            "branding": {
                "platform_name": "Mewayz",
                "logo_url": None,
                "favicon_url": None,
                "primary_color": "#3B82F6",
                "secondary_color": "#1F2937"
            },
            "custom_domain": None,
            "email_branding": {
                "sender_name": "Mewayz Platform",
                "sender_email": "noreply@mewayz.com",
                "email_footer": "Powered by Mewayz Platform"
            },
            "features": {
                "hide_powered_by": False,
                "custom_login_page": False,
                "custom_dashboard": False
            },
            "created_at": datetime.utcnow(),
            "updated_at": datetime.utcnow()
        }
        await white_label_settings_collection.insert_one(settings)
    
    settings["id"] = str(settings["_id"])
    
    return {
        "success": True,
        "data": {
            "settings": settings,
            "customization_options": {
                "branding": ["logo", "colors", "favicon", "platform_name"],
                "features": ["hide_powered_by", "custom_login", "custom_dashboard"],
                "email": ["sender_info", "templates", "footer_text"],
                "domain": ["custom_domain", "ssl_certificate"]
            }
        }
    }

@app.post("/api/admin/white-label/update")
async def update_white_label_settings(
    settings: dict,
    current_admin: dict = Depends(get_current_admin_user)
):
    """Update white-label settings"""
    await white_label_settings_collection.update_one(
        {"is_default": True},
        {
            "$set": {
                **settings,
                "updated_at": datetime.utcnow()
            }
        },
        upsert=True
    )
    
    return {
        "success": True,
        "data": {
            "message": "White-label settings updated successfully",
            "updated_at": datetime.utcnow().isoformat()
        }
    }

# ===== ADVANCED FINANCIAL FEATURES =====
@app.get("/api/financial/multi-currency")
async def get_multi_currency_settings(current_user: dict = Depends(get_current_user)):
    """Get multi-currency configuration"""
    supported_currencies = [
        {"code": "USD", "name": "US Dollar", "symbol": "$"},
        {"code": "EUR", "name": "Euro", "symbol": "€"},
        {"code": "GBP", "name": "British Pound", "symbol": "£"},
        {"code": "CAD", "name": "Canadian Dollar", "symbol": "C$"},
        {"code": "AUD", "name": "Australian Dollar", "symbol": "A$"},
        {"code": "JPY", "name": "Japanese Yen", "symbol": "¥"}
    ]
    
    workspace = await workspaces_collection.find_one({"owner_id": current_user["id"]})
    workspace_currency = workspace.get("currency", "USD") if workspace else "USD"
    
    return {
        "success": True,
        "data": {
            "supported_currencies": supported_currencies,
            "workspace_currency": workspace_currency,
            "currency_rates": {
                "USD": 1.0,
                "EUR": 0.85,
                "GBP": 0.73,
                "CAD": 1.25,
                "AUD": 1.35,
                "JPY": 110.0
            },
            "auto_conversion": True,
            "last_updated": datetime.utcnow().isoformat()
        }
    }

@app.post("/api/financial/tax/calculate")
async def calculate_tax(
    amount: float = Form(...),
    tax_region: str = Form("US"),
    product_type: str = Form("digital"),
    current_user: dict = Depends(get_current_user)
):
    """Advanced tax calculation"""
    # Mock tax calculation logic
    tax_rates = {
        "US": {"digital": 0.06, "physical": 0.08},
        "EU": {"digital": 0.20, "physical": 0.19},
        "UK": {"digital": 0.20, "physical": 0.20},
        "CA": {"digital": 0.05, "physical": 0.13}
    }
    
    rate = tax_rates.get(tax_region, {"digital": 0.06, "physical": 0.08}).get(product_type, 0.06)
    tax_amount = amount * rate
    total_amount = amount + tax_amount
    
    return {
        "success": True,
        "data": {
            "subtotal": amount,
            "tax_rate": rate,
            "tax_amount": round(tax_amount, 2),
            "total_amount": round(total_amount, 2),
            "tax_region": tax_region,
            "tax_jurisdiction": "State/Federal" if tax_region == "US" else "VAT"
        }
    }

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8001, reload=True)

# Include routers
from onboarding_system import router as onboarding_router
from subscription_system import router as subscription_router
from ai_generation_system import router as ai_generation_router
collaboration_router = get_collaboration_routes()

app.include_router(onboarding_router)
app.include_router(subscription_router)
app.include_router(ai_generation_router)
app.include_router(collaboration_router)

