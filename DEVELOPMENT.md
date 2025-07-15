# Mewayz Platform - Development Guide

**Professional Development Documentation for Mewayz Technologies Inc.'s Flagship Platform**

*Building seamless business solutions with modern technology stacks*

---

## 🎯 Platform Overview

Mewayz represents the pinnacle of Mewayz Technologies Inc.'s commitment to creating seamless business management solutions. This development guide provides comprehensive technical documentation for contributing to and extending the Mewayz platform.

### Brand Architecture
- **Mewayz**: The user-facing platform brand
- **Mewayz Technologies Inc.**: The engineering and innovation company
- **Seamless**: Our core development philosophy

### Domain Configuration
- **Production Domain**: mewayz.com
- **Development**: Local environment configurations
- **API Routes**: All backend routes must use `/api` prefix

**Important**: Do not modify environment URLs or domain configurations without proper testing to avoid breaking functionality.

---

## 🏗️ Technical Architecture

### Simplified Laravel-Only Architecture

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │   Laravel       │    │   Database      │
│   (Port 3000)   │◄──►│   (Port 8001)   │◄──►│   MySQL/MariaDB │
│   Static Files  │    │   Complete      │    │   Data Storage  │
│   (Optional)    │    │   Backend       │    │                 │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

### Technology Stack

#### Backend (Laravel 10+)
- **Framework**: Laravel 10.48.4 (PHP 8.2.28)
- **Database**: MySQL 8.0+ / MariaDB
- **Authentication**: Laravel Sanctum with OAuth 2.0
- **API**: RESTful API with comprehensive endpoints
- **Security**: AES-256, TLS 1.3, 2FA, RBAC
- **Queue**: Redis for background job processing
- **Cache**: Redis for application caching
- **Storage**: Local with S3 compatibility

#### Frontend (Multi-Platform)
- **Web**: Laravel Blade + Vite + Alpine.js
- **Mobile**: Flutter 3.x (Dart)
- **State Management**: Provider (Flutter)
- **Styling**: Tailwind CSS + Custom Design System
- **PWA**: Service Worker with offline capabilities
- **Build Tools**: Vite for asset compilation

#### Development Tools
- **Composer**: PHP dependency management
- **NPM/Yarn**: JavaScript dependency management
- **Vite**: Modern build tool and dev server
- **PHP CS Fixer**: Code style fixing
- **ESLint**: JavaScript linting
- **Prettier**: Code formatting

---

## 📦 Project Structure

```
mewayz/
├── app/                    # Laravel application
│   ├── Http/
│   │   ├── Controllers/    # API and web controllers
│   │   ├── Middleware/     # Custom middleware
│   │   └── Requests/       # Form requests
│   ├── Models/             # Eloquent models
│   ├── Services/           # Business logic services
│   └── Providers/          # Service providers
├── config/                 # Configuration files
├── database/
│   ├── migrations/         # Database migrations
│   ├── seeders/           # Database seeders
│   └── factories/         # Model factories
├── resources/
│   ├── views/             # Blade templates
│   ├── js/                # JavaScript assets
│   └── sass/              # Sass stylesheets
├── routes/
│   ├── api.php            # API routes
│   ├── web.php            # Web routes
│   └── auth.php           # Authentication routes
├── flutter_app/           # Flutter mobile application
│   ├── lib/
│   │   ├── screens/       # Flutter screens
│   │   ├── widgets/       # Custom widgets
│   │   ├── services/      # API services
│   │   └── providers/     # State management
│   └── pubspec.yaml       # Flutter dependencies
├── public/                # Public web assets
├── storage/               # Storage directories
├── tests/                 # Test files
└── vendor/                # Composer dependencies
```

---

## 🚀 Development Setup

### Prerequisites

- **PHP**: 8.2.28 or higher
- **Composer**: Latest version
- **Node.js**: 18+ LTS
- **MySQL/MariaDB**: 8.0+
- **Redis**: 6.0+ (optional but recommended)
- **Flutter**: 3.x (for mobile development)

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/mewayz/mewayz.git
   cd mewayz
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Build assets**
   ```bash
   npm run dev
   ```

6. **Start development server**
   ```bash
   php artisan serve --host=0.0.0.0 --port=8001
   ```

### Flutter Development

```bash
cd flutter_app
flutter pub get
flutter run -d chrome
```

---

