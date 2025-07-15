<div class="flex">
    <div class="mr-5">
        {!! __icon('interface-essential', 'delete-disabled.2', 'w-5 h-5 !text-[color:red]') !!}
    </div>
    <div>
        <p class="font-medium text-sm">
            {{ __('Order Canceled') }}
        </p>
        <p class="text-gray-400 text-sm mt-2">
            {{ \Carbon\Carbon::parse($item->created_at)->toFormattedDateString() }}
        </p>
    </div>
    <div class="ml-auto text-right">
        <p class="font-medium text-xs">
            {{ __('Canceled') }}
        </p>
    </div>
</div>