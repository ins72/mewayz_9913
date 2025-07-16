# Mewayz Platform - API Documentation

Complete API reference for the Mewayz platform, including authentication, endpoints, and integration examples.

## 🔗 Base URL

```
Production: https://api.mewayz.com
Development: http://localhost:8001
```

## 🔐 Authentication

### API Authentication Methods

#### 1. Session Authentication (Web)
```javascript
// Login via web form
POST /login
{
  "email": "user@example.com",
  "password": "password"
}
```

#### 2. Laravel Sanctum (API)
```bash
# Get API token
POST /api/auth/token
{
  "email": "user@example.com",
  "password": "password"
}

# Use token in headers
Authorization: Bearer {token}
```

#### 3. CSRF Protection
```javascript
// Include CSRF token in headers
X-CSRF-TOKEN: {csrf_token}
```

## 🏥 Health Check

### System Health
```http
GET /api/health
```

**Response:**
```json
{
  "success": true,
  "message": "System health check completed",
  "data": {
    "status": "healthy",
    "timestamp": "2025-01-16T12:00:00Z",
    "version": "2.0",
    "features": {
      "dashboard": true,
      "payment_processing": true,
      "instagram_management": true,
      "link_in_bio": true,
      "site_builder": true,
      "crm_system": true,
      "email_marketing": true,
      "analytics": true
    }
  }
}
```

## 💳 Payment API

### Get Payment Packages
```http
GET /api/payments/packages
```

**Response:**
```json
{
  "success": true,
  "packages": {
    "starter": {
      "amount": 9.99,
      "currency": "USD",
      "name": "Starter Package",
      "features": [
        "Up to 5 sites",
        "Basic analytics",
        "Email support",
        "1GB storage"
      ]
    },
    "professional": {
      "amount": 29.99,
      "currency": "USD",
      "name": "Professional Package",
      "features": [
        "Up to 25 sites",
        "Advanced analytics",
        "Priority support",
        "10GB storage",
        "Custom domains"
      ]
    },
    "enterprise": {
      "amount": 99.99,
      "currency": "USD",
      "name": "Enterprise Package",
      "features": [
        "Unlimited sites",
        "Enterprise analytics",
        "24/7 phone support",
        "100GB storage",
        "White-label solution"
      ]
    }
  }
}
```

### Create Checkout Session
```http
POST /api/payments/checkout/session
```

**Request:**
```json
{
  "package_id": "professional",
  "success_url": "https://your-domain.com/success?session_id={CHECKOUT_SESSION_ID}",
  "cancel_url": "https://your-domain.com/cancel",
  "metadata": {
    "source": "dashboard",
    "user_id": "123"
  }
}
```

**Response:**
```json
{
  "success": true,
  "session_id": "cs_test_abc123",
  "url": "https://checkout.stripe.com/pay/cs_test_abc123",
  "expires_at": "2025-01-16T13:00:00Z"
}
```

### Check Payment Status
```http
GET /api/payments/checkout/status/{session_id}
```

**Response:**
```json
{
  "success": true,
  "session_id": "cs_test_abc123",
  "status": "complete",
  "payment_status": "paid",
  "amount_total": 2999,
  "currency": "usd",
  "created": "2025-01-16T12:00:00Z",
  "expires_at": "2025-01-16T13:00:00Z"
}
```

### Stripe Webhook
```http
POST /api/webhook/stripe
```

**Headers:**
```
Stripe-Signature: t=1234567890,v1=signature
```

**Supported Events:**
- `checkout.session.completed`
- `payment_intent.succeeded`
- `customer.subscription.created`
- `customer.subscription.updated`
- `customer.subscription.deleted`

## 👥 User Management

### Get Current User
```http
GET /api/user
```

**Response:**
```json
{
  "success": true,
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "email_verified_at": "2025-01-16T12:00:00Z",
    "created_at": "2025-01-16T12:00:00Z",
    "updated_at": "2025-01-16T12:00:00Z"
  }
}
```

### Update User Profile
```http
PUT /api/user
```

**Request:**
```json
{
  "name": "John Smith",
  "email": "johnsmith@example.com"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Profile updated successfully",
  "user": {
    "id": 1,
    "name": "John Smith",
    "email": "johnsmith@example.com",
    "updated_at": "2025-01-16T12:00:00Z"
  }
}
```

## 🌐 Site Management

### List Sites
```http
GET /api/sites
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "My Portfolio",
      "domain": "myportfolio.mewayz.com",
      "status": "active",
      "created_at": "2025-01-16T12:00:00Z",
      "updated_at": "2025-01-16T12:00:00Z"
    }
  ],
  "meta": {
    "total": 1,
    "per_page": 15,
    "current_page": 1,
    "last_page": 1
  }
}
```

### Create Site
```http
POST /api/sites
```

