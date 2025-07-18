<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\SubscriptionPlanController;
use App\Http\Controllers\Admin\EnvironmentController;
use App\Http\Controllers\Admin\BulkOperationController;
use App\Http\Controllers\Admin\FeatureFlagController;
use App\Http\Controllers\Admin\SystemAnalyticsController;
use App\Http\Controllers\Admin\DatabaseManagementController;

/*
|--------------------------------------------------------------------------
| Admin API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin API routes for your application.
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    
    // Dashboard Routes
    Route::get('/dashboard', [AdminDashboardController::class, 'index']);
    Route::get('/dashboard/analytics', [AdminDashboardController::class, 'getAnalytics']);
    Route::get('/dashboard/system-info', [AdminDashboardController::class, 'getSystemInfo']);
    
    // User Management Routes
    Route::prefix('users')->group(function () {
        Route::get('/', [UserManagementController::class, 'index']);
        Route::get('/{id}', [UserManagementController::class, 'show']);
        Route::post('/bulk-import', [UserManagementController::class, 'bulkImport']);
        Route::post('/bulk-update', [UserManagementController::class, 'bulkUpdate']);
        Route::post('/bulk-delete', [UserManagementController::class, 'bulkDelete']);
        Route::get('/statistics', [UserManagementController::class, 'getStatistics']);
    });
    
    // Subscription Plan Management Routes
    Route::prefix('plans')->group(function () {
        Route::get('/', [SubscriptionPlanController::class, 'index']);
        Route::post('/', [SubscriptionPlanController::class, 'store']);
        Route::get('/{id}', [SubscriptionPlanController::class, 'show']);
        Route::put('/{id}', [SubscriptionPlanController::class, 'update']);
        Route::delete('/{id}', [SubscriptionPlanController::class, 'destroy']);
        Route::put('/{id}/features', [SubscriptionPlanController::class, 'updateFeatures']);
        Route::put('/{id}/pricing', [SubscriptionPlanController::class, 'updatePricing']);
        Route::post('/comparison', [SubscriptionPlanController::class, 'getComparison']);
        Route::get('/analytics', [SubscriptionPlanController::class, 'getAnalytics']);
    });
    
    // Environment & System Configuration Routes
    Route::prefix('environment')->group(function () {
        Route::get('/', [EnvironmentController::class, 'index']);
        Route::post('/', [EnvironmentController::class, 'store']);
        Route::put('/{id}', [EnvironmentController::class, 'update']);
        Route::delete('/{id}', [EnvironmentController::class, 'destroy']);
        Route::post('/sync-from-env', [EnvironmentController::class, 'syncFromEnv']);
        Route::post('/write-to-env', [EnvironmentController::class, 'writeToEnv']);
        Route::get('/system-settings', [EnvironmentController::class, 'getSystemSettings']);
        Route::put('/system-settings/{key}', [EnvironmentController::class, 'updateSystemSetting']);
        Route::get('/system-info', [EnvironmentController::class, 'getSystemInfo']);
        Route::post('/clear-cache', [EnvironmentController::class, 'clearCache']);
    });
    
    // Bulk Operations Routes
    Route::prefix('bulk-operations')->group(function () {
        Route::get('/', [BulkOperationController::class, 'index']);
        Route::get('/{id}', [BulkOperationController::class, 'show']);
        Route::post('/{id}/retry', [BulkOperationController::class, 'retry']);
        Route::delete('/{id}', [BulkOperationController::class, 'destroy']);
        Route::get('/{id}/download-results', [BulkOperationController::class, 'downloadResults']);
    });
    
    // Feature Flags Routes
    Route::prefix('feature-flags')->group(function () {
        Route::get('/', [FeatureFlagController::class, 'index']);
        Route::post('/', [FeatureFlagController::class, 'store']);
        Route::get('/{id}', [FeatureFlagController::class, 'show']);
        Route::put('/{id}', [FeatureFlagController::class, 'update']);
        Route::delete('/{id}', [FeatureFlagController::class, 'destroy']);
        Route::put('/{id}/toggle', [FeatureFlagController::class, 'toggle']);
        Route::get('/{id}/analytics', [FeatureFlagController::class, 'getAnalytics']);
    });
    
    // System Analytics Routes
    Route::prefix('analytics')->group(function () {
        Route::get('/overview', [SystemAnalyticsController::class, 'overview']);
        Route::get('/users', [SystemAnalyticsController::class, 'userAnalytics']);
        Route::get('/revenue', [SystemAnalyticsController::class, 'revenueAnalytics']);
        Route::get('/performance', [SystemAnalyticsController::class, 'performanceAnalytics']);
        Route::get('/errors', [SystemAnalyticsController::class, 'errorAnalytics']);
        Route::get('/custom-report', [SystemAnalyticsController::class, 'customReport']);
        Route::post('/export', [SystemAnalyticsController::class, 'exportReport']);
    });
    
    // Database Management Routes
    Route::prefix('database')->group(function () {
        Route::get('/tables', [DatabaseManagementController::class, 'getTables']);
        Route::get('/tables/{table}', [DatabaseManagementController::class, 'getTableData']);
        Route::get('/tables/{table}/structure', [DatabaseManagementController::class, 'getTableStructure']);
        Route::post('/tables/{table}/records', [DatabaseManagementController::class, 'createRecord']);
        Route::put('/tables/{table}/records/{id}', [DatabaseManagementController::class, 'updateRecord']);
        Route::delete('/tables/{table}/records/{id}', [DatabaseManagementController::class, 'deleteRecord']);
        Route::post('/execute-query', [DatabaseManagementController::class, 'executeQuery']);
        Route::get('/backup', [DatabaseManagementController::class, 'createBackup']);
        Route::post('/restore', [DatabaseManagementController::class, 'restoreBackup']);
    });
    
    // User Segments Routes
    Route::prefix('segments')->group(function () {
        Route::get('/', [UserSegmentController::class, 'index']);
        Route::post('/', [UserSegmentController::class, 'store']);
        Route::get('/{id}', [UserSegmentController::class, 'show']);
        Route::put('/{id}', [UserSegmentController::class, 'update']);
        Route::delete('/{id}', [UserSegmentController::class, 'destroy']);
        Route::post('/{id}/refresh', [UserSegmentController::class, 'refreshMemberships']);
        Route::get('/{id}/users', [UserSegmentController::class, 'getUsers']);
    });
    
    // API Key Management Routes
    Route::prefix('api-keys')->group(function () {
        Route::get('/', [ApiKeyController::class, 'index']);
        Route::post('/', [ApiKeyController::class, 'store']);
        Route::get('/{id}', [ApiKeyController::class, 'show']);
        Route::put('/{id}', [ApiKeyController::class, 'update']);
        Route::delete('/{id}', [ApiKeyController::class, 'destroy']);
        Route::post('/{id}/regenerate', [ApiKeyController::class, 'regenerate']);
        Route::get('/{id}/usage', [ApiKeyController::class, 'getUsage']);
    });
    
    // Activity Logs Routes
    Route::prefix('activity-logs')->group(function () {
        Route::get('/', [ActivityLogController::class, 'index']);
        Route::get('/{id}', [ActivityLogController::class, 'show']);
        Route::post('/export', [ActivityLogController::class, 'export']);
    });
});

// Public admin routes (no authentication required)
Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/forgot-password', [AdminAuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AdminAuthController::class, 'resetPassword']);
});

// Admin authenticated routes
Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('/logout', [AdminAuthController::class, 'logout']);
    Route::get('/profile', [AdminAuthController::class, 'profile']);
    Route::put('/profile', [AdminAuthController::class, 'updateProfile']);
    Route::put('/password', [AdminAuthController::class, 'updatePassword']);
    Route::post('/enable-2fa', [AdminAuthController::class, 'enableTwoFactor']);
    Route::post('/disable-2fa', [AdminAuthController::class, 'disableTwoFactor']);
});