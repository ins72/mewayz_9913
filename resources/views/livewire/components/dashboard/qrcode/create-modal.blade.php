
<?php
   use App\Livewire\Actions\ToastUp;
   use App\Models\Qrcode;
   use function Livewire\Volt\{state, mount, placeholder, uses, rules, updated, usesFileUploads};

   usesFileUploads();

   state([
      'text' => fn() => config('app.url'),
      'qr' => null,
   ]);

   rules(fn () => [
      'text' => 'required',
   ]);

   uses([ToastUp::class]);
   mount(function() {
      $this->generate();
   });

   placeholder('
   <div class="p-5 w-full">
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)]"></div>
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
   </div>');

   $save = function(){
      $this->validate();
      
      $extra = [];
      $items = ['eye_ball', 'eye_frame', 'dots', 'frames', 'logos'];

      foreach ($items as $item) {
         if (!isset($extra[$item])) {
            $extra[$item] = [
               'silence' => 'golden'
            ];
         }
      }

      $new = new Qrcode;
      $new->user_id = iam()->id;
      $new->extra = $extra;
      $new->text = $this->text;
      $new->save();


      $this->dispatch('refreshQrCodes');
      $this->dispatch('close');
   };

   $generate = function(){
      $options = [];
      $back_color = qrcdr()->hexdecColor('#ffffff', '#000000');
      $frontcolor = qrcdr()->hexdecColor('#000000', '#000000');

      $this->qr = \App\QrCode\SandyQrCode::svg($this->text, $outfile = false, $level = QR_ECLEVEL_Q, $size = 32, $margin = 4, $saveandprint = false, $back_color = 0xFFFFFF, $frontcolor, $options);
   };
?>


<div class="w-full">
   <div class="flex flex-col">
      <a x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-3 right-3 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
         <i class="fi fi-rr-cross text-sm"></i>
      </a>

      <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('Create Qrcode') }}</header>

      <hr class="yena-divider">

      <form wire:submit="save" class="px-8 pt-2 pb-6">
         <div class="qrcode-img-wrapper !h-64 !w-full">
            <div class="qrcode-img-content h-full w-full relative right-0 left-0 top-0 bottom-0">
                <div class="w-full h-full p-3">
                    {!! $qr !!}
                </div>
            </div>
        </div>
         <div class="yena-form-group" x-data="{scrollHeight:5}">
            <textarea type="text" :style="{
                'height': scrollHeight + 'px'
            }" @input="scrollHeight-0;scrollHeight=$event.target.scrollHeight" wire:model="text" placeholder="{{ __('Qrcode text... (e.g., link, text, etc)') }}" class="!px-[1rem] !rounded-lg !shadow-lg md:!text-[var(--yena-fontSizes-lg)] focus:!shadow-lg bg-white w-[100%] resize-none min-h-[60px] max-h-[300px]" style="height: 5px;"></textarea>
         </div>

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
            <div class="mt-5 bg-red-200 font--11 p-1 px-2 rounded-md">
                  <div class="flex items-center">
                     <div>
                        <i class="fi fi-rr-cross-circle flex text-xs"></i>
                     </div>
                     <div class="flex-grow ml-1 text-xs">{{ str_replace('create.', '', $error) }}</div>
                  </div>
            </div>
         @endif
         <button class="yena-button-stack mt-5 w-full">{{ __('Save') }}</button>
      </form>
   </div>
</div>