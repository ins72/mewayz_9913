# Mewayz Platform v2 - API Documentation

*Last Updated: July 17, 2025*

## Overview

This document provides comprehensive API documentation for the Mewayz Platform v2. The platform exposes 200+ RESTful endpoints across 50+ controllers, organized into 4 phases of functionality.

## Base URL

```
Production: https://your-domain.com/api
Development: http://localhost:8001/api
```

## Authentication

The platform uses Laravel Sanctum with custom middleware for authentication:

```bash
# Login to get token
POST /api/auth/login
Content-Type: application/json
{
  "email": "user@example.com",
  "password": "password"
}

# Response
{
  "success": true,
  "token": "1|abc123...",
  "user": { ... }
}

# Use token in subsequent requests
Authorization: Bearer 1|abc123...
```

## API Endpoints by Phase

### Phase 1: Enhanced User Experience

#### Onboarding System
- `GET /api/onboarding/progress` - Get user onboarding progress
- `POST /api/onboarding/progress` - Update onboarding progress
- `GET /api/onboarding/recommendations` - Get personalized template recommendations
- `POST /api/onboarding/step/complete` - Complete onboarding step
- `GET /api/onboarding/demo` - Get interactive demo data

#### Theme Management
- `GET /api/theme/` - Get current theme settings
- `POST /api/theme/update` - Update theme preferences
- `GET /api/theme/system` - Get intelligent system theme detection
- `GET /api/theme/presets` - Get available theme presets

#### Core Platform Features
- `GET /api/websites/templates` - Get website templates
- `GET /api/bio-sites/` - Get bio sites
- `POST /api/bio-sites/` - Create bio site
- `GET /api/workspaces` - Get user workspaces

### Phase 2: Enterprise Features

#### SSO Management
- `GET /api/sso/providers` - Get SSO providers
- `POST /api/sso/providers` - Create SSO provider
- `PUT /api/sso/providers/{id}` - Update SSO provider
- `DELETE /api/sso/providers/{id}` - Delete SSO provider

#### Team Management
- `GET /api/team/departments` - Get departments
- `POST /api/team/departments` - Create department
- `GET /api/team/members` - Get team members
- `POST /api/team/members` - Add team member
- `GET /api/team/roles` - Get team roles

#### White Label Solutions
- `GET /api/white-label/settings` - Get white label settings
- `POST /api/white-label/settings` - Update white label settings
- `GET /api/white-label/domains` - Get custom domains
- `POST /api/white-label/domains` - Add custom domain

#### Audit & Compliance
- `GET /api/audit/logs` - Get audit logs
- `POST /api/audit/logs` - Create audit log entry
- `GET /api/compliance/reports` - Get compliance reports
- `POST /api/compliance/reports` - Generate compliance report

### Phase 3: International & Security

#### Multi-Language Support
- `GET /api/i18n/languages` - Get supported languages
- `POST /api/i18n/languages` - Add language
- `GET /api/i18n/translations` - Get translations
- `POST /api/i18n/translations` - Update translations

#### Regional Settings
- `GET /api/regional/settings` - Get regional settings
- `POST /api/regional/settings` - Update regional settings
- `GET /api/regional/currencies` - Get supported currencies
- `GET /api/regional/tax-rates` - Get tax rates

#### Security Features
- `GET /api/security/events` - Get security events
- `POST /api/security/events` - Log security event
- `GET /api/security/threats` - Get threat detection data
- `GET /api/security/compliance` - Get compliance status

### Phase 4: Advanced AI & Analytics

#### AI Content Generation
- `POST /api/ai/content/generate` - Generate AI content
- `POST /api/ai/leads/score` - Score leads using AI
- `GET /api/ai/models` - Get available AI models
- `POST /api/ai/models` - Configure AI model

#### Advanced Analytics
- `GET /api/analytics/business-intelligence` - Get BI data
- `GET /api/analytics/predictive` - Get predictive analytics
- `GET /api/analytics/performance` - Get performance metrics
- `GET /api/analytics/realtime` - Get real-time metrics

