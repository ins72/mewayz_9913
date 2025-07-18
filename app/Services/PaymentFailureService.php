<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\PaymentFailure;
use App\Models\Workspace;
use App\Services\EmailService;
use App\Services\NotificationService;
use App\Jobs\ProcessPaymentRetry;
use App\Jobs\SendPaymentFailureNotification;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PaymentFailureService
{
    public function __construct(
        private EmailService $emailService,
        private NotificationService $notificationService,
        private StripeService $stripeService
    ) {}

    /**
     * Handle a payment failure.
     */
    public function handlePaymentFailure(
        Subscription $subscription,
        string $invoiceId,
        string $failureReason,
        string $failureCode = null
    ): PaymentFailure {
        Log::info('Processing payment failure', [
            'subscription_id' => $subscription->id,
            'invoice_id' => $invoiceId,
            'failure_reason' => $failureReason,
            'failure_code' => $failureCode,
        ]);

        // Create payment failure record
        $paymentFailure = PaymentFailure::create([
            'subscription_id' => $subscription->id,
            'stripe_invoice_id' => $invoiceId,
            'failure_reason' => $failureReason,
            'failure_code' => $failureCode,
            'retry_attempt' => 0,
            'next_retry_at' => now()->addDay(),
        ]);

        // Update subscription status
        $subscription->update([
            'status' => 'past_due',
            'grace_period_ends_at' => now()->addDays(7),
            'last_payment_failed_at' => now(),
            'retry_count' => $subscription->retry_count + 1,
        ]);

        // Start grace period
        $this->startGracePeriod($subscription);

        // Send immediate notification
        $this->sendImmediateFailureNotification($subscription, $paymentFailure);

        // Schedule retry attempts
        $this->scheduleRetryAttempts($subscription, $paymentFailure);

        return $paymentFailure;
    }

    /**
     * Start grace period for failed payment.
     */
    private function startGracePeriod(Subscription $subscription): void
    {
        $workspace = $subscription->workspace;
        
        // Keep features enabled during grace period
        Log::info('Grace period started', [
            'subscription_id' => $subscription->id,
            'workspace_id' => $workspace->id,
            'grace_period_ends_at' => $subscription->grace_period_ends_at,
        ]);

        // Send grace period notification
        $this->notificationService->sendGracePeriodNotification($workspace, $subscription);
    }

    /**
     * Schedule retry attempts based on smart retry logic.
     */
    private function scheduleRetryAttempts(Subscription $subscription, PaymentFailure $paymentFailure): void
    {
        $retrySchedule = $this->getRetrySchedule();
        
        foreach ($retrySchedule as $attempt => $days) {
            $retryDate = now()->addDays($days);
            
            // Avoid weekends and holidays
            $retryDate = $this->adjustRetryDate($retryDate);
            
            ProcessPaymentRetry::dispatch($subscription, $paymentFailure, $attempt)
                ->delay($retryDate);
            
            Log::info('Scheduled payment retry', [
                'subscription_id' => $subscription->id,
                'attempt' => $attempt,
                'retry_date' => $retryDate,
            ]);
        }
    }

    /**
     * Get intelligent retry schedule.
     */
    private function getRetrySchedule(): array
    {
        return [
            1 => 1,   // Day 1: First retry
            2 => 3,   // Day 3: Second retry
            3 => 5,   // Day 5: Third retry
            4 => 7,   // Day 7: Final retry before suspension
            5 => 14,  // Day 14: Post-suspension retry
            6 => 21,  // Day 21: Win-back attempt
            7 => 28,  // Day 28: Final win-back attempt
        ];
    }

    /**
     * Adjust retry date to avoid weekends and holidays.
     */
    private function adjustRetryDate(Carbon $date): Carbon
    {
        // Skip weekends
        if ($date->isWeekend()) {
            $date = $date->next(Carbon::MONDAY);
        }

        // Skip common holidays (simplified)
        $holidays = [
            '12-25', '12-31', '01-01', '07-04', '11-28', '11-29'
        ];

        if (in_array($date->format('m-d'), $holidays)) {
            $date = $date->addDay();
        }

        return $date;
    }

    /**
     * Process payment retry attempt.
     */
    public function processRetryAttempt(Subscription $subscription, PaymentFailure $paymentFailure, int $attempt): bool
    {
        Log::info('Processing payment retry', [
            'subscription_id' => $subscription->id,
            'payment_failure_id' => $paymentFailure->id,
            'attempt' => $attempt,
        ]);

        try {
            // Attempt to retry payment via Stripe
            $success = $this->stripeService->retryPayment($paymentFailure->stripe_invoice_id);

            if ($success) {
                $this->handleSuccessfulRetry($subscription, $paymentFailure);
                return true;
            } else {
                $this->handleFailedRetry($subscription, $paymentFailure, $attempt);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Payment retry failed', [
                'subscription_id' => $subscription->id,
                'attempt' => $attempt,
                'error' => $e->getMessage(),
            ]);

            $this->handleFailedRetry($subscription, $paymentFailure, $attempt);
            return false;
        }
    }

    /**
     * Handle successful payment retry.
     */
    private function handleSuccessfulRetry(Subscription $subscription, PaymentFailure $paymentFailure): void
    {
        // Update subscription status
        $subscription->update([
            'status' => 'active',
            'grace_period_ends_at' => null,
            'retry_count' => 0,
            'last_payment_failed_at' => null,
        ]);

        // Mark payment failure as resolved
        $paymentFailure->markAsResolved('payment_retry_success');

        // Send success notification
        $this->sendPaymentSuccessNotification($subscription);

        Log::info('Payment retry successful', [
            'subscription_id' => $subscription->id,
            'payment_failure_id' => $paymentFailure->id,
        ]);
    }

    /**
     * Handle failed payment retry.
     */
    private function handleFailedRetry(Subscription $subscription, PaymentFailure $paymentFailure, int $attempt): void
    {
        $maxAttempts = count($this->getRetrySchedule());

        if ($attempt >= $maxAttempts) {
            // Final attempt failed - suspend account
            $this->suspendSubscription($subscription);
        } else {
            // Schedule next retry
            $paymentFailure->scheduleNextRetry();
            $this->sendRetryFailureNotification($subscription, $paymentFailure, $attempt);
        }
    }

    /**
     * Suspend subscription after all retries failed.
     */
    private function suspendSubscription(Subscription $subscription): void
    {
        $subscription->update([
            'status' => 'suspended',
            'grace_period_ends_at' => null,
        ]);

        // Disable features but keep data
        $workspace = $subscription->workspace;
        $this->disableWorkspaceFeatures($workspace);

        // Send suspension notification
        $this->sendSuspensionNotification($subscription);

        // Start win-back campaign
        $this->startWinBackCampaign($subscription);

        Log::info('Subscription suspended', [
            'subscription_id' => $subscription->id,
            'workspace_id' => $workspace->id,
        ]);
    }

    /**
     * Send immediate payment failure notification.
     */
    private function sendImmediateFailureNotification(Subscription $subscription, PaymentFailure $paymentFailure): void
    {
        $workspace = $subscription->workspace;
        $owner = $workspace->owner();

        if (!$owner) {
            return;
        }

        $emailData = [
            'user_name' => $owner->name,
            'workspace_name' => $workspace->name,
            'failure_reason' => $paymentFailure->getFailureReasonDisplayName(),
            'amount' => $subscription->getMonthlyCost(),
            'retry_date' => $paymentFailure->next_retry_at,
            'grace_period_ends' => $subscription->grace_period_ends_at,
            'update_payment_url' => route('subscription.payment-method'),
        ];

        $this->emailService->sendPaymentFailureEmail($owner->email, $emailData);

        // Send in-app notification
        $this->notificationService->sendPaymentFailureNotification($workspace, $paymentFailure);
    }

    /**
     * Send payment success notification.
     */
    private function sendPaymentSuccessNotification(Subscription $subscription): void
    {
        $workspace = $subscription->workspace;
        $owner = $workspace->owner();

        if (!$owner) {
            return;
        }

        $emailData = [
            'user_name' => $owner->name,
            'workspace_name' => $workspace->name,
            'amount' => $subscription->getMonthlyCost(),
            'next_billing_date' => $subscription->getNextBillingDate(),
        ];

        $this->emailService->sendPaymentSuccessEmail($owner->email, $emailData);
        $this->notificationService->sendPaymentSuccessNotification($workspace);
    }

    /**
     * Send retry failure notification.
     */
    private function sendRetryFailureNotification(Subscription $subscription, PaymentFailure $paymentFailure, int $attempt): void
    {
        $workspace = $subscription->workspace;
        $owner = $workspace->owner();

        if (!$owner) {
            return;
        }

        $daysRemaining = $subscription->daysLeftInGracePeriod();
        $urgency = $this->getNotificationUrgency($daysRemaining);

        $emailData = [
            'user_name' => $owner->name,
            'workspace_name' => $workspace->name,
            'attempt' => $attempt,
            'days_remaining' => $daysRemaining,
            'urgency' => $urgency,
            'failure_reason' => $paymentFailure->getFailureReasonDisplayName(),
            'next_retry_date' => $paymentFailure->next_retry_at,
            'update_payment_url' => route('subscription.payment-method'),
            'contact_support_url' => route('support.contact'),
        ];

        $template = match ($urgency) {
            'low' => 'payment-failure-gentle',
            'medium' => 'payment-failure-reminder',
            'high' => 'payment-failure-urgent',
            default => 'payment-failure-gentle',
        };

        $this->emailService->sendEmail($owner->email, $template, $emailData);
    }

    /**
     * Get notification urgency based on days remaining.
     */
    private function getNotificationUrgency(int $daysRemaining): string
    {
        if ($daysRemaining <= 1) {
            return 'high';
        } elseif ($daysRemaining <= 3) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    /**
     * Send suspension notification.
     */
    private function sendSuspensionNotification(Subscription $subscription): void
    {
        $workspace = $subscription->workspace;
        $owner = $workspace->owner();

        if (!$owner) {
            return;
        }

        $emailData = [
            'user_name' => $owner->name,
            'workspace_name' => $workspace->name,
            'suspension_date' => now(),
            'reactivate_url' => route('subscription.reactivate'),
            'contact_support_url' => route('support.contact'),
        ];

        $this->emailService->sendSuspensionEmail($owner->email, $emailData);
        $this->notificationService->sendSuspensionNotification($workspace);
    }

    /**
     * Start win-back campaign.
     */
    private function startWinBackCampaign(Subscription $subscription): void
    {
        $workspace = $subscription->workspace;
        
        // Schedule win-back emails
        $this->scheduleWinBackEmails($workspace);
        
        // Create retention offers
        $this->createRetentionOffers($subscription);
        
        Log::info('Win-back campaign started', [
            'subscription_id' => $subscription->id,
            'workspace_id' => $workspace->id,
        ]);
    }

    /**
     * Schedule win-back email sequence.
     */
    private function scheduleWinBackEmails(Workspace $workspace): void
    {
        $winBackSchedule = [
            1 => 'We miss you - 50% off to come back',
            7 => 'Your data is waiting for you',
            14 => 'Last chance - special offer inside',
            30 => 'We\'d love to have you back',
            60 => 'Final reminder - your account expires soon',
        ];

        foreach ($winBackSchedule as $days => $subject) {
            SendPaymentFailureNotification::dispatch($workspace, 'win_back', $subject)
                ->delay(now()->addDays($days));
        }
    }

    /**
     * Create retention offers.
     */
    private function createRetentionOffers(Subscription $subscription): void
    {
        $offers = [
            [
                'type' => 'discount',
                'title' => '50% Off for 3 Months',
                'description' => 'Get 50% off your subscription for 3 months',
                'discount_percentage' => 50,
                'duration_months' => 3,
                'expires_at' => now()->addDays(30),
            ],
            [
                'type' => 'downgrade',
                'title' => 'Switch to a Lower Plan',
                'description' => 'Keep essential features at a lower cost',
                'expires_at' => now()->addDays(30),
            ],
            [
                'type' => 'pause',
                'title' => 'Pause Your Subscription',
                'description' => 'Take a break for up to 90 days',
                'pause_duration' => 90,
                'expires_at' => now()->addDays(7),
            ],
        ];

        foreach ($offers as $offer) {
            $subscription->retentionOffers()->create($offer);
        }
    }

    /**
     * Disable workspace features while preserving data.
     */
    private function disableWorkspaceFeatures(Workspace $workspace): void
    {
        $workspace->features()->updateExistingPivot(
            $workspace->features()->pluck('key')->toArray(),
            ['is_enabled' => false]
        );
    }

    /**
     * Get payment failure analytics.
     */
    public function getPaymentFailureAnalytics(int $days = 30): array
    {
        $failures = PaymentFailure::where('created_at', '>=', now()->subDays($days))
            ->with(['subscription.workspace'])
            ->get();

        $analytics = [
            'total_failures' => $failures->count(),
            'resolved_failures' => $failures->where('resolved_at', '!=', null)->count(),
            'pending_failures' => $failures->where('resolved_at', null)->count(),
            'by_failure_code' => $failures->groupBy('failure_code')->map->count(),
            'by_retry_attempt' => $failures->groupBy('retry_attempt')->map->count(),
            'recovery_rate' => 0,
            'average_recovery_time' => 0,
        ];

        if ($analytics['total_failures'] > 0) {
            $analytics['recovery_rate'] = ($analytics['resolved_failures'] / $analytics['total_failures']) * 100;
        }

        $resolvedFailures = $failures->where('resolved_at', '!=', null);
        if ($resolvedFailures->count() > 0) {
            $totalRecoveryTime = $resolvedFailures->sum(function ($failure) {
                return $failure->created_at->diffInHours($failure->resolved_at);
            });
            $analytics['average_recovery_time'] = $totalRecoveryTime / $resolvedFailures->count();
        }

        return $analytics;
    }

    /**
     * Get subscription health metrics.
     */
    public function getSubscriptionHealthMetrics(): array
    {
        $subscriptions = Subscription::with('workspace')->get();

        $metrics = [
            'total_subscriptions' => $subscriptions->count(),
            'active_subscriptions' => $subscriptions->where('status', 'active')->count(),
            'past_due_subscriptions' => $subscriptions->where('status', 'past_due')->count(),
            'suspended_subscriptions' => $subscriptions->where('status', 'suspended')->count(),
            'cancelled_subscriptions' => $subscriptions->where('status', 'cancelled')->count(),
            'health_score' => 0,
            'at_risk_count' => 0,
            'grace_period_count' => 0,
        ];

        $metrics['at_risk_count'] = $subscriptions->filter(function ($sub) {
            return $sub->isPastDue() && $sub->daysLeftInGracePeriod() <= 3;
        })->count();

        $metrics['grace_period_count'] = $subscriptions->filter(function ($sub) {
            return $sub->isInGracePeriod();
        })->count();

        if ($metrics['total_subscriptions'] > 0) {
            $metrics['health_score'] = ($metrics['active_subscriptions'] / $metrics['total_subscriptions']) * 100;
        }

        return $metrics;
    }
}