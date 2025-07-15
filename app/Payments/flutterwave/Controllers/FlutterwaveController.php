<?php

namespace App\Payments\flutterwave\controllers;

use Route;
use App\Payments;
use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Models\CheckoutGo;
use App\Models\PaymentsSpv;
use Illuminate\Http\Request;
use App\Flutterwave\Flutterwave;
use Illuminate\Support\Facades\Http;

class FlutterwaveController{
    function __construct(){

    }

    public function request($checkout){
        $price = number_format($checkout->price, 0, '.', '');
        $callback = route('yena-payments-flutterwave-verify', ['sxref' => $checkout->uref]);
        $headers = [
            'Content-Type' => 'application/json',
            'authorization' => "Bearer " . ao($checkout->keys, 'secret'),
        ];


        switch($checkout->payment_type) {
            case 'onetime':
                $payment_id = md5($checkout->uref . $checkout->payment_type . $checkout->frequency . $checkout->email . Carbon::now());

                $body = \Unirest\Request\Body::json([
                    'tx_ref' => $payment_id,
                    'amount' => $price,
                    'currency' => $checkout->currency,
                    'redirect_url' => $callback,
                    'meta' => [
                        'uref' => $checkout->uref,
                    ],
                    'customer' => [
                        'email' => $checkout->email,
                    ],
                ]);
                $response = \Unirest\Request::post('https://api.flutterwave.com/v3/payments', $headers, $body);

                /* Check against errors */
                if($response->code >= 400) {
                    return ['redirect' => '/', 'status' => 0, 'response' => $response->body->message];
                }

                return ['redirect' => $response->body->data->link, 'status' => 1, 'response' => 'success'];
            break;

            case 'recurring':
                /* Get all available payment plans */
                $response = \Unirest\Request::get('https://api.flutterwave.com/v3/payment-plans', $headers);

                /* Check against errors */
                if($response->code >= 400) {
                    return ['redirect' => '/', 'status' => 0, 'response' => $response->body->message];
                }

                $payment_plan = null;

                /* Go through each plan and try to find the one */
                foreach($response->body->data as $plan) {
                    if($plan->amount == $price && $plan->interval == ($checkout->frequency == 'monthly' ? 'monthly' : 'yearly')) {
                        $payment_plan = $plan;
                    }
                }

                if(!$payment_plan) {
                    /* Generate a new payment plan */
                    $response = \Unirest\Request::post('https://api.flutterwave.com/v3/payment-plans', $headers,
                        \Unirest\Request\Body::json([
                            'name' => ao($checkout->meta, 'payment_mode.title'),
                            'amount' => $price,
                            'currency' => $checkout->currency,
                            'interval' => $checkout->frequency == 'monthly' ? 'monthly' : 'yearly',
                        ])
                    );

                    /* Check against errors */
                    if($response->code >= 400) {
                        return ['redirect' => '/', 'status' => 0, 'response' => $response->body->message];
                    }

                    $payment_plan = $response->body->data;
                }

                $payment_id = md5($checkout->uref . $checkout->payment_type . $checkout->frequency . $checkout->email . Carbon::now());

                $response = \Unirest\Request::post(
                    'https://api.flutterwave.com/v3/payments', $headers,\Unirest\Request\Body::json([
                        'payment_plan' => $payment_plan->id,
                        'tx_ref' => $payment_id,
                        'amount' => $price,
                        'currency' => $checkout->currency,
                        'redirect_url' => $callback,
                        'meta' => [
                            'uref' => $checkout->uref,
                        ],
                        'customer' => [
                            'email' => $checkout->email,
                        ],
                    ])
                );

                /* Check against errors */
                if($response->code >= 400) {
                    return ['redirect' => '/', 'status' => 0, 'response' => $response->body->message];
                }

                /* Redirect to payment */
                return ['redirect' => $response->body->data->link, 'status' => 1, 'response' => 'success'];

            break;

        }
    }

    public function webhook(Request $request){
        $payload = @file_get_contents('php://input');

        $data = json_decode($payload, true);

        if(!$data) {
            die('0');
        }

        if(!isset($data['status']) || !isset($data['id'])) {
            die('1');
        }

        if($data['status'] != 'successful') {
            die('2');
        }
        /* Get transaction data */
        $response = \Unirest\Request::get(
            'https://api.flutterwave.com/v3/transactions/' . $data['id'] . '/verify', [
                'Authorization' => 'Bearer ' . settings('payment_flutterwave.secret'),
                'Content-Type' => 'application/json',
            ],
        );

        /* Check against errors */
        if($response->code >= 400) {
            http_response_code(400); die($response->body->message);
        }

        $payment = $response->body->data;

        if($response->body->status != 'success' || $payment->status != 'successful') {
            http_response_code(400); die('payment not successful');
        }
        /* Get payment data */
        $payment_subscription_id = null;

        /* Check if it's a subscription */
        if(isset($data['paymentPlan']) && !is_null($data['paymentPlan'])) {
            /* Get subscription data */
            $response = \Unirest\Request::get(
                'https://api.flutterwave.com/v3/subscriptions?transaction_id=' . $payment->id, [
                    'Authorization' => 'Bearer ' . settings('payment_flutterwave.secret'),
                    'Content-Type' => 'application/json',
                ],
            );

            /* Check against errors */
            if($response->code >= 400) {
                http_response_code(400); die($response->body->message);
            }

            if(isset($response->body->data[0]) && $response->body->data[0]->status != 'cancelled') {
                $payment_subscription_id = $response->body->data[0]->id;
            }
        }

        /* Process meta data */
        $metadata = $payment->meta;
        if(!$checkout = CheckoutGo::where('uref', $metadata->uref)->first()) die();

        $function = unserialize($checkout->call_function);
        $function($checkout, $checkout->uref);

        $checkout->payment_subscription_id = $payment_subscription_id;
        $checkout->save();
        $checkout->setPaid();

        echo 'successful';
    }

    public function cancel($checkout){
        if(!$checkout->payment_subscription_id) return;
        $response = \Unirest\Request::put(
            'https://api.flutterwave.com/v3/subscriptions/' . $checkout->payment_subscription_id . '/cancel', [
                'Authorization' => 'Bearer ' . settings('payment_flutterwave.secret'),
                'Content-Type' => 'application/json',
            ],
        );
    }

    public function verify(Request $request){
        // Get refrence from url
        if(!$checkout = CheckoutGo::where('uref', $request->get('sxref'))->first()) abort(404);

        // flutterwave secret key 
        $key = settings('payment_flutterwave.secret');
        try {
            $transactionID = $request->get('transaction_id');
            $data =  Http::withToken($key)->get("https://api.flutterwave.com/v3/transactions/" . $transactionID . '/verify')->json();
        } catch (\Exception $e) {
            return redirect('/')->with('error',  __('API returned error - :message', ['message' => $e->getMessage()]));
        }

        if (ao($data, 'data.status') == 'successful') {
            $callback = url_query($checkout->callback, ['sxref' => $checkout->uref]);
            return redirect($callback);
        }


        return redirect('/')->with('error',  __('Could not complete your payment.'));
    }
}