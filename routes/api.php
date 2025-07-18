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
use App\Http\Controllers\Api\LinkInBioController;
use App\Http\Controllers\Api\TemplateMarketplaceController;
use App\Http\Controllers\Api\GamificationController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\SubscriptionPlanController;
use App\Http\Controllers\Admin\EnvironmentController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Api\InstagramController;
use App\Http\Controllers\Api\StripePaymentController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\SystemController;
use App\Http\Controllers\Api\PlatformController;
use App\Http\Controllers\Api\BrandingController;
use App\Http\Controllers\Api\OptimizationController;
use App\Http\Controllers\Api\WebsiteBuilderController;
use App\Http\Controllers\Api\BiometricAuthController;
use App\Http\Controllers\Api\RealTimeController;
use App\Http\Controllers\Api\EscrowController;
use App\Http\Controllers\Api\AdvancedAnalyticsController;
use App\Http\Controllers\Api\AdvancedBookingController;
use App\Http\Controllers\Api\AdvancedFinancialController;
use App\Http\Controllers\Api\EnhancedAIController;
use App\Http\Controllers\Api\InstagramDatabaseController;
use App\Http\Controllers\Api\VisualBioBuilderController;
use App\Http\Controllers\Api\WebSocketController;
use App\Http\Controllers\Api\UnifiedDataController;

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

// Test Routes
Route::get('/test', function () {
    return response()->json([
        'message' => 'Mewayz API is working!',
        'status' => 'success',
        'version' => '1.0.0',
        'timestamp' => now()
    ]);
});

Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'database' => 'connected',
        'redis' => 'available',
        'timestamp' => now()
    ]);
});

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
    Route::get('/oauth/status', [ApiOAuthController::class, 'getStatus']);
    Route::get('/oauth/{provider}', [OAuthController::class, 'redirectToProvider']);
    Route::get('/oauth/{provider}/callback', [OAuthController::class, 'handleProviderCallback']);
    Route::post('/oauth/{provider}/test', [OAuthController::class, 'handleProviderCallback']); // For test mode
    
    // Two-Factor Authentication routes
    Route::post('/2fa/generate', [TwoFactorController::class, 'generate']);
    Route::post('/2fa/enable', [TwoFactorController::class, 'enable']);
    Route::post('/2fa/disable', [TwoFactorController::class, 'disable']);
    Route::get('/2fa/status', [TwoFactorController::class, 'status']);
    Route::post('/2fa/recovery-codes', [TwoFactorController::class, 'recoveryCodes']);
    Route::post('/2fa/verify', [TwoFactorController::class, 'verify']);
});

