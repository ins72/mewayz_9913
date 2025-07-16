# Mewayz Platform - Development Guide

This comprehensive guide covers everything you need to know for developing on the Mewayz platform, from local setup to deployment.

## 🚀 Getting Started

### Prerequisites

#### System Requirements
- **PHP**: 8.2 or higher
- **Node.js**: 18.0 or higher
- **Composer**: 2.0 or higher
- **Git**: 2.0 or higher
- **Database**: MariaDB 10.6+ or MySQL 8.0+

#### Development Tools
- **IDE**: VS Code, PHPStorm, or similar
- **Browser**: Chrome/Firefox with dev tools
- **Terminal**: Command line interface
- **Postman**: API testing (optional)

### Local Development Setup

#### 1. Clone Repository
```bash
git clone https://github.com/your-org/mewayz.git
cd mewayz
```

#### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

#### 3. Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database (edit .env file)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mewayz
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Configure Stripe (for payment testing)
STRIPE_KEY=pk_test_your_publishable_key
STRIPE_SECRET=sk_test_your_secret_key
STRIPE_API_KEY=sk_test_your_secret_key
```

#### 4. Database Setup
```bash
# Create database
mysql -u root -p -e "CREATE DATABASE mewayz CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations
php artisan migrate

# Seed database with sample data
php artisan db:seed
```

#### 5. Build Assets
```bash
# For development (with hot reload)
npm run dev

# For production build
npm run build
```

#### 6. Start Development Server
```bash
# Start Laravel development server
php artisan serve

# The application will be available at http://localhost:8000
```

### Development Environment Tools

#### VS Code Extensions
```json
{
  "recommendations": [
    "bmewburn.vscode-intelephense-client",
    "onecentlin.laravel-blade",
    "ryannaddy.laravel-artisan",
    "bradlc.vscode-tailwindcss",
    "formulahendry.auto-rename-tag",
    "esbenp.prettier-vscode"
  ]
}
```

#### VS Code Settings
```json
{
  "php.validate.executablePath": "/usr/bin/php",
  "emmet.includeLanguages": {
    "blade": "html"
  },
  "files.associations": {
    "*.blade.php": "blade"
  }
}
```

## 🏗 Project Architecture

### Directory Structure
```
/app/
├── app/                      # Laravel application
│   ├── Http/                 # HTTP layer
│   │   ├── Controllers/      # Controllers
│   │   ├── Middleware/       # Custom middleware
│   │   └── Requests/         # Form requests
│   ├── Models/               # Eloquent models
│   ├── Services/             # Business logic
│   └── Providers/            # Service providers
├── database/                 # Database files
├── resources/                # Frontend resources
│   ├── views/                # Blade templates
│   ├── css/                  # Stylesheets
│   └── js/                   # JavaScript
├── routes/                   # Route definitions
├── public/                   # Public assets
└── storage/                  # Storage files
```

### Key Components

#### Models
```php
// app/Models/User.php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    
    protected $fillable = [
        'name',
        'email',
        'password',
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
```

#### Services
```php
// app/Services/StripeService.php
<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }
    
    public function createCheckoutSession(array $params): Session
    {
        return Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'unit_amount' => $params['amount'],
                    'product_data' => [
                        'name' => $params['name'],
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $params['success_url'],
            'cancel_url' => $params['cancel_url'],
        ]);
    }
}
```

#### Controllers
```php
// app/Http/Controllers/Api/StripePaymentController.php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCheckoutSessionRequest;
use App\Services\StripeService;
use Illuminate\Http\JsonResponse;

class StripePaymentController extends Controller
{
    public function __construct(
        private StripeService $stripeService
    ) {}
    
