
<?php
    use App\Models\LinkShortener;
    use App\Models\LinkShortenerVisitor;
    use App\Livewire\Actions\ToastUp;
    use function Livewire\Volt\{state, mount, placeholder, rules, uses, usesFileUploads};
    uses([ToastUp::class]);
    usesFileUploads();

    state([
        'user' => fn() => iam(),
    ]);

    state([
        'slug'
    ]);

    state([
        'shorten' => null,
    ]);
    state([
        'visitors' => [],
    ]);

    rules(fn() => [
        'shorten.link' => 'required|url',
    ]);

    mount(function(){
        $this->_refresh();
    });

    $_refresh = function(){
        if (!$this->shorten = LinkShortener::where('user_id', $this->user->id)->where('slug', $this->slug)->first()) {
            abort(404);
        }
    };

    $getAnalytics = function(){
        $this->visitors = $this->shorten->getInsight();
    };

    $save = function(){
        $this->validate();
        $this->shorten->save();
        
        $this->flashToast('success', __('Link saved'));
    };
?>
<div>
  <div>
    <div x-data="console__edit_link_shortener">
 
       <div class="md:flex p-0 md:h-full justify-between gap-4">
          <div class="w-full min-w-0">
             
             <div class="banner">
                <div class="banner__container !bg-white">
                   <div class="banner__preview !right-0 !w-[300px] !top-[4rem]">
                      {!! __icon('interface-essential', 'attachment-link.4') !!}
                   </div>
                   <div class="banner__wrap z-[50]">
                      <div class="banner__title h3 !text-black">{{ __('Link Analytics') }}</div>
                      <div class="relative flex w-[100%] isolate gap-2">
                         <input type="text" class="w-[100%] h-[3rem] text-[1rem] pl-4 pr-10 rounded-md min-w-0 outline-[transparent_solid_2px] outline-offset-[2px] relative appearance-none duration-200 border-2 border-solid [border-image:initial] border-transparent bg-[#f7f3f2] !text-black" placeholder="{{ __('link goes here...') }}" readonly value="{{ route('out-shorten-page', ['slug' => $shorten->slug]) }}">
       
                         <div class="right-0 h-[3rem] text-[1rem] flex items-center justify-center absolute top-0 z-[1]">
                            <button type="button" class="inline-flex appearance-none items-center justify-center select-none relative whitespace-nowrap align-middle outline-[transparent_solid_2px] outline-offset-[2px] leading-[1.2] rounded-md font-semibold [transition-property:background-color,border-color,color,fill,stroke,opacity,box-shadow,transform] duration-200 h-[2rem] [2.5rem] text-[1rem] pl-4 pr-4 text-[#3c3838] [box-shadow:0_4px_6px_-1px_rgba(0,_0,_0,_0.1),_0_2px_4px_-1px_rgba(0,_0,_0,_0.06)] bg-[linear-gradient(180deg,_#FFFFFF_0%,_#FCF9F5_100%)] border border-solid border-[#e5e0df] w-[100%] m-2" @click="$clipboard('{{ route('out-shorten-page', ['slug' => $shorten->slug]) }}'); $el.innerText = window.builderObject.copiedText;">{{ __('Copy Link') }}</button>
                         </div>
                      </div>
                      {{-- <div class="banner__text !text-black">{{ __('Power your pages with our Booking App.') }}</div> --}}
                      
                      {{-- <div class="mt-3 flex gap-2">
                         <button class="yena-button-stack !rounded-full" @click="$dispatch('open-modal', 'create-booking-modal')">{{ __('Create Booking') }}</button>
                         

                         <a class="yena-button-stack !rounded-full lg:!hidden" @click="$dispatch('open-modal', 'booking-settings-modal');">{{ __('Settings') }}</a>
                      </div> --}}
                   </div>
                </div>
             </div>

             <div>
                <div class="p-2 md:!p-5 rounded-xl bg-white">
                            
                    <div>
                        <div class="grid grid-cols-1 md:!grid-cols-2 gap-2">
                            <div class="details-item w-[100%] p-3 md:p-5 z[box-shadow:0_4px_6px_-1px_rgba(0,_0,_0,_0.1),0_2px_4px_-1px_rgba(0,_0,_0,_0.06)] rounded-md !bg-white [border:1px_solid_var(--c-mix-1)] !w-full">
                                <div class="details-head">
                                    <div class="flex items-center justify-center flex-shrink-0 flex-grow-0 w-8 h-8 rounded-full text-[var(--yena-colors-gray-800)] text-center bg-[var(--yena-colors-gray-200)] opacity-100 mr-2">
                                        {!! __i('Business, Products', 'Business, Chart.4', 'w-5 h-5') !!}
                                    </div>
                                    <div class="details-text caption-sm text-xs md:text-base">{{ __('Total Views') }}</div>
                                </div>
                                <template x-if="analyticsLoading">
                                    <div class="--placeholder-skeleton w-full h-[35px] rounded-sm mt-1"></div>
                                </template>
                                <template x-if="!analyticsLoading">
                                    <div class="details-counter text-2xl md:text-4xl truncate font-bold truncate">{{ number_format(ao($visitors, 'getviews.visits')) }}</div>
                                </template>
                            </div>
                            <div class="details-item w-[100%] p-3 md:p-5 z[box-shadow:0_4px_6px_-1px_rgba(0,_0,_0,_0.1),0_2px_4px_-1px_rgba(0,_0,_0,_0.06)] rounded-md !bg-white [border:1px_solid_var(--c-mix-1)] !w-full">
                                <div class="details-head">
                                    <div class="flex items-center justify-center flex-shrink-0 flex-grow-0 w-8 h-8 rounded-full text-[var(--yena-colors-gray-800)] text-center bg-[var(--yena-colors-gray-200)] opacity-100 mr-2">
                                        {!! __i('Business, Products', 'Business, Chart.24', 'w-5 h-5') !!}
                                    </div>
                                    <div class="details-text caption-sm text-xs md:text-base">{{ __('Unique Views') }}</div>
                                </div>
                                <template x-if="analyticsLoading">
                                    <div class="--placeholder-skeleton w-full h-[35px] rounded-sm mt-1"></div>
                                </template>
                                <template x-if="!analyticsLoading">
                                    <div class="details-counter text-2xl md:text-4xl truncate font-bold truncate">{{ number_format(ao($visitors, 'getviews.unique')) }}</div>
                                </template>
                            </div>
                        </div>
                        <div class="top-stats-c">
                        <div class="top-stats-c-block">
                            <p class="!justify-start mb-1 gap-1 items-center">
                                    <span class="flex items-center justify-center flex-shrink-0 flex-grow-0 w-8 h-8 rounded-full text-[var(--yena-colors-gray-800)] text-center bg-[var(--yena-colors-gray-200)] opacity-100 mr-2">
                                        {!! __i('Maps, Navigation', 'Earth, Home, World.3', 'w-5 h-5') !!}
                                    </span>
                                    
                                    {{ __('Countries') }}
                                </p>
                            <p>
                                <template x-if="analyticsLoading">
                                    <div class="--placeholder-skeleton w-full h-[20px] rounded-sm mr-4"></div>
                                </template>
                                <template x-if="!analyticsLoading">
                                    <span >{{ count(ao($visitors, 'countries') ?? []) }}</span>
                                </template>
                                <span >{{ __('Count') }}</span>
                            </p>
                        </div>
                        <ul >
                            @foreach ((ao($visitors, 'countries') ?? []) as $key => $item)
                            <li>
                                <span >{{ ao($item, 'name') }}</span>
                                <span >{{ nr(ao($item, 'visits')) }}</span>
                            </li>
                            @endforeach
                        </ul>
                        </div>
                        <div class="top-stats-c">
                        <div class="top-stats-c-block">
                            <p class="!justify-start mb-1 gap-1 items-center">
                                    <span class="flex items-center justify-center flex-shrink-0 flex-grow-0 w-8 h-8 rounded-full text-[var(--yena-colors-gray-800)] text-center bg-[var(--yena-colors-gray-200)] opacity-100 mr-2">
                                        {!! __i('Maps, Navigation', 'Direction, Arrow, Road', 'w-5 h-5') !!}
                                    </span>
                                    
                                    {{ __('Cities') }}
                                </p>
                            <p>
                                <template x-if="analyticsLoading">
                                    <div class="--placeholder-skeleton w-full h-[20px] rounded-sm mr-4"></div>
                                </template>
                                <template x-if="!analyticsLoading">
                                    <span >{{ count(ao($visitors, 'cities') ?? []) }}</span>
                                </template>
                                <span >{{ __('Count') }}</span>
                            </p>
                        </div>
                        <ul >
                            @foreach ((ao($visitors, 'cities') ?? []) as $key => $item)
                            <li>
                                <span >{{ $key }}</span>
                                <span >{{ nr(ao($item, 'visits')) }}</span>
                            </li>
                            @endforeach
                        </ul>
                        </div>
                        <div class="top-stats-c">
                        <div class="top-stats-c-block">
                            <p class="!justify-start mb-1 gap-1 items-center">
                                    <span class="flex items-center justify-center flex-shrink-0 flex-grow-0 w-8 h-8 rounded-full text-[var(--yena-colors-gray-800)] text-center bg-[var(--yena-colors-gray-200)] opacity-100 mr-2">
                                        {!! __i('Computers Devices Electronics', 'Iphone, Mobile, Phone', 'w-5 h-5') !!}
                                    </span>
                                    
                                    {{ __('Device') }}
                                </p>
                            <p>
                                <template x-if="analyticsLoading">
                                    <div class="--placeholder-skeleton w-full h-[20px] rounded-sm mr-4"></div>
                                </template>
                                <template x-if="!analyticsLoading">
                                    <span >{{ count(ao($visitors, 'devices') ?? []) }}</span>
                                </template>
                                <span >{{ __('Count') }}</span>
                            </p>
                        </div>
                        <ul >
                            @foreach ((ao($visitors, 'devices') ?? []) as $key => $item)
                            <li>
                                <span >{{ ao($item, 'name') }}</span>
                                <span >{{ nr(ao($item, 'visits')) }}</span>
                            </li>
                            @endforeach
                        </ul>
                        </div>
                        <div class="top-stats-c">
                        <div class="top-stats-c-block">
                            <p class="!justify-start mb-1 gap-1 items-center">
                                    <span class="flex items-center justify-center flex-shrink-0 flex-grow-0 w-8 h-8 rounded-full text-[var(--yena-colors-gray-800)] text-center bg-[var(--yena-colors-gray-200)] opacity-100 mr-2">
                                        {!! __i('Computers Devices Electronics', 'display-monitor-2', 'w-5 h-5') !!}
                                    </span>
                                    
                                    {{ __('Browser') }}
                                </p>
                            <p>
                                <template x-if="analyticsLoading">
                                    <div class="--placeholder-skeleton w-full h-[20px] rounded-sm mr-4"></div>
                                </template>
                                <template x-if="!analyticsLoading">
                                    <span >{{ count(ao($visitors, 'browsers') ?? []) }}</span>
                                </template>
                                <span >{{ __('Count') }}</span>
                            </p>
                        </div>
                        <ul >
                            @foreach ((ao($visitors, 'browsers') ?? []) as $key => $item)
                            <li>
                                <span >{{ ao($item, 'name') }}</span>
                                <span >{{ nr(ao($item, 'visits')) }}</span>
                            </li>
                            @endforeach
                        </ul>
                        </div>
                    </div>
                </div>
             </div>
          </div>
          <div>
            <div class="short-cal min-w-[310px] flex flex-[1] flex-col gap-[12px] p-[12px] border-l border-solid border-gray-50 w-full lg:!w-max lg:!max-w-[310px] mt-4 lg:![margin-top:0]">
              <div class="flex items-center justify-between">
                  <p class="text-color-headline font-bold">{{ __('Edit') }}</p>
              </div>
              
              <div class="flex flex-col gap-2">
                  <div class="calendar-day-view flex items-center justify-center">
                    <div class="h-full w-full flex flex-col bg-[var(--yena-colors-gray-100)] rounded-[10px]">

                        
                        <div class="p-4 w-full">
                            <form wire:submit="save">
                                <div class="form-input !bg-transparent">
                                    <label>{{ __('Link') }}</label>
                                    <input type="text" wire:model="shorten.link" placeholder="{{ __('type your link') }}">
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
                  </div>
              </div>
             
           </div>
          </div>
       </div>
    </div>
    @script
      <script>
          Alpine.data('console__edit_link_shortener', () => {
            return {
                _page: '-',
                analyticsLoading: true,
                analytics: {
                  booking_count: '0',
                },

                init(){
                  let $this = this;
                  $this.$wire.getAnalytics().then(r => {
                    $this.analyticsLoading = false;
                  })
                },
            }
          });
      </script>
    @endscript
 </div>
</div>