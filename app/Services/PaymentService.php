<?php

namespace App\Services;

use App\Models\User;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use App\Models\UserSubscription;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\CardException;
use Carbon\Carbon;

class PaymentService
{
    protected $stripeService;
    protected $notificationService;

    public function __construct(StripeService $stripeService, NotificationService $notificationService)
    {
        $this->stripeService = $stripeService;
        $this->notificationService = $notificationService;
    }

    /**
     * Create payment method
     */
    public function createPaymentMethod(User $user, array $paymentMethodData)
    {
        DB::beginTransaction();

        try {
            // Create or get Stripe customer
            $stripeCustomer = $this->stripeService->createOrGetCustomer($user);

            // Create payment method in Stripe
            $stripePaymentMethod = $this->stripeService->createPaymentMethod($paymentMethodData);

            // Attach to customer
            $stripePaymentMethod->attach(['customer' => $stripeCustomer->id]);

            // Set as default if it's the first payment method
            $isFirst = !$user->paymentMethods()->exists();
            if ($isFirst) {
                $this->stripeService->setDefaultPaymentMethod($stripeCustomer->id, $stripePaymentMethod->id);
            }

            // Create local payment method
            $paymentMethod = PaymentMethod::create([
                'user_id' => $user->id,
                'stripe_payment_method_id' => $stripePaymentMethod->id,
                'type' => $stripePaymentMethod->type,
                'brand' => $stripePaymentMethod->card->brand ?? null,
                'last_four' => $stripePaymentMethod->card->last4 ?? null,
                'expires_at' => $stripePaymentMethod->card ? 
                    Carbon::create($stripePaymentMethod->card->exp_year, $stripePaymentMethod->card->exp_month, 1)->endOfMonth() : null,
                'billing_address' => $paymentMethodData['billing_address'] ?? null,
                'is_default' => $isFirst,
                'is_active' => true
            ]);

            DB::commit();

            return $paymentMethod;

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating payment method: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Process subscription payment
     */
    public function processSubscriptionPayment(UserSubscription $subscription)
    {
        DB::beginTransaction();

        try {
            // Get the latest invoice from Stripe
            $stripeInvoice = $this->stripeService->getInvoice($subscription->stripe_invoice_id);

            // Create transaction record
            $transaction = Transaction::create([
                'user_id' => $subscription->user_id,
                'payment_method_id' => $subscription->default_payment_method_id,
                'stripe_payment_intent_id' => $stripeInvoice->payment_intent,
                'amount' => $stripeInvoice->amount_paid / 100,
                'currency' => $stripeInvoice->currency,
                'description' => 'Subscription payment for ' . $subscription->plan->name,
                'status' => $stripeInvoice->status === 'paid' ? 'succeeded' : 'pending',
                'metadata' => [
                    'subscription_id' => $subscription->id,
                    'invoice_id' => $stripeInvoice->id
                ]
            ]);

            // Create invoice record
            $invoice = Invoice::create([
                'user_id' => $subscription->user_id,
                'subscription_id' => $subscription->id,
                'stripe_invoice_id' => $stripeInvoice->id,
                'invoice_number' => $stripeInvoice->number,
                'subtotal' => $stripeInvoice->subtotal / 100,
                'tax_amount' => $stripeInvoice->tax / 100,
                'total' => $stripeInvoice->total / 100,
                'status' => $stripeInvoice->status,
                'due_date' => $stripeInvoice->due_date ? Carbon::createFromTimestamp($stripeInvoice->due_date) : null,
                'paid_at' => $stripeInvoice->status_transitions->paid_at ? Carbon::createFromTimestamp($stripeInvoice->status_transitions->paid_at) : null,
                'line_items' => collect($stripeInvoice->lines->data)->map(function ($item) {
                    return [
                        'description' => $item->description,
                        'amount' => $item->amount / 100,
                        'quantity' => $item->quantity
                    ];
                })
            ]);

            // Update subscription status if payment succeeded
            if ($stripeInvoice->status === 'paid') {
                $subscription->update(['status' => 'active']);
            }

            DB::commit();

            return $invoice;

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error processing subscription payment: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Process refund
     */
    public function processRefund(Transaction $transaction, $amount = null, $reason = null)
    {
        DB::beginTransaction();

        try {
            $refundAmount = $amount ?? $transaction->amount;

            // Create refund in Stripe
            $stripeRefund = $this->stripeService->createRefund([
                'payment_intent' => $transaction->stripe_payment_intent_id,
                'amount' => $refundAmount * 100,
                'reason' => $reason ?? 'requested_by_customer'
            ]);

            // Create refund transaction
            $refundTransaction = Transaction::create([
                'user_id' => $transaction->user_id,
                'payment_method_id' => $transaction->payment_method_id,
                'stripe_payment_intent_id' => $stripeRefund->payment_intent,
                'amount' => -$refundAmount,
                'currency' => $transaction->currency,
                'type' => 'refund',
                'description' => 'Refund for: ' . $transaction->description,
                'status' => 'succeeded',
                'metadata' => [
                    'original_transaction_id' => $transaction->id,
                    'refund_reason' => $reason,
                    'stripe_refund_id' => $stripeRefund->id
                ]
            ]);

            // Update original transaction
            $transaction->update([
                'metadata' => array_merge($transaction->metadata ?? [], [
                    'refunded_amount' => $refundAmount,
                    'refund_transaction_id' => $refundTransaction->id
                ])
            ]);

            DB::commit();

            // Send refund notification
            $this->notificationService->sendRefundConfirmation($transaction->user, $refundTransaction);

            return $refundTransaction;

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error processing refund: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle failed payment
     */
    public function handleFailedPayment(UserSubscription $subscription, $stripeInvoice)
    {
        DB::beginTransaction();

        try {
            // Update subscription status
            $subscription->update(['status' => 'past_due']);

            // Create failed transaction record
            $transaction = Transaction::create([
                'user_id' => $subscription->user_id,
                'payment_method_id' => $subscription->default_payment_method_id,
                'stripe_payment_intent_id' => $stripeInvoice->payment_intent,
                'amount' => $stripeInvoice->amount_due / 100,
                'currency' => $stripeInvoice->currency,
                'description' => 'Failed subscription payment for ' . $subscription->plan->name,
                'status' => 'failed',
                'failure_reason' => $stripeInvoice->last_payment_error->message ?? 'Payment failed',
                'metadata' => [
                    'subscription_id' => $subscription->id,
                    'invoice_id' => $stripeInvoice->id
                ]
            ]);

            DB::commit();

            // Send payment failure notification
            $this->notificationService->sendPaymentFailure($subscription->user, $subscription, $transaction);

            // Schedule retry attempts
            $this->schedulePaymentRetry($subscription);

            return $transaction;

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error handling failed payment: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Retry failed payment
     */
    public function retryFailedPayment(UserSubscription $subscription)
    {
        try {
            // Get the latest unpaid invoice
            $stripeInvoice = $this->stripeService->getLatestUnpaidInvoice($subscription->stripe_subscription_id);

            if (!$stripeInvoice) {
                return false;
            }

            // Attempt to pay the invoice
            $paidInvoice = $this->stripeService->payInvoice($stripeInvoice->id);

            if ($paidInvoice->status === 'paid') {
                // Update subscription status
                $subscription->update(['status' => 'active']);

                // Create successful transaction
                $transaction = Transaction::create([
                    'user_id' => $subscription->user_id,
                    'payment_method_id' => $subscription->default_payment_method_id,
                    'stripe_payment_intent_id' => $paidInvoice->payment_intent,
                    'amount' => $paidInvoice->amount_paid / 100,
                    'currency' => $paidInvoice->currency,
                    'description' => 'Retry payment for ' . $subscription->plan->name,
                    'status' => 'succeeded',
                    'metadata' => [
                        'subscription_id' => $subscription->id,
                        'invoice_id' => $paidInvoice->id,
                        'retry_payment' => true
                    ]
                ]);

                // Send success notification
                $this->notificationService->sendPaymentRetrySuccess($subscription->user, $subscription, $transaction);

                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Error retrying failed payment: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Calculate payment statistics
     */
    public function getPaymentStatistics(User $user, $period = '30d')
    {
        $startDate = $this->getStartDate($period);

        $transactions = Transaction::where('user_id', $user->id)
            ->where('created_at', '>=', $startDate)
            ->get();

        return [
            'total_transactions' => $transactions->count(),
            'successful_transactions' => $transactions->where('status', 'succeeded')->count(),
            'failed_transactions' => $transactions->where('status', 'failed')->count(),
            'total_amount' => $transactions->where('status', 'succeeded')->sum('amount'),
            'refunded_amount' => $transactions->where('type', 'refund')->sum('amount'),
            'success_rate' => $transactions->count() > 0 ? 
                ($transactions->where('status', 'succeeded')->count() / $transactions->count()) * 100 : 0,
            'average_transaction_amount' => $transactions->where('status', 'succeeded')->avg('amount') ?? 0,
            'payment_methods_used' => $transactions->pluck('payment_method_id')->unique()->count()
        ];
    }

    /**
     * Schedule payment retry
     */
    private function schedulePaymentRetry(UserSubscription $subscription)
    {
        // Schedule retry attempts (1 day, 3 days, 7 days)
        $retryDays = [1, 3, 7];
        
        foreach ($retryDays as $day) {
            dispatch(new \App\Jobs\RetryFailedPayment($subscription))
                ->delay(now()->addDays($day));
        }
    }

    /**
     * Get start date for period
     */
    private function getStartDate($period)
    {
        switch ($period) {
            case '7d': return now()->subDays(7);
            case '30d': return now()->subDays(30);
            case '90d': return now()->subDays(90);
            case '1y': return now()->subYear();
            default: return now()->subDays(30);
        }
    }
}