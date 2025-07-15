# 📡 Mewayz Platform - API Reference

## API Overview

The Mewayz Platform provides a comprehensive RESTful API with 150+ endpoints across 25+ feature categories. All APIs follow consistent design patterns and return standardized JSON responses.

## Base Information

- **Base URL**: `https://api.mewayz.com` (Production) / `http://localhost:8001/api` (Development)
- **Version**: v1 (current)
- **Authentication**: Bearer Token (Laravel Sanctum)
- **Content-Type**: `application/json`
- **Rate Limiting**: 1000 requests per minute per user

## Authentication

### **Authentication Flow**
```
1. POST /api/auth/login → Returns access token
2. Include token in headers: Authorization: Bearer {token}
3. Use token for all subsequent API requests
```

### **Authentication Endpoints**

#### **User Login**
```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "user@example.com",
      "email_verified_at": "2025-01-01T00:00:00Z"
    },
    "token": "1|abcdef123456789",
    "expires_at": "2025-07-16T10:30:00Z"
  }
}
```

#### **User Registration**
```http
POST /api/auth/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "user@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

#### **Get Current User**
```http
GET /api/auth/me
Authorization: Bearer {token}
```

#### **Logout**
```http
POST /api/auth/logout
Authorization: Bearer {token}
```

## Core API Endpoints

### **Workspace Management**

#### **Get Workspace Setup Progress**
```http
GET /api/workspace-setup/current-step
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "current_step": 1,
    "total_steps": 6,
    "completed_steps": ["business_info"],
    "next_step": "feature_selection"
  }
}
```

#### **Get Main Business Goals**
```http
GET /api/workspace-setup/main-goals
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "goals": [
      {
        "id": "instagram_management",
        "title": "Instagram Management",
        "description": "Manage Instagram accounts and content",
        "icon": "instagram",
        "features": ["post_scheduling", "analytics", "hashtag_research"]
      },
      {
        "id": "link_in_bio",
        "title": "Link in Bio",
        "description": "Create professional bio pages",
        "icon": "link",
        "features": ["page_builder", "analytics", "custom_domains"]
      }
    ]
  }
}
```

#### **Save Main Goals Selection**
```http
POST /api/workspace-setup/main-goals
Authorization: Bearer {token}
Content-Type: application/json

{
  "goals": ["instagram_management", "link_in_bio"],
  "primary_goal": "instagram_management"
}
```

#### **Get Available Features**
```http
GET /api/workspace-setup/available-features
Authorization: Bearer {token}
```

#### **Save Feature Selection**
```http
POST /api/workspace-setup/feature-selection
Authorization: Bearer {token}
Content-Type: application/json

{
  "features": ["post_scheduling", "analytics", "page_builder"],
  "plan": "professional"
}
```

### **Instagram Management**

#### **Get Instagram Accounts**
```http
GET /api/instagram-management/accounts
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "accounts": [
      {
        "id": 1,
        "username": "myaccount",
        "profile_picture": "https://example.com/profile.jpg",
        "followers_count": 1500,
        "following_count": 800,
        "posts_count": 120,
        "created_at": "2025-01-01T00:00:00Z"
      }
    ]
  }
}
```

#### **Add Instagram Account**
```http
POST /api/instagram-management/accounts
Authorization: Bearer {token}
Content-Type: application/json

{
  "username": "newaccount",
  "access_token": "instagram_access_token",
  "profile_data": {
    "bio": "My Instagram bio",
    "website": "https://mywebsite.com"
  }
}
```

#### **Get Instagram Posts**
```http
GET /api/instagram-management/posts?page=1&limit=10
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "posts": [
      {
        "id": 1,
        "account_id": 1,
        "content": "Check out this amazing post!",
        "media_url": "https://example.com/image.jpg",
        "hashtags": ["#amazing", "#post"],
        "scheduled_at": "2025-07-16T10:00:00Z",
        "posted_at": null,
        "engagement_stats": {
          "likes": 0,
          "comments": 0,
          "shares": 0
        }
      }
    ]
  },
  "meta": {
    "pagination": {
      "current_page": 1,
      "per_page": 10,
      "total": 50,
      "total_pages": 5
    }
  }
}
```

#### **Create Instagram Post**
```http
POST /api/instagram-management/posts
Authorization: Bearer {token}
Content-Type: application/json

