<div class="flex">
    <div class="mr-5">
        {!! __i('--ie', 'checkmark-done-check', 'w-5 h-5 text-green-500') !!}
    </div>
    <div>
        <p class="font-medium text-sm">
            {{ __('Order Completed') }}
        </p>
        <p class="text-gray-400 text-sm mt-2">
            {{ \Carbon\Carbon::parse($item->created_at)->toFormattedDateString() }}
        </p>
    </div>
    <div class="ml-auto text-right">
        <p class="font-medium text-xs">
            {{ __('Marked as completed') }}
        </p>
    </div>
</div>