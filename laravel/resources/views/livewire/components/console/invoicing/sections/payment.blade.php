<div>

    <div class="w-[100%]">
        <div class="mx-2 mb-2 rounded-8 bg-white p-6 text-center lg:!mx-auto">
           <div class="detail mb-2 text-xs leading-[1.1] text-center">{{ __('From') }}</div>
            <a href="#" rel="noopener noreferrer" target="_blank">
             <img class="h-24 w-24 rounded-full mx-auto object-cover" alt=" " :src="getMedia(invoice.data.image)">
            </a>
           <div class="my-3 text-center">
              <div class="title mb-2 text-16 text-center" x-text="invoice.data.name"></div>
              <a class="justify-center text-gray-600 text-center" x-text="invoice.data.email" :href="'mailto:' + invoice.data.email"></a>
           </div>
        </div>
        <div class="mx-2 mb-2 rounded-8 bg-white p-6 lg:!mx-auto">
           <div class="detail text-xs leading-[1.1] text-center">{{ __('To') }}</div>
           <template x-if="invoice.payer.image">
                <div class="mx-auto mt-2 h-10 w-10 overflow-hidden rounded-4">
                    <img class="h-full w-[100%] rounded-4 mx-auto object-cover" alt=" " :src="getMedia(invoice.payer.image)">
                </div>
           </template>
           <div class="my-4 text-center">
                <div class="font-bold text-center" x-text="invoice.payer.name"></div>
                <a class="justify-center text-gray-600 text-center" x-text="invoice.payer.email" :href="'mailto:' + invoice.payer.email"></a>
                <div class="mt-1 text-gray-600 text-center" x-text="invoice.payer.billing"></div>
           </div>
           <div class="flex flex-row items-center justify-between border border-solid border-gray-200 px-6 py-6 text-18">
              <div class="text-gray-600" x-text="invoice.data.item_description"></div>
              <div class="mx-2"></div>
              <div class="font-bold" x-html="currency.code + invoice.price"></div>
           </div>
           <div class="my-4 text-center">
                <template x-if="!invoice.paid">
                    <div class="font-bold text-center" x-text="'{{ __('Due in') }}' + ' ' + moment(invoice.due).diff(moment(), 'days') +' '+ '{{ __('days') }}'"></div>
                </template>
              <div class="detail text-gray-600 text-center" x-text="moment(invoice.due).format('MMM DD, YYYY')"></div>
           </div>
           <template x-if="invoice.data.message">
            <div class="mt-2 text-gray-600 text-center" x-text="invoice.data.message"></div>
           </template>
           <template x-if="!invoice.paid">
            <button class="yena-black-btn !w-[100%] text-center !justify-center mt-2" tabindex="0" type="button" @click="payInvoice">{{ __('Pay Invoice') }}</button>
           </template>
           @php
               $error = false;

               if(!$errors->isEmpty()){
                   $error = $errors->first();
               }

               if(Session::get('error._error')){
                   $error = Session::get('error._error');
               }
           @endphp
           @if ($error)
               <div class="mt-1 bg-red-200 font--11 p-1 px-2 rounded-md">
                   <div class="flex items-center">
                       <div>
                           <i class="fi fi-rr-cross-circle flex text-xs"></i>
                       </div>
                       <div class="flex-grow ml-1 text-xs">{{ str_replace('create.', '', $error) }}</div>
                   </div>
               </div>
           @endif
        </div>
        <div class="mx-2 mb-2 rounded-8 bg-white p-6 text-center lg:!mx-auto">
           <div class="flex gap-3">
                <button class="yena-black-btn !w-[100%] text-center !justify-center" tabindex="0" type="button" @click="downloadInvoice">{{ __('Download Invoice') }}</button>
           </div>
        </div>
     </div>
</div>