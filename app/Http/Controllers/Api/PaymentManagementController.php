<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\PaymentTransaction;
use App\Models\User;
use App\Models\SubscriptionPlan;

class PaymentManagementController extends Controller
{
    private $stripeApiKey;

    public function __construct()
    {
        $this->stripeApiKey = env('STRIPE_API_KEY');
    }

    /**
     * Create a checkout session for subscription plans
     */
    public function createCheckoutSession(Request $request)
    {
        try {
            $request->validate([
                'package_id' => 'required|string',
                'success_url' => 'required|url',
                'cancel_url' => 'required|url',
                'metadata' => 'nullable|array'
            ]);

            $user = $request->user();
            
            // Define fixed subscription packages (prevent price manipulation)
            $packages = [
                'starter' => ['amount' => 9.99, 'name' => 'Starter Plan'],
                'professional' => ['amount' => 29.99, 'name' => 'Professional Plan'],
                'enterprise' => ['amount' => 99.99, 'name' => 'Enterprise Plan']
            ];

            $packageId = $request->input('package_id');
            if (!isset($packages[$packageId])) {
                return response()->json(['error' => 'Invalid subscription package'], 400);
            }

            $package = $packages[$packageId];
            $amount = $package['amount'];
            $currency = 'usd';

            // Generate unique session ID
            $sessionId = Str::uuid();

            // Create payment transaction record
            $transaction = PaymentTransaction::create([
                'id' => Str::uuid(),
                'user_id' => $user ? $user->id : null,
                'session_id' => $sessionId,
                'amount' => $amount,
                'currency' => $currency,
                'payment_status' => 'pending',
                'service_type' => 'subscription',
                'service_id' => $packageId,
                'metadata' => json_encode(array_merge([
                    'package_id' => $packageId,
                    'package_name' => $package['name'],
                    'user_email' => $user ? $user->email : null
                ], $request->input('metadata', [])))
            ]);

            // Create Stripe checkout session using emergentintegrations
            $hostUrl = $request->getSchemeAndHttpHost();
            $webhookUrl = $hostUrl . '/api/webhook/stripe';
            
            $stripeCheckout = new \EmergentIntegrations\Payments\Stripe\Checkout\StripeCheckout(
                $this->stripeApiKey,
                $webhookUrl
            );

            $checkoutRequest = new \EmergentIntegrations\Payments\Stripe\Checkout\CheckoutSessionRequest(
                amount: $amount,
                currency: $currency,
                success_url: $request->input('success_url'),
                cancel_url: $request->input('cancel_url'),
                metadata: [
                    'session_id' => $sessionId,
                    'user_id' => $user ? $user->id : null,
                    'package_id' => $packageId
                ]
            );

            $session = $stripeCheckout->createCheckoutSession($checkoutRequest);

            // Update transaction with Stripe session ID
            $transaction->update([
                'stripe_session_id' => $session->session_id
            ]);

            return response()->json([
                'success' => true,
                'url' => $session->url,
                'session_id' => $session->session_id,
                'local_session_id' => $sessionId
            ]);

        } catch (\Exception $e) {
            Log::error('Payment checkout creation failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to create checkout session',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check checkout session status
     */
    public function checkCheckoutStatus(Request $request, $sessionId)
    {
        try {
            $transaction = PaymentTransaction::where('session_id', $sessionId)
                ->orWhere('stripe_session_id', $sessionId)
                ->first();

            if (!$transaction) {
                return response()->json(['error' => 'Payment session not found'], 404);
            }

            // If already processed, return cached status
            if (in_array($transaction->payment_status, ['paid', 'failed', 'expired'])) {
                return response()->json([
                    'status' => $transaction->payment_status,
                    'payment_status' => $transaction->payment_status,
                    'amount_total' => $transaction->amount * 100, // Convert to cents
                    'currency' => $transaction->currency,
                    'metadata' => json_decode($transaction->metadata, true)
                ]);
            }

            // Check with Stripe
            $stripeCheckout = new \EmergentIntegrations\Payments\Stripe\Checkout\StripeCheckout(
                $this->stripeApiKey,
                ''
            );

            $checkoutStatus = $stripeCheckout->getCheckoutStatus($transaction->stripe_session_id);

            // Update transaction status
            $transaction->update([
                'payment_status' => $checkoutStatus->payment_status
            ]);

            // Process successful payment
            if ($checkoutStatus->payment_status === 'paid') {
                $this->processSuccessfulPayment($transaction);
            }

            return response()->json([
                'status' => $checkoutStatus->status,
                'payment_status' => $checkoutStatus->payment_status,
                'amount_total' => $checkoutStatus->amount_total,
                'currency' => $checkoutStatus->currency,
                'metadata' => $checkoutStatus->metadata
            ]);

        } catch (\Exception $e) {
            Log::error('Payment status check failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to check payment status',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle Stripe webhook
     */
    public function handleStripeWebhook(Request $request)
    {
        try {
            $stripeCheckout = new \EmergentIntegrations\Payments\Stripe\Checkout\StripeCheckout(
                $this->stripeApiKey,
                ''
            );

            $webhookResponse = $stripeCheckout->handleWebhook(
                $request->getContent(),
                $request->header('Stripe-Signature')
            );

            // Find transaction by session ID
            $transaction = PaymentTransaction::where('stripe_session_id', $webhookResponse->session_id)->first();

            if (!$transaction) {
                Log::warning('Webhook received for unknown session: ' . $webhookResponse->session_id);
                return response()->json(['received' => true]);
            }

            // Update transaction status
            $transaction->update([
                'payment_status' => $webhookResponse->payment_status
            ]);

            // Process successful payment
            if ($webhookResponse->payment_status === 'paid') {
                $this->processSuccessfulPayment($transaction);
            }

            return response()->json(['received' => true]);

        } catch (\Exception $e) {
            Log::error('Webhook processing failed: ' . $e->getMessage());
            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * Process successful payment
     */
    private function processSuccessfulPayment($transaction)
    {
        try {
            $metadata = json_decode($transaction->metadata, true);
            
            if ($transaction->service_type === 'subscription') {
                // Create or update user subscription
                $user = User::find($transaction->user_id);
                if ($user) {
                    // Find or create subscription plan
                    $plan = SubscriptionPlan::where('slug', $metadata['package_id'])->first();
                    if (!$plan) {
                        // Create plan if it doesn't exist
                        $planData = [
                            'starter' => ['name' => 'Starter Plan', 'base_price' => 9.99],
                            'professional' => ['name' => 'Professional Plan', 'base_price' => 29.99],
                            'enterprise' => ['name' => 'Enterprise Plan', 'base_price' => 99.99]
                        ];
                        
                        if (isset($planData[$metadata['package_id']])) {
                            $plan = SubscriptionPlan::create([
                                'name' => $planData[$metadata['package_id']]['name'],
                                'slug' => $metadata['package_id'],
                                'base_price' => $planData[$metadata['package_id']]['base_price'],
                                'type' => $metadata['package_id'] === 'starter' ? 'free' : 'professional',
                                'is_active' => true
                            ]);
                        }
                    }

                    if ($plan) {
                        // Create user subscription
                        DB::table('user_subscriptions')->updateOrInsert(
                            ['user_id' => $user->id],
                            [
                                'plan_id' => $plan->id,
                                'status' => 'active',
                                'amount' => $transaction->amount,
                                'billing_cycle' => 'monthly',
                                'current_period_start' => now(),
                                'current_period_end' => now()->addMonth(),
                                'created_at' => now(),
                                'updated_at' => now()
                            ]
                        );
                    }
                }
            }

            Log::info('Payment processed successfully', [
                'transaction_id' => $transaction->id,
                'amount' => $transaction->amount,
                'service_type' => $transaction->service_type
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to process successful payment: ' . $e->getMessage());
        }
    }

    /**
     * Get user payment history
     */
    public function getPaymentHistory(Request $request)
    {
        try {
            $user = $request->user();
            
            $transactions = PaymentTransaction::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'transactions' => $transactions->items(),
                'pagination' => [
                    'current_page' => $transactions->currentPage(),
                    'total_pages' => $transactions->lastPage(),
                    'total_items' => $transactions->total()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get payment history: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to retrieve payment history'
            ], 500);
        }
    }

    /**
     * Get available subscription packages
     */
    public function getSubscriptionPackages()
    {
        try {
            $packages = [
                'starter' => [
                    'id' => 'starter',
                    'name' => 'Starter Plan',
                    'price' => 9.99,
                    'currency' => 'USD',
                    'features' => [
                        'Up to 5 bio sites',
                        'Basic analytics',
                        'Standard support',
                        'Mobile app access'
                    ]
                ],
                'professional' => [
                    'id' => 'professional',
                    'name' => 'Professional Plan',
                    'price' => 29.99,
                    'currency' => 'USD',
                    'features' => [
                        'Unlimited bio sites',
                        'Advanced analytics',
                        'Priority support',
                        'Custom domains',
                        'White-label options'
                    ]
                ],
                'enterprise' => [
                    'id' => 'enterprise',
                    'name' => 'Enterprise Plan',
                    'price' => 99.99,
                    'currency' => 'USD',
                    'features' => [
                        'All Professional features',
                        'Advanced integrations',
                        'Dedicated support',
                        'Custom development',
                        'API access'
                    ]
                ]
            ];

            return response()->json([
                'success' => true,
                'packages' => $packages
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get subscription packages: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to retrieve subscription packages'
            ], 500);
        }
    }
}