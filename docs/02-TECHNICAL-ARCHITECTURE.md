# 🏗️ Mewayz Platform - Technical Architecture

## Architecture Overview

The Mewayz platform is built on a modern, scalable architecture that prioritizes performance, security, and maintainability. Our single-stack Laravel approach ensures consistency and reduces complexity while providing enterprise-grade capabilities.

## Core Architecture Principles

### 1. **Single-Stack Consistency**
- **Laravel 10+** as the primary backend framework
- **Unified Database** with MariaDB for all data
- **Consistent API Design** across all endpoints
- **Centralized Authentication** with Laravel Sanctum
- **Unified Logging** and monitoring

### 2. **API-First Design**
- **RESTful Architecture** with consistent endpoints
- **Resource-Based URLs** for intuitive navigation
- **Proper HTTP Methods** (GET, POST, PUT, DELETE)
- **Standardized Response Format** across all endpoints
- **Comprehensive Error Handling** with meaningful messages

### 3. **Multi-Tenant Architecture**
- **Organization-Based Isolation** for data security
- **Role-Based Access Control** (RBAC) for permissions
- **Scalable Data Model** supporting thousands of tenants
- **Performance Optimization** with proper indexing
- **Resource Isolation** for security and performance

## System Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                        CLIENT LAYER                            │
├─────────────────────────────────────────────────────────────────┤
│  Laravel Web Interface (Primary) │ Mobile Apps (Future)        │
│  Blade + Livewire + Alpine.js    │ React Native / Flutter      │
└─────────────────────────────────────────────────────────────────┘
                                │
                    ┌─────────────────────┐
                    │     LOAD BALANCER   │
                    │   (Nginx/HAProxy)   │
                    └─────────────────────┘
                                │
┌─────────────────────────────────────────────────────────────────┐
│                    APPLICATION LAYER                           │
├─────────────────────────────────────────────────────────────────┤
│           Laravel 10+ Application Server                       │
│  ┌─────────────────┬─────────────────┬─────────────────────┐   │
│  │   API Gateway   │   Web Interface │   Background Jobs   │   │
│  │   (REST APIs)   │   (Blade/Livewire)│   (Queue System)   │   │
│  └─────────────────┴─────────────────┴─────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
                                │
┌─────────────────────────────────────────────────────────────────┐
│                     SERVICE LAYER                              │
├─────────────────────────────────────────────────────────────────┤
│  ┌─────────────┬─────────────┬─────────────┬─────────────────┐  │
│  │   Auth      │   Social    │   CRM       │   E-commerce    │  │
│  │   Service   │   Media     │   Service   │   Service       │  │
│  │             │   Service   │             │                 │  │
│  ├─────────────┼─────────────┼─────────────┼─────────────────┤  │
│  │   Email     │   Course    │   Analytics │   Payment       │  │
│  │   Service   │   Service   │   Service   │   Service       │  │
│  └─────────────┴─────────────┴─────────────┴─────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
                                │
┌─────────────────────────────────────────────────────────────────┐
│                      DATA LAYER                                │
├─────────────────────────────────────────────────────────────────┤
│  ┌─────────────┬─────────────┬─────────────┬─────────────────┐  │
│  │   MariaDB   │   Redis     │   File      │   Queue         │  │
│  │   Database  │   Cache     │   Storage   │   Storage       │  │
│  │   (Primary) │   (Sessions)│   (Assets)  │   (Jobs)        │  │
│  └─────────────┴─────────────┴─────────────┴─────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
                                │