**Request:**
```json
{
  "name": "My New Site",
  "domain": "mynewsite",
  "template": "professional",
  "settings": {
    "theme": "dark",
    "layout": "modern"
  }
}
```

**Response:**
```json
{
  "success": true,
  "message": "Site created successfully",
  "site": {
    "id": 2,
    "name": "My New Site",
    "domain": "mynewsite.mewayz.com",
    "status": "active",
    "template": "professional",
    "created_at": "2025-01-16T12:00:00Z"
  }
}
```

### Get Site Details
```http
GET /api/sites/{id}
```

**Response:**
```json
{
  "success": true,
  "site": {
    "id": 1,
    "name": "My Portfolio",
    "domain": "myportfolio.mewayz.com",
    "status": "active",
    "template": "professional",
    "settings": {
      "theme": "dark",
      "layout": "modern",
      "custom_css": "",
      "analytics_enabled": true
    },
    "stats": {
      "total_visits": 1250,
      "unique_visitors": 890,
      "page_views": 3400,
      "bounce_rate": 0.35
    },
    "created_at": "2025-01-16T12:00:00Z",
    "updated_at": "2025-01-16T12:00:00Z"
  }
}
```

### Update Site
```http
PUT /api/sites/{id}
```

**Request:**
```json
{
  "name": "Updated Portfolio",
  "settings": {
    "theme": "light",
    "layout": "classic"
  }
}
```

### Delete Site
```http
DELETE /api/sites/{id}
```

**Response:**
```json
{
  "success": true,
  "message": "Site deleted successfully"
}
```

## 📸 Instagram Management

### Get Instagram Accounts
```http
GET /api/instagram/accounts
```

**Response:**
```json
{
  "success": true,
  "accounts": [
    {
      "id": 1,
      "username": "myaccount",
      "followers": 15000,
      "following": 500,
      "posts": 250,
      "engagement_rate": 0.045,
      "status": "active",
      "connected_at": "2025-01-16T12:00:00Z"
    }
  ]
}
```

### Schedule Instagram Post
```http
POST /api/instagram/schedule
```

**Request:**
```json
{
  "account_id": 1,
  "content": "Check out our new product! #newproduct #launch",
  "media_urls": [
    "https://example.com/image1.jpg",
    "https://example.com/image2.jpg"
  ],
  "scheduled_at": "2025-01-17T10:00:00Z",
  "hashtags": ["#newproduct", "#launch", "#business"]
}
```

**Response:**
```json
{
  "success": true,
  "message": "Post scheduled successfully",
  "post": {
    "id": 123,
    "account_id": 1,
    "content": "Check out our new product! #newproduct #launch",
    "scheduled_at": "2025-01-17T10:00:00Z",
    "status": "scheduled"
  }
}
```

### Get Instagram Analytics
```http
GET /api/instagram/analytics
```

**Query Parameters:**
- `account_id` (optional): Filter by account
- `period` (optional): `7d`, `30d`, `90d` (default: `30d`)
- `metrics` (optional): `engagement`, `reach`, `impressions`

**Response:**
```json
{
  "success": true,
  "analytics": {
    "account_id": 1,
    "period": "30d",
    "metrics": {
      "posts": 15,
      "likes": 2500,
      "comments": 180,
      "shares": 45,
      "saves": 320,
      "reach": 12000,
      "impressions": 18000,
      "engagement_rate": 0.048
    },
    "top_posts": [
      {
        "id": "post_123",
        "content": "Our best performing post",
        "likes": 450,
        "comments": 32,
        "engagement_rate": 0.085
      }
    ]
  }
}
```

## 📊 Analytics

### Get Dashboard Analytics
```http
GET /api/analytics/dashboard
```

**Response:**
```json
{
  "success": true,
  "analytics": {
    "overview": {
      "total_sites": 5,
      "total_visits": 15000,
      "total_revenue": 1250.00,
      "active_subscriptions": 3
    },
    "traffic": {
      "today": 150,
      "yesterday": 180,
      "this_week": 1200,
      "this_month": 4500
    },
    "top_sites": [
      {
        "site_id": 1,
        "name": "My Portfolio",
        "visits": 8000,
        "revenue": 650.00
      }
    ],
    "recent_activity": [
      {
        "type": "site_created",
        "description": "New site created: My Blog",
        "timestamp": "2025-01-16T12:00:00Z"
      }
    ]
  }
}
```

### Get Site Analytics
```http
GET /api/analytics/sites/{id}
```

**Response:**
```json
{
  "success": true,
  "analytics": {
    "site_id": 1,
    "period": "30d",
    "metrics": {
      "visits": 8000,
      "unique_visitors": 6200,
      "page_views": 15000,
      "bounce_rate": 0.32,
      "average_session_duration": 185,
      "conversion_rate": 0.025
    },
    "traffic_sources": {
      "direct": 45,
      "social": 35,
      "search": 15,
      "referral": 5
    },
    "top_pages": [
      {
        "path": "/",
        "visits": 3000,
        "unique_visitors": 2800
      }
    ]
  }
}
```

