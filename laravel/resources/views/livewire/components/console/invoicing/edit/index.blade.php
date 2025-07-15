
<?php
    use App\Models\Invoice;
    use App\Livewire\Actions\ToastUp;
    use function Livewire\Volt\{state, mount, placeholder, rules, uses, updated, usesFileUploads};
    uses([ToastUp::class]);
    usesFileUploads();

    state([
        'slug',
        'user' => fn() => iam(),
        'currency' => fn() => $this->user->currency(),
    ]);

    state([
        'invoice' => null,
        'invoiceArray' => []
    ]);

    state([
        'currentInvoiceNumber' => 0
    ]);

    state([
        'data' => [
            'due' => '',
        ],
        'fromImage' => null,
        'payerImage' => null,
    ]);

    updated([
      'payerImage' => function(){
         if(!empty($this->payerImage)){
            $payer = $this->invoice->payer ?: [];
            $this->validate([
               'payerImage' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5048',
            ]);
            storageDelete('media/invoices', ao($this->invoice->payer, 'image'));
            
 
            $payer['image'] = str_replace('media/invoices/', "", $this->payerImage->storePublicly('media/invoices', sandy_filesystem('media')));
             
            $this->payerImage = null;
         }
 
         $this->invoice->payer = $payer;
         $this->invoice->save();
         $this->_refresh();
       },
      'fromImage' => function(){
         if(!empty($this->fromImage)){
            $data = $this->invoice->data ?: [];
            $this->validate([
               'fromImage' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5048',
            ]);
            storageDelete('media/invoices', ao($this->invoice->payer, 'image'));
            
 
            $data['image'] = str_replace('media/invoices/', "", $this->fromImage->storePublicly('media/invoices', sandy_filesystem('media')));
             
            $this->fromImage = null;
         }
 
         $this->invoice->data = $data;
         $this->invoice->save();
         $this->_refresh();
       },
    ]);

    rules(fn() => [
        // 'shorten.link' => 'required|url',
    ]);

    mount(function(){
        $this->_refresh();
    });

    $_refresh = function(){
        $user_id = $this->user->id;
        if (!$this->invoice = Invoice::where('user_id', $this->user->id)->where('slug', $this->slug)->first()) {
            abort(404);
        }

        $records = \Cache::remember("invoiceRecordModel:{$this->invoice->id}", 900, function () use($user_id) {
            return Invoice::orderBy('id', 'asc')->where('user_id', $user_id)->get();
        });

        $this->currentInvoiceNumber = null;
        foreach ($records as $index => $record) {
            // Check if this is the current invoice
            if ($record->id == $this->invoice->id) {
                // Format the number to have leading zeros
                $this->currentInvoiceNumber = str_pad($index + 1, 4, '0', STR_PAD_LEFT);
                break;
            }
        }

        $this->invoiceArray = $this->invoice->toArray();
    };

    $removePayerImage = function(){
        $payer = $this->invoice->payer ?: [];
        
        storageDelete('media/invoices', ao($this->invoice->payer, 'image'));
        $payer['image'] = null;
        $this->payerImage = null;
        $this->invoice->payer = $payer;
        $this->invoice->save();
        $this->_refresh();
    };

    $removeFromImage = function(){
        $data = $this->invoice->data ?: [];
        
        storageDelete('media/invoices', ao($this->invoice->data, 'image'));
        $data['image'] = null;
        $this->fromImage = null;
        $this->invoice->data = $data;
        $this->invoice->save();
        $this->_refresh();
    };

    $getAnalytics = function(){
        $this->visitors = $this->shorten->getInsight();
    };

    $save = function(){
        $this->validate();
        $this->shorten->save();
        
        $this->flashToast('success', __('Link saved'));
    };

    $saveJs = function(){
        $this->invoice->fill($this->invoiceArray);
        $this->invoice->price = (float) str_replace(',', '.', $this->invoice->price);
        $this->invoice->save();
        
        $this->_refresh();
    };
