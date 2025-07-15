<?php

namespace App\Payments\paypal\controllers;

use App\Models\CheckoutGo;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\PaymentsSpv;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use App\Payments;
use Route;

class PayPalController{

    public $sandbox_api_url = 'https://api-m.sandbox.paypal.com/';
    public $live_api_url = 'https://api-m.paypal.com/';
    public $access_token = null;

    public function get_api_url() {
        return settings('payment_paypal.mode') == 'live' ? $this->live_api_url : $this->sandbox_api_url;
    }

    public function get_access_token() {
        if($this->access_token) return $this->access_token;

        /* Generate PayPal access token */
        \Unirest\Request::auth(settings('payment_paypal.client'), settings('payment_paypal.secret'));

        $response = \Unirest\Request::post($this->get_api_url() . 'v1/oauth2/token', [], \Unirest\Request\Body::form(['grant_type' => 'client_credentials']));

        /* Check against errors */
        if($response->code >= 400) {
            throw new \Exception($response->body->error . ':' . $response->body->error_description);
        }

        return $this->access_token = $response->body->access_token;
    }

    public function get_headers() {
        return [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->get_access_token()
        ];
    }

    public function request($checkout){
        $callback = route('yena-payments-paypal-verify', ['sxref' => $checkout->uref]);
        $cancel_url = url('/');
        $price = in_array($checkout->currency, ['JPY', 'TWD', 'HUF']) ? number_format($checkout->price, 0, '.', '') : number_format($checkout->price, 2, '.', '');

        try {
            $paypal_api_url = $this->get_api_url();
            $headers = $this->get_headers();
        } catch (\Exception $exception) {
            return ['redirect' => $cancel_url, 'status' => 0, 'response' => $exception->getMessage()];
        }

        $custom_id = $checkout->uref . '&' . $checkout->payment_type . '&' . $checkout->frequency . '&' . $price . '&' . $checkout->email;


        switch($checkout->payment_type) {
            case 'onetime':
                /* Create an order */
                $response = \Unirest\Request::post($paypal_api_url . 'v2/checkout/orders', $headers, \Unirest\Request\Body::json([
                    'intent' => 'CAPTURE',
                    'purchase_units' => [[
                        'reference_id' => $checkout->uref,
                        'amount' => [
                            'currency_code' => $checkout->currency,
                            'value' => $price,
                            'breakdown' => [
                                'item_total' => [
                                    'currency_code' => $checkout->currency,
                                    'value' => $price
                                ]
                            ]
                        ],
                        'description' => ao($checkout->meta, 'payment_mode.title'),
                        'custom_id' => $custom_id,
                        'items' => [[
                            'name' => config('app.name') .'_'. ao($checkout->meta, 'payment_mode.name'),
                            'description' => ao($checkout->meta, 'payment_mode.title'),
                            'quantity' => 1,
                            'unit_amount' => [
                                'currency_code' => $checkout->currency,
                                'value' => $price
                            ]
                        ]]
                    ]],
                    'application_context' => [
                        'brand_name' => config('app.name'),
                        'landing_page' => 'NO_PREFERENCE',
                        'shipping_preference' => 'NO_SHIPPING',
                        'user_action' => 'PAY_NOW',
                        'return_url' => $callback,
                        'cancel_url' => $cancel_url,
                    ]
                ]));

                /* Check against errors */
                if($response->code >= 400) {
                    return ['redirect' => $cancel_url, 'status' => 0, 'response' => $response->body->message];
                }

                $paypal_payment_url = $response->body->links[1]->href;

                return ['redirect' => $paypal_payment_url, 'status' => 1, 'response' => 'success'];

            break;

            case 'recurring':

                /* Generate the plan id with the proper parameters */
                $paypal_plan_id = ao($checkout->meta, 'payment_mode.name') . '_' . $checkout->payment_type . '_' . $price . '_' . $checkout->currency;

                /* Product */
                $response = \Unirest\Request::get($paypal_api_url . 'v1/catalogs/products/' . $paypal_plan_id, $headers);

                /* Check against errors */
                if($response->code == 404) {
                    /* Create the product if not existing */
                    $response = \Unirest\Request::post($paypal_api_url . 'v1/catalogs/products', $headers, \Unirest\Request\Body::json([
                        'id' => $paypal_plan_id,
                        'name' => config('app.name') . ' - ' . ao($checkout->meta, 'payment_mode.name'),
                        'type' => 'DIGITAL',
                    ]));

                    /* Check against errors */
                    if($response->code >= 400) {
                        return ['redirect' => $cancel_url, 'status' => 0, 'response' => $response->body->name . ':' . $response->body->message];
                    }
                }


                /* Create a new plan */
                $response = \Unirest\Request::post($paypal_api_url . 'v1/billing/plans', $headers, \Unirest\Request\Body::json([
                    'product_id' => $paypal_plan_id,
                    'name' => config('app.name') . ' - ' . ao($checkout->meta, 'payment_mode.name') . ' - ' . $checkout->frequency,
                    'description' => $checkout->frequency,
                    'status' => 'ACTIVE',
                    'billing_cycles' => [[
                        'pricing_scheme' => [
                            'fixed_price' => [
                                'currency_code' => $checkout->currency,
                                'value' => $price
                            ]
                        ],
                        'frequency' => [
                            'interval_unit' => 'DAY',
                            'interval_count' => $checkout->frequency == 'monthly' ? 30 : 365
                        ],
                        'tenure_type' => 'REGULAR',
                        'sequence' => 1,
                        'total_cycles' => $checkout->frequency == 'monthly' ? 60 : 5,
                    ]],
                    'payment_preferences' => [
                        'auto_bill_outstanding' => true,
                        'setup_fee' => [
                            'currency_code' => $checkout->currency,
                            'value' => $price
                        ],
                        'setup_fee_failure_action' => 'CANCEL',
                        'payment_failure_threshold' => 0
                    ]
                ]));

                /* Check against errors */
                if($response->code >= 400) {
                    return ['redirect' => $cancel_url, 'status' => 0, 'response' => $response->body->name . ':' . $response->body->message];
                }

                /* Create a new subscription */
                $response = \Unirest\Request::post($paypal_api_url . 'v1/billing/subscriptions', $headers, \Unirest\Request\Body::json([
                    'plan_id' => $response->body->id,
                    'start_time' => (new \DateTime())->modify($checkout->frequency == 'monthly' ? '+30 days' : '+1 year')->format(DATE_ISO8601),
                    'quantity' => 1,
                    'custom_id' => $custom_id,
                    'payment_method' => [
                        'payer_selected' => 'PAYPAL',
                        'payee_preferred' => 'IMMEDIATE_PAYMENT_REQUIRED'
                    ],
                    'application_context' => [
                        'brand_name' => config('app.name'),
                        'shipping_preference' => 'NO_SHIPPING',
                        'user_action' => 'SUBSCRIBE_NOW',
                        'return_url' => $callback,
                        'cancel_url' => $cancel_url,
                    ]
                ]));

                /* Check against errors */
                if($response->code >= 400) {
                    return ['redirect' => $cancel_url, 'status' => 0, 'response' => $response->body->name . ':' . $response->body->message];
                }

                $paypal_payment_url = $response->body->links[0]->href;

                return ['redirect' => $paypal_payment_url, 'status' => 1, 'response' => 'success'];

            break;
        }
        return;
    }

