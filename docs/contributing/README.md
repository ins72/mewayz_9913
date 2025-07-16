# 🤝 Contributing to Mewayz

Thank you for considering contributing to Mewayz! This guide will help you understand our development process and get you started with contributing.

## 🚀 Getting Started

### Prerequisites
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8.0+
- Redis
- Git

### Development Setup
1. **Fork the Repository**
   ```bash
   git clone https://github.com/your-username/mewayz.git
   cd mewayz
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Start Development Server**
   ```bash
   php artisan serve
   npm run dev
   ```

## 🔄 Development Workflow

### Branch Strategy
We use **GitFlow** branching model:

- `main` - Production-ready code
- `develop` - Integration branch for features
- `feature/*` - New features
- `bugfix/*` - Bug fixes
- `hotfix/*` - Critical production fixes
- `release/*` - Release preparation

### Creating a Feature Branch
```bash
git checkout develop
git pull origin develop
git checkout -b feature/your-feature-name
```

### Making Changes
1. **Code your changes**
2. **Write tests** (required for new features)
3. **Update documentation** (if needed)
4. **Follow coding standards**

### Commit Messages
We follow [Conventional Commits](https://www.conventionalcommits.org/):

```
type(scope): description

feat(auth): add OAuth2 integration
fix(bio-sites): resolve link ordering issue
docs(api): update authentication guide
style(ui): improve responsive design
refactor(crm): optimize contact queries
test(email): add campaign validation tests
```

**Types:**
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation
- `style`: Code style changes
- `refactor`: Code refactoring
- `test`: Adding tests
- `chore`: Maintenance tasks

### Pull Request Process
1. **Create Pull Request**
   - Target `develop` branch
   - Use descriptive title
   - Fill out PR template
   - Link related issues

2. **Code Review**
   - Address feedback
   - Update tests if needed
   - Ensure CI passes

3. **Merge**
   - Squash commits
   - Update changelog
   - Close related issues

## 📝 Coding Standards

### PHP Standards
We follow **PSR-12** coding standards:

```php
<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;

class UserService
{
    public function __construct(
        private User $user
    ) {}

    public function findActiveUsers(): Collection
    {
        return $this->user
            ->where('is_active', true)
            ->get();
    }
}
```

### JavaScript Standards
We use **ESLint** and **Prettier**:

```javascript
// Good
const users = await fetch('/api/users')
  .then(response => response.json())
  .catch(error => console.error('Error:', error));

// Bad
const users = await fetch("/api/users").then(response=>response.json()).catch(error=>console.error("Error:",error));
```

### CSS Standards
We use **Tailwind CSS** utility classes:

```html
<!-- Good -->
<div class="bg-white shadow-md rounded-lg p-6">
  <h2 class="text-xl font-semibold text-gray-800">Title</h2>
  <p class="text-gray-600 mt-2">Description</p>
</div>

<!-- Avoid custom CSS unless necessary -->
```

## 🧪 Testing

### Running Tests
```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Run tests with coverage
php artisan test --coverage
```

### Writing Tests
#### Unit Tests
```php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\UserService;
use App\Models\User;

class UserServiceTest extends TestCase
{
    public function test_find_active_users_returns_only_active_users(): void
    {
        // Arrange
        User::factory()->create(['is_active' => true]);
        User::factory()->create(['is_active' => false]);
        
        $service = new UserService(new User);
        
        // Act
        $activeUsers = $service->findActiveUsers();
        
        // Assert
        $this->assertCount(1, $activeUsers);
        $this->assertTrue($activeUsers->first()->is_active);
    }
}
```

#### Feature Tests
```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class BioSiteTest extends TestCase
{
    public function test_user_can_create_bio_site(): void
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        // Act
        $response = $this->postJson('/api/bio-sites', [
            'name' => 'My Bio Site',
            'address' => 'myhandle'
        ]);
        
        // Assert
        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'data' => [
                'name' => 'My Bio Site',
                'address' => 'myhandle'
            ]
        ]);
    }
}
```

### Test Coverage Requirements
- **New features**: 80% minimum coverage
- **Bug fixes**: Must include regression tests
- **API endpoints**: Must have feature tests
- **Services**: Must have unit tests

## 📖 Documentation

### Code Documentation
```php
/**
 * Create a new bio site for the authenticated user.
 *
 * @param  CreateBioSiteRequest  $request
 * @return JsonResponse
 * 
 * @throws ValidationException
 */
