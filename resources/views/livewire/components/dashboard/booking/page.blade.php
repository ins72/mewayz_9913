<?php
  use App\Models\BookingAppointment;
  use function Livewire\Volt\{state, mount};


  $getAnalytics = function(){
    $booking_count = BookingAppointment::where('user_id', iam()->id)->count();

    return [
      'booking_count' => $booking_count,
    ];
  };
?>

<div>
   <style>
     .media-section{
       top: 0 !important;
       position: relative !important;
     }
 
     .yena-container, .yena-root-main{
       /* padding: 0 !important; */
     }
 
     .yena-container{
       max-width: 100% !important;
     }
   </style>
  <div>
   
   {{-- <livewire:booking.single :user="iam()" :key="uukey('app', 'components.booking.single')"/> --}}


    <div x-data="console__booking">
 
       <div class="md:flex p-0 md:h-full justify-between gap-4">
          <div class="w-full min-w-0 {{-- max-h-[calc(100vh_-_60px)] overflow-auto --}}">
             
             <div class="banner">
                <div class="banner__container !bg-white">
                   <div class="banner__preview !right-0 !w-[300px] !top-[10rem]">
                      {!! __icon('Cleaning, Housekeeping', 'calendar-schedule') !!}
                   </div>
                   <div class="banner__wrap z-[50]">
                      <div class="banner__title h3 !text-black">{{ __('Bookings') }}</div>
                      <div class="banner__text !text-black">{{ __('Power your pages with our Booking App.') }}</div>
                      
                      <div class="mt-7 grid grid-cols-1 gap-1">
                         <div>
                            <div class="rounded-8 p-2 text-center col-span-2 lg:col-span-1 bg-[#f7f3f2] text-black">
                               <div class="detail text-gray-600">{{ __('Total Booked') }}</div>
                               <template x-if="analyticsLoading">
                                   <div class="--placeholder-skeleton w-full h-[20px] rounded-sm mt-1"></div>
                               </template>
                               <template x-if="!analyticsLoading">
                                <div class="number-secondary" x-html="analytics.booking_count"></div>
                               </template>
                            </div>
                         </div>
                      </div>
                      <div class="mt-3 flex gap-2">
                         <button class="yena-button-stack !rounded-full" @click="$dispatch('open-modal', 'create-booking-modal')">{{ __('Create Booking') }}</button>
                         

                         <a class="yena-button-stack !rounded-full lg:!hidden" @click="$dispatch('open-modal', 'booking-settings-modal');">{{ __('Settings') }}</a>
                      </div>
                   </div>
                </div>
             </div>

             
             
             <livewire:components.console.booking.spot.cal lazy :key="uukey('app', 'console.booking.spot.cal')">
             
             {{-- <livewire:components.console.booking.calendar lazy :key="uukey('app', 'console.booking.calendar')"> --}}
          </div>
          <div>
            <div class="short-cal min-w-[310px] max-w-[310px] hidden md:!flex flex-[1] flex-col w-max gap-[12px] p-[12px] mt-0 border-l border-solid border-gray-50">
              <div class="flex items-center justify-between">
                  <p class="text-color-headline font-bold">{{ __('Settings') }}</p>
                  <div class="p-[4px] flex items-center">
                      <span class="default-tooltip relative top-[1px]">
                          <a class="cursor-pointer">
                            {!! __i('--ie', 'settings.2', 'w-5 h-5') !!}
                          </a>
                      </span>
                  </div>
              </div>
              <div class="flex gap-2">
                  <a @click="_page='-'" class="yena-button-stack --primary !text-xs !h-8">
                     {{ __('Settings') }}
                  </a>
                  <a @click="_page='services'" class="yena-button-stack --primary !text-xs !h-8">
                     {{ __('Services') }}
                  </a>
                  {{-- <a @click="_page='gallery'" class="yena-button-stack --primary !text-xs !h-8">
                     {{ __('Gallery') }}
                  </a> --}}
              </div>
              <div class="flex flex-col gap-2">
                  <div class="calendar-day-view flex items-center justify-center">
                    <div class="h-full w-full flex flex-col items-center bg-[var(--yena-colors-gray-100)] rounded-[10px]">
                       <div class="p-4 w-full">
                        <div x-cloak x-show="_page=='-'">
                          <livewire:components.console.booking.settings lazy :key="uukey('app', 'console.booking.settings')">
                        </div>
                        <div x-cloak x-show="_page=='services'">
                          <livewire:components.console.booking.services lazy :key="uukey('app', 'console.booking.services')">
                        </div>
                        {{-- <div x-cloak x-show="_page=='gallery'">
                          <livewire:components.console.booking.gallery lazy :key="uukey('app', 'console.booking.gallery')">
                        </div> --}}
                       </div>
                       {{-- <div class="flex flex-col items-center my-auto gap-2">
                          {!! __i('--ie', 'calendar-schedule-checkmark', 'w-10 h-10') !!}
                          <div class="flex flex-col">
                             <p class="text-color-descriptive text-center w-full">{{ __('Looks like you don\'t have any scheduled posts...') }}</p>
                          </div>
                          <a class="yena-button-stack !rounded-full" href="" @navigate>{{ __('Create a new post') }}</a>
                       </div> --}}
                    </div>
                  </div>
              </div>
             
           </div>
          </div>
       </div>

       
       <x-modal name="create-booking-modal" :show="false" removeoverflow="true" maxWidth="2xl">
          <livewire:components.console.booking.booking zzlazy :key="uukey('app', 'console.booking.booking')">
      </x-modal>

      <template x-teleport="body">
         <div class="[&_.x-modal-body]:m-0 [&_.x-modal-body]:ml-auto [&_.x-modal-body]:mr-0 [&_.x-modal-body]:h-full [&_.fixed]:!p-0 [&_.x-modal-body]:!rounded-none [&_.x-modal-body]:overflow-auto">
            <x-modal name="edit-booking-modal" :show="false" removeoverflow="true" maxWidth="xl" >
               <livewire:components.console.booking.edit-modal :key="uukey('app', 'booking-page-edit')">
            </x-modal>
         </div>
      </template>
       
      <x-modal name="booking-settings-modal" :show="false" removeoverflow="true" maxWidth="2xl">

        <div class="w-full">
          <div class="flex flex-col">
             <a x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-3 right-3 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
                <i class="fi fi-rr-cross text-sm"></i>
             </a>
       
             <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('Settings') }}</header>
       
             <hr class="yena-divider">
       
             <div class="px-8 pt-2 pb-6">
              <div class="flex gap-2 mb-4">
                  <a @click="_page='-'" class="yena-button-stack --primary !text-xs !h-8">
                     {{ __('Settings') }}
                  </a>
                  <a @click="_page='services'" class="yena-button-stack --primary !text-xs !h-8">
                     {{ __('Services') }}
                  </a>
                  <a @click="_page='gallery'" class="yena-button-stack --primary !text-xs !h-8">
                     {{ __('Gallery') }}
                  </a>
              </div>

              <div>
                <div x-cloak x-show="_page=='-'">
                  <livewire:components.console.booking.settings lazy :key="uukey('app', 'console.booking.settings-modal')">
                </div>
                <div x-cloak x-show="_page=='services'">
                  <livewire:components.console.booking.services lazy :key="uukey('app', 'console.booking.services-modal')">
                </div>
                <div x-cloak x-show="_page=='gallery'">
                  <livewire:components.console.booking.gallery lazy :key="uukey('app', 'console.booking.gallery-modal')">
                </div>
               </div>
             </div>
          </div>
       </div>
     </x-modal>
    </div>
    @script
      <script>
          Alpine.data('console__booking', () => {
            return {
                _page: '-',
                analyticsLoading: true,
                analytics: {
                  booking_count: '0',
                },

                init(){
                  let $this = this;
                  $this.$store.app.isShortSidebar = true;
                  $this.$wire.getAnalytics().then(r => {
                    $this.analytics = r;
                    $this.analyticsLoading = false;
                  });
                },
            }
          });
      </script>
    @endscript
 </div>
</div>