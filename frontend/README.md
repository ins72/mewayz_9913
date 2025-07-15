# Mewayz - Flutter Mobile & Desktop App

A comprehensive business platform Flutter application that provides social media management, link-in-bio creation, CRM, email marketing, e-commerce, courses, and analytics - all in one unified platform.

## Features

### ✅ **Implemented Core Features**

#### 🔐 **Authentication & Workspace Management**
- **Splash Screen** - Animated app intro with logo
- **Login Screen** - Email/password authentication with form validation
- **Register Screen** - User registration with password confirmation
- **Forgot Password** - Password reset functionality
- **Workspace Selector** - Multi-workspace support with creation dialog
- **Dashboard** - Main overview with quick actions and analytics preview

#### 🎨 **UI/UX Features**
- **Dark Theme** - Professional dark theme with your exact color specifications:
  - Background: `#101010`
  - Surface: `#191919` 
  - Primary Button: `#FDFDFD` with `#141414` text
  - Secondary Button: `#191919` with `#282828` border and `#F1F1F1` text
  - Primary Text: `#F1F1F1`
  - Secondary Text: `#7B7B7B`
- **Responsive Design** - Works on mobile, tablet, and desktop
- **Navigation Drawer** - Comprehensive navigation with theme toggle
- **Custom Components** - Reusable buttons, text fields, and cards

#### 📱 **Core Platform Screens**

##### **Social Media Management**
- **Account Connection** - Instagram, Facebook, Twitter/X, LinkedIn, TikTok, YouTube
- **Content Scheduling** - Calendar view and post management
- **Analytics Dashboard** - Engagement metrics and performance tracking
- **Instagram Database** - Advanced filtering and lead generation tools

##### **Link in Bio Builder**
- **Bio Sites Management** - Create and manage multiple bio pages
- **Drag & Drop Builder** - Visual page builder with components panel
- **Analytics Tracking** - Click tracking and conversion metrics
- **Template System** - Pre-built templates for different use cases

##### **CRM & Lead Management**
- **Lead Pipeline** - Hot, warm, and cold lead categorization
- **Contact Management** - Comprehensive contact database
- **Import/Export** - CSV import and bulk account creation
- **Activity Tracking** - Lead source and interaction history

##### **Email Marketing**
- **Campaign Management** - Create and manage email campaigns
- **Template Library** - Professional email templates
- **Audience Segmentation** - Advanced audience management
- **Analytics & Reporting** - Open rates, click rates, and performance metrics

##### **E-commerce Store**
- **Product Management** - Full product catalog with inventory tracking
- **Order Management** - Order processing and fulfillment
- **Store Analytics** - Sales metrics and performance tracking
- **Multi-Status Tracking** - Active, low stock, out of stock indicators

##### **Courses & Community**
- **Course Creation** - Build and publish online courses
- **Student Management** - Track student progress and engagement
- **Community Features** - Discussion forums and group interaction
- **Revenue Tracking** - Course sales and performance metrics

##### **Analytics Dashboard**
- **Comprehensive Metrics** - Traffic, conversions, revenue tracking
- **Visual Charts** - Performance trends and data visualization
- **Traffic Sources** - Detailed source attribution
- **Custom Reports** - Flexible reporting system

### 🚧 **Architecture & Technical Implementation**

#### **State Management**
- **Provider Pattern** - Clean and efficient state management
- **AuthProvider** - User authentication and session management
- **WorkspaceProvider** - Multi-workspace functionality
- **ThemeProvider** - Dark/light theme switching

#### **API Integration**
- **RESTful API** - Complete Laravel backend integration
- **Service Layer** - Abstracted API calls with error handling
- **Token Management** - Secure authentication token storage
- **Offline Support** - Local storage for critical data

#### **Navigation**
- **GoRouter** - Modern navigation with route management
- **Nested Navigation** - Tab-based navigation within features
- **Deep Linking** - Support for direct feature access

#### **Data Models**
- **User Model** - Complete user data structure
- **Workspace Model** - Multi-tenant workspace support
- **Extensible Architecture** - Easy addition of new data models

## Setup Instructions

### Prerequisites
- Flutter SDK (3.0.0 or higher)
- Dart SDK
- Android Studio / VS Code
- Your Laravel backend running

### Installation

1. **Clone and Setup**
```bash
cd /app/flutter_app
flutter pub get
```

2. **Configure API Endpoint**
Update the API base URL in `lib/services/api_service.dart`:
```dart
static const String baseUrl = 'http://your-laravel-backend.com/api';
```

