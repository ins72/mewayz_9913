<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SocialMediaController;
use App\Http\Controllers\Api\BioSiteController;
use App\Http\Controllers\Api\WorkspaceController;
use App\Http\Controllers\Api\WorkspaceSetupController;
use App\Http\Controllers\Api\WorkspaceSetupWizardController;
use App\Http\Controllers\Api\TeamManagementController;
use App\Http\Controllers\Api\InstagramManagementController;
use App\Http\Controllers\Api\CrmController;
use App\Http\Controllers\Api\EcommerceController;
use App\Http\Controllers\Api\EmailMarketingController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\Api\OAuthController as ApiOAuthController;
use App\Http\Controllers\Api\AIController;
use App\Http\Controllers\Api\PWAController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Api\InstagramController;
use App\Http\Controllers\Api\StripePaymentController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\SystemController;
use App\Http\Controllers\Api\PlatformController;
use App\Http\Controllers\Api\BrandingController;
use App\Http\Controllers\Api\OptimizationController;

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

// Health and System Routes
Route::get('/health', [HealthController::class, 'index']);
Route::get('/system/info', [SystemController::class, 'info']);
Route::get('/system/maintenance', [SystemController::class, 'maintenance']);
Route::post('/system/cache/clear', [SystemController::class, 'clearCache']);
Route::post('/system/optimize', [SystemController::class, 'optimize']);

// Platform Information Routes
Route::get('/platform/overview', [PlatformController::class, 'overview']);
Route::get('/platform/statistics', [PlatformController::class, 'statistics']);
Route::get('/platform/features', [PlatformController::class, 'features']);
Route::get('/platform/roadmap', [PlatformController::class, 'roadmap']);

// Branding Routes
Route::get('/branding/info', [BrandingController::class, 'info']);
Route::get('/branding/assets', [BrandingController::class, 'assets']);
Route::get('/branding/consistency-report', [BrandingController::class, 'consistencyReport']);
Route::post('/branding/update', [BrandingController::class, 'update']);

// Optimization Routes
Route::get('/optimization/performance', [OptimizationController::class, 'performance']);
Route::get('/optimization/recommendations', [OptimizationController::class, 'recommendations']);
Route::post('/optimization/database', [OptimizationController::class, 'optimizeDatabase']);
Route::post('/optimization/cache', [OptimizationController::class, 'optimizeCache']);
Route::post('/optimization/system', [OptimizationController::class, 'optimizeSystem']);

