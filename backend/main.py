"""
Professional FastAPI Application
Mewayz Platform - Restructured Architecture
"""
from fastapi import FastAPI, HTTPException, Depends, status
from fastapi.middleware.cors import CORSMiddleware
from contextlib import asynccontextmanager
import uvicorn

from core.config import settings
from core.database import connect_to_mongo, close_mongo_connection
from api import auth, users, analytics, dashboard, workspaces, blog, admin, ai, bio_sites, ecommerce, bookings, social_media, marketing, integrations, business_intelligence
from api import subscription_management, google_oauth, financial_management, link_shortener, analytics_system, team_management, form_builder, promotions_referrals, ai_token_management, course_management, crm_management, website_builder, email_marketing, advanced_analytics, escrow_system, onboarding_system, template_marketplace, ai_content_generation, social_email_integration, advanced_financial_analytics

# Application lifespan management
@asynccontextmanager
async def lifespan(app: FastAPI):
    # Startup
    await connect_to_mongo()
    yield
    # Shutdown
    await close_mongo_connection()

# Create FastAPI application
app = FastAPI(
    title=settings.APP_NAME,
    version=settings.VERSION,
    docs_url="/docs" if settings.DEBUG else None,
    redoc_url="/redoc" if settings.DEBUG else None,
    lifespan=lifespan
)

# CORS Middleware
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # Configure appropriately for production
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Health check endpoint
@app.get("/health")
async def health_check():
    """Health check endpoint"""
    return {
        "status": "healthy",
        "app_name": settings.APP_NAME,
        "version": settings.VERSION,
        "debug": settings.DEBUG
    }

# Root endpoint
@app.get("/")
async def root():
    """Root endpoint"""
    return {
        "message": f"Welcome to {settings.APP_NAME}",
        "version": settings.VERSION,
        "docs_url": "/docs" if settings.DEBUG else "Contact admin for API documentation"
    }

# Include routers with real functionality
app.include_router(auth.router, prefix="/api/auth", tags=["Authentication"])
app.include_router(users.router, prefix="/api/users", tags=["Users"])
app.include_router(analytics.router, prefix="/api/analytics", tags=["Analytics"])
app.include_router(dashboard.router, prefix="/api/dashboard", tags=["Dashboard"])
app.include_router(workspaces.router, prefix="/api/workspaces", tags=["Workspaces"])
app.include_router(blog.router, prefix="/api/blog", tags=["Blog & Content"])
app.include_router(admin.router, prefix="/api/admin", tags=["Administration"])
app.include_router(ai.router, prefix="/api/ai", tags=["AI Services"])
app.include_router(bio_sites.router, prefix="/api/bio-sites", tags=["Bio Sites"])
app.include_router(ecommerce.router, prefix="/api/ecommerce", tags=["E-commerce"])
app.include_router(bookings.router, prefix="/api/bookings", tags=["Booking System"])
app.include_router(social_media.router, prefix="/api/social-media", tags=["Social Media"])
app.include_router(marketing.router, prefix="/api/marketing", tags=["Marketing & Email"])
app.include_router(integrations.router, prefix="/api/integrations", tags=["Integrations"])
app.include_router(business_intelligence.router, prefix="/api/business-intelligence", tags=["Business Intelligence"])

# FIRST WAVE - HIGH-VALUE FEATURES - Migrated from Monolithic Structure
app.include_router(subscription_management.router, prefix="/api/subscriptions", tags=["Subscription Management"])
app.include_router(google_oauth.router, prefix="/api/oauth", tags=["OAuth Integration"])
app.include_router(financial_management.router, prefix="/api/financial", tags=["Financial Management"])
app.include_router(link_shortener.router, prefix="/api/links", tags=["Link Shortener"])
app.include_router(analytics_system.router, prefix="/api/analytics-system", tags=["Analytics System"])

# SECOND WAVE - BUSINESS COLLABORATION FEATURES - Newly Migrated
app.include_router(team_management.router, prefix="/api/team", tags=["Team Management"])
app.include_router(form_builder.router, prefix="/api/forms", tags=["Form Builder"])

# THIRD WAVE - MARKETING & PROMOTIONAL FEATURES - Newly Migrated  
app.include_router(promotions_referrals.router, prefix="/api/promotions", tags=["Promotions & Referrals"])

# FOURTH WAVE - ADVANCED BUSINESS SYSTEMS - Newly Migrated
app.include_router(ai_token_management.router, prefix="/api/tokens", tags=["AI Token Management"])
app.include_router(course_management.router, prefix="/api/courses", tags=["Course & Learning Management"])

# FIFTH WAVE - CRM & WEBSITE BUILDER SYSTEMS - Newly Migrated
app.include_router(crm_management.router, prefix="/api/crm", tags=["CRM Management"])
app.include_router(website_builder.router, prefix="/api/website-builder", tags=["Website Builder"])

# SIXTH WAVE - EMAIL MARKETING & ADVANCED ANALYTICS - Newly Migrated  
app.include_router(email_marketing.router, prefix="/api/email-marketing", tags=["Email Marketing"])
app.include_router(advanced_analytics.router, prefix="/api/advanced-analytics", tags=["Advanced Analytics"])

# SEVENTH WAVE - ESCROW & ONBOARDING SYSTEMS - Newly Migrated
app.include_router(escrow_system.router, prefix="/api/escrow", tags=["Escrow System"])
app.include_router(onboarding_system.router, prefix="/api/onboarding", tags=["Onboarding System"])

# EIGHTH WAVE - TEMPLATE MARKETPLACE & AI CONTENT - Newly Migrated
app.include_router(template_marketplace.router, prefix="/api/templates", tags=["Template Marketplace"])
app.include_router(ai_content_generation.router, prefix="/api/ai-content", tags=["AI Content Generation"])

if __name__ == "__main__":
    uvicorn.run(
        "main:app",
        host="0.0.0.0",
        port=8001,
        reload=settings.DEBUG,
        log_level="info" if not settings.DEBUG else "debug"
    )