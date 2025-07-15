

<?php
   use App\Models\Product;
   use App\Models\ProductOrder;

   use function Livewire\Volt\{state, mount, placeholder, on};

   mount(function(){
      $this->getProducts();
   });

   state([
      'products' => [],
      'user' => fn() => iam(),
   ]);

   on([
      'productUpdated' => fn() => $this->getProducts(),
      'productCreated' => fn() => $this->getProducts(),
   ]);


   $getProducts = function(){
      $products = Product::where('user_id', iam()->id);

      $products = $products->orderBy('position', 'ASC')->orderBy('id', 'DESC')->get();

      $this->products = $products;
   };

   $deleteProduct = function($id){
      
      if (!$product = Product::where('user_id', iam()->id)->where('id', $id)->first()) return;


      storageDelete('media/store/image', $product->featured_img);

      if (!empty($product->media) && is_array($product->media)) {
         foreach ($product->media as $key => $value) {
            storageDelete('media/store/image', $value);
         }
      }


      $options = $product->variant()->get();

      foreach ($options as $item) {
         if($item->type == 'image'){
            storageDelete('media/store/variant', $item->variation_value);
         }

         $item->delete();
      }
      //   if (!empty($product->files) && is_array($product->files)) {
      //       foreach ($product->files as $key => $value) {
      //           storageDelete('media/shop/downloadables', $value);
      //       }
      //   }

      $product->delete();

      $this->getProducts();
   };
   
   $getAnalytics = function(){
        $customers = ProductOrder::where('user_id', $this->user->id)
        ->select('payee_user_id', \DB::raw('count(*) as total'))
        ->groupBy('payee_user_id')->pluck('payee_user_id');

        $products = Product::where('user_id', $this->user->id)->count();

        $orders = ProductOrder::where('user_id', $this->user->id)->count();
        $earned = ProductOrder::where('user_id', $this->user->id)->sum('price');

        return [
            'orders' => number_format($orders),
            'customers' => number_format(count($customers)),
            'earned' => iam()->price($earned),
            'products' => $products,
        ];
    };
