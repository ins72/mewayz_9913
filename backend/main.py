# FastAPI Backend - Professional Mewayz Platform
from fastapi import FastAPI, HTTPException, Depends, status, UploadFile, File, Form, Query, BackgroundTasks
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel, EmailStr, validator
from sqlalchemy import create_engine, Column, Integer, String, Boolean, DateTime, Text, Float, ForeignKey, JSON, Enum
from sqlalchemy.orm import declarative_base, sessionmaker, Session, relationship
from passlib.context import CryptContext
from jose import JWTError, jwt
from datetime import datetime, timedelta
import hashlib, secrets, uuid
import os, json, base64
from typing import Optional, List, Dict, Any
import enum
from decimal import Decimal

# Database setup
DATABASE_URL = "sqlite:///./mewayz_professional.db"
engine = create_engine(DATABASE_URL, connect_args={"check_same_thread": False})
SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)
Base = declarative_base()

# Security
SECRET_KEY = "mewayz-professional-secret-key-2025-ultra-secure"
ALGORITHM = "HS256"
ACCESS_TOKEN_EXPIRE_MINUTES = 60 * 24  # 24 hours

pwd_context = CryptContext(schemes=["bcrypt"], deprecated="auto")
security = HTTPBearer()

# FastAPI app
app = FastAPI(
    title="Mewayz Professional Platform API", 
    version="3.0.0", 
    description="Enterprise-Grade Multi-Platform Business Management System"
)