    public function webhook(Request $request){
        
        $payload = @file_get_contents('php://input');
        $data = json_decode($payload);

        if($payload && $data) {
            try {
                $paypal_api_url = $this->get_api_url();
                $headers = $this->get_headers();
            } catch (\Exception $exception) {
                echo $exception->getMessage();
                http_response_code(400); die();
            }

            /* Approve one time payment order and process it */
            if($data->event_type == 'CHECKOUT.ORDER.APPROVED') {
                $response = \Unirest\Request::post($paypal_api_url . 'v2/checkout/orders/' . $data->resource->id . '/capture', $headers);

                /* Check against errors */
                if($response->code >= 400) {
                    echo $response->body->name . ':' . $response->body->message;
                    http_response_code(400); die();
                }

                /* Start getting the payment details */
                $payment_subscription_id = null;

                /* Parse metadata */
                $metadata = explode('&', $response->body->purchase_units[0]->payments->captures[0]->custom_id);

                if(!$checkout = CheckoutGo::where('uref', $metadata[0])->first()) die();
        
                $function = unserialize($checkout->call_function);
                $function($checkout, $checkout->uref);
        
                $checkout->payment_subscription_id = $payment_subscription_id;
                $checkout->save();
                $checkout->setPaid();

                die('successful');
            }

            /* Handle received payments by subscriptions */
            if($data->event_type == 'PAYMENT.SALE.COMPLETED') {
                $response = \Unirest\Request::get($paypal_api_url . 'v1/billing/subscriptions/' . $data->resource->billing_agreement_id . '?fields=plan', $headers);

                /* Check against errors */
                if($response->code >= 400) {
                    echo $response->body->name . ':' . $response->body->message;
                    http_response_code(400); die();
                }

                /* Start getting the payment details */
                $payment_subscription_id = $data->resource->billing_agreement_id;

                $metadata = explode('&', $response->body->custom_id);
                if(!$checkout = CheckoutGo::where('uref', $metadata[0])->first()) die();
        
                $function = unserialize($checkout->call_function);
                $function($checkout, $checkout->uref);
        
                $checkout->payment_subscription_id = $payment_subscription_id;
                $checkout->save();
                $checkout->setPaid();
                die('successful');
            }

        }

        die('');
    }

    public function cancel($checkout){
        if(!$checkout->payment_subscription_id) return;
        $paypal_api_url = $this->get_api_url();
        $headers = $this->get_headers();

        $response = \Unirest\Request::post($paypal_api_url . 'v1/billing/subscriptions/' . $checkout->payment_subscription_id . '/cancel', $headers, \Unirest\Request\Body::json([
            'reason' => __('Cant continue the subscription')
        ]));
    }

    public function verify(Request $request){
        if(!$checkout = CheckoutGo::where('uref', $request->get('sxref'))->first()) abort(404);

        $callback = url_query($checkout->callback, ['sxref' => $checkout->uref]);
        return redirect($callback);
    }
}