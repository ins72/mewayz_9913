<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WorkspaceSetupController;

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

Route::get('/', function () {
    return view('pages.index');
})->name('home');

// Include legal routes
require __DIR__.'/legal.php';

// Include auth routes
require __DIR__.'/auth.php';

// Include business routes
require __DIR__.'/business.php';

// Include install routes
require __DIR__.'/install.php';

Route::get('/landing', function () {
    return view('pages.landing');
});

// Workspace Setup Routes
Route::middleware('auth')->group(function () {
    Route::get('/workspace-setup', [WorkspaceSetupController::class, 'index'])->name('workspace-setup.index');
    Route::post('/api/workspace-setup/step-1', [WorkspaceSetupController::class, 'processStep1'])->name('workspace-setup.step-1');
    Route::post('/api/workspace-setup/step-2', [WorkspaceSetupController::class, 'processStep2'])->name('workspace-setup.step-2');
    Route::post('/api/workspace-setup/step-3', [WorkspaceSetupController::class, 'processStep3'])->name('workspace-setup.step-3');
    Route::post('/api/workspace-setup/step-4', [WorkspaceSetupController::class, 'processStep4'])->name('workspace-setup.step-4');
    Route::post('/api/workspace-setup/step-5', [WorkspaceSetupController::class, 'processStep5'])->name('workspace-setup.step-5');
    Route::post('/api/workspace-setup/available-features', [WorkspaceSetupController::class, 'getAvailableFeatures'])->name('workspace-setup.available-features');
    Route::post('/api/workspace-setup/calculate-pricing', [WorkspaceSetupController::class, 'calculatePricing'])->name('workspace-setup.calculate-pricing');
});

// Dashboard Routes (protected by auth middleware)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('pages.dashboard.index');
    })->name('dashboard');

    Route::get('/dashboard', function () {
        return view('pages.dashboard.index');
    })->name('dashboard-index');

    Route::get('/dashboard/workspace', function () {
        return view('pages.dashboard.workspace.index');
    })->name('dashboard-workspace-index');

    Route::get('/dashboard/sites', function () {
        return view('pages.dashboard.sites.index');
    })->name('dashboard-sites-index');

    Route::get('/dashboard/templates', function () {
        return view('pages.dashboard.templates.index');
    })->name('dashboard-templates-index');

    Route::get('/dashboard/audience', function () {
        return view('pages.dashboard.audience.index');
    })->name('dashboard-audience-index');

    Route::get('/dashboard/community', function () {
        return view('pages.dashboard.community.index');
    })->name('dashboard-community-index');

    Route::get('/dashboard/booking', function () {
        return view('pages.dashboard.booking.index');
    })->name('dashboard-booking-index');

    Route::get('/dashboard/automation', function () {
        return view('pages.dashboard.automation.index');
    })->name('dashboard-automation-index');

    Route::get('/dashboard/reports', function () {
        return view('pages.dashboard.reports.index');
    })->name('dashboard-reports-index');

    Route::get('/dashboard/wallet', function () {
        return view('pages.dashboard.wallet.index');
    })->name('dashboard-wallet-index');

    Route::get('/dashboard/invoices', function () {
        return view('pages.dashboard.invoices.index');
    })->name('dashboard-invoices-index');

    Route::get('/dashboard/team', function () {
        return view('pages.dashboard.team.index');
    })->name('dashboard-team-index');

    Route::get('/dashboard/ai', function () {
        return view('pages.dashboard.ai.index');
    })->name('dashboard-ai-index');

    Route::get('/dashboard/media', function () {
        return view('pages.dashboard.media.index');
    })->name('dashboard-media-index');

    Route::get('/dashboard/integrations', function () {
        return view('pages.dashboard.integrations.index');
    })->name('dashboard-integrations-index');

    Route::get('/dashboard/help', function () {
        return view('pages.dashboard.help.index');
    })->name('dashboard-help-index');

    Route::get('/dashboard/upgrade', function () {
        return view('pages.dashboard.upgrade.index');
    })->name('dashboard-upgrade-index');

    Route::get('/dashboard/instagram', function () {
        return view('pages.dashboard.instagram.index');
    })->name('dashboard-instagram-index');

    Route::get('/dashboard/social', function () {
        return view('pages.dashboard.social.index');
    })->name('dashboard-social-index');

    Route::get('/dashboard/linkinbio', function () {
        return view('pages.dashboard.linkinbio.index');
    })->name('dashboard-linkinbio-index');

    Route::get('/dashboard/courses', function () {
        return view('pages.dashboard.courses.index');
    })->name('dashboard-courses-index');

    Route::get('/dashboard/store', function () {
        return view('pages.dashboard.store.index');
    })->name('dashboard-store-index');

    Route::get('/dashboard/crm', function () {
        return view('pages.dashboard.crm.index');
    })->name('dashboard-crm-index');

    Route::get('/dashboard/email', function () {
        return view('pages.dashboard.email.index');
    })->name('dashboard-email-index');

    Route::get('/dashboard/analytics', function () {
        return view('pages.dashboard.analytics.index');
    })->name('dashboard-analytics-index');

    Route::get('/dashboard/qr', function () {
        return view('pages.dashboard.qr.index');
    })->name('dashboard-qr-index');

    Route::get('/dashboard/calendar', function () {
        return view('pages.dashboard.calendar.index');
    })->name('dashboard-calendar-index');

    Route::get('/dashboard/settings', function () {
        return view('pages.dashboard.settings.index');
    })->name('dashboard-settings-index');
});

// Test route without auth
Route::get('/test-dashboard', function () {
    return 'Dashboard test route working!';
})->name('test-dashboard');

// Use the controllers
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\LegalController;

// Essential Business Pages Routes (using BusinessController)
Route::get('/about', [BusinessController::class, 'about'])->name('about');
Route::get('/pricing', [BusinessController::class, 'pricing'])->name('pricing');
Route::get('/features', [BusinessController::class, 'features'])->name('features');
Route::get('/contact', [BusinessController::class, 'contact'])->name('contact');
Route::post('/contact', [BusinessController::class, 'submitContact'])->name('contact.submit');
Route::get('/blog', [BusinessController::class, 'blog'])->name('blog');
Route::get('/case-studies', [BusinessController::class, 'caseStudies'])->name('case-studies');
Route::get('/testimonials', [BusinessController::class, 'testimonials'])->name('testimonials');
Route::get('/careers', [BusinessController::class, 'careers'])->name('careers');
Route::get('/partners', [BusinessController::class, 'partners'])->name('partners');
Route::get('/security', [BusinessController::class, 'security'])->name('security');

// Legal & Compliance Pages (using LegalController)
Route::get('/terms-of-service', [LegalController::class, 'termsOfService'])->name('terms-of-service');
Route::get('/privacy-policy', [LegalController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/cookie-policy', [LegalController::class, 'cookiePolicy'])->name('cookie-policy');
Route::get('/refund-policy', [LegalController::class, 'refundPolicy'])->name('refund-policy');
Route::get('/accessibility', [LegalController::class, 'accessibilityStatement'])->name('accessibility');

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');