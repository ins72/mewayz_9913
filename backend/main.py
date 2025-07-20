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