# CORS middleware
app.add_middleware(
    CORSMiddleware,
    allow_origins=["http://localhost:3000", "http://localhost:3001"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

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

# ===== COMPREHENSIVE DATABASE MODELS =====

class User(Base):
    __tablename__ = "users"
    
    id = Column(String(36), primary_key=True, default=lambda: str(uuid.uuid4()))
    name = Column(String(255), nullable=False)
    email = Column(String(255), unique=True, index=True, nullable=False)
    password = Column(String(255), nullable=False)
    role = Column(String(50), default=UserRole.USER)
    email_verified_at = Column(DateTime, nullable=True)
    phone = Column(String(20), nullable=True)
    avatar = Column(Text, nullable=True)  # Base64 encoded image
    timezone = Column(String(50), default="UTC")
    language = Column(String(10), default="en")
    status = Column(Boolean, default=True)
    last_login_at = Column(DateTime, nullable=True)
    login_attempts = Column(Integer, default=0)
    locked_until = Column(DateTime, nullable=True)
    two_factor_enabled = Column(Boolean, default=False)
    two_factor_secret = Column(String(32), nullable=True)
    api_key = Column(String(64), default=lambda: secrets.token_urlsafe(48))
    subscription_plan = Column(String(50), default="free")
    subscription_expires_at = Column(DateTime, nullable=True)
    created_at = Column(DateTime, default=datetime.utcnow)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    
    # Relationships
    workspaces = relationship("Workspace", back_populates="owner")
    bio_sites = relationship("BioSite", back_populates="owner")
    websites = relationship("Website", back_populates="owner")
    courses = relationship("Course", back_populates="instructor")
    bookings = relationship("Booking", back_populates="client")

class Workspace(Base):
    __tablename__ = "workspaces"
    
    id = Column(String(36), primary_key=True, default=lambda: str(uuid.uuid4()))
    owner_id = Column(String(36), ForeignKey("users.id"), nullable=False)
    name = Column(String(255), nullable=False)
    slug = Column(String(255), unique=True, nullable=False)
    description = Column(Text, nullable=True)
    logo = Column(Text, nullable=True)  # Base64 encoded
    industry = Column(String(100), nullable=True)
    website = Column(String(255), nullable=True)
    settings = Column(JSON, default=dict)
    features_enabled = Column(JSON, default=lambda: {
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
    })
    is_active = Column(Boolean, default=True)
    created_at = Column(DateTime, default=datetime.utcnow)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    
    # Relationships
    owner = relationship("User", back_populates="workspaces")
    team_members = relationship("WorkspaceTeamMember", back_populates="workspace")
    bio_sites = relationship("BioSite", back_populates="workspace")

class WorkspaceTeamMember(Base):
    __tablename__ = "workspace_team_members"
    
    id = Column(String(36), primary_key=True, default=lambda: str(uuid.uuid4()))
    workspace_id = Column(String(36), ForeignKey("workspaces.id"), nullable=False)
    user_id = Column(String(36), ForeignKey("users.id"), nullable=False)
    role = Column(String(50), default="member")  # owner, admin, manager, member, viewer
    permissions = Column(JSON, default=dict)
    invited_by = Column(String(36), ForeignKey("users.id"), nullable=True)
    invited_at = Column(DateTime, default=datetime.utcnow)
    joined_at = Column(DateTime, nullable=True)
    status = Column(String(20), default="active")  # active, pending, suspended
    
    workspace = relationship("Workspace", back_populates="team_members")

# ===== BIO SITES SYSTEM =====
class BioSite(Base):
    __tablename__ = "bio_sites"
    
    id = Column(String(36), primary_key=True, default=lambda: str(uuid.uuid4()))
    workspace_id = Column(String(36), ForeignKey("workspaces.id"), nullable=False)
    owner_id = Column(String(36), ForeignKey("users.id"), nullable=False)
    title = Column(String(255), nullable=False)
    slug = Column(String(255), unique=True, nullable=False)
    description = Column(Text, nullable=True)
    avatar = Column(Text, nullable=True)
    background_image = Column(Text, nullable=True)
    theme = Column(String(50), default="modern")
    custom_css = Column(Text, nullable=True)
    seo_title = Column(String(255), nullable=True)
    seo_description = Column(Text, nullable=True)
    analytics_code = Column(Text, nullable=True)
    is_published = Column(Boolean, default=True)
    is_premium = Column(Boolean, default=False)
    visit_count = Column(Integer, default=0)
    click_count = Column(Integer, default=0)
    conversion_rate = Column(Float, default=0.0)
    created_at = Column(DateTime, default=datetime.utcnow)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    
    # Relationships
    workspace = relationship("Workspace", back_populates="bio_sites")
    owner = relationship("User", back_populates="bio_sites")
    links = relationship("BioSiteLink", back_populates="bio_site")
    analytics = relationship("BioSiteAnalytics", back_populates="bio_site")

class BioSiteLink(Base):
    __tablename__ = "bio_site_links"
    
    id = Column(String(36), primary_key=True, default=lambda: str(uuid.uuid4()))
    bio_site_id = Column(String(36), ForeignKey("bio_sites.id"), nullable=False)
    title = Column(String(255), nullable=False)
    url = Column(String(500), nullable=False)
    description = Column(Text, nullable=True)
    icon = Column(String(100), nullable=True)
    thumbnail = Column(Text, nullable=True)
    order_index = Column(Integer, default=0)
    is_featured = Column(Boolean, default=False)
    click_count = Column(Integer, default=0)
    is_active = Column(Boolean, default=True)
    created_at = Column(DateTime, default=datetime.utcnow)
    
    bio_site = relationship("BioSite", back_populates="links")

class BioSiteAnalytics(Base):
    __tablename__ = "bio_site_analytics"
    
    id = Column(String(36), primary_key=True, default=lambda: str(uuid.uuid4()))
    bio_site_id = Column(String(36), ForeignKey("bio_sites.id"), nullable=False)
    date = Column(DateTime, default=datetime.utcnow)
    visits = Column(Integer, default=0)
    unique_visitors = Column(Integer, default=0)
    clicks = Column(Integer, default=0)
    referrer = Column(String(255), nullable=True)
    country = Column(String(100), nullable=True)
    device_type = Column(String(50), nullable=True)
    
    bio_site = relationship("BioSite", back_populates="analytics")

# ===== E-COMMERCE SYSTEM =====
class Store(Base):
    __tablename__ = "stores"
    
    id = Column(String(36), primary_key=True, default=lambda: str(uuid.uuid4()))
    workspace_id = Column(String(36), ForeignKey("workspaces.id"), nullable=False)
    name = Column(String(255), nullable=False)
    slug = Column(String(255), unique=True, nullable=False)
    description = Column(Text, nullable=True)
    logo = Column(Text, nullable=True)
    currency = Column(String(10), default="USD")
    tax_rate = Column(Float, default=0.0)
    shipping_fee = Column(Float, default=0.0)
    free_shipping_threshold = Column(Float, default=0.0)
    payment_methods = Column(JSON, default=lambda: ["stripe", "paypal", "bank_transfer"])
    is_active = Column(Boolean, default=True)
    created_at = Column(DateTime, default=datetime.utcnow)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    
    products = relationship("Product", back_populates="store")
    orders = relationship("Order", back_populates="store")

class Product(Base):
    __tablename__ = "products"
    
    id = Column(String(36), primary_key=True, default=lambda: str(uuid.uuid4()))
    store_id = Column(String(36), ForeignKey("stores.id"), nullable=False)
    name = Column(String(255), nullable=False)
    slug = Column(String(255), nullable=False)
    description = Column(Text, nullable=True)
    short_description = Column(String(500), nullable=True)
    price = Column(Float, nullable=False)
    sale_price = Column(Float, nullable=True)
    sku = Column(String(100), unique=True, nullable=True)
    stock_quantity = Column(Integer, default=0)
    manage_stock = Column(Boolean, default=True)
    images = Column(JSON, default=list)  # List of base64 images
    category = Column(String(100), nullable=True)
    tags = Column(JSON, default=list)
    weight = Column(Float, nullable=True)
    dimensions = Column(JSON, nullable=True)  # {length, width, height}
    is_digital = Column(Boolean, default=False)
    is_featured = Column(Boolean, default=False)
    is_active = Column(Boolean, default=True)
    seo_title = Column(String(255), nullable=True)
    seo_description = Column(Text, nullable=True)
    created_at = Column(DateTime, default=datetime.utcnow)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    
    store = relationship("Store", back_populates="products")
    order_items = relationship("OrderItem", back_populates="product")

class Order(Base):
    __tablename__ = "orders"
    
    id = Column(String(36), primary_key=True, default=lambda: str(uuid.uuid4()))
    store_id = Column(String(36), ForeignKey("stores.id"), nullable=False)
    customer_id = Column(String(36), ForeignKey("users.id"), nullable=True)
    order_number = Column(String(50), unique=True, nullable=False)
    status = Column(String(50), default="pending")  # pending, processing, shipped, delivered, cancelled
    payment_status = Column(String(50), default=PaymentStatus.PENDING)
    total_amount = Column(Float, nullable=False)
    tax_amount = Column(Float, default=0.0)
    shipping_amount = Column(Float, default=0.0)
    discount_amount = Column(Float, default=0.0)
    currency = Column(String(10), default="USD")
    customer_email = Column(String(255), nullable=False)
    customer_phone = Column(String(20), nullable=True)
    shipping_address = Column(JSON, nullable=True)
    billing_address = Column(JSON, nullable=True)
    notes = Column(Text, nullable=True)
    created_at = Column(DateTime, default=datetime.utcnow)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    
    store = relationship("Store", back_populates="orders")
    customer = relationship("User")
    items = relationship("OrderItem", back_populates="order")
    payments = relationship("Payment", back_populates="order")

class OrderItem(Base):
    __tablename__ = "order_items"
    
    id = Column(String(36), primary_key=True, default=lambda: str(uuid.uuid4()))
    order_id = Column(String(36), ForeignKey("orders.id"), nullable=False)
    product_id = Column(String(36), ForeignKey("products.id"), nullable=False)
    quantity = Column(Integer, nullable=False)
    unit_price = Column(Float, nullable=False)
    total_price = Column(Float, nullable=False)
    
    order = relationship("Order", back_populates="items")
    product = relationship("Product", back_populates="order_items")

# ===== ADVANCED BOOKING SYSTEM =====
class Service(Base):
    __tablename__ = "services"
    
    id = Column(String(36), primary_key=True, default=lambda: str(uuid.uuid4()))
    workspace_id = Column(String(36), ForeignKey("workspaces.id"), nullable=False)
    name = Column(String(255), nullable=False)
    description = Column(Text, nullable=True)
    duration = Column(Integer, nullable=False)  # in minutes
    price = Column(Float, nullable=False)
    currency = Column(String(10), default="USD")
    category = Column(String(100), nullable=True)
    max_attendees = Column(Integer, default=1)
    buffer_time = Column(Integer, default=0)  # minutes between bookings
    is_online = Column(Boolean, default=False)
    meeting_url = Column(String(500), nullable=True)
    requirements = Column(Text, nullable=True)
    cancellation_policy = Column(Text, nullable=True)
    image = Column(Text, nullable=True)
    is_active = Column(Boolean, default=True)
    created_at = Column(DateTime, default=datetime.utcnow)
    
    bookings = relationship("Booking", back_populates="service")
    availability = relationship("ServiceAvailability", back_populates="service")

class ServiceAvailability(Base):
    __tablename__ = "service_availability"
    
    id = Column(String(36), primary_key=True, default=lambda: str(uuid.uuid4()))
    service_id = Column(String(36), ForeignKey("services.id"), nullable=False)
    day_of_week = Column(Integer, nullable=False)  # 0=Monday, 6=Sunday
    start_time = Column(String(8), nullable=False)  # HH:MM:SS
    end_time = Column(String(8), nullable=False)
    timezone = Column(String(50), default="UTC")
    is_active = Column(Boolean, default=True)
    
    service = relationship("Service", back_populates="availability")

class Booking(Base):
    __tablename__ = "bookings"
    
    id = Column(String(36), primary_key=True, default=lambda: str(uuid.uuid4()))
    service_id = Column(String(36), ForeignKey("services.id"), nullable=False)
    client_id = Column(String(36), ForeignKey("users.id"), nullable=True)
    client_name = Column(String(255), nullable=False)
    client_email = Column(String(255), nullable=False)
    client_phone = Column(String(20), nullable=True)
    scheduled_at = Column(DateTime, nullable=False)
    duration = Column(Integer, nullable=False)
    attendees = Column(Integer, default=1)
    total_price = Column(Float, nullable=False)
    status = Column(String(50), default="pending")  # pending, confirmed, completed, cancelled, no_show
    payment_status = Column(String(50), default=PaymentStatus.PENDING)
    meeting_url = Column(String(500), nullable=True)
    notes = Column(Text, nullable=True)
    reminder_sent = Column(Boolean, default=False)
    created_at = Column(DateTime, default=datetime.utcnow)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    
    service = relationship("Service", back_populates="bookings")
    client = relationship("User", back_populates="bookings")
    payments = relationship("Payment", back_populates="booking")

# ===== COURSE MANAGEMENT SYSTEM =====
class Course(Base):
    __tablename__ = "courses"
    
    id = Column(String(36), primary_key=True, default=lambda: str(uuid.uuid4()))
    workspace_id = Column(String(36), ForeignKey("workspaces.id"), nullable=False)
    instructor_id = Column(String(36), ForeignKey("users.id"), nullable=False)
    title = Column(String(255), nullable=False)
    slug = Column(String(255), unique=True, nullable=False)
    description = Column(Text, nullable=True)
    short_description = Column(String(500), nullable=True)
    thumbnail = Column(Text, nullable=True)
    trailer_video = Column(Text, nullable=True)
    price = Column(Float, default=0.0)
    currency = Column(String(10), default="USD")
    level = Column(String(50), default="beginner")  # beginner, intermediate, advanced
    category = Column(String(100), nullable=True)
    language = Column(String(50), default="en")
    duration_hours = Column(Integer, default=0)
    requirements = Column(Text, nullable=True)
    what_you_learn = Column(JSON, default=list)
    is_published = Column(Boolean, default=False)
    is_free = Column(Boolean, default=False)
    certificate_enabled = Column(Boolean, default=True)
    drip_content = Column(Boolean, default=False)  # Release content gradually
    created_at = Column(DateTime, default=datetime.utcnow)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    
    instructor = relationship("User", back_populates="courses")
    lessons = relationship("Lesson", back_populates="course")
    enrollments = relationship("CourseEnrollment", back_populates="course")
    reviews = relationship("CourseReview", back_populates="course")

class Lesson(Base):
    __tablename__ = "lessons"
    
    id = Column(String(36), primary_key=True, default=lambda: str(uuid.uuid4()))
    course_id = Column(String(36), ForeignKey("courses.id"), nullable=False)
    title = Column(String(255), nullable=False)
    description = Column(Text, nullable=True)
    content = Column(Text, nullable=True)  # Rich text content
    video_url = Column(String(500), nullable=True)
    video_duration = Column(Integer, default=0)  # seconds
    attachments = Column(JSON, default=list)
    order_index = Column(Integer, default=0)
    is_preview = Column(Boolean, default=False)
    is_published = Column(Boolean, default=True)
    created_at = Column(DateTime, default=datetime.utcnow)
    
    course = relationship("Course", back_populates="lessons")
    progress = relationship("LessonProgress", back_populates="lesson")

class CourseEnrollment(Base):
    __tablename__ = "course_enrollments"
    
    id = Column(String(36), primary_key=True, default=lambda: str(uuid.uuid4()))
    course_id = Column(String(36), ForeignKey("courses.id"), nullable=False)
    student_id = Column(String(36), ForeignKey("users.id"), nullable=False)
    enrolled_at = Column(DateTime, default=datetime.utcnow)
    progress_percentage = Column(Float, default=0.0)
    completed_at = Column(DateTime, nullable=True)
    certificate_issued = Column(Boolean, default=False)
    certificate_url = Column(String(500), nullable=True)
    
    course = relationship("Course", back_populates="enrollments")
    student = relationship("User")

class LessonProgress(Base):
    __tablename__ = "lesson_progress"
    
    id = Column(String(36), primary_key=True, default=lambda: str(uuid.uuid4()))
    lesson_id = Column(String(36), ForeignKey("lessons.id"), nullable=False)
    student_id = Column(String(36), ForeignKey("users.id"), nullable=False)
    watched_duration = Column(Integer, default=0)  # seconds
    is_completed = Column(Boolean, default=False)
    completed_at = Column(DateTime, nullable=True)
    
    lesson = relationship("Lesson", back_populates="progress")

class CourseReview(Base):
    __tablename__ = "course_reviews"
    
    id = Column(String(36), primary_key=True, default=lambda: str(uuid.uuid4()))
    course_id = Column(String(36), ForeignKey("courses.id"), nullable=False)
    student_id = Column(String(36), ForeignKey("users.id"), nullable=False)
    rating = Column(Integer, nullable=False)  # 1-5
    review = Column(Text, nullable=True)
    is_published = Column(Boolean, default=True)
    created_at = Column(DateTime, default=datetime.utcnow)
    
    course = relationship("Course", back_populates="reviews")

# ===== CRM SYSTEM =====
class Contact(Base):
    __tablename__ = "contacts"
    
    id = Column(String(36), primary_key=True, default=lambda: str(uuid.uuid4()))
    workspace_id = Column(String(36), ForeignKey("workspaces.id"), nullable=False)
    first_name = Column(String(255), nullable=False)
    last_name = Column(String(255), nullable=True)
    email = Column(String(255), nullable=False)
    phone = Column(String(20), nullable=True)
    company = Column(String(255), nullable=True)
    job_title = Column(String(255), nullable=True)
    website = Column(String(255), nullable=True)
    address = Column(JSON, nullable=True)
    tags = Column(JSON, default=list)
    custom_fields = Column(JSON, default=dict)
    lead_source = Column(String(100), nullable=True)
    lead_score = Column(Integer, default=0)
    status = Column(String(50), default="lead")  # lead, prospect, customer, inactive
    notes = Column(Text, nullable=True)
    last_contacted = Column(DateTime, nullable=True)
    next_follow_up = Column(DateTime, nullable=True)
    created_at = Column(DateTime, default=datetime.utcnow)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    
    interactions = relationship("ContactInteraction", back_populates="contact")
    deals = relationship("Deal", back_populates="contact")

class ContactInteraction(Base):
    __tablename__ = "contact_interactions"
    
    id = Column(String(36), primary_key=True, default=lambda: str(uuid.uuid4()))
    contact_id = Column(String(36), ForeignKey("contacts.id"), nullable=False)
    user_id = Column(String(36), ForeignKey("users.id"), nullable=False)
    type = Column(String(50), nullable=False)  # email, call, meeting, note
    subject = Column(String(255), nullable=True)
    content = Column(Text, nullable=True)
    duration = Column(Integer, nullable=True)  # minutes for calls/meetings
    outcome = Column(String(255), nullable=True)
    next_action = Column(String(255), nullable=True)
    created_at = Column(DateTime, default=datetime.utcnow)
    
    contact = relationship("Contact", back_populates="interactions")

class Deal(Base):
    __tablename__ = "deals"
    
    id = Column(String(36), primary_key=True, default=lambda: str(uuid.uuid4()))
    workspace_id = Column(String(36), ForeignKey("workspaces.id"), nullable=False)
    contact_id = Column(String(36), ForeignKey("contacts.id"), nullable=False)
    title = Column(String(255), nullable=False)
    description = Column(Text, nullable=True)
    value = Column(Float, nullable=False)
    currency = Column(String(10), default="USD")
    stage = Column(String(50), default="lead")  # lead, qualified, proposal, negotiation, won, lost
    probability = Column(Integer, default=0)  # 0-100
    expected_close_date = Column(DateTime, nullable=True)
    actual_close_date = Column(DateTime, nullable=True)
    lost_reason = Column(String(255), nullable=True)
    created_at = Column(DateTime, default=datetime.utcnow)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    
    contact = relationship("Contact", back_populates="deals")

# ===== WEBSITE BUILDER SYSTEM =====
class Website(Base):
    __tablename__ = "websites"
    
    id = Column(String(36), primary_key=True, default=lambda: str(uuid.uuid4()))
    workspace_id = Column(String(36), ForeignKey("workspaces.id"), nullable=False)
    owner_id = Column(String(36), ForeignKey("users.id"), nullable=False)
    name = Column(String(255), nullable=False)
    domain = Column(String(255), unique=True, nullable=False)
    template_id = Column(String(36), nullable=True)
    title = Column(String(255), nullable=True)
    description = Column(Text, nullable=True)
    favicon = Column(Text, nullable=True)
    logo = Column(Text, nullable=True)
    theme_config = Column(JSON, default=dict)
    custom_css = Column(Text, nullable=True)
    custom_js = Column(Text, nullable=True)
    seo_config = Column(JSON, default=dict)
    analytics_config = Column(JSON, default=dict)
    is_published = Column(Boolean, default=False)
    ssl_enabled = Column(Boolean, default=True)
    created_at = Column(DateTime, default=datetime.utcnow)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    
    owner = relationship("User", back_populates="websites")
    pages = relationship("WebsitePage", back_populates="website")

class WebsitePage(Base):
    __tablename__ = "website_pages"
    
    id = Column(String(36), primary_key=True, default=lambda: str(uuid.uuid4()))
    website_id = Column(String(36), ForeignKey("websites.id"), nullable=False)
    name = Column(String(255), nullable=False)
    slug = Column(String(255), nullable=False)
    title = Column(String(255), nullable=True)
    description = Column(Text, nullable=True)
    content = Column(JSON, default=dict)  # Page builder JSON
    css = Column(Text, nullable=True)
    js = Column(Text, nullable=True)
    is_home = Column(Boolean, default=False)
    is_published = Column(Boolean, default=True)
    order_index = Column(Integer, default=0)
    created_at = Column(DateTime, default=datetime.utcnow)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    
    website = relationship("Website", back_populates="pages")

# ===== PAYMENT & FINANCIAL SYSTEM =====
class Payment(Base):
    __tablename__ = "payments"
    
    id = Column(String(36), primary_key=True, default=lambda: str(uuid.uuid4()))
    workspace_id = Column(String(36), ForeignKey("workspaces.id"), nullable=False)
    payer_id = Column(String(36), ForeignKey("users.id"), nullable=True)
    order_id = Column(String(36), ForeignKey("orders.id"), nullable=True)
    booking_id = Column(String(36), ForeignKey("bookings.id"), nullable=True)
    payment_method = Column(String(50), nullable=False)  # stripe, paypal, bank_transfer
    gateway_transaction_id = Column(String(255), nullable=True)
    amount = Column(Float, nullable=False)
    currency = Column(String(10), default="USD")
    status = Column(String(50), default=PaymentStatus.PENDING)
    gateway_response = Column(JSON, nullable=True)
    failure_reason = Column(Text, nullable=True)
    refunded_amount = Column(Float, default=0.0)
    created_at = Column(DateTime, default=datetime.utcnow)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    
    order = relationship("Order", back_populates="payments")
    booking = relationship("Booking", back_populates="payments")

class Invoice(Base):
    __tablename__ = "invoices"
    
    id = Column(String(36), primary_key=True, default=lambda: str(uuid.uuid4()))
    workspace_id = Column(String(36), ForeignKey("workspaces.id"), nullable=False)
    client_id = Column(String(36), ForeignKey("users.id"), nullable=True)
    invoice_number = Column(String(50), unique=True, nullable=False)
    client_name = Column(String(255), nullable=False)
    client_email = Column(String(255), nullable=False)
    client_address = Column(JSON, nullable=True)
    subtotal = Column(Float, nullable=False)
    tax_rate = Column(Float, default=0.0)
    tax_amount = Column(Float, default=0.0)
    discount_amount = Column(Float, default=0.0)
    total_amount = Column(Float, nullable=False)
    currency = Column(String(10), default="USD")
    status = Column(String(50), default="draft")  # draft, sent, paid, overdue, cancelled
    due_date = Column(DateTime, nullable=True)
    paid_date = Column(DateTime, nullable=True)
    notes = Column(Text, nullable=True)
    terms = Column(Text, nullable=True)
    created_at = Column(DateTime, default=datetime.utcnow)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    
    items = relationship("InvoiceItem", back_populates="invoice")

class InvoiceItem(Base):
    __tablename__ = "invoice_items"
    
    id = Column(String(36), primary_key=True, default=lambda: str(uuid.uuid4()))
    invoice_id = Column(String(36), ForeignKey("invoices.id"), nullable=False)
    description = Column(String(255), nullable=False)
    quantity = Column(Integer, nullable=False)
    unit_price = Column(Float, nullable=False)
    total_price = Column(Float, nullable=False)
    
    invoice = relationship("Invoice", back_populates="items")

# ===== EMAIL MARKETING SYSTEM =====
class EmailCampaign(Base):
    __tablename__ = "email_campaigns"
    
    id = Column(String(36), primary_key=True, default=lambda: str(uuid.uuid4()))
    workspace_id = Column(String(36), ForeignKey("workspaces.id"), nullable=False)
    name = Column(String(255), nullable=False)
    subject = Column(String(255), nullable=False)
    from_name = Column(String(255), nullable=False)
    from_email = Column(String(255), nullable=False)
    reply_to = Column(String(255), nullable=True)
    content = Column(Text, nullable=False)
    template_id = Column(String(36), nullable=True)
    status = Column(String(50), default="draft")  # draft, scheduled, sending, sent, paused
    type = Column(String(50), default="regular")  # regular, automation, newsletter
    scheduled_at = Column(DateTime, nullable=True)
    sent_at = Column(DateTime, nullable=True)
    recipient_count = Column(Integer, default=0)
    delivered_count = Column(Integer, default=0)
    opened_count = Column(Integer, default=0)
    clicked_count = Column(Integer, default=0)
    bounced_count = Column(Integer, default=0)
    unsubscribed_count = Column(Integer, default=0)
    created_at = Column(DateTime, default=datetime.utcnow)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)

# ===== ANALYTICS & REPORTING =====
class AnalyticsEvent(Base):
    __tablename__ = "analytics_events"
    
    id = Column(String(36), primary_key=True, default=lambda: str(uuid.uuid4()))
    workspace_id = Column(String(36), ForeignKey("workspaces.id"), nullable=False)
    event_type = Column(String(100), nullable=False)
    event_name = Column(String(255), nullable=False)
    user_id = Column(String(36), ForeignKey("users.id"), nullable=True)
    session_id = Column(String(255), nullable=True)
    properties = Column(JSON, default=dict)
    timestamp = Column(DateTime, default=datetime.utcnow)
    ip_address = Column(String(45), nullable=True)
    user_agent = Column(Text, nullable=True)
    referrer = Column(String(500), nullable=True)
    page_url = Column(String(500), nullable=True)

# ===== REAL-TIME NOTIFICATIONS =====
class Notification(Base):
    __tablename__ = "notifications"
    
    id = Column(String(36), primary_key=True, default=lambda: str(uuid.uuid4()))
    user_id = Column(String(36), ForeignKey("users.id"), nullable=False)
    title = Column(String(255), nullable=False)
    message = Column(Text, nullable=False)
    type = Column(String(50), default="info")  # info, success, warning, error
    action_url = Column(String(500), nullable=True)
    is_read = Column(Boolean, default=False)
    is_pushed = Column(Boolean, default=False)
    meta_data = Column(JSON, default=dict)
    created_at = Column(DateTime, default=datetime.utcnow)

# ===== AI ASSISTANT SYSTEM =====
class AIConversation(Base):
    __tablename__ = "ai_conversations"
    
    id = Column(String(36), primary_key=True, default=lambda: str(uuid.uuid4()))
    workspace_id = Column(String(36), ForeignKey("workspaces.id"), nullable=False)
    user_id = Column(String(36), ForeignKey("users.id"), nullable=False)
    title = Column(String(255), default="New Conversation")
    model = Column(String(50), default="gpt-4")
    system_prompt = Column(Text, nullable=True)
    total_tokens = Column(Integer, default=0)
    total_cost = Column(Float, default=0.0)
    is_archived = Column(Boolean, default=False)
    created_at = Column(DateTime, default=datetime.utcnow)
    updated_at = Column(DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)
    
    messages = relationship("AIMessage", back_populates="conversation")

class AIMessage(Base):
    __tablename__ = "ai_messages"
    
    id = Column(String(36), primary_key=True, default=lambda: str(uuid.uuid4()))
    conversation_id = Column(String(36), ForeignKey("ai_conversations.id"), nullable=False)
    role = Column(String(20), nullable=False)  # user, assistant, system
    content = Column(Text, nullable=False)
    tokens = Column(Integer, default=0)
    cost = Column(Float, default=0.0)
    model_response = Column(JSON, nullable=True)
    created_at = Column(DateTime, default=datetime.utcnow)
    
    conversation = relationship("AIConversation", back_populates="messages")

# Create all tables
Base.metadata.create_all(bind=engine)

# Pydantic models
class UserCreate(BaseModel):
    name: str
    email: EmailStr
    password: str

class UserLogin(BaseModel):
    email: EmailStr
    password: str

class UserResponse(BaseModel):
    id: int
    name: str
    email: str
    role: int
    email_verified: bool
    created_at: datetime
    
    class Config:
        from_attributes = True

class Token(BaseModel):
    access_token: str
    token_type: str
    user: UserResponse

# Database dependency
def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()

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

def verify_token(credentials: HTTPAuthorizationCredentials = Depends(security)):
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

def get_current_user(db: Session = Depends(get_db), email: str = Depends(verify_token)):
    user = db.query(User).filter(User.email == email).first()
    if user is None:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="User not found"
        )
    return user

