
<?php

   use Carbon\Carbon;
   use App\Models\Site;
   use App\Models\SitesVisitor;
   use App\Models\Audience;
   use App\Models\CoursesOrder;
   use App\Models\ProductOrder;
   use App\Models\WalletTransaction;
   use function Livewire\Volt\{state, mount, placeholder, on};

   mount(function(){
      
   });

   state([
       'user' => fn() => iam()->toArray(),
   ]);
   placeholder('placeholders.dashboard.sites.page-placeholder');

   mount(function(){
      //  dd($this->queryLeadsMetric());
   });

   $queryLeadsMetric = function(){
      $start_date = Carbon::now()->subDays(30)->format('Y-m-d');
      $end_date = Carbon::now()->addDays(30)->format('Y-m-d');

      $metric = [];

      $query = Audience::select(['created_at'])
         ->where('owner_id', iam()->id)
         ->whereBetween('created_at', [$start_date, $end_date])
         ->get();

      foreach ($query as $item) {
         $date = Carbon::parse($item->created_at)->toFormattedDateString();

         if (!isset($metric[$date])) {
            $metric[$date] = [
                  'count' => 0,
            ];
         }

         $metric[$date]['count']++;
      }
      // Add dollar sign to the final amount
      // foreach ($metric as $date => $data) {
      //    $metric[$date]['amount'] = ao(iam()->currency(), 'currency') . number_format($data['amount'], 2);
      // }

      return _get_chart_data($metric);
   };

   $queryStoreMetric = function(){
      $start_date = Carbon::now()->subDays(30)->format('Y-m-d');
      $end_date = Carbon::now()->addDays(30)->format('Y-m-d');

      $metric = [];

      $query = ProductOrder::select(['price', 'created_at'])
         ->where('user_id', iam()->id)
         ->whereBetween('created_at', [$start_date, $end_date])
         ->get();

      foreach ($query as $item) {
         $date = Carbon::parse($item->created_at)->toFormattedDateString();

         if (!isset($metric[$date])) {
            $metric[$date] = [
                  'count' => 0,
                  'amount' => 0,
            ];
         }

         $metric[$date]['count']++;
         $metric[$date]['amount'] += $item->price;
      }
      // Add dollar sign to the final amount
      // foreach ($metric as $date => $data) {
      //    $metric[$date]['amount'] = ao(iam()->currency(), 'currency') . number_format($data['amount'], 2);
      // }

      return _get_chart_data($metric);
   };

   $queryWalletMetric = function(){
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

   $querySiteVisitorMetric = function(){
      $start_of_this_year = \Carbon\Carbon::now()->startOfYear();
      $start_of_last_year = \Carbon\Carbon::now()->subYear()->startOfYear();
      $end_of_last_year = $start_of_this_year->copy()->subSecond();

      $sites = Site::where('user_id', iam()->id)->get();
      $lastyear_all = [];
      $thisyear_all = [];

      foreach ($sites as $site) {
         $site_id = $site->id;

         // Get Visitors for This Year
         $visitors_this_year = SitesVisitor::where('site_id', $site_id)
            ->where('created_at', '>=', $start_of_this_year)
            ->get();

         // Get Visitors for Last Year
         $visitors_last_year = SitesVisitor::where('site_id', $site_id)
            ->whereBetween('created_at', [$start_of_last_year, $end_of_last_year])
            ->get();

         // Process This Year's Views
         foreach ($visitors_this_year as $item) {
            $date = (string) \Carbon\Carbon::parse($item->created_at)->format('M');
            if (!empty($date) && !array_key_exists($date, $thisyear_all)) {
                  $thisyear_all[$date] = [
                     'visits' => 0,
                     'unique' => 0,
                  ];
            }

            if (array_key_exists($date, $thisyear_all)) {
                  $thisyear_all[$date]['unique']++;
                  $thisyear_all[$date]['visits'] += $item->views;
            }
         }

         // Process Last Year's Views
         foreach ($visitors_last_year as $item) {
            $date = (string) \Carbon\Carbon::parse($item->created_at)->format('M');
            if (!empty($date) && !array_key_exists($date, $lastyear_all)) {
                  $lastyear_all[$date] = [
                     'visits' => 0,
                     'unique' => 0,
                  ];
            }

            if (array_key_exists($date, $lastyear_all)) {
                  $lastyear_all[$date]['unique']++;
                  $lastyear_all[$date]['visits'] += $item->views;
            }
         }
      }
      $total_days_this_year = \Carbon\Carbon::now()->dayOfYear;
      $total_days_last_year = \Carbon\Carbon::parse('December 31 last year')->dayOfYear;

      // Calculate total visits this year and last year
      $total_visits_this_year = array_reduce($thisyear_all, function ($carry, $item) {
         return $carry + $item['unique'];
      }, 0);

      $total_visits_last_year = array_reduce($lastyear_all, function ($carry, $item) {
         return $carry + $item['unique'];
      }, 0);

      // Calculate average views per day
      $average_views_per_day_this_year = $total_visits_this_year / $total_days_this_year;
      $average_views_per_day_last_year = $total_visits_last_year / $total_days_last_year;
      // Calculate the percentage increase/decrease
      try {
         $percentage_change = ($average_views_per_day_this_year - $average_views_per_day_last_year) / $average_views_per_day_last_year * 100;
      } catch (\Throwable $th) {
         $percentage_change = 0;
      }


      $is_increase = $percentage_change >= 0;
      return [
         'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
         'lastyear' => _get_chart_data($lastyear_all),
         'thisyear' => _get_chart_data($thisyear_all),
         'average_views_per_day_this_year' => number_format($average_views_per_day_this_year, 2),
         'percentage_change' => number_format($percentage_change),
         'is_increase' => $is_increase,
      ];
   };
   $getAnalytics = function(){
      $userId = iam()->id;
      $startDate = Carbon::now()->subDays(30)->format('Y-m-d');
      $endDate = Carbon::now()->format('Y-m-d');

      $total_donations = 0;
      $audience = Audience::where('owner_id', $userId)->count();
      $products = ProductOrder::where('user_id', $userId)->sum('price');
      $courses = CoursesOrder::where('user_id', $userId)->sum('price');

      return [
         'products' => ao(iam()->currency(), 'code') . nr($products),
         'courses' => ao(iam()->currency(), 'code') . nr($courses),
         'total_donations' => $total_donations,
         'audience' => $audience,
      ];
   };
?>

<div>
   
   <div>
      <div x-data="console__index">

         <div class="lg:!flex p-0 lg:!h-full justify-between gap-4">
            <div class="w-full min-w-0">
               
               <div class="details card relative !bg-white dark:bg-gray-800 overflow-hidden rounded-3xl mb-4">
                  <div class="details__container z-10 relative !bg-transparent remove-before">
                     <div class="max-w-[400px] mb-5">
                        {!! __icon('interface-essential', 'dashboard.3', 'w-12 h-12 mb-2 text-gray-700 dark:text-gray-300') !!}
                        <div class="banner__title h3 !text-black dark:!text-white" x-text="greetings"></div>
                        {{-- <div class="banner__text !text-black">{{ __('Catch up with what\'s going on and keep your audience engaged.') }}</div> --}}
                     </div>
                     <hr class="my-4 border-gray-200 dark:border-gray-700">
                     {{-- <div class="details__title h6">Active Users right now 💡</div> --}}
                     <div class="details__row">
                        <div class="details__col">
                           <div class="details__top">
                              <div class="details__number h1 text-gray-900 dark:text-white" x-text="average_views_per_day_this_year"></div>
                              <div class="details__line">
                                 <div>
                                    <div class="w-[38px] h-[38px] rounded-xl [box-shadow:var(--yena-shadows-md)] bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center mr-2">
                                       {!! __i('Business, Products', 'blackboard-business-chart', 'w-5 h-5 text-white') !!}
                                    </div>
                                 </div>
                                 <div class="details__info caption-sm text-gray-600 dark:text-gray-400">{{ __('Average site visitors') }}</div>
                              </div>
                           </div>
                           <div class="details__bottom">
                              <div class="details__statistics">
                                 <div class="details__chart details__chart_activity" wire:ignore>
                                    <div id="chart-active-users" x-ref="chartYearVisits"></div>
                                 </div>
                                 <div class="details__status">
                                    <div class="details__icon" :class="{
                                       '!bg-red-400 text-white': !percentage_change_increase,
                                       '!bg-green-400 text-white': percentage_change_increase,
                                    }">
                                       <i class="ph text-xs" :class="{
                                          'ph-caret-down': !percentage_change_increase,
                                          'ph-caret-up': percentage_change_increase,
                                       }"></i>
                                    </div>
                                    <div class="details__percent caption-sm color-blue-dark" x-text="percentage_change + '%'"></div>
                                 </div>
                              </div>
                              {{-- <div class="details__info caption-sm">Update your payout method in Settings</div> --}}
                           </div>
                        </div>
                        <div class="details__col">
                           <div class="details__box !bg-gray-50 dark:!bg-gray-700">
                              <div class="details__chart details__chart_counter relative" wire:ignore>
                                 <template x-if="chartLoading">
                                     <div class="flex flex-col gap-2 p-5 w-full mt-auto">
                                       <div class="--placeholder-skeleton w-full h-[35px] rounded-sm"></div>
                                       <div class="--placeholder-skeleton w-full h-[35px] rounded-sm"></div>
                                       <div class="--placeholder-skeleton w-full h-[35px] rounded-sm"></div>
                                       <div class="--placeholder-skeleton w-full h-[35px] rounded-sm"></div>
                                     </div>
                                 </template>
                                 <div class="chart-users-counter" x-ref="siteVisitorsChart"></div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="details__list details__list_four">
                        <div class="details__item">
                           <div class="details__head">
                              <div class="details__preview bg-purple-600">
                                 {!! __i('emails', 'email-mail-letter', 'w-[10px] text-white') !!}
                              </div>
                              <div class="details__text caption-sm text-gray-600 dark:text-gray-400">{{ __('Audience') }}</div>
                           </div>
                           <div class="details__counter h3 text-gray-900 dark:text-white">
                              <template x-if="analyticsLoading">
                                  <div class="--placeholder-skeleton w-full h-[20px] rounded-sm mb-2"></div>
                              </template>
                              <template x-if="!analyticsLoading">
                                 <span x-html="analytics.audience"></span>
                              </template>
                           </div>
                           <div class="details__indicator">
                              <div class="details__progress bg-purple-600 w-[55%]"></div>
                           </div>
                        </div>
                        <div class="details__item">
                           <div class="details__head">
                              <div class="details__preview bg-pink-600">
                                 {!! __i('Building, Construction', 'store', 'w-[10px] text-white') !!}
                              </div>
                              <div class="details__text caption-sm text-gray-600 dark:text-gray-400">{{ __('Products') }}</div>
                           </div>
                           <div class="details__counter h3 text-gray-900 dark:text-white">
                              <template x-if="analyticsLoading">
                                  <div class="--placeholder-skeleton w-full h-[20px] rounded-sm mb-2"></div>
                              </template>
                              <template x-if="!analyticsLoading">
                                 <span x-html="analytics.products"></span>
                              </template>
                           </div>
                           <div class="details__indicator">
                              <div class="details__progress bg-pink-600" style="width: 52%;"></div>
                           </div>
                        </div>
                        <div class="details__item">
                           <div class="details__head">
                              <div class="details__preview bg-blue-600">
                                 {!! __i('Content Edit', 'Book, Open.4', 'w-[10px] text-white') !!}
                              </div>
                              <div class="details__text caption-sm text-gray-600 dark:text-gray-400">{{ __('Courses') }}</div>
                           </div>
                           <div class="details__counter h3 text-gray-900 dark:text-white">
                              <template x-if="analyticsLoading">
                                 <div class="--placeholder-skeleton w-full h-[20px] rounded-sm mb-2"></div>
                             </template>
                             <template x-if="!analyticsLoading">
                                <span x-html="analytics.courses"></span>
                             </template>
                           </div>
                           <div class="details__indicator">
                              <div class="details__progress bg-blue-600" style="width: 55%;"></div>
                           </div>
                        </div>
                        <div class="details__item">
                           <div class="details__head">
                              <div class="details__preview bg-red-600">
                                 {!! __i('custom', 'settings-pay-1', 'w-[10px] text-white') !!}
                              </div>
                              <div class="details__text caption-sm text-gray-600 dark:text-gray-400">{{ __('Total Donations') }}</div>
                           </div>
                           <div class="details__counter h3 text-gray-900 dark:text-white">
                              <template x-if="analyticsLoading">
                                  <div class="--placeholder-skeleton w-full h-[20px] rounded-sm mb-2"></div>
                              </template>
                              <template x-if="!analyticsLoading">
                                 <span x-html="analytics.total_donations"></span>
                              </template>
                           </div>
                           <div class="details__indicator">
                              <div class="details__progress bg-red-600" style="width: 68%;"></div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               
               <div wire:ignore>
                  <div class="grid grid-cols-1 md:!grid-cols-2 gap-4">
                     <div class="widget widget_chart card">
                        <div class="p-4 bg-white dark:bg-gray-800 mb-0 rounded-8 cursor-pointer lg:flex lg:justify-between lg:items-center hover:opacity-70">
                           <div class="flex flex-col lg:flex-row items-center gap-3 ">
                              
                              {!! __i('emails', 'email-hand', 'w-12 h-12 self-start text-gray-600 dark:text-gray-400') !!}
      
      
                              <div class="flex flex-col">
                                 <div class="text-gray-800 dark:text-white font-bold text-2xl">{{ __('Leads') }}</div>
                              </div>
                           </div>
                        </div>
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
                           
                           <div class="leads-line-chart" x-ref="leadsLineChart"></div>
                          </div>
                        </div>
                      </div>
                     <div class="widget widget_chart card">
                        <div class="p-4 bg-white dark:bg-gray-800 mb-0 rounded-8 cursor-pointer lg:flex lg:justify-between lg:items-center hover:opacity-70">
                           <div class="flex flex-col lg:flex-row items-center gap-3 ">
                              
                              {!! __i('shopping-ecommerce', 'store-chart-graph', 'w-12 h-12 self-start text-gray-600 dark:text-gray-400') !!}
      
      
                              <div class="flex flex-col">
                                 <div class="text-gray-800 dark:text-white font-bold text-2xl">{{ __('Store Earnings') }}</div>
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
                           
                           <div class="store-line-chart" x-ref="storeLineChart"></div>
                          </div>
                   
                          {{-- <div class="widget__btns"><button class="widget__btn btn btn_purple btn_wide">Analytics</button></div> --}}
                        </div>
                      </div>
                  </div>
               </div>

               <div class="grid grid-cols-1 md:!grid-cols-2 gap-4 mt-4">
                  <a class="p-4 bg-white dark:bg-gray-800 card rounded-8 cursor-pointer lg:flex lg:justify-between lg:items-center hover:opacity-70" href="{{ route('dashboard-sites-index') }}" @navigate>
                     <div class="flex flex-col lg:flex-row items-center gap-3 ">
                        
                        {!! __i('--ie', 'browser-internet-web-network-window-app-icon', 'w-12 h-12 self-start text-gray-600 dark:text-gray-400') !!}


                        <div class="flex flex-col">
                           <div class="text-gray-800 dark:text-white font-bold">{{ __('My sites') }}</div>
                           <div class="text-gray-600 dark:text-gray-400">{{ __('Create & manage sites with AI') }}</div>
                        </div>
                     </div>
                     <div>
                        <i class="ph ph-caret-right text-gray-400 dark:text-gray-500"></i>
                     </div>
                  </a>
                  <a href="{{ route('dashboard-store-index') }}" @navigate class="p-4 bg-white dark:bg-gray-800 card rounded-8 cursor-pointer lg:flex lg:justify-between lg:items-center hover:opacity-70">
                     <div class="flex flex-col lg:flex-row items-center gap-3 ">
                        
                        {!! __i('Building, Construction', 'store', 'w-12 h-12 self-start text-gray-600 dark:text-gray-400') !!}


                        <div class="flex flex-col">
                           <div class="text-gray-800 dark:text-white font-bold">{{ __('Products') }}</div>
                           <div class="text-gray-600 dark:text-gray-400">{{ __('Let your audience purchase your product') }}</div>
                        </div>
                     </div>
                     <div>
                        <i class="ph ph-caret-right text-gray-400 dark:text-gray-500"></i>
                     </div>
                  </a>
                  <a href="{{ route('dashboard-audience-index') }}" @navigate class="p-4 bg-white dark:bg-gray-800 card rounded-8 cursor-pointer lg:flex lg:justify-between lg:items-center hover:opacity-70">
                     <div class="flex flex-col lg:flex-row items-center gap-3 ">
                        
                        {!! __i('emails', 'email-mail-letter', 'w-12 h-12 self-start text-gray-600 dark:text-gray-400') !!}


                        <div class="flex flex-col">
                           <div class="text-gray-800 dark:text-white font-bold">{{ __('Leads') }}</div>
                           <div class="text-gray-600 dark:text-gray-400">{{ __('Create & broadcast to your audience') }}</div>
                        </div>
                     </div>
                     <div>
                        <i class="ph ph-caret-right text-gray-400 dark:text-gray-500"></i>
                     </div>
                  </a>
                  <a href="{{ route('dashboard-courses-index') }}" @navigate class="p-4 bg-white dark:bg-gray-800 card rounded-8 cursor-pointer lg:flex lg:justify-between lg:items-center hover:opacity-70">
                     <div class="flex flex-col lg:flex-row items-center gap-3 ">
                        
                        {!! __i('Content Edit', 'Book, Open.4', 'w-12 h-12 self-start text-gray-600 dark:text-gray-400') !!}


                        <div class="flex flex-col">
                           <div class="text-gray-800 dark:text-white font-bold">{{ __('Courses') }}</div>
                           <div class="text-gray-600 dark:text-gray-400">{{ __('Share knowledge with courses') }}</div>
                        </div>
                     </div>
                     <div>
                        <i class="ph ph-caret-right text-gray-400 dark:text-gray-500"></i>
                     </div>
                  </a>
               </div>
            </div>
            <div class="min-w-[310px] w-full lg:!w-max mt-4 lg:![margin-top:0]">
               <div class="short-cal flex flex-[1] flex-col gap-[12px] p-[12px] mt-0 border-l border-solid border-gray-200 dark:border-gray-700">
                  <div class="flex items-center justify-between">
                     <p class="text-color-headline font-bold text-gray-900 dark:text-white">{{ __('My wallet') }}</p>
                     <div class="p-[4px] flex items-center">
                        <span class="default-tooltip relative top-[1px]">
                              <a class="cursor-pointer">
                                 {!! __i('custom', 'settings-pay-1', 'w-4 h-4 text-gray-600 dark:text-gray-400') !!}
                              </a>
                        </span>
                     </div>
                  </div>
                  <div class="flex flex-col h-full gap-2">
                     <div class="calendar-day-view h-full flex items-center justify-center">
                        <div class="h-full w-full flex flex-col items-center bg-gray-100 dark:bg-gray-800 rounded-[10px]">
                           <div class="widget widget_stat widget_shadow widget_after !w-full !rounded-[10px] card">
                              <div class="widget__chart widget__chart_items" wire:ignore>
                                 <template x-if="chartLoading">
                                     <div class="flex gap-2 p-5 w-full mx-auto">
                                       <div class="--placeholder-skeleton w-28 h-28 p-4 rounded-full mx-auto">
                                          <div class="bg-white dark:bg-gray-700 h-full w-full rounded-full"></div>
                                       </div>
                                     </div>
                                 </template>

                                 <div>
                                    <div id="chart-earnings-by-item" x-ref="chartWalletPie"></div>
                                 </div>
                              </div>
                              {{-- <div class="widget__title">Earnings By Item</div> --}}
                              <div class="text-7xl leading-[1.2] font-semibold tracking-[-1px] text-center mx-auto mt-4 text-gray-900 dark:text-white" x-html="donationsYear"></div>
                              <div class="max-w-[185px] mb-[18px] text-gray-600 dark:text-gray-400 text-center mx-auto">{{ __("This is the total received in your wallet this year") }}</div>
                              <div class="widget__legend">
                                 <div class="widget__color">
                                    <div class="widget__bg !bg-[#FFCE73]"></div>
                                    <div class="widget__text text-gray-700 dark:text-gray-300">{{ __('This Year') }}</div>
                                 </div>
                                 <div class="widget__color">
                                    <div class="widget__bg !bg-[#A0D7E7]"></div>
                                    <div class="widget__text text-gray-700 dark:text-gray-300">{{ __('Last Year') }}</div>
                                 </div>
                              </div>
                           </div>
                           {{-- <div class="bg-[rgb(255,_255,_255)] text-[rgb(17,_24,_39)] text-center overflow-hidden rounded-[16px] p-[32px]">
   
                              <p class="text-xl font-semibold pt-[16px] pb-[24px]">We have big update for you!</p>
                           </div> --}}
                           <div class="flex flex-col items-center gap-2 py-20 px-5">
                              {!! __i('money', 'Wallet-MAIN', 'w-12 h-12 text-gray-600 dark:text-gray-400') !!}
                              <div class="flex flex-col">
                                 <p class="text-color-descriptive text-center w-full text-gray-600 dark:text-gray-400">{{ __('Manage your withdrawal on your wallet page.') }}</p>
                              </div>
                              <a class="btn btn-primary !rounded-full" href="{{ route('dashboard-wallet-index') }}" @navigate>{{ __('Manage') }}</a>
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
         Alpine.data('console__index', () => {
            return {
               user: @entangle('user'),
               chartLoading: true,
               average_views_per_day_this_year: 0,
               percentage_change: 0,
               percentage_change_increase: 0,
               analyticsLoading: true,
               analytics: {
                  courses_earned: '$0',
                  products_earned: '$0',
                  total_donations: '0',
                  audience: '0',
               },

               donationsYear: '0',
               get greetings(){
                  const hours = new Date().getHours();
                  let message;

                  if (hours < 12) {
                     message = 'Good Morning';
                  } else if (hours < 18) {
                     message = 'Good Afternoon';
                  } else {
                     message = 'Good Evening';
                  }

                  return message + ', ' + this.user.name + '!';
               },

               querySmallChart(r){
                  let $this = this;
                  var options = {
                     labels: r.thisyear.labels,
                     series: [{
                        name: 'Active users',
                        data: r.thisyear.visits
                     }],
                     colors: ['#6C5DD3'],
                     chart: {
                        height: '100%',
                        type: 'line',
                        toolbar: {
                           show: false
                        },
                        sparkline: {
                           enabled: true
                        }
                     },
                     grid: {
                        borderColor: '#E4E4E4',
                        strokeDashArray: 0,
                        xaxis: {
                           lines: {
                              show: true
                           }
                        },
                        yaxis: {
                           lines: {
                              show: false
                           }
                        }
                     },
                     tooltip: {
                        enabled: false
                     },
                     stroke: {
                        width: 2,
                        curve: 'smooth'
                     },
                     xaxis: {
                        axisBorder: {
                           show: false
                        },
                        axisTicks: {
                           show: false
                        }
                     },
                     legend: {
                        show: false
                     },
                     dataLabels: {
                        enabled: false
                     }
                  };
                  new ApexCharts($this.$refs.chartYearVisits, options).render();
               },

               queryChart(){
                  let $this = this;
                  $this.$wire.queryWalletMetric().then(r => {
                     $this.donationsYear = r.thisYearPrice;
                     
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
                     new ApexCharts($this.$refs.chartWalletPie, options).render();
                  });
                  $this.$wire.querySiteVisitorMetric().then(r => {
                     $this.querySmallChart(r);
                     $this.average_views_per_day_this_year = r.average_views_per_day_this_year;
                     $this.percentage_change = r.percentage_change;
                     $this.percentage_change_increase = r.is_increase;
                     
                     let options = {
                        ...window.apexOptions,
                        labels: r.labels,
                        series: [
                           {
                              name: '{{ __('This Year') }}',
                              data: r.thisyear.unique,
                           },
                           {
                              name: '{{ __('Last Year') }}',
                              data: r.lastyear.unique,
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
                     new ApexCharts($this.$refs.siteVisitorsChart, options).render();
                  });
                  
                  $this.$wire.queryStoreMetric().then(r => {
                     let options = {
                        ...window.apexOptions,
                        labels: r.labels,
                        series: [
                           {
                              name: '{{ __('Amount') }}',
                              data: r.amount,
                           },
                           {
                              name: '{{ __('Sold') }}',
                              data: r.count,
                           },
                        ],
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
                        colors: ['#FFB7F5', '#FFB7F5', '#FFB7F5'],
                        yaxis: {
                           show: false,
                        },
                     };

                     options['chart']['height'] = 244;
                     // options['chart']['type'] = 'bar';

                     $this.chartLoading = false;
                     new ApexCharts($this.$refs.storeLineChart, options).render();
                  });

                  
                  $this.$wire.queryLeadsMetric().then(r => {
                     let options = {
                        ...window.apexOptions,
                        labels: r.labels,
                        series: [
                           {
                              name: '{{ __('Leads') }}',
                              data: r.count,
                           },
                        ],
                        colors: ['#FFB7F5', '#FFB7F5', '#FFB7F5'],
                        yaxis: {
                           show: false,
                        },
                     };

                     options['chart']['height'] = 244;

                     $this.chartLoading = false;
                     new ApexCharts($this.$refs.leadsLineChart, options).render();
                  });
               },

               init(){
                  let $this = this;

                  this.queryChart();
                  setTimeout(() => {
                     $this.$wire.getAnalytics().then(r => {
                        $this.analytics = r;
                        $this.analyticsLoading = false;
                     });
                  }, 200);
               }
            }
         });
      </script>
      @endscript
   </div>
</div>