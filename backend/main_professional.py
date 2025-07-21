"""
Professional FastAPI Application - Mewayz Platform
Enterprise-Grade Multi-Tenant Business Solution

Version: 3.0.0
Architecture: Modular FastAPI + React + MongoDB
Systems Implemented: 50 Feature Systems
"""

from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from contextlib import asynccontextmanager
import uvicorn

# Core imports
from core.config import settings
from core.database import connect_to_mongo, close_mongo_connection

# API module imports - Core Business Systems
from api import (
    # Authentication & User Management
    auth, users, admin,
    
    # Business Intelligence & Analytics  
    analytics, dashboard, business_intelligence, analytics_system, advanced_analytics,
    
    # Content & Workspace Management
    workspaces, blog, content_creation_suite,
    
    # AI & Automation Services
    ai, ai_token_management, ai_content_generation, advanced_ai_suite, automation_system,
    
    # E-commerce & Financial Systems
    ecommerce, enhanced_ecommerce, financial_management, advanced_financial_analytics,
    escrow_system, subscription_management,
    
    # Customer Engagement & Experience
    bio_sites, social_media, social_media_suite, marketing, email_marketing,
    customer_experience_suite, social_email_integration, notifications_system as notification_system,
    
    # Business Operations
    bookings, crm_management, team_management, course_management, support_system,
    
    # Integration & Communication Systems  
    integrations, google_oauth, webhook_system, i18n_system,
    
    # Development & Management Tools
    website_builder, form_builder, template_marketplace, media_library,
    
    # Marketing & Growth
    promotions_referrals, link_shortener, survey_system,
    
    # Enterprise & Compliance Systems
    onboarding_system, rate_limiting_system, monitoring_system, 
    backup_system, compliance_system
)


# Application Lifecycle Management
@asynccontextmanager
async def lifespan(app: FastAPI):
    """Manage application startup and shutdown"""
    # Startup
    await connect_to_mongo()
    print(f"🚀 {settings.APP_NAME} v{settings.VERSION} started successfully")
    yield
    # Shutdown  
    await close_mongo_connection()
    print(f"🛑 {settings.APP_NAME} shutdown completed")


# FastAPI Application Instance
app = FastAPI(
    title=settings.APP_NAME,
    description="Enterprise-Grade Multi-Tenant Business Solution",
    version=settings.VERSION,
    docs_url="/api/docs" if settings.DEBUG else None,
    redoc_url="/api/redoc" if settings.DEBUG else None,
    openapi_url="/api/openapi.json" if settings.DEBUG else None,
    lifespan=lifespan,
    contact={
        "name": "Mewayz Platform Team",
        "url": "https://mewayz.com",
        "email": "support@mewayz.com",
    },
    license_info={
        "name": "Proprietary License",
        "url": "https://mewayz.com/license",
    },
)

# CORS Configuration
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"] if settings.DEBUG else settings.ALLOWED_ORIGINS,
    allow_credentials=True,
    allow_methods=["GET", "POST", "PUT", "DELETE", "PATCH", "OPTIONS"],
    allow_headers=["*"],
)

# Health Check Endpoint
@app.get("/health", tags=["System"])
async def health_check():
    """System health check endpoint"""
    return {
        "status": "healthy",
        "app_name": settings.APP_NAME,
        "version": settings.VERSION,
        "environment": "development" if settings.DEBUG else "production",
        "systems_count": 50
    }

# Root API Information
@app.get("/api", tags=["System"])
async def api_info():
    """API information and version details"""
    return {
        "message": f"Welcome to {settings.APP_NAME} API",
        "version": settings.VERSION,
        "systems_implemented": 50,
        "architecture": "Modular FastAPI + React + MongoDB",
        "documentation": "/api/docs" if settings.DEBUG else "Contact support for documentation",
        "status": "production_ready"
    }

# Root Application Endpoint
@app.get("/", tags=["System"])  
async def root():
    """Root endpoint - redirects to application"""
    return {
        "message": f"Welcome to {settings.APP_NAME}",
        "version": settings.VERSION,
        "api_url": "/api",
        "frontend_url": "/",
        "documentation": "/api/docs" if settings.DEBUG else None
    }


# =============================================================================
# API ROUTER REGISTRATION - ORGANIZED BY BUSINESS DOMAIN
# =============================================================================

# CORE AUTHENTICATION & USER MANAGEMENT
app.include_router(auth.router, prefix="/api/auth", tags=["🔐 Authentication"])
app.include_router(users.router, prefix="/api/users", tags=["👥 Users"])
app.include_router(admin.router, prefix="/api/admin", tags=["⚙️ Administration"])

# BUSINESS INTELLIGENCE & ANALYTICS
app.include_router(analytics.router, prefix="/api/analytics", tags=["📊 Analytics"])
app.include_router(dashboard.router, prefix="/api/dashboard", tags=["📈 Dashboard"])
app.include_router(business_intelligence.router, prefix="/api/business-intelligence", tags=["🧠 Business Intelligence"])
app.include_router(analytics_system.router, prefix="/api/analytics-system", tags=["📊 Analytics System"])
app.include_router(advanced_analytics.router, prefix="/api/advanced-analytics", tags=["📊 Advanced Analytics"])