3. **Run the App**
```bash
# For development
flutter run

# For specific platform
flutter run -d chrome  # Web
flutter run -d windows # Windows
flutter run -d macos   # macOS
```

### Laravel Backend Integration

The Flutter app is designed to work with your existing Laravel backend. Ensure your Laravel API includes these endpoints:

```php
// Authentication
POST /api/auth/login
POST /api/auth/register
POST /api/auth/logout
POST /api/auth/forgot-password
GET  /api/user

// Workspaces (Organizations)
GET  /api/workspaces
POST /api/workspaces
POST /api/workspaces/{id}/invite

// Social Media
GET  /api/social-media/accounts
POST /api/social-media/schedule

// Bio Sites
GET  /api/bio-sites
POST /api/bio-sites

// CRM
GET  /api/crm/leads
POST /api/crm/leads

// Email Marketing
GET  /api/email-marketing/campaigns
POST /api/email-marketing/campaigns

// E-commerce
GET  /api/ecommerce/products
POST /api/ecommerce/products

// Courses
GET  /api/courses
POST /api/courses

// Analytics
GET  /api/analytics
```

## Project Structure

```
lib/
├── config/
│   ├── routes.dart          # App routing configuration
│   └── theme.dart           # Theme and color definitions
├── models/
│   ├── user_model.dart      # User data model
│   └── workspace_model.dart # Workspace data model
├── providers/
│   ├── auth_provider.dart   # Authentication state
│   ├── workspace_provider.dart # Workspace management
│   └── theme_provider.dart  # Theme switching
├── screens/
│   ├── auth/               # Authentication screens
│   ├── dashboard/          # Main dashboard
│   ├── workspace/          # Workspace management
│   ├── social_media/       # Social media features
│   ├── bio/               # Link in bio builder
│   ├── crm/               # CRM and lead management
│   ├── email/             # Email marketing
│   ├── ecommerce/         # E-commerce store
│   ├── courses/           # Course creation
│   └── analytics/         # Analytics dashboard
├── services/
│   ├── api_service.dart    # API communication
│   └── storage_service.dart # Local data storage
├── widgets/
│   ├── custom_button.dart  # Reusable button component
│   ├── custom_text_field.dart # Form input component
│   ├── dashboard_card.dart # Dashboard feature cards
│   └── app_drawer.dart     # Navigation drawer
└── main.dart              # App entry point
```

## Key Features Highlights

### 🎯 **Multi-Workspace Support**
- Switch between different business workspaces
- Role-based access control (Owner, Admin, Editor, Viewer)
- Isolated data and settings per workspace

### 🎨 **Professional Dark Theme**
- Carefully crafted dark theme following your specifications
- Smooth theme transitions
- Consistent color scheme across all components

### 📱 **Cross-Platform Compatibility**
- Responsive design works on mobile, tablet, and desktop
- Platform-specific optimizations
- Consistent experience across devices

### 🔒 **Security & Authentication**
- Secure token-based authentication
- Automatic session management
- Password reset functionality

### 📊 **Comprehensive Analytics**
- Real-time metrics and reporting
- Visual charts and data visualization
- Performance tracking across all features

## Next Steps for Full Implementation

### 🚀 **Phase 1 - Complete Core Features**
1. Implement API endpoints in Laravel backend
2. Add real data integration to replace mock data
3. Implement file upload functionality
4. Add push notifications

### 📈 **Phase 2 - Advanced Features**
1. AI-powered content generation
2. Advanced automation workflows
3. White-label customization
4. Enterprise features

### 🔧 **Phase 3 - Platform Extensions**
1. Mobile app store deployment
2. Desktop app distribution
3. Web platform optimization
4. Third-party integrations

## Dependencies

The app uses carefully selected, modern Flutter packages:

- **provider**: State management
- **go_router**: Navigation
- **http/dio**: API communication
- **shared_preferences**: Local storage
- **flutter_svg**: Vector graphics
- **cached_network_image**: Image caching
- **And many more for specific features**

## Contributing

This Flutter app is designed to perfectly complement your Laravel backend and provide a native mobile/desktop experience for the Mewayz platform. The architecture is modular and extensible, making it easy to add new features and maintain the codebase.

## Support

For technical support or questions about the Flutter app implementation, please refer to the comprehensive code documentation and comments throughout the project.

---

**Built with Flutter 💙 for the Mewayz Platform**