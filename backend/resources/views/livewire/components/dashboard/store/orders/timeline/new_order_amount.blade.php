<div class="flex">
    <div class="mr-5">
        {!! __i('money', 'Moneybag', 'w-5 h-5') !!}
    </div>
    <div>
        <p class="font-medium text-sm">
            {{ __('Product Order') }}
        </p>
        <p class="text-gray-400 text-sm mt-2">
            {{ \Carbon\Carbon::parse($item->created_at)->toFormattedDateString() }}
        </p>
    </div>
    <div class="ml-auto text-right">
        <p class="flex justify-end items-center">
            <span class="text-gray-400 mr-2"><i class="sni sni-plus"></i></span>
            <span class="font-medium text-sm">
                {!! iam()->price(ao($item->data, 'amount')) !!}
            </span>
        </p>
        @if ($user = \App\Models\User::find(ao($item->data, 'user_id')))
        <p class="uppercase text-xs text-gray-600">
            {{ __('By') }} {{ $user->name }}
        </p>
        @endif
    </div>
</div>