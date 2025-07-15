<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\OAuthController;
// use App\Http\Controllers\General\GeneralController;
// use App\Http\Controllers\InstallController;
// use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\ConsoleController;
use App\Http\Controllers\BioSiteController;
use App\Http\Controllers\SocialMediaController;
use App\Http\Controllers\CrmController;
use App\Http\Controllers\EmailMarketingController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\EcommerceController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\Admin\{AdminController, SiteController, PlanController};
use App\Http\Controllers\Installation\InstallationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// OAuth Routes (for web redirects)
Route::prefix('auth')->group(function () {
    Route::get('google', [OAuthController::class, 'redirectToProvider'])->defaults('provider', 'google');
    Route::get('google/callback', [OAuthController::class, 'handleProviderCallback'])->defaults('provider', 'google');
    Route::get('facebook', [OAuthController::class, 'redirectToProvider'])->defaults('provider', 'facebook');
    Route::get('facebook/callback', [OAuthController::class, 'handleProviderCallback'])->defaults('provider', 'facebook');
    Route::get('apple', [OAuthController::class, 'redirectToProvider'])->defaults('provider', 'apple');
    Route::get('apple/callback', [OAuthController::class, 'handleProviderCallback'])->defaults('provider', 'apple');
});

// Landing page route
Route::get('/', function () {
    return view('pages.landing');
});

// Flutter app route
Route::get('/app', function () {
    return response()->file(public_path('flutter.html'));
});

// Mobile app route alias
Route::get('/mobile', function () {
    return response()->file(public_path('flutter.html'));
});

// Flutter app direct route
Route::get('/flutter.html', function () {
    return response()->file(public_path('flutter.html'));
});

// Authentication routes are handled by auth.php using Livewire Volt
// Route::get('/login', [AuthController::class, 'login'])->name('login');
// Route::post('/login', [AuthController::class, 'authenticate']);
// Route::get('/register', [AuthController::class, 'register'])->name('register');
// Route::post('/register', [AuthController::class, 'store']);
// Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Installation routes (commented out - controller missing)
// Route::get('/install', [InstallationController::class, 'index'])->name('install');
// Route::post('/install', [InstallationController::class, 'store']);

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Console/Dashboard (commented out - controller missing)
    // Route::get('/console', [ConsoleController::class, 'index'])->name('console');
    // Route::get('/dashboard', [ConsoleController::class, 'index'])->name('dashboard');
    
    // Profile (commented out - controller missing)
    // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Bio Sites
    Route::get('/bio-sites', [BioSiteController::class, 'index'])->name('bio-sites');
    Route::get('/bio-sites/create', [BioSiteController::class, 'create'])->name('bio-sites.create');
    Route::post('/bio-sites', [BioSiteController::class, 'store'])->name('bio-sites.store');
    Route::get('/bio-sites/{bioSite}', [BioSiteController::class, 'show'])->name('bio-sites.show');
    Route::get('/bio-sites/{bioSite}/edit', [BioSiteController::class, 'edit'])->name('bio-sites.edit');
    Route::patch('/bio-sites/{bioSite}', [BioSiteController::class, 'update'])->name('bio-sites.update');
    Route::delete('/bio-sites/{bioSite}', [BioSiteController::class, 'destroy'])->name('bio-sites.destroy');
    
    // Social Media
    Route::get('/social-media', [SocialMediaController::class, 'index'])->name('social-media');
    Route::get('/social-media/accounts', [SocialMediaController::class, 'accounts'])->name('social-media.accounts');
    Route::get('/social-media/posts', [SocialMediaController::class, 'posts'])->name('social-media.posts');
    Route::post('/social-media/posts', [SocialMediaController::class, 'createPost'])->name('social-media.posts.create');
    
    // CRM
    Route::get('/crm', [CrmController::class, 'index'])->name('crm');
    Route::get('/crm/contacts', [CrmController::class, 'contacts'])->name('crm.contacts');
    Route::get('/crm/leads', [CrmController::class, 'leads'])->name('crm.leads');
    Route::post('/crm/contacts', [CrmController::class, 'storeContact'])->name('crm.contacts.store');
    
    // Email Marketing
    Route::get('/email-marketing', [EmailMarketingController::class, 'index'])->name('email-marketing');
    Route::get('/email-marketing/campaigns', [EmailMarketingController::class, 'campaigns'])->name('email-marketing.campaigns');
    Route::post('/email-marketing/campaigns', [EmailMarketingController::class, 'createCampaign'])->name('email-marketing.campaigns.create');
    
    // E-commerce
    Route::get('/ecommerce', [EcommerceController::class, 'index'])->name('ecommerce');
    Route::get('/ecommerce/products', [EcommerceController::class, 'products'])->name('ecommerce.products');
    Route::get('/ecommerce/orders', [EcommerceController::class, 'orders'])->name('ecommerce.orders');
    
    // Courses
    Route::get('/courses', [CourseController::class, 'index'])->name('courses');
    Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    
    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
    Route::get('/analytics/reports', [AnalyticsController::class, 'reports'])->name('analytics.reports');
    
    // Booking
    Route::get('/booking', [BookingController::class, 'index'])->name('booking');
    Route::get('/booking/calendar', [BookingController::class, 'calendar'])->name('booking.calendar');
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
    
    // Invoices
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices');
    Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    
    // Transactions
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions');
    
    // Subscriptions
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions');
    Route::post('/subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store');
    
    // Affiliate
    Route::get('/affiliate', [AffiliateController::class, 'index'])->name('affiliate');
    Route::get('/affiliate/dashboard', [AffiliateController::class, 'dashboard'])->name('affiliate.dashboard');
    
    // Partners
    Route::get('/partners', [PartnerController::class, 'index'])->name('partners');
    Route::get('/partners/dashboard', [PartnerController::class, 'dashboard'])->name('partners.dashboard');
    
    // Website Builder
    Route::get('/website', [WebsiteController::class, 'index'])->name('website');
    Route::get('/website/builder', [WebsiteController::class, 'builder'])->name('website.builder');
    Route::post('/website', [WebsiteController::class, 'store'])->name('website.store');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    
    // Site management
    Route::get('/sites', [SiteController::class, 'index'])->name('admin.sites');
    Route::get('/sites/{site}', [SiteController::class, 'show'])->name('admin.sites.show');
    Route::patch('/sites/{site}', [SiteController::class, 'update'])->name('admin.sites.update');
    Route::delete('/sites/{site}', [SiteController::class, 'destroy'])->name('admin.sites.destroy');
    
    // Plans management
    Route::get('/plans', [PlanController::class, 'index'])->name('admin.plans');
    Route::get('/plans/create', [PlanController::class, 'create'])->name('admin.plans.create');
    Route::post('/plans', [PlanController::class, 'store'])->name('admin.plans.store');
    Route::get('/plans/{plan}', [PlanController::class, 'show'])->name('admin.plans.show');
    Route::get('/plans/{plan}/edit', [PlanController::class, 'edit'])->name('admin.plans.edit');
    Route::patch('/plans/{plan}', [PlanController::class, 'update'])->name('admin.plans.update');
    Route::delete('/plans/{plan}', [PlanController::class, 'destroy'])->name('admin.plans.destroy');
    
    // Users management
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('admin.users.show');
    Route::patch('/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
    
    // Settings
    Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::patch('/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');
});

// Public bio site routes
Route::get('/{username}', [BioSiteController::class, 'public'])->name('bio-site.public');

// Fallback route
Route::fallback(function () {
    return view('pages.landing');
});

require __DIR__.'/auth.php';