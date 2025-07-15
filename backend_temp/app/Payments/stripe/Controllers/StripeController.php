<?php

namespace App\Payments\stripe\Controllers;

use App\Models\CheckoutGo;
use App\Payments;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Subscription;
use App\Sandy\RunWebhook;
use App\Models\PaymentsSpv;
use App\Models\SandyWebhook;
use Illuminate\Http\Request;

class StripeController{
    public function request($checkout){
        $callback = route('yena-payments-stripe-verify', ['sxref' => $checkout->uref]);
        $cancel_url = url('/');


        
        /* Initiate Stripe */
        \Stripe\Stripe::setApiKey(ao($checkout->keys, 'secret'));
        \Stripe\Stripe::setApiVersion('2023-10-16');
        /* Final price */
        $stripe_formatted_price = in_array($checkout->currency, ['MGA', 'BIF', 'CLP', 'PYG', 'DJF', 'RWF', 'GNF', 'UGX', 'JPY', 'VND', 'VUV', 'XAF', 'KMF', 'KRW', 'XOF', 'XPF']) ? number_format($checkout->price, 0, '.', '') : number_format($checkout->price, 2, '.', '') * 100;

        switch($checkout->payment_type) {
            case 'onetime':

                try {
                    $stripe_session = \Stripe\Checkout\Session::create([
                        'mode' => 'payment',
                        'customer_email' => $checkout->email,
                        'currency' => $checkout->currency,

                        'line_items' => [
                            [
                                'price_data' => [
                                    'currency' => $checkout->currency,
                                    'product_data' => [
                                        'name' => config('app.name') .' - '. ao($checkout->meta, 'payment_mode.name'),
                                        'description' => ao($checkout->meta, 'payment_mode.title'),
                                    ],
                                    'unit_amount' => $stripe_formatted_price,
                                ],
                                'quantity' => 1
                            ],
                        ],

                        'metadata' => [
                            'uref' => $checkout->uref,
                        ],

                        'success_url' => $callback,
                        'cancel_url' => $cancel_url,
                    ]);
                } catch (\Exception $exception) {
                    return ['redirect' => $cancel_url, 'status' => 0, 'response' => $exception->getMessage()];
                }

            break;

            case 'recurring':

                try {
                    $stripe_session = \Stripe\Checkout\Session::create([
                        'mode' => 'subscription',
                        'customer_email' => $checkout->email,
                        'currency' => $checkout->currency,

                        'line_items' => [
                            [
                                'price_data' => [
                                    'currency' => $checkout->currency,
                                    'product_data' => [
                                        'name' => config('app.name') .' - '. ao($checkout->meta, 'payment_mode.name'),
                                        'description' => ao($checkout->meta, 'payment_mode.title'),
                                    ],
                                    'unit_amount' => $stripe_formatted_price,
                                    'recurring' => [
                                        'interval' => 'day',
                                        'interval_count' => $checkout->frequency == 'monthly' ? 30 : 365,
                                    ]
                                ],
                                'quantity' => 1
                            ],
                        ],

                        'metadata' => [
                            'uref' => $checkout->uref,
                        ],

                        'subscription_data' => [
                            'metadata' => [
                                'uref' => $checkout->uref,
                            ],
                        ],

                        'success_url' => $callback,
                        'cancel_url' => $cancel_url,
                    ]);
                } catch (\Exception $exception) {
                    return ['redirect' => $cancel_url, 'status' => 0, 'response' => $exception->getMessage()];
                }

                break;
        }

        $spv_keys = $checkout->keys;
        $spv_keys['checkout_id'] = $stripe_session->id;
        $checkout->keys = $spv_keys;
        $checkout->update();

        return ['redirect' => $stripe_session->url, 'status' => 1, 'response' => 'success'];
    }

    public function verify(Request $request){
        if(!$checkout = CheckoutGo::where('uref', $request->get('sxref'))->first()) abort(404);
        // Get refrence from url
        $reference = ao($checkout->keys, 'checkout_id');

        // Get stripe payment
        try {
            Stripe::setApiKey(ao($checkout->keys, 'secret'));
            $stripe = \Stripe\Checkout\Session::retrieve($reference, []);


            if ($stripe->payment_status !== 'paid') {
                $error = 'Payment status is unpaid.';

                if (!empty($error)) {
                    $error = 'Payment status is unpaid.';
                }

                //Payments::failed($spv->id, $reference, $stripe);

                return redirect('/')->with('error',  $error);
            }


            if ($stripe->payment_status == 'paid') {
                $callback = url_query($checkout->callback, ['sxref' => $checkout->uref]);
                return redirect($callback);
            }
        } catch (\Exception $e) {
            return redirect('/')->with('error',  __('API returned error - :message', ['message' => $e->getMessage()]));
        }
    }

    public function cancel($checkout){
        if(!$checkout->payment_subscription_id) return;
        
        \Stripe\Stripe::setApiKey(settings('payment_stripe.secret'));
        \Stripe\Stripe::setApiVersion('2023-10-16');

        /* Cancel the Stripe Subscription */
        $subscription = \Stripe\Subscription::retrieve($checkout->payment_subscription_id);
        $subscription->cancel();
    }
    
    public function webhook(Request $request){
        /* Initiate Stripe */
        \Stripe\Stripe::setApiKey(settings('payment_stripe.secret'));
        \Stripe\Stripe::setApiVersion('2023-10-16');

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sig_header, settings('payment_stripe.webhook_secret'));
        } catch(\Exception $exception) {
            /* Invalid payload */
            echo $exception->getMessage(); http_response_code(400); die();
        }

        if(!in_array($event->type, ['invoice.paid', 'checkout.session.completed'])) {
            die('Event type not needed to be handled, returning ok.');
        }

        $session = $event->data->object;

        switch($event->type) {
            /* Handling recurring payments */
            case 'invoice.paid':
                /* Process meta data */
                $metadata = $session->lines->data[0]->metadata;
                /* Vars */
                $payment_type = $session->subscription ? 'recurring' : 'one_time';
                $payment_subscription_id = $payment_type == 'recurring' ? $session->subscription : '';

            break;

            /* Handling one time payments */
            case 'checkout.session.completed':
                /* Exit when the webhook comes for recurring payments as the invoice.paid event will handle it */
                if($session->subscription) {
                    die();
                }

                /* Process meta data */
                $metadata = $session->metadata;
                /* Vars */
                $payment_type = $session->subscription ? 'recurring' : 'onetime';
                $payment_subscription_id =  $payment_type == 'recurring' ? $session->subscription : '';
            break;
        }

        if(!$checkout = CheckoutGo::where('uref', $metadata->uref)->first()) die();

        $function = unserialize($checkout->call_function);
        $function($checkout, $checkout->uref);

        $checkout->payment_subscription_id = $payment_subscription_id;
        $checkout->save();
        $checkout->setPaid();

        echo 'successful';
    }
}