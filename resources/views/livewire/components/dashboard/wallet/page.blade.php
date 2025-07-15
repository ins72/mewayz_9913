

<?php
   use App\Models\Product;
   use App\Models\ProductOrder;
   use App\Models\WalletTransaction;

   use function Livewire\Volt\{state, mount, placeholder, on};

   mount(function(){
    $this->getProducts();
    $this->loadTransactions();
   });

   state([
      'products' => [],
      'user' => fn() => iam(),
   ]);

   state([
       'transactions'
   ]);

   on([
      'productUpdated' => fn() => $this->getProducts(),
      'productCreated' => fn() => $this->getProducts(),
   ]);


   $loadTransactions = function(){
    $this->transactions = WalletTransaction::where('user_id', $this->user->id)
    ->orderBy('id', 'DESC')
    ->limit(10)
    ->get();
   };


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

    $queryPieMetric = function(){
      $start_of_this_year = \Carbon\Carbon::now()->startOfYear();
      $start_of_last_year = \Carbon\Carbon::now()->subYear()->startOfYear();
      $end_of_last_year = $start_of_this_year->copy()->subSecond();
      // Get Donations for This Year
      $donations_this_year = WalletTransaction::where('user_id', iam()->id)
         ->where('created_at', '>=', $start_of_this_year)
         ->get();

      // Get Donations for Last Year
      $donations_last_year = WalletTransaction::where('user_id', iam()->id)
         ->whereBetween('created_at', [$start_of_last_year, $end_of_last_year])
         ->get();

      // Process This Year's Donations
      $thisYearAmount = 0;
      $lastYearAmount = 0;

      // Process This Year's Donations
      foreach ($donations_this_year as $item) {
         $thisYearAmount += $item->amount;
      }

      // Process Last Year's Donations
      foreach ($donations_last_year as $item) {
         $lastYearAmount += $item->amount;
      }

      return [
         'lastYear' => $lastYearAmount,
         'thisYear' => $thisYearAmount,
         'thisYearPrice' => ao(iam()->currency(), 'code') . nr($thisYearAmount),
      ];
    };

    $queryEarningsMetric = function(){
      $start_of_this_year = \Carbon\Carbon::now()->startOfYear();
      $start_of_last_year = \Carbon\Carbon::now()->subYear()->startOfYear();
      $end_of_last_year = $start_of_this_year->copy()->subSecond();
      // Get Visitors for This Year
      $visitors_this_year = WalletTransaction::where('user_id', $this->user->id)
         ->where('created_at', '>=', $start_of_this_year)
         ->get();

      // Get Visitors for Last Year
      $visitors_last_year = WalletTransaction::where('user_id', $this->user->id)
         ->whereBetween('created_at', [$start_of_last_year, $end_of_last_year])
         ->get();
      
      $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
      $thisyear_all = array_fill_keys($months, ['amount' => 0]);
      $lastyear_all = array_fill_keys($months, ['amount' => 0]);

      // Process This Year's Views
      foreach ($visitors_this_year as $item) {
          $date = (string) \Carbon\Carbon::parse($item->created_at)->format('M');
          if (!empty($date) && array_key_exists($date, $thisyear_all)) {
              $thisyear_all[$date]['amount'] += $item->amount;
          }
      }

      // Process Last Year's Views
      foreach ($visitors_last_year as $item) {
          $date = (string) \Carbon\Carbon::parse($item->created_at)->format('M');
          if (!empty($date) && array_key_exists($date, $lastyear_all)) {
              $lastyear_all[$date]['amount'] += $item->amount;
          }
      }

      // Ensure all months are present with zero if not already filled
      foreach ($months as $month) {
          if (!array_key_exists($month, $thisyear_all)) {
              $thisyear_all[$month] = ['amount' => 0];
          }
          if (!array_key_exists($month, $lastyear_all)) {
              $lastyear_all[$month] = ['amount' => 0];
          }
      }

      return [
         'labels' => $months,
         'lastyear' => _get_chart_data($lastyear_all),
         'thisyear' => _get_chart_data($thisyear_all),
      ];
    };
