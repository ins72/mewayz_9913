<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EnterpriseController;
use App\Http\Controllers\Api\ComplianceController;
use App\Http\Controllers\Api\AdvancedAIController;
use App\Http\Controllers\Api\InternationalizationController;

/*
|--------------------------------------------------------------------------
| Phase 2: Professional Features API Routes
|--------------------------------------------------------------------------
|
| Advanced team management, white-label solutions, enterprise integrations,
| enhanced security features, and business intelligence suite
|
*/

// Enterprise Features
Route::middleware(\App\Http\Middleware\CustomSanctumAuth::class)->prefix('enterprise')->group(function () {
    
    // SSO Provider Management
    Route::prefix('sso')->group(function () {
        Route::get('/providers', [EnterpriseController::class, 'getSSOProviders']);
        Route::post('/providers', [EnterpriseController::class, 'createSSOProvider']);
        Route::put('/providers/{id}', [EnterpriseController::class, 'updateSSOProvider']);
        Route::post('/providers/{id}/test', [EnterpriseController::class, 'testSSOProvider']);
        Route::delete('/providers/{id}', [EnterpriseController::class, 'deleteSSOProvider']);
    });
    
    // White Label Configuration
    Route::prefix('white-label')->group(function () {
        Route::get('/config', [EnterpriseController::class, 'getWhiteLabelConfig']);
        Route::put('/config', [EnterpriseController::class, 'updateWhiteLabelConfig']);
        Route::post('/css', [EnterpriseController::class, 'generateWhiteLabelCSS']);
    });
    
    // Department Management
    Route::prefix('departments')->group(function () {
        Route::get('/', [EnterpriseController::class, 'getDepartments']);
        Route::post('/', [EnterpriseController::class, 'createDepartment']);
        Route::put('/{id}', [EnterpriseController::class, 'updateDepartment']);
        Route::delete('/{id}', [EnterpriseController::class, 'deleteDepartment']);
        Route::post('/{id}/users', [EnterpriseController::class, 'addUserToDepartment']);
        Route::delete('/{id}/users/{userId}', [EnterpriseController::class, 'removeUserFromDepartment']);
    });
    
    // Team Management
    Route::prefix('teams')->group(function () {
        Route::get('/', [EnterpriseController::class, 'getTeams']);
        Route::post('/', [EnterpriseController::class, 'createTeam']);
        Route::put('/{id}', [EnterpriseController::class, 'updateTeam']);
        Route::delete('/{id}', [EnterpriseController::class, 'deleteTeam']);
        Route::post('/{id}/members', [EnterpriseController::class, 'addTeamMember']);
        Route::delete('/{id}/members/{userId}', [EnterpriseController::class, 'removeTeamMember']);
        Route::put('/{id}/members/{userId}/role', [EnterpriseController::class, 'updateMemberRole']);
    });
    
    // Audit Logs
    Route::prefix('audit')->group(function () {
        Route::get('/logs', [EnterpriseController::class, 'getAuditLogs']);
        Route::get('/logs/{id}', [EnterpriseController::class, 'getAuditLog']);
        Route::post('/logs/export', [EnterpriseController::class, 'exportAuditLogs']);
    });
    
    // Security Events
    Route::prefix('security')->group(function () {
        Route::get('/events', [EnterpriseController::class, 'getSecurityEvents']);
        Route::get('/events/{id}', [EnterpriseController::class, 'getSecurityEvent']);
        Route::post('/events/{id}/resolve', [EnterpriseController::class, 'resolveSecurityEvent']);
        Route::get('/dashboard', [EnterpriseController::class, 'getSecurityDashboard']);
    });
    
    // Performance Analytics
    Route::prefix('analytics')->group(function () {
        Route::get('/performance', [EnterpriseController::class, 'getPerformanceAnalytics']);
        Route::get('/usage', [EnterpriseController::class, 'getUsageAnalytics']);
        Route::get('/costs', [EnterpriseController::class, 'getCostAnalytics']);
        Route::get('/efficiency', [EnterpriseController::class, 'getEfficiencyMetrics']);
    });
});

