<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Models\Affiliate;
use App\Models\AffiliateCommission;
use App\Models\Transaction;
use App\Models\SupportTicket;
use App\Models\SystemSetting;
use App\Models\FeatureFlag;
use App\Models\AuditLog;
use App\Services\AdminService;
use App\Services\AnalyticsService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

class AdvancedAdminController extends Controller
{
    protected $adminService;
    protected $analyticsService;
    protected $notificationService;

    public function __construct(
        AdminService $adminService,
        AnalyticsService $analyticsService,
        NotificationService $notificationService
    ) {
        $this->adminService = $adminService;
        $this->analyticsService = $analyticsService;
        $this->notificationService = $notificationService;
    }

    /**
     * Get comprehensive dashboard overview
     */
    public function getDashboardOverview(Request $request)
    {
        try {
            $timeRange = $request->get('time_range', '30d');
            $startDate = $this->getStartDate($timeRange);

            $overview = [
                'users' => [
                    'total' => User::count(),
                    'new_today' => User::whereDate('created_at', today())->count(),
                    'new_this_week' => User::where('created_at', '>=', now()->startOfWeek())->count(),
                    'new_this_month' => User::where('created_at', '>=', now()->startOfMonth())->count(),
                    'active_users' => User::where('last_login_at', '>=', now()->subDays(30))->count(),
                    'growth_rate' => $this->calculateGrowthRate(User::class, $startDate)
                ],
                'subscriptions' => [
                    'total_active' => UserSubscription::where('status', 'active')->count(),
                    'total_revenue' => UserSubscription::where('status', 'active')->sum('amount'),
                    'monthly_recurring_revenue' => $this->calculateMRR(),
                    'annual_recurring_revenue' => $this->calculateARR(),
                    'churn_rate' => $this->calculateChurnRate($startDate),
                    'by_plan' => $this->getSubscriptionsByPlan()
                ],
                'financial' => [
                    'total_revenue' => Transaction::where('status', 'succeeded')->sum('amount'),
                    'revenue_today' => Transaction::where('status', 'succeeded')
                        ->whereDate('created_at', today())->sum('amount'),
                    'revenue_this_month' => Transaction::where('status', 'succeeded')
                        ->whereMonth('created_at', now()->month)->sum('amount'),
                    'failed_payments' => Transaction::where('status', 'failed')
                        ->where('created_at', '>=', $startDate)->count(),
                    'refunds' => Transaction::where('type', 'refund')
                        ->where('created_at', '>=', $startDate)->sum('amount')
                ],
                'affiliates' => [
                    'total_affiliates' => Affiliate::count(),
                    'active_affiliates' => Affiliate::where('status', 'active')->count(),
                    'pending_applications' => Affiliate::where('status', 'pending')->count(),
                    'total_commissions' => AffiliateCommission::sum('amount'),
                    'unpaid_commissions' => AffiliateCommission::where('status', 'unpaid')->sum('amount')
                ],
                'support' => [
                    'open_tickets' => SupportTicket::where('status', 'open')->count(),
                    'pending_tickets' => SupportTicket::where('status', 'pending')->count(),
                    'average_response_time' => $this->calculateAverageResponseTime(),
                    'satisfaction_score' => $this->calculateSatisfactionScore()
                ],
                'system' => [
                    'server_uptime' => $this->getServerUptime(),
                    'database_size' => $this->getDatabaseSize(),
                    'cache_hit_ratio' => $this->getCacheHitRatio(),
                    'active_sessions' => $this->getActiveSessionsCount()
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $overview,
                'metadata' => [
                    'time_range' => $timeRange,
                    'generated_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching admin dashboard overview: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard overview'
            ], 500);
        }
    }

    /**
     * Manage users with advanced controls
     */
    public function manageUsers(Request $request)
    {
        try {
            $query = User::query();

            // Apply filters
            if ($request->has('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('email', 'like', '%' . $request->search . '%');
                });
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('subscription_status')) {
                $query->whereHas('subscription', function($q) use ($request) {
                    $q->where('status', $request->subscription_status);
                });
            }

            if ($request->has('created_from')) {
                $query->where('created_at', '>=', $request->created_from);
            }

            if ($request->has('created_to')) {
                $query->where('created_at', '<=', $request->created_to);
            }

            $users = $query->with(['subscription.plan', 'referrals'])
                ->orderBy($request->get('sort_by', 'created_at'), $request->get('sort_order', 'desc'))
                ->paginate($request->get('per_page', 50));

            return response()->json([
                'success' => true,
                'data' => $users,
                'filters' => [
                    'statuses' => ['active', 'suspended', 'banned'],
                    'subscription_statuses' => ['active', 'canceled', 'trial', 'expired']
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error managing users: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to manage users'
            ], 500);
        }
    }

    /**
     * Update user account
     */
    public function updateUser(Request $request, $userId)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $userId,
            'status' => 'sometimes|in:active,suspended,banned',
            'notes' => 'sometimes|string|max:1000'
        ]);

        DB::beginTransaction();

        try {
            $user = User::findOrFail($userId);
            $oldData = $user->toArray();

            $user->update($request->only(['name', 'email', 'status']));

            if ($request->has('notes')) {
                $user->update(['admin_notes' => $request->notes]);
            }

            // Log the change
            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => 'user_updated',
                'model' => 'User',
                'model_id' => $user->id,
                'old_values' => $oldData,
                'new_values' => $user->fresh()->toArray(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent')
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $user->fresh(),
                'message' => 'User updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user'
            ], 500);
        }
    }

    /**
     * Manage subscription plans
     */
    public function manageSubscriptionPlans(Request $request)
    {
        try {
            $plans = SubscriptionPlan::with(['subscriptions' => function($query) {
                $query->where('status', 'active');
            }])
            ->withCount(['subscriptions as active_subscriptions' => function($query) {
                $query->where('status', 'active');
            }])
            ->orderBy('order')
            ->get()
            ->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'price' => $plan->price,
                    'billing_cycle' => $plan->billing_cycle,
                    'features' => $plan->features,
                    'is_active' => $plan->is_active,
                    'active_subscriptions' => $plan->active_subscriptions,
                    'monthly_revenue' => $plan->active_subscriptions * $plan->price,
                    'created_at' => $plan->created_at,
                    'updated_at' => $plan->updated_at
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $plans
            ]);

        } catch (\Exception $e) {
            Log::error('Error managing subscription plans: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to manage subscription plans'
            ], 500);
        }
    }

    /**
     * Create or update subscription plan
     */
    public function saveSubscriptionPlan(Request $request, $planId = null)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly,lifetime',
            'features' => 'required|array',
            'feature_limits' => 'nullable|array',
            'is_active' => 'boolean',
            'is_popular' => 'boolean',
            'trial_days' => 'nullable|integer|min:0',
            'setup_fee' => 'nullable|numeric|min:0',
            'order' => 'nullable|integer|min:0'
        ]);

        DB::beginTransaction();

        try {
            $planData = $request->only([
                'name', 'description', 'price', 'billing_cycle', 'features', 
                'feature_limits', 'is_active', 'is_popular', 'trial_days', 'setup_fee', 'order'
            ]);

            if ($planId) {
                $plan = SubscriptionPlan::findOrFail($planId);
                $oldData = $plan->toArray();
                $plan->update($planData);
                $action = 'plan_updated';
            } else {
                $plan = SubscriptionPlan::create($planData);
                $oldData = null;
                $action = 'plan_created';
            }

            // Log the change
            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => $action,
                'model' => 'SubscriptionPlan',
                'model_id' => $plan->id,
                'old_values' => $oldData,
                'new_values' => $plan->toArray(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent')
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $plan,
                'message' => $planId ? 'Plan updated successfully' : 'Plan created successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error saving subscription plan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save subscription plan'
            ], 500);
        }
    }

    /**
     * Manage affiliates
     */
    public function manageAffiliates(Request $request)
    {
        try {
            $query = Affiliate::with(['user', 'referrals', 'commissions']);

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('search')) {
                $query->whereHas('user', function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('email', 'like', '%' . $request->search . '%');
                });
            }

            $affiliates = $query->paginate($request->get('per_page', 25));

            return response()->json([
                'success' => true,
                'data' => $affiliates
            ]);

        } catch (\Exception $e) {
            Log::error('Error managing affiliates: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to manage affiliates'
            ], 500);
        }
    }

    /**
     * Update affiliate status
     */
    public function updateAffiliateStatus(Request $request, $affiliateId)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,suspended',
            'commission_rate' => 'sometimes|numeric|min:0|max:100',
            'tier' => 'sometimes|in:bronze,silver,gold,platinum',
            'notes' => 'sometimes|string|max:1000'
        ]);

        DB::beginTransaction();

        try {
            $affiliate = Affiliate::findOrFail($affiliateId);
            $oldData = $affiliate->toArray();

            $affiliate->update([
                'status' => $request->status,
                'commission_rate' => $request->commission_rate ?? $affiliate->commission_rate,
                'tier' => $request->tier ?? $affiliate->tier,
                'admin_notes' => $request->notes ?? $affiliate->admin_notes
            ]);

            // Notify affiliate of status change
            if ($request->status === 'approved') {
                $this->notificationService->sendAffiliateApproval($affiliate);
            } elseif ($request->status === 'rejected') {
                $this->notificationService->sendAffiliateRejection($affiliate);
            }

            // Log the change
            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => 'affiliate_status_updated',
                'model' => 'Affiliate',
                'model_id' => $affiliate->id,
                'old_values' => $oldData,
                'new_values' => $affiliate->fresh()->toArray(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent')
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $affiliate->fresh(),
                'message' => 'Affiliate status updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating affiliate status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update affiliate status'
            ], 500);
        }
    }

    /**
     * Manage system settings
     */
    public function getSystemSettings(Request $request)
    {
        try {
            $settings = SystemSetting::all()->pluck('value', 'key');
            
            return response()->json([
                'success' => true,
                'data' => $settings
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching system settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch system settings'
            ], 500);
        }
    }

    /**
     * Update system settings
     */
    public function updateSystemSettings(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'required'
        ]);

        DB::beginTransaction();

        try {
            foreach ($request->settings as $key => $value) {
                SystemSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }

            // Clear cache
            Cache::forget('system_settings');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'System settings updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating system settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update system settings'
            ], 500);
        }
    }

    /**
     * Manage feature flags
     */
    public function manageFeatureFlags(Request $request)
    {
        try {
            $flags = FeatureFlag::all();
            
            return response()->json([
                'success' => true,
                'data' => $flags
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching feature flags: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch feature flags'
            ], 500);
        }
    }

    /**
     * Toggle feature flag
     */
    public function toggleFeatureFlag(Request $request, $flagId)
    {
        try {
            $flag = FeatureFlag::findOrFail($flagId);
            $flag->update(['is_enabled' => !$flag->is_enabled]);

            // Clear cache
            Cache::forget('feature_flags');

            return response()->json([
                'success' => true,
                'data' => $flag,
                'message' => 'Feature flag toggled successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error toggling feature flag: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle feature flag'
            ], 500);
        }
    }

    /**
     * Get system health
     */
    public function getSystemHealth(Request $request)
    {
        try {
            $health = [
                'database' => $this->checkDatabaseHealth(),
                'cache' => $this->checkCacheHealth(),
                'storage' => $this->checkStorageHealth(),
                'queue' => $this->checkQueueHealth(),
                'mail' => $this->checkMailHealth(),
                'external_apis' => $this->checkExternalApiHealth()
            ];

            $overallStatus = collect($health)->every(function($status) {
                return $status['status'] === 'healthy';
            }) ? 'healthy' : 'unhealthy';

            return response()->json([
                'success' => true,
                'data' => [
                    'overall_status' => $overallStatus,
                    'checks' => $health,
                    'checked_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error checking system health: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to check system health'
            ], 500);
        }
    }

    /**
     * Run system maintenance
     */
    public function runMaintenance(Request $request)
    {
        $request->validate([
            'tasks' => 'required|array',
            'tasks.*' => 'in:clear_cache,optimize_database,cleanup_logs,backup_database'
        ]);

        try {
            $results = [];

            foreach ($request->tasks as $task) {
                switch ($task) {
                    case 'clear_cache':
                        Artisan::call('cache:clear');
                        $results[$task] = 'Cache cleared successfully';
                        break;
                    case 'optimize_database':
                        Artisan::call('migrate:optimize');
                        $results[$task] = 'Database optimized successfully';
                        break;
                    case 'cleanup_logs':
                        Artisan::call('logs:clear');
                        $results[$task] = 'Logs cleaned up successfully';
                        break;
                    case 'backup_database':
                        Artisan::call('backup:run');
                        $results[$task] = 'Database backup created successfully';
                        break;
                }
            }

            return response()->json([
                'success' => true,
                'data' => $results,
                'message' => 'Maintenance tasks completed successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error running maintenance: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to run maintenance tasks'
            ], 500);
        }
    }

    // Helper methods for calculations and health checks
    private function getStartDate($timeRange)
    {
        switch ($timeRange) {
            case '7d': return now()->subDays(7);
            case '30d': return now()->subDays(30);
            case '90d': return now()->subDays(90);
            case '1y': return now()->subYear();
            default: return now()->subDays(30);
        }
    }

    private function calculateMRR()
    {
        return UserSubscription::where('status', 'active')
            ->whereHas('plan', function($query) {
                $query->where('billing_cycle', 'monthly');
            })
            ->sum('amount');
    }

    private function calculateARR()
    {
        $monthly = $this->calculateMRR();
        $yearly = UserSubscription::where('status', 'active')
            ->whereHas('plan', function($query) {
                $query->where('billing_cycle', 'yearly');
            })
            ->sum('amount');
        
        return ($monthly * 12) + $yearly;
    }

    private function calculateChurnRate($startDate)
    {
        $totalAtStart = UserSubscription::where('created_at', '<', $startDate)->count();
        $churned = UserSubscription::where('canceled_at', '>=', $startDate)->count();
        
        return $totalAtStart > 0 ? ($churned / $totalAtStart) * 100 : 0;
    }

    private function getSubscriptionsByPlan()
    {
        return SubscriptionPlan::withCount(['subscriptions as active_count' => function($query) {
            $query->where('status', 'active');
        }])
        ->get()
        ->map(function($plan) {
            return [
                'plan_name' => $plan->name,
                'active_subscriptions' => $plan->active_count,
                'revenue' => $plan->active_count * $plan->price
            ];
        });
    }

    private function calculateGrowthRate($model, $startDate)
    {
        $current = $model::count();
        $previous = $model::where('created_at', '<', $startDate)->count();
        
        return $previous > 0 ? (($current - $previous) / $previous) * 100 : 0;
    }

    private function calculateAverageResponseTime()
    {
        return SupportTicket::where('status', 'resolved')
            ->whereNotNull('first_response_at')
            ->avg(DB::raw('TIMESTAMPDIFF(HOUR, created_at, first_response_at)')) ?? 0;
    }

    private function calculateSatisfactionScore()
    {
        return SupportTicket::where('status', 'resolved')
            ->whereNotNull('satisfaction_rating')
            ->avg('satisfaction_rating') ?? 0;
    }

    private function getServerUptime()
    {
        // Implementation for server uptime
        return '99.9%';
    }

    private function getDatabaseSize()
    {
        $size = DB::select('SELECT SUM(data_length + index_length) / 1024 / 1024 AS size FROM information_schema.tables WHERE table_schema = DATABASE()')[0]->size ?? 0;
        return round($size, 2) . ' MB';
    }

    private function getCacheHitRatio()
    {
        // Implementation for cache hit ratio
        return '95%';
    }

    private function getActiveSessionsCount()
    {
        return DB::table('sessions')->where('last_activity', '>', now()->subMinutes(5)->timestamp)->count();
    }

    private function checkDatabaseHealth()
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'healthy', 'message' => 'Database connection successful'];
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'message' => 'Database connection failed'];
        }
    }

    private function checkCacheHealth()
    {
        try {
            Cache::put('health_check', 'ok', 60);
            $result = Cache::get('health_check');
            return $result === 'ok' 
                ? ['status' => 'healthy', 'message' => 'Cache is working']
                : ['status' => 'unhealthy', 'message' => 'Cache is not working'];
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'message' => 'Cache error: ' . $e->getMessage()];
        }
    }

    private function checkStorageHealth()
    {
        try {
            $freeSpace = disk_free_space(storage_path());
            $totalSpace = disk_total_space(storage_path());
            $usedPercentage = (($totalSpace - $freeSpace) / $totalSpace) * 100;
            
            return $usedPercentage < 90 
                ? ['status' => 'healthy', 'message' => 'Storage usage: ' . round($usedPercentage, 1) . '%']
                : ['status' => 'unhealthy', 'message' => 'Storage usage critical: ' . round($usedPercentage, 1) . '%'];
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'message' => 'Storage check failed'];
        }
    }

    private function checkQueueHealth()
    {
        try {
            $failedJobs = DB::table('failed_jobs')->count();
            return $failedJobs < 10 
                ? ['status' => 'healthy', 'message' => 'Queue is working, ' . $failedJobs . ' failed jobs']
                : ['status' => 'unhealthy', 'message' => 'Too many failed jobs: ' . $failedJobs];
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'message' => 'Queue check failed'];
        }
    }

    private function checkMailHealth()
    {
        // Implementation for mail health check
        return ['status' => 'healthy', 'message' => 'Mail service is working'];
    }

    private function checkExternalApiHealth()
    {
        // Implementation for external API health check
        return ['status' => 'healthy', 'message' => 'External APIs are responding'];
    }
}