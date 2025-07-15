# 🧪 Mewayz Platform - Testing Guide

## Testing Overview

The Mewayz platform follows a comprehensive testing strategy to ensure reliability, performance, and security. This guide covers all testing protocols, procedures, and best practices for the Laravel-based architecture.

## Testing Architecture

### **Testing Pyramid**
```
┌─────────────────────────────────────────────────────────────────┐
│                      TESTING PYRAMID                           │
├─────────────────────────────────────────────────────────────────┤
│                    End-to-End Tests                             │
│                   (Browser Automation)                          │
├─────────────────────────────────────────────────────────────────┤
│                 Integration Tests                               │
│                (API + Database)                                 │
├─────────────────────────────────────────────────────────────────┤
│                    Unit Tests                                   │
│              (Models, Services, Controllers)                    │
└─────────────────────────────────────────────────────────────────┘
```

### **Testing Stack**
- **Backend Testing**: Laravel's built-in testing framework (PHPUnit)
- **Frontend Testing**: Playwright for browser automation
- **API Testing**: Laravel HTTP Tests with database transactions
- **Database Testing**: SQLite in-memory database for speed
- **Performance Testing**: Load testing with custom metrics

## Environment Setup

### **Testing Environment Requirements**
- **PHP**: 8.2+ with required extensions
- **Laravel**: 10+ with testing dependencies
- **Database**: MariaDB for integration tests, SQLite for unit tests
- **Node.js**: 18+ for frontend asset compilation
- **Playwright**: Latest version for browser automation

### **Environment Configuration**
```bash
# Create testing environment file
cp .env.testing.example .env.testing

# Configure testing database
DB_CONNECTION=sqlite
DB_DATABASE=:memory:

# Set testing-specific variables
APP_ENV=testing
APP_DEBUG=true
CACHE_DRIVER=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync
```

### **Database Setup for Testing**
```bash
# Run migrations for testing
php artisan migrate --env=testing

# Seed test data
php artisan db:seed --env=testing

# Create test admin user
php artisan make:test-user --env=testing
```

## Backend Testing

### **Unit Testing**

#### **Model Tests**
```php
<?php
// tests/Unit/Models/UserTest.php
namespace Tests\Unit\Models;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_have_organizations()
    {
        $user = User::factory()->create();
        $organization = $user->organizations()->create([
            'name' => 'Test Organization',
            'description' => 'Test Description'
        ]);

        $this->assertCount(1, $user->organizations);
        $this->assertEquals('Test Organization', $user->organizations->first()->name);
    }

    public function test_user_password_is_hashed()
    {
        $user = User::factory()->create([
            'password' => 'password123'
        ]);

        $this->assertTrue(Hash::check('password123', $user->password));
    }
}
```

#### **Service Tests**
```php
<?php
// tests/Unit/Services/InstagramServiceTest.php
namespace Tests\Unit\Services;

use App\Services\InstagramService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InstagramServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_hashtag_research_returns_suggestions()
    {
        $service = new InstagramService();
        $suggestions = $service->researchHashtags('travel');

        $this->assertIsArray($suggestions);
        $this->assertNotEmpty($suggestions);
        $this->assertArrayHasKey('hashtag', $suggestions[0]);
        $this->assertArrayHasKey('difficulty_level', $suggestions[0]);
    }
}
```

### **Feature Testing**

#### **Authentication Tests**
```php
<?php
// tests/Feature/AuthenticationTest.php
namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'user',
                        'token'
                    ]
                ]);
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401)
                ->assertJson([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ]);
    }
}
```

#### **API Endpoint Tests**
```php
<?php
// tests/Feature/InstagramManagementTest.php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Organization;
use App\Models\InstagramAccount;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InstagramManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $organization;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->organization = Organization::factory()->create();
        $this->user->organizations()->attach($this->organization);
    }

    public function test_user_can_create_instagram_account()
    {
        $response = $this->actingAs($this->user, 'sanctum')
                        ->postJson('/api/instagram-management/accounts', [
                            'username' => 'testaccount',
                            'access_token' => 'test_token',
                            'profile_data' => [
                                'bio' => 'Test bio',
                                'followers_count' => 100
                            ]
                        ]);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'account' => [
                            'id',
                            'username',
                            'profile_data'
                        ]
                    ]
                ]);

        $this->assertDatabaseHas('instagram_accounts', [
            'username' => 'testaccount',
            'workspace_id' => $this->organization->id
        ]);
    }

    public function test_user_can_get_instagram_accounts()
    {
        InstagramAccount::factory()->count(3)->create([
            'workspace_id' => $this->organization->id
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
                        ->getJson('/api/instagram-management/accounts');

        $response->assertStatus(200)
                ->assertJsonCount(3, 'data.accounts');
    }
}
```

