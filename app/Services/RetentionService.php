<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\Workspace;
use App\Models\User;
use App\Models\RetentionAttempt;
use App\Services\EmailService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class RetentionService
{
    public function __construct(
        private EmailService $emailService,
        private NotificationService $notificationService,
        private SubscriptionService $subscriptionService
    ) {}

    /**
     * Handle cancellation request with retention flow.
     */
    public function handleCancellationRequest(
        Subscription $subscription,
        string $reason = null,
        string $feedback = null
    ): array {
        Log::info('Processing cancellation request', [
            'subscription_id' => $subscription->id,
            'reason' => $reason,
        ]);

        // Record cancellation attempt
        $retentionAttempt = RetentionAttempt::create([
            'subscription_id' => $subscription->id,
            'type' => 'cancellation_save',
            'reason' => $reason,
            'feedback' => $feedback,
            'success' => false,
        ]);

        // Generate retention offers based on cancellation reason
        $offers = $this->generateRetentionOffers($subscription, $reason);

        // Track cancellation intent
        $this->trackCancellationIntent($subscription, $reason);

        return [
            'retention_attempt_id' => $retentionAttempt->id,
            'offers' => $offers,
            'next_step' => 'show_retention_offers',
        ];
    }

    /**
     * Generate retention offers based on cancellation reason.
     */
    private function generateRetentionOffers(Subscription $subscription, ?string $reason): array
    {
        $offers = [];
        $currentPrice = $subscription->getMonthlyCost();

        switch ($reason) {
            case 'too_expensive':
                $offers[] = [
                    'type' => 'discount',
                    'title' => '50% Off for 3 Months',
                    'description' => 'We understand budget is important. Get 50% off for the next 3 months.',
                    'discount_percentage' => 50,
                    'duration_months' => 3,
                    'new_price' => $currentPrice * 0.5,
                    'total_savings' => $currentPrice * 0.5 * 3,
                    'cta' => 'Accept Discount',
                ];

                // Downgrade offer
                $lowerPlan = $this->findLowerPlan($subscription->plan);
                if ($lowerPlan) {
                    $offers[] = [
                        'type' => 'downgrade',
                        'title' => 'Switch to ' . $lowerPlan->name,
                        'description' => 'Keep essential features at a lower cost.',
                        'plan_id' => $lowerPlan->id,
                        'plan_name' => $lowerPlan->name,
                        'new_price' => $lowerPlan->calculateMonthlyPrice([]),
                        'monthly_savings' => $currentPrice - $lowerPlan->calculateMonthlyPrice([]),
                        'cta' => 'Switch Plan',
                    ];
                }

                // Annual discount
                $offers[] = [
                    'type' => 'annual_discount',
                    'title' => 'Switch to Annual - Save 20%',
                    'description' => 'Save 20% with annual billing. No commitment after the first year.',
                    'discount_percentage' => 20,
                    'yearly_price' => $currentPrice * 12 * 0.8,
                    'total_savings' => $currentPrice * 12 * 0.2,
                    'cta' => 'Switch to Annual',
                ];
                break;

            case 'not_using_enough':
                $offers[] = [
                    'type' => 'feature_training',
                    'title' => 'Free 1-on-1 Training Session',
                    'description' => 'Let us help you get the most out of your subscription with a personalized training session.',
                    'includes' => [
                        'Personal onboarding call',
                        'Feature walkthrough',
                        'Custom workflow setup',
                        'Ongoing support',
                    ],
                    'cta' => 'Schedule Training',
                ];

                $offers[] = [
                    'type' => 'feature_audit',
                    'title' => 'Free Feature Audit',
                    'description' => 'We\'ll analyze your usage and recommend the perfect feature set for your needs.',
                    'includes' => [
                        'Usage analysis',
                        'Feature recommendations',
                        'Cost optimization',
                        'Custom plan creation',
                    ],
                    'cta' => 'Get Audit',
                ];
                break;

            case 'missing_features':
                $offers[] = [
                    'type' => 'feature_preview',
                    'title' => 'Beta Access to Upcoming Features',
                    'description' => 'Get early access to features we\'re building that might be exactly what you need.',
                    'includes' => [
                        'Beta feature access',
                        'Direct feedback line',
                        'Priority feature requests',
                        'Special beta pricing',
                    ],
                    'cta' => 'Join Beta',
                ];

                $offers[] = [
                    'type' => 'custom_development',
                    'title' => 'Custom Feature Development',
                    'description' => 'For Enterprise customers, we can build custom features to meet your specific needs.',
                    'includes' => [
                        'Custom feature development',
                        'Priority support',
                        'Dedicated success manager',
                        'SLA guarantees',
                    ],
                    'cta' => 'Contact Sales',
                ];
                break;

            case 'technical_issues':
                $offers[] = [
                    'type' => 'priority_support',
                    'title' => 'Priority Technical Support',
                    'description' => 'Get dedicated support to resolve any technical issues you\'re experiencing.',
                    'includes' => [
                        'Direct line to engineering',
                        'Priority issue resolution',
                        'Extended support hours',
                        'Video call support',
                    ],
                    'cta' => 'Get Support',
                ];

                $offers[] = [
                    'type' => 'setup_assistance',
                    'title' => 'Free Setup Assistance',
                    'description' => 'Our team will help you set up everything properly to avoid technical issues.',
                    'includes' => [
                        'Complete setup review',
                        'Configuration assistance',
                        'Integration help',
                        'Performance optimization',
                    ],
                    'cta' => 'Get Help',
                ];
                break;

            case 'found_alternative':
                $offers[] = [
                    'type' => 'feature_match',
                    'title' => 'Feature Matching Guarantee',
                    'description' => 'We\'ll match any feature you found elsewhere or give you 3 months free.',
                    'includes' => [
                        'Feature comparison analysis',
                        'Custom feature development',
                        'Price matching',
                        '3 months free guarantee',
                    ],
                    'cta' => 'Compare Features',
                ];

                $offers[] = [
                    'type' => 'migration_assistance',
                    'title' => 'Free Migration Back Guarantee',
                    'description' => 'Try the alternative. If you want to come back, we\'ll help you migrate for free.',
                    'includes' => [
                        'Data export assistance',
                        'Free migration back',
                        'Extended trial period',
                        'No setup fees',
                    ],
                    'cta' => 'Learn More',
                ];
                break;

            default:
                // Generic retention offers
                $offers[] = [
                    'type' => 'pause',
                    'title' => 'Pause Your Subscription',
                    'description' => 'Take a break for up to 3 months. Your data will be safe and you can resume anytime.',
                    'pause_duration' => 90,
                    'resume_date' => now()->addDays(90)->format('F j, Y'),
                    'cta' => 'Pause Subscription',
                ];

                $offers[] = [
                    'type' => 'discount',
                    'title' => '30% Off for 6 Months',
                    'description' => 'Stay with us and save 30% for the next 6 months.',
                    'discount_percentage' => 30,
                    'duration_months' => 6,
                    'new_price' => $currentPrice * 0.7,
                    'total_savings' => $currentPrice * 0.3 * 6,
                    'cta' => 'Accept Discount',
                ];
                break;
        }

        return $offers;
    }

    /**
     * Apply retention offer.
     */
    public function applyRetentionOffer(
        Subscription $subscription,
        string $retentionAttemptId,
        array $offer
    ): array {
        $retentionAttempt = RetentionAttempt::findOrFail($retentionAttemptId);

        try {
            switch ($offer['type']) {
                case 'discount':
                    $result = $this->applyDiscountOffer($subscription, $offer);
                    break;

                case 'downgrade':
                    $result = $this->applyDowngradeOffer($subscription, $offer);
                    break;

                case 'pause':
                    $result = $this->applyPauseOffer($subscription, $offer);
                    break;

                case 'annual_discount':
                    $result = $this->applyAnnualDiscountOffer($subscription, $offer);
                    break;

                case 'feature_training':
                    $result = $this->scheduleFeatureTraining($subscription, $offer);
                    break;

                case 'priority_support':
                    $result = $this->enablePrioritySupport($subscription, $offer);
                    break;

                default:
                    $result = ['success' => false, 'message' => 'Unknown offer type'];
            }

            if ($result['success']) {
                $retentionAttempt->update([
                    'success' => true,
                    'offer_type' => $offer['type'],
                    'offer_data' => $offer,
                ]);

                $this->sendRetentionSuccessNotification($subscription, $offer);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to apply retention offer', [
                'subscription_id' => $subscription->id,
                'offer_type' => $offer['type'],
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Apply discount offer.
     */
    private function applyDiscountOffer(Subscription $subscription, array $offer): array
    {
        // This would integrate with Stripe to apply coupon
        // For now, we'll just record the offer
        $subscription->update([
            'metadata' => array_merge($subscription->metadata ?? [], [
                'retention_discount' => [
                    'percentage' => $offer['discount_percentage'],
                    'duration_months' => $offer['duration_months'],
                    'applied_at' => now(),
                    'expires_at' => now()->addMonths($offer['duration_months']),
                ],
            ]),
        ]);

        return [
            'success' => true,
            'message' => 'Discount applied successfully',
            'discount_percentage' => $offer['discount_percentage'],
            'duration_months' => $offer['duration_months'],
        ];
    }

    /**
     * Apply downgrade offer.
     */
    private function applyDowngradeOffer(Subscription $subscription, array $offer): array
    {
        $newPlan = \App\Models\SubscriptionPlan::findOrFail($offer['plan_id']);
        
        // Use subscription service to change plan
        $updatedSubscription = $this->subscriptionService->changePlan(
            $subscription,
            $newPlan->id,
            [], // Features would be determined by the new plan
            'monthly'
        );

        return [
            'success' => true,
            'message' => 'Plan changed successfully',
            'new_plan' => $newPlan->name,
            'new_price' => $newPlan->calculateMonthlyPrice([]),
        ];
    }

    /**
     * Apply pause offer.
     */
    private function applyPauseOffer(Subscription $subscription, array $offer): array
    {
        $pauseUntil = now()->addDays($offer['pause_duration']);
        
        $subscription->update([
            'status' => 'paused',
            'metadata' => array_merge($subscription->metadata ?? [], [
                'pause_data' => [
                    'paused_at' => now(),
                    'resume_at' => $pauseUntil,
                    'pause_duration' => $offer['pause_duration'],
                ],
            ]),
        ]);

        // Schedule resume
        \App\Jobs\ResumeSubscription::dispatch($subscription)->delay($pauseUntil);

        return [
            'success' => true,
            'message' => 'Subscription paused successfully',
            'resume_date' => $pauseUntil,
        ];
    }

    /**
     * Schedule feature training.
     */
    private function scheduleFeatureTraining(Subscription $subscription, array $offer): array
    {
        // This would integrate with a scheduling system
        // For now, we'll just record the request
        $subscription->update([
            'metadata' => array_merge($subscription->metadata ?? [], [
                'feature_training_requested' => [
                    'requested_at' => now(),
                    'status' => 'pending',
                    'offer_data' => $offer,
                ],
            ]),
        ]);

        // Send notification to support team
        $this->notifySupportTeam($subscription, 'feature_training_requested');

        return [
            'success' => true,
            'message' => 'Training session scheduled. Our team will contact you within 24 hours.',
        ];
    }

    /**
     * Enable priority support.
     */
    private function enablePrioritySupport(Subscription $subscription, array $offer): array
    {
        $subscription->update([
            'metadata' => array_merge($subscription->metadata ?? [], [
                'priority_support' => [
                    'enabled_at' => now(),
                    'expires_at' => now()->addMonths(3),
                    'offer_data' => $offer,
                ],
            ]),
        ]);

        return [
            'success' => true,
            'message' => 'Priority support enabled for 3 months',
        ];
    }

    /**
     * Process final cancellation after retention attempts.
     */
    public function processFinalCancellation(
        Subscription $subscription,
        string $retentionAttemptId,
        bool $cancelImmediately = false
    ): array {
        $retentionAttempt = RetentionAttempt::findOrFail($retentionAttemptId);

        // Mark retention attempt as unsuccessful
        $retentionAttempt->update(['success' => false]);

        // Process cancellation
        $cancelledSubscription = $this->subscriptionService->cancelSubscription(
            $subscription,
            $retentionAttempt->reason,
            $retentionAttempt->feedback,
            $cancelImmediately
        );

        // Send cancellation confirmation
        $this->sendCancellationConfirmation($cancelledSubscription);

        // Schedule win-back campaign
        $this->scheduleWinBackCampaign($cancelledSubscription);

        return [
            'success' => true,
            'message' => 'Subscription cancelled successfully',
            'subscription' => $cancelledSubscription,
            'access_until' => $cancelledSubscription->current_period_end,
        ];
    }

    /**
     * Send cancellation confirmation.
     */
    private function sendCancellationConfirmation(Subscription $subscription): void
    {
        $workspace = $subscription->workspace;
        $owner = $workspace->owner();

        if (!$owner) {
            return;
        }

        $emailData = [
            'user_name' => $owner->name,
            'workspace_name' => $workspace->name,
            'cancelled_at' => now(),
            'access_until' => $subscription->current_period_end,
            'reactivate_url' => route('subscription.reactivate'),
            'export_data_url' => route('account.export-data'),
        ];

        $this->emailService->sendCancellationConfirmationEmail($owner->email, $emailData);
    }

    /**
     * Schedule win-back campaign.
     */
    private function scheduleWinBackCampaign(Subscription $subscription): void
    {
        $workspace = $subscription->workspace;
        
        $winBackSchedule = [
            7 => 'We miss you - Come back with 50% off',
            30 => 'Your data is still safe - Easy to reactivate',
            60 => 'Special offer - 3 months free',
            90 => 'Final reminder - Account will be deleted soon',
        ];

        foreach ($winBackSchedule as $days => $subject) {
            \App\Jobs\SendWinBackEmail::dispatch($workspace, $subject)
                ->delay(now()->addDays($days));
        }
    }

    /**
     * Get retention analytics.
     */
    public function getRetentionAnalytics(int $days = 30): array
    {
        $attempts = RetentionAttempt::where('created_at', '>=', now()->subDays($days))
            ->with('subscription')
            ->get();

        $analytics = [
            'total_attempts' => $attempts->count(),
            'successful_attempts' => $attempts->where('success', true)->count(),
            'by_type' => $attempts->groupBy('type')->map->count(),
            'by_reason' => $attempts->groupBy('reason')->map->count(),
            'success_rate' => 0,
            'most_effective_offers' => [],
            'revenue_saved' => 0,
        ];

        if ($analytics['total_attempts'] > 0) {
            $analytics['success_rate'] = ($analytics['successful_attempts'] / $analytics['total_attempts']) * 100;
        }

        // Calculate revenue saved
        $successfulAttempts = $attempts->where('success', true);
        $analytics['revenue_saved'] = $successfulAttempts->sum(function ($attempt) {
            return $attempt->subscription->getMonthlyCost() * 12; // Assume 12 months retained
        });

        return $analytics;
    }

    /**
     * Find a lower-priced plan.
     */
    private function findLowerPlan($currentPlan): ?\App\Models\SubscriptionPlan
    {
        return \App\Models\SubscriptionPlan::where('feature_price_monthly', '<', $currentPlan->feature_price_monthly)
            ->where('is_active', true)
            ->orderBy('feature_price_monthly', 'desc')
            ->first();
    }

    /**
     * Track cancellation intent for analytics.
     */
    private function trackCancellationIntent(Subscription $subscription, ?string $reason): void
    {
        Log::info('Cancellation intent tracked', [
            'subscription_id' => $subscription->id,
            'reason' => $reason,
            'plan_id' => $subscription->plan_id,
            'monthly_cost' => $subscription->getMonthlyCost(),
        ]);
    }

    /**
     * Notify support team.
     */
    private function notifySupportTeam(Subscription $subscription, string $type): void
    {
        // This would integrate with support system
        Log::info('Support team notified', [
            'subscription_id' => $subscription->id,
            'type' => $type,
        ]);
    }

    /**
     * Send retention success notification.
     */
    private function sendRetentionSuccessNotification(Subscription $subscription, array $offer): void
    {
        $workspace = $subscription->workspace;
        $owner = $workspace->owner();

        if (!$owner) {
            return;
        }

        $emailData = [
            'user_name' => $owner->name,
            'workspace_name' => $workspace->name,
            'offer_type' => $offer['type'],
            'offer_title' => $offer['title'],
            'offer_description' => $offer['description'],
        ];

        $this->emailService->sendRetentionSuccessEmail($owner->email, $emailData);
    }
}