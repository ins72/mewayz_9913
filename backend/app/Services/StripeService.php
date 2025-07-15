<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Webhook;
use Illuminate\Support\Facades\Log;

class StripeService
{
    protected $webhookSecret;

    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_API_KEY'));
        $this->webhookSecret = env('STRIPE_WEBHOOK_SECRET', '');
    }

    /**
     * Create a Stripe checkout session
     */
    public function createCheckoutSession(array $requestData): array
    {
        try {
            // Extract request data
            $amount = $requestData['amount'] ?? null;
            $currency = strtolower($requestData['currency'] ?? 'usd');
            $stripePriceId = $requestData['stripe_price_id'] ?? null;
            $quantity = $requestData['quantity'] ?? 1;
            $successUrl = $requestData['success_url'];
            $cancelUrl = $requestData['cancel_url'];
            $metadata = $requestData['metadata'] ?? [];

            // Create checkout session parameters
            $sessionParams = [
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'mode' => 'payment',
                'metadata' => $metadata
            ];

            if ($stripePriceId) {
                // Fixed price product
                $sessionParams['line_items'] = [[
                    'price' => $stripePriceId,
                    'quantity' => $quantity,
                ]];
            } else {
                // Custom amount - create a price on the fly
                $sessionParams['line_items'] = [[
                    'price_data' => [
                        'currency' => $currency,
                        'product_data' => [
                            'name' => 'Payment',
                        ],
                        'unit_amount' => intval(floatval($amount) * 100), // Convert to cents
                    ],
                    'quantity' => 1,
                ]];
            }

            // Create session
            $session = Session::create($sessionParams);

            return [
                'success' => true,
                'url' => $session->url,
                'session_id' => $session->id
            ];

        } catch (\Exception $e) {
            Log::error('Stripe checkout session creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get the status of a checkout session
     */
    public function getCheckoutStatus(string $sessionId): array
    {
        try {
            $session = Session::retrieve($sessionId);

            // Map Stripe status to our format
            $paymentStatus = 'unpaid';
            if ($session->payment_status === 'paid') {
                $paymentStatus = 'paid';
            } elseif ($session->payment_status === 'unpaid') {
                $paymentStatus = 'unpaid';
            } elseif ($session->payment_status === 'no_payment_required') {
                $paymentStatus = 'paid';
            }

            return [
                'success' => true,
                'status' => $session->status,
                'payment_status' => $paymentStatus,
                'amount_total' => $session->amount_total,
                'currency' => $session->currency,
                'metadata' => $session->metadata ? $session->metadata->toArray() : []
            ];

        } catch (\Exception $e) {
            Log::error('Stripe checkout status check failed', [
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Handle Stripe webhook
     */
    public function handleWebhook(string $payload, string $sigHeader): array
    {
        try {
            if (!empty($this->webhookSecret)) {
                // Verify webhook signature
                $event = Webhook::constructEvent($payload, $sigHeader, $this->webhookSecret);
            } else {
                // If no webhook secret, just parse the event
                $event = json_decode($payload, true);
            }

            // Extract relevant information
            $eventType = $event['type'];
            $eventId = $event['id'];

            $sessionId = '';
            $paymentStatus = 'unknown';
            $metadata = [];

            if ($eventType === 'checkout.session.completed') {
                $session = $event['data']['object'];
                $sessionId = $session['id'];
                $paymentStatus = $session['payment_status'] === 'paid' ? 'paid' : 'unpaid';
                $metadata = $session['metadata'] ?? [];
            }

            return [
                'success' => true,
                'event_type' => $eventType,
                'event_id' => $eventId,
                'session_id' => $sessionId,
                'payment_status' => $paymentStatus,
                'metadata' => $metadata
            ];

        } catch (\Exception $e) {
            Log::error('Stripe webhook processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}