### **Database Testing**

#### **Migration Tests**
```php
<?php
// tests/Unit/Database/MigrationTest.php
namespace Tests\Unit\Database;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

class MigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_instagram_accounts_table_has_correct_columns()
    {
        $this->assertTrue(Schema::hasTable('instagram_accounts'));
        $this->assertTrue(Schema::hasColumn('instagram_accounts', 'id'));
        $this->assertTrue(Schema::hasColumn('instagram_accounts', 'workspace_id'));
        $this->assertTrue(Schema::hasColumn('instagram_accounts', 'username'));
        $this->assertTrue(Schema::hasColumn('instagram_accounts', 'access_token'));
        $this->assertTrue(Schema::hasColumn('instagram_accounts', 'profile_data'));
    }

    public function test_foreign_key_constraints_exist()
    {
        // Test that foreign key constraints are properly set
        $this->assertTrue(Schema::hasTable('instagram_accounts'));
        $this->assertTrue(Schema::hasTable('organizations'));
        
        // This would fail if foreign key constraint doesn't exist
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        DB::table('instagram_accounts')->insert([
            'workspace_id' => 99999, // Non-existent organization
            'username' => 'test',
            'access_token' => 'token',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
```

## Frontend Testing

### **Playwright Setup**
```bash
# Install Playwright
npm install -D @playwright/test

# Install browsers
npx playwright install

# Configure Playwright
npx playwright init
```

### **Browser Automation Tests**
```javascript
// tests/frontend/auth.spec.js
import { test, expect } from '@playwright/test';

test.describe('Authentication', () => {
  test('user can login successfully', async ({ page }) => {
    await page.goto('http://localhost:8001/login');
    
    await page.fill('input[name="email"]', 'admin@example.com');
    await page.fill('input[name="password"]', 'admin123');
    await page.click('button[type="submit"]');
    
    await expect(page).toHaveURL('http://localhost:8001/console');
    await expect(page.locator('h1')).toContainText('Dashboard');
  });

  test('user cannot login with invalid credentials', async ({ page }) => {
    await page.goto('http://localhost:8001/login');
    
    await page.fill('input[name="email"]', 'invalid@example.com');
    await page.fill('input[name="password"]', 'wrongpassword');
    await page.click('button[type="submit"]');
    
    await expect(page.locator('.error-message')).toContainText('Invalid credentials');
  });
});
```

### **Bio Sites Testing**
```javascript
// tests/frontend/bio-sites.spec.js
import { test, expect } from '@playwright/test';

test.describe('Bio Sites', () => {
  test.beforeEach(async ({ page }) => {
    // Login before each test
    await page.goto('http://localhost:8001/login');
    await page.fill('input[name="email"]', 'admin@example.com');
    await page.fill('input[name="password"]', 'admin123');
    await page.click('button[type="submit"]');
  });

  test('user can create new bio site', async ({ page }) => {
    await page.goto('http://localhost:8001/console/bio');
    
    await page.click('button:has-text("Create New Bio Site")');
    await page.fill('input[name="name"]', 'Test Bio Site');
    await page.fill('input[name="subdomain"]', 'testbio');
    await page.selectOption('select[name="theme"]', 'modern');
    await page.fill('textarea[name="bio"]', 'This is a test bio site');
    
    await page.click('button[type="submit"]');
    
    await expect(page.locator('.success-message')).toContainText('Bio site created successfully');
    await expect(page.locator('.bio-site-card')).toContainText('Test Bio Site');
  });

  test('user can add links to bio site', async ({ page }) => {
    await page.goto('http://localhost:8001/console/bio/testbio');
    
    await page.click('button:has-text("Add Link")');
    await page.fill('input[name="title"]', 'My Website');
    await page.fill('input[name="url"]', 'https://example.com');
    await page.fill('textarea[name="description"]', 'Visit my website');
    
    await page.click('button[type="submit"]');
    
    await expect(page.locator('.link-item')).toContainText('My Website');
  });
});
```

