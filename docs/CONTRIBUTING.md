# Contributing to Mewayz Platform

Thank you for your interest in contributing to the Mewayz platform! This document provides guidelines and information for contributors.

## 🤝 Code of Conduct

By participating in this project, you agree to abide by our [Code of Conduct](CODE_OF_CONDUCT.md). Please read it to understand the expected behavior when interacting with the community.

## 🚀 Getting Started

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- Git
- MariaDB/MySQL
- Basic knowledge of Laravel and modern web development

### Development Setup

1. **Fork and Clone**
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

5. **Build Assets**
   ```bash
   npm run dev
   ```

## 📋 How to Contribute

### Types of Contributions
- **Bug Reports**: Help us identify and fix issues
- **Feature Requests**: Suggest new functionality
- **Code Contributions**: Submit bug fixes or new features
- **Documentation**: Improve or add documentation
- **Testing**: Write tests and improve test coverage
- **UI/UX**: Enhance user interface and experience

### Before You Start
1. Check existing issues to avoid duplicates
2. Discuss major changes in an issue first
3. Ensure your development environment is set up
4. Read the relevant documentation

## 🐛 Reporting Bugs

### Bug Report Template
```markdown
## Bug Description
A clear and concise description of the bug.

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
- OS: [e.g., Ubuntu 20.04]
- PHP Version: [e.g., 8.2]
- Laravel Version: [e.g., 10.48]
- Browser: [e.g., Chrome 120]

## Screenshots
If applicable, add screenshots to help explain the problem.

## Additional Context
Any other context about the problem.
```

### Security Issues
For security vulnerabilities, please email security@mewayz.com instead of creating a public issue.

## 💡 Feature Requests

### Feature Request Template
```markdown
## Feature Description
A clear and concise description of the feature.

## Problem Statement
What problem does this feature solve?

## Proposed Solution
How would you like this feature to work?

## Alternatives Considered
What other approaches did you consider?

## Additional Context
Any other context, mockups, or examples.
```

## 🔧 Development Guidelines

### Code Style
We follow PSR-12 coding standards and Laravel conventions.

#### PHP Code Style
```php
<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class ExampleService
{
    public function processUser(User $user): bool
    {
        // Use descriptive variable names
        $isValidUser = $this->validateUser($user);
        
        if (!$isValidUser) {
            Log::warning('Invalid user processed', ['user_id' => $user->id]);
            return false;
        }
        
        // Process user
        return true;
    }
    
    private function validateUser(User $user): bool
    {
        return $user->email_verified_at !== null;
    }
}
```

#### JavaScript Code Style
```javascript
// Use modern ES6+ syntax
const handlePayment = async (packageId) => {
    try {
        const response = await fetch('/api/payments/checkout/session', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                package_id: packageId,
                success_url: window.location.origin + '/success',
                cancel_url: window.location.origin + '/cancel',
            }),
        });
        
        const data = await response.json();
        
        if (data.success) {
            window.location.href = data.url;
        } else {
            throw new Error(data.error || 'Payment failed');
        }
    } catch (error) {
        console.error('Payment error:', error);
        showErrorMessage(error.message);
    }
};
```

#### CSS/SCSS Style
```scss
// Use BEM methodology and utility classes
.payment-card {
    @apply bg-card-bg rounded-lg p-6 border border-border-color;
    
    &__header {
        @apply flex items-center justify-between mb-4;
    }
    
    &__title {
        @apply text-xl font-semibold text-primary-text;
    }
    
    &__button {
        @apply btn btn-primary w-full;
        
        &:hover {
            @apply bg-primary-hover;
        }
        
        &:disabled {
            @apply bg-gray-400 cursor-not-allowed;
        }
    }
}
```

### Database Guidelines

#### Migrations
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('stripe_session_id')->unique();
            $table->string('package_id');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3);
            $table->string('status');
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index('stripe_session_id');
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
```

#### Model Definitions
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentTransaction extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'stripe_session_id',
        'package_id',
        'amount',
        'currency',
        'status',
        'metadata',
    ];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
```

### Testing Guidelines

#### Writing Tests
```php
<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\PaymentTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_user_can_create_checkout_session(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->postJson('/api/payments/checkout/session', [
                'package_id' => 'starter',
                'success_url' => 'https://example.com/success',
                'cancel_url' => 'https://example.com/cancel',
            ]);
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'session_id',
                'url',
            ]);
        
        $this->assertDatabaseHas('payment_transactions', [
            'user_id' => $user->id,
            'package_id' => 'starter',
            'status' => 'pending',
        ]);
    }
    
    public function test_invalid_package_id_returns_validation_error(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->postJson('/api/payments/checkout/session', [
                'package_id' => 'invalid',
                'success_url' => 'https://example.com/success',
                'cancel_url' => 'https://example.com/cancel',
            ]);
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['package_id']);
    }
}
```

#### Running Tests
```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/PaymentTest.php

# Run tests with coverage
php artisan test --coverage

# Run tests in parallel
php artisan test --parallel
```

## 📝 Pull Request Process