┌─────────────────────────────────────────────────────────────────┐
│                 EXTERNAL INTEGRATIONS                          │
├─────────────────────────────────────────────────────────────────┤
│  ┌─────────────┬─────────────┬─────────────┬─────────────────┐  │
│  │   Stripe    │   OpenAI    │   ElasticMail│   Social Media  │  │
│  │   (Payments)│   (AI)      │   (Email)   │   APIs          │  │
│  └─────────────┴─────────────┴─────────────┴─────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
```

## Technology Stack

### **Backend Technologies**
```
┌─────────────────────────────────────────────────────────────────┐
│                      BACKEND STACK                             │
├─────────────────┬─────────────────┬─────────────────────────────┤
│   PHP 8.2+      │   Laravel 10+   │   MariaDB 10.6+             │
│   (Runtime)     │   (Framework)   │   (Database)                │
├─────────────────┼─────────────────┼─────────────────────────────┤
│   Composer      │   Eloquent ORM  │   Laravel Sanctum           │
│   (Dependencies)│   (Database)    │   (Authentication)          │
├─────────────────┼─────────────────┼─────────────────────────────┤
│   Redis         │   Queue System  │   File Storage              │
│   (Cache)       │   (Background)  │   (Assets)                  │
└─────────────────┴─────────────────┴─────────────────────────────┘
```

### **Frontend Technologies**
```
┌─────────────────────────────────────────────────────────────────┐
│                     FRONTEND STACK                             │
├─────────────────┬─────────────────┬─────────────────────────────┤
│   Blade         │   Livewire      │   Alpine.js                 │
│   (Templates)   │   (Components)  │   (JavaScript)              │
├─────────────────┼─────────────────┼─────────────────────────────┤
│   Tailwind CSS  │   Vite          │   NPM                       │
│   (Styling)     │   (Build Tool)  │   (Package Manager)         │
├─────────────────┼─────────────────┼─────────────────────────────┤
│   Laravel Folio │   HTMX          │   Web Components            │
│   (Routing)     │   (Interactivity)│   (Custom Elements)        │
└─────────────────┴─────────────────┴─────────────────────────────┘
```

### **Infrastructure Stack**
```
┌─────────────────────────────────────────────────────────────────┐
│                  INFRASTRUCTURE STACK                          │
├─────────────────┬─────────────────┬─────────────────────────────┤
│   Nginx         │   Supervisor    │   Ubuntu 22.04+            │
│   (Web Server)  │   (Process Mgr) │   (Operating System)       │
├─────────────────┼─────────────────┼─────────────────────────────┤
│   SSL/TLS       │   Let's Encrypt │   CloudFlare                │
│   (Security)    │   (Certificates)│   (CDN/DNS)                 │
├─────────────────┼─────────────────┼─────────────────────────────┤
│   Docker        │   Git           │   CI/CD Pipeline            │
│   (Containerization)│ (Version Control)│ (Deployment)          │
└─────────────────┴─────────────────┴─────────────────────────────┘
```

## Database Architecture

### **Database Design Philosophy**
- **Third Normal Form (3NF)**: Proper normalization to eliminate redundancy
- **Foreign Key Constraints**: Referential integrity across all relationships
- **Optimized Indexing**: Strategic indexes for query performance
- **Soft Deletes**: Maintain data history while allowing "deletion"
- **Audit Trails**: Track all data changes for compliance

### **Core Database Tables**

#### **User Management**
```sql
-- User authentication and profiles
users (id, name, email, password, email_verified_at, 2fa_secret, created_at, updated_at)
organizations (id, name, description, logo_url, settings, created_at, updated_at)
user_organizations (user_id, organization_id, role, permissions, created_at)

-- Team management
yena_teams (id, name, description, organization_id, created_at, updated_at)
yena_teams_user_table (team_id, user_id, role, permissions, created_at)
yena_teams_invite (id, team_id, email, role, token, expires_at, created_at)
```

#### **Social Media Management**
```sql
-- Social media accounts and posts
social_media_accounts (id, user_id, platform, username, access_token, refresh_token, expires_at, created_at)
social_media_posts (id, account_id, content, media_urls, hashtags, scheduled_at, posted_at, engagement_stats, created_at)

-- Instagram specific tables
instagram_accounts (id, workspace_id, username, access_token, profile_data, created_at, updated_at)
instagram_posts (id, account_id, content, media_url, hashtags, scheduled_at, posted_at, engagement_stats, created_at)
instagram_hashtags (id, hashtag, difficulty_level, engagement_rate, created_at, updated_at)
```

#### **Bio Sites & Website Builder**
```sql
-- Bio sites and website builder
bio_sites (id, user_id, name, subdomain, custom_domain, theme, settings, seo_settings, created_at, updated_at)
sites (id, user_id, name, subdomain, custom_domain, theme, settings, seo_settings, created_at, updated_at)

-- Content management
pages (id, site_id, title, slug, content, meta_title, meta_description, is_published, created_at, updated_at)
sections (id, page_id, type, content, settings, order, created_at, updated_at)
section_items (id, section_id, type, content, settings, order, created_at, updated_at)

-- Links and tracking
bio_sites_links (id, bio_site_id, title, url, description, icon, order, is_active, click_count, created_at, updated_at)
bio_sites_linker (id, bio_site_id, short_code, original_url, click_count, created_at, updated_at)
```

#### **E-commerce & Payments**
```sql
-- Product management
products (id, user_id, name, description, price, cost, stock, category, images, seo_settings, is_active, created_at, updated_at)
product_variants (id, product_id, name, price, stock, sku, attributes, created_at, updated_at)
product_categories (id, name, description, parent_id, created_at, updated_at)

-- Order management
orders (id, user_id, total_amount, tax_amount, shipping_amount, status, payment_method, payment_status, created_at, updated_at)
order_items (id, order_id, product_id, variant_id, quantity, price, created_at, updated_at)

