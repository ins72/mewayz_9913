<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SocialMediaController;
use App\Http\Controllers\Api\BioSiteController;
use App\Http\Controllers\Api\WorkspaceController;
use App\Http\Controllers\Api\CrmController;
use App\Http\Controllers\Api\EcommerceController;
use App\Http\Controllers\Api\EmailMarketingController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Api\InstagramController;

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

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'API is working',
        'timestamp' => now()
    ]);
});

// Public routes (no authentication required)
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    
    // OAuth routes
    Route::get('/oauth/{provider}', [OAuthController::class, 'redirectToProvider']);
    Route::get('/oauth/{provider}/callback', [OAuthController::class, 'handleProviderCallback']);
    Route::get('/oauth-status', [OAuthController::class, 'getOAuthStatus']);
    
    // Two-Factor Authentication routes
    Route::post('/2fa/generate', [TwoFactorController::class, 'generate']);
    Route::post('/2fa/enable', [TwoFactorController::class, 'enable']);
    Route::post('/2fa/disable', [TwoFactorController::class, 'disable']);
    Route::get('/2fa/status', [TwoFactorController::class, 'status']);
    Route::post('/2fa/recovery-codes', [TwoFactorController::class, 'recoveryCodes']);
    Route::post('/2fa/verify', [TwoFactorController::class, 'verify']);
});

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    
    // User profile routes
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
    
    // Workspace routes
    Route::get('/workspaces', [WorkspaceController::class, 'index']);
    Route::post('/workspaces', [WorkspaceController::class, 'store']);
    Route::get('/workspaces/{id}', [WorkspaceController::class, 'show']);
    Route::put('/workspaces/{id}', [WorkspaceController::class, 'update']);
    Route::delete('/workspaces/{id}', [WorkspaceController::class, 'destroy']);
    
    // Social Media routes
    Route::prefix('social-media')->group(function () {
        Route::get('/accounts', [SocialMediaController::class, 'getAccounts']);
        Route::post('/accounts/connect', [SocialMediaController::class, 'connectAccount']);
        Route::delete('/accounts/{id}', [SocialMediaController::class, 'disconnectAccount']);
        Route::get('/analytics', [SocialMediaController::class, 'getAnalytics']);
        Route::post('/posts', [SocialMediaController::class, 'createPost']);
        Route::get('/posts', [SocialMediaController::class, 'getPosts']);
        Route::put('/posts/{id}', [SocialMediaController::class, 'updatePost']);
        Route::delete('/posts/{id}', [SocialMediaController::class, 'deletePost']);
    });
    
    // Instagram Intelligence Engine routes
    Route::prefix('instagram')->group(function () {
        Route::get('/auth', [InstagramController::class, 'initiateAuth']);
        Route::post('/auth/callback', [InstagramController::class, 'handleCallback']);
        Route::get('/competitor-analysis', [InstagramController::class, 'getCompetitorAnalysis']);
        Route::post('/advanced-competitor-analysis', [InstagramController::class, 'getAdvancedCompetitorAnalysis']);
        Route::get('/hashtag-analysis', [InstagramController::class, 'getHashtagAnalysis']);
        Route::get('/analytics', [InstagramController::class, 'getAnalytics']);
        Route::post('/predict-content-performance', [InstagramController::class, 'predictContentPerformance']);
        Route::get('/audience-intelligence', [InstagramController::class, 'getAdvancedAudienceIntelligence']);
        Route::post('/refresh-token', [InstagramController::class, 'refreshToken']);
        Route::get('/content-suggestions', [InstagramController::class, 'getContentSuggestions']);
    });
    
    // Bio Site routes
    Route::prefix('bio-sites')->group(function () {
        Route::get('/', [BioSiteController::class, 'index']);
        Route::post('/', [BioSiteController::class, 'store']);
        Route::get('/themes', [BioSiteController::class, 'getThemes']);
        Route::get('/{id}', [BioSiteController::class, 'show']);
        Route::put('/{id}', [BioSiteController::class, 'update']);
        Route::delete('/{id}', [BioSiteController::class, 'destroy']);
        Route::get('/{id}/analytics', [BioSiteController::class, 'getAnalytics']);
        Route::get('/{id}/advanced-analytics', [BioSiteController::class, 'getAdvancedAnalytics']);
        Route::post('/{id}/duplicate', [BioSiteController::class, 'duplicate']);
        Route::get('/{id}/export', [BioSiteController::class, 'export']);
        Route::post('/{id}/ab-test', [BioSiteController::class, 'createABTest']);
        Route::get('/{bioSiteId}/ab-test/{testId}/results', [BioSiteController::class, 'getABTestResults']);
        Route::post('/{id}/monetization', [BioSiteController::class, 'addMonetizationFeatures']);
        
        // Bio Site Links management
        Route::get('/{bioSiteId}/links', [BioSiteController::class, 'getLinks']);
        Route::post('/{bioSiteId}/links', [BioSiteController::class, 'createLink']);
        Route::put('/{bioSiteId}/links/{linkId}', [BioSiteController::class, 'updateLink']);
        Route::delete('/{bioSiteId}/links/{linkId}', [BioSiteController::class, 'deleteLink']);
        Route::post('/{bioSiteId}/links/reorder', [BioSiteController::class, 'updateLinkOrder']);
    });
    
    // CRM routes
    Route::prefix('crm')->group(function () {
        Route::get('/contacts', [CrmController::class, 'getContacts']);
        Route::post('/contacts', [CrmController::class, 'createContact']);
        Route::get('/contacts/{id}', [CrmController::class, 'getContact']);
        Route::put('/contacts/{id}', [CrmController::class, 'updateContact']);
        Route::delete('/contacts/{id}', [CrmController::class, 'deleteContact']);
        Route::get('/leads', [CrmController::class, 'getLeads']);
        Route::post('/leads', [CrmController::class, 'createLead']);
        Route::put('/leads/{id}', [CrmController::class, 'updateLead']);
        Route::delete('/leads/{id}', [CrmController::class, 'deleteLead']);
        
        // NEW ADVANCED CRM FEATURES
        Route::post('/automation-workflow', [CrmController::class, 'createAutomationWorkflow']);
        Route::get('/ai-lead-scoring', [CrmController::class, 'getAILeadScoring']);
        Route::get('/advanced-pipeline-management', [CrmController::class, 'getAdvancedPipelineManagement']);
        Route::get('/predictive-analytics', [CrmController::class, 'getPredictiveAnalytics']);
    });
    
    // E-commerce routes
    Route::prefix('ecommerce')->group(function () {
        Route::get('/products', [EcommerceController::class, 'getProducts']);
        Route::post('/products', [EcommerceController::class, 'createProduct']);
        Route::get('/products/{id}', [EcommerceController::class, 'getProduct']);
        Route::put('/products/{id}', [EcommerceController::class, 'updateProduct']);
        Route::delete('/products/{id}', [EcommerceController::class, 'deleteProduct']);
        Route::get('/orders', [EcommerceController::class, 'getOrders']);
        Route::post('/orders', [EcommerceController::class, 'createOrder']);
        Route::get('/orders/{id}', [EcommerceController::class, 'getOrder']);
        Route::put('/orders/{id}', [EcommerceController::class, 'updateOrder']);
    });
    
    // Email Marketing routes
    Route::prefix('email-marketing')->group(function () {
        Route::get('/campaigns', [EmailMarketingController::class, 'getCampaigns']);
        Route::post('/campaigns', [EmailMarketingController::class, 'createCampaign']);
        Route::get('/campaigns/{id}', [EmailMarketingController::class, 'getCampaign']);
        Route::put('/campaigns/{id}', [EmailMarketingController::class, 'updateCampaign']);
        Route::delete('/campaigns/{id}', [EmailMarketingController::class, 'deleteCampaign']);
        Route::post('/campaigns/{id}/send', [EmailMarketingController::class, 'sendCampaign']);
        Route::get('/templates', [EmailMarketingController::class, 'getTemplates']);
        Route::post('/templates', [EmailMarketingController::class, 'createTemplate']);
        Route::get('/templates/{id}', [EmailMarketingController::class, 'getTemplate']);
        Route::put('/templates/{id}', [EmailMarketingController::class, 'updateTemplate']);
        Route::delete('/templates/{id}', [EmailMarketingController::class, 'deleteTemplate']);
    });
    
    // Course routes
    Route::prefix('courses')->group(function () {
        Route::get('/', [CourseController::class, 'index']);
        Route::post('/', [CourseController::class, 'store']);
        Route::get('/{id}', [CourseController::class, 'show']);
        Route::put('/{id}', [CourseController::class, 'update']);
        Route::delete('/{id}', [CourseController::class, 'destroy']);
        Route::get('/{id}/lessons', [CourseController::class, 'getLessons']);
        Route::post('/{id}/lessons', [CourseController::class, 'createLesson']);
        Route::get('/{id}/students', [CourseController::class, 'getStudents']);
        Route::post('/{id}/enroll', [CourseController::class, 'enrollStudent']);
    });
    
    // Analytics routes
    Route::prefix('analytics')->group(function () {
        Route::get('/', [AnalyticsController::class, 'overview']);
        Route::get('/reports', [AnalyticsController::class, 'getReports']);
        Route::get('/social-media', [AnalyticsController::class, 'getSocialMediaAnalytics']);
        Route::get('/bio-sites', [AnalyticsController::class, 'getBioSiteAnalytics']);
        Route::get('/ecommerce', [AnalyticsController::class, 'getEcommerceAnalytics']);
        Route::get('/email-marketing', [AnalyticsController::class, 'getEmailMarketingAnalytics']);
    });
    
});

// Fallback route for 404 errors
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found'
    ], 404);
});