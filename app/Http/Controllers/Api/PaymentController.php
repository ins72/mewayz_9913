<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use App\Models\Invoice;
use App\Models\User;
use App\Services\PaymentService;
use App\Services\StripeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\CardException;

class PaymentController extends Controller
{
    protected $paymentService;
    protected $stripeService;

    public function __construct(PaymentService $paymentService, StripeService $stripeService)
    {
        $this->paymentService = $paymentService;
        $this->stripeService = $stripeService;
    }

    /**
     * Get user's saved payment methods
     */
    public function getPaymentMethods(Request $request)
    {
        try {
            $user = $request->user();
            $paymentMethods = PaymentMethod::where('user_id', $user->id)
                ->where('is_active', true)
                ->orderBy('is_default', 'desc')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($method) {
                    return [
                        'id' => $method->id,
                        'type' => $method->type,
                        'brand' => $method->brand,
                        'last_four' => $method->last_four,
                        'expires_at' => $method->expires_at,
                        'is_default' => $method->is_default,
                        'billing_address' => $method->billing_address,
                        'created_at' => $method->created_at
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $paymentMethods
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching payment methods: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch payment methods'
            ], 500);
        }
    }

    /**
     * Add a new payment method
     */
    public function addPaymentMethod(Request $request)
    {
        $request->validate([
            'payment_method_id' => 'required|string', // Stripe payment method ID
            'set_as_default' => 'boolean',
            'billing_address' => 'required|array',
            'billing_address.name' => 'required|string',
            'billing_address.line1' => 'required|string',
            'billing_address.line2' => 'nullable|string',
            'billing_address.city' => 'required|string',
            'billing_address.state' => 'required|string',
            'billing_address.postal_code' => 'required|string',
            'billing_address.country' => 'required|string'
        ]);

        DB::beginTransaction();

        try {
            $user = $request->user();
            
            // Create or get Stripe customer
            $stripeCustomer = $this->stripeService->createOrGetCustomer($user);

            // Attach payment method to customer
            $stripePaymentMethod = $this->stripeService->attachPaymentMethod(
                $request->payment_method_id,
                $stripeCustomer->id
            );

            // Set as default if requested or if it's the first payment method
            if ($request->set_as_default || !$user->paymentMethods()->exists()) {
                // Remove default from existing methods
                $user->paymentMethods()->update(['is_default' => false]);
                
                // Set as default in Stripe
                $this->stripeService->setDefaultPaymentMethod(
                    $stripeCustomer->id,
                    $stripePaymentMethod->id
                );
            }

            // Save to database
            $paymentMethod = PaymentMethod::create([
                'user_id' => $user->id,
                'stripe_payment_method_id' => $stripePaymentMethod->id,
                'type' => $stripePaymentMethod->type,
                'brand' => $stripePaymentMethod->card->brand ?? null,
                'last_four' => $stripePaymentMethod->card->last4 ?? null,
                'expires_at' => $stripePaymentMethod->card ? 
                    Carbon::create($stripePaymentMethod->card->exp_year, $stripePaymentMethod->card->exp_month, 1)->endOfMonth() : null,
                'billing_address' => $request->billing_address,
                'is_default' => $request->set_as_default || !$user->paymentMethods()->exists(),
                'is_active' => true
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $paymentMethod->id,
                    'type' => $paymentMethod->type,
                    'brand' => $paymentMethod->brand,
                    'last_four' => $paymentMethod->last_four,
                    'expires_at' => $paymentMethod->expires_at,
                    'is_default' => $paymentMethod->is_default,
                    'billing_address' => $paymentMethod->billing_address
                ],
                'message' => 'Payment method added successfully'
            ]);

        } catch (CardException $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Card error: ' . $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error adding payment method: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to add payment method'
            ], 500);
        }
    }

    /**
     * Remove a payment method
     */
    public function removePaymentMethod(Request $request, $paymentMethodId)
    {
        DB::beginTransaction();

        try {
            $user = $request->user();
            $paymentMethod = PaymentMethod::where('id', $paymentMethodId)
                ->where('user_id', $user->id)
                ->firstOrFail();

            // Check if this is the only payment method and user has active subscriptions
            if ($user->paymentMethods()->count() === 1 && $user->hasActiveSubscriptions()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot remove the only payment method with active subscriptions'
                ], 400);
            }

            // Detach from Stripe
            $this->stripeService->detachPaymentMethod($paymentMethod->stripe_payment_method_id);

            // If this was the default, set another as default
            if ($paymentMethod->is_default) {
                $newDefault = $user->paymentMethods()
                    ->where('id', '!=', $paymentMethod->id)
                    ->first();
                
                if ($newDefault) {
                    $newDefault->update(['is_default' => true]);
                    $this->stripeService->setDefaultPaymentMethod(
                        $user->stripe_customer_id,
                        $newDefault->stripe_payment_method_id
                    );
                }
            }

            $paymentMethod->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment method removed successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error removing payment method: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove payment method'
            ], 500);
        }
    }

    /**
     * Set default payment method
     */
    public function setDefaultPaymentMethod(Request $request, $paymentMethodId)
    {
        DB::beginTransaction();

        try {
            $user = $request->user();
            $paymentMethod = PaymentMethod::where('id', $paymentMethodId)
                ->where('user_id', $user->id)
                ->firstOrFail();

            // Remove default from all methods
            $user->paymentMethods()->update(['is_default' => false]);

            // Set new default
            $paymentMethod->update(['is_default' => true]);

            // Update in Stripe
            $this->stripeService->setDefaultPaymentMethod(
                $user->stripe_customer_id,
                $paymentMethod->stripe_payment_method_id
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Default payment method updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error setting default payment method: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to set default payment method'
            ], 500);
        }
    }

    /**
     * Process a one-time payment
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.5',
            'currency' => 'required|string|size:3',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'description' => 'required|string',
            'metadata' => 'nullable|array',
            'confirm' => 'boolean'
        ]);

        DB::beginTransaction();

        try {
            $user = $request->user();
            $paymentMethod = null;

            if ($request->payment_method_id) {
                $paymentMethod = PaymentMethod::where('id', $request->payment_method_id)
                    ->where('user_id', $user->id)
                    ->firstOrFail();
            } else {
                $paymentMethod = $user->paymentMethods()
                    ->where('is_default', true)
                    ->first();
            }

            if (!$paymentMethod) {
                return response()->json([
                    'success' => false,
                    'message' => 'No payment method available'
                ], 400);
            }

            // Create payment intent
            $paymentIntent = $this->stripeService->createPaymentIntent([
                'amount' => $request->amount * 100, // Convert to cents
                'currency' => $request->currency,
                'customer' => $user->stripe_customer_id,
                'payment_method' => $paymentMethod->stripe_payment_method_id,
                'description' => $request->description,
                'metadata' => $request->metadata ?? [],
                'confirm' => $request->confirm ?? false
            ]);

            // Create transaction record
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'payment_method_id' => $paymentMethod->id,
                'stripe_payment_intent_id' => $paymentIntent->id,
                'amount' => $request->amount,
                'currency' => $request->currency,
                'description' => $request->description,
                'status' => $paymentIntent->status,
                'metadata' => $request->metadata ?? []
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'transaction_id' => $transaction->id,
                    'payment_intent_id' => $paymentIntent->id,
                    'client_secret' => $paymentIntent->client_secret,
                    'status' => $paymentIntent->status
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error processing payment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to process payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle payment webhooks
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = $this->stripeService->constructWebhookEvent($payload, $sigHeader);

            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $this->handlePaymentSucceeded($event->data->object);
                    break;
                case 'payment_intent.payment_failed':
                    $this->handlePaymentFailed($event->data->object);
                    break;
                case 'invoice.payment_succeeded':
                    $this->handleInvoicePaymentSucceeded($event->data->object);
                    break;
                case 'invoice.payment_failed':
                    $this->handleInvoicePaymentFailed($event->data->object);
                    break;
                case 'customer.subscription.updated':
                    $this->handleSubscriptionUpdated($event->data->object);
                    break;
                case 'customer.subscription.deleted':
                    $this->handleSubscriptionDeleted($event->data->object);
                    break;
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Webhook error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Get payment history
     */
    public function getPaymentHistory(Request $request)
    {
        try {
            $user = $request->user();
            $transactions = Transaction::where('user_id', $user->id)
                ->with('paymentMethod')
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $transactions
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching payment history: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch payment history'
            ], 500);
        }
    }

    /**
     * Handle successful payment
     */
    private function handlePaymentSucceeded($paymentIntent)
    {
        $transaction = Transaction::where('stripe_payment_intent_id', $paymentIntent->id)->first();
        if ($transaction) {
            $transaction->update([
                'status' => 'succeeded',
                'processed_at' => now()
            ]);
        }
    }

    /**
     * Handle failed payment
     */
    private function handlePaymentFailed($paymentIntent)
    {
        $transaction = Transaction::where('stripe_payment_intent_id', $paymentIntent->id)->first();
        if ($transaction) {
            $transaction->update([
                'status' => 'failed',
                'failure_reason' => $paymentIntent->last_payment_error->message ?? 'Unknown error'
            ]);
        }
    }

    /**
     * Handle successful invoice payment
     */
    private function handleInvoicePaymentSucceeded($invoice)
    {
        // Update invoice status and handle subscription activation
        $localInvoice = Invoice::where('stripe_invoice_id', $invoice->id)->first();
        if ($localInvoice) {
            $localInvoice->update(['status' => 'paid']);
            
            // Activate subscription if needed
            if ($localInvoice->subscription && $localInvoice->subscription->status === 'pending') {
                $localInvoice->subscription->update(['status' => 'active']);
            }
        }
    }

    /**
     * Handle failed invoice payment
     */
    private function handleInvoicePaymentFailed($invoice)
    {
        $localInvoice = Invoice::where('stripe_invoice_id', $invoice->id)->first();
        if ($localInvoice) {
            $localInvoice->update(['status' => 'failed']);
            
            // Handle subscription failure
            if ($localInvoice->subscription) {
                $this->paymentService->handleFailedPayment($localInvoice->subscription, $invoice);
            }
        }
    }

    /**
     * Handle subscription updates
     */
    private function handleSubscriptionUpdated($subscription)
    {
        // Update local subscription data
        $localSubscription = UserSubscription::where('stripe_subscription_id', $subscription->id)->first();
        if ($localSubscription) {
            $localSubscription->update([
                'status' => $subscription->status,
                'current_period_start' => Carbon::createFromTimestamp($subscription->current_period_start),
                'current_period_end' => Carbon::createFromTimestamp($subscription->current_period_end)
            ]);
        }
    }

    /**
     * Handle subscription deletion
     */
    private function handleSubscriptionDeleted($subscription)
    {
        $localSubscription = UserSubscription::where('stripe_subscription_id', $subscription->id)->first();
        if ($localSubscription) {
            $localSubscription->update(['status' => 'canceled']);
        }
    }
}