### Pull Request Template
```markdown
## Description
Brief description of the changes made.

## Type of Change
- [ ] Bug fix (non-breaking change that fixes an issue)
- [ ] New feature (non-breaking change that adds functionality)
- [ ] Breaking change (fix or feature that would cause existing functionality to not work as expected)
- [ ] Documentation update
- [ ] Performance improvement
- [ ] Code refactoring

## Changes Made
- List the specific changes made
- Include any new files or modified files
- Mention any database changes

## Testing
- [ ] Tests pass locally
- [ ] New tests added for new functionality
- [ ] Manual testing completed

## Documentation
- [ ] Code is self-documenting
- [ ] README updated (if applicable)
- [ ] API documentation updated (if applicable)
- [ ] Changelog updated

## Screenshots (if applicable)
Add screenshots to help explain your changes.

## Checklist
- [ ] My code follows the project's coding standards
- [ ] I have performed a self-review of my own code
- [ ] I have commented my code, particularly in hard-to-understand areas
- [ ] I have made corresponding changes to the documentation
- [ ] My changes generate no new warnings
- [ ] I have added tests that prove my fix is effective or that my feature works
- [ ] New and existing unit tests pass locally with my changes
```

### Pull Request Guidelines
1. **Create a branch**: Use descriptive branch names
   ```bash
   git checkout -b feature/stripe-payment-integration
   git checkout -b bugfix/authentication-session-timeout
   ```

2. **Make commits**: Write clear, descriptive commit messages
   ```bash
   git commit -m "Add Stripe payment integration with checkout sessions"
   git commit -m "Fix authentication session timeout issue"
   ```

3. **Keep PRs focused**: One feature or fix per pull request
4. **Write tests**: Include tests for new functionality
5. **Update documentation**: Keep docs up to date
6. **Review your own PR**: Check for any issues before submitting

### Review Process
1. **Automated checks**: CI/CD pipeline runs tests
2. **Code review**: Team members review the code
3. **Testing**: Manual testing if required
4. **Approval**: At least one approval required
5. **Merge**: Squash and merge to main branch

## 🧪 Testing

### Test Categories
- **Unit Tests**: Test individual components
- **Feature Tests**: Test application features
- **Integration Tests**: Test component interactions
- **Browser Tests**: Test user interactions

### Test Coverage Goals
- **Overall coverage**: > 80%
- **Critical paths**: 100% coverage
- **New features**: 100% coverage
- **Bug fixes**: Include regression tests

### Running Tests
```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test suite
php artisan test --testsuite=Feature

# Run tests in parallel
php artisan test --parallel
```

## 📚 Documentation

### Documentation Standards
- **Clear and concise**: Easy to understand
- **Up to date**: Reflect current functionality
- **Complete**: Cover all features and APIs
- **Examples**: Include code examples
- **Structured**: Well-organized and searchable

### Documentation Types
- **API Documentation**: Complete API reference
- **User Guide**: End-user documentation
- **Development Guide**: Setup and development
- **Architecture**: System design and structure
- **Troubleshooting**: Common issues and solutions

### Writing Documentation
```markdown
# Feature Name

## Overview
Brief description of the feature.

## Installation
Step-by-step installation instructions.

## Usage
How to use the feature with examples.

## API Reference
Detailed API documentation.

## Examples
Code examples and use cases.

## Troubleshooting
Common issues and solutions.
```

## 🔄 Release Process

### Version Numbers
We follow [Semantic Versioning](https://semver.org/):
- **MAJOR**: Breaking changes
- **MINOR**: New features, backward compatible
- **PATCH**: Bug fixes

### Release Checklist
- [ ] Update version numbers
- [ ] Update changelog
- [ ] Run full test suite
- [ ] Update documentation
- [ ] Create release notes
- [ ] Tag release
- [ ] Deploy to production

## 🏆 Recognition

### Contributors
We recognize all contributors in:
- README.md contributors section
- Release notes
- Annual contributor awards
- Community highlights

### Contribution Types
- **Code**: Bug fixes, features, refactoring
- **Documentation**: Writing, editing, translation
- **Testing**: Bug reports, test writing
- **Design**: UI/UX improvements
- **Community**: Support, moderation, advocacy

## 📞 Getting Help

### Communication Channels
- **GitHub Issues**: Bug reports and feature requests
- **GitHub Discussions**: General discussions
- **Email**: development@mewayz.com
- **Discord**: Community chat (link in README)

### Mentorship
New contributors can request mentorship:
- **Code review**: Detailed feedback on PRs
- **Architecture guidance**: System design help
- **Career advice**: Professional development

## 📋 Contribution Ideas

### Good First Issues
- Documentation improvements
- Small bug fixes
- Test coverage improvements
- UI/UX enhancements
- Code cleanup and refactoring

### Advanced Contributions
- New feature development
- Performance optimizations
- Security improvements
- API design
- Architecture enhancements

### Long-term Projects
- Mobile app development
- AI feature integration
- Multi-language support
- Advanced analytics
- Enterprise features

## 🎯 Goals and Roadmap

### Short-term Goals (Q1 2025)
- Improve test coverage to 90%
- Add more comprehensive documentation
- Implement advanced analytics
- Enhance mobile responsiveness

### Long-term Goals (2025)
- Mobile app launch
- AI-powered features
- Enterprise edition
- International expansion
- Community-driven development

## 🙏 Thank You

We appreciate every contribution, no matter how small. Your involvement helps make Mewayz better for everyone. Thank you for being part of our community!

---

**Last Updated**: January 16, 2025  
**Contributing Guide Version**: 2.0  
**Next Review**: April 2025

*This guide is a living document and will be updated as the project evolves.*