public function store(CreateBioSiteRequest $request): JsonResponse
{
    $bioSite = $this->bioSiteService->create($request->validated());
    
    return response()->json([
        'success' => true,
        'data' => new BioSiteResource($bioSite)
    ], 201);
}
```

### API Documentation
Update OpenAPI specs when adding/modifying endpoints:

```yaml
/api/bio-sites:
  post:
    summary: Create bio site
    tags: [Bio Sites]
    requestBody:
      required: true
      content:
        application/json:
          schema:
            type: object
            properties:
              name:
                type: string
                example: "My Bio Site"
              address:
                type: string
                example: "myhandle"
    responses:
      201:
        description: Bio site created successfully
```

### User Documentation
Update user guides when adding new features:
- Screenshots of new UI
- Step-by-step instructions
- Common use cases
- Troubleshooting tips

## 🐛 Bug Reports

### Before Reporting
1. **Search existing issues**
2. **Check documentation**
3. **Test on latest version**
4. **Reproduce the bug**

### Bug Report Template
```markdown
## Bug Description
A clear description of the bug.

## Steps to Reproduce
1. Go to '...'
2. Click on '....'
3. Scroll down to '....'
4. See error

## Expected Behavior
What you expected to happen.

## Actual Behavior
What actually happened.

## Environment
- OS: [e.g. macOS 12.0]
- Browser: [e.g. Chrome 98]
- PHP Version: [e.g. 8.2]
- Laravel Version: [e.g. 11.0]

## Screenshots
If applicable, add screenshots.

## Additional Context
Any other context about the problem.
```

## 💡 Feature Requests

### Before Requesting
1. **Check existing requests**
2. **Discuss in community**
3. **Consider alternatives**
4. **Define use cases**

### Feature Request Template
```markdown
## Feature Summary
Brief description of the feature.

## Problem Statement
What problem does this solve?

## Proposed Solution
How should this feature work?

## Alternative Solutions
Other ways to solve this problem.

## Use Cases
When would this feature be used?

## Implementation Notes
Technical considerations.
```

## 🏷️ Issue Labels

### Priority Labels
- `priority:critical` - Must fix immediately
- `priority:high` - Should fix soon
- `priority:medium` - Standard priority
- `priority:low` - Nice to have

### Type Labels
- `type:bug` - Something isn't working
- `type:feature` - New feature request
- `type:enhancement` - Improvement to existing feature
- `type:documentation` - Documentation update
- `type:refactor` - Code refactoring

### Status Labels
- `status:needs-review` - Needs code review
- `status:in-progress` - Being worked on
- `status:blocked` - Blocked by other work
- `status:ready` - Ready for merge

### Component Labels
- `component:api` - API related
- `component:ui` - User interface
- `component:database` - Database related
- `component:auth` - Authentication
- `component:bio-sites` - Bio sites feature
- `component:social-media` - Social media integration
- `component:ecommerce` - E-commerce features

## 👥 Community Guidelines

### Code of Conduct
- **Be respectful** to all contributors
- **Be constructive** in feedback
- **Be patient** with newcomers
- **Be inclusive** of different perspectives

### Communication Channels
- **GitHub Issues** - Bug reports and feature requests
- **GitHub Discussions** - General discussions
- **Discord** - Real-time chat
- **Email** - Direct communication

### Recognition
Contributors are recognized through:
- **GitHub contributors page**
- **Release notes mention**
- **Hall of fame** in documentation
- **Special contributor badge**

## 🎯 Development Focus Areas

### Current Priorities
1. **Performance optimization**
2. **Mobile responsiveness**
3. **API documentation**
4. **Test coverage**
5. **Security improvements**

### Future Roadmap
1. **Real-time features**
2. **Advanced analytics**
3. **Mobile app**
4. **Third-party integrations**
5. **Enterprise features**

## 🔧 Development Tools

### Recommended IDE Extensions
#### VS Code
- PHP Intelephense
- Laravel Extension Pack
- Tailwind CSS IntelliSense
- GitLens
- ESLint
- Prettier

#### PHPStorm
- Laravel Plugin
- Tailwind CSS Plugin
- JavaScript Debugger
- Database Tools

### Development Commands
```bash
# Code formatting
./vendor/bin/php-cs-fixer fix

