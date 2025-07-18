<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Models\PaymentMethod;
use App\Models\Invoice;
use App\Models\SubscriptionTransaction;
use App\Models\User;
use App\Services\PaymentService;
use App\Services\SubscriptionService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SubscriptionManagementController extends Controller
{
    protected $paymentService;
    protected $subscriptionService;
    protected $notificationService;

    public function __construct(
        PaymentService $paymentService,
        SubscriptionService $subscriptionService,
        NotificationService $notificationService
    ) {
        $this->paymentService = $paymentService;
        $this->subscriptionService = $subscriptionService;
        $this->notificationService = $notificationService;
    }

    /**
     * Get all available subscription plans (real-time)
     */
    public function getPlans()
    {
        try {
            $plans = SubscriptionPlan::where('is_active', true)
                ->with(['features', 'addons'])
                ->orderBy('order')
                ->get()
                ->map(function ($plan) {
                    return [
                        'id' => $plan->id,
                        'name' => $plan->name,
                        'description' => $plan->description,
                        'price' => $plan->price,
                        'billing_cycle' => $plan->billing_cycle,
                        'features' => $plan->features->pluck('name'),
                        'feature_limits' => $plan->feature_limits,
                        'addons' => $plan->addons->map(function ($addon) {
                            return [
                                'id' => $addon->id,
                                'name' => $addon->name,
                                'price' => $addon->price,
                                'description' => $addon->description
                            ];
                        }),
                        'is_popular' => $plan->is_popular,
                        'discount_percentage' => $plan->discount_percentage,
                        'trial_days' => $plan->trial_days,
                        'setup_fee' => $plan->setup_fee,
                        'created_at' => $plan->created_at,
                        'updated_at' => $plan->updated_at
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $plans,
                'metadata' => [
                    'total_plans' => $plans->count(),
                    'last_updated' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching subscription plans: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch subscription plans'
            ], 500);
        }
    }

    /**
     * Get current user's subscription details
     */
    public function getCurrentSubscription(Request $request)
    {
        try {
            $user = $request->user();
            $subscription = UserSubscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->with(['plan', 'addons', 'invoices'])
                ->first();

            if (!$subscription) {
                return response()->json([
                    'success' => true,
                    'data' => null,
                    'message' => 'No active subscription found'
                ]);
            }

            $subscriptionData = [
                'id' => $subscription->id,
                'plan' => [
                    'id' => $subscription->plan->id,
                    'name' => $subscription->plan->name,
                    'price' => $subscription->plan->price,
                    'billing_cycle' => $subscription->plan->billing_cycle
                ],
                'status' => $subscription->status,
                'current_period_start' => $subscription->current_period_start,
                'current_period_end' => $subscription->current_period_end,
                'trial_ends_at' => $subscription->trial_ends_at,
                'next_billing_date' => $subscription->next_billing_date,
                'cancel_at_period_end' => $subscription->cancel_at_period_end,
                'canceled_at' => $subscription->canceled_at,
                'usage_stats' => $this->getUserUsageStats($user),
                'addons' => $subscription->addons->map(function ($addon) {
                    return [
                        'id' => $addon->id,
                        'name' => $addon->name,
                        'price' => $addon->price,
                        'quantity' => $addon->pivot->quantity ?? 1
                    ];
                }),
                'payment_method' => $subscription->default_payment_method ? [
                    'id' => $subscription->default_payment_method->id,
                    'type' => $subscription->default_payment_method->type,
                    'last_four' => $subscription->default_payment_method->last_four,
                    'brand' => $subscription->default_payment_method->brand,
                    'expires_at' => $subscription->default_payment_method->expires_at
                ] : null,
                'upcoming_invoice' => $this->getUpcomingInvoice($subscription)
            ];

            return response()->json([
                'success' => true,
                'data' => $subscriptionData
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching current subscription: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch subscription details'
            ], 500);
        }
    }

    /**
     * Subscribe to a plan
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'payment_method_data' => 'nullable|array',
            'billing_address' => 'required|array',
            'addons' => 'nullable|array',
            'addons.*.id' => 'exists:subscription_addons,id',
            'addons.*.quantity' => 'integer|min:1'
        ]);

        DB::beginTransaction();
        
        try {
            $user = $request->user();
            $plan = SubscriptionPlan::findOrFail($request->plan_id);

            // Check if user already has an active subscription
            $existingSubscription = UserSubscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->first();

            if ($existingSubscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'User already has an active subscription'
                ], 400);
            }

            // Handle payment method
            $paymentMethod = null;
            if ($request->payment_method_id) {
                $paymentMethod = PaymentMethod::where('id', $request->payment_method_id)
                    ->where('user_id', $user->id)
                    ->first();
            } elseif ($request->payment_method_data) {
                $paymentMethod = $this->paymentService->createPaymentMethod(
                    $user,
                    $request->payment_method_data
                );
            }

            // Create subscription
            $subscription = $this->subscriptionService->createSubscription(
                $user,
                $plan,
                $paymentMethod,
                $request->billing_address,
                $request->addons
            );

            // Process initial payment
            $invoice = $this->paymentService->processSubscriptionPayment($subscription);

            DB::commit();

            // Send welcome notification
            $this->notificationService->sendSubscriptionWelcome($user, $subscription);

            return response()->json([
                'success' => true,
                'data' => [
                    'subscription' => $subscription,
                    'invoice' => $invoice
                ],
                'message' => 'Subscription created successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating subscription: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create subscription: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Change subscription plan
     */
    public function changePlan(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'prorate' => 'boolean',
            'effective_date' => 'nullable|date|after:now'
        ]);

        DB::beginTransaction();

        try {
            $user = $request->user();
            $newPlan = SubscriptionPlan::findOrFail($request->plan_id);
            $subscription = UserSubscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->firstOrFail();

            if ($subscription->plan_id === $newPlan->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is already on this plan'
                ], 400);
            }

            // Calculate prorated amount
            $prorationDetails = null;
            if ($request->prorate ?? true) {
                $prorationDetails = $this->subscriptionService->calculateProration(
                    $subscription,
                    $newPlan
                );
            }

            // Update subscription
            $updatedSubscription = $this->subscriptionService->changePlan(
                $subscription,
                $newPlan,
                $request->effective_date,
                $prorationDetails
            );

            DB::commit();

            // Send notification
            $this->notificationService->sendPlanChangeConfirmation($user, $subscription, $newPlan);

            return response()->json([
                'success' => true,
                'data' => [
                    'subscription' => $updatedSubscription,
                    'proration_details' => $prorationDetails
                ],
                'message' => 'Plan changed successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error changing subscription plan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to change plan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel subscription
     */
    public function cancelSubscription(Request $request)
    {
        $request->validate([
            'cancel_immediately' => 'boolean',
            'cancellation_reason' => 'required|string|max:500',
            'feedback' => 'nullable|string|max:1000'
        ]);

        DB::beginTransaction();

        try {
            $user = $request->user();
            $subscription = UserSubscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->firstOrFail();

            $cancelImmediately = $request->cancel_immediately ?? false;
            
            $canceledSubscription = $this->subscriptionService->cancelSubscription(
                $subscription,
                $cancelImmediately,
                $request->cancellation_reason,
                $request->feedback
            );

            DB::commit();

            // Send cancellation confirmation
            $this->notificationService->sendCancellationConfirmation($user, $canceledSubscription);

            return response()->json([
                'success' => true,
                'data' => $canceledSubscription,
                'message' => $cancelImmediately 
                    ? 'Subscription canceled immediately' 
                    : 'Subscription will be canceled at the end of the current period'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error canceling subscription: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel subscription: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reactivate canceled subscription
     */
    public function reactivateSubscription(Request $request)
    {
        try {
            $user = $request->user();
            $subscription = UserSubscription::where('user_id', $user->id)
                ->where('status', 'canceled')
                ->where('cancel_at_period_end', true)
                ->firstOrFail();

            $reactivatedSubscription = $this->subscriptionService->reactivateSubscription($subscription);

            // Send reactivation confirmation
            $this->notificationService->sendReactivationConfirmation($user, $reactivatedSubscription);

            return response()->json([
                'success' => true,
                'data' => $reactivatedSubscription,
                'message' => 'Subscription reactivated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error reactivating subscription: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to reactivate subscription: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get subscription history
     */
    public function getSubscriptionHistory(Request $request)
    {
        try {
            $user = $request->user();
            $subscriptions = UserSubscription::where('user_id', $user->id)
                ->with(['plan', 'invoices'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $subscriptions
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching subscription history: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch subscription history'
            ], 500);
        }
    }

    /**
     * Get invoices
     */
    public function getInvoices(Request $request)
    {
        try {
            $user = $request->user();
            $invoices = Invoice::where('user_id', $user->id)
                ->with(['subscription.plan', 'items'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $invoices
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching invoices: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch invoices'
            ], 500);
        }
    }

    /**
     * Download invoice
     */
    public function downloadInvoice(Request $request, $invoiceId)
    {
        try {
            $user = $request->user();
            $invoice = Invoice::where('id', $invoiceId)
                ->where('user_id', $user->id)
                ->firstOrFail();

            $pdf = $this->subscriptionService->generateInvoicePDF($invoice);

            return response($pdf, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="invoice-' . $invoice->invoice_number . '.pdf"');

        } catch (\Exception $e) {
            Log::error('Error downloading invoice: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to download invoice'
            ], 500);
        }
    }

    /**
     * Get user usage statistics
     */
    private function getUserUsageStats($user)
    {
        return [
            'current_period_usage' => [
                'api_calls' => $user->getCurrentPeriodApiCalls(),
                'storage_used' => $user->getCurrentStorageUsage(),
                'bandwidth_used' => $user->getCurrentBandwidthUsage(),
                'projects' => $user->projects()->count(),
                'team_members' => $user->teamMembers()->count()
            ],
            'limits' => [
                'api_calls' => $user->subscription->plan->api_calls_limit,
                'storage' => $user->subscription->plan->storage_limit,
                'bandwidth' => $user->subscription->plan->bandwidth_limit,
                'projects' => $user->subscription->plan->projects_limit,
                'team_members' => $user->subscription->plan->team_members_limit
            ]
        ];
    }

    /**
     * Get upcoming invoice preview
     */
    private function getUpcomingInvoice($subscription)
    {
        return $this->subscriptionService->getUpcomingInvoice($subscription);
    }
}