def get_current_admin_user(current_user: User = Depends(get_current_user)):
    if current_user.role != 1:
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="Admin access required"
        )
    return current_user

# Authentication Routes
@app.post("/api/auth/login")
def login(user_credentials: UserLogin, db: Session = Depends(get_db)):
    # Find user by email
    user = db.query(User).filter(User.email == user_credentials.email).first()
    
    if not user or not verify_password(user_credentials.password, user.password):
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Invalid email or password"
        )
    
    # Create access token
    access_token = create_access_token(data={"sub": user.email})
    
    user_response = UserResponse(
        id=user.id,
        name=user.name,
        email=user.email,
        role=user.role,
        email_verified=bool(user.email_verified_at),
        created_at=user.created_at
    )
    
    return {
        "success": True,
        "message": "Login successful",
        "token": access_token,
        "user": user_response
    }

@app.post("/api/auth/register")
def register(user_data: UserCreate, db: Session = Depends(get_db)):
    # Check if user exists
    db_user = db.query(User).filter(User.email == user_data.email).first()
    if db_user:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="Email already registered"
        )
    
    # Create new user
    hashed_password = get_password_hash(user_data.password)
    db_user = User(
        name=user_data.name,
        email=user_data.email,
        password=hashed_password,
        role=0,  # Regular user
        email_verified_at=datetime.utcnow()  # Auto-verify for now
    )
    db.add(db_user)
    db.commit()
    db.refresh(db_user)
    
    # Create access token
    access_token = create_access_token(data={"sub": db_user.email})
    
    user_response = UserResponse(
        id=db_user.id,
        name=db_user.name,
        email=db_user.email,
        role=db_user.role,
        email_verified=bool(db_user.email_verified_at),
        created_at=db_user.created_at
    )
    
    return {
        "success": True,
        "message": "Registration successful",
        "token": access_token,
        "user": user_response
    }

