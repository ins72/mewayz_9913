# Mewayz Platform - Developer Guide

*Technical documentation for developers building on Mewayz Platform v2.0*

## 🏗️ Architecture Overview

Mewayz Platform is built on modern Laravel architecture with enterprise-grade patterns and practices. This guide provides deep technical insights for developers, system architects, and DevOps engineers.

### Technology Stack
- **Backend**: Laravel 11.x (PHP 8.2+)
- **Database**: MySQL 8.0+ with Redis for caching
- **Frontend**: Laravel Blade + Alpine.js + Tailwind CSS
- **Build System**: Vite.js for modern asset bundling
- **Queue System**: Laravel Queue with Redis/Database drivers
- **Authentication**: Laravel Sanctum with custom middleware
- **Real-time**: WebSocket with Laravel Broadcasting
- **Testing**: PHPUnit with feature and unit tests

---

## 🚀 Development Environment Setup

### Prerequisites
```bash
# Required software
PHP 8.2+
MySQL 8.0+
Redis 6.0+
Node.js 18+
NPM/Yarn
Composer 2.x
Git
```

### Local Development Setup
```bash
# 1. Clone and setup repository
git clone https://github.com/mewayz/platform.git
cd mewayz-platform

# 2. Install PHP dependencies
composer install

# 3. Install Node.js dependencies
npm install

# 4. Environment configuration
cp .env.example .env
php artisan key:generate

# 5. Database setup
mysql -u root -p -e "CREATE DATABASE mewayz_dev"
php artisan migrate --seed

# 6. Build assets
npm run dev

# 7. Start development server
php artisan serve --host=0.0.0.0 --port=8001

# 8. Start queue worker (separate terminal)
php artisan queue:work

# 9. Start task scheduler (separate terminal)
php artisan schedule:work
```

### Docker Development (Alternative)
```bash
# Using Laravel Sail
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail npm run dev
```

---

## 🏛️ Application Architecture

### Directory Structure
```
/app/
├── app/                    # Laravel application core
│   ├── Console/           # Artisan commands
│   ├── Exceptions/        # Exception handling
│   ├── Http/              # HTTP layer
│   │   ├── Controllers/   # Request controllers
│   │   │   ├── Api/       # API controllers
│   │   │   ├── Admin/     # Admin controllers
│   │   │   └── Web/       # Web controllers
│   │   ├── Middleware/    # Request middleware
│   │   ├── Requests/      # Form request validation
│   │   └── Resources/     # API resources
│   ├── Models/            # Eloquent models
│   ├── Providers/         # Service providers
│   ├── Services/          # Business logic services
│   └── Traits/            # Reusable traits
├── database/              # Database layer
│   ├── factories/         # Model factories
│   ├── migrations/        # Database migrations
│   └── seeders/           # Database seeders
├── resources/             # Frontend resources
│   ├── css/               # Stylesheets
│   ├── js/                # JavaScript files
│   └── views/             # Blade templates
├── routes/                # Route definitions
│   ├── api.php            # API routes
│   ├── web.php            # Web routes
│   ├── admin.php          # Admin routes
│   └── channels.php       # Broadcasting routes
├── storage/               # Application storage
├── tests/                 # Test suites
├── public/                # Public assets
└── docs/                  # Documentation
```

### Design Patterns Used

#### Repository Pattern
```php
namespace App\Repositories;

interface WorkspaceRepositoryInterface
{
    public function findById(int $id): Workspace;
    public function create(array $data): Workspace;
    public function update(int $id, array $data): Workspace;
    public function delete(int $id): bool;
}

class WorkspaceRepository implements WorkspaceRepositoryInterface
{
    public function __construct(private Workspace $model) {}
    
    public function findById(int $id): Workspace
    {
        return $this->model->findOrFail($id);
    }
    
    // Implementation...
}
```

#### Service Layer Pattern
```php
namespace App\Services;

class WorkspaceService
{
    public function __construct(
        private WorkspaceRepositoryInterface $repository,
        private FeatureService $featureService
    ) {}
    
    public function createWorkspace(array $data): Workspace
    {
        $workspace = $this->repository->create($data);
        
        // Apply business logic
        $this->featureService->assignDefaultFeatures($workspace);
        
        // Trigger events
        WorkspaceCreated::dispatch($workspace);
        
        return $workspace;
    }
}
```

