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
from api import auth, users, analytics, dashboard, workspaces, blog, admin, ai, bio_sites, ecommerce, bookings, social_media, marketing, integrations, business_intelligence, survey_system, media_library, i18n_system, notification_system
from api import subscription_management, google_oauth, financial_management, link_shortener, analytics_system, team_management, form_builder, promotions_referrals, ai_token_management, course_management, crm_management, website_builder, email_marketing, advanced_analytics, escrow_system, onboarding_system, template_marketplace, ai_content_generation, social_email_integration, advanced_financial_analytics, enhanced_ecommerce, automation_system, advanced_ai_suite, support_system, content_creation_suite, customer_experience_suite, social_media_suite

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

# NINTH WAVE - SOCIAL EMAIL INTEGRATION & ADVANCED FINANCIAL ANALYTICS - Newly Migrated
app.include_router(social_email_integration.router, prefix="/api/social-email", tags=["Social Email Integration"])
app.include_router(advanced_financial_analytics.router, prefix="/api/advanced-financial", tags=["Advanced Financial Analytics"])
app.include_router(enhanced_ecommerce.router, prefix="/api/enhanced-ecommerce", tags=["Enhanced E-commerce"])

# TENTH WAVE - AUTOMATION, ADVANCED AI & SUPPORT SYSTEMS - Newly Migrated
app.include_router(automation_system.router, prefix="/api/automation", tags=["Automation System"])
app.include_router(advanced_ai_suite.router, prefix="/api/advanced-ai", tags=["Advanced AI Suite"])
app.include_router(support_system.router, prefix="/api/support", tags=["Support System"])

# ELEVENTH WAVE - CONTENT CREATION, CUSTOMER EXPERIENCE & SOCIAL MEDIA - Newly Migrated
app.include_router(content_creation_suite.router, prefix="/api/content-creation", tags=["Content Creation Suite"])
app.include_router(customer_experience_suite.router, prefix="/api/customer-experience", tags=["Customer Experience Suite"])
app.include_router(social_media_suite.router, prefix="/api/social-media", tags=["Social Media Suite"])

# TWELFTH WAVE - SURVEY & FEEDBACK SYSTEM - Newly Implemented
app.include_router(survey_system.router, prefix="/api/surveys", tags=["Survey & Feedback System"])

# TWELFTH WAVE - MEDIA LIBRARY & FILE MANAGEMENT - Newly Implemented  
app.include_router(media_library.router, prefix="/api/media", tags=["Media Library & File Management"])

# THIRTEENTH WAVE - INTERNATIONALIZATION & LOCALIZATION - Newly Implemented
app.include_router(i18n_system.router, prefix="/api/i18n", tags=["Internationalization & Localization"])

if __name__ == "__main__":
    uvicorn.run(
        "main:app",
        host="0.0.0.0",
        port=8001,
        reload=settings.DEBUG,
        log_level="info" if not settings.DEBUG else "debug"
    )