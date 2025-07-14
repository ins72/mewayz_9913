<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\WorkspaceController;
use App\Http\Controllers\Api\SocialMediaController;
use App\Http\Controllers\Api\BioSiteController;
use App\Http\Controllers\Api\CrmController;
use App\Http\Controllers\Api\EmailMarketingController;
use App\Http\Controllers\Api\EcommerceController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\AnalyticsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Health check endpoint
Route::get('health', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'API is working',
        'timestamp' => now()
    ]);
});

// Public Auth Routes
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
});

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth Routes
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::put('profile', [AuthController::class, 'updateProfile']);
    });

    // User Route
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Workspace Routes
    Route::prefix('workspaces')->group(function () {
        Route::get('/', [WorkspaceController::class, 'index']);
        Route::post('/', [WorkspaceController::class, 'store']);
        Route::get('/{workspace}', [WorkspaceController::class, 'show']);
        Route::put('/{workspace}', [WorkspaceController::class, 'update']);
        Route::delete('/{workspace}', [WorkspaceController::class, 'destroy']);
        Route::post('/{workspace}/invite', [WorkspaceController::class, 'inviteTeamMember']);
        Route::get('/{workspace}/members', [WorkspaceController::class, 'getMembers']);
    });

    // Social Media Routes
    Route::prefix('social-media')->group(function () {
        Route::get('accounts', [SocialMediaController::class, 'getAccounts']);
        Route::post('accounts/connect', [SocialMediaController::class, 'connectAccount']);
        Route::delete('accounts/{account}', [SocialMediaController::class, 'disconnectAccount']);
        Route::post('schedule', [SocialMediaController::class, 'schedulePost']);
        Route::get('scheduled-posts', [SocialMediaController::class, 'getScheduledPosts']);
        Route::get('analytics', [SocialMediaController::class, 'getAnalytics']);
        Route::get('instagram/search', [SocialMediaController::class, 'searchInstagramAccounts']);
        Route::post('instagram/export', [SocialMediaController::class, 'exportInstagramData']);
    });

    // Bio Sites Routes
    Route::prefix('bio-sites')->group(function () {
        Route::get('/', [BioSiteController::class, 'index']);
        Route::post('/', [BioSiteController::class, 'store']);
        Route::get('/{bioSite}', [BioSiteController::class, 'show']);
        Route::put('/{bioSite}', [BioSiteController::class, 'update']);
        Route::delete('/{bioSite}', [BioSiteController::class, 'destroy']);
        Route::get('/{bioSite}/analytics', [BioSiteController::class, 'getAnalytics']);
        Route::get('/templates', [BioSiteController::class, 'getTemplates']);
    });

    // CRM Routes
    Route::prefix('crm')->group(function () {
        Route::get('leads', [CrmController::class, 'getLeads']);
        Route::post('leads', [CrmController::class, 'createLead']);
        Route::get('leads/{lead}', [CrmController::class, 'showLead']);
        Route::put('leads/{lead}', [CrmController::class, 'updateLead']);
        Route::delete('leads/{lead}', [CrmController::class, 'deleteLead']);
        Route::get('contacts', [CrmController::class, 'getContacts']);
        Route::post('contacts/import', [CrmController::class, 'importContacts']);
        Route::get('pipeline', [CrmController::class, 'getPipeline']);
        Route::post('bulk-accounts', [CrmController::class, 'createBulkAccounts']);
    });

    // Email Marketing Routes
    Route::prefix('email-marketing')->group(function () {
        Route::get('campaigns', [EmailMarketingController::class, 'getCampaigns']);
        Route::post('campaigns', [EmailMarketingController::class, 'createCampaign']);
        Route::get('campaigns/{campaign}', [EmailMarketingController::class, 'showCampaign']);
        Route::put('campaigns/{campaign}', [EmailMarketingController::class, 'updateCampaign']);
        Route::delete('campaigns/{campaign}', [EmailMarketingController::class, 'deleteCampaign']);
        Route::post('campaigns/{campaign}/send', [EmailMarketingController::class, 'sendCampaign']);
        Route::get('templates', [EmailMarketingController::class, 'getTemplates']);
        Route::post('templates', [EmailMarketingController::class, 'createTemplate']);
        Route::get('analytics', [EmailMarketingController::class, 'getAnalytics']);
        Route::get('audience', [EmailMarketingController::class, 'getAudience']);
    });

    // E-commerce Routes
    Route::prefix('ecommerce')->group(function () {
        Route::get('products', [EcommerceController::class, 'getProducts']);
        Route::post('products', [EcommerceController::class, 'createProduct']);
        Route::get('products/{product}', [EcommerceController::class, 'showProduct']);
        Route::put('products/{product}', [EcommerceController::class, 'updateProduct']);
        Route::delete('products/{product}', [EcommerceController::class, 'deleteProduct']);
        Route::get('orders', [EcommerceController::class, 'getOrders']);
        Route::get('orders/{order}', [EcommerceController::class, 'showOrder']);
        Route::put('orders/{order}/status', [EcommerceController::class, 'updateOrderStatus']);
        Route::get('analytics', [EcommerceController::class, 'getAnalytics']);
        Route::get('store/settings', [EcommerceController::class, 'getStoreSettings']);
        Route::put('store/settings', [EcommerceController::class, 'updateStoreSettings']);
    });

    // Course Routes
    Route::prefix('courses')->group(function () {
        Route::get('/', [CourseController::class, 'index']);
        Route::post('/', [CourseController::class, 'store']);
        Route::get('/{course}', [CourseController::class, 'show']);
        Route::put('/{course}', [CourseController::class, 'update']);
        Route::delete('/{course}', [CourseController::class, 'destroy']);
        Route::get('/{course}/students', [CourseController::class, 'getStudents']);
        Route::get('/{course}/lessons', [CourseController::class, 'getLessons']);
        Route::post('/{course}/lessons', [CourseController::class, 'createLesson']);
        Route::get('/analytics', [CourseController::class, 'getAnalytics']);
        Route::get('/community/groups', [CourseController::class, 'getCommunityGroups']);
    });

    // Analytics Routes
    Route::prefix('analytics')->group(function () {
        Route::get('/', [AnalyticsController::class, 'getOverview']);
        Route::get('traffic', [AnalyticsController::class, 'getTrafficAnalytics']);
        Route::get('revenue', [AnalyticsController::class, 'getRevenueAnalytics']);
        Route::get('reports', [AnalyticsController::class, 'getReports']);
        Route::post('reports/generate', [AnalyticsController::class, 'generateReport']);
    });
});
