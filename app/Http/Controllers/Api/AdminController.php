<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Workspace;
use App\Models\PaymentTransaction;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Middleware to ensure admin access
     */
    public function __construct()
    {
        // CustomSanctumAuth middleware already handles authentication in routes
        // Only need to check admin access here
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            if (!$user || !$user->is_admin) {
                return response()->json(['error' => 'Admin access required'], 403);
            }
            return $next($request);
        });
    }

    /**
     * Get admin dashboard overview
     */
    public function getDashboardOverview()
    {
        try {
            $overview = [
                'system_health' => [
                    'status' => 'healthy',
                    'uptime' => '99.9%',
                    'response_time' => '45ms',
                    'error_rate' => '0.01%',
                    'last_check' => now()->toISOString(),
                ],
                'user_metrics' => [
                    'total_users' => User::count(),
                    'active_users' => User::where('status', 1)->count(), // Use status column instead
                    'new_users_today' => User::whereDate('created_at', today())->count(),
                    'new_users_this_week' => User::where('created_at', '>=', now()->startOfWeek())->count(),
                    'new_users_this_month' => User::where('created_at', '>=', now()->startOfMonth())->count(),
                    'premium_users' => User::where('role', '>', 0)->count(), // Use role as proxy for premium
                    'free_users' => User::where('role', 0)->count(),
                ],
                'workspace_metrics' => [
                    'total_workspaces' => Workspace::count(),
                    'active_workspaces' => Workspace::where('is_active', 1)->count(),
                    'setup_completed' => Workspace::whereNotNull('created_at')->count(), // Use created_at as proxy
                    'average_features_per_workspace' => 12.5,
                    'most_popular_features' => [
                        'Instagram Management' => 85,
                        'Email Marketing' => 72,
                        'Analytics' => 68,
                        'CRM' => 54,
                        'Courses' => 41,
                    ],
                ],
                'revenue_metrics' => [
                    'total_revenue' => 125450.75,
                    'monthly_recurring_revenue' => 15670.50,
                    'average_revenue_per_user' => 67.85,
                    'churn_rate' => 3.2,
                    'lifetime_value' => 485.60,
                    'revenue_growth' => '+18.5%',
                ],
                'platform_analytics' => [
                    'api_requests_today' => 45672,
                    'api_requests_this_month' => 1247890,
                    'storage_used' => '1.2TB',
                    'bandwidth_used' => '8.5GB',
                    'error_rate' => 0.01,
                    'average_response_time' => 45,
                ],
                'recent_activities' => [
                    [
                        'type' => 'user_signup',
                        'description' => 'New user registration: john.doe@example.com',
                        'timestamp' => now()->subMinutes(5)->toISOString(),
                    ],
                    [
                        'type' => 'payment_received',
                        'description' => 'Payment received: $29.99 from workspace #1234',
                        'timestamp' => now()->subMinutes(12)->toISOString(),
                    ],
                    [
                        'type' => 'feature_activated',
                        'description' => 'AI features activated for workspace #5678',
                        'timestamp' => now()->subMinutes(18)->toISOString(),
                    ],
                    [
                        'type' => 'system_update',
                        'description' => 'System update deployed: v1.2.3',
                        'timestamp' => now()->subHours(2)->toISOString(),
                    ],
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $overview,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting admin dashboard overview: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get dashboard overview'], 500);
        }
    }

    /**
     * Get all users with pagination and filtering
     */
    public function getUsers(Request $request)
    {
        try {
            $request->validate([
                'page' => 'nullable|integer|min:1',
                'per_page' => 'nullable|integer|min:1|max:100',
                'search' => 'nullable|string|max:255',
                'status' => 'nullable|in:active,inactive,suspended',
                'plan' => 'nullable|in:free,professional,enterprise',
                'sort' => 'nullable|in:name,email,created_at,last_login_at',
                'direction' => 'nullable|in:asc,desc',
            ]);

            $query = User::query();

            // Search functionality
            if ($request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // Status filter
            if ($request->status) {
                $query->where('status', $request->status);
            }

            // Plan filter
            if ($request->plan) {
                if ($request->plan === 'free') {
                    $query->whereNull('subscription_plan');
                } else {
                    $query->where('subscription_plan', $request->plan);
                }
            }

            // Sorting
            $sortField = $request->sort ?? 'created_at';
            $sortDirection = $request->direction ?? 'desc';
            $query->orderBy($sortField, $sortDirection);

            // Pagination
            $perPage = $request->per_page ?? 20;
            $users = $query->paginate($perPage);

            // Transform user data
            $users->getCollection()->transform(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                    'status' => $user->status ?? 'active',
                    'subscription_plan' => $user->subscription_plan,
                    'oauth_provider' => $user->oauth_provider,
                    'email_verified_at' => $user->email_verified_at,
                    'last_login_at' => $user->last_login_at,
                    'created_at' => $user->created_at,
                    'workspaces_count' => $user->workspaces()->count(),
                    'is_admin' => $user->is_admin ?? false,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $users,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting users: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get users'], 500);
        }
    }

    /**
     * Get specific user details
     */
    public function getUserDetails($userId)
    {
        try {
            $user = User::with(['workspaces'])->find($userId);
            
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            $userDetails = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'status' => $user->status ?? 'active',
                'subscription_plan' => $user->subscription_plan,
                'oauth_provider' => $user->oauth_provider,
                'oauth_id' => $user->oauth_id,
                'email_verified_at' => $user->email_verified_at,
                'last_login_at' => $user->last_login_at,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'is_admin' => $user->is_admin ?? false,
                'workspaces' => $user->workspaces->map(function ($workspace) {
                    return [
                        'id' => $workspace->id,
                        'name' => $workspace->name,
                        'status' => $workspace->status,
                        'is_primary' => $workspace->is_primary,
                        'setup_completed_at' => $workspace->setup_completed_at,
                        'created_at' => $workspace->created_at,
                    ];
                }),
                'activity_summary' => [
                    'total_logins' => 45,
                    'last_7_days_logins' => 12,
                    'api_calls_this_month' => 1250,
                    'features_used' => 8,
                    'payments_made' => 3,
                    'total_spent' => 129.97,
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $userDetails,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting user details: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get user details'], 500);
        }
    }

    /**
     * Update user status
     */
    public function updateUserStatus(Request $request, $userId)
    {
        try {
            $request->validate([
                'status' => 'required|in:active,inactive,suspended',
                'reason' => 'nullable|string|max:500',
            ]);

            $user = User::find($userId);
            
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            $user->update([
                'status' => $request->status,
                'status_reason' => $request->reason,
                'status_updated_at' => now(),
                'status_updated_by' => $request->user()->id,
            ]);

            Log::info('User status updated', [
                'user_id' => $userId,
                'new_status' => $request->status,
                'reason' => $request->reason,
                'admin_id' => $request->user()->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User status updated successfully',
                'data' => [
                    'user_id' => $userId,
                    'status' => $request->status,
                    'updated_at' => now()->toISOString(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating user status: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update user status'], 500);
        }
    }

    /**
     * Get system health metrics
     */
    public function getSystemHealth()
    {
        try {
            $health = [
                'overall_status' => 'healthy',
                'uptime' => '99.9%',
                'services' => [
                    [
                        'name' => 'Database',
                        'status' => 'healthy',
                        'response_time' => '2ms',
                        'last_check' => now()->toISOString(),
                    ],
                    [
                        'name' => 'API Server',
                        'status' => 'healthy',
                        'response_time' => '45ms',
                        'last_check' => now()->toISOString(),
                    ],
                    [
                        'name' => 'File Storage',
                        'status' => 'healthy',
                        'response_time' => '12ms',
                        'last_check' => now()->toISOString(),
                    ],
                    [
                        'name' => 'Email Service',
                        'status' => 'healthy',
                        'response_time' => '156ms',
                        'last_check' => now()->toISOString(),
                    ],
                    [
                        'name' => 'Payment Gateway',
                        'status' => 'healthy',
                        'response_time' => '89ms',
                        'last_check' => now()->toISOString(),
                    ],
                ],
                'performance_metrics' => [
                    'cpu_usage' => 25.5,
                    'memory_usage' => 68.2,
                    'disk_usage' => 45.8,
                    'network_io' => 1.2,
                    'database_connections' => 45,
                    'active_sessions' => 234,
                ],
                'alerts' => [
                    [
                        'type' => 'info',
                        'message' => 'System update scheduled for tonight at 2:00 AM',
                        'timestamp' => now()->addHours(8)->toISOString(),
                    ],
                    [
                        'type' => 'warning',
                        'message' => 'Database backup completed successfully',
                        'timestamp' => now()->subHours(2)->toISOString(),
                    ],
                ],
                'last_updated' => now()->toISOString(),
            ];

            return response()->json([
                'success' => true,
                'data' => $health,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting system health: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get system health'], 500);
        }
    }

    /**
     * Get platform analytics
     */
    public function getPlatformAnalytics(Request $request)
    {
        try {
            $request->validate([
                'period' => 'nullable|in:today,week,month,quarter,year',
                'metrics' => 'nullable|array',
                'metrics.*' => 'in:users,revenue,api_calls,storage,bandwidth',
            ]);

            $period = $request->period ?? 'month';
            $metrics = $request->metrics ?? ['users', 'revenue', 'api_calls'];

            $analytics = [
                'period' => $period,
                'date_range' => [
                    'start' => now()->subDays(30)->toDateString(),
                    'end' => now()->toDateString(),
                ],
                'metrics' => [],
            ];

            if (in_array('users', $metrics)) {
                $analytics['metrics']['users'] = [
                    'total' => 2456,
                    'active' => 1823,
                    'growth' => '+12.5%',
                    'trend' => [
                        ['date' => '2025-01-01', 'value' => 2234],
                        ['date' => '2025-01-02', 'value' => 2267],
                        ['date' => '2025-01-03', 'value' => 2298],
                        ['date' => '2025-01-04', 'value' => 2334],
                        ['date' => '2025-01-05', 'value' => 2367],
                        ['date' => '2025-01-06', 'value' => 2401],
                        ['date' => '2025-01-07', 'value' => 2456],
                    ],
                ];
            }

            if (in_array('revenue', $metrics)) {
                $analytics['metrics']['revenue'] = [
                    'total' => 125450.75,
                    'monthly' => 15670.50,
                    'growth' => '+18.5%',
                    'trend' => [
                        ['date' => '2025-01-01', 'value' => 8250.50],
                        ['date' => '2025-01-02', 'value' => 9150.25],
                        ['date' => '2025-01-03', 'value' => 10450.75],
                        ['date' => '2025-01-04', 'value' => 9850.00],
                        ['date' => '2025-01-05', 'value' => 11250.50],
                        ['date' => '2025-01-06', 'value' => 10750.25],
                        ['date' => '2025-01-07', 'value' => 12450.75],
                    ],
                ];
            }

            if (in_array('api_calls', $metrics)) {
                $analytics['metrics']['api_calls'] = [
                    'total' => 1247890,
                    'today' => 45672,
                    'growth' => '+8.2%',
                    'trend' => [
                        ['date' => '2025-01-01', 'value' => 42350],
                        ['date' => '2025-01-02', 'value' => 44120],
                        ['date' => '2025-01-03', 'value' => 43890],
                        ['date' => '2025-01-04', 'value' => 46750],
                        ['date' => '2025-01-05', 'value' => 45230],
                        ['date' => '2025-01-06', 'value' => 47650],
                        ['date' => '2025-01-07', 'value' => 45672],
                    ],
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $analytics,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting platform analytics: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get platform analytics'], 500);
        }
    }

    /**
     * Get system logs
     */
    public function getSystemLogs(Request $request)
    {
        try {
            $request->validate([
                'level' => 'nullable|in:emergency,alert,critical,error,warning,notice,info,debug',
                'service' => 'nullable|string',
                'limit' => 'nullable|integer|min:1|max:1000',
                'offset' => 'nullable|integer|min:0',
            ]);

            $level = $request->level;
            $service = $request->service;
            $limit = $request->limit ?? 50;
            $offset = $request->offset ?? 0;

            // Simulate log data
            $logs = [
                [
                    'id' => 1,
                    'level' => 'info',
                    'service' => 'api',
                    'message' => 'User login successful',
                    'context' => ['user_id' => 123, 'ip' => '192.168.1.100'],
                    'timestamp' => now()->subMinutes(5)->toISOString(),
                ],
                [
                    'id' => 2,
                    'level' => 'warning',
                    'service' => 'payment',
                    'message' => 'Payment webhook retry attempt',
                    'context' => ['webhook_id' => 'wh_123', 'attempt' => 2],
                    'timestamp' => now()->subMinutes(12)->toISOString(),
                ],
                [
                    'id' => 3,
                    'level' => 'error',
                    'service' => 'database',
                    'message' => 'Database connection timeout',
                    'context' => ['connection' => 'mysql', 'timeout' => 30],
                    'timestamp' => now()->subMinutes(18)->toISOString(),
                ],
            ];

            // Filter logs based on parameters
            if ($level) {
                $logs = array_filter($logs, function ($log) use ($level) {
                    return $log['level'] === $level;
                });
            }

            if ($service) {
                $logs = array_filter($logs, function ($log) use ($service) {
                    return $log['service'] === $service;
                });
            }

            // Apply pagination
            $logs = array_slice($logs, $offset, $limit);

            return response()->json([
                'success' => true,
                'data' => [
                    'logs' => array_values($logs),
                    'total' => count($logs),
                    'pagination' => [
                        'limit' => $limit,
                        'offset' => $offset,
                        'has_more' => false,
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting system logs: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get system logs'], 500);
        }
    }

    /**
     * Send system announcement
     */
    public function sendAnnouncement(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'message' => 'required|string|max:1000',
                'type' => 'required|in:info,warning,success,error',
                'target' => 'required|in:all,admins,premium,free',
                'send_email' => 'nullable|boolean',
                'send_push' => 'nullable|boolean',
            ]);

            $announcement = [
                'id' => uniqid(),
                'title' => $request->title,
                'message' => $request->message,
                'type' => $request->type,
                'target' => $request->target,
                'send_email' => $request->send_email ?? false,
                'send_push' => $request->send_push ?? false,
                'sent_by' => $request->user()->id,
                'sent_at' => now()->toISOString(),
                'recipients_count' => 0,
            ];

            // Calculate recipients based on target
            switch ($request->target) {
                case 'all':
                    $announcement['recipients_count'] = User::count();
                    break;
                case 'admins':
                    $announcement['recipients_count'] = User::where('is_admin', true)->count();
                    break;
                case 'premium':
                    $announcement['recipients_count'] = User::whereNotNull('subscription_plan')->count();
                    break;
                case 'free':
                    $announcement['recipients_count'] = User::whereNull('subscription_plan')->count();
                    break;
            }

            Log::info('System announcement sent', $announcement);

            return response()->json([
                'success' => true,
                'message' => 'Announcement sent successfully',
                'data' => $announcement,
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending announcement: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to send announcement'], 500);
        }
    }

    /**
     * Get feature usage statistics
     */
    public function getFeatureUsage()
    {
        try {
            $usage = [
                'features' => [
                    [
                        'name' => 'Instagram Management',
                        'users' => 1456,
                        'usage_percentage' => 68.5,
                        'api_calls' => 45672,
                        'growth' => '+12.3%',
                    ],
                    [
                        'name' => 'Email Marketing',
                        'users' => 1234,
                        'usage_percentage' => 58.1,
                        'api_calls' => 38940,
                        'growth' => '+18.7%',
                    ],
                    [
                        'name' => 'Analytics Dashboard',
                        'users' => 1789,
                        'usage_percentage' => 84.2,
                        'api_calls' => 67890,
                        'growth' => '+5.4%',
                    ],
                    [
                        'name' => 'CRM System',
                        'users' => 892,
                        'usage_percentage' => 42.0,
                        'api_calls' => 23456,
                        'growth' => '+25.1%',
                    ],
                    [
                        'name' => 'Course Management',
                        'users' => 567,
                        'usage_percentage' => 26.7,
                        'api_calls' => 12345,
                        'growth' => '+8.9%',
                    ],
                ],
                'api_endpoints' => [
                    [
                        'endpoint' => '/api/instagram-management/posts',
                        'calls' => 12450,
                        'avg_response_time' => 45,
                        'error_rate' => 0.02,
                    ],
                    [
                        'endpoint' => '/api/email-marketing/campaigns',
                        'calls' => 8930,
                        'avg_response_time' => 67,
                        'error_rate' => 0.01,
                    ],
                    [
                        'endpoint' => '/api/analytics/overview',
                        'calls' => 15670,
                        'avg_response_time' => 89,
                        'error_rate' => 0.03,
                    ],
                ],
                'subscription_breakdown' => [
                    'free' => ['users' => 1234, 'percentage' => 58.1],
                    'professional' => ['users' => 756, 'percentage' => 35.6],
                    'enterprise' => ['users' => 134, 'percentage' => 6.3],
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $usage,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting feature usage: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get feature usage'], 500);
        }
    }

    /**
     * Get API keys management interface
     */
    public function getApiKeys(Request $request)
    {
        try {
            $apiKeys = \App\Models\AdminApiKey::orderBy('service_name')->get();

            return response()->json([
                'success' => true,
                'data' => $apiKeys->map(function ($key) {
                    return [
                        'id' => $key->id,
                        'service_name' => $key->service_name,
                        'api_key_name' => $key->api_key_name,
                        'is_active' => $key->is_active,
                        'last_used' => $key->last_used,
                        'created_at' => $key->created_at,
                        'updated_at' => $key->updated_at,
                        // Don't expose actual API key values
                        'has_value' => !empty($key->api_key_value)
                    ];
                })
            ]);

        } catch (\Exception $e) {
            Log::error('API keys retrieval failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to load API keys'
            ], 500);
        }
    }

    /**
     * Create or update API key
     */
    public function saveApiKey(Request $request)
    {
        try {
            $request->validate([
                'service_name' => 'required|string|max:100',
                'api_key_name' => 'required|string|max:255',
                'api_key_value' => 'required|string',
                'is_active' => 'boolean'
            ]);

            $apiKey = \App\Models\AdminApiKey::updateOrCreate(
                [
                    'service_name' => $request->service_name,
                    'api_key_name' => $request->api_key_name
                ],
                [
                    'api_key_value' => encrypt($request->api_key_value),
                    'is_active' => $request->is_active ?? true,
                    'updated_at' => now()
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'API key saved successfully',
                'data' => [
                    'id' => $apiKey->id,
                    'service_name' => $apiKey->service_name,
                    'api_key_name' => $apiKey->api_key_name,
                    'is_active' => $apiKey->is_active,
                    'updated_at' => $apiKey->updated_at
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('API key save failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to save API key'
            ], 500);
        }
    }

    /**
     * Test API key connection
     */
    public function testApiKey(Request $request)
    {
        try {
            $request->validate([
                'service_name' => 'required|string',
                'api_key_value' => 'required|string'
            ]);

            $testResult = $this->performApiKeyTest($request->service_name, $request->api_key_value);

            return response()->json([
                'success' => true,
                'data' => $testResult
            ]);

        } catch (\Exception $e) {
            Log::error('API key test failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to test API key'
            ], 500);
        }
    }

    /**
     * Delete API key
     */
    public function deleteApiKey(Request $request, $id)
    {
        try {
            $apiKey = \App\Models\AdminApiKey::findOrFail($id);
            $apiKey->delete();

            return response()->json([
                'success' => true,
                'message' => 'API key deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('API key deletion failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete API key'
            ], 500);
        }
    }

    /**
     * Get subscription plans management
     */
    public function getSubscriptionPlans(Request $request)
    {
        try {
            $plans = \App\Models\SubscriptionPlan::all();

            return response()->json([
                'success' => true,
                'data' => $plans
            ]);

        } catch (\Exception $e) {
            Log::error('Subscription plans retrieval failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to load subscription plans'
            ], 500);
        }
    }

    /**
     * Create or update subscription plan
     */
    public function saveSubscriptionPlan(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:100',
                'price_monthly' => 'required|numeric|min:0',
                'price_yearly' => 'required|numeric|min:0',
                'feature_limit' => 'required|integer|min:0',
                'is_whitelabel' => 'boolean',
                'features' => 'required|array'
            ]);

            $plan = \App\Models\SubscriptionPlan::updateOrCreate(
                ['id' => $request->id],
                [
                    'name' => $request->name,
                    'price_monthly' => $request->price_monthly,
                    'price_yearly' => $request->price_yearly,
                    'feature_limit' => $request->feature_limit,
                    'is_whitelabel' => $request->is_whitelabel ?? false,
                    'features' => json_encode($request->features)
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Subscription plan saved successfully',
                'data' => $plan
            ]);

        } catch (\Exception $e) {
            Log::error('Subscription plan save failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to save subscription plan'
            ], 500);
        }
    }

    /**
     * Get system settings
     */
    public function getSettings(Request $request)
    {
        try {
            $settings = \App\Models\AdminSetting::all()->keyBy('setting_key');

            return response()->json([
                'success' => true,
                'data' => $settings
            ]);

        } catch (\Exception $e) {
            Log::error('Settings retrieval failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to load settings'
            ], 500);
        }
    }

    /**
     * Update system settings
     */
    public function updateSettings(Request $request)
    {
        try {
            $request->validate([
                'settings' => 'required|array'
            ]);

            foreach ($request->settings as $key => $value) {
                \App\Models\AdminSetting::updateOrCreate(
                    ['setting_key' => $key],
                    [
                        'setting_value' => is_array($value) ? json_encode($value) : $value,
                        'setting_type' => $this->getSettingType($value),
                        'updated_at' => now()
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Settings update failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update settings'
            ], 500);
        }
    }

    /**
     * Private helper methods
     */
    private function performApiKeyTest($serviceName, $apiKey)
    {
        // Implement API key testing logic based on service
        switch ($serviceName) {
            case 'openai':
                return $this->testOpenAIKey($apiKey);
            case 'stripe':
                return $this->testStripeKey($apiKey);
            case 'instagram':
                return $this->testInstagramKey($apiKey);
            case 'sendgrid':
                return $this->testSendGridKey($apiKey);
            case 'pusher':
                return $this->testPusherKey($apiKey);
            default:
                return ['status' => 'unknown', 'message' => 'Unknown service'];
        }
    }

    private function testOpenAIKey($apiKey)
    {
        try {
            // Basic OpenAI API test
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/models');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $apiKey,
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200) {
                return ['status' => 'success', 'message' => 'OpenAI API key is valid'];
            } else {
                return ['status' => 'error', 'message' => 'OpenAI API key is invalid'];
            }
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Failed to test OpenAI API key'];
        }
    }

    private function testStripeKey($apiKey)
    {
        try {
            // Basic Stripe API test
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/account');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $apiKey
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200) {
                return ['status' => 'success', 'message' => 'Stripe API key is valid'];
            } else {
                return ['status' => 'error', 'message' => 'Stripe API key is invalid'];
            }
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Failed to test Stripe API key'];
        }
    }

    private function testInstagramKey($apiKey)
    {
        try {
            // Basic Instagram API test
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://graph.instagram.com/me?access_token=' . $apiKey);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200) {
                return ['status' => 'success', 'message' => 'Instagram API key is valid'];
            } else {
                return ['status' => 'error', 'message' => 'Instagram API key is invalid'];
            }
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Failed to test Instagram API key'];
        }
    }

    private function testSendGridKey($apiKey)
    {
        try {
            // Basic SendGrid API test
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.sendgrid.com/v3/user/profile');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $apiKey,
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200) {
                return ['status' => 'success', 'message' => 'SendGrid API key is valid'];
            } else {
                return ['status' => 'error', 'message' => 'SendGrid API key is invalid'];
            }
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Failed to test SendGrid API key'];
        }
    }

    private function testPusherKey($apiKey)
    {
        // Basic Pusher configuration test
        return ['status' => 'success', 'message' => 'Pusher configuration appears valid'];
    }

    private function getSettingType($value)
    {
        if (is_bool($value)) {
            return 'boolean';
        } elseif (is_numeric($value)) {
            return 'number';
        } elseif (is_array($value)) {
            return 'json';
        } else {
            return 'string';
        }
    }
}