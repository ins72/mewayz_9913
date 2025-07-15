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

### Domain Configuration
- **Production Domain**: mewayz.com
- **Development**: Local environment configurations
- **API Routes**: All backend routes must use `/api` prefix

**Important**: Do not modify environment URLs or domain configurations without proper testing to avoid breaking functionality.

---

## 🏗️ Technical Architecture

### Simplified Laravel-Only Architecture

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │   Laravel       │    │   Database      │
│   (Port 3000)   │◄──►│   (Port 8001)   │◄──►│   MySQL/MariaDB │
│   Static Files  │    │   Complete      │    │   Data Storage  │
│   (Optional)    │    │   Backend       │    │                 │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

### Technology Stack

#### Backend (Laravel 10+)
- **Framework**: Laravel 10.48.4 (PHP 8.2.28)
- **Database**: MySQL 8.0+ / MariaDB
- **Authentication**: Laravel Sanctum with OAuth 2.0
- **API**: RESTful API with comprehensive endpoints
- **Security**: AES-256, TLS 1.3, 2FA, RBAC
- **Queue**: Redis for background job processing
- **Cache**: Redis for application caching
- **Storage**: Local with S3 compatibility

#### Frontend (Multi-Platform)
- **Web**: Laravel Blade + Vite + Alpine.js
- **Mobile**: Flutter 3.x (Dart)
- **State Management**: Provider (Flutter)
- **Styling**: Tailwind CSS + Custom Design System
- **PWA**: Service Worker with offline capabilities
- **Build Tools**: Vite for asset compilation

#### Development Tools
- **Composer**: PHP dependency management
- **NPM/Yarn**: JavaScript dependency management
- **Vite**: Modern build tool and dev server
- **PHP CS Fixer**: Code style fixing
- **ESLint**: JavaScript linting
- **Prettier**: Code formatting
