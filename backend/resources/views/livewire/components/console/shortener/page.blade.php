<?php
    use App\Models\LinkShortener;
    use App\Models\LinkShortenerVisitor;
    use function Livewire\Volt\{state, mount, updated, on, placeholder};
    state([
        'user' => fn() => iam(),
    ]);
    placeholder('
    <div class="w-[100%] p-5 mt-1">
        <div class="--placeholder-skeleton w-[100%] h-[30px] rounded-[var(--yena-radii-sm)]"></div>
        <div class="--placeholder-skeleton w-[100%] h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
        <div class="--placeholder-skeleton w-[100%] h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
    </div>');

    state([
        'has_more_pages' => false,
        'per_page' => 10,
    ]);
    state([
        'links' => [],
    ]);

    mount(function(){
        $this->loadData();
    });

    on([
        'updateLinks' => fn() => $this->loadData(),
    ]);

    $loadData = function(){
        $data = LinkShortener::where('user_id', $this->user->id)->orderBy('id', 'desc');

            // if (!empty($query = $this->search)) {
            //     $searchBy = filter_var($query, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
            //     $audiences = $audiences->where("contact->$searchBy", 'LIKE', '%' . $query . '%');
            // }

        $data = $data->paginate($this->per_page, ['*'], 'page');
        $this->per_page = $this->per_page + 5;
        $this->has_more_pages = $data->hasMorePages();

        $this->links = $data->items();
    };
    $deleteLink = function($id){
        if (!$link = LinkShortener::where('user_id', iam()->id)->where('id', $id)->first()) return;

        LinkShortenerVisitor::where('link_id', $link->id)->get();

        $link->delete();
        $this->loadData();
    };
    
    $getAnalytics = function(){
        $userId = iam()->id;
        $total_views = 0;

        $get = LinkShortener::where('user_id', $this->user->id)->get();
        foreach ($get as $item) {
            $total_views += $item->visitors()->count();
        }

        $total_views = nr($total_views);

        return [
            'total_views' => $total_views,
            'total_links' => $get->count(),
        ];
    };
?>
<div>

    <div x-data="console__link_shortener">
        <div class="banner">
           <div class="banner__container !bg-white">
              <div class="banner__preview !right-0 !w-[300px] !top-[10rem]">
                 {!! __icon('interface-essential', 'attachment-link.4') !!}
              </div>
              <div class="banner__wrap z-[50]">
                 <div class="banner__title h3 !text-black">{{ __('Link Shortener') }}</div>
                 <div class="banner__text !text-black">{{ __('A simple and fast way to shorten links.') }}</div>
                 
                 <div class="mt-7 grid grid-cols-2 gap-1">
                    <div>
                       <div class="rounded-8 p-2 text-center col-span-2 lg:col-span-1 bg-[#f7f3f2] text-black">
                          <div class="detail text-gray-600">{{ __('Total Links') }}</div>
                          <template x-if="analyticsLoading">
                              <div class="--placeholder-skeleton w-full h-[20px] rounded-sm mt-1"></div>
                          </template>
                          <template x-if="!analyticsLoading">
                           <div class="number-secondary" x-html="analytics.total_links"></div>
                          </template>
                       </div>
                    </div>
                    <div>
                       <div class="rounded-8 p-2 text-center col-span-2 lg:col-span-1 bg-[#f7f3f2] text-black">
                          <div class="detail text-gray-600">{{ __('Total Views') }}</div>
                          <template x-if="analyticsLoading">
                              <div class="--placeholder-skeleton w-full h-[20px] rounded-sm mt-1"></div>
                          </template>
                          <template x-if="!analyticsLoading">
                           <div class="number-secondary" x-html="analytics.total_views"></div>
                          </template>
                       </div>
                    </div>
                 </div>
                 <div class="mt-3 flex gap-2">
                    <button class="yena-button-stack !rounded-full" @click="$dispatch('open-modal', 'create-shorten-modal')">{{ __('Create Link') }}</button>
                 </div>
              </div>
           </div>
        </div>
        <div class="mt-4 p-3 bg-white rounded-[24px] grid grid-cols-1 lg:!grid-cols-2 gap-4 gap-2">
            @foreach ($links as $item)
                <div>
                    <div class="px-[10px] py-[15px] flex flex-col relative bg-[#f7f3f2] rounded-[16px]" x-data="{is_delete:false, share: false}"  @click="$dispatch('open-modal', 'transaction-modal'); $dispatch('registerTransaction', {id: '{{ $item->id }}'})">
                        <div class="card-button mb-2 flex gap-2" x-cloak @click="$event.stopPropagation();" :class="{
                           '!hidden': !is_delete
                          }">
                           <button class="btn btn-medium neutral !h-8 !flex !items-center !justify-center !rounded-full !text-black !bg-white" type="button" @click="is_delete=!is_delete">{{ __('Cancel') }}</button>
         
                           <button class="btn btn-medium !bg-[var(--c-red)] !text-[var(--c-light)] !h-8 !flex !items-center !justify-center !rounded-full w-full" @click="$wire.deleteLink('{{ $item->id }}'); is_delete=false;">{{ __('Yes, Delete') }}</button>
                        </div>
                        <div class="card-button mb-2 flex gap-2" x-cloak @click.outside="share=false" :class="{
                           '!hidden': !share
                          }">
                           <div class="relative flex w-[100%] isolate gap-2">
                              <input type="text" class="w-[100%] h-[3rem] text-[1rem] pl-4 pr-10 rounded-md min-w-0 outline-[transparent_solid_2px] outline-offset-[2px] relative appearance-none duration-200 border-2 border-solid [border-image:initial] border-transparent bg-[#fff] !text-black" placeholder="{{ __('link goes here...') }}" readonly value="{{ route('out-shorten-page', ['slug' => $item->slug]) }}">
            
                              <div class="right-0 h-[3rem] text-[1rem] flex items-center justify-center absolute top-0 z-[1]">
                                 <button type="button" class="inline-flex appearance-none items-center justify-center select-none relative whitespace-nowrap align-middle outline-[transparent_solid_2px] outline-offset-[2px] leading-[1.2] rounded-md font-semibold [transition-property:background-color,border-color,color,fill,stroke,opacity,box-shadow,transform] duration-200 h-[2rem] [2.5rem] text-[1rem] pl-4 pr-4 text-[#3c3838] [box-shadow:0_4px_6px_-1px_rgba(0,_0,_0,_0.1),_0_2px_4px_-1px_rgba(0,_0,_0,_0.06)] bg-[linear-gradient(180deg,_#FFFFFF_0%,_#FCF9F5_100%)] border border-solid border-[#e5e0df] w-[100%] m-2" @click="$clipboard('{{ route('out-shorten-page', ['slug' => $item->slug]) }}'); $el.innerText = window.builderObject.copiedText;">{{ __('Copy Link') }}</button>
                              </div>
                           </div>
                        </div>
    
                        <div class="flex items-center justify-between">
                            <div class="flex items-center pr-[10px]">
                                <div class="w-[40px] h-[40px] items-center min-w-[40px] bg-[#e8e8e8] rounded-[100px] flex justify-center relative">
                                    <img src="{{ $item->getIcon() }}" alt=" " class="w-full h-full object-cover rounded-lg">
                                </div>
                                <div class="ml-[20px] text-[#000] text-[0.815rem] font-bold h-full px-[0] py-px flex flex-col justify-between">
                                    <span class="text-[12.3px] leading-[1.4] truncate w-full mt-0">{{ removeHttpHttpsWww($item->link) }}</span>
                                   <span class="font-light text-[#000] mt-[6px] text-[0.7rem] uppercase">{{ $item->slug }}</span>
                                </div>
                             </div>
                             
                             <div class="flex items-center h-full" :class="{
                                '!hidden': is_delete
                             }">
                                <a class="text-xs flex items-center gap-1 cursor-pointer" href="{{ route('console-shortener-edit', ['slug' => $item->slug]) }}">
                                   {!! __i('--ie', 'eye.5', 'w-4 h-4') !!}
                                   {{ nr($item->visitors()->count()) }}
                                </a>
                                <div class="ml-3 mr-2 w-[1px] bg-black h-full block opacity-20"></div>
                                <a href="{{ route('console-shortener-edit', ['slug' => $item->slug]) }}" class="w-[35px] h-[35px] flex items-center rounded-[5px] justify-center bg-[#FFFFFF] [transition:background-color_0.2s_ease-out] cursor-pointer">
                                 <i class="ph ph-pencil text-sm"></i>
                                </a>
                                <div class="w-[35px] h-[35px] flex items-center rounded-[5px] justify-center bg-[#FFFFFF] ml-2 text-black cursor-pointer" @click="$event.stopPropagation(); share=!share">
                                 <i class="fi fi-rr-share text-sm"></i>
                                </div>
                                <div class="w-[35px] h-[35px] flex items-center rounded-[5px] justify-center bg-red-400 ml-2 text-white cursor-pointer" @click="$event.stopPropagation(); is_delete=true;">
                                 <i class="fi fi-rr-trash text-sm"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        
        <x-modal name="create-shorten-modal" :show="false" removeoverflow="true" maxWidth="2xl">
            <livewire:components.console.shortener.create zzlazy :key="uukey('app', 'console.shortener.create')">
        </x-modal>
    </div>
    @script
      <script>
          Alpine.data('console__link_shortener', () => {
            return {
                _page: '-',
                analyticsLoading: true,
                analytics: {
                    total_links: '0',
                    total_views: '0',
                },
                has_more_pages: @entangle('has_more_pages'),

                init(){
                    let $this = this;
                    let _throttleTimer = null;
                    let _throttleDelay = 100;
                    let $windowContactWrapper = $this.$root;

                    let handler = function(e) {
                        clearTimeout(_throttleTimer);
                        _throttleTimer = setTimeout(function() {
                            if ($windowContactWrapper.scrollTop + $windowContactWrapper.clientHeight > $windowContactWrapper.scrollHeight - 100) {
                                if($this.has_more_pages){
                                    $this.$wire.loadData();
                                }
                            }
                        }, _throttleDelay);
                    };

                    $windowContactWrapper.removeEventListener('scroll', handler);
                    $windowContactWrapper.addEventListener('scroll', handler);
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