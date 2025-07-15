<?php
   use App\Models\BioSite;
   use App\Yena\SandyAudience;
   use function Livewire\Volt\{state, mount, rules};

   state([
      'message' => '',
   ]);

   $post = function($section, $email){
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

      // dd($section, $site, $email, $site->user);
      $validator = Validator::make([
         'email' => $email
      ], [
         'email' => 'required|email'
      ]);

      if($validator->fails()){
         return [
            'status' => 'error',
            'response' => $validator->errors()->first('email'),
         ];
      }

      SandyAudience::subscribe_audience($site->user->id, $site->user, $email);
      return [
         'status' => 'success',
         'response' => '',
      ];
   };
?>

<div wire:ignore class="livewire-comp"></div>