
<form action="{{ route('console-admin-languages-post', 'edit') }}" method="POST">
  @csrf
  <input type="hidden" name="language" value="{{ $locale }}">
  <div class="grid grid-cols-1 gap-4 mb-5">
    <div class="form-input">
      <label>{{ __('Language Name') }}</label>
      <input type="text" name="name" value="{{ ao($info($locale), 'name') }}">
    </div>
  </div>
  
  @php
      $checked = config('app.locale') == $locale ? true : false;
  @endphp
  
  <div class="flex items-center gap-4 py-4 mb-5">
    <div>
       <div>{{ __('Set as Default Language') }}</div>
       <div class="text-xs font-normal text-gray-500">{{ __('If this language is set as default, app will appear in this language for all users.') }}</div>
    </div>
    <div class="flex-grow"></div>

    <x-input.checkbox name="default" checked="{{ $checked }}"/>
 </div>
  
  <button class="yena-button-stack !text-sm !w-full" aria-expanded="false">
    <div class="--sandy-button-container">
        <span>{{ __('Save') }}</span>
    </div>
  </button>
</form>