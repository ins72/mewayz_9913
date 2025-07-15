<x-layouts.base>
   <x-slot:title>{{ __('Invoicing') }}</x-slot>

   <div>
    <div>
      <div style="position: relative; margin: 0 0.5rem 0.5rem; height: 650px; overflow-x: hidden; background-color: white; font-size: 12px; box-shadow: rgba(0, 0, 0, 0.1) 0px 4px 6px;">
        <div id="invoice-pdf-container" style="position: relative; box-sizing: border-box; display: flex; align-items: center; justify-content: center; letter-spacing: 0.05em; height: 100%; width: 100%; background: linear-gradient(224.19deg, rgb(164, 252, 172) -2.33%, rgb(164, 195, 242) 51.26%, rgb(98, 225, 247) 106.61%);">
            <div style="display: flex; height: calc(100% - 20px); width: calc(100% - 20px); flex-direction: column; justify-content: space-between; background-color: white;">
                <div style="padding: 0.5rem 0.75rem; padding-top: 0.75rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <div style="display: flex; flex-direction: column; justify-content: space-between;">
                            <div style="font-size: 18px;line-height: normal;font-weight: 700;letter-spacing: -0.012em;">{{ __('Invoice') }}</div>
                            <div style="margin: 0.5rem 0; font-size: 10px; line-height: 1.1;">{{ __('No.') }} <span x-text="currentInvoiceNumber"></span></div>
                            <div style="font-size: 10px; line-height: 1.1;"><span style="font-weight: bold; color: rgb(75, 85, 99);">{{ __('Invoice Date:') }} </span> <span x-text="moment().format('MMM DD, YYYY')"></span></div>
                        </div>
                        <div style="flex-basis: 41.67%; display: flex; flex-direction: column; justify-content: space-between;">
                            <div style="font-size: 10px; line-height: 1.1; font-weight: bold; color: rgb(75, 85, 99);">{{ __('Amount Due:') }}</div>
                            <div style="font-size: 22px; font-weight: bold; line-height: 1.1; letter-spacing: -0.42px;" x-html="currency.code + invoice.price"></div>
                            <div style="font-size: 10px; line-height: 1.1;">
                                <span style="font-weight: bold; color: rgb(75, 85, 99);">{{ __('Payment Due Date:') }} </span><span style="white-space: nowrap;" x-text="moment(invoice.due).format('MMM DD, YYYY')"></span>
                            </div>
                        </div>
                    </div>
                    <div class="Divider" style="margin: 1rem 0;"></div>
                    <div style="display: flex; justify-content: space-between;">
                        <div style="display: flex; flex-direction: column;">
                            <div style="font-size: 10px; line-height: 1.1; font-weight: bold; color: rgb(75, 85, 99);">{{ __('From:') }}</div>
                            <div style="margin-top: 0.25rem; display: flex;">
                                <template x-if="invoice.data.image">
                                    <div>
                                        <div style="height: 2rem; width: 2rem; margin-right: 0.5rem;">
                                            <img style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;" alt=" " :src="getMedia(invoice.data.image)">
                                        </div>
                                    </div>
                                </template>
                                <div style="font-size: 8px;">
                                    <div style="font-size: 10px; line-height: 1.1; font-weight: bold;" x-text="invoice.data.name"></div>
                                    <div style="margin-top: 0.25rem;" x-text="invoice.data.email"></div>
                                    <div style="margin-top: 0.25rem;" x-text="invoice.data.billing"></div>
                                </div>
                            </div>
                        </div>
                        <div style="flex-basis: 41.67%; display: flex; flex-direction: column;">
                            <div style="font-size: 10px; line-height: 1.1; font-weight: bold; color: rgb(75, 85, 99);">{{ __('Billed To:') }}</div>
                            <div style="margin-top: 0.25rem; display: flex;">
                                <div style="display:flex;">
                                    <template x-if="invoice.payer.image">
                                        <div>
                                            <div style="height: 2rem; width: 2rem; margin-right: 0.5rem;">
                                                <img style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;" alt=" " :src="getMedia(invoice.payer.image)">
                                            </div>
                                        </div>
                                    </template>
                                    <div style="font-size: 8px;">
                                        <div style="font-size: 10px; line-height: 1.1; font-weight: bold;" x-text="invoice.payer.name"></div>
                                        <div style="margin-top: 0.25rem;" x-text="invoice.payer.email"></div>
                                        <div style="margin-top: 0.25rem;" x-text="invoice.payer.billing"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="margin-top: 1.5rem; font-size: 10px; line-height: 1.1;">
                        <div style="display:flex; justify-content: space-between; border-radius: 8px 8px 0 0; background-color: rgb(209, 213, 219); padding-left: 1rem; padding-right: 1rem; padding-top: 0.75rem; padding-bottom: 0.75rem;">
                            <div>{{ __('Item description') }}</div>
                            <div>{{ __('Amount') }}</div>
                        </div>
                        <div style="display:flex; justify-content: space-between; gap:.5rem; border-radius:0 0 8px 8px; border-width:.5px; border-style: solid;border-color: rgb(209,213,219); padding-left:.5rem;padding-right:.5rem;padding-top:.5rem;padding-bottom:.5rem;font-weight:bold;">
                            <div x-text="invoice.data.item_description"></div>
                            <div x-html="currency.code + invoice.price"></div>
                        </div>
                        <div style="margin-top:.75rem;margin-right:.25rem; display:flex; justify-content:end; gap:.5rem;">
                            <span>Total:</span>
                            <span style="margin-right:.0625rem;font-weight:bold;" x-html="currency.code + invoice.price"></span>
                        </div>
                    </div>
                    <template x-if="invoice.data.message">
                        <div style="display:flex; flex-direction,column;font-size:.625rem; line-height:.9;">
                            <div style="font-weight:bold;color:#4b5563;">{{ __('Memo:') }}</div>
                            <div style="margin-top:.25rem;" x-text="invoice.data.message"></div>
                        </div>
                    </template>
                </div>
                <div style="display:flex; align-items:center; justify-content:space-between;padding-left:.75rem;padding-right:.75rem;padding-top:.5rem;padding-bottom:.5rem;font-size:.5rem;">
                    <div>{{ __('Contact') }}<span class="underline">{{ config('app.APP_EMAIL') }}</span>{{ __('for support') }}</div>
                    <div style="display:flex; align-items:center;">
                        {{ __('Invoice powered by') }}
                        <div style="padding-left:.25rem;">
                            <img src="{{ logo() }}" style="height:.75rem;width:.75rem;object-fit:contain;" alt=" " width="36" class="block">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    </div>
   </div>
</x-layouts.base>