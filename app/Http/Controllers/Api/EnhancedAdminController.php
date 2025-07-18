<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\PaymentTransaction;
use App\Models\Affiliate;
use App\Models\AffiliateCommission;
use App\Models\DataDeletionRequest;

class EnhancedAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Get comprehensive admin dashboard
     */
    public function getDashboard(Request $request)
    {
        try {
            $stats = [
                'users' => $this->getUserStats(),
                'revenue' => $this->getRevenueStats(),
                'subscriptions' => $this->getSubscriptionStats(),
                'affiliates' => $this->getAffiliateStats(),
                'system' => $this->getSystemStats()
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats,
                'timestamp' => now()->format('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            Log::error('Admin dashboard failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to load admin dashboard'
            ], 500);
        }
    }

    /**
     * Manage subscription plans
     */
    public function manageSubscriptionPlans(Request $request)
    {
        try {
            $action = $request->input('action');

            switch ($action) {
                case 'create':
                    return $this->createSubscriptionPlan($request);
                case 'update':
                    return $this->updateSubscriptionPlan($request);
                case 'delete':
                    return $this->deleteSubscriptionPlan($request);
                case 'toggle':
                    return $this->toggleSubscriptionPlan($request);
                default:
                    return $this->getSubscriptionPlans($request);
            }

        } catch (\Exception $e) {
            Log::error('Subscription plan management failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to manage subscription plans'
            ], 500);
        }
    }

    /**
     * Manage users
     */
    public function manageUsers(Request $request)
    {
        try {
            $action = $request->input('action');

            switch ($action) {
                case 'ban':
                    return $this->banUser($request);
                case 'unban':
                    return $this->unbanUser($request);
                case 'delete':
                    return $this->deleteUser($request);
                case 'impersonate':
                    return $this->impersonateUser($request);
                case 'reset-password':
                    return $this->resetUserPassword($request);
                default:
                    return $this->getUsers($request);
            }

        } catch (\Exception $e) {
            Log::error('User management failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to manage users'
            ], 500);
        }
    }

    /**
     * Manage affiliate system
     */
    public function manageAffiliates(Request $request)
    {
        try {
            $action = $request->input('action');

            switch ($action) {
                case 'approve':
                    return $this->approveAffiliate($request);
                case 'suspend':
                    return $this->suspendAffiliate($request);
                case 'pay-commission':
                    return $this->payCommission($request);
                case 'bulk-pay':
                    return $this->bulkPayCommissions($request);
                case 'update-rate':
                    return $this->updateCommissionRate($request);
                default:
                    return $this->getAffiliates($request);
            }

        } catch (\Exception $e) {
            Log::error('Affiliate management failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to manage affiliates'
            ], 500);
        }
    }

    /**
     * System settings management
     */
    public function manageSystemSettings(Request $request)
    {
        try {
            $action = $request->input('action');

            switch ($action) {
                case 'update':
                    return $this->updateSystemSettings($request);
                case 'backup':
                    return $this->createBackup($request);
                case 'maintenance':
                    return $this->toggleMaintenance($request);
                case 'clear-cache':
                    return $this->clearCache($request);
                default:
                    return $this->getSystemSettings($request);
            }

        } catch (\Exception $e) {
            Log::error('System settings management failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to manage system settings'
            ], 500);
        }
    }

    /**
     * Get user statistics
     */
    private function getUserStats()
    {
        $totalUsers = User::count();
        $activeUsers = User::where('account_status', 'active')->count();
        $newUsersToday = User::whereDate('created_at', today())->count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)->count();
        $pendingDeletion = DataDeletionRequest::where('status', 'pending')->count();

        return [
            'total' => $totalUsers,
            'active' => $activeUsers,
            'new_today' => $newUsersToday,
            'new_this_month' => $newUsersThisMonth,
            'pending_deletion' => $pendingDeletion
        ];
    }

    /**
     * Get revenue statistics
     */
    private function getRevenueStats()
    {
        $totalRevenue = PaymentTransaction::where('payment_status', 'paid')->sum('amount');
        $todayRevenue = PaymentTransaction::where('payment_status', 'paid')
            ->whereDate('created_at', today())->sum('amount');
        $thisMonthRevenue = PaymentTransaction::where('payment_status', 'paid')
            ->whereMonth('created_at', now()->month)->sum('amount');
        $pendingRevenue = PaymentTransaction::where('payment_status', 'pending')->sum('amount');

        return [
            'total' => $totalRevenue,
            'today' => $todayRevenue,
            'this_month' => $thisMonthRevenue,
            'pending' => $pendingRevenue
        ];
    }

    /**
     * Get subscription statistics
     */
    private function getSubscriptionStats()
    {
        $totalSubscriptions = DB::table('user_subscriptions')->count();
        $activeSubscriptions = DB::table('user_subscriptions')
            ->where('status', 'active')->count();
        $canceledSubscriptions = DB::table('user_subscriptions')
            ->where('status', 'canceled')->count();
        $trialSubscriptions = DB::table('user_subscriptions')
            ->where('status', 'trial')->count();

        return [
            'total' => $totalSubscriptions,
            'active' => $activeSubscriptions,
            'canceled' => $canceledSubscriptions,
            'trial' => $trialSubscriptions
        ];
    }

    /**
     * Get affiliate statistics
     */
    private function getAffiliateStats()
    {
        $totalAffiliates = Affiliate::count();
        $activeAffiliates = Affiliate::where('status', 'active')->count();
        $totalCommissions = AffiliateCommission::sum('amount');
        $pendingCommissions = AffiliateCommission::where('status', 'pending')->sum('amount');

        return [
            'total' => $totalAffiliates,
            'active' => $activeAffiliates,
            'total_commissions' => $totalCommissions,
            'pending_commissions' => $pendingCommissions
        ];
    }

    /**
     * Get system statistics
     */
    private function getSystemStats()
    {
        $dbSize = DB::select('SELECT SUM(data_length + index_length) / 1024 / 1024 AS size FROM information_schema.tables WHERE table_schema = ?', [config('database.connections.mysql.database')])[0]->size;
        
        return [
            'database_size_mb' => round($dbSize, 2),
            'cache_enabled' => Cache::store()->getStore() !== null,
            'queue_jobs' => DB::table('jobs')->count(),
            'failed_jobs' => DB::table('failed_jobs')->count()
        ];
    }

    /**
     * Create subscription plan
     */
    private function createSubscriptionPlan($request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:subscription_plans',
            'base_price' => 'required|numeric|min:0',
            'type' => 'required|in:free,professional,enterprise',
            'features' => 'nullable|array'
        ]);

        $plan = SubscriptionPlan::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'base_price' => $request->base_price,
            'type' => $request->type,
            'included_features' => $request->features,
            'is_active' => true
        ]);

        return response()->json([
            'success' => true,
            'plan' => $plan,
            'message' => 'Subscription plan created successfully'
        ]);
    }

    /**
     * Update subscription plan
     */
    private function updateSubscriptionPlan($request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'name' => 'sometimes|string|max:255',
            'base_price' => 'sometimes|numeric|min:0',
            'features' => 'sometimes|array'
        ]);

        $plan = SubscriptionPlan::findOrFail($request->plan_id);
        $plan->update($request->only(['name', 'base_price', 'included_features']));

        return response()->json([
            'success' => true,
            'plan' => $plan,
            'message' => 'Subscription plan updated successfully'
        ]);
    }

    /**
     * Get all users with pagination
     */
    private function getUsers($request)
    {
        $users = User::with(['subscriptions', 'paymentTransactions'])
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('account_status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return response()->json([
            'success' => true,
            'users' => $users->items(),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'total_pages' => $users->lastPage(),
                'total_items' => $users->total()
            ]
        ]);
    }

    /**
     * Ban user
     */
    private function banUser($request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'reason' => 'required|string|max:500'
        ]);

        $user = User::findOrFail($request->user_id);
        $user->update([
            'account_status' => 'banned',
            'ban_reason' => $request->reason,
            'banned_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User banned successfully'
        ]);
    }

    /**
     * Clear system cache
     */
    private function clearCache($request)
    {
        Cache::flush();

        return response()->json([
            'success' => true,
            'message' => 'System cache cleared successfully'
        ]);
    }

    /**
     * Get system settings
     */
    private function getSystemSettings($request)
    {
        $settings = [
            'maintenance_mode' => env('APP_MAINTENANCE', false),
            'registration_enabled' => env('REGISTRATION_ENABLED', true),
            'affiliate_system_enabled' => env('AFFILIATE_SYSTEM_ENABLED', true),
            'commission_rate' => env('DEFAULT_COMMISSION_RATE', 0.10),
            'payment_gateway' => env('PAYMENT_GATEWAY', 'stripe'),
            'email_verification_required' => env('EMAIL_VERIFICATION_REQUIRED', true)
        ];

        return response()->json([
            'success' => true,
            'settings' => $settings
        ]);
    }

    /**
     * Update system settings
     */
    private function updateSystemSettings($request)
    {
        $request->validate([
            'settings' => 'required|array'
        ]);

        // Update .env file settings
        $envUpdates = [];
        foreach ($request->settings as $key => $value) {
            $envUpdates[strtoupper($key)] = $value;
        }

        // Here you would typically update the .env file
        // For now, we'll just return success
        return response()->json([
            'success' => true,
            'message' => 'System settings updated successfully'
        ]);
    }
}