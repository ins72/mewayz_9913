<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StripePaymentController extends Controller
{
    /**
     * Define fixed payment packages - NEVER allow frontend to set prices
     */
    const PACKAGES = [
        'starter' => ['amount' => 9.99, 'currency' => 'USD', 'name' => 'Starter Package'],
        'professional' => ['amount' => 29.99, 'currency' => 'USD', 'name' => 'Professional Package'],
        'enterprise' => ['amount' => 99.99, 'currency' => 'USD', 'name' => 'Enterprise Package'],
    ];
    
    /**
     * Create a new checkout session
     */
    public function createCheckoutSession(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'package_id' => 'required_without:stripe_price_id|string',
                'stripe_price_id' => 'required_without:package_id|string',
                'quantity' => 'integer|min:1|max:10',
                'success_url' => 'required|url',
                'cancel_url' => 'required|url',
                'metadata' => 'array'
            ]);
            
            // Get user info
            $user = Auth::user();
            $userEmail = $user ? $user->email : $request->input('email');
            
            // Prepare checkout data
            $checkoutData = [
                'success_url' => $request->input('success_url'),
                'cancel_url' => $request->input('cancel_url'),
                'metadata' => array_merge($request->input('metadata', []), [
                    'user_id' => $user ? (string)$user->id : '',
                    'email' => $userEmail ?: '',
                    'source' => 'laravel_api'
                ])
            ];
            
            // Handle different payment types
            if ($request->has('package_id')) {
                // Fixed package - amount defined on server
                $packageId = $request->input('package_id');
                
                if (!isset(self::PACKAGES[$packageId])) {
                    return response()->json(['error' => 'Invalid package selected'], 400);
                }
                
                $package = self::PACKAGES[$packageId];
                $checkoutData['amount'] = $package['amount'];
                $checkoutData['currency'] = $package['currency'];
                $amount = $package['amount'];
                $currency = $package['currency'];
                $stripePriceId = null;
                $quantity = 1;
            } else {
                // Stripe price ID with quantity
                $stripePriceId = $request->input('stripe_price_id');
                $quantity = $request->input('quantity', 1);
                $checkoutData['stripe_price_id'] = $stripePriceId;
                $checkoutData['quantity'] = $quantity;
                $amount = 0; // Will be set by Stripe
                $currency = 'USD';
            }
            
            // Create Python process to handle Stripe integration
            $webhookUrl = url('/api/webhook/stripe');
            $pythonScript = base_path('stripe_integration.py');
            
            // Convert checkout data to JSON
            $inputData = json_encode($checkoutData);
            
            // Execute Python script
            $escapedInput = escapeshellarg($inputData);
            $stripeApiKey = env('STRIPE_API_KEY');
            $command = "cd " . base_path() . " && STRIPE_API_KEY={$stripeApiKey} echo {$escapedInput} | /root/.venv/bin/python3 {$pythonScript} create_session '{$webhookUrl}'";
            $output = shell_exec($command);
            
            if (!$output) {
                return response()->json(['error' => 'Failed to create checkout session'], 500);
            }
            
            $result = json_decode($output, true);
            
            if (!$result['success']) {
                return response()->json(['error' => $result['error']], 500);
            }
            
            // Create payment transaction record
            $transaction = PaymentTransaction::create([
                'session_id' => $result['session_id'],
                'user_id' => $user ? $user->id : null,
                'email' => $userEmail,
                'amount' => $amount,
                'currency' => $currency,
                'metadata' => $checkoutData['metadata'],
                'payment_status' => 'initiated',
                'stripe_price_id' => $stripePriceId,
                'quantity' => $quantity
            ]);
            
            Log::info('Stripe checkout session created', [
                'session_id' => $result['session_id'],
                'user_id' => $user ? $user->id : null,
                'amount' => $amount,
                'currency' => $currency
            ]);
            
            return response()->json([
                'success' => true,
                'url' => $result['url'],
                'session_id' => $result['session_id']
            ]);
            
        } catch (\Exception $e) {
            Log::error('Stripe checkout session creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 'Failed to create checkout session'], 500);
        }
    }
    
    /**
     * Get checkout session status
     */
    public function getCheckoutStatus($sessionId)
    {
        try {
            // Find transaction in database
            $transaction = PaymentTransaction::where('session_id', $sessionId)->first();
            
            if (!$transaction) {
                return response()->json(['error' => 'Transaction not found'], 404);
            }
            
            // Get status from Stripe
            $pythonScript = base_path('stripe_integration.py');
            $stripeApiKey = env('STRIPE_API_KEY');
            $command = "cd " . base_path() . " && STRIPE_API_KEY={$stripeApiKey} /root/.venv/bin/python3 {$pythonScript} get_status '{$sessionId}'";
            $output = shell_exec($command);
            
            if (!$output) {
                return response()->json(['error' => 'Failed to get checkout status'], 500);
            }
            
            $result = json_decode($output, true);
            
            if (!$result['success']) {
                return response()->json(['error' => $result['error']], 500);
            }
            
            // Update transaction status if changed and not already processed
            if ($transaction->payment_status !== $result['payment_status'] && 
                !in_array($transaction->payment_status, ['paid', 'failed', 'expired'])) {
                
                $transaction->update([
                    'payment_status' => $result['payment_status']
                ]);
                
                // If payment successful, perform post-payment actions
                if ($result['payment_status'] === 'paid') {
                    $this->handleSuccessfulPayment($transaction);
                }
            }
            
            return response()->json([
                'success' => true,
                'status' => $result['status'],
                'payment_status' => $result['payment_status'],
                'amount_total' => $result['amount_total'],
                'currency' => $result['currency'],
                'metadata' => $result['metadata']
            ]);
            
        } catch (\Exception $e) {
            Log::error('Stripe checkout status check failed', [
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['error' => 'Failed to get checkout status'], 500);
        }
    }
    
    /**
     * Handle Stripe webhook
     */
    public function handleWebhook(Request $request)
    {
        try {
            $signature = $request->header('Stripe-Signature');
            $body = $request->getContent();
            
            if (!$signature) {
                return response()->json(['error' => 'Missing stripe signature'], 400);
            }
            
            // Prepare webhook data
            $webhookData = json_encode([
                'body' => $body,
                'signature' => $signature
            ]);
            
            // Execute Python script
            $pythonScript = base_path('stripe_integration.py');
            $escapedWebhookData = escapeshellarg($webhookData);
            $command = "cd " . base_path() . " && echo {$escapedWebhookData} | /root/.venv/bin/python3 {$pythonScript} handle_webhook";
            $output = shell_exec($command);
            
            if (!$output) {
                return response()->json(['error' => 'Failed to process webhook'], 500);
            }
            
            $result = json_decode($output, true);
            
            if (!$result['success']) {
                return response()->json(['error' => $result['error']], 500);
            }
            
            // Process webhook based on event type
            if ($result['event_type'] === 'checkout.session.completed') {
                $sessionId = $result['session_id'];
                
                // Find and update transaction
                $transaction = PaymentTransaction::where('session_id', $sessionId)->first();
                
                if ($transaction && $transaction->payment_status !== 'paid') {
                    $transaction->update(['payment_status' => 'paid']);
                    $this->handleSuccessfulPayment($transaction);
                }
            }
            
            Log::info('Stripe webhook processed', [
                'event_type' => $result['event_type'],
                'session_id' => $result['session_id'],
                'payment_status' => $result['payment_status']
            ]);
            
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            Log::error('Stripe webhook processing failed', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }
    
    /**
     * Handle successful payment
     */
    private function handleSuccessfulPayment(PaymentTransaction $transaction)
    {
        // Add credits or activate subscription based on package
        if ($transaction->user) {
            Log::info('Processing successful payment', [
                'user_id' => $transaction->user_id,
                'amount' => $transaction->amount,
                'currency' => $transaction->currency
            ]);
            
            // Add your business logic here
            // For example: add credits to user account, activate subscription, etc.
        }
    }
    
    /**
     * Get available packages
     */
    public function getPackages()
    {
        return response()->json([
            'success' => true,
            'packages' => self::PACKAGES
        ]);
    }
}