-- Payment processing
payment_transactions (id, user_id, amount, currency, payment_method, transaction_id, status, metadata, created_at, updated_at)
checkout_sessions (id, user_id, items, total_amount, status, stripe_session_id, created_at, updated_at)
```

#### **Course Management**
```sql
-- Course structure
courses (id, user_id, title, description, price, thumbnail, category, level, status, created_at, updated_at)
course_modules (id, course_id, title, description, order, created_at, updated_at)
course_lessons (id, module_id, title, content, video_url, duration, order, created_at, updated_at)

-- Student management
course_enrollments (id, course_id, user_id, enrolled_at, completed_at, progress, created_at, updated_at)
lesson_progress (id, enrollment_id, lesson_id, completed_at, watch_time, created_at, updated_at)
```

#### **CRM & Email Marketing**
```sql
-- Contact management
contacts (id, user_id, name, email, phone, company, tags, custom_fields, source, score, created_at, updated_at)
contact_interactions (id, contact_id, type, subject, content, channel, created_at, updated_at)

-- Email marketing
email_campaigns (id, user_id, name, subject, content, from_name, from_email, status, sent_at, created_at, updated_at)
email_templates (id, user_id, name, subject, content, category, created_at, updated_at)
campaign_sends (id, campaign_id, contact_id, sent_at, opened_at, clicked_at, bounced_at, created_at)
```

#### **Analytics & Tracking**
```sql
-- Visitor tracking
sites_visitors (id, site_id, ip_address, user_agent, referer, page_visited, visited_at, session_id, created_at)
bio_sites_visitors (id, bio_site_id, ip_address, user_agent, referer, visited_at, session_id, created_at)

-- Link tracking
sites_linker_track (id, linker_id, ip_address, user_agent, referer, clicked_at, created_at)
bio_sites_linker_track (id, linker_id, ip_address, user_agent, referer, clicked_at, created_at)

-- Performance metrics
analytics_events (id, user_id, event_type, event_data, source, created_at)
analytics_reports (id, user_id, report_type, data, generated_at, created_at)
```

### **Indexing Strategy**
```sql
-- Primary performance indexes
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_created_at ON users(created_at);
CREATE INDEX idx_organizations_user_id ON organizations(user_id);

-- Social media indexes
CREATE INDEX idx_social_posts_account_id ON social_media_posts(account_id);
CREATE INDEX idx_social_posts_scheduled_at ON social_media_posts(scheduled_at);
CREATE INDEX idx_instagram_posts_account_id ON instagram_posts(account_id);

-- Bio sites indexes
CREATE INDEX idx_bio_sites_user_id ON bio_sites(user_id);
CREATE INDEX idx_bio_sites_subdomain ON bio_sites(subdomain);
CREATE INDEX idx_bio_sites_custom_domain ON bio_sites(custom_domain);

-- E-commerce indexes
CREATE INDEX idx_products_user_id ON products(user_id);
CREATE INDEX idx_products_category ON products(category);
CREATE INDEX idx_orders_user_id ON orders(user_id);
CREATE INDEX idx_orders_status ON orders(status);

-- Analytics indexes
CREATE INDEX idx_visitors_site_id_date ON sites_visitors(site_id, created_at);
CREATE INDEX idx_visitors_bio_site_id_date ON bio_sites_visitors(bio_site_id, created_at);
CREATE INDEX idx_analytics_events_user_id_type ON analytics_events(user_id, event_type);
```

## Security Architecture

### **Authentication Flow**
```
1. User Login Request
   ↓
2. Credentials Validation (Laravel Hash)
   ↓
3. 2FA Verification (if enabled)
   ↓
4. OAuth Provider Verification (if OAuth)
   ↓
5. Generate Sanctum Token
   ↓
6. Token Stored in Database
   ↓
7. Return Token to Client
   ↓
8. Client Includes Token in Headers
   ↓
9. Server Validates Token on Each Request
   ↓
