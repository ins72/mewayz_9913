<?php

namespace App\Payments\click\Controllers;

use App\Models\CheckoutGo;
use GuzzleHttp\Client;
use Stripe\Stripe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClickController{
    public function request($checkout){
        $callback = route('yena-payments-click-verify', ['sxref' => $checkout->uref]);
        $cancel_url = url('/');


        $merchant_id = settings('payment_click.merchant_id');
        $secret = settings('payment_click.secret');
        $service_id = settings('payment_click.service_id');
        $merchant_user_id = settings('payment_click.merchant_user_id');
        $price = number_format( $checkout->price, 0, '.', '' );
        
        $url = "https://my.click.uz/services/pay?service_id=$service_id&merchant_id=$merchant_id&amount=$price&transaction_param=$checkout->uref&return_url=$callback&merchant_user_id=$merchant_user_id";

        return ['redirect' => $url, 'status' => 1, 'response' => 'success'];
    }

    public function verify(Request $request){
        if(!$checkout = CheckoutGo::where('uref', $request->get('sxref'))->first()) abort(404);


        if($request->get('payment_status') == 2){
            $callback = url_query($checkout->callback, ['sxref' => $checkout->uref]);
            return redirect($callback);
        }
        $error = 'Payment status is unpaid.';

        return redirect('/')->with('error',  $error);
        // $client = new Client([
        //     'base_uri' => 'https://api.click.uz/v2/merchant/',
        // ]);
        // $secret = settings('payment_click.secret');
        // $merchant_user_id = settings('payment_click.merchant_user_id');

        // $timestamp = time();
        // $digest = sha1($timestamp . $secret);
        // $authHeader = "{$merchant_user_id}:{$digest}:{$timestamp}";

        // try {
        //     $response = $client->get('https://api.click.uz/v2/merchant/payment/status/34604/3375356023', [
        //         'headers' => [
        //             'Accept' => 'application/json',
        //             'Auth' => $authHeader,
        //             'Content-Type' => 'application/json',
        //         ],
        //     ]);

        //     return json_decode($response->getBody(), true);
        // } catch (\Exception $e) {
        //     dd($e->getMessage());
        //     return null;
        // }
        return;
    }

    
    public function prepare(Request $request)
    {
        $secret = settings('payment_click.secret');
        $validator = Validator::make($request->all(), [
            'click_trans_id' => 'required',
            'service_id' => 'required',
            'merchant_trans_id' => 'required',
            'amount' => 'required|numeric',
            'action' => 'required',
            'sign_time' => 'required',
            'sign_string' => 'required',
            'click_paydoc_id' => 'required',
            'error' => 'nullable',
            'error_note' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => '-8',
                'error_note' => 'Error in request from click'
            ]);
        }

        $signString = $request->click_trans_id .
                      $request->service_id . $secret .
                      $request->merchant_trans_id .
                      $request->amount .
                      $request->action .
                      $request->sign_time;

        $signString = md5($signString);

        if ($signString !== $request->sign_string) {
            return response()->json([
                'error' => '-1',
                'error_note' => 'Sign check error'
            ]);
        }

        $checkout = CheckoutGo::where('uref', $request->merchant_trans_id)->first();

        // $order = Order::find($request->merchant_trans_id);

        if (!$checkout) {
            return response()->json([
                'error' => '-5',
                'error_note' => 'User does not exist'
            ]);
        }

        if ($checkout->paid) {
            return response()->json([
                'error' => '-4',
                'error_note' => 'Already paid'
            ]);
        }

        if (abs($checkout->price - (float)$request->amount) > 0.01) {
            return response()->json([
                'error' => '-2',
                'error_note' => 'Incorrect parameter amount'
            ]);
        }

        try {
            $prepareId = $checkout->id;

            return response()->json([
                'click_trans_id' => $request->click_trans_id,
                'merchant_trans_id' => $request->merchant_trans_id,
                'merchant_prepare_id' => $prepareId,
                'error' => '0',
                'error_note' => 'Success'
            ]);

        } catch (\Exception $ex) {
            return response()->json([
                'error' => '-7',
                'error_note' => 'Failed to update user'
            ]);
        }
    }

    public function complete(Request $request)
    {
        $secret = settings('payment_click.secret');
        $requiredFields = [
            'click_trans_id',
            'service_id',
            'merchant_trans_id',
            'merchant_prepare_id',
            'amount',
            'action',
            'sign_time'
        ];
    
        foreach ($requiredFields as $field) {
            if (!$request->has($field)) {
                return response()->json([
                    'error' => '-8',
                    'error_note' => 'Error in request from click'
                ]);
            }
        }
    
        $signString = $request->input('click_trans_id') .
                      $request->input('service_id') . $secret .
                      $request->input('merchant_trans_id') .
                      $request->input('merchant_prepare_id') .
                      $request->input('amount') .
                      $request->input('action') .
                      $request->input('sign_time');
    
        $signString = md5($signString);
    
        if ($signString !== $request->input('sign_string')) {
            return response()->json([
                'error' => '-1',
                'error_note' => 'Sign check error'
            ]);
        }
    
        $checkout = CheckoutGo::where('uref', $request->merchant_trans_id)->first();
        $keys = $checkout->keys;
    
        if (!$checkout) {
            return response()->json([
                'error' => '-5',
                'error_note' => 'User does not exist'
            ]);
        }

        if (abs($checkout->price - (float)$request->input('amount')) > 0.01) {
            return response()->json([
                'error' => '-2',
                'error_note' => 'Incorrect parameter amount'
            ]);
        }
    
        if (ao($keys, 'status') == 'failed') {
            return response()->json([
                'error' => '-9',
                'error_note' => 'Transaction cancelled'
            ]);
        }
    
        if ($checkout->paid) {
            return response()->json([
                'error' => '-4',
                'error_note' => 'Already paid'
            ]);
        }
    
        if ($request->input('error') < 0) {

            $keys['status'] = 'failed';


            $checkout->keys = $keys;
            $checkout->save();
    
            return response()->json([
                'click_trans_id'      => $request->input('click_trans_id'),
                'merchant_trans_id'   => $request->input('merchant_trans_id'),
                'merchant_confirm_id' => $request->input('merchant_prepare_id'),
                'error'               => '-9',
                'error_note'          => 'Transaction cancelled'
            ]);
        }
    
        try {

            $keys['status'] = 'paid';


            $checkout->keys = $keys;
            $checkout->save();

            $function = unserialize($checkout->call_function);
            $function($checkout, $checkout->uref);

            // Run stuff
            $checkout->setPaid();
    
            return response()->json([
                'click_trans_id'      => $request->input('click_trans_id'),
                'merchant_trans_id'   => $request->input('merchant_trans_id'),
                'merchant_confirm_id' => $request->input('merchant_prepare_id'),
                'error'               => '0',
                'error_note'          => 'Success'
            ]);
    
        } catch (\Exception $ex) {
            // Log::error($ex);
    
            return response()->json([
                'error'      => '-7',
                'error_note' => 'Failed to update user'
            ]);
        }
    }

    public function cancel($checkout){
        if(!$checkout->payment_subscription_id) return;
    }
}