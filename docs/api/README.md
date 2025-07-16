# 📡 Mewayz API Documentation

Welcome to the Mewayz API documentation. This comprehensive guide covers all API endpoints, authentication, and integration patterns.

## 🚀 Quick Start

### Base URL
```
Production: https://api.mewayz.com
Staging: https://staging-api.mewayz.com
Development: http://localhost:8000/api
```

### Authentication
All API requests require authentication using Laravel Sanctum tokens.

```bash
# Get authentication token
curl -X POST https://api.mewayz.com/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "user@example.com", "password": "password"}'

# Use token in requests
curl -X GET https://api.mewayz.com/user \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

## 📋 API Overview

### Rate Limiting
- **Authenticated requests**: 1000 per hour
- **Unauthenticated requests**: 100 per hour
- **Webhook endpoints**: 500 per hour

### Response Format
All API responses follow this structure:

```json
{
  "success": true,
  "data": {},
  "message": "Operation successful",
  "meta": {
    "pagination": {},
    "filters": {},
    "sorting": {}
  }
}
```

### Error Handling
```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "The given data was invalid.",
    "details": {
      "email": ["The email field is required."]
    }
  }
}
```

## 🔐 Authentication Endpoints

### Login
```http
POST /auth/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}
```

### Register
```http
POST /auth/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password",
  "password_confirmation": "password"
}
```

### Logout
```http
POST /auth/logout
Authorization: Bearer {token}
```

### Refresh Token
```http
POST /auth/refresh
Authorization: Bearer {token}
```

### User Profile
```http
GET /auth/me
Authorization: Bearer {token}
```

## 👥 User Management

### Get User Profile
```http
GET /auth/me
Authorization: Bearer {token}
```

### Update Profile
```http
PUT /auth/profile
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "John Doe Updated",
  "email": "john.updated@example.com"
}
```

## 🏢 Workspace Management

### List Workspaces
```http
GET /workspaces
Authorization: Bearer {token}
```

### Create Workspace
```http
POST /workspaces
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "My Workspace",
  "description": "Workspace description"
}
```

### Get Workspace
```http
GET /workspaces/{id}
Authorization: Bearer {token}
```

### Update Workspace
```http
PUT /workspaces/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Updated Workspace",
  "description": "Updated description"
}
```

### Delete Workspace
```http
DELETE /workspaces/{id}
Authorization: Bearer {token}
```

## 🔗 Bio Sites API

### List Bio Sites
```http
GET /bio-sites
Authorization: Bearer {token}
```

### Create Bio Site
```http
POST /bio-sites
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "My Bio Site",
  "address": "myhandle",
  "bio": "Welcome to my bio site",
  "settings": {
    "theme": "modern",
    "colors": {
      "primary": "#000000",
      "secondary": "#ffffff"
    }
  }
}
```

### Get Bio Site
```http
GET /bio-sites/{id}
Authorization: Bearer {token}
```

### Update Bio Site
```http
PUT /bio-sites/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Updated Bio Site",
  "bio": "Updated bio description"
}
```

### Delete Bio Site
```http
DELETE /bio-sites/{id}
Authorization: Bearer {token}
```

### Bio Site Analytics
```http
GET /bio-sites/{id}/analytics
Authorization: Bearer {token}
```

### Bio Site Links
```http
GET /bio-sites/{bioSiteId}/links
Authorization: Bearer {token}
```

```http
POST /bio-sites/{bioSiteId}/links
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "My Website",
  "url": "https://example.com",
  "description": "Visit my website",
  "icon": "website",
  "position": 1
}
```

## 📱 Social Media API

### Get Connected Accounts
```http
GET /social-media/accounts
Authorization: Bearer {token}
```

### Connect Social Account
```http
POST /social-media/accounts/connect
Authorization: Bearer {token}
Content-Type: application/json

