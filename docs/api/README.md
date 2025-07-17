# Mewayz Platform v2 - API Documentation

*Last Updated: January 17, 2025*

## 🚀 **API OVERVIEW**

Welcome to the **Mewayz Platform v2** API documentation. This comprehensive guide covers all 150+ API endpoints, authentication, and integration patterns for our **Laravel 11 + MySQL** platform.

---

## 🔗 **BASE URLS**

### Environment URLs
```
Production: https://api.mewayz.com
Staging: https://staging-api.mewayz.com
Development: http://localhost:8000/api
```

### API Versioning
```
Current Version: v2
API Prefix: /api/v2/
Full URL: https://api.mewayz.com/api/v2/
```

---

## 🔐 **AUTHENTICATION**

### CustomSanctumAuth Middleware
All API requests require authentication using Laravel Sanctum tokens with our custom middleware.

```bash
# Get authentication token
curl -X POST https://api.mewayz.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "user@example.com", "password": "password"}'

# Response
{
  "success": true,
  "data": {
    "token": "1|abc123...",
    "user": {...},
    "expires_at": "2025-01-24T12:00:00Z"
  }
}

# Use token in requests
curl -X GET https://api.mewayz.com/api/user \
  -H "Authorization: Bearer 1|abc123..." \
  -H "Content-Type: application/json"
```

### Authentication Methods
- **Email/Password**: Traditional authentication
- **Google OAuth**: `POST /api/auth/google`
- **Apple Sign-In**: `POST /api/auth/apple`
- **Facebook Login**: `POST /api/auth/facebook`
- **Biometric Auth**: `POST /api/auth/biometric`
- **Two-Factor Auth**: `POST /api/auth/2fa/verify`

---

## 📊 **API STATISTICS**

### Current Implementation
- **Total Endpoints**: 150+
- **Controllers**: 40+
- **Database Tables**: 85+
- **Response Time**: < 200ms average
- **Uptime**: 99.9% SLA

### Rate Limiting
- **Free Plan**: 100 requests/minute
- **Professional Plan**: 500 requests/minute
- **Enterprise Plan**: 2000 requests/minute

---

## 🎯 **CORE API ENDPOINTS**

### Authentication & Users
```
POST   /api/auth/login           # User login
POST   /api/auth/register        # User registration
POST   /api/auth/logout          # User logout
GET    /api/user                 # Get current user
PUT    /api/user                 # Update user profile
POST   /api/auth/forgot-password # Password reset
POST   /api/auth/reset-password  # Password reset confirmation
```

### Workspaces
```
GET    /api/workspaces           # List user workspaces
POST   /api/workspaces           # Create workspace
GET    /api/workspaces/{id}      # Get workspace details
PUT    /api/workspaces/{id}      # Update workspace
DELETE /api/workspaces/{id}      # Delete workspace
POST   /api/workspaces/{id}/invite # Invite team member
```

### Social Media Management
```
GET    /api/social-media/accounts        # List social accounts
POST   /api/social-media/accounts        # Connect social account
GET    /api/social-media/posts           # List posts
POST   /api/social-media/posts           # Create post
PUT    /api/social-media/posts/{id}      # Update post
DELETE /api/social-media/posts/{id}      # Delete post
POST   /api/social-media/posts/{id}/schedule # Schedule post
```

### Instagram Database
```
GET    /api/instagram/profiles           # Search Instagram profiles
POST   /api/instagram/profiles/import    # Import profiles
GET    /api/instagram/analytics          # Instagram analytics
POST   /api/instagram/export             # Export profile data
```

### Link in Bio
```
GET    /api/bio-sites                    # List bio sites
POST   /api/bio-sites                    # Create bio site
GET    /api/bio-sites/{id}               # Get bio site
PUT    /api/bio-sites/{id}               # Update bio site
DELETE /api/bio-sites/{id}               # Delete bio site
GET    /api/bio-sites/{id}/analytics     # Bio site analytics
```

### E-commerce
```
GET    /api/ecommerce/products           # List products
POST   /api/ecommerce/products           # Create product
GET    /api/ecommerce/orders             # List orders
POST   /api/ecommerce/orders             # Create order
GET    /api/ecommerce/analytics          # E-commerce analytics
```

### CRM & Leads
```
GET    /api/crm/contacts                 # List contacts
POST   /api/crm/contacts                 # Create contact
GET    /api/crm/leads                    # List leads
POST   /api/crm/leads                    # Create lead
GET    /api/crm/pipeline                 # Sales pipeline
```

### Email Marketing
```
GET    /api/email-marketing/campaigns    # List campaigns
POST   /api/email-marketing/campaigns    # Create campaign
GET    /api/email-marketing/templates    # List templates
POST   /api/email-marketing/send         # Send email
```

### Courses
```
GET    /api/courses                      # List courses
POST   /api/courses                      # Create course
GET    /api/courses/{id}/modules         # Course modules
POST   /api/courses/{id}/enroll          # Enroll student
GET    /api/courses/{id}/analytics       # Course analytics
```

