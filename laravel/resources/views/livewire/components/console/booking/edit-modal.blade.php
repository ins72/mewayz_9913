
<?php
   use App\Livewire\Actions\ToastUp;
   use App\Models\BookingService;
   use App\Models\Product;
   use App\Models\ProductOption;
   use App\Traits\AudienceTraits;
   use App\Yena\BookingTime;
   use function Livewire\Volt\{state, mount, placeholder, uses, on, rules, updated, usesFileUploads};

   usesFileUploads();
   
   on([
      'registerService' => function($id){
         $this->service_id = $id;
         $this->_get();
         $this->initWeekdays();
      },
   ]);
   updated([
      // 'featured_image' => function(){
      //    if(!empty($this->featured_image)){
      //       $this->validate([
      //          'featured_image' => 'image|mimes:jpeg,png,jpg,gif|max:5048'
      //       ]);
      //       $filesystem = sandy_filesystem('media/booking/image');
      //       storageDelete('media/booking/image', $this->service->featured_img);


      //       $avatar = $this->featured_image->storePublicly('media/booking/image', $filesystem);
      //       $avatar = str_replace("media/booking/image/", "", $avatar);
      //       $this->service->featured_img = $avatar;
      //       $this->service->save();
      //       $this->get();

      //       $this->dispatch('productUpdated');
      //    }
      // },
      'image' => function(){
         $this->skipRender();
         $this->validate([
               'image.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:5048'
         ]);

         $filesystem = sandy_filesystem('media/booking/image');
         $images = $this->service->gallery ?: [];
         
         foreach ($this->image as $photo) {
            $image = $photo->storePublicly('media/booking/image', $filesystem);
            $image = str_replace("media/booking/image/", "", $image);
            
            $images[] = $image;
         }

         $this->service->gallery = $images;
         $this->service->save();
         $this->_get();

         $this->dispatch('serviceUpdated');
      },
   ]);

   uses([ToastUp::class]);

   state([
      'featured_image' => null,
      'image' => null,
   ]);

   state([
       'user' => fn() => iam(),
   ]);

   state([
      'service' => fn() => new BookingService,
      'service_id' => null,
      'serviceArray' => [],
      'weekdays' => [],
   ]);

   rules(fn () => [
        'service.name' => 'required',
        'service.description' => '',
        'service.duration' => '',
        'service.price' => '',
        'service.booking_time_interval' => '',
        
        'weekdays.*.enable' => '',
        'weekdays.*.start_time' => '',
        'weekdays.*.end_time' => '',
   ]);
   mount(function(){
      // $this->_get();
   });

   placeholder('
   <div class="p-5 w-full mt-1">
      <div class="flex mb-2 gap-4">
         <div>
            <div class="--placeholder-skeleton w-[200px] h-[200px] rounded-3xl"></div>
         </div>
         <div class="flex flex-col gap-2 w-full">
            <div class="--placeholder-skeleton w-full h-[20px] rounded-[var(--yena-radii-sm)] mt-1"></div>
            <div class="--placeholder-skeleton w-full h-[20px] rounded-[var(--yena-radii-sm)] mt-1"></div>
            <div class="--placeholder-skeleton w-full h-[20px] rounded-[var(--yena-radii-sm)] mt-1"></div>
            <div class="--placeholder-skeleton w-[150px] h-[40px] rounded-full mt-5"></div>
         </div>
      </div>
      
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)]"></div>
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
   </div>');


   $initWeekdays = function(){
      $timeClass = new BookingTime($this->user->id);
      $times = $timeClass->get_days_array();

      foreach ($times as $key => $value) {
         try {
            $workhours = $this->service->booking_workhours ?? $this->user->booking_workhours;
            $start_time = date('H:i', mktime(0, ao($workhours, "$key.from")));
            $end_time = date('H:i', mktime(0, ao($workhours, "$key.to")));
         } catch (\Throwable $th) {
            $start_time = \Carbon\Carbon::now()->format('H:i');
            $end_time = \Carbon\Carbon::now()->format('H:i');
         }

         $enable = ao($this->service->booking_workhours, "$key.enable") ?? ao($this->user->booking_workhours, "$key.enable");

         $this->weekdays[$key] = [
            'enable' => $enable,
            'day_name' => $value,
            'start_time' => $start_time,
            'end_time' => $end_time,
            // 'processedTime' => $timeClass->minutes_to_hours_and_minutes()
         ];
      }
   };

   $_get = function($id = null){
      $this->service = BookingService::where('user_id', iam()->id);

      if($this->service_id){
         $this->service = $this->service->where('id', $this->service_id);
      }

      $this->service = $this->service->orderBy('id', 'desc')->first();
      $this->serviceArray = $this->service->toArray();
   };

   $deleteMedia = function($index){
      $media = $this->service->gallery;
      if(!is_array($media)) return;

      if(!in_array($index, $media)) return;

      
      storageDelete('media/booking/image', $index);
      // unset($media[$index]);

      foreach ($media as $key => $value) {
         if($value == $index) unset($media[$key]);
      }

      $media = array_values($media);

      $this->service->media = $media;
      $this->service->save();
      $this->_get();

      $this->dispatch('serviceUpdated');
   };

   $save = function($audience){
      $hours = $this->service->booking_workhours;
      foreach ($this->weekdays as $key => $value) {
          $value['from'] = hour2min(\Carbon\Carbon::parse($value['start_time'])->format('H:i A'));
          $value['to'] = hour2min(\Carbon\Carbon::parse($value['end_time'])->format('H:i A'));
          $hours[$key] = $value;
      }
      $this->service->booking_workhours = $hours;
      $this->service->save();
      $this->initWeekdays();
      
      $this->flashToast('success', __('Service saved'));
      $this->dispatch('serviceUpdated');
   };