# Static analysis
./vendor/bin/phpstan analyse

# Security check
composer audit

# Asset building
npm run dev
npm run build
npm run watch
```

## 📊 Performance Guidelines

### Database Queries
```php
// Good - Eager loading
$users = User::with('bioSites')->get();

// Bad - N+1 queries
$users = User::all();
foreach ($users as $user) {
    $bioSites = $user->bioSites; // N+1 query
}
```

### Caching
```php
// Cache expensive operations
return Cache::remember('user.bio-sites.' . $userId, 3600, function () use ($userId) {
    return User::find($userId)->bioSites;
});
```

### Asset Optimization
```javascript
// Lazy load components
const BioSiteEditor = lazy(() => import('./BioSiteEditor'));

// Optimize images
<img src="/images/hero.jpg" loading="lazy" alt="Hero" />
```

## 🛡️ Security Guidelines

### Input Validation
```php
// Always validate input
$request->validate([
    'email' => 'required|email|max:255',
    'name' => 'required|string|max:100',
    'bio' => 'nullable|string|max:1000'
]);
```

### Authorization
```php
// Check permissions
$this->authorize('update', $bioSite);

// Use policies
public function update(User $user, BioSite $bioSite): bool
{
    return $user->id === $bioSite->user_id;
}
```

### Data Sanitization
```php
// Sanitize output
echo e($userInput); // Escape HTML

// Use mass assignment protection
protected $fillable = ['name', 'email', 'bio'];
```

## 📈 Monitoring & Analytics

### Performance Monitoring
```php
// Log slow queries
DB::listen(function ($query) {
    if ($query->time > 1000) {
        Log::warning('Slow query detected', [
            'sql' => $query->sql,
            'time' => $query->time
        ]);
    }
});
```

### Error Tracking
```php
// Report errors
try {
    $this->processPayment($order);
} catch (Exception $e) {
    report($e);
    return response()->json(['error' => 'Payment failed'], 500);
}
```

## 🎉 Release Process

### Version Numbering
We use [Semantic Versioning](https://semver.org/):
- **MAJOR**: Breaking changes
- **MINOR**: New features (backward compatible)
- **PATCH**: Bug fixes (backward compatible)

### Release Checklist
- [ ] Update version numbers
- [ ] Update changelog
- [ ] Run all tests
- [ ] Update documentation
- [ ] Create release notes
- [ ] Tag release
- [ ] Deploy to staging
- [ ] Deploy to production

## 🙏 Recognition

### Contributors
We appreciate all contributors! Check out our [Hall of Fame](CONTRIBUTORS.md).

### How to Get Recognized
- **Submit quality PRs**
- **Help with code reviews**
- **Improve documentation**
- **Help community members**
- **Report bugs**

---

**Thank you for contributing to Mewayz!** 🎉

Together, we're building the future of creator economy platforms.

**Need Help?**
- 📧 Contributors: contributors@mewayz.com
- 💬 Discord: [discord.gg/mewayz-dev](https://discord.gg/mewayz-dev)
- 📚 Documentation: [docs.mewayz.com](https://docs.mewayz.com)

**Last Updated**: January 2025  
**Version**: 1.0.0