10. Grant/Deny Access Based on Permissions
```

### **Security Measures**
- **Input Validation**: Laravel Form Requests with comprehensive validation
- **SQL Injection Prevention**: Eloquent ORM with prepared statements
- **XSS Protection**: Blade template escaping and CSP headers
- **CSRF Protection**: Laravel CSRF tokens for all forms
- **Rate Limiting**: API rate limiting per user and endpoint
- **Password Security**: Bcrypt hashing with salt
- **Session Security**: Secure session configuration
- **Data Encryption**: AES-256 encryption for sensitive data

### **Role-Based Access Control (RBAC)**
```php
// User roles hierarchy
'super_admin' => ['*'], // Full access
'admin' => ['users.manage', 'sites.manage', 'analytics.view'],
'manager' => ['sites.manage', 'analytics.view'],
'editor' => ['sites.edit', 'content.manage'],
'viewer' => ['analytics.view', 'content.view'],
'user' => ['profile.manage', 'own_content.manage']
```

## Performance Architecture

### **Caching Strategy**
```
┌─────────────────────────────────────────────────────────────────┐
│                     CACHING LAYERS                             │
├─────────────────┬─────────────────┬─────────────────────────────┤
│   Browser Cache│   CDN Cache     │   Application Cache         │
│   (Static Assets│   (Images, CSS, │   (Database Queries,       │
│   CSS, JS)      │   JS Files)     │   API Responses)           │
├─────────────────┼─────────────────┼─────────────────────────────┤
│   Redis Cache   │   Database Cache│   File System Cache        │
│   (Sessions,    │   (Query Results│   (Compiled Templates,     │
│   Config)       │   Table Cache)  │   Optimized Assets)        │
└─────────────────┴─────────────────┴─────────────────────────────┘
```

### **Query Optimization**
- **Eager Loading**: Load related models to avoid N+1 queries
- **Query Caching**: Cache frequent database queries
- **Database Indexes**: Strategic indexes for all search columns
- **Pagination**: Efficient pagination for large datasets
- **Connection Pooling**: Optimize database connections

### **Asset Optimization**
- **Vite Build System**: Modern asset compilation
- **CSS/JS Minification**: Reduce file sizes
- **Image Optimization**: Compress and optimize images
- **Lazy Loading**: Load images and content on demand
- **CDN Integration**: Serve static assets from CDN

## API Architecture

### **RESTful Design Principles**
- **Resource-Based URLs**: `/api/users`, `/api/courses`, `/api/bio-sites`
- **HTTP Methods**: GET (read), POST (create), PUT (update), DELETE (remove)
- **Status Codes**: Proper HTTP status codes for responses
- **Consistent Format**: Standardized JSON response format
- **Error Handling**: Meaningful error messages and codes

### **API Response Format**
```json
{
  "success": true,
  "message": "Request completed successfully",
  "data": {
    // Response data
  },
  "meta": {
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "total": 150,
      "total_pages": 10
    },
    "timestamp": "2025-07-15T10:30:00Z",
    "request_id": "req_123456789"
  }
}
```

### **API Versioning Strategy**
- **URL Versioning**: `/api/v1/`, `/api/v2/`
- **Header Versioning**: `Accept: application/vnd.api+json;version=1`
- **Backward Compatibility**: Maintain older versions for stable clients
- **Deprecation Policy**: 6-month notice for breaking changes

## Deployment Architecture

### **Environment Configuration**
```
┌─────────────────────────────────────────────────────────────────┐
│                   DEPLOYMENT ENVIRONMENTS                      │
├─────────────────┬─────────────────┬─────────────────────────────┤
│   Development   │   Staging       │   Production                │
│   (Local)       │   (Pre-production)│   (Live)                 │
├─────────────────┼─────────────────┼─────────────────────────────┤
│   - Debug On    │   - Debug Off   │   - Debug Off              │
│   - Test Data   │   - Prod-like   │   - Live Data              │
│   - Fast Builds │   - Full Tests  │   - Optimized              │
└─────────────────┴─────────────────┴─────────────────────────────┘
```

### **Deployment Pipeline**
```
1. Code Commit (Git)
   ↓
2. Automated Tests (PHPUnit)
   ↓
3. Security Scans (SonarQube)
   ↓
4. Build Assets (Vite)
   ↓
5. Database Migration (Artisan)
   ↓
6. Deploy to Staging
   ↓
7. Integration Tests
   ↓
8. Deploy to Production
   ↓
9. Health Checks
   ↓
10. Rollback if Issues
```

## Monitoring & Logging

### **Monitoring Stack**
- **Application Monitoring**: Laravel Telescope for debugging
- **Performance Monitoring**: New Relic or DataDog for metrics
- **Error Tracking**: Sentry for error monitoring
- **Uptime Monitoring**: Pingdom for availability
- **Security Monitoring**: Custom security event logging

### **Logging Strategy**
```php
// Log levels and usage
'emergency' => 'System is unusable',
'alert' => 'Action must be taken immediately',
'critical' => 'Critical conditions',
'error' => 'Error conditions',
'warning' => 'Warning conditions',
'notice' => 'Normal but significant conditions',
'info' => 'Informational messages',
'debug' => 'Debug-level messages'
```

---

**Technical Architecture Documentation**
*Mewayz Platform - Version 2.0*
*Built with Laravel 10+ and modern web technologies*
*Last Updated: July 15, 2025*