# Contributing to Mewayz Platform

**Developer Contribution Guide**  
*By Mewayz Technologies Inc.*

---

## 👋 Welcome Contributors!

Thank you for your interest in contributing to Mewayz! This guide will help you understand how to contribute to our all-in-one business platform effectively.

## 📋 Table of Contents

1. [Code of Conduct](#code-of-conduct)
2. [Getting Started](#getting-started)
3. [Development Setup](#development-setup)
4. [Contribution Process](#contribution-process)
5. [Code Standards](#code-standards)
6. [Testing Requirements](#testing-requirements)
7. [Documentation](#documentation)
8. [Community](#community)

---

## 🤝 Code of Conduct

### Our Commitment

We are committed to providing a welcoming and inclusive environment for all contributors, regardless of:
- Experience level
- Gender identity and expression
- Sexual orientation
- Disability
- Personal appearance
- Body size
- Race
- Ethnicity
- Age
- Religion
- Nationality

### Expected Behavior

- **Be respectful**: Treat all community members with respect and kindness
- **Be collaborative**: Work together to solve problems and improve the platform
- **Be constructive**: Provide constructive feedback and suggestions
- **Be patient**: Help newcomers learn and grow
- **Be inclusive**: Welcome people from all backgrounds and perspectives

### Unacceptable Behavior

- Harassment or discriminatory behavior
- Trolling, insulting, or derogatory comments
- Personal or political attacks
- Public or private harassment
- Publishing others' private information
- Any other conduct that could reasonably be considered inappropriate

---

## 🚀 Getting Started

### Prerequisites

Before contributing, ensure you have:
- **PHP 8.2.28+** installed
- **Composer** for dependency management
- **Node.js 18+** for frontend assets
- **MySQL/MariaDB** for database
- **Git** for version control
- **Flutter** (for mobile development)

### Understanding the Codebase

#### Architecture Overview
- **Backend**: Laravel 10+ (PHP)
- **Frontend**: Laravel Blade + Flutter
- **Database**: MySQL/MariaDB
- **API**: RESTful with Laravel Sanctum
- **Queue**: Redis-based job processing

#### Key Directories
```
/app                 # Laravel application
/flutter_app         # Flutter mobile app
/public              # Public web assets
/resources           # Frontend resources
/database            # Database migrations
/tests               # Test files
/docs                # Documentation
```

---

## 🛠️ Development Setup

### 1. Fork and Clone

```bash
# Fork the repository on GitHub
# Then clone your fork
git clone https://github.com/yourusername/mewayz.git
cd mewayz

# Add upstream remote
git remote add upstream https://github.com/mewayz/mewayz.git
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Install Flutter dependencies
cd flutter_app
flutter pub get
cd ..
```

### 3. Environment Setup

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mewayz_dev
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Database Setup

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE mewayz_dev;"

# Run migrations
php artisan migrate

# Seed database (optional)
php artisan db:seed
```

### 5. Start Development Server

```bash
# Start Laravel server
php artisan serve --host=0.0.0.0 --port=8001

# In another terminal, build assets
npm run dev
```

---

## 🔄 Contribution Process

### 1. Choose an Issue

- Browse [open issues](https://github.com/mewayz/mewayz/issues)
- Look for issues labeled `good first issue` for beginners
- Check `help wanted` issues for priority items
- Comment on the issue to express interest

### 2. Create a Branch

```bash
# Sync with upstream
git fetch upstream
git checkout main
git merge upstream/main

# Create feature branch
git checkout -b feature/issue-description
```

### 3. Make Changes

- Follow our [code standards](#code-standards)
- Write comprehensive tests
- Update documentation as needed
- Ensure all tests pass

### 4. Test Your Changes

```bash
# Run PHP tests
php artisan test

# Run Flutter tests
cd flutter_app
flutter test

# Run linting
composer run lint
npm run lint
```

### 5. Submit Pull Request

```bash
# Commit changes
git add .
git commit -m "feat: add new feature for issue #123"

# Push to your fork
git push origin feature/issue-description

# Create pull request on GitHub
```

### 6. Code Review Process

- Automated checks will run
- Core team will review your code
- Address any feedback
- Once approved, changes will be merged

---

## 📝 Code Standards

### PHP Standards

#### PSR-12 Compliance
Follow PSR-12 coding standards:

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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

#### Laravel Best Practices
- Use Laravel's built-in features
- Follow Laravel naming conventions
- Implement proper validation
- Use Eloquent ORM efficiently
- Handle exceptions gracefully

### JavaScript Standards

#### ES6+ Features
Use modern JavaScript features:

```javascript
// Use arrow functions
const fetchUsers = async () => {
    try {
        const response = await fetch('/api/users');
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error fetching users:', error);
    }
};

// Use destructuring
const { name, email } = user;

// Use template literals
const message = `Welcome ${name}!`;
```

### Flutter Standards

#### Dart Code Style
Follow Dart style guide:

```dart
class UserService {
  static const String _baseUrl = 'https://api.mewayz.com';
  
  Future<List<User>> getUsers() async {
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

### Database Standards

#### Migration Best Practices
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            
            $table->index(['email', 'created_at']);
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
```

---

## 🧪 Testing Requirements

### Backend Testing

#### Unit Tests
```php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_user_can_be_created(): void
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
        
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
    }
}
```

#### Feature Tests
```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_user_can_login(): void
    {
        $user = User::factory()->create();
        
        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => ['user', 'token'],
                 ]);
    }
}
```

### Frontend Testing

#### Flutter Widget Tests
```dart
import 'package:flutter_test/flutter_test.dart';
import 'package:mewayz_app/widgets/user_card.dart';

void main() {
  group('UserCard Widget', () {
    testWidgets('displays user information correctly', (WidgetTester tester) async {
      const user = User(
        id: 1,
        name: 'John Doe',
        email: 'john@example.com',
      );
      
      await tester.pumpWidget(MaterialApp(
        home: UserCard(user: user),
      ));
      
      expect(find.text('John Doe'), findsOneWidget);
      expect(find.text('john@example.com'), findsOneWidget);
    });
  });
}
```

### Test Coverage

Maintain minimum test coverage:
- **Unit Tests**: 80%+
- **Feature Tests**: 70%+
- **Integration Tests**: 60%+

---

## 📚 Documentation

### Code Documentation

#### PHP DocBlocks
```php
/**
 * Create a new user account
 *
 * @param array $userData User registration data
 * @return User The created user instance
 * @throws ValidationException If validation fails
 */
public function createUser(array $userData): User
{
    // Implementation
}
```

#### JavaScript JSDoc
```javascript
/**
 * Fetch user data from API
 * @param {number} userId - The user ID
 * @returns {Promise<Object>} User data
 * @throws {Error} If request fails
 */
async function fetchUser(userId) {
    // Implementation
}
```

### API Documentation

Update API documentation for new endpoints:
```yaml
# openapi.yaml
/api/users:
  get:
    summary: Get all users
    parameters:
      - name: page
        in: query
        schema:
          type: integer
    responses:
      200:
        description: Users retrieved successfully
```

---

## 👥 Community

### Communication Channels

#### Discord Server
Join our Discord for real-time discussions:
- **General**: General discussions
- **Development**: Development-related topics
- **Help**: Get help with contributions
- **Announcements**: Important updates

#### GitHub Discussions
Use GitHub Discussions for:
- Feature requests
- Architecture discussions
- Q&A
- Show and tell

### Community Guidelines

#### Be Helpful
- Answer questions when you can
- Share knowledge and resources
- Provide constructive feedback
- Welcome new contributors

#### Be Respectful
- Respect different opinions
- Be patient with beginners
- Acknowledge contributions
- Follow code of conduct

### Recognition

We recognize contributors through:
- **Contributors page**: Listed on our website
- **Release notes**: Mentioned in changelogs
- **Community highlights**: Featured in newsletters
- **Special badges**: GitHub profile badges

---

## 🎯 Types of Contributions

### Bug Reports

#### How to Report
1. Check existing issues first
2. Use bug report template
3. Provide detailed steps to reproduce
4. Include system information
5. Add relevant screenshots

#### Good Bug Report
```
**Bug Description**
Login fails with 500 error when using OAuth

**Steps to Reproduce**
1. Click "Login with Google"
2. Complete OAuth flow
3. Redirected to login page with error

**Expected Behavior**
Should login successfully

**System Information**
- OS: macOS 12.6
- Browser: Chrome 108
- PHP: 8.2.28
```

### Feature Requests

#### How to Request
1. Check existing feature requests
2. Use feature request template
3. Explain the use case
4. Provide detailed requirements
5. Consider implementation impact

### Code Contributions

#### Areas to Contribute
- **Backend**: API endpoints, services, middleware
- **Frontend**: UI components, pages, features
- **Mobile**: Flutter widgets, screens, services
- **Testing**: Unit tests, feature tests, e2e tests
- **Documentation**: Guides, API docs, comments

### Documentation Improvements

#### Types of Documentation
- **User guides**: Help users use features
- **Developer docs**: Technical implementation
- **API documentation**: Endpoint references
- **Tutorials**: Step-by-step guides

---

## 🏆 Contributor Levels

### First-Time Contributor
- Fix typos and documentation
- Add missing tests
- Improve error messages
- Small bug fixes

### Regular Contributor
- Implement new features
- Major bug fixes
- Performance improvements
- Code refactoring

### Core Contributor
- Architecture decisions
- Review pull requests
- Mentor new contributors
- Lead feature development

---

## 📞 Getting Help

### Support Channels
- **Discord**: Real-time help
- **GitHub Issues**: Bug reports and features
- **Email**: contribute@mewayz.com
- **Documentation**: https://docs.mewayz.com

### Mentorship Program
- Pair with experienced contributors
- Guided contribution process
- Regular check-ins and feedback
- Skill development opportunities

---

## 📝 Legal

### Contributor License Agreement (CLA)
By contributing, you agree that:
- You have the right to submit the work
- You grant Mewayz Technologies Inc. rights to use your contribution
- Your contribution is provided under the same license as the project

### Attribution
All contributors will be recognized in:
- CONTRIBUTORS.md file
- Release notes
- Project documentation
- Community announcements

---

Thank you for contributing to Mewayz! Your contributions help make business management seamless for creators and entrepreneurs worldwide.

*Mewayz Platform - Contributing Guide*  
*Built by Mewayz Technologies Inc.*  
*Creating seamless business solutions for the modern digital world*

**Version**: 1.0.0  
**Last Updated**: December 2024