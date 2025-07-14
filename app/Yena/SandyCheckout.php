<?php

namespace App\Yena;
use Route;
use App\Models\CheckoutGo;
use App\Models\PaymentsSpv;
use App\Models\PaymentsSpvHistory;
use Laravel\SerializableClosure\SerializableClosure as SerializableClosureSerializableClosure;
use Opis\Closure\SerializableClosure;

class SandyCheckout {

    private $dir;

    public static function bookUser($user, $data){
        $call_function = function($checkout, $payment_subscription_id = null) use($user,$data){
            $sandybook = new \App\Yena\YenaBook($user->id);

            $sandybook
            ->setServices(ao($data, 'services'))
            ->setTime(ao($data, 'time'))
            ->setDate(ao($data, 'date'))
            ->setCustomer(ao($data, 'customer'))
            ->save([], false, 1);

            \App\Yena\SandyAudience::create_audience($user->id, ao($data, 'customer'));
            // Send Email?
        };

        return serialize(new SerializableClosure($call_function));
    }

    public static function tipUser($amount, $user, $data, $auth, $site_id){
        $call_function = function($checkout, $payment_subscription_id = null) use($amount, $user,$data, $auth, $site_id){
            $duration_time = 0;
            $duration = ao($data, 'duration');
            if($duration == 'recurring'){
                $duration_time = 30;
            }

            $recurring_id = null;
            if($checkout->payment_type == 'recurring'){
                if(!$_recurring = \App\Models\UserDonationsRecurring::where('last_subscription_uref', $checkout->uref)->first()){
                    $_recurring = new \App\Models\UserDonationsRecurring;
                    $_recurring->user_id = $user->id;
                    $_recurring->last_subscription_uref = $checkout->uref;
                    $_recurring->is_active = 1;
                    $_recurring->save();
                }

                $recurring_id = $_recurring->id;
            }

            // $payment_subscription_ids = $user->payment_subscription_ids;
            // $plan = \App\Models\Plan::find($plan->id);

            // if($checkout->payment_type == 'recurring'){     
            //     $payment_subscription_ids[] = [
            //         'plan_id' => $plan->id,
            //         'subscription_id' => $payment_subscription_id
            //     ];
            //     $user->payment_subscription_ids = $payment_subscription_ids;
            // }
            // if($checkout->uref !== $user->last_subscription_uref){
            //     $user->cancelCurrentSubscription();
            // }

            $payee_user_id = null;
            if ($payee = $auth) {
                $payee_user_id = $payee->id;
                \App\Yena\SandyAudience::create_audience($user->id, $payee_user_id);
            }
    
            $tip = new \App\Models\UserDonation;
            $tip->user_id = $user->id;
            $tip->bio_id = $site_id;
            $tip->payee_user_id = $payee_user_id;
            $tip->is_private = 1;
            $tip->amount = $checkout->price;
            $tip->currency = $checkout->currency;
            $tip->info = ao($checkout->meta, 'info');
            
            if($checkout->payment_type == 'recurring'){
                $tip->is_recurring = 1;
                $tip->recurring_id = $recurring_id;
            }


            $tip->save();
                
            $mail = new \App\Yena\YenaMail;
            $mail->send([
                'to' => ao($data, 'email'),
                'subject' => __('You just got tipped'),
            ], 'bio.tip', [
                'currency' => \App\Yena\Currency::symbol($checkout->currency),
                'amount' => $checkout->price,
                'description' => ao($checkout->meta, 'item.description'),
            ]);
            // Send email notification
            // $user->save();
        };

        return serialize(new SerializableClosure($call_function));
    }

    public static function buyTemplate($user, $template, $siteName, $createdBy){
        $fn = function($checkout) use($user, $template, $siteName, $createdBy){
            $name = $siteName;
            $randomSlug = str()->random(15);
            $addressSlug = str()->random(7);
            $slug_name = slugify($name, '-');
            $_slug = "$slug_name-$randomSlug";
            $address = "$slug_name-$addressSlug";


            $access = new \App\Models\YenaTemplateAccess;
            $access->user_id = $user->id;
            $access->template_id = $template->id;
            $access->site_id = $template->site_id;
            $access->save();

            // $template = \App\Models\YenaTemplate::find($template->id);
            $site = $template->site;

            $site = $site->duplicateSite();
            $site->user_id = $user->id;
            $site->created_by = $createdBy;
            $site->name = $name;
            $site->_slug = $_slug;
            $site->address = $address;
            $site->save();
        };
        
        return serialize(new SerializableClosure($fn));
    }