{
  "account_id": 1,
  "content": "New post content with hashtags",
  "media_url": "https://example.com/image.jpg",
  "hashtags": ["#newpost", "#content"],
  "scheduled_at": "2025-07-16T15:00:00Z"
}
```

#### **Update Instagram Post**
```http
PUT /api/instagram-management/posts/1
Authorization: Bearer {token}
Content-Type: application/json

{
  "content": "Updated post content",
  "hashtags": ["#updated", "#content"],
  "scheduled_at": "2025-07-16T16:00:00Z"
}
```

#### **Delete Instagram Post**
```http
DELETE /api/instagram-management/posts/1
Authorization: Bearer {token}
```

#### **Hashtag Research**
```http
GET /api/instagram-management/hashtag-research?keyword=travel
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "suggestions": [
      {
        "hashtag": "#travel",
        "posts_count": 500000000,
        "difficulty_level": "high",
        "engagement_rate": 2.5,
        "related_tags": ["#vacation", "#adventure", "#wanderlust"]
      },
      {
        "hashtag": "#travelgram",
        "posts_count": 50000000,
        "difficulty_level": "medium",
        "engagement_rate": 4.2,
        "related_tags": ["#travel", "#photography", "#explore"]
      }
    ]
  }
}
```

#### **Instagram Analytics**
```http
GET /api/instagram-management/analytics?account_id=1&period=7d
Authorization: Bearer {token}
```

### **Bio Sites Management**

#### **Get Bio Sites**
```http
GET /api/bio-sites
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "bio_sites": [
      {
        "id": 1,
        "name": "My Bio Site",
        "subdomain": "mybio",
        "custom_domain": "mybio.com",
        "theme": "modern",
        "bio": "Welcome to my bio page!",
        "links_count": 5,
        "total_clicks": 1250,
        "created_at": "2025-01-01T00:00:00Z"
      }
    ]
  }
}
```

#### **Create Bio Site**
```http
POST /api/bio-sites
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "My New Bio Site",
  "subdomain": "mynewbio",
  "theme": "professional",
  "bio": "Professional bio page for my business",
  "settings": {
    "show_social_links": true,
    "enable_analytics": true,
    "custom_css": ""
  }
}
```

#### **Update Bio Site**
```http
PUT /api/bio-sites/1
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Updated Bio Site",
  "bio": "Updated bio content",
  "theme": "dark"
}
```

#### **Get Bio Site Links**
```http
GET /api/bio-sites/1/links
Authorization: Bearer {token}
```

#### **Add Bio Site Link**
```http
POST /api/bio-sites/1/links
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "My Website",
  "url": "https://mywebsite.com",
  "description": "Visit my main website",
  "icon": "website",
  "order": 1
}
```

### **CRM Management**

#### **Get Contacts**
```http
GET /api/crm/contacts?page=1&limit=20&search=john
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "contacts": [
      {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "phone": "+1234567890",
        "company": "Example Corp",
        "tags": ["lead", "interested"],
        "score": 85,
        "source": "website",
        "last_interaction": "2025-07-15T09:00:00Z",
        "created_at": "2025-07-01T00:00:00Z"
      }
    ]
  }
}
```

#### **Create Contact**
```http
POST /api/crm/contacts
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Jane Smith",
  "email": "jane@example.com",
  "phone": "+1234567891",
  "company": "Smith Industries",
  "tags": ["prospect", "high-value"],
  "custom_fields": {
    "industry": "Technology",
    "budget": "$10,000"
  }
}
```

#### **Update Contact**
```http
PUT /api/crm/contacts/1
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "John Doe Updated",
  "tags": ["customer", "converted"],
  "score": 95
}
```

### **E-commerce Management**

#### **Get Products**
```http
GET /api/ecommerce/products?category=electronics&status=active
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "products": [
      {
        "id": 1,
        "name": "Wireless Headphones",
        "description": "High-quality wireless headphones",
        "price": 99.99,
        "cost": 45.00,
        "stock": 50,
        "category": "electronics",
        "images": ["https://example.com/headphones.jpg"],
        "sku": "WH-001",
        "is_active": true,
        "created_at": "2025-01-01T00:00:00Z"
      }
    ]
  }
}
```

#### **Create Product**
```http
POST /api/ecommerce/products
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Smart Watch",
  "description": "Feature-rich smart watch",
  "price": 299.99,
  "cost": 150.00,
  "stock": 25,
  "category": "electronics",
  "images": ["https://example.com/watch.jpg"],
  "sku": "SW-001",
  "seo_settings": {
    "meta_title": "Smart Watch - Best Features",
    "meta_description": "Discover our feature-rich smart watch"
  }
}
```

#### **Get Orders**
```http
GET /api/ecommerce/orders?status=pending&date_from=2025-07-01
Authorization: Bearer {token}
```

### **Course Management**

#### **Get Courses**
```http
GET /api/courses
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "courses": [
      {
        "id": 1,
        "title": "Web Development Basics",
        "description": "Learn the fundamentals of web development",
        "price": 199.99,
        "thumbnail": "https://example.com/course-thumb.jpg",
        "category": "programming",
        "level": "beginner",
        "duration": "8 weeks",
        "lessons_count": 24,
        "students_count": 150,
        "rating": 4.8,
        "created_at": "2025-01-01T00:00:00Z"
      }
    ]
  }
}
```

#### **Create Course**
```http
POST /api/courses
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "Advanced JavaScript",
  "description": "Master advanced JavaScript concepts",
  "price": 299.99,
  "category": "programming",
  "level": "advanced",
  "thumbnail": "https://example.com/js-course.jpg"
}
```

#### **Get Course Lessons**
```http
GET /api/courses/1/lessons
Authorization: Bearer {token}
```

### **Email Marketing**

#### **Get Email Campaigns**
```http
GET /api/email-marketing/campaigns
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "campaigns": [
      {
        "id": 1,
        "name": "Welcome Series",
        "subject": "Welcome to our platform!",
        "from_name": "Mewayz Team",
        "from_email": "hello@mewayz.com",
        "status": "sent",
        "recipients_count": 500,
        "open_rate": 25.5,
        "click_rate": 8.2,
        "sent_at": "2025-07-15T10:00:00Z",
        "created_at": "2025-07-14T00:00:00Z"
      }
    ]
  }
}
```

#### **Create Email Campaign**
```http
POST /api/email-marketing/campaigns
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Product Launch",
  "subject": "Exciting new product launch!",
  "content": "<h1>Check out our new product</h1><p>We're excited to announce...</p>",
  "from_name": "Mewayz Team",
  "from_email": "hello@mewayz.com",
  "template_id": 1,
  "segment_id": 2
}
```

### **Payment Processing**

#### **Get Payment Packages**
```http
GET /api/payments/packages
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "packages": [
      {
        "id": "starter",
        "name": "Starter Package",
        "price": 9.99,
        "currency": "USD",
        "billing_cycle": "monthly",
        "features": ["5 bio sites", "Basic analytics", "Email support"],
        "stripe_price_id": "price_1234567890"
      },
      {
        "id": "professional",
        "name": "Professional Package",
        "price": 29.99,
        "currency": "USD",
        "billing_cycle": "monthly",
        "features": ["Unlimited bio sites", "Advanced analytics", "Priority support"],
        "stripe_price_id": "price_0987654321"
      }
    ]
  }
}
```

#### **Create Checkout Session**
```http
POST /api/payments/checkout/session
Authorization: Bearer {token}
Content-Type: application/json

