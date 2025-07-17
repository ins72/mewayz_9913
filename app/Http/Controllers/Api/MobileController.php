<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserPreference;
use App\Models\MobileSession;
use App\Models\PushNotificationSubscription;
use App\Services\PushNotificationService;

class MobileController extends Controller
{
    protected $pushNotificationService;

    public function __construct(PushNotificationService $pushNotificationService)
    {
        $this->pushNotificationService = $pushNotificationService;
    }

    /**
     * Get mobile configuration
     */
    public function getConfig(Request $request)
    {
        try {
            $user = $request->user();
            $preferences = UserPreference::firstOrCreate(['user_id' => $user->id]);

            $config = [
                'gestures' => $this->getGestureConfig(),
                'navigation' => $this->getNavigationConfig(),
                'accessibility' => $preferences->accessibility_options ?? [],
                'performance' => $this->getPerformanceConfig(),
                'offline' => $this->getOfflineConfig(),
                'push_notifications' => $this->getPushNotificationConfig($user)
            ];

            return response()->json([
                'success' => true,
                'data' => $config,
                'message' => 'Mobile configuration retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve mobile configuration',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update mobile preferences
     */
    public function updatePreferences(Request $request)
    {
        $request->validate([
            'gestures_enabled' => 'nullable|boolean',
            'haptic_feedback' => 'nullable|boolean',
            'offline_mode' => 'nullable|boolean',
            'push_notifications' => 'nullable|array',
            'navigation_style' => 'nullable|string|in:bottom,side,floating'
        ]);

        try {
            $user = $request->user();
            $preferences = UserPreference::firstOrCreate(['user_id' => $user->id]);

            $mobilePreferences = $preferences->mobile_preferences ?? [];

            if ($request->has('gestures_enabled')) {
                $mobilePreferences['gestures_enabled'] = $request->gestures_enabled;
            }

            if ($request->has('haptic_feedback')) {
                $mobilePreferences['haptic_feedback'] = $request->haptic_feedback;
            }

            if ($request->has('offline_mode')) {
                $mobilePreferences['offline_mode'] = $request->offline_mode;
            }

            if ($request->has('navigation_style')) {
                $mobilePreferences['navigation_style'] = $request->navigation_style;
            }

            if ($request->has('push_notifications')) {
                $mobilePreferences['push_notifications'] = $request->push_notifications;
                $this->updatePushNotificationSettings($user, $request->push_notifications);
            }

            $preferences->update(['mobile_preferences' => $mobilePreferences]);

            return response()->json([
                'success' => true,
                'data' => $mobilePreferences,
                'message' => 'Mobile preferences updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to update mobile preferences',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Register mobile session
     */
    public function registerSession(Request $request)
    {
        $request->validate([
            'device_type' => 'required|string|in:mobile,tablet,desktop',
            'platform' => 'required|string|in:ios,android,web',
            'device_info' => 'nullable|array',
            'app_version' => 'nullable|string',
            'screen_size' => 'nullable|array'
        ]);

        try {
            $user = $request->user();

            $session = MobileSession::create([
                'user_id' => $user->id,
                'device_type' => $request->device_type,
                'platform' => $request->platform,
                'device_info' => $request->device_info ?? [],
                'app_version' => $request->app_version,
                'screen_size' => $request->screen_size ?? [],
                'session_start' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent')
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'session_id' => $session->id,
                    'config' => $this->getMobileSessionConfig($session)
                ],
                'message' => 'Mobile session registered successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to register mobile session',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Subscribe to push notifications
     */
    public function subscribePushNotifications(Request $request)
    {
        $request->validate([
            'subscription' => 'required|array',
            'subscription.endpoint' => 'required|string',
            'subscription.keys' => 'required|array',
            'subscription.keys.p256dh' => 'required|string',
            'subscription.keys.auth' => 'required|string'
        ]);

        try {
            $user = $request->user();
            $subscription = $request->subscription;

            $pushSubscription = PushNotificationSubscription::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'endpoint' => $subscription['endpoint']
                ],
                [
                    'p256dh_key' => $subscription['keys']['p256dh'],
                    'auth_key' => $subscription['keys']['auth'],
                    'user_agent' => $request->header('User-Agent'),
                    'subscribed_at' => now()
                ]
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'subscription_id' => $pushSubscription->id,
                    'status' => 'subscribed'
                ],
                'message' => 'Push notification subscription created successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to subscribe to push notifications',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test push notification
     */
    public function testPushNotification(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:255',
            'title' => 'nullable|string|max:100'
        ]);

        try {
            $user = $request->user();
            
            $result = $this->pushNotificationService->sendToUser($user, [
                'title' => $request->title ?? 'Test Notification',
                'message' => $request->message,
                'type' => 'test',
                'data' => [
                    'test' => true,
                    'timestamp' => now()->toISOString()
                ]
            ]);

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Test notification sent successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to send test notification',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get PWA manifest
     */
    public function getManifest(Request $request)
    {
        try {
            $user = $request->user();
            $workspace = $user->workspaces()->first();

            $manifest = [
                'name' => 'Mewayz Platform v2',
                'short_name' => 'Mewayz',
                'description' => 'All-in-One Business Platform',
                'start_url' => '/',
                'display' => 'standalone',
                'background_color' => '#101010',
                'theme_color' => '#007AFF',
                'orientation' => 'portrait-primary',
                'icons' => [
                    [
                        'src' => '/icons/icon-72x72.png',
                        'sizes' => '72x72',
                        'type' => 'image/png'
                    ],
                    [
                        'src' => '/icons/icon-96x96.png',
                        'sizes' => '96x96',
                        'type' => 'image/png'
                    ],
                    [
                        'src' => '/icons/icon-128x128.png',
                        'sizes' => '128x128',
                        'type' => 'image/png'
                    ],
                    [
                        'src' => '/icons/icon-144x144.png',
                        'sizes' => '144x144',
                        'type' => 'image/png'
                    ],
                    [
                        'src' => '/icons/icon-152x152.png',
                        'sizes' => '152x152',
                        'type' => 'image/png'
                    ],
                    [
                        'src' => '/icons/icon-192x192.png',
                        'sizes' => '192x192',
                        'type' => 'image/png'
                    ],
                    [
                        'src' => '/icons/icon-384x384.png',
                        'sizes' => '384x384',
                        'type' => 'image/png'
                    ],
                    [
                        'src' => '/icons/icon-512x512.png',
                        'sizes' => '512x512',
                        'type' => 'image/png'
                    ]
                ],
                'categories' => ['business', 'productivity', 'social'],
                'screenshots' => [
                    [
                        'src' => '/screenshots/desktop-1.png',
                        'type' => 'image/png',
                        'sizes' => '1280x720'
                    ],
                    [
                        'src' => '/screenshots/mobile-1.png',
                        'type' => 'image/png',
                        'sizes' => '750x1334'
                    ]
                ]
            ];

            // Customize manifest for workspace branding
            if ($workspace && !empty($workspace->settings['branding'])) {
                $branding = $workspace->settings['branding'];
                
                if (!empty($branding['logo'])) {
                    $manifest['icons'][] = [
                        'src' => $branding['logo'],
                        'sizes' => '512x512',
                        'type' => 'image/png',
                        'purpose' => 'any maskable'
                    ];
                }
                
                if (!empty($branding['colors']['primary'])) {
                    $manifest['theme_color'] = $branding['colors']['primary'];
                }
            }

            return response()->json($manifest);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate PWA manifest',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get gesture configuration
     */
    private function getGestureConfig()
    {
        return [
            'swipe_navigation' => [
                'enabled' => true,
                'gestures' => [
                    'swipe_left' => 'next_page',
                    'swipe_right' => 'previous_page',
                    'swipe_up' => 'refresh',
                    'swipe_down' => 'close_modal'
                ]
            ],
            'touch_gestures' => [
                'enabled' => true,
                'gestures' => [
                    'long_press' => 'context_menu',
                    'double_tap' => 'quick_action',
                    'pinch' => 'zoom',
                    'two_finger_tap' => 'undo'
                ]
            ],
            'haptic_feedback' => [
                'enabled' => true,
                'intensity' => 'medium',
                'patterns' => [
                    'success' => 'light',
                    'error' => 'heavy',
                    'warning' => 'medium',
                    'notification' => 'light'
                ]
            ]
        ];
    }

    /**
     * Get navigation configuration
     */
    private function getNavigationConfig()
    {
        return [
            'style' => 'bottom',
            'items' => [
                [
                    'id' => 'dashboard',
                    'label' => 'Dashboard',
                    'icon' => 'home',
                    'route' => '/dashboard'
                ],
                [
                    'id' => 'social',
                    'label' => 'Social',
                    'icon' => 'users',
                    'route' => '/social'
                ],
                [
                    'id' => 'analytics',
                    'label' => 'Analytics',
                    'icon' => 'bar-chart',
                    'route' => '/analytics'
                ],
                [
                    'id' => 'crm',
                    'label' => 'CRM',
                    'icon' => 'contacts',
                    'route' => '/crm'
                ],
                [
                    'id' => 'more',
                    'label' => 'More',
                    'icon' => 'more',
                    'route' => '/more'
                ]
            ]
        ];
    }

    /**
     * Get performance configuration
     */
    private function getPerformanceConfig()
    {
        return [
            'lazy_loading' => true,
            'image_optimization' => true,
            'cache_strategy' => 'aggressive',
            'preload_critical' => true,
            'reduce_animations' => false
        ];
    }

    /**
     * Get offline configuration
     */
    private function getOfflineConfig()
    {
        return [
            'enabled' => true,
            'cache_duration' => 24 * 60 * 60 * 1000, // 24 hours in milliseconds
            'cached_pages' => [
                'dashboard',
                'social',
                'analytics',
                'crm'
            ],
            'sync_on_reconnect' => true,
            'background_sync' => true
        ];
    }

    /**
     * Get push notification configuration
     */
    private function getPushNotificationConfig($user)
    {
        $subscription = PushNotificationSubscription::where('user_id', $user->id)
            ->latest()
            ->first();

        return [
            'enabled' => $subscription !== null,
            'subscription_id' => $subscription?->id,
            'vapid_public_key' => config('push.vapid.public_key'),
            'supported_types' => [
                'workspace_updates',
                'team_notifications',
                'system_alerts',
                'marketing_messages'
            ]
        ];
    }

    /**
     * Get mobile session configuration
     */
    private function getMobileSessionConfig($session)
    {
        return [
            'optimizations' => $this->getDeviceOptimizations($session),
            'features' => $this->getDeviceFeatures($session),
            'ui_adjustments' => $this->getUIAdjustments($session)
        ];
    }

    /**
     * Get device-specific optimizations
     */
    private function getDeviceOptimizations($session)
    {
        $optimizations = [];

        if ($session->device_type === 'mobile') {
            $optimizations['touch_targets'] = 'large';
            $optimizations['font_size'] = 'mobile';
            $optimizations['layout'] = 'single_column';
        }

        if ($session->platform === 'ios') {
            $optimizations['safe_area'] = true;
            $optimizations['haptic_feedback'] = true;
        }

        return $optimizations;
    }

    /**
     * Get device-specific features
     */
    private function getDeviceFeatures($session)
    {
        $features = [];

        if ($session->device_type === 'mobile') {
            $features['camera'] = true;
            $features['location'] = true;
            $features['push_notifications'] = true;
        }

        if ($session->platform === 'ios') {
            $features['face_id'] = true;
            $features['touch_id'] = true;
        }

        return $features;
    }

    /**
     * Get UI adjustments
     */
    private function getUIAdjustments($session)
    {
        $adjustments = [];

        if ($session->screen_size) {
            $width = $session->screen_size['width'] ?? 0;
            $height = $session->screen_size['height'] ?? 0;

            if ($width < 768) {
                $adjustments['navigation'] = 'bottom';
                $adjustments['sidebar'] = 'overlay';
            }
        }

        return $adjustments;
    }

    /**
     * Update push notification settings
     */
    private function updatePushNotificationSettings($user, $settings)
    {
        $subscriptions = PushNotificationSubscription::where('user_id', $user->id)->get();

        foreach ($subscriptions as $subscription) {
            $subscription->update([
                'notification_types' => $settings['enabled_types'] ?? [],
                'quiet_hours' => $settings['quiet_hours'] ?? null,
                'frequency' => $settings['frequency'] ?? 'immediate'
            ]);
        }
    }
}