// Protected routes (require authentication)
// Instagram debugging route
Route::middleware(\App\Http\Middleware\CustomSanctumAuth::class)->get('/instagram/debug', function (Request $request) {
    try {
        $user = $request->user();
        $organizations = $user->organizations()->count();
        
        return response()->json([
            'success' => true,
            'user_id' => $user->id,
            'organizations_count' => $organizations,
            'debug' => 'Instagram debug successful',
            'timestamp' => now()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Test route without auth middleware
Route::get('/test-no-auth', function (Request $request) {
    return response()->json([
        'message' => 'Test without auth middleware',
        'timestamp' => now()
    ]);
});

// Protected routes (require authentication) - FIXED: Using custom auth middleware
Route::middleware(\App\Http\Middleware\CustomSanctumAuth::class)->group(function () {
    // Authentication endpoints
    Route::get('/test-custom-auth', function (Request $request) {
        $user = $request->user();
        return response()->json([
            'message' => 'Custom auth test successful',
            'user_id' => $user->id,
            'user_name' => $user->name,
            'timestamp' => now()
        ]);
    });
    
    Route::get('/auth/me', [AuthController::class, 'me']);
    
    // OAuth status (authenticated)
    Route::get('/oauth/status', [ApiOAuthController::class, 'getStatus']);
    
    // Workspace routes
    Route::get('/workspaces', [WorkspaceController::class, 'index']);
    Route::post('/workspaces', [WorkspaceController::class, 'store']);
    Route::get('/workspaces/{id}', [WorkspaceController::class, 'show']);
    Route::put('/workspaces/{id}', [WorkspaceController::class, 'update']);
    Route::delete('/workspaces/{id}', [WorkspaceController::class, 'destroy']);
    
    // Gamification routes
    Route::prefix('gamification')->group(function () {
        Route::get('/profile', [GamificationController::class, 'getProfile']);
        Route::get('/achievements', [GamificationController::class, 'getAchievements']);
        Route::post('/award-xp', [GamificationController::class, 'awardXp']);
        Route::post('/update-streak', [GamificationController::class, 'updateStreak']);
        Route::get('/leaderboard', [GamificationController::class, 'getLeaderboard']);
        Route::get('/statistics', [GamificationController::class, 'getStatistics']);
    });
    
    // Admin routes
    Route::prefix('admin')->middleware(['custom.auth', 'admin'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index']);
        
        // User Management
        Route::prefix('users')->group(function () {
            Route::get('/', [UserManagementController::class, 'index']);
            Route::get('/{id}', [UserManagementController::class, 'show']);
            Route::post('/bulk-import', [UserManagementController::class, 'bulkImport']);
            Route::post('/bulk-update', [UserManagementController::class, 'bulkUpdate']);
            Route::post('/bulk-delete', [UserManagementController::class, 'bulkDelete']);
            Route::get('/statistics', [UserManagementController::class, 'getStatistics']);
        });
        
        // Subscription Plans
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
        
        // Environment Configuration
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
    });
    
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
        
        // ElasticEmail integration routes
        Route::get('/test-elastic-email', [EmailMarketingController::class, 'testElasticEmail']);
        Route::post('/campaigns/{campaignId}/send-elastic-email', [EmailMarketingController::class, 'sendCampaignWithElasticEmail']);
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
    
    // Instagram Database & Lead Generation routes
    Route::prefix('instagram-database')->group(function () {
        Route::get('/search', [InstagramDatabaseController::class, 'searchProfiles']);
        Route::get('/profiles/{id}', [InstagramDatabaseController::class, 'getProfile']);
        Route::post('/scrape', [InstagramDatabaseController::class, 'scrapeProfile']);
        Route::post('/export', [InstagramDatabaseController::class, 'exportProfiles']);
        Route::post('/import', [InstagramDatabaseController::class, 'importProfiles']);
        Route::get('/analytics', [InstagramDatabaseController::class, 'getAnalytics']);
    });
    
    // AI & Automation routes
    Route::prefix('ai-automation')->group(function () {
        Route::post('/generate-content', [AiAutomationController::class, 'generateContent']);
        Route::post('/content-suggestions', [AiAutomationController::class, 'getContentSuggestions']);
        Route::get('/workflows', [AiAutomationController::class, 'getWorkflows']);
        Route::post('/workflows', [AiAutomationController::class, 'createWorkflow']);
        Route::post('/workflows/{id}/execute', [AiAutomationController::class, 'executeWorkflow']);
        Route::get('/analytics', [AiAutomationController::class, 'getAiAnalytics']);
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
    
    // Template Marketplace routes
    Route::prefix('templates')->group(function () {
        Route::get('/', [TemplateMarketplaceController::class, 'getTemplates']);
        Route::get('/categories', [TemplateMarketplaceController::class, 'getCategories']);
        Route::get('/{id}', [TemplateMarketplaceController::class, 'getTemplateDetails']);
        Route::post('/{id}/purchase', [TemplateMarketplaceController::class, 'purchaseTemplate']);
        Route::get('/user/purchased', [TemplateMarketplaceController::class, 'getUserTemplates']);
        Route::post('/upload', [TemplateMarketplaceController::class, 'uploadTemplate']);
        Route::post('/{id}/rate', [TemplateMarketplaceController::class, 'rateTemplate']);
    });
    
    // Gamification routes
    Route::prefix('gamification')->group(function () {
        Route::get('/achievements', [GamificationController::class, 'getAchievements']);
        Route::get('/progress', [GamificationController::class, 'getProgress']);
        Route::get('/leaderboard', [GamificationController::class, 'getLeaderboard']);
        Route::get('/badges', [GamificationController::class, 'getBadges']);
        Route::get('/challenges', [GamificationController::class, 'getChallenges']);
        Route::get('/rewards', [GamificationController::class, 'getRewards']);
        Route::post('/rewards/{id}/redeem', [GamificationController::class, 'redeemReward']);
    });
    
    // Website Builder routes
    Route::prefix('websites')->group(function () {
        Route::get('/', [WebsiteBuilderController::class, 'index']);
        Route::post('/', [WebsiteBuilderController::class, 'store']);
        Route::get('/templates', [WebsiteBuilderController::class, 'getTemplates']);
        Route::get('/components', [WebsiteBuilderController::class, 'getComponents']);
        Route::get('/{id}', [WebsiteBuilderController::class, 'show']);
        Route::put('/{id}', [WebsiteBuilderController::class, 'update']);
        Route::delete('/{id}', [WebsiteBuilderController::class, 'destroy']);
        Route::put('/{id}/publish', [WebsiteBuilderController::class, 'publish']);
        Route::get('/{id}/analytics', [WebsiteBuilderController::class, 'getAnalytics']);
        
        // Page management routes
        Route::post('/{id}/pages', [WebsiteBuilderController::class, 'createPage']);
        Route::put('/{id}/pages/{pageId}', [WebsiteBuilderController::class, 'updatePage']);
        Route::delete('/{id}/pages/{pageId}', [WebsiteBuilderController::class, 'deletePage']);
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

    // Admin Dashboard routes (MOVED INSIDE MIDDLEWARE GROUP)
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
        
        // API Key Management routes
        Route::get('/api-keys', [AdminController::class, 'getApiKeys']);
        Route::post('/api-keys', [AdminController::class, 'saveApiKey']);
        Route::post('/api-keys/test', [AdminController::class, 'testApiKey']);
        Route::delete('/api-keys/{id}', [AdminController::class, 'deleteApiKey']);
        
        // Subscription Plan Management routes
        Route::get('/subscription-plans', [AdminController::class, 'getSubscriptionPlans']);
        Route::post('/subscription-plans', [AdminController::class, 'saveSubscriptionPlan']);
        
        // System Settings routes
        Route::get('/settings', [AdminController::class, 'getSettings']);
        Route::post('/settings', [AdminController::class, 'updateSettings']);
    });

    // Link in Bio Builder routes
    Route::prefix('link-in-bio')->group(function () {
        Route::get('/sites', [LinkInBioController::class, 'getBioSites']);
        Route::post('/sites', [LinkInBioController::class, 'createBioSite']);
        Route::get('/sites/{id}', [LinkInBioController::class, 'getBioSiteBuilder']);
        Route::put('/sites/{id}', [LinkInBioController::class, 'saveBioSite']);
        Route::get('/sites/{id}/analytics', [LinkInBioController::class, 'getBioSiteAnalytics']);
        Route::post('/sites/{id}/ab-test', [LinkInBioController::class, 'abTestBioSite']);
        Route::get('/templates', [LinkInBioController::class, 'getTemplates']);
        Route::get('/components', [LinkInBioController::class, 'getComponents']);
    });
    
    // Visual Bio Builder routes (New Drag & Drop Builder)
    Route::prefix('visual-bio-builder')->group(function () {
        Route::get('/components', [VisualBioBuilderController::class, 'getComponents']);
        Route::get('/templates', [VisualBioBuilderController::class, 'getTemplates']);
        Route::get('/sites/{id}/builder', [VisualBioBuilderController::class, 'getBioSiteBuilder']);
        Route::post('/sites/{id}/save', [VisualBioBuilderController::class, 'saveBioSite']);
        Route::post('/sites/{id}/template', [VisualBioBuilderController::class, 'applyTemplate']);
        Route::post('/sites/{id}/upload', [VisualBioBuilderController::class, 'uploadMedia']);
        Route::get('/sites/{slug}/preview', [VisualBioBuilderController::class, 'previewBioSite']);
    });

    // Unified Data & Cross-Platform Integration routes
    Route::prefix('unified')->group(function () {
        // Unified customer journey
        Route::get('/customer-journey/{customerId}', [UnifiedDataController::class, 'getUnifiedCustomerJourney']);
        
        // Cross-platform analytics
        Route::get('/analytics', [UnifiedDataController::class, 'getCrossPlatformAnalytics']);
        
        // Intelligent automation recommendations
        Route::get('/automation-recommendations', [UnifiedDataController::class, 'getIntelligentAutomationRecommendations']);
        
        // Cross-platform campaign execution
        Route::post('/campaigns', [UnifiedDataController::class, 'executeCrossPlatformCampaign']);
        
        // Unified insights
        Route::get('/insights', [UnifiedDataController::class, 'getUnifiedInsights']);
        
        // Data synchronization
        Route::post('/sync', [UnifiedDataController::class, 'synchronizeAllData']);
        
        // Lead routing
        Route::post('/lead-routing', [UnifiedDataController::class, 'performIntelligentLeadRouting']);
        
        // Performance optimization
        Route::post('/optimize', [UnifiedDataController::class, 'optimizeCrossPlatformPerformance']);
        
        // Attribution analysis
        Route::get('/attribution/{timeRange?}', [UnifiedDataController::class, 'getAttributionAnalysis']);
        
        // Customer 360 view
        Route::get('/customer-360/{customerId}', [UnifiedDataController::class, 'getCustomer360View']);
        
        // Revenue attribution
        Route::get('/revenue-attribution', [UnifiedDataController::class, 'getRevenueAttribution']);
        
        // Platform health check
        Route::get('/platform-health', [UnifiedDataController::class, 'getPlatformHealthCheck']);
        
        // Integration opportunities
        Route::get('/integration-opportunities', [UnifiedDataController::class, 'getIntegrationOpportunities']);
        
        // Advanced automation workflows
        Route::post('/automation-workflows', [UnifiedDataController::class, 'createAdvancedAutomationWorkflows']);
        
        // Predictive modeling
        Route::get('/predictive-models', [UnifiedDataController::class, 'getPredictiveModels']);
        
        // Customer segment analysis
        Route::get('/segment-analysis', [UnifiedDataController::class, 'getCustomerSegmentAnalysis']);
        
        // Engagement scoring
        Route::get('/engagement-scoring', [UnifiedDataController::class, 'getEngagementScoring']);
        
        // Conversion optimization
        Route::get('/conversion-optimization', [UnifiedDataController::class, 'getConversionOptimization']);
        
        // Platform recommendations
        Route::get('/platform-recommendations', [UnifiedDataController::class, 'getPlatformRecommendations']);
        
        // Business intelligence
        Route::get('/business-intelligence', [UnifiedDataController::class, 'getBusinessIntelligence']);
    });

});

// Stripe Payment routes - public access for webhooks
Route::prefix('payments')->group(function () {
    Route::get('/packages', [StripePaymentController::class, 'getPackages']);
    Route::post('/checkout/session', [StripePaymentController::class, 'createCheckoutSession']);
    Route::get('/checkout/status/{sessionId}', [StripePaymentController::class, 'getCheckoutStatus']);
});

// Stripe routes aliases (for frontend compatibility)
Route::prefix('stripe')->group(function () {
    Route::get('/packages', [StripePaymentController::class, 'getPackages']);
    Route::post('/checkout/session', [StripePaymentController::class, 'createCheckoutSession']);
    Route::get('/checkout/status/{sessionId}', [StripePaymentController::class, 'getCheckoutStatus']);
});

// Stripe webhook - must be outside auth middleware
Route::post('/webhook/stripe', [StripePaymentController::class, 'handleWebhook']);

// Biometric Authentication routes
Route::prefix('biometric')->group(function () {
    Route::post('/registration-options', [BiometricAuthController::class, 'getRegistrationOptions'])->middleware(\App\Http\Middleware\CustomSanctumAuth::class);
    Route::post('/register', [BiometricAuthController::class, 'register'])->middleware(\App\Http\Middleware\CustomSanctumAuth::class);
    Route::post('/authentication-options', [BiometricAuthController::class, 'getAuthenticationOptions']);
    Route::post('/authenticate', [BiometricAuthController::class, 'authenticate']);
    Route::get('/credentials', [BiometricAuthController::class, 'getUserCredentials'])->middleware(\App\Http\Middleware\CustomSanctumAuth::class);
    Route::delete('/credentials/{credentialId}', [BiometricAuthController::class, 'revoke'])->middleware(\App\Http\Middleware\CustomSanctumAuth::class);
});

// Real-time features
Route::middleware(\App\Http\Middleware\CustomSanctumAuth::class)->prefix('realtime')->group(function () {
    Route::get('/notifications', [RealTimeController::class, 'getNotifications']);
    Route::post('/notifications/{notificationId}/read', [RealTimeController::class, 'markAsRead']);
    Route::get('/activity-feed', [RealTimeController::class, 'getActivityFeed']);
    Route::get('/system-status', [RealTimeController::class, 'getSystemStatus']);
    Route::get('/user-presence', [RealTimeController::class, 'getUserPresence']);
    Route::post('/messages', [RealTimeController::class, 'sendMessage']);
    Route::get('/workspace-metrics', [RealTimeController::class, 'getWorkspaceMetrics']);
});

// Escrow & Transaction Security
Route::middleware(\App\Http\Middleware\CustomSanctumAuth::class)->prefix('escrow')->group(function () {
    Route::get('/', [EscrowController::class, 'index']);
    Route::post('/', [EscrowController::class, 'store']);
    Route::get('/{id}', [EscrowController::class, 'show']);
    Route::post('/{id}/fund', [EscrowController::class, 'fundTransaction']);
    Route::post('/{id}/deliver', [EscrowController::class, 'deliverItem']);
    Route::post('/{id}/accept', [EscrowController::class, 'acceptDelivery']);
    Route::post('/{id}/dispute', [EscrowController::class, 'createDispute']);
    Route::get('/statistics/overview', [EscrowController::class, 'getStatistics']);
});

// Advanced Analytics & Business Intelligence
Route::middleware(\App\Http\Middleware\CustomSanctumAuth::class)->prefix('analytics')->group(function () {
    Route::get('/business-intelligence', [AdvancedAnalyticsController::class, 'getBusinessIntelligence']);
    Route::get('/realtime-metrics', [AdvancedAnalyticsController::class, 'getRealtimeMetrics']);
    Route::get('/cohort-analysis', [AdvancedAnalyticsController::class, 'getCohortAnalysis']);
    Route::get('/funnel-analysis', [AdvancedAnalyticsController::class, 'getFunnelAnalysis']);
    Route::get('/ab-test-results', [AdvancedAnalyticsController::class, 'getABTestResults']);
    Route::post('/custom-report', [AdvancedAnalyticsController::class, 'generateCustomReport']);
    Route::get('/predictive-analytics', [AdvancedAnalyticsController::class, 'getPredictiveAnalytics']);
});

// Advanced Booking System
Route::middleware(\App\Http\Middleware\CustomSanctumAuth::class)->prefix('booking')->group(function () {
    Route::get('/services', [AdvancedBookingController::class, 'getServices']);
    Route::post('/services', [AdvancedBookingController::class, 'createService']);
    Route::get('/services/{serviceId}/available-slots', [AdvancedBookingController::class, 'getAvailableSlots']);
    Route::post('/appointments', [AdvancedBookingController::class, 'createAppointment']);
    Route::get('/appointments', [AdvancedBookingController::class, 'getAppointments']);
    Route::put('/appointments/{appointmentId}/status', [AdvancedBookingController::class, 'updateAppointmentStatus']);
    Route::post('/services/{serviceId}/availability', [AdvancedBookingController::class, 'setAvailability']);
    Route::get('/analytics', [AdvancedBookingController::class, 'getBookingAnalytics']);
});

// Advanced Financial Management
Route::middleware(\App\Http\Middleware\CustomSanctumAuth::class)->prefix('financial')->group(function () {
    Route::get('/dashboard', [AdvancedFinancialController::class, 'getFinancialDashboard']);
    Route::post('/invoices', [AdvancedFinancialController::class, 'createInvoice']);
    Route::get('/invoices', [AdvancedFinancialController::class, 'getInvoices']);
    Route::post('/invoices/{invoiceId}/send', [AdvancedFinancialController::class, 'sendInvoice']);
    Route::post('/invoices/{invoiceId}/payment', [AdvancedFinancialController::class, 'recordPayment']);
    Route::post('/tax/calculate', [AdvancedFinancialController::class, 'calculateTax']);
    Route::get('/reports', [AdvancedFinancialController::class, 'getFinancialReports']);
    Route::get('/payment-analytics', [AdvancedFinancialController::class, 'getPaymentMethodAnalytics']);
});

// Enhanced AI Features
Route::middleware(\App\Http\Middleware\CustomSanctumAuth::class)->prefix('ai')->group(function () {
    Route::post('/content/generate', [EnhancedAIController::class, 'generateContentSuggestions']);
    Route::post('/content/seo-optimize', [EnhancedAIController::class, 'optimizeContentForSEO']);
    Route::post('/competitors/analyze', [EnhancedAIController::class, 'analyzeCompetitors']);
    Route::post('/insights/business', [EnhancedAIController::class, 'generateBusinessInsights']);
    Route::post('/sentiment/analyze', [EnhancedAIController::class, 'analyzeSentiment']);
    Route::post('/pricing/optimize', [EnhancedAIController::class, 'optimizePricing']);
    Route::post('/leads/score', [EnhancedAIController::class, 'scoreLeads']);
    Route::post('/chatbot/respond', [EnhancedAIController::class, 'generateChatbotResponse']);
    Route::post('/trends/predict', [EnhancedAIController::class, 'predictTrends']);
});

// Legacy route for compatibility
Route::middleware(\App\Http\Middleware\CustomSanctumAuth::class)->get('/user', function (Request $request) {
    return $request->user();
});

// Link Shortener System
Route::middleware(\App\Http\Middleware\CustomSanctumAuth::class)->prefix('links')->group(function () {
    Route::get('/', [App\Http\Controllers\Api\LinkShortenerController::class, 'index']);
    Route::post('/', [App\Http\Controllers\Api\LinkShortenerController::class, 'create']);
    Route::get('/{id}/analytics', [App\Http\Controllers\Api\LinkShortenerController::class, 'analytics']);
    Route::put('/{id}', [App\Http\Controllers\Api\LinkShortenerController::class, 'update']);
    Route::delete('/{id}', [App\Http\Controllers\Api\LinkShortenerController::class, 'delete']);
    Route::get('/bulk-analytics', [App\Http\Controllers\Api\LinkShortenerController::class, 'bulkAnalytics']);
});

// Public link redirect (no authentication required)
Route::get('/l/{slug}', [App\Http\Controllers\Api\LinkShortenerController::class, 'redirect']);
Route::post('/l/{slug}', [App\Http\Controllers\Api\LinkShortenerController::class, 'redirect']);

// Referral System
Route::middleware(\App\Http\Middleware\CustomSanctumAuth::class)->prefix('referrals')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Api\ReferralController::class, 'dashboard']);
    Route::post('/invitations', [App\Http\Controllers\Api\ReferralController::class, 'sendInvitations']);
    Route::get('/analytics', [App\Http\Controllers\Api\ReferralController::class, 'analytics']);
    Route::get('/rewards', [App\Http\Controllers\Api\ReferralController::class, 'rewards']);
    Route::post('/process', [App\Http\Controllers\Api\ReferralController::class, 'processReferral']);
    Route::post('/complete', [App\Http\Controllers\Api\ReferralController::class, 'completeReferral']);
});

// Template Marketplace
Route::middleware(\App\Http\Middleware\CustomSanctumAuth::class)->prefix('templates')->group(function () {
    Route::get('/', [App\Http\Controllers\Api\TemplateMarketplaceController::class, 'index']);
    Route::post('/', [App\Http\Controllers\Api\TemplateMarketplaceController::class, 'store']);
    Route::get('/categories', [App\Http\Controllers\Api\TemplateMarketplaceController::class, 'categories']);
    Route::get('/featured', [App\Http\Controllers\Api\TemplateMarketplaceController::class, 'featured']);
    Route::get('/my-templates', [App\Http\Controllers\Api\TemplateMarketplaceController::class, 'myTemplates']);
    Route::get('/my-purchases', [App\Http\Controllers\Api\TemplateMarketplaceController::class, 'myPurchases']);
    Route::get('/{id}', [App\Http\Controllers\Api\TemplateMarketplaceController::class, 'show']);
    Route::put('/{id}', [App\Http\Controllers\Api\TemplateMarketplaceController::class, 'update']);
    Route::delete('/{id}', [App\Http\Controllers\Api\TemplateMarketplaceController::class, 'destroy']);
    Route::post('/{id}/purchase', [App\Http\Controllers\Api\TemplateMarketplaceController::class, 'purchase']);
    Route::get('/{id}/download', [App\Http\Controllers\Api\TemplateMarketplaceController::class, 'download']);
    Route::post('/{id}/review', [App\Http\Controllers\Api\TemplateMarketplaceController::class, 'addReview']);
});

// WebSocket Collaboration routes
Route::middleware(\App\Http\Middleware\CustomSanctumAuth::class)->group(function () {
    Route::prefix('websocket')->group(function () {
        // Workspace presence management
        Route::post('/join-workspace', [WebSocketController::class, 'joinWorkspace']);
        Route::post('/leave-workspace', [WebSocketController::class, 'leaveWorkspace']);
        Route::get('/workspace-users/{workspaceId}', [WebSocketController::class, 'getWorkspaceUsers']);
        
        // Real-time cursor tracking
        Route::post('/update-cursor', [WebSocketController::class, 'updateCursor']);
        
        // Real-time document collaboration
        Route::post('/update-document', [WebSocketController::class, 'updateDocument']);
        
        // Real-time notifications
        Route::post('/send-notification', [WebSocketController::class, 'sendNotification']);
        
        // Activity feed
        Route::get('/activity-feed', [WebSocketController::class, 'getActivityFeed']);
        
        // Collaborative sessions
        Route::post('/start-session', [WebSocketController::class, 'startSession']);
        Route::post('/join-session', [WebSocketController::class, 'joinSession']);
        Route::post('/end-session', [WebSocketController::class, 'endSession']);
    });
});

// Include Phase 1 Foundation Features
require __DIR__ . '/api_phase1.php';

// Include Phase 2 Professional Features
require __DIR__ . '/api_phase2.php';

// Include Phase 3 Scale Features
require __DIR__ . '/api_phase3.php';

// Include Phase 4 Innovation Features
require __DIR__ . '/api_phase4.php';

// Fallback route for 404 errors
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found'
    ], 404);
});