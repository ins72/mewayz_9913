<?php

namespace App\Payments\razor\controllers;

use App\Models\CheckoutGo;
use Illuminate\Http\Request;
use Razorpay\Api\Api;

class RazorController{
    public function request($checkout){
        $callback = route('yena-payments-razor-verify', ['sxref' => $checkout->uref]);
        $razorpay = new Api(ao($checkout->keys, 'client'), ao($checkout->keys, 'secret'));
        $price = number_format($checkout->price, 2, '.', '');

        switch($checkout->payment_type) {
            case 'onetime':

                /* Generate the payment link */
                try {
                    $response = $razorpay->paymentLink->create([
                        'amount' => $price * 100,
                        'currency' => $checkout->currency,
                        'accept_partial' => false,
                        'description' => ao($checkout->meta, 'payment_mode.title'),
                        'customer' => [
                            'email' => $checkout->email,
                        ],
                        'notify' => [
                            'sms' => false,
                            'email' => false,
                        ],
                        'reminder_enable' => false,
                        'notes' => [
                            'uref' => $checkout->uref,
                        ],
                        'callback_url' => $callback,
                        'callback_method' => 'get'
                    ]);
                } catch (\Exception $exception) {
                    return ['redirect' => '/', 'status' => 0, 'response' => $exception->getMessage()];
                }

                return ['redirect' => $response['short_url'], 'status' => 1, 'response' => 'success'];

            break;

            case 'recurring':

                try {
                    $plan = $razorpay->plan->create([
                        'period' => 'daily',
                        'interval' => $checkout->frequency == 'monthly' ? 30 : 365,
                        'item' => [
                            'name' => ao($checkout->meta, 'payment_mode.name'),
                            'description' => ao($checkout->meta, 'payment_mode.title'),
                            'amount' => $price * 100,
                            'currency' => $checkout->currency,
                        ],
                    ]);
                }  catch (\Exception $exception) {
                    return ['redirect' => '/', 'status' => 0, 'response' => $exception->getMessage()];
                }

                /* Generate the payment link */
                try {
                    $response = $razorpay->subscription->create([
                        'plan_id' => $plan['id'],
                        'total_count' => $checkout->frequency == 'monthly' ? 60 : 5,
                        'quantity' => 1,
                        'notes' => [
                            'uref' => $checkout->uref,
                        ]
                    ]);
                } catch (\Exception $exception) {
                    return ['redirect' => '/', 'status' => 0, 'response' => $exception->getMessage()];
                }
                return ['redirect' => $response['short_url'], 'status' => 1, 'response' => 'success'];

            break;
        }
    }
    public function webhook(Request $request){
        if((strtoupper($_SERVER['REQUEST_METHOD']) != 'POST') || !isset($_SERVER['HTTP_X_RAZORPAY_SIGNATURE'])) {
            die();
        }

        $payload = trim(@file_get_contents('php://input'));

        if($_SERVER['HTTP_X_RAZORPAY_SIGNATURE'] !== hash_hmac('sha256', $payload, settings('payment_razor.webhook_secret'))) {
            die();
        }

        $data = json_decode($payload);

        if(!$data) {
            die();
        }

        if($data->event == 'payment_link.paid') {
            $payment_subscription_id = null;
            $metadata = $data->payload->payment_link->entity->notes;
            if(!$checkout = CheckoutGo::where('uref', $metadata->uref)->first()) die();
    
            $function = unserialize($checkout->call_function);
            $function($checkout, $checkout->uref);
    
            $checkout->payment_subscription_id = $payment_subscription_id;
            $checkout->save();
            $checkout->setPaid();    

            die();
        }

        if($data->event == 'subscription.charged') {
            $payment_subscription_id = $data->payload->subscription->entity->id;
            $metadata = $data->payload->subscription->entity->notes;
            if(!$checkout = CheckoutGo::where('uref', $metadata->uref)->first()) die();
    
            $function = unserialize($checkout->call_function);
            $function($checkout, $checkout->uref);
    
            $checkout->payment_subscription_id = $payment_subscription_id;
            $checkout->save();
            $checkout->setPaid();
    
            die('successful');
        }

        die();
    }

    public function cancel($checkout){
        if(!$checkout->payment_subscription_id) return;
        $razorpay = new Api(settings('payment_razor.client'), settings('payment_razor.secret'));

        $response = $razorpay->subscription->fetch($checkout->payment_subscription_id)->cancel();
    }

    public function verify(Request $request){
        if(!$checkout = CheckoutGo::where('uref', $request->get('sxref'))->first()) abort(404);


        // Api Keys
        $client = ao($checkout->keys, 'client');
        $secret = ao($checkout->keys, 'secret');

        try {
            $api = new Api($client, $secret);
            $payment = $api->payment->fetch($request->get('razorpay_payment_id'));


            $json = [];
            foreach ($payment as $key => $value){
                $json[$key] = $value;
            }


            if (!$payment->captured) {
                return redirect('/')->with('error',  __('Payment is not successful.'));
            }
            
            $callback = url_query($checkout->callback, ['sxref' => $checkout->uref]);
            return redirect($callback);

        } catch (\Exception $e) {
            return redirect('/')->with('error',  $e->getMessage());
        }

        return redirect('/')->with('error',  __('Could not complete your payment.'));
    }
}