#### Observer Pattern
```php
namespace App\Observers;

class WorkspaceObserver
{
    public function creating(Workspace $workspace): void
    {
        $workspace->uuid = Str::uuid();
        $workspace->slug = Str::slug($workspace->name);
    }
    
    public function created(Workspace $workspace): void
    {
        $this->createDefaultFeatures($workspace);
        $this->sendWelcomeEmail($workspace);
    }
}
```

---

## 🗄️ Database Architecture

### Entity Relationship Design

#### Core Entities
```sql
-- Users and Authentication
users (id, uuid, email, name, password, settings)
personal_access_tokens (id, tokenable_id, tokenable_type, name, token)

-- Multi-tenant Workspace System
workspaces (id, uuid, name, slug, description, settings, subscription_plan_id)
workspace_users (id, workspace_id, user_id, role, permissions)

-- Feature Management System
goals (id, key, name, description, icon, category)
features (id, key, name, description, goal_key, type, is_active)
workspace_goals (id, workspace_id, goal_key, is_enabled, settings)
workspace_features (id, workspace_id, feature_key, is_enabled, quota_limit, usage_count)

-- Subscription & Billing
subscription_plans (id, name, pricing_type, base_price, feature_price_monthly)
plan_features (id, plan_id, feature_key, is_included, quota_limit)
subscriptions (id, workspace_id, plan_id, status, current_period_start, current_period_end)
payment_methods (id, workspace_id, stripe_payment_method_id, type, is_default)
```

#### Business Domain Models
```sql
-- Social Media Management
social_accounts (id, workspace_id, platform, username, access_token, profile_data)
social_posts (id, workspace_id, social_account_id, content, media_urls, scheduled_for, status)

-- Link in Bio
link_pages (id, workspace_id, slug, title, description, theme_settings, view_count)
link_blocks (id, link_page_id, type, title, content, url, sort_order, click_count)

-- E-commerce
products (id, workspace_id, name, description, price, sku, inventory_quantity, images)
orders (id, workspace_id, order_number, customer_email, total_amount, status)
order_items (id, order_id, product_id, quantity, price)

-- Course Management
courses (id, workspace_id, title, description, price, thumbnail_url, status)
course_modules (id, course_id, title, sort_order, is_published)
course_lessons (id, module_id, title, content, video_url, duration_minutes)
course_enrollments (id, course_id, user_id, enrolled_at, completed_at)
```

### Migration Strategy
```php
// Example migration
class CreateWorkspaceFeaturesTable extends Migration
{
    public function up(): void
    {
        Schema::create('workspace_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->string('feature_key');
            $table->boolean('is_enabled')->default(true);
            $table->integer('quota_limit')->nullable();
            $table->integer('usage_count')->default(0);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            
            $table->unique(['workspace_id', 'feature_key']);
            $table->index(['workspace_id', 'is_enabled']);
            $table->foreign('feature_key')->references('key')->on('features');
        });
    }
}
```

---

## 🔌 API Architecture

### RESTful API Design

#### Resource Controllers
```php
namespace App\Http\Controllers\Api;

class WorkspaceController extends Controller
{
    public function __construct(private WorkspaceService $service) {}
    
    public function index(Request $request): JsonResponse
    {
        $workspaces = $this->service->getUserWorkspaces(
            $request->user(),
            $request->get('page', 1)
        );
        
        return WorkspaceResource::collection($workspaces)
            ->response()
            ->setStatusCode(200);
    }
    
    public function store(CreateWorkspaceRequest $request): JsonResponse
    {
        $workspace = $this->service->createWorkspace($request->validated());
        
        return (new WorkspaceResource($workspace))
            ->response()
            ->setStatusCode(201);
    }
    
    public function show(Workspace $workspace): JsonResponse
    {
        $this->authorize('view', $workspace);
        
        return new WorkspaceResource($workspace);
    }
}
```

#### API Resources
```php
namespace App\Http\Resources;

class WorkspaceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'goals' => GoalResource::collection($this->whenLoaded('goals')),
            'features' => FeatureResource::collection($this->whenLoaded('features')),
            'subscription' => new SubscriptionResource($this->whenLoaded('subscription')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
```

#### Form Request Validation
```php
namespace App\Http\Requests;

class CreateWorkspaceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'goals' => ['required', 'array', 'min:1'],
            'goals.*' => ['required', 'string', 'exists:goals,key'],
        ];
    }
    
    public function messages(): array
    {
        return [
            'goals.required' => 'Please select at least one business goal.',
            'goals.*.exists' => 'Invalid goal selected.',
        ];
    }
}
```

