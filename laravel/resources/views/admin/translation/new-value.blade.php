
<form action="{{ route('console-admin-languages-post', 'new_value') }}" method="POST">
  @csrf
  <input type="hidden" name="language" value="{{ $locale }}">
  <div class="grid grid-cols-1 gap-4 mb-5">
    <div class="form-input">
      <label>{{ __('Previous Value') }}</label>
      <input type="text" name="previous">
    </div>
    <div class="form-input">
      <label>{{ __('New Value') }}</label>
      <input type="text" name="new">
    </div>
  </div>
  
  <button type="submit" class=" bg-primary focus:bg-primary-400 hover:bg-primary-400 block appearance-none rounded-md text-sm font-medium text-white duration-100 focus:outline-none disabled:opacity-75 px-4 py-2.5 w-full">
    <div class="relative flex items-center justify-center ">
        <div class="duration-100">{{ __('Submit') }}</div>
    </div>
  </button>
</form>