# Mewayz Platform v2 - Developer Guide

*Last Updated: January 17, 2025*

## 👨‍💻 **DEVELOPER OVERVIEW**

Welcome to the **Mewayz Platform v2** developer documentation. This guide covers technical implementation, API integration, and development workflows for our **Laravel 11 + MySQL** platform.

---

## 🏗️ **TECHNICAL ARCHITECTURE**

### Backend Stack
- **Framework**: Laravel 11 with PHP 8.2+
- **Database**: MySQL 8.0+ with 85+ optimized tables
- **Authentication**: CustomSanctumAuth middleware
- **API Design**: 150+ RESTful endpoints across 40+ controllers
- **Caching**: Redis for session and query caching
- **File Storage**: AWS S3 integration with CDN
- **Queue System**: Laravel Queues for background processing

### Frontend Stack
- **Template Engine**: Laravel Blade with modern JavaScript
- **Build Tool**: Vite for asset compilation and optimization
- **Styling**: Tailwind CSS with custom dark theme
- **JavaScript**: Alpine.js for interactive components
- **PWA Features**: Service Worker and Web App Manifest
- **Mobile-First**: Responsive design optimized for mobile devices

### Database Schema
- **Primary Database**: MySQL with 85+ optimized tables
- **UUID Primary Keys**: Enhanced security and scalability
- **Proper Relationships**: Foreign key constraints and indexes
- **Migrations**: Laravel migrations for version control
- **Seeders**: Database seeders for initial data

---

## 🚀 **DEVELOPMENT SETUP**

### Prerequisites
- **PHP**: 8.2 or higher
- **Composer**: 2.0 or higher
- **Node.js**: 18.0 or higher
- **MySQL**: 8.0 or higher
- **Redis**: 6.0 or higher

### Local Development Environment
```bash
# Clone the repository
git clone https://github.com/mewayz/platform.git
cd platform

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Create database
mysql -u root -p -e "CREATE DATABASE mewayz_v2;"

# Run migrations
php artisan migrate

# Seed database (optional)
php artisan db:seed

# Compile assets
npm run dev

# Start development server
php artisan serve
```

### Development Tools
- **IDE**: PhpStorm, VS Code, or Sublime Text
- **Database**: MySQL Workbench, phpMyAdmin, or TablePlus
- **API Testing**: Postman, Insomnia, or curl
- **Version Control**: Git with GitHub/GitLab
- **Task Runner**: Laravel Sail (Docker environment)

---

## 📡 **API DEVELOPMENT**

### API Structure
```
/api/v2/
├── auth/              # Authentication endpoints
├── workspaces/        # Workspace management
├── social-media/      # Social media features
├── bio-sites/         # Link in bio functionality
├── ecommerce/         # E-commerce features
├── crm/               # CRM functionality
├── email-marketing/   # Email campaigns
├── courses/           # Course management
├── analytics/         # Analytics and reporting
├── escrow/            # Escrow transactions
├── ai/                # AI features
└── admin/             # Admin functionality
```

### API Authentication
```php
// CustomSanctumAuth Middleware
class CustomSanctumAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized'
            ], 401);
        }
        
        return $next($request);
    }
}
```

### API Response Format
```php
// Standard Success Response
return response()->json([
    'success' => true,
    'data' => $data,
    'message' => 'Operation successful',
    'meta' => [
        'current_page' => 1,
        'per_page' => 20,
        'total' => 100
    ]
]);

// Standard Error Response
return response()->json([
    'success' => false,
    'error' => [
        'code' => 'VALIDATION_ERROR',
        'message' => 'The given data was invalid.',
        'details' => $validator->errors()
    ]
], 422);
```

---

## 🗄️ **DATABASE DEVELOPMENT**

### Database Schema Design
```sql
-- Core Tables
users (id UUID, name, email, password, created_at, updated_at)
workspaces (id UUID, name, user_id, settings, created_at, updated_at)
workspace_users (id UUID, workspace_id, user_id, role, created_at, updated_at)

-- Social Media Tables
social_media_accounts (id UUID, workspace_id, platform, access_token, created_at, updated_at)
social_media_posts (id UUID, workspace_id, content, published_at, created_at, updated_at)
instagram_profiles (id UUID, workspace_id, username, followers_count, created_at, updated_at)

-- E-commerce Tables
products (id UUID, workspace_id, name, price, stock, created_at, updated_at)
orders (id UUID, workspace_id, user_id, total_amount, status, created_at, updated_at)
order_items (id UUID, order_id, product_id, quantity, price, created_at, updated_at)
```

