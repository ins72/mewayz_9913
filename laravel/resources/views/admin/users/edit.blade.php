
<div>
  <div class="h-full overflow-y-auto">
    <a @click="closeModal('user-' + {{ $_user->id }})" class="absolute appearance-none select-none top-3 right-3 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
       <i class="fi fi-rr-cross text-sm"></i>
    </a>
 
    <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('Edit User') }}</header>
    <hr class="yena-divider">
 


    <form class="px-6 pb-6 pt-4" method="POST" action="{{ route('console-admin-users-post', 'edit') }}">
      @csrf
      <input type="hidden" value="{{ $_user->id }}" name="_id">
    
      <div class="md:grid grid-cols-2 gap-4">
        <div class="form-input mb-7 md:mb-0">
          <label>{{ __('Name') }}</label>
          <input type="text" name="name" value="{{ $_user->name }}">
        </div>
        <div class="form-input">
          <label>{{ __('Email') }}</label>
          <input type="text" name="email" value="{{ $_user->email }}">
        </div>
      </div>
      <div class="md:grid grid-cols-2 gap-4 mt-5">
        <div class="form-input mb-7 md:mb-0">
          <label class="initial">{{ __('User Status') }}</label>
          <select name="status">
            <option value="0" {{ !$_user->status ? 'selected' : '' }}>{{ __('Disabled') }}</option>
            <option value="1" {{ $_user->status ? 'selected' : '' }}>{{ __('Active') }}</option>
          </select>
        </div>
        <div class="form-input">
          <label class="initial">{{ __('User Role') }}</label>
          <select name="role">
            <option value="0" {{ !$_user->role ? 'selected' : '' }}>{{ __('User') }}</option>
            <option value="1" {{ $_user->role ? 'selected' : '' }}>{{ __('Admin') }}</option>
          </select>
        </div>
      </div>
      
      <div class="md:grid grid-cols-2 gap-4 mt-5">
        <div class="form-input md:mb-0">
          <label>{{ __('Password') }}</label>
          <input type="password" name="password">
        </div>
        <div class="form-input">
          <label>{{ __('Confirm Password') }}</label>
          <input type="password" name="password_confirmation">
        </div>
      </div>
    
    
            
      <button type="submit" class="yena-button-stack mt-4 w-[100%]">
        <div class="relative flex items-center justify-center ">
          <div class="duration-100 undefined false">{{ __('Save') }}</div>
        </div>
     </button>
    </form>
  </div>
 </div>
 

<div>
  
  <form action="{{ route('console-admin-users-post', 'login') }}" method="POST" class="popup-user-login px-6 mb-5">
    @csrf
      <input type="hidden" value="{{ $_user->id }}" name="_id">
      
            
      <button type="submit" class="yena-button-stack mt-0 w-[100%]">
        <div class="relative flex items-center justify-center ">
          <div class="duration-100 undefined false">{{ __('Login') }}</div>
        </div>
      </button>
  </form>
</div>