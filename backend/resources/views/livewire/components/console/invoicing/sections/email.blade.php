<div>

    <div class="mx-2 mb-2 box-border rounded-8 bg-white p-6 text-left text-12 shadow shadow-gray-400 drop-shadow-md desktop:mx-auto">
        <div class="text-16 font-semibold">{{ __('Invoice from') }} <span x-text="invoice.data.name"></span></div>
        <div class="my-2 text-28 font-bold" x-html="currency.code + invoice.price"></div>
        <div>{{ __('Invoice Date:') }} <span x-text="moment().format('MMM DD, YYYY')"></span></div>
        <div>{{ __('Due Date:') }} <span x-text="moment(invoice.due).format('MMM DD, YYYY')"></span></div>
        <div class="Divider my-6 FullWidth"></div>
        <div class="flex">
           <div class="w-1/4">
              <div>{{ __('To') }}</div>
              <div>{{ __('From') }}</div>
           </div>
           <div>
              <div><span x-text="invoice.payer.name"></span> (<a href="_blank"><span x-text="invoice.payer.email"></span></a>)</div>
              <div><span x-text="invoice.data.name"></span> (<a href="_blank"><span x-text="invoice.data.email"></span></a>)</div>
           </div>
        </div>
        <div class="Divider my-6 FullWidth"></div>
        <div class="flex">
           <div class="w-1/2">
              <div class="font-bold">{{ __('Description') }}</div>
              <div x-text="invoice.data.item_description"></div>
           </div>
           <div class="w-1/2">
              <div class="font-bold">{{ __('Amount') }}</div>
              <div x-html="currency.code + invoice.price"></div>
           </div>
        </div>
        <div class="mt-2 font-bold">{{ __('Memo') }}</div>
        <div x-text="invoice.data.message"></div>
        <div class="Divider my-6 FullWidth"></div>
        <div class="mt-2 flex justify-center">
            <button class="yena-black-btn" tabindex="0" type="button">{{ __('Pay Invoice') }}</button>
        </div>
        {{-- <div class="mt-4 flex justify-center items-center text-14 text-gray-700">
           <p>{{ __('Powered by') }}</p>
           <img src="{{ logo() }}" class="h-16 w-16 object-contain" alt=" " width="36" class="block">
        </div> --}}
        <div class="mt-2 text-center text-gray-700 gap-2">
            
            @php
                $terms_link = settings('others.terms');
                $privacy_link = settings('others.privacy');
            @endphp
            {!! __t("By paying, I agree to our <a href=\"$terms_link\">Terms of service</a> and <a href=\"$privacy_link\">Privacy policy</a>.") !!}
        </div>
     </div>
</div>