### Model Relationships
```php
// User Model
class User extends Authenticatable
{
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }
    
    public function workspaces()
    {
        return $this->hasMany(Workspace::class);
    }
    
    public function workspaceUsers()
    {
        return $this->hasMany(WorkspaceUser::class);
    }
}

// Workspace Model
class Workspace extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'name', 'slug', 'description', 'user_id', 'settings'
    ];
    
    protected $casts = [
        'settings' => 'json'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function users()
    {
        return $this->belongsToMany(User::class, 'workspace_users');
    }
}
```

### Migration Best Practices
```php
// Create Migration
php artisan make:migration create_workspaces_table

// Migration Structure
public function up()
{
    Schema::create('workspaces', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->string('name');
        $table->string('slug')->unique();
        $table->text('description')->nullable();
        $table->uuid('user_id');
        $table->json('settings')->nullable();
        $table->timestamps();
        
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        $table->index(['user_id', 'created_at']);
    });
}
```

---

## 🎨 **FRONTEND DEVELOPMENT**

### Blade Templates
```blade
<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Mewayz') }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900 text-white">
    <div id="app">
        @yield('content')
    </div>
    
    @yield('scripts')
</body>
</html>
```

### Alpine.js Components
```javascript
// resources/js/components/workspace-selector.js
Alpine.data('workspaceSelector', () => ({
    workspaces: [],
    currentWorkspace: null,
    loading: false,
    
    async init() {
        await this.loadWorkspaces();
    },
    
    async loadWorkspaces() {
        this.loading = true;
        try {
            const response = await fetch('/api/workspaces', {
                headers: {
                    'Authorization': `Bearer ${this.token}`,
                    'Content-Type': 'application/json'
                }
            });
            this.workspaces = await response.json();
        } catch (error) {
            console.error('Error loading workspaces:', error);
        } finally {
            this.loading = false;
        }
    },
    
    selectWorkspace(workspace) {
        this.currentWorkspace = workspace;
        localStorage.setItem('currentWorkspace', JSON.stringify(workspace));
        window.location.reload();
    }
}));
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
                'app-bg': '#101010',
                'card-bg': '#191919',
                'primary-text': '#F1F1F1',
                'secondary-text': '#7B7B7B',
            }
        }
    },
    plugins: []
}
```

---

## 🔧 **BACKEND DEVELOPMENT**

### Controller Structure
```php
// app/Http/Controllers/Api/WorkspaceController.php
class WorkspaceController extends Controller
{
    public function index(Request $request)
    {
        $workspaces = $request->user()->workspaces()
            ->with('users')
            ->paginate(20);
            
        return response()->json([
            'success' => true,
            'data' => $workspaces,
            'message' => 'Workspaces retrieved successfully'
        ]);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);
        
        $workspace = $request->user()->workspaces()->create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'settings' => [
                'theme' => 'dark',
                'features' => []
            ]
        ]);
        
        return response()->json([
            'success' => true,
            'data' => $workspace,
            'message' => 'Workspace created successfully'
        ], 201);
    }
}
```

### Service Layer
```php
// app/Services/WorkspaceService.php
class WorkspaceService
{
    public function createWorkspace(User $user, array $data): Workspace
    {
        DB::beginTransaction();
        
        try {
            $workspace = $user->workspaces()->create([
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'description' => $data['description'] ?? null,
                'settings' => $this->getDefaultSettings($data)
            ]);
            
            // Create default workspace user record
            $workspace->users()->attach($user->id, [
                'role' => 'owner',
                'permissions' => ['*']
            ]);
            
            DB::commit();
            return $workspace;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    private function getDefaultSettings(array $data): array
    {
        return [
            'theme' => 'dark',
            'features' => $data['features'] ?? [],
            'branding' => [
                'logo' => null,
                'colors' => [
                    'primary' => '#007AFF',
                    'secondary' => '#191919'
                ]
            ]
        ];
    }
}
```

---

## 🧪 **TESTING**

### Unit Testing
```php
// tests/Unit/WorkspaceServiceTest.php
class WorkspaceServiceTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_can_create_workspace()
    {
        $user = User::factory()->create();
        $service = new WorkspaceService();
        
        $workspace = $service->createWorkspace($user, [
            'name' => 'Test Workspace',
            'description' => 'Test description'
        ]);
        
        $this->assertDatabaseHas('workspaces', [
            'name' => 'Test Workspace',
            'user_id' => $user->id
        ]);
        
        $this->assertDatabaseHas('workspace_users', [
            'workspace_id' => $workspace->id,
            'user_id' => $user->id,
            'role' => 'owner'
        ]);
    }
}
```

### Feature Testing
```php
// tests/Feature/WorkspaceApiTest.php
class WorkspaceApiTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_can_list_workspaces()
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory()->create(['user_id' => $user->id]);
        
        $response = $this->actingAs($user)
            ->getJson('/api/workspaces');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'data' => [
                        '*' => ['id', 'name', 'slug', 'description']
                    ]
                ]
            ]);
    }
}
```