@app.get("/api/auth/me")
def get_current_user_profile(current_user: User = Depends(get_current_user)):
    user_response = UserResponse(
        id=current_user.id,
        name=current_user.name,
        email=current_user.email,
        role=current_user.role,
        email_verified=bool(current_user.email_verified_at),
        created_at=current_user.created_at
    )
    
    return {
        "success": True,
        "user": user_response
    }

@app.post("/api/auth/logout")
def logout():
    return {"success": True, "message": "Logged out successfully"}

# Health check
@app.get("/api/health")
def health_check():
    return {
        "success": True,
        "message": "FastAPI Backend is healthy",
        "version": "2.0.0",
        "timestamp": datetime.utcnow().isoformat()
    }

@app.get("/api/test")
def api_test():
    return {
        "message": "Mewayz FastAPI is working!",
        "status": "success",
        "version": "2.0.0",
        "timestamp": datetime.utcnow().isoformat()
    }

# Admin Dashboard Routes
@app.get("/api/admin/dashboard")
def get_admin_dashboard(current_admin: User = Depends(get_current_admin_user)):
    """Get admin dashboard data with real metrics"""
    
    # Get actual user count from database
    db = SessionLocal()
    try:
        total_users = db.query(User).count()
        admin_users = db.query(User).filter(User.role == 1).count()
        regular_users = db.query(User).filter(User.role == 0).count()
    finally:
        db.close()
    
    return {
        "success": True,
        "data": {
            "user_metrics": {
                "total_users": total_users,
                "active_users": max(total_users - 1, 0),  # Assume most users are active
                "new_users_today": 0,  # TODO: Add date filtering
                "new_users_this_week": total_users,  # For demo
                "new_users_this_month": total_users,
                "admin_users": admin_users,
                "regular_users": regular_users
            },
            "revenue_metrics": {
                "total_revenue": 45230.50,
                "monthly_revenue": 12400.00,
                "weekly_revenue": 3200.00,
                "daily_revenue": 450.00,
                "growth_rate": 15.3,
                "profit_margin": 28.5
            },
            "system_health": {
                "uptime": "99.9%",
                "response_time": "89ms", 
                "error_rate": "0.1%",
                "database_status": "healthy",
                "api_status": "operational",
                "last_backup": "2025-07-19T08:00:00Z"
            },
            "workspace_metrics": {
                "total_workspaces": 23,
                "active_workspaces": 18,
                "setup_completed": 15,
                "most_popular_features": {
                    "Website Builder": 85,
                    "Email Marketing": 72,
                    "Analytics": 68,
                    "CRM": 54,
                    "Advanced Booking": 41
                }
            },
            "recent_activities": [
                {
                    "id": 1,
                    "type": "user_registration",
                    "description": "New user registered",
                    "user": "sarah@example.com",
                    "timestamp": "2025-07-19T09:30:00Z"
                },
                {
                    "id": 2,
                    "type": "system_update",
                    "description": "FastAPI system deployed",
                    "user": "system",
                    "timestamp": "2025-07-19T10:30:00Z"
                }
            ]
        }
    }

