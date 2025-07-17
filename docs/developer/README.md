# Mewayz Platform v2 - Developer Guide

*Last Updated: July 17, 2025*

## Overview

This guide provides comprehensive technical documentation for developers working with the Mewayz Platform v2. The platform is built on Laravel 11 + MySQL with modern development practices and enterprise-grade architecture.

## Architecture Overview

### Technology Stack
- **Backend Framework**: Laravel 11 with PHP 8.2+
- **Database**: MySQL 8.0+ (MariaDB compatible)
- **Frontend**: Laravel Blade templates with Vite.js
- **Styling**: Tailwind CSS with SASS preprocessing
- **JavaScript**: Modern ES6+ with module system
- **Process Management**: Supervisor for service orchestration
- **Authentication**: Laravel Sanctum with custom middleware
- **API Design**: RESTful architecture with JSON responses

### Directory Structure
```
/app/
├── app/                    # Laravel application core
│   ├── Http/
│   │   ├── Controllers/    # API and web controllers
│   │   │   └── Api/        # API-specific controllers
│   │   ├── Middleware/     # Custom middleware
│   │   └── Requests/       # Form request validation
│   ├── Models/            # Eloquent models
│   ├── Services/          # Business logic services
│   └── Providers/         # Service providers
├── database/
│   ├── migrations/        # Database schema migrations
│   ├── seeders/          # Database seeders
│   └── factories/        # Model factories
├── resources/
│   ├── views/            # Blade templates
│   ├── css/              # Stylesheets
│   ├── js/               # JavaScript files
│   └── sass/             # SASS files
├── routes/
│   ├── web.php           # Web routes
│   ├── api.php           # Main API routes
│   └── api_phase*.php    # Phase-specific API routes
├── public/               # Public assets
├── storage/              # Application storage
└── tests/                # Test files
```

## Development Environment Setup

### Prerequisites
- PHP 8.2+ with required extensions
- Composer for PHP dependency management
- MySQL 8.0+ or MariaDB 10.5+
- Node.js 18+ with NPM
- Git for version control

### Installation Steps

1. **Clone and Setup**
   ```bash
   git clone [repository-url]
   cd mewayz-platform
   
   # Install PHP dependencies
   composer install
   
   # Install Node.js dependencies
   npm install
   ```

2. **Environment Configuration**
   ```bash
   # Copy environment file
   cp .env.example .env
   
   # Generate application key
   php artisan key:generate
   
   # Configure database in .env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=mewayz
   DB_USERNAME=root
   DB_PASSWORD=
   ```

3. **Database Setup**
   ```bash
   # Create database
   mysql -u root -p -e "CREATE DATABASE mewayz;"
   
   # Run migrations
   php artisan migrate
   
   # Seed database (optional)
   php artisan db:seed
   ```

4. **Asset Compilation**
   ```bash
   # Development build
   npm run dev
   
   # Production build
   npm run build
   ```

5. **Start Services**
   ```bash
   # Start Laravel development server
   php artisan serve --host=0.0.0.0 --port=8001
   
   # Start queue worker
   php artisan queue:work
   ```

## API Development

### Controller Structure

Controllers are organized by functionality and phase:

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExampleController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data = ExampleModel::where('user_id', $request->user()->id)
                ->paginate(20);
            
            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Data retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve data'
            ], 500);
        }
    }
}
```

### Authentication Middleware

The platform uses custom Sanctum authentication:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class CustomSanctumAuth
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
        
        $accessToken = PersonalAccessToken::findToken($token);
        
        if (!$accessToken) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token'
            ], 401);
        }
        
        $request->setUserResolver(function () use ($accessToken) {
            return $accessToken->tokenable;
        });
        
        return $next($request);
    }
}
```

### Database Models

Models follow Eloquent conventions with relationships:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExampleModel extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'description',
        'user_id',
        'status'
    ];
    
    protected $casts = [
        'settings' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function relatedModels()
    {
        return $this->hasMany(RelatedModel::class);
    }
}
```

### API Routes

Routes are organized by phases for maintainability:

```php
<?php
// routes/api_phase1.php

use App\Http\Controllers\Api\OnboardingController;
use App\Http\Controllers\Api\ThemeController;

Route::middleware([\App\Http\Middleware\CustomSanctumAuth::class])
    ->group(function () {
        
    // Onboarding routes
    Route::prefix('onboarding')->group(function () {
        Route::get('/progress', [OnboardingController::class, 'getProgress']);
        Route::post('/progress', [OnboardingController::class, 'updateProgress']);
        Route::get('/recommendations', [OnboardingController::class, 'getRecommendations']);
    });
    
    // Theme routes
    Route::prefix('theme')->group(function () {
        Route::get('/', [ThemeController::class, 'getCurrentTheme']);
        Route::post('/update', [ThemeController::class, 'updateTheme']);
        Route::get('/system', [ThemeController::class, 'getSystemTheme']);
    });
});
```

## Database Development

### Migration Guidelines

Create migrations with proper foreign key relationships:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('example_table', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('settings')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('cascade');
            $table->index(['user_id', 'status']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('example_table');
    }
};
```

### Seeder Development

Create seeders for testing and development:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ExampleModel;

class ExampleSeeder extends Seeder
{
    public function run()
    {
        $users = User::factory(10)->create();
        
        foreach ($users as $user) {
            ExampleModel::factory(5)->create([
                'user_id' => $user->id
            ]);
        }
    }
}
```

## Frontend Development

### Blade Templates

Use Blade templates with Tailwind CSS:

```blade
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Content cards -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">{{ __('Dashboard') }}</h2>
            <div class="space-y-4">
                <!-- Dashboard content -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Page-specific JavaScript
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize dashboard
    });
