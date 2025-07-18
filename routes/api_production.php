<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SubscriptionManagementController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\AccountManagementController;
use App\Http\Controllers\Api\AffiliateController;
use App\Http\Controllers\Admin\AdvancedAdminController;

/*
|--------------------------------------------------------------------------
| Production-Ready API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public Routes
Route::prefix('api')->group(function () {
    // Subscription Plans (Public)
    Route::get('/subscription-plans', [SubscriptionManagementController::class, 'getPlans']);
    
    // Affiliate Tracking
    Route::get('/affiliate/{referralCode}', [AffiliateController::class, 'trackClick'])->name('affiliate.track');
    
    // Payment Webhooks
    Route::post('/webhooks/stripe', [PaymentController::class, 'handleWebhook']);
});

// Protected Routes (Require Authentication)
Route::middleware('auth:sanctum')->prefix('api')->group(function () {
    
    // Subscription Management
    Route::prefix('subscriptions')->group(function () {
        Route::get('/current', [SubscriptionManagementController::class, 'getCurrentSubscription']);
        Route::post('/subscribe', [SubscriptionManagementController::class, 'subscribe']);
        Route::post('/change-plan', [SubscriptionManagementController::class, 'changePlan']);
        Route::post('/cancel', [SubscriptionManagementController::class, 'cancelSubscription']);
        Route::post('/reactivate', [SubscriptionManagementController::class, 'reactivateSubscription']);
        Route::get('/history', [SubscriptionManagementController::class, 'getSubscriptionHistory']);
        Route::get('/invoices', [SubscriptionManagementController::class, 'getInvoices']);
        Route::get('/invoices/{invoiceId}/download', [SubscriptionManagementController::class, 'downloadInvoice']);
    });

    // Payment Management
    Route::prefix('payments')->group(function () {
        Route::get('/methods', [PaymentController::class, 'getPaymentMethods']);
        Route::post('/methods', [PaymentController::class, 'addPaymentMethod']);
        Route::delete('/methods/{paymentMethodId}', [PaymentController::class, 'removePaymentMethod']);
        Route::post('/methods/{paymentMethodId}/default', [PaymentController::class, 'setDefaultPaymentMethod']);
        Route::post('/process', [PaymentController::class, 'processPayment']);
        Route::get('/history', [PaymentController::class, 'getPaymentHistory']);
    });

    // Account Management
    Route::prefix('account')->group(function () {
        Route::get('/overview', [AccountManagementController::class, 'getAccountOverview']);
        Route::put('/update', [AccountManagementController::class, 'updateAccount']);
        Route::post('/change-password', [AccountManagementController::class, 'changePassword']);
        Route::post('/enable-2fa', [AccountManagementController::class, 'enableTwoFactor']);
        Route::post('/disable-2fa', [AccountManagementController::class, 'disableTwoFactor']);
        Route::post('/request-deletion', [AccountManagementController::class, 'requestAccountDeletion']);
        Route::post('/cancel-deletion', [AccountManagementController::class, 'cancelAccountDeletion']);
        Route::post('/export-data', [AccountManagementController::class, 'requestDataExport']);
        Route::get('/sessions', [AccountManagementController::class, 'getActiveSessions']);
        Route::delete('/sessions/{sessionId}', [AccountManagementController::class, 'revokeSession']);
    });

    // Affiliate System
    Route::prefix('affiliate')->group(function () {
        Route::get('/dashboard', [AffiliateController::class, 'getDashboard']);
        Route::post('/apply', [AffiliateController::class, 'applyToBecome']);
        Route::get('/links', [AffiliateController::class, 'getLinks']);
        Route::post('/links', [AffiliateController::class, 'createLink']);
        Route::get('/referrals', [AffiliateController::class, 'getReferrals']);
        Route::get('/commissions', [AffiliateController::class, 'getCommissions']);
        Route::get('/payments', [AffiliateController::class, 'getPayments']);
        Route::post('/request-payout', [AffiliateController::class, 'requestPayout']);
        Route::get('/marketing-materials', [AffiliateController::class, 'getMarketingMaterials']);
    });

    // Integration Management
    Route::prefix('integrations')->group(function () {
        Route::get('/', [IntegrationController::class, 'getAvailableIntegrations']);
        Route::get('/connected', [IntegrationController::class, 'getConnectedIntegrations']);
        Route::post('/connect', [IntegrationController::class, 'connectIntegration']);
        Route::post('/disconnect/{integrationId}', [IntegrationController::class, 'disconnectIntegration']);
        Route::post('/sync/{integrationId}', [IntegrationController::class, 'syncIntegration']);
        Route::get('/sync-status/{integrationId}', [IntegrationController::class, 'getSyncStatus']);
    });

    // Analytics & Insights
    Route::prefix('analytics')->group(function () {
        Route::get('/dashboard', [AnalyticsController::class, 'getDashboard']);
        Route::get('/revenue', [AnalyticsController::class, 'getRevenueAnalytics']);
        Route::get('/usage', [AnalyticsController::class, 'getUsageAnalytics']);
        Route::get('/growth', [AnalyticsController::class, 'getGrowthAnalytics']);
        Route::get('/export', [AnalyticsController::class, 'exportData']);
    });

    // Support System
    Route::prefix('support')->group(function () {
        Route::get('/tickets', [SupportController::class, 'getTickets']);
        Route::post('/tickets', [SupportController::class, 'createTicket']);
        Route::get('/tickets/{ticketId}', [SupportController::class, 'getTicket']);
        Route::post('/tickets/{ticketId}/reply', [SupportController::class, 'replyToTicket']);
        Route::post('/tickets/{ticketId}/close', [SupportController::class, 'closeTicket']);
        Route::post('/tickets/{ticketId}/rate', [SupportController::class, 'rateTicket']);
        Route::get('/kb', [SupportController::class, 'getKnowledgeBase']);
        Route::get('/kb/{articleId}', [SupportController::class, 'getKnowledgeBaseArticle']);
    });

    // Notification Management
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'getNotifications']);
        Route::post('/mark-read', [NotificationController::class, 'markAsRead']);
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::get('/settings', [NotificationController::class, 'getSettings']);
        Route::post('/settings', [NotificationController::class, 'updateSettings']);
    });

    // Team Management
    Route::prefix('team')->group(function () {
        Route::get('/members', [TeamController::class, 'getMembers']);
        Route::post('/invite', [TeamController::class, 'inviteMember']);
        Route::post('/members/{memberId}/role', [TeamController::class, 'updateMemberRole']);
        Route::delete('/members/{memberId}', [TeamController::class, 'removeMember']);
        Route::get('/invitations', [TeamController::class, 'getInvitations']);
        Route::post('/invitations/{invitationId}/resend', [TeamController::class, 'resendInvitation']);
        Route::delete('/invitations/{invitationId}', [TeamController::class, 'cancelInvitation']);
    });

    // Workspace Management
    Route::prefix('workspaces')->group(function () {
        Route::get('/', [WorkspaceController::class, 'getWorkspaces']);
        Route::post('/', [WorkspaceController::class, 'createWorkspace']);
        Route::get('/{workspaceId}', [WorkspaceController::class, 'getWorkspace']);
        Route::put('/{workspaceId}', [WorkspaceController::class, 'updateWorkspace']);
        Route::delete('/{workspaceId}', [WorkspaceController::class, 'deleteWorkspace']);
        Route::post('/{workspaceId}/duplicate', [WorkspaceController::class, 'duplicateWorkspace']);
        Route::post('/{workspaceId}/export', [WorkspaceController::class, 'exportWorkspace']);
        Route::post('/{workspaceId}/share', [WorkspaceController::class, 'shareWorkspace']);
    });

    // File Management
    Route::prefix('files')->group(function () {
        Route::get('/', [FileController::class, 'getFiles']);
        Route::post('/upload', [FileController::class, 'uploadFile']);
        Route::get('/{fileId}', [FileController::class, 'getFile']);
        Route::delete('/{fileId}', [FileController::class, 'deleteFile']);
        Route::post('/{fileId}/share', [FileController::class, 'shareFile']);
        Route::get('/shared/{shareToken}', [FileController::class, 'getSharedFile']);
    });

    // Templates
    Route::prefix('templates')->group(function () {
        Route::get('/', [TemplateController::class, 'getTemplates']);
        Route::get('/categories', [TemplateController::class, 'getCategories']);
        Route::get('/{templateId}', [TemplateController::class, 'getTemplate']);
        Route::post('/{templateId}/use', [TemplateController::class, 'useTemplate']);
        Route::post('/create', [TemplateController::class, 'createTemplate']);
        Route::get('/my-templates', [TemplateController::class, 'getMyTemplates']);
    });

    // Automation
    Route::prefix('automation')->group(function () {
        Route::get('/workflows', [AutomationController::class, 'getWorkflows']);
        Route::post('/workflows', [AutomationController::class, 'createWorkflow']);
        Route::get('/workflows/{workflowId}', [AutomationController::class, 'getWorkflow']);
        Route::put('/workflows/{workflowId}', [AutomationController::class, 'updateWorkflow']);
        Route::delete('/workflows/{workflowId}', [AutomationController::class, 'deleteWorkflow']);
        Route::post('/workflows/{workflowId}/toggle', [AutomationController::class, 'toggleWorkflow']);
        Route::get('/workflows/{workflowId}/logs', [AutomationController::class, 'getWorkflowLogs']);
    });

    // API Keys Management
    Route::prefix('api-keys')->group(function () {
        Route::get('/', [ApiKeyController::class, 'getApiKeys']);
        Route::post('/', [ApiKeyController::class, 'createApiKey']);
        Route::put('/{keyId}', [ApiKeyController::class, 'updateApiKey']);
        Route::delete('/{keyId}', [ApiKeyController::class, 'deleteApiKey']);
        Route::post('/{keyId}/regenerate', [ApiKeyController::class, 'regenerateApiKey']);
        Route::get('/{keyId}/usage', [ApiKeyController::class, 'getApiKeyUsage']);
    });
});

// Admin Routes (Require Admin Authentication)
Route::middleware(['auth:sanctum', 'admin'])->prefix('api/admin')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [AdvancedAdminController::class, 'getDashboardOverview']);
    
    // User Management
    Route::prefix('users')->group(function () {
        Route::get('/', [AdvancedAdminController::class, 'manageUsers']);
        Route::get('/{userId}', [AdvancedAdminController::class, 'getUser']);
        Route::put('/{userId}', [AdvancedAdminController::class, 'updateUser']);
        Route::post('/{userId}/suspend', [AdvancedAdminController::class, 'suspendUser']);
        Route::post('/{userId}/unsuspend', [AdvancedAdminController::class, 'unsuspendUser']);
        Route::post('/{userId}/ban', [AdvancedAdminController::class, 'banUser']);
        Route::post('/{userId}/unban', [AdvancedAdminController::class, 'unbanUser']);
        Route::delete('/{userId}', [AdvancedAdminController::class, 'deleteUser']);
        Route::post('/{userId}/impersonate', [AdvancedAdminController::class, 'impersonateUser']);
        Route::get('/{userId}/activity', [AdvancedAdminController::class, 'getUserActivity']);
    });

    // Subscription Plan Management
    Route::prefix('subscription-plans')->group(function () {
        Route::get('/', [AdvancedAdminController::class, 'manageSubscriptionPlans']);
        Route::post('/', [AdvancedAdminController::class, 'saveSubscriptionPlan']);
        Route::get('/{planId}', [AdvancedAdminController::class, 'getSubscriptionPlan']);
        Route::put('/{planId}', [AdvancedAdminController::class, 'saveSubscriptionPlan']);
        Route::delete('/{planId}', [AdvancedAdminController::class, 'deleteSubscriptionPlan']);
        Route::get('/{planId}/analytics', [AdvancedAdminController::class, 'getPlanAnalytics']);
    });

    // Affiliate Management
    Route::prefix('affiliates')->group(function () {
        Route::get('/', [AdvancedAdminController::class, 'manageAffiliates']);
        Route::get('/{affiliateId}', [AdvancedAdminController::class, 'getAffiliate']);
        Route::put('/{affiliateId}', [AdvancedAdminController::class, 'updateAffiliateStatus']);
        Route::post('/{affiliateId}/approve', [AdvancedAdminController::class, 'approveAffiliate']);
        Route::post('/{affiliateId}/reject', [AdvancedAdminController::class, 'rejectAffiliate']);
        Route::post('/{affiliateId}/suspend', [AdvancedAdminController::class, 'suspendAffiliate']);
        Route::get('/{affiliateId}/commissions', [AdvancedAdminController::class, 'getAffiliateCommissions']);
        Route::post('/process-payments', [AdvancedAdminController::class, 'processAffiliatePayments']);
    });

    // System Settings
    Route::prefix('settings')->group(function () {
        Route::get('/', [AdvancedAdminController::class, 'getSystemSettings']);
        Route::post('/', [AdvancedAdminController::class, 'updateSystemSettings']);
        Route::get('/feature-flags', [AdvancedAdminController::class, 'manageFeatureFlags']);
        Route::post('/feature-flags/{flagId}/toggle', [AdvancedAdminController::class, 'toggleFeatureFlag']);
        Route::get('/integrations', [AdvancedAdminController::class, 'getIntegrationSettings']);
        Route::post('/integrations', [AdvancedAdminController::class, 'updateIntegrationSettings']);
    });

    // System Health & Monitoring
    Route::prefix('system')->group(function () {
        Route::get('/health', [AdvancedAdminController::class, 'getSystemHealth']);
        Route::post('/maintenance', [AdvancedAdminController::class, 'runMaintenance']);
        Route::get('/logs', [AdvancedAdminController::class, 'getSystemLogs']);
        Route::get('/metrics', [AdvancedAdminController::class, 'getSystemMetrics']);
        Route::get('/performance', [AdvancedAdminController::class, 'getPerformanceMetrics']);
    });

    // Analytics & Reports
    Route::prefix('analytics')->group(function () {
        Route::get('/revenue', [AdvancedAdminController::class, 'getRevenueAnalytics']);
        Route::get('/users', [AdvancedAdminController::class, 'getUserAnalytics']);
        Route::get('/subscriptions', [AdvancedAdminController::class, 'getSubscriptionAnalytics']);
        Route::get('/affiliates', [AdvancedAdminController::class, 'getAffiliateAnalytics']);
        Route::get('/support', [AdvancedAdminController::class, 'getSupportAnalytics']);
        Route::post('/export', [AdvancedAdminController::class, 'exportAnalytics']);
    });

    // Support Management
    Route::prefix('support')->group(function () {
        Route::get('/tickets', [AdvancedAdminController::class, 'getSupportTickets']);
        Route::get('/tickets/{ticketId}', [AdvancedAdminController::class, 'getSupportTicket']);
        Route::post('/tickets/{ticketId}/assign', [AdvancedAdminController::class, 'assignSupportTicket']);
        Route::post('/tickets/{ticketId}/reply', [AdvancedAdminController::class, 'replySupportTicket']);
        Route::post('/tickets/{ticketId}/close', [AdvancedAdminController::class, 'closeSupportTicket']);
        Route::get('/agents', [AdvancedAdminController::class, 'getSupportAgents']);
        Route::post('/agents', [AdvancedAdminController::class, 'createSupportAgent']);
    });

    // Content Management
    Route::prefix('content')->group(function () {
        Route::get('/pages', [AdvancedAdminController::class, 'getPages']);
        Route::post('/pages', [AdvancedAdminController::class, 'createPage']);
        Route::put('/pages/{pageId}', [AdvancedAdminController::class, 'updatePage']);
        Route::delete('/pages/{pageId}', [AdvancedAdminController::class, 'deletePage']);
        Route::get('/templates', [AdvancedAdminController::class, 'getTemplates']);
        Route::post('/templates', [AdvancedAdminController::class, 'createTemplate']);
        Route::put('/templates/{templateId}', [AdvancedAdminController::class, 'updateTemplate']);
        Route::delete('/templates/{templateId}', [AdvancedAdminController::class, 'deleteTemplate']);
    });

    // Audit Logs
    Route::prefix('audit')->group(function () {
        Route::get('/logs', [AdvancedAdminController::class, 'getAuditLogs']);
        Route::get('/logs/{logId}', [AdvancedAdminController::class, 'getAuditLog']);
        Route::post('/logs/export', [AdvancedAdminController::class, 'exportAuditLogs']);
    });

    // Backup & Restore
    Route::prefix('backup')->group(function () {
        Route::get('/list', [AdvancedAdminController::class, 'getBackups']);
        Route::post('/create', [AdvancedAdminController::class, 'createBackup']);
        Route::post('/restore/{backupId}', [AdvancedAdminController::class, 'restoreBackup']);
        Route::delete('/delete/{backupId}', [AdvancedAdminController::class, 'deleteBackup']);
    });
});

// Webhook Routes
Route::prefix('webhooks')->group(function () {
    Route::post('/stripe', [PaymentController::class, 'handleWebhook']);
    Route::post('/affiliate/{affiliateId}', [AffiliateController::class, 'handleWebhook']);
    Route::post('/integration/{integrationId}', [IntegrationController::class, 'handleWebhook']);
});

// Public API Routes (with rate limiting)
Route::middleware('throttle:api')->prefix('api/public')->group(function () {
    Route::get('/status', function () {
        return response()->json([
            'status' => 'operational',
            'version' => config('app.version'),
            'timestamp' => now()->toISOString()
        ]);
    });

    Route::get('/features', function () {
        return response()->json([
            'features' => [
                'subscription_management' => true,
                'affiliate_system' => true,
                'multi_workspace' => true,
                'real_time_collaboration' => true,
                'advanced_analytics' => true,
                'api_access' => true,
                'white_label' => true,
                'sso' => true
            ]
        ]);
    });
});