{
  "package_id": "professional",
  "billing_cycle": "monthly",
  "success_url": "https://mysite.com/success",
  "cancel_url": "https://mysite.com/cancel"
}
```

### **Analytics**

#### **Get Analytics Overview**
```http
GET /api/analytics?period=7d
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "overview": {
      "total_visitors": 1250,
      "unique_visitors": 890,
      "page_views": 3500,
      "bounce_rate": 45.2,
      "avg_session_duration": 180,
      "conversion_rate": 3.8
    },
    "traffic_sources": {
      "direct": 45.5,
      "social": 30.2,
      "search": 18.3,
      "referral": 6.0
    },
    "top_pages": [
      {
        "page": "/bio/johndoe",
        "views": 450,
        "unique_views": 320,
        "bounce_rate": 38.2
      }
    ]
  }
}
```

#### **Get Bio Site Analytics**
```http
GET /api/analytics/bio-sites/1?period=30d
Authorization: Bearer {token}
```

#### **Get E-commerce Analytics**
```http
GET /api/analytics/ecommerce?period=30d
Authorization: Bearer {token}
```

## Error Handling

### **Error Response Format**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  },
  "error_code": "VALIDATION_ERROR",
  "request_id": "req_123456789"
}
```

### **Common Error Codes**
- `400 Bad Request`: Invalid request format or parameters
- `401 Unauthorized`: Missing or invalid authentication token
- `403 Forbidden`: User doesn't have permission for this resource
- `404 Not Found`: Resource not found
- `422 Unprocessable Entity`: Validation errors
- `429 Too Many Requests`: Rate limit exceeded
- `500 Internal Server Error`: Server error

