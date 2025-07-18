# Mewayz Platform API Reference

*Version: 2.0 | Last Updated: July 19, 2025*

## 🌟 Overview

The Mewayz Platform API provides programmatic access to all platform features, enabling developers to build custom integrations, automate workflows, and extend functionality.

### API Characteristics
- **RESTful Design**: Standard HTTP methods and status codes
- **JSON Format**: All requests and responses use JSON
- **Rate Limited**: Prevents abuse and ensures fair usage
- **Versioned**: Maintains backward compatibility
- **Secure**: OAuth 2.0 and API key authentication

---

## 🚀 Quick Start

### 1. Authentication
```bash
# Get access token
curl -X POST https://api.mewayz.com/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "your@email.com",
    "password": "your_password"
  }'

# Response
{
  "success": true,
  "data": {
    "token": "1|eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "your@email.com"
    }
  }
}
```

### 2. Making API Calls
```bash
# Use token in subsequent requests
curl -X GET https://api.mewayz.com/workspaces \
  -H "Authorization: Bearer {your_token}" \
  -H "Accept: application/json"
```

### 3. Response Format
All API responses follow this consistent format:
```json
{
  "success": true,
  "data": {
    // Response data here
  },
  "message": "Operation completed successfully",
  "meta": {
    "timestamp": "2025-07-19T10:30:00Z",
    "version": "2.0.0",
    "rate_limit": {
      "remaining": 999,
      "reset_at": "2025-07-19T11:00:00Z"
    }
  }
}
```

---

## 🔐 Authentication

### Methods Available
1. **API Token** (Recommended for integrations)
2. **OAuth 2.0** (For third-party applications)
3. **Session-based** (For web applications)

### API Token Authentication
```bash
# Include in header
Authorization: Bearer {your_api_token}

# Or as query parameter
?api_token={your_api_token}
```

### OAuth 2.0 Flow
```bash
# 1. Authorization URL
GET /oauth/authorize?client_id={client_id}&redirect_uri={redirect_uri}&response_type=code&scope=read+write

# 2. Exchange code for token
POST /oauth/token
{
  "grant_type": "authorization_code",
  "client_id": "{client_id}",
  "client_secret": "{client_secret}",
  "code": "{authorization_code}",
  "redirect_uri": "{redirect_uri}"
}
```

---

## 📋 Core Endpoints

### System Health
```bash
GET /api/health
# Check system status and availability
```

### Authentication
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/auth/login` | POST | User authentication |
| `/api/auth/register` | POST | User registration |
| `/api/auth/logout` | POST | Logout current session |
| `/api/auth/me` | GET | Get current user info |
| `/api/auth/refresh` | POST | Refresh authentication token |

### Workspace Management
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/workspaces` | GET | List all workspaces |
| `/api/workspaces` | POST | Create new workspace |
| `/api/workspaces/{id}` | GET | Get specific workspace |
| `/api/workspaces/{id}` | PUT | Update workspace |
| `/api/workspaces/{id}` | DELETE | Delete workspace |

### Social Media Management
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/social-media/accounts` | GET | List connected accounts |
| `/api/social-media/accounts` | POST | Connect new account |
| `/api/social-media/posts` | GET | List scheduled posts |
| `/api/social-media/posts` | POST | Create/schedule new post |
| `/api/social-media/analytics` | GET | Get social media analytics |

### Link in Bio
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/bio-sites` | GET | List bio sites |
| `/api/bio-sites` | POST | Create new bio site |
| `/api/bio-sites/{slug}` | GET | Get bio site by slug |
| `/api/bio-sites/{id}/analytics` | GET | Get bio site analytics |

### E-commerce
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/ecommerce/products` | GET | List products |
| `/api/ecommerce/products` | POST | Create product |
| `/api/ecommerce/orders` | GET | List orders |
| `/api/ecommerce/orders/{id}` | GET | Get order details |

### Course Management
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/courses` | GET | List courses |
| `/api/courses` | POST | Create course |
| `/api/courses/{id}/lessons` | GET | Get course lessons |
| `/api/courses/{id}/students` | GET | Get enrolled students |

### CRM & Email Marketing
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/crm/contacts` | GET | List contacts |
| `/api/crm/contacts` | POST | Create contact |
| `/api/email-marketing/campaigns` | GET | List campaigns |
| `/api/email-marketing/campaigns` | POST | Create campaign |

### Analytics
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/analytics/overview` | GET | Platform overview stats |
| `/api/analytics/social-media` | GET | Social media analytics |
| `/api/analytics/ecommerce` | GET | E-commerce analytics |
| `/api/analytics/reports` | GET | Custom reports |

---

## 📊 Request & Response Examples

### Create Workspace
**Request:**
```bash
POST /api/workspaces
Content-Type: application/json
Authorization: Bearer {token}

{
  "name": "My Business",
  "description": "My awesome business workspace",
  "goals": ["instagram", "ecommerce", "courses"]
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 123,
    "name": "My Business",
    "description": "My awesome business workspace",
    "slug": "my-business",
    "goals": ["instagram", "ecommerce", "courses"],
    "created_at": "2025-07-19T10:30:00Z",
    "updated_at": "2025-07-19T10:30:00Z"
  },
  "message": "Workspace created successfully"
}
```

