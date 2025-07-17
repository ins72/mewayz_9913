<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Models\ReferralReward;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ReferralController extends Controller
{
    /**
     * Get user's referral dashboard
     */
    public function dashboard(Request $request)
    {
        try {
            $user = Auth::user();
            $workspaceId = $request->workspace_id;

            // Get referral statistics
            $referrals = Referral::where('referrer_id', $user->id);
            
            if ($workspaceId) {
                $referrals->where('workspace_id', $workspaceId);
            }

            $totalReferrals = $referrals->count();
            $successfulReferrals = $referrals->where('status', 'completed')->count();
            $pendingReferrals = $referrals->where('status', 'pending')->count();
            $totalEarnings = $referrals->where('status', 'completed')->sum('reward_amount');

            // Get recent referrals
            $recentReferrals = $referrals->with('referee:id,name,email')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // Get referral rewards
            $rewards = ReferralReward::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // Get referral link
            $referralCode = $user->referral_code ?: $this->generateReferralCode($user);

            return response()->json([
                'success' => true,
                'data' => [
                    'stats' => [
                        'total_referrals' => $totalReferrals,
                        'successful_referrals' => $successfulReferrals,
                        'pending_referrals' => $pendingReferrals,
                        'total_earnings' => $totalEarnings,
                        'conversion_rate' => $totalReferrals > 0 ? round(($successfulReferrals / $totalReferrals) * 100, 2) : 0,
                    ],
                    'referral_code' => $referralCode,
                    'referral_link' => url("/register?ref={$referralCode}"),
                    'recent_referrals' => $recentReferrals,
                    'rewards' => $rewards,
                ],
                'message' => 'Referral dashboard retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve referral dashboard: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve referral dashboard'
            ], 500);
        }
    }

    /**
     * Send referral invitations
     */
    public function sendInvitations(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'emails' => 'required|array|min:1|max:50',
            'emails.*' => 'required|email|max:255',
            'workspace_id' => 'required|exists:workspaces,id',
            'message' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $user = Auth::user();
            $emails = $request->emails;
            $workspaceId = $request->workspace_id;
            $customMessage = $request->message;

            // Generate referral code if not exists
            $referralCode = $user->referral_code ?: $this->generateReferralCode($user);

            $successCount = 0;
            $failedEmails = [];

            foreach ($emails as $email) {
                try {
                    // Check if user already exists
                    $existingUser = User::where('email', $email)->first();
                    
                    if ($existingUser) {
                        $failedEmails[] = $email . ' (already registered)';
                        continue;
                    }

                    // Check if already referred
                    $existingReferral = Referral::where('referrer_id', $user->id)
                        ->where('referee_email', $email)
                        ->first();

                    if ($existingReferral) {
                        $failedEmails[] = $email . ' (already referred)';
                        continue;
                    }

                    // Create referral record
                    $referral = Referral::create([
                        'referrer_id' => $user->id,
                        'workspace_id' => $workspaceId,
                        'referee_email' => $email,
                        'referral_code' => $referralCode,
                        'status' => 'sent',
                        'invitation_sent_at' => now(),
                        'custom_message' => $customMessage,
                    ]);

                    // Send invitation email
                    $this->sendReferralEmail($user, $email, $referralCode, $customMessage);

                    $successCount++;
                } catch (\Exception $e) {
                    $failedEmails[] = $email . ' (failed to send)';
                    Log::error("Failed to send referral to {$email}: " . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Sent {$successCount} referral invitations successfully",
                'data' => [
                    'sent_count' => $successCount,
                    'failed_emails' => $failedEmails,
                    'referral_code' => $referralCode,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send referral invitations: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send referral invitations'
            ], 500);
        }
    }

    /**
     * Process referral signup
     */
    public function processReferral(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'referral_code' => 'required|string|max:50',
            'referee_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $referralCode = $request->referral_code;
            $refereeId = $request->referee_id;

            // Find the referrer
            $referrer = User::where('referral_code', $referralCode)->first();

            if (!$referrer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid referral code'
                ], 400);
            }

            // Find the referee
            $referee = User::find($refereeId);

            if (!$referee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid referee'
                ], 400);
            }

            // Check if referral record exists
            $referral = Referral::where('referrer_id', $referrer->id)
                ->where('referee_email', $referee->email)
                ->first();

            if (!$referral) {
                // Create new referral record for organic referrals
                $referral = Referral::create([
                    'referrer_id' => $referrer->id,
                    'referee_id' => $refereeId,
                    'referee_email' => $referee->email,
                    'referral_code' => $referralCode,
                    'status' => 'pending',
                    'signed_up_at' => now(),
                ]);
            } else {
                // Update existing referral
                $referral->update([
                    'referee_id' => $refereeId,
                    'status' => 'pending',
                    'signed_up_at' => now(),
                ]);
            }

            // Process immediate rewards (if any)
            $this->processSignupReward($referral);

            return response()->json([
                'success' => true,
                'message' => 'Referral processed successfully',
                'data' => [
                    'referral_id' => $referral->id,
                    'status' => $referral->status,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to process referral: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to process referral'
            ], 500);
        }
    }

    /**
     * Complete referral (when referee makes a qualifying action)
     */
    public function completeReferral(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'referee_id' => 'required|exists:users,id',
            'qualifying_action' => 'required|string|in:subscription,purchase,workspace_creation',
            'action_value' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $refereeId = $request->referee_id;
            $qualifyingAction = $request->qualifying_action;
            $actionValue = $request->action_value ?? 0;

            // Find the referral
            $referral = Referral::where('referee_id', $refereeId)
                ->where('status', 'pending')
                ->first();

            if (!$referral) {
                return response()->json([
                    'success' => false,
                    'message' => 'No pending referral found'
                ], 400);
            }

            // Calculate reward amount based on action
            $rewardAmount = $this->calculateRewardAmount($qualifyingAction, $actionValue);

            // Update referral status
            $referral->update([
                'status' => 'completed',
                'qualifying_action' => $qualifyingAction,
                'action_value' => $actionValue,
                'reward_amount' => $rewardAmount,
                'completed_at' => now(),
            ]);

            // Create reward record
            $reward = ReferralReward::create([
                'user_id' => $referral->referrer_id,
                'referral_id' => $referral->id,
                'reward_type' => 'commission',
                'amount' => $rewardAmount,
                'currency' => 'USD',
                'description' => "Referral reward for {$qualifyingAction}",
                'status' => 'pending',
            ]);

            // Process the reward (add to user's wallet, etc.)
            $this->processReward($reward);

            return response()->json([
                'success' => true,
                'message' => 'Referral completed successfully',
                'data' => [
                    'referral_id' => $referral->id,
                    'reward_amount' => $rewardAmount,
                    'reward_id' => $reward->id,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to complete referral: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete referral'
            ], 500);
        }
    }

    /**
     * Get referral analytics
     */
    public function analytics(Request $request)
    {
        try {
            $user = Auth::user();
            $workspaceId = $request->workspace_id;
            $period = $request->period ?? 30; // days

            $query = Referral::where('referrer_id', $user->id);
            
            if ($workspaceId) {
                $query->where('workspace_id', $workspaceId);
            }

            // Get referrals by date
            $referralsByDate = $query->where('created_at', '>=', now()->subDays($period))
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->get();

            // Get referrals by status
            $referralsByStatus = $query->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get();

            // Get referrals by qualifying action
            $referralsByAction = $query->where('status', 'completed')
                ->selectRaw('qualifying_action, COUNT(*) as count, SUM(reward_amount) as total_reward')
                ->groupBy('qualifying_action')
                ->get();

            // Get top performing periods
            $topPeriods = $query->where('status', 'completed')
                ->selectRaw('YEAR(completed_at) as year, MONTH(completed_at) as month, COUNT(*) as count, SUM(reward_amount) as total_reward')
                ->groupBy('year', 'month')
                ->orderBy('total_reward', 'desc')
                ->limit(12)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'referrals_by_date' => $referralsByDate,
                    'referrals_by_status' => $referralsByStatus,
                    'referrals_by_action' => $referralsByAction,
                    'top_periods' => $topPeriods,
                ],
                'message' => 'Referral analytics retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve referral analytics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve referral analytics'
            ], 500);
        }
    }

    /**
     * Get referral rewards
     */
    public function rewards(Request $request)
    {
        try {
            $user = Auth::user();
            $status = $request->status; // pending, paid, cancelled

            $query = ReferralReward::where('user_id', $user->id);

            if ($status) {
                $query->where('status', $status);
            }

            $rewards = $query->orderBy('created_at', 'desc')->paginate(20);

            // Get reward summary
            $summary = [
                'total_pending' => ReferralReward::where('user_id', $user->id)->where('status', 'pending')->sum('amount'),
                'total_paid' => ReferralReward::where('user_id', $user->id)->where('status', 'paid')->sum('amount'),
                'total_cancelled' => ReferralReward::where('user_id', $user->id)->where('status', 'cancelled')->sum('amount'),
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'rewards' => $rewards,
                    'summary' => $summary,
                ],
                'message' => 'Referral rewards retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve referral rewards: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve referral rewards'
            ], 500);
        }
    }

    /**
     * Generate referral code for user
     */
    private function generateReferralCode($user)
    {
        do {
            $code = strtoupper(substr($user->name, 0, 3) . Str::random(5));
        } while (User::where('referral_code', $code)->exists());

        $user->update(['referral_code' => $code]);
        return $code;
    }

    /**
     * Send referral email
     */
    private function sendReferralEmail($referrer, $email, $referralCode, $customMessage = null)
    {
        // This is a placeholder - implement actual email sending
        // You would use Laravel's Mail facade here
        
        $referralLink = url("/register?ref={$referralCode}");
        
        // Mock email sending
        Log::info("Referral email sent to {$email} from {$referrer->name} with code {$referralCode}");
        
        // In a real implementation, you would send an actual email:
        // Mail::to($email)->send(new ReferralInvitation($referrer, $referralLink, $customMessage));
    }

    /**
     * Process signup reward
     */
    private function processSignupReward($referral)
    {
        // Implement signup reward logic here
        // This could be credits, bonus features, etc.
        
        Log::info("Processing signup reward for referral {$referral->id}");
    }

    /**
     * Calculate reward amount based on qualifying action
     */
    private function calculateRewardAmount($action, $actionValue)
    {
        switch ($action) {
            case 'subscription':
                return $actionValue * 0.20; // 20% commission
            case 'purchase':
                return $actionValue * 0.15; // 15% commission
            case 'workspace_creation':
                return 5.00; // Fixed $5 reward
            default:
                return 0;
        }
    }

    /**
     * Process reward (add to user's wallet, etc.)
     */
    private function processReward($reward)
    {
        // Implement reward processing logic
        // This could update user's wallet, create payment records, etc.
        
        $reward->update(['status' => 'paid', 'paid_at' => now()]);
        
        Log::info("Processed reward {$reward->id} for user {$reward->user_id}");
    }
}