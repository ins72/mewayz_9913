<div class="h-full overflow-y-auto">
   <a @click="add_user=false" class="absolute appearance-none select-none top-3 right-3 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
      <i class="fi fi-rr-cross text-sm"></i>
   </a>

   <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('Attach user to plan') }}</header>
   <hr class="yena-divider">
   <form class="px-6 pb-6 mt-4" method="post" action="{{ route('console-admin-plans-post', 'add_user') }}">
      @csrf

      <div class="grid md:grid-cols-2 grid-cols-1 gap-4">
         <div class="form-input">
         <label class="initial">{{ __('Plan') }}</label>
         <select name="plan_id">
            @foreach ($plans as $plan)
               <option value="{{ $plan->id }}">{{ $plan->name }}</option>
            @endforeach
         </select>
         </div>
         <div class="form-input">
         <label class="initial">{{ __('User') }}</label>
         <select name="user_id">
            @foreach (\App\Models\User::get() as $user)
               <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
         </select>
         </div>
      </div>
      <div class="form-input mt-5">
         <label class="initial">{{ __('Expire on') }}</label>
         <input
         type="date"
         name="date"
         autocomplete="off"
         >
      </div>
      <button type="submit" class="yena-button-stack mt-4 w-[100%]">
         <div class="relative flex items-center justify-center ">
            <div class="duration-100 undefined false">{{ __('Save') }}</div>
         </div>
      </button>
   </form>
</div>