?>
<div>
    
    <div x-data="app_wallet">
         <div class="banner">
            <div class="banner__container !bg-white">
              <div class="banner__preview !hidden lg:!flex">
                {!! __icon('money', 'Wallet-MAIN') !!}
              </div>
              <div class="banner__wrap">
                <div class="banner__title h3 !text-black">{{ __('Your Wallet') }}</div>
                {{-- <div class="banner__text !text-black">{{ __('Easily Build and Track Your Products') }}</div> --}}
                {{-- <div class="mt-7 grid grid-cols-3 gap-1 lg:grid-cols-3">
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
                               <div @click="$dispatch('open-modal', 'store-customers-modal')" class="[box-shadow:var(--yena-shadows-md)] [background:var(--yena-colors-gradient-light)] text-xs mt-[5px] rounded-md cursor-pointer">{{ __('View') }}</div>
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
                              <div @click="$dispatch('open-modal', 'store-orders-modal')" class="[box-shadow:var(--yena-shadows-md)] [background:var(--yena-colors-gradient-light)] text-xs mt-[5px] rounded-md cursor-pointer">{{ __('View') }}</div>
                           </div>
                        </template>
                     </div>
                    </div>
                </div> --}}

                <div class="balance-card">
                    <div class="balance-card__head">
                      <div class="balance-card__title">{{ __('Total Balance') }}
                        <div class="tooltip tooltipstered">
                          {{-- <svg class="icon icon-info-circle">
                            <use xlink:href="#icon-info-circle"></use>
                          </svg> --}}
                        </div>
                      </div>
                    </div>
                    <div class="balance-card__price">{!! iam()->price(iam()->balanceFloat, 2) !!}
                      {{-- <svg class="icon icon-eye">
                        <use xlink:href="#icon-eye"></use>
                      </svg> --}}
                    </div>
                    <div class="balance-card__btns grid grid-cols-1 gap-3">
                        <a class="sandy-button !bg-black py-2 flex-grow w-[100%] flex justify-center items-center !text-white rounded-xl !cursor-pointer" @click="$dispatch('open-modal', 'withdraw-modal')">
                            <div class="--sandy-button-container">
                                <span class="text-xs">{{ __('Withdraw') }}</span>
                            </div>
                        </a>
                    </div>
                  </div>
                        
                  <div class="page__line">
                    <div class="page__nav">
                        <a class="page__link active">{{ __('Overview') }}</a>
                        <a class="page__link" @click="$dispatch('open-modal', 'transactions-modal')">{{ __('Transaction') }}</a>
                        <a class="page__link" @click="$dispatch('open-modal', 'wallet-settings-modal')">{{ __('Settings') }}</a>
                    </div>
                  </div>
                
                {{-- <div class="mt-3 flex gap-2">
                    <button class="yena-button-stack !rounded-full" @click="$dispatch('open-modal', 'create-store-modal')">{{ __('Withdraw') }}</button>
                </div> --}}
              </div>
            </div>
          </div>
          <div>

            <div class="lg:!flex p-0 lg:!h-full justify-between gap-4 !w-full">
                <div class="w-full min-w-0">
                   <div wire:ignore>
                      <div class="grid grid-cols-1 gap-4">
                         <div class="widget widget_chart">
                            <div class="p-4 bg-[#f7f3f2] !bg-white mb-0 rounded-8 cursor-pointer lg:flex lg:justify-between lg:items-center hover:opacity-70">
                               <div class="flex flex-col lg:flex-row items-center gap-3 ">
                                  
                                  {!! __i('Business, Products', 'document-cash-chart', 'w-10 h-10 self-start') !!}
          
          
                                  <div class="flex flex-col">
                                     <div class="text-gray-800 font-bold text-2xl">{{ __('Earnings') }}</div>
                                     {{-- <div class="text-gray-600"></div> --}}
                                  </div>
                               </div>
                            </div>
                            {{-- <div class="widget__title">{{ __('Store Earnings') }}</div> --}}
                            <div class="widget__wrap">
                              <div class="widget__chart widget__chart_earning relative" wire:ignore>
                               <template x-if="chartLoading">
                                   <div class="flex flex-col gap-2 p-5 w-full mt-auto">
                                     <div class="--placeholder-skeleton w-full h-[40px] rounded-sm"></div>
                                     <div class="--placeholder-skeleton w-full h-[40px] rounded-sm"></div>
                                     <div class="--placeholder-skeleton w-full h-[40px] rounded-sm"></div>
                                     <div class="--placeholder-skeleton w-full h-[40px] rounded-sm"></div>
                                   </div>
                               </template>
                               
                               <div class="store-line-chart" x-ref="earningsChart"></div>
                              </div>
                       
                              {{-- <div class="widget__btns"><button class="widget__btn btn btn_purple btn_wide">Analytics</button></div> --}}
                            </div>
                          </div>
                      </div>
                   </div>
                   


                    <div class="wallet-transactions mt-4 p-3 bg-white rounded-[24px] flex flex-col gap-2">
                        @foreach ($transactions as $item)
                            @php
                                $component = "livewire::components.console.wallet.include.transaction-include-$item->type";
                            @endphp
                            <a class="wallet-transactions-item bg-[#f7f3f2] rounded-[16px]"  @click="$dispatch('open-modal', 'transaction-modal'); $dispatch('registerTransaction', {id: '{{ $item->id }}'})">
                                <x-dynamic-component :component="$component" :$item/>
                            </a>
                        @endforeach
                    </div>
                </div>
                <div class="min-w-[310px] w-full lg:!w-max mt-4 lg:![margin-top:0]">
                   <div class="short-cal flex flex-[1] flex-col gap-[12px] p-[12px] mt-0 border-l border-solid border-gray-50">
                      <div class="flex items-center justify-between">
                         <p class="text-color-headline font-bold">{{ __('My earnings') }}</p>
                         <div class="p-[4px] flex items-center">
                            <span class="default-tooltip relative top-[1px]">
                                  <a class="cursor-pointer">
                                     {!! __i('custom', 'settings-pay-1', 'w-4 h-4') !!}
                                  </a>
                            </span>
                         </div>
                      </div>
                      <div class="flex flex-col h-full gap-2">
                         <div class="calendar-day-view h-full flex items-center justify-center">
                            <div class="h-full w-full flex flex-col items-center bg-[var(--yena-colors-gray-100)] rounded-[10px]">
                               <div class="widget widget_stat widget_shadow widget_after !w-full !rounded-[10px]">
                                  <div class="widget__chart widget__chart_items" wire:ignore>
                                     <template x-if="chartLoading">
                                         <div class="flex gap-2 p-5 w-full mx-auto">
                                           <div class="--placeholder-skeleton w-28 h-28 p-4 rounded-full mx-auto">
                                              <div class="bg-white h-full w-full rounded-full"></div>
                                           </div>
                                         </div>
                                     </template>
    
                                     <div>
                                        <div x-ref="chartPie"></div>
                                     </div>
                                  </div>
                                  <div class="text-7xl leading-[1.2] font-semibold tracking-[-1px] text-center mx-auto mt-4" x-html="yearEarnings"></div>
                                  <div class="max-w-[185px] mb-[18px] text-[#808191] text-center mx-auto">{{ __("This is your total earned this year") }}</div>
                                  <div class="widget__legend">
                                     <div class="widget__color">
                                        <div class="widget__bg !bg-[#FFCE73]"></div>
                                        <div class="widget__text">{{ __('This Year') }}</div>
                                     </div>
                                     <div class="widget__color">
                                        <div class="widget__bg !bg-[#A0D7E7]"></div>
                                        <div class="widget__text">{{ __('Last Year') }}</div>
                                     </div>
                                  </div>
                               </div>
                               <div class="flex flex-col items-center gap-2 py-20 px-5">
                                  {!! __i('Building, Construction', 'store', 'w-12 h-12') !!}
                                  <div class="flex flex-col">
                                     <p class="text-color-descriptive text-center w-full">{{ __('Earn money by creating & sharing your products.') }}</p>
                                  </div>
                                  <a class="yena-button-stack !rounded-full" href="{{ route('console-store-index') }}" @navigate>{{ __('Get Started') }}</a>
                               </div>
                            </div>
                         </div>
                      </div>
                   
                   </div>
                </div>
             </div>


          </div>
          
    
          
         <template x-teleport="body">
             <x-modal name="transactions-modal" :show="false" removeoverflow="true" maxWidth="2xl" >
                <livewire:components.console.wallet.transactions :key="uukey('app', 'console.wallet.transactions')">
             </x-modal>
          </template>
    
          
          <template x-teleport="body">
            <x-modal name="transaction-modal" :show="false" removeoverflow="true" maxWidth="xl" >
               <livewire:components.console.wallet.transaction :key="uukey('app', 'console.wallet.transaction')">
            </x-modal>
         </template>
          
         <template x-teleport="body">
             <x-modal name="withdraw-modal" :show="false" removeoverflow="true" maxWidth="xl" >
                <livewire:components.console.wallet.withdraw :key="uukey('app', 'console.wallet.withdraw')">
             </x-modal>
          </template>
          
          <template x-teleport="body">
              <x-modal name="wallet-settings-modal" :show="false" removeoverflow="true" maxWidth="xl" >
                 <livewire:components.console.wallet.settings :key="uukey('app', 'console.wallet.settings')">
              </x-modal>
           </template>
    </div>
   @script
   <script>
       Alpine.data('app_wallet', () => {
          return {
            analyticsLoading: true,
            chartLoading: true,
            yearEarnings: 0,
            analytics: {
              earned: '$0',
              customers: '0',
              products: '0',
              orders: '0',
            },

            queryChart(){
                let $this = this;
                $this.$wire.queryPieMetric().then(r => {
                    $this.yearEarnings = r.thisYearPrice;
                        
                    let options = {
                        labels: ['{{ __('Last Year') }}', '{{ __('This Year') }}'],
                        series: [r.lastYear, r.thisYear],
                        colors: ['#A0D7E7', '#FFCE73', '#E4E4E4'],
                        chart: {
                            height: '100%',
                            type: 'donut'
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '71%',
                                    polygons: {
                                        strokeWidth: 0
                                    }
                                },
                                expandOnClick: false
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        states: {
                            hover: {
                                filter: {
                                    type: 'darken',
                                    value: 0.9
                                }
                            }
                        },
                        legend: {
                            show: false
                        },
                        tooltip: {
                            enabled: true
                        }
                    };
                    $this.chartLoading = false;
                    new ApexCharts($this.$refs.chartPie, options).render();
                });
                $this.$wire.queryEarningsMetric().then(r => {
                    let options = {
                        ...window.apexOptions,
                        labels: r.labels,
                        series: [
                            {
                                name: '{{ __('This Year') }}',
                                data: r.thisyear.amount,
                            },
                            {
                                name: '{{ __('Last Year') }}',
                                data: r.lastyear.amount,
                            },
                        ],
                        colors: ['#6C5DD3', '#A0D7E7'],
                        grid: {
                            borderColor: '#E4E4E4',
                            strokeDashArray: 0,
                            xaxis: {
                                lines: {
                                    show: false
                                }
                            },
                            yaxis: {
                                lines: {
                                    show: false
                                }
                            },
                            padding: {
                                top: 0,
                                left: 10,
                                right: 0,
                                bottom: 0
                            }
                        },
                        yaxis: {
                            show: true,
                        },
                        xaxis: {
                            ...window.apexOptions.xaxis,
                            show: true,
                            labels: {
                                show: true
                            },
                        },
                        stroke: {
                            curve: 'smooth'
                        },
                        dataLabels: {
                            enabled: false
                        },
                        legend: {
                            show: false
                        },
                        chart: {
                            ...window.apexOptions.chart,
                            type: 'bar',
                            height: '100%',
                        },
                    };

                    options['chart']['height'] = 200;

                    $this.chartLoading = false;
                    new ApexCharts($this.$refs.earningsChart, options).render();
                });
            },

            init(){
              let $this = this;
              $this.queryChart();

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