?>

<div class="w-full">
   <div x-data="service_editing">
      <div class="flex flex-col">
         <a x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-3 right-3 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
            <i class="fi fi-rr-cross text-sm"></i>
         </a>
   
         <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('Edit Service') }}</header>
   
         <hr class="yena-divider">
   
         <form @submit.prevent="save" class="px-8 pt-2 pb-6">
   
            <div class="settings__upload" data-generic-preview>
               {{-- <div class="settings__preview !bg-[#f3f3f3] flex items-center justify-center !h-[8rem] !w-[8rem] !rounded-full overflow-hidden">
                  <input class="settings__input z-50" type="file" wire:model="featured_image">

                  @php
                      $_avatar = false;
                      if($product->featured_img){
                        $_avatar = $product->getFeaturedImage();
                      }
                      
                      if($featured_image) $_avatar = $featured_image->temporaryUrl();
                  @endphp
   
                  {!! __i('--ie', 'image-picture', 'w-8 h-8 z-[99] bg-[#fff] rounded-[550px] p-[7px] text-[#000]') !!}
                  @if ($_avatar)
                     <img src="{{ $_avatar }}" alt="">
                  @endif
               </div> --}}
               <div class="settings__wrap">


                 <div class="text-[1.5rem] leading-10 font-bold">{{ $service->name }}</div>
                 <div class="settings__content flex items-center gap-2">{{ __('Created on :date', ['date' => \Carbon\Carbon::parse($service->created_at)->toFormattedDateString()]) }}</div>
                 <div class="flex gap-2">
                     <a @click="_page='-'" class="yena-button-stack --primary !text-xs !h-8">
                        {{ __('Manage') }}
                     </a>
                     <a @click="_page='gallery'" class="yena-button-stack --primary !text-xs !h-8">
                        {{ __('Gallery') }}
                     </a>
                 </div>
               </div>
            </div>
            <div class="flex flex-col mt-4">
               <div x-show="_page=='-'" x-cloak>
                  <div class="flex flex-col gap-4">
                     <div class="flex flex-col gap-3 mb-2">
                        <div class="form-input">
                           <input type="text" name="name" wire:model="service.name" placeholder="{{ __('Name') }}">
                        </div>
                        <div
                           x-ref="quillEditor"
                           x-init="
                              quill = new window.Quill($refs.quillEditor, {theme: 'snow'});
                              quill.on('text-change', function () {
                                 description = quill.root.innerHTML;
                              });
      
                              quill.root.innerHTML = description;
                           "
                        >
                        
                        </div>
                     </div>
                     <div class="grid grid-cols-2 gap-4">
                        <div class="form-input">
                           <input type="number" placeholder="{{ __('Price') }}" name="price" wire:model="service.price">
                        </div>
                                 
                        <div class="custom-content-input border-2 border-dashed !m-0 max-h-[42px]">
                           <input type="text" wire:model="service.duration" placeholder="{{ __('Service Duration') }}" class="w-[100%] !bg-gray-100">
                           <label class="h-10 !flex items-center px-5 ![box-shadow:var(--yena-shadows-md)]">
                              {{ __('Min') }}
                           </label>
                        </div>
                     </div>
                     <div class="flex flex-col gap-2">
                        <div class="flex items-center justify-between">
                           <p class="font-bold">{{ __('Time Interval') }}</p>
                           <div class="p-[4px] flex items-center">
                               <span class="relative top-[1px]">
                                   <a class="cursor-pointer">
                                       {!! __i('--ie', 'alarm-clock-time-timer-fast.2', 'w-5 h-5 text-black') !!}
                                   </a>
                               </span>
                           </div>
                       </div>
                       <div class="grid grid-cols-1 md:!grid-cols-2 gap-3">
                           <button class="yena-button-o !h-auto !min-h-[38px] !items-center !p-1 !bg-[#ffffffa3] hover:border-[var(--yena-colors-purple-400)] border-2 border-solid [border-image:initial] border-[var(--yena-colors-transparent)] shadow-sm !text-left !justify-start gap-2 ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)] {{ $service->booking_time_interval == '15' ? '!border-[var(--yena-colors-purple-400)]' : '' }}" type="button" wire:click="$set('service.booking_time_interval', '15')">
                               <div>
                                   <div class="bg-[#f7f3f2] w-8 h-8 rounded-lg flex items-center justify-center">
                                       {!! __i('Photo Edit', 'Timer, 10 Seconds', 'w-5 h-5 text-black') !!}
                                   </div>
                               </div>
           
                               <div class="flex flex-col">
                                   <p class="text-sm font-bold text-[var(--yena-colors-gray-800)]">{{ __('15 Min') }}</p>
                               </div>
                           </button>
                           <button class="yena-button-o !h-auto !min-h-[38px] !items-center !p-1 !bg-[#ffffffa3] hover:border-[var(--yena-colors-purple-400)] border-2 border-solid [border-image:initial] border-[var(--yena-colors-transparent)] shadow-sm !text-left !justify-start gap-2 ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)] {{ $service->booking_time_interval == '45' ? '!border-[var(--yena-colors-purple-400)]' : '' }}" type="button" wire:click="$set('service.booking_time_interval', '45')">
                             <div>
                                <div class="bg-[#f7f3f2] w-8 h-8 rounded-lg flex items-center justify-center">
                                   {!! __i('Photo Edit', 'Timer, 10 Seconds', 'w-5 h-5 text-black') !!}
                                </div>
                             </div>
             
                             <div class="flex flex-col">
                               <p class="text-sm font-bold text-[var(--yena-colors-gray-800)]">{{ __('45 Min') }}</p>
                             </div>
                           </button>
                           <button class="yena-button-o !h-auto !min-h-[38px] !items-center !p-1 !bg-[#ffffffa3] hover:border-[var(--yena-colors-purple-400)] border-2 border-solid [border-image:initial] border-[var(--yena-colors-transparent)] shadow-sm !text-left !justify-start gap-2 ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)] col-span-2 {{ $service->booking_time_interval == '75' ? '!border-[var(--yena-colors-purple-400)]' : '' }}" type="button" wire:click="$set('service.booking_time_interval', '75')">
                             <div>
                                <div class="bg-[#f7f3f2] w-8 h-8 rounded-lg flex items-center justify-center">
                                   {!! __i('Photo Edit', 'Timer, 10 Seconds', 'w-5 h-5 text-black') !!}
                                </div>
                             </div>
             
                             <div class="flex flex-col">
                               <p class="text-sm font-bold text-[var(--yena-colors-gray-800)]">{{ __('75 Min') }}</p>
                             </div>
                           </button>
                       </div>
                     </div>

                     <div class="flex flex-col gap-2">
                        <div class="flex items-center justify-between">
                           <p class="font-bold">{{ __('Service Availability') }}</p>
                           <div class="p-[4px] flex items-center">
                               <span class="relative top-[1px]">
                                   <a class="cursor-pointer">
                                       {!! __i('--ie', 'alarm-clock-time-timer-fast.2', 'w-5 h-5 text-black') !!}
                                   </a>
                               </span>
                           </div>
                       </div>
                        <div>
                           @foreach($weekdays as $key => $weekday)
                              <div class="weekday-schedule-w !bg-transparent" x-data="{ enabled: @json($weekdays[$key]['enable']) }">
                                 <div class="ws-head-w">
                                       <label class="sandy-switch is-koon flex items-center col-span-2">
                                          <input class="sandy-switch-input" name="weekday[{{ $key }}][enable]" value="1" wire:model="weekdays.{{ $key }}.enable" @input="$event.target.checked ? enabled = true : enabled = false" type="checkbox">
                                          <span class="sandy-switch-in"><span class="sandy-switch-box"></span></span>
                                       </label>
                                       <div class="ws-head">
                                          <div class="ws-day-name flex flex-col">
                                             <span class="font-bold text-sm">{{ $weekday['day_name'] }}</span>
                                             <span class="text-xs">{{ $weekday['start_time'] }} - {{ $weekday['end_time'] }}</span>
                                          </div>
                                          <div class="bg-g-gray w-8 h-8 flex items-center justify-center rounded-full ml-auto wp-edit-icon !hidden">
                                             eee
                                             {{-- {!! orion('edit-1', 'w-3 h-3') !!} --}}
                                          </div>
                                       </div>
                                 </div>
                                 <div class="weekday-schedule-form" :class="{
                                       '!flex': enabled
                                 }">
                                       <div class="ws-period grid grid-cols-1 gap-4 !border-0 !bg-transparent !w-full">
                                          <div class="wj-time-group wj-time-input-w as-period border border-solid koon-grey-border-color rounded-xl px-5 ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)] !w-full">
                                             <label for="weekday[{{ $key }}][from]" class="font-normal">{{ __('Start') }}</label>
                     
                                             <div class="wj-time-input-fields">
                                                   <input type="time" placeholder="HH:MM" id="weekday[{{ $key }}][from]"
                                                      name="weekday[{{ $key }}][from]" wire:model="weekdays.{{ $key }}.start_time"
                                                      class="wj-form-control wj-mask-time hourpicker !max-w-full !w-44 text-xs rounded-full koon-grey-bg">
                                             </div>
                                          </div>
                     
                                          <div class="wj-time-group wj-time-input-w as-period border border-solid koon-grey-border-color rounded-xl px-5 ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)] !w-full">
                                             <label for="weekday[{{ $key }}][to]" class="font-normal">{{ __('Finish') }}</label>
                     
                                             <div class="wj-time-input-fields">
                                                   <input type="time" placeholder="HH:MM" id="weekday[{{ $key }}][to]"
                                                      name="weekday[{{ $key }}][to]" wire:model="weekdays.{{ $key }}.end_time"
                                                      class="wj-form-control wj-mask-time hourpicker !max-w-full !w-44 text-xs rounded-full koon-grey-bg">
                                             </div>
                                          </div>
                                       </div>
                                 </div>
                              </div>
                           @endforeach
                        </div>
                     </div>
                  </div>
               </div>
               <div x-show="_page=='gallery'" x-cloak>
                  <div class="media-section ![height:initial] !relative ![top:initial] !w-full ![z-index:initial] !border-0" wire:ignore>
                     <div class="container-small tab-content-box !p-0 !m-0">
                        <div class="tab-content">
                              <div class="active" data-tab-content>
                                 <div class="device-library ![overflow:initial] !relative ![height:initial] !p-0">
                                    <div class="upload-manager"
                                    x-on:livewire-upload-start="uploading = true"
                                    x-on:livewire-upload-finish="uploading = false"
                                    x-on:livewire-upload-cancel="uploading = false"
                                    x-on:livewire-upload-error="uploading = false"
                                    x-on:livewire-upload-progress="progress = $event.detail.progress">
                                       <div class="upload-card relative">
                                          
                                          <input type="file" wire:model="image" multiple name="image" class="absolute right-0 top-0 w-full h-full opacity-0">

                                          <div class="upload-box mb-1">
                                             {!! __icon('interface-essential', 'image-picture-upload-arrow', 'w-5 h-5') !!}
                                          </div>

                                          <p x-cloak x-show="uploading" x-text="'{{ __('Uploading') }}' + ' · ' + progress + '%'"></p>

                                          <p x-cloak x-show="!uploading">{{ __('Add image  · 5MB max') }} </p>
                                       </div>
                                       <button class="btn !hidden" x-cloak :class="{'!hidden': !uploading}">{{ __('Cancel') }}</button>

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
                                          <div class="mb-1 bg-red-200 text-[11px] p-1 px-2 rounded-md">
                                             <div class="flex items-center">
                                                <div>
                                                   <i class="fi fi-rr-cross-circle flex text-xs"></i>
                                                </div>
                                                <div class="flex-grow ml-1 text-xs">{{ $error }}</div>
                                             </div>
                                          </div>
                                       @endif
                                    
                                       <div class="files" :class="{
                                          '!hidden': !service.gallery || service.gallery && service.gallery.length == 0
                                       }">

                                       <template x-for="(media, index) in service.gallery" :key="index">
                                          <div class="file-card">
                                             
                                             <label x-data="{m: getMedia(media)}" x-intersect="m">
                                                <span class="checkmark !bg-white shadow shadow-xl" @click="$wire.deleteMedia(media)">
                                                      {!! __i('interface-essential', 'trash-delete-remove', 'w-4 h-4 text-red-400') !!}
                                                </span>
                                                <span class="media-cover"></span>
                                                <img :src="m" loading="lazy" alt="">
                                                <div class="image-options"></div>
                                             </label>
                                          </div>
                                       </template>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                        </div>
                     </div>
                  </div>
               </div>
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

   
   @script
   <script>
       Alpine.data('service_editing', () => {
          return {
            _page: '-',
            colors: [],
            uploading: false,
            progress: 0,
            titleText: '{{ __('Media') }}',
            service: @entangle('serviceArray').live,
            gs: '{{ gs('media/booking/image') }}',
            description: @entangle('service.description'),

            getMedia(media){

               return this.gs +'/'+ media;
            },

            save(){
               let $this = this;
               

               $this.$wire.save($this.audience);
            },
            
            _color(color){
               return color.replace(/#/g, '');
            },
            init(){
               var $this = this;
            }
          }
       });
   </script>
   @endscript
</div>