## 📧 Email Marketing

### Get Email Campaigns
```http
GET /api/email/campaigns
```

**Response:**
```json
{
  "success": true,
  "campaigns": [
    {
      "id": 1,
      "name": "Monthly Newsletter",
      "subject": "Our Latest Updates",
      "status": "sent",
      "sent_at": "2025-01-16T12:00:00Z",
      "recipients": 1500,
      "open_rate": 0.28,
      "click_rate": 0.045
    }
  ]
}
```

### Create Email Campaign
```http
POST /api/email/campaigns
```

**Request:**
```json
{
  "name": "Product Launch",
  "subject": "Exciting New Product Launch!",
  "content": "<html>...</html>",
  "recipients": "all_subscribers",
  "schedule_at": "2025-01-17T10:00:00Z"
}
```

## 🔍 Error Handling

### Standard Error Response
```json
{
  "success": false,
  "error": "Validation failed",
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

### HTTP Status Codes
- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `429` - Too Many Requests
- `500` - Internal Server Error

## 📚 Rate Limiting

### Rate Limits
- **API Endpoints**: 60 requests per minute
- **Payment Endpoints**: 10 requests per minute
- **Webhook Endpoints**: 1000 requests per minute

### Rate Limit Headers
```http
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1642339200
```

## 🔧 SDK Examples

### PHP SDK
```php
<?php
use GuzzleHttp\Client;

$client = new Client([
    'base_uri' => 'https://api.mewayz.com',
    'headers' => [
        'Authorization' => 'Bearer ' . $token,
        'Content-Type' => 'application/json',
    ]
]);

// Create checkout session
$response = $client->post('/api/payments/checkout/session', [
    'json' => [
        'package_id' => 'professional',
        'success_url' => 'https://example.com/success',
        'cancel_url' => 'https://example.com/cancel'
    ]
]);
```

### JavaScript SDK
```javascript
const MewayzAPI = {
    baseURL: 'https://api.mewayz.com',
    token: 'your-api-token',
    
    async request(endpoint, options = {}) {
        const response = await fetch(`${this.baseURL}${endpoint}`, {
            ...options,
            headers: {
                'Authorization': `Bearer ${this.token}`,
                'Content-Type': 'application/json',
                ...options.headers
            }
        });
        return response.json();
    },
    
    async createCheckoutSession(packageId, successUrl, cancelUrl) {
        return this.request('/api/payments/checkout/session', {
            method: 'POST',
            body: JSON.stringify({
                package_id: packageId,
                success_url: successUrl,
                cancel_url: cancelUrl
            })
        });
    }
};
```

### Python SDK
```python
import requests

class MewayzAPI:
    def __init__(self, token, base_url='https://api.mewayz.com'):
        self.token = token
        self.base_url = base_url
        self.headers = {
            'Authorization': f'Bearer {token}',
            'Content-Type': 'application/json'
        }
    
    def create_checkout_session(self, package_id, success_url, cancel_url):
        url = f'{self.base_url}/api/payments/checkout/session'
        data = {
            'package_id': package_id,
            'success_url': success_url,
            'cancel_url': cancel_url
        }
        response = requests.post(url, json=data, headers=self.headers)
        return response.json()
```

## 🧪 Testing

### Test Endpoints
```bash
# Health check
curl -X GET https://api.mewayz.com/api/health

# Get packages
curl -X GET https://api.mewayz.com/api/payments/packages

# Create checkout session
curl -X POST https://api.mewayz.com/api/payments/checkout/session \
  -H "Content-Type: application/json" \
  -d '{
    "package_id": "starter",
    "success_url": "https://example.com/success",
    "cancel_url": "https://example.com/cancel"
  }'
```

### Test Stripe Integration
```bash
# Test Stripe webhook
curl -X POST https://api.mewayz.com/api/webhook/stripe \
  -H "Stripe-Signature: t=1234567890,v1=test_signature" \
  -H "Content-Type: application/json" \
  -d '{
    "type": "checkout.session.completed",
    "data": {
      "object": {
        "id": "cs_test_123",
        "payment_status": "paid"
      }
    }
  }'
```

## 📞 Support

### API Support
- **Documentation**: This document
- **Status Page**: https://status.mewayz.com
- **Support Email**: api-support@mewayz.com
- **GitHub Issues**: https://github.com/your-org/mewayz/issues

### API Version
- **Current Version**: v1
- **Versioning**: Semantic versioning
- **Deprecation Policy**: 6 months notice

---

**Last Updated**: January 16, 2025  
**API Version**: v1  
**Status**: Production Ready