</script>
@endpush
```

### JavaScript Development

Use modern JavaScript with modules:

```javascript
// resources/js/dashboard.js
import { ApiClient } from './modules/api-client.js';

class Dashboard {
    constructor() {
        this.apiClient = new ApiClient();
        this.init();
    }
    
    init() {
        this.loadDashboardData();
        this.bindEvents();
    }
    
    async loadDashboardData() {
        try {
            const response = await this.apiClient.get('/api/dashboard/data');
            this.renderDashboard(response.data);
        } catch (error) {
            console.error('Failed to load dashboard data:', error);
        }
    }
    
    bindEvents() {
        document.addEventListener('click', this.handleClick.bind(this));
    }
    
    handleClick(event) {
        // Handle dashboard interactions
    }
    
    renderDashboard(data) {
        // Render dashboard content
    }
}

// Initialize dashboard
new Dashboard();
```

## Testing

### Backend Testing

Create feature tests for API endpoints:

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleApiTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_can_get_user_data()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->getJson('/api/example');
        
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data',
                    'message'
                ]);
    }
}
```

### Frontend Testing

Use Laravel Dusk for browser testing:

```php
<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DashboardTest extends DuskTestCase
{
    public function test_dashboard_loads_correctly()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/dashboard')
                    ->assertSee('Dashboard')
                    ->assertVisible('.dashboard-container');
        });
    }
}
```

## Performance Optimization

### Database Optimization

```php
// Use eager loading to prevent N+1 queries
$users = User::with(['profile', 'subscriptions'])->get();

// Use database transactions for multiple operations
DB::transaction(function () {
    $user = User::create($userData);
    $profile = Profile::create($profileData);
    $subscription = Subscription::create($subscriptionData);
});

// Use query optimization
$users = User::select(['id', 'name', 'email'])
    ->where('active', true)
    ->orderBy('created_at', 'desc')
    ->paginate(20);
```

### Caching Strategy

```php
// Cache expensive queries
$users = Cache::remember('active_users', 3600, function () {
    return User::where('active', true)->get();
});

// Cache API responses
public function getStats(Request $request)
{
    $cacheKey = 'user_stats_' . $request->user()->id;
    
    $stats = Cache::remember($cacheKey, 1800, function () use ($request) {
        return $this->calculateUserStats($request->user());
    });
    
    return response()->json(['data' => $stats]);
}
```

## Security Best Practices

### Input Validation

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateExampleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'settings' => 'array',
            'settings.theme' => 'string|in:light,dark',
        ];
    }
}
```

### SQL Injection Prevention

```php
// Use Eloquent ORM or query builder
$users = User::where('email', $email)->first();

// Use parameter binding for raw queries
$results = DB::select('SELECT * FROM users WHERE email = ?', [$email]);

// Never concatenate user input
// DON'T DO THIS:
// $query = "SELECT * FROM users WHERE email = '$email'";
```

## Deployment

### Environment Configuration

```bash
# Production environment variables
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database configuration
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=mewayz_production
DB_USERNAME=mewayz_user
DB_PASSWORD=secure_password

# Cache configuration
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### Optimization Commands

```bash
# Optimize for production
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Build production assets
npm run build
```

## Code Standards

### PHP Standards

Follow PSR-12 coding standards:

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ExampleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $data = $this->getExampleData($request);
        
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => 'Data retrieved successfully',
        ]);
    }
    
    private function getExampleData(Request $request): array
    {
        // Implementation
        return [];
    }
}
```

### JavaScript Standards

Use ES6+ features and consistent formatting:

```javascript
// Use const/let instead of var
const apiClient = new ApiClient();
let currentUser = null;

// Use arrow functions
const processData = (data) => {
    return data.map(item => ({
        id: item.id,
        name: item.name,
        processed: true
    }));
};

// Use async/await
const fetchUserData = async (userId) => {
    try {
        const response = await apiClient.get(`/api/users/${userId}`);
        return response.data;
    } catch (error) {
        console.error('Failed to fetch user data:', error);
        throw error;
    }
};
```

## Version Control

### Git Workflow

```bash
# Feature development
git checkout -b feature/new-feature
git add .
git commit -m "feat: add new feature"
git push origin feature/new-feature

# Create pull request
# After review and approval, merge to main
```

### Commit Message Format

```
type(scope): description

feat: add new feature
fix: resolve bug in authentication
docs: update API documentation
style: improve code formatting
refactor: restructure user service
test: add unit tests for user model
chore: update dependencies
```

## Monitoring and Logging

### Application Logging

```php
use Illuminate\Support\Facades\Log;

// Log different levels
Log::info('User logged in', ['user_id' => $user->id]);
Log::warning('High memory usage detected');
Log::error('Failed to process payment', ['error' => $exception->getMessage()]);

// Custom log channels
Log::channel('api')->info('API request', [
    'endpoint' => $request->path(),
    'method' => $request->method(),
    'user_id' => $request->user()->id ?? null
]);
```

### Error Handling

```php
<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'code' => $exception->getCode()
            ], 500);
        }
        
        return parent::render($request, $exception);
    }
}
```

This developer guide provides comprehensive information for working with the Mewayz Platform v2. For specific implementation details, refer to the codebase and additional documentation in the `docs` folder.