    public function createCheckoutSession(CreateCheckoutSessionRequest $request): JsonResponse
    {
        try {
            $session = $this->stripeService->createCheckoutSession($request->validated());
            
            return response()->json([
                'success' => true,
                'session_id' => $session->id,
                'url' => $session->url,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
```

## 🎨 Frontend Development

### Blade Templates

#### Layout Structure
```html
<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $title ?? 'Mewayz' }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div id="app">
        {{ $slot }}
    </div>
</body>
</html>
```

#### Component Example
```html
<!-- resources/views/components/payment-card.blade.php -->
@props(['package' => null])

<div class="payment-card">
    <div class="payment-card__header">
        <h3 class="payment-card__title">{{ $package['name'] }}</h3>
        <div class="payment-card__price">${{ $package['amount'] }}</div>
    </div>
    
    <div class="payment-card__features">
        @foreach($package['features'] as $feature)
            <div class="payment-card__feature">
                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ $feature }}
            </div>
        @endforeach
    </div>
    
    <button class="payment-card__button" onclick="selectPackage('{{ $package['id'] }}')">
        Select Package
    </button>
</div>
```

### CSS/SCSS Development

#### Design System
```scss
// resources/sass/_variables.scss
:root {
    // Colors
    --app-bg: #101010;
    --card-bg: #191919;
    --border-color: #333333;
    --primary-text: #F1F1F1;
    --secondary-text: #CCCCCC;
    --accent-text: #999999;
    
    // Primary colors
    --primary: #007bff;
    --primary-hover: #0056b3;
    --primary-active: #003d7a;
    
    // Status colors
    --success: #28a745;
    --warning: #ffc107;
    --error: #dc3545;
    --info: #17a2b8;
    
    // Spacing
    --spacing-xs: 0.25rem;
    --spacing-sm: 0.5rem;
    --spacing-md: 1rem;
    --spacing-lg: 1.5rem;
    --spacing-xl: 2rem;
    
    // Typography
    --font-family: 'Figtree', sans-serif;
    --font-size-sm: 0.875rem;
    --font-size-base: 1rem;
    --font-size-lg: 1.125rem;
    --font-size-xl: 1.25rem;
    --font-size-2xl: 1.5rem;
}
```

#### Component Styles
```scss
// resources/sass/components/_payment-card.scss
.payment-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 0.5rem;
    padding: 1.5rem;
    transition: all 0.3s ease;
    
    &:hover {
        border-color: var(--primary);
        transform: translateY(-2px);
    }
    
    &__header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    
    &__title {
        font-size: var(--font-size-xl);
        font-weight: 600;
        color: var(--primary-text);
    }
    
    &__price {
        font-size: var(--font-size-2xl);
        font-weight: 700;
        color: var(--primary);
    }
    
    &__features {
        margin-bottom: 1.5rem;
    }
    
    &__feature {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
        color: var(--secondary-text);
        font-size: var(--font-size-sm);
    }
    
    &__button {
        width: 100%;
        padding: 0.75rem 1.5rem;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 0.375rem;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.2s;
        
        &:hover {
            background: var(--primary-hover);
        }
        
        &:disabled {
            background: #666666;
            cursor: not-allowed;
        }
    }
}
```

### JavaScript Development

#### Alpine.js Components
```javascript
// resources/js/components/payment.js
document.addEventListener('alpine:init', () => {
    Alpine.data('paymentHandler', () => ({
        selectedPackage: null,
        loading: false,
        
        selectPackage(packageId) {
            this.selectedPackage = packageId;
        },
        
        async createPayment() {
            if (!this.selectedPackage) {
                alert('Please select a package first');
                return;
            }
            
            this.loading = true;
            
            try {
                const response = await fetch('/api/payments/checkout/session', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        package_id: this.selectedPackage,
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
                alert('Payment failed: ' + error.message);
            } finally {
                this.loading = false;
            }
        }
    }));
});
```

#### Event Handling
```javascript
// resources/js/app.js
import './bootstrap';
import './components/payment';

// Global error handler
window.addEventListener('error', (event) => {
    console.error('Global error:', event.error);
    // Send to error reporting service
});

// CSRF token setup
window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Axios configuration
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = window.csrfToken;
```

## 🗃️ Database Development

### Migration Best Practices

#### Creating Migrations
```bash
# Create a new migration
php artisan make:migration create_payment_transactions_table

# Create migration with model
php artisan make:model PaymentTransaction -m

# Create migration for existing table
php artisan make:migration add_stripe_session_id_to_payment_transactions_table
```

#### Migration Example
```php
// database/migrations/2024_01_01_000000_create_payment_transactions_table.php
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
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled']);
            $table->json('metadata')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index('stripe_session_id');
            $table->index('created_at');
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
```

### Model Relationships

#### Defining Relationships
```php
// app/Models/User.php
class User extends Authenticatable
{
    public function sites()
    {
        return $this->hasMany(Site::class);
    }
    
    public function paymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }
    
    public function activeSubscription()
    {
        return $this->hasOne(PaymentTransaction::class)
            ->where('status', 'completed')
            ->latest();
    }
}

// app/Models/Site.php
class Site extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function analytics()
    {
        return $this->hasMany(Analytics::class);
    }
}
```

### Database Seeding

#### Seeder Example
```php
// database/seeders/PaymentPackageSeeder.php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentPackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'id' => 'starter',
                'name' => 'Starter Package',
                'amount' => 999, // $9.99 in cents
                'features' => json_encode([
                    'Up to 5 sites',
                    'Basic analytics',
                    'Email support',
                    '1GB storage',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'professional',
                'name' => 'Professional Package',
                'amount' => 2999, // $29.99 in cents
                'features' => json_encode([
                    'Up to 25 sites',
                    'Advanced analytics',
                    'Priority support',
                    '10GB storage',
                    'Custom domains',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 'enterprise',
                'name' => 'Enterprise Package',
                'amount' => 9999, // $99.99 in cents
                'features' => json_encode([
                    'Unlimited sites',
                    'Enterprise analytics',
                    '24/7 phone support',
                    '100GB storage',
                    'White-label solution',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        DB::table('payment_packages')->insert($packages);
    }
}
```

## 🧪 Testing

### Unit Tests

#### Model Testing
```php
// tests/Unit/Models/UserTest.php
<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\Site;
use App\Models\PaymentTransaction;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_user_can_have_multiple_sites(): void
    {
        $user = User::factory()->create();
        $sites = Site::factory()->count(3)->create(['user_id' => $user->id]);
        
        $this->assertCount(3, $user->sites);
        $this->assertInstanceOf(Site::class, $user->sites->first());
    }
    
    public function test_user_can_have_payment_transactions(): void
    {
        $user = User::factory()->create();
        $transaction = PaymentTransaction::factory()->create(['user_id' => $user->id]);
        
        $this->assertCount(1, $user->paymentTransactions);
        $this->assertEquals($transaction->id, $user->paymentTransactions->first()->id);
    }
}
```

### Feature Tests

#### API Testing
```php
// tests/Feature/PaymentTest.php
<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\PaymentTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_authenticated_user_can_create_checkout_session(): void
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
    
    public function test_guest_cannot_create_checkout_session(): void
    {
        $response = $this->postJson('/api/payments/checkout/session', [
            'package_id' => 'starter',
            'success_url' => 'https://example.com/success',
            'cancel_url' => 'https://example.com/cancel',
        ]);
        
        $response->assertStatus(401);
    }
}
```

### Browser Tests

#### Laravel Dusk
```php
// tests/Browser/PaymentTest.php
<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PaymentTest extends DuskTestCase
{
    public function test_user_can_complete_payment_flow(): void
    {
        $user = User::factory()->create();
        
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/dashboard/upgrade')
                ->click('[data-package="starter"]')
                ->waitFor('.payment-modal')
                ->assertSee('Processing payment...')
                ->waitUntilMissing('.payment-modal', 30)
                ->assertPathIs('/dashboard/success');
        });
    }
}
```

## 🚀 Deployment

### Environment Configuration

#### Production Environment
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=your-database-host
DB_DATABASE=your-database-name
DB_USERNAME=your-database-user
DB_PASSWORD=your-secure-password

# Cache
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Mail
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls

# Stripe
STRIPE_KEY=pk_live_your_live_publishable_key
STRIPE_SECRET=sk_live_your_live_secret_key
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret
```

