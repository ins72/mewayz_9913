<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\DataDeletionRequest;

class AccountDeletionController extends Controller
{
    /**
     * Request account deletion
     */
    public function requestDeletion(Request $request)
    {
        try {
            $request->validate([
                'password' => 'required',
                'reason' => 'nullable|string|max:500',
                'feedback' => 'nullable|string|max:1000'
            ]);

            $user = $request->user();

            // Verify password
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'error' => 'Invalid password'
                ], 400);
            }

            // Check if deletion request already exists
            $existingRequest = DataDeletionRequest::where('user_id', $user->id)
                ->where('status', 'pending')
                ->first();

            if ($existingRequest) {
                return response()->json([
                    'error' => 'Account deletion request already submitted'
                ], 400);
            }

            // Create deletion request
            $deletionRequest = DataDeletionRequest::create([
                'user_id' => $user->id,
                'reason' => $request->reason,
                'feedback' => $request->feedback,
                'status' => 'pending',
                'requested_at' => now(),
                'scheduled_deletion_at' => now()->addDays(30) // 30-day grace period
            ]);

            // Mark user as pending deletion
            $user->update([
                'account_status' => 'pending_deletion',
                'deletion_scheduled_at' => now()->addDays(30)
            ]);

            Log::info('Account deletion requested', [
                'user_id' => $user->id,
                'email' => $user->email,
                'reason' => $request->reason
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Account deletion request submitted. Your account will be deleted in 30 days unless you cancel this request.',
                'scheduled_deletion' => $deletionRequest->scheduled_deletion_at->format('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            Log::error('Account deletion request failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to process deletion request'
            ], 500);
        }
    }

    /**
     * Cancel account deletion request
     */
    public function cancelDeletion(Request $request)
    {
        try {
            $user = $request->user();

            $deletionRequest = DataDeletionRequest::where('user_id', $user->id)
                ->where('status', 'pending')
                ->first();

            if (!$deletionRequest) {
                return response()->json([
                    'error' => 'No pending deletion request found'
                ], 404);
            }

            // Cancel deletion request
            $deletionRequest->update([
                'status' => 'cancelled',
                'cancelled_at' => now()
            ]);

            // Restore user account
            $user->update([
                'account_status' => 'active',
                'deletion_scheduled_at' => null
            ]);

            Log::info('Account deletion cancelled', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Account deletion request cancelled successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Account deletion cancellation failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to cancel deletion request'
            ], 500);
        }
    }

    /**
     * Get account deletion status
     */
    public function getDeletionStatus(Request $request)
    {
        try {
            $user = $request->user();

            $deletionRequest = DataDeletionRequest::where('user_id', $user->id)
                ->where('status', 'pending')
                ->first();

            if (!$deletionRequest) {
                return response()->json([
                    'has_pending_deletion' => false,
                    'account_status' => $user->account_status ?? 'active'
                ]);
            }

            return response()->json([
                'has_pending_deletion' => true,
                'account_status' => $user->account_status,
                'scheduled_deletion_at' => $deletionRequest->scheduled_deletion_at->format('Y-m-d H:i:s'),
                'days_remaining' => now()->diffInDays($deletionRequest->scheduled_deletion_at, false),
                'reason' => $deletionRequest->reason,
                'requested_at' => $deletionRequest->requested_at->format('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get deletion status: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to retrieve deletion status'
            ], 500);
        }
    }

    /**
     * Execute account deletion (Admin or scheduled task)
     */
    public function executeDeletion(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'confirmation' => 'required|string'
            ]);

            $userId = $request->user_id;
            $confirmation = $request->confirmation;

            // Verify confirmation string
            if ($confirmation !== 'DELETE_ACCOUNT_PERMANENTLY') {
                return response()->json([
                    'error' => 'Invalid confirmation string'
                ], 400);
            }

            $user = User::findOrFail($userId);

            // Check if user is authorized to delete this account
            $currentUser = $request->user();
            if ($currentUser->id !== $userId && !$currentUser->hasRole('admin')) {
                return response()->json([
                    'error' => 'Unauthorized to delete this account'
                ], 403);
            }

            // Start database transaction
            DB::beginTransaction();

            try {
                // Delete user data in proper order to avoid foreign key constraints
                $this->deleteUserData($user);

                // Delete the user account
                $user->delete();

                // Update deletion request status
                DataDeletionRequest::where('user_id', $userId)
                    ->update([
                        'status' => 'completed',
                        'deleted_at' => now()
                    ]);

                DB::commit();

                Log::info('Account permanently deleted', [
                    'user_id' => $userId,
                    'email' => $user->email,
                    'deleted_by' => $currentUser->id
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Account deleted permanently'
                ]);

            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Account deletion execution failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to delete account permanently'
            ], 500);
        }
    }

    /**
     * Delete all user data
     */
    private function deleteUserData($user)
    {
        // Delete user's bio sites
        DB::table('bio_sites')->where('user_id', $user->id)->delete();

        // Delete user's workspaces
        DB::table('workspaces')->where('user_id', $user->id)->delete();

        // Delete user's email campaigns
        DB::table('email_campaigns')->where('user_id', $user->id)->delete();

        // Delete user's booking services
        DB::table('booking_services')->where('user_id', $user->id)->delete();

        // Delete user's escrow transactions
        DB::table('escrow_transactions')->where('buyer_id', $user->id)
            ->orWhere('seller_id', $user->id)->delete();

        // Delete user's payment transactions
        DB::table('payment_transactions')->where('user_id', $user->id)->delete();

        // Delete user's subscriptions
        DB::table('user_subscriptions')->where('user_id', $user->id)->delete();

        // Delete user's achievements
        DB::table('user_achievements')->where('user_id', $user->id)->delete();

        // Delete user's XP events
        DB::table('xp_events')->where('user_id', $user->id)->delete();

        // Delete user's streaks
        DB::table('streaks')->where('user_id', $user->id)->delete();

        // Delete user's leaderboard entries
        DB::table('leaderboard_entries')->where('user_id', $user->id)->delete();

        // Delete user's challenges
        DB::table('user_challenges')->where('user_id', $user->id)->delete();

        // Delete user's course enrollments
        DB::table('course_enrollments')->where('user_id', $user->id)->delete();

        // Delete user's lesson completions
        DB::table('lesson_completions')->where('user_id', $user->id)->delete();

        // Delete user's quiz completions
        DB::table('quiz_completions')->where('user_id', $user->id)->delete();

        // Delete user's API tokens
        DB::table('personal_access_tokens')->where('tokenable_id', $user->id)->delete();

        // Delete user's audit logs
        DB::table('audit_logs')->where('user_id', $user->id)->delete();

        Log::info('User data deleted', ['user_id' => $user->id]);
    }

    /**
     * Export user data (GDPR compliance)
     */
    public function exportUserData(Request $request)
    {
        try {
            $user = $request->user();

            $userData = [
                'user_profile' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at
                ],
                'bio_sites' => DB::table('bio_sites')->where('user_id', $user->id)->get(),
                'workspaces' => DB::table('workspaces')->where('user_id', $user->id)->get(),
                'email_campaigns' => DB::table('email_campaigns')->where('user_id', $user->id)->get(),
                'booking_services' => DB::table('booking_services')->where('user_id', $user->id)->get(),
                'payment_transactions' => DB::table('payment_transactions')->where('user_id', $user->id)->get(),
                'subscriptions' => DB::table('user_subscriptions')->where('user_id', $user->id)->get(),
                'achievements' => DB::table('user_achievements')->where('user_id', $user->id)->get(),
                'course_enrollments' => DB::table('course_enrollments')->where('user_id', $user->id)->get()
            ];

            // Log export request
            Log::info('User data exported', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return response()->json([
                'success' => true,
                'data' => $userData,
                'exported_at' => now()->format('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            Log::error('User data export failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to export user data'
            ], 500);
        }
    }
}