### **Instagram Management Testing**
```javascript
// tests/frontend/instagram.spec.js
import { test, expect } from '@playwright/test';

test.describe('Instagram Management', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('http://localhost:8001/login');
    await page.fill('input[name="email"]', 'admin@example.com');
    await page.fill('input[name="password"]', 'admin123');
    await page.click('button[type="submit"]');
  });

  test('user can access Instagram management', async ({ page }) => {
    await page.goto('http://localhost:8001/instagram-management.html');
    
    await expect(page.locator('h1')).toContainText('Instagram Management');
    await expect(page.locator('#accounts-section')).toBeVisible();
    await expect(page.locator('#posts-section')).toBeVisible();
  });

  test('user can create Instagram post', async ({ page }) => {
    await page.goto('http://localhost:8001/instagram-management.html');
    
    await page.click('button:has-text("Create Post")');
    await page.fill('textarea[name="content"]', 'Test post content #test');
    await page.fill('input[name="media_url"]', 'https://example.com/image.jpg');
    
    await page.click('button[type="submit"]');
    
    await expect(page.locator('.success-message')).toContainText('Post created successfully');
  });
});
```

## API Testing

### **Comprehensive API Test Suite**
```php
<?php
// tests/Feature/ComprehensiveApiTest.php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Organization;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ComprehensiveApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    public function test_all_workspace_setup_endpoints()
    {
        $endpoints = [
            'GET /api/workspace-setup/current-step',
            'GET /api/workspace-setup/main-goals',
            'GET /api/workspace-setup/available-features',
            'GET /api/workspace-setup/subscription-plans',
            'POST /api/workspace-setup/main-goals',
            'POST /api/workspace-setup/feature-selection',
            'POST /api/workspace-setup/team-setup',
            'POST /api/workspace-setup/subscription-selection',
            'POST /api/workspace-setup/branding-configuration',
            'POST /api/workspace-setup/complete'
        ];

        foreach ($endpoints as $endpoint) {
            $this->testEndpoint($endpoint);
        }
    }

    public function test_all_instagram_management_endpoints()
    {
        $organization = Organization::factory()->create();
        $this->user->organizations()->attach($organization);

        $endpoints = [
            'GET /api/instagram-management/accounts',
            'POST /api/instagram-management/accounts',
            'GET /api/instagram-management/posts',
            'POST /api/instagram-management/posts',
            'GET /api/instagram-management/hashtag-research',
            'GET /api/instagram-management/analytics'
        ];

        foreach ($endpoints as $endpoint) {
            $this->testEndpoint($endpoint);
        }
    }

    private function testEndpoint($endpoint)
    {
        [$method, $url] = explode(' ', $endpoint);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->json($method, $url, $this->getTestData($endpoint));

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data'
                ]);
    }

    private function getTestData($endpoint)
    {
        $testData = [
            'POST /api/workspace-setup/main-goals' => [
                'goals' => ['instagram_management', 'link_in_bio'],
                'primary_goal' => 'instagram_management'
            ],
            'POST /api/instagram-management/accounts' => [
                'username' => 'testaccount',
                'access_token' => 'test_token',
                'profile_data' => ['bio' => 'Test bio']
            ],
            'POST /api/instagram-management/posts' => [
                'account_id' => 1,
                'content' => 'Test post',
                'media_url' => 'https://example.com/image.jpg',
                'hashtags' => ['#test']
            ]
        ];

        return $testData[$endpoint] ?? [];
    }
}
```

## Performance Testing

### **Load Testing**
```php
<?php
// tests/Feature/PerformanceTest.php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class PerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_response_time()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $startTime = microtime(true);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/workspace-setup/current-step');
        
        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

        $response->assertStatus(200);
        $this->assertLessThan(150, $responseTime, 'API response time should be under 150ms');
    }

    public function test_database_query_performance()
    {
        // Create test data
        $users = User::factory()->count(100)->create();
        $organizations = Organization::factory()->count(50)->create();
        
        // Test complex query performance
        $startTime = microtime(true);
        
        DB::table('users')
            ->join('user_organizations', 'users.id', '=', 'user_organizations.user_id')
            ->join('organizations', 'user_organizations.organization_id', '=', 'organizations.id')
            ->select('users.*', 'organizations.name as org_name')
            ->get();
        
        $endTime = microtime(true);
        $queryTime = ($endTime - $startTime) * 1000;

        $this->assertLessThan(30, $queryTime, 'Database query should be under 30ms');
    }
}
```

## Security Testing

