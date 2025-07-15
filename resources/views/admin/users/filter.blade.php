
<form class="items-center" method="GET">
  <div class="form-input">
    <label>{{ __('Search') }}</label>
    <input class="notifications__input" type="text" name="query" value="{{ request()->get('query') }}" placeholder="{{ __('Query') }}">
  </div>
  

  <div class="border-b border-solid border-gray-300 my-5"></div>
  <div class="grid grid-cols-2 gap-4 overflow-y-auto">
        
    <div class="form-input ">
      <label class="initial mb-0">{{ __('Search by') }}</label>
      <select name="search_by">
        @foreach (['email' => 'Email', 'name' => 'Name'] as $key => $value)
        <option value="{{$key}}" {{ request()->get('search_by') == $key ? 'selected' : '' }}>{{ __($value) }}</option>
        @endforeach
      </select>
    </div>
    <div class="form-input ">
      <label class="initial mb-0">{{ __('Status') }}</label>
      <select name="status">
        <option value="">{{ __('All') }}</option>
        @foreach (['active' => 'Active', 'disabled' => 'Disabled'] as $key => $value)
        <option value="{{$key}}" {{ request()->get('status') == $key ? 'selected' : '' }}>{{ __($value) }}</option>
        @endforeach
      </select>
    </div>
    <div class="form-input ">
      <label class="initial mb-0">{{ __('Country') }}</label>
      <select name="country">
        <option value="">{{ __('All') }}</option>
      </select>
    </div>
    <div class="form-input ">
      <label class="initial mb-0">{{ __('Order by') }}</label>
      <select name="order_by">
        @foreach (['created_at' => 'Registration Date', 'lastActivity' => 'Last Activity', 'email' => 'Email', 'name' => 'Name'] as $key => $value)
        <option value="{{$key}}" {{ request()->get('order_by') == $key ? 'selected' : '' }}>{{ __($value) }}</option>
        @endforeach
      </select>
    </div>
    <!-- OrderType:START -->
    <div class="form-input ">
      <label class="initial mb-0">{{ __('Order type') }}</label>
      <select name="order_type">
        @foreach (['DESC' => 'Descending', 'ASC' => 'Ascending'] as $key => $value)
        <option value="{{$key}}" {{ request()->get('order_type') == $key ? 'selected' : '' }}>{{ __($value) }}</option>
        @endforeach
      </select>
    </div>
    <!-- OrderType:END -->
    <!-- Results Per Page:START -->
    <div class="form-input ">
      <label class="initial mb-0">{{ __('Per Page') }}</label>
      <select name="per_page">
        @foreach ([10, 25, 50, 100, 250] as $key => $value)
        <option value="{{$value}}" {{ request()->get('per_page') == $value ? 'selected' : '' }}>{{ $value }}</option>
        @endforeach
      </select>
    </div>
    </div>

    <button type="submit" class="yena-button-stack mt-4 w-[100%]">
      <div class="relative flex items-center justify-center ">
        <div class="duration-100 undefined false">{{ __('Filter') }}</div>
      </div>
    </button>
   <a class="text-blue-600 mt-4 font-bold block text-xs" href="">{{ __('Reset filter') }}</a>
</form>