### Authentication & Authorization

#### Custom Sanctum Middleware
```php
namespace App\Http\Middleware;

class CustomSanctumAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken() ?? $request->get('api_token');
        
        if (!$token) {
            return response()->json(['error' => 'Authentication required'], 401);
        }
        
        $personalAccessToken = PersonalAccessToken::findToken($token);
        
        if (!$personalAccessToken || !$personalAccessToken->tokenable) {
            return response()->json(['error' => 'Invalid token'], 401);
        }
        
        $user = $personalAccessToken->tokenable;
        $request->setUserResolver(fn () => $user);
        
        return $next($request);
    }
}
```

#### Policy-Based Authorization
```php
namespace App\Policies;

class WorkspacePolicy
{
    public function view(User $user, Workspace $workspace): bool
    {
        return $workspace->users()->where('user_id', $user->id)->exists();
    }
    
    public function update(User $user, Workspace $workspace): bool
    {
        return $workspace->users()
            ->where('user_id', $user->id)
            ->whereIn('role', ['owner', 'admin'])
            ->exists();
    }
    
    public function delete(User $user, Workspace $workspace): bool
    {
        return $workspace->users()
            ->where('user_id', $user->id)
            ->where('role', 'owner')
            ->exists();
    }
}
```

---

## 🎨 Frontend Architecture

### Blade Components
```php
namespace App\View\Components;

class FeatureGate extends Component
{
    public function __construct(
        public string $feature,
        public ?Workspace $workspace = null
    ) {}
    
    public function render(): View
    {
        return view('components.feature-gate');
    }
    
    public function shouldRender(): bool
    {
        $workspace = $this->workspace ?? request()->user()?->currentWorkspace;
        
        return $workspace && $workspace->hasFeature($this->feature);
    }
}
```

```blade
{{-- resources/views/components/feature-gate.blade.php --}}
@if($shouldRender())
    {{ $slot }}
@else
    <div class="feature-locked">
        <p>This feature is not available in your current plan.</p>
        <a href="{{ route('subscription.plans') }}" class="btn-primary">Upgrade Now</a>
    </div>
@endif
```

### Alpine.js Integration
```html
<!-- Workspace Selector Component -->
<div x-data="workspaceSelector()" x-init="loadWorkspaces()">
    <select x-model="selectedWorkspace" @change="switchWorkspace()">
        <template x-for="workspace in workspaces" :key="workspace.id">
            <option :value="workspace.id" x-text="workspace.name"></option>
        </template>
    </select>
</div>

<script>
function workspaceSelector() {
    return {
        workspaces: [],
        selectedWorkspace: null,
        
        async loadWorkspaces() {
            try {
                const response = await fetch('/api/workspaces', {
                    headers: {
                        'Authorization': `Bearer ${this.getToken()}`,
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                this.workspaces = data.data;
            } catch (error) {
                console.error('Failed to load workspaces:', error);
            }
        },
        
        async switchWorkspace() {
            // Implementation for workspace switching
        }
    }
}
</script>
```

### Tailwind CSS Configuration
```javascript
// tailwind.config.js
module.exports = {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            colors: {
                'app-bg': '#FAFAFA',
                'card-bg': '#FFFFFF',
                'primary-text': '#1A1A1A',
                'secondary-text': '#6B6B6B',
                'primary-button': '#1A1A1A',
                'accent-blue': '#007AFF',
                'success-green': '#26DE81',
                'warning-orange': '#F9CA24',
                'error-red': '#FF3838',
            },
            fontFamily: {
                sans: ['Inter', 'sans-serif'],
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
}
```

---

## 🧪 Testing Strategy

### Test Structure
```
/tests/
├── Feature/               # Integration tests
│   ├── Api/              # API endpoint tests
│   ├── Auth/             # Authentication tests
│   └── Workspace/        # Workspace feature tests
├── Unit/                 # Unit tests
│   ├── Models/           # Model tests
│   ├── Services/         # Service tests
│   └── Helpers/          # Helper function tests
└── TestCase.php          # Base test case
```