#### Automation Workflows
- `GET /api/automation/workflows` - Get automation workflows
- `POST /api/automation/workflows` - Create workflow
- `PUT /api/automation/workflows/{id}` - Update workflow
- `DELETE /api/automation/workflows/{id}` - Delete workflow

## Core System Endpoints

### Authentication
- `POST /api/auth/register` - User registration
- `POST /api/auth/login` - User login
- `POST /api/auth/logout` - User logout
- `GET /api/auth/me` - Get current user
- `PUT /api/auth/profile` - Update user profile

### Biometric Authentication
- `POST /api/biometric/registration-options` - Get biometric registration options
- `POST /api/biometric/authentication-options` - Get biometric authentication options
- `GET /api/biometric/credentials` - Get user biometric credentials

### Real-Time Features
- `GET /api/realtime/notifications` - Get notifications
- `POST /api/realtime/notifications` - Send notification
- `GET /api/realtime/activity-feed` - Get activity feed
- `GET /api/realtime/system-status` - Get system status

### Booking System
- `GET /api/booking/services` - Get booking services
- `POST /api/booking/services` - Create booking service
- `GET /api/booking/appointments` - Get appointments
- `POST /api/booking/appointments` - Book appointment

### Financial Management
- `GET /api/financial/dashboard` - Get financial dashboard
- `GET /api/financial/invoices` - Get invoices
- `POST /api/financial/invoices` - Create invoice
- `GET /api/financial/reports` - Get financial reports

### Escrow System
- `GET /api/escrow/` - Get escrow transactions
- `POST /api/escrow/` - Create escrow transaction
- `GET /api/escrow/statistics/overview` - Get escrow statistics
- `PUT /api/escrow/{id}/status` - Update escrow status

## Request/Response Format

### Standard Request Format
```json
{
  "Content-Type": "application/json",
  "Accept": "application/json",
  "Authorization": "Bearer {token}"
}
```

### Standard Response Format
```json
{
  "success": true,
  "data": { ... },
  "message": "Operation completed successfully",
  "timestamp": "2025-07-17T22:00:00Z"
}
```

### Error Response Format
```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    "field": ["validation error message"]
  },
  "code": 400
}
```

## HTTP Status Codes

- `200 OK` - Request successful
- `201 Created` - Resource created successfully
- `400 Bad Request` - Invalid request data
- `401 Unauthorized` - Authentication required
- `403 Forbidden` - Insufficient permissions
- `404 Not Found` - Resource not found
- `422 Unprocessable Entity` - Validation errors
- `500 Internal Server Error` - Server error

## Rate Limiting

- **Authenticated users**: 1000 requests per hour
- **Guest users**: 100 requests per hour
- **API key users**: 5000 requests per hour

## Pagination

List endpoints support pagination:

```bash
GET /api/endpoint?page=1&per_page=20

# Response
{
  "data": [...],
  "current_page": 1,
  "per_page": 20,
  "total": 100,
  "last_page": 5,
  "next_page_url": "...",
  "prev_page_url": null
}
```

## Testing

### Backend Testing Results
- **Total Endpoints Tested**: 23 critical endpoints
- **Success Rate**: 100% (23/23 passed)
- **Response Time**: 0.02-0.04 seconds average
- **Authentication**: Custom Sanctum middleware working

### Example Test Commands
```bash
# Health check
curl -X GET http://localhost:8001/api/health

# Login
curl -X POST http://localhost:8001/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}'

# Get user profile
curl -X GET http://localhost:8001/api/auth/me \
  -H "Authorization: Bearer {token}"
```

## Support

For API support and questions:
- Check the [Troubleshooting Guide](../troubleshooting/README.md)
- Review the [Developer Guide](../developer/README.md)
- Consult the main [Documentation](../README.md)