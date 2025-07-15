
<?php
   use App\Models\YenaTemplate;
   use App\Models\Site;
   use App\Livewire\Actions\ToastUp;
   use App\Yena\Site\Generate;
   use function Livewire\Volt\{state, mount, placeholder, uses, rules, updated};

   state([
      'name' => ''
   ]);

   rules(fn () => [
      'name' => 'required|min:2',
   ]);
   uses([ToastUp::class]);
   mount(fn() => '');

   $createSite = function($id){
      $this->validate();
		$randomSlug = str()->random(15);
		$addressSlug = str()->random(7);

      $name = $this->name;
		$slug_name = slugify($name, '-');
		$_slug = "$slug_name-$randomSlug";
		$address = "$slug_name-$addressSlug";
      if(!$template = YenaTemplate::find($id)) abort(404);

      if(!$template->isFree() && !iam()->hasTemplateAccess($template->id)){
         $this->js(
            '
               window.runToast("error", "'. __('Purchase this template to use.') .'");
            '
         );

         // Maybe dispatch event to purchase
         return;
      }

      // Template is free
      
      $site = $template->site;
         
      $_c = Site::where('user_id', iam()->id)->count();
      if(__o_feature('consume.sites', iam()) != -1 && $_c >= __o_feature('consume.sites', iam())){
         $this->js('window.runToast("error", "'. __('You have reached your site creation limit. Please upgrade your plan.') .'");');
         return;
      }

      $site = $site->duplicateSite();
      $site->user_id = iam()->id;
      $site->created_by = iam()->get_original_user()->id;
      $site->name = $name;
      $site->_slug = $_slug;
      $site->address = $address;
      $site->save();
      $route = route('console-builder-index', ['slug' => $site->_slug]);

      $this->js(
         '
               window.runToast("success", "'. __('Site created successfully') .'")
               setTimeout(function() {
                window.location.replace("'.$route.'");
               }, 2000);
         '
      );
   };

   placeholder('
   <div class="p-5 w-full">
      <div class="--placeholder-skeleton w-full h-[60px] rounded-[var(--yena-radii-sm)]"></div>
      <div class="--placeholder-skeleton w-full h-[60px] rounded-[var(--yena-radii-sm)] mt-1"></div>
      <div class="--placeholder-skeleton w-full h-[60px] rounded-[var(--yena-radii-sm)] mt-1"></div>
   </div>');
?>


<div class="w-full">
   <div class="flex flex-col">
      <a x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-3 right-3 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
         <i class="fi fi-rr-cross text-sm"></i>
      </a>

      <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('Create Site') }}</header>

      <hr class="yena-divider">

      <form wire:submit="createSite(selectedTemplate)" class="px-8 pt-4 pb-6">
         <div class="flex flex-col gap-6">
            <x-input-x wire:model="name" label="{{ __('Your business name') }}"></x-input-x>
         </div>

         @php
            $error = false;

            if(!$errors->isEmpty()){
                  $error = $errors->first();
            }
         @endphp
         @if ($error)
            <div class="mt-4 bg-red-200 text-[11px] p-1 px-2 rounded-md">
                  <div class="flex items-center">
                     <div>
                        <i class="fi fi-rr-cross-circle flex text-xs"></i>
                     </div>
                     <div class="flex-grow ml-1 text-xs">{{ $error }}</div>
                  </div>
            </div>
         @endif

         <button class="yena-button-stack mt-5 w-full">{{ __('Create') }}</button>
      </form>
   </div>
</div>