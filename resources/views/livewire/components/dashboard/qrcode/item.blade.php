
<?php
   use App\Models\Qrcode;

   use function Livewire\Volt\{state, mount, placeholder, on};

   mount(function(){
      
   });

    on([
        'qrUpdated.{item.id}' => function(){
            $this->item = Qrcode::find($this->item->id);
        }
    ]);

   placeholder('<div class="--placeholder-skeleton w-[230px] h-[240px] rounded-[var(--yena-radii-sm)]"></div>');

   state([
      'item',
   ]);
?>

<div>
   
    <div class="yena-linkbox !shadow-none" x-data="{is_delete:false}">
        <div class="card-button flex gap-2 p-2 !bg-[#f7f3f2]" x-cloak @click="$event.stopPropagation();" :class="{
           '!hidden': !is_delete
          }">
           <button class="btn btn-medium neutral !h-8 !flex !items-center !justify-center !rounded-full !text-black !bg-white" type="button" @click="is_delete=!is_delete">{{ __('Cancel') }}</button>

           <button class="btn btn-medium !bg-[var(--c-red)] !text-[var(--c-light)] !h-8 !flex !items-center !justify-center !rounded-full w-full" @click="$wire.$parent.deleteQr('{{ $item->id }}'); is_delete=false;">{{ __('Yes, Delete') }}</button>
        </div>

        <div class="absolute z-[99] top-[20px] right-[20px] bg-[#fff] p-[6px] rounded-[10px] flex gap-2" :class="{
           '!hidden': is_delete
        }">
           <div class="bg-[#f3f3f3] w-8 h-8 flex items-center justify-center rounded-full cursor-pointer" @click="$dispatch('open-modal', 'edit-qrcode-modal'); $dispatch('registerQr', {qr_id: '{{ $item->id }}'})">
              {!! __i('interface-essential', 'pen-edit.5', 'w-4 h-4 text-black') !!}
           </div>
           <div class="bg-[#f3f3f3] w-8 h-8 flex items-center justify-center rounded-full cursor-pointer" @click="$event.stopPropagation(); is_delete=true;">
              {!! __i('interface-essential', 'trash-delete-remove', 'w-4 h-4 text-red-400') !!}
           </div>
        </div>
        <div class="flex flex-col h-full">
           <div class="w-full">
              <div class="-thumbnail">
                 <div class="--thumbnail-inner [&_svg]:!h-[8rem] [&_svg]:!w-[100%]">
                    <img src="data:image/png;base64,{!! $item->convertQrcode() !!}" alt="">
                 </div>
              </div>
           </div>
  
           <div class="-content">
              <a claas="--over-lay">
                 <p class="--title !h-auto !text-sm">{{ $item->text }}</p>
              </a>
  
              <div class="flex flex-row gap-2 mt-0 w-[100%]" :class="{
                 '!hidden': is_delete
                }">
                 <button class="yena-button-stack !rounded-md !w-full" @click="$wire.$parent.download('{{ $item->id }}')">{{ __('Download') }}</button>
              </div>
           </div>
        </div>
     </div>
</div>