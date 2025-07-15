
<ul class="flex flex-col">
        <li class="[list-style:none] flex relative h-[24px] min-h-0">
            <div class="flex flex-col flex-[0_1_0%] items-center">
                <span class="my-2 p-0.5 flex self-baseline border-solid border-2 rounded-[50%] [box-shadow:rgba(0,_0,_0,_0.2)_0px_2px_1px_-1px,_rgba(0,_0,_0,_0.14)_0px_1px_1px_0px,_rgba(0,_0,_0,_0.12)_0px_1px_3px_0px] mx-0 my-[11.5px] border-transparent text-[rgb(250,_250,_250)] bg-green-600"></span>
            </div>
                
            <div class="text-[12px] lg:!text-[14px] font-normal text-[1rem] leading-normal flex-1 px-[16px] py-[6px] text-left -mt-1">{{ __('Invoice created on :date', [
                'date' => \Carbon\Carbon::parse($invoice->created_at)->toFormattedDateString()
            ]) }}</div>
        </li>


        <li class="[list-style:none] flex relative h-[50px] lg:!h-[40px] min-h-0">
            <div class="flex flex-col flex-[0_1_0%] items-center">
                <span class="w-[2px] {{ $invoice->last_viewed ? 'bg-green-600' : 'bg-[rgb(189,_189,_189)]' }} flex-grow"></span>
                
                <span class="my-2 p-0.5 flex self-baseline border-solid border-2 rounded-[50%] [box-shadow:rgba(0,_0,_0,_0.2)_0px_2px_1px_-1px,_rgba(0,_0,_0,_0.14)_0px_1px_1px_0px,_rgba(0,_0,_0,_0.12)_0px_1px_3px_0px] mx-0 my-[11.5px] border-transparent text-[rgb(250,_250,_250)] {{ $invoice->last_viewed ? 'bg-green-600' : 'bg-[rgb(189,_189,_189)]' }}"></span>
            </div>
                
            <div class="mt-6 lg:!mt-[12px] text-[12px] lg:!text-[14px] font-normal text-[1rem] leading-normal flex-1 px-[16px] py-[6px] text-left">{{ !$invoice->last_viewed ? __('Invoice has not been viewed yet') : __('This invoice was last viewed on :date', [
                'date' => \Carbon\Carbon::parse($invoice->last_viewed)->toFormattedDateString()
            ])  }}</div>
        </li>

        @php
            $reminderSent = $invoice->timelines()->where('type', 'email_sent')->first();
        @endphp
        
        <li class="[list-style:none] flex relative h-[50px] lg:!h-[40px] min-h-0">
            <div class="flex flex-col flex-[0_1_0%] items-center">
                <span class="w-[2px] bg-[rgb(189,_189,_189)] flex-grow {{ $reminderSent ? 'bg-green-600' : 'bg-[rgb(189,_189,_189)]' }}"></span>
                
                <span class="my-2 p-0.5 flex self-baseline border-solid border-2 rounded-[50%] [box-shadow:rgba(0,_0,_0,_0.2)_0px_2px_1px_-1px,_rgba(0,_0,_0,_0.14)_0px_1px_1px_0px,_rgba(0,_0,_0,_0.12)_0px_1px_3px_0px] mx-0 my-[11.5px] border-transparent text-[rgb(250,_250,_250)] {{ $reminderSent ? 'bg-green-600' : 'bg-[rgb(189,_189,_189)]' }}"></span>
            </div>
                
            <div class="mt-6 lg:!mt-[12px] text-[12px] lg:!text-[14px] font-normal text-[1rem] leading-normal flex-1 px-[16px] py-[6px] text-left">
                
                @if ($reminderSent)
                    {{ __('We emailed the invoice to :name on :date', ['date' => \Carbon\Carbon::parse($reminderSent->created_at)->toFormattedDateString(), 'name' => ao($invoice->payer, 'name')]) }}
                @else
                    {{ __('Email has not sent to :name yet', ['name' => ao($invoice->payer, 'name')]) }}
                @endif
            </div>
        </li>

        @php
            $reminderSent = $invoice->timelines()->where('type', 'reminder_14_days')->exists();
            $twoWeeksDate = \Carbon\Carbon::parse($invoice->due)->subWeeks(2)->toFormattedDateString();
        @endphp
        @if (!$invoice->paid || $reminderSent)
        <li class="[list-style:none] flex relative h-[50px] lg:!h-[40px] min-h-0">
            <div class="flex flex-col flex-[0_1_0%] items-center">
                <span class="w-[2px] bg-[rgb(189,_189,_189)] flex-grow {{ $reminderSent ? 'bg-green-600' : 'bg-[rgb(189,_189,_189)]' }}"></span>
                
                <span class="my-2 p-0.5 flex self-baseline border-solid border-2 rounded-[50%] [box-shadow:rgba(0,_0,_0,_0.2)_0px_2px_1px_-1px,_rgba(0,_0,_0,_0.14)_0px_1px_1px_0px,_rgba(0,_0,_0,_0.12)_0px_1px_3px_0px] mx-0 my-[11.5px] border-transparent text-[rgb(250,_250,_250)] {{ $reminderSent ? 'bg-green-600' : 'bg-[rgb(189,_189,_189)]' }}"></span>
            </div>
                
            <div class="mt-6 lg:!mt-[12px] text-[12px] lg:!text-[14px] font-normal text-[1rem] leading-normal flex-1 px-[16px] py-[6px] text-left">
                
                @if ($reminderSent)
                    {{ __('Reminder for 2 weeks has been sent on :date', ['date' => $twoWeeksDate]) }}
                @else
                    {{ __('We\'ll send a 2 week reminder on :date', ['date' => $twoWeeksDate]) }}
                @endif
            </div>
        </li>
        @endif
        @php
            $reminderSent = $invoice->timelines()->where('type', 'reminder_7_days')->exists();
            $oneWeekDate = \Carbon\Carbon::parse($invoice->due)->subWeeks(1)->toFormattedDateString();
        @endphp
        
        @if (!$invoice->paid || $reminderSent)
        <li class="[list-style:none] flex relative h-[50px] lg:!h-[40px] min-h-0">
            <div class="flex flex-col flex-[0_1_0%] items-center">
                <span class="w-[2px] bg-[rgb(189,_189,_189)] flex-grow {{ $reminderSent ? 'bg-green-600' : 'bg-[rgb(189,_189,_189)]' }}"></span>
                
                <span class="my-2 p-0.5 flex self-baseline border-solid border-2 rounded-[50%] [box-shadow:rgba(0,_0,_0,_0.2)_0px_2px_1px_-1px,_rgba(0,_0,_0,_0.14)_0px_1px_1px_0px,_rgba(0,_0,_0,_0.12)_0px_1px_3px_0px] mx-0 my-[11.5px] border-transparent text-[rgb(250,_250,_250)] {{ $reminderSent ? 'bg-green-600' : 'bg-[rgb(189,_189,_189)]' }}"></span>
            </div>
                
            <div class="mt-6 lg:!mt-[12px] text-[12px] lg:!text-[14px] font-normal text-[1rem] leading-normal flex-1 px-[16px] py-[6px] text-left">
                
                @if ($reminderSent)
                    {{ __('Reminder for 1 week has been sent on :date', ['date' => $oneWeekDate]) }}
                @else
                    {{ __('We\'ll send a 1 week reminder on :date', ['date' => $oneWeekDate]) }}
                @endif
            </div>
        </li>
        @endif
        
        @php
            $reminderSent = $invoice->timelines()->where('type', 'reminder_1_day')->exists();
            $oneDayDate = \Carbon\Carbon::parse($invoice->due)->subDays(1)->toFormattedDateString();
        @endphp
        @if (!$invoice->paid || $reminderSent)
        <li class="[list-style:none] flex relative h-[50px] lg:!h-[40px] min-h-0">
            <div class="flex flex-col flex-[0_1_0%] items-center">
                <span class="w-[2px] bg-[rgb(189,_189,_189)] flex-grow {{ $reminderSent ? 'bg-green-600' : 'bg-[rgb(189,_189,_189)]' }}"></span>
                
                <span class="my-2 p-0.5 flex self-baseline border-solid border-2 rounded-[50%] [box-shadow:rgba(0,_0,_0,_0.2)_0px_2px_1px_-1px,_rgba(0,_0,_0,_0.14)_0px_1px_1px_0px,_rgba(0,_0,_0,_0.12)_0px_1px_3px_0px] mx-0 my-[11.5px] border-transparent text-[rgb(250,_250,_250)] {{ $reminderSent ? 'bg-green-600' : 'bg-[rgb(189,_189,_189)]' }}"></span>
            </div>
                
            <div class="mt-6 lg:!mt-[12px] text-[12px] lg:!text-[14px] font-normal text-[1rem] leading-normal flex-1 px-[16px] py-[6px] text-left">
                
                @if ($reminderSent)
                    {{ __('Reminder for 1 day has been sent on :date', ['date' => $oneDayDate]) }}
                @else
                    {{ __('We\'ll send a 1 day reminder on :date', ['date' => $oneDayDate]) }}
                @endif
            </div>
        </li>
        @endif
        
        @php
            $invoicePaid = $invoice->timelines()->where('type', 'paid')->first();
        @endphp
        @if ($invoice->paid && $invoicePaid)
        <li class="[list-style:none] flex relative h-[50px] lg:!h-[40px] min-h-0">
            <div class="flex flex-col flex-[0_1_0%] items-center">
                <span class="w-[2px] bg-[rgb(189,_189,_189)] flex-grow bg-green-600"></span>
                
                <span class="my-2 p-0.5 flex self-baseline border-solid border-2 rounded-[50%] [box-shadow:rgba(0,_0,_0,_0.2)_0px_2px_1px_-1px,_rgba(0,_0,_0,_0.14)_0px_1px_1px_0px,_rgba(0,_0,_0,_0.12)_0px_1px_3px_0px] mx-0 my-[11.5px] border-transparent text-[rgb(250,_250,_250)] bg-green-600"></span>
            </div>
                
            <div class="mt-6 lg:!mt-[12px] text-[12px] lg:!text-[14px] font-normal text-[1rem] leading-normal flex-1 px-[16px] py-[6px] text-left">
                {{ __('Invoice payment was paid on :date', [
                    'date' => \Carbon\Carbon::parse($invoicePaid->created_at)->toFormattedDateString()
                ]) }}
            </div>
        </li>
        @else
        <li class="[list-style:none] flex relative h-[50px] lg:!h-[40px] min-h-0">
            <div class="flex flex-col flex-[0_1_0%] items-center">
                <span class="w-[2px] bg-[rgb(189,_189,_189)] flex-grow bg-[rgb(189,_189,_189)]"></span>
                
                <span class="my-2 p-0.5 flex self-baseline border-solid border-2 rounded-[50%] [box-shadow:rgba(0,_0,_0,_0.2)_0px_2px_1px_-1px,_rgba(0,_0,_0,_0.14)_0px_1px_1px_0px,_rgba(0,_0,_0,_0.12)_0px_1px_3px_0px] mx-0 my-[11.5px] border-transparent text-[rgb(250,_250,_250)] bg-[rgb(189,_189,_189)]"></span>
            </div>
                
            <div class="mt-6 lg:!mt-[12px] text-[12px] lg:!text-[14px] font-normal text-[1rem] leading-normal flex-1 px-[16px] py-[6px] text-left">
                @if (\Carbon\Carbon::now()->isPast() && !$invoice->paid)
                    {{ __('Invoice payment is past due since :date', [
                        'date' => \Carbon\Carbon::parse($invoice->due)->toFormattedDateString()
                    ]) }}
                @else
                    {{ __('Invoice payment is due by :date', [
                        'date' => \Carbon\Carbon::parse($invoice->due)->toFormattedDateString()
                    ]) }}
                @endif
            </div>
        </li>
        @endif
</ul>