{
  "provider": "instagram",
  "access_token": "token_here"
}
```

### Get Social Media Posts
```http
GET /social-media/posts
Authorization: Bearer {token}
```

### Create Social Media Post
```http
POST /social-media/posts
Authorization: Bearer {token}
Content-Type: application/json

{
  "content": "Hello world!",
  "platforms": ["instagram", "facebook"],
  "media": ["image1.jpg", "image2.jpg"],
  "scheduled_at": "2025-01-20T10:00:00Z"
}
```

### Get Social Media Analytics
```http
GET /social-media/analytics
Authorization: Bearer {token}
```

## 📷 Instagram API

### Get Instagram Accounts
```http
GET /instagram/accounts
Authorization: Bearer {token}
```

### Get Instagram Posts
```http
GET /instagram/posts
Authorization: Bearer {token}
```

### Create Instagram Post
```http
POST /instagram/posts
Authorization: Bearer {token}
Content-Type: application/json

{
  "caption": "Amazing content!",
  "media_type": "IMAGE",
  "media_url": "https://example.com/image.jpg",
  "scheduled_at": "2025-01-20T15:00:00Z"
}
```

### Get Instagram Analytics
```http
GET /instagram/analytics
Authorization: Bearer {token}
```

### Hashtag Research
```http
GET /instagram/hashtag-analysis
Authorization: Bearer {token}
```

### Competitor Analysis
```http
POST /instagram/advanced-competitor-analysis
Authorization: Bearer {token}
Content-Type: application/json

{
  "competitor_username": "competitor_handle",
  "analysis_type": "content_strategy"
}
```

## 🛍️ E-commerce API

### Get Products
```http
GET /ecommerce/products
Authorization: Bearer {token}
```

### Create Product
```http
POST /ecommerce/products
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Product Name",
  "description": "Product description",
  "price": 29.99,
  "inventory": 100,
  "images": ["image1.jpg", "image2.jpg"],
  "category": "electronics"
}
```

### Get Product
```http
GET /ecommerce/products/{id}
Authorization: Bearer {token}
```

### Update Product
```http
PUT /ecommerce/products/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Updated Product Name",
  "price": 39.99
}
```

### Delete Product
```http
DELETE /ecommerce/products/{id}
Authorization: Bearer {token}
```

### Get Orders
```http
GET /ecommerce/orders
Authorization: Bearer {token}
```

### Get Order
```http
GET /ecommerce/orders/{id}
Authorization: Bearer {token}
```

### Update Order Status
```http
PUT /ecommerce/orders/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "status": "shipped",
  "tracking_number": "1234567890"
}
```

## 📚 Courses API

### List Courses
```http
GET /courses
Authorization: Bearer {token}
```

### Create Course
```http
POST /courses
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Course Name",
  "description": "Course description",
  "price": 99.99,
  "status": "published",
  "course_level": "beginner"
}
```

### Get Course
```http
GET /courses/{id}
Authorization: Bearer {token}
```

### Update Course
```http
PUT /courses/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Updated Course Name",
  "price": 129.99
}
```

### Delete Course
```http
DELETE /courses/{id}
Authorization: Bearer {token}
```

### Get Course Lessons
```http
GET /courses/{id}/lessons
Authorization: Bearer {token}
```

### Create Course Lesson
```http
POST /courses/{id}/lessons
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "Lesson Title",
  "content": "Lesson content",
  "video_url": "https://example.com/video.mp4",
  "duration": 1800,
  "position": 1
}
```

### Get Course Students
```http
GET /courses/{id}/students
Authorization: Bearer {token}
```

### Enroll Student
```http
POST /courses/{id}/enroll
Authorization: Bearer {token}
Content-Type: application/json