### Browser Testing
```php
// tests/Browser/WorkspaceTest.php
class WorkspaceTest extends DuskTestCase
{
    public function test_user_can_create_workspace()
    {
        $user = User::factory()->create();
        
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/workspaces/create')
                ->type('name', 'New Workspace')
                ->type('description', 'Workspace description')
                ->press('Create Workspace')
                ->waitForText('Workspace created successfully')
                ->assertSee('New Workspace');
        });
    }
}
```

---

## 🚀 **DEPLOYMENT**

### Environment Configuration
```env
# .env.production
APP_NAME="Mewayz Platform v2"
APP_ENV=production
APP_KEY=base64:your-app-key-here
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=mewayz_v2
DB_USERNAME=mewayz_user
DB_PASSWORD=secure_password

REDIS_HOST=localhost
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls

STRIPE_KEY=pk_live_your_stripe_key
STRIPE_SECRET=sk_live_your_stripe_secret
```

### Deployment Script
```bash
#!/bin/bash
# deploy.sh

# Pull latest code
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader

# Install Node.js dependencies
npm ci

# Build assets
npm run build

# Run migrations
php artisan migrate --force

# Clear and cache config
php artisan config:clear
php artisan config:cache

# Clear and cache routes
php artisan route:clear
php artisan route:cache

# Clear and cache views
php artisan view:clear
php artisan view:cache

# Restart queue workers
php artisan queue:restart

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm

echo "Deployment completed successfully!"
```

---

## 📊 **MONITORING & DEBUGGING**

### Error Tracking
```php
// config/logging.php
'channels' => [
    'stack' => [
        'driver' => 'stack',
        'channels' => ['single', 'slack'],
        'ignore_exceptions' => false,
    ],
    'single' => [
        'driver' => 'single',
        'path' => storage_path('logs/laravel.log'),
        'level' => env('LOG_LEVEL', 'debug'),
    ],
    'slack' => [
        'driver' => 'slack',
        'url' => env('LOG_SLACK_WEBHOOK_URL'),
        'username' => 'Laravel Log',
        'emoji' => ':boom:',
        'level' => env('LOG_LEVEL', 'critical'),
    ],
]
```

### Performance Monitoring
```php
// app/Http/Middleware/PerformanceMonitoring.php
class PerformanceMonitoring
{
    public function handle($request, Closure $next)
    {
        $start = microtime(true);
        
        $response = $next($request);
        
        $duration = microtime(true) - $start;
        
        if ($duration > 1.0) {
            Log::warning('Slow request detected', [
                'url' => $request->url(),
                'method' => $request->method(),
                'duration' => $duration,
                'memory' => memory_get_peak_usage(true)
            ]);
        }
        
        return $response;
    }
}
```

---

## 📚 **BEST PRACTICES**

### Code Standards
- **PSR-12**: Follow PHP coding standards
- **Laravel Conventions**: Use Laravel naming conventions
- **Type Hints**: Use strict type declarations
- **Documentation**: Document all public methods
- **Testing**: Write tests for all new features

### Security Best Practices
- **Input Validation**: Validate all user inputs
- **SQL Injection**: Use Eloquent ORM or prepared statements
- **XSS Protection**: Escape all output
- **CSRF Protection**: Use CSRF tokens
- **Authentication**: Implement proper authentication

### Performance Optimization
- **Database Indexing**: Index frequently queried columns
- **Query Optimization**: Use eager loading and select specific columns
- **Caching**: Implement Redis caching
- **CDN**: Use CDN for static assets
- **Compression**: Enable gzip compression

---

## 🛠️ **DEVELOPMENT TOOLS**

### Recommended Extensions
- **PhpStorm**: Laravel Plugin, Database Tools
- **VS Code**: Laravel Extension Pack, PHP Intelephense
- **Chrome**: Laravel Debugbar, Vue DevTools
- **Postman**: API testing and documentation
- **TablePlus**: Database management

### Debugging Tools
- **Laravel Debugbar**: Web profiler
- **Telescope**: Application debugging
- **Ray**: Debug tool by Spatie
- **Xdebug**: PHP debugger
- **Laravel Tinker**: Interactive shell

---

## 📞 **DEVELOPER SUPPORT**

### Resources
- **Documentation**: https://docs.mewayz.com
- **API Reference**: https://api.mewayz.com/docs
- **GitHub Repository**: https://github.com/mewayz/platform
- **Developer Forum**: https://forum.mewayz.com
- **Status Page**: https://status.mewayz.com

### Getting Help
- **Technical Support**: dev-support@mewayz.com
- **Bug Reports**: bugs@mewayz.com
- **Feature Requests**: features@mewayz.com
- **Security Issues**: security@mewayz.com

---

*Last Updated: January 17, 2025*
*Platform Version: v2.0.0*
*Framework: Laravel 11 + MySQL*
*Status: Production-Ready*