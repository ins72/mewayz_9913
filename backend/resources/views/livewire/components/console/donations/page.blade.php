
<?php
   use Carbon\Carbon;
   use App\Models\UserDonation;
   use App\Models\UserDonationsRecurring;
   use App\Models\CheckoutGo;
   
   use function Livewire\Volt\{state, mount, placeholder, on, usesPagination, with};

   mount(function(){
      $this->getProducts();
   });

   state([
      'tips' => [],
      'recurring' => [],
      'donations' => [],
      'all_time_earnings' => [],
      'recent_tips' => []
   ]);

   on([
      
   ]);

   mount(function(){
      $this->_get();
   });

   $_get = function(){
      $this->tips = UserDonation::where('user_id', iam()->id)->orderBy('id', 'DESC')->get();

      $this->recurring = UserDonationsRecurring::where('user_id', iam()->id)->orderByDesc(function ($query) {
         $query->select('created_at')
            ->from('user_donations')
            ->whereColumn('user_donations.recurring_id', 'user_donations_recurring.id')
            ->orderBy('created_at', 'desc')
            ->limit(1);
      })->get();
   };

   $getAnalytics = function(){
      $userId = iam()->id;
      $startDate = Carbon::now()->subDays(30)->format('Y-m-d');
      $endDate = Carbon::now()->format('Y-m-d');

      $total_donations = UserDonation::where('user_id', $userId)->count();
      $allTimeEarnings = UserDonation::where('user_id', $userId)->sum('amount');
      $recentTips = UserDonation::where('user_id', $userId)
         ->where('created_at', '>=', $startDate)
         ->sum('amount');
      $total_members = UserDonation::distinct('payee_user_id')->count('payee_user_id');

      return [
         'all_time_earnings' => iam()->price($allTimeEarnings),
         'recent_tips' => iam()->price($recentTips),
         'total_donations' => $total_donations,
         'total_members' => $total_members,
      ];
   };

   $cancelDonation = function($id){
      if(!$recurring = UserDonationsRecurring::where('user_id', iam()->id)->where('id', $id)->first()) return;

      
      if($checkout = CheckoutGo::where('uref', $recurring->last_subscription_uref)->first()){
          // Check if is recurring subscription
          $config = config("yena.gateway.$checkout->method");
          if($checkout->payment_type == 'recurring' && $config){
              $requestFunction = ao($config, 'cancelFunction');
              $class = app()->make(ao($config, 'requestClass'));

              try {
                  $class->$requestFunction($checkout);
              } catch (\Throwable $th) {
                  //throw $th;
              }
              $recurring->last_subscription_uref = null;
              $recurring->is_active = false;
          }
      }

      $recurring->save();
      $this->_get();
   };
