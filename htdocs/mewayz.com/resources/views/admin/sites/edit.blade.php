
<div>
  <div class="h-full overflow-y-auto">
    <div class="relative flex h-32 flex-col justify-end overflow-hidden bg-gray-100 p-6">
       <div class="absolute inset-0"></div>
       <div class="absolute inset-0 bg-gradient-to-tr from-gray-100 to-gray-100/0"></div>
       <div class="absolute right-4 top-4" x-on:click="closeModal('user-' + {{ $_user->id }})">
          <button class="rounded-md p-1 text-gray-600 hover:bg-gray-200">
             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke="currentColor" fill="none" style="" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
             </svg>
          </button>
       </div>
       <div class="font-heading mb-2 pr-2 font--12 font-extrabold uppercase tracking-wider text-gray-700 flex items-center mb-2">
        <span class="whitespace-nowrap">{{ __('Super Admin') }}</span>
        <div class="border-b border-solid border-gray-300 w-full ml-2 flex"></div>
      </div>
       <div class="relative">
          <h1 class="text-xl font-bold">{{ __('Edit User') }}</h1>
       </div>
    </div>


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
    
    
      <button type="submit" class="hover:shadow-primary-600/20 bg-black text-white duration-200 hover:scale-[1.02] hover:shadow-xl active:scale-[0.98] disabled:opacity-80 mt-4 w-full block appearance-none rounded-lg text-sm font-medium duration-100 focus:outline-transparent px-4 py-2.5">
        <div class="relative flex items-center justify-center ">
           <div class="duration-100 undefined false">{{ __('Save') }}</div>
        </div>
     </button>
    </form>
  </div>
 </div>
 

<div>
  
  <form action="{{ route('console-admin-users-post', 'login') }}" method="POST" class="popup-user-login px-6">
    @csrf
      <input type="hidden" value="{{ $_user->id }}" name="_id">
      <button class="sandy-button bg-black py-2 flex-grow">
        <div class="--sandy-button-container">
          <span class="text-xs">{{ __('Login') }}</span>
        </div>
    </button>
  </form>
</div>