### Feature Testing Example
```php
namespace Tests\Feature\Api;

class WorkspaceControllerTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_user_can_create_workspace(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/workspaces', [
                'name' => 'Test Workspace',
                'description' => 'A test workspace',
                'goals' => ['instagram', 'ecommerce']
            ]);
            
        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id', 'uuid', 'name', 'slug', 'description', 'goals'
                ]
            ]);
            
        $this->assertDatabaseHas('workspaces', [
            'name' => 'Test Workspace',
            'slug' => 'test-workspace'
        ]);
    }
    
    public function test_workspace_creation_requires_authentication(): void
    {
        $response = $this->postJson('/api/workspaces', [
            'name' => 'Test Workspace'
        ]);
        
        $response->assertStatus(401);
    }
}
```

### Unit Testing Example
```php
namespace Tests\Unit\Services;

class FeatureServiceTest extends TestCase
{
    public function test_can_check_feature_access(): void
    {
        $workspace = Workspace::factory()->create();
        $feature = Feature::factory()->create(['key' => 'test_feature']);
        
        $workspace->features()->attach($feature->id, [
            'is_enabled' => true,
            'quota_limit' => 100,
            'usage_count' => 50
        ]);
        
        $service = app(FeatureService::class);
        
        $this->assertTrue($service->hasAccess($workspace, 'test_feature'));
        $this->assertFalse($service->hasReachedLimit($workspace, 'test_feature'));
    }
}
```

---

## 🚀 Deployment

### Production Deployment Checklist
```bash
# 1. Server requirements
- PHP 8.2+
- MySQL 8.0+
- Redis 6.0+
- Nginx/Apache
- SSL Certificate
- Process Manager (Supervisor)

# 2. Application deployment
composer install --no-dev --optimize-autoloader
npm ci --only=production
npm run build

# 3. Laravel optimization
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart

# 4. Database migration
php artisan migrate --force

# 5. Storage permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 6. Process management
supervisorctl start all
```

### Docker Production Setup
```dockerfile
# Dockerfile
FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libxml2-dev zip unzip

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql gd xml

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www
RUN chmod -R 755 /var/www/storage

EXPOSE 9000
CMD ["php-fpm"]
```

### Environment Configuration
```env
# Production environment variables
APP_NAME="Mewayz Platform"
APP_ENV=production
APP_KEY=base64:your-generated-key
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=mewayz_production
DB_USERNAME=mewayz_user
DB_PASSWORD=secure_password

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=redis
REDIS_PASSWORD=redis_password
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
```

---

## 📊 Performance Optimization

### Database Optimization
```php
// Query optimization with eager loading
$workspaces = Workspace::with([
    'users' => fn($q) => $q->select('id', 'name', 'email'),
    'features' => fn($q) => $q->where('is_enabled', true),
    'subscription.plan'
])->paginate(20);

// Index optimization
Schema::table('workspace_features', function (Blueprint $table) {
    $table->index(['workspace_id', 'is_enabled']);
    $table->index(['feature_key', 'usage_count']);
    $table->index(['last_used_at']);
});

// Query scopes for reusable filters
class Workspace extends Model
{
    public function scopeActive($query)
    {
        return $query->whereHas('subscription', fn($q) => 
            $q->where('status', 'active')
        );
    }
    
    public function scopeWithFeature($query, string $featureKey)
    {
        return $query->whereHas('features', fn($q) => 
            $q->where('feature_key', $featureKey)
             ->where('is_enabled', true)
        );
    }
}
```

### Caching Strategy
```php
// Repository with caching
class WorkspaceRepository
{
    public function findById(int $id): Workspace
    {
        return Cache::tags(['workspaces'])
            ->remember("workspace.{$id}", 3600, fn() => 
                Workspace::with('features', 'users')->findOrFail($id)
            );
    }
    
    public function getUserWorkspaces(User $user): Collection
    {
        return Cache::tags(['workspaces', "user.{$user->id}"])
            ->remember("user.{$user->id}.workspaces", 1800, fn() =>
                $user->workspaces()->with('subscription.plan')->get()
            );
    }
}

// Cache invalidation
class WorkspaceObserver
{
    public function updated(Workspace $workspace): void
    {
        Cache::tags(['workspaces'])->flush();
        Cache::forget("workspace.{$workspace->id}");
    }
}
```

