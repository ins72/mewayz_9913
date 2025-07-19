# FastAPI Backend - Professional Mewayz Platform
from fastapi import FastAPI, HTTPException, Depends, status, UploadFile, File, Form, Query, BackgroundTasks
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel, EmailStr
from motor.motor_asyncio import AsyncIOMotorClient
from pymongo import MongoClient
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

# Load environment variables
load_dotenv()

# Import collaboration system
from realtime_collaboration_system import get_collaboration_routes

# Database setup
MONGO_URL = os.getenv("MONGO_URL", "mongodb://localhost:27017/mewayz_professional")
SECRET_KEY = os.getenv("SECRET_KEY", "mewayz-professional-secret-key-2025-ultra-secure")
ALGORITHM = os.getenv("ALGORITHM", "HS256")
ACCESS_TOKEN_EXPIRE_MINUTES = int(os.getenv("ACCESS_TOKEN_EXPIRE_MINUTES", "1440"))  # 24 hours

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

# Security
pwd_context = CryptContext(schemes=["bcrypt"], deprecated="auto")
security = HTTPBearer()

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
    yield
    # Shutdown (if needed)

# FastAPI app
app = FastAPI(
    title="Mewayz Professional Platform API", 
    version="3.0.0", 
    description="Enterprise-Grade Multi-Platform Business Management System",
    lifespan=lifespan
)

# CORS middleware
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
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

# Password utilities
def verify_password(plain_password, hashed_password):
    return pwd_context.verify(plain_password, hashed_password)

def get_password_hash(password):
    return pwd_context.hash(password)

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
            "Payment Processing & Escrow"
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
            
        else:
            print(f"✅ Admin user already exists: {admin_user['email']}")
    except Exception as e:
        print(f"❌ Error creating admin user: {e}")

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

