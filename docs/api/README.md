# Mewayz Platform API Documentation
**Version 3.0.0** | *July 20, 2025*

## API Overview

The Mewayz Platform API is built with FastAPI and provides comprehensive REST endpoints for all platform features. All API endpoints require authentication unless otherwise specified.

**Base URL**: `/api`  
**Authentication**: Bearer token (JWT)  
**Content Type**: `application/json`

### Quick Reference

```bash
# Authentication
POST /api/auth/login
POST /api/auth/register
GET  /api/auth/me

# Link Shortener
GET  /api/link-shortener/links
POST /api/link-shortener/create
GET  /api/link-shortener/stats

# Team Management
GET  /api/team/members
POST /api/team/invite

# Form Templates
GET  /api/form-templates
POST /api/form-templates

# Discount Codes
GET  /api/discount-codes
POST /api/discount-codes

# Business Features
GET  /api/bio-sites
GET  /api/ecommerce/dashboard
GET  /api/bookings/dashboard
GET  /api/analytics/overview

# System
GET  /api/health
```

## Authentication

### Login
```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "user": {
    "id": "uuid",
    "name": "User Name",
    "email": "user@example.com",
    "role": "user"
  }
}
```

### Register
```http
POST /api/auth/register
Content-Type: application/json

{
  "name": "Full Name",
  "email": "user@example.com",
  "password": "password"
}
```

## Link Shortener API

### Get All Links
```http
GET /api/link-shortener/links
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "links": [
      {
        "id": "uuid",
        "original_url": "https://example.com/long-url",
        "short_code": "abc123",
        "short_url": "https://mwz.to/abc123",
        "clicks": 245,
        "status": "active",
        "created_at": "2025-07-20T10:30:00Z"
      }
    ]
  }
}
```

### Create Short Link
```http
POST /api/link-shortener/create
Authorization: Bearer {token}
Content-Type: application/json

{
  "original_url": "https://example.com/very-long-url",
  "custom_code": "my-code"  // optional
}
```

### Get Link Statistics
```http
GET /api/link-shortener/stats
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "stats": {
      "total_links": 127,
      "active_links": 95,
      "total_clicks": 5834,
      "click_rate": 78.5
    }
  }
}
```

## Team Management API

### Get Team Members
```http
GET /api/team/members
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "members": [
      {
        "id": "uuid",
        "name": "John Doe",
        "email": "john@example.com",
        "role": "admin",
        "status": "active",
        "last_active": "2 minutes ago",
        "joined_at": "2025-01-15T00:00:00Z"
      }
    ]
  }
}
```

### Invite Team Member
```http
POST /api/team/invite
Authorization: Bearer {token}
Content-Type: application/json

{
  "email": "newmember@example.com",
  "role": "editor",
  "workspace_id": "workspace-uuid"
}
```

## Form Templates API

### Get Form Templates
```http
GET /api/form-templates
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "templates": [
      {
        "id": "uuid",
        "name": "Contact Form",
        "description": "Basic contact form",
        "category": "contact",
        "fields": ["name", "email", "message"],
        "submissions": 142,
        "is_published": true,
        "created_at": "2025-07-01T00:00:00Z"
      }
    ]
  }
}
```

### Create Form Template
```http
POST /api/form-templates
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Newsletter Signup",
  "description": "Email collection form",
  "category": "marketing",
  "fields": [
    {"name": "email", "type": "email", "required": true},
    {"name": "firstName", "type": "text", "required": false}
  ]
}
```

## Discount Codes API

### Get Discount Codes
```http
GET /api/discount-codes
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "codes": [
      {
        "id": "uuid",
        "code": "WELCOME20",
        "description": "Welcome discount",
        "type": "percentage",
        "value": 20,
        "usage_limit": 100,
        "used_count": 45,
        "is_active": true,
        "expires_at": "2025-12-31T23:59:59Z",
        "created_at": "2025-06-01T00:00:00Z"
      }
    ]
  }
}
```

### Create Discount Code
```http
POST /api/discount-codes
Authorization: Bearer {token}
Content-Type: application/json

{
  "code": "SUMMER25",
  "description": "Summer sale discount",
  "type": "percentage",
  "value": 25,
  "usage_limit": 500,
  "expires_at": "2025-09-01T00:00:00Z",
  "applicable_products": ["all"]
}
```

## Business Features API

### Bio Sites
```http
GET /api/bio-sites
GET /api/bio-sites/themes
POST /api/bio-sites
```

### E-commerce
```http
GET /api/ecommerce/products
GET /api/ecommerce/orders
GET /api/ecommerce/dashboard
```

### Advanced Booking
```http
GET /api/bookings/services
GET /api/bookings/appointments
GET /api/bookings/dashboard
```

### Financial Management
```http
GET /api/financial/invoices
GET /api/financial/dashboard/comprehensive
```

### Analytics
```http
GET /api/analytics/overview
GET /api/analytics/business-intelligence/advanced
```

## Error Handling

All API endpoints return consistent error responses:

```json
{
  "success": false,
  "message": "Error description",
  "detail": "Detailed error information",
  "status_code": 400
}
```

### Common Status Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Internal Server Error

## Authentication Headers

Include the JWT token in all authenticated requests:

```http
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
Content-Type: application/json
```

## Rate Limiting

- **Free Plan**: 100 requests/hour per endpoint
- **Pro Plan**: 1,000 requests/hour per endpoint  
- **Enterprise Plan**: Unlimited requests

## Webhooks

Subscribe to real-time events:

- `user.created` - New user registration
- `workspace.created` - New workspace creation
- `payment.completed` - Successful payment
- `team.member_invited` - Team member invitation
- `link.clicked` - Short link clicked

## SDKs and Libraries

Official SDKs available for:

- **Python**: `pip install mewayz-sdk`
- **JavaScript**: `npm install @mewayz/sdk`
- **PHP**: `composer require mewayz/php-sdk`

## Support

- **API Issues**: api-support@mewayz.com
- **Documentation**: docs.mewayz.com
- **Status Page**: status.mewayz.com

---

*Mewayz Platform API v3.0.0 - Complete business platform API*