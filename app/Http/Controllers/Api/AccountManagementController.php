<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AccountDeletionRequest;
use App\Models\DataExportRequest;
use App\Models\UserSubscription;
use App\Services\AccountDeletionService;
use App\Services\DataExportService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AccountManagementController extends Controller
{
    protected $accountDeletionService;
    protected $dataExportService;
    protected $notificationService;

    public function __construct(
        AccountDeletionService $accountDeletionService,
        DataExportService $dataExportService,
        NotificationService $notificationService
    ) {
        $this->accountDeletionService = $accountDeletionService;
        $this->dataExportService = $dataExportService;
        $this->notificationService = $notificationService;
    }

    /**
     * Get account overview
     */
    public function getAccountOverview(Request $request)
    {
        try {
            $user = $request->user();
            $user->load(['subscription.plan', 'paymentMethods', 'referrals', 'achievements']);

            $overview = [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                    'created_at' => $user->created_at,
                    'email_verified_at' => $user->email_verified_at,
                    'two_factor_enabled' => $user->two_factor_enabled,
                    'preferences' => $user->preferences,
                    'timezone' => $user->timezone,
                    'language' => $user->language
                ],
                'subscription' => $user->subscription ? [
                    'plan' => $user->subscription->plan->name,
                    'status' => $user->subscription->status,
                    'next_billing' => $user->subscription->next_billing_date,
                    'trial_ends' => $user->subscription->trial_ends_at
                ] : null,
                'usage_stats' => $this->getUsageStats($user),
                'payment_methods' => $user->paymentMethods->count(),
                'referrals' => [
                    'total' => $user->referrals()->count(),
                    'successful' => $user->referrals()->where('status', 'completed')->count(),
                    'earnings' => $user->referrals()->where('status', 'completed')->sum('commission_amount')
                ],
                'achievements' => $user->achievements->count(),
                'account_age_days' => $user->created_at->diffInDays(now()),
                'last_login' => $user->last_login_at,
                'security_score' => $this->calculateSecurityScore($user)
            ];

            return response()->json([
                'success' => true,
                'data' => $overview
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching account overview: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch account overview'
            ], 500);
        }
    }

    /**
     * Update account information
     */
    public function updateAccount(Request $request)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $request->user()->id,
            'avatar' => 'sometimes|image|max:2048',
            'timezone' => 'sometimes|string|max:50',
            'language' => 'sometimes|string|max:10',
            'preferences' => 'sometimes|array',
            'notification_settings' => 'sometimes|array'
        ]);

        DB::beginTransaction();

        try {
            $user = $request->user();
            $updates = [];

            if ($request->has('name')) {
                $updates['name'] = $request->name;
            }

            if ($request->has('email') && $request->email !== $user->email) {
                $updates['email'] = $request->email;
                $updates['email_verified_at'] = null; // Require re-verification
            }

            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $updates['avatar'] = $avatarPath;
                
                // Delete old avatar if exists
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }
            }

            if ($request->has('timezone')) {
                $updates['timezone'] = $request->timezone;
            }

            if ($request->has('language')) {
                $updates['language'] = $request->language;
            }

            if ($request->has('preferences')) {
                $updates['preferences'] = array_merge($user->preferences ?? [], $request->preferences);
            }

            if ($request->has('notification_settings')) {
                $updates['notification_settings'] = array_merge($user->notification_settings ?? [], $request->notification_settings);
            }

            $user->update($updates);

            DB::commit();

            // Send email verification if email changed
            if (isset($updates['email'])) {
                $user->sendEmailVerificationNotification();
            }

            return response()->json([
                'success' => true,
                'data' => $user->fresh(),
                'message' => 'Account updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating account: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update account'
            ], 500);
        }
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
            'logout_other_devices' => 'boolean'
        ]);

        try {
            $user = $request->user();

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 400);
            }

            $user->update([
                'password' => Hash::make($request->new_password),
                'password_changed_at' => now()
            ]);

            // Logout other devices if requested
            if ($request->logout_other_devices) {
                $user->tokens()->where('id', '!=', $request->user()->currentAccessToken()->id)->delete();
            }

            // Log security event
            $this->logSecurityEvent($user, 'password_changed', $request->ip());

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error changing password: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to change password'
            ], 500);
        }
    }

    /**
     * Enable two-factor authentication
     */
    public function enableTwoFactor(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6'
        ]);

        try {
            $user = $request->user();

            if ($user->two_factor_enabled) {
                return response()->json([
                    'success' => false,
                    'message' => 'Two-factor authentication is already enabled'
                ], 400);
            }

            // Verify the code
            if (!$this->verifyTwoFactorCode($user, $request->code)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid verification code'
                ], 400);
            }

            $user->update([
                'two_factor_enabled' => true,
                'two_factor_confirmed_at' => now()
            ]);

            // Generate recovery codes
            $recoveryCodes = $this->generateRecoveryCodes($user);

            // Log security event
            $this->logSecurityEvent($user, 'two_factor_enabled', $request->ip());

            return response()->json([
                'success' => true,
                'data' => [
                    'recovery_codes' => $recoveryCodes
                ],
                'message' => 'Two-factor authentication enabled successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error enabling two-factor authentication: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to enable two-factor authentication'
            ], 500);
        }
    }

    /**
     * Disable two-factor authentication
     */
    public function disableTwoFactor(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
            'code' => 'required|string|size:6'
        ]);

        try {
            $user = $request->user();

            if (!$user->two_factor_enabled) {
                return response()->json([
                    'success' => false,
                    'message' => 'Two-factor authentication is not enabled'
                ], 400);
            }

            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password is incorrect'
                ], 400);
            }

            if (!$this->verifyTwoFactorCode($user, $request->code)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid verification code'
                ], 400);
            }

            $user->update([
                'two_factor_enabled' => false,
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null
            ]);

            // Log security event
            $this->logSecurityEvent($user, 'two_factor_disabled', $request->ip());

            return response()->json([
                'success' => true,
                'message' => 'Two-factor authentication disabled successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error disabling two-factor authentication: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to disable two-factor authentication'
            ], 500);
        }
    }

    /**
     * Request account deletion
     */
    public function requestAccountDeletion(Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
            'password' => 'required|string',
            'delete_immediately' => 'boolean',
            'feedback' => 'nullable|string|max:1000'
        ]);

        try {
            $user = $request->user();

            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password is incorrect'
                ], 400);
            }

            // Check for active subscriptions
            if ($user->hasActiveSubscriptions()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please cancel all active subscriptions before deleting your account'
                ], 400);
            }

            $deleteImmediately = $request->delete_immediately ?? false;
            $gracePeriod = $deleteImmediately ? 0 : 30; // 30-day grace period

            $deletionRequest = AccountDeletionRequest::create([
                'user_id' => $user->id,
                'reason' => $request->reason,
                'feedback' => $request->feedback,
                'delete_immediately' => $deleteImmediately,
                'scheduled_for' => now()->addDays($gracePeriod),
                'status' => 'pending',
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent')
            ]);

            // Schedule deletion
            if ($deleteImmediately) {
                $this->accountDeletionService->deleteAccount($user, $deletionRequest);
            } else {
                $this->accountDeletionService->scheduleDeletion($user, $deletionRequest);
            }

            // Send confirmation email
            $this->notificationService->sendAccountDeletionRequest($user, $deletionRequest);

            return response()->json([
                'success' => true,
                'data' => [
                    'request_id' => $deletionRequest->id,
                    'scheduled_for' => $deletionRequest->scheduled_for,
                    'can_cancel_until' => $deleteImmediately ? null : $deletionRequest->scheduled_for->subDays(1)
                ],
                'message' => $deleteImmediately 
                    ? 'Account deletion initiated immediately' 
                    : 'Account deletion scheduled. You have 30 days to cancel this request.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error requesting account deletion: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to request account deletion'
            ], 500);
        }
    }

    /**
     * Cancel account deletion
     */
    public function cancelAccountDeletion(Request $request)
    {
        try {
            $user = $request->user();
            $deletionRequest = AccountDeletionRequest::where('user_id', $user->id)
                ->where('status', 'pending')
                ->first();

            if (!$deletionRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'No pending deletion request found'
                ], 400);
            }

            if ($deletionRequest->scheduled_for->isPast()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Deletion request cannot be canceled as it has already been processed'
                ], 400);
            }

            $deletionRequest->update([
                'status' => 'canceled',
                'canceled_at' => now()
            ]);

            // Send cancellation confirmation
            $this->notificationService->sendAccountDeletionCanceled($user, $deletionRequest);

            return response()->json([
                'success' => true,
                'message' => 'Account deletion request canceled successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error canceling account deletion: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel account deletion'
            ], 500);
        }
    }

    /**
     * Request data export
     */
    public function requestDataExport(Request $request)
    {
        $request->validate([
            'data_types' => 'required|array',
            'data_types.*' => 'in:profile,subscriptions,payments,projects,analytics,messages,files',
            'format' => 'required|in:json,csv,pdf'
        ]);

        try {
            $user = $request->user();

            $exportRequest = DataExportRequest::create([
                'user_id' => $user->id,
                'data_types' => $request->data_types,
                'format' => $request->format,
                'status' => 'pending',
                'requested_at' => now(),
                'ip_address' => $request->ip()
            ]);

            // Queue export job
            $this->dataExportService->queueExport($exportRequest);

            return response()->json([
                'success' => true,
                'data' => [
                    'request_id' => $exportRequest->id,
                    'estimated_completion' => now()->addHours(24)
                ],
                'message' => 'Data export request submitted. You will receive an email when ready.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error requesting data export: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to request data export'
            ], 500);
        }
    }

    /**
     * Get active sessions
     */
    public function getActiveSessions(Request $request)
    {
        try {
            $user = $request->user();
            $sessions = $user->tokens()
                ->where('expires_at', '>', now())
                ->orderBy('last_used_at', 'desc')
                ->get()
                ->map(function ($token) {
                    return [
                        'id' => $token->id,
                        'name' => $token->name,
                        'last_used' => $token->last_used_at,
                        'ip_address' => $token->ip_address,
                        'user_agent' => $token->user_agent,
                        'is_current' => $token->id === request()->user()->currentAccessToken()->id
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $sessions
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching active sessions: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch active sessions'
            ], 500);
        }
    }

    /**
     * Revoke session
     */
    public function revokeSession(Request $request, $sessionId)
    {
        try {
            $user = $request->user();
            $token = $user->tokens()->where('id', $sessionId)->first();

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session not found'
                ], 404);
            }

            $token->delete();

            return response()->json([
                'success' => true,
                'message' => 'Session revoked successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error revoking session: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to revoke session'
            ], 500);
        }
    }

    /**
     * Get usage statistics
     */
    private function getUsageStats($user)
    {
        return [
            'storage_used' => $user->getStorageUsage(),
            'api_calls_this_month' => $user->getApiCallsThisMonth(),
            'projects_created' => $user->projects()->count(),
            'total_earnings' => $user->getTotalEarnings(),
            'referrals_made' => $user->referrals()->count()
        ];
    }

    /**
     * Calculate security score
     */
    private function calculateSecurityScore($user)
    {
        $score = 0;
        
        // Email verified
        if ($user->email_verified_at) $score += 20;
        
        // Two-factor authentication
        if ($user->two_factor_enabled) $score += 30;
        
        // Strong password (assume if changed recently)
        if ($user->password_changed_at && $user->password_changed_at->diffInDays() < 90) $score += 25;
        
        // Recent activity
        if ($user->last_login_at && $user->last_login_at->diffInDays() < 30) $score += 15;
        
        // Payment method added
        if ($user->paymentMethods()->count() > 0) $score += 10;
        
        return min(100, $score);
    }

    /**
     * Log security event
     */
    private function logSecurityEvent($user, $event, $ipAddress)
    {
        // Implementation for security event logging
        Log::info('Security event', [
            'user_id' => $user->id,
            'event' => $event,
            'ip_address' => $ipAddress,
            'timestamp' => now()
        ]);
    }

    /**
     * Verify two-factor code
     */
    private function verifyTwoFactorCode($user, $code)
    {
        // Implementation for two-factor verification
        return true; // Simplified for demo
    }

    /**
     * Generate recovery codes
     */
    private function generateRecoveryCodes($user)
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
        }
        
        $user->update(['two_factor_recovery_codes' => encrypt($codes)]);
        
        return $codes;
    }
}