// Compliance Management
Route::middleware(\App\Http\Middleware\CustomSanctumAuth::class)->prefix('compliance')->group(function () {
    Route::get('/dashboard', [ComplianceController::class, 'getDashboard']);
    Route::get('/reports', [ComplianceController::class, 'getReports']);
    Route::get('/report-types', [ComplianceController::class, 'getReportTypes']);
    Route::post('/reports', [ComplianceController::class, 'createReport']);
    Route::get('/reports/{id}', [ComplianceController::class, 'getReport']);
    Route::delete('/reports/{id}', [ComplianceController::class, 'deleteReport']);
    Route::get('/checklist', [ComplianceController::class, 'getChecklist']);
    Route::put('/settings', [ComplianceController::class, 'updateSettings']);
    Route::post('/reports/{id}/download', [ComplianceController::class, 'downloadReport']);
});

// Advanced AI Features
Route::middleware(\App\Http\Middleware\CustomSanctumAuth::class)->prefix('advanced-ai')->group(function () {
    Route::get('/models', [AdvancedAIController::class, 'getModels']);
    Route::post('/generate', [AdvancedAIController::class, 'generateAdvancedContent']);
    Route::post('/sentiment', [AdvancedAIController::class, 'analyzeSentiment']);
    Route::post('/insights', [AdvancedAIController::class, 'generateBusinessInsights']);
    Route::post('/predict', [AdvancedAIController::class, 'predictTrends']);
    Route::post('/chatbot', [AdvancedAIController::class, 'generateChatbotResponse']);
    Route::get('/usage', [AdvancedAIController::class, 'getUsageStatistics']);
    Route::post('/optimize', [AdvancedAIController::class, 'optimizeContent']);
    Route::post('/personalize', [AdvancedAIController::class, 'personalizeContent']);
});

// Internationalization
Route::middleware(\App\Http\Middleware\CustomSanctumAuth::class)->prefix('i18n')->group(function () {
    Route::get('/languages', [InternationalizationController::class, 'getLanguages']);
    Route::get('/languages/{code}/translations', [InternationalizationController::class, 'getTranslations']);
    Route::put('/languages/{code}/translations', [InternationalizationController::class, 'updateTranslations']);
    Route::get('/languages/{code}/missing', [InternationalizationController::class, 'getMissingTranslations']);
    Route::post('/languages/{code}/import', [InternationalizationController::class, 'importTranslations']);
    Route::get('/languages/{code}/export', [InternationalizationController::class, 'exportTranslations']);
    
    Route::get('/currencies', [InternationalizationController::class, 'getCurrencies']);
    Route::get('/exchange-rates', [InternationalizationController::class, 'getExchangeRates']);
    Route::post('/convert', [InternationalizationController::class, 'convertCurrency']);
    
    Route::get('/tax-rates', [InternationalizationController::class, 'getTaxRates']);
    Route::post('/calculate-tax', [InternationalizationController::class, 'calculateTax']);
    
    Route::get('/settings', [InternationalizationController::class, 'getLocalizationSettings']);
});

// Advanced Time Tracking
Route::middleware(\App\Http\Middleware\CustomSanctumAuth::class)->prefix('time-tracking')->group(function () {
    Route::get('/entries', [App\Http\Controllers\Api\TimeTrackingController::class, 'getTimeEntries']);
    Route::post('/entries', [App\Http\Controllers\Api\TimeTrackingController::class, 'createTimeEntry']);
    Route::put('/entries/{id}', [App\Http\Controllers\Api\TimeTrackingController::class, 'updateTimeEntry']);
    Route::delete('/entries/{id}', [App\Http\Controllers\Api\TimeTrackingController::class, 'deleteTimeEntry']);
    Route::post('/entries/{id}/start', [App\Http\Controllers\Api\TimeTrackingController::class, 'startTimer']);
    Route::post('/entries/{id}/stop', [App\Http\Controllers\Api\TimeTrackingController::class, 'stopTimer']);
    Route::get('/reports', [App\Http\Controllers\Api\TimeTrackingController::class, 'getReports']);
    Route::get('/projects', [App\Http\Controllers\Api\TimeTrackingController::class, 'getProjects']);
    Route::post('/projects', [App\Http\Controllers\Api\TimeTrackingController::class, 'createProject']);
});