    public static function call_function($duration, $user, $plan){
        $call_function = function($checkout, $payment_subscription_id = null) use($duration, $user, $plan){
            $duration_time = 0;

            switch ($duration) {
                case 'year':
                    $duration_time = 365;
                break;
                case 'month':
                    $duration_time = 30;
                break;
            }

            $user = \App\Models\User::find($user->id);
            $payment_subscription_ids = $user->payment_subscription_ids;
            $plan = \App\Models\Plan::find($plan->id);

            if($checkout->payment_type == 'recurring'){     
                $payment_subscription_ids[] = [
                    'plan_id' => $plan->id,
                    'subscription_id' => $payment_subscription_id
                ];
                $user->payment_subscription_ids = $payment_subscription_ids;
            }
            if($checkout->uref !== $user->last_subscription_uref){
                $user->cancelCurrentSubscription();
            }

            $user->last_subscription_uref = $checkout->uref;
            $subscription = $user->upgradeCurrentPlanTo($plan, $duration_time, false, false);
            
            $paymentArray = [
                'user'          => $user->id,
                'name'          => $user->name,
                'plan'          => $plan->id,
                'plan_name'     => $plan->name,
                'email'         => $user->email,
                'ref'           => \Str::random(5),
                'currency'      => $checkout->currency,
                'duration'      => $duration,
                'price'         => $checkout->price,
                'gateway'       => $checkout->method,
                'created_at'    => \Carbon\Carbon::now()
            ];

            \App\Models\PlanPayment::insert($paymentArray);
            
            $plan_history = new \App\Models\PlansHistory;
            $plan_history->plan_id = $plan->id;
            $plan_history->user_id = $user->id;
            $plan_history->save();

            
            // Send email notification
            $user->save();
        };

        return serialize(new SerializableClosure($call_function));
    }

    public static function store_function($user){
        $call_function = function($checkout, $payment_subscription_id = null) use($user){
            $details = [
                'shipping' => ao($checkout->meta, 'shipping'),
                'shipping_location' => ao($checkout->meta, 'shipping_location')
            ];

            $extra = [
                'cart' => ao($checkout->meta, 'cart')
            ];

            $payee_id = ao($checkout->meta, 'payee_id');

            $order = new \App\Models\ProductOrder;
            $order->user_id = $user->id;
            $order->payee_user_id = $payee_id;
            $order->email = $checkout->email;
            $order->price = $checkout->price;
            $order->details = $details;
            $order->currency = $checkout->currency;
            // $order->ref = $checkout->method_ref;
            $order->extra = $extra;
            $order->products = ao($checkout->meta, 'products');
            $order->status = 1;
            $order->save();

            // Add TimeLine

            $timeline = [
                'title' => __('Order Created'),
                'amount' => $checkout->price,
                'user_id'   => $payee_id
            ];

            $ordertimeline = new \App\Models\ProductOrderTimeline;
            $ordertimeline->user_id = $user->id;
            $ordertimeline->tid = $order->id;
            $ordertimeline->type = 'new_order_amount';
            $ordertimeline->data = $timeline;
            $ordertimeline->save();

            // $session_cart = \DarryCart::session($this->bio->id)->getContent();

            // foreach ($session_cart as $item) {
            //     $quantity = $item->quantity;

            //     $product = Product::find(ao($item->attributes, 'product_id'));

            //     if ($product && ao($product->stock_settings, 'enable')) {

            //         if (!empty(ao($item, 'attributes.options')) && $option = ProductOption::find(ao($item, 'attributes.options.id'))) {
            //             $option->decrement('stock', $option->stock - $quantity <= 0 ? $option->stock : $quantity);
            //         }else{
            //             $product->decrement('stock', $product->stock - $quantity <= 0 ? $product->stock : $quantity);
            //         }
            //     }
            // }

            // return true;
            //Send email to customer



            if (!$payee = \App\Models\User::find($order->payee_user_id)) {
                return false;
            }

            $price = $checkout->price; 

            $percentage = 0;
            // $percentage = (int) settings('payment_owallet.percent');
            // if($bio_id){
            //     $percentage = (int) plan('settings.percentage_payment', $bio_id);
            // }
            // $platform_fee = ($percentage / 100) * $price;

            // $_platform_extra = (float) (settings('payment_owallet.extra_fee') ? settings('payment_owallet.extra_fee') : 0);
            // $_platform_extra = 0;
            // $_platform_extra = in_array($checkout->currency, ['MGA', 'BIF', 'CLP', 'PYG', 'DJF', 'RWF', 'GNF', 'UGX', 'JPY', 'VND', 'VUV', 'XAF', 'KMF', 'KRW', 'XOF', 'XPF']) ? number_format($_platform_extra, 2, '.', '') : number_format($_platform_extra, 2, '.', '') * 100;

            // $platform_fee = ($percentage / 100) * $price + $_platform_extra;
            $platform_fee = ($percentage / 100) * $price;

            $_price = $price - $platform_fee;

            $users = [
                1 => ['user' => $user->id, 'type' => 'plus']
            ];
            $user->depositFloat($_price);
            $payee = \App\Models\User::where('email', $checkout->email)->first();

            $transaction_data = [
                'amount_settled' => $_price,
                'amount_received' => $price,
                'percentage' => $percentage,
                'bio' => $user->id,
                'payee' => $payee ? $payee->id : null,
                'item' => ao($checkout->meta, 'item'),
                'location' => tracking_log(),
            ];

            foreach ($users as $key => $value) {
                $transaction = new \App\Models\WalletTransaction;
                $transaction->user_id = ao($value, 'user');
                $transaction->method = 'yena_wallet';
                $transaction->amount = $price;
                $transaction->amount_settled = $_price;
                $transaction->currency = $checkout->currency;
                $transaction->spv_id = $checkout->id;
                $transaction->type = ao($value, 'type');
                $transaction->transaction = $transaction_data;
                // $transaction->payload = $data;
                $transaction->save();
            }

            // // Email class
            // $email = new \App\Email;
            // // Get email template
            // $template = $email->template(block_path('shop', 'Email/purchased_product_email.php'), ['order' => $order, 'order_id' => $order->id]);
            // // Email array
            // $mail = [
            //     'to' => $spv->email,
            //     'subject' => __('Your purchased product(s)', ['website' => config('app.name')]),
            //     'body' => $template
            // ];

            // $email->send($mail);
        };

        return serialize(new SerializableClosure($call_function));
    }