## 🔧 Development Workflow

### Code Standards

#### PHP (PSR-12)
```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $users = User::paginate(15);
        
        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }
}
```

#### Flutter (Dart)
```dart
class UserService {
  static const String _baseUrl = 'http://localhost:8001/api';
  
  static Future<List<User>> getUsers() async {
    try {
      final response = await http.get(
        Uri.parse('$_baseUrl/users'),
        headers: {'Content-Type': 'application/json'},
      );
      
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return (data['data'] as List)
            .map((user) => User.fromJson(user))
            .toList();
      }
      
      throw Exception('Failed to load users');
    } catch (e) {
      throw Exception('Error: $e');
    }
  }
}
```

### Testing

#### Backend Testing
```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=UserTest

# Run with coverage
php artisan test --coverage
```

#### Frontend Testing
```bash
# Flutter tests
cd flutter_app
flutter test

# JavaScript tests
npm test
```

### Code Quality

#### PHP
```bash
# Fix code style
composer run fix-style

# Run static analysis
composer run analyse

# Check code quality
composer run quality
```

#### JavaScript
```bash
# Run linting
npm run lint

# Fix linting issues
npm run lint:fix

# Format code
npm run format
```

---

## 🗄️ Database Schema

### Core Tables

#### Users Table
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    google_id VARCHAR(255) NULL,
    apple_id VARCHAR(255) NULL,
    two_factor_secret TEXT NULL,
    two_factor_recovery_codes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_created_at (created_at)
);
```

#### Organizations Table
```sql
CREATE TABLE organizations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    settings JSON NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_slug (slug)
);
```

### Business Feature Tables

#### Social Media
```sql
CREATE TABLE social_media_accounts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    platform VARCHAR(50) NOT NULL,
    account_id VARCHAR(255) NOT NULL,
    access_token TEXT NOT NULL,
    refresh_token TEXT NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

#### CRM
```sql
CREATE TABLE audiences (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NULL,
    company VARCHAR(255) NULL,
    type ENUM('contact', 'lead') DEFAULT 'contact',
    status ENUM('hot', 'warm', 'cold') DEFAULT 'cold',
    source VARCHAR(100) NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_email (user_id, email),
    INDEX idx_type_status (type, status)
);
```

---

## 🔌 API Development

### RESTful API Design

#### Standard Response Format
```json
{
  "success": true,
  "message": "Operation successful",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  },
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 100
  }
}
```

#### Error Response Format
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

### Authentication

#### Laravel Sanctum
```php
// Protect routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

// Issue token
$token = $user->createToken('api-token')->plainTextToken;
```

#### API Usage
```bash
# With token
curl -H "Authorization: Bearer {token}" \
     -H "Content-Type: application/json" \
     https://mewayz.com/api/user
```

---

## 📱 Frontend Development

### Laravel Blade

#### Layout Structure
```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Mewayz Platform')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @yield('content')
</body>
</html>
```

#### Component Usage
```blade
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-3xl font-bold mb-6">Dashboard</h1>
    
    @include('components.stats-cards')
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($items as $item)
            @include('components.item-card', ['item' => $item])
        @endforeach
    </div>
</div>
@endsection
```

### Flutter Development

#### State Management
```dart
class AuthProvider extends ChangeNotifier {
  User? _user;
  bool _isLoading = false;
  
  User? get user => _user;
  bool get isLoading => _isLoading;
  bool get isAuthenticated => _user != null;
  
  Future<void> login(String email, String password) async {
    _isLoading = true;
    notifyListeners();
    
    try {
      final response = await ApiService.login(email, password);
      if (response['success']) {
        _user = User.fromJson(response['data']['user']);
        await _saveToken(response['data']['token']);
      }
    } catch (e) {
      throw Exception('Login failed: $e');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }
}
```

#### Widget Structure
```dart
class UserCard extends StatelessWidget {
  final User user;
  
  const UserCard({Key? key, required this.user}) : super(key: key);
  
  @override
  Widget build(BuildContext context) {
    return Card(
      child: ListTile(
        leading: CircleAvatar(
          backgroundImage: NetworkImage(user.avatar),
        ),
        title: Text(user.name),
        subtitle: Text(user.email),
        trailing: IconButton(
          icon: const Icon(Icons.more_vert),
          onPressed: () => _showOptions(context),
        ),
      ),
    );
  }
}
```