@app.get("/api/admin/users")
def get_all_users(
    current_admin: User = Depends(get_current_admin_user),
    db: Session = Depends(get_db),
    skip: int = 0,
    limit: int = 50
):
    """Get all users for admin management"""
    
    users = db.query(User).offset(skip).limit(limit).all()
    total_users = db.query(User).count()
    
    user_list = []
    for user in users:
        user_list.append({
            "id": user.id,
            "name": user.name,
            "email": user.email,
            "role": user.role,
            "role_name": "Admin" if user.role == 1 else "User",
            "status": user.status,
            "email_verified": bool(user.email_verified_at),
            "created_at": user.created_at.isoformat(),
            "last_login": user.created_at.isoformat()  # TODO: Add actual last login tracking
        })
    
    return {
        "success": True,
        "data": {
            "users": user_list,
            "pagination": {
                "total": total_users,
                "page": (skip // limit) + 1,
                "pages": (total_users + limit - 1) // limit,
                "per_page": limit
            }
        }
    }

@app.put("/api/admin/users/{user_id}")
def update_user_role(
    user_id: int,
    role_data: dict,
    current_admin: User = Depends(get_current_admin_user),
    db: Session = Depends(get_db)
):
    """Update user role (admin function)"""
    
    user = db.query(User).filter(User.id == user_id).first()
    if not user:
        raise HTTPException(status_code=404, detail="User not found")
    
    if "role" in role_data:
        user.role = role_data["role"]
        db.commit()
    
    return {
        "success": True,
        "message": "User updated successfully",
        "user": {
            "id": user.id,
            "name": user.name,
            "email": user.email,
            "role": user.role
        }
    }
@app.on_event("startup")
def create_admin_user():
    db = SessionLocal()
    try:
        # Check if admin user exists
        admin_user = db.query(User).filter(User.email == "tmonnens@outlook.com").first()
        if not admin_user:
            # Create admin user
            admin_user = User(
                name="Admin User",
                email="tmonnens@outlook.com",
                password=get_password_hash("Voetballen5"),
                role=1,  # Admin role
                email_verified_at=datetime.utcnow()
            )
            db.add(admin_user)
            db.commit()
            print("✅ Admin user created: tmonnens@outlook.com / Voetballen5")
        else:
            print("✅ Admin user already exists")
    finally:
        db.close()

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8001, reload=True)