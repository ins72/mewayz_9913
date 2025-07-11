<div class="h-full overflow-y-auto">
   <a @click="create_modal=false" class="absolute appearance-none select-none top-3 right-3 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
      <i class="fi fi-rr-cross text-sm"></i>
   </a>

   <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('Create Package') }}</header>
   <hr class="yena-divider">

   <form class="px-6 pb-6" method="post" action="{{ route('console-admin-plans-post', 'create') }}">
      @csrf

      <div class="mt-4">
         <div class="form-input">
         <label>{{ __('Package Name') }}</label>
         <input type="text" name="name" value="{{ old('name') }}">
         </div>
      </div>
      <button type="submit" class="yena-button-stack mt-4 w-[100%]">
         <div class="relative flex items-center justify-center ">
            <div class="duration-100 undefined false">{{ __('Save') }}</div>
         </div>
      </button>
   </form>
</div>