### Queue Optimization
```php
// Efficient job processing
class ProcessSocialMediaPost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public int $timeout = 120;
    public int $tries = 3;
    public int $backoff = 60;
    
    public function __construct(
        private int $postId,
        private array $platforms
    ) {}
    
    public function handle(): void
    {
        $post = SocialPost::find($this->postId);
        
        if (!$post) {
            $this->fail('Post not found');
            return;
        }
        
        foreach ($this->platforms as $platform) {
            dispatch(new PublishToSocialPlatform($post, $platform))
                ->onQueue('social-publishing');
        }
    }
}
```

---

## 🔒 Security Best Practices

### Input Validation
```php
// Comprehensive validation
class CreateProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'sanitize:string'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999'],
            'description' => ['nullable', 'string', 'max:5000', 'sanitize:html'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['required', 'image', 'max:10240', 'mimes:jpeg,png,webp'],
        ];
    }
    
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => strip_tags($this->name),
            'price' => (float) $this->price,
        ]);
    }
}
```

### Rate Limiting
```php
// API rate limiting
Route::middleware(['throttle:api'])->group(function () {
    Route::apiResource('workspaces', WorkspaceController::class);
});

// Custom rate limiting
Route::middleware(['throttle:social-posting'])->group(function () {
    Route::post('/social-media/posts', [SocialMediaController::class, 'store']);
});

// In RouteServiceProvider
RateLimiter::for('social-posting', function (Request $request) {
    return $request->user()
        ? Limit::perMinute(10)->by($request->user()->id)
        : Limit::perMinute(2)->by($request->ip());
});
```

### Data Protection
```php
// Model encryption
class User extends Model
{
    protected $casts = [
        'social_tokens' => 'encrypted:array',
        'payment_methods' => 'encrypted:array',
    ];
    
    protected $hidden = [
        'password', 'social_tokens', 'remember_token'
    ];
}

// Audit logging
class AuditLogger
{
    public static function log(string $action, Model $model, array $changes = []): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'changes' => $changes,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }
}
```

---

## 📈 Monitoring & Logging

### Application Monitoring
```php
// Custom monitoring service
class ApplicationMonitor
{
    public function recordMetric(string $name, float $value, array $tags = []): void
    {
        Metric::create([
            'name' => $name,
            'value' => $value,
            'tags' => $tags,
            'recorded_at' => now(),
        ]);
    }
    
    public function recordApiCall(Request $request, Response $response): void
    {
        $this->recordMetric('api.request', 1, [
            'endpoint' => $request->path(),
            'method' => $request->method(),
            'status_code' => $response->status(),
            'response_time' => $this->getResponseTime(),
        ]);
    }
}

// Middleware for monitoring
class ApiMonitoringMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        
        $response = $next($request);
        
        $responseTime = (microtime(true) - $startTime) * 1000;
        
        app(ApplicationMonitor::class)->recordApiCall($request, $response);
        
        return $response;
    }
}
```

### Structured Logging
```php
// Log service
class LogService
{
    public function logUserAction(string $action, array $context = []): void
    {
        Log::info('User action performed', [
            'action' => $action,
            'user_id' => auth()->id(),
            'workspace_id' => auth()->user()?->currentWorkspace?->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString(),
            ...$context
        ]);
    }
    
    public function logSystemEvent(string $event, array $data = []): void
    {
        Log::channel('system')->info($event, $data);
    }
}
```

---

## 🚦 Continuous Integration/Deployment

### GitHub Actions Workflow
```yaml
name: CI/CD Pipeline

on:
  push:
    branches: [main, develop]
  pull_request:
    branches: [main]

jobs:
  test:
    runs-on: ubuntu-latest
    
    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_ROOT_PASSWORD: password
          MYSQL_DATABASE: mewayz_test
        ports:
          - 3306:3306
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.2'
        extensions: mbstring, xml, ctype, json, openssl, pdo, mysql
    
    - name: Install Dependencies
      run: composer install --prefer-dist --no-progress
    
    - name: Generate Application Key
      run: php artisan key:generate
    
    - name: Run Database Migrations
      run: php artisan migrate --force
    
    - name: Run Tests
      run: php artisan test --coverage
    
    - name: Upload Coverage
      uses: codecov/codecov-action@v1

  deploy:
    runs-on: ubuntu-latest
    needs: test
    if: github.ref == 'refs/heads/main'
    
    steps:
    - name: Deploy to Production
      run: |
        echo "Deploying to production..."
        # Add deployment commands here
```

---

**Ready to contribute?** Check out our **[Contributing Guidelines](../contributing/README.md)** and **[Code Style Guide](code-style.md)**.