?>
<div>
    
    <div x-data="app_store">
         <div class="banner">
            <div class="banner__container !bg-white">
              <div class="banner__preview">
                {!! __icon('Building, Construction', 'store') !!}
              </div>
              <div class="banner__wrap">
                <div class="banner__title h3 !text-black">{{ __('Your Products') }}</div>
                <div class="banner__text !text-black">{{ __('Easily Build and Track Your Products') }}</div>
                <div class="mt-7 grid grid-cols-3 gap-1 lg:grid-cols-3">
                   <div class="col-span-3">
                     <div class="rounded-8 p-2 text-center col-span-2 lg:col-span-1 bg-[#f7f3f2] text-black">
                        <div class="detail text-gray-600">{{ __('Earned') }}</div>
                        <template x-if="analyticsLoading">
                            <div class="--placeholder-skeleton w-full h-[20px] rounded-sm mt-1"></div>
                        </template>
                        <template x-if="!analyticsLoading">
                            <div class="number-secondary" x-html="analytics.earned"></div>
                        </template>
                     </div>
                    </div>
                    <div class="rounded-8 p-2 text-center bg-[#f7f3f2] text-black">
                       <div class="detail text-gray-600">{{ __('Products') }}</div>
                       <template x-if="analyticsLoading">
                           <div class="--placeholder-skeleton w-full h-[20px] rounded-sm mt-1"></div>
                       </template>
                       <template x-if="!analyticsLoading">
                           <div class="number-secondary" x-text="analytics.products"></div>
                       </template>
                    </div>
                    <div>
                     <div class="rounded-8 p-2 text-center bg-[#f7f3f2] text-black">
                        <div class="detail text-gray-600">{{ __('Customers') }}</div>
                        <template x-if="analyticsLoading">
                            <div class="--placeholder-skeleton w-full h-[20px] rounded-sm mt-1"></div>
                        </template>
                        <template x-if="!analyticsLoading">
                            <div>
                               <div class="number-secondary" x-text="analytics.customers"></div>
                               <div @click="$dispatch('open-modal', 'store-customers-modal')" class="[box-shadow:var(--yena-shadows-md)] [background:var(--yena-colors-gradient-light)] text-xs mt-[5px] rounded-md cursor-pointer px-2 font-bold">{{ __('View') }}</div>
                            </div>
                        </template>
                     </div>
                    </div>
                    <div>
                     <div class="rounded-8 p-2 text-center bg-[#f7f3f2] text-black">
                        <div class="detail text-gray-600">{{ __('Orders') }}</div>
                        <template x-if="analyticsLoading">
                            <div class="--placeholder-skeleton w-full h-[20px] rounded-sm mt-1"></div>
                        </template>
                        <template x-if="!analyticsLoading">
                           <div>
                              <div class="number-secondary" x-text="analytics.orders"></div>
                              <div @click="$dispatch('open-modal', 'store-orders-modal')" class="[box-shadow:var(--yena-shadows-md)] [background:var(--yena-colors-gradient-light)] text-xs mt-[5px] rounded-md cursor-pointer px-2 font-bold">{{ __('View') }}</div>
                           </div>
                        </template>
                     </div>
                    </div>
                </div>
                
                <div class="mt-3 flex gap-2">
                    <button class="yena-button-stack !rounded-full" @click="$dispatch('open-modal', 'create-store-modal')">{{ __('Create Product') }}</button>
                    {{-- <a class="yena-button-stack !rounded-full" @click="$dispatch('open-modal', 'store-settings-modal');">{{ __('Orders') }}</a> --}}
                    <a class="yena-button-stack !rounded-full" @click="$dispatch('open-modal', 'store-settings-modal');">{{ __('Settings') }}</a>
                </div>
              </div>
            </div>
          </div>
          <div class="cri">
            <div class="catalog">
               <div class="catalog__wrapper !p-0">
                  <div class="catalog__head">
                     <div class="catalog__title h3">{{ __('Products') }}</div>
                     <!-- tabs-->
                     {{-- <button class="catalog__toggle !ml-auto">
                        <svg class="icon icon-filter-1">
                           <use xlink:href="#icon-filter-1"></use>
                        </svg>
                        <svg class="icon icon-close">
                           <use xlink:href="#icon-close"></use>
                        </svg>
                     </button> --}}
                  </div>
                  <!-- filters-->
                  @if ($products->isEmpty())
                  <div class="">
                      <div class="flex flex-col justify-center items-start px-0 py-[60px]">
                         {!! __i('--ie', 'eye-cross-circle 2', 'w-14 h-14') !!}
                         <p class="mt-3 text-base text-left md:!text-2xl font-bold">
                            {!! __t('You have no product. <br> Create a product to get started.') !!}
                         </p>
                      </div>
                  </div>
                  @endif
                  <div class="most !p-0" id="collections">
                     <div class="most__collections !grid grid-cols-1 md:!grid-cols-2 gap-4 items-start" x-data x-masonry.poll.50>
                        @foreach ($products as $item)
                        @php
                            $featured = $item->getFeaturedImage();

                           //  $featuredClass = !$featured ? '' : '';
                        @endphp
                        <div>
                           <div class="most__collection relative" x-data="{is_delete:false, share: false}" @click="$dispatch('open-modal', 'edit-store-modal'); $dispatch('registerProduct', {id: '{{ $item->id }}'})">
                              <div class="card-button mb-2 flex gap-2" x-cloak @click="$event.stopPropagation();" :class="{
                                 '!hidden': !is_delete
                                }">
                                 <button class="btn btn-medium neutral !h-8 !flex !items-center !justify-center !rounded-full !text-black !bg-white" type="button" @click="is_delete=!is_delete">{{ __('Cancel') }}</button>
               
                                 <button class="btn btn-medium !bg-[var(--c-red)] !text-[var(--c-light)] !h-8 !flex !items-center !justify-center !rounded-full w-full" @click="$wire.deleteProduct('{{ $item->id }}'); is_delete=false;">{{ __('Yes, Delete') }}</button>
                              </div>
   
                              <div class="absolute z-[99] top-[20px] left-[20px] bg-[#fff] p-[6px] rounded-[10px] flex gap-2" :class="{
                                 '!hidden': is_delete
                              }">
                                 <div class="bg-[#f3f3f3] w-8 h-8 flex items-center justify-center rounded-full cursor-pointer" app-sandy-prevent
                                 href="{{-- route('user-mix-audience-view', $item->id) --}}">
                                    {!! __i('interface-essential', 'pen-edit.5', 'w-4 h-4 text-black') !!}
                                 </div>
                                 <div class="bg-[#f3f3f3] w-8 h-8 flex items-center justify-center rounded-full cursor-pointer" @click="$event.stopPropagation(); is_delete=true;">
                                    {!! __i('interface-essential', 'trash-delete-remove', 'w-4 h-4 text-red-400') !!}
                                 </div>
                              </div>
                              <div class="most__images">
                                 <div class="most__image {{ is_array($item->media) && count($item->media) < 1 || !is_array($item->media) ? '!w-full !h-[381px]' : '' }} {{ !$featured ? '!bg-[#f3f3f3] flex items-center justify-center' : '' }}">
                                    @if ($featured)
                                    <img src="{{ $featured }}" alt="">
                                    @else
                                    {!! __i('--ie', 'image-picture', 'text-gray-300 w-8 h-8') !!}
                                    @endif
                                 </div>
   
                                 @if (is_array($item->media))
                                    @foreach ($item->media as $index => $m)
                                    @php
                                        if($index >= 2) break;
                                    @endphp
                                        
                                    <div class="most__image">
                                       <img src="{{ gs('media/store/image', $m) }}" alt="">
                                    </div>
                                    @endforeach
   
                                    @if (count($item->media) > 2)
                                    <div class="most__image">
                                       <div class="most__counter" style="background-color:#BCE6EC">+{{ count($item->media) - 2 }}</div>
                                    </div>
                                    @endif
                                 @endif
                              </div>
                              <div class="most__details">
                                 <div class="most__box">
                                    <div class="most__subtitle h4">{{ $item->name }}</div>
                                    <div class="most__author">
                                       @if ($item->variant()->first())
                                       <div class="most__avatar">
                                       {!! __i('Business, Products', 'business-chart-search', 'w-[32px] h-[32px]') !!}
                                       </div>
                                       {{ __('Variable Product') }}
                                       @else
                                       <div class="most__avatar">
                                       {!! __i('Business, Products', 'chart-analytics-curcor', 'w-[32px] h-[32px]') !!}
                                       </div>
                                       {{ __('Single Product') }}
                                       @endif
                                    </div>
                                 </div>
                                 <div class="most__box">
                                    <div class="most__text">{{ __('Price') }}</div>
                                    <div class="most__price h4">
                                       {!! iam()->price($item->price) !!}
                                    </div>
                                 </div>
                              </div>
                              <a class="sandy-button !bg-black py-2 flex-grow w-[100%] flex justify-center items-center !text-white rounded-lg" @click="$event.stopPropagation(); share=!share">
                                  <div class="--sandy-button-container">
                                      <span class="text-xs">{{ __('Share') }}</span>
                                  </div>
                              </a>
                              
                              <div class="card-button mb-2 flex gap-2" x-cloak @click="$event.stopPropagation();" :class="{
                                 '!hidden': !share
                                }">
                                 <div class="relative flex w-[100%] isolate mt-2 gap-2">
                                    <input type="text" class="w-[100%] h-[3rem] text-[1rem] pl-4 pr-10 rounded-md min-w-0 outline-[transparent_solid_2px] outline-offset-[2px] relative appearance-none duration-200 border-2 border-solid [border-image:initial] border-transparent bg-[#f7f3f2] !text-black" placeholder="{{ __('link goes here...') }}" readonly value="{{ route('out-products-single-page', ['slug' => $item->slug]) }}">
                  
                                    <div class="right-0 h-[3rem] text-[1rem] flex items-center justify-center absolute top-0 z-[1]">
                                       <button type="button" class="inline-flex appearance-none items-center justify-center select-none relative whitespace-nowrap align-middle outline-[transparent_solid_2px] outline-offset-[2px] leading-[1.2] rounded-md font-semibold [transition-property:background-color,border-color,color,fill,stroke,opacity,box-shadow,transform] duration-200 h-[2rem] [2.5rem] text-[1rem] pl-4 pr-4 text-[#3c3838] [box-shadow:0_4px_6px_-1px_rgba(0,_0,_0,_0.1),_0_2px_4px_-1px_rgba(0,_0,_0,_0.06)] bg-[linear-gradient(180deg,_#FFFFFF_0%,_#FCF9F5_100%)] border border-solid border-[#e5e0df] w-[100%] m-2" @click="$clipboard('{{ route('out-products-single-page', ['slug' => $item->slug]) }}'); $el.innerText = window.builderObject.copiedText;">{{ __('Copy Link') }}</button>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        @endforeach
                     </div>
                  </div>
                  {{-- <div class="catalog__list">
                     @foreach ($products as $item)
                     <a class="card" href="nft.html">
                        <div class="card__preview">
                           <img class="card__photo" src="{{ $item->getFeaturedImage() }}" alt="">
                           <div class="card__category">
                              <div class="card__image"><img src="img/content/collection/image-6.jpg" alt=""></div>
                              ESCP
                           </div>
                           <div class="card__title">The Currency</div>
                        </div>
                        <div class="card__user">
                           <div class="card__avatar"><img src="img/content/artists/artist-1.jpg" alt=""></div>
                           <div class="card__login">@elnafrederick
                           </div>
                           <div class="card__verified"><img src="img/content/verified.png" alt=""></div>
                        </div>
                        <div class="card__foot remove-before">
                           <div class="card__box">
                              <div class="card__text">{{ __('Price') }}</div>
                              <div class="card__price">0.4321</div>
                           </div>
                           <div class="card__box">
                              <div class="card__text">Buy now</div>
                              <div class="card__price">2.02 ETH</div>
                           </div>
                        </div>
                     </a>
                     @endforeach
                  </div> --}}
                  <div class="catalog__btns !hidden">
                     <button class="button-stroke button-medium catalog__button">load more</button>
                  </div>
               </div>
            </div>
         </div>
          
    
    
          
          <x-modal name="store-settings-modal" :show="false" removeoverflow="true" maxWidth="3xl">
            <livewire:components.console.store.settings :key="uukey('app', 'store-settings')">
         </x-modal>

         <x-modal name="store-orders-modal" :show="false" removeoverflow="true" maxWidth="2xl">
           <livewire:components.console.store.orders.index :key="uukey('app', 'console.store.orders')">
        </x-modal>

        <x-modal name="store-shipping-modal" :show="false" removeoverflow="true" maxWidth="3xl">
          <livewire:components.console.store.shipping :key="uukey('app', 'store-settings-shipping')">
       </x-modal>

        <x-modal name="store-customers-modal" :show="false" removeoverflow="true" maxWidth="xl">
          <livewire:components.console.store.customers :key="uukey('app', 'console.store.customers')">
       </x-modal>
    
        <template x-teleport="body">
           <x-modal name="create-store-modal" :show="false" removeoverflow="true" maxWidth="xl" >
              <livewire:components.console.store.create-modal :key="uukey('app', 'store-page-create')">
           </x-modal>
        </template>

        <template x-teleport="body">
           <div class="[&_.x-modal-body]:m-0 [&_.x-modal-body]:ml-auto [&_.x-modal-body]:mr-0 [&_.x-modal-body]:h-full [&_.fixed]:!p-0 [&_.x-modal-body]:!rounded-none [&_.x-modal-body]:overflow-auto">
              <x-modal name="edit-store-modal" :show="false" removeoverflow="true" maxWidth="xl" >
                 <livewire:components.console.store.edit-modal :key="uukey('app', 'store-page-edit')">
              </x-modal>
           </div>
        </template>
    </div>

      {{-- <div class="items-center flex flex-none flex-nowrap gap-[40px] h-min justify-start overflow-hidden relative w-full">

        <div class="flex-[1_0_0px] h-auto relative w-px">

            <div class="items-start flex flex-col flex-nowrap gap-[10px] h-min justify-center overflow-hidden p-[40px] relative">
                <div>
                    <h1 class="text-left ">20</h1>
                </div>
                
                <div class="">
                    <h5 class="framer-text framer-styles-preset-1946vie">years of experience</h5>
                </div>
            </div>
        </div>
      </div> --}}
   @script
   <script>
       Alpine.data('app_store', () => {
          return {
            analyticsLoading: true,
            analytics: {
              earned: '$0',
              customers: '0',
              products: '0',
              orders: '0',
            },

            init(){
              let $this = this;
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