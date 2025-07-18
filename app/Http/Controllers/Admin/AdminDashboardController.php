<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Admin\SubscriptionPlan;
use App\Models\Admin\AdminActivityLog;
use App\Models\Admin\BulkOperation;
use App\Models\Gamification\UserLevel;
use App\Models\Gamification\Achievement;
use App\Models\Gamification\XpEvent;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $adminUser = $request->user();
            
            // Overview Statistics
            $stats = [
                'total_users' => User::count(),
                'active_users' => User::where('status', 'active')->count(),
                'new_users_today' => User::whereDate('created_at', today())->count(),
                'new_users_this_week' => User::where('created_at', '>=', now()->startOfWeek())->count(),
                'new_users_this_month' => User::where('created_at', '>=', now()->startOfMonth())->count(),
                'total_subscriptions' => $this->safeCount(SubscriptionPlan::class),
                'active_subscriptions' => $this->safeCount(SubscriptionPlan::class, ['status' => 'active']),
                'total_revenue' => $this->getTotalRevenue(),
                'monthly_revenue' => $this->getMonthlyRevenue(),
                'total_gamification_users' => $this->safeCount(UserLevel::class),
                'total_achievements' => $this->safeCount(Achievement::class),
                'total_xp_awarded' => $this->safeSum(XpEvent::class, 'final_xp'),
                'admin_actions_today' => $this->safeCount(AdminActivityLog::class, ['created_at' => today()])
            ];

            // Recent Activity
            $recentActivity = $this->safeGetRecentActivity();
            
            // User Growth Chart Data
            $userGrowthData = $this->getUserGrowthData();
            
            // Revenue Chart Data
            $revenueData = $this->getRevenueData();
            
            // Plan Distribution
            $planDistribution = $this->getPlanDistribution();
            
            // System Health
            $systemHealth = $this->getSystemHealth();

            // Top Performing Plans
            $topPlans = $this->safeGetTopPlans();

            return response()->json([
                'success' => true,
                'data' => [
                    'stats' => $stats,
                    'recent_activity' => $recentActivity,
                    'user_growth_data' => $userGrowthData,
                    'revenue_data' => $revenueData,
                    'plan_distribution' => $planDistribution,
                    'system_health' => $systemHealth,
                    'top_plans' => $topPlans,
                    'admin_user' => [
                        'name' => $adminUser->name,
                        'email' => $adminUser->email,
                        'role' => $adminUser->role,
                        'permissions' => $adminUser->permissions,
                        'last_login' => $adminUser->last_login
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Admin dashboard failed', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load dashboard data'
            ], 500);
        }
    }

    private function getTotalRevenue(): float
    {
        // Mock revenue calculation - replace with actual logic
        return 125000.00;
    }

    private function getMonthlyRevenue(): float
    {
        // Mock monthly revenue - replace with actual logic
        return 15000.00;
    }

    private function getUserGrowthData(): array
    {
        $data = [];
        $startDate = now()->subDays(30);
        
        for ($i = 0; $i < 30; $i++) {
            $date = $startDate->copy()->addDays($i);
            $data[] = [
                'date' => $date->format('Y-m-d'),
                'users' => User::whereDate('created_at', $date)->count()
            ];
        }
        
        return $data;
    }

    private function getRevenueData(): array
    {
        // Mock revenue data - replace with actual logic
        $data = [];
        $startDate = now()->subDays(30);
        
        for ($i = 0; $i < 30; $i++) {
            $date = $startDate->copy()->addDays($i);
            $data[] = [
                'date' => $date->format('Y-m-d'),
                'revenue' => rand(300, 1200) // Mock data
            ];
        }
        
        return $data;
    }

    private function getPlanDistribution(): array
    {
        return SubscriptionPlan::select('name', DB::raw('COUNT(*) as count'))
                              ->groupBy('name')
                              ->get()
                              ->toArray();
    }

    private function getSystemHealth(): array
    {
        return [
            'status' => 'healthy',
            'uptime' => '99.9%',
            'response_time' => '250ms',
            'disk_usage' => '45%',
            'memory_usage' => '62%',
            'cpu_usage' => '23%',
            'active_connections' => 145,
            'queue_size' => 12
        ];
    }

    private function safeCount($modelClass, $conditions = [])
    {
        try {
            $query = $modelClass::query();
            foreach ($conditions as $field => $value) {
                if ($field === 'created_at' && $value instanceof \Carbon\Carbon) {
                    $query->whereDate($field, $value);
                } else {
                    $query->where($field, $value);
                }
            }
            return $query->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function safeSum($modelClass, $field)
    {
        try {
            return $modelClass::sum($field) ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }
}