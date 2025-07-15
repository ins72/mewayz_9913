<?php

namespace App\Payments\paystack\Controllers;

use GuzzleHttp\Client;
use App\Models\CheckoutGo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class PaystackController{
    
    public $api_url = 'https://api.paystack.co';

    public function request($checkout){
        $price = number_format($checkout->price, 2, '.', '');
        $callback = route('yena-payments-paystack-verify', ['sxref' => $checkout->uref]);
        $headers = [
            'Content-Type' => 'application/json',
            'cache-control' => 'no-cache',
            'authorization' => "Bearer " . ao($checkout->keys, 'secret'),
        ];
    
        switch($checkout->payment_type) {
            case 'onetime':
                $body = \Unirest\Request\Body::json([
                    'key' => ao($checkout->keys, 'public'),
                    'email' => $checkout->email,
                    'amount' => (int) ($price * 100),
                    'currency' => $checkout->currency,
                    'metadata' => [
                        'uref' => $checkout->uref,
                    ],
                    'callback_url' => $callback,
                ]);
                $response = \Unirest\Request::post("$this->api_url/transaction/initialize", $headers, $body);

                if(!$response->body->status) {
                    return ['redirect' => '/', 'status' => 0, 'response' => $response->body->message];
                }
                
                return ['redirect' => $response->body->data->authorization_url, 'status' => 1, 'response' => 'success'];
            break;

            case 'recurring':
                $planBody = \Unirest\Request\Body::json([
                    'name' => ao($checkout->meta, 'payment_mode.title'),
                    'interval' => $checkout->frequency == 'monthly' ? 'monthly' : 'annually',
                    'amount' => (int) ($price * 100),
                    'currency' => $checkout->currency,
                ]);
                $response = \Unirest\Request::post("$this->api_url/plan", $headers, $planBody);

                if(!$response->body->status) {
                    return ['redirect' => '/', 'status' => 0, 'response' => $response->body->message];
                }

                $paystack_plan_code = $response->body->data->plan_code;

                /* Generate the payment link */
                $body = \Unirest\Request\Body::json([
                    'key' => ao($checkout->keys, 'public'),
                    'email' => $checkout->email,
                    'amount' => (int) ($price * 100),
                    'currency' => $checkout->currency,
                    'metadata' => [
                        'uref' => $checkout->uref,
                    ],
                    'callback_url' => $callback,
                    'plan' => $paystack_plan_code
                ]);
                $response = \Unirest\Request::post("$this->api_url/transaction/initialize", $headers, $body);
                if(!$response->body->status) {
                    return ['redirect' => '/', 'status' => 0, 'response' => $response->body->message];
                }

                /* Redirect to payment */
                return ['redirect' => $response->body->data->authorization_url, 'status' => 1, 'response' => 'success'];
            break;
        }
        
        // Return empty response
        return ['redirect' => '/', 'status' => 0, 'response' => __('No response got')];
    }

    public function webhook(Request $request){
        // $logFile = 'webhook_log.txt';
        // file_put_contents(base_path($logFile), date('Y-m-d H:i:s') . " - dtsrt???" . "\n", FILE_APPEND);
        if(!isset($_SERVER['HTTP_X_PAYSTACK_SIGNATURE'])) {
            die();
        }

        $payload = @file_get_contents('php://input');
        $headers = [
            'Content-Type' => 'application/json',
            'cache-control' => 'no-cache',
            'authorization' => "Bearer " . settings('payment_paystack.secret'),
        ];
        
        if($_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] !== hash_hmac('sha512', $payload, settings('payment_paystack.secret'))) {
            die();
        }
        $data = json_decode($payload);

        if(!$data) {
            die();
        }

        if($data->event == 'charge.success') {
            $payment_subscription_id = null;

            /* Get subscription details if needed */
            if(isset($data->data->plan->id)) {
                $id = $data->data->plan->id;
                $response = \Unirest\Request::get("$this->api_url/plan/$id", $headers);

                if(!$response->body->status) {
                    http_response_code(400); die();
                }

                $payment_subscription_id = $response->body->data->subscriptions[0]->subscription_code . '###' . $response->body->data->subscriptions[0]->email_token;
            }
            
            /* Process meta data */
            $metadata = $data->data->metadata;
            if(!$checkout = CheckoutGo::where('uref', $metadata->uref)->first()) die();

            $function = unserialize($checkout->call_function);
            $function($checkout, $checkout->uref);

            $checkout->payment_subscription_id = $payment_subscription_id;
            $checkout->save();
            $checkout->setPaid();

            die('successful');
        }
    }

    public function cancel($checkout){
        $headers = [
            'Content-Type' => 'application/json',
            'cache-control' => 'no-cache',
            'authorization' => "Bearer " . settings('payment_paystack.secret'),
        ];

        $payment_subscription_id = explode('###', $checkout->payment_subscription_id);
        $code = $payment_subscription_id[0];
        $token = $payment_subscription_id[1];

        $response = \Unirest\Request::post($this->api_url . '/subscription/disable', $headers, \Unirest\Request\Body::json([
            'code' => $code,
            'token' => $token,
        ]));

        // if(!$response->body->status) {
        //     throw new \Exception($response->body->message);
        // }
    }

    public function verify(Request $request){
        $client = new Client(['http_errors' => false]);

        //
        if(!$checkout = CheckoutGo::where('uref', $request->get('sxref'))->first()) abort(404);
        
        // Get refrence from url
        $reference = $request->get('reference');

        // Paystack headers
        $headers = [
            'Content-Type' => 'application/json',
            'cache-control' => 'no-cache',
            'authorization' => "Bearer "  . ao($checkout->keys, 'secret'),
        ];

        // Paystack verify
        try {
            $result = $client->request('GET', 'https://api.paystack.co/transaction/verify/' . rawurlencode($reference), ['headers' => $headers]);

            $tranx = json_decode($result->getBody()->getContents());
            if(!$tranx->status){
              return redirect('/')->with('error',  __('API returned error - :message', ['message' => $tranx->message]));
            }
            $callback = url_query($checkout->callback, ['sxref' => $checkout->uref]);
            return redirect($callback);
        } catch (\Exception $e) {
            
        }
    }
}