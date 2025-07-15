# 📡 Mewayz Platform API Documentation

*Complete API Reference for Mewayz Platform*

## 📋 Overview

The Mewayz Platform provides a comprehensive RESTful API that powers all business functions. This documentation covers all 40+ API endpoints across authentication, social media, bio sites, CRM, e-commerce, courses, email marketing, and analytics.

### API Base URL
```
http://localhost:8001/api
```

### Authentication
All API endpoints (except public endpoints) require authentication using Laravel Sanctum tokens.

**Authorization Header:**
```
Authorization: Bearer {token}
```

## 🔐 Authentication Endpoints

### Register User
```http
POST /api/auth/register
```

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "created_at": "2025-07-15T10:30:00Z"
    },
    "token": "1|abc123..."
  }
}
```

### Login User
```http
POST /api/auth/login
```

**Request Body:**
```json
{
  "email": "john@example.com",
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
      "email": "john@example.com"
    },
    "token": "1|abc123..."
  }
}
```

### Enable Two-Factor Authentication
```http
POST /api/auth/2fa/enable
```

**Response:**
```json
{
  "success": true,
  "message": "Two-factor authentication enabled",
  "data": {
    "qr_code": "data:image/svg+xml;base64,PHN2ZyB3aWR0aD0i...",
    "secret": "JBSWY3DPEHPK3PXP",
    "recovery_codes": [
      "8f4e1c1b",
      "2a9d7e3f",
      "5c1b8f4e"
    ]
  }
}
```

## 🏢 Workspace Management

### Get Workspaces
```http
GET /api/workspaces
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "My Workspace",
      "description": "Primary workspace",
      "logo": "https://example.com/logo.png",
      "members_count": 5,
      "created_at": "2025-07-15T10:30:00Z"
    }
  ]
}
```

### Create Workspace
```http
POST /api/workspaces
```

**Request Body:**
```json
{
  "name": "New Workspace",
  "description": "Description of the workspace"
}
```

## 📱 Social Media Management

### Get Social Media Accounts
```http
GET /api/social-media/accounts
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "platform": "instagram",
      "username": "myaccount",
      "is_connected": true,
      "followers_count": 1500,
      "last_sync": "2025-07-15T10:30:00Z"
    }
  ]
}
```

### Schedule Social Media Post
```http
POST /api/social-media/schedule
```

**Request Body:**
```json
{
  "platform": "instagram",
  "content": "Post content here",
  "media_urls": ["https://example.com/image.jpg"],
  "scheduled_at": "2025-07-15T12:00:00Z"
}
```

### Get Social Media Analytics
```http
GET /api/social-media/analytics
```

**Query Parameters:**
- `platform` (optional): Filter by platform
- `period` (optional): Time period (7d, 30d, 90d)

**Response:**
```json
{
  "success": true,
  "data": {
    "total_posts": 45,
    "total_likes": 2500,
    "total_comments": 180,
    "engagement_rate": 4.2,
    "platforms": {
      "instagram": {
        "posts": 25,
        "likes": 1500,
        "comments": 120
      }
    }
  }
}
```

## 📈 Instagram Intelligence

### Get Instagram Competitor Analysis
```http
GET /api/instagram/competitor-analysis
```

**Query Parameters:**
- `competitors[]`: Array of competitor usernames
- `period`: Analysis period (7d, 30d, 90d)

**Response:**
```json
{
  "success": true,
  "data": {
    "competitors": [
      {
        "username": "competitor1",
        "followers_count": 5000,
        "engagement_rate": 3.5,
        "post_frequency": 1.2,
        "top_hashtags": ["#business", "#marketing"]
      }
    ],
    "insights": {
      "opportunities": ["Post more frequently", "Use trending hashtags"],
      "threats": ["Competitor growing faster"]
    }
  }
}
```

### Get Hashtag Analysis
```http
GET /api/instagram/hashtag-analysis
```

**Query Parameters:**
- `hashtags[]`: Array of hashtags to analyze
- `period`: Analysis period

**Response:**
```json
{
  "success": true,
  "data": {
    "hashtags": [
      {
        "hashtag": "#business",
        "posts_count": 1500000,
        "engagement_rate": 2.8,
        "difficulty": "medium",
        "trending_score": 85
      }
    ],
    "recommendations": [
      "#businesstips",
      "#entrepreneur",
      "#marketing"
    ]
  }
}
```

## 🔗 Bio Sites Management

### Get Bio Sites
```http
GET /api/bio-sites
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "My Bio Site",
      "subdomain": "mysite",
      "theme": "minimal",
      "is_active": true,
      "views_count": 1250,
      "clicks_count": 85,
      "created_at": "2025-07-15T10:30:00Z"
    }
  ]
}
```

### Create Bio Site
```http
POST /api/bio-sites
```

**Request Body:**
```json
{
  "name": "My New Bio Site",
  "subdomain": "mynewsite",
  "theme": "minimal",
  "bio": "Welcome to my bio site",
  "profile_image": "https://example.com/profile.jpg"
}
```

### Get Bio Site Analytics
```http
GET /api/bio-sites/{id}/analytics
```

**Query Parameters:**
- `period`: Time period (7d, 30d, 90d)

**Response:**
```json
{
  "success": true,
  "data": {
    "views": {
      "total": 1250,
      "today": 45,
      "chart_data": [
        {"date": "2025-07-15", "views": 45},
        {"date": "2025-07-14", "views": 52}
      ]
    },
    "clicks": {
      "total": 85,
      "today": 8,
      "links": [
        {"url": "https://example.com", "clicks": 25},
        {"url": "https://shop.example.com", "clicks": 15}
      ]
    }
  }
}
```

## 👥 CRM Management

### Get Contacts
```http
GET /api/crm/contacts
```

**Query Parameters:**
- `page`: Page number
- `search`: Search query
- `tags[]`: Filter by tags

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
        "tags": ["customer", "premium"],
        "created_at": "2025-07-15T10:30:00Z"
      }
    ]
  }
}
```

### Get Leads
```http
GET /api/crm/leads
```

**Query Parameters:**
- `status`: Filter by status (new, contacted, qualified, converted)
- `score_min`: Minimum lead score

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "score": 85,
      "status": "qualified",
      "source": "website",
      "created_at": "2025-07-15T10:30:00Z"
    }
  ]
}
```

## 📧 Email Marketing

### Get Email Campaigns
```http
GET /api/email-marketing/campaigns
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Welcome Campaign",
      "subject": "Welcome to our platform",
      "status": "sent",
      "recipients_count": 1500,
      "open_rate": 28.5,
      "click_rate": 4.2,
      "sent_at": "2025-07-15T10:30:00Z"
    }
  ]
}
```

### Get Email Templates
```http
GET /api/email-marketing/templates
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Welcome Template",
      "category": "welcome",
      "content": "<html>Template content</html>",
      "preview_url": "https://example.com/preview/1"
    }
  ]
}
```

## 🛍️ E-commerce Management

### Get Products
```http
GET /api/ecommerce/products
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Premium Course",
      "description": "Learn advanced techniques",
      "price": 99.99,
      "currency": "USD",
      "stock": 100,
      "category": "courses",
      "images": ["https://example.com/image1.jpg"],
      "status": "active",
      "created_at": "2025-07-15T10:30:00Z"
    }
  ]
}
```

### Get Orders
```http
GET /api/ecommerce/orders
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "order_number": "ORD-001",
      "customer_name": "John Doe",
      "customer_email": "john@example.com",
      "total": 99.99,
      "status": "completed",
      "created_at": "2025-07-15T10:30:00Z"
    }
  ]
}
```