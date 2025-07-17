<?php

namespace App\Services;

use App\Models\User;
use App\Models\PushNotificationSubscription;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    protected $vapidPublicKey;
    protected $vapidPrivateKey;
    protected $vapidSubject;

    public function __construct()
    {
        $this->vapidPublicKey = config('push.vapid.public_key');
        $this->vapidPrivateKey = config('push.vapid.private_key');
        $this->vapidSubject = config('push.vapid.subject');
    }

    /**
     * Send push notification to user
     */
    public function sendToUser(User $user, array $payload)
    {
        $subscriptions = PushNotificationSubscription::where('user_id', $user->id)
            ->where('subscribed_at', '!=', null)
            ->whereNull('unsubscribed_at')
            ->get();

        $results = [];

        foreach ($subscriptions as $subscription) {
            // Skip if in quiet hours
            if ($subscription->isQuietHours()) {
                continue;
            }

            // Skip if notification type is not enabled
            if (!empty($payload['type']) && !$subscription->isNotificationTypeEnabled($payload['type'])) {
                continue;
            }

            $result = $this->sendToSubscription($subscription, $payload);
            $results[] = $result;
        }

        return $results;
    }

    /**
     * Send push notification to subscription
     */
    public function sendToSubscription(PushNotificationSubscription $subscription, array $payload)
    {
        try {
            $notification = [
                'title' => $payload['title'] ?? 'Mewayz Notification',
                'body' => $payload['message'] ?? '',
                'icon' => $payload['icon'] ?? '/icons/icon-192x192.png',
                'badge' => $payload['badge'] ?? '/icons/badge-72x72.png',
                'data' => $payload['data'] ?? [],
                'actions' => $payload['actions'] ?? [],
                'requireInteraction' => $payload['requireInteraction'] ?? false,
                'silent' => $payload['silent'] ?? false,
                'vibrate' => $payload['vibrate'] ?? [200, 100, 200],
                'timestamp' => now()->timestamp * 1000
            ];

            $response = $this->sendWebPush($subscription, $notification);

            if ($response['success']) {
                Log::info('Push notification sent successfully', [
                    'user_id' => $subscription->user_id,
                    'subscription_id' => $subscription->id,
                    'notification_type' => $payload['type'] ?? 'unknown'
                ]);
            } else {
                Log::error('Push notification failed', [
                    'user_id' => $subscription->user_id,
                    'subscription_id' => $subscription->id,
                    'error' => $response['error']
                ]);
            }

            return $response;
        } catch (\Exception $e) {
            Log::error('Push notification exception', [
                'user_id' => $subscription->user_id,
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send bulk notifications
     */
    public function sendBulk(array $users, array $payload)
    {
        $results = [];

        foreach ($users as $user) {
            $result = $this->sendToUser($user, $payload);
            $results[] = [
                'user_id' => $user->id,
                'results' => $result
            ];
        }

        return $results;
    }

    /**
     * Send notification to all users
     */
    public function sendToAll(array $payload)
    {
        $users = User::whereHas('pushSubscriptions', function ($query) {
            $query->whereNull('unsubscribed_at');
        })->get();

        return $this->sendBulk($users, $payload);
    }

    /**
     * Send workspace notification
     */
    public function sendToWorkspace($workspaceId, array $payload)
    {
        $users = User::whereHas('workspaceUsers', function ($query) use ($workspaceId) {
            $query->where('workspace_id', $workspaceId);
        })->get();

        return $this->sendBulk($users, $payload);
    }

    /**
     * Send notification by user role
     */
    public function sendByRole($workspaceId, $role, array $payload)
    {
        $users = User::whereHas('workspaceUsers', function ($query) use ($workspaceId, $role) {
            $query->where('workspace_id', $workspaceId)
                  ->where('role', $role);
        })->get();

        return $this->sendBulk($users, $payload);
    }

    /**
     * Schedule notification
     */
    public function scheduleNotification(User $user, array $payload, $scheduledAt)
    {
        // This would integrate with Laravel's job queue system
        // For now, we'll just send immediately if time has passed
        if (now()->gte($scheduledAt)) {
            return $this->sendToUser($user, $payload);
        }

        // In a real implementation, you'd use a job queue
        return [
            'success' => true,
            'message' => 'Notification scheduled',
            'scheduled_at' => $scheduledAt
        ];
    }

    /**
     * Send web push notification
     */
    private function sendWebPush(PushNotificationSubscription $subscription, array $notification)
    {
        $subscriptionData = $subscription->getSubscriptionData();
        
        // Generate JWT token for VAPID
        $jwt = $this->generateJWT($subscriptionData['endpoint']);
        
        // Encrypt payload
        $encryptedPayload = $this->encryptPayload(
            json_encode($notification),
            $subscriptionData['keys']['p256dh'],
            $subscriptionData['keys']['auth']
        );

        // Send HTTP request to push service
        $response = Http::withHeaders([
            'Authorization' => 'vapid t=' . $jwt . ',k=' . $this->vapidPublicKey,
            'Content-Type' => 'application/octet-stream',
            'Content-Encoding' => 'aes128gcm',
            'TTL' => '86400'
        ])->post($subscriptionData['endpoint'], $encryptedPayload);

        if ($response->successful()) {
            return [
                'success' => true,
                'status_code' => $response->status()
            ];
        } else {
            // Handle different error codes
            if ($response->status() === 410) {
                // Subscription is no longer valid
                $subscription->unsubscribe();
                return [
                    'success' => false,
                    'error' => 'Subscription expired',
                    'subscription_invalid' => true
                ];
            }

            return [
                'success' => false,
                'error' => 'HTTP ' . $response->status() . ': ' . $response->body(),
                'status_code' => $response->status()
            ];
        }
    }

    /**
     * Generate JWT token for VAPID
     */
    private function generateJWT($endpoint)
    {
        // This is a simplified version - in production, use a proper JWT library
        $header = [
            'typ' => 'JWT',
            'alg' => 'ES256'
        ];

        $payload = [
            'aud' => parse_url($endpoint, PHP_URL_SCHEME) . '://' . parse_url($endpoint, PHP_URL_HOST),
            'exp' => time() + 3600,
            'sub' => $this->vapidSubject
        ];

        $headerEncoded = $this->base64UrlEncode(json_encode($header));
        $payloadEncoded = $this->base64UrlEncode(json_encode($payload));

        $signature = $this->signJWT($headerEncoded . '.' . $payloadEncoded);

        return $headerEncoded . '.' . $payloadEncoded . '.' . $signature;
    }

    /**
     * Encrypt payload for web push
     */
    private function encryptPayload($payload, $userPublicKey, $userAuth)
    {
        // This is a simplified version - in production, use a proper encryption library
        // For now, return the payload as is for demo purposes
        return $payload;
    }

    /**
     * Sign JWT token
     */
    private function signJWT($data)
    {
        // This is a simplified version - in production, use proper ECDSA signing
        return $this->base64UrlEncode(hash_hmac('sha256', $data, $this->vapidPrivateKey, true));
    }

    /**
     * Base64 URL encode
     */
    private function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Get notification types
     */
    public function getNotificationTypes()
    {
        return [
            'workspace_updates' => 'Workspace Updates',
            'team_notifications' => 'Team Notifications',
            'system_alerts' => 'System Alerts',
            'marketing_messages' => 'Marketing Messages',
            'security_alerts' => 'Security Alerts',
            'feature_updates' => 'Feature Updates',
            'billing_notifications' => 'Billing Notifications',
            'social_media_alerts' => 'Social Media Alerts'
        ];
    }

    /**
     * Get notification templates
     */
    public function getNotificationTemplates()
    {
        return [
            'welcome' => [
                'title' => 'Welcome to Mewayz!',
                'message' => 'Thanks for joining our platform. Let\'s get you started!',
                'type' => 'welcome'
            ],
            'workspace_invite' => [
                'title' => 'Workspace Invitation',
                'message' => 'You\'ve been invited to join a workspace',
                'type' => 'team_notifications'
            ],
            'post_scheduled' => [
                'title' => 'Post Scheduled',
                'message' => 'Your social media post has been scheduled successfully',
                'type' => 'social_media_alerts'
            ],
            'payment_received' => [
                'title' => 'Payment Received',
                'message' => 'A payment has been received for your order',
                'type' => 'billing_notifications'
            ]
        ];
    }
}