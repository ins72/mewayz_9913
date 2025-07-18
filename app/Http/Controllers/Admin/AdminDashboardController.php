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
                'active_users' => User::where('status', 1)->count(),
                'new_users_today' => User::whereDate('created_at', today())->count(),
                'new_users_this_week' => User::where('created_at', '>=', now()->startOfWeek())->count(),
                'new_users_this_month' => User::where('created_at', '>=', now()->startOfMonth())->count(),
                'total_subscriptions' => SubscriptionPlan::count(),
                'active_subscriptions' => SubscriptionPlan::where('status', 'active')->count(),
                'total_revenue' => $this->getTotalRevenue(),
                'monthly_revenue' => $this->getMonthlyRevenue(),
                'total_gamification_users' => UserLevel::count(),
                'total_achievements' => Achievement::count(),
                'total_xp_awarded' => XpEvent::sum('final_xp'),
                'admin_actions_today' => AdminActivityLog::whereDate('created_at', today())->count()
            ];

            // Recent Activity
            $recentActivity = AdminActivityLog::with('adminUser')
                                             ->orderBy('created_at', 'desc')
                                             ->limit(10)
                                             ->get();

            // User Growth Chart Data
            $userGrowthData = $this->getUserGrowthData();
            
            // Revenue Chart Data
            $revenueData = $this->getRevenueData();
            
            // Plan Distribution
            $planDistribution = $this->getPlanDistribution();
            
            // System Health
            $systemHealth = $this->getSystemHealth();

            // Top Performing Plans
            $topPlans = SubscriptionPlan::withCount('assignments')
                                       ->where('status', 'active')
                                       ->orderBy('assignments_count', 'desc')
                                       ->limit(5)
                                       ->get();

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
}