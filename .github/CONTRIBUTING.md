# Contributing to Mewayz

Thank you for your interest in contributing to Mewayz! We welcome contributions from everyone. This document provides guidelines for contributing to this project.

## 🚀 Quick Start

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## 📋 Code of Conduct

By participating in this project, you are expected to uphold our Code of Conduct:

- **Be respectful** and inclusive
- **Be collaborative** and constructive
- **Be responsible** for your actions
- **Be patient** with newcomers

## 🔄 Development Process

### 1. Setting Up Development Environment

```bash
# Clone your fork
git clone https://github.com/YOUR_USERNAME/mewayz.git
cd mewayz

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate
php artisan db:seed

# Start development server
php artisan serve
npm run dev
```

### 2. Branch Naming Convention

- `feature/feature-name` - New features
- `bugfix/bug-description` - Bug fixes
- `hotfix/critical-fix` - Critical production fixes
- `docs/documentation-update` - Documentation updates
- `refactor/code-improvement` - Code refactoring

### 3. Commit Message Format

We use [Conventional Commits](https://www.conventionalcommits.org/):

```
type(scope): description

feat(auth): add OAuth integration
fix(bio-sites): resolve link ordering issue
docs(api): update authentication guide
style(ui): improve button styling
refactor(services): optimize user service
test(courses): add lesson creation tests
```

**Types:**
- `feat` - New feature
- `fix` - Bug fix
- `docs` - Documentation
- `style` - Code style changes
- `refactor` - Code refactoring
- `test` - Adding tests
- `chore` - Maintenance tasks

## 🧪 Testing

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Run with coverage
php artisan test --coverage
```

### Writing Tests

All new features must include tests:

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class BioSiteTest extends TestCase
{
    public function test_user_can_create_bio_site()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        $response = $this->postJson('/api/bio-sites', [
            'name' => 'Test Bio Site',
            'address' => 'testhandle'
        ]);
        
        $response->assertStatus(201);
        $this->assertDatabaseHas('bio_sites', [
            'name' => 'Test Bio Site',
            'address' => 'testhandle'
        ]);
    }
}
```

## 📝 Code Style

### PHP

We follow **PSR-12** standards:

```php
<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\BioSite;
use Illuminate\Database\Eloquent\Collection;

class BioSiteService
{
    public function __construct(
        private BioSite $bioSite
    ) {}

    public function createBioSite(array $data): BioSite
    {
        return $this->bioSite->create($data);
    }
}
```

### JavaScript

```javascript
// Use const/let instead of var
const users = await fetchUsers();
let currentPage = 1;

// Use arrow functions
const processUser = (user) => {
    return {
        id: user.id,
        name: user.name.toUpperCase(),
    };
};

// Use template literals
const message = `Welcome ${user.name}!`;
```

### CSS/Tailwind

```html
<!-- Use Tailwind utility classes -->
<div class="bg-white shadow-md rounded-lg p-6">
    <h2 class="text-xl font-semibold text-gray-800 mb-4">
        Title
    </h2>
    <p class="text-gray-600">
        Description text
    </p>
</div>
```

## 🐛 Bug Reports

### Before Reporting

1. Check existing issues
2. Test on latest version
3. Gather debugging information

### Bug Report Template

```markdown
## Bug Description
Clear description of the bug

## Steps to Reproduce
1. Step one
2. Step two
3. Step three

## Expected Behavior
What should happen

## Actual Behavior
What actually happens

## Environment
- OS: [e.g. macOS 12.0]
- Browser: [e.g. Chrome 98]
- PHP Version: [e.g. 8.2]
- Laravel Version: [e.g. 11.0]

## Screenshots
If applicable
```

## 💡 Feature Requests

### Before Requesting

1. Check existing feature requests
2. Discuss in community Discord
3. Consider implementation complexity

### Feature Request Template

```markdown
## Feature Summary
Brief description

## Problem Statement
What problem does this solve?

## Proposed Solution
How should it work?

## Use Cases
When would this be used?

## Technical Considerations
Implementation notes
```

## 🔧 Pull Request Process

### 1. Before Submitting

- [ ] Tests pass locally
- [ ] Code follows style guidelines
- [ ] Documentation updated
- [ ] Changelog updated (if needed)

### 2. Pull Request Checklist

- [ ] Clear title and description
- [ ] Linked to relevant issues
- [ ] Tests added/updated
- [ ] Documentation updated
- [ ] No breaking changes (or clearly marked)

### 3. Review Process

1. **Automated checks** must pass
2. **Code review** by maintainers
3. **Manual testing** if needed
4. **Approval** and merge

## 📚 Documentation

### Code Documentation

```php
/**
 * Create a new bio site for the user.
 *
 * @param  array  $data
 * @return BioSite
 * @throws ValidationException
 */
public function createBioSite(array $data): BioSite
{
    // Implementation
}
```

### User Documentation

When adding new features:

1. Update user guide
2. Add screenshots
3. Include examples
4. Update API docs

## 🏷️ Issue Labels

### Type Labels
- `bug` - Something isn't working
- `enhancement` - New feature or improvement
- `documentation` - Documentation update
- `question` - Further information needed

### Priority Labels
- `priority: low` - Nice to have
- `priority: medium` - Standard priority
- `priority: high` - Important
- `priority: critical` - Must fix immediately

### Status Labels
- `status: needs review` - Needs review
- `status: in progress` - Being worked on
- `status: blocked` - Blocked by other work

## 🎯 Areas for Contribution

### High Priority
- [ ] Performance optimizations
- [ ] Mobile responsiveness
- [ ] API documentation
- [ ] Test coverage
- [ ] Security improvements

### Medium Priority
- [ ] UI/UX improvements
- [ ] Feature enhancements
- [ ] Integration testing
- [ ] Error handling
- [ ] Accessibility

### Low Priority
- [ ] Code refactoring
- [ ] Documentation improvements
- [ ] Example applications
- [ ] Community features

## 🛡️ Security

### Reporting Security Issues

**DO NOT** create public issues for security vulnerabilities.

Email: security@mewayz.com

Include:
- Description of vulnerability
- Steps to reproduce
- Potential impact
- Suggested fix (if any)

### Security Guidelines

- Validate all inputs
- Use parameterized queries
- Implement proper authentication
- Follow Laravel security best practices

## 🤝 Community

### Communication Channels

- **GitHub Issues** - Bug reports and feature requests
- **GitHub Discussions** - General discussions
- **Discord** - Real-time chat
- **Twitter** - Updates and announcements

### Getting Help

1. Check documentation
2. Search existing issues
3. Ask in Discord
4. Create GitHub issue

## 🏆 Recognition

### Contributor Levels

- **Contributor** - Made valuable contributions
- **Regular Contributor** - Consistent contributions
- **Core Contributor** - Major feature contributions
- **Maintainer** - Project maintenance responsibilities

### Hall of Fame

Contributors are recognized in:
- GitHub contributors page
- Project documentation
- Release notes
- Annual contributor awards

## 📈 Project Metrics

We track:
- Code coverage
- Performance metrics
- User adoption
- Community growth
- Issue resolution time

## 🎉 First-Time Contributors

### Good First Issues

Look for issues labeled `good first issue`:
- Documentation improvements
- Small bug fixes
- UI enhancements
- Test additions

### Mentorship

New contributors receive:
- Code review feedback
- Implementation guidance
- Best practice recommendations
- Community support

## 📅 Release Process

### Versioning

We use [Semantic Versioning](https://semver.org/):
- `MAJOR.MINOR.PATCH`
- Major: Breaking changes
- Minor: New features
- Patch: Bug fixes

### Release Schedule

- **Major releases** - Every 6 months
- **Minor releases** - Every 2 months
- **Patch releases** - As needed

## 🔄 Maintenance

### Regular Tasks

- Dependency updates
- Security patches
- Performance monitoring
- Documentation updates
- Community engagement

### Long-term Goals

- Scalability improvements
- New integrations
- Mobile applications
- Enterprise features

---

## 🙏 Thank You

Thank you for contributing to Mewayz! Your contributions help make this project better for everyone.

**Questions?** Join our Discord: https://discord.gg/mewayz