## Rate Limiting

### **Rate Limits**
- **General API**: 1000 requests per minute per user
- **Authentication**: 10 requests per minute per IP
- **File Upload**: 50 requests per hour per user
- **Email Sending**: 100 requests per hour per user

### **Rate Limit Headers**
```http
X-RateLimit-Limit: 1000
X-RateLimit-Remaining: 999
X-RateLimit-Reset: 1625097600
```

## Webhooks

### **Stripe Webhook**
```http
POST /api/webhook/stripe
Content-Type: application/json
Stripe-Signature: {signature}

{
  "id": "evt_123456789",
  "object": "event",
  "type": "payment_intent.succeeded",
  "data": {
    "object": {
      "id": "pi_123456789",
      "amount": 2999,
      "currency": "usd",
      "status": "succeeded"
    }
  }
}
```

## SDK Examples

### **JavaScript/Node.js**
```javascript
const axios = require('axios');

const client = axios.create({
  baseURL: 'https://api.mewayz.com',
  headers: {
    'Authorization': 'Bearer YOUR_TOKEN',
    'Content-Type': 'application/json'
  }
});

// Get user profile
const user = await client.get('/auth/me');

// Create bio site
const bioSite = await client.post('/bio-sites', {
  name: 'My Bio Site',
  subdomain: 'mybio',
  theme: 'modern'
});
```

### **PHP**
```php
<?php
use GuzzleHttp\Client;

$client = new Client([
    'base_uri' => 'https://api.mewayz.com',
    'headers' => [
        'Authorization' => 'Bearer YOUR_TOKEN',
        'Content-Type' => 'application/json'
    ]
]);

// Get user profile
$response = $client->get('/auth/me');
$user = json_decode($response->getBody(), true);

// Create bio site
$response = $client->post('/bio-sites', [
    'json' => [
        'name' => 'My Bio Site',
        'subdomain' => 'mybio',
        'theme' => 'modern'
    ]
]);
```

### **Python**
```python
import requests

class MewayzAPI:
    def __init__(self, token):
        self.base_url = 'https://api.mewayz.com'
        self.headers = {
            'Authorization': f'Bearer {token}',
            'Content-Type': 'application/json'
        }
    
    def get_user(self):
        response = requests.get(f'{self.base_url}/auth/me', headers=self.headers)
        return response.json()
    
    def create_bio_site(self, name, subdomain, theme):
        data = {
            'name': name,
            'subdomain': subdomain,
            'theme': theme
        }
        response = requests.post(f'{self.base_url}/bio-sites', json=data, headers=self.headers)
        return response.json()
```

---

**API Reference Documentation**
*Mewayz Platform - Version 2.0*
*150+ endpoints across 25+ feature categories*
*Last Updated: July 15, 2025*