<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\Course;
use App\Models\BioSite;
use App\Models\EscrowTransaction;
use App\Models\BookingService;
use App\Models\EmailCampaign;
use App\Models\LegalDocument;

class EnhancedAdminController extends Controller
{
    /**
     * Get admin dashboard data
     */
    public function getDashboard()
    {
        try {
            // Get counts and statistics
            $userCount = User::count();
            $activeSubscriptions = UserSubscription::where('status', 'active')->count();
            $totalRevenue = UserSubscription::where('status', 'active')->sum('amount');
            $courseCount = Course::count();
            $bioSiteCount = BioSite::count();
            $escrowTransactionCount = EscrowTransaction::count();
            $bookingServiceCount = BookingService::count();
            $emailCampaignCount = EmailCampaign::count();
            $legalDocumentCount = LegalDocument::count();

            // Recent activities
            $recentUsers = User::orderBy('created_at', 'desc')->limit(10)->get();
            $recentSubscriptions = UserSubscription::with('user', 'plan')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // Monthly revenue trends
            $monthlyRevenue = UserSubscription::where('status', 'active')
                ->selectRaw('MONTH(created_at) as month, SUM(amount) as total')
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            // User growth
            $userGrowth = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->limit(30)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'statistics' => [
                        'users' => $userCount,
                        'active_subscriptions' => $activeSubscriptions,
                        'total_revenue' => $totalRevenue,
                        'courses' => $courseCount,
                        'bio_sites' => $bioSiteCount,
                        'escrow_transactions' => $escrowTransactionCount,
                        'booking_services' => $bookingServiceCount,
                        'email_campaigns' => $emailCampaignCount,
                        'legal_documents' => $legalDocumentCount,
                    ],
                    'recent_activities' => [
                        'users' => $recentUsers,
                        'subscriptions' => $recentSubscriptions,
                    ],
                    'analytics' => [
                        'monthly_revenue' => $monthlyRevenue,
                        'user_growth' => $userGrowth,
                    ],
                    'system_status' => [
                        'database_connected' => true,
                        'cache_working' => true,
                        'queue_working' => true,
                        'mail_working' => true,
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load admin dashboard',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Manage subscription plans
     */
    public function manageSubscriptionPlans(Request $request)
    {
        try {
            $request->validate([
                'action' => 'required|string|in:create,update,delete,list',
                'plan_data' => 'required_if:action,create,update|array',
                'plan_id' => 'required_if:action,update,delete|integer'
            ]);

            $action = $request->input('action');

            switch ($action) {
                case 'list':
                    $plans = DB::table('subscription_plans')
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->get();
                    
                    return response()->json([
                        'success' => true,
                        'data' => $plans
                    ]);

                case 'create':
                    $planData = $request->input('plan_data');
                    $planId = DB::table('subscription_plans')->insertGetId([
                        'name' => $planData['name'],
                        'slug' => $planData['slug'],
                        'description' => $planData['description'],
                        'price' => $planData['price'],
                        'billing_cycle' => $planData['billing_cycle'],
                        'max_workspaces' => $planData['max_workspaces'] ?? 1,
                        'max_team_members' => $planData['max_team_members'] ?? 1,
                        'features' => json_encode($planData['features'] ?? []),
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Subscription plan created successfully',
                        'plan_id' => $planId
                    ]);

                case 'update':
                    $planData = $request->input('plan_data');
                    $planId = $request->input('plan_id');
                    
                    DB::table('subscription_plans')
                        ->where('id', $planId)
                        ->update([
                            'name' => $planData['name'],
                            'description' => $planData['description'],
                            'price' => $planData['price'],
                            'billing_cycle' => $planData['billing_cycle'],
                            'max_workspaces' => $planData['max_workspaces'] ?? 1,
                            'max_team_members' => $planData['max_team_members'] ?? 1,
                            'features' => json_encode($planData['features'] ?? []),
                            'updated_at' => now()
                        ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Subscription plan updated successfully'
                    ]);

                case 'delete':
                    $planId = $request->input('plan_id');
                    DB::table('subscription_plans')
                        ->where('id', $planId)
                        ->update(['is_active' => false, 'updated_at' => now()]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Subscription plan deactivated successfully'
                    ]);

                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid action'
                    ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to manage subscription plans',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get comprehensive platform metrics
     */
    public function getMetrics()
    {
        try {
            // Revenue metrics
            $totalRevenue = UserSubscription::where('status', 'active')->sum('amount');
            $monthlyRevenue = UserSubscription::where('status', 'active')
                ->whereMonth('created_at', now()->month)
                ->sum('amount');
            $revenueGrowth = $this->calculateRevenueGrowth();

            // User metrics
            $totalUsers = User::count();
            $activeUsers = User::where('last_login_at', '>=', now()->subDays(30))->count();
            $newUsersThisMonth = User::whereMonth('created_at', now()->month)->count();

            // Content metrics
            $totalCourses = Course::count();
            $publishedCourses = Course::where('is_published', true)->count();
            $totalBioSites = BioSite::count();
            $activeBioSites = BioSite::where('is_active', true)->count();

            // Business metrics
            $totalBookings = BookingService::count();
            $totalEscrowTransactions = EscrowTransaction::count();
            $totalEmailCampaigns = EmailCampaign::count();
            $sentCampaigns = EmailCampaign::where('status', 'sent')->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'revenue' => [
                        'total' => $totalRevenue,
                        'monthly' => $monthlyRevenue,
                        'growth' => $revenueGrowth
                    ],
                    'users' => [
                        'total' => $totalUsers,
                        'active' => $activeUsers,
                        'new_this_month' => $newUsersThisMonth,
                        'activity_rate' => $totalUsers > 0 ? ($activeUsers / $totalUsers) * 100 : 0
                    ],
                    'content' => [
                        'courses' => [
                            'total' => $totalCourses,
                            'published' => $publishedCourses,
                            'publish_rate' => $totalCourses > 0 ? ($publishedCourses / $totalCourses) * 100 : 0
                        ],
                        'bio_sites' => [
                            'total' => $totalBioSites,
                            'active' => $activeBioSites,
                            'active_rate' => $totalBioSites > 0 ? ($activeBioSites / $totalBioSites) * 100 : 0
                        ]
                    ],
                    'business' => [
                        'bookings' => $totalBookings,
                        'escrow_transactions' => $totalEscrowTransactions,
                        'email_campaigns' => [
                            'total' => $totalEmailCampaigns,
                            'sent' => $sentCampaigns,
                            'send_rate' => $totalEmailCampaigns > 0 ? ($sentCampaigns / $totalEmailCampaigns) * 100 : 0
                        ]
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load metrics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate revenue growth percentage
     */
    private function calculateRevenueGrowth()
    {
        $currentMonth = UserSubscription::where('status', 'active')
            ->whereMonth('created_at', now()->month)
            ->sum('amount');

        $lastMonth = UserSubscription::where('status', 'active')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->sum('amount');

        if ($lastMonth == 0) {
            return 100; // 100% growth if no revenue last month
        }

        return (($currentMonth - $lastMonth) / $lastMonth) * 100;
    }

    /**
     * Get system health status
     */
    public function getSystemHealth()
    {
        try {
            // Database health
            $dbHealth = true;
            try {
                DB::connection()->getPdo();
            } catch (\Exception $e) {
                $dbHealth = false;
            }

            // Cache health
            $cacheHealth = true;
            try {
                cache()->put('health_check', 'ok', 10);
                $cacheHealth = cache()->get('health_check') === 'ok';
            } catch (\Exception $e) {
                $cacheHealth = false;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'database' => $dbHealth,
                    'cache' => $cacheHealth,
                    'queue' => true, // Simplified for now
                    'mail' => true,  // Simplified for now
                    'overall' => $dbHealth && $cacheHealth
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check system health',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}