?>
<div>
    
    <div x-data="app_donations">
      <div class="banner">
          <div class="banner__container !bg-white">
            <div class="banner__preview">
              {!! __icon('custom', 'settings-pay-1') !!}
            </div>
            <div class="banner__wrap">
              <div class="banner__title h3 !text-black">{{ __('My donations') }}</div>
              <div class="banner__text !text-black">{{ __('View donations from all your bio pages') }}</div>
              
              <div class="mt-7 grid grid-cols-2 gap-1">
                  <div>
                   <div class="rounded-8 p-2 text-center col-span-2 lg:col-span-1 bg-[#f7f3f2] text-black">
                      <div class="detail text-gray-600">{{ __('Last 30 Days') }}</div>
                      <template x-if="analyticsLoading">
                          <div class="--placeholder-skeleton w-full h-[20px] rounded-sm mt-1"></div>
                      </template>
                      <template x-if="!analyticsLoading">
                        <div class="number-secondary !text-sm mt-1" x-html="analytics.recent_tips"></div>
                      </template>
                   </div>
                  </div>
                  <div>
                   <div class="rounded-8 p-2 text-center bg-[#f7f3f2] text-black">
                      <div class="detail text-gray-600">{{ __('Total Donations') }}</div>
                      <template x-if="analyticsLoading">
                          <div class="--placeholder-skeleton w-full h-[20px] rounded-sm mt-1"></div>
                      </template>
                      <template x-if="!analyticsLoading">
                        <div class="number-secondary !text-sm mt-1" x-html="analytics.total_donations"></div>
                      </template>
                   </div>
                  </div>
                  <div>
                   <div class="rounded-8 p-2 text-center bg-[#f7f3f2] text-black">
                      <div class="detail text-gray-600">{{ __('Donating Members') }}</div>
                      <template x-if="analyticsLoading">
                          <div class="--placeholder-skeleton w-full h-[20px] rounded-sm mt-1"></div>
                      </template>
                      <template x-if="!analyticsLoading">
                        <div class="number-secondary !text-sm mt-1" x-html="analytics.total_members"></div>
                      </template>
                   </div>
                  </div>
                  <div>
                   <div class="rounded-8 p-2 text-center bg-[#f7f3f2] text-black">
                      <div class="detail text-gray-600">{{ __('All Time') }}</div>
                      <template x-if="analyticsLoading">
                          <div class="--placeholder-skeleton w-full h-[20px] rounded-sm mt-1"></div>
                      </template>
                      <template x-if="!analyticsLoading">
                        <div class="number-secondary !text-sm mt-1" x-html="analytics.all_time_earnings"></div>
                      </template>
                   </div>
                  </div>
               </div>
               <div class="page__line">
                 <div class="page__nav">
                     <a class="page__link active" @click="_page='-'" :class="{
                        'active': _page=='-',
                     }">{{ __('Overview') }}</a>
                     <a class="page__link" @click="_page='recurring'" :class="{
                        'active': _page=='recurring',
                     }">{{ __('Active Recurring') }}</a>
                 </div>
               </div>
            </div>
          </div>
        </div>
    
          
    
        <div class="mx-auto w-full max-w-screen-xl pb-10 pt-0">
         <div x-cloak x-show="_page=='recurring'">
            <div class="grid grid-cols-1 md:!grid-cols-2 gap-4">
               @foreach ($recurring as $item)
               @php
                   $lastPayment = $item->getLastDonation();
               @endphp
               <div class="flex flex-col p-5 [border-bottom:1px_solid_#F9F7F7] bg-white rounded-xl" x-data="{is_delete:false}">
                  <div class="flex items-center w-full mb-2">
                     <div class="w-20 social bg-[#f3f3f3] h-16 rounded-xl flex items-center justify-center">
                        {!! __i('custom', 'settings-pay-1', 'w-8 h-8') !!}
                     </div>
                     
                     <div class="w-6/12 ml-2">
                       <h6 class="font-medium text-lg leading-[24px] tracking-[-.014em] text-[#000] cursor-pointer block align-middle">
                        {{ $lastPayment ? __('Last Payment') .' - '. \Carbon\Carbon::parse($lastPayment->created_at)->toFormattedDateString()  : '' }}
                       </h6>
   
                       <div class="flex items-center gap-3">
                        <span class="block text-[#000] text-center font-medium text-[11px] leading-[16px] tracking-[.01em] no-underline px-[10px] py-[4px] rounded-[350px] capitalize align-middle {{ $item->is_active ? 'bg-green-400' : 'bg-red-400' }}">{{ $item->is_active ? __('Active') : __('Canceled') }}</span>
                        
                        <span class="block text-[#000] text-center font-medium text-[11px] leading-[16px] tracking-[.01em] no-underline px-[10px] py-[4px] rounded-[350px] capitalize bg-[#E7E9EB] align-middle">{{ __(':count payments', ['count' => $item->donations()->count()]) }}</span>
                       </div>
                     </div>
                  </div>
                  
                  @foreach ($item->donations()->orderBy('id', 'desc')->get() as $donation)
                  @php
                      $payee = $donation->payee;
                  @endphp
                  <div class="border-gray-50 relative rounded-lg border-2 bg-[#f7f3f2] p-3 pr-1 transition-all hover:shadow-md sm:p-4">
                     <li class="relative flex items-center justify-between">
                        <div class="relative flex items-center w-[100%]">
                        
                           <div>
                              <div class="w-8 h-8 sm:h-10 sm:w-10">

                                 @if (!$donation->payee)
                                 <div>
                                    <div class="rounded-md p-2 bg-[#eee] !h-full !w-[100%] default">
                                       <div>
                                          {!! __i('--ie', 'image-picture', 'text-gray-300') !!}
                                       </div>
                                    </div>
                                 </div>
                                 @else
                                 <img alt=" " class="h-full h-full rounded-md object-cover" src="{{ $donation->payee->getAvatar() }}">
                                 @endif
                              </div>
                           </div>


                           <div class="ml-2 w-[100%]">
                              <div class="flex items-center w-[100%]">
                                 <a class="w-24 truncate text-sm font-semibold text-blue-800 sm:w-full sm:text-base">{{ $payee ? $payee->name : ao($donation->info, 'name') }}</a>
                                 
                                 <a class="flex items-center space-x-1 rounded-md bg-white px-2 py-0.5 transition-all duration-75 hover:scale-105 active:scale-100 ml-auto">
                                    <i class="ph ph-coin text-gray-700 text-sm mr-1"></i>
                                    <p class="whitespace-nowrap text-sm text-gray-500"> <span>{!! iam()->price($donation->amount) !!}</span></p>
                                 </a>
                                 <a class="flex items-center space-x-1 rounded-md bg-white px-2 py-0.5 transition-all duration-75 hover:scale-105 active:scale-100 ml-1">
                                    <i class="ph ph-calendar text-gray-700 text-sm mr-1"></i>
                                    
                                    
                                    
                                    <p class="whitespace-nowrap text-sm text-gray-500"> <span>{{ \Carbon\Carbon::parse($donation->created_at)->toFormattedDateString() }}</span></p>
                                 </a>
                              </div>
                              <h3 class="max-w-[200px] truncate text-sm font-medium text-gray-700 md:max-w-md xl:max-w-lg">{{ $payee ? $payee->email : ao($donation->info, 'email') }}</h3>
                           </div>
                        </div>
                     </li>
                  </div>
                  @endforeach
                  
                  @if ($item->is_active)
                  <a @click="$event.stopPropagation(); is_delete=!is_delete;" class="yena-black-btn mt-2 text-center">{{ __('Cancel') }}</a>
                  @endif

                  <div class="card-button p-3 mb-4 flex gap-2 bg-[var(--yena-colors-gray-100)] w-full rounded-lg !hidden" x-cloak @click="$event.stopPropagation();" :class="{
                     '!hidden': !is_delete
                     }">
                     <button class="btn btn-medium neutral !h-8 !flex !items-center !justify-center !rounded-full !text-black !bg-white" type="button" @click="is_delete=!is_delete">{{ __('Cancel') }}</button>
      
                     <button class="btn btn-medium !bg-[var(--c-red)] !text-[var(--c-light)] !h-8 !flex !items-center !justify-center !rounded-full w-full" @click="$wire.cancelDonation('{{ $item->id }}'); is_delete=false;">{{ __('Yes, Cancel') }}</button>
                  </div>
                </div>
               @endforeach
            </div>
         </div>
         <div x-cloak x-show="_page=='-'">
            <div class="page-trans">
               <div class="flex-table">
                  <div class="flex-table-header">
                     <span>{{ __('Date') }}</span>
                     <span>{{ __('From') }}</span>
                     <span>{{ __('Is Recurring') }}</span>
                     <span>{{ __('Amount') }}</span>
                     <span>{{ __('Source') }}</span>
                  </div>
                  @foreach ($tips as $item)
                  @php
                      $recurring = $item->getRecurring();
                  @endphp
                  <div class="flex-table-item !rounded-none overflow-x-auto !shadow-none !m-0 !bg-transparent border-b border-solid border-gray-300">
                   <div class="flex-table-cell !px-0" data-th="{{ __('Date') }}">
                     <div class="h-full flex items-center justify-center ml-4 md:!ml-0">
                         <div class="row-content truncate whitespace-pre-wrap text-gray-600" >{{ \Carbon\Carbon::parse($item->created_at)->toDayDateTimeString() }}</div>
                     </div>
                   </div>
                     <div class="flex-table-cell !px-0" data-th="{{ __('User') }}">
                        <div>
                             <div class="flex min-w-[10rem] max-w-[15rem] gap-2 ml-4 md:!ml-0">
                               @if ($item->payee)
                               <div class="relative flex-none">
                                 <div class="relative">
                                     <div class="sj-avatar-container">
                                       <img src="{{ $item->payee->getAvatar() }}" alt=" " class="w-10 h-10 rounded-full object-cover" referrerpolicy="no-referrer">
                                     </div>
                                     <div class="absolute -bottom-2 -right-1">
                                       <div class="rounded-full bg-white/75 p-1 backdrop-blur-sm">
                                           <div class=""></div>
                                       </div>
                                     </div>
                                 </div>
                               </div>
                               @endif
                               <div class="flex flex-col justify-center truncate">
                                 <div class="truncate whitespace-pre-wrap text-gray-900">{{ $item->payee ? $item->payee->name : ao($item->info, 'name') }}</div>
                                 <div class="truncate whitespace-pre-wrap">{{ $item->payee ? $item->payee->email : ao($item->info, 'email') }}</div>
                               </div>
                           </div>
                        </div>
                     </div>
                     <div class="flex-table-cell !px-0" data-th="{{ __('Is Recurring') }}">
                       <div class="ml-4 md:!ml-0 mt-2 flex flex-wrap gap-1">
                        <span class="rounded-full font--10 px-2 font-semibold bg-green-400 text-black">{{ $recurring ? __('Monthly Recurring') : __('One time') }}</span>
                        @if ($recurring)
                        <span class="rounded-full font--10 px-2 font-semibold bg-green-400 text-black {{ $recurring->is_active ? 'bg-green-400' : 'bg-red-400' }}">{{ $recurring->is_active ? __('Active') : __('Canceled') }}</span>
                        @endif

                       </div>
                     </div>
                     <div class="flex-table-cell !px-0" data-th="{{ __('Amount') }}">
                       <div class="ml-4 md:!ml-0 mt-2 flex flex-wrap gap-1">
                        <span class="rounded-full font--10 px-2 font-semibold bg-green-400 text-black">{!! iam()->price($item->amount) !!}</span>
                       </div>
                     </div>
                     <div class="flex-table-cell !px-0" data-th="{{ __('Source') }}">
                        <div>
                             <div class="flex min-w-[10rem] max-w-[15rem] gap-2 ml-4 md:!ml-0">
                               <div class="relative flex-none">
                                 <div class="relative">
                                     <div class="sj-avatar-container">
                                       <img src="{{ $item->page->getLogo() }}" alt=" " class="w-10 h-10 rounded-md object-cover" referrerpolicy="no-referrer">
                                     </div>
                                     <div class="absolute -bottom-2 -right-1">
                                       <div class="rounded-full bg-white/75 p-1 backdrop-blur-sm">
                                           <div class=""></div>
                                       </div>
                                     </div>
                                 </div>
                               </div>
                               <div class="flex flex-col justify-center truncate">
                                 <div class="truncate whitespace-pre-wrap text-gray-900">{{ $item->page->name }}</div>
                                 {{-- <div class="truncate whitespace-pre-wrap">{{ $item->payee->email }}</div> --}}
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
   </div>

   @script
   <script>
       Alpine.data('app_donations', () => {
          return {
            _page: '-',
            // show_option: false,
            analyticsLoading: true,
            analytics: {
               all_time_earnings: '$0',
               recent_tips: '$0',
               total_donations: '0',
               total_members: '0',
            },
            init(){
               var $this = this;
               // console.log('r')

               $this.$wire.getAnalytics().then(r => {
                $this.analytics = r;
                $this.analyticsLoading = false;
              });
            }
          }
       });
   </script>
   @endscript
</div>