// Public routes (no authentication required)
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    
    // OAuth routes
    Route::get('/oauth/providers', [ApiOAuthController::class, 'getProviders']);
    Route::get('/oauth/{provider}', [ApiOAuthController::class, 'redirectToProvider']);
    Route::get('/oauth/{provider}/callback', [ApiOAuthController::class, 'handleProviderCallback']);
    Route::post('/oauth/{provider}/test', [ApiOAuthController::class, 'handleProviderCallback']); // For test mode
    
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
    
    // OAuth management (protected routes)
    Route::get('/oauth/status', [ApiOAuthController::class, 'getOAuthStatus']);
    Route::post('/oauth/{provider}/link', [ApiOAuthController::class, 'linkAccount']);
    Route::delete('/oauth/{provider}/unlink', [ApiOAuthController::class, 'unlinkAccount']);
    
    // Workspace routes
    Route::get('/workspaces', [WorkspaceController::class, 'index']);
    Route::post('/workspaces', [WorkspaceController::class, 'store']);
    Route::get('/workspaces/{id}', [WorkspaceController::class, 'show']);
    Route::put('/workspaces/{id}', [WorkspaceController::class, 'update']);
    Route::delete('/workspaces/{id}', [WorkspaceController::class, 'destroy']);
    
    // Instagram Management routes
    Route::prefix('instagram-management')->group(function () {
        Route::get('/accounts', [InstagramManagementController::class, 'getAccounts']);
        Route::post('/accounts', [InstagramManagementController::class, 'addAccount']);
        Route::get('/posts', [InstagramManagementController::class, 'getPosts']);
        Route::post('/posts', [InstagramManagementController::class, 'createPost']);
        Route::put('/posts/{postId}', [InstagramManagementController::class, 'updatePost']);
        Route::delete('/posts/{postId}', [InstagramManagementController::class, 'deletePost']);
        Route::get('/hashtag-research', [InstagramManagementController::class, 'getHashtagResearch']);
        Route::get('/analytics', [InstagramManagementController::class, 'getAnalytics']);
    });
    
    // Email Marketing Hub routes
    Route::prefix('email-marketing')->group(function () {
        Route::get('/campaigns', [EmailMarketingController::class, 'getCampaigns']);
        Route::post('/campaigns', [EmailMarketingController::class, 'createCampaign']);
        Route::get('/campaigns/{campaignId}', [EmailMarketingController::class, 'getCampaign']);
        Route::put('/campaigns/{campaignId}', [EmailMarketingController::class, 'updateCampaign']);
        Route::delete('/campaigns/{campaignId}', [EmailMarketingController::class, 'deleteCampaign']);
        Route::post('/campaigns/{campaignId}/send', [EmailMarketingController::class, 'sendCampaign']);
        Route::get('/templates', [EmailMarketingController::class, 'getTemplates']);
        Route::get('/lists', [EmailMarketingController::class, 'getEmailLists']);
        Route::get('/subscribers', [EmailMarketingController::class, 'getSubscribers']);
        Route::get('/analytics', [EmailMarketingController::class, 'getAnalytics']);
    });
    
    // Workspace Setup Wizard routes
    Route::prefix('workspace-setup')->group(function () {
        Route::get('/current-step', [WorkspaceSetupController::class, 'getCurrentStep']);
        Route::get('/main-goals', [WorkspaceSetupController::class, 'getMainGoals']);
        Route::get('/available-features', [WorkspaceSetupController::class, 'getAvailableFeatures']);
        Route::get('/subscription-plans', [WorkspaceSetupController::class, 'getSubscriptionPlans']);
        Route::post('/main-goals', [WorkspaceSetupController::class, 'saveMainGoals']);
        Route::post('/feature-selection', [WorkspaceSetupController::class, 'saveFeatureSelection']);
        Route::post('/team-setup', [WorkspaceSetupController::class, 'saveTeamSetup']);
        Route::post('/subscription-selection', [WorkspaceSetupController::class, 'saveSubscriptionSelection']);
        Route::post('/branding-configuration', [WorkspaceSetupController::class, 'saveBrandingConfiguration']);
        Route::post('/complete', [WorkspaceSetupController::class, 'completeSetup']);
        Route::get('/summary', [WorkspaceSetupController::class, 'getSetupSummary']);
        Route::post('/reset', [WorkspaceSetupController::class, 'resetSetup']);
    });
    
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
        Route::get('/', [AnalyticsController::class, 'getOverview']);
        Route::get('/reports', [AnalyticsController::class, 'getReports']);
        Route::get('/social-media', [AnalyticsController::class, 'getSocialMediaAnalytics']);
        Route::get('/bio-sites', [AnalyticsController::class, 'getBioSiteAnalytics']);
        Route::get('/ecommerce', [AnalyticsController::class, 'getEcommerceAnalytics']);
        Route::get('/email-marketing', [AnalyticsController::class, 'getEmailMarketingAnalytics']);
    });
    
    // Workspace Setup Wizard routes
    Route::prefix('workspace-setup')->group(function () {
        Route::get('/initial-data', [WorkspaceSetupWizardController::class, 'getInitialData']);
        Route::post('/goals', [WorkspaceSetupWizardController::class, 'saveGoals']);
        Route::get('/features', [WorkspaceSetupWizardController::class, 'getFeatures']);
        Route::post('/features', [WorkspaceSetupWizardController::class, 'saveFeatures']);
        Route::post('/team', [WorkspaceSetupWizardController::class, 'saveTeamSetup']);
        Route::post('/pricing/calculate', [WorkspaceSetupWizardController::class, 'calculatePricing']);
        Route::post('/subscription', [WorkspaceSetupWizardController::class, 'saveSubscription']);
        Route::post('/branding', [WorkspaceSetupWizardController::class, 'saveBranding']);
        Route::get('/status', [WorkspaceSetupWizardController::class, 'getSetupStatus']);
        Route::post('/reset', [WorkspaceSetupWizardController::class, 'resetSetup']);
    });
    
    // Team Management routes
    Route::prefix('team')->group(function () {
        Route::get('/', [TeamManagementController::class, 'getTeam']);
        Route::post('/invite', [TeamManagementController::class, 'sendInvitation']);
        Route::post('/invitation/{uuid}/accept', [TeamManagementController::class, 'acceptInvitation']);
        Route::post('/invitation/{uuid}/reject', [TeamManagementController::class, 'rejectInvitation']);
        Route::get('/invitation/{uuid}', [TeamManagementController::class, 'getInvitationDetails']);
        Route::post('/invitation/{id}/resend', [TeamManagementController::class, 'resendInvitation']);
        Route::delete('/invitation/{id}', [TeamManagementController::class, 'cancelInvitation']);
        Route::put('/member/{id}/role', [TeamManagementController::class, 'updateMemberRole']);
        Route::delete('/member/{id}', [TeamManagementController::class, 'removeMember']);
    });
    
    // AI Integration routes
    Route::prefix('ai')->group(function () {
        Route::get('/services', [AIController::class, 'getServices']);
        Route::post('/chat', [AIController::class, 'chat']);
        Route::post('/generate-content', [AIController::class, 'generateContent']);
        Route::post('/recommendations', [AIController::class, 'getRecommendations']);
        Route::post('/analyze-text', [AIController::class, 'analyzeText']);
    });
    
    // PWA routes
    Route::prefix('pwa')->group(function () {
        Route::get('/manifest', [PWAController::class, 'getManifest']);
        Route::post('/subscribe', [PWAController::class, 'subscribePushNotifications']);
        Route::post('/unsubscribe', [PWAController::class, 'unsubscribePushNotifications']);
        Route::post('/test-notification', [PWAController::class, 'sendTestNotification']);
        Route::get('/installation-status', [PWAController::class, 'getInstallationStatus']);
        Route::get('/offline-content', [PWAController::class, 'getOfflineContent']);
        Route::post('/update-cache', [PWAController::class, 'updateCache']);
        Route::get('/analytics', [PWAController::class, 'getAnalytics']);
    });
    
    // Enhanced Analytics routes
    Route::prefix('analytics')->group(function () {
        Route::get('/overview', [AnalyticsController::class, 'getOverview']);
        Route::get('/traffic', [AnalyticsController::class, 'getTrafficAnalytics']);
        Route::get('/social-media', [AnalyticsController::class, 'getSocialMediaAnalytics']);
        Route::get('/bio-sites', [AnalyticsController::class, 'getBioSitesAnalytics']);
        Route::get('/ecommerce', [AnalyticsController::class, 'getEcommerceAnalytics']);
        Route::get('/courses', [AnalyticsController::class, 'getCourseAnalytics']);
        Route::get('/real-time', [AnalyticsController::class, 'getRealTimeAnalytics']);
        Route::post('/export', [AnalyticsController::class, 'exportAnalytics']);
    });
    
    // Admin Dashboard routes
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'getDashboardOverview']);
        Route::get('/users', [AdminController::class, 'getUsers']);
        Route::get('/users/{id}', [AdminController::class, 'getUserDetails']);
        Route::put('/users/{id}/status', [AdminController::class, 'updateUserStatus']);
        Route::get('/system-health', [AdminController::class, 'getSystemHealth']);
        Route::get('/platform-analytics', [AdminController::class, 'getPlatformAnalytics']);
        Route::get('/system-logs', [AdminController::class, 'getSystemLogs']);
        Route::post('/announcement', [AdminController::class, 'sendAnnouncement']);
        Route::get('/feature-usage', [AdminController::class, 'getFeatureUsage']);
    });
    
});

// Stripe Payment routes - public access for webhooks
Route::prefix('payments')->group(function () {
    Route::get('/packages', [StripePaymentController::class, 'getPackages']);
    Route::post('/checkout/session', [StripePaymentController::class, 'createCheckoutSession']);
    Route::get('/checkout/status/{sessionId}', [StripePaymentController::class, 'getCheckoutStatus']);
});

// Stripe webhook - must be outside auth middleware
Route::post('/webhook/stripe', [StripePaymentController::class, 'handleWebhook']);

// Legacy route for compatibility
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Fallback route for 404 errors
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found'
    ], 404);
});
