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

// Include business routes
require __DIR__.'/business.php';

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

// Dashboard Routes
// Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('pages.dashboard.index');
    })->name('dashboard-index');
// });
    
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
    
    Route::get('/dashboard/calendar', function () {
        return view('pages.dashboard.calendar.index');
    })->name('dashboard-calendar-index');
    
    Route::get('/dashboard/analytics', function () {
        return view('pages.dashboard.analytics.index');
    })->name('dashboard-analytics-index');
    
    Route::get('/dashboard/settings', function () {
        return view('pages.dashboard.settings.index');
    })->name('dashboard-settings-index');
    
    Route::get('/dashboard/qr', function () {
        return view('pages.dashboard.qr.index');
    })->name('dashboard-qr-index');
});

// Test route without auth
Route::get('/test-dashboard', function () {
    return 'Dashboard test route working!';
})->name('test-dashboard');

// Authentication Routes - handled by auth.php
require __DIR__ . '/auth.php';

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');