<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LegalDocument;
use App\Models\CookieConsent;
use App\Models\DataExportRequest;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class LegalController extends Controller
{
    /**
     * Show Terms of Service page
     */
    public function termsOfService()
    {
        $document = LegalDocument::where('type', 'terms_of_service')
            ->where('is_active', true)
            ->latest()
            ->first();

        return view('legal.terms-of-service', compact('document'));
    }

    /**
     * Show Privacy Policy page
     */
    public function privacyPolicy()
    {
        $document = LegalDocument::where('type', 'privacy_policy')
            ->where('is_active', true)
            ->latest()
            ->first();

        return view('legal.privacy-policy', compact('document'));
    }

    /**
     * Show Cookie Policy page
     */
    public function cookiePolicy()
    {
        $document = LegalDocument::where('type', 'cookie_policy')
            ->where('is_active', true)
            ->latest()
            ->first();

        return view('legal.cookie-policy', compact('document'));
    }

    /**
     * Show Refund Policy page
     */
    public function refundPolicy()
    {
        $document = LegalDocument::where('type', 'refund_policy')
            ->where('is_active', true)
            ->latest()
            ->first();

        return view('legal.refund-policy', compact('document'));
    }

    /**
     * Show Accessibility Statement page
     */
    public function accessibilityStatement()
    {
        $document = LegalDocument::where('type', 'accessibility_statement')
            ->where('is_active', true)
            ->latest()
            ->first();

        return view('legal.accessibility-statement', compact('document'));
    }

    /**
     * Handle cookie consent
     */
    public function handleCookieConsent(Request $request)
    {
        $request->validate([
            'consent_type' => 'required|in:accept_all,reject_all,customize',
            'cookies' => 'array',
            'ip_address' => 'nullable|ip',
            'user_agent' => 'nullable|string'
        ]);

        $consent = CookieConsent::create([
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
            'consent_type' => $request->consent_type,
            'cookies_accepted' => $request->cookies ?? [],
            'ip_address' => $request->ip_address ?? $request->ip(),
            'user_agent' => $request->user_agent ?? $request->header('User-Agent'),
            'consented_at' => now(),
            'expires_at' => now()->addYear()
        ]);

        // Log consent for audit trail
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'cookie_consent',
            'model' => 'CookieConsent',
            'model_id' => $consent->id,
            'old_values' => null,
            'new_values' => $consent->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cookie consent recorded successfully',
            'consent_id' => $consent->id
        ]);
    }

    /**
     * GDPR Data Export Request
     */
    public function requestDataExport(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'data_types' => 'required|array',
            'data_types.*' => 'in:profile,courses,payments,analytics,messages,files',
            'format' => 'required|in:json,csv,pdf',
            'reason' => 'nullable|string|max:500'
        ]);

        $exportRequest = DataExportRequest::create([
            'user_id' => auth()->id(),
            'email' => $request->email,
            'data_types' => $request->data_types,
            'format' => $request->format,
            'reason' => $request->reason,
            'status' => 'pending',
            'requested_at' => now(),
            'ip_address' => $request->ip()
        ]);

        // Log the request
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'data_export_request',
            'model' => 'DataExportRequest',
            'model_id' => $exportRequest->id,
            'old_values' => null,
            'new_values' => $exportRequest->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent')
        ]);

        // Queue export processing
        dispatch(new \App\Jobs\ProcessDataExport($exportRequest));

        return response()->json([
            'success' => true,
            'message' => 'Data export request submitted successfully. You will receive an email when ready.',
            'request_id' => $exportRequest->id,
            'estimated_completion' => now()->addHours(24)->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * GDPR Data Deletion Request
     */
    public function requestDataDeletion(Request $request)
    {
        $request->validate([
            'confirmation' => 'required|accepted',
            'reason' => 'nullable|string|max:500',
            'keep_anonymous_data' => 'boolean'
        ]);

        $user = auth()->user();

        // Create deletion request
        $deletionRequest = \App\Models\DataDeletionRequest::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'reason' => $request->reason,
            'keep_anonymous_data' => $request->keep_anonymous_data ?? false,
            'status' => 'pending',
            'requested_at' => now(),
            'ip_address' => $request->ip()
        ]);

        // Log the request
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'data_deletion_request',
            'model' => 'DataDeletionRequest',
            'model_id' => $deletionRequest->id,
            'old_values' => null,
            'new_values' => $deletionRequest->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent')
        ]);

        // Queue deletion processing (with safety delays)
        dispatch(new \App\Jobs\ProcessDataDeletion($deletionRequest))
            ->delay(now()->addHours(72)); // 72-hour cooling period

        return response()->json([
            'success' => true,
            'message' => 'Data deletion request submitted. You have 72 hours to cancel this request.',
            'request_id' => $deletionRequest->id,
            'cancellation_deadline' => now()->addHours(72)->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * Cancel data deletion request
     */
    public function cancelDataDeletion(Request $request, $requestId)
    {
        $deletionRequest = \App\Models\DataDeletionRequest::where('id', $requestId)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->first();

        if (!$deletionRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Deletion request not found or cannot be cancelled'
            ], 404);
        }

        if ($deletionRequest->requested_at->addHours(72)->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Cancellation period has expired'
            ], 400);
        }

        $deletionRequest->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $request->reason ?? 'User requested cancellation'
        ]);

        // Log the cancellation
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'data_deletion_cancelled',
            'model' => 'DataDeletionRequest',
            'model_id' => $deletionRequest->id,
            'old_values' => ['status' => 'pending'],
            'new_values' => ['status' => 'cancelled'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data deletion request cancelled successfully'
        ]);
    }

    /**
     * Get user's data processing activities
     */
    public function getDataProcessingActivities(Request $request)
    {
        $user = auth()->user();
        
        $activities = [
            'account_data' => [
                'description' => 'Your account information including name, email, and profile data',
                'legal_basis' => 'Contract performance',
                'retention_period' => 'Until account deletion',
                'data_types' => ['name', 'email', 'phone', 'profile_image', 'preferences']
            ],
            'course_data' => [
                'description' => 'Your learning activities, progress, and course interactions',
                'legal_basis' => 'Contract performance',
                'retention_period' => '7 years after course completion',
                'data_types' => ['enrollments', 'progress', 'completions', 'certificates', 'quiz_results']
            ],
            'payment_data' => [
                'description' => 'Your payment and billing information',
                'legal_basis' => 'Contract performance and legal obligations',
                'retention_period' => '7 years for tax purposes',
                'data_types' => ['payment_methods', 'transaction_history', 'invoices', 'refunds']
            ],
            'analytics_data' => [
                'description' => 'Your usage patterns and platform interactions',
                'legal_basis' => 'Legitimate interest',
                'retention_period' => '2 years',
                'data_types' => ['page_views', 'time_spent', 'feature_usage', 'device_information']
            ],
            'communication_data' => [
                'description' => 'Your messages, support tickets, and email communications',
                'legal_basis' => 'Contract performance',
                'retention_period' => '3 years',
                'data_types' => ['messages', 'support_tickets', 'email_history', 'notifications']
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $activities,
            'user_rights' => [
                'access' => 'Right to access your personal data',
                'rectification' => 'Right to correct inaccurate data',
                'erasure' => 'Right to request deletion of your data',
                'portability' => 'Right to receive your data in a structured format',
                'restrict' => 'Right to restrict processing',
                'object' => 'Right to object to processing',
                'withdraw_consent' => 'Right to withdraw consent where applicable'
            ]
        ]);
    }

    /**
     * Get audit log for user
     */
    public function getAuditLog(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'action' => 'nullable|string',
            'limit' => 'nullable|integer|min:1|max:100'
        ]);

        $query = AuditLog::where('user_id', auth()->id());

        if ($request->start_date) {
            $query->where('created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->where('created_at', '<=', $request->end_date);
        }

        if ($request->action) {
            $query->where('action', $request->action);
        }

        $logs = $query->orderBy('created_at', 'desc')
            ->limit($request->limit ?? 50)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'action' => $log->action,
                    'description' => $this->formatAuditLogDescription($log),
                    'timestamp' => $log->created_at->format('Y-m-d H:i:s'),
                    'ip_address' => $log->ip_address,
                    'user_agent' => $log->user_agent
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $logs,
            'total' => $query->count()
        ]);
    }

    /**
     * Format audit log description for user-friendly display
     */
    private function formatAuditLogDescription($log)
    {
        $descriptions = [
            'login' => 'Logged into account',
            'logout' => 'Logged out of account',
            'profile_update' => 'Updated profile information',
            'password_change' => 'Changed password',
            'cookie_consent' => 'Updated cookie preferences',
            'data_export_request' => 'Requested data export',
            'data_deletion_request' => 'Requested account deletion',
            'course_enrollment' => 'Enrolled in a course',
            'course_completion' => 'Completed a course',
            'payment_made' => 'Made a payment',
            'file_upload' => 'Uploaded a file',
            'file_download' => 'Downloaded a file'
        ];

        return $descriptions[$log->action] ?? 'Performed action: ' . $log->action;
    }
}