?>
<div>
    <style>
        .yena-sidebar, .mobile-header-toolbar{
            display: none !important;
        }
        .yena-root-main, .yena-container{
            padding: 0 !important;
            margin: 0 !important;
            max-width: 100% !important;
        }
    </style>
  <div>
    <div x-data="console__edit_invoice_shortener">
        <div>
            
            
            <form class="flex h-full overflow-hidden tracking-normal">
                <div class="relative flex-1">
                    <div class="w-full border-0 border-b border-solid border-gray-500">
                        <div class="w-full">
                            <span class="relative overflow-hidden block z-0 bg-[rgba(28,_28,_28,_0.133)] h-[8px]">
                                <span class="absolute left-0 bottom-0 top-0 [transition:transform_0.4s_linear] origin-[left_center] bg-black" :style="{
                                    'width': getPercentageBar
                                }"></span>
                            </span>
                        </div>
                        <div class="flex items-center justify-between px-6 py-4">
                            <button class="yena-button-o" type="button" :class="{
                                '!opacity-0 !pointer-event-none !cursor-default': step == 'from'
                            }" @click="previousStep">
                                <i class="ph ph-caret-left text-xl"></i>
                            </button>
                            <div class="text-center">
                                <div class="text-xs leading-[1.4] tracking-[-.42px] uppercase text-gray-600 text-center">{{ __('Edit invoice') }}</div>
                                
                                
                                <div class="font-bold leading-[1.1] tracking-[-.42px]" x-cloak>
                                    <span x-show="step=='from'">{{ __('1. Who are you?') }}</span>
                                    <span x-show="step=='to'">{{ __('2. Who is it for?') }}</span>
                                    <span x-show="step=='what'">{{ __('3. What is it for?') }}</span>
                                    <span x-show="step=='due'">{{ __('4. Due date and details') }}</span>
                                </div>
                            </div>
                            <a class="yena-button-o" href="{{ route('console-invoicing-index') }}">
                                <i class="ph ph-x text-xl"></i>
                            </a>
                        </div>
                    </div>
                    <div class="w-full max-w-[100vw] overflow-y-auto h-[calc(-86px_+_100vh)] bg-white">
                        <div class="pb-[150px]">
                            <div x-show="step=='from'" x-cloak>
                                <div class="mx-auto mt-6 max-w-[480px] p-4 pb-16 lg:!m-auto lg:!max-w-[544px]">
                                    <div class="mb-4">
                                        <div class="flex items-center flex-row gap-[var(--yena-space-3)] w-full h-[var(--yena-sizes-10)] my-4">
                                            <span class="flex-shrink-0 text-[var(--yena-colors-gray-600)]">{{ __('Payer details') }}</span>
                                            <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
                                        </div>
    
                                         <div class="grid grid-cols-2 gap-2">
                                            <div class="form-input">
                                                <input type="text" x-model="invoice.data.name" placeholder="{{ __('Your name') }}">
                                            </div>
                                            <div class="form-input">
                                                <input type="email" x-model="invoice.data.email" placeholder="{{ __('Your email') }}">
                                            </div>
                                            <div class="form-input col-span-2">
                                                <input type="text" x-model="invoice.data.billing" placeholder="{{ __('Billing address') }}">
                                            </div>
                                         </div>
                                         <div class="flex items-center flex-row gap-[var(--yena-space-3)] w-full h-[var(--yena-sizes-10)] my-4">
                                             <span class="flex-shrink-0 text-[var(--yena-colors-gray-600)]">{{ __('Add your logo / avatar') }}</span>
                                             <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
                                         </div>
                                         @php
                                            $thumb = gs('media/invoices', ao($invoice->data, 'image'));
                                            $ht = false;
                       
                                            if($fromImage){
                                                  $thumb = $fromImage->temporaryUrl();
                                                  $ht = true;
                                            }else{
                                                  if(!empty(ao($invoice->data, 'image'))){
                                                     $ht = true;
                                                  }
                                            }
                                         @endphp
                                         <div class="group block h-20 bg-white- cursor-pointer rounded-lg border-2 border-dashed {{ !$ht ? 'border-gray-200' : 'border-transparent' }} text-center hover:border-solid hover:border-yellow-600 relative">
                                            @if ($ht)
                                            <div class="flex w-full h-full items-center justify-center absolute right-0 top-0 left-0 bottom-0 z-50">
                                                  <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center icon-shadow" @click="$wire.removeFromImage()">
                                                     <i class="fi fi-rr-trash"></i>
                                                  </div>
                                            </div>
                                            @endif
                                            @if (!$ht)
                                            <input type="file" wire:model="fromImage" class="opacity-0 h-full w-full absolute right-0 top-0 cursor-pointer">
                       
                                            <div class="w-full h-full flex items-center justify-center">
                                               <div wire:loading wire:target="fromImage">
                                                  <span class="loader-line-dot-dot !text-black !text-[2px] -mt-2 m-0"></span>
                                               </div>
                                               <i class="fi fi-ss-plus" wire:loading.class="!hidden" wire:target="fromImage"></i>
                                            </div>
                                            @endif
                                            @if ($ht)
                                                  <div class="h-full w-full">
                                                     <img src="{{ $thumb }}" class="h-full w-full object-contain rounded-md" alt="">
                                                  </div>
                                            @endif
                                         </div>
                                    </div>
                                </div>
                            </div>
                            <div x-show="step=='to'" x-cloak>
                                <div class="mx-auto mt-6 max-w-[480px] p-4 pb-16 lg:!m-auto lg:!max-w-[544px]">
                                    <div class="mb-4">
                                        <div class="flex items-center flex-row gap-[var(--yena-space-3)] w-full h-[var(--yena-sizes-10)] my-4">
                                            <span class="flex-shrink-0 text-[var(--yena-colors-gray-600)]">{{ __('Payer details') }}</span>
                                            <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
                                        </div>
    
                                         <div class="grid grid-cols-2 gap-2">
                                            <div class="form-input">
                                                <input type="text" x-model="invoice.payer.name" placeholder="{{ __('Your name') }}">
                                            </div>
                                            <div class="form-input">
                                                <input type="email" x-model="invoice.payer.email" placeholder="{{ __('Your email') }}">
                                            </div>
                                            <div class="form-input col-span-2">
                                                <input type="text" x-model="invoice.payer.billing" placeholder="{{ __('Billing address') }}">
                                            </div>
                                         </div>
                                         <div class="flex items-center flex-row gap-[var(--yena-space-3)] w-full h-[var(--yena-sizes-10)] my-4">
                                             <span class="flex-shrink-0 text-[var(--yena-colors-gray-600)]">{{ __('Add their brand logo') }}</span>
                                             <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
                                         </div>
                                         @php
                                            $thumb = gs('media/invoices', ao($invoice->payer, 'image'));
                                            $ht = false;
                       
                                            if($payerImage){
                                                  $thumb = $payerImage->temporaryUrl();
                                                  $ht = true;
                                            }else{
                                                  if(!empty(ao($invoice->payer, 'image'))){
                                                     $ht = true;
                                                  }
                                            }
                                         @endphp
                                         <div class="group block h-20 bg-white- cursor-pointer rounded-lg border-2 border-dashed {{ !$ht ? 'border-gray-200' : 'border-transparent' }} text-center hover:border-solid hover:border-yellow-600 relative">
                                            @if ($ht)
                                            <div class="group-hover:flex hidden w-full h-full items-center justify-center absolute right-0 top-0 left-0 bottom-0 z-50">
                                                  <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center icon-shadow" @click="$wire.removePayerImage()">
                                                     <i class="fi fi-rr-trash"></i>
                                                  </div>
                                            </div>
                                            @endif
                                            @if (!$ht)
                                            <input type="file" wire:model="payerImage" class="opacity-0 h-full w-full absolute right-0 top-0 cursor-pointer">
                       
                                            <div class="w-full h-full flex items-center justify-center">
                                               <div wire:loading wire:target="payerImage">
                                                  <span class="loader-line-dot-dot !text-black !text-[2px] -mt-2 m-0"></span>
                                               </div>
                                               <i class="fi fi-ss-plus" wire:loading.class="!hidden" wire:target="payerImage"></i>
                                            </div>
                                            @endif
                                            @if ($ht)
                                                  <div class="h-full w-full">
                                                     <img src="{{ $thumb }}" class="h-full w-full object-contain rounded-md" alt="">
                                                  </div>
                                            @endif
                                         </div>
                                    </div>
                                </div>
                            </div>
                            <div x-show="step=='what'" x-cloak>
                                <div class="mx-auto mt-6 max-w-[480px] p-4 pb-16 lg:!m-auto lg:!max-w-[544px]">
                                    <div class="mb-4">
                                        <div class="flex items-center flex-row gap-[var(--yena-space-3)] w-full h-[var(--yena-sizes-10)] my-4">
                                            <span class="flex-shrink-0 text-[var(--yena-colors-gray-600)]">{{ __('Invoice amount') }}</span>
                                            <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
                                        </div>
                                        <div class="custom-content-input border-2 border-dashed">
                                           <label class="h-10 !flex items-center px-5">
                                            {!! ao($currency, 'code') !!}
                                           </label>
                                           <input type="text" x-model="invoice.price" placeholder="{{ __('Amount') }}" class="w-[100%] !bg-gray-100">
                                        </div>
                                        <div class="flex items-center flex-row gap-[var(--yena-space-3)] w-full h-[var(--yena-sizes-10)] my-4">
                                            <span class="flex-shrink-0 text-[var(--yena-colors-gray-600)]">{{ __('Description') }}</span>
                                            <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
                                        </div>
                                        <div class="form-input">
                                            <textarea x-model="invoice.data.item_description" cols="30" rows="10"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div x-show="step=='due'" x-cloak>
                                <div class="mx-auto mt-6 max-w-[480px] p-4 pb-16 lg:!m-auto lg:!max-w-[544px]">
                                    <div class="mb-4">
                                        <div class="flex items-center flex-row gap-[var(--yena-space-3)] w-full h-[var(--yena-sizes-10)] my-4">
                                            <span class="flex-shrink-0 text-[var(--yena-colors-gray-600)]">{{ __('Invoice due date') }}</span>
                                            <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
                                        </div>
                                        <div class="flex items-center gap-3 mb-3">
                                            <button class="yena-button-o !bg-[var(--yena-colors-trueblue-50)] !rounded-full !text-black" :class="{
                                                '!bg-black !text-white': selectedPresetDue == 30
                                            }" type="button" @click="addDue(30)">
                                                {{ __('in 30 days') }}
                                             </button>
                                             <button class="yena-button-o !bg-[var(--yena-colors-trueblue-50)] !rounded-full !text-black" :class="{
                                                 '!bg-black !text-white': selectedPresetDue == 45
                                             }" type="button" @click="addDue(45)">
                                                 {{ __('in 45 days') }}
                                              </button>
                                              <button class="yena-button-o !bg-[var(--yena-colors-trueblue-50)] !rounded-full !text-black" :class="{
                                                  '!bg-black !text-white': selectedPresetDue == 60
                                              }" type="button" @click="addDue(60)">
                                                  {{ __('in 60 days') }}
                                               </button>
                                               <button class="yena-button-o !bg-[var(--yena-colors-trueblue-50)] !rounded-full !text-black" :class="{
                                                   '!bg-black !text-white': selectedPresetDue == 90
                                               }" type="button" @click="addDue(90)">
                                                   {{ __('in 90 days') }}
                                                </button>
                                        </div>
                                        <div class="form-input">
                                            <input type="date" @input="selectedPresetDue=null" x-model="invoice.due" placeholder="{{ __('Your due date') }}">
                                        </div>
                                        <div class="flex items-center flex-row gap-[var(--yena-space-3)] w-full h-[var(--yena-sizes-10)] my-4">
                                            <span class="flex-shrink-0 text-[var(--yena-colors-gray-600)]">{{ __('Add a message (optional)') }}</span>
                                            <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
                                        </div>
                                        <div class="form-input">
                                            <textarea x-model="invoice.data.message" cols="30" rows="10"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div x-show="step=='final'" x-cloak>
                                <div class="mx-auto mt-6 max-w-[480px] p-4 pb-16 lg:!m-auto lg:!max-w-[544px]">
                                    <div class="mb-4">
                                        <div>
                                           <div>
                                               <div class="flex flex-col justify-center items-start px-0 py-[60px]">
                                                 {!! __i('--ie', 'checkmark-done-check', 'w-14 h-14') !!}
                                                 <p class="mt-3 text-base text-left md:!text-2xl font-bold">
                                                    {!! __t('Your invoice is done and ready to receive payment.') !!}
                                                 </p>
                                                 <a class="yena-black-btn gap-2 mt-2 cursor-pointer" href="{{ route('out-invoice-single', ['slug' => $invoice->slug]) }}" target="_blank">{{ __('View invoice') }}</a>
                                               </div>
                                           </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="absolute bottom-0 z-30 w-full border-0 border-t border-solid border-gray-500 bg-white py-4">
                        <div class="mx-4 max-w-[480px] lg:!m-auto">
                            <button class="yena-black-btn !w-full !rounded-full relative" type="button" @click="step=='final' ? window.location.replace('{{ route('console-invoicing-index') }}') : nextStep">
                                <div class="flex w-full items-center justify-center gap-2 text-md-bold">
                                    <span x-show="step!=='final'" x-cloak>
                                        {{ __('Next Step') }}
                                    </span>
                                    <span x-show="step=='final'" x-cloak>
                                        {{ __('Go home') }}
                                    </span>

                                    <span x-show="savingState" x-cloak>
                                        <span class="loader-line-dot-dot !text-white !text-[2px] !mt-2 !ml-2"></span>
                                    </span>
                                    {{-- <i class="ph ph-caret-right"></i> --}}
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="max-h-screen flex-1 overflow-y-scroll bg-black !hidden lg:!block">
                <div class="mx-auto min-h-screen max-w-md py-9 pb-[180px] lg:!min-w-[400px] lg:!pb-16 lg:!pt-3">
                    <h3 class="mb-0 text-center text-12 font-semibold uppercase text-gray-600">{{ __('Preview') }}</h3>
                    <div class="overflow-hidden min-h-[48px] flex mb-[10px]">
                        <div class="relative inline-block flex-auto whitespace-nowrap overflow-hidden w-full">
                            <div class="flex justify-center" role="tablist">
                                <button class="max-w-[360px] min-w-[90px] relative min-h-[48px] flex-shrink-0 px-[16px] py-[12px] overflow-hidden whitespace-normal text-center flex-col text-12 font-semibold uppercase text-center" :class="{
                                    'border-b-2 border-solid border-white !text-white': preview=='pdf',
                                    '!text-gray-600': preview!=='pdf',
                                }" type="button" @click="preview='pdf'">{{ __('PDF') }}</button>
                                
                                <button class="max-w-[360px] min-w-[90px] relative min-h-[48px] flex-shrink-0 px-[16px] py-[12px] overflow-hidden whitespace-normal text-center flex-col text-12 font-semibold uppercase text-center !text-gray-600" :class="{
                                    'border-b-2 border-solid border-white !text-white': preview=='payment',
                                    '!text-gray-600': preview!=='payment',
                                }" type="button" @click="preview='payment'">{{ __('Payment page') }}</button>
                                
                                <button class="max-w-[360px] min-w-[90px] relative min-h-[48px] flex-shrink-0 px-[16px] py-[12px] overflow-hidden whitespace-normal text-center flex-col text-12 font-semibold uppercase text-center !text-gray-600" :class="{
                                    'border-b-2 border-solid border-white !text-white': preview=='email',
                                    '!text-gray-600': preview!=='email',
                                }" type="button" @click="preview='email'">{{ __('Email') }}</button>
                            </div>
                        </div>
                    </div>
                    <div class="pointer-events-none">
                        <template x-if="preview=='pdf'">
                            <x-livewire::components.console.invoicing.sections.pdf />
                        </template>
                        <template x-if="preview=='payment'">
                            <x-livewire::components.console.invoicing.sections.payment />
                        </template>
                        <template x-if="preview=='email'">
                            <x-livewire::components.console.invoicing.sections.email />
                        </template>
                    </div>
                </div>
                </div>
            </form>

        </div>
    </div>
    @script
      <script>
          Alpine.data('console__edit_invoice_shortener', () => {
            return {
                autoSaveTimer: null,
                savingState: 0,
                step: 'from',
                steps: ['from', 'to', 'what', 'due', 'final'],
                preview: 'pdf',
                data: @entangle('data'),
                currentInvoiceNumber: @entangle('currentInvoiceNumber'),
                invoice: @entangle('invoiceArray'),
                currency: @entangle('currency'),
                selectedPresetDue: null,
                gs: '{{ gs('media/invoices') }}',

                getMedia(media){
                    return this.gs +'/'+ media;
                },

                previousStep(){
                    const totalSteps = this.steps.length;
                    const currentIndex = this.steps.indexOf(this.step);

                    if(currentIndex == 0) return;

                    let nextStep = this.steps[currentIndex - 1];
                    this.step = nextStep;
                },
                nextStep(){
                    const totalSteps = this.steps.length;
                    const currentIndex = this.steps.indexOf(this.step);

                    let nextStep = this.steps[currentIndex + 1];
                    this.step = nextStep;
                },
                addDue(days = 30){
                    let currentDate = new Date();
                    currentDate.setDate(currentDate.getDate() + days);

                    // Convert back to a string in yyyy-mm-dd format
                    let year = currentDate.getFullYear();
                    let month = ('0' + (currentDate.getMonth() + 1)).slice(-2); // Months are zero-based
                    let day = ('0' + currentDate.getDate()).slice(-2);
                    this.selectedPresetDue = days;

                    this.invoice.due = `${year}-${month}-${day}`;
                },
                get getPercentageBar(){
                    const totalSteps = this.steps.length;
                    const currentIndex = this.steps.indexOf(this.step);
                    
                    if (currentIndex === -1) {
                        return "Invalid step";
                    }

                    const percentage = Math.max(25, (currentIndex / (totalSteps - 1)) * 100);
                    return `${percentage}%`;
                },

                init(){
                  let $this = this;

                  $this.$watch('invoice', (value) => {
                    clearTimeout($this.autoSaveTimer);

                    $this.autoSaveTimer = setTimeout(function(){
                        $this.savingState = 1;
                        $this.$wire.saveJs().then(r => {
                            $this.savingState = 0;
                        });
                    }, 2000);
                  });
                },
            }
          });
      </script>
    @endscript
 </div>
</div>