<div class="flex">
    <div class="mr-5">
        {!! __i('emails', 'email-mail-letter-fast-send-circle', 'w-5 h-5') !!}
    </div>
    <div>
        <p class="font-medium text-sm">
            {{ __('Email receipt sent') }}
        </p>
        <p class="text-gray-400 text-sm mt-2">
            {{ \Carbon\Carbon::parse($item->created_at)->toFormattedDateString() }}
        </p>
    </div>
    <div class="ml-auto text-right truncate">
        <p class="font-medium text-xs truncate">
            {{ ao($item->data, 'email') }}
        </p>
    </div>
</div>