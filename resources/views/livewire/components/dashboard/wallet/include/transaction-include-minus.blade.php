@php
    $user = \App\Models\User::find(ao($item->transaction, 'user_id'));
@endphp

<div class="wallet-transactions-details">
   <div class="transaction-avatar">
      @if ($user)
         <img src="{{ $user->getAvatar() }}" alt=" " class="rounded-full">
      @endif
   </div>
   <div class="transaction-title flex flex-col justify-between">
      <span class="transaction-name"> {!! ao($item->transaction, 'item.description') !!}</span>
      <span class="transaction-date uppercase">{{ \Carbon\Carbon::parse($item->created_at)->toFormattedDateString() }}</span>
   </div>
</div>
<div class="transaction-amount flex items-center">
   @if ($user)
      <div class="transaction-price negative !text-red-500">- {!! price_with_cur($item->currency, $item->amount) !!}</div>
   @endif
   <div>
      <svg width="9" height="14" viewBox="0 0 9 14" fill="none" xmlns="http://www.w3.org/2000/svg">
         <path d="M0 12.5825L1.51406 14L9 7V7V7L1.51406 0L0 1.4175L5.96719 7L0 12.5825Z" fill="#CACACA"></path>
      </svg>
   </div>
</div>