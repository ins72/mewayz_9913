@php
    $plan = $item;
@endphp
<div class="h-[calc(100vh_-_50px)] overflow-y-auto">
  <a @click="closeModal('plan-' + {{ $item->id }})" class="absolute appearance-none select-none top-3 right-3 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
     <i class="fi fi-rr-cross text-sm"></i>
  </a>

  <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('Edit Plan') }}</header>
  <hr class="yena-divider">

  <form class="px-6 pb-6" method="post" action="{{ route('console-admin-plans-post', 'edit') }}">
    @csrf

    <input type="hidden" name="_id" value="{{ $plan->id }}">

    <div class="mt-4">
      <div class="form-input">
        <label>{{ __('Package Name') }}</label>
        <input type="text" name="name" value="{{ $plan->name }}">
      </div>
      <div class="form-input mt-4">
        <label>{{ __('Package Description') }}</label>
        <textarea name="description" cols="30" rows="5">{{ $plan->description }}</textarea>
      </div>


     
      <div class="font-heading my-4 mt-5 pr-2 text-[12px] font-extrabold uppercase tracking-wider text-zinc-400 flex items-center mb-2">
        <span class="whitespace-nowrap">{{ __('Pricing') }}</span>
        <div class="border-b border-solid border-gray-300 w-full ml-2 flex"></div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div class="form-input">
          <input type="text" name="monthly" placeholder="{{ __('Monthly Price') }}" value="{{ $plan->price }}">
        </div>
        <div class="form-input">
          <input type="text" name="annual" placeholder="{{ __('Annual Price') }}" value="{{ $plan->annual_price }}">
        </div>
      </div>

      
      <div class="font-heading my-4 pr-2 text-zinc-400 flex items-center">
        <span class="whitespace-nowrap"><i class="fi fi-rr-settings"></i></span>
        <div class="border-b border-solid border-gray-300 w-full ml-2 flex"></div>
      </div>

      <div class="flex items-center gap-4 py-4">
        <div class="mt-1 self-start">
           <i class="fi fi-rr-puzzle-alt text-lg"></i>
        </div>
        <div>
           <div class="text-[12px]">{{ __('Free Plan') }}</div>
           <div class="text-xs font-normal text-gray-500">{{ __('Make this plan free without payments.') }}</div>
        </div>
        <div class="flex-grow"></div>

        <x-input.checkbox  checked="{{ $plan->is_free }}" value="1" name="is_free" ></x-input.checkbox> 
     </div>

     <div class="flex items-center gap-4 py-4">
       <div class="mt-1 self-start">
          <i class="fi fi-rr-coins text-lg"></i>
       </div>
       <div>
          <div class="text-[12px]">{{ __('Enable Trial') }}</div>
          <div class="text-xs font-normal text-gray-500">{{ __('Make this plan free without payments.') }}</div>
       </div>
       <div class="flex-grow"></div>

        <x-input.checkbox checked="{{ $plan->has_trial }}" value="1" name="has_trial" ></x-input.checkbox> 
    </div>
    <div class="form-input">
      <label class="text-xs">{{ __('Trial Expiry') }}</label>
      <input type="text" name="trial_days" value="{{ $plan->trial_days }}" class="text-xs">
    </div>
     
      <div class="font-heading mb-2 mt-5 pr-2 text-[12px] font-extrabold uppercase tracking-wider text-zinc-400 flex items-center mb-2">
        <span class="whitespace-nowrap">{{ __('Plan Features') }}</span>
        <div class="border-b border-solid border-gray-300 w-full ml-2 flex"></div>
      </div>
      <div class="grid grid-cols-1 gap-4">

        @foreach (ao($skeleton, 'feature') as $key => $item)

          @php
              $feature = $plan->features()->code($key)->first();
          @endphp

          <label class="sandy-big-checkbox">
              <input type="hidden" name="feature[{{ $key }}]" value="0">
              <input type="checkbox" name="feature[{{ $key }}]" class="sandy-input-inner" value="1" {{ $feature && $feature->enable ? 'checked' : '' }}>
              <div class="checkbox-inner !h-[3em] !py-2 !border-gray-200">
                <div class="checkbox-wrap">
                  <div class="content">
                    <h1 class="text-[12px]">{{ ao($item, 'name') }}</h1>
                    <p class="font--10">{{ ao($item, 'description') }}</p>
                  </div>
                  <div class="icon">
                    <div class="active-dot h-5 w-5">
                      <i class="fi fi-rr-check font--8"></i>
                    </div>
                  </div>
                </div>
              </div>
          </label>
        @endforeach
        </div>

      
      <div class="font-heading mb-2 mt-5 pr-2 text-[12px] font-extrabold uppercase tracking-wider text-zinc-400 flex items-center mb-2">
        <span class="whitespace-nowrap">{{ __('Consumeable') }}</span>
        <div class="border-b border-solid border-gray-300 w-full ml-2 flex"></div>
      </div>
      <div class="grid grid-cols-1 gap-4">

        @foreach (ao($skeleton, 'consume') as $key => $item)

          @php
              $feature = $plan->features()->code($key)->first();
          @endphp

          <div class="bg-[var(--yena-colors-gray-50)] p-4 rounded-xl">
            <div>
              <label class="text-xs">{{ ao($item, 'name') }}</label>
              <p>{{ ao($item, 'description') }}</p>
            </div>
            <div class="custom-content-input border-2 border-dashed mb-1">
              <input type="text" name="consume[{{ $key }}]" value="{{ $feature ? $feature->limit : -1 }}" class="w-[100%] !bg-white" placeholder="{{ ao($item, 'description') }}">
                
              @if ($key == 'consume.site_traffic')
              <label class="h-10 !flex items-center px-5">
                {{ __('/ mo') }}
              </label>
              @endif
              @if ($key == 'consume.upload_storage')
              <label class="h-10 !flex items-center px-5">
                {{ __('MB') }}
              </label>
              @endif
          </div>
          </div>
        @endforeach
        </div>
        <div>
          <p class="mt-2 text-xs text-gray-600 flex items-center gap-1"><i class="fi fi-rr-triangle-warning"></i> {{ __('-1 for unlimited') }}</p>
        </div>

    </div>
    <button type="submit" class="yena-button-stack mt-4 w-[100%]">
       <div class="relative flex items-center justify-center ">
          <div class="duration-100 undefined false">{{ __('Save') }}</div>
       </div>
    </button>
  </form>
</div>