### Schedule Social Media Post
**Request:**
```bash
POST /api/social-media/posts
Content-Type: application/json
Authorization: Bearer {token}

{
  "accounts": ["instagram_123", "facebook_456"],
  "content": "Check out our latest product! 🚀",
  "media": ["https://example.com/image.jpg"],
  "scheduled_for": "2025-07-20T14:00:00Z",
  "hashtags": ["#product", "#launch", "#awesome"]
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 789,
    "content": "Check out our latest product! 🚀",
    "accounts": [
      {
        "id": "instagram_123",
        "platform": "instagram",
        "username": "@mybusiness"
      },
      {
        "id": "facebook_456",
        "platform": "facebook",
        "name": "My Business Page"
      }
    ],
    "scheduled_for": "2025-07-20T14:00:00Z",
    "status": "scheduled",
    "created_at": "2025-07-19T10:30:00Z"
  },
  "message": "Post scheduled successfully"
}
```

---

## ⚠️ Error Handling

### HTTP Status Codes
| Code | Meaning | Description |
|------|---------|-------------|
| 200 | OK | Request successful |
| 201 | Created | Resource created successfully |
| 400 | Bad Request | Invalid request data |
| 401 | Unauthorized | Authentication required |
| 403 | Forbidden | Insufficient permissions |
| 404 | Not Found | Resource not found |
| 422 | Validation Error | Invalid input data |
| 429 | Rate Limited | Too many requests |
| 500 | Server Error | Internal server error |

### Error Response Format
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  },
  "meta": {
    "timestamp": "2025-07-19T10:30:00Z",
    "version": "2.0.0"
  }
}
```

---

## 🔒 Rate Limiting

### Limits by Plan
| Plan | Requests/Hour | Burst Limit |
|------|---------------|-------------|
| Free | 100 | 10/minute |
| Professional | 1,000 | 50/minute |
| Enterprise | 10,000 | 200/minute |

### Rate Limit Headers
```http
X-RateLimit-Limit: 1000
X-RateLimit-Remaining: 999
X-RateLimit-Reset: 1642678800
```

### Handling Rate Limits
```bash
# Check remaining requests
curl -I https://api.mewayz.com/workspaces \
  -H "Authorization: Bearer {token}"

# Implement exponential backoff
if [ "$response_code" -eq 429 ]; then
  sleep_time=$((2 ** retry_count))
  sleep $sleep_time
fi
```

---

## 📝 Pagination

### Standard Pagination
```bash
GET /api/workspaces?page=2&per_page=25
```

**Response:**
```json
{
  "success": true,
  "data": [...],
  "meta": {
    "current_page": 2,
    "last_page": 5,
    "per_page": 25,
    "total": 123
  },
  "links": {
    "first": "https://api.mewayz.com/workspaces?page=1",
    "last": "https://api.mewayz.com/workspaces?page=5",
    "prev": "https://api.mewayz.com/workspaces?page=1",
    "next": "https://api.mewayz.com/workspaces?page=3"
  }
}
```

---

## 🔍 Filtering & Searching

### Query Parameters
```bash
# Filter by status
GET /api/social-media/posts?status=scheduled

# Search by keyword
GET /api/crm/contacts?search=john

# Sort results
GET /api/ecommerce/products?sort=price&order=asc

# Date range filtering
GET /api/analytics/overview?from=2025-01-01&to=2025-01-31

# Multiple filters
GET /api/crm/contacts?status=active&tag=customer&sort=created_at&order=desc
```

---

## 🎯 Webhooks

### Available Events
- `workspace.created`
- `social_media.post.published`
- `ecommerce.order.created`
- `course.enrollment.completed`
- `payment.received`

### Webhook Configuration
```bash
POST /api/webhooks
{
  "url": "https://yoursite.com/webhook/mewayz",
  "events": ["ecommerce.order.created", "payment.received"],
  "secret": "your_webhook_secret"
}
```

### Webhook Payload Example
```json
{
  "event": "ecommerce.order.created",
  "data": {
    "id": 456,
    "total": 99.99,
    "customer_email": "customer@example.com",
    "created_at": "2025-07-19T10:30:00Z"
  },
  "timestamp": "2025-07-19T10:30:00Z"
}
```

---

## 🛠️ SDKs & Libraries

### Official SDKs
- **PHP**: `composer require mewayz/php-sdk`
- **JavaScript**: `npm install @mewayz/js-sdk`
- **Python**: `pip install mewayz-sdk`

### PHP SDK Example
```php
use Mewayz\SDK\Client;

$client = new Client('your_api_token');

// Create workspace
$workspace = $client->workspaces()->create([
    'name' => 'My Business',
    'goals' => ['instagram', 'ecommerce']
]);

// Schedule social post
$post = $client->socialMedia()->schedulePost([
    'content' => 'Hello world!',
    'accounts' => ['instagram_123'],
    'scheduled_for' => '2025-07-20T14:00:00Z'
]);
```

### JavaScript SDK Example
```javascript
import MewayzSDK from '@mewayz/js-sdk';

const client = new MewayzSDK('your_api_token');

// Get workspaces
const workspaces = await client.workspaces.list();

// Create product
const product = await client.ecommerce.products.create({
  name: 'Awesome Product',
  price: 29.99,
  description: 'This is an awesome product'
});
```

---

## 📞 Support

### Getting Help
- **Documentation**: [https://docs.mewayz.com](https://docs.mewayz.com)
- **API Status**: [https://status.mewayz.com](https://status.mewayz.com)
- **Support Email**: api-support@mewayz.com
- **Developer Community**: [Discord Server](https://discord.gg/mewayz)

### API Support Levels
| Plan | Support Level | Response Time |
|------|---------------|---------------|
| Free | Community | Best effort |
| Professional | Email | 24-48 hours |
| Enterprise | Priority + Phone | 4-8 hours |

---

**Ready to build something amazing?** Start with our **[Quick Start Guide](quick-start.md)** or explore the **[Code Examples](examples/)**.