### Build Process

#### Production Build
```bash
# Install dependencies
composer install --no-dev --optimize-autoloader

# Build assets
npm run build

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Clear caches
php artisan cache:clear
```

### Docker Deployment

#### Dockerfile
```dockerfile
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create application directory
WORKDIR /var/www

# Copy application
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Expose port
EXPOSE 9000

CMD ["php-fpm"]
```

#### Docker Compose
```yaml
version: '3.8'

services:
  app:
    build: .
    container_name: mewayz-app
    depends_on:
      - db
      - redis
    environment:
      - APP_ENV=production
      - DB_HOST=db
      - REDIS_HOST=redis
    volumes:
      - ./storage:/var/www/storage
      - ./bootstrap/cache:/var/www/bootstrap/cache

  webserver:
    image: nginx:alpine
    container_name: mewayz-webserver
    depends_on:
      - app
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./public:/var/www/public
      - ./docker/nginx/conf.d:/etc/nginx/conf.d

  db:
    image: mariadb:10.6
    container_name: mewayz-db
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: mewayz
      MYSQL_USER: mewayz
      MYSQL_PASSWORD: password
    volumes:
      - db_data:/var/lib/mysql

  redis:
    image: redis:alpine
    container_name: mewayz-redis

volumes:
  db_data:
```