# CONTENT & WORKSPACE MANAGEMENT  
app.include_router(workspaces.router, prefix="/api/workspaces", tags=["🏢 Workspaces"])
app.include_router(blog.router, prefix="/api/blog", tags=["📝 Blog & Content"])
app.include_router(content_creation_suite.router, prefix="/api/content-creation", tags=["✍️ Content Creation"])

# AI & AUTOMATION SERVICES
app.include_router(ai.router, prefix="/api/ai", tags=["🤖 AI Services"])
app.include_router(ai_token_management.router, prefix="/api/tokens", tags=["🎫 AI Token Management"]) 
app.include_router(ai_content_generation.router, prefix="/api/ai-content", tags=["🤖 AI Content Generation"])
app.include_router(advanced_ai_suite.router, prefix="/api/advanced-ai", tags=["🤖 Advanced AI Suite"])
app.include_router(automation_system.router, prefix="/api/automation", tags=["⚙️ Automation System"])

# E-COMMERCE & FINANCIAL SYSTEMS
app.include_router(ecommerce.router, prefix="/api/ecommerce", tags=["🛒 E-commerce"])
app.include_router(enhanced_ecommerce.router, prefix="/api/enhanced-ecommerce", tags=["🛒 Enhanced E-commerce"])
app.include_router(financial_management.router, prefix="/api/financial", tags=["💰 Financial Management"])
app.include_router(advanced_financial_analytics.router, prefix="/api/advanced-financial", tags=["💰 Advanced Financial Analytics"])
app.include_router(escrow_system.router, prefix="/api/escrow", tags=["🏦 Escrow System"])
app.include_router(subscription_management.router, prefix="/api/subscriptions", tags=["💳 Subscription Management"])

# CUSTOMER ENGAGEMENT & EXPERIENCE
app.include_router(bio_sites.router, prefix="/api/bio-sites", tags=["🌐 Bio Sites"])
app.include_router(social_media.router, prefix="/api/social-media", tags=["📱 Social Media"])
app.include_router(social_media_suite.router, prefix="/api/social-media-suite", tags=["📱 Social Media Suite"])
app.include_router(marketing.router, prefix="/api/marketing", tags=["📢 Marketing"])
app.include_router(email_marketing.router, prefix="/api/email-marketing", tags=["📧 Email Marketing"])
app.include_router(customer_experience_suite.router, prefix="/api/customer-experience", tags=["🎯 Customer Experience"])
app.include_router(social_email_integration.router, prefix="/api/social-email", tags=["📧 Social Email Integration"])
app.include_router(notification_system.router, prefix="/api/notifications", tags=["🔔 Notifications"])

# BUSINESS OPERATIONS
app.include_router(bookings.router, prefix="/api/bookings", tags=["📅 Booking System"])
app.include_router(crm_management.router, prefix="/api/crm", tags=["👥 CRM Management"])
app.include_router(team_management.router, prefix="/api/team", tags=["👥 Team Management"])
app.include_router(course_management.router, prefix="/api/courses", tags=["🎓 Course Management"])
app.include_router(support_system.router, prefix="/api/support", tags=["🆘 Support System"])

# INTEGRATION & COMMUNICATION SYSTEMS
app.include_router(integrations.router, prefix="/api/integrations", tags=["🔗 Integrations"])
app.include_router(google_oauth.router, prefix="/api/oauth", tags=["🔐 OAuth Integration"])
app.include_router(webhook_system.router, prefix="/api/webhooks", tags=["🔗 Webhook System"])
app.include_router(i18n_system.router, prefix="/api/i18n", tags=["🌍 Internationalization"])

# DEVELOPMENT & MANAGEMENT TOOLS
app.include_router(website_builder.router, prefix="/api/website-builder", tags=["🏗️ Website Builder"])
app.include_router(form_builder.router, prefix="/api/forms", tags=["📝 Form Builder"])
app.include_router(template_marketplace.router, prefix="/api/templates", tags=["🏪 Template Marketplace"])
app.include_router(media_library.router, prefix="/api/media", tags=["📁 Media Library"])

# MARKETING & GROWTH SYSTEMS
app.include_router(promotions_referrals.router, prefix="/api/promotions", tags=["🎁 Promotions & Referrals"])
app.include_router(link_shortener.router, prefix="/api/links", tags=["🔗 Link Shortener"])
app.include_router(survey_system.router, prefix="/api/surveys", tags=["📋 Survey System"])

# ENTERPRISE & COMPLIANCE SYSTEMS
app.include_router(onboarding_system.router, prefix="/api/onboarding", tags=["🚀 Onboarding System"])
app.include_router(rate_limiting_system.router, prefix="/api/rate-limits", tags=["⚡ Rate Limiting"])
app.include_router(monitoring_system.router, prefix="/api/monitoring", tags=["📊 Monitoring & Observability"])
app.include_router(backup_system.router, prefix="/api/backup", tags=["💾 Backup & Disaster Recovery"])
app.include_router(compliance_system.router, prefix="/api/compliance", tags=["📋 Compliance & Audit"])


# Development Server Configuration
if __name__ == "__main__":
    uvicorn.run(
        "main:app",
        host="0.0.0.0",
        port=8001,
        reload=settings.DEBUG,
        log_level="info" if not settings.DEBUG else "debug",
        workers=1 if settings.DEBUG else 4,
        access_log=settings.DEBUG
    )