{
  "student_email": "student@example.com"
}
```

## 📧 Email Marketing API

### List Campaigns
```http
GET /email-marketing/campaigns
Authorization: Bearer {token}
```

### Create Campaign
```http
POST /email-marketing/campaigns
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Campaign Name",
  "subject": "Email Subject",
  "content": "Email content",
  "recipient_lists": ["list1", "list2"],
  "scheduled_at": "2025-01-20T10:00:00Z"
}
```

### Get Campaign
```http
GET /email-marketing/campaigns/{id}
Authorization: Bearer {token}
```

### Send Campaign
```http
POST /email-marketing/campaigns/{id}/send
Authorization: Bearer {token}
```

### Get Email Templates
```http
GET /email-marketing/templates
Authorization: Bearer {token}
```

### Create Email Template
```http
POST /email-marketing/templates
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Template Name",
  "subject": "Template Subject",
  "content": "Template content",
  "category": "newsletter"
}
```

### Get Email Lists
```http
GET /email-marketing/lists
Authorization: Bearer {token}
```

### Get Subscribers
```http
GET /email-marketing/subscribers
Authorization: Bearer {token}
```

### Campaign Analytics
```http
GET /email-marketing/campaigns/{id}/analytics
Authorization: Bearer {token}
```

## 👥 CRM API

### Get Contacts
```http
GET /crm/contacts
Authorization: Bearer {token}
```

### Create Contact
```http
POST /crm/contacts
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Contact Name",
  "email": "contact@example.com",
  "phone": "+1234567890",
  "company": "Company Name",
  "tags": ["lead", "potential"]
}
```

### Get Contact
```http
GET /crm/contacts/{id}
Authorization: Bearer {token}
```

### Update Contact
```http
PUT /crm/contacts/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Updated Contact Name",
  "status": "customer"
}
```

### Delete Contact
```http
DELETE /crm/contacts/{id}
Authorization: Bearer {token}
```

### Get Leads
```http
GET /crm/leads
Authorization: Bearer {token}
```

### Create Lead
```http
POST /crm/leads
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Lead Name",
  "email": "lead@example.com",
  "source": "website",
  "status": "new",
  "value": 1000
}
```

### AI Lead Scoring
```http
GET /crm/ai-lead-scoring
Authorization: Bearer {token}
```

### Pipeline Management
```http
GET /crm/advanced-pipeline-management
Authorization: Bearer {token}
```

## 📊 Analytics API

### Get Analytics Overview
```http
GET /analytics
Authorization: Bearer {token}
```

### Get Traffic Analytics
```http
GET /analytics/traffic
Authorization: Bearer {token}
```

### Get Social Media Analytics
```http
GET /analytics/social-media
Authorization: Bearer {token}
```

### Get Bio Sites Analytics
```http
GET /analytics/bio-sites
Authorization: Bearer {token}
```

### Get E-commerce Analytics
```http
GET /analytics/ecommerce
Authorization: Bearer {token}
```

### Get Course Analytics
```http
GET /analytics/courses
Authorization: Bearer {token}
```

### Get Real-time Analytics
```http
GET /analytics/real-time
Authorization: Bearer {token}
```

### Export Analytics
```http
POST /analytics/export
Authorization: Bearer {token}
Content-Type: application/json

{
  "type": "csv",
  "date_range": "last_30_days",
  "metrics": ["views", "clicks", "conversions"]
}
```

## 🤖 AI API

### Get AI Services
```http
GET /ai/services
Authorization: Bearer {token}
```

### AI Chat
```http
POST /ai/chat
Authorization: Bearer {token}
Content-Type: application/json

{
  "message": "Help me write a product description",
  "context": "ecommerce"
}
```

### Generate Content
```http
POST /ai/generate-content
Authorization: Bearer {token}
Content-Type: application/json

{
  "type": "social_media_post",
  "prompt": "Create a post about our new product",
  "tone": "professional",
  "length": "short"
}
```

### Get AI Recommendations
```http
POST /ai/recommendations
Authorization: Bearer {token}
Content-Type: application/json

{
  "type": "content_optimization",
  "data": {
    "content": "Existing content to optimize"
  }
}
```

### Analyze Text
```http
POST /ai/analyze-text
Authorization: Bearer {token}
Content-Type: application/json

