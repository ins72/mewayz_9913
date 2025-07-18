<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OnboardingController;
use App\Http\Controllers\Api\ThemeController;
use App\Http\Controllers\Api\MobileController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\PushNotificationController;

/*
|--------------------------------------------------------------------------
| Phase 1 API Routes - Foundation Features
|--------------------------------------------------------------------------
|
| These routes implement Phase 1 foundation features:
| - Enhanced onboarding experience
| - Light/Dark mode implementation
| - Mobile gesture navigation
| - Push notification system
| - Basic analytics improvements
|
*/

Route::middleware(\App\Http\Middleware\CustomSanctumAuth::class)->group(function () {
    
    // ================================
    // ENHANCED ONBOARDING SYSTEM
    // ================================
    
    Route::prefix('onboarding')->group(function () {
        Route::get('/progress', [OnboardingController::class, 'getProgress']);
        Route::post('/progress', [OnboardingController::class, 'updateProgress']);
        Route::get('/demo', [OnboardingController::class, 'getInteractiveDemo']);
        Route::get('/recommendations', [OnboardingController::class, 'getRecommendations']);
        Route::post('/step/complete', [OnboardingController::class, 'completeStep']);
        Route::post('/skip', [OnboardingController::class, 'skipOnboarding']);
        Route::post('/restart', [OnboardingController::class, 'restartOnboarding']);
    });
    
    // ================================
    // THEME & PERSONALIZATION SYSTEM
    // ================================
    
    Route::prefix('theme')->group(function () {
        Route::get('/', [ThemeController::class, 'getTheme']);
        Route::post('/update', [ThemeController::class, 'updateTheme']);
        Route::get('/system', [ThemeController::class, 'getSystemTheme']);
        Route::post('/custom-colors', [ThemeController::class, 'updateCustomColors']);
        Route::get('/presets', [ThemeController::class, 'getThemePresets']);
        Route::post('/export', [ThemeController::class, 'exportTheme']);
        Route::post('/import', [ThemeController::class, 'importTheme']);
    });
    
    // ================================
    // MOBILE & PWA SYSTEM
    // ================================
    
    Route::prefix('mobile')->group(function () {
        Route::get('/config', [MobileController::class, 'getConfig']);
        Route::post('/preferences', [MobileController::class, 'updatePreferences']);
        Route::post('/session', [MobileController::class, 'registerSession']);
        Route::get('/manifest', [MobileController::class, 'getManifest']);
        Route::get('/service-worker', [MobileController::class, 'getServiceWorker']);
        Route::post('/install', [MobileController::class, 'trackInstall']);
        Route::post('/offline-sync', [MobileController::class, 'handleOfflineSync']);
    });
    
    // ================================
    // PUSH NOTIFICATION SYSTEM
    // ================================
    
    Route::prefix('push-notifications')->group(function () {
        Route::post('/subscribe', [MobileController::class, 'subscribePushNotifications']);
        Route::post('/unsubscribe', [MobileController::class, 'unsubscribePushNotifications']);
        Route::post('/test', [MobileController::class, 'testPushNotification']);
        Route::get('/subscriptions', [MobileController::class, 'getSubscriptions']);
        Route::post('/update-preferences', [MobileController::class, 'updateNotificationPreferences']);
        Route::get('/types', [MobileController::class, 'getNotificationTypes']);
    });
    
    // ================================
    // ENHANCED ANALYTICS SYSTEM
    // ================================
    
    Route::prefix('analytics')->group(function () {
        // Basic Analytics
        Route::get('/dashboard', [AnalyticsController::class, 'getDashboard']);
        Route::get('/overview', [AnalyticsController::class, 'getOverview']);
        Route::get('/real-time', [AnalyticsController::class, 'getRealTime']);
        Route::post('/track', [AnalyticsController::class, 'trackEvent']);
        
        // Gamification Analytics
        Route::get('/gamification', [AnalyticsController::class, 'getGamification']);
        Route::get('/achievements', [AnalyticsController::class, 'getAchievements']);
        Route::get('/leaderboard', [AnalyticsController::class, 'getLeaderboard']);
        Route::get('/progress', [AnalyticsController::class, 'getProgress']);
        Route::get('/challenges', [AnalyticsController::class, 'getChallenges']);
        
        // Custom Reports
        Route::get('/reports', [AnalyticsController::class, 'getReports']);
        Route::post('/reports', [AnalyticsController::class, 'createReport']);
        Route::get('/reports/{id}', [AnalyticsController::class, 'getReport']);
        Route::post('/reports/{id}/schedule', [AnalyticsController::class, 'scheduleReport']);
        Route::post('/reports/{id}/export', [AnalyticsController::class, 'exportReport']);
        
        // Advanced Analytics
        Route::get('/advanced/cohort', [AnalyticsController::class, 'getCohortAnalysis']);
        Route::get('/advanced/funnel', [AnalyticsController::class, 'getFunnelAnalysis']);
        Route::get('/advanced/retention', [AnalyticsController::class, 'getRetentionAnalysis']);
        Route::get('/advanced/segmentation', [AnalyticsController::class, 'getSegmentationAnalysis']);
        Route::get('/advanced/predictive', [AnalyticsController::class, 'getPredictiveInsights']);
    });
    
    // ================================
    // USER PREFERENCES SYSTEM
    // ================================
    
    // Route::prefix('preferences')->group(function () {
    //     Route::get('/', [UserPreferencesController::class, 'getPreferences']);
    //     Route::post('/update', [UserPreferencesController::class, 'updatePreferences']);
    //     Route::post('/reset', [UserPreferencesController::class, 'resetPreferences']);
    //     Route::get('/export', [UserPreferencesController::class, 'exportPreferences']);
    //     Route::post('/import', [UserPreferencesController::class, 'importPreferences']);
    // });
    
    // ================================
    // ACCESSIBILITY FEATURES
    // ================================
    
    // Route::prefix('accessibility')->group(function () {
    //     Route::get('/options', [AccessibilityController::class, 'getOptions']);
    //     Route::post('/update', [AccessibilityController::class, 'updateOptions']);
    //     Route::get('/voice-commands', [AccessibilityController::class, 'getVoiceCommands']);
    //     Route::post('/voice-commands', [AccessibilityController::class, 'updateVoiceCommands']);
    //     Route::get('/keyboard-shortcuts', [AccessibilityController::class, 'getKeyboardShortcuts']);
    //     Route::post('/keyboard-shortcuts', [AccessibilityController::class, 'updateKeyboardShortcuts']);
    // });
    
    // ================================
    // PERFORMANCE MONITORING
    // ================================
    
    Route::prefix('performance')->group(function () {
        Route::post('/metrics', [PerformanceController::class, 'recordMetrics']);
        Route::get('/vitals', [PerformanceController::class, 'getVitals']);
        Route::get('/optimization', [PerformanceController::class, 'getOptimizations']);
        Route::post('/report-issue', [PerformanceController::class, 'reportIssue']);
    });
    
    // ================================
    // FEATURE FLAGS & A/B TESTING
    // ================================
    
    Route::prefix('features')->group(function () {
        Route::get('/flags', [FeatureFlagsController::class, 'getFlags']);
        Route::post('/flags/{flag}/toggle', [FeatureFlagsController::class, 'toggleFlag']);
        Route::get('/experiments', [FeatureFlagsController::class, 'getExperiments']);
        Route::post('/experiments/{experiment}/participate', [FeatureFlagsController::class, 'participateInExperiment']);
    });
    
    // ================================
    // HELP & GUIDANCE SYSTEM
    // ================================
    
    Route::prefix('help')->group(function () {
        Route::get('/tours', [HelpController::class, 'getTours']);
        Route::post('/tours/{tour}/start', [HelpController::class, 'startTour']);
        Route::post('/tours/{tour}/complete', [HelpController::class, 'completeTour']);
        Route::get('/tooltips', [HelpController::class, 'getTooltips']);
        Route::post('/tooltips/{tooltip}/dismiss', [HelpController::class, 'dismissTooltip']);
        Route::get('/contextual-help', [HelpController::class, 'getContextualHelp']);
    });
    
    // ================================
    // FEEDBACK & SATISFACTION
    // ================================
    
    Route::prefix('feedback')->group(function () {
        Route::post('/nps', [FeedbackController::class, 'submitNPS']);
        Route::post('/satisfaction', [FeedbackController::class, 'submitSatisfaction']);
        Route::post('/feature-request', [FeedbackController::class, 'submitFeatureRequest']);
        Route::post('/bug-report', [FeedbackController::class, 'submitBugReport']);
        Route::get('/surveys', [FeedbackController::class, 'getSurveys']);
        Route::post('/surveys/{survey}/respond', [FeedbackController::class, 'respondToSurvey']);
    });
    
    // ================================
    // SMART RECOMMENDATIONS
    // ================================
    
    Route::prefix('recommendations')->group(function () {
        Route::get('/dashboard', [RecommendationsController::class, 'getDashboardRecommendations']);
        Route::get('/features', [RecommendationsController::class, 'getFeatureRecommendations']);
        Route::get('/content', [RecommendationsController::class, 'getContentRecommendations']);
        Route::get('/optimization', [RecommendationsController::class, 'getOptimizationRecommendations']);
        Route::post('/dismiss', [RecommendationsController::class, 'dismissRecommendation']);
        Route::post('/accept', [RecommendationsController::class, 'acceptRecommendation']);
    });
    
    // ================================
    // QUICK ACTIONS & SHORTCUTS
    // ================================
    
    Route::prefix('quick-actions')->group(function () {
        Route::get('/', [QuickActionsController::class, 'getQuickActions']);
        Route::post('/create', [QuickActionsController::class, 'createQuickAction']);
        Route::post('/execute', [QuickActionsController::class, 'executeQuickAction']);
        Route::post('/customize', [QuickActionsController::class, 'customizeQuickActions']);
        Route::get('/shortcuts', [QuickActionsController::class, 'getKeyboardShortcuts']);
    });
    
    // ================================
    // WORKSPACE PERSONALIZATION
    // ================================
    
    Route::prefix('workspace-personalization')->group(function () {
        Route::get('/layout', [WorkspacePersonalizationController::class, 'getLayout']);
        Route::post('/layout', [WorkspacePersonalizationController::class, 'updateLayout']);
        Route::get('/widgets', [WorkspacePersonalizationController::class, 'getWidgets']);
        Route::post('/widgets', [WorkspacePersonalizationController::class, 'updateWidgets']);
        Route::get('/custom-views', [WorkspacePersonalizationController::class, 'getCustomViews']);
        Route::post('/custom-views', [WorkspacePersonalizationController::class, 'createCustomView']);
    });
    
    // ================================
    // COLLABORATION FEATURES
    // ================================
    
    Route::prefix('collaboration')->group(function () {
        Route::get('/presence', [CollaborationController::class, 'getPresence']);
        Route::post('/presence', [CollaborationController::class, 'updatePresence']);
        Route::get('/activity-feed', [CollaborationController::class, 'getActivityFeed']);
        Route::post('/comments', [CollaborationController::class, 'createComment']);
        Route::get('/comments', [CollaborationController::class, 'getComments']);
        Route::post('/mentions', [CollaborationController::class, 'createMention']);
    });
    
    // ================================
    // AUTOMATION & WORKFLOWS
    // ================================
    
    Route::prefix('automation')->group(function () {
        Route::get('/workflows', [AutomationController::class, 'getWorkflows']);
        Route::post('/workflows', [AutomationController::class, 'createWorkflow']);
        Route::get('/workflows/{workflow}', [AutomationController::class, 'getWorkflow']);
        Route::post('/workflows/{workflow}/execute', [AutomationController::class, 'executeWorkflow']);
        Route::get('/triggers', [AutomationController::class, 'getTriggers']);
        Route::get('/actions', [AutomationController::class, 'getActions']);
    });
    
    // ================================
    // INTEGRATIONS MARKETPLACE
    // ================================
    
    Route::prefix('integrations')->group(function () {
        Route::get('/marketplace', [IntegrationsController::class, 'getMarketplace']);
        Route::get('/installed', [IntegrationsController::class, 'getInstalled']);
        Route::post('/install', [IntegrationsController::class, 'installIntegration']);
        Route::post('/uninstall', [IntegrationsController::class, 'uninstallIntegration']);
        Route::post('/configure', [IntegrationsController::class, 'configureIntegration']);
        Route::get('/webhooks', [IntegrationsController::class, 'getWebhooks']);
        Route::post('/webhooks', [IntegrationsController::class, 'createWebhook']);
    });
    
    // ================================
    // ADVANCED SEARCH & FILTERS
    // ================================
    
    Route::prefix('search')->group(function () {
        Route::get('/global', [SearchController::class, 'globalSearch']);
        Route::get('/suggestions', [SearchController::class, 'getSuggestions']);
        Route::post('/save-search', [SearchController::class, 'saveSearch']);
        Route::get('/saved-searches', [SearchController::class, 'getSavedSearches']);
        Route::get('/filters', [SearchController::class, 'getFilters']);
        Route::post('/filters', [SearchController::class, 'createFilter']);
    });
    
    // ================================
    // EXPORT & IMPORT SYSTEM
    // ================================
    
    Route::prefix('data-management')->group(function () {
        Route::get('/export-options', [DataManagementController::class, 'getExportOptions']);
        Route::post('/export', [DataManagementController::class, 'exportData']);
        Route::get('/import-options', [DataManagementController::class, 'getImportOptions']);
        Route::post('/import', [DataManagementController::class, 'importData']);
        Route::get('/export-history', [DataManagementController::class, 'getExportHistory']);
        Route::get('/import-history', [DataManagementController::class, 'getImportHistory']);
    });
    
    // ================================
    // BACKUP & RESTORE SYSTEM
    // ================================
    
    Route::prefix('backup')->group(function () {
        Route::get('/status', [BackupController::class, 'getStatus']);
        Route::post('/create', [BackupController::class, 'createBackup']);
        Route::get('/list', [BackupController::class, 'listBackups']);
        Route::post('/restore', [BackupController::class, 'restoreBackup']);
        Route::get('/schedule', [BackupController::class, 'getSchedule']);
        Route::post('/schedule', [BackupController::class, 'updateSchedule']);
    });
    
    // ================================
    // SYSTEM HEALTH & MONITORING
    // ================================
    
    Route::prefix('system')->group(function () {
        Route::get('/health', [SystemController::class, 'getHealth']);
        Route::get('/status', [SystemController::class, 'getStatus']);
        Route::get('/metrics', [SystemController::class, 'getMetrics']);
        Route::post('/diagnostic', [SystemController::class, 'runDiagnostic']);
        Route::get('/logs', [SystemController::class, 'getLogs']);
        Route::post('/clear-cache', [SystemController::class, 'clearCache']);
    });
    
});

// ================================
// PUBLIC ROUTES (NO AUTH REQUIRED)
// ================================

Route::prefix('public')->group(function () {
    Route::get('/system-status', [PublicController::class, 'getSystemStatus']);
    Route::get('/pwa-manifest', [PublicController::class, 'getPWAManifest']);
    Route::get('/service-worker', [PublicController::class, 'getServiceWorker']);
    Route::get('/feature-flags', [PublicController::class, 'getPublicFeatureFlags']);
});