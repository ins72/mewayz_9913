<div>
    <div class="invoice-pdf-preview relative mx-2 mb-2 box-border h-[650px] overflow-x-hidden bg-white text-12 drop-shadow-md lg:!mx-auto">
        <div id="invoice-pdf-container" class="relative box-border flex items-center justify-center tracking-wide h-full w-[100%]" style="background: linear-gradient(224.19deg, rgb(252 230 164) -2.33%, rgb(164 242 206) 51.26%, rgb(247 98 230) 106.61%);">
        <div class="flex h-[calc(100%-20px)] w-[calc(100%-20px)] flex-col justify-between bg-white">
            <div class="scale-90 px-3 pt-2 lg:!scale-100 lg:!px-12 lg:!pt-9">
                <div class="flex justify-between">
                    <div class="flex flex-col justify-between">
                    <div class="title">{{ __('Invoice') }}</div>
                    <div class="my-2 text-[10px] leading-[1.1]">{{ __('No.') }} <span x-text="currentInvoiceNumber"></span></div>
                    <div class="text-[10px] leading-[1.1]"><span class="font-semibold text-gray-700">{{ __('Invoice Date:') }} </span> <span x-text="moment().format('MMM DD, YYYY')"></span></div>
                    </div>
                    <div class="flex w-5/12 flex-col justify-between">
                        <div class="text-[10px] leading-[1.1] font-semibold text-gray-700">{{ __('Amount Due:') }}</div>
                        <div class=" text-[22px] font-bold leading-[1.1] tracking-[-.42px]" x-html="currency.code + invoice.price"></div>
                        <div class="text-[10px] leading-[1.1]">
                            <span class="font-semibold text-gray-700">{{ __('Payment Due Date:') }} </span><span class="whitespace-nowrap" x-text="moment(invoice.due).format('MMM DD, YYYY')"></span>
                        </div>
                    </div>
                </div>
                <div class="Divider my-4"></div>
                <div class="flex justify-between">
                    <div class="flex flex-col">
                    <div class="text-[10px] leading-[1.1] font-semibold text-gray-700">{{ __('From:') }}</div>
                    <div class="mt-1 flex">
                        <template x-if="invoice.data.image">
                            <div>
                                <div class="h-8 w-8 mr-2">
                                    <img class="w-[100%] h-full rounded-full object-cover" alt=" " :src="getMedia(invoice.data.image)">
                                </div>
                            </div>
                        </template>
                        <div class="text-[8px]">
                            <div class="text-[10px] leading-[1.1] font-semibold" x-text="invoice.data.name"></div>
                            <div class="mt-1" x-text="invoice.data.email"></div>
                            <div class="mt-1" x-text="invoice.data.billing"></div>
                        </div>
                    </div>
                    </div>
                    <div class="flex w-5/12 flex-col">
                    <div class="text-[10px] leading-[1.1] font-semibold text-gray-700">{{ __('Billed To:') }}</div>
                    <div class="mt-1 flex">
                        <div class="flex">
                            <template x-if="invoice.payer.image">
                                <div>
                                    <div class="h-8 w-8 mr-2">
                                        <img class="w-[100%] h-full rounded-full object-cover" alt=" " :src="getMedia(invoice.payer.image)">
                                    </div>
                                </div>
                            </template>
                            <div class="text-[8px]">
                                <div class="text-[10px] leading-[1.1] font-semibold" x-text="invoice.payer.name"></div>
                                <div class="mt-1" x-text="invoice.payer.email"></div>
                                <div class="mt-1" x-text="invoice.payer.billing"></div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="mt-6 text-[10px] leading-[1.1]">
                    <div class="flex justify-between rounded-t-8 bg-gray-300 px-4 py-3">
                    <div>{{ __('Item description') }}</div>
                    <div>{{ __('Amount') }}</div>
                    </div>
                    <div class="flex justify-between gap-2 rounded-b-8 border border-solid border-gray-300 px-4 py-2 font-semibold">
                        <div x-text="invoice.data.item_description"></div>
                        <div x-html="currency.code + invoice.price"></div>
                    </div>
                    <div class="my-3 mr-4 flex justify-end gap-2">
                        <span>{{ __('Total:') }} </span>
                        <span class="mr-[1px] font-semibold" x-html="currency.code + invoice.price"></span>
                    </div>
                </div>
                <template x-if="invoice.data.message">
                    <div class="flex flex-col text-[10px] leading-[1.1]">
                        <div class="font-semibold text-gray-700">{{ __('Memo:') }}</div>
                        <div class="mt-1" x-text="invoice.data.message"></div>
                    </div>
                </template>
                {{-- <div class="mt-6"></div> --}}
            </div>
            <div class="flex items-center justify-between px-4 py-2 text-[8px]">
                <div>{{ __('Contact') }} <span class="underline">{{ config('app.APP_EMAIL') }}</span> {{ __('for support') }}</div>
                {{-- <a href="https://beacons.ai" class="font-semibold text-black underline" rel="noopener noreferrer" target="_blank">Try Beacons Invoice</a> --}}
                <div class="flex items-center">
                    {{ __('Invoice powered by') }}
                    <div class="px-1">
                        <img src="{{ logo() }}" class="h-16 w-16 object-contain" alt=" " width="36" class="block">
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>