@php
    $invoiceField = config('yena.invoicefields');
@endphp
<div class="p-6 shadow-none mt-0 rounded-xl shadow-lg bg-white">
  <div class="font-heading font--12 font-extrabold uppercase tracking-wider text-zinc-400 flex items-center text-xl">
     <span class="whitespace-nowrap">{{ __('Invoice') }}</span>
  </div>
  <hr class="my-4">

  <div class="form-input mb-5">
    <label>{{ __('Enable') }}</label>
    <select name="settings[invoice][enable]">
      @foreach (['0' => 'Disable', '1' => 'Enable'] as $key => $value)
      <option value="{{ $key }}" {{ settings('invoice.enable') == $key ? 'selected' : '' }}>
        {{ __($value) }}
      </option>
      @endforeach
    </select>
  </div>
  
  <div class="font-heading my-6 pr-2 text-zinc-400 flex items-center">
   <span class="whitespace-nowrap"><i class="fi fi-rr-settings"></i></span>
   <div class="border-b border-solid border-gray-300 w-full ml-2 flex"></div>
 </div>
  
  <div class="grid grid-cols-2 gap-4">
      @foreach ($invoiceField as $key => $value)
      <div class="form-input">
      <label>{{ ao($value, 'name') }}</label>
      <input type="text" name="settings[invoice][{{$key}}]" value="{{ settings("invoice.$key") }}">
      </div>
      @endforeach
   </div>
</div>