### **Authentication Security Tests**
```php
<?php
// tests/Feature/SecurityTest.php
namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_requires_authentication()
    {
        $response = $this->getJson('/api/workspace-setup/current-step');
        
        $response->assertStatus(401)
                ->assertJson([
                    'message' => 'Unauthenticated.'
                ]);
    }

    public function test_api_rate_limiting()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // Make requests up to rate limit
        for ($i = 0; $i < 60; $i++) {
            $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $token
            ])->getJson('/api/workspace-setup/current-step');
            
            $response->assertStatus(200);
        }

        // Next request should be rate limited
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/workspace-setup/current-step');
        
        $response->assertStatus(429);
    }

    public function test_sql_injection_prevention()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $maliciousInput = "'; DROP TABLE users; --";
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/instagram-management/accounts', [
            'username' => $maliciousInput,
            'access_token' => 'test_token'
        ]);

        // Should not succeed due to validation
        $response->assertStatus(422);
        
        // Verify users table still exists
        $this->assertTrue(Schema::hasTable('users'));
    }
}
```

## Test Data Management

### **Database Factories**
```php
<?php
// database/factories/InstagramAccountFactory.php
namespace Database\Factories;

use App\Models\InstagramAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

class InstagramAccountFactory extends Factory
{
    protected $model = InstagramAccount::class;

    public function definition()
    {
        return [
            'workspace_id' => 1,
            'username' => $this->faker->unique()->userName,
            'access_token' => $this->faker->sha256,
            'profile_data' => [
                'bio' => $this->faker->text(150),
                'followers_count' => $this->faker->numberBetween(100, 10000),
                'following_count' => $this->faker->numberBetween(50, 1000),
                'posts_count' => $this->faker->numberBetween(10, 500)
            ]
        ];
    }
}
```

### **Test Seeders**
```php
<?php
// database/seeders/TestSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Organization;

class TestSeeder extends Seeder
{
    public function run()
    {
        // Create test admin user
        $admin = User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin123')
        ]);

        // Create test organization
        $organization = Organization::factory()->create([
            'name' => 'Test Organization'
        ]);

        // Associate user with organization
        $admin->organizations()->attach($organization);

        // Create additional test data
        User::factory()->count(10)->create();
        Organization::factory()->count(5)->create();
    }
}
```

## Running Tests

### **Command Line Testing**
```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage

# Run specific test file
php artisan test tests/Feature/InstagramManagementTest.php

# Run with detailed output
php artisan test --verbose

# Run frontend tests
npm run test:frontend

# Run Playwright tests
npx playwright test

# Run performance tests
php artisan test --group=performance
```

### **Continuous Integration**
```yaml
# .github/workflows/tests.yml
name: Tests

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main ]

jobs:
  test:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.2'
        extensions: mbstring, dom, fileinfo, mysql
        
    - name: Copy .env
      run: php -r "file_exists('.env') || copy('.env.testing', '.env');"
      
    - name: Install Dependencies
      run: composer install -q --no-ansi --no-interaction --no-scripts --no-progress --prefer-dist
      
    - name: Generate key
      run: php artisan key:generate
      
    - name: Directory Permissions
      run: chmod -R 777 storage bootstrap/cache
      
    - name: Create Database
      run: |
        mkdir -p database
        touch database/database.sqlite
        
    - name: Execute tests
      env:
        DB_CONNECTION: sqlite
        DB_DATABASE: database/database.sqlite
      run: php artisan test
```

## Test Reporting

### **Test Coverage Reports**
```bash
# Generate HTML coverage report
php artisan test --coverage-html coverage-report

# Generate text coverage report
php artisan test --coverage-text

# Generate XML coverage report
php artisan test --coverage-xml
```

### **Performance Metrics**
```php
<?php
// tests/Feature/PerformanceMetricsTest.php
namespace Tests\Feature;

use Tests\TestCase;

class PerformanceMetricsTest extends TestCase
{
    public function test_api_performance_benchmarks()
    {
        $metrics = [
            'authentication' => $this->benchmarkEndpoint('POST', '/api/auth/login'),
            'workspace_setup' => $this->benchmarkEndpoint('GET', '/api/workspace-setup/current-step'),
            'instagram_accounts' => $this->benchmarkEndpoint('GET', '/api/instagram-management/accounts'),
            'bio_sites' => $this->benchmarkEndpoint('GET', '/api/bio-sites'),
        ];

        foreach ($metrics as $endpoint => $time) {
            $this->assertLessThan(150, $time, "{$endpoint} should respond in under 150ms");
        }
    }

    private function benchmarkEndpoint($method, $url)
    {
        $startTime = microtime(true);
        $this->json($method, $url);
        $endTime = microtime(true);
        
        return ($endTime - $startTime) * 1000;
    }
}
```

---

**Testing Guide - Comprehensive Platform Testing**
*Mewayz Platform - Version 2.0*
*Ensuring quality and reliability through comprehensive testing*
*Last Updated: July 15, 2025*