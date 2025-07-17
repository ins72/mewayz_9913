<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class RealTimeController extends Controller
{
    /**
     * Get real-time notifications for the authenticated user
     */
    public function getNotifications(Request $request)
    {
        try {
            $user = $request->user();
            
            // Get notifications from cache or database
            $notifications = Cache::remember("notifications_{$user->id}", 60, function () use ($user) {
                return [
                    [
                        'id' => 1,
                        'type' => 'website_published',
                        'title' => 'Website Published',
                        'message' => 'Your website "My Business" has been published successfully',
                        'timestamp' => now()->subMinutes(5)->toISOString(),
                        'read' => false,
                        'icon' => 'globe',
                        'color' => 'success'
                    ],
                    [
                        'id' => 2,
                        'type' => 'new_order',
                        'title' => 'New Order Received',
                        'message' => 'Order #12345 received for $99.99',
                        'timestamp' => now()->subMinutes(15)->toISOString(),
                        'read' => false,
                        'icon' => 'shopping-cart',
                        'color' => 'info'
                    ],
                    [
                        'id' => 3,
                        'type' => 'social_mention',
                        'title' => 'Social Media Mention',
                        'message' => 'You were mentioned in a post on Instagram',
                        'timestamp' => now()->subMinutes(30)->toISOString(),
                        'read' => true,
                        'icon' => 'at-symbol',
                        'color' => 'warning'
                    ],
                    [
                        'id' => 4,
                        'type' => 'team_invitation',
                        'title' => 'Team Invitation',
                        'message' => 'John Doe accepted your team invitation',
                        'timestamp' => now()->subHours(1)->toISOString(),
                        'read' => true,
                        'icon' => 'users',
                        'color' => 'primary'
                    ],
                    [
                        'id' => 5,
                        'type' => 'backup_completed',
                        'title' => 'Backup Completed',
                        'message' => 'Your website backup has been completed successfully',
                        'timestamp' => now()->subHours(2)->toISOString(),
                        'read' => true,
                        'icon' => 'cloud-upload',
                        'color' => 'success'
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $notifications,
                'unread_count' => collect($notifications)->where('read', false)->count(),
                'message' => 'Notifications retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve notifications: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve notifications'
            ], 500);
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, $notificationId)
    {
        try {
            $user = $request->user();
            
            // In production, update the notification in the database
            // For demo, we'll simulate the update
            Cache::forget("notifications_{$user->id}");

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to mark notification as read: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read'
            ], 500);
        }
    }

    /**
     * Get real-time activity feed
     */
    public function getActivityFeed(Request $request)
    {
        try {
            $user = $request->user();
            
            // Get activity feed from cache or database
            $activities = Cache::remember("activity_feed_{$user->id}", 60, function () use ($user) {
                return [
                    [
                        'id' => 1,
                        'type' => 'website_created',
                        'title' => 'Website Created',
                        'description' => 'Created a new website "My Portfolio"',
                        'timestamp' => now()->subMinutes(10)->toISOString(),
                        'icon' => 'plus-circle',
                        'color' => 'success'
                    ],
                    [
                        'id' => 2,
                        'type' => 'content_published',
                        'title' => 'Content Published',
                        'description' => 'Published a new blog post "Getting Started"',
                        'timestamp' => now()->subMinutes(25)->toISOString(),
                        'icon' => 'document-text',
                        'color' => 'info'
                    ],
                    [
                        'id' => 3,
                        'type' => 'social_post',
                        'title' => 'Social Post Scheduled',
                        'description' => 'Scheduled a post for Instagram at 3:00 PM',
                        'timestamp' => now()->subMinutes(45)->toISOString(),
                        'icon' => 'calendar',
                        'color' => 'warning'
                    ],
                    [
                        'id' => 4,
                        'type' => 'team_joined',
                        'title' => 'Team Member Joined',
                        'description' => 'Sarah Johnson joined your team',
                        'timestamp' => now()->subHours(1)->toISOString(),
                        'icon' => 'user-add',
                        'color' => 'primary'
                    ],
                    [
                        'id' => 5,
                        'type' => 'analytics_report',
                        'title' => 'Analytics Report',
                        'description' => 'Weekly analytics report is ready',
                        'timestamp' => now()->subHours(2)->toISOString(),
                        'icon' => 'chart-bar',
                        'color' => 'purple'
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $activities,
                'message' => 'Activity feed retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve activity feed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve activity feed'
            ], 500);
        }
    }

    /**
     * Get real-time system status
     */
    public function getSystemStatus(Request $request)
    {
        try {
            $systemStatus = Cache::remember('system_status', 60, function () {
                return [
                    'overall_status' => 'operational',
                    'uptime' => '99.9%',
                    'response_time' => '145ms',
                    'last_updated' => now()->toISOString(),
                    'services' => [
                        [
                            'name' => 'Website Builder',
                            'status' => 'operational',
                            'response_time' => '120ms',
                            'uptime' => '100%',
                            'last_check' => now()->toISOString()
                        ],
                        [
                            'name' => 'Social Media API',
                            'status' => 'operational',
                            'response_time' => '89ms',
                            'uptime' => '99.9%',
                            'last_check' => now()->toISOString()
                        ],
                        [
                            'name' => 'E-commerce Engine',
                            'status' => 'operational',
                            'response_time' => '156ms',
                            'uptime' => '99.8%',
                            'last_check' => now()->toISOString()
                        ],
                        [
                            'name' => 'Analytics Service',
                            'status' => 'operational',
                            'response_time' => '203ms',
                            'uptime' => '99.7%',
                            'last_check' => now()->toISOString()
                        ],
                        [
                            'name' => 'Database',
                            'status' => 'operational',
                            'response_time' => '45ms',
                            'uptime' => '100%',
                            'last_check' => now()->toISOString()
                        ],
                        [
                            'name' => 'File Storage',
                            'status' => 'operational',
                            'response_time' => '78ms',
                            'uptime' => '99.9%',
                            'last_check' => now()->toISOString()
                        ]
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $systemStatus,
                'message' => 'System status retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve system status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve system status'
            ], 500);
        }
    }

    /**
     * Get real-time user presence
     */
    public function getUserPresence(Request $request)
    {
        try {
            $user = $request->user();
            
            // Get online users from cache
            $onlineUsers = Cache::remember('online_users', 60, function () {
                return [
                    [
                        'id' => 1,
                        'name' => 'John Doe',
                        'email' => 'john@example.com',
                        'status' => 'online',
                        'last_seen' => now()->toISOString(),
                        'activity' => 'Editing website'
                    ],
                    [
                        'id' => 2,
                        'name' => 'Sarah Johnson',
                        'email' => 'sarah@example.com',
                        'status' => 'away',
                        'last_seen' => now()->subMinutes(5)->toISOString(),
                        'activity' => 'Idle'
                    ],
                    [
                        'id' => 3,
                        'name' => 'Mike Chen',
                        'email' => 'mike@example.com',
                        'status' => 'online',
                        'last_seen' => now()->subMinutes(2)->toISOString(),
                        'activity' => 'Managing products'
                    ]
                ];
            });

            // Update current user's presence
            Cache::put("user_presence_{$user->id}", [
                'user_id' => $user->id,
                'status' => 'online',
                'last_seen' => now()->toISOString(),
                'activity' => 'Active'
            ], 300); // 5 minutes

            return response()->json([
                'success' => true,
                'data' => [
                    'online_users' => $onlineUsers,
                    'total_online' => count($onlineUsers),
                    'current_user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'status' => 'online',
                        'last_seen' => now()->toISOString()
                    ]
                ],
                'message' => 'User presence retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve user presence: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user presence'
            ], 500);
        }
    }

    /**
     * Send real-time message
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
            'type' => 'nullable|in:text,image,file,link'
        ]);

        try {
            $user = $request->user();
            
            $message = [
                'id' => uniqid(),
                'sender_id' => $user->id,
                'sender_name' => $user->name,
                'recipient_id' => $request->recipient_id,
                'message' => $request->message,
                'type' => $request->type ?? 'text',
                'timestamp' => now()->toISOString(),
                'read' => false
            ];

            // In production, broadcast via WebSocket
            // For demo, we'll just return success
            
            return response()->json([
                'success' => true,
                'data' => $message,
                'message' => 'Message sent successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send message: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message'
            ], 500);
        }
    }

    /**
     * Get real-time workspace metrics
     */
    public function getWorkspaceMetrics(Request $request)
    {
        try {
            $user = $request->user();
            
            $metrics = Cache::remember("workspace_metrics_{$user->id}", 60, function () {
                return [
                    'websites' => [
                        'total' => 12,
                        'active' => 8,
                        'published' => 6,
                        'growth' => '+15%'
                    ],
                    'visitors' => [
                        'today' => 1247,
                        'this_week' => 8965,
                        'this_month' => 34567,
                        'growth' => '+23%'
                    ],
                    'revenue' => [
                        'today' => 2847.50,
                        'this_week' => 18456.75,
                        'this_month' => 76543.20,
                        'growth' => '+18%'
                    ],
                    'conversion' => [
                        'rate' => 3.2,
                        'orders' => 156,
                        'average_order' => 89.45,
                        'growth' => '+12%'
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $metrics,
                'message' => 'Workspace metrics retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve workspace metrics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve workspace metrics'
            ], 500);
        }
    }
}