    public static function course_function($user){
        
        $call_function = function($checkout, $payment_subscription_id = null) use($user){
            $payee_id = ao($checkout->meta, 'payee_id');
            // if (!$payee = \App\Models\User::find(ao($checkout->meta, 'payee_id'))) {
            //     return false;
            // }
                
            // Email class
            // $email = new \App\Email;
            // // Get email template
            // $template = $email->template(block_path('course', 'Email/unlocked_course_email.php'), ['spv' => $spv]);
            // // Email array
            // $mail = [
            //     'to' => $payee->email,
            //     'subject' => __('You unlocked :course', ['website' => config('app.name'), 'course' => ao($spv->meta, 'item.name')]),
            //     'body' => $template
            // ];
    
            // $email->send($mail);
    
            // Send Email - FIX
            //dispatch(function() use ($spv){
    
            //});
    
            $course_id = ao($checkout->meta, 'course');
            if ($check = \App\Models\CoursesEnrollment::where('user_id', $user->id)->where('payee_user_id', $payee_id)->where('course_id', $course_id)->first()) {
                $check->update();
            }else{
                $enroll = new \App\Models\CoursesEnrollment;
                $enroll->user_id = $user->id;
                $enroll->payee_user_id = $payee_id;
                $enroll->course_id = $course_id;
                $enroll->save();
            }
            
            \App\Yena\SandyAudience::create_audience($user->id, $payee_id);
            // Order
            
            $order                  = new \App\Models\CoursesOrder;
            $order->user_id         = $user->id;
            $order->payee_user_id   = $payee_id;
            $order->course_id       = $course_id;
            $order->email           = $checkout->email;
            $order->price           = $checkout->price;
            $order->currency        = $checkout->currency;
            // $order->ref             = $checkout->method_ref;
            $order->status          = 1;
            $order->save();



            // Wallet
            $price = $checkout->price; 
            $percentage = 0;
            $platform_fee = ($percentage / 100) * $price;

            $_price = $price - $platform_fee;

            $users = [
                1 => ['user' => $user->id, 'type' => 'plus']
            ];
            $user->depositFloat($_price);
            $payee = \App\Models\User::where('email', $checkout->email)->first();

            $transaction_data = [
                'amount_settled' => $_price,
                'amount_received' => $price,
                'percentage' => $percentage,
                'bio' => $user->id,
                'payee' => $payee ? $payee->id : null,
                'item' => ao($checkout->meta, 'item'),
                'location' => tracking_log(),
            ];

            foreach ($users as $key => $value) {
                $transaction = new \App\Models\WalletTransaction;
                $transaction->user_id = ao($value, 'user');
                $transaction->method = 'yena_wallet';
                $transaction->amount = $price;
                $transaction->amount_settled = $_price;
                $transaction->currency = $checkout->currency;
                $transaction->spv_id = $checkout->id;
                $transaction->type = ao($value, 'type');
                $transaction->transaction = $transaction_data;
                // $transaction->payload = $data;
                $transaction->save();
            }
        };

        return serialize(new SerializableClosure($call_function));
    }

    public static function cr($gateway = false, $data = [], $call_function = null){
        $config = config("yena.gateway.$gateway");
        if (!$gateway && !$config) return false;

        $requestFunction = ao($config, 'requestFunction');
        $class = app()->make(ao($config, 'requestClass'));

        $gateway_keys = settings("payment_$gateway");

        $updatedData = [
            'keys' => $gateway_keys,
            'currency' => settings('payment.currency'),
            'method' => $gateway,
            'call_function' => $call_function,
            'payment_type' => ao($data, 'payment_type') ? ao($data, 'payment_type') : 'onetime',
        ];

        $data = [...$data, ...$updatedData];

        $checkout = CheckoutGo::create($data);
        //$checkout = CheckoutGo::find(2);
        $function = $class->$requestFunction($checkout);

        if(!ao($function, 'status')){
            session()->flash(ao($function, 'response'));
        }


        return ao($function, 'redirect');
    }
}
