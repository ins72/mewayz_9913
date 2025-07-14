# Mewayz Platform - Development Guide

**Professional Development Documentation for Mewayz Technologies Inc.'s Flagship Platform**

*Building seamless business solutions with modern technology stacks*

---

## 🎯 Platform Overview

Mewayz represents the pinnacle of Mewayz Technologies Inc.'s commitment to creating seamless business management solutions. This development guide provides comprehensive technical documentation for contributing to and extending the Mewayz platform.

### Brand Architecture
- **Mewayz**: The user-facing platform brand
- **Mewayz Technologies Inc.**: The engineering and innovation company
- **Seamless**: Our core development philosophy

---

## Database Schema

### Core Tables

#### Users Table
```sql
CREATE TABLE users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    google_id VARCHAR(255) NULL,
    apple_id VARCHAR(255) NULL,
    two_factor_secret TEXT NULL,
    two_factor_recovery_codes TEXT NULL,
    last_login_at TIMESTAMP NULL,
    last_login_ip VARCHAR(45) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### Workspaces Table
```sql
CREATE TABLE workspaces (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    owner_id BIGINT NOT NULL,
    settings JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
);
```

#### Bio Sites Table
```sql
CREATE TABLE bio_sites (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    theme VARCHAR(255) DEFAULT 'default',
    custom_css TEXT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

#### Social Media Accounts Table
```sql
CREATE TABLE social_media_accounts (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    platform VARCHAR(50) NOT NULL,
    account_id VARCHAR(255) NOT NULL,
    username VARCHAR(255) NOT NULL,
    access_token TEXT NOT NULL,
    refresh_token TEXT NULL,
    expires_at TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### Business Feature Tables

#### CRM Tables
```sql
CREATE TABLE audience (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NULL,
    type VARCHAR(50) NOT NULL DEFAULT 'contact',
    status VARCHAR(50) DEFAULT 'active',
    tags JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

#### Products Table (E-commerce)
```sql
CREATE TABLE products (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    price DECIMAL(10,2) NOT NULL,
    sku VARCHAR(255) UNIQUE NOT NULL,
    inventory_count INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

#### Courses Table
```sql
CREATE TABLE courses (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    price DECIMAL(10,2) DEFAULT 0.00,
    is_published BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## API Controllers

### AuthController
Located: `app/Http/Controllers/Api/AuthController.php`

**Key Methods:**
- `register(Request $request)` - User registration
- `login(Request $request)` - User authentication
- `logout(Request $request)` - User logout
- `me(Request $request)` - Get current user
- `updateProfile(Request $request)` - Update user profile

### OAuth & 2FA Controllers
Located: `app/Http/Controllers/Auth/`

**OAuthController Methods:**
- `redirectToProvider($provider)` - Redirect to OAuth provider
- `handleProviderCallback($provider)` - Handle OAuth callback
- `getOAuthStatus()` - Get OAuth connection status
- `linkAccount()` - Link OAuth account
- `unlinkAccount()` - Unlink OAuth account

**TwoFactorController Methods:**
- `generate()` - Generate 2FA secret
- `enable()` - Enable 2FA
- `disable()` - Disable 2FA
- `verify()` - Verify 2FA code
- `generateRecoveryCodes()` - Generate recovery codes
- `status()` - Get 2FA status

### Business Feature Controllers

#### WorkspaceController
- `index()` - List workspaces
- `store()` - Create workspace
- `show($workspace)` - Get workspace
- `update($workspace)` - Update workspace
- `destroy($workspace)` - Delete workspace

#### SocialMediaController
- `getAccounts()` - Get connected accounts
- `connectAccount()` - Connect social media account
- `disconnectAccount($account)` - Disconnect account
- `schedulePost()` - Schedule a post
- `getScheduledPosts()` - Get scheduled posts
- `getAnalytics()` - Get analytics

#### BioSiteController
- `index()` - List bio sites
- `store()` - Create bio site
- `show($bioSite)` - Get bio site
- `update($bioSite)` - Update bio site
- `destroy($bioSite)` - Delete bio site
- `getAnalytics($bioSite)` - Get bio site analytics

#### CrmController
- `getLeads()` - Get leads
- `createLead()` - Create lead
- `getContacts()` - Get contacts
- `importContacts()` - Import contacts
- `getPipeline()` - Get pipeline

#### EmailMarketingController
- `getCampaigns()` - Get campaigns
- `createCampaign()` - Create campaign
- `sendCampaign($campaign)` - Send campaign
- `getTemplates()` - Get templates
- `createTemplate()` - Create template
- `getAnalytics()` - Get analytics

#### EcommerceController
- `getProducts()` - Get products
- `createProduct()` - Create product
- `getOrders()` - Get orders
- `updateOrderStatus($order)` - Update order status
- `getAnalytics()` - Get analytics

#### CourseController
- `index()` - List courses
- `store()` - Create course
- `show($course)` - Get course
- `getStudents($course)` - Get students
- `getLessons($course)` - Get lessons
- `createLesson($course)` - Create lesson

#### AnalyticsController
- `getOverview()` - Get analytics overview
- `getTrafficAnalytics()` - Get traffic analytics
- `getRevenueAnalytics()` - Get revenue analytics
- `getReports()` - Get reports
- `generateReport()` - Generate report

## Authentication Flow

### Standard Authentication
1. User registers via `POST /api/auth/register`
2. User receives authentication token
3. User logs in via `POST /api/auth/login`
4. Token used for authenticated requests

### OAuth Authentication
1. User redirects to `GET /api/auth/oauth/{provider}`
2. User authorizes with provider
3. Provider redirects to `GET /api/auth/oauth/{provider}/callback`
4. System creates or links user account
5. User receives authentication token

### Two-Factor Authentication
1. User generates 2FA secret via `POST /api/auth/2fa/generate`
2. User scans QR code with authenticator app
3. User enables 2FA via `POST /api/auth/2fa/enable`
4. Subsequent logins require 2FA code
5. User can verify via `POST /api/auth/2fa/verify`

## Flutter Application Structure

### Configuration
- `lib/config/routes.dart` - GoRouter configuration
- `lib/config/theme.dart` - App theme configuration
- `lib/config/colors.dart` - Color constants

### Screens
- `lib/screens/auth/` - Authentication screens
- `lib/screens/dashboard/` - Dashboard screens
- `lib/screens/social_media/` - Social media screens
- `lib/screens/bio/` - Bio site screens
- `lib/screens/crm/` - CRM screens
- `lib/screens/landing/` - Landing screens

### Services
- `lib/services/api_service.dart` - API client
- `lib/services/pwa_service.dart` - PWA functionality
- `lib/services/notification_service.dart` - Push notifications
- `lib/services/offline_storage_service.dart` - Offline storage

### Providers
- `lib/providers/auth_provider.dart` - Authentication state
- `lib/providers/pwa_provider.dart` - PWA state

### Widgets
- `lib/widgets/custom_button.dart` - Custom button component
- `lib/widgets/custom_text_field.dart` - Custom text field
- `lib/widgets/logo_widget.dart` - Logo component
- `lib/widgets/layout/` - Layout components
- `lib/widgets/cards/` - Card components
- `lib/widgets/forms/` - Form components

## PWA Implementation

### Service Worker
Location: `flutter_app/web/sw.js`

**Features:**
- Cache API responses
- Offline page fallback
- Background sync
- Push notifications

### Manifest
Location: `flutter_app/web/manifest.json`

**Configuration:**
- App name: "Mewayz"
- Theme color: #FDFDFD
- Background color: #101010
- Display mode: standalone
- Icons: 192x192, 512x512

### PWA Services
Location: `flutter_app/lib/services/pwa_service.dart`

**Features:**
- Service worker registration
- Push notification handling
- Offline detection
- App installation prompts

## Testing Guidelines

### Backend Testing
Run comprehensive API tests:
```bash
# Test all endpoints
curl -X GET http://localhost:8001/api/health
curl -X POST http://localhost:8001/api/auth/login
curl -X GET http://localhost:8001/api/workspaces
```

### Frontend Testing
Test UI components and user flows:
```bash
# Start Flutter app
cd flutter_app
flutter run -d web-server --web-port=3000

# Test PWA features
# - Service worker registration
# - Offline functionality
# - Push notifications
# - App installation
```

## Deployment Checklist

### Pre-Deployment
- [ ] Run database migrations
- [ ] Build frontend assets
- [ ] Configure environment variables
- [ ] Test all API endpoints
- [ ] Verify OAuth configurations
- [ ] Test PWA functionality

### Production Deployment
- [ ] Set APP_ENV=production
- [ ] Configure SSL certificates
- [ ] Set up monitoring
- [ ] Configure backups
- [ ] Test performance
- [ ] Verify security headers

### Post-Deployment
- [ ] Monitor error logs
- [ ] Check performance metrics
- [ ] Verify all features working
- [ ] Test user registration/login
- [ ] Verify OAuth flows
- [ ] Test PWA installation