<?php
   use App\Models\BioSite;
   use App\Yena\SandyAudience;
   use function Livewire\Volt\{state, mount, rules};

   state([
      'message' => '',
   ]);

   $post = function($section, $data = []){
      // $this->validate([
      //    'message' => 'required',
      // ]);
      $this->skipRender();
      if(!$site = BioSite::find(ao($section, 'site_id'))) {
         return [
            'status' => 'error',
            'response' => __('Site not found'),
         ];
      }

      $name = ao($data, 'name');
      $email = ao($data, 'email');
      $amount = ao($data, 'amount');

      // dd($section, $site, $email, $site->user);
      $validator = Validator::make([
         'email' => $email,
         'name' => $name,
         'amount' => $amount,
      ], [
         'name' => 'required',
         'email' => 'required|email',
         'amount' => 'required|numeric|min:1'
      ]);

      if($validator->fails()){
         return [
            'status' => 'error',
            'response' => $validator->errors()->first(),
         ];
      }

      $duration = ao($data, 'duration');
      $user = $site->user;
      $currency = settings('payment.currency');

      $price = $amount;
      $plan_id = md5("spv_bio_plan_recurring_id.{$duration}.{$user->id}.{$price}");
      $meta = [
          'payment_mode' => [
              'type' => $duration,
              'interval' => $duration == 'recurring' ? 'month' : '',
              'title' => __('Tip of :price', ['price' => "$price $currency"]),
              'name' => $user->name,
              'id' => $plan_id
         ],
         'info' => [
            'name' => $name,
            'email' => $email,
            'site_id' => $site->id,
            'section_id' => ao($section, 'uuid'),
            'currency' => $currency
         ],
         'item' => [
            'name' => __('Tip Jar'),
            'description' => __('You just got tipped <strong>:amount</strong> from your page <strong>:page</strong> by :email', ['amount' => $price, 'page' => $site->name, 'email' => $email])
         ],
      ];

      $_data = [
          'uref'           => md5(microtime()),
          'email'          => $email,
          'price'          => $price,
          'callback'       => route('general-success', [
            'redirect' => $this->redirect
          ]),
          'frequency'      => $duration == 'recurring' ? 'monthly' : '',
          'currency'       => $currency,
          'payment_type'   => $duration,
          'meta'           => $meta,
      ];
      
      $call_function = \App\Yena\SandyCheckout::tipUser($amount, $user, $data, iam(), $site->id);
      $call = \App\Yena\SandyCheckout::cr($user->paymentMethod(), $_data, $call_function);
        
      return $this->js("window.location.replace('$call');");
   };
?>

<div wire:ignore class="livewire-comp"></div>