<?php

namespace App\Services;

use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Models\PaymentMethod;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Models\SubscriptionAddon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class SubscriptionService
{
    protected $paymentService;
    protected $stripeService;

    public function __construct(PaymentService $paymentService, StripeService $stripeService)
    {
        $this->paymentService = $paymentService;
        $this->stripeService = $stripeService;
    }

    /**
     * Create a new subscription
     */
    public function createSubscription(User $user, SubscriptionPlan $plan, PaymentMethod $paymentMethod = null, array $billingAddress = [], array $addons = [])
    {
        DB::beginTransaction();

        try {
            // Create Stripe customer if not exists
            $stripeCustomer = $this->stripeService->createOrGetCustomer($user);

            // Calculate trial end date
            $trialEnd = $plan->trial_days ? now()->addDays($plan->trial_days) : null;

            // Create subscription in Stripe
            $stripeSubscription = $this->stripeService->createSubscription([
                'customer' => $stripeCustomer->id,
                'items' => [
                    [
                        'price' => $plan->stripe_price_id,
                        'quantity' => 1,
                    ],
                ],
                'default_payment_method' => $paymentMethod ? $paymentMethod->stripe_payment_method_id : null,
                'trial_end' => $trialEnd ? $trialEnd->timestamp : null,
                'billing_address' => $billingAddress,
                'expand' => ['latest_invoice.payment_intent'],
            ]);

            // Create local subscription
            $subscription = UserSubscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'stripe_subscription_id' => $stripeSubscription->id,
                'status' => $stripeSubscription->status,
                'amount' => $plan->price,
                'billing_cycle' => $plan->billing_cycle,
                'current_period_start' => Carbon::createFromTimestamp($stripeSubscription->current_period_start),
                'current_period_end' => Carbon::createFromTimestamp($stripeSubscription->current_period_end),
                'trial_ends_at' => $trialEnd,
                'next_billing_date' => Carbon::createFromTimestamp($stripeSubscription->current_period_end),
                'default_payment_method_id' => $paymentMethod ? $paymentMethod->id : null,
                'metadata' => [
                    'billing_address' => $billingAddress,
                    'stripe_customer_id' => $stripeCustomer->id
                ]
            ]);

            // Add addons if any
            if (!empty($addons)) {
                $this->addSubscriptionAddons($subscription, $addons);
            }

            // Create invoice
            $invoice = $this->createInvoiceFromStripe($subscription, $stripeSubscription->latest_invoice);

            DB::commit();

            return $subscription;

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating subscription: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Change subscription plan
     */
    public function changePlan(UserSubscription $subscription, SubscriptionPlan $newPlan, $effectiveDate = null, $prorationDetails = null)
    {
        DB::beginTransaction();

        try {
            // Update subscription in Stripe
            $stripeSubscription = $this->stripeService->updateSubscription($subscription->stripe_subscription_id, [
                'items' => [
                    [
                        'id' => $subscription->stripe_subscription_item_id,
                        'price' => $newPlan->stripe_price_id,
                    ],
                ],
                'proration_behavior' => $prorationDetails ? 'create_prorations' : 'none',
                'proration_date' => $effectiveDate ? Carbon::parse($effectiveDate)->timestamp : null,
            ]);

            // Update local subscription
            $subscription->update([
                'plan_id' => $newPlan->id,
                'amount' => $newPlan->price,
                'billing_cycle' => $newPlan->billing_cycle,
                'current_period_start' => Carbon::createFromTimestamp($stripeSubscription->current_period_start),
                'current_period_end' => Carbon::createFromTimestamp($stripeSubscription->current_period_end),
                'next_billing_date' => Carbon::createFromTimestamp($stripeSubscription->current_period_end),
            ]);

            // Create proration invoice if needed
            if ($prorationDetails) {
                $this->createProrationInvoice($subscription, $prorationDetails);
            }

            DB::commit();

            return $subscription->fresh();

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error changing subscription plan: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Cancel subscription
     */
    public function cancelSubscription(UserSubscription $subscription, $immediately = false, $reason = null, $feedback = null)
    {
        DB::beginTransaction();

        try {
            if ($immediately) {
                // Cancel immediately in Stripe
                $stripeSubscription = $this->stripeService->cancelSubscription($subscription->stripe_subscription_id);
                
                $subscription->update([
                    'status' => 'canceled',
                    'canceled_at' => now(),
                    'cancel_at_period_end' => false,
                    'metadata' => array_merge($subscription->metadata ?? [], [
                        'cancellation_reason' => $reason,
                        'cancellation_feedback' => $feedback,
                        'canceled_immediately' => true
                    ])
                ]);
            } else {
                // Cancel at period end
                $stripeSubscription = $this->stripeService->updateSubscription($subscription->stripe_subscription_id, [
                    'cancel_at_period_end' => true,
                    'cancellation_details' => [
                        'comment' => $reason,
                        'feedback' => $feedback
                    ]
                ]);

                $subscription->update([
                    'cancel_at_period_end' => true,
                    'canceled_at' => now(),
                    'metadata' => array_merge($subscription->metadata ?? [], [
                        'cancellation_reason' => $reason,
                        'cancellation_feedback' => $feedback
                    ])
                ]);
            }

            DB::commit();

            return $subscription->fresh();

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error canceling subscription: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Reactivate subscription
     */
    public function reactivateSubscription(UserSubscription $subscription)
    {
        DB::beginTransaction();

        try {
            // Reactivate in Stripe
            $stripeSubscription = $this->stripeService->updateSubscription($subscription->stripe_subscription_id, [
                'cancel_at_period_end' => false,
            ]);

            $subscription->update([
                'status' => $stripeSubscription->status,
                'cancel_at_period_end' => false,
                'canceled_at' => null,
                'metadata' => array_merge($subscription->metadata ?? [], [
                    'reactivated_at' => now()->toISOString()
                ])
            ]);

            DB::commit();

            return $subscription->fresh();

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error reactivating subscription: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Calculate proration for plan change
     */
    public function calculateProration(UserSubscription $subscription, SubscriptionPlan $newPlan)
    {
        $currentPlan = $subscription->plan;
        $daysRemaining = $subscription->current_period_end->diffInDays(now());
        $totalDays = $subscription->current_period_start->diffInDays($subscription->current_period_end);

        $unusedAmount = ($currentPlan->price * $daysRemaining) / $totalDays;
        $newAmount = ($newPlan->price * $daysRemaining) / $totalDays;
        $prorationAmount = $newAmount - $unusedAmount;

        return [
            'current_plan_unused' => round($unusedAmount, 2),
            'new_plan_amount' => round($newAmount, 2),
            'proration_amount' => round($prorationAmount, 2),
            'days_remaining' => $daysRemaining,
            'effective_date' => now()->toISOString()
        ];
    }

    /**
     * Add addons to subscription
     */
    public function addSubscriptionAddons(UserSubscription $subscription, array $addons)
    {
        foreach ($addons as $addonData) {
            $addon = SubscriptionAddon::find($addonData['id']);
            if ($addon) {
                $subscription->addons()->attach($addon->id, [
                    'quantity' => $addonData['quantity'] ?? 1,
                    'price' => $addon->price
                ]);
            }
        }
    }

    /**
     * Get upcoming invoice
     */
    public function getUpcomingInvoice(UserSubscription $subscription)
    {
        try {
            $upcomingInvoice = $this->stripeService->getUpcomingInvoice($subscription->stripe_subscription_id);
            
            return [
                'amount' => $upcomingInvoice->amount_due / 100,
                'currency' => $upcomingInvoice->currency,
                'period_start' => Carbon::createFromTimestamp($upcomingInvoice->period_start),
                'period_end' => Carbon::createFromTimestamp($upcomingInvoice->period_end),
                'due_date' => Carbon::createFromTimestamp($upcomingInvoice->due_date),
                'line_items' => collect($upcomingInvoice->lines->data)->map(function ($item) {
                    return [
                        'description' => $item->description,
                        'amount' => $item->amount / 100,
                        'quantity' => $item->quantity
                    ];
                })
            ];

        } catch (\Exception $e) {
            Log::error('Error getting upcoming invoice: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate invoice PDF
     */
    public function generateInvoicePDF(Invoice $invoice)
    {
        $data = [
            'invoice' => $invoice,
            'user' => $invoice->user,
            'subscription' => $invoice->subscription,
            'company' => [
                'name' => config('app.name'),
                'address' => config('app.company_address'),
                'phone' => config('app.company_phone'),
                'email' => config('app.company_email')
            ]
        ];

        $pdf = Pdf::loadView('invoices.template', $data);
        return $pdf->output();
    }

    /**
     * Create invoice from Stripe invoice
     */
    private function createInvoiceFromStripe(UserSubscription $subscription, $stripeInvoice)
    {
        return Invoice::create([
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
    }

    /**
     * Create proration invoice
     */
    private function createProrationInvoice(UserSubscription $subscription, array $prorationDetails)
    {
        return Invoice::create([
            'user_id' => $subscription->user_id,
            'subscription_id' => $subscription->id,
            'invoice_number' => 'PRORATION-' . now()->format('YmdHis'),
            'subtotal' => $prorationDetails['proration_amount'],
            'tax_amount' => 0,
            'total' => $prorationDetails['proration_amount'],
            'status' => 'paid',
            'paid_at' => now(),
            'line_items' => [
                [
                    'description' => 'Plan change proration',
                    'amount' => $prorationDetails['proration_amount'],
                    'quantity' => 1
                ]
            ]
        ]);
    }
}