{
  "text": "Text to analyze",
  "analysis_type": "sentiment"
}
```

## 👥 Team Management API

### Get Team Members
```http
GET /team
Authorization: Bearer {token}
```

### Send Team Invitation
```http
POST /team/invite
Authorization: Bearer {token}
Content-Type: application/json

{
  "email": "newmember@example.com",
  "role": "editor",
  "permissions": ["read", "write"]
}
```

### Accept Invitation
```http
POST /team/invitation/{uuid}/accept
Authorization: Bearer {token}
```

### Update Member Role
```http
PUT /team/member/{id}/role
Authorization: Bearer {token}
Content-Type: application/json

{
  "role": "admin"
}
```

### Remove Team Member
```http
DELETE /team/member/{id}
Authorization: Bearer {token}
```

## 💳 Payment API

### Get Payment Packages
```http
GET /payments/packages
```

### Create Checkout Session
```http
POST /payments/checkout/session
Authorization: Bearer {token}
Content-Type: application/json

{
  "package_id": "premium_monthly",
  "success_url": "https://example.com/success",
  "cancel_url": "https://example.com/cancel"
}
```

### Get Checkout Status
```http
GET /payments/checkout/status/{sessionId}
Authorization: Bearer {token}
```

## 🔔 Webhooks

### Webhook Events
- `user.created`
- `user.updated`
- `bio_site.created`
- `bio_site.updated`
- `order.created`
- `order.updated`
- `payment.success`
- `payment.failed`
- `campaign.sent`
- `course.enrolled`

### Webhook Payload Example
```json
{
  "event": "bio_site.created",
  "data": {
    "id": "123",
    "name": "My Bio Site",
    "address": "myhandle",
    "user_id": "456"
  },
  "timestamp": "2025-01-20T10:00:00Z"
}
```

### Configure Webhooks
```http
POST /webhooks
Authorization: Bearer {token}
Content-Type: application/json

{
  "url": "https://yourapp.com/webhook",
  "events": ["bio_site.created", "order.created"],
  "secret": "your_webhook_secret"
}
```

## 📝 Error Codes

| Code | Description |
|------|-------------|
| 400 | Bad Request |
| 401 | Unauthorized |
| 403 | Forbidden |
| 404 | Not Found |
| 422 | Validation Error |
| 429 | Rate Limit Exceeded |
| 500 | Internal Server Error |

## 🔧 SDKs & Libraries

### Official SDKs
- **JavaScript/Node.js**: `npm install @mewayz/sdk`
- **Python**: `pip install mewayz-sdk`
- **PHP**: `composer require mewayz/sdk`

### Community SDKs
- **Ruby**: `gem install mewayz`
- **Go**: `go get github.com/mewayz/go-sdk`

### Usage Example (JavaScript)
```javascript
import { MewayzClient } from '@mewayz/sdk';

const client = new MewayzClient({
  token: 'your_api_token',
  baseUrl: 'https://api.mewayz.com'
});

// Get user profile
const profile = await client.auth.getProfile();

// Create bio site
const bioSite = await client.bioSites.create({
  name: 'My Bio Site',
  address: 'myhandle'
});
```

## 🛡️ Security

### API Key Management
- Store API keys securely
- Use environment variables
- Rotate keys regularly
- Never expose keys in client-side code

### Rate Limiting
- Implement exponential backoff
- Handle rate limit responses
- Monitor usage patterns

### Webhook Security
- Verify webhook signatures
- Use HTTPS endpoints
- Implement idempotency

---

**Need Help?**
- 📧 API Support: api@mewayz.com
- 📚 API Documentation: [docs.mewayz.com/api](https://docs.mewayz.com/api)
- 🔧 Status Page: [status.mewayz.com](https://status.mewayz.com)
- 💬 Developer Discord: [discord.gg/mewayz-dev](https://discord.gg/mewayz-dev)

**Last Updated**: January 2025  
**Version**: 1.0.0