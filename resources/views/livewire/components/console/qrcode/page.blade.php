
<?php

   use App\Livewire\Actions\ToastUp;
   use App\Models\Qrcode;
   use function Livewire\Volt\{state, mount, placeholder, on, with, rules, usesPagination, uses, updated};

   uses([ToastUp::class]);
   
   // usesPagination();

   mount(function(){
      $this->get();
   });

   placeholder('placeholders.console.sites.page-placeholder');

   state([
      'codes' => [],
   ]);

   on([
      'refreshQrCodes' => function(){
         $this->get();
      },
      'close' => function(){
         $this->get();
      },
   ]);

   $get = function(){
      $this->codes = Qrcode::where('user_id', iam()->id)->orderBy('id', 'DESC')->get();
   };

   $download = function($id){

      if(!$qrcode = Qrcode::where('id', $id)->where('user_id', iam()->id)->first()) return;

      $data = $qrcode->processQrDownload();
      $filename = str()->random(10).'_qr.png';
      return response()->streamDownload(function () use($data, $filename) {
         echo base64_decode($data);
      }, $filename);
   };

   $deleteQr = function($id){
      Qrcode::where('user_id', iam()->id)->where('id', $id)->delete();
      $this->get();
   };
   
?>

<div>
   
   <div x-data="app_qrcode">

      
      <div class="banner">
         <div class="banner__container !bg-white">
           <div class="banner__preview">
             {!! __icon('shopping-ecommerce', 'Qr code.1') !!}
           </div>
           <div class="banner__wrap">
             <div class="banner__title h3 !text-black">{{ __('Qr Code') }}</div>
             <div class="banner__text !text-black">{{ __('Create advanced qrcode for any link or text') }}</div>
             
             <div class="mt-7 grid grid-cols-1 gap-1">
                 <div class="rounded-8 p-2 text-center col-span-2 lg:col-span-1 bg-[#f7f3f2]">
                    <div class="detail text-gray-600">{{ __('Total Qr') }}</div>
                    <div class="number-secondary text-black">{{ $codes->count() }}</div>
                 </div>
              </div>
             <div class="mt-3 flex gap-2">
                 <button class="yena-button-stack !rounded-full" @click="$dispatch('open-modal', 'create-qrcode-modal');">{{ __('Create Qr') }}</button>
             </div>
           </div>
         </div>
      </div>
       
   
      <div class="">

         <div class="page-trans">

      
            <ul class="col-span-1 grid auto-rows-min grid-cols-1 md:!grid-cols-3 lg:!grid-cols-4 gap-3">
               @foreach ($codes as $key => $item)
               <livewire:components.console.qrcode.item lazy :$item :key="uukey('sites', 'qrcodeitem-comp-' . $item->id)"/>
               @endforeach
            </ul>
            {{-- {{ $codes->links() }} --}}
         </div>
      </div>
      
      <template x-teleport="body">
         <x-modal name="create-qrcode-modal" :show="false" removeoverflow="true" maxWidth="xl" >
            <livewire:components.console.qrcode.create-modal lazy :key="uukey('app', 'qrcode-page-create')">
         </x-modal>
      </template>
      
      <template x-teleport="body">
         <x-modal name="edit-qrcode-modal" :show="false" removeoverflow="true" maxWidth="xl" >
            <livewire:components.console.qrcode.edit-modal zzlazy :key="uukey('app', 'qrcode-page-edit')">
         </x-modal>
      </template>

   </div>
   @script
   <script>
       Alpine.data('app_qrcode', () => {
          return {
            init(){
               var $this = this;

                document.addEventListener('alpine:navigated', (e) => {
                   $this.$wire.get();
                });
            }
          }
       });
   </script>
   @endscript
</div>