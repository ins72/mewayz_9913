# Mewayz Platform - API Documentation

**Comprehensive API Reference**  
*By Mewayz Technologies Inc.*

---

## 📋 Table of Contents

1. [Overview](#overview)
2. [Authentication](#authentication)
3. [Rate Limiting](#rate-limiting)
4. [Error Handling](#error-handling)
5. [Core Endpoints](#core-endpoints)
6. [Business Feature APIs](#business-feature-apis)
7. [Webhooks](#webhooks)
8. [SDKs](#sdks)

---

## 🎯 Overview

The Mewayz API provides programmatic access to all platform features. It's built on RESTful principles and returns JSON responses. All API endpoints are prefixed with `/api` and served from the main Laravel backend.

### Base URL
```
Production: https://mewayz.com/api
Development: http://localhost:8001/api
```

### API Version
Current version: `v1`

### Content Type
All requests should include:
```
Content-Type: application/json
Accept: application/json
```

---

## 🔐 Authentication

### Laravel Sanctum

Mewayz uses Laravel Sanctum for API authentication. You can authenticate using:

1. **Personal Access Tokens** - For API access
2. **Session-based** - For web applications
3. **OAuth 2.0** - For third-party integrations

### Getting Access Tokens

#### Login Endpoint
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
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "user@example.com"
    },
    "token": "1|abcdef123456789..."
  }
}
```

#### Using Tokens

Include the token in the Authorization header:
```http
Authorization: Bearer 1|abcdef123456789...
```

### Registration

```http
POST /api/auth/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "user@example.com",
  "password": "password",
  "password_confirmation": "password"
}
```

---

## 🚦 Rate Limiting

### Limits
- **Authenticated requests**: 60 requests per minute
- **Unauthenticated requests**: 60 requests per minute
- **Authentication endpoints**: 5 requests per minute

### Headers
Rate limit information is included in response headers:
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1640995200
```

---

## ❌ Error Handling

### Standard Error Response
```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    "field": ["Validation error message"]
  }
}
```

### HTTP Status Codes
- **200**: Success
- **201**: Created
- **400**: Bad Request
- **401**: Unauthorized
- **403**: Forbidden
- **404**: Not Found
- **422**: Validation Error
- **429**: Rate Limit Exceeded
- **500**: Internal Server Error

---

## 🔧 Core Endpoints

### Health Check

```http
GET /api/health
```

**Response:**
```json
{
  "status": "ok",
  "message": "API is working",
  "timestamp": "2024-12-15T10:30:00Z"
}
```

### User Profile

#### Get Current User
```http
GET /api/auth/me
Authorization: Bearer {token}
```

#### Update Profile
```http
PUT /api/auth/profile
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Updated Name",
  "email": "updated@example.com"
}
```

### Two-Factor Authentication

#### Enable 2FA
```http
POST /api/auth/2fa/enable
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "secret": "ABC123DEF456",
    "qr_code": "data:image/png;base64,..."
  }
}
```

#### Verify 2FA
```http
POST /api/auth/2fa/verify
Authorization: Bearer {token}
Content-Type: application/json

{
  "code": "123456"
}
```

---

## 🏢 Business Feature APIs

### Workspace Management

#### List Workspaces
```http
GET /api/workspaces
Authorization: Bearer {token}
```

#### Create Workspace
```http
POST /api/workspaces
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "My Workspace",
  "description": "Workspace description"
}
```

#### Invite Team Member
```http
POST /api/workspaces/{id}/invite
Authorization: Bearer {token}
Content-Type: application/json

{
  "email": "member@example.com",
  "role": "member"
}
```

### Social Media Management

#### List Connected Accounts
```http
GET /api/social-media/accounts
Authorization: Bearer {token}
```

#### Connect Social Account
```http
POST /api/social-media/accounts/connect
Authorization: Bearer {token}
Content-Type: application/json

{
  "platform": "facebook",
  "access_token": "token_here"
}
```

#### Create Post
```http
POST /api/social-media/posts
Authorization: Bearer {token}
Content-Type: application/json

{
  "content": "Post content",
  "platforms": ["facebook", "twitter"],
  "scheduled_at": "2024-12-15T15:00:00Z"
}
```

#### Get Analytics
```http
GET /api/social-media/analytics
Authorization: Bearer {token}
```

### CRM System

#### List Contacts
```http
GET /api/crm/contacts
Authorization: Bearer {token}
```

**Query Parameters:**
- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 15)
- `search`: Search term
- `type`: Contact type (contact, lead)
- `status`: Contact status (hot, warm, cold)

#### Create Contact
```http
POST /api/crm/contacts
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "+1234567890",
  "company": "Example Corp",
  "type": "contact",
  "status": "warm"
}
```

#### Import Contacts
```http
POST /api/crm/contacts/import
Authorization: Bearer {token}
Content-Type: multipart/form-data

file: contacts.csv
```

### E-commerce

#### List Products
```http
GET /api/ecommerce/products
Authorization: Bearer {token}
```

#### Create Product
```http
POST /api/ecommerce/products
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Product Name",
  "description": "Product description",
  "price": 29.99,
  "stock": 100,
  "category": "electronics"
}
```

#### Get Store Analytics
```http
GET /api/ecommerce/analytics
Authorization: Bearer {token}
```

### Bio Sites

#### List Bio Sites
```http
GET /api/bio-sites
Authorization: Bearer {token}
```

#### Create Bio Site
```http
POST /api/bio-sites
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "My Bio Site",
  "url": "my-bio-site",
  "description": "Personal bio site",
  "theme": "dark"
}
```

#### Get Bio Site Analytics
```http
GET /api/bio-sites/{id}/analytics
Authorization: Bearer {token}
```

---

## 🔗 Webhooks

### Webhook Events

Mewayz can send webhook notifications for various events:

- `user.created`
- `workspace.created`
- `contact.created`
- `order.created`
- `post.published`

### Webhook Payload

```json
{
  "event": "contact.created",
  "data": {
    "id": 123,
    "name": "John Doe",
    "email": "john@example.com",
    "created_at": "2024-12-15T10:30:00Z"
  },
  "timestamp": "2024-12-15T10:30:00Z"
}
```

### Webhook Configuration

```http
POST /api/webhooks
Authorization: Bearer {token}
Content-Type: application/json

{
  "url": "https://your-app.com/webhooks",
  "events": ["contact.created", "order.created"],
  "secret": "your_webhook_secret"
}
```

---

## 🛠️ SDKs

### JavaScript SDK

```javascript
// Installation
npm install mewayz-sdk

// Usage
import MewayzSDK from 'mewayz-sdk';

const client = new MewayzSDK({
  baseURL: 'https://mewayz.com/api',
  token: 'your_token_here'
});

// Get user profile
const user = await client.auth.me();

// Create contact
const contact = await client.crm.createContact({
  name: 'John Doe',
  email: 'john@example.com'
});
```

### PHP SDK

```php
// Installation
composer require mewayz/php-sdk

// Usage
use Mewayz\SDK\Client;

$client = new Client([
    'base_url' => 'https://mewayz.com/api',
    'token' => 'your_token_here'
]);

// Get user profile
$user = $client->auth->me();

// Create contact
$contact = $client->crm->createContact([
    'name' => 'John Doe',
    'email' => 'john@example.com'
]);
```

### Python SDK

```python
# Installation
pip install mewayz-sdk

# Usage
from mewayz_sdk import Client

client = Client(
    base_url='https://mewayz.com/api',
    token='your_token_here'
)

# Get user profile
user = client.auth.me()

# Create contact
contact = client.crm.create_contact({
    'name': 'John Doe',
    'email': 'john@example.com'
})
```

---

## 📱 Mobile API Usage

### Flutter Integration

```dart
import 'package:http/http.dart' as http;
import 'dart:convert';

class MewayzAPI {
  static const String baseUrl = 'https://mewayz.com/api';
  static String? token;

  static Future<Map<String, dynamic>> login(String email, String password) async {
    final response = await http.post(
      Uri.parse('$baseUrl/auth/login'),
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode({
        'email': email,
        'password': password,
      }),
    );

    return jsonDecode(response.body);
  }

  static Future<Map<String, dynamic>> createContact(Map<String, dynamic> data) async {
    final response = await http.post(
      Uri.parse('$baseUrl/crm/contacts'),
      headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer $token',
      },
      body: jsonEncode(data),
    );

    return jsonDecode(response.body);
  }
}
```

---

## 🔍 Testing

### API Testing

```bash
# Health check
curl -X GET https://mewayz.com/api/health

# Login
curl -X POST https://mewayz.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}'

# Create contact with token
curl -X POST https://mewayz.com/api/crm/contacts \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{"name":"John Doe","email":"john@example.com"}'
```

### Postman Collection

Download our Postman collection:
- [Mewayz API Collection](https://documenter.getpostman.com/view/mewayz-api)

---

## 📚 Additional Resources

### Documentation
- [User Guide](USER_GUIDE.md)
- [Development Guide](DEVELOPMENT.md)
- [Troubleshooting](TROUBLESHOOTING.md)

### Support
- **Email**: api-support@mewayz.com
- **Documentation**: https://docs.mewayz.com
- **Community**: https://community.mewayz.com

### Changelog
- [API Changelog](CHANGELOG.md)
- [Breaking Changes](BREAKING_CHANGES.md)

---

*Mewayz Platform - API Documentation*  
*Built by Mewayz Technologies Inc.*  
*Creating seamless business solutions for the modern digital world*

**Version**: 1.0.0  
**Last Updated**: December 2024