// Advanced Workflow Automation
Route::middleware(\App\Http\Middleware\CustomSanctumAuth::class)->prefix('workflows')->group(function () {
    Route::get('/', [App\Http\Controllers\Api\WorkflowController::class, 'getWorkflows']);
    Route::post('/', [App\Http\Controllers\Api\WorkflowController::class, 'createWorkflow']);
    Route::get('/{id}', [App\Http\Controllers\Api\WorkflowController::class, 'getWorkflow']);
    Route::put('/{id}', [App\Http\Controllers\Api\WorkflowController::class, 'updateWorkflow']);
    Route::delete('/{id}', [App\Http\Controllers\Api\WorkflowController::class, 'deleteWorkflow']);
    Route::post('/{id}/execute', [App\Http\Controllers\Api\WorkflowController::class, 'executeWorkflow']);
    Route::post('/{id}/toggle', [App\Http\Controllers\Api\WorkflowController::class, 'toggleWorkflow']);
    Route::get('/{id}/logs', [App\Http\Controllers\Api\WorkflowController::class, 'getWorkflowLogs']);
    Route::get('/templates', [App\Http\Controllers\Api\WorkflowController::class, 'getTemplates']);
    Route::post('/{id}/duplicate', [App\Http\Controllers\Api\WorkflowController::class, 'duplicateWorkflow']);
});

// Advanced Permissions & Roles
Route::middleware(\App\Http\Middleware\CustomSanctumAuth::class)->prefix('permissions')->group(function () {
    Route::get('/roles', [App\Http\Controllers\Api\PermissionController::class, 'getRoles']);
    Route::post('/roles', [App\Http\Controllers\Api\PermissionController::class, 'createRole']);
    Route::put('/roles/{id}', [App\Http\Controllers\Api\PermissionController::class, 'updateRole']);
    Route::delete('/roles/{id}', [App\Http\Controllers\Api\PermissionController::class, 'deleteRole']);
    Route::get('/permissions', [App\Http\Controllers\Api\PermissionController::class, 'getPermissions']);
    Route::post('/users/{id}/roles', [App\Http\Controllers\Api\PermissionController::class, 'assignRole']);
    Route::delete('/users/{id}/roles/{roleId}', [App\Http\Controllers\Api\PermissionController::class, 'revokeRole']);
    Route::get('/users/{id}/permissions', [App\Http\Controllers\Api\PermissionController::class, 'getUserPermissions']);
});

// Enhanced API Management
Route::middleware(\App\Http\Middleware\CustomSanctumAuth::class)->prefix('api-management')->group(function () {
    Route::get('/keys', [App\Http\Controllers\Api\ApiManagementController::class, 'getApiKeys']);
    Route::post('/keys', [App\Http\Controllers\Api\ApiManagementController::class, 'createApiKey']);
    Route::put('/keys/{id}', [App\Http\Controllers\Api\ApiManagementController::class, 'updateApiKey']);
    Route::delete('/keys/{id}', [App\Http\Controllers\Api\ApiManagementController::class, 'deleteApiKey']);
    Route::get('/keys/{id}/usage', [App\Http\Controllers\Api\ApiManagementController::class, 'getApiKeyUsage']);
    Route::post('/keys/{id}/regenerate', [App\Http\Controllers\Api\ApiManagementController::class, 'regenerateApiKey']);
    Route::get('/rate-limits', [App\Http\Controllers\Api\ApiManagementController::class, 'getRateLimits']);
    Route::put('/rate-limits', [App\Http\Controllers\Api\ApiManagementController::class, 'updateRateLimits']);
});