## 🔧 Development Tools

### Artisan Commands

#### Custom Commands
```php
// app/Console/Commands/ProcessPayments.php
<?php

namespace App\Console\Commands;

use App\Models\PaymentTransaction;
use Illuminate\Console\Command;

class ProcessPayments extends Command
{
    protected $signature = 'payments:process';
    protected $description = 'Process pending payments';
    
    public function handle(): void
    {
        $pendingPayments = PaymentTransaction::where('status', 'pending')
            ->where('created_at', '<', now()->subMinutes(5))
            ->get();
        
        $this->info("Found {$pendingPayments->count()} pending payments");
        
        foreach ($pendingPayments as $payment) {
            $this->processPayment($payment);
        }
        
        $this->info('Payment processing completed');
    }
    
    private function processPayment(PaymentTransaction $payment): void
    {
        // Process payment logic
        $this->line("Processing payment {$payment->id}");
    }
}
```

### Queue Jobs

#### Job Example
```php
// app/Jobs/ProcessPaymentWebhook.php
<?php

namespace App\Jobs;

use App\Models\PaymentTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPaymentWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public function __construct(
        private array $webhookData
    ) {}
    
    public function handle(): void
    {
        $sessionId = $this->webhookData['data']['object']['id'];
        
        $transaction = PaymentTransaction::where('stripe_session_id', $sessionId)
            ->firstOrFail();
        
        $transaction->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
        
        // Send confirmation email, update user subscription, etc.
    }
}
```

## 📊 Performance Optimization

### Database Optimization

#### Query Optimization
```php
// Inefficient (N+1 problem)
$users = User::all();
foreach ($users as $user) {
    echo $user->sites->count(); // This runs a query for each user
}

// Efficient (eager loading)
$users = User::withCount('sites')->get();
foreach ($users as $user) {
    echo $user->sites_count; // No additional queries
}
```

#### Caching Strategies
```php
// Service-level caching
class DashboardService
{
    public function getUserStats(User $user): array
    {
        return Cache::remember("user_stats_{$user->id}", 3600, function () use ($user) {
            return [
                'total_sites' => $user->sites()->count(),
                'total_visits' => $user->sites()->sum('visits'),
                'total_revenue' => $user->paymentTransactions()
                    ->where('status', 'completed')
                    ->sum('amount'),
            ];
        });
    }
}
```

### Frontend Optimization

#### Asset Optimization
```javascript
// Vite configuration
export default defineConfig({
    plugins: [laravel(['resources/css/app.css', 'resources/js/app.js'])],
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['alpinejs'],
                    dashboard: ['./resources/js/dashboard.js'],
                },
            },
        },
        minify: 'terser',
    },
});
```

## 🐛 Debugging

### Laravel Debugging

#### Debug Tools
```php
// Enable debug mode
APP_DEBUG=true

// Use debug functions
dd($variable); // Dump and die
dump($variable); // Dump variable

// Query debugging
DB::enableQueryLog();
// Your queries here
dd(DB::getQueryLog());

// Log debugging
Log::debug('Debug message', ['data' => $data]);
```

#### Error Handling
```php
// Custom error handler
class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ValidationException) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $exception->errors(),
            ], 422);
        }
        
        return parent::render($request, $exception);
    }
}
```

## 📝 Code Quality

### Static Analysis

#### PHPStan Configuration
```neon
# phpstan.neon
parameters:
    level: 5
    paths:
        - app
    ignoreErrors:
        - '#Call to an undefined method#'
```

#### Coding Standards
```xml
<!-- phpcs.xml -->
<?xml version="1.0"?>
<ruleset name="Mewayz">
    <file>app</file>