### Analytics
```
GET    /api/analytics/dashboard          # Dashboard metrics
GET    /api/analytics/reports            # Custom reports
POST   /api/analytics/reports            # Generate report
GET    /api/analytics/gamification       # Gamification data
```

### Escrow
```
GET    /api/escrow/transactions          # List transactions
POST   /api/escrow/transactions          # Create transaction
PUT    /api/escrow/transactions/{id}     # Update transaction
POST   /api/escrow/disputes              # Create dispute
```

### AI & Automation
```
POST   /api/ai/content-generation        # Generate content
POST   /api/ai/seo-optimization          # SEO optimization
POST   /api/ai/image-generation          # Generate images
GET    /api/automation/workflows         # List workflows
POST   /api/automation/workflows         # Create workflow
```

### Admin
```
GET    /api/admin/users                  # List all users
GET    /api/admin/workspaces             # List all workspaces
GET    /api/admin/analytics              # Platform analytics
POST   /api/admin/plans                  # Create subscription plan
GET    /api/admin/settings               # Platform settings
```

---

## 📝 **REQUEST/RESPONSE FORMAT**

### Standard Request Headers
```
Content-Type: application/json
Authorization: Bearer {token}
Accept: application/json
```

### Standard Response Format
```json
{
  "success": true,
  "data": {
    // Response data
  },
  "message": "Success message",
  "meta": {
    "current_page": 1,
    "per_page": 20,
    "total": 100
  }
}
```

### Error Response Format
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

---

## 🔄 **PAGINATION**

### Standard Pagination
```
GET /api/endpoint?page=1&per_page=20&sort=created_at&order=desc
```

### Response Format
```json
{
  "success": true,
  "data": [...],
  "meta": {
    "current_page": 1,
    "per_page": 20,
    "total": 100,
    "last_page": 5,
    "from": 1,
    "to": 20
  }
}
```

---

## 🛡️ **SECURITY**

### Authentication Security
- **JWT Tokens**: Secure token-based authentication
- **Token Expiry**: Configurable token expiration
- **Refresh Tokens**: Automatic token refresh
- **Rate Limiting**: Request rate limiting per plan

### Data Security
- **Input Validation**: All inputs validated
- **SQL Injection Protection**: Eloquent ORM protection
- **XSS Protection**: Cross-site scripting protection
- **CSRF Protection**: Cross-site request forgery protection

---

## 🔌 **WEBHOOKS**

### Webhook Events
```
user.created
user.updated
workspace.created
order.completed
payment.processed
subscription.updated
```

### Webhook Configuration
```bash
POST /api/webhooks
{
  "url": "https://your-app.com/webhooks",
  "events": ["order.completed", "payment.processed"],
  "secret": "your-webhook-secret"
}
```

---

## 📊 **MONITORING & ANALYTICS**

### API Metrics
- **Response Time**: Average < 200ms
- **Success Rate**: 99.9%
- **Error Rate**: < 0.1%
- **Uptime**: 99.9% SLA

### Available Metrics
- Request count by endpoint
- Response time distribution
- Error rate by endpoint
- User activity metrics

---

## 🧪 **TESTING**

### API Testing Tools
- **Postman Collection**: Available for download
- **OpenAPI/Swagger**: Interactive documentation
- **Unit Tests**: PHPUnit test suite
- **Integration Tests**: End-to-end API tests

### Testing Endpoints
```
GET    /api/health              # Health check
GET    /api/version             # API version info
POST   /api/test/email          # Test email functionality
```

---

## 🔧 **INTEGRATION EXAMPLES**

### JavaScript/React
```javascript
// API client setup
const apiClient = axios.create({
  baseURL: 'https://api.mewayz.com/api',
  headers: {
    'Content-Type': 'application/json',
    'Authorization': `Bearer ${token}`
  }
});

// Get user workspaces
const workspaces = await apiClient.get('/workspaces');
```

### PHP
```php
// Using Guzzle HTTP client
$client = new GuzzleHttp\Client([
    'base_uri' => 'https://api.mewayz.com/api/',
    'headers' => [
        'Authorization' => 'Bearer ' . $token,
        'Content-Type' => 'application/json'
    ]
]);

$response = $client->get('workspaces');
```

### Python
```python
import requests

headers = {
    'Authorization': f'Bearer {token}',
    'Content-Type': 'application/json'
}

response = requests.get('https://api.mewayz.com/api/workspaces', headers=headers)
```

---

## 📚 **ADDITIONAL RESOURCES**

### Documentation
- **Postman Collection**: [Download](https://api.mewayz.com/docs/postman)
- **OpenAPI Spec**: [View](https://api.mewayz.com/docs/openapi)
- **SDK Libraries**: Available for JavaScript, PHP, Python

### Support
- **API Support**: api-support@mewayz.com
- **Documentation Issues**: docs@mewayz.com
- **Status Page**: https://status.mewayz.com

---

*Last Updated: January 17, 2025*
*API Version: